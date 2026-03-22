<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Http\Requests\UpdateAppointmentServicesRequest;
use App\Models\Appointment;
use App\Models\AppointmentService;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\TurnTracker;
use App\Services\GoHighLevelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class SalonAppointmentController extends Controller
{
    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        $data = $request->validated();
        $type = (string) ($data['type'] ?? 'walk-in');
        $appointmentDateTime = $data['appointment_datetime'] ?? null;
        if (! $appointmentDateTime) {
            $appointmentDateTime = now();
        } else {
            $appointmentDateTime = Carbon::parse((string) $appointmentDateTime);
        }

        $appointment = Appointment::query()->create([
            'customer_id' => $data['customer_id'],
            'type' => $type,
            'status' => $data['status'] ?? 'waiting',
            'appointment_datetime' => $appointmentDateTime,
        ]);

        if (! empty($data['assigned_technician'])) {
            $appointment->technicians()->sync($data['assigned_technician']);
        }

        $appointment->load(['customer', 'technicians', 'appointmentServices.serviceCategory', 'appointmentServices.service']);

        // Sync to GoHighLevel (non-blocking)
        Log::info('Appointment created: id='.$appointment->id.' type='.$appointment->type);
        if ($appointment->type === 'booked') {
            GoHighLevelService::syncAppointment($appointment);
            $appointment->refresh();
            $appointment->load(['customer', 'technicians', 'appointmentServices.serviceCategory', 'appointmentServices.service']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Added to waiting list successfully.',
            'data' => $this->appointmentToApiShape($appointment),
        ], 201);
    }

    public function update(UpdateAppointmentRequest $request, Appointment $appointment): JsonResponse
    {
        $updates = [];

        if ($request->filled('customer_id')) {
            $updates['customer_id'] = $request->validated('customer_id');
        }

        if ($request->filled('appointment_datetime')) {
            $updates['appointment_datetime'] = $request->validated('appointment_datetime');
        }

        if ($request->filled('status')) {
            $updates['status'] = $request->validated('status');
        }

        if ($request->has('color')) {
            $updates['color'] = $request->validated('color');
        }

        if (! empty($updates)) {
            $appointment->update($updates);
        }

        if ($request->has('assigned_technician')) {
            $appointment->technicians()->sync($request->validated('assigned_technician') ?? []);
        }

        $appointment->load(['customer', 'technicians', 'appointmentServices.serviceCategory', 'appointmentServices.service']);

        // Sync to GHL when:
        // 1. Assignment is confirmed (status changed to unpaid) and not yet synced
        // 2. Appointment datetime changed on an already-synced appointment (e.g. drag-drop on calendar)
        $shouldSync = (($updates['status'] ?? null) === 'unpaid' && ! $appointment->ghl_appointment_id)
            || (isset($updates['appointment_datetime']) && $appointment->ghl_appointment_id);

        if ($shouldSync) {
            GoHighLevelService::syncAppointment($appointment);
            $appointment->refresh();
            $appointment->load(['customer', 'technicians', 'appointmentServices.serviceCategory', 'appointmentServices.service']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Assignment saved successfully.',
            'data' => $this->appointmentToApiShape($appointment),
        ]);
    }

    public function destroy(Request $request, Appointment $appointment): JsonResponse
    {
        if (! $request->session()->has('salon_authenticated')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        // Decrement turn tracker service counts for each assigned technician
        $appointmentServices = $appointment->appointmentServices()->with('service')->get();
        $serviceCountsByTechnician = [];
        foreach ($appointmentServices as $aptService) {
            $userId = $aptService->user_id;
            if (! $userId) {
                continue;
            }
            $serviceCount = $aptService->service?->service_count ?? 0;
            $quantity = $aptService->quantity ?? 1;
            $serviceCountsByTechnician[$userId] = ($serviceCountsByTechnician[$userId] ?? 0) + ($serviceCount * $quantity);
        }
        foreach ($serviceCountsByTechnician as $userId => $countToSubtract) {
            $tracker = TurnTracker::query()->where('user_id', $userId)->first();
            if ($tracker) {
                $tracker->services = max(0, (float) $tracker->services - $countToSubtract);
                $tracker->save();
            }
        }

        // Delete from GoHighLevel if synced
        if ($appointment->ghl_appointment_id) {
            GoHighLevelService::deleteGhlAppointment($appointment->ghl_appointment_id);
        }

        // Delete related records
        $appointment->appointmentServices()->delete();
        $appointment->technicians()->detach();

        $appointment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Removed from waiting list successfully.',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function appointmentToApiShape(Appointment $appointment): array
    {
        return [
            'id' => $appointment->id,
            'customer_id' => $appointment->customer_id,
            'appointment' => $appointment->type,
            'status' => $appointment->status,
            'color' => $appointment->color,
            'ghl_appointment_id' => $appointment->ghl_appointment_id,
            'ghl_calendar_id' => $appointment->ghl_calendar_id,
            'created_at' => $appointment->created_at?->toIso8601String(),
            'appointment_datetime' => $appointment->appointment_datetime?->format('Y-m-d\TH:i:s'),
            'assigned_technician' => $appointment->technicians->pluck('id')->values()->all(),
            'services' => $appointment->appointmentServices->map(fn ($as) => [
                'service' => $as->serviceCategory?->slug,
                'service_id' => $as->service_id,
                'service_name' => $as->service?->name,
                'service_color' => $as->service?->color,
                'technician_id' => $as->user_id,
                'quantity' => (int) ($as->quantity ?? 1),
                'unit_price' => $as->unit_price !== null ? (float) $as->unit_price : null,
            ])->values()->all(),
        ];
    }

    public function updateServices(UpdateAppointmentServicesRequest $request, Appointment $appointment): JsonResponse
    {
        $items = $request->validated('services');
        $serviceIds = collect($items)
            ->pluck('service_id')
            ->filter(fn ($id) => $id !== null && $id !== '')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $servicesById = $serviceIds->isNotEmpty()
            ? Service::query()->with('categories:service_categories.id,service_categories.slug')->whereIn('id', $serviceIds)->get()->keyBy('id')
            : collect();

        $slugsFromItems = collect($items)->pluck('service')->filter()->values();
        $slugsFromServices = $servicesById->values()->flatMap(fn (Service $s) => $s->categories->pluck('slug'));
        $allSlugs = $slugsFromItems->merge($slugsFromServices)->unique()->values()->all();

        $categoryIds = ! empty($allSlugs)
            ? ServiceCategory::query()->whereIn('slug', $allSlugs)->pluck('id', 'slug')
            : collect();

        $appointment->appointmentServices()->delete();

        foreach ($items as $item) {
            $serviceId = isset($item['service_id']) ? (int) $item['service_id'] : null;
            $service = $serviceId ? $servicesById->get($serviceId) : null;

            $slug = $item['service'] ?? null;
            $categoryId = $slug ? ($categoryIds[$slug] ?? null) : null;

            if ($service) {
                if (! $slug || ! $service->categories->contains('slug', $slug)) {
                    $slug = $service->categories->first()?->slug;
                    $categoryId = $slug ? ($categoryIds[$slug] ?? null) : null;
                }
            }

            if (! $categoryId) {
                continue;
            }
            AppointmentService::query()->create([
                'appointment_id' => $appointment->id,
                'service_id' => $service?->id,
                'service_category_id' => $categoryId,
                'user_id' => $item['technician_id'],
                'quantity' => (int) ($item['quantity'] ?? 1),
                'unit_price' => isset($item['unit_price']) ? (float) $item['unit_price'] : null,
            ]);
        }

        $appointment->load(['customer', 'technicians', 'appointmentServices.serviceCategory', 'appointmentServices.service']);

        return response()->json([
            'success' => true,
            'message' => 'Cart saved successfully.',
            'data' => $this->appointmentToApiShape($appointment),
        ]);
    }

    public function syncCalendar(Request $request, Appointment $appointment): JsonResponse
    {
        $calendarId = $request->input('calendar_id');
        if (! $calendarId) {
            return response()->json(['success' => false, 'message' => 'Calendar ID is required.'], 422);
        }

        $oldGhlId = $appointment->ghl_appointment_id;
        GoHighLevelService::syncAppointment($appointment, $calendarId);
        $appointment->refresh();
        $appointment->load(['customer', 'technicians', 'appointmentServices.serviceCategory', 'appointmentServices.service']);

        $synced = $appointment->ghl_appointment_id && $appointment->ghl_appointment_id !== $oldGhlId;

        return response()->json([
            'success' => $synced,
            'message' => $synced ? 'Appointment synced to calendar.' : 'Failed to sync appointment to calendar.',
            'data' => $this->appointmentToApiShape($appointment),
        ], $synced ? 200 : 422);
    }
}

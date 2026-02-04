<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Http\Requests\UpdateAppointmentServicesRequest;
use App\Models\Appointment;
use App\Models\AppointmentService;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SalonAppointmentController extends Controller
{
    public function store(StoreAppointmentRequest $request): JsonResponse
    {
        $data = $request->validated();
        $appointment = Appointment::query()->create([
            'customer_id' => $data['customer_id'],
            'type' => $data['type'] ?? 'walk-in',
            'status' => $data['status'] ?? 'waiting',
            'appointment_datetime' => $data['appointment_datetime'],
        ]);

        if (! empty($data['assigned_technician'])) {
            $appointment->technicians()->sync($data['assigned_technician']);
        }

        $appointment->load(['customer', 'technicians', 'appointmentServices.serviceCategory', 'appointmentServices.service']);

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

        if (! empty($updates)) {
            $appointment->update($updates);
        }

        if ($request->filled('assigned_technician')) {
            $appointment->technicians()->sync($request->validated('assigned_technician'));
        }

        $appointment->load(['customer', 'technicians', 'appointmentServices.serviceCategory', 'appointmentServices.service']);

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
            'created_at' => $appointment->created_at?->toIso8601String(),
            'appointment_datetime' => $appointment->appointment_datetime?->toIso8601String(),
            'assigned_technician' => $appointment->technicians->pluck('id')->values()->all(),
            'services' => $appointment->appointmentServices->map(fn ($as) => [
                'service' => $as->serviceCategory?->slug,
                'service_id' => $as->service_id,
                'service_name' => $as->service?->name,
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
}

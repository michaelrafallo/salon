<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\TurnTracker;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SalonDataController extends Controller
{
    /**
     * Return appointments in same shape as appointments.json.
     */
    public function appointments(Request $request): JsonResponse
    {
        $query = Appointment::query()
            ->with(['customer', 'technicians', 'appointmentServices.serviceCategory', 'appointmentServices.service'])
            ->orderBy('appointment_datetime');

        $currentRole = (string) $request->session()->get('salon_role', 'admin');
        if ($currentRole === 'technician') {
            $email = (string) $request->session()->get('salon_user_email', '');
            $technicianId = $email !== ''
                ? User::query()->where('email', $email)->value('id')
                : null;

            if (! $technicianId) {
                return response()->json(['appointments' => []]);
            }

            $query->whereHas('technicians', fn ($q) => $q->where('users.id', (int) $technicianId));
        }

        $rows = $query->get();

        $appointments = $rows->map(function (Appointment $apt) {
            return [
                'id' => $apt->id,
                'customer_id' => $apt->customer_id,
                'appointment' => $apt->type,
                'status' => $apt->status,
                'color' => $apt->color,
                'ghl_appointment_id' => $apt->ghl_appointment_id,
                'ghl_calendar_id' => $apt->ghl_calendar_id,
                'created_at' => $apt->created_at?->toIso8601String(),
                'appointment_datetime' => $apt->appointment_datetime?->format('Y-m-d\TH:i:s'),
                'assigned_technician' => $apt->technicians->pluck('id')->values()->all(),
                'services' => $apt->appointmentServices->map(fn ($as) => [
                    'service' => $as->serviceCategory?->slug,
                    'service_id' => $as->service_id,
                    'service_name' => $as->service?->name,
                    'service_color' => $as->service?->color,
                    'technician_id' => $as->user_id,
                    'quantity' => (int) ($as->quantity ?? 1),
                    'unit_price' => $as->unit_price !== null ? (float) $as->unit_price : null,
                ])->values()->all(),
            ];
        });

        return response()->json(['appointments' => $appointments]);
    }

    /**
     * Return customers in same shape as customers.json.
     */
    public function customers(): JsonResponse
    {
        $rows = Customer::query()
            ->withCount('appointments')
            ->get();

        $customers = $rows->map(function (Customer $c) {
            $totalSpent = (float) Payment::query()
                ->whereHas('appointment', fn ($q) => $q->where('customer_id', $c->id))
                ->sum('amount');

            return [
                'id' => $c->id,
                'firstName' => $c->first_name,
                'lastName' => $c->last_name,
                'phone' => $c->phone,
                'email' => $c->email,
                'createdAt' => $c->created_at?->format('Y-m-d'),
                'totalBookings' => $c->appointments_count ?? 0,
                'totalSpent' => round($totalSpent, 2),
                'creditBalance' => (float) $c->credit_balance,
                'profilePhoto' => $c->profile_photo ? Storage::disk('public')->url($c->profile_photo) : null,
            ];
        });

        return response()->json(['customers' => $customers]);
    }

    /**
     * Return users (salon staff) in same shape as users.json. Password excluded.
     * Technicians include clock_in and clock_out from turn_trackers table.
     */
    public function users(): JsonResponse
    {
        $rows = User::query()
            ->salonStaff()
            ->orderBy('id')
            ->get();

        $technicianIds = $rows->filter(fn (User $u) => $u->role === 'technician')->pluck('id')->all();
        $turnTrackers = TurnTracker::query()
            ->whereIn('user_id', $technicianIds)
            ->get()
            ->keyBy('user_id');

        $users = $rows->map(function (User $u) use ($turnTrackers) {
            $base = [
                'id' => $u->id,
                'username' => $u->username,
                'firstName' => $u->first_name,
                'lastName' => $u->last_name,
                'email' => $u->email,
                'phone' => $u->phone,
                'role' => $u->role,
                'status' => $u->status ?? 'active',
                'initials' => $u->initials,
                'profilePhoto' => $u->profile_photo,
                'profilePhotoUrl' => $u->profile_photo ? asset('storage/'.$u->profile_photo) : null,
                'createdAt' => $u->created_at?->format('Y-m-d'),
                'last_login_at' => $u->last_login_at?->toIso8601String(),
                'lastLogin' => $u->last_login_at?->format('M j, Y g:i A'),
                'ghlStaffId' => $u->ghl_staff_id,
                'ghlCalendarId' => $u->ghl_calendar_id,
            ];
            if ($u->role === 'technician') {
                $earnings = Payment::query()
                    ->whereHas('appointment.technicians', fn ($q) => $q->where('users.id', $u->id))
                    ->sum('amount');
                $base['totalEarnings'] = round((float) $earnings, 2);
                $base['totalTips'] = 0;
                $base['totalCommission'] = 0;
                $tracker = $turnTrackers->get($u->id);
                $base['clock_in'] = $tracker?->clock_in?->format('M j, Y g:i A');
                $base['clock_out'] = $tracker?->clock_out?->format('M j, Y g:i A');
                $base['services'] = $tracker ? (float) $tracker->services : 0;
            }

            return $base;
        });

        return response()->json(['users' => $users]);
    }

    /**
     * Return payments in same shape as payments.json.
     */
    public function payments(): JsonResponse
    {
        $rows = Payment::query()
            ->with(['appointment.customer', 'appointment.appointmentServices.service', 'appointment.appointmentServices.serviceCategory'])
            ->orderBy('paid_at')
            ->get();

        $payments = $rows->map(function (Payment $p) {
            $customer = $p->appointment?->customer;
            $name = $customer ? trim($customer->first_name.' '.$customer->last_name) : '';
            $initials = $customer ? strtoupper(mb_substr($customer->first_name, 0, 1).mb_substr($customer->last_name, 0, 1)) : '';
            $services = $p->appointment?->appointmentServices?->map(function ($svc) {
                $name = $svc->service?->name ?? $svc->serviceCategory?->name ?? 'Service';
                $qty = (int) ($svc->quantity ?? 1);
                $unitPrice = $svc->unit_price !== null ? (float) $svc->unit_price : 0.0;

                return [
                    'name' => $name,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'line_total' => round($qty * $unitPrice, 2),
                ];
            })->values()->all() ?? [];

            return [
                'id' => $p->id,
                'appointmentId' => $p->appointment_id,
                'customerName' => $name,
                'customerInitials' => $initials,
                'customerColor' => 'bg-[#e6f0f3]',
                'customerTextColor' => 'text-[#003047]',
                'amount' => (float) $p->amount,
                'subTotal' => (float) $p->sub_total,
                'discount' => (float) $p->discount,
                'credits' => (float) $p->credits,
                'giftCard' => (float) $p->gift_card,
                'tax' => (float) $p->tax,
                'tip' => (float) $p->tip,
                'method' => $p->method,
                'methodColor' => 'bg-[#e6f0f3]',
                'methodTextColor' => 'text-[#003047]',
                'status' => $status = ($p->status ?? 'Completed'),
                'refund_notes' => $p->refund_notes,
                'refunded_at' => $p->refunded_at?->format('Y-m-d\TH:i'),
                'statusColor' => in_array($status, ['Refunded', 'Voided'], true) ? 'bg-gray-100' : ($status === 'Pending' ? 'bg-amber-100' : 'bg-green-100'),
                'statusTextColor' => in_array($status, ['Refunded', 'Voided'], true) ? 'text-gray-700' : ($status === 'Pending' ? 'text-amber-700' : 'text-green-700'),
                'date' => $p->paid_at?->format('Y-m-d h:i A'),
                'services' => $services,
                'bookingId' => 'ORDER'.$p->appointment_id,
            ];
        });

        return response()->json(['payments' => $payments]);
    }

    /**
     * Return services in same shape as services.json.
     */
    public function services(): JsonResponse
    {
        $rows = Service::query()
            ->with('categories')
            ->orderBy('id')
            ->get();

        $services = $rows->map(fn (Service $s) => [
            'id' => $s->id,
            'name' => $s->name,
            'description' => $s->description,
            'price' => (float) $s->price,
            'active' => $s->active,
            'image' => $s->image,
            'image_url' => $s->image ? asset('storage/'.$s->image) : null,
            'color' => $s->color,
            'service_count' => (float) ($s->service_count ?? 0),
            'categories' => $s->categories->pluck('slug')->values()->all(),
        ]);

        return response()->json(['services' => $services]);
    }

    /**
     * Return service categories as object keyed by slug (same as service-categories.json).
     */
    public function serviceCategories(): JsonResponse
    {
        $categories = ServiceCategory::query()
            ->orderBy('slug')
            ->pluck('name', 'slug');

        return response()->json(['categories' => $categories]);
    }

    /**
     * Return bookings built from appointments (same shape as booking.json).
     */
    public function bookings(): JsonResponse
    {
        $rows = Appointment::query()
            ->with(['customer', 'technicians', 'appointmentServices.serviceCategory', 'appointmentServices.service', 'payment'])
            ->orderByDesc('appointment_datetime')
            ->get();

        $categoryIdToPrice = $this->buildCategoryIdToPriceMap();

        $bookings = $rows->map(function (Appointment $apt) use ($categoryIdToPrice) {
            $customer = $apt->customer;
            $customerName = $customer ? trim($customer->first_name.' '.$customer->last_name) : '';
            $technicianNames = $apt->technicians->map(fn ($u) => trim($u->first_name.' '.$u->last_name))->values()->all();
            $total = $apt->payment ? (float) $apt->payment->amount : 0.0;
            $services = $apt->appointmentServices->map(function ($as) use ($categoryIdToPrice) {
                $cat = $as->serviceCategory;
                $name = $cat?->name ?? 'Service';
                $price = $categoryIdToPrice[$as->service_category_id] ?? 0;

                return ['name' => $name, 'price' => $price, 'quantity' => 1];
            })->values()->all();

            return [
                'id' => 'ORDER'.$apt->id,
                'customerName' => $customerName,
                'status' => $apt->status,
                'bookingType' => $apt->type,
                'bookingDate' => $apt->appointment_datetime?->format('Y-m-d'),
                'bookingTime' => $apt->appointment_datetime?->format('H:i'),
                'total' => round($total, 2),
                'technician' => $technicianNames,
                'services' => $services,
            ];
        });

        return response()->json(['bookings' => $bookings]);
    }

    /**
     * Return payout transactions from appointment_technician (per technician).
     *
     * Query params:
     * - technician_id: required
     * - from: optional YYYY-MM-DD
     * - to: optional YYYY-MM-DD
     */
    public function payout(Request $request): JsonResponse
    {
        $technicianId = (int) $request->query('technician_id', 0);
        $currentRole = (string) $request->session()->get('salon_role', 'admin');
        $currentEmail = (string) $request->session()->get('salon_user_email', '');

        if ($currentRole === 'technician' && $currentEmail !== '') {
            $resolvedTechnicianId = User::query()->where('email', $currentEmail)->value('id');
            $technicianId = $resolvedTechnicianId ? (int) $resolvedTechnicianId : 0;
        }

        $from = $request->query('from');
        $to = $request->query('to');

        if ($technicianId <= 0) {
            return response()->json(['transactions' => []]);
        }

        $query = DB::table('appointment_technician as at')
            ->join('appointments as a', 'a.id', '=', 'at.appointment_id')
            ->join('payments as p', 'p.appointment_id', '=', 'a.id')
            ->where('at.user_id', $technicianId)
            ->whereNotNull('p.paid_at')
            ->select([
                'at.appointment_id as appointment_id',
                'p.paid_at as date',
                DB::raw("DATE_FORMAT(COALESCE(a.appointment_datetime, p.created_at), '%H:%i') as time"),
                'at.total_service as amount',
                'at.tip as tip',
                'at.commission as commission',
                'at.total as total',
            ])
            ->orderByDesc('p.paid_at')
            ->orderByDesc(DB::raw('COALESCE(a.appointment_datetime, p.created_at)'));

        if (is_string($from) && $from !== '') {
            $query->whereDate('p.paid_at', '>=', $from);
        }
        if (is_string($to) && $to !== '') {
            $query->whereDate('p.paid_at', '<=', $to);
        }

        $transactions = $query
            ->get()
            ->map(fn ($row) => [
                'appointment_id' => (int) $row->appointment_id,
                'date' => (string) $row->date,
                'time' => (string) ($row->time ?? '00:00'),
                'amount' => round((float) $row->amount, 2),
                'tip' => round((float) $row->tip, 2),
                'commission' => round((float) $row->commission, 2),
                'total' => round((float) $row->total, 2),
            ])
            ->values()
            ->all();

        return response()->json(['transactions' => $transactions]);
    }

    /**
     * Return a single technician (user) by id with legacy view shape + commissions.
     */
    public function technician(Request $request, int $id): JsonResponse
    {
        $user = User::query()
            ->salonStaff()
            ->find($id);

        if (! $user) {
            return response()->json(['message' => 'Technician not found'], 404);
        }

        $appointmentsWithPayment = Appointment::query()
            ->whereHas('technicians', fn ($q) => $q->where('users.id', $user->id))
            ->whereHas('payment')
            ->with('payment')
            ->get();

        $customersCount = $appointmentsWithPayment->pluck('customer_id')->unique()->count();
        $today = now()->toDateString();
        $todayServices = $appointmentsWithPayment->filter(fn ($a) => $a->appointment_datetime?->toDateString() === $today)->count();

        $commissions = DB::table('appointment_technician as at')
            ->join('appointments as a', 'a.id', '=', 'at.appointment_id')
            ->join('payments as p', 'p.appointment_id', '=', 'a.id')
            ->where('at.user_id', $user->id)
            ->whereNotNull('p.paid_at')
            ->groupBy('p.paid_at')
            ->orderByDesc('p.paid_at')
            ->selectRaw('p.paid_at as date')
            ->selectRaw('SUM(at.total_service) as total')
            ->selectRaw('SUM(at.tip) as tip')
            ->selectRaw('SUM(at.commission) as commission')
            ->get()
            ->map(fn ($row) => [
                'date' => (string) $row->date,
                'total' => round((float) $row->total, 2),
                'tip' => round((float) $row->tip, 2),
                'commission' => round((float) $row->commission, 2),
            ])
            ->all();

        $totalService = 0.0;
        $totalTip = 0.0;
        $totalCommission = 0.0;
        foreach ($commissions as $c) {
            $totalService += (float) ($c['total'] ?? 0);
            $totalTip += (float) ($c['tip'] ?? 0);
            $totalCommission += (float) ($c['commission'] ?? 0);
        }

        $avatarColors = ['pink', 'purple', 'teal', 'indigo', 'rose', 'blue'];
        $avatarColor = $avatarColors[($user->id - 1) % count($avatarColors)];

        $tracker = TurnTracker::query()->where('user_id', $user->id)->first();
        $clockIn = $tracker?->clock_in?->format('M j, Y g:i A');
        $clockOut = $tracker?->clock_out?->format('M j, Y g:i A');

        $isClockedIn = (bool) $tracker?->clock_in && ! $tracker?->clock_out;
        $statusLabel = $isClockedIn ? 'Active' : 'Inactive';
        $statusColor = $isClockedIn ? 'green' : 'gray';

        $technician = [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone' => $user->phone ?? '',
            'title' => 'Technician',
            'status' => $statusLabel,
            'status_color' => $statusColor,
            'avatar_color' => $avatarColor,
            'initials' => $user->initials ?? strtoupper(mb_substr($user->first_name ?? '', 0, 1).mb_substr($user->last_name ?? '', 0, 1)),
            'today_services' => $todayServices,
            'current_queue' => 0,
            'total_services' => $appointmentsWithPayment->count(),
            'total_earnings' => round($totalService, 2),
            'customers' => $customersCount,
            'total' => round($totalService, 2),
            'tip' => round($totalTip, 2),
            'commission' => round($totalCommission, 2),
            'clock_in' => $clockIn ?? '--',
            'clock_out' => $clockOut ?? '--',
            'profilePhoto' => $user->profile_photo,
            'profilePhotoUrl' => $user->profile_photo ? asset('storage/'.$user->profile_photo) : null,
        ];

        return response()->json([
            'technician' => $technician,
            'commissions' => $commissions,
        ]);
    }

    /**
     * Build a map of service_category_id => price (first service price per category).
     *
     * @return array<int, float>
     */
    private function buildCategoryIdToPriceMap(): array
    {
        $services = Service::query()
            ->with('categories:id')
            ->get();

        $map = [];
        foreach ($services as $service) {
            foreach ($service->categories as $category) {
                if (! isset($map[$category->id])) {
                    $map[$category->id] = (float) $service->price;
                }
            }
        }

        return $map;
    }
}

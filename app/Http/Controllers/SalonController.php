<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\GiftCard;
use App\Models\OnlineCheckin;
use App\Models\Payment;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Setting;
use App\Models\TurnTracker;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SalonController extends Controller
{
    public function showLogin(): View
    {
        return view('salon.auth.login');
    }

    public function loginPost(Request $request): RedirectResponse
    {
        $request->session()->put('salon_authenticated', true);
        $email = (string) $request->input('email', 'admin@salon.com');
        $request->session()->put('salon_user_email', $email);

        $user = User::query()->where('email', $email)->first();
        if ($user) {
            $user->forceFill(['last_login_at' => now()])->save();
            $request->session()->put('salon_role', $user->role ?? 'admin');
        }

        return redirect()->route('salon.dashboard');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget([
            'salon_authenticated',
            'salon_user_email',
            'salon_role',
            'salon_impersonator_email',
            'salon_impersonator_role',
        ]);

        return redirect()->route('salon.login');
    }

    public function showForgotPassword(): View
    {
        return view('salon.auth.forgot-password');
    }

    public function dashboard(Request $request): View|RedirectResponse
    {
        $currentRole = $request->session()->get('salon_role', 'admin');

        $customersTotal = (int) Customer::query()->count();

        $activeTechnicians = (int) TurnTracker::query()
            ->whereNotNull('clock_in')
            ->whereNull('clock_out')
            ->whereHas('user', fn ($q) => $q->where('role', 'technician'))
            ->count();

        $waitingBase = Appointment::query()->whereRaw('LOWER(status) = ?', ['waiting']);
        $waitingBooked = (int) (clone $waitingBase)->whereRaw('LOWER(type) = ?', ['booked'])->count();
        $waitingWalkIn = (int) (clone $waitingBase)->whereRaw('LOWER(type) = ?', ['walk-in'])->count();

        $ticketsUnpaid = (int) Appointment::query()->whereRaw('LOWER(status) = ?', ['unpaid'])->count();
        $ticketsPaid = (int) Appointment::query()->whereRaw('LOWER(status) = ?', ['paid'])->count();
        $ticketsCancelled = (int) Appointment::query()
            ->whereRaw('LOWER(status) IN (?, ?)', ['cancelled', 'canceled'])
            ->count();

        $ticketsRefunded = (int) Appointment::query()
            ->where(function ($query) {
                $query
                    ->whereRaw('LOWER(status) = ?', ['refunded'])
                    ->orWhereHas('payment', fn ($p) => $p->whereRaw('LOWER(status) = ?', ['refunded']));
            })
            ->count();

        $dashboardTechnician = null;
        $technicianStats = null;

        if ($currentRole === 'technician') {
            $email = (string) $request->session()->get('salon_user_email', '');
            $dashboardTechnician = $email !== ''
                ? User::query()->technicians()->where('email', $email)->first()
                : null;

            if (! $dashboardTechnician && $email !== '') {
                $dashboardTechnician = User::query()->where('email', $email)->first();
            }
        }

        $recentWaitingList = [];
        if (in_array($currentRole, ['admin', 'receptionist', 'technician'], true)) {
            $waitingQuery = Appointment::query()
                ->whereRaw('LOWER(status) = ?', ['waiting'])
                ->with([
                    'customer:id,first_name,last_name',
                    'appointmentServices.service:id,name',
                    'appointmentServices.serviceCategory:service_categories.id,service_categories.name,service_categories.slug',
                    'technicians:id',
                ])
                ->orderByDesc('appointment_datetime')
                ->orderByDesc('created_at');

            if ($currentRole === 'technician') {
                $technicianId = $dashboardTechnician?->id;
                if ($technicianId) {
                    $waitingQuery->whereHas('technicians', fn ($q) => $q->where('users.id', (int) $technicianId));
                } else {
                    $waitingQuery->whereRaw('1 = 0');
                }
            }

            $recentWaitingList = $waitingQuery
                ->limit(5)
                ->get()
                ->map(function (Appointment $apt) {
                    $customerName = trim(($apt->customer?->first_name ?? '').' '.($apt->customer?->last_name ?? ''));
                    $customerInitials = strtoupper(
                        mb_substr((string) ($apt->customer?->first_name ?? 'C'), 0, 1)
                        .mb_substr((string) ($apt->customer?->last_name ?? 'U'), 0, 1)
                    );

                    $services = $apt->appointmentServices ?? collect();
                    $serviceCount = method_exists($services, 'count') ? (int) $services->count() : 0;
                    $firstServiceName = $serviceCount > 0
                        ? (($services[0]->service?->name ?? $services[0]->serviceCategory?->name ?? $services[0]->serviceCategory?->slug) ?: 'Service')
                        : null;
                    $servicesSummary = $serviceCount === 0
                        ? null
                        : ($serviceCount === 1 ? $firstServiceName : ($firstServiceName.' + '.($serviceCount - 1).' more'));

                    $when = $apt->appointment_datetime ?? $apt->created_at;

                    return [
                        'id' => $apt->id,
                        'type' => $apt->type,
                        'customerName' => $customerName !== '' ? $customerName : 'Customer #'.((int) $apt->customer_id),
                        'customerInitials' => $customerInitials !== '' ? $customerInitials : 'CU',
                        'servicesSummary' => $servicesSummary,
                        'whenAgo' => $when ? $when->diffForHumans() : null,
                        'whenDisplay' => $when ? $when->format('M j, Y g:i A') : null,
                    ];
                })
                ->values()
                ->all();
        }

        if ($currentRole === 'technician') {
            $today = now()->toDateString();
            $technicianId = $dashboardTechnician?->id;

            if ($technicianId) {
                $baseAppointments = DB::table('appointments as a')
                    ->join('appointment_technician as at', 'at.appointment_id', '=', 'a.id')
                    ->where('at.user_id', $technicianId)
                    ->whereRaw('DATE(COALESCE(a.appointment_datetime, a.created_at)) = ?', [$today]);

                $appointmentsToday = (int) (clone $baseAppointments)->distinct()->count('a.id');

                $completedToday = (int) (clone $baseAppointments)
                    ->whereRaw('LOWER(a.status) IN (?, ?)', ['paid', 'completed'])
                    ->distinct()
                    ->count('a.id');

                $inProgressToday = (int) (clone $baseAppointments)
                    ->whereRaw('LOWER(a.status) = ?', ['in-progress'])
                    ->distinct()
                    ->count('a.id');

                $totals = DB::table('appointment_technician as at')
                    ->where('at.user_id', $technicianId)
                    ->selectRaw('COALESCE(SUM(at.total), 0) AS total')
                    ->selectRaw('COALESCE(SUM(at.tip), 0) AS tip')
                    ->selectRaw('COALESCE(SUM(at.commission), 0) AS commission')
                    ->first();

                $technicianStats = [
                    'appointments_today' => $appointmentsToday,
                    'completed_today' => $completedToday,
                    'in_progress_today' => $inProgressToday,
                    'total_today' => (float) ($totals->total ?? 0),
                    'tip_today' => (float) ($totals->tip ?? 0),
                    'commission_today' => (float) ($totals->commission ?? 0),
                ];
            } else {
                $technicianStats = [
                    'appointments_today' => 0,
                    'completed_today' => 0,
                    'in_progress_today' => 0,
                    'total_today' => 0.0,
                    'tip_today' => 0.0,
                    'commission_today' => 0.0,
                ];
            }
        }

        return view('salon.dashboard.index', [
            'pageTitle' => 'Dashboard',
            'current_role' => $currentRole,
            'dashboardStats' => [
                'customers_total' => $customersTotal,
                'technicians_active' => $activeTechnicians,
                'waiting_booked' => $waitingBooked,
                'waiting_walk_in' => $waitingWalkIn,
                'tickets_unpaid' => $ticketsUnpaid,
                'tickets_paid' => $ticketsPaid,
                'tickets_cancelled' => $ticketsCancelled,
                'tickets_refunded' => $ticketsRefunded,
            ],
            'recentWaitingList' => $recentWaitingList,
            'dashboardTechnician' => $dashboardTechnician,
            'technicianStats' => $technicianStats,
        ]);
    }

    public function booking(): View
    {
        return view('salon.booking.booking', ['pageTitle' => 'New Booking']);
    }

    public function calendar(): View
    {
        return view('salon.booking.calendar', ['pageTitle' => 'Calendar']);
    }

    public function createTicket(): View
    {
        return view('salon.booking.create-ticket', ['pageTitle' => 'Create Ticket']);
    }

    public function editBooking(Request $request): View
    {
        $bookingId = (int) $request->query('id', 0);

        $editBookingBootstrap = null;
        if ($bookingId > 0) {
            $appointment = Appointment::query()
                ->with(['technicians', 'appointmentServices.serviceCategory', 'appointmentServices.service'])
                ->find($bookingId);

            $bookingPayload = null;
            $error = null;

            if (! $appointment) {
                $error = 'Booking not found.';
            } else {
                $bookingPayload = [
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

            $customerRows = Customer::query()
                ->withCount('appointments')
                ->orderByDesc('id')
                ->get();

            $spentByCustomerId = Payment::query()
                ->selectRaw('appointments.customer_id AS customer_id, COALESCE(SUM(payments.amount), 0) AS total_spent')
                ->join('appointments', 'appointments.id', '=', 'payments.appointment_id')
                ->whereIn('appointments.customer_id', $customerRows->pluck('id')->all())
                ->groupBy('appointments.customer_id')
                ->pluck('total_spent', 'customer_id')
                ->all();

            $customers = $customerRows
                ->map(function (Customer $c) use ($spentByCustomerId) {
                    $totalSpent = (float) ($spentByCustomerId[$c->id] ?? 0);

                    return [
                        'id' => $c->id,
                        'firstName' => $c->first_name,
                        'lastName' => $c->last_name,
                        'phone' => $c->phone,
                        'email' => $c->email,
                        'createdAt' => $c->created_at?->format('Y-m-d'),
                        'totalBookings' => (int) ($c->appointments_count ?? 0),
                        'totalSpent' => round($totalSpent, 2),
                        'creditBalance' => (float) $c->credit_balance,
                        'profilePhoto' => $c->profile_photo ? Storage::disk('public')->url($c->profile_photo) : null,
                    ];
                })
                ->values()
                ->all();

            $technicianRows = User::query()
                ->technicians()
                ->orderBy('id')
                ->get([
                    'id',
                    'first_name',
                    'last_name',
                    'role',
                    'status',
                    'initials',
                    'profile_photo',
                ]);

            $techIds = $technicianRows->pluck('id')->all();
            $turnTrackers = TurnTracker::query()
                ->whereIn('user_id', $techIds)
                ->get()
                ->keyBy('user_id');

            $technicians = $technicianRows
                ->map(function (User $u) use ($turnTrackers) {
                    $tracker = $turnTrackers->get($u->id);

                    return [
                        'id' => $u->id,
                        'firstName' => $u->first_name,
                        'lastName' => $u->last_name,
                        'role' => $u->role,
                        'status' => $u->status ?? 'active',
                        'initials' => $u->initials,
                        'profilePhoto' => $u->profile_photo,
                        'profilePhotoUrl' => $u->profile_photo ? asset('storage/'.$u->profile_photo) : null,
                        'clock_in' => $tracker?->clock_in?->format('M j, Y g:i A'),
                        'clock_out' => $tracker?->clock_out?->format('M j, Y g:i A'),
                        'services' => $tracker ? (int) $tracker->services : 0,
                    ];
                })
                ->values()
                ->all();

            $editBookingBootstrap = [
                'bookingId' => $bookingId,
                'booking' => $bookingPayload,
                'customers' => $customers,
                'users' => $technicians,
                'error' => $error,
            ];
        }

        return view('salon.booking.edit-booking', [
            'pageTitle' => 'Edit Booking',
            'editBookingBootstrap' => $editBookingBootstrap,
        ]);
    }

    public function pay(Request $request): View
    {
        $appointmentId = (int) $request->query('id', 0);

        $payBootstrap = null;
        if ($appointmentId > 0) {
            $appointment = Appointment::query()
                ->with([
                    'customer',
                    'technicians:id,first_name,last_name,initials,role,status',
                    'appointmentServices.serviceCategory:service_categories.id,service_categories.slug',
                    'appointmentServices.service:id,name',
                ])
                ->find($appointmentId);

            if ($appointment && $appointment->customer) {
                $customer = $appointment->customer;

                $totalSpent = (float) Payment::query()
                    ->whereHas('appointment', fn ($q) => $q->where('customer_id', $customer->id))
                    ->sum('amount');

                $customerPayload = [
                    'id' => $customer->id,
                    'firstName' => $customer->first_name,
                    'lastName' => $customer->last_name,
                    'phone' => $customer->phone,
                    'email' => $customer->email,
                    'createdAt' => $customer->created_at?->format('Y-m-d'),
                    'totalBookings' => $customer->appointments()->count(),
                    'totalSpent' => round($totalSpent, 2),
                    'creditBalance' => (float) $customer->credit_balance,
                    'profilePhoto' => $customer->profile_photo ? Storage::disk('public')->url($customer->profile_photo) : null,
                ];

                $appointmentPayload = [
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

                $categories = ServiceCategory::query()
                    ->orderBy('slug')
                    ->pluck('name', 'slug')
                    ->all();

                $services = Service::query()
                    ->with('categories')
                    ->orderBy('id')
                    ->get()
                    ->map(fn (Service $s) => [
                        'id' => $s->id,
                        'name' => $s->name,
                        'description' => $s->description,
                        'price' => (float) $s->price,
                        'active' => (bool) $s->active,
                        'image' => $s->image,
                        'image_url' => $s->image ? asset('storage/'.$s->image) : null,
                        'color' => $s->color,
                        'categories' => $s->categories->pluck('slug')->values()->all(),
                    ])
                    ->values()
                    ->all();

                $technicianRows = User::query()
                    ->technicians()
                    ->orderBy('id')
                    ->get([
                        'id',
                        'first_name',
                        'last_name',
                        'role',
                        'status',
                        'initials',
                        'profile_photo',
                    ]);

                $techIds = $technicianRows->pluck('id')->all();
                $turnTrackers = TurnTracker::query()
                    ->whereIn('user_id', $techIds)
                    ->get()
                    ->keyBy('user_id');

                $technicians = $technicianRows
                    ->map(function (User $u) use ($turnTrackers) {
                        $tracker = $turnTrackers->get($u->id);

                        return [
                            'id' => $u->id,
                            'firstName' => $u->first_name,
                            'lastName' => $u->last_name,
                            'role' => $u->role,
                            'status' => $u->status ?? 'active',
                            'initials' => $u->initials,
                            'profilePhotoUrl' => $u->profile_photo ? asset('storage/'.$u->profile_photo) : null,
                            'clock_in' => $tracker?->clock_in?->format('M j, Y g:i A'),
                            'clock_out' => $tracker?->clock_out?->format('M j, Y g:i A'),
                            'services' => $tracker ? (int) $tracker->services : 0,
                        ];
                    })
                    ->values()
                    ->all();

                $settings = Setting::getAllAsKeyValue();
                $settingsPayload = [
                    'discounts_enabled' => $settings['discounts_enabled'] ?? null,
                    'gift_cards_enabled' => $settings['gift_cards_enabled'] ?? null,
                    'tax_rate' => $settings['tax_rate'] ?? null,
                    'tax_name' => $settings['tax_name'] ?? null,
                    'tax_apply_to_all' => $settings['tax_apply_to_all'] ?? null,
                    'points_rate_fixed' => $settings['points_rate_fixed'] ?? null,
                    'points_rate_percentage' => $settings['points_rate_percentage'] ?? null,
                    'points_unit_value' => $settings['points_unit_value'] ?? null,
                    'points_per_unit' => $settings['points_per_unit'] ?? null,
                    'reward_percentage' => $settings['reward_percentage'] ?? null,
                ];

                $coupons = Coupon::query()
                    ->orderByDesc('id')
                    ->get()
                    ->map(fn (Coupon $c) => [
                        'id' => $c->id,
                        'code' => $c->code,
                        'description' => $c->description,
                        'discount_type' => $c->discount_type,
                        'discount_value' => (float) $c->discount_value,
                        'min_order_amount' => $c->min_order_amount !== null ? (float) $c->min_order_amount : null,
                        'active' => (bool) $c->active,
                    ])
                    ->values()
                    ->all();

                $giftCards = GiftCard::query()
                    ->orderByDesc('id')
                    ->get()
                    ->map(fn (GiftCard $g) => [
                        'id' => $g->id,
                        'code' => $g->code,
                        'description' => $g->description,
                        'pin' => $g->pin,
                        'initial_value' => (float) $g->initial_value,
                        'balance' => (float) $g->balance,
                        'active' => (bool) $g->active,
                    ])
                    ->values()
                    ->all();

                $payBootstrap = [
                    'appointmentId' => $appointmentId,
                    'appointment' => $appointmentPayload,
                    'customer' => $customerPayload,
                    'categories' => $categories,
                    'services' => $services,
                    'users' => $technicians,
                    'settings' => $settingsPayload,
                    'coupons' => $coupons,
                    'gift_cards' => $giftCards,
                ];
            } else {
                $payBootstrap = [
                    'appointmentId' => $appointmentId,
                    'error' => 'Appointment not found',
                ];
            }
        }

        return view('salon.booking.pay', [
            'pageTitle' => 'Pay',
            'payBootstrap' => $payBootstrap,
        ]);
    }

    public function tickets(): View
    {
        $appointmentsQuery = Appointment::query()
            ->whereRaw('LOWER(status) IN (?, ?, ?, ?, ?, ?)', [
                'unpaid',
                'paid',
                'cancelled',
                'canceled',
                'refunded',
                'closed',
            ])
            ->with([
                'technicians:id,first_name,last_name,initials,role,status',
                'appointmentServices.serviceCategory:service_categories.id,service_categories.slug',
                'appointmentServices.service:id,name',
            ])
            ->orderByDesc('appointment_datetime')
            ->orderByDesc('created_at');

        $appointmentRows = $appointmentsQuery->get();
        $appointmentIds = $appointmentRows->pluck('id')->values()->all();

        $customerIds = $appointmentRows
            ->pluck('customer_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $customers = Customer::query()
            ->whereIn('id', $customerIds)
            ->select(['id', 'first_name', 'last_name', 'phone', 'email', 'created_at'])
            ->orderByDesc('id')
            ->get()
            ->map(fn (Customer $c) => [
                'id' => $c->id,
                'firstName' => $c->first_name,
                'lastName' => $c->last_name,
                'phone' => $c->phone,
                'email' => $c->email,
                'createdAt' => $c->created_at?->format('Y-m-d'),
            ])
            ->values()
            ->all();

        $appointments = $appointmentRows
            ->map(fn (Appointment $apt) => [
                'id' => $apt->id,
                'customer_id' => $apt->customer_id,
                'appointment' => $apt->type,
                'status' => $apt->status,
                'created_at' => $apt->created_at?->toIso8601String(),
                'appointment_datetime' => $apt->appointment_datetime?->toIso8601String(),
                'assigned_technician' => $apt->technicians->pluck('id')->values()->all(),
                'services' => $apt->appointmentServices->map(fn ($as) => [
                    'service' => $as->serviceCategory?->slug,
                    'service_id' => $as->service_id,
                    'service_name' => $as->service?->name,
                    'technician_id' => $as->user_id,
                    'quantity' => (int) ($as->quantity ?? 1),
                    'unit_price' => $as->unit_price !== null ? (float) $as->unit_price : null,
                ])->values()->all(),
            ])
            ->values()
            ->all();

        $technicianRows = User::query()
            ->technicians()
            ->orderBy('id')
            ->get(['id', 'first_name', 'last_name', 'role', 'status', 'initials', 'profile_photo']);

        $techIds = $technicianRows->pluck('id')->all();
        $turnTrackers = TurnTracker::query()
            ->whereIn('user_id', $techIds)
            ->get()
            ->keyBy('user_id');

        $technicians = $technicianRows
            ->map(function (User $u) use ($turnTrackers) {
                $tracker = $turnTrackers->get($u->id);

                return [
                    'id' => $u->id,
                    'firstName' => $u->first_name,
                    'lastName' => $u->last_name,
                    'role' => $u->role,
                    'status' => $u->status ?? 'active',
                    'initials' => $u->initials,
                    'profilePhotoUrl' => $u->profile_photo ? asset('storage/'.$u->profile_photo) : null,
                    'clock_in' => $tracker?->clock_in?->format('M j, Y g:i A'),
                    'clock_out' => $tracker?->clock_out?->format('M j, Y g:i A'),
                    'services' => $tracker ? (int) $tracker->services : 0,
                ];
            })
            ->values()
            ->all();

        $paymentRows = Payment::query()
            ->whereIn('appointment_id', $appointmentIds)
            ->with(['appointment.customer', 'appointment.appointmentServices.service', 'appointment.appointmentServices.serviceCategory'])
            ->orderBy('paid_at')
            ->get();

        $payments = $paymentRows->map(function (Payment $p) {
            $customer = $p->appointment?->customer;
            $name = $customer ? trim($customer->first_name.' '.$customer->last_name) : '';
            $initials = $customer ? strtoupper(mb_substr($customer->first_name, 0, 1).mb_substr($customer->last_name, 0, 1)) : '';
            $services = $p->appointment?->appointmentServices?->map(function ($svc) {
                $serviceName = $svc->service?->name ?? $svc->serviceCategory?->name ?? 'Service';
                $qty = (int) ($svc->quantity ?? 1);
                $unitPrice = $svc->unit_price !== null ? (float) $svc->unit_price : 0.0;

                return [
                    'name' => $serviceName,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'line_total' => round($qty * $unitPrice, 2),
                ];
            })->values()->all() ?? [];

            $status = $p->status ?? 'Completed';

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
                'status' => $status,
                'refund_notes' => $p->refund_notes,
                'refunded_at' => $p->refunded_at?->format('Y-m-d\TH:i'),
                'statusColor' => in_array($status, ['Refunded', 'Voided'], true) ? 'bg-gray-100' : ($status === 'Pending' ? 'bg-amber-100' : 'bg-green-100'),
                'statusTextColor' => in_array($status, ['Refunded', 'Voided'], true) ? 'text-gray-700' : ($status === 'Pending' ? 'text-amber-700' : 'text-green-700'),
                'date' => $p->paid_at?->format('Y-m-d'),
                'services' => $services,
                'bookingId' => 'ORDER'.$p->appointment_id,
            ];
        })->values()->all();

        return view('salon.booking.tickets', [
            'pageTitle' => 'Tickets',
            'ticketsBootstrap' => [
                'customers' => $customers,
                'appointments' => $appointments,
                'users' => $technicians,
                'payments' => $payments,
            ],
        ]);
    }

    public function waitingList(Request $request): View
    {
        $customers = Customer::query()
            ->select(['id', 'first_name', 'last_name', 'phone', 'email', 'created_at'])
            ->orderByDesc('id')
            ->get()
            ->map(fn (Customer $c) => [
                'id' => $c->id,
                'firstName' => $c->first_name,
                'lastName' => $c->last_name,
                'phone' => $c->phone,
                'email' => $c->email,
                'createdAt' => $c->created_at?->format('Y-m-d'),
            ])
            ->values()
            ->all();

        $appointments = Appointment::query()
            ->whereRaw('LOWER(status) = ?', ['waiting'])
            ->with(['technicians:id,first_name,last_name,initials', 'appointmentServices.serviceCategory:service_categories.id,service_categories.slug', 'appointmentServices.service:id,name'])
            ->orderByDesc('appointment_datetime')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Appointment $apt) => [
                'id' => $apt->id,
                'customer_id' => $apt->customer_id,
                'appointment' => $apt->type,
                'status' => $apt->status,
                'created_at' => $apt->created_at?->toIso8601String(),
                'appointment_datetime' => $apt->appointment_datetime?->toIso8601String(),
                'assigned_technician' => $apt->technicians->pluck('id')->values()->all(),
                'services' => $apt->appointmentServices->map(fn ($as) => [
                    'service' => $as->serviceCategory?->slug,
                    'service_id' => $as->service_id,
                    'service_name' => $as->service?->name,
                    'technician_id' => $as->user_id,
                    'quantity' => (int) ($as->quantity ?? 1),
                    'unit_price' => $as->unit_price !== null ? (float) $as->unit_price : null,
                ])->values()->all(),
            ])
            ->values()
            ->all();

        $technicianRows = User::query()
            ->technicians()
            ->orderBy('id')
            ->get(['id', 'first_name', 'last_name', 'role', 'status', 'initials', 'profile_photo']);

        $techIds = $technicianRows->pluck('id')->all();
        $turnTrackers = TurnTracker::query()
            ->whereIn('user_id', $techIds)
            ->get()
            ->keyBy('user_id');

        $technicians = $technicianRows
            ->map(function (User $u) use ($turnTrackers) {
                $tracker = $turnTrackers->get($u->id);

                return [
                    'id' => $u->id,
                    'firstName' => $u->first_name,
                    'lastName' => $u->last_name,
                    'role' => $u->role,
                    'status' => $u->status ?? 'active',
                    'initials' => $u->initials,
                    'profilePhotoUrl' => $u->profile_photo ? asset('storage/'.$u->profile_photo) : null,
                    'clock_in' => $tracker?->clock_in?->format('M j, Y g:i A'),
                    'clock_out' => $tracker?->clock_out?->format('M j, Y g:i A'),
                    'services' => $tracker ? (int) $tracker->services : 0,
                ];
            })
            ->values()
            ->all();

        return view('salon.booking.waiting-list', [
            'pageTitle' => 'Waiting List',
            'waitingListBootstrap' => [
                'customers' => $customers,
                'appointments' => $appointments,
                'users' => $technicians,
            ],
        ]);
    }

    public function customersIndex(): View
    {
        $rows = Customer::query()
            ->withCount('appointments')
            ->orderByDesc('id')
            ->get();

        $visitAggByCustomerId = Appointment::query()
            ->whereIn('customer_id', $rows->pluck('id')->all())
            ->selectRaw('customer_id as customer_id')
            ->selectRaw('COUNT(*) as total_visits')
            ->selectRaw('MAX(COALESCE(appointment_datetime, created_at)) as last_visit_at')
            ->groupBy('customer_id')
            ->get()
            ->keyBy('customer_id');

        $spentByCustomerId = Payment::query()
            ->selectRaw('appointments.customer_id AS customer_id, COALESCE(SUM(payments.amount), 0) AS total_spent')
            ->join('appointments', 'appointments.id', '=', 'payments.appointment_id')
            ->whereIn('appointments.customer_id', $rows->pluck('id')->all())
            ->groupBy('appointments.customer_id')
            ->pluck('total_spent', 'customer_id')
            ->all();

        $customers = $rows
            ->map(function (Customer $c) use ($spentByCustomerId, $visitAggByCustomerId) {
                $totalSpent = (float) ($spentByCustomerId[$c->id] ?? 0);
                $visitAgg = $visitAggByCustomerId->get($c->id);

                $lastVisitAt = $visitAgg?->last_visit_at ? \Carbon\Carbon::parse($visitAgg->last_visit_at) : null;

                return [
                    'id' => $c->id,
                    'firstName' => $c->first_name,
                    'lastName' => $c->last_name,
                    'phone' => $c->phone,
                    'email' => $c->email,
                    'createdAt' => $c->created_at?->format('Y-m-d'),
                    'totalBookings' => (int) ($c->appointments_count ?? 0),
                    'totalVisits' => (int) ($visitAgg?->total_visits ?? 0),
                    'totalSpent' => round($totalSpent, 2),
                    'lastVisitDate' => $lastVisitAt?->format('Y-m-d'),
                    'lastVisit' => $lastVisitAt ? $lastVisitAt->diffForHumans() : null,
                    'creditBalance' => (float) $c->credit_balance,
                    'profilePhoto' => $c->profile_photo ? Storage::disk('public')->url($c->profile_photo) : null,
                ];
            })
            ->values()
            ->all();

        return view('salon.customers.index', [
            'pageTitle' => 'Customers',
            'customersBootstrap' => [
                'customers' => $customers,
            ],
        ]);
    }

    public function customersView(Request $request): View|RedirectResponse
    {
        $id = $request->query('id');
        if (! $id) {
            return redirect()->route('salon.customers.index')
                ->with('error', 'No customer selected.');
        }

        $customer = Customer::query()
            ->withCount('appointments')
            ->find($id);

        if (! $customer) {
            return redirect()->route('salon.customers.index')
                ->with('error', 'Customer not found.');
        }

        $creditLedgers = $customer->creditLedgers()
            ->with('user')
            ->latest('created_at')
            ->limit(100)
            ->get();

        $onlineCheckins = OnlineCheckin::query()
            ->where('customer_id', $customer->id)
            ->with(['appointment.technicians', 'appointment.appointmentServices.service', 'appointment.appointmentServices.serviceCategory', 'appointment.payment'])
            ->latest('created_at')
            ->limit(100)
            ->get();

        $bookings = Appointment::query()
            ->where('customer_id', $customer->id)
            ->with([
                'technicians',
                'appointmentServices.serviceCategory',
                'appointmentServices.service',
                'payment',
            ])
            ->orderByDesc('appointment_datetime')
            ->orderByDesc('created_at')
            ->get();

        $totalSpent = (float) Payment::query()
            ->whereHas('appointment', fn ($q) => $q->where('customer_id', $customer->id))
            ->sum('amount');

        $lastVisitDate = $bookings->first()?->appointment_datetime?->format('Y-m-d')
            ?? $bookings->first()?->created_at?->format('Y-m-d');

        $bookingDates = $bookings->map(fn (Appointment $a) => $a->created_at?->format('Y-m-d') ?? $a->appointment_datetime?->format('Y-m-d'))->filter();
        $customerSince = $bookingDates->isNotEmpty() ? $bookingDates->min() : $customer->created_at?->format('Y-m-d');

        $profilePhotoUrl = $customer->profile_photo
            ? Storage::disk('public')->url($customer->profile_photo)
            : null;

        return view('salon.customers.view', [
            'pageTitle' => 'Customer Details',
            'customer' => $customer,
            'creditLedgers' => $creditLedgers,
            'bookings' => $bookings,
            'totalSpent' => $totalSpent,
            'totalVisits' => $bookings->count(),
            'lastVisitDate' => $lastVisitDate,
            'customerSince' => $customerSince,
            'profilePhotoUrl' => $profilePhotoUrl,
            'onlineCheckins' => $onlineCheckins,
        ]);
    }

    public function servicesIndex(): View
    {
        $categories = ServiceCategory::query()
            ->orderBy('slug')
            ->pluck('name', 'slug')
            ->all();

        $services = Service::query()
            ->with('categories')
            ->orderBy('id')
            ->get()
            ->map(fn (Service $s) => [
                'id' => $s->id,
                'name' => $s->name,
                'description' => $s->description,
                'price' => (float) $s->price,
                'active' => (bool) $s->active,
                'image' => $s->image,
                'image_url' => $s->image ? asset('storage/'.$s->image) : null,
                'color' => $s->color,
                'categories' => $s->categories->pluck('slug')->values()->all(),
            ])
            ->values()
            ->all();

        return view('salon.services.index', [
            'pageTitle' => 'Services',
            'servicesBootstrap' => [
                'categories' => $categories,
                'services' => $services,
            ],
        ]);
    }

    public function techniciansIndex(): View
    {
        $technicianRows = User::query()
            ->technicians()
            ->orderBy('id')
            ->get([
                'id',
                'username',
                'first_name',
                'last_name',
                'email',
                'phone',
                'role',
                'status',
                'initials',
                'profile_photo',
                'created_at',
            ]);

        $techIds = $technicianRows->pluck('id')->all();

        $turnTrackers = TurnTracker::query()
            ->whereIn('user_id', $techIds)
            ->get()
            ->keyBy('user_id');

        $totalsByTechId = DB::table('appointment_technician as at')
            ->join('appointments as a', 'a.id', '=', 'at.appointment_id')
            ->join('payments as p', 'p.appointment_id', '=', 'a.id')
            ->whereIn('at.user_id', $techIds)
            ->whereNotNull('p.paid_at')
            ->selectRaw('at.user_id as user_id')
            ->selectRaw('COALESCE(SUM(at.total_service), 0) AS total_earnings')
            ->selectRaw('COALESCE(SUM(at.tip), 0) AS total_tips')
            ->selectRaw('COALESCE(SUM(at.commission), 0) AS total_commission')
            ->groupBy('at.user_id')
            ->get()
            ->keyBy('user_id');

        $technicians = $technicianRows
            ->map(function (User $u) use ($turnTrackers, $totalsByTechId) {
                $tracker = $turnTrackers->get($u->id);
                $totals = $totalsByTechId->get($u->id);

                return [
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
                    'clock_in' => $tracker?->clock_in?->format('M j, Y g:i A'),
                    'clock_out' => $tracker?->clock_out?->format('M j, Y g:i A'),
                    'services' => $tracker ? (int) $tracker->services : 0,
                    'totalEarnings' => $totals ? (float) $totals->total_earnings : 0.0,
                    'totalTips' => $totals ? (float) $totals->total_tips : 0.0,
                    'totalCommission' => $totals ? (float) $totals->total_commission : 0.0,
                ];
            })
            ->values()
            ->all();

        return view('salon.technicians.index', [
            'pageTitle' => 'Technicians',
            'techniciansBootstrap' => [
                'users' => $technicians,
            ],
        ]);
    }

    public function techniciansView(Request $request): View
    {
        $technicianId = (int) $request->query('id', 0);
        $currentRole = (string) $request->session()->get('salon_role', 'admin');
        $currentEmail = (string) $request->session()->get('salon_user_email', '');

        if ($currentRole === 'technician' && $currentEmail !== '') {
            $resolvedTechnicianId = User::query()->where('email', $currentEmail)->value('id');
            $technicianId = $resolvedTechnicianId ? (int) $resolvedTechnicianId : 0;
        }

        $technicianViewBootstrap = null;
        if ($technicianId > 0) {
            $user = User::query()
                ->salonStaff()
                ->find($technicianId);

            if (! $user) {
                $technicianViewBootstrap = [
                    'technicianId' => $technicianId,
                    'error' => 'Technician not found',
                ];
            } else {
                $totalsRow = DB::table('appointment_technician as at')
                    ->join('appointments as a', 'a.id', '=', 'at.appointment_id')
                    ->join('payments as p', 'p.appointment_id', '=', 'a.id')
                    ->where('at.user_id', $user->id)
                    ->whereNotNull('p.paid_at')
                    ->selectRaw('COALESCE(SUM(at.total_service), 0) AS total_service')
                    ->selectRaw('COALESCE(SUM(at.tip), 0) AS total_tip')
                    ->selectRaw('COALESCE(SUM(at.commission), 0) AS total_commission')
                    ->first();

                $customersCount = (int) DB::table('appointment_technician as at')
                    ->join('appointments as a', 'a.id', '=', 'at.appointment_id')
                    ->join('payments as p', 'p.appointment_id', '=', 'a.id')
                    ->where('at.user_id', $user->id)
                    ->whereNotNull('p.paid_at')
                    ->distinct()
                    ->count('a.customer_id');

                $avatarColors = ['pink', 'purple', 'teal', 'indigo', 'rose', 'blue'];
                $avatarColor = $avatarColors[($user->id - 1) % count($avatarColors)];

                $tracker = TurnTracker::query()
                    ->where('user_id', $user->id)
                    ->first();

                $clockIn = $tracker?->clock_in?->format('M j, Y g:i A') ?? '--';
                $clockOut = $tracker?->clock_out?->format('M j, Y g:i A') ?? '--';

                $isClockedIn = (bool) $tracker?->clock_in && ! $tracker?->clock_out;
                $statusLabel = $isClockedIn ? 'Available' : 'Offline';
                $statusColor = $isClockedIn ? 'green' : 'gray';

                $totalService = (float) ($totalsRow->total_service ?? 0);
                $totalTip = (float) ($totalsRow->total_tip ?? 0);
                $totalCommission = (float) ($totalsRow->total_commission ?? 0);

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
                    'customers' => $customersCount,
                    'total' => round($totalService, 2),
                    'tip' => round($totalTip, 2),
                    'commission' => round($totalCommission, 2),
                    'clock_in' => $clockIn,
                    'clock_out' => $clockOut,
                    'profilePhoto' => $user->profile_photo,
                    'profilePhotoUrl' => $user->profile_photo ? asset('storage/'.$user->profile_photo) : null,
                ];

                $from = $request->query('from');
                $to = $request->query('to');
                $dateType = $request->query('datetype');

                if (! is_string($from) || $from === '' || ! is_string($to) || $to === '') {
                    $today = now();
                    $range = is_string($dateType) ? $dateType : 'today';

                    if ($range === 'week') {
                        $from = $today->copy()->startOfWeek()->toDateString();
                        $to = $today->copy()->endOfWeek()->toDateString();
                        $dateType = 'week';
                    } elseif ($range === 'month') {
                        $from = $today->copy()->startOfMonth()->toDateString();
                        $to = $today->copy()->endOfMonth()->toDateString();
                        $dateType = 'month';
                    } elseif ($range === 'year') {
                        $from = $today->copy()->startOfYear()->toDateString();
                        $to = $today->copy()->endOfYear()->toDateString();
                        $dateType = 'year';
                    } else {
                        $from = $today->toDateString();
                        $to = $today->toDateString();
                        $dateType = 'today';
                    }
                }

                $transactionsQuery = DB::table('appointment_technician as at')
                    ->join('appointments as a', 'a.id', '=', 'at.appointment_id')
                    ->join('payments as p', 'p.appointment_id', '=', 'a.id')
                    ->where('at.user_id', $user->id)
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
                    $transactionsQuery->whereDate('p.paid_at', '>=', $from);
                }
                if (is_string($to) && $to !== '') {
                    $transactionsQuery->whereDate('p.paid_at', '<=', $to);
                }

                $transactions = $transactionsQuery
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

                $technicianViewBootstrap = [
                    'technicianId' => $technicianId,
                    'technician' => $technician,
                    'transactions' => $transactions,
                    'dateRangeFrom' => $from,
                    'dateRangeTo' => $to,
                    'dateType' => $dateType,
                ];
            }
        }

        return view('salon.technicians.view', [
            'pageTitle' => 'Technician Details',
            'technicianViewBootstrap' => $technicianViewBootstrap,
        ]);
    }

    public function usersIndex(): View
    {
        $rows = User::query()
            ->salonStaff()
            ->orderBy('id')
            ->get([
                'id',
                'username',
                'first_name',
                'last_name',
                'email',
                'phone',
                'profile_photo',
                'role',
                'status',
                'initials',
                'created_at',
                'last_login_at',
            ]);

        $technicianIds = $rows->filter(fn (User $u) => $u->role === 'technician')->pluck('id')->all();

        $turnTrackers = TurnTracker::query()
            ->whereIn('user_id', $technicianIds)
            ->get()
            ->keyBy('user_id');

        $totalsByTechId = DB::table('appointment_technician as at')
            ->join('appointments as a', 'a.id', '=', 'at.appointment_id')
            ->join('payments as p', 'p.appointment_id', '=', 'a.id')
            ->whereIn('at.user_id', $technicianIds)
            ->whereNotNull('p.paid_at')
            ->selectRaw('at.user_id as user_id')
            ->selectRaw('COALESCE(SUM(at.total_service), 0) AS total_earnings')
            ->selectRaw('COALESCE(SUM(at.tip), 0) AS total_tips')
            ->selectRaw('COALESCE(SUM(at.commission), 0) AS total_commission')
            ->groupBy('at.user_id')
            ->get()
            ->keyBy('user_id');

        $users = $rows->map(function (User $u) use ($turnTrackers, $totalsByTechId) {
            $base = [
                'id' => $u->id,
                'username' => $u->username,
                'firstName' => $u->first_name,
                'lastName' => $u->last_name,
                'email' => $u->email,
                'phone' => $u->phone,
                'role' => $u->role,
                'status' => $u->status ?? 'active',
                'active' => strtolower($u->status ?? 'active') === 'active',
                'initials' => $u->initials,
                'profilePhoto' => $u->profile_photo,
                'profilePhotoUrl' => $u->profile_photo ? asset('storage/'.$u->profile_photo) : null,
                'createdAt' => $u->created_at?->format('Y-m-d'),
                'last_login_at' => $u->last_login_at?->toIso8601String(),
                'lastLogin' => $u->last_login_at?->format('M j, Y g:i A'),
            ];

            if ($u->role === 'technician') {
                $tracker = $turnTrackers->get($u->id);
                $totals = $totalsByTechId->get($u->id);
                $base['clock_in'] = $tracker?->clock_in?->format('M j, Y g:i A');
                $base['clock_out'] = $tracker?->clock_out?->format('M j, Y g:i A');
                $base['services'] = $tracker ? (int) $tracker->services : 0;
                $base['totalEarnings'] = $totals ? (float) $totals->total_earnings : 0.0;
                $base['totalTips'] = $totals ? (float) $totals->total_tips : 0.0;
                $base['totalCommission'] = $totals ? (float) $totals->total_commission : 0.0;
            }

            return $base;
        })->values()->all();

        return view('salon.users.index', [
            'pageTitle' => 'Users',
            'usersBootstrap' => [
                'users' => $users,
            ],
        ]);
    }

    public function usersView(): View
    {
        return view('salon.users.view', ['pageTitle' => 'User Details']);
    }

    public function paymentsIndex(): View
    {
        $rows = Payment::query()
            ->with(['appointment.customer', 'appointment.appointmentServices.service', 'appointment.appointmentServices.serviceCategory'])
            ->orderBy('paid_at')
            ->get();

        $payments = $rows->map(function (Payment $p) {
            $customer = $p->appointment?->customer;
            $name = $customer ? trim($customer->first_name.' '.$customer->last_name) : '';
            $initials = $customer
                ? strtoupper(mb_substr($customer->first_name, 0, 1).mb_substr($customer->last_name, 0, 1))
                : '';

            $services = $p->appointment?->appointmentServices?->map(function ($svc) {
                $serviceName = $svc->service?->name ?? $svc->serviceCategory?->name ?? 'Service';
                $qty = (int) ($svc->quantity ?? 1);
                $unitPrice = $svc->unit_price !== null ? (float) $svc->unit_price : 0.0;

                return [
                    'name' => $serviceName,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'line_total' => round($qty * $unitPrice, 2),
                ];
            })->values()->all() ?? [];

            $status = $p->status ?? 'Completed';

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
                'status' => $status,
                'refund_notes' => $p->refund_notes,
                'refunded_at' => $p->refunded_at?->format('Y-m-d\TH:i'),
                'statusColor' => in_array($status, ['Refunded', 'Voided'], true) ? 'bg-gray-100' : ($status === 'Pending' ? 'bg-amber-100' : 'bg-green-100'),
                'statusTextColor' => in_array($status, ['Refunded', 'Voided'], true) ? 'text-gray-700' : ($status === 'Pending' ? 'text-amber-700' : 'text-green-700'),
                'date' => $p->paid_at?->format('Y-m-d'),
                'services' => $services,
                'bookingId' => 'ORDER'.$p->appointment_id,
            ];
        })->values()->all();

        return view('salon.payments.index', [
            'pageTitle' => 'Payments',
            'paymentsBootstrap' => [
                'payments' => $payments,
            ],
        ]);
    }

    public function payoutIndex(Request $request): View
    {
        $currentRole = (string) $request->session()->get('salon_role', 'admin');
        $email = (string) $request->session()->get('salon_user_email', '');

        $loggedInUserId = $email !== ''
            ? User::query()->where('email', $email)->value('id')
            : null;

        $technicianId = null;
        if ($currentRole === 'technician') {
            $technicianId = $loggedInUserId ? (int) $loggedInUserId : null;
        } else {
            $techParam = $request->query('technician');
            $technicianId = is_string($techParam) && $techParam !== '' ? (int) $techParam : null;
        }

        $from = $request->query('from');
        $to = $request->query('to');
        $dateType = $request->query('datetype');

        if (! is_string($from) || $from === '' || ! is_string($to) || $to === '') {
            $today = now()->toDateString();
            $from = $today;
            $to = $today;
            $dateType = is_string($dateType) && $dateType !== '' ? $dateType : 'today';
        }

        if (! is_string($dateType) || $dateType === '') {
            $dateType = null;
        }

        $technicians = User::query()
            ->where('role', 'technician')
            ->orderBy('id')
            ->get(['id', 'first_name', 'last_name', 'role'])
            ->map(fn (User $u) => [
                'id' => $u->id,
                'firstName' => $u->first_name,
                'lastName' => $u->last_name,
                'role' => $u->role,
            ])
            ->values()
            ->all();

        $transactions = [];
        if ($technicianId) {
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

            if ($from !== '') {
                $query->whereDate('p.paid_at', '>=', $from);
            }
            if ($to !== '') {
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
        }

        return view('salon.payout.index', [
            'pageTitle' => 'Payout',
            'loggedInUserId' => $loggedInUserId ? (int) $loggedInUserId : null,
            'payoutBootstrap' => [
                'technicians' => $technicians,
                'selectedTechnicianId' => $technicianId,
                'from' => $from,
                'to' => $to,
                'datetype' => $dateType,
                'transactions' => $transactions,
            ],
        ]);
    }

    public function ordersIndex(): View
    {
        return view('salon.orders.index', ['pageTitle' => 'Orders']);
    }

    public function turnTrackerIndex(): View
    {
        $technicianIds = User::query()
            ->technicians()
            ->pluck('id')
            ->all();

        $entries = TurnTracker::query()
            ->with('user:id,first_name,last_name,initials,profile_photo')
            ->whereIn('user_id', $technicianIds)
            ->whereNull('clock_out')
            ->orderBy('id')
            ->get();

        $data = $entries->map(function (TurnTracker $t) {
            $u = $t->user;

            return [
                'id' => $t->id,
                'user_id' => $t->user_id,
                'services' => $t->services,
                'clock_in' => $t->clock_in?->toIso8601String(),
                'clock_in_display' => $t->clock_in?->format('M j, Y g:i A'),
                'firstName' => $u?->first_name,
                'lastName' => $u?->last_name,
                'fullName' => trim(($u?->first_name ?? '').' '.($u?->last_name ?? '')),
                'initials' => $u?->initials ?? strtoupper(substr($u?->first_name ?? '', 0, 1).substr($u?->last_name ?? '', 0, 1)),
                'photo' => $u?->profile_photo ? asset('storage/'.$u->profile_photo) : null,
            ];
        })->values()->all();

        $settings = Setting::getAllAsKeyValue();
        $turnTrackerOrder = $settings['turn_tracker_order'] ?? 'lowest';
        if (! in_array($turnTrackerOrder, ['lowest', 'highest'], true)) {
            $turnTrackerOrder = 'lowest';
        }

        return view('salon.turn-tracker.index', [
            'pageTitle' => 'Turn Tracker',
            'turnTrackerBootstrap' => [
                'success' => true,
                'entries' => $data,
                'turn_tracker_order' => $turnTrackerOrder,
            ],
        ]);
    }

    public function settingsIndex(): View
    {
        return view('salon.settings.index', [
            'pageTitle' => 'Settings',
            'settingsBootstrap' => Setting::getAllAsKeyValue(),
        ]);
    }

    /**
     * In-app documentation / user guide.
     */
    public function documentationIndex(Request $request): View
    {
        $currentRole = (string) $request->session()->get('salon_role', 'admin');
        if (! in_array($currentRole, ['admin', 'receptionist', 'technician'], true)) {
            $currentRole = 'admin';
        }

        return view('salon.documentation.index', [
            'pageTitle' => 'Documentation',
            'currentRole' => $currentRole,
            'documentationLinks' => [
                'dashboard' => route('salon.dashboard'),
                'tickets' => route('salon.booking.tickets'),
                'waitingList' => route('salon.booking.waiting-list'),
                'calendar' => route('salon.booking.calendar'),
                'customers' => route('salon.customers.index'),
                'services' => route('salon.services.index'),
                'technicians' => route('salon.technicians.index'),
                'turnTracker' => route('salon.turn-tracker.index'),
                'users' => route('salon.users.index'),
                'payments' => route('salon.payments.index'),
                'payout' => route('salon.payout.index'),
                'settings' => route('salon.settings.index'),
                'profile' => route('salon.profile.index'),
            ],
        ]);
    }

    public function profileIndex(Request $request): View
    {
        $email = $request->session()->get('salon_user_email');
        $profileUser = $email
            ? User::query()->where('email', $email)->first()
            : null;

        return view('salon.profile.index', [
            'pageTitle' => 'Profile',
            'profileUser' => $profileUser,
        ]);
    }
}

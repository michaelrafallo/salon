<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Payment;
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
        $request->session()->put('salon_user_email', $request->input('email', 'admin@salon.com'));

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

    public function editBooking(): View
    {
        return view('salon.booking.edit-booking', ['pageTitle' => 'Edit Booking']);
    }

    public function pay(): View
    {
        return view('salon.booking.pay', ['pageTitle' => 'Pay']);
    }

    public function tickets(): View
    {
        return view('salon.booking.tickets', ['pageTitle' => 'Tickets']);
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
            ->get(['id', 'first_name', 'last_name', 'role', 'status', 'initials']);

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
        return view('salon.customers.index', ['pageTitle' => 'Customers']);
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

        $bookings = Appointment::query()
            ->where('customer_id', $customer->id)
            ->with(['technicians', 'appointmentServices.serviceCategory'])
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
        ]);
    }

    public function servicesIndex(): View
    {
        return view('salon.services.index', ['pageTitle' => 'Services']);
    }

    public function techniciansIndex(): View
    {
        return view('salon.technicians.index', ['pageTitle' => 'Technicians']);
    }

    public function techniciansView(): View
    {
        return view('salon.technicians.view', ['pageTitle' => 'Technician Details']);
    }

    public function usersIndex(): View
    {
        return view('salon.users.index', ['pageTitle' => 'Users']);
    }

    public function usersView(): View
    {
        return view('salon.users.view', ['pageTitle' => 'User Details']);
    }

    public function paymentsIndex(): View
    {
        return view('salon.payments.index', ['pageTitle' => 'Payments']);
    }

    public function payoutIndex(Request $request): View
    {
        $email = (string) $request->session()->get('salon_user_email', '');
        $loggedInUserId = $email !== ''
            ? User::query()->where('email', $email)->value('id')
            : null;

        return view('salon.payout.index', [
            'pageTitle' => 'Payout',
            'loggedInUserId' => $loggedInUserId ? (int) $loggedInUserId : null,
        ]);
    }

    public function ordersIndex(): View
    {
        return view('salon.orders.index', ['pageTitle' => 'Orders']);
    }

    public function turnTrackerIndex(): View
    {
        return view('salon.turn-tracker.index', ['pageTitle' => 'Turn Tracker']);
    }

    public function settingsIndex(): View
    {
        return view('salon.settings.index', ['pageTitle' => 'Settings']);
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

<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $request->session()->forget(['salon_authenticated', 'salon_user_email', 'salon_role']);

        return redirect()->route('salon.login');
    }

    public function showForgotPassword(): View
    {
        return view('salon.auth.forgot-password');
    }

    public function dashboard(Request $request): View|RedirectResponse
    {
        $userlevel = $request->query('userlevel');
        if (in_array($userlevel, ['admin', 'receptionist', 'technician'], true)) {
            $request->session()->put('salon_role', $userlevel);

            return redirect()->route('salon.dashboard');
        }

        $currentRole = $request->session()->get('salon_role', 'admin');

        return view('salon.dashboard.index', [
            'pageTitle' => 'Dashboard',
            'current_role' => $currentRole,
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

    public function waitingList(): View
    {
        return view('salon.booking.waiting-list', ['pageTitle' => 'Waiting List']);
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

    public function payoutIndex(): View
    {
        return view('salon.payout.index', ['pageTitle' => 'Payout']);
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

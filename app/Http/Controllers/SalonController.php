<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function customersView(): View
    {
        return view('salon.customers.view', ['pageTitle' => 'Customer Details']);
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

    public function profileIndex(): View
    {
        return view('salon.profile.index', ['pageTitle' => 'Profile']);
    }
}

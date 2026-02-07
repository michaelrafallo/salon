@extends('layouts.salon')

@section('content')
@php
    $activeRole = request()->query('role', $currentRole ?? 'admin');
    if (! in_array($activeRole, ['admin', 'receptionist', 'technician'], true)) {
        $activeRole = $currentRole ?? 'admin';
    }

    $links = $documentationLinks ?? [];

    $roleLabel = match ($activeRole) {
        'receptionist' => 'Receptionist',
        'technician' => 'Technician',
        default => 'Admin',
    };
@endphp

<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-3">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Documentation</h1>
                <p class="text-gray-600 text-sm sm:text-base mt-1">
                    Role guide: <span class="font-semibold text-gray-900">{{ $roleLabel }}</span>
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ $links['dashboard'] ?? route('salon.dashboard') }}" class="px-4 py-2 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition text-sm font-medium text-gray-700">
                    Back to Dashboard
                </a>
                <a href="{{ $links['profile'] ?? route('salon.profile.index') }}" class="px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition text-sm font-medium">
                    Profile
                </a>
            </div>
        </div>

        <div class="mb-6 flex items-center gap-2 border-b border-gray-200 overflow-x-auto">
            <a href="{{ route('salon.documentation.index', ['role' => 'admin']) }}"
                class="px-4 py-3 text-sm font-medium transition whitespace-nowrap {{ $activeRole === 'admin' ? 'text-[#003047] border-b-2 border-[#003047]' : 'text-gray-500 border-b-2 border-transparent hover:text-gray-700' }}">
                Admin
            </a>
            <a href="{{ route('salon.documentation.index', ['role' => 'receptionist']) }}"
                class="px-4 py-3 text-sm font-medium transition whitespace-nowrap {{ $activeRole === 'receptionist' ? 'text-[#003047] border-b-2 border-[#003047]' : 'text-gray-500 border-b-2 border-transparent hover:text-gray-700' }}">
                Receptionist
            </a>
            <a href="{{ route('salon.documentation.index', ['role' => 'technician']) }}"
                class="px-4 py-3 text-sm font-medium transition whitespace-nowrap {{ $activeRole === 'technician' ? 'text-[#003047] border-b-2 border-[#003047]' : 'text-gray-500 border-b-2 border-transparent hover:text-gray-700' }}">
                Technician
            </a>
        </div>

        {{-- ADMIN --}}
        @if($activeRole === 'admin')
            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Admin daily flow</h2>
                    <ol class="text-sm text-gray-700 space-y-2 list-decimal list-inside">
                        <li>Open <a class="text-[#003047] font-medium hover:underline" href="{{ $links['dashboard'] ?? '#' }}">Dashboard</a> to check totals and the latest tickets.</li>
                        <li>Manage intake from <a class="text-[#003047] font-medium hover:underline" href="{{ $links['waitingList'] ?? '#' }}">Waiting List</a> and <a class="text-[#003047] font-medium hover:underline" href="{{ $links['tickets'] ?? '#' }}">Tickets</a>.</li>
                        <li>Schedule and reschedule appointments from <a class="text-[#003047] font-medium hover:underline" href="{{ $links['calendar'] ?? '#' }}">Calendar</a>.</li>
                        <li>Take payments from the ticket payment flow (Tickets → Pay).</li>
                        <li>Review <a class="text-[#003047] font-medium hover:underline" href="{{ $links['payments'] ?? '#' }}">Payments</a> and run <a class="text-[#003047] font-medium hover:underline" href="{{ $links['payout'] ?? '#' }}">Payout</a>.</li>
                        <li>Maintain <a class="text-[#003047] font-medium hover:underline" href="{{ $links['services'] ?? '#' }}">Services</a>, <a class="text-[#003047] font-medium hover:underline" href="{{ $links['technicians'] ?? '#' }}">Technicians</a>, and <a class="text-[#003047] font-medium hover:underline" href="{{ $links['customers'] ?? '#' }}">Customers</a> data.</li>
                        <li>Update configuration in <a class="text-[#003047] font-medium hover:underline" href="{{ $links['settings'] ?? '#' }}">Settings</a>.</li>
                    </ol>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Dashboard</h2>
                    <div class="text-sm text-gray-700 space-y-3">
                        <p>Dashboard is your daily control center. It shows salon totals and quick actions.</p>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Check the stats cards (customers total, active technicians, tickets by status).</li>
                            <li>Review the “Tickets” widget for the latest items and open tickets when needed.</li>
                            <li>Use Quick Actions to jump to Tickets, Calendar, Services, Customers, Payments, Payout, and Settings.</li>
                        </ol>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Waiting List</h2>
                    <div class="text-sm text-gray-700 space-y-3">
                        <p>Use Waiting List to manage customers currently waiting (Walk-In or Booked).</p>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Use the top tabs (All / Walk-In / Booked) to filter the queue.</li>
                            <li>Use grid/list view toggle to switch layout (your browser remembers the view).</li>
                            <li>Use search to find a customer quickly.</li>
                            <li>Assign technician(s) and verify the service(s) for the ticket.</li>
                            <li>When ready, open the ticket workflow (move to Tickets / payment flow as your process requires).</li>
                        </ol>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Tickets</h2>
                    <div class="text-sm text-gray-700 space-y-3">
                        <p>Tickets are the active service/payment records. Use the status tabs to manage workflow.</p>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Use the status tabs (Unpaid / Paid / Cancelled / Refunded) to filter.</li>
                            <li>Use search to find the customer’s ticket.</li>
                            <li>Open the ticket and confirm: services, quantities, assigned technicians.</li>
                            <li>Click Pay / Checkout to complete payment when the ticket is Unpaid.</li>
                            <li>After payment, confirm the ticket is marked Paid and appears in the Paid tab.</li>
                        </ol>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Calendar</h2>
                    <div class="text-sm text-gray-700 space-y-3">
                        <p>Calendar is used for scheduling and moving appointments by time/technician.</p>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Use grid view for a standard calendar layout; use list view for technician time-slot grid.</li>
                            <li>Create a new booking using the New Booking button when needed.</li>
                            <li>Drag and drop appointments (when enabled) to change time or technician; the app saves updates.</li>
                            <li>Appointments without technicians appear in “Not Assigned”.</li>
                        </ol>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Services</h2>
                    <div class="text-sm text-gray-700 space-y-3">
                        <p>Services are your catalog used in Tickets and Payments.</p>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Add or edit services to keep names/prices accurate.</li>
                            <li>Confirm the service categories and ensure active services are available for selection.</li>
                            <li>When a service changes, verify new tickets reflect the change.</li>
                        </ol>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Customers Data</h2>
                    <div class="text-sm text-gray-700 space-y-3">
                        <p>Use Customers Data to search customers, view visit history, and open customer profiles.</p>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Search by customer name, email, or phone.</li>
                            <li>Review Total Visits and Last Visit columns to understand engagement.</li>
                            <li>Open a customer profile to review ticket history and pay any unpaid ticket.</li>
                        </ol>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Technicians</h2>
                    <div class="text-sm text-gray-700 space-y-3">
                        <p>Technicians page helps you manage technicians and review performance details.</p>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Review technician status and availability as needed.</li>
                            <li>Open a technician to view commissions/transactions for date ranges.</li>
                            <li>Use this data when verifying payouts and performance.</li>
                        </ol>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Turn Tracker</h2>
                    <div class="text-sm text-gray-700 space-y-3">
                        <p>Turn Tracker tracks technician clock-in/out and service load for fair assignment.</p>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Check which technicians are clocked in and their service counts.</li>
                            <li>Use the ordering rule (lowest/highest) to guide assignments.</li>
                            <li>Use this view during peak times to distribute work fairly.</li>
                        </ol>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Users</h2>
                    <div class="text-sm text-gray-700 space-y-3">
                        <p>Users is for managing staff accounts (admin/receptionist/technician roles).</p>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Create users for new staff, set role, and set status (active/inactive).</li>
                            <li>Edit user details when staff info changes.</li>
                            <li>Use “Login as” for support/testing; stop impersonation from the banner at the top.</li>
                        </ol>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Payments</h2>
                    <div class="text-sm text-gray-700 space-y-3">
                        <p>Payments shows paid transactions and service line details.</p>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Use Payments to confirm amounts, methods, and payment status.</li>
                            <li>Open payment records to verify services and totals if a dispute occurs.</li>
                            <li>Use this page when auditing daily totals and refunds.</li>
                        </ol>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Payout</h2>
                    <div class="text-sm text-gray-700 space-y-3">
                        <p>Payout summarizes technician commissions/tips and payouts.</p>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Review payouts by technician and date range.</li>
                            <li>Cross-check against tickets and payments if totals look incorrect.</li>
                            <li>Use this page before sending payouts to technicians.</li>
                        </ol>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Settings</h2>
                    <div class="text-sm text-gray-700 space-y-3">
                        <p>Settings controls tax/currency, discounts/coupons, and gift card configuration.</p>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Set tax and currency display for receipts and totals.</li>
                            <li>Configure discounts and coupons used during checkout.</li>
                            <li>Configure gift card behavior (if enabled) for payment flow.</li>
                        </ol>
                    </div>
                </div>
            </div>
        @endif

        {{-- RECEPTIONIST --}}
        @if($activeRole === 'receptionist')
            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Receptionist daily flow</h2>
                    <ol class="text-sm text-gray-700 space-y-2 list-decimal list-inside">
                        <li>Start on <a class="text-[#003047] font-medium hover:underline" href="{{ $links['dashboard'] ?? '#' }}">Dashboard</a> to see the latest tickets.</li>
                        <li>Add customers to <a class="text-[#003047] font-medium hover:underline" href="{{ $links['waitingList'] ?? '#' }}">Waiting List</a> (Walk-In or Booked).</li>
                        <li>Move customers into <a class="text-[#003047] font-medium hover:underline" href="{{ $links['tickets'] ?? '#' }}">Tickets</a> and collect payment.</li>
                        <li>Use <a class="text-[#003047] font-medium hover:underline" href="{{ $links['calendar'] ?? '#' }}">Calendar</a> for scheduling and adjustments.</li>
                        <li>Use <a class="text-[#003047] font-medium hover:underline" href="{{ $links['customers'] ?? '#' }}">Customers Data</a> to find customer history and unpaid tickets.</li>
                        <li>Use <a class="text-[#003047] font-medium hover:underline" href="{{ $links['payments'] ?? '#' }}">Payments</a> to confirm payment history.</li>
                    </ol>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Dashboard</h2>
                    <div class="text-sm text-gray-700 space-y-3">
                        <p>Use Dashboard to monitor today’s workload and jump to common actions.</p>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Check the Tickets widget for the latest items.</li>
                            <li>Use Quick Actions to open Waiting List, Tickets, Calendar, Customers, Payments, and Payout.</li>
                        </ol>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Waiting List</h2>
                    <div class="text-sm text-gray-700 space-y-3">
                        <p>Waiting List is where you add and manage customers in the queue.</p>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Filter by Walk-In/Booked to match the customer type.</li>
                            <li>Assign technician(s) and confirm services.</li>
                            <li>Move the customer into the ticket/payment workflow when ready.</li>
                        </ol>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Tickets</h2>
                    <div class="text-sm text-gray-700 space-y-3">
                        <p>Use Tickets to complete service details and collect payment.</p>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Find the customer ticket (search + status tabs).</li>
                            <li>Open ticket and verify services + technicians.</li>
                            <li>Proceed to Pay/Checkout and complete payment.</li>
                            <li>Confirm it appears in the Paid tab after completion.</li>
                        </ol>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-3">Handling tickets (intake → payment)</h2>
                    <ol class="text-sm text-gray-700 space-y-2 list-decimal list-inside">
                        <li>Create or select the customer (Waiting List → Add New if needed).</li>
                        <li>Confirm services and assign technician(s).</li>
                        <li>Open the ticket in Tickets and go to Pay/Checkout to complete payment.</li>
                        <li>After payment, confirm the ticket status updates to Paid.</li>
                    </ol>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Calendar</h2>
                    <div class="text-sm text-gray-700 space-y-3">
                        <p>Use Calendar to book, move, and confirm appointment schedules.</p>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Switch between grid and list view depending on your workflow.</li>
                            <li>Create a booking when needed and assign technician(s).</li>
                            <li>Move appointments (drag/drop) to adjust schedule (when enabled).</li>
                        </ol>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-3">Calendar basics</h2>
                    <ul class="list-disc list-inside space-y-1 text-sm text-gray-700">
                        <li>Use Calendar to see the day schedule.</li>
                        <li>Use list view if you need to work by technician columns.</li>
                        <li>If you move an appointment in the grid, the app will save the new time/technician (when supported).</li>
                    </ul>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Services</h2>
                    <div class="text-sm text-gray-700 space-y-3">
                        <p>Services are typically maintained by admin, but you may reference them during ticket setup.</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Use Services to confirm service names/prices when a customer asks.</li>
                            <li>If you need a service changed, ask admin.</li>
                        </ul>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Customers Data</h2>
                    <div class="text-sm text-gray-700 space-y-3">
                        <p>Customers Data helps you find customers, view history, and open their tickets.</p>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Search by name, phone, or email.</li>
                            <li>Open a customer profile to view ticket history.</li>
                            <li>Use Pay on unpaid tickets if the customer is ready to checkout.</li>
                        </ol>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Technicians</h2>
                    <div class="text-sm text-gray-700 space-y-3">
                        <p>Use Technicians to view technician availability/performance if needed for scheduling.</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Check who is available/busy to assign correctly.</li>
                            <li>Open technician details when you need to confirm performance/commissions (if allowed).</li>
                        </ul>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Turn Tracker</h2>
                    <div class="text-sm text-gray-700 space-y-3">
                        <p>Turn Tracker helps you assign technicians fairly based on clock-in order and service count.</p>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Identify who is clocked in and their current service load.</li>
                            <li>Assign new tickets following the ordering rule shown in the page.</li>
                        </ol>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Users</h2>
                    <div class="text-sm text-gray-700 space-y-3">
                        <p><span class="font-medium text-gray-900">Admin only.</span> Receptionists typically do not have access to Users.</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>If a staff account needs changes, ask admin to update the user profile.</li>
                        </ul>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Payments</h2>
                    <div class="text-sm text-gray-700 space-y-3">
                        <p>Payments is used to verify completed transactions.</p>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Search/scan recent payments to confirm a customer checkout.</li>
                            <li>Use payment details to verify services and totals if asked.</li>
                        </ol>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Payout</h2>
                    <div class="text-sm text-gray-700 space-y-3">
                        <p>Payout summarizes technician earnings and payout history.</p>
                        <ul class="list-disc list-inside space-y-1">
                            <li>Use this page to answer technician questions about totals.</li>
                            <li>If payout requires adjustments, notify admin.</li>
                        </ul>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Settings</h2>
                    <div class="text-sm text-gray-700 space-y-3">
                        <p><span class="font-medium text-gray-900">Admin only.</span> If tax/discount/gift-card settings need updates, notify admin.</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- TECHNICIAN --}}
        @if($activeRole === 'technician')
            <div class="space-y-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Technician daily flow</h2>
                    <ol class="text-sm text-gray-700 space-y-2 list-decimal list-inside">
                        <li>Go to <a class="text-[#003047] font-medium hover:underline" href="{{ $links['dashboard'] ?? '#' }}">Dashboard</a> and clock in.</li>
                        <li>Check the “Tickets” widget (assigned waiting list items).</li>
                        <li>Use <a class="text-[#003047] font-medium hover:underline" href="{{ $links['calendar'] ?? '#' }}">Calendar</a> to view your schedule.</li>
                        <li>Review your payouts in <a class="text-[#003047] font-medium hover:underline" href="{{ $links['payout'] ?? '#' }}">Payout</a>.</li>
                    </ol>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-3">Dashboard</h2>
                    <ul class="list-disc list-inside space-y-1 text-sm text-gray-700">
                        <li>Use Clock In / Clock Out to track your shift.</li>
                        <li>Your dashboard shows stats for today (appointments, completed, in progress).</li>
                        <li>“Tickets” widget shows assigned waiting items (not clickable by design).</li>
                    </ul>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-3">Calendar</h2>
                    <ul class="list-disc list-inside space-y-1 text-sm text-gray-700">
                        <li>Use Calendar to see your appointments by time.</li>
                        <li>List view can show a technician grid; use it to verify where you’re assigned.</li>
                    </ul>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-3">Payout</h2>
                    <ul class="list-disc list-inside space-y-1 text-sm text-gray-700">
                        <li>Payout shows your commissions/tips totals (your role sees only your own records).</li>
                        <li>If something looks incorrect, notify admin to review payment and appointment assignments.</li>
                    </ul>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">How to use: Dashboard</h2>
                    <div class="text-sm text-gray-700 space-y-3">
                        <p>Dashboard shows your day performance and lets you clock in/out.</p>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Clock in at the start of your shift and clock out when you finish.</li>
                            <li>Review Today’s Appointments / Completed / In Progress to track your pace.</li>
                            <li>Use the Tickets widget as a reminder of waiting items assigned to you (receptionist/admin manages details).</li>
                        </ol>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">How to use: Calendar</h2>
                    <div class="text-sm text-gray-700 space-y-3">
                        <p>Calendar shows your schedule and assigned appointments.</p>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Open Calendar and check the current day.</li>
                            <li>Use list view if you prefer the technician grid layout.</li>
                            <li>If you notice a wrong time/assignment, tell receptionist/admin to update the ticket.</li>
                        </ol>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">How to use: Payout</h2>
                    <div class="text-sm text-gray-700 space-y-3">
                        <p>Payout shows your earnings history.</p>
                        <ol class="list-decimal list-inside space-y-1">
                            <li>Open Payout and filter/review your totals for the date range shown.</li>
                            <li>If totals look wrong, confirm the ticket services and tips were entered correctly, then notify admin.</li>
                        </ol>
                    </div>
                </div>
            </div>
        @endif
    </div>
</main>
@endsection


@extends('layouts.salon')

@section('content')
@php
    $customersIndexUrl = route('salon.customers.index');
    $apiCustomersUrl = url('api/salon/customers');
    $initials = strtoupper(substr($customer->first_name ?? '', 0, 1) . substr($customer->last_name ?? '', 0, 1));
    $avatarColorBg = 'bg-[#e6f0f3]';
    $avatarColorText = 'text-[#003047]';
    $memberFor = '';
    if ($customerSince) {
        $since = \Carbon\Carbon::parse($customerSince);
        $months = now()->diffInMonths($since);
        $memberFor = $months === 0 ? 'New member' : ($months === 1 ? 'Member for 1 month' : "Member for {$months} months");
    }
@endphp

<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="mb-6">
            <a href="{{ $customersIndexUrl }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                <span class="text-sm font-medium">Back to Customers</span>
            </a>
        </div>

        <!-- Customer Header (PHP-rendered) -->
        <div id="customerHeader" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-start gap-6 flex-1">
                    <div id="customerAvatar" class="w-24 h-24 {{ $avatarColorBg }} rounded-full flex items-center justify-center flex-shrink-0 overflow-hidden">
                        @if($profilePhotoUrl ?? null)
                            <img src="{{ e($profilePhotoUrl) }}" alt="" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <span id="customerInitials" class="text-4xl font-bold {{ $avatarColorText }}" style="display:none">{{ $initials }}</span>
                        @else
                            <span id="customerInitials" class="text-4xl font-bold {{ $avatarColorText }}">{{ $initials }}</span>
                        @endif
                    </div>
                    <div class="flex-1">
                        <h1 id="customerName" class="text-3xl font-bold text-gray-900 mb-2">{{ $customer->first_name }} {{ $customer->last_name }}</h1>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Email</p>
                                <p class="text-base font-medium text-gray-900" id="customerEmail">{{ $customer->email ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Phone</p>
                                <p class="text-base font-medium text-gray-900" id="customerPhone">{{ $customer->phone ?? '—' }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-sm text-gray-500 mb-1">Address</p>
                                <p class="text-base font-medium text-gray-900" id="customerAddress">{{ $customer->address ?? '—' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="button" onclick="openEditCustomerModal()" class="px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95 text-sm">Edit Customer</button>
                    <button type="button" onclick="openAdjustCreditsModal()" class="px-4 py-2 bg-[#e6f0f3] text-[#003047] rounded-lg hover:bg-[#b3d1d9] transition font-medium active:scale-95 text-sm">Adjust Credits</button>
                    <button type="button" onclick="deleteCustomerFromView()" class="px-4 py-2 bg-red-50 text-red-600 border border-red-200 rounded-lg hover:bg-red-100 transition font-medium active:scale-95 text-sm">Delete Customer</button>
                </div>
            </div>
        </div>

        <!-- Statistics (PHP-rendered) -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <p class="text-sm text-gray-500 mb-2">Total Visits</p>
                <p id="totalVisits" class="text-3xl font-bold text-gray-900">{{ $totalVisits }}</p>
                <p id="lastVisitText" class="text-xs text-gray-500 mt-2">{{ $lastVisitDate ? 'Last visit: ' . \Carbon\Carbon::parse($lastVisitDate)->format('M j, Y') : 'No visits yet' }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <p class="text-sm text-gray-500 mb-2">Total Spent</p>
                <p id="totalSpent" class="text-3xl font-bold text-gray-900">{{ $currencySymbol ?? '$' }}{{ number_format($totalSpent, 2) }}</p>
                <p id="averagePerVisit" class="text-xs text-gray-500 mt-2">Average per visit: {{ $currencySymbol ?? '$' }}{{ $totalVisits > 0 ? number_format($totalSpent / $totalVisits, 2) : '0.00' }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <p class="text-sm text-gray-500 mb-2">Customer Since</p>
                <p id="customerSince" class="text-3xl font-bold text-gray-900">{{ $customerSince ? \Carbon\Carbon::parse($customerSince)->format('M Y') : '—' }}</p>
                <p id="memberFor" class="text-xs text-gray-500 mt-2">{{ $memberFor }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <p class="text-sm text-gray-500 mb-2">Credit Balance</p>
                <p id="creditBalanceDisplay" class="text-3xl font-bold text-gray-900">{{ $currencySymbol ?? '$' }}{{ number_format((float) ($customer->credit_balance ?? 0), 2) }}</p>
                <p class="text-xs text-gray-500 mt-2">Available to redeem at checkout</p>
            </div>
        </div>

        <!-- Tabs: Tickets / Credit History (PHP-rendered) -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
            <div class="border-b border-gray-200 px-6">
                <nav class="-mb-px flex gap-6" aria-label="Customer details tabs">
                    <button id="customerTabTickets" type="button" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" aria-controls="customerTabPanelTickets">
                        Tickets
                    </button>
                    <button id="customerTabCredits" type="button" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm" aria-controls="customerTabPanelCredits">
                        Credit History
                    </button>
                </nav>
            </div>

            <div id="customerTabPanelTickets" class="p-6">
                <h2 class="sr-only">Tickets</h2>
                <div id="ticketsContainer" class="space-y-4">
                    @forelse($bookings as $booking)
                        @php
                            $bookingDateTime = $booking->appointment_datetime ?? $booking->created_at;
                            $dateDisplay = $bookingDateTime ? \Carbon\Carbon::parse($bookingDateTime)->format('M j, Y') : '—';

                            $statusRaw = strtolower(trim((string) ($booking->status ?? '')));
                            $statusKey = match ($statusRaw) {
                                'cancelled', 'canceled' => 'cancelled',
                                default => $statusRaw !== '' ? $statusRaw : 'unpaid',
                            };

                            $statusDisplay = match ($statusKey) {
                                'unpaid' => 'Unpaid',
                                'paid' => 'Paid',
                                'waiting' => 'Waiting',
                                'in-progress' => 'In Progress',
                                'completed', 'closed' => 'Completed',
                                'refunded' => 'Refunded',
                                'cancelled' => 'Cancelled',
                                default => ucfirst($statusKey),
                            };

                            $statusColor = match ($statusKey) {
                                'unpaid' => 'bg-red-100 text-red-700',
                                'waiting' => 'bg-yellow-100 text-yellow-700',
                                'paid' => 'bg-blue-100 text-blue-700',
                                'completed', 'closed' => 'bg-green-100 text-green-700',
                                'refunded' => 'bg-purple-100 text-purple-700',
                                'cancelled' => 'bg-gray-100 text-gray-700',
                                default => 'bg-gray-100 text-gray-700',
                            };

                            $techNames = $booking->technicians->map(fn ($u) => trim(($u->first_name ?? '').' '.($u->last_name ?? '')))->filter()->implode(', ');

                            $services = $booking->appointmentServices ?? collect();
                            $serviceCount = method_exists($services, 'count') ? $services->count() : 0;
                            $firstServiceName = $serviceCount > 0
                                ? (($services[0]->service?->name ?? $services[0]->serviceCategory?->name ?? $services[0]->serviceCategory?->slug) ?: 'Service')
                                : null;
                            $serviceSummary = $serviceCount === 0
                                ? 'No services'
                                : ($serviceCount === 1 ? $firstServiceName : ($firstServiceName.' + '.($serviceCount - 1).' more'));

                            $totalAmount = $booking->payment?->amount !== null
                                ? (float) $booking->payment->amount
                                : (float) $services->sum(function ($svc) {
                                    $qty = (int) ($svc->quantity ?? 1);
                                    $unitPrice = $svc->unit_price !== null
                                        ? (float) $svc->unit_price
                                        : (float) ($svc->service?->price ?? 0);

                                    return $qty * $unitPrice;
                                });
                        @endphp
                        <div class="p-5 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                            <div class="flex justify-between items-start mb-3">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-2">
                                        <p class="font-semibold text-gray-900 text-lg">Ticket #{{ $booking->id }}</p>
                                        <span class="px-2 py-1 {{ $statusColor }} text-xs font-medium rounded">{{ $statusDisplay }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-3">{{ $serviceSummary }}</p>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-2xl font-bold text-gray-900">{{ $currencySymbol ?? '$' }}{{ number_format($totalAmount, 2) }}</span>
                                    @if($statusKey === 'unpaid')
                                        <a href="{{ route('salon.booking.pay', ['id' => $booking->id]) }}" class="px-3 py-1.5 bg-[#003047] text-white text-xs font-medium rounded hover:bg-[#002535] transition active:scale-95 flex items-center gap-1">
                                            Pay
                                        </a>
                                    @endif
                                    <button type="button" onclick="printTicket({{ $booking->id }})" class="px-3 py-1.5 bg-[#003047] text-white text-xs font-medium rounded hover:bg-[#002535] transition active:scale-95 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                        Print
                                    </button>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-3 border-t border-gray-200">
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Date</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $dateDisplay }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 mb-1">Technicians</p>
                                    <p class="text-sm font-medium text-gray-900">{{ $techNames ?: 'Not Assigned' }}</p>
                                </div>
                                <div class="md:col-span-2">
                                    <p class="text-xs text-gray-500 mb-2">Services</p>
                                    <div class="space-y-1">
                                        @forelse($booking->appointmentServices as $svc)
                                            @php
                                                $qty = (int)($svc->quantity ?? 1);
                                                $unitPrice = $svc->unit_price !== null
                                                    ? (float) $svc->unit_price
                                                    : (float) ($svc->service?->price ?? 0);
                                                $lineTotal = $qty * $unitPrice;
                                                $serviceName = $svc->service?->name ?? $svc->serviceCategory?->name ?? $svc->serviceCategory?->slug ?? 'Service';
                                            @endphp
                                            <div class="flex justify-between items-center">
                                                <span class="text-sm text-gray-900">{{ $serviceName }}{{ $qty > 1 ? ' × ' . $qty : '' }}</span>
                                                <span class="text-sm font-medium text-gray-900">{{ $currencySymbol ?? '$' }}{{ number_format($lineTotal, 2) }}</span>
                                            </div>
                                        @empty
                                            <p class="text-sm text-gray-500">No services listed</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-8">No tickets found for this customer.</p>
                    @endforelse
                </div>
            </div>

            <div id="customerTabPanelCredits" class="p-6 hidden">
                <h2 class="sr-only">Credit History</h2>
                <div class="w-full overflow-x-auto">
                    <table class="w-full min-w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Operation</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Processed By</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Remaining</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">New Balance</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($creditLedgers as $ledger)
                                @php
                                    $amountPrefix = match ($ledger->operation) {
                                        'add' => '+',
                                        'subtract', 'redeem' => '-',
                                        default => '',
                                    };

                                    $processedBy = '—';
                                    if ($ledger->user) {
                                        $processedBy = trim(($ledger->user->first_name ?? '').' '.($ledger->user->last_name ?? ''));
                                        if ($processedBy === '') {
                                            $processedBy = $ledger->user->name ?? $ledger->user->email ?? '—';
                                        }
                                    }
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ $ledger->created_at?->format('M j, Y g:i A') ?? '—' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ ucfirst($ledger->operation) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                        {{ $processedBy }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-right">
                                        {{ $amountPrefix }}{{ $currencySymbol ?? '$' }}{{ number_format((float) $ledger->amount, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 text-right">
                                        {{ $currencySymbol ?? '$' }}{{ number_format((float) $ledger->remaining_balance, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 text-right">
                                        {{ $currencySymbol ?? '$' }}{{ number_format((float) $ledger->new_balance, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-6 text-center text-sm text-gray-500">
                                        No credit history yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

@push('scripts')
@php
    $customerDataForJs = [
        'id' => $customer->id,
        'firstName' => $customer->first_name,
        'lastName' => $customer->last_name,
        'email' => $customer->email,
        'phone' => $customer->phone,
        'address' => $customer->address ?? null,
        'profilePhoto' => $profilePhotoUrl ?? null,
        'creditBalance' => (float) ($customer->credit_balance ?? 0),
        'status' => 'active',
    ];
@endphp
<script>
(function() {
var apiCustomersUrl = '{{ $apiCustomersUrl }}';
var customersIndexUrl = '{{ $customersIndexUrl }}';

// Customer data from server (for edit/delete modals)
var customerData = @json($customerDataForJs);

function renderCustomerInfo() {
    if (!customerData) return;
    var nameEl = document.getElementById('customerName');
    var emailEl = document.getElementById('customerEmail');
    var phoneEl = document.getElementById('customerPhone');
    var addressEl = document.getElementById('customerAddress');
    if (nameEl) nameEl.textContent = (customerData.firstName || '') + ' ' + (customerData.lastName || '');
    if (emailEl) emailEl.textContent = customerData.email || '—';
    if (phoneEl) phoneEl.textContent = customerData.phone || '—';
    if (addressEl) addressEl.textContent = customerData.address || '—';
    var avatarEl = document.getElementById('customerAvatar');
    if (avatarEl && customerData.profilePhoto) {
        avatarEl.innerHTML = '<img src="' + (customerData.profilePhoto || '').replace(/"/g, '&quot;') + '" alt="" class="w-full h-full object-cover" onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'flex\';"><span id="customerInitials" class="text-4xl font-bold text-[#003047]" style="display:none">' + (customerData.firstName || '').substring(0, 1).toUpperCase() + (customerData.lastName || '').substring(0, 1).toUpperCase() + '</span>';
    }
}

function renderCustomerCredits() {
    if (!customerData) return;
    var creditEl = document.getElementById('creditBalanceDisplay');
    if (creditEl) {
        var balance = parseFloat(customerData.creditBalance) || 0;
        creditEl.textContent = window.salonFormatMoney(balance);
    }
}

function setCustomerViewTab(tab) {
    var btnTickets = document.getElementById('customerTabTickets');
    var btnCredits = document.getElementById('customerTabCredits');
    var panelTickets = document.getElementById('customerTabPanelTickets');
    var panelCredits = document.getElementById('customerTabPanelCredits');
    if (!btnTickets || !btnCredits || !panelTickets || !panelCredits) return;

    var activeClasses = 'border-[#003047] text-[#003047]';
    var inactiveClasses = 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300';

    var showTickets = tab !== 'credits';
    panelTickets.classList.toggle('hidden', !showTickets);
    panelCredits.classList.toggle('hidden', showTickets);

    btnTickets.setAttribute('aria-selected', showTickets ? 'true' : 'false');
    btnCredits.setAttribute('aria-selected', showTickets ? 'false' : 'true');

    btnTickets.className = btnTickets.className.replace(activeClasses, '').replace(inactiveClasses, '').trim() + ' ' + (showTickets ? activeClasses : inactiveClasses);
    btnCredits.className = btnCredits.className.replace(activeClasses, '').replace(inactiveClasses, '').trim() + ' ' + (showTickets ? inactiveClasses : activeClasses);

    try { localStorage.setItem('customerViewTab', showTickets ? 'tickets' : 'credits'); } catch (e) {}
}

function initCustomerViewTabs() {
    var btnTickets = document.getElementById('customerTabTickets');
    var btnCredits = document.getElementById('customerTabCredits');
    if (!btnTickets || !btnCredits) return;

    btnTickets.addEventListener('click', function() { setCustomerViewTab('tickets'); });
    btnCredits.addEventListener('click', function() { setCustomerViewTab('credits'); });

    var initial = 'tickets';
    if (window.location && window.location.hash === '#credits') initial = 'credits';
    try {
        var saved = localStorage.getItem('customerViewTab');
        if (saved === 'credits' || saved === 'tickets') initial = saved;
    } catch (e) {}
    setCustomerViewTab(initial);
}

function openEditCustomerModal() {
    if (!customerData) return;
    var esc = function(s) {
        s = (s == null ? '' : String(s));
        return s.replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    };
    var firstName = esc(customerData.firstName);
    var lastName = esc(customerData.lastName);
    var email = esc(customerData.email);
    var phone = esc(customerData.phone);
    var modalContent = '<div class="p-6"><div class="flex items-center justify-between mb-4"><h3 class="text-xl font-bold text-gray-900">Edit Customer</h3><button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><form onsubmit="return window.updateCustomer(event)" class="space-y-4"><div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">First Name</label><input type="text" name="first_name" value="' + firstName + '" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label><input type="text" name="last_name" value="' + lastName + '" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div></div><div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">Email</label><input type="email" name="email" value="' + email + '" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Phone</label><input type="tel" name="phone" value="' + phone + '" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div></div><div class="flex justify-end gap-3 pt-4"><button type="button" onclick="closeModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium">Cancel</button><button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium">Update Customer</button></div></form></div>';
    if (typeof openModal === 'function') {
        openModal(modalContent);
    } else {
        alert('Modal is not available. Please refresh the page.');
    }
}

function openAdjustCreditsModal() {
    if (!customerData) return;
    var balance = parseFloat(customerData.creditBalance) || 0;
    var modalContent = '<div class="p-6"><div class="flex items-center justify-between mb-4"><h3 class="text-xl font-bold text-gray-900">Adjust Credits</h3><button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><form onsubmit="return window.updateCustomerCredits(event)" class="space-y-4"><div class="p-4 bg-gray-50 rounded-lg"><p class="text-sm text-gray-500 mb-1">Current Balance</p><p class="text-2xl font-bold text-gray-900">' + window.salonFormatMoney(balance) + '</p></div><div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">Operation</label><select name="operation" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent bg-white"><option value="add">Add Credits</option><option value="subtract">Subtract Credits</option><option value="set">Set Balance</option></select></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Amount</label><input type="number" name="amount" step="0.01" min="0" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="0.00"></div></div><div class="flex justify-end gap-3 pt-4"><button type="button" onclick="closeModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium">Cancel</button><button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium">Save Credits</button></div></form></div>';
    if (typeof openModal === 'function') {
        openModal(modalContent);
    } else {
        alert('Modal is not available. Please refresh the page.');
    }
}

function updateCustomer(e) {
    if (e && e.preventDefault) e.preventDefault();
    var form = e.target;
    var data = { first_name: form.first_name.value.trim(), last_name: form.last_name.value.trim(), email: form.email.value.trim() || null, phone: form.phone.value.trim() || null };
    var btn = form.querySelector('button[type="submit"]');
    if (btn) { btn.disabled = true; btn.textContent = 'Updating...'; }
    if (typeof salonApi === 'undefined' || !salonApi.put) {
        if (typeof showErrorMessage === 'function') showErrorMessage('Unable to save. Please refresh and try again.');
        if (btn) { btn.disabled = false; btn.textContent = 'Update Customer'; }
        return false;
    }
    salonApi.put(apiCustomersUrl + '/' + customerData.id, data).then(function(res) {
        customerData.firstName = res.data.firstName;
        customerData.lastName = res.data.lastName;
        customerData.email = res.data.email;
        customerData.phone = res.data.phone;
        if (res.data.profilePhoto !== undefined) customerData.profilePhoto = res.data.profilePhoto;
        renderCustomerInfo();
        if (typeof showSuccessMessage === 'function') showSuccessMessage(res.message || 'Customer updated.');
        if (typeof closeModal === 'function') closeModal();
    }).catch(function(err) {
        if (typeof showErrorMessage === 'function') showErrorMessage(err.message || 'Failed to update customer.');
        if (btn) { btn.disabled = false; btn.textContent = 'Update Customer'; }
    });
    return false;
}

function updateCustomerCredits(e) {
    if (e && e.preventDefault) e.preventDefault();
    var form = e.target;
    var amountVal = parseFloat(form.amount.value);
    var operation = form.operation.value;
    if (isNaN(amountVal) || amountVal < 0) {
        if (typeof showErrorMessage === 'function') showErrorMessage('Please enter a valid amount.');
        return false;
    }
    var btn = form.querySelector('button[type="submit"]');
    if (btn) { btn.disabled = true; btn.textContent = 'Saving...'; }
    if (typeof salonApi === 'undefined' || !salonApi.post) {
        if (typeof showErrorMessage === 'function') showErrorMessage('Unable to save. Please refresh and try again.');
        if (btn) { btn.disabled = false; btn.textContent = 'Save Credits'; }
        return false;
    }
    salonApi.post(apiCustomersUrl + '/' + customerData.id + '/credits', { amount: amountVal, operation: operation }).then(function(res) {
        customerData.creditBalance = res.data.creditBalance;
        renderCustomerCredits();
        if (typeof showSuccessMessage === 'function') showSuccessMessage(res.message || 'Credits updated.');
        if (typeof closeModal === 'function') closeModal();
    }).catch(function(err) {
        if (typeof showErrorMessage === 'function') showErrorMessage(err.message || 'Failed to update credits.');
        if (btn) { btn.disabled = false; btn.textContent = 'Save Credits'; }
    });
    return false;
}

function deleteCustomerFromView() {
    if (!customerData) return;
    var name = ((customerData.firstName || '') + ' ' + (customerData.lastName || '')).trim() || 'this customer';
    var doDelete = function() { doDeleteCustomer(); };
    if (typeof openConfirmModal === 'function') {
        openConfirmModal({
            title: 'Delete customer',
            message: 'You are about to permanently delete this customer: ' + name + '. This cannot be undone. Do you want to continue?',
            confirmLabel: 'Delete',
            onConfirm: doDelete
        });
    } else {
        if (confirm('Delete ' + name + '? This cannot be undone.')) doDelete();
    }
}

function doDeleteCustomer() {
    if (typeof salonApi === 'undefined' || !salonApi.delete) {
        if (typeof showErrorMessage === 'function') showErrorMessage('Unable to delete. Please refresh and try again.');
        return;
    }
    salonApi.delete(apiCustomersUrl + '/' + customerData.id).then(function() {
        if (typeof showSuccessMessage === 'function') showSuccessMessage('Customer deleted.');
        window.location.href = customersIndexUrl;
    }).catch(function(err) {
        if (typeof showErrorMessage === 'function') showErrorMessage(err.message || 'Failed to delete customer.');
    });
}

function printTicket(bookingId) {
    window.print();
}

// Expose to global so onclick/onsubmit in page and modal can call them (must be after all function definitions)
window.openEditCustomerModal = openEditCustomerModal;
window.updateCustomer = updateCustomer;
window.openAdjustCreditsModal = openAdjustCreditsModal;
window.updateCustomerCredits = updateCustomerCredits;
window.deleteCustomerFromView = deleteCustomerFromView;
window.setCustomerViewTab = setCustomerViewTab;

initCustomerViewTabs();
})();
</script>
@endpush
@endsection

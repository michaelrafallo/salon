@extends('layouts.salon')

@section('content')
@php
    $techniciansIndexUrl = route('salon.technicians.index');
    $baseUrl = rtrim(url('api/salon/data'), '/');
    $apiSalonUrl = rtrim(url('api/salon'), '/');
@endphp
<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ $techniciansIndexUrl }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span class="text-sm font-medium">Back to Technicians</span>
            </a>
        </div>

        <!-- Loading / Not found / Content -->
        <div id="technicianLoading" class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
            <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-[#003047] mx-auto mb-4"></div>
            <p class="text-gray-500">Loading technician...</p>
        </div>
        <div id="technicianNotFound" class="hidden bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center text-gray-500">
            <p class="text-lg font-medium mb-2">Technician not found</p>
            <a href="{{ $techniciansIndexUrl }}" class="text-[#003047] hover:underline">Return to Technicians</a>
        </div>
        <div id="technicianContent" class="hidden">
            <!-- Technician Header -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-start gap-6 flex-1">
                        <div id="techAvatar" class="w-24 h-24 rounded-full flex items-center justify-center flex-shrink-0">
                            <span id="techInitials" class="text-4xl font-bold"></span>
                        </div>
                        <div class="flex-1">
                            <h1 id="techName" class="text-3xl font-bold text-gray-900 mb-2"></h1>
                            <p id="techTitle" class="text-lg text-gray-600 mb-2"></p>
                            <span id="techStatus" class="px-3 py-1 text-xs font-medium rounded mb-4 inline-block"></span>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Email</p>
                                    <p class="text-base font-medium text-gray-900" id="technicianEmail"></p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500 mb-1">Phone</p>
                                    <p class="text-base font-medium text-gray-900" id="technicianPhone"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <button type="button" onclick="openEditTechnicianModal()" class="px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95 text-sm">
                            Edit Technician
                        </button>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <p class="text-sm text-gray-500 mb-2">Customers</p>
                    <p id="statCustomers" class="text-3xl font-bold text-gray-900">0</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <p class="text-sm text-gray-500 mb-2">Total</p>
                    <p id="statTotal" class="text-3xl font-bold text-gray-900">{{ $currencySymbol }}0.00</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <p class="text-sm text-gray-500 mb-2">Tip</p>
                    <p id="statTip" class="text-3xl font-bold text-gray-900">{{ $currencySymbol }}0.00</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <p class="text-sm text-gray-500 mb-2">Commission</p>
                    <p id="statCommission" class="text-3xl font-bold text-gray-900">{{ $currencySymbol }}0.00</p>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <p class="text-sm text-gray-500 mb-2">Clock</p>
                    <div class="space-y-2">
                        <div>
                            <p class="text-xs text-gray-500">In:</p>
                            <p id="clockIn" class="text-sm font-medium text-gray-900">--</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Out:</p>
                            <p id="clockOut" class="text-sm font-medium text-gray-900">--</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Commissions Table -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Commissions</h2>
                </div>
                <div class="mb-6 bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <div class="flex flex-col lg:flex-row items-start lg:items-center gap-4">
                        <div class="flex items-center gap-3 flex-1 w-full lg:w-auto">
                            <div class="flex items-center gap-2 bg-white border border-gray-300 rounded-lg px-3 py-2 shadow-sm hover:border-[#003047] transition-colors flex-1 lg:flex-initial">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <input type="date" id="dateRangeFrom" onchange="applyDateRangeFilter()" class="flex-1 text-sm font-medium text-gray-700 border-0 focus:outline-none focus:ring-0 bg-transparent">
                            </div>
                            <span class="text-gray-400 font-medium hidden sm:inline">to</span>
                            <div class="flex items-center gap-2 bg-white border border-gray-300 rounded-lg px-3 py-2 shadow-sm hover:border-[#003047] transition-colors flex-1 lg:flex-initial">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <input type="date" id="dateRangeTo" onchange="applyDateRangeFilter()" class="flex-1 text-sm font-medium text-gray-700 border-0 focus:outline-none focus:ring-0 bg-transparent">
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <button type="button" onclick="setDateRange('today', this)" class="date-preset-btn px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-[#003047] hover:text-white hover:border-[#003047] transition-all active:scale-95 shadow-sm">Today</button>
                            <button type="button" onclick="setDateRange('week', this)" class="date-preset-btn px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-[#003047] hover:text-white hover:border-[#003047] transition-all active:scale-95 shadow-sm">This Week</button>
                            <button type="button" onclick="setDateRange('month', this)" class="date-preset-btn px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-[#003047] hover:text-white hover:border-[#003047] transition-all active:scale-95 shadow-sm">This Month</button>
                            <button type="button" onclick="setDateRange('year', this)" class="date-preset-btn px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-[#003047] hover:text-white hover:border-[#003047] transition-all active:scale-95 shadow-sm">This Year</button>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Date</th>
                                <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700">Total Service</th>
                                <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700">Tip</th>
                                <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700">Commission</th>
                                <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700">Total</th>
                                <th class="text-center py-3 px-4 text-sm font-semibold text-gray-700">Action</th>
                            </tr>
                        </thead>
                        <tbody id="commissionsTableBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

@push('styles')
<style>
@media print {
    /* Remove shadows and unnecessary elements for print */
    #modalOverlay, .shadow, .shadow-sm, .shadow-md, .shadow-lg {
        box-shadow: none !important;
    }

    /* Hide modal overlay background */
    #modalOverlay {
        background: transparent !important;
    }

    /* Remove rounded corners and adjust spacing */
    .rounded-lg, .rounded {
        border-radius: 0 !important;
    }

    /* Hide buttons in print view */
    #modalOverlay button,
    .no-print {
        display: none !important;
    }

    /* Ensure content fits on page */
    #modalContainer {
        max-width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        box-shadow: none !important;
    }

    /* Clean up the ticket display */
    body {
        background: white !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
(function() {
    window.salonTechnicianViewBootstrap = @json($technicianViewBootstrap ?? null);
    var base = '{{ $baseUrl }}';
    var apiSalonUrl = '{{ $apiSalonUrl }}';
    var bootstrap = window.salonTechnicianViewBootstrap || null;
    var technicianId = null;
    var technicianData = null;
    var filteredCommissionsData = [];
    var dateRangeFrom = null;
    var dateRangeTo = null;
    var payoutTransactions = [];
    var bootstrapTransactionsKey = null;
    if (bootstrap && Array.isArray(bootstrap.transactions)) {
        payoutTransactions = bootstrap.transactions;
        bootstrapTransactionsKey = (bootstrap.dateRangeFrom || '') + '|' + (bootstrap.dateRangeTo || '');
    }

    function formatYmd(date) {
        if (!date) return '';
        var y = date.getFullYear();
        var m = String(date.getMonth() + 1).padStart(2, '0');
        var d = String(date.getDate()).padStart(2, '0');
        return y + '-' + m + '-' + d;
    }
    function setActivePreset(range) {
        document.querySelectorAll('.date-preset-btn').forEach(function(btn) {
            btn.classList.remove('bg-[#003047]', 'text-white', 'border-[#003047]');
            btn.classList.add('bg-white', 'text-gray-700', 'border-gray-300');
        });
        if (!range) return;
        var activeBtn = null;
        document.querySelectorAll('.date-preset-btn').forEach(function(btn) {
            var on = btn.getAttribute('onclick') || '';
            if (on.indexOf("'" + range + "'") >= 0) {
                activeBtn = btn;
            }
        });
        if (!activeBtn) return;
        activeBtn.classList.remove('bg-white', 'text-gray-700', 'border-gray-300');
        activeBtn.classList.add('bg-[#003047]', 'text-white', 'border-[#003047]');
    }

    function getAvatarClasses(avatarColor) {
        var map = { pink: ['bg-[#e6f0f3]', 'text-[#003047]'], purple: ['bg-purple-100', 'text-purple-600'], teal: ['bg-teal-100', 'text-teal-600'], indigo: ['bg-indigo-100', 'text-indigo-600'], rose: ['bg-rose-100', 'text-rose-600'], blue: ['bg-blue-100', 'text-blue-600'] };
        return map[avatarColor] || map.pink;
    }
    function getStatusClasses(statusColor) {
        var map = { green: 'bg-green-100 text-green-700', yellow: 'bg-[#e6f0f3] text-[#003047]', gray: 'bg-gray-100 text-gray-700' };
        return map[statusColor] || map.gray;
    }
    function renderTechnician(t) {
        var avatar = getAvatarClasses(t.avatar_color);
        document.getElementById('techAvatar').className = 'w-24 h-24 ' + avatar[0] + ' rounded-full flex items-center justify-center flex-shrink-0';
        document.getElementById('techInitials').className = 'text-4xl font-bold ' + avatar[1];
        document.getElementById('techInitials').textContent = t.initials || '';
        document.getElementById('techName').textContent = (t.first_name || '') + ' ' + (t.last_name || '');
        document.getElementById('techTitle').textContent = t.title || 'Technician';
        var statusEl = document.getElementById('techStatus');
        statusEl.className = 'px-3 py-1 text-xs font-medium rounded mb-4 inline-block ' + getStatusClasses(t.status_color);
        statusEl.textContent = t.status || 'Offline';
        document.getElementById('technicianEmail').textContent = t.email || '';
        document.getElementById('technicianPhone').textContent = t.phone || '';
        document.getElementById('statCustomers').textContent = t.customers ?? 0;
        document.getElementById('statTotal').textContent = window.salonFormatMoney(t.total != null ? Number(t.total) : 0);
        document.getElementById('statTip').textContent = window.salonFormatMoney(t.tip != null ? Number(t.tip) : 0);
        document.getElementById('statCommission').textContent = window.salonFormatMoney(t.commission != null ? Number(t.commission) : 0);
        document.getElementById('clockIn').textContent = t.clock_in || '--';
        document.getElementById('clockOut').textContent = t.clock_out || '--';
    }
    function updateURLWithDateRange(from, to, dateType) {
        var url = new URL(window.location);
        url.searchParams.set('from', from);
        url.searchParams.set('to', to);
        if (dateType) url.searchParams.set('datetype', dateType);
        else url.searchParams.delete('datetype');
        window.history.replaceState({}, '', url);
    }
    function fetchPayoutTransactions() {
        if (!technicianId) return Promise.resolve([]);
        var currentKey = (dateRangeFrom || '') + '|' + (dateRangeTo || '');
        if (bootstrapTransactionsKey && currentKey === bootstrapTransactionsKey && Array.isArray(payoutTransactions)) {
            return Promise.resolve(payoutTransactions);
        }
        var url = base + '/payout?technician_id=' + encodeURIComponent(technicianId);
        if (dateRangeFrom) url += '&from=' + encodeURIComponent(dateRangeFrom);
        if (dateRangeTo) url += '&to=' + encodeURIComponent(dateRangeTo);
        return fetch(url).then(function(r) { return r.json(); }).then(function(res) {
            var tx = (res && res.transactions) ? res.transactions : [];
            payoutTransactions = Array.isArray(tx) ? tx : [];
            return payoutTransactions;
        }).catch(function() {
            payoutTransactions = [];
            return payoutTransactions;
        });
    }
    function setDateRange(range, buttonElement) {
        var today = new Date();
        var fromDate, toDate;
        switch (range) {
            case 'today':
                fromDate = new Date(today);
                toDate = new Date(today);
                break;
            case 'week':
                var dayOfWeek = today.getDay();
                var diff = today.getDate() - dayOfWeek;
                fromDate = new Date(today.getFullYear(), today.getMonth(), diff);
                toDate = new Date(fromDate);
                toDate.setDate(fromDate.getDate() + 6);
                break;
            case 'month':
                fromDate = new Date(today.getFullYear(), today.getMonth(), 1);
                toDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                break;
            case 'year':
                fromDate = new Date(today.getFullYear(), 0, 1);
                toDate = new Date(today.getFullYear(), 11, 31);
                break;
            default:
                return;
        }
        dateRangeFrom = formatYmd(fromDate);
        dateRangeTo = formatYmd(toDate);
        document.getElementById('dateRangeFrom').value = dateRangeFrom;
        document.getElementById('dateRangeTo').value = dateRangeTo;
        updateURLWithDateRange(dateRangeFrom, dateRangeTo, range);
        setActivePreset(range);
        applyDateRangeFilter(true);
    }
    function applyDateRangeFilter(preserveButtonState) {
        var fromInput = document.getElementById('dateRangeFrom');
        var toInput = document.getElementById('dateRangeTo');
        if (!fromInput || !toInput) return;
        dateRangeFrom = fromInput.value;
        dateRangeTo = toInput.value;
        if (dateRangeFrom && dateRangeTo && !preserveButtonState) {
            updateURLWithDateRange(dateRangeFrom, dateRangeTo, null);
            setActivePreset(null);
        }
        if (!preserveButtonState) {
            document.querySelectorAll('.date-preset-btn').forEach(function(btn) {
                btn.classList.remove('bg-[#003047]', 'text-white', 'border-[#003047]');
                btn.classList.add('bg-white', 'text-gray-700', 'border-gray-300');
            });
        }
        fetchPayoutTransactions().then(function() {
            renderCommissions();
        });
    }
    function renderCommissions() {
        var tbody = document.getElementById('commissionsTableBody');
        if (!tbody) return;
        var grouped = {};
        payoutTransactions.forEach(function(t) {
            if (!t || !t.date) return;
            var dateKey = t.date;
            if (!grouped[dateKey]) grouped[dateKey] = { date: dateKey, total: 0, tip: 0, commission: 0 };
            grouped[dateKey].total += Number(t.amount || 0);
            grouped[dateKey].tip += Number(t.tip || 0);
            grouped[dateKey].commission += Number(t.commission || 0);
        });
        filteredCommissionsData = Object.keys(grouped).sort(function(a, b) { return b.localeCompare(a); }).map(function(k) { return grouped[k]; });
        if (!filteredCommissionsData || filteredCommissionsData.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-8 text-gray-500">No commissions found</td></tr>';
            return;
        }
        var totalService = 0, totalTip = 0, totalCommission = 0, totalTotal = 0;
        var html = '';
        filteredCommissionsData.forEach(function(c) {
            var dateStr = c.date ? new Date(c.date + 'T00:00:00').toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' }) : 'N/A';
            var serviceAmount = Number(c.total) || 0;
            var tipAmount = Number(c.tip) || 0;
            var commissionAmount = Number(c.commission) || 0;
            var rowTotal = tipAmount + commissionAmount;
            totalService += serviceAmount;
            totalTip += c.tip;
            totalCommission += c.commission;
            totalTotal += rowTotal;
            html += '<tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">';
            html += '<td class="py-3 px-4 text-sm text-gray-900">' + dateStr + '</td>';
            html += '<td class="py-3 px-4 text-sm text-gray-900 text-right font-medium">' + window.salonFormatMoney(serviceAmount) + '</td>';
            html += '<td class="py-3 px-4 text-sm text-gray-900 text-right font-medium">' + window.salonFormatMoney(tipAmount) + '</td>';
            html += '<td class="py-3 px-4 text-sm text-[#003047] text-right font-bold">' + window.salonFormatMoney(commissionAmount) + '</td>';
            html += '<td class="py-3 px-4 text-sm text-gray-900 text-right font-semibold">' + window.salonFormatMoney(rowTotal) + '</td>';
            html += '<td class="py-3 px-4 text-center"><button type="button" onclick="openCommissionsDateDetails(\'' + c.date + '\')" class="px-3 py-1.5 bg-gray-500 text-white text-xs font-medium rounded hover:bg-gray-600 transition active:scale-95">Open</button></td>';
            html += '</tr>';
        });
        html += '<tr class="border-t-2 border-gray-300 bg-gray-50"><td class="py-4 px-4 text-sm font-bold text-gray-900">Total</td>';
        html += '<td class="py-4 px-4 text-sm font-bold text-gray-900 text-right">' + window.salonFormatMoney(totalService) + '</td>';
        html += '<td class="py-4 px-4 text-sm font-bold text-gray-900 text-right">' + window.salonFormatMoney(totalTip) + '</td>';
        html += '<td class="py-4 px-4 text-sm font-bold text-[#003047] text-right">' + window.salonFormatMoney(totalCommission) + '</td>';
        html += '<td class="py-4 px-4 text-sm font-bold text-gray-900 text-right">' + window.salonFormatMoney(totalTotal) + '</td>';
        html += '<td class="py-4 px-4 text-center"><button type="button" onclick="showCommissionsPrintModal()" class="p-2 text-gray-600 hover:text-[#003047] hover:bg-gray-100 rounded-lg transition-colors" title="Print"><svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg></button></td></tr>';
        tbody.innerHTML = html;
    }
    window.openCommissionsDateDetails = function(dateKey) {
        var dateTransactions = payoutTransactions.filter(function(t) { return t.date === dateKey; });
        if (!dateTransactions.length) {
            alert('No transactions found for this date');
            return;
        }
        var dateTotal = 0, dateTip = 0, dateCommission = 0, dateGrandTotal = 0;
        dateTransactions.forEach(function(t) {
            dateTotal += Number(t.amount || 0);
            dateTip += Number(t.tip || 0);
            dateCommission += Number(t.commission || 0);
            dateGrandTotal += Number(t.total != null ? t.total : (Number(t.tip || 0) + Number(t.commission || 0)));
        });
        var date = new Date(dateKey + 'T00:00:00');
        var monthNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        var formattedDate = date.getDate() + '-' + monthNames[date.getMonth()] + '-' + date.getFullYear();
        var name = (technicianData && ((technicianData.first_name || '') + ' ' + (technicianData.last_name || '')).trim()) || 'Technician';
        var techNameUpper = name.toUpperCase();
        var transactionsList = '';
        dateTransactions.forEach(function(transaction) {
            var time = transaction.time || '00:00';
            var parts = time.split(':');
            var formattedTime = parts[0].padStart(2, '0') + ':' + (parts[1] || '00').padStart(2, '0');
            var amount = Number(transaction.amount || 0);
            var tip = Number(transaction.tip || 0);
            var commission = Number(transaction.commission || 0);
            var total = Number(transaction.total != null ? transaction.total : (tip + commission));
            transactionsList += '<tr class="border-b border-gray-200"><td class="py-2 px-4 text-sm text-gray-900 border-r border-gray-200">' + formattedTime + ' | ' + formattedDate + '</td><td class="py-2 px-4 text-sm text-gray-900 text-right">' + window.salonFormatMoney(amount) + ' | ' + window.salonFormatMoney(tip) + ' | ' + window.salonFormatMoney(commission) + ' | ' + window.salonFormatMoney(total) + '</td></tr>';
        });
        var modalContent = ''
            + '<div class="p-6 bg-white border border-gray-300 rounded-lg">'
            + '<div class="text-center mb-6 border-b border-gray-300 pb-4">'
            + '<h1 class="text-3xl font-bold text-gray-900 mb-2">Dons Nail Spa</h1>'
            + '<p class="text-sm text-gray-700">258 Hedrick St, Beckley, WV 25801</p>'
            + '<p class="text-sm text-gray-700">Phone: 681-2077114</p>'
            + '<p class="text-sm text-gray-700">Merchant ID (MID): 23420</p>'
            + '</div>'
            + '<div class="mb-4 border-b border-gray-200 pb-3 text-center">'
            + '<h2 class="text-xl font-bold text-gray-900 mb-1">' + name + ' Daily Report</h2>'
            + '<p class="text-sm text-gray-600">' + formattedDate + '</p>'
            + '</div>'
            + '<div class="mb-6 overflow-y-auto max-h-96 border border-gray-300 rounded-lg">'
            + '<table class="w-full">'
            + '<thead class="bg-gray-50 sticky top-0"><tr class="border-b border-gray-300">'
            + '<th class="text-left py-3 px-4 text-sm font-semibold text-gray-500 uppercase border-r border-gray-300">' + techNameUpper + '</th>'
            + '<th class="text-right py-3 px-4 text-sm font-semibold text-gray-500 uppercase">SERVICE | TIP | COMMISSION | TOTAL</th>'
            + '</tr></thead>'
            + '<tbody>' + transactionsList + '</tbody>'
            + '</table>'
            + '</div>'
            + '<div class="border-t border-gray-200 pt-4 mb-6">'
            + '<div class="flex justify-between items-center mb-2 pb-2 border-b border-gray-200"><span class="text-sm font-semibold text-gray-900">Total Service:</span><span class="text-sm font-semibold text-gray-900">' + window.salonFormatMoney(dateTotal) + '</span></div>'
            + '<div class="flex justify-between items-center mb-2 pb-2 border-b border-gray-200"><span class="text-sm font-semibold text-gray-900">Total Tip:</span><span class="text-sm font-semibold text-gray-900">' + window.salonFormatMoney(dateTip) + '</span></div>'
            + '<div class="flex justify-between items-center mb-2 pb-2 border-b border-gray-200"><span class="text-sm font-semibold text-gray-900">Total Commission:</span><span class="text-sm font-semibold text-gray-900">' + window.salonFormatMoney(dateCommission) + '</span></div>'
            + '<div class="flex justify-between items-center"><span class="text-sm font-semibold text-gray-900">Total:</span><span class="text-sm font-semibold text-gray-900">' + window.salonFormatMoney(dateGrandTotal) + '</span></div>'
            + '</div>'
            + '<div class="flex justify-end gap-3 border-t border-gray-200 pt-4">'
            + '<button onclick="window.print()" class="px-6 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#004060] transition-colors font-medium">Print</button>'
            + '<button onclick="closeModal()" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">Close</button>'
            + '</div>'
            + '</div>';
        if (typeof openModal === 'function') {
            openModal(modalContent, 'medium');
        }
    };

    // Needed for inline onclick/onchange handlers in the Blade markup.
    window.setDateRange = setDateRange;
    window.applyDateRangeFilter = function() {
        applyDateRangeFilter(false);
    };
    window.openEditTechnicianModal = function() {
        if (!technicianData) return;
        var t = technicianData;
        var statusChecked = (t.status === 'Available' || t.status === 'Busy') ? ' checked' : '';
        var content = '<div class="p-6"><div class="flex items-center justify-between mb-4"><h3 class="text-xl font-bold text-gray-900">Edit Technician</h3><button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div>';
        content += '<form onsubmit="updateTechnician(event)" class="space-y-4"><input type="hidden" name="technician_id" value="' + t.id + '">';
        content += '<div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">First Name</label><input type="text" name="first_name" value="' + (t.first_name || '').replace(/"/g, '&quot;') + '" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div>';
        content += '<div><label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label><input type="text" name="last_name" value="' + (t.last_name || '').replace(/"/g, '&quot;') + '" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div></div>';
        content += '<div><label class="block text-sm font-medium text-gray-700 mb-2">Title</label><input type="text" name="title" value="' + (t.title || '').replace(/"/g, '&quot;') + '" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div>';
        content += '<div><label class="block text-sm font-medium text-gray-700 mb-2">Email</label><input type="email" name="email" value="' + (t.email || '').replace(/"/g, '&quot;') + '" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div>';
        content += '<div><label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label><input type="tel" name="phone" value="' + (t.phone || '').replace(/"/g, '&quot;') + '" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div>';
        content += '<div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg"><div><label class="text-sm font-medium text-gray-900">Active</label></div><label class="relative inline-flex items-center cursor-pointer"><input type="checkbox" name="active" class="sr-only peer"' + statusChecked + '><div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[\'\'] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div></label></div>';
        content += '<div class="flex justify-end gap-3 pt-4"><button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Update Technician</button></div></form></div>';
        openModal(content);
    };
    window.updateTechnician = function(event) {
        event.preventDefault();
        var form = event.target;
        var submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) { submitBtn.disabled = true; submitBtn.textContent = 'Saving...'; }
        var firstName = form.querySelector('[name="first_name"]').value.trim();
        var lastName = form.querySelector('[name="last_name"]').value.trim();
        var email = form.querySelector('[name="email"]').value.trim();
        var phone = (form.querySelector('[name="phone"]').value || '').trim();
        var activeCheckbox = form.querySelector('[name="active"]');
        var status = (activeCheckbox && activeCheckbox.checked) ? 'active' : 'inactive';
        var payload = { first_name: firstName, last_name: lastName, email: email, phone: phone || null, status: status };
        var apiUrl = apiSalonUrl + '/users/' + technicianId;
        if (typeof salonApi === 'undefined' || !salonApi.put) {
            if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = 'Update Technician'; }
            if (typeof showErrorMessage === 'function') showErrorMessage('Unable to save. Please refresh and try again.');
            return;
        }
        salonApi.put(apiUrl, payload).then(function(res) {
            technicianData.first_name = firstName;
            technicianData.last_name = lastName;
            technicianData.email = email;
            technicianData.phone = phone;
            technicianData.status = status === 'active' ? 'Available' : 'Offline';
            document.getElementById('technicianEmail').textContent = email;
            document.getElementById('technicianPhone').textContent = phone;
            document.getElementById('techName').textContent = firstName + ' ' + lastName;
            var statusEl = document.getElementById('techStatus');
            if (statusEl) {
                statusEl.textContent = status === 'active' ? 'Available' : 'Offline';
                statusEl.className = 'px-3 py-1 text-xs font-medium rounded mb-4 inline-block ' + (status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700');
            }
            closeModal();
            showSuccessMessage(res.message || 'Technician details updated successfully!');
        }).catch(function(err) {
            if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = 'Update Technician'; }
            var msg = err && err.message ? err.message : 'Failed to update technician.';
            if (err && err.body && err.body.errors && typeof err.body.errors === 'object') {
                var firstKey = Object.keys(err.body.errors)[0];
                if (firstKey && err.body.errors[firstKey] && err.body.errors[firstKey][0]) msg = err.body.errors[firstKey][0];
            }
            if (typeof showErrorMessage === 'function') showErrorMessage(msg);
            else alert(msg);
        });
    };
    window.showCommissionsPrintModal = function() {
        if (!filteredCommissionsData || filteredCommissionsData.length === 0) {
            alert('No commissions data to print');
            return;
        }
        var name = (technicianData && (technicianData.first_name + ' ' + technicianData.last_name)) || '';
        var technicianName = name.toUpperCase();
        var fromDate = dateRangeFrom ? new Date(dateRangeFrom + 'T00:00:00') : new Date();
        var toDate = dateRangeTo ? new Date(dateRangeTo + 'T23:59:59') : new Date();
        var dateRangeText = dateRangeFrom === dateRangeTo ? fromDate.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }).replace(/ /g, '-') : fromDate.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }).replace(/ /g, '-') + ' to ' + toDate.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }).replace(/ /g, '-');
        var totalAmount = 0, totalTip = 0, totalCommission = 0, totalGrand = 0;
        filteredCommissionsData.forEach(function(c) {
            totalAmount += Number(c.total || 0);
            totalTip += Number(c.tip || 0);
            totalCommission += Number(c.commission || 0);
            totalGrand += (Number(c.tip || 0) + Number(c.commission || 0));
        });
        var rows = filteredCommissionsData.map(function(c) {
            var d = new Date(c.date + 'T00:00:00');
            var time = d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: false });
            var dateStr = d.toLocaleDateString('en-US', { month: '2-digit', day: '2-digit', year: '2-digit' });
            var total = Number(c.tip || 0) + Number(c.commission || 0);
            return '<tr class="border-b border-gray-200"><td class="px-2 py-2 text-sm text-gray-900">' + time + ' | ' + dateStr + '</td><td class="px-2 py-2 text-sm text-gray-900 text-right">' + window.salonFormatMoney(Number(c.total || 0)) + ' | ' + window.salonFormatMoney(Number(c.tip || 0)) + ' | ' + window.salonFormatMoney(Number(c.commission || 0)) + ' | ' + window.salonFormatMoney(total) + '</td></tr>';
        }).join('');
        var content = '<div class="p-6 mx-auto"><div class="bg-white border border-gray-300 rounded-lg p-6"><div class="text-center mb-6 border-b border-gray-300 pb-4"><h2 class="text-xl font-bold text-gray-900 mb-2">Dons Nail Spa</h2><p class="text-sm text-gray-600">258 Hedrick St, Beckley, WV 25801</p><p class="text-sm text-gray-600">Phone: 681-2077114</p><p class="text-sm text-gray-600">Merchant ID (MID): 23420</p></div><div class="text-center mb-4"><h3 class="text-lg font-semibold text-gray-900">' + technicianName + ' Daily Report</h3><p class="text-sm text-gray-600 mt-1">' + dateRangeText + '</p></div><div class="mb-6"><div class="max-h-96 overflow-y-auto border border-gray-200 rounded"><table class="w-full border-collapse"><thead class="sticky top-0 bg-white z-10"><tr class="border-b-2 border-gray-300"><th class="px-2 py-2 text-left text-xs font-semibold text-gray-700 uppercase">' + technicianName + '</th><th class="px-2 py-2 text-right text-xs font-semibold text-gray-700 uppercase">SERVICE | TIP | COMMISSION | TOTAL</th></tr></thead><tbody>' + rows + '</tbody></table></div></div><div class="border-t-2 border-gray-300 pt-4 mt-4"><div class="flex justify-between items-center mb-2"><span class="text-sm font-semibold text-gray-900">Total Service:</span><span class="text-sm font-bold text-gray-900">' + window.salonFormatMoney(totalAmount) + '</span></div><div class="flex justify-between items-center"><span class="text-sm font-semibold text-gray-900">Total Tip:</span><span class="text-sm font-bold text-gray-900">' + window.salonFormatMoney(totalTip) + '</span></div><div class="flex justify-between items-center mt-2 pt-2 border-t border-gray-200"><span class="text-sm font-semibold text-[#003047]">Total Commission:</span><span class="text-sm font-bold text-[#003047]">' + window.salonFormatMoney(totalCommission) + '</span></div><div class="flex justify-between items-center mt-2"><span class="text-sm font-semibold text-gray-900">Total:</span><span class="text-sm font-bold text-gray-900">' + window.salonFormatMoney(totalGrand) + '</span></div></div><div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200"><button type="button" onclick="window.print()" class="px-6 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium">Print</button><button type="button" onclick="closeModal()" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">Close</button></div></div></div>';
        openModal(content, 'medium');
    };

    function initFromUrl() {
        var params = new URLSearchParams(window.location.search);
        var id = params.get('id');
        if (!id) {
            document.getElementById('technicianLoading').classList.add('hidden');
            document.getElementById('technicianNotFound').classList.remove('hidden');
            return;
        }
        technicianId = id;
        var url = base + '/technicians/' + id;

        if (bootstrap && bootstrap.error) {
            document.getElementById('technicianLoading').classList.add('hidden');
            document.getElementById('technicianNotFound').classList.remove('hidden');
            return;
        }

        if (bootstrap && bootstrap.technician && bootstrap.technician.id != null && bootstrap.technician.id.toString() === id.toString()) {
            document.getElementById('technicianLoading').classList.add('hidden');
            technicianData = bootstrap.technician;
            renderTechnician(technicianData);
            document.getElementById('technicianContent').classList.remove('hidden');
            var urlFrom = params.get('from');
            var urlTo = params.get('to');
            var urlDateType = params.get('datetype');
            if (urlFrom && urlTo) {
                dateRangeFrom = urlFrom;
                dateRangeTo = urlTo;
                document.getElementById('dateRangeFrom').value = dateRangeFrom;
                document.getElementById('dateRangeTo').value = dateRangeTo;
                setActivePreset(urlDateType || null);
            } else if (bootstrap.dateRangeFrom && bootstrap.dateRangeTo) {
                dateRangeFrom = bootstrap.dateRangeFrom;
                dateRangeTo = bootstrap.dateRangeTo;
                document.getElementById('dateRangeFrom').value = dateRangeFrom;
                document.getElementById('dateRangeTo').value = dateRangeTo;
                updateURLWithDateRange(dateRangeFrom, dateRangeTo, bootstrap.dateType || 'today');
                setActivePreset(bootstrap.dateType || 'today');
            } else {
                var today = new Date();
                dateRangeFrom = formatYmd(today);
                dateRangeTo = formatYmd(today);
                document.getElementById('dateRangeFrom').value = dateRangeFrom;
                document.getElementById('dateRangeTo').value = dateRangeTo;
                updateURLWithDateRange(dateRangeFrom, dateRangeTo, 'today');
                setActivePreset('today');
            }
            applyDateRangeFilter(true);
            return;
        }

        fetch(url).then(function(r) {
            if (!r.ok) throw new Error('Not found');
            return r.json();
        }).then(function(data) {
            document.getElementById('technicianLoading').classList.add('hidden');
            technicianData = data.technician;
            renderTechnician(technicianData);
            document.getElementById('technicianContent').classList.remove('hidden');
            var urlFrom = params.get('from');
            var urlTo = params.get('to');
            var urlDateType = params.get('datetype');
            if (urlFrom && urlTo) {
                dateRangeFrom = urlFrom;
                dateRangeTo = urlTo;
                document.getElementById('dateRangeFrom').value = dateRangeFrom;
                document.getElementById('dateRangeTo').value = dateRangeTo;
                setActivePreset(urlDateType || null);
            } else {
                var today = new Date();
                dateRangeFrom = formatYmd(today);
                dateRangeTo = formatYmd(today);
                document.getElementById('dateRangeFrom').value = dateRangeFrom;
                document.getElementById('dateRangeTo').value = dateRangeTo;
                updateURLWithDateRange(dateRangeFrom, dateRangeTo, 'today');
                setActivePreset('today');
            }
            applyDateRangeFilter(true);
        }).catch(function() {
            document.getElementById('technicianLoading').classList.add('hidden');
            document.getElementById('technicianNotFound').classList.remove('hidden');
        });
    }
    document.addEventListener('DOMContentLoaded', initFromUrl);
})();
</script>
@endpush
@endsection

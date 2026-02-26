@extends('layouts.salon')

@section('content')
@php
    $isTechnician = session('salon_role', 'admin') === 'technician';
    $loggedInUserId = $loggedInUserId ?? null;
@endphp
<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1">Payout</h1>
                <p class="text-gray-500 text-sm">Manage technician payouts and commissions</p>
            </div>
            <button type="button" onclick="salonPayoutPrintReport()" class="flex items-center gap-2 px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#004060] transition-colors font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Open
            </button>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
            <div class="mb-6 bg-gray-50 rounded-xl p-4 border border-gray-200" id="filtersSection">
                <div class="flex flex-col lg:flex-row items-start lg:items-center gap-4">
                    @if(!$isTechnician)
                    <div class="flex items-center gap-2 bg-white border border-gray-300 rounded-lg px-3 py-2 shadow-sm hover:border-[#003047] transition-colors">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <select id="technicianFilter" onchange="salonPayoutOnTechnicianChange()" class="text-sm font-medium text-gray-700 border-0 focus:outline-none focus:ring-0 bg-transparent cursor-pointer">
                            <option value="">Select Technician</option>
                        </select>
                    </div>
                    @endif
                    <div class="flex items-center gap-3 flex-1 w-full lg:w-auto">
                        <div class="flex items-center gap-2 bg-white border border-gray-300 rounded-lg px-3 py-2 shadow-sm hover:border-[#003047] transition-colors flex-1 lg:flex-initial">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <input type="date" id="dateRangeFrom" onchange="salonPayoutApplyDateFilter()" class="flex-1 text-sm font-medium text-gray-700 border-0 focus:outline-none focus:ring-0 bg-transparent">
                        </div>
                        <span class="text-gray-400 font-medium hidden sm:inline">to</span>
                        <div class="flex items-center gap-2 bg-white border border-gray-300 rounded-lg px-3 py-2 shadow-sm hover:border-[#003047] transition-colors flex-1 lg:flex-initial">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <input type="date" id="dateRangeTo" onchange="salonPayoutApplyDateFilter()" class="flex-1 text-sm font-medium text-gray-700 border-0 focus:outline-none focus:ring-0 bg-transparent">
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap ml-auto">
                        <button type="button" onclick="salonPayoutSetDateRange('today', this)" class="date-preset-btn px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-[#003047] hover:text-white hover:border-[#003047] transition-all active:scale-95 shadow-sm">Today</button>
                        <button type="button" onclick="salonPayoutSetDateRange('week', this)" class="date-preset-btn px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-[#003047] hover:text-white hover:border-[#003047] transition-all active:scale-95 shadow-sm">This Week</button>
                        <button type="button" onclick="salonPayoutSetDateRange('month', this)" class="date-preset-btn px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-[#003047] hover:text-white hover:border-[#003047] transition-all active:scale-95 shadow-sm">This Month</button>
                        <button type="button" onclick="salonPayoutSetDateRange('year', this)" class="date-preset-btn px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-[#003047] hover:text-white hover:border-[#003047] transition-all active:scale-95 shadow-sm">This Year</button>
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
                            <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700"></th>
                        </tr>
                    </thead>
                    <tbody id="payoutTableBody">
                        @if($isTechnician)
                            <tr><td colspan="6" class="text-center py-8 text-gray-500"><p>Loading payouts...</p></td></tr>
                        @else
                            <tr><td colspan="6" class="text-center py-8 text-gray-500"><p>Please select a technician to view payouts</p></td></tr>
                        @endif
                    </tbody>
                </table>
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
var base = window.salonJsonBase || '{{ url("api/salon/data") }}';
window.salonPayoutBootstrap = window.salonPayoutBootstrap || @json($payoutBootstrap ?? null);
var isTechnicianUser = {{ $isTechnician ? 'true' : 'false' }};
var loggedInUserId = @json($loggedInUserId);
var payoutData = [], filteredPayoutData = [], dateRangeFrom = null, dateRangeTo = null, selectedTechnicianId = isTechnicianUser ? (loggedInUserId ? String(loggedInUserId) : null) : null, techniciansList = [];
function salonPayoutFormatYmd(date) {
    if (!date) return '';
    var y = date.getFullYear();
    var m = String(date.getMonth() + 1).padStart(2, '0');
    var d = String(date.getDate()).padStart(2, '0');
    return y + '-' + m + '-' + d;
}
async function salonPayoutFetchPayouts() {
    try {
        var urlParams = new URLSearchParams(window.location.search);
        var urlFrom = urlParams.get('from');
        var urlTo = urlParams.get('to');
        var urlDateType = urlParams.get('datetype');
        var urlTech = urlParams.get('technician');

        if (!isTechnicianUser && urlTech) {
            selectedTechnicianId = urlTech;
            var dropdown = document.getElementById('technicianFilter');
            if (dropdown) dropdown.value = urlTech;
        }

        if (urlFrom && urlTo) {
            dateRangeFrom = urlFrom;
            dateRangeTo = urlTo;
            var fromEl = document.getElementById('dateRangeFrom'), toEl = document.getElementById('dateRangeTo');
            if (fromEl) fromEl.value = dateRangeFrom;
            if (toEl) toEl.value = dateRangeTo;
        } else {
            var today = new Date();
            dateRangeFrom = salonPayoutFormatYmd(today);
            dateRangeTo = salonPayoutFormatYmd(today);
            var fromEl = document.getElementById('dateRangeFrom'), toEl = document.getElementById('dateRangeTo');
            if (fromEl) fromEl.value = dateRangeFrom;
            if (toEl) toEl.value = dateRangeTo;
            if (!isTechnicianUser) {
                salonPayoutUpdateURLWithDateRange(dateRangeFrom, dateRangeTo, 'today');
            }
        }

        if (urlDateType) {
            setTimeout(function() {
                salonPayoutSetActivePreset(urlDateType);
            }, 50);
        } else if (!urlFrom || !urlTo) {
            setTimeout(function() {
                salonPayoutSetActivePreset('today');
            }, 50);
        }

        if (!selectedTechnicianId) {
            payoutData = [];
            filteredPayoutData = [];
            salonPayoutRenderPayouts();
            return;
        }

        var payoutUrl = base + '/payout?technician_id=' + encodeURIComponent(selectedTechnicianId);
        if (dateRangeFrom) payoutUrl += '&from=' + encodeURIComponent(dateRangeFrom);
        if (dateRangeTo) payoutUrl += '&to=' + encodeURIComponent(dateRangeTo);

        var res = await fetch(payoutUrl);
        var data = await res.json();
        payoutData = (data.transactions || []).sort(function(a, b) {
            var dateA = new Date(a.date), dateB = new Date(b.date);
            if (dateB.getTime() !== dateA.getTime()) return dateB - dateA;
            var timeA = (a.time || '00:00').split(':').map(Number), timeB = (b.time || '00:00').split(':').map(Number);
            var timeAValue = timeA[0] * 60 + timeA[1], timeBValue = timeB[0] * 60 + timeB[1];
            return timeBValue - timeAValue;
        });

        filteredPayoutData = payoutData;
        salonPayoutRenderPayouts();
    } catch (err) {
        console.error('Error fetching payouts:', err);
        payoutData = [];
        filteredPayoutData = [];
        salonPayoutRenderPayouts();
    }
}
function salonPayoutUpdateURLWithDateRange(from, to, dateType) {
    var url = new URL(window.location);
    url.searchParams.set('from', from);
    url.searchParams.set('to', to);
    if (dateType) url.searchParams.set('datetype', dateType);
    else url.searchParams.delete('datetype');
    if (selectedTechnicianId) url.searchParams.set('technician', selectedTechnicianId);
    window.history.pushState({}, '', url);
}
function salonPayoutSetActivePreset(range) {
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
window.salonPayoutSetDateRange = function(range, buttonElement) {
    var today = new Date(), fromDate, toDate;
    switch(range) {
        case 'today': fromDate = new Date(today); toDate = new Date(today); break;
        case 'week':
            var dayOfWeek = today.getDay(), diff = today.getDate() - dayOfWeek;
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
        default: return;
    }
    dateRangeFrom = salonPayoutFormatYmd(fromDate);
    dateRangeTo = salonPayoutFormatYmd(toDate);
    var fromEl = document.getElementById('dateRangeFrom'), toEl = document.getElementById('dateRangeTo');
    if (fromEl) fromEl.value = dateRangeFrom;
    if (toEl) toEl.value = dateRangeTo;
    salonPayoutUpdateURLWithDateRange(dateRangeFrom, dateRangeTo, range);
    salonPayoutSetActivePreset(range);
    salonPayoutApplyDateRangeFilter(true);
};
window.salonPayoutOnTechnicianChange = function() {
    var sel = document.getElementById('technicianFilter');
    if (!sel) return;
    var newTechId = sel.value || null;
    if (newTechId === selectedTechnicianId) return;
    selectedTechnicianId = newTechId;
    var url = new URL(window.location);
    if (selectedTechnicianId) url.searchParams.set('technician', selectedTechnicianId);
    else url.searchParams.delete('technician');
    window.history.pushState({}, '', url);
    if (selectedTechnicianId) {
        salonPayoutFetchPayouts();
    } else {
        payoutData = [];
        filteredPayoutData = [];
        salonPayoutRenderPayouts();
    }
};
function salonPayoutPopulateTechnicianDropdown(technicians) {
    var dropdown = document.getElementById('technicianFilter');
    if (!dropdown) return;
    var currentValue = dropdown.value;
    var wasEmpty = !currentValue || currentValue === '';
    dropdown.removeAttribute('onchange');
    dropdown.innerHTML = '<option value="">Select Technician</option>';
    technicians.forEach(function(tech) {
        var fullName = tech.firstName + ' ' + tech.lastName;
        var opt = document.createElement('option');
        opt.value = tech.id;
        opt.textContent = fullName;
        dropdown.appendChild(opt);
    });
    if (selectedTechnicianId) dropdown.value = selectedTechnicianId;
    else if (!wasEmpty && currentValue) dropdown.value = currentValue;
    dropdown.setAttribute('onchange', 'salonPayoutOnTechnicianChange()');
}
window.salonPayoutApplyDateFilter = function() { salonPayoutApplyDateRangeFilter(false); };
function salonPayoutApplyDateRangeFilter(preserveButtonState) {
    var fromInput = document.getElementById('dateRangeFrom');
    var toInput = document.getElementById('dateRangeTo');
    var techSelect = document.getElementById('technicianFilter');
    if (!fromInput || !toInput) return;
    dateRangeFrom = fromInput.value;
    dateRangeTo = toInput.value;
    if (isTechnicianUser) {
        if (!selectedTechnicianId && loggedInUserId) selectedTechnicianId = String(loggedInUserId);
    } else {
        selectedTechnicianId = techSelect ? techSelect.value : null;
    }
    if (!selectedTechnicianId && !isTechnicianUser) {
        filteredPayoutData = [];
        salonPayoutRenderPayouts();
        var url = new URL(window.location);
        url.searchParams.delete('technician');
        if (dateRangeFrom && dateRangeTo && !preserveButtonState) {
            url.searchParams.set('from', dateRangeFrom);
            url.searchParams.set('to', dateRangeTo);
        }
        window.history.pushState({}, '', url);
        return;
    }
    if (dateRangeFrom && dateRangeTo && !preserveButtonState) {
        salonPayoutUpdateURLWithDateRange(dateRangeFrom, dateRangeTo, null);
        salonPayoutSetActivePreset(null);
    }
    var url = new URL(window.location);
    url.searchParams.set('technician', selectedTechnicianId);
    window.history.pushState({}, '', url);
    if (!preserveButtonState) {
        document.querySelectorAll('.date-preset-btn').forEach(function(btn) {
            btn.classList.remove('bg-[#003047]', 'text-white', 'border-[#003047]');
            btn.classList.add('bg-white', 'text-gray-700', 'border-gray-300');
        });
    }
    salonPayoutFetchPayouts();
}
window.salonPayoutViewPayoutDetails = function(dateKey) {
    var dateTransactions = filteredPayoutData.filter(function(t) { return t.date === dateKey; });
    if (dateTransactions.length === 0) {
        var modalContent = '<div class="p-6"><div class="flex items-center justify-center mb-4"><div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center"><svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div></div><h3 class="text-xl font-bold text-gray-900 mb-2 text-center">No Transactions Found</h3><p class="text-gray-600 text-center mb-6">No transactions found for this date.</p><div class="flex justify-center"><button onclick="closeModal()" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">OK</button></div></div>';
        if (typeof openModal === 'function') openModal(modalContent, 'small');
        return;
    }
    var dateTotal = 0, dateTip = 0, dateCommission = 0, dateGrandTotal = 0;
    dateTransactions.forEach(function(t) {
        dateTotal += Number(t.amount || 0);
        dateTip += Number(t.tip || 0);
        dateCommission += Number(t.commission || 0);
        dateGrandTotal += Number(t.total != null ? t.total : ((t.tip || 0) + (t.commission || 0)));
    });
    var date = new Date(dateKey);
    var monthNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    var month = monthNames[date.getMonth()];
    var day = date.getDate();
    var year = date.getFullYear();
    var formattedDate = day + '-' + month + '-' + year;
    var tech = techniciansList.find(function(t) { return t.id.toString() === selectedTechnicianId.toString(); });
    var techName = tech ? tech.firstName + ' ' + tech.lastName : 'Technician';
    var techNameUpper = techName.toUpperCase();
    var transactionsList = '';
    dateTransactions.forEach(function(transaction) {
        var time = transaction.time || '00:00';
        var parts = time.split(':');
        var formattedTime = parts[0].padStart(2, '0') + ':' + parts[1].padStart(2, '0');
        var transDate = new Date(transaction.date);
        var transMonth = String(transDate.getMonth() + 1).padStart(2, '0');
        var transDay = String(transDate.getDate()).padStart(2, '0');
        var transYear = String(transDate.getFullYear()).slice(-2);
        var formattedTransDate = transMonth + '/' + transDay + '/' + transYear;
        var amount = Number(transaction.amount || 0);
        var tip = Number(transaction.tip || 0);
        var commission = Number(transaction.commission || 0);
        var total = Number(transaction.total != null ? transaction.total : (tip + commission));
        transactionsList += '<tr class="border-b border-gray-200"><td class="py-2 px-4 text-sm text-gray-900 border-r border-gray-200">' + formattedTime + ' | ' + formattedTransDate + '</td><td class="py-2 px-4 text-sm text-gray-900 text-right">' + window.salonFormatMoney(amount) + ' | ' + window.salonFormatMoney(tip) + ' | ' + window.salonFormatMoney(commission) + ' | ' + window.salonFormatMoney(total) + '</td></tr>';
    });
    var modalContent = '<div class="p-6 bg-white border border-gray-300 rounded-lg"><div class="text-center mb-6 border-b border-gray-300 pb-4"><h1 class="text-3xl font-bold text-gray-900 mb-2">Dons Nail Spa</h1><p class="text-sm text-gray-700">258 Hedrick St, Beckley, WV 25801</p><p class="text-sm text-gray-700">Phone: 681-2077114</p><p class="text-sm text-gray-700">Merchant ID (MID): 23420</p></div><div class="mb-4 border-b border-gray-200 pb-3 text-center"><h2 class="text-xl font-bold text-gray-900 mb-1">' + techName + ' Daily Report</h2><p class="text-sm text-gray-600">' + formattedDate + '</p></div><div class="mb-6 overflow-y-auto max-h-96 border border-gray-300 rounded-lg"><table class="w-full"><thead class="bg-gray-50 sticky top-0"><tr class="border-b border-gray-300"><th class="text-left py-3 px-4 text-sm font-semibold text-gray-500 uppercase border-r border-gray-300">' + techNameUpper + '</th><th class="text-right py-3 px-4 text-sm font-semibold text-gray-500 uppercase">SERVICE | TIP | COMMISSION | TOTAL</th></tr></thead><tbody>' + transactionsList + '</tbody></table></div><div class="border-t border-gray-200 pt-4 mb-6"><div class="flex justify-between items-center mb-2 pb-2 border-b border-gray-200"><span class="text-sm font-semibold text-gray-900">Total Service:</span><span class="text-sm font-semibold text-gray-900">' + window.salonFormatMoney(dateTotal) + '</span></div><div class="flex justify-between items-center mb-2 pb-2 border-b border-gray-200"><span class="text-sm font-semibold text-gray-900">Total Tip:</span><span class="text-sm font-semibold text-gray-900">' + window.salonFormatMoney(dateTip) + '</span></div><div class="flex justify-between items-center mb-2 pb-2 border-b border-gray-200"><span class="text-sm font-semibold text-gray-900">Total Commission:</span><span class="text-sm font-semibold text-gray-900">' + window.salonFormatMoney(dateCommission) + '</span></div><div class="flex justify-between items-center"><span class="text-sm font-semibold text-gray-900">Total:</span><span class="text-sm font-semibold text-gray-900">' + window.salonFormatMoney(dateGrandTotal) + '</span></div></div><div class="flex justify-end gap-3 border-t border-gray-200 pt-4"><button onclick="window.print()" class="px-6 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#004060] transition-colors font-medium">Print</button><button onclick="closeModal()" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">Close</button></div></div>';
    if (typeof openModal === 'function') {
        openModal(modalContent, 'medium');
    }
};
function salonPayoutRenderPayouts() {
    var tbody = document.getElementById('payoutTableBody');
    if (!tbody) return;
    if (!selectedTechnicianId) {
        if (!isTechnicianUser) {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-8 text-gray-500"><p>Please select a technician to view payouts</p></td></tr>';
        } else {
            tbody.innerHTML = '<tr><td colspan="6" class="text-center py-8 text-gray-500"><p>Loading payouts...</p></td></tr>';
        }
        return;
    }
    if (!filteredPayoutData || filteredPayoutData.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center py-8 text-gray-500">No payout data found for the selected date range</td></tr>';
        return;
    }
    var dailyTotals = {};
    var grandTotalService = 0, grandTip = 0, grandCommission = 0, grandTotal = 0;
    filteredPayoutData.forEach(function(t) {
        var dateKey = t.date;
        if (!dailyTotals[dateKey]) {
            dailyTotals[dateKey] = { date: dateKey, total_service: 0, tip: 0, commission: 0, total: 0 };
        }
        var amount = Number(t.amount || 0);
        var tip = Number(t.tip || 0);
        var commission = Number(t.commission || 0);
        var total = Number(t.total != null ? t.total : (tip + commission));
        dailyTotals[dateKey].total_service += amount;
        dailyTotals[dateKey].tip += tip;
        dailyTotals[dateKey].commission += commission;
        dailyTotals[dateKey].total += total;
        grandTotalService += amount;
        grandTip += tip;
        grandCommission += commission;
        grandTotal += total;
    });
    var sortedDates = Object.keys(dailyTotals).sort(function(a, b) { return new Date(b) - new Date(a); });
    var html = '';
    sortedDates.forEach(function(dateKey) {
        var daily = dailyTotals[dateKey];
        var date = new Date(daily.date);
        var monthNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        var month = monthNames[date.getMonth()];
        var day = date.getDate();
        var year = date.getFullYear();
        var formattedDate = month + ' ' + day + ', ' + year;
        html += '<tr class="border-b border-gray-100 hover:bg-gray-50"><td class="py-3 px-4 text-sm text-gray-900">' + formattedDate + '</td><td class="py-3 px-4 text-sm text-gray-900 text-right">' + window.salonFormatMoney(daily.total_service) + '</td><td class="py-3 px-4 text-sm text-gray-900 text-right">' + window.salonFormatMoney(daily.tip) + '</td><td class="py-3 px-4 text-sm font-semibold text-[#003047] text-right">' + window.salonFormatMoney(daily.commission) + '</td><td class="py-3 px-4 text-sm font-semibold text-gray-900 text-right">' + window.salonFormatMoney(daily.total) + '</td><td class="py-3 px-4"><button onclick="salonPayoutViewPayoutDetails(\'' + dateKey + '\')" class="px-3 py-1.5 bg-gray-500 text-white text-xs font-medium rounded hover:bg-gray-600 transition active:scale-95 flex items-center gap-1 ml-auto">Open</button></td></tr>';
    });
    html += '<tr class="bg-gray-50 border-t-2 border-gray-300"><td class="py-3 px-4 text-sm font-semibold text-gray-900">Total</td><td class="py-3 px-4 text-sm font-semibold text-gray-900 text-right">' + window.salonFormatMoney(grandTotalService) + '</td><td class="py-3 px-4 text-sm font-semibold text-gray-900 text-right">' + window.salonFormatMoney(grandTip) + '</td><td class="py-3 px-4 text-sm font-semibold text-[#003047] text-right">' + window.salonFormatMoney(grandCommission) + '</td><td class="py-3 px-4 text-sm font-semibold text-gray-900 text-right">' + window.salonFormatMoney(grandTotal) + '</td><td class="py-3 px-4"></td></tr>';
    tbody.innerHTML = html;
}
window.salonPayoutPrintReport = function() {
    if (!selectedTechnicianId) {
        var modalContent = '<div class="p-6"><div class="flex items-center justify-center mb-4"><div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center"><svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg></div></div><h3 class="text-xl font-bold text-gray-900 mb-2 text-center">Technician Required</h3><p class="text-gray-600 text-center mb-6">Please select a technician first before opening the report.</p><div class="flex justify-center"><button onclick="closeModal()" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">OK</button></div></div>';
        if (typeof openModal === 'function') openModal(modalContent, 'small');
        return;
    }
    if (!filteredPayoutData || filteredPayoutData.length === 0) {
        var modalContent = '<div class="p-6"><div class="flex items-center justify-center mb-4"><div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center"><svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div></div><h3 class="text-xl font-bold text-gray-900 mb-2 text-center">No Data Available</h3><p class="text-gray-600 text-center mb-6">No payout data available to print for the selected filters.</p><div class="flex justify-center"><button onclick="closeModal()" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">OK</button></div></div>';
        if (typeof openModal === 'function') openModal(modalContent, 'small');
        return;
    }
    var tech = techniciansList.find(function(t) { return t.id.toString() === selectedTechnicianId.toString(); });
    var techName = tech ? (tech.firstName + ' ' + tech.lastName).toUpperCase() : 'TECHNICIAN';
    var dateRangeText = '';
    if (dateRangeFrom && dateRangeTo) {
        var fromDate = new Date(dateRangeFrom);
        var toDate = new Date(dateRangeTo);
        var formatDate = function(date) {
            var day = String(date.getDate()).padStart(2, '0');
            var monthNames = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
            var month = monthNames[date.getMonth()];
            var year = date.getFullYear();
            return day + '-' + month + '-' + year;
        };
        if (dateRangeFrom === dateRangeTo) {
            dateRangeText = formatDate(fromDate);
        } else {
            dateRangeText = formatDate(fromDate) + ' to ' + formatDate(toDate);
        }
    }
    var dailyTotals = {};
    var totalAmount = 0, totalTip = 0, totalCommission = 0, totalGrand = 0;
    filteredPayoutData.forEach(function(t) {
        var dateKey = t.date;
        if (!dailyTotals[dateKey]) {
            dailyTotals[dateKey] = { date: dateKey, amount: 0, tip: 0, commission: 0, total: 0, transactions: [] };
        }
        var amount = Number(t.amount || 0);
        var tip = Number(t.tip || 0);
        var commission = Number(t.commission || 0);
        var total = Number(t.total != null ? t.total : (tip + commission));
        dailyTotals[dateKey].amount += amount;
        dailyTotals[dateKey].tip += tip;
        dailyTotals[dateKey].commission += commission;
        dailyTotals[dateKey].total += total;
        dailyTotals[dateKey].transactions.push(t);
        totalAmount += amount;
        totalTip += tip;
        totalCommission += commission;
        totalGrand += total;
    });
    var sortedDailyTotals = Object.values(dailyTotals).sort(function(a, b) { return new Date(b.date) - new Date(a.date); });
    var transactionsHtml = '';
    sortedDailyTotals.forEach(function(daily) {
        var firstTrans = daily.transactions.sort(function(a, b) {
            var timeA = a.time.split(':').map(Number), timeB = b.time.split(':').map(Number);
            return (timeA[0] * 60 + timeA[1]) - (timeB[0] * 60 + timeB[1]);
        })[0];
        var time = firstTrans ? firstTrans.time : '00:00';
        var parts = time.split(':');
        var formattedTime = parts[0].padStart(2, '0') + ':' + parts[1].padStart(2, '0');
        var date = new Date(daily.date);
        var month = String(date.getMonth() + 1).padStart(2, '0');
        var day = String(date.getDate()).padStart(2, '0');
        var year = String(date.getFullYear()).slice(-2);
        var formattedDate = month + '/' + day + '/' + year;
        transactionsHtml += '<tr class="border-b border-gray-200"><td class="py-2 px-4 text-sm text-gray-900 border-r border-gray-200">' + formattedTime + ' | ' + formattedDate + '</td><td class="py-2 px-4 text-sm text-gray-900 text-right">' + window.salonFormatMoney(daily.amount) + ' | ' + window.salonFormatMoney(daily.tip) + ' | ' + window.salonFormatMoney(daily.commission) + ' | ' + window.salonFormatMoney(daily.total) + '</td></tr>';
    });
    var modalContent = '<div class="p-6 bg-white border border-gray-300 rounded-lg"><div class="text-center mb-6 border-b border-gray-300 pb-4"><h1 class="text-3xl font-bold text-gray-900 mb-2">Dons Nail Spa</h1><p class="text-sm text-gray-700">258 Hedrick St, Beckley, WV 25801</p><p class="text-sm text-gray-700">681-2077114</p><p class="text-sm text-gray-700">MID: 23420</p></div><div class="mb-4 border-b border-gray-200 pb-3"><h2 class="text-xl font-bold text-gray-900 mb-1">' + techName + ' Daily Report</h2><p class="text-sm text-gray-600">' + dateRangeText + '</p></div><div class="mb-6 overflow-y-auto max-h-96 border border-gray-300 rounded-lg"><table class="w-full"><thead class="bg-gray-50 sticky top-0"><tr class="border-b border-gray-300"><th class="text-left py-3 px-4 text-sm font-semibold text-gray-900 border-r border-gray-300">' + techName + '</th><th class="text-right py-3 px-4 text-sm font-semibold text-gray-900">SERVICE | TIP | COMMISSION | TOTAL</th></tr></thead><tbody id="printTransactionsBody">' + transactionsHtml + '</tbody></table></div><div class="border border-gray-300 rounded-lg p-4 mb-6 bg-gray-50"><div class="flex justify-between items-center mb-2 pb-2 border-b border-gray-200"><span class="text-sm font-semibold text-gray-900">Total Service:</span><span class="text-sm font-semibold text-gray-900">' + window.salonFormatMoney(totalAmount) + '</span></div><div class="flex justify-between items-center mb-2 pb-2 border-b border-gray-200"><span class="text-sm font-semibold text-gray-900">Total Tip:</span><span class="text-sm font-semibold text-gray-900">' + window.salonFormatMoney(totalTip) + '</span></div><div class="flex justify-between items-center mb-2 pb-2 border-b border-gray-200"><span class="text-sm font-semibold text-gray-900">Total Commission:</span><span class="text-sm font-semibold text-gray-900">' + window.salonFormatMoney(totalCommission) + '</span></div><div class="flex justify-between items-center"><span class="text-sm font-semibold text-gray-900">Total:</span><span class="text-sm font-semibold text-gray-900">' + window.salonFormatMoney(totalGrand) + '</span></div></div><div class="flex justify-end gap-3 border-t border-gray-200 pt-4"><button onclick="window.print()" class="px-6 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#004060] transition-colors font-medium">Print</button><button onclick="closeModal()" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">Close</button></div></div>';
    if (typeof openModal === 'function') {
        openModal(modalContent, 'medium');
    }
};
async function salonPayoutLoadTechnicians() {
    try {
        if (window.salonPayoutBootstrap && Array.isArray(window.salonPayoutBootstrap.technicians)) {
            techniciansList = window.salonPayoutBootstrap.technicians || [];
            if (!isTechnicianUser) {
                salonPayoutPopulateTechnicianDropdown(techniciansList);
            }
            return;
        }
        var techRes = await fetch(base + '/users');
        var techData = await techRes.json();
        var technicians = techData.users || [];
        techniciansList = technicians.filter(function(t) { return t.role === 'technician'; });
        if (isTechnicianUser) {
            selectedTechnicianId = loggedInUserId ? String(loggedInUserId) : null;
        } else {
            salonPayoutPopulateTechnicianDropdown(techniciansList);
        }
    } catch (err) {
        console.error('Error loading technicians:', err);
    }
}
document.addEventListener('DOMContentLoaded', function() {
    if (window.salonPayoutBootstrap && typeof window.salonPayoutBootstrap === 'object') {
        var boot = window.salonPayoutBootstrap || {};
        if (boot.from) dateRangeFrom = String(boot.from);
        if (boot.to) dateRangeTo = String(boot.to);
        if (boot.selectedTechnicianId) selectedTechnicianId = String(boot.selectedTechnicianId);
        if (Array.isArray(boot.transactions)) {
            payoutData = (boot.transactions || []).slice().sort(function(a, b) {
                var dateA = new Date(a.date), dateB = new Date(b.date);
                if (dateB.getTime() !== dateA.getTime()) return dateB - dateA;
                var timeA = (a.time || '00:00').split(':').map(Number), timeB = (b.time || '00:00').split(':').map(Number);
                var timeAValue = timeA[0] * 60 + timeA[1], timeBValue = timeB[0] * 60 + timeB[1];
                return timeBValue - timeAValue;
            });
            filteredPayoutData = payoutData;
        }
        var fromInputBoot = document.getElementById('dateRangeFrom');
        var toInputBoot = document.getElementById('dateRangeTo');
        if (fromInputBoot && dateRangeFrom) fromInputBoot.value = dateRangeFrom;
        if (toInputBoot && dateRangeTo) toInputBoot.value = dateRangeTo;
        if (!isTechnicianUser && selectedTechnicianId) {
            var dropdownBoot = document.getElementById('technicianFilter');
            if (dropdownBoot) dropdownBoot.value = selectedTechnicianId;
        }
        if (boot.datetype) {
            setTimeout(function() { salonPayoutSetActivePreset(String(boot.datetype)); }, 50);
        }
    }
    var urlParams = new URLSearchParams(window.location.search);
    var urlFrom = urlParams.get('from');
    var urlTo = urlParams.get('to');
    var urlDateType = urlParams.get('datetype');
    var today = new Date();
    if (!dateRangeFrom) dateRangeFrom = salonPayoutFormatYmd(today);
    if (!dateRangeTo) dateRangeTo = salonPayoutFormatYmd(today);
    var fromInput = document.getElementById('dateRangeFrom');
    var toInput = document.getElementById('dateRangeTo');
    if (fromInput) fromInput.value = dateRangeFrom;
    if (toInput) toInput.value = dateRangeTo;
    if (!urlFrom || !urlTo) {
        if (!isTechnicianUser) {
            salonPayoutUpdateURLWithDateRange(dateRangeFrom, dateRangeTo, 'today');
        }
        setTimeout(function() {
            salonPayoutSetActivePreset('today');
        }, 50);
    } else if (urlDateType) {
        setTimeout(function() {
            salonPayoutSetActivePreset(urlDateType);
        }, 50);
    } else {
        setTimeout(function() {
            salonPayoutSetActivePreset(null);
        }, 50);
    }
    salonPayoutLoadTechnicians().then(function() {
        if (isTechnicianUser) {
            selectedTechnicianId = loggedInUserId ? String(loggedInUserId) : selectedTechnicianId;
            if (payoutData && payoutData.length) {
                salonPayoutRenderPayouts();
            } else {
                salonPayoutFetchPayouts();
            }
        } else {
            var urlTech = urlParams.get('technician');
            if (urlTech) {
                selectedTechnicianId = urlTech;
                var dropdown = document.getElementById('technicianFilter');
                if (dropdown) dropdown.value = urlTech;
                if (payoutData && payoutData.length) {
                    salonPayoutRenderPayouts();
                } else {
                    salonPayoutFetchPayouts();
                }
            } else if (payoutData && payoutData.length) {
                salonPayoutRenderPayouts();
            }
        }
    });
    window.addEventListener('popstate', function(event) {
        var urlParams = new URLSearchParams(window.location.search);
        var urlTech = urlParams.get('technician');
        var urlDateType = urlParams.get('datetype');
        var urlFrom = urlParams.get('from');
        var urlTo = urlParams.get('to');
        if (urlTech) {
            selectedTechnicianId = urlTech;
            var dropdown = document.getElementById('technicianFilter');
            if (dropdown) dropdown.value = urlTech;
        } else {
            if (!isTechnicianUser) {
                selectedTechnicianId = null;
                var dropdown = document.getElementById('technicianFilter');
                if (dropdown) dropdown.value = '';
            }
        }
        if (urlDateType) {
            salonPayoutSetActivePreset(urlDateType);
        } else if (!urlFrom || !urlTo) {
            salonPayoutSetActivePreset('today');
        } else {
            salonPayoutSetActivePreset(null);
        }
        salonPayoutFetchPayouts();
    });
});
})();
</script>
@endpush
@endsection

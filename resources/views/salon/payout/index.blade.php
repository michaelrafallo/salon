@extends('layouts.salon')

@section('content')
@php
    $isTechnician = session('salon_role', 'admin') === 'technician';
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
                        <tr><td colspan="6" class="text-center py-8 text-gray-500"><p>Please select a technician to view payouts</p></td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
@push('scripts')
<script>
(function() {
var base = window.salonJsonBase || '{{ url("json") }}';
var isTechnicianUser = {{ $isTechnician ? 'true' : 'false' }};
var payoutData = [], filteredPayoutData = [], dateRangeFrom = null, dateRangeTo = null, selectedTechnicianId = isTechnicianUser ? '1' : null, techniciansList = [];
async function salonPayoutFetchPayouts() {
    try {
        var bookingsRes = await fetch(base + '/booking.json');
        var techRes = await fetch(base + '/users.json');
        var bookingsData = await bookingsRes.json();
        var techniciansData = await techRes.json();
        var bookings = bookingsData.bookings || [];
        var technicians = techniciansData.users || [];
        techniciansList = technicians.filter(function(t) { return t.role === 'technician'; });
        if (!selectedTechnicianId) {
            var urlParams = new URLSearchParams(window.location.search);
            var urlTech = urlParams.get('technician');
            if (urlTech) selectedTechnicianId = urlTech;
        }
        var technicianMap = {};
        techniciansList.forEach(function(t) {
            var fullName = t.firstName + ' ' + t.lastName;
            technicianMap[fullName] = t;
        });
        salonPayoutPopulateTechnicianDropdown(techniciansList);
        if (!selectedTechnicianId) {
            payoutData = [];
            filteredPayoutData = [];
            salonPayoutRenderPayouts();
            return;
        }
        var transactions = [];
        bookings.forEach(function(booking) {
            if (!booking.technician || !booking.bookingDate) return;
            var techs = Array.isArray(booking.technician) ? booking.technician : [booking.technician];
            var total = parseFloat(booking.total) || 0;
            var tip = parseFloat(booking.tip) || (total * 0.15);
            techs.forEach(function(techName) {
                var tech = technicianMap[techName];
                if (!tech || tech.id.toString() !== selectedTechnicianId.toString()) return;
                transactions.push({ time: booking.bookingTime || '00:00', date: booking.bookingDate, amount: total, tip: tip });
            });
        });
        payoutData = transactions.sort(function(a, b) {
            var dateA = new Date(a.date), dateB = new Date(b.date);
            if (dateB.getTime() !== dateA.getTime()) return dateB - dateA;
            var timeA = a.time.split(':').map(Number), timeB = b.time.split(':').map(Number);
            var timeAValue = timeA[0] * 60 + timeA[1], timeBValue = timeB[0] * 60 + timeB[1];
            return timeBValue - timeAValue;
        });
        var today = new Date(), december1 = new Date(today.getFullYear(), 11, 1);
        var sampleTemplates = [
            { time: '19:23', amount: 70.00, tip: 0.00 }, { time: '17:28', amount: 45.00, tip: 10.00 },
            { time: '16:34', amount: 50.00, tip: 0.00 }, { time: '15:41', amount: 60.00, tip: 0.00 },
            { time: '14:59', amount: 45.00, tip: 0.00 }, { time: '13:31', amount: 70.00, tip: 0.00 },
            { time: '12:00', amount: 145.00, tip: 0.00 }, { time: '11:15', amount: 55.00, tip: 8.25 },
            { time: '10:30', amount: 80.00, tip: 12.00 }, { time: '09:45', amount: 40.00, tip: 6.00 }
        ];
        var currentDate = new Date(december1);
        while (currentDate <= today) {
            var dateStr = currentDate.toISOString().split('T')[0];
            var hasData = payoutData.some(function(t) { return t.date === dateStr; });
            if (!hasData && Math.random() > 0.2) {
                var numTrans = Math.floor(Math.random() * 5) + 1;
                for (var i = 0; i < numTrans; i++) {
                    var template = sampleTemplates[Math.floor(Math.random() * sampleTemplates.length)];
                    var variation = 0.8 + (Math.random() * 0.4);
                    var amount = Math.round(template.amount * variation * 100) / 100;
                    var tip = template.tip > 0 ? Math.round(template.amount * 0.15 * variation * 100) / 100 : 0;
                    payoutData.push({ time: template.time, date: dateStr, amount: amount, tip: tip });
                }
            }
            currentDate.setDate(currentDate.getDate() + 1);
        }
        payoutData = payoutData.sort(function(a, b) {
            var dateA = new Date(a.date), dateB = new Date(b.date);
            if (dateB.getTime() !== dateA.getTime()) return dateB - dateA;
            var timeA = a.time.split(':').map(Number), timeB = b.time.split(':').map(Number);
            var timeAValue = timeA[0] * 60 + timeA[1], timeBValue = timeB[0] * 60 + timeB[1];
            return timeBValue - timeAValue;
        });
        var urlParams = new URLSearchParams(window.location.search);
        var urlFrom = urlParams.get('from'), urlTo = urlParams.get('to'), urlDateType = urlParams.get('datetype');
        if (urlFrom && urlTo) {
            dateRangeFrom = urlFrom;
            dateRangeTo = urlTo;
            var fromEl = document.getElementById('dateRangeFrom'), toEl = document.getElementById('dateRangeTo');
            if (fromEl) fromEl.value = dateRangeFrom;
            if (toEl) toEl.value = dateRangeTo;
            if (urlDateType) {
                setTimeout(function() {
                    document.querySelectorAll('.date-preset-btn').forEach(function(btn) {
                        btn.classList.remove('bg-[#003047]', 'text-white', 'border-[#003047]');
                        btn.classList.add('bg-white', 'text-gray-700', 'border-gray-300');
                    });
                    var activeBtn = document.querySelector('button[onclick*="setDateRange(\'' + urlDateType + '\'"]');
                    if (activeBtn) {
                        activeBtn.classList.remove('bg-white', 'text-gray-700', 'border-gray-300');
                        activeBtn.classList.add('bg-[#003047]', 'text-white', 'border-[#003047]');
                    }
                }, 100);
            }
        } else {
            var today = new Date();
            dateRangeFrom = today.toISOString().split('T')[0];
            dateRangeTo = today.toISOString().split('T')[0];
            var fromEl = document.getElementById('dateRangeFrom'), toEl = document.getElementById('dateRangeTo');
            if (fromEl) fromEl.value = dateRangeFrom;
            if (toEl) toEl.value = dateRangeTo;
            salonPayoutUpdateURLWithDateRange(dateRangeFrom, dateRangeTo, 'today');
            setTimeout(function() {
                var todayBtn = document.querySelector('button[onclick*="today"]');
                if (todayBtn) {
                    todayBtn.classList.remove('bg-white', 'text-gray-700', 'border-gray-300');
                    todayBtn.classList.add('bg-[#003047]', 'text-white', 'border-[#003047]');
                }
            }, 100);
        }
        salonPayoutApplyDateRangeFilter(true);
    } catch (err) {
        console.error('Error fetching payouts:', err);
        var today = new Date(), december1 = new Date(today.getFullYear(), 11, 1);
        var sampleTemplates = [
            { time: '19:23', amount: 70.00, tip: 0.00 }, { time: '17:28', amount: 45.00, tip: 10.00 },
            { time: '16:34', amount: 50.00, tip: 0.00 }, { time: '15:41', amount: 60.00, tip: 0.00 },
            { time: '14:59', amount: 45.00, tip: 0.00 }, { time: '13:31', amount: 70.00, tip: 0.00 },
            { time: '12:00', amount: 145.00, tip: 0.00 }, { time: '11:15', amount: 55.00, tip: 8.25 },
            { time: '10:30', amount: 80.00, tip: 12.00 }, { time: '09:45', amount: 40.00, tip: 6.00 }
        ];
        payoutData = [];
        var currentDate = new Date(december1);
        while (currentDate <= today) {
            var dateStr = currentDate.toISOString().split('T')[0];
            if (Math.random() > 0.2) {
                var numTrans = Math.floor(Math.random() * 5) + 1;
                for (var i = 0; i < numTrans; i++) {
                    var template = sampleTemplates[Math.floor(Math.random() * sampleTemplates.length)];
                    var variation = 0.8 + (Math.random() * 0.4);
                    var amount = Math.round(template.amount * variation * 100) / 100;
                    var tip = template.tip > 0 ? Math.round(template.amount * 0.15 * variation * 100) / 100 : 0;
                    payoutData.push({ time: template.time, date: dateStr, amount: amount, tip: tip });
                }
            }
            currentDate.setDate(currentDate.getDate() + 1);
        }
        payoutData = payoutData.sort(function(a, b) {
            var dateA = new Date(a.date), dateB = new Date(b.date);
            if (dateB.getTime() !== dateA.getTime()) return dateB - dateA;
            var timeA = a.time.split(':').map(Number), timeB = b.time.split(':').map(Number);
            var timeAValue = timeA[0] * 60 + timeA[1], timeBValue = timeB[0] * 60 + timeB[1];
            return timeBValue - timeAValue;
        });
        filteredPayoutData = payoutData;
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
    dateRangeFrom = fromDate.toISOString().split('T')[0];
    dateRangeTo = toDate.toISOString().split('T')[0];
    var fromEl = document.getElementById('dateRangeFrom'), toEl = document.getElementById('dateRangeTo');
    if (fromEl) fromEl.value = dateRangeFrom;
    if (toEl) toEl.value = dateRangeTo;
    salonPayoutUpdateURLWithDateRange(dateRangeFrom, dateRangeTo, range);
    document.querySelectorAll('.date-preset-btn').forEach(function(btn) {
        btn.classList.remove('bg-[#003047]', 'text-white', 'border-[#003047]');
        btn.classList.add('bg-white', 'text-gray-700', 'border-gray-300');
    });
    var activeButton = buttonElement || document.querySelector('button[onclick*="setDateRange(\'' + range + '\'")]');
    if (activeButton) {
        activeButton.classList.remove('bg-white', 'text-gray-700', 'border-gray-300');
        activeButton.classList.add('bg-[#003047]', 'text-white', 'border-[#003047]');
    }
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
        if (!selectedTechnicianId) selectedTechnicianId = '1';
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
    if (dateRangeFrom && dateRangeTo && payoutData.length > 0) {
        var fromDate = new Date(dateRangeFrom);
        var toDate = new Date(dateRangeTo);
        toDate.setHours(23, 59, 59, 999);
        filteredPayoutData = payoutData.filter(function(t) {
            var tDate = new Date(t.date);
            return tDate >= fromDate && tDate <= toDate;
        });
    } else {
        filteredPayoutData = payoutData;
    }
    salonPayoutRenderPayouts();
}
window.salonPayoutViewPayoutDetails = function(dateKey) {
    var dateTransactions = filteredPayoutData.filter(function(t) { return t.date === dateKey; });
    if (dateTransactions.length === 0) {
        alert('No transactions found for this date');
        return;
    }
    var dateTotal = 0, dateTip = 0;
    dateTransactions.forEach(function(t) {
        dateTotal += t.amount;
        dateTip += t.tip;
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
        transactionsList += '<tr class="border-b border-gray-200"><td class="py-2 px-4 text-sm text-gray-900 border-r border-gray-200">' + formattedTime + ' | ' + formattedTransDate + '</td><td class="py-2 px-4 text-sm text-gray-900 text-right">$' + transaction.amount.toFixed(2) + ' | $' + transaction.tip.toFixed(2) + '</td></tr>';
    });
    var modalContent = '<div class="p-6 bg-white border border-gray-300 rounded-lg"><div class="text-center mb-6 border-b border-gray-300 pb-4"><h1 class="text-3xl font-bold text-gray-900 mb-2">Dons Nail Spa</h1><p class="text-sm text-gray-700">258 Hedrick St, Beckley, WV 25801</p><p class="text-sm text-gray-700">Phone: 681-2077114</p><p class="text-sm text-gray-700">Merchant ID (MID): 23420</p></div><div class="mb-4 border-b border-gray-200 pb-3 text-center"><h2 class="text-xl font-bold text-gray-900 mb-1">' + techName + ' Daily Report</h2><p class="text-sm text-gray-600">' + formattedDate + '</p></div><div class="mb-6 overflow-y-auto max-h-96 border border-gray-300 rounded-lg"><table class="w-full"><thead class="bg-gray-50 sticky top-0"><tr class="border-b border-gray-300"><th class="text-left py-3 px-4 text-sm font-semibold text-gray-500 uppercase border-r border-gray-300">' + techNameUpper + '</th><th class="text-right py-3 px-4 text-sm font-semibold text-gray-500 uppercase">AMOUNT</th></tr></thead><tbody>' + transactionsList + '</tbody></table></div><div class="border-t border-gray-200 pt-4 mb-6"><div class="flex justify-between items-center mb-2 pb-2 border-b border-gray-200"><span class="text-sm font-semibold text-gray-900">Total Amount:</span><span class="text-sm font-semibold text-gray-900">$' + dateTotal.toFixed(2) + '</span></div><div class="flex justify-between items-center"><span class="text-sm font-semibold text-gray-900">Total Tip:</span><span class="text-sm font-semibold text-gray-900">$' + dateTip.toFixed(2) + '</span></div></div><div class="flex justify-end gap-3 border-t border-gray-200 pt-4"><button onclick="window.print()" class="px-6 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#004060] transition-colors font-medium">Print</button><button onclick="closeModal()" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">Close</button></div></div>';
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
    var grandTotal = 0, grandTip = 0, grandCommission = 0, grandTotalTipCommission = 0;
    filteredPayoutData.forEach(function(t) {
        var dateKey = t.date;
        if (!dailyTotals[dateKey]) {
            dailyTotals[dateKey] = { date: dateKey, total: 0, tip: 0, commission: 0, totalTipCommission: 0 };
        }
        dailyTotals[dateKey].total += t.amount;
        dailyTotals[dateKey].tip += t.tip;
        grandTotal += t.amount;
        grandTip += t.tip;
    });
    Object.keys(dailyTotals).forEach(function(dateKey) {
        dailyTotals[dateKey].commission = dailyTotals[dateKey].total * 0.30;
        dailyTotals[dateKey].totalTipCommission = dailyTotals[dateKey].tip + dailyTotals[dateKey].commission;
        grandCommission += dailyTotals[dateKey].commission;
        grandTotalTipCommission += dailyTotals[dateKey].totalTipCommission;
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
        html += '<tr class="border-b border-gray-100 hover:bg-gray-50"><td class="py-3 px-4 text-sm text-gray-900">' + formattedDate + '</td><td class="py-3 px-4 text-sm text-gray-900 text-right">$' + daily.total.toFixed(2) + '</td><td class="py-3 px-4 text-sm text-gray-900 text-right">$' + daily.tip.toFixed(2) + '</td><td class="py-3 px-4 text-sm font-semibold text-[#003047] text-right">$' + daily.commission.toFixed(2) + '</td><td class="py-3 px-4 text-sm font-semibold text-gray-900 text-right">$' + daily.totalTipCommission.toFixed(2) + '</td><td class="py-3 px-4"><button onclick="salonPayoutViewPayoutDetails(\'' + dateKey + '\')" class="px-3 py-1.5 bg-gray-500 text-white text-xs font-medium rounded hover:bg-gray-600 transition active:scale-95 flex items-center gap-1 ml-auto">Open</button></td></tr>';
    });
    html += '<tr class="bg-gray-50 border-t-2 border-gray-300"><td class="py-3 px-4 text-sm font-semibold text-gray-900">Total</td><td class="py-3 px-4 text-sm font-semibold text-gray-900 text-right">$' + grandTotal.toFixed(2) + '</td><td class="py-3 px-4 text-sm font-semibold text-gray-900 text-right">$' + grandTip.toFixed(2) + '</td><td class="py-3 px-4 text-sm font-semibold text-[#003047] text-right">$' + grandCommission.toFixed(2) + '</td><td class="py-3 px-4 text-sm font-semibold text-gray-900 text-right">$' + grandTotalTipCommission.toFixed(2) + '</td><td class="py-3 px-4"></td></tr>';
    tbody.innerHTML = html;
}
window.salonPayoutPrintReport = function() {
    if (!selectedTechnicianId) {
        alert('Please select a technician first');
        return;
    }
    if (!filteredPayoutData || filteredPayoutData.length === 0) {
        alert('No payout data to print');
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
    var totalAmount = 0, totalTip = 0;
    filteredPayoutData.forEach(function(t) {
        var dateKey = t.date;
        if (!dailyTotals[dateKey]) {
            dailyTotals[dateKey] = { date: dateKey, amount: 0, tip: 0, transactions: [] };
        }
        dailyTotals[dateKey].amount += t.amount;
        dailyTotals[dateKey].tip += t.tip;
        dailyTotals[dateKey].transactions.push(t);
        totalAmount += t.amount;
        totalTip += t.tip;
    });
    var totalCommission = totalAmount * 0.30;
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
        transactionsHtml += '<tr class="border-b border-gray-200"><td class="py-2 px-4 text-sm text-gray-900 border-r border-gray-200">' + formattedTime + ' | ' + formattedDate + '</td><td class="py-2 px-4 text-sm text-gray-900 text-right">$' + daily.amount.toFixed(2) + ' | $' + daily.tip.toFixed(2) + '</td></tr>';
    });
    var modalContent = '<div class="p-6 bg-white border border-gray-300 rounded-lg"><div class="text-center mb-6 border-b border-gray-300 pb-4"><h1 class="text-3xl font-bold text-gray-900 mb-2">Dons Nail Spa</h1><p class="text-sm text-gray-700">258 Hedrick St, Beckley, WV 25801</p><p class="text-sm text-gray-700">681-2077114</p><p class="text-sm text-gray-700">MID: 23420</p></div><div class="mb-4 border-b border-gray-200 pb-3"><h2 class="text-xl font-bold text-gray-900 mb-1">' + techName + ' Daily Report</h2><p class="text-sm text-gray-600">' + dateRangeText + '</p></div><div class="mb-6 overflow-y-auto max-h-96 border border-gray-300 rounded-lg"><table class="w-full"><thead class="bg-gray-50 sticky top-0"><tr class="border-b border-gray-300"><th class="text-left py-3 px-4 text-sm font-semibold text-gray-900 border-r border-gray-300">' + techName + '</th><th class="text-right py-3 px-4 text-sm font-semibold text-gray-900">AMOUNT</th></tr></thead><tbody id="printTransactionsBody">' + transactionsHtml + '</tbody></table></div><div class="border border-gray-300 rounded-lg p-4 mb-6 bg-gray-50"><div class="flex justify-between items-center mb-2 pb-2 border-b border-gray-200"><span class="text-sm font-semibold text-gray-900">Total Amount:</span><span class="text-sm font-semibold text-gray-900">$' + totalAmount.toFixed(2) + '</span></div><div class="flex justify-between items-center mb-2 pb-2 border-b border-gray-200"><span class="text-sm font-semibold text-gray-900">Total Tip:</span><span class="text-sm font-semibold text-gray-900">$' + totalTip.toFixed(2) + '</span></div><div class="flex justify-between items-center"><span class="text-sm font-semibold text-gray-900">Total Commission:</span><span class="text-sm font-semibold text-gray-900">$' + totalCommission.toFixed(2) + '</span></div></div><div class="flex justify-end gap-3 border-t border-gray-200 pt-4"><button onclick="window.print()" class="px-6 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#004060] transition-colors font-medium">Print</button><button onclick="closeModal()" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">Close</button></div></div>';
    if (typeof openModal === 'function') {
        openModal(modalContent, 'medium');
    }
};
async function salonPayoutLoadTechnicians() {
    try {
        var techRes = await fetch(base + '/users.json');
        var techData = await techRes.json();
        var technicians = techData.users || [];
        techniciansList = technicians.filter(function(t) { return t.role === 'technician'; });
        if (isTechnicianUser) {
            selectedTechnicianId = '1';
        } else {
            salonPayoutPopulateTechnicianDropdown(techniciansList);
        }
    } catch (err) {
        console.error('Error loading technicians:', err);
    }
}
document.addEventListener('DOMContentLoaded', function() {
    var urlParams = new URLSearchParams(window.location.search);
    var urlFrom = urlParams.get('from');
    var urlTo = urlParams.get('to');
    var today = new Date();
    dateRangeFrom = today.toISOString().split('T')[0];
    dateRangeTo = today.toISOString().split('T')[0];
    var fromInput = document.getElementById('dateRangeFrom');
    var toInput = document.getElementById('dateRangeTo');
    if (fromInput) fromInput.value = dateRangeFrom;
    if (toInput) toInput.value = dateRangeTo;
    if (!urlFrom || !urlTo) {
        if (!isTechnicianUser) {
            salonPayoutUpdateURLWithDateRange(dateRangeFrom, dateRangeTo, 'today');
        }
        setTimeout(function() {
            var todayBtn = document.querySelector('button[onclick*="setDateRange(\'today\'"]');
            if (todayBtn) {
                document.querySelectorAll('.date-preset-btn').forEach(function(btn) {
                    btn.classList.remove('bg-[#003047]', 'text-white', 'border-[#003047]');
                    btn.classList.add('bg-white', 'text-gray-700', 'border-gray-300');
                });
                todayBtn.classList.remove('bg-white', 'text-gray-700', 'border-gray-300');
                todayBtn.classList.add('bg-[#003047]', 'text-white', 'border-[#003047]');
            }
        }, 100);
    }
    salonPayoutLoadTechnicians().then(function() {
        if (isTechnicianUser) {
            selectedTechnicianId = '1';
            salonPayoutFetchPayouts();
        } else {
            var urlTech = urlParams.get('technician');
            if (urlTech) {
                selectedTechnicianId = urlTech;
                var dropdown = document.getElementById('technicianFilter');
                if (dropdown) dropdown.value = urlTech;
                salonPayoutFetchPayouts();
            }
        }
    });
    window.addEventListener('popstate', function(event) {
        var urlParams = new URLSearchParams(window.location.search);
        var urlTech = urlParams.get('technician');
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
        salonPayoutFetchPayouts();
    });
});
})();
</script>
@endpush
@endsection

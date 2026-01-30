@extends('layouts.salon')

@section('content')
@php
    $bookingUrl = route('salon.booking.index');
    $payUrl = route('salon.booking.pay');
@endphp
<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Tickets</h1>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-1">
                    <button id="gridViewBtn" onclick="salonTicketsToggleView('grid')" class="p-2 rounded-md hover:bg-white transition active:scale-95">
                        <svg class="w-5 h-5 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    </button>
                    <button id="listViewBtn" onclick="salonTicketsToggleView('list')" class="p-2 rounded-md hover:bg-white transition active:scale-95">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    </button>
                </div>
                <a href="{{ $bookingUrl }}" class="px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm sm:text-base active:scale-95 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Create Ticket
                </a>
            </div>
        </div>
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-6 border-b border-gray-200">
                <button onclick="salonTicketsFilter('unpaid')" id="filterUnpaid" class="filter-tab px-1 py-3 text-sm font-medium text-gray-900 border-b-2 border-[#003047] transition">Unpaid</button>
                <button onclick="salonTicketsFilter('paid')" id="filterPaid" class="filter-tab px-1 py-3 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 transition">Paid</button>
                <button onclick="salonTicketsFilter('cancelled')" id="filterCancelled" class="filter-tab px-1 py-3 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 transition">Cancelled</button>
                <button onclick="salonTicketsFilter('refunded')" id="filterRefunded" class="filter-tab px-1 py-3 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 transition">Refunded</button>
            </div>
            <div class="relative max-w-md">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" id="ticketSearchInput" placeholder="Search customers" oninput="salonTicketsSearch(this.value)" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-base">
            </div>
        </div>
        <div id="gridView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6"></div>
        <div id="listView" class="hidden">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-16">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned Technicians</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Appointment</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Details</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="listViewBody" class="bg-white divide-y divide-gray-200"></tbody>
                </table>
            </div>
        </div>
        <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div id="ticketsResultsCounter" class="text-sm text-gray-600"></div>
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-600">Show:</label>
                <select id="perPageSelect" onchange="salonTicketsChangePerPage(this.value)" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-sm bg-white cursor-pointer">
                    <option value="15">15</option><option value="25">25</option><option value="50">50</option><option value="100">100</option><option value="250">250</option><option value="500">500</option><option value="all">All</option>
                </select>
                <span class="text-sm text-gray-600">per page</span>
            </div>
        </div>
        <div id="ticketsPagination" class="mt-4 flex justify-center"></div>
    </div>
</main>
@push('scripts')
<script>
(function() {
var base = window.salonJsonBase || '{{ url("json") }}';
var bookingUrl = '{{ $bookingUrl }}';
var payUrl = '{{ $payUrl }}';
var allCustomers = [], allAppointments = [], allTechnicians = [], allPayments = [], allMergedData = [], ticketsData = [];
var PAGE_SIZE = 15, currentPage = 1, totalPages = 1, currentSearchTerm = '', currentStatusFilter = 'unpaid';
var currentView = localStorage.getItem('ticketsView') || 'grid';
var durationInterval = null;
var availableTechnicians = [];
var originalTechnicianOrder = [];
var assignedTechnicianIds = [];
var currentCustomerId = null;
var currentCustomerName = '';
var technicianSearchTerm = '';
var colorClasses = [
    { bg: 'bg-[#e6f0f3]', text: 'text-[#003047]' }, { bg: 'bg-purple-100', text: 'text-purple-600' },
    { bg: 'bg-teal-100', text: 'text-teal-600' }, { bg: 'bg-indigo-100', text: 'text-indigo-600' },
    { bg: 'bg-rose-100', text: 'text-rose-600' }, { bg: 'bg-blue-100', text: 'text-blue-600' },
    { bg: 'bg-amber-100', text: 'text-amber-600' }, { bg: 'bg-green-100', text: 'text-green-600' }
];
function getInitials(c) { return c.initials || ((c.firstName||'')[0] + (c.lastName||'')[0]).toUpperCase(); }
function getTechnicianInitials(t) { return t.initials || ((t.firstName||'')[0] + (t.lastName||'')[0]).toUpperCase(); }
function getTechnicianNames(ids) {
    if (!ids || !Array.isArray(ids) || !ids.length) return 'Not assigned';
    var names = ids.map(function(id) {
        var techId = typeof id === 'string' ? parseInt(id, 10) : id;
        var tech = allTechnicians.find(function(t) {
            var tId = typeof t.id === 'string' ? parseInt(t.id, 10) : t.id;
            return tId === techId;
        });
        return tech ? tech.firstName + ' ' + tech.lastName : null;
    }).filter(Boolean);
    return names.length ? names.join(', ') : 'Not assigned';
}
function renderTechniciansList(ids) {
    if (!ids || !Array.isArray(ids) || !ids.length) return '<span class="text-sm text-gray-400">Not assigned</span>';
    var techs = ids.map(function(id) {
        var techId = typeof id === 'string' ? parseInt(id, 10) : id;
        return allTechnicians.find(function(t) {
            var tId = typeof t.id === 'string' ? parseInt(t.id, 10) : t.id;
            return tId === techId;
        });
    }).filter(Boolean);
    if (!techs.length) return '<span class="text-sm text-gray-400">Not assigned</span>';
    var html = techs.map(function(tech, i) {
        var inits = getTechnicianInitials(tech), name = tech.firstName + ' ' + tech.lastName;
        var c = colorClasses[i % colorClasses.length];
        var photo = tech.profilePhoto || tech.avatar || tech.image || null;
        return '<div class="flex items-center gap-2 mb-1 last:mb-0">' +
            (photo ? '<img src="' + photo + '" alt="' + name + '" class="w-8 h-8 rounded-full object-cover flex-shrink-0 border-2 border-white shadow-sm" onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'flex\';"><div class="w-8 h-8 ' + c.bg + ' rounded-full flex items-center justify-center flex-shrink-0 hidden"><span class="text-xs font-bold ' + c.text + '">' + inits + '</span></div>' :
            '<div class="w-8 h-8 ' + c.bg + ' rounded-full flex items-center justify-center flex-shrink-0 border-2 border-white shadow-sm"><span class="text-xs font-bold ' + c.text + '">' + inits + '</span></div>') +
            '<span class="text-sm text-gray-900">' + name + '</span></div>';
    }).join('');
    return '<div class="flex flex-col">' + html + '</div>';
}
function mergeAppointmentsWithCustomers() {
    allMergedData = allAppointments.map(function(apt) {
        var customer = allCustomers.find(function(c) { return c.id === apt.customer_id; });
        if (!customer) return null;
        var type = apt.appointment === 'walk-in' ? 'Walk-In' : apt.appointment === 'booked' ? 'Booked' : (apt.appointment || 'Walk-In');
        var payment = allPayments.find(function(p) {
            if (p.appointmentId && p.appointmentId.toString() === apt.id.toString()) return true;
            var customerName = customer.firstName + ' ' + customer.lastName;
            if (p.customerName === customerName) return true;
            if (p.bookingId && apt.id.toString() === p.bookingId.toString()) return true;
            return false;
        });
        return Object.assign({}, customer, {
            appointmentId: apt.id,
            id: apt.id,
            appointment: type,
            status: apt.status || 'unpaid',
            created_at: apt.created_at,
            appointment_datetime: apt.appointment_datetime || apt.created_at,
            assigned_technician: apt.assigned_technician || [],
            services: apt.services || [],
            payment: payment || null,
            customer_id: apt.customer_id
        });
    }).filter(Boolean);
    allMergedData = allMergedData.filter(function(item) {
        if (!item || !item.status) return false;
        var status = item.status.toLowerCase().trim();
        var validStatuses = ['unpaid', 'in-progress', 'completed', 'waiting', 'paid', 'cancelled', 'canceled', 'refunded', 'closed'];
        return validStatuses.indexOf(status) >= 0;
    });
}
function parseDate(dateString) {
    if (!dateString) return null;
    try {
        var date = new Date(dateString);
        if (!isNaN(date.getTime())) return date;
        if (dateString.indexOf(' ') >= 0 && dateString.indexOf('T') < 0) {
            date = new Date(dateString.replace(' ', 'T'));
            if (!isNaN(date.getTime())) return date;
        }
        var match = dateString.match(/(\d{4})-(\d{2})-(\d{2})[T\s]?(\d{2}):(\d{2}):?(\d{2})?/);
        if (match) {
            var year = parseInt(match[1]), month = parseInt(match[2]) - 1, day = parseInt(match[3]);
            var hour = parseInt(match[4]) || 0, minute = parseInt(match[5]) || 0, second = parseInt(match[6]) || 0;
            date = new Date(year, month, day, hour, minute, second);
            if (!isNaN(date.getTime())) return date;
        }
        match = dateString.match(/(\d{4})-(\d{2})-(\d{2})/);
        if (match) {
            date = new Date(parseInt(match[1]), parseInt(match[2]) - 1, parseInt(match[3]));
            if (!isNaN(date.getTime())) return date;
        }
        return null;
    } catch (e) {
        console.error('Error parsing date:', dateString, e);
        return null;
    }
}
function getTimeStarted(customer) {
    var startTime = customer.appointment_datetime || customer.created_at;
    if (!startTime) return 'N/A';
    var date = parseDate(startTime);
    if (!date) return 'N/A';
    try {
        return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
    } catch (e) {
        return 'N/A';
    }
}
function calculateDuration(startTime) {
    if (!startTime) return '00:00:00';
    var start = parseDate(startTime);
    if (!start || isNaN(start.getTime())) return '00:00:00';
    try {
        var now = new Date();
        var diff = Math.max(0, Math.floor((now - start) / 1000));
        var hours = Math.floor(diff / 3600);
        var minutes = Math.floor((diff % 3600) / 60);
        var seconds = diff % 60;
        return String(hours).padStart(2, '0') + ':' + String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
    } catch (e) {
        return '00:00:00';
    }
}
function updateDurationCounters() {
    var counters = document.querySelectorAll('.duration-counter');
    counters.forEach(function(counter) {
        var startTime = counter.getAttribute('data-start-time');
        if (startTime && startTime !== 'null' && startTime !== 'undefined' && startTime.trim() !== '') {
            var duration = calculateDuration(startTime);
            if (counter.textContent !== duration) counter.textContent = duration;
        } else {
            if (counter.textContent !== '00:00:00') counter.textContent = '00:00:00';
        }
    });
}
function startDurationCounters() {
    if (durationInterval) clearInterval(durationInterval);
    updateDurationCounters();
    durationInterval = setInterval(updateDurationCounters, 1000);
}
function stopDurationCounters() {
    if (durationInterval) {
        clearInterval(durationInterval);
        durationInterval = null;
    }
}
function getPaymentDetails(customer) {
    if (!customer.payment) return '<span class="text-sm text-gray-400">No payment</span>';
    var payment = customer.payment;
    var amount = payment.amount ? '$' + parseFloat(payment.amount).toFixed(2) : '$0.00';
    var method = payment.method || 'N/A';
    var status = payment.status || 'Pending';
    if (currentStatusFilter === 'unpaid' && status === 'Completed') status = 'Payment Failed';
    var statusColors = {
        'Completed': 'bg-green-100 text-green-700',
        'Pending': 'bg-yellow-100 text-yellow-700',
        'Failed': 'bg-red-100 text-red-700',
        'Payment Failed': 'bg-red-100 text-red-700',
        'Refunded': 'bg-gray-100 text-gray-700'
    };
    var statusClass = statusColors[status] || 'bg-gray-100 text-gray-700';
    return '<div class="space-y-1"><div class="text-sm font-semibold text-gray-900">' + amount + '</div><div class="text-xs text-gray-600">' + method + '</div><span class="inline-block px-2 py-0.5 ' + statusClass + ' text-xs font-medium rounded">' + status + '</span></div>';
}
function getActionButtons(customer, fullName, isGrid) {
    var status = customer.status ? customer.status.toLowerCase() : '';
    var appointmentId = customer.appointmentId || customer.id;
    var escapedName = fullName.replace(/'/g, "\\'");
    if (status === 'paid' || status === 'cancelled' || status === 'canceled' || status === 'refunded' || status === 'closed') {
        var buttonClass = isGrid ? 'flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm active:scale-95' : 'inline-flex items-center gap-2 px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm active:scale-95';
        return '<button onclick="event.stopPropagation(); salonTicketsViewDetails(\'' + appointmentId + '\', \'' + escapedName + '\')" class="' + buttonClass + '"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>View</button>';
    }
    var assignButtonClass = isGrid ? 'flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm active:scale-95' : 'inline-flex items-center gap-2 px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm active:scale-95';
    var payButtonClass = isGrid ? 'flex-1 inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium text-sm active:scale-95' : 'inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium text-sm active:scale-95';
    return '<button onclick="event.stopPropagation(); salonTicketsAssignCustomer(\'' + customer.id + '\', \'' + escapedName + '\')" class="' + assignButtonClass + '"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>Assign</button><button onclick="event.stopPropagation(); window.location.href=\'' + payUrl + '?id=' + appointmentId + '\'" class="' + payButtonClass + '">Pay</button>';
}
function getPaginated() {
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity) return ticketsData;
    var start = (currentPage - 1) * PAGE_SIZE;
    return ticketsData.slice(start, start + PAGE_SIZE);
}
function updatePaginationState() {
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity) { totalPages = 1; currentPage = 1; return; }
    totalPages = Math.max(1, Math.ceil(ticketsData.length / PAGE_SIZE));
    if (currentPage > totalPages) currentPage = totalPages;
    if (currentPage < 1) currentPage = 1;
}
function renderPagination() {
    var el = document.getElementById('ticketsPagination');
    if (!el) return;
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity || ticketsData.length <= PAGE_SIZE || totalPages <= 1) { el.innerHTML = ''; return; }
    var h = '<div class="flex items-center gap-2 justify-center">';
    var disabledClass = 'text-gray-400 cursor-not-allowed opacity-50';
    var activeClass = 'bg-[#003047] text-white';
    var defaultClass = 'bg-white text-gray-700 border border-gray-300 hover:border-[#003047] hover:text-[#003047]';
    h += '<button class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300 ' + (currentPage === 1 ? disabledClass : 'bg-white text-[#003047] hover:bg-gray-100 hover:border-[#003047]') + '" ' + (currentPage === 1 ? 'disabled' : '') + ' onclick="salonTicketsGoToPage(1)" title="First page">&laquo;</button>';
    h += '<button class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300 ' + (currentPage === 1 ? disabledClass : 'bg-white text-[#003047] hover:bg-gray-100 hover:border-[#003047]') + '" ' + (currentPage === 1 ? 'disabled' : '') + ' onclick="salonTicketsChangePage(-1)" title="Previous page">&lt;</button>';
    var maxButtons = 6;
    var startPage = Math.max(1, currentPage - Math.floor(maxButtons / 2));
    var endPage = startPage + maxButtons - 1;
    if (endPage > totalPages) {
        endPage = totalPages;
        startPage = Math.max(1, endPage - maxButtons + 1);
    }
    for (var p = startPage; p <= endPage; p++) {
        var isActive = p === currentPage;
        h += '<button class="px-3 py-2 text-sm font-medium rounded-md border ' + (isActive ? activeClass : defaultClass) + '" onclick="salonTicketsGoToPage(' + p + ')">' + p + '</button>';
    }
    h += '<button class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300 ' + (currentPage === totalPages ? disabledClass : 'bg-white text-[#003047] hover:bg-gray-100 hover:border-[#003047]') + '" ' + (currentPage === totalPages ? 'disabled' : '') + ' onclick="salonTicketsChangePage(1)" title="Next page">&gt;</button>';
    h += '<button class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300 ' + (currentPage === totalPages ? disabledClass : 'bg-white text-[#003047] hover:bg-gray-100 hover:border-[#003047]') + '" ' + (currentPage === totalPages ? 'disabled' : '') + ' onclick="salonTicketsGoToPage(' + totalPages + ')" title="Last page">&raquo;</button></div>';
    el.innerHTML = h;
}
window.salonTicketsGoToPage = function(p) {
    if (p < 1 || p > totalPages || p === currentPage) return;
    currentPage = p;
    salonTicketsRender();
    updateCounter();
};
window.salonTicketsChangePage = function(offset) {
    salonTicketsGoToPage(currentPage + offset);
};
window.salonTicketsChangePerPage = function(val) {
    PAGE_SIZE = val === 'all' ? Infinity : parseInt(val, 10);
    currentPage = 1;
    localStorage.setItem('ticketsPerPage', val);
    updatePaginationState();
    salonTicketsRender();
    updateCounter();
};
function updateCounter() {
    var el = document.getElementById('ticketsResultsCounter');
    if (!el) return;
    var total = ticketsData.length;
    if (total === 0) { el.textContent = 'No results found'; return; }
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity) { el.textContent = 'Showing all ' + total + ' result' + (total !== 1 ? 's' : ''); return; }
    var start = (currentPage - 1) * PAGE_SIZE, end = Math.min(start + PAGE_SIZE, total);
    el.textContent = 'Showing ' + (start + 1) + '-' + end + ' of ' + total + ' result' + (total !== 1 ? 's' : '');
}
function renderGrid() {
    var el = document.getElementById('gridView');
    if (!el) return;
    var list = getPaginated();
    if (list.length === 0) {
        el.innerHTML = '<div class="col-span-full text-center py-12"><svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg><p class="text-gray-500 text-sm">No tickets found</p></div>';
        return;
    }
    el.innerHTML = list.map(function(customer, index) {
        var color = colorClasses[index % colorClasses.length];
        var initials = getInitials(customer);
        var fullName = customer.firstName + ' ' + customer.lastName;
        var appointmentType = customer.appointment || 'Walk-In';
        var appointmentTypeLower = appointmentType.toLowerCase();
        var statusClass = appointmentTypeLower === 'walk-in' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700';
        return '<div class="customer-card bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow flex flex-col h-full"><div class="flex-1"><div class="flex items-center gap-4 mb-4"><div class="w-16 h-16 ' + color.bg + ' rounded-full flex items-center justify-center flex-shrink-0"><span class="text-2xl font-bold ' + color.text + '">' + initials + '</span></div><div class="flex-1 min-w-0"><h3 class="font-normal text-gray-900 text-xl truncate">' + fullName + '</h3><p class="text-sm text-gray-500">' + (customer.phone || '') + '</p><div class="mt-2">' + renderTechniciansList(customer.assigned_technician) + '</div></div></div></div><div class="pt-4 border-t border-gray-200 mt-auto space-y-3"><div class="space-y-2"><span class="inline-block px-3 py-1 ' + statusClass + ' text-xs font-medium rounded-full">' + appointmentType + '</span></div><div class="flex gap-2">' + getActionButtons(customer, fullName, true) + '</div></div></div>';
    }).join('');
}
function renderList() {
    var tbody = document.getElementById('listViewBody');
    if (!tbody) return;
    var list = getPaginated();
    if (list.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-12 text-center"><svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg><p class="text-gray-500 text-sm">No tickets found</p></td></tr>';
        return;
    }
    tbody.innerHTML = list.map(function(customer, index) {
        var color = colorClasses[index % colorClasses.length];
        var initials = getInitials(customer);
        var fullName = customer.firstName + ' ' + customer.lastName;
        var appointmentType = customer.appointment || 'Walk-In';
        var appointmentTypeLower = appointmentType.toLowerCase();
        var statusClass = appointmentTypeLower === 'walk-in' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700';
        var rowNum = (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity) ? index + 1 : (currentPage - 1) * PAGE_SIZE + index + 1;
        return '<tr class="customer-row hover:bg-gray-50 transition"><td class="px-3 py-4 whitespace-nowrap text-center"><div class="text-sm text-gray-600">' + rowNum + '</div></td><td class="px-6 py-4 whitespace-nowrap"><div class="flex items-center"><div class="w-10 h-10 ' + color.bg + ' rounded-full flex items-center justify-center flex-shrink-0 mr-3"><span class="text-sm font-bold ' + color.text + '">' + initials + '</span></div><div><div class="text-base font-normal text-gray-900">' + fullName + '</div></div></div></td><td class="px-6 py-4 whitespace-nowrap"><div class="flex flex-col gap-1"><div class="text-sm text-gray-900">' + getTimeStarted(customer) + '</div><div class="text-base font-bold text-[#003047] duration-counter" data-start-time="' + (customer.appointment_datetime || customer.created_at || '').toString() + '" data-customer-id="' + (customer.id || customer.appointmentId || '') + '">' + calculateDuration(customer.appointment_datetime || customer.created_at) + '</div></div></td><td class="px-6 py-4">' + renderTechniciansList(customer.assigned_technician) + '</td><td class="px-6 py-4 whitespace-nowrap"><span class="inline-block px-3 py-1 ' + statusClass + ' text-xs font-medium rounded-full">' + appointmentType + '</span></td><td class="px-6 py-4 whitespace-nowrap">' + getPaymentDetails(customer) + '</td><td class="px-6 py-4 whitespace-nowrap text-right"><div class="flex items-center justify-end gap-2">' + getActionButtons(customer, fullName) + '</div></td></tr>';
    }).join('');
    setTimeout(function() { startDurationCounters(); }, 100);
}
function salonTicketsRender() {
    updatePaginationState();
    renderGrid();
    renderList();
    renderPagination();
    updateCounter();
}
function applyFilters() {
    var filtered = allMergedData.filter(function(item) {
        if (!item || !item.status) return false;
        var status = item.status.toLowerCase().trim();
        if (currentStatusFilter === 'unpaid') return status === 'unpaid';
        if (currentStatusFilter === 'paid') return status === 'paid';
        if (currentStatusFilter === 'cancelled') return status === 'cancelled' || status === 'canceled';
        if (currentStatusFilter === 'refunded') {
            if (status === 'refunded') return true;
            if (item.payment && item.payment.status === 'Refunded') return true;
            if (status === 'closed' && item.payment && item.payment.status === 'Refunded') return true;
            return false;
        }
        return true;
    });
    if (currentSearchTerm) {
        filtered = filtered.filter(function(c) {
            var text = (c.firstName + ' ' + c.lastName + ' ' + (c.email || '') + ' ' + (c.phone || '')).toLowerCase();
            return text.indexOf(currentSearchTerm) >= 0;
        });
    }
    filtered.sort(function(a, b) {
        return new Date(a.created_at) - new Date(b.created_at);
    });
    ticketsData = filtered;
    currentPage = 1;
    updatePaginationState();
    salonTicketsRender();
    updateCounter();
}
window.salonTicketsFilter = function(status) {
    currentStatusFilter = status;
    var url = new URL(window.location);
    if (status === 'unpaid') url.searchParams.set('status', 'unpaid');
    else if (status === 'paid') url.searchParams.set('status', 'paid');
    else if (status === 'cancelled') url.searchParams.set('status', 'cancelled');
    else if (status === 'refunded') url.searchParams.set('status', 'refunded');
    window.history.pushState({}, '', url);
    updateTabStates(status);
    applyFilters();
};
function updateTabStates(status) {
    var unpaidTab = document.getElementById('filterUnpaid');
    var paidTab = document.getElementById('filterPaid');
    var cancelledTab = document.getElementById('filterCancelled');
    var refundedTab = document.getElementById('filterRefunded');
    if (unpaidTab) {
        if (status === 'unpaid') {
            unpaidTab.classList.remove('text-gray-500', 'border-transparent');
            unpaidTab.classList.add('text-gray-900', 'border-[#003047]');
        } else {
            unpaidTab.classList.remove('text-gray-900', 'border-[#003047]');
            unpaidTab.classList.add('text-gray-500', 'border-transparent');
        }
    }
    if (paidTab) {
        if (status === 'paid') {
            paidTab.classList.remove('text-gray-500', 'border-transparent');
            paidTab.classList.add('text-gray-900', 'border-[#003047]');
        } else {
            paidTab.classList.remove('text-gray-900', 'border-[#003047]');
            paidTab.classList.add('text-gray-500', 'border-transparent');
        }
    }
    if (cancelledTab) {
        if (status === 'cancelled') {
            cancelledTab.classList.remove('text-gray-500', 'border-transparent');
            cancelledTab.classList.add('text-gray-900', 'border-[#003047]');
        } else {
            cancelledTab.classList.remove('text-gray-900', 'border-[#003047]');
            cancelledTab.classList.add('text-gray-500', 'border-transparent');
        }
    }
    if (refundedTab) {
        if (status === 'refunded') {
            refundedTab.classList.remove('text-gray-500', 'border-transparent');
            refundedTab.classList.add('text-gray-900', 'border-[#003047]');
        } else {
            refundedTab.classList.remove('text-gray-900', 'border-[#003047]');
            refundedTab.classList.add('text-gray-500', 'border-transparent');
        }
    }
}
function getStatusFromURL() {
    var p = new URLSearchParams(window.location.search).get('status');
    if (p && ['unpaid', 'paid', 'cancelled', 'refunded'].indexOf(p) >= 0) return p;
    return 'unpaid';
}
window.salonTicketsSearch = function(val) {
    currentSearchTerm = (val || '').toLowerCase();
    applyFilters();
};
window.salonTicketsToggleView = function(view) {
    currentView = view;
    localStorage.setItem('ticketsView', view);
    var g = document.getElementById('gridView'), l = document.getElementById('listView'), gb = document.getElementById('gridViewBtn'), lb = document.getElementById('listViewBtn');
    if (view === 'grid') {
        g.classList.remove('hidden');
        l.classList.add('hidden');
        if (gb && gb.querySelector('svg')) {
            gb.querySelector('svg').classList.remove('text-gray-500');
            gb.querySelector('svg').classList.add('text-gray-900');
        }
        if (lb && lb.querySelector('svg')) {
            lb.querySelector('svg').classList.remove('text-gray-900');
            lb.querySelector('svg').classList.add('text-gray-500');
        }
        renderGrid();
    } else {
        g.classList.add('hidden');
        l.classList.remove('hidden');
        if (lb && lb.querySelector('svg')) {
            lb.querySelector('svg').classList.remove('text-gray-500');
            lb.querySelector('svg').classList.add('text-gray-900');
        }
        if (gb && gb.querySelector('svg')) {
            gb.querySelector('svg').classList.remove('text-gray-900');
            gb.querySelector('svg').classList.add('text-gray-500');
        }
        renderList();
    }
};
window.salonTicketsViewDetails = function(appointmentId, customerName) {
    var appointment = allMergedData.find(function(apt) {
        if (apt.appointmentId && apt.appointmentId.toString() === appointmentId.toString()) return true;
        if (apt.id && apt.id.toString() === appointmentId.toString()) return true;
        return false;
    });
    if (!appointment) {
        alert('Appointment not found. Please try again.');
        return;
    }
    var customerId = appointment.customer_id;
    var customer = customerId ? allCustomers.find(function(c) { return c.id.toString() === customerId.toString(); }) : null;
    var fullName = customer ? customer.firstName + ' ' + customer.lastName : (appointment.firstName && appointment.lastName ? appointment.firstName + ' ' + appointment.lastName : customerName);
    var customerPhone = customer ? (customer.phone || 'No phone') : (appointment.phone || 'No phone');
    var customerEmail = customer ? (customer.email || 'No email') : (appointment.email || 'No email');
    var payment = appointment.payment || null;
    var paymentAmount = payment ? '$' + parseFloat(payment.amount).toFixed(2) : '$0.00';
    var paymentMethod = payment ? payment.method : 'N/A';
    var paymentStatus = payment ? payment.status : 'N/A';
    var paymentDate = payment ? payment.date : 'N/A';
    var aptDateTime = appointment.appointment_datetime || appointment.created_at;
    var aptDate = new Date(aptDateTime);
    var appointmentDate = aptDate.toLocaleDateString('en-US', { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' });
    var appointmentTime = aptDate.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    var services = appointment.services || [];
    var servicesByTechnician = {};
    if (services.length > 0) {
        services.forEach(function(s) {
            var serviceName = s.service ? s.service.replace(/-/g, ' ').replace(/\b\w/g, function(l) { return l.toUpperCase(); }) : 'Unknown';
            var techId = s.technician_id;
            var techName = 'Not Assigned';
            if (techId) {
                var technician = allTechnicians.find(function(t) { return t.id.toString() === techId.toString(); });
                techName = technician ? technician.firstName + ' ' + technician.lastName : 'Technician #' + techId;
            }
            if (!servicesByTechnician[techName]) servicesByTechnician[techName] = [];
            servicesByTechnician[techName].push(serviceName);
        });
    }
    var enhancedServicesList = Object.keys(servicesByTechnician).length > 0 ? Object.keys(servicesByTechnician).map(function(techName) {
        var techServices = servicesByTechnician[techName];
        return '<div class="mb-4 last:mb-0"><div class="mb-2"><h5 class="text-sm font-semibold text-gray-900">' + techName + '</h5></div><div class="space-y-1 ml-4">' + techServices.map(function(serviceName) {
            return '<div class="text-sm text-gray-700 pl-3 border-l-2 border-gray-200">' + serviceName + '</div>';
        }).join('') + '</div></div>';
    }).join('') : '<div class="text-sm text-gray-400 p-2">No services</div>';
    var technicians = appointment.assigned_technician || [];
    var techniciansList = technicians.length > 0 ? technicians.map(function(techId) {
        var technician = allTechnicians.find(function(t) { return t.id.toString() === techId.toString(); });
        return technician ? technician.firstName + ' ' + technician.lastName : 'Technician #' + techId;
    }).join(', ') : 'Not assigned';
    var appointmentType = appointment.appointment || 'walk-in';
    var appointmentTypeDisplay = appointmentType === 'walk-in' ? 'Walk-In' : 'Booked';
    var status = appointment.status || 'unpaid';
    var statusDisplay = status.charAt(0).toUpperCase() + status.slice(1);
    var statusColors = {
        'paid': 'bg-green-100 text-green-700',
        'unpaid': 'bg-yellow-100 text-yellow-700',
        'waiting': 'bg-blue-100 text-blue-700',
        'closed': 'bg-gray-100 text-gray-700'
    };
    var statusClass = statusColors[status] || 'bg-gray-100 text-gray-700';
    var aptId = appointment.appointmentId || appointment.id || 'N/A';
    var createdDate = appointment.created_at ? new Date(appointment.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : 'N/A';
    var transactionId = payment ? (payment.id || payment.bookingId || 'N/A') : 'N/A';
    var content = '<div class="p-6"><div class="flex items-center justify-between mb-6"><h3 class="text-2xl font-bold text-gray-900">Ticket Details</h3><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><div class="space-y-2"><div class="bg-gray-50 rounded-xl p-4"><h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Ticket Information</h4><div class="grid grid-cols-2 gap-4"><div><p class="text-xs text-gray-500 mb-1">Ticket ID</p><p class="text-base font-semibold text-gray-900">#' + aptId + '</p></div><div><p class="text-xs text-gray-500 mb-1">Status</p><span class="inline-block px-3 py-1 ' + statusClass + ' text-xs font-medium rounded-full">' + statusDisplay + '</span></div><div><p class="text-xs text-gray-500 mb-1">Appointment Type</p><p class="text-base font-semibold text-gray-900">' + appointmentTypeDisplay + '</p></div><div><p class="text-xs text-gray-500 mb-1">Created Date</p><p class="text-base font-semibold text-gray-900">' + createdDate + '</p></div></div></div><div class="bg-gray-50 rounded-xl p-4"><h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Customer Information</h4><div class="grid grid-cols-2 gap-4"><div><p class="text-xs text-gray-500 mb-1">Name</p><p class="text-base font-semibold text-gray-900">' + fullName + '</p></div><div><p class="text-xs text-gray-500 mb-1">Phone</p><p class="text-base font-semibold text-gray-900">' + customerPhone + '</p></div><div><p class="text-xs text-gray-500 mb-1">Email</p><p class="text-base font-semibold text-gray-900">' + customerEmail + '</p></div><div><p class="text-xs text-gray-500 mb-1">Customer ID</p><p class="text-base font-semibold text-gray-900">#' + (appointment.customer_id || 'N/A') + '</p></div></div></div><div class="bg-gray-50 rounded-xl p-4"><h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Appointment Details</h4><div class="grid grid-cols-2 gap-4"><div><p class="text-xs text-gray-500 mb-1">Date</p><p class="text-base font-semibold text-gray-900">' + appointmentDate + '</p></div><div><p class="text-xs text-gray-500 mb-1">Time</p><p class="text-base font-semibold text-gray-900">' + appointmentTime + '</p></div><div><p class="text-xs text-gray-500 mb-1">Assigned Technicians</p><p class="text-base font-semibold text-gray-900">' + techniciansList + '</p></div></div></div><div class="bg-gray-50 rounded-xl p-4"><h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Services</h4><div class="space-y-2">' + enhancedServicesList + '</div></div>' + (status !== 'cancelled' && status !== 'canceled' ? '<div class="bg-gray-50 rounded-xl p-4"><h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Payment Information</h4><div class="grid grid-cols-2 gap-4"><div><p class="text-xs text-gray-500 mb-1">Amount</p><p class="text-lg font-bold text-gray-900">' + paymentAmount + '</p></div><div><p class="text-xs text-gray-500 mb-1">Payment Method</p><p class="text-base font-semibold text-gray-900">' + paymentMethod + '</p></div><div><p class="text-xs text-gray-500 mb-1">Payment Status</p><span class="inline-block px-3 py-1 ' + (paymentStatus === 'Completed' ? 'bg-green-100 text-green-700' : paymentStatus === 'Refunded' ? 'bg-gray-100 text-gray-700' : paymentStatus === 'Failed' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') + ' text-xs font-medium rounded-full">' + paymentStatus + '</span></div><div><p class="text-xs text-gray-500 mb-1">Payment Date</p><p class="text-base font-semibold text-gray-900">' + paymentDate + '</p></div>' + (transactionId !== 'N/A' ? '<div><p class="text-xs text-gray-500 mb-1">Transaction ID</p><p class="text-base font-semibold text-gray-900">' + transactionId + '</p></div>' : '') + '</div>' + (status === 'paid' ? '<div class="mt-4 pt-4 border-t border-gray-200"><button onclick="salonTicketsConfirmRefund(\'' + aptId + '\', \'' + fullName.replace(/'/g, "\\'") + '\', \'' + paymentAmount + '\')" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium text-sm active:scale-95"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>Refund</button></div>' : '') + '</div>' : '') + (status === 'cancelled' || status === 'canceled' ? '<div class="bg-gray-50 rounded-xl p-4"><div class="flex items-center justify-between"><div><h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Ticket Status</h4><p class="text-base text-gray-700">This ticket has been cancelled</p></div><button onclick="salonTicketsConfirmRestore(\'' + aptId + '\', \'' + fullName.replace(/'/g, "\\'") + '\')" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium text-sm active:scale-95"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Restore</button></div></div>' : '') + '</div><div class="flex items-center justify-end gap-3 pt-6 mt-6 border-t border-gray-200"><button onclick="closeModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">Close</button></div></div>';
    openModal(content, 'medium');
};
window.salonTicketsConfirmRefund = function(appointmentId, customerName, amount) {
    var amountValue = parseFloat(amount.replace('$', '').replace(',', ''));
    var appointment = allMergedData.find(function(apt) {
        return (apt.appointmentId && apt.appointmentId.toString() === appointmentId.toString()) || (apt.id && apt.id.toString() === appointmentId.toString());
    });
    if (!appointment) {
        alert('Appointment not found.');
        return;
    }
    var payment = appointment.payment || null;
    var paymentMethod = payment ? payment.method : 'N/A';
    var transactionId = payment ? (payment.id || payment.bookingId || 'N/A') : 'N/A';
    var content = '<div class="p-6"><div class="flex items-center justify-between mb-6"><div class="flex items-center gap-3"><div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center"><svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg></div><div><h3 class="text-xl font-bold text-gray-900">Refund Transaction</h3><p class="text-sm text-gray-500">Confirm refund for ' + customerName + '</p></div></div><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><div class="space-y-4"><div class="bg-gray-50 rounded-lg p-4"><h4 class="text-sm font-semibold text-gray-700 mb-3 uppercase">Transaction Details</h4><div class="grid grid-cols-2 gap-4"><div><p class="text-xs text-gray-500 mb-1">Amount</p><p class="text-lg font-bold text-gray-900">' + amount + '</p></div><div><p class="text-xs text-gray-500 mb-1">Payment Method</p><p class="text-sm font-semibold text-gray-900">' + paymentMethod + '</p></div><div><p class="text-xs text-gray-500 mb-1">Transaction ID</p><p class="text-sm font-semibold text-gray-900">' + transactionId + '</p></div></div></div><div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4"><div class="flex items-start gap-3"><svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg><div><h4 class="text-sm font-semibold text-yellow-800 mb-1">Refund Warning</h4><p class="text-sm text-yellow-700">This action cannot be undone. The refund will be processed immediately.</p></div></div></div></div><div class="flex justify-end gap-3 mt-6 pt-6 border-t border-gray-200"><button onclick="closeModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">Cancel</button><button onclick="salonTicketsProcessRefund(\'' + appointmentId + '\', \'' + customerName.replace(/'/g, "\\'") + '\', ' + amountValue + ')" class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium active:scale-95">Confirm Refund</button></div></div>';
    openModal(content);
};
window.salonTicketsProcessRefund = function(appointmentId, customerName, amount) {
    var appointment = allMergedData.find(function(apt) {
        return (apt.appointmentId && apt.appointmentId.toString() === appointmentId.toString()) || (apt.id && apt.id.toString() === appointmentId.toString());
    });
    if (!appointment) {
        alert('Appointment not found.');
        return;
    }
    appointment.status = 'refunded';
    if (appointment.payment) {
        appointment.payment.status = 'Refunded';
    }
    allMergedData = allMergedData.filter(function(item) {
        if (!item || !item.status) return false;
        var status = item.status.toLowerCase().trim();
        var validStatuses = ['in-progress', 'completed', 'waiting', 'paid', 'cancelled', 'canceled', 'refunded', 'closed'];
        return validStatuses.indexOf(status) >= 0;
    });
    currentStatusFilter = 'refunded';
    updateTabStates('refunded');
    var url = new URL(window.location);
    url.searchParams.set('status', 'refunded');
    window.history.pushState({}, '', url);
    applyFilters();
    closeModal();
    showSuccessMessage('Refund of $' + amount.toFixed(2) + ' has been processed successfully for ' + customerName + '. The ticket has been moved to the "Refunded" tab.');
};
window.salonTicketsConfirmRestore = function(appointmentId, customerName) {
    var appointment = allMergedData.find(function(apt) {
        return (apt.appointmentId && apt.appointmentId.toString() === appointmentId.toString()) || (apt.id && apt.id.toString() === appointmentId.toString());
    });
    if (!appointment) {
        alert('Appointment not found.');
        return;
    }
    appointment.status = 'unpaid';
    allMergedData = allMergedData.filter(function(item) {
        if (!item || !item.status) return false;
        var status = item.status.toLowerCase().trim();
        var validStatuses = ['in-progress', 'completed', 'waiting', 'paid', 'cancelled', 'canceled', 'refunded', 'closed'];
        return validStatuses.indexOf(status) >= 0;
    });
    currentStatusFilter = 'unpaid';
    updateTabStates('unpaid');
    var url = new URL(window.location);
    url.searchParams.set('status', 'unpaid');
    window.history.pushState({}, '', url);
    applyFilters();
    closeModal();
    showSuccessMessage('Ticket #' + appointmentId + ' has been restored successfully for ' + customerName + '. The ticket has been moved to the "Unpaid" tab.');
};
window.salonTicketsAssignCustomer = function(customerId, customerName) {
    currentCustomerId = customerId;
    currentCustomerName = customerName;
    technicianSearchTerm = '';
    var customer = allMergedData.find(function(c) {
        return (c.id && c.id.toString() === customerId.toString()) || (c.customer_id && c.customer_id.toString() === customerId.toString());
    });
    if (customer && customer.assigned_technician && Array.isArray(customer.assigned_technician)) {
        assignedTechnicianIds = customer.assigned_technician.map(function(id) { return id.toString(); });
    } else {
        assignedTechnicianIds = [];
    }
    var content = '<div class="p-6"><div class="flex items-center justify-between mb-6"><h3 class="text-xl font-bold text-gray-900">Assign Technician to ' + customerName + '</h3><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><div class="grid grid-cols-2 gap-6"><div class="border border-gray-200 rounded-lg p-4"><div class="flex items-center justify-between mb-2"><h4 class="text-sm font-semibold text-gray-900">Available Technicians</h4><span id="availableCount" class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">0</span></div><p class="text-xs text-gray-500 mb-2">Click to assign technicians to services</p><div class="mb-4"><div class="relative"><svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg><input type="text" id="technicianSearchInput" placeholder="Search technicians..." oninput="salonTicketsSearchTechnicians(this.value)" class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-sm"><button id="clearTechnicianSearchBtn" onclick="salonTicketsClearTechnicianSearch()" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div></div><div id="availableTechniciansContainer" class="space-y-3 min-h-[500px] max-h-[500px] overflow-y-auto"></div></div><div class="border border-gray-200 rounded-lg p-4"><div class="flex items-center justify-between mb-2"><h4 class="text-sm font-semibold text-gray-900">Assigned Technicians</h4><span id="assignedCount" class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">0</span></div><p class="text-xs text-gray-500 mb-4">Click to remove assigned technicians</p><div id="assignedTechniciansContainer" class="space-y-3 min-h-[500px] max-h-[500px] overflow-y-auto"><div class="flex items-center justify-center h-full min-h-[500px]"><p class="text-sm text-gray-400">No technicians assigned</p></div></div></div></div><div class="pt-6 mt-6 border-t border-gray-200"><div class="flex items-center justify-end"><div class="flex gap-3"><button onclick="closeModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">Cancel</button><button onclick="salonTicketsConfirmAssign()" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Save Changes</button></div></div></div></div>';
    openModal(content, 'large', false);
    setTimeout(function() {
        var modalContainer = document.getElementById('modalContainer');
        if (modalContainer) modalContainer.style.maxHeight = '95vh';
    }, 50);
    salonTicketsLoadTechnicians();
    setTimeout(function() {
        var searchInput = document.getElementById('technicianSearchInput');
        var clearBtn = document.getElementById('clearTechnicianSearchBtn');
        if (searchInput && clearBtn) clearBtn.classList.add('hidden');
    }, 100);
};
window.salonTicketsLoadTechnicians = function() {
    fetch(base + '/users.json').then(function(r) { return r.json(); }).then(function(data) {
        availableTechnicians = (data.users || []).filter(function(u) { return u.role === 'technician' || u.userlevel === 'technician'; });
        originalTechnicianOrder = availableTechnicians.map(function(t) { return t.id; });
        salonTicketsRenderAvailableTechnicians();
        salonTicketsRenderAssignedTechnicians();
        salonTicketsUpdateCounts();
    }).catch(function(err) {
        console.error('Error loading technicians:', err);
        var container = document.getElementById('availableTechniciansContainer');
        if (container) container.innerHTML = '<div class="text-center py-12"><p class="text-sm text-gray-400">Error loading technicians</p></div>';
    });
};
window.salonTicketsSearchTechnicians = function(val) {
    technicianSearchTerm = (val || '').toLowerCase().trim();
    var clearBtn = document.getElementById('clearTechnicianSearchBtn');
    if (clearBtn) {
        if (val.trim()) clearBtn.classList.remove('hidden');
        else clearBtn.classList.add('hidden');
    }
    salonTicketsRenderAvailableTechnicians();
};
window.salonTicketsClearTechnicianSearch = function() {
    var searchInput = document.getElementById('technicianSearchInput');
    var clearBtn = document.getElementById('clearTechnicianSearchBtn');
    if (searchInput) { searchInput.value = ''; technicianSearchTerm = ''; searchInput.focus(); }
    if (clearBtn) clearBtn.classList.add('hidden');
    salonTicketsRenderAvailableTechnicians();
};
window.salonTicketsRenderAvailableTechnicians = function() {
    var container = document.getElementById('availableTechniciansContainer');
    if (!container) return;
    if (!availableTechnicians.length) {
        container.innerHTML = '<div class="flex items-center justify-center h-full min-h-[500px]"><p class="text-sm text-gray-400">No technicians available</p></div>';
        return;
    }
    var filtered = technicianSearchTerm ? availableTechnicians.filter(function(t) {
        var name = (t.firstName + ' ' + t.lastName).toLowerCase();
        var inits = (t.initials || (t.firstName || '')[0] + (t.lastName || '')[0]).toLowerCase();
        return (name + ' ' + inits).indexOf(technicianSearchTerm) >= 0;
    }) : availableTechnicians;
    if (!filtered.length) {
        container.innerHTML = '<div class="flex items-center justify-center h-full min-h-[500px]"><p class="text-sm text-gray-400">No technicians found</p></div>';
        return;
    }
    filtered.sort(function(a, b) {
        var aIdStr = a.id.toString();
        var bIdStr = b.id.toString();
        var aIsAssigned = assignedTechnicianIds.indexOf(aIdStr) >= 0;
        var bIsAssigned = assignedTechnicianIds.indexOf(bIdStr) >= 0;
        if (aIsAssigned && !bIsAssigned) return 1;
        if (!aIsAssigned && bIsAssigned) return -1;
        var aIndex = originalTechnicianOrder.indexOf(a.id);
        var bIndex = originalTechnicianOrder.indexOf(b.id);
        return aIndex - bIndex;
    });
    var html = '';
    filtered.forEach(function(technician) {
        var techIdStr = technician.id.toString();
        var isAssigned = assignedTechnicianIds.indexOf(techIdStr) >= 0;
        var initials = technician.initials || (technician.firstName || '')[0] + (technician.lastName || '')[0];
        var fullName = technician.firstName + ' ' + technician.lastName;
        var containerClasses = isAssigned ? 'flex items-center gap-3 p-2 rounded-lg transition-colors opacity-50 grayscale cursor-pointer group hover:bg-gray-100' : 'flex items-center gap-3 cursor-pointer group hover:bg-gray-50 p-2 rounded-lg transition-colors';
        var avatarClasses = isAssigned ? 'w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center' : 'w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center';
        var initialClasses = isAssigned ? 'text-sm font-bold text-gray-500' : 'text-sm font-bold text-gray-600';
        var nameClasses = isAssigned ? 'text-base font-medium text-gray-400' : 'text-base font-medium text-gray-900';
        var badgeClasses = isAssigned ? 'absolute -bottom-1 -right-1 w-5 h-5 bg-gray-400 text-white rounded-full flex items-center justify-center text-xs font-bold border-2 border-white' : 'absolute -bottom-1 -right-1 w-5 h-5 bg-[#003047] text-white rounded-full flex items-center justify-center text-xs font-bold border-2 border-white';
        html += '<div onclick="' + (isAssigned ? 'salonTicketsRemoveAssignedTechnician(' + technician.id + ')' : 'salonTicketsAssignTechnician(' + technician.id + ')') + '" class="' + containerClasses + '"><div class="relative flex-shrink-0"><div class="' + avatarClasses + '"><span class="' + initialClasses + '">' + initials + '</span></div><div class="' + badgeClasses + '">0</div></div><div class="flex-1"><p class="' + nameClasses + '">' + fullName + '</p></div></div>';
    });
    container.innerHTML = html;
};
window.salonTicketsRenderAssignedTechnicians = function() {
    var container = document.getElementById('assignedTechniciansContainer');
    if (!container) return;
    if (!assignedTechnicianIds.length) {
        container.innerHTML = '<div class="flex items-center justify-center h-full min-h-[500px]"><p class="text-sm text-gray-400">No technicians assigned</p></div>';
        return;
    }
    var html = '';
    assignedTechnicianIds.forEach(function(techIdStr) {
        var technician = availableTechnicians.find(function(t) { return t.id.toString() === techIdStr; });
        if (!technician) return;
        var initials = technician.initials || (technician.firstName || '')[0] + (technician.lastName || '')[0];
        var fullName = technician.firstName + ' ' + technician.lastName;
        html += '<div onclick="salonTicketsRemoveAssignedTechnician(' + technician.id + ')" class="flex items-center gap-3 cursor-pointer group hover:bg-gray-50 p-2 rounded-lg transition-colors"><div class="relative flex-shrink-0"><div class="w-12 h-12 bg-[#003047] rounded-full flex items-center justify-center"><span class="text-sm font-bold text-white">' + initials + '</span></div><div class="absolute -bottom-1 -right-1 w-5 h-5 bg-[#003047] text-white rounded-full flex items-center justify-center text-xs font-bold border-2 border-white">0</div></div><div class="flex-1"><p class="text-base font-medium text-gray-900">' + fullName + '</p></div></div>';
    });
    container.innerHTML = html;
};
window.salonTicketsAssignTechnician = function(technicianId) {
    var techIdStr = technicianId.toString();
    if (assignedTechnicianIds.indexOf(techIdStr) < 0) {
        assignedTechnicianIds.push(techIdStr);
        salonTicketsRenderAvailableTechnicians();
        salonTicketsRenderAssignedTechnicians();
        salonTicketsUpdateCounts();
    }
};
window.salonTicketsRemoveAssignedTechnician = function(technicianId) {
    var techIdStr = technicianId.toString();
    assignedTechnicianIds = assignedTechnicianIds.filter(function(id) { return id !== techIdStr; });
    salonTicketsRenderAvailableTechnicians();
    salonTicketsRenderAssignedTechnicians();
    salonTicketsUpdateCounts();
};
window.salonTicketsUpdateCounts = function() {
    var availableCountEl = document.getElementById('availableCount');
    var assignedCountEl = document.getElementById('assignedCount');
    if (availableCountEl) availableCountEl.textContent = availableTechnicians.length;
    if (assignedCountEl) assignedCountEl.textContent = assignedTechnicianIds.length;
};
window.salonTicketsConfirmAssign = function() {
    if (!assignedTechnicianIds.length) {
        alert('Please assign at least one technician');
        return;
    }
    var assignedNames = assignedTechnicianIds.map(function(id) {
        var tech = availableTechnicians.find(function(t) { return t.id.toString() === id; });
        return tech ? tech.firstName + ' ' + tech.lastName : '';
    }).filter(Boolean);
    var message = currentCustomerName + ' assigned to ' + assignedNames.join(', ') + ' successfully!';
    showSuccessMessage(message);
    closeModal();
    assignedTechnicianIds = [];
    currentCustomerId = null;
    currentCustomerName = '';
    setTimeout(function() { location.reload(); }, 1500);
};
async function fetchTickets() {
    try {
        var custRes = await fetch(base + '/customers.json');
        var aptRes = await fetch(base + '/appointments.json');
        var techRes = await fetch(base + '/users.json');
        var payRes = await fetch(base + '/payments.json');
        var custData = await custRes.json();
        var aptData = await aptRes.json();
        var techData = await techRes.json();
        var payData = await payRes.json();
        allCustomers = custData.customers || [];
        allAppointments = aptData.appointments || [];
        allTechnicians = (techData.users || []).filter(function(u) { return u.role === 'technician' || u.userlevel === 'technician'; });
        allPayments = payData.payments || [];
        mergeAppointmentsWithCustomers();
        currentStatusFilter = getStatusFromURL();
        if (!new URLSearchParams(window.location.search).get('status')) {
            var url = new URL(window.location);
            url.searchParams.set('status', currentStatusFilter);
            window.history.replaceState({}, '', url);
        }
        updateTabStates(currentStatusFilter);
        applyFilters();
        window.addEventListener('popstate', function() {
            currentStatusFilter = getStatusFromURL();
            updateTabStates(currentStatusFilter);
            applyFilters();
        });
    } catch (err) {
        console.error('Error fetching data:', err);
        showErrorMessage('Failed to load data');
    }
}
document.addEventListener('DOMContentLoaded', function() {
    var saved = localStorage.getItem('ticketsPerPage');
    if (saved) {
        var sel = document.getElementById('perPageSelect');
        if (sel) {
            sel.value = saved;
            PAGE_SIZE = saved === 'all' ? Infinity : parseInt(saved, 10);
        }
    }
    fetchTickets().then(function() {
        salonTicketsToggleView(currentView);
    });
});
window.addEventListener('beforeunload', function() {
    stopDurationCounters();
});
})();
</script>
@endpush
@endsection

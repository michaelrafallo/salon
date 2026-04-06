@extends('layouts.salon')

@section('content')
@php
    $bookingUrl = route('salon.booking.index');
    $payUrl = route('salon.booking.pay');
    $ticketsTab = request()->query('status', 'unpaid');
    if (! in_array($ticketsTab, ['unpaid', 'paid', 'cancelled', 'refunded'], true)) {
        $ticketsTab = 'unpaid';
    }
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
            </div>
        </div>
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-6 border-b border-gray-200">
                <button onclick="salonTicketsFilter('unpaid')" id="filterUnpaid" class="filter-tab px-1 py-3 text-sm font-medium transition {{ $ticketsTab === 'unpaid' ? 'text-gray-900 border-b-2 border-[#003047]' : 'text-gray-500 border-b-2 border-transparent hover:text-gray-700' }}">Unpaid</button>
                <button onclick="salonTicketsFilter('paid')" id="filterPaid" class="filter-tab px-1 py-3 text-sm font-medium transition {{ $ticketsTab === 'paid' ? 'text-gray-900 border-b-2 border-[#003047]' : 'text-gray-500 border-b-2 border-transparent hover:text-gray-700' }}">Paid</button>
                <button onclick="salonTicketsFilter('cancelled')" id="filterCancelled" class="filter-tab px-1 py-3 text-sm font-medium transition {{ $ticketsTab === 'cancelled' ? 'text-gray-900 border-b-2 border-[#003047]' : 'text-gray-500 border-b-2 border-transparent hover:text-gray-700' }}">Cancelled</button>
                <button onclick="salonTicketsFilter('refunded')" id="filterRefunded" class="filter-tab px-1 py-3 text-sm font-medium transition {{ $ticketsTab === 'refunded' ? 'text-gray-900 border-b-2 border-[#003047]' : 'text-gray-500 border-b-2 border-transparent hover:text-gray-700' }}">Refunded</button>
            </div>
            <div class="flex items-center gap-3">
                <input type="date" id="ticketDateFilterInput" onchange="salonTicketsFilterByDate(this.value)" class="px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-base text-gray-900">
                <div class="relative max-w-md">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" id="ticketSearchInput" placeholder="Search customers" oninput="salonTicketsSearch(this.value)" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-base">
                </div>
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
                            <th id="paymentDetailsHeader" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Details</th>
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
@push('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
<style>
.select-svc-carousel-wrapper { position: relative; padding: 0 40px; }
.select-svc-slick-carousel { height: 70px; opacity: 0; visibility: hidden; transition: opacity 0.3s ease-in-out; }
.select-svc-slick-carousel.slick-initialized { opacity: 1; visibility: visible; }
.select-svc-slick-carousel.show-fallback { opacity: 1 !important; visibility: visible !important; display: flex !important; flex-wrap: wrap !important; gap: 8px !important; overflow-x: auto !important; height: auto !important; }
.select-svc-slick-carousel.show-fallback > div { flex: 0 0 auto; width: calc(16.666% - 7px); }
.select-svc-slick-carousel .slick-slide { margin: 0 4px; height: 70px !important; display: flex; align-items: stretch; }
.select-svc-slick-carousel .slick-slide > div { height: 70px !important; width: 100%; display: flex; }
.select-svc-slick-carousel .slick-list { margin: 0 -4px; height: 70px; }
.select-svc-slick-carousel .slick-track { display: flex !important; align-items: stretch; height: 70px; }
.select-svc-cat-card { word-wrap: break-word; overflow-wrap: break-word; hyphens: auto; height: 70px !important; min-height: 70px; max-height: 70px; }
.select-svc-slick-carousel .slick-prev, .select-svc-slick-carousel .slick-next { width: 32px; height: 32px; background: white; border: 1px solid #d1d5db; border-radius: 50%; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.1); z-index: 10; }
.select-svc-slick-carousel .slick-prev { left: -40px; }
.select-svc-slick-carousel .slick-next { right: -40px; }
.select-svc-slick-carousel .slick-prev:before, .select-svc-slick-carousel .slick-next:before { content: ''; display: inline-block; width: 16px; height: 16px; background-size: contain; background-repeat: no-repeat; background-position: center; opacity: 1; }
.select-svc-slick-carousel .slick-prev:before { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%234b5563'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15 19l-7-7 7-7'/%3E%3C/svg%3E"); }
.select-svc-slick-carousel .slick-next:before { background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%234b5563'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 5l7 7-7 7'/%3E%3C/svg%3E"); }
.select-svc-slick-carousel .slick-prev:hover, .select-svc-slick-carousel .slick-next:hover { background: #f9fafb; }
.select-svc-slick-carousel .slick-disabled { opacity: 0.3; cursor: default; }
@keyframes rotate-clock { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
.rotating-clock { animation: rotate-clock 2s linear infinite; display: inline-block; }
</style>
@endpush
@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
<script>
(function() {
var base = window.salonJsonBase || '{{ url("api/salon/data") }}';
window.salonTicketsBootstrap = window.salonTicketsBootstrap || @json($ticketsBootstrap ?? null);
var apiAppointmentsUrl = '{{ url("api/salon/appointments") }}';
var apiPaymentsUrl = '{{ url("api/salon/payments") }}';
var bookingUrl = '{{ $bookingUrl }}';
var payUrl = '{{ $payUrl }}';
var allCustomers = [], allAppointments = [], allTechnicians = [], allPayments = [], allMergedData = [], ticketsData = [];
var PAGE_SIZE = 15, currentPage = 1, totalPages = 1, currentSearchTerm = '', currentStatusFilter = @json($ticketsTab);
var currentDateFilter = (function() {
    var p = new URLSearchParams(window.location.search).get('date');
    if (p) return p;
    var n = new Date();
    return n.getFullYear() + '-' + String(n.getMonth() + 1).padStart(2, '0') + '-' + String(n.getDate()).padStart(2, '0');
})();
var currentView = localStorage.getItem('ticketsView') || 'grid';
var durationInterval = null;
var availableTechnicians = [];
var originalTechnicianOrder = [];
var assignedTechnicianIds = [];
var currentCustomerId = null, currentAppointmentId = null;
var currentCustomerName = '';
var technicianSearchTerm = '';
var assignedTechnicianSearchTerm = '';
var resizeHandlerForTechnicians = null;
var currentEventModalElement = null;
var turnTrackerOrder = 'lowest', turnTrackerUserIds = new Set(), turnTrackerPositions = new Map();
// Select services state
var selectServicesData = [], selectServicesCategoriesMap = {}, selectServicesCategory = null, selectServicesCart = [];
var selectServicesAppointmentId = null, selectServicesTechnicianId = null, selectServicesTechnicianName = '';
var selectServicesLoaded = false, selectServicesOldServiceCount = 0;
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
        var photo = tech.profilePhotoUrl || tech.profilePhoto || tech.avatar || tech.image || null;
        var hoverAttr = photo ? ' onmouseenter="showTechPhotoPreview(event, \'' + photo.replace(/'/g, "\\'") + '\', \'' + name.replace(/'/g, "\\'") + '\')" onmouseleave="hideTechPhotoPreview()"' : '';
        return '<div class="flex items-center gap-2 mb-1 last:mb-0">' +
            (photo ? '<img src="' + photo + '" alt="' + name + '" class="w-8 h-8 rounded-full object-cover flex-shrink-0 border-2 border-white shadow-sm cursor-pointer"' + hoverAttr + ' onerror="this.style.display=\'none\'; this.nextElementSibling.style.display=\'flex\';"><div class="w-8 h-8 ' + c.bg + ' rounded-full flex items-center justify-center flex-shrink-0 hidden"><span class="text-xs font-bold ' + c.text + '">' + inits + '</span></div>' :
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
    var startTime = customer.appointment_datetime;
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
    var amount = payment.amount ? window.salonFormatMoney(parseFloat(payment.amount)) : window.salonFormatMoney(0);
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
    return '<button onclick="event.stopPropagation(); salonTicketsViewDetails(\'' + appointmentId + '\', \'' + escapedName + '\')" class="' + assignButtonClass + '"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>View</button><button onclick="event.stopPropagation(); window.location.href=\'' + payUrl + '?id=' + appointmentId + '\'" class="' + payButtonClass + '">Pay</button><button onclick="event.stopPropagation(); salonTicketsShowDeleteConfirm(\'' + appointmentId + '\', \'' + escapedName + '\')" class="inline-flex items-center justify-center px-4 py-2 text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition font-medium text-sm active:scale-95 self-stretch" title="Delete ticket"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>';
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
    var isUnpaid = currentStatusFilter === 'unpaid';
    var paymentHeader = document.getElementById('paymentDetailsHeader');
    if (paymentHeader) paymentHeader.style.display = isUnpaid ? 'none' : '';
    var colSpan = isUnpaid ? '6' : '7';
    var list = getPaginated();
    if (list.length === 0) {
        tbody.innerHTML = '<tr><td colspan="' + colSpan + '" class="px-6 py-12 text-center"><svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg><p class="text-gray-500 text-sm">No tickets found</p></td></tr>';
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
        var customerStatus = (customer.status || '').toLowerCase();
        var startTimeCell = customerStatus === 'unpaid' || customerStatus === 'waiting' || customerStatus === 'in-progress'
            ? '<div class="flex flex-col gap-1"><div class="text-sm text-gray-900">' + getTimeStarted(customer) + '</div><div class="flex items-center gap-1"><svg style="width:12px;height:12px;color:#008106;" class="rotating-clock" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><span class="text-base font-bold text-[#003047] duration-counter" data-start-time="' + (customer.appointment_datetime || '').toString() + '" data-customer-id="' + (customer.id || customer.appointmentId || '') + '">' + calculateDuration(customer.appointment_datetime) + '</span></div></div>'
            : '<div class="text-sm text-gray-900">' + getTimeStarted(customer) + '</div>';
        var paymentCell = isUnpaid ? '' : '<td class="px-6 py-4 whitespace-nowrap">' + getPaymentDetails(customer) + '</td>';
        return '<tr class="customer-row hover:bg-gray-50 transition"><td class="px-3 py-4 whitespace-nowrap text-center"><div class="text-sm text-gray-600">' + rowNum + '</div></td><td class="px-6 py-4 whitespace-nowrap"><div class="flex items-center"><div class="w-10 h-10 ' + color.bg + ' rounded-full flex items-center justify-center flex-shrink-0 mr-3"><span class="text-sm font-bold ' + color.text + '">' + initials + '</span></div><div><div class="text-base font-normal text-gray-900">' + fullName + '</div></div></div></td><td class="px-6 py-4 whitespace-nowrap">' + startTimeCell + '</td><td class="px-6 py-4">' + renderTechniciansList(customer.assigned_technician) + '</td><td class="px-6 py-4 whitespace-nowrap"><span class="inline-block px-3 py-1 ' + statusClass + ' text-xs font-medium rounded-full">' + appointmentType + '</span></td>' + paymentCell + '<td class="px-6 py-4 whitespace-nowrap text-right"><div class="flex items-center justify-end gap-2">' + getActionButtons(customer, fullName) + '</div></td></tr>';
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
    if (currentDateFilter) {
        filtered = filtered.filter(function(item) {
            var dt = item.appointment_datetime || item.created_at;
            if (!dt) return false;
            var itemDate = new Date(dt);
            var y = itemDate.getFullYear(), m = String(itemDate.getMonth() + 1).padStart(2, '0'), d = String(itemDate.getDate()).padStart(2, '0');
            return (y + '-' + m + '-' + d) === currentDateFilter;
        });
    }
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
window.salonTicketsFilterByDate = function(dateStr) {
    currentDateFilter = dateStr || '';
    var url = new URL(window.location.href);
    if (currentDateFilter) {
        url.searchParams.set('date', currentDateFilter);
    } else {
        url.searchParams.delete('date');
    }
    window.history.pushState({}, '', url);
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
function setViewUI(view) {
    var g = document.getElementById('gridView');
    var l = document.getElementById('listView');
    var gb = document.getElementById('gridViewBtn');
    var lb = document.getElementById('listViewBtn');

    var isGrid = view === 'grid';
    if (g) g.classList.toggle('hidden', !isGrid);
    if (l) l.classList.toggle('hidden', isGrid);

    if (gb) {
        gb.classList.toggle('bg-white', isGrid);
        gb.classList.toggle('shadow-sm', isGrid);
        if (gb.querySelector('svg')) {
            gb.querySelector('svg').classList.toggle('text-gray-900', isGrid);
            gb.querySelector('svg').classList.toggle('text-gray-500', !isGrid);
        }
    }
    if (lb) {
        lb.classList.toggle('bg-white', !isGrid);
        lb.classList.toggle('shadow-sm', !isGrid);
        if (lb.querySelector('svg')) {
            lb.querySelector('svg').classList.toggle('text-gray-900', !isGrid);
            lb.querySelector('svg').classList.toggle('text-gray-500', isGrid);
        }
    }
}
window.salonTicketsToggleView = function(view) {
    currentView = view;
    localStorage.setItem('ticketsView', view);
    setViewUI(view);
    if (view === 'grid') {
        stopDurationCounters();
        renderGrid();
        return;
    }
    renderList();
};

// Build technician display HTML for the detail modal (like waiting list)
function buildTicketTechnicianDisplayHtml(appointmentId) {
    var appointment = allAppointments.find(function(a) { return a.id.toString() === appointmentId.toString(); });
    var assignedIds = appointment && Array.isArray(appointment.assigned_technician) ? appointment.assigned_technician : [];
    if (assignedIds.length === 0) return '<p class="text-sm text-gray-400">Not Assigned</p>';
    var allSvcs = appointment && Array.isArray(appointment.services) ? appointment.services : [];
    return '<div class="space-y-2">' + assignedIds.map(function(techId) {
        var tech = allTechnicians.find(function(t) { return t.id.toString() === techId.toString(); });
        if (!tech) return '';
        var name = tech.firstName + ' ' + tech.lastName;
        var initials = tech.initials || (tech.firstName || '')[0] + (tech.lastName || '')[0];
        var photo = tech.profilePhotoUrl || tech.photo || null;
        var serviceCount = typeof tech.services === 'number' ? tech.services : 0;
        var techSvcs = allSvcs.filter(function(s) { return s.technician_id && s.technician_id.toString() === techId.toString(); });
        var techIsOnline = !!(tech.clock_in && !tech.clock_out);
        var techBadgeColor = techIsOnline ? 'bg-green-500' : 'bg-gray-400';
        var techStatusBadge = '<span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full border-2 border-white ' + techBadgeColor + '"></span>';
        var avatarInner = photo
            ? '<div class="w-9 h-9 rounded-full overflow-hidden border border-gray-200" style="min-width:36px;min-height:36px;max-width:36px;max-height:36px"><img src="' + photo + '" alt="' + name + '" style="width:36px;height:36px;object-fit:cover"></div>'
            : '<div class="w-9 h-9 bg-[#e6f0f3] rounded-full flex items-center justify-center border border-gray-200" style="min-width:36px;min-height:36px;max-width:36px;max-height:36px"><span class="text-xs font-bold text-[#003047]">' + initials + '</span></div>';
        var avatarHtml = '<div class="relative">' + avatarInner + techStatusBadge + '</div>';
        var svcListHtml = '';
        if (techSvcs.length > 0) {
            var svcItems = techSvcs.map(function(s) {
                var sName = s.service_name || s.service || 'Service';
                var qty = s.quantity || 1;
                var sColor = s.service_color || '';
                if (!sColor && s.service_id && selectServicesData && selectServicesData.length > 0) {
                    var svcInfo = selectServicesData.find(function(d) { return d.id === s.service_id; });
                    if (svcInfo && svcInfo.color) sColor = svcInfo.color;
                }
                var colorDot = sColor ? '<span class="inline-block w-2 h-2 rounded-full flex-shrink-0" style="background:' + sColor + '"></span>' : '';
                return '<span class="inline-flex items-center gap-1 text-xs text-gray-500 truncate">' + colorDot + '<span class="truncate">' + sName + (qty > 1 ? ' x' + qty : '') + '</span></span>';
            });
            svcListHtml = '<div class="mt-1 grid grid-cols-4 gap-1">' + svcItems.join('') + '</div>';
        }
        return '<div class="p-2 bg-white rounded-lg border border-gray-200">'
            + '<div class="flex items-center gap-3">'
            + '<div class="flex-shrink-0">' + avatarHtml + '</div>'
            + '<div class="flex-1 min-w-0">'
            + '<p class="text-sm font-medium text-gray-900 truncate">' + name + '</p>'
            + '<p class="text-xs text-gray-500">Services: ' + serviceCount + '</p>'
            + '</div>'
            + '<button onclick="event.stopPropagation(); salonTicketsOpenSelectServices(' + appointmentId + ', ' + techId + ', \'' + name.replace(/'/g, "\\'") + '\')" class="px-3 py-1.5 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition-all font-medium text-xs flex items-center gap-1 flex-shrink-0">'
            + '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>'
            + 'Select</button>'
            + '</div>'
            + svcListHtml
            + '</div>';
    }).join('') + '</div>';
}
function updateTicketTechnicianDisplay() {
    if (!currentEventModalElement || !currentAppointmentId) return;
    currentEventModalElement.innerHTML = buildTicketTechnicianDisplayHtml(currentAppointmentId);
}

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
    var aptId2 = appointment.appointmentId || appointment.id;
    currentAppointmentId = aptId2;
    currentCustomerName = customerName;
    assignedTechnicianIds = (appointment.assigned_technician && Array.isArray(appointment.assigned_technician)) ? appointment.assigned_technician.map(function(id) { return id.toString(); }) : [];
    var customerId = appointment.customer_id;
    var customer = customerId ? allCustomers.find(function(c) { return c.id.toString() === customerId.toString(); }) : null;
    var fullName = customer ? customer.firstName + ' ' + customer.lastName : (appointment.firstName && appointment.lastName ? appointment.firstName + ' ' + appointment.lastName : customerName);
    var customerPhone = customer ? (customer.phone || 'No phone') : (appointment.phone || 'No phone');
    var customerEmail = customer ? (customer.email || 'No email') : (appointment.email || 'No email');
    var payment = appointment.payment || null;
    var paymentAmount = payment ? window.salonFormatMoney(parseFloat(payment.amount)) : window.salonFormatMoney(0);
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
            var serviceName = s.service_name || (s.service ? s.service.replace(/-/g, ' ').replace(/\b\w/g, function(l) { return l.toUpperCase(); }) : 'Unknown');
            var sColor = s.service_color || '';
            if (!sColor && s.service_id && selectServicesData && selectServicesData.length > 0) {
                var svcInfo = selectServicesData.find(function(d) { return d.id === s.service_id; });
                if (svcInfo && svcInfo.color) sColor = svcInfo.color;
            }
            var qty = s.quantity || 1;
            var techId = s.technician_id;
            var techName = 'Not Assigned';
            if (techId) {
                var technician = allTechnicians.find(function(t) { return t.id.toString() === techId.toString(); });
                techName = technician ? technician.firstName + ' ' + technician.lastName : 'Technician #' + techId;
            }
            if (!servicesByTechnician[techName]) servicesByTechnician[techName] = [];
            servicesByTechnician[techName].push({ name: serviceName, color: sColor, quantity: qty });
        });
    }
    var enhancedServicesList = Object.keys(servicesByTechnician).length > 0 ? Object.keys(servicesByTechnician).map(function(techName) {
        var techServices = servicesByTechnician[techName];
        return '<div class="mb-4 last:mb-0"><div class="mb-2"><h5 class="text-sm font-semibold text-gray-900">' + techName + '</h5></div><div class="grid grid-cols-4 gap-1 ml-4">' + techServices.map(function(svc) {
            var colorDot = svc.color ? '<span class="inline-block w-2 h-2 rounded-full flex-shrink-0" style="background:' + svc.color + '"></span>' : '';
            return '<span class="inline-flex items-center gap-1 text-xs text-gray-700 truncate">' + colorDot + '<span class="truncate">' + svc.name + (svc.quantity > 1 ? ' x' + svc.quantity : '') + '</span></span>';
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
    var techDisplayHtml = buildTicketTechnicianDisplayHtml(aptId);
    var escapedFullName = fullName.replace(/'/g, "\\'");
    var content = '<div class="max-h-[90vh] flex flex-col">'
        + '<div class="p-6 border-b border-gray-200 flex items-center justify-between flex-shrink-0"><h3 class="text-2xl font-bold text-gray-900">Ticket Details</h3><div class="flex items-center gap-2"><span class="px-3 py-1.5 rounded-lg text-xs font-semibold border bg-gray-100 text-gray-700 border-gray-200">#' + aptId + '</span><span class="px-3 py-1.5 rounded-lg text-xs font-semibold border ' + statusClass + '">' + statusDisplay + '</span><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div></div>'
        + '<div class="flex-1 min-h-0 overflow-y-auto p-6"><div class="space-y-2">'
        + '<div class="bg-gray-50 rounded-xl p-4"><h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Ticket Information</h4><div class="grid grid-cols-3 gap-4"><div><p class="text-xs text-gray-500 mb-1">Ticket ID</p><p class="text-base font-semibold text-gray-900">#' + aptId + '</p></div><div><p class="text-xs text-gray-500 mb-1">Status</p><span class="inline-block px-3 py-1 ' + statusClass + ' text-xs font-medium rounded-full">' + statusDisplay + '</span></div><div><p class="text-xs text-gray-500 mb-1">Appointment Type</p><p class="text-base font-semibold text-gray-900">' + appointmentTypeDisplay + '</p></div><div><p class="text-xs text-gray-500 mb-1">Date</p><p class="text-base font-semibold text-gray-900">' + appointmentDate + '</p></div><div><p class="text-xs text-gray-500 mb-1">Time</p><p class="text-base font-semibold text-gray-900">' + appointmentTime + '</p></div><div><p class="text-xs text-gray-500 mb-1">Created Date</p><p class="text-base font-semibold text-gray-900">' + createdDate + '</p></div></div></div>'
        + '<div class="bg-gray-50 rounded-xl p-4"><h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Customer Information</h4><div class="grid grid-cols-2 gap-4"><div><p class="text-xs text-gray-500 mb-1">Name</p><p class="text-base font-semibold text-gray-900">' + fullName + '</p></div><div><p class="text-xs text-gray-500 mb-1">Phone</p><p class="text-base font-semibold text-gray-900">' + customerPhone + '</p></div><div><p class="text-xs text-gray-500 mb-1">Email</p><p class="text-base font-semibold text-gray-900">' + customerEmail + '</p></div><div><p class="text-xs text-gray-500 mb-1">Customer ID</p><p class="text-base font-semibold text-gray-900">#' + (appointment.customer_id || 'N/A') + '</p></div></div></div>'
        // Technician section with Assign button and technician cards (like waiting list) - only for unpaid
        + (status === 'unpaid' ? '<div class="bg-gray-50 rounded-xl p-4"><div class="flex items-center justify-between mb-2"><p class="text-sm font-semibold text-gray-500 uppercase tracking-wide">Technicians</p>'
        + '<button onclick="salonTicketsOpenTechnicianModal()" class="px-3 py-1.5 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition-all font-medium text-xs flex items-center gap-1"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>Assign</button>'
        + '</div><div id="ticketTechnicianDisplay_' + aptId + '">' + techDisplayHtml + '</div></div>' : '')
        + (status !== 'unpaid' && status !== 'waiting' ? '<div class="bg-gray-50 rounded-xl p-4"><h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Services</h4><div class="space-y-2">' + enhancedServicesList + '</div></div>' : '')
        + (status !== 'unpaid' && status !== 'waiting' && status !== 'cancelled' && status !== 'canceled' ? '<div class="bg-gray-50 rounded-xl p-4"><h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Payment Information</h4><div class="grid grid-cols-2 gap-4"><div><p class="text-xs text-gray-500 mb-1">Amount</p><p class="text-lg font-bold text-gray-900">' + paymentAmount + '</p></div><div><p class="text-xs text-gray-500 mb-1">Payment Method</p><p class="text-base font-semibold text-gray-900">' + paymentMethod + '</p></div><div><p class="text-xs text-gray-500 mb-1">Payment Status</p><span class="inline-block px-3 py-1 ' + (paymentStatus === 'Completed' ? 'bg-green-100 text-green-700' : paymentStatus === 'Refunded' ? 'bg-gray-100 text-gray-700' : paymentStatus === 'Failed' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') + ' text-xs font-medium rounded-full">' + paymentStatus + '</span></div><div><p class="text-xs text-gray-500 mb-1">Payment Date</p><p class="text-base font-semibold text-gray-900">' + paymentDate + '</p></div>' + (transactionId !== 'N/A' ? '<div><p class="text-xs text-gray-500 mb-1">Transaction ID</p><p class="text-base font-semibold text-gray-900">' + transactionId + '</p></div>' : '') + '</div></div>' : '')
        + (status === 'cancelled' || status === 'canceled' ? '<div class="bg-gray-50 rounded-xl p-4"><div class="flex items-center justify-between flex-wrap gap-3"><div><h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Ticket Status</h4><p class="text-base text-gray-700">This ticket has been cancelled</p></div><div class="flex items-center gap-2"><button onclick="salonTicketsShowRestoreConfirm(\'' + aptId + '\', \'' + escapedFullName + '\')" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium text-sm active:scale-95"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Restore</button><button onclick="salonTicketsShowDeleteConfirm(\'' + aptId + '\', \'' + escapedFullName + '\')" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium text-sm active:scale-95"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>Delete</button></div></div></div>' : '')
        + '</div></div>'
        + '<div class="flex items-center justify-between gap-3 p-6 border-t border-gray-200 flex-shrink-0">'
        + (status === 'paid' ? '<button onclick="salonTicketsConfirmRefund(\'' + aptId + '\', \'' + escapedFullName + '\', \'' + paymentAmount + '\')" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium text-sm active:scale-95"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>Refund</button>' : status === 'unpaid' ? '<button onclick="salonTicketsShowDeleteConfirm(\'' + aptId + '\', \'' + escapedFullName + '\')" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium text-sm active:scale-95"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>Delete</button>' : '<div></div>')
        + '<div class="flex items-center gap-3">'
        + '<button onclick="closeModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">Close</button>'
        + (status === 'unpaid' ? '<a href="' + payUrl + '?id=' + aptId + '" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium active:scale-95 inline-flex items-center gap-2 no-underline"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>Pay</a>' : '')
        + '</div></div></div>';
    openModal(content, 'medium');
    setTimeout(function() {
        currentEventModalElement = document.getElementById('ticketTechnicianDisplay_' + aptId);
    }, 100);
};
window.salonTicketsConfirmRefund = function(appointmentId, customerName, amount) {
    var amountValue = parseFloat(amount.replace(/[^0-9.\-]/g, ''));
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
    var todayStr = new Date().toISOString().slice(0, 16);
    var content = '<div class="p-6"><div class="flex items-center justify-between mb-6"><div class="flex items-center gap-3"><div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center"><svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg></div><div><h3 class="text-xl font-bold text-gray-900">Refund Transaction</h3><p class="text-sm text-gray-500">Confirm refund for ' + customerName + '</p></div></div><button onclick="closeNestedModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><div class="space-y-4"><div class="bg-gray-50 rounded-lg p-4"><h4 class="text-sm font-semibold text-gray-700 mb-3 uppercase">Transaction Details</h4><div class="grid grid-cols-2 gap-4"><div><p class="text-xs text-gray-500 mb-1">Amount</p><p class="text-lg font-bold text-gray-900">' + amount + '</p></div><div><p class="text-xs text-gray-500 mb-1">Payment Method</p><p class="text-sm font-semibold text-gray-900">' + paymentMethod + '</p></div><div><p class="text-xs text-gray-500 mb-1">Transaction ID</p><p class="text-sm font-semibold text-gray-900">' + transactionId + '</p></div></div></div><div class="bg-gray-50 rounded-lg p-4"><h4 class="text-sm font-semibold text-gray-700 mb-3 uppercase">Refund Details</h4><div class="space-y-3"><div><label class="text-xs text-gray-500 mb-1 block">Refund Date & Time</label><input type="datetime-local" id="refundDateInput" value="' + todayStr + '" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none" /></div><div><label class="text-xs text-gray-500 mb-1 block">Notes</label><textarea id="refundNotesInput" rows="3" placeholder="Reason for refund..." class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 outline-none resize-none"></textarea></div></div></div><div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4"><div class="flex items-start gap-3"><svg class="w-5 h-5 text-yellow-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg><div><h4 class="text-sm font-semibold text-yellow-800 mb-1">Refund Warning</h4><p class="text-sm text-yellow-700">This action cannot be undone. The refund will be processed immediately.</p></div></div></div></div><div class="flex justify-end gap-3 mt-6 pt-6 border-t border-gray-200"><button onclick="closeNestedModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">Cancel</button><button onclick="salonTicketsProcessRefund(\'' + appointmentId + '\', \'' + customerName.replace(/'/g, "\\'") + '\', ' + amountValue + ')" class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium active:scale-95">Confirm Refund</button></div></div>';
    openNestedModal(content);
};
window.salonTicketsProcessRefund = function(appointmentId, customerName, amount) {
    var appointment = allMergedData.find(function(apt) {
        return (apt.appointmentId && apt.appointmentId.toString() === appointmentId.toString()) || (apt.id && apt.id.toString() === appointmentId.toString());
    });
    if (!appointment) {
        alert('Appointment not found.');
        return;
    }
    var refundNotes = document.getElementById('refundNotesInput') ? document.getElementById('refundNotesInput').value.trim() : '';
    var refundDate = document.getElementById('refundDateInput') ? document.getElementById('refundDateInput').value : '';
    var paymentId = appointment.payment && (appointment.payment.id || appointment.payment.bookingId);
    var paymentData = { status: 'Refunded' };
    if (refundNotes) paymentData.refund_notes = refundNotes;
    if (refundDate) paymentData.refunded_at = refundDate;
    var appointmentPromise = salonApi.put(apiAppointmentsUrl + '/' + appointmentId, { status: 'refunded' });
    var paymentPromise = paymentId ? salonApi.put(apiPaymentsUrl + '/' + paymentId, paymentData) : Promise.resolve();
    Promise.all([appointmentPromise, paymentPromise]).then(function() {
        appointment.status = 'refunded';
        if (appointment.payment) {
            appointment.payment.status = 'Refunded';
        }
        var idx = allAppointments.findIndex(function(a) { return a.id.toString() === appointmentId.toString(); });
        if (idx >= 0 && allAppointments[idx]) {
            allAppointments[idx].status = 'refunded';
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
        closeNestedModal();
        closeModal();
        showSuccessMessage('Refund of ' + window.salonFormatMoney(amount) + ' has been processed successfully for ' + customerName + '. The ticket has been moved to the "Refunded" tab.');
    }).catch(function(err) {
        showErrorMessage(err.message || 'Failed to update status. Please try again.');
    });
};
window.salonTicketsShowRestoreConfirm = function(appointmentId, customerName) {
    var escapedName = (customerName + '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");
    var content = '<div class="p-6"><div class="flex items-center justify-between mb-6"><div class="flex items-center gap-3"><div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center"><svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg></div><div><h3 class="text-xl font-bold text-gray-900">Restore Ticket</h3><p class="text-sm text-gray-500">Confirm for ' + customerName + '</p></div></div><button onclick="closeNestedModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><div class="space-y-4"><div class="bg-gray-50 rounded-lg p-4"><p class="text-sm text-gray-700">Restore this cancelled ticket? The ticket will be set to <strong>unpaid</strong> and moved to the Unpaid tab.</p></div></div><div class="flex justify-end gap-3 mt-6 pt-6 border-t border-gray-200"><button onclick="closeNestedModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">Cancel</button><button onclick="salonTicketsProcessRestore(\'' + appointmentId + '\', \'' + escapedName + '\')" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium active:scale-95">Confirm</button></div></div>';
    openNestedModal(content, 'small');
};
window.salonTicketsProcessRestore = function(appointmentId, customerName) {
    var appointment = allMergedData.find(function(apt) {
        return (apt.appointmentId && apt.appointmentId.toString() === appointmentId.toString()) || (apt.id && apt.id.toString() === appointmentId.toString());
    });
    if (!appointment) {
        alert('Appointment not found.');
        return;
    }
    salonApi.put(apiAppointmentsUrl + '/' + appointmentId, { status: 'unpaid' }).then(function(res) {
        var data = res.data;
        if (data) {
            var idx = allAppointments.findIndex(function(a) { return a.id.toString() === appointmentId.toString(); });
            if (idx >= 0) {
                allAppointments[idx] = data;
            } else if (data) {
                allAppointments.push(data);
            }
        }
        appointment.status = 'unpaid';
        mergeAppointmentsWithCustomers();
        currentStatusFilter = 'unpaid';
        updateTabStates('unpaid');
        var url = new URL(window.location);
        url.searchParams.set('status', 'unpaid');
        window.history.pushState({}, '', url);
        applyFilters();
        closeNestedModal();
        closeModal();
        showSuccessMessage('Ticket #' + appointmentId + ' has been restored for ' + customerName + '. The ticket has been moved to the "Unpaid" tab.');
    }).catch(function(err) {
        showErrorMessage(err.message || 'Failed to update status. Please try again.');
    });
};
window.salonTicketsShowDeleteConfirm = function(appointmentId, customerName) {
    openConfirmModal({
        title: 'Delete Ticket',
        message: 'You are about to permanently delete ' + (customerName ? boldName(customerName) : 'this ticket') + '. This action cannot be undone. Do you want to continue?',
        confirmLabel: 'Delete',
        nested: true,
        onConfirm: function() { salonTicketsProcessDelete(appointmentId, customerName); }
    });
};
window.salonTicketsProcessDelete = function(appointmentId, customerName) {
    salonApi.delete(apiAppointmentsUrl + '/' + appointmentId).then(function() {
        allAppointments = allAppointments.filter(function(a) { return a.id.toString() !== appointmentId.toString(); });
        mergeAppointmentsWithCustomers();
        applyFilters();
        closeNestedModal();
        closeModal();
        showSuccessMessage('Ticket #' + appointmentId + ' has been deleted.');
    }).catch(function(err) {
        showErrorMessage(err.message || 'Failed to delete ticket. Please try again.');
    });
};
// Open technician selection as nested modal from detail modal
window.salonTicketsOpenTechnicianModal = function() {
    technicianSearchTerm = ''; assignedTechnicianSearchTerm = '';
    // Refresh assigned IDs from current appointment data
    var appointment = allAppointments.find(function(a) { return a.id.toString() === currentAppointmentId.toString(); });
    assignedTechnicianIds = (appointment && Array.isArray(appointment.assigned_technician)) ? appointment.assigned_technician.map(function(id) { return id.toString(); }) : [];
    var modalHtml = '<div class="flex flex-col h-[80vh] max-h-[80vh] overflow-hidden">' +
        '<!-- Fixed Header -->' +
        '<div class="flex-shrink-0 px-4 sm:px-6 py-4 border-b border-gray-200 bg-white">' +
            '<div class="flex items-center justify-between">' +
                '<h3 class="text-xl font-bold text-gray-900">Select Technicians for ' + currentCustomerName + '</h3>' +
                '<button onclick="closeNestedModal()" class="p-2 -m-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2" aria-label="Close">' +
                    '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>' +
                '</button>' +
            '</div>' +
        '</div>' +
        '<!-- Content Area -->' +
        '<div class="flex-1 min-h-0 px-4 sm:px-6 py-4 sm:py-6 bg-gray-50">' +
            '<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6 h-full">' +
                '<!-- Available Technicians Column -->' +
                '<div class="border border-gray-200 rounded-lg p-3 sm:p-4 flex flex-col h-full bg-white">' +
                    '<div class="flex-shrink-0">' +
                        '<div class="flex items-center justify-between mb-2">' +
                            '<h4 class="text-sm font-semibold text-gray-900">Available Technicians</h4>' +
                            '<span id="availableCount" class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">0</span>' +
                        '</div>' +
                        '<p class="text-xs text-gray-500 mb-3">Click to assign technicians</p>' +
                        '<div class="relative mb-4">' +
                            '<svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>' +
                            '<input type="text" id="technicianSearchInput" placeholder="Search available..." oninput="window.salonTicketsSearchTechnicians(this.value)" class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] text-sm">' +
                            '<button id="clearSearchBtn" onclick="window.salonTicketsClearTechnicianSearch()" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition hidden">' +
                                '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>' +
                            '</button>' +
                        '</div>' +
                    '</div>' +
                    '<div id="availableTechniciansContainer" class="overflow-y-auto space-y-3"></div>' +
                '</div>' +
                '<!-- Assigned Technicians Column -->' +
                '<div class="border border-gray-200 rounded-lg p-3 sm:p-4 flex flex-col h-full bg-white">' +
                    '<div class="flex-shrink-0">' +
                        '<div class="flex items-center justify-between mb-2">' +
                            '<h4 class="text-sm font-semibold text-gray-900">Assigned Technicians</h4>' +
                            '<span id="assignedCount" class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">0</span>' +
                        '</div>' +
                        '<p class="text-xs text-gray-500 mb-3">Click to remove technicians</p>' +
                        '<div class="relative mb-4">' +
                            '<svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>' +
                            '<input type="text" id="assignedTechnicianSearchInput" placeholder="Search assigned..." oninput="window.salonTicketsSearchAssignedTechnicians(this.value)" class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] text-sm">' +
                            '<button id="clearAssignedSearchBtn" onclick="window.salonTicketsClearAssignedSearch()" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition hidden">' +
                                '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>' +
                            '</button>' +
                        '</div>' +
                    '</div>' +
                    '<div id="assignedTechniciansContainer" class="overflow-y-auto space-y-3"></div>' +
                '</div>' +
            '</div>' +
        '</div>' +
        '<!-- Fixed Footer -->' +
        '<div class="flex-shrink-0 px-4 sm:px-6 py-4 border-t border-gray-200 bg-white">' +
            '<div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3">' +
                '<button onclick="closeNestedModal()" class="min-w-[5rem] px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95 text-center">' +
                    'Cancel' +
                '</button>' +
                '<button onclick="salonTicketsConfirmAssign()" class="min-w-[5rem] px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95 text-center">' +
                    'Save Assignment' +
                '</button>' +
            '</div>' +
        '</div>' +
    '</div>';
    openNestedModal(modalHtml, 'large-flex', false);
    setTimeout(function() {
        // Setup dynamic height for containers based on screen size
        resizeHandlerForTechnicians = function() {
            var availableContainer = document.getElementById('availableTechniciansContainer');
            var assignedContainer = document.getElementById('assignedTechniciansContainer');
            var screenHeight = window.innerHeight;
            var screenWidth = window.innerWidth;
            var containerHeight;

            if (screenWidth < 640) {
                containerHeight = Math.floor(screenHeight * 0.25) + 'px'; // ~25vh for small screens
            } else if (screenWidth < 1024) {
                containerHeight = Math.floor(screenHeight * 0.30) + 'px'; // ~30vh for medium screens
            } else {
                containerHeight = Math.floor(screenHeight * 0.35) + 'px'; // ~35vh for large screens
            }

            if (availableContainer) availableContainer.style.height = containerHeight;
            if (assignedContainer) assignedContainer.style.height = containerHeight;
        };

        resizeHandlerForTechnicians();
        window.addEventListener('resize', resizeHandlerForTechnicians);

        salonTicketsLoadTechnicians();
    }, 50);
};
window.salonTicketsCloseAssignModal = function() {
    if (resizeHandlerForTechnicians) {
        window.removeEventListener('resize', resizeHandlerForTechnicians);
        resizeHandlerForTechnicians = null;
    }
    assignedTechnicianSearchTerm = '';
    technicianSearchTerm = '';
    closeNestedModal();
};
function fetchTurnTrackerOrder() {
    var turnTrackerUrl = base.replace(/\/data\/?$/, '') + '/turn-tracker';
    return fetch(turnTrackerUrl, { credentials: 'same-origin' }).then(function(r) { return r.json(); }).then(function(data) {
        turnTrackerOrder = data.turn_tracker_order === 'highest' ? 'highest' : 'lowest';
        var entries = (data.entries || []).map(function(e) {
            return { user_id: e.user_id, services: typeof e.services === 'number' ? e.services : parseFloat(e.services) || 0, clock_in: e.clock_in || null };
        });
        entries.sort(function(a, b) {
            var diff = a.services - b.services;
            if (turnTrackerOrder === 'highest') diff = -diff;
            if (diff !== 0) return diff;
            var aTime = a.clock_in ? new Date(a.clock_in).getTime() : 0;
            var bTime = b.clock_in ? new Date(b.clock_in).getTime() : 0;
            return aTime - bTime;
        });
        turnTrackerUserIds = new Set(entries.map(function(e) { return e.user_id; }));
        turnTrackerPositions = new Map();
        entries.forEach(function(e, i) { turnTrackerPositions.set(e.user_id, i); });
    }).catch(function(err) { console.error('Error fetching turn tracker order:', err); });
}
window.salonTicketsLoadTechnicians = function() {
    fetch(base + '/users').then(function(r) { return r.json(); }).then(function(data) {
        availableTechnicians = (data.users || []).filter(function(u) { return u.role === 'technician' || u.userlevel === 'technician'; });
        originalTechnicianOrder = availableTechnicians.map(function(t) { return t.id; });
        return fetchTurnTrackerOrder();
    }).then(function() {
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
    var clearBtn = document.getElementById('clearSearchBtn');
    if (clearBtn) {
        if (val.trim()) clearBtn.classList.remove('hidden');
        else clearBtn.classList.add('hidden');
    }
    salonTicketsRenderAvailableTechnicians();
};
window.salonTicketsClearTechnicianSearch = function() {
    var searchInput = document.getElementById('technicianSearchInput');
    var clearBtn = document.getElementById('clearSearchBtn');
    if (searchInput) { searchInput.value = ''; technicianSearchTerm = ''; searchInput.focus(); }
    if (clearBtn) clearBtn.classList.add('hidden');
    salonTicketsRenderAvailableTechnicians();
};
window.salonTicketsSearchAssignedTechnicians = function(val) {
    assignedTechnicianSearchTerm = (val || '').toLowerCase().trim();
    var btn = document.getElementById('clearAssignedSearchBtn');
    if (btn) {
        if (val.trim()) btn.classList.remove('hidden');
        else btn.classList.add('hidden');
    }
    salonTicketsRenderAssignedTechnicians();
};
window.salonTicketsClearAssignedSearch = function() {
    var inp = document.getElementById('assignedTechnicianSearchInput');
    var btn = document.getElementById('clearAssignedSearchBtn');
    if (inp) { inp.value = ''; assignedTechnicianSearchTerm = ''; inp.focus(); }
    if (btn) btn.classList.add('hidden');
    salonTicketsRenderAssignedTechnicians();
};
window.salonTicketsRenderAvailableTechnicians = function() {
    var container = document.getElementById('availableTechniciansContainer');
    if (!container) return;
    if (!availableTechnicians.length) {
        container.innerHTML = '<div class="flex items-center justify-center h-full min-h-[200px]"><p class="text-sm text-gray-400">No technicians available</p></div>';
        return;
    }
    var filtered = technicianSearchTerm ? availableTechnicians.filter(function(t) {
        var name = (t.firstName + ' ' + t.lastName).toLowerCase();
        var inits = (t.initials || (t.firstName || '')[0] + (t.lastName || '')[0]).toLowerCase();
        return (name + ' ' + inits).indexOf(technicianSearchTerm) >= 0;
    }) : availableTechnicians;
    if (!filtered.length) {
        container.innerHTML = '<div class="flex items-center justify-center h-full min-h-[200px]"><p class="text-sm text-gray-400">No technicians found</p></div>';
        return;
    }
    filtered.sort(function(a, b) {
        // Assigned technicians go last
        var aIdStr = a.id.toString(), bIdStr = b.id.toString();
        var aIsAssigned = assignedTechnicianIds.indexOf(aIdStr) >= 0;
        var bIsAssigned = assignedTechnicianIds.indexOf(bIdStr) >= 0;
        if (aIsAssigned && !bIsAssigned) return 1;
        if (!aIsAssigned && bIsAssigned) return -1;

        // Turn tracker technicians first, in exact turn tracker order; non-tracker last
        var aInTracker = turnTrackerUserIds.has(a.id);
        var bInTracker = turnTrackerUserIds.has(b.id);
        if (aInTracker && !bInTracker) return -1;
        if (!aInTracker && bInTracker) return 1;
        if (aInTracker && bInTracker) {
            return (turnTrackerPositions.get(a.id) || 0) - (turnTrackerPositions.get(b.id) || 0);
        }
        return 0;
    });
    var badgeStyle = 'bottom: -5px; right: -5px;';
    var html = '';
    filtered.forEach(function(technician) {
        var techIdStr = technician.id.toString();
        var isAssigned = assignedTechnicianIds.indexOf(techIdStr) >= 0;
        var initials = technician.initials || (technician.firstName || '')[0] + (technician.lastName || '')[0];
        var fullName = technician.firstName + ' ' + technician.lastName;
        var techPhoto = technician.profilePhotoUrl || technician.photo || null;
        var containerClasses = isAssigned ? 'flex items-center gap-3 p-2 rounded-lg transition-colors opacity-50 grayscale cursor-pointer group hover:bg-gray-100' : 'flex items-center gap-3 cursor-pointer group hover:bg-gray-50 p-2 rounded-lg transition-colors';
        var avatarClasses = isAssigned ? 'w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center' : 'w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center';
        var initialClasses = isAssigned ? 'text-sm font-bold text-gray-500' : 'text-sm font-bold text-gray-600';
        var nameClasses = isAssigned ? 'text-base font-medium text-gray-400' : 'text-base font-medium text-gray-900';
        var isOnline = !!(technician.clock_in && !technician.clock_out);
        var badgeClasses = isAssigned ? 'absolute w-5 h-5 rounded-full border-2 border-white bg-gray-400' : (isOnline ? 'absolute w-5 h-5 rounded-full border-2 border-white bg-green-500' : 'absolute w-5 h-5 rounded-full border-2 border-white bg-gray-400');
        var servicesNum = typeof technician.services === 'number' ? technician.services : 0;
        var modalHoverAttr = techPhoto ? ' onmouseenter="showTechPhotoPreview(event, \'' + techPhoto.replace(/'/g, "\\'").replace(/"/g, '&quot;') + '\', \'' + fullName.replace(/'/g, "\\'") + '\')" onmouseleave="hideTechPhotoPreview()"' : '';
        var avatarHtml = techPhoto
            ? '<img src="' + techPhoto.replace(/"/g, '&quot;') + '" alt="" class="w-12 h-12 rounded-full object-cover' + (isAssigned ? ' opacity-50 grayscale' : '') + ' cursor-pointer"' + modalHoverAttr + '>'
            : '<div class="' + avatarClasses + '"><span class="' + initialClasses + '">' + initials + '</span></div>';
        html += '<div onclick="' + (isAssigned ? 'salonTicketsRemoveAssignedTechnician(' + technician.id + ')' : 'salonTicketsAssignTechnician(' + technician.id + ')') + '" class="' + containerClasses + '"><div class="relative flex-shrink-0">' + avatarHtml + '<div class="' + badgeClasses + '" style="' + badgeStyle + '" title="' + (isOnline ? 'Online' : 'Offline') + '"></div></div><div class="flex-1 min-w-0"><p class="' + nameClasses + '">' + fullName + '</p></div><div class="flex-shrink-0 text-right"><div class="text-xs font-medium text-gray-500 uppercase">Services</div><div class="text-lg font-semibold text-gray-900">' + servicesNum + '</div></div></div>';
    });
    container.innerHTML = html;
};
window.salonTicketsRenderAssignedTechnicians = function() {
    var container = document.getElementById('assignedTechniciansContainer');
    if (!container) return;
    if (!assignedTechnicianIds.length) {
        container.innerHTML = '<div class="flex items-center justify-center h-full min-h-[200px]"><p class="text-sm text-gray-400">No technicians assigned</p></div>';
        return;
    }

    // Get assigned technicians from IDs
    var assignedTechs = assignedTechnicianIds.map(function(idStr) {
        return availableTechnicians.find(function(t) { return t.id.toString() === idStr; });
    }).filter(function(t) { return t != null; });

    // Apply search filter if search term exists
    if (assignedTechnicianSearchTerm) {
        assignedTechs = assignedTechs.filter(function(t) {
            var name = (t.firstName + ' ' + t.lastName).toLowerCase();
            var inits = (t.initials || (t.firstName || '')[0] + (t.lastName || '')[0]).toLowerCase();
            return (name + ' ' + inits).indexOf(assignedTechnicianSearchTerm) >= 0;
        });
    }

    if (!assignedTechs.length) {
        container.innerHTML = '<div class="flex items-center justify-center h-full min-h-[200px]"><p class="text-sm text-gray-400">No technicians found</p></div>';
        return;
    }

    var badgeStyle = 'bottom: -5px; right: -5px;';
    var html = '';
    assignedTechs.forEach(function(technician) {
        var initials = technician.initials || (technician.firstName || '')[0] + (technician.lastName || '')[0];
        var fullName = technician.firstName + ' ' + technician.lastName;
        var techPhoto = technician.profilePhotoUrl || technician.photo || null;
        var isOnline = !!(technician.clock_in && !technician.clock_out);
        var badgeClasses = isOnline ? 'absolute w-5 h-5 rounded-full border-2 border-white bg-green-500' : 'absolute w-5 h-5 rounded-full border-2 border-white bg-gray-400';
        var servicesNum = typeof technician.services === 'number' ? technician.services : 0;
        var assignedHoverAttr = techPhoto ? ' onmouseenter="showTechPhotoPreview(event, \'' + techPhoto.replace(/'/g, "\\'").replace(/"/g, '&quot;') + '\', \'' + fullName.replace(/'/g, "\\'") + '\')" onmouseleave="hideTechPhotoPreview()"' : '';
        var assignedAvatarHtml = techPhoto
            ? '<img src="' + techPhoto.replace(/"/g, '&quot;') + '" alt="" class="w-12 h-12 rounded-full object-cover cursor-pointer"' + assignedHoverAttr + '>'
            : '<div class="w-12 h-12 bg-[#003047] rounded-full flex items-center justify-center"><span class="text-sm font-bold text-white">' + initials + '</span></div>';
        html += '<div onclick="salonTicketsRemoveAssignedTechnician(' + technician.id + ')" class="flex items-center gap-3 cursor-pointer group hover:bg-gray-50 p-2 rounded-lg transition-colors"><div class="relative flex-shrink-0">' + assignedAvatarHtml + '<div class="' + badgeClasses + '" style="' + badgeStyle + '" title="' + (isOnline ? 'Online' : 'Offline') + '"></div></div><div class="flex-1 min-w-0"><p class="text-base font-medium text-gray-900">' + fullName + '</p></div><div class="flex-shrink-0 text-right"><div class="text-xs font-medium text-gray-500 uppercase">Services</div><div class="text-lg font-semibold text-gray-900">' + servicesNum + '</div></div></div>';
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
    if (!currentAppointmentId) {
        showErrorMessage('Appointment not found.');
        return;
    }
    var payload = { assigned_technician: assignedTechnicianIds.map(function(id) { return parseInt(id, 10); }) };
    var btn = document.querySelector('[onclick*="salonTicketsConfirmAssign"]');
    if (btn) { btn.disabled = true; btn.textContent = 'Saving...'; }

    // Detect removed technicians for service cleanup
    var appointment = allAppointments.find(function(a) { return a.id.toString() === currentAppointmentId.toString(); });
    var prevTechIds = (appointment && Array.isArray(appointment.assigned_technician)) ? appointment.assigned_technician.map(function(id) { return id.toString(); }) : [];
    var removedTechIds = prevTechIds.filter(function(id) { return !assignedTechnicianIds.some(function(t) { return t.toString() === id; }); });
    var allTechsCleared = assignedTechnicianIds.length === 0;

    salonApi.put(apiAppointmentsUrl + '/' + currentAppointmentId, payload).then(function(res) {
        var data = res.data;
        var idx = allAppointments.findIndex(function(a) { return a.id === currentAppointmentId; });
        if (idx >= 0 && data) allAppointments[idx] = data;
        else if (data) allAppointments.push(data);

        // Clean up appointment_services for removed technicians
        var aptData = allAppointments.find(function(a) { return a.id.toString() === currentAppointmentId.toString(); });
        var hasRemovedTechs = removedTechIds.length > 0;
        if ((hasRemovedTechs || allTechsCleared) && aptData && Array.isArray(aptData.services) && aptData.services.length > 0) {
            var keepServices = allTechsCleared ? [] : aptData.services.filter(function(s) {
                return s.technician_id && !removedTechIds.some(function(rid) { return rid === s.technician_id.toString(); });
            });
            var svcPayload = keepServices.map(function(s) {
                var svcData = selectServicesData.length > 0 ? selectServicesData.find(function(d) { return d.id === s.service_id; }) : null;
                return { service: (svcData && svcData.categories && svcData.categories[0]) || s.service || '', service_id: s.service_id, technician_id: s.technician_id, quantity: s.quantity || 1, unit_price: s.unit_price };
            });
            var svcUrl = base.replace(/\/data\/?$/, '') + '/appointments/' + currentAppointmentId + '/services';
            salonApi.put(svcUrl, { services: svcPayload }).then(function(svcRes) {
                if (aptData && svcRes && svcRes.data && Array.isArray(svcRes.data.services)) aptData.services = svcRes.data.services;
                updateTicketTechnicianDisplay();
            }).catch(function(err) { console.error('Service cleanup failed:', err); });
        }

        mergeAppointmentsWithCustomers();
        applyFilters();
        var assignedNames = assignedTechnicianIds.map(function(id) {
            var tech = availableTechnicians.find(function(t) { return t.id.toString() === id; });
            return tech ? tech.firstName + ' ' + tech.lastName : '';
        }).filter(Boolean);
        var message = res.message || (currentCustomerName + ' assigned to ' + assignedNames.join(', ') + ' successfully.');
        showSuccessMessage(message);
        closeNestedModal();
        updateTicketTechnicianDisplay();
    }).catch(function(err) {
        showErrorMessage(err.message || 'Failed to save assignment.');
    }).finally(function() {
        if (btn) { btn.disabled = false; btn.textContent = 'Save Assignment'; }
    });
};
// --- Select Services Modal ---
window.salonTicketsOpenSelectServices = function(appointmentId, technicianId, technicianName) {
    selectServicesAppointmentId = appointmentId;
    selectServicesTechnicianId = technicianId;
    selectServicesTechnicianName = technicianName;
    selectServicesCategory = null;
    selectServicesCart = [];
    selectServicesOldServiceCount = 0;

    var appointment = allAppointments.find(function(a) { return a.id.toString() === appointmentId.toString(); });
    var existingSvcs = appointment && Array.isArray(appointment.services) ? appointment.services : [];
    var techSvcs = existingSvcs.filter(function(s) { return s.technician_id && s.technician_id.toString() === technicianId.toString(); });

    var modalContent = '<div class="flex flex-col h-[80vh] max-h-[80vh] overflow-hidden">'
        + '<div class="flex-shrink-0 px-6 py-4 border-b border-gray-200 bg-white"><div class="flex items-center justify-between"><div><h3 class="text-xl font-bold text-gray-900">Select Services</h3><p class="text-sm text-gray-500">' + technicianName + '</p></div>'
        + '<button onclick="closeNestedModal()" class="p-2 -m-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div></div>'
        + '<div class="flex-1 min-h-0 px-6 py-4 bg-gray-50 overflow-y-auto">'
        + '<div class="mb-4"><div class="relative"><svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>'
        + '<input type="text" id="selectServicesSearchInput" placeholder="Search services..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] text-sm" oninput="salonTicketsRenderSelectServicesGrid()"></div></div>'
        + '<div class="select-svc-carousel-wrapper mb-4"><div id="selectServicesCategoriesList" class="select-svc-slick-carousel"></div></div>'
        + '<div id="selectServicesGrid" class="grid grid-cols-2 sm:grid-cols-4 gap-3"></div></div>'
        + '<div class="flex-shrink-0 px-6 py-4 border-t border-gray-200 bg-white">'
        + '<div id="selectServicesCartSummary" class="mb-3"></div>'
        + '<div class="flex items-center justify-end gap-3">'
        + '<button onclick="closeNestedModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium">Cancel</button>'
        + '<button id="selectServicesSaveBtn" onclick="salonTicketsSaveSelectServicesCart()" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium flex items-center gap-2">Save Services</button>'
        + '</div></div></div>';

    openNestedModal(modalContent, 'large-flex', false);

    function populateCartFromExisting() {
        techSvcs.forEach(function(s) {
            var svcData = selectServicesData.find(function(d) { return d.id === s.service_id; });
            var sc = svcData && typeof svcData.service_count === 'number' ? svcData.service_count : 0;
            selectServicesCart.push({
                service_id: s.service_id,
                name: s.service_name || (svcData ? svcData.name : s.service) || 'Service',
                price: s.unit_price != null ? s.unit_price : (svcData ? svcData.price : 0),
                quantity: s.quantity || 1,
                category_slug: (svcData && svcData.categories && svcData.categories[0]) || s.service || '',
                service_count: sc
            });
            selectServicesOldServiceCount += sc * (s.quantity || 1);
        });
    }

    if (selectServicesLoaded) {
        populateCartFromExisting();
        salonTicketsRenderSelectServicesCategories();
        salonTicketsRenderSelectServicesGrid();
    } else {
        Promise.all([
            fetch(base + '/services').then(function(r) { return r.json(); }),
            fetch(base + '/service-categories').then(function(r) { return r.json(); })
        ]).then(function(results) {
            selectServicesData = (results[0].services || []).filter(function(s) { return s.active !== false; });
            selectServicesCategoriesMap = results[1].categories || results[1] || {};
            selectServicesLoaded = true;
            populateCartFromExisting();
            salonTicketsRenderSelectServicesCategories();
            salonTicketsRenderSelectServicesGrid();
        }).catch(function(err) { console.error('Failed to load services:', err); });
    }
};

function salonTicketsRenderSelectServicesCategories() {
    var container = document.getElementById('selectServicesCategoriesList');
    if (!container) return;
    var $c = $(container);
    if ($c.hasClass('slick-initialized')) { try { $c.slick('unslick'); } catch (e) {} }
    var activeClass = 'bg-[#e6f0f3] border-[#003047] text-[#003047]';
    var inactiveClass = 'bg-white border-gray-200 text-gray-700 hover:border-[#003047] hover:bg-[#e6f0f3] hover:text-[#003047]';
    var html = '<div><button type="button" onclick="salonTicketsFilterServiceCategory(null)" class="select-svc-cat-card w-full h-[70px] px-4 py-2 rounded-lg text-sm font-medium border transition-all duration-200 flex items-center justify-center text-center break-words active:scale-95 ' + (selectServicesCategory === null ? activeClass : inactiveClass) + '" data-category-key="all">All Categories</button></div>';
    var sorted = Object.entries(selectServicesCategoriesMap).sort(function(a, b) { return (a[1] || '').localeCompare(b[1] || ''); });
    sorted.forEach(function(entry) {
        html += '<div><button type="button" onclick="salonTicketsFilterServiceCategory(\'' + entry[0] + '\')" class="select-svc-cat-card w-full h-[70px] px-4 py-2 rounded-lg text-sm font-medium border transition-all duration-200 flex items-center justify-center text-center break-words active:scale-95 ' + (selectServicesCategory === entry[0] ? activeClass : inactiveClass) + '" data-category-key="' + entry[0] + '">' + entry[1] + '</button></div>';
    });
    container.innerHTML = html;
    setTimeout(function() {
        if (typeof $ !== 'undefined' && typeof $.fn.slick !== 'undefined') {
            $c.slick({ slidesToShow: 6, slidesToScroll: 6, infinite: false, arrows: true, dots: false, adaptiveHeight: false, variableWidth: false, responsive: [{ breakpoint: 1024, settings: { slidesToShow: 4, slidesToScroll: 4 } }, { breakpoint: 640, settings: { slidesToShow: 2, slidesToScroll: 2 } }] });
            $c.css({ opacity: '1', visibility: 'visible' });
        } else { container.classList.add('show-fallback'); container.style.opacity = '1'; container.style.visibility = 'visible'; }
    }, 50);
}

window.salonTicketsFilterServiceCategory = function(key) {
    selectServicesCategory = key;
    document.querySelectorAll('.select-svc-cat-card').forEach(function(card) {
        var cardKey = card.getAttribute('data-category-key');
        var isActive = (key === null && cardKey === 'all') || (key === cardKey);
        if (isActive) { card.classList.remove('bg-white', 'border-gray-200', 'text-gray-700'); card.classList.add('bg-[#e6f0f3]', 'border-[#003047]', 'text-[#003047]'); }
        else { card.classList.remove('bg-[#e6f0f3]', 'border-[#003047]', 'text-[#003047]'); card.classList.add('bg-white', 'border-gray-200', 'text-gray-700'); }
    });
    salonTicketsRenderSelectServicesGrid();
};

window.salonTicketsRenderSelectServicesGrid = function() {
    var container = document.getElementById('selectServicesGrid');
    if (!container) return;
    var filtered = selectServicesData;
    if (selectServicesCategory !== null) filtered = filtered.filter(function(s) { return s.categories && s.categories.indexOf(selectServicesCategory) >= 0; });
    var searchInput = document.getElementById('selectServicesSearchInput');
    if (searchInput && searchInput.value.trim() !== '') { var term = searchInput.value.toLowerCase(); filtered = filtered.filter(function(s) { return s.name.toLowerCase().indexOf(term) >= 0; }); }
    if (filtered.length === 0) { container.innerHTML = '<div class="col-span-full text-center py-8"><p class="text-gray-500 text-sm">No services found</p></div>'; return; }
    var html = '';
    filtered.forEach(function(service) {
        var cartItem = selectServicesCart.find(function(c) { return c.service_id === service.id; });
        var imgUrl = service.image_url || null;
        var thumbHtml = imgUrl
            ? '<div class="relative w-full bg-gray-100 rounded-t-lg overflow-hidden" style="height:120px"><img src="' + imgUrl + '" alt="" class="w-full h-full object-cover" onerror="this.parentNode.innerHTML=\'<div class=\\\'w-full h-full flex items-center justify-center bg-gray-100\\\'><svg class=\\\'w-8 h-8 text-gray-300\\\' fill=\\\'none\\\' stroke=\\\'currentColor\\\' viewBox=\\\'0 0 24 24\\\'><path stroke-linecap=\\\'round\\\' stroke-linejoin=\\\'round\\\' stroke-width=\\\'1.5\\\' d=\\\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\\\'></path></svg></div>\'"></div>'
            : '<div class="w-full flex items-center justify-center rounded-t-lg" style="height:120px;background:' + (service.color || '#f3f4f6') + '"><svg class="w-8 h-8 ' + (service.color ? 'text-white opacity-50' : 'text-gray-300') + '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></div>';
        var colorDot = service.color ? '<span class="inline-block w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:' + service.color + '"></span>' : '';
        html += '<div class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:border-[#003047] hover:shadow-md transition-all flex flex-col">'
            + thumbHtml + '<div class="p-3 flex flex-col flex-1">'
            + '<h4 class="text-sm font-semibold text-gray-900 mb-1 flex items-center gap-1.5">' + colorDot + service.name + '</h4>'
            + '<p class="text-sm text-gray-600 mb-2">' + window.salonFormatMoney(service.price) + '</p>'
            + (cartItem
                ? '<div class="flex items-center justify-between mt-auto bg-gray-100 rounded-lg p-1">'
                + '<button onclick="salonTicketsSelectServicesUpdateQty(' + service.id + ', -1)" class="w-8 h-8 flex items-center justify-center bg-white text-gray-700 hover:bg-red-50 hover:text-red-600 rounded-md border border-gray-200 shadow-sm transition active:scale-95"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg></button>'
                + '<span class="text-sm font-bold text-gray-900 min-w-[2rem] text-center">' + cartItem.quantity + '</span>'
                + '<button onclick="salonTicketsSelectServicesUpdateQty(' + service.id + ', 1)" class="w-8 h-8 flex items-center justify-center bg-white text-gray-700 hover:bg-green-50 hover:text-green-600 rounded-md border border-gray-200 shadow-sm transition active:scale-95"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg></button></div>'
                : '<button onclick="salonTicketsSelectServicesAddToCart(' + service.id + ')" class="w-full px-3 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-xs active:scale-95 mt-auto">Add</button>')
            + '</div></div>';
    });
    container.innerHTML = html;
    salonTicketsRenderSelectServicesCartSummary();
};

window.salonTicketsSelectServicesAddToCart = function(serviceId) {
    var service = selectServicesData.find(function(s) { return s.id === serviceId; });
    if (!service) return;
    var existing = selectServicesCart.find(function(c) { return c.service_id === serviceId; });
    if (existing) { existing.quantity += 1; }
    else { selectServicesCart.push({ service_id: serviceId, name: service.name, price: service.price, quantity: 1, category_slug: (service.categories && service.categories[0]) || '', service_count: typeof service.service_count === 'number' ? service.service_count : 0 }); }
    salonTicketsRenderSelectServicesGrid();
};
window.salonTicketsSelectServicesUpdateQty = function(serviceId, delta) {
    var item = selectServicesCart.find(function(c) { return c.service_id === serviceId; });
    if (!item) return;
    item.quantity += delta;
    if (item.quantity <= 0) selectServicesCart = selectServicesCart.filter(function(c) { return c.service_id !== serviceId; });
    salonTicketsRenderSelectServicesGrid();
};
window.salonTicketsSelectServicesRemoveFromCart = function(serviceId) {
    selectServicesCart = selectServicesCart.filter(function(c) { return c.service_id !== serviceId; });
    salonTicketsRenderSelectServicesGrid();
};

function salonTicketsRenderSelectServicesCartSummary() {
    var container = document.getElementById('selectServicesCartSummary');
    if (!container) return;
    if (selectServicesCart.length === 0) { container.innerHTML = '<p class="text-sm text-gray-400">No services selected</p>'; return; }
    var total = 0;
    var html = '<div class="overflow-y-auto space-y-1" style="max-height:150px">';
    selectServicesCart.forEach(function(item) {
        var lineTotal = item.price * item.quantity; total += lineTotal;
        html += '<div class="flex items-center justify-between text-sm"><div class="flex items-center gap-2">'
            + '<button onclick="salonTicketsSelectServicesRemoveFromCart(' + item.service_id + ')" class="w-5 h-5 flex items-center justify-center rounded-full bg-red-100 text-red-500 hover:bg-red-200 transition flex-shrink-0"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>'
            + '<span class="text-gray-700">' + item.name + ' &times; ' + item.quantity + '</span></div>'
            + '<span class="font-medium text-gray-900">' + window.salonFormatMoney(lineTotal) + '</span></div>';
    });
    html += '</div><div class="flex items-center justify-between text-sm font-bold pt-1 border-t border-gray-200 mt-1"><span>Total</span><span>' + window.salonFormatMoney(total) + '</span></div>';
    container.innerHTML = html;
}

window.salonTicketsSaveSelectServicesCart = function() {
    var saveBtn = document.getElementById('selectServicesSaveBtn');
    if (saveBtn) { saveBtn.disabled = true; saveBtn.classList.add('opacity-50', 'cursor-not-allowed'); saveBtn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Saving...'; }
    function resetSaveBtn() { if (saveBtn) { saveBtn.disabled = false; saveBtn.classList.remove('opacity-50', 'cursor-not-allowed'); saveBtn.innerHTML = 'Save Services'; } }

    var appointment = allAppointments.find(function(a) { return a.id.toString() === selectServicesAppointmentId.toString(); });
    var existingSvcs = appointment && Array.isArray(appointment.services) ? appointment.services : [];
    var otherTechSvcs = existingSvcs.filter(function(s) { return !s.technician_id || s.technician_id.toString() !== selectServicesTechnicianId.toString(); }).map(function(s) {
        var svcData = selectServicesData.find(function(d) { return d.id === s.service_id; });
        return { service: (svcData && svcData.categories && svcData.categories[0]) || s.service || '', service_id: s.service_id, technician_id: s.technician_id, quantity: s.quantity || 1, unit_price: s.unit_price };
    });
    var currentTechSvcs = selectServicesCart.map(function(item) { return { service: item.category_slug, service_id: item.service_id, technician_id: selectServicesTechnicianId, quantity: item.quantity, unit_price: item.price }; });
    var servicesPayload = otherTechSvcs.concat(currentTechSvcs).filter(function(s) { return s.service && s.technician_id; });
    var apiUrl = base.replace(/\/data\/?$/, '') + '/appointments/' + selectServicesAppointmentId + '/services';

    if (typeof salonApi !== 'undefined' && salonApi.put) {
        salonApi.put(apiUrl, { services: servicesPayload }).then(function(res) {
            if (appointment && res && res.data && Array.isArray(res.data.services)) appointment.services = res.data.services;
            // Update turn tracker
            var tech = allTechnicians.find(function(t) { return t.id === selectServicesTechnicianId; });
            var baseServices = tech && typeof tech.services === 'number' ? tech.services : 0;
            var newCartServiceCount = selectServicesCart.reduce(function(sum, item) { return sum + ((item.service_count || 0) * item.quantity); }, 0);
            var newTotal = baseServices - selectServicesOldServiceCount + newCartServiceCount;
            var turnTrackerUrl = base.replace(/\/data\/?$/, '') + '/turn-tracker';
            salonApi.put(turnTrackerUrl, { entries: [{ user_id: selectServicesTechnicianId, services: newTotal }] }).then(function() {
                if (tech) tech.services = newTotal;
                updateTicketTechnicianDisplay();
            }).catch(function(err) { console.error('Turn tracker update failed:', err); });
            if (typeof showSuccessMessage === 'function') showSuccessMessage('Services saved for ' + selectServicesTechnicianName + '.');
            closeNestedModal();
            updateTicketTechnicianDisplay();
        }).catch(function(err) { resetSaveBtn(); if (typeof showErrorMessage === 'function') showErrorMessage(err && err.message ? err.message : 'Failed to save services.'); });
    }
};

async function fetchTickets() {
    try {
        if (window.salonTicketsBootstrap && window.salonTicketsBootstrap.appointments) {
            var boot = window.salonTicketsBootstrap || {};
            allCustomers = boot.customers || [];
            allAppointments = boot.appointments || [];
            allTechnicians = (boot.users || []).filter(function(u) { return u.role === 'technician' || u.userlevel === 'technician'; });
            allPayments = boot.payments || [];
        } else {
            var custRes = await fetch(base + '/customers');
            var aptRes = await fetch(base + '/appointments');
            var techRes = await fetch(base + '/users');
            var payRes = await fetch(base + '/payments');
            var custData = await custRes.json();
            var aptData = await aptRes.json();
            var techData = await techRes.json();
            var payData = await payRes.json();
            allCustomers = custData.customers || [];
            allAppointments = aptData.appointments || [];
            allTechnicians = (techData.users || []).filter(function(u) { return u.role === 'technician' || u.userlevel === 'technician'; });
            allPayments = payData.payments || [];
        }
        // Preload services data for color dots in technician display
        if (!selectServicesLoaded) {
            try {
                var svcResults = await Promise.all([
                    fetch(base + '/services').then(function(r) { return r.json(); }),
                    fetch(base + '/service-categories').then(function(r) { return r.json(); })
                ]);
                selectServicesData = (svcResults[0].services || []).filter(function(s) { return s.active !== false; });
                selectServicesCategoriesMap = svcResults[1].categories || svcResults[1] || {};
                selectServicesLoaded = true;
            } catch(e) { console.error('Failed to preload services:', e); }
        }
        mergeAppointmentsWithCustomers();
        currentStatusFilter = getStatusFromURL();
        var _params = new URLSearchParams(window.location.search);
        if (!_params.get('status') || !_params.get('date')) {
            var url = new URL(window.location);
            url.searchParams.set('status', currentStatusFilter);
            url.searchParams.set('date', currentDateFilter);
            window.history.replaceState({}, '', url);
        }
        updateTabStates(currentStatusFilter);
        applyFilters();
        window.addEventListener('popstate', function() {
            var params = new URLSearchParams(window.location.search);
            currentStatusFilter = getStatusFromURL();
            var _n = new Date();
            currentDateFilter = params.get('date') || (_n.getFullYear() + '-' + String(_n.getMonth() + 1).padStart(2, '0') + '-' + String(_n.getDate()).padStart(2, '0'));
            var dateInput = document.getElementById('ticketDateFilterInput');
            if (dateInput) dateInput.value = currentDateFilter;
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
    var dateInput = document.getElementById('ticketDateFilterInput');
    if (dateInput) dateInput.value = currentDateFilter;
    setViewUI(currentView);
    fetchTickets().then(function() {
        salonTicketsToggleView(currentView);
    });
});
window.addEventListener('beforeunload', function() {
    stopDurationCounters();
});
var techPreviewEl = null;
window.showTechPhotoPreview = function(e, url, name) {
    hideTechPhotoPreview();
    var rect = e.target.getBoundingClientRect();
    techPreviewEl = document.createElement('div');
    techPreviewEl.style.cssText = 'position:fixed;z-index:9999;pointer-events:none;';
    var previewSize = 200;
    var arrowSize = 8;
    var totalH = previewSize + 8 + arrowSize;
    var centerX = rect.left + rect.width / 2;
    var left = centerX - (previewSize + 8) / 2;
    var showAbove = rect.top - totalH - 4 >= 0;
    var top;
    if (showAbove) {
        top = rect.top - totalH - 4;
    } else {
        top = rect.bottom + arrowSize + 4;
    }
    if (left < 8) left = 8;
    if (left + previewSize + 8 > window.innerWidth - 8) left = window.innerWidth - previewSize - 16;
    var arrowLeft = centerX - left - arrowSize;
    if (arrowLeft < 12) arrowLeft = 12;
    if (arrowLeft > previewSize - 4) arrowLeft = previewSize - 4;
    var arrowHtml = showAbove
        ? '<div style="position:absolute;bottom:-' + arrowSize + 'px;left:' + arrowLeft + 'px;width:0;height:0;border-left:' + arrowSize + 'px solid transparent;border-right:' + arrowSize + 'px solid transparent;border-top:' + arrowSize + 'px solid #fff;"></div>'
        : '<div style="position:absolute;top:-' + arrowSize + 'px;left:' + arrowLeft + 'px;width:0;height:0;border-left:' + arrowSize + 'px solid transparent;border-right:' + arrowSize + 'px solid transparent;border-bottom:' + arrowSize + 'px solid #fff;"></div>';
    techPreviewEl.innerHTML = '<div style="position:relative;background:#fff;border-radius:0.5rem;box-shadow:0 10px 25px rgba(0,0,0,0.25);padding:4px;">' +
        '<img src="' + url + '" alt="' + name + '" style="width:' + previewSize + 'px;height:' + previewSize + 'px;object-fit:cover;border-radius:0.375rem;display:block;">' +
        arrowHtml + '</div>';
    document.body.appendChild(techPreviewEl);
    techPreviewEl.style.left = left + 'px';
    techPreviewEl.style.top = top + 'px';
};
window.hideTechPhotoPreview = function() {
    if (techPreviewEl) { techPreviewEl.remove(); techPreviewEl = null; }
};
})();
</script>
@endpush
@endsection

@extends('layouts.salon')

@section('content')
@php
    $waitingListTab = request()->query('status', 'all');
    if (! in_array($waitingListTab, ['all', 'walk-in', 'booked'], true)) {
        $waitingListTab = 'all';
    }
@endphp
<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Waiting List</h1>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-1">
                    <button id="gridViewBtn" onclick="toggleView('grid')" class="p-2 rounded-md hover:bg-white transition active:scale-95">
                        <svg class="w-5 h-5 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    </button>
                    <button id="listViewBtn" onclick="toggleView('list')" class="p-2 rounded-md hover:bg-white transition active:scale-95">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    </button>
                </div>
                <button onclick="openNewCustomerModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm sm:text-base active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add New
                </button>
            </div>
        </div>

        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-6 border-b border-gray-200">
                <button onclick="filterByStatus('all')" id="filterAll" class="filter-tab px-1 py-3 text-sm font-medium transition {{ $waitingListTab === 'all' ? 'text-gray-900 border-b-2 border-[#003047]' : 'text-gray-500 border-b-2 border-transparent hover:text-gray-700' }}">All</button>
                <button onclick="filterByStatus('walk-in')" id="filterWalkIn" class="filter-tab px-1 py-3 text-sm font-medium transition {{ $waitingListTab === 'walk-in' ? 'text-gray-900 border-b-2 border-[#003047]' : 'text-gray-500 border-b-2 border-transparent hover:text-gray-700' }}">Walk-In</button>
                <button onclick="filterByStatus('booked')" id="filterBooked" class="filter-tab px-1 py-3 text-sm font-medium transition {{ $waitingListTab === 'booked' ? 'text-gray-900 border-b-2 border-[#003047]' : 'text-gray-500 border-b-2 border-transparent hover:text-gray-700' }}">Booked</button>
            </div>
            <div class="flex items-center gap-3">
                <input type="date" id="dateFilterInput" onchange="filterByDate(this.value)" class="px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-base text-gray-900">
                <div class="relative max-w-md">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" id="customerSearchInput" placeholder="Search customers" oninput="searchCustomers(this.value)" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-base">
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned Technicians</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Appointment</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="listViewBody" class="bg-white divide-y divide-gray-200"></tbody>
                </table>
            </div>
        </div>

        <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div id="customersResultsCounter" class="text-sm text-gray-600"></div>
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-600">Show:</label>
                <select id="perPageSelect" onchange="changePerPage(this.value)" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-sm bg-white cursor-pointer">
                    <option value="15">15</option><option value="25">25</option><option value="50">50</option><option value="100">100</option><option value="250">250</option><option value="500">500</option><option value="all">All</option>
                </select>
                <span class="text-sm text-gray-600">per page</span>
            </div>
        </div>

        <div id="customersPagination" class="mt-4 flex justify-center"></div>
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
window.salonWaitingListBootstrap = window.salonWaitingListBootstrap || @json($waitingListBootstrap ?? null);
var apiCustomersUrl = '{{ url("api/salon/customers") }}';
var apiAppointmentsUrl = '{{ url("api/salon/appointments") }}';
var allCustomers = [], allAppointments = [], allTechnicians = [], allMergedData = [], customersData = [];
var PAGE_SIZE = 15, currentPage = 1, totalPages = 1, currentSearchTerm = '', currentStatusFilter = @json($waitingListTab);
var currentDateFilter = (function() {
    var p = new URLSearchParams(window.location.search).get('date');
    if (p) return p;
    var n = new Date();
    return n.getFullYear() + '-' + String(n.getMonth() + 1).padStart(2, '0') + '-' + String(n.getDate()).padStart(2, '0');
})();
var currentView = localStorage.getItem('customersView') || 'grid';

var colorClasses = [
    { bg: 'bg-[#e6f0f3]', text: 'text-[#003047]' }, { bg: 'bg-purple-100', text: 'text-purple-600' },
    { bg: 'bg-teal-100', text: 'text-teal-600' }, { bg: 'bg-indigo-100', text: 'text-indigo-600' },
    { bg: 'bg-rose-100', text: 'text-rose-600' }, { bg: 'bg-blue-100', text: 'text-blue-600' },
    { bg: 'bg-amber-100', text: 'text-amber-600' }, { bg: 'bg-green-100', text: 'text-green-600' }
];

function getTechnicianInitials(t) { return t.initials || ((t.firstName||'')[0] + (t.lastName||'')[0]).toUpperCase(); }
function getTechnicianNames(ids) {
    if (!ids || !Array.isArray(ids) || !ids.length) return 'Not assigned';
    var names = ids.map(function(id) { var t = allTechnicians.find(function(x) { return x.id === id; }); return t ? t.firstName + ' ' + t.lastName : null; }).filter(Boolean);
    return names.length ? names.join(', ') : 'Not assigned';
}
function renderTechniciansList(technicianIds) {
    if (!technicianIds || !Array.isArray(technicianIds) || !technicianIds.length) return '<span class="text-sm text-gray-400">Not assigned</span>';
    var techs = technicianIds.map(function(id) { return allTechnicians.find(function(t) { return t.id === id; }); }).filter(Boolean);
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
        return Object.assign({}, customer, { appointmentId: apt.id, appointment: type, status: apt.status || 'waiting', created_at: apt.created_at, appointment_datetime: apt.appointment_datetime, assigned_technician: apt.assigned_technician || [], services: apt.services || [] });
    }).filter(Boolean);
}
function formatAppointmentDate(isoStr) {
    if (!isoStr) return '—';
    var d = new Date(isoStr);
    if (isNaN(d.getTime())) return '—';
    return d.toLocaleDateString(undefined, { year: 'numeric', month: 'short', day: 'numeric' }) + ' ' + d.toLocaleTimeString(undefined, { hour: '2-digit', minute: '2-digit' });
}
function getInitials(c) { return c.initials || ((c.firstName||'')[0] + (c.lastName||'')[0]).toUpperCase(); }
var durationInterval = null;
function parseDate(dateString) {
    if (!dateString) return null;
    try {
        var cleaned = dateString.toString().replace(/Z$/, '').replace(/[+-]\d{2}:\d{2}$/, '').replace(/\.\d+/, '');
        var match = cleaned.match(/^(\d{4})-(\d{2})-(\d{2})[T ](\d{2}):(\d{2})(?::(\d{2}))?/);
        if (match) return new Date(parseInt(match[1]), parseInt(match[2]) - 1, parseInt(match[3]), parseInt(match[4]), parseInt(match[5]), parseInt(match[6]) || 0);
        var d = new Date(dateString);
        return isNaN(d.getTime()) ? null : d;
    } catch (e) { return null; }
}
function getTimeStarted(customer) {
    var startTime = customer.appointment_datetime;
    if (!startTime) return 'N/A';
    var date = parseDate(startTime);
    if (!date) return 'N/A';
    try { return date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true }); } catch (e) { return 'N/A'; }
}
function calculateDuration(startTime) {
    if (!startTime) return '00:00:00';
    var start = parseDate(startTime);
    if (!start || isNaN(start.getTime())) return '00:00:00';
    var now = new Date();
    var diff = Math.max(0, Math.floor((now - start) / 1000));
    var hours = Math.floor(diff / 3600);
    var minutes = Math.floor((diff % 3600) / 60);
    var seconds = diff % 60;
    return String(hours).padStart(2, '0') + ':' + String(minutes).padStart(2, '0') + ':' + String(seconds).padStart(2, '0');
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
    if (durationInterval) { clearInterval(durationInterval); durationInterval = null; }
}

function getPaginatedCustomers() {
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity) return customersData;
    var start = (currentPage - 1) * PAGE_SIZE;
    return customersData.slice(start, start + PAGE_SIZE);
}
function updatePaginationState() {
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity) { totalPages = 1; currentPage = 1; return; }
    totalPages = Math.max(1, Math.ceil(customersData.length / PAGE_SIZE));
    if (currentPage > totalPages) currentPage = totalPages;
    if (currentPage < 1) currentPage = 1;
}
function renderPagination() {
    var el = document.getElementById('customersPagination');
    if (!el) return;
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity || customersData.length <= PAGE_SIZE || totalPages <= 1) { el.innerHTML = ''; return; }
    var h = '<div class="flex items-center gap-2 justify-center">';
    h += '<button class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300 ' + (currentPage === 1 ? 'text-gray-400 cursor-not-allowed opacity-50' : 'bg-white text-[#003047] hover:bg-gray-100') + '" ' + (currentPage === 1 ? 'disabled' : '') + ' onclick="window.waitingListGoToPage(1)">&laquo;</button>';
    h += '<button class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300 ' + (currentPage === 1 ? 'text-gray-400 cursor-not-allowed opacity-50' : 'bg-white text-[#003047] hover:bg-gray-100') + '" ' + (currentPage === 1 ? 'disabled' : '') + ' onclick="window.waitingListChangePage(-1)">&lt;</button>';
    for (var p = Math.max(1, currentPage - 2), end = Math.min(totalPages, p + 4); p <= end; p++) {
        var active = p === currentPage;
        h += '<button class="px-3 py-2 text-sm font-medium rounded-md border ' + (active ? 'bg-[#003047] text-white' : 'bg-white text-gray-700 border-gray-300 hover:border-[#003047]') + '" onclick="window.waitingListGoToPage(' + p + ')">' + p + '</button>';
    }
    h += '<button class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300 ' + (currentPage === totalPages ? 'text-gray-400 cursor-not-allowed opacity-50' : 'bg-white text-[#003047] hover:bg-gray-100') + '" ' + (currentPage === totalPages ? 'disabled' : '') + ' onclick="window.waitingListChangePage(1)">&gt;</button>';
    h += '<button class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300 ' + (currentPage === totalPages ? 'text-gray-400 cursor-not-allowed opacity-50' : 'bg-white text-[#003047] hover:bg-gray-100') + '" ' + (currentPage === totalPages ? 'disabled' : '') + ' onclick="window.waitingListGoToPage(' + totalPages + ')">&raquo;</button>';
    h += '</div>';
    el.innerHTML = h;
}
window.waitingListGoToPage = function(p) { if (p < 1 || p > totalPages || p === currentPage) return; currentPage = p; renderCustomers(); updateResultsCounter(); };
window.waitingListChangePage = function(d) { waitingListGoToPage(currentPage + d); };

function updateResultsCounter() {
    var el = document.getElementById('customersResultsCounter');
    if (!el) return;
    var total = customersData.length;
    if (total === 0) { el.textContent = 'No results found'; return; }
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity) { el.textContent = 'Showing all ' + total + ' result' + (total !== 1 ? 's' : ''); return; }
    var start = (currentPage - 1) * PAGE_SIZE, end = Math.min(start + PAGE_SIZE, total);
    el.textContent = 'Showing ' + (start + 1) + '-' + end + ' of ' + total + ' result' + (total !== 1 ? 's' : '');
}

function renderGridView() {
    var gridView = document.getElementById('gridView');
    if (!gridView) return;
    var list = getPaginatedCustomers();
    if (list.length === 0) {
        gridView.innerHTML = '<div class="col-span-full text-center py-12"><svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg><p class="text-gray-500 text-sm">No customers found</p></div>';
        return;
    }
    gridView.innerHTML = list.map(function(customer, index) {
        var color = colorClasses[index % colorClasses.length], initials = getInitials(customer), fullName = customer.firstName + ' ' + customer.lastName;
        var aptType = customer.appointment || 'Walk-In', statusClass = aptType.toLowerCase() === 'walk-in' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700';
        var aptDateStr = formatAppointmentDate(customer.created_at);
        var gridStartTime = '<div class="flex items-center gap-2 text-sm text-gray-700"><svg style="width:12px;height:12px;color:#008106;" class="rotating-clock" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><span>' + getTimeStarted(customer) + '</span><span class="font-bold text-[#003047] duration-counter" data-start-time="' + (customer.appointment_datetime || '').toString() + '" data-customer-id="' + (customer.id || customer.appointmentId || '') + '">' + calculateDuration(customer.appointment_datetime) + '</span></div>';
        return '<div class="customer-card bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow flex flex-col h-full"><div class="flex-1"><div class="flex items-center gap-4 mb-4"><div class="w-16 h-16 ' + color.bg + ' rounded-full flex items-center justify-center flex-shrink-0"><span class="text-2xl font-bold ' + color.text + '">' + initials + '</span></div><div class="flex-1 min-w-0"><h3 class="font-normal text-gray-900 text-xl truncate">' + fullName + '</h3><p class="text-sm text-gray-500">' + (customer.phone || '') + '</p><div class="mt-2">' + renderTechniciansList(customer.assigned_technician) + '</div></div></div></div><div class="pt-4 border-t border-gray-200 mt-auto space-y-3"><span class="inline-block px-3 py-1 ' + statusClass + ' text-xs font-medium rounded-full">' + aptType + '</span>' + gridStartTime + (aptDateStr !== '—' ? '<p class="text-xs text-gray-500">' + aptDateStr + '</p>' : '') + '<div class="flex gap-2"><button onclick="event.stopPropagation(); assignCustomer(\'' + customer.id + '\', \'' + fullName.replace(/'/g, "\\'") + '\')" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm active:scale-95"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>Assign</button><button type="button" onclick="event.stopPropagation(); removeFromWaitingList(' + (customer.appointmentId || 0) + ', \'' + fullName.replace(/'/g, "\\'") + '\')" class="px-4 py-2 text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition font-medium text-sm active:scale-95" title="Remove from waiting list"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button></div></div></div>';
    }).join('');
}
function renderListView() {
    var tbody = document.getElementById('listViewBody');
    if (!tbody) return;
    var list = getPaginatedCustomers();
    if (list.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="px-6 py-12 text-center"><p class="text-gray-500 text-sm">No customers found</p></td></tr>';
        return;
    }
    tbody.innerHTML = list.map(function(customer, index) {
        var color = colorClasses[index % colorClasses.length], initials = getInitials(customer), fullName = customer.firstName + ' ' + customer.lastName;
        var aptType = customer.appointment || 'Walk-In', statusClass = aptType.toLowerCase() === 'walk-in' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700';
        var rowNum = (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity) ? index + 1 : (currentPage - 1) * PAGE_SIZE + index + 1;
        var startTimeCell = '<div class="flex flex-col gap-1"><div class="text-sm text-gray-900">' + getTimeStarted(customer) + '</div><div class="flex items-center gap-1"><svg style="width:12px;height:12px;color:#008106;" class="rotating-clock" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><span class="text-base font-bold text-[#003047] duration-counter" data-start-time="' + (customer.appointment_datetime || '').toString() + '" data-customer-id="' + (customer.id || customer.appointmentId || '') + '">' + calculateDuration(customer.appointment_datetime) + '</span></div></div>';
        return '<tr class="customer-row hover:bg-gray-50 transition"><td class="px-3 py-4 whitespace-nowrap text-center"><div class="text-sm text-gray-600">' + rowNum + '</div></td><td class="px-6 py-4 whitespace-nowrap"><div class="flex items-center"><div class="w-10 h-10 ' + color.bg + ' rounded-full flex items-center justify-center flex-shrink-0 mr-3"><span class="text-sm font-bold ' + color.text + '">' + initials + '</span></div><div><div class="text-base font-normal text-gray-900">' + fullName + '</div></div></div></td><td class="px-6 py-4 whitespace-nowrap">' + startTimeCell + '</td><td class="px-6 py-4 whitespace-nowrap"><div class="text-sm text-gray-900">' + (customer.phone || '') + '</div></td><td class="px-6 py-4">' + renderTechniciansList(customer.assigned_technician) + '</td><td class="px-6 py-4 whitespace-nowrap"><span class="inline-block px-3 py-1 ' + statusClass + ' text-xs font-medium rounded-full">' + aptType + '</span></td><td class="px-6 py-4 whitespace-nowrap text-right"><div class="flex items-center justify-end gap-2"><button onclick="event.stopPropagation(); assignCustomer(\'' + customer.id + '\', \'' + fullName.replace(/'/g, "\\'") + '\')" class="inline-flex items-center gap-2 px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm active:scale-95"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>Assign</button><button type="button" onclick="event.stopPropagation(); removeFromWaitingList(' + (customer.appointmentId || 0) + ', \'' + fullName.replace(/'/g, "\\'") + '\')" class="inline-flex items-center justify-center px-3 py-2 text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition font-medium text-sm active:scale-95" title="Remove from waiting list"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button></div></td></tr>';
    }).join('');
}
function renderCustomers() { updatePaginationState(); renderGridView(); renderListView(); renderPagination(); updateResultsCounter(); setTimeout(function() { startDurationCounters(); }, 100); }

function applyFilters() {
    var filtered = allMergedData.filter(function(item) {
        if (currentStatusFilter === 'walk-in') return item.appointment && item.appointment.toLowerCase() === 'walk-in';
        if (currentStatusFilter === 'booked') return item.appointment && item.appointment.toLowerCase() === 'booked';
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
        var ta = (a.appointment || '').toLowerCase(), tb = (b.appointment || '').toLowerCase();
        if (ta === 'booked' && tb !== 'booked') return -1;
        if (ta !== 'booked' && tb === 'booked') return 1;
        var aDate = a.appointment_datetime ? new Date(a.appointment_datetime).getTime() : Infinity;
        var bDate = b.appointment_datetime ? new Date(b.appointment_datetime).getTime() : Infinity;
        return aDate - bDate;
    });
    customersData = filtered;
    currentPage = 1;
    updatePaginationState();
    renderCustomers();
    updateResultsCounter();
}
window.filterByStatus = function(status) {
    currentStatusFilter = status;
    var url = new URL(window.location.href);
    url.searchParams.set('status', status);
    window.history.pushState({}, '', url);
    ['All', 'WalkIn', 'Booked'].forEach(function(tab) {
        var id = 'filter' + tab, el = document.getElementById(id);
        if (!el) return;
        var active = (status === 'all' && tab === 'All') || (status === 'walk-in' && tab === 'WalkIn') || (status === 'booked' && tab === 'Booked');
        el.classList.toggle('text-gray-900', active); el.classList.toggle('border-[#003047]', active);
        el.classList.toggle('text-gray-500', !active); el.classList.toggle('border-transparent', !active);
    });
    applyFilters();
};
window.filterByDate = function(dateStr) {
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
function getStatusFromURL() {
    var p = new URLSearchParams(window.location.search).get('status');
    return (p && ['all', 'walk-in', 'booked'].indexOf(p) >= 0) ? p : 'all';
}
window.updateTabStates = function(status) {
    ['All', 'WalkIn', 'Booked'].forEach(function(tab) {
        var id = 'filter' + tab, el = document.getElementById(id);
        if (!el) return;
        var active = (status === 'all' && tab === 'All') || (status === 'walk-in' && tab === 'WalkIn') || (status === 'booked' && tab === 'Booked');
        el.classList.toggle('text-gray-900', active); el.classList.toggle('border-[#003047]', active);
        el.classList.toggle('text-gray-500', !active); el.classList.toggle('border-transparent', !active);
    });
};

function setViewUI(view) {
    var gridView = document.getElementById('gridView');
    var listView = document.getElementById('listView');
    var gridBtn = document.getElementById('gridViewBtn');
    var listBtn = document.getElementById('listViewBtn');

    var isGrid = view === 'grid';
    if (gridView) gridView.classList.toggle('hidden', !isGrid);
    if (listView) listView.classList.toggle('hidden', isGrid);

    if (gridBtn) {
        gridBtn.classList.toggle('bg-white', isGrid);
        gridBtn.classList.toggle('shadow-sm', isGrid);
        if (gridBtn.querySelector('svg')) {
            gridBtn.querySelector('svg').classList.toggle('text-gray-900', isGrid);
            gridBtn.querySelector('svg').classList.toggle('text-gray-500', !isGrid);
        }
    }

    if (listBtn) {
        listBtn.classList.toggle('bg-white', !isGrid);
        listBtn.classList.toggle('shadow-sm', !isGrid);
        if (listBtn.querySelector('svg')) {
            listBtn.querySelector('svg').classList.toggle('text-gray-900', !isGrid);
            listBtn.querySelector('svg').classList.toggle('text-gray-500', isGrid);
        }
    }
}

window.toggleView = function(view) {
    currentView = view;
    localStorage.setItem('customersView', view);
    setViewUI(view);
    if (view === 'grid') renderGridView();
    else renderListView();
};

window.searchCustomers = function(val) { currentSearchTerm = val.toLowerCase(); applyFilters(); };
window.changePerPage = function(val) {
    PAGE_SIZE = val === 'all' ? Infinity : parseInt(val, 10);
    currentPage = 1;
    localStorage.setItem('customersPerPage', val);
    updatePaginationState();
    renderCustomers();
    updateResultsCounter();
};

window.openNewCustomerModal = function() {
    window.selectedCustomer = null;
    var content = '<div class="p-6"><div class="flex items-center justify-between mb-4"><h3 class="text-xl font-bold text-gray-900">Add to Waiting List</h3><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><div class="mb-6"><button type="button" onclick="openAddCustomerModal()" class="mb-4 w-full px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm flex items-center justify-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>Add New Customer</button><p class="text-sm text-gray-600 font-medium mb-2">Select Customer</p><div class="relative"><div id="customerDropdown" class="relative"><button type="button" id="customerDropdownBtn" onclick="toggleCustomerDropdown()" class="w-full text-left pl-12 pr-12 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-base bg-white flex items-center justify-between"><div class="absolute left-3 top-1/2 transform -translate-y-1/2 w-6 h-6 text-gray-400 pointer-events-none"><svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg></div><span id="customerDropdownText" class="text-gray-500">Search by name, phone, or email...</span><svg id="customerDropdownIcon" class="w-6 h-6 text-gray-400 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></button><div id="customerDropdownMenu" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg hidden"><div class="p-3 border-b border-gray-200 sticky top-0 bg-white"><div class="relative"><input type="text" id="customerSearchInput" placeholder="Search customers..." class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-base" oninput="searchCustomersForWaitingList(this.value)" onclick="event.stopPropagation()"><svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none mt-1 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg></div></div><div id="customerSearchResults" class="p-2 space-y-1"></div></div></div></div><div id="selectedCustomerDisplay" class="mt-3 hidden"><div class="bg-[#e6f0f3] border border-[#b3d1d9] rounded-lg p-4 flex items-center justify-between"><div class="flex items-center gap-4"><div class="w-14 h-14 bg-[#e6f0f3] rounded-full flex items-center justify-center flex-shrink-0"><span id="selectedCustomerInitials" class="text-base font-bold text-[#003047]"></span></div><div><p id="selectedCustomerName" class="text-base font-semibold text-gray-900"></p><p id="selectedCustomerContact" class="text-sm text-gray-500"></p></div></div><button type="button" onclick="clearSelectedCustomer()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div></div></div><div class="flex justify-end gap-3 pt-4 border-t border-gray-200"><button type="button" onclick="closeModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">Cancel</button><button type="button" onclick="addToWaitingList()" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Add to Waiting List</button></div></div>';
    openModal(content);
}
window.toggleCustomerDropdown = function() {
    var menu = document.getElementById('customerDropdownMenu'), icon = document.getElementById('customerDropdownIcon');
    if (menu && menu.classList.contains('hidden')) { menu.classList.remove('hidden'); if (icon) icon.classList.add('rotate-180'); setTimeout(function() { var inp = document.getElementById('customerSearchInput'); if (inp) { inp.focus(); searchCustomersForWaitingList(''); } }, 100); }
    else if (menu) { menu.classList.add('hidden'); if (icon) icon.classList.remove('rotate-180'); }
};
window.searchCustomersForWaitingList = function(term) {
    var resultsDiv = document.getElementById('customerSearchResults');
    if (!resultsDiv || !allCustomers) return;
    var lower = term.toLowerCase().trim();
    var list = lower ? allCustomers.filter(function(c) { var name = (c.firstName + ' ' + c.lastName).toLowerCase(); return name.indexOf(lower) >= 0 || (c.phone || '').toLowerCase().indexOf(lower) >= 0 || (c.email || '').toLowerCase().indexOf(lower) >= 0; }) : allCustomers;
    if (!list.length) { resultsDiv.innerHTML = '<div class="p-3 text-sm text-gray-500 text-center">No customer found.</div>'; return; }
    resultsDiv.innerHTML = list.map(function(c) {
        var inits = getInitials(c);
        return '<button type="button" onclick="selectCustomerForWaitingList(' + c.id + '); event.stopPropagation();" class="w-full text-left px-4 py-3 hover:bg-gray-50 rounded-lg transition flex items-center gap-3"><div class="w-10 h-10 bg-[#e6f0f3] rounded-full flex items-center justify-center flex-shrink-0"><span class="text-sm font-bold text-[#003047]">' + inits + '</span></div><div class="flex-1"><p class="text-base font-semibold text-gray-900">' + c.firstName + ' ' + c.lastName + '</p><p class="text-sm text-gray-500">' + (c.phone || '') + (c.email ? ' • ' + c.email : '') + '</p></div></button>';
    }).join('');
}
window.selectCustomerForWaitingList = function(customerId) {
    var customer = allCustomers.find(function(c) { return c.id === customerId; });
    if (!customer) return;
    window.selectedCustomer = customer;
    var menu = document.getElementById('customerDropdownMenu'), icon = document.getElementById('customerDropdownIcon'), text = document.getElementById('customerDropdownText');
    if (menu) menu.classList.add('hidden');
    if (icon) icon.classList.remove('rotate-180');
    if (text) { text.textContent = customer.firstName + ' ' + customer.lastName; text.classList.remove('text-gray-500'); text.classList.add('text-gray-900', 'font-medium'); }
    var disp = document.getElementById('selectedCustomerDisplay'), initsEl = document.getElementById('selectedCustomerInitials'), nameEl = document.getElementById('selectedCustomerName'), contactEl = document.getElementById('selectedCustomerContact');
    if (disp && initsEl && nameEl && contactEl) { initsEl.textContent = getInitials(customer); nameEl.textContent = customer.firstName + ' ' + customer.lastName; contactEl.textContent = (customer.phone || '') + (customer.email ? ' • ' + customer.email : ''); disp.classList.remove('hidden'); }
    var inp = document.getElementById('customerSearchInput');
    if (inp) inp.value = '';
}
window.clearSelectedCustomer = function() {
    window.selectedCustomer = null;
    var disp = document.getElementById('selectedCustomerDisplay');
    if (disp) disp.classList.add('hidden');
    var text = document.getElementById('customerDropdownText');
    if (text) { text.textContent = 'Search by name, phone, or email...'; text.classList.add('text-gray-500'); text.classList.remove('text-gray-900', 'font-medium'); }
    var inp = document.getElementById('customerSearchInput');
    if (inp) inp.value = '';
};
window.openAddCustomerModal = function() {
    var overlay = document.getElementById('addCustomerModalOverlay'), content = document.getElementById('addCustomerModalContent');
    if (!overlay || !content) return;
    var menu = document.getElementById('customerDropdownMenu'), icon = document.getElementById('customerDropdownIcon');
    if (menu) menu.classList.add('hidden');
    if (icon) icon.classList.remove('rotate-180');
    content.innerHTML = '<div class="p-6"><div class="flex items-center justify-between mb-4"><h3 class="text-xl font-bold text-gray-900">Add New Customer</h3><button onclick="closeAddCustomerModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><form onsubmit="saveNewCustomer(event)" class="space-y-4"><div class="grid grid-cols-2 gap-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">First Name</label><input type="text" id="newCustomerFirstName" name="first_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label><input type="text" id="newCustomerLastName" name="last_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div></div><div class="grid grid-cols-2 gap-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label><input type="tel" id="newCustomerPhone" name="phone" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Email (optional)</label><input type="email" id="newCustomerEmail" name="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div></div><div class="flex justify-end gap-3 pt-4"><button type="button" onclick="closeAddCustomerModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">Cancel</button><button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Save Customer</button></div></form></div>';
    overlay.classList.remove('hidden');
    overlay.style.display = 'flex';
    overlay.style.alignItems = 'center';
    overlay.style.justifyContent = 'center';
    document.body.style.overflow = 'hidden';
}
window.closeAddCustomerModal = function() {
    var o = document.getElementById('addCustomerModalOverlay');
    if (o) {
        o.classList.add('hidden');
        o.style.display = 'none';
    }
    document.body.style.overflow = 'auto';
};
window.saveNewCustomer = function(e) {
    e.preventDefault();
    var form = e.target;
    var firstName = (form.querySelector('[name="first_name"]') || document.getElementById('newCustomerFirstName')).value.trim();
    var lastName = (form.querySelector('[name="last_name"]') || document.getElementById('newCustomerLastName')).value.trim();
    var phone = (form.querySelector('[name="phone"]') || document.getElementById('newCustomerPhone')).value.trim();
    var email = (form.querySelector('[name="email"]') || document.getElementById('newCustomerEmail')).value.trim();
    var btn = form.querySelector('button[type="submit"]');
    if (btn) { btn.disabled = true; btn.textContent = 'Saving...'; }
    if (typeof salonApi === 'undefined' || !salonApi.post) {
        if (btn) { btn.disabled = false; btn.textContent = 'Save Customer'; }
        showErrorMessage('Unable to save. Please refresh and try again.');
        return;
    }
    salonApi.post(apiCustomersUrl, { first_name: firstName, last_name: lastName, phone: phone || null, email: email || null }).then(function(res) {
        var data = res.data || res;
        var newCustomer = { id: data.id, firstName: data.firstName || firstName, lastName: data.lastName || lastName, phone: data.phone || phone || '', email: data.email || email || '', createdAt: data.createdAt || new Date().toISOString().split('T')[0] };
        allCustomers.push(newCustomer);
        closeAddCustomerModal();
        selectCustomerForWaitingList(data.id);
        showSuccessMessage(res.message || 'Customer added successfully!');
    }).catch(function(err) {
        if (btn) { btn.disabled = false; btn.textContent = 'Save Customer'; }
        var msg = err && err.message ? err.message : 'Failed to save customer.';
        if (err && err.body && err.body.errors && typeof err.body.errors === 'object') {
            var firstKey = Object.keys(err.body.errors)[0];
            if (firstKey && err.body.errors[firstKey] && err.body.errors[firstKey][0]) msg = err.body.errors[firstKey][0];
        }
        showErrorMessage(msg);
    });
}
window.addToWaitingList = function() {
    if (!window.selectedCustomer) { alert('Please select a customer first'); return; }
    var customer = window.selectedCustomer;
    if (typeof salonApi === 'undefined' || !salonApi.post) {
        showErrorMessage('Unable to add to waiting list. Please refresh and try again.');
        return;
    }
    salonApi.post(apiAppointmentsUrl, { customer_id: customer.id, type: 'walk-in', status: 'waiting' }).then(function(res) {
        var apt = res.data || res;
        allAppointments.push(apt);
        mergeAppointmentsWithCustomers();
        allMergedData = allMergedData.filter(function(item) { return (item.status || '').toLowerCase() === 'waiting'; });
        applyFilters();
        renderCustomers();
        updateResultsCounter();
        renderPagination();
        closeModal();
        window.selectedCustomer = null;
        showSuccessMessage(res.message || (customer.firstName + ' ' + customer.lastName + ' added to waiting list successfully!'));
    }).catch(function(err) {
        var msg = err && err.message ? err.message : 'Failed to add to waiting list.';
        if (err && err.body && err.body.errors && typeof err.body.errors === 'object') {
            var firstKey = Object.keys(err.body.errors)[0];
            if (firstKey && err.body.errors[firstKey] && err.body.errors[firstKey][0]) msg = err.body.errors[firstKey][0];
        }
        showErrorMessage(msg);
    });
};

window.removeFromWaitingList = function(appointmentId, customerName) {
    if (!appointmentId) return;
    if (typeof openConfirmModal !== 'function') {
        if (confirm('Remove ' + customerName + ' from the waiting list?')) { doRemoveFromWaitingList(appointmentId); }
        return;
    }
    openConfirmModal({
        title: 'Remove from waiting list',
        message: 'You are about to permanently remove ' + (customerName ? boldName(customerName) : 'this customer') + ' from the waiting list. Do you want to continue?',
        confirmLabel: 'Remove',
        onConfirm: function() { doRemoveFromWaitingList(appointmentId); }
    });
};
function doRemoveFromWaitingList(appointmentId) {
    if (typeof salonApi === 'undefined' || !salonApi.delete) {
        showErrorMessage('Unable to remove. Please refresh and try again.');
        return;
    }
    salonApi.delete(apiAppointmentsUrl + '/' + appointmentId).then(function(res) {
        allAppointments = allAppointments.filter(function(a) { return a.id !== appointmentId && a.id !== parseInt(appointmentId, 10); });
        mergeAppointmentsWithCustomers();
        allMergedData = allMergedData.filter(function(item) { return (item.status || '').toLowerCase() === 'waiting'; });
        applyFilters();
        renderCustomers();
        updateResultsCounter();
        renderPagination();
        closeModal();
        showSuccessMessage(res.message || 'Removed from waiting list successfully.');
    }).catch(function(err) {
        var msg = err && err.message ? err.message : 'Failed to remove from waiting list.';
        if (err && err.body && err.body.message) msg = err.body.message;
        showErrorMessage(msg);
    });
}

var availableTechnicians = [], originalTechnicianOrder = [], assignedTechnicianIds = [], currentCustomerId = null, currentCustomerName = '', currentAppointmentId = null, technicianSearchTerm = '', assignedTechnicianSearchTerm = '', selectedStatus = 'waiting', resizeHandlerForTechnicians = null;
var turnTrackerOrder = 'lowest', turnTrackerUserIds = new Set(), turnTrackerPositions = new Map();
var currentEventModalElement = null;
// Select services state
var selectServicesData = [], selectServicesCategoriesMap = {}, selectServicesCategory = null, selectServicesCart = [];
var selectServicesAppointmentId = null, selectServicesTechnicianId = null, selectServicesTechnicianName = '';
var selectServicesLoaded = false, selectServicesOldServiceCount = 0;

// Build technician display HTML for the detail modal
function buildTechnicianDisplayHtml(appointmentId) {
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
            + '<button onclick="event.stopPropagation(); openSelectServicesModal(' + appointmentId + ', ' + techId + ', \'' + name.replace(/'/g, "\\'") + '\')" class="px-3 py-1.5 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition-all font-medium text-xs flex items-center gap-1 flex-shrink-0">'
            + '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>'
            + 'Select</button>'
            + '</div>'
            + svcListHtml
            + '</div>';
    }).join('') + '</div>';
}

function updateWaitingListTechnicianDisplay() {
    if (!currentEventModalElement || !currentAppointmentId) return;
    currentEventModalElement.innerHTML = buildTechnicianDisplayHtml(currentAppointmentId);
}

// Detail modal (like calendar event modal)
window.assignCustomer = function(customerId, customerName) {
    currentCustomerId = customerId; currentCustomerName = customerName;
    var customer = customersData.find(function(c) { return c.id.toString() === customerId.toString(); });
    currentAppointmentId = customer && customer.appointmentId ? customer.appointmentId : null;
    assignedTechnicianIds = (customer && customer.assigned_technician && Array.isArray(customer.assigned_technician)) ? customer.assigned_technician.map(function(id) { return id.toString(); }) : [];

    var aptType = customer ? (customer.appointment || 'Walk-In') : 'Walk-In';
    var typeBadgeClass = aptType === 'Walk-In' ? 'bg-blue-100 text-blue-700 border-blue-200' : 'bg-purple-100 text-purple-700 border-purple-200';
    var statusLabel = 'Waiting';
    var statusBadgeClass = 'bg-yellow-100 text-yellow-700 border-yellow-200';
    var customerPhone = customer ? (customer.phone || '') : '';
    var customerEmail = customer ? (customer.email || '') : '';
    var appointmentId = currentAppointmentId;

    var techDisplayHtml = buildTechnicianDisplayHtml(appointmentId);

    var modalContent = '<div class="flex flex-col" style="max-height:85vh">'
        + '<div class="flex-shrink-0 px-6 py-4 border-b border-gray-200 bg-white rounded-t-2xl">'
        + '<div class="flex items-start justify-between">'
        + '<div><h3 class="text-2xl font-bold text-gray-900 mb-1">' + customerName + '</h3></div>'
        + '<div class="flex items-center gap-2">'
        + '<span class="px-3 py-1.5 rounded-lg text-xs font-semibold border bg-gray-100 text-gray-700 border-gray-200">#' + appointmentId + '</span>'
        + '<span class="px-3 py-1.5 rounded-lg text-xs font-semibold border ' + typeBadgeClass + '">' + aptType + '</span>'
        + '<span class="px-3 py-1.5 rounded-lg text-xs font-semibold border ' + statusBadgeClass + '">' + statusLabel + '</span>'
        + '<button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>'
        + '</div></div></div>'
        + '<div class="flex-1 overflow-y-auto px-6 py-4"><div class="space-y-4">'
        + '<div class="p-4 bg-gray-50 rounded-xl">'
        + '<div class="flex items-center justify-between mb-2">'
        + '<p class="text-xs text-gray-500">Technicians</p>'
        + '<button onclick="openWaitingListTechnicianModal()" class="px-3 py-1.5 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition-all font-medium text-xs flex items-center gap-1">'
        + '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>'
        + 'Assign</button></div>'
        + '<div id="technicianDisplay_' + appointmentId + '">' + techDisplayHtml + '</div>'
        + '</div>'
        + (customerPhone || customerEmail ? '<div class="grid grid-cols-2 gap-4">'
            + (customerPhone ? '<div class="p-4 bg-gray-50 rounded-xl"><div class="flex items-center gap-3"><div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center flex-shrink-0"><svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg></div><div class="flex-1 min-w-0"><p class="text-xs text-gray-500 mb-0.5">Phone</p><p class="font-semibold text-gray-900 truncate">' + customerPhone + '</p></div></div></div>' : '')
            + (customerEmail ? '<div class="p-4 bg-gray-50 rounded-xl"><div class="flex items-center gap-3"><div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0"><svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg></div><div class="flex-1 min-w-0"><p class="text-xs text-gray-500 mb-0.5">Email</p><p class="font-semibold text-gray-900 truncate">' + customerEmail + '</p></div></div></div>' : '')
            + '</div>' : '')
        + '</div></div>'
        + '<div class="flex-shrink-0 px-6 py-4 border-t border-gray-200 bg-white rounded-b-2xl">'
        + '<div class="flex items-center justify-end"><div class="flex gap-3">'
        + '<button onclick="removeFromWaitingList(' + appointmentId + ', \'' + customerName.replace(/'/g, "\\'") + '\')" class="px-4 py-2.5 border-2 border-red-500 text-red-500 bg-transparent rounded-lg hover:bg-red-50 transition-all font-medium flex items-center justify-center gap-2 active:scale-95"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>Delete</button>'
        + '<button onclick="confirmAssignmentToTicket()" class="px-4 py-2.5 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition-all font-medium flex flex-col items-center justify-center active:scale-95"><span class="flex items-center gap-2"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>Confirm Assignment</span><span style="font-size:12px;" class="font-normal opacity-75">Move to Ticket</span></button>'
        + '</div></div></div></div>';

    openModal(modalContent, 'default');
    setTimeout(function() {
        currentEventModalElement = document.getElementById('technicianDisplay_' + appointmentId);
    }, 100);
};

// Technician selection modal (opens as nested modal from detail modal)
window.openWaitingListTechnicianModal = function() {
    technicianSearchTerm = ''; assignedTechnicianSearchTerm = '';
    // Refresh assigned IDs from current appointment data
    var appointment = allAppointments.find(function(a) { return a.id.toString() === currentAppointmentId.toString(); });
    assignedTechnicianIds = (appointment && Array.isArray(appointment.assigned_technician)) ? appointment.assigned_technician.map(function(id) { return id.toString(); }) : [];

    var modalHtml = '<div class="flex flex-col h-[80vh] max-h-[80vh] overflow-hidden">'
        + '<div class="flex-shrink-0 px-4 sm:px-6 py-4 border-b border-gray-200 bg-white">'
        + '<div class="flex items-center justify-between">'
        + '<h3 class="text-lg sm:text-xl font-bold text-gray-900">Select Technicians for ' + currentCustomerName.replace(/'/g, "\\'") + '</h3>'
        + '<button onclick="closeNestedModal()" class="text-gray-400 hover:text-gray-600 transition"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>'
        + '</div></div>'
        + '<div class="flex-1 min-h-0 px-4 sm:px-6 py-4 sm:py-6 bg-gray-50">'
        + '<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6 h-full">'
        + '<div class="border border-gray-200 rounded-lg p-3 sm:p-4 flex flex-col h-full bg-white">'
        + '<div class="flex-shrink-0"><div class="flex items-center justify-between mb-2"><h4 class="text-sm font-semibold text-gray-900">Available Technicians</h4><span id="availableCount" class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">0</span></div>'
        + '<p class="text-xs text-gray-500 mb-3">Click to assign technicians</p>'
        + '<div class="relative mb-4"><svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>'
        + '<input type="text" id="technicianSearchInput" placeholder="Search technicians..." oninput="window.waitingListSearchTechnicians(this.value)" class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] text-sm">'
        + '<button id="clearTechnicianSearchBtn" onclick="window.waitingListClearTechnicianSearch()" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition hidden"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>'
        + '</div></div>'
        + '<div id="availableTechniciansContainer" class="overflow-y-auto space-y-3"></div></div>'
        + '<div class="border border-gray-200 rounded-lg p-3 sm:p-4 flex flex-col h-full bg-white">'
        + '<div class="flex-shrink-0"><div class="flex items-center justify-between mb-2"><h4 class="text-sm font-semibold text-gray-900">Assigned Technicians</h4><span id="assignedCount" class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">0</span></div>'
        + '<p class="text-xs text-gray-500 mb-3">Click to remove</p>'
        + '<div class="relative mb-4"><svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>'
        + '<input type="text" id="assignedTechnicianSearchInput" placeholder="Search assigned..." oninput="window.waitingListSearchAssignedTechnicians(this.value)" class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] text-sm">'
        + '<button id="clearAssignedSearchBtn" onclick="window.waitingListClearAssignedSearch()" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition hidden"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>'
        + '</div></div>'
        + '<div id="assignedTechniciansContainer" class="overflow-y-auto space-y-3"></div></div>'
        + '</div></div>'
        + '<div class="flex-shrink-0 px-4 sm:px-6 py-4 border-t border-gray-200 bg-white">'
        + '<div class="flex items-center justify-end"><div class="flex flex-col-reverse sm:flex-row gap-3 w-full sm:w-auto">'
        + '<button onclick="closeNestedModal()" class="w-full sm:w-auto px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium">Cancel</button>'
        + '<button onclick="window.waitingListConfirmAssign()" class="w-full sm:w-auto px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium">Confirm</button>'
        + '</div></div></div></div>';

    openNestedModal(modalHtml, 'large-flex', false);

    if (resizeHandlerForTechnicians) window.removeEventListener('resize', resizeHandlerForTechnicians);
    resizeHandlerForTechnicians = function() {
        var ac = document.getElementById('availableTechniciansContainer');
        var asc = document.getElementById('assignedTechniciansContainer');
        var sw = window.innerWidth, sh = window.innerHeight;
        var h = (sw < 640 ? Math.floor(sh * 0.25) : sw < 1024 ? Math.floor(sh * 0.30) : Math.floor(sh * 0.35)) + 'px';
        if (ac) ac.style.height = h;
        if (asc) asc.style.height = h;
    };
    setTimeout(function() {
        loadTechniciansForAssign();
        resizeHandlerForTechnicians();
        window.addEventListener('resize', resizeHandlerForTechnicians);
    }, 50);
}
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
function loadTechniciansForAssign() {
    function finishLoad() {
        renderAvailableTechnicians();
        renderAssignedTechnicians();
        updateCounts();
        var dropdownText = document.getElementById('statusDropdownText');
        if (dropdownText) {
            if (selectedStatus === 'waiting') dropdownText.textContent = 'Waiting';
            else if (selectedStatus === 'in-progress') dropdownText.textContent = 'In Progress';
            else if (selectedStatus === 'completed') dropdownText.textContent = 'Completed';
            else dropdownText.textContent = 'In Progress';
        }
        updateStatusHighlighting();
    }

    var techReady = Promise.resolve();
    if (!allTechnicians || !allTechnicians.length) {
        techReady = fetch(base + '/users').then(function(r) { return r.json(); }).then(function(data) {
            allTechnicians = (data.users || []).filter(function(u) { return u.role === 'technician' || u.userlevel === 'technician'; });
        });
    }

    techReady.then(function() {
        availableTechnicians = allTechnicians;
        originalTechnicianOrder = availableTechnicians.map(function(t) { return t.id; });
        return fetchTurnTrackerOrder();
    }).then(function() {
        finishLoad();
    }).catch(function(err) { console.error(err); var c = document.getElementById('availableTechniciansContainer'); if (c) c.innerHTML = '<div class="text-center py-8 text-sm text-gray-400">Error loading technicians</div>'; });
}
window.waitingListSearchTechnicians = function(val) {
    technicianSearchTerm = (val || '').toLowerCase().trim();
    var btn = document.getElementById('clearTechnicianSearchBtn');
    if (btn) {
        if (val.trim()) btn.classList.remove('hidden');
        else btn.classList.add('hidden');
    }
    renderAvailableTechnicians();
};
window.waitingListClearTechnicianSearch = function() {
    var inp = document.getElementById('technicianSearchInput');
    var btn = document.getElementById('clearTechnicianSearchBtn');
    if (inp) { inp.value = ''; technicianSearchTerm = ''; inp.focus(); }
    if (btn) btn.classList.add('hidden');
    renderAvailableTechnicians();
};
window.waitingListSearchAssignedTechnicians = function(val) {
    assignedTechnicianSearchTerm = (val || '').toLowerCase().trim();
    var btn = document.getElementById('clearAssignedSearchBtn');
    if (btn) {
        if (val.trim()) btn.classList.remove('hidden');
        else btn.classList.add('hidden');
    }
    renderAssignedTechnicians();
};
window.waitingListClearAssignedSearch = function() {
    var inp = document.getElementById('assignedTechnicianSearchInput');
    var btn = document.getElementById('clearAssignedSearchBtn');
    if (inp) { inp.value = ''; assignedTechnicianSearchTerm = ''; inp.focus(); }
    if (btn) btn.classList.add('hidden');
    renderAssignedTechnicians();
};
function renderAvailableTechnicians() {
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
    container.innerHTML = filtered.map(function(tech) {
        var idStr = tech.id.toString(), isAssigned = assignedTechnicianIds.indexOf(idStr) >= 0;
        var inits = tech.initials || (tech.firstName || '')[0] + (tech.lastName || '')[0];
        var name = tech.firstName + ' ' + tech.lastName;
        var techPhoto = tech.profilePhotoUrl || tech.photo || null;
        var containerCls = isAssigned ? 'flex items-center gap-3 p-2 rounded-lg transition-colors opacity-50 grayscale cursor-pointer group hover:bg-gray-100' : 'flex items-center gap-3 cursor-pointer group hover:bg-gray-50 p-2 rounded-lg transition-colors';
        var avatarCls = isAssigned ? 'w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center' : 'w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center';
        var initialCls = isAssigned ? 'text-sm font-bold text-gray-500' : 'text-sm font-bold text-gray-600';
        var nameCls = isAssigned ? 'text-base font-medium text-gray-400' : 'text-base font-medium text-gray-900';
        var isOnline = !!(tech.clock_in && !tech.clock_out);
        var badgeCls = isAssigned ? 'absolute w-5 h-5 rounded-full border-2 border-white bg-gray-400' : (isOnline ? 'absolute w-5 h-5 rounded-full border-2 border-white bg-green-500' : 'absolute w-5 h-5 rounded-full border-2 border-white bg-gray-400');
        var badgeStyle = 'bottom: -5px; right: -5px;';
        var servicesNum = typeof tech.services === 'number' ? tech.services : 0;
        var modalHoverAttr = techPhoto ? ' onmouseenter="showTechPhotoPreview(event, \'' + techPhoto.replace(/'/g, "\\'").replace(/"/g, '&quot;') + '\', \'' + name.replace(/'/g, "\\'") + '\')" onmouseleave="hideTechPhotoPreview()"' : '';
        var avatarHtml = techPhoto
            ? '<img src="' + techPhoto.replace(/"/g, '&quot;') + '" alt="" class="w-12 h-12 rounded-full object-cover' + (isAssigned ? ' opacity-50 grayscale' : '') + ' cursor-pointer"' + modalHoverAttr + '>'
            : '<div class="' + avatarCls + '"><span class="' + initialCls + '">' + inits + '</span></div>';
        return '<div onclick="' + (isAssigned ? 'removeAssignedTechnician(' + tech.id + ')' : 'assignTechnician(' + tech.id + ')') + '" class="' + containerCls + '"><div class="relative flex-shrink-0">' + avatarHtml + '<div class="' + badgeCls + '" style="' + badgeStyle + '" title="' + (isOnline ? 'Online' : 'Offline') + '"></div></div><div class="flex-1 min-w-0"><p class="' + nameCls + '">' + name + '</p></div><div class="flex-shrink-0 text-right"><div class="text-xs font-medium text-gray-500 uppercase">Services</div><div class="text-lg font-semibold text-gray-900">' + servicesNum + '</div></div></div>';
    }).join('');
}
function renderAssignedTechnicians() {
    var container = document.getElementById('assignedTechniciansContainer');
    if (!container) return;
    if (!assignedTechnicianIds.length) {
        container.innerHTML = '<div class="flex items-center justify-center h-full min-h-[200px]"><p class="text-sm text-gray-400">No technicians assigned</p></div>';
        return;
    }

    // Get all assigned technicians
    var assignedTechs = assignedTechnicianIds.map(function(idStr) {
        return availableTechnicians.find(function(t) { return t.id.toString() === idStr; });
    }).filter(function(t) { return t != null; });

    // Filter by search term if exists
    if (assignedTechnicianSearchTerm) {
        assignedTechs = assignedTechs.filter(function(t) {
            var name = (t.firstName + ' ' + t.lastName).toLowerCase();
            var inits = (t.initials || (t.firstName || '')[0] + (t.lastName || '')[0]).toLowerCase();
            return (name + ' ' + inits).indexOf(assignedTechnicianSearchTerm) >= 0;
        });
    }

    // Show "no results" message if search filtered everything out
    if (!assignedTechs.length) {
        container.innerHTML = '<div class="flex items-center justify-center h-full min-h-[200px]"><p class="text-sm text-gray-400">No technicians found</p></div>';
        return;
    }

    container.innerHTML = assignedTechs.map(function(tech) {
        var inits = tech.initials || (tech.firstName || '')[0] + (tech.lastName || '')[0];
        var name = tech.firstName + ' ' + tech.lastName;
        var techPhoto = tech.profilePhotoUrl || tech.photo || null;
        var isOnline = !!(tech.clock_in && !tech.clock_out);
        var badgeCls = isOnline ? 'absolute w-5 h-5 rounded-full border-2 border-white bg-green-500' : 'absolute w-5 h-5 rounded-full border-2 border-white bg-gray-400';
        var badgeStyle = 'bottom: -5px; right: -5px;';
        var servicesNum = typeof tech.services === 'number' ? tech.services : 0;
        var assignedHoverAttr = techPhoto ? ' onmouseenter="showTechPhotoPreview(event, \'' + techPhoto.replace(/'/g, "\\'").replace(/"/g, '&quot;') + '\', \'' + name.replace(/'/g, "\\'") + '\')" onmouseleave="hideTechPhotoPreview()"' : '';
        var assignedAvatarHtml = techPhoto
            ? '<img src="' + techPhoto.replace(/"/g, '&quot;') + '" alt="" class="w-12 h-12 rounded-full object-cover cursor-pointer"' + assignedHoverAttr + '>'
            : '<div class="w-12 h-12 bg-[#003047] rounded-full flex items-center justify-center"><span class="text-sm font-bold text-white">' + inits + '</span></div>';
        return '<div onclick="removeAssignedTechnician(' + tech.id + ')" class="flex items-center gap-3 cursor-pointer group hover:bg-gray-50 p-2 rounded-lg transition-colors"><div class="relative flex-shrink-0">' + assignedAvatarHtml + '<div class="' + badgeCls + '" style="' + badgeStyle + '" title="' + (isOnline ? 'Online' : 'Offline') + '"></div></div><div class="flex-1 min-w-0"><p class="text-base font-medium text-gray-900">' + name + '</p></div><div class="flex-shrink-0 text-right"><div class="text-xs font-medium text-gray-500 uppercase">Services</div><div class="text-lg font-semibold text-gray-900">' + servicesNum + '</div></div></div>';
    }).join('');
}
window.assignTechnician = function(techId) {
    var idStr = techId.toString();
    if (assignedTechnicianIds.indexOf(idStr) < 0) {
        assignedTechnicianIds.push(idStr);
        renderAvailableTechnicians();
        renderAssignedTechnicians();
    updateCounts();
    }
};
window.removeAssignedTechnician = function(techId) {
    assignedTechnicianIds = assignedTechnicianIds.filter(function(id) { return id !== techId.toString(); });
    renderAvailableTechnicians();
    renderAssignedTechnicians();
    updateCounts();
};
function updateCounts() {
    var availEl = document.getElementById('availableCount');
    var assignEl = document.getElementById('assignedCount');
    if (availEl) availEl.textContent = availableTechnicians.length.toString();
    if (assignEl) assignEl.textContent = assignedTechnicianIds.length.toString();
}
// Start session toggle removed — functionality deprecated. Confirm Assignment will set status to 'unpaid'.
window.waitingListToggleStatusDropdown = function() {
    var menu = document.getElementById('statusDropdownMenu');
    var icon = document.getElementById('statusDropdownIcon');
    if (menu) {
        var isHidden = menu.classList.contains('hidden');
        if (isHidden) {
            menu.classList.remove('hidden');
            if (icon) icon.classList.add('rotate-180');
            updateStatusHighlighting();
        } else {
            menu.classList.add('hidden');
            if (icon) icon.classList.remove('rotate-180');
        }
    }
};
function updateStatusHighlighting() {
    var waitingBtn = document.getElementById('statusOptionWaiting');
    var inProgressBtn = document.getElementById('statusOptionInProgress');
    var completedBtn = document.getElementById('statusOptionCompleted');
    [waitingBtn, inProgressBtn, completedBtn].forEach(function(btn) {
        if (btn) btn.className = 'w-full px-4 py-3 text-left text-base text-gray-900 hover:bg-gray-50 transition flex items-center gap-2';
    });
    if (selectedStatus === 'waiting' && waitingBtn) {
        waitingBtn.className = 'w-full px-4 py-3 text-left text-base bg-[#003047] text-white hover:bg-[#002535] transition flex items-center gap-2';
    } else if (selectedStatus === 'in-progress' && inProgressBtn) {
        inProgressBtn.className = 'w-full px-4 py-3 text-left text-base bg-[#003047] text-white hover:bg-[#002535] transition flex items-center gap-2';
    } else if (selectedStatus === 'completed' && completedBtn) {
        completedBtn.className = 'w-full px-4 py-3 text-left text-base bg-[#003047] text-white hover:bg-[#002535] transition flex items-center gap-2';
    }
}
window.waitingListSelectStatus = function(status) {
    selectedStatus = status;
    var dropdownText = document.getElementById('statusDropdownText');
    var dropdownMenu = document.getElementById('statusDropdownMenu');
    var dropdownIcon = document.getElementById('statusDropdownIcon');
    if (dropdownText) {
        if (status === 'waiting') dropdownText.textContent = 'Waiting';
        else if (status === 'in-progress') dropdownText.textContent = 'In Progress';
        else if (status === 'completed') dropdownText.textContent = 'Completed';
    }
    updateStatusHighlighting();
    if (dropdownMenu) dropdownMenu.classList.add('hidden');
    if (dropdownIcon) dropdownIcon.classList.remove('rotate-180');
};
document.addEventListener('click', function(e) {
    var dropdownButton = document.getElementById('statusDropdownButton');
    var dropdownMenu = document.getElementById('statusDropdownMenu');
    if (dropdownButton && dropdownMenu && !dropdownButton.contains(e.target) && !dropdownMenu.contains(e.target)) {
        dropdownMenu.classList.add('hidden');
        var dropdownIcon = document.getElementById('statusDropdownIcon');
        if (dropdownIcon) dropdownIcon.classList.remove('rotate-180');
    }
});
window.waitingListConfirmAssign = function() {
    if (!currentAppointmentId) { showErrorMessage('Appointment not found.'); return; }
    var now = new Date();
    var appointmentDatetime = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0') + 'T' + String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0') + ':' + String(now.getSeconds()).padStart(2, '0');
    var payload = {
        assigned_technician: assignedTechnicianIds.map(function(id) { return parseInt(id, 10); }),
        appointment_datetime: appointmentDatetime
    };
    var btn = document.querySelector('[onclick*="waitingListConfirmAssign"]');
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
                updateWaitingListTechnicianDisplay();
            }).catch(function(err) { console.error('Service cleanup failed:', err); });
        }

        mergeAppointmentsWithCustomers();
        allMergedData = allMergedData.filter(function(item) { return (item.status || '').toLowerCase() === 'waiting'; });
        applyFilters();
        var names = assignedTechnicianIds.map(function(id) { var t = availableTechnicians.find(function(x) { return x.id.toString() === id; }); return t ? t.firstName + ' ' + t.lastName : ''; }).filter(Boolean);
        var message = res.message || (currentCustomerName + ' assigned to ' + names.join(', ') + ' successfully.');
        showSuccessMessage(message);
        closeNestedModal();
        updateWaitingListTechnicianDisplay();
    }).catch(function(err) {
        showErrorMessage(err.message || 'Failed to save assignment.');
    }).finally(function() {
        if (btn) { btn.disabled = false; btn.textContent = 'Confirm'; }
    });
};

// Confirm Assignment — move to ticket (status=unpaid, set appointment date)
window.confirmAssignmentToTicket = function() {
    if (!currentAppointmentId) { showErrorMessage('Appointment not found.'); return; }
    var appointment = allAppointments.find(function(a) { return a.id.toString() === currentAppointmentId.toString(); });
    var techIds = (appointment && Array.isArray(appointment.assigned_technician)) ? appointment.assigned_technician : assignedTechnicianIds;
    if (!techIds.length) { alert('Please assign at least one technician first.'); return; }
    var now = new Date();
    var appointmentDatetime = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0') + 'T' + String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0') + ':' + String(now.getSeconds()).padStart(2, '0');
    var payload = {
        assigned_technician: techIds.map(function(id) { return parseInt(id, 10); }),
        status: 'unpaid',
        appointment_datetime: appointmentDatetime
    };
    var btn = document.querySelector('[onclick*="confirmAssignmentToTicket"]');
    var btnOriginalHtml = '';
    if (btn) {
        btnOriginalHtml = btn.innerHTML;
        btn.disabled = true;
        btn.classList.add('opacity-50', 'cursor-not-allowed');
        btn.innerHTML = '<span class="flex items-center gap-2"><svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Processing...</span>';
    }
    salonApi.put(apiAppointmentsUrl + '/' + currentAppointmentId, payload).then(function(res) {
        var data = res.data;
        var idx = allAppointments.findIndex(function(a) { return a.id === currentAppointmentId; });
        if (idx >= 0 && data) allAppointments[idx] = data;
        mergeAppointmentsWithCustomers();
        allMergedData = allMergedData.filter(function(item) { return (item.status || '').toLowerCase() === 'waiting'; });
        applyFilters();
        showSuccessMessage(res.message || (currentCustomerName + ' moved to ticket successfully.'));
        closeModal();
    }).catch(function(err) {
        showErrorMessage(err.message || 'Failed to confirm assignment.');
    }).finally(function() {
        if (btn) { btn.disabled = false; btn.classList.remove('opacity-50', 'cursor-not-allowed'); btn.innerHTML = btnOriginalHtml; }
    });
};

// --- Select Services Modal ---
window.openSelectServicesModal = function(appointmentId, technicianId, technicianName) {
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
        + '<input type="text" id="selectServicesSearchInput" placeholder="Search services..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] text-sm" oninput="renderSelectServicesGrid()"></div></div>'
        + '<div class="select-svc-carousel-wrapper mb-4"><div id="selectServicesCategoriesList" class="select-svc-slick-carousel"></div></div>'
        + '<div id="selectServicesGrid" class="grid grid-cols-2 sm:grid-cols-4 gap-3"></div></div>'
        + '<div class="flex-shrink-0 px-6 py-4 border-t border-gray-200 bg-white">'
        + '<div id="selectServicesCartSummary" class="mb-3"></div>'
        + '<div class="flex items-center justify-end gap-3">'
        + '<button onclick="closeNestedModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium">Cancel</button>'
        + '<button id="selectServicesSaveBtn" onclick="saveSelectServicesCart()" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium flex items-center gap-2">Save Services</button>'
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
        renderSelectServicesCategories();
        renderSelectServicesGrid();
    } else {
        Promise.all([
            fetch(base + '/services').then(function(r) { return r.json(); }),
            fetch(base + '/service-categories').then(function(r) { return r.json(); })
        ]).then(function(results) {
            selectServicesData = (results[0].services || []).filter(function(s) { return s.active !== false; });
            selectServicesCategoriesMap = results[1].categories || results[1] || {};
            selectServicesLoaded = true;
            populateCartFromExisting();
            renderSelectServicesCategories();
            renderSelectServicesGrid();
        }).catch(function(err) { console.error('Failed to load services:', err); });
    }
};

function renderSelectServicesCategories() {
    var container = document.getElementById('selectServicesCategoriesList');
    if (!container) return;
    var $c = $(container);
    if ($c.hasClass('slick-initialized')) { try { $c.slick('unslick'); } catch (e) {} }
    var activeClass = 'bg-[#e6f0f3] border-[#003047] text-[#003047]';
    var inactiveClass = 'bg-white border-gray-200 text-gray-700 hover:border-[#003047] hover:bg-[#e6f0f3] hover:text-[#003047]';
    var html = '<div><button type="button" onclick="selectServicesFilterCategory(null)" class="select-svc-cat-card w-full h-[70px] px-4 py-2 rounded-lg text-sm font-medium border transition-all duration-200 flex items-center justify-center text-center break-words active:scale-95 ' + (selectServicesCategory === null ? activeClass : inactiveClass) + '" data-category-key="all">All Categories</button></div>';
    var sorted = Object.entries(selectServicesCategoriesMap).sort(function(a, b) { return (a[1] || '').localeCompare(b[1] || ''); });
    sorted.forEach(function(entry) {
        html += '<div><button type="button" onclick="selectServicesFilterCategory(\'' + entry[0] + '\')" class="select-svc-cat-card w-full h-[70px] px-4 py-2 rounded-lg text-sm font-medium border transition-all duration-200 flex items-center justify-center text-center break-words active:scale-95 ' + (selectServicesCategory === entry[0] ? activeClass : inactiveClass) + '" data-category-key="' + entry[0] + '">' + entry[1] + '</button></div>';
    });
    container.innerHTML = html;
    setTimeout(function() {
        if (typeof $ !== 'undefined' && typeof $.fn.slick !== 'undefined') {
            $c.slick({ slidesToShow: 6, slidesToScroll: 6, infinite: false, arrows: true, dots: false, adaptiveHeight: false, variableWidth: false, responsive: [{ breakpoint: 1024, settings: { slidesToShow: 4, slidesToScroll: 4 } }, { breakpoint: 640, settings: { slidesToShow: 2, slidesToScroll: 2 } }] });
            $c.css({ opacity: '1', visibility: 'visible' });
        } else { container.classList.add('show-fallback'); container.style.opacity = '1'; container.style.visibility = 'visible'; }
    }, 50);
}

window.selectServicesFilterCategory = function(key) {
    selectServicesCategory = key;
    document.querySelectorAll('.select-svc-cat-card').forEach(function(card) {
        var cardKey = card.getAttribute('data-category-key');
        var isActive = (key === null && cardKey === 'all') || (key === cardKey);
        if (isActive) { card.classList.remove('bg-white', 'border-gray-200', 'text-gray-700'); card.classList.add('bg-[#e6f0f3]', 'border-[#003047]', 'text-[#003047]'); }
        else { card.classList.remove('bg-[#e6f0f3]', 'border-[#003047]', 'text-[#003047]'); card.classList.add('bg-white', 'border-gray-200', 'text-gray-700'); }
    });
    renderSelectServicesGrid();
};

window.renderSelectServicesGrid = function() {
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
                + '<button onclick="selectServicesUpdateQty(' + service.id + ', -1)" class="w-8 h-8 flex items-center justify-center bg-white text-gray-700 hover:bg-red-50 hover:text-red-600 rounded-md border border-gray-200 shadow-sm transition active:scale-95"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg></button>'
                + '<span class="text-sm font-bold text-gray-900 min-w-[2rem] text-center">' + cartItem.quantity + '</span>'
                + '<button onclick="selectServicesUpdateQty(' + service.id + ', 1)" class="w-8 h-8 flex items-center justify-center bg-white text-gray-700 hover:bg-green-50 hover:text-green-600 rounded-md border border-gray-200 shadow-sm transition active:scale-95"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg></button></div>'
                : '<button onclick="selectServicesAddToCart(' + service.id + ')" class="w-full px-3 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-xs active:scale-95 mt-auto">Add</button>')
            + '</div></div>';
    });
    container.innerHTML = html;
    renderSelectServicesCartSummary();
};

window.selectServicesAddToCart = function(serviceId) {
    var service = selectServicesData.find(function(s) { return s.id === serviceId; });
    if (!service) return;
    var existing = selectServicesCart.find(function(c) { return c.service_id === serviceId; });
    if (existing) { existing.quantity += 1; }
    else { selectServicesCart.push({ service_id: serviceId, name: service.name, price: service.price, quantity: 1, category_slug: (service.categories && service.categories[0]) || '', service_count: typeof service.service_count === 'number' ? service.service_count : 0 }); }
    renderSelectServicesGrid();
};
window.selectServicesUpdateQty = function(serviceId, delta) {
    var item = selectServicesCart.find(function(c) { return c.service_id === serviceId; });
    if (!item) return;
    item.quantity += delta;
    if (item.quantity <= 0) selectServicesCart = selectServicesCart.filter(function(c) { return c.service_id !== serviceId; });
    renderSelectServicesGrid();
};
window.selectServicesRemoveFromCart = function(serviceId) {
    selectServicesCart = selectServicesCart.filter(function(c) { return c.service_id !== serviceId; });
    renderSelectServicesGrid();
};

function renderSelectServicesCartSummary() {
    var container = document.getElementById('selectServicesCartSummary');
    if (!container) return;
    if (selectServicesCart.length === 0) { container.innerHTML = '<p class="text-sm text-gray-400">No services selected</p>'; return; }
    var total = 0;
    var html = '<div class="overflow-y-auto space-y-1" style="max-height:150px">';
    selectServicesCart.forEach(function(item) {
        var lineTotal = item.price * item.quantity; total += lineTotal;
        html += '<div class="flex items-center justify-between text-sm"><div class="flex items-center gap-2">'
            + '<button onclick="selectServicesRemoveFromCart(' + item.service_id + ')" class="w-5 h-5 flex items-center justify-center rounded-full bg-red-100 text-red-500 hover:bg-red-200 transition flex-shrink-0"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>'
            + '<span class="text-gray-700">' + item.name + ' &times; ' + item.quantity + '</span></div>'
            + '<span class="font-medium text-gray-900">' + window.salonFormatMoney(lineTotal) + '</span></div>';
    });
    html += '</div><div class="flex items-center justify-between text-sm font-bold pt-1 border-t border-gray-200 mt-1"><span>Total</span><span>' + window.salonFormatMoney(total) + '</span></div>';
    container.innerHTML = html;
}

window.saveSelectServicesCart = function() {
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
                updateWaitingListTechnicianDisplay();
            }).catch(function(err) { console.error('Turn tracker update failed:', err); });
            if (typeof showSuccessMessage === 'function') showSuccessMessage('Services saved for ' + selectServicesTechnicianName + '.');
            closeNestedModal();
            updateWaitingListTechnicianDisplay();
        }).catch(function(err) { resetSaveBtn(); if (typeof showErrorMessage === 'function') showErrorMessage(err && err.message ? err.message : 'Failed to save services.'); });
    }
};

async function fetchCustomers() {
    try {
        if (window.salonWaitingListBootstrap && window.salonWaitingListBootstrap.customers) {
            var boot = window.salonWaitingListBootstrap || {};
            allCustomers = boot.customers || [];
            allAppointments = boot.appointments || [];
            allTechnicians = (boot.users || []).filter(function(u) { return u.role === 'technician' || u.userlevel === 'technician'; });
        } else {
            var custRes = await fetch(base + '/customers'), aptRes = await fetch(base + '/appointments'), techRes = await fetch(base + '/users');
            var custData = await custRes.json(), aptData = await aptRes.json(), techData = await techRes.json();
            allCustomers = custData.customers || [];
            allAppointments = aptData.appointments || [];
            allTechnicians = (techData.users || []).filter(function(u) { return u.role === 'technician' || u.userlevel === 'technician'; });
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
        allMergedData = allMergedData.filter(function(item) { return (item.status || '').toLowerCase() === 'waiting'; });
        currentStatusFilter = getStatusFromURL();
        var urlParams = new URLSearchParams(window.location.search);
        var needsReplace = false;
        if (!urlParams.get('status')) { needsReplace = true; }
        if (!urlParams.get('date')) { needsReplace = true; }
        if (needsReplace) {
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
            var dateInput = document.getElementById('dateFilterInput');
            if (dateInput) dateInput.value = currentDateFilter;
            updateTabStates(currentStatusFilter);
            applyFilters();
        });
    } catch (err) {
        console.error('Error fetching data:', err);
        showErrorMessage('Failed to load data');
    }
}

// Technician photo hover preview
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

document.addEventListener('DOMContentLoaded', function() {
    var saved = localStorage.getItem('customersPerPage');
    if (saved) { var sel = document.getElementById('perPageSelect'); if (sel) { sel.value = saved; PAGE_SIZE = saved === 'all' ? Infinity : parseInt(saved, 10); } }
    var dateInput = document.getElementById('dateFilterInput');
    if (dateInput) dateInput.value = currentDateFilter;
    setViewUI(currentView);
    fetchCustomers().then(function() { window.toggleView(currentView); });
});
})();
</script>
@endpush
@endsection

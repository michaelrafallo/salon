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
            <div class="relative max-w-md">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" id="customerSearchInput" placeholder="Search customers" oninput="searchCustomers(this.value)" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-base">
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned Technicians</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Appointment Date</th>
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
@push('scripts')
<script>
(function() {
var base = window.salonJsonBase || '{{ url("api/salon/data") }}';
window.salonWaitingListBootstrap = window.salonWaitingListBootstrap || @json($waitingListBootstrap ?? null);
var apiCustomersUrl = '{{ url("api/salon/customers") }}';
var apiAppointmentsUrl = '{{ url("api/salon/appointments") }}';
var allCustomers = [], allAppointments = [], allTechnicians = [], allMergedData = [], customersData = [];
var PAGE_SIZE = 15, currentPage = 1, totalPages = 1, currentSearchTerm = '', currentStatusFilter = @json($waitingListTab);
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
        var aptDateStr = formatAppointmentDate(customer.appointment_datetime);
        return '<div class="customer-card bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow flex flex-col h-full"><div class="flex-1"><div class="flex items-center gap-4 mb-4"><div class="w-16 h-16 ' + color.bg + ' rounded-full flex items-center justify-center flex-shrink-0"><span class="text-2xl font-bold ' + color.text + '">' + initials + '</span></div><div class="flex-1 min-w-0"><h3 class="font-normal text-gray-900 text-xl truncate">' + fullName + '</h3><p class="text-sm text-gray-500">' + (customer.phone || '') + '</p><div class="mt-2">' + renderTechniciansList(customer.assigned_technician) + '</div></div></div></div><div class="pt-4 border-t border-gray-200 mt-auto space-y-3"><span class="inline-block px-3 py-1 ' + statusClass + ' text-xs font-medium rounded-full">' + aptType + '</span>' + (aptDateStr !== '—' ? '<p class="text-xs text-gray-500">' + aptDateStr + '</p>' : '') + '<div class="flex gap-2"><button onclick="event.stopPropagation(); assignCustomer(\'' + customer.id + '\', \'' + fullName.replace(/'/g, "\\'") + '\')" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm active:scale-95"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>Assign</button><button type="button" onclick="event.stopPropagation(); removeFromWaitingList(' + (customer.appointmentId || 0) + ', \'' + fullName.replace(/'/g, "\\'") + '\')" class="px-4 py-2 text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition font-medium text-sm active:scale-95" title="Remove from waiting list"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button></div></div></div>';
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
        var aptDateStr = formatAppointmentDate(customer.appointment_datetime);
        return '<tr class="customer-row hover:bg-gray-50 transition"><td class="px-3 py-4 whitespace-nowrap text-center"><div class="text-sm text-gray-600">' + rowNum + '</div></td><td class="px-6 py-4 whitespace-nowrap"><div class="flex items-center"><div class="w-10 h-10 ' + color.bg + ' rounded-full flex items-center justify-center flex-shrink-0 mr-3"><span class="text-sm font-bold ' + color.text + '">' + initials + '</span></div><div><div class="text-base font-normal text-gray-900">' + fullName + '</div></div></div></td><td class="px-6 py-4 whitespace-nowrap"><div class="text-sm text-gray-900">' + (customer.phone || '') + '</div></td><td class="px-6 py-4">' + renderTechniciansList(customer.assigned_technician) + '</td><td class="px-6 py-4 whitespace-nowrap"><div class="text-sm text-gray-900">' + aptDateStr + '</div></td><td class="px-6 py-4 whitespace-nowrap"><span class="inline-block px-3 py-1 ' + statusClass + ' text-xs font-medium rounded-full">' + aptType + '</span></td><td class="px-6 py-4 whitespace-nowrap text-right"><div class="flex items-center justify-end gap-2"><button onclick="event.stopPropagation(); assignCustomer(\'' + customer.id + '\', \'' + fullName.replace(/'/g, "\\'") + '\')" class="inline-flex items-center gap-2 px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm active:scale-95"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>Assign</button><button type="button" onclick="event.stopPropagation(); removeFromWaitingList(' + (customer.appointmentId || 0) + ', \'' + fullName.replace(/'/g, "\\'") + '\')" class="inline-flex items-center justify-center px-3 py-2 text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition font-medium text-sm active:scale-95" title="Remove from waiting list"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button></div></td></tr>';
    }).join('');
}
function renderCustomers() { updatePaginationState(); renderGridView(); renderListView(); renderPagination(); updateResultsCounter(); }

function applyFilters() {
    var filtered = allMergedData.filter(function(item) {
        if (currentStatusFilter === 'walk-in') return item.appointment && item.appointment.toLowerCase() === 'walk-in';
        if (currentStatusFilter === 'booked') return item.appointment && item.appointment.toLowerCase() === 'booked';
        return true;
    });
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
        showSuccessMessage(res.message || 'Removed from waiting list successfully.');
    }).catch(function(err) {
        var msg = err && err.message ? err.message : 'Failed to remove from waiting list.';
        if (err && err.body && err.body.message) msg = err.body.message;
        showErrorMessage(msg);
    });
}

var availableTechnicians = [], originalTechnicianOrder = [], assignedTechnicianIds = [], currentCustomerId = null, currentCustomerName = '', currentAppointmentId = null, technicianSearchTerm = '', assignedTechnicianSearchTerm = '', selectedStatus = 'waiting', resizeHandlerForTechnicians = null;
window.assignCustomer = function(customerId, customerName) {
    currentCustomerId = customerId; currentCustomerName = customerName; technicianSearchTerm = ''; assignedTechnicianSearchTerm = ''; selectedStatus = 'waiting';
    var customer = customersData.find(function(c) { return c.id.toString() === customerId.toString(); });
    currentAppointmentId = customer && customer.appointmentId ? customer.appointmentId : null;
    assignedTechnicianIds = (customer && customer.assigned_technician && Array.isArray(customer.assigned_technician)) ? customer.assigned_technician.map(function(id) { return id.toString(); }) : [];
    var isWaiting = customer && customer.status && customer.status.toLowerCase() === 'waiting';
    if (!isWaiting && customer && customer.status) { var s = customer.status.toLowerCase(); selectedStatus = (s === 'in-progress' || s === 'completed') ? s : 'in-progress'; }
    var modalHtml = '<div class="flex flex-col h-[80vh] max-h-[80vh] overflow-hidden">' +
        '<!-- Fixed Header -->' +
        '<div class="flex-shrink-0 px-4 sm:px-6 py-4 border-b border-gray-200 bg-white">' +
            '<div class="flex items-center justify-between">' +
                '<h3 class="text-lg sm:text-xl font-bold text-gray-900">Assign Technician to ' + customerName.replace(/'/g, "\\'") + '</h3>' +
                '<button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition">' +
                    '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                        '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>' +
                    '</svg>' +
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
                            '<svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>' +
                            '</svg>' +
                            '<input type="text" id="technicianSearchInput" placeholder="Search technicians..." oninput="window.waitingListSearchTechnicians(this.value)" class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] text-sm">' +
                            '<button id="clearTechnicianSearchBtn" onclick="window.waitingListClearTechnicianSearch()" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition hidden">' +
                                '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>' +
                                '</svg>' +
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
                        '<p class="text-xs text-gray-500 mb-3">Click to remove</p>' +
                        '<div class="relative mb-4">' +
                            '<svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>' +
                            '</svg>' +
                            '<input type="text" id="assignedTechnicianSearchInput" placeholder="Search assigned..." oninput="window.waitingListSearchAssignedTechnicians(this.value)" class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] text-sm">' +
                            '<button id="clearAssignedSearchBtn" onclick="window.waitingListClearAssignedSearch()" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition hidden">' +
                                '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>' +
                                '</svg>' +
                            '</button>' +
                        '</div>' +
                    '</div>' +
                    '<div id="assignedTechniciansContainer" class="overflow-y-auto space-y-3"></div>' +
                '</div>' +
            '</div>' +
        '</div>' +
        '<!-- Fixed Footer -->' +
        '<div class="flex-shrink-0 px-4 sm:px-6 py-4 border-t border-gray-200 bg-white">' +
            (isWaiting ?
                '<div class="flex items-center justify-end">' +
                    '<div class="flex flex-col-reverse sm:flex-row gap-3 w-full sm:w-auto">' +
                        '<button onclick="closeModal()" class="w-full sm:w-auto px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium">Cancel</button>' +
                        '<button onclick="window.waitingListConfirmAssign()" class="w-full sm:w-auto px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium">Confirm Assignment</button>' +
                    '</div>' +
                '</div>'
            :
                '<div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">' +
                    '<div class="flex-shrink-0 w-full sm:w-auto sm:max-w-xs">' +
                        '<div class="relative">' +
                            '<button type="button" id="statusDropdownButton" onclick="window.waitingListToggleStatusDropdown()" class="w-full px-4 py-3 text-left bg-white border border-gray-300 rounded-lg shadow-sm hover:border-[#003047] focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-[#003047] transition-all flex items-center justify-between">' +
                                '<span id="statusDropdownText" class="text-base text-gray-900">In Progress</span>' +
                                '<svg id="statusDropdownIcon" class="w-5 h-5 text-gray-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>' +
                                '</svg>' +
                            '</button>' +
                            '<div id="statusDropdownMenu" class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg">' +
                                '<div class="py-1">' +
                                    '<button type="button" id="statusOptionWaiting" onclick="window.waitingListSelectStatus(\'waiting\')" class="w-full px-4 py-3 text-left text-base text-gray-900 hover:bg-gray-50 transition flex items-center gap-2"><span>Waiting</span></button>' +
                                    '<button type="button" id="statusOptionInProgress" onclick="window.waitingListSelectStatus(\'in-progress\')" class="w-full px-4 py-3 text-left text-base text-gray-900 hover:bg-gray-50 transition flex items-center gap-2"><span>In Progress</span></button>' +
                                    '<button type="button" id="statusOptionCompleted" onclick="window.waitingListSelectStatus(\'completed\')" class="w-full px-4 py-3 text-left text-base text-gray-900 hover:bg-gray-50 transition flex items-center gap-2"><span>Completed</span></button>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                    '<div class="flex flex-col-reverse sm:flex-row gap-3 w-full sm:w-auto">' +
                        '<button onclick="closeModal()" class="w-full sm:w-auto px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium">Cancel</button>' +
                        '<button onclick="window.waitingListConfirmAssign()" class="w-full sm:w-auto px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium">Confirm Assignment</button>' +
                    '</div>' +
                '</div>'
            ) +
        '</div>' +
    '</div>';
    openModal(modalHtml, 'large-flex', false);

    // Remove previous resize handler if exists
    if (resizeHandlerForTechnicians) {
        window.removeEventListener('resize', resizeHandlerForTechnicians);
    }

    // Function to set dynamic height for both technician containers
    resizeHandlerForTechnicians = function() {
        var availableContainer = document.getElementById('availableTechniciansContainer');
        var assignedContainer = document.getElementById('assignedTechniciansContainer');

        var screenHeight = window.innerHeight;
        var screenWidth = window.innerWidth;
        var containerHeight;

        // Modal is 80vh, need to account for header (~10vh), footer (~12vh),
        // content area padding (~4vh), column headers with search (~18vh)
        // Available space: ~36vh maximum

        if (screenWidth < 640) {
            // Mobile: stacked columns, smaller height to fit both sections
            containerHeight = Math.floor(screenHeight * 0.25) + 'px'; // ~25vh
        } else if (screenWidth < 1024) {
            // Tablet: still might be stacked, medium height
            containerHeight = Math.floor(screenHeight * 0.30) + 'px'; // ~30vh
        } else {
            // Desktop: side-by-side columns, can use more height
            containerHeight = Math.floor(screenHeight * 0.35) + 'px'; // ~35vh
        }

        // Set height for both containers
        if (availableContainer) {
            availableContainer.style.height = containerHeight;
        }
        if (assignedContainer) {
            assignedContainer.style.height = containerHeight;
        }
    };

    setTimeout(function() {
        loadTechniciansForAssign();

        var searchInput = document.getElementById('technicianSearchInput');
        var clearBtn = document.getElementById('clearTechnicianSearchBtn');
        if (searchInput && clearBtn) {
            clearBtn.classList.add('hidden');
        }

        var assignedSearchInput = document.getElementById('assignedTechnicianSearchInput');
        var assignedClearBtn = document.getElementById('clearAssignedSearchBtn');
        if (assignedSearchInput && assignedClearBtn) {
            assignedClearBtn.classList.add('hidden');
        }

        // Set initial height on load
        resizeHandlerForTechnicians();

        // Add resize event listener to adapt height on window resize
        window.addEventListener('resize', resizeHandlerForTechnicians);
    }, 50);
}
function loadTechniciansForAssign() {
    if (allTechnicians && allTechnicians.length) {
        availableTechnicians = allTechnicians;
        originalTechnicianOrder = availableTechnicians.map(function(t) { return t.id; });
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
        return;
    }

    fetch(base + '/users').then(function(r) { return r.json(); }).then(function(data) {
        availableTechnicians = (data.users || []).filter(function(u) { return u.role === 'technician' || u.userlevel === 'technician'; });
        originalTechnicianOrder = availableTechnicians.map(function(t) { return t.id; });
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
        var aIdStr = a.id.toString(), bIdStr = b.id.toString();
        var aIsAssigned = assignedTechnicianIds.indexOf(aIdStr) >= 0;
        var bIsAssigned = assignedTechnicianIds.indexOf(bIdStr) >= 0;
        if (aIsAssigned && !bIsAssigned) return 1;
        if (!aIsAssigned && bIsAssigned) return -1;
        var aOnline = !!(a.clock_in && !a.clock_out);
        var bOnline = !!(b.clock_in && !b.clock_out);
        if (aOnline && !bOnline) return -1;
        if (!aOnline && bOnline) return 1;
        var aServices = typeof a.services === 'number' ? a.services : 0;
        var bServices = typeof b.services === 'number' ? b.services : 0;
        var diff = aServices - bServices;
        if (diff !== 0) return diff;
        var aTime = a.clock_in ? new Date(a.clock_in).getTime() : Infinity;
        var bTime = b.clock_in ? new Date(b.clock_in).getTime() : Infinity;
        return aTime - bTime;
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
        var avatarHtml = techPhoto
            ? '<img src="' + techPhoto.replace(/"/g, '&quot;') + '" alt="" class="w-12 h-12 rounded-full object-cover' + (isAssigned ? ' opacity-50 grayscale' : '') + '">'
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
        var assignedAvatarHtml = techPhoto
            ? '<img src="' + techPhoto.replace(/"/g, '&quot;') + '" alt="" class="w-12 h-12 rounded-full object-cover">'
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
    if (!assignedTechnicianIds.length) { alert('Please assign at least one technician'); return; }
    if (!currentAppointmentId) { showErrorMessage('Appointment not found.'); return; }
    var payload = { assigned_technician: assignedTechnicianIds.map(function(id) { return parseInt(id, 10); }), status: 'unpaid' };
    var btn = document.querySelector('[onclick*="waitingListConfirmAssign"]');
    if (btn) { btn.disabled = true; btn.textContent = 'Saving...'; }
    salonApi.put(apiAppointmentsUrl + '/' + currentAppointmentId, payload).then(function(res) {
        var data = res.data;
        var idx = allAppointments.findIndex(function(a) { return a.id === currentAppointmentId; });
        if (idx >= 0 && data) allAppointments[idx] = data;
        else if (data) allAppointments.push(data);
        mergeAppointmentsWithCustomers();
        allMergedData = allMergedData.filter(function(item) { return (item.status || '').toLowerCase() === 'waiting'; });
        applyFilters();
        var names = assignedTechnicianIds.map(function(id) { var t = availableTechnicians.find(function(x) { return x.id.toString() === id; }); return t ? t.firstName + ' ' + t.lastName : ''; }).filter(Boolean);
        var message = res.message || (currentCustomerName + ' assigned to ' + names.join(', ') + ' successfully.');
        showSuccessMessage(message);
        closeModal();
        assignedTechnicianIds = [];
        currentCustomerId = null;
        currentCustomerName = '';
        currentAppointmentId = null;
        // startSessionEnabled removed; status is always set to 'unpaid' on confirm
    }).catch(function(err) {
        showErrorMessage(err.message || 'Failed to save assignment.');
    }).finally(function() {
        if (btn) { btn.disabled = false; btn.textContent = 'Confirm Assignment'; }
    });
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
        mergeAppointmentsWithCustomers();
        allMergedData = allMergedData.filter(function(item) { return (item.status || '').toLowerCase() === 'waiting'; });
        currentStatusFilter = getStatusFromURL();
        if (!new URLSearchParams(window.location.search).get('status')) {
            var url = new URL(window.location);
            url.searchParams.set('status', currentStatusFilter);
            window.history.replaceState({}, '', url);
        }
        updateTabStates(currentStatusFilter);
        applyFilters();
        window.addEventListener('popstate', function() { currentStatusFilter = getStatusFromURL(); updateTabStates(currentStatusFilter); applyFilters(); });
    } catch (err) {
        console.error('Error fetching data:', err);
        showErrorMessage('Failed to load data');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var saved = localStorage.getItem('customersPerPage');
    if (saved) { var sel = document.getElementById('perPageSelect'); if (sel) { sel.value = saved; PAGE_SIZE = saved === 'all' ? Infinity : parseInt(saved, 10); } }
    setViewUI(currentView);
    fetchCustomers().then(function() { window.toggleView(currentView); });
});
})();
</script>
@endpush
@endsection

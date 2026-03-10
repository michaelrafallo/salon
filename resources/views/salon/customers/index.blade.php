@extends('layouts.salon')

@section('content')
@php
    $customersViewUrl = route('salon.customers.view');
    $apiCustomersUrl = url('api/salon/customers');
@endphp
<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Customers</h1>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-1">
                    <button id="gridViewBtn" onclick="salonCustomersToggleView('grid')" class="p-2 rounded-md hover:bg-white transition active:scale-95">
                        <svg class="w-5 h-5 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    </button>
                    <button id="listViewBtn" onclick="salonCustomersToggleView('list')" class="p-2 rounded-md hover:bg-white transition active:scale-95">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    </button>
                </div>
                <button type="button" onclick="salonCustomersOpenNewModal()" class="px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm sm:text-base active:scale-95">+ New Customer</button>
            </div>
        </div>
        <div class="mb-6">
            <div class="relative max-w-md">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" id="customerSearchInput" placeholder="Search customers by name, phone, or email..." oninput="salonCustomersSearch(this.value)" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-base">
            </div>
        </div>
        <div id="customersBulkBar" class="mb-4 flex flex-wrap items-center gap-3" data-bulk-action-url="{{ route('api.salon.settings.clickaio.bulk-action') }}">
            <input type="checkbox" id="customersSelectAll" class="w-4 h-4 text-[#003047] border-gray-300 rounded focus:ring-[#003047]">
            <select id="customersBulkAction" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent bg-white text-sm">
                <option value="">Select Action</option>
                <option value="sync_from">Sync from Clickaio</option>
                <option value="sync_to">Sync to Clickaio</option>
                <option value="delete">Delete</option>
            </select>
            <button type="button" id="customersBulkGoBtn" disabled class="px-5 py-2 bg-[#003047] text-white text-sm rounded-lg hover:bg-[#002535] transition font-medium active:scale-95 disabled:opacity-40 disabled:cursor-not-allowed disabled:active:scale-100">Go</button>
            <span id="customersBulkCount" class="text-xs text-gray-500"></span>
        </div>
        <div id="gridView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6"></div>
        <div id="listView" class="hidden">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-3 py-3 w-10"></th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Visits</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Visit</th>
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
                <select id="perPageSelect" onchange="salonCustomersChangePerPage(this.value)" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-sm bg-white cursor-pointer">
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
window.salonCustomersBootstrap = window.salonCustomersBootstrap || @json($customersBootstrap ?? null);
var viewUrl = '{{ $customersViewUrl }}';
var apiCustomersUrl = '{{ $apiCustomersUrl }}';
var allCustomers = [], customersData = [], PAGE_SIZE = 15, currentPage = 1, totalPages = 1, currentSearchTerm = '';
var currentView = localStorage.getItem('customersView') || 'grid';
var colorClasses = [
    { bg: 'bg-[#e6f0f3]', text: 'text-[#003047]' }, { bg: 'bg-purple-100', text: 'text-purple-600' },
    { bg: 'bg-teal-100', text: 'text-teal-600' }, { bg: 'bg-indigo-100', text: 'text-indigo-600' },
    { bg: 'bg-rose-100', text: 'text-rose-600' }, { bg: 'bg-blue-100', text: 'text-blue-600' },
    { bg: 'bg-amber-100', text: 'text-amber-600' }, { bg: 'bg-green-100', text: 'text-green-600' }
];
function getInitials(c) { return c.initials || ((c.firstName||'')[0] + (c.lastName||'')[0]).toUpperCase(); }
function getLastVisit(c) {
    if (c.lastVisit) return c.lastVisit;
    if (c.lastVisitDate) {
        var d2 = new Date(c.lastVisitDate);
        if (!isNaN(d2.getTime())) {
            var days2 = Math.floor((new Date() - d2) / 86400000);
            if (days2 === 0) return 'Today'; if (days2 === 1) return '1 day ago'; if (days2 < 7) return days2 + ' days ago'; if (days2 < 14) return '1 week ago'; if (days2 < 30) return Math.floor(days2/7) + ' weeks ago';
            return Math.floor(days2/30) + ' months ago';
        }
    }
    if (c.createdAt) {
        var d = new Date(c.createdAt), days = Math.floor((new Date() - d) / 86400000);
        if (days === 0) return 'Today'; if (days === 1) return '1 day ago'; if (days < 7) return days + ' days ago'; if (days < 14) return '1 week ago'; if (days < 30) return Math.floor(days/7) + ' weeks ago';
        return Math.floor(days/30) + ' months ago';
    }
    return 'N/A';
}
function getTotalVisits(c) { return (c.totalVisits != null ? c.totalVisits : c.totalBookings) || 0; }
function getPaginated() {
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
    h += '<button class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300 ' + (currentPage === 1 ? 'text-gray-400 cursor-not-allowed opacity-50' : 'bg-white text-[#003047] hover:bg-gray-100') + '" ' + (currentPage === 1 ? 'disabled' : '') + ' onclick="salonCustomersGoToPage(1)">&laquo;</button>';
    h += '<button class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300 ' + (currentPage === 1 ? 'text-gray-400 cursor-not-allowed opacity-50' : 'bg-white text-[#003047] hover:bg-gray-100') + '" ' + (currentPage === 1 ? 'disabled' : '') + ' onclick="salonCustomersGoToPage(' + (currentPage - 1) + ')">&lt;</button>';
    for (var p = Math.max(1, currentPage - 2), end = Math.min(totalPages, p + 4); p <= end; p++) {
        h += '<button class="px-3 py-2 text-sm font-medium rounded-md border ' + (p === currentPage ? 'bg-[#003047] text-white' : 'bg-white text-gray-700 border-gray-300 hover:border-[#003047]') + '" onclick="salonCustomersGoToPage(' + p + ')">' + p + '</button>';
    }
    h += '<button class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300 ' + (currentPage === totalPages ? 'text-gray-400 cursor-not-allowed opacity-50' : 'bg-white text-[#003047] hover:bg-gray-100') + '" ' + (currentPage === totalPages ? 'disabled' : '') + ' onclick="salonCustomersGoToPage(' + (currentPage + 1) + ')">&gt;</button>';
    h += '<button class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300 ' + (currentPage === totalPages ? 'text-gray-400 cursor-not-allowed opacity-50' : 'bg-white text-[#003047] hover:bg-gray-100') + '" ' + (currentPage === totalPages ? 'disabled' : '') + ' onclick="salonCustomersGoToPage(' + totalPages + ')">&raquo;</button></div>';
    el.innerHTML = h;
}
window.salonCustomersGoToPage = function(p) { if (p < 1 || p > totalPages) return; currentPage = p; salonCustomersRender(); };
function updateCounter() {
    var el = document.getElementById('customersResultsCounter');
    if (!el) return;
    var total = customersData.length;
    if (total === 0) { el.textContent = 'No results found'; return; }
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity) { el.textContent = 'Showing all ' + total + ' result' + (total !== 1 ? 's' : ''); return; }
    var start = (currentPage - 1) * PAGE_SIZE, end = Math.min(start + PAGE_SIZE, total);
    el.textContent = 'Showing ' + (start + 1) + '-' + end + ' of ' + total + ' result' + (total !== 1 ? 's' : '');
}
function renderGrid() {
    var el = document.getElementById('gridView');
    if (!el) return;
    var list = getPaginated();
    if (list.length === 0) { el.innerHTML = '<div class="col-span-full text-center py-12"><p class="text-gray-500 text-sm">No customers found</p></div>'; return; }
    el.innerHTML = list.map(function(c, i) {
        var color = colorClasses[i % colorClasses.length], initials = getInitials(c), name = c.firstName + ' ' + c.lastName, visits = getTotalVisits(c), last = getLastVisit(c);
        return '<div class="customer-card bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow relative group flex flex-col"><div class="absolute top-3 right-3 z-10" onclick="event.stopPropagation()"><input type="checkbox" class="cust-row-cb w-4 h-4 text-[#003047] border-gray-300 rounded focus:ring-[#003047]" data-id="' + c.id + '"></div><div onclick="window.location.href=\'' + viewUrl + '?id=' + c.id + '\'" class="cursor-pointer flex-1"><div class="flex items-center gap-4 mb-4"><div class="w-16 h-16 ' + color.bg + ' rounded-full flex items-center justify-center flex-shrink-0"><span class="text-2xl font-bold ' + color.text + '">' + initials + '</span></div><div class="flex-1 min-w-0"><h3 class="font-semibold text-gray-900 text-lg truncate">' + name + '</h3><p class="text-sm text-gray-500 truncate">' + (c.email || '') + '</p>' + (c.ghlContactId ? '<p class="text-xs text-green-600 truncate font-mono flex items-center gap-1"><svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>' + c.ghlContactId + '</p>' : '') + '<p class="text-sm text-gray-500">' + (c.phone || '') + '</p></div></div><div class="grid grid-cols-2 gap-3 pt-4 border-t border-gray-200"><div><p class="text-xs text-gray-500">Total Visits</p><p class="text-lg font-bold text-gray-900">' + visits + '</p></div><div><p class="text-xs text-gray-500">Last Visit</p><p class="text-sm font-medium text-gray-900">' + last + '</p></div></div></div><div class="flex gap-2 pt-4 mt-4 border-t border-gray-100" onclick="event.stopPropagation()"><button type="button" onclick="salonCustomersOpenEditModal(' + c.id + ')" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm active:scale-95"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>Quick Edit</button><button type="button" onclick="salonCustomersDelete(' + c.id + ', \'' + name.replace(/'/g, "\\'") + '\')" class="inline-flex items-center justify-center px-4 py-2 text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition font-medium text-sm active:scale-95"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button></div></div>';
    }).join('');
}
function renderList() {
    var tbody = document.getElementById('listViewBody');
    if (!tbody) return;
    var list = getPaginated();
    if (list.length === 0) { tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-12 text-center"><p class="text-gray-500 text-sm">No customers found</p></td></tr>'; return; }
    tbody.innerHTML = list.map(function(c, i) {
        var color = colorClasses[i % colorClasses.length], initials = getInitials(c), name = c.firstName + ' ' + c.lastName, visits = getTotalVisits(c), last = getLastVisit(c);
        return '<tr class="hover:bg-gray-50 transition"><td class="px-3 py-4" onclick="event.stopPropagation()"><input type="checkbox" class="cust-row-cb w-4 h-4 text-[#003047] border-gray-300 rounded focus:ring-[#003047]" data-id="' + c.id + '"></td><td class="px-6 py-4 whitespace-nowrap"><div class="flex items-center"><div onclick="window.location.href=\'' + viewUrl + '?id=' + c.id + '\'" class="w-10 h-10 ' + color.bg + ' rounded-full flex items-center justify-center flex-shrink-0 mr-3 cursor-pointer"><span class="text-sm font-bold ' + color.text + '">' + initials + '</span></div><div onclick="window.location.href=\'' + viewUrl + '?id=' + c.id + '\'" class="cursor-pointer"><div class="text-sm font-medium text-gray-900">' + name + '</div><div class="text-sm text-gray-500">' + (c.email || '') + '</div>' + (c.ghlContactId ? '<div class="text-xs text-green-600 font-mono flex items-center gap-1"><svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>' + c.ghlContactId + '</div>' : '') + '</div></div></td><td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' + (c.phone || '') + '</td><td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">' + visits + '</td><td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' + last + '</td><td class="px-6 py-4 whitespace-nowrap text-sm text-right"><div class="flex items-center justify-end gap-2"><button type="button" onclick="event.stopPropagation(); salonCustomersOpenEditModal(' + c.id + ')" class="inline-flex items-center justify-center w-8 h-8 cursor-pointer bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition active:scale-95" title="Quick Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></button><button type="button" onclick="event.stopPropagation(); salonCustomersDelete(' + c.id + ', \'' + name.replace(/'/g, "\\'") + '\')" class="inline-flex items-center justify-center w-8 h-8 cursor-pointer text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition active:scale-95" title="Delete"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button></div></td></tr>';
    }).join('');
}
function salonCustomersRender() {
    updatePaginationState();
    renderGrid();
    renderList();
    renderPagination();
    updateCounter();
}
window.salonCustomersSearch = function(val) { currentSearchTerm = (val || '').toLowerCase(); applyFilters(); };
window.salonCustomersChangePerPage = function(val) {
    PAGE_SIZE = val === 'all' ? Infinity : parseInt(val, 10);
    currentPage = 1;
    localStorage.setItem('customersPerPage', val);
    salonCustomersRender();
};
function applyFilters() {
    customersData = allCustomers.filter(function(c) {
        if (!currentSearchTerm) return true;
        var text = (c.firstName + ' ' + c.lastName + ' ' + (c.email || '') + ' ' + (c.phone || '')).toLowerCase();
        return text.indexOf(currentSearchTerm) >= 0;
    });
    currentPage = 1;
    salonCustomersRender();
}
window.salonCustomersToggleView = function(view) {
    currentView = view;
    localStorage.setItem('customersView', view);
    setViewUI(view);
    if (view === 'grid') {
        renderGrid();
        return;
    }
    renderList();
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
window.salonCustomersOpenNewModal = function() {
    var content = '<div class="p-6"><div class="flex items-center justify-between mb-4"><h3 class="text-xl font-bold text-gray-900">New Customer</h3><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><form onsubmit="salonCustomersSaveCustomer(event)" class="space-y-4"><div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">First Name</label><input type="text" name="first_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label><input type="text" name="last_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div></div><div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">Email</label><input type="email" name="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label><input type="tel" name="phone" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Address (optional)</label><input type="text" name="address" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div class="flex justify-end gap-3 pt-4"><button type="button" onclick="closeModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">Cancel</button><button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Save Customer</button></div></form></div>';
    openModal(content);
};
window.salonCustomersSaveCustomer = function(e) {
    e.preventDefault();
    var form = e.target;
    var first = (form.elements && form.elements['first_name']) ? form.elements['first_name'].value : (form.first_name && form.first_name.value);
    var last = (form.elements && form.elements['last_name']) ? form.elements['last_name'].value : (form.last_name && form.last_name.value);
    var email = (form.elements && form.elements['email']) ? form.elements['email'].value : (form.email && form.email.value);
    var phone = (form.elements && form.elements['phone']) ? form.elements['phone'].value : (form.phone && form.phone.value);
    var data = { first_name: (first || '').trim(), last_name: (last || '').trim(), email: (email || '').trim() || null, phone: (phone || '').trim() || null };
    var btn = form.querySelector('button[type="submit"]');
    if (btn) { btn.disabled = true; btn.textContent = 'Saving...'; }
    if (typeof salonApi === 'undefined') {
        if (btn) { btn.disabled = false; btn.textContent = 'Save Customer'; }
        showErrorMessage('Unable to send request. Please refresh the page.');
        return;
    }
    salonApi.post(apiCustomersUrl, data).then(function(res) {
        showSuccessMessage(res.message || 'Customer added successfully!');
        closeModal();
        var d = res.data || {};
        allCustomers.unshift({ id: d.id, firstName: d.firstName || data.first_name, lastName: d.lastName || data.last_name, email: d.email || null, phone: d.phone || null, ghlContactId: d.ghlContactId || null, createdAt: d.createdAt || new Date().toISOString().slice(0, 10), totalBookings: 0, totalVisits: 0, lastVisit: null, lastVisitDate: null, totalSpent: 0 });
        applyFilters();
        salonCustomersRender();
    }).catch(function(err) {
        var msg = err.message || 'Failed to save customer.';
        if (err.body && err.body.errors && typeof err.body.errors === 'object') {
            var firstError = Object.values(err.body.errors)[0];
            if (Array.isArray(firstError) && firstError[0]) msg = firstError[0];
            else if (typeof firstError === 'string') msg = firstError;
        }
        showErrorMessage(msg);
        if (btn) { btn.disabled = false; btn.textContent = 'Save Customer'; }
    });
};
window.salonCustomersOpenEditModal = function(id) {
    var c = allCustomers.find(function(x) { return x.id === id; });
    if (!c) return;
    var content = '<div class="p-6"><div class="flex items-center justify-between mb-4"><h3 class="text-xl font-bold text-gray-900">Edit Customer</h3><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><form onsubmit="salonCustomersUpdateCustomer(event, ' + c.id + ')" class="space-y-4"><div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">First Name</label><input type="text" name="first_name" value="' + (c.firstName || '').replace(/"/g, '&quot;') + '" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label><input type="text" name="last_name" value="' + (c.lastName || '').replace(/"/g, '&quot;') + '" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div></div><div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">Email</label><input type="email" name="email" value="' + (c.email || '').replace(/"/g, '&quot;') + '" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Phone</label><input type="tel" name="phone" value="' + (c.phone || '').replace(/"/g, '&quot;') + '" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div></div><div class="flex justify-end gap-3 pt-4"><button type="button" onclick="closeModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">Cancel</button><button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Update Customer</button></div></form></div>';
    openModal(content);
};
window.salonCustomersUpdateCustomer = function(e, id) {
    e.preventDefault();
    var form = e.target;
    var data = { first_name: form.first_name.value.trim(), last_name: form.last_name.value.trim(), email: form.email.value.trim() || null, phone: form.phone.value.trim() || null };
    var btn = form.querySelector('button[type="submit"]');
    if (btn) { btn.disabled = true; btn.textContent = 'Updating...'; }
    salonApi.put(apiCustomersUrl + '/' + id, data).then(function(res) {
        showSuccessMessage(res.message || 'Customer updated.');
        closeModal();
        var idx = allCustomers.findIndex(function(x) { return x.id === parseInt(id, 10); });
        if (idx >= 0) allCustomers[idx] = { id: res.data.id, firstName: res.data.firstName, lastName: res.data.lastName, email: res.data.email, phone: res.data.phone, createdAt: allCustomers[idx].createdAt, totalBookings: allCustomers[idx].totalBookings, totalSpent: allCustomers[idx].totalSpent };
        applyFilters();
        salonCustomersRender();
    }).catch(function(err) {
        showErrorMessage(err.message || 'Failed to update customer.');
        if (btn) { btn.disabled = false; btn.textContent = 'Update Customer'; }
    });
};
window.salonCustomersDelete = function(id, name) {
    openConfirmModal({
        title: 'Delete customer',
        message: 'You are about to permanently remove ' + (name ? boldName(name) : 'this customer') + '. Do you want to continue?',
        confirmLabel: 'Delete',
        onConfirm: function() {
            salonApi.delete(apiCustomersUrl + '/' + id).then(function() {
                showSuccessMessage('Customer deleted.');
                allCustomers = allCustomers.filter(function(c) { return c.id !== id && c.id !== parseInt(id, 10); });
                applyFilters();
                salonCustomersRender();
            }).catch(function(err) {
                showErrorMessage(err.message || 'Failed to delete customer.');
            });
        }
    });
};
// Bulk actions
var bulkBar = document.getElementById('customersBulkBar');
var bulkSelectAll = document.getElementById('customersSelectAll');
var bulkAction = document.getElementById('customersBulkAction');
var bulkGoBtn = document.getElementById('customersBulkGoBtn');
var bulkCountEl = document.getElementById('customersBulkCount');
var bulkActionUrl = bulkBar ? bulkBar.getAttribute('data-bulk-action-url') : '';

function escHtml(s) { return (s || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }

function bulkUpdateGoState() {
    var hasAction = bulkAction && bulkAction.value !== '';
    var checked = document.querySelectorAll('.cust-row-cb:checked');
    bulkGoBtn.disabled = !(hasAction && checked.length > 0);
    bulkCountEl.textContent = checked.length > 0 ? checked.length + ' selected' : '';
}

function bulkBindCheckboxes() {
    document.querySelectorAll('.cust-row-cb').forEach(function(cb) {
        cb.addEventListener('change', function() {
            var allCbs = document.querySelectorAll('.cust-row-cb');
            var checkedCbs = document.querySelectorAll('.cust-row-cb:checked');
            bulkSelectAll.checked = allCbs.length > 0 && allCbs.length === checkedCbs.length;
            bulkSelectAll.indeterminate = checkedCbs.length > 0 && checkedCbs.length < allCbs.length;
            bulkUpdateGoState();
        });
    });
}

// Hook into render to rebind checkboxes
var origRender = salonCustomersRender;
salonCustomersRender = function() {
    origRender();
    bulkSelectAll.checked = false;
    bulkSelectAll.indeterminate = false;
    bulkBindCheckboxes();
    bulkUpdateGoState();
};

bulkSelectAll.addEventListener('change', function() {
    var checked = this.checked;
    document.querySelectorAll('.cust-row-cb').forEach(function(cb) { cb.checked = checked; });
    this.indeterminate = false;
    bulkUpdateGoState();
});

bulkAction.addEventListener('change', bulkUpdateGoState);

bulkGoBtn.addEventListener('click', function() {
    var action = bulkAction.value;
    if (!action) return;
    var ids = [];
    document.querySelectorAll('.cust-row-cb:checked').forEach(function(cb) {
        ids.push(parseInt(cb.getAttribute('data-id'), 10));
    });
    if (ids.length === 0) return;

    var actionLabels = { sync_from: 'Sync from Clickaio', sync_to: 'Sync to Clickaio', 'delete': 'Delete Customers' };
    var actionLabel = actionLabels[action] || action;
    var iconColor = action === 'delete' ? 'bg-red-100' : 'bg-blue-100';
    var iconSvgColor = action === 'delete' ? 'text-red-600' : 'text-blue-600';
    var confirmBtnClass = action === 'delete' ? 'bg-red-500 hover:bg-red-600' : 'bg-[#003047] hover:bg-[#002535]';
    var iconSvg = action === 'delete'
        ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>'
        : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>';
    var modalMessage = action === 'delete'
        ? 'Are you sure you want to delete <strong>' + ids.length + '</strong> selected customer' + (ids.length > 1 ? 's' : '') + '? This action cannot be undone.'
        : 'Are you sure you want to run <strong>' + escHtml(actionLabel) + '</strong> on <strong>' + ids.length + '</strong> customer' + (ids.length > 1 ? 's' : '') + '?';

    var modalContent = '<div class="p-6">'
        + '<div class="flex items-center gap-4 mb-4">'
        + '<style>@keyframes cust-spin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}.cust-spinning{animation:cust-spin 1s linear infinite}</style>'
        + '<div class="w-12 h-12 ' + iconColor + ' rounded-full flex items-center justify-center flex-shrink-0">'
        + '<svg id="cust-bulk-icon" class="w-6 h-6 ' + iconSvgColor + '" fill="none" stroke="currentColor" viewBox="0 0 24 24">' + iconSvg + '</svg>'
        + '</div>'
        + '<div class="flex-1"><h3 class="text-xl font-bold text-gray-900">' + escHtml(actionLabel) + '</h3></div>'
        + '</div>'
        + '<p id="cust-bulk-message" class="text-gray-700 mb-6 ml-16">' + modalMessage + '</p>'
        + '<div id="cust-bulk-progress" class="hidden mb-4 ml-16">'
        + '<div class="flex items-center justify-between mb-1"><span id="cust-bulk-progress-label" class="text-sm font-medium text-gray-700">Processing...</span><span id="cust-bulk-progress-count" class="text-sm font-semibold text-gray-900">0/' + ids.length + '</span></div>'
        + '<div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden"><div id="cust-bulk-progress-bar" class="h-3 rounded-full transition-all duration-300 ease-out" style="width: 0%; background-color: ' + (action === 'delete' ? '#ef4444' : '#003047') + '"></div></div>'
        + '<div class="flex items-center justify-between mt-1"><p id="cust-bulk-progress-detail" class="text-xs text-gray-500"></p><span id="cust-bulk-progress-pct" class="text-xs font-semibold text-gray-700">0%</span></div>'
        + '</div>'
        + '<div id="cust-bulk-buttons" class="flex justify-end gap-3 pt-4 border-t border-gray-200">'
        + '<button onclick="closeModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">Cancel</button>'
        + '<button type="button" id="cust-bulk-confirm-btn" class="px-6 py-3 text-white ' + confirmBtnClass + ' rounded-lg transition font-medium active:scale-95">' + (action === 'delete' ? 'Yes, Delete' : 'Yes, Proceed') + '</button>'
        + '</div></div>';

    if (typeof openModal === 'function') {
        openModal(modalContent);
        var confirmBtn = document.getElementById('cust-bulk-confirm-btn');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', function() {
                // Show progress UI
                var progressSection = document.getElementById('cust-bulk-progress');
                var progressBar = document.getElementById('cust-bulk-progress-bar');
                var progressPct = document.getElementById('cust-bulk-progress-pct');
                var progressLabel = document.getElementById('cust-bulk-progress-label');
                var progressDetail = document.getElementById('cust-bulk-progress-detail');
                var buttonsSection = document.getElementById('cust-bulk-buttons');
                var messageEl = document.getElementById('cust-bulk-message');

                messageEl.classList.add('hidden');
                progressSection.classList.remove('hidden');
                buttonsSection.innerHTML = '<button onclick="closeModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95 opacity-50 cursor-not-allowed" disabled>Please wait...</button>';
                var bulkIcon = document.getElementById('cust-bulk-icon');
                if (bulkIcon && action !== 'delete') bulkIcon.classList.add('cust-spinning');

                // Prevent page reload/navigation while processing
                function bulkBeforeUnload(e) { e.preventDefault(); e.returnValue = ''; }
                window.addEventListener('beforeunload', bulkBeforeUnload);

                var progressCount = document.getElementById('cust-bulk-progress-count');
                function updateProgress(done, total, statusText) {
                    var pct = Math.round((done / total) * 100);
                    progressBar.style.width = pct + '%';
                    progressCount.textContent = done + '/' + total;
                    progressPct.textContent = pct + '%';
                    progressDetail.textContent = statusText || '';
                }

                if (action === 'delete') {
                    progressLabel.textContent = 'Deleting customers...';
                    var completed = 0, successCount = 0, failCount = 0;
                    var deletedIds = [];

                    // Process sequentially for visible progress
                    function deleteNext(index) {
                        if (index >= ids.length) {
                            // Done
                            window.removeEventListener('beforeunload', bulkBeforeUnload);
                            allCustomers = allCustomers.filter(function(c) { return deletedIds.indexOf(c.id) === -1; });
                            customersData = customersData.filter(function(c) { return deletedIds.indexOf(c.id) === -1; });
                            progressLabel.textContent = 'Complete!';
                            updateProgress(ids.length, ids.length, successCount + ' deleted' + (failCount > 0 ? ', ' + failCount + ' failed' : ''));
                            progressBar.style.backgroundColor = '#22c55e';
                            buttonsSection.innerHTML = '<button onclick="closeModal()" class="px-6 py-3 text-white bg-green-500 rounded-lg hover:bg-green-600 transition font-medium active:scale-95">Done</button>';
                            bulkSelectAll.checked = false;
                            bulkSelectAll.indeterminate = false;
                            bulkAction.value = '';
                            bulkUpdateGoState();
                            salonCustomersRender();
                            if (failCount > 0) {
                                showSuccessMessage(successCount + ' deleted, ' + failCount + ' failed.');
                            } else {
                                showSuccessMessage(successCount + ' customer' + (successCount !== 1 ? 's' : '') + ' deleted.');
                            }
                            return;
                        }
                        var id = ids[index];
                        var cust = allCustomers.find(function(c) { return c.id === id; });
                        var custName = cust ? (cust.firstName + ' ' + cust.lastName).trim() : '#' + id;
                        updateProgress(index, ids.length, 'Deleting ' + custName);
                        salonApi.delete(apiCustomersUrl + '/' + id)
                            .then(function() { successCount++; deletedIds.push(id); })
                            .catch(function() { failCount++; })
                            .finally(function() {
                                completed++;
                                deleteNext(index + 1);
                            });
                    }
                    deleteNext(0);
                } else {
                    // Sync: process one customer at a time for real-time progress
                    var syncLabel = action === 'sync_from' ? 'Syncing from Clickaio...' : 'Syncing to Clickaio...';
                    progressLabel.textContent = syncLabel;
                    var syncCompleted = 0, syncSynced = 0, syncSkipped = 0, syncFailed = 0;

                    function syncNext(index) {
                        if (index >= ids.length) {
                            window.removeEventListener('beforeunload', bulkBeforeUnload);
                            if (bulkIcon) bulkIcon.classList.remove('cust-spinning');
                            progressLabel.textContent = 'Complete!';
                            var details = [];
                            if (syncSynced > 0) details.push(syncSynced + ' synced');
                            if (syncSkipped > 0) details.push(syncSkipped + ' skipped');
                            if (syncFailed > 0) details.push(syncFailed + ' failed');
                            updateProgress(ids.length, ids.length, details.join(', '));
                            progressBar.style.backgroundColor = '#22c55e';
                            buttonsSection.innerHTML = '<button onclick="closeModal()" class="px-6 py-3 text-white bg-green-500 rounded-lg hover:bg-green-600 transition font-medium active:scale-95">Done</button>';
                            bulkSelectAll.checked = false;
                            bulkSelectAll.indeterminate = false;
                            bulkAction.value = '';
                            bulkUpdateGoState();
                            salonCustomersRender();
                            if (typeof showSuccessMessage === 'function') showSuccessMessage(details.join(', ') + '.');
                            return;
                        }
                        var id = ids[index];
                        var cust = allCustomers.find(function(c) { return c.id === id; });
                        var custName = cust ? (cust.firstName + ' ' + cust.lastName).trim() : '#' + id;
                        updateProgress(index, ids.length, 'Processing ' + custName + '...');

                        salonApi.post(bulkActionUrl, { action: action, customer_ids: [id] })
                            .then(function(res) {
                                var d = res && res.data ? res.data : {};
                                if (d.synced > 0) {
                                    syncSynced++;
                                    var updatedMap = res && res.customers ? res.customers : {};
                                    allCustomers.forEach(function(c, i) { if (updatedMap.hasOwnProperty(c.id)) allCustomers[i].ghlContactId = updatedMap[c.id]; });
                                    customersData.forEach(function(c, i) { if (updatedMap.hasOwnProperty(c.id)) customersData[i].ghlContactId = updatedMap[c.id]; });
                                    updateProgress(index + 1, ids.length, custName + ' — found!');
                                } else if (d.skipped > 0) {
                                    syncSkipped++;
                                    updateProgress(index + 1, ids.length, custName + ' — already synced');
                                } else {
                                    syncFailed++;
                                    updateProgress(index + 1, ids.length, custName + ' — not found');
                                }
                            })
                            .catch(function() {
                                syncFailed++;
                                updateProgress(index + 1, ids.length, custName + ' — error');
                            })
                            .finally(function() {
                                syncCompleted++;
                                syncNext(index + 1);
                            });
                    }
                    syncNext(0);
                }
            });
        }
    }
});

if (window.salonCustomersBootstrap && window.salonCustomersBootstrap.customers) {
    allCustomers = window.salonCustomersBootstrap.customers || [];
    var saved = localStorage.getItem('customersPerPage');
    if (saved) { var sel = document.getElementById('perPageSelect'); if (sel) { sel.value = saved; PAGE_SIZE = saved === 'all' ? Infinity : parseInt(saved, 10); } }
    setViewUI(currentView);
    applyFilters();
    salonCustomersToggleView(currentView);
} else {
    fetch(base + '/customers').then(function(r) { return r.json(); }).then(function(data) {
        allCustomers = data.customers || [];
        var saved = localStorage.getItem('customersPerPage');
        if (saved) { var sel = document.getElementById('perPageSelect'); if (sel) { sel.value = saved; PAGE_SIZE = saved === 'all' ? Infinity : parseInt(saved, 10); } }
        setViewUI(currentView);
        applyFilters();
        salonCustomersToggleView(currentView);
    }).catch(function(err) { console.error(err); showErrorMessage('Failed to load customers'); });
}
})();
</script>
@endpush
@endsection

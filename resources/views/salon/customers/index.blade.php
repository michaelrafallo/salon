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
        <div id="gridView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6"></div>
        <div id="listView" class="hidden">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Visits</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Visit</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
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
        return '<div class="customer-card bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow relative group"><div onclick="window.location.href=\'' + viewUrl + '?id=' + c.id + '\'" class="flex items-center gap-4 mb-4 cursor-pointer"><div class="w-16 h-16 ' + color.bg + ' rounded-full flex items-center justify-center flex-shrink-0"><span class="text-2xl font-bold ' + color.text + '">' + initials + '</span></div><div class="flex-1 min-w-0"><h3 class="font-semibold text-gray-900 text-lg truncate">' + name + '</h3><p class="text-sm text-gray-500 truncate">' + (c.email || '') + '</p><p class="text-sm text-gray-500">' + (c.phone || '') + '</p></div></div><div class="grid grid-cols-2 gap-3 mb-4 pt-4 border-t border-gray-200"><div><p class="text-xs text-gray-500">Total Visits</p><p class="text-lg font-bold text-gray-900">' + visits + '</p></div><div><p class="text-xs text-gray-500">Last Visit</p><p class="text-sm font-medium text-gray-900">' + last + '</p></div></div><div class="flex gap-2 pt-2 border-t border-gray-100" onclick="event.stopPropagation()"><button type="button" onclick="salonCustomersOpenEditModal(' + c.id + ')" class="px-3 py-1.5 text-sm bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Edit</button><button type="button" onclick="salonCustomersDelete(' + c.id + ', \'' + name.replace(/'/g, "\\'") + '\')" class="px-3 py-1.5 text-sm bg-red-50 text-red-600 rounded-lg hover:bg-red-100">Delete</button></div></div>';
    }).join('');
}
function renderList() {
    var tbody = document.getElementById('listViewBody');
    if (!tbody) return;
    var list = getPaginated();
    if (list.length === 0) { tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-12 text-center"><p class="text-gray-500 text-sm">No customers found</p></td></tr>'; return; }
    tbody.innerHTML = list.map(function(c, i) {
        var color = colorClasses[i % colorClasses.length], initials = getInitials(c), name = c.firstName + ' ' + c.lastName, visits = getTotalVisits(c), last = getLastVisit(c);
        return '<tr class="hover:bg-gray-50 transition"><td class="px-6 py-4 whitespace-nowrap"><div class="flex items-center"><div onclick="window.location.href=\'' + viewUrl + '?id=' + c.id + '\'" class="w-10 h-10 ' + color.bg + ' rounded-full flex items-center justify-center flex-shrink-0 mr-3 cursor-pointer"><span class="text-sm font-bold ' + color.text + '">' + initials + '</span></div><div onclick="window.location.href=\'' + viewUrl + '?id=' + c.id + '\'" class="cursor-pointer"><div class="text-sm font-medium text-gray-900">' + name + '</div><div class="text-sm text-gray-500">' + (c.email || '') + '</div></div></div></td><td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' + (c.phone || '') + '</td><td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">' + visits + '</td><td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' + last + '</td><td class="px-6 py-4 whitespace-nowrap text-sm"><button type="button" onclick="event.stopPropagation(); salonCustomersOpenEditModal(' + c.id + ')" class="text-[#003047] hover:underline mr-2">Edit</button><button type="button" onclick="event.stopPropagation(); salonCustomersDelete(' + c.id + ', \'' + name.replace(/'/g, "\\'") + '\')" class="text-red-600 hover:underline">Delete</button></td></tr>';
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
        allCustomers.unshift({ id: d.id, firstName: d.firstName || data.first_name, lastName: d.lastName || data.last_name, email: d.email || null, phone: d.phone || null, createdAt: d.createdAt || new Date().toISOString().slice(0, 10), totalBookings: 0, totalVisits: 0, lastVisit: null, lastVisitDate: null, totalSpent: 0 });
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
        message: 'You are about to permanently remove this customer' + (name ? ': ' + (name || '').replace(/"/g, '\\"') : '') + '. Do you want to continue?',
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

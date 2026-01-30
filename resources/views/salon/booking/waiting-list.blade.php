@extends('layouts.salon')

@section('content')
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
                <button onclick="filterByStatus('all')" id="filterAll" class="filter-tab px-1 py-3 text-sm font-medium text-gray-900 border-b-2 border-[#003047] transition">All</button>
                <button onclick="filterByStatus('walk-in')" id="filterWalkIn" class="filter-tab px-1 py-3 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 transition">Walk-In</button>
                <button onclick="filterByStatus('booked')" id="filterBooked" class="filter-tab px-1 py-3 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 transition">Booked</button>
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
var base = window.salonJsonBase || '{{ url("json") }}';
var allCustomers = [], allAppointments = [], allTechnicians = [], allMergedData = [], customersData = [];
var PAGE_SIZE = 15, currentPage = 1, totalPages = 1, currentSearchTerm = '', currentStatusFilter = 'waiting';
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
        return Object.assign({}, customer, { appointmentId: apt.id, appointment: type, status: apt.status || 'waiting', created_at: apt.created_at, assigned_technician: apt.assigned_technician || [], services: apt.services || [] });
    }).filter(Boolean);
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
        return '<div class="customer-card bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow flex flex-col h-full"><div class="flex-1"><div class="flex items-center gap-4 mb-4"><div class="w-16 h-16 ' + color.bg + ' rounded-full flex items-center justify-center flex-shrink-0"><span class="text-2xl font-bold ' + color.text + '">' + initials + '</span></div><div class="flex-1 min-w-0"><h3 class="font-normal text-gray-900 text-xl truncate">' + fullName + '</h3><p class="text-sm text-gray-500">' + (customer.phone || '') + '</p><div class="mt-2">' + renderTechniciansList(customer.assigned_technician) + '</div></div></div></div><div class="pt-4 border-t border-gray-200 mt-auto space-y-3"><span class="inline-block px-3 py-1 ' + statusClass + ' text-xs font-medium rounded-full">' + aptType + '</span><button onclick="event.stopPropagation(); assignCustomer(\'' + customer.id + '\', \'' + fullName.replace(/'/g, "\\'") + '\')" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm active:scale-95"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>Assign</button></div></div>';
    }).join('');
}
function renderListView() {
    var tbody = document.getElementById('listViewBody');
    if (!tbody) return;
    var list = getPaginatedCustomers();
    if (list.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-12 text-center"><p class="text-gray-500 text-sm">No customers found</p></td></tr>';
        return;
    }
    tbody.innerHTML = list.map(function(customer, index) {
        var color = colorClasses[index % colorClasses.length], initials = getInitials(customer), fullName = customer.firstName + ' ' + customer.lastName;
        var aptType = customer.appointment || 'Walk-In', statusClass = aptType.toLowerCase() === 'walk-in' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700';
        var rowNum = (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity) ? index + 1 : (currentPage - 1) * PAGE_SIZE + index + 1;
        return '<tr class="customer-row hover:bg-gray-50 transition"><td class="px-3 py-4 whitespace-nowrap text-center"><div class="text-sm text-gray-600">' + rowNum + '</div></td><td class="px-6 py-4 whitespace-nowrap"><div class="flex items-center"><div class="w-10 h-10 ' + color.bg + ' rounded-full flex items-center justify-center flex-shrink-0 mr-3"><span class="text-sm font-bold ' + color.text + '">' + initials + '</span></div><div><div class="text-base font-normal text-gray-900">' + fullName + '</div></div></div></td><td class="px-6 py-4 whitespace-nowrap"><div class="text-sm text-gray-900">' + (customer.phone || '') + '</div></td><td class="px-6 py-4">' + renderTechniciansList(customer.assigned_technician) + '</td><td class="px-6 py-4 whitespace-nowrap"><span class="inline-block px-3 py-1 ' + statusClass + ' text-xs font-medium rounded-full">' + aptType + '</span></td><td class="px-6 py-4 whitespace-nowrap text-right"><button onclick="event.stopPropagation(); assignCustomer(\'' + customer.id + '\', \'' + fullName.replace(/'/g, "\\'") + '\')" class="inline-flex items-center gap-2 px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm active:scale-95"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>Assign</button></td></tr>';
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
        return new Date(a.created_at) - new Date(b.created_at);
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

window.toggleView = function(view) {
    currentView = view;
    localStorage.setItem('customersView', view);
    var gridView = document.getElementById('gridView'), listView = document.getElementById('listView');
    var gridBtn = document.getElementById('gridViewBtn'), listBtn = document.getElementById('listViewBtn');
    if (view === 'grid') {
        gridView.classList.remove('hidden'); listView.classList.add('hidden');
        if (gridBtn && gridBtn.querySelector('svg')) { gridBtn.querySelector('svg').classList.remove('text-gray-500'); gridBtn.querySelector('svg').classList.add('text-gray-900'); }
        if (listBtn && listBtn.querySelector('svg')) { listBtn.querySelector('svg').classList.remove('text-gray-900'); listBtn.querySelector('svg').classList.add('text-gray-500'); }
        renderGridView();
    } else {
        gridView.classList.add('hidden'); listView.classList.remove('hidden');
        if (listBtn && listBtn.querySelector('svg')) { listBtn.querySelector('svg').classList.remove('text-gray-500'); listBtn.querySelector('svg').classList.add('text-gray-900'); }
        if (gridBtn && gridBtn.querySelector('svg')) { gridBtn.querySelector('svg').classList.remove('text-gray-900'); gridBtn.querySelector('svg').classList.add('text-gray-500'); }
        renderListView();
    }
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
    var firstName = document.getElementById('newCustomerFirstName').value, lastName = document.getElementById('newCustomerLastName').value, phone = document.getElementById('newCustomerPhone').value, email = document.getElementById('newCustomerEmail').value;
    var newId = allCustomers.length ? Math.max.apply(null, allCustomers.map(function(c) { return c.id; })) + 1 : 1;
    var newCustomer = { id: newId, firstName: firstName, lastName: lastName, phone: phone || '', email: email || '', createdAt: new Date().toISOString().split('T')[0] };
    allCustomers.push(newCustomer);
    closeAddCustomerModal();
    selectCustomerForWaitingList(newId);
    showSuccessMessage('Customer added successfully!');
}
window.addToWaitingList = function() {
    if (!window.selectedCustomer) { alert('Please select a customer first'); return; }
    showSuccessMessage(window.selectedCustomer.firstName + ' ' + window.selectedCustomer.lastName + ' added to waiting list successfully!');
    closeModal();
    window.selectedCustomer = null;
    setTimeout(function() { location.reload(); }, 1500);
};

var availableTechnicians = [], originalTechnicianOrder = [], assignedTechnicianIds = [], currentCustomerId = null, currentCustomerName = '', technicianSearchTerm = '', startSessionEnabled = false, selectedStatus = 'waiting';
window.assignCustomer = function(customerId, customerName) {
    currentCustomerId = customerId; currentCustomerName = customerName; technicianSearchTerm = ''; startSessionEnabled = false; selectedStatus = 'waiting';
    var customer = customersData.find(function(c) { return c.id.toString() === customerId.toString(); });
    assignedTechnicianIds = (customer && customer.assigned_technician && Array.isArray(customer.assigned_technician)) ? customer.assigned_technician.map(function(id) { return id.toString(); }) : [];
    var isWaiting = customer && customer.status && customer.status.toLowerCase() === 'waiting';
    if (!isWaiting && customer && customer.status) { var s = customer.status.toLowerCase(); selectedStatus = (s === 'in-progress' || s === 'completed') ? s : 'in-progress'; }
    var modalHtml = '<div class="p-6"><div class="flex items-center justify-between mb-6"><h3 class="text-xl font-bold text-gray-900">Assign Technician to ' + customerName.replace(/'/g, "\\'") + '</h3><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><div class="grid grid-cols-2 gap-6"><div class="border border-gray-200 rounded-lg p-4"><div class="flex items-center justify-between mb-2"><h4 class="text-sm font-semibold text-gray-900">Available Technicians</h4><span id="availableCount" class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">0</span></div><p class="text-xs text-gray-500 mb-2">Click to assign technicians</p><div class="relative mb-4"><svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg><input type="text" id="technicianSearchInput" placeholder="Search technicians..." oninput="window.waitingListSearchTechnicians(this.value)" class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] text-sm"><button id="clearTechnicianSearchBtn" onclick="window.waitingListClearTechnicianSearch()" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><div id="availableTechniciansContainer" class="space-y-3 min-h-[200px] max-h-[300px] overflow-y-auto"></div></div><div class="border border-gray-200 rounded-lg p-4"><div class="flex items-center justify-between mb-2"><h4 class="text-sm font-semibold text-gray-900">Assigned Technicians</h4><span id="assignedCount" class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">0</span></div><p class="text-xs text-gray-500 mb-4">Click to remove</p><div id="assignedTechniciansContainer" class="space-y-3 min-h-[200px] max-h-[300px] overflow-y-auto"></div></div></div>' + (isWaiting ? '<div class="pt-6 mt-6 border-t border-gray-200"><div class="flex items-center justify-between"><div class="flex items-center gap-4"><button type="button" id="startSessionToggle" onclick="window.waitingListToggleStartSession()" disabled class="relative inline-flex h-7 w-14 items-center rounded-full bg-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-[#003047] focus:ring-offset-2 opacity-50 cursor-not-allowed" role="switch" aria-checked="false"><span id="startSessionToggleThumb" class="inline-block h-5 w-5 transform rounded-full bg-white transition-transform translate-x-1"></span></button><label for="startSessionToggle" class="text-base font-medium text-gray-400 cursor-not-allowed">Start Session</label></div><div class="flex gap-3"><button onclick="closeModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium">Cancel</button><button onclick="window.waitingListConfirmAssign()" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium">Save Assignment</button></div></div></div>' : '<div class="pt-6 mt-6 border-t border-gray-200"><div class="flex items-center justify-between"><div class="flex-1 max-w-xs"><div class="relative"><button type="button" id="statusDropdownButton" onclick="window.waitingListToggleStatusDropdown()" class="w-full px-4 py-3 text-left bg-white border border-gray-300 rounded-lg shadow-sm hover:border-[#003047] focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-[#003047] transition-all flex items-center justify-between"><span id="statusDropdownText" class="text-base text-gray-900">In Progress</span><svg id="statusDropdownIcon" class="w-5 h-5 text-gray-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg></button><div id="statusDropdownMenu" class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg"><div class="py-1"><button type="button" id="statusOptionWaiting" onclick="window.waitingListSelectStatus(\'waiting\')" class="w-full px-4 py-3 text-left text-base text-gray-900 hover:bg-gray-50 transition flex items-center gap-2"><span>Waiting</span></button><button type="button" id="statusOptionInProgress" onclick="window.waitingListSelectStatus(\'in-progress\')" class="w-full px-4 py-3 text-left text-base text-gray-900 hover:bg-gray-50 transition flex items-center gap-2"><span>In Progress</span></button><button type="button" id="statusOptionCompleted" onclick="window.waitingListSelectStatus(\'completed\')" class="w-full px-4 py-3 text-left text-base text-gray-900 hover:bg-gray-50 transition flex items-center gap-2"><span>Completed</span></button></div></div></div></div><div class="flex gap-3"><button onclick="closeModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium">Cancel</button><button onclick="window.waitingListConfirmAssign()" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium">Save Assignment</button></div></div></div>') + '</div>';
    openModal(modalHtml, 'large', false);
    setTimeout(function() {
        var modal = document.getElementById('modalContainer');
        if (modal) modal.style.maxHeight = '95vh';
        loadTechniciansForAssign();
        var searchInput = document.getElementById('technicianSearchInput');
        var clearBtn = document.getElementById('clearTechnicianSearchBtn');
        if (searchInput && clearBtn) {
            clearBtn.classList.add('hidden');
        }
    }, 50);
}
function loadTechniciansForAssign() {
    fetch(base + '/users.json').then(function(r) { return r.json(); }).then(function(data) {
        availableTechnicians = (data.users || []).filter(function(u) { return u.role === 'technician' || u.userlevel === 'technician'; });
        originalTechnicianOrder = availableTechnicians.map(function(t) { return t.id; });
        renderAvailableTechnicians();
        renderAssignedTechnicians();
        updateCounts();
        updateStartSessionToggleState();
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
        var aIdx = originalTechnicianOrder.indexOf(a.id);
        var bIdx = originalTechnicianOrder.indexOf(b.id);
        return aIdx - bIdx;
    });
    container.innerHTML = filtered.map(function(tech) {
        var idStr = tech.id.toString(), isAssigned = assignedTechnicianIds.indexOf(idStr) >= 0;
        var inits = tech.initials || (tech.firstName || '')[0] + (tech.lastName || '')[0];
        var name = tech.firstName + ' ' + tech.lastName;
        var containerCls = isAssigned ? 'flex items-center gap-3 p-2 rounded-lg transition-colors opacity-50 grayscale cursor-pointer group hover:bg-gray-100' : 'flex items-center gap-3 cursor-pointer group hover:bg-gray-50 p-2 rounded-lg transition-colors';
        var avatarCls = isAssigned ? 'w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center' : 'w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center';
        var initialCls = isAssigned ? 'text-sm font-bold text-gray-500' : 'text-sm font-bold text-gray-600';
        var nameCls = isAssigned ? 'text-base font-medium text-gray-400' : 'text-base font-medium text-gray-900';
        var badgeCls = isAssigned ? 'absolute -bottom-1 -right-1 w-5 h-5 bg-gray-400 text-white rounded-full flex items-center justify-center text-xs font-bold border-2 border-white' : 'absolute -bottom-1 -right-1 w-5 h-5 bg-[#003047] text-white rounded-full flex items-center justify-center text-xs font-bold border-2 border-white';
        return '<div onclick="' + (isAssigned ? 'removeAssignedTechnician(' + tech.id + ')' : 'assignTechnician(' + tech.id + ')') + '" class="' + containerCls + '"><div class="relative flex-shrink-0"><div class="' + avatarCls + '"><span class="' + initialCls + '">' + inits + '</span></div><div class="' + badgeCls + '">0</div></div><div class="flex-1"><p class="' + nameCls + '">' + name + '</p></div></div>';
    }).join('');
}
function renderAssignedTechnicians() {
    var container = document.getElementById('assignedTechniciansContainer');
    if (!container) return;
    if (!assignedTechnicianIds.length) {
        container.innerHTML = '<div class="flex items-center justify-center h-full min-h-[200px]"><p class="text-sm text-gray-400">No technicians assigned</p></div>';
        return;
    }
    container.innerHTML = assignedTechnicianIds.map(function(idStr) {
        var tech = availableTechnicians.find(function(t) { return t.id.toString() === idStr; });
        if (!tech) return '';
        var inits = tech.initials || (tech.firstName || '')[0] + (tech.lastName || '')[0];
        var name = tech.firstName + ' ' + tech.lastName;
        return '<div onclick="removeAssignedTechnician(' + tech.id + ')" class="flex items-center gap-3 cursor-pointer group hover:bg-gray-50 p-2 rounded-lg transition-colors"><div class="relative flex-shrink-0"><div class="w-12 h-12 bg-[#003047] rounded-full flex items-center justify-center"><span class="text-sm font-bold text-white">' + inits + '</span></div><div class="absolute -bottom-1 -right-1 w-5 h-5 bg-[#003047] text-white rounded-full flex items-center justify-center text-xs font-bold border-2 border-white">0</div></div><div class="flex-1"><p class="text-base font-medium text-gray-900">' + name + '</p></div></div>';
    }).join('');
}
window.assignTechnician = function(techId) {
    var idStr = techId.toString();
    if (assignedTechnicianIds.indexOf(idStr) < 0) {
        assignedTechnicianIds.push(idStr);
        renderAvailableTechnicians();
        renderAssignedTechnicians();
        updateCounts();
        updateStartSessionToggleState();
    }
};
window.removeAssignedTechnician = function(techId) {
    assignedTechnicianIds = assignedTechnicianIds.filter(function(id) { return id !== techId.toString(); });
    renderAvailableTechnicians();
    renderAssignedTechnicians();
    updateCounts();
    updateStartSessionToggleState();
};
function updateCounts() {
    var availEl = document.getElementById('availableCount');
    var assignEl = document.getElementById('assignedCount');
    if (availEl) availEl.textContent = availableTechnicians.length.toString();
    if (assignEl) assignEl.textContent = assignedTechnicianIds.length.toString();
}
function updateStartSessionToggleState() {
    var toggle = document.getElementById('startSessionToggle');
    var label = document.querySelector('label[for="startSessionToggle"]');
    if (!toggle) return;
    var hasAssigned = assignedTechnicianIds.length > 0;
    if (hasAssigned) {
        toggle.disabled = false;
        toggle.classList.remove('opacity-50', 'cursor-not-allowed');
        toggle.classList.add('cursor-pointer');
        if (label) {
            label.classList.remove('text-gray-400', 'cursor-not-allowed');
            label.classList.add('text-gray-700', 'cursor-pointer');
        }
    } else {
        toggle.disabled = true;
        toggle.classList.add('opacity-50', 'cursor-not-allowed');
        toggle.classList.remove('cursor-pointer');
        if (label) {
            label.classList.add('text-gray-400', 'cursor-not-allowed');
            label.classList.remove('text-gray-700', 'cursor-pointer');
        }
        startSessionEnabled = false;
        var thumb = document.getElementById('startSessionToggleThumb');
        if (thumb) {
            toggle.classList.remove('bg-[#003047]');
            toggle.classList.add('bg-gray-200');
            toggle.setAttribute('aria-checked', 'false');
            thumb.classList.remove('translate-x-8');
            thumb.classList.add('translate-x-1');
        }
    }
}
window.waitingListToggleStartSession = function() {
    if (assignedTechnicianIds.length === 0) return;
    startSessionEnabled = !startSessionEnabled;
    var toggle = document.getElementById('startSessionToggle');
    var thumb = document.getElementById('startSessionToggleThumb');
    if (toggle && thumb) {
        if (startSessionEnabled) {
            toggle.classList.remove('bg-gray-200');
            toggle.classList.add('bg-[#003047]');
            toggle.setAttribute('aria-checked', 'true');
            thumb.classList.remove('translate-x-1');
            thumb.classList.add('translate-x-8');
        } else {
            toggle.classList.remove('bg-[#003047]');
            toggle.classList.add('bg-gray-200');
            toggle.setAttribute('aria-checked', 'false');
            thumb.classList.remove('translate-x-8');
            thumb.classList.add('translate-x-1');
        }
    }
};
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
    var names = assignedTechnicianIds.map(function(id) { var t = availableTechnicians.find(function(x) { return x.id.toString() === id; }); return t ? t.firstName + ' ' + t.lastName : ''; }).filter(Boolean);
    var message = startSessionEnabled ? currentCustomerName + ' assigned to ' + names.join(', ') + ' and session started!' : currentCustomerName + ' assigned to ' + names.join(', ') + ' successfully!';
    showSuccessMessage(message);
    closeModal();
    assignedTechnicianIds = [];
    currentCustomerId = null;
    currentCustomerName = '';
    startSessionEnabled = false;
};

async function fetchCustomers() {
    try {
        var custRes = await fetch(base + '/customers.json'), aptRes = await fetch(base + '/appointments.json'), techRes = await fetch(base + '/users.json');
        var custData = await custRes.json(), aptData = await aptRes.json(), techData = await techRes.json();
        allCustomers = custData.customers || [];
        allAppointments = aptData.appointments || [];
        allTechnicians = (techData.users || []).filter(function(u) { return u.role === 'technician' || u.userlevel === 'technician'; });
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
    fetchCustomers().then(function() { window.toggleView(currentView); });
});
})();
</script>
@endpush
@endsection

@extends('layouts.salon')

@section('content')
@php
    $techniciansViewUrl = route('salon.technicians.view');
    $isAdmin = in_array(session('salon_role', 'admin'), ['admin'], true);
@endphp
<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Technicians</h1>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-1">
                    <button id="gridViewBtn" onclick="salonTechToggleView('grid')" class="p-2 rounded-md hover:bg-white transition active:scale-95">
                        <svg class="w-5 h-5 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    </button>
                    <button id="listViewBtn" onclick="salonTechToggleView('list')" class="p-2 rounded-md hover:bg-white transition active:scale-95">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    </button>
                </div>
                <button type="button" onclick="salonTechOpenAddModal()" class="px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm sm:text-base active:scale-95">+ Add Technician</button>
            </div>
        </div>
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-2 border-b border-gray-200 overflow-x-auto">
                <button onclick="salonTechFilter('all')" id="tab-all" class="px-4 py-2 text-sm font-medium text-[#003047] border-b-2 border-[#003047] transition whitespace-nowrap">All</button>
                <button onclick="salonTechFilter('active')" id="tab-online" class="px-4 py-2 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 transition whitespace-nowrap">Active</button>
                <button onclick="salonTechFilter('inactive')" id="tab-offline" class="px-4 py-2 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 transition whitespace-nowrap">Inactive</button>
            </div>
            <div class="relative w-full sm:w-[400px]">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" id="technicianSearchInput" placeholder="Search technicians..." oninput="salonTechSearch(this.value)" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-base">
            </div>
        </div>
        <div id="gridView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6"></div>
        <div id="listView" class="hidden">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Technician</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            @if($isAdmin)
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tip</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commission</th>
                            @endif
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clock</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="listViewBody" class="bg-white divide-y divide-gray-200"></tbody>
                </table>
            </div>
        </div>
        <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div id="techniciansResultsCounter" class="text-sm text-gray-600"></div>
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-600">Show:</label>
                <select id="perPageSelect" onchange="salonTechChangePerPage(this.value)" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] text-sm bg-white cursor-pointer">
                    <option value="15">15</option><option value="25">25</option><option value="50">50</option><option value="100">100</option><option value="250">250</option><option value="500">500</option><option value="all">All</option>
                </select>
                <span class="text-sm text-gray-600">per page</span>
            </div>
        </div>
        <div id="techniciansPagination" class="mt-4 flex justify-center"></div>
    </div>
</main>
@push('scripts')
<script>
(function() {
var base = window.salonJsonBase || '{{ url("json") }}';
var viewUrl = '{{ $techniciansViewUrl }}';
var isAdmin = {{ $isAdmin ? 'true' : 'false' }};
var allTechnicians = [], techniciansData = [], technicianData = {}, currentStatusFilter = 'all', currentSearchTerm = '';
var PAGE_SIZE = 15, currentPage = 1, totalPages = 1, currentView = localStorage.getItem('techniciansView') || 'grid';
var colorClasses = [
    { bg: 'bg-[#e6f0f3]', text: 'text-[#003047]' }, { bg: 'bg-purple-100', text: 'text-purple-600' },
    { bg: 'bg-teal-100', text: 'text-teal-600' }, { bg: 'bg-indigo-100', text: 'text-indigo-600' },
    { bg: 'bg-rose-100', text: 'text-rose-600' }, { bg: 'bg-blue-100', text: 'text-blue-600' },
    { bg: 'bg-amber-100', text: 'text-amber-600' }, { bg: 'bg-green-100', text: 'text-green-600' }
];
function getInitials(t) { return t.initials || ((t.firstName||'')[0] + (t.lastName||'')[0]).toUpperCase(); }
function getPaginated() {
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity) return techniciansData;
    var start = (currentPage - 1) * PAGE_SIZE;
    return techniciansData.slice(start, start + PAGE_SIZE);
}
function updatePaginationState() {
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity) { totalPages = 1; currentPage = 1; return; }
    totalPages = Math.max(1, Math.ceil(techniciansData.length / PAGE_SIZE));
    if (currentPage > totalPages) currentPage = totalPages;
    if (currentPage < 1) currentPage = 1;
}
function renderPagination() {
    var el = document.getElementById('techniciansPagination');
    if (!el) return;
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity || techniciansData.length <= PAGE_SIZE || totalPages <= 1) { el.innerHTML = ''; return; }
    var h = '<div class="flex items-center gap-2 justify-center">';
    var disabledClass = 'text-gray-400 cursor-not-allowed opacity-50';
    var activeClass = 'bg-[#003047] text-white';
    var defaultClass = 'bg-white text-gray-700 border border-gray-300 hover:border-[#003047] hover:text-[#003047]';
    h += '<button class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300 ' + (currentPage === 1 ? disabledClass : 'bg-white text-[#003047] hover:bg-gray-100 hover:border-[#003047]') + '" ' + (currentPage === 1 ? 'disabled' : '') + ' onclick="salonTechGoToPage(1)" title="First page">&laquo;</button>';
    h += '<button class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300 ' + (currentPage === 1 ? disabledClass : 'bg-white text-[#003047] hover:bg-gray-100 hover:border-[#003047]') + '" ' + (currentPage === 1 ? 'disabled' : '') + ' onclick="salonTechGoToPage(' + (currentPage - 1) + ')" title="Previous page">&lt;</button>';
    var maxButtons = 6;
    var startPage = Math.max(1, currentPage - Math.floor(maxButtons / 2));
    var endPage = startPage + maxButtons - 1;
    if (endPage > totalPages) {
        endPage = totalPages;
        startPage = Math.max(1, endPage - maxButtons + 1);
    }
    for (var p = startPage; p <= endPage; p++) {
        var isActive = p === currentPage;
        h += '<button class="px-3 py-2 text-sm font-medium rounded-md border ' + (isActive ? activeClass : defaultClass) + '" onclick="salonTechGoToPage(' + p + ')">' + p + '</button>';
    }
    h += '<button class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300 ' + (currentPage === totalPages ? disabledClass : 'bg-white text-[#003047] hover:bg-gray-100 hover:border-[#003047]') + '" ' + (currentPage === totalPages ? 'disabled' : '') + ' onclick="salonTechGoToPage(' + (currentPage + 1) + ')" title="Next page">&gt;</button>';
    h += '<button class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300 ' + (currentPage === totalPages ? disabledClass : 'bg-white text-[#003047] hover:bg-gray-100 hover:border-[#003047]') + '" ' + (currentPage === totalPages ? 'disabled' : '') + ' onclick="salonTechGoToPage(' + totalPages + ')" title="Last page">&raquo;</button></div>';
    el.innerHTML = h;
}
function updateCounter() {
    var el = document.getElementById('techniciansResultsCounter');
    if (!el) return;
    var total = techniciansData.length;
    if (total === 0) { el.textContent = 'No results found'; return; }
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity) { el.textContent = 'Showing all ' + total + ' result' + (total !== 1 ? 's' : ''); return; }
    var start = (currentPage - 1) * PAGE_SIZE, end = Math.min(start + PAGE_SIZE, total);
    el.textContent = 'Showing ' + (start + 1) + '-' + end + ' of ' + total + ' result' + (total !== 1 ? 's' : '');
}
function applyFilters() {
    techniciansData = allTechnicians.filter(function(t) {
        var statusMatch = true;
        if (currentStatusFilter !== 'all') {
            var isLoggedIn = localStorage.getItem('technician_' + t.id + '_loggedIn') === 'true';
            var status = isLoggedIn ? 'active' : 'inactive';
            statusMatch = status === currentStatusFilter;
        }
        var searchMatch = true;
        if (currentSearchTerm) {
            var fullName = t.firstName + ' ' + t.lastName;
            var searchText = (fullName + ' ' + (t.email || '')).toLowerCase();
            searchMatch = searchText.indexOf(currentSearchTerm) >= 0;
        }
        return statusMatch && searchMatch;
    });
    currentPage = 1;
    updatePaginationState();
    salonTechRender();
    updateCounter();
}
function renderGrid() {
    var el = document.getElementById('gridView');
    if (!el) return;
    var list = getPaginated();
    if (list.length === 0) { el.innerHTML = '<div class="col-span-full text-center py-12"><p class="text-gray-500 text-sm">No technicians found</p></div>'; return; }
    el.innerHTML = list.map(function(t, i) {
        var inits = getInitials(t), name = t.firstName + ' ' + t.lastName, color = colorClasses[i % colorClasses.length];
        var isLoggedIn = localStorage.getItem('technician_' + t.id + '_loggedIn') === 'true';
        var statusClass = isLoggedIn ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700';
        var statusText = isLoggedIn ? 'Active' : 'Inactive';
        var stats = isAdmin ? '<div class="grid grid-cols-3 gap-3 mb-4 pt-4 border-t border-gray-200"><div><p class="text-xs text-gray-500">Total</p><p class="text-lg font-bold text-gray-900">$' + (t.totalEarnings || 0).toFixed(2) + '</p></div><div><p class="text-xs text-gray-500">Tip</p><p class="text-lg font-bold text-gray-900">$' + (t.totalTips || 0).toFixed(2) + '</p></div><div><p class="text-xs text-gray-500">Commission</p><p class="text-lg font-bold text-gray-900">$' + (t.totalCommission || 0).toFixed(2) + '</p></div></div>' : '';
        return '<div onclick="event.stopPropagation(); window.location.href=\'' + viewUrl + '?id=' + t.id + '\'" class="technician-card bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow cursor-pointer active:scale-95" data-status="' + statusText.toLowerCase() + '"><div class="flex items-center gap-4 mb-4"><div class="w-16 h-16 ' + color.bg + ' rounded-full flex items-center justify-center flex-shrink-0"><span class="text-2xl font-bold ' + color.text + '">' + inits + '</span></div><div class="flex-1 min-w-0"><h3 class="font-semibold text-gray-900 text-lg truncate">' + name + '</h3><p class="text-sm text-gray-500">Technician</p><div class="flex items-center gap-2 mt-1"><span id="status-badge-' + t.id + '" class="px-2 py-1 ' + statusClass + ' text-xs font-medium rounded">' + statusText + '</span></div></div></div>' + stats + '</div>';
    }).join('');
}
function renderList() {
    var tbody = document.getElementById('listViewBody');
    if (!tbody) return;
    var list = getPaginated();
    var colCount = isAdmin ? 7 : 5;
    if (list.length === 0) { tbody.innerHTML = '<tr><td colspan="' + colCount + '" class="px-6 py-12 text-center"><p class="text-gray-500 text-sm">No technicians found</p></td></tr>'; return; }
    tbody.innerHTML = list.map(function(t, i) {
        var inits = getInitials(t), name = t.firstName + ' ' + t.lastName, color = colorClasses[i % colorClasses.length];
        var isLoggedIn = localStorage.getItem('technician_' + t.id + '_loggedIn') === 'true';
        var statusClass = isLoggedIn ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700';
        var statusText = isLoggedIn ? 'Active' : 'Inactive';
        var clockInTime = localStorage.getItem('technician_' + t.id + '_clockIn') || '--';
        var clockOutTime = localStorage.getItem('technician_' + t.id + '_clockOut') || '--';
        var buttonClass = isLoggedIn ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600';
        var buttonText = isLoggedIn ? 'Clock Out' : 'Clock In';
        var adminCols = isAdmin ? '<td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">$' + (t.totalEarnings || 0).toFixed(2) + '</td><td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">$' + (t.totalTips || 0).toFixed(2) + '</td><td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">$' + (t.totalCommission || 0).toFixed(2) + '</td>' : '';
        return '<tr onclick="event.stopPropagation(); window.location.href=\'' + viewUrl + '?id=' + t.id + '\'" class="technician-row hover:bg-gray-50 cursor-pointer transition" data-status="' + statusText.toLowerCase() + '"><td class="px-6 py-4 whitespace-nowrap"><div class="flex items-center"><div class="w-10 h-10 ' + color.bg + ' rounded-full flex items-center justify-center flex-shrink-0 mr-3"><span class="text-sm font-bold ' + color.text + '">' + inits + '</span></div><div class="text-sm font-medium text-gray-900">' + name + '</div></div></td><td class="px-6 py-4 whitespace-nowrap"><span id="status-badge-list-' + t.id + '" class="px-2 py-1 ' + statusClass + ' text-xs font-medium rounded">' + statusText + '</span></td>' + adminCols + '<td class="px-6 py-4 whitespace-nowrap" onclick="event.stopPropagation()"><div class="space-y-1" id="clockInfo-' + t.id + '"><div class="text-xs text-gray-900 font-medium" id="clockTimeIn-' + t.id + '">In: ' + clockInTime + '</div><div class="text-xs text-gray-900 font-medium" id="clockTimeOut-' + t.id + '">Out: ' + clockOutTime + '</div></div></td><td class="px-6 py-4 whitespace-nowrap text-right" onclick="event.stopPropagation()"><div class="flex items-center justify-end gap-2"><button id="loginToggle-' + t.id + '" onclick="salonTechToggleLogin(' + t.id + ')" class="px-3 py-1.5 ' + buttonClass + ' text-white text-xs font-medium rounded hover:opacity-90 transition active:scale-95">' + buttonText + '</button><button onclick="salonTechPrintTicket(' + t.id + ')" class="px-3 py-1.5 bg-gray-500 text-white text-xs font-medium rounded hover:bg-gray-600 transition active:scale-95 flex items-center gap-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>Ticket</button></div></td></tr>';
    }).join('');
}
function salonTechRender() {
    updatePaginationState();
    renderGrid();
    renderList();
    initializeTechnicianLoginStates();
    renderPagination();
    updateCounter();
}
window.salonTechGoToPage = function(p) {
    if (p < 1 || p > totalPages || p === currentPage) return;
    currentPage = p;
    salonTechRender();
    updateCounter();
};
window.salonTechFilter = function(f) {
    var internalStatus = f;
    var urlStatus = f;
    if (f === 'active') {
        internalStatus = 'active';
        urlStatus = 'active';
    } else if (f === 'inactive') {
        internalStatus = 'inactive';
        urlStatus = 'inactive';
    } else if (f === 'online') {
        internalStatus = 'active';
        urlStatus = 'active';
    } else if (f === 'offline') {
        internalStatus = 'inactive';
        urlStatus = 'inactive';
    }
    currentStatusFilter = internalStatus;
    var url = new URL(window.location);
    if (internalStatus === 'all' || internalStatus === '') {
        url.searchParams.delete('status');
    } else {
        url.searchParams.set('status', urlStatus);
    }
    window.history.pushState({}, '', url);
    document.querySelectorAll('[id^="tab-"]').forEach(function(btn) {
        var id = btn.id;
        var active = (internalStatus === 'all' && id === 'tab-all') || (internalStatus === 'active' && id === 'tab-online') || (internalStatus === 'inactive' && id === 'tab-offline');
        btn.className = 'px-4 py-2 text-sm font-medium whitespace-nowrap border-b-2 transition ' + (active ? 'text-[#003047] border-[#003047]' : 'text-gray-500 border-transparent hover:text-gray-700');
    });
    applyFilters();
};
window.salonTechSearch = function(val) { currentSearchTerm = (val || '').toLowerCase(); applyFilters(); };
window.salonTechChangePerPage = function(val) {
    PAGE_SIZE = val === 'all' ? Infinity : parseInt(val, 10);
    currentPage = 1;
    updatePaginationState();
    salonTechRender();
    updateCounter();
    localStorage.setItem('techniciansPerPage', val);
};
window.salonTechToggleView = function(view) {
    currentView = view;
    localStorage.setItem('techniciansView', view);
    var g = document.getElementById('gridView'), l = document.getElementById('listView'), gb = document.getElementById('gridViewBtn'), lb = document.getElementById('listViewBtn');
    if (view === 'grid') { g.classList.remove('hidden'); l.classList.add('hidden'); if (gb && gb.querySelector('svg')) { gb.querySelector('svg').classList.add('text-gray-900'); gb.querySelector('svg').classList.remove('text-gray-500'); } if (lb && lb.querySelector('svg')) { lb.querySelector('svg').classList.remove('text-gray-900'); lb.querySelector('svg').classList.add('text-gray-500'); } renderGrid(); }
    else { g.classList.add('hidden'); l.classList.remove('hidden'); if (lb && lb.querySelector('svg')) { lb.querySelector('svg').classList.add('text-gray-900'); lb.querySelector('svg').classList.remove('text-gray-500'); } if (gb && gb.querySelector('svg')) { gb.querySelector('svg').classList.remove('text-gray-900'); gb.querySelector('svg').classList.add('text-gray-500'); } renderList(); }
};
function initializeTechnicianLoginStates() {
    allTechnicians.forEach(function(technician) {
        var techId = technician.id;
        var isLoggedIn = localStorage.getItem('technician_' + techId + '_loggedIn') === 'true';
        var button = document.getElementById('loginToggle-' + techId);
        var clockInTime = localStorage.getItem('technician_' + techId + '_clockIn');
        var clockOutTime = localStorage.getItem('technician_' + techId + '_clockOut');
        if (button) {
            if (isLoggedIn) {
                button.textContent = 'Clock Out';
                button.classList.remove('bg-green-500', 'hover:bg-green-600');
                button.classList.add('bg-red-500', 'hover:bg-red-600');
            } else {
                button.textContent = 'Clock In';
                button.classList.remove('bg-red-500', 'hover:bg-red-600');
                button.classList.add('bg-green-500', 'hover:bg-green-600');
            }
        }
        updateStatusBadge(techId, isLoggedIn);
        updateClockDisplay(techId, clockInTime, clockOutTime);
    });
}
function updateStatusBadge(techId, isOnline) {
    var gridBadge = document.getElementById('status-badge-' + techId);
    if (gridBadge) {
        if (isOnline) {
            gridBadge.textContent = 'Active';
            gridBadge.className = 'px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded';
        } else {
            gridBadge.textContent = 'Inactive';
            gridBadge.className = 'px-2 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded';
        }
    }
    var listBadge = document.getElementById('status-badge-list-' + techId);
    if (listBadge) {
        if (isOnline) {
            listBadge.textContent = 'Active';
            listBadge.className = 'px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded';
        } else {
            listBadge.textContent = 'Inactive';
            listBadge.className = 'px-2 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded';
        }
    }
}
function updateClockDisplay(techId, clockInTime, clockOutTime) {
    var clockInEl = document.getElementById('clockTimeIn-' + techId);
    var clockOutEl = document.getElementById('clockTimeOut-' + techId);
    if (clockInEl) clockInEl.textContent = 'In: ' + (clockInTime || '--');
    if (clockOutEl) clockOutEl.textContent = 'Out: ' + (clockOutTime || '--');
}
function formatDateTime(date) {
    var months = ['Jan.', 'Feb.', 'Mar.', 'Apr.', 'May', 'Jun.', 'Jul.', 'Aug.', 'Sep.', 'Oct.', 'Nov.', 'Dec.'];
    var month = months[date.getMonth()];
    var day = date.getDate();
    var year = date.getFullYear();
    var hours = date.getHours();
    var minutes = date.getMinutes();
    var ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12;
    var minutesStr = minutes < 10 ? '0' + minutes : minutes;
    return month + ' ' + day + ', ' + year + ' ' + hours + ':' + minutesStr + ' ' + ampm;
}
window.salonTechToggleLogin = function(techId) {
    var button = document.getElementById('loginToggle-' + techId);
    var isLoggedIn = button.textContent.trim() === 'Clock Out';
    var action = isLoggedIn ? 'Clock Out' : 'Clock In';
    var tech = technicianData[techId];
    if (!tech) {
        console.error('Technician data not found for ID:', techId);
        return;
    }
    salonTechShowLoginModal(tech, action, techId, isLoggedIn);
};
window.salonTechShowLoginModal = function(technician, action, techId, isCurrentlyLoggedIn) {
    var now = new Date();
    var dateTime = now.toLocaleString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true });
    var actionBgColor = action === 'Clock In' ? 'bg-green-100' : 'bg-red-100';
    var actionTextColor = action === 'Clock In' ? 'text-green-700' : 'text-red-700';
    var content = '<div class="p-6 max-w-2xl"><div class="flex items-center justify-between mb-6"><h3 class="text-2xl font-bold text-gray-900">Technician ' + action + '</h3><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><div class="space-y-6"><div class="bg-gray-50 rounded-lg p-5"><h4 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">Technician Details</h4><div class="grid grid-cols-2 gap-4"><div><p class="text-xs text-gray-500 mb-1">Name</p><p class="text-sm font-medium text-gray-900">' + technician.name + '</p></div><div><p class="text-xs text-gray-500 mb-1">Email</p><p class="text-sm font-medium text-gray-900">' + technician.email + '</p></div><div><p class="text-xs text-gray-500 mb-1">Role</p><p class="text-sm font-medium text-gray-900">' + technician.role + '</p></div><div><p class="text-xs text-gray-500 mb-1">ID</p><p class="text-sm font-medium text-gray-900">#' + techId + '</p></div></div></div><div class="bg-gray-50 rounded-lg p-5"><h4 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">Action</h4><div class="flex items-center gap-3"><span class="px-4 py-2 ' + actionBgColor + ' ' + actionTextColor + ' text-sm font-semibold rounded-lg">' + action + '</span><p class="text-sm text-gray-600">' + (action === 'Clock In' ? 'Technician is clocking into the system' : 'Technician is clocking out of the system') + '</p></div></div><div class="bg-gray-50 rounded-lg p-5"><h4 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">Date & Time</h4><p class="text-sm font-medium text-gray-900">' + dateTime + '</p></div></div><div class="flex justify-end gap-3 mt-6 pt-6 border-t border-gray-200"><button onclick="closeModal()" class="px-6 py-2.5 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">Cancel</button><button onclick="salonTechConfirmLogin(' + techId + ', \'' + action + '\')" class="px-6 py-2.5 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Confirm ' + action + '</button></div></div>';
    openModal(content);
};
window.salonTechConfirmLogin = function(techId, action) {
    var button = document.getElementById('loginToggle-' + techId);
    var isClockIn = action === 'Clock In';
    var now = new Date();
    var formattedDateTime = formatDateTime(now);
    if (isClockIn) {
        button.textContent = 'Clock Out';
        button.classList.remove('bg-green-500', 'hover:bg-green-600');
        button.classList.add('bg-red-500', 'hover:bg-red-600');
        localStorage.setItem('technician_' + techId + '_loggedIn', 'true');
        localStorage.setItem('technician_' + techId + '_clockIn', formattedDateTime);
        localStorage.removeItem('technician_' + techId + '_clockOut');
        updateClockDisplay(techId, formattedDateTime, '--');
        updateStatusBadge(techId, true);
    } else {
        button.textContent = 'Clock In';
        button.classList.remove('bg-red-500', 'hover:bg-red-600');
        button.classList.add('bg-green-500', 'hover:bg-green-600');
        localStorage.setItem('technician_' + techId + '_loggedIn', 'false');
        localStorage.setItem('technician_' + techId + '_clockOut', formattedDateTime);
        var clockInTime = localStorage.getItem('technician_' + techId + '_clockIn') || '--';
        updateClockDisplay(techId, clockInTime, formattedDateTime);
        updateStatusBadge(techId, false);
    }
    applyFilters();
    closeModal();
    showSuccessMessage('Technician ' + action.toLowerCase() + ' successful!');
};
window.salonTechOpenAddModal = function() {
    var content = '<div class="p-6"><div class="flex items-center justify-between mb-4"><h3 class="text-xl font-bold text-gray-900">Add New Technician</h3><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><form onsubmit="salonTechSaveTechnician(event)" class="space-y-4"><div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">First Name</label><input type="text" name="first_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label><input type="text" name="last_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Email</label><input type="email" name="email" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label><input type="tel" name="phone" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div class="flex justify-end gap-3 pt-4"><button type="button" onclick="closeModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">Cancel</button><button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Save Technician</button></div></form></div>';
    openModal(content);
};
window.salonTechSaveTechnician = function(e) {
    e.preventDefault();
    showSuccessMessage('Technician added successfully!');
    closeModal();
    setTimeout(function() { location.reload(); }, 1500);
};
window.salonTechPrintTicket = function(techId) {
    var tech = allTechnicians.find(function(t) { return t.id === techId; });
    if (!tech) { showErrorMessage('Technician not found'); return; }
    var techName = tech.firstName + ' ' + tech.lastName;
    Promise.all([fetch(base + '/appointments.json'), fetch(base + '/payments.json'), fetch(base + '/customers.json')]).then(function(responses) {
        return Promise.all(responses.map(function(r) { return r.json(); }));
    }).then(function(data) {
        var aptData = data[0], payData = data[1], custData = data[2];
        var today = new Date(), todayStr = today.toISOString().split('T')[0];
        var formattedDate = today.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }).replace(/ /g, '-');
        var techAppts = aptData.appointments.filter(function(apt) {
            var isAssigned = apt.assigned_technician && apt.assigned_technician.indexOf(techId) >= 0;
            var hasService = apt.services && apt.services.some(function(s) {
                var sTechId = s.technician_id ? (typeof s.technician_id === 'string' ? parseInt(s.technician_id) : s.technician_id) : null;
                return sTechId === techId;
            });
            var aptDate = apt.appointment_datetime ? apt.appointment_datetime.split('T')[0] : apt.created_at.split('T')[0];
            return (isAssigned || hasService) && aptDate === todayStr;
        });
        var transactions = [];
        var totalAmount = 0, totalTip = 0;
        techAppts.forEach(function(apt) {
            var customer = custData.customers.find(function(c) { return c.id === apt.customer_id; });
            var payment = payData.payments.find(function(p) {
                if (p.appointmentId && p.appointmentId.toString() === apt.id.toString()) return true;
                if (customer && p.customerName) {
                    var custFullName = customer.firstName + ' ' + customer.lastName;
                    if (p.customerName === custFullName) return true;
                }
                if (p.bookingId && apt.id.toString() === p.bookingId.toString()) return true;
                return false;
            });
            if (!payment || payment.status !== 'Completed') return;
            var aptDateTime = apt.appointment_datetime || apt.created_at;
            var aptDate = new Date(aptDateTime);
            var time = aptDate.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: false });
            var date = aptDate.toLocaleDateString('en-US', { month: '2-digit', day: '2-digit', year: '2-digit' });
            var techAmount = 0;
            if (apt.services && apt.services.length > 0) {
                var techServiceCount = apt.services.filter(function(service) {
                    var sTechId = service.technician_id ? (typeof service.technician_id === 'string' ? parseInt(service.technician_id) : service.technician_id) : null;
                    return sTechId === techId;
                }).length;
                if (techServiceCount > 0) techAmount = (payment.amount / apt.services.length) * techServiceCount;
            } else {
                var assignedCount = apt.assigned_technician ? apt.assigned_technician.length : 1;
                techAmount = payment.amount / assignedCount;
            }
            var techTip = 0;
            if (payment.tip) {
                var assignedCount = apt.assigned_technician ? apt.assigned_technician.length : 1;
                techTip = payment.tip / assignedCount;
            }
            if (techAmount > 0) {
                transactions.push({ time: time, date: date, amount: techAmount, tip: techTip });
                totalAmount += techAmount;
                totalTip += techTip;
            }
        });
        if (transactions.length === 0) {
            var todayDate = new Date();
            var sampleDate = todayDate.toLocaleDateString('en-US', { month: '2-digit', day: '2-digit', year: '2-digit' });
            var samples = [{ time: '12:00', date: sampleDate, amount: 145.00, tip: 0.00 }, { time: '13:31', date: sampleDate, amount: 70.00, tip: 0.00 }, { time: '14:59', date: sampleDate, amount: 45.00, tip: 0.00 }, { time: '15:41', date: sampleDate, amount: 60.00, tip: 0.00 }, { time: '16:34', date: sampleDate, amount: 50.00, tip: 0.00 }, { time: '17:28', date: sampleDate, amount: 45.00, tip: 10.00 }, { time: '19:23', date: sampleDate, amount: 70.00, tip: 0.00 }];
            transactions = transactions.concat(samples);
            totalAmount = samples.reduce(function(sum, txn) { return sum + txn.amount; }, 0);
            totalTip = samples.reduce(function(sum, txn) { return sum + txn.tip; }, 0);
        }
        transactions.sort(function(a, b) {
            var timeA = a.time.split(':').map(Number), timeB = b.time.split(':').map(Number);
            return (timeA[0] * 60 + timeA[1]) - (timeB[0] * 60 + timeB[1]);
        });
        var txnHTML = transactions.length > 0 ? transactions.map(function(txn) {
            return '<tr class="border-b border-gray-200"><td class="px-2 py-2 text-sm text-gray-900">' + txn.time + ' | ' + txn.date + '</td><td class="px-2 py-2 text-sm text-gray-900 text-right">$' + txn.amount.toFixed(2) + ' | $' + txn.tip.toFixed(2) + '</td></tr>';
        }).join('') : '<tr><td colspan="4" class="px-4 py-8 text-center text-sm text-gray-400">No transactions found for today</td></tr>';
        var content = '<div class="p-6 mx-auto"><div class="bg-white border border-gray-300 rounded-lg p-6"><div class="text-center mb-6 border-b border-gray-300 pb-4"><h2 class="text-xl font-bold text-gray-900 mb-2">Dons Nail Spa</h2><p class="text-sm text-gray-600">258 Hedrick St, Beckley, WV 25801</p><p class="text-sm text-gray-600">Phone: 681-2077114</p><p class="text-sm text-gray-600">Merchant ID (MID): 23420</p></div><div class="text-center mb-4"><h3 class="text-lg font-semibold text-gray-900">' + techName + ' Daily Report</h3><p class="text-sm text-gray-600 mt-1">' + formattedDate + '</p></div><div class="mb-6"><div class="max-h-96 overflow-y-auto border border-gray-200 rounded"><table class="w-full border-collapse"><thead class="sticky top-0 bg-white z-10"><tr class="border-b-2 border-gray-300"><th class="px-2 py-2 text-left text-xs font-semibold text-gray-700 uppercase">' + techName + '</th><th class="px-2 py-2 text-right text-xs font-semibold text-gray-700 uppercase">Amount</th></tr></thead><tbody>' + txnHTML + '</tbody></table></div></div><div class="border-t-2 border-gray-300 pt-4 mt-4"><div class="flex justify-between items-center mb-2"><span class="text-sm font-semibold text-gray-900">Total Amount:</span><span class="text-sm font-bold text-gray-900">$' + totalAmount.toFixed(2) + '</span></div><div class="flex justify-between items-center"><span class="text-sm font-semibold text-gray-900">Total Tip:</span><span class="text-sm font-bold text-gray-900">$' + totalTip.toFixed(2) + '</span></div></div><div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200"><button onclick="window.print()" class="px-6 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium">Print</button><button onclick="closeModal()" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">Close</button></div></div></div>';
        openModal(content, 'medium');
    }).catch(function(err) {
        console.error('Error loading technician ticket:', err);
        showErrorMessage('Failed to load technician ticket');
    });
};
fetch(base + '/users.json').then(function(r) { return r.json(); }).then(function(data) {
    allTechnicians = (data.users || []).filter(function(u) { return u.role === 'technician' || u.userlevel === 'technician'; });
    allTechnicians.forEach(function(tech) {
        technicianData[tech.id] = { name: tech.firstName + ' ' + tech.lastName, email: tech.email, role: 'Technician', initials: tech.initials || (tech.firstName || '')[0] + (tech.lastName || '')[0] };
    });
    techniciansData = allTechnicians;
    var saved = localStorage.getItem('techniciansPerPage');
    if (saved) { var sel = document.getElementById('perPageSelect'); if (sel) { sel.value = saved; PAGE_SIZE = saved === 'all' ? Infinity : parseInt(saved, 10); } }
    var urlParams = new URLSearchParams(window.location.search);
    var statusParam = urlParams.get('status');
    if (statusParam === 'online') statusParam = 'active';
    else if (statusParam === 'offline') statusParam = 'inactive';
    var defaultStatus = statusParam || 'all';
    currentStatusFilter = defaultStatus;
    if (defaultStatus === 'active') currentStatusFilter = 'active';
    else if (defaultStatus === 'inactive') currentStatusFilter = 'inactive';
    salonTechFilter(defaultStatus);
    salonTechToggleView(currentView);
}).catch(function(err) { console.error(err); showErrorMessage('Failed to load technicians'); });
})();
</script>
@endpush
@endsection

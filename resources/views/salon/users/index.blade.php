@extends('layouts.salon')

@section('content')
@php
    $usersViewUrl = route('salon.users.view');
@endphp
<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Users</h1>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-1">
                    <button id="gridViewBtn" onclick="salonUsersToggleView('grid')" class="p-2 rounded-md hover:bg-white transition active:scale-95">
                        <svg class="w-5 h-5 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    </button>
                    <button id="listViewBtn" onclick="salonUsersToggleView('list')" class="p-2 rounded-md hover:bg-white transition active:scale-95">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    </button>
                </div>
                <button type="button" onclick="salonUsersOpenNewUserModal()" class="px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm sm:text-base active:scale-95">+ New User</button>
            </div>
        </div>
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-2 border-b border-gray-200 overflow-x-auto">
                <button onclick="salonUsersFilter('all')" id="tab-all" class="px-4 py-2 text-sm font-medium text-[#003047] border-b-2 border-[#003047] transition whitespace-nowrap">All</button>
                <button onclick="salonUsersFilter('Admin')" id="tab-Admin" class="px-4 py-2 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 transition whitespace-nowrap">Admin</button>
                <button onclick="salonUsersFilter('Receptionist')" id="tab-Receptionist" class="px-4 py-2 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 transition whitespace-nowrap">Receptionist</button>
                <button onclick="salonUsersFilter('Technician')" id="tab-Technician" class="px-4 py-2 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 transition whitespace-nowrap">Technician</button>
            </div>
            <div class="relative w-full sm:w-[400px]">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" id="staffSearchInput" placeholder="Search staff by name or email" oninput="salonUsersSearch(this.value)" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-base">
            </div>
        </div>
        <div id="gridView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6"></div>
        <div id="listView" class="hidden">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Login</th>
                        </tr>
                    </thead>
                    <tbody id="listViewBody" class="bg-white divide-y divide-gray-200"></tbody>
                </table>
            </div>
        </div>
        <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div id="usersResultsCounter" class="text-sm text-gray-600"></div>
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-600">Show:</label>
                <select id="perPageSelect" onchange="salonUsersChangePerPage(this.value)" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] text-sm bg-white cursor-pointer">
                    <option value="15">15</option><option value="25">25</option><option value="50">50</option><option value="100">100</option><option value="250">250</option><option value="500">500</option><option value="all">All</option>
                </select>
                <span class="text-sm text-gray-600">per page</span>
            </div>
        </div>
        <div id="usersPagination" class="mt-4 flex justify-center"></div>
    </div>
</main>
@push('scripts')
<script>
(function() {
var base = window.salonJsonBase || '{{ url("json") }}';
var viewUrl = '{{ $usersViewUrl }}';
var allUsers = [], usersData = [], currentRoleFilter = 'all', currentSearchTerm = '', PAGE_SIZE = 15, currentPage = 1, totalPages = 1, currentView = localStorage.getItem('staffView') || 'grid';

var roleColors = {
    'admin': { bg: 'bg-[#e6f0f3]', text: 'text-[#003047]', badge: 'bg-[#e6f0f3]', badgeText: 'text-[#003047]' },
    'receptionist': { bg: 'bg-purple-100', text: 'text-purple-600', badge: 'bg-blue-50', badgeText: 'text-blue-600' },
    'technician': { bg: 'bg-indigo-100', text: 'text-indigo-600', badge: 'bg-purple-50', badgeText: 'text-purple-600' }
};

function getInitials(u) { return u.initials || ((u.firstName||'')[0] + (u.lastName||'')[0]).toUpperCase(); }
function getRoleDisplayName(role) { return (role || '').charAt(0).toUpperCase() + (role || '').slice(1).toLowerCase(); }
function getRoleColors(role) { return roleColors[(role || '').toLowerCase()] || roleColors['technician']; }
function getPaginated() {
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity) return usersData;
    var start = (currentPage - 1) * PAGE_SIZE;
    return usersData.slice(start, start + PAGE_SIZE);
}
function updatePaginationState() {
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity) { totalPages = 1; currentPage = 1; return; }
    totalPages = Math.max(1, Math.ceil(usersData.length / PAGE_SIZE));
    if (currentPage > totalPages) currentPage = totalPages;
    if (currentPage < 1) currentPage = 1;
}
function renderPagination() {
    var el = document.getElementById('usersPagination');
    if (!el) return;
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity || usersData.length <= PAGE_SIZE || totalPages <= 1) { el.innerHTML = ''; return; }
    var h = '<div class="flex items-center gap-2 justify-center">';
    h += '<button class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300 ' + (currentPage === 1 ? 'text-gray-400 cursor-not-allowed opacity-50' : 'bg-white text-[#003047] hover:bg-gray-100 hover:border-[#003047]') + '" ' + (currentPage === 1 ? 'disabled' : '') + ' onclick="salonUsersGoToPage(1)">&laquo;</button>';
    h += '<button class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300 ' + (currentPage === 1 ? 'text-gray-400 cursor-not-allowed opacity-50' : 'bg-white text-[#003047] hover:bg-gray-100 hover:border-[#003047]') + '" ' + (currentPage === 1 ? 'disabled' : '') + ' onclick="salonUsersGoToPage(' + (currentPage - 1) + ')">&lt;</button>';
    var maxButtons = 6, startPage = Math.max(1, currentPage - Math.floor(maxButtons / 2)), endPage = Math.min(totalPages, startPage + maxButtons - 1);
    if (endPage - startPage < maxButtons - 1) startPage = Math.max(1, endPage - maxButtons + 1);
    for (var p = startPage; p <= endPage; p++) {
        h += '<button class="px-3 py-2 text-sm font-medium rounded-md border ' + (p === currentPage ? 'bg-[#003047] text-white' : 'bg-white text-gray-700 border-gray-300 hover:border-[#003047] hover:text-[#003047]') + '" onclick="salonUsersGoToPage(' + p + ')">' + p + '</button>';
    }
    h += '<button class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300 ' + (currentPage === totalPages ? 'text-gray-400 cursor-not-allowed opacity-50' : 'bg-white text-[#003047] hover:bg-gray-100 hover:border-[#003047]') + '" ' + (currentPage === totalPages ? 'disabled' : '') + ' onclick="salonUsersGoToPage(' + (currentPage + 1) + ')">&gt;</button>';
    h += '<button class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300 ' + (currentPage === totalPages ? 'text-gray-400 cursor-not-allowed opacity-50' : 'bg-white text-[#003047] hover:bg-gray-100 hover:border-[#003047]') + '" ' + (currentPage === totalPages ? 'disabled' : '') + ' onclick="salonUsersGoToPage(' + totalPages + ')">&raquo;</button></div>';
    el.innerHTML = h;
}
function updateCounter() {
    var el = document.getElementById('usersResultsCounter');
    if (!el) return;
    var total = usersData.length;
    if (total === 0) { el.textContent = 'No results found'; return; }
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity) { el.textContent = 'Showing all ' + total + ' result' + (total !== 1 ? 's' : ''); return; }
    var start = (currentPage - 1) * PAGE_SIZE, end = Math.min(start + PAGE_SIZE, total);
    el.textContent = 'Showing ' + (start + 1) + '-' + end + ' of ' + total + ' result' + (total !== 1 ? 's' : '');
}
function applyFilters() {
    usersData = allUsers.filter(function(u) {
        var role = u.role || u.userlevel || '';
        var roleMatch = true;
        if (currentRoleFilter !== 'all') {
            var userRoleDisplay = getRoleDisplayName(role);
            roleMatch = userRoleDisplay.toLowerCase() === currentRoleFilter.toLowerCase();
        }
        var searchMatch = true;
        if (currentSearchTerm) {
            var text = (u.firstName + ' ' + u.lastName + ' ' + (u.email || '')).toLowerCase();
            searchMatch = text.indexOf(currentSearchTerm) >= 0;
        }
        return roleMatch && searchMatch;
    });
    currentPage = 1;
    salonUsersRender();
}
function renderGrid() {
    var el = document.getElementById('gridView');
    if (!el) return;
    var list = getPaginated();
    if (list.length === 0) {
        el.innerHTML = '<div class="col-span-full text-center py-12"><svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg><p class="text-gray-500 text-sm">No users found</p></div>';
        return;
    }
    el.innerHTML = list.map(function(u) {
        var colors = getRoleColors(u.role || u.userlevel), inits = getInitials(u), name = u.firstName + ' ' + u.lastName, roleDisplay = getRoleDisplayName(u.role || u.userlevel);
        var statusText = u.active ? 'Active' : 'Inactive', statusColor = u.active ? 'text-green-600' : 'text-gray-500';
        var technicianStatus = (u.role === 'technician' || u.userlevel === 'technician') && u.status ? u.status : statusText;
        var technicianStatusColor = (u.role === 'technician' || u.userlevel === 'technician') && u.status === 'Available' ? 'text-green-600' : (u.role === 'technician' || u.userlevel === 'technician') && u.status === 'Busy' ? 'text-[#003047]' : statusColor;
        var statsHTML = (u.role === 'technician' || u.userlevel === 'technician') && u.totalEarnings !== undefined ? '<div class="grid grid-cols-2 gap-3 mb-4 pt-4 border-t border-gray-200"><div><p class="text-xs text-gray-500">Status</p><p class="text-sm font-medium ' + technicianStatusColor + '">' + technicianStatus + '</p></div><div><p class="text-xs text-gray-500">Earnings</p><p class="text-sm font-medium text-gray-900">$' + (u.totalEarnings || 0).toFixed(2) + '</p></div></div>' : '<div class="grid grid-cols-2 gap-3 mb-4 pt-4 border-t border-gray-200"><div><p class="text-xs text-gray-500">Status</p><p class="text-sm font-medium ' + statusColor + '">' + statusText + '</p></div><div><p class="text-xs text-gray-500">Last Login</p><p class="text-sm font-medium text-gray-900">Today</p></div></div>';
        return '<div data-role="' + roleDisplay + '" onclick="window.location.href=\'' + viewUrl + '?id=' + u.id + '\'" class="staff-card bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow cursor-pointer active:scale-95"><div class="flex items-center gap-4 mb-4"><div class="w-16 h-16 ' + colors.bg + ' rounded-full flex items-center justify-center flex-shrink-0"><span class="text-2xl font-bold ' + colors.text + '">' + inits + '</span></div><div class="flex-1 min-w-0"><h3 class="font-semibold text-gray-900 text-lg truncate">' + name + '</h3><p class="text-sm text-gray-500 truncate">' + (u.email || '') + '</p><p class="text-xs ' + colors.text + ' font-medium mt-1">' + roleDisplay + '</p></div></div>' + statsHTML + '</div>';
    }).join('');
}
function renderList() {
    var tbody = document.getElementById('listViewBody');
    if (!tbody) return;
    var list = getPaginated();
    if (list.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-12 text-center"><svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg><p class="text-gray-500 text-sm">No users found</p></td></tr>';
        return;
    }
    tbody.innerHTML = list.map(function(u) {
        var colors = getRoleColors(u.role || u.userlevel), inits = getInitials(u), name = u.firstName + ' ' + u.lastName, roleDisplay = getRoleDisplayName(u.role || u.userlevel);
        var statusText = u.active ? 'Active' : 'Inactive', statusColor = u.active ? 'text-green-600' : 'text-gray-500';
        var technicianStatus = (u.role === 'technician' || u.userlevel === 'technician') && u.status ? u.status : statusText;
        var technicianStatusColor = (u.role === 'technician' || u.userlevel === 'technician') && u.status === 'Available' ? 'text-green-600' : (u.role === 'technician' || u.userlevel === 'technician') && u.status === 'Busy' ? 'text-[#003047]' : statusColor;
        return '<tr data-role="' + roleDisplay + '" onclick="window.location.href=\'' + viewUrl + '?id=' + u.id + '\'" class="staff-row hover:bg-gray-50 cursor-pointer transition"><td class="px-6 py-4 whitespace-nowrap"><div class="flex items-center"><div class="w-10 h-10 ' + colors.bg + ' rounded-full flex items-center justify-center flex-shrink-0"><span class="text-sm font-bold ' + colors.text + '">' + inits + '</span></div><div class="ml-4"><div class="text-sm font-medium text-gray-900">' + name + '</div><div class="text-sm text-gray-500">' + (u.email || '') + '</div></div></div></td><td class="px-6 py-4 whitespace-nowrap"><span class="text-xs ' + colors.badgeText + ' font-medium px-2 py-1 ' + colors.badge + ' rounded">' + roleDisplay + '</span></td><td class="px-6 py-4 whitespace-nowrap"><span class="text-sm font-medium ' + technicianStatusColor + '">' + technicianStatus + '</span></td><td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Today</td></tr>';
    }).join('');
}
function salonUsersRender() {
    updatePaginationState();
    renderGrid();
    renderList();
    renderPagination();
    updateCounter();
}
window.salonUsersGoToPage = function(p) { if (p < 1 || p > totalPages || p === currentPage) return; currentPage = p; salonUsersRender(); };
window.salonUsersFilter = function(role, updateURL) {
    currentRoleFilter = role;
    if (updateURL !== false) {
        var url = new URL(window.location);
        if (role === 'all') url.searchParams.delete('role');
        else url.searchParams.set('role', role.toLowerCase());
        window.history.pushState({}, '', url);
    }
    document.querySelectorAll('[id^="tab-"]').forEach(function(btn) {
        var id = btn.id.replace('tab-', '');
        var active = (role === 'all' && id === 'all') || (role === id);
        btn.className = 'px-4 py-2 text-sm font-medium whitespace-nowrap border-b-2 transition ' + (active ? 'text-[#003047] border-[#003047]' : 'text-gray-500 border-transparent hover:text-gray-700');
    });
    applyFilters();
};
window.salonUsersSearch = function(val) { currentSearchTerm = (val || '').toLowerCase(); applyFilters(); };
window.salonUsersChangePerPage = function(val) { PAGE_SIZE = val === 'all' ? Infinity : parseInt(val, 10); currentPage = 1; localStorage.setItem('usersPerPage', val); salonUsersRender(); };
window.salonUsersToggleView = function(view) {
    currentView = view;
    localStorage.setItem('staffView', view);
    var g = document.getElementById('gridView'), l = document.getElementById('listView'), gb = document.getElementById('gridViewBtn'), lb = document.getElementById('listViewBtn');
    if (view === 'grid') {
        g.classList.remove('hidden'); l.classList.add('hidden');
        if (gb && gb.querySelector('svg')) { gb.querySelector('svg').classList.remove('text-gray-500'); gb.querySelector('svg').classList.add('text-gray-900'); }
        if (lb && lb.querySelector('svg')) { lb.querySelector('svg').classList.remove('text-gray-900'); lb.querySelector('svg').classList.add('text-gray-500'); }
        renderGrid();
    } else {
        g.classList.add('hidden'); l.classList.remove('hidden');
        if (lb && lb.querySelector('svg')) { lb.querySelector('svg').classList.remove('text-gray-500'); lb.querySelector('svg').classList.add('text-gray-900'); }
        if (gb && gb.querySelector('svg')) { gb.querySelector('svg').classList.remove('text-gray-900'); gb.querySelector('svg').classList.add('text-gray-500'); }
        renderList();
    }
};
function initializeRoleFilter() {
    var urlParams = new URLSearchParams(window.location.search);
    var role = urlParams.get('role');
    if (role) {
        var roleCapitalized = role.toLowerCase() === 'admin' ? 'Admin' : role.toLowerCase() === 'receptionist' ? 'Receptionist' : role.toLowerCase() === 'technician' ? 'Technician' : role.charAt(0).toUpperCase() + role.slice(1).toLowerCase();
        currentRoleFilter = roleCapitalized;
        document.querySelectorAll('[id^="tab-"]').forEach(function(tab) {
            tab.classList.remove('text-[#003047]', 'border-[#003047]', 'border-b-2');
            tab.classList.add('text-gray-500', 'border-transparent');
        });
        var activeTab = document.getElementById('tab-' + roleCapitalized);
        if (activeTab) {
            activeTab.classList.remove('text-gray-500', 'border-transparent');
            activeTab.classList.add('text-[#003047]', 'border-[#003047]', 'border-b-2');
        }
    }
    applyFilters();
}
window.salonUsersOpenNewUserModal = function() {
    var content = '<div class="p-6"><div class="flex items-center justify-between mb-4"><h3 class="text-xl font-bold text-gray-900">New User</h3><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><form onsubmit="salonUsersSaveUser(event)" class="space-y-4"><div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">First Name</label><input type="text" name="first_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label><input type="text" name="last_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div></div><div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">Email</label><input type="email" name="email" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label><input type="tel" name="phone" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Role</label><select name="role" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"><option value="">Select Role</option><option value="Admin">Admin</option><option value="Receptionist">Receptionist</option><option value="Technician">Technician</option></select></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Password</label><input type="password" name="password" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg"><div><label class="text-sm font-medium text-gray-900">Active</label></div><label class="relative inline-flex items-center cursor-pointer"><input type="checkbox" name="active" class="sr-only peer" checked><div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[\'\'] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div></label></div><div class="flex justify-end gap-3 pt-4"><button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Save User</button></div></form></div>';
    openModal(content);
};
window.salonUsersSaveUser = function(e) {
    e.preventDefault();
    showSuccessMessage('User added successfully!');
    closeModal();
    setTimeout(function() { location.reload(); }, 1500);
};
document.addEventListener('DOMContentLoaded', function() {
    var saved = localStorage.getItem('usersPerPage');
    if (saved) {
        var sel = document.getElementById('perPageSelect');
        if (sel) { sel.value = saved; PAGE_SIZE = saved === 'all' ? Infinity : parseInt(saved, 10); }
    }
    fetch(base + '/users.json').then(function(r) { return r.json(); }).then(function(data) {
        allUsers = data.users || [];
        initializeRoleFilter();
        salonUsersToggleView(currentView);
    }).catch(function(err) { console.error(err); showErrorMessage('Failed to load users'); });
});
window.addEventListener('popstate', function() {
    var urlParams = new URLSearchParams(window.location.search);
    var role = urlParams.get('role');
    if (role) {
        var roleCapitalized = role.toLowerCase() === 'admin' ? 'Admin' : role.toLowerCase() === 'receptionist' ? 'Receptionist' : role.toLowerCase() === 'technician' ? 'Technician' : role.charAt(0).toUpperCase() + role.slice(1).toLowerCase();
        salonUsersFilter(roleCapitalized, false);
    } else {
        salonUsersFilter('all', false);
    }
});
})();
</script>
@endpush
@endsection

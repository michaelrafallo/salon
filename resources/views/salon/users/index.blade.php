@extends('layouts.salon')

@section('content')
@php
    $usersViewUrl = route('salon.users.view');
    $apiUsersUrl = url('api/salon/users');
    $dashboardUrl = route('salon.dashboard');
    $usersTab = request()->query('role', 'all');
    if (! in_array($usersTab, ['all', 'admin', 'receptionist', 'technician'], true)) {
        $usersTab = 'all';
    }
    $usersTabLabel = $usersTab === 'all' ? 'all' : ucfirst($usersTab);
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
                <button onclick="salonUsersFilter('all')" id="tab-all" class="px-4 py-2 text-sm font-medium transition whitespace-nowrap {{ $usersTabLabel === 'all' ? 'text-[#003047] border-b-2 border-[#003047]' : 'text-gray-500 border-b-2 border-transparent hover:text-gray-700' }}">All</button>
                <button onclick="salonUsersFilter('Admin')" id="tab-Admin" class="px-4 py-2 text-sm font-medium transition whitespace-nowrap {{ $usersTabLabel === 'Admin' ? 'text-[#003047] border-b-2 border-[#003047]' : 'text-gray-500 border-b-2 border-transparent hover:text-gray-700' }}">Admin</button>
                <button onclick="salonUsersFilter('Receptionist')" id="tab-Receptionist" class="px-4 py-2 text-sm font-medium transition whitespace-nowrap {{ $usersTabLabel === 'Receptionist' ? 'text-[#003047] border-b-2 border-[#003047]' : 'text-gray-500 border-b-2 border-transparent hover:text-gray-700' }}">Receptionist</button>
                <button onclick="salonUsersFilter('Technician')" id="tab-Technician" class="px-4 py-2 text-sm font-medium transition whitespace-nowrap {{ $usersTabLabel === 'Technician' ? 'text-[#003047] border-b-2 border-[#003047]' : 'text-gray-500 border-b-2 border-transparent hover:text-gray-700' }}">Technician</button>
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
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
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
var base = window.salonJsonBase || '{{ url("api/salon/data") }}';
window.salonUsersBootstrap = window.salonUsersBootstrap || @json($usersBootstrap ?? null);
var viewUrl = '{{ $usersViewUrl }}';
var apiUsersUrl = '{{ $apiUsersUrl }}';
var dashboardUrl = '{{ $dashboardUrl }}';
var allUsers = [], usersData = [], currentRoleFilter = @json($usersTabLabel), currentSearchTerm = '', PAGE_SIZE = 15, currentPage = 1, totalPages = 1, currentView = localStorage.getItem('staffView') || 'grid';

var roleColors = {
    'admin': { bg: 'bg-[#e6f0f3]', text: 'text-[#003047]', badge: 'bg-[#e6f0f3]', badgeText: 'text-[#003047]' },
    'receptionist': { bg: 'bg-purple-100', text: 'text-purple-600', badge: 'bg-blue-50', badgeText: 'text-blue-600' },
    'technician': { bg: 'bg-indigo-100', text: 'text-indigo-600', badge: 'bg-purple-50', badgeText: 'text-purple-600' }
};

function getInitials(u) { return u.initials || ((u.firstName||'')[0] + (u.lastName||'')[0]).toUpperCase(); }
function getRoleDisplayName(role) { return (role || '').charAt(0).toUpperCase() + (role || '').slice(1).toLowerCase(); }
function getRoleColors(role) { return roleColors[(role || '').toLowerCase()] || roleColors['technician']; }
function getUserAvatar(u, size, textSize) {
    var colors = getRoleColors(u.role || u.userlevel), inits = getInitials(u);
    if (u.profilePhotoUrl) {
        var safeUrl = u.profilePhotoUrl.replace(/"/g, '&quot;').replace(/'/g, "\\'");
        return '<div class="' + size + ' rounded-full overflow-hidden flex-shrink-0 cursor-pointer" onclick="event.stopPropagation();previewUserPhoto(\'' + safeUrl + '\')"><img src="' + u.profilePhotoUrl.replace(/"/g, '&quot;') + '" alt="" class="w-full h-full object-cover" onerror="this.style.display=\'none\';this.parentNode.innerHTML=\'<div class=&quot;' + size + ' ' + colors.bg + ' rounded-full flex items-center justify-center flex-shrink-0&quot;><span class=&quot;' + textSize + ' font-bold ' + colors.text + '&quot;>' + inits + '</span></div>\';this.parentNode.onclick=null;this.parentNode.style.cursor=\'default\'"></div>';
    }
    return '<div class="' + size + ' ' + colors.bg + ' rounded-full flex items-center justify-center flex-shrink-0"><span class="' + textSize + ' font-bold ' + colors.text + '">' + inits + '</span></div>';
}
function getLastLogin(u) {
    if (!u || typeof u !== 'object') return '—';
    // If a last login field is added later, prefer it.
    return u.lastLogin || u.last_login || u.lastLoginAt || u.last_login_at || '—';
}
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
function normalizeUsers(list) {
    return (list || []).map(function(u) {
        if (!u || typeof u !== 'object') return u;
        if (typeof u.active === 'undefined') {
            var status = (u.status || 'active').toString().toLowerCase();
            u.active = status === 'active';
        }
        return u;
    });
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
        var lastLogin = getLastLogin(u);
        var statsHTML = '<div class="grid grid-cols-2 gap-3 pt-4 border-t border-gray-200"><div><p class="text-xs text-gray-500">Status</p><p class="text-sm font-medium ' + ((u.role === 'technician' || u.userlevel === 'technician') ? technicianStatusColor : statusColor) + '">' + ((u.role === 'technician' || u.userlevel === 'technician') ? technicianStatus : statusText) + '</p></div><div><p class="text-xs text-gray-500">Last Login</p><p class="text-sm font-medium text-gray-900">' + lastLogin + '</p></div></div>';
        var safeName = (name || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");
        return '<div data-role="' + roleDisplay + '" class="staff-card bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow relative flex flex-col"><div onclick="window.location.href=\'' + viewUrl + '?id=' + u.id + '\'" class="cursor-pointer flex-1"><div class="flex items-center gap-4 mb-4">' + getUserAvatar(u, 'w-16 h-16', 'text-2xl') + '<div class="flex-1 min-w-0"><h3 class="font-semibold text-gray-900 text-lg truncate">' + name + '</h3><p class="text-sm text-gray-500 truncate">' + (u.email || '') + '</p><p class="text-xs ' + colors.text + ' font-medium mt-1">' + roleDisplay + '</p></div></div>' + statsHTML + '</div><div class="flex flex-wrap gap-2 pt-4 mt-4 border-t border-gray-100" onclick="event.stopPropagation()"><button type="button" onclick="salonUsersLoginAs(' + u.id + ', \'' + safeName + '\')" class="inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium text-sm active:scale-95"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg></button><button type="button" onclick="salonUsersOpenEditModal(' + u.id + ')" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm active:scale-95"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>Quick Edit</button><button type="button" onclick="salonUsersDelete(' + u.id + ', \'' + safeName + '\')" class="inline-flex items-center justify-center px-4 py-2 text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition font-medium text-sm active:scale-95"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button></div></div>';
    }).join('');
}
function renderList() {
    var tbody = document.getElementById('listViewBody');
    if (!tbody) return;
    var list = getPaginated();
    if (list.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-12 text-center"><svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg><p class="text-gray-500 text-sm">No users found</p></td></tr>';
        return;
    }
    tbody.innerHTML = list.map(function(u) {
        var colors = getRoleColors(u.role || u.userlevel), inits = getInitials(u), name = u.firstName + ' ' + u.lastName, roleDisplay = getRoleDisplayName(u.role || u.userlevel);
        var statusText = u.active ? 'Active' : 'Inactive', statusColor = u.active ? 'text-green-600' : 'text-gray-500';
        var technicianStatus = (u.role === 'technician' || u.userlevel === 'technician') && u.status ? u.status : statusText;
        var technicianStatusColor = (u.role === 'technician' || u.userlevel === 'technician') && u.status === 'Available' ? 'text-green-600' : (u.role === 'technician' || u.userlevel === 'technician') && u.status === 'Busy' ? 'text-[#003047]' : statusColor;
        var safeName = (name || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");
        return '<tr data-role="' + roleDisplay + '" class="staff-row hover:bg-gray-50 transition"><td onclick="window.location.href=\'' + viewUrl + '?id=' + u.id + '\'" class="px-6 py-4 whitespace-nowrap cursor-pointer"><div class="flex items-center">' + getUserAvatar(u, 'w-10 h-10', 'text-sm') + '<div class="ml-4"><div class="text-sm font-medium text-gray-900">' + name + '</div><div class="text-sm text-gray-500">' + (u.email || '') + '</div></div></div></td><td class="px-6 py-4 whitespace-nowrap"><span class="text-xs ' + colors.badgeText + ' font-medium px-2 py-1 ' + colors.badge + ' rounded">' + roleDisplay + '</span></td><td class="px-6 py-4 whitespace-nowrap"><span class="text-sm font-medium ' + technicianStatusColor + '">' + technicianStatus + '</span></td><td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">' + getLastLogin(u) + '</td><td class="px-6 py-4 whitespace-nowrap text-sm text-right"><div class="flex items-center justify-end gap-2"><button type="button" onclick="event.stopPropagation(); salonUsersLoginAs(' + u.id + ', \'' + safeName + '\')" class="inline-flex items-center justify-center w-8 h-8 cursor-pointer bg-green-600 text-white rounded-lg hover:bg-green-700 transition active:scale-95" title="Login as"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg></button><button type="button" onclick="event.stopPropagation(); salonUsersOpenEditModal(' + u.id + ')" class="inline-flex items-center justify-center w-8 h-8 cursor-pointer bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition active:scale-95" title="Quick Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></button><button type="button" onclick="event.stopPropagation(); salonUsersDelete(' + u.id + ', \'' + safeName + '\')" class="inline-flex items-center justify-center w-8 h-8 cursor-pointer text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition active:scale-95" title="Delete"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button></div></td></tr>';
    }).join('');
}

window.salonUsersLoginAs = function(id, name) {
    var label = (name || 'this user') + '';
    if (typeof openConfirmModal === 'function') {
        openConfirmModal({
            title: 'Login as user',
            message: 'You are about to login as ' + boldName(label) + '. Do you want to continue?',
            confirmLabel: 'Login as',
            onConfirm: function() { salonUsersDoLoginAs(id, label); }
        });
        return;
    }
    if (confirm('Login as ' + label + '?')) {
        salonUsersDoLoginAs(id, label);
    }
};

function salonUsersDoLoginAs(id, name) {
    if (!id) return;
    if (typeof salonApi === 'undefined' || !salonApi.post) {
        showErrorMessage('Unable to login as right now.');
        return;
    }
    salonApi.post(apiUsersUrl + '/' + id + '/login-as', {}).then(function(res) {
        showSuccessMessage(res.message || ('Logged in as ' + name + '.'));
        setTimeout(function() { window.location.href = dashboardUrl; }, 300);
    }).catch(function(err) {
        showErrorMessage((err && err.message) ? err.message : 'Failed to login as user.');
    });
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
window.salonUsersToggleView = function(view) {
    currentView = view;
    localStorage.setItem('staffView', view);
    setViewUI(view);
    if (view === 'grid') {
        renderGrid();
        return;
    }
    renderList();
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
    var photoBlock = '<div class="w-1/2"><label class="block text-sm font-medium text-gray-700 mb-2">Profile Photo</label><input type="file" name="profile_photo" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-sm"><p class="mt-1 text-xs text-gray-500">Optional. Max 2 MB. JPG, PNG, GIF, WebP.</p></div>';
    var activeToggle = '<div class="w-1/2"><label class="block text-sm font-medium text-gray-700 mb-2">Active</label><label class="relative inline-flex items-center cursor-pointer"><input type="checkbox" name="active" class="sr-only peer" checked><div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[\'\'] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div></label></div>';
    var content = '<div class="p-6"><div class="flex items-center justify-between mb-4"><h3 class="text-xl font-bold text-gray-900">New User</h3><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><form onsubmit="salonUsersSaveUser(event)" class="space-y-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">Username</label><input type="text" name="username" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="e.g. jane.doe"></div><div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">First Name</label><input type="text" name="first_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label><input type="text" name="last_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div></div><div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">Email</label><input type="email" name="email" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Phone</label><input type="tel" name="phone" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Role</label><select name="role" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"><option value="">Select Role</option><option value="superadmin">Super Admin</option><option value="admin">Admin</option><option value="receptionist">Receptionist</option><option value="technician">Technician</option></select></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Password</label><input type="password" name="password" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div class="flex gap-4 items-start">' + photoBlock + activeToggle + '</div><div class="flex justify-end gap-3 pt-4"><button type="button" onclick="closeModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button><button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium">Save User</button></div></form></div>';
    openModal(content);
};
window.salonUsersSaveUser = function(e) {
    e.preventDefault();
    var form = e.target;
    var fd = new FormData();
    fd.append('username', form.username.value.trim());
    fd.append('first_name', form.first_name.value.trim());
    fd.append('last_name', form.last_name.value.trim());
    fd.append('email', form.email.value.trim());
    fd.append('password', form.password.value);
    fd.append('phone', form.phone.value.trim() || '');
    fd.append('role', form.role.value);
    var activeEl = form.querySelector('input[name="active"]');
    fd.append('status', activeEl && activeEl.checked ? 'active' : 'inactive');
    var fileEl = form.querySelector('input[name="profile_photo"]');
    if (fileEl && fileEl.files && fileEl.files[0]) fd.append('profile_photo', fileEl.files[0]);
    var btn = form.querySelector('button[type="submit"]');
    if (btn) { btn.disabled = true; btn.textContent = 'Saving...'; }
    salonApi.postFormData(apiUsersUrl, fd).then(function(res) {
        showSuccessMessage(res.message || 'User added successfully!');
        closeModal();
        allUsers.unshift(res.data);
        applyFilters();
        salonUsersRender();
    }).catch(function(err) {
        showErrorMessage(err.message || 'Failed to save user.');
        if (btn) { btn.disabled = false; btn.textContent = 'Save User'; }
    });
};
window.salonUsersOpenEditModal = function(id) {
    var u = allUsers.find(function(x) { return x.id === id; });
    if (!u) return;
    var roleVal = (u.role || u.userlevel || 'technician').toLowerCase();
    var imgUrl = u.profilePhotoUrl || null;
    var isActive = u.active !== false;
    var photoBlock = '<div class="w-1/2"><label class="block text-sm font-medium text-gray-700 mb-2">Profile Photo</label>' +
        (imgUrl ? '<div id="currentPhotoPreview" class="mb-3 flex items-start gap-3"><img src="' + imgUrl.replace(/"/g, '&quot;') + '" alt="" class="h-20 w-20 object-cover rounded-lg border border-gray-200 flex-shrink-0 cursor-pointer hover:opacity-80 transition" onclick="previewUserPhoto(\'' + imgUrl.replace(/'/g, "\\'") + '\')"><button type="button" onclick="removeUserPhoto()" class="px-3 py-1.5 text-sm text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition font-medium active:scale-95">Remove Photo</button></div>' : '') +
        '<input type="hidden" id="removePhotoFlag" name="remove_photo" value="0">' +
        '<input type="file" name="profile_photo" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-sm"><p class="mt-1 text-xs text-gray-500">Optional. Max 2 MB. Leave empty to keep current.</p></div>';
    var activeToggle = '<div class="w-1/2"><label class="block text-sm font-medium text-gray-700 mb-2">Active</label><label class="relative inline-flex items-center cursor-pointer"><input type="checkbox" name="active" class="sr-only peer" ' + (isActive ? 'checked' : '') + '><div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[\'\'] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div></label></div>';
    var content = '<div class="p-6"><div class="flex items-center justify-between mb-4"><h3 class="text-xl font-bold text-gray-900">Edit User</h3><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><form onsubmit="salonUsersUpdateUser(event, ' + u.id + ')" class="space-y-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">Username</label><input type="text" name="username" value="' + (u.username || '').replace(/"/g, '&quot;') + '" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">First Name</label><input type="text" name="first_name" value="' + (u.firstName || '').replace(/"/g, '&quot;') + '" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label><input type="text" name="last_name" value="' + (u.lastName || '').replace(/"/g, '&quot;') + '" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div></div><div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">Email</label><input type="email" name="email" value="' + (u.email || '').replace(/"/g, '&quot;') + '" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Phone</label><input type="tel" name="phone" value="' + (u.phone || '').replace(/"/g, '&quot;') + '" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Role</label><select name="role" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"><option value="superadmin"' + (roleVal === 'superadmin' ? ' selected' : '') + '>Super Admin</option><option value="admin"' + (roleVal === 'admin' ? ' selected' : '') + '>Admin</option><option value="receptionist"' + (roleVal === 'receptionist' ? ' selected' : '') + '>Receptionist</option><option value="technician"' + (roleVal === 'technician' ? ' selected' : '') + '>Technician</option></select></div><div><label class="block text-sm font-medium text-gray-700 mb-2">New Password (leave blank to keep)</label><input type="password" name="password" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Leave blank to keep current"></div><div class="flex gap-4 items-start">' + photoBlock + activeToggle + '</div><div class="flex justify-end gap-3 pt-4"><button type="button" onclick="closeModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button><button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium">Update User</button></div></form></div>';
    openModal(content);
};
window.salonUsersUpdateUser = function(e, id) {
    e.preventDefault();
    var form = e.target;
    var fd = new FormData();
    fd.append('_method', 'PUT');
    fd.append('username', form.username.value.trim());
    fd.append('first_name', form.first_name.value.trim());
    fd.append('last_name', form.last_name.value.trim());
    fd.append('email', form.email.value.trim());
    fd.append('phone', form.phone.value.trim() || '');
    fd.append('role', form.role.value);
    var activeEl = form.querySelector('input[name="active"]');
    fd.append('status', activeEl && activeEl.checked ? 'active' : 'inactive');
    if (form.password.value) fd.append('password', form.password.value);
    var fileEl = form.querySelector('input[name="profile_photo"]');
    if (fileEl && fileEl.files && fileEl.files[0]) fd.append('profile_photo', fileEl.files[0]);
    var removeFlag = form.querySelector('input[name="remove_photo"]');
    if (removeFlag && removeFlag.value === '1') fd.append('remove_photo', '1');
    var btn = form.querySelector('button[type="submit"]');
    if (btn) { btn.disabled = true; btn.textContent = 'Updating...'; }
    salonApi.postFormData(apiUsersUrl + '/' + id, fd).then(function(res) {
        showSuccessMessage(res.message || 'User updated.');
        closeModal();
        var idx = allUsers.findIndex(function(x) { return x.id === parseInt(id, 10); });
        if (idx >= 0) allUsers[idx] = res.data;
        applyFilters();
        salonUsersRender();
    }).catch(function(err) {
        showErrorMessage(err.message || 'Failed to update user.');
        if (btn) { btn.disabled = false; btn.textContent = 'Update User'; }
    });
};
window.previewUserPhoto = function(url) {
    var overlay = document.createElement('div');
    overlay.style.cssText = 'position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.85);display:flex;align-items:center;justify-content:center;cursor:pointer;';
    overlay.onclick = function() { overlay.remove(); };
    overlay.innerHTML = '<button onclick="event.stopPropagation();this.parentElement.remove();" style="position:absolute;top:1rem;right:1rem;background:rgba(255,255,255,0.15);border:none;color:#fff;border-radius:9999px;width:2.5rem;height:2.5rem;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:background 0.2s;" onmouseenter="this.style.background=\'rgba(255,255,255,0.3)\'" onmouseleave="this.style.background=\'rgba(255,255,255,0.15)\'"><svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button><img src="' + url + '" alt="Profile Photo" style="max-height:90vh;max-width:90vw;object-fit:contain;border-radius:0.5rem;" onclick="event.stopPropagation();">';
    document.body.appendChild(overlay);
};
window.removeUserPhoto = function() {
    var preview = document.getElementById('currentPhotoPreview');
    var flag = document.getElementById('removePhotoFlag');
    if (preview) preview.style.display = 'none';
    if (flag) flag.value = '1';
};
window.salonUsersDelete = function(id, name) {
    openConfirmModal({
        title: 'Delete user',
        message: 'You are about to permanently remove ' + (name ? boldName(name) : 'this user') + '. Do you want to continue?',
        confirmLabel: 'Delete',
        onConfirm: function() {
            salonApi.delete(apiUsersUrl + '/' + id).then(function() {
                showSuccessMessage('User deleted.');
                allUsers = allUsers.filter(function(u) { return u.id !== id && u.id !== parseInt(id, 10); });
                applyFilters();
                salonUsersRender();
            }).catch(function(err) {
                showErrorMessage(err.message || 'Failed to delete user.');
            });
        }
    });
};
document.addEventListener('DOMContentLoaded', function() {
    var saved = localStorage.getItem('usersPerPage');
    if (saved) {
        var sel = document.getElementById('perPageSelect');
        if (sel) { sel.value = saved; PAGE_SIZE = saved === 'all' ? Infinity : parseInt(saved, 10); }
    }
    setViewUI(currentView);
    if (window.salonUsersBootstrap && window.salonUsersBootstrap.users) {
        allUsers = normalizeUsers(window.salonUsersBootstrap.users || []);
        initializeRoleFilter();
        salonUsersToggleView(currentView);
    } else {
        fetch(base + '/users').then(function(r) { return r.json(); }).then(function(data) {
            allUsers = normalizeUsers(data.users || []);
            initializeRoleFilter();
            salonUsersToggleView(currentView);
        }).catch(function(err) { console.error(err); showErrorMessage('Failed to load users'); });
    }
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

<?php
$pageTitle = 'Users';
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/modal.php';
?>

<!-- Main Content Area -->
<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Users</h1>
            <div class="flex items-center gap-3">
                <!-- View Toggle -->
                <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-1">
                    <button id="gridViewBtn" onclick="toggleView('grid')" class="p-2 rounded-md hover:bg-white transition active:scale-95">
                        <svg class="w-5 h-5 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                        </svg>
                    </button>
                    <button id="listViewBtn" onclick="toggleView('list')" class="p-2 rounded-md hover:bg-white transition active:scale-95">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
                <button onclick="openNewUserModal()" class="px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm sm:text-base active:scale-95">
                    + New User
                </button>
            </div>
        </div>

        <!-- Role Filter Tabs and Search Bar -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <!-- Role Filter Tabs -->
            <div class="flex items-center gap-2 border-b border-gray-200 overflow-x-auto">
                <button onclick="filterByRole('all')" id="tab-all" class="role-tab px-4 py-2 text-sm font-medium text-[#003047] border-b-2 border-[#003047] transition whitespace-nowrap">
                    All
                </button>
                <button onclick="filterByRole('Admin')" id="tab-Admin" class="role-tab px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300 transition whitespace-nowrap">
                    Admin
                </button>
                <button onclick="filterByRole('Receptionist')" id="tab-Receptionist" class="role-tab px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300 transition whitespace-nowrap">
                    Receptionist
                </button>
                <button onclick="filterByRole('Technician')" id="tab-Technician" class="role-tab px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300 transition whitespace-nowrap">
                    Technician
                </button>
            </div>
            
            <!-- Search Bar -->
            <div class="relative w-full sm:w-[400px]">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" id="staffSearchInput" placeholder="Search staff by name or email" oninput="filterStaff(this.value)" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-base">
            </div>
        </div>

        <!-- Staff Grid View - 4 columns -->
        <div id="gridView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- Staff cards will be dynamically loaded here -->
        </div>

        <!-- Staff List View -->
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
                    <tbody id="listViewBody" class="bg-white divide-y divide-gray-200">
                        <!-- Staff rows will be dynamically loaded here -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Results Counter and Per Page Selector -->
        <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div id="usersResultsCounter" class="text-sm text-gray-600">
                <!-- Results counter will be populated by JavaScript -->
            </div>
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-600">Show:</label>
                <select id="perPageSelect" onchange="changePerPage(this.value)" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-sm bg-white cursor-pointer">
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="250">250</option>
                    <option value="500">500</option>
                    <option value="all">All</option>
                </select>
                <span class="text-sm text-gray-600">per page</span>
            </div>
        </div>

        <!-- Pagination -->
        <div id="usersPagination" class="mt-4 flex justify-center"></div>
    </div>
</main>

<script>
// Global variables
let allUsers = [];
let usersData = []; // Filtered users for display
let currentView = localStorage.getItem('staffView') || 'grid';
let PAGE_SIZE = 15; // Can be changed by user
let currentPage = 1;
let totalPages = 1;
let currentRoleFilter = 'all';
let currentSearchTerm = '';

// Color schemes for different roles
const roleColors = {
    'admin': { bg: 'bg-[#e6f0f3]', text: 'text-[#003047]', badge: 'bg-[#e6f0f3]', badgeText: 'text-[#003047]' },
    'receptionist': { bg: 'bg-purple-100', text: 'text-purple-600', badge: 'bg-blue-50', badgeText: 'text-blue-600' },
    'technician': { bg: 'bg-indigo-100', text: 'text-indigo-600', badge: 'bg-purple-50', badgeText: 'text-purple-600' }
};

// Fetch users from JSON
async function fetchUsers() {
    try {
        const response = await fetch('../json/users.json');
        const data = await response.json();
        allUsers = data.users;
        usersData = allUsers; // Initially show all users
        // Don't render here - initializeRoleFilter will call applyFilters which will render
    } catch (error) {
        console.error('Error fetching users:', error);
        showErrorMessage('Failed to load users data');
    }
}

// Get initials from name
function getInitials(user) {
    if (user.initials) return user.initials;
    const first = user.firstName ? user.firstName[0] : '';
    const last = user.lastName ? user.lastName[0] : '';
    return (first + last).toUpperCase();
}

// Get role display name
function getRoleDisplayName(role) {
    return role.charAt(0).toUpperCase() + role.slice(1);
}

// Get color scheme for role
function getRoleColors(role) {
    return roleColors[role.toLowerCase()] || roleColors['technician'];
}

// Render staff cards and rows
function renderStaff() {
    updatePaginationState();
    renderGridView();
    renderListView();
    renderPagination();
    updateResultsCounter();
}

// Render grid view
function renderGridView() {
    const gridView = document.getElementById('gridView');
    if (!gridView) return;
    
    const paginatedUsers = getPaginatedUsers();
    
    if (paginatedUsers.length === 0) {
        gridView.innerHTML = `
            <div class="col-span-full text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <p class="text-gray-500 text-sm">No users found</p>
            </div>
        `;
        return;
    }
    
    gridView.innerHTML = paginatedUsers.map(user => {
        const colors = getRoleColors(user.role);
        const initials = getInitials(user);
        const fullName = `${user.firstName} ${user.lastName}`;
        const roleDisplay = getRoleDisplayName(user.role);
        const statusText = user.active ? 'Active' : 'Inactive';
        const statusColor = user.active ? 'text-green-600' : 'text-gray-500';
        
        // For technicians, show status if available
        const technicianStatus = user.role === 'technician' && user.status ? user.status : statusText;
        const technicianStatusColor = user.role === 'technician' && user.status === 'Available' ? 'text-green-600' : 
                                     user.role === 'technician' && user.status === 'Busy' ? 'text-[#003047]' : statusColor;
        
        // Show earnings for technicians
        const statsHTML = user.role === 'technician' && user.totalEarnings !== undefined ? `
            <div class="grid grid-cols-2 gap-3 mb-4 pt-4 border-t border-gray-200">
                <div>
                    <p class="text-xs text-gray-500">Status</p>
                    <p class="text-sm font-medium ${technicianStatusColor}">${technicianStatus}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Earnings</p>
                    <p class="text-sm font-medium text-gray-900">$${user.totalEarnings.toFixed(2)}</p>
                </div>
            </div>
        ` : `
            <div class="grid grid-cols-2 gap-3 mb-4 pt-4 border-t border-gray-200">
                <div>
                    <p class="text-xs text-gray-500">Status</p>
                    <p class="text-sm font-medium ${statusColor}">${statusText}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Last Login</p>
                    <p class="text-sm font-medium text-gray-900">Today</p>
                </div>
            </div>
        `;
        
        return `
            <div data-role="${roleDisplay}" onclick="window.location.href='view.php?id=${user.id}'" class="staff-card bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow cursor-pointer active:scale-95">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-16 h-16 ${colors.bg} rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-2xl font-bold ${colors.text}">${initials}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 text-lg truncate">${fullName}</h3>
                        <p class="text-sm text-gray-500 truncate">${user.email}</p>
                        <p class="text-xs ${colors.text} font-medium mt-1">${roleDisplay}</p>
                    </div>
                </div>
                ${statsHTML}
            </div>
        `;
    }).join('');
}

// Render list view
function renderListView() {
    const listViewBody = document.getElementById('listViewBody');
    if (!listViewBody) return;
    
    const paginatedUsers = getPaginatedUsers();
    
    if (paginatedUsers.length === 0) {
        listViewBody.innerHTML = `
            <tr>
                <td colspan="4" class="px-6 py-12 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <p class="text-gray-500 text-sm">No users found</p>
                </td>
            </tr>
        `;
        return;
    }
    
    listViewBody.innerHTML = paginatedUsers.map(user => {
        const colors = getRoleColors(user.role);
        const initials = getInitials(user);
        const fullName = `${user.firstName} ${user.lastName}`;
        const roleDisplay = getRoleDisplayName(user.role);
        const statusText = user.active ? 'Active' : 'Inactive';
        const statusColor = user.active ? 'text-green-600' : 'text-gray-500';
        
        // For technicians, show status if available
        const technicianStatus = user.role === 'technician' && user.status ? user.status : statusText;
        const technicianStatusColor = user.role === 'technician' && user.status === 'Available' ? 'text-green-600' : 
                                     user.role === 'technician' && user.status === 'Busy' ? 'text-[#003047]' : statusColor;
        
        return `
            <tr data-role="${roleDisplay}" onclick="window.location.href='view.php?id=${user.id}'" class="staff-row hover:bg-gray-50 cursor-pointer transition">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="w-10 h-10 ${colors.bg} rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-bold ${colors.text}">${initials}</span>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">${fullName}</div>
                            <div class="text-sm text-gray-500">${user.email}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-xs ${colors.badgeText} font-medium px-2 py-1 ${colors.badge} rounded">${roleDisplay}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-sm font-medium ${technicianStatusColor}">${technicianStatus}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Today</td>
            </tr>
        `;
    }).join('');
}

// Pagination helpers
function getPaginatedUsers() {
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity) {
        return usersData;
    }
    const startIndex = (currentPage - 1) * PAGE_SIZE;
    return usersData.slice(startIndex, startIndex + PAGE_SIZE);
}

function updatePaginationState() {
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity) {
        totalPages = 1;
        currentPage = 1;
        return;
    }
    totalPages = Math.max(1, Math.ceil(usersData.length / PAGE_SIZE));
    if (currentPage > totalPages) {
        currentPage = totalPages;
    }
    if (currentPage < 1) {
        currentPage = 1;
    }
}

function renderPagination() {
    const paginationContainer = document.getElementById('usersPagination');
    if (!paginationContainer) return;
    
    // Hide pagination if showing all or if results fit in one page
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity || usersData.length <= PAGE_SIZE || totalPages <= 1) {
        paginationContainer.innerHTML = '';
        return;
    }
    
    let paginationHTML = '<div class="flex items-center gap-2 justify-center">';
    
    const disabledClass = 'text-gray-400 cursor-not-allowed opacity-50';
    const activeClass = 'bg-[#003047] text-white';
    const defaultClass = 'bg-white text-gray-700 border border-gray-300 hover:border-[#003047] hover:text-[#003047]';
    
    // First button
    paginationHTML += `
        <button class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300 ${currentPage === 1 ? disabledClass : 'bg-white text-[#003047] hover:bg-gray-100 hover:border-[#003047]'}"
                ${currentPage === 1 ? 'disabled' : ''}
                onclick="goToPage(1)"
                title="First page">
            &laquo;
        </button>
    `;
    
    // Previous button
    paginationHTML += `
        <button class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300 ${currentPage === 1 ? disabledClass : 'bg-white text-[#003047] hover:bg-gray-100 hover:border-[#003047]'}"
                ${currentPage === 1 ? 'disabled' : ''}
                onclick="changePage(-1)"
                title="Previous page">
            &lt;
        </button>
    `;
    
    const maxButtons = 6;
    let startPage = Math.max(1, currentPage - Math.floor(maxButtons / 2));
    let endPage = startPage + maxButtons - 1;
    
    if (endPage > totalPages) {
        endPage = totalPages;
        startPage = Math.max(1, endPage - maxButtons + 1);
    }
    
    // Page number buttons
    for (let page = startPage; page <= endPage; page++) {
        const isActive = page === currentPage;
        paginationHTML += `
            <button class="px-3 py-2 text-sm font-medium rounded-md border ${isActive ? activeClass : defaultClass}"
                    onclick="goToPage(${page})">
                ${page}
            </button>
        `;
    }
    
    // Next button
    paginationHTML += `
        <button class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300 ${currentPage === totalPages ? disabledClass : 'bg-white text-[#003047] hover:bg-gray-100 hover:border-[#003047]'}"
                ${currentPage === totalPages ? 'disabled' : ''}
                onclick="changePage(1)"
                title="Next page">
            &gt;
        </button>
    `;
    
    // Last button
    paginationHTML += `
        <button class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300 ${currentPage === totalPages ? disabledClass : 'bg-white text-[#003047] hover:bg-gray-100 hover:border-[#003047]'}"
                ${currentPage === totalPages ? 'disabled' : ''}
                onclick="goToPage(${totalPages})"
                title="Last page">
            &raquo;
        </button>
    `;
    
    paginationHTML += '</div>';
    paginationContainer.innerHTML = paginationHTML;
}

function goToPage(page) {
    if (page < 1 || page > totalPages || page === currentPage) return;
    currentPage = page;
    renderStaff();
    updateResultsCounter();
}

function changePage(offset) {
    goToPage(currentPage + offset);
}

// Change items per page
function changePerPage(value) {
    PAGE_SIZE = value === 'all' ? Infinity : parseInt(value);
    currentPage = 1;
    updatePaginationState();
    renderStaff();
    updateResultsCounter();
    
    // Save preference to localStorage
    localStorage.setItem('usersPerPage', value);
}

// Update results counter
function updateResultsCounter() {
    const resultsCounter = document.getElementById('usersResultsCounter');
    if (!resultsCounter) return;
    
    const total = usersData.length;
    
    if (total === 0) {
        resultsCounter.textContent = 'No results found';
        return;
    }
    
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity) {
        resultsCounter.textContent = `Showing all ${total} result${total !== 1 ? 's' : ''}`;
        return;
    }
    
    const startIndex = (currentPage - 1) * PAGE_SIZE;
    const endIndex = Math.min(startIndex + PAGE_SIZE, total);
    const start = total > 0 ? startIndex + 1 : 0;
    
    resultsCounter.textContent = `Showing ${start}-${endIndex} of ${total} result${total !== 1 ? 's' : ''}`;
}

function toggleView(view) {
    currentView = view;
    localStorage.setItem('staffView', view);
    
    const gridView = document.getElementById('gridView');
    const listView = document.getElementById('listView');
    const gridBtn = document.getElementById('gridViewBtn');
    const listBtn = document.getElementById('listViewBtn');
    
    if (view === 'grid') {
        gridView.classList.remove('hidden');
        listView.classList.add('hidden');
        gridBtn.querySelector('svg').classList.remove('text-gray-500');
        gridBtn.querySelector('svg').classList.add('text-gray-900');
        gridBtn.classList.add('bg-white');
        gridBtn.classList.remove('hover:bg-white');
        listBtn.querySelector('svg').classList.remove('text-gray-900');
        listBtn.querySelector('svg').classList.add('text-gray-500');
        listBtn.classList.remove('bg-white');
        listBtn.classList.add('hover:bg-white');
        renderGridView();
    } else {
        gridView.classList.add('hidden');
        listView.classList.remove('hidden');
        listBtn.querySelector('svg').classList.remove('text-gray-500');
        listBtn.querySelector('svg').classList.add('text-gray-900');
        listBtn.classList.add('bg-white');
        listBtn.classList.remove('hover:bg-white');
        gridBtn.querySelector('svg').classList.remove('text-gray-900');
        gridBtn.querySelector('svg').classList.add('text-gray-500');
        gridBtn.classList.remove('bg-white');
        gridBtn.classList.add('hover:bg-white');
        renderListView();
    }
}

// Initialize view on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize per page selector from localStorage
    const savedPerPage = localStorage.getItem('usersPerPage');
    if (savedPerPage) {
        const perPageSelect = document.getElementById('perPageSelect');
        if (perPageSelect) {
            perPageSelect.value = savedPerPage;
            PAGE_SIZE = savedPerPage === 'all' ? Infinity : parseInt(savedPerPage);
        }
    }
    
    fetchUsers().then(() => {
        toggleView(currentView);
        initializeRoleFilter();
    });
});

function initializeRoleFilter() {
    // Get role from URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const role = urlParams.get('role');
    
    if (role) {
        // Convert URL parameter to proper case (technician -> Technician)
        // Handle multi-word roles like "Admin" which is already capitalized
        let roleCapitalized;
        if (role.toLowerCase() === 'admin') {
            roleCapitalized = 'Admin';
        } else if (role.toLowerCase() === 'receptionist') {
            roleCapitalized = 'Receptionist';
        } else if (role.toLowerCase() === 'technician') {
            roleCapitalized = 'Technician';
        } else {
            roleCapitalized = role.charAt(0).toUpperCase() + role.slice(1).toLowerCase();
        }
        currentRoleFilter = roleCapitalized;
        // Update active tab without updating URL or calling applyFilters yet
        document.querySelectorAll('.role-tab').forEach(tab => {
            tab.classList.remove('text-[#003047]', 'border-[#003047]', 'border-b-2');
            tab.classList.add('text-gray-500', 'border-transparent');
        });
        const activeTab = document.getElementById('tab-' + roleCapitalized);
        if (activeTab) {
            activeTab.classList.remove('text-gray-500', 'border-transparent');
            activeTab.classList.add('text-[#003047]', 'border-[#003047]', 'border-b-2');
        }
    }
    
    // Apply filters to render the data (this will render staff)
    applyFilters();
}

function filterByRole(role, updateURL = true) {
    currentRoleFilter = role;
    
    // Update URL
    if (updateURL) {
        const url = new URL(window.location);
        if (role === 'all') {
            url.searchParams.delete('role');
        } else {
            url.searchParams.set('role', role.toLowerCase());
        }
        window.history.pushState({}, '', url);
    }
    
    // Update active tab
    document.querySelectorAll('.role-tab').forEach(tab => {
        tab.classList.remove('text-[#003047]', 'border-[#003047]', 'border-b-2');
        tab.classList.add('text-gray-500', 'border-transparent');
    });
    
    const activeTab = document.getElementById('tab-' + role);
    if (activeTab) {
        activeTab.classList.remove('text-gray-500', 'border-transparent');
        activeTab.classList.add('text-[#003047]', 'border-[#003047]', 'border-b-2');
    }
    
    applyFilters();
}

function filterStaff(searchTerm) {
    currentSearchTerm = searchTerm.toLowerCase();
    applyFilters();
}

function applyFilters() {
    // Filter users data based on role and search
    usersData = allUsers.filter(user => {
        // Role filter - compare case-insensitively
        let roleMatch = true;
        if (currentRoleFilter !== 'all') {
            const userRoleDisplay = getRoleDisplayName(user.role);
            // Normalize both for comparison (handle case differences)
            roleMatch = userRoleDisplay.toLowerCase() === currentRoleFilter.toLowerCase();
        }
        
        // Search filter
        let searchMatch = true;
        if (currentSearchTerm !== '') {
            const fullName = `${user.firstName} ${user.lastName}`;
            const searchText = (fullName + ' ' + (user.email || '')).toLowerCase();
            searchMatch = searchText.includes(currentSearchTerm);
        }
        
        return roleMatch && searchMatch;
    });
    
    // Reset to first page when filters change
    currentPage = 1;
    updatePaginationState();
    renderStaff();
    updateResultsCounter();
}

function openNewUserModal() {
    const modalContent = `
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">New User</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form onsubmit="saveUser(event)" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                        <input type="text" name="first_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                        <input type="text" name="last_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" name="phone" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                    <select name="role" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                        <option value="">Select Role</option>
                        <option value="Admin">Admin</option>
                        <option value="Receptionist">Receptionist</option>
                        <option value="Technician">Technician</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                </div>
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <label class="text-sm font-medium text-gray-900">Active</label>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="active" class="sr-only peer" checked>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div>
                    </label>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">
                        Save User
                    </button>
                </div>
            </form>
        </div>
    `;
    openModal(modalContent);
}

function saveUser(event) {
    event.preventDefault();
    showSuccessMessage('User added successfully!');
    closeModal();
    setTimeout(() => location.reload(), 1500);
}

function showSuccessMessage(message) {
    const successDiv = document.createElement('div');
    successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    successDiv.textContent = message;
    document.body.appendChild(successDiv);
    setTimeout(() => successDiv.remove(), 3000);
}

function showErrorMessage(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    errorDiv.textContent = message;
    document.body.appendChild(errorDiv);
    setTimeout(() => errorDiv.remove(), 3000);
}

</script>

<?php include '../includes/footer.php'; ?>



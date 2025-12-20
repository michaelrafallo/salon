<?php
$pageTitle = 'Technicians';
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/modal.php';

// Get current role (default to 'admin')
$current_role = isset($_SESSION['selected_role']) ? $_SESSION['selected_role'] : 'admin';
$is_admin = ($current_role === 'admin');
?>

<!-- Main Content Area -->
<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Technicians</h1>
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
                <button onclick="openAddTechnicianModal()" class="px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm sm:text-base active:scale-95">
                    + Add Technician
                </button>
            </div>
        </div>

        <!-- Status Filter Tabs and Search Bar -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <!-- Status Filter Tabs -->
            <div class="flex items-center gap-2 border-b border-gray-200 overflow-x-auto">
                <button onclick="filterByStatus('all')" id="tab-all" class="status-tab px-4 py-2 text-sm font-medium text-[#003047] border-b-2 border-[#003047] transition whitespace-nowrap">
                    All
                </button>
                <button onclick="filterByStatus('active')" id="tab-online" class="status-tab px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300 transition whitespace-nowrap">
                    Active
                </button>
                <button onclick="filterByStatus('inactive')" id="tab-offline" class="status-tab px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300 transition whitespace-nowrap">
                    Inactive
                </button>
            </div>
            
            <!-- Search Bar -->
            <div class="relative w-full sm:w-[400px]">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" id="technicianSearchInput" placeholder="Search technicians..." oninput="searchTechnicians(this.value)" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-base">
            </div>
        </div>

        <!-- Technicians Grid View - Tablet/Touch Optimized -->
        <div id="gridView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- Technician cards will be dynamically loaded here -->
        </div>

        <!-- Technicians List View -->
        <div id="listView" class="hidden">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Technician</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <?php if ($is_admin): ?>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tip</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commission</th>
                            <?php endif; ?>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clock</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="listViewBody" class="bg-white divide-y divide-gray-200">
                        <!-- Technician rows will be dynamically loaded here -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Results Counter and Per Page Selector -->
        <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div id="techniciansResultsCounter" class="text-sm text-gray-600">
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
        <div id="techniciansPagination" class="mt-4 flex justify-center"></div>
    </div>
</main>

<script>
// Global variables for filtering
let currentStatusFilter = 'all';
let currentSearchTerm = '';
let allTechnicians = [];
let techniciansData = []; // Filtered technicians for display
let technicianData = {};
const isAdmin = <?php echo $is_admin ? 'true' : 'false'; ?>;
let PAGE_SIZE = 15; // Can be changed by user
let currentPage = 1;
let totalPages = 1;

// View toggle functionality
let currentView = localStorage.getItem('techniciansView') || 'grid';

// Fetch technicians from JSON
async function fetchTechnicians() {
    try {
        const response = await fetch('../json/users.json');
        const data = await response.json();
        // Filter only technicians
        allTechnicians = data.users.filter(user => user.role === 'technician');
        
        // Build technicianData object for clock in/out functionality
        allTechnicians.forEach(technician => {
            technicianData[technician.id] = {
                name: `${technician.firstName} ${technician.lastName}`,
                email: technician.email,
                role: 'Technician',
                initials: technician.initials || (technician.firstName?.[0] || '') + (technician.lastName?.[0] || '')
            };
        });
        
        techniciansData = allTechnicians; // Initially show all technicians
        renderTechnicians();
    } catch (error) {
        console.error('Error fetching technicians:', error);
        showErrorMessage('Failed to load technicians data');
    }
}

// Get initials from technician
function getInitials(technician) {
    return technician.initials || (technician.firstName?.[0] || '') + (technician.lastName?.[0] || '');
}

// Color variations for technician cards
const colorClasses = [
    { bg: 'bg-[#e6f0f3]', text: 'text-[#003047]' },
    { bg: 'bg-purple-100', text: 'text-purple-600' },
    { bg: 'bg-teal-100', text: 'text-teal-600' },
    { bg: 'bg-indigo-100', text: 'text-indigo-600' },
    { bg: 'bg-rose-100', text: 'text-rose-600' },
    { bg: 'bg-blue-100', text: 'text-blue-600' },
    { bg: 'bg-amber-100', text: 'text-amber-600' },
    { bg: 'bg-green-100', text: 'text-green-600' }
];

// Render technicians in grid and list views
function renderTechnicians() {
    updatePaginationState();
    renderGridView();
    renderListView();
    initializeTechnicianLoginStates();
    renderPagination();
    updateResultsCounter();
}

// Render grid view
function renderGridView() {
    const gridView = document.getElementById('gridView');
    if (!gridView) return;
    
    const paginatedTechnicians = getPaginatedTechnicians();
    
    if (paginatedTechnicians.length === 0) {
        gridView.innerHTML = `
            <div class="col-span-full text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                <p class="text-gray-500 text-sm">No technicians found</p>
            </div>
        `;
        return;
    }
    
    gridView.innerHTML = paginatedTechnicians.map((technician, index) => {
        const initials = getInitials(technician);
        const fullName = `${technician.firstName} ${technician.lastName}`;
        const color = colorClasses[index % colorClasses.length];
        const isLoggedIn = localStorage.getItem('technician_' + technician.id + '_loggedIn') === 'true';
        const statusClass = isLoggedIn 
            ? 'bg-green-100 text-green-700' 
            : 'bg-gray-100 text-gray-700';
        const statusText = isLoggedIn ? 'Active' : 'Inactive';
        
        const statsHTML = isAdmin ? `
            <div class="grid grid-cols-3 gap-3 mb-4 pt-4 border-t border-gray-200">
                <div>
                    <p class="text-xs text-gray-500">Total</p>
                    <p class="text-lg font-bold text-gray-900">$${(technician.totalEarnings || 0).toFixed(2)}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Tip</p>
                    <p class="text-lg font-bold text-gray-900">$${(technician.totalTips || 0).toFixed(2)}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-500">Commission</p>
                    <p class="text-lg font-bold text-gray-900">$${(technician.totalCommission || 0).toFixed(2)}</p>
                </div>
            </div>
        ` : '';
        
        return `
            <div onclick="event.stopPropagation(); window.location.href='view.php?id=${technician.id}'" class="technician-card bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow cursor-pointer active:scale-95" data-status="${statusText.toLowerCase()}">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-16 h-16 ${color.bg} rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-2xl font-bold ${color.text}">${initials}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 text-lg truncate">${fullName}</h3>
                        <p class="text-sm text-gray-500">Technician</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span id="status-badge-${technician.id}" class="px-2 py-1 ${statusClass} text-xs font-medium rounded">${statusText}</span>
                        </div>
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
    
    const paginatedTechnicians = getPaginatedTechnicians();
    
    if (paginatedTechnicians.length === 0) {
        listViewBody.innerHTML = `
            <tr>
                <td colspan="${isAdmin ? '7' : '4'}" class="px-6 py-12 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <p class="text-gray-500 text-sm">No technicians found</p>
                </td>
            </tr>
        `;
        return;
    }
    
    listViewBody.innerHTML = paginatedTechnicians.map((technician, index) => {
        const initials = getInitials(technician);
        const fullName = `${technician.firstName} ${technician.lastName}`;
        const color = colorClasses[index % colorClasses.length];
        const isLoggedIn = localStorage.getItem('technician_' + technician.id + '_loggedIn') === 'true';
        const statusClass = isLoggedIn 
            ? 'bg-green-100 text-green-700' 
            : 'bg-gray-100 text-gray-700';
        const statusText = isLoggedIn ? 'Active' : 'Inactive';
        const clockInTime = localStorage.getItem('technician_' + technician.id + '_clockIn') || '--';
        const clockOutTime = localStorage.getItem('technician_' + technician.id + '_clockOut') || '--';
        const buttonClass = isLoggedIn 
            ? 'bg-red-500 hover:bg-red-600' 
            : 'bg-green-500 hover:bg-green-600';
        const buttonText = isLoggedIn ? 'Clock Out' : 'Clock In';
        
        const adminColumns = isAdmin ? `
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-bold text-gray-900">$${(technician.totalEarnings || 0).toFixed(2)}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-bold text-gray-900">$${(technician.totalTips || 0).toFixed(2)}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-bold text-gray-900">$${(technician.totalCommission || 0).toFixed(2)}</div>
            </td>
        ` : '';
        
        return `
            <tr onclick="event.stopPropagation(); window.location.href='view.php?id=${technician.id}'" class="technician-row hover:bg-gray-50 cursor-pointer transition" data-status="${statusText.toLowerCase()}">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="w-10 h-10 ${color.bg} rounded-full flex items-center justify-center flex-shrink-0 mr-3">
                            <span class="text-sm font-bold ${color.text}">${initials}</span>
                        </div>
                        <div class="text-sm font-medium text-gray-900">${fullName}</div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span id="status-badge-list-${technician.id}" class="px-2 py-1 ${statusClass} text-xs font-medium rounded">${statusText}</span>
                </td>
                ${adminColumns}
                <td class="px-6 py-4 whitespace-nowrap" onclick="event.stopPropagation()">
                    <div class="space-y-1" id="clockInfo-${technician.id}">
                        <div class="text-xs text-gray-900 font-medium" id="clockTimeIn-${technician.id}">In: ${clockInTime}</div>
                        <div class="text-xs text-gray-900 font-medium" id="clockTimeOut-${technician.id}">Out: ${clockOutTime}</div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right" onclick="event.stopPropagation()">
                    <div class="flex items-center justify-end gap-2">
                        <button id="loginToggle-${technician.id}" onclick="toggleTechnicianLogin(${technician.id})" class="px-3 py-1.5 ${buttonClass} text-white text-xs font-medium rounded hover:opacity-90 transition active:scale-95">
                            ${buttonText}
                        </button>
                        <button onclick="printTechnician(${technician.id})" class="px-3 py-1.5 bg-gray-500 text-white text-xs font-medium rounded hover:bg-gray-600 transition active:scale-95 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            Ticket
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

// Pagination helpers
function getPaginatedTechnicians() {
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity) {
        return techniciansData;
    }
    const startIndex = (currentPage - 1) * PAGE_SIZE;
    return techniciansData.slice(startIndex, startIndex + PAGE_SIZE);
}

function updatePaginationState() {
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity) {
        totalPages = 1;
        currentPage = 1;
        return;
    }
    totalPages = Math.max(1, Math.ceil(techniciansData.length / PAGE_SIZE));
    if (currentPage > totalPages) {
        currentPage = totalPages;
    }
    if (currentPage < 1) {
        currentPage = 1;
    }
}

function renderPagination() {
    const paginationContainer = document.getElementById('techniciansPagination');
    if (!paginationContainer) return;
    
    // Hide pagination if showing all or if results fit in one page
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity || techniciansData.length <= PAGE_SIZE || totalPages <= 1) {
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
    renderTechnicians();
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
    renderTechnicians();
    updateResultsCounter();
    
    // Save preference to localStorage
    localStorage.setItem('techniciansPerPage', value);
}

// Update results counter
function updateResultsCounter() {
    const resultsCounter = document.getElementById('techniciansResultsCounter');
    if (!resultsCounter) return;
    
    const total = techniciansData.length;
    
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
    localStorage.setItem('techniciansView', view);
    
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

// Filter by status
function filterByStatus(status) {
    // Map status to internal format for filtering (uses 'online'/'offline')
    // Map to URL format for display (uses 'active'/'inactive')
    let internalStatus = status;
    let urlStatus = status;
    
    // Handle new format: 'active'/'inactive'
    if (status === 'active') {
        internalStatus = 'online';
        urlStatus = 'active';
    } else if (status === 'inactive') {
        internalStatus = 'offline';
        urlStatus = 'inactive';
    }
    // Handle old format for backward compatibility: 'online'/'offline'
    else if (status === 'online') {
        internalStatus = 'online';
        urlStatus = 'active';
    } else if (status === 'offline') {
        internalStatus = 'offline';
        urlStatus = 'inactive';
    }
    
    currentStatusFilter = internalStatus;
    
    // Update URL parameter with 'active'/'inactive' (or 'all')
    const url = new URL(window.location);
    if (internalStatus === 'all' || internalStatus === '') {
        url.searchParams.delete('status');
    } else {
        url.searchParams.set('status', urlStatus);
    }
    window.history.pushState({}, '', url);
    
    // Update active tab styling
    document.querySelectorAll('.status-tab').forEach(tab => {
        tab.classList.remove('text-[#003047]', 'border-[#003047]', 'border-b-2');
        tab.classList.add('text-gray-500', 'border-transparent');
    });
    
    // Get the active tab ID based on internal status
    let activeTabId = 'tab-all';
    if (internalStatus === 'online') activeTabId = 'tab-online';
    else if (internalStatus === 'offline') activeTabId = 'tab-offline';
    
    const activeTab = document.getElementById(activeTabId);
    if (activeTab) {
        activeTab.classList.remove('text-gray-500', 'border-transparent');
        activeTab.classList.add('text-[#003047]', 'border-[#003047]', 'border-b-2');
    }
    
    applyFilters();
}

// Search technicians
function searchTechnicians(searchTerm) {
    currentSearchTerm = searchTerm.toLowerCase();
    applyFilters();
}

// Apply filters (status and search)
function applyFilters() {
    // Filter technicians data based on status and search
    techniciansData = allTechnicians.filter(technician => {
        // Status filter
        let statusMatch = true;
        if (currentStatusFilter !== 'all') {
            const isLoggedIn = localStorage.getItem('technician_' + technician.id + '_loggedIn') === 'true';
            const status = isLoggedIn ? 'online' : 'offline';
            statusMatch = status === currentStatusFilter;
        }
        
        // Search filter
        let searchMatch = true;
        if (currentSearchTerm !== '') {
            const fullName = `${technician.firstName} ${technician.lastName}`;
            const searchText = (fullName + ' ' + (technician.email || '')).toLowerCase();
            searchMatch = searchText.includes(currentSearchTerm);
        }
        
        return statusMatch && searchMatch;
    });
    
    // Reset to first page when filters change
    currentPage = 1;
    updatePaginationState();
    renderTechnicians();
    updateResultsCounter();
}

// Initialize view on page load
document.addEventListener('DOMContentLoaded', function() {
    // Initialize per page selector from localStorage
    const savedPerPage = localStorage.getItem('techniciansPerPage');
    if (savedPerPage) {
        const perPageSelect = document.getElementById('perPageSelect');
        if (perPageSelect) {
            perPageSelect.value = savedPerPage;
            PAGE_SIZE = savedPerPage === 'all' ? Infinity : parseInt(savedPerPage);
        }
    }
    
    fetchTechnicians().then(() => {
    toggleView(currentView);
        
        // Initialize status filter from URL parameter
        const urlParams = new URLSearchParams(window.location.search);
        let statusParam = urlParams.get('status');
        
        // Map old URL parameters to new ones for backward compatibility
        if (statusParam === 'online') statusParam = 'active';
        else if (statusParam === 'offline') statusParam = 'inactive';
        
        // Use status from URL or default to 'all'
        const defaultStatus = statusParam || 'all';
        
        // Filter by the status from URL or default to all
        filterByStatus(defaultStatus);
    });
});

function initializeTechnicianLoginStates() {
    // Initialize login state from localStorage for each technician
    allTechnicians.forEach(technician => {
        const technicianId = technician.id;
        const isLoggedIn = localStorage.getItem('technician_' + technicianId + '_loggedIn') === 'true';
        const button = document.getElementById('loginToggle-' + technicianId);
        const clockInTime = localStorage.getItem('technician_' + technicianId + '_clockIn');
        const clockOutTime = localStorage.getItem('technician_' + technicianId + '_clockOut');
        
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
        
        // Update status badge based on login state
        updateStatusBadge(technicianId, isLoggedIn);
        
        // Update clock display with stored times
        updateClockDisplay(technicianId, clockInTime, clockOutTime);
    });
}

function toggleTechnicianLogin(technicianId) {
    const button = document.getElementById('loginToggle-' + technicianId);
    const isLoggedIn = button.textContent.trim() === 'Clock Out';
    const action = isLoggedIn ? 'Clock Out' : 'Clock In';
    const tech = technicianData[technicianId];
    
    if (!tech) {
        console.error('Technician data not found for ID:', technicianId);
        return;
    }
    
    // Show modal with details
    showLoginLogoutModal(tech, action, technicianId, isLoggedIn);
}

function showLoginLogoutModal(technician, action, technicianId, isCurrentlyLoggedIn) {
    const now = new Date();
    const dateTime = now.toLocaleString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: true
    });
    
    const actionColor = action === 'Clock In' ? 'green' : 'red';
    const actionBgColor = action === 'Clock In' ? 'bg-green-100' : 'bg-red-100';
    const actionTextColor = action === 'Clock In' ? 'text-green-700' : 'text-red-700';
    
    const modalContent = `
        <div class="p-6 max-w-2xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Technician ${action}</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="space-y-6">
                <!-- Technician Details -->
                <div class="bg-gray-50 rounded-lg p-5">
                    <h4 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">Technician Details</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Name</p>
                            <p class="text-sm font-medium text-gray-900">${technician.name}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Email</p>
                            <p class="text-sm font-medium text-gray-900">${technician.email}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Role</p>
                            <p class="text-sm font-medium text-gray-900">${technician.role}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">ID</p>
                            <p class="text-sm font-medium text-gray-900">#${technicianId}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Action -->
                <div class="bg-gray-50 rounded-lg p-5">
                    <h4 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">Action</h4>
                    <div class="flex items-center gap-3">
                        <span class="px-4 py-2 ${actionBgColor} ${actionTextColor} text-sm font-semibold rounded-lg">
                            ${action}
                        </span>
                        <p class="text-sm text-gray-600">
                            ${action === 'Clock In' ? 'Technician is clocking into the system' : 'Technician is clocking out of the system'}
                        </p>
                    </div>
                </div>
                
                <!-- Date & Time -->
                <div class="bg-gray-50 rounded-lg p-5">
                    <h4 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">Date & Time</h4>
                    <p class="text-sm font-medium text-gray-900">${dateTime}</p>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-gray-200">
                <button onclick="closeModal()" class="px-6 py-2.5 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">
                    Cancel
                </button>
                <button onclick="confirmLoginLogout(${technicianId}, '${action}')" class="px-6 py-2.5 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">
                    Confirm ${action}
                </button>
            </div>
        </div>
    `;
    openModal(modalContent);
}

function formatDateTime(date) {
    const months = ['Jan.', 'Feb.', 'Mar.', 'Apr.', 'May', 'Jun.', 'Jul.', 'Aug.', 'Sep.', 'Oct.', 'Nov.', 'Dec.'];
    const month = months[date.getMonth()];
    const day = date.getDate();
    const year = date.getFullYear();
    let hours = date.getHours();
    const minutes = date.getMinutes();
    const ampm = hours >= 12 ? 'PM' : 'AM';
    hours = hours % 12;
    hours = hours ? hours : 12;
    const minutesStr = minutes < 10 ? '0' + minutes : minutes;
    return `${month} ${day}, ${year} ${hours}:${minutesStr} ${ampm}`;
}

function updateClockDisplay(technicianId, clockInTime, clockOutTime) {
    const clockInElement = document.getElementById('clockTimeIn-' + technicianId);
    const clockOutElement = document.getElementById('clockTimeOut-' + technicianId);
    
    if (clockInElement) {
        clockInElement.textContent = 'In: ' + (clockInTime || '--');
    }
    if (clockOutElement) {
        clockOutElement.textContent = 'Out: ' + (clockOutTime || '--');
    }
}

function confirmLoginLogout(technicianId, action) {
    const button = document.getElementById('loginToggle-' + technicianId);
    const isClockIn = action === 'Clock In';
    const now = new Date();
    const formattedDateTime = formatDateTime(now);
    
    if (isClockIn) {
        // Clock In - Set status to Active
        button.textContent = 'Clock Out';
        button.classList.remove('bg-green-500', 'hover:bg-green-600');
        button.classList.add('bg-red-500', 'hover:bg-red-600');
        localStorage.setItem('technician_' + technicianId + '_loggedIn', 'true');
        localStorage.setItem('technician_' + technicianId + '_clockIn', formattedDateTime);
        // Clear clock out time
        localStorage.removeItem('technician_' + technicianId + '_clockOut');
        updateClockDisplay(technicianId, formattedDateTime, '--');
        updateStatusBadge(technicianId, true); // Active
    } else {
        // Clock Out - Set status to Inactive
        button.textContent = 'Clock In';
        button.classList.remove('bg-red-500', 'hover:bg-red-600');
        button.classList.add('bg-green-500', 'hover:bg-green-600');
        localStorage.setItem('technician_' + technicianId + '_loggedIn', 'false');
        localStorage.setItem('technician_' + technicianId + '_clockOut', formattedDateTime);
        // Get clock in time from localStorage
        const clockInTime = localStorage.getItem('technician_' + technicianId + '_clockIn') || '--';
        updateClockDisplay(technicianId, clockInTime, formattedDateTime);
        updateStatusBadge(technicianId, false); // Inactive
    }
    
    // Reapply filters to update displayed technicians
    applyFilters();
    
    closeModal();
    showSuccessMessage(`Technician ${action.toLowerCase()} successful!`);
    
    // In a real application, you would send this data to the server
    console.log(`${action} technician:`, technicianId);
}

// Update status badge (Active/Inactive)
function updateStatusBadge(technicianId, isOnline) {
    // Update grid view badge
    const gridBadge = document.getElementById('status-badge-' + technicianId);
    if (gridBadge) {
        if (isOnline) {
            gridBadge.textContent = 'Active';
            gridBadge.className = 'px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded';
        } else {
            gridBadge.textContent = 'Inactive';
            gridBadge.className = 'px-2 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded';
        }
    }
    
    // Update list view badge
    const listBadge = document.getElementById('status-badge-list-' + technicianId);
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

function openAddTechnicianModal() {
    const modalContent = `
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">Add New Technician</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form onsubmit="saveTechnician(event)" class="space-y-4">
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
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <input type="tel" name="phone" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">
                        Save Technician
                    </button>
                </div>
            </form>
        </div>
    `;
    openModal(modalContent);
}

function saveTechnician(event) {
    event.preventDefault();
    showSuccessMessage('Technician added successfully!');
    closeModal();
    setTimeout(() => location.reload(), 1500);
}


function openAssignTechFromList(button) {
    const card = button.closest('.bg-white');
    const technicianName = card.querySelector('h3').textContent;
    const modalContent = `
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">Assign ${technicianName}</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <p class="text-sm text-gray-600 mb-4">Select a service from the queue to assign this technician.</p>
            <div class="space-y-2 border border-gray-300 rounded-lg p-4 max-h-60 overflow-y-auto">
                <button onclick="assignToService('ORDER925', 'Sarah Johnson', '${technicianName}')" class="w-full text-left p-3 hover:bg-gray-50 rounded border border-gray-200">
                    <p class="font-medium text-gray-900">Order #925</p>
                    <p class="text-xs text-gray-500">Sarah Johnson - Classic Manicure</p>
                </button>
                <button onclick="assignToService('ORDER926', 'Emily Chen', '${technicianName}')" class="w-full text-left p-3 hover:bg-gray-50 rounded border border-gray-200">
                    <p class="font-medium text-gray-900">Order #926</p>
                    <p class="text-xs text-gray-500">Emily Chen - Acrylic Full Set</p>
                </button>
            </div>
        </div>
    `;
    openModal(modalContent);
}

function assignToService(orderId, customerName, technicianName) {
    showSuccessMessage(`${technicianName} assigned to ${customerName}'s order!`);
    closeModal();
}

function showSuccessMessage(message) {
    const successDiv = document.createElement('div');
    successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    successDiv.textContent = message;
    document.body.appendChild(successDiv);
    setTimeout(() => successDiv.remove(), 3000);
}

// Show technician ticket modal
async function printTechnician(technicianId) {
    try {
        // Get technician info
        const technician = allTechnicians.find(t => t.id === technicianId);
        if (!technician) {
            showErrorMessage('Technician not found');
            return;
        }
        
        const technicianName = `${technician.firstName} ${technician.lastName}`;
        
        // Fetch appointments and payments
        const [appointmentsResponse, paymentsResponse] = await Promise.all([
            fetch('../json/appointments.json'),
            fetch('../json/payments.json')
        ]);
        
        const appointmentsData = await appointmentsResponse.json();
        const paymentsData = await paymentsResponse.json();
        
        // Get today's date
        const today = new Date();
        const todayStr = today.toISOString().split('T')[0]; // YYYY-MM-DD
        const formattedDate = today.toLocaleDateString('en-GB', { 
            day: '2-digit', 
            month: 'short', 
            year: 'numeric' 
        }).replace(/ /g, '-'); // Format: 19-Nov-2025
        
        // Filter appointments for this technician from today
        const technicianAppointments = appointmentsData.appointments.filter(apt => {
            // Check if technician is assigned
            const isAssigned = apt.assigned_technician && apt.assigned_technician.includes(technicianId);
            
            // Check if technician is in services
            const hasService = apt.services && apt.services.some(s => {
                const serviceTechId = s.technician_id ? 
                    (typeof s.technician_id === 'string' ? parseInt(s.technician_id) : s.technician_id) : 
                    null;
                return serviceTechId === technicianId;
            });
            
            // Check if appointment date is today
            const aptDate = apt.appointment_datetime ? apt.appointment_datetime.split('T')[0] : apt.created_at.split('T')[0];
            const isToday = aptDate === todayStr;
            
            return (isAssigned || hasService) && isToday;
        });
        
        // Fetch customers to match names
        const customersResponse = await fetch('../json/customers.json');
        const customersData = await customersResponse.json();
        
        // Build transactions list
        const transactions = [];
        let totalAmount = 0;
        let totalTip = 0;
        
        technicianAppointments.forEach(apt => {
            // Find customer
            const customer = customersData.customers.find(c => c.id === apt.customer_id);
            
            // Find payment for this appointment
            const payment = paymentsData.payments.find(p => {
                // Match by appointmentId
                if (p.appointmentId && p.appointmentId.toString() === apt.id.toString()) {
                    return true;
                }
                // Match by customer name
                if (customer && p.customerName) {
                    const customerFullName = `${customer.firstName} ${customer.lastName}`;
                    if (p.customerName === customerFullName) {
                        return true;
                    }
                }
                // Match by bookingId if available
                if (p.bookingId && apt.id.toString() === p.bookingId.toString()) {
                    return true;
                }
                return false;
            });
            
            // Only include if there's a payment (completed transactions)
            if (!payment || payment.status !== 'Completed') {
                return;
            }
            
            // Get appointment time
            const aptDateTime = apt.appointment_datetime || apt.created_at;
            const aptDate = new Date(aptDateTime);
            const time = aptDate.toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit',
                hour12: false 
            });
            const date = aptDate.toLocaleDateString('en-US', { 
                month: '2-digit', 
                day: '2-digit', 
                year: '2-digit' 
            });
            
            // Calculate amount for this technician
            // If multiple technicians, divide payment amount proportionally
            let technicianAmount = 0;
            if (apt.services && apt.services.length > 0) {
                // Count services assigned to this technician
                const techServiceCount = apt.services.filter(service => {
                    const serviceTechId = service.technician_id ? 
                        (typeof service.technician_id === 'string' ? parseInt(service.technician_id) : service.technician_id) : 
                        null;
                    return serviceTechId === technicianId;
                }).length;
                
                if (techServiceCount > 0) {
                    // Divide payment amount by total services, then multiply by technician's service count
                    technicianAmount = (payment.amount / apt.services.length) * techServiceCount;
                }
            } else {
                // If no services specified, divide equally among assigned technicians
                const assignedCount = apt.assigned_technician ? apt.assigned_technician.length : 1;
                technicianAmount = payment.amount / assignedCount;
            }
            
            // Get tip (divide equally if multiple technicians)
            let technicianTip = 0;
            if (payment.tip) {
                const assignedCount = apt.assigned_technician ? apt.assigned_technician.length : 1;
                technicianTip = payment.tip / assignedCount;
            }
            
            if (technicianAmount > 0) {
                transactions.push({
                    time: time,
                    date: date,
                    amount: technicianAmount,
                    tip: technicianTip
                });
                
                totalAmount += technicianAmount;
                totalTip += technicianTip;
            }
        });
        
        // Add sample transactions if no real transactions found (for demonstration)
        if (transactions.length === 0) {
            const todayDate = new Date();
            const sampleDate = todayDate.toLocaleDateString('en-US', { 
                month: '2-digit', 
                day: '2-digit', 
                year: '2-digit' 
            });
            
            // Sample transactions matching the screenshot format
            const sampleTransactions = [
                { time: '12:00', date: sampleDate, amount: 145.00, tip: 0.00 },
                { time: '13:31', date: sampleDate, amount: 70.00, tip: 0.00 },
                { time: '14:59', date: sampleDate, amount: 45.00, tip: 0.00 },
                { time: '15:41', date: sampleDate, amount: 60.00, tip: 0.00 },
                { time: '16:34', date: sampleDate, amount: 50.00, tip: 0.00 },
                { time: '17:28', date: sampleDate, amount: 45.00, tip: 10.00 },
                { time: '19:23', date: sampleDate, amount: 70.00, tip: 0.00 }
            ];
            
            transactions.push(...sampleTransactions);
            totalAmount = sampleTransactions.reduce((sum, txn) => sum + txn.amount, 0);
            totalTip = sampleTransactions.reduce((sum, txn) => sum + txn.tip, 0);
        }
        
        // Sort transactions by time (oldest first)
        transactions.sort((a, b) => {
            const timeA = a.time.split(':').map(Number);
            const timeB = b.time.split(':').map(Number);
            const minutesA = timeA[0] * 60 + timeA[1];
            const minutesB = timeB[0] * 60 + timeB[1];
            return minutesA - minutesB; // Oldest first
        });
        
        // Build modal content matching the screenshot format
        const transactionsHTML = transactions.length > 0 ? transactions.map(txn => `
            <tr class="border-b border-gray-200">
                <td class="px-2 py-2 text-sm text-gray-900">${txn.time} | ${txn.date}</td>
                <td class="px-2 py-2 text-sm text-gray-900 text-right">$${txn.amount.toFixed(2)} | $${txn.tip.toFixed(2)}</td>
            </tr>
        `).join('') : `
            <tr>
                <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-400">No transactions found for today</td>
            </tr>
        `;
        
        const modalContent = `
            <div class="p-6 mx-auto">
                <div class="bg-white border border-gray-300 rounded-lg p-6">
                    <!-- Business Header -->
                    <div class="text-center mb-6 border-b border-gray-300 pb-4">
                        <h2 class="text-xl font-bold text-gray-900 mb-2">Dons Nail Spa</h2>
                        <p class="text-sm text-gray-600">258 Hedrick St, Beckley, WV 25801</p>
                        <p class="text-sm text-gray-600">Phone: 681-2077114</p>
                        <p class="text-sm text-gray-600">Merchant ID (MID): 23420</p>
                    </div>
                    
                    <!-- Report Title -->
                    <div class="text-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">${technicianName} Daily Report</h3>
                        <p class="text-sm text-gray-600 mt-1">${formattedDate}</p>
                    </div>
                    
                    <!-- Transactions Table -->
                    <div class="mb-6">
                        <div class="max-h-96 overflow-y-auto border border-gray-200 rounded">
                            <table class="w-full border-collapse">
                                <thead class="sticky top-0 bg-white z-10">
                                    <tr class="border-b-2 border-gray-300">
                                        <th class="px-2 py-2 text-left text-xs font-semibold text-gray-700 uppercase">${technicianName}</th>
                                        <th class="px-2 py-2 text-right text-xs font-semibold text-gray-700 uppercase">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${transactionsHTML}
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Summary -->
                    <div class="border-t-2 border-gray-300 pt-4 mt-4">
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm font-semibold text-gray-900">Total Amount:</span>
                            <span class="text-sm font-bold text-gray-900">$${totalAmount.toFixed(2)}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-semibold text-gray-900">Total Tip:</span>
                            <span class="text-sm font-bold text-gray-900">$${totalTip.toFixed(2)}</span>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                        <button onclick="window.print()" class="px-6 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium">
                            Print
                        </button>
                        <button onclick="closeModal()" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        openModal(modalContent, 'medium');
        
    } catch (error) {
        console.error('Error loading technician ticket:', error);
        showErrorMessage('Failed to load technician ticket');
    }
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

<?php include '../includes/footer.php'; ?>

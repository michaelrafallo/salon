<?php
$pageTitle = 'Customers';
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/modal.php';
?>

<!-- Main Content Area -->
<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Customers</h1>
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
                <button onclick="openNewCustomerModal()" class="px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm sm:text-base active:scale-95">
                    + New Customer
                </button>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="mb-6">
            <div class="relative max-w-md">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" id="customerSearchInput" placeholder="Search customers by name, phone, or email..." oninput="searchCustomers(this.value)" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-base">
            </div>
        </div>

        <!-- Customers Grid View - Tablet/Touch Optimized -->
        <div id="gridView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- Customer cards will be dynamically loaded here -->
        </div>

        <!-- Customers List View -->
        <div id="listView" class="hidden">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Visits</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Visit</th>
                        </tr>
                    </thead>
                    <tbody id="listViewBody" class="bg-white divide-y divide-gray-200">
                        <!-- Customer rows will be dynamically loaded here -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Results Counter and Per Page Selector -->
        <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div id="customersResultsCounter" class="text-sm text-gray-600">
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
        <div id="customersPagination" class="mt-4 flex justify-center"></div>
    </div>
</main>

<script>
// Customer data
let allCustomers = [];
let customersData = []; // Filtered customers for display
let PAGE_SIZE = 15; // Can be changed by user
let currentPage = 1;
let totalPages = 1;
let currentSearchTerm = '';
// View toggle functionality
let currentView = localStorage.getItem('customersView') || 'grid';

// Color variations for customer cards
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

// Fetch customers from JSON
async function fetchCustomers() {
    try {
        const response = await fetch('../json/customers.json');
        const data = await response.json();
        allCustomers = data.customers;
        customersData = allCustomers; // Initially show all customers
        renderCustomers();
    } catch (error) {
        console.error('Error fetching customers:', error);
        showErrorMessage('Failed to load customers data');
    }
}

// Get initials from customer name
function getInitials(customer) {
    if (customer.initials) return customer.initials;
    const first = customer.firstName ? customer.firstName[0] : '';
    const last = customer.lastName ? customer.lastName[0] : '';
    return (first + last).toUpperCase();
}

// Get last visit date (can be enhanced with actual booking data)
function getLastVisit(customer) {
    // If lastVisit exists in customer data, use it
    if (customer.lastVisit) return customer.lastVisit;
    
    // For now, calculate a relative date from createdAt or use a default
    // This can be enhanced by calculating from booking.json
    if (customer.createdAt) {
        const createdDate = new Date(customer.createdAt);
        const daysDiff = Math.floor((new Date() - createdDate) / (1000 * 60 * 60 * 24));
        if (daysDiff === 0) return 'Today';
        if (daysDiff === 1) return '1 day ago';
        if (daysDiff < 7) return `${daysDiff} days ago`;
        if (daysDiff < 14) return '1 week ago';
        if (daysDiff < 30) return `${Math.floor(daysDiff / 7)} weeks ago`;
        return `${Math.floor(daysDiff / 30)} months ago`;
    }
    
    return 'N/A';
}

// Get total visits (mock for now - can be enhanced with actual booking data)
function getTotalVisits(customer) {
    return customer.totalBookings || 0;
}

// Render customers in grid and list views
function renderCustomers() {
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
    
    const paginatedCustomers = getPaginatedCustomers();
    
    if (paginatedCustomers.length === 0) {
        gridView.innerHTML = `
            <div class="col-span-full text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                <p class="text-gray-500 text-sm">No customers found</p>
            </div>
        `;
        return;
    }
    
    gridView.innerHTML = paginatedCustomers.map((customer, index) => {
        const color = colorClasses[index % colorClasses.length];
        const initials = getInitials(customer);
        const fullName = `${customer.firstName} ${customer.lastName}`;
        const totalVisits = getTotalVisits(customer);
        const lastVisit = getLastVisit(customer);
        
        return `
            <div onclick="window.location.href='view.php?id=${customer.id}'" class="customer-card bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow cursor-pointer active:scale-95">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-16 h-16 ${color.bg} rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-2xl font-bold ${color.text}">${initials}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 text-lg truncate">${fullName}</h3>
                        <p class="text-sm text-gray-500 truncate">${customer.email || ''}</p>
                        <p class="text-sm text-gray-500">${customer.phone || ''}</p>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3 mb-4 pt-4 border-t border-gray-200">
                    <div>
                        <p class="text-xs text-gray-500">Total Visits</p>
                        <p class="text-lg font-bold text-gray-900">${totalVisits}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Last Visit</p>
                        <p class="text-sm font-medium text-gray-900">${lastVisit}</p>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

// Render list view
function renderListView() {
    const listViewBody = document.getElementById('listViewBody');
    if (!listViewBody) return;
    
    const paginatedCustomers = getPaginatedCustomers();
    
    if (paginatedCustomers.length === 0) {
        listViewBody.innerHTML = `
            <tr>
                <td colspan="4" class="px-6 py-12 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <p class="text-gray-500 text-sm">No customers found</p>
                </td>
            </tr>
        `;
        return;
    }
    
    listViewBody.innerHTML = paginatedCustomers.map((customer, index) => {
        const color = colorClasses[index % colorClasses.length];
        const initials = getInitials(customer);
        const fullName = `${customer.firstName} ${customer.lastName}`;
        const totalVisits = getTotalVisits(customer);
        const lastVisit = getLastVisit(customer);
        
        return `
            <tr onclick="window.location.href='view.php?id=${customer.id}'" class="customer-row hover:bg-gray-50 cursor-pointer transition">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="w-10 h-10 ${color.bg} rounded-full flex items-center justify-center flex-shrink-0 mr-3">
                            <span class="text-sm font-bold ${color.text}">${initials}</span>
                        </div>
                        <div>
                            <div class="text-sm font-medium text-gray-900">${fullName}</div>
                            <div class="text-sm text-gray-500">${customer.email || ''}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">${customer.phone || ''}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm font-bold text-gray-900">${totalVisits}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">${lastVisit}</div>
                </td>
            </tr>
        `;
    }).join('');
}

// Pagination helpers
function getPaginatedCustomers() {
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity) {
        return customersData;
    }
    const startIndex = (currentPage - 1) * PAGE_SIZE;
    return customersData.slice(startIndex, startIndex + PAGE_SIZE);
}

function updatePaginationState() {
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity) {
        totalPages = 1;
        currentPage = 1;
        return;
    }
    totalPages = Math.max(1, Math.ceil(customersData.length / PAGE_SIZE));
    if (currentPage > totalPages) {
        currentPage = totalPages;
    }
    if (currentPage < 1) {
        currentPage = 1;
    }
}

function renderPagination() {
    const paginationContainer = document.getElementById('customersPagination');
    if (!paginationContainer) return;
    
    // Hide pagination if showing all or if results fit in one page
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity || customersData.length <= PAGE_SIZE || totalPages <= 1) {
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
    renderCustomers();
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
    renderCustomers();
    updateResultsCounter();
    
    // Save preference to localStorage
    localStorage.setItem('customersPerPage', value);
}

// Update results counter
function updateResultsCounter() {
    const resultsCounter = document.getElementById('customersResultsCounter');
    if (!resultsCounter) return;
    
    const total = customersData.length;
    
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

// Search customers
function searchCustomers(searchTerm) {
    currentSearchTerm = searchTerm.toLowerCase();
    applyFilters();
}

// Apply filters (search)
function applyFilters() {
    customersData = allCustomers.filter(customer => {
        // Search filter
        let searchMatch = true;
        if (currentSearchTerm !== '') {
            const fullName = `${customer.firstName} ${customer.lastName}`;
            const searchText = (fullName + ' ' + (customer.email || '') + ' ' + (customer.phone || '')).toLowerCase();
            searchMatch = searchText.includes(currentSearchTerm);
        }
        
        return searchMatch;
    });
    
    // Reset to first page when filters change
    currentPage = 1;
    updatePaginationState();
    renderCustomers();
    updateResultsCounter();
}

function toggleView(view) {
    currentView = view;
    localStorage.setItem('customersView', view);
    
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
    const savedPerPage = localStorage.getItem('customersPerPage');
    if (savedPerPage) {
        const perPageSelect = document.getElementById('perPageSelect');
        if (perPageSelect) {
            perPageSelect.value = savedPerPage;
            PAGE_SIZE = savedPerPage === 'all' ? Infinity : parseInt(savedPerPage);
        }
    }
    
    fetchCustomers().then(() => {
    toggleView(currentView);
    });
});

function openNewCustomerModal() {
    const modalContent = `
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">New Customer</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form onsubmit="saveCustomer(event)" class="space-y-4">
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
                        <input type="email" name="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" name="phone" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Address (optional)</label>
                    <input type="text" name="address" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">
                        Save Customer
                    </button>
                </div>
            </form>
        </div>
    `;
    openModal(modalContent);
}

function saveCustomer(event) {
    event.preventDefault();
    showSuccessMessage('Customer added successfully!');
    closeModal();
    setTimeout(() => location.reload(), 1500);
}

function openCustomerHistory(button) {
    const card = button.closest('.bg-white');
    const customerName = card.querySelector('h3').textContent;
    const modalContent = `
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">Service History - ${customerName}</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="space-y-3 max-h-96 overflow-y-auto">
                <div class="p-4 border border-gray-200 rounded-lg">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="font-medium text-gray-900">Classic Manicure + Gel Polish</p>
                            <p class="text-sm text-gray-500"><?php echo date('M d, Y', strtotime('-2 days')); ?></p>
                        </div>
                        <span class="text-lg font-bold text-gray-900">$55.00</span>
                    </div>
                    <p class="text-xs text-gray-500">Technician: Maria Garcia</p>
                </div>
                <div class="p-4 border border-gray-200 rounded-lg">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="font-medium text-gray-900">Spa Pedicure</p>
                            <p class="text-sm text-gray-500"><?php echo date('M d, Y', strtotime('-1 week')); ?></p>
                        </div>
                        <span class="text-lg font-bold text-gray-900">$55.00</span>
                    </div>
                    <p class="text-xs text-gray-500">Technician: Anna Kim</p>
                </div>
                <div class="p-4 border border-gray-200 rounded-lg">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <p class="font-medium text-gray-900">Gel Manicure</p>
                            <p class="text-sm text-gray-500"><?php echo date('M d, Y', strtotime('-2 weeks')); ?></p>
                        </div>
                        <span class="text-lg font-bold text-gray-900">$45.00</span>
                    </div>
                    <p class="text-xs text-gray-500">Technician: Lisa Wong</p>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-6">
                <button onclick="closeModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">
                    Close
                </button>
            </div>
        </div>
    `;
    openModal(modalContent);
}

function showSuccessMessage(message) {
    const successDiv = document.createElement('div');
    successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    successDiv.textContent = message;
    document.body.appendChild(successDiv);
    setTimeout(() => successDiv.remove(), 3000);
}
</script>

<?php include '../includes/footer.php'; ?>

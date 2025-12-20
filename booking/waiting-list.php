<?php
$pageTitle = 'Waiting List';
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/modal.php';
?>

<!-- Main Content Area -->
<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Waiting List</h1>
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
                <button onclick="openNewCustomerModal()" class="inline-flex items-center gap-2 px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm sm:text-base active:scale-95">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add New
                </button>
            </div>
        </div>

        <!-- Filter Tabs and Search Bar -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <!-- Filter Tabs -->
            <div class="flex items-center gap-6 border-b border-gray-200">
                <button onclick="filterByStatus('all')" id="filterAll" class="filter-tab px-1 py-3 text-sm font-medium text-gray-900 border-b-2 border-[#003047] transition">
                    All
                </button>
                <button onclick="filterByStatus('walk-in')" id="filterWalkIn" class="filter-tab px-1 py-3 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 transition">
                    Walk-In
                </button>
                <button onclick="filterByStatus('booked')" id="filterBooked" class="filter-tab px-1 py-3 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 transition">
                    Booked
                </button>
            </div>
            
            <!-- Search Bar -->
            <div class="relative max-w-md">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" id="customerSearchInput" placeholder="Search customers" oninput="searchCustomers(this.value)" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-base">
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
                            <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-16">#</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned Technicians</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Appointment</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
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
let allAppointments = [];
let allTechnicians = []; // Store technicians from users.json
let allMergedData = []; // All merged appointments with customers
let customersData = []; // Filtered customers for display (merged with appointments)
let PAGE_SIZE = 15; // Can be changed by user
let currentPage = 1;
let totalPages = 1;
let currentSearchTerm = '';
let currentStatusFilter = 'waiting'; // Default to 'waiting'
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

// Fetch customers and appointments from JSON
async function fetchCustomers() {
    try {
        // Fetch customers
        const customersResponse = await fetch('../json/customers.json');
        const customersData_json = await customersResponse.json();
        allCustomers = customersData_json.customers || [];
        
        // Fetch appointments
        const appointmentsResponse = await fetch('../json/appointments.json');
        const appointmentsData_json = await appointmentsResponse.json();
        allAppointments = appointmentsData_json.appointments || [];
        
        // Fetch technicians
        const techniciansResponse = await fetch('../json/users.json');
        const techniciansData_json = await techniciansResponse.json();
        allTechnicians = techniciansData_json.users.filter(user => user.role === 'technician' || user.userlevel === 'technician') || [];
        
        // Merge appointments with customers
        mergeAppointmentsWithCustomers();
        
        // Filter to only include appointments with "waiting" status
        allMergedData = allMergedData.filter(item => {
            const status = item.status ? item.status.toLowerCase() : '';
            return status === 'waiting';
        });
        
        // Get filter from URL parameter and set initial filter
        currentStatusFilter = getStatusFromURL();
        
        // Update URL if no status parameter exists
        if (!new URLSearchParams(window.location.search).get('status')) {
            const url = new URL(window.location);
            url.searchParams.set('status', currentStatusFilter);
            window.history.replaceState({}, '', url);
        }
        
        updateTabStates(currentStatusFilter);
        
        // Apply initial filters
        applyFilters();
        
        // Listen for browser back/forward button
        window.addEventListener('popstate', function(event) {
            currentStatusFilter = getStatusFromURL();
            updateTabStates(currentStatusFilter);
            applyFilters();
        });
    } catch (error) {
        console.error('Error fetching data:', error);
        showErrorMessage('Failed to load data');
    }
}

// Get technician initials
function getTechnicianInitials(technician) {
    if (technician.initials) return technician.initials;
    const first = technician.firstName ? technician.firstName[0] : '';
    const last = technician.lastName ? technician.lastName[0] : '';
    return (first + last).toUpperCase();
}

// Get technician names from IDs (for text display)
function getTechnicianNames(technicianIds) {
    if (!technicianIds || !Array.isArray(technicianIds) || technicianIds.length === 0) {
        return 'Not assigned';
    }
    
    const names = technicianIds.map(id => {
        const technician = allTechnicians.find(t => t.id === id);
        if (technician) {
            return `${technician.firstName} ${technician.lastName}`;
        }
        return null;
    }).filter(name => name !== null);
    
    return names.length > 0 ? names.join(', ') : 'Not assigned';
}

// Render technicians list with thumbnails
function renderTechniciansList(technicianIds) {
    if (!technicianIds || !Array.isArray(technicianIds) || technicianIds.length === 0) {
        return '<span class="text-sm text-gray-400">Not assigned</span>';
    }
    
    const technicians = technicianIds.map(id => {
        return allTechnicians.find(t => t.id === id);
    }).filter(t => t !== undefined);
    
    if (technicians.length === 0) {
        return '<span class="text-sm text-gray-400">Not assigned</span>';
    }
    
    const technicianItems = technicians.map((technician, index) => {
        const initials = getTechnicianInitials(technician);
        const fullName = `${technician.firstName} ${technician.lastName}`;
        const colorIndex = index % colorClasses.length;
        const color = colorClasses[colorIndex];
        
        // Check if technician has a profile photo (could be profilePhoto, avatar, image, etc.)
        const profilePhoto = technician.profilePhoto || technician.avatar || technician.image || technician.photo || null;
        
        return `
            <div class="flex items-center gap-2 mb-1 last:mb-0">
                ${profilePhoto ? `
                    <img src="${profilePhoto}" alt="${fullName}" class="w-8 h-8 rounded-full object-cover flex-shrink-0 border-2 border-white shadow-sm" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="w-8 h-8 ${color.bg} rounded-full flex items-center justify-center flex-shrink-0 hidden">
                        <span class="text-xs font-bold ${color.text}">${initials}</span>
                    </div>
                ` : `
                    <div class="w-8 h-8 ${color.bg} rounded-full flex items-center justify-center flex-shrink-0 border-2 border-white shadow-sm">
                        <span class="text-xs font-bold ${color.text}">${initials}</span>
                    </div>
                `}
                <span class="text-sm text-gray-900">${fullName}</span>
            </div>
        `;
    }).join('');
    
    return `<div class="flex flex-col">${technicianItems}</div>`;
}

// Merge appointments with customers to create waiting list items
function mergeAppointmentsWithCustomers() {
    allMergedData = allAppointments.map(appointment => {
        const customer = allCustomers.find(c => c.id === appointment.customer_id);
        if (!customer) {
            console.warn(`Customer not found for appointment ${appointment.id}`);
            return null;
        }
        
        // Format appointment type: capitalize first letter of each word
        const appointmentType = appointment.appointment === 'walk-in' ? 'Walk-In' : 
                                appointment.appointment === 'booked' ? 'Booked' : 
                                appointment.appointment || 'Walk-In';
        
        return {
            ...customer,
            appointmentId: appointment.id,
            appointment: appointmentType,
            status: appointment.status || 'waiting', // waiting | in-progress | completed | no-show
            created_at: appointment.created_at,
            assigned_technician: appointment.assigned_technician || [],
            services: appointment.services || []
        };
    }).filter(item => item !== null); // Remove null entries
}

// Format date for display
function formatDate(dateString) {
    if (!dateString) return '';
    try {
        const date = new Date(dateString);
        const options = { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        };
        return date.toLocaleString('en-US', options);
    } catch (error) {
        return dateString;
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
        const appointmentType = customer.appointment || 'Walk-In';
        const appointmentTypeLower = appointmentType.toLowerCase();
        const statusClass = appointmentTypeLower === 'walk-in' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700';
        const displayStatus = appointmentType;
        
        return `
            <div class="customer-card bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow flex flex-col h-full">
                <div class="flex-1">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-16 h-16 ${color.bg} rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-2xl font-bold ${color.text}">${initials}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-normal text-gray-900 text-xl truncate">${fullName}</h3>
                            <p class="text-sm text-gray-500">${customer.phone || ''}</p>
                            <div class="mt-2">${renderTechniciansList(customer.assigned_technician)}</div>
                        </div>
                    </div>
                </div>
                <div class="pt-4 border-t border-gray-200 mt-auto space-y-3">
                    <div class="space-y-2">
                        <span class="inline-block px-3 py-1 ${statusClass} text-xs font-medium rounded-full">${displayStatus}</span>
                    </div>
                    <button onclick="event.stopPropagation(); assignCustomer('${customer.id}', '${fullName.replace(/'/g, "\\'")}')" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Assign
                    </button>
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
                <td colspan="6" class="px-6 py-12 text-center">
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
        const appointmentType = customer.appointment || 'Walk-In';
        const appointmentTypeLower = appointmentType.toLowerCase();
        const statusClass = appointmentTypeLower === 'walk-in' ? 'bg-blue-100 text-blue-700' : 'bg-purple-100 text-purple-700';
        const displayStatus = appointmentType;
        const rowNumber = PAGE_SIZE === 'all' || PAGE_SIZE === Infinity 
            ? index + 1 
            : (currentPage - 1) * PAGE_SIZE + index + 1;
        
        return `
            <tr class="customer-row hover:bg-gray-50 transition">
                <td class="px-3 py-4 whitespace-nowrap text-center">
                    <div class="text-sm text-gray-600">${rowNumber}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="w-10 h-10 ${color.bg} rounded-full flex items-center justify-center flex-shrink-0 mr-3">
                            <span class="text-sm font-bold ${color.text}">${initials}</span>
                        </div>
                        <div>
                            <div class="text-base font-normal text-gray-900">${fullName}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-gray-900">${customer.phone || ''}</div>
                </td>
                <td class="px-6 py-4">
                    ${renderTechniciansList(customer.assigned_technician)}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-block px-3 py-1 ${statusClass} text-xs font-medium rounded-full">${displayStatus}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right">
                    <button onclick="event.stopPropagation(); assignCustomer('${customer.id}', '${fullName.replace(/'/g, "\\'")}')" class="inline-flex items-center gap-2 px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Assign
                    </button>
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

// Apply filters (search and appointment type)
function applyFilters() {
    // Start with all merged data (already filtered to "waiting" status only)
    let filteredData = [...allMergedData];
    
    // Apply appointment type filter (walk-in/booked) if not 'all'
    if (currentStatusFilter === 'walk-in') {
        filteredData = filteredData.filter(item => item.appointment && item.appointment.toLowerCase() === 'walk-in');
    } else if (currentStatusFilter === 'booked') {
        filteredData = filteredData.filter(item => item.appointment && item.appointment.toLowerCase() === 'booked');
    }
    // If 'all', don't filter by appointment type (show all waiting appointments)
    
    // Apply search filter
    if (currentSearchTerm !== '') {
        filteredData = filteredData.filter(customer => {
            const fullName = `${customer.firstName} ${customer.lastName}`;
            const searchText = (fullName + ' ' + (customer.email || '') + ' ' + (customer.phone || '')).toLowerCase();
            return searchText.includes(currentSearchTerm);
        });
    }
    
    // Sort: booked first, then walk-in, then by date (oldest first within each type)
    filteredData.sort((a, b) => {
        // Get appointment types (normalize to lowercase)
        const typeA = (a.appointment || '').toLowerCase();
        const typeB = (b.appointment || '').toLowerCase();
        
        // Sort by appointment type: booked comes before walk-in
        if (typeA === 'booked' && typeB !== 'booked') return -1;
        if (typeA !== 'booked' && typeB === 'booked') return 1;
        
        // If both are same type (or both are neither), sort by date (oldest first)
        const dateA = new Date(a.created_at);
        const dateB = new Date(b.created_at);
        return dateA - dateB;
    });
    
    customersData = filteredData;
    
    // Reset to first page when filters change
    currentPage = 1;
    updatePaginationState();
    renderCustomers();
    updateResultsCounter();
}

// Filter by status
function filterByStatus(status) {
    currentStatusFilter = status;
    
    // Update URL with filter parameter
    const url = new URL(window.location);
    if (status === 'all') {
        url.searchParams.set('status', 'all');
    } else if (status === 'walk-in') {
        url.searchParams.set('status', 'walk-in');
    } else if (status === 'booked') {
        url.searchParams.set('status', 'booked');
    }
    window.history.pushState({}, '', url);
    
    // Update tab active states
    updateTabStates(status);
    
    applyFilters();
}

// Update tab active states
function updateTabStates(status) {
    const tabs = ['All', 'WalkIn', 'Booked'];
    tabs.forEach(tab => {
        const tabId = `filter${tab}`;
        const tabElement = document.getElementById(tabId);
        if (tabElement) {
            if ((status === 'all' && tab === 'All') ||
                (status === 'walk-in' && tab === 'WalkIn') ||
                (status === 'booked' && tab === 'Booked')) {
                tabElement.classList.remove('text-gray-500', 'border-transparent');
                tabElement.classList.add('text-gray-900', 'border-[#003047]');
            } else {
                tabElement.classList.remove('text-gray-900', 'border-[#003047]');
                tabElement.classList.add('text-gray-500', 'border-transparent');
            }
        }
    });
}

// Get filter from URL parameter
function getStatusFromURL() {
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    if (status && ['all', 'walk-in', 'booked'].includes(status)) {
        return status;
    }
    return 'all'; // Default to all
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
    // Reset selected customer
    window.selectedCustomer = null;
    
    const modalContent = `
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">Add to Waiting List</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Customer Selection Section -->
            <div class="mb-6">
                <button type="button" 
                        id="addNewCustomerBtn"
                        onclick="openAddCustomerModal()" 
                        class="mb-4 w-full px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add New Customer
                </button>
                <p class="text-sm text-gray-600 font-medium mb-2">Select Customer</p>
                <div class="relative">
                    <div id="customerDropdown" class="relative">
                        <button type="button" 
                                id="customerDropdownBtn"
                                onclick="toggleCustomerDropdown()" 
                                class="w-full text-left pl-12 pr-12 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-base bg-white flex items-center justify-between">
                            <span id="customerDropdownText" class="text-gray-500">Search by name, phone, or email...</span>
                            <svg id="customerDropdownIcon" class="w-6 h-6 text-gray-400 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div class="absolute left-3 top-1/2 transform -translate-y-1/2 w-6 h-6 text-gray-400 pointer-events-none">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        
                        <!-- Dropdown Menu -->
                        <div id="customerDropdownMenu" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-200 overflow-y-auto hidden">
                            <div class="p-3 border-b border-gray-200 sticky top-0 bg-white">
                                <input type="text" 
                                       id="customerSearchInput" 
                                       placeholder="Search customers..." 
                                       class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-base"
                                       oninput="searchCustomersForWaitingList(this.value)"
                                       onclick="event.stopPropagation()">
                                <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none mt-1 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <div id="customerSearchResults" class="p-2 space-y-1"></div>
                        </div>
                    </div>
                </div>
                
                <!-- Selected Customer Display -->
                <div id="selectedCustomerDisplay" class="mt-3 hidden">
                    <div class="bg-[#e6f0f3] border border-[#b3d1d9] rounded-lg p-4 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-[#e6f0f3] rounded-full flex items-center justify-center flex-shrink-0">
                                <span id="selectedCustomerInitials" class="text-base font-bold text-[#003047]"></span>
                            </div>
                            <div>
                                <p id="selectedCustomerName" class="text-base font-semibold text-gray-900"></p>
                                <p id="selectedCustomerContact" class="text-sm text-gray-500"></p>
                            </div>
                        </div>
                        <button type="button" onclick="clearSelectedCustomer()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                <button type="button" onclick="closeModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">
                    Cancel
                </button>
                <button type="button" onclick="addToWaitingList()" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">
                    Add to Waiting List
                </button>
            </div>
        </div>
    `;
    openModal(modalContent);
}

function toggleCustomerDropdown() {
    const dropdownMenu = document.getElementById('customerDropdownMenu');
    const dropdownIcon = document.getElementById('customerDropdownIcon');
    
    if (dropdownMenu && dropdownMenu.classList.contains('hidden')) {
        dropdownMenu.classList.remove('hidden');
        if (dropdownIcon) dropdownIcon.classList.add('rotate-180');
        // Focus on search input when dropdown opens
        setTimeout(() => {
            const searchInput = document.getElementById('customerSearchInput');
            if (searchInput) {
                searchInput.focus();
                // Show all customers initially
                searchCustomersForWaitingList('');
            }
        }, 100);
    } else if (dropdownMenu) {
        dropdownMenu.classList.add('hidden');
        if (dropdownIcon) dropdownIcon.classList.remove('rotate-180');
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdown = document.getElementById('customerDropdown');
    const dropdownMenu = document.getElementById('customerDropdownMenu');
    
    if (dropdown && dropdownMenu && !dropdown.contains(event.target)) {
        dropdownMenu.classList.add('hidden');
        const dropdownIcon = document.getElementById('customerDropdownIcon');
        if (dropdownIcon) {
            dropdownIcon.classList.remove('rotate-180');
        }
    }
});

function searchCustomersForWaitingList(searchTerm) {
    const searchLower = searchTerm.toLowerCase().trim();
    const resultsDiv = document.getElementById('customerSearchResults');
    
    if (!resultsDiv || !allCustomers) return;
    
    // Filter customers by name, phone, or email
    let matchingCustomers = allCustomers;
    
    if (searchLower !== '') {
        matchingCustomers = allCustomers.filter(customer => {
            const fullName = `${customer.firstName} ${customer.lastName}`.toLowerCase();
            const phone = (customer.phone || '').toLowerCase();
            const email = (customer.email || '').toLowerCase();
            
            return fullName.includes(searchLower) || 
                   phone.includes(searchLower) || 
                   email.includes(searchLower);
        });
    }
    
    if (matchingCustomers.length === 0) {
        resultsDiv.innerHTML = `
            <div class="p-3 text-sm text-gray-500 text-center">
                No customer found.
            </div>
        `;
        return;
    }
    
    // Display matching customers
    let resultsHTML = '';
    matchingCustomers.forEach(customer => {
        const initials = getInitials(customer);
        resultsHTML += `
            <button type="button" 
                    onclick="selectCustomerForWaitingList(${customer.id}); event.stopPropagation();" 
                    class="w-full text-left px-4 py-3 hover:bg-gray-50 rounded-lg transition flex items-center gap-3">
                <div class="w-10 h-10 bg-[#e6f0f3] rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-sm font-bold text-[#003047]">${initials}</span>
                </div>
                <div class="flex-1">
                    <p class="text-base font-semibold text-gray-900">${customer.firstName} ${customer.lastName}</p>
                    <p class="text-sm text-gray-500">${customer.phone || ''}${customer.email ? ' • ' + customer.email : ''}</p>
                </div>
            </button>
        `;
    });
    
    resultsDiv.innerHTML = resultsHTML;
}

function selectCustomerForWaitingList(customerId) {
    const customer = allCustomers.find(c => c.id === customerId);
    if (!customer) return;
    
    // Store selected customer
    window.selectedCustomer = customer;
    
    // Close dropdown
    const dropdownMenu = document.getElementById('customerDropdownMenu');
    const dropdownIcon = document.getElementById('customerDropdownIcon');
    const dropdownText = document.getElementById('customerDropdownText');
    
    if (dropdownMenu) {
        dropdownMenu.classList.add('hidden');
    }
    if (dropdownIcon) {
        dropdownIcon.classList.remove('rotate-180');
    }
    
    // Update dropdown button text
    if (dropdownText) {
        dropdownText.textContent = `${customer.firstName} ${customer.lastName}`;
        dropdownText.classList.remove('text-gray-500');
        dropdownText.classList.add('text-gray-900', 'font-medium');
    }
    
    // Show selected customer display
    const selectedDisplay = document.getElementById('selectedCustomerDisplay');
    const initialsDiv = document.getElementById('selectedCustomerInitials');
    const nameDiv = document.getElementById('selectedCustomerName');
    const contactDiv = document.getElementById('selectedCustomerContact');
    
    if (selectedDisplay && initialsDiv && nameDiv && contactDiv) {
        const initials = getInitials(customer);
        initialsDiv.textContent = initials;
        nameDiv.textContent = `${customer.firstName} ${customer.lastName}`;
        contactDiv.textContent = `${customer.phone || ''}${customer.email ? ' • ' + customer.email : ''}`;
        
        selectedDisplay.classList.remove('hidden');
    }
    
    // Clear search input
    const searchInput = document.getElementById('customerSearchInput');
    if (searchInput) {
        searchInput.value = '';
    }
}

function clearSelectedCustomer() {
    window.selectedCustomer = null;
    
    // Hide selected customer display
    const selectedDisplay = document.getElementById('selectedCustomerDisplay');
    if (selectedDisplay) {
        selectedDisplay.classList.add('hidden');
    }
    
    // Reset dropdown button text
    const dropdownText = document.getElementById('customerDropdownText');
    if (dropdownText) {
        dropdownText.textContent = 'Search by name, phone, or email...';
        dropdownText.classList.add('text-gray-500');
        dropdownText.classList.remove('text-gray-900', 'font-medium');
    }
    
    // Clear search input
    const searchInput = document.getElementById('customerSearchInput');
    if (searchInput) {
        searchInput.value = '';
    }
}

function openAddCustomerModal() {
    // Close customer dropdown if open
    const dropdownMenu = document.getElementById('customerDropdownMenu');
    const dropdownIcon = document.getElementById('customerDropdownIcon');
    if (dropdownMenu) {
        dropdownMenu.classList.add('hidden');
    }
    if (dropdownIcon) {
        dropdownIcon.classList.remove('rotate-180');
    }
    
    const modalContent = `
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">Add New Customer</h3>
                <button onclick="closeAddCustomerModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form onsubmit="saveNewCustomer(event)" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                        <input type="text" id="newCustomerFirstName" name="first_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                        <input type="text" id="newCustomerLastName" name="last_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" id="newCustomerPhone" name="phone" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email (optional)</label>
                        <input type="email" id="newCustomerEmail" name="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="closeAddCustomerModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">
                        Save Customer
                    </button>
                </div>
            </form>
        </div>
    `;
    
    // Open the Add Customer modal
    const overlay = document.getElementById('addCustomerModalOverlay');
    const content = document.getElementById('addCustomerModalContent');
    if (overlay && content) {
        content.innerHTML = modalContent;
        overlay.classList.remove('hidden');
    }
}

function closeAddCustomerModal() {
    const overlay = document.getElementById('addCustomerModalOverlay');
    if (overlay) {
        overlay.classList.add('hidden');
    }
}

function saveNewCustomer(event) {
    event.preventDefault();
    
    const firstName = document.getElementById('newCustomerFirstName').value;
    const lastName = document.getElementById('newCustomerLastName').value;
    const phone = document.getElementById('newCustomerPhone').value;
    const email = document.getElementById('newCustomerEmail').value;
    
    // Generate new customer ID
    const newId = allCustomers.length > 0 ? Math.max(...allCustomers.map(c => c.id), 0) + 1 : 1;
    
    // Add new customer to the list
    const newCustomer = {
        id: newId,
        firstName: firstName,
        lastName: lastName,
        phone: phone || '',
        email: email || '',
        createdAt: new Date().toISOString().split('T')[0]
    };
    
    allCustomers.push(newCustomer);
    
    // Close the add customer modal
    closeAddCustomerModal();
    
    // Select the newly created customer in the parent modal
    selectCustomerForWaitingList(newId);
    
    showSuccessMessage('Customer added successfully!');
}

function addToWaitingList() {
    if (!window.selectedCustomer) {
        alert('Please select a customer first');
        return;
    }
    
    // Here you would save to waiting list/assign to a technician
    // For now, just show success message
    showSuccessMessage(`${window.selectedCustomer.firstName} ${window.selectedCustomer.lastName} added to waiting list successfully!`);
    closeModal();
    
    // Reset
    window.selectedCustomer = null;
    
    // Reload the page to refresh the list
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

// Global variables for assignment modal
let availableTechnicians = [];
let originalTechnicianOrder = []; // Store original order of technicians
let assignedTechnicianIds = [];
let currentCustomerId = null;
let currentCustomerName = '';
let technicianSearchTerm = '';
let startSessionEnabled = false; // Track Start Session toggle state
let selectedStatus = 'waiting'; // Track selected status in dropdown

function assignCustomer(customerId, customerName) {
    currentCustomerId = customerId;
    currentCustomerName = customerName;
    technicianSearchTerm = '';
    startSessionEnabled = false; // Reset toggle state
    selectedStatus = 'waiting'; // Reset status to waiting
    
    // Load existing assigned technicians from the appointment
    const customer = customersData.find(c => c.id.toString() === customerId.toString());
    if (customer && customer.assigned_technician && Array.isArray(customer.assigned_technician)) {
        // Convert to strings to match the format used in assignedTechnicianIds
        assignedTechnicianIds = customer.assigned_technician.map(id => id.toString());
    } else {
        assignedTechnicianIds = [];
    }
    
    // Check if status is "waiting" to show Start Session toggle
    const isWaitingStatus = customer && customer.status && customer.status.toLowerCase() === 'waiting';
    
    // Set selectedStatus based on current customer status (for in-progress/completed modals)
    if (!isWaitingStatus && customer && customer.status) {
        const statusLower = customer.status.toLowerCase();
        if (statusLower === 'in-progress' || statusLower === 'completed') {
            selectedStatus = statusLower;
        } else {
            selectedStatus = 'in-progress'; // Default to in-progress if status is not recognized
        }
    }
    
    // Open assign modal
    const modalContent = `
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-gray-900">Assign Technician to ${customerName}</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="grid grid-cols-2 gap-6">
                <!-- Available Technicians Section -->
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-sm font-semibold text-gray-900">Available Technicians</h4>
                        <span id="availableCount" class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">0</span>
                    </div>
                    <p class="text-xs text-gray-500 mb-2">Click to assign technicians to services</p>
                    <!-- Search Bar -->
                    <div class="mb-4">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input type="text" id="technicianSearchInput" placeholder="Search technicians..." oninput="searchTechnicians(this.value)" class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-sm">
                            <!-- Clear button -->
                            <button id="clearTechnicianSearchBtn" onclick="clearTechnicianSearch()" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div id="availableTechniciansContainer" class="space-y-3 min-h-[500px] max-h-[500px] overflow-y-auto">
                        <!-- Technicians will be loaded here -->
                    </div>
                </div>
                
                <!-- Assigned Technicians Section -->
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-sm font-semibold text-gray-900">Assigned Technicians</h4>
                        <span id="assignedCount" class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">0</span>
                    </div>
                    <p class="text-xs text-gray-500 mb-4">Click to remove assigned technicians</p>
                    <div id="assignedTechniciansContainer" class="space-y-3 min-h-[500px] max-h-[500px] overflow-y-auto">
                        <div class="flex items-center justify-center h-full min-h-[500px]">
                            <p class="text-sm text-gray-400">No technicians assigned</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Start Session Toggle - Only show for waiting status -->
            ${isWaitingStatus ? `
            <div class="pt-6 mt-6 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <button type="button" id="startSessionToggle" onclick="toggleStartSession()" disabled class="relative inline-flex h-7 w-14 items-center rounded-full bg-gray-200 transition-colors focus:outline-none focus:ring-2 focus:ring-[#003047] focus:ring-offset-2 opacity-50 cursor-not-allowed" role="switch" aria-checked="false">
                            <span id="startSessionToggleThumb" class="inline-block h-5 w-5 transform rounded-full bg-white transition-transform translate-x-1"></span>
                        </button>
                        <label for="startSessionToggle" class="text-base font-medium text-gray-400 cursor-not-allowed">
                            Start Session
                        </label>
                    </div>
                    <div class="flex gap-3">
                        <button onclick="closeModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">
                            Cancel
                        </button>
                        <button onclick="confirmAssign()" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">
                            Save Assignment
                        </button>
                    </div>
                </div>
            </div>
            ` : `
            <div class="pt-6 mt-6 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex-1 max-w-xs">
                        <div class="relative">
                            <button type="button" id="statusDropdownButton" onclick="toggleStatusDropdown()" class="w-full px-4 py-3 text-left bg-white border border-gray-300 rounded-lg shadow-sm hover:border-[#003047] focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-[#003047] transition-all flex items-center justify-between">
                                <span id="statusDropdownText" class="text-base text-gray-900">In Progress</span>
                                <svg id="statusDropdownIcon" class="w-5 h-5 text-gray-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div id="statusDropdownMenu" class="hidden absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg">
                                <div class="py-1">
                                    <button type="button" id="statusOptionWaiting" onclick="selectStatus('waiting')" class="w-full px-4 py-3 text-left text-base text-gray-900 hover:bg-gray-50 transition flex items-center gap-2">
                                        <span>Waiting</span>
                                    </button>
                                    <button type="button" id="statusOptionInProgress" onclick="selectStatus('in-progress')" class="w-full px-4 py-3 text-left text-base text-gray-900 hover:bg-gray-50 transition flex items-center gap-2">
                                        <span>In Progress</span>
                                    </button>
                                    <button type="button" id="statusOptionCompleted" onclick="selectStatus('completed')" class="w-full px-4 py-3 text-left text-base text-gray-900 hover:bg-gray-50 transition flex items-center gap-2">
                                        <span>Completed</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <button onclick="closeModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">
                            Cancel
                        </button>
                        <button onclick="confirmAssign()" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">
                            Save Assignment
                        </button>
                    </div>
                </div>
            </div>
            `}
        </div>
    `;
    openModal(modalContent, 'large', false);
    
    // Increase modal height for assign technician modal
    setTimeout(() => {
        const modalContainer = document.getElementById('modalContainer');
        if (modalContainer) {
            modalContainer.style.maxHeight = '95vh';
        }
    }, 50);
    
    // Load technicians
    loadTechniciansForAssign();
    
    // Initialize clear button visibility
    setTimeout(() => {
        const searchInput = document.getElementById('technicianSearchInput');
        const clearBtn = document.getElementById('clearTechnicianSearchBtn');
        if (searchInput && clearBtn) {
            // Hide clear button initially
            clearBtn.classList.add('hidden');
        }
    }, 100);
}

function loadTechniciansForAssign() {
    // Fetch technicians from users.json
    fetch('../json/users.json')
        .then(response => response.json())
        .then(data => {
            availableTechnicians = data.users.filter(user => user.role === 'technician' || user.userlevel === 'technician');
            // Store original order by ID for sorting
            originalTechnicianOrder = availableTechnicians.map(t => t.id);
            renderAvailableTechnicians();
            renderAssignedTechnicians();
            updateCounts();
            updateStartSessionToggleState();
            
            // Initialize status dropdown (only for in-progress/completed modals)
            const dropdownText = document.getElementById('statusDropdownText');
            if (dropdownText) {
                if (selectedStatus === 'waiting') {
                    dropdownText.textContent = 'Waiting';
                } else if (selectedStatus === 'in-progress') {
                    dropdownText.textContent = 'In Progress';
                } else if (selectedStatus === 'completed') {
                    dropdownText.textContent = 'Completed';
                } else {
                    dropdownText.textContent = 'In Progress'; // Default
                }
            }
            
            // Initialize status highlighting
            updateStatusHighlighting();
        })
        .catch(error => {
            console.error('Error loading technicians:', error);
            document.getElementById('availableTechniciansContainer').innerHTML = `
                <div class="text-center py-12">
                    <p class="text-sm text-gray-400">Error loading technicians</p>
                </div>
            `;
        });
}

function searchTechnicians(searchTerm) {
    technicianSearchTerm = searchTerm.toLowerCase().trim();
    
    // Show/hide clear button
    const clearBtn = document.getElementById('clearTechnicianSearchBtn');
    if (clearBtn) {
        if (searchTerm.trim() !== '') {
            clearBtn.classList.remove('hidden');
        } else {
            clearBtn.classList.add('hidden');
        }
    }
    
    renderAvailableTechnicians();
}

function clearTechnicianSearch() {
    const searchInput = document.getElementById('technicianSearchInput');
    const clearBtn = document.getElementById('clearTechnicianSearchBtn');
    
    if (searchInput) {
        searchInput.value = '';
        technicianSearchTerm = '';
        searchInput.focus();
    }
    
    if (clearBtn) {
        clearBtn.classList.add('hidden');
    }
    
    renderAvailableTechnicians();
}

function renderAvailableTechnicians() {
    const container = document.getElementById('availableTechniciansContainer');
    if (!container) return;
    
    if (availableTechnicians.length === 0) {
        container.innerHTML = `
            <div class="flex items-center justify-center h-full min-h-[500px]">
                <p class="text-sm text-gray-400">No technicians available</p>
            </div>
        `;
        return;
    }
    
    // Filter technicians based on search term
    let filteredTechnicians = availableTechnicians;
    if (technicianSearchTerm !== '') {
        filteredTechnicians = availableTechnicians.filter(technician => {
            const fullName = `${technician.firstName} ${technician.lastName}`.toLowerCase();
            const initials = (technician.initials || (technician.firstName?.[0] || '') + (technician.lastName?.[0] || '')).toLowerCase();
            const searchText = fullName + ' ' + initials;
            return searchText.includes(technicianSearchTerm);
        });
    }
    
    if (filteredTechnicians.length === 0) {
        container.innerHTML = `
            <div class="flex items-center justify-center h-full min-h-[500px]">
                <p class="text-sm text-gray-400">No technicians found</p>
            </div>
        `;
        return;
    }
    
    // Sort technicians: non-assigned first (in original order), assigned (grayed out) last (in original order)
    filteredTechnicians.sort((a, b) => {
        const aIdStr = a.id.toString();
        const bIdStr = b.id.toString();
        const aIsAssigned = assignedTechnicianIds.includes(aIdStr);
        const bIsAssigned = assignedTechnicianIds.includes(bIdStr);
        
        // If one is assigned and the other isn't, assigned goes last
        if (aIsAssigned && !bIsAssigned) return 1;
        if (!aIsAssigned && bIsAssigned) return -1;
        
        // If both have same assignment status, maintain original order
        const aIndex = originalTechnicianOrder.indexOf(a.id);
        const bIndex = originalTechnicianOrder.indexOf(b.id);
        return aIndex - bIndex;
    });
    
    let html = '';
    filteredTechnicians.forEach(technician => {
        const technicianIdStr = technician.id.toString();
        const isAssigned = assignedTechnicianIds.includes(technicianIdStr);
        const initials = technician.initials || (technician.firstName?.[0] || '') + (technician.lastName?.[0] || '');
        const fullName = `${technician.firstName} ${technician.lastName}`;
        
        // Determine classes based on assignment status
        const containerClasses = isAssigned 
            ? "flex items-center gap-3 p-2 rounded-lg transition-colors opacity-50 grayscale cursor-pointer group hover:bg-gray-100"
            : "flex items-center gap-3 cursor-pointer group hover:bg-gray-50 p-2 rounded-lg transition-colors";
        
        const avatarClasses = isAssigned
            ? "w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center"
            : "w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center";
        
        const initialClasses = isAssigned
            ? "text-sm font-bold text-gray-500"
            : "text-sm font-bold text-gray-600";
        
        const nameClasses = isAssigned
            ? "text-base font-medium text-gray-400"
            : "text-base font-medium text-gray-900";
        
        const badgeClasses = isAssigned
            ? "absolute -bottom-1 -right-1 w-5 h-5 bg-gray-400 text-white rounded-full flex items-center justify-center text-xs font-bold border-2 border-white"
            : "absolute -bottom-1 -right-1 w-5 h-5 bg-[#003047] text-white rounded-full flex items-center justify-center text-xs font-bold border-2 border-white";
        
        html += `
            <div onclick="${isAssigned ? 'removeAssignedTechnician(' + technician.id + ')' : 'assignTechnician(' + technician.id + ')'}" class="${containerClasses}">
                <!-- Avatar with badge -->
                <div class="relative flex-shrink-0">
                    <div class="${avatarClasses}">
                        <span class="${initialClasses}">${initials}</span>
                    </div>
                    <!-- Badge at bottom right -->
                    <div class="${badgeClasses}">
                        0
                    </div>
                </div>
                <!-- Name -->
                <div class="flex-1">
                    <p class="${nameClasses}">${fullName}</p>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

function renderAssignedTechnicians() {
    const container = document.getElementById('assignedTechniciansContainer');
    if (!container) return;
    
    if (assignedTechnicianIds.length === 0) {
        container.innerHTML = `
            <div class="flex items-center justify-center h-full min-h-[500px]">
                <p class="text-sm text-gray-400">No technicians assigned</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    assignedTechnicianIds.forEach(technicianIdStr => {
        const technician = availableTechnicians.find(t => t.id.toString() === technicianIdStr);
        if (!technician) return;
        
        const initials = technician.initials || (technician.firstName?.[0] || '') + (technician.lastName?.[0] || '');
        const fullName = `${technician.firstName} ${technician.lastName}`;
        
        html += `
            <div onclick="removeAssignedTechnician(${technician.id})" class="flex items-center gap-3 cursor-pointer group hover:bg-gray-50 p-2 rounded-lg transition-colors">
                <!-- Avatar with badge -->
                <div class="relative flex-shrink-0">
                    <div class="w-12 h-12 bg-[#003047] rounded-full flex items-center justify-center">
                        <span class="text-sm font-bold text-white">${initials}</span>
                    </div>
                    <!-- Badge at bottom right -->
                    <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-[#003047] text-white rounded-full flex items-center justify-center text-xs font-bold border-2 border-white">
                        0
                    </div>
                </div>
                <!-- Name -->
                <div class="flex-1">
                    <p class="text-base font-medium text-gray-900">${fullName}</p>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

function assignTechnician(technicianId) {
    const technicianIdStr = technicianId.toString();
    if (!assignedTechnicianIds.includes(technicianIdStr)) {
        assignedTechnicianIds.push(technicianIdStr);
        renderAvailableTechnicians();
        renderAssignedTechnicians();
        updateCounts();
        updateStartSessionToggleState();
    }
}

function removeAssignedTechnician(technicianId) {
    const technicianIdStr = technicianId.toString();
    assignedTechnicianIds = assignedTechnicianIds.filter(id => id !== technicianIdStr);
    renderAvailableTechnicians();
    renderAssignedTechnicians();
    updateCounts();
    updateStartSessionToggleState();
}

function updateCounts() {
    const availableCountEl = document.getElementById('availableCount');
    const assignedCountEl = document.getElementById('assignedCount');
    
    if (availableCountEl) {
        // Show total count since all technicians are displayed (some grayed out)
        availableCountEl.textContent = availableTechnicians.length;
    }
    
    if (assignedCountEl) {
        assignedCountEl.textContent = assignedTechnicianIds.length;
    }
}

// Update Start Session toggle state based on assigned technicians
function updateStartSessionToggleState() {
    const toggle = document.getElementById('startSessionToggle');
    const label = document.querySelector('label[for="startSessionToggle"]');
    
    if (!toggle) return;
    
    const hasAssignedTechnicians = assignedTechnicianIds.length > 0;
    
    if (hasAssignedTechnicians) {
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
        // Reset toggle to off if disabled
        startSessionEnabled = false;
        const thumb = document.getElementById('startSessionToggleThumb');
        if (thumb) {
            toggle.classList.remove('bg-[#003047]');
            toggle.classList.add('bg-gray-200');
            toggle.setAttribute('aria-checked', 'false');
            thumb.classList.remove('translate-x-8');
            thumb.classList.add('translate-x-1');
        }
    }
}

// Toggle Start Session
function toggleStartSession() {
    if (assignedTechnicianIds.length === 0) {
        return; // Don't allow toggling if no technicians assigned
    }
    
    startSessionEnabled = !startSessionEnabled;
    const toggle = document.getElementById('startSessionToggle');
    const thumb = document.getElementById('startSessionToggleThumb');
    
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
}

// Toggle Status Dropdown
function toggleStatusDropdown() {
    const dropdownMenu = document.getElementById('statusDropdownMenu');
    const dropdownIcon = document.getElementById('statusDropdownIcon');
    
    if (dropdownMenu) {
        const isHidden = dropdownMenu.classList.contains('hidden');
        if (isHidden) {
            dropdownMenu.classList.remove('hidden');
            if (dropdownIcon) {
                dropdownIcon.classList.add('rotate-180');
            }
            // Update highlighting when dropdown opens
            updateStatusHighlighting();
        } else {
            dropdownMenu.classList.add('hidden');
            if (dropdownIcon) {
                dropdownIcon.classList.remove('rotate-180');
            }
        }
    }
}

// Update Status Highlighting in Dropdown
function updateStatusHighlighting() {
    // Remove active class from all status options
    const waitingBtn = document.getElementById('statusOptionWaiting');
    const inProgressBtn = document.getElementById('statusOptionInProgress');
    const completedBtn = document.getElementById('statusOptionCompleted');
    
    // Reset all buttons to default state
    [waitingBtn, inProgressBtn, completedBtn].forEach(btn => {
        if (btn) {
            btn.className = 'w-full px-4 py-3 text-left text-base text-gray-900 hover:bg-gray-50 transition flex items-center gap-2';
        }
    });
    
    // Add active class to selected status
    if (selectedStatus === 'waiting' && waitingBtn) {
        waitingBtn.className = 'w-full px-4 py-3 text-left text-base bg-[#003047] text-white hover:bg-[#002535] transition flex items-center gap-2';
    } else if (selectedStatus === 'in-progress' && inProgressBtn) {
        inProgressBtn.className = 'w-full px-4 py-3 text-left text-base bg-[#003047] text-white hover:bg-[#002535] transition flex items-center gap-2';
    } else if (selectedStatus === 'completed' && completedBtn) {
        completedBtn.className = 'w-full px-4 py-3 text-left text-base bg-[#003047] text-white hover:bg-[#002535] transition flex items-center gap-2';
    }
}

// Select Status from Dropdown
function selectStatus(status) {
    selectedStatus = status;
    const dropdownText = document.getElementById('statusDropdownText');
    const dropdownMenu = document.getElementById('statusDropdownMenu');
    const dropdownIcon = document.getElementById('statusDropdownIcon');
    
    if (dropdownText) {
        if (status === 'waiting') {
            dropdownText.textContent = 'Waiting';
        } else if (status === 'in-progress') {
            dropdownText.textContent = 'In Progress';
        } else if (status === 'completed') {
            dropdownText.textContent = 'Completed';
        }
    }
    
    // Update highlighting
    updateStatusHighlighting();
    
    if (dropdownMenu) {
        dropdownMenu.classList.add('hidden');
    }
    
    if (dropdownIcon) {
        dropdownIcon.classList.remove('rotate-180');
    }
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    const dropdownButton = document.getElementById('statusDropdownButton');
    const dropdownMenu = document.getElementById('statusDropdownMenu');
    
    if (dropdownButton && dropdownMenu && !dropdownButton.contains(event.target) && !dropdownMenu.contains(event.target)) {
        dropdownMenu.classList.add('hidden');
        const dropdownIcon = document.getElementById('statusDropdownIcon');
        if (dropdownIcon) {
            dropdownIcon.classList.remove('rotate-180');
        }
    }
});

function confirmAssign() {
    if (assignedTechnicianIds.length === 0) {
        alert('Please assign at least one technician');
        return;
    }
    
    const assignedTechnicianNames = assignedTechnicianIds.map(id => {
        const tech = availableTechnicians.find(t => t.id.toString() === id);
        return tech ? `${tech.firstName} ${tech.lastName}` : '';
    }).filter(Boolean);
    
    // Here you would save the assignment to your backend/JSON
    // Include startSessionEnabled state if needed
    const message = startSessionEnabled 
        ? `${currentCustomerName} assigned to ${assignedTechnicianNames.join(', ')} and session started!`
        : `${currentCustomerName} assigned to ${assignedTechnicianNames.join(', ')} successfully!`;
    
    showSuccessMessage(message);
    closeModal();
    
    // Reset
    assignedTechnicianIds = [];
    currentCustomerId = null;
    currentCustomerName = '';
    startSessionEnabled = false;
    
    // You can reload or update the UI here
    // setTimeout(() => location.reload(), 1500);
}
</script>

<!-- Add Customer Modal Overlay (separate from main modal, appears on top) -->
<div id="addCustomerModalOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-[60] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <div id="addCustomerModalContent">
            <!-- Add Customer modal content will be inserted here -->
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>


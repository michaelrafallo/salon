<?php
$pageTitle = 'Tickets';
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/modal.php';
?>

<!-- Main Content Area -->
<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Tickets</h1>
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
                <!-- Create Ticket Button -->
                <button onclick="openCreateTicketModal()" class="px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm sm:text-base active:scale-95 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create Ticket
                </button>
            </div>
        </div>

        <!-- Filter Tabs and Search Bar -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <!-- Filter Tabs -->
            <div class="flex items-center gap-6 border-b border-gray-200">
                <button onclick="filterByStatus('unpaid')" id="filterUnpaid" class="filter-tab px-1 py-3 text-sm font-medium text-gray-900 border-b-2 border-[#003047] transition">
                    Unpaid
                </button>
                <button onclick="filterByStatus('paid')" id="filterPaid" class="filter-tab px-1 py-3 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 transition">
                    Paid
                </button>
                <button onclick="filterByStatus('cancelled')" id="filterCancelled" class="filter-tab px-1 py-3 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 transition">
                    Cancelled
                </button>
                <button onclick="filterByStatus('refunded')" id="filterRefunded" class="filter-tab px-1 py-3 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 transition">
                    Refunded
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned Technicians</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Appointment</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Details</th>
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
let allPayments = []; // Store payments from payments.json
let allMergedData = []; // All merged appointments with customers
let customersData = []; // Filtered customers for display (merged with appointments)
let PAGE_SIZE = 15; // Can be changed by user
let currentPage = 1;
let totalPages = 1;
let currentSearchTerm = '';
let currentStatusFilter = 'unpaid'; // Default to 'unpaid'
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
        
        // Fetch payments
        try {
            const paymentsResponse = await fetch('../json/payments.json');
            const paymentsData_json = await paymentsResponse.json();
            allPayments = paymentsData_json.payments || [];
        } catch (error) {
            console.error('Error fetching payments:', error);
            allPayments = [];
        }
        
        // Merge appointments with customers
        mergeAppointmentsWithCustomers();
        
        // Filter appointments - Tickets page shows unpaid, paid, cancelled, and refunded
        // Keep all appointments that match any of the ticket statuses
        allMergedData = allMergedData.filter(item => {
            if (!item || !item.status) {
                return false;
            }
            const status = item.status.toLowerCase().trim();
            // Unpaid includes: unpaid, in-progress, completed, waiting
            // Paid includes: paid
            // Cancelled includes: cancelled, canceled
            // Refunded includes: refunded, closed (with refunded payments)
            const validStatuses = ['unpaid', 'in-progress', 'completed', 'waiting', 'paid', 'cancelled', 'canceled', 'refunded', 'closed'];
            return validStatuses.includes(status);
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
        // Convert both to numbers for comparison to handle string/number mismatches
        const techId = typeof id === 'string' ? parseInt(id, 10) : id;
        const technician = allTechnicians.find(t => {
            const tId = typeof t.id === 'string' ? parseInt(t.id, 10) : t.id;
            return tId === techId;
        });
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
    
    // Debug: Log technician IDs and available technicians
    if (allTechnicians.length === 0) {
        console.warn('No technicians loaded. Available technician IDs:', technicianIds);
        return '<span class="text-sm text-gray-400">Not assigned</span>';
    }
    
    const technicians = technicianIds.map(id => {
        // Convert both to numbers for comparison to handle string/number mismatches
        const techId = typeof id === 'string' ? parseInt(id, 10) : id;
        const technician = allTechnicians.find(t => {
            const tId = typeof t.id === 'string' ? parseInt(t.id, 10) : t.id;
            return tId === techId;
        });
        if (!technician) {
            console.warn(`Technician with ID ${techId} not found. Available technician IDs:`, allTechnicians.map(t => t.id));
        }
        return technician;
    }).filter(t => t !== undefined);
    
    if (technicians.length === 0) {
        console.warn('No technicians matched. Looking for IDs:', technicianIds, 'Available:', allTechnicians.map(t => t.id));
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
        
        // Find payment for this appointment (match by appointmentId or customer_id)
        const payment = allPayments.find(p => {
            // Try to match by appointmentId if payment has it
            if (p.appointmentId && p.appointmentId.toString() === appointment.id.toString()) {
                return true;
            }
            // Try to match by customer name (fallback)
            const customerName = `${customer.firstName} ${customer.lastName}`;
            if (p.customerName === customerName) {
                return true;
            }
            return false;
        });
        
        return {
            ...customer,
            appointmentId: appointment.id,
            id: appointment.id, // Also include id for compatibility
            appointment: appointmentType,
            status: appointment.status || 'waiting', // waiting | unpaid | paid | cancelled | refunded | closed
            created_at: appointment.created_at,
            appointment_datetime: appointment.appointment_datetime || appointment.created_at,
            assigned_technician: appointment.assigned_technician || [],
            services: appointment.services || [],
            payment: payment || null,
            customer_id: appointment.customer_id // Include customer_id for lookup
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

// Parse date string to Date object (handles multiple formats)
function parseDate(dateString) {
    if (!dateString) return null;
    
    try {
        // Try standard Date parsing first
        let date = new Date(dateString);
        
        // Check if date is valid
        if (!isNaN(date.getTime())) {
            return date;
        }
        
        // Try parsing ISO format with 'T' separator if it has a space
        if (dateString.includes(' ') && !dateString.includes('T')) {
            date = new Date(dateString.replace(' ', 'T'));
            if (!isNaN(date.getTime())) {
                return date;
            }
        }
        
        // Try parsing date parts manually for format: YYYY-MM-DD HH:MM:SS or YYYY-MM-DDTHH:MM:SS
        const dateTimeMatch = dateString.match(/(\d{4})-(\d{2})-(\d{2})[T\s]?(\d{2}):(\d{2}):?(\d{2})?/);
        if (dateTimeMatch) {
            const [, year, month, day, hour, minute, second] = dateTimeMatch;
            date = new Date(
                parseInt(year), 
                parseInt(month) - 1, 
                parseInt(day), 
                parseInt(hour) || 0, 
                parseInt(minute) || 0, 
                parseInt(second) || 0
            );
            if (!isNaN(date.getTime())) {
                return date;
            }
        }
        
        // Try format: YYYY-MM-DD
        const dateMatch = dateString.match(/(\d{4})-(\d{2})-(\d{2})/);
        if (dateMatch) {
            const [, year, month, day] = dateMatch;
            date = new Date(parseInt(year), parseInt(month) - 1, parseInt(day));
            if (!isNaN(date.getTime())) {
                return date;
            }
        }
        
        return null;
    } catch (error) {
        console.error('Error parsing date:', dateString, error);
        return null;
    }
}

// Get time started in 09:00 AM format
function getTimeStarted(customer) {
    const startTime = customer.appointment_datetime || customer.created_at;
    if (!startTime) return 'N/A';
    
    const date = parseDate(startTime);
    if (!date) return 'N/A';
    
    try {
        return date.toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit',
            hour12: true 
        });
    } catch (error) {
        return 'N/A';
    }
}

// Calculate duration from start time to now in HH:MM:SS format
function calculateDuration(startTime) {
    if (!startTime) return '00:00:00';
    
    const start = parseDate(startTime);
    if (!start || isNaN(start.getTime())) {
        return '00:00:00';
    }
    
    try {
        const now = new Date();
        const diff = Math.max(0, Math.floor((now - start) / 1000)); // Difference in seconds
        
        const hours = Math.floor(diff / 3600);
        const minutes = Math.floor((diff % 3600) / 60);
        const seconds = diff % 60;
        
        return `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
    } catch (error) {
        console.error('Error calculating duration:', error);
        return '00:00:00';
    }
}

// Update all duration counters
function updateDurationCounters() {
    const counters = document.querySelectorAll('.duration-counter');
    counters.forEach(counter => {
        const startTime = counter.getAttribute('data-start-time');
        if (startTime && startTime !== 'null' && startTime !== 'undefined' && startTime.trim() !== '') {
            const duration = calculateDuration(startTime);
            // Only update if the value changed to avoid unnecessary DOM updates
            if (counter.textContent !== duration) {
                counter.textContent = duration;
            }
        } else {
            // If no valid start time, show default
            if (counter.textContent !== '00:00:00') {
                counter.textContent = '00:00:00';
            }
        }
    });
}

// Start the duration counter interval
let durationInterval = null;
function startDurationCounters() {
    // Clear existing interval if any
    if (durationInterval) {
        clearInterval(durationInterval);
    }
    
    // Update immediately
    updateDurationCounters();
    
    // Update every second
    durationInterval = setInterval(updateDurationCounters, 1000);
}

// Stop the duration counter interval
function stopDurationCounters() {
    if (durationInterval) {
        clearInterval(durationInterval);
        durationInterval = null;
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

// Get action buttons based on status
function getActionButtons(customer, fullName, isGrid = false) {
    const status = customer.status ? customer.status.toLowerCase() : '';
    const appointmentId = customer.appointmentId || customer.id;
    const escapedName = fullName.replace(/'/g, "\\'");
    
    // For paid, cancelled, and refunded status, show View button only
    if (status === 'paid' || status === 'cancelled' || status === 'canceled' || status === 'refunded' || status === 'closed') {
        const buttonClass = isGrid 
            ? 'flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm active:scale-95'
            : 'inline-flex items-center gap-2 px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm active:scale-95';
        
        return `
            <button onclick="event.stopPropagation(); viewAppointmentDetails('${appointmentId}', '${escapedName}')" class="${buttonClass}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                View
            </button>
        `;
    }
    
    // For other statuses, show Assign and Pay buttons
    const assignButtonClass = isGrid
        ? 'flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm active:scale-95'
        : 'inline-flex items-center gap-2 px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm active:scale-95';
    
    const payButtonClass = isGrid
        ? 'flex-1 inline-flex items-center justify-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium text-sm active:scale-95'
        : 'inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium text-sm active:scale-95';
    
    return `
        <button onclick="event.stopPropagation(); assignCustomer('${customer.id}', '${escapedName}')" class="${assignButtonClass}">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Assign
        </button>
        <button onclick="event.stopPropagation(); window.location.href='pay.php?id=${appointmentId}'" class="${payButtonClass}">
            Pay
        </button>
    `;
}

// View appointment details modal for paid, cancelled, and refunded appointments
// Open create ticket page
function openCreateTicketModal() {
    window.location.href = 'create-ticket.php';
}

function viewAppointmentDetails(appointmentId, customerName) {
    // Find the appointment - try multiple ways to match
    const appointment = allMergedData.find(apt => {
        if (apt.appointmentId && apt.appointmentId.toString() === appointmentId.toString()) {
            return true;
        }
        if (apt.id && apt.id.toString() === appointmentId.toString()) {
            return true;
        }
        return false;
    });
    
    if (!appointment) {
        alert('Appointment not found. Please try again.');
        return;
    }
    
    // Get customer details
    // The merged data already contains customer info spread into it
    const customerId = appointment.customer_id;
    const customer = customerId ? allCustomers.find(c => c.id.toString() === customerId.toString()) : null;
    // Use customer data from merged appointment (which has customer info spread) or from allCustomers
    const fullName = customer ? `${customer.firstName} ${customer.lastName}` : (appointment.firstName && appointment.lastName ? `${appointment.firstName} ${appointment.lastName}` : customerName);
    const customerPhone = customer ? (customer.phone || 'No phone') : (appointment.phone || 'No phone');
    const customerEmail = customer ? (customer.email || 'No email') : (appointment.email || 'No email');
    
    // Get payment details
    const payment = appointment.payment || null;
    const paymentAmount = payment ? `$${parseFloat(payment.amount).toFixed(2)}` : '$0.00';
    const paymentMethod = payment ? payment.method : 'N/A';
    const paymentStatus = payment ? payment.status : 'N/A';
    const paymentDate = payment ? payment.date : 'N/A';
    
    // Get appointment datetime
    const aptDateTime = appointment.appointment_datetime || appointment.created_at;
    const aptDate = new Date(aptDateTime);
    const appointmentDate = aptDate.toLocaleDateString('en-US', { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' });
    const appointmentTime = aptDate.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    
    // Get services
    const services = appointment.services || [];
    const servicesList = services.length > 0 
        ? services.map(s => {
            const serviceName = s.service ? s.service.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'Unknown';
            return `<div class="text-sm text-gray-700">${serviceName}</div>`;
        }).join('')
        : '<div class="text-sm text-gray-400">No services</div>';
    
    // Get assigned technicians
    const technicians = appointment.assigned_technician || [];
    const techniciansList = technicians.length > 0
        ? technicians.map(techId => {
            const technician = allTechnicians.find(t => t.id.toString() === techId.toString());
            return technician ? `${technician.firstName} ${technician.lastName}` : `Technician #${techId}`;
        }).join(', ')
        : 'Not assigned';
    
    // Get appointment type
    const appointmentType = appointment.appointment || 'walk-in';
    const appointmentTypeDisplay = appointmentType === 'walk-in' ? 'Walk-In' : 'Booked';
    
    // Status display
    const status = appointment.status || 'unpaid';
    const statusDisplay = status.charAt(0).toUpperCase() + status.slice(1);
    const statusColors = {
        'paid': 'bg-green-100 text-green-700',
        'unpaid': 'bg-yellow-100 text-yellow-700',
        'waiting': 'bg-blue-100 text-blue-700',
        'closed': 'bg-gray-100 text-gray-700'
    };
    const statusClass = statusColors[status] || 'bg-gray-100 text-gray-700';
    
    // Get appointment ID
    const aptId = appointment.appointmentId || appointment.id || 'N/A';
    
    // Get created date
    const createdDate = appointment.created_at ? new Date(appointment.created_at).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }) : 'N/A';
    
    // Get transaction ID from payment
    const transactionId = payment ? (payment.id || payment.bookingId || 'N/A') : 'N/A';
    
    // Group services by technician
    const servicesByTechnician = {};
    
    if (services.length > 0) {
        services.forEach(s => {
            const serviceName = s.service ? s.service.replace('-', ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'Unknown';
            const techId = s.technician_id;
            
            let techName = 'Not Assigned';
            if (techId) {
                const technician = allTechnicians.find(t => t.id.toString() === techId.toString());
                techName = technician ? `${technician.firstName} ${technician.lastName}` : `Technician #${techId}`;
            }
            
            if (!servicesByTechnician[techName]) {
                servicesByTechnician[techName] = [];
            }
            servicesByTechnician[techName].push(serviceName);
        });
    }
    
    // Build enhanced services list grouped by technician
    const enhancedServicesList = Object.keys(servicesByTechnician).length > 0
        ? Object.keys(servicesByTechnician).map(techName => {
            const techServices = servicesByTechnician[techName];
            return `
                <div class="mb-4 last:mb-0">
                    <div class="mb-2">
                        <h5 class="text-sm font-semibold text-gray-900">${techName}</h5>
                    </div>
                    <div class="space-y-1 ml-4">
                        ${techServices.map(serviceName => `
                            <div class="text-sm text-gray-700 pl-3 border-l-2 border-gray-200">${serviceName}</div>
                        `).join('')}
                    </div>
                </div>
            `;
        }).join('')
        : '<div class="text-sm text-gray-400 p-2">No services</div>';
    
    const modalContent = `
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Ticket Details</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="space-y-2">
                <!-- Ticket Information -->
                <div class="bg-gray-50 rounded-xl p-4">
                    <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Ticket Information</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Ticket ID</p>
                            <p class="text-base font-semibold text-gray-900">#${aptId}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Status</p>
                            <span class="inline-block px-3 py-1 ${statusClass} text-xs font-medium rounded-full">${statusDisplay}</span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Appointment Type</p>
                            <p class="text-base font-semibold text-gray-900">${appointmentTypeDisplay}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Created Date</p>
                            <p class="text-base font-semibold text-gray-900">${createdDate}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Customer Information -->
                <div class="bg-gray-50 rounded-xl p-4">
                    <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Customer Information</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Name</p>
                            <p class="text-base font-semibold text-gray-900">${fullName}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Phone</p>
                            <p class="text-base font-semibold text-gray-900">${customerPhone}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Email</p>
                            <p class="text-base font-semibold text-gray-900">${customerEmail}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Customer ID</p>
                            <p class="text-base font-semibold text-gray-900">#${appointment.customer_id || 'N/A'}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Appointment Details -->
                <div class="bg-gray-50 rounded-xl p-4">
                    <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Appointment Details</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Date</p>
                            <p class="text-base font-semibold text-gray-900">${appointmentDate}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Time</p>
                            <p class="text-base font-semibold text-gray-900">${appointmentTime}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Assigned Technicians</p>
                            <p class="text-base font-semibold text-gray-900">${techniciansList}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Services -->
                <div class="bg-gray-50 rounded-xl p-4">
                    <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Services</h4>
                    <div class="space-y-2">
                        ${enhancedServicesList}
                    </div>
                </div>
                
                ${status !== 'cancelled' && status !== 'canceled' ? `
                <!-- Payment Information -->
                <div class="bg-gray-50 rounded-xl p-4">
                    <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Payment Information</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Amount</p>
                            <p class="text-lg font-bold text-gray-900">${paymentAmount}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Payment Method</p>
                            <p class="text-base font-semibold text-gray-900">${paymentMethod}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Payment Status</p>
                            <span class="inline-block px-3 py-1 ${paymentStatus === 'Completed' ? 'bg-green-100 text-green-700' : paymentStatus === 'Refunded' ? 'bg-gray-100 text-gray-700' : paymentStatus === 'Failed' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700'} text-xs font-medium rounded-full">${paymentStatus}</span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Payment Date</p>
                            <p class="text-base font-semibold text-gray-900">${paymentDate}</p>
                        </div>
                        ${transactionId !== 'N/A' ? `
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Transaction ID</p>
                            <p class="text-base font-semibold text-gray-900">${transactionId}</p>
                        </div>
                        ` : ''}
                    </div>
                    ${status === 'paid' ? `
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <button onclick="confirmRefund('${aptId}', '${fullName.replace(/'/g, "\\'")}', '${paymentAmount}')" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium text-sm active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                            </svg>
                            Refund
                        </button>
                    </div>
                    ` : ''}
                </div>
                ` : ''}
                
                ${status === 'cancelled' || status === 'canceled' ? `
                <!-- Restore Button for Cancelled Tickets -->
                <div class="bg-gray-50 rounded-xl p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-1">Ticket Status</h4>
                            <p class="text-base text-gray-700">This ticket has been cancelled</p>
                        </div>
                        <button onclick="confirmRestore('${aptId}', '${fullName.replace(/'/g, "\\'")}')" class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium text-sm active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Restore
                        </button>
                    </div>
                </div>
                ` : ''}
            </div>
            
            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-6 mt-6 border-t border-gray-200">
                <button onclick="closeModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">
                    Close
                </button>
            </div>
        </div>
    `;
    
    openModal(modalContent, 'medium');
}

// Confirm and process refund
function confirmRefund(appointmentId, customerName, amount) {
    // Parse amount (remove $ and convert to number)
    const amountValue = parseFloat(amount.replace('$', '').replace(',', ''));
    
    // Find the appointment to get more details
    const appointment = allMergedData.find(apt => 
        (apt.appointmentId && apt.appointmentId.toString() === appointmentId.toString()) || 
        (apt.id && apt.id.toString() === appointmentId.toString())
    );
    
    if (!appointment) {
        alert('Appointment not found.');
        return;
    }
    
    const payment = appointment.payment || null;
    const paymentMethod = payment ? payment.method : 'N/A';
    const transactionId = payment ? (payment.id || payment.bookingId || 'N/A') : 'N/A';
    
    const refundModalContent = `
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">Confirm Refund</h3>
                </div>
                <button onclick="closeRefundModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="space-y-6">
                <!-- Warning Message -->
                <div class="bg-amber-50 border-l-4 border-amber-400 p-4 rounded-r-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-amber-400 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-amber-800">This action cannot be undone</p>
                            <p class="text-sm text-amber-700 mt-1">Once processed, the refund will be permanent and the payment status will be updated.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Refund Details -->
                <div class="bg-gray-50 rounded-xl p-5">
                    <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Refund Details</h4>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between py-2 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Customer Name</span>
                            <span class="text-base font-semibold text-gray-900">${customerName}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Refund Amount</span>
                            <span class="text-lg font-bold text-red-600">$${amountValue.toFixed(2)}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Payment Method</span>
                            <span class="text-base font-semibold text-gray-900">${paymentMethod}</span>
                        </div>
                        ${transactionId !== 'N/A' ? `
                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm text-gray-600">Transaction ID</span>
                            <span class="text-base font-semibold text-gray-900">${transactionId}</span>
                        </div>
                        ` : ''}
                    </div>
                </div>
                
                <!-- What happens next -->
                <div class="bg-blue-50 rounded-xl p-4 border border-blue-200">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-blue-900">What happens next?</p>
                            <ul class="text-sm text-blue-800 mt-2 space-y-1 list-disc list-inside">
                                <li>The appointment status will be updated to "Refunded"</li>
                                <li>The payment status will be marked as "Refunded"</li>
                                <li>The ticket will move to the "Refunded" tab</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-6 mt-6 border-t border-gray-200">
                <button onclick="closeRefundModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">
                    Cancel
                </button>
                <button onclick="processRefund('${appointmentId}', '${customerName.replace(/'/g, "\\'")}', ${amountValue}); closeRefundModal();" class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium active:scale-95 inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                    </svg>
                    Confirm Refund
                </button>
            </div>
        </div>
    `;
    
    openRefundModal(refundModalContent);
}

// Open refund confirmation modal (with higher z-index)
function openRefundModal(content) {
    // Create refund modal overlay if it doesn't exist
    let refundOverlay = document.getElementById('refundModalOverlay');
    if (!refundOverlay) {
        refundOverlay = document.createElement('div');
        refundOverlay.id = 'refundModalOverlay';
        refundOverlay.className = 'fixed inset-0 bg-black bg-opacity-50 z-[60] hidden flex items-center justify-center p-2 sm:p-4 backdrop-blur-sm transition-opacity duration-300';
        document.body.appendChild(refundOverlay);
        
        const refundContainer = document.createElement('div');
        refundContainer.id = 'refundModalContainer';
        refundContainer.className = 'bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] transform transition-all duration-300 scale-95';
        refundContainer.onclick = (e) => e.stopPropagation();
        
        const refundContent = document.createElement('div');
        refundContent.id = 'refundModalContent';
        refundContainer.appendChild(refundContent);
        refundOverlay.appendChild(refundContainer);
    }
    
    const refundContent = document.getElementById('refundModalContent');
    const refundContainer = document.getElementById('refundModalContainer');
    
    refundContent.innerHTML = content;
    refundOverlay.classList.remove('hidden');
    
    // Animate modal in
    setTimeout(() => {
        refundContainer.classList.remove('scale-95');
        refundContainer.classList.add('scale-100');
    }, 10);
}

// Close refund modal
function closeRefundModal() {
    const refundOverlay = document.getElementById('refundModalOverlay');
    const refundContainer = document.getElementById('refundModalContainer');
    
    if (refundContainer) {
        refundContainer.classList.remove('scale-100');
        refundContainer.classList.add('scale-95');
    }
    
    setTimeout(() => {
        if (refundOverlay) {
            refundOverlay.classList.add('hidden');
        }
    }, 200);
}

// Close refund modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const refundOverlay = document.getElementById('refundModalOverlay');
        if (refundOverlay && !refundOverlay.classList.contains('hidden')) {
            closeRefundModal();
            e.stopPropagation(); // Prevent closing the main modal
        }
    }
});

// Process refund
function processRefund(appointmentId, customerName, amount) {
    // Find the appointment
    const appointment = allMergedData.find(apt => 
        (apt.appointmentId && apt.appointmentId.toString() === appointmentId.toString()) || 
        (apt.id && apt.id.toString() === appointmentId.toString())
    );
    
    if (!appointment) {
        alert('Appointment not found.');
        return;
    }
    
    // Update appointment status to refunded
    // Note: In a real application, this would make an API call to update the database
    // For now, we'll update the local data and show a success message
    
    // Find the appointment in allAppointments
    const appointmentIndex = allAppointments.findIndex(apt => apt.id.toString() === appointmentId.toString());
    if (appointmentIndex !== -1) {
        allAppointments[appointmentIndex].status = 'refunded';
    }
    
    // Update payment status if payment exists
    if (appointment.payment) {
        const paymentIndex = allPayments.findIndex(p => p.id === appointment.payment.id);
        if (paymentIndex !== -1) {
            allPayments[paymentIndex].status = 'Refunded';
            allPayments[paymentIndex].statusColor = 'bg-gray-100';
            allPayments[paymentIndex].statusTextColor = 'text-gray-700';
        }
    }
    
    // Re-merge data
    mergeAppointmentsWithCustomers();
    
    // Filter appointments - Tickets page shows unpaid, paid, cancelled, and refunded
    allMergedData = allMergedData.filter(item => {
        if (!item || !item.status) {
            return false;
        }
        const status = item.status.toLowerCase().trim();
        const validStatuses = ['in-progress', 'completed', 'waiting', 'paid', 'cancelled', 'canceled', 'refunded', 'closed'];
        return validStatuses.includes(status);
    });
    
    // Update the current filter if needed
    applyFilters();
    
    // Close the ticket details modal
    closeModal();
    
    // Show success message
    showSuccessMessage(`Refund of $${amount.toFixed(2)} processed successfully for ${customerName}. The ticket has been moved to the "Refunded" tab.`);
    
    // Note: In a production environment, you would also:
    // 1. Make an API call to update the database
    // 2. Update the payments.json file
    // 3. Update the appointments.json file
    // 4. Handle any payment gateway refunds
}

// Confirm and process restore for cancelled tickets
function confirmRestore(appointmentId, customerName) {
    // Find the appointment to get more details
    const appointment = allMergedData.find(apt => 
        (apt.appointmentId && apt.appointmentId.toString() === appointmentId.toString()) || 
        (apt.id && apt.id.toString() === appointmentId.toString())
    );
    
    if (!appointment) {
        alert('Appointment not found.');
        return;
    }
    
    const restoreModalContent = `
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">Confirm Restore</h3>
                </div>
                <button onclick="closeRefundModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="space-y-6">
                <!-- Info Message -->
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-blue-800">Restore Cancelled Ticket</p>
                            <p class="text-sm text-blue-700 mt-1">This will restore the cancelled ticket and make it available for processing again.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Ticket Details -->
                <div class="bg-gray-50 rounded-xl p-5">
                    <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Ticket Details</h4>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between py-2 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Customer Name</span>
                            <span class="text-base font-semibold text-gray-900">${customerName}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Ticket ID</span>
                            <span class="text-base font-semibold text-gray-900">#${appointmentId}</span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm text-gray-600">Current Status</span>
                            <span class="inline-block px-3 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded-full">Cancelled</span>
                        </div>
                    </div>
                </div>
                
                <!-- What happens next -->
                <div class="bg-green-50 rounded-xl p-4 border border-green-200">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-green-900">What happens next?</p>
                            <ul class="text-sm text-green-800 mt-2 space-y-1 list-disc list-inside">
                                <li>The appointment status will be updated to "Unpaid"</li>
                                <li>The ticket will move to the "Unpaid" tab</li>
                                <li>The ticket will be available for processing again</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-6 mt-6 border-t border-gray-200">
                <button onclick="closeRefundModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">
                    Cancel
                </button>
                <button onclick="processRestore('${appointmentId}', '${customerName.replace(/'/g, "\\'")}'); closeRefundModal();" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium active:scale-95 inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Confirm Restore
                </button>
            </div>
        </div>
    `;
    
    openRefundModal(restoreModalContent);
}

// Process restore for cancelled tickets
function processRestore(appointmentId, customerName) {
    // Find the appointment
    const appointment = allMergedData.find(apt => 
        (apt.appointmentId && apt.appointmentId.toString() === appointmentId.toString()) || 
        (apt.id && apt.id.toString() === appointmentId.toString())
    );
    
    if (!appointment) {
        alert('Appointment not found.');
        return;
    }
    
    // Update appointment status to unpaid
    // Note: In a real application, this would make an API call to update the database
    // For now, we'll update the local data and show a success message
    
    // Find the appointment in allAppointments
    const appointmentIndex = allAppointments.findIndex(apt => apt.id.toString() === appointmentId.toString());
    if (appointmentIndex !== -1) {
        allAppointments[appointmentIndex].status = 'unpaid';
    }
    
    // Re-merge data
    mergeAppointmentsWithCustomers();
    
    // Filter appointments - Tickets page shows unpaid, paid, cancelled, and refunded
    allMergedData = allMergedData.filter(item => {
        if (!item || !item.status) {
            return false;
        }
        const status = item.status.toLowerCase().trim();
        const validStatuses = ['in-progress', 'completed', 'waiting', 'paid', 'cancelled', 'canceled', 'refunded', 'closed'];
        return validStatuses.includes(status);
    });
    
    // Update the current filter to unpaid
    currentStatusFilter = 'unpaid';
    updateTabStates('unpaid');
    
    // Update URL
    const url = new URL(window.location);
    url.searchParams.set('status', 'unpaid');
    window.history.pushState({}, '', url);
    
    // Update the current filter if needed
    applyFilters();
    
    // Close the ticket details modal
    closeModal();
    
    // Show success message
    showSuccessMessage(`Ticket #${appointmentId} has been restored successfully for ${customerName}. The ticket has been moved to the "Unpaid" tab.`);
    
    // Note: In a production environment, you would also:
    // 1. Make an API call to update the database
    // 2. Update the appointments.json file
}

// Confirm and process restore for cancelled tickets
function confirmRestore(appointmentId, customerName) {
    // Find the appointment to get more details
    const appointment = allMergedData.find(apt => 
        (apt.appointmentId && apt.appointmentId.toString() === appointmentId.toString()) || 
        (apt.id && apt.id.toString() === appointmentId.toString())
    );
    
    if (!appointment) {
        alert('Appointment not found.');
        return;
    }
    
    const restoreModalContent = `
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900">Confirm Restore</h3>
                </div>
                <button onclick="closeRefundModal()" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="space-y-6">
                <!-- Info Message -->
                <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-blue-400 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-blue-800">Restore Cancelled Ticket</p>
                            <p class="text-sm text-blue-700 mt-1">This will restore the cancelled ticket and make it available for processing again.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Ticket Details -->
                <div class="bg-gray-50 rounded-xl p-5">
                    <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-4">Ticket Details</h4>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between py-2 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Customer Name</span>
                            <span class="text-base font-semibold text-gray-900">${customerName}</span>
                        </div>
                        <div class="flex items-center justify-between py-2 border-b border-gray-200">
                            <span class="text-sm text-gray-600">Ticket ID</span>
                            <span class="text-base font-semibold text-gray-900">#${appointmentId}</span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm text-gray-600">Current Status</span>
                            <span class="inline-block px-3 py-1 bg-gray-100 text-gray-700 text-xs font-medium rounded-full">Cancelled</span>
                        </div>
                    </div>
                </div>
                
                <!-- What happens next -->
                <div class="bg-green-50 rounded-xl p-4 border border-green-200">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-green-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        <div>
                            <p class="text-sm font-medium text-green-900">What happens next?</p>
                            <ul class="text-sm text-green-800 mt-2 space-y-1 list-disc list-inside">
                                <li>The appointment status will be updated to "Unpaid"</li>
                                <li>The ticket will move to the "Unpaid" tab</li>
                                <li>The ticket will be available for processing again</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-6 mt-6 border-t border-gray-200">
                <button onclick="closeRefundModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">
                    Cancel
                </button>
                <button onclick="processRestore('${appointmentId}', '${customerName.replace(/'/g, "\\'")}'); closeRefundModal();" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium active:scale-95 inline-flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Confirm Restore
                </button>
            </div>
        </div>
    `;
    
    openRefundModal(restoreModalContent);
}

// Process restore for cancelled tickets
function processRestore(appointmentId, customerName) {
    // Find the appointment
    const appointment = allMergedData.find(apt => 
        (apt.appointmentId && apt.appointmentId.toString() === appointmentId.toString()) || 
        (apt.id && apt.id.toString() === appointmentId.toString())
    );
    
    if (!appointment) {
        alert('Appointment not found.');
        return;
    }
    
    // Update appointment status to unpaid
    // Note: In a real application, this would make an API call to update the database
    // For now, we'll update the local data and show a success message
    
    // Find the appointment in allAppointments
    const appointmentIndex = allAppointments.findIndex(apt => apt.id.toString() === appointmentId.toString());
    if (appointmentIndex !== -1) {
        allAppointments[appointmentIndex].status = 'unpaid';
    }
    
    // Re-merge data
    mergeAppointmentsWithCustomers();
    
    // Filter appointments - Tickets page shows unpaid, paid, cancelled, and refunded
    allMergedData = allMergedData.filter(item => {
        if (!item || !item.status) {
            return false;
        }
        const status = item.status.toLowerCase().trim();
        const validStatuses = ['in-progress', 'completed', 'waiting', 'paid', 'cancelled', 'canceled', 'refunded', 'closed'];
        return validStatuses.includes(status);
    });
    
    // Update the current filter to unpaid
    currentStatusFilter = 'unpaid';
    updateTabStates('unpaid');
    
    // Update URL
    const url = new URL(window.location);
    url.searchParams.set('status', 'unpaid');
    window.history.pushState({}, '', url);
    
    // Update the current filter if needed
    applyFilters();
    
    // Close the ticket details modal
    closeModal();
    
    // Show success message
    showSuccessMessage(`Ticket #${appointmentId} has been restored successfully for ${customerName}. The ticket has been moved to the "Unpaid" tab.`);
    
    // Note: In a production environment, you would also:
    // 1. Make an API call to update the database
    // 2. Update the appointments.json file
}

// Get payment details for display
function getPaymentDetails(customer) {
    if (!customer.payment) {
        return '<span class="text-sm text-gray-400">No payment</span>';
    }
    
    const payment = customer.payment;
    const amount = payment.amount ? `$${parseFloat(payment.amount).toFixed(2)}` : '$0.00';
    const method = payment.method || 'N/A';
    let status = payment.status || 'Pending';
    
    // In unpaid section, change "Completed" status to "Payment Failed"
    if (currentStatusFilter === 'unpaid' && status === 'Completed') {
        status = 'Payment Failed';
    }
    
    // Status color classes
    const statusColors = {
        'Completed': 'bg-green-100 text-green-700',
        'Pending': 'bg-yellow-100 text-yellow-700',
        'Failed': 'bg-red-100 text-red-700',
        'Payment Failed': 'bg-red-100 text-red-700',
        'Refunded': 'bg-gray-100 text-gray-700'
    };
    const statusClass = statusColors[status] || 'bg-gray-100 text-gray-700';
    
    return `
        <div class="space-y-1">
            <div class="text-sm font-semibold text-gray-900">${amount}</div>
            <div class="text-xs text-gray-600">${method}</div>
            <span class="inline-block px-2 py-0.5 ${statusClass} text-xs font-medium rounded">${status}</span>
        </div>
    `;
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
                <p class="text-gray-500 text-sm">No tickets found</p>
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
                    <div class="flex gap-2">
                        ${getActionButtons(customer, fullName, true)}
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
                <td colspan="7" class="px-6 py-12 text-center">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <p class="text-gray-500 text-sm">No tickets found</p>
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
                    <div class="flex flex-col gap-1">
                        <div class="text-sm text-gray-900">
                            ${getTimeStarted(customer)}
                        </div>
                        <div class="text-base font-bold text-[#003047] duration-counter" data-start-time="${(customer.appointment_datetime || customer.created_at || '').toString()}" data-customer-id="${customer.id || customer.appointmentId || ''}">
                            ${calculateDuration(customer.appointment_datetime || customer.created_at)}
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    ${renderTechniciansList(customer.assigned_technician)}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-block px-3 py-1 ${statusClass} text-xs font-medium rounded-full">${displayStatus}</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    ${getPaymentDetails(customer)}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right">
                    <div class="flex items-center justify-end gap-2">
                        ${getActionButtons(customer, fullName)}
                    </div>
                </td>
            </tr>
        `;
    }).join('');
    
    // Start duration counters after rendering
    setTimeout(() => {
        startDurationCounters();
    }, 100);
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

// Apply filters (search and status)
function applyFilters() {
    // Start with all merged data (already merged in mergeAppointmentsWithCustomers)
    let filteredData = [...allMergedData];
    
    // Filter by status (unpaid | paid | cancelled | refunded)
    // Unpaid includes: waiting, in-progress, completed (with or without payments)
    // Paid includes: paid
    // Cancelled includes: cancelled appointments
    // Refunded includes: refunded appointments (check payment status)

    if (currentStatusFilter === 'unpaid') {
        filteredData = filteredData.filter(item => {
            if (!item || !item.status) return false;
            const status = item.status.toLowerCase().trim();
            // Show appointments with unpaid status
            return status === 'unpaid';
        });
    } else if (currentStatusFilter === 'paid') {
        filteredData = filteredData.filter(item => {
            if (!item || !item.status) return false;
            return item.status.toLowerCase().trim() === 'paid';
        });
    } else if (currentStatusFilter === 'cancelled') {
        filteredData = filteredData.filter(item => {
            const status = item.status ? item.status.toLowerCase().trim() : '';
            return status == 'cancelled';
        });
    } else if (currentStatusFilter === 'refunded') {
        filteredData = filteredData.filter(item => {
            const status = item.status ? item.status.toLowerCase().trim() : '';
            // Check if appointment status is refunded
            if (status == 'refunded') {
                return true;
            }
            // Check if payment status is Refunded
            if (item.payment && item.payment.status === 'Refunded') {
                return true;
            }
            // Check if status is closed and has refunded payment
            if (status === 'closed' && item.payment && item.payment.status === 'Refunded') {
                return true;
            }
            return false;
        });
    }
    
    // Apply search filter
    if (currentSearchTerm !== '') {
        filteredData = filteredData.filter(customer => {
            const fullName = `${customer.firstName} ${customer.lastName}`;
            const searchText = (fullName + ' ' + (customer.email || '') + ' ' + (customer.phone || '')).toLowerCase();
            return searchText.includes(currentSearchTerm);
        });
    }
    
    // Sort by date (oldest first - as per user's earlier change)
    filteredData.sort((a, b) => {
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
    if (status === 'unpaid') {
        url.searchParams.set('status', 'unpaid');
    } else if (status === 'paid') {
        url.searchParams.set('status', 'paid');
    } else if (status === 'cancelled') {
        url.searchParams.set('status', 'cancelled');
    } else if (status === 'refunded') {
        url.searchParams.set('status', 'refunded');
    }
    window.history.pushState({}, '', url);
    
    // Update tab active states
    updateTabStates(status);
    
    applyFilters();
}

// Update tab active states
function updateTabStates(status) {
    const unpaidTab = document.getElementById('filterUnpaid');
    const paidTab = document.getElementById('filterPaid');
    const cancelledTab = document.getElementById('filterCancelled');
    const refundedTab = document.getElementById('filterRefunded');
    
    if (unpaidTab) {
        if (status === 'unpaid') {
            unpaidTab.classList.remove('text-gray-500', 'border-transparent');
            unpaidTab.classList.add('text-gray-900', 'border-[#003047]');
            } else {
            unpaidTab.classList.remove('text-gray-900', 'border-[#003047]');
            unpaidTab.classList.add('text-gray-500', 'border-transparent');
        }
    }
    
    if (paidTab) {
        if (status === 'paid') {
            paidTab.classList.remove('text-gray-500', 'border-transparent');
            paidTab.classList.add('text-gray-900', 'border-[#003047]');
        } else {
            paidTab.classList.remove('text-gray-900', 'border-[#003047]');
            paidTab.classList.add('text-gray-500', 'border-transparent');
        }
    }
    
    if (cancelledTab) {
        if (status === 'cancelled') {
            cancelledTab.classList.remove('text-gray-500', 'border-transparent');
            cancelledTab.classList.add('text-gray-900', 'border-[#003047]');
        } else {
            cancelledTab.classList.remove('text-gray-900', 'border-[#003047]');
            cancelledTab.classList.add('text-gray-500', 'border-transparent');
            }
        }
    
    if (refundedTab) {
        if (status === 'refunded') {
            refundedTab.classList.remove('text-gray-500', 'border-transparent');
            refundedTab.classList.add('text-gray-900', 'border-[#003047]');
        } else {
            refundedTab.classList.remove('text-gray-900', 'border-[#003047]');
            refundedTab.classList.add('text-gray-500', 'border-transparent');
        }
    }
}

// Get filter from URL parameter
function getStatusFromURL() {
    const urlParams = new URLSearchParams(window.location.search);
    const status = urlParams.get('status');
    if (status && ['unpaid', 'paid', 'cancelled', 'refunded'].includes(status)) {
        return status;
    }
    return 'unpaid'; // Default to unpaid
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

function assignCustomer(customerId, customerName) {
    currentCustomerId = customerId;
    currentCustomerName = customerName;
    technicianSearchTerm = '';
    
    // Load existing assigned technicians from the appointment
    const customer = customersData.find(c => c.id.toString() === customerId.toString());
    if (customer && customer.assigned_technician && Array.isArray(customer.assigned_technician)) {
        // Convert to strings to match the format used in assignedTechnicianIds
        assignedTechnicianIds = customer.assigned_technician.map(id => id.toString());
    } else {
        assignedTechnicianIds = [];
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
            
            <div class="pt-6 mt-6 border-t border-gray-200">
                <div class="flex items-center justify-end">
                    <div class="flex gap-3">
                        <button onclick="closeModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">
                            Cancel
                        </button>
                        <button onclick="confirmAssign()" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">
                            Save Changes
                        </button>
                    </div>
                </div>
            </div>
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
    }
}

function removeAssignedTechnician(technicianId) {
    const technicianIdStr = technicianId.toString();
    assignedTechnicianIds = assignedTechnicianIds.filter(id => id !== technicianIdStr);
    renderAvailableTechnicians();
    renderAssignedTechnicians();
    updateCounts();
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
    const message = `${currentCustomerName} assigned to ${assignedTechnicianNames.join(', ')} successfully!`;
    
    showSuccessMessage(message);
    closeModal();
    
    // Reset
    assignedTechnicianIds = [];
    currentCustomerId = null;
    currentCustomerName = '';
    
    // You can reload or update the UI here
    // setTimeout(() => location.reload(), 1500);
}
</script>

<style>
/* Duration counter styles */
.duration-counter {
    font-weight: 400;
    font-size: 14px;
    transition: all 0.3s ease;
}
</style>

<!-- Add Customer Modal Overlay (separate from main modal, appears on top) -->
<div id="addCustomerModalOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-[60] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <div id="addCustomerModalContent">
            <!-- Add Customer modal content will be inserted here -->
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>


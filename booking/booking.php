<?php
$pageTitle = 'New Booking';
include '../includes/header.php';
include '../includes/modal.php';
?>

<!-- Main Content Area -->
<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-6">
                <a href="calendar.php" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    <span class="text-sm font-medium">Go to Calendar</span>
                </a>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">New Booking</h1>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-[30%_70%] gap-6">
            <!-- Column 1: Select Customer | Booking Date & Time -->
            <div class="space-y-2">
                <!-- Select Customer Section -->
                <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-end mb-2">
                        <button onclick="openAddNewCustomerModal()" class="px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm active:scale-95 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add New Customer
                        </button>
                    </div>
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
                            <div id="customerDropdownMenu" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-[400px] overflow-y-auto hidden">
                                <div class="p-3 border-b border-gray-200 sticky top-0 bg-white">
                                    <input type="text" 
                                           id="customerSearchInput" 
                                           placeholder="Search customers..." 
                                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-base"
                                           oninput="searchCustomers(this.value)"
                                           onclick="event.stopPropagation()">
                                    <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none mt-1 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                <div id="customerSearchResults" class="p-2 space-y-1"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Selected Customer Display -->
                <div id="selectedCustomerDisplay" class="hidden">
                    <div class="bg-[#e6f0f3] border border-[#b3d1d9] rounded-lg p-4 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-[#003047] rounded-full flex items-center justify-center flex-shrink-0">
                                <span id="selectedCustomerInitials" class="text-base font-bold text-white"></span>
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
                
                <!-- Booking Date & Time -->
                <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <p class="text-sm text-gray-600 font-medium mb-3">Select Date & Time</p>
                    <div id="appointmentCalendar" class="w-full mb-4">
                        <!-- Calendar will be rendered here -->
                    </div>
                </div>
                
                <!-- Preferred Time -->
                <div class="bg-white border border-gray-200 rounded-lg p-4 flex flex-col">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Preferred Time</h3>
                    <div id="availableTimeSlots" class="space-y-4 overflow-y-auto flex-1 min-h-0 max-h-[400px]">
                        <p class="text-sm text-gray-500 text-center py-4">Please select a date to view available time slots</p>
                    </div>
                </div>
            </div>
            
            <!-- Column 2: Assign Technician -->
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <div class="grid grid-cols-2 gap-4 h-full">
                    <!-- Available Technicians Section -->
                    <div class="border-r border-gray-200 pr-4">
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
                                <button id="clearTechnicianSearchBtn" onclick="clearTechnicianSearch()" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div id="availableTechniciansContainer" class="space-y-2 min-h-[400px] max-h-[75vh] overflow-y-auto">
                            <!-- Technicians will be loaded here -->
                        </div>
                    </div>
                    
                    <!-- Assigned Technicians Section -->
                    <div class="pl-4">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-sm font-semibold text-gray-900">Assigned Technicians</h4>
                            <span id="assignedCount" class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">0</span>
                        </div>
                        <p class="text-xs text-gray-500 mb-4">Click to remove assigned technicians</p>
                        <div id="assignedTechniciansContainer" class="space-y-2 min-h-[400px] max-h-[400px] overflow-y-auto">
                            <div class="flex items-center justify-center h-full min-h-[400px]">
                                <p class="text-sm text-gray-400">No technicians assigned</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Padding for floating footer -->
        <div class="h-20"></div>
    </div>
</main>

<!-- Floating Footer: Save Booking Button -->
<div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-40">
    <div class="px-4 py-3">
        <div class="flex justify-end">
            <button onclick="saveBooking()" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95 flex items-center gap-2">
                <span>Save Booking</span>
            </button>
        </div>
    </div>
</div>

<script>
// Global variables
let allCustomers = [];
let selectedCustomer = null;
let selectedAppointmentDate = null;
let selectedAppointmentTime = null;

// Calendar variables
let currentCalendarMonth = new Date().getMonth();
let currentCalendarYear = new Date().getFullYear();

// Technician variables
let availableTechnicians = [];
let assignedTechnicianIds = [];
let technicianSearchTerm = '';

// Fetch customers
async function fetchCustomers() {
    try {
        const response = await fetch('../json/customers.json');
        const data = await response.json();
        allCustomers = data.customers || [];
    } catch (error) {
        console.error('Error fetching customers:', error);
        allCustomers = [];
    }
}

// Customer dropdown functions
function toggleCustomerDropdown() {
    const dropdownMenu = document.getElementById('customerDropdownMenu');
    const dropdownIcon = document.getElementById('customerDropdownIcon');
    
    if (dropdownMenu && dropdownMenu.classList.contains('hidden')) {
        dropdownMenu.classList.remove('hidden');
        if (dropdownIcon) dropdownIcon.classList.add('rotate-180');
        setTimeout(() => {
            const searchInput = document.getElementById('customerSearchInput');
            if (searchInput) {
                searchInput.focus();
                searchCustomers('');
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

function searchCustomers(searchTerm) {
    const searchLower = searchTerm.toLowerCase().trim();
    const resultsDiv = document.getElementById('customerSearchResults');
    
    if (!resultsDiv || !allCustomers) return;
    
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
    
    let resultsHTML = '';
    matchingCustomers.forEach(customer => {
        const initials = getInitials(customer);
        resultsHTML += `
            <button type="button" 
                    onclick="selectCustomer(${customer.id}); event.stopPropagation();" 
                    class="w-full text-left px-4 py-3 hover:bg-gray-50 rounded-lg transition flex items-center gap-3">
                <div class="w-10 h-10 bg-[#e6f0f3] rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-sm font-bold text-[#003047]">${initials}</span>
                </div>
                <div class="flex-1">
                    <p class="text-base font-semibold text-gray-900">${customer.firstName} ${customer.lastName}</p>
                    <p class="text-sm text-gray-500">${customer.phone || 'No phone'} ${customer.email ? '• ' + customer.email : ''}</p>
                </div>
            </button>
        `;
    });
    
    resultsDiv.innerHTML = resultsHTML;
}

function getInitials(customer) {
    const first = customer.firstName ? customer.firstName[0].toUpperCase() : '';
    const last = customer.lastName ? customer.lastName[0].toUpperCase() : '';
    return (first + last).substring(0, 2);
}

function selectCustomer(customerId) {
    const customer = allCustomers.find(c => c.id === customerId);
    if (!customer) return;
    
    selectedCustomer = customer;
    
    // Update dropdown button
    const dropdownText = document.getElementById('customerDropdownText');
    if (dropdownText) {
        dropdownText.textContent = `${customer.firstName} ${customer.lastName}`;
        dropdownText.classList.remove('text-gray-500');
        dropdownText.classList.add('text-gray-900', 'font-medium');
    }
    
    // Show selected customer display
    const selectedDisplay = document.getElementById('selectedCustomerDisplay');
    const selectedName = document.getElementById('selectedCustomerName');
    const selectedContact = document.getElementById('selectedCustomerContact');
    const selectedInitials = document.getElementById('selectedCustomerInitials');
    
    if (selectedDisplay) selectedDisplay.classList.remove('hidden');
    if (selectedName) selectedName.textContent = `${customer.firstName} ${customer.lastName}`;
    if (selectedContact) {
        const contactParts = [];
        if (customer.phone) contactParts.push(customer.phone);
        if (customer.email) contactParts.push(customer.email);
        selectedContact.textContent = contactParts.join(' • ') || 'No contact information';
    }
    if (selectedInitials) selectedInitials.textContent = getInitials(customer);
    
    // Close dropdown
    toggleCustomerDropdown();
}

function clearSelectedCustomer() {
    selectedCustomer = null;
    
    const dropdownText = document.getElementById('customerDropdownText');
    if (dropdownText) {
        dropdownText.textContent = 'Search by name, phone, or email...';
        dropdownText.classList.remove('text-gray-900', 'font-medium');
        dropdownText.classList.add('text-gray-500');
    }
    
    const selectedDisplay = document.getElementById('selectedCustomerDisplay');
    if (selectedDisplay) selectedDisplay.classList.add('hidden');
}

function openAddNewCustomerModal() {
    const modalContent = `
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">Add New Customer</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
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
                    <button type="button" onclick="closeModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">
                        Save Customer
                    </button>
                </div>
            </form>
        </div>
    `;
    
    openModal(modalContent, 'default', false);
}

function saveNewCustomer(event) {
    event.preventDefault();
    
    const firstName = document.getElementById('newCustomerFirstName').value.trim();
    const lastName = document.getElementById('newCustomerLastName').value.trim();
    const phone = document.getElementById('newCustomerPhone').value.trim();
    const email = document.getElementById('newCustomerEmail').value.trim();
    
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
    
    // Close the modal
    closeModal();
    
    // Automatically select the newly created customer
    selectCustomer(newId);
    
    // Show success message
    showSuccessMessage('Customer added successfully!');
}

function showSuccessMessage(message) {
    const successDiv = document.createElement('div');
    successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity';
    successDiv.textContent = message;
    document.body.appendChild(successDiv);
    
    setTimeout(() => {
        successDiv.style.opacity = '0';
        setTimeout(() => successDiv.remove(), 300);
    }, 3000);
}

// Calendar functions
function initializeAppointmentCalendar() {
    const calendarContainer = document.getElementById('appointmentCalendar');
    if (!calendarContainer) return;
    
    const now = new Date();
    
    // Check for URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const dateParam = urlParams.get('date');
    const timeParam = urlParams.get('time');
    
    if (dateParam) {
        // Parse date from URL parameter (format: YYYY-MM-DD)
        const dateParts = dateParam.split('-');
        if (dateParts.length === 3) {
            const year = parseInt(dateParts[0]);
            const month = parseInt(dateParts[1]) - 1; // Month is 0-indexed
            const day = parseInt(dateParts[2]);
            selectedAppointmentDate = new Date(year, month, day);
            currentCalendarMonth = month;
            currentCalendarYear = year;
        } else {
            // Use current date if parsing fails
            selectedAppointmentDate = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            currentCalendarMonth = now.getMonth();
            currentCalendarYear = now.getFullYear();
        }
    } else {
        // Set current date as selected by default
        selectedAppointmentDate = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        currentCalendarMonth = now.getMonth();
        currentCalendarYear = now.getFullYear();
    }
    
    renderCalendar(currentCalendarMonth, currentCalendarYear);
    updateAvailableTimeSlots();
    
    // If time parameter is provided, select that time slot
    if (timeParam && selectedAppointmentDate) {
        // Wait a bit for time slots to render, then select the time
        setTimeout(() => {
            selectAppointmentTime(timeParam);
        }, 100);
    }
}

function renderCalendar(month, year) {
    const calendarContainer = document.getElementById('appointmentCalendar');
    if (!calendarContainer) return;
    
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    
    let calendarHTML = `
        <div class="mb-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">${monthNames[month]} ${year}</h3>
                <div class="flex gap-2">
                    <button onclick="changeMonth(-1)" class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition">‹</button>
                    <button onclick="changeMonth(1)" class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition">›</button>
                </div>
            </div>
            <div class="grid grid-cols-7 gap-1 mb-2">
    `;
    
    // Day headers
    dayNames.forEach(day => {
        calendarHTML += `<div class="text-center text-xs font-semibold text-gray-600 py-2">${day}</div>`;
    });
    
    // Empty cells for days before month starts
    for (let i = 0; i < firstDay; i++) {
        calendarHTML += `<div class="aspect-square"></div>`;
    }
    
    // Days of the month
    const today = new Date();
    for (let day = 1; day <= daysInMonth; day++) {
        const date = new Date(year, month, day);
        const isToday = date.toDateString() === today.toDateString();
        const isPast = date < today && !isToday;
        const isSelected = selectedAppointmentDate && date.toDateString() === selectedAppointmentDate.toDateString();
        
        let classes = 'aspect-square flex items-center justify-center text-sm rounded-lg transition cursor-pointer ';
        if (isPast) {
            classes += 'text-gray-300 cursor-not-allowed';
        } else if (isSelected) {
            classes += 'bg-[#003047] text-white font-semibold';
        } else if (isToday) {
            classes += 'bg-[#e6f0f3] text-[#003047] font-semibold hover:bg-[#b3d1d9]';
        } else {
            classes += 'text-gray-700 hover:bg-gray-100';
        }
        
        calendarHTML += `
            <div class="${classes}" ${!isPast ? `onclick="selectAppointmentDate(${year}, ${month}, ${day})"` : ''}>
                ${day}
            </div>
        `;
    }
    
    calendarHTML += `
            </div>
        </div>
    `;
    
    calendarContainer.innerHTML = calendarHTML;
}

function changeMonth(direction) {
    currentCalendarMonth += direction;
    if (currentCalendarMonth < 0) {
        currentCalendarMonth = 11;
        currentCalendarYear--;
    } else if (currentCalendarMonth > 11) {
        currentCalendarMonth = 0;
        currentCalendarYear++;
    }
    
    renderCalendar(currentCalendarMonth, currentCalendarYear);
}

function selectAppointmentDate(year, month, day) {
    selectedAppointmentDate = new Date(year, month, day);
    selectedAppointmentTime = null;
    renderCalendar(currentCalendarMonth, currentCalendarYear);
    updateAvailableTimeSlots();
}

function updateAvailableTimeSlots() {
    const timeSlotsContainer = document.getElementById('availableTimeSlots');
    if (!timeSlotsContainer) return;
    
    if (!selectedAppointmentDate) {
        timeSlotsContainer.innerHTML = '<p class="text-sm text-gray-500 text-center py-4">Please select a date to view available time slots</p>';
        return;
    }
    
    // Generate time slots (9 AM to 6 PM, 30-minute intervals)
    const timeSlots = [];
    for (let hour = 9; hour < 18; hour++) {
        for (let minute = 0; minute < 60; minute += 30) {
            const timeString = `${hour.toString().padStart(2, '0')}:${minute.toString().padStart(2, '0')}`;
            const displayTime = `${hour > 12 ? hour - 12 : hour === 0 ? 12 : hour}:${minute.toString().padStart(2, '0')} ${hour >= 12 ? 'PM' : 'AM'}`;
            timeSlots.push({ value: timeString, display: displayTime });
        }
    }
    
    // Filter out past times if today
    const now = new Date();
    const isToday = selectedAppointmentDate.toDateString() === now.toDateString();
    const availableSlots = timeSlots.filter(slot => {
        if (!isToday) return true;
        const [hours, minutes] = slot.value.split(':').map(Number);
        const slotTime = new Date(now.getFullYear(), now.getMonth(), now.getDate(), hours, minutes);
        return slotTime > now;
    });
    
    if (availableSlots.length === 0) {
        timeSlotsContainer.innerHTML = '<p class="text-sm text-gray-500 text-center py-4">No available time slots for this date</p>';
        return;
    }
    
    // Group time slots into Morning and Afternoon
    const morningSlots = availableSlots.filter(slot => {
        const hour = parseInt(slot.value.split(':')[0]);
        return hour < 12;
    });
    
    const afternoonSlots = availableSlots.filter(slot => {
        const hour = parseInt(slot.value.split(':')[0]);
        return hour >= 12;
    });
    
    let slotsHTML = '';
    
    // Morning Section
    if (morningSlots.length > 0) {
        slotsHTML += `
            <div>
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Morning</h4>
                <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
        `;
        morningSlots.forEach(slot => {
            const isSelected = selectedAppointmentTime === slot.value;
            slotsHTML += `
                <button type="button" 
                        onclick="selectAppointmentTime('${slot.value}')" 
                        class="px-3 py-2 text-sm font-medium rounded-lg border transition ${isSelected ? 'bg-[#003047] text-white border-[#003047]' : 'bg-white text-gray-700 border-gray-300 hover:border-[#003047] hover:bg-[#e6f0f3]'}">
                    ${slot.display}
                </button>
            `;
        });
        slotsHTML += `
                </div>
            </div>
        `;
    }
    
    // Afternoon Section
    if (afternoonSlots.length > 0) {
        slotsHTML += `
            <div>
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Afternoon</h4>
                <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
        `;
        afternoonSlots.forEach(slot => {
            const isSelected = selectedAppointmentTime === slot.value;
            slotsHTML += `
                <button type="button" 
                        onclick="selectAppointmentTime('${slot.value}')" 
                        class="px-3 py-2 text-sm font-medium rounded-lg border transition ${isSelected ? 'bg-[#003047] text-white border-[#003047]' : 'bg-white text-gray-700 border-gray-300 hover:border-[#003047] hover:bg-[#e6f0f3]'}">
                    ${slot.display}
                </button>
            `;
        });
        slotsHTML += `
                </div>
            </div>
        `;
    }
    
    timeSlotsContainer.innerHTML = slotsHTML;
}

function selectAppointmentTime(time) {
    selectedAppointmentTime = time;
    updateAvailableTimeSlots();
}

// Technician functions
async function fetchTechnicians() {
    try {
        const response = await fetch('../json/users.json');
        const data = await response.json();
        availableTechnicians = data.users.filter(user => (user.role === 'technician' || user.userlevel === 'technician') && user.status === 'active');
        renderAvailableTechnicians();
        renderAssignedTechnicians();
        updateCounts();
    } catch (error) {
        console.error('Error fetching technicians:', error);
        availableTechnicians = [];
    }
}

function searchTechnicians(searchTerm) {
    technicianSearchTerm = searchTerm.toLowerCase().trim();
    
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
            <div class="flex items-center justify-center h-full min-h-[90vh]">
                <p class="text-sm text-gray-400">No technicians available</p>
            </div>
        `;
        return;
    }
    
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
            <div class="flex items-center justify-center h-full min-h-[90vh]">
                <p class="text-sm text-gray-400">No technicians found</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    filteredTechnicians.forEach(technician => {
        const technicianIdStr = technician.id.toString();
        const isAssigned = assignedTechnicianIds.includes(technicianIdStr);
        const initials = technician.initials || (technician.firstName?.[0] || '') + (technician.lastName?.[0] || '');
        const fullName = `${technician.firstName} ${technician.lastName}`;
        
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
                <div class="relative flex-shrink-0">
                    <div class="${avatarClasses}">
                        <span class="${initialClasses}">${initials}</span>
                    </div>
                    <div class="${badgeClasses}">
                        0
                    </div>
                </div>
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
            <div class="flex items-center justify-center h-full min-h-[400px]">
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
                <div class="relative flex-shrink-0">
                    <div class="w-12 h-12 bg-[#003047] rounded-full flex items-center justify-center">
                        <span class="text-sm font-bold text-white">${initials}</span>
                    </div>
                    <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-[#003047] text-white rounded-full flex items-center justify-center text-xs font-bold border-2 border-white">
                        0
                    </div>
                </div>
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
    
    let filteredCount = availableTechnicians.length;
    if (technicianSearchTerm !== '') {
        filteredCount = availableTechnicians.filter(technician => {
            const fullName = `${technician.firstName} ${technician.lastName}`.toLowerCase();
            const initials = (technician.initials || (technician.firstName?.[0] || '') + (technician.lastName?.[0] || '')).toLowerCase();
            const searchText = fullName + ' ' + initials;
            return searchText.includes(technicianSearchTerm);
        }).length;
    }
    
    if (availableCountEl) {
        availableCountEl.textContent = filteredCount.toString();
    }
    
    if (assignedCountEl) {
        assignedCountEl.textContent = assignedTechnicianIds.length.toString();
    }
}

// Save booking function
function saveBooking() {
    // Validate required fields
    if (!selectedCustomer) {
        alert('Please select a customer first.');
        return;
    }
    
    if (!selectedAppointmentDate) {
        alert('Please select a booking date.');
        return;
    }
    
    if (!selectedAppointmentTime) {
        alert('Please select a booking time.');
        return;
    }
    
    if (assignedTechnicianIds.length === 0) {
        alert('Please assign at least one technician.');
        return;
    }
    
    // Prepare booking data
    const bookingData = {
        customer_id: selectedCustomer.id,
        appointment: 'booked', // Always booked since we removed walk-in option
        status: 'waiting',
        created_at: new Date().toISOString(),
        assigned_technician: assignedTechnicianIds.map(id => parseInt(id)),
        appointment_date: selectedAppointmentDate.toISOString().split('T')[0],
        appointment_time: selectedAppointmentTime,
        services: [] // Services will be added in future steps
    };
    
    // TODO: Save booking to backend/JSON
    console.log('Saving booking:', bookingData);
    
    // Show success message
    alert(`Booking created successfully for ${selectedCustomer.firstName} ${selectedCustomer.lastName}!`);
    
    // Redirect to calendar or booking list
    window.location.href = 'calendar.php';
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', async function() {
    await fetchCustomers();
    await fetchTechnicians();
    initializeAppointmentCalendar();
});
</script>

<?php include '../includes/footer.php'; ?>


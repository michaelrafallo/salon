<?php
$pageTitle = 'Booking';
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/modal.php';
?>

<!-- Main Content Area -->
<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Queue</h1>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-3">
                    <a href="booking.php" class="px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm sm:text-base active:scale-95 inline-flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        Start Booking
                    </a>
                </div>
            </div>
        </div>

        <!-- Search Bar and Status Filter -->
        <div class="mb-6 flex flex-col sm:flex-row gap-4">
            <div class="relative flex-1 max-w-md">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" placeholder="Search customer, service, or appointment..." class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-base">
            </div>
            <div class="flex-shrink-0">
                <select id="statusFilter" onchange="filterByStatus(this.value)" class="w-full sm:w-auto px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-base bg-white cursor-pointer">
                    <option value="">All Status</option>
                    <option value="waiting">Waiting</option>
                    <option value="in-progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="paid">Paid</option>
                </select>
            </div>
        </div>

        <!-- Booking Cards Grid - Tablet/Touch Optimized -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6">
            <!-- Booking Card 1 - Waiting -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow flex flex-col">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="min-w-0 flex-1">
                            <h3 class="font-semibold text-gray-900 truncate text-lg">Sarah Johnson</h3>
                            <p class="text-xs text-gray-500">Walk-In • <?php echo date('M d, Y'); ?></p>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0 ml-2">
                        <span class="inline-block px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-medium rounded-full">Waiting</span>
                    </div>
                </div>
                
                <!-- Services -->
                <div class="mb-4 space-y-2">
                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Classic Manicure</p>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">$35.00</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Gel Polish</p>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">$20.00</span>
                    </div>
                </div>

                <!-- Total and Actions -->
                <div class="flex flex-col gap-3 pt-4 border-t border-gray-200 mt-auto">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Total</span>
                        <span class="text-xl font-bold text-gray-900">$55.00</span>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="openDetailsModal('Sarah Johnson', 'ORDER925', 'Classic Manicure - $35.00|Gel Polish - $20.00', '$55.00', 'Waiting', '<?php echo date('M d, Y'); ?>', 'Walk-In', 'Maria Garcia')" class="flex-1 px-4 py-3 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition active:scale-95">See Details</button>
                        <button onclick="openPaymentModal('Sarah Johnson', 'ORDER925', '$55.00')" class="flex-1 px-4 py-3 text-sm font-medium text-white bg-[#003047] rounded-lg hover:bg-[#002535] transition active:scale-95">Pay</button>
                    </div>
                </div>
            </div>

            <!-- Booking Card 2 - In Progress -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow flex flex-col">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="min-w-0 flex-1">
                            <h3 class="font-semibold text-gray-900 truncate text-lg">Emily Chen</h3>
                            <p class="text-xs text-gray-500">Booked • <?php echo date('M d, Y'); ?></p>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0 ml-2">
                        <span class="inline-block px-3 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">In Progress</span>
                    </div>
                </div>
                
                <!-- Services -->
                <div class="mb-4 space-y-2">
                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Acrylic Full Set</p>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">$65.00</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Nail Art Design</p>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">$25.00</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Gel Polish</p>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">$20.00</span>
                    </div>
                    <div class="flex items-center justify-center pt-1">
                        <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full">+2 more</span>
                    </div>
                </div>

                <!-- Total and Actions -->
                <div class="flex flex-col gap-3 pt-4 border-t border-gray-200 mt-auto">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Total</span>
                        <span class="text-xl font-bold text-gray-900">$200.00</span>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="openDetailsModal('Emily Chen', 'ORDER926', 'Acrylic Full Set - $65.00|Nail Art Design - $25.00|Gel Polish - $20.00|Spa Pedicure - $55.00|Classic Manicure - $35.00', '$200.00', 'In Progress', '<?php echo date('M d, Y'); ?>', 'Booked', 'Maria Garcia, Lisa Wong, Anna Kim, Sarah Lee')" class="flex-1 px-4 py-3 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition active:scale-95">See Details</button>
                        <button onclick="openPaymentModal('Emily Chen', 'ORDER926', '$200.00')" class="flex-1 px-4 py-3 text-sm font-medium text-white bg-[#003047] rounded-lg hover:bg-[#002535] transition active:scale-95">Pay</button>
                    </div>
                </div>
            </div>

            <!-- Booking Card 3 - Completed -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow flex flex-col">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="min-w-0 flex-1">
                            <h3 class="font-semibold text-gray-900 truncate text-lg">Jessica Martinez</h3>
                            <p class="text-xs text-gray-500">Booked • <?php echo date('M d, Y'); ?></p>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0 ml-2">
                        <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full">Completed</span>
                    </div>
                </div>
                
                <!-- Services -->
                <div class="mb-4 space-y-2">
                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Spa Pedicure</p>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">$55.00</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Gel Polish</p>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">$20.00</span>
                    </div>
                </div>

                <!-- Total and Actions -->
                <div class="flex flex-col gap-3 pt-4 border-t border-gray-200 mt-auto">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Total</span>
                        <span class="text-xl font-bold text-gray-900">$75.00</span>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="openDetailsModal('Jessica Martinez', 'ORDER929', 'Spa Pedicure - $55.00|Gel Polish - $20.00', '$75.00', 'Completed', '<?php echo date('M d, Y'); ?>', 'Booked', 'Anna Kim')" class="flex-1 px-4 py-3 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition active:scale-95">See Details</button>
                        <button onclick="openPaymentModal('Jessica Martinez', 'ORDER929', '$75.00')" class="flex-1 px-4 py-3 text-sm font-medium text-white bg-[#003047] rounded-lg hover:bg-[#002535] transition active:scale-95">Pay</button>
                    </div>
                </div>
            </div>

            <!-- Booking Card 4 - Waiting (Booked) -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow flex flex-col">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="min-w-0 flex-1">
                            <h3 class="font-semibold text-gray-900 truncate text-lg">Amanda Taylor</h3>
                            <p class="text-xs text-gray-500">Booked • <?php echo date('M d, Y'); ?> • 2:00 PM</p>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0 ml-2">
                        <span class="inline-block px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-medium rounded-full">Waiting</span>
                    </div>
                </div>
                
                <!-- Services -->
                <div class="mb-4 space-y-2">
                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Full Set Gel Extensions</p>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">$85.00</span>
                    </div>
                </div>

                <!-- Total and Actions -->
                <div class="flex flex-col gap-3 pt-4 border-t border-gray-200 mt-auto">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Total</span>
                        <span class="text-xl font-bold text-gray-900">$85.00</span>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="openDetailsModal('Amanda Taylor', 'ORDER927', 'Full Set Gel Extensions - $85.00', '$85.00', 'Waiting', '<?php echo date('M d, Y'); ?> • 2:00 PM', 'Booked', 'Lisa Wong')" class="flex-1 px-4 py-3 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition active:scale-95">See Details</button>
                        <button onclick="openPaymentModal('Amanda Taylor', 'ORDER927', '$85.00')" class="flex-1 px-4 py-3 text-sm font-medium text-white bg-[#003047] rounded-lg hover:bg-[#002535] transition active:scale-95">Pay</button>
                    </div>
                </div>
            </div>

            <!-- Booking Card 5 - In Progress (Multiple Techs) -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow flex flex-col">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="min-w-0 flex-1">
                            <h3 class="font-semibold text-gray-900 truncate text-lg">Michelle Brown</h3>
                            <p class="text-xs text-gray-500">Walk-In • <?php echo date('M d, Y'); ?></p>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0 ml-2">
                        <span class="inline-block px-3 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">In Progress</span>
                    </div>
                </div>
                
                <!-- Services -->
                <div class="mb-4 space-y-2">
                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Classic Manicure</p>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">$35.00</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Spa Pedicure</p>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">$55.00</span>
                    </div>
                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Gel Polish</p>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">$20.00</span>
                    </div>
                    <div class="flex items-center justify-center pt-1">
                        <span class="inline-block px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full">+1 more</span>
                    </div>
                </div>

                <!-- Total and Actions -->
                <div class="flex flex-col gap-3 pt-4 border-t border-gray-200 mt-auto">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Total</span>
                        <span class="text-xl font-bold text-gray-900">$135.00</span>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="openDetailsModal('Michelle Brown', 'ORDER928', 'Classic Manicure - $35.00|Spa Pedicure - $55.00|Gel Polish - $20.00|Nail Art Design - $25.00', '$135.00', 'In Progress', '<?php echo date('M d, Y'); ?>', 'Walk-In', 'Sarah Lee, Maria Garcia, Lisa Wong, Anna Kim')" class="flex-1 px-4 py-3 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition active:scale-95">See Details</button>
                        <button onclick="openPaymentModal('Michelle Brown', 'ORDER928', '$135.00')" class="flex-1 px-4 py-3 text-sm font-medium text-white bg-[#003047] rounded-lg hover:bg-[#002535] transition active:scale-95">Pay</button>
                    </div>
                </div>
            </div>

            <!-- Booking Card 6 - Paid -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow flex flex-col">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3 flex-1 min-w-0">
                        <div class="min-w-0 flex-1">
                            <h3 class="font-semibold text-gray-900 truncate text-lg">Rachel Green</h3>
                            <p class="text-xs text-gray-500">Booked • <?php echo date('M d, Y'); ?></p>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0 ml-2">
                        <span class="inline-block px-3 py-1 bg-red-100 text-red-700 text-xs font-medium rounded-full">Paid</span>
                    </div>
                </div>
                
                <!-- Services -->
                <div class="mb-4 space-y-2">
                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Gel Manicure</p>
                        </div>
                        <span class="text-sm font-semibold text-gray-900">$45.00</span>
                    </div>
                </div>

                <!-- Total and Actions -->
                <div class="flex flex-col gap-3 pt-4 border-t border-gray-200 mt-auto">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Total</span>
                        <span class="text-xl font-bold text-gray-900">$45.00</span>
                    </div>
                    <div class="flex gap-2">
                        <button onclick="openDetailsModal('Rachel Green', 'ORDER930', 'Gel Manicure - $45.00', '$45.00', 'Paid', '<?php echo date('M d, Y'); ?>', 'Booked', 'Lisa Wong')" class="flex-1 px-4 py-3 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition active:scale-95">See Details</button>
                        <button disabled class="flex-1 px-4 py-3 text-sm font-medium text-white bg-gray-300 rounded-lg cursor-not-allowed opacity-60">Pay</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Confirmation Dialog Overlay (separate from main modal) -->
<div id="confirmDialogOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-[60] hidden flex items-center justify-center p-4" onclick="closeConfirmDialog()">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full" onclick="event.stopPropagation()">
        <div id="confirmDialogContent">
            <!-- Confirmation dialog content will be inserted here -->
        </div>
    </div>
</div>

<!-- Add Customer Modal Overlay (separate from main modal, appears on top) -->
<div id="addCustomerModalOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-[60] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <div id="addCustomerModalContent">
            <!-- Add Customer modal content will be inserted here -->
        </div>
    </div>
</div>

<script>
function openWalkInModal() {
    // Open the same modal as Book Appointment but mark it as walk-in
    openNewWalkInModal();
    // You can add walk-in specific logic here if needed
}

function openNewWalkInModal() {
    // Create services list HTML with checkboxes and quantity inputs (similar to Booking Details)
    let servicesHTML = '';
    availableServices.forEach(service => {
        const serviceId = service.name.replace(/\s+/g, '-').toLowerCase();
        servicesHTML += `
            <div id="newBooking-service-label-${serviceId}" class="newBooking-service-item service-item flex items-center gap-3 p-3 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 transition-all" data-service-name="${service.name.toLowerCase()}">
                <label class="cursor-pointer">
                    <input type="checkbox" 
                           class="newBooking-service-checkbox service-checkbox w-5 h-5 text-[#003047]" 
                           data-name="${service.name}" 
                           data-price="${service.price}" 
                           onchange="handleNewBookingServiceCheckboxChange('${service.name.replace(/'/g, "\\'")}', this.checked)">
                </label>
                <div class="flex-1 flex items-center justify-between">
                    <div class="flex-1 cursor-pointer" onclick="toggleNewBookingServiceCheckbox('${service.name.replace(/'/g, "\\'")}')">
                        <p class="text-sm font-medium text-gray-900">${service.name}</p>
                        <p class="text-xs text-gray-500">$${service.price.toFixed(2)} each</p>
                    </div>
                    <div class="flex items-center">
                        <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                            <button type="button" 
                                    onclick="event.preventDefault(); event.stopPropagation(); decrementNewBookingQuantity('${service.name.replace(/'/g, "\\'")}');" 
                                    class="newBooking-service-quantity-decrement service-quantity-decrement w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 active:bg-gray-300 transition-colors text-gray-700 font-medium disabled:opacity-50 disabled:cursor-not-allowed"
                                    data-name="${service.name}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </button>
                            <input type="number" 
                                   min="0" 
                                   value="0" 
                                   class="newBooking-service-quantity service-quantity w-14 px-1 py-1 text-sm text-center border-0 focus:outline-none focus:ring-0 bg-white font-medium" 
                                   data-name="${service.name}"
                                   data-price="${service.price}"
                                   onchange="handleNewBookingQuantityChange('${service.name.replace(/'/g, "\\'")}', this.value)"
                                   oninput="updateNewBookingQuantityFromInput('${service.name.replace(/'/g, "\\'")}', this.value)">
                            <button type="button" 
                                    onclick="event.preventDefault(); event.stopPropagation(); incrementNewBookingQuantity('${service.name.replace(/'/g, "\\'")}');" 
                                    class="newBooking-service-quantity-increment service-quantity-increment w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 active:bg-gray-300 transition-colors text-gray-700 font-medium"
                                    data-name="${service.name}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    const modalContent = `
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">New Booking</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form onsubmit="saveWalkIn(event)" class="space-y-4">
                <!-- Customer Search/Selection -->
                <div id="customerSearchSection">
                    <p class="text-sm text-gray-600 font-medium mb-2">Select Customer</p>
                    <div class="flex gap-2">
                        <div class="relative flex-1">
                            <div id="customerDropdown" class="relative">
                                <button type="button" 
                                        id="customerDropdownBtn"
                                        onclick="toggleCustomerDropdown()" 
                                        class="w-full text-left pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-sm bg-white flex items-center justify-between">
                                    <span id="customerDropdownText" class="text-gray-500">Search by name, phone, or email...</span>
                                    <svg id="customerDropdownIcon" class="w-5 h-5 text-gray-400 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </button>
                                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                    </svg>
                                </div>
                                
                                <!-- Dropdown Menu -->
                                <div id="customerDropdownMenu" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-64 overflow-y-auto hidden">
                                    <div class="p-2 border-b border-gray-200 sticky top-0 bg-white">
                                        <input type="text" 
                                               id="customerSearchInput" 
                                               placeholder="Search customers..." 
                                               class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-sm"
                                               oninput="searchCustomers(this.value)"
                                               onclick="event.stopPropagation()">
                                        <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none mt-1 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <div id="customerSearchResults" class="p-2 space-y-1"></div>
                                </div>
                            </div>
                        </div>
                        <button type="button" 
                                id="addNewCustomerBtn"
                                onclick="openAddCustomerModal()" 
                                class="px-4 py-2 border border-[#b3d1d9] bg-[#e6f0f3] text-[#003047] rounded-lg hover:bg-[#e6f0f3] transition font-medium text-sm whitespace-nowrap flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Add New Customer
                        </button>
                    </div>
                    
                    <!-- Selected Customer Display -->
                    <div id="selectedCustomerDisplay" class="mt-3 hidden">
                        <div class="bg-[#e6f0f3] border border-[#b3d1d9] rounded-lg p-3 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-[#e6f0f3] rounded-full flex items-center justify-center flex-shrink-0">
                                    <span id="selectedCustomerInitials" class="text-sm font-bold text-[#003047]"></span>
                                </div>
                                <div>
                                    <p id="selectedCustomerName" class="text-sm font-semibold text-gray-900"></p>
                                    <p id="selectedCustomerContact" class="text-xs text-gray-500"></p>
                                </div>
                            </div>
                            <button type="button" onclick="clearSelectedCustomer()" class="text-gray-400 hover:text-gray-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Services Section -->
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-sm text-gray-600 font-medium">Services</p>
                    </div>
                    <!-- Search Input and Toggle -->
                    <div class="mb-3 flex gap-2">
                        <div class="relative flex-1">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input type="text" 
                                   id="newBookingServiceSearchInput" 
                                   placeholder="Search services..." 
                                   class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-sm"
                                   oninput="filterNewBookingServices(this.value); updateNewBookingClearButton(this.value);">
                            <button type="button" 
                                    id="newBookingClearSearchBtn" 
                                    onclick="clearNewBookingSearch()" 
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <button type="button" 
                                id="newBookingSelectAllBtn" 
                                onclick="toggleNewBookingSelectAll()" 
                                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition font-medium text-sm whitespace-nowrap flex items-center gap-2">
                            <svg id="newBookingSelectAllIcon" class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                            <span id="newBookingSelectAllText">Select All</span>
                        </button>
                        <button type="button" 
                                id="newBookingToggleSelectedBtn" 
                                onclick="toggleNewBookingSelectedOnly()" 
                                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition font-medium text-sm whitespace-nowrap flex items-center gap-2">
                            <svg id="newBookingToggleSelectedIcon" class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span id="newBookingToggleSelectedText">Show Selected</span>
                        </button>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 space-y-2 h-[600px] overflow-y-auto" id="newBookingServicesListContainer">
                        ${servicesHTML}
                    </div>
                </div>
                
                <!-- Total -->
                <div class="pt-4 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-semibold text-gray-900">Total</span>
                        <span id="newBookingTotal" class="text-2xl font-bold text-gray-900">$0.00</span>
                    </div>
                </div>
                
                <!-- Buttons -->
                <div class="flex justify-end gap-3 pt-6">
                    <button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">
                        Add to Queue
                    </button>
                </div>
            </form>
        </div>
    `;
    
    // Store modal content for restoration after add customer modal
    window.newBookingModalContent = modalContent;
    
    openModal(modalContent, 'large');
    
    // Initialize New Booking modal
    setTimeout(() => {
        const modalContainer = document.querySelector('#modalOverlay .bg-white');
        if (modalContainer) {
            modalContainer.style.maxHeight = '95vh';
        }
        // Update all quantity button states
        availableServices.forEach(service => {
            updateNewBookingQuantityButtons(service.name);
        });
        calculateNewBookingTotal();
        
        // Initialize toggle state
        window.newBookingShowSelectedOnly = false;
        
        // Update toggle button state
        updateNewBookingToggleSelectedButtonState();
        
        // Update select all button state
        updateNewBookingSelectAllButtonState();
        
        // Initialize customer search
        window.selectedCustomer = null;
    }, 100);
}

// Mock customer data (in a real app, this would come from a database)
let mockCustomers = [
    { id: 1, firstName: 'Sarah', lastName: 'Johnson', phone: '(555) 123-4567', email: 'sarah.j@email.com' },
    { id: 2, firstName: 'Emily', lastName: 'Chen', phone: '(555) 234-5678', email: 'emily.c@email.com' },
    { id: 3, firstName: 'Jessica', lastName: 'Martinez', phone: '(555) 345-6789', email: 'jessica.m@email.com' },
    { id: 4, firstName: 'Amanda', lastName: 'Taylor', phone: '(555) 456-7890', email: 'amanda.t@email.com' },
    { id: 5, firstName: 'Michelle', lastName: 'Brown', phone: '(555) 567-8901', email: 'michelle.b@email.com' },
    { id: 6, firstName: 'Rachel', lastName: 'Green', phone: '(555) 678-9012', email: 'rachel.g@email.com' }
];

function toggleCustomerDropdown() {
    const dropdownMenu = document.getElementById('customerDropdownMenu');
    const dropdownIcon = document.getElementById('customerDropdownIcon');
    
    if (dropdownMenu.classList.contains('hidden')) {
        dropdownMenu.classList.remove('hidden');
        dropdownIcon.classList.add('rotate-180');
        // Focus on search input when dropdown opens
        setTimeout(() => {
            const searchInput = document.getElementById('customerSearchInput');
            if (searchInput) {
                searchInput.focus();
                // Show all customers initially
                searchCustomers('');
            }
        }, 100);
    } else {
        dropdownMenu.classList.add('hidden');
        dropdownIcon.classList.remove('rotate-180');
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
    
    // Filter customers by name, phone, or email
    let matchingCustomers = mockCustomers;
    
    if (searchLower !== '') {
        matchingCustomers = mockCustomers.filter(customer => {
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
        const initials = `${customer.firstName[0]}${customer.lastName[0]}`.toUpperCase();
        resultsHTML += `
            <button type="button" 
                    onclick="selectCustomer(${customer.id}); event.stopPropagation();" 
                    class="w-full text-left px-3 py-2 hover:bg-gray-50 rounded-lg transition flex items-center gap-3">
                <div class="w-8 h-8 bg-[#e6f0f3] rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-xs font-bold text-[#003047]">${initials}</span>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-semibold text-gray-900">${customer.firstName} ${customer.lastName}</p>
                    <p class="text-xs text-gray-500">${customer.phone || ''}${customer.email ? ' • ' + customer.email : ''}</p>
                </div>
            </button>
        `;
    });
    
    resultsDiv.innerHTML = resultsHTML;
}

function selectCustomer(customerId) {
    const customer = mockCustomers.find(c => c.id === customerId);
    if (!customer) return;
    
    // Store selected customer
    window.selectedCustomer = customer;
    
    // Close dropdown
    const dropdownMenu = document.getElementById('customerDropdownMenu');
    const dropdownIcon = document.getElementById('customerDropdownIcon');
    const dropdownBtn = document.getElementById('customerDropdownBtn');
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
    
    const initials = `${customer.firstName[0]}${customer.lastName[0]}`.toUpperCase();
    initialsDiv.textContent = initials;
    nameDiv.textContent = `${customer.firstName} ${customer.lastName}`;
    contactDiv.textContent = `${customer.phone || ''}${customer.email ? ' • ' + customer.email : ''}`;
    
    selectedDisplay.classList.remove('hidden');
    
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
    selectedDisplay.classList.add('hidden');
    
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
    // Close customer dropdown
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
                    <button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">
                        Save Customer
                    </button>
                </div>
            </form>
        </div>
    `;
    
    // Open the Add Customer modal in its own overlay (doesn't close New Booking modal)
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
    const newId = Math.max(...mockCustomers.map(c => c.id), 0) + 1;
    
    // Create new customer object
    const newCustomer = {
        id: newId,
        firstName: firstName,
        lastName: lastName,
        phone: phone,
        email: email
    };
    
    // Add to mock customers array
    mockCustomers.push(newCustomer);
    
    // Close only the Add Customer modal (not the New Booking modal)
    closeAddCustomerModal();
    
    // Select the newly created customer in the New Booking modal
    setTimeout(() => {
        selectCustomer(newId);
        showSuccessMessage(`Customer ${firstName} ${lastName} added and selected!`);
    }, 100);
}

// Available services with prices
const availableServices = [
    { name: 'Classic Manicure', price: 35.00 },
    { name: 'Gel Manicure', price: 45.00 },
    { name: 'Gel Polish', price: 20.00 },
    { name: 'Spa Pedicure', price: 55.00 },
    { name: 'Acrylic Full Set', price: 65.00 },
    { name: 'Nail Art Design', price: 25.00 },
    { name: 'Full Set Gel Extensions', price: 85.00 }
];

function openViewDetailsModal(customerName, orderId, servicesData, total, status = '', bookingDate = '', bookingType = '', technician = '') {
    // Parse services data - it can be either HTML string or array of service objects
    let existingServices = [];
    
    if (typeof servicesData === 'string') {
        // Parse from pipe-separated string (format: "Service Name - $XX.XX|Service Name - $XX.XX")
        existingServices = parseServicesFromString(servicesData);
    } else if (Array.isArray(servicesData)) {
        existingServices = servicesData;
    }
    
    // Create services list HTML - just display the services without checkboxes
    let servicesHTML = '';
    if (existingServices.length === 0) {
        servicesHTML = '<p class="text-sm text-gray-500 text-center py-4">No services found</p>';
    } else {
        existingServices.forEach(service => {
            const serviceName = service.name || service;
            const servicePrice = service.price || 0;
            const quantity = service.quantity || 1;
            const totalPrice = servicePrice * quantity;
            
            servicesHTML += `
                <div class="flex items-center justify-between p-3 bg-white border border-gray-200 rounded-lg">
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-900">${serviceName}</p>
                        <p class="text-xs text-gray-500">$${servicePrice.toFixed(2)} each ${quantity > 1 ? '× ' + quantity : ''}</p>
                    </div>
                    <span class="text-sm font-semibold text-gray-900">$${totalPrice.toFixed(2)}</span>
                </div>
            `;
        });
    }
    
    const modalContent = `
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">Booking Details</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Customer</p>
                        <p class="font-semibold text-gray-900">${customerName || ''}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Booking ID</p>
                        <p class="font-semibold text-gray-900">${orderId || ''}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Booking Date</p>
                        <p class="font-semibold text-gray-900">${bookingDate || 'N/A'}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Booking Type</p>
                        <p class="font-semibold text-gray-900">${bookingType || 'N/A'}</p>
                    </div>
                    ${technician && technician !== 'Not Assigned' ? `
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Technician</p>
                        <p class="font-semibold text-gray-900">${technician}</p>
                    </div>
                    ` : ''}
                </div>
                <div>
                    <p class="text-sm text-gray-600 font-medium mb-3">Services</p>
                    <div class="bg-gray-50 rounded-lg p-4 space-y-2 max-h-[400px] overflow-y-auto">
                        ${servicesHTML}
                    </div>
                </div>
                <div class="pt-4 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-semibold text-gray-900">Total</span>
                        <span class="text-2xl font-bold text-gray-900">${total || '$0.00'}</span>
                    </div>
                </div>
            </div>
            ${status.toLowerCase() !== 'paid' ? `
            <div class="flex justify-end items-center gap-3 pt-6">
                <button onclick="openPaymentFromDetails('${customerName.replace(/'/g, "\\'")}', '${orderId}', '${total || '$0.00'}')" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium active:scale-95">
                    Pay
                </button>
            </div>
            ` : ''}
        </div>
    `;
    openModal(modalContent, 'large');
}

function toggleServiceCheckbox(serviceName) {
    // Find the checkbox by service name
    const allCheckboxes = document.querySelectorAll('.service-checkbox[data-name]');
    let checkbox = null;
    
    allCheckboxes.forEach(cb => {
        if (cb.getAttribute('data-name') === serviceName) {
            checkbox = cb;
        }
    });
    
    if (checkbox) {
        // Toggle the checkbox
        checkbox.checked = !checkbox.checked;
        // Trigger the change handler
        handleServiceCheckboxChange(serviceName, checkbox.checked);
    }
}

function toggleNewBookingServiceCheckbox(serviceName) {
    // Find the checkbox by service name
    const allCheckboxes = document.querySelectorAll('.newBooking-service-checkbox[data-name]');
    let checkbox = null;
    
    allCheckboxes.forEach(cb => {
        if (cb.getAttribute('data-name') === serviceName) {
            checkbox = cb;
        }
    });
    
    if (checkbox) {
        // Toggle the checkbox
        checkbox.checked = !checkbox.checked;
        // Trigger the change handler
        handleNewBookingServiceCheckboxChange(serviceName, checkbox.checked);
    }
}

function filterServices(searchTerm) {
    const searchLower = searchTerm.toLowerCase().trim();
    const serviceItems = document.querySelectorAll('.service-item');
    const showSelectedOnly = window.showSelectedOnly || false;
    
    serviceItems.forEach(item => {
        const serviceName = item.getAttribute('data-service-name');
        const checkbox = item.querySelector('.service-checkbox');
        const isChecked = checkbox && checkbox.checked;
        
        // Check if item matches search term
        const matchesSearch = searchLower === '' || (serviceName && serviceName.includes(searchLower));
        
        // Check if item matches selected filter
        const matchesSelected = !showSelectedOnly || isChecked;
        
        // Show item only if it matches both search and selected filter
        if (matchesSearch && matchesSelected) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
}

function updateClearButton(value) {
    const clearBtn = document.getElementById('clearSearchBtn');
    if (clearBtn) {
        if (value && value.trim() !== '') {
            clearBtn.classList.remove('hidden');
        } else {
            clearBtn.classList.add('hidden');
        }
    }
}

function clearSearch() {
    const searchInput = document.getElementById('serviceSearchInput');
    if (searchInput) {
        searchInput.value = '';
        filterServices('');
        updateClearButton('');
        searchInput.focus();
    }
}

function toggleSelectedOnly() {
    // Don't toggle if button is disabled
    const toggleBtn = document.getElementById('toggleSelectedBtn');
    if (toggleBtn && toggleBtn.disabled) return;
    
    window.showSelectedOnly = !window.showSelectedOnly;
    const toggleText = document.getElementById('toggleSelectedText');
    const toggleIcon = document.getElementById('toggleSelectedIcon');
    const searchInput = document.getElementById('serviceSearchInput');
    const searchTerm = searchInput ? searchInput.value : '';
    
    // Update button appearance
    if (window.showSelectedOnly) {
        toggleBtn.classList.remove('border-gray-300', 'hover:bg-gray-50');
        toggleBtn.classList.add('bg-[#003047]', 'border-[#003047]', 'text-white', 'hover:bg-[#002535]');
        toggleText.textContent = 'Show All';
        toggleIcon.classList.remove('text-gray-600');
        toggleIcon.classList.add('text-white');
    } else {
        toggleBtn.classList.remove('bg-[#003047]', 'border-[#003047]', 'text-white', 'hover:bg-[#002535]');
        toggleBtn.classList.add('border-gray-300', 'hover:bg-gray-50');
        toggleText.textContent = 'Show Selected';
        toggleIcon.classList.remove('text-white');
        toggleIcon.classList.add('text-gray-600');
    }
    
    // Re-apply filters
    filterServices(searchTerm);
}

// Check if all items are selected in Booking Details modal
function areAllItemsSelected() {
    const checkboxes = document.querySelectorAll('.service-checkbox[data-name]');
    if (checkboxes.length === 0) return false;
    
    for (let checkbox of checkboxes) {
        if (!checkbox.checked) {
            return false;
        }
    }
    return true;
}

// Update the Select All button state in Booking Details modal
function updateSelectAllButtonState() {
    const selectAllBtn = document.getElementById('selectAllBtn');
    const selectAllText = document.getElementById('selectAllText');
    const selectAllIcon = document.getElementById('selectAllIcon');
    
    if (!selectAllBtn || !selectAllText || !selectAllIcon) return;
    
    const allSelected = areAllItemsSelected();
    const path = selectAllIcon.querySelector('path');
    
    if (allSelected) {
        selectAllText.textContent = 'Unselect All';
        if (path) {
            path.setAttribute('d', 'M6 18L18 6M6 6l12 12');
        }
    } else {
        selectAllText.textContent = 'Select All';
        if (path) {
            path.setAttribute('d', 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4');
        }
    }
}

// Toggle select/unselect all items in Booking Details modal
function toggleSelectAll() {
    const allSelected = areAllItemsSelected();
    const checkboxes = document.querySelectorAll('.service-checkbox[data-name]');
    
    checkboxes.forEach(checkbox => {
        const serviceName = checkbox.getAttribute('data-name');
        const shouldSelect = !allSelected;
        
        if (checkbox.checked !== shouldSelect) {
            checkbox.checked = shouldSelect;
            handleServiceCheckboxChange(serviceName, shouldSelect);
        }
    });
    
    // Update button state
    updateSelectAllButtonState();
}

function parseServicesFromString(servicesString) {
    // Parse pipe-separated string like "Service Name - $XX.XX|Service Name 2 - $YY.YY"
    const services = [];
    const parts = servicesString.split('|');
    
    parts.forEach(part => {
        const match = part.trim().match(/^(.+?)\s*-\s*\$\s*([\d.]+)$/);
        if (match) {
            services.push({
                name: match[1].trim(),
                price: parseFloat(match[2]),
                quantity: 1
            });
        }
    });
    
    return services;
}

function handleServiceCheckboxChange(serviceName, isChecked) {
    const quantityInput = document.querySelector(`.service-quantity[data-name="${serviceName}"]`);
    const labelId = `service-label-${serviceName.replace(/\s+/g, '-').toLowerCase()}`;
    const label = document.getElementById(labelId);
    const searchInput = document.getElementById('serviceSearchInput');
    const searchTerm = searchInput ? searchInput.value : '';
    
    // Update quantity
    if (quantityInput) {
        if (isChecked) {
            quantityInput.value = 1;
        } else {
            quantityInput.value = 0;
        }
    }
    
    // Update label styling to highlight checked items
    if (label) {
        if (isChecked) {
            label.classList.remove('bg-white', 'border-gray-200');
            label.classList.add('bg-[#e6f0f3]', 'border-[#b3d1d9]', 'shadow-sm');
        } else {
            label.classList.remove('bg-[#e6f0f3]', 'border-[#b3d1d9]', 'shadow-sm');
            label.classList.add('bg-white', 'border-gray-200');
        }
    }
    
    updateQuantityButtons(serviceName);
    calculateOrderTotal();
    
    // Re-apply filters if toggle is active
    filterServices(searchTerm);
    
    // Update toggle button state
    updateToggleSelectedButtonState();
    
    // Update select all button state
    updateSelectAllButtonState();
}

// Check if any items are selected in Booking Details modal
function hasSelectedItems() {
    const checkboxes = document.querySelectorAll('.service-checkbox[data-name]');
    for (let checkbox of checkboxes) {
        if (checkbox.checked) {
            return true;
        }
    }
    return false;
}

// Update the toggle "Show Selected" button state in Booking Details modal
function updateToggleSelectedButtonState() {
    const toggleBtn = document.getElementById('toggleSelectedBtn');
    if (!toggleBtn) return;
    
    const hasSelected = hasSelectedItems();
    
    if (hasSelected) {
        // Enable button
        toggleBtn.disabled = false;
        toggleBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        toggleBtn.classList.add('cursor-pointer');
    } else {
        // Disable button
        toggleBtn.disabled = true;
        toggleBtn.classList.add('opacity-50', 'cursor-not-allowed');
        toggleBtn.classList.remove('cursor-pointer');
        
        // Reset toggle state if no items are selected
        if (window.showSelectedOnly) {
            window.showSelectedOnly = false;
            const toggleText = document.getElementById('toggleSelectedText');
            const toggleIcon = document.getElementById('toggleSelectedIcon');
            if (toggleText) toggleText.textContent = 'Show Selected';
            if (toggleIcon) {
                toggleIcon.classList.remove('text-white');
                toggleIcon.classList.add('text-gray-600');
            }
            toggleBtn.classList.remove('bg-[#003047]', 'border-[#003047]', 'text-white', 'hover:bg-[#002535]');
            toggleBtn.classList.add('border-gray-300', 'hover:bg-gray-50');
        }
    }
}

function incrementQuantity(serviceName) {
    // Find the quantity input by checking all inputs with the data-name attribute
    const allQuantityInputs = document.querySelectorAll('.service-quantity[data-name]');
    let quantityInput = null;
    
    // Find the matching input by comparing the data-name attribute value
    allQuantityInputs.forEach(input => {
        if (input.getAttribute('data-name') === serviceName) {
            quantityInput = input;
        }
    });
    
    if (quantityInput) {
        let currentValue = parseInt(quantityInput.value) || 0;
        currentValue++;
        quantityInput.value = currentValue;
        
        // Trigger input event to ensure other handlers fire
        quantityInput.dispatchEvent(new Event('input', { bubbles: true }));
        
        // Find the matching checkbox after we've updated the value
        const allCheckboxes = document.querySelectorAll('.service-checkbox[data-name]');
        let checkbox = null;
        allCheckboxes.forEach(cb => {
            if (cb.getAttribute('data-name') === serviceName) {
                checkbox = cb;
            }
        });
        
        // Ensure checkbox is checked if quantity > 0
        if (checkbox && currentValue > 0) {
            if (!checkbox.checked) {
                checkbox.checked = true;
                // Update highlighting only if checkbox state changed
                handleServiceCheckboxChange(serviceName, true);
            }
        }
        
        updateQuantityButtons(serviceName);
        calculateOrderTotal();
    }
}

function decrementQuantity(serviceName) {
    // Find the quantity input by checking all inputs with the data-name attribute
    const allQuantityInputs = document.querySelectorAll('.service-quantity[data-name]');
    let quantityInput = null;
    
    // Find the matching input by comparing the data-name attribute value
    allQuantityInputs.forEach(input => {
        if (input.getAttribute('data-name') === serviceName) {
            quantityInput = input;
        }
    });
    
    if (quantityInput) {
        let currentValue = parseInt(quantityInput.value) || 0;
        if (currentValue > 0) {
            currentValue--;
            quantityInput.value = currentValue;
            
            // Trigger input event to ensure other handlers fire
            quantityInput.dispatchEvent(new Event('input', { bubbles: true }));
            
            // Find the matching checkbox after we've updated the value
            const allCheckboxes = document.querySelectorAll('.service-checkbox[data-name]');
            let checkbox = null;
            allCheckboxes.forEach(cb => {
                if (cb.getAttribute('data-name') === serviceName) {
                    checkbox = cb;
                }
            });
            
            // Uncheck checkbox if quantity becomes 0
            if (checkbox && currentValue === 0) {
                if (checkbox.checked) {
                    checkbox.checked = false;
                    // Update highlighting only if checkbox state changed
                    handleServiceCheckboxChange(serviceName, false);
                }
            }
        }
        
        updateQuantityButtons(serviceName);
        calculateOrderTotal();
    }
}

function handleQuantityChange(serviceName, value) {
    const quantity = parseInt(value) || 0;
    const checkbox = document.querySelector(`.service-checkbox[data-name="${serviceName}"]`);
    
    // Update checkbox based on quantity
    if (checkbox) {
        const wasChecked = checkbox.checked;
        checkbox.checked = quantity > 0;
        
        // Update highlighting if state changed
        if (wasChecked !== checkbox.checked) {
            handleServiceCheckboxChange(serviceName, checkbox.checked);
        }
    }
    
    // Update button states
    updateQuantityButtons(serviceName);
    
    // Recalculate total
    calculateOrderTotal();
}

function updateQuantityFromInput(serviceName, value) {
    // Sync checkbox state while user is typing
    const quantity = parseInt(value) || 0;
    const checkbox = document.querySelector(`.service-checkbox[data-name="${serviceName}"]`);
    
    if (checkbox) {
        const wasChecked = checkbox.checked;
        checkbox.checked = quantity > 0;
        
        // Update highlighting if state changed
        if (wasChecked !== checkbox.checked) {
            handleServiceCheckboxChange(serviceName, checkbox.checked);
        }
    }
    
    updateQuantityButtons(serviceName);
}

function updateQuantityButtons(serviceName) {
    const quantityInput = document.querySelector(`.service-quantity[data-name="${serviceName}"]`);
    const decrementBtn = document.querySelector(`.service-quantity-decrement[data-name="${serviceName}"]`);
    
    if (quantityInput && decrementBtn) {
        const quantity = parseInt(quantityInput.value) || 0;
        if (quantity <= 0) {
            decrementBtn.disabled = true;
            decrementBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            decrementBtn.disabled = false;
            decrementBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }
}

function calculateOrderTotal() {
    let total = 0;
    const checkboxes = document.querySelectorAll('.service-checkbox:checked');
    
    checkboxes.forEach(checkbox => {
        const quantityInput = document.querySelector(`.service-quantity[data-name="${checkbox.dataset.name}"]`);
        const quantity = parseInt(quantityInput.value) || 0;
        const price = parseFloat(checkbox.dataset.price) || 0;
        total += quantity * price;
    });
    
    const totalElement = document.getElementById('orderTotal');
    if (totalElement) {
        totalElement.textContent = '$' + total.toFixed(2);
    }
}

function saveOrderDetails(orderId) {
    const selectedServices = [];
    document.querySelectorAll('.service-checkbox:checked').forEach(checkbox => {
        const quantityInput = document.querySelector(`.service-quantity[data-name="${checkbox.dataset.name}"]`);
        const quantity = parseInt(quantityInput.value) || 0;
        if (quantity > 0) {
            selectedServices.push({
                name: checkbox.dataset.name,
                price: parseFloat(checkbox.dataset.price),
                quantity: quantity
            });
        }
    });
    
    // Show success message
    showSuccessMessage('Booking details updated successfully!');
    console.log('Booking ID:', orderId);
    console.log('Selected Services:', selectedServices);
    
    // Here you would typically save to server
    // For now, just close modal after a delay
    setTimeout(() => {
        closeModal();
        location.reload();
    }, 1500);
}

function cancelOrderDetails() {
    // Show modern confirmation dialog (appears on top of Booking Details modal)
    showConfirmDialog(
        'Cancel Booking',
        'Are you sure you want to cancel this booking? All unsaved changes will be lost.',
        function() {
            // User confirmed - close Booking Details modal without saving
            closeModal();
        },
        function() {
            // User cancelled - do nothing, keep Booking Details modal open
            // The confirmation dialog will close automatically
        }
    );
}

function showConfirmDialog(title, message, onConfirm, onCancel) {
    const dialogContent = `
        <div class="p-6">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-[#e6f0f3] rounded-full flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-[#003047]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-xl font-bold text-gray-900">${title}</h3>
                </div>
            </div>
            <p class="text-gray-700 mb-6 ml-16">${message}</p>
            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200">
                <button onclick="closeConfirmDialog(); if (window.confirmConfirm) { window.confirmConfirm(); }" class="px-6 py-3 text-white bg-red-500 rounded-lg hover:bg-red-600 transition font-medium active:scale-95">
                    Yes, Cancel Booking
                </button>
            </div>
        </div>
    `;
    
    // Store callbacks
    window.confirmConfirm = onConfirm;
    window.confirmCancel = onCancel;
    
    // Show confirmation dialog in separate overlay (higher z-index than main modal)
    const confirmOverlay = document.getElementById('confirmDialogOverlay');
    const confirmContent = document.getElementById('confirmDialogContent');
    confirmContent.innerHTML = dialogContent;
    confirmOverlay.classList.remove('hidden');
}

function closeConfirmDialog() {
    // Only close the confirmation dialog, not the Booking Details modal
    const confirmOverlay = document.getElementById('confirmDialogOverlay');
    confirmOverlay.classList.add('hidden');
    
    // Clean up callbacks
    delete window.confirmConfirm;
    delete window.confirmCancel;
}

// Alias for compatibility - accepts pipe-separated service list
function openDetailsModal(customerName, orderId, servicesString, total, status = '', bookingDate = '', bookingType = '', technician = '') {
    // Pass the services string directly to be parsed
    openViewDetailsModal(customerName, orderId, servicesString, total, status, bookingDate, bookingType, technician);
}

function openPaymentFromDetails(customerName, orderId, initialTotal) {
    // Get the current total from the orderTotal element (it may have changed)
    const totalElement = document.getElementById('orderTotal');
    let currentTotal = initialTotal;
    
    if (totalElement) {
        // Extract the total value from the element text (format: $XX.XX)
        const totalText = totalElement.textContent.trim();
        currentTotal = totalText.startsWith('$') ? totalText : '$' + totalText;
    }
    
    // Close the Booking Details modal first
    closeModal();
    
    // Open the payment modal with current total
    setTimeout(() => {
        openPaymentModal(customerName, orderId, currentTotal);
    }, 300);
}

function openPaymentModal(customerName, orderId, total) {
    // Calculate subtotal and tax (assuming 5% tax for now)
    const totalAmount = parseFloat(total.replace('$', ''));
    const subtotal = (totalAmount / 1.05).toFixed(2);
    const tax = (totalAmount - subtotal).toFixed(2);
    
    // Store original total for tip calculations
    window.paymentSubtotal = parseFloat(subtotal);
    window.paymentTax = parseFloat(tax);
    window.paymentTip = 0;
    
    // Get current date and time
    const now = new Date();
    const dateStr = now.toLocaleDateString('en-US', { weekday: 'short', year: 'numeric', month: 'long', day: 'numeric' });
    const timeStr = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
    
    // Get customer initial for avatar
    const customerInitial = customerName.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
    
    const modalContent = `
        <div class="p-6 max-w-6xl mx-auto">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Payment</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Side: Customer Info & Transaction Details -->
                <div class="space-y-4">
                    <!-- Customer Info -->
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-[#e6f0f3] rounded-lg flex items-center justify-center flex-shrink-0">
                            <span class="text-lg font-semibold text-[#003047]">${customerInitial}</span>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-semibold text-gray-900">${customerName}</h4>
                            <p class="text-sm text-gray-600">${orderId} / Walk-In</p>
                            <p class="text-xs text-gray-500">${dateStr} ${timeStr}</p>
                        </div>
                    </div>
                    
                    <!-- Transaction Details -->
                    <div class="mt-4">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Transaction Details</h4>
                        <div class="bg-gray-50 rounded-lg p-4 space-y-4">
                            <div id="paymentItemsList" class="space-y-2">
                                <!-- Items will be populated here -->
                            </div>
                            
                            <div class="space-y-2 border-t border-gray-200 pt-3">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Items (<span id="itemCount">0</span>)</span>
                                    <span class="text-gray-900 font-medium" id="paymentSubtotalDisplay">$${subtotal}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Tax (5%)</span>
                                    <span class="text-gray-900 font-medium" id="paymentTaxDisplay">$${tax}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Tip</span>
                                    <span class="text-gray-900 font-medium" id="paymentTipDisplay">$0.00</span>
                                </div>
                                <div class="flex justify-between items-center pt-2 border-t border-gray-200">
                                    <span class="text-lg font-semibold text-gray-900">Total</span>
                                    <span class="text-2xl font-bold text-gray-900" id="paymentTotalDisplay">${total}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Side: Payment Input -->
                <div class="space-y-4">
                    <!-- Payment Method -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select a payment method</label>
                        <select name="payment_method" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent bg-white">
                            <option value="cash" selected>Cash</option>
                            <option value="authorize_net">Authorize.net (Card)</option>
                            <option value="nmi">NMI.com (Card)</option>
                        </select>
                    </div>
                    
                    <!-- Tip Section -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tip</label>
                        <div class="grid grid-cols-4 gap-2 mb-2">
                            <button type="button" onclick="setTipPercentage(10)" class="px-3 py-2 text-sm font-medium text-gray-700 bg-[#e6f0f3] rounded-lg hover:bg-[#b3d1d9] transition active:scale-95 border border-[#b3d1d9]">10%</button>
                            <button type="button" onclick="setTipPercentage(15)" class="px-3 py-2 text-sm font-medium text-gray-700 bg-[#e6f0f3] rounded-lg hover:bg-[#b3d1d9] transition active:scale-95 border border-[#b3d1d9]">15%</button>
                            <button type="button" onclick="setTipPercentage(20)" class="px-3 py-2 text-sm font-medium text-gray-700 bg-[#e6f0f3] rounded-lg hover:bg-[#b3d1d9] transition active:scale-95 border border-[#b3d1d9]">20%</button>
                            <button type="button" onclick="setTipPercentage(25)" class="px-3 py-2 text-sm font-medium text-gray-700 bg-[#e6f0f3] rounded-lg hover:bg-[#b3d1d9] transition active:scale-95 border border-[#b3d1d9]">25%</button>
                        </div>
                        <input type="number" name="tip" id="tipInput" step="0.01" min="0" value="0" oninput="updateTipFromInput()" placeholder="0.00" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" />
                    </div>
                    
                    <!-- Amount Display -->
                    <div class="bg-gray-50 rounded-lg p-6 text-center">
                        <div class="text-5xl font-bold text-gray-900" id="paymentAmount">$0</div>
                        <input type="hidden" id="paymentAmountValue" value="0">
                    </div>
                    
                    <!-- Quick Cash Buttons -->
                    <div class="grid grid-cols-4 gap-2">
                        <button type="button" onclick="addPaymentAmount(5)" class="px-4 py-3 text-sm font-medium text-gray-700 bg-teal-50 rounded-lg hover:bg-teal-100 transition active:scale-95 border border-teal-200">$5</button>
                        <button type="button" onclick="addPaymentAmount(10)" class="px-4 py-3 text-sm font-medium text-gray-700 bg-teal-50 rounded-lg hover:bg-teal-100 transition active:scale-95 border border-teal-200">$10</button>
                        <button type="button" onclick="addPaymentAmount(20)" class="px-4 py-3 text-sm font-medium text-gray-700 bg-teal-50 rounded-lg hover:bg-teal-100 transition active:scale-95 border border-teal-200">$20</button>
                        <button type="button" onclick="addPaymentAmount(50)" class="px-4 py-3 text-sm font-medium text-gray-700 bg-teal-50 rounded-lg hover:bg-teal-100 transition active:scale-95 border border-teal-200">$50</button>
                    </div>
                    
                    <!-- Numeric Keypad -->
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" onclick="addPaymentDigit('1')" class="px-4 py-4 text-lg font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition active:scale-95">1</button>
                        <button type="button" onclick="addPaymentDigit('2')" class="px-4 py-4 text-lg font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition active:scale-95">2</button>
                        <button type="button" onclick="addPaymentDigit('3')" class="px-4 py-4 text-lg font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition active:scale-95">3</button>
                        <button type="button" onclick="addPaymentDigit('4')" class="px-4 py-4 text-lg font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition active:scale-95">4</button>
                        <button type="button" onclick="addPaymentDigit('5')" class="px-4 py-4 text-lg font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition active:scale-95">5</button>
                        <button type="button" onclick="addPaymentDigit('6')" class="px-4 py-4 text-lg font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition active:scale-95">6</button>
                        <button type="button" onclick="addPaymentDigit('7')" class="px-4 py-4 text-lg font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition active:scale-95">7</button>
                        <button type="button" onclick="addPaymentDigit('8')" class="px-4 py-4 text-lg font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition active:scale-95">8</button>
                        <button type="button" onclick="addPaymentDigit('9')" class="px-4 py-4 text-lg font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition active:scale-95">9</button>
                        <button type="button" onclick="addPaymentDigit('.')" class="px-4 py-4 text-lg font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition active:scale-95">.</button>
                        <button type="button" onclick="addPaymentDigit('0')" class="px-4 py-4 text-lg font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition active:scale-95">0</button>
                        <button type="button" onclick="removePaymentDigit()" class="px-4 py-4 text-lg font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition active:scale-95">
                            <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z"></path>
                            </svg>
                        </button>
                    </div>
                    
                    <!-- Pay Now Button -->
                    <form onsubmit="processPayment(event, '${orderId}')">
                        <input type="hidden" name="tip_amount" id="tipAmountHidden" value="0">
                        <button type="submit" class="w-full px-6 py-4 text-lg font-semibold text-white bg-[#003047] rounded-lg hover:bg-[#002535] transition active:scale-95">
                            Pay Now
                        </button>
                    </form>
                </div>
            </div>
        </div>
    `;
    openModal(modalContent, 'large');
    
    // Initialize payment amount to empty
    paymentAmountStr = '';
    
    // Load items for this booking (this would normally come from the server)
    loadPaymentItems(orderId, total);
    
    // Initialize the display
    setTimeout(() => {
        updatePaymentDisplay();
        updatePaymentTotal();
    }, 100);
}

function loadPaymentItems(orderId, total) {
    // This would normally fetch items from the server
    // For now, we'll parse from the orderId or use default items
    const itemsList = document.getElementById('paymentItemsList');
    const itemCountEl = document.getElementById('itemCount');
    
    // Mock items based on order ID (in a real app, this would come from API)
    let items = [];
    if (orderId === 'ORDER925') {
        items = [
            { name: 'Classic Manicure', qty: 1, price: 35.00 },
            { name: 'Gel Polish', qty: 1, price: 20.00 }
        ];
    } else if (orderId === 'ORDER926') {
        items = [
            { name: 'Acrylic Full Set', qty: 1, price: 65.00 },
            { name: 'Nail Art Design', qty: 1, price: 25.00 }
        ];
    } else if (orderId === 'ORDER929') {
        items = [
            { name: 'Spa Pedicure', qty: 1, price: 55.00 },
            { name: 'Gel Polish', qty: 1, price: 20.00 }
        ];
    } else if (orderId === 'ORDER927') {
        items = [
            { name: 'Full Set Gel Extensions', qty: 1, price: 85.00 }
        ];
    } else if (orderId === 'ORDER930') {
        items = [
            { name: 'Gel Manicure', qty: 1, price: 45.00 }
        ];
    }
    
    itemsList.innerHTML = items.map(item => `
        <div class="flex justify-between py-2">
            <span class="text-sm text-gray-900">${item.name} (${item.qty}x)</span>
            <span class="text-sm font-semibold text-gray-900">$${item.price.toFixed(2)}</span>
        </div>
    `).join('');
    
    itemCountEl.textContent = items.length;
}

let paymentAmountStr = '';
function addPaymentDigit(digit) {
    // Check if elements exist
    const displayEl = document.getElementById('paymentAmount');
    if (!displayEl) return;
    
    // Handle decimal point
    if (digit === '.') {
        if (paymentAmountStr === '') {
            paymentAmountStr = '0.';
        } else if (paymentAmountStr.includes('.')) {
            return; // Don't allow multiple decimal points
        } else {
            paymentAmountStr += '.';
        }
    } else {
        // Handle numeric digits
        // Limit to 2 decimal places after decimal point
        if (paymentAmountStr.includes('.')) {
            const parts = paymentAmountStr.split('.');
            if (parts[1] && parts[1].length >= 2) {
                return; // Already has 2 decimal places
            }
        }
        paymentAmountStr += digit;
    }
    updatePaymentDisplay();
}

function removePaymentDigit() {
    // Check if elements exist
    const displayEl = document.getElementById('paymentAmount');
    if (!displayEl) return;
    
    paymentAmountStr = paymentAmountStr.slice(0, -1);
    updatePaymentDisplay();
}

function addPaymentAmount(amount) {
    // Check if elements exist
    const displayEl = document.getElementById('paymentAmount');
    if (!displayEl) return;
    
    const current = parseFloat(paymentAmountStr) || 0;
    paymentAmountStr = (current + amount).toFixed(2);
    updatePaymentDisplay();
}

function updatePaymentDisplay() {
    const displayEl = document.getElementById('paymentAmount');
    const valueEl = document.getElementById('paymentAmountValue');
    
    if (!displayEl || !valueEl) return;
    
    const amount = parseFloat(paymentAmountStr) || 0;
    displayEl.textContent = '$' + amount.toFixed(2);
    valueEl.value = amount.toFixed(2);
}

function setTipPercentage(percentage) {
    if (!window.paymentSubtotal) return;
    
    const subtotal = window.paymentSubtotal;
    const tipAmount = (subtotal * percentage / 100).toFixed(2);
    
    const tipInput = document.getElementById('tipInput');
    const tipAmountHidden = document.getElementById('tipAmountHidden');
    
    if (tipInput) {
        tipInput.value = tipAmount;
    }
    if (tipAmountHidden) {
        tipAmountHidden.value = tipAmount;
    }
    
    window.paymentTip = parseFloat(tipAmount);
    updatePaymentTotal();
}

function updateTipFromInput() {
    const tipInput = document.getElementById('tipInput');
    const tipAmountHidden = document.getElementById('tipAmountHidden');
    
    if (!tipInput) return;
    
    const tipAmount = parseFloat(tipInput.value) || 0;
    
    if (tipAmountHidden) {
        tipAmountHidden.value = tipAmount.toFixed(2);
    }
    
    window.paymentTip = tipAmount;
    updatePaymentTotal();
}

function updatePaymentTotal() {
    if (window.paymentSubtotal === undefined) return;
    
    const subtotal = window.paymentSubtotal;
    const tax = window.paymentTax;
    const tip = window.paymentTip || 0;
    const total = subtotal + tax + tip;
    
    // Update display
    const tipDisplay = document.getElementById('paymentTipDisplay');
    const totalDisplay = document.getElementById('paymentTotalDisplay');
    
    if (tipDisplay) {
        tipDisplay.textContent = '$' + tip.toFixed(2);
    }
    if (totalDisplay) {
        totalDisplay.textContent = '$' + total.toFixed(2);
    }
}

// New Booking Modal Functions
function handleNewBookingServiceCheckboxChange(serviceName, isChecked) {
    const quantityInput = document.querySelector(`.newBooking-service-quantity[data-name="${serviceName}"]`);
    const labelId = `newBooking-service-label-${serviceName.replace(/\s+/g, '-').toLowerCase()}`;
    const label = document.getElementById(labelId);
    const searchInput = document.getElementById('newBookingServiceSearchInput');
    const searchTerm = searchInput ? searchInput.value : '';
    
    // Update quantity
    if (quantityInput) {
        if (isChecked) {
            quantityInput.value = 1;
        } else {
            quantityInput.value = 0;
        }
    }
    
    // Update label styling to highlight checked items
    if (label) {
        if (isChecked) {
            label.classList.remove('bg-white', 'border-gray-200');
            label.classList.add('bg-[#e6f0f3]', 'border-[#b3d1d9]', 'shadow-sm');
        } else {
            label.classList.remove('bg-[#e6f0f3]', 'border-[#b3d1d9]', 'shadow-sm');
            label.classList.add('bg-white', 'border-gray-200');
        }
    }
    
    updateNewBookingQuantityButtons(serviceName);
    calculateNewBookingTotal();
    
    // Re-apply filters if toggle is active
    filterNewBookingServices(searchTerm);
    
    // Update toggle button state
    updateNewBookingToggleSelectedButtonState();
    
    // Update select all button state
    updateNewBookingSelectAllButtonState();
}

// Check if any items are selected in New Booking modal
function hasNewBookingSelectedItems() {
    const checkboxes = document.querySelectorAll('.newBooking-service-checkbox[data-name]');
    for (let checkbox of checkboxes) {
        if (checkbox.checked) {
            return true;
        }
    }
    return false;
}

// Update the toggle "Show Selected" button state in New Booking modal
function updateNewBookingToggleSelectedButtonState() {
    const toggleBtn = document.getElementById('newBookingToggleSelectedBtn');
    if (!toggleBtn) return;
    
    const hasSelected = hasNewBookingSelectedItems();
    
    if (hasSelected) {
        // Enable button
        toggleBtn.disabled = false;
        toggleBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        toggleBtn.classList.add('cursor-pointer');
    } else {
        // Disable button
        toggleBtn.disabled = true;
        toggleBtn.classList.add('opacity-50', 'cursor-not-allowed');
        toggleBtn.classList.remove('cursor-pointer');
        
        // Reset toggle state if no items are selected
        if (window.newBookingShowSelectedOnly) {
            window.newBookingShowSelectedOnly = false;
            const toggleText = document.getElementById('newBookingToggleSelectedText');
            const toggleIcon = document.getElementById('newBookingToggleSelectedIcon');
            if (toggleText) toggleText.textContent = 'Show Selected';
            if (toggleIcon) {
                toggleIcon.classList.remove('text-white');
                toggleIcon.classList.add('text-gray-600');
            }
            toggleBtn.classList.remove('bg-[#003047]', 'border-[#003047]', 'text-white', 'hover:bg-[#002535]');
            toggleBtn.classList.add('border-gray-300', 'hover:bg-gray-50');
        }
    }
}

function incrementNewBookingQuantity(serviceName) {
    // Find the quantity input by checking all inputs with the data-name attribute
    const allQuantityInputs = document.querySelectorAll('.newBooking-service-quantity[data-name]');
    let quantityInput = null;
    
    // Find the matching input by comparing the data-name attribute value
    allQuantityInputs.forEach(input => {
        if (input.getAttribute('data-name') === serviceName) {
            quantityInput = input;
        }
    });
    
    if (quantityInput) {
        let currentValue = parseInt(quantityInput.value) || 0;
        currentValue++;
        quantityInput.value = currentValue;
        
        // Trigger input event to ensure other handlers fire
        quantityInput.dispatchEvent(new Event('input', { bubbles: true }));
        
        // Find the matching checkbox after we've updated the value
        const allCheckboxes = document.querySelectorAll('.newBooking-service-checkbox[data-name]');
        let checkbox = null;
        allCheckboxes.forEach(cb => {
            if (cb.getAttribute('data-name') === serviceName) {
                checkbox = cb;
            }
        });
        
        // Ensure checkbox is checked if quantity > 0
        if (checkbox && currentValue > 0) {
            if (!checkbox.checked) {
                checkbox.checked = true;
                // Update highlighting only if checkbox state changed
                handleNewBookingServiceCheckboxChange(serviceName, true);
            }
        }
        
        updateNewBookingQuantityButtons(serviceName);
        calculateNewBookingTotal();
    }
}

function decrementNewBookingQuantity(serviceName) {
    // Find the quantity input by checking all inputs with the data-name attribute
    const allQuantityInputs = document.querySelectorAll('.newBooking-service-quantity[data-name]');
    let quantityInput = null;
    let checkbox = null;
    
    // Find the matching input by comparing the data-name attribute value
    allQuantityInputs.forEach(input => {
        if (input.getAttribute('data-name') === serviceName) {
            quantityInput = input;
        }
    });
    
    if (quantityInput) {
        let currentValue = parseInt(quantityInput.value) || 0;
        if (currentValue > 0) {
            currentValue--;
            quantityInput.value = currentValue;
            
            // Trigger input event to ensure other handlers fire
            quantityInput.dispatchEvent(new Event('input', { bubbles: true }));
            
            // Find the matching checkbox after we've updated the value
            const allCheckboxes = document.querySelectorAll('.newBooking-service-checkbox[data-name]');
            let checkbox = null;
            allCheckboxes.forEach(cb => {
                if (cb.getAttribute('data-name') === serviceName) {
                    checkbox = cb;
                }
            });
            
            // Uncheck checkbox if quantity becomes 0
            if (checkbox && currentValue === 0) {
                if (checkbox.checked) {
                    checkbox.checked = false;
                    // Update highlighting only if checkbox state changed
                    handleNewBookingServiceCheckboxChange(serviceName, false);
                }
            }
        }
        
        updateNewBookingQuantityButtons(serviceName);
        calculateNewBookingTotal();
    }
}

function handleNewBookingQuantityChange(serviceName, value) {
    const quantity = parseInt(value) || 0;
    const checkbox = document.querySelector(`.newBooking-service-checkbox[data-name="${serviceName}"]`);
    
    // Update checkbox based on quantity
    if (checkbox) {
        const wasChecked = checkbox.checked;
        checkbox.checked = quantity > 0;
        
        // Update highlighting if state changed
        if (wasChecked !== checkbox.checked) {
            handleNewBookingServiceCheckboxChange(serviceName, checkbox.checked);
        }
    }
    
    // Update button states
    updateNewBookingQuantityButtons(serviceName);
    
    // Recalculate total
    calculateNewBookingTotal();
}

function updateNewBookingQuantityFromInput(serviceName, value) {
    // Sync checkbox state while user is typing
    const quantity = parseInt(value) || 0;
    const checkbox = document.querySelector(`.newBooking-service-checkbox[data-name="${serviceName}"]`);
    
    if (checkbox) {
        const wasChecked = checkbox.checked;
        checkbox.checked = quantity > 0;
        
        // Update highlighting if state changed
        if (wasChecked !== checkbox.checked) {
            handleNewBookingServiceCheckboxChange(serviceName, checkbox.checked);
        }
    }
    
    updateNewBookingQuantityButtons(serviceName);
}

function updateNewBookingQuantityButtons(serviceName) {
    const quantityInput = document.querySelector(`.newBooking-service-quantity[data-name="${serviceName}"]`);
    const decrementBtn = document.querySelector(`.newBooking-service-quantity-decrement[data-name="${serviceName}"]`);
    
    if (quantityInput && decrementBtn) {
        const quantity = parseInt(quantityInput.value) || 0;
        if (quantity <= 0) {
            decrementBtn.disabled = true;
            decrementBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            decrementBtn.disabled = false;
            decrementBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }
}

function calculateNewBookingTotal() {
    let total = 0;
    const checkboxes = document.querySelectorAll('.newBooking-service-checkbox:checked');
    
    checkboxes.forEach(checkbox => {
        const quantityInput = document.querySelector(`.newBooking-service-quantity[data-name="${checkbox.dataset.name}"]`);
        const quantity = parseInt(quantityInput.value) || 0;
        const price = parseFloat(checkbox.dataset.price) || 0;
        total += quantity * price;
    });
    
    const totalElement = document.getElementById('newBookingTotal');
    if (totalElement) {
        totalElement.textContent = '$' + total.toFixed(2);
    }
}

function filterNewBookingServices(searchTerm) {
    const searchLower = searchTerm.toLowerCase().trim();
    const serviceItems = document.querySelectorAll('.newBooking-service-item');
    const showSelectedOnly = window.newBookingShowSelectedOnly || false;
    
    serviceItems.forEach(item => {
        const serviceName = item.getAttribute('data-service-name');
        const checkbox = item.querySelector('.newBooking-service-checkbox');
        const isChecked = checkbox && checkbox.checked;
        
        // Check if item matches search term
        const matchesSearch = searchLower === '' || (serviceName && serviceName.includes(searchLower));
        
        // Check if item matches selected filter
        const matchesSelected = !showSelectedOnly || isChecked;
        
        // Show item only if it matches both search and selected filter
        if (matchesSearch && matchesSelected) {
            item.style.display = 'flex';
        } else {
            item.style.display = 'none';
        }
    });
}

function updateNewBookingClearButton(value) {
    const clearBtn = document.getElementById('newBookingClearSearchBtn');
    if (clearBtn) {
        if (value && value.trim() !== '') {
            clearBtn.classList.remove('hidden');
        } else {
            clearBtn.classList.add('hidden');
        }
    }
}

function clearNewBookingSearch() {
    const searchInput = document.getElementById('newBookingServiceSearchInput');
    if (searchInput) {
        searchInput.value = '';
        filterNewBookingServices('');
        updateNewBookingClearButton('');
        searchInput.focus();
    }
}

function toggleNewBookingSelectedOnly() {
    // Don't toggle if button is disabled
    const toggleBtn = document.getElementById('newBookingToggleSelectedBtn');
    if (toggleBtn && toggleBtn.disabled) return;
    
    window.newBookingShowSelectedOnly = !window.newBookingShowSelectedOnly;
    const toggleText = document.getElementById('newBookingToggleSelectedText');
    const toggleIcon = document.getElementById('newBookingToggleSelectedIcon');
    const searchInput = document.getElementById('newBookingServiceSearchInput');
    const searchTerm = searchInput ? searchInput.value : '';
    
    // Update button appearance
    if (window.newBookingShowSelectedOnly) {
        toggleBtn.classList.remove('border-gray-300', 'hover:bg-gray-50');
        toggleBtn.classList.add('bg-[#003047]', 'border-[#003047]', 'text-white', 'hover:bg-[#002535]');
        toggleText.textContent = 'Show All';
        toggleIcon.classList.remove('text-gray-600');
        toggleIcon.classList.add('text-white');
    } else {
        toggleBtn.classList.remove('bg-[#003047]', 'border-[#003047]', 'text-white', 'hover:bg-[#002535]');
        toggleBtn.classList.add('border-gray-300', 'hover:bg-gray-50');
        toggleText.textContent = 'Show Selected';
        toggleIcon.classList.remove('text-white');
        toggleIcon.classList.add('text-gray-600');
    }
    
    // Re-apply filters
    filterNewBookingServices(searchTerm);
}

// Check if all items are selected in New Booking modal
function areNewBookingAllItemsSelected() {
    const checkboxes = document.querySelectorAll('.newBooking-service-checkbox[data-name]');
    if (checkboxes.length === 0) return false;
    
    for (let checkbox of checkboxes) {
        if (!checkbox.checked) {
            return false;
        }
    }
    return true;
}

// Update the Select All button state in New Booking modal
function updateNewBookingSelectAllButtonState() {
    const selectAllBtn = document.getElementById('newBookingSelectAllBtn');
    const selectAllText = document.getElementById('newBookingSelectAllText');
    const selectAllIcon = document.getElementById('newBookingSelectAllIcon');
    
    if (!selectAllBtn || !selectAllText || !selectAllIcon) return;
    
    const allSelected = areNewBookingAllItemsSelected();
    const path = selectAllIcon.querySelector('path');
    
    if (allSelected) {
        selectAllText.textContent = 'Unselect All';
        if (path) {
            path.setAttribute('d', 'M6 18L18 6M6 6l12 12');
        }
    } else {
        selectAllText.textContent = 'Select All';
        if (path) {
            path.setAttribute('d', 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4');
        }
    }
}

// Toggle select/unselect all items in New Booking modal
function toggleNewBookingSelectAll() {
    const allSelected = areNewBookingAllItemsSelected();
    const checkboxes = document.querySelectorAll('.newBooking-service-checkbox[data-name]');
    
    checkboxes.forEach(checkbox => {
        const serviceName = checkbox.getAttribute('data-name');
        const shouldSelect = !allSelected;
        
        if (checkbox.checked !== shouldSelect) {
            checkbox.checked = shouldSelect;
            handleNewBookingServiceCheckboxChange(serviceName, shouldSelect);
        }
    });
    
    // Update button state
    updateNewBookingSelectAllButtonState();
}

function saveWalkIn(event) {
    event.preventDefault();
    
    // Check if using existing customer or new customer
    if (window.selectedCustomer) {
        // Using existing customer
        const customerName = `${window.selectedCustomer.firstName} ${window.selectedCustomer.lastName}`;
        showSuccessMessage(`Booking created for ${customerName}!`);
    } else {
        // Using new customer form
        const firstNameInput = document.getElementById('first_name');
        const lastNameInput = document.getElementById('last_name');
        if (firstNameInput && lastNameInput) {
            const firstName = firstNameInput.value;
            const lastName = lastNameInput.value;
            const customerName = `${firstName} ${lastName}`;
            showSuccessMessage(`New customer ${customerName} added to booking!`);
        } else {
            showSuccessMessage('Booking created successfully!');
        }
    }
    
    closeModal();
    setTimeout(() => location.reload(), 1500);
}

function processPayment(event, orderId) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const paymentMethod = formData.get('payment_method');
    const tipAmount = parseFloat(document.getElementById('tipAmountHidden').value) || 0;
    const paymentAmount = parseFloat(document.getElementById('paymentAmountValue').value) || 0;
    
    // Get totals
    const subtotal = window.paymentSubtotal || 0;
    const tax = window.paymentTax || 0;
    const tip = tipAmount;
    const total = subtotal + tax + tip;
    
    // Validate payment amount
    if (paymentAmount < total) {
        showErrorMessage('Payment amount is less than the total amount. Please enter a valid amount.');
        return;
    }
    
    // Calculate change if cash payment
    const change = paymentMethod === 'cash' ? (paymentAmount - total).toFixed(2) : 0;
    
    // In a real application, you would send this data to the server
    console.log('Processing payment:', {
        orderId,
        paymentMethod,
        subtotal,
        tax,
        tip,
        total,
        paymentAmount,
        change
    });
    
    showSuccessMessage('Payment processed successfully!');
    closeModal();
    setTimeout(() => location.reload(), 1500);
}

function showErrorMessage(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    errorDiv.textContent = message;
    document.body.appendChild(errorDiv);
    setTimeout(() => errorDiv.remove(), 3000);
}

function cancelOrder(orderId) {
    if (confirm('Are you sure you want to cancel this booking?')) {
        showSuccessMessage('Booking cancelled successfully!');
        setTimeout(() => location.reload(), 1500);
    }
}

function showSuccessMessage(message) {
    const successDiv = document.createElement('div');
    successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    successDiv.textContent = message;
    document.body.appendChild(successDiv);
    setTimeout(() => successDiv.remove(), 3000);
}

function filterByStatus(status) {
    const cards = document.querySelectorAll('.bg-white.rounded-lg.shadow-sm');
    cards.forEach(card => {
        // Find the status badge - it's a span with rounded-full class
        const statusBadge = card.querySelector('span.rounded-full');
        if (!statusBadge) return;
        
        const statusText = statusBadge.textContent.trim().toLowerCase();
        let cardStatus = '';
        
        // Map status badge text to filter values
        if (statusText.includes('waiting') || statusText.includes('in booking')) {
            cardStatus = 'waiting';
        } else if (statusText.includes('in progress')) {
            cardStatus = 'in-progress';
        } else if (statusText.includes('completed')) {
            cardStatus = 'completed';
        } else if (statusText.includes('paid')) {
            cardStatus = 'paid';
        }
        
        if (status === '' || cardStatus === status) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

function openReceiptModal(customerName, orderId, total, date) {
    const modalContent = `
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">Receipt</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="space-y-4">
                <div class="text-center border-b border-gray-200 pb-4">
                    <h4 class="font-bold text-lg text-gray-900">Nail Salon POS</h4>
                    <p class="text-sm text-gray-600">123 Main Street</p>
                    <p class="text-sm text-gray-600">(555) 123-4567</p>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Booking #:</span>
                        <span class="text-sm font-medium text-gray-900">${orderId}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Date:</span>
                        <span class="text-sm font-medium text-gray-900">${date}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Customer:</span>
                        <span class="text-sm font-medium text-gray-900">${customerName}</span>
                    </div>
                </div>
                <div class="border-t border-gray-200 pt-4">
                    <div class="flex justify-between items-center">
                        <span class="text-lg font-semibold text-gray-900">Total</span>
                        <span class="text-2xl font-bold text-gray-900">${total}</span>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-6">
                <button onclick="window.print()" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">
                    Print Receipt
                </button>
                <button onclick="closeModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">
                    Close
                </button>
            </div>
        </div>
    `;
    openModal(modalContent);
}

// Close confirmation dialog on Escape key (only if it's open)
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const confirmOverlay = document.getElementById('confirmDialogOverlay');
        if (confirmOverlay && !confirmOverlay.classList.contains('hidden')) {
            closeConfirmDialog();
        }
    }
});

</script>

<?php include '../includes/footer.php'; ?>








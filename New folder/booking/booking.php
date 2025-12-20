<?php
$pageTitle = 'Walk-In';
include '../includes/header.php';
include '../includes/modal.php';
?>

<!-- Main Content Area -->
<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-6">
                <a href="index.php" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    <span class="text-sm font-medium">Go to Queue</span>
                </a>
                <button id="miniCartButton" onclick="toggleMiniCart()" class="relative px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm sm:text-base flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <span>Cart</span>
                    <span id="cartBadge" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full w-6 h-6 flex items-center justify-center hidden">0</span>
                </button>
            </div>

            <!-- Step Indicator -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
                <div class="flex items-center justify-between">
                    <!-- Step 1: Customer Details -->
                    <div id="step1Container" class="flex items-center flex-1 step-container">
                        <div id="step1Indicator" class="flex items-center justify-center w-10 h-10 rounded-full bg-[#003047] text-white font-semibold text-sm mr-3">
                            1
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Customer Details</p>
                            <p class="text-xs text-gray-500">Select Customer</p>
                        </div>
                    </div>
                    
                    <!-- Connector Line -->
                    <div class="flex-1 h-0.5 bg-gray-300 mx-4"></div>
                    
                    <!-- Step 2: Order -->
                    <div id="step2Container" class="flex items-center flex-1 step-container">
                        <div id="step2Indicator" class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-200 text-gray-600 font-semibold text-sm mr-3">
                            2
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-600">Order</p>
                            <p class="text-xs text-gray-500">Select Services</p>
                        </div>
                    </div>
                    
                    <!-- Connector Line -->
                    <div class="flex-1 h-0.5 bg-gray-300 mx-4"></div>
                    
                    <!-- Step 3: Cart -->
                    <div id="step3Container" class="flex items-center flex-1 step-container">
                        <div id="step3Indicator" class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-200 text-gray-600 font-semibold text-sm mr-3">
                            3
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-600">Cart</p>
                            <p class="text-xs text-gray-500">Review Items</p>
                        </div>
                    </div>
                    
                    <!-- Connector Line -->
                    <div class="flex-1 h-0.5 bg-gray-300 mx-4"></div>
                    
                    <!-- Step 4: Checkout -->
                    <div id="step4Container" class="flex items-center flex-1 step-container">
                        <div id="step4Indicator" class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-200 text-gray-600 font-semibold text-sm mr-3">
                            4
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-600">Checkout</p>
                            <p class="text-xs text-gray-500">Complete Order</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Layout: Categories Sidebar + Form -->
        <div class="flex flex-col lg:flex-row gap-6 h-[calc(100vh-200px)]">
            <!-- Left Sidebar: Categories (Only show in Step 2 - Order) -->
            <div id="categoriesSidebar" class="w-full lg:w-64 flex-shrink-0 flex flex-col hidden">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 flex flex-col h-full">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex-shrink-0">Categories</h2>
                    <div id="categoriesList" class="space-y-2 overflow-y-auto flex-1 pr-2">
                        <!-- Categories will be loaded dynamically -->
                    </div>
                </div>
            </div>

            <!-- Right Content: Multi-Step Form -->
            <div class="flex-1 min-w-0 flex flex-col">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col h-full overflow-hidden">
                    <form onsubmit="saveWalkIn(event)" class="space-y-6 flex flex-col h-full overflow-y-auto">
                        <!-- Step 1: Customer Details -->
                        <div id="step1" class="step-content flex flex-col flex-1 overflow-hidden">
                            <!-- Customer Search/Selection -->
                            <div id="customerSearchSection" class="mb-6 flex-shrink-0">
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
                            
                            <!-- Booking Type Selection -->
                            <div class="mb-6 flex-shrink-0">
                                <p class="text-sm text-gray-600 font-medium mb-3">Booking Type</p>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="relative flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-[#003047] transition-all duration-200 has-[:checked]:border-[#003047] has-[:checked]:bg-[#e6f0f3]">
                                        <input type="radio" name="bookingType" value="walk-in" id="bookingTypeWalkIn" checked class="sr-only peer" onchange="updateBookingType(this.value)">
                                        <div class="flex items-center gap-3 flex-1">
                                            <div class="w-5 h-5 border-2 border-gray-300 rounded-full flex items-center justify-center transition-all peer-checked:border-[#003047] peer-checked:bg-[#003047]" style="border-color: var(--primary-color, #003047);">
                                                <div class="w-2 h-2 bg-white rounded-full opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-sm font-semibold text-gray-900">Walk-In</p>
                                                <p class="text-xs text-gray-500">Immediate service</p>
                                            </div>
                                        </div>
                                    </label>
                                    <label class="relative flex items-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-[#003047] transition-all duration-200 has-[:checked]:border-[#003047] has-[:checked]:bg-[#e6f0f3]">
                                        <input type="radio" name="bookingType" value="appointment" id="bookingTypeAppointment" class="sr-only peer" onchange="updateBookingType(this.value)">
                                        <div class="flex items-center gap-3 flex-1">
                                            <div class="w-5 h-5 border-2 border-gray-300 rounded-full flex items-center justify-center transition-all peer-checked:border-[#003047] peer-checked:bg-[#003047]" style="border-color: var(--primary-color, #003047);">
                                                <div class="w-2 h-2 bg-white rounded-full opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-sm font-semibold text-gray-900">Book Appointment</p>
                                                <p class="text-xs text-gray-500">Schedule for later</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            
                            <!-- Appointment Scheduling Section (Only shown when Book Appointment is selected) -->
                            <div id="appointmentSchedulingSection" class="mb-6 hidden flex flex-col flex-1 min-h-0 max-h-[calc(100vh-400px)] pr-2">
                                <p class="text-sm text-gray-600 font-medium mb-3 flex-shrink-0">Select Date & Time</p>
                                <div class="grid grid-cols-1 lg:grid-cols-[30%_70%] gap-6 flex-1 min-h-0">
                                    <!-- Left: Calendar -->
                                    <div class="bg-white border border-gray-200 rounded-lg p-4 overflow-y-auto">
                                        <div id="appointmentCalendar" class="w-full">
                                            <!-- Calendar will be rendered here -->
                                        </div>
                                    </div>
                                    
                                    <!-- Right: Booking Time -->
                                    <div class="bg-white border border-gray-200 rounded-lg p-4 flex flex-col overflow-hidden">
                                        <h3 class="text-lg font-semibold text-gray-900 mb-3 flex-shrink-0">Preffered Time</h3>
                                        <div id="availableTimeSlots" class="space-y-2 overflow-y-auto flex-1 min-h-0">
                                            <!-- Time slots will be populated here -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Step 1 Navigation - Fixed at Bottom -->
                            <div class="flex justify-end gap-3 pt-6 border-t border-gray-200 flex-shrink-0 bg-white sticky bottom-0 mt-auto">
                                <button type="button" onclick="goToStep(2)" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95 flex items-center gap-2">
                                    Next: Order
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Step 2: Order -->
                        <div id="step2" class="step-content hidden flex flex-col flex-1 overflow-hidden">
                            <div class="mb-3 flex-shrink-0 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                                <div>
                                    <h2 class="text-xl font-bold text-gray-900 mb-1">Select Services</h2>
                                    <p class="text-sm text-gray-600">Choose services to add to your cart</p>
                                </div>
                                <!-- Search Input -->
                                <div class="flex-shrink-0 w-full sm:w-auto sm:min-w-[300px]">
                                    <div class="relative">
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
                                </div>
                            </div>
                            
                            <!-- Services Section -->
                            <div class="flex flex-col flex-1 min-h-0">
                                <div class="bg-gray-50 rounded-lg p-4 flex-1 overflow-y-auto min-h-0" id="newBookingServicesListContainer">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                                        <!-- Services will be populated by JavaScript -->
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Step 2 Navigation -->
                            <div class="flex justify-between gap-3 pt-6 border-t border-gray-200 flex-shrink-0">
                                <button type="button" onclick="goToStep(1)" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                    Back
                                </button>
                                <button type="button" onclick="goToStep(3)" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95 flex items-center gap-2">
                                    Next: Cart
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Step 3: Cart -->
                        <div id="step3" class="step-content hidden flex flex-col flex-1 overflow-hidden">
                            <div class="mb-6 flex-shrink-0">
                                <h2 class="text-xl font-bold text-gray-900 mb-2">Review Your Cart</h2>
                                <p class="text-sm text-gray-600">Review and adjust quantities of selected services</p>
                            </div>
                            
                            <div id="cartItemsContainer" class="space-y-4 mb-6 flex-1 overflow-y-auto min-h-0">
                                <!-- Cart items will be populated by JavaScript -->
                            </div>
                            
                            <!-- Cart Total -->
                            <div class="pt-4 border-t border-gray-200 mb-6 flex-shrink-0">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-semibold text-gray-900">Total</span>
                                    <span id="cartTotal" class="text-2xl font-bold text-[#003047]">$0.00</span>
                                </div>
                            </div>
                            
                            <!-- Step 2 Navigation -->
                            <div class="flex justify-between gap-3 pt-6 border-t border-gray-200 flex-shrink-0">
                                <button type="button" onclick="goToStep(1)" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                    Back
                                </button>
                                <button type="button" onclick="goToStep(4)" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95 flex items-center gap-2">
                                    Next: Checkout
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Step 4: Checkout -->
                        <div id="step4" class="step-content hidden flex flex-col flex-1 overflow-hidden">
                            <div class="mb-6 flex-shrink-0">
                                <h2 class="text-xl font-bold text-gray-900 mb-2">Checkout</h2>
                                <p class="text-sm text-gray-600">Complete your order details</p>
                            </div>
                            
                            <div class="flex-1 overflow-y-auto min-h-0 space-y-6">
                                <!-- Order Summary -->
                                <div class="bg-gray-50 rounded-lg p-4 flex-shrink-0">
                                    <h3 class="text-sm font-semibold text-gray-900 mb-3">Order Summary</h3>
                                    <div id="checkoutSummary" class="space-y-2 mb-4">
                                        <!-- Order summary will be populated by JavaScript -->
                                    </div>
                                    <div class="pt-3 border-t border-gray-300">
                                        <div class="flex justify-between items-center">
                                            <span class="text-base font-semibold text-gray-900">Total</span>
                                            <span id="checkoutTotal" class="text-xl font-bold text-[#003047]">$0.00</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Customer Info Display -->
                                <div id="checkoutCustomerInfo" class="flex-shrink-0">
                                    <!-- Customer info will be populated by JavaScript -->
                                </div>
                                
                                <!-- Additional Notes -->
                                <div class="flex-shrink-0">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                                    <textarea id="orderNotes" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Add any special instructions or notes..."></textarea>
                                </div>
                            </div>
                            
                            <!-- Step 4 Navigation -->
                            <div class="flex justify-between gap-3 pt-6 border-t border-gray-200 flex-shrink-0">
                                <button type="button" onclick="goToStep(3)" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                    Back
                                </button>
                                <div class="flex gap-3">
                                    <button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Add to Queue
                                    </button>
                                    <button type="button" onclick="openPaymentModalFromCheckout()" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium active:scale-95 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                        </svg>
                                        Pay
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Mini Cart Slide-in Panel -->
<div id="miniCartOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-[70] hidden transition-opacity duration-300" onclick="toggleMiniCart()"></div>
<div id="miniCartPanel" class="fixed top-0 right-0 h-full w-full sm:w-96 bg-white shadow-2xl z-[80] transform translate-x-full transition-transform duration-200 ease-out flex flex-col">
    <div class="p-6 flex flex-col flex-1 min-h-0">
        <!-- Mini Cart Header -->
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200 flex-shrink-0">
            <h2 class="text-xl font-bold text-gray-900">Your Cart</h2>
            <button onclick="toggleMiniCart()" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <!-- Cart Items - Scrollable -->
        <div id="miniCartItems" class="space-y-4 flex-1 overflow-y-auto min-h-0 mb-6">
            <!-- Cart items will be populated by JavaScript -->
        </div>
        
        <!-- Cart Total and Action Buttons - Fixed at Bottom -->
        <div class="flex-shrink-0 bg-white border-t border-gray-200 pt-4 pb-6 -mx-6 px-6">
            <!-- Cart Total -->
            <div class="mb-4">
                <div class="flex justify-between items-center">
                    <span class="text-lg font-semibold text-gray-900">Total</span>
                    <span id="miniCartTotal" class="text-2xl font-bold text-[#003047]">$0.00</span>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div id="miniCartActionButtons" class="space-y-3">
                <button onclick="closeMiniCartAndGoToCart()" class="w-full px-4 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium">
                    View Cart
                </button>
                <button onclick="closeMiniCartAndGoToCheckout()" class="w-full px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium">
                    Proceed to Checkout
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add Customer Modal Overlay -->
<div id="addCustomerModalOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-[60] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <div id="addCustomerModalContent">
            <!-- Add Customer modal content will be inserted here -->
        </div>
    </div>
</div>

<script>
// Mock customer data (in a real app, this would come from a database)
let mockCustomers = [
    { id: 1, firstName: 'Sarah', lastName: 'Johnson', phone: '(555) 123-4567', email: 'sarah.j@email.com' },
    { id: 2, firstName: 'Emily', lastName: 'Chen', phone: '(555) 234-5678', email: 'emily.c@email.com' },
    { id: 3, firstName: 'Jessica', lastName: 'Martinez', phone: '(555) 345-6789', email: 'jessica.m@email.com' },
    { id: 4, firstName: 'Amanda', lastName: 'Taylor', phone: '(555) 456-7890', email: 'amanda.t@email.com' },
    { id: 5, firstName: 'Michelle', lastName: 'Brown', phone: '(555) 567-8901', email: 'michelle.b@email.com' },
    { id: 6, firstName: 'Rachel', lastName: 'Green', phone: '(555) 678-9012', email: 'rachel.g@email.com' }
];

// Services and categories data
let servicesData = [];
let categoriesMap = {};
let selectedCategory = null;

// Cart to store selected services
let cart = [];

// Step management
let currentStep = 1;

// Booking type (walk-in or appointment)
let bookingType = 'walk-in';

// Fetch categories and services
async function fetchCategoriesAndServices() {
    try {
        // Fetch categories
        const categoriesResponse = await fetch('../json/service-categories.json');
        const categoriesData = await categoriesResponse.json();
        categoriesMap = categoriesData.categories;
        
        // Fetch services
        const servicesResponse = await fetch('../json/services.json');
        const servicesDataResponse = await servicesResponse.json();
        servicesData = servicesDataResponse.services.filter(service => service.active);
        
        // Initialize UI
        initializeCategoriesList();
        initializeServicesList();
    } catch (error) {
        console.error('Error fetching data:', error);
        // Fallback to hardcoded services
        servicesData = [
            { id: 1, name: 'Classic Manicure', price: 35.00, categories: ['manicures', 'polish-services'] },
            { id: 2, name: 'Gel Manicure', price: 45.00, categories: ['manicures', 'gel-services', 'polish-services'] },
            { id: 3, name: 'Gel Polish', price: 20.00, categories: ['gel-services', 'polish-services', 'add-ons'] },
            { id: 4, name: 'Spa Pedicure', price: 55.00, categories: ['pedicures', 'spa-services'] },
            { id: 5, name: 'Acrylic Full Set', price: 65.00, categories: ['acrylic-services', 'extensions'] },
            { id: 6, name: 'Nail Art Design', price: 25.00, categories: ['nail-art', 'add-ons'] },
            { id: 7, name: 'Gel Extensions', price: 85.00, categories: ['gel-services', 'extensions'] }
        ];
        initializeCategoriesList();
        initializeServicesList();
    }
}

// Initialize categories list
function initializeCategoriesList() {
    const categoriesList = document.getElementById('categoriesList');
    if (!categoriesList) return;
    
    // Add "All Categories" option
    let categoriesHTML = `
        <button type="button" 
                onclick="filterByCategory(null)" 
                class="category-card w-full text-left p-3 bg-[#003047] rounded-lg hover:bg-[#002535] transition-all duration-200 font-medium text-sm shadow-sm active:scale-95" 
                data-category-key="all">
            <div class="flex items-center justify-between">
                <span class="text-white">All Categories</span>
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
        </button>
    `;
    
    // Add category cards sorted by display name
    const sortedCategories = Object.entries(categoriesMap).sort((a, b) => a[1].localeCompare(b[1]));
    sortedCategories.forEach(([key, displayName]) => {
        categoriesHTML += `
            <button type="button" 
                    onclick="filterByCategory('${key}')" 
                    class="category-card w-full text-left p-3 bg-white border border-gray-200 rounded-lg hover:border-[#003047] hover:bg-[#e6f0f3] transition-all duration-200 font-medium text-sm active:scale-95" 
                    data-category-key="${key}">
                <div class="flex items-center justify-between">
                    <span class="text-gray-700">${displayName}</span>
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </button>
        `;
    });
    
    categoriesList.innerHTML = categoriesHTML;
}

// Filter services by category
function filterByCategory(categoryKey) {
    selectedCategory = categoryKey;
    
    // Update category card styles
    document.querySelectorAll('.category-card').forEach(card => {
        const cardKey = card.getAttribute('data-category-key');
        const span = card.querySelector('span');
        const svg = card.querySelector('svg');
        
        if (categoryKey === null && cardKey === 'all') {
            // Active state
            card.classList.remove('bg-white', 'border-gray-200');
            card.classList.add('bg-[#003047]', 'hover:bg-[#002535]');
            if (span) {
                span.classList.remove('text-gray-700');
                span.classList.add('text-white');
            }
            if (svg) {
                svg.classList.remove('text-gray-400');
                svg.classList.add('text-white');
            }
        } else if (categoryKey === cardKey) {
            // Active state
            card.classList.remove('bg-white', 'border-gray-200');
            card.classList.add('bg-[#003047]', 'hover:bg-[#002535]');
            if (span) {
                span.classList.remove('text-gray-700');
                span.classList.add('text-white');
            }
            if (svg) {
                svg.classList.remove('text-gray-400');
                svg.classList.add('text-white');
            }
        } else {
            // Inactive state
            card.classList.remove('bg-[#003047]', 'hover:bg-[#002535]');
            card.classList.add('bg-white', 'border-gray-200');
            if (span) {
                span.classList.remove('text-white');
                span.classList.add('text-gray-700');
            }
            if (svg) {
                svg.classList.remove('text-white');
                svg.classList.add('text-gray-400');
            }
        }
    });
    
    // Filter and re-render services
    initializeServicesList();
}

// Initialize services list on page load
document.addEventListener('DOMContentLoaded', function() {
    fetchCategoriesAndServices();
    window.selectedCustomer = null;
    // Initialize cart badge
    updateCartBadge(0);
    
    // Check URL for step parameter
    const urlStep = getStepFromURL();
    if (urlStep) {
        // Navigate to step from URL (without validation to allow direct URL access)
        currentStep = urlStep;
        document.querySelectorAll('.step-content').forEach(stepEl => {
            stepEl.classList.add('hidden');
        });
        const stepElement = document.getElementById('step' + urlStep);
        if (stepElement) {
            stepElement.classList.remove('hidden');
        }
        updateStepIndicators(urlStep);
        
        // Show/hide categories sidebar
        const categoriesSidebar = document.getElementById('categoriesSidebar');
        if (categoriesSidebar) {
            if (urlStep === 2) {
                categoriesSidebar.classList.remove('hidden');
            } else {
                categoriesSidebar.classList.add('hidden');
            }
        }
        
        // Render cart if on step 3
        if (urlStep === 3) {
            renderCartItems();
        }
        
        // Update checkout summary if on step 4
        if (urlStep === 4) {
            updateCheckoutSummary();
        }
    } else {
        // Initialize step indicators to step 1
        updateStepIndicators(1);
        // Update URL to step 1
        updateURLStep(1);
    }
    
    // Initialize calendar month/year variables
    const now = new Date();
    window.currentCalendarMonth = now.getMonth();
    window.currentCalendarYear = now.getFullYear();
    
    // Check if appointment is selected and initialize calendar
    const appointmentRadio = document.getElementById('bookingTypeAppointment');
    if (appointmentRadio && appointmentRadio.checked) {
        updateBookingType('appointment');
    }
});

// Handle browser back/forward buttons
window.addEventListener('popstate', function(event) {
    const urlStep = getStepFromURL();
    if (urlStep) {
        // Navigate to step from URL without validation
        currentStep = urlStep;
        document.querySelectorAll('.step-content').forEach(stepEl => {
            stepEl.classList.add('hidden');
        });
        const stepElement = document.getElementById('step' + urlStep);
        if (stepElement) {
            stepElement.classList.remove('hidden');
        }
        updateStepIndicators(urlStep);
        
        // Show/hide categories sidebar
        const categoriesSidebar = document.getElementById('categoriesSidebar');
        if (categoriesSidebar) {
            if (urlStep === 2) {
                categoriesSidebar.classList.remove('hidden');
            } else {
                categoriesSidebar.classList.add('hidden');
            }
        }
        
        // Render cart if on step 3
        if (urlStep === 3) {
            renderCartItems();
        }
        
        // Update checkout summary if on step 4
        if (urlStep === 4) {
            updateCheckoutSummary();
        }
    } else {
        // Default to step 1
        goToStep(1);
    }
});

function initializeServicesList() {
    const container = document.getElementById('newBookingServicesListContainer');
    if (!container) return;
    
    // Get the grid container (first child)
    const gridContainer = container.querySelector('.grid') || container;
    
    // Filter services by selected category
    let filteredServices = servicesData.length > 0 ? servicesData : [];
    if (selectedCategory !== null && servicesData.length > 0) {
        filteredServices = servicesData.filter(service => 
            service.categories && service.categories.includes(selectedCategory)
        );
    }
    
    // Also apply search filter if active
    const searchInput = document.getElementById('newBookingServiceSearchInput');
    if (searchInput && searchInput.value.trim() !== '') {
        const searchTerm = searchInput.value.toLowerCase();
        filteredServices = filteredServices.filter(service => 
            service.name.toLowerCase().includes(searchTerm)
        );
    }
    
    
    // Color variations for thumbnails
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
    
    let servicesHTML = '';
    if (filteredServices.length === 0) {
        servicesHTML = `
            <div class="col-span-full text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-gray-500 text-sm">No services found</p>
            </div>
        `;
    } else {
        filteredServices.forEach((service, index) => {
            const serviceId = service.name.replace(/\s+/g, '-').toLowerCase();
            const color = colorClasses[index % colorClasses.length];
            
            servicesHTML += `
                <div id="newBooking-service-label-${serviceId}" class="newBooking-service-item service-item bg-white border border-gray-200 rounded-lg overflow-hidden hover:border-[#003047] hover:shadow-md transition-all" data-service-name="${service.name.toLowerCase()}">
                    <!-- Thumbnail -->
                    <div class="w-full h-32 ${color.bg} flex items-center justify-center">
                        <svg class="w-12 h-12 ${color.text}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                    </div>
                    
                    <!-- Content -->
                    <div class="p-4">
                        <h3 class="text-sm font-semibold text-gray-900 mb-1">${service.name}</h3>
                        <p class="text-lg font-bold text-[#003047] mb-3">$${service.price.toFixed(2)}</p>
                        
                        <!-- Add to Cart Button -->
                        <button type="button" 
                                onclick="addServiceToCart('${service.name.replace(/'/g, "\\'")}', ${service.price})" 
                                class="w-full px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm active:scale-95 flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            Add to Cart
                        </button>
                    </div>
                </div>
            `;
        });
    }
    
    // Update grid container or container directly
    if (gridContainer.classList.contains('grid')) {
        gridContainer.innerHTML = servicesHTML;
    } else {
        // If no grid container exists, create one
        container.innerHTML = `<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">${servicesHTML}</div>`;
    }
    
    calculateNewBookingTotal();
}

// Helper function to get existing quantity for a service from cart
function getExistingQuantity(serviceName) {
    const item = cart.find(item => item.name === serviceName);
    return item ? item.quantity : 0;
}

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

// Selected date and time for appointment
let selectedAppointmentDate = null;
let selectedAppointmentTime = null;

function updateBookingType(type) {
    bookingType = type;
    console.log('Booking type updated to:', bookingType);
    
    // Show/hide appointment scheduling section
    const appointmentSection = document.getElementById('appointmentSchedulingSection');
    if (appointmentSection) {
        if (type === 'appointment') {
            appointmentSection.classList.remove('hidden');
            initializeAppointmentCalendar();
        } else {
            appointmentSection.classList.add('hidden');
            selectedAppointmentDate = null;
            selectedAppointmentTime = null;
        }
    }
}

function initializeAppointmentCalendar() {
    const calendarContainer = document.getElementById('appointmentCalendar');
    if (!calendarContainer) return;
    
    // Generate calendar for current month
    const now = new Date();
    const currentMonth = now.getMonth();
    const currentYear = now.getFullYear();
    
    renderCalendar(currentMonth, currentYear);
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
    window.currentCalendarMonth = month;
    window.currentCalendarYear = year;
}

function changeMonth(direction) {
    if (!window.currentCalendarMonth && window.currentCalendarMonth !== 0) {
        const now = new Date();
        window.currentCalendarMonth = now.getMonth();
        window.currentCalendarYear = now.getFullYear();
    }
    
    window.currentCalendarMonth += direction;
    if (window.currentCalendarMonth < 0) {
        window.currentCalendarMonth = 11;
        window.currentCalendarYear--;
    } else if (window.currentCalendarMonth > 11) {
        window.currentCalendarMonth = 0;
        window.currentCalendarYear++;
    }
    
    renderCalendar(window.currentCalendarMonth, window.currentCalendarYear);
    updateAvailableTimeSlots();
}

function selectAppointmentDate(year, month, day) {
    selectedAppointmentDate = new Date(year, month, day);
    selectedAppointmentTime = null;
    renderCalendar(window.currentCalendarMonth || month, window.currentCalendarYear || year);
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
            const displayTime = `${hour > 12 ? hour - 12 : hour}:${minute.toString().padStart(2, '0')} ${hour >= 12 ? 'PM' : 'AM'}`;
            timeSlots.push({ value: timeString, display: displayTime });
        }
    }
    
    // Filter out past times for today
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
        const [hours] = slot.value.split(':').map(Number);
        return hours < 12; // 9:00 AM - 11:30 AM
    });
    
    const afternoonSlots = availableSlots.filter(slot => {
        const [hours] = slot.value.split(':').map(Number);
        return hours >= 12; // 12:00 PM - 5:30 PM
    });
    
    let slotsHTML = '';
    
    // Morning Section
    if (morningSlots.length > 0) {
        slotsHTML += `
            <div class="mb-4">
                <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Morning</h4>
                <div class="grid grid-cols-3 gap-2">
        `;
        morningSlots.forEach(slot => {
            const isSelected = selectedAppointmentTime === slot.value;
            slotsHTML += `
                <button type="button" 
                        onclick="selectAppointmentTime('${slot.value}')" 
                        class="px-3 py-2 text-sm font-medium rounded-lg border transition ${
                            isSelected 
                                ? 'bg-[#003047] text-white border-[#003047]' 
                                : 'bg-white text-gray-700 border-gray-300 hover:border-[#003047] hover:bg-[#e6f0f3]'
                        }">
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
                <div class="grid grid-cols-3 gap-2">
        `;
        afternoonSlots.forEach(slot => {
            const isSelected = selectedAppointmentTime === slot.value;
            slotsHTML += `
                <button type="button" 
                        onclick="selectAppointmentTime('${slot.value}')" 
                        class="px-3 py-2 text-sm font-medium rounded-lg border transition ${
                            isSelected 
                                ? 'bg-[#003047] text-white border-[#003047]' 
                                : 'bg-white text-gray-700 border-gray-300 hover:border-[#003047] hover:bg-[#e6f0f3]'
                        }">
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
    
    // Close the Add Customer modal
    closeAddCustomerModal();
    
    // Select the newly created customer
    setTimeout(() => {
        selectCustomer(newId);
        showSuccessMessage(`Customer ${firstName} ${lastName} added and selected!`);
    }, 100);
}

// Add service to cart
function addServiceToCart(serviceName, price) {
    const existingItem = cart.find(item => item.name === serviceName);
    
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            name: serviceName,
            price: price,
            quantity: 1
        });
    }
    
    calculateNewBookingTotal();
    showSuccessMessage(`${serviceName} added to cart!`);
    // Update mini cart if it's open
    if (document.getElementById('miniCartPanel')?.classList.contains('translate-x-0')) {
        renderMiniCart();
    }
}

// Update cart item quantity
function updateCartQuantity(serviceName, quantity) {
    const item = cart.find(item => item.name === serviceName);
    if (item) {
        if (quantity <= 0) {
            cart = cart.filter(item => item.name !== serviceName);
            // If item removed, need to re-render mini cart
            const miniCartPanel = document.getElementById('miniCartPanel');
            if (miniCartPanel && miniCartPanel.classList.contains('translate-x-0')) {
                renderMiniCart();
            }
        } else {
            item.quantity = quantity;
            // Update mini cart elements directly for instant feedback
            const miniCartPanel = document.getElementById('miniCartPanel');
            if (miniCartPanel && miniCartPanel.classList.contains('translate-x-0')) {
                const itemId = serviceName.replace(/[^a-zA-Z0-9]/g, '-').toLowerCase();
                const qtyElement = document.getElementById('miniCartQty-' + itemId);
                const priceElement = document.getElementById('miniCartPrice-' + itemId);
                if (qtyElement) {
                    qtyElement.textContent = quantity;
                }
                if (priceElement) {
                    priceElement.textContent = '$' + (item.price * quantity).toFixed(2);
                }
            }
        }
        calculateNewBookingTotal();
        // Re-render cart if we're on step 3
        if (currentStep === 3) {
            renderCartItems();
        }
    }
}

// Remove item from cart
function removeFromCart(serviceName) {
    cart = cart.filter(item => item.name !== serviceName);
    calculateNewBookingTotal();
    showSuccessMessage(`${serviceName} removed from cart`);
    // Re-render cart if we're on step 3
    if (currentStep === 3) {
        renderCartItems();
    }
    // Update mini cart if it's open
    if (document.getElementById('miniCartPanel')?.classList.contains('translate-x-0')) {
        renderMiniCart();
    }
}

function calculateNewBookingTotal() {
    let total = 0;
    let totalItems = 0;
    
    cart.forEach(item => {
        total += item.price * item.quantity;
        totalItems += item.quantity;
    });
    
    const totalElement = document.getElementById('newBookingTotal');
    if (totalElement) {
        totalElement.textContent = '$' + total.toFixed(2);
    }
    
    // Also update cart total in step 2
    const cartTotalElement = document.getElementById('cartTotal');
    if (cartTotalElement) {
        cartTotalElement.textContent = '$' + total.toFixed(2);
    }
    
    // Also update checkout total in step 3
    const checkoutTotalElement = document.getElementById('checkoutTotal');
    if (checkoutTotalElement) {
        checkoutTotalElement.textContent = '$' + total.toFixed(2);
    }
    
    // Update mini cart total
    const miniCartTotalElement = document.getElementById('miniCartTotal');
    if (miniCartTotalElement) {
        miniCartTotalElement.textContent = '$' + total.toFixed(2);
    }
    
    // Update cart badge
    updateCartBadge(totalItems);
    
    // Update mini cart if it's open
    if (document.getElementById('miniCartPanel')?.classList.contains('translate-x-0')) {
        renderMiniCart();
    }
}

function updateCartBadge(count) {
    const badge = document.getElementById('cartBadge');
    if (badge) {
        if (count > 0) {
            badge.classList.remove('hidden');
            badge.textContent = count > 99 ? '99+' : count;
        } else {
            badge.classList.add('hidden');
        }
    }
}

function toggleMiniCart() {
    const overlay = document.getElementById('miniCartOverlay');
    const panel = document.getElementById('miniCartPanel');
    
    if (overlay && panel) {
        if (panel.classList.contains('translate-x-full')) {
            // Open cart - render content first, then slide in from right
            renderMiniCart();
            // Ensure transition classes are present for smooth slide
            panel.classList.add('transition-transform', 'duration-200', 'ease-out');
            overlay.classList.remove('hidden');
            // Use requestAnimationFrame to ensure smooth animation
            requestAnimationFrame(() => {
                panel.classList.remove('translate-x-full');
                panel.classList.add('translate-x-0');
            });
        } else {
            // Close cart - slide out to right
            overlay.classList.add('hidden');
            panel.classList.remove('translate-x-0');
            panel.classList.add('translate-x-full');
        }
    }
}

function closeMiniCartAndGoToCart() {
    // Close the mini cart first
    const overlay = document.getElementById('miniCartOverlay');
    const panel = document.getElementById('miniCartPanel');
    
    if (overlay && panel) {
        overlay.classList.add('hidden');
        panel.classList.remove('translate-x-0');
        panel.classList.add('translate-x-full');
    }
    
    // Then navigate to step 3 (Cart step)
    setTimeout(() => {
        goToStep(3);
    }, 200); // Wait for animation to complete
}

function closeMiniCartAndGoToCheckout() {
    // Close the mini cart first
    const overlay = document.getElementById('miniCartOverlay');
    const panel = document.getElementById('miniCartPanel');
    
    if (overlay && panel) {
        overlay.classList.add('hidden');
        panel.classList.remove('translate-x-0');
        panel.classList.add('translate-x-full');
    }
    
    // Then navigate to step 4 (Checkout step)
    setTimeout(() => {
        goToStep(4);
    }, 200); // Wait for animation to complete
}

function renderMiniCart() {
    const miniCartItems = document.getElementById('miniCartItems');
    const miniCartActionButtons = document.getElementById('miniCartActionButtons');
    if (!miniCartItems) return;
    
    if (cart.length === 0) {
        miniCartItems.innerHTML = `
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <p class="text-gray-500 text-sm">Your cart is empty</p>
            </div>
        `;
        // Hide action buttons when cart is empty
        if (miniCartActionButtons) {
            miniCartActionButtons.classList.add('hidden');
        }
        calculateNewBookingTotal();
        return;
    }
    
    // Show action buttons when cart has items
    if (miniCartActionButtons) {
        miniCartActionButtons.classList.remove('hidden');
    }
    
    let cartHTML = '';
    cart.forEach(item => {
        const itemId = item.name.replace(/[^a-zA-Z0-9]/g, '-').toLowerCase();
        cartHTML += `
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4" data-item-name="${item.name.replace(/"/g, '&quot;')}">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-semibold text-gray-900 mb-1 truncate">${item.name}</h3>
                        <p class="text-xs text-gray-600 mb-2">$${item.price.toFixed(2)} each</p>
                        <div class="flex items-center gap-2">
                            <button type="button" 
                                    onclick="updateCartQuantity('${item.name.replace(/'/g, "\\'")}', ${item.quantity - 1})" 
                                    class="w-6 h-6 flex items-center justify-center bg-white border border-gray-300 rounded hover:bg-gray-50 transition text-gray-700 cursor-pointer">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </button>
                            <span id="miniCartQty-${itemId}" class="text-sm font-medium text-gray-900 min-w-[1.5rem] text-center">${item.quantity}</span>
                            <button type="button" 
                                    onclick="updateCartQuantity('${item.name.replace(/'/g, "\\'")}', ${item.quantity + 1})" 
                                    class="w-6 h-6 flex items-center justify-center bg-white border border-gray-300 rounded hover:bg-gray-50 transition text-gray-700 cursor-pointer">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p id="miniCartPrice-${itemId}" class="text-sm font-semibold text-[#003047] mb-2">$${(item.price * item.quantity).toFixed(2)}</p>
                        <button type="button" 
                                onclick="removeFromCart('${item.name.replace(/'/g, "\\'")}')" 
                                class="text-red-500 hover:text-red-700 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        `;
    });
    miniCartItems.innerHTML = cartHTML;
    calculateNewBookingTotal();
}

// Step Navigation Functions
// Step name mapping for URL
const stepNames = {
    1: 'customer-details',
    2: 'order',
    3: 'cart',
    4: 'checkout'
};

// Reverse mapping from step name to number
const stepNameToNumber = {
    'customer-details': 1,
    'order': 2,
    'cart': 3,
    'checkout': 4
};

function goToStep(step) {
    // Validate step before proceeding
    if (step === 2) {
        // Validate customer selection before proceeding to Order
        const selectedCustomerDisplay = document.getElementById('selectedCustomerDisplay');
        if (!selectedCustomerDisplay || selectedCustomerDisplay.classList.contains('hidden')) {
            showErrorMessage('Please select a customer before proceeding.');
            return;
        }
    }
    
    if (step === 3 && cart.length === 0) {
        showErrorMessage('Please add at least one service to your cart before proceeding.');
        return;
    }
    
    if (step === 4) {
        // Validate customer selection before checkout
        const selectedCustomerDisplay = document.getElementById('selectedCustomerDisplay');
        if (!selectedCustomerDisplay || selectedCustomerDisplay.classList.contains('hidden')) {
            showErrorMessage('Please select a customer before proceeding to checkout.');
            return;
        }
        updateCheckoutSummary();
    }
    
    // Hide all steps
    document.querySelectorAll('.step-content').forEach(stepEl => {
        stepEl.classList.add('hidden');
    });
    
    // Show selected step
    const stepElement = document.getElementById('step' + step);
    if (stepElement) {
        stepElement.classList.remove('hidden');
    }
    
    // Update step indicators
    updateStepIndicators(step);
    
    // Show/hide categories sidebar (only show in step 2 - Order)
    const categoriesSidebar = document.getElementById('categoriesSidebar');
    if (categoriesSidebar) {
        if (step === 2) {
            categoriesSidebar.classList.remove('hidden');
        } else {
            categoriesSidebar.classList.add('hidden');
        }
    }
    
    // Show/hide mini cart button (hide on step 1 - Customer Details)
    const miniCartButton = document.getElementById('miniCartButton');
    if (miniCartButton) {
        if (step === 1) {
            miniCartButton.classList.add('hidden');
        } else {
            miniCartButton.classList.remove('hidden');
        }
    }
    
    // Update current step
    currentStep = step;
    
    // Update URL with step parameter
    updateURLStep(step);
    
    // Render cart if going to step 3
    if (step === 3) {
        renderCartItems();
    }
}

function updateURLStep(step) {
    const stepName = stepNames[step];
    if (stepName) {
        const url = new URL(window.location);
        url.searchParams.set('step', stepName);
        window.history.pushState({ step: step }, '', url);
    }
}

function getStepFromURL() {
    const urlParams = new URLSearchParams(window.location.search);
    const stepParam = urlParams.get('step');
    if (stepParam && stepNameToNumber[stepParam]) {
        return stepNameToNumber[stepParam];
    }
    return null;
}

function updateStepIndicators(activeStep) {
    for (let i = 1; i <= 4; i++) {
        const indicator = document.getElementById('step' + i + 'Indicator');
        const stepContainer = document.getElementById('step' + i + 'Container');
        const titleElement = stepContainer?.querySelector('p.text-sm');
        const subtitleElement = stepContainer?.querySelector('p.text-xs');
        
        if (indicator && stepContainer) {
            // Remove all click handlers first
            stepContainer.onclick = null;
            stepContainer.classList.remove('cursor-pointer', 'hover:opacity-80', 'transition-opacity');
            
            if (i === activeStep) {
                // Current step - clickable
                indicator.classList.remove('bg-gray-200', 'text-gray-600', 'bg-green-500');
                indicator.classList.add('bg-[#003047]', 'text-white');
                stepContainer.classList.add('cursor-pointer', 'hover:opacity-80', 'transition-opacity');
                stepContainer.onclick = () => goToStep(i);
                if (titleElement) {
                    titleElement.classList.remove('text-gray-600');
                    titleElement.classList.add('text-gray-900');
                }
            } else if (i < activeStep) {
                // Completed step - clickable
                indicator.classList.remove('bg-gray-200', 'text-gray-600', 'bg-[#003047]');
                indicator.classList.add('bg-green-500', 'text-white');
                stepContainer.classList.add('cursor-pointer', 'hover:opacity-80', 'transition-opacity');
                stepContainer.onclick = () => goToStep(i);
                if (titleElement) {
                    titleElement.classList.remove('text-gray-900', 'text-gray-600');
                    titleElement.classList.add('text-gray-700');
                }
            } else {
                // Future step - not clickable
                indicator.classList.remove('bg-[#003047]', 'text-white', 'bg-green-500');
                indicator.classList.add('bg-gray-200', 'text-gray-600');
                stepContainer.classList.remove('cursor-pointer', 'hover:opacity-80');
                if (titleElement) {
                    titleElement.classList.remove('text-gray-900', 'text-gray-700');
                    titleElement.classList.add('text-gray-600');
                }
            }
        }
    }
}

function renderCartItems() {
    const cartContainer = document.getElementById('cartItemsContainer');
    if (!cartContainer) return;

    if (cart.length === 0) {
        cartContainer.innerHTML = `
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <p class="text-gray-500 text-sm mb-4">Your cart is empty</p>
                <button type="button" onclick="goToStep(1)" class="px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm">
                    Add Services
                </button>
            </div>
        `;
        return;
    }

    let cartHTML = '';
    cart.forEach((item, index) => {
        cartHTML += `
            <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <h3 class="text-base font-semibold text-gray-900 mb-1">${item.name}</h3>
                        <p class="text-sm text-gray-600">$${item.price.toFixed(2)} each</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="flex items-center gap-2 bg-white border border-gray-300 rounded-lg px-2 py-1">
                            <button type="button" 
                                    onclick="updateCartQuantity('${item.name.replace(/'/g, "\\'")}', ${item.quantity - 1})" 
                                    class="w-7 h-7 flex items-center justify-center hover:bg-gray-100 rounded transition text-gray-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </button>
                            <span class="text-sm font-medium text-gray-900 min-w-[2rem] text-center">${item.quantity}</span>
                            <button type="button" 
                                    onclick="updateCartQuantity('${item.name.replace(/'/g, "\\'")}', ${item.quantity + 1})" 
                                    class="w-7 h-7 flex items-center justify-center hover:bg-gray-100 rounded transition text-gray-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="text-right min-w-[5rem]">
                            <p class="text-base font-semibold text-[#003047]">$${(item.price * item.quantity).toFixed(2)}</p>
                        </div>
                        <button type="button" 
                                onclick="removeFromCart('${item.name.replace(/'/g, "\\'")}')" 
                                class="ml-2 text-red-500 hover:text-red-700 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        `;
    });
    cartContainer.innerHTML = cartHTML;
    calculateNewBookingTotal();
}

function updateCheckoutSummary() {
    const summaryContainer = document.getElementById('checkoutSummary');
    const customerInfoContainer = document.getElementById('checkoutCustomerInfo');
    
    if (!summaryContainer || !customerInfoContainer) return;
    
    // Update order summary
    let summaryHTML = '';
    cart.forEach(item => {
        summaryHTML += `
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">${item.name} x ${item.quantity}</span>
                <span class="text-gray-900 font-medium">$${(item.price * item.quantity).toFixed(2)}</span>
            </div>
        `;
    });
    summaryContainer.innerHTML = summaryHTML;
    
    // Update customer info
    const selectedCustomerDisplay = document.getElementById('selectedCustomerDisplay');
    if (selectedCustomerDisplay && !selectedCustomerDisplay.classList.contains('hidden')) {
        const customerName = document.getElementById('selectedCustomerName')?.textContent || 'N/A';
        const customerContact = document.getElementById('selectedCustomerContact')?.textContent || '';
        
        customerInfoContainer.innerHTML = `
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Customer Information</h3>
                <div class="space-y-2">
                    <div>
                        <p class="text-xs text-gray-500">Name</p>
                        <p class="text-sm font-medium text-gray-900">${customerName}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Contact</p>
                        <p class="text-sm font-medium text-gray-900">${customerContact}</p>
                    </div>
                </div>
            </div>
        `;
    } else {
        customerInfoContainer.innerHTML = `
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <p class="text-sm text-yellow-800">No customer selected. Please go back to select a customer.</p>
            </div>
        `;
    }
    
    calculateNewBookingTotal();
}

function filterNewBookingServices(searchTerm) {
    // Re-render services list with search filter
    initializeServicesList();
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


function saveWalkIn(event) {
    event.preventDefault();
    
    // Validate customer selection
    if (!window.selectedCustomer) {
        showErrorMessage('Please select or add a customer first.');
        return;
    }
    
    // Validate cart is not empty
    if (cart.length === 0) {
        showErrorMessage('Please add at least one service to your cart.');
        goToStep(2); // Go back to Order step
        return;
    }
    
    // Validate booking type is selected
    if (!bookingType) {
        showErrorMessage('Please select a booking type.');
        goToStep(1); // Go back to Customer Details step
        return;
    }
    
    // All validations passed - proceed with booking
    const customerName = `${window.selectedCustomer.firstName} ${window.selectedCustomer.lastName}`;
    const bookingTypeText = bookingType === 'walk-in' ? 'Walk-In' : 'Book Appointment';
    
    // Show success message
    showSuccessMessage(`${bookingTypeText} booking created for ${customerName}!`);
    
    // Redirect after a short delay
    setTimeout(() => {
        window.location.href = 'index.php';
    }, 1500);
}

function showSuccessMessage(message) {
    const successDiv = document.createElement('div');
    successDiv.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    successDiv.textContent = message;
    document.body.appendChild(successDiv);
    setTimeout(() => successDiv.remove(), 3000);
}

function showErrorMessage(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'fixed top-4 left-1/2 transform -translate-x-1/2 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    errorDiv.textContent = message;
    document.body.appendChild(errorDiv);
    setTimeout(() => errorDiv.remove(), 3000);
}

// Payment Modal Functions
let paymentAmountStr = '';

function openPaymentModalFromCheckout() {
    // Validate customer and cart
    if (!window.selectedCustomer) {
        showErrorMessage('Please select a customer first.');
        return;
    }
    
    if (cart.length === 0) {
        showErrorMessage('Please add at least one service to your cart.');
        return;
    }
    
    // Get customer name and generate order ID
    const customerName = `${window.selectedCustomer.firstName} ${window.selectedCustomer.lastName}`;
    const orderId = 'ORDER' + Date.now().toString().slice(-6);
    
    // Calculate total from cart
    let total = 0;
    cart.forEach(item => {
        total += item.price * item.quantity;
    });
    
    // Open payment modal
    openPaymentModal(customerName, orderId, '$' + total.toFixed(2));
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
        <div class="p-4 sm:p-6 w-full">
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
                            <p class="text-sm text-gray-600">${orderId} / ${bookingType === 'walk-in' ? 'Walk-In' : 'Appointment'}</p>
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
    openModal(modalContent, 'xl');
    
    // Initialize payment amount to empty
    paymentAmountStr = '';
    
    // Load items from cart
    loadPaymentItemsFromCart();
    
    // Initialize the display
    setTimeout(() => {
        updatePaymentDisplay();
        updatePaymentTotal();
    }, 100);
}

function loadPaymentItemsFromCart() {
    const itemsList = document.getElementById('paymentItemsList');
    const itemCountEl = document.getElementById('itemCount');
    
    if (!itemsList || !itemCountEl) return;
    
    // Use cart items
    const items = cart.map(item => ({
        name: item.name,
        qty: item.quantity,
        price: item.price
    }));
    
    itemsList.innerHTML = items.map(item => `
        <div class="flex justify-between py-2">
            <span class="text-sm text-gray-900">${item.name} (${item.qty}x)</span>
            <span class="text-sm font-semibold text-gray-900">$${(item.price * item.qty).toFixed(2)}</span>
        </div>
    `).join('');
    
    itemCountEl.textContent = items.length;
}

function addPaymentDigit(digit) {
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
        paymentAmountStr += digit;
    }
    
    updatePaymentDisplay();
}

function removePaymentDigit() {
    const displayEl = document.getElementById('paymentAmount');
    if (!displayEl) return;
    
    paymentAmountStr = paymentAmountStr.slice(0, -1);
    updatePaymentDisplay();
}

function addPaymentAmount(amount) {
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
    
    // Close modal
    closeModal();
    
    // Show success message
    showSuccessMessage(`Payment processed successfully! ${change > 0 ? 'Change: $' + change : ''}`);
    
    // Redirect to queue page after payment
    setTimeout(() => {
        window.location.href = 'index.php';
    }, 1500);
}
</script>

<style>
/* Mini Cart Panel Styling */
#miniCartPanel {
    max-height: 100vh;
}

#miniCartOverlay {
    backdrop-filter: blur(2px);
}

/* Categories Sidebar Scrollbar Styling */
#categoriesList {
    scrollbar-width: thin;
    scrollbar-color: #cbd5e0 #f7fafc;
}

#categoriesList::-webkit-scrollbar {
    width: 6px;
}

#categoriesList::-webkit-scrollbar-track {
    background: #f7fafc;
    border-radius: 3px;
}

#categoriesList::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 3px;
}

#categoriesList::-webkit-scrollbar-thumb:hover {
    background: #a0aec0;
}

/* Category Card Hover Effects */
.category-card {
    transition: all 0.2s ease;
}

.category-card:hover {
    transform: translateX(2px);
}

/* Service Item Improvements */
.newBooking-service-item {
    transition: all 0.2s ease;
}

.newBooking-service-item:hover {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

/* Booking Type Radio Button Styling */
input[name="bookingType"]:checked ~ div > div:first-child {
    border-color: #003047 !important;
    background-color: #003047 !important;
}

input[name="bookingType"]:checked ~ div > div:first-child > div {
    opacity: 1 !important;
}

/* Responsive adjustments */
@media (max-width: 1024px) {
    #categoriesList {
        max-height: 300px;
    }
}
</style>

<?php include '../includes/footer.php'; ?>

<?php
$pageTitle = 'Payment';
include '../includes/header.php';
include '../includes/modal.php';
?>

<!-- Main Content Area -->
<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <!-- Header with Back Button -->
        <div class="mb-6">
            <a href="tickets.php" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span class="text-sm font-medium">Back to Tickets</span>
            </a>
        </div>

        <!-- Customer Info and Step Tabs Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-3 py-2 px-4">
            <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                <!-- Left: Customer Info -->
                <div class="flex items-center gap-4 flex-1">
                    <div id="customerInfoContainer" class="flex items-center gap-3">
                        <!-- Customer info will be populated by JavaScript -->
                    </div>
                </div>
                
                <!-- Right: Step Tabs -->
                <div class="flex-shrink-0">
                    <div class="flex border-b border-gray-200">
                        <button onclick="switchStep(1)" id="step1Tab" class="flex-1 lg:flex-initial px-6 py-3 text-left border-b-2 border-[#003047] transition">
                            <div class="flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-[#003047] text-white text-sm font-semibold">1</span>
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900">Cart</h3>
                                    <p class="text-xs text-gray-500 hidden lg:block">Select services and assign to technicians</p>
                                </div>
                            </div>
                        </button>
                        <button onclick="switchStep(2)" id="step2Tab" class="flex-1 lg:flex-initial px-6 py-3 text-left border-b-2 border-transparent hover:border-gray-300 transition">
                            <div class="flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-gray-200 text-gray-600 text-sm font-semibold">2</span>
                                <div>
                                    <h3 class="text-base font-semibold text-gray-500">Checkout</h3>
                                    <p class="text-xs text-gray-500 hidden lg:block">Review and complete payment</p>
                                </div>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 1: Cart -->
        <div id="step1Content" class="step-content">
            <!-- Two Column Layout: Services | Technicians -->
            <div class="flex flex-col lg:flex-row gap-6 h-[calc(100vh-200px)]">
            <!-- Left Column: Categories Sidebar + Services Grid -->
            <div class="w-full lg:flex-1 flex gap-3 flex-shrink-0">
                <!-- Categories Sidebar -->
                <div class="w-64 flex-shrink-0 flex flex-col">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 flex flex-col h-full">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex-shrink-0">Categories</h2>
                        <div id="categoriesList" class="space-y-2 overflow-y-auto flex-1 pr-2">
                            <!-- Categories will be loaded dynamically -->
                        </div>
                    </div>
                </div>

                <!-- Services Grid -->
                <div class="flex-1 min-w-0 flex flex-col">
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col h-full overflow-hidden">
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
                                           id="serviceSearchInput" 
                                           placeholder="Search services..." 
                                           class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-sm"
                                           oninput="filterServices(this.value); updateClearButton(this.value);">
                                    <button type="button" 
                                            id="clearSearchBtn" 
                                            onclick="clearSearch()" 
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
                            <div class="rounded-lg flex-1 overflow-y-auto min-h-0" id="servicesListContainer">
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <!-- Services will be populated by JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Technicians List -->
            <div class="w-full lg:w-1/3 flex-shrink-0 flex flex-col">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col h-full overflow-hidden">
                    <h2 class="text-xl font-bold text-gray-900 mb-4 flex-shrink-0">Assigned Technicians</h2>
                    <div id="techniciansListContainer" class="flex-1 overflow-y-auto min-h-0 mb-4">
                        <!-- Technicians will be populated by JavaScript -->
                    </div>
                    <!-- Next Step Button -->
                    <button onclick="switchStep(2)" class="w-full px-6 py-4 text-lg font-semibold text-white bg-[#003047] rounded-lg hover:bg-[#002535] transition active:scale-95 flex-shrink-0">
                        Next: Checkout
                    </button>
                </div>
            </div>
        </div>
        </div>

        <!-- Step 2: Checkout -->
        <div id="step2Content" class="step-content hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
                <!-- Left Side: Transaction Details -->
                <div class="flex flex-col">
                    <!-- Transaction Details -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex-1">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Transaction Details</h4>
                        <div class="bg-gray-50 rounded-lg p-4 flex flex-col">
                            <!-- Header Row -->
                            <div class="grid grid-cols-12 gap-4 pb-2 mb-2 border-b border-gray-300">
                                <div class="col-span-6">
                                    <span class="text-xs font-semibold text-gray-700 uppercase">Item</span>
                                </div>
                                <div class="col-span-3 text-center">
                                    <span class="text-xs font-semibold text-gray-700 uppercase">Quantity</span>
                                </div>
                                <div class="col-span-3 text-right">
                                    <span class="text-xs font-semibold text-gray-700 uppercase">Amount</span>
                                </div>
                            </div>
                            <div id="checkoutItemsList" class="overflow-y-auto space-y-2 pr-2 max-h-[400px]" style="scrollbar-width: thin; scrollbar-color: #cbd5e0 #f3f4f6;">
                                <!-- Items will be populated by JavaScript -->
                            </div>
                            
                            <div class="space-y-2 border-t border-gray-200 pt-3 mt-auto">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Items (<span id="checkoutItemCount">0</span>)</span>
                                    <span class="text-gray-900 font-medium" id="checkoutSubtotalDisplay">$0.00</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Tax (5%)</span>
                                    <span class="text-gray-900 font-medium" id="checkoutTaxDisplay">$0.00</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Tip</span>
                                    <span class="text-gray-900 font-medium" id="checkoutTipDisplay">$0.00</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Discount</span>
                                    <span class="text-gray-900 font-medium text-red-600" id="checkoutDiscountDisplay">$0.00</span>
                                </div>
                                <div class="flex justify-between items-center pt-2 border-t border-gray-200">
                                    <span class="text-lg font-semibold text-gray-900">Total</span>
                                    <span class="text-2xl font-bold text-gray-900" id="checkoutTotalDisplay">$0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Technicians Tip Split Section -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Technicians Tip</h4>
                        <div id="techniciansTipSection">
                            <div id="techniciansTipList" class="space-y-3 max-h-[300px] overflow-y-auto pr-2" style="scrollbar-width: thin; scrollbar-color: #cbd5e0 #f3f4f6;">
                                <!-- Technicians tip inputs will be populated by JavaScript -->
                            </div>
                            <div class="mt-3 pt-3 border-t border-gray-200">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm font-medium text-gray-700">Total Split:</span>
                                    <span class="text-sm font-bold text-gray-900" id="totalTipSplitDisplay">$0.00</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Side: Payment Input -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col space-y-4">
                    <!-- Payment Method -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Select a payment method</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="relative flex items-center justify-center p-4 border-2 border-[#003047] rounded-lg cursor-pointer hover:border-[#003047] transition-all duration-200 has-[:checked]:border-[#003047] has-[:checked]:bg-[#e6f0f3]">
                                <input type="radio" name="payment_method" value="cash" id="paymentMethodCash" checked class="sr-only peer" onchange="updatePaymentMethod('cash'); updatePaymentMethodStyles();">
                                <div class="flex items-center gap-2">
                                    <div id="paymentMethodCashCircle" class="w-5 h-5 border-2 border-[#003047] rounded-full flex items-center justify-center transition-all bg-[#003047]">
                                        <div class="w-2 h-2 bg-white rounded-full transition-opacity"></div>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900">Cash</span>
                                </div>
                            </label>
                            <label class="relative flex items-center justify-center p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-[#003047] transition-all duration-200 has-[:checked]:border-[#003047] has-[:checked]:bg-[#e6f0f3]">
                                <input type="radio" name="payment_method" value="card" id="paymentMethodCard" class="sr-only peer" onchange="updatePaymentMethod('card'); updatePaymentMethodStyles();">
                                <div class="flex items-center gap-2">
                                    <div id="paymentMethodCardCircle" class="w-5 h-5 border-2 border-gray-300 rounded-full flex items-center justify-center transition-all">
                                        <div class="w-2 h-2 bg-white rounded-full opacity-0 transition-opacity"></div>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900">Card</span>
                                </div>
                            </label>
                        </div>
                        <input type="hidden" id="paymentMethod" value="cash">
                    </div>
                    
                    <!-- Tip and Discount Section (2 columns) -->
                    <div class="grid grid-cols-2 gap-4">
                        <!-- Tip Section -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tip</label>
                            <div class="grid grid-cols-2 gap-2 mb-2">
                                <button type="button" onclick="setTipPercentage(10)" class="px-3 py-2 text-sm font-medium text-gray-700 bg-[#e6f0f3] rounded-lg hover:bg-[#b3d1d9] transition active:scale-95 border border-[#b3d1d9]">10%</button>
                                <button type="button" onclick="setTipPercentage(15)" class="px-3 py-2 text-sm font-medium text-gray-700 bg-[#e6f0f3] rounded-lg hover:bg-[#b3d1d9] transition active:scale-95 border border-[#b3d1d9]">15%</button>
                                <button type="button" onclick="setTipPercentage(20)" class="px-3 py-2 text-sm font-medium text-gray-700 bg-[#e6f0f3] rounded-lg hover:bg-[#b3d1d9] transition active:scale-95 border border-[#b3d1d9]">20%</button>
                                <button type="button" onclick="setTipPercentage(25)" class="px-3 py-2 text-sm font-medium text-gray-700 bg-[#e6f0f3] rounded-lg hover:bg-[#b3d1d9] transition active:scale-95 border border-[#b3d1d9]">25%</button>
                            </div>
                            <input type="number" name="tip" id="tipInput" step="0.01" min="0" value="0" oninput="updateTipFromInput()" placeholder="0.00" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" />
                        </div>
                        
                        <!-- Discount Section -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Discount</label>
                            <div class="grid grid-cols-2 gap-2 mb-2">
                                <button type="button" onclick="setDiscountPercentage(5)" class="px-3 py-2 text-sm font-medium text-gray-700 bg-[#e6f0f3] rounded-lg hover:bg-[#b3d1d9] transition active:scale-95 border border-[#b3d1d9]">5%</button>
                                <button type="button" onclick="setDiscountPercentage(10)" class="px-3 py-2 text-sm font-medium text-gray-700 bg-[#e6f0f3] rounded-lg hover:bg-[#b3d1d9] transition active:scale-95 border border-[#b3d1d9]">10%</button>
                                <button type="button" onclick="setDiscountPercentage(15)" class="px-3 py-2 text-sm font-medium text-gray-700 bg-[#e6f0f3] rounded-lg hover:bg-[#b3d1d9] transition active:scale-95 border border-[#b3d1d9]">15%</button>
                                <button type="button" onclick="setDiscountPercentage(20)" class="px-3 py-2 text-sm font-medium text-gray-700 bg-[#e6f0f3] rounded-lg hover:bg-[#b3d1d9] transition active:scale-95 border border-[#b3d1d9]">20%</button>
                            </div>
                            <input type="number" name="discount" id="discountInput" step="0.01" min="0" value="0" oninput="updateDiscountFromInput()" placeholder="0.00" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" />
                        </div>
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
                    <form onsubmit="processPayment(event)">
                        <input type="hidden" name="tip_amount" id="tipAmountHidden" value="0">
                        <input type="hidden" name="discount_amount" id="discountAmountHidden" value="0">
                        <button type="submit" class="w-full px-6 py-4 text-lg font-semibold text-white bg-[#003047] rounded-lg hover:bg-[#002535] transition active:scale-95">
                            Pay Now
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
// Get appointment ID from URL
const urlParams = new URLSearchParams(window.location.search);
const appointmentId = urlParams.get('id');

// Global variables
let appointmentData = null;
let customerData = null;
let allServicesData = [];
let servicesData = [];
let categoriesMap = {};
let selectedCategory = null;
let cart = [];
let techniciansData = [];
let assignedTechnicianIds = [];
let selectedTechnicianId = null; // Track selected/active technician
let paymentSubtotal = 0;
let paymentTax = 0;
let paymentTip = 0;
let paymentDiscount = 0;
let paymentAmountStr = '';
let currentStep = 1; // Track current step
let technicianTips = {}; // { technicianId: { percentage: number, amount: number } }

// Fetch appointment and customer data
async function loadPaymentData() {
    if (!appointmentId) {
        showError('No appointment ID provided');
        return;
    }

    try {
        // Fetch appointments
        const appointmentsResponse = await fetch('../json/appointments.json');
        const appointmentsData = await appointmentsResponse.json();
        appointmentData = appointmentsData.appointments.find(apt => apt.id.toString() === appointmentId || apt.id === parseInt(appointmentId));
        
        if (!appointmentData) {
            showError('Appointment not found');
            return;
        }

        // Fetch customers
        const customersResponse = await fetch('../json/customers.json');
        const customersData = await customersResponse.json();
        customerData = customersData.customers.find(c => c.id === appointmentData.customer_id);
        
        if (!customerData) {
            showError('Customer not found');
            return;
        }

        // Load assigned technicians
        if (appointmentData.assigned_technician && Array.isArray(appointmentData.assigned_technician)) {
            assignedTechnicianIds = appointmentData.assigned_technician.map(id => id.toString());
            // Set first assigned technician as selected by default
            if (assignedTechnicianIds.length > 0) {
                selectedTechnicianId = assignedTechnicianIds[0];
            }
        }

        // Load categories and services
        await fetchCategoriesAndServices();
        
        // Load technicians
        await fetchTechnicians();
        
        // Update customer info in header
        updateCustomerInfoHeader();
    } catch (error) {
        console.error('Error loading payment data:', error);
        showError('Failed to load payment details');
    }
}

// Update customer info in header
function updateCustomerInfoHeader() {
    if (!customerData || !appointmentData) return;
    
    const customerName = `${customerData.firstName} ${customerData.lastName}`;
    const customerInitial = customerName.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
    const orderId = `ORDER${appointmentData.id.toString().padStart(3, '0')}`;
    const appointmentType = appointmentData.appointment === 'walk-in' ? 'Walk-In' : 'Booked';
    
    // Format date
    const date = new Date(appointmentData.created_at);
    const dateStr = date.toLocaleDateString('en-US', { weekday: 'short', year: 'numeric', month: 'long', day: 'numeric' });
    const timeStr = date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
    
    const customerInfoContainer = document.getElementById('customerInfoContainer');
    if (customerInfoContainer) {
        customerInfoContainer.innerHTML = `
            <div class="w-12 h-12 bg-[#e6f0f3] rounded-lg flex items-center justify-center flex-shrink-0">
                <span class="text-lg font-semibold text-[#003047]">${customerInitial}</span>
            </div>
            <div class="flex-1">
                <h2 class="text-xl font-bold text-gray-900">${customerName}</h2>
                <p class="text-sm text-gray-600">${orderId} / ${appointmentType}</p>
                <p class="text-xs text-gray-500">${dateStr} ${timeStr}</p>
            </div>
        `;
    }
}

function showError(message) {
    const container = document.querySelector('main .p-4, main .p-6, main .p-8');
    if (container) {
        container.innerHTML = `
            <div class="text-center py-12">
                <svg class="w-16 h-16 text-red-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-red-500 text-sm mb-4">${message}</p>
                <a href="tickets.php" class="inline-block px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition">
                    Back to Tickets
                </a>
            </div>
        `;
    }
}

// Fetch categories and services
async function fetchCategoriesAndServices() {
    try {
        // Fetch categories
        const categoriesResponse = await fetch('../json/service-categories.json');
        const categoriesData = await categoriesResponse.json();
        categoriesMap = categoriesData.categories;
        
        // Fetch services
        const servicesResponse = await fetch('../json/services.json');
        const servicesData_json = await servicesResponse.json();
        allServicesData = servicesData_json.services || [];
        servicesData = allServicesData.filter(s => s.active !== false);
        
        // Initialize categories list
        initializeCategoriesList();
        
        // Initialize services list
        initializeServicesList();
    } catch (error) {
        console.error('Error fetching categories and services:', error);
    }
}

// Fetch technicians
async function fetchTechnicians() {
    try {
        const response = await fetch('../json/users.json');
        const data = await response.json();
        techniciansData = data.users.filter(user => (user.role === 'technician' || user.userlevel === 'technician') && user.status === 'active');
        renderTechniciansList();
    } catch (error) {
        console.error('Error fetching technicians:', error);
        techniciansData = [];
        renderTechniciansList();
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

// Filter by category
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

// Initialize services list
function initializeServicesList() {
    const container = document.getElementById('servicesListContainer');
    if (!container) return;
    
    const gridContainer = container.querySelector('.grid');
    if (!gridContainer) return;
    
    // Filter services by selected category
    let filteredServices = servicesData;
    if (selectedCategory !== null) {
        filteredServices = servicesData.filter(service => 
            service.categories && service.categories.includes(selectedCategory)
        );
    }
    
    // Also apply search filter if active
    const searchInput = document.getElementById('serviceSearchInput');
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
            // Check if service is in cart for the selected technician
            const isInCart = selectedTechnicianId ? cart.find(item => 
                item.name === service.name && item.technician_id === selectedTechnicianId
            ) : null;
            
            servicesHTML += `
                <div class="service-item bg-white border border-gray-200 rounded-lg overflow-hidden hover:border-[#003047] hover:shadow-md transition-all flex flex-col h-full">
                    <!-- Thumbnail -->
                    <div class="w-full h-32 ${color.bg} flex items-center justify-center flex-shrink-0">
                        <svg class="w-12 h-12 ${color.text}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                    </div>
                    
                    <!-- Content -->
                    <div class="p-4 flex flex-col flex-1">
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-gray-900 mb-1">${service.name}</h3>
                            <p class="text-lg font-bold text-[#003047] mb-3">$${service.price.toFixed(2)}</p>
                        </div>
                        
                        <!-- Add to Cart Button -->
                        <button type="button" 
                                onclick="addServiceToCart('${service.name.replace(/'/g, "\\'")}', ${service.price})" 
                                class="w-full px-6 py-3 ${!selectedTechnicianId ? 'bg-gray-400 cursor-not-allowed' : isInCart ? 'bg-green-600 hover:bg-green-700' : 'bg-[#003047] hover:bg-[#002535]'} text-white rounded-lg transition font-medium text-sm active:scale-95 flex items-center justify-center gap-2 mt-auto"
                                ${!selectedTechnicianId ? 'disabled title="Please select a technician first"' : ''}>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            ${!selectedTechnicianId ? 'Select Technician First' : isInCart ? `In Cart (${isInCart.quantity}x)` : 'Add to Cart'}
                        </button>
                    </div>
                </div>
            `;
        });
    }
    
    gridContainer.innerHTML = servicesHTML;
}

// Filter services by search
function filterServices(searchTerm) {
    initializeServicesList();
}

// Update clear button visibility
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

// Clear search
function clearSearch() {
    const searchInput = document.getElementById('serviceSearchInput');
    if (searchInput) {
        searchInput.value = '';
        filterServices('');
        updateClearButton('');
        searchInput.focus();
    }
}

// Add service to cart
function addServiceToCart(serviceName, servicePrice) {
    if (!selectedTechnicianId) {
        alert('Please select a technician first before adding services.');
        return;
    }
    
    // Check if service already exists for this technician
    const existingItem = cart.find(item => 
        item.name === serviceName && item.technician_id === selectedTechnicianId
    );
    
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            name: serviceName,
            price: servicePrice,
            quantity: 1,
            technician_id: selectedTechnicianId
        });
    }
    
    // Re-render services to update button states
    initializeServicesList();
    // Re-render technicians to show updated services
    renderTechniciansList();
}

// Render technicians list
function renderTechniciansList() {
    const container = document.getElementById('techniciansListContainer');
    if (!container) return;
    
    // Filter to only show assigned technicians
    const assignedTechnicians = techniciansData.filter(technician => {
        const technicianIdStr = technician.id.toString();
        return assignedTechnicianIds.includes(technicianIdStr);
    });
    
    if (assignedTechnicians.length === 0) {
        container.innerHTML = `
            <div class="text-center py-12">
                <p class="text-sm text-gray-500">No assigned technicians</p>
            </div>
        `;
        return;
    }
    
    let techniciansHTML = '<div class="space-y-3">';
    assignedTechnicians.forEach(technician => {
        const technicianIdStr = technician.id.toString();
        const isActive = selectedTechnicianId === technicianIdStr;
        const initials = technician.initials || (technician.firstName?.[0] || '') + (technician.lastName?.[0] || '');
        const fullName = `${technician.firstName} ${technician.lastName}`;
        const profilePhoto = technician.photo || technician.profilePhoto || null;
        
        // Get services assigned to this technician
        const technicianServices = cart.filter(item => item.technician_id === technicianIdStr);
        
        techniciansHTML += `
            <div onclick="selectTechnician('${technicianIdStr}')" class="flex flex-col gap-2 p-3 rounded-lg border-2 cursor-pointer transition ${isActive ? 'border-[#003047] bg-white' : 'border-gray-200 bg-white hover:bg-gray-50'}">
                <div class="flex items-center gap-3">
                    <div class="relative flex-shrink-0">
                        ${profilePhoto 
                            ? `<img src="${profilePhoto}" alt="${fullName}" class="w-12 h-12 rounded-full object-cover border-2 border-white">`
                            : `<div class="w-12 h-12 bg-[#e6f0f3] rounded-full flex items-center justify-center border-2 border-white">
                                <span class="text-sm font-bold text-[#003047]">${initials}</span>
                            </div>`
                        }
                        <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-[#003047] text-white rounded-full flex items-center justify-center text-xs font-bold border-2 border-white">
                            ✓
                        </div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">${fullName}</p>
                        <p class="text-xs text-gray-500">Technician</p>
                    </div>
                </div>
                ${technicianServices.length > 0 ? `
                    <div class="mt-2 pt-2 border-t border-gray-300">
                        <div class="space-y-2">
                            ${technicianServices.map((service, index) => `
                                <div class="bg-gray-50 rounded-lg p-3 border border-gray-200">
                                    <div class="flex items-center justify-between gap-3">
                                        <!-- Service Name and Price -->
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-gray-900">${service.name}</p>
                                            <p class="text-xs text-gray-500">$${service.price.toFixed(2)} each</p>
                                        </div>
                                        
                                        <!-- Quantity Selector -->
                                        <div class="flex items-center gap-2">
                                            <button onclick="event.stopPropagation(); updateServiceQuantity('${service.name.replace(/'/g, "\\'")}', ${service.price}, '${technicianIdStr}', ${service.quantity - 1})" class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-200 rounded border border-gray-300 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                </svg>
                                            </button>
                                            <span class="text-sm font-medium text-gray-900 min-w-[2rem] text-center">${service.quantity}</span>
                                            <button onclick="event.stopPropagation(); updateServiceQuantity('${service.name.replace(/'/g, "\\'")}', ${service.price}, '${technicianIdStr}', ${service.quantity + 1})" class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-200 rounded border border-gray-300 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                </svg>
                                            </button>
                                        </div>
                                        
                                        <!-- Total Price -->
                                        <div class="text-sm font-semibold text-gray-900 min-w-[4rem] text-right">
                                            $${(service.price * service.quantity).toFixed(2)}
                                        </div>
                                        
                                        <!-- Remove Button -->
                                        <button onclick="event.stopPropagation(); removeServiceFromCart('${service.name.replace(/'/g, "\\'")}', '${technicianIdStr}')" class="w-8 h-8 flex items-center justify-center text-red-500 hover:bg-red-50 rounded transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                ` : `
                    <div class="mt-2 pt-2 border-t border-gray-300">
                        <p class="text-xs text-gray-500 italic">No services assigned</p>
                    </div>
                `}
            </div>
        `;
    });
    techniciansHTML += '</div>';
    
    container.innerHTML = techniciansHTML;
}

// Select/activate a technician
function selectTechnician(technicianId) {
    selectedTechnicianId = technicianId;
    renderTechniciansList();
    // Re-render services to update button states
    initializeServicesList();
}

// Update service quantity in cart
function updateServiceQuantity(serviceName, servicePrice, technicianId, newQuantity) {
    if (newQuantity <= 0) {
        // If quantity is 0 or less, remove the item
        removeServiceFromCart(serviceName, technicianId);
        return;
    }
    
    // Find the service in cart for this technician
    const serviceIndex = cart.findIndex(item => 
        item.name === serviceName && item.technician_id === technicianId
    );
    
    if (serviceIndex > -1) {
        cart[serviceIndex].quantity = newQuantity;
        
        // Re-render technicians to show updated quantities
        renderTechniciansList();
        // Re-render services to update button states
        initializeServicesList();
    }
}

// Remove service from cart
function removeServiceFromCart(serviceName, technicianId) {
    // Find and remove the service from cart
    const serviceIndex = cart.findIndex(item => 
        item.name === serviceName && item.technician_id === technicianId
    );
    
    if (serviceIndex > -1) {
        cart.splice(serviceIndex, 1);
        
        // Re-render technicians to show updated cart
        renderTechniciansList();
        // Re-render services to update button states
        initializeServicesList();
    }
}

// Update service quantity in cart
function updateServiceQuantity(serviceName, servicePrice, technicianId, newQuantity) {
    if (newQuantity <= 0) {
        // If quantity is 0 or less, remove the item
        removeServiceFromCart(serviceName, technicianId);
        return;
    }
    
    // Find the service in cart for this technician
    const serviceIndex = cart.findIndex(item => 
        item.name === serviceName && item.technician_id === technicianId
    );
    
    if (serviceIndex > -1) {
        cart[serviceIndex].quantity = newQuantity;
        
        // Re-render technicians to show updated quantities
        renderTechniciansList();
        // Re-render services to update button states
        initializeServicesList();
    }
}

// Remove service from cart
function removeServiceFromCart(serviceName, technicianId) {
    // Find and remove the service from cart
    const serviceIndex = cart.findIndex(item => 
        item.name === serviceName && item.technician_id === technicianId
    );
    
    if (serviceIndex > -1) {
        cart.splice(serviceIndex, 1);
        
        // Re-render technicians to show updated cart
        renderTechniciansList();
        // Re-render services to update button states
        initializeServicesList();
    }
}


// Switch between steps
function switchStep(step) {
    if (step === 2 && cart.length === 0) {
        alert('Please add at least one service to your cart before checkout.');
        return;
    }
    
    currentStep = step;
    
    // Update step content visibility
    const step1Content = document.getElementById('step1Content');
    const step2Content = document.getElementById('step2Content');
    
    if (step === 1) {
        step1Content.classList.remove('hidden');
        step2Content.classList.add('hidden');
    } else {
        step1Content.classList.add('hidden');
        step2Content.classList.remove('hidden');
        renderCheckoutStep();
    }
    
    // Update tab styles
    const step1Tab = document.getElementById('step1Tab');
    const step2Tab = document.getElementById('step2Tab');
    
    if (step === 1) {
        step1Tab.classList.remove('border-transparent');
        step1Tab.classList.add('border-[#003047]');
        step1Tab.querySelector('span').classList.remove('bg-gray-200', 'text-gray-600');
        step1Tab.querySelector('span').classList.add('bg-[#003047]', 'text-white');
        step1Tab.querySelector('h3').classList.remove('text-gray-500');
        step1Tab.querySelector('h3').classList.add('text-gray-900');
        
        step2Tab.classList.remove('border-[#003047]');
        step2Tab.classList.add('border-transparent');
        step2Tab.querySelector('span').classList.remove('bg-[#003047]', 'text-white');
        step2Tab.querySelector('span').classList.add('bg-gray-200', 'text-gray-600');
        step2Tab.querySelector('h3').classList.remove('text-gray-900');
        step2Tab.querySelector('h3').classList.add('text-gray-500');
    } else {
        step2Tab.classList.remove('border-transparent');
        step2Tab.classList.add('border-[#003047]');
        step2Tab.querySelector('span').classList.remove('bg-gray-200', 'text-gray-600');
        step2Tab.querySelector('span').classList.add('bg-[#003047]', 'text-white');
        step2Tab.querySelector('h3').classList.remove('text-gray-500');
        step2Tab.querySelector('h3').classList.add('text-gray-900');
        
        step1Tab.classList.remove('border-[#003047]');
        step1Tab.classList.add('border-transparent');
        step1Tab.querySelector('span').classList.remove('bg-[#003047]', 'text-white');
        step1Tab.querySelector('span').classList.add('bg-gray-200', 'text-gray-600');
        step1Tab.querySelector('h3').classList.remove('text-gray-900');
        step1Tab.querySelector('h3').classList.add('text-gray-500');
    }
}

// Render checkout step content
function renderCheckoutStep() {
    if (!customerData || !appointmentData) return;
    
    // Calculate totals
    paymentSubtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    paymentTax = paymentSubtotal * 0.05; // 5% tax
    paymentTip = 0;
    paymentDiscount = 0;
    const total = paymentSubtotal + paymentTax + paymentTip - paymentDiscount;
    
    const customerName = `${customerData.firstName} ${customerData.lastName}`;
    const customerInitial = customerName.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();
    const orderId = `ORDER${appointmentData.id.toString().padStart(3, '0')}`;
    const appointmentType = appointmentData.appointment === 'walk-in' ? 'Walk-In' : 'Booked';
    
    // Update items list
    const itemsListContainer = document.getElementById('checkoutItemsList');
    if (itemsListContainer) {
        itemsListContainer.innerHTML = cart.map(item => `
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-6">
                    <span class="text-sm text-gray-900">${item.name}</span>
                </div>
                <div class="col-span-3 text-center">
                    <span class="text-sm text-gray-900">${item.quantity}</span>
                </div>
                <div class="col-span-3 text-right">
                    <span class="text-sm font-semibold text-gray-900">$${(item.price * item.quantity).toFixed(2)}</span>
                </div>
            </div>
        `).join('');
    }
    
    // Update totals
    document.getElementById('checkoutItemCount').textContent = cart.length;
    document.getElementById('checkoutSubtotalDisplay').textContent = `$${paymentSubtotal.toFixed(2)}`;
    document.getElementById('checkoutTaxDisplay').textContent = `$${paymentTax.toFixed(2)}`;
    document.getElementById('checkoutTipDisplay').textContent = `$${paymentTip.toFixed(2)}`;
    document.getElementById('checkoutDiscountDisplay').textContent = `$${paymentDiscount.toFixed(2)}`;
    document.getElementById('checkoutTotalDisplay').textContent = `$${total.toFixed(2)}`;
    
    // Reset payment amount
    paymentAmountStr = '';
    updatePaymentDisplay();
    updatePaymentTotal();
    
    // Render technicians tip split section
    renderTechniciansTipSplit();
    
    // Initialize payment method styles
    setTimeout(() => {
        updatePaymentMethodStyles();
    }, 100);
}

// Open checkout modal (legacy function - now redirects to step 2)
function openCheckoutModal() {
    switchStep(2);
}

// Align payment modal column heights
function alignPaymentModalHeights() {
    // Fixed height is set via inline style, no dynamic calculation needed
}

// Payment functions
function setTipPercentage(percentage) {
    const tipAmount = paymentSubtotal * (percentage / 100);
    paymentTip = tipAmount;
    document.getElementById('tipInput').value = tipAmount.toFixed(2);
    updatePaymentTotal();
    // Update technicians tip split when tip amount changes
    renderTechniciansTipSplit();
}

function updateTipFromInput() {
    const tipInput = document.getElementById('tipInput');
    paymentTip = parseFloat(tipInput.value) || 0;
    updatePaymentTotal();
    // Update technicians tip split when tip amount changes
    renderTechniciansTipSplit();
}

// Discount functions
function setDiscountPercentage(percentage) {
    const discountAmount = paymentSubtotal * (percentage / 100);
    paymentDiscount = discountAmount;
    document.getElementById('discountInput').value = discountAmount.toFixed(2);
    updatePaymentTotal();
}

function updateDiscountFromInput() {
    const discountInput = document.getElementById('discountInput');
    paymentDiscount = parseFloat(discountInput.value) || 0;
    updatePaymentTotal();
}

function updatePaymentTotal() {
    const total = paymentSubtotal + paymentTax + paymentTip - paymentDiscount;
    
    // Update modal displays (if they exist)
    const paymentTipDisplay = document.getElementById('paymentTipDisplay');
    const paymentTotalDisplay = document.getElementById('paymentTotalDisplay');
    if (paymentTipDisplay) paymentTipDisplay.textContent = `$${paymentTip.toFixed(2)}`;
    if (paymentTotalDisplay) paymentTotalDisplay.textContent = `$${total.toFixed(2)}`;
    
    // Update step 2 displays
    const checkoutTipDisplay = document.getElementById('checkoutTipDisplay');
    const checkoutDiscountDisplay = document.getElementById('checkoutDiscountDisplay');
    const checkoutTotalDisplay = document.getElementById('checkoutTotalDisplay');
    if (checkoutTipDisplay) checkoutTipDisplay.textContent = `$${paymentTip.toFixed(2)}`;
    if (checkoutDiscountDisplay) checkoutDiscountDisplay.textContent = `$${paymentDiscount.toFixed(2)}`;
    if (checkoutTotalDisplay) checkoutTotalDisplay.textContent = `$${total.toFixed(2)}`;
    
    const tipAmountHidden = document.getElementById('tipAmountHidden');
    if (tipAmountHidden) tipAmountHidden.value = paymentTip.toFixed(2);
    
    const discountAmountHidden = document.getElementById('discountAmountHidden');
    if (discountAmountHidden) discountAmountHidden.value = paymentDiscount.toFixed(2);
}

// Render technicians tip split section
function renderTechniciansTipSplit() {
    if (!assignedTechnicianIds || assignedTechnicianIds.length === 0) {
        const tipSection = document.getElementById('techniciansTipSection');
        if (tipSection) tipSection.classList.add('hidden');
        return;
    }
    
    const tipSection = document.getElementById('techniciansTipSection');
    if (tipSection) tipSection.classList.remove('hidden');
    
    // Get assigned technicians
    const assignedTechnicians = techniciansData.filter(technician => {
        const technicianIdStr = technician.id.toString();
        return assignedTechnicianIds.includes(technicianIdStr);
    });
    
    if (assignedTechnicians.length === 0) {
        if (tipSection) tipSection.classList.add('hidden');
        return;
    }
    
    // Initialize technician tips if not set
    if (Object.keys(technicianTips).length === 0) {
        assignedTechnicians.forEach(technician => {
            const technicianIdStr = technician.id.toString();
            const defaultPercentage = 100 / assignedTechnicians.length;
            technicianTips[technicianIdStr] = {
                percentage: defaultPercentage,
                amount: 0
            };
        });
    }
    
    const techniciansTipList = document.getElementById('techniciansTipList');
    if (!techniciansTipList) return;
    
    techniciansTipList.innerHTML = assignedTechnicians.map(technician => {
        const technicianIdStr = technician.id.toString();
        const initials = technician.initials || (technician.firstName?.[0] || '') + (technician.lastName?.[0] || '');
        const fullName = `${technician.firstName} ${technician.lastName}`;
        const profilePhoto = technician.photo || technician.profilePhoto || null;
        
        const tipData = technicianTips[technicianIdStr] || { percentage: 100 / assignedTechnicians.length, amount: 0 };
        const currentPercentage = tipData.percentage || (100 / assignedTechnicians.length);
        
        // Use amount from tipData if manually set, otherwise calculate from percentage
        const calculatedAmount = tipData.amount > 0 && tipData.amount !== (paymentTip * currentPercentage / 100) 
            ? tipData.amount 
            : (paymentTip * currentPercentage / 100);
        
        return `
            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200" data-technician-id="${technicianIdStr}">
                <div class="flex-shrink-0">
                    ${profilePhoto 
                        ? `<img src="${profilePhoto}" alt="${fullName}" class="w-10 h-10 rounded-full object-cover border-2 border-white">`
                        : `<div class="w-10 h-10 bg-[#e6f0f3] rounded-full flex items-center justify-center border-2 border-white">
                            <span class="text-sm font-bold text-[#003047]">${initials}</span>
                        </div>`
                    }
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">${fullName}</p>
                </div>
                <div class="flex gap-2 flex-1">
                    <div class="flex-1">
                        <div class="flex items-center border border-gray-300 rounded-lg">
                            <input type="number" 
                                 id="tip-percentage-${technicianIdStr}" 
                                 value="${currentPercentage % 1 === 0 ? currentPercentage : currentPercentage.toFixed(1)}" 
                                 step="0.1" 
                                 min="0" 
                                 max="100" 
                                 oninput="updateTechnicianTipPercentage('${technicianIdStr}', this.value)"
                                 class="flex-1 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent rounded-l-lg border-0">
                            <span class="px-3 py-2 text-sm text-gray-500 bg-gray-50 border-l border-gray-300 rounded-r-lg">%</span>
                        </div>
                    </div>
                    <div class="flex-1">
                        <div class="flex items-center border border-gray-300 rounded-lg">
                            <input type="number" 
                                 id="tip-amount-${technicianIdStr}" 
                                 value="${calculatedAmount.toFixed(2)}" 
                                 step="0.01" 
                                 min="0" 
                                 oninput="updateTechnicianTipAmount('${technicianIdStr}', this.value)"
                                 class="flex-1 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent rounded-l-lg border-0">
                            <span class="px-3 py-2 text-sm text-gray-500 bg-gray-50 border-l border-gray-300 rounded-r-lg">$</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }).join('');
    
    updateTotalTipSplit();
}

// Update technician tip percentage
function updateTechnicianTipPercentage(technicianId, value) {
    const percentage = parseFloat(value) || 0;
    
    if (!technicianTips[technicianId]) {
        technicianTips[technicianId] = { percentage: 0, amount: 0 };
    }
    
    technicianTips[technicianId].percentage = percentage;
    
    // Calculate and update amount based on percentage
    const calculatedAmount = paymentTip * percentage / 100;
    technicianTips[technicianId].amount = calculatedAmount;
    
    // Update the amount input field
    const amountInput = document.getElementById(`tip-amount-${technicianId}`);
    if (amountInput) {
        amountInput.value = calculatedAmount.toFixed(2);
    }
    
    updateTotalTipSplit();
}

// Update technician tip amount
function updateTechnicianTipAmount(technicianId, value) {
    const amount = parseFloat(value) || 0;
    
    if (!technicianTips[technicianId]) {
        technicianTips[technicianId] = { percentage: 0, amount: 0 };
    }
    
    technicianTips[technicianId].amount = amount;
    
    // Calculate and update percentage based on amount
    if (paymentTip > 0) {
        const calculatedPercentage = (amount / paymentTip) * 100;
        technicianTips[technicianId].percentage = calculatedPercentage;
        
        // Update the percentage input field
        const percentageInput = document.getElementById(`tip-percentage-${technicianId}`);
        if (percentageInput) {
            percentageInput.value = calculatedPercentage % 1 === 0 ? calculatedPercentage : calculatedPercentage.toFixed(1);
        }
    }
    
    updateTotalTipSplit();
}

// Update total tip split display
function updateTotalTipSplit() {
    const assignedTechnicians = techniciansData.filter(technician => {
        const technicianIdStr = technician.id.toString();
        return assignedTechnicianIds.includes(technicianIdStr);
    });
    
    let totalSplit = 0;
    assignedTechnicians.forEach(technician => {
        const technicianIdStr = technician.id.toString();
        const tipData = technicianTips[technicianIdStr] || { percentage: 0, amount: 0 };
        
        // Always use amount field (it's kept in sync with percentage)
        const amount = tipData.amount || (paymentTip * (tipData.percentage || 0) / 100);
        totalSplit += amount;
    });
    
    const totalTipSplitDisplay = document.getElementById('totalTipSplitDisplay');
    if (totalTipSplitDisplay) {
        totalTipSplitDisplay.textContent = `$${totalSplit.toFixed(2)}`;
    }
}

function addPaymentAmount(amount) {
    const currentAmount = parseFloat(document.getElementById('paymentAmountValue').value) || 0;
    const newAmount = currentAmount + amount;
    paymentAmountStr = newAmount.toFixed(2);
    updatePaymentDisplay();
}

function addPaymentDigit(digit) {
    if (digit === '.') {
        if (paymentAmountStr === '') {
            paymentAmountStr = '0.';
        } else if (paymentAmountStr.includes('.')) {
            return;
        } else {
            paymentAmountStr += '.';
        }
    } else {
        paymentAmountStr += digit;
    }
    updatePaymentDisplay();
}

function removePaymentDigit() {
    if (paymentAmountStr.length > 0) {
        paymentAmountStr = paymentAmountStr.slice(0, -1);
        updatePaymentDisplay();
    }
}

function updatePaymentDisplay() {
    const displayEl = document.getElementById('paymentAmount');
    const valueEl = document.getElementById('paymentAmountValue');
    if (!displayEl || !valueEl) return;
    
    if (paymentAmountStr === '') {
        displayEl.textContent = '$0';
        valueEl.value = '0';
    } else {
        const amount = parseFloat(paymentAmountStr) || 0;
        displayEl.textContent = '$' + amount.toFixed(2);
        valueEl.value = amount.toFixed(2);
    }
}

// Update payment method
function updatePaymentMethod(method) {
    const hiddenInput = document.getElementById('paymentMethod');
    if (hiddenInput) {
        hiddenInput.value = method;
    }
}

// Update payment method circle styles
function updatePaymentMethodStyles() {
    const cashRadio = document.getElementById('paymentMethodCash');
    const cardRadio = document.getElementById('paymentMethodCard');
    const cashCircle = document.getElementById('paymentMethodCashCircle');
    const cardCircle = document.getElementById('paymentMethodCardCircle');
    
    if (cashRadio && cardRadio && cashCircle && cardCircle) {
        // Update Cash circle
        if (cashRadio.checked) {
            cashCircle.classList.remove('border-gray-300');
            cashCircle.classList.add('border-[#003047]', 'bg-[#003047]');
            cashCircle.querySelector('div').classList.remove('opacity-0');
            cashCircle.querySelector('div').classList.add('opacity-100');
        } else {
            cashCircle.classList.remove('border-[#003047]', 'bg-[#003047]');
            cashCircle.classList.add('border-gray-300');
            cashCircle.querySelector('div').classList.remove('opacity-100');
            cashCircle.querySelector('div').classList.add('opacity-0');
        }
        
        // Update Card circle
        if (cardRadio.checked) {
            cardCircle.classList.remove('border-gray-300');
            cardCircle.classList.add('border-[#003047]', 'bg-[#003047]');
            cardCircle.querySelector('div').classList.remove('opacity-0');
            cardCircle.querySelector('div').classList.add('opacity-100');
        } else {
            cardCircle.classList.remove('border-[#003047]', 'bg-[#003047]');
            cardCircle.classList.add('border-gray-300');
            cardCircle.querySelector('div').classList.remove('opacity-100');
            cardCircle.querySelector('div').classList.add('opacity-0');
        }
    }
}

function processPayment(event) {
    event.preventDefault();
    
    const paymentMethod = document.getElementById('paymentMethod').value;
    const paymentAmount = parseFloat(document.getElementById('paymentAmountValue').value) || 0;
    const total = paymentSubtotal + paymentTax + paymentTip - paymentDiscount;
    
    if (paymentAmount < total) {
        alert(`Payment amount ($${paymentAmount.toFixed(2)}) is less than total ($${total.toFixed(2)}). Please enter the correct amount.`);
        return;
    }
    
    // Calculate change
    const change = paymentAmount - total;
    
    // Here you would process the payment and save to backend/JSON
    console.log('Processing payment:', {
        appointmentId: appointmentId,
        customerId: customerData.id,
        services: cart,
        technicians: assignedTechnicianIds,
        subtotal: paymentSubtotal,
        tax: paymentTax,
        tip: paymentTip,
        discount: paymentDiscount,
        total: total,
        paymentMethod: paymentMethod,
        paymentAmount: paymentAmount,
        change: change
    });
    
    // Show success message and redirect
    showSuccessMessage('Payment processed successfully!');
    setTimeout(() => {
        window.location.href = 'tickets.php';
    }, 1500);
}

function showSuccessMessage(message) {
    const successDiv = document.createElement('div');
    successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    successDiv.textContent = message;
    document.body.appendChild(successDiv);
    setTimeout(() => successDiv.remove(), 3000);
}

// Load data on page load
document.addEventListener('DOMContentLoaded', function() {
    loadPaymentData();
});
</script>

<?php include '../includes/footer.php'; ?>

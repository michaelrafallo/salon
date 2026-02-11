@extends('layouts.salon')

@section('content')
@php
    $ticketsUrl = route('salon.booking.tickets');
@endphp
<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <!-- Customer Info and Step Tabs Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6 py-2 px-4">
            <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                <!-- Back Button -->
                <div class="flex-shrink-0">
                    <a href="{{ $ticketsUrl }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        <span class="text-sm font-medium">Back</span>
                    </a>
                </div>
                <!-- Left: Customer Info -->
                <div class="flex items-center gap-4 flex-1">
                    <div id="customerInfoContainer" class="flex items-center gap-3">
                        <!-- Customer info will be populated by JavaScript -->
                    </div>
                </div>
                <!-- Right: Step Tabs -->
                <div class="flex-shrink-0">
                    <div class="flex border-b border-gray-200">
                        <button onclick="salonPaySwitchStep(1)" id="step1Tab" class="flex-1 lg:flex-initial px-6 py-3 text-left border-b-2 border-[#003047] transition">
                            <div class="flex items-center gap-3">
                                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-[#003047] text-white text-sm font-semibold">1</span>
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900">Cart</h3>
                                    <p class="text-xs text-gray-500 hidden lg:block">Select services and assign to technicians</p>
                                </div>
                            </div>
                        </button>
                        <button type="button" onclick="salonPaySaveAndGoToCheckout()" id="step2Tab" class="flex-1 lg:flex-initial px-6 py-3 text-left border-b-2 border-transparent hover:border-gray-300 transition">
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
        <!-- Steps Container -->
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Step 1: Cart -->
            <div id="step1Content" class="step-content flex-1">
                <!-- Two Column Layout: Services | Technicians -->
                <div class="flex flex-col lg:flex-row gap-6 h-[calc(100vh-200px)] overflow-hidden">
                    <!-- Right Column: Technicians List -->
                    <div class="w-full lg:w-1/3 lg:order-2 flex flex-col min-w-0">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col h-full overflow-hidden">
                            <div class="flex items-center justify-between gap-3 mb-4 flex-shrink-0">
                                <h2 class="text-xl font-bold text-gray-900">Assigned Technicians</h2>
                                <button
                                    type="button"
                                    onclick="salonPayOpenAssignTechnicianModal()"
                                    class="inline-flex items-center gap-2 px-3 py-2 rounded-lg border border-gray-200 bg-white text-gray-700 hover:border-[#003047] hover:text-[#003047] hover:bg-[#e6f0f3] transition active:scale-95 text-sm font-medium"
                                    title="Assign technician"
                                    aria-label="Assign technician"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Assign
                                </button>
                            </div>
                            <div id="techniciansListContainer" class="flex-1 overflow-y-auto min-h-0 mb-4">
                                <!-- Technicians will be populated by JavaScript -->
                            </div>
                            <!-- Next Step Button -->
                            <div class="flex items-stretch gap-3 flex-shrink-0">
                                <button type="button" id="salonPayNextToCheckoutBtn" onclick="salonPaySaveAndGoToCheckout()" class="flex-1 px-6 py-4 text-lg font-semibold text-white bg-[#003047] rounded-lg hover:bg-[#002535] transition active:scale-95">
                                    Next: Checkout
                                </button>
                                <button
                                    type="button"
                                    id="salonPaySaveCartBtn"
                                    onclick="salonPaySaveCart()"
                                    class="w-14 px-4 py-4 rounded-lg border border-gray-200 bg-white text-gray-700 hover:border-[#003047] hover:text-[#003047] hover:bg-[#e6f0f3] transition active:scale-95 flex items-center justify-center"
                                    title="Save cart"
                                    aria-label="Save cart"
                                >
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 21H7a2 2 0 01-2-2V5a2 2 0 012-2h8l4 4v12a2 2 0 01-2 2z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 21v-8H7v8" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 3v4h8" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Left Column: Services Grid -->
                    <div class="w-full lg:w-2/3 lg:order-1 grid min-w-0">
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
                                        <input type="text" id="serviceSearchInput" placeholder="Search services..." class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-sm" oninput="salonPayFilterServices(this.value); salonPayUpdateClearButton(this.value);">
                                        <button type="button" id="clearSearchBtn" onclick="salonPayClearSearch()" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden transition">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- Categories Horizontal List (Slick Carousel) -->
                            <div class="mb-4 flex-shrink-0 categories-carousel-wrapper">
                                <div id="categoriesList" class="categories-slick-carousel">
                                    <!-- Categories will be loaded dynamically and initialized as Slick carousel -->
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
            </div>
            <!-- Step 2: Checkout -->
            <div id="step2Content" class="step-content hidden flex-1">
                <div class="grid grid-cols-1 lg:grid-cols-[1fr_auto_1fr] gap-6 items-stretch">
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
                                        <span class="text-gray-600">Sub Total</span>
                                        <span class="text-gray-900 font-medium" id="checkoutSubtotalDisplay">{{ $currencySymbol }}0.00</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <div class="flex items-center gap-2">
                                            <span class="text-gray-600">Discount</span>
                                            <button type="button" id="removeDiscountLink" onclick="salonPayRemoveDiscount()" class="text-xs font-medium text-[#003047] hover:text-[#002535] hover:underline hidden">Remove</button>
                                        </div>
                                        <span class="text-gray-900 font-medium text-red-600" id="checkoutDiscountDisplay">{{ $currencySymbol }}0.00</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <div class="flex items-center gap-2">
                                            <span class="text-gray-600">Credits</span>
                                            <button type="button" id="removeCreditsLink" onclick="salonPayRemoveCredits()" class="text-xs font-medium text-[#003047] hover:text-[#002535] hover:underline hidden">Remove</button>
                                        </div>
                                        <span class="text-gray-900 font-medium text-red-600" id="checkoutCreditsDisplay">{{ $currencySymbol }}0.00</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <div class="flex items-center gap-2">
                                            <span class="text-gray-600">Gift Card</span>
                                            <button type="button" id="removeGiftCardLink" onclick="salonPayRemoveGiftCard()" class="text-xs font-medium text-[#003047] hover:text-[#002535] hover:underline hidden">Remove</button>
                                        </div>
                                        <span class="text-gray-900 font-medium text-red-600" id="checkoutGiftCardDisplay">{{ $currencySymbol }}0.00</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600" id="checkoutTaxLabel">Tax</span>
                                        <span class="text-gray-900 font-medium" id="checkoutTaxDisplay">{{ $currencySymbol }}0.00</span>
                                    </div>
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600">Tip</span>
                                        <span class="text-gray-900 font-medium" id="checkoutTipDisplay">{{ $currencySymbol }}0.00</span>
                                    </div>
                                    <div class="flex justify-between items-center pt-2 border-t border-gray-200">
                                        <span class="text-lg font-semibold text-gray-900">Total</span>
                                        <span class="text-2xl font-bold text-gray-900" id="checkoutTotalDisplay">{{ $currencySymbol }}0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Technicians Tip Split Section -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mt-6">
                            <h4 class="text-lg font-semibold text-gray-900 mb-4">Technicians Tip</h4>
                            <!-- Tabs: Percentage, Even, and Custom -->
                            <div class="flex border-b border-gray-200 mb-4">
                                <button type="button" id="tipPercentageTab" onclick="salonPaySwitchTipSplitMode('percentage')" class="flex-1 px-4 py-2 text-sm font-medium text-gray-700 border-b-2 border-[#003047] bg-transparent transition">
                                    Percentage
                                </button>
                                <button type="button" id="tipEvenTab" onclick="salonPaySwitchTipSplitMode('even')" class="flex-1 px-4 py-2 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 transition">
                                    Even
                                </button>
                                <button type="button" id="tipCustomTab" onclick="salonPaySwitchTipSplitMode('custom')" class="flex-1 px-4 py-2 text-sm font-medium text-gray-500 border-b-2 border-transparent hover:text-gray-700 transition">
                                    Custom
                                </button>
                            </div>
                            <!-- Percentage Tab Content -->
                            <div id="percentageTabContent">
                                <!-- Tip Section -->
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tip</label>
                                    <div class="grid grid-cols-4 gap-2 mb-2">
                                        <button type="button" onclick="salonPaySetTipPercentage(10)" class="py-2 text-xs font-medium text-gray-700 bg-[#e6f0f3] rounded-lg hover:bg-[#b3d1d9] transition active:scale-95 border border-[#b3d1d9]">10%</button>
                                        <button type="button" onclick="salonPaySetTipPercentage(15)" class="py-2 text-xs font-medium text-gray-700 bg-[#e6f0f3] rounded-lg hover:bg-[#b3d1d9] transition active:scale-95 border border-[#b3d1d9]">15%</button>
                                        <button type="button" onclick="salonPaySetTipPercentage(20)" class="py-2 text-xs font-medium text-gray-700 bg-[#e6f0f3] rounded-lg hover:bg-[#b3d1d9] transition active:scale-95 border border-[#b3d1d9]">20%</button>
                                        <button type="button" onclick="salonPaySetTipPercentage(25)" class="py-2 text-xs font-medium text-gray-700 bg-[#e6f0f3] rounded-lg hover:bg-[#b3d1d9] transition active:scale-95 border border-[#b3d1d9]">25%</button>
                                    </div>
                                    <div>
                                        <input type="number" name="tip" id="tipInput" step="0.01" min="0" value="0" oninput="salonPayUpdateTipFromInput()" placeholder="0.00" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                                    </div>
                                </div>
                                <div id="techniciansTipSection">
                                    <div id="techniciansTipList" class="space-y-3 max-h-[300px] overflow-y-auto pr-2" style="scrollbar-width: thin; scrollbar-color: #cbd5e0 #f3f4f6;">
                                        <!-- Technicians tip inputs will be populated by JavaScript -->
                                    </div>
                                    <div class="mt-3 pt-3 border-t border-gray-200">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm font-medium text-gray-700">Total Split:</span>
                                            <span class="text-sm font-bold text-gray-900" id="totalTipSplitDisplay">{{ $currencySymbol }}0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Even Tab Content -->
                            <div id="evenTabContent" class="hidden">
                                <!-- Tip Section -->
                                <div class="mb-6">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Tip</label>
                                    <div class="grid grid-cols-4 gap-2 mb-2">
                                        <button type="button" onclick="salonPaySetTipPercentage(10)" class="py-2 text-xs font-medium text-gray-700 bg-[#e6f0f3] rounded-lg hover:bg-[#b3d1d9] transition active:scale-95 border border-[#b3d1d9]">10%</button>
                                        <button type="button" onclick="salonPaySetTipPercentage(15)" class="py-2 text-xs font-medium text-gray-700 bg-[#e6f0f3] rounded-lg hover:bg-[#b3d1d9] transition active:scale-95 border border-[#b3d1d9]">15%</button>
                                        <button type="button" onclick="salonPaySetTipPercentage(20)" class="py-2 text-xs font-medium text-gray-700 bg-[#e6f0f3] rounded-lg hover:bg-[#b3d1d9] transition active:scale-95 border border-[#b3d1d9]">20%</button>
                                        <button type="button" onclick="salonPaySetTipPercentage(25)" class="py-2 text-xs font-medium text-gray-700 bg-[#e6f0f3] rounded-lg hover:bg-[#b3d1d9] transition active:scale-95 border border-[#b3d1d9]">25%</button>
                                    </div>
                                    <div>
                                        <input type="number" name="tip" id="tipInputEven" step="0.01" min="0" value="0" oninput="salonPayUpdateTipFromInput()" placeholder="0.00" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                                    </div>
                                </div>
                                <div id="techniciansTipSectionEven">
                                    <div id="techniciansTipListEven" class="space-y-3 max-h-[300px] overflow-y-auto pr-2" style="scrollbar-width: thin; scrollbar-color: #cbd5e0 #f3f4f6;">
                                        <!-- Technicians tip inputs will be populated by JavaScript -->
                                    </div>
                                    <div class="mt-3 pt-3 border-t border-gray-200">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm font-medium text-gray-700">Total Split:</span>
                                            <span class="text-sm font-bold text-gray-900" id="totalTipSplitDisplayEven">{{ $currencySymbol }}0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Custom Tab Content -->
                            <div id="customTabContent" class="hidden">
                                <div id="techniciansTipSectionCustom">
                                    <div id="techniciansTipListCustom" class="space-y-3 max-h-[300px] overflow-y-auto pr-2" style="scrollbar-width: thin; scrollbar-color: #cbd5e0 #f3f4f6;">
                                        <!-- Technicians tip inputs will be populated by JavaScript -->
                                    </div>
                                    <div class="mt-3 pt-3 border-t border-gray-200">
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm font-medium text-gray-700">Total Split:</span>
                                            <span class="text-sm font-bold text-gray-900" id="totalTipSplitDisplayCustom">{{ $currencySymbol }}0.00</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Middle: Payment Method -->
                    <div class="flex flex-col h-full">
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 max-w-[300px] w-full h-full flex flex-col">
                            <label class="block text-sm font-medium text-gray-700 mb-4">Pay with:</label>
                            <div class="flex flex-col gap-2">
                                <input type="radio" name="payment_method" value="cash" id="paymentMethodCash" checked class="sr-only">
                                <button type="button" id="paymentMethodCashBtn" onclick="salonPaySelectPaymentMethod('cash')" class="p-4 border-2 border-[#003047] bg-[#e6f0f3] rounded-lg cursor-pointer hover:border-[#003047] transition-all duration-200 text-left flex items-center gap-3">
                                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-sm font-semibold text-gray-900">Cash</span>
                                </button>
                                <input type="radio" name="payment_method" value="card" id="paymentMethodCard" class="sr-only">
                                <button type="button" id="paymentMethodCardBtn" onclick="salonPaySelectPaymentMethod('card')" class="p-4 border-2 border-gray-200 bg-white rounded-lg cursor-pointer hover:border-[#003047] transition-all duration-200 text-left flex items-center gap-3">
                                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                    <span class="text-sm font-semibold text-gray-900">Card</span>
                                </button>
                                <button type="button" id="giftCardActionBtn" onclick="salonPayOpenGiftCardModal()" class="p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-[#003047] transition-all duration-200 text-left flex items-center gap-3">
                                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                                    </svg>
                                    <span class="text-sm font-semibold text-gray-900">Gift Card</span>
                                </button>
                                <button type="button" id="redeemActionBtn" onclick="salonPayOpenRedeemModal()" class="p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-[#003047] transition-all duration-200 text-left flex items-center gap-3">
                                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                                    </svg>
                                    <span class="text-sm font-semibold text-gray-900">Redeem</span>
                                </button>
                                <button type="button" id="discountActionBtn" onclick="salonPayOpenDiscountModal()" class="p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-[#003047] transition-all duration-200 text-left flex items-center gap-3">
                                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                    <span class="text-sm font-semibold text-gray-900">Discount</span>
                                </button>
                            </div>
                            <input type="hidden" id="paymentMethod" value="cash">
                        </div>
                    </div>
                    <!-- Right Side: Payment Input -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 flex flex-col space-y-4 lg:pl-6 h-full">
                        <!-- Pay Now Button -->
                        <form onsubmit="salonPayProcessPayment(event)">
                            <input type="hidden" name="tip_amount" id="tipAmountHidden" value="0">
                            <input type="hidden" name="discount_amount" id="discountAmountHidden" value="0">
                            <input type="hidden" name="credit_amount" id="creditAmountHidden" value="0">
                            <input type="hidden" name="credit_customer_id" id="creditCustomerIdHidden" value="">
                            <input type="hidden" name="gift_card_amount" id="giftCardAmountHidden" value="0">
                            <input type="hidden" name="gift_card_code" id="giftCardCodeHidden" value="">
                            <button type="submit" class="w-full px-6 py-4 text-lg font-semibold text-white bg-[#003047] rounded-lg hover:bg-[#002535] transition active:scale-95">
                                Pay Now
                            </button>
                        </form>
                        <!-- Amount Display (always visible) -->
                        <div class="bg-gray-50 rounded-lg p-2 text-center">
                            <div class="text-5xl font-bold text-gray-900" id="paymentAmount">{{ $currencySymbol }}0</div>
                            <input type="hidden" id="paymentAmountValue" value="0">
                        </div>
                        <!-- Cash Payment Content (Calculator) -->
                        <div id="cashPaymentContent" class="flex flex-col space-y-4 flex-1">
                            <!-- Quick Cash Buttons -->
                            <div class="grid grid-cols-5 gap-2">
                                <button type="button" onclick="salonPayAddPaymentAmount(5)" class="px-4 py-3 text-sm font-medium text-gray-700 bg-teal-50 rounded-lg hover:bg-teal-100 transition active:scale-95 border border-teal-200">{{ $currencySymbol }}5</button>
                                <button type="button" onclick="salonPayAddPaymentAmount(10)" class="px-4 py-3 text-sm font-medium text-gray-700 bg-teal-50 rounded-lg hover:bg-teal-100 transition active:scale-95 border border-teal-200">{{ $currencySymbol }}10</button>
                                <button type="button" onclick="salonPayAddPaymentAmount(20)" class="px-4 py-3 text-sm font-medium text-gray-700 bg-teal-50 rounded-lg hover:bg-teal-100 transition active:scale-95 border border-teal-200">{{ $currencySymbol }}20</button>
                                <button type="button" onclick="salonPayAddPaymentAmount(50)" class="px-4 py-3 text-sm font-medium text-gray-700 bg-teal-50 rounded-lg hover:bg-teal-100 transition active:scale-95 border border-teal-200">{{ $currencySymbol }}50</button>
                                <button type="button" onclick="salonPayAddPaymentAmount(100)" class="px-4 py-3 text-sm font-medium text-gray-700 bg-teal-50 rounded-lg hover:bg-teal-100 transition active:scale-95 border border-teal-200">{{ $currencySymbol }}100</button>
                            </div>
                            <!-- Numeric Keypad -->
                            <div class="grid grid-cols-3 gap-2">
                                <button type="button" onclick="salonPayAddPaymentDigit('1')" class="px-4 py-4 text-lg font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition active:scale-95">1</button>
                                <button type="button" onclick="salonPayAddPaymentDigit('2')" class="px-4 py-4 text-lg font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition active:scale-95">2</button>
                                <button type="button" onclick="salonPayAddPaymentDigit('3')" class="px-4 py-4 text-lg font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition active:scale-95">3</button>
                                <button type="button" onclick="salonPayAddPaymentDigit('4')" class="px-4 py-4 text-lg font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition active:scale-95">4</button>
                                <button type="button" onclick="salonPayAddPaymentDigit('5')" class="px-4 py-4 text-lg font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition active:scale-95">5</button>
                                <button type="button" onclick="salonPayAddPaymentDigit('6')" class="px-4 py-4 text-lg font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition active:scale-95">6</button>
                                <button type="button" onclick="salonPayAddPaymentDigit('7')" class="px-4 py-4 text-lg font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition active:scale-95">7</button>
                                <button type="button" onclick="salonPayAddPaymentDigit('8')" class="px-4 py-4 text-lg font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition active:scale-95">8</button>
                                <button type="button" onclick="salonPayAddPaymentDigit('9')" class="px-4 py-4 text-lg font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition active:scale-95">9</button>
                                <button type="button" onclick="salonPayAddPaymentDigit('.')" class="px-4 py-4 text-lg font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition active:scale-95">.</button>
                                <button type="button" onclick="salonPayAddPaymentDigit('0')" class="px-4 py-4 text-lg font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition active:scale-95">0</button>
                                <button type="button" onclick="salonPayRemovePaymentDigit()" class="px-4 py-4 text-lg font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition active:scale-95">
                                    <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2M3 12l6.414 6.414a2 2 0 001.414.586H19a2 2 0 002-2V7a2 2 0 00-2-2h-8.172a2 2 0 00-1.414.586L3 12z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <!-- Card Payment Content -->
                        <div id="cardPaymentContent" class="hidden flex flex-col space-y-4 flex-1">
                            <div class="space-y-4 flex-1">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Card Number</label>
                                    <input type="text" id="cardNumberInput" placeholder="4242 4242 4242 4242" maxlength="19" oninput="salonPayFormatCardNumber(this)" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Approved Code</label>
                                    <input type="text" id="approvedCodeInput" placeholder="Enter approved code" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@push('styles')
<!-- Slick Carousel CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
<style>
    /* Slick carousel custom styling - Equal heights */
    .categories-carousel-wrapper {
        position: relative;
        padding: 0 40px;
    }
    /* Hide carousel until Slick initializes to prevent FOUC (Flash of Unstyled Content) */
    .categories-slick-carousel {
        height: 70px; /* Fixed carousel height */
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease-in-out;
    }
    .categories-slick-carousel.slick-initialized {
        opacity: 1;
        visibility: visible;
    }
    /* Fallback: show carousel if Slick doesn't initialize after 3 seconds */
    .categories-slick-carousel.show-fallback {
        opacity: 1 !important;
        visibility: visible !important;
        display: flex !important;
        flex-wrap: wrap !important;
        gap: 8px !important;
        overflow-x: auto !important;
    }
    .categories-slick-carousel.show-fallback > div {
        flex: 0 0 auto;
        width: calc(16.666% - 7px); /* ~6 items per row */
    }
    @media (max-width: 1024px) {
        .categories-slick-carousel.show-fallback > div {
            width: calc(25% - 6px); /* 4 items per row */
        }
    }
    @media (max-width: 640px) {
        .categories-slick-carousel.show-fallback > div {
            width: calc(50% - 4px); /* 2 items per row */
        }
    }
    .categories-slick-carousel .slick-slide {
        margin: 0 4px;
        height: 70px !important; /* Force equal height */
        display: flex;
        align-items: stretch;
    }
    .categories-slick-carousel .slick-slide > div {
        height: 70px !important; /* Force inner div height */
        width: 100%;
        display: flex;
    }
    .categories-slick-carousel .slick-list {
        margin: 0 -4px;
        height: 70px; /* Match carousel height */
    }
    .categories-slick-carousel .slick-track {
        display: flex !important;
        align-items: stretch;
        height: 70px; /* Match carousel height */
    }
    .category-card {
        word-wrap: break-word;
        overflow-wrap: break-word;
        hyphens: auto;
        height: 70px !important; /* Force button height */
        min-height: 70px;
        max-height: 70px;
    }
    .categories-slick-carousel .slick-prev,
    .categories-slick-carousel .slick-next {
        width: 32px;
        height: 32px;
        background: white;
        border: 1px solid #d1d5db;
        border-radius: 50%;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        z-index: 10;
    }
    .categories-slick-carousel .slick-prev {
        left: -40px;
    }
    .categories-slick-carousel .slick-next {
        right: -40px;
    }
    /* Chevron icons for arrows */
    .categories-slick-carousel .slick-prev:before,
    .categories-slick-carousel .slick-next:before {
        content: '';
        display: inline-block;
        width: 16px;
        height: 16px;
        background-size: contain;
        background-repeat: no-repeat;
        background-position: center;
        opacity: 1;
    }
    .categories-slick-carousel .slick-prev:before {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%234b5563'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15 19l-7-7 7-7'/%3E%3C/svg%3E");
    }
    .categories-slick-carousel .slick-next:before {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%234b5563'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 5l7 7-7 7'/%3E%3C/svg%3E");
    }
    .categories-slick-carousel .slick-prev:hover,
    .categories-slick-carousel .slick-next:hover {
        background: #f9fafb;
    }
    .categories-slick-carousel .slick-disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>
@endpush

@push('scripts')
<!-- jQuery (required for Slick) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<!-- Slick Carousel JS -->
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

<script>
window.salonJsonBase = '{{ url("api/salon/data") }}';
window.salonTicketsUrl = '{{ $ticketsUrl }}';
window.salonApiAppointmentsUrl = '{{ url("api/salon/appointments") }}';
window.salonApiBase = '{{ url("api/salon") }}';
window.salonCurrencySymbol = '{{ $currencySymbol }}';
window.salonPayBootstrap = @json($payBootstrap ?? null);
window.salonStorageUrl = '{{ rtrim(asset("storage"), "/") }}';

// Slick Carousel initialization function - with better error handling
window.salonPayInitializeSlickCarousel = function() {
    // Check if jQuery is available
    if (typeof jQuery === 'undefined' || typeof $ === 'undefined') {
        console.error('jQuery is not loaded. Slick carousel cannot initialize.');
        // Show carousel anyway without Slick
        var carousel = document.getElementById('categoriesList');
        if (carousel) {
            carousel.style.opacity = '1';
            carousel.style.visibility = 'visible';
        }
        return;
    }

    // Check if Slick is available
    if (typeof $.fn.slick === 'undefined') {
        console.error('Slick carousel library is not loaded.');
        // Show carousel anyway without Slick
        var carousel = document.getElementById('categoriesList');
        if (carousel) {
            carousel.style.opacity = '1';
            carousel.style.visibility = 'visible';
        }
        return;
    }

    var $carousel = $('#categoriesList');

    if ($carousel.length === 0) {
        console.warn('Categories carousel element not found');
        return;
    }

    // Destroy existing instance if any
    if ($carousel.hasClass('slick-initialized')) {
        try {
            $carousel.slick('unslick');
        } catch (e) {
            console.warn('Error destroying previous slick instance:', e);
        }
    }

    try {
        // Initialize Slick carousel with equal heights
        $carousel.slick({
            slidesToShow: 6,
            slidesToScroll: 6,
            infinite: false,
            arrows: true,
            dots: false,
            adaptiveHeight: false, // Maintain equal height for all slides
            variableWidth: false,   // Ensure consistent width
            lazyLoad: 'ondemand',   // Improve performance
            responsive: [
                {
                    breakpoint: 1024,
                    settings: {
                        slidesToShow: 4,
                        slidesToScroll: 4,
                        adaptiveHeight: false
                    }
                },
                {
                    breakpoint: 640,
                    settings: {
                        slidesToShow: 2,
                        slidesToScroll: 2,
                        adaptiveHeight: false
                    }
                }
            ]
        });

        console.log('Slick carousel initialized successfully');

        // Ensure carousel is visible after initialization
        $carousel.css({
            'opacity': '1',
            'visibility': 'visible'
        });
    } catch (error) {
        console.error('Slick carousel initialization failed:', error);
        // Fallback: show carousel even if Slick fails
        $carousel.css({
            'opacity': '1',
            'visibility': 'visible'
        });
    }
};

// Ensure scripts are loaded before initializing
(function() {
    var scriptsLoaded = 0;
    var scriptsNeeded = 2; // jQuery + Slick

    function checkScriptsLoaded() {
        scriptsLoaded++;
        if (scriptsLoaded >= scriptsNeeded) {
            console.log('All carousel dependencies loaded');
        }
    }

    // Check if jQuery is already loaded
    if (typeof jQuery !== 'undefined') {
        checkScriptsLoaded();
    }

    // Check if Slick is already loaded
    if (typeof $.fn !== 'undefined' && typeof $.fn.slick !== 'undefined') {
        checkScriptsLoaded();
    }
})();
</script>
<script src="{{ asset('js/salon-pay.js') }}"></script>
@endpush
@endsection

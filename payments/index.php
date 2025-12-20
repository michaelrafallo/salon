<?php
$pageTitle = 'Payments';
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/modal.php';
?>

<!-- Main Content Area -->
<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Payments</h1>
            <!-- View Toggle -->
            <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-1">
                <button id="listViewBtn" onclick="toggleView('list')" class="p-2 rounded-md hover:bg-white transition active:scale-95">
                    <svg class="w-5 h-5 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
                </button>
                <button id="gridViewBtn" onclick="toggleView('grid')" class="p-2 rounded-md hover:bg-white transition active:scale-95">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Recent Transactions List View -->
        <div id="listView" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Transactions</h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700">Customer</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700">Amount</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700">Method</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700">Status</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700">Date</th>
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="listViewBody" class="divide-y divide-gray-200">
                        <!-- Payment rows will be dynamically loaded here -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Transactions Grid View -->
        <div id="gridView" class="hidden">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Transactions</h2>
            <div id="gridViewContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                <!-- Payment cards will be dynamically loaded here -->
            </div>
        </div>

        <!-- Results Counter and Per Page Selector -->
        <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div id="paymentsResultsCounter" class="text-sm text-gray-600">
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
        <div id="paymentsPagination" class="mt-4 flex justify-center"></div>
                                </div>
</main>

<script>
// Payment data
let allPayments = [];
let paymentsData = []; // Filtered payments for display
let PAGE_SIZE = 15; // Can be changed by user
let currentPage = 1;
let totalPages = 1;

// Format date from YYYY-MM-DD to M d, Y format
function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString + 'T00:00:00');
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const month = months[date.getMonth()];
    const day = date.getDate();
    const year = date.getFullYear();
    return `${month} ${day}, ${year}`;
}

// Fetch payments from JSON
async function initializePayments() {
    try {
        const response = await fetch('../json/payments.json');
        const data = await response.json();
        
        // Process payments and format dates
        allPayments = data.payments.map(payment => ({
            ...payment,
            date: formatDate(payment.date)
        }));
        
        paymentsData = allPayments;
        renderPayments();
    } catch (error) {
        console.error('Error fetching payments:', error);
        showErrorMessage('Failed to load payments data');
        paymentsData = [];
        renderPayments();
    }
}

// Render payments in list and grid views
function renderPayments() {
    updatePaginationState();
    renderListView();
    renderGridView();
    renderPagination();
    updateResultsCounter();
}

// Render list view
function renderListView() {
    const listViewBody = document.getElementById('listViewBody');
    if (!listViewBody) return;
    
    const paginatedPayments = getPaginatedPayments();
    
    if (paginatedPayments.length === 0) {
        listViewBody.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-12 text-center text-gray-500 text-sm">
                    No payments found.
                            </td>
                        </tr>
        `;
        return;
    }
    
    listViewBody.innerHTML = paginatedPayments.map(payment => `
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 ${payment.customerColor} rounded-full flex items-center justify-center">
                                        <span class="text-xs font-bold ${payment.customerTextColor}">${payment.customerInitials}</span>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">${payment.customerName}</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-sm font-semibold text-gray-900">$${payment.amount.toFixed(2)}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 ${payment.methodColor} ${payment.methodTextColor} text-xs font-medium rounded">${payment.method}</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 ${payment.statusColor} ${payment.statusTextColor} text-xs font-medium rounded">${payment.status}</span>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600">${payment.date}</td>
                            <td class="py-3 px-4">
                                <button onclick="openReceiptModal('${payment.customerName}', '${payment.id}', '$${payment.amount.toFixed(2)}', '${payment.date}')" class="text-[#003047] hover:text-[#002535] text-sm font-medium">View Receipt</button>
                            </td>
                        </tr>
    `).join('');
}

// Render grid view
function renderGridView() {
    const gridViewContainer = document.getElementById('gridViewContainer');
    if (!gridViewContainer) return;
    
    const paginatedPayments = getPaginatedPayments();
    
    if (paginatedPayments.length === 0) {
        gridViewContainer.innerHTML = `
            <div class="col-span-full text-center py-12 text-gray-500 text-sm">
                No payments found.
            </div>
        `;
        return;
    }
    
    gridViewContainer.innerHTML = paginatedPayments.map(payment => `
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 ${payment.customerColor} rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-bold ${payment.customerTextColor}">${payment.customerInitials}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-900 text-lg truncate">${payment.customerName}</h3>
                            <p class="text-xs text-gray-500">${payment.id}</p>
                        </div>
                    </div>
                    <div class="mb-4">
                        <p class="text-2xl font-bold text-gray-900 mb-2">$${payment.amount.toFixed(2)}</p>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-1 ${payment.methodColor} ${payment.methodTextColor} text-xs font-medium rounded">${payment.method}</span>
                            <span class="px-2 py-1 ${payment.statusColor} ${payment.statusTextColor} text-xs font-medium rounded">${payment.status}</span>
                        </div>
                    </div>
                    <button onclick="openReceiptModal('${payment.customerName}', '${payment.id}', '$${payment.amount.toFixed(2)}', '${payment.date}')" class="w-full px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm active:scale-95">
                        View Receipt
                    </button>
                </div>
    `).join('');
}

// Pagination helpers
function getPaginatedPayments() {
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity) {
        return paymentsData;
    }
    const startIndex = (currentPage - 1) * PAGE_SIZE;
    return paymentsData.slice(startIndex, startIndex + PAGE_SIZE);
}

function updatePaginationState() {
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity) {
        totalPages = 1;
        currentPage = 1;
        return;
    }
    totalPages = Math.max(1, Math.ceil(paymentsData.length / PAGE_SIZE));
    if (currentPage > totalPages) {
        currentPage = totalPages;
    }
    if (currentPage < 1) {
        currentPage = 1;
    }
}

function renderPagination() {
    const paginationContainer = document.getElementById('paymentsPagination');
    if (!paginationContainer) return;
    
    // Hide pagination if showing all or if results fit in one page
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity || paymentsData.length <= PAGE_SIZE || totalPages <= 1) {
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
    renderPayments();
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
    renderPayments();
    updateResultsCounter();
    
    // Save preference to localStorage
    localStorage.setItem('paymentsPerPage', value);
}

// Update results counter
function updateResultsCounter() {
    const resultsCounter = document.getElementById('paymentsResultsCounter');
    if (!resultsCounter) return;
    
    const total = paymentsData.length;
    
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

// View toggle functionality
let currentView = localStorage.getItem('paymentsView') || 'list';

function toggleView(view) {
    currentView = view;
    localStorage.setItem('paymentsView', view);
    
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
document.addEventListener('DOMContentLoaded', async function() {
    // Initialize per page selector from localStorage
    const savedPerPage = localStorage.getItem('paymentsPerPage');
    if (savedPerPage) {
        const perPageSelect = document.getElementById('perPageSelect');
        if (perPageSelect) {
            perPageSelect.value = savedPerPage;
            PAGE_SIZE = savedPerPage === 'all' ? Infinity : parseInt(savedPerPage);
        }
    }
    
    await initializePayments();
    toggleView(currentView);
});

function openReceiptModal(customerName, transactionId, amount, date) {
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
                        <span class="text-sm text-gray-600">Transaction #:</span>
                        <span class="text-sm font-medium text-gray-900">${transactionId}</span>
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
                        <span class="text-2xl font-bold text-gray-900">${amount}</span>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-6">
                <button onclick="openVoidConfirmationModal('${transactionId}', '${customerName}', '${amount}')" class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium active:scale-95 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Void
                </button>
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

// Open void confirmation modal (separate modal with higher z-index)
function openVoidConfirmationModal(transactionId, customerName, amount) {
    // Create a separate modal overlay with higher z-index
    let voidModalOverlay = document.getElementById('voidModalOverlay');
    
    // Create the overlay if it doesn't exist
    if (!voidModalOverlay) {
        voidModalOverlay = document.createElement('div');
        voidModalOverlay.id = 'voidModalOverlay';
        voidModalOverlay.className = 'fixed inset-0 bg-black bg-opacity-60 z-[60] flex items-center justify-center p-2 sm:p-4 backdrop-blur-sm transition-opacity duration-300';
        document.body.appendChild(voidModalOverlay);
    }
    
    const modalContent = `
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-95" onclick="event.stopPropagation()" id="voidModalContainer">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-900">Confirm Void Transaction</h3>
                    <button onclick="closeVoidModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="space-y-4">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div class="flex items-start gap-3">
                            <svg class="w-6 h-6 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-red-900 mb-2">Are you sure you want to void this transaction?</p>
                                <p class="text-sm text-red-700">This action cannot be undone.</p>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-2 border-t border-gray-200 pt-4">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Transaction #:</span>
                            <span class="text-sm font-medium text-gray-900">${transactionId}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Customer:</span>
                            <span class="text-sm font-medium text-gray-900">${customerName}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600">Amount:</span>
                            <span class="text-sm font-medium text-gray-900">${amount}</span>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end gap-3 pt-6">
                    <button onclick="closeVoidModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">
                        Cancel
                    </button>
                    <button onclick="voidPayment('${transactionId}')" class="px-6 py-3 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium active:scale-95 flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Confirm Void
                    </button>
                </div>
            </div>
        </div>
    `;
    
    voidModalOverlay.innerHTML = modalContent;
    voidModalOverlay.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Animate modal in
    setTimeout(() => {
        const container = document.getElementById('voidModalContainer');
        if (container) {
            container.classList.remove('scale-95');
            container.classList.add('scale-100');
        }
    }, 10);
    
    // Close on outside click
    voidModalOverlay.onclick = function(e) {
        if (e.target === voidModalOverlay) {
            closeVoidModal();
        }
    };
}

// Close void confirmation modal
function closeVoidModal() {
    const voidModalOverlay = document.getElementById('voidModalOverlay');
    const voidModalContainer = document.getElementById('voidModalContainer');
    
    if (voidModalContainer) {
        voidModalContainer.classList.remove('scale-100');
        voidModalContainer.classList.add('scale-95');
    }
    
    setTimeout(() => {
        if (voidModalOverlay) {
            voidModalOverlay.classList.add('hidden');
        }
        document.body.style.overflow = 'auto';
    }, 200);
}

// Void payment function
function voidPayment(transactionId) {
    // Close the void confirmation modal
    closeVoidModal();
    
    // Find and update the payment status
    const paymentIndex = allPayments.findIndex(p => p.id === transactionId);
    if (paymentIndex !== -1) {
        // Update payment status to voided
        allPayments[paymentIndex].status = 'Voided';
        allPayments[paymentIndex].statusColor = 'bg-gray-100';
        allPayments[paymentIndex].statusTextColor = 'text-gray-700';
        
        // Update paymentsData
        paymentsData = allPayments;
        
        // Re-render payments
        renderPayments();
        
        // Close the receipt modal
        closeModal();
        
        // Show success message
        showSuccessMessage('Transaction voided successfully');
    } else {
        showErrorMessage('Transaction not found');
    }
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

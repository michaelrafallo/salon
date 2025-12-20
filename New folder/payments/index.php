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
                            <th class="text-left py-3 px-4 text-sm font-medium text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-[#e6f0f3] rounded-full flex items-center justify-center">
                                        <span class="text-xs font-bold text-[#003047]">SJ</span>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">Sarah Johnson</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-sm font-semibold text-gray-900">$55.00</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded">Authorize.net</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded">Completed</span>
                            </td>
                            <td class="py-3 px-4">
                                <button onclick="openReceiptModal('Sarah Johnson', 'TXN001', '$55.00', '<?php echo date('M d, Y'); ?>')" class="text-[#003047] hover:text-[#002535] text-sm font-medium">View Receipt</button>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center">
                                        <span class="text-xs font-bold text-purple-600">EC</span>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">Emily Chen</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-sm font-semibold text-gray-900">$90.00</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded">NMI.com</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded">Completed</span>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600"><?php echo date('M d, Y'); ?></td>
                            <td class="py-3 px-4">
                                <button onclick="openReceiptModal('Emily Chen', 'TXN002', '$90.00', '<?php echo date('M d, Y'); ?>')" class="text-[#003047] hover:text-[#002535] text-sm font-medium">View Receipt</button>
                            </td>
                        </tr>
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-teal-100 rounded-full flex items-center justify-center">
                                        <span class="text-xs font-bold text-teal-600">JM</span>
                                    </div>
                                    <span class="text-sm font-medium text-gray-900">Jessica Martinez</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-sm font-semibold text-gray-900">$75.00</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 bg-[#e6f0f3] text-[#003047] text-xs font-medium rounded">Cash</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded">Completed</span>
                            </td>
                            <td class="py-3 px-4 text-sm text-gray-600"><?php echo date('M d, Y'); ?></td>
                            <td class="py-3 px-4">
                                <button onclick="openReceiptModal('Jessica Martinez', 'TXN003', '$75.00', '<?php echo date('M d, Y'); ?>')" class="text-[#003047] hover:text-[#002535] text-sm font-medium">View Receipt</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Transactions Grid View -->
        <div id="gridView" class="hidden">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Transactions</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                <!-- Transaction Card 1 -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-[#e6f0f3] rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-bold text-[#003047]">SJ</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-900 text-lg truncate">Sarah Johnson</h3>
                            <p class="text-xs text-gray-500">TXN001</p>
                        </div>
                    </div>
                    <div class="mb-4">
                        <p class="text-2xl font-bold text-gray-900 mb-2">$55.00</p>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded">Authorize.net</span>
                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded">Completed</span>
                        </div>
                    </div>
                    <button onclick="openReceiptModal('Sarah Johnson', 'TXN001', '$55.00', '<?php echo date('M d, Y'); ?>')" class="w-full px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm active:scale-95">
                        View Receipt
                    </button>
                </div>

                <!-- Transaction Card 2 -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-bold text-purple-600">EC</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-900 text-lg truncate">Emily Chen</h3>
                            <p class="text-xs text-gray-500">TXN002</p>
                        </div>
                    </div>
                    <div class="mb-4">
                        <p class="text-2xl font-bold text-gray-900 mb-2">$90.00</p>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded">NMI.com</span>
                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded">Completed</span>
                        </div>
                    </div>
                    <button onclick="openReceiptModal('Emily Chen', 'TXN002', '$90.00', '<?php echo date('M d, Y'); ?>')" class="w-full px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm active:scale-95">
                        View Receipt
                    </button>
                </div>

                <!-- Transaction Card 3 -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="w-12 h-12 bg-teal-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-bold text-teal-600">JM</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-semibold text-gray-900 text-lg truncate">Jessica Martinez</h3>
                            <p class="text-xs text-gray-500">TXN003</p>
                        </div>
                    </div>
                    <div class="mb-4">
                        <p class="text-2xl font-bold text-gray-900 mb-2">$75.00</p>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-1 bg-[#e6f0f3] text-[#003047] text-xs font-medium rounded">Cash</span>
                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded">Completed</span>
                        </div>
                    </div>
                    <button onclick="openReceiptModal('Jessica Martinez', 'TXN003', '$75.00', '<?php echo date('M d, Y'); ?>')" class="w-full px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm active:scale-95">
                        View Receipt
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
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
    }
}

// Initialize view on page load
document.addEventListener('DOMContentLoaded', function() {
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

function showSuccessMessage(message) {
    const successDiv = document.createElement('div');
    successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    successDiv.textContent = message;
    document.body.appendChild(successDiv);
    setTimeout(() => successDiv.remove(), 3000);
}
</script>

<?php include '../includes/footer.php'; ?>

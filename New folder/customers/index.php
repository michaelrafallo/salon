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
                <input type="text" placeholder="Search customers by name, phone, or email..." class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-base">
            </div>
        </div>

        <!-- Customers Grid View - Tablet/Touch Optimized -->
        <div id="gridView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- Customer Card 1 -->
            <div onclick="window.location.href='view.php?id=1'" class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow cursor-pointer active:scale-95">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-16 h-16 bg-[#e6f0f3] rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-2xl font-bold text-[#003047]">SJ</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 text-lg truncate">Sarah Johnson</h3>
                        <p class="text-sm text-gray-500 truncate">sarah.j@email.com</p>
                        <p class="text-sm text-gray-500">(555) 123-4567</p>
                    </div>
                </div>
                
                <!-- Stats -->
                <div class="grid grid-cols-2 gap-3 mb-4 pt-4 border-t border-gray-200">
                    <div>
                        <p class="text-xs text-gray-500">Total Visits</p>
                        <p class="text-lg font-bold text-gray-900">12</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Last Visit</p>
                        <p class="text-sm font-medium text-gray-900">2 days ago</p>
                    </div>
                </div>
            </div>

            <!-- Customer Card 2 -->
            <div onclick="window.location.href='view.php?id=2'" class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow cursor-pointer active:scale-95">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-2xl font-bold text-purple-600">EC</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 text-lg truncate">Emily Chen</h3>
                        <p class="text-sm text-gray-500 truncate">emily.c@email.com</p>
                        <p class="text-sm text-gray-500">(555) 234-5678</p>
                    </div>
                </div>
                
                <!-- Stats -->
                <div class="grid grid-cols-2 gap-3 mb-4 pt-4 border-t border-gray-200">
                    <div>
                        <p class="text-xs text-gray-500">Total Visits</p>
                        <p class="text-lg font-bold text-gray-900">8</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Last Visit</p>
                        <p class="text-sm font-medium text-gray-900">1 week ago</p>
                    </div>
                </div>
            </div>

            <!-- Customer Card 3 -->
            <div onclick="window.location.href='view.php?id=3'" class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow cursor-pointer active:scale-95">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-16 h-16 bg-teal-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-2xl font-bold text-teal-600">JM</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 text-lg truncate">Jessica Martinez</h3>
                        <p class="text-sm text-gray-500 truncate">jessica.m@email.com</p>
                        <p class="text-sm text-gray-500">(555) 345-6789</p>
                    </div>
                </div>
                
                <!-- Stats -->
                <div class="grid grid-cols-2 gap-3 mb-4 pt-4 border-t border-gray-200">
                    <div>
                        <p class="text-xs text-gray-500">Total Visits</p>
                        <p class="text-lg font-bold text-gray-900">15</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Last Visit</p>
                        <p class="text-sm font-medium text-gray-900">Today</p>
                    </div>
                </div>
            </div>

            <!-- Customer Card 4 -->
            <div onclick="window.location.href='view.php?id=4'" class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow cursor-pointer active:scale-95">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-2xl font-bold text-indigo-600">AT</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 text-lg truncate">Amanda Taylor</h3>
                        <p class="text-sm text-gray-500 truncate">amanda.t@email.com</p>
                        <p class="text-sm text-gray-500">(555) 456-7890</p>
                    </div>
                </div>
                
                <!-- Stats -->
                <div class="grid grid-cols-2 gap-3 mb-4 pt-4 border-t border-gray-200">
                    <div>
                        <p class="text-xs text-gray-500">Total Visits</p>
                        <p class="text-lg font-bold text-gray-900">5</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Last Visit</p>
                        <p class="text-sm font-medium text-gray-900">3 days ago</p>
                    </div>
                </div>
            </div>
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
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr onclick="window.location.href='view.php?id=1'" class="hover:bg-gray-50 cursor-pointer transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-[#e6f0f3] rounded-full flex items-center justify-center flex-shrink-0 mr-3">
                                        <span class="text-sm font-bold text-[#003047]">SJ</span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">Sarah Johnson</div>
                                        <div class="text-sm text-gray-500">sarah.j@email.com</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">(555) 123-4567</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">12</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">2 days ago</div>
                            </td>
                        </tr>
                        <tr onclick="window.location.href='view.php?id=2'" class="hover:bg-gray-50 cursor-pointer transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0 mr-3">
                                        <span class="text-sm font-bold text-purple-600">EC</span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">Emily Chen</div>
                                        <div class="text-sm text-gray-500">emily.c@email.com</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">(555) 234-5678</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">8</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">1 week ago</div>
                            </td>
                        </tr>
                        <tr onclick="window.location.href='view.php?id=3'" class="hover:bg-gray-50 cursor-pointer transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-teal-100 rounded-full flex items-center justify-center flex-shrink-0 mr-3">
                                        <span class="text-sm font-bold text-teal-600">JM</span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">Jessica Martinez</div>
                                        <div class="text-sm text-gray-500">jessica.m@email.com</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">(555) 345-6789</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">15</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">Today</div>
                            </td>
                        </tr>
                        <tr onclick="window.location.href='view.php?id=4'" class="hover:bg-gray-50 cursor-pointer transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0 mr-3">
                                        <span class="text-sm font-bold text-indigo-600">AT</span>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">Amanda Taylor</div>
                                        <div class="text-sm text-gray-500">amanda.t@email.com</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">(555) 456-7890</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">5</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">3 days ago</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script>
// View toggle functionality
let currentView = localStorage.getItem('customersView') || 'grid';

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

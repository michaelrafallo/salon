<?php
$pageTitle = 'Staff';
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/modal.php';
?>

<!-- Main Content Area -->
<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Staff</h1>
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
                <button onclick="openNewUserModal()" class="px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm sm:text-base active:scale-95">
                    + New Staff
                </button>
            </div>
        </div>

        <!-- Role Filter Tabs and Search Bar -->
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <!-- Role Filter Tabs -->
            <div class="flex items-center gap-2 border-b border-gray-200 overflow-x-auto">
                <button onclick="filterByRole('all')" id="tab-all" class="role-tab px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300 transition whitespace-nowrap">
                    All
                </button>
                <button onclick="filterByRole('Admin')" id="tab-Admin" class="role-tab px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300 transition whitespace-nowrap">
                    Admin
                </button>
                <button onclick="filterByRole('Receptionist')" id="tab-Receptionist" class="role-tab px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300 transition whitespace-nowrap">
                    Receptionist
                </button>
                <button onclick="filterByRole('Technician')" id="tab-Technician" class="role-tab px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 border-b-2 border-transparent hover:border-gray-300 transition whitespace-nowrap">
                    Technician
                </button>
            </div>
            
            <!-- Search Bar -->
            <div class="relative w-full sm:w-[400px]">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" placeholder="Search staff by name or email" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-base">
            </div>
        </div>

        <!-- Staff Grid View - 4 columns -->
        <div id="gridView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- Staff Card 1 -->
            <div data-role="Admin" onclick="window.location.href='view.php?id=1'" class="staff-card bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow cursor-pointer active:scale-95">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-16 h-16 bg-[#e6f0f3] rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-2xl font-bold text-[#003047]">AU</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 text-lg truncate">Admin User</h3>
                        <p class="text-sm text-gray-500 truncate">admin@salon.com</p>
                        <p class="text-xs text-[#003047] font-medium mt-1">Admin</p>
                    </div>
                </div>
                
                <!-- Stats -->
                <div class="grid grid-cols-2 gap-3 mb-4 pt-4 border-t border-gray-200">
                    <div>
                        <p class="text-xs text-gray-500">Status</p>
                        <p class="text-sm font-medium text-green-600">Active</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Last Login</p>
                        <p class="text-sm font-medium text-gray-900">Today</p>
                    </div>
                </div>
            </div>

            <!-- Staff Card 2 -->
            <div data-role="Receptionist" onclick="window.location.href='view.php?id=2'" class="staff-card bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow cursor-pointer active:scale-95">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-2xl font-bold text-purple-600">JS</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 text-lg truncate">John Smith</h3>
                        <p class="text-sm text-gray-500 truncate">john.s@salon.com</p>
                        <p class="text-xs text-blue-600 font-medium mt-1">Receptionist</p>
                    </div>
                </div>
                
                <!-- Stats -->
                <div class="grid grid-cols-2 gap-3 mb-4 pt-4 border-t border-gray-200">
                    <div>
                        <p class="text-xs text-gray-500">Status</p>
                        <p class="text-sm font-medium text-green-600">Active</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Last Login</p>
                        <p class="text-sm font-medium text-gray-900">2 days ago</p>
                    </div>
                </div>
            </div>

            <!-- Staff Card 3 -->
            <div data-role="Receptionist" onclick="window.location.href='view.php?id=3'" class="staff-card bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow cursor-pointer active:scale-95">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-16 h-16 bg-teal-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-2xl font-bold text-teal-600">EW</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 text-lg truncate">Emily Wilson</h3>
                        <p class="text-sm text-gray-500 truncate">emily.w@salon.com</p>
                        <p class="text-xs text-green-600 font-medium mt-1">Receptionist</p>
                    </div>
                </div>
                
                <!-- Stats -->
                <div class="grid grid-cols-2 gap-3 mb-4 pt-4 border-t border-gray-200">
                    <div>
                        <p class="text-xs text-gray-500">Status</p>
                        <p class="text-sm font-medium text-green-600">Active</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Last Login</p>
                        <p class="text-sm font-medium text-gray-900">1 week ago</p>
                    </div>
                </div>
            </div>

            <!-- Staff Card 4 -->
            <div data-role="Technician" onclick="window.location.href='view.php?id=4'" class="staff-card bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow cursor-pointer active:scale-95">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-2xl font-bold text-indigo-600">RB</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 text-lg truncate">Robert Brown</h3>
                        <p class="text-sm text-gray-500 truncate">robert.b@salon.com</p>
                        <p class="text-xs text-purple-600 font-medium mt-1">Technician</p>
                    </div>
                </div>
                
                <!-- Stats -->
                <div class="grid grid-cols-2 gap-3 mb-4 pt-4 border-t border-gray-200">
                    <div>
                        <p class="text-xs text-gray-500">Status</p>
                        <p class="text-sm font-medium text-gray-500">Inactive</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Last Login</p>
                        <p class="text-sm font-medium text-gray-900">1 month ago</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Staff List View -->
        <div id="listView" class="hidden">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Login</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr data-role="Admin" onclick="window.location.href='view.php?id=1'" class="staff-row hover:bg-gray-50 cursor-pointer transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-[#e6f0f3] rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-sm font-bold text-[#003047]">AU</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">Admin User</div>
                                        <div class="text-sm text-gray-500">admin@salon.com</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-xs text-[#003047] font-medium px-2 py-1 bg-[#e6f0f3] rounded">Admin</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-green-600">Active</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Today</td>
                        </tr>
                        <tr data-role="Receptionist" onclick="window.location.href='view.php?id=2'" class="staff-row hover:bg-gray-50 cursor-pointer transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-sm font-bold text-purple-600">JS</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">John Smith</div>
                                        <div class="text-sm text-gray-500">john.s@salon.com</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-xs text-blue-600 font-medium px-2 py-1 bg-blue-50 rounded">Receptionist</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-green-600">Active</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">2 days ago</td>
                        </tr>
                        <tr data-role="Receptionist" onclick="window.location.href='view.php?id=3'" class="staff-row hover:bg-gray-50 cursor-pointer transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-teal-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-sm font-bold text-teal-600">EW</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">Emily Wilson</div>
                                        <div class="text-sm text-gray-500">emily.w@salon.com</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-xs text-green-600 font-medium px-2 py-1 bg-green-50 rounded">Receptionist</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-green-600">Active</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">1 week ago</td>
                        </tr>
                        <tr data-role="Technician" onclick="window.location.href='view.php?id=4'" class="staff-row hover:bg-gray-50 cursor-pointer transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-sm font-bold text-indigo-600">RB</span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">Robert Brown</div>
                                        <div class="text-sm text-gray-500">robert.b@salon.com</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-xs text-purple-600 font-medium px-2 py-1 bg-purple-50 rounded">Technician</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-medium text-gray-500">Inactive</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">1 month ago</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script>
// View toggle functionality
let currentView = localStorage.getItem('staffView') || 'grid';

function toggleView(view) {
    currentView = view;
    localStorage.setItem('staffView', view);
    
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
    initializeRoleFilter();
});

function initializeRoleFilter() {
    // Get role from URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const role = urlParams.get('role');
    
    if (role) {
        // Convert URL parameter to proper case (technician -> Technician)
        const roleCapitalized = role.charAt(0).toUpperCase() + role.slice(1).toLowerCase();
        filterByRole(roleCapitalized, false); // false = don't update URL (already in URL)
    } else {
        filterByRole('all', false);
    }
}

function filterByRole(role, updateURL = true) {
    // Update URL
    if (updateURL) {
        const url = new URL(window.location);
        if (role === 'all') {
            url.searchParams.delete('role');
        } else {
            url.searchParams.set('role', role.toLowerCase());
        }
        window.history.pushState({}, '', url);
    }
    
    // Update active tab
    document.querySelectorAll('.role-tab').forEach(tab => {
        tab.classList.remove('text-[#003047]', 'border-[#003047]', 'border-b-2');
        tab.classList.add('text-gray-500', 'border-transparent');
    });
    
    const activeTab = document.getElementById('tab-' + role);
    if (activeTab) {
        activeTab.classList.remove('text-gray-500', 'border-transparent');
        activeTab.classList.add('text-[#003047]', 'border-[#003047]', 'border-b-2');
    }
    
    // Filter staff cards (grid view)
    const staffCards = document.querySelectorAll('.staff-card');
    staffCards.forEach(card => {
        if (role === 'all' || card.getAttribute('data-role') === role) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
    
    // Filter staff rows (list view)
    const staffRows = document.querySelectorAll('.staff-row');
    staffRows.forEach(row => {
        if (role === 'all' || row.getAttribute('data-role') === role) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function openNewUserModal() {
    const modalContent = `
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">New Staff</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form onsubmit="saveUser(event)" class="space-y-4">
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
                        <input type="email" name="email" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" name="phone" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                    <select name="role" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                        <option value="">Select Role</option>
                        <option value="Admin">Admin</option>
                        <option value="Receptionist">Receptionist</option>
                        <option value="Technician">Technician</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                </div>
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <label class="text-sm font-medium text-gray-900">Active</label>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="active" class="sr-only peer" checked>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div>
                    </label>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">
                        Save Staff
                    </button>
                </div>
            </form>
        </div>
    `;
    openModal(modalContent);
}

function saveUser(event) {
    event.preventDefault();
    showSuccessMessage('Staff added successfully!');
    closeModal();
    setTimeout(() => location.reload(), 1500);
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


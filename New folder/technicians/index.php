<?php
$pageTitle = 'Technicians';
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/modal.php';

// Get current role (default to 'admin')
$current_role = isset($_SESSION['selected_role']) ? $_SESSION['selected_role'] : 'admin';
$is_admin = ($current_role === 'admin');
?>

<!-- Main Content Area -->
<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Technicians</h1>
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
                <button onclick="openAddTechnicianModal()" class="px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm sm:text-base active:scale-95">
                    + Add Technician
                </button>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="flex flex-col sm:flex-row gap-4 mb-6">
            <div class="relative flex-1 max-w-md">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <input type="text" placeholder="Search technicians..." class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-base">
            </div>
        </div>

        <!-- Technicians Grid View - Tablet/Touch Optimized -->
        <div id="gridView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- Technician Card 1 -->
            <div onclick="event.stopPropagation(); window.location.href='view.php?id=1'" class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow cursor-pointer active:scale-95">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-16 h-16 bg-[#e6f0f3] rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-2xl font-bold text-[#003047]">MG</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 text-lg truncate">Maria Garcia</h3>
                        <p class="text-sm text-gray-500">Senior Technician</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded">Available</span>
                        </div>
                    </div>
                </div>
                
                <?php if ($is_admin): ?>
                <!-- Stats -->
                <div class="grid grid-cols-3 gap-3 mb-4 pt-4 border-t border-gray-200">
                    <div>
                        <p class="text-xs text-gray-500">Total</p>
                        <p class="text-lg font-bold text-gray-900">$280</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Tip</p>
                        <p class="text-lg font-bold text-gray-900">$45</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Commission</p>
                        <p class="text-lg font-bold text-gray-900">$84</p>
                    </div>
                </div>
                <?php endif; ?>

            </div>

            <!-- Technician Card 2 -->
            <div onclick="event.stopPropagation(); window.location.href='view.php?id=2'" class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow cursor-pointer active:scale-95">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-2xl font-bold text-purple-600">LW</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 text-lg truncate">Lisa Wong</h3>
                        <p class="text-sm text-gray-500">Technician</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="px-2 py-1 bg-[#e6f0f3] text-[#003047] text-xs font-medium rounded">Busy</span>
                        </div>
                    </div>
                </div>
                
                <?php if ($is_admin): ?>
                <!-- Stats -->
                <div class="grid grid-cols-3 gap-3 mb-4 pt-4 border-t border-gray-200">
                    <div>
                        <p class="text-xs text-gray-500">Total</p>
                        <p class="text-lg font-bold text-gray-900">$210</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Tip</p>
                        <p class="text-lg font-bold text-gray-900">$35</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Commission</p>
                        <p class="text-lg font-bold text-gray-900">$63</p>
                    </div>
                </div>
                <?php endif; ?>

            </div>

            <!-- Technician Card 3 -->
            <div onclick="event.stopPropagation(); window.location.href='view.php?id=3'" class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow cursor-pointer active:scale-95">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-16 h-16 bg-teal-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-2xl font-bold text-teal-600">AK</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 text-lg truncate">Anna Kim</h3>
                        <p class="text-sm text-gray-500">Technician</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded">Available</span>
                        </div>
                    </div>
                </div>
                
                <?php if ($is_admin): ?>
                <!-- Stats -->
                <div class="grid grid-cols-3 gap-3 mb-4 pt-4 border-t border-gray-200">
                    <div>
                        <p class="text-xs text-gray-500">Total</p>
                        <p class="text-lg font-bold text-gray-900">$175</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Tip</p>
                        <p class="text-lg font-bold text-gray-900">$28</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Commission</p>
                        <p class="text-lg font-bold text-gray-900">$52.50</p>
                    </div>
                </div>
                <?php endif; ?>

            </div>

            <!-- Technician Card 4 -->
            <div onclick="event.stopPropagation(); window.location.href='view.php?id=4'" class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow cursor-pointer active:scale-95">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-2xl font-bold text-indigo-600">SL</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-semibold text-gray-900 text-lg truncate">Sarah Lee</h3>
                        <p class="text-sm text-gray-500">Junior Technician</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded">Available</span>
                        </div>
                    </div>
                </div>
                
                <?php if ($is_admin): ?>
                <!-- Stats -->
                <div class="grid grid-cols-3 gap-3 mb-4 pt-4 border-t border-gray-200">
                    <div>
                        <p class="text-xs text-gray-500">Total</p>
                        <p class="text-lg font-bold text-gray-900">$140</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Tip</p>
                        <p class="text-lg font-bold text-gray-900">$22</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Commission</p>
                        <p class="text-lg font-bold text-gray-900">$42</p>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </div>

        <!-- Technicians List View -->
        <div id="listView" class="hidden">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Technician</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <?php if ($is_admin): ?>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tip</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Commission</th>
                            <?php endif; ?>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Clock</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr onclick="event.stopPropagation(); window.location.href='view.php?id=1'" class="hover:bg-gray-50 cursor-pointer transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-[#e6f0f3] rounded-full flex items-center justify-center flex-shrink-0 mr-3">
                                        <span class="text-sm font-bold text-[#003047]">MG</span>
                                    </div>
                                    <div class="text-sm font-medium text-gray-900">Maria Garcia</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded">Available</span>
                            </td>
                            <?php if ($is_admin): ?>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">$280</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">$45</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">$84</div>
                            </td>
                            <?php endif; ?>
                            <td class="px-6 py-4 whitespace-nowrap" onclick="event.stopPropagation()">
                                <div class="space-y-1" id="clockInfo-1">
                                    <div class="text-xs text-gray-900 font-medium" id="clockTimeIn-1">In: Nov. 23, 2025 8:00 AM</div>
                                    <div class="text-xs text-gray-900 font-medium" id="clockTimeOut-1">Out: --</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap" onclick="event.stopPropagation()">
                                <button id="loginToggle-1" onclick="toggleTechnicianLogin(1)" class="px-3 py-1.5 bg-green-500 text-white text-xs font-medium rounded hover:bg-green-600 transition active:scale-95">
                                    Clock In
                                </button>
                            </td>
                        </tr>
                        <tr onclick="event.stopPropagation(); window.location.href='view.php?id=2'" class="hover:bg-gray-50 cursor-pointer transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0 mr-3">
                                        <span class="text-sm font-bold text-purple-600">LW</span>
                                    </div>
                                    <div class="text-sm font-medium text-gray-900">Lisa Wong</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 bg-[#e6f0f3] text-[#003047] text-xs font-medium rounded">Busy</span>
                            </td>
                            <?php if ($is_admin): ?>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">$210</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">$35</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">$63</div>
                            </td>
                            <?php endif; ?>
                            <td class="px-6 py-4 whitespace-nowrap" onclick="event.stopPropagation()">
                                <div class="space-y-1" id="clockInfo-2">
                                    <div class="text-xs text-gray-900 font-medium" id="clockTimeIn-2">In: Nov. 22, 2025 7:30 AM</div>
                                    <div class="text-xs text-gray-900 font-medium" id="clockTimeOut-2">Out: --</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap" onclick="event.stopPropagation()">
                                <button id="loginToggle-2" onclick="toggleTechnicianLogin(2)" class="px-3 py-1.5 bg-green-500 text-white text-xs font-medium rounded hover:bg-green-600 transition active:scale-95">
                                    Clock In
                                </button>
                            </td>
                        </tr>
                        <tr onclick="event.stopPropagation(); window.location.href='view.php?id=3'" class="hover:bg-gray-50 cursor-pointer transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-teal-100 rounded-full flex items-center justify-center flex-shrink-0 mr-3">
                                        <span class="text-sm font-bold text-teal-600">AK</span>
                                    </div>
                                    <div class="text-sm font-medium text-gray-900">Anna Kim</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded">Available</span>
                            </td>
                            <?php if ($is_admin): ?>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">$175</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">$28</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">$52.50</div>
                            </td>
                            <?php endif; ?>
                            <td class="px-6 py-4 whitespace-nowrap" onclick="event.stopPropagation()">
                                <div class="space-y-1" id="clockInfo-3">
                                    <div class="text-xs text-gray-900 font-medium" id="clockTimeIn-3">In: Nov. 24, 2025 9:15 AM</div>
                                    <div class="text-xs text-gray-900 font-medium" id="clockTimeOut-3">Out: --</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap" onclick="event.stopPropagation()">
                                <button id="loginToggle-3" onclick="toggleTechnicianLogin(3)" class="px-3 py-1.5 bg-green-500 text-white text-xs font-medium rounded hover:bg-green-600 transition active:scale-95">
                                    Clock In
                                </button>
                            </td>
                        </tr>
                        <tr onclick="event.stopPropagation(); window.location.href='view.php?id=4'" class="hover:bg-gray-50 cursor-pointer transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0 mr-3">
                                        <span class="text-sm font-bold text-indigo-600">SL</span>
                                    </div>
                                    <div class="text-sm font-medium text-gray-900">Sarah Lee</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded">Available</span>
                            </td>
                            <?php if ($is_admin): ?>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">$140</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">$22</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-gray-900">$42</div>
                            </td>
                            <?php endif; ?>
                            <td class="px-6 py-4 whitespace-nowrap" onclick="event.stopPropagation()">
                                <div class="space-y-1" id="clockInfo-4">
                                    <div class="text-xs text-gray-900 font-medium" id="clockTimeIn-4">In: Nov. 21, 2025 8:45 AM</div>
                                    <div class="text-xs text-gray-900 font-medium" id="clockTimeOut-4">Out: --</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap" onclick="event.stopPropagation()">
                                <button id="loginToggle-4" onclick="toggleTechnicianLogin(4)" class="px-3 py-1.5 bg-green-500 text-white text-xs font-medium rounded hover:bg-green-600 transition active:scale-95">
                                    Clock In
                                </button>
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
let currentView = localStorage.getItem('techniciansView') || 'grid';

function toggleView(view) {
    currentView = view;
    localStorage.setItem('techniciansView', view);
    
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
    initializeTechnicianLoginStates();
});

// Technician data for login/logout modal
const technicianData = {
    1: {
        name: 'Maria Garcia',
        email: 'maria.garcia@salon.com',
        role: 'Senior Technician',
        initials: 'MG'
    },
    2: {
        name: 'Lisa Wong',
        email: 'lisa.wong@salon.com',
        role: 'Technician',
        initials: 'LW'
    },
    3: {
        name: 'Anna Kim',
        email: 'anna.kim@salon.com',
        role: 'Technician',
        initials: 'AK'
    },
    4: {
        name: 'Sarah Lee',
        email: 'sarah.lee@salon.com',
        role: 'Junior Technician',
        initials: 'SL'
    }
};

function initializeTechnicianLoginStates() {
    // Initialize login state from localStorage for each technician
    [1, 2, 3, 4].forEach(technicianId => {
        const isLoggedIn = localStorage.getItem('technician_' + technicianId + '_loggedIn') === 'true';
        const button = document.getElementById('loginToggle-' + technicianId);
        if (button) {
            if (isLoggedIn) {
                button.textContent = 'Clock Out';
                button.classList.remove('bg-green-500', 'hover:bg-green-600');
                button.classList.add('bg-red-500', 'hover:bg-red-600');
            } else {
                button.textContent = 'Clock In';
                button.classList.remove('bg-red-500', 'hover:bg-red-600');
                button.classList.add('bg-green-500', 'hover:bg-green-600');
            }
        }
    });
}

function toggleTechnicianLogin(technicianId) {
    const button = document.getElementById('loginToggle-' + technicianId);
    const isLoggedIn = button.textContent.trim() === 'Clock Out';
    const action = isLoggedIn ? 'Clock Out' : 'Clock In';
    const tech = technicianData[technicianId];
    
    if (!tech) {
        console.error('Technician data not found for ID:', technicianId);
        return;
    }
    
    // Show modal with details
    showLoginLogoutModal(tech, action, technicianId, isLoggedIn);
}

function showLoginLogoutModal(technician, action, technicianId, isCurrentlyLoggedIn) {
    const now = new Date();
    const dateTime = now.toLocaleString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: true
    });
    
    const actionColor = action === 'Clock In' ? 'green' : 'red';
    const actionBgColor = action === 'Clock In' ? 'bg-green-100' : 'bg-red-100';
    const actionTextColor = action === 'Clock In' ? 'text-green-700' : 'text-red-700';
    
    const modalContent = `
        <div class="p-6 max-w-2xl">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Technician ${action}</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="space-y-6">
                <!-- Technician Details -->
                <div class="bg-gray-50 rounded-lg p-5">
                    <h4 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">Technician Details</h4>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Name</p>
                            <p class="text-sm font-medium text-gray-900">${technician.name}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Email</p>
                            <p class="text-sm font-medium text-gray-900">${technician.email}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Role</p>
                            <p class="text-sm font-medium text-gray-900">${technician.role}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">ID</p>
                            <p class="text-sm font-medium text-gray-900">#${technicianId}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Action -->
                <div class="bg-gray-50 rounded-lg p-5">
                    <h4 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">Action</h4>
                    <div class="flex items-center gap-3">
                        <span class="px-4 py-2 ${actionBgColor} ${actionTextColor} text-sm font-semibold rounded-lg">
                            ${action}
                        </span>
                        <p class="text-sm text-gray-600">
                            ${action === 'Clock In' ? 'Technician is clocking into the system' : 'Technician is clocking out of the system'}
                        </p>
                    </div>
                </div>
                
                <!-- Date & Time -->
                <div class="bg-gray-50 rounded-lg p-5">
                    <h4 class="text-sm font-semibold text-gray-700 mb-4 uppercase tracking-wide">Date & Time</h4>
                    <p class="text-sm font-medium text-gray-900">${dateTime}</p>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-gray-200">
                <button onclick="closeModal()" class="px-6 py-2.5 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">
                    Cancel
                </button>
                <button onclick="confirmLoginLogout(${technicianId}, '${action}')" class="px-6 py-2.5 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">
                    Confirm ${action}
                </button>
            </div>
        </div>
    `;
    openModal(modalContent);
}

function confirmLoginLogout(technicianId, action) {
    const button = document.getElementById('loginToggle-' + technicianId);
    const isClockIn = action === 'Clock In';
    
    if (isClockIn) {
        // Clock In
        button.textContent = 'Clock Out';
        button.classList.remove('bg-green-500', 'hover:bg-green-600');
        button.classList.add('bg-red-500', 'hover:bg-red-600');
        localStorage.setItem('technician_' + technicianId + '_loggedIn', 'true');
    } else {
        // Clock Out
        button.textContent = 'Clock In';
        button.classList.remove('bg-red-500', 'hover:bg-red-600');
        button.classList.add('bg-green-500', 'hover:bg-green-600');
        localStorage.setItem('technician_' + technicianId + '_loggedIn', 'false');
    }
    
    closeModal();
    showSuccessMessage(`Technician ${action.toLowerCase()} successful!`);
    
    // In a real application, you would send this data to the server
    console.log(`${action} technician:`, technicianId);
}

function openAddTechnicianModal() {
    const modalContent = `
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">Add New Technician</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form onsubmit="saveTechnician(event)" class="space-y-4">
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
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <input type="tel" name="phone" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">
                        Save Technician
                    </button>
                </div>
            </form>
        </div>
    `;
    openModal(modalContent);
}

function saveTechnician(event) {
    event.preventDefault();
    showSuccessMessage('Technician added successfully!');
    closeModal();
    setTimeout(() => location.reload(), 1500);
}


function openAssignTechFromList(button) {
    const card = button.closest('.bg-white');
    const technicianName = card.querySelector('h3').textContent;
    const modalContent = `
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">Assign ${technicianName}</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <p class="text-sm text-gray-600 mb-4">Select a service from the queue to assign this technician.</p>
            <div class="space-y-2 border border-gray-300 rounded-lg p-4 max-h-60 overflow-y-auto">
                <button onclick="assignToService('ORDER925', 'Sarah Johnson', '${technicianName}')" class="w-full text-left p-3 hover:bg-gray-50 rounded border border-gray-200">
                    <p class="font-medium text-gray-900">Order #925</p>
                    <p class="text-xs text-gray-500">Sarah Johnson - Classic Manicure</p>
                </button>
                <button onclick="assignToService('ORDER926', 'Emily Chen', '${technicianName}')" class="w-full text-left p-3 hover:bg-gray-50 rounded border border-gray-200">
                    <p class="font-medium text-gray-900">Order #926</p>
                    <p class="text-xs text-gray-500">Emily Chen - Acrylic Full Set</p>
                </button>
            </div>
        </div>
    `;
    openModal(modalContent);
}

function assignToService(orderId, customerName, technicianName) {
    showSuccessMessage(`${technicianName} assigned to ${customerName}'s order!`);
    closeModal();
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

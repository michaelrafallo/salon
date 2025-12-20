<?php
$pageTitle = 'Services';
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/modal.php';
?>

<!-- Main Content Area -->
<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Services</h1>
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
                <button onclick="openAddServiceModal()" class="px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm sm:text-base active:scale-95">
                    + Add Service
                </button>
            </div>
        </div>

        <!-- Services Grid View - Tablet/Touch Optimized -->
        <div id="gridView" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
            <!-- Services will be loaded dynamically from API -->
        </div>

        <!-- Services List View -->
        <div id="listView" class="hidden">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Service</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody id="listViewBody" class="bg-white divide-y divide-gray-200">
                        <!-- Services will be loaded dynamically from API -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script>
// Services data
let servicesData = [];
let categoriesMap = {};

// Fetch categories mapping
async function fetchCategories() {
    try {
        const response = await fetch('../json/service-categories.json');
        const data = await response.json();
        categoriesMap = data.categories;
    } catch (error) {
        console.error('Error fetching categories:', error);
    }
}

// Fetch services from API
async function fetchServices() {
    try {
        await fetchCategories(); // Load categories first
        const response = await fetch('../json/services.json');
        const data = await response.json();
        servicesData = data.services.filter(service => service.active);
        renderServices();
    } catch (error) {
        console.error('Error fetching services:', error);
        showErrorMessage('Failed to load services. Please try again later.');
    }
}

// Helper function to get category display name from key
function getCategoryDisplayName(key) {
    return categoriesMap[key] || key;
}

// Helper function to get category key from display name
function getCategoryKey(displayName) {
    for (const [key, value] of Object.entries(categoriesMap)) {
        if (value === displayName) {
            return key;
        }
    }
    return displayName.toLowerCase().replace(/\s+/g, '-');
}

// Render services in grid view
function renderGridView() {
    const gridView = document.getElementById('gridView');
    if (!gridView) return;
    
    gridView.innerHTML = '';
    
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
    
    servicesData.forEach((service, index) => {
        const color = colorClasses[index % colorClasses.length];
        const card = document.createElement('div');
        card.className = 'bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow cursor-pointer active:scale-95';
        card.onclick = () => openServiceModal(service.name, service.description, service.price, service.active, service.categories);
        
        card.innerHTML = `
            <div class="flex items-start mb-3">
                <div class="w-12 h-12 ${color.bg} rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 ${color.text}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
            </div>
            <h3 class="font-semibold text-gray-900 text-lg mb-2">${service.name}</h3>
            <p class="text-sm text-gray-600 mb-4">${service.description}</p>
            <div class="pt-4 border-t border-gray-200">
                <span class="text-2xl font-bold text-gray-900">$${service.price.toFixed(2)}</span>
            </div>
        `;
        
        gridView.appendChild(card);
    });
}

// Render services in list view
function renderListView() {
    const listViewBody = document.getElementById('listViewBody');
    if (!listViewBody) return;
    
    listViewBody.innerHTML = '';
    
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
    
    servicesData.forEach((service, index) => {
        const color = colorClasses[index % colorClasses.length];
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50 cursor-pointer transition';
        row.onclick = () => openServiceModal(service.name, service.description, service.price, service.active, service.categories);
        
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="w-10 h-10 ${color.bg} rounded-lg flex items-center justify-center flex-shrink-0 mr-3">
                        <svg class="w-6 h-6 ${color.text}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                    </div>
                    <div class="text-sm font-medium text-gray-900">${service.name}</div>
                </div>
            </td>
            <td class="px-6 py-4">
                <div class="text-sm text-gray-900">${service.description}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-bold text-gray-900">$${service.price.toFixed(2)}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="text-sm font-medium text-green-600">Active</span>
            </td>
        `;
        
        listViewBody.appendChild(row);
    });
}

// Render services based on current view
function renderServices() {
    const currentView = localStorage.getItem('servicesView') || 'grid';
    if (currentView === 'grid') {
        renderGridView();
    } else {
        renderListView();
    }
}

// View toggle functionality
let currentView = localStorage.getItem('servicesView') || 'grid';

function toggleView(view) {
    currentView = view;
    localStorage.setItem('servicesView', view);
    
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
document.addEventListener('DOMContentLoaded', function() {
    fetchServices();
    toggleView(currentView);
});

function showErrorMessage(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    errorDiv.textContent = message;
    document.body.appendChild(errorDiv);
    setTimeout(() => errorDiv.remove(), 5000);
}

function openAddServiceModal() {
    // Generate category checkboxes dynamically from categoriesMap
    let categoryCheckboxes = '';
    if (Object.keys(categoriesMap).length > 0) {
        // Sort categories by display name
        const sortedCategories = Object.entries(categoriesMap).sort((a, b) => a[1].localeCompare(b[1]));
        categoryCheckboxes = sortedCategories.map(([key, displayName]) => `
            <label class="flex items-center p-2 bg-white rounded-lg border border-gray-200 hover:border-[#003047] hover:bg-[#e6f0f3] cursor-pointer transition-all duration-200 group has-[:checked]:border-[#003047] has-[:checked]:bg-[#e6f0f3]">
                <input type="checkbox" name="category[]" value="${key}" class="w-4 h-4 text-[#003047] border-gray-300 rounded focus:ring-[#003047] focus:ring-2 cursor-pointer" style="accent-color: #003047;">
                <span class="ml-2 text-sm font-medium text-gray-700 group-hover:text-[#003047] has-[:checked]:text-[#003047]">${displayName}</span>
            </label>
        `).join('');
    }
    
    const modalContent = `
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">Add New Service</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form onsubmit="saveService(event)" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Service Name</label>
                    <input type="text" name="name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Classic Manicure">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <div class="border border-gray-300 rounded-lg p-2 bg-gray-50">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            ${categoryCheckboxes}
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">Select one or more categories for this service</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Service description"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Price ($)</label>
                    <input type="number" name="price" required step="0.01" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="35.00">
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
                        Save Service
                    </button>
                </div>
            </form>
        </div>
    `;
    openModal(modalContent);
}

function openServiceModal(serviceName, description, price, active, categories = []) {
    // Helper function to check if category is selected (by key)
    const isCategoryChecked = (categoryKey) => {
        return categories && categories.includes(categoryKey) ? 'checked' : '';
    };
    
    // Generate category checkboxes dynamically from categoriesMap
    let categoryCheckboxes = '';
    if (Object.keys(categoriesMap).length > 0) {
        // Sort categories by display name
        const sortedCategories = Object.entries(categoriesMap).sort((a, b) => a[1].localeCompare(b[1]));
        categoryCheckboxes = sortedCategories.map(([key, displayName]) => `
            <label class="flex items-center p-2 bg-white rounded-lg border border-gray-200 hover:border-[#003047] hover:bg-[#e6f0f3] cursor-pointer transition-all duration-200 group has-[:checked]:border-[#003047] has-[:checked]:bg-[#e6f0f3]">
                <input type="checkbox" name="category[]" value="${key}" ${isCategoryChecked(key)} class="w-4 h-4 text-[#003047] border-gray-300 rounded focus:ring-[#003047] focus:ring-2 cursor-pointer" style="accent-color: #003047;">
                <span class="ml-2 text-sm font-medium text-gray-700 group-hover:text-[#003047] has-[:checked]:text-[#003047]">${displayName}</span>
            </label>
        `).join('');
    }
    
    const modalContent = `
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">Edit Service</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form onsubmit="saveService(event)" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Service Name</label>
                    <input type="text" name="name" required value="${serviceName}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Classic Manicure">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <div class="border border-gray-300 rounded-lg p-2 bg-gray-50">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            ${categoryCheckboxes}
                        </div>
                    </div>
                    <p class="mt-2 text-xs text-gray-500">Select one or more categories for this service</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Service description">${description}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Price ($)</label>
                    <input type="number" name="price" required step="0.01" value="${price}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="35.00">
                </div>
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <label class="text-sm font-medium text-gray-900">Active</label>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="active" class="sr-only peer" ${active ? 'checked' : ''}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div>
                    </label>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">
                        Save Service
                    </button>
                </div>
            </form>
        </div>
    `;
    openModal(modalContent);
}

function saveService(event) {
    event.preventDefault();
    showSuccessMessage('Service saved successfully!');
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

// Add event listeners for checkbox styling
document.addEventListener('DOMContentLoaded', function() {
    // This will be called when modals are opened
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length) {
                const checkboxes = document.querySelectorAll('input[name="category[]"]');
                checkboxes.forEach(function(checkbox) {
                    checkbox.addEventListener('change', function() {
                        const label = this.closest('label');
                        if (this.checked) {
                            label.classList.add('border-[#003047]', 'bg-[#e6f0f3]');
                            label.querySelector('span').classList.add('text-[#003047]');
                        } else {
                            label.classList.remove('border-[#003047]', 'bg-[#e6f0f3]');
                            label.querySelector('span').classList.remove('text-[#003047]');
                        }
                    });
                });
            }
        });
    });
    
    const modalContent = document.getElementById('modalContent');
    if (modalContent) {
        observer.observe(modalContent, { childList: true, subtree: true });
    }
});
</script>

<style>
/* Modern Category Selection Styles */
input[name="category[]"] {
    accent-color: #003047 !important;
}

input[name="category[]"]:checked {
    background-color: #003047 !important;
    border-color: #003047 !important;
    accent-color: #003047 !important;
}

input[name="category[]"]:checked + span {
    color: #003047 !important;
    font-weight: 600;
}

label:has(input[name="category[]"]:checked) {
    border-color: #003047 !important;
    background-color: #e6f0f3 !important;
}

label:has(input[name="category[]"]:checked) span {
    color: #003047 !important;
}
</style>

<?php include '../includes/footer.php'; ?>

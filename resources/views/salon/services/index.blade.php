@extends('layouts.salon')

@section('content')
@php
    $isAdmin = in_array(session('salon_role', 'admin'), ['admin'], true);
@endphp
<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Services</h1>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-1">
                    <button id="gridViewBtn" onclick="toggleView('grid')" class="p-2 rounded-md hover:bg-white transition active:scale-95">
                        <svg class="w-5 h-5 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    </button>
                    <button id="listViewBtn" onclick="toggleView('list')" class="p-2 rounded-md hover:bg-white transition active:scale-95">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    </button>
                </div>
                <button onclick="openAddServiceModal()" class="px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm sm:text-base active:scale-95">+ Add Service</button>
            </div>
        </div>
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex-shrink-0">
                <select id="categoryFilter" onchange="filterByCategory(this.value)" class="w-full sm:w-auto px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-base bg-white cursor-pointer">
                    <option value="">All Categories</option>
                </select>
            </div>
            <div class="relative w-full sm:w-[400px]">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                <input type="text" id="serviceSearchInput" placeholder="Search services by name or description..." oninput="searchServices(this.value)" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-base">
            </div>
        </div>
        <div id="gridView" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6"></div>
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
                    <tbody id="listViewBody" class="bg-white divide-y divide-gray-200"></tbody>
                </table>
            </div>
        </div>
        <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div id="resultsCounter" class="text-sm text-gray-600"></div>
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-600">Show:</label>
                <select id="perPageSelect" onchange="changePerPage(this.value)" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-sm bg-white cursor-pointer">
                    <option value="15">15</option><option value="25">25</option><option value="50">50</option><option value="100">100</option><option value="250">250</option><option value="500">500</option><option value="all">All</option>
                </select>
                <span class="text-sm text-gray-600">per page</span>
            </div>
        </div>
        <div id="servicesPagination" class="mt-4 flex justify-center"></div>
    </div>
</main>
@push('scripts')
<script>
var base = window.salonJsonBase || '{{ url("json") }}';
var isAdmin = {{ $isAdmin ? 'true' : 'false' }};
var SERVICE_PASSWORD = '54321';
var allServices = [], servicesData = [], categoriesMap = {}, currentCategoryFilter = '', currentSearchTerm = '';
var PAGE_SIZE = 15, currentPage = 1, totalPages = 1, currentView = localStorage.getItem('servicesView') || 'grid';
var pendingAction = null;
var colorClasses = [
    { bg: 'bg-[#e6f0f3]', text: 'text-[#003047]' }, { bg: 'bg-purple-100', text: 'text-purple-600' },
    { bg: 'bg-teal-100', text: 'text-teal-600' }, { bg: 'bg-indigo-100', text: 'text-indigo-600' },
    { bg: 'bg-rose-100', text: 'text-rose-600' }, { bg: 'bg-blue-100', text: 'text-blue-600' },
    { bg: 'bg-amber-100', text: 'text-amber-600' }, { bg: 'bg-green-100', text: 'text-green-600' }
];
function getPaginatedServices() {
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity) return servicesData;
    var start = (currentPage - 1) * PAGE_SIZE;
    return servicesData.slice(start, start + PAGE_SIZE);
}
function updatePaginationState() {
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity) { totalPages = 1; currentPage = 1; return; }
    totalPages = Math.max(1, Math.ceil(servicesData.length / PAGE_SIZE));
    if (currentPage > totalPages) currentPage = totalPages;
    if (currentPage < 1) currentPage = 1;
}
function renderPagination() {
    var el = document.getElementById('servicesPagination');
    if (!el) return;
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity || servicesData.length <= PAGE_SIZE || totalPages <= 1) { el.innerHTML = ''; return; }
    var h = '<div class="flex items-center gap-2 justify-center">';
    var disabledClass = 'text-gray-400 cursor-not-allowed opacity-50';
    var activeClass = 'bg-[#003047] text-white';
    var defaultClass = 'bg-white text-gray-700 border border-gray-300 hover:border-[#003047] hover:text-[#003047]';
    h += '<button class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300 ' + (currentPage === 1 ? disabledClass : 'bg-white text-[#003047] hover:bg-gray-100 hover:border-[#003047]') + '" ' + (currentPage === 1 ? 'disabled' : '') + ' onclick="goToPage(1)" title="First page">&laquo;</button>';
    h += '<button class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300 ' + (currentPage === 1 ? disabledClass : 'bg-white text-[#003047] hover:bg-gray-100 hover:border-[#003047]') + '" ' + (currentPage === 1 ? 'disabled' : '') + ' onclick="changePage(-1)" title="Previous page">&lt;</button>';
    var maxButtons = 6, startPage = Math.max(1, currentPage - Math.floor(maxButtons / 2)), endPage = Math.min(totalPages, startPage + maxButtons - 1);
    if (endPage - startPage < maxButtons - 1) startPage = Math.max(1, endPage - maxButtons + 1);
    for (var p = startPage; p <= endPage; p++) {
        h += '<button class="px-3 py-2 text-sm font-medium rounded-md border ' + (p === currentPage ? activeClass : defaultClass) + '" onclick="goToPage(' + p + ')">' + p + '</button>';
    }
    h += '<button class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300 ' + (currentPage === totalPages ? disabledClass : 'bg-white text-[#003047] hover:bg-gray-100 hover:border-[#003047]') + '" ' + (currentPage === totalPages ? 'disabled' : '') + ' onclick="changePage(1)" title="Next page">&gt;</button>';
    h += '<button class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300 ' + (currentPage === totalPages ? disabledClass : 'bg-white text-[#003047] hover:bg-gray-100 hover:border-[#003047]') + '" ' + (currentPage === totalPages ? 'disabled' : '') + ' onclick="goToPage(' + totalPages + ')" title="Last page">&raquo;</button></div>';
    el.innerHTML = h;
}
function updateResultsCounter() {
    var el = document.getElementById('resultsCounter');
    if (!el) return;
    var total = servicesData.length;
    if (total === 0) { el.textContent = 'No results found'; return; }
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity) { el.textContent = 'Showing all ' + total + ' result' + (total !== 1 ? 's' : ''); return; }
    var start = (currentPage - 1) * PAGE_SIZE, end = Math.min(start + PAGE_SIZE, total);
    el.textContent = 'Showing ' + (start + 1) + '-' + end + ' of ' + total + ' result' + (total !== 1 ? 's' : '');
}
function applyFilters() {
    servicesData = allServices.filter(function(s) {
        var categoryMatch = true;
        if (currentCategoryFilter !== '') {
            categoryMatch = s.categories && s.categories.includes(currentCategoryFilter);
        }
        var searchMatch = true;
        if (currentSearchTerm !== '') {
            var text = ((s.name || '') + ' ' + (s.description || '')).toLowerCase();
            searchMatch = text.includes(currentSearchTerm);
        }
        return categoryMatch && searchMatch;
    });
    currentPage = 1;
    updatePaginationState();
    renderServices();
    updateResultsCounter();
}
function renderGridView() {
    var el = document.getElementById('gridView');
    if (!el) return;
    el.innerHTML = '';
    var list = getPaginatedServices();
    if (list.length === 0) {
        el.innerHTML = '<div class="col-span-full bg-white border border-dashed border-gray-300 rounded-lg p-10 text-center"><p class="text-gray-500 text-sm">No services found.</p></div>';
        return;
    }
    list.forEach(function(s, i) {
        var color = colorClasses[i % colorClasses.length];
        var card = document.createElement('div');
        card.className = 'bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow cursor-pointer active:scale-95';
        card.onclick = function() {
            openServiceModal(s.name, s.description, s.price, s.active, s.categories);
        };
        card.innerHTML = '<div class="flex items-start mb-3"><div class="w-12 h-12 ' + color.bg + ' rounded-lg flex items-center justify-center"><svg class="w-6 h-6 ' + color.text + '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg></div></div><h3 class="font-semibold text-gray-900 text-lg mb-2">' + (s.name || '') + '</h3><p class="text-sm text-gray-600 mb-4">' + (s.description || '') + '</p><div class="pt-4 border-t border-gray-200"><span class="text-2xl font-bold text-gray-900">$' + (parseFloat(s.price || 0).toFixed(2)) + '</span></div>';
        el.appendChild(card);
    });
}
function renderListView() {
    var tbody = document.getElementById('listViewBody');
    if (!tbody) return;
    tbody.innerHTML = '';
    var list = getPaginatedServices();
    if (list.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-12 text-center text-gray-500 text-sm">No services found.</td></tr>';
        return;
    }
    list.forEach(function(s, i) {
        var color = colorClasses[i % colorClasses.length];
        var row = document.createElement('tr');
        row.className = 'hover:bg-gray-50 cursor-pointer transition';
        row.onclick = function() {
            openServiceModal(s.name, s.description, s.price, s.active, s.categories);
        };
        row.innerHTML = '<td class="px-6 py-4 whitespace-nowrap"><div class="flex items-center"><div class="w-10 h-10 ' + color.bg + ' rounded-lg flex items-center justify-center flex-shrink-0 mr-3"><svg class="w-6 h-6 ' + color.text + '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg></div><div class="text-sm font-medium text-gray-900">' + (s.name || '') + '</div></div></td><td class="px-6 py-4"><div class="text-sm text-gray-900">' + (s.description || '') + '</div></td><td class="px-6 py-4 whitespace-nowrap"><div class="text-sm font-bold text-gray-900">$' + (parseFloat(s.price || 0).toFixed(2)) + '</div></td><td class="px-6 py-4 whitespace-nowrap"><span class="text-sm font-medium text-green-600">Active</span></td>';
        tbody.appendChild(row);
    });
}
function renderServices() {
    updatePaginationState();
    if (currentView === 'grid') renderGridView();
    else renderListView();
    renderPagination();
    updateResultsCounter();
}
function goToPage(page) {
    if (page < 1 || page > totalPages || page === currentPage) return;
    currentPage = page;
    renderServices();
    updateResultsCounter();
}
function changePage(offset) {
    goToPage(currentPage + offset);
}
function changePerPage(value) {
    PAGE_SIZE = value === 'all' ? Infinity : parseInt(value);
    currentPage = 1;
    updatePaginationState();
    renderServices();
    updateResultsCounter();
    localStorage.setItem('servicesPerPage', value);
}
function filterByCategory(category) {
    currentCategoryFilter = category || '';
    var url = new URL(window.location);
    if (category === '' || category === null) url.searchParams.delete('category');
    else url.searchParams.set('category', category);
    window.history.pushState({}, '', url);
    var sel = document.getElementById('categoryFilter');
    if (sel) sel.value = category || '';
    applyFilters();
}
function searchServices(searchTerm) {
    currentSearchTerm = (searchTerm || '').toLowerCase();
    applyFilters();
}
function toggleView(view) {
    currentView = view;
    localStorage.setItem('servicesView', view);
    var g = document.getElementById('gridView'), l = document.getElementById('listView'), gb = document.getElementById('gridViewBtn'), lb = document.getElementById('listViewBtn');
    if (view === 'grid') {
        g.classList.remove('hidden'); l.classList.add('hidden');
        if (gb && gb.querySelector('svg')) { gb.querySelector('svg').classList.remove('text-gray-500'); gb.querySelector('svg').classList.add('text-gray-900'); }
        gb.classList.add('bg-white'); gb.classList.remove('hover:bg-white');
        if (lb && lb.querySelector('svg')) { lb.querySelector('svg').classList.remove('text-gray-900'); lb.querySelector('svg').classList.add('text-gray-500'); }
        lb.classList.remove('bg-white'); lb.classList.add('hover:bg-white');
        renderGridView();
    } else {
        g.classList.add('hidden'); l.classList.remove('hidden');
        if (lb && lb.querySelector('svg')) { lb.querySelector('svg').classList.remove('text-gray-500'); lb.querySelector('svg').classList.add('text-gray-900'); }
        lb.classList.add('bg-white'); lb.classList.remove('hover:bg-white');
        if (gb && gb.querySelector('svg')) { gb.querySelector('svg').classList.remove('text-gray-900'); gb.querySelector('svg').classList.add('text-gray-500'); }
        gb.classList.remove('bg-white'); gb.classList.add('hover:bg-white');
        renderListView();
    }
}
function openPasswordModal(action) {
    pendingAction = action;
    var content = '<div class="p-6"><div class="flex items-center justify-between mb-4"><h3 class="text-xl font-bold text-gray-900">Enter Password</h3><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><div class="space-y-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">Password</label><input type="password" id="servicePasswordInput" placeholder="Enter password" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" autofocus><p id="passwordError" class="mt-2 text-sm text-red-600 hidden">Incorrect password. Please try again.</p></div><div class="flex justify-end gap-3 pt-4"><button type="button" onclick="closeModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">Cancel</button><button type="button" onclick="verifyServicePassword()" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Verify</button></div></div></div>';
    openModal(content, 'default', false);
    setTimeout(function() {
        var inp = document.getElementById('servicePasswordInput');
        if (inp) {
            inp.focus();
            inp.addEventListener('keypress', function(e) { if (e.key === 'Enter') verifyServicePassword(); });
        }
    }, 100);
}
function verifyServicePassword() {
    var inp = document.getElementById('servicePasswordInput');
    var err = document.getElementById('passwordError');
    var pwd = inp ? inp.value : '';
    if (pwd === SERVICE_PASSWORD) {
        closeModal();
        setTimeout(function() {
            if (pendingAction && typeof pendingAction === 'function') { pendingAction(); pendingAction = null; }
        }, 200);
    } else {
        if (err) err.classList.remove('hidden');
        if (inp) { inp.value = ''; inp.focus(); }
    }
}
function openAddServiceModal() {
    if (!isAdmin) {
        openPasswordModal(function() { openAddServiceModalContent(); });
        return;
    }
    openAddServiceModalContent();
}
function openAddServiceModalContent() {
    var sorted = Object.entries(categoriesMap).sort(function(a, b) { return (a[1] || '').localeCompare(b[1] || ''); });
    var checkboxes = sorted.map(function(entry) {
        var k = entry[0], v = entry[1];
        return '<label class="flex items-center p-2 bg-white rounded-lg border border-gray-200 hover:border-[#003047] hover:bg-[#e6f0f3] cursor-pointer transition-all duration-200 group has-[:checked]:border-[#003047] has-[:checked]:bg-[#e6f0f3]"><input type="checkbox" name="category[]" value="' + k + '" class="w-4 h-4 text-[#003047] border-gray-300 rounded focus:ring-[#003047] focus:ring-2 cursor-pointer" style="accent-color: #003047;"><span class="ml-2 text-sm font-medium text-gray-700 group-hover:text-[#003047] has-[:checked]:text-[#003047]">' + v + '</span></label>';
    }).join('');
    var content = '<div class="p-6"><div class="flex items-center justify-between mb-4"><h3 class="text-xl font-bold text-gray-900">Add New Service</h3><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><form onsubmit="saveService(event)" class="space-y-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">Service Name</label><input type="text" name="name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Classic Manicure"></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Category</label><div class="border border-gray-300 rounded-lg p-2 bg-gray-50"><div class="grid grid-cols-1 sm:grid-cols-2 gap-2">' + checkboxes + '</div></div><p class="mt-2 text-xs text-gray-500">Select one or more categories for this service</p></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Description</label><textarea name="description" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Service description"></textarea></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Price ($)</label><input type="number" name="price" required step="0.01" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="35.00"></div><div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg"><div><label class="text-sm font-medium text-gray-900">Active</label></div><label class="relative inline-flex items-center cursor-pointer"><input type="checkbox" name="active" class="sr-only peer" checked><div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[\'\'] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div></label></div><div class="flex justify-end gap-3 pt-4"><button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Save Service</button></div></form></div>';
    openModal(content);
}
function openServiceModal(serviceName, description, price, active, categories) {
    if (!isAdmin) {
        openPasswordModal(function() { openServiceModalContent(serviceName, description, price, active, categories); });
        return;
    }
    openServiceModalContent(serviceName, description, price, active, categories);
}
function openServiceModalContent(serviceName, description, price, active, categories) {
    var cats = categories || [];
    var sorted = Object.entries(categoriesMap).sort(function(a, b) { return (a[1] || '').localeCompare(b[1] || ''); });
    var checkboxes = sorted.map(function(entry) {
        var k = entry[0], v = entry[1], checked = cats.indexOf(k) >= 0 ? 'checked' : '';
        return '<label class="flex items-center p-2 bg-white rounded-lg border border-gray-200 hover:border-[#003047] hover:bg-[#e6f0f3] cursor-pointer transition-all duration-200 group has-[:checked]:border-[#003047] has-[:checked]:bg-[#e6f0f3]"><input type="checkbox" name="category[]" value="' + k + '" ' + checked + ' class="w-4 h-4 text-[#003047] border-gray-300 rounded focus:ring-[#003047] focus:ring-2 cursor-pointer" style="accent-color: #003047;"><span class="ml-2 text-sm font-medium text-gray-700 group-hover:text-[#003047] has-[:checked]:text-[#003047]">' + v + '</span></label>';
    }).join('');
    var content = '<div class="p-6"><div class="flex items-center justify-between mb-4"><h3 class="text-xl font-bold text-gray-900">Edit Service</h3><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><form onsubmit="saveService(event)" class="space-y-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">Service Name</label><input type="text" name="name" required value="' + (serviceName || '').replace(/"/g, '&quot;') + '" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Classic Manicure"></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Category</label><div class="border border-gray-300 rounded-lg p-2 bg-gray-50"><div class="grid grid-cols-1 sm:grid-cols-2 gap-2">' + checkboxes + '</div></div><p class="mt-2 text-xs text-gray-500">Select one or more categories for this service</p></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Description</label><textarea name="description" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Service description">' + (description || '').replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</textarea></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Price ($)</label><input type="number" name="price" required step="0.01" value="' + (price || 0) + '" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="35.00"></div><div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg"><div><label class="text-sm font-medium text-gray-900">Active</label></div><label class="relative inline-flex items-center cursor-pointer"><input type="checkbox" name="active" class="sr-only peer" ' + (active ? 'checked' : '') + '><div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[\'\'] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div></label></div><div class="flex justify-end gap-3 pt-4"><button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Save Service</button></div></form></div>';
    openModal(content);
}
function saveService(event) {
    event.preventDefault();
    showSuccessMessage('Service saved successfully!');
    closeModal();
    setTimeout(function() { location.reload(); }, 1500);
}
function populateCategoryDropdown() {
    var sel = document.getElementById('categoryFilter');
    if (!sel) return;
    while (sel.children.length > 1) sel.removeChild(sel.lastChild);
    var sorted = Object.entries(categoriesMap).sort(function(a, b) { return (a[1] || '').localeCompare(b[1] || ''); });
    sorted.forEach(function(entry) {
        var opt = document.createElement('option');
        opt.value = entry[0];
        opt.textContent = entry[1];
        sel.appendChild(opt);
    });
}
document.addEventListener('DOMContentLoaded', function() {
    var saved = localStorage.getItem('servicesPerPage');
    if (saved) {
        var sel = document.getElementById('perPageSelect');
        if (sel) { sel.value = saved; PAGE_SIZE = saved === 'all' ? Infinity : parseInt(saved, 10); }
    }
    Promise.all([
        fetch(base + '/service-categories.json').then(function(r) { return r.json(); }),
        fetch(base + '/services.json').then(function(r) { return r.json(); })
    ]).then(function(arr) {
        categoriesMap = arr[0].categories || {};
        allServices = (arr[1].services || []).filter(function(s) { return s.active; });
        populateCategoryDropdown();
        var urlParams = new URLSearchParams(window.location.search);
        var catParam = urlParams.get('category');
        if (catParam) {
            currentCategoryFilter = catParam;
            var sel = document.getElementById('categoryFilter');
            if (sel) sel.value = catParam;
        }
        applyFilters();
        toggleView(currentView);
    }).catch(function(err) {
        console.error(err);
        showErrorMessage('Failed to load services');
    });
    var observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length) {
                var checkboxes = document.querySelectorAll('input[name="category[]"]');
                checkboxes.forEach(function(checkbox) {
                    checkbox.addEventListener('change', function() {
                        var label = this.closest('label');
                        if (this.checked) {
                            label.classList.add('border-[#003047]', 'bg-[#e6f0f3]');
                            var span = label.querySelector('span');
                            if (span) span.classList.add('text-[#003047]');
                        } else {
                            label.classList.remove('border-[#003047]', 'bg-[#e6f0f3]');
                            var span = label.querySelector('span');
                            if (span) span.classList.remove('text-[#003047]');
                        }
                    });
                });
            }
        });
    });
    var modalContent = document.getElementById('modalContent');
    if (modalContent) observer.observe(modalContent, { childList: true, subtree: true });
});
</script>
<style>
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
@endpush
@endsection

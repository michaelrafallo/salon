@extends('layouts.salon')

@section('content')
@php
    $isAdmin = in_array(session('salon_role', 'admin'), ['superadmin', 'admin'], true);
@endphp
<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Service Categories</h1>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-1">
                    <button id="gridViewBtn" onclick="toggleView('grid')" class="p-2 rounded-md hover:bg-white transition active:scale-95">
                        <svg class="w-5 h-5 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    </button>
                    <button id="listViewBtn" onclick="toggleView('list')" class="p-2 rounded-md hover:bg-white transition active:scale-95">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    </button>
                </div>
                <button onclick="openAddCategoryModal()" class="px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm sm:text-base active:scale-95">+ Add Category</button>
            </div>
        </div>
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-2"></div>
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:flex-shrink-0">
                <div class="relative w-full sm:w-[280px] lg:w-[320px]">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" id="categorySearchInput" placeholder="Search categories..." oninput="searchCategories(this.value)" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-base">
                </div>
            </div>
        </div>
        <div id="gridView" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6"></div>
        <div id="listView" class="hidden">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Services</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
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
                    <option value="15">15</option><option value="25">25</option><option value="50">50</option><option value="100">100</option><option value="all">All</option>
                </select>
                <span class="text-sm text-gray-600">per page</span>
            </div>
        </div>
        <div id="categoriesPagination" class="mt-4 flex justify-center"></div>
    </div>
</main>
@push('scripts')
<script>
(function() {
var apiCategoriesUrl = '{{ url("api/salon/service-categories") }}';
var isAdmin = {{ $isAdmin ? 'true' : 'false' }};
var allCategories = [], categoriesData = [], currentSearchTerm = '';
var editingCategoryId = null;
var PAGE_SIZE = 15, currentPage = 1, totalPages = 1, currentView = localStorage.getItem('serviceCategoriesView') || 'grid';
var colorClasses = [
    { bg: 'bg-[#e6f0f3]', text: 'text-[#003047]' }, { bg: 'bg-purple-100', text: 'text-purple-600' },
    { bg: 'bg-teal-100', text: 'text-teal-600' }, { bg: 'bg-indigo-100', text: 'text-indigo-600' },
    { bg: 'bg-rose-100', text: 'text-rose-600' }, { bg: 'bg-blue-100', text: 'text-blue-600' },
    { bg: 'bg-amber-100', text: 'text-amber-600' }, { bg: 'bg-green-100', text: 'text-green-600' }
];
function getPaginated() {
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity) return categoriesData;
    var start = (currentPage - 1) * PAGE_SIZE;
    return categoriesData.slice(start, start + PAGE_SIZE);
}
function updatePaginationState() {
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity) { totalPages = 1; currentPage = 1; return; }
    totalPages = Math.max(1, Math.ceil(categoriesData.length / PAGE_SIZE));
    if (currentPage > totalPages) currentPage = totalPages;
    if (currentPage < 1) currentPage = 1;
}
function renderPagination() {
    var el = document.getElementById('categoriesPagination');
    if (!el) return;
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity || categoriesData.length <= PAGE_SIZE || totalPages <= 1) { el.innerHTML = ''; return; }
    var h = '<div class="flex items-center gap-2 justify-center">';
    var disabledClass = 'text-gray-400 cursor-not-allowed opacity-50';
    var activeClass = 'bg-[#003047] text-white';
    var defaultClass = 'bg-white text-gray-700 border border-gray-300 hover:border-[#003047] hover:text-[#003047]';
    h += '<button class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300 ' + (currentPage === 1 ? disabledClass : 'bg-white text-[#003047] hover:bg-gray-100 hover:border-[#003047]') + '" ' + (currentPage === 1 ? 'disabled' : '') + ' onclick="goToPage(1)">&laquo;</button>';
    h += '<button class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300 ' + (currentPage === 1 ? disabledClass : 'bg-white text-[#003047] hover:bg-gray-100 hover:border-[#003047]') + '" ' + (currentPage === 1 ? 'disabled' : '') + ' onclick="changePage(-1)">&lt;</button>';
    var maxButtons = 6, startPage = Math.max(1, currentPage - Math.floor(maxButtons / 2)), endPage = Math.min(totalPages, startPage + maxButtons - 1);
    if (endPage - startPage < maxButtons - 1) startPage = Math.max(1, endPage - maxButtons + 1);
    for (var p = startPage; p <= endPage; p++) {
        h += '<button class="px-3 py-2 text-sm font-medium rounded-md border ' + (p === currentPage ? activeClass : defaultClass) + '" onclick="goToPage(' + p + ')">' + p + '</button>';
    }
    h += '<button class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300 ' + (currentPage === totalPages ? disabledClass : 'bg-white text-[#003047] hover:bg-gray-100 hover:border-[#003047]') + '" ' + (currentPage === totalPages ? 'disabled' : '') + ' onclick="changePage(1)">&gt;</button>';
    h += '<button class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300 ' + (currentPage === totalPages ? disabledClass : 'bg-white text-[#003047] hover:bg-gray-100 hover:border-[#003047]') + '" ' + (currentPage === totalPages ? 'disabled' : '') + ' onclick="goToPage(' + totalPages + ')">&raquo;</button></div>';
    el.innerHTML = h;
}
function updateResultsCounter() {
    var el = document.getElementById('resultsCounter');
    if (!el) return;
    var total = categoriesData.length;
    if (total === 0) { el.textContent = 'No results found'; return; }
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity) { el.textContent = 'Showing all ' + total + ' result' + (total !== 1 ? 's' : ''); return; }
    var start = (currentPage - 1) * PAGE_SIZE, end = Math.min(start + PAGE_SIZE, total);
    el.textContent = 'Showing ' + (start + 1) + '-' + end + ' of ' + total + ' result' + (total !== 1 ? 's' : '');
}
function applyFilters() {
    categoriesData = allCategories.filter(function(c) {
        if (currentSearchTerm === '') return true;
        var text = ((c.name || '') + ' ' + (c.slug || '')).toLowerCase();
        return text.indexOf(currentSearchTerm) >= 0;
    });
    currentPage = 1;
    updatePaginationState();
    renderCategories();
    updateResultsCounter();
}
window.searchCategories = function(val) {
    currentSearchTerm = (val || '').toLowerCase();
    applyFilters();
};
function renderGridView() {
    var el = document.getElementById('gridView');
    if (!el) return;
    el.innerHTML = '';
    var list = getPaginated();
    if (list.length === 0) {
        el.innerHTML = '<div class="col-span-full bg-white border border-dashed border-gray-300 rounded-lg p-10 text-center"><p class="text-gray-500 text-sm">No categories found.</p></div>';
        return;
    }
    list.forEach(function(c, i) {
        var color = colorClasses[i % colorClasses.length];
        var card = document.createElement('div');
        card.className = 'bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow flex flex-col';
        card.innerHTML = '<div onclick="openEditCategoryModal(' + c.id + ')" class="cursor-pointer flex-1">'
            + '<div class="flex items-center gap-3 mb-3">'
            + '<div class="w-12 h-12 ' + color.bg + ' rounded-lg flex items-center justify-center flex-shrink-0">'
            + '<svg class="w-6 h-6 ' + color.text + '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>'
            + '</div>'
            + '<div class="min-w-0"><h3 class="font-semibold text-gray-900 text-lg truncate">' + (c.name || '') + '</h3>'
            + '<p class="text-sm text-gray-500">' + (c.slug || '') + '</p></div></div>'
            + '<div class="pt-4 border-t border-gray-200"><span class="text-sm text-gray-600">' + (c.services_count || 0) + ' service' + ((c.services_count || 0) !== 1 ? 's' : '') + '</span></div></div>'
            + '<div class="flex gap-2 pt-4 mt-4 border-t border-gray-100" onclick="event.stopPropagation()">'
            + '<button type="button" onclick="openEditCategoryModal(' + c.id + ')" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm active:scale-95">'
            + '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>Edit</button>'
            + '<button type="button" onclick="deleteCategory(' + c.id + ', \'' + (c.name || '').replace(/'/g, "\\'") + '\')" class="inline-flex items-center justify-center px-4 py-2 text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition font-medium text-sm active:scale-95">'
            + '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button></div>';
        el.appendChild(card);
    });
}
function renderListView() {
    var tbody = document.getElementById('listViewBody');
    if (!tbody) return;
    tbody.innerHTML = '';
    var list = getPaginated();
    if (list.length === 0) {
        tbody.innerHTML = '<tr><td colspan="4" class="px-6 py-12 text-center text-gray-500 text-sm">No categories found.</td></tr>';
        return;
    }
    list.forEach(function(c, i) {
        var color = colorClasses[i % colorClasses.length];
        var row = document.createElement('tr');
        row.className = 'hover:bg-gray-50 transition';
        row.innerHTML = '<td class="px-6 py-4 whitespace-nowrap"><div onclick="openEditCategoryModal(' + c.id + ')" class="flex items-center cursor-pointer gap-3">'
            + '<div class="w-10 h-10 ' + color.bg + ' rounded-lg flex items-center justify-center flex-shrink-0">'
            + '<svg class="w-5 h-5 ' + color.text + '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg></div>'
            + '<span class="text-sm font-medium text-gray-900">' + (c.name || '') + '</span></div></td>'
            + '<td class="px-6 py-4 whitespace-nowrap"><span class="text-sm text-gray-500">' + (c.slug || '') + '</span></td>'
            + '<td class="px-6 py-4 whitespace-nowrap"><span class="text-sm text-gray-900">' + (c.services_count || 0) + '</span></td>'
            + '<td class="px-6 py-4 whitespace-nowrap text-sm text-right"><div class="flex items-center justify-end gap-2">'
            + '<button type="button" onclick="event.stopPropagation(); openEditCategoryModal(' + c.id + ')" class="inline-flex items-center justify-center w-8 h-8 cursor-pointer bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition active:scale-95" title="Edit">'
            + '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></button>'
            + '<button type="button" onclick="event.stopPropagation(); deleteCategory(' + c.id + ', \'' + (c.name || '').replace(/'/g, "\\'") + '\')" class="inline-flex items-center justify-center w-8 h-8 cursor-pointer text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition active:scale-95" title="Delete">'
            + '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button></div></td>';
        tbody.appendChild(row);
    });
}
function renderCategories() {
    updatePaginationState();
    if (currentView === 'grid') renderGridView();
    else renderListView();
    renderPagination();
    updateResultsCounter();
}
window.goToPage = function(page) {
    if (page < 1 || page > totalPages || page === currentPage) return;
    currentPage = page;
    renderCategories();
    updateResultsCounter();
};
window.changePage = function(offset) { goToPage(currentPage + offset); };
window.changePerPage = function(value) {
    PAGE_SIZE = value === 'all' ? Infinity : parseInt(value);
    currentPage = 1;
    updatePaginationState();
    renderCategories();
    updateResultsCounter();
    localStorage.setItem('serviceCategoriesPerPage', value);
};
function setViewUI(view) {
    var g = document.getElementById('gridView'), l = document.getElementById('listView');
    var gb = document.getElementById('gridViewBtn'), lb = document.getElementById('listViewBtn');
    var isGrid = view === 'grid';
    if (g) g.classList.toggle('hidden', !isGrid);
    if (l) l.classList.toggle('hidden', isGrid);
    if (gb) { gb.classList.toggle('bg-white', isGrid); gb.classList.toggle('shadow-sm', isGrid); if (gb.querySelector('svg')) { gb.querySelector('svg').classList.toggle('text-gray-900', isGrid); gb.querySelector('svg').classList.toggle('text-gray-500', !isGrid); } }
    if (lb) { lb.classList.toggle('bg-white', !isGrid); lb.classList.toggle('shadow-sm', !isGrid); if (lb.querySelector('svg')) { lb.querySelector('svg').classList.toggle('text-gray-900', !isGrid); lb.querySelector('svg').classList.toggle('text-gray-500', isGrid); } }
}
window.toggleView = function(view) {
    currentView = view;
    localStorage.setItem('serviceCategoriesView', view);
    setViewUI(view);
    if (view === 'grid') renderGridView(); else renderListView();
};
window.openAddCategoryModal = function() {
    editingCategoryId = null;
    var content = '<div class="flex flex-col max-h-[90vh]">'
        + '<div class="shrink-0 px-6 py-4 border-b border-gray-200 bg-white rounded-t-2xl"><div class="flex items-center justify-between"><h3 class="text-xl font-bold text-gray-900">Add New Category</h3><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div></div>'
        + '<form onsubmit="saveCategory(event)" class="flex flex-col flex-1 min-h-0">'
        + '<div class="flex-1 overflow-y-auto px-6 py-4 space-y-4">'
        + '<div><label class="block text-sm font-medium text-gray-700 mb-2">Category Name</label>'
        + '<input type="text" name="name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="e.g. Manicure, Pedicure, Waxing"></div>'
        + '</div>'
        + '<div class="shrink-0 px-6 py-4 border-t border-gray-200 bg-white rounded-b-2xl flex justify-end gap-3">'
        + '<button type="button" onclick="closeModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">Cancel</button>'
        + '<button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Save Category</button>'
        + '</div></form></div>';
    openModal(content);
};
window.openEditCategoryModal = function(id) {
    var c = allCategories.find(function(x) { return x.id === id || x.id === parseInt(id, 10); });
    if (!c) return;
    editingCategoryId = c.id;
    var content = '<div class="flex flex-col max-h-[90vh]">'
        + '<div class="shrink-0 px-6 py-4 border-b border-gray-200 bg-white rounded-t-2xl"><div class="flex items-center justify-between"><h3 class="text-xl font-bold text-gray-900">Edit Category</h3><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div></div>'
        + '<form onsubmit="saveCategory(event)" class="flex flex-col flex-1 min-h-0">'
        + '<div class="flex-1 overflow-y-auto px-6 py-4 space-y-4">'
        + '<div><label class="block text-sm font-medium text-gray-700 mb-2">Category Name</label>'
        + '<input type="text" name="name" required value="' + (c.name || '').replace(/"/g, '&quot;') + '" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div>'
        + '<div><label class="block text-sm font-medium text-gray-700 mb-2">Slug</label>'
        + '<input type="text" value="' + (c.slug || '').replace(/"/g, '&quot;') + '" disabled class="w-full px-4 py-3 border border-gray-200 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed">'
        + '<p class="mt-1 text-xs text-gray-500">Auto-generated from name.</p></div>'
        + '<div><label class="block text-sm font-medium text-gray-700 mb-2">Services</label>'
        + '<p class="text-sm text-gray-600">' + (c.services_count || 0) + ' service' + ((c.services_count || 0) !== 1 ? 's' : '') + ' using this category</p></div>'
        + '</div>'
        + '<div class="shrink-0 px-6 py-4 border-t border-gray-200 bg-white rounded-b-2xl flex justify-end gap-3">'
        + '<button type="button" onclick="closeModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">Cancel</button>'
        + '<button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Update Category</button>'
        + '</div></form></div>';
    openModal(content);
};
window.saveCategory = function(event) {
    event.preventDefault();
    var form = event.target;
    var btn = form.querySelector('button[type="submit"]');
    var name = form.name.value.trim();
    if (btn) { btn.disabled = true; btn.textContent = editingCategoryId ? 'Updating...' : 'Saving...'; }
    var url = editingCategoryId ? apiCategoriesUrl + '/' + editingCategoryId : apiCategoriesUrl;
    var body = { name: name };
    var promise;
    if (editingCategoryId) {
        promise = salonApi.put(url, body);
    } else {
        promise = salonApi.post(url, body);
    }
    promise.then(function(res) {
        showSuccessMessage(res.message || 'Category saved successfully.');
        closeModal();
        var c = res.data;
        var idx = allCategories.findIndex(function(x) { return x.id === c.id; });
        if (idx >= 0) allCategories[idx] = c;
        else allCategories.unshift(c);
        editingCategoryId = null;
        applyFilters();
        renderCategories();
    }).catch(function(err) {
        var msg = err.message || 'Failed to save category.';
        if (err.body && err.body.errors && typeof err.body.errors === 'object') {
            var firstKey = Object.keys(err.body.errors)[0];
            if (firstKey && err.body.errors[firstKey] && err.body.errors[firstKey][0]) msg = err.body.errors[firstKey][0];
        }
        showErrorMessage(msg);
    }).finally(function() {
        if (btn) { btn.disabled = false; btn.textContent = editingCategoryId ? 'Update Category' : 'Save Category'; }
    });
};
window.deleteCategory = function(id, name) {
    openConfirmModal({
        title: 'Delete category',
        message: 'You are about to permanently remove ' + (name ? boldName(name) : 'this category') + '. Services using this category will be unlinked. Do you want to continue?',
        confirmLabel: 'Delete',
        nested: true,
        onConfirm: function() {
            salonApi.delete(apiCategoriesUrl + '/' + id).then(function() {
                closeModal();
                showSuccessMessage('Category deleted.');
                allCategories = allCategories.filter(function(c) { return c.id !== id && c.id !== parseInt(id, 10); });
                applyFilters();
                renderCategories();
            }).catch(function(err) {
                showErrorMessage(err.message || 'Failed to delete category.');
            });
        }
    });
};
// Boot
document.addEventListener('DOMContentLoaded', function() {
    var saved = localStorage.getItem('serviceCategoriesPerPage');
    if (saved) {
        var sel = document.getElementById('perPageSelect');
        if (sel) { sel.value = saved; PAGE_SIZE = saved === 'all' ? Infinity : parseInt(saved, 10); }
    }
    setViewUI(currentView);
    var bootstrap = @json($categoriesBootstrap ?? null);
    if (bootstrap && bootstrap.categories) {
        allCategories = bootstrap.categories;
        applyFilters();
        toggleView(currentView);
    } else {
        var base = window.salonJsonBase || '{{ url("api/salon/data") }}';
        fetch(base + '/service-categories').then(function(r) { return r.json(); }).then(function(data) {
            var cats = data.categories || {};
            allCategories = Object.entries(cats).map(function(entry, i) {
                return { id: i + 1, slug: entry[0], name: entry[1], services_count: 0 };
            });
            applyFilters();
            toggleView(currentView);
        }).catch(function(err) {
            console.error(err);
            showErrorMessage('Failed to load categories');
        });
    }
});
})();
</script>
@endpush
@endsection

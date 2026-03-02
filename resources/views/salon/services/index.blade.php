@extends('layouts.salon')

@section('content')
@php
    $isAdmin = in_array(session('salon_role', 'admin'), ['admin'], true);
    $servicesTab = request()->query('status', 'all');
    if (! in_array($servicesTab, ['all', 'active', 'inactive'], true)) {
        $servicesTab = 'all';
    }
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
            <div class="flex items-center gap-2 border-b border-gray-200 overflow-x-auto min-w-0">
                <button type="button" id="tab-all" onclick="filterByStatus('all')" class="px-4 py-2 text-sm font-medium transition whitespace-nowrap {{ $servicesTab === 'all' ? 'text-[#003047] border-b-2 border-[#003047]' : 'text-gray-500 border-b-2 border-transparent hover:text-gray-700' }}">All</button>
                <button type="button" id="tab-active" onclick="filterByStatus('active')" class="px-4 py-2 text-sm font-medium transition whitespace-nowrap {{ $servicesTab === 'active' ? 'text-[#003047] border-b-2 border-[#003047]' : 'text-gray-500 border-b-2 border-transparent hover:text-gray-700' }}">Active</button>
                <button type="button" id="tab-inactive" onclick="filterByStatus('inactive')" class="px-4 py-2 text-sm font-medium transition whitespace-nowrap {{ $servicesTab === 'inactive' ? 'text-[#003047] border-b-2 border-[#003047]' : 'text-gray-500 border-b-2 border-transparent hover:text-gray-700' }}">Inactive</button>
            </div>
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 sm:flex-shrink-0">
                <select id="categoryFilter" onchange="filterByCategory(this.value)" class="w-full sm:w-auto sm:min-w-[180px] px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-base bg-white cursor-pointer">
                    <option value="">All Categories</option>
                </select>
                <div class="relative w-full sm:w-[280px] lg:w-[320px]">
                    <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" id="serviceSearchInput" placeholder="Search services by name or description..." oninput="searchServices(this.value)" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-base">
                </div>
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
var base = window.salonJsonBase || '{{ url("api/salon/data") }}';
window.salonServicesBootstrap = window.salonServicesBootstrap || @json($servicesBootstrap ?? null);
var apiServicesUrl = '{{ url("api/salon/services") }}';
var storageUrl = '{{ rtrim(asset("storage"), "/") }}';
var isAdmin = {{ $isAdmin ? 'true' : 'false' }};
var SERVICE_PASSWORD = '54321';
var allServices = [], servicesData = [], categoriesMap = {}, currentCategoryFilter = '', currentSearchTerm = '', currentStatusFilter = @json($servicesTab);
var editingServiceId = null;
var PAGE_SIZE = 15, currentPage = 1, totalPages = 1, currentView = localStorage.getItem('servicesView') || 'grid';
var pendingAction = null;
var selectedServiceColor = null;
var serviceColorPresets = [
    {hex:'#FF0000',name:'Red'},{hex:'#800000',name:'Maroon'},{hex:'#FF4500',name:'Orange Red'},
    {hex:'#FF8C00',name:'Dark Orange'},{hex:'#FFA500',name:'Orange'},{hex:'#FF7F50',name:'Coral'},
    {hex:'#FFD700',name:'Gold'},{hex:'#FFFF00',name:'Yellow'},{hex:'#32CD32',name:'Lime Green'},
    {hex:'#008000',name:'Green'},{hex:'#006400',name:'Dark Green'},{hex:'#2E8B57',name:'Sea Green'},
    {hex:'#008080',name:'Teal'},{hex:'#00CED1',name:'Turquoise'},{hex:'#00BFFF',name:'Sky Blue'},
    {hex:'#4169E1',name:'Royal Blue'},{hex:'#0000FF',name:'Blue'},{hex:'#000080',name:'Navy'},
    {hex:'#4B0082',name:'Indigo'},{hex:'#8A2BE2',name:'Violet'},{hex:'#800080',name:'Purple'},
    {hex:'#FF00FF',name:'Magenta'},{hex:'#FF69B4',name:'Hot Pink'},{hex:'#FF1493',name:'Deep Pink'},
    {hex:'#8B4513',name:'Brown'},{hex:'#D2691E',name:'Chocolate'},
    {hex:'#000000',name:'Black'}
];
var presetHexes = serviceColorPresets.map(function(c) { return c.hex; });
function buildColorPickerHtml(currentColor) {
    var preview = currentColor
        ? '<div style="width:48px;height:48px;border-radius:9999px;border:2px solid #fff;box-shadow:0 4px 6px -1px rgba(0,0,0,.1);background:' + currentColor + '"></div><button type="button" onclick="selectServiceColor(null)" style="position:absolute;top:-4px;left:-4px;width:18px;height:18px;border-radius:9999px;background:#fff;border:1px solid #d1d5db;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 1px 2px rgba(0,0,0,.1)" title="Remove color"><svg style="width:10px;height:10px" fill="none" stroke="#9ca3af" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg></button>'
        : '<div style="width:48px;height:48px;border-radius:9999px;border:2px dashed #d1d5db;display:flex;align-items:center;justify-content:center;background:#fff"><svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg></div>';
    var buttons = serviceColorPresets.map(function(c) {
        var isSelected = currentColor && currentColor.toUpperCase() === c.hex;
        return '<button type="button" onclick="selectServiceColor(\'' + c.hex + '\')" class="rounded-full border-2 transition-all hover:scale-110 ' + (isSelected ? 'border-gray-900 ring-2 ring-offset-1 ring-gray-900' : 'border-white shadow-sm') + '" style="width:28px;height:28px;background:' + c.hex + '" title="' + c.name + '"></button>';
    }).join('');
    var isCustom = currentColor && !presetHexes.some(function(p) { return currentColor.toUpperCase() === p; });
    var customBtn = '<div class="relative"><button type="button" onclick="document.getElementById(\'serviceCustomColorPicker\').click()" class="rounded-full border-2 transition-all hover:scale-110 flex items-center justify-center ' + (isCustom ? 'border-gray-900 ring-2 ring-offset-1 ring-gray-900' : 'border-gray-300') + '" style="width:28px;height:28px;background:conic-gradient(red, yellow, lime, aqua, blue, magenta, red)" title="Custom color"></button><input type="color" id="serviceCustomColorPicker" value="' + (currentColor || '#003047') + '" class="absolute opacity-0 w-0 h-0" onchange="selectServiceColor(this.value)"></div>';
    return '<div class="p-4 bg-gray-50 rounded-xl"><div class="flex items-center gap-4"><div class="flex-shrink-0" id="serviceColorPreview" style="width:48px;height:48px;position:relative">' + preview + '</div><div class="flex-1 min-w-0"><p class="text-xs font-medium text-gray-500 mb-2">Service Color</p><div class="flex items-center gap-2 flex-wrap">' + buttons + customBtn + '</div></div></div></div>';
}
function selectServiceColor(hex) {
    selectedServiceColor = hex;
    var container = document.getElementById('serviceColorPreview');
    if (!container) return;
    if (hex) {
        container.innerHTML = '<div style="width:48px;height:48px;border-radius:9999px;border:2px solid #fff;box-shadow:0 4px 6px -1px rgba(0,0,0,.1);background:' + hex + '"></div><button type="button" onclick="selectServiceColor(null)" style="position:absolute;top:-4px;left:-4px;width:18px;height:18px;border-radius:9999px;background:#fff;border:1px solid #d1d5db;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 1px 2px rgba(0,0,0,.1)" title="Remove color"><svg style="width:10px;height:10px" fill="none" stroke="#9ca3af" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg></button>';
    } else {
        container.innerHTML = '<div style="width:48px;height:48px;border-radius:9999px;border:2px dashed #d1d5db;display:flex;align-items:center;justify-content:center;background:#fff"><svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg></div>';
    }
    // Update button highlights
    var modal = container.closest('.max-h-\\[90vh\\]') || container.closest('[class*="max-h"]') || document.getElementById('modalContent');
    if (!modal) return;
    modal.querySelectorAll('.rounded-full.border-2[title]').forEach(function(btn) {
        var btnColor = btn.style.background;
        var title = btn.getAttribute('title');
        if (title === 'Custom color') {
            var isCustom = hex && !presetHexes.some(function(p) { return hex.toUpperCase() === p; });
            btn.className = 'rounded-full border-2 transition-all hover:scale-110 flex items-center justify-center ' + (isCustom ? 'border-gray-900 ring-2 ring-offset-1 ring-gray-900' : 'border-gray-300');
            return;
        }
        var preset = serviceColorPresets.find(function(p) { return p.name === title; });
        if (!preset) return;
        var isSelected = hex && hex.toUpperCase() === preset.hex;
        btn.className = 'rounded-full border-2 transition-all hover:scale-110 ' + (isSelected ? 'border-gray-900 ring-2 ring-offset-1 ring-gray-900' : 'border-white shadow-sm');
    });
}
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
        var statusMatch = true;
        if (currentStatusFilter === 'active') statusMatch = !!s.active;
        else if (currentStatusFilter === 'inactive') statusMatch = !s.active;
        var categoryMatch = true;
        if (currentCategoryFilter !== '') {
            categoryMatch = s.categories && s.categories.includes(currentCategoryFilter);
        }
        var searchMatch = true;
        if (currentSearchTerm !== '') {
            var text = ((s.name || '') + ' ' + (s.description || '')).toLowerCase();
            searchMatch = text.includes(currentSearchTerm);
        }
        return statusMatch && categoryMatch && searchMatch;
    });
    currentPage = 1;
    updatePaginationState();
    renderServices();
    updateResultsCounter();
}
function filterByStatus(status) {
    currentStatusFilter = status;
    var url = new URL(window.location);
    if (status === 'all') url.searchParams.delete('status'); else url.searchParams.set('status', status);
    window.history.pushState({}, '', url);
    ['all', 'active', 'inactive'].forEach(function(id) {
        var btn = document.getElementById('tab-' + id);
        if (!btn) return;
        var active = id === status;
        btn.className = 'px-4 py-2 text-sm font-medium whitespace-nowrap border-b-2 transition ' + (active ? 'text-[#003047] border-[#003047]' : 'text-gray-500 border-transparent hover:text-gray-700');
    });
    applyFilters();
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
        var imgUrl = (s.image ? (storageUrl + '/' + s.image) : null) || s.image_url || null;
        var safeImgUrl = imgUrl ? imgUrl.replace(/"/g, '&quot;').replace(/'/g, '&#39;') : '';
        var thumbHtml = imgUrl
            ? '<div class="flex items-start mb-3 cursor-pointer" onclick="event.stopPropagation();previewServiceImage(\'' + safeImgUrl + '\')"><img src="' + safeImgUrl + '" alt="" class="w-full h-32 object-cover rounded-lg hover:opacity-80 transition" onerror="this.parentElement.onclick=null;this.parentElement.style.cursor=\'default\';this.parentElement.innerHTML=\'<div class=\\\'w-full h-32 rounded-lg bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center\\\'><svg class=\\\'w-8 h-8 text-gray-300\\\' fill=\\\'none\\\' stroke=\\\'currentColor\\\' viewBox=\\\'0 0 24 24\\\'><path stroke-linecap=\\\'round\\\' stroke-linejoin=\\\'round\\\' stroke-width=\\\'2\\\' d=\\\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\\\'></path></svg></div>\'"></div>'
            : '<div class="flex items-start mb-3"><div class="w-full h-32 rounded-lg bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center"><svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></div></div>';
        var card = document.createElement('div');
        card.className = 'bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow flex flex-col';
        var colorDot = s.color ? '<span class="inline-block w-3 h-3 rounded-full flex-shrink-0" style="background:' + s.color + '"></span>' : '<span class="inline-block w-3 h-3 rounded-full flex-shrink-0 border-2 border-gray-300"></span>';
        card.innerHTML = thumbHtml + '<div onclick="openServiceModal(' + JSON.stringify(s).replace(/"/g, '&quot;') + ')" class="cursor-pointer flex-1"><h3 class="font-semibold text-gray-900 text-lg mb-2 flex items-center gap-2">' + colorDot + (s.name || '') + '</h3><p class="text-sm text-gray-600 mb-4 line-clamp-2">' + (s.description || '') + '</p><div class="pt-4 border-t border-gray-200"><span class="text-2xl font-bold text-gray-900">' + window.salonFormatMoney(parseFloat(s.price || 0)) + '</span></div></div><div class="flex gap-2 pt-4 mt-4 border-t border-gray-100" onclick="event.stopPropagation()"><button type="button" onclick="openEditServiceModal(' + s.id + ')" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm active:scale-95"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>Quick Edit</button><button type="button" onclick="deleteService(' + s.id + ', \'' + (s.name || '').replace(/'/g, "\\'") + '\')" class="inline-flex items-center justify-center px-4 py-2 text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition font-medium text-sm active:scale-95"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button></div>';
        el.appendChild(card);
    });
}
function renderListView() {
    var tbody = document.getElementById('listViewBody');
    if (!tbody) return;
    tbody.innerHTML = '';
    var list = getPaginatedServices();
    if (list.length === 0) {
        tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-12 text-center text-gray-500 text-sm">No services found.</td></tr>';
        return;
    }
    list.forEach(function(s, i) {
        var color = colorClasses[i % colorClasses.length];
        var imgUrl = (s.image ? (storageUrl + '/' + s.image) : null) || s.image_url || null;
        var safeListImgUrl = imgUrl ? imgUrl.replace(/"/g, '&quot;').replace(/'/g, '&#39;') : '';
        var thumbCell = imgUrl
            ? '<img src="' + safeListImgUrl + '" alt="" class="w-10 h-10 rounded-lg object-cover flex-shrink-0 mr-3 cursor-pointer hover:opacity-80 transition" onclick="event.stopPropagation();previewServiceImage(\'' + safeListImgUrl + '\')" onerror="this.onclick=null;this.outerHTML=\'<div class=\\\'w-10 h-10 rounded-lg bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center flex-shrink-0 mr-3\\\'><svg class=\\\'w-4 h-4 text-gray-300\\\' fill=\\\'none\\\' stroke=\\\'currentColor\\\' viewBox=\\\'0 0 24 24\\\'><path stroke-linecap=\\\'round\\\' stroke-linejoin=\\\'round\\\' stroke-width=\\\'2\\\' d=\\\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\\\'></path></svg></div>\'">'
            : '<div class="w-10 h-10 rounded-lg bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center flex-shrink-0 mr-3"><svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></div>';
        var row = document.createElement('tr');
        row.className = 'hover:bg-gray-50 transition';
        var statusHtml = s.active ? '<span class="text-sm font-medium text-green-600">Active</span>' : '<span class="text-sm font-medium text-gray-500">Inactive</span>';
        var colorDot = s.color ? '<span class="inline-block w-3 h-3 rounded-full flex-shrink-0" style="background:' + s.color + '"></span>' : '<span class="inline-block w-3 h-3 rounded-full flex-shrink-0 border-2 border-gray-300"></span>';
        row.innerHTML = '<td class="px-6 py-4 whitespace-nowrap"><div onclick="openServiceModal(' + JSON.stringify(s).replace(/"/g, '&quot;') + ')" class="flex items-center cursor-pointer">' + thumbCell + '<div class="text-sm font-medium text-gray-900 flex items-center gap-2">' + colorDot + (s.name || '') + '</div></div></td><td class="px-6 py-4"><div onclick="openServiceModal(' + JSON.stringify(s).replace(/"/g, '&quot;') + ')" class="text-sm text-gray-900 cursor-pointer">' + (s.description || '') + '</div></td><td class="px-6 py-4 whitespace-nowrap"><div onclick="openServiceModal(' + JSON.stringify(s).replace(/"/g, '&quot;') + ')" class="text-sm font-bold text-gray-900 cursor-pointer">' + window.salonFormatMoney(parseFloat(s.price || 0)) + '</div></td><td class="px-6 py-4 whitespace-nowrap"><div onclick="openServiceModal(' + JSON.stringify(s).replace(/"/g, '&quot;') + ')" class="cursor-pointer">' + statusHtml + '</div></td><td class="px-6 py-4 whitespace-nowrap text-sm text-right"><div class="flex items-center justify-end gap-2"><button type="button" onclick="event.stopPropagation(); openEditServiceModal(' + s.id + ')" class="inline-flex items-center justify-center w-8 h-8 cursor-pointer bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition active:scale-95" title="Quick Edit"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></button><button type="button" onclick="event.stopPropagation(); deleteService(' + s.id + ', \'' + (s.name || '').replace(/'/g, "\\'") + '\')" class="inline-flex items-center justify-center w-8 h-8 cursor-pointer text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition active:scale-95" title="Delete"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button></div></td>';
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
function setViewUI(view) {
    var g = document.getElementById('gridView');
    var l = document.getElementById('listView');
    var gb = document.getElementById('gridViewBtn');
    var lb = document.getElementById('listViewBtn');

    var isGrid = view === 'grid';
    if (g) g.classList.toggle('hidden', !isGrid);
    if (l) l.classList.toggle('hidden', isGrid);

    if (gb) {
        gb.classList.toggle('bg-white', isGrid);
        gb.classList.toggle('shadow-sm', isGrid);
        if (gb.querySelector('svg')) {
            gb.querySelector('svg').classList.toggle('text-gray-900', isGrid);
            gb.querySelector('svg').classList.toggle('text-gray-500', !isGrid);
        }
    }
    if (lb) {
        lb.classList.toggle('bg-white', !isGrid);
        lb.classList.toggle('shadow-sm', !isGrid);
        if (lb.querySelector('svg')) {
            lb.querySelector('svg').classList.toggle('text-gray-900', !isGrid);
            lb.querySelector('svg').classList.toggle('text-gray-500', isGrid);
        }
    }
}
function toggleView(view) {
    currentView = view;
    localStorage.setItem('servicesView', view);
    setViewUI(view);
    if (view === 'grid') {
        renderGridView();
        return;
    }
    renderListView();
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
    editingServiceId = null;
    selectedServiceColor = null;
    var sorted = Object.entries(categoriesMap).sort(function(a, b) { return (a[1] || '').localeCompare(b[1] || ''); });
    var checkboxes = sorted.map(function(entry) {
        var k = entry[0], v = entry[1];
        return '<label class="flex items-center p-2 bg-white rounded-lg border border-gray-200 hover:border-[#003047] hover:bg-[#e6f0f3] cursor-pointer transition-all duration-200 group has-[:checked]:border-[#003047] has-[:checked]:bg-[#e6f0f3]"><input type="checkbox" name="category[]" value="' + k + '" class="w-4 h-4 text-[#003047] border-gray-300 rounded focus:ring-[#003047] focus:ring-2 cursor-pointer" style="accent-color: #003047;"><span class="ml-2 text-sm font-medium text-gray-700 group-hover:text-[#003047] has-[:checked]:text-[#003047]">' + v + '</span></label>';
    }).join('');
    var fileBlock = '<div class="w-1/2"><label class="block text-sm font-medium text-gray-700 mb-2">Image</label><input type="file" name="image" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-sm"><p class="mt-1 text-xs text-gray-500">Optional. Max 2 MB. JPG, PNG, GIF, WebP.</p></div>';
    var colorPickerHtml = buildColorPickerHtml(null);
    var content = '<div class="max-h-[90vh] flex flex-col"><div class="p-6 border-b border-gray-200 flex items-center justify-between"><h3 class="text-xl font-bold text-gray-900">Add New Service</h3><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><form onsubmit="saveService(event)" class="flex flex-col flex-1 min-h-0" enctype="multipart/form-data"><div class="space-y-4 p-6 overflow-y-auto"><div class="flex gap-4"><div class="flex-1"><label class="block text-sm font-medium text-gray-700 mb-2">Service Name</label><input type="text" name="name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Classic Manicure"></div><div class="w-32"><label class="block text-sm font-medium text-gray-700 mb-2">Price (' + (window.salonCurrencySymbol || '$') + ')</label><input type="number" name="price" required step="0.01" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="35.00"></div></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Category</label><div class="border border-gray-300 rounded-lg p-2 bg-gray-50"><div class="grid grid-cols-2 sm:grid-cols-3 gap-2">' + checkboxes + '</div></div><p class="mt-2 text-xs text-gray-500">Select one or more categories for this service</p></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Description</label><textarea name="description" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Service description"></textarea></div>' + colorPickerHtml + '<div class="flex gap-4 items-start">' + fileBlock + '<div class="w-1/2"><label class="block text-sm font-medium text-gray-700 mb-2">Active</label><label class="relative inline-flex items-center cursor-pointer"><input type="checkbox" name="active" class="sr-only peer" checked><div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[\'\'] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div></label></div></div></div><div class="flex justify-end gap-3 p-6 border-t border-gray-200"><button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Save Service</button></div></form></div>';
    openModal(content);
}
function openServiceModal(service) {
    if (!service || typeof service !== 'object') return;
    if (!isAdmin) {
        openPasswordModal(function() { openServiceModalContent(service); });
        return;
    }
    openServiceModalContent(service);
}
function openEditServiceModal(id) {
    var service = allServices.find(function(s) { return s.id === id || s.id === parseInt(id, 10); });
    if (!service) return;
    openServiceModal(service);
}
function openServiceModalContent(service) {
    var id = service.id, name = service.name || '', description = service.description || '', price = service.price != null ? service.price : 0, active = !!service.active;
    var imgUrl = (service.image ? (storageUrl + '/' + service.image) : null) || service.image_url || null;
    var cats = service.categories || [];
    editingServiceId = id || null;
    selectedServiceColor = service.color || null;
    var sorted = Object.entries(categoriesMap).sort(function(a, b) { return (a[1] || '').localeCompare(b[1] || ''); });
    var checkboxes = sorted.map(function(entry) {
        var k = entry[0], v = entry[1], checked = cats.indexOf(k) >= 0 ? 'checked' : '';
        return '<label class="flex items-center p-2 bg-white rounded-lg border border-gray-200 hover:border-[#003047] hover:bg-[#e6f0f3] cursor-pointer transition-all duration-200 group has-[:checked]:border-[#003047] has-[:checked]:bg-[#e6f0f3]"><input type="checkbox" name="category[]" value="' + k + '" ' + checked + ' class="w-4 h-4 text-[#003047] border-gray-300 rounded focus:ring-[#003047] focus:ring-2 cursor-pointer" style="accent-color: #003047;"><span class="ml-2 text-sm font-medium text-gray-700 group-hover:text-[#003047] has-[:checked]:text-[#003047]">' + v + '</span></label>';
    }).join('');
    var fileBlock = '<div class="w-1/2"><label class="block text-sm font-medium text-gray-700 mb-2">Image</label>' +
        (imgUrl ? '<div id="currentImagePreview" class="mb-3 flex items-start gap-3"><img src="' + imgUrl.replace(/"/g, '&quot;') + '" alt="" class="h-20 w-20 object-cover rounded-lg border border-gray-200 flex-shrink-0 cursor-pointer hover:opacity-80 transition" onclick="previewServiceImage(\'' + imgUrl.replace(/'/g, "\\'") + '\')"><button type="button" onclick="removeServiceImage()" class="px-3 py-1.5 text-sm text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition font-medium active:scale-95">Remove Image</button></div>' : '') +
        '<input type="hidden" id="removeImageFlag" name="remove_image" value="0">' +
        '<input type="file" name="image" accept="image/*" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-sm"><p class="mt-1 text-xs text-gray-500">Optional. Max 2 MB. Leave empty to keep current.</p></div>';
    var deleteBtn = '';
    var colorPickerHtml = buildColorPickerHtml(selectedServiceColor);
    var content = '<div class="max-h-[90vh] flex flex-col"><div class="p-6 border-b border-gray-200 flex items-center justify-between"><h3 class="text-xl font-bold text-gray-900">' + (id ? 'Edit Service' : 'View Service') + '</h3><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><form onsubmit="saveService(event)" class="flex flex-col flex-1 min-h-0" enctype="multipart/form-data"><div class="space-y-4 p-6 overflow-y-auto"><div class="flex gap-4"><div class="flex-1"><label class="block text-sm font-medium text-gray-700 mb-2">Service Name</label><input type="text" name="name" required value="' + (name || '').replace(/"/g, '&quot;') + '" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Classic Manicure"></div><div class="w-32"><label class="block text-sm font-medium text-gray-700 mb-2">Price (' + (window.salonCurrencySymbol || '$') + ')</label><input type="number" name="price" required step="0.01" value="' + (price || 0) + '" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="35.00"></div></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Category</label><div class="border border-gray-300 rounded-lg p-2 bg-gray-50"><div class="grid grid-cols-2 sm:grid-cols-3 gap-2">' + checkboxes + '</div></div><p class="mt-2 text-xs text-gray-500">Select one or more categories for this service</p></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Description</label><textarea name="description" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Service description">' + (description || '').replace(/</g, '&lt;').replace(/>/g, '&gt;') + '</textarea></div>' + colorPickerHtml + '<div class="flex gap-4 items-start">' + fileBlock + '<div class="w-1/2"><label class="block text-sm font-medium text-gray-700 mb-2">Active</label><label class="relative inline-flex items-center cursor-pointer"><input type="checkbox" name="active" class="sr-only peer" ' + (active ? 'checked' : '') + '><div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[\'\'] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div></label></div></div></div><div class="flex justify-end gap-3 p-6 border-t border-gray-200">' + deleteBtn + '<button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Save Service</button></div></form></div>';
    openModal(content);
}
function buildServiceFormData(form) {
    var fd = new FormData();
    fd.append('name', form.name.value.trim());
    fd.append('description', form.description.value.trim() || '');
    fd.append('price', form.price.value ? parseFloat(form.price.value, 10) : 0);
    var activeEl = form.querySelector('input[name="active"]');
    fd.append('active', activeEl && activeEl.checked ? '1' : '0');
    if (selectedServiceColor) fd.append('color', selectedServiceColor);
    else fd.append('color', '');
    form.querySelectorAll('input[name="category[]"]:checked').forEach(function(cb) { fd.append('categories[]', cb.value); });
    var fileEl = form.querySelector('input[name="image"]');
    if (fileEl && fileEl.files && fileEl.files[0]) fd.append('image', fileEl.files[0]);
    var removeImageFlag = form.querySelector('input[name="remove_image"]');
    if (removeImageFlag && removeImageFlag.value === '1') fd.append('remove_image', '1');
    return fd;
}
function saveService(event) {
    event.preventDefault();
    var form = event.target;
    var btn = form.querySelector('button[type="submit"]');
    var formData = buildServiceFormData(form);
    if (btn) { btn.disabled = true; btn.textContent = 'Saving...'; }
    var url = editingServiceId ? apiServicesUrl + '/' + editingServiceId : apiServicesUrl;
    if (editingServiceId) formData.append('_method', 'PUT');
    var promise = salonApi.postFormData(url, formData);
    promise.then(function(res) {
        showSuccessMessage(res.message || 'Service saved successfully.');
        closeModal();
        editingServiceId = null;
        var s = res.data;
        var idx = allServices.findIndex(function(x) { return x.id === s.id; });
        if (idx >= 0) allServices[idx] = s;
        else allServices.unshift(s);
        applyFilters();
        renderServices();
    }).catch(function(err) {
        var msg = err.message || 'Failed to save service.';
        if (err.body && err.body.errors && typeof err.body.errors === 'object') {
            var firstKey = Object.keys(err.body.errors)[0];
            if (firstKey && err.body.errors[firstKey] && err.body.errors[firstKey][0]) msg = err.body.errors[firstKey][0];
        }
        showErrorMessage(msg);
    }).finally(function() {
        if (btn) { btn.disabled = false; btn.textContent = 'Save Service'; }
    });
}
function deleteService(id, name) {
    openConfirmModal({
        title: 'Delete service',
        message: 'You are about to permanently remove ' + (name ? boldName(name) : 'this service') + '. Do you want to continue?',
        confirmLabel: 'Delete',
        nested: true,
        onConfirm: function() {
            salonApi.delete(apiServicesUrl + '/' + id).then(function() {
                closeModal();
                showSuccessMessage('Service deleted.');
                allServices = allServices.filter(function(s) { return s.id !== id && s.id !== parseInt(id, 10); });
                applyFilters();
                renderServices();
            }).catch(function(err) {
                showErrorMessage(err.message || 'Failed to delete service.');
            });
        }
    });
}
function previewServiceImage(url) {
    var overlay = document.createElement('div');
    overlay.style.cssText = 'position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,0.85);display:flex;align-items:center;justify-content:center;cursor:pointer;';
    overlay.onclick = function() { overlay.remove(); };
    overlay.innerHTML = '<button onclick="event.stopPropagation();this.parentElement.remove();" style="position:absolute;top:1rem;right:1rem;background:rgba(255,255,255,0.15);border:none;color:#fff;border-radius:9999px;width:2.5rem;height:2.5rem;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:background 0.2s;" onmouseenter="this.style.background=\'rgba(255,255,255,0.3)\'" onmouseleave="this.style.background=\'rgba(255,255,255,0.15)\'"><svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button><img src="' + url + '" alt="Service Image" style="max-height:90vh;max-width:90vw;object-fit:contain;border-radius:0.5rem;" onclick="event.stopPropagation();">';
    document.body.appendChild(overlay);
}
function removeServiceImage() {
    var preview = document.getElementById('currentImagePreview');
    var flag = document.getElementById('removeImageFlag');
    if (preview) {
        preview.style.display = 'none';
    }
    if (flag) {
        flag.value = '1';
    }
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
    setViewUI(currentView);
    var dataPromise;
    if (window.salonServicesBootstrap && window.salonServicesBootstrap.services) {
        dataPromise = Promise.resolve({
            categories: window.salonServicesBootstrap.categories || {},
            services: window.salonServicesBootstrap.services || []
        });
    } else {
        dataPromise = Promise.all([
            fetch(base + '/service-categories').then(function(r) { return r.json(); }),
            fetch(base + '/services').then(function(r) { return r.json(); })
        ]).then(function(arr) {
            return { categories: (arr[0] && arr[0].categories) || {}, services: (arr[1] && arr[1].services) || [] };
        });
    }
    dataPromise.then(function(payload) {
        categoriesMap = payload.categories || {};
        allServices = payload.services || [];
        populateCategoryDropdown();
        var urlParams = new URLSearchParams(window.location.search);
        var catParam = urlParams.get('category');
        if (catParam) {
            currentCategoryFilter = catParam;
            var sel = document.getElementById('categoryFilter');
            if (sel) sel.value = catParam;
        }
        var statusParam = urlParams.get('status');
        if (statusParam === 'active' || statusParam === 'inactive') {
            currentStatusFilter = statusParam;
            ['all', 'active', 'inactive'].forEach(function(id) {
                var btn = document.getElementById('tab-' + id);
                if (!btn) return;
                var active = id === statusParam;
                btn.className = 'px-4 py-2 text-sm font-medium whitespace-nowrap border-b-2 transition ' + (active ? 'text-[#003047] border-[#003047]' : 'text-gray-500 border-transparent hover:text-gray-700');
            });
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

@extends('layouts.salon')

@section('content')
<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Payments</h1>
            <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-1">
                <button id="listViewBtn" onclick="salonPaymentsToggleView('list')" class="p-2 rounded-md hover:bg-white transition active:scale-95">
                    <svg class="w-5 h-5 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                </button>
                <button id="gridViewBtn" onclick="salonPaymentsToggleView('grid')" class="p-2 rounded-md hover:bg-white transition active:scale-95">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                </button>
            </div>
        </div>
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
                    <tbody id="listViewBody" class="divide-y divide-gray-200"></tbody>
                </table>
            </div>
        </div>
        <div id="gridView" class="hidden">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Transactions</h2>
            <div id="gridViewContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6"></div>
        </div>
        <div class="mt-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div id="paymentsResultsCounter" class="text-sm text-gray-600"></div>
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-600">Show:</label>
                <select id="perPageSelect" onchange="salonPaymentsChangePerPage(this.value)" class="px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-sm bg-white cursor-pointer">
                    <option value="15">15</option><option value="25">25</option><option value="50">50</option><option value="100">100</option><option value="250">250</option><option value="500">500</option><option value="all">All</option>
                </select>
                <span class="text-sm text-gray-600">per page</span>
            </div>
        </div>
        <div id="paymentsPagination" class="mt-4 flex justify-center"></div>
    </div>
</main>
@push('scripts')
<script>
(function() {
var base = window.salonJsonBase || '{{ url("api/salon/data") }}';
window.salonPaymentsBootstrap = window.salonPaymentsBootstrap || @json($paymentsBootstrap ?? null);
var apiPaymentsUrl = '{{ url("api/salon/payments") }}';
var allPayments = [], paymentsData = [], PAGE_SIZE = 15, currentPage = 1, totalPages = 1;
var currentView = localStorage.getItem('paymentsView') || 'list';
function formatDate(str) {
    if (!str) return '';
    var d = new Date(str + 'T00:00:00');
    var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    return months[d.getMonth()] + ' ' + d.getDate() + ', ' + d.getFullYear();
}
function formatMoney(val) {
    return window.salonFormatMoney(val || 0);
}
function escapeHtml(str) {
    return (str == null ? '' : String(str))
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
function getPaginated() {
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity) return paymentsData;
    var start = (currentPage - 1) * PAGE_SIZE;
    return paymentsData.slice(start, start + PAGE_SIZE);
}
function updatePaginationState() {
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity) { totalPages = 1; currentPage = 1; return; }
    totalPages = Math.max(1, Math.ceil(paymentsData.length / PAGE_SIZE));
    if (currentPage > totalPages) currentPage = totalPages;
    if (currentPage < 1) currentPage = 1;
}
function renderPagination() {
    var el = document.getElementById('paymentsPagination');
    if (!el) return;
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity || paymentsData.length <= PAGE_SIZE || totalPages <= 1) { el.innerHTML = ''; return; }
    var h = '<div class="flex items-center gap-2 justify-center">';
    var disabledClass = 'text-gray-400 cursor-not-allowed opacity-50';
    var activeClass = 'bg-[#003047] text-white';
    var defaultClass = 'bg-white text-gray-700 border border-gray-300 hover:border-[#003047] hover:text-[#003047]';
    h += '<button class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300 ' + (currentPage === 1 ? disabledClass : 'bg-white text-[#003047] hover:bg-gray-100 hover:border-[#003047]') + '" ' + (currentPage === 1 ? 'disabled' : '') + ' onclick="salonPaymentsGoToPage(1)" title="First page">&laquo;</button>';
    h += '<button class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300 ' + (currentPage === 1 ? disabledClass : 'bg-white text-[#003047] hover:bg-gray-100 hover:border-[#003047]') + '" ' + (currentPage === 1 ? 'disabled' : '') + ' onclick="salonPaymentsChangePage(-1)" title="Previous page">&lt;</button>';
    var maxButtons = 6, startPage = Math.max(1, currentPage - Math.floor(maxButtons / 2)), endPage = startPage + maxButtons - 1;
    if (endPage > totalPages) { endPage = totalPages; startPage = Math.max(1, endPage - maxButtons + 1); }
    for (var p = startPage; p <= endPage; p++) {
        h += '<button class="px-3 py-2 text-sm font-medium rounded-md border ' + (p === currentPage ? activeClass : defaultClass) + '" onclick="salonPaymentsGoToPage(' + p + ')">' + p + '</button>';
    }
    h += '<button class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300 ' + (currentPage === totalPages ? disabledClass : 'bg-white text-[#003047] hover:bg-gray-100 hover:border-[#003047]') + '" ' + (currentPage === totalPages ? 'disabled' : '') + ' onclick="salonPaymentsChangePage(1)" title="Next page">&gt;</button>';
    h += '<button class="px-3 py-2 text-sm font-medium rounded-md border border-gray-300 ' + (currentPage === totalPages ? disabledClass : 'bg-white text-[#003047] hover:bg-gray-100 hover:border-[#003047]') + '" ' + (currentPage === totalPages ? 'disabled' : '') + ' onclick="salonPaymentsGoToPage(' + totalPages + ')" title="Last page">&raquo;</button></div>';
    el.innerHTML = h;
}
function updateCounter() {
    var el = document.getElementById('paymentsResultsCounter');
    if (!el) return;
    var total = paymentsData.length;
    if (total === 0) { el.textContent = 'No results found'; return; }
    if (PAGE_SIZE === 'all' || PAGE_SIZE === Infinity) { el.textContent = 'Showing all ' + total + ' result' + (total !== 1 ? 's' : ''); return; }
    var start = (currentPage - 1) * PAGE_SIZE, end = Math.min(start + PAGE_SIZE, total);
    el.textContent = 'Showing ' + (start + 1) + '-' + end + ' of ' + total + ' result' + (total !== 1 ? 's' : '');
}
function renderList() {
    var tbody = document.getElementById('listViewBody');
    if (!tbody) return;
    var list = getPaginated();
    if (list.length === 0) { tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-12 text-center text-gray-500 text-sm">No payments found.</td></tr>'; return; }
    tbody.innerHTML = list.map(function(p) {
        var customerName = p.customerName || p.customer || '—';
        var customerColor = p.customerColor || 'bg-[#e6f0f3]';
        var customerTextColor = p.customerTextColor || 'text-[#003047]';
        var customerInitials = p.customerInitials || '—';
        var amount = parseFloat(p.amount || p.total || 0);
        var method = p.method || p.paymentMethod || '—';
        var methodColor = p.methodColor || 'bg-gray-100';
        var methodTextColor = p.methodTextColor || 'text-gray-700';
        var status = p.status || '—';
        var statusColor = p.statusColor || 'bg-gray-100';
        var statusTextColor = p.statusTextColor || 'text-gray-700';
        var date = p.date || formatDate(p.date);
        return '<tr class="hover:bg-gray-50"><td class="py-3 px-4"><div class="flex items-center gap-3"><div class="w-8 h-8 ' + customerColor + ' rounded-full flex items-center justify-center"><span class="text-xs font-bold ' + customerTextColor + '">' + customerInitials + '</span></div><span class="text-sm font-medium text-gray-900">' + customerName + '</span></div></td><td class="py-3 px-4 text-sm font-semibold text-gray-900">' + formatMoney(amount) + '</td><td class="py-3 px-4"><span class="px-2 py-1 ' + methodColor + ' ' + methodTextColor + ' text-xs font-medium rounded">' + method + '</span></td><td class="py-3 px-4"><span class="px-2 py-1 ' + statusColor + ' ' + statusTextColor + ' text-xs font-medium rounded">' + status + '</span></td><td class="py-3 px-4 text-sm text-gray-600">' + date + '</td><td class="py-3 px-4"><button onclick="salonPaymentsOpenReceiptModal(\'' + p.id + '\')" class="text-[#003047] hover:text-[#002535] text-sm font-medium">View Receipt</button></td></tr>';
    }).join('');
}
function renderGrid() {
    var el = document.getElementById('gridViewContainer');
    if (!el) return;
    var list = getPaginated();
    if (list.length === 0) { el.innerHTML = '<div class="col-span-full text-center py-12 text-gray-500 text-sm">No payments found.</div>'; return; }
    el.innerHTML = list.map(function(p) {
        var customerName = p.customerName || p.customer || '—';
        var customerColor = p.customerColor || 'bg-[#e6f0f3]';
        var customerTextColor = p.customerTextColor || 'text-[#003047]';
        var customerInitials = p.customerInitials || '—';
        var amount = parseFloat(p.amount || p.total || 0);
        var method = p.method || p.paymentMethod || '—';
        var methodColor = p.methodColor || 'bg-gray-100';
        var methodTextColor = p.methodTextColor || 'text-gray-700';
        var status = p.status || '—';
        var statusColor = p.statusColor || 'bg-gray-100';
        var statusTextColor = p.statusTextColor || 'text-gray-700';
        var date = p.date || formatDate(p.date);
        return '<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md transition-shadow"><div class="flex items-center gap-4 mb-4"><div class="w-12 h-12 ' + customerColor + ' rounded-full flex items-center justify-center flex-shrink-0"><span class="text-sm font-bold ' + customerTextColor + '">' + customerInitials + '</span></div><div class="flex-1 min-w-0"><h3 class="font-semibold text-gray-900 text-lg truncate">' + customerName + '</h3><p class="text-xs text-gray-500">' + p.id + '</p></div></div><div class="mb-4"><p class="text-2xl font-bold text-gray-900 mb-2">' + formatMoney(amount) + '</p><div class="flex items-center gap-2"><span class="px-2 py-1 ' + methodColor + ' ' + methodTextColor + ' text-xs font-medium rounded">' + method + '</span><span class="px-2 py-1 ' + statusColor + ' ' + statusTextColor + ' text-xs font-medium rounded">' + status + '</span></div></div><button onclick="salonPaymentsOpenReceiptModal(\'' + p.id + '\')" class="w-full px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm active:scale-95">View Receipt</button></div>';
    }).join('');
}
function salonPaymentsRender() {
    updatePaginationState();
    renderList();
    renderGrid();
    renderPagination();
    updateCounter();
}
window.salonPaymentsGoToPage = function(p) { if (p < 1 || p > totalPages || p === currentPage) return; currentPage = p; salonPaymentsRender(); };
window.salonPaymentsChangePage = function(offset) { salonPaymentsGoToPage(currentPage + offset); };
window.salonPaymentsChangePerPage = function(val) {
    PAGE_SIZE = val === 'all' ? Infinity : parseInt(val, 10);
    currentPage = 1;
    localStorage.setItem('paymentsPerPage', val);
    salonPaymentsRender();
};
window.salonPaymentsToggleView = function(view) {
    currentView = view;
    localStorage.setItem('paymentsView', view);
    setViewUI(view);
    if (view === 'grid') {
        renderGrid();
        return;
    }
    renderList();
};
function setViewUI(view) {
    var l = document.getElementById('listView');
    var g = document.getElementById('gridView');
    var lb = document.getElementById('listViewBtn');
    var gb = document.getElementById('gridViewBtn');

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
window.salonPaymentsOpenReceiptModal = function(transactionId) {
    var payment = allPayments.find(function(p) { return String(p.id) === String(transactionId); });
    if (!payment) {
        if (typeof showErrorMessage === 'function') showErrorMessage('Transaction not found');
        return;
    }
    var customerName = payment.customerName || payment.customer || '—';
    var safeCustomerName = customerName.replace(/'/g, "\\'");
    var date = payment.date || '';
    var services = Array.isArray(payment.services) ? payment.services : [];
    var servicesHtml = services.length
        ? services.map(function(s) {
            return '<div class="flex justify-between text-sm text-gray-700"><span>' + (s.name || 'Service') + (s.quantity > 1 ? ' × ' + s.quantity : '') + '</span><span>' + formatMoney(s.line_total) + '</span></div>';
        }).join('')
        : '<div class="text-sm text-gray-500">No services listed</div>';
    var statusLower = String(payment.status || '').toLowerCase();
    var isRefundedOrVoided = statusLower === 'refunded' || statusLower === 'voided';
    var refundNotes = payment.refund_notes || payment.refundNotes || '';
    var refundNotesHtml = (isRefundedOrVoided && refundNotes)
        ? '<div class="mt-3 p-4 bg-amber-50 border border-amber-200 rounded-lg">'
            + '<p class="text-xs font-semibold text-amber-900 mb-1">Refund notes</p>'
            + '<p class="text-sm text-amber-900 whitespace-pre-wrap">' + escapeHtml(refundNotes) + '</p>'
            + '</div>'
        : '';
    var refundBtnHtml = isRefundedOrVoided
        ? '<button type="button" disabled class="px-6 py-3 bg-gray-100 text-gray-400 rounded-lg font-medium cursor-not-allowed opacity-80 flex items-center gap-2" title="Already refunded">'
            + '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>'
            + 'Refunded</button>'
        : '<button type="button" id="receiptRefundBtn" data-transaction-id="' + escapeHtml(payment.id) + '" data-customer-name="' + escapeHtml(customerName) + '" data-amount="' + escapeHtml(formatMoney(payment.amount)) + '" onclick="salonPaymentsOpenRefundConfirmationModal(this.dataset.transactionId, this.dataset.customerName, this.dataset.amount)" class="px-6 py-3 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition font-medium active:scale-95 flex items-center gap-2">'
            + '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1"></path></svg>'
            + 'Refund</button>';

    var modalContent = ''
        + '<div class="max-h-[90vh] flex flex-col">'
        + '<div class="p-6 border-b border-gray-200 flex items-center justify-between">'
        + '<h3 class="text-xl font-bold text-gray-900">Receipt</h3>'
        + '<button onclick="salonPaymentsCloseModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>'
        + '</div>'
        + '<div class="space-y-4 p-6 overflow-y-auto">'
        + '<div class="text-center border-b border-gray-200 pb-4"><h4 class="font-bold text-lg text-gray-900">Nail Salon POS</h4><p class="text-sm text-gray-600">123 Main Street</p><p class="text-sm text-gray-600">(555) 123-4567</p></div>'
        + '<div class="space-y-2">'
        + '<div class="flex justify-between"><span class="text-sm text-gray-600">Transaction #:</span><span class="text-sm font-medium text-gray-900">' + payment.id + '</span></div>'
        + '<div class="flex justify-between"><span class="text-sm text-gray-600">Date:</span><span class="text-sm font-medium text-gray-900">' + date + '</span></div>'
        + '<div class="flex justify-between"><span class="text-sm text-gray-600">Customer:</span><span class="text-sm font-medium text-gray-900">' + customerName + '</span></div>'
        + '<div class="flex justify-between"><span class="text-sm text-gray-600">Method:</span><span class="text-sm font-medium text-gray-900">' + (payment.method || '—') + '</span></div>'
        + '<div class="flex justify-between"><span class="text-sm text-gray-600">Status:</span><span class="text-sm font-medium text-gray-900">' + (payment.status || '—') + '</span></div>'
        + refundNotesHtml
        + '</div>'
        + '<div class="border-t border-gray-200 pt-4"><h4 class="text-sm font-semibold text-gray-700 mb-2">Services</h4><div class="space-y-2">' + servicesHtml + '</div></div>'
        + '<div class="border-t border-gray-200 pt-4 space-y-2">'
        + '<div class="flex justify-between text-sm text-gray-700"><span>Sub Total</span><span>' + formatMoney(payment.subTotal) + '</span></div>'
        + '<div class="flex justify-between text-sm text-gray-700"><span>Discount</span><span>-' + formatMoney(payment.discount) + '</span></div>'
        + '<div class="flex justify-between text-sm text-gray-700"><span>Credits</span><span>-' + formatMoney(payment.credits) + '</span></div>'
        + '<div class="flex justify-between text-sm text-gray-700"><span>Gift Card</span><span>-' + formatMoney(payment.giftCard) + '</span></div>'
        + '<div class="flex justify-between text-sm text-gray-700"><span>Tax</span><span>' + formatMoney(payment.tax) + '</span></div>'
        + '<div class="flex justify-between text-sm text-gray-700"><span>Tip</span><span>' + formatMoney(payment.tip) + '</span></div>'
        + '<div class="flex justify-between items-center pt-2 border-t border-gray-200"><span class="text-lg font-semibold text-gray-900">Total</span><span class="text-2xl font-bold text-gray-900">' + formatMoney(payment.amount) + '</span></div>'
        + '</div>'
        + '</div>'
        + '<div class="flex justify-end gap-3 p-6 border-t border-gray-200">'
        + refundBtnHtml
        + '<button onclick="window.print()" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Print Receipt</button>'
        + '<button onclick="salonPaymentsCloseModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">Close</button>'
        + '</div>'
        + '</div>';
    if (typeof openModal === 'function') {
        openModal(modalContent);
    } else {
        console.error('openModal function not found');
    }
};
window.salonPaymentsCloseModal = function() {
    if (typeof closeModal === 'function') {
        closeModal();
    }
};
window.salonPaymentsOpenRefundConfirmationModal = function(transactionId, customerName, amount) {
    var refundModalOverlay = document.getElementById('refundModalOverlay');
    if (!refundModalOverlay) {
        refundModalOverlay = document.createElement('div');
        refundModalOverlay.id = 'refundModalOverlay';
        refundModalOverlay.className = 'fixed inset-0 z-[60] flex items-center justify-center p-2 sm:p-4 transition-opacity duration-300';
        refundModalOverlay.style.backgroundColor = 'rgba(0,0,0,0.5)';
        document.body.appendChild(refundModalOverlay);
    }

    var modalContent = ''
        + '<div class="bg-white rounded-2xl shadow-2xl max-w-md w-full transform transition-all duration-300 scale-95" onclick="event.stopPropagation()" id="refundModalContainer">'
        + '<div class="p-6">'
        + '<div class="flex items-center justify-between mb-4">'
        + '<h3 class="text-xl font-bold text-gray-900">Confirm Refund</h3>'
        + '<button onclick="salonPaymentsCloseRefundModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>'
        + '</div>'
        + '<div class="space-y-4">'
        + '<div class="bg-amber-50 border border-amber-200 rounded-lg p-4">'
        + '<div class="flex items-start gap-3">'
        + '<svg class="w-6 h-6 text-amber-700 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>'
        + '<div><p class="text-sm font-semibold text-amber-900 mb-2">Refund this transaction?</p><p class="text-sm text-amber-800">This will mark the payment as refunded.</p></div>'
        + '</div>'
        + '</div>'
        + '<div class="space-y-2 border-t border-gray-200 pt-4">'
        + '<div class="flex justify-between"><span class="text-sm text-gray-600">Transaction #:</span><span class="text-sm font-medium text-gray-900">' + transactionId + '</span></div>'
        + '<div class="flex justify-between"><span class="text-sm text-gray-600">Customer:</span><span class="text-sm font-medium text-gray-900">' + customerName + '</span></div>'
        + '<div class="flex justify-between"><span class="text-sm text-gray-600">Amount:</span><span class="text-sm font-medium text-gray-900">' + amount + '</span></div>'
        + '</div>'
        + '<div class="border-t border-gray-200 pt-4">'
        + '<label class="block text-sm font-medium text-gray-700 mb-2">Notes (optional)</label>'
        + '<textarea id="refundNotesInput" rows="3" maxlength="2000" placeholder="Add refund notes..." class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-sm"></textarea>'
        + '<p class="mt-2 text-xs text-gray-500">Notes will be saved with this payment.</p>'
        + '</div>'
        + '</div>'
        + '<div class="flex justify-end gap-3 pt-6">'
        + '<button onclick="salonPaymentsCloseRefundModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">Cancel</button>'
        + '<button type="button" id="confirmRefundBtn" data-transaction-id="' + escapeHtml(transactionId) + '" onclick="salonPaymentsRefundPayment(this.dataset.transactionId)" class="px-6 py-3 bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition font-medium active:scale-95 flex items-center gap-2">'
        + '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1"></path></svg>'
        + 'Confirm Refund</button>'
        + '</div>'
        + '</div>'
        + '</div>';

    refundModalOverlay.innerHTML = modalContent;
    refundModalOverlay.style.backgroundColor = 'rgba(0,0,0,0.5)';
    refundModalOverlay.style.backdropFilter = 'none';
    refundModalOverlay.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    setTimeout(function() {
        var container = document.getElementById('refundModalContainer');
        if (container) {
            container.classList.remove('scale-95');
            container.classList.add('scale-100');
        }
    }, 10);
    refundModalOverlay.onclick = function(e) {
        if (e.target === refundModalOverlay) {
            salonPaymentsCloseRefundModal();
        }
    };
};
window.salonPaymentsCloseRefundModal = function() {
    var refundModalOverlay = document.getElementById('refundModalOverlay');
    var refundModalContainer = document.getElementById('refundModalContainer');
    if (refundModalContainer) {
        refundModalContainer.classList.remove('scale-100');
        refundModalContainer.classList.add('scale-95');
    }
    setTimeout(function() {
        if (refundModalOverlay) {
            refundModalOverlay.classList.add('hidden');
        }
        var mainOverlay = document.getElementById('modalOverlay');
        var mainOpen = mainOverlay && mainOverlay.style.display !== 'none' && !mainOverlay.classList.contains('hidden');
        document.body.style.overflow = mainOpen ? 'hidden' : 'auto';
    }, 200);
};
window.salonPaymentsRefundPayment = function(transactionId) {
    var notesEl = document.getElementById('refundNotesInput');
    var notes = notesEl ? String(notesEl.value || '').trim() : '';
    salonPaymentsCloseRefundModal();

    var paymentIndex = allPayments.findIndex(function(p) { return String(p.id) === String(transactionId); });
    if (paymentIndex === -1) {
        if (typeof showErrorMessage === 'function') showErrorMessage('Transaction not found');
        return;
    }
    salonApi.put(apiPaymentsUrl + '/' + encodeURIComponent(transactionId), { status: 'Refunded', refund_notes: (notes || null) }).then(function() {
        allPayments[paymentIndex].status = 'Refunded';
        allPayments[paymentIndex].refund_notes = notes || '';
        allPayments[paymentIndex].statusColor = 'bg-amber-100';
        allPayments[paymentIndex].statusTextColor = 'text-amber-700';
        paymentsData = allPayments;
        salonPaymentsRender();
        salonPaymentsCloseModal();
        if (typeof showSuccessMessage === 'function') showSuccessMessage('Transaction refunded successfully');
    }).catch(function(err) {
        if (typeof showErrorMessage === 'function') showErrorMessage(err.message || 'Failed to refund transaction');
    });
};
document.addEventListener('DOMContentLoaded', function() {
    var savedPerPage = localStorage.getItem('paymentsPerPage');
    if (savedPerPage) {
        var perPageSelect = document.getElementById('perPageSelect');
        if (perPageSelect) {
            perPageSelect.value = savedPerPage;
            PAGE_SIZE = savedPerPage === 'all' ? Infinity : parseInt(savedPerPage, 10);
        }
    }
    setViewUI(currentView);
    if (window.salonPaymentsBootstrap && window.salonPaymentsBootstrap.payments) {
        allPayments = (window.salonPaymentsBootstrap.payments || []).map(function(p) {
            return Object.assign({}, p, { date: formatDate(p.date) || p.date });
        });
        paymentsData = allPayments;
        salonPaymentsRender();
        salonPaymentsToggleView(currentView);
        return;
    }
    fetch(base + '/payments').then(function(r) { return r.json(); }).then(function(data) {
        allPayments = (data.payments || []).map(function(p) { return Object.assign({}, p, { date: formatDate(p.date) || p.date }); });
        paymentsData = allPayments;
        salonPaymentsRender();
        salonPaymentsToggleView(currentView);
    }).catch(function(err) {
        console.error(err);
        if (typeof showErrorMessage === 'function') {
            showErrorMessage('Failed to load payments');
        }
        var tbody = document.getElementById('listViewBody');
        if (tbody) {
            tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-12 text-center text-red-500">Failed to load payments</td></tr>';
        }
    });
});
})();
</script>
@endpush
@endsection

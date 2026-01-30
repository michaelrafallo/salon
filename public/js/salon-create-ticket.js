(function() {
var base = window.salonJsonBase || '/json';
var ticketsUrl = window.salonTicketsUrl || '/booking/tickets';
var allServicesData = [], servicesData = [], categoriesMap = {}, selectedCategory = null;
var cart = [], techniciansData = [], assignedTechnicianIds = [], selectedTechnicianId = null;
var paymentSubtotal = 0, paymentTax = 0, paymentTip = 0, paymentDiscount = 0, paymentAmountStr = '';
var currentStep = 1, technicianTips = {}, tipSplitMode = 'percentage';
var availableTechnicians = [], technicianSearchTerm = '';
var colorClasses = [
    { bg: 'bg-[#e6f0f3]', text: 'text-[#003047]' }, { bg: 'bg-purple-100', text: 'text-purple-600' },
    { bg: 'bg-teal-100', text: 'text-teal-600' }, { bg: 'bg-indigo-100', text: 'text-indigo-600' },
    { bg: 'bg-rose-100', text: 'text-rose-600' }, { bg: 'bg-blue-100', text: 'text-blue-600' },
    { bg: 'bg-amber-100', text: 'text-amber-600' }, { bg: 'bg-green-100', text: 'text-green-600' }
];
function fetchCategoriesAndServices() {
    return Promise.all([
        fetch(base + '/service-categories.json').then(function(r) { return r.json(); }),
        fetch(base + '/services.json').then(function(r) { return r.json(); })
    ]).then(function(arr) {
        categoriesMap = arr[0].categories || {};
        allServicesData = arr[1].services || [];
        servicesData = allServicesData.filter(function(s) { return s.active !== false; });
        initializeCategoriesList();
        initializeServicesList();
    }).catch(function(err) { console.error(err); });
}
function fetchTechnicians() {
    return fetch(base + '/users.json').then(function(r) { return r.json(); }).then(function(data) {
        techniciansData = (data.users || []).filter(function(u) {
            return (u.role === 'technician' || u.userlevel === 'technician') && (u.status === 'active' || !u.status);
        });
        renderTechniciansList();
    }).catch(function(err) { console.error(err); techniciansData = []; });
}
function initializeCategoriesList() {
    var el = document.getElementById('categoriesList');
    if (!el) return;
    var sorted = Object.entries(categoriesMap).sort(function(a, b) { return (a[1] || '').localeCompare(b[1] || ''); });
    var html = '<button type="button" onclick="salonTicketFilterCategory(null)" class="category-card px-4 py-2 bg-[#e6f0f3] border border-[#003047] text-[#003047] rounded-lg hover:bg-[#e6f0f3] transition-all font-medium text-sm active:scale-95 whitespace-nowrap" data-category-key="all">All Categories</button>';
    sorted.forEach(function(entry) {
        var k = entry[0], v = entry[1];
        html += '<button type="button" onclick="salonTicketFilterCategory(\'' + k + '\')" class="category-card px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:border-[#003047] hover:bg-[#e6f0f3] hover:text-[#003047] transition-all font-medium text-sm active:scale-95 whitespace-nowrap" data-category-key="' + k + '">' + v + '</button>';
    });
    el.innerHTML = html;
}
window.salonTicketFilterCategory = function(cat) {
    selectedCategory = cat;
    document.querySelectorAll('.category-card').forEach(function(card) {
        var key = card.getAttribute('data-category-key');
        if ((cat === null && key === 'all') || cat === key) {
            card.classList.remove('bg-white', 'border-gray-200', 'text-gray-700');
            card.classList.add('bg-[#e6f0f3]', 'border-[#003047]', 'text-[#003047]');
        } else {
            card.classList.remove('bg-[#e6f0f3]', 'border-[#003047]', 'text-[#003047]');
            card.classList.add('bg-white', 'border-gray-200', 'text-gray-700');
        }
    });
    initializeServicesList();
};
function initializeServicesList() {
    var container = document.getElementById('servicesListContainer');
    if (!container) return;
    var grid = container.querySelector('.grid');
    if (!grid) return;
    var filtered = servicesData;
    if (selectedCategory !== null) {
        filtered = servicesData.filter(function(s) {
            return s.categories && s.categories.indexOf(selectedCategory) >= 0;
        });
    }
    var searchInp = document.getElementById('serviceSearchInput');
    if (searchInp && searchInp.value.trim()) {
        var term = searchInp.value.toLowerCase();
        filtered = filtered.filter(function(s) {
            return (s.name || '').toLowerCase().indexOf(term) >= 0;
        });
    }
    if (!filtered.length) {
        grid.innerHTML = '<div class="col-span-full text-center py-12"><svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><p class="text-gray-500 text-sm">No services found</p></div>';
        return;
    }
    grid.innerHTML = filtered.map(function(service, i) {
        var color = colorClasses[i % colorClasses.length];
        var serviceId = (service.name || '').replace(/\s+/g, '-').toLowerCase();
        var isInCart = selectedTechnicianId ? cart.find(function(item) {
            return item.name === service.name && item.technician_id === selectedTechnicianId;
        }) : null;
        return '<div class="service-item bg-white border border-gray-200 rounded-lg overflow-hidden hover:border-[#003047] hover:shadow-md transition-all flex flex-col h-full"><div class="w-full h-32 ' + color.bg + ' flex items-center justify-center flex-shrink-0"><svg class="w-12 h-12 ' + color.text + '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg></div><div class="p-4 flex flex-col flex-1"><div class="flex-1"><h3 class="text-sm font-semibold text-gray-900 mb-1">' + (service.name || '') + '</h3><p class="text-lg font-bold text-[#003047] mb-3">$' + (parseFloat(service.price || 0).toFixed(2)) + '</p></div><button type="button" onclick="salonTicketAddServiceToCart(\'' + (service.name || '').replace(/'/g, "\\'") + '\', ' + (service.price || 0) + ')" class="w-full px-6 py-3 ' + (!selectedTechnicianId ? 'bg-gray-400 cursor-not-allowed' : isInCart ? 'bg-green-600 hover:bg-green-700' : 'bg-[#003047] hover:bg-[#002535]') + ' text-white rounded-lg transition font-medium text-sm active:scale-95 flex items-center justify-center gap-2 mt-auto" ' + (!selectedTechnicianId ? 'disabled title="Please select a technician first"' : '') + '><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>' + (!selectedTechnicianId ? 'Select Technician First' : isInCart ? 'In Cart (' + isInCart.quantity + 'x)' : 'Add to Cart') + '</button></div></div>';
    }).join('');
}
window.salonTicketFilterServices = function(val) { initializeServicesList(); };
window.salonTicketUpdateClearButton = function(val) {
    var btn = document.getElementById('clearSearchBtn');
    if (btn) {
        if (val && val.trim()) btn.classList.remove('hidden');
        else btn.classList.add('hidden');
    }
};
window.salonTicketClearSearch = function() {
    var inp = document.getElementById('serviceSearchInput');
    if (inp) { inp.value = ''; salonTicketUpdateClearButton(''); initializeServicesList(); inp.focus(); }
};
window.salonTicketAddServiceToCart = function(name, price) {
    if (!selectedTechnicianId) { alert('Please select a technician first before adding services.'); return; }
    var existing = cart.find(function(item) { return item.name === name && item.technician_id === selectedTechnicianId; });
    if (existing) existing.quantity += 1;
    else cart.push({ name: name, price: price, quantity: 1, technician_id: selectedTechnicianId });
    initializeServicesList();
    renderTechniciansList();
    if (currentStep === 2) { renderTechniciansTipSplit(); renderCheckoutStep(); }
};
function renderTechniciansList() {
    var container = document.getElementById('techniciansListContainer');
    if (!container) return;
    var assigned = techniciansData.filter(function(t) {
        return assignedTechnicianIds.indexOf(t.id.toString()) >= 0;
    });
    if (!assigned.length) {
        container.innerHTML = '<div class="text-center py-12"><p class="text-sm text-gray-500">No assigned technicians</p></div>';
        return;
    }
    var html = '<div class="space-y-3">';
    assigned.forEach(function(tech) {
        var idStr = tech.id.toString(), isActive = selectedTechnicianId === idStr;
        var inits = tech.initials || (tech.firstName || '')[0] + (tech.lastName || '')[0];
        var name = tech.firstName + ' ' + tech.lastName;
        var techServices = cart.filter(function(item) { return item.technician_id === idStr; });
        html += '<div onclick="salonTicketSelectTechnician(\'' + idStr + '\')" class="flex flex-col gap-2 p-3 rounded-lg border-2 cursor-pointer transition ' + (isActive ? 'border-[#003047] bg-white' : 'border-gray-200 bg-white hover:bg-gray-50') + '"><div class="flex items-center gap-3"><div class="relative flex-shrink-0"><div class="w-12 h-12 bg-[#e6f0f3] rounded-full flex items-center justify-center border-2 border-white"><span class="text-sm font-bold text-[#003047]">' + inits + '</span></div><div class="absolute -bottom-1 -right-1 w-5 h-5 bg-[#003047] text-white rounded-full flex items-center justify-center text-xs font-bold border-2 border-white">✓</div></div><div class="flex-1 min-w-0"><p class="text-sm font-medium text-gray-900 truncate">' + name + '</p><p class="text-xs text-gray-500">Technician</p></div></div>';
        if (techServices.length > 0) {
            html += '<div class="mt-2 pt-2 border-t border-gray-300"><div class="space-y-2">';
            techServices.forEach(function(service) {
                html += '<div class="bg-gray-50 rounded-lg p-3 border border-gray-200"><div class="flex items-center justify-between gap-3"><div class="flex-1 min-w-0"><p class="text-sm font-semibold text-gray-900">' + service.name + '</p><p class="text-xs text-gray-500">$' + (service.price || 0).toFixed(2) + ' each</p></div><div class="flex items-center gap-2"><button onclick="event.stopPropagation(); salonTicketUpdateServiceQuantity(\'' + (service.name || '').replace(/'/g, "\\'") + '\', ' + (service.price || 0) + ', \'' + idStr + '\', ' + (service.quantity - 1) + ')" class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-200 rounded border border-gray-300 transition">-</button><span class="text-sm font-medium text-gray-900 min-w-[2rem] text-center">' + service.quantity + '</span><button onclick="event.stopPropagation(); salonTicketUpdateServiceQuantity(\'' + (service.name || '').replace(/'/g, "\\'") + '\', ' + (service.price || 0) + ', \'' + idStr + '\', ' + (service.quantity + 1) + ')" class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-200 rounded border border-gray-300 transition">+</button></div><div class="text-sm font-semibold text-gray-900 min-w-[4rem] text-right">$' + ((service.price || 0) * service.quantity).toFixed(2) + '</div><button onclick="event.stopPropagation(); salonTicketRemoveServiceFromCart(\'' + (service.name || '').replace(/'/g, "\\'") + '\', \'' + idStr + '\')" class="w-8 h-8 flex items-center justify-center text-red-500 hover:bg-red-50 rounded transition">×</button></div></div>';
            });
            html += '</div></div>';
        } else {
            html += '<div class="mt-2 pt-2 border-t border-gray-300"><p class="text-xs text-gray-500 italic">No services assigned</p></div>';
        }
        html += '</div>';
    });
    html += '</div>';
    container.innerHTML = html;
}
window.salonTicketSelectTechnician = function(id) {
    selectedTechnicianId = id;
    renderTechniciansList();
    initializeServicesList();
};
window.salonTicketUpdateServiceQuantity = function(name, price, techId, newQty) {
    if (newQty <= 0) { salonTicketRemoveServiceFromCart(name, techId); return; }
    var idx = cart.findIndex(function(item) { return item.name === name && item.technician_id === techId; });
    if (idx >= 0) {
        cart[idx].quantity = newQty;
        renderTechniciansList();
        initializeServicesList();
        if (currentStep === 2) { renderTechniciansTipSplit(); renderCheckoutStep(); }
    }
};
window.salonTicketRemoveServiceFromCart = function(name, techId) {
    var idx = cart.findIndex(function(item) { return item.name === name && item.technician_id === techId; });
    if (idx >= 0) {
        cart.splice(idx, 1);
        renderTechniciansList();
        initializeServicesList();
        if (currentStep === 2) { renderTechniciansTipSplit(); renderCheckoutStep(); }
    }
};
window.salonTicketOpenTechnicianModal = function() {
    technicianSearchTerm = '';
    var content = '<div class="p-6"><div class="flex items-center justify-between mb-6"><h3 class="text-xl font-bold text-gray-900">Select Technicians</h3><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><div class="grid grid-cols-2 gap-6"><div class="border border-gray-200 rounded-lg p-4"><div class="flex items-center justify-between mb-2"><h4 class="text-sm font-semibold text-gray-900">Available Technicians</h4><span id="availableCount" class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">0</span></div><p class="text-xs text-gray-500 mb-2">Click to assign technicians</p><div class="mb-4"><div class="relative"><svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg><input type="text" id="technicianSearchInput" placeholder="Search technicians..." oninput="salonTicketSearchTechnicians(this.value)" class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-sm"><button id="clearTechnicianSearchBtn" onclick="salonTicketClearTechnicianSearch()" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden">×</button></div></div><div id="availableTechniciansContainer" class="space-y-3 min-h-[500px] max-h-[500px] overflow-y-auto"></div></div><div class="border border-gray-200 rounded-lg p-4"><div class="flex items-center justify-between mb-2"><h4 class="text-sm font-semibold text-gray-900">Assigned Technicians</h4><span id="assignedCount" class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">0</span></div><p class="text-xs text-gray-500 mb-4">Click to remove</p><div id="assignedTechniciansContainer" class="space-y-3 min-h-[500px] max-h-[500px] overflow-y-auto"></div></div></div><div class="pt-6 mt-6 border-t border-gray-200"><div class="flex items-center justify-end"><div class="flex gap-3"><button onclick="closeModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium">Cancel</button><button onclick="salonTicketConfirmTechnicianSelection()" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium">Save Changes</button></div></div></div></div>';
    openModal(content, 'large', false);
    setTimeout(function() {
        var modal = document.getElementById('modalContainer');
        if (modal) modal.style.maxHeight = '95vh';
    }, 50);
    loadTechniciansForSelection();
};
function loadTechniciansForSelection() {
    fetch(base + '/users.json').then(function(r) { return r.json(); }).then(function(data) {
        availableTechnicians = (data.users || []).filter(function(u) {
            return (u.role === 'technician' || u.userlevel === 'technician') && (u.status === 'active' || !u.status);
        });
        renderAvailableTechnicians();
        renderAssignedTechnicians();
        updateTechnicianCounts();
    }).catch(function(err) { console.error(err); });
}
window.salonTicketSearchTechnicians = function(val) {
    technicianSearchTerm = (val || '').toLowerCase().trim();
    var btn = document.getElementById('clearTechnicianSearchBtn');
    if (btn) {
        if (val.trim()) btn.classList.remove('hidden');
        else btn.classList.add('hidden');
    }
    renderAvailableTechnicians();
};
window.salonTicketClearTechnicianSearch = function() {
    var inp = document.getElementById('technicianSearchInput');
    var btn = document.getElementById('clearTechnicianSearchBtn');
    if (inp) { inp.value = ''; technicianSearchTerm = ''; inp.focus(); }
    if (btn) btn.classList.add('hidden');
    renderAvailableTechnicians();
};
function renderAvailableTechnicians() {
    var container = document.getElementById('availableTechniciansContainer');
    if (!container) return;
    if (!availableTechnicians.length) {
        container.innerHTML = '<div class="flex items-center justify-center h-full min-h-[500px]"><p class="text-sm text-gray-400">No technicians available</p></div>';
        return;
    }
    var filtered = technicianSearchTerm ? availableTechnicians.filter(function(t) {
        var name = (t.firstName + ' ' + t.lastName).toLowerCase();
        var inits = (t.initials || (t.firstName || '')[0] + (t.lastName || '')[0]).toLowerCase();
        return (name + ' ' + inits).indexOf(technicianSearchTerm) >= 0;
    }) : availableTechnicians;
    if (!filtered.length) {
        container.innerHTML = '<div class="flex items-center justify-center h-full min-h-[500px]"><p class="text-sm text-gray-400">No technicians found</p></div>';
        return;
    }
    container.innerHTML = filtered.map(function(tech) {
        var idStr = tech.id.toString(), isAssigned = assignedTechnicianIds.indexOf(idStr) >= 0;
        var inits = tech.initials || (tech.firstName || '')[0] + (tech.lastName || '')[0];
        var name = tech.firstName + ' ' + tech.lastName;
        var cls = isAssigned ? 'opacity-50 grayscale cursor-pointer group hover:bg-gray-100' : 'cursor-pointer group hover:bg-gray-50';
        return '<div onclick="' + (isAssigned ? 'salonTicketRemoveAssignedTechnician(' + tech.id + ')' : 'salonTicketAssignTechnician(' + tech.id + ')') + '" class="flex items-center gap-3 p-2 rounded-lg transition-colors ' + cls + '"><div class="relative flex-shrink-0"><div class="w-12 h-12 ' + (isAssigned ? 'bg-gray-300' : 'bg-gray-200') + ' rounded-full flex items-center justify-center"><span class="text-sm font-bold ' + (isAssigned ? 'text-gray-500' : 'text-gray-600') + '">' + inits + '</span></div><div class="absolute -bottom-1 -right-1 w-5 h-5 ' + (isAssigned ? 'bg-gray-400' : 'bg-[#003047]') + ' text-white rounded-full flex items-center justify-center text-xs font-bold border-2 border-white">0</div></div><div class="flex-1"><p class="text-base font-medium ' + (isAssigned ? 'text-gray-400' : 'text-gray-900') + '">' + name + '</p></div></div>';
    }).join('');
}
function renderAssignedTechnicians() {
    var container = document.getElementById('assignedTechniciansContainer');
    if (!container) return;
    if (!assignedTechnicianIds.length) {
        container.innerHTML = '<div class="flex items-center justify-center h-full min-h-[500px]"><p class="text-sm text-gray-400">No technicians assigned</p></div>';
        return;
    }
    container.innerHTML = assignedTechnicianIds.map(function(idStr) {
        var tech = availableTechnicians.find(function(t) { return t.id.toString() === idStr; });
        if (!tech) return '';
        var inits = tech.initials || (tech.firstName || '')[0] + (tech.lastName || '')[0];
        var name = tech.firstName + ' ' + tech.lastName;
        return '<div onclick="salonTicketRemoveAssignedTechnician(' + tech.id + ')" class="flex items-center gap-3 cursor-pointer group hover:bg-gray-50 p-2 rounded-lg transition-colors"><div class="relative flex-shrink-0"><div class="w-12 h-12 bg-[#003047] rounded-full flex items-center justify-center"><span class="text-sm font-bold text-white">' + inits + '</span></div><div class="absolute -bottom-1 -right-1 w-5 h-5 bg-[#003047] text-white rounded-full flex items-center justify-center text-xs font-bold border-2 border-white">0</div></div><div class="flex-1"><p class="text-base font-medium text-gray-900">' + name + '</p></div></div>';
    }).join('');
}
window.salonTicketAssignTechnician = function(techId) {
    var idStr = techId.toString();
    if (assignedTechnicianIds.indexOf(idStr) < 0) {
        assignedTechnicianIds.push(idStr);
        renderAvailableTechnicians();
        renderAssignedTechnicians();
        updateTechnicianCounts();
    }
};
window.salonTicketRemoveAssignedTechnician = function(techId) {
    assignedTechnicianIds = assignedTechnicianIds.filter(function(id) { return id !== techId.toString(); });
    renderAvailableTechnicians();
    renderAssignedTechnicians();
    updateTechnicianCounts();
};
function updateTechnicianCounts() {
    var availEl = document.getElementById('availableCount'), assignEl = document.getElementById('assignedCount');
    var count = technicianSearchTerm ? availableTechnicians.filter(function(t) {
        var name = (t.firstName + ' ' + t.lastName).toLowerCase();
        var inits = (t.initials || (t.firstName || '')[0] + (t.lastName || '')[0]).toLowerCase();
        return (name + ' ' + inits).indexOf(technicianSearchTerm) >= 0;
    }).length : availableTechnicians.length;
    if (availEl) availEl.textContent = count.toString();
    if (assignEl) assignEl.textContent = assignedTechnicianIds.length.toString();
}
window.salonTicketConfirmTechnicianSelection = function() {
    if (assignedTechnicianIds.length > 0) selectedTechnicianId = assignedTechnicianIds[0];
    closeModal();
    if (techniciansData && techniciansData.length > 0) renderTechniciansList();
    else fetchTechnicians().then(function() { renderTechniciansList(); });
};
window.salonTicketSwitchStep = function(step) {
    if (step === 2 && cart.length === 0) { alert('Please add at least one service to your cart before checkout.'); return; }
    currentStep = step;
    var step1 = document.getElementById('step1Content'), step2 = document.getElementById('step2Content');
    var tab1 = document.getElementById('step1Tab'), tab2 = document.getElementById('step2Tab');
    if (step === 1) {
        step1.classList.remove('hidden');
        step2.classList.add('hidden');
        if (tab1) { tab1.classList.add('border-[#003047]'); tab1.classList.remove('border-transparent'); }
        if (tab2) { tab2.classList.remove('border-[#003047]'); tab2.classList.add('border-transparent'); }
    } else {
        step1.classList.add('hidden');
        step2.classList.remove('hidden');
        if (tab2) { tab2.classList.add('border-[#003047]'); tab2.classList.remove('border-transparent'); }
        if (tab1) { tab1.classList.remove('border-[#003047]'); tab1.classList.add('border-transparent'); }
        renderCheckoutStep();
    }
};
function renderCheckoutStep() {
    paymentSubtotal = cart.reduce(function(sum, item) { return sum + ((item.price || 0) * (item.quantity || 1)); }, 0);
    paymentTip = 0;
    paymentDiscount = 0;
    var discounted = paymentSubtotal - paymentDiscount;
    paymentTax = discounted * 0.05;
    var container = document.getElementById('checkoutItemsList');
    if (container) {
        container.innerHTML = cart.map(function(item) {
            return '<div class="grid grid-cols-12 gap-4"><div class="col-span-6"><span class="text-sm text-gray-900">' + (item.name || '') + '</span></div><div class="col-span-3 text-center"><span class="text-sm text-gray-900">' + (item.quantity || 1) + '</span></div><div class="col-span-3 text-right"><span class="text-sm font-semibold text-gray-900">$' + (((item.price || 0) * (item.quantity || 1)).toFixed(2)) + '</span></div></div>';
        }).join('');
    }
    var subtotalEl = document.getElementById('checkoutSubtotalDisplay');
    var taxEl = document.getElementById('checkoutTaxDisplay');
    var discountEl = document.getElementById('checkoutDiscountDisplay');
    var tipEl = document.getElementById('checkoutTipDisplay');
    var totalEl = document.getElementById('checkoutTotalDisplay');
    if (subtotalEl) subtotalEl.textContent = '$' + paymentSubtotal.toFixed(2);
    if (taxEl) taxEl.textContent = '$' + paymentTax.toFixed(2);
    if (discountEl) discountEl.textContent = '-$' + paymentDiscount.toFixed(2);
    if (tipEl) tipEl.textContent = '$' + paymentTip.toFixed(2);
    paymentAmountStr = '';
    updatePaymentDisplay();
    renderTechniciansTipSplit();
    updatePaymentTotal();
    setTimeout(function() {
        salonTicketSwitchTipMode(tipSplitMode);
        var cash = document.getElementById('cashPaymentContent');
        var card = document.getElementById('cardPaymentContent');
        if (cash) cash.classList.remove('hidden');
        if (card) card.classList.add('hidden');
    }, 100);
}
function renderTechniciansTipSplit() {
    var assigned = techniciansData.filter(function(t) {
        return assignedTechnicianIds.indexOf(t.id.toString()) >= 0;
    });
    if (!assigned.length) return;
    var totalTip = calculateTotalTipSplit();
    var listEl = document.getElementById('techniciansTipList');
    var listEvenEl = document.getElementById('techniciansTipListEven');
    var listCustomEl = document.getElementById('techniciansTipListCustom');
    var html = assigned.map(function(tech) {
        var idStr = tech.id.toString();
        var tip = technicianTips[idStr] || { percentage: 0, amount: 0 };
        var name = tech.firstName + ' ' + tech.lastName;
        return '<div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg"><div class="flex-1"><p class="text-sm font-medium text-gray-900">' + name + '</p><p class="text-xs text-gray-500">' + (tip.percentage || 0).toFixed(1) + '%</p></div><div class="text-sm font-semibold text-gray-900">$' + (tip.amount || 0).toFixed(2) + '</div></div>';
    }).join('');
    if (listEl) listEl.innerHTML = html;
    if (listEvenEl) listEvenEl.innerHTML = html;
    if (listCustomEl) listCustomEl.innerHTML = html;
    var totalEl = document.getElementById('totalTipSplitDisplay');
    var totalEvenEl = document.getElementById('totalTipSplitDisplayEven');
    var totalCustomEl = document.getElementById('totalTipSplitDisplayCustom');
    if (totalEl) totalEl.textContent = '$' + totalTip.toFixed(2);
    if (totalEvenEl) totalEvenEl.textContent = '$' + totalTip.toFixed(2);
    if (totalCustomEl) totalCustomEl.textContent = '$' + totalTip.toFixed(2);
}
function calculateTotalTipSplit() {
    return Object.values(technicianTips).reduce(function(sum, tip) { return sum + (tip.amount || 0); }, 0);
}
window.salonTicketSetTipPercentage = function(pct) {
    var tipAmount = paymentSubtotal * (pct / 100);
    paymentTip = tipAmount;
    var inp = document.getElementById('tipInput');
    var inpEven = document.getElementById('tipInputEven');
    if (inp) inp.value = tipAmount.toFixed(2);
    if (inpEven) inpEven.value = tipAmount.toFixed(2);
    updatePaymentTotal();
    renderTechniciansTipSplit();
};
window.salonTicketUpdateTipFromInput = function() {
    var inp = document.getElementById('tipInput');
    var inpEven = document.getElementById('tipInputEven');
    var val = parseFloat(inp ? inp.value : (inpEven ? inpEven.value : 0)) || 0;
    if (inp) inp.value = val.toFixed(2);
    if (inpEven) inpEven.value = val.toFixed(2);
    paymentTip = val;
    updatePaymentTotal();
    renderTechniciansTipSplit();
};
window.salonTicketSwitchTipMode = function(mode) {
    tipSplitMode = mode;
    var pctTab = document.getElementById('tipPercentageTab');
    var evenTab = document.getElementById('tipEvenTab');
    var customTab = document.getElementById('tipCustomTab');
    var pctContent = document.getElementById('percentageTabContent');
    var evenContent = document.getElementById('evenTabContent');
    var customContent = document.getElementById('customTabContent');
    if (mode === 'percentage') {
        if (pctTab) { pctTab.classList.add('border-[#003047]', 'text-gray-700'); pctTab.classList.remove('border-transparent', 'text-gray-500'); }
        if (evenTab) { evenTab.classList.remove('border-[#003047]', 'text-gray-700'); evenTab.classList.add('border-transparent', 'text-gray-500'); }
        if (customTab) { customTab.classList.remove('border-[#003047]', 'text-gray-700'); customTab.classList.add('border-transparent', 'text-gray-500'); }
        if (pctContent) pctContent.classList.remove('hidden');
        if (evenContent) evenContent.classList.add('hidden');
        if (customContent) customContent.classList.add('hidden');
    } else if (mode === 'even') {
        if (evenTab) { evenTab.classList.add('border-[#003047]', 'text-gray-700'); evenTab.classList.remove('border-transparent', 'text-gray-500'); }
        if (pctTab) { pctTab.classList.remove('border-[#003047]', 'text-gray-700'); pctTab.classList.add('border-transparent', 'text-gray-500'); }
        if (customTab) { customTab.classList.remove('border-[#003047]', 'text-gray-700'); customTab.classList.add('border-transparent', 'text-gray-500'); }
        if (evenContent) evenContent.classList.remove('hidden');
        if (pctContent) pctContent.classList.add('hidden');
        if (customContent) customContent.classList.add('hidden');
        splitTipEvenly();
    } else {
        if (customTab) { customTab.classList.add('border-[#003047]', 'text-gray-700'); customTab.classList.remove('border-transparent', 'text-gray-500'); }
        if (pctTab) { pctTab.classList.remove('border-[#003047]', 'text-gray-700'); pctTab.classList.add('border-transparent', 'text-gray-500'); }
        if (evenTab) { evenTab.classList.remove('border-[#003047]', 'text-gray-700'); evenTab.classList.add('border-transparent', 'text-gray-500'); }
        if (customContent) customContent.classList.remove('hidden');
        if (pctContent) pctContent.classList.add('hidden');
        if (evenContent) evenContent.classList.add('hidden');
    }
};
function splitTipEvenly() {
    if (!assignedTechnicianIds.length) return;
    var assigned = techniciansData.filter(function(t) {
        return assignedTechnicianIds.indexOf(t.id.toString()) >= 0;
    });
    if (!assigned.length) return;
    var evenPct = 100 / assigned.length;
    var evenAmount = paymentTip / assigned.length;
    assigned.forEach(function(tech) {
        var idStr = tech.id.toString();
        technicianTips[idStr] = { percentage: evenPct, amount: evenAmount };
    });
    renderTechniciansTipSplit();
}
window.salonTicketSelectPaymentMethod = function(method) {
    var cashBtn = document.getElementById('paymentMethodCashBtn');
    var cardBtn = document.getElementById('paymentMethodCardBtn');
    var cashContent = document.getElementById('cashPaymentContent');
    var cardContent = document.getElementById('cardPaymentContent');
    var cashRadio = document.getElementById('paymentMethodCash');
    var cardRadio = document.getElementById('paymentMethodCard');
    if (method === 'cash') {
        if (cashBtn) { cashBtn.classList.add('border-[#003047]', 'bg-[#e6f0f3]'); cashBtn.classList.remove('border-gray-200', 'bg-white'); }
        if (cardBtn) { cardBtn.classList.remove('border-[#003047]', 'bg-[#e6f0f3]'); cardBtn.classList.add('border-gray-200', 'bg-white'); }
        if (cashContent) cashContent.classList.remove('hidden');
        if (cardContent) cardContent.classList.add('hidden');
        if (cashRadio) cashRadio.checked = true;
    } else {
        if (cardBtn) { cardBtn.classList.add('border-[#003047]', 'bg-[#e6f0f3]'); cardBtn.classList.remove('border-gray-200', 'bg-white'); }
        if (cashBtn) { cashBtn.classList.remove('border-[#003047]', 'bg-[#e6f0f3]'); cashBtn.classList.add('border-gray-200', 'bg-white'); }
        if (cardContent) cardContent.classList.remove('hidden');
        if (cashContent) cashContent.classList.add('hidden');
        if (cardRadio) cardRadio.checked = true;
    }
};
window.salonTicketAddPaymentAmount = function(amt) {
    var current = parseFloat(paymentAmountStr || '0');
    paymentAmountStr = (current + amt).toFixed(2);
    updatePaymentDisplay();
};
window.salonTicketAddPaymentDigit = function(digit) {
    if (digit === '.') {
        if (paymentAmountStr.indexOf('.') >= 0) return;
        paymentAmountStr = (paymentAmountStr || '0') + '.';
    } else {
        paymentAmountStr = (paymentAmountStr || '0') + digit;
    }
    updatePaymentDisplay();
};
window.salonTicketRemovePaymentDigit = function() {
    paymentAmountStr = (paymentAmountStr || '0').slice(0, -1);
    if (!paymentAmountStr || paymentAmountStr === '0') paymentAmountStr = '0';
    updatePaymentDisplay();
};
function updatePaymentDisplay() {
    var el = document.getElementById('paymentAmount');
    var valEl = document.getElementById('paymentAmountValue');
    var val = parseFloat(paymentAmountStr || '0');
    if (el) el.textContent = '$' + val.toFixed(2);
    if (valEl) valEl.value = val.toFixed(2);
}
function updatePaymentTotal() {
    var discounted = paymentSubtotal - paymentDiscount;
    paymentTax = discounted * 0.05;
    var totalTip = calculateTotalTipSplit();
    var total = discounted + paymentTax + totalTip;
    var tipEl = document.getElementById('checkoutTipDisplay');
    var totalEl = document.getElementById('checkoutTotalDisplay');
    if (tipEl) tipEl.textContent = '$' + totalTip.toFixed(2);
    if (totalEl) totalEl.textContent = '$' + total.toFixed(2);
}
window.salonTicketOpenDiscountModal = function() {
    var content = '<div class="p-6"><div class="flex items-center justify-between mb-4"><h3 class="text-xl font-bold text-gray-900">Discount</h3><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">×</button></div><div class="space-y-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">Discount</label><div class="grid grid-cols-5 gap-2 mb-2"><button type="button" onclick="salonTicketSetDiscountPercentage(5)" class="py-2 text-xs font-medium text-gray-700 bg-[#e6f0f3] rounded-lg hover:bg-[#b3d1d9] transition active:scale-95 border border-[#b3d1d9]">5%</button><button type="button" onclick="salonTicketSetDiscountPercentage(10)" class="py-2 text-xs font-medium text-gray-700 bg-[#e6f0f3] rounded-lg hover:bg-[#b3d1d9] transition active:scale-95 border border-[#b3d1d9]">10%</button><button type="button" onclick="salonTicketSetDiscountPercentage(15)" class="py-2 text-xs font-medium text-gray-700 bg-[#e6f0f3] rounded-lg hover:bg-[#b3d1d9] transition active:scale-95 border border-[#b3d1d9]">15%</button><button type="button" onclick="salonTicketSetDiscountPercentage(20)" class="py-2 text-xs font-medium text-gray-700 bg-[#e6f0f3] rounded-lg hover:bg-[#b3d1d9] transition active:scale-95 border border-[#b3d1d9]">20%</button></div><input type="number" name="discount" id="discountInputModal" step="0.01" min="0" value="' + paymentDiscount.toFixed(2) + '" oninput="salonTicketUpdateDiscountFromInput()" placeholder="0.00" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047]" /></div><div class="flex gap-3 justify-end"><button type="button" onclick="closeModal()" class="px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">Cancel</button><button type="button" onclick="closeModal()" class="px-6 py-3 text-sm font-medium text-white bg-[#003047] rounded-lg hover:bg-[#002535] transition">Apply</button></div></div></div>';
    openModal(content, 'default', false);
};
window.salonTicketSetDiscountPercentage = function(pct) {
    paymentDiscount = paymentSubtotal * (pct / 100);
    var inp = document.getElementById('discountInputModal');
    if (inp) inp.value = paymentDiscount.toFixed(2);
    updatePaymentTotal();
};
window.salonTicketUpdateDiscountFromInput = function() {
    var inp = document.getElementById('discountInputModal');
    if (inp) {
        paymentDiscount = parseFloat(inp.value) || 0;
        updatePaymentTotal();
    }
};
window.salonTicketFormatCardNumber = function(input) {
    var val = input.value.replace(/\D/g, '');
    if (val.length > 16) val = val.substring(0, 16);
    var formatted = '';
    for (var i = 0; i < val.length; i++) {
        if (i > 0 && i % 4 === 0) formatted += ' ';
        formatted += val[i];
    }
    input.value = formatted;
};
window.salonTicketProcessPayment = function(e) {
    e.preventDefault();
    var totalTip = calculateTotalTipSplit();
    var discounted = paymentSubtotal - paymentDiscount;
    paymentTax = discounted * 0.05;
    var total = discounted + paymentTax + totalTip;
    var paymentAmount = parseFloat(paymentAmountStr || '0');
    var method = document.getElementById('paymentMethodCash') && document.getElementById('paymentMethodCash').checked ? 'cash' : 'card';
    if (method === 'cash' && paymentAmount < total) {
        alert('Payment amount is less than total. Please enter the correct amount.');
        return;
    }
    console.log('Processing payment:', { total, paymentAmount, method, cart, technicianTips });
    showSuccessMessage('Payment processed successfully!');
    setTimeout(function() { window.location.href = ticketsUrl; }, 1500);
};
document.addEventListener('DOMContentLoaded', function() {
    Promise.all([fetchCategoriesAndServices(), fetchTechnicians()]).then(function() {
        if (assignedTechnicianIds.length > 0) {
            selectedTechnicianId = assignedTechnicianIds[0];
            renderTechniciansList();
        }
    });
});
})();

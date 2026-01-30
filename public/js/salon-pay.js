(function() {
var base = window.salonJsonBase || '/json';
var ticketsUrl = window.salonTicketsUrl || '/booking/tickets';
var urlParams = new URLSearchParams(window.location.search);
var appointmentId = urlParams.get('id');
var appointmentData = null, customerData = null;
var allServicesData = [], servicesData = [], categoriesMap = {}, selectedCategory = null;
var cart = [], techniciansData = [], assignedTechnicianIds = [], selectedTechnicianId = null;
var paymentSubtotal = 0, paymentTax = 0, paymentTip = 0, paymentDiscount = 0, paymentAmountStr = '';
var currentStep = 1, technicianTips = {}, tipSplitMode = 'percentage';
var colorClasses = [
    { bg: 'bg-[#e6f0f3]', text: 'text-[#003047]' }, { bg: 'bg-purple-100', text: 'text-purple-600' },
    { bg: 'bg-teal-100', text: 'text-teal-600' }, { bg: 'bg-indigo-100', text: 'text-indigo-600' },
    { bg: 'bg-rose-100', text: 'text-rose-600' }, { bg: 'bg-blue-100', text: 'text-blue-600' },
    { bg: 'bg-amber-100', text: 'text-amber-600' }, { bg: 'bg-green-100', text: 'text-green-600' }
];
async function salonPayLoadPaymentData() {
    if (!appointmentId) {
        salonPayShowError('No appointment ID provided');
        return;
    }
    try {
        var aptRes = await fetch(base + '/appointments.json');
        var custRes = await fetch(base + '/customers.json');
        var aptData = await aptRes.json();
        var custData = await custRes.json();
        appointmentData = aptData.appointments.find(function(apt) {
            return apt.id.toString() === appointmentId || apt.id === parseInt(appointmentId, 10);
        });
        if (!appointmentData) {
            salonPayShowError('Appointment not found');
            return;
        }
        customerData = custData.customers.find(function(c) { return c.id === appointmentData.customer_id; });
        if (!customerData) {
            salonPayShowError('Customer not found');
            return;
        }
        if (appointmentData.assigned_technician && Array.isArray(appointmentData.assigned_technician)) {
            assignedTechnicianIds = appointmentData.assigned_technician.map(function(id) { return id.toString(); });
            if (assignedTechnicianIds.length > 0) selectedTechnicianId = assignedTechnicianIds[0];
        }
        await salonPayFetchCategoriesAndServices();
        await salonPayFetchTechnicians();
        salonPayUpdateCustomerInfoHeader();
    } catch (err) {
        console.error('Error loading payment data:', err);
        salonPayShowError('Failed to load payment details');
    }
}
function salonPayUpdateCustomerInfoHeader() {
    if (!customerData || !appointmentData) return;
    var customerName = customerData.firstName + ' ' + customerData.lastName;
    var customerInitial = customerName.split(' ').map(function(n) { return n[0]; }).join('').substring(0, 2).toUpperCase();
    var orderId = 'ORDER' + appointmentData.id.toString().padStart(3, '0');
    var appointmentType = appointmentData.appointment === 'walk-in' ? 'Walk-In' : 'Booked';
    var date = new Date(appointmentData.created_at);
    var dateStr = date.toLocaleDateString('en-US', { weekday: 'short', year: 'numeric', month: 'long', day: 'numeric' });
    var timeStr = date.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', hour12: true });
    var container = document.getElementById('customerInfoContainer');
    if (container) {
        container.innerHTML = '<div class="w-12 h-12 bg-[#e6f0f3] rounded-lg flex items-center justify-center flex-shrink-0"><span class="text-lg font-semibold text-[#003047]">' + customerInitial + '</span></div><div class="flex-1"><h2 class="text-xl font-bold text-gray-900">' + customerName + '</h2><p class="text-sm text-gray-600">' + orderId + ' / ' + appointmentType + '</p><p class="text-xs text-gray-500">' + dateStr + ' ' + timeStr + '</p></div>';
    }
}
function salonPayShowError(message) {
    var container = document.querySelector('main .p-4, main .p-6, main .p-8');
    if (container) {
        container.innerHTML = '<div class="text-center py-12"><svg class="w-16 h-16 text-red-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><p class="text-red-500 text-sm mb-4">' + message + '</p><a href="' + ticketsUrl + '" class="inline-block px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition">Back to Tickets</a></div>';
    }
}
async function salonPayFetchCategoriesAndServices() {
    try {
        var catRes = await fetch(base + '/service-categories.json');
        var svcRes = await fetch(base + '/services.json');
        var catData = await catRes.json();
        var svcData = await svcRes.json();
        categoriesMap = catData.categories || {};
        allServicesData = svcData.services || [];
        servicesData = allServicesData.filter(function(s) { return s.active !== false; });
        salonPayInitializeCategoriesList();
        salonPayInitializeServicesList();
    } catch (err) {
        console.error('Error fetching categories and services:', err);
    }
}
async function salonPayFetchTechnicians() {
    try {
        var res = await fetch(base + '/users.json');
        var data = await res.json();
        techniciansData = (data.users || []).filter(function(u) {
            return (u.role === 'technician' || u.userlevel === 'technician') && (u.status === 'active' || !u.status);
        });
        salonPayRenderTechniciansList();
    } catch (err) {
        console.error('Error fetching technicians:', err);
        techniciansData = [];
        salonPayRenderTechniciansList();
    }
}
function salonPayInitializeCategoriesList() {
    var list = document.getElementById('categoriesList');
    if (!list) return;
    var html = '<button type="button" onclick="salonPayFilterByCategory(null)" class="category-card px-4 py-2 bg-[#e6f0f3] border border-[#003047] text-[#003047] rounded-lg hover:bg-[#e6f0f3] hover:text-[#003047] transition-all duration-200 font-medium text-sm shadow-sm active:scale-95 whitespace-nowrap" data-category-key="all">All Categories</button>';
    var sorted = Object.entries(categoriesMap).sort(function(a, b) { return a[1].localeCompare(b[1]); });
    sorted.forEach(function(entry) {
        var key = entry[0], displayName = entry[1];
        html += '<button type="button" onclick="salonPayFilterByCategory(\'' + key + '\')" class="category-card px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:border-[#003047] hover:bg-[#e6f0f3] hover:text-[#003047] transition-all duration-200 font-medium text-sm active:scale-95 whitespace-nowrap" data-category-key="' + key + '">' + displayName + '</button>';
    });
    list.innerHTML = html;
}
window.salonPayFilterByCategory = function(categoryKey) {
    selectedCategory = categoryKey;
    document.querySelectorAll('.category-card').forEach(function(card) {
        var cardKey = card.getAttribute('data-category-key');
        if (categoryKey === null && cardKey === 'all') {
            card.classList.remove('bg-white', 'border-gray-200', 'text-gray-700');
            card.classList.add('bg-[#e6f0f3]', 'border-[#003047]', 'text-[#003047]', 'hover:bg-[#e6f0f3]', 'hover:text-[#003047]');
        } else if (categoryKey === cardKey) {
            card.classList.remove('bg-white', 'border-gray-200', 'text-gray-700');
            card.classList.add('bg-[#e6f0f3]', 'border-[#003047]', 'text-[#003047]', 'hover:bg-[#e6f0f3]', 'hover:text-[#003047]');
        } else {
            card.classList.remove('bg-[#e6f0f3]', 'border-[#003047]', 'text-[#003047]', 'hover:bg-[#e6f0f3]', 'hover:text-[#003047]');
            card.classList.add('bg-white', 'border-gray-200', 'text-gray-700', 'hover:text-[#003047]');
        }
    });
    salonPayInitializeServicesList();
};
function salonPayInitializeServicesList() {
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
    var searchInput = document.getElementById('serviceSearchInput');
    if (searchInput && searchInput.value.trim() !== '') {
        var term = searchInput.value.toLowerCase();
        filtered = filtered.filter(function(s) {
            return s.name.toLowerCase().indexOf(term) >= 0;
        });
    }
    var html = '';
    if (filtered.length === 0) {
        html = '<div class="col-span-full text-center py-12"><svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><p class="text-gray-500 text-sm">No services found</p></div>';
    } else {
        filtered.forEach(function(service, index) {
            var serviceId = service.name.replace(/\s+/g, '-').toLowerCase();
            var color = colorClasses[index % colorClasses.length];
            var isInCart = selectedTechnicianId ? cart.find(function(item) {
                return item.name === service.name && item.technician_id === selectedTechnicianId;
            }) : null;
            html += '<div class="service-item bg-white border border-gray-200 rounded-lg overflow-hidden hover:border-[#003047] hover:shadow-md transition-all flex flex-col h-full"><div class="w-full h-32 ' + color.bg + ' flex items-center justify-center flex-shrink-0"><svg class="w-12 h-12 ' + color.text + '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg></div><div class="p-4 flex flex-col flex-1"><div class="flex-1"><h3 class="text-sm font-semibold text-gray-900 mb-1">' + service.name + '</h3><p class="text-lg font-bold text-[#003047] mb-3">$' + service.price.toFixed(2) + '</p></div><button type="button" onclick="salonPayAddServiceToCart(\'' + service.name.replace(/'/g, "\\'") + '\', ' + service.price + ')" class="w-full px-6 py-3 ' + (!selectedTechnicianId ? 'bg-gray-400 cursor-not-allowed' : isInCart ? 'bg-green-600 hover:bg-green-700' : 'bg-[#003047] hover:bg-[#002535]') + ' text-white rounded-lg transition font-medium text-sm active:scale-95 flex items-center justify-center gap-2 mt-auto" ' + (!selectedTechnicianId ? 'disabled title="Please select a technician first"' : '') + '><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>' + (!selectedTechnicianId ? 'Select Technician First' : isInCart ? 'In Cart (' + isInCart.quantity + 'x)' : 'Add to Cart') + '</button></div></div>';
        });
    }
    grid.innerHTML = html;
}
window.salonPayFilterServices = function(term) {
    salonPayInitializeServicesList();
};
window.salonPayUpdateClearButton = function(val) {
    var btn = document.getElementById('clearSearchBtn');
    if (btn) {
        if (val && val.trim() !== '') {
            btn.classList.remove('hidden');
        } else {
            btn.classList.add('hidden');
        }
    }
};
window.salonPayClearSearch = function() {
    var input = document.getElementById('serviceSearchInput');
    if (input) {
        input.value = '';
        salonPayFilterServices('');
        salonPayUpdateClearButton('');
        input.focus();
    }
};
window.salonPayAddServiceToCart = function(serviceName, servicePrice) {
    if (!selectedTechnicianId) {
        alert('Please select a technician first before adding services.');
        return;
    }
    var existing = cart.find(function(item) {
        return item.name === serviceName && item.technician_id === selectedTechnicianId;
    });
    if (existing) {
        existing.quantity += 1;
    } else {
        cart.push({
            name: serviceName,
            price: servicePrice,
            quantity: 1,
            technician_id: selectedTechnicianId
        });
    }
    salonPayInitializeServicesList();
    salonPayRenderTechniciansList();
    var step2 = document.getElementById('step2Content');
    if (step2 && !step2.classList.contains('hidden')) {
        salonPayRenderTechniciansTipSplit();
        salonPayRenderCheckoutStep();
    }
};
function salonPayRenderTechniciansList() {
    var container = document.getElementById('techniciansListContainer');
    if (!container) return;
    var assigned = techniciansData.filter(function(t) {
        var idStr = t.id.toString();
        return assignedTechnicianIds.indexOf(idStr) >= 0;
    });
    if (assigned.length === 0) {
        container.innerHTML = '<div class="text-center py-12"><p class="text-sm text-gray-500">No assigned technicians</p></div>';
        return;
    }
    var html = '<div class="space-y-3">';
    assigned.forEach(function(technician) {
        var idStr = technician.id.toString();
        var isActive = selectedTechnicianId === idStr;
        var initials = technician.initials || (technician.firstName || '')[0] + (technician.lastName || '')[0];
        var fullName = technician.firstName + ' ' + technician.lastName;
        var photo = technician.photo || technician.profilePhoto || null;
        var techServices = cart.filter(function(item) { return item.technician_id === idStr; });
        html += '<div onclick="salonPaySelectTechnician(\'' + idStr + '\')" class="flex flex-col gap-2 p-3 rounded-lg border-2 cursor-pointer transition ' + (isActive ? 'border-[#003047] bg-white' : 'border-gray-200 bg-white hover:bg-gray-50') + '"><div class="flex items-center gap-3"><div class="relative flex-shrink-0">' + (photo ? '<img src="' + photo + '" alt="' + fullName + '" class="w-12 h-12 rounded-full object-cover border-2 border-white">' : '<div class="w-12 h-12 bg-[#e6f0f3] rounded-full flex items-center justify-center border-2 border-white"><span class="text-sm font-bold text-[#003047]">' + initials + '</span></div>') + '<div class="absolute -bottom-1 -right-1 w-5 h-5 bg-[#003047] text-white rounded-full flex items-center justify-center text-xs font-bold border-2 border-white">✓</div></div><div class="flex-1 min-w-0"><p class="text-sm font-medium text-gray-900 truncate">' + fullName + '</p><p class="text-xs text-gray-500">Technician</p></div></div>';
        if (techServices.length > 0) {
            html += '<div class="mt-2 pt-2 border-t border-gray-300"><div class="space-y-2">';
            techServices.forEach(function(service) {
                html += '<div class="bg-gray-50 rounded-lg p-3 border border-gray-200"><div class="flex items-center justify-between gap-3"><div class="flex-1 min-w-0"><p class="text-sm font-semibold text-gray-900">' + service.name + '</p><p class="text-xs text-gray-500">$' + service.price.toFixed(2) + ' each</p></div><div class="flex items-center gap-2"><button onclick="event.stopPropagation(); salonPayUpdateServiceQuantity(\'' + service.name.replace(/'/g, "\\'") + '\', ' + service.price + ', \'' + idStr + '\', ' + (service.quantity - 1) + ')" class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-200 rounded border border-gray-300 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg></button><span class="text-sm font-medium text-gray-900 min-w-[2rem] text-center">' + service.quantity + '</span><button onclick="event.stopPropagation(); salonPayUpdateServiceQuantity(\'' + service.name.replace(/'/g, "\\'") + '\', ' + service.price + ', \'' + idStr + '\', ' + (service.quantity + 1) + ')" class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-200 rounded border border-gray-300 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg></button></div><div class="text-sm font-semibold text-gray-900 min-w-[4rem] text-right">$' + (service.price * service.quantity).toFixed(2) + '</div><button onclick="event.stopPropagation(); salonPayRemoveServiceFromCart(\'' + service.name.replace(/'/g, "\\'") + '\', \'' + idStr + '\')" class="w-8 h-8 flex items-center justify-center text-red-500 hover:bg-red-50 rounded transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div></div>';
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
window.salonPaySelectTechnician = function(technicianId) {
    selectedTechnicianId = technicianId;
    salonPayRenderTechniciansList();
    salonPayInitializeServicesList();
};
window.salonPayUpdateServiceQuantity = function(serviceName, servicePrice, technicianId, newQuantity) {
    if (newQuantity <= 0) {
        salonPayRemoveServiceFromCart(serviceName, technicianId);
        return;
    }
    var index = cart.findIndex(function(item) {
        return item.name === serviceName && item.technician_id === technicianId;
    });
    if (index >= 0) {
        cart[index].quantity = newQuantity;
        salonPayRenderTechniciansList();
        salonPayInitializeServicesList();
        var step2 = document.getElementById('step2Content');
        if (step2 && !step2.classList.contains('hidden')) {
            salonPayRenderTechniciansTipSplit();
            salonPayRenderCheckoutStep();
        }
    }
};
window.salonPayRemoveServiceFromCart = function(serviceName, technicianId) {
    var index = cart.findIndex(function(item) {
        return item.name === serviceName && item.technician_id === technicianId;
    });
    if (index >= 0) {
        cart.splice(index, 1);
        salonPayRenderTechniciansList();
        salonPayInitializeServicesList();
        var step2 = document.getElementById('step2Content');
        if (step2 && !step2.classList.contains('hidden')) {
            salonPayRenderTechniciansTipSplit();
            salonPayRenderCheckoutStep();
        }
    }
};
window.salonPaySwitchStep = function(step) {
    if (step === 2 && cart.length === 0) {
        alert('Please add at least one service to your cart before checkout.');
        return;
    }
    currentStep = step;
    var step1 = document.getElementById('step1Content');
    var step2 = document.getElementById('step2Content');
    if (step === 1) {
        step1.classList.remove('hidden');
        step2.classList.add('hidden');
    } else {
        step1.classList.add('hidden');
        step2.classList.remove('hidden');
        salonPayRenderCheckoutStep();
    }
    var step1Tab = document.getElementById('step1Tab');
    var step2Tab = document.getElementById('step2Tab');
    if (step === 1) {
        step1Tab.classList.remove('border-transparent');
        step1Tab.classList.add('border-[#003047]');
        step1Tab.querySelector('span').classList.remove('bg-gray-200', 'text-gray-600');
        step1Tab.querySelector('span').classList.add('bg-[#003047]', 'text-white');
        step1Tab.querySelector('h3').classList.remove('text-gray-500');
        step1Tab.querySelector('h3').classList.add('text-gray-900');
        step2Tab.classList.remove('border-[#003047]');
        step2Tab.classList.add('border-transparent');
        step2Tab.querySelector('span').classList.remove('bg-[#003047]', 'text-white');
        step2Tab.querySelector('span').classList.add('bg-gray-200', 'text-gray-600');
        step2Tab.querySelector('h3').classList.remove('text-gray-900');
        step2Tab.querySelector('h3').classList.add('text-gray-500');
    } else {
        step2Tab.classList.remove('border-transparent');
        step2Tab.classList.add('border-[#003047]');
        step2Tab.querySelector('span').classList.remove('bg-gray-200', 'text-gray-600');
        step2Tab.querySelector('span').classList.add('bg-[#003047]', 'text-white');
        step2Tab.querySelector('h3').classList.remove('text-gray-500');
        step2Tab.querySelector('h3').classList.add('text-gray-900');
        step1Tab.classList.remove('border-[#003047]');
        step1Tab.classList.add('border-transparent');
        step1Tab.querySelector('span').classList.remove('bg-[#003047]', 'text-white');
        step1Tab.querySelector('span').classList.add('bg-gray-200', 'text-gray-600');
        step1Tab.querySelector('h3').classList.remove('text-gray-900');
        step1Tab.querySelector('h3').classList.add('text-gray-500');
    }
};
function salonPayRenderCheckoutStep() {
    if (!customerData || !appointmentData) return;
    paymentSubtotal = cart.reduce(function(sum, item) { return sum + (item.price * item.quantity); }, 0);
    paymentTip = 0;
    paymentDiscount = 0;
    var discountedSubtotal = paymentSubtotal - paymentDiscount;
    paymentTax = discountedSubtotal * 0.05;
    var total = discountedSubtotal + paymentTax + paymentTip;
    var itemsList = document.getElementById('checkoutItemsList');
    if (itemsList) {
        itemsList.innerHTML = cart.map(function(item) {
            return '<div class="grid grid-cols-12 gap-4"><div class="col-span-6"><span class="text-sm text-gray-900">' + item.name + '</span></div><div class="col-span-3 text-center"><span class="text-sm text-gray-900">' + item.quantity + '</span></div><div class="col-span-3 text-right"><span class="text-sm font-semibold text-gray-900">$' + (item.price * item.quantity).toFixed(2) + '</span></div></div>';
        }).join('');
    }
    document.getElementById('checkoutSubtotalDisplay').textContent = '$' + paymentSubtotal.toFixed(2);
    document.getElementById('checkoutTaxDisplay').textContent = '$' + paymentTax.toFixed(2);
    document.getElementById('checkoutDiscountDisplay').textContent = '-$' + paymentDiscount.toFixed(2);
    paymentAmountStr = '';
    salonPayUpdatePaymentDisplay();
    salonPayRenderTechniciansTipSplit();
    salonPayUpdatePaymentTotal();
    setTimeout(function() {
        salonPayUpdatePaymentMethodStyles();
        salonPaySwitchTipSplitMode(tipSplitMode);
        var cashContent = document.getElementById('cashPaymentContent');
        var cardContent = document.getElementById('cardPaymentContent');
        if (cashContent) cashContent.classList.remove('hidden');
        if (cardContent) cardContent.classList.add('hidden');
    }, 100);
}
window.salonPaySetTipPercentage = function(percentage) {
    var tipAmount = paymentSubtotal * (percentage / 100);
    paymentTip = tipAmount;
    var tipInput = document.getElementById('tipInput');
    var tipInputEven = document.getElementById('tipInputEven');
    if (tipInput) tipInput.value = tipAmount.toFixed(2);
    if (tipInputEven) tipInputEven.value = tipAmount.toFixed(2);
    salonPayUpdatePaymentTotal();
    salonPayRenderTechniciansTipSplit();
};
window.salonPayUpdateTipFromInput = function() {
    var tipInput = document.getElementById('tipInput');
    var tipInputEven = document.getElementById('tipInputEven');
    var tipValue = parseFloat(tipInput ? tipInput.value : (tipInputEven ? tipInputEven.value : 0)) || 0;
    if (tipInput) tipInput.value = tipValue;
    if (tipInputEven) tipInputEven.value = tipValue;
    paymentTip = tipValue;
    salonPayUpdatePaymentTotal();
    salonPayRenderTechniciansTipSplit();
};
function salonPayCalculateTechnicianServiceTotals() {
    var technicianTotals = {};
    var totalServices = 0;
    cart.forEach(function(item) {
        var idStr = item.technician_id.toString();
        var itemTotal = item.price * item.quantity;
        if (!technicianTotals[idStr]) technicianTotals[idStr] = 0;
        technicianTotals[idStr] += itemTotal;
        totalServices += itemTotal;
    });
    return { technicianTotals: technicianTotals, totalServices: totalServices };
}
function salonPayRenderTechniciansTipSplit() {
    if (!assignedTechnicianIds || assignedTechnicianIds.length === 0) {
        var tipSection = document.getElementById('techniciansTipSection');
        var tipSectionEven = document.getElementById('techniciansTipSectionEven');
        var tipSectionCustom = document.getElementById('techniciansTipSectionCustom');
        if (tipSection) tipSection.style.display = 'none';
        if (tipSectionEven) tipSectionEven.style.display = 'none';
        if (tipSectionCustom) tipSectionCustom.style.display = 'none';
        return;
    }
    var assigned = techniciansData.filter(function(t) {
        var idStr = t.id.toString();
        return assignedTechnicianIds.indexOf(idStr) >= 0;
    });
    if (assigned.length === 0) {
        var tipSection = document.getElementById('techniciansTipSection');
        var tipSectionEven = document.getElementById('techniciansTipSectionEven');
        var tipSectionCustom = document.getElementById('techniciansTipSectionCustom');
        if (tipSection) tipSection.style.display = 'none';
        if (tipSectionEven) tipSectionEven.style.display = 'none';
        if (tipSectionCustom) tipSectionCustom.style.display = 'none';
        return;
    }
    var totals = salonPayCalculateTechnicianServiceTotals();
    var technicianTotals = totals.technicianTotals;
    var totalServices = totals.totalServices;
    if (tipSplitMode === 'percentage' && totalServices > 0) {
        assigned.forEach(function(technician) {
            var idStr = technician.id.toString();
            var techTotal = technicianTotals[idStr] || 0;
            if (techTotal > 0) {
                var percentage = Math.round(((techTotal / totalServices) * 100) * 10) / 10;
                var amount = (paymentTip * percentage) / 100;
                technicianTips[idStr] = { percentage: percentage, amount: amount };
            } else {
                technicianTips[idStr] = { percentage: 0, amount: 0 };
            }
        });
    } else if (tipSplitMode === 'even') {
        var techsWithServices = assigned.filter(function(t) {
            var idStr = t.id.toString();
            return (technicianTotals[idStr] || 0) > 0;
        });
        if (techsWithServices.length > 0) {
            var evenPercentage = Math.round((100 / techsWithServices.length) * 10) / 10;
            var evenAmount = paymentTip / techsWithServices.length;
            assigned.forEach(function(technician) {
                var idStr = technician.id.toString();
                var techTotal = technicianTotals[idStr] || 0;
                if (techTotal > 0) {
                    technicianTips[idStr] = { percentage: evenPercentage, amount: evenAmount };
                } else {
                    technicianTips[idStr] = { percentage: 0, amount: 0 };
                }
            });
        } else {
            assigned.forEach(function(technician) {
                var idStr = technician.id.toString();
                technicianTips[idStr] = { percentage: 0, amount: 0 };
            });
        }
    } else {
        if (Object.keys(technicianTips).length === 0) {
            assigned.forEach(function(technician) {
                var idStr = technician.id.toString();
                var techTotal = technicianTotals[idStr] || 0;
                if (techTotal > 0) {
                    var defaultPercentage = 100 / assigned.length;
                    technicianTips[idStr] = { percentage: defaultPercentage, amount: 0 };
                } else {
                    technicianTips[idStr] = { percentage: 0, amount: 0 };
                }
            });
        }
    }
    var tipSection = document.getElementById('techniciansTipSection');
    var tipSectionEven = document.getElementById('techniciansTipSectionEven');
    var tipSectionCustom = document.getElementById('techniciansTipSectionCustom');
    if (tipSection) tipSection.style.display = tipSplitMode === 'percentage' ? 'block' : 'none';
    if (tipSectionEven) tipSectionEven.style.display = tipSplitMode === 'even' ? 'block' : 'none';
    if (tipSectionCustom) tipSectionCustom.style.display = tipSplitMode === 'custom' ? 'block' : 'none';
    var techniciansTipList;
    if (tipSplitMode === 'percentage') {
        techniciansTipList = document.getElementById('techniciansTipList');
    } else if (tipSplitMode === 'even') {
        techniciansTipList = document.getElementById('techniciansTipListEven');
    } else {
        techniciansTipList = document.getElementById('techniciansTipListCustom');
    }
    if (!techniciansTipList) return;
    var tipListHTML = assigned.map(function(technician) {
        var idStr = technician.id.toString();
        var initials = technician.initials || (technician.firstName || '')[0] + (technician.lastName || '')[0];
        var fullName = technician.firstName + ' ' + technician.lastName;
        var photo = technician.photo || technician.profilePhoto || null;
        var techTotal = technicianTotals[idStr] || 0;
        var hasServices = techTotal > 0;
        var tipData, currentPercentage, calculatedAmount;
        if (!hasServices) {
            currentPercentage = 0;
            calculatedAmount = 0;
        } else {
            tipData = technicianTips[idStr] || { percentage: 100 / assigned.length, amount: 0 };
            currentPercentage = tipData.percentage || (100 / assigned.length);
            if (tipSplitMode === 'percentage' || tipSplitMode === 'even') {
                calculatedAmount = (paymentTip * currentPercentage) / 100;
            } else {
                calculatedAmount = tipData.amount > 0 && tipData.amount !== (paymentTip * currentPercentage / 100) ? tipData.amount : (paymentTip * currentPercentage / 100);
            }
        }
        var cardClasses = hasServices ? 'flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200' : 'flex items-center gap-3 p-3 bg-gray-50 rounded-lg border border-gray-200 opacity-50';
        var nameClasses = hasServices ? 'text-sm font-medium text-gray-900 truncate' : 'text-sm font-medium text-gray-400 truncate';
        var photoClasses = hasServices ? 'w-10 h-10 rounded-full object-cover border-2 border-white' : 'w-10 h-10 rounded-full object-cover border-2 border-gray-300 opacity-60';
        var initialsBgClasses = hasServices ? 'w-10 h-10 bg-[#e6f0f3] rounded-full flex items-center justify-center border-2 border-white' : 'w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center border-2 border-gray-300';
        var initialsTextClasses = hasServices ? 'text-sm font-bold text-[#003047]' : 'text-sm font-bold text-gray-400';
        return '<div class="' + cardClasses + '" data-technician-id="' + idStr + '"><div class="flex-shrink-0">' + (photo ? '<img src="' + photo + '" alt="' + fullName + '" class="' + photoClasses + '">' : '<div class="' + initialsBgClasses + '"><span class="' + initialsTextClasses + '">' + initials + '</span></div>') + '</div><div class="flex-1 min-w-0"><p class="' + nameClasses + '">' + fullName + '</p></div>' + (tipSplitMode === 'percentage' || tipSplitMode === 'even' ? '<div class="flex gap-2 flex-1"><div class="flex-1"><div class="flex items-center border ' + (hasServices ? 'border-gray-300' : 'border-gray-200') + ' rounded-lg"><input type="number" id="tip-percentage-' + idStr + '" value="' + (currentPercentage % 1 === 0 ? Math.round(currentPercentage) : currentPercentage.toFixed(1)) + '" step="0.1" min="0" max="100" ' + (!hasServices || tipSplitMode === 'percentage' || tipSplitMode === 'even' ? 'readonly tabindex="-1"' : '') + ' ' + (!hasServices ? 'disabled' : '') + ' ' + (tipSplitMode === 'percentage' || tipSplitMode === 'even' ? '' : 'oninput="salonPayUpdateTechnicianTipPercentage(\'' + idStr + '\', this.value)"') + ' class="flex-1 px-3 py-2 text-sm ' + (!hasServices || tipSplitMode === 'percentage' || tipSplitMode === 'even' ? 'bg-gray-100 cursor-not-allowed' : '') + ' ' + (!hasServices ? 'text-gray-400' : 'text-gray-900') + ' focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent rounded-l-lg border-0"><span class="px-3 py-2 text-sm ' + (hasServices ? 'text-gray-500' : 'text-gray-300') + ' bg-gray-50 border-l ' + (hasServices ? 'border-gray-300' : 'border-gray-200') + ' rounded-r-lg">%</span></div></div><div class="flex-1"><div class="flex items-center border ' + (hasServices ? 'border-gray-300' : 'border-gray-200') + ' rounded-lg"><input type="number" id="tip-amount-' + idStr + '" value="' + calculatedAmount.toFixed(2) + '" step="0.01" min="0" readonly tabindex="-1" ' + (!hasServices ? 'disabled' : '') + ' class="flex-1 px-3 py-2 text-sm bg-gray-100 cursor-not-allowed ' + (!hasServices ? 'text-gray-400' : 'text-gray-900') + ' focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent rounded-l-lg border-0"><span class="px-3 py-2 text-sm ' + (hasServices ? 'text-gray-500' : 'text-gray-300') + ' bg-gray-50 border-l ' + (hasServices ? 'border-gray-300' : 'border-gray-200') + ' rounded-r-lg">$</span></div></div></div>' : '<div><div class="flex items-center border ' + (hasServices ? 'border-gray-300' : 'border-gray-200') + ' rounded-lg"><input type="number" id="tip-amount-' + idStr + '" value="' + calculatedAmount.toFixed(2) + '" step="0.01" min="0" ' + (!hasServices ? 'readonly tabindex="-1" disabled' : '') + ' ' + (hasServices ? 'oninput="salonPayUpdateTechnicianTipAmount(\'' + idStr + '\', this.value)"' : '') + ' class="flex-1 px-3 py-2 text-sm ' + (!hasServices ? 'bg-gray-100 cursor-not-allowed text-gray-400' : '') + ' focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent rounded-l-lg border-0"><span class="px-3 py-2 text-sm ' + (hasServices ? 'text-gray-500' : 'text-gray-300') + ' bg-gray-50 border-l ' + (hasServices ? 'border-gray-300' : 'border-gray-200') + ' rounded-r-lg">$</span></div></div>') + '</div>';
    }).join('');
    techniciansTipList.innerHTML = tipListHTML;
    salonPayUpdateTotalTipSplit();
}
window.salonPayUpdateTechnicianTipPercentage = function(technicianId, value) {
    var percentage = parseFloat(value) || 0;
    if (!technicianTips[technicianId]) {
        technicianTips[technicianId] = { percentage: 0, amount: 0 };
    }
    technicianTips[technicianId].percentage = percentage;
    var calculatedAmount = paymentTip * percentage / 100;
    technicianTips[technicianId].amount = calculatedAmount;
    var amountInput = document.getElementById('tip-amount-' + technicianId);
    if (amountInput) {
        amountInput.value = calculatedAmount.toFixed(2);
    }
    salonPayUpdateTotalTipSplit();
};
window.salonPayUpdateTechnicianTipAmount = function(technicianId, value) {
    var amount = parseFloat(value) || 0;
    if (!technicianTips[technicianId]) {
        technicianTips[technicianId] = { percentage: 0, amount: 0 };
    }
    technicianTips[technicianId].amount = amount;
    if (paymentTip > 0) {
        var calculatedPercentage = (amount / paymentTip) * 100;
        technicianTips[technicianId].percentage = calculatedPercentage;
        var percentageInput = document.getElementById('tip-percentage-' + technicianId);
        if (percentageInput) {
            percentageInput.value = calculatedPercentage % 1 === 0 ? calculatedPercentage : calculatedPercentage.toFixed(1);
        }
    }
    salonPayUpdateTotalTipSplit();
};
function salonPayCalculateTotalTipSplit() {
    var assigned = techniciansData.filter(function(t) {
        var idStr = t.id.toString();
        return assignedTechnicianIds.indexOf(idStr) >= 0;
    });
    var totalSplit = 0;
    assigned.forEach(function(technician) {
        var idStr = technician.id.toString();
        var tipData = technicianTips[idStr] || { percentage: 0, amount: 0 };
        var amount = tipData.amount || (paymentTip * (tipData.percentage || 0) / 100);
        totalSplit += amount;
    });
    return totalSplit;
}
function salonPayUpdateTotalTipSplit() {
    var totalSplit = salonPayCalculateTotalTipSplit();
    var totalTipSplitDisplay = document.getElementById('totalTipSplitDisplay');
    var totalTipSplitDisplayEven = document.getElementById('totalTipSplitDisplayEven');
    var totalTipSplitDisplayCustom = document.getElementById('totalTipSplitDisplayCustom');
    if (totalTipSplitDisplay) totalTipSplitDisplay.textContent = '$' + totalSplit.toFixed(2);
    if (totalTipSplitDisplayEven) totalTipSplitDisplayEven.textContent = '$' + totalSplit.toFixed(2);
    if (totalTipSplitDisplayCustom) totalTipSplitDisplayCustom.textContent = '$' + totalSplit.toFixed(2);
    salonPayUpdatePaymentTotal();
}
window.salonPaySwitchTipSplitMode = function(mode) {
    tipSplitMode = mode;
    var percentageTab = document.getElementById('tipPercentageTab');
    var evenTab = document.getElementById('tipEvenTab');
    var customTab = document.getElementById('tipCustomTab');
    var percentageContent = document.getElementById('percentageTabContent');
    var evenContent = document.getElementById('evenTabContent');
    var customContent = document.getElementById('customTabContent');
    [percentageTab, evenTab, customTab].forEach(function(tab) {
        if (tab) {
            tab.classList.remove('text-gray-700', 'border-[#003047]');
            tab.classList.add('text-gray-500', 'border-transparent');
        }
    });
    if (percentageContent) percentageContent.classList.add('hidden');
    if (evenContent) evenContent.classList.add('hidden');
    if (customContent) customContent.classList.add('hidden');
    if (mode === 'percentage') {
        if (percentageTab) {
            percentageTab.classList.remove('text-gray-500', 'border-transparent');
            percentageTab.classList.add('text-gray-700', 'border-[#003047]');
        }
        if (percentageContent) percentageContent.classList.remove('hidden');
    } else if (mode === 'even') {
        if (evenTab) {
            evenTab.classList.remove('text-gray-500', 'border-transparent');
            evenTab.classList.add('text-gray-700', 'border-[#003047]');
        }
        if (evenContent) evenContent.classList.remove('hidden');
        salonPaySplitTipEvenly();
    } else if (mode === 'custom') {
        if (customTab) {
            customTab.classList.remove('text-gray-500', 'border-transparent');
            customTab.classList.add('text-gray-700', 'border-[#003047]');
        }
        if (customContent) customContent.classList.remove('hidden');
    }
    salonPayRenderTechniciansTipSplit();
};
function salonPaySplitTipEvenly() {
    if (!assignedTechnicianIds || assignedTechnicianIds.length === 0) return;
    var assigned = techniciansData.filter(function(t) {
        var idStr = t.id.toString();
        return assignedTechnicianIds.indexOf(idStr) >= 0;
    });
    if (assigned.length === 0) return;
    var totals = salonPayCalculateTechnicianServiceTotals();
    var technicianTotals = totals.technicianTotals;
    var techsWithServices = assigned.filter(function(t) {
        var idStr = t.id.toString();
        return (technicianTotals[idStr] || 0) > 0;
    });
    if (techsWithServices.length > 0) {
        var evenPercentage = 100 / techsWithServices.length;
        var evenAmount = paymentTip / techsWithServices.length;
        assigned.forEach(function(technician) {
            var idStr = technician.id.toString();
            var techTotal = technicianTotals[idStr] || 0;
            if (techTotal > 0) {
                technicianTips[idStr] = { percentage: evenPercentage, amount: evenAmount };
            } else {
                technicianTips[idStr] = { percentage: 0, amount: 0 };
            }
        });
    }
    salonPayRenderTechniciansTipSplit();
}
function salonPayUpdatePaymentTotal() {
    var discountedSubtotal = paymentSubtotal - paymentDiscount;
    paymentTax = discountedSubtotal * 0.05;
    var totalTipSplitAmount = salonPayCalculateTotalTipSplit();
    var total = discountedSubtotal + paymentTax + totalTipSplitAmount;
    var checkoutTipDisplay = document.getElementById('checkoutTipDisplay');
    var checkoutDiscountDisplay = document.getElementById('checkoutDiscountDisplay');
    var checkoutTaxDisplay = document.getElementById('checkoutTaxDisplay');
    var checkoutTotalDisplay = document.getElementById('checkoutTotalDisplay');
    if (checkoutTipDisplay) checkoutTipDisplay.textContent = '$' + totalTipSplitAmount.toFixed(2);
    if (checkoutDiscountDisplay) checkoutDiscountDisplay.textContent = '-$' + paymentDiscount.toFixed(2);
    if (checkoutTaxDisplay) checkoutTaxDisplay.textContent = '$' + paymentTax.toFixed(2);
    if (checkoutTotalDisplay) checkoutTotalDisplay.textContent = '$' + total.toFixed(2);
    var tipAmountHidden = document.getElementById('tipAmountHidden');
    if (tipAmountHidden) tipAmountHidden.value = totalTipSplitAmount.toFixed(2);
    var discountAmountHidden = document.getElementById('discountAmountHidden');
    if (discountAmountHidden) discountAmountHidden.value = paymentDiscount.toFixed(2);
    var cardRadio = document.getElementById('paymentMethodCard');
    if (cardRadio && cardRadio.checked) {
        paymentAmountStr = total.toFixed(2);
        salonPayUpdatePaymentDisplay();
    }
}
window.salonPayAddPaymentAmount = function(amount) {
    var currentAmount = parseFloat(document.getElementById('paymentAmountValue').value) || 0;
    var newAmount = currentAmount + amount;
    paymentAmountStr = newAmount.toFixed(2);
    salonPayUpdatePaymentDisplay();
};
window.salonPayAddPaymentDigit = function(digit) {
    if (digit === '.') {
        if (paymentAmountStr === '') {
            paymentAmountStr = '0.';
        } else if (paymentAmountStr.indexOf('.') >= 0) {
            return;
        } else {
            paymentAmountStr += '.';
        }
    } else {
        paymentAmountStr += digit;
    }
    salonPayUpdatePaymentDisplay();
};
window.salonPayRemovePaymentDigit = function() {
    if (paymentAmountStr.length > 0) {
        paymentAmountStr = paymentAmountStr.slice(0, -1);
        salonPayUpdatePaymentDisplay();
    }
};
function salonPayUpdatePaymentDisplay() {
    var displayEl = document.getElementById('paymentAmount');
    var valueEl = document.getElementById('paymentAmountValue');
    if (!displayEl || !valueEl) return;
    if (paymentAmountStr === '') {
        displayEl.textContent = '$0';
        valueEl.value = '0';
    } else {
        var amount = parseFloat(paymentAmountStr) || 0;
        displayEl.textContent = '$' + amount.toFixed(2);
        valueEl.value = amount.toFixed(2);
    }
}
window.salonPaySelectPaymentMethod = function(method) {
    var cashRadio = document.getElementById('paymentMethodCash');
    var cardRadio = document.getElementById('paymentMethodCard');
    var cashBtn = document.getElementById('paymentMethodCashBtn');
    var cardBtn = document.getElementById('paymentMethodCardBtn');
    var hiddenInput = document.getElementById('paymentMethod');
    var cashContent = document.getElementById('cashPaymentContent');
    var cardContent = document.getElementById('cardPaymentContent');
    if (method === 'cash') {
        if (cashRadio) cashRadio.checked = true;
        if (cardRadio) cardRadio.checked = false;
        if (cashContent) cashContent.classList.remove('hidden');
        if (cardContent) cardContent.classList.add('hidden');
    } else if (method === 'card') {
        if (cashRadio) cashRadio.checked = false;
        if (cardRadio) cardRadio.checked = true;
        if (cashContent) cashContent.classList.add('hidden');
        if (cardContent) cardContent.classList.remove('hidden');
        var cardNumberInput = document.getElementById('cardNumberInput');
        var approvedCodeInput = document.getElementById('approvedCodeInput');
        if (cardNumberInput) {
            cardNumberInput.removeAttribute('readonly');
            cardNumberInput.removeAttribute('disabled');
        }
        if (approvedCodeInput) {
            approvedCodeInput.removeAttribute('readonly');
            approvedCodeInput.removeAttribute('disabled');
        }
        var checkoutTotalDisplay = document.getElementById('checkoutTotalDisplay');
        if (checkoutTotalDisplay) {
            var totalText = checkoutTotalDisplay.textContent.replace('$', '').trim();
            var totalAmount = parseFloat(totalText) || 0;
            if (totalAmount > 0) {
                paymentAmountStr = totalAmount.toFixed(2);
                salonPayUpdatePaymentDisplay();
            }
        }
    }
    if (hiddenInput) {
        hiddenInput.value = method;
    }
    salonPayUpdatePaymentMethodStyles();
};
function salonPayUpdatePaymentMethodStyles() {
    var cashRadio = document.getElementById('paymentMethodCash');
    var cardRadio = document.getElementById('paymentMethodCard');
    var cashBtn = document.getElementById('paymentMethodCashBtn');
    var cardBtn = document.getElementById('paymentMethodCardBtn');
    if (cashBtn && cashRadio) {
        if (cashRadio.checked) {
            cashBtn.classList.remove('border-gray-200', 'bg-white');
            cashBtn.classList.add('border-[#003047]', 'bg-[#e6f0f3]');
        } else {
            cashBtn.classList.remove('border-[#003047]', 'bg-[#e6f0f3]');
            cashBtn.classList.add('border-gray-200', 'bg-white');
        }
    }
    if (cardBtn && cardRadio) {
        if (cardRadio.checked) {
            cardBtn.classList.remove('border-gray-200', 'bg-white');
            cardBtn.classList.add('border-[#003047]', 'bg-[#e6f0f3]');
        } else {
            cardBtn.classList.remove('border-[#003047]', 'bg-[#e6f0f3]');
            cardBtn.classList.add('border-gray-200', 'bg-white');
        }
    }
}
window.salonPayFormatCardNumber = function(input) {
    var value = input.value.replace(/\D/g, '');
    if (value.length > 16) {
        value = value.substring(0, 16);
    }
    var formattedValue = '';
    for (var i = 0; i < value.length; i++) {
        if (i > 0 && i % 4 === 0) {
            formattedValue += ' ';
        }
        formattedValue += value[i];
    }
    input.value = formattedValue;
};
window.salonPayOpenGiftCardModal = function() {
    var content = '<div class="p-6"><div class="flex items-center justify-between mb-4"><h3 class="text-xl font-bold text-gray-900">Gift Card</h3><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><div class="space-y-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">Gift Card Number</label><input type="text" id="giftCardNumberInput" placeholder="Enter gift card number" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div><label class="block text-sm font-medium text-gray-700 mb-2">PIN (if required)</label><input type="text" id="giftCardPinInput" placeholder="Enter PIN" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div class="flex gap-3 justify-end"><button type="button" onclick="closeModal()" class="px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">Cancel</button><button type="button" onclick="salonPayApplyGiftCard()" class="px-6 py-3 text-sm font-medium text-white bg-[#003047] rounded-lg hover:bg-[#002535] transition">Apply</button></div></div></div>';
    openModal(content, 'default', false);
};
window.salonPayApplyGiftCard = function() {
    var giftCardNumberInput = document.getElementById('giftCardNumberInput');
    var giftCardPinInput = document.getElementById('giftCardPinInput');
    var number = giftCardNumberInput ? giftCardNumberInput.value.trim() : '';
    var pin = giftCardPinInput ? giftCardPinInput.value.trim() : '';
    if (!number) {
        alert('Please enter a gift card number');
        return;
    }
    console.log('Applying gift card:', { number: number, pin: pin });
    closeModal();
};
window.salonPayOpenRedeemModal = function() {
    var content = '<div class="p-6"><div class="flex items-center justify-between mb-4"><h3 class="text-xl font-bold text-gray-900">Redeem</h3><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><div class="space-y-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">Redeem Code</label><input type="text" id="redeemCodeInput" placeholder="Enter redeem code" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div class="flex gap-3 justify-end"><button type="button" onclick="closeModal()" class="px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">Cancel</button><button type="button" onclick="salonPayApplyRedeem()" class="px-6 py-3 text-sm font-medium text-white bg-[#003047] rounded-lg hover:bg-[#002535] transition">Apply</button></div></div></div>';
    openModal(content, 'default', false);
};
window.salonPayApplyRedeem = function() {
    var redeemCodeInput = document.getElementById('redeemCodeInput');
    var code = redeemCodeInput ? redeemCodeInput.value.trim() : '';
    if (!code) {
        alert('Please enter a redeem code');
        return;
    }
    console.log('Applying redeem code:', code);
    closeModal();
};
window.salonPayOpenDiscountModal = function() {
    var content = '<div class="p-6"><div class="flex items-center justify-between mb-4"><h3 class="text-xl font-bold text-gray-900">Discount</h3><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><div class="space-y-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">Discount</label><div class="grid grid-cols-5 gap-2 mb-2"><button type="button" onclick="salonPaySetDiscountPercentage(5); salonPaySwitchToDiscountAmountView();" class="py-2 text-xs font-medium text-gray-700 bg-[#e6f0f3] rounded-lg hover:bg-[#b3d1d9] transition active:scale-95 border border-[#b3d1d9]">5%</button><button type="button" onclick="salonPaySetDiscountPercentage(10); salonPaySwitchToDiscountAmountView();" class="py-2 text-xs font-medium text-gray-700 bg-[#e6f0f3] rounded-lg hover:bg-[#b3d1d9] transition active:scale-95 border border-[#b3d1d9]">10%</button><button type="button" onclick="salonPaySetDiscountPercentage(15); salonPaySwitchToDiscountAmountView();" class="py-2 text-xs font-medium text-gray-700 bg-[#e6f0f3] rounded-lg hover:bg-[#b3d1d9] transition active:scale-95 border border-[#b3d1d9]">15%</button><button type="button" onclick="salonPaySetDiscountPercentage(20); salonPaySwitchToDiscountAmountView();" class="py-2 text-xs font-medium text-gray-700 bg-[#e6f0f3] rounded-lg hover:bg-[#b3d1d9] transition active:scale-95 border border-[#b3d1d9]">20%</button><button type="button" onclick="salonPaySwitchToDiscountCodeView();" class="py-2 text-xs font-medium text-gray-700 bg-[#e6f0f3] rounded-lg hover:bg-[#b3d1d9] transition active:scale-95 border border-[#b3d1d9]">CODE</button></div><div id="discountAmountView"><input type="number" name="discount" id="discountInputModal" step="0.01" min="0" value="' + paymentDiscount.toFixed(2) + '" oninput="salonPayUpdateDiscountFromInputModal()" placeholder="0.00" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div id="discountCodeView" class="hidden"><label class="block text-sm font-medium text-gray-700 mb-2">Enter Discount Code</label><input type="text" id="discountCodeInputModal" placeholder="Enter Discount Code" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div></div><div class="flex gap-3 justify-end"><button type="button" onclick="closeModal()" class="px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">Cancel</button><button type="button" onclick="closeModal()" class="px-6 py-3 text-sm font-medium text-white bg-[#003047] rounded-lg hover:bg-[#002535] transition">Apply</button></div></div>';
    openModal(content, 'default', false);
};
window.salonPaySetDiscountPercentage = function(percentage) {
    var discountAmount = paymentSubtotal * (percentage / 100);
    paymentDiscount = discountAmount;
    var modalInput = document.getElementById('discountInputModal');
    if (modalInput) {
        modalInput.value = discountAmount.toFixed(2);
    }
    salonPayUpdatePaymentTotal();
};
window.salonPayUpdateDiscountFromInputModal = function() {
    var discountInputModal = document.getElementById('discountInputModal');
    if (discountInputModal) {
        paymentDiscount = parseFloat(discountInputModal.value) || 0;
        salonPayUpdatePaymentTotal();
    }
};
window.salonPaySwitchToDiscountCodeView = function() {
    var amountView = document.getElementById('discountAmountView');
    var codeView = document.getElementById('discountCodeView');
    if (amountView && codeView) {
        amountView.classList.add('hidden');
        codeView.classList.remove('hidden');
    }
};
window.salonPaySwitchToDiscountAmountView = function() {
    var amountView = document.getElementById('discountAmountView');
    var codeView = document.getElementById('discountCodeView');
    if (amountView && codeView) {
        amountView.classList.remove('hidden');
        codeView.classList.add('hidden');
    }
};
window.salonPayProcessPayment = function(event) {
    event.preventDefault();
    var paymentMethod = document.getElementById('paymentMethod').value;
    var paymentAmount = parseFloat(document.getElementById('paymentAmountValue').value) || 0;
    var discountedSubtotal = paymentSubtotal - paymentDiscount;
    var calculatedTax = discountedSubtotal * 0.05;
    var totalTipSplitAmount = salonPayCalculateTotalTipSplit();
    var total = discountedSubtotal + calculatedTax + totalTipSplitAmount;
    if (paymentAmount < total) {
        alert('Payment amount ($' + paymentAmount.toFixed(2) + ') is less than total ($' + total.toFixed(2) + '). Please enter the correct amount.');
        return;
    }
    var change = paymentAmount - total;
    console.log('Processing payment:', {
        appointmentId: appointmentId,
        customerId: customerData.id,
        services: cart,
        technicians: assignedTechnicianIds,
        subtotal: paymentSubtotal,
        tax: paymentTax,
        tip: totalTipSplitAmount,
        discount: paymentDiscount,
        total: total,
        paymentMethod: paymentMethod,
        paymentAmount: paymentAmount,
        change: change
    });
    if (typeof showSuccessMessage === 'function') {
        showSuccessMessage('Payment processed successfully!');
    } else {
        alert('Payment processed successfully!');
    }
    setTimeout(function() {
        window.location.href = ticketsUrl;
    }, 1500);
};
document.addEventListener('DOMContentLoaded', function() {
    salonPayLoadPaymentData();
});
})();

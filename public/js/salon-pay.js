(function() {
var base = window.salonJsonBase || '/json';
var ticketsUrl = window.salonTicketsUrl || '/booking/tickets';
var apiAppointmentsUrl = window.salonApiAppointmentsUrl || '';
var apiBase = window.salonApiBase || '';
var bootstrap = window.salonPayBootstrap || null;
if (!apiBase) {
    if (base && base.indexOf('/api/salon/data') >= 0) {
        apiBase = base.replace(/\/data\/?$/, '');
    } else {
        apiBase = '/api/salon';
    }
}
var urlParams = new URLSearchParams(window.location.search);
var appointmentId = urlParams.get('id');
var appointmentData = null, customerData = null;
var allServicesData = [], servicesData = [], categoriesMap = {}, selectedCategory = null;
var cart = [], techniciansData = [], assignedTechnicianIds = [], selectedTechnicianId = null;
var paymentSubtotal = 0, paymentTax = 0, paymentTip = 0, paymentDiscount = 0, paymentCredits = 0, paymentGiftCard = 0, paymentAmountStr = '';
var currentStep = 1, technicianTips = {}, tipSplitMode = 'percentage';
var lastSavedCartSignature = null;
var initialTechServices = {};
function salonPayServiceColorDot(serviceIdOrName) {
    var svc = null;
    if (typeof serviceIdOrName === 'number' || (typeof serviceIdOrName === 'string' && /^\d+$/.test(serviceIdOrName))) {
        svc = servicesData.find(function(s) { return s.id == serviceIdOrName; });
    }
    if (!svc && typeof serviceIdOrName === 'string') {
        svc = servicesData.find(function(s) { return s.name === serviceIdOrName; });
    }
    if (svc && svc.color) return '<span class="inline-block w-3 h-3 rounded-full flex-shrink-0" style="background:' + svc.color + '"></span>';
    return '<span class="inline-block w-3 h-3 rounded-full flex-shrink-0 border-2 border-gray-300"></span>';
}
var discountsEnabled = true;
var couponsData = [];
var couponsLoading = null;
var giftCardsData = [];
var giftCardsLoading = null;
var appliedGiftCardCode = '';
var giftCardsEnabled = true;
var availableCredits = 0;
var taxRate = 0;
var taxName = 'Tax';
var taxApplyToAll = true;
var pointsRateFixed = false;
var pointsRatePercentage = false;
var pointsUnitValue = 0;
var pointsPerUnit = 0;
var rewardPercentage = 0;
var colorClasses = [
    { bg: 'bg-[#e6f0f3]', text: 'text-[#003047]' }, { bg: 'bg-purple-100', text: 'text-purple-600' },
    { bg: 'bg-teal-100', text: 'text-teal-600' }, { bg: 'bg-indigo-100', text: 'text-indigo-600' },
    { bg: 'bg-rose-100', text: 'text-rose-600' }, { bg: 'bg-blue-100', text: 'text-blue-600' },
    { bg: 'bg-amber-100', text: 'text-amber-600' }, { bg: 'bg-green-100', text: 'text-green-600' }
];

function salonPayApplyBootstrapCatalog() {
    if (!bootstrap) return false;

    if (bootstrap.categories && typeof bootstrap.categories === 'object') {
        categoriesMap = bootstrap.categories;
    }

    if (Array.isArray(bootstrap.services)) {
        allServicesData = bootstrap.services;
        servicesData = allServicesData.filter(function(s) { return s.active !== false; });
    }

    if (Array.isArray(bootstrap.users)) {
        techniciansData = bootstrap.users.filter(function(u) {
            return (u.role === 'technician' || u.userlevel === 'technician') && (u.status === 'active' || !u.status);
        });
        techniciansData.forEach(function(t) {
            initialTechServices[t.id.toString()] = typeof t.services === 'number' ? t.services : 0;
        });
    }

    salonPayInitializeCategoriesList();
    salonPayInitializeServicesList();
    salonPayRenderTechniciansList();

    return true;
}

function salonPayApplyBootstrapSettings() {
    if (!bootstrap || !bootstrap.settings) return false;

    var data = bootstrap.settings || {};
    var enabled = data.discounts_enabled;
    discountsEnabled = enabled === true || enabled === 1 || enabled === '1' || enabled === 'true';

    var giftEnabled = data.gift_cards_enabled;
    giftCardsEnabled = giftEnabled === true || giftEnabled === 1 || giftEnabled === '1' || giftEnabled === 'true';

    var rate = parseFloat(data.tax_rate);
    taxRate = isNaN(rate) ? 0 : rate;

    taxName = data.tax_name ? String(data.tax_name) : 'Tax';

    var applyAll = data.tax_apply_to_all;
    taxApplyToAll = applyAll === true || applyAll === 1 || applyAll === '1' || applyAll === 'true';

    var prf = data.points_rate_fixed;
    pointsRateFixed = prf === true || prf === 1 || prf === '1' || prf === 'true';
    var prp = data.points_rate_percentage;
    pointsRatePercentage = prp === true || prp === 1 || prp === '1' || prp === 'true';
    pointsUnitValue = parseFloat(data.points_unit_value) || 0;
    pointsPerUnit = parseFloat(data.points_per_unit) || 0;
    rewardPercentage = parseFloat(data.reward_percentage) || 0;

    salonPayUpdateTaxLabel();
    salonPayUpdateActionButtonsState();
    salonPayRefreshTotalsForTaxChange();

    return true;
}

function salonPayGetQueryParam(name) {
    try {
        return new URLSearchParams(window.location.search).get(name);
    } catch (e) {
        return null;
    }
}

function salonPaySetStepInUrl(step) {
    try {
        var url = new URL(window.location.href);
        if (step && Number(step) === 2) url.searchParams.set('step', '2');
        else url.searchParams.delete('step');
        window.history.replaceState({}, '', url.toString());
    } catch (e) {
        // no-op
    }
}
function salonPayMakeStableCartSignature(servicesPayload) {
    var items = Array.isArray(servicesPayload) ? servicesPayload : [];
    var normalized = items.map(function(s) {
        var unit = (s.unit_price == null || s.unit_price === '') ? null : Number(s.unit_price);
        return {
            service: s.service || '',
            service_id: (s.service_id == null || s.service_id === '') ? null : Number(s.service_id),
            technician_id: (s.technician_id == null || s.technician_id === '') ? null : Number(s.technician_id),
            quantity: (s.quantity == null || s.quantity === '') ? 1 : Number(s.quantity),
            unit_price: unit == null || isNaN(unit) ? null : Math.round(unit * 100) / 100
        };
    }).filter(function(s) {
        return !!s.service && !!s.technician_id;
    });
    normalized.sort(function(a, b) {
        if (a.technician_id !== b.technician_id) return a.technician_id - b.technician_id;
        var aSid = a.service_id == null ? -1 : a.service_id;
        var bSid = b.service_id == null ? -1 : b.service_id;
        if (aSid !== bSid) return aSid - bSid;
        if (a.service !== b.service) return a.service < b.service ? -1 : 1;
        var aPrice = a.unit_price == null ? -1 : a.unit_price;
        var bPrice = b.unit_price == null ? -1 : b.unit_price;
        if (aPrice !== bPrice) return aPrice - bPrice;
        return a.quantity - b.quantity;
    });
    return JSON.stringify(normalized);
}
async function salonPayLoadPaymentData() {
    if (!appointmentId) {
        salonPayShowError('No appointment ID provided');
        return;
    }

    if (bootstrap && bootstrap.error) {
        salonPayShowError(bootstrap.error || 'Failed to load payment details');
        return;
    }

    if (bootstrap && bootstrap.appointment && bootstrap.customer) {
        var bootstrapId = bootstrap.appointmentId != null ? bootstrap.appointmentId.toString() : null;
        if (!bootstrapId || bootstrapId === appointmentId.toString()) {
            appointmentData = bootstrap.appointment;
            customerData = bootstrap.customer;

            availableCredits = customerData.creditBalance != null ? parseFloat(customerData.creditBalance) || 0 : 0;
            if (appointmentData.assigned_technician && Array.isArray(appointmentData.assigned_technician)) {
                assignedTechnicianIds = appointmentData.assigned_technician.map(function(id) { return id.toString(); });
                if (assignedTechnicianIds.length > 0) selectedTechnicianId = assignedTechnicianIds[0];
            }

            salonPayApplyBootstrapCatalog();
            salonPayApplyBootstrapSettings();

            salonPayBuildCartFromAppointmentServices();
            salonPayUpdateCustomerInfoHeader();

            // Deep-link support: /booking/pay?id=87&step=2
            var stepParam = parseInt(salonPayGetQueryParam('step') || '1', 10);
            if (stepParam === 2) {
                // Uses the same behavior as clicking Checkout tab / Next button.
                if (typeof window.salonPaySaveAndGoToCheckout === 'function') {
                    window.salonPaySaveAndGoToCheckout();
                } else {
                    window.salonPaySwitchStep(2);
                }
            } else {
                salonPaySetStepInUrl(1);
            }

            return;
        }
    }
    try {
        var aptRes = await fetch(base + '/appointments');
        var custRes = await fetch(base + '/customers');
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
        availableCredits = customerData.creditBalance != null ? parseFloat(customerData.creditBalance) || 0 : 0;
        if (appointmentData.assigned_technician && Array.isArray(appointmentData.assigned_technician)) {
            assignedTechnicianIds = appointmentData.assigned_technician.map(function(id) { return id.toString(); });
            if (assignedTechnicianIds.length > 0) selectedTechnicianId = assignedTechnicianIds[0];
        }
        await salonPayFetchCategoriesAndServices();
        await salonPayFetchTechnicians();
        salonPayBuildCartFromAppointmentServices();
        salonPayUpdateCustomerInfoHeader();
        salonPayFetchDiscountSettings();

        // Deep-link support: /booking/pay?id=87&step=2
        var stepParam = parseInt(salonPayGetQueryParam('step') || '1', 10);
        if (stepParam === 2) {
            // Uses the same behavior as clicking Checkout tab / Next button.
            if (typeof window.salonPaySaveAndGoToCheckout === 'function') {
                window.salonPaySaveAndGoToCheckout();
            } else {
                window.salonPaySwitchStep(2);
            }
        } else {
            salonPaySetStepInUrl(1);
        }
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
function salonPayBuildCartFromAppointmentServices() {
    if (!appointmentData || !appointmentData.services || !appointmentData.services.length) return;
    cart = [];
    appointmentData.services.forEach(function(item) {
        var slug = item.service;
        var technicianIdStr = (item.technician_id || item.technician_id === 0) ? item.technician_id.toString() : null;
        if (!technicianIdStr) return;
        var service = null;
        if (item.service_id != null && item.service_id !== '') {
            service = servicesData.find(function(s) { return s.id == item.service_id; }) || null;
        }
        if (!service) {
            service = servicesData.find(function(s) { return s.categories && s.categories.indexOf(slug) >= 0; }) || null;
        }
        var name = service ? service.name : (item.service_name || categoriesMap[slug] || slug);
        var price = item.unit_price != null ? parseFloat(item.unit_price) : (service ? parseFloat(service.price) : 0);
        var qty = Math.max(1, parseInt(item.quantity, 10) || 1);
        cart.push({
            name: name,
            price: price,
            quantity: qty,
            technician_id: technicianIdStr,
            category_slug: slug,
            service_id: service ? service.id : (item.service_id != null && item.service_id !== '' ? parseInt(item.service_id, 10) : null)
        });
    });
    salonPayRenderTechniciansList();
    salonPayInitializeServicesList();
    // Treat loaded cart as "saved" baseline.
    lastSavedCartSignature = salonPayMakeStableCartSignature(salonPayCartToServicesPayload());
}
function salonPayShowError(message) {
    var container = document.querySelector('main .p-4, main .p-6, main .p-8');
    if (container) {
        container.innerHTML = '<div class="text-center py-12"><svg class="w-16 h-16 text-red-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg><p class="text-red-500 text-sm mb-4">' + message + '</p><a href="' + ticketsUrl + '" class="inline-block px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition">Back to Tickets</a></div>';
    }
}
async function salonPayFetchCategoriesAndServices() {
    if (salonPayApplyBootstrapCatalog()) {
        return;
    }
    try {
        var catRes = await fetch(base + '/service-categories');
        var svcRes = await fetch(base + '/services');
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
    if (salonPayApplyBootstrapCatalog()) {
        return;
    }
    try {
        var res = await fetch(base + '/users');
        var data = await res.json();
        techniciansData = (data.users || []).filter(function(u) {
            return (u.role === 'technician' || u.userlevel === 'technician') && (u.status === 'active' || !u.status);
        });
        techniciansData.forEach(function(t) {
            if (!(t.id.toString() in initialTechServices)) {
                initialTechServices[t.id.toString()] = typeof t.services === 'number' ? t.services : 0;
            }
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

    // Generate category buttons wrapped in divs for Slick carousel
    // Fixed height: 70px, text wrapping enabled, centered content
    var html = '<div><button type="button" onclick="salonPayFilterByCategory(null)" class="category-card w-full h-[70px] px-4 py-2 bg-[#e6f0f3] border border-[#003047] text-[#003047] rounded-lg hover:bg-[#e6f0f3] hover:text-[#003047] transition-all duration-200 font-medium text-sm shadow-sm active:scale-95 flex items-center justify-center text-center break-words" data-category-key="all">All Categories</button></div>';

    var sorted = Object.entries(categoriesMap).sort(function(a, b) { return a[1].localeCompare(b[1]); });
    sorted.forEach(function(entry) {
        var key = entry[0], displayName = entry[1];
        html += '<div><button type="button" onclick="salonPayFilterByCategory(\'' + key + '\')" class="category-card w-full h-[70px] px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg hover:border-[#003047] hover:bg-[#e6f0f3] hover:text-[#003047] transition-all duration-200 font-medium text-sm active:scale-95 flex items-center justify-center text-center break-words" data-category-key="' + key + '">' + displayName + '</button></div>';
    });

    list.innerHTML = html;

    // Initialize Slick carousel after DOM update
    setTimeout(function() {
        if (typeof window.salonPayInitializeSlickCarousel === 'function') {
            window.salonPayInitializeSlickCarousel();
        } else {
            console.warn('salonPayInitializeSlickCarousel function not available');
            // Fallback: show carousel if Slick function not available
            var carousel = document.getElementById('categoriesList');
            if (carousel) {
                carousel.classList.add('show-fallback');
                carousel.style.opacity = '1';
                carousel.style.visibility = 'visible';
            }
        }

        // Safety fallback: ensure carousel is visible after 3 seconds
        setTimeout(function() {
            var carousel = document.getElementById('categoriesList');
            if (carousel) {
                var isVisible = carousel.style.visibility === 'visible' ||
                               window.getComputedStyle(carousel).visibility === 'visible';
                var hasSlick = carousel.classList.contains('slick-initialized');

                if (!isVisible || !hasSlick) {
                    console.warn('Slick carousel fallback: forcing visibility without Slick');
                    carousel.classList.add('show-fallback');
                    carousel.style.opacity = '1';
                    carousel.style.visibility = 'visible';
                }
            }
        }, 3000);
    }, 100);
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
            var color = colorClasses[index % colorClasses.length];
            var isInCart = selectedTechnicianId ? cart.find(function(item) {
                return (item.service_id != null ? item.service_id === service.id : item.name === service.name) && item.technician_id === selectedTechnicianId;
            }) : null;
            var categorySlug = (service.categories && service.categories[0]) ? String(service.categories[0]).replace(/'/g, "\\'") : '';
            var sid = (service.id != null) ? service.id : '';

            // Get service image URL (matching services page pattern)
            var storageUrl = window.salonStorageUrl || '';
            var imgUrl = (service.image ? (storageUrl + '/' + service.image) : null) || service.image_url || null;
            var thumbnailHtml = '';

            if (imgUrl) {
                // If service has an image, display it with wrapper and object-cover
                thumbnailHtml = '<div class="flex items-start"><img src="' + imgUrl.replace(/"/g, '&quot;').replace(/'/g, '&#39;') + '" alt="" class="w-full h-32 object-cover rounded-lg" onerror="this.parentElement.style.display=\'none\'"></div>';
            }
            // No wrapper or image if no image URL exists

            html += '<div class="service-item bg-white border border-gray-200 rounded-lg overflow-hidden hover:border-[#003047] hover:shadow-md transition-all flex flex-col h-full">' +
                thumbnailHtml +
                '<div class="p-4 flex flex-col flex-1">' +
                '<div class="flex-1">' +
                '<h3 class="text-lg font-semibold text-gray-900 mb-1 flex items-center gap-2">' + salonPayServiceColorDot(service.id) + service.name + '</h3>' +
                '<p class="text-lg font-normal text-gray-600 mb-3">' + window.salonFormatMoney(service.price) + '</p>' +
                '</div>' +
                '<button type="button" onclick="salonPayAddServiceToCart(\'' + service.name.replace(/'/g, "\\'") + '\', ' + service.price + ', \'' + categorySlug + '\', ' + sid + ')" class="w-full px-6 py-3 ' + (!selectedTechnicianId ? 'bg-gray-400 cursor-not-allowed' : isInCart ? 'bg-green-600 hover:bg-green-700' : 'bg-[#003047] hover:bg-[#002535]') + ' text-white rounded-lg transition font-medium text-sm active:scale-95 flex items-center justify-center gap-2 mt-auto" ' + (!selectedTechnicianId ? 'disabled title="Please select a technician first"' : '') + '>' +
                '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>' +
                (!selectedTechnicianId ? 'Select Technician First' : isInCart ? 'In Cart (' + isInCart.quantity + 'x)' : 'Add to Cart') +
                '</button>' +
                '</div>' +
                '</div>';
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
function salonPayResolveSlugForItem(item) {
    if (item.service_id != null) {
        var svc = servicesData.find(function(s) { return s.id == item.service_id; });
        if (svc && svc.categories && svc.categories[0]) return String(svc.categories[0]);
    }
    if (item.category_slug) return String(item.category_slug);
    var svcByName = servicesData.find(function(s) { return s.name === item.name; });
    if (svcByName && svcByName.categories && svcByName.categories[0]) return String(svcByName.categories[0]);
    return '';
}
function salonPayCartToServicesPayload() {
    return cart.map(function(item) {
        var slug = salonPayResolveSlugForItem(item);
        return {
            service: slug,
            service_id: item.service_id != null ? parseInt(item.service_id, 10) : null,
            technician_id: parseInt(item.technician_id, 10),
            quantity: item.quantity,
            unit_price: parseFloat(item.price)
        };
    }).filter(function(s) { return s.service && s.technician_id; });
}

function salonPaySetButtonBusy(btn, isBusy) {
    if (!btn) return;
    btn.disabled = !!isBusy;
    if (isBusy) btn.classList.add('opacity-50', 'cursor-not-allowed');
    else btn.classList.remove('opacity-50', 'cursor-not-allowed');
}

function salonPayEscapeHtml(str) {
    return (str == null ? '' : String(str))
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function salonPayNormalizeCouponCode(code) {
    return (code || '').toString().trim().toUpperCase();
}

function salonPayNormalizeGiftCardCode(code) {
    return (code || '').toString().trim().toUpperCase();
}

function salonPayFetchDiscountSettings() {
    if (salonPayApplyBootstrapSettings()) {
        return Promise.resolve(discountsEnabled);
    }
    return fetch(apiBase + '/settings').then(function(r) { return r.json(); }).then(function(payload) {
        var data = payload && payload.data ? payload.data : {};
        var enabled = data.discounts_enabled;
        discountsEnabled = enabled === true || enabled === 1 || enabled === '1' || enabled === 'true';
        var giftEnabled = data.gift_cards_enabled;
        giftCardsEnabled = giftEnabled === true || giftEnabled === 1 || giftEnabled === '1' || giftEnabled === 'true';
        var rate = parseFloat(data.tax_rate);
        taxRate = isNaN(rate) ? 0 : rate;
        taxName = data.tax_name ? String(data.tax_name) : 'Tax';
        var applyAll = data.tax_apply_to_all;
        taxApplyToAll = applyAll === true || applyAll === 1 || applyAll === '1' || applyAll === 'true';
        salonPayUpdateTaxLabel();
        salonPayUpdateActionButtonsState();
        salonPayRefreshTotalsForTaxChange();
        return discountsEnabled;
    }).catch(function() {
        discountsEnabled = true;
        giftCardsEnabled = true;
        taxRate = 0;
        taxName = 'Tax';
        taxApplyToAll = true;
        salonPayUpdateTaxLabel();
        salonPayUpdateActionButtonsState();
        salonPayRefreshTotalsForTaxChange();
        return discountsEnabled;
    });
}

function salonPayFetchCoupons() {
    if (bootstrap && Array.isArray(bootstrap.coupons)) {
        couponsData = bootstrap.coupons.filter(function(c) { return c && c.active !== false; });
        return Promise.resolve(couponsData);
    }
    return fetch(apiBase + '/coupons').then(function(r) { return r.json(); }).then(function(payload) {
        var items = (payload && payload.data) ? payload.data : [];
        couponsData = Array.isArray(items) ? items.filter(function(c) { return c && c.active !== false; }) : [];
        return couponsData;
    }).catch(function() {
        couponsData = [];
        return couponsData;
    });
}

function salonPayFetchGiftCards() {
    if (bootstrap && Array.isArray(bootstrap.gift_cards)) {
        giftCardsData = bootstrap.gift_cards.filter(function(c) { return c && c.active !== false; });
        return Promise.resolve(giftCardsData);
    }
    return fetch(apiBase + '/gift-cards').then(function(r) { return r.json(); }).then(function(payload) {
        var items = (payload && payload.data) ? payload.data : [];
        giftCardsData = Array.isArray(items) ? items.filter(function(c) { return c && c.active !== false; }) : [];
        return giftCardsData;
    }).catch(function() {
        giftCardsData = [];
        return giftCardsData;
    });
}

function salonPayEnsureDiscountDataLoaded() {
    if (couponsLoading) return couponsLoading;
    couponsLoading = Promise.all([salonPayFetchDiscountSettings(), salonPayFetchCoupons()]).finally(function() {
        couponsLoading = null;
    });
    return couponsLoading;
}

function salonPayEnsureGiftCardsLoaded() {
    if (giftCardsLoading) return giftCardsLoading;
    giftCardsLoading = salonPayFetchGiftCards().finally(function() {
        giftCardsLoading = null;
    });
    return giftCardsLoading;
}

function salonPayFindCouponByCode(code) {
    var normalized = salonPayNormalizeCouponCode(code);
    if (!normalized) return null;
    return couponsData.find(function(c) {
        return salonPayNormalizeCouponCode(c.code) === normalized;
    }) || null;
}

function salonPayFindGiftCardByCode(code) {
    var normalized = salonPayNormalizeGiftCardCode(code);
    if (!normalized) return null;
    return giftCardsData.find(function(c) {
        return salonPayNormalizeGiftCardCode(c.code) === normalized;
    }) || null;
}

function salonPayCalculateDiscountFromCoupon(coupon) {
    if (!coupon) return 0;
    var discountAmount = 0;
    if (coupon.discount_type === 'percent') {
        discountAmount = paymentSubtotal * (Number(coupon.discount_value) / 100);
    } else {
        discountAmount = Number(coupon.discount_value) || 0;
    }
    if (discountAmount > paymentSubtotal) discountAmount = paymentSubtotal;
    return discountAmount;
}

function salonPaySetDiscountMessage(message, isError) {
    var msg = document.getElementById('discountCodeMessage');
    if (!msg) return;
    msg.textContent = message || '';
    msg.classList.remove('text-red-500', 'text-gray-500', 'text-green-600');
    if (isError) msg.classList.add('text-red-500');
    else if (message) msg.classList.add('text-green-600');
    else msg.classList.add('text-gray-500');
}

function salonPaySetGiftCardMessage(message, isError) {
    var msg = document.getElementById('giftCardMessage');
    if (!msg) return;
    msg.textContent = message || '';
    msg.classList.remove('text-red-500', 'text-gray-500', 'text-green-600');
    if (isError) msg.classList.add('text-red-500');
    else if (message) msg.classList.add('text-green-600');
    else msg.classList.add('text-gray-500');
}

function salonPaySetRedeemMessage(message, isError) {
    var msg = document.getElementById('redeemMessage');
    if (!msg) return;
    msg.textContent = message || '';
    msg.classList.remove('text-red-500', 'text-gray-500', 'text-green-600');
    if (isError) msg.classList.add('text-red-500');
    else if (message) msg.classList.add('text-green-600');
    else msg.classList.add('text-gray-500');
}

function salonPayGetEffectiveTaxRate() {
    if (!taxApplyToAll) return 0;
    return taxRate > 0 ? taxRate : 0;
}

function salonPayUpdateTaxLabel() {
    var label = document.getElementById('checkoutTaxLabel');
    if (!label) return;
    var rate = salonPayGetEffectiveTaxRate();
    if (rate > 0) label.textContent = taxName + ' (' + rate.toFixed(2).replace(/\.00$/, '') + '%)';
    else label.textContent = taxName;
}

function salonPaySetActionButtonState(buttonId, isEnabled) {
    var btn = document.getElementById(buttonId);
    if (!btn) return;
    btn.classList.remove('opacity-60', 'bg-gray-50', 'text-gray-400', 'border-gray-200', 'hover:border-[#003047]');
    btn.classList.add('border-gray-200');
    if (isEnabled) {
        btn.classList.add('hover:border-[#003047]');
        btn.removeAttribute('aria-disabled');
        btn.setAttribute('data-disabled', '0');
    } else {
        btn.classList.add('opacity-60', 'bg-gray-50');
        btn.setAttribute('aria-disabled', 'true');
        btn.setAttribute('data-disabled', '1');
    }
    var text = btn.querySelector('span');
    if (text) {
        text.classList.remove('text-gray-400');
        if (!isEnabled) text.classList.add('text-gray-400');
    }
    var icon = btn.querySelector('svg');
    if (icon) {
        icon.classList.remove('text-gray-400');
        if (!isEnabled) icon.classList.add('text-gray-400');
    }
}

function salonPayUpdateActionButtonsState() {
    // Do not disable the "Discount" action button. If discount codes (coupons)
    // are disabled in settings, we disable the CODE tab inside the modal instead.
    salonPaySetActionButtonState('discountActionBtn', true);
    salonPaySetActionButtonState('redeemActionBtn', true);
    salonPaySetActionButtonState('giftCardActionBtn', giftCardsEnabled);
}

function salonPayRefreshTotalsForTaxChange() {
    var step2 = document.getElementById('step2Content');
    if (step2 && !step2.classList.contains('hidden')) {
        salonPayUpdatePaymentTotal();
    }
}

function salonPayGetTotalBeforeGiftCard() {
    var discountedSubtotal = paymentSubtotal - paymentDiscount;
    var totalTipSplitAmount = salonPayCalculateTotalTipSplit();
    paymentTax = discountedSubtotal * (salonPayGetEffectiveTaxRate() / 100);
    var totalBeforeCredits = discountedSubtotal + paymentTax + totalTipSplitAmount;
    return totalBeforeCredits - paymentCredits;
}

window.salonPayHandleDiscountCodeInput = function(value) {
    var code = salonPayNormalizeCouponCode(value);
    if (!discountsEnabled) {
        salonPaySetDiscountMessage('Discount codes are disabled in settings.', true);
        return;
    }
    if (!code) {
        salonPaySetDiscountMessage('', false);
        return;
    }
    var coupon = salonPayFindCouponByCode(code);
    if (!coupon) {
        salonPaySetDiscountMessage('No matching discount code found.', true);
        return;
    }
    if (coupon.min_order_amount && paymentSubtotal < Number(coupon.min_order_amount)) {
        salonPaySetDiscountMessage('Minimum order is ' + window.salonFormatMoney(Number(coupon.min_order_amount)) + '.', true);
        return;
    }
    var discountAmount = salonPayCalculateDiscountFromCoupon(coupon);
    salonPaySetDiscountMessage('Applied ' + coupon.code + ' for ' + window.salonFormatMoney(discountAmount) + ' off.', false);
};

// --- Assign Technicians Modal (copied from Waiting List UI/UX) ---
var payAvailableTechnicians = [];
var payTechnicianSearchTerm = '';

window.salonPayOpenAssignTechnicianModal = function() {
    if (!appointmentId) return;
    payTechnicianSearchTerm = '';

    var customerName = customerData ? ((customerData.firstName || '') + ' ' + (customerData.lastName || '')).trim() : '';
    var safeName = customerName.replace(/'/g, "\\'");

    var modalHtml = '<div class="p-6"><div class="flex items-center justify-between mb-6"><h3 class="text-xl font-bold text-gray-900">Assign Technician to ' + safeName + '</h3><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><div class="grid grid-cols-2 gap-6"><div class="border border-gray-200 rounded-lg p-4"><div class="flex items-center justify-between mb-2"><h4 class="text-sm font-semibold text-gray-900">Available Technicians</h4><span id="availableCount" class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">0</span></div><p class="text-xs text-gray-500 mb-2">Click to assign technicians</p><div class="relative mb-4"><svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg><input type="text" id="technicianSearchInput" placeholder="Search technicians..." oninput="window.salonPaySearchTechnicians(this.value)" class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] text-sm"><button id="clearTechnicianSearchBtn" onclick="window.salonPayClearTechnicianSearch()" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><div id="availableTechniciansContainer" class="space-y-3 min-h-[200px] max-h-[300px] overflow-y-auto"></div></div><div class="border border-gray-200 rounded-lg p-4"><div class="flex items-center justify-between mb-2"><h4 class="text-sm font-semibold text-gray-900">Assigned Technicians</h4><span id="assignedCount" class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">0</span></div><p class="text-xs text-gray-500 mb-4">Click to remove</p><div id="assignedTechniciansContainer" class="space-y-3 min-h-[200px] max-h-[300px] overflow-y-auto"></div></div></div><div class="pt-6 mt-6 border-t border-gray-200"><div class="flex items-center justify-end gap-3"><button onclick="closeModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium">Cancel</button><button onclick="window.salonPayConfirmAssignTechnicians()" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium">Save Assignment</button></div></div></div>';

    openModal(modalHtml, 'large', false);
    setTimeout(function() {
        var modal = document.getElementById('modalContainer');
        if (modal) modal.style.maxHeight = '95vh';
        salonPayLoadTechniciansForAssign();
        var searchInput = document.getElementById('technicianSearchInput');
        var clearBtn = document.getElementById('clearTechnicianSearchBtn');
        if (searchInput && clearBtn) clearBtn.classList.add('hidden');
    }, 50);
};

function salonPayLoadTechniciansForAssign() {
    // Prefer already-loaded technicians from the Pay page.
    if (techniciansData && techniciansData.length) {
        payAvailableTechnicians = techniciansData.slice();
        salonPayRenderAvailableTechnicians();
        salonPayRenderAssignedTechnicians();
        salonPayUpdateTechnicianCounts();
        return;
    }

    fetch(base + '/users').then(function(r) { return r.json(); }).then(function(data) {
        payAvailableTechnicians = (data.users || []).filter(function(u) { return u.role === 'technician' || u.userlevel === 'technician'; });
        salonPayRenderAvailableTechnicians();
        salonPayRenderAssignedTechnicians();
        salonPayUpdateTechnicianCounts();
    }).catch(function(err) {
        console.error(err);
        var c = document.getElementById('availableTechniciansContainer');
        if (c) c.innerHTML = '<div class="text-center py-8 text-sm text-gray-400">Error loading technicians</div>';
    });
}

window.salonPaySearchTechnicians = function(val) {
    payTechnicianSearchTerm = (val || '').toLowerCase().trim();
    var btn = document.getElementById('clearTechnicianSearchBtn');
    if (btn) {
        if (val && val.trim()) btn.classList.remove('hidden');
        else btn.classList.add('hidden');
    }
    salonPayRenderAvailableTechnicians();
};

window.salonPayClearTechnicianSearch = function() {
    var inp = document.getElementById('technicianSearchInput');
    var btn = document.getElementById('clearTechnicianSearchBtn');
    if (inp) { inp.value = ''; payTechnicianSearchTerm = ''; inp.focus(); }
    if (btn) btn.classList.add('hidden');
    salonPayRenderAvailableTechnicians();
};

function salonPayRenderAvailableTechnicians() {
    var container = document.getElementById('availableTechniciansContainer');
    if (!container) return;
    if (!payAvailableTechnicians.length) {
        container.innerHTML = '<div class="flex items-center justify-center h-full min-h-[200px]"><p class="text-sm text-gray-400">No technicians available</p></div>';
        return;
    }
    var filtered = payTechnicianSearchTerm ? payAvailableTechnicians.filter(function(t) {
        var name = (t.firstName + ' ' + t.lastName).toLowerCase();
        var inits = (t.initials || (t.firstName || '')[0] + (t.lastName || '')[0]).toLowerCase();
        return (name + ' ' + inits).indexOf(payTechnicianSearchTerm) >= 0;
    }) : payAvailableTechnicians;

    if (!filtered.length) {
        container.innerHTML = '<div class="flex items-center justify-center h-full min-h-[200px]"><p class="text-sm text-gray-400">No technicians found</p></div>';
        return;
    }

    filtered.sort(function(a, b) {
        var aIdStr = a.id.toString(), bIdStr = b.id.toString();
        var aIsAssigned = assignedTechnicianIds.indexOf(aIdStr) >= 0;
        var bIsAssigned = assignedTechnicianIds.indexOf(bIdStr) >= 0;
        if (aIsAssigned && !bIsAssigned) return 1;
        if (!aIsAssigned && bIsAssigned) return -1;

        var aOnline = !!(a.clock_in && !a.clock_out);
        var bOnline = !!(b.clock_in && !b.clock_out);
        if (aOnline && !bOnline) return -1;
        if (!aOnline && bOnline) return 1;

        var aServices = typeof a.services === 'number' ? a.services : 0;
        var bServices = typeof b.services === 'number' ? b.services : 0;
        var diff = aServices - bServices;
        if (diff !== 0) return diff;

        var aTime = a.clock_in ? new Date(a.clock_in).getTime() : Infinity;
        var bTime = b.clock_in ? new Date(b.clock_in).getTime() : Infinity;
        return aTime - bTime;
    });

    container.innerHTML = filtered.map(function(tech) {
        var idStr = tech.id.toString(), isAssigned = assignedTechnicianIds.indexOf(idStr) >= 0;
        var inits = tech.initials || (tech.firstName || '')[0] + (tech.lastName || '')[0];
        var techPhoto = tech.profilePhotoUrl || tech.photo || null;
        var name = tech.firstName + ' ' + tech.lastName;
        var containerCls = isAssigned ? 'flex items-center gap-3 p-2 rounded-lg transition-colors opacity-50 grayscale cursor-pointer group hover:bg-gray-100' : 'flex items-center gap-3 cursor-pointer group hover:bg-gray-50 p-2 rounded-lg transition-colors';
        var avatarCls = isAssigned ? 'w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center' : 'w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center';
        var initialCls = isAssigned ? 'text-sm font-bold text-gray-500' : 'text-sm font-bold text-gray-600';
        var nameCls = isAssigned ? 'text-base font-medium text-gray-400' : 'text-base font-medium text-gray-900';
        var isOnline = !!(tech.clock_in && !tech.clock_out);
        var badgeCls = isAssigned ? 'absolute w-5 h-5 rounded-full border-2 border-white bg-gray-400' : (isOnline ? 'absolute w-5 h-5 rounded-full border-2 border-white bg-green-500' : 'absolute w-5 h-5 rounded-full border-2 border-white bg-gray-400');
        var badgeStyle = 'bottom: -5px; right: -5px;';
        var servicesNum = typeof tech.services === 'number' ? tech.services : 0;
        var avatarHtml = techPhoto ? '<img src="' + techPhoto + '" alt="" class="w-12 h-12 rounded-full object-cover">' : '<div class="' + avatarCls + '"><span class="' + initialCls + '">' + inits + '</span></div>';
        return '<div onclick="' + (isAssigned ? 'salonPayRemoveAssignedTechnician(' + tech.id + ')' : 'salonPayAssignTechnician(' + tech.id + ')') + '" class="' + containerCls + '"><div class="relative flex-shrink-0">' + avatarHtml + '<div class="' + badgeCls + '" style="' + badgeStyle + '" title="' + (isOnline ? 'Online' : 'Offline') + '"></div></div><div class="flex-1 min-w-0"><p class="' + nameCls + '">' + name + '</p></div><div class="flex-shrink-0 text-right"><div class="text-xs font-medium text-gray-500 uppercase">Services</div><div class="text-lg font-semibold text-gray-900">' + servicesNum + '</div></div></div>';
    }).join('');
}

function salonPayRenderAssignedTechnicians() {
    var container = document.getElementById('assignedTechniciansContainer');
    if (!container) return;
    if (!assignedTechnicianIds.length) {
        container.innerHTML = '<div class="flex items-center justify-center h-full min-h-[200px]"><p class="text-sm text-gray-400">No technicians assigned</p></div>';
        return;
    }
    container.innerHTML = assignedTechnicianIds.map(function(idStr) {
        var tech = payAvailableTechnicians.find(function(t) { return t.id.toString() === idStr; });
        if (!tech) return '';
        var inits = tech.initials || (tech.firstName || '')[0] + (tech.lastName || '')[0];
        var techPhoto = tech.profilePhotoUrl || tech.photo || null;
        var name = tech.firstName + ' ' + tech.lastName;
        var isOnline = !!(tech.clock_in && !tech.clock_out);
        var badgeCls = isOnline ? 'absolute w-5 h-5 rounded-full border-2 border-white bg-green-500' : 'absolute w-5 h-5 rounded-full border-2 border-white bg-gray-400';
        var badgeStyle = 'bottom: -5px; right: -5px;';
        var servicesNum = typeof tech.services === 'number' ? tech.services : 0;
        var assignedAvatarHtml = techPhoto ? '<img src="' + techPhoto + '" alt="" class="w-12 h-12 rounded-full object-cover">' : '<div class="w-12 h-12 bg-[#003047] rounded-full flex items-center justify-center"><span class="text-sm font-bold text-white">' + inits + '</span></div>';
        return '<div onclick="salonPayRemoveAssignedTechnician(' + tech.id + ')" class="flex items-center gap-3 cursor-pointer group hover:bg-gray-50 p-2 rounded-lg transition-colors"><div class="relative flex-shrink-0">' + assignedAvatarHtml + '<div class="' + badgeCls + '" style="' + badgeStyle + '" title="' + (isOnline ? 'Online' : 'Offline') + '"></div></div><div class="flex-1 min-w-0"><p class="text-base font-medium text-gray-900">' + name + '</p></div><div class="flex-shrink-0 text-right"><div class="text-xs font-medium text-gray-500 uppercase">Services</div><div class="text-lg font-semibold text-gray-900">' + servicesNum + '</div></div></div>';
    }).join('');
}

window.salonPayAssignTechnician = function(techId) {
    var idStr = techId.toString();
    if (assignedTechnicianIds.indexOf(idStr) < 0) {
        assignedTechnicianIds.push(idStr);
        if (!selectedTechnicianId) selectedTechnicianId = idStr;
        salonPayRenderAvailableTechnicians();
        salonPayRenderAssignedTechnicians();
        salonPayUpdateTechnicianCounts();
    }
};

window.salonPayRemoveAssignedTechnician = function(techId) {
    var idStr = techId.toString();
    assignedTechnicianIds = assignedTechnicianIds.filter(function(id) { return id !== idStr; });
    if (selectedTechnicianId === idStr) {
        selectedTechnicianId = assignedTechnicianIds.length ? assignedTechnicianIds[0] : null;
    }
    salonPayRenderAvailableTechnicians();
    salonPayRenderAssignedTechnicians();
    salonPayUpdateTechnicianCounts();
};

function salonPayUpdateTechnicianCounts() {
    var availEl = document.getElementById('availableCount');
    var assignEl = document.getElementById('assignedCount');
    if (availEl) availEl.textContent = payAvailableTechnicians.length.toString();
    if (assignEl) assignEl.textContent = assignedTechnicianIds.length.toString();
}

window.salonPayConfirmAssignTechnicians = function() {
    if (!appointmentId) return;
    if (!apiAppointmentsUrl || typeof salonApi === 'undefined' || !salonApi.put) {
        alert('Save is not available right now.');
        return;
    }
    var ids = (assignedTechnicianIds || []).map(function(x) { return parseInt(x, 10); }).filter(function(n) { return !isNaN(n); });
    salonApi.put(apiAppointmentsUrl + '/' + appointmentId, { assigned_technician: ids }).then(function(res) {
        if (res && res.data && Array.isArray(res.data.assigned_technician)) {
            assignedTechnicianIds = res.data.assigned_technician.map(function(x) { return x.toString(); });
            appointmentData.assigned_technician = res.data.assigned_technician;
            if (!selectedTechnicianId || assignedTechnicianIds.indexOf(selectedTechnicianId) < 0) {
                selectedTechnicianId = assignedTechnicianIds.length ? assignedTechnicianIds[0] : null;
            }
        }
        salonPayRenderTechniciansList();
        salonPayInitializeServicesList();
        if (typeof showSuccessMessage === 'function') showSuccessMessage('Assignment saved.');
        closeModal();
    }).catch(function(err) {
        if (typeof showErrorMessage === 'function') showErrorMessage(err.message || 'Failed to save assignment.');
        else alert(err.message || 'Failed to save assignment.');
    });
};

function salonPaySaveCartRequest(options) {
    var opts = options || {};
    if (!appointmentId) {
        return Promise.reject(new Error('No appointment ID provided.'));
    }
    if (!apiAppointmentsUrl || typeof salonApi === 'undefined' || !salonApi.put) {
        return Promise.reject(new Error('Save is not available right now.'));
    }
    var servicesPayload = salonPayCartToServicesPayload();
    var signature = salonPayMakeStableCartSignature(servicesPayload);
    var payload = { services: servicesPayload };
    return salonApi.put(apiAppointmentsUrl + '/' + appointmentId + '/services', payload).then(function(res) {
        if (res && res.data && res.data.services) {
            appointmentData.services = res.data.services;
        }
        lastSavedCartSignature = signature;

        // Update turn tracker services for each assigned technician
        var entries = [];
        assignedTechnicianIds.forEach(function(idStr) {
            var technician = techniciansData.find(function(t) { return t.id.toString() === idStr; });
            if (!technician) return;
            var baseServices = idStr in initialTechServices ? initialTechServices[idStr] : (typeof technician.services === 'number' ? technician.services : 0);
            var techCartItems = cart.filter(function(item) { return item.technician_id === idStr; });
            var cartServiceCount = techCartItems.reduce(function(sum, item) {
                var svc = servicesData.find(function(s) { return s.id == item.service_id; });
                var sc = svc && typeof svc.service_count === 'number' ? svc.service_count : 0;
                return sum + (sc * (item.quantity || 1));
            }, 0);
            entries.push({ user_id: parseInt(idStr, 10), services: baseServices + cartServiceCount });
        });
        if (entries.length > 0 && salonApi.put) {
            var turnTrackerUrl = base.replace(/\/data\/?$/, '') + '/turn-tracker';
            salonApi.put(turnTrackerUrl, { entries: entries }).catch(function(err) { console.error('Turn tracker update failed:', err); });
        }

        if (!opts.silentSuccess) {
            if (typeof showSuccessMessage === 'function') showSuccessMessage('Cart saved successfully.');
            else alert('Cart saved successfully.');
        }
        return res;
    });
}

window.salonPaySaveCart = function() {
    if (!appointmentId) {
        alert('No appointment ID provided.');
        return;
    }
    if (!apiAppointmentsUrl || typeof salonApi === 'undefined' || !salonApi.put) {
        alert('Save is not available right now.');
        return;
    }

    var btn = document.getElementById('salonPaySaveCartBtn');
    salonPaySetButtonBusy(btn, true);

    salonPaySaveCartRequest({ silentSuccess: false }).catch(function(err) {
        if (typeof showErrorMessage === 'function') showErrorMessage(err.message || 'Failed to save cart.');
        else alert(err.message || 'Failed to save cart.');
    }).finally(function() {
        salonPaySetButtonBusy(btn, false);
    });
};

window.salonPaySaveAndGoToCheckout = function() {
    if (cart.length === 0) {
        alert('Please add at least one service to your cart before checkout.');
        return;
    }

    var currentSignature = salonPayMakeStableCartSignature(salonPayCartToServicesPayload());
    if (lastSavedCartSignature && currentSignature === lastSavedCartSignature) {
        salonPaySwitchStep(2);
        return;
    }

    var nextBtn = document.getElementById('salonPayNextToCheckoutBtn');
    var saveBtn = document.getElementById('salonPaySaveCartBtn');
    salonPaySetButtonBusy(nextBtn, true);
    salonPaySetButtonBusy(saveBtn, true);

    salonPaySaveCartRequest({ silentSuccess: true }).then(function() {
        salonPaySwitchStep(2);
    }).catch(function(err) {
        if (typeof showErrorMessage === 'function') showErrorMessage(err.message || 'Failed to save cart.');
        else alert(err.message || 'Failed to save cart.');
    }).finally(function() {
        salonPaySetButtonBusy(nextBtn, false);
        salonPaySetButtonBusy(saveBtn, false);
    });
};

window.salonPayAddServiceToCart = function(serviceName, servicePrice, categorySlug, serviceId) {
    if (!selectedTechnicianId) {
        alert('Please select a technician first before adding services.');
        return;
    }
    var slug = '';
    if (serviceId != null && serviceId !== '') {
        var svc = servicesData.find(function(s) { return s.id == serviceId; });
        if (svc) {
            slug = (svc.categories && svc.categories[0]) ? String(svc.categories[0]) : (categorySlug || '');
        } else {
            slug = categorySlug || '';
        }
    } else {
        slug = categorySlug || (servicesData.find(function(s) { return s.name === serviceName; }) && servicesData.find(function(s) { return s.name === serviceName; }).categories && servicesData.find(function(s) { return s.name === serviceName; }).categories[0]) || '';
    }
    var existing = cart.find(function(item) {
        if (serviceId != null && serviceId !== '') return item.service_id == serviceId && item.technician_id === selectedTechnicianId;
        return item.name === serviceName && item.technician_id === selectedTechnicianId;
    });
    if (existing) {
        existing.quantity += 1;
    } else {
        cart.push({
            name: serviceName,
            price: servicePrice,
            quantity: 1,
            technician_id: selectedTechnicianId,
            category_slug: slug,
            service_id: (serviceId != null && serviceId !== '') ? parseInt(serviceId, 10) : null
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
        var photo = technician.profilePhotoUrl || technician.photo || null;
        var techServices = cart.filter(function(item) { return item.technician_id === idStr; });
        var baseServices = idStr in initialTechServices ? initialTechServices[idStr] : (typeof technician.services === 'number' ? technician.services : 0);
        var cartServiceCount = techServices.reduce(function(sum, item) {
            var svc = servicesData.find(function(s) { return s.id == item.service_id; });
            var sc = svc && typeof svc.service_count === 'number' ? svc.service_count : 0;
            return sum + (sc * (item.quantity || 1));
        }, 0);
        var totalServices = baseServices + cartServiceCount;
        html += '<div onclick="salonPaySelectTechnician(\'' + idStr + '\')" class="flex flex-col gap-2 p-3 rounded-lg border-2 cursor-pointer transition ' + (isActive ? 'border-[#003047] bg-white' : 'border-gray-200 bg-white hover:bg-gray-50') + '"><div class="flex items-center gap-3"><div class="relative flex-shrink-0">' + (photo ? '<img src="' + photo + '" alt="' + fullName + '" class="w-12 h-12 rounded-full object-cover border-2 border-white">' : '<div class="w-12 h-12 bg-[#e6f0f3] rounded-full flex items-center justify-center border-2 border-white"><span class="text-sm font-bold text-[#003047]">' + initials + '</span></div>') + '<div class="absolute -bottom-1 -right-1 w-5 h-5 bg-[#003047] text-white rounded-full flex items-center justify-center text-xs font-bold border-2 border-white">✓</div></div><div class="flex-1 min-w-0"><p class="text-sm font-medium text-gray-900 truncate">' + fullName + '</p><p class="text-xs text-gray-500">Technician</p><p class="text-xs text-gray-500">Services: ' + totalServices + '</p></div></div>';
        html += '<div class="mt-2 pt-2 border-t border-gray-200"><p class="text-xs font-semibold text-gray-600 mb-2">Assigned services</p>';
        if (techServices.length > 0) {
            html += '<div class="space-y-2">';
            techServices.forEach(function(service) {
                html += '<div class="bg-gray-50 rounded-lg p-3 border border-gray-200"><div class="flex items-center justify-between gap-3"><div class="flex-1 min-w-0"><p class="text-sm font-semibold text-gray-900 flex items-center gap-2">' + salonPayServiceColorDot(service.service_id || service.name) + service.name + '</p><p class="text-xs text-gray-500">' + window.salonFormatMoney(service.price) + ' each</p></div><div class="flex items-center gap-2"><button onclick="event.stopPropagation(); salonPayUpdateServiceQuantity(\'' + service.name.replace(/'/g, "\\'") + '\', ' + service.price + ', \'' + idStr + '\', ' + (service.quantity - 1) + ')" class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-200 rounded border border-gray-300 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg></button><span class="text-sm font-medium text-gray-900 min-w-[2rem] text-center">' + service.quantity + '</span><button onclick="event.stopPropagation(); salonPayUpdateServiceQuantity(\'' + service.name.replace(/'/g, "\\'") + '\', ' + service.price + ', \'' + idStr + '\', ' + (service.quantity + 1) + ')" class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-200 rounded border border-gray-300 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg></button></div><div class="text-sm font-semibold text-gray-900 min-w-[4rem] text-right">' + window.salonFormatMoney(service.price * service.quantity) + '</div><button onclick="event.stopPropagation(); salonPayRemoveServiceFromCart(\'' + service.name.replace(/'/g, "\\'") + '\', \'' + idStr + '\')" class="w-8 h-8 flex items-center justify-center text-red-500 hover:bg-red-50 rounded transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div></div>';
            });
            html += '</div></div>';
        } else {
            html += '<p class="text-xs text-gray-500 italic">No services assigned</p></div>';
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
    salonPayDoSwitchStep(step);
};
function salonPayDoSwitchStep(step) {
    currentStep = step;
    var step1 = document.getElementById('step1Content');
    var step2 = document.getElementById('step2Content');
    var step1Tab = document.getElementById('step1Tab');
    var step2Tab = document.getElementById('step2Tab');
    if (!step1 || !step2) return;
    if (step === 1) {
        step1.classList.remove('hidden');
        step2.classList.add('hidden');
        salonPaySetStepInUrl(1);
    } else {
        step1.classList.add('hidden');
        step2.classList.remove('hidden');
        salonPayRenderCheckoutStep();
        salonPaySetStepInUrl(2);
    }
    if (!step1Tab || !step2Tab) return;
    var step1Span = step1Tab.querySelector('span');
    var step1H3 = step1Tab.querySelector('h3');
    var step2Span = step2Tab.querySelector('span');
    var step2H3 = step2Tab.querySelector('h3');
    if (step === 1) {
        step1Tab.classList.remove('border-transparent');
        step1Tab.classList.add('border-[#003047]');
        if (step1Span) { step1Span.classList.remove('bg-gray-200', 'text-gray-600'); step1Span.classList.add('bg-[#003047]', 'text-white'); }
        if (step1H3) { step1H3.classList.remove('text-gray-500'); step1H3.classList.add('text-gray-900'); }
        step2Tab.classList.remove('border-[#003047]');
        step2Tab.classList.add('border-transparent');
        if (step2Span) { step2Span.classList.remove('bg-[#003047]', 'text-white'); step2Span.classList.add('bg-gray-200', 'text-gray-600'); }
        if (step2H3) { step2H3.classList.remove('text-gray-900'); step2H3.classList.add('text-gray-500'); }
    } else {
        step2Tab.classList.remove('border-transparent');
        step2Tab.classList.add('border-[#003047]');
        if (step2Span) { step2Span.classList.remove('bg-gray-200', 'text-gray-600'); step2Span.classList.add('bg-[#003047]', 'text-white'); }
        if (step2H3) { step2H3.classList.remove('text-gray-500'); step2H3.classList.add('text-gray-900'); }
        step1Tab.classList.remove('border-[#003047]');
        step1Tab.classList.add('border-transparent');
        if (step1Span) { step1Span.classList.remove('bg-[#003047]', 'text-white'); step1Span.classList.add('bg-gray-200', 'text-gray-600'); }
        if (step1H3) { step1H3.classList.remove('text-gray-900'); step1H3.classList.add('text-gray-500'); }
    }
};
function salonPayRenderCheckoutStep() {
    if (!customerData || !appointmentData) return;
    paymentSubtotal = cart.reduce(function(sum, item) { return sum + (item.price * item.quantity); }, 0);
    paymentTip = 0;
    paymentDiscount = 0;
    paymentCredits = 0;
    paymentGiftCard = 0;
    appliedGiftCardCode = '';
    var discountedSubtotal = paymentSubtotal - paymentDiscount;
    paymentTax = discountedSubtotal * (salonPayGetEffectiveTaxRate() / 100);
    var total = discountedSubtotal + paymentTax + paymentTip;
    var itemsList = document.getElementById('checkoutItemsList');
    if (itemsList) {
        itemsList.innerHTML = cart.map(function(item) {
            return '<div class="grid grid-cols-12 gap-4"><div class="col-span-6"><span class="text-sm text-gray-900 flex items-center gap-2">' + salonPayServiceColorDot(item.service_id || item.name) + item.name + '</span></div><div class="col-span-3 text-center"><span class="text-sm text-gray-900">' + item.quantity + '</span></div><div class="col-span-3 text-right"><span class="text-sm font-semibold text-gray-900">' + window.salonFormatMoney(item.price * item.quantity) + '</span></div></div>';
        }).join('');
    }
    document.getElementById('checkoutSubtotalDisplay').textContent = window.salonFormatMoney(paymentSubtotal);
    document.getElementById('checkoutTaxDisplay').textContent = window.salonFormatMoney(paymentTax);
    document.getElementById('checkoutDiscountDisplay').textContent = window.salonFormatMoney(-paymentDiscount);
    var creditsDisplay = document.getElementById('checkoutCreditsDisplay');
    if (creditsDisplay) creditsDisplay.textContent = window.salonFormatMoney(-paymentCredits);
    var giftCardDisplay = document.getElementById('checkoutGiftCardDisplay');
    if (giftCardDisplay) giftCardDisplay.textContent = window.salonFormatMoney(-paymentGiftCard);
    paymentAmountStr = '';
    salonPayUpdatePaymentDisplay();
    salonPayRenderTechniciansTipSplit();
    salonPayUpdatePaymentTotal();
    salonPayUpdateRemoveAdjustmentLinks();
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
        var photo = technician.profilePhotoUrl || technician.photo || null;
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
        return '<div class="' + cardClasses + '" data-technician-id="' + idStr + '"><div class="flex-shrink-0">' + (photo ? '<img src="' + photo + '" alt="' + fullName + '" class="' + photoClasses + '">' : '<div class="' + initialsBgClasses + '"><span class="' + initialsTextClasses + '">' + initials + '</span></div>') + '</div><div class="flex-1 min-w-0"><p class="' + nameClasses + '">' + fullName + '</p></div>' + (tipSplitMode === 'percentage' || tipSplitMode === 'even' ? '<div class="flex gap-2 flex-1"><div class="flex-1"><div class="flex items-center border ' + (hasServices ? 'border-gray-300' : 'border-gray-200') + ' rounded-lg"><input type="number" id="tip-percentage-' + idStr + '" value="' + (currentPercentage % 1 === 0 ? Math.round(currentPercentage) : currentPercentage.toFixed(1)) + '" step="0.1" min="0" max="100" ' + (!hasServices || tipSplitMode === 'percentage' || tipSplitMode === 'even' ? 'readonly tabindex="-1"' : '') + ' ' + (!hasServices ? 'disabled' : '') + ' ' + (tipSplitMode === 'percentage' || tipSplitMode === 'even' ? '' : 'oninput="salonPayUpdateTechnicianTipPercentage(\'' + idStr + '\', this.value)"') + ' class="flex-1 px-3 py-2 text-sm ' + (!hasServices || tipSplitMode === 'percentage' || tipSplitMode === 'even' ? 'bg-gray-100 cursor-not-allowed' : '') + ' ' + (!hasServices ? 'text-gray-400' : 'text-gray-900') + ' focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent rounded-l-lg border-0"><span class="px-3 py-2 text-sm ' + (hasServices ? 'text-gray-500' : 'text-gray-300') + ' bg-gray-50 border-l ' + (hasServices ? 'border-gray-300' : 'border-gray-200') + ' rounded-r-lg">%</span></div></div><div class="flex-1"><div class="flex items-center border ' + (hasServices ? 'border-gray-300' : 'border-gray-200') + ' rounded-lg"><input type="number" id="tip-amount-' + idStr + '" value="' + calculatedAmount.toFixed(2) + '" step="0.01" min="0" readonly tabindex="-1" ' + (!hasServices ? 'disabled' : '') + ' class="flex-1 px-3 py-2 text-sm bg-gray-100 cursor-not-allowed ' + (!hasServices ? 'text-gray-400' : 'text-gray-900') + ' focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent rounded-l-lg border-0"><span class="px-3 py-2 text-sm ' + (hasServices ? 'text-gray-500' : 'text-gray-300') + ' bg-gray-50 border-l ' + (hasServices ? 'border-gray-300' : 'border-gray-200') + ' rounded-r-lg">' + (window.salonCurrencySymbol || '$') + '</span></div></div></div>' : '<div><div class="flex items-center border ' + (hasServices ? 'border-gray-300' : 'border-gray-200') + ' rounded-lg"><input type="number" id="tip-amount-' + idStr + '" value="' + calculatedAmount.toFixed(2) + '" step="0.01" min="0" ' + (!hasServices ? 'readonly tabindex="-1" disabled' : '') + ' ' + (hasServices ? 'oninput="salonPayUpdateTechnicianTipAmount(\'' + idStr + '\', this.value)"' : '') + ' class="flex-1 px-3 py-2 text-sm ' + (!hasServices ? 'bg-gray-100 cursor-not-allowed text-gray-400' : '') + ' focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent rounded-l-lg border-0"><span class="px-3 py-2 text-sm ' + (hasServices ? 'text-gray-500' : 'text-gray-300') + ' bg-gray-50 border-l ' + (hasServices ? 'border-gray-300' : 'border-gray-200') + ' rounded-r-lg">' + (window.salonCurrencySymbol || '$') + '</span></div></div>') + '</div>';
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
    if (totalTipSplitDisplay) totalTipSplitDisplay.textContent = window.salonFormatMoney(totalSplit);
    if (totalTipSplitDisplayEven) totalTipSplitDisplayEven.textContent = window.salonFormatMoney(totalSplit);
    if (totalTipSplitDisplayCustom) totalTipSplitDisplayCustom.textContent = window.salonFormatMoney(totalSplit);
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
    paymentTax = discountedSubtotal * (salonPayGetEffectiveTaxRate() / 100);
    var totalTipSplitAmount = salonPayCalculateTotalTipSplit();
    var totalBeforeCredits = discountedSubtotal + paymentTax + totalTipSplitAmount;
    var maxCredits = Math.min(availableCredits, totalBeforeCredits);
    if (paymentCredits > maxCredits) {
        paymentCredits = maxCredits;
    }
    var totalAfterCredits = totalBeforeCredits - paymentCredits;
    if (paymentGiftCard > totalAfterCredits) {
        paymentGiftCard = totalAfterCredits;
    }
    var total = totalAfterCredits - paymentGiftCard;
    var checkoutTipDisplay = document.getElementById('checkoutTipDisplay');
    var checkoutDiscountDisplay = document.getElementById('checkoutDiscountDisplay');
    var checkoutTaxDisplay = document.getElementById('checkoutTaxDisplay');
    var checkoutTotalDisplay = document.getElementById('checkoutTotalDisplay');
    var checkoutGiftCardDisplay = document.getElementById('checkoutGiftCardDisplay');
    var checkoutCreditsDisplay = document.getElementById('checkoutCreditsDisplay');
    if (checkoutTipDisplay) checkoutTipDisplay.textContent = window.salonFormatMoney(totalTipSplitAmount);
    if (checkoutDiscountDisplay) checkoutDiscountDisplay.textContent = window.salonFormatMoney(-paymentDiscount);
    if (checkoutCreditsDisplay) checkoutCreditsDisplay.textContent = window.salonFormatMoney(-paymentCredits);
    if (checkoutGiftCardDisplay) checkoutGiftCardDisplay.textContent = window.salonFormatMoney(-paymentGiftCard);
    if (checkoutTaxDisplay) checkoutTaxDisplay.textContent = window.salonFormatMoney(paymentTax);
    if (checkoutTotalDisplay) checkoutTotalDisplay.textContent = window.salonFormatMoney(total);

    var creditPointsSection = document.getElementById('creditPointsSection');
    var currentPointsDisplay = document.getElementById('checkoutCurrentPointsDisplay');
    var earningPointsDisplay = document.getElementById('checkoutEarningPointsDisplay');
    if (creditPointsSection) {
        var earningPoints = 0;
        if (pointsRateFixed && pointsUnitValue > 0 && pointsPerUnit > 0 && total > 0) {
            earningPoints = Math.round(((total / pointsUnitValue) * pointsPerUnit) * 100) / 100;
        } else if (pointsRatePercentage && rewardPercentage > 0 && total > 0) {
            earningPoints = Math.round((total * (rewardPercentage / 100)) * 100) / 100;
        }
        if (pointsRateFixed || pointsRatePercentage) {
            creditPointsSection.classList.remove('hidden');
            var currentPts = availableCredits || 0;
            if (currentPointsDisplay) currentPointsDisplay.textContent = currentPts.toFixed(2);
            if (earningPointsDisplay) earningPointsDisplay.textContent = '+' + earningPoints.toFixed(2);
        } else {
            creditPointsSection.classList.add('hidden');
        }
    }

    var tipAmountHidden = document.getElementById('tipAmountHidden');
    if (tipAmountHidden) tipAmountHidden.value = totalTipSplitAmount.toFixed(2);
    var discountAmountHidden = document.getElementById('discountAmountHidden');
    if (discountAmountHidden) discountAmountHidden.value = paymentDiscount.toFixed(2);
    var creditAmountHidden = document.getElementById('creditAmountHidden');
    if (creditAmountHidden) creditAmountHidden.value = paymentCredits.toFixed(2);
    var creditCustomerIdHidden = document.getElementById('creditCustomerIdHidden');
    if (creditCustomerIdHidden) creditCustomerIdHidden.value = paymentCredits > 0 && customerData ? customerData.id : '';
    var giftCardAmountHidden = document.getElementById('giftCardAmountHidden');
    if (giftCardAmountHidden) giftCardAmountHidden.value = paymentGiftCard.toFixed(2);
    var giftCardCodeHidden = document.getElementById('giftCardCodeHidden');
    if (giftCardCodeHidden) giftCardCodeHidden.value = appliedGiftCardCode || '';
    var cardRadio = document.getElementById('paymentMethodCard');
    if (cardRadio && cardRadio.checked) {
        paymentAmountStr = total.toFixed(2);
        salonPayUpdatePaymentDisplay();
    }

    salonPayUpdateRemoveAdjustmentLinks();
}

function salonPayUpdateRemoveAdjustmentLinks() {
    var discountLink = document.getElementById('removeDiscountLink');
    var creditsLink = document.getElementById('removeCreditsLink');
    var giftCardLink = document.getElementById('removeGiftCardLink');
    if (discountLink) discountLink.classList.toggle('hidden', !(paymentDiscount > 0));
    if (creditsLink) creditsLink.classList.toggle('hidden', !(paymentCredits > 0));
    if (giftCardLink) giftCardLink.classList.toggle('hidden', !(paymentGiftCard > 0));
}

window.salonPayRemoveDiscount = function() {
    paymentDiscount = 0;
    salonPayUpdatePaymentTotal();
};

window.salonPayRemoveCredits = function() {
    paymentCredits = 0;
    salonPayUpdatePaymentTotal();
};

window.salonPayRemoveGiftCard = function() {
    paymentGiftCard = 0;
    appliedGiftCardCode = '';
    salonPayUpdatePaymentTotal();
};
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
        displayEl.textContent = (window.salonCurrencySymbol || '$') + '0';
        valueEl.value = '0';
    } else {
        var amount = parseFloat(paymentAmountStr) || 0;
        displayEl.textContent = window.salonFormatMoney(amount);
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
            var totalText = checkoutTotalDisplay.textContent.replace(/[^0-9.\-]/g, '').trim();
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
    var content = '<div class="p-6"><div class="flex items-center justify-between mb-4"><h3 class="text-xl font-bold text-gray-900">Gift Card</h3><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><div class="space-y-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">Gift Card Code</label><input type="text" id="giftCardNumberInput" placeholder="Enter gift card code" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div><label class="block text-sm font-medium text-gray-700 mb-2">PIN (if required)</label><input type="text" id="giftCardPinInput" placeholder="Enter PIN" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div id="giftCardMessage" class="text-xs text-gray-500"></div><div class="flex gap-3 justify-end"><button type="button" onclick="closeModal()" class="px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">Cancel</button><button type="button" onclick="salonPayApplyGiftCard()" class="px-6 py-3 text-sm font-medium text-white bg-[#003047] rounded-lg hover:bg-[#002535] transition">Apply</button></div></div></div>';
    openModal(content, 'default', false);
    salonPayEnsureDiscountDataLoaded();
    salonPayEnsureGiftCardsLoaded().then(function() {
        if (!giftCardsEnabled) {
            salonPaySetGiftCardMessage('Gift cards are disabled in settings.', true);
        }
    });
};
window.salonPayApplyGiftCard = function() {
    var giftCardNumberInput = document.getElementById('giftCardNumberInput');
    var giftCardPinInput = document.getElementById('giftCardPinInput');
    var number = giftCardNumberInput ? giftCardNumberInput.value.trim() : '';
    var pin = giftCardPinInput ? giftCardPinInput.value.trim() : '';
    if (!giftCardsEnabled) {
        salonPaySetGiftCardMessage('Gift cards are disabled in settings.', true);
        return;
    }
    if (!number) {
        salonPaySetGiftCardMessage('Please enter a gift card code.', true);
        return;
    }
    salonPayEnsureGiftCardsLoaded().then(function() {
        var card = salonPayFindGiftCardByCode(number);
        if (!card) {
            salonPaySetGiftCardMessage('No matching gift card found.', true);
            return;
        }
        if (card.pin && card.pin !== pin) {
            salonPaySetGiftCardMessage('PIN does not match.', true);
            return;
        }
        var balance = Number(card.balance || 0);
        if (balance <= 0) {
            salonPaySetGiftCardMessage('This gift card has no remaining balance.', true);
            return;
        }
        var totalBeforeGift = salonPayGetTotalBeforeGiftCard();
        paymentGiftCard = Math.min(balance, totalBeforeGift);
        appliedGiftCardCode = card.code || salonPayNormalizeGiftCardCode(number);
        salonPayUpdatePaymentTotal();
        closeModal();
    });
};
window.salonPayOpenRedeemModal = function() {
    var balance = availableCredits || 0;
    var content = '<div class="p-6"><div class="flex items-center justify-between mb-4"><h3 class="text-xl font-bold text-gray-900">Redeem Credits</h3><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><div class="space-y-4"><div class="p-4 bg-gray-50 rounded-lg"><p class="text-sm text-gray-500 mb-1">Available Credits</p><p class="text-2xl font-bold text-gray-900">' + window.salonFormatMoney(balance) + '</p></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Amount to Redeem</label><input type="number" id="redeemAmountInput" step="0.01" min="0" placeholder="0.00" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div id="redeemMessage" class="text-xs text-gray-500"></div><div class="flex gap-3 justify-end"><button type="button" onclick="closeModal()" class="px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">Cancel</button><button type="button" onclick="salonPayApplyRedeem()" class="px-6 py-3 text-sm font-medium text-white bg-[#003047] rounded-lg hover:bg-[#002535] transition">Apply</button></div></div></div>';
    openModal(content, 'default', false);
    if (balance <= 0) {
        salonPaySetRedeemMessage('No credits available for this customer.', true);
    }
};
window.salonPayApplyRedeem = function() {
    var amountInput = document.getElementById('redeemAmountInput');
    var amount = amountInput ? parseFloat(amountInput.value) : 0;
    if (!amountInput || isNaN(amount) || amount <= 0) {
        salonPaySetRedeemMessage('Please enter a valid amount.', true);
        return;
    }
    if (availableCredits <= 0) {
        salonPaySetRedeemMessage('No credits available for this customer.', true);
        return;
    }
    var discountedSubtotal = paymentSubtotal - paymentDiscount;
    var totalTipSplitAmount = salonPayCalculateTotalTipSplit();
    paymentTax = discountedSubtotal * (salonPayGetEffectiveTaxRate() / 100);
    var totalBeforeCredits = discountedSubtotal + paymentTax + totalTipSplitAmount;
    var maxCredits = Math.min(availableCredits, totalBeforeCredits);
    if (amount > maxCredits) {
        salonPaySetRedeemMessage('Maximum redeemable amount is ' + window.salonFormatMoney(maxCredits) + '.', true);
        return;
    }
    paymentCredits = amount;
    salonPayUpdatePaymentTotal();
    closeModal();
};
window.salonPayOpenDiscountModal = function() {
    var codeBtnDisabledAttr = discountsEnabled ? '' : ' disabled aria-disabled="true"';
    var codeBtnClasses = discountsEnabled
        ? 'py-2 text-xs font-medium text-gray-700 bg-[#e6f0f3] rounded-lg hover:bg-[#b3d1d9] transition active:scale-95 border border-[#b3d1d9]'
        : 'py-2 text-xs font-medium text-gray-400 bg-gray-100 rounded-lg border border-gray-200 cursor-not-allowed opacity-70';

    var content = ''
        + '<div class="p-6">'
        + '<div class="flex items-center justify-between mb-4">'
        + '<h3 class="text-xl font-bold text-gray-900">Discount</h3>'
        + '<button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>'
        + '</div>'
        + '<div class="space-y-4">'
        + '<div>'
        + '<label class="block text-sm font-medium text-gray-700 mb-2">Discount</label>'
        + '<div class="grid grid-cols-5 gap-2 mb-2">'
        + '<button type="button" onclick="salonPaySetDiscountPercentage(5); salonPaySwitchToDiscountAmountView();" class="py-2 text-xs font-medium text-gray-700 bg-[#e6f0f3] rounded-lg hover:bg-[#b3d1d9] transition active:scale-95 border border-[#b3d1d9]">5%</button>'
        + '<button type="button" onclick="salonPaySetDiscountPercentage(10); salonPaySwitchToDiscountAmountView();" class="py-2 text-xs font-medium text-gray-700 bg-[#e6f0f3] rounded-lg hover:bg-[#b3d1d9] transition active:scale-95 border border-[#b3d1d9]">10%</button>'
        + '<button type="button" onclick="salonPaySetDiscountPercentage(15); salonPaySwitchToDiscountAmountView();" class="py-2 text-xs font-medium text-gray-700 bg-[#e6f0f3] rounded-lg hover:bg-[#b3d1d9] transition active:scale-95 border border-[#b3d1d9]">15%</button>'
        + '<button type="button" onclick="salonPaySetDiscountPercentage(20); salonPaySwitchToDiscountAmountView();" class="py-2 text-xs font-medium text-gray-700 bg-[#e6f0f3] rounded-lg hover:bg-[#b3d1d9] transition active:scale-95 border border-[#b3d1d9]">20%</button>'
        + '<button type="button" id="discountCodeTabBtn" onclick="salonPaySwitchToDiscountCodeView();" class="' + codeBtnClasses + '"' + codeBtnDisabledAttr + '>CODE</button>'
        + '</div>'
        + '<div id="discountAmountView">'
        + '<input type="number" name="discount" id="discountInputModal" step="0.01" min="0" value="' + paymentDiscount.toFixed(2) + '" oninput="salonPayUpdateDiscountFromInputModal()" placeholder="0.00" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">'
        + '</div>'
        + '<div id="discountCodeView" class="hidden">'
        + '<label class="block text-sm font-medium text-gray-700 mb-2">Enter Discount Code</label>'
        + '<input type="text" id="discountCodeInputModal" placeholder="Enter Discount Code" oninput="salonPayHandleDiscountCodeInput(this.value)" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">'
        + '<div id="discountCodeMessage" class="mt-2 text-xs text-gray-500"></div>'
        + '</div>'
        + '</div>'
        + '<div class="flex gap-3 justify-end">'
        + '<button type="button" onclick="closeModal()" class="px-6 py-3 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">Cancel</button>'
        + '<button type="button" onclick="salonPayApplyDiscountFromModal()" class="px-6 py-3 text-sm font-medium text-white bg-[#003047] rounded-lg hover:bg-[#002535] transition">Apply</button>'
        + '</div>'
        + '</div>';
    openModal(content, 'default', false);
    salonPayEnsureDiscountDataLoaded().then(function() {
        if (!discountsEnabled) {
            // Ensure we can't land in code view if codes are disabled.
            window.salonPaySwitchToDiscountAmountView();
            salonPaySetDiscountMessage('Discount codes are disabled in settings.', true);
        }
        window.salonPayHandleDiscountCodeInput('');
    });
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

window.salonPayApplyDiscountFromModal = function() {
    var codeView = document.getElementById('discountCodeView');
    var amountView = document.getElementById('discountAmountView');
    if (codeView && !codeView.classList.contains('hidden')) {
        var input = document.getElementById('discountCodeInputModal');
        var code = input ? input.value : '';
        if (!discountsEnabled) {
            salonPaySetDiscountMessage('Discount codes are disabled in settings.', true);
            return;
        }
        var coupon = salonPayFindCouponByCode(code);
        if (!coupon) {
            salonPaySetDiscountMessage('No matching discount code found.', true);
            return;
        }
        if (coupon.min_order_amount && paymentSubtotal < Number(coupon.min_order_amount)) {
            salonPaySetDiscountMessage('Minimum order is ' + window.salonFormatMoney(Number(coupon.min_order_amount)) + '.', true);
            return;
        }
        paymentDiscount = salonPayCalculateDiscountFromCoupon(coupon);
        salonPayUpdatePaymentTotal();
        closeModal();
        return;
    }
    if (amountView && !amountView.classList.contains('hidden')) {
        salonPayUpdateDiscountFromInputModal();
        closeModal();
    }
};
window.salonPaySwitchToDiscountCodeView = function() {
    if (!discountsEnabled) {
        salonPaySetDiscountMessage('Discount codes are disabled in settings.', true);
        return;
    }
    var amountView = document.getElementById('discountAmountView');
    var codeView = document.getElementById('discountCodeView');
    if (amountView && codeView) {
        amountView.classList.add('hidden');
        codeView.classList.remove('hidden');
    }
    salonPayEnsureDiscountDataLoaded().then(function() {
        var input = document.getElementById('discountCodeInputModal');
        if (input) window.salonPayHandleDiscountCodeInput(input.value);
    });
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
    if (!appointmentId) {
        alert('No appointment ID provided.');
        return;
    }
    var discountedSubtotal = paymentSubtotal - paymentDiscount;
    var calculatedTax = discountedSubtotal * (salonPayGetEffectiveTaxRate() / 100);
    var totalTipSplitAmount = salonPayCalculateTotalTipSplit();
    var totalBeforeCredits = discountedSubtotal + calculatedTax + totalTipSplitAmount;
    var totalAfterCredits = totalBeforeCredits - paymentCredits;
    var total = totalAfterCredits - paymentGiftCard;
    if (paymentAmount < total) {
        alert('Payment amount (' + window.salonFormatMoney(paymentAmount) + ') is less than total (' + window.salonFormatMoney(total) + '). Please enter the correct amount.');
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
        credits: paymentCredits,
        gift_card: paymentGiftCard,
        gift_card_code: appliedGiftCardCode,
        total: total,
        paymentMethod: paymentMethod,
        paymentAmount: paymentAmount,
        change: change
    });
    if (typeof salonApi === 'undefined' || !salonApi.post) {
        if (typeof showErrorMessage === 'function') showErrorMessage('Unable to process payment right now.');
        else alert('Unable to process payment right now.');
        return;
    }
    var tipsByTechnician = assignedTechnicianIds.map(function(idStr) {
        var tipInfo = technicianTips[idStr] || {};
        return {
            technician_id: parseInt(idStr, 10),
            tip: tipInfo.amount != null ? Number(tipInfo.amount) : 0
        };
    }).filter(function(row) { return !isNaN(row.technician_id); });
    salonApi.post(apiBase + '/payments', {
        appointment_id: appointmentId ? parseInt(appointmentId, 10) : null,
        method: paymentMethod,
        amount: total,
        sub_total: paymentSubtotal,
        discount: paymentDiscount,
        credits: paymentCredits,
        gift_card: paymentGiftCard,
        tax: paymentTax,
        tip: totalTipSplitAmount,
        status: 'Paid',
        tips_by_technician: tipsByTechnician
    }).then(function() {
        if (typeof showSuccessMessage === 'function') {
            showSuccessMessage('Payment processed successfully!');
        } else {
            alert('Payment processed successfully!');
        }
        setTimeout(function() {
            window.location.href = ticketsUrl;
        }, 1500);
    }).catch(function(err) {
        if (typeof showErrorMessage === 'function') showErrorMessage(err.message || 'Failed to save payment.');
        else alert(err.message || 'Failed to save payment.');
    });
};
document.addEventListener('DOMContentLoaded', function() {
    salonPayLoadPaymentData();
});
})();

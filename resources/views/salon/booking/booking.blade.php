@extends('layouts.salon')

@section('content')
@php
    $calendarUrl = route('salon.booking.calendar');
@endphp
<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="mb-6">
            <div class="flex items-center justify-between mb-6">
                <a href="{{ $calendarUrl }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    <span class="text-sm font-medium">Go to Calendar</span>
                </a>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">New Booking</h1>
            </div>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-[30%_70%] gap-6">
            <div class="space-y-2">
                <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <div class="flex items-center justify-end mb-2">
                        <button onclick="salonBookingOpenAddCustomerModal()" class="px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm active:scale-95 flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Add New Customer
                        </button>
                    </div>
                    <p class="text-sm text-gray-600 font-medium mb-2">Select Customer</p>
                    <div class="relative">
                        <div id="customerDropdown" class="relative">
                            <button type="button" id="customerDropdownBtn" onclick="salonBookingToggleCustomerDropdown()" class="w-full text-left pl-12 pr-12 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-base bg-white flex items-center justify-between">
                                <span id="customerDropdownText" class="text-gray-500">Search by name, phone, or email...</span>
                                <svg id="customerDropdownIcon" class="w-6 h-6 text-gray-400 transform transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                            <div class="absolute left-3 top-1/2 transform -translate-y-1/2 w-6 h-6 text-gray-400 pointer-events-none">
                                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <div id="customerDropdownMenu" class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-[400px] overflow-y-auto hidden">
                                <div class="p-3 border-b border-gray-200 sticky top-0 bg-white">
                                    <input type="text" id="customerSearchInput" placeholder="Search customers..." class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-base" oninput="salonBookingSearchCustomers(this.value)" onclick="event.stopPropagation()">
                                    <svg class="absolute left-4 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400 pointer-events-none mt-1 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <div id="customerSearchResults" class="p-2 space-y-1"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="selectedCustomerDisplay" class="hidden">
                    <div class="bg-[#e6f0f3] border border-[#b3d1d9] rounded-lg p-4 flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-[#003047] rounded-full flex items-center justify-center flex-shrink-0">
                                <span id="selectedCustomerInitials" class="text-base font-bold text-white"></span>
                            </div>
                            <div>
                                <p id="selectedCustomerName" class="text-base font-semibold text-gray-900"></p>
                                <p id="selectedCustomerContact" class="text-sm text-gray-500"></p>
                            </div>
                        </div>
                        <button type="button" onclick="salonBookingClearSelectedCustomer()" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>
                <div class="bg-white border border-gray-200 rounded-lg p-4">
                    <p class="text-sm text-gray-600 font-medium mb-3">Select Date & Time</p>
                    <div id="appointmentCalendar" class="w-full mb-4"></div>
                </div>
                <div class="bg-white border border-gray-200 rounded-lg p-4 flex flex-col">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Preferred Time</h3>
                    <div id="availableTimeSlots" class="space-y-4 overflow-y-auto flex-1 min-h-0 max-h-[400px]">
                        <p class="text-sm text-gray-500 text-center py-4">Please select a date to view available time slots</p>
                    </div>
                </div>
            </div>
            <div class="bg-white border border-gray-200 rounded-lg p-4">
                <div class="grid grid-cols-2 gap-4 h-full">
                    <div class="border-r border-gray-200 pr-4">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-sm font-semibold text-gray-900">Available Technicians</h4>
                            <span id="availableCount" class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">0</span>
                        </div>
                        <p class="text-xs text-gray-500 mb-2">Click to assign technicians to services</p>
                        <div class="mb-4">
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                <input type="text" id="technicianSearchInput" placeholder="Search technicians..." oninput="salonBookingSearchTechnicians(this.value)" class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-sm">
                                <button id="clearTechnicianSearchBtn" onclick="salonBookingClearTechnicianSearch()" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        </div>
                        <div id="availableTechniciansContainer" class="space-y-2 min-h-[400px] max-h-[75vh] overflow-y-auto"></div>
                    </div>
                    <div class="pl-4">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-sm font-semibold text-gray-900">Assigned Technicians</h4>
                            <span id="assignedCount" class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">0</span>
                        </div>
                        <p class="text-xs text-gray-500 mb-4">Click to remove assigned technicians</p>
                        <div id="assignedTechniciansContainer" class="space-y-2 min-h-[400px] max-h-[400px] overflow-y-auto">
                            <div class="flex items-center justify-center h-full min-h-[400px]">
                                <p class="text-sm text-gray-400">No technicians assigned</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="h-20"></div>
    </div>
</main>
<div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 shadow-lg z-40">
    <div class="px-4 py-3">
        <div class="flex justify-end">
            <button onclick="salonBookingSave()" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95 flex items-center gap-2">
                <span>Save Booking</span>
            </button>
        </div>
    </div>
</div>
@push('scripts')
<script>
(function() {
var base = window.salonJsonBase || '{{ url("json") }}';
var calendarUrl = '{{ $calendarUrl }}';
var allCustomers = [], selectedCustomer = null, selectedAppointmentDate = null, selectedAppointmentTime = null;
var currentCalendarMonth = new Date().getMonth(), currentCalendarYear = new Date().getFullYear();
var availableTechnicians = [], assignedTechnicianIds = [], technicianSearchTerm = '';
function getInitials(c) {
    var first = (c.firstName || '')[0] || '';
    var last = (c.lastName || '')[0] || '';
    return (first + last).toUpperCase().substring(0, 2);
}
function fetchCustomers() {
    return fetch(base + '/customers.json').then(function(r) { return r.json(); }).then(function(data) {
        allCustomers = data.customers || [];
    }).catch(function(err) { console.error(err); allCustomers = []; });
}
window.salonBookingToggleCustomerDropdown = function() {
    var menu = document.getElementById('customerDropdownMenu'), icon = document.getElementById('customerDropdownIcon');
    if (menu && menu.classList.contains('hidden')) {
        menu.classList.remove('hidden');
        if (icon) icon.classList.add('rotate-180');
        setTimeout(function() {
            var inp = document.getElementById('customerSearchInput');
            if (inp) { inp.focus(); salonBookingSearchCustomers(''); }
        }, 100);
    } else if (menu) {
        menu.classList.add('hidden');
        if (icon) icon.classList.remove('rotate-180');
    }
};
document.addEventListener('click', function(e) {
    var dropdown = document.getElementById('customerDropdown'), menu = document.getElementById('customerDropdownMenu');
    if (dropdown && menu && !dropdown.contains(e.target)) {
        menu.classList.add('hidden');
        var icon = document.getElementById('customerDropdownIcon');
        if (icon) icon.classList.remove('rotate-180');
    }
});
window.salonBookingSearchCustomers = function(term) {
    var resultsDiv = document.getElementById('customerSearchResults');
    if (!resultsDiv || !allCustomers) return;
    var lower = (term || '').toLowerCase().trim();
    var list = lower ? allCustomers.filter(function(c) {
        var name = (c.firstName + ' ' + c.lastName).toLowerCase();
        var phone = (c.phone || '').toLowerCase();
        var email = (c.email || '').toLowerCase();
        return name.indexOf(lower) >= 0 || phone.indexOf(lower) >= 0 || email.indexOf(lower) >= 0;
    }) : allCustomers;
    if (!list.length) {
        resultsDiv.innerHTML = '<div class="p-3 text-sm text-gray-500 text-center">No customer found.</div>';
        return;
    }
    resultsDiv.innerHTML = list.map(function(c) {
        var inits = getInitials(c);
        return '<button type="button" onclick="salonBookingSelectCustomer(' + c.id + '); event.stopPropagation();" class="w-full text-left px-4 py-3 hover:bg-gray-50 rounded-lg transition flex items-center gap-3"><div class="w-10 h-10 bg-[#e6f0f3] rounded-full flex items-center justify-center flex-shrink-0"><span class="text-sm font-bold text-[#003047]">' + inits + '</span></div><div class="flex-1"><p class="text-base font-semibold text-gray-900">' + c.firstName + ' ' + c.lastName + '</p><p class="text-sm text-gray-500">' + (c.phone || 'No phone') + (c.email ? ' • ' + c.email : '') + '</p></div></button>';
    }).join('');
};
window.salonBookingSelectCustomer = function(customerId) {
    var customer = allCustomers.find(function(c) { return c.id === customerId; });
    if (!customer) return;
    selectedCustomer = customer;
    var text = document.getElementById('customerDropdownText');
    if (text) {
        text.textContent = customer.firstName + ' ' + customer.lastName;
        text.classList.remove('text-gray-500');
        text.classList.add('text-gray-900', 'font-medium');
    }
    var disp = document.getElementById('selectedCustomerDisplay'), nameEl = document.getElementById('selectedCustomerName'), contactEl = document.getElementById('selectedCustomerContact'), initsEl = document.getElementById('selectedCustomerInitials');
    if (disp) disp.classList.remove('hidden');
    if (nameEl) nameEl.textContent = customer.firstName + ' ' + customer.lastName;
    if (contactEl) {
        var parts = [];
        if (customer.phone) parts.push(customer.phone);
        if (customer.email) parts.push(customer.email);
        contactEl.textContent = parts.join(' • ') || 'No contact information';
    }
    if (initsEl) initsEl.textContent = getInitials(customer);
    salonBookingToggleCustomerDropdown();
};
window.salonBookingClearSelectedCustomer = function() {
    selectedCustomer = null;
    var text = document.getElementById('customerDropdownText');
    if (text) {
        text.textContent = 'Search by name, phone, or email...';
        text.classList.remove('text-gray-900', 'font-medium');
        text.classList.add('text-gray-500');
    }
    var disp = document.getElementById('selectedCustomerDisplay');
    if (disp) disp.classList.add('hidden');
};
window.salonBookingOpenAddCustomerModal = function() {
    var content = '<div class="p-6"><div class="flex items-center justify-between mb-4"><h3 class="text-xl font-bold text-gray-900">Add New Customer</h3><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><form onsubmit="salonBookingSaveNewCustomer(event)" class="space-y-4"><div class="grid grid-cols-2 gap-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">First Name</label><input type="text" id="newCustomerFirstName" name="first_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047]"></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label><input type="text" id="newCustomerLastName" name="last_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047]"></div></div><div class="grid grid-cols-2 gap-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label><input type="tel" id="newCustomerPhone" name="phone" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047]"></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Email (optional)</label><input type="email" id="newCustomerEmail" name="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047]"></div></div><div class="flex justify-end gap-3 pt-4"><button type="button" onclick="closeModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium">Cancel</button><button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium">Save Customer</button></div></form></div>';
    openModal(content, 'default', false);
};
window.salonBookingSaveNewCustomer = function(e) {
    e.preventDefault();
    var firstName = document.getElementById('newCustomerFirstName').value.trim();
    var lastName = document.getElementById('newCustomerLastName').value.trim();
    var phone = document.getElementById('newCustomerPhone').value.trim();
    var email = document.getElementById('newCustomerEmail').value.trim();
    var newId = allCustomers.length ? Math.max.apply(null, allCustomers.map(function(c) { return c.id; })) + 1 : 1;
    var newCustomer = { id: newId, firstName: firstName, lastName: lastName, phone: phone || '', email: email || '', createdAt: new Date().toISOString().split('T')[0] };
    allCustomers.push(newCustomer);
    closeModal();
    salonBookingSelectCustomer(newId);
    showSuccessMessage('Customer added successfully!');
};
function initializeAppointmentCalendar() {
    var container = document.getElementById('appointmentCalendar');
    if (!container) return;
    var now = new Date();
    var urlParams = new URLSearchParams(window.location.search);
    var dateParam = urlParams.get('date'), timeParam = urlParams.get('time');
    if (dateParam) {
        var parts = dateParam.split('-');
        if (parts.length === 3) {
            selectedAppointmentDate = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
            currentCalendarMonth = parseInt(parts[1]) - 1;
            currentCalendarYear = parseInt(parts[0]);
        } else {
            selectedAppointmentDate = new Date(now.getFullYear(), now.getMonth(), now.getDate());
            currentCalendarMonth = now.getMonth();
            currentCalendarYear = now.getFullYear();
        }
    } else {
        selectedAppointmentDate = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        currentCalendarMonth = now.getMonth();
        currentCalendarYear = now.getFullYear();
    }
    renderCalendar(currentCalendarMonth, currentCalendarYear);
    updateAvailableTimeSlots();
    if (timeParam && selectedAppointmentDate) {
        setTimeout(function() { selectAppointmentTime(timeParam); }, 100);
    }
}
function renderCalendar(month, year) {
    var container = document.getElementById('appointmentCalendar');
    if (!container) return;
    var firstDay = new Date(year, month, 1).getDay();
    var daysInMonth = new Date(year, month + 1, 0).getDate();
    var monthNames = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    var dayNames = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
    var html = '<div class="mb-4"><div class="flex items-center justify-between mb-4"><h3 class="text-lg font-semibold text-gray-900">' + monthNames[month] + ' ' + year + '</h3><div class="flex gap-2"><button onclick="salonBookingChangeMonth(-1)" class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition">‹</button><button onclick="salonBookingChangeMonth(1)" class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 rounded-lg transition">›</button></div></div><div class="grid grid-cols-7 gap-1 mb-2">';
    dayNames.forEach(function(day) { html += '<div class="text-center text-xs font-semibold text-gray-600 py-2">' + day + '</div>'; });
    for (var i = 0; i < firstDay; i++) { html += '<div class="aspect-square"></div>'; }
    var today = new Date();
    for (var day = 1; day <= daysInMonth; day++) {
        var date = new Date(year, month, day);
        var isToday = date.toDateString() === today.toDateString();
        var isPast = date < today && !isToday;
        var isSelected = selectedAppointmentDate && date.toDateString() === selectedAppointmentDate.toDateString();
        var classes = 'aspect-square flex items-center justify-center text-sm rounded-lg transition cursor-pointer ';
        if (isPast) classes += 'text-gray-300 cursor-not-allowed';
        else if (isSelected) classes += 'bg-[#003047] text-white font-semibold';
        else if (isToday) classes += 'bg-[#e6f0f3] text-[#003047] font-semibold hover:bg-[#b3d1d9]';
        else classes += 'text-gray-700 hover:bg-gray-100';
        html += '<div class="' + classes + '" ' + (!isPast ? 'onclick="salonBookingSelectDate(' + year + ', ' + month + ', ' + day + ')"' : '') + '>' + day + '</div>';
    }
    html += '</div></div>';
    container.innerHTML = html;
}
window.salonBookingChangeMonth = function(dir) {
    currentCalendarMonth += dir;
    if (currentCalendarMonth < 0) { currentCalendarMonth = 11; currentCalendarYear--; }
    else if (currentCalendarMonth > 11) { currentCalendarMonth = 0; currentCalendarYear++; }
    renderCalendar(currentCalendarMonth, currentCalendarYear);
};
window.salonBookingSelectDate = function(year, month, day) {
    selectedAppointmentDate = new Date(year, month, day);
    selectedAppointmentTime = null;
    renderCalendar(currentCalendarMonth, currentCalendarYear);
    updateAvailableTimeSlots();
};
function updateAvailableTimeSlots() {
    var container = document.getElementById('availableTimeSlots');
    if (!container) return;
    if (!selectedAppointmentDate) {
        container.innerHTML = '<p class="text-sm text-gray-500 text-center py-4">Please select a date to view available time slots</p>';
        return;
    }
    var slots = [];
    for (var hour = 9; hour < 18; hour++) {
        for (var minute = 0; minute < 60; minute += 30) {
            var timeStr = String(hour).padStart(2, '0') + ':' + String(minute).padStart(2, '0');
            var displayHour = hour === 0 ? 12 : (hour > 12 ? hour - 12 : hour);
            var ampm = hour >= 12 ? 'PM' : 'AM';
            var display = displayHour + ':' + String(minute).padStart(2, '0') + ' ' + ampm;
            slots.push({ value: timeStr, display: display });
        }
    }
    var now = new Date();
    var selectedDateStr = selectedAppointmentDate.toDateString();
    var todayStr = now.toDateString();
    var isToday = selectedDateStr === todayStr;
    var available = slots.filter(function(slot) {
        if (!isToday) return true;
        var parts = slot.value.split(':').map(Number);
        var slotTime = new Date(now.getFullYear(), now.getMonth(), now.getDate(), parts[0], parts[1]);
        return slotTime.getTime() > now.getTime();
    });
    if (!available.length) {
        container.innerHTML = '<p class="text-sm text-gray-500 text-center py-4">No available time slots for this date</p>';
        return;
    }
    var morning = available.filter(function(s) { return parseInt(s.value.split(':')[0]) < 12; });
    var afternoon = available.filter(function(s) { return parseInt(s.value.split(':')[0]) >= 12; });
    var html = '';
    if (morning.length) {
        html += '<div><h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Morning</h4><div class="grid grid-cols-3 sm:grid-cols-4 gap-2">';
        morning.forEach(function(slot) {
            var sel = selectedAppointmentTime === slot.value;
            html += '<button type="button" onclick="selectAppointmentTime(\'' + slot.value + '\')" class="px-3 py-2 text-sm font-medium rounded-lg border transition ' + (sel ? 'bg-[#003047] text-white border-[#003047]' : 'bg-white text-gray-700 border-gray-300 hover:border-[#003047] hover:bg-[#e6f0f3]') + '">' + slot.display + '</button>';
        });
        html += '</div></div>';
    }
    if (afternoon.length) {
        html += '<div><h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Afternoon</h4><div class="grid grid-cols-3 sm:grid-cols-4 gap-2">';
        afternoon.forEach(function(slot) {
            var sel = selectedAppointmentTime === slot.value;
            html += '<button type="button" onclick="selectAppointmentTime(\'' + slot.value + '\')" class="px-3 py-2 text-sm font-medium rounded-lg border transition ' + (sel ? 'bg-[#003047] text-white border-[#003047]' : 'bg-white text-gray-700 border-gray-300 hover:border-[#003047] hover:bg-[#e6f0f3]') + '">' + slot.display + '</button>';
        });
        html += '</div></div>';
    }
    container.innerHTML = html;
}
window.selectAppointmentTime = function(time) {
    selectedAppointmentTime = time;
    updateAvailableTimeSlots();
};
function fetchTechnicians() {
    return fetch(base + '/users.json').then(function(r) { return r.json(); }).then(function(data) {
        availableTechnicians = (data.users || []).filter(function(u) {
            return (u.role === 'technician' || u.userlevel === 'technician') && (u.status === 'active' || !u.status);
        });
        renderAvailableTechnicians();
        renderAssignedTechnicians();
        updateCounts();
    }).catch(function(err) { console.error(err); availableTechnicians = []; });
}
window.salonBookingSearchTechnicians = function(val) {
    technicianSearchTerm = (val || '').toLowerCase().trim();
    var clearBtn = document.getElementById('clearTechnicianSearchBtn');
    if (clearBtn) {
        if (val.trim()) clearBtn.classList.remove('hidden');
        else clearBtn.classList.add('hidden');
    }
    renderAvailableTechnicians();
};
window.salonBookingClearTechnicianSearch = function() {
    var inp = document.getElementById('technicianSearchInput'), clearBtn = document.getElementById('clearTechnicianSearchBtn');
    if (inp) { inp.value = ''; technicianSearchTerm = ''; inp.focus(); }
    if (clearBtn) clearBtn.classList.add('hidden');
    renderAvailableTechnicians();
};
function renderAvailableTechnicians() {
    var container = document.getElementById('availableTechniciansContainer');
    if (!container) return;
    if (!availableTechnicians.length) {
        container.innerHTML = '<div class="flex items-center justify-center h-full min-h-[90vh]"><p class="text-sm text-gray-400">No technicians available</p></div>';
        return;
    }
    var list = technicianSearchTerm ? availableTechnicians.filter(function(t) {
        var name = (t.firstName + ' ' + t.lastName).toLowerCase();
        var inits = (t.initials || (t.firstName || '')[0] + (t.lastName || '')[0]).toLowerCase();
        return (name + ' ' + inits).indexOf(technicianSearchTerm) >= 0;
    }) : availableTechnicians;
    if (!list.length) {
        container.innerHTML = '<div class="flex items-center justify-center h-full min-h-[90vh]"><p class="text-sm text-gray-400">No technicians found</p></div>';
        return;
    }
    container.innerHTML = list.map(function(tech) {
        var idStr = tech.id.toString(), isAssigned = assignedTechnicianIds.indexOf(idStr) >= 0;
        var inits = tech.initials || (tech.firstName || '')[0] + (tech.lastName || '')[0];
        var name = tech.firstName + ' ' + tech.lastName;
        var cls = isAssigned ? 'opacity-50 grayscale cursor-pointer group hover:bg-gray-100' : 'cursor-pointer group hover:bg-gray-50';
        return '<div onclick="' + (isAssigned ? 'salonBookingRemoveTechnician(' + tech.id + ')' : 'salonBookingAssignTechnician(' + tech.id + ')') + '" class="flex items-center gap-3 p-2 rounded-lg transition-colors ' + cls + '"><div class="relative flex-shrink-0"><div class="w-12 h-12 ' + (isAssigned ? 'bg-gray-300' : 'bg-gray-200') + ' rounded-full flex items-center justify-center"><span class="text-sm font-bold ' + (isAssigned ? 'text-gray-500' : 'text-gray-600') + '">' + inits + '</span></div><div class="absolute -bottom-1 -right-1 w-5 h-5 ' + (isAssigned ? 'bg-gray-400' : 'bg-[#003047]') + ' text-white rounded-full flex items-center justify-center text-xs font-bold border-2 border-white">0</div></div><div class="flex-1"><p class="text-base font-medium ' + (isAssigned ? 'text-gray-400' : 'text-gray-900') + '">' + name + '</p></div></div>';
    }).join('');
}
function renderAssignedTechnicians() {
    var container = document.getElementById('assignedTechniciansContainer');
    if (!container) return;
    if (!assignedTechnicianIds.length) {
        container.innerHTML = '<div class="flex items-center justify-center h-full min-h-[400px]"><p class="text-sm text-gray-400">No technicians assigned</p></div>';
        return;
    }
    container.innerHTML = assignedTechnicianIds.map(function(idStr) {
        var tech = availableTechnicians.find(function(t) { return t.id.toString() === idStr; });
        if (!tech) return '';
        var inits = tech.initials || (tech.firstName || '')[0] + (tech.lastName || '')[0];
        var name = tech.firstName + ' ' + tech.lastName;
        return '<div onclick="salonBookingRemoveTechnician(' + tech.id + ')" class="flex items-center gap-3 cursor-pointer group hover:bg-gray-50 p-2 rounded-lg transition-colors"><div class="relative flex-shrink-0"><div class="w-12 h-12 bg-[#003047] rounded-full flex items-center justify-center"><span class="text-sm font-bold text-white">' + inits + '</span></div><div class="absolute -bottom-1 -right-1 w-5 h-5 bg-[#003047] text-white rounded-full flex items-center justify-center text-xs font-bold border-2 border-white">0</div></div><div class="flex-1"><p class="text-base font-medium text-gray-900">' + name + '</p></div></div>';
    }).join('');
}
window.salonBookingAssignTechnician = function(techId) {
    var idStr = techId.toString();
    if (assignedTechnicianIds.indexOf(idStr) < 0) {
        assignedTechnicianIds.push(idStr);
        renderAvailableTechnicians();
        renderAssignedTechnicians();
        updateCounts();
    }
};
window.salonBookingRemoveTechnician = function(techId) {
    assignedTechnicianIds = assignedTechnicianIds.filter(function(id) { return id !== techId.toString(); });
    renderAvailableTechnicians();
    renderAssignedTechnicians();
    updateCounts();
};
function updateCounts() {
    var availEl = document.getElementById('availableCount'), assignEl = document.getElementById('assignedCount');
    var count = technicianSearchTerm ? availableTechnicians.filter(function(t) {
        var name = (t.firstName + ' ' + t.lastName).toLowerCase();
        var inits = (t.initials || (t.firstName || '')[0] + (t.lastName || '')[0]).toLowerCase();
        return (name + ' ' + inits).indexOf(technicianSearchTerm) >= 0;
    }).length : availableTechnicians.length;
    if (availEl) availEl.textContent = count.toString();
    if (assignEl) assignEl.textContent = assignedTechnicianIds.length.toString();
}
window.salonBookingSave = function() {
    if (!selectedCustomer) { alert('Please select a customer first.'); return; }
    if (!selectedAppointmentDate) { alert('Please select a booking date.'); return; }
    if (!selectedAppointmentTime) { alert('Please select a booking time.'); return; }
    if (!assignedTechnicianIds.length) { alert('Please assign at least one technician.'); return; }
    var bookingData = {
        customer_id: selectedCustomer.id,
        appointment: 'booked',
        status: 'waiting',
        created_at: new Date().toISOString(),
        assigned_technician: assignedTechnicianIds.map(function(id) { return parseInt(id, 10); }),
        appointment_date: selectedAppointmentDate.toISOString().split('T')[0],
        appointment_time: selectedAppointmentTime,
        services: []
    };
    console.log('Saving booking:', bookingData);
    showSuccessMessage('Booking created successfully for ' + selectedCustomer.firstName + ' ' + selectedCustomer.lastName + '!');
    setTimeout(function() { window.location.href = calendarUrl; }, 1500);
};
document.addEventListener('DOMContentLoaded', function() {
    Promise.all([fetchCustomers(), fetchTechnicians()]).then(function() {
        initializeAppointmentCalendar();
    });
});
})();
</script>
@endpush
@endsection

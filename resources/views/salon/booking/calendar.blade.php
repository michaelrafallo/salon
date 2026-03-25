@extends('layouts.salon')

@section('content')
@php
    $bookingUrl = route('salon.booking.index');
    $currentRole = session('salon_role', 'admin');
    $isTechnician = in_array($currentRole, ['technician'], true);
    $editBookingUrl = route('salon.booking.edit-booking');
    $appointmentsApiUrl = url('api/salon/appointments');
    $defaultCalendarId = \App\Models\Setting::query()->where('option_key', 'clickaio_calendar_id')->value('option_value') ?? '';
    $clickaioCalendars = \App\Models\Setting::query()->where('option_key', 'clickaio_calendars')->value('option_value') ?? '{}';
@endphp

<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1">Calendar</h1>
                    <p class="text-gray-500 text-sm">{{ now()->format('l, F d, Y') }}</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="hidden sm:flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded-full bg-white border-2 border-[#003047]"></div>
                            <span class="text-xs text-gray-600 font-medium">Not Assigned</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded-full bg-[#003047] border-2 border-[#003047]"></div>
                            <span class="text-xs text-gray-600 font-medium">Assigned</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <svg class="rotating-clock" style="width: 16px; height: 16px; color: #008106;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-xs text-gray-600 font-medium">Unpaid</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="w-4 h-4 rounded-full bg-[#9ca3af] border-2 border-[#9ca3af]"></div>
                            <span class="text-xs text-gray-600 font-medium">No Show</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-1 bg-gray-100 rounded-lg p-1">
                        <button id="gridViewBtn" onclick="salonCalendarToggleView('grid')" class="px-4 py-2 rounded-md transition-all flex items-center gap-2 view-toggle-btn">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                        </button>
                        <button id="listViewBtn" onclick="salonCalendarToggleView('list')" class="px-4 py-2 rounded-md transition-all flex items-center gap-2 view-toggle-btn active">
                            <svg class="w-5 h-5 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                        </button>
                    </div>
                    <a href="{{ $bookingUrl }}" class="px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm sm:text-base flex items-center gap-2 active:scale-95">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        <span>New Booking</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Appointment Filter Tabs -->
        <div class="mb-4">
            <div class="flex items-center gap-2 bg-white rounded-lg p-1 shadow-sm border border-gray-200 w-fit">
                <button id="tabAll" onclick="switchAppointmentTab('all')" class="px-6 py-2.5 rounded-md transition-all font-medium text-sm appointment-tab-btn active">
                    All
                </button>
                <button id="tabBooked" onclick="switchAppointmentTab('booked')" class="px-6 py-2.5 rounded-md transition-all font-medium text-sm appointment-tab-btn">
                    Booked
                </button>
                <button id="tabWalkIn" onclick="switchAppointmentTab('walkin')" class="px-6 py-2.5 rounded-md transition-all font-medium text-sm appointment-tab-btn">
                    Walk-In
                </button>
            </div>
        </div>

        <div id="calendarContainer" class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden hidden">
            <div id="calendar" class="w-full p-4 sm:p-6"></div>
        </div>
        <div id="listViewContainer" class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden mt-6">
            <div class="w-full">
                <div id="technicianListView" class="w-full">
                    <!-- List view will be rendered here -->
                </div>
            </div>
        </div>
    </div>
</main>

@push('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.calendar-select2 .select2-container--default .select2-selection--single { height: 38px; padding: 4px 8px; border: 1px solid #d1d5db; border-radius: 0.5rem; background: #fff; }
.calendar-select2 .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 28px; color: #374151; font-size: 0.875rem; }
.calendar-select2 .select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px; }
.calendar-select2 .select2-container--default.select2-container--focus .select2-selection--single { border-color: #003047; box-shadow: 0 0 0 2px rgba(0,48,71,.2); outline: none; }
.select2-dropdown { border-color: #d1d5db; border-radius: 0.5rem; }
.select2-results__option--highlighted[aria-selected] { background-color: #003047 !important; }
.select2-results__option { font-size: 0.875rem; }
.select2-search--dropdown .select2-search__field { border: 1px solid #d1d5db; border-radius: 0.375rem; padding: 6px 8px; font-size: 0.875rem; }
.select2-search--dropdown .select2-search__field:focus { border-color: #003047; outline: none; box-shadow: 0 0 0 2px rgba(0,48,71,.2); }
</style>
@endpush

@push('scripts')
<!-- FullCalendar CSS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css' rel='stylesheet' />

<!-- jQuery + Slick Carousel -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js'></script>

<script>
// Pass PHP role to JavaScript
const currentUserRole = '{{ $currentRole }}';
const isTechnician = currentUserRole === 'technician';
var base = window.salonJsonBase || '{{ url("api/salon/data") }}';
window.salonCalendarAppointmentsApiUrl = '{{ $appointmentsApiUrl }}';
window.salonWebhookApiUrl = '{{ url("api/salon/settings/webhook") }}';

// Store bookings and customers data
let bookingsData = [];
let customersData = [];
let techniciansData = [];
let selectedListViewDate = new Date(); // Default to today
let showOnlyBusyTechnicians = localStorage.getItem('showOnlyBusyTechnicians') === 'true'; // Toggle to show only technicians with bookings/events
let showOnlyBusyTimeSlots = localStorage.getItem('showOnlyBusyTimeSlots') === 'true'; // Toggle to show only time slots with events
let noShowStatus = {}; // Track no show status for appointments { appointmentId: true/false }
let statusBeforeNoShow = {}; // When marking no-show, remember previous status for uncheck
let selectedTechnicianIds = []; // Selected technician IDs for appointment
let currentAppointmentId = null; // Current appointment ID being edited
let technicianSearchTerm = ''; // Search term for technician search
let assignedTechnicianSearchTerm = ''; // Search term for assigned technicians
let resizeHandlerForTechnicians = null; // Resize handler for dynamic container heights
let currentEvent = null; // Current FullCalendar event being edited
let currentEventModalElement = null; // Reference to the technician display element in the event modal
let activeAppointmentFilter = 'all'; // Track active appointment type filter (all, booked, walkin)
let turnTrackerOrder = 'lowest'; // Turn tracker sort order from settings
let turnTrackerUserIds = new Set(); // User IDs listed in the turn tracker
let turnTrackerPositions = new Map(); // user_id → sorted position index from turn tracker
let ghlCalendars = []; // Cached GHL calendars list
let ghlDefaultCalendarId = '{{ $defaultCalendarId }}'; // Default calendar ID from settings
let clickaioCalendarsConfig = @json(json_decode($clickaioCalendars, true) ?? new \stdClass()); // Calendar color/selection config from settings

function getCalendarColor(calendarId) {
    if (clickaioCalendarsConfig && clickaioCalendarsConfig[calendarId] && clickaioCalendarsConfig[calendarId].color) {
        return clickaioCalendarsConfig[calendarId].color;
    }
    return null;
}

// --- Date helpers (avoid UTC date shifting for YYYY-MM-DD inputs) ---
function formatYmdLocal(date) {
    if (!date || isNaN(date.getTime())) return '';
    var y = date.getFullYear();
    var m = String(date.getMonth() + 1).padStart(2, '0');
    var d = String(date.getDate()).padStart(2, '0');
    return y + '-' + m + '-' + d;
}
function parseYmdAsLocalDate(ymd) {
    if (!ymd || typeof ymd !== 'string') return null;
    var match = ymd.trim().match(/^(\d{4})-(\d{2})-(\d{2})$/);
    if (!match) return null;
    var y = parseInt(match[1], 10);
    var m = parseInt(match[2], 10) - 1;
    var d = parseInt(match[3], 10);
    return new Date(y, m, d);
}
function parseIsoDatetimeLocal(isoStr) {
    if (!isoStr || typeof isoStr !== 'string') return null;
    // Treat stored datetimes as local by ignoring timezone suffixes.
    var s = isoStr.trim()
        .replace(/Z$/i, '')
        .replace(/([+-]\d{2}:?\d{2})$/i, '')
        .replace(/\.\d+/, '');
    var match = s.match(/^(\d{4})-(\d{2})-(\d{2})[T\s](\d{2}):(\d{2})(?::(\d{2}))?/);
    if (!match) return null;
    var y = parseInt(match[1], 10);
    var m = parseInt(match[2], 10) - 1;
    var d = parseInt(match[3], 10);
    var hh = parseInt(match[4], 10);
    var mm = parseInt(match[5], 10);
    var ss = parseInt(match[6] || '0', 10);
    return new Date(y, m, d, hh, mm, ss);
}

// Fetch customers to match phone numbers
async function fetchCustomers() {
    try {
        const response = await fetch(base + '/customers');
        const data = await response.json();
        customersData = data.customers;
    } catch (error) {
        console.error('Error fetching customers:', error);
        // Continue without customer data
    }
}

// Get customer phone by name
function getCustomerPhone(customerName) {
    if (!customersData || customersData.length === 0) {
        return '+1 (555) 000-0000';
    }
    
    const customer = customersData.find(function(c) {
        const fullName = c.firstName + ' ' + c.lastName;
        return fullName === customerName;
    });
    
    return customer ? customer.phone || '+1 (555) 000-0000' : '+1 (555) 000-0000';
}

// Fetch technicians from users.json
async function fetchTechnicians() {
    try {
        const response = await fetch(base + '/users');
        const data = await response.json();
        techniciansData = data.users.filter(user => user.role === 'technician' && user.status === 'active');
    } catch (error) {
        console.error('Error fetching technicians:', error);
        techniciansData = [];
    }
}

// Fetch turn tracker order setting and listed user IDs
async function fetchTurnTrackerOrder() {
    try {
        var settingsUrl = base.replace(/\/data\/?$/, '') + '/turn-tracker';
        const response = await fetch(settingsUrl, { credentials: 'same-origin' });
        const data = await response.json();
        turnTrackerOrder = data.turn_tracker_order === 'highest' ? 'highest' : 'lowest';
        const entries = (data.entries || []).map(function(e) {
            return { user_id: e.user_id, services: typeof e.services === 'number' ? e.services : parseFloat(e.services) || 0, clock_in: e.clock_in || null };
        });
        // Sort entries the same way the turn tracker page does
        entries.sort(function(a, b) {
            let diff = a.services - b.services;
            if (turnTrackerOrder === 'highest') diff = -diff;
            if (diff !== 0) return diff;
            var aTime = a.clock_in ? new Date(a.clock_in).getTime() : 0;
            var bTime = b.clock_in ? new Date(b.clock_in).getTime() : 0;
            return aTime - bTime;
        });
        turnTrackerUserIds = new Set(entries.map(function(e) { return e.user_id; }));
        turnTrackerPositions = new Map();
        entries.forEach(function(e, i) { turnTrackerPositions.set(e.user_id, i); });
    } catch (error) {
        console.error('Error fetching turn tracker order:', error);
    }
}

async function fetchGhlCalendars() {
    if (ghlCalendars.length > 0) return; // Already fetched
    try {
        const response = await fetch('{{ url("api/salon/settings/clickaio/calendars") }}', { credentials: 'same-origin' });
        const data = await response.json();
        if (data.success && Array.isArray(data.calendars)) {
            ghlCalendars = data.calendars;
        }
    } catch (error) {
        console.error('Error fetching GHL calendars:', error);
    }
}

function updateSyncCalendarBtn(appointmentId) {
    const btn = document.getElementById('syncCalendarBtn_' + appointmentId);
    const statusEl = document.getElementById('calendarSyncStatus_' + appointmentId);
    if (!btn) return;
    const selectedVal = $('#calendarSelect_' + appointmentId).val() || '';
    const syncedCalId = btn.getAttribute('data-synced-calendar') || '';
    const apt = bookingsData.find(a => a.id.toString() === appointmentId.toString());
    const isSynced = !!(apt && apt.ghl_appointment_id && syncedCalId === selectedVal);
    btn.disabled = isSynced;
    btn.className = btn.className
        .replace(/bg-gray-300 text-gray-500 cursor-not-allowed/g, '')
        .replace(/bg-\[#003047\] text-white hover:bg-\[#002535\]/g, '')
        + (isSynced ? ' bg-gray-300 text-gray-500 cursor-not-allowed' : ' bg-[#003047] text-white hover:bg-[#002535]');
    if (statusEl) {
        if (isSynced) {
            statusEl.innerHTML = '<span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold border bg-green-100 text-green-700 border-green-200"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Synced</span>';
        } else if (!apt || !apt.ghl_appointment_id) {
            statusEl.innerHTML = '<span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold border bg-gray-100 text-gray-500 border-gray-200"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01"></path></svg> Not synced</span>';
        } else {
            statusEl.innerHTML = '';
        }
    }
}

function initCalendarSelect2(appointmentId) {
    var $select = $('#calendarSelect_' + appointmentId);
    if ($select.length && $.fn.select2) {
        $select.select2({
            placeholder: 'Select calendar',
            width: '100%',
            dropdownParent: $('#modalOverlay')
        });
        $select.on('change', function() {
            var calendarId = $(this).val();
            if (calendarId) {
                syncAppointmentToCalendar(appointmentId, calendarId);
                // Auto-update event color to match the new calendar's color
                var calColor = getCalendarColor(calendarId);
                if (calColor) {
                    var calHex = calColor.toUpperCase();
                    var calName = (clickaioCalendarsConfig && clickaioCalendarsConfig[calendarId] && clickaioCalendarsConfig[calendarId].name) || 'Calendar';
                    setEventColor(appointmentId, calColor, true);
                    // Update color preview inline
                    var previewEl = document.getElementById('eventColorPreview_' + appointmentId);
                    if (previewEl) {
                        previewEl.innerHTML = '<div style="width:48px;height:48px;border-radius:9999px;border:2px solid #fff;box-shadow:0 4px 6px -1px rgba(0,0,0,.1);background:' + calColor + '"></div><button onclick="setEventColor(\'' + appointmentId + '\', null)" style="position:absolute;top:-4px;left:-4px;width:18px;height:18px;border-radius:9999px;background:#fff;border:1px solid #d1d5db;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 1px 2px rgba(0,0,0,.1)" title="Remove color"><svg style="width:10px;height:10px" fill="none" stroke="#9ca3af" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg></button>';
                    }
                    // Replace the 1st swatch (calendar swatch) with the new calendar's color
                    var swatchContainer = document.getElementById('eventColorSwatches_' + appointmentId);
                    if (swatchContainer) {
                        var firstSwatch = swatchContainer.querySelector('button[data-calendar-swatch]');
                        if (firstSwatch) {
                            firstSwatch.style.background = calHex;
                            firstSwatch.setAttribute('title', calName);
                            firstSwatch.setAttribute('onclick', "setEventColor('" + appointmentId + "', '" + calHex + "')");
                        } else {
                            // No calendar swatch yet — insert one at the beginning
                            var newSwatch = document.createElement('button');
                            newSwatch.setAttribute('data-calendar-swatch', 'true');
                            newSwatch.setAttribute('onclick', "setEventColor('" + appointmentId + "', '" + calHex + "')");
                            newSwatch.className = 'rounded-full border-2 transition-all hover:scale-110 border-gray-900 ring-2 ring-offset-1 ring-gray-900';
                            newSwatch.style.cssText = 'width:28px;height:28px;background:' + calHex;
                            newSwatch.title = calName;
                            swatchContainer.insertBefore(newSwatch, swatchContainer.firstChild);
                        }
                        // Update selection state: select 1st swatch, deselect others
                        swatchContainer.querySelectorAll('button.rounded-full').forEach(function(btn, idx) {
                            if (btn.getAttribute('data-calendar-swatch') === 'true') {
                                btn.className = 'rounded-full border-2 transition-all hover:scale-110 border-gray-900 ring-2 ring-offset-1 ring-gray-900';
                            } else {
                                btn.className = btn.className.replace(/border-gray-900/g, 'border-white').replace(/ring-2 ring-offset-1 ring-gray-900/g, 'shadow-sm');
                            }
                        });
                    }
                }
            }
        });
    }
}

async function syncAppointmentToCalendar(appointmentId, calendarId) {
    if (!calendarId) return;
    const apiUrl = window.salonCalendarAppointmentsApiUrl;
    const statusEl = document.getElementById('calendarSyncStatus_' + appointmentId);
    try {
        if (statusEl) {
            statusEl.innerHTML = '<span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold border bg-blue-100 text-blue-700 border-blue-200"><svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Syncing...</span>';
        }
        const res = await salonApi.post(apiUrl + '/' + appointmentId + '/sync-calendar', { calendar_id: calendarId });
        if (res.success && res.data.ghl_appointment_id) {
            const apt = bookingsData.find(a => a.id.toString() === appointmentId.toString());
            if (apt) {
                apt.ghl_appointment_id = res.data.ghl_appointment_id;
                apt.ghl_calendar_id = res.data.ghl_calendar_id;
            }
            if (statusEl) {
                statusEl.innerHTML = '<span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold border bg-green-100 text-green-700 border-green-200"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Synced</span>';
            }
            showSuccessMessage('Appointment synced to calendar successfully.');
        } else {
            showErrorMessage('Failed to sync appointment to calendar.');
            if (statusEl) {
                statusEl.innerHTML = '<span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold border bg-red-100 text-red-700 border-red-200"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg> Failed</span>';
            }
        }
    } catch (error) {
        console.error('Error syncing to calendar:', error);
        showErrorMessage(error && error.message ? error.message : 'Failed to sync appointment to calendar.');
        if (statusEl) {
            statusEl.innerHTML = '<span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold border bg-red-100 text-red-700 border-red-200"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg> Failed</span>';
        }
    }
}

// Fetch appointments from JSON
async function fetchBookings() {
    try {
        // Fetch appointments, customers, technicians, and turn tracker order in parallel
        await Promise.all([fetchCustomers(), fetchTechnicians(), fetchTurnTrackerOrder(), fetchGhlCalendars()]);
        
        const response = await fetch(base + '/appointments');
        const data = await response.json();
        bookingsData = data.appointments || [];
        noShowStatus = {};
        (bookingsData || []).forEach(function(apt) {
            if (apt.status === 'no-show') noShowStatus[apt.id] = true;
        });

        // Apply appointment type filter
        const filteredAppointments = getFilteredAppointments(bookingsData);
        return convertAppointmentsToEvents(filteredAppointments);
    } catch (error) {
        console.error('Error fetching appointments:', error);
        showErrorMessage('Failed to load appointments data');
        return [];
    }
}

// Convert appointments data to FullCalendar events format
function convertAppointmentsToEvents(appointments) {
    return appointments.map(function(appointment) {
        // Map status to event class and display text
        const statusMap = {
            'waiting': { class: 'event-in-booking', display: 'Waiting' },
            'in-progress': { class: 'event-in-progress', display: 'In Progress' },
            'completed': { class: 'event-completed', display: 'Completed' },
            'paid': { class: 'event-completed', display: 'Paid' },
            'no-show': { class: 'event-no-show', display: 'No Show' }
        };
        
        const statusInfo = statusMap[appointment.status] || { class: 'event-booked', display: 'Booked' };
        
        // Get customer name from customers data
        const customer = customersData.find(c => c.id.toString() === appointment.customer_id.toString());
        const customerName = customer ? `${customer.firstName} ${customer.lastName}` : `Customer #${appointment.customer_id}`;
        const customerPhone = customer ? (customer.phone || 'No phone') : 'No phone';
        const customerEmail = customer ? (customer.email || '') : '';
        
        // Parse appointment datetime as local time (ignore Z so saved time displays correctly in calendar and modal)
        let appointmentDateTime = appointment.appointment_datetime;
        let startDate;
        if (appointmentDateTime) {
            if (typeof appointmentDateTime !== 'string') appointmentDateTime = String(appointmentDateTime);
            if (appointmentDateTime.indexOf('T') === -1) appointmentDateTime = appointmentDateTime + 'T10:00:00';
            const dateStr = appointmentDateTime.trim().replace(/Z$/, '').replace(/[+-]\d{2}:\d{2}$/, '').replace(/\.\d+/, '');
            const match = dateStr.match(/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2})(?::(\d{2}))?/);
            if (match) {
                const year = parseInt(match[1], 10);
                const month = parseInt(match[2], 10) - 1;
                const day = parseInt(match[3], 10);
                const hours = parseInt(match[4], 10);
                const minutes = parseInt(match[5], 10) || 0;
                const seconds = parseInt(match[6], 10) || 0;
                startDate = new Date(year, month, day, hours, minutes, seconds);
            } else {
                startDate = new Date(appointmentDateTime);
            }
        } else {
            startDate = new Date();
        }
        
        // Format as ISO string preserving local time (FullCalendar accepts Date objects or ISO strings)
        // Use the Date object directly or format without timezone to preserve local time
        const year = startDate.getFullYear();
        const month = String(startDate.getMonth() + 1).padStart(2, '0');
        const day = String(startDate.getDate()).padStart(2, '0');
        const hours = String(startDate.getHours()).padStart(2, '0');
        const minutes = String(startDate.getMinutes()).padStart(2, '0');
        const seconds = String(startDate.getSeconds()).padStart(2, '0');
        appointmentDateTime = `${year}-${month}-${day}T${hours}:${minutes}:${seconds}`;
        
        // Calculate duration based on number of services (default 1 hour, add 30 min per additional service)
        const serviceCount = appointment.services ? appointment.services.length : 1;
        const durationHours = Math.max(1, Math.min(3, 1 + (serviceCount - 1) * 0.5)); // Min 1 hour, max 3 hours
        
        // Calculate end datetime
        const endDate = new Date(startDate);
        endDate.setHours(endDate.getHours() + Math.floor(durationHours));
        endDate.setMinutes(endDate.getMinutes() + ((durationHours % 1) * 60));
        
        // Format technicians array as string - get names from techniciansData
        let technicians = 'Not Assigned';
        if (appointment.assigned_technician && Array.isArray(appointment.assigned_technician) && appointment.assigned_technician.length > 0) {
            const technicianNames = appointment.assigned_technician.map(techId => {
                const tech = techniciansData.find(t => t.id.toString() === techId.toString());
                return tech ? `${tech.firstName} ${tech.lastName}` : `Technician #${techId}`;
            });
            technicians = technicianNames.join(', ');
        }
        
        // Format services as string - get service names from services array
        let services = 'No services';
        if (appointment.services && Array.isArray(appointment.services) && appointment.services.length > 0) {
            services = appointment.services.map(s => s.service || 'Service').join(', ');
        }
        
        // Format price - not available in appointments
        const price = window.salonFormatMoney(0);
        
        // Check if technician is assigned to determine event styling
        const hasTechnician = appointment.assigned_technician && 
                              Array.isArray(appointment.assigned_technician) && 
                              appointment.assigned_technician.length > 0;
        
        // Check if appointment is marked as no show
        const isNoShow = noShowStatus[appointment.id] === true;
        
        // Build class names array - add technician-assigned class if has technician
        const classNames = [statusInfo.class];
        if (hasTechnician) {
            classNames.push('event-has-technician');
        }
        if (isNoShow) {
            classNames.push('event-no-show');
        }
        
        // Determine event color from the first service color
        let firstServiceColor = null;
        if (appointment.services && Array.isArray(appointment.services) && appointment.services.length > 0) {
            for (let si = 0; si < appointment.services.length; si++) {
                let sColor = appointment.services[si].service_color;
                if (!sColor && appointment.services[si].service_id && typeof selectServicesData !== 'undefined' && selectServicesData.length > 0) {
                    const svcInfo = selectServicesData.find(d => d.id === appointment.services[si].service_id);
                    if (svcInfo && svcInfo.color) sColor = svcInfo.color;
                }
                if (sColor) { firstServiceColor = sColor; break; }
            }
        }

        // Apply event colors based on state — no-show always wins (CSS handles it)
        let eventBgColor, eventBorderColor, eventTextColor;

        if (isNoShow) {
            eventBgColor = '';
            eventBorderColor = '';
            eventTextColor = '';
        } else if (firstServiceColor) {
            eventBgColor = firstServiceColor;
            eventBorderColor = firstServiceColor;
            eventTextColor = '#ffffff';
            classNames.push('event-custom-color');
        } else if (appointment.color) {
            eventBgColor = appointment.color;
            eventBorderColor = appointment.color;
            eventTextColor = '#ffffff';
            classNames.push('event-custom-color');
        } else {
            eventBgColor = hasTechnician ? '#003047' : 'transparent';
            eventBorderColor = '#003047';
            eventTextColor = hasTechnician ? '#ffffff' : '#003047';
        }

        return {
            id: appointment.id,
            title: customerName,
            start: appointmentDateTime,
            end: endDate.toISOString(),
            backgroundColor: eventBgColor,
            borderColor: eventBorderColor,
            textColor: eventTextColor,
            classNames: classNames,
            extendedProps: {
                customer: customerName,
                service: services,
                technician: technicians,
                price: price,
                status: statusInfo.display,
                phone: customerPhone,
                email: customerEmail,
                bookingId: appointment.id,
                bookingType: appointment.appointment || 'booked',
                originalStatus: appointment.status,
                hasTechnician: hasTechnician,
                isNoShow: isNoShow,
                color: appointment.color || null
            }
        };
    });
}

// Store calendar instance globally
let calendarInstance = null;
let isInitialLoad = true; // Track if this is the initial page load

document.addEventListener('DOMContentLoaded', async function() {
    var calendarEl = document.getElementById('calendar');
    
    // Get view from URL parameter
    var urlParams = new URLSearchParams(window.location.search);
    var viewParam = urlParams.get('view');
    var dateParam = urlParams.get('date');
    var monthParam = urlParams.get('month');
    var yearParam = urlParams.get('year');
    
    // If date parameter exists and view is list, use it
    if (viewParam === 'list' && dateParam) {
        selectedListViewDate = parseYmdAsLocalDate(dateParam) || parseIsoDatetimeLocal(dateParam) || new Date(dateParam);
    }
    
    // Map URL view names to FullCalendar view names
    var viewMap = {
        'month': 'dayGridMonth',
        'week': 'timeGridWeek',
        'day': 'timeGridDay',
        'list': 'listWeek'
    };
    
    // Get initial view from URL or default to month
    var initialView = viewMap[viewParam] || 'dayGridMonth';
    
    // Calculate initial date based on URL parameters
    var initialDate = null;
    if (monthParam && yearParam && viewParam === 'month') {
        const monthNames = ['january', 'february', 'march', 'april', 'may', 'june', 
                          'july', 'august', 'september', 'october', 'november', 'december'];
        const monthIndex = monthNames.indexOf(monthParam.toLowerCase());
        if (monthIndex !== -1) {
            const year = parseInt(yearParam);
            if (!isNaN(year)) {
                // Create date for the first day of the specified month/year
                initialDate = new Date(year, monthIndex, 1);
            }
        }
    }

    // Initialize appointment type filter from localStorage
    const savedFilter = localStorage.getItem('activeAppointmentFilter');
    if (savedFilter && ['all', 'booked', 'walkin'].includes(savedFilter)) {
        activeAppointmentFilter = savedFilter;
    }
    updateAppointmentTabButtons(activeAppointmentFilter);

    // Handle initial view - show calendar or list view
    const calendarContainer = document.getElementById('calendarContainer');
    const listViewContainer = document.getElementById('listViewContainer');

    // Fetch bookings initially to populate bookingsData
    await fetchBookings();

    if (viewParam && viewParam !== 'list') {
        // Grid/calendar view (month, week, day, grid)
        if (calendarContainer) calendarContainer.classList.remove('hidden');
        if (listViewContainer) listViewContainer.classList.add('hidden');
        updateViewButtons('grid');
    } else {
        // Default to list view
        if (calendarContainer) calendarContainer.classList.add('hidden');
        if (listViewContainer) listViewContainer.classList.remove('hidden');
        updateViewButtons('list');
        // Render list view after data is loaded
        renderTechnicianListView();
    }

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: initialView,
        initialDate: initialDate || undefined,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: ''
        },
        height: 'auto',
        events: fetchBookings,
        eventClick: function(info) {
            const event = info.event;
            const extendedProps = event.extendedProps;
            
            // Store event reference for technician selection
            currentEvent = event;
            
            // Find the actual appointment to get full data
            const appointment = bookingsData.find(a => a.id.toString() === event.id.toString());
            
            // Build appointment data object from event
            const bookingTypeRaw = (appointment && appointment.appointment) ? appointment.appointment : (extendedProps.bookingType || 'booked');
            const appointmentData = {
                id: event.id,
                customer: extendedProps.customer,
                technician: extendedProps.technician,
                phone: extendedProps.phone,
                email: extendedProps.email || '',
                status: extendedProps.originalStatus || extendedProps.status,
                date: event.start,
                isNoShow: extendedProps.isNoShow || noShowStatus[event.id] || false,
                assigned_technician: appointment ? appointment.assigned_technician : null,
                bookingType: bookingTypeRaw === 'walk-in' ? 'Walk-In' : 'Booked',
                color: extendedProps.color || (appointment ? appointment.color : null) || null
            };
            
            // Use shared modal function
            showAppointmentModal(appointmentData);
        },
        eventMouseEnter: function(info) {
            info.el.style.cursor = 'pointer';
        },
        eventDidMount: function(info) {
            info.el.setAttribute('data-event-id', info.event.id);
            var customColor = info.event.extendedProps.color;
            var isNoShow = info.event.extendedProps.isNoShow || false;
            if (isNoShow) {
                // No-show always wins — remove all inline styles so CSS .event-no-show takes over
                info.el.removeAttribute('style');
                var mainEl = info.el.querySelector('.fc-event-main');
                if (mainEl) mainEl.removeAttribute('style');
            } else if (customColor) {
                info.el.style.setProperty('background-color', customColor, 'important');
                info.el.style.setProperty('background', customColor, 'important');
                info.el.style.setProperty('border-color', customColor, 'important');
                info.el.style.setProperty('color', '#ffffff', 'important');
                info.el.setAttribute('data-custom-color', customColor);
            }
        },
        eventDragStart: function(info) {
            // Add class to body for global cursor styling
            document.body.classList.add('fc-dragging');
            // Make the dragged event more visible
            info.el.classList.add('fc-event-dragging');
        },
        eventDrag: function(info) {
        },
        eventDragStop: function(info) {
            // Remove class from body and reset cursor
            document.body.classList.remove('fc-dragging');
            document.body.classList.remove('fc-drag-not-allowed');
            // Remove dragging class from event
            info.el.classList.remove('fc-event-dragging');
            info.el.classList.remove('fc-drag-not-allowed');
        },
        eventDrop: isTechnician ? null : function(info) {
            // Event has been dropped at a new date/time
            const event = info.event;
            const newStart = event.start;
            const newEnd = event.end;

            // Update the appointment data in bookingsData
            const appointmentId = parseInt(event.id);
            const appointment = bookingsData.find(apt => apt.id === appointmentId);
            
            if (appointment) {
                // Format new datetime
                const year = newStart.getFullYear();
                const month = String(newStart.getMonth() + 1).padStart(2, '0');
                const day = String(newStart.getDate()).padStart(2, '0');
                const hours = String(newStart.getHours()).padStart(2, '0');
                const minutes = String(newStart.getMinutes()).padStart(2, '0');
                const seconds = String(newStart.getSeconds()).padStart(2, '0');
                
                // Update appointment datetime fields
                appointment.appointment_datetime = `${year}-${month}-${day}T${hours}:${minutes}:${seconds}`;
                appointment.appointment_date = `${year}-${month}-${day}`;
                appointment.appointment_time = `${hours}:${minutes}`;
                
                // Persist to backend
                const apiUrl = window.salonCalendarAppointmentsApiUrl;
                if (apiUrl && typeof salonApi !== 'undefined' && salonApi.put) {
                    salonApi.put(apiUrl + '/' + appointmentId, { appointment_datetime: appointment.appointment_datetime })
                        .then(function(res) {
                            if (res && res.data) {
                                appointment.appointment_datetime = res.data.appointment_datetime || appointment.appointment_datetime;
                                if (Array.isArray(res.data.assigned_technician)) {
                                    appointment.assigned_technician = res.data.assigned_technician;
                                }
                            }
                        })
                        .catch(function(err) {
                            info.revert();
                            if (typeof showErrorMessage === 'function') {
                                showErrorMessage(err && err.message ? err.message : 'Failed to update appointment time.');
                            }
                        });
                }
                
                // Update list view if it's currently visible
                const listViewContainer = document.getElementById('listViewContainer');
                if (listViewContainer && !listViewContainer.classList.contains('hidden')) {
                    renderTechnicianListView();
            }
            
            // Show success message
                const timeDisplay = newStart.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                const dateDisplay = newStart.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                showToastMessage(`Appointment moved to ${dateDisplay} at ${timeDisplay}`, 'success');
            } else {
                // Fallback to old message if appointment not found
            showEventMovedMessage(event.title, newStart);
            }
            
            // Log for debugging
            console.log('Event moved:', {
                title: event.title,
                newStart: newStart,
                newEnd: newEnd,
                oldStart: info.oldEvent.start,
                oldEnd: info.oldEvent.end,
                view: currentView
            });
        },
        eventResize: isTechnician ? null : function(info) {
            // Event duration has been changed
            const event = info.event;
            const newStart = event.start;
            const newEnd = event.end;
            
            // Update the appointment data in bookingsData
            const appointmentId = parseInt(event.id);
            const appointment = bookingsData.find(apt => apt.id === appointmentId);
            
            if (appointment) {
                // Format new datetime
                const year = newStart.getFullYear();
                const month = String(newStart.getMonth() + 1).padStart(2, '0');
                const day = String(newStart.getDate()).padStart(2, '0');
                const hours = String(newStart.getHours()).padStart(2, '0');
                const minutes = String(newStart.getMinutes()).padStart(2, '0');
                const seconds = String(newStart.getSeconds()).padStart(2, '0');
                
                // Update appointment datetime fields
                appointment.appointment_datetime = `${year}-${month}-${day}T${hours}:${minutes}:${seconds}`;
                appointment.appointment_date = `${year}-${month}-${day}`;
                appointment.appointment_time = `${hours}:${minutes}`;
                
                // TODO: Save to backend/JSON file
                console.log('Appointment duration updated:', {
                    id: appointmentId,
                    newStart: newStart,
                    newEnd: newEnd,
                    appointment: appointment
                });
                
                // Update list view if it's currently visible
                const listViewContainer = document.getElementById('listViewContainer');
                if (listViewContainer && !listViewContainer.classList.contains('hidden')) {
                    renderTechnicianListView();
                }
                
                // Show success message
                const duration = Math.round((newEnd - newStart) / (1000 * 60)); // Duration in minutes
                showToastMessage(`Appointment duration updated to ${duration} minutes`, 'success');
            }
        },
        dayMaxEvents: 3,
        moreLinkClick: 'popover',
        eventDisplay: 'block',
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            meridiem: 'short'
        },
        eventContent: function(arg) {
            // Check if event is unpaid
            const originalStatus = arg.event.extendedProps.originalStatus;
            const isUnpaid = originalStatus === 'unpaid';
            const isConfirmed = originalStatus === 'confirmed';

            // Format time
            const timeText = arg.timeText;

            // Create custom content
            const arrayOfDomNodes = [];

            // Create time wrapper with flex layout for proper alignment
            const timeEl = document.createElement('div');
            timeEl.className = 'fc-event-time';
            timeEl.style.display = 'flex';
            timeEl.style.alignItems = 'center';
            timeEl.style.gap = '4px';

            if (isConfirmed) {
                const checkIcon = document.createElement('span');
                checkIcon.style.display = 'inline-flex';
                checkIcon.style.alignItems = 'center';
                checkIcon.innerHTML = `<svg style="width: 12px; height: 12px; color: #16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`;
                timeEl.appendChild(checkIcon);
            } else if (isUnpaid) {
                // Add green clock icon before time for unpaid events
                const clockIcon = document.createElement('span');
                clockIcon.className = 'rotating-clock';
                clockIcon.style.display = 'inline-flex';
                clockIcon.style.alignItems = 'center';
                clockIcon.innerHTML = `<svg style="width: 12px; height: 12px; color: #008106;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`;
                timeEl.appendChild(clockIcon);
            }

            const timeSpan = document.createElement('span');
            timeSpan.innerText = timeText;
            timeEl.appendChild(timeSpan);

            arrayOfDomNodes.push(timeEl);

            // Create title
            const titleEl = document.createElement('div');
            titleEl.className = 'fc-event-title';
            titleEl.innerText = arg.event.title;
            arrayOfDomNodes.push(titleEl);

            return { domNodes: arrayOfDomNodes };
        },
        slotMinTime: '08:00:00',
        slotMaxTime: '24:00:00',
        businessHours: {
            daysOfWeek: [1, 2, 3, 4, 5, 6],
            startTime: '09:00',
            endTime: '18:00',
        },
        nowIndicator: true,
        editable: !isTechnician,
        eventStartEditable: !isTechnician,
        eventDurationEditable: !isTechnician,
        selectable: true,
        selectMirror: true,
        weekends: true,
        locale: 'en',
        viewDidMount: function(view) {
            // Only update URL if we're in calendar view (not when switching from list)
            var viewNameMap = {
                'dayGridMonth': 'month',
                'timeGridWeek': 'week',
                'timeGridDay': 'day',
                'listWeek': 'list'
            };
            
            // Check if we're currently showing the calendar container (not list view)
            const calendarContainer = document.getElementById('calendarContainer');
            if (calendarContainer && !calendarContainer.classList.contains('hidden')) {
                var viewName = viewNameMap[view.view.type] || 'month';
                var url = new URL(window.location);
                url.searchParams.set('view', viewName);
                url.searchParams.delete('date'); // Remove date param when in calendar view
                window.history.pushState({}, '', url);
            }
            
            // Update button states based on current view (only for calendar views, and only if calendar is visible)
            if (view.view.type === 'dayGridMonth' && calendarContainer && !calendarContainer.classList.contains('hidden')) {
                updateViewButtons('grid');
            }
            
        },
        datesSet: function(dateInfo) {
            // Update URL with month when arrows are clicked (not on initial load)
            if (!isInitialLoad) {
                const calendarContainer = document.getElementById('calendarContainer');
                if (calendarContainer && !calendarContainer.classList.contains('hidden')) {
                    const currentView = dateInfo.view.type;
                    
                    // Only update URL for month view when arrows are clicked
                    if (currentView === 'dayGridMonth') {
                        // Get the actual month being displayed from the view's current start date
                        // dateInfo.start gives us the first visible day (which might be from previous month)
                        // We need to find a day that's actually in the month being displayed

                        // Get a date in the middle of the visible range to ensure we're in the target month
                        const startDate = new Date(dateInfo.start);
                        const endDate = new Date(dateInfo.end);
                        const middleDate = new Date((startDate.getTime() + endDate.getTime()) / 2);

                        const monthNames = ['january', 'february', 'march', 'april', 'may', 'june',
                                          'july', 'august', 'september', 'october', 'november', 'december'];

                        const monthIndex = middleDate.getMonth(); // 0-11
                        const monthName = monthNames[monthIndex];
                        const year = middleDate.getFullYear();

                        // Update URL with month and year parameters
                        var url = new URL(window.location);
                        url.searchParams.set('view', 'month');
                        url.searchParams.set('month', monthName);
                        url.searchParams.set('year', year.toString());
                        url.searchParams.delete('date'); // Remove date param when in calendar view
                        window.history.pushState({}, '', url);
                    }
                }
            }
            
            // Gray out past days when dates change
        },
        dayCellClassNames: function(info) {
            return [];
        }
    });
    
    calendar.render();
    calendarInstance = calendar;
    
    // Mark initial load as complete after a short delay to allow calendar to render
    setTimeout(function() {
        isInitialLoad = false;
    }, 500);
    
    // Make calendar responsive
    window.addEventListener('resize', function() {
        calendar.updateSize();
    });
});

// Switch between grid and list views
function switchView(viewType) {
    const calendarContainer = document.getElementById('calendarContainer');
    const listViewContainer = document.getElementById('listViewContainer');
    
    if (viewType === 'grid') {
        // Show calendar container first
        if (calendarContainer) calendarContainer.classList.remove('hidden');
        if (listViewContainer) listViewContainer.classList.add('hidden');
        
        updateViewButtons('grid');
        
        // Update URL - remove date parameter when switching to calendar view
        var url = new URL(window.location);
        url.searchParams.set('view', 'month');
        url.searchParams.delete('date');
        window.history.pushState({}, '', url);
        
        // Wait for container to be visible, then update calendar
        setTimeout(() => {
            if (calendarInstance) {
                // Change view to month if not already
                if (calendarInstance.view.type !== 'dayGridMonth') {
                    calendarInstance.changeView('dayGridMonth');
                }
                // Update calendar size after container becomes visible
                calendarInstance.updateSize();
                // Refresh calendar to ensure it displays correctly
                calendarInstance.render();
            }
        }, 50);
    } else if (viewType === 'list') {
        if (calendarContainer) calendarContainer.classList.add('hidden');
        if (listViewContainer) listViewContainer.classList.remove('hidden');
        updateViewButtons('list');
        renderTechnicianListView();
        // Update URL with current date
        var url = new URL(window.location);
        url.searchParams.set('view', 'list');
        const currentDateString = formatYmdLocal(selectedListViewDate) || selectedListViewDate.toISOString().split('T')[0];
        url.searchParams.set('date', currentDateString);
        window.history.pushState({}, '', url);
    }
}

// Alias function for onclick handlers
function salonCalendarToggleView(viewType) {
    switchView(viewType);
}

// Update button active states
function updateViewButtons(activeView) {
    const gridBtn = document.getElementById('gridViewBtn');
    const listBtn = document.getElementById('listViewBtn');

    if (gridBtn && listBtn) {
        const gridSvg = gridBtn.querySelector('svg');
        const listSvg = listBtn.querySelector('svg');
        if (activeView === 'grid') {
            gridBtn.classList.add('active');
            listBtn.classList.remove('active');
            if (gridSvg) { gridSvg.classList.remove('text-gray-500'); gridSvg.classList.add('text-gray-900'); }
            if (listSvg) { listSvg.classList.remove('text-gray-900'); listSvg.classList.add('text-gray-500'); }
        } else {
            listBtn.classList.add('active');
            gridBtn.classList.remove('active');
            if (listSvg) { listSvg.classList.remove('text-gray-500'); listSvg.classList.add('text-gray-900'); }
            if (gridSvg) { gridSvg.classList.remove('text-gray-900'); gridSvg.classList.add('text-gray-500'); }
        }
    }
}

// Render technician list view
function renderTechnicianListView() {
    const container = document.getElementById('technicianListView');
    if (!container) return;

    if (!techniciansData || techniciansData.length === 0) {
        container.innerHTML = '<p class="text-center text-gray-500 py-8">No technicians available</p>';
        return;
    }

    // Apply appointment type filter
    const filteredBookings = getFilteredAppointments(bookingsData);

    // Match the turn tracker page ordering exactly: technicians in the turn tracker first
    // (in the same sorted order as the turn tracker page), then non-tracker technicians last.
    const orderedTechnicians = [...techniciansData].sort((a, b) => {
        const aInTracker = turnTrackerUserIds.has(a.id);
        const bInTracker = turnTrackerUserIds.has(b.id);
        if (aInTracker && !bInTracker) return -1;
        if (!aInTracker && bInTracker) return 1;
        if (aInTracker && bInTracker) {
            return (turnTrackerPositions.get(a.id) || 0) - (turnTrackerPositions.get(b.id) || 0);
        }
        return 0;
    });
    
    // Use selected date for filtering appointments
    const selectedDate = new Date(selectedListViewDate);
    selectedDate.setHours(0, 0, 0, 0);

    // Filter to only show technicians with bookings/events on this day if toggle is on
    if (showOnlyBusyTechnicians) {
        const busyTechIds = new Set();
        filteredBookings.forEach(apt => {
            // Parse appointment date
            let aptDate = null;
            if (apt.appointment_date) {
                const dateParts = apt.appointment_date.split('-');
                aptDate = new Date(parseInt(dateParts[0]), parseInt(dateParts[1]) - 1, parseInt(dateParts[2]));
            } else if (apt.appointment_datetime) {
                aptDate = parseIsoDatetimeLocal(apt.appointment_datetime) || new Date(apt.appointment_datetime);
            } else if (apt.created_at) {
                aptDate = parseIsoDatetimeLocal(apt.created_at) || new Date(apt.created_at);
            }
            if (!aptDate || isNaN(aptDate.getTime())) return;
            const aptDateOnly = new Date(aptDate);
            aptDateOnly.setHours(0, 0, 0, 0);
            if (aptDateOnly.getTime() !== selectedDate.getTime()) return;

            // Mark assigned technicians as busy
            if (apt.assigned_technician && Array.isArray(apt.assigned_technician)) {
                apt.assigned_technician.forEach(id => busyTechIds.add(id.toString()));
            }
        });
        // Remove technicians with no bookings on this day
        for (let i = orderedTechnicians.length - 1; i >= 0; i--) {
            if (!busyTechIds.has(orderedTechnicians[i].id.toString())) {
                orderedTechnicians.splice(i, 1);
            }
        }
    }

    // Format date for date picker (YYYY-MM-DD)
    const datePickerValue = formatYmdLocal(selectedDate) || selectedDate.toISOString().split('T')[0];
    
    // Generate 24-hour time slots starting at 8:00 AM
    const timeSlots = [];
    for (let i = 0; i < 24; i++) {
        const hour = (8 + i) % 24;
        const timeString = `${hour.toString().padStart(2, '0')}:00`;
        const displayTime = `${hour > 12 ? hour - 12 : hour === 0 ? 12 : hour === 12 ? 12 : hour}:00 ${hour >= 12 ? 'PM' : 'AM'}`;
        timeSlots.push({ value: timeString, display: displayTime, hour: hour });
    }
    
    // Build HTML
    let html = '<div class="overflow-x-auto">';
    html += '<table class="w-full border-collapse">';
    html += '<colgroup><col style="width: 155px; min-width: 155px; max-width: 155px;"></colgroup>';
    html += '<thead>';
    html += '<tr>';
    html += '<th valign="bottom" class="sticky left-0 z-10 bg-white border-r border-b border-gray-300 pl-3 pt-3 pb-3 pr-1 text-left font-semibold text-gray-700" style="width: 155px; min-width: 155px; max-width: 155px;">';
    html += '<div class="flex flex-col gap-2">';
    html += '<label class="text-xs text-gray-500 font-medium">Date & Time</label>';
    html += '<div class="relative">';
    html += `<input type="date" id="listViewDatePicker" value="${datePickerValue}" onchange="changeListViewDate(this.value)" class="absolute opacity-0 w-0 h-0" style="pointer-events: auto;">`;
    html += `<button type="button" onclick="document.getElementById('listViewDatePicker').showPicker ? document.getElementById('listViewDatePicker').showPicker() : document.getElementById('listViewDatePicker').click()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-900 cursor-pointer hover:bg-gray-50 transition-colors text-left" style="width: 130px;">${selectedDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</button>`;
    html += '</div>';
    html += '</div>';
    html += `<button type="button" onclick="toggleBusyTimeSlots()" class="mt-2 w-full border border-[#003047] rounded px-1.5 py-0.5 flex flex-col items-center leading-tight cursor-pointer transition-colors ${showOnlyBusyTimeSlots ? 'bg-[#003047] text-white border-[#003047]' : 'bg-white text-gray-900 hover:bg-gray-50'}" title="${showOnlyBusyTimeSlots ? 'Show all time slots' : 'Show only time slots with events'}">
        <span class="text-[7px] text-${showOnlyBusyTimeSlots ? 'gray-300' : 'gray-400'} uppercase text-xs">Filter</span>
        <span class="font-bold">${showOnlyBusyTimeSlots ? 'On' : 'Off'}</span>
    </button>`;
    html += '</th>';

    // Add Salon Appointment column header (first column after Date & Time)
    html += `<th valign="top"  class="border-r border-b border-gray-300 p-3 text-center font-semibold text-gray-700 min-w-[150px] bg-[#e6f0f3]">
        <div class="flex flex-col items-center gap-2">
            <div class="w-10 h-10 bg-[#003047] rounded-full flex items-center justify-center border-2 border-gray-200">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <span class="text-xs font-medium text-gray-900">Salon Appointment</span>
        </div>
        <button type="button" onclick="event.stopPropagation(); toggleBusyTechnicians()" class="mt-2 w-full border border-[#003047] rounded px-1.5 py-0.5 flex flex-col items-center leading-tight cursor-pointer transition-colors ${showOnlyBusyTechnicians ? 'bg-[#003047] text-white border-[#003047]' : 'bg-white text-gray-900 hover:bg-gray-50'}" title="${showOnlyBusyTechnicians ? 'Show all technicians' : 'Show only technicians with bookings'}">
            <span class="text-[7px] text-${showOnlyBusyTechnicians ? 'gray-300' : 'gray-400'} uppercase text-xs">Filter</span>
            <span class="font-bold">${showOnlyBusyTechnicians ? 'On' : 'Off'}</span>
        </button>
    </th>`;
    
    // Add technician headers (ordered like Waiting List modal)
    orderedTechnicians.forEach(technician => {
        const initials = technician.initials || (technician.firstName?.[0] || '') + (technician.lastName?.[0] || '');
        const fullName = `${technician.firstName} ${technician.lastName}`;
        const profilePhoto = technician.profilePhotoUrl || technician.photo || null;
        const isOnline = !!(technician.clock_in && !technician.clock_out);
        const onlineBadgeClass = isOnline ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-gray-100 text-gray-700 border border-gray-200';
        const onlineBadgeText = isOnline ? 'Online' : 'Offline';
        
        const serviceCount = typeof technician.services === 'number' ? technician.services : 0;

        html += `<th class="border-r border-b border-gray-300 p-3 text-center font-semibold text-gray-700 min-w-[150px] cursor-pointer hover:bg-gray-50 transition-colors relative" onclick="showTechnicianMessageModal(${technician.id}, '${fullName.replace(/'/g, "\\'")}')">`;
        html += `<div class="flex flex-col items-center gap-1">`;
        html += `<div class="relative">`;
        if (profilePhoto) {
            html += `<img src="${profilePhoto}" alt="${fullName}" class="w-10 h-10 rounded-full object-cover border-2 border-gray-200">`;
        } else {
            html += `<div class="w-10 h-10 bg-[#e6f0f3] rounded-full flex items-center justify-center border-2 border-gray-200">`;
            html += `<span class="text-sm font-bold text-[#003047]">${initials}</span>`;
            html += `</div>`;
        }
        // Online/offline dot badge (like Waiting List modal)
        html += `<div class="absolute w-4 h-4 rounded-full border-2 border-white ${isOnline ? 'bg-green-500' : 'bg-gray-400'}" style="bottom: -4px; right: -4px;" title="${onlineBadgeText}"></div>`;
        html += `</div>`;

        html += `<span class="text-xs font-medium text-gray-900">${fullName}</span>`;
        html += `</div>`;
        html += `<div class="mt-2 border border-gray-300 rounded px-1.5 py-0.5 bg-white flex flex-col items-center leading-tight"><span class="text-[7px] text-gray-400 uppercase text-xs">Service</span><span class="font-bold text-gray-900">${serviceCount}</span></div>`;

        html += `</th>`;
    });
    
    html += '</tr>';
    html += '</thead>';
    html += '<tbody>';
    
    // Filter time slots to only show those with events if toggle is on
    let displayTimeSlots = timeSlots;
    if (showOnlyBusyTimeSlots) {
        displayTimeSlots = timeSlots.filter(timeSlot => {
            const [hours, minutes] = timeSlot.value.split(':').map(Number);
            const slotStart = new Date(selectedDate);
            slotStart.setHours(hours, minutes, 0, 0);
            const slotEnd = new Date(slotStart);
            slotEnd.setHours(hours + 1, 0, 0, 0);

            return filteredBookings.some(apt => {
                let aptDate = null;
                if (apt.appointment_date) {
                    const dateParts = apt.appointment_date.split('-');
                    aptDate = new Date(parseInt(dateParts[0]), parseInt(dateParts[1]) - 1, parseInt(dateParts[2]));
                } else if (apt.appointment_datetime) {
                    aptDate = parseIsoDatetimeLocal(apt.appointment_datetime) || new Date(apt.appointment_datetime);
                } else if (apt.created_at) {
                    aptDate = parseIsoDatetimeLocal(apt.created_at) || new Date(apt.created_at);
                }
                if (!aptDate || isNaN(aptDate.getTime())) return false;
                const aptDateOnly = new Date(aptDate);
                aptDateOnly.setHours(0, 0, 0, 0);
                if (aptDateOnly.getTime() !== selectedDate.getTime()) return false;

                let aptStart = null;
                if (apt.appointment_date && apt.appointment_time) {
                    const dateParts = apt.appointment_date.split('-');
                    aptStart = new Date(parseInt(dateParts[0]), parseInt(dateParts[1]) - 1, parseInt(dateParts[2]));
                    const [h, m] = apt.appointment_time.split(':').map(Number);
                    aptStart.setHours(h, m || 0, 0, 0);
                } else if (apt.appointment_datetime) {
                    aptStart = parseIsoDatetimeLocal(apt.appointment_datetime) || new Date(apt.appointment_datetime);
                } else if (apt.created_at) {
                    aptStart = parseIsoDatetimeLocal(apt.created_at) || new Date(apt.created_at);
                }
                if (!aptStart || isNaN(aptStart.getTime())) return false;
                return (aptStart >= slotStart && aptStart < slotEnd);
            });
        });
    }

    // Time slot rows
    displayTimeSlots.forEach(timeSlot => {
        html += '<tr class="hover:bg-gray-50">';
        html += `<td class="sticky left-0 z-10 bg-white border-r border-b border-gray-300 p-3 font-medium text-gray-700 text-sm" style="width: 155px; min-width: 155px; max-width: 155px;">${timeSlot.display}</td>`;
        
        // Salon Appointment column cell (for appointments without assigned technician)
        const [salonHours, salonMinutes] = timeSlot.value.split(':').map(Number);
        const salonSlotStart = new Date(selectedDate);
        salonSlotStart.setHours(salonHours, salonMinutes, 0, 0);
        const salonSlotEnd = new Date(salonSlotStart);
        salonSlotEnd.setHours(salonHours + 1, 0, 0, 0);
        
        // Find appointments without assigned technician in this time slot
        const salonAppointments = filteredBookings.filter(apt => {
            // Check if appointment has NO assigned technician
            if (apt.assigned_technician && Array.isArray(apt.assigned_technician) && apt.assigned_technician.length > 0) {
                return false;
            }
            
            // Parse appointment date
            let aptDate = null;
            if (apt.appointment_date) {
                const dateParts = apt.appointment_date.split('-');
                aptDate = new Date(parseInt(dateParts[0]), parseInt(dateParts[1]) - 1, parseInt(dateParts[2]));
            } else if (apt.appointment_datetime) {
                aptDate = parseIsoDatetimeLocal(apt.appointment_datetime) || new Date(apt.appointment_datetime);
            } else if (apt.created_at) {
                aptDate = parseIsoDatetimeLocal(apt.created_at) || new Date(apt.created_at);
            }
            if (!aptDate || isNaN(aptDate.getTime())) return false;
            
            const aptDateOnly = new Date(aptDate);
            aptDateOnly.setHours(0, 0, 0, 0);
            
            // Check if appointment is on selected date
            if (aptDateOnly.getTime() !== selectedDate.getTime()) return false;
            
            // Calculate appointment start and end time
            let aptStart = null;
            if (apt.appointment_date && apt.appointment_time) {
                const dateParts = apt.appointment_date.split('-');
                aptStart = new Date(parseInt(dateParts[0]), parseInt(dateParts[1]) - 1, parseInt(dateParts[2]));
                const [hours, minutes] = apt.appointment_time.split(':').map(Number);
                aptStart.setHours(hours, minutes || 0, 0, 0);
            } else if (apt.appointment_datetime) {
                aptStart = parseIsoDatetimeLocal(apt.appointment_datetime) || new Date(apt.appointment_datetime);
            } else if (apt.created_at) {
                aptStart = parseIsoDatetimeLocal(apt.created_at) || new Date(apt.created_at);
            } else if (apt.appointment_date) {
                const dateParts = apt.appointment_date.split('-');
                aptStart = new Date(parseInt(dateParts[0]), parseInt(dateParts[1]) - 1, parseInt(dateParts[2]));
                aptStart.setHours(9, 0, 0, 0);
            }
            
            if (!aptStart || isNaN(aptStart.getTime())) return false;
            
            // Only show appointment in the time slot where it starts
            return (aptStart >= salonSlotStart && aptStart < salonSlotEnd);
        });
        
        html += `<td class="border-r border-b border-gray-300 p-2 align-top relative droppable-slot bg-[#f0f7f9] cursor-pointer" 
            style="min-height: 60px;"
            data-technician-id="salon"
            data-time-slot="${timeSlot.value}"
            ondragover="handleSlotDragOver(event)"
            ondrop="handleSlotDrop(event, 'salon', '${timeSlot.value}')"
            ondragenter="handleSlotDragEnter(event)"
            ondragleave="handleSlotDragLeave(event)"
            onclick="if (!event.target.closest('.draggable-appointment')) { 
                const now = new Date();
                const currentDate = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0');
                // Use current hour with :00 minutes to match time slot format
                const currentTime = String(now.getHours()).padStart(2, '0') + ':00';
                window.location.href='{{ $bookingUrl }}?date=' + currentDate + '&time=' + currentTime;
            }">`;
        
        if (salonAppointments.length > 0) {
            salonAppointments.forEach(apt => {
                const customer = customersData.find(c => c.id.toString() === apt.customer_id.toString());
                const customerName = customer ? `${customer.firstName} ${customer.lastName}` : `Customer #${apt.customer_id}`;
                
                // Parse appointment start time
                let aptStart = null;
                if (apt.appointment_date && apt.appointment_time) {
                    const dateParts = apt.appointment_date.split('-');
                    aptStart = new Date(parseInt(dateParts[0]), parseInt(dateParts[1]) - 1, parseInt(dateParts[2]));
                    const [hours, minutes] = apt.appointment_time.split(':').map(Number);
                    aptStart.setHours(hours, minutes, 0, 0);
                } else if (apt.appointment_datetime) {
                    aptStart = new Date(apt.appointment_datetime);
                } else if (apt.created_at) {
                    aptStart = new Date(apt.created_at);
                }
                if (!aptStart) return;
                
                // Calculate end time
                const serviceCount = apt.services ? apt.services.length : 1;
                const durationHours = Math.max(1, Math.min(3, 1 + (serviceCount - 1) * 0.5));
                const aptEnd = new Date(aptStart);
                aptEnd.setHours(aptEnd.getHours() + Math.floor(durationHours));
                aptEnd.setMinutes(aptEnd.getMinutes() + ((durationHours % 1) * 60));
                
                const startTime = aptStart.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                const endTime = aptEnd.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });

                // Check if appointment is marked as no show
                const isNoShow = noShowStatus[apt.id] === true;

                // Check if appointment is unpaid
                const isUnpaid = apt.status === 'unpaid';

                // Determine event color from the first service color of the first assigned technician
                let salonEventColor = null;
                if (apt.services && Array.isArray(apt.services) && apt.services.length > 0) {
                    for (let si = 0; si < apt.services.length; si++) {
                        let sColor = apt.services[si].service_color;
                        if (!sColor && apt.services[si].service_id && typeof selectServicesData !== 'undefined' && selectServicesData.length > 0) {
                            const svcInfo = selectServicesData.find(d => d.id === apt.services[si].service_id);
                            if (svcInfo && svcInfo.color) sColor = svcInfo.color;
                        }
                        if (sColor) { salonEventColor = sColor; break; }
                    }
                }

                // Apply color coding based on first service color, no show, or custom color
                let colorClass = '';
                let inlineStyle = '';
                if (isNoShow) {
                    colorClass = 'bg-[#9ca3af] text-[#003047] border-[#6b7280] opacity-70';
                } else if (salonEventColor) {
                    colorClass = 'text-white';
                    inlineStyle = 'background-color:' + salonEventColor + ' !important;border-color:' + salonEventColor + ' !important;color:#fff !important;';
                } else if (apt.color) {
                    colorClass = 'text-white';
                    inlineStyle = 'background-color:' + apt.color + ' !important;border-color:' + apt.color + ' !important;color:#fff !important;';
                } else {
                    // White background with blue border for salon appointments
                    colorClass = 'bg-white text-[#003047] border-[#003047]';
                }

                // Build time display with optional clock icon
                const clockIcon = isUnpaid ? '<svg style="width: 12px; height: 12px; color: #008106; display: inline-block; margin-right: 4px;" class="rotating-clock" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>' : '';
                const timeDisplay = `${clockIcon}${startTime}`;

                html += `<div class="mb-1 p-2 rounded border-2 text-xs font-medium ${colorClass} cursor-move hover:opacity-80 draggable-appointment" style="${inlineStyle}"
                    draggable="true"
                    data-appointment-id="${apt.id}"
                    ondragstart="handleAppointmentDragStart(event, ${apt.id})"
                    ondragend="handleAppointmentDragEnd(event)"
                    onclick="if (!event.target.classList.contains('dragging')) { event.stopPropagation(); viewAppointment(${apt.id}); }">`;
                html += `<div class="font-semibold">${customerName}</div>`;
                html += `<div class="text-xs opacity-75" style="display: flex; align-items: center;">${timeDisplay}</div>`;
                html += `</div>`;
            });
        }
        
        html += '</td>';
        
        orderedTechnicians.forEach(technician => {
            const technicianId = technician.id.toString();
            const [hours, minutes] = timeSlot.value.split(':').map(Number);
            const slotStart = new Date(selectedDate);
            slotStart.setHours(hours, minutes, 0, 0);
            const slotEnd = new Date(slotStart);
            slotEnd.setHours(hours + 1, 0, 0, 0);
            
            // Find appointments for this technician in this time slot
            const appointments = filteredBookings.filter(apt => {
                // First check if technician is assigned (quick check)
                if (!apt.assigned_technician || !Array.isArray(apt.assigned_technician)) return false;
                if (!apt.assigned_technician.some(id => id.toString() === technicianId)) return false;
                
                // Parse appointment date - use appointment_date if available, otherwise parse from appointment_datetime or created_at
                let aptDate = null;
                if (apt.appointment_date) {
                    const dateParts = apt.appointment_date.split('-');
                    aptDate = new Date(parseInt(dateParts[0]), parseInt(dateParts[1]) - 1, parseInt(dateParts[2]));
                } else if (apt.appointment_datetime) {
                    aptDate = parseIsoDatetimeLocal(apt.appointment_datetime) || new Date(apt.appointment_datetime);
                } else if (apt.created_at) {
                    aptDate = parseIsoDatetimeLocal(apt.created_at) || new Date(apt.created_at);
                }
                if (!aptDate || isNaN(aptDate.getTime())) return false;
                
                const aptDateOnly = new Date(aptDate);
                aptDateOnly.setHours(0, 0, 0, 0);
                
                // Check if appointment is on selected date
                if (aptDateOnly.getTime() !== selectedDate.getTime()) return false;
                
                // Calculate appointment start and end time
                let aptStart = null;
                if (apt.appointment_date && apt.appointment_time) {
                    // Use appointment_date and appointment_time together
                    const dateParts = apt.appointment_date.split('-');
                    aptStart = new Date(parseInt(dateParts[0]), parseInt(dateParts[1]) - 1, parseInt(dateParts[2]));
                    const [hours, minutes] = apt.appointment_time.split(':').map(Number);
                    aptStart.setHours(hours, minutes || 0, 0, 0);
                } else if (apt.appointment_datetime) {
                    // Use appointment_datetime (includes both date and time)
                    aptStart = parseIsoDatetimeLocal(apt.appointment_datetime) || new Date(apt.appointment_datetime);
                } else if (apt.created_at) {
                    // Fallback to created_at
                    aptStart = parseIsoDatetimeLocal(apt.created_at) || new Date(apt.created_at);
                } else if (apt.appointment_date) {
                    // Only date available, use default time of 9:00 AM
                    const dateParts = apt.appointment_date.split('-');
                    aptStart = new Date(parseInt(dateParts[0]), parseInt(dateParts[1]) - 1, parseInt(dateParts[2]));
                    aptStart.setHours(9, 0, 0, 0);
                }
                
                if (!aptStart || isNaN(aptStart.getTime())) return false;
                
                // Calculate end time based on services
                const serviceCount = apt.services ? apt.services.length : 1;
                const durationHours = Math.max(1, Math.min(3, 1 + (serviceCount - 1) * 0.5));
                const aptEnd = new Date(aptStart);
                aptEnd.setHours(aptEnd.getHours() + Math.floor(durationHours));
                aptEnd.setMinutes(aptEnd.getMinutes() + ((durationHours % 1) * 60));
                
                // Only show appointment in the time slot where it starts
                // Check if appointment starts within this time slot (inclusive of slot start, exclusive of slot end)
                return (aptStart >= slotStart && aptStart < slotEnd);
            });
            
            html += `<td class="border-r border-b border-gray-300 p-2 align-top relative droppable-slot" 
                style="min-height: 60px;"
                data-technician-id="${technician.id}"
                data-time-slot="${timeSlot.value}"
                ondragover="handleSlotDragOver(event)"
                ondrop="handleSlotDrop(event, ${technician.id}, '${timeSlot.value}')"
                ondragenter="handleSlotDragEnter(event)"
                ondragleave="handleSlotDragLeave(event)">`;
            
            if (appointments.length > 0) {
                appointments.forEach(apt => {
                    const customer = customersData.find(c => c.id.toString() === apt.customer_id.toString());
                    const customerName = customer ? `${customer.firstName} ${customer.lastName}` : `Customer #${apt.customer_id}`;
                    
                    // Parse appointment start time
                    let aptStart = null;
                    if (apt.appointment_date && apt.appointment_time) {
                        const dateParts = apt.appointment_date.split('-');
                        aptStart = new Date(parseInt(dateParts[0]), parseInt(dateParts[1]) - 1, parseInt(dateParts[2]));
                        const [hours, minutes] = apt.appointment_time.split(':').map(Number);
                        aptStart.setHours(hours, minutes, 0, 0);
                    } else if (apt.appointment_datetime) {
                        aptStart = parseIsoDatetimeLocal(apt.appointment_datetime) || new Date(apt.appointment_datetime);
                    } else if (apt.created_at) {
                        aptStart = parseIsoDatetimeLocal(apt.created_at) || new Date(apt.created_at);
                    }
                    if (!aptStart) return;
                    
                    // Calculate end time
                    const serviceCount = apt.services ? apt.services.length : 1;
                    const durationHours = Math.max(1, Math.min(3, 1 + (serviceCount - 1) * 0.5));
                    const aptEnd = new Date(aptStart);
                    aptEnd.setHours(aptEnd.getHours() + Math.floor(durationHours));
                    aptEnd.setMinutes(aptEnd.getMinutes() + ((durationHours % 1) * 60));
                    
                    const startTime = aptStart.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
                    const endTime = aptEnd.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });

                    // Check if appointment is marked as no show
                    const isNoShow = noShowStatus[apt.id] === true;

                    // Check if appointment is unpaid
                    const isUnpaid = apt.status === 'unpaid';

                    // Check if technician is assigned
                    const hasTechnician = apt.assigned_technician && Array.isArray(apt.assigned_technician) && apt.assigned_technician.length > 0;

                    // Determine the color for this technician's event card
                    // Use the first service color assigned to this specific technician
                    let techEventColor = null;
                    if (apt.services && Array.isArray(apt.services)) {
                        const techServices = apt.services.filter(s => s.technician_id && s.technician_id.toString() === technicianId);
                        for (let si = 0; si < techServices.length; si++) {
                            let sColor = techServices[si].service_color;
                            if (!sColor && techServices[si].service_id && typeof selectServicesData !== 'undefined' && selectServicesData.length > 0) {
                                const svcInfo = selectServicesData.find(d => d.id === techServices[si].service_id);
                                if (svcInfo && svcInfo.color) sColor = svcInfo.color;
                            }
                            if (sColor) { techEventColor = sColor; break; }
                        }
                    }

                    // Apply color coding based on technician's first service color, no show, and technician assignment
                    let colorClass = '';
                    let inlineStyle = '';
                    if (isNoShow) {
                        // Gray background for no show
                        colorClass = 'bg-[#9ca3af] text-[#003047] border-[#6b7280] opacity-70';
                    } else if (techEventColor) {
                        colorClass = 'text-white';
                        inlineStyle = 'background-color:' + techEventColor + ' !important;border-color:' + techEventColor + ' !important;color:#fff !important;';
                    } else if (hasTechnician) {
                        // Blue background with blue border for assigned technician
                        colorClass = 'bg-[#003047] text-white border-[#003047]';
                    } else {
                        // White background with blue border for no assigned technician
                        colorClass = 'bg-white text-[#003047] border-[#003047]';
                    }

                    // Build time display with optional clock icon
                    const clockIcon = isUnpaid ? '<svg style="width: 12px; height: 12px; color: #008106; display: inline-block; margin-right: 4px;" class="rotating-clock" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>' : '';
                    const timeDisplay = `${clockIcon}${startTime}`;

                    html += `<div class="mb-1 p-2 rounded border-2 text-xs font-medium ${colorClass} cursor-move hover:opacity-80 draggable-appointment" style="${inlineStyle}"
                        draggable="true"
                        data-appointment-id="${apt.id}"
                        ondragstart="handleAppointmentDragStart(event, ${apt.id})"
                        ondragend="handleAppointmentDragEnd(event)"
                        onclick="if (!event.target.classList.contains('dragging')) { event.stopPropagation(); viewAppointment(${apt.id}); }">`;
                    html += `<div class="font-semibold">${customerName}</div>`;
                    html += `<div class="text-xs opacity-75" style="display: flex; align-items: center;">${timeDisplay}</div>`;
                    html += `</div>`;
                });
            }
            
            html += '</td>';
        });
        html += '</tr>';
    });
    
    html += '</tbody>';
    html += '</table>';
    html += '</div>';
    
    container.innerHTML = html;
}

// Change list view date
function changeListViewDate(dateString) {
    selectedListViewDate = parseYmdAsLocalDate(dateString) || parseIsoDatetimeLocal(dateString) || new Date(dateString);
    
    // Update URL with date parameter
    var url = new URL(window.location);
    url.searchParams.set('view', 'list');
    url.searchParams.set('date', dateString);
    window.history.pushState({}, '', url);
    
    renderTechnicianListView();
}

// Toggle showing only technicians with bookings/events
function toggleBusyTechnicians() {
    showOnlyBusyTechnicians = !showOnlyBusyTechnicians;
    localStorage.setItem('showOnlyBusyTechnicians', showOnlyBusyTechnicians);
    renderTechnicianListView();
}

// Toggle showing only time slots with events
function toggleBusyTimeSlots() {
    showOnlyBusyTimeSlots = !showOnlyBusyTimeSlots;
    localStorage.setItem('showOnlyBusyTimeSlots', showOnlyBusyTimeSlots);
    renderTechnicianListView();
}

// Show technician message modal
function showTechnicianMessageModal(technicianId, technicianName) {
    // Get selected date
    const selectedDate = new Date(selectedListViewDate);
    selectedDate.setHours(0, 0, 0, 0);

    // Apply appointment type filter
    const filteredBookings = getFilteredAppointments(bookingsData);

    // Find all appointments for this technician on the selected date
    const technicianAppointments = filteredBookings.filter(apt => {
        // Check if technician is assigned
        if (!apt.assigned_technician || !Array.isArray(apt.assigned_technician)) return false;
        if (!apt.assigned_technician.some(id => id.toString() === technicianId.toString())) return false;
        
        // Parse appointment date
        let aptDate = null;
        if (apt.appointment_date) {
            const dateParts = apt.appointment_date.split('-');
            aptDate = new Date(parseInt(dateParts[0]), parseInt(dateParts[1]) - 1, parseInt(dateParts[2]));
        } else if (apt.appointment_datetime) {
            aptDate = new Date(apt.appointment_datetime);
        } else if (apt.created_at) {
            aptDate = new Date(apt.created_at);
        }
        if (!aptDate || isNaN(aptDate.getTime())) return false;
        
        const aptDateOnly = new Date(aptDate);
        aptDateOnly.setHours(0, 0, 0, 0);
        
        // Check if appointment is on selected date
        return aptDateOnly.getTime() === selectedDate.getTime();
    });
    
    // Get unique customers with their appointment details
    const customerAppointments = [];
    const customerMap = new Map();
    
    technicianAppointments.forEach(apt => {
        const customer = customersData.find(c => c.id.toString() === apt.customer_id.toString());
        if (!customer) return;
        
        // Parse appointment time
        let aptStart = null;
        let aptTime = '';
        if (apt.appointment_date && apt.appointment_time) {
            aptTime = apt.appointment_time;
        } else if (apt.appointment_datetime) {
            aptStart = new Date(apt.appointment_datetime);
            aptTime = aptStart.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        }
        
        const customerId = customer.id.toString();
        if (!customerMap.has(customerId)) {
            customerMap.set(customerId, {
                customer: customer,
                appointments: []
            });
        }
        customerMap.get(customerId).appointments.push({
            time: aptTime,
            id: apt.id
        });
    });
    
    // Convert map to array
    customerMap.forEach((value, key) => {
        customerAppointments.push(value);
    });
    
    // Format date for display
    const dateDisplay = selectedDate.toLocaleDateString('en-US', { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
    
    // Professional Email message template
    const defaultEmailMessage = `Dear [Customer Name],

We hope this message finds you well. We wanted to inform you that you have an appointment scheduled with ${technicianName} on ${dateDisplay} at [Appointment Time].

Unfortunately, ${technicianName} has encountered an unexpected emergency and will be unable to attend your appointment today. We sincerely apologize for any inconvenience this may cause.

We would be happy to assist you with the following options:
1. Reschedule your appointment for a more convenient time
2. Arrange for another qualified technician to provide your service today
3. Visit us at your scheduled time - we will ensure another staff member is available to assist you

Please let us know which option works best for you, and we will make the necessary arrangements. We appreciate your understanding and look forward to serving you.

Best regards,
Salon Management Team`;

    // Professional SMS message template (shorter, concise)
    const defaultSMSMessage = `Hi [Customer Name], 

Your appointment with ${technicianName} on ${dateDisplay} at [Appointment Time] needs to be rescheduled due to an emergency. 

Options:
1. Reschedule for another time
2. Another technician can assist you today
3. Visit at your scheduled time - another staff member will be available

Please reply with your preference. Thank you for your understanding.

- Salon Management`;

    // Build modal content
    let modalContent = `
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold text-gray-900">Send Message to Customers</h2>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="mb-6">
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <div class="flex items-start gap-3">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-blue-900 mb-1">Technician: ${technicianName}</h3>
                            <p class="text-sm text-blue-700">Date: ${dateDisplay}</p>
                            <p class="text-sm text-blue-700 mt-1">Total Customers: ${customerAppointments.length}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Message Templates</label>
                
                <!-- Tabs for Email and SMS -->
                <div class="flex border-b border-gray-200 mb-4">
                    <button onclick="switchMessageTab('email')" id="emailTabBtn" class="px-4 py-2 text-sm font-medium text-[#003047] border-b-2 border-[#003047] transition-colors">
                        <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Email
                    </button>
                    <button onclick="switchMessageTab('sms')" id="smsTabBtn" class="px-4 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors">
                        <svg class="w-4 h-4 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        SMS
                    </button>
                </div>
                
                <!-- Email Template -->
                <div id="emailTemplateSection" class="message-template-section">
                    <label class="block text-xs font-medium text-gray-600 mb-2">Email Template</label>
                    <textarea id="emailTemplate" rows="12" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent resize-none" placeholder="Enter your email message...">${defaultEmailMessage}</textarea>
                    <p class="text-xs text-gray-500 mt-2">Use [Customer Name] and [Appointment Time] as placeholders that will be replaced with actual values.</p>
                </div>
                
                <!-- SMS Template -->
                <div id="smsTemplateSection" class="message-template-section hidden">
                    <label class="block text-xs font-medium text-gray-600 mb-2">SMS Template</label>
                    <textarea id="smsTemplate" rows="8" oninput="updateSMSCharCount()" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent resize-none" placeholder="Enter your SMS message...">${defaultSMSMessage}</textarea>
                    <div class="flex items-center justify-between mt-2">
                        <p class="text-xs text-gray-500">Use [Customer Name] and [Appointment Time] as placeholders.</p>
                        <p id="smsCharCount" class="text-xs text-gray-400">${defaultSMSMessage.length} / 160 characters</p>
                    </div>
                </div>
            </div>
            
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">Recipients (${customerAppointments.length} customers)</label>
                <div class="max-h-60 overflow-y-auto border border-gray-200 rounded-lg p-3 bg-gray-50">
    `;
    
    // Add customer list
    customerAppointments.forEach((item, index) => {
        const customer = item.customer;
        const customerName = `${customer.firstName} ${customer.lastName}`;
        const appointmentsList = item.appointments.map(apt => apt.time).join(', ');
        
        modalContent += `
            <div class="flex items-center justify-between py-2 ${index < customerAppointments.length - 1 ? 'border-b border-gray-200' : ''}">
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">${customerName}</p>
                    <p class="text-xs text-gray-500">Appointment${item.appointments.length > 1 ? 's' : ''}: ${appointmentsList}</p>
                </div>
                <div class="flex items-center gap-3">
                    ${customer.email ? `<span class="inline-flex items-center gap-1 text-xs text-gray-600"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>${customer.email}</span>` : '<span class="text-xs text-gray-400">No email</span>'}
                    ${customer.phone ? `<span class="inline-flex items-center gap-1 text-xs text-gray-600"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>${customer.phone}</span>` : '<span class="text-xs text-gray-400">No phone</span>'}
                </div>
            </div>
        `;
    });
    
    modalContent += `
                </div>
            </div>
            
            <div class="flex items-center justify-end gap-3">
                <button onclick="closeModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button onclick="sendTechnicianMessages(${technicianId}, '${technicianName.replace(/'/g, "\\'")}')" class="px-4 py-2 text-sm font-medium text-white bg-[#003047] rounded-lg hover:bg-[#004060] transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    Send Messages
                </button>
            </div>
        </div>
    `;
    
    if (typeof openModal === 'function') {
        openModal(modalContent, 'large');
        
        // Initialize SMS character count after modal is opened
        setTimeout(() => {
            const smsTemplate = document.getElementById('smsTemplate');
            if (smsTemplate) {
                updateSMSCharCount();
            }
        }, 100);
    }
}

// Switch between Email and SMS tabs
function switchMessageTab(type) {
    const emailTabBtn = document.getElementById('emailTabBtn');
    const smsTabBtn = document.getElementById('smsTabBtn');
    const emailSection = document.getElementById('emailTemplateSection');
    const smsSection = document.getElementById('smsTemplateSection');
    const smsTemplate = document.getElementById('smsTemplate');
    const smsCharCount = document.getElementById('smsCharCount');
    
    if (type === 'email') {
        emailTabBtn.classList.add('text-[#003047]', 'border-b-2', 'border-[#003047]');
        emailTabBtn.classList.remove('text-gray-500');
        smsTabBtn.classList.remove('text-[#003047]', 'border-b-2', 'border-[#003047]');
        smsTabBtn.classList.add('text-gray-500');
        emailSection.classList.remove('hidden');
        smsSection.classList.add('hidden');
    } else {
        smsTabBtn.classList.add('text-[#003047]', 'border-b-2', 'border-[#003047]');
        smsTabBtn.classList.remove('text-gray-500');
        emailTabBtn.classList.remove('text-[#003047]', 'border-b-2', 'border-[#003047]');
        emailTabBtn.classList.add('text-gray-500');
        emailSection.classList.add('hidden');
        smsSection.classList.remove('hidden');
        
        // Update character count
        if (smsTemplate && smsCharCount) {
            updateSMSCharCount();
        }
    }
}

// Update SMS character count
function updateSMSCharCount() {
    const smsTemplate = document.getElementById('smsTemplate');
    const smsCharCount = document.getElementById('smsCharCount');
    if (smsTemplate && smsCharCount) {
        const length = smsTemplate.value.length;
        smsCharCount.textContent = `${length} / 160 characters`;
        if (length > 160) {
            smsCharCount.classList.add('text-red-500');
            smsCharCount.classList.remove('text-gray-400');
        } else {
            smsCharCount.classList.remove('text-red-500');
            smsCharCount.classList.add('text-gray-400');
        }
    }
}

// Send messages to all customers
function sendTechnicianMessages(technicianId, technicianName) {
    const emailTemplate = document.getElementById('emailTemplate')?.value || '';
    const smsTemplate = document.getElementById('smsTemplate')?.value || '';
    
    if (!emailTemplate.trim() && !smsTemplate.trim()) {
        showToastMessage('Please enter at least one message template', 'error');
        return;
    }
    
    // Get selected date
    const selectedDate = new Date(selectedListViewDate);
    selectedDate.setHours(0, 0, 0, 0);

    // Apply appointment type filter
    const filteredBookings = getFilteredAppointments(bookingsData);

    // Find all appointments for this technician on the selected date
    const technicianAppointments = filteredBookings.filter(apt => {
        if (!apt.assigned_technician || !Array.isArray(apt.assigned_technician)) return false;
        if (!apt.assigned_technician.some(id => id.toString() === technicianId.toString())) return false;
        
        let aptDate = null;
        if (apt.appointment_date) {
            const dateParts = apt.appointment_date.split('-');
            aptDate = new Date(parseInt(dateParts[0]), parseInt(dateParts[1]) - 1, parseInt(dateParts[2]));
        } else if (apt.appointment_datetime) {
            aptDate = new Date(apt.appointment_datetime);
        } else if (apt.created_at) {
            aptDate = new Date(apt.created_at);
        }
        if (!aptDate || isNaN(aptDate.getTime())) return false;
        
        const aptDateOnly = new Date(aptDate);
        aptDateOnly.setHours(0, 0, 0, 0);
        
        return aptDateOnly.getTime() === selectedDate.getTime();
    });
    
    // Get unique customers
    const customerMap = new Map();
    technicianAppointments.forEach(apt => {
        const customer = customersData.find(c => c.id.toString() === apt.customer_id.toString());
        if (!customer) return;
        
        let aptTime = '';
        if (apt.appointment_date && apt.appointment_time) {
            aptTime = apt.appointment_time;
        } else if (apt.appointment_datetime) {
            const aptStart = new Date(apt.appointment_datetime);
            aptTime = aptStart.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        }
        
        const customerId = customer.id.toString();
        if (!customerMap.has(customerId)) {
            customerMap.set(customerId, {
                customer: customer,
                time: aptTime
            });
        }
    });
    
    // Send messages (simulate - in production, this would call an API)
    let emailSentCount = 0;
    let smsSentCount = 0;
    
    customerMap.forEach((item) => {
        const customer = item.customer;
        const customerName = `${customer.firstName} ${customer.lastName}`;
        const appointmentTime = item.time;
        
        // Send Email if template exists and customer has email
        if (emailTemplate.trim() && customer.email) {
            let personalizedEmail = emailTemplate
                .replace(/\[Customer Name\]/g, customerName)
                .replace(/\[Appointment Time\]/g, appointmentTime);
            
            // In production, send via email API here
            console.log('Sending email to:', {
                customer: customerName,
                email: customer.email,
                message: personalizedEmail
            });
            emailSentCount++;
        }
        
        // Send SMS if template exists and customer has phone
        if (smsTemplate.trim() && customer.phone) {
            let personalizedSMS = smsTemplate
                .replace(/\[Customer Name\]/g, customerName)
                .replace(/\[Appointment Time\]/g, appointmentTime);
            
            // In production, send via SMS API here
            console.log('Sending SMS to:', {
                customer: customerName,
                phone: customer.phone,
                message: personalizedSMS
            });
            smsSentCount++;
        }
    });
    
    closeModal();
    
    // Show success message with counts
    let successMessage = '';
    if (emailSentCount > 0 && smsSentCount > 0) {
        successMessage = `${emailSentCount} email${emailSentCount !== 1 ? 's' : ''} and ${smsSentCount} SMS${smsSentCount !== 1 ? 'es' : ''} sent successfully`;
    } else if (emailSentCount > 0) {
        successMessage = `${emailSentCount} email${emailSentCount !== 1 ? 's' : ''} sent successfully`;
    } else if (smsSentCount > 0) {
        successMessage = `${smsSentCount} SMS${smsSentCount !== 1 ? 'es' : ''} sent successfully`;
    } else {
        successMessage = 'No messages sent. Customers may be missing email or phone numbers.';
    }
    
    showToastMessage(successMessage, emailSentCount > 0 || smsSentCount > 0 ? 'success' : 'error');
}

// Drag and Drop handlers for list view
let draggedAppointmentId = null;
let draggedAppointmentElement = null;
let draggedFromTechnicianId = null;

function handleAppointmentDragStart(event, appointmentId) {
    draggedAppointmentId = appointmentId;
    draggedAppointmentElement = event.target;
    
    // Find the source technician ID from the parent td element
    const parentCell = event.target.closest('td[data-technician-id]');
    if (parentCell) {
        draggedFromTechnicianId = parentCell.getAttribute('data-technician-id');
    } else {
        draggedFromTechnicianId = null;
    }
    
    event.dataTransfer.effectAllowed = 'move';
    event.dataTransfer.setData('text/plain', appointmentId.toString());
    
    // Add visual feedback
    event.target.style.opacity = '0.5';
    event.target.classList.add('dragging');
    
    // Add dragging class to body
    document.body.classList.add('dragging-appointment');
}

function handleAppointmentDragEnd(event) {
    // Remove visual feedback
    if (event.target) {
        event.target.style.opacity = '1';
        event.target.classList.remove('dragging');
    }
    
    // Remove dragging class from body
    document.body.classList.remove('dragging-appointment');
    
    // Remove drop zone highlights
    document.querySelectorAll('.droppable-slot').forEach(slot => {
        slot.classList.remove('drag-over');
    });
    
    draggedAppointmentId = null;
    draggedAppointmentElement = null;
    draggedFromTechnicianId = null;
}

function handleSlotDragOver(event) {
    event.preventDefault();
    event.dataTransfer.dropEffect = 'move';
    return false;
}

function handleSlotDragEnter(event) {
    event.preventDefault();
    event.currentTarget.classList.add('drag-over');
}

function handleSlotDragLeave(event) {
    // Only remove highlight if we're actually leaving the element (not just a child)
    if (!event.currentTarget.contains(event.relatedTarget)) {
        event.currentTarget.classList.remove('drag-over');
    }
}

async function handleSlotDrop(event, technicianId, timeSlot) {
    event.preventDefault();
    event.stopPropagation();
    
    // Remove visual feedback
    event.currentTarget.classList.remove('drag-over');
    document.body.classList.remove('dragging-appointment');
    
    if (!draggedAppointmentId) {
        // Try to get from dataTransfer as fallback
        const appointmentId = event.dataTransfer.getData('text/plain');
        if (!appointmentId) return;
        draggedAppointmentId = parseInt(appointmentId);
    }
    
    // Find the appointment
    const appointment = bookingsData.find(apt => apt.id.toString() === draggedAppointmentId.toString());
    if (!appointment) {
        showToastMessage('Appointment not found', 'error');
        return;
    }
    
    // Parse the time slot (format: "HH:00")
    const [hours, minutes] = timeSlot.split(':').map(Number);
    
    // Create new appointment datetime
    const newDate = new Date(selectedListViewDate);
    newDate.setHours(hours, minutes || 0, 0, 0);
    
    // Format as ISO string for appointment_datetime
    const year = newDate.getFullYear();
    const month = String(newDate.getMonth() + 1).padStart(2, '0');
    const day = String(newDate.getDate()).padStart(2, '0');
    const hoursStr = String(hours).padStart(2, '0');
    const minutesStr = String(minutes || 0).padStart(2, '0');
    const newDateTime = `${year}-${month}-${day}T${hoursStr}:${minutesStr}:00`;
    
    // Update appointment
    appointment.appointment_datetime = newDateTime;
    appointment.appointment_date = `${year}-${month}-${day}`;
    appointment.appointment_time = `${hoursStr}:${minutesStr}`;
    
    // Update assigned technician
    if (!appointment.assigned_technician || !Array.isArray(appointment.assigned_technician)) {
        appointment.assigned_technician = [];
    }
    
    const technicianIdStr = technicianId.toString();
    
    // Build backend payload (appointment_datetime + assigned_technician)
    const apiUrl = window.salonCalendarAppointmentsApiUrl;
    const payload = { appointment_datetime: newDateTime };

    // Handle Salon Appointment column (clear all technicians)
    if (technicianIdStr === 'salon') {
        // Clear all assigned technicians
        appointment.assigned_technician = [];
        payload.assigned_technician = [];
    } else {
        // Handle regular technician assignment
        const destinationTechnicianId = parseInt(technicianId);
        
        // Only remove the source technician (where drag started), keep all other technicians
        if (draggedFromTechnicianId) {
            const sourceTechnicianId = parseInt(draggedFromTechnicianId);
            // Remove source technician if it exists
            appointment.assigned_technician = appointment.assigned_technician.filter(
                techId => parseInt(techId) !== sourceTechnicianId
            );
        }
        
        // Add destination technician if not already present
        const destinationIdExists = appointment.assigned_technician.some(
            techId => parseInt(techId) === destinationTechnicianId
        );
        
        if (!destinationIdExists) {
            appointment.assigned_technician.push(destinationTechnicianId);
        }
        
        // Ensure array contains unique values (in case of duplicates)
        appointment.assigned_technician = [...new Set(appointment.assigned_technician.map(id => parseInt(id)))];
        
        payload.assigned_technician = appointment.assigned_technician.map(function(id) { return parseInt(id, 10); }).filter(function(n) { return !isNaN(n); });
    }

    // Persist to backend (fallback to optimistic-only if API unavailable)
    let saved = null;
    if (apiUrl && typeof salonApi !== 'undefined' && salonApi.put) {
        try {
            const res = await salonApi.put(apiUrl + '/' + appointment.id, payload);
            if (res && res.data) {
                // Apply canonical values returned by API
                appointment.appointment_datetime = res.data.appointment_datetime || appointment.appointment_datetime;
                appointment.status = res.data.status || appointment.status;
                if (Array.isArray(res.data.assigned_technician)) {
                    appointment.assigned_technician = res.data.assigned_technician;
                }
                if (Array.isArray(res.data.services)) {
                    appointment.services = res.data.services;
                }
                saved = res.data;
            }
        } catch (err) {
            // If save fails, refetch + redraw to avoid UI desync
            console.error(err);
            showToastMessage((err && err.message) ? err.message : 'Failed to update appointment.', 'error');
        }
    }

    // Show success message (based on destination)
    const timeDisplay = newDate.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
    if (technicianIdStr === 'salon') {
        showToastMessage(`Appointment moved to Salon Appointment at ${timeDisplay}`, saved ? 'success' : 'info');
    } else {
        const technician = techniciansData.find(t => t.id.toString() === technicianIdStr);
        const technicianName = technician ? `${technician.firstName} ${technician.lastName}` : `Technician #${technicianId}`;
        showToastMessage(`Appointment moved to ${technicianName} at ${timeDisplay}`, saved ? 'success' : 'info');
    }
    
    // Re-render the list view
    renderTechnicianListView();
    
    // Also update the calendar grid view if it exists
    if (calendarInstance) {
        // Find the event in the calendar
        const event = calendarInstance.getEventById(appointment.id.toString());
        
        if (event) {
            // Update the existing event
            event.setStart(newDateTime);
            
            // Update end time based on duration
            const serviceCount = appointment.services ? appointment.services.length : 1;
            const durationHours = Math.max(1, Math.min(3, 1 + (serviceCount - 1) * 0.5));
            const newEndDate = new Date(newDate);
            newEndDate.setHours(newEndDate.getHours() + Math.floor(durationHours));
            newEndDate.setMinutes(newEndDate.getMinutes() + ((durationHours % 1) * 60));
            event.setEnd(newEndDate.toISOString());
            
            // Update extended props
            event.setExtendedProp('hasTechnician', appointment.assigned_technician && appointment.assigned_technician.length > 0);
            
            // Update styling — no-show: no inline styles, CSS handles it
            const hasTechnician = appointment.assigned_technician && appointment.assigned_technician.length > 0;
            const isNoShowNow = noShowStatus[appointment.id] === true;
            const customColor = appointment.color || null;
            if (isNoShowNow) {
                event.setProp('backgroundColor', '');
                event.setProp('borderColor', '');
                event.setProp('textColor', '');
                setTimeout(function() {
                    var targetEls = document.querySelectorAll('[data-event-id="' + appointment.id + '"]');
                    targetEls.forEach(function(el) {
                        el.removeAttribute('style');
                        var mainEl = el.querySelector('.fc-event-main');
                        if (mainEl) mainEl.removeAttribute('style');
                    });
                }, 50);
            } else if (customColor) {
                event.setProp('backgroundColor', customColor);
                event.setProp('borderColor', customColor);
                event.setProp('textColor', '#ffffff');
            } else if (hasTechnician) {
                event.setProp('backgroundColor', '#003047');
                event.setProp('borderColor', '#003047');
                event.setProp('textColor', '#ffffff');
            } else {
                event.setProp('backgroundColor', 'transparent');
                event.setProp('borderColor', '#003047');
                event.setProp('textColor', '#003047');
            }
            
            // Update technician name in extended props
            let technicianName = 'Not Assigned';
            if (technicianIdStr === 'salon') {
                technicianName = 'Salon Appointment';
            } else {
                const technician = techniciansData.find(t => t.id.toString() === technicianIdStr);
                technicianName = technician ? `${technician.firstName} ${technician.lastName}` : `Technician #${technicianId}`;
            }
            event.setExtendedProp('technician', technicianName);
        } else {
            // Event doesn't exist yet, refetch all events
            calendarInstance.removeAllEvents();
            const events = convertAppointmentsToEvents(bookingsData);
            calendarInstance.addEventSource(events);
        }
        
        // Refresh the calendar view
        calendarInstance.render();
    }
    
    draggedAppointmentId = null;
    draggedAppointmentElement = null;
}

// Shared function to show appointment modal (used by both calendar and list views)
function showAppointmentModal(appointmentData) {
    const appointmentId = appointmentData.id;
    const customerName = appointmentData.customer;
    const technicians = appointmentData.technician || 'Not Assigned';
    const customerPhone = appointmentData.phone;
    const customerEmail = appointmentData.email || '';
    const hasRealPhone = customerPhone && customerPhone !== 'No phone';
    const status = appointmentData.status;
    const appointmentDate = appointmentData.date;
    const isNoShow = appointmentData.isNoShow || noShowStatus[appointmentId] || false;
    const bookingType = appointmentData.bookingType || 'Booked';
    const eventColor = appointmentData.color || null;
    const typeBadgeClass = bookingType === 'Walk-In' ? 'bg-blue-100 text-blue-700 border-blue-200' : 'bg-purple-100 text-purple-700 border-purple-200';
    const statusLower = (status || '').toLowerCase();
    const statusBadgeClass = statusLower === 'confirmed' ? 'bg-green-100 text-green-700 border-green-200'
        : statusLower === 'paid' ? 'bg-emerald-100 text-emerald-700 border-emerald-200'
        : statusLower === 'completed' ? 'bg-teal-100 text-teal-700 border-teal-200'
        : statusLower === 'refunded' ? 'bg-red-100 text-red-700 border-red-200'
        : 'bg-yellow-100 text-yellow-700 border-yellow-200';
    const statusLabel = status ? status.charAt(0).toUpperCase() + status.slice(1) : 'Waiting';

    // Find event if it exists (for calendar view)
    if (calendarInstance && !currentEvent) {
        const events = calendarInstance.getEvents();
        currentEvent = events.find(e => e.id && e.id.toString() === appointmentId.toString());
    }
    
    // Create modal content
    const modalContent = `
        <div class="flex flex-col" style="max-height:85vh">
            <div class="flex-shrink-0 px-6 py-4 border-b border-gray-200 bg-white rounded-t-2xl">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-1">${customerName}</h3>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1.5 rounded-lg text-xs font-semibold border bg-gray-100 text-gray-700 border-gray-200">
                            #${appointmentId}
                        </span>
                        <span class="px-3 py-1.5 rounded-lg text-xs font-semibold border ${typeBadgeClass}">
                            ${bookingType}
                        </span>
                        <span class="px-3 py-1.5 rounded-lg text-xs font-semibold border ${statusBadgeClass}">
                            ${statusLabel}
                        </span>
                        <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600 transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto px-6 py-4">
            <div class="space-y-4">
                <div class="p-4 bg-gray-50 rounded-xl">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs text-gray-500">Technicians</p>
                        ${!isTechnician ? `
                        <button onclick="openTechnicianSelectionModalWithEvent('${appointmentId}', '${customerName}')" class="px-3 py-1.5 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition-all font-medium text-xs flex items-center gap-1">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Assign
                        </button>
                        ` : ''}
                    </div>
                    <div id="technicianDisplay_${appointmentId}">
                    ${(function() {
                        const appointment = bookingsData.find(a => a.id.toString() === appointmentId.toString());
                        const assignedIds = appointment && Array.isArray(appointment.assigned_technician) ? appointment.assigned_technician : [];
                        if (assignedIds.length === 0) return '<p class="text-sm text-gray-400">Not Assigned</p>';
                        const allSvcs = appointment && Array.isArray(appointment.services) ? appointment.services : [];
                        return '<div class="space-y-2">' + assignedIds.map(techId => {
                            const tech = techniciansData.find(t => t.id.toString() === techId.toString());
                            if (!tech) return '';
                            const name = tech.firstName + ' ' + tech.lastName;
                            const initials = tech.initials || (tech.firstName || '')[0] + (tech.lastName || '')[0];
                            const photo = tech.profilePhotoUrl || tech.photo || null;
                            const serviceCount = typeof tech.services === 'number' ? tech.services : 0;
                            const techSvcs = allSvcs.filter(s => s.technician_id && s.technician_id.toString() === techId.toString());
                            const techIsOnline = !!(tech.clock_in && !tech.clock_out);
                            const techBadgeColor = techIsOnline ? 'bg-green-500' : 'bg-gray-400';
                            const techStatusBadge = '<span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full border-2 border-white ' + techBadgeColor + '"></span>';
                            const avatarInner = photo
                                ? '<div class="w-9 h-9 rounded-full overflow-hidden border border-gray-200" style="min-width:36px;min-height:36px;max-width:36px;max-height:36px"><img src="' + photo + '" alt="' + name + '" style="width:36px;height:36px;object-fit:cover"></div>'
                                : '<div class="w-9 h-9 bg-[#e6f0f3] rounded-full flex items-center justify-center border border-gray-200" style="min-width:36px;min-height:36px;max-width:36px;max-height:36px"><span class="text-xs font-bold text-[#003047]">' + initials + '</span></div>';
                            const avatarHtml = '<div class="relative">' + avatarInner + techStatusBadge + '</div>';
                            let svcListHtml = '';
                            if (techSvcs.length > 0) {
                                const svcItems = techSvcs.map(function(s) {
                                    const sName = s.service_name || s.service || 'Service';
                                    const qty = s.quantity || 1;
                                    var sColor = s.service_color || '';
                                    if (!sColor && s.service_id && typeof selectServicesData !== 'undefined' && selectServicesData.length > 0) {
                                        var svcInfo = selectServicesData.find(function(d) { return d.id === s.service_id; });
                                        if (svcInfo && svcInfo.color) sColor = svcInfo.color;
                                    }
                                    const colorDot = sColor ? '<span class="inline-block w-2 h-2 rounded-full flex-shrink-0" style="background:' + sColor + '"></span>' : '';
                                    return '<span class="inline-flex items-center gap-1 text-xs text-gray-500 truncate">' + colorDot + '<span class="truncate">' + sName + (qty > 1 ? ' x' + qty : '') + '</span></span>';
                                });
                                svcListHtml = '<div class="mt-1 grid grid-cols-4 gap-1">' + svcItems.join('') + '</div>';
                            }
                            return '<div class="p-2 bg-white rounded-lg border border-gray-200">'
                                + '<div class="flex items-center gap-3">'
                                + '<div class="flex-shrink-0">' + avatarHtml + '</div>'
                                + '<div class="flex-1 min-w-0">'
                                + '<p class="text-sm font-medium text-gray-900 truncate">' + name + '</p>'
                                + '<p class="text-xs text-gray-500">Services: ' + serviceCount + '</p>'
                                + '</div>'
                                + '<button onclick="event.stopPropagation(); openSelectServicesModal(' + appointmentId + ', ' + techId + ', \'' + name.replace(/'/g, "\\'") + '\')" class="px-3 py-1.5 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition-all font-medium text-xs flex items-center gap-1 flex-shrink-0">'
                                + '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>'
                                + 'Select</button>'
                                + '</div>'
                                + svcListHtml
                                + '</div>';
                        }).join('') + '</div>';
                    })()}
                    </div>
                </div>

                ${!isTechnician ? `
                <div class="p-4 bg-gray-50 rounded-xl">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs text-gray-500">Service Calendar</p>
                        <div id="calendarSyncStatus_${appointmentId}">
                            ${(function() {
                                const apt = bookingsData.find(a => a.id.toString() === appointmentId.toString());
                                const syncedCalId = apt && apt.ghl_calendar_id ? apt.ghl_calendar_id : '';
                                const selectedCalId = syncedCalId || ghlDefaultCalendarId;
                                if (apt && apt.ghl_appointment_id && syncedCalId === selectedCalId) {
                                    return '<span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold border bg-green-100 text-green-700 border-green-200"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Synced</span>';
                                }
                                if (!apt || !apt.ghl_appointment_id) {
                                    return '<span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-semibold border bg-gray-100 text-gray-500 border-gray-200"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01"></path></svg> Not synced</span>';
                                }
                                return '';
                            })()}
                        </div>
                    </div>
                    <div class="calendar-select2">
                        <select id="calendarSelect_${appointmentId}" style="width:100%">
                            ${(function() {
                                const apt = bookingsData.find(a => a.id.toString() === appointmentId.toString());
                                const currentCalId = (apt && apt.ghl_calendar_id) || ghlDefaultCalendarId;
                                if (ghlCalendars.length === 0) {
                                    if (currentCalId) {
                                        return '<option value="' + currentCalId + '">Calendar ID: ' + currentCalId.substring(0, 12) + '...</option>';
                                    }
                                    return '<option value="">No calendars available</option>';
                                }
                                return ghlCalendars.map(function(cal) {
                                    const selected = cal.id === currentCalId ? ' selected' : '';
                                    return '<option value="' + cal.id + '"' + selected + '>' + cal.name + '</option>';
                                }).join('');
                            })()}
                        </select>
                    </div>
                </div>
                ` : ''}

                <div class="grid grid-cols-2 gap-4">
                    <div class="p-4 bg-gray-50 rounded-xl">
                        <p class="text-xs text-gray-500 mb-1">Date</p>
                        <p class="font-semibold text-gray-900">${appointmentDate.toLocaleDateString('en-US', { weekday: 'short', year: 'numeric', month: 'short', day: 'numeric' })}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-xl">
                        <p class="text-xs text-gray-500 mb-1">Time</p>
                        <p class="font-semibold text-gray-900">${appointmentDate.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}</p>
                    </div>
                </div>
                
                ${customerPhone || customerEmail ? `
                <div class="grid grid-cols-2 gap-4">
                    ${customerPhone ? `
                    <div class="p-4 bg-gray-50 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs text-gray-500 mb-0.5">Phone</p>
                                <p class="font-semibold text-gray-900 truncate">${customerPhone}</p>
                            </div>
                        </div>
                    </div>
                    ` : ''}
                    ${customerEmail ? `
                    <div class="p-4 bg-gray-50 rounded-xl">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs text-gray-500 mb-0.5">Email</p>
                                <p class="font-semibold text-gray-900 truncate">${customerEmail}</p>
                            </div>
                        </div>
                    </div>
                    ` : ''}
                </div>
                ${!isTechnician ? `
                <div id="eventColorSection_${appointmentId}" class="p-4 bg-gray-50 rounded-xl hidden" style="min-width:340px">
                    <div class="flex items-center gap-4">
                        <div id="eventColorPreview_${appointmentId}" class="flex-shrink-0" style="width:48px;height:48px;position:relative">
                            ${eventColor
                                ? '<div style="width:48px;height:48px;border-radius:9999px;border:2px solid #fff;box-shadow:0 4px 6px -1px rgba(0,0,0,.1);background:' + eventColor + '"></div><button onclick="setEventColor(\'' + appointmentId + '\', null)" style="position:absolute;top:-4px;left:-4px;width:18px;height:18px;border-radius:9999px;background:#fff;border:1px solid #d1d5db;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 1px 2px rgba(0,0,0,.1)" title="Remove color"><svg style="width:10px;height:10px" fill="none" stroke="#9ca3af" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg></button>'
                                : '<div style="width:48px;height:48px;border-radius:9999px;border:2px dashed #d1d5db;display:flex;align-items:center;justify-content:center;background:#fff"><svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg></div>'
                            }
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-500 mb-2">Event Color</p>
                            <div id="eventColorSwatches_${appointmentId}" class="flex items-center gap-2 flex-wrap">
                                ${(function() {
                                    const apt = bookingsData.find(a => a.id.toString() === appointmentId.toString());
                                    const seenColors = {};

                                    // Active calendar color swatch (1st position - from currently selected Service Calendar)
                                    var activeCalId = (apt && apt.ghl_calendar_id) || ghlDefaultCalendarId;
                                    var activeCalColor = getCalendarColor(activeCalId);
                                    var activeCalName = (clickaioCalendarsConfig && clickaioCalendarsConfig[activeCalId] && clickaioCalendarsConfig[activeCalId].name) || 'Calendar';
                                    var activeCalSwatchHtml = '';
                                    if (activeCalColor) {
                                        var hex = activeCalColor.toUpperCase();
                                        seenColors[hex] = true;
                                        var isSelected = eventColor && eventColor.toUpperCase() === hex;
                                        activeCalSwatchHtml = '<button data-calendar-swatch="true" onclick="setEventColor(\'' + appointmentId + '\', \'' + hex + '\')" class="rounded-full border-2 transition-all hover:scale-110 ' + (isSelected ? 'border-gray-900 ring-2 ring-offset-1 ring-gray-900' : 'border-white shadow-sm') + '" style="width:28px;height:28px;background:' + hex + '" title="' + activeCalName + '"></button>';
                                    }

                                    // Other calendar color swatches (from settings, excluding active)
                                    var otherCalSwatches = [];
                                    if (typeof clickaioCalendarsConfig === 'object' && clickaioCalendarsConfig) {
                                        Object.keys(clickaioCalendarsConfig).forEach(function(calId) {
                                            var calConfig = clickaioCalendarsConfig[calId];
                                            if (calConfig && calConfig.color && calConfig.selected) {
                                                var hex = calConfig.color.toUpperCase();
                                                if (!seenColors[hex]) {
                                                    seenColors[hex] = true;
                                                    var isSelected = eventColor && eventColor.toUpperCase() === hex;
                                                    otherCalSwatches.push('<button onclick="setEventColor(\'' + appointmentId + '\', \'' + hex + '\')" class="rounded-full border-2 transition-all hover:scale-110 ' + (isSelected ? 'border-gray-900 ring-2 ring-offset-1 ring-gray-900' : 'border-white shadow-sm') + '" style="width:28px;height:28px;background:' + hex + '" title="' + (calConfig.name || 'Calendar') + '"></button>');
                                                }
                                            }
                                        });
                                    }
                                    var calSwatchesHtml = activeCalSwatchHtml + otherCalSwatches.join('');

                                    // Service color swatches (deduped against calendar colors)
                                    const svcs = apt && Array.isArray(apt.services) ? apt.services : [];
                                    const serviceColors = [];
                                    svcs.forEach(function(s) {
                                        var c = s.service_color;
                                        if (!c && s.service_id && typeof selectServicesData !== 'undefined' && selectServicesData.length > 0) {
                                            var svcInfo = selectServicesData.find(function(d) { return d.id === s.service_id; });
                                            if (svcInfo && svcInfo.color) c = svcInfo.color;
                                        }
                                        if (c && !seenColors[c.toUpperCase()]) {
                                            seenColors[c.toUpperCase()] = true;
                                            serviceColors.push({hex: c.toUpperCase(), name: (s.service_name || 'Service')});
                                        }
                                    });
                                    var serviceSwatches = serviceColors.map(function(c) {
                                        const isSelected = eventColor && eventColor.toUpperCase() === c.hex;
                                        return '<button onclick="setEventColor(\'' + appointmentId + '\', \'' + c.hex + '\')" class="rounded-full border-2 transition-all hover:scale-110 ' + (isSelected ? 'border-gray-900 ring-2 ring-offset-1 ring-gray-900' : 'border-white shadow-sm') + '" style="width:28px;height:28px;background:' + c.hex + '" title="' + c.name + '"></button>';
                                    }).join('');

                                    // Separator between calendar and service swatches
                                    var separator = (calSwatchesHtml && serviceSwatches) ? '<div style="width:1px;height:20px;background:#d1d5db;margin:0 2px;flex-shrink:0"></div>' : '';

                                    // Known hexes includes both calendar and service colors
                                    var knownHexes = Object.keys(seenColors);
                                    var isCustom = eventColor && !knownHexes.some(function(p){ return eventColor.toUpperCase() === p; });
                                    var customBtn = '<div class="relative"><button onclick="document.getElementById(\'customColorPicker_' + appointmentId + '\').click()" class="rounded-full border-2 transition-all hover:scale-110 flex items-center justify-center ' + (isCustom ? 'border-gray-900 ring-2 ring-offset-1 ring-gray-900' : 'border-gray-300') + '" style="width:28px;height:28px;background: conic-gradient(red, yellow, lime, aqua, blue, magenta, red)" title="Custom color"></button><input type="color" id="customColorPicker_' + appointmentId + '" value="' + (eventColor || '#003047') + '" class="absolute opacity-0 w-0 h-0" onchange="setEventColor(\'' + appointmentId + '\', this.value)"></div>';
                                    return calSwatchesHtml + separator + serviceSwatches + customBtn;
                                })()}
                            </div>
                        </div>
                    </div>
                </div>
                ` : ''}
                <div class="p-4 bg-gray-50 rounded-xl">
                    <div class="space-y-4">
                        <div>
                            <label class="flex items-center justify-between cursor-pointer mb-2">
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" id="noShowToggle" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500" ${isNoShow ? 'checked' : ''} onchange="toggleNoShow(${appointmentId}, this.checked)">
                                    <span class="text-sm font-medium text-gray-700">No Show</span>
                                </div>
                            </label>
                            <p class="text-xs text-gray-500 ml-6">Mark this appointment as a no-show if the customer did not arrive for their scheduled appointment.</p>
                        </div>
                        <div id="smsNotificationSection_${appointmentId}" class="${isNoShow ? '' : 'hidden'}">
                            <div class="flex items-center justify-between mb-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" id="smsNotificationToggle" class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500" onchange="toggleSMSNotificationCheckbox(${appointmentId}, this.checked)">
                                    <span class="text-sm font-medium text-gray-700">${hasRealPhone ? 'Send SMS Notification' : 'Send Email Notification'}</span>
                                </label>
                                <button id="sendSMSBtn_${appointmentId}" onclick="sendSMSNotification(${appointmentId})" disabled class="px-4 py-2 bg-gray-300 text-gray-500 rounded-lg hover:bg-gray-400 transition-all font-medium text-sm flex items-center gap-2 cursor-not-allowed">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    Send
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 ml-6">${hasRealPhone ? 'Send a SMS notification to the customer about this appointment.' : 'Send an email notification to the customer about this appointment.'}</p>
                        </div>
                    </div>
                </div>
                ` : ''}
            </div>
            </div>

            <div class="flex-shrink-0 px-6 py-4 border-t border-gray-200 bg-white rounded-b-2xl">
                <div class="flex items-center justify-end">
                    <div class="flex gap-3">
                    ${!isTechnician ? `
                        <button onclick="deleteAppointment('${appointmentId}', '${customerName.replace(/'/g, "\\'")}')" class="px-4 py-2.5 border-2 border-red-500 text-red-500 bg-transparent rounded-lg hover:bg-red-50 transition-all font-medium flex items-center justify-center gap-2 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Delete
                    </button>
                        <button onclick="assignAndUpdateStatus('${appointmentId}', '${customerName}')" class="px-4 py-2.5 border-2 border-[#003047] text-[#003047] bg-transparent rounded-lg hover:bg-[#e6f0f3] transition-all font-medium flex items-center justify-center gap-2 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Assign
                    </button>
                        <button onclick="editBooking(${appointmentId})" class="px-4 py-2.5 border-2 border-[#003047] text-[#003047] bg-transparent rounded-lg hover:bg-[#e6f0f3] transition-all font-medium flex items-center justify-center gap-2 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Booking
                    </button>
                    ` : ''}
                    </div>
                </div>
            </div>
        </div>
    `;
    
    if (typeof openModal === 'function') {
        openModal(modalContent, 'default');
        
        // Set references for technician selection updates
        currentAppointmentId = appointmentId;
        // Store reference to the technician display element
        setTimeout(() => {
            currentEventModalElement = document.getElementById(`technicianDisplay_${appointmentId}`);
            // Update technician display and show/hide event color section based on current view
            updateEventModalTechnicianDisplay();
            // Initialize Select2 on calendar dropdown
            initCalendarSelect2(appointmentId);
            // Auto-set event color from calendar if no color is set
            if (!eventColor) {
                const apt = bookingsData.find(a => a.id.toString() === appointmentId.toString());
                const currentCalId = (apt && apt.ghl_calendar_id) || ghlDefaultCalendarId;
                const calColor = getCalendarColor(currentCalId);
                if (calColor) {
                    setEventColor(appointmentId, calColor);
                }
            }
        }, 100);
    }
}

// View appointment details from list view (now uses shared modal)
function viewAppointment(bookingId) {
    const appointment = bookingsData.find(apt => apt.id.toString() === bookingId.toString());
    if (!appointment) return;
    
    const customer = customersData.find(c => c.id.toString() === appointment.customer_id.toString());
    const customerName = customer ? `${customer.firstName} ${customer.lastName}` : `Customer #${appointment.customer_id}`;
    const customerPhone = customer ? (customer.phone || null) : null;
    const customerEmail = customer ? (customer.email || '') : '';

    const aptDateTime = appointment.appointment_datetime || appointment.created_at;
    const aptDate = new Date(aptDateTime);
    
    // Get technician names
    let technicians = 'Not Assigned';
    if (appointment.assigned_technician && Array.isArray(appointment.assigned_technician) && appointment.assigned_technician.length > 0) {
        const technicianNames = appointment.assigned_technician.map(techId => {
            const tech = techniciansData.find(t => t.id.toString() === techId.toString());
            return tech ? `${tech.firstName} ${tech.lastName}` : `Technician #${techId}`;
        });
        technicians = technicianNames.join(', ');
    }
    
    // Build appointment data object
    const bookingTypeDisplay = (appointment.appointment === 'walk-in') ? 'Walk-In' : 'Booked';
    const appointmentData = {
        id: appointment.id,
        customer: customerName,
        technician: technicians,
        phone: customerPhone,
        email: customerEmail,
        status: appointment.status,
        date: aptDate,
        isNoShow: noShowStatus[appointment.id] || false,
        assigned_technician: appointment.assigned_technician,
        bookingType: bookingTypeDisplay,
        color: appointment.color || null
    };
    
    // Use shared modal function
    showAppointmentModal(appointmentData);
}

function openNewBookingModal() {
    // Redirect to booking page or open new booking modal
    window.location.href = '{{ $bookingUrl }}';
}

function editBooking(eventId) {
    // Redirect to edit booking page with event ID
    window.location.href = '{{ $editBookingUrl }}?id=' + eventId;
}

function setEventColor(appointmentId, color, skipModalRefresh) {
    const apiUrl = window.salonCalendarAppointmentsApiUrl;
    salonApi.put(apiUrl + '/' + appointmentId, { color: color }).then(function() {
        // Update bookingsData
        const apt = bookingsData.find(a => a.id.toString() === appointmentId.toString());
        if (apt) apt.color = color;

        // Update the FullCalendar event visually
        if (calendarInstance) {
            const event = calendarInstance.getEventById(appointmentId.toString());
            if (event) {
                // Find the DOM element for this event
                const eventEls = document.querySelectorAll('[data-custom-color]');

                if (color) {
                    event.setProp('backgroundColor', color);
                    event.setProp('borderColor', color);
                    event.setProp('textColor', '#ffffff');
                    // Add custom color class
                    const currentClasses = event.classNames || [];
                    if (currentClasses.indexOf('event-custom-color') === -1) {
                        event.setProp('classNames', currentClasses.concat(['event-custom-color']));
                    }
                } else {
                    // Revert to default colors based on technician assignment
                    const hasTech = event.extendedProps.hasTechnician;
                    const isNoShow = event.extendedProps.isNoShow;
                    if (isNoShow) {
                        event.setProp('backgroundColor', '');
                        event.setProp('borderColor', '');
                        event.setProp('textColor', '');
                    } else {
                        event.setProp('backgroundColor', hasTech ? '#003047' : 'transparent');
                        event.setProp('borderColor', '#003047');
                        event.setProp('textColor', hasTech ? '#ffffff' : '#003047');
                    }
                    // Remove custom color class
                    const currentClasses = event.classNames || [];
                    event.setProp('classNames', currentClasses.filter(function(c) { return c !== 'event-custom-color'; }));
                }
                event.setExtendedProp('color', color);

                // Force !important styles on the DOM element after FullCalendar re-renders
                setTimeout(function() {
                    var evIsNoShow = event.extendedProps.isNoShow;
                    const targetEls = document.querySelectorAll('[data-event-id="' + appointmentId + '"]');
                    targetEls.forEach(function(el) {
                        if (evIsNoShow) {
                            el.removeAttribute('style');
                            el.removeAttribute('data-custom-color');
                            var mainEl = el.querySelector('.fc-event-main');
                            if (mainEl) mainEl.removeAttribute('style');
                        } else if (color) {
                            el.style.setProperty('background-color', color, 'important');
                            el.style.setProperty('background', color, 'important');
                            el.style.setProperty('border-color', color, 'important');
                            el.style.setProperty('color', '#ffffff', 'important');
                            el.setAttribute('data-custom-color', color);
                        } else {
                            el.removeAttribute('data-custom-color');
                            el.style.removeProperty('background-color');
                            el.style.removeProperty('background');
                            el.style.removeProperty('border-color');
                            el.style.removeProperty('color');
                        }
                    });
                }, 50);
            }
        }

        // Re-render list view if visible
        const listViewContainer = document.getElementById('listViewContainer');
        if (listViewContainer && !listViewContainer.classList.contains('hidden')) {
            renderTechnicianListView();
        }

        // Re-render modal to update selected state (skip when called from service save flow)
        if (!skipModalRefresh) {
            if (typeof closeModal === 'function') closeModal();
            const updatedAppointment = bookingsData.find(a => a.id.toString() === appointmentId.toString());
            if (updatedAppointment) {
                viewAppointment(appointmentId);
            }
        }
    }).catch(function(err) {
        console.error('Failed to save event color:', err);
    });
}

function deleteAppointment(appointmentId, customerName) {
    openConfirmModal({
        title: 'Delete appointment',
        message: 'You are about to permanently remove the appointment for ' + boldName(customerName) + '. Do you want to continue?',
        confirmLabel: 'Delete',
        nested: true,
        onConfirm: function() {
            // Gather appointment and customer data before deletion
            const appointment = bookingsData.find(a => a.id.toString() === appointmentId.toString());
            const customer = appointment ? customersData.find(c => c.id.toString() === appointment.customer_id.toString()) : null;

            var webhookPayload = null;
            if (appointment && customer) {
                var aptDt = appointment.appointment_datetime || appointment.created_at || '';
                if (typeof aptDt === 'string') {
                    aptDt = aptDt.replace(/Z$/, '').replace(/[+-]\d{2}:\d{2}$/, '').replace(/\.\d+/, '');
                }
                var dtObj = aptDt ? new Date(aptDt.replace(/-/g, '/').replace('T', ' ')) : null;
                var formattedDt = '';
                if (dtObj && !isNaN(dtObj.getTime())) {
                    var yy = dtObj.getFullYear();
                    var mm = String(dtObj.getMonth() + 1).padStart(2, '0');
                    var dd = String(dtObj.getDate()).padStart(2, '0');
                    var hh = dtObj.getHours();
                    var mi = String(dtObj.getMinutes()).padStart(2, '0');
                    var ampm = hh >= 12 ? 'PM' : 'AM';
                    var hh12 = hh % 12 || 12;
                    formattedDt = yy + '-' + mm + '-' + dd + ' ' + String(hh12).padStart(2, '0') + ':' + mi + ' ' + ampm;
                }
                webhookPayload = {
                    customer_id: String(appointment.customer_id),
                    status: 'cancelled',
                    firstname: customer.firstName || '',
                    lastname: customer.lastName || '',
                    phone: customer.phone || '',
                    email: customer.email || '',
                    datetime: formattedDt
                };
            }

            const apiUrl = window.salonCalendarAppointmentsApiUrl;
            salonApi.delete(apiUrl + '/' + appointmentId).then(function() {
                // Update local techniciansData service counts (backend already updated the turn tracker DB)
                if (appointment && Array.isArray(appointment.services) && appointment.services.length > 0) {
                    var deletedCountByTech = {};
                    appointment.services.forEach(function(s) {
                        if (!s.technician_id) return;
                        var tid = s.technician_id.toString();
                        var svcData = typeof selectServicesData !== 'undefined' && selectServicesData.length > 0
                            ? selectServicesData.find(function(d) { return d.id === s.service_id; }) : null;
                        var sc = svcData && typeof svcData.service_count === 'number' ? svcData.service_count : 0;
                        if (!deletedCountByTech[tid]) deletedCountByTech[tid] = 0;
                        deletedCountByTech[tid] += sc * (s.quantity || 1);
                    });
                    Object.keys(deletedCountByTech).forEach(function(tid) {
                        var tech = techniciansData.find(function(t) { return t.id.toString() === tid; });
                        if (tech) tech.services = Math.max(0, (tech.services || 0) - deletedCountByTech[tid]);
                    });
                }

                // Close modal
                if (typeof closeModal === 'function') closeModal();

                // Remove from bookingsData
                const index = bookingsData.findIndex(a => a.id.toString() === appointmentId.toString());
                if (index !== -1) bookingsData.splice(index, 1);

                // Remove from calendar
                if (calendarInstance) {
                    const event = calendarInstance.getEventById(appointmentId.toString());
                    if (event) event.remove();
                }

                // Re-render list view if visible
                const listViewContainer = document.getElementById('listViewContainer');
                if (listViewContainer && !listViewContainer.classList.contains('hidden')) {
                    renderTechnicianListView();
                }

                showSuccessMessage('Appointment deleted successfully');

                // Send update appointment webhook
                if (webhookPayload) {
                    salonApi.post(window.salonWebhookApiUrl, {
                        webhook_key: 'ghl_webhook_update_appointment',
                        payload: webhookPayload,
                    }).catch(function(err) {
                        console.error('Failed to send update appointment webhook:', err);
                    });
                }
            }).catch(function(err) {
                console.error('Failed to delete appointment:', err);
                showErrorMessage('Failed to delete appointment');
            });
        }
    });
}

function assignAppointment(appointmentId) {
    // Close the modal
    if (typeof closeModal === 'function') {
        closeModal();
    }
    
    // Find and fade out event in calendar grid view
    if (calendarInstance) {
        const event = calendarInstance.getEventById(appointmentId.toString());
        if (event) {
            // Get the event element
            let eventEl = event.el;
            if (!eventEl) {
                // Try finding by event ID in the DOM
                eventEl = document.querySelector(`[data-event-id="${appointmentId}"]`);
            }
            if (!eventEl) {
                // Try finding by class and title
                const eventTitle = event.title || '';
                const allEvents = document.querySelectorAll('.fc-event');
                allEvents.forEach(el => {
                    if (el.textContent && el.textContent.includes(eventTitle)) {
                        eventEl = el;
                    }
                });
            }
            
            if (eventEl) {
                // Apply fade-out animation
                eventEl.style.transition = 'opacity 0.5s ease-out, transform 0.5s ease-out';
                eventEl.style.opacity = '0';
                eventEl.style.transform = 'scale(0.8)';
                
                // Remove the event after fade-out completes
                setTimeout(() => {
                    event.remove();
                    if (calendarInstance) {
                        calendarInstance.render();
                    }
                }, 500);
            } else {
                // If element not found, just remove the event
                setTimeout(() => {
                    event.remove();
                    if (calendarInstance) {
                        calendarInstance.render();
                    }
                }, 100);
            }
        }
    }
    
    // Fade out in list view if it exists
    const listViewAppointments = document.querySelectorAll(`[data-appointment-id="${appointmentId}"]`);
    if (listViewAppointments.length > 0) {
        listViewAppointments.forEach(appointmentEl => {
            appointmentEl.style.transition = 'opacity 0.5s ease-out, transform 0.5s ease-out';
            appointmentEl.style.opacity = '0';
            appointmentEl.style.transform = 'scale(0.8)';
            setTimeout(() => {
                appointmentEl.remove();
                // Re-render list view if needed
                if (typeof renderTechnicianListView === 'function') {
                    renderTechnicianListView();
                }
            }, 500);
        });
    }
    
    // Remove from bookingsData array
    const appointmentIndex = bookingsData.findIndex(apt => apt.id.toString() === appointmentId.toString());
    if (appointmentIndex !== -1) {
        bookingsData.splice(appointmentIndex, 1);
    }
    
    // Show toast message after a short delay to allow modal to close
    setTimeout(() => {
        showToastMessage('Appointment assigned successfully!', 'success');
    }, 100);
}

function toggleSMSNotificationCheckbox(bookingId, isChecked) {
    // Enable/disable the Send button based on checkbox state
    const sendBtn = document.getElementById(`sendSMSBtn_${bookingId}`);
    if (sendBtn) {
        if (isChecked) {
            sendBtn.disabled = false;
            sendBtn.classList.remove('bg-gray-300', 'text-gray-500', 'cursor-not-allowed', 'hover:bg-gray-400');
            sendBtn.classList.add('bg-green-500', 'text-white', 'hover:bg-green-600', 'cursor-pointer');
        } else {
            sendBtn.disabled = true;
            sendBtn.classList.remove('bg-green-500', 'text-white', 'hover:bg-green-600', 'cursor-pointer');
            sendBtn.classList.add('bg-gray-300', 'text-gray-500', 'cursor-not-allowed', 'hover:bg-gray-400');
        }
    }
}

function sendSMSNotification(bookingId) {
    const appointment = bookingsData.find(apt => apt.id.toString() === bookingId.toString());
    if (!appointment) {
        showToastMessage('Appointment not found', 'error');
        return;
    }

    const customer = customersData.find(c => c.id.toString() === appointment.customer_id.toString());
    if (!customer) {
        showToastMessage('Customer not found', 'error');
        return;
    }

    const phoneNumber = customer.phone || '';
    const emailAddress = customer.email || '';
    if (!phoneNumber && !emailAddress) {
        showToastMessage('No phone number or email available for this customer', 'error');
        return;
    }

    const sendBtn = document.getElementById(`sendSMSBtn_${bookingId}`);
    const checkbox = document.getElementById('smsNotificationToggle');
    if (sendBtn) {
        sendBtn.disabled = true;
        sendBtn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg> Sending...';
    }

    const webhookPayload = {
        firstname: customer.firstName || '',
        lastname: customer.lastName || '',
        email: emailAddress,
        phone: phoneNumber,
    };

    console.log('No Show Webhook Payload:', JSON.stringify(webhookPayload, null, 2));

    salonApi.post(window.salonWebhookApiUrl, {
        webhook_key: 'ghl_webhook_no_show_sms',
        payload: webhookPayload,
    })
    .then(function(res) {
        if (res && res.success) {
            showToastMessage('No Show notification sent successfully', 'success');
            if (checkbox) checkbox.checked = false;
            if (sendBtn) {
                sendBtn.disabled = true;
                sendBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Sent';
                sendBtn.classList.remove('bg-green-500', 'hover:bg-green-600', 'cursor-pointer');
                sendBtn.classList.add('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
            }
        } else {
            showToastMessage(res.message || 'Failed to send notification', 'error');
            if (sendBtn) {
                sendBtn.disabled = false;
                sendBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg> Send';
            }
        }
    })
    .catch(function(err) {
        console.error('Webhook error:', err);
        showToastMessage('Failed to send notification', 'error');
        if (sendBtn) {
            sendBtn.disabled = false;
            sendBtn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg> Send';
        }
    });
}

function toggleNoShow(bookingId, isNoShow) {
    const smsSection = document.getElementById(`smsNotificationSection_${bookingId}`);
    if (smsSection) {
        if (isNoShow) {
            smsSection.classList.remove('hidden');
        } else {
            smsSection.classList.add('hidden');
        }
    }

    const event = calendarInstance ? calendarInstance.getEventById(bookingId.toString()) : null;
    const appointment = bookingsData.find(function(apt) { return apt.id.toString() === bookingId.toString(); });
    const currentStatus = (event && event.extendedProps && event.extendedProps.originalStatus) || (appointment && appointment.status) || 'waiting';
    
    if (isNoShow) {
        statusBeforeNoShow[bookingId] = currentStatus;
    }
    const newStatus = isNoShow ? 'no-show' : (statusBeforeNoShow[bookingId] || 'waiting');
    
    const apiUrl = window.salonCalendarAppointmentsApiUrl;
    if (!apiUrl || typeof salonApi === 'undefined' || !salonApi.put) {
        noShowStatus[bookingId] = isNoShow;
        updateNoShowUI(bookingId, isNoShow, event);
        if (isNoShow) showToastMessage('Marked as No Show', 'success');
        else { delete statusBeforeNoShow[bookingId]; showToastMessage('No Show status removed', 'success'); }
        return;
    }
    
    salonApi.put(apiUrl + '/' + bookingId, { status: newStatus })
        .then(function(res) {
            noShowStatus[bookingId] = isNoShow;
            if (!isNoShow) delete statusBeforeNoShow[bookingId];
            var aptIndex = bookingsData.findIndex(function(apt) { return apt.id.toString() === bookingId.toString(); });
            if (aptIndex !== -1 && res && res.data) {
                bookingsData[aptIndex].status = res.data.status || newStatus;
            } else if (aptIndex !== -1) {
                bookingsData[aptIndex].status = newStatus;
            }
            updateNoShowUI(bookingId, isNoShow, event);
            if (isNoShow) showToastMessage('Marked as No Show', 'success');
            else showToastMessage('No Show status removed', 'success');
        })
        .catch(function(err) {
            if (typeof showErrorMessage === 'function') {
                showErrorMessage(err && err.message ? err.message : 'Failed to update no-show status.');
            }
        });
}

function updateNoShowUI(bookingId, isNoShow, event) {
    if (!event && calendarInstance) {
        event = calendarInstance.getEventById(bookingId.toString());
    }
    if (event) {
        var apt = bookingsData.find(function(a) { return a.id.toString() === bookingId.toString(); });
        var customColor = (apt && apt.color) || (event.extendedProps && event.extendedProps.color) || null;
        var hasTechnician = event.extendedProps && event.extendedProps.hasTechnician;

        if (isNoShow) {
            // No-show always wins — clear all inline colors, let CSS handle it
            event.setProp('backgroundColor', '');
            event.setProp('borderColor', '');
            event.setProp('textColor', '');
            if (!event.classNames.includes('event-no-show')) {
                event.setProp('classNames', [...event.classNames, 'event-no-show']);
            }
        } else {
            if (customColor) {
                event.setProp('backgroundColor', customColor);
                event.setProp('borderColor', customColor);
                event.setProp('textColor', '#ffffff');
            } else {
                event.setProp('backgroundColor', hasTechnician ? '#003047' : 'transparent');
                event.setProp('textColor', hasTechnician ? '#ffffff' : '#003047');
                event.setProp('borderColor', '#003047');
            }
            var classNames = event.classNames.filter(function(cn) { return cn !== 'event-no-show'; });
            event.setProp('classNames', classNames);
        }
        event.setExtendedProp('isNoShow', isNoShow);

        if (calendarInstance) calendarInstance.render();

        // Force DOM cleanup after render
        setTimeout(function() {
            var targetEls = document.querySelectorAll('[data-event-id="' + bookingId + '"]');
            targetEls.forEach(function(el) {
                if (isNoShow) {
                    // Remove all inline styles so CSS .event-no-show takes full control
                    el.removeAttribute('style');
                    var mainEl = el.querySelector('.fc-event-main');
                    if (mainEl) mainEl.removeAttribute('style');
                } else if (customColor) {
                    el.style.setProperty('background-color', customColor, 'important');
                    el.style.setProperty('background', customColor, 'important');
                    el.style.setProperty('border-color', customColor, 'important');
                    el.style.setProperty('color', '#ffffff', 'important');
                }
            });
        }, 50);
    }
    var listViewContainer = document.getElementById('listViewContainer');
    if (listViewContainer && !listViewContainer.classList.contains('hidden') && typeof renderTechnicianListView === 'function') {
        renderTechnicianListView();
    }
}

function showToastMessage(message, type = 'info') {
    const colors = {
        success: 'bg-green-500',
        warning: 'bg-orange-500',
        error: 'bg-red-500',
        info: 'bg-blue-500'
    };
    
    const icons = {
        success: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>`,
        warning: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>`,
        error: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>`,
        info: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>`
    };
    
    const bgColor = colors[type] || colors.info;
    const icon = icons[type] || icons.info;
    
    const toastDiv = document.createElement('div');
    toastDiv.className = `fixed top-4 right-4 ${bgColor} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all transform translate-x-0`;
    toastDiv.style.opacity = '0';
    toastDiv.style.transform = 'translateX(400px)';
    toastDiv.innerHTML = `
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${icon}
            </svg>
            <div>
                <p class="font-semibold text-sm">${message}</p>
            </div>
        </div>
    `;
    document.body.appendChild(toastDiv);
    
    // Animate in
    setTimeout(() => {
        toastDiv.style.opacity = '1';
        toastDiv.style.transform = 'translateX(0)';
    }, 10);
    
    // Animate out and remove
    setTimeout(() => {
        toastDiv.style.opacity = '0';
        toastDiv.style.transform = 'translateX(400px)';
        setTimeout(() => toastDiv.remove(), 300);
    }, 3000);
}

function showSuccessMessage(message) {
    const successDiv = document.createElement('div');
    successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all';
    successDiv.innerHTML = `
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <div>
                <p class="font-semibold">Success</p>
                <p class="text-sm opacity-90">${message}</p>
            </div>
        </div>
    `;
    document.body.appendChild(successDiv);
    
    setTimeout(() => {
        successDiv.style.opacity = '0';
        successDiv.style.transform = 'translateY(-20px)';
        setTimeout(() => successDiv.remove(), 300);
    }, 3000);
}

function showEventMovedMessage(eventTitle, newDate) {
    const messageDiv = document.createElement('div');
    messageDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all';
    messageDiv.innerHTML = `
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <div>
                <p class="font-semibold">Event Moved</p>
                <p class="text-sm opacity-90">${eventTitle} moved to ${newDate.toLocaleDateString()}</p>
            </div>
        </div>
    `;
    document.body.appendChild(messageDiv);
    
    setTimeout(() => {
        messageDiv.style.opacity = '0';
        messageDiv.style.transform = 'translateY(-20px)';
        setTimeout(() => messageDiv.remove(), 300);
    }, 3000);
}

function showErrorMessage(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-all';
    errorDiv.innerHTML = `
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
            <div>
                <p class="font-semibold">Error</p>
                <p class="text-sm opacity-90">${message}</p>
            </div>
        </div>
    `;
    document.body.appendChild(errorDiv);
    
    setTimeout(() => {
        errorDiv.style.opacity = '0';
        errorDiv.style.transform = 'translateY(-20px)';
        setTimeout(() => errorDiv.remove(), 300);
    }, 3000);
}


// Assign and update status to unpaid
async function assignAndUpdateStatus(appointmentId, customerName) {
    const apiUrl = window.salonCalendarAppointmentsApiUrl;

    // Update status to unpaid
    if (apiUrl && typeof salonApi !== 'undefined' && salonApi.put) {
        try {
            await salonApi.put(apiUrl + '/' + appointmentId, { status: 'unpaid' });

            // Update local appointment data
            const appointment = bookingsData.find(apt => apt.id.toString() === appointmentId.toString());
            if (appointment) {
                appointment.status = 'unpaid';
            }

            // Update calendar event color
            if (calendarInstance) {
                const events = calendarInstance.getEvents();
                const event = events.find(e => e.id && e.id.toString() === appointmentId.toString());
                if (event) {
                    event.setProp('className', 'fc-event-unpaid');
                    event.setExtendedProp('originalStatus', 'unpaid');
                }
            }

            // Re-render list view if it's currently visible
            const listViewContainer = document.getElementById('listViewContainer');
            if (listViewContainer && !listViewContainer.classList.contains('hidden') && typeof renderTechnicianListView === 'function') {
                renderTechnicianListView();
            }

            // Close the modal
            if (typeof closeModal === 'function') {
                closeModal();
            }

            // Show success message
            if (typeof showSuccessMessage === 'function') {
                showSuccessMessage('Appointment successfully assigned.');
            }
        } catch (err) {
            console.error('Error updating appointment status:', err);
            if (typeof showErrorMessage === 'function') {
                showErrorMessage('Failed to update appointment status.');
            }
        }
    } else {
        // Close modal and show message even if API is not available
        if (typeof closeModal === 'function') {
            closeModal();
        }
        if (typeof showSuccessMessage === 'function') {
            showSuccessMessage('Appointment successfully assigned.');
        }
    }
}

// Open technician selection modal with event reference
function openTechnicianSelectionModalWithEvent(appointmentId, customerName) {
    // Find the FullCalendar event from the calendar instance
    if (calendarInstance) {
        const events = calendarInstance.getEvents();
        currentEvent = events.find(e => e.id && e.id.toString() === appointmentId.toString());
    }

    openTechnicianSelectionModal(appointmentId, customerName);
}

// Open technician selection modal
function openTechnicianSelectionModal(appointmentId, customerName) {
    currentAppointmentId = appointmentId;
    selectedTechnicianIds = [];
    technicianSearchTerm = '';
    
    // Store reference to the technician display element
    currentEventModalElement = document.getElementById(`technicianDisplay_${appointmentId}`);
    
    // Get current assigned technicians from the appointment
    const appointment = bookingsData.find(apt => apt.id.toString() === appointmentId.toString());
    if (appointment && appointment.assigned_technician) {
        if (Array.isArray(appointment.assigned_technician)) {
            selectedTechnicianIds = appointment.assigned_technician.map(id => id.toString());
        } else {
            selectedTechnicianIds = [appointment.assigned_technician.toString()];
        }
    }
    
    const modalContent = `
        <div class="flex flex-col h-[80vh] max-h-[80vh] overflow-hidden">
            <!-- Fixed Header -->
            <div class="flex-shrink-0 px-4 sm:px-6 py-4 border-b border-gray-200 bg-white">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-gray-900">Select Technicians for ${customerName}</h3>
                    <button onclick="closeCalendarTechnicianModal()" class="p-2 -m-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition focus:outline-none focus:ring-2 focus:ring-gray-400 focus:ring-offset-2" aria-label="Close">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Content Area -->
            <div class="flex-1 min-h-0 px-4 sm:px-6 py-4 sm:py-6 bg-gray-50">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6 h-full">
                    <!-- Available Technicians Column -->
                    <div class="border border-gray-200 rounded-lg p-3 sm:p-4 flex flex-col h-full bg-white">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-semibold text-gray-900">Available Technicians</h4>
                                <span id="availableCount" class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">${techniciansData.length}</span>
                            </div>
                            <p class="text-xs text-gray-500 mb-3">Click to assign technicians</p>
                            <div class="relative mb-4">
                                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <input type="text" id="technicianSearchInput" placeholder="Search technicians..." oninput="searchTechnicians(this.value)" class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] text-sm">
                                <button id="clearTechnicianSearchBtn" onclick="clearTechnicianSearch()" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition hidden">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div id="availableTechniciansContainer" class="overflow-y-auto space-y-3"></div>
                    </div>

                    <!-- Assigned Technicians Column -->
                    <div class="border border-gray-200 rounded-lg p-3 sm:p-4 flex flex-col h-full bg-white">
                        <div class="flex-shrink-0">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-semibold text-gray-900">Assigned Technicians</h4>
                                <span id="assignedCount" class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">${selectedTechnicianIds.length}</span>
                            </div>
                            <p class="text-xs text-gray-500 mb-3">Click to remove technicians</p>
                            <div class="relative mb-4">
                                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <input type="text" id="assignedTechnicianSearchInput" placeholder="Search assigned..." oninput="searchAssignedTechnicians(this.value)" class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] text-sm">
                                <button id="clearAssignedSearchBtn" onclick="clearAssignedSearch()" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 transition hidden">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div id="assignedTechniciansContainer" class="overflow-y-auto space-y-3"></div>
                    </div>
                </div>
            </div>

            <!-- Fixed Footer -->
            <div class="flex-shrink-0 px-4 sm:px-6 py-4 border-t border-gray-200 bg-white">
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3">
                    <button onclick="closeCalendarTechnicianModal()" class="min-w-[5rem] px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95 text-center">
                        Cancel
                    </button>
                    <button onclick="confirmTechnicianSelection()" class="min-w-[5rem] px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95 text-center">
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    `;

    openNestedModal(modalContent, 'large-flex', false);

    // Setup dynamic height for containers based on screen size
    setTimeout(function() {
        // Remove previous resize handler if exists
        if (resizeHandlerForTechnicians) {
            window.removeEventListener('resize', resizeHandlerForTechnicians);
        }

        resizeHandlerForTechnicians = function() {
            const availableContainer = document.getElementById('availableTechniciansContainer');
            const assignedContainer = document.getElementById('assignedTechniciansContainer');
            const screenHeight = window.innerHeight;
            const screenWidth = window.innerWidth;
            let containerHeight;

            if (screenWidth < 640) {
                containerHeight = Math.floor(screenHeight * 0.25) + 'px'; // ~25vh for small screens
            } else if (screenWidth < 1024) {
                containerHeight = Math.floor(screenHeight * 0.30) + 'px'; // ~30vh for medium screens
            } else {
                containerHeight = Math.floor(screenHeight * 0.35) + 'px'; // ~35vh for large screens
            }

            if (availableContainer) availableContainer.style.height = containerHeight;
            if (assignedContainer) assignedContainer.style.height = containerHeight;
        };

        resizeHandlerForTechnicians();
        window.addEventListener('resize', resizeHandlerForTechnicians);

        renderAvailableTechnicians();
        renderAssignedTechnicians();
    }, 50);
}

// Close calendar technician modal with cleanup
function closeCalendarTechnicianModal() {
    // Cleanup resize handler
    if (resizeHandlerForTechnicians) {
        window.removeEventListener('resize', resizeHandlerForTechnicians);
        resizeHandlerForTechnicians = null;
    }
    // Reset search terms
    technicianSearchTerm = '';
    assignedTechnicianSearchTerm = '';
    closeNestedModal();
}

// Search technicians
function searchTechnicians(searchTerm) {
    technicianSearchTerm = searchTerm.toLowerCase().trim();

    const clearBtn = document.getElementById('clearTechnicianSearchBtn');
    if (clearBtn) {
        if (searchTerm.trim() !== '') {
            clearBtn.classList.remove('hidden');
        } else {
            clearBtn.classList.add('hidden');
        }
    }

    renderAvailableTechnicians();
}

// Search assigned technicians
function searchAssignedTechnicians(searchTerm) {
    assignedTechnicianSearchTerm = searchTerm.toLowerCase().trim();

    const clearBtn = document.getElementById('clearAssignedSearchBtn');
    if (clearBtn) {
        if (searchTerm.trim() !== '') {
            clearBtn.classList.remove('hidden');
        } else {
            clearBtn.classList.add('hidden');
        }
    }

    renderAssignedTechnicians();
}

// Clear technician search
function clearTechnicianSearch() {
    const searchInput = document.getElementById('technicianSearchInput');
    const clearBtn = document.getElementById('clearTechnicianSearchBtn');

    if (searchInput) {
        searchInput.value = '';
        technicianSearchTerm = '';
        searchInput.focus();
    }

    if (clearBtn) {
        clearBtn.classList.add('hidden');
    }

    renderAvailableTechnicians();
}

// Clear assigned technician search
function clearAssignedSearch() {
    const searchInput = document.getElementById('assignedTechnicianSearchInput');
    const clearBtn = document.getElementById('clearAssignedSearchBtn');

    if (searchInput) {
        searchInput.value = '';
        assignedTechnicianSearchTerm = '';
        searchInput.focus();
    }

    if (clearBtn) {
        clearBtn.classList.add('hidden');
    }

    renderAssignedTechnicians();
}

// Render available technicians
function renderAvailableTechnicians() {
    const container = document.getElementById('availableTechniciansContainer');
    if (!container) return;

    if (techniciansData.length === 0) {
        container.innerHTML = `
            <div class="flex items-center justify-center h-full min-h-[200px]">
                <p class="text-sm text-gray-400">No technicians available</p>
            </div>
        `;
        return;
    }

    let filteredTechnicians = techniciansData;
    if (technicianSearchTerm !== '') {
        filteredTechnicians = techniciansData.filter(technician => {
            const fullName = `${technician.firstName} ${technician.lastName}`.toLowerCase();
            const initials = (technician.initials || (technician.firstName?.[0] || '') + (technician.lastName?.[0] || '')).toLowerCase();
            const searchText = fullName + ' ' + initials;
            return searchText.includes(technicianSearchTerm);
        });
    }

    if (filteredTechnicians.length === 0) {
        container.innerHTML = `
            <div class="flex items-center justify-center h-full min-h-[200px]">
                <p class="text-sm text-gray-400">No technicians found</p>
            </div>
        `;
        return;
    }
    
    filteredTechnicians = [...filteredTechnicians].sort((a, b) => {
        // Assigned technicians go last
        const aIdStr = a.id.toString();
        const bIdStr = b.id.toString();
        const aIsAssigned = selectedTechnicianIds.includes(aIdStr);
        const bIsAssigned = selectedTechnicianIds.includes(bIdStr);
        if (aIsAssigned && !bIsAssigned) return 1;
        if (!aIsAssigned && bIsAssigned) return -1;

        // Turn tracker technicians first, in exact turn tracker order; non-tracker last
        const aInTracker = turnTrackerUserIds.has(a.id);
        const bInTracker = turnTrackerUserIds.has(b.id);
        if (aInTracker && !bInTracker) return -1;
        if (!aInTracker && bInTracker) return 1;
        if (aInTracker && bInTracker) {
            return (turnTrackerPositions.get(a.id) || 0) - (turnTrackerPositions.get(b.id) || 0);
        }
        return 0;
    });
    
    const badgeStyle = 'bottom: -5px; right: -5px;';
    let html = '';
    filteredTechnicians.forEach(technician => {
        const technicianIdStr = technician.id.toString();
        const isAssigned = selectedTechnicianIds.includes(technicianIdStr);
        const initials = technician.initials || (technician.firstName?.[0] || '') + (technician.lastName?.[0] || '');
        const fullName = `${technician.firstName} ${technician.lastName}`;
        
        const containerClasses = isAssigned 
            ? "flex items-center gap-3 p-2 rounded-lg transition-colors opacity-50 grayscale cursor-pointer group hover:bg-gray-100"
            : "flex items-center gap-3 cursor-pointer group hover:bg-gray-50 p-2 rounded-lg transition-colors";
        
        const availPhoto = technician.profilePhotoUrl || technician.photo || null;
        const avatarClasses = isAssigned
            ? "w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center"
            : "w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center";

        const initialClasses = isAssigned
            ? "text-sm font-bold text-gray-500"
            : "text-sm font-bold text-gray-600";

        const nameClasses = isAssigned
            ? "text-base font-medium text-gray-400"
            : "text-base font-medium text-gray-900";

        const isOnline = !!(technician.clock_in && !technician.clock_out);
        const badgeClasses = isAssigned
            ? "absolute w-5 h-5 rounded-full border-2 border-white bg-gray-400"
            : (isOnline ? "absolute w-5 h-5 rounded-full border-2 border-white bg-green-500" : "absolute w-5 h-5 rounded-full border-2 border-white bg-gray-400");
        const servicesNum = typeof technician.services === 'number' ? technician.services : 0;
        const badgeTitle = isOnline ? 'Online' : 'Offline';

        const availAvatarHtml = availPhoto
            ? `<img src="${availPhoto}" alt="${fullName}" class="w-12 h-12 rounded-full object-cover ${isAssigned ? 'opacity-50 grayscale' : ''}">`
            : `<div class="${avatarClasses}"><span class="${initialClasses}">${initials}</span></div>`;

        html += `
            <div onclick="${isAssigned ? 'removeAssignedTechnician(' + technician.id + ')' : 'assignTechnician(' + technician.id + ')'}" class="${containerClasses}">
                <div class="relative flex-shrink-0">
                    ${availAvatarHtml}
                    <div class="${badgeClasses}" style="${badgeStyle}" title="${badgeTitle}"></div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="${nameClasses}">${fullName}</p>
                </div>
                <div class="flex-shrink-0 text-right">
                    <div class="text-xs font-medium text-gray-500 uppercase">Services</div>
                    <div class="text-lg font-semibold text-gray-900">${servicesNum}</div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
    updateCounts();
}

// Render assigned technicians
function renderAssignedTechnicians() {
    const container = document.getElementById('assignedTechniciansContainer');
    if (!container) return;

    if (selectedTechnicianIds.length === 0) {
        container.innerHTML = `
            <div class="flex items-center justify-center h-full min-h-[200px]">
                <p class="text-sm text-gray-400">No technicians assigned</p>
            </div>
        `;
        updateCounts();
        return;
    }

    // Get assigned technicians from IDs
    let assignedTechs = selectedTechnicianIds.map(technicianIdStr => {
        return techniciansData.find(t => t.id.toString() === technicianIdStr);
    }).filter(t => t != null);

    // Apply search filter if search term exists
    if (assignedTechnicianSearchTerm !== '') {
        assignedTechs = assignedTechs.filter(technician => {
            const fullName = `${technician.firstName} ${technician.lastName}`.toLowerCase();
            const initials = (technician.initials || (technician.firstName?.[0] || '') + (technician.lastName?.[0] || '')).toLowerCase();
            const searchText = fullName + ' ' + initials;
            return searchText.includes(assignedTechnicianSearchTerm);
        });
    }

    if (assignedTechs.length === 0) {
        container.innerHTML = `
            <div class="flex items-center justify-center h-full min-h-[200px]">
                <p class="text-sm text-gray-400">No technicians found</p>
            </div>
        `;
        updateCounts();
        return;
    }

    const badgeStyle = 'bottom: -5px; right: -5px;';
    let html = '';
    assignedTechs.forEach(technician => {
        if (!technician) return;
        
        const initials = technician.initials || (technician.firstName?.[0] || '') + (technician.lastName?.[0] || '');
        const fullName = `${technician.firstName} ${technician.lastName}`;
        const assignedPhoto = technician.profilePhotoUrl || technician.photo || null;
        const isOnline = !!(technician.clock_in && !technician.clock_out);
        const badgeClasses = isOnline ? "absolute w-5 h-5 rounded-full border-2 border-white bg-green-500" : "absolute w-5 h-5 rounded-full border-2 border-white bg-gray-400";
        const servicesNum = typeof technician.services === 'number' ? technician.services : 0;
        const badgeTitle = isOnline ? 'Online' : 'Offline';

        const assignedAvatarHtml = assignedPhoto
            ? `<img src="${assignedPhoto}" alt="${fullName}" class="w-12 h-12 rounded-full object-cover">`
            : `<div class="w-12 h-12 bg-[#003047] rounded-full flex items-center justify-center"><span class="text-sm font-bold text-white">${initials}</span></div>`;

        html += `
            <div onclick="removeAssignedTechnician(${technician.id})" class="flex items-center gap-3 cursor-pointer group hover:bg-gray-50 p-2 rounded-lg transition-colors">
                <div class="relative flex-shrink-0">
                    ${assignedAvatarHtml}
                    <div class="${badgeClasses}" style="${badgeStyle}" title="${badgeTitle}"></div>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-base font-medium text-gray-900">${fullName}</p>
                </div>
                <div class="flex-shrink-0 text-right">
                    <div class="text-xs font-medium text-gray-500 uppercase">Services</div>
                    <div class="text-lg font-semibold text-gray-900">${servicesNum}</div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
    updateCounts();
}

// Assign technician
function assignTechnician(technicianId) {
    const technicianIdStr = technicianId.toString();
    if (!selectedTechnicianIds.includes(technicianIdStr)) {
        selectedTechnicianIds.push(technicianIdStr);
        renderAvailableTechnicians();
        renderAssignedTechnicians();
    }
}

// Remove assigned technician
function removeAssignedTechnician(technicianId) {
    const technicianIdStr = technicianId.toString();
    selectedTechnicianIds = selectedTechnicianIds.filter(id => id !== technicianIdStr);
    renderAvailableTechnicians();
    renderAssignedTechnicians();
}

// Update counts
function updateCounts() {
    const availableCountEl = document.getElementById('availableCount');
    const assignedCountEl = document.getElementById('assignedCount');
    
    if (availableCountEl) {
        let filteredCount = techniciansData.length;
        if (technicianSearchTerm !== '') {
            filteredCount = techniciansData.filter(technician => {
                const fullName = `${technician.firstName} ${technician.lastName}`.toLowerCase();
                const initials = (technician.initials || (technician.firstName?.[0] || '') + (technician.lastName?.[0] || '')).toLowerCase();
                const searchText = fullName + ' ' + initials;
                return searchText.includes(technicianSearchTerm);
            }).length;
        }
        availableCountEl.textContent = filteredCount;
    }
    
    if (assignedCountEl) {
        assignedCountEl.textContent = selectedTechnicianIds.length;
    }
}

// Confirm technician selection
function confirmTechnicianSelection() {
    if (!currentAppointmentId) return;
    
    const technicianIds = selectedTechnicianIds.map(id => parseInt(id));
    const apiUrl = window.salonCalendarAppointmentsApiUrl;
    const doUpdateUI = function() {
        const appointmentIndex = bookingsData.findIndex(apt => apt.id.toString() === currentAppointmentId.toString());
        if (appointmentIndex !== -1) {
            bookingsData[appointmentIndex].assigned_technician = technicianIds.length > 0 ? technicianIds : null;
        }
        updateEventModalTechnicianDisplay();
        updateCalendarEventDisplay();
        const listViewContainer = document.getElementById('listViewContainer');
        if (listViewContainer && !listViewContainer.classList.contains('hidden')) {
            if (typeof renderTechnicianListView === 'function') {
                renderTechnicianListView();
            }
        }
        closeCalendarTechnicianModal();
        if (typeof showSuccessMessage === 'function') {
            showSuccessMessage('Technician assignment saved.');
        }
    };

    if (apiUrl && typeof salonApi !== 'undefined' && salonApi.put) {
        const btn = document.querySelector('[onclick="confirmTechnicianSelection()"]');
        if (btn) { btn.disabled = true; btn.textContent = 'Saving…'; }

        // Find which technicians were removed to clean up their services
        var aptData = bookingsData.find(function(a) { return a.id.toString() === currentAppointmentId.toString(); });
        var prevTechIds = aptData && Array.isArray(aptData.assigned_technician)
            ? aptData.assigned_technician.map(function(id) { return id.toString(); })
            : [];
        var removedTechIds = prevTechIds.filter(function(id) { return !technicianIds.some(function(t) { return t.toString() === id; }); });

        salonApi.put(apiUrl + '/' + currentAppointmentId, { assigned_technician: technicianIds })
            .then(function() {
                // Remove services for removed technicians, or all services if no technicians left
                var hasRemovedTechs = removedTechIds.length > 0;
                var allTechsCleared = technicianIds.length === 0;
                if ((hasRemovedTechs || allTechsCleared) && aptData && Array.isArray(aptData.services) && aptData.services.length > 0) {
                    var keepServices = allTechsCleared ? [] : aptData.services.filter(function(s) {
                        return !removedTechIds.some(function(rid) { return s.technician_id && s.technician_id.toString() === rid; });
                    });
                    var payload = keepServices.map(function(s) {
                        var svcData = selectServicesData.length > 0 ? selectServicesData.find(function(d) { return d.id === s.service_id; }) : null;
                        return {
                            service: (svcData && svcData.categories && svcData.categories[0]) || s.service || '',
                            service_id: s.service_id,
                            technician_id: s.technician_id,
                            quantity: s.quantity || 1,
                            unit_price: s.unit_price
                        };
                    }).filter(function(s) { return s.service && s.technician_id; });
                    var svcUrl = base.replace(/\/data\/?$/, '') + '/appointments/' + currentAppointmentId + '/services';
                    salonApi.put(svcUrl, { services: payload }).then(function(res) {
                        if (aptData && res && res.data && Array.isArray(res.data.services)) {
                            aptData.services = res.data.services.map(function(s) {
                                if (!s.service_color && s.service_id && selectServicesData.length > 0) {
                                    var info = selectServicesData.find(function(d) { return d.id === s.service_id; });
                                    if (info && info.color) s.service_color = info.color;
                                }
                                return s;
                            });
                        } else if (aptData) {
                            aptData.services = keepServices;
                        }
                        updateEventModalTechnicianDisplay();
                        refreshEventColorSwatches(currentAppointmentId);
                    }).catch(function(err) { console.error('Failed to clean up services:', err); });

                    // Update turn tracker service count for removed technicians
                    var turnTrackerUrl = base.replace(/\/data\/?$/, '') + '/turn-tracker';
                    var removedServices = aptData.services.filter(function(s) {
                        return removedTechIds.some(function(rid) { return s.technician_id && s.technician_id.toString() === rid; });
                    });
                    // Group removed services by technician and calculate service_count to subtract
                    var removedCountByTech = {};
                    removedServices.forEach(function(s) {
                        var tid = s.technician_id.toString();
                        var svcData = selectServicesData.length > 0 ? selectServicesData.find(function(d) { return d.id === s.service_id; }) : null;
                        var sc = svcData && typeof svcData.service_count === 'number' ? svcData.service_count : 0;
                        if (!removedCountByTech[tid]) removedCountByTech[tid] = 0;
                        removedCountByTech[tid] += sc * (s.quantity || 1);
                    });
                    var turnTrackerEntries = [];
                    Object.keys(removedCountByTech).forEach(function(tid) {
                        var tech = techniciansData.find(function(t) { return t.id.toString() === tid; });
                        var currentServices = tech && typeof tech.services === 'number' ? tech.services : 0;
                        var newTotal = Math.max(0, currentServices - removedCountByTech[tid]);
                        turnTrackerEntries.push({ user_id: parseInt(tid), services: newTotal });
                        if (tech) tech.services = newTotal;
                    });
                    if (turnTrackerEntries.length > 0) {
                        salonApi.put(turnTrackerUrl, { entries: turnTrackerEntries }).then(function() {
                            renderTechnicianListView();
                        }).catch(function(err) { console.error('Turn tracker update failed for removed technicians:', err); });
                    }
                }
                doUpdateUI();
            })
            .catch(function(err) {
                if (btn) { btn.disabled = false; btn.textContent = 'Confirm'; }
                if (typeof showErrorMessage === 'function') {
                    showErrorMessage(err && err.message ? err.message : 'Failed to save technician assignment.');
                }
            });
    } else {
        const appointmentIndex = bookingsData.findIndex(apt => apt.id.toString() === currentAppointmentId.toString());
        if (appointmentIndex !== -1) {
            var fallbackPrevIds = Array.isArray(bookingsData[appointmentIndex].assigned_technician)
                ? bookingsData[appointmentIndex].assigned_technician.map(function(id) { return id.toString(); })
                : [];
            var fallbackRemovedIds = fallbackPrevIds.filter(function(id) { return !technicianIds.some(function(t) { return t.toString() === id; }); });
            bookingsData[appointmentIndex].assigned_technician = technicianIds.length > 0 ? technicianIds : null;
            if (technicianIds.length === 0) {
                bookingsData[appointmentIndex].services = [];
            } else if (fallbackRemovedIds.length > 0 && Array.isArray(bookingsData[appointmentIndex].services)) {
                bookingsData[appointmentIndex].services = bookingsData[appointmentIndex].services.filter(function(s) {
                    return !fallbackRemovedIds.some(function(rid) { return s.technician_id && s.technician_id.toString() === rid; });
                });
            }
        }
        updateEventModalTechnicianDisplay();
        updateCalendarEventDisplay();
        const listViewContainer = document.getElementById('listViewContainer');
        if (listViewContainer && !listViewContainer.classList.contains('hidden')) {
            if (typeof renderTechnicianListView === 'function') {
                renderTechnicianListView();
            }
        }
        closeCalendarTechnicianModal();
    }
}

// Update technician display in the event modal
function updateEventModalTechnicianDisplay() {
    if (!currentEventModalElement || !currentAppointmentId) return;

    var payBaseUrl = '{{ route("salon.booking.pay") }}';
    var appointment = bookingsData.find(function(a) { return a.id.toString() === currentAppointmentId.toString(); });

    // Use selectedTechnicianIds if available, otherwise fall back to appointment's assigned technicians
    var techIds = selectedTechnicianIds.length > 0
        ? selectedTechnicianIds
        : (appointment && Array.isArray(appointment.assigned_technician)
            ? appointment.assigned_technician.map(function(id) { return id.toString(); })
            : []);

    // Show/hide event color section based on technician assignment
    var colorSection = document.getElementById('eventColorSection_' + currentAppointmentId);
    if (techIds.length === 0) {
        currentEventModalElement.innerHTML = '<p class="text-sm text-gray-400">Not Assigned</p>';
        // Hide event color section and clear color when no technicians
        if (colorSection) {
            colorSection.classList.add('hidden');
            setEventColor(currentAppointmentId, null, true);
        }
        return;
    }
    // Show event color section only in calendar/grid view, hide in list view
    if (colorSection) {
        var calendarContainer = document.getElementById('calendarContainer');
        var isCalendarView = calendarContainer && !calendarContainer.classList.contains('hidden');
        if (isCalendarView) {
            colorSection.classList.remove('hidden');
        } else {
            colorSection.classList.add('hidden');
        }
    }

    var allSvcs = appointment && Array.isArray(appointment.services) ? appointment.services : [];
    var html = '<div class="space-y-2">';
    techIds.forEach(function(techIdStr) {
        var tech = techniciansData.find(function(t) { return t.id.toString() === techIdStr; });
        if (!tech) return;
        var name = tech.firstName + ' ' + tech.lastName;
        var initials = tech.initials || (tech.firstName || '')[0] + (tech.lastName || '')[0];
        var photo = tech.profilePhotoUrl || tech.photo || null;
        var serviceCount = typeof tech.services === 'number' ? tech.services : 0;
        var techSvcs = allSvcs.filter(function(s) { return s.technician_id && s.technician_id.toString() === tech.id.toString(); });
        var techIsOnline = !!(tech.clock_in && !tech.clock_out);
        var techBadgeColor = techIsOnline ? 'bg-green-500' : 'bg-gray-400';
        var techStatusBadge = '<span class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full border-2 border-white ' + techBadgeColor + '"></span>';
        var avatarInner = photo
            ? '<div class="w-9 h-9 rounded-full overflow-hidden border border-gray-200" style="min-width:36px;min-height:36px;max-width:36px;max-height:36px"><img src="' + photo + '" alt="' + name + '" style="width:36px;height:36px;object-fit:cover"></div>'
            : '<div class="w-9 h-9 bg-[#e6f0f3] rounded-full flex items-center justify-center border border-gray-200" style="min-width:36px;min-height:36px;max-width:36px;max-height:36px"><span class="text-xs font-bold text-[#003047]">' + initials + '</span></div>';
        var avatarHtml = '<div class="relative">' + avatarInner + techStatusBadge + '</div>';
        var svcListHtml = '';
        if (techSvcs.length > 0) {
            var svcItems = techSvcs.map(function(s) {
                var sName = s.service_name || s.service || 'Service';
                var qty = s.quantity || 1;
                var sColor = s.service_color || '';
                if (!sColor && s.service_id && selectServicesData.length > 0) {
                    var svcInfo = selectServicesData.find(function(d) { return d.id === s.service_id; });
                    if (svcInfo && svcInfo.color) sColor = svcInfo.color;
                }
                var colorDot = sColor ? '<span class="inline-block w-2 h-2 rounded-full flex-shrink-0" style="background:' + sColor + '"></span>' : '';
                return '<span class="inline-flex items-center gap-1 text-xs text-gray-500 truncate">' + colorDot + '<span class="truncate">' + sName + (qty > 1 ? ' x' + qty : '') + '</span></span>';
            });
            svcListHtml = '<div class="mt-1 grid grid-cols-4 gap-1">' + svcItems.join('') + '</div>';
        }
        html += '<div class="p-2 bg-white rounded-lg border border-gray-200">'
            + '<div class="flex items-center gap-3">'
            + '<div class="flex-shrink-0">' + avatarHtml + '</div>'
            + '<div class="flex-1 min-w-0">'
            + '<p class="text-sm font-medium text-gray-900 truncate">' + name + '</p>'
            + '<p class="text-xs text-gray-500">Services: ' + serviceCount + '</p>'
            + '</div>'
            + '<button onclick="event.stopPropagation(); openSelectServicesModal(' + currentAppointmentId + ', ' + tech.id + ', \'' + name.replace(/'/g, "\\'") + '\')" class="px-3 py-1.5 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition-all font-medium text-xs flex items-center gap-1 flex-shrink-0">'
            + '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>'
            + 'Select</button>'
            + '</div>'
            + svcListHtml
            + '</div>';
    });
    html += '</div>';
    currentEventModalElement.innerHTML = html;
}

// Update calendar event display
function updateCalendarEventDisplay() {
    if (!currentEvent || !currentAppointmentId) return;
    
    // Find the appointment
    const appointment = bookingsData.find(apt => apt.id.toString() === currentAppointmentId.toString());
    if (!appointment) return;
    
    // Get updated technician names
    let techniciansText = 'Not Assigned';
    const hasTechnician = appointment.assigned_technician && 
                          Array.isArray(appointment.assigned_technician) && 
                          appointment.assigned_technician.length > 0;
    
    if (hasTechnician) {
        const technicianNames = appointment.assigned_technician.map(techId => {
            const tech = techniciansData.find(t => t.id.toString() === techId.toString());
            return tech ? `${tech.firstName} ${tech.lastName}` : `Technician #${techId}`;
        });
        techniciansText = technicianNames.join(', ');
    }
    
    // Update event extended props
    currentEvent.setExtendedProp('technician', techniciansText);
    
    // Determine the first service color
    let firstServiceColor = null;
    if (appointment.services && Array.isArray(appointment.services) && appointment.services.length > 0) {
        for (let si = 0; si < appointment.services.length; si++) {
            let sColor = appointment.services[si].service_color;
            if (!sColor && appointment.services[si].service_id && typeof selectServicesData !== 'undefined' && selectServicesData.length > 0) {
                const svcInfo = selectServicesData.find(d => d.id === appointment.services[si].service_id);
                if (svcInfo && svcInfo.color) sColor = svcInfo.color;
            }
            if (sColor) { firstServiceColor = sColor; break; }
        }
    }

    // Update event styling — no-show: no inline styles, CSS handles it
    const isNoShow = noShowStatus[appointment.id] === true;
    const effectiveColor = firstServiceColor || appointment.color;

    if (isNoShow) {
        currentEvent.setProp('backgroundColor', '');
        currentEvent.setProp('borderColor', '');
        currentEvent.setProp('textColor', '');
    } else if (effectiveColor) {
        currentEvent.setProp('backgroundColor', effectiveColor);
        currentEvent.setProp('borderColor', effectiveColor);
        currentEvent.setProp('textColor', '#ffffff');
    } else {
        currentEvent.setProp('backgroundColor', hasTechnician ? '#003047' : 'transparent');
        currentEvent.setProp('borderColor', '#003047');
        currentEvent.setProp('textColor', hasTechnician ? '#ffffff' : '#003047');
    }

    // Update class names - preserve existing status class and update technician/no-show classes
    const existingClassNames = currentEvent.classNames || [];
    const statusClass = existingClassNames.find(cn => cn.startsWith('event-status-') || ['event-booked', 'event-in-booking', 'event-completed', 'event-in-progress'].includes(cn));
    const classNames = [];
    if (statusClass) classNames.push(statusClass);
    if (!isNoShow && effectiveColor) {
        classNames.push('event-custom-color');
    }
    if (hasTechnician) {
        classNames.push('event-has-technician');
    }
    if (isNoShow) {
        classNames.push('event-no-show');
    }
    currentEvent.setProp('classNames', classNames);

    // Strip inline styles from DOM for no-show
    if (isNoShow) {
        setTimeout(function() {
            var targetEls = document.querySelectorAll('[data-event-id="' + currentAppointmentId + '"]');
            targetEls.forEach(function(el) {
                el.removeAttribute('style');
                var mainEl = el.querySelector('.fc-event-main');
                if (mainEl) mainEl.removeAttribute('style');
            });
        }, 50);
    }
}

// =========================================================================
// Appointment Type Filter Functions
// =========================================================================

/**
 * Filter appointments based on the active tab selection
 */
function getFilteredAppointments(appointments) {
    if (activeAppointmentFilter === 'all') {
        return appointments;
    } else if (activeAppointmentFilter === 'booked') {
        return appointments.filter(apt => apt.appointment === 'booked' || !apt.appointment);
    } else if (activeAppointmentFilter === 'walkin') {
        return appointments.filter(apt => apt.appointment === 'walk-in');
    }
    return appointments;
}

/**
 * Switch between appointment type tabs
 */
function switchAppointmentTab(tabType) {
    activeAppointmentFilter = tabType;

    // Save to localStorage for persistence
    localStorage.setItem('activeAppointmentFilter', tabType);

    // Update tab button styles
    updateAppointmentTabButtons(tabType);

    // Refresh calendar events
    if (calendarInstance) {
        calendarInstance.refetchEvents();
    }

    // Also refresh list view if it's currently visible
    const listViewContainer = document.getElementById('listViewContainer');
    if (listViewContainer && !listViewContainer.classList.contains('hidden')) {
        renderTechnicianListView();
    }
}

/**
 * Update tab button active states
 */
function updateAppointmentTabButtons(activeTab) {
    const tabs = {
        'all': document.getElementById('tabAll'),
        'booked': document.getElementById('tabBooked'),
        'walkin': document.getElementById('tabWalkIn')
    };

    // Remove active class from all tabs
    Object.values(tabs).forEach(tab => {
        if (tab) tab.classList.remove('active');
    });

    // Add active class to the selected tab
    if (tabs[activeTab]) {
        tabs[activeTab].classList.add('active');
    }
}

// --- Select Services Modal ---
var selectServicesData = [];
var selectServicesCategoriesMap = {};
var selectServicesCategory = null;
var selectServicesCart = [];
var selectServicesAppointmentId = null;
var selectServicesTechnicianId = null;
var selectServicesTechnicianName = '';
var selectServicesLoaded = false;
var selectServicesOldServiceCount = 0;

function openSelectServicesModal(appointmentId, technicianId, technicianName) {
    selectServicesAppointmentId = appointmentId;
    selectServicesTechnicianId = technicianId;
    selectServicesTechnicianName = technicianName;
    selectServicesCategory = null;
    selectServicesCart = [];
    selectServicesOldServiceCount = 0;

    // Pre-populate cart from existing appointment services for this technician
    var appointment = bookingsData.find(function(a) { return a.id.toString() === appointmentId.toString(); });
    var existingSvcs = appointment && Array.isArray(appointment.services) ? appointment.services : [];
    var techSvcs = existingSvcs.filter(function(s) { return s.technician_id && s.technician_id.toString() === technicianId.toString(); });

    var modalContent = '<div class="flex flex-col h-[80vh] max-h-[80vh] overflow-hidden">'
        + '<div class="flex-shrink-0 px-6 py-4 border-b border-gray-200 bg-white">'
        + '<div class="flex items-center justify-between">'
        + '<div><h3 class="text-xl font-bold text-gray-900">Select Services</h3>'
        + '<p class="text-sm text-gray-500">' + technicianName + '</p></div>'
        + '<button onclick="closeNestedModal()" class="p-2 -m-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition">'
        + '<svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>'
        + '</button></div></div>'
        + '<div class="flex-1 min-h-0 px-6 py-4 bg-gray-50 overflow-y-auto">'
        + '<div class="mb-4"><div class="relative">'
        + '<svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>'
        + '<input type="text" id="selectServicesSearchInput" placeholder="Search services..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] text-sm" oninput="renderSelectServicesGrid()">'
        + '</div></div>'
        + '<div class="select-svc-carousel-wrapper mb-4"><div id="selectServicesCategoriesList" class="select-svc-slick-carousel"></div></div>'
        + '<div id="selectServicesGrid" class="grid grid-cols-2 sm:grid-cols-4 gap-3"></div>'
        + '</div>'
        + '<div class="flex-shrink-0 px-6 py-4 border-t border-gray-200 bg-white">'
        + '<div id="selectServicesCartSummary" class="mb-3"></div>'
        + '<div class="flex items-center justify-end gap-3">'
        + '<button onclick="closeNestedModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium">Cancel</button>'
        + '<button id="selectServicesSaveBtn" onclick="saveSelectServicesCart()" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium flex items-center gap-2">Save Services</button>'
        + '</div></div></div>';

    openNestedModal(modalContent, 'large-flex', false);

    function populateCartFromExisting() {
        techSvcs.forEach(function(s) {
            var svcData = selectServicesData.find(function(d) { return d.id === s.service_id; });
            var sc = svcData && typeof svcData.service_count === 'number' ? svcData.service_count : 0;
            selectServicesCart.push({
                service_id: s.service_id,
                name: s.service_name || (svcData ? svcData.name : s.service) || 'Service',
                price: s.unit_price != null ? s.unit_price : (svcData ? svcData.price : 0),
                quantity: s.quantity || 1,
                category_slug: (svcData && svcData.categories && svcData.categories[0]) || s.service || '',
                service_count: sc
            });
            selectServicesOldServiceCount += sc * (s.quantity || 1);
        });
    }

    if (selectServicesLoaded) {
        populateCartFromExisting();
        renderSelectServicesCategories();
        renderSelectServicesGrid();
    } else {
        Promise.all([
            fetch(base + '/services').then(function(r) { return r.json(); }),
            fetch(base + '/service-categories').then(function(r) { return r.json(); })
        ]).then(function(results) {
            selectServicesData = (results[0].services || []).filter(function(s) { return s.active !== false; });
            selectServicesCategoriesMap = results[1].categories || results[1] || {};
            selectServicesLoaded = true;
            populateCartFromExisting();
            renderSelectServicesCategories();
            renderSelectServicesGrid();
        }).catch(function(err) {
            console.error('Failed to load services:', err);
        });
    }
}

function renderSelectServicesCategories() {
    var container = document.getElementById('selectServicesCategoriesList');
    if (!container) return;

    // Destroy previous Slick instance if any
    var $c = $(container);
    if ($c.hasClass('slick-initialized')) {
        try { $c.slick('unslick'); } catch (e) {}
    }

    var activeClass = 'bg-[#e6f0f3] border-[#003047] text-[#003047]';
    var inactiveClass = 'bg-white border-gray-200 text-gray-700 hover:border-[#003047] hover:bg-[#e6f0f3] hover:text-[#003047]';
    var html = '<div><button type="button" onclick="selectServicesFilterCategory(null)" class="select-svc-cat-card w-full h-[70px] px-4 py-2 rounded-lg text-sm font-medium border transition-all duration-200 flex items-center justify-center text-center break-words active:scale-95 ' + (selectServicesCategory === null ? activeClass : inactiveClass) + '" data-category-key="all">All Categories</button></div>';
    var sorted = Object.entries(selectServicesCategoriesMap).sort(function(a, b) { return (a[1] || '').localeCompare(b[1] || ''); });
    sorted.forEach(function(entry) {
        var key = entry[0], name = entry[1];
        html += '<div><button type="button" onclick="selectServicesFilterCategory(\'' + key + '\')" class="select-svc-cat-card w-full h-[70px] px-4 py-2 rounded-lg text-sm font-medium border transition-all duration-200 flex items-center justify-center text-center break-words active:scale-95 ' + (selectServicesCategory === key ? activeClass : inactiveClass) + '" data-category-key="' + key + '">' + name + '</button></div>';
    });
    container.innerHTML = html;

    // Initialize Slick
    setTimeout(function() {
        if (typeof $ !== 'undefined' && typeof $.fn.slick !== 'undefined') {
            $c.slick({
                slidesToShow: 6,
                slidesToScroll: 6,
                infinite: false,
                arrows: true,
                dots: false,
                adaptiveHeight: false,
                variableWidth: false,
                responsive: [
                    { breakpoint: 1024, settings: { slidesToShow: 4, slidesToScroll: 4 } },
                    { breakpoint: 640, settings: { slidesToShow: 2, slidesToScroll: 2 } }
                ]
            });
            $c.css({ opacity: '1', visibility: 'visible' });
        } else {
            // Fallback if Slick not available
            container.classList.add('show-fallback');
            container.style.opacity = '1';
            container.style.visibility = 'visible';
        }
    }, 50);
}

window.selectServicesFilterCategory = function(key) {
    selectServicesCategory = key;
    // Update active styling without re-init
    document.querySelectorAll('.select-svc-cat-card').forEach(function(card) {
        var cardKey = card.getAttribute('data-category-key');
        var isActive = (key === null && cardKey === 'all') || (key === cardKey);
        if (isActive) {
            card.classList.remove('bg-white', 'border-gray-200', 'text-gray-700');
            card.classList.add('bg-[#e6f0f3]', 'border-[#003047]', 'text-[#003047]');
        } else {
            card.classList.remove('bg-[#e6f0f3]', 'border-[#003047]', 'text-[#003047]');
            card.classList.add('bg-white', 'border-gray-200', 'text-gray-700');
        }
    });
    renderSelectServicesGrid();
};

function renderSelectServicesGrid() {
    var container = document.getElementById('selectServicesGrid');
    if (!container) return;
    var filtered = selectServicesData;
    if (selectServicesCategory !== null) {
        filtered = filtered.filter(function(s) { return s.categories && s.categories.indexOf(selectServicesCategory) >= 0; });
    }
    var searchInput = document.getElementById('selectServicesSearchInput');
    if (searchInput && searchInput.value.trim() !== '') {
        var term = searchInput.value.toLowerCase();
        filtered = filtered.filter(function(s) { return s.name.toLowerCase().indexOf(term) >= 0; });
    }
    if (filtered.length === 0) {
        container.innerHTML = '<div class="col-span-full text-center py-8"><p class="text-gray-500 text-sm">No services found</p></div>';
        return;
    }
    var html = '';
    filtered.forEach(function(service) {
        var cartItem = selectServicesCart.find(function(c) { return c.service_id === service.id; });
        var imgUrl = service.image_url || null;
        var thumbHtml = imgUrl
            ? '<div class="relative w-full bg-gray-100 rounded-t-lg overflow-hidden" style="height:120px"><img src="' + imgUrl + '" alt="" class="w-full h-full object-cover" onerror="this.parentNode.innerHTML=\'<div class=\\\'w-full h-full flex items-center justify-center bg-gray-100\\\'><svg class=\\\'w-8 h-8 text-gray-300\\\' fill=\\\'none\\\' stroke=\\\'currentColor\\\' viewBox=\\\'0 0 24 24\\\'><path stroke-linecap=\\\'round\\\' stroke-linejoin=\\\'round\\\' stroke-width=\\\'1.5\\\' d=\\\'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z\\\'></path></svg></div>\'"></div>'
            : '<div class="w-full flex items-center justify-center rounded-t-lg" style="height:120px;background:' + (service.color || '#f3f4f6') + '"><svg class="w-8 h-8 ' + (service.color ? 'text-white opacity-50' : 'text-gray-300') + '" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg></div>';
        var colorDot = service.color ? '<span class="inline-block w-2.5 h-2.5 rounded-full flex-shrink-0" style="background:' + service.color + '"></span>' : '';
        html += '<div class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:border-[#003047] hover:shadow-md transition-all flex flex-col">'
            + thumbHtml
            + '<div class="p-3 flex flex-col flex-1">'
            + '<h4 class="text-sm font-semibold text-gray-900 mb-1 flex items-center gap-1.5">' + colorDot + service.name + '</h4>'
            + '<p class="text-sm text-gray-600 mb-2">' + window.salonFormatMoney(service.price) + '</p>'
            + (cartItem
                ? '<div class="flex items-center justify-between mt-auto bg-gray-100 rounded-lg p-1">'
                + '<button onclick="selectServicesUpdateQty(' + service.id + ', -1)" class="w-8 h-8 flex items-center justify-center bg-white text-gray-700 hover:bg-red-50 hover:text-red-600 rounded-md border border-gray-200 shadow-sm transition active:scale-95"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg></button>'
                + '<span class="text-sm font-bold text-gray-900 min-w-[2rem] text-center">' + cartItem.quantity + '</span>'
                + '<button onclick="selectServicesUpdateQty(' + service.id + ', 1)" class="w-8 h-8 flex items-center justify-center bg-white text-gray-700 hover:bg-green-50 hover:text-green-600 rounded-md border border-gray-200 shadow-sm transition active:scale-95"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg></button>'
                + '</div>'
                : '<button onclick="selectServicesAddToCart(' + service.id + ')" class="w-full px-3 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-xs active:scale-95 mt-auto">Add</button>')
            + '</div></div>';
    });
    container.innerHTML = html;
    renderSelectServicesCartSummary();
}

window.selectServicesAddToCart = function(serviceId) {
    var service = selectServicesData.find(function(s) { return s.id === serviceId; });
    if (!service) return;
    var existing = selectServicesCart.find(function(c) { return c.service_id === serviceId; });
    if (existing) {
        existing.quantity += 1;
    } else {
        selectServicesCart.push({
            service_id: serviceId,
            name: service.name,
            price: service.price,
            quantity: 1,
            category_slug: (service.categories && service.categories[0]) || '',
            service_count: typeof service.service_count === 'number' ? service.service_count : 0
        });
    }
    renderSelectServicesGrid();
};

window.selectServicesUpdateQty = function(serviceId, delta) {
    var item = selectServicesCart.find(function(c) { return c.service_id === serviceId; });
    if (!item) return;
    item.quantity += delta;
    if (item.quantity <= 0) {
        selectServicesCart = selectServicesCart.filter(function(c) { return c.service_id !== serviceId; });
    }
    renderSelectServicesGrid();
};

window.selectServicesRemoveFromCart = function(serviceId) {
    selectServicesCart = selectServicesCart.filter(function(c) { return c.service_id !== serviceId; });
    renderSelectServicesGrid();
};

function renderSelectServicesCartSummary() {
    var container = document.getElementById('selectServicesCartSummary');
    if (!container) return;
    if (selectServicesCart.length === 0) {
        container.innerHTML = '<p class="text-sm text-gray-400">No services selected</p>';
        return;
    }
    var total = 0;
    var html = '<div class="overflow-y-auto space-y-1" style="max-height:150px">';
    selectServicesCart.forEach(function(item) {
        var lineTotal = item.price * item.quantity;
        total += lineTotal;
        html += '<div class="flex items-center justify-between text-sm">'
            + '<div class="flex items-center gap-2">'
            + '<button onclick="selectServicesRemoveFromCart(' + item.service_id + ')" class="w-5 h-5 flex items-center justify-center rounded-full bg-red-100 text-red-500 hover:bg-red-200 transition flex-shrink-0"><svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>'
            + '<span class="text-gray-700">' + item.name + ' × ' + item.quantity + '</span>'
            + '</div>'
            + '<span class="font-medium text-gray-900">' + window.salonFormatMoney(lineTotal) + '</span></div>';
    });
    html += '</div>';
    html += '<div class="flex items-center justify-between text-sm font-bold pt-1 border-t border-gray-200 mt-1"><span>Total</span><span>' + window.salonFormatMoney(total) + '</span></div>';
    container.innerHTML = html;
}

window.refreshEventColorSwatches = function(appointmentId) {
    var swatchesEl = document.getElementById('eventColorSwatches_' + appointmentId);
    if (!swatchesEl) return;
    var apt = bookingsData.find(function(a) { return a.id.toString() === appointmentId.toString(); });
    var currentColor = apt ? (apt.color || null) : null;
    var svcs = apt && Array.isArray(apt.services) ? apt.services : [];
    var serviceColors = [];
    var seenColors = {};
    svcs.forEach(function(s) {
        var c = s.service_color;
        var svcInfo = null;
        // Fallback: look up color from selectServicesData if not in response
        if (s.service_id && selectServicesData.length > 0) {
            svcInfo = selectServicesData.find(function(d) { return d.id === s.service_id; });
            if (!c && svcInfo && svcInfo.color) c = svcInfo.color;
        }
        if (c && !seenColors[c.toUpperCase()]) {
            seenColors[c.toUpperCase()] = true;
            var svcCount = svcInfo && typeof svcInfo.service_count === 'number' ? svcInfo.service_count : 0;
            serviceColors.push({hex: c.toUpperCase(), name: (s.service_name || 'Service'), service_count: svcCount});
        }
    });

    var knownHexes = serviceColors.map(function(c) { return c.hex; });
    var colorInServices = currentColor && knownHexes.some(function(p) { return currentColor.toUpperCase() === p; });

    // 1) No services at all → clear color
    if (serviceColors.length === 0) {
        if (currentColor) {
            setEventColor(appointmentId, null, true);
            currentColor = null;
        }
    // 2) Current color not in available service colors → auto-select best
    } else if (!currentColor || !colorInServices) {
        var best = serviceColors.reduce(function(a, b) { return b.service_count > a.service_count ? b : a; }, serviceColors[0]);
        if (best && best.hex) {
            setEventColor(appointmentId, best.hex, true);
            currentColor = best.hex;
        }
    }

    var serviceSwatches = serviceColors.map(function(c) {
        var isSelected = currentColor && currentColor.toUpperCase() === c.hex;
        return '<button onclick="setEventColor(\'' + appointmentId + '\', \'' + c.hex + '\')" class="rounded-full border-2 transition-all hover:scale-110 ' + (isSelected ? 'border-gray-900 ring-2 ring-offset-1 ring-gray-900' : 'border-white shadow-sm') + '" style="width:28px;height:28px;background:' + c.hex + '" title="' + c.name + '"></button>';
    }).join('');
    var isCustom = currentColor && !knownHexes.some(function(p) { return currentColor.toUpperCase() === p; });
    var customBtn = '<div class="relative"><button onclick="document.getElementById(\'customColorPicker_' + appointmentId + '\').click()" class="rounded-full border-2 transition-all hover:scale-110 flex items-center justify-center ' + (isCustom ? 'border-gray-900 ring-2 ring-offset-1 ring-gray-900' : 'border-gray-300') + '" style="width:28px;height:28px;background: conic-gradient(red, yellow, lime, aqua, blue, magenta, red)" title="Custom color"></button><input type="color" id="customColorPicker_' + appointmentId + '" value="' + (currentColor || '#003047') + '" class="absolute opacity-0 w-0 h-0" onchange="setEventColor(\'' + appointmentId + '\', this.value)"></div>';
    swatchesEl.innerHTML = serviceSwatches + customBtn;

    // Update the preview circle
    var previewEl = document.getElementById('eventColorPreview_' + appointmentId);
    if (previewEl) {
        if (currentColor) {
            previewEl.innerHTML = '<div style="width:48px;height:48px;border-radius:9999px;border:2px solid #fff;box-shadow:0 4px 6px -1px rgba(0,0,0,.1);background:' + currentColor + '"></div><button onclick="setEventColor(\'' + appointmentId + '\', null)" style="position:absolute;top:-4px;left:-4px;width:18px;height:18px;border-radius:9999px;background:#fff;border:1px solid #d1d5db;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 1px 2px rgba(0,0,0,.1)" title="Remove color"><svg style="width:10px;height:10px" fill="none" stroke="#9ca3af" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg></button>';
        } else {
            previewEl.innerHTML = '<div style="width:48px;height:48px;border-radius:9999px;border:2px dashed #d1d5db;display:flex;align-items:center;justify-content:center;background:#fff"><svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg></div>';
        }
    }
};

window.saveSelectServicesCart = function() {
    // Show loading state
    var saveBtn = document.getElementById('selectServicesSaveBtn');
    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.classList.add('opacity-50', 'cursor-not-allowed');
        saveBtn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Saving...';
    }

    function resetSaveBtn() {
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            saveBtn.innerHTML = 'Save Services';
        }
    }

    // Build payload: current technician's cart + other technicians' existing services
    var appointment = bookingsData.find(function(a) { return a.id.toString() === selectServicesAppointmentId.toString(); });
    var existingSvcs = appointment && Array.isArray(appointment.services) ? appointment.services : [];

    // Other technicians' services (keep as-is)
    var otherTechSvcs = existingSvcs.filter(function(s) {
        return !s.technician_id || s.technician_id.toString() !== selectServicesTechnicianId.toString();
    }).map(function(s) {
        var svcData = selectServicesData.find(function(d) { return d.id === s.service_id; });
        var slug = (svcData && svcData.categories && svcData.categories[0]) || s.service || '';
        return {
            service: slug,
            service_id: s.service_id,
            technician_id: s.technician_id,
            quantity: s.quantity || 1,
            unit_price: s.unit_price
        };
    });

    // Current technician's services from cart
    var currentTechSvcs = selectServicesCart.map(function(item) {
        return {
            service: item.category_slug,
            service_id: item.service_id,
            technician_id: selectServicesTechnicianId,
            quantity: item.quantity,
            unit_price: item.price
        };
    });

    var servicesPayload = otherTechSvcs.concat(currentTechSvcs).filter(function(s) { return s.service && s.technician_id; });
    var apiUrl = base.replace(/\/data\/?$/, '') + '/appointments/' + selectServicesAppointmentId + '/services';
    if (typeof salonApi !== 'undefined' && salonApi.put) {
        salonApi.put(apiUrl, { services: servicesPayload }).then(function(res) {
            // Update bookingsData with new services from response
            var appointment = bookingsData.find(function(a) { return a.id.toString() === selectServicesAppointmentId.toString(); });
            if (appointment && res && res.data && Array.isArray(res.data.services)) {
                // Enrich response services with color from selectServicesData
                appointment.services = res.data.services.map(function(s) {
                    if (!s.service_color && s.service_id && selectServicesData.length > 0) {
                        var svcInfo = selectServicesData.find(function(d) { return d.id === s.service_id; });
                        if (svcInfo && svcInfo.color) s.service_color = svcInfo.color;
                    }
                    return s;
                });
            }

            // Update turn tracker: subtract old service count, add new
            var tech = techniciansData.find(function(t) { return t.id === selectServicesTechnicianId; });
            var baseServices = tech && typeof tech.services === 'number' ? tech.services : 0;
            var newCartServiceCount = selectServicesCart.reduce(function(sum, item) {
                return sum + ((item.service_count || 0) * item.quantity);
            }, 0);
            var newTotal = baseServices - selectServicesOldServiceCount + newCartServiceCount;
            // Update local data immediately so UI reflects the change
            if (tech) tech.services = newTotal;
            var turnTrackerUrl = base.replace(/\/data\/?$/, '') + '/turn-tracker';
            salonApi.put(turnTrackerUrl, { entries: [{ user_id: selectServicesTechnicianId, services: newTotal }] }).then(function() {
                // Refresh technician display with updated services
                updateEventModalTechnicianDisplay();
            }).catch(function(err) { console.error('Turn tracker update failed:', err); });

            if (typeof showSuccessMessage === 'function') showSuccessMessage('Services saved for ' + selectServicesTechnicianName + '.');
            closeNestedModal();
            // Refresh technician display with updated services list
            updateEventModalTechnicianDisplay();
            // Refresh color swatches with updated service colors
            refreshEventColorSwatches(selectServicesAppointmentId);
            // Re-render list view to update event card colors and service count in header
            var listViewContainer = document.getElementById('listViewContainer');
            if (listViewContainer && !listViewContainer.classList.contains('hidden')) {
                renderTechnicianListView();
            }
            // Update calendar event colors in grid view
            updateCalendarEventDisplay();
        }).catch(function(err) {
            resetSaveBtn();
            if (typeof showErrorMessage === 'function') showErrorMessage(err && err.message ? err.message : 'Failed to save services.');
        });
    }
};

</script>

<style>
/* Select Services Slick Carousel */
.select-svc-carousel-wrapper {
    position: relative;
    padding: 0 40px;
}
.select-svc-slick-carousel {
    height: 70px;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.3s ease-in-out;
}
.select-svc-slick-carousel.slick-initialized {
    opacity: 1;
    visibility: visible;
}
.select-svc-slick-carousel.show-fallback {
    opacity: 1 !important;
    visibility: visible !important;
    display: flex !important;
    flex-wrap: wrap !important;
    gap: 8px !important;
    overflow-x: auto !important;
    height: auto !important;
}
.select-svc-slick-carousel.show-fallback > div {
    flex: 0 0 auto;
    width: calc(16.666% - 7px);
}
.select-svc-slick-carousel .slick-slide {
    margin: 0 4px;
    height: 70px !important;
    display: flex;
    align-items: stretch;
}
.select-svc-slick-carousel .slick-slide > div {
    height: 70px !important;
    width: 100%;
    display: flex;
}
.select-svc-slick-carousel .slick-list {
    margin: 0 -4px;
    height: 70px;
}
.select-svc-slick-carousel .slick-track {
    display: flex !important;
    align-items: stretch;
    height: 70px;
}
.select-svc-cat-card {
    word-wrap: break-word;
    overflow-wrap: break-word;
    hyphens: auto;
    height: 70px !important;
    min-height: 70px;
    max-height: 70px;
}
.select-svc-slick-carousel .slick-prev,
.select-svc-slick-carousel .slick-next {
    width: 32px;
    height: 32px;
    background: white;
    border: 1px solid #d1d5db;
    border-radius: 50%;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    z-index: 10;
}
.select-svc-slick-carousel .slick-prev { left: -40px; }
.select-svc-slick-carousel .slick-next { right: -40px; }
.select-svc-slick-carousel .slick-prev:before,
.select-svc-slick-carousel .slick-next:before {
    content: '';
    display: inline-block;
    width: 16px;
    height: 16px;
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
    opacity: 1;
}
.select-svc-slick-carousel .slick-prev:before {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%234b5563'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M15 19l-7-7 7-7'/%3E%3C/svg%3E");
}
.select-svc-slick-carousel .slick-next:before {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%234b5563'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M9 5l7 7-7 7'/%3E%3C/svg%3E");
}
.select-svc-slick-carousel .slick-prev:hover,
.select-svc-slick-carousel .slick-next:hover {
    background: #f9fafb;
}
.select-svc-slick-carousel .slick-disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Modern FullCalendar Styles */
.fc {
    font-family: 'Poppins', sans-serif;
}

/* View Toggle Buttons */
.view-toggle-btn {
    color: #6b7280;
    background: transparent;
    border: none;
    cursor: pointer;
}

.view-toggle-btn:hover {
    color: #374151;
    background: transparent;
}

.view-toggle-btn.active {
    background: white;
    color: #003047;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

/* Appointment Type Filter Tab Buttons */
.appointment-tab-btn {
    color: #6b7280;
    background: transparent;
    border: none;
    cursor: pointer;
    transition: all 0.2s ease;
}

.appointment-tab-btn:hover {
    color: #374151;
    background: #f3f4f6;
}

.appointment-tab-btn.active {
    background: #003047;
    color: white;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

/* Technician List View Styles */
#technicianListView {
    width: 100%;
}

#technicianListView table {
    width: 100%;
    border-collapse: collapse;
}

#technicianListView thead {
    background: linear-gradient(180deg, #ffffff 0%, #f9fafb 100%);
}

#technicianListView th {
    position: sticky;
    top: 0;
    z-index: 5;
    background: linear-gradient(180deg, #ffffff 0%, #f9fafb 100%);
}

#technicianListView th.sticky {
    position: sticky;
    left: 0;
    z-index: 10;
    background: linear-gradient(180deg, #ffffff 0%, #f9fafb 100%);
}

#technicianListView td.sticky {
    position: sticky;
    left: 0;
    z-index: 10;
    background: white;
}

#technicianListView tr:hover td.sticky {
    background: #f9fafb;
}

#technicianListView tbody tr:hover {
    background: #f9fafb;
}

/* Global cursor styles for dragging */
body.fc-dragging:not(.fc-drag-not-allowed),
body.fc-dragging:not(.fc-drag-not-allowed) * {
    cursor: grabbing !important;
}

/* List view drag and drop styles */
body.dragging-appointment {
    cursor: move !important;
}

.draggable-appointment {
    user-select: none;
    transition: opacity 0.2s, transform 0.2s;
}

.draggable-appointment.dragging {
    opacity: 0.5 !important;
    transform: scale(0.95);
}

.droppable-slot {
    transition: background-color 0.2s, border 0.2s;
    position: relative;
}

.droppable-slot.drag-over {
    background-color: #e6f0f3 !important;
    border: 2px dashed #003047 !important;
}

.droppable-slot.drag-over::after {
    content: 'Drop here';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #003047;
    color: white;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 10px;
    font-weight: bold;
    z-index: 10;
    pointer-events: none;
}

/* Not-allowed cursor when dragging over past dates */
body.fc-drag-not-allowed,
body.fc-drag-not-allowed * {
    cursor: not-allowed !important;
}

/* Make dragged events visible */
.fc-event-dragging,
.fc-event-selected {
    opacity: 0.8 !important;
    transform: scale(1.05) !important;
    z-index: 10000 !important;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3) !important;
    pointer-events: none !important;
}

/* Not allowed state when dragging over past dates */
.fc-event-dragging.fc-drag-not-allowed {
    opacity: 0.5 !important;
    cursor: not-allowed !important;
    filter: grayscale(50%);
}

/* Header Toolbar */
.fc-header-toolbar {
    margin-bottom: 2rem;
    padding: 1rem 0;
    flex-wrap: wrap;
    gap: 1rem;
    border-bottom: 2px solid #f3f4f6;
}

.fc-toolbar-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: #111827;
    letter-spacing: -0.025em;
}

/* Toolbar Chunks - Add spacing between left, center, right sections */
.fc-toolbar-chunk {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.fc-toolbar-chunk:not(:last-child) {
    margin-right: 1rem;
}

/* Button Groups - Add spacing between button groups */
.fc-button-group {
    display: inline-flex;
    gap: 0.5rem !important;
    margin-right: 0.75rem;
}

.fc-button-group:last-child {
    margin-right: 0;
}

/* Individual buttons within groups */
.fc-button-group .fc-button {
    margin: 0 !important;
}

/* Buttons */
.fc-button {
    background: transparent !important;
    border: 1.5px solid #003047 !important;
    color: #003047 !important;
    padding: 0.5rem 1rem !important;
    border-radius: 0.625rem !important;
    font-weight: 600 !important;
    font-size: 0.8125rem !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    box-shadow: none !important;
    text-transform: none !important;
    line-height: 1.25 !important;
}

.fc-button:hover {
    background: #003047 !important;
    color: white !important;
    border-color: #003047 !important;
    transform: translateY(-1px) !important;
    box-shadow: 0 2px 6px rgba(0, 48, 71, 0.2) !important;
}

.fc-button:active {
    transform: translateY(0) !important;
}

.fc-button-active {
    background: #003047 !important;
    color: white !important;
    border-color: #003047 !important;
    box-shadow: 0 2px 6px rgba(0, 48, 71, 0.3) !important;
}

.fc-button-primary:not(:disabled):active,
.fc-button-primary:not(:disabled).fc-button-active {
    background: #003047 !important;
    color: white !important;
    border-color: #003047 !important;
    box-shadow: 0 2px 6px rgba(0, 48, 71, 0.3) !important;
}

.fc-button-primary:disabled {
    opacity: 0.5 !important;
    cursor: not-allowed !important;
}

/* Today Button */
.fc-today-button {
    background: white !important;
    color: #003047 !important;
    border: 2px solid #003047 !important;
    font-weight: 600 !important;
    margin-left: 0.5rem !important;
    margin-right: 0.5rem !important;
    padding: 0.5rem 1rem !important;
    font-size: 0.8125rem !important;
}

.fc-today-button:hover {
    background: #f9fafb !important;
    border-color: #002535 !important;
}

/* Navigation buttons (prev/next) */
.fc-prev-button,
.fc-next-button {
    margin-right: 0.5rem !important;
}

.fc-prev-button:last-child,
.fc-next-button:last-child {
    margin-right: 0 !important;
}

/* Calendar Grid */
.fc-daygrid {
    padding: 0.5rem;
}

.fc-daygrid-day {
    border-color: #e5e7eb !important;
    transition: background-color 0.2s;
    margin: 0.3125rem !important; /* 5px spacing */
    border-radius: 0.5rem;
    padding: 0.25rem !important;
}

.fc-daygrid-day:hover {
    background-color: #f9fafb !important;
}

.fc-daygrid-day-frame {
    min-height: 6rem;
    padding: 0.5rem;
}

.fc-daygrid-day-events {
    margin-top: 0.5rem;
    gap: 0.25rem;
}

.fc-daygrid-day-number {
    color: #374151;
    font-weight: 600;
    font-size: 0.9375rem;
    padding: 0.5rem;
    transition: all 0.2s;
}

.fc-daygrid-day-number:hover {
    color: #003047;
    transform: scale(1.1);
}

.fc-day-today {
    background: linear-gradient(135deg, #e6f0f3 0%, #d1e4e9 100%) !important;
    border-color: #e5e7eb !important;
    border-radius: 0 !important;
}


.fc-day-today .fc-daygrid-day-number {
    color: #003047;
    font-weight: 700;
    background: #003047;
    color: white;
    border-radius: 50%;
    width: 2rem;
    height: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0.25rem;
}

/* Events on current day - Primary color */
.fc-day-today .fc-event {
    border-color: #003047 !important;
    color: #003047 !important;
}

.fc-day-today .fc-event:hover {
    background: #003047 !important;
    color: white !important;
    border-color: #003047 !important;
}

/* Column Headers */
.fc-col-header-cell {
    background: linear-gradient(180deg, #ffffff 0%, #f9fafb 100%);
    padding: 1rem 0;
    border-color: #e5e7eb;
}

.fc-col-header-cell-cushion {
    color: #374151;
    font-weight: 700;
    font-size: 0.875rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* Events */
.fc-event {
    border-radius: 0.5rem !important;
    padding: 0.25rem 0.5rem !important;
    font-size: 0.75rem !important;
    font-weight: 600 !important;
    cursor: pointer !important;
    border: 1.5px solid #003047 !important;
    background: transparent !important;
    color: #003047 !important;
    box-shadow: none !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    margin: 1px 0 !important;
    overflow: hidden !important;
    position: relative !important;
    line-height: 1.3 !important;
}

/* Override transparent background for no-show events */
.fc-event.event-no-show {
    background: #9ca3af !important;
    background-color: #9ca3af !important;
}

.fc-event:active {
    cursor: grabbing !important;
}

.fc-event::before {
    display: none;
}

.fc-event:not(.event-custom-color):hover {
    background: #003047 !important;
    color: white !important;
    border-color: #003047 !important;
    transform: translateY(-2px) scale(1.02) !important;
    box-shadow: 0 2px 8px rgba(0, 48, 71, 0.3) !important;
    z-index: 10 !important;
    cursor: pointer !important;
}

.fc-event.event-custom-color:hover {
    transform: translateY(-2px) scale(1.02) !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3) !important;
    z-index: 10 !important;
    cursor: pointer !important;
    filter: brightness(0.85) !important;
}

/* Dragging state */
.fc-event.fc-event-selected,
.fc-event-dragging {
    cursor: grabbing !important;
    opacity: 0.8 !important;
    transform: scale(1.05) !important;
    z-index: 10000 !important;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3) !important;
}

.fc-event:not(.event-custom-color):hover .fc-event-title {
    color: white !important;
}

.fc-event:not(.event-custom-color):hover .fc-event-time {
    color: white !important;
}

.fc-event-time {
    color: inherit;
}

.fc-event-title {
    font-weight: 600;
    line-height: 1.3;
    font-size: 0.75rem !important;
}

/* Event Status Colors - Outline Primary Style */
.event-in-booking,
.event-in-progress,
.event-booked,
.event-completed {
    border-color: #003047 !important;
}

/* No Show Events - Grayed Out */
.event-no-show {
    background-color: #9ca3af !important;
    background: #9ca3af !important;
    border-color: #6b7280 !important;
    color: #003047 !important;
    opacity: 0.7 !important;
    filter: grayscale(100%) !important;
}

.event-no-show:hover {
    background-color: #6b7280 !important;
    border-color: #4b5563 !important;
    opacity: 0.8 !important;
    filter: grayscale(100%) !important;
    color: #003047 !important;
}

/* Override other event styles when marked as no show */
.event-no-show.event-has-technician {
    background-color: #9ca3af !important;
    border-color: #6b7280 !important;
    color: #003047 !important;
}

.event-no-show.event-has-technician:hover {
    background-color: #6b7280 !important;
    border-color: #4b5563 !important;
    color: #003047 !important;
}

/* Events with assigned technicians - blue background with white text */
.event-has-technician {
    background: #003047 !important;
    color: #ffffff !important;
    border-color: #003047 !important;
}

/* Events without assigned technicians - transparent background */
.event-in-booking:not(.event-has-technician):not(.event-no-show),
.event-in-progress:not(.event-has-technician):not(.event-no-show),
.event-booked:not(.event-has-technician):not(.event-no-show),
.event-completed:not(.event-has-technician):not(.event-no-show) {
    background: transparent !important;
    color: #003047 !important;
}

.event-in-booking:hover,
.event-in-progress:hover,
.event-booked:hover,
.event-completed:hover {
    border-color: #003047 !important;
}

/* Hover state for events with technicians - darker blue */
.event-has-technician:hover {
    background: #002535 !important;
    color: #ffffff !important;
    border-color: #002535 !important;
}

/* Hover state for events without technicians - blue background */
.event-in-booking:not(.event-has-technician):hover,
.event-in-progress:not(.event-has-technician):hover,
.event-booked:not(.event-has-technician):hover,
.event-completed:not(.event-has-technician):hover {
    background: #003047 !important;
    color: white !important;
    border-color: #003047 !important;
}

/* Custom color events - override all status styling */
.fc-event.event-custom-color,
.fc-event.event-custom-color.event-has-technician,
.fc-event.event-custom-color.event-no-show,
.fc-event.event-custom-color.event-in-booking,
.fc-event.event-custom-color.event-in-progress,
.fc-event.event-custom-color.event-booked,
.fc-event.event-custom-color.event-completed {
    opacity: 1 !important;
    filter: none !important;
}

.fc-event.event-custom-color:hover {
    filter: brightness(0.85) !important;
    opacity: 1 !important;
    color: #ffffff !important;
}

/* Clock icon rotation animation */
@keyframes rotate-clock {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.rotating-clock {
    animation: rotate-clock 2s linear infinite;
    display: inline-block;
}

/* More Events Link */
.fc-more-link {
    font-weight: 600;
    color: #003047 !important;
    text-decoration: none !important;
    padding: 0.25rem 0.5rem;
    border-radius: 0.5rem;
    transition: all 0.2s;
}

.fc-more-link:hover {
    background: #e6f0f3;
    color: #002535 !important;
}

/* Time Grid (Week/Day View) */
.fc-timegrid-slot {
    border-color: #f3f4f6 !important;
    height: 3rem !important;
}

/* Gray out past time slots in week/day view */
.fc-timegrid-slot-past {
    background-color: #f9fafb !important;
    opacity: 0.5 !important;
}

.fc-timegrid-col.fc-day-past {
    background-color: #f9fafb !important;
    opacity: 0.5 !important;
}

.fc-timegrid-col.fc-day-past .fc-timegrid-slot {
    opacity: 0.5 !important;
}

.fc-timegrid-slot-label {
    color: #6b7280;
    font-size: 0.75rem;
    font-weight: 600;
}

.fc-timegrid-now-indicator-line {
    border-color: #003047;
    border-width: 2px;
}

.fc-timegrid-now-indicator-arrow {
    border-color: #003047;
}

.fc-timegrid-event {
    border-radius: 0.75rem !important;
    padding: 0.5rem !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
    cursor: pointer !important;
}

.fc-timegrid-event:hover {
    cursor: pointer !important;
}

.fc-timegrid-event:active,
.fc-timegrid-event.fc-event-dragging {
    cursor: grabbing !important;
}

/* List View */
.fc-list-event {
    padding: 1rem !important;
    border-radius: 0.75rem !important;
    margin-bottom: 0.5rem !important;
    transition: all 0.2s !important;
    cursor: pointer !important;
}

.fc-list-event:active,
.fc-list-event.fc-event-dragging {
    cursor: grabbing !important;
}

.fc-list-event:hover {
    transform: translateX(4px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
    background: #f9fafb !important;
}

.fc-list-event:hover .fc-list-event-title,
.fc-list-event:hover .fc-list-event-time,
.fc-list-event:hover * {
    color: #003047 !important;
}

.fc-list-event-title {
    font-weight: 600;
}

/* Scrollbar Styling */
.fc-scroller::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

.fc-scroller::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}

.fc-scroller::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.fc-scroller::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Popover */
.fc-popover {
    border-radius: 1rem !important;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15) !important;
    border: 1px solid #e5e7eb !important;
    overflow: hidden;
}

.fc-popover-header {
    background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
    padding: 1rem;
    border-bottom: 1px solid #e5e7eb;
}

.fc-popover-body {
    padding: 0.75rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .fc-header-toolbar {
        flex-direction: column;
        align-items: stretch;
        gap: 0.75rem;
    }
    
    .fc-toolbar-chunk {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .fc-toolbar-title {
        font-size: 1.25rem;
    }
    
    .fc-button-group {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem !important;
        width: 100%;
        margin-right: 0 !important;
    }
    
    .fc-button {
        width: 100%;
        font-size: 0.6875rem !important;
        padding: 0.4375rem 0.625rem !important;
        margin: 0 !important;
    }
    
    .fc-toolbar-chunk {
        gap: 0.5rem;
        margin-right: 0 !important;
    }
    
    .fc-today-button {
        margin-left: 0 !important;
        margin-right: 0 !important;
    }
    
    .fc-daygrid-day-number {
        font-size: 0.875rem;
    }
    
    .fc-event {
        font-size: 0.6875rem !important;
        padding: 0.25rem 0.375rem !important;
    }
    
    .fc-col-header-cell-cushion {
        font-size: 0.75rem;
    }
}

@media (max-width: 640px) {
    .fc-toolbar-chunk {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .fc-button-group {
        grid-template-columns: 1fr;
    }
}
</style>

@endpush
@endsection

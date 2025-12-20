<?php
$pageTitle = 'Calendar';
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/modal.php';
?>

<!-- Main Content Area -->
<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="mb-4">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1">Booking Calendar</h1>
                <p class="text-gray-500 text-sm"><?php echo date('l, F d, Y'); ?></p>
            </div>
        </div>

        <!-- Calendar Container -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div id="calendar" class="w-full p-4 sm:p-6"></div>
        </div>
    </div>
</main>

<!-- FullCalendar CSS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css' rel='stylesheet' />

<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js'></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    
    // Get view from URL parameter
    var urlParams = new URLSearchParams(window.location.search);
    var viewParam = urlParams.get('view');
    
    // Map URL view names to FullCalendar view names
    var viewMap = {
        'month': 'dayGridMonth',
        'week': 'timeGridWeek',
        'day': 'timeGridDay',
        'list': 'listWeek'
    };
    
    // Get initial view from URL or default to month
    var initialView = viewMap[viewParam] || 'dayGridMonth';
    
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: initialView,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        height: 'auto',
        events: [
            {
                title: 'Sarah Johnson',
                start: new Date().toISOString().split('T')[0] + 'T10:00:00',
                end: new Date().toISOString().split('T')[0] + 'T11:00:00',
                backgroundColor: 'transparent',
                borderColor: '#003047',
                textColor: '#003047',
                classNames: ['event-in-booking'],
                extendedProps: {
                    customer: 'Sarah Johnson',
                    service: 'Classic Manicure',
                    technician: 'Not Assigned',
                    price: '$35.00',
                    status: 'In Booking',
                    phone: '+1 (555) 123-4567'
                }
            },
            {
                title: 'Emily Chen',
                start: new Date().toISOString().split('T')[0] + 'T14:00:00',
                end: new Date().toISOString().split('T')[0] + 'T15:00:00',
                backgroundColor: 'transparent',
                borderColor: '#003047',
                textColor: '#003047',
                classNames: ['event-in-progress'],
                extendedProps: {
                    customer: 'Emily Chen',
                    service: 'Gel Polish',
                    technician: 'Jessica Martinez',
                    price: '$20.00',
                    status: 'In Progress',
                    phone: '+1 (555) 234-5678'
                }
            },
            {
                title: 'Jessica Martinez',
                start: new Date(Date.now() + 86400000).toISOString().split('T')[0] + 'T09:00:00',
                end: new Date(Date.now() + 86400000).toISOString().split('T')[0] + 'T11:00:00',
                backgroundColor: 'transparent',
                borderColor: '#003047',
                textColor: '#003047',
                classNames: ['event-booked'],
                extendedProps: {
                    customer: 'Jessica Martinez',
                    service: 'Full Set',
                    technician: 'Not Assigned',
                    price: '$45.00',
                    status: 'Booked',
                    phone: '+1 (555) 345-6789'
                }
            },
            {
                title: 'Michael Brown',
                start: new Date(Date.now() + 86400000).toISOString().split('T')[0] + 'T13:00:00',
                end: new Date(Date.now() + 86400000).toISOString().split('T')[0] + 'T14:00:00',
                backgroundColor: 'transparent',
                borderColor: '#003047',
                textColor: '#003047',
                classNames: ['event-completed'],
                extendedProps: {
                    customer: 'Michael Brown',
                    service: 'Pedicure',
                    technician: 'Sarah Johnson',
                    price: '$30.00',
                    status: 'Completed',
                    phone: '+1 (555) 456-7890'
                }
            },
            {
                title: 'David Wilson',
                start: new Date(Date.now() + 172800000).toISOString().split('T')[0] + 'T10:00:00',
                end: new Date(Date.now() + 172800000).toISOString().split('T')[0] + 'T12:00:00',
                backgroundColor: 'transparent',
                borderColor: '#003047',
                textColor: '#003047',
                classNames: ['event-in-booking'],
                extendedProps: {
                    customer: 'David Wilson',
                    service: 'Manicure & Pedicure',
                    technician: 'Not Assigned',
                    price: '$65.00',
                    status: 'Booked',
                    phone: '+1 (555) 567-8901'
                }
            },
            {
                title: 'Lisa Anderson',
                start: new Date(new Date().getFullYear(), 10, 29).toISOString().split('T')[0] + 'T09:00:00',
                end: new Date(new Date().getFullYear(), 10, 29).toISOString().split('T')[0] + 'T10:30:00',
                backgroundColor: 'transparent',
                borderColor: '#003047',
                textColor: '#003047',
                classNames: ['event-in-booking'],
                extendedProps: {
                    customer: 'Lisa Anderson',
                    service: 'Classic Manicure',
                    technician: 'Not Assigned',
                    price: '$35.00',
                    status: 'Booked',
                    phone: '+1 (555) 678-9012'
                }
            },
            {
                title: 'Robert Taylor',
                start: new Date(new Date().getFullYear(), 10, 29).toISOString().split('T')[0] + 'T11:00:00',
                end: new Date(new Date().getFullYear(), 10, 29).toISOString().split('T')[0] + 'T12:00:00',
                backgroundColor: 'transparent',
                borderColor: '#003047',
                textColor: '#003047',
                classNames: ['event-booked'],
                extendedProps: {
                    customer: 'Robert Taylor',
                    service: 'Gel Polish',
                    technician: 'Jessica Martinez',
                    price: '$20.00',
                    status: 'Booked',
                    phone: '+1 (555) 789-0123'
                }
            },
            {
                title: 'Amanda White',
                start: new Date(new Date().getFullYear(), 10, 29).toISOString().split('T')[0] + 'T13:00:00',
                end: new Date(new Date().getFullYear(), 10, 29).toISOString().split('T')[0] + 'T14:30:00',
                backgroundColor: 'transparent',
                borderColor: '#003047',
                textColor: '#003047',
                classNames: ['event-in-progress'],
                extendedProps: {
                    customer: 'Amanda White',
                    service: 'Full Set & Pedicure',
                    technician: 'Sarah Johnson',
                    price: '$75.00',
                    status: 'In Progress',
                    phone: '+1 (555) 890-1234'
                }
            },
            {
                title: 'James Davis',
                start: new Date(new Date().getFullYear(), 10, 29).toISOString().split('T')[0] + 'T15:00:00',
                end: new Date(new Date().getFullYear(), 10, 29).toISOString().split('T')[0] + 'T16:00:00',
                backgroundColor: 'transparent',
                borderColor: '#003047',
                textColor: '#003047',
                classNames: ['event-booked'],
                extendedProps: {
                    customer: 'James Davis',
                    service: 'Pedicure',
                    technician: 'Not Assigned',
                    price: '$30.00',
                    status: 'Booked',
                    phone: '+1 (555) 901-2345'
                }
            },
            {
                title: 'Maria Garcia',
                start: new Date(new Date().getFullYear(), 10, 29).toISOString().split('T')[0] + 'T16:30:00',
                end: new Date(new Date().getFullYear(), 10, 29).toISOString().split('T')[0] + 'T17:30:00',
                backgroundColor: 'transparent',
                borderColor: '#003047',
                textColor: '#003047',
                classNames: ['event-in-booking'],
                extendedProps: {
                    customer: 'Maria Garcia',
                    service: 'Gel Polish & Manicure',
                    technician: 'Jessica Martinez',
                    price: '$55.00',
                    status: 'Booked',
                    phone: '+1 (555) 012-3456'
                }
            }
        ],
        eventClick: function(info) {
            const event = info.event;
            const extendedProps = event.extendedProps;
            
            const statusColors = {
                'In Booking': 'bg-blue-50 text-blue-700 border-blue-200',
                'In Progress': 'bg-[#e6f0f3] text-[#003047] border-[#003047]',
                'Booked': 'bg-amber-50 text-amber-700 border-amber-200',
                'Completed': 'bg-green-50 text-green-700 border-green-200'
            };
            
            // Create modern modal content
            const modalContent = `
                <div class="p-6">
                    <div class="flex items-start justify-between mb-6">
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-1">${extendedProps.customer}</h3>
                            <p class="text-gray-500 text-sm">${event.start.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</p>
                        </div>
                        <span class="px-3 py-1.5 rounded-lg text-xs font-semibold border ${statusColors[extendedProps.status] || 'bg-gray-50 text-gray-700 border-gray-200'}">
                            ${extendedProps.status}
                        </span>
                    </div>
                    
                    <div class="space-y-4 mb-6">
                        <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-[#003047] rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500 mb-0.5">Service</p>
                                <p class="font-semibold text-gray-900">${extendedProps.service}</p>
                            </div>
                        </div>
                        
                        <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500 mb-0.5">Technician</p>
                                <p class="font-semibold text-gray-900">${extendedProps.technician}</p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 bg-gray-50 rounded-xl">
                                <p class="text-xs text-gray-500 mb-1">Time</p>
                                <p class="font-semibold text-gray-900">${event.start.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })} - ${event.end.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}</p>
                            </div>
                            <div class="p-4 bg-gray-50 rounded-xl">
                                <p class="text-xs text-gray-500 mb-1">Price</p>
                                <p class="font-semibold text-gray-900 text-lg">${extendedProps.price}</p>
                            </div>
                        </div>
                        
                        ${extendedProps.phone ? `
                        <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
                            <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-xs text-gray-500 mb-0.5">Contact</p>
                                <p class="font-semibold text-gray-900">${extendedProps.phone}</p>
                            </div>
                        </div>
                        ` : ''}
                    </div>
                    
                    <div class="flex gap-3 pt-4 border-t border-gray-200">
                        <button onclick="closeModal()" class="flex-1 px-4 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all font-medium flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Close
                        </button>
                        <button onclick="editBooking('${event.id}')" class="flex-1 px-4 py-3 bg-[#003047] text-white rounded-xl hover:bg-[#002535] transition-all font-medium flex items-center justify-center gap-2 shadow-md hover:shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit Booking
                        </button>
                    </div>
                </div>
            `;
            
            // Show modal
            if (typeof openModal === 'function') {
                openModal(modalContent, 'default');
            } else {
                alert(`Customer: ${extendedProps.customer}\nService: ${extendedProps.service}\nTechnician: ${extendedProps.technician}\nPrice: ${extendedProps.price}\nStatus: ${extendedProps.status}`);
            }
        },
        eventMouseEnter: function(info) {
            info.el.style.cursor = 'pointer';
        },
        dayMaxEvents: 3,
        moreLinkClick: 'popover',
        eventDisplay: 'block',
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            meridiem: 'short'
        },
        slotMinTime: '08:00:00',
        slotMaxTime: '20:00:00',
        businessHours: {
            daysOfWeek: [1, 2, 3, 4, 5, 6],
            startTime: '09:00',
            endTime: '18:00',
        },
        nowIndicator: true,
        editable: false,
        selectable: true,
        selectMirror: true,
        weekends: true,
        locale: 'en',
        viewDidMount: function(view) {
            // Update URL when view changes
            var viewNameMap = {
                'dayGridMonth': 'month',
                'timeGridWeek': 'week',
                'timeGridDay': 'day',
                'listWeek': 'list'
            };
            
            var viewName = viewNameMap[view.view.type] || 'month';
            var url = new URL(window.location);
            url.searchParams.set('view', viewName);
            window.history.pushState({}, '', url);
        }
    });
    
    calendar.render();
    
    // Make calendar responsive
    window.addEventListener('resize', function() {
        calendar.updateSize();
    });
});

function openNewBookingModal() {
    // Redirect to booking page or open new booking modal
    window.location.href = '../booking/';
}

function editBooking(eventId) {
    // Handle edit booking action
    console.log('Edit booking:', eventId);
    if (typeof closeModal === 'function') {
        closeModal();
    }
}
</script>

<style>
/* Modern FullCalendar Styles */
.fc {
    font-family: 'Poppins', sans-serif;
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

.fc-event::before {
    display: none;
}

.fc-event:hover {
    background: #003047 !important;
    color: white !important;
    border-color: #003047 !important;
    transform: translateY(-2px) scale(1.02) !important;
    box-shadow: 0 2px 8px rgba(0, 48, 71, 0.3) !important;
    z-index: 10 !important;
}

.fc-event:hover .fc-event-title {
    color: white !important;
}

.fc-event:hover .fc-event-time {
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
    background: transparent !important;
    border-color: #003047 !important;
    color: #003047 !important;
}

.event-in-booking:hover,
.event-in-progress:hover,
.event-booked:hover,
.event-completed:hover {
    background: #003047 !important;
    border-color: #003047 !important;
    color: white !important;
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
}

/* List View */
.fc-list-event {
    padding: 1rem !important;
    border-radius: 0.75rem !important;
    margin-bottom: 0.5rem !important;
    transition: all 0.2s !important;
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

<?php include '../includes/footer.php'; ?>


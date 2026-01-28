<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get current role (default to 'admin')
$current_role = isset($_SESSION['selected_role']) ? $_SESSION['selected_role'] : 'admin';

$pageTitle = 'Calendar';
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/modal.php';
?>

<script>
    // Pass PHP role to JavaScript
    const currentUserRole = '<?php echo $current_role; ?>';
    const isTechnician = currentUserRole === 'technician';
</script>

<!-- Main Content Area -->
<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1">Calendar</h1>
                    <p class="text-gray-500 text-sm"><?php echo date('l, F d, Y'); ?></p>
                </div>
                <div class="flex items-center gap-4">
                    <!-- Legend Badges -->
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
                            <div class="w-4 h-4 rounded-full bg-[#9ca3af] border-2 border-[#9ca3af]"></div>
                            <span class="text-xs text-gray-600 font-medium">No Show</span>
                        </div>
                    </div>
                    <!-- Grid and List View Toggle -->
                    <div class="inline-flex items-center bg-gray-100 rounded-lg p-1">
                        <button id="gridViewBtn" onclick="switchView('grid')" class="px-4 py-2 rounded-md transition-all flex items-center gap-2 view-toggle-btn active">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                            </svg>
                        </button>
                        <button id="listViewBtn" onclick="switchView('list')" class="px-4 py-2 rounded-md transition-all flex items-center gap-2 view-toggle-btn">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>
                    <a href="booking.php" class="px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium text-sm sm:text-base flex items-center gap-2 active:scale-95">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <span>New Booking</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Calendar Container -->
        <div id="calendarContainer" class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
            <div id="calendar" class="w-full p-4 sm:p-6"></div>
        </div>

        <!-- Custom List View Container -->
        <div id="listViewContainer" class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden hidden">
            <div class="w-full">
                <div id="technicianListView" class="w-full">
                    <!-- List view will be rendered here -->
                </div>
            </div>
        </div>
    </div>
</main>

<!-- FullCalendar CSS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.css' rel='stylesheet' />

<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.5/main.min.js'></script>

<script>
// Store bookings and customers data
let bookingsData = [];
let customersData = [];
let techniciansData = [];
let selectedListViewDate = new Date(); // Default to today
let noShowStatus = {}; // Track no show status for appointments { appointmentId: true/false }
let selectedTechnicianIds = []; // Selected technician IDs for appointment
let currentAppointmentId = null; // Current appointment ID being edited
let technicianSearchTerm = ''; // Search term for technician search
let currentEvent = null; // Current FullCalendar event being edited
let currentEventModalElement = null; // Reference to the technician display element in the event modal

// Fetch customers to match phone numbers
async function fetchCustomers() {
    try {
        const response = await fetch('../json/customers.json');
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
        const response = await fetch('../json/users.json');
        const data = await response.json();
        techniciansData = data.users.filter(user => user.role === 'technician' && user.status === 'active');
    } catch (error) {
        console.error('Error fetching technicians:', error);
        techniciansData = [];
    }
}

// Fetch appointments from JSON
async function fetchBookings() {
    try {
        // Fetch appointments, customers, and technicians in parallel
        await Promise.all([fetchCustomers(), fetchTechnicians()]);
        
        const response = await fetch('../json/appointments.json');
        const data = await response.json();
        bookingsData = data.appointments || [];
        
        return convertAppointmentsToEvents(bookingsData);
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
            'paid': { class: 'event-completed', display: 'Paid' }
        };
        
        const statusInfo = statusMap[appointment.status] || { class: 'event-booked', display: 'Booked' };
        
        // Get customer name from customers data
        const customer = customersData.find(c => c.id.toString() === appointment.customer_id.toString());
        const customerName = customer ? `${customer.firstName} ${customer.lastName}` : `Customer #${appointment.customer_id}`;
        const customerPhone = customer ? (customer.phone || 'No phone') : 'No phone';
        
        // Parse appointment datetime - use appointment_datetime if available, otherwise use created_at
        let appointmentDateTime = appointment.appointment_datetime || appointment.created_at;
        
        // Parse the datetime correctly as local time
        let startDate;
        if (appointmentDateTime) {
            // If it doesn't have time, add default time
            if (appointmentDateTime.indexOf('T') === -1) {
                appointmentDateTime = appointmentDateTime + 'T10:00:00';
            }
            // Parse as local time if no timezone specified
            // ISO format without timezone (e.g., "2025-12-01T10:30:00") should be treated as local time
            const dateStr = appointmentDateTime;
            if (dateStr.indexOf('Z') === -1 && !dateStr.match(/[+-]\d{2}:\d{2}$/)) {
                // No timezone, parse components and create local date
                const [datePart, timePart] = dateStr.split('T');
                const [year, month, day] = datePart.split('-').map(Number);
                const timeComponents = (timePart || '00:00:00').split(':');
                const [hours, minutes] = timeComponents.map(Number);
                const seconds = timeComponents[2] ? parseInt(timeComponents[2]) : 0;
                startDate = new Date(year, month - 1, day, hours, minutes, seconds);
            } else {
                // Has timezone, parse normally
                startDate = new Date(dateStr);
            }
        } else {
            // Fallback to current date/time
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
        
        // Format price - not available in appointments, set to $0.00
        const price = '$0.00';
        
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
        
        // Apply gray styling if no show
        let eventBgColor = hasTechnician ? '#003047' : 'transparent';
        let eventBorderColor = '#003047';
        let eventTextColor = hasTechnician ? '#ffffff' : '#003047';
        
        if (isNoShow) {
            eventBgColor = '#9ca3af';
            eventBorderColor = '#6b7280';
            eventTextColor = '#003047';
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
                bookingId: appointment.id,
                bookingType: appointment.appointment || 'booked',
                originalStatus: appointment.status,
                hasTechnician: hasTechnician,
                isNoShow: isNoShow
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
        selectedListViewDate = new Date(dateParam);
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
    
    // Handle initial view - show calendar or list view
    const calendarContainer = document.getElementById('calendarContainer');
    const listViewContainer = document.getElementById('listViewContainer');
    
    // Fetch bookings and convert to events
    const events = await fetchBookings();
    
    if (viewParam === 'list') {
        if (calendarContainer) calendarContainer.classList.add('hidden');
        if (listViewContainer) listViewContainer.classList.remove('hidden');
        updateViewButtons('list');
        // Render list view after data is loaded
        renderTechnicianListView();
    } else {
        if (calendarContainer) calendarContainer.classList.remove('hidden');
        if (listViewContainer) listViewContainer.classList.add('hidden');
        updateViewButtons('grid');
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
        events: events,
        eventClick: function(info) {
            const event = info.event;
            const extendedProps = event.extendedProps;
            
            // Store event reference for technician selection
            currentEvent = event;
            
            // Find the actual appointment to get full data
            const appointment = bookingsData.find(a => a.id.toString() === event.id.toString());
            
            // Build appointment data object from event
            const appointmentData = {
                id: event.id,
                customer: extendedProps.customer,
                technician: extendedProps.technician,
                phone: extendedProps.phone,
                status: extendedProps.status,
                date: event.start,
                isNoShow: extendedProps.isNoShow || noShowStatus[event.id] || false,
                assigned_technician: appointment ? appointment.assigned_technician : null
            };
            
            // Use shared modal function
            showAppointmentModal(appointmentData);
        },
        eventMouseEnter: function(info) {
            info.el.style.cursor = 'pointer';
        },
        eventDragStart: function(info) {
            // Add class to body for global cursor styling
            document.body.classList.add('fc-dragging');
            // Make the dragged event more visible
            info.el.classList.add('fc-event-dragging');
        },
        eventDrag: function(info) {
            // Get the date/time being hovered over during drag
            const draggedEvent = info.event;
            const newStart = draggedEvent.start;
            
            // Get current date and time
            const now = new Date();
            
            // Get today's date (start of day, no time) for month view
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            // Get the new event date (start of day, no time)
            const newDate = new Date(newStart);
            newDate.setHours(0, 0, 0, 0);
            
            // Get current view type
            const currentView = info.view.type;
            
            // For week and day views, check both date and time
            let isPast = false;
            if (currentView === 'timeGridWeek' || currentView === 'timeGridDay') {
                // Check if the new start time is in the past
                if (newStart < now) {
                    isPast = true;
                }
            } else {
                // For month/list views, only check the date
                if (newDate < today) {
                    isPast = true;
                }
            }
            
            // Show not-allowed cursor if past
            if (isPast) {
                document.body.classList.add('fc-drag-not-allowed');
                info.el.classList.add('fc-drag-not-allowed');
            } else {
                document.body.classList.remove('fc-drag-not-allowed');
                info.el.classList.remove('fc-drag-not-allowed');
            }
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
            
            // Get current date and time
            const now = new Date();
            
            // Get today's date (start of day, no time) for month view
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            // Get the new event date (start of day, no time)
            const newDate = new Date(newStart);
            newDate.setHours(0, 0, 0, 0);
            
            // Get current view type
            const currentView = info.view.type;
            
            // Check if the drop is valid based on view type
            let isValid = true;
            let errorMessage = '';
            
            if (currentView === 'timeGridWeek' || currentView === 'timeGridDay') {
                // For week and day views, check if the new start time is in the past
                if (newStart < now) {
                    isValid = false;
                    errorMessage = 'Cannot move events to past times';
                }
            } else {
                // For month/list views, only check if the date is in the past
                if (newDate < today) {
                    isValid = false;
                    errorMessage = 'Cannot move events to past dates';
                }
            }
            
            // If invalid, revert the drop
            if (!isValid) {
                info.revert();
                showErrorMessage(errorMessage);
                return;
            }
            
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
                console.log('Appointment updated:', {
                    id: appointmentId,
                    newDateTime: appointment.appointment_datetime,
                    appointment: appointment
                });
                
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
        slotMinTime: '08:00:00',
        slotMaxTime: '20:00:00',
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
            
            // Update button states based on current view (only for calendar views)
            if (view.view.type === 'dayGridMonth') {
                updateViewButtons('grid');
            }
            
            // Gray out past days
            grayOutPastDays();
        },
        datesSet: function(dateInfo) {
            // Update URL with month when arrows are clicked (not on initial load)
            if (!isInitialLoad) {
                const calendarContainer = document.getElementById('calendarContainer');
                if (calendarContainer && !calendarContainer.classList.contains('hidden')) {
                    const currentView = dateInfo.view.type;
                    
                    // Only update URL for month view when arrows are clicked
                    if (currentView === 'dayGridMonth') {
                        // Get the month being displayed - use the start of the visible month
                        const currentDate = new Date(dateInfo.start);
                        const monthNames = ['january', 'february', 'march', 'april', 'may', 'june', 
                                          'july', 'august', 'september', 'october', 'november', 'december'];
                        // Get month index (0-11) - dateInfo.start might be from previous month's week
                        // So we get the actual month from the view's current date
                        // For month view, we need to find the first day of the actual month being displayed
                        // Get the month from the calendar's current date
                        let monthIndex = currentDate.getMonth();
                        // Add +1 to fix month index issue as requested
                        monthIndex = (monthIndex + 1) % 12;
                        const monthName = monthNames[monthIndex].toLowerCase();
                        const year = currentDate.getFullYear();
                        
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
            setTimeout(function() {
                grayOutPastDays();
            }, 100);
        },
        dayCellClassNames: function(info) {
            // Automatically add class to past days and past time slots
            const now = new Date();
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            const dayDate = new Date(info.date);
            const dayDateOnly = new Date(dayDate);
            dayDateOnly.setHours(0, 0, 0, 0);
            
            // Get current view type
            const currentView = info.view.type;
            
            // For week/day views, check if the day/time is in the past
            if (currentView === 'timeGridWeek' || currentView === 'timeGridDay') {
                // For time grid views, check if the entire day or time is past
                if (dayDateOnly < today || dayDate < now) {
                    return ['fc-day-past'];
                }
            } else {
                // For month/list views, only check the date
                if (dayDateOnly < today) {
                    return ['fc-day-past'];
                }
            }
            
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
        const currentDateString = selectedListViewDate.toISOString().split('T')[0];
        url.searchParams.set('date', currentDateString);
        window.history.pushState({}, '', url);
    }
}

// Update button active states
function updateViewButtons(activeView) {
    const gridBtn = document.getElementById('gridViewBtn');
    const listBtn = document.getElementById('listViewBtn');
    
    if (gridBtn && listBtn) {
        if (activeView === 'grid') {
            gridBtn.classList.add('active');
            listBtn.classList.remove('active');
        } else {
            listBtn.classList.add('active');
            gridBtn.classList.remove('active');
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
    
    // Use selected date for filtering appointments
    const selectedDate = new Date(selectedListViewDate);
    selectedDate.setHours(0, 0, 0, 0);
    
    // Format date for date picker (YYYY-MM-DD)
    const datePickerValue = selectedDate.toISOString().split('T')[0];
    
    // Generate time slots from 8am to 8pm
    const timeSlots = [];
    for (let hour = 8; hour <= 20; hour++) {
        const timeString = `${hour.toString().padStart(2, '0')}:00`;
        const displayTime = `${hour > 12 ? hour - 12 : hour === 0 ? 12 : hour === 12 ? 12 : hour}:00 ${hour >= 12 ? 'PM' : 'AM'}`;
        timeSlots.push({ value: timeString, display: displayTime, hour: hour });
    }
    
    // Build HTML
    let html = '<div class="overflow-x-auto">';
    html += '<table class="w-full border-collapse">';
    html += '<thead>';
    html += '<tr>';
    html += '<th class="sticky left-0 z-10 bg-white border-r border-b border-gray-300 pl-3 pt-3 pb-3 pr-1 text-left font-semibold text-gray-700 min-w-[155px]">';
    html += '<div class="flex flex-col gap-2">';
    html += '<label class="text-xs text-gray-500 font-medium">Date & Time</label>';
    html += '<div class="relative">';
    html += `<input type="date" id="listViewDatePicker" value="${datePickerValue}" onchange="changeListViewDate(this.value)" class="absolute opacity-0 w-0 h-0" style="pointer-events: auto;">`;
    html += `<button type="button" onclick="document.getElementById('listViewDatePicker').showPicker ? document.getElementById('listViewDatePicker').showPicker() : document.getElementById('listViewDatePicker').click()" class="px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-900 cursor-pointer hover:bg-gray-50 transition-colors text-left" style="width: 130px;">${selectedDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</button>`;
    html += '</div>';
    html += '</div>';
    html += '</th>';
    
    // Add Salon Appointment column header (first column after Date & Time)
    html += `<th class="border-r border-b border-gray-300 p-3 text-center font-semibold text-gray-700 min-w-[150px] bg-[#e6f0f3]">
        <div class="flex flex-col items-center gap-2">
            <div class="w-10 h-10 bg-[#003047] rounded-full flex items-center justify-center border-2 border-gray-200">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <span class="text-xs font-medium text-gray-900">Salon Appointment</span>
        </div>
    </th>`;
    
    // Add technician headers
    techniciansData.forEach(technician => {
        const initials = technician.initials || (technician.firstName?.[0] || '') + (technician.lastName?.[0] || '');
        const fullName = `${technician.firstName} ${technician.lastName}`;
        const profilePhoto = technician.photo || technician.profilePhoto || null;
        
        html += `<th class="border-r border-b border-gray-300 p-3 text-center font-semibold text-gray-700 min-w-[150px] cursor-pointer hover:bg-gray-50 transition-colors" onclick="showTechnicianMessageModal(${technician.id}, '${fullName.replace(/'/g, "\\'")}')">`;
        html += `<div class="flex flex-col items-center gap-2">`;
        if (profilePhoto) {
            html += `<img src="${profilePhoto}" alt="${fullName}" class="w-10 h-10 rounded-full object-cover border-2 border-gray-200">`;
        } else {
            html += `<div class="w-10 h-10 bg-[#e6f0f3] rounded-full flex items-center justify-center border-2 border-gray-200">`;
            html += `<span class="text-sm font-bold text-[#003047]">${initials}</span>`;
            html += `</div>`;
        }
        html += `<span class="text-xs font-medium text-gray-900">${fullName}</span>`;
        html += `</div>`;
        html += `</th>`;
    });
    
    html += '</tr>';
    html += '</thead>';
    html += '<tbody>';
    
    // Time slot rows
    timeSlots.forEach(timeSlot => {
        html += '<tr class="hover:bg-gray-50">';
        html += `<td class="sticky left-0 z-10 bg-white border-r border-b border-gray-300 p-3 font-medium text-gray-700 text-sm">${timeSlot.display}</td>`;
        
        // Salon Appointment column cell (for appointments without assigned technician)
        const [salonHours, salonMinutes] = timeSlot.value.split(':').map(Number);
        const salonSlotStart = new Date(selectedDate);
        salonSlotStart.setHours(salonHours, salonMinutes, 0, 0);
        const salonSlotEnd = new Date(salonSlotStart);
        salonSlotEnd.setHours(salonHours + 1, 0, 0, 0);
        
        // Find appointments without assigned technician in this time slot
        const salonAppointments = bookingsData.filter(apt => {
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
                aptDate = new Date(apt.appointment_datetime);
            } else if (apt.created_at) {
                aptDate = new Date(apt.created_at);
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
                aptStart = new Date(apt.appointment_datetime);
            } else if (apt.created_at) {
                aptStart = new Date(apt.created_at);
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
                window.location.href='booking.php?date=' + currentDate + '&time=' + currentTime;
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
                
                // Apply color coding based on no show
                let colorClass = '';
                if (isNoShow) {
                    colorClass = 'bg-[#9ca3af] text-[#003047] border-[#6b7280] opacity-70';
                } else {
                    // White background with blue border for salon appointments
                    colorClass = 'bg-white text-[#003047] border-[#003047]';
                }
                
                html += `<div class="mb-1 p-2 rounded border-2 text-xs font-medium ${colorClass} cursor-move hover:opacity-80 draggable-appointment" 
                    draggable="true" 
                    data-appointment-id="${apt.id}"
                    ondragstart="handleAppointmentDragStart(event, ${apt.id})"
                    ondragend="handleAppointmentDragEnd(event)"
                    onclick="if (!event.target.classList.contains('dragging')) { event.stopPropagation(); viewAppointment(${apt.id}); }">`;
                html += `<div class="font-semibold">${customerName}</div>`;
                html += `<div class="text-xs opacity-75">${startTime} - ${endTime}</div>`;
                html += `</div>`;
            });
        }
        
        html += '</td>';
        
        techniciansData.forEach(technician => {
            const technicianId = technician.id.toString();
            const [hours, minutes] = timeSlot.value.split(':').map(Number);
            const slotStart = new Date(selectedDate);
            slotStart.setHours(hours, minutes, 0, 0);
            const slotEnd = new Date(slotStart);
            slotEnd.setHours(hours + 1, 0, 0, 0);
            
            // Find appointments for this technician in this time slot
            const appointments = bookingsData.filter(apt => {
                // First check if technician is assigned (quick check)
                if (!apt.assigned_technician || !Array.isArray(apt.assigned_technician)) return false;
                if (!apt.assigned_technician.some(id => id.toString() === technicianId)) return false;
                
                // Parse appointment date - use appointment_date if available, otherwise parse from appointment_datetime or created_at
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
                    aptStart = new Date(apt.appointment_datetime);
                } else if (apt.created_at) {
                    // Fallback to created_at
                    aptStart = new Date(apt.created_at);
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
                    
                    // Check if technician is assigned
                    const hasTechnician = apt.assigned_technician && Array.isArray(apt.assigned_technician) && apt.assigned_technician.length > 0;
                    
                    // Apply color coding based on no show and technician assignment
                    let colorClass = '';
                    if (isNoShow) {
                        // Gray background for no show
                        colorClass = 'bg-[#9ca3af] text-[#003047] border-[#6b7280] opacity-70';
                    } else if (hasTechnician) {
                        // Blue background with blue border for assigned technician
                        colorClass = 'bg-[#003047] text-white border-[#003047]';
                    } else {
                        // White background with blue border for no assigned technician
                        colorClass = 'bg-white text-[#003047] border-[#003047]';
                    }
                    
                    html += `<div class="mb-1 p-2 rounded border-2 text-xs font-medium ${colorClass} cursor-move hover:opacity-80 draggable-appointment" 
                        draggable="true" 
                        data-appointment-id="${apt.id}"
                        ondragstart="handleAppointmentDragStart(event, ${apt.id})"
                        ondragend="handleAppointmentDragEnd(event)"
                        onclick="if (!event.target.classList.contains('dragging')) { event.stopPropagation(); viewAppointment(${apt.id}); }">`;
                    html += `<div class="font-semibold">${customerName}</div>`;
                    html += `<div class="text-xs opacity-75">${startTime} - ${endTime}</div>`;
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
    selectedListViewDate = new Date(dateString);
    
    // Update URL with date parameter
    var url = new URL(window.location);
    url.searchParams.set('view', 'list');
    url.searchParams.set('date', dateString);
    window.history.pushState({}, '', url);
    
    renderTechnicianListView();
}

// Show technician message modal
function showTechnicianMessageModal(technicianId, technicianName) {
    // Get selected date
    const selectedDate = new Date(selectedListViewDate);
    selectedDate.setHours(0, 0, 0, 0);
    
    // Find all appointments for this technician on the selected date
    const technicianAppointments = bookingsData.filter(apt => {
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
    
    // Find all appointments for this technician on the selected date
    const technicianAppointments = bookingsData.filter(apt => {
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
    
    // Handle Salon Appointment column (clear all technicians)
    if (technicianIdStr === 'salon') {
        // Clear all assigned technicians
        appointment.assigned_technician = [];
        
        // TODO: Save to backend/JSON file
        console.log('Appointment updated:', {
            id: appointment.id,
            newDateTime: newDateTime,
            technician: 'Salon Appointment (unassigned)',
            appointment: appointment
        });
        
        // Show success message
        const timeDisplay = newDate.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        showToastMessage(`Appointment moved to Salon Appointment at ${timeDisplay}`, 'success');
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
        
        // TODO: Save to backend/JSON file
        console.log('Appointment updated:', {
            id: appointment.id,
            newDateTime: newDateTime,
            newTechnician: technicianId,
            appointment: appointment
        });
        
        // Show success message
        const technician = techniciansData.find(t => t.id.toString() === technicianIdStr);
        const technicianName = technician ? `${technician.firstName} ${technician.lastName}` : `Technician #${technicianId}`;
        const timeDisplay = newDate.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        showToastMessage(`Appointment moved to ${technicianName} at ${timeDisplay}`, 'success');
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
            
            // Update styling based on technician assignment
            const hasTechnician = appointment.assigned_technician && appointment.assigned_technician.length > 0;
            if (hasTechnician) {
                event.setProp('backgroundColor', noShowStatus[appointment.id] ? '#9ca3af' : '#003047');
                event.setProp('textColor', noShowStatus[appointment.id] ? '#003047' : '#ffffff');
            } else {
                event.setProp('backgroundColor', noShowStatus[appointment.id] ? '#9ca3af' : 'transparent');
                event.setProp('textColor', '#003047');
            }
            
            // Ensure no-show events always have gray background
            if (noShowStatus[appointment.id]) {
                event.setProp('backgroundColor', '#9ca3af');
            }
            
            // Update border color for no show
            if (noShowStatus[appointment.id]) {
                event.setProp('borderColor', '#6b7280');
            } else {
                event.setProp('borderColor', '#003047');
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
    const status = appointmentData.status;
    const appointmentDate = appointmentData.date;
    const isNoShow = appointmentData.isNoShow || noShowStatus[appointmentId] || false;
    
    // Find event if it exists (for calendar view)
    if (calendarInstance && !currentEvent) {
        const events = calendarInstance.getEvents();
        currentEvent = events.find(e => e.id && e.id.toString() === appointmentId.toString());
    }
    
    const statusColors = {
        'In Booking': 'bg-blue-50 text-blue-700 border-blue-200',
        'In Progress': 'bg-[#e6f0f3] text-[#003047] border-[#003047]',
        'Booked': 'bg-amber-50 text-amber-700 border-amber-200',
        'Completed': 'bg-green-50 text-green-700 border-green-200'
    };
    
    // Create modal content
    const modalContent = `
        <div class="p-6">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-1">${customerName}</h3>
                </div>
                <span class="px-3 py-1.5 rounded-lg text-xs font-semibold border ${statusColors[status] || 'bg-gray-50 text-gray-700 border-gray-200'}">
                    ${status}
                </span>
            </div>
            
            <div class="space-y-4 mb-6">
                <div class="flex items-center gap-3 p-4 bg-gray-50 rounded-xl">
                    <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 mb-0.5">Technician</p>
                        <div id="technicianDisplay_${appointmentId}">
                        <p class="font-semibold text-gray-900">${technicians}</p>
                    </div>
                    </div>
                    ${!isTechnician ? `
                    <button onclick="openTechnicianSelectionModalWithEvent('${appointmentId}', '${customerName}')" class="px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition-all font-medium text-sm flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Select
                    </button>
                    ` : ''}
                </div>
                
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
                
                ${customerPhone ? `
                <div class="p-4 bg-gray-50 rounded-xl">
                    <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-teal-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-xs text-gray-500 mb-0.5">Contact</p>
                        <p class="font-semibold text-gray-900">${customerPhone}</p>
                        </div>
                    </div>
                    <div class="space-y-4 pt-4 border-t border-gray-200">
                        <div>
                            <label class="flex items-center justify-between cursor-pointer mb-2">
                                <div class="flex items-center gap-2">
                                    <input type="checkbox" id="noShowToggle" class="w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500" ${isNoShow ? 'checked' : ''} onchange="toggleNoShow(${appointmentId}, this.checked)">
                                    <span class="text-sm font-medium text-gray-700">No Show</span>
                                </div>
                            </label>
                            <p class="text-xs text-gray-500 ml-6">Mark this appointment as a no-show if the customer did not arrive for their scheduled appointment.</p>
                        </div>
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" id="smsNotificationToggle" class="w-4 h-4 text-green-600 border-gray-300 rounded focus:ring-green-500" onchange="toggleSMSNotificationCheckbox(${appointmentId}, this.checked)">
                                    <span class="text-sm font-medium text-gray-700">Send SMS Notification</span>
                                </label>
                                <button id="sendSMSBtn_${appointmentId}" onclick="sendSMSNotification(${appointmentId})" disabled class="px-4 py-2 bg-gray-300 text-gray-500 rounded-lg hover:bg-gray-400 transition-all font-medium text-sm flex items-center gap-2 cursor-not-allowed">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    Send
                                </button>
                            </div>
                            <p class="text-xs text-gray-500 ml-6">Send an SMS notification to the customer about this appointment.</p>
                        </div>
                    </div>
                </div>
                ` : ''}
            </div>
            
            <div class="flex items-center justify-end pt-4 border-t border-gray-200">
                <div class="flex gap-3">
                    <button onclick="closeModal()" class="px-4 py-3 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200 transition-all font-medium flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Close
                </button>
                ${!isTechnician ? `
                    <button onclick="editBooking(${appointmentId})" class="px-4 py-3 bg-[#003047] text-white rounded-xl hover:bg-[#002535] transition-all font-medium flex items-center justify-center gap-2 shadow-md hover:shadow-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Booking
                </button>
                ` : ''}
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
    
    const aptDateTime = appointment.appointment_datetime || appointment.created_at;
    const aptDate = new Date(aptDateTime);
    
    const statusMap = {
        'waiting': 'In Booking',
        'in-progress': 'In Progress',
        'completed': 'Completed',
        'paid': 'Paid'
    };
    const status = statusMap[appointment.status] || 'Booked';
    
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
    const appointmentData = {
        id: appointment.id,
        customer: customerName,
        technician: technicians,
        phone: customerPhone,
        status: status,
        date: aptDate,
        isNoShow: noShowStatus[appointment.id] || false,
        assigned_technician: appointment.assigned_technician
    };
    
    // Use shared modal function
    showAppointmentModal(appointmentData);
}

function openNewBookingModal() {
    // Redirect to booking page or open new booking modal
    window.location.href = '../booking/';
}

function editBooking(eventId) {
    // Redirect to edit booking page with event ID
    window.location.href = 'edit-booking.php?id=' + eventId;
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
    // Find the appointment
    const appointment = bookingsData.find(apt => apt.id.toString() === bookingId.toString());
    if (!appointment) {
        showToastMessage('Appointment not found', 'error');
        return;
    }
    
    // Find customer phone
    const customer = customersData.find(c => c.id.toString() === appointment.customer_id.toString());
    const phoneNumber = customer ? (customer.phone || '') : '';
    
    if (!phoneNumber) {
        showToastMessage('No phone number available for this customer', 'error');
        return;
    }
    
    // TODO: Implement actual SMS sending logic
    // You can integrate with SMS service providers like Twilio, AWS SNS, etc.
    console.log('Sending SMS to:', phoneNumber, 'for appointment:', bookingId);
    
    // Show success message
    showToastMessage(`SMS notification sent to ${phoneNumber}`, 'success');
    
    // Disable the checkbox and button after sending
    const checkbox = document.getElementById('smsNotificationToggle');
    const sendBtn = document.getElementById(`sendSMSBtn_${bookingId}`);
    if (checkbox) checkbox.checked = false;
    if (sendBtn) {
        sendBtn.disabled = true;
        sendBtn.classList.remove('bg-green-500', 'text-white', 'hover:bg-green-600', 'cursor-pointer');
        sendBtn.classList.add('bg-gray-300', 'text-gray-500', 'cursor-not-allowed', 'hover:bg-gray-400');
    }
}

function toggleNoShow(bookingId, isNoShow) {
    // Handle no show toggle
    console.log('No Show toggled for booking:', bookingId, 'Status:', isNoShow);
    
    // Update no show status
    noShowStatus[bookingId] = isNoShow;
    
    // Update the event in the calendar
    if (calendarInstance) {
        const event = calendarInstance.getEventById(bookingId.toString());
        if (event) {
            // Update event styling
            if (isNoShow) {
                // Apply gray styling for no show
                event.setProp('backgroundColor', '#9ca3af');
                event.setProp('borderColor', '#6b7280');
                event.setProp('textColor', '#003047');
                
                // Add no-show class if not already present
                if (!event.classNames.includes('event-no-show')) {
                    event.setProp('classNames', [...event.classNames, 'event-no-show']);
                }
            } else {
                // Restore original styling based on technician assignment
                const extendedProps = event.extendedProps;
                const hasTechnician = extendedProps.hasTechnician || false;
                
                if (hasTechnician) {
                    // Blue background with blue border for assigned technician
                    event.setProp('backgroundColor', '#003047');
                    event.setProp('textColor', '#ffffff');
                } else {
                    // White/transparent background with blue border for no assigned technician
                    event.setProp('backgroundColor', 'transparent');
                    event.setProp('textColor', '#003047');
                }
                
                event.setProp('borderColor', '#003047');
                
                // Remove no-show class
                const classNames = event.classNames.filter(cn => cn !== 'event-no-show');
                event.setProp('classNames', classNames);
            }
            
            // Update extended props
            event.setExtendedProp('isNoShow', isNoShow);
            
            // Force calendar to re-render the event
            calendarInstance.render();
        }
    }
    
    // Update list view if it's currently visible
    const listViewContainer = document.getElementById('listViewContainer');
    if (listViewContainer && !listViewContainer.classList.contains('hidden')) {
        renderTechnicianListView();
    }
    
    // TODO: Update booking status in backend/JSON
    // You can add code here to save the no show status to your backend
    
    // Show toast alert message
    if (isNoShow) {
        showToastMessage('Marked as No Show', 'success');
    } else {
        showToastMessage('No Show status removed', 'success');
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

function grayOutPastDays() {
    // Get today's date (start of day)
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    // Find all day cells in month view
    const dayCells = document.querySelectorAll('.fc-daygrid-day');
    
    dayCells.forEach(function(dayCell) {
        // Try multiple ways to get the date from FullCalendar
        let dayDate = null;
        
        // Method 1: Check for data-date attribute
        const dateStr = dayCell.getAttribute('data-date');
        if (dateStr) {
            dayDate = new Date(dateStr);
        } else {
            // Method 2: Get from the day number element's parent date info
            const dayNumber = dayCell.querySelector('.fc-daygrid-day-number');
            if (dayNumber) {
                // FullCalendar stores date in the element's data
                const parent = dayCell.closest('[data-date]');
                if (parent) {
                    dayDate = new Date(parent.getAttribute('data-date'));
                } else {
                    // Try to reconstruct date from view context
                    const viewEl = document.querySelector('.fc-view-harness');
                    if (viewEl) {
                        // Get calendar instance from global if available
                        // Or check the day number text
                        const ariaLabel = dayCell.getAttribute('aria-label');
                        if (ariaLabel) {
                            // Try to parse date from aria-label like "Tuesday, December 3, 2024"
                            const dateMatch = ariaLabel.match(/(\w+),?\s+(\w+)\s+(\d+),?\s+(\d+)/);
                            if (dateMatch) {
                                dayDate = new Date(dateMatch[2] + ' ' + dateMatch[3] + ', ' + dateMatch[4]);
                            }
                        }
                    }
                }
            }
        }
        
        // If we found a date, compare with today
        if (dayDate) {
            dayDate.setHours(0, 0, 0, 0);
            if (dayDate < today) {
                dayCell.classList.add('fc-day-past');
            } else {
                dayCell.classList.remove('fc-day-past');
            }
        }
    });
    
    // Also handle time grid columns for week/day view
    const timeGridCols = document.querySelectorAll('.fc-timegrid-col');
    const now = new Date();
    
    timeGridCols.forEach(function(col) {
        const dateStr = col.getAttribute('data-date');
        if (dateStr) {
            const dayDate = new Date(dateStr);
            dayDate.setHours(0, 0, 0, 0);
            
            // For week/day views, check if the entire day column is in the past
            if (dayDate < today) {
                col.classList.add('fc-day-past');
            } else {
                col.classList.remove('fc-day-past');
            }
        }
    });
    
    // Mark past time slots within today's columns
    const timeSlots = document.querySelectorAll('.fc-timegrid-slot');
    timeSlots.forEach(function(slot) {
        // Check if slot time is in the past
        // FullCalendar stores time information in the slot elements
        const slotEl = slot;
        const slotData = slotEl.getAttribute('data-time');
        
        // Find the column this slot belongs to
        const col = slotEl.closest('.fc-timegrid-col');
        if (col) {
            const colDateStr = col.getAttribute('data-date');
            if (colDateStr) {
                const colDate = new Date(colDateStr);
                // Check if this is today and the time slot is in the past
                const colDateOnly = new Date(colDate);
                colDateOnly.setHours(0, 0, 0, 0);
                const todayOnly = new Date(today);
                
                if (colDateOnly.getTime() === todayOnly.getTime()) {
                    // This is today, check if slot time has passed
                    // FullCalendar stores slot info differently, so we'll mark it via CSS
                    const slotTime = slotEl.getAttribute('data-time') || '';
                    // We'll let the dayCellClassNames handle this more reliably
                }
            }
        }
    });
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
        <div class="p-4 max-w-6xl">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">Select Technicians</h3>
                <button onclick="closeNestedModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <!-- Available Technicians Section -->
                <div class="border-r border-gray-200 pr-4">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-sm font-semibold text-gray-900">Available Technicians</h4>
                        <span id="availableCount" class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">${techniciansData.length}</span>
                    </div>
                    <p class="text-xs text-gray-500 mb-2">Click to assign technicians to services</p>
                    <!-- Search Bar -->
                    <div class="mb-3">
                        <div class="relative">
                            <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <input type="text" id="technicianSearchInput" placeholder="Search technicians..." oninput="searchTechnicians(this.value)" class="w-full pl-10 pr-10 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent text-sm">
                            <button id="clearTechnicianSearchBtn" onclick="clearTechnicianSearch()" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600 hidden">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div id="availableTechniciansContainer" class="space-y-2 min-h-[300px] max-h-[60vh] overflow-y-auto">
                        <!-- Technicians will be loaded here -->
                    </div>
                </div>
                
                <!-- Assigned Technicians Section -->
                <div class="pl-4">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-sm font-semibold text-gray-900">Assigned Technicians</h4>
                        <span id="assignedCount" class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">${selectedTechnicianIds.length}</span>
                    </div>
                    <p class="text-xs text-gray-500 mb-3">Click to remove assigned technicians</p>
                    <div id="assignedTechniciansContainer" class="space-y-2 min-h-[300px] max-h-[60vh] overflow-y-auto">
                        <!-- Assigned technicians will be loaded here -->
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 mt-4 pt-4 border-t border-gray-200">
                <button onclick="closeNestedModal()" class="px-5 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">
                    Cancel
                </button>
                <button onclick="confirmTechnicianSelection()" class="px-5 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">
                    Confirm
                </button>
            </div>
        </div>
    `;
    
    openNestedModal(modalContent, 'large', false);
    renderAvailableTechnicians();
    renderAssignedTechnicians();
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

// Render available technicians
function renderAvailableTechnicians() {
    const container = document.getElementById('availableTechniciansContainer');
    if (!container) return;
    
    if (techniciansData.length === 0) {
        container.innerHTML = `
            <div class="flex items-center justify-center h-full min-h-[300px]">
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
            <div class="flex items-center justify-center h-full min-h-[300px]">
                <p class="text-sm text-gray-400">No technicians found</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    filteredTechnicians.forEach(technician => {
        const technicianIdStr = technician.id.toString();
        const isAssigned = selectedTechnicianIds.includes(technicianIdStr);
        const initials = technician.initials || (technician.firstName?.[0] || '') + (technician.lastName?.[0] || '');
        const fullName = `${technician.firstName} ${technician.lastName}`;
        
        const containerClasses = isAssigned 
            ? "flex items-center gap-3 p-2 rounded-lg transition-colors opacity-50 grayscale cursor-pointer group hover:bg-gray-100"
            : "flex items-center gap-3 cursor-pointer group hover:bg-gray-50 p-2 rounded-lg transition-colors";
        
        const avatarClasses = isAssigned
            ? "w-12 h-12 bg-gray-300 rounded-full flex items-center justify-center"
            : "w-12 h-12 bg-gray-200 rounded-full flex items-center justify-center";
        
        const initialClasses = isAssigned
            ? "text-sm font-bold text-gray-500"
            : "text-sm font-bold text-gray-600";
        
        const nameClasses = isAssigned
            ? "text-base font-medium text-gray-400"
            : "text-base font-medium text-gray-900";
        
        const badgeClasses = isAssigned
            ? "absolute -bottom-1 -right-1 w-5 h-5 bg-gray-400 text-white rounded-full flex items-center justify-center text-xs font-bold border-2 border-white"
            : "absolute -bottom-1 -right-1 w-5 h-5 bg-[#003047] text-white rounded-full flex items-center justify-center text-xs font-bold border-2 border-white";
        
        html += `
            <div onclick="${isAssigned ? 'removeAssignedTechnician(' + technician.id + ')' : 'assignTechnician(' + technician.id + ')'}" class="${containerClasses}">
                <div class="relative flex-shrink-0">
                    <div class="${avatarClasses}">
                        <span class="${initialClasses}">${initials}</span>
                    </div>
                    <div class="${badgeClasses}">
                        0
                    </div>
                </div>
                <div class="flex-1">
                    <p class="${nameClasses}">${fullName}</p>
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
            <div class="flex items-center justify-center h-full min-h-[300px]">
                <p class="text-sm text-gray-400">No technicians assigned</p>
            </div>
        `;
        updateCounts();
        return;
    }
    
    let html = '';
    selectedTechnicianIds.forEach(technicianIdStr => {
        const technician = techniciansData.find(t => t.id.toString() === technicianIdStr);
        if (!technician) return;
        
        const initials = technician.initials || (technician.firstName?.[0] || '') + (technician.lastName?.[0] || '');
        const fullName = `${technician.firstName} ${technician.lastName}`;
        
        html += `
            <div onclick="removeAssignedTechnician(${technician.id})" class="flex items-center gap-3 cursor-pointer group hover:bg-gray-50 p-2 rounded-lg transition-colors">
                <div class="relative flex-shrink-0">
                    <div class="w-12 h-12 bg-[#003047] rounded-full flex items-center justify-center">
                        <span class="text-sm font-bold text-white">${initials}</span>
                    </div>
                    <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-[#003047] text-white rounded-full flex items-center justify-center text-xs font-bold border-2 border-white">
                        0
                    </div>
                </div>
                <div class="flex-1">
                    <p class="text-base font-medium text-gray-900">${fullName}</p>
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
    
    // Find the appointment in bookingsData
    const appointmentIndex = bookingsData.findIndex(apt => apt.id.toString() === currentAppointmentId.toString());
    if (appointmentIndex === -1) return;
    
    // Update the appointment with selected technicians
    const technicianIds = selectedTechnicianIds.map(id => parseInt(id));
    bookingsData[appointmentIndex].assigned_technician = technicianIds.length > 0 ? technicianIds : null;
    
    // TODO: Save to backend/JSON file
    console.log('Updated appointment technicians:', bookingsData[appointmentIndex]);
    
    // Update the technician display in the event modal
    updateEventModalTechnicianDisplay();
    
    // Update the calendar event display
    updateCalendarEventDisplay();
    
    // Update list view if it's currently visible
    const listViewContainer = document.getElementById('listViewContainer');
    if (listViewContainer && !listViewContainer.classList.contains('hidden')) {
        if (typeof renderTechnicianListView === 'function') {
            renderTechnicianListView();
        }
    }
    
    // Close the technician selection modal (not the event modal)
    closeNestedModal();
}

// Update technician display in the event modal
function updateEventModalTechnicianDisplay() {
    if (!currentEventModalElement || !currentAppointmentId) return;
    
    // Get updated technician names
    let techniciansText = 'Not Assigned';
    if (selectedTechnicianIds.length > 0) {
        const technicianNames = selectedTechnicianIds.map(techIdStr => {
            const tech = techniciansData.find(t => t.id.toString() === techIdStr);
            return tech ? `${tech.firstName} ${tech.lastName}` : `Technician #${techIdStr}`;
        });
        techniciansText = technicianNames.join(', ');
    }
    
    // Update the display
    currentEventModalElement.innerHTML = `<p class="font-semibold text-gray-900">${techniciansText}</p>`;
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
    
    // Update event styling based on technician assignment
    const isNoShow = noShowStatus[appointment.id] === true;
    let eventBgColor = hasTechnician ? '#003047' : 'transparent';
    let eventBorderColor = '#003047';
    let eventTextColor = hasTechnician ? '#ffffff' : '#003047';
    
    if (isNoShow) {
        eventBgColor = '#9ca3af';
        eventBorderColor = '#6b7280';
        eventTextColor = '#003047';
    }
    
    // Update event colors
    currentEvent.setProp('backgroundColor', eventBgColor);
    currentEvent.setProp('borderColor', eventBorderColor);
    currentEvent.setProp('textColor', eventTextColor);
    
    // Update class names - preserve existing status class and update technician/no-show classes
    const existingClassNames = currentEvent.classNames || [];
    const statusClass = existingClassNames.find(cn => cn.startsWith('event-status-') || ['event-booked', 'event-in-booking', 'event-completed', 'event-in-progress'].includes(cn));
    const classNames = [];
    if (statusClass) classNames.push(statusClass);
    if (hasTechnician) {
        classNames.push('event-has-technician');
    }
    if (isNoShow) {
        classNames.push('event-no-show');
    }
    currentEvent.setProp('classNames', classNames);
}

</script>

<style>
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

/* Gray out past days */
.fc-day-past {
    background-color: #f9fafb !important;
    opacity: 0.5 !important;
    pointer-events: auto !important;
}

.fc-day-past .fc-daygrid-day-number {
    color: #9ca3af !important;
}

.fc-day-past .fc-event {
    opacity: 0.6 !important;
    pointer-events: none !important;
}

/* Prevent interaction with past day events */
.fc-day-past .fc-event:hover {
    opacity: 0.6 !important;
    transform: none !important;
    cursor: not-allowed !important;
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

.fc-event:hover {
    background: #003047 !important;
    color: white !important;
    border-color: #003047 !important;
    transform: translateY(-2px) scale(1.02) !important;
    box-shadow: 0 2px 8px rgba(0, 48, 71, 0.3) !important;
    z-index: 10 !important;
    cursor: pointer !important;
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

<?php include '../includes/footer.php'; ?>


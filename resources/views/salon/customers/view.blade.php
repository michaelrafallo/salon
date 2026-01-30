@extends('layouts.salon')

@section('content')
@php
    $customersIndexUrl = route('salon.customers.index');
@endphp

<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ $customersIndexUrl }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span class="text-sm font-medium">Back to Customers</span>
            </a>
        </div>

        <!-- Customer Header -->
        <div id="customerHeader" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-start gap-6 flex-1">
                    <div id="customerAvatar" class="w-24 h-24 bg-[#e6f0f3] rounded-full flex items-center justify-center flex-shrink-0">
                        <span id="customerInitials" class="text-4xl font-bold text-[#003047]"></span>
                    </div>
                    <div class="flex-1">
                        <h1 id="customerName" class="text-3xl font-bold text-gray-900 mb-2"></h1>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Email</p>
                                <p class="text-base font-medium text-gray-900" id="customerEmail"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Phone</p>
                                <p class="text-base font-medium text-gray-900" id="customerPhone"></p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-sm text-gray-500 mb-1">Address</p>
                                <p class="text-base font-medium text-gray-900" id="customerAddress"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <button onclick="openEditCustomerModal()" class="px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95 text-sm">
                    Edit Customer
                </button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <p class="text-sm text-gray-500 mb-2">Total Visits</p>
                <p id="totalVisits" class="text-3xl font-bold text-gray-900">0</p>
                <p id="lastVisitText" class="text-xs text-gray-500 mt-2"></p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <p class="text-sm text-gray-500 mb-2">Total Spent</p>
                <p id="totalSpent" class="text-3xl font-bold text-gray-900">$0.00</p>
                <p id="averagePerVisit" class="text-xs text-gray-500 mt-2"></p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <p class="text-sm text-gray-500 mb-2">Customer Since</p>
                <p id="customerSince" class="text-3xl font-bold text-gray-900"></p>
                <p id="memberFor" class="text-xs text-gray-500 mt-2"></p>
            </div>
        </div>

        <!-- Tickets -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Tickets</h2>
            <div id="ticketsContainer" class="space-y-4">
                <p class="text-center text-gray-500 py-8">Loading tickets...</p>
            </div>
        </div>
    </div>
</main>

@push('scripts')
<script>
window.salonJsonBase = '{{ asset("json") }}';
var base = window.salonJsonBase || '{{ url("json") }}';

// Get customer ID from URL
function getCustomerIdFromURL() {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get('id');
}

// Global customer data
let customerData = null;
let customerBookings = [];
let allCustomers = [];
let allAppointments = [];
let allTechnicians = [];

// Color classes for avatars
const avatarColors = [
    { bg: 'bg-[#e6f0f3]', text: 'text-[#003047]' },
    { bg: 'bg-purple-100', text: 'text-purple-600' },
    { bg: 'bg-teal-100', text: 'text-teal-600' },
    { bg: 'bg-indigo-100', text: 'text-indigo-600' }
];

// Get initials from name
function getInitials(firstName, lastName) {
    const first = firstName ? firstName[0].toUpperCase() : '';
    const last = lastName ? lastName[0].toUpperCase() : '';
    return (first + last).substring(0, 2);
}

// Get avatar color based on customer ID
function getAvatarColor(customerId) {
    const index = (customerId - 1) % avatarColors.length;
    return avatarColors[index];
}

// Format date
function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString + 'T00:00:00');
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    return months[date.getMonth()] + ' ' + date.getDate() + ', ' + date.getFullYear();
}

// Format date for display (M d, Y)
function formatDateDisplay(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString + 'T00:00:00');
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    return months[date.getMonth()] + ' ' + date.getDate() + ', ' + date.getFullYear();
}

// Load customer data
async function loadCustomerData() {
    const customerId = getCustomerIdFromURL();
    if (!customerId) {
        showErrorMessage('No customer ID provided.');
        return;
    }

    try {
        // Fetch customers, appointments, and technicians
        const [customersResponse, appointmentsResponse, techniciansResponse] = await Promise.all([
            fetch(base + '/customers.json'),
            fetch(base + '/appointments.json'),
            fetch(base + '/users.json')
        ]);

        const customersData = await customersResponse.json();
        const appointmentsData = await appointmentsResponse.json();
        const techniciansData = await techniciansResponse.json();

        allCustomers = customersData.customers || [];
        allAppointments = appointmentsData.appointments || [];
        allTechnicians = techniciansData.users.filter(u => u.role === 'technician' || u.userlevel === 'technician') || [];

        // Find customer
        customerData = allCustomers.find(c => c.id.toString() === customerId.toString());
        if (!customerData) {
            showErrorMessage('Customer not found.');
            return;
        }

        // Find bookings for this customer
        customerBookings = allAppointments.filter(apt => 
            apt.customer_id && apt.customer_id.toString() === customerId.toString()
        );

        // Render customer info
        renderCustomerInfo();
        
        // Calculate and render statistics
        calculateAndRenderStatistics();
        
        // Render tickets
        renderTickets();
    } catch (error) {
        console.error('Error loading customer data:', error);
        showErrorMessage('Failed to load customer data.');
    }
}

// Render customer information
function renderCustomerInfo() {
    if (!customerData) return;

    const initials = getInitials(customerData.firstName, customerData.lastName);
    const avatarColor = getAvatarColor(customerData.id);

    // Update avatar
    const avatarEl = document.getElementById('customerAvatar');
    const initialsEl = document.getElementById('customerInitials');
    if (avatarEl) {
        avatarEl.className = `w-24 h-24 ${avatarColor.bg} rounded-full flex items-center justify-center flex-shrink-0`;
    }
    if (initialsEl) {
        initialsEl.className = `text-4xl font-bold ${avatarColor.text}`;
        initialsEl.textContent = initials;
    }

    // Update name
    const nameEl = document.getElementById('customerName');
    if (nameEl) {
        nameEl.textContent = `${customerData.firstName} ${customerData.lastName}`;
    }

    // Update contact info
    const emailEl = document.getElementById('customerEmail');
    const phoneEl = document.getElementById('customerPhone');
    const addressEl = document.getElementById('customerAddress');
    
    if (emailEl) emailEl.textContent = customerData.email || '—';
    if (phoneEl) phoneEl.textContent = customerData.phone || '—';
    if (addressEl) addressEl.textContent = customerData.address || '—';
}

// Calculate and render statistics
function calculateAndRenderStatistics() {
    if (!customerData) return;

    // Calculate total visits (number of bookings)
    const totalVisits = customerBookings.length;
    
    // Calculate total spent (sum of all booking amounts)
    let totalSpent = 0;
    customerBookings.forEach(booking => {
        if (booking.services && Array.isArray(booking.services)) {
            booking.services.forEach(service => {
                totalSpent += parseFloat(service.price || service.amount || 0);
            });
        }
    });

    // Find last visit date
    let lastVisitDate = null;
    if (customerBookings.length > 0) {
        const dates = customerBookings.map(b => {
            if (b.appointment_date) return b.appointment_date;
            if (b.appointment_datetime) return b.appointment_datetime.split('T')[0];
            if (b.created_at) return b.created_at.split('T')[0];
            return null;
        }).filter(d => d !== null).sort().reverse();
        if (dates.length > 0) {
            lastVisitDate = dates[0];
        }
    }

    // Calculate customer since (earliest booking or created_at)
    let customerSince = null;
    if (customerBookings.length > 0) {
        const dates = customerBookings.map(b => {
            if (b.created_at) return b.created_at.split('T')[0];
            if (b.appointment_date) return b.appointment_date;
            if (b.appointment_datetime) return b.appointment_datetime.split('T')[0];
            return null;
        }).filter(d => d !== null).sort();
        if (dates.length > 0) {
            customerSince = dates[0];
        }
    } else if (customerData.createdAt) {
        customerSince = customerData.createdAt;
    }

    // Calculate member duration
    let memberFor = '';
    if (customerSince) {
        const sinceDate = new Date(customerSince + 'T00:00:00');
        const now = new Date();
        const monthsDiff = (now.getFullYear() - sinceDate.getFullYear()) * 12 + (now.getMonth() - sinceDate.getMonth());
        if (monthsDiff === 0) {
            memberFor = 'New member';
        } else if (monthsDiff === 1) {
            memberFor = 'Member for 1 month';
        } else {
            memberFor = `Member for ${monthsDiff} months`;
        }
    }

    // Update statistics display
    const totalVisitsEl = document.getElementById('totalVisits');
    const lastVisitTextEl = document.getElementById('lastVisitText');
    const totalSpentEl = document.getElementById('totalSpent');
    const averagePerVisitEl = document.getElementById('averagePerVisit');
    const customerSinceEl = document.getElementById('customerSince');
    const memberForEl = document.getElementById('memberFor');

    if (totalVisitsEl) totalVisitsEl.textContent = totalVisits;
    if (lastVisitTextEl) {
        if (lastVisitDate) {
            lastVisitTextEl.textContent = 'Last visit: ' + formatDateDisplay(lastVisitDate);
        } else {
            lastVisitTextEl.textContent = 'No visits yet';
        }
    }
    if (totalSpentEl) totalSpentEl.textContent = '$' + totalSpent.toFixed(2);
    if (averagePerVisitEl) {
        const average = totalVisits > 0 ? (totalSpent / totalVisits) : 0;
        averagePerVisitEl.textContent = 'Average per visit: $' + average.toFixed(2);
    }
    if (customerSinceEl && customerSince) {
        const sinceDate = new Date(customerSince + 'T00:00:00');
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        customerSinceEl.textContent = months[sinceDate.getMonth()] + ' ' + sinceDate.getFullYear();
    }
    if (memberForEl) memberForEl.textContent = memberFor;
}

// Render tickets
function renderTickets() {
    const container = document.getElementById('ticketsContainer');
    if (!container) return;

    if (customerBookings.length === 0) {
        container.innerHTML = '<p class="text-center text-gray-500 py-8">No tickets found for this customer.</p>';
        return;
    }

    // Sort bookings by date (newest first)
    const sortedBookings = [...customerBookings].sort((a, b) => {
        const dateA = a.appointment_date || (a.appointment_datetime ? a.appointment_datetime.split('T')[0] : a.created_at ? a.created_at.split('T')[0] : '');
        const dateB = b.appointment_date || (b.appointment_datetime ? b.appointment_datetime.split('T')[0] : b.created_at ? b.created_at.split('T')[0] : '');
        return dateB.localeCompare(dateA);
    });

    let html = '';
    sortedBookings.forEach((booking, index) => {
        // Get booking date
        let bookingDate = booking.appointment_date;
        if (!bookingDate && booking.appointment_datetime) {
            bookingDate = booking.appointment_datetime.split('T')[0];
        }
        if (!bookingDate && booking.created_at) {
            bookingDate = booking.created_at.split('T')[0];
        }
        const dateDisplay = bookingDate ? formatDateDisplay(bookingDate) : '—';

        // Get services
        const services = booking.services || [];
        let totalAmount = 0;
        services.forEach(service => {
            totalAmount += parseFloat(service.price || service.amount || service.service_price || 0);
        });

        // Get technicians
        let technicianNames = [];
        if (booking.assigned_technician && Array.isArray(booking.assigned_technician)) {
            technicianNames = booking.assigned_technician.map(techId => {
                const tech = allTechnicians.find(t => t.id.toString() === techId.toString());
                return tech ? `${tech.firstName} ${tech.lastName}` : `Technician #${techId}`;
            });
        }

        // Get status
        const status = booking.status || 'Completed';
        const statusColor = status === 'Completed' ? 'bg-green-100 text-green-700' : 
                           status === 'Paid' ? 'bg-blue-100 text-blue-700' :
                           status === 'Waiting' ? 'bg-yellow-100 text-yellow-700' :
                           'bg-gray-100 text-gray-700';

        // Get booking ID
        const bookingId = booking.id || `BK-${String(index + 1).padStart(3, '0')}`;

        // Get details
        const details = booking.notes || booking.details || 'Nail care service';

        html += `
            <div class="p-5 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                <div class="flex justify-between items-start mb-3">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <p class="font-semibold text-gray-900 text-lg">Ticket #${bookingId}</p>
                            <span class="px-2 py-1 ${statusColor} text-xs font-medium rounded">${status}</span>
                        </div>
                        <p class="text-sm text-gray-600 mb-3">${details}</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-2xl font-bold text-gray-900">$${totalAmount.toFixed(2)}</span>
                        <button onclick="printTicket('${bookingId}')" class="px-3 py-1.5 bg-[#003047] text-white text-xs font-medium rounded hover:bg-[#002535] transition active:scale-95 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                            </svg>
                            Print
                        </button>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-3 border-t border-gray-200">
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Date</p>
                        <p class="text-sm font-medium text-gray-900">${dateDisplay}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 mb-1">Technicians</p>
                        <p class="text-sm font-medium text-gray-900">${technicianNames.length > 0 ? technicianNames.join(', ') : 'Not Assigned'}</p>
                    </div>
                    <div class="md:col-span-2">
                        <p class="text-xs text-gray-500 mb-2">Services</p>
                        <div class="space-y-1">
                            ${services.length > 0 ? services.map(service => {
                                const serviceName = service.service || service.name || 'Service';
                                const servicePrice = parseFloat(service.price || service.amount || service.service_price || 0);
                                return `
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-900">${serviceName}</span>
                                        <span class="text-sm font-medium text-gray-900">$${servicePrice.toFixed(2)}</span>
                                    </div>
                                `;
                            }).join('') : '<p class="text-sm text-gray-500">No services listed</p>'}
                        </div>
                    </div>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

// Open edit customer modal
function openEditCustomerModal() {
    if (!customerData) return;

    const modalContent = `
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">Edit Customer</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form onsubmit="updateCustomer(event)" class="space-y-4">
                <input type="hidden" name="customer_id" value="${customerData.id}">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                        <input type="text" name="first_name" id="editFirstName" value="${customerData.firstName || ''}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                        <input type="text" name="last_name" id="editLastName" value="${customerData.lastName || ''}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" id="editEmail" value="${customerData.email || ''}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" name="phone" id="editPhone" value="${customerData.phone || ''}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Address (optional)</label>
                    <input type="text" name="address" id="editAddress" value="${customerData.address || ''}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                </div>
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <label class="text-sm font-medium text-gray-900">Active</label>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="active" id="editActive" class="sr-only peer" ${customerData.status !== 'inactive' ? 'checked' : ''}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div>
                    </label>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">
                        Update Customer
                    </button>
                </div>
            </form>
        </div>
    `;
    
    if (typeof openModal === 'function') {
        openModal(modalContent);
    }
}

// Update customer
function updateCustomer(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const firstName = formData.get('first_name');
    const lastName = formData.get('last_name');
    const email = formData.get('email');
    const phone = formData.get('phone');
    const address = formData.get('address');
    const active = formData.get('active') === 'on';
    
    // Update customer data
    customerData.firstName = firstName;
    customerData.lastName = lastName;
    customerData.email = email;
    customerData.phone = phone;
    customerData.address = address;
    customerData.status = active ? 'active' : 'inactive';
    
    // Update displayed information
    renderCustomerInfo();
    
    // Show success message
    if (typeof showSuccessMessage === 'function') {
        showSuccessMessage('Customer details updated successfully!');
    } else {
        alert('Customer details updated successfully!');
    }
    
    // Close modal
    if (typeof closeModal === 'function') {
        closeModal();
    }
    
    // TODO: In a real application, send this data to the server
    // fetch('update-customer.php', {
    //     method: 'POST',
    //     body: formData
    // }).then(response => response.json()).then(data => {
    //     showSuccessMessage('Customer details updated successfully!');
    //     closeModal();
    //     location.reload();
    // });
}

// Print ticket
function printTicket(bookingId) {
    window.print();
}

// Show error message
function showErrorMessage(message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    errorDiv.textContent = message;
    document.body.appendChild(errorDiv);
    setTimeout(() => errorDiv.remove(), 3000);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadCustomerData();
});
</script>
@endpush
@endsection

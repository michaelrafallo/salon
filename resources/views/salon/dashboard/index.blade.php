@extends('layouts.salon')

@section('content')
<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-600 text-sm sm:text-base">{{ now()->format('l, d F Y') }}</p>
        </div>

        @if($current_role === 'receptionist')
            @include('salon.dashboard.partials.receptionist')
        @elseif($current_role === 'technician')
            @include('salon.dashboard.partials.technician')
        @else
            @include('salon.dashboard.partials.admin')
        @endif
    </div>
</main>

@if($current_role === 'technician')
@push('scripts')
<script>
function updateClockDateTime() {
    var clockDateElement = document.getElementById('clockDate');
    var clockTimeElement = document.getElementById('clockTime');
    if (clockDateElement && clockTimeElement) {
        var now = new Date();
        clockDateElement.textContent = now.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
        clockTimeElement.textContent = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }
}
setInterval(updateClockDateTime, 1000);
updateClockDateTime();
function initializeTechnicianLoginState() {
    var isLoggedIn = localStorage.getItem('technician_dashboard_loggedIn') === 'true';
    var clockInDateTime = localStorage.getItem('technician_dashboard_clockInDateTime');
    var btn = document.getElementById('technicianLoginBtn');
    var text = document.getElementById('technicianLoginText');
    var clockInDisplay = document.getElementById('clockInDateTimeDisplay');
    var clockInDateTimeSpan = document.getElementById('clockInDateTime');
    if (btn && text) {
        if (isLoggedIn) {
            btn.classList.remove('bg-[#003047]', 'hover:bg-[#002535]');
            btn.classList.add('bg-red-600', 'hover:bg-red-700');
            text.textContent = 'Clock Out';
            if (clockInDisplay && clockInDateTimeSpan && clockInDateTime) {
                clockInDateTimeSpan.textContent = clockInDateTime;
                clockInDisplay.classList.remove('hidden');
            }
        } else {
            btn.classList.remove('bg-red-600', 'hover:bg-red-700');
            btn.classList.add('bg-[#003047]', 'hover:bg-[#002535]');
            text.textContent = 'Clock In';
            if (clockInDisplay) clockInDisplay.classList.add('hidden');
        }
    }
}
document.addEventListener('DOMContentLoaded', initializeTechnicianLoginState);
function toggleTechnicianLogin() {
    var btn = document.getElementById('technicianLoginBtn');
    var text = document.getElementById('technicianLoginText');
    var clockInDisplay = document.getElementById('clockInDateTimeDisplay');
    var clockInDateTimeSpan = document.getElementById('clockInDateTime');
    if (!btn || !text) return;
    var isLoggedIn = text.textContent.trim() === 'Clock Out';
    var newState = !isLoggedIn;
    var now = new Date();
    var dateTime = now.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) + ' ' + now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    localStorage.setItem('technician_dashboard_loggedIn', newState.toString());
    if (newState) {
        btn.classList.remove('bg-[#003047]', 'hover:bg-[#002535]');
        btn.classList.add('bg-red-600', 'hover:bg-red-700');
        text.textContent = 'Clock Out';
        localStorage.setItem('technician_dashboard_clockInDateTime', dateTime);
        if (clockInDisplay && clockInDateTimeSpan) {
            clockInDateTimeSpan.textContent = dateTime;
            clockInDisplay.classList.remove('hidden');
        }
        showSuccessMessage('Clocked in successfully at ' + dateTime);
    } else {
        btn.classList.remove('bg-red-600', 'hover:bg-red-700');
        btn.classList.add('bg-[#003047]', 'hover:bg-[#002535]');
        text.textContent = 'Clock In';
        localStorage.removeItem('technician_dashboard_clockInDateTime');
        if (clockInDisplay) clockInDisplay.classList.add('hidden');
        showSuccessMessage('Clocked out successfully at ' + dateTime);
    }
    updateClockDateTime();
}
function showSuccessMessage(message) {
    var d = document.createElement('div');
    d.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    d.textContent = message;
    document.body.appendChild(d);
    setTimeout(function() { d.remove(); }, 3000);
}
</script>
@endpush
@endif
@endsection

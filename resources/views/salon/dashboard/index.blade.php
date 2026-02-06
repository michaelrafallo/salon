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

function formatIsoDateTime(iso) {
    if (!iso) return '';
    var d = new Date(iso);
    if (isNaN(d.getTime())) return '';
    return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) + ' ' +
        d.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
}

function setTechnicianClockState(isClockedIn, clockInIso) {
    var btn = document.getElementById('technicianLoginBtn');
    var text = document.getElementById('technicianLoginText');
    var clockInDisplay = document.getElementById('clockInDateTimeDisplay');
    var clockInDateTimeSpan = document.getElementById('clockInDateTime');

    if (!btn || !text) return;

    if (isClockedIn) {
        btn.classList.remove('bg-[#003047]', 'hover:bg-[#002535]');
        btn.classList.add('bg-red-600', 'hover:bg-red-700');
        text.textContent = 'Clock Out';

        var display = formatIsoDateTime(clockInIso);
        if (clockInDisplay && clockInDateTimeSpan && display) {
            clockInDateTimeSpan.textContent = display;
            clockInDisplay.classList.remove('hidden');
        } else if (clockInDisplay) {
            clockInDisplay.classList.add('hidden');
        }
    } else {
        btn.classList.remove('bg-red-600', 'hover:bg-red-700');
        btn.classList.add('bg-[#003047]', 'hover:bg-[#002535]');
        text.textContent = 'Clock In';
        if (clockInDisplay) clockInDisplay.classList.add('hidden');
    }
}

function setTechnicianClockLoading(isLoading) {
    var btn = document.getElementById('technicianLoginBtn');
    if (!btn) return;
    btn.disabled = !!isLoading;
    btn.classList.toggle('opacity-60', !!isLoading);
    btn.classList.toggle('cursor-not-allowed', !!isLoading);
}

function loadTechnicianClockStateFromServer() {
    var btn = document.getElementById('technicianLoginBtn');
    if (!btn) return;

    var technicianId = btn.dataset.technicianId || '';
    var turnTrackerUrl = btn.dataset.turnTrackerUrl || '';

    if (!technicianId || !turnTrackerUrl) {
        setTechnicianClockState(false);
        return;
    }

    setTechnicianClockLoading(true);

    fetch(turnTrackerUrl, { credentials: 'same-origin' })
        .then(function (r) {
            if (!r.ok) throw new Error('Failed to load turn tracker');
            return r.json();
        })
        .then(function (data) {
            var entries = data.entries || [];
            var match = entries.find(function (e) { return String(e.user_id) === String(technicianId); });
            if (match) {
                setTechnicianClockState(true, match.clock_in);
            } else {
                setTechnicianClockState(false);
            }
        })
        .catch(function () {
            setTechnicianClockState(false);
        })
        .finally(function () {
            setTechnicianClockLoading(false);
        });
}

document.addEventListener('DOMContentLoaded', loadTechnicianClockStateFromServer);
function toggleTechnicianLogin() {
    var btn = document.getElementById('technicianLoginBtn');
    var text = document.getElementById('technicianLoginText');
    var clockInDisplay = document.getElementById('clockInDateTimeDisplay');
    var clockInDateTimeSpan = document.getElementById('clockInDateTime');
    if (!btn || !text) return;

    var isClockedIn = text.textContent.trim() === 'Clock Out';
    var clockInUrl = btn.dataset.clockInUrl || '';
    var clockOutUrl = btn.dataset.clockOutUrl || '';

    if (typeof salonApi === 'undefined' || !salonApi.post) {
        showSuccessMessage('Action unavailable. Please refresh the page.');
        return;
    }

    setTechnicianClockLoading(true);

    var url = isClockedIn ? clockOutUrl : clockInUrl;
    if (!url) {
        setTechnicianClockLoading(false);
        return;
    }

    salonApi.post(url, {})
        .then(function (res) {
            if (isClockedIn) {
                setTechnicianClockState(false);
                showSuccessMessage('Clocked out successfully.');
            } else {
                var clockInIso = res && res.data ? res.data.clock_in : null;
                setTechnicianClockState(true, clockInIso);
                showSuccessMessage('Clocked in successfully.');
            }
        })
        .catch(function (err) {
            console.error(err);
            showSuccessMessage((err && err.message) ? err.message : 'Request failed.');
        })
        .finally(function () {
            setTechnicianClockLoading(false);
            updateClockDateTime();
        });
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

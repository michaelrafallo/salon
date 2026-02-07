@extends('layouts.salon')

@section('content')
@php
    $apiTurnTrackerUrl = url('api/salon/turn-tracker');
    $apiSettingsUrl = url('api/salon/settings');
@endphp
<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0" data-settings-url="{{ $apiSettingsUrl }}">
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1">Turn Tracker</h1>
            <p class="text-gray-500 text-sm">View technicians and their service counts. Edit counts below.</p>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Technicians</h2>
            </div>
            <div id="techniciansContainer" class="space-y-2">
                <div class="text-center py-8 text-gray-500"><p>Loading technicians...</p></div>
            </div>
        </div>
    </div>
</main>
@push('scripts')
<script>
(function() {
var apiTurnTrackerUrl = '{{ $apiTurnTrackerUrl }}';
window.salonTurnTrackerBootstrap = window.salonTurnTrackerBootstrap || @json($turnTrackerBootstrap ?? null);
var techniciansData = [];
var saveTimeout = null;
var turnTrackerOrder = 'lowest';

function salonTurnTrackerSortCompare(a, b) {
    var diff = (a.serviceCount || 0) - (b.serviceCount || 0);
    if (turnTrackerOrder === 'highest') {
        diff = -diff;
    }
    if (diff !== 0) return diff;
    var aTime = a.clockIn ? new Date(a.clockIn).getTime() : 0;
    var bTime = b.clockIn ? new Date(b.clockIn).getTime() : 0;
    return aTime - bTime;
}

function salonTurnTrackerApplyPayload(data) {
    var container = document.getElementById('techniciansContainer');
    if (!data || typeof data !== 'object') return;
    var entries = data.entries || [];
    turnTrackerOrder = data.turn_tracker_order === 'highest' ? 'highest' : 'lowest';
    techniciansData = entries.map(function(e) {
        return {
            id: e.user_id,
            user_id: e.user_id,
            fullName: e.fullName || ((e.firstName || '') + ' ' + (e.lastName || '')).trim() || 'Technician',
            initials: e.initials || (((e.firstName || '')[0] || '') + ((e.lastName || '')[0] || '')).toUpperCase() || '—',
            photo: e.photo || null,
            serviceCount: typeof e.services === 'number' ? e.services : parseInt(e.services, 10) || 0,
            clockIn: e.clock_in || null,
            clockInDisplay: e.clock_in_display || null
        };
    });
    techniciansData.sort(salonTurnTrackerSortCompare);
    salonTurnTrackerRenderTechnicians();
    if (container && techniciansData.length === 0) {
        container.innerHTML = '<div class="text-center py-8 text-gray-500"><p>No technicians found.</p></div>';
    }
}

function salonTurnTrackerLoadData() {
    var container = document.getElementById('techniciansContainer');
    var opts = { credentials: 'same-origin' };
    fetch(apiTurnTrackerUrl, opts)
        .then(function(r) {
            if (!r.ok) throw new Error('Failed to load turn tracker');
            return r.json();
        })
        .then(function(data) {
            salonTurnTrackerApplyPayload(data || {});
        })
        .catch(function(err) {
            console.error(err);
            if (container) container.innerHTML = '<div class="text-center py-8 text-red-500"><p>Error loading data. Please try again later.</p></div>';
        });
}

function salonTurnTrackerSaveToServer() {
    if (typeof salonApi === 'undefined' || !salonApi.put) return;
    var entries = techniciansData.map(function(t) {
        return { user_id: t.id, services: t.serviceCount };
    });
    salonApi.put(apiTurnTrackerUrl, { entries: entries }).catch(function(err) {
        console.error('Turn tracker save failed:', err);
        if (typeof showErrorMessage === 'function') showErrorMessage(err.message || 'Failed to save.');
    });
}

function salonTurnTrackerDebouncedSave() {
    if (saveTimeout) clearTimeout(saveTimeout);
    saveTimeout = setTimeout(salonTurnTrackerSaveToServer, 400);
}

window.salonTurnTrackerUpdateServiceCount = function(technicianId, newCount, showToast) {
    var tech = techniciansData.find(function(t) { return t.id === technicianId || t.id === parseInt(technicianId, 10); });
    if (tech) {
        tech.serviceCount = parseInt(newCount, 10) || 0;
        salonTurnTrackerDebouncedSave();
        if (techniciansData.length > 0) {
            techniciansData.sort(salonTurnTrackerSortCompare);
            salonTurnTrackerRenderTechnicians();
        }
        if (showToast) salonTurnTrackerShowServiceCountToast(tech.fullName, tech.serviceCount);
    }
};

window.salonTurnTrackerSaveServiceCount = function(technicianId) {
    var input = document.querySelector('input[data-technician-id="' + technicianId + '"]');
    if (input) {
        var newCount = parseInt(input.value, 10) || 0;
        salonTurnTrackerUpdateServiceCount(technicianId, newCount, true);
    }
};

function salonTurnTrackerShowServiceCountToast(technicianName, serviceCount) {
    var existing = document.getElementById('serviceCountToast');
    if (existing) existing.remove();
    var toast = document.createElement('div');
    toast.id = 'serviceCountToast';
    toast.className = 'fixed bottom-4 left-4 bg-[#003047] text-white px-4 py-3 rounded-lg shadow-lg z-50 transition-all duration-300';
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(-100px)';
    toast.innerHTML = '<div class="flex items-center gap-3"><svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg><div><p class="font-semibold text-sm">Service count updated</p><p class="text-xs opacity-90">' + (technicianName || '').replace(/</g, '&lt;') + ': ' + serviceCount + ' ' + (serviceCount === 1 ? 'service' : 'services') + '</p></div></div>';
    document.body.appendChild(toast);
    setTimeout(function() { toast.style.opacity = '1'; toast.style.transform = 'translateX(0)'; }, 10);
    setTimeout(function() {
        toast.style.opacity = '0'; toast.style.transform = 'translateX(-100px)';
        setTimeout(function() { if (toast.parentNode) toast.remove(); }, 300);
    }, 3000);
}

function salonTurnTrackerFormatClockIn(isoString, displayString) {
    if (displayString) return displayString;
    if (!isoString) return '--';
    var d = new Date(isoString);
    if (isNaN(d.getTime())) return '--';
    return d.toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function salonTurnTrackerRenderTechnicians() {
    var container = document.getElementById('techniciansContainer');
    if (!container) return;
    if (techniciansData.length === 0) {
        container.innerHTML = '<div class="text-center py-8 text-gray-500"><p>No technicians found.</p></div>';
        return;
    }
    var html = '';
    techniciansData.forEach(function(tech, index) {
        var clockInLabel = salonTurnTrackerFormatClockIn(tech.clockIn, tech.clockInDisplay);
        var photoHtml = tech.photo
            ? '<img src="' + (tech.photo || '').replace(/"/g, '&quot;') + '" alt="" class="w-10 h-10 rounded-full object-cover border border-gray-200">'
            : '<div class="w-10 h-10 bg-[#e6f0f3] rounded-full flex items-center justify-center border border-gray-200"><span class="text-xs font-bold text-[#003047]">' + (tech.initials || '—') + '</span></div>';
        html += '<div class="technician-item flex items-center gap-3 p-3 rounded-lg border border-gray-200 bg-gray-50 hover:bg-gray-100 transition-all" data-index="' + index + '" data-technician-id="' + tech.id + '"><div class="flex-shrink-0 w-8 h-8 rounded-full bg-[#003047] text-white flex items-center justify-center font-bold text-sm">' + (index + 1) + '</div><div class="flex-shrink-0">' + photoHtml + '</div><div class="flex-1 min-w-0"><p class="font-semibold text-gray-900 text-sm truncate">' + (tech.fullName || '').replace(/</g, '&lt;') + '</p><p class="text-xs text-gray-500 truncate">Clock In: ' + String(clockInLabel).replace(/</g, '&lt;') + '</p></div><div class="flex-shrink-0 flex items-center gap-2"><span class="text-xs text-gray-600">Services:</span><input type="number" value="' + tech.serviceCount + '" min="0" class="w-20 px-2 py-1 text-sm font-semibold text-[#003047] border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" onchange="salonTurnTrackerUpdateServiceCount(' + tech.id + ', this.value)" onblur="salonTurnTrackerSaveServiceCount(' + tech.id + ')" data-technician-id="' + tech.id + '"></div></div>';
    });
    container.innerHTML = html;
}

document.addEventListener('DOMContentLoaded', function() {
    if (window.salonTurnTrackerBootstrap && window.salonTurnTrackerBootstrap.entries) {
        salonTurnTrackerApplyPayload(window.salonTurnTrackerBootstrap);
    } else {
        salonTurnTrackerLoadData();
    }
    setInterval(salonTurnTrackerLoadData, 60000);
});
})();
</script>
@endpush
@endsection

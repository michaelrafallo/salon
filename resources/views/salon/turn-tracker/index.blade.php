@extends('layouts.salon')

@section('content')
<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1">Turn Tracker</h1>
            <p class="text-gray-500 text-sm">View all technicians and their service counts</p>
        </div>
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Technicians</h2>
            <div id="techniciansContainer" class="space-y-2">
                <div class="text-center py-8 text-gray-500"><p>Loading technicians...</p></div>
            </div>
        </div>
    </div>
</main>
<style>
.drop-highlight {
    background-color: #e6f0f3 !important;
    border-color: #003047 !important;
    border-width: 3px !important;
    transform: scale(1.02);
    transition: all 0.3s ease;
}
</style>
@push('scripts')
<script>
(function() {
var base = window.salonJsonBase || '{{ url("json") }}';
var techniciansList = [], techniciansData = [], bookingsData = [], draggedIndex = null, draggedElement = null;
async function salonTurnTrackerLoadData() {
    try {
        var techRes = await fetch(base + '/users.json');
        var bookRes = await fetch(base + '/booking.json');
        var techJson = await techRes.json();
        var bookJson = await bookRes.json();
        techniciansList = (techJson.users || []).filter(function(u) { return u.role === 'technician'; });
        bookingsData = bookJson.bookings || [];
        salonTurnTrackerCalculateServiceCounts();
        salonTurnTrackerLoadServiceCounts();
        salonTurnTrackerRenderTechnicians();
    } catch (err) {
        console.error(err);
        var container = document.getElementById('techniciansContainer');
        if (container) container.innerHTML = '<div class="text-center py-8 text-red-500"><p>Error loading data. Please try again later.</p></div>';
    }
}
function salonTurnTrackerCalculateServiceCounts() {
    techniciansData = techniciansList.map(function(tech) {
        var fullName = tech.firstName + ' ' + tech.lastName;
        var serviceCount = 0;
        bookingsData.forEach(function(booking) {
            var techs = Array.isArray(booking.technician) ? booking.technician : [booking.technician];
            if (techs.indexOf(fullName) >= 0) {
                if (booking.services && Array.isArray(booking.services)) {
                    booking.services.forEach(function(s) { serviceCount += s.quantity || 1; });
                } else {
                    serviceCount += 1;
                }
            }
        });
        return {
            id: tech.id,
            firstName: tech.firstName,
            lastName: tech.lastName,
            fullName: fullName,
            photo: tech.photo || tech.profilePhoto || null,
            initials: tech.initials || (tech.firstName || '')[0] + (tech.lastName || '')[0],
            serviceCount: serviceCount
        };
    });
    var savedOrder = localStorage.getItem('turnTrackerOrder');
    if (savedOrder) {
        try {
            var order = JSON.parse(savedOrder);
            var map = {}, ordered = [];
            techniciansData.forEach(function(t) { map[t.id] = t; });
            order.forEach(function(id) {
                if (map[id]) {
                    ordered.push(map[id]);
                    delete map[id];
                }
            });
            Object.keys(map).forEach(function(k) { ordered.push(map[k]); });
            techniciansData = ordered;
        } catch (e) {
            console.error('Error loading saved order:', e);
            techniciansData.sort(function(a, b) {
                if (a.serviceCount !== b.serviceCount) return a.serviceCount - b.serviceCount;
                return a.fullName.localeCompare(b.fullName);
            });
        }
    } else {
        techniciansData.sort(function(a, b) {
            if (a.serviceCount !== b.serviceCount) return a.serviceCount - b.serviceCount;
            return a.fullName.localeCompare(b.fullName);
        });
    }
}
function salonTurnTrackerSaveOrder() {
    var order = techniciansData.map(function(t) { return t.id; });
    localStorage.setItem('turnTrackerOrder', JSON.stringify(order));
}
function salonTurnTrackerSaveServiceCounts() {
    var counts = {};
    techniciansData.forEach(function(tech) { counts[tech.id] = tech.serviceCount; });
    localStorage.setItem('turnTrackerServiceCounts', JSON.stringify(counts));
}
function salonTurnTrackerLoadServiceCounts() {
    var saved = localStorage.getItem('turnTrackerServiceCounts');
    if (saved) {
        try {
            var counts = JSON.parse(saved);
            techniciansData.forEach(function(tech) {
                if (counts.hasOwnProperty(tech.id)) {
                    tech.serviceCount = counts[tech.id];
                }
            });
        } catch (e) {
            console.error('Error loading saved service counts:', e);
        }
    }
}
window.salonTurnTrackerUpdateServiceCount = function(technicianId, newCount, showToast) {
    var tech = techniciansData.find(function(t) { return t.id === technicianId; });
    if (tech) {
        tech.serviceCount = parseInt(newCount, 10) || 0;
        salonTurnTrackerSaveServiceCounts();
        if (showToast) {
            salonTurnTrackerShowServiceCountToast(tech.fullName, tech.serviceCount);
        }
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
    toast.innerHTML = '<div class="flex items-center gap-3"><svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg><div><p class="font-semibold text-sm">Service count updated</p><p class="text-xs opacity-90">' + technicianName + ': ' + serviceCount + ' ' + (serviceCount === 1 ? 'service' : 'services') + '</p></div></div>';
    document.body.appendChild(toast);
    setTimeout(function() {
        toast.style.opacity = '1';
        toast.style.transform = 'translateX(0)';
    }, 10);
    setTimeout(function() {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(-100px)';
        setTimeout(function() {
            if (toast.parentNode) toast.remove();
        }, 300);
    }, 3000);
}
function salonTurnTrackerShowReorderToast(technicianName, oldPosition, newPosition) {
    var existing = document.getElementById('reorderToast');
    if (existing) existing.remove();
    var toast = document.createElement('div');
    toast.id = 'reorderToast';
    toast.className = 'fixed bottom-4 left-4 bg-[#003047] text-white px-4 py-3 rounded-lg shadow-lg z-50 transition-all duration-300';
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(-100px)';
    var positionText = 'Position ' + oldPosition + ' → ' + newPosition;
    toast.innerHTML = '<div class="flex items-center gap-3"><svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"></path></svg><div><p class="font-semibold text-sm">Order updated</p><p class="text-xs opacity-90">' + technicianName + ': ' + positionText + '</p></div></div>';
    document.body.appendChild(toast);
    setTimeout(function() {
        toast.style.opacity = '1';
        toast.style.transform = 'translateX(0)';
    }, 10);
    setTimeout(function() {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(-100px)';
        setTimeout(function() {
            if (toast.parentNode) toast.remove();
        }, 300);
    }, 3000);
}
function salonTurnTrackerDisableDragForItem(event) {
    var item = event.target.closest('.technician-item');
    if (item) {
        item.draggable = false;
        item.classList.add('cursor-text');
    }
}
function salonTurnTrackerEnableDragForItem(event) {
    var item = event.target.closest('.technician-item');
    if (item) {
        item.draggable = true;
        item.classList.remove('cursor-text');
    }
}
window.salonTurnTrackerHandleDragStart = function(event, index) {
    if (event.target.tagName === 'INPUT' || event.target.closest('input')) {
        event.preventDefault();
        return false;
    }
    draggedIndex = index;
    draggedElement = event.currentTarget;
    event.currentTarget.classList.add('opacity-50', 'border-[#003047]', 'bg-[#e6f0f3]');
    event.dataTransfer.effectAllowed = 'move';
    event.dataTransfer.setData('text/html', event.currentTarget.outerHTML);
};
window.salonTurnTrackerHandleDragOver = function(event) {
    event.preventDefault();
    event.dataTransfer.dropEffect = 'move';
    return false;
};
window.salonTurnTrackerHandleDragEnter = function(event) {
    if (event.currentTarget !== draggedElement) {
        event.currentTarget.classList.add('border-[#003047]', 'bg-blue-50');
    }
};
window.salonTurnTrackerHandleDragLeave = function(event) {
    if (event.currentTarget !== draggedElement) {
        event.currentTarget.classList.remove('border-[#003047]', 'bg-blue-50');
    }
};
window.salonTurnTrackerHandleDrop = function(event, dropIndex) {
    event.preventDefault();
    event.stopPropagation();
    if (draggedIndex === null || draggedIndex === dropIndex) return false;
    event.currentTarget.classList.remove('border-[#003047]', 'bg-blue-50');
    var dragged = techniciansData[draggedIndex];
    var oldPosition = draggedIndex + 1;
    var newPosition = dropIndex + 1;
    techniciansData.splice(draggedIndex, 1);
    techniciansData.splice(dropIndex, 0, dragged);
    salonTurnTrackerSaveOrder();
    salonTurnTrackerRenderTechnicians();
    salonTurnTrackerShowReorderToast(dragged.fullName, oldPosition, newPosition);
    setTimeout(function() {
        var droppedItem = document.querySelector('[data-technician-id="' + dragged.id + '"]');
        if (droppedItem) {
            droppedItem.classList.add('drop-highlight');
            setTimeout(function() {
                droppedItem.classList.remove('drop-highlight');
            }, 1000);
        }
    }, 10);
    return false;
};
window.salonTurnTrackerHandleDragEnd = function(event) {
    var items = document.querySelectorAll('.technician-item');
    items.forEach(function(item) {
        item.classList.remove('opacity-50', 'border-[#003047]', 'bg-[#e6f0f3]', 'bg-blue-50');
    });
    draggedElement = null;
    draggedIndex = null;
};
function salonTurnTrackerRenderTechnicians() {
    var container = document.getElementById('techniciansContainer');
    if (!container) return;
    if (techniciansData.length === 0) {
        container.innerHTML = '<div class="text-center py-8 text-gray-500"><p>No technicians found.</p></div>';
        return;
    }
    var html = '';
    techniciansData.forEach(function(tech, index) {
        html += '<div class="technician-item flex items-center gap-2 p-2 rounded-lg border border-gray-200 bg-gray-50 hover:bg-gray-100 transition-all cursor-move" draggable="true" data-index="' + index + '" data-technician-id="' + tech.id + '" ondragstart="salonTurnTrackerHandleDragStart(event, ' + index + ')" ondragover="salonTurnTrackerHandleDragOver(event)" ondrop="salonTurnTrackerHandleDrop(event, ' + index + ')" ondragend="salonTurnTrackerHandleDragEnd(event)" ondragenter="salonTurnTrackerHandleDragEnter(event)" ondragleave="salonTurnTrackerHandleDragLeave(event)"><div class="flex-shrink-0 text-gray-400 hover:text-gray-600"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path></svg></div><div class="flex-shrink-0 w-8 h-8 rounded-full bg-[#003047] text-white flex items-center justify-center font-bold text-sm">' + (index + 1) + '</div><div class="flex-shrink-0">' + (tech.photo ? '<img src="' + tech.photo + '" alt="' + tech.fullName + '" class="w-10 h-10 rounded-full object-cover border border-gray-200">' : '<div class="w-10 h-10 bg-[#e6f0f3] rounded-full flex items-center justify-center border border-gray-200"><span class="text-xs font-bold text-[#003047]">' + tech.initials + '</span></div>') + '</div><div class="flex-1 min-w-0"><p class="font-semibold text-gray-900 text-sm truncate">' + tech.fullName + '</p></div><div class="flex-shrink-0 flex items-center gap-2" onclick="event.stopPropagation()" onmousedown="event.stopPropagation()"><span class="text-xs text-gray-600">Services:</span><input type="number" value="' + tech.serviceCount + '" min="0" class="w-20 px-2 py-1 text-sm font-semibold text-[#003047] border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" onchange="salonTurnTrackerUpdateServiceCount(' + tech.id + ', this.value)" onblur="salonTurnTrackerSaveServiceCount(' + tech.id + '); salonTurnTrackerEnableDragForItem(event);" onclick="event.stopPropagation()" onmousedown="event.stopPropagation()" onfocus="salonTurnTrackerDisableDragForItem(event)" data-technician-id="' + tech.id + '"></div></div>';
    });
    container.innerHTML = html;
}
document.addEventListener('DOMContentLoaded', function() {
    salonTurnTrackerLoadData();
    setInterval(function() {
        salonTurnTrackerLoadData();
    }, 30000);
});
})();
</script>
@endpush
@endsection

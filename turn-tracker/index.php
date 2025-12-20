<?php
$pageTitle = 'Turn Tracker';
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/modal.php';

// Get current role (default to 'admin')
$current_role = isset($_SESSION['selected_role']) ? $_SESSION['selected_role'] : 'admin';
$isTechnician = ($current_role === 'technician');
?>

<script>
    // Pass PHP role to JavaScript
    const currentUserRole = '<?php echo $current_role; ?>';
    const isTechnicianUser = <?php echo $isTechnician ? 'true' : 'false'; ?>;
</script>

<!-- Main Content Area -->
<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1">Turn Tracker</h1>
            <p class="text-gray-500 text-sm">View all technicians and their service counts</p>
        </div>

        <!-- Technicians List Section -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Technicians</h2>
            
            <div id="techniciansContainer" class="space-y-2">
                <!-- Technicians will be populated by JavaScript -->
                <div class="text-center py-8 text-gray-500">
                    <p>Loading technicians...</p>
                </div>
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

<script>
let techniciansList = [];
let techniciansData = [];
let bookingsData = [];
let draggedElement = null;
let draggedIndex = null;

// Load technicians and bookings
async function loadData() {
    try {
        const [techniciansResponse, bookingsResponse] = await Promise.all([
            fetch('../json/users.json'),
            fetch('../json/booking.json')
        ]);
        
        const techniciansJson = await techniciansResponse.json();
        const bookingsJson = await bookingsResponse.json();
        
        techniciansList = (techniciansJson.users || []).filter(tech => tech.role === 'technician');
        bookingsData = bookingsJson.bookings || [];
        
        // Calculate service counts for each technician
        calculateServiceCounts();
        
        // Render technicians (order is handled in calculateServiceCounts)
        renderTechnicians();
    } catch (error) {
        console.error('Error loading data:', error);
        document.getElementById('techniciansContainer').innerHTML = `
            <div class="text-center py-8 text-red-500">
                <p>Error loading data. Please try again later.</p>
            </div>
        `;
    }
}

// Calculate service counts for each technician
function calculateServiceCounts() {
    techniciansData = techniciansList.map(technician => {
        const fullName = `${technician.firstName} ${technician.lastName}`;
        let serviceCount = 0;
        
        // Count services from bookings where this technician is assigned
        bookingsData.forEach(booking => {
            const technicians = Array.isArray(booking.technician) ? booking.technician : [booking.technician];
            
            if (technicians.includes(fullName)) {
                // Count the number of services in this booking
                if (booking.services && Array.isArray(booking.services)) {
                    booking.services.forEach(service => {
                        serviceCount += service.quantity || 1;
                    });
                } else {
                    // If no services array, count as 1 service
                    serviceCount += 1;
                }
            }
        });
        
        return {
            id: technician.id,
            firstName: technician.firstName,
            lastName: technician.lastName,
            fullName: fullName,
            photo: technician.photo || technician.profilePhoto || null,
            initials: technician.initials || (technician.firstName?.[0] || '') + (technician.lastName?.[0] || ''),
            serviceCount: serviceCount
        };
    });
    
    // Load custom order from localStorage if it exists
    const savedOrder = localStorage.getItem('turnTrackerOrder');
    if (savedOrder) {
        try {
            const orderArray = JSON.parse(savedOrder);
            // Reorder techniciansData based on saved order
            const orderedData = [];
            const dataMap = new Map(techniciansData.map(tech => [tech.id, tech]));
            
            // First, add technicians in saved order
            orderArray.forEach(techId => {
                const tech = dataMap.get(techId);
                if (tech) {
                    orderedData.push(tech);
                    dataMap.delete(techId);
                }
            });
            
            // Then add any new technicians that weren't in the saved order
            dataMap.forEach(tech => {
                orderedData.push(tech);
            });
            
            techniciansData = orderedData;
        } catch (error) {
            console.error('Error loading saved order:', error);
            // Fall back to default sort
            techniciansData.sort((a, b) => {
                if (a.serviceCount !== b.serviceCount) {
                    return a.serviceCount - b.serviceCount;
                }
                return a.fullName.localeCompare(b.fullName);
            });
        }
    } else {
        // Default sort by service count (ascending - lowest first), then by name
        techniciansData.sort((a, b) => {
            if (a.serviceCount !== b.serviceCount) {
                return a.serviceCount - b.serviceCount;
            }
            return a.fullName.localeCompare(b.fullName);
        });
    }
}

// Save custom order to localStorage
function saveOrder() {
    const orderArray = techniciansData.map(tech => tech.id);
    localStorage.setItem('turnTrackerOrder', JSON.stringify(orderArray));
}

// Render technicians list
function renderTechnicians() {
    const container = document.getElementById('techniciansContainer');
    if (!container) return;
    
    if (techniciansData.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8 text-gray-500">
                <p>No technicians found.</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    techniciansData.forEach((technician, index) => {
        html += `
            <div class="technician-item flex items-center gap-2 p-2 rounded-lg border border-gray-200 bg-gray-50 hover:bg-gray-100 transition-all cursor-move" 
                 draggable="true" 
                 data-index="${index}"
                 data-technician-id="${technician.id}"
                 ondragstart="handleDragStart(event, ${index})"
                 ondragover="handleDragOver(event)"
                 ondrop="handleDrop(event, ${index})"
                 ondragend="handleDragEnd(event)"
                 ondragenter="handleDragEnter(event)"
                 ondragleave="handleDragLeave(event)">
                <!-- Drag Handle Icon -->
                <div class="flex-shrink-0 text-gray-400 hover:text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                    </svg>
                </div>
                
                <!-- Position Number -->
                <div class="flex-shrink-0 w-8 h-8 rounded-full bg-[#003047] text-white flex items-center justify-center font-bold text-sm">
                    ${index + 1}
                </div>
                
                <!-- Profile Picture -->
                <div class="flex-shrink-0">
                    ${technician.photo ? 
                        `<img src="${technician.photo}" alt="${technician.fullName}" class="w-10 h-10 rounded-full object-cover border border-gray-200">` :
                        `<div class="w-10 h-10 bg-[#e6f0f3] rounded-full flex items-center justify-center border border-gray-200">
                            <span class="text-xs font-bold text-[#003047]">${technician.initials}</span>
                        </div>`
                    }
                </div>
                
                <!-- Technician Info -->
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900 text-sm truncate">${technician.fullName}</p>
                    <p class="text-xs text-gray-600">Services: <span class="font-semibold text-[#003047]">${technician.serviceCount}</span></p>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

// Drag and Drop Handlers
function handleDragStart(event, index) {
    draggedIndex = index;
    draggedElement = event.currentTarget;
    event.currentTarget.classList.add('opacity-50', 'border-[#003047]', 'bg-[#e6f0f3]');
    event.dataTransfer.effectAllowed = 'move';
    event.dataTransfer.setData('text/html', event.currentTarget.outerHTML);
}

function handleDragOver(event) {
    event.preventDefault();
    event.dataTransfer.dropEffect = 'move';
    return false;
}

function handleDragEnter(event) {
    if (event.currentTarget !== draggedElement) {
        event.currentTarget.classList.add('border-[#003047]', 'bg-blue-50');
    }
}

function handleDragLeave(event) {
    if (event.currentTarget !== draggedElement) {
        event.currentTarget.classList.remove('border-[#003047]', 'bg-blue-50');
    }
}

function handleDrop(event, dropIndex) {
    event.preventDefault();
    event.stopPropagation();
    
    if (draggedIndex === null || draggedIndex === dropIndex) {
        return false;
    }
    
    // Remove visual feedback
    event.currentTarget.classList.remove('border-[#003047]', 'bg-blue-50');
    
    // Reorder array
    const draggedTech = techniciansData[draggedIndex];
    techniciansData.splice(draggedIndex, 1);
    techniciansData.splice(dropIndex, 0, draggedTech);
    
    // Save new order
    saveOrder();
    
    // Re-render
    renderTechnicians();
    
    // Highlight the dropped item after re-render
    setTimeout(() => {
        const droppedItem = document.querySelector(`[data-technician-id="${draggedTech.id}"]`);
        if (droppedItem) {
            // Add highlight effect (no shadow)
            droppedItem.classList.add('drop-highlight');
            
            // Remove highlight after 1 second
            setTimeout(() => {
                droppedItem.classList.remove('drop-highlight');
            }, 1000);
        }
    }, 10);
    
    return false;
}

function handleDragEnd(event) {
    // Remove all drag styling
    const items = document.querySelectorAll('.technician-item');
    items.forEach(item => {
        item.classList.remove('opacity-50', 'border-[#003047]', 'bg-[#e6f0f3]', 'bg-blue-50');
    });
    
    draggedElement = null;
    draggedIndex = null;
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadData();
    
    // Auto-refresh every 30 seconds
    setInterval(() => {
        loadData();
    }, 30000);
});
</script>

<?php include '../includes/footer.php'; ?>

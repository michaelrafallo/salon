<?php
// Get technician ID from URL parameter
$technicianId = isset($_GET['id']) ? intval($_GET['id']) : 1;

// Mock technician data - In a real application, this would come from a database
$technicians = [
    1 => [
        'id' => 1,
        'first_name' => 'Maria',
        'last_name' => 'Garcia',
        'email' => 'maria.g@salon.com',
        'phone' => '(555) 111-2222',
        'title' => 'Senior Technician',
        'status' => 'Available',
        'status_color' => 'green',
        'avatar_color' => 'pink',
        'initials' => 'MG',
        'today_services' => 8,
        'current_queue' => 2,
        'total_services' => 245,
        'total_earnings' => 12250.00,
        'customers' => 45,
        'total' => 280.00,
        'tip' => 45.00,
        'commission' => 84.00,
        'clock_in' => 'Nov. 23, 2025 8:00 AM',
        'clock_out' => '--',
    ],
    2 => [
        'id' => 2,
        'first_name' => 'Lisa',
        'last_name' => 'Wong',
        'email' => 'lisa.w@salon.com',
        'phone' => '(555) 222-3333',
        'title' => 'Technician',
        'status' => 'Busy',
        'status_color' => 'yellow',
        'avatar_color' => 'purple',
        'initials' => 'LW',
        'today_services' => 6,
        'current_queue' => 1,
        'total_services' => 180,
        'total_earnings' => 9000.00,
        'customers' => 38,
        'total' => 210.00,
        'tip' => 35.00,
        'commission' => 63.00,
        'clock_in' => 'Nov. 22, 2025 7:30 AM',
        'clock_out' => '--',
    ],
    3 => [
        'id' => 3,
        'first_name' => 'Anna',
        'last_name' => 'Kim',
        'email' => 'anna.k@salon.com',
        'phone' => '(555) 333-4444',
        'title' => 'Technician',
        'status' => 'Available',
        'status_color' => 'green',
        'avatar_color' => 'teal',
        'initials' => 'AK',
        'today_services' => 5,
        'current_queue' => 0,
        'total_services' => 165,
        'total_earnings' => 8250.00,
        'customers' => 32,
        'total' => 175.00,
        'tip' => 28.00,
        'commission' => 52.50,
        'clock_in' => 'Nov. 24, 2025 9:15 AM',
        'clock_out' => '--',
    ],
    4 => [
        'id' => 4,
        'first_name' => 'Sarah',
        'last_name' => 'Lee',
        'email' => 'sarah.l@salon.com',
        'phone' => '(555) 444-5555',
        'title' => 'Junior Technician',
        'status' => 'Available',
        'status_color' => 'green',
        'avatar_color' => 'indigo',
        'initials' => 'SL',
        'today_services' => 4,
        'current_queue' => 1,
        'total_services' => 95,
        'total_earnings' => 4750.00,
        'customers' => 28,
        'total' => 140.00,
        'tip' => 22.00,
        'commission' => 42.00,
        'clock_in' => 'Nov. 21, 2025 8:45 AM',
        'clock_out' => '--',
    ]
];

// Get technician data
$technician = isset($technicians[$technicianId]) ? $technicians[$technicianId] : $technicians[1];

$pageTitle = $technician['first_name'] . ' ' . $technician['last_name'];
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/modal.php';
?>

<!-- Main Content Area -->
<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="index.php" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span class="text-sm font-medium">Back to Technicians</span>
            </a>
        </div>

        <!-- Technician Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-start gap-6 flex-1">
                    <?php
                    $avatarBgClass = $technician['avatar_color'] === 'pink' ? 'bg-[#e6f0f3]' : 
                                     ($technician['avatar_color'] === 'purple' ? 'bg-purple-100' : 
                                     ($technician['avatar_color'] === 'teal' ? 'bg-teal-100' : 'bg-indigo-100'));
                    $avatarTextClass = $technician['avatar_color'] === 'pink' ? 'text-[#003047]' : 
                                       ($technician['avatar_color'] === 'purple' ? 'text-purple-600' : 
                                       ($technician['avatar_color'] === 'teal' ? 'text-teal-600' : 'text-indigo-600'));
                    $statusBgClass = $technician['status_color'] === 'green' ? 'bg-green-100 text-green-700' : 
                                     ($technician['status_color'] === 'yellow' ? 'bg-[#e6f0f3] text-[#003047]' : 'bg-gray-100 text-gray-700');
                    ?>
                    <div class="w-24 h-24 <?php echo $avatarBgClass; ?> rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-4xl font-bold <?php echo $avatarTextClass; ?>"><?php echo htmlspecialchars($technician['initials']); ?></span>
                    </div>
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-gray-900 mb-2"><?php echo htmlspecialchars($technician['first_name'] . ' ' . $technician['last_name']); ?></h1>
                        <p class="text-lg text-gray-600 mb-2"><?php echo htmlspecialchars($technician['title']); ?></p>
                        <span class="px-3 py-1 <?php echo $statusBgClass; ?> text-xs font-medium rounded mb-4 inline-block"><?php echo htmlspecialchars($technician['status']); ?></span>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Email</p>
                                <p class="text-base font-medium text-gray-900" id="technicianEmail"><?php echo htmlspecialchars($technician['email']); ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Phone</p>
                                <p class="text-base font-medium text-gray-900" id="technicianPhone"><?php echo htmlspecialchars($technician['phone']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                <button onclick="openEditTechnicianModal()" class="px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95 text-sm">
                    Edit Technician
                </button>
                </div>
            </div>
        </div>


        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <p class="text-sm text-gray-500 mb-2">Customers</p>
                <p class="text-3xl font-bold text-gray-900"><?php echo $technician['customers']; ?></p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <p class="text-sm text-gray-500 mb-2">Total</p>
                <p class="text-3xl font-bold text-gray-900">$<?php echo number_format($technician['total'], 2); ?></p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <p class="text-sm text-gray-500 mb-2">Tip</p>
                <p class="text-3xl font-bold text-gray-900">$<?php echo number_format($technician['tip'], 2); ?></p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <p class="text-sm text-gray-500 mb-2">Commission</p>
                <p class="text-3xl font-bold text-gray-900">$<?php echo number_format($technician['commission'], 2); ?></p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <p class="text-sm text-gray-500 mb-2">Clock</p>
                <div class="space-y-2">
                    <div>
                        <p class="text-xs text-gray-500">In:</p>
                        <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($technician['clock_in']); ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Out:</p>
                        <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($technician['clock_out']); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Commissions Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-xl font-bold text-gray-900">Commissions</h2>
            </div>
            
            <!-- Date Range Selector -->
            <div class="mb-6 bg-gray-50 rounded-xl p-4 border border-gray-200">
                <div class="flex flex-col lg:flex-row items-start lg:items-center gap-4">
                    <!-- Date Inputs Group -->
                    <div class="flex items-center gap-3 flex-1 w-full lg:w-auto">
                        <div class="flex items-center gap-2 bg-white border border-gray-300 rounded-lg px-3 py-2 shadow-sm hover:border-[#003047] transition-colors flex-1 lg:flex-initial">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <input type="date" id="dateRangeFrom" onchange="applyDateRangeFilter()" class="flex-1 text-sm font-medium text-gray-700 border-0 focus:outline-none focus:ring-0 bg-transparent">
                        </div>
                        <span class="text-gray-400 font-medium hidden sm:inline">to</span>
                        <div class="flex items-center gap-2 bg-white border border-gray-300 rounded-lg px-3 py-2 shadow-sm hover:border-[#003047] transition-colors flex-1 lg:flex-initial">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <input type="date" id="dateRangeTo" onchange="applyDateRangeFilter()" class="flex-1 text-sm font-medium text-gray-700 border-0 focus:outline-none focus:ring-0 bg-transparent">
                        </div>
                    </div>
                    
                    <!-- Quick Preset Buttons -->
                    <div class="flex items-center gap-2 flex-wrap">
                        <button onclick="setDateRange('today', this)" class="date-preset-btn px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-[#003047] hover:text-white hover:border-[#003047] transition-all active:scale-95 shadow-sm">
                            Today
                        </button>
                        <button onclick="setDateRange('week', this)" class="date-preset-btn px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-[#003047] hover:text-white hover:border-[#003047] transition-all active:scale-95 shadow-sm">
                            This Week
                        </button>
                        <button onclick="setDateRange('month', this)" class="date-preset-btn px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-[#003047] hover:text-white hover:border-[#003047] transition-all active:scale-95 shadow-sm">
                            This Month
                        </button>
                        <button onclick="setDateRange('year', this)" class="date-preset-btn px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-[#003047] hover:text-white hover:border-[#003047] transition-all active:scale-95 shadow-sm">
                            This Year
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-700">Date</th>
                            <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700">Total</th>
                            <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700">Tip</th>
                            <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700">Commission</th>
                            <th class="text-center py-3 px-4 text-sm font-semibold text-gray-700">Action</th>
                        </tr>
                    </thead>
                    <tbody id="commissionsTableBody">
                        <!-- Commissions will be populated by JavaScript -->
                        <tr>
                            <td colspan="5" class="text-center py-8 text-gray-500">
                                <div class="flex items-center justify-center">
                                    <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-[#003047]"></div>
                                    <span class="ml-3">Loading commissions...</span>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script>
const technicianData = <?php echo json_encode($technician); ?>;
let commissionsData = [];
let filteredCommissionsData = [];
let commissionRate = 0.30; // Default 30% commission rate
let dateRangeFrom = null;
let dateRangeTo = null;

// Fetch commissions data
async function fetchCommissions() {
    try {
        const response = await fetch('../json/booking.json');
        const data = await response.json();
        const bookings = data.bookings || [];
        
        // Get technician full name
        const technicianFullName = `${technicianData.first_name} ${technicianData.last_name}`;
        
        // Filter bookings for this technician and calculate commissions
        commissionsData = bookings
            .filter(booking => {
                if (!booking.technician) return false;
                const technicians = Array.isArray(booking.technician) ? booking.technician : [booking.technician];
                return technicians.includes(technicianFullName);
            })
            .map(booking => {
                const total = booking.total || 0;
                // Calculate tip (assuming 15% of total as tip)
                const tip = total * 0.15;
                // Calculate commission (30% of total by default)
                const commission = total * commissionRate;
                
                return {
                    date: booking.bookingDate || '',
                    total: total,
                    tip: tip,
                    commission: commission
                };
            })
            .sort((a, b) => new Date(b.date) - new Date(a.date)); // Sort by date descending
        
        // Read URL parameters or set default to today
        const urlParams = new URLSearchParams(window.location.search);
        const urlFrom = urlParams.get('from');
        const urlTo = urlParams.get('to');
        const urlDateType = urlParams.get('datetype');
        
        if (urlFrom && urlTo) {
            // Use URL parameters
            dateRangeFrom = urlFrom;
            dateRangeTo = urlTo;
            
            document.getElementById('dateRangeFrom').value = dateRangeFrom;
            document.getElementById('dateRangeTo').value = dateRangeTo;
            
            // Set active button based on datetype
            if (urlDateType) {
                setTimeout(() => {
                    // Remove active state from all buttons
                    document.querySelectorAll('.date-preset-btn').forEach(btn => {
                        btn.classList.remove('bg-[#003047]', 'text-white', 'border-[#003047]');
                        btn.classList.add('bg-white', 'text-gray-700', 'border-gray-300');
                    });
                    
                    // Find and activate the button matching the datetype
                    const activeButton = document.querySelector(`button[onclick*="setDateRange('${urlDateType}'"]`);
                    if (activeButton) {
                        activeButton.classList.remove('bg-white', 'text-gray-700', 'border-gray-300');
                        activeButton.classList.add('bg-[#003047]', 'text-white', 'border-[#003047]');
                    }
                }, 100);
            }
        } else {
            // Set default date range to today
            const today = new Date();
            dateRangeFrom = today.toISOString().split('T')[0];
            dateRangeTo = today.toISOString().split('T')[0];
            
            // Set date inputs
            document.getElementById('dateRangeFrom').value = dateRangeFrom;
            document.getElementById('dateRangeTo').value = dateRangeTo;
            
            // Update URL with default today
            updateURLWithDateRange(dateRangeFrom, dateRangeTo, 'today');
            
            // Set "Today" button as active
            setTimeout(() => {
                const todayButton = document.querySelector('button[onclick*="today"]');
                if (todayButton) {
                    todayButton.classList.remove('bg-white', 'text-gray-700', 'border-gray-300');
                    todayButton.classList.add('bg-[#003047]', 'text-white', 'border-[#003047]');
                }
            }, 100);
        }
        
        // Apply date range filter
        applyDateRangeFilter();
    } catch (error) {
        console.error('Error fetching commissions:', error);
        document.getElementById('commissionsTableBody').innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-8 text-red-500">
                    Failed to load commissions data
                </td>
            </tr>
        `;
    }
}

// Update URL with date range parameters
function updateURLWithDateRange(from, to, dateType = null) {
    const url = new URL(window.location);
    url.searchParams.set('from', from);
    url.searchParams.set('to', to);
    if (dateType) {
        url.searchParams.set('datetype', dateType);
    } else {
        url.searchParams.delete('datetype');
    }
    window.history.pushState({}, '', url);
}

// Set date range presets
function setDateRange(range, buttonElement) {
    const today = new Date();
    let fromDate, toDate;
    
    switch(range) {
        case 'today':
            fromDate = new Date(today);
            toDate = new Date(today);
            break;
        case 'week':
            const dayOfWeek = today.getDay();
            const diff = today.getDate() - dayOfWeek; // Get Sunday
            fromDate = new Date(today.getFullYear(), today.getMonth(), diff);
            toDate = new Date(fromDate);
            toDate.setDate(fromDate.getDate() + 6); // Saturday
            break;
        case 'month':
            fromDate = new Date(today.getFullYear(), today.getMonth(), 1);
            toDate = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            break;
        case 'year':
            fromDate = new Date(today.getFullYear(), 0, 1);
            toDate = new Date(today.getFullYear(), 11, 31);
            break;
        default:
            return;
    }
    
    dateRangeFrom = fromDate.toISOString().split('T')[0];
    dateRangeTo = toDate.toISOString().split('T')[0];
    
    document.getElementById('dateRangeFrom').value = dateRangeFrom;
    document.getElementById('dateRangeTo').value = dateRangeTo;
    
    // Update URL with date range and type
    updateURLWithDateRange(dateRangeFrom, dateRangeTo, range);
    
    // Update active preset button styling - remove active from all buttons first
    document.querySelectorAll('.date-preset-btn').forEach(btn => {
        btn.classList.remove('bg-[#003047]', 'text-white', 'border-[#003047]');
        btn.classList.add('bg-white', 'text-gray-700', 'border-gray-300');
    });

    // Highlight the clicked button - make it active
    let activeButton = null;
    if (buttonElement) {
        activeButton = buttonElement;
    } else {
        // Fallback: find button by range if buttonElement not provided
        activeButton = document.querySelector(`button[onclick*="setDateRange('${range}')"]`);
    }
    
    if (activeButton) {
        activeButton.classList.remove('bg-white', 'text-gray-700', 'border-gray-300');
        activeButton.classList.add('bg-[#003047]', 'text-white', 'border-[#003047]');
    }

    // Apply filter without clearing button states (pass true to preserve active state)
    applyDateRangeFilter(true);
}

// Filter commissions by date range
function applyDateRangeFilter(preserveButtonState = false) {
    const fromInput = document.getElementById('dateRangeFrom');
    const toInput = document.getElementById('dateRangeTo');
    
    if (!fromInput || !toInput) return;
    
    dateRangeFrom = fromInput.value;
    dateRangeTo = toInput.value;
    
    // Update URL (without datetype when manually changed)
    if (dateRangeFrom && dateRangeTo && !preserveButtonState) {
        updateURLWithDateRange(dateRangeFrom, dateRangeTo, null);
    }
    
    // Clear preset button highlights when dates are manually changed (not when called from preset buttons)
    if (!preserveButtonState) {
        document.querySelectorAll('.date-preset-btn').forEach(btn => {
            btn.classList.remove('bg-[#003047]', 'text-white', 'border-[#003047]');
            btn.classList.add('bg-white', 'text-gray-700', 'border-gray-300');
        });
    }
    
    if (!dateRangeFrom || !dateRangeTo) {
        // If no dates selected, show all
        filteredCommissionsData = commissionsData;
        renderCommissions();
        return;
    }
    
    // Filter by date range
    const fromDate = new Date(dateRangeFrom + 'T00:00:00');
    const toDate = new Date(dateRangeTo + 'T23:59:59');
    
    let filtered = commissionsData.filter(commission => {
        if (!commission.date) return false;
        const commissionDate = new Date(commission.date + 'T00:00:00');
        return commissionDate >= fromDate && commissionDate <= toDate;
    });
    
    // Group by date and aggregate totals
    const groupedByDate = {};
    filtered.forEach(commission => {
        const dateKey = commission.date;
        if (!groupedByDate[dateKey]) {
            groupedByDate[dateKey] = {
                date: dateKey,
                total: 0,
                tip: 0,
                commission: 0
            };
        }
        groupedByDate[dateKey].total += commission.total;
        groupedByDate[dateKey].tip += commission.tip;
        groupedByDate[dateKey].commission += commission.commission;
    });
    
    // Convert back to array and sort by date (descending - newest first)
    filteredCommissionsData = Object.values(groupedByDate)
        .sort((a, b) => new Date(b.date) - new Date(a.date));
    
    renderCommissions();
}

// Render commissions table
function renderCommissions() {
    const tbody = document.getElementById('commissionsTableBody');
    
    if (!filteredCommissionsData || filteredCommissionsData.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center py-8 text-gray-500">
                    No commissions found
                </td>
            </tr>
        `;
        return;
    }
    
    let html = '';
    let totalTotal = 0;
    let totalTip = 0;
    let totalCommission = 0;
    
    filteredCommissionsData.forEach(commission => {
        const date = commission.date ? new Date(commission.date + 'T00:00:00').toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        }) : 'N/A';
        
        totalTotal += commission.total;
        totalTip += commission.tip;
        totalCommission += commission.commission;
        
        html += `
            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                <td class="py-3 px-4 text-sm text-gray-900">${date}</td>
                <td class="py-3 px-4 text-sm text-gray-900 text-right font-medium">$${commission.total.toFixed(2)}</td>
                <td class="py-3 px-4 text-sm text-gray-900 text-right font-medium">$${commission.tip.toFixed(2)}</td>
                <td class="py-3 px-4 text-sm text-[#003047] text-right font-bold">$${commission.commission.toFixed(2)}</td>
                <td class="py-3 px-4 text-center">
                    <button onclick="openEditCommissionModal('${commission.date}', ${commission.total}, ${commission.tip}, ${commission.commission})" class="p-2 text-gray-600 hover:text-[#003047] hover:bg-gray-100 rounded-lg transition-colors" title="Edit Commission">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </button>
                </td>
            </tr>
        `;
    });
    
    // Add totals row
    html += `
        <tr class="border-t-2 border-gray-300 bg-gray-50">
            <td class="py-4 px-4 text-sm font-bold text-gray-900">Total</td>
            <td class="py-4 px-4 text-sm font-bold text-gray-900 text-right">$${totalTotal.toFixed(2)}</td>
            <td class="py-4 px-4 text-sm font-bold text-gray-900 text-right">$${totalTip.toFixed(2)}</td>
            <td class="py-4 px-4 text-sm font-bold text-[#003047] text-right">$${totalCommission.toFixed(2)}</td>
            <td class="py-4 px-4 text-center">
                <button onclick="showCommissionsPrintModal()" class="p-2 text-gray-600 hover:text-[#003047] hover:bg-gray-100 rounded-lg transition-colors" title="Print">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                </button>
            </td>
        </tr>
    `;
    
    tbody.innerHTML = html;
}

// Show commissions print modal
function showCommissionsPrintModal() {
    if (!filteredCommissionsData || filteredCommissionsData.length === 0) {
        alert('No commissions data to print');
        return;
    }
    
    // Get technician name
    const technicianName = `${technicianData.first_name} ${technicianData.last_name}`.toUpperCase();
    
    // Format date range
    const fromDate = dateRangeFrom ? new Date(dateRangeFrom + 'T00:00:00') : new Date();
    const toDate = dateRangeTo ? new Date(dateRangeTo + 'T23:59:59') : new Date();
    
    let dateRangeText = '';
    if (dateRangeFrom === dateRangeTo) {
        // Single date
        dateRangeText = fromDate.toLocaleDateString('en-GB', { 
            day: '2-digit', 
            month: 'short', 
            year: 'numeric' 
        }).replace(/ /g, '-');
    } else {
        // Date range
        const fromStr = fromDate.toLocaleDateString('en-GB', { 
            day: '2-digit', 
            month: 'short', 
            year: 'numeric' 
        }).replace(/ /g, '-');
        const toStr = toDate.toLocaleDateString('en-GB', { 
            day: '2-digit', 
            month: 'short', 
            year: 'numeric' 
        }).replace(/ /g, '-');
        dateRangeText = `${fromStr} to ${toStr}`;
    }
    
    // Calculate totals
    let totalAmount = 0;
    let totalTip = 0;
    let totalCommission = 0;
    
    filteredCommissionsData.forEach(commission => {
        totalAmount += commission.total;
        totalTip += commission.tip;
        totalCommission += commission.commission;
    });
    
    // Build transactions table rows
    const transactionsHTML = filteredCommissionsData.map(commission => {
        const date = new Date(commission.date + 'T00:00:00');
        const time = date.toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit',
            hour12: false 
        });
        const dateStr = date.toLocaleDateString('en-US', { 
            month: '2-digit', 
            day: '2-digit', 
            year: '2-digit' 
        });
        
        return `
            <tr class="border-b border-gray-200">
                <td class="px-2 py-2 text-sm text-gray-900">${time} | ${dateStr}</td>
                <td class="px-2 py-2 text-sm text-gray-900 text-right">$${commission.total.toFixed(2)} | $${commission.tip.toFixed(2)}</td>
            </tr>
        `;
    }).join('');
    
    const modalContent = `
        <div class="p-6 mx-auto">
            <div class="bg-white border border-gray-300 rounded-lg p-6">
                <!-- Business Header -->
                <div class="text-center mb-6 border-b border-gray-300 pb-4">
                    <h2 class="text-xl font-bold text-gray-900 mb-2">Dons Nail Spa</h2>
                    <p class="text-sm text-gray-600">258 Hedrick St, Beckley, WV 25801</p>
                    <p class="text-sm text-gray-600">Phone: 681-2077114</p>
                    <p class="text-sm text-gray-600">Merchant ID (MID): 23420</p>
                </div>
                
                <!-- Report Title -->
                <div class="text-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">${technicianName} Daily Report</h3>
                    <p class="text-sm text-gray-600 mt-1">${dateRangeText}</p>
                </div>
                
                <!-- Transactions Table -->
                <div class="mb-6">
                    <div class="max-h-96 overflow-y-auto border border-gray-200 rounded">
                        <table class="w-full border-collapse">
                            <thead class="sticky top-0 bg-white z-10">
                                <tr class="border-b-2 border-gray-300">
                                    <th class="px-2 py-2 text-left text-xs font-semibold text-gray-700 uppercase">${technicianName}</th>
                                    <th class="px-2 py-2 text-right text-xs font-semibold text-gray-700 uppercase">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${transactionsHTML}
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Summary -->
                <div class="border-t-2 border-gray-300 pt-4 mt-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-semibold text-gray-900">Total Amount:</span>
                        <span class="text-sm font-bold text-gray-900">$${totalAmount.toFixed(2)}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-semibold text-gray-900">Total Tip:</span>
                        <span class="text-sm font-bold text-gray-900">$${totalTip.toFixed(2)}</span>
                    </div>
                    <div class="flex justify-between items-center mt-2 pt-2 border-t border-gray-200">
                        <span class="text-sm font-semibold text-[#003047]">Total Commission:</span>
                        <span class="text-sm font-bold text-[#003047]">$${totalCommission.toFixed(2)}</span>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-gray-200">
                    <button onclick="window.print()" class="px-6 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium">
                        Print
                    </button>
                    <button onclick="closeModal()" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                        Close
                    </button>
                </div>
            </div>
        </div>
    `;
    
    openModal(modalContent, 'medium');
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    fetchCommissions();
    
    // Listen for browser back/forward button
    window.addEventListener('popstate', function(event) {
        // Reload commissions with URL parameters
        fetchCommissions();
    });
});

function openEditTechnicianModal() {
    const modalContent = `
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">Edit Technician</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form onsubmit="updateTechnician(event)" class="space-y-4">
                <input type="hidden" name="technician_id" value="${technicianData.id}">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                        <input type="text" name="first_name" id="editFirstName" value="${technicianData.first_name}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                        <input type="text" name="last_name" id="editLastName" value="${technicianData.last_name}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                    <input type="text" name="title" id="editTitle" value="${technicianData.title}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" id="editEmail" value="${technicianData.email}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <input type="tel" name="phone" id="editPhone" value="${technicianData.phone}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                </div>
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <label class="text-sm font-medium text-gray-900">Active</label>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="active" class="sr-only peer" ${technicianData.status === 'Available' || technicianData.status === 'Busy' ? 'checked' : ''}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div>
                    </label>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">
                        Update Technician
                    </button>
                </div>
            </form>
        </div>
    `;
    openModal(modalContent);
}

function updateTechnician(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const firstName = formData.get('first_name');
    const lastName = formData.get('last_name');
    const email = formData.get('email');
    const phone = formData.get('phone');
    
    // Update displayed information
    document.getElementById('technicianEmail').textContent = email;
    document.getElementById('technicianPhone').textContent = phone;
    
    // Update page title
    document.querySelector('h1').textContent = firstName + ' ' + lastName;
    
    // Show success message
    showSuccessMessage('Technician details updated successfully!');
    
    // Close modal
    closeModal();
    
    // In a real application, you would send this data to the server
    // fetch('update.php', {
    //     method: 'POST',
    //     body: formData
    // }).then(response => response.json()).then(data => {
    //     showSuccessMessage('Technician details updated successfully!');
    //     closeModal();
    //     location.reload();
    // });
}


function showSuccessMessage(message) {
    const successDiv = document.createElement('div');
    successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    successDiv.textContent = message;
    document.body.appendChild(successDiv);
    setTimeout(() => successDiv.remove(), 3000);
}

// Open Edit Commission Modal
function openEditCommissionModal(date, total, tip, commission) {
    const formattedDate = new Date(date + 'T00:00:00').toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    });
    
    const modalContent = `
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">Edit Commission</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form onsubmit="updateCommission(event, '${date}')" class="space-y-4">
                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                    <p class="text-sm font-medium text-gray-700">Date: <span class="text-gray-900">${formattedDate}</span></p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Total ($)</label>
                        <p class="text-2xl font-bold text-gray-900">$${total.toFixed(2)}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tip ($)</label>
                        <p class="text-2xl font-bold text-gray-900">$${tip.toFixed(2)}</p>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Commission ($)</label>
                    <input type="number" id="editCommissionInput" value="${commission.toFixed(2)}" min="0" step="0.01" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="button" onclick="closeModal()" class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">
                        Cancel
                    </button>
                    <button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">
                        Update Commission
                    </button>
                </div>
            </form>
        </div>
    `;
    openModal(modalContent, 'default', false);
}

// Update Commission
function updateCommission(event, date) {
    event.preventDefault();
    
    const commissionInput = document.getElementById('editCommissionInput');
    const newCommission = parseFloat(commissionInput.value);
    
    if (isNaN(newCommission) || newCommission < 0) {
        alert('Please enter a valid positive number for commission');
        return;
    }
    
    // Update filteredCommissionsData (the grouped data displayed in table)
    const filteredIndex = filteredCommissionsData.findIndex(c => c.date === date);
    if (filteredIndex !== -1) {
        // Only update commission, keep total and tip unchanged
        filteredCommissionsData[filteredIndex].commission = newCommission;
        
        // Re-render the commissions table with updated data
        renderCommissions();
        
        // Show success message
        showSuccessMessage('Commission updated successfully!');
        
        // Close modal
        closeModal();
    } else {
        alert('Commission not found');
    }
}
</script>

<?php include '../includes/footer.php'; ?>


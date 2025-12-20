<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get current role (default to 'admin')
$current_role = isset($_SESSION['selected_role']) ? $_SESSION['selected_role'] : 'admin';
$isTechnician = ($current_role === 'technician');

$pageTitle = 'Payout';
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/modal.php';
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
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-1">Payout</h1>
                <p class="text-gray-500 text-sm">Manage technician payouts and commissions</p>
            </div>
            <button onclick="printPayoutReport()" class="flex items-center gap-2 px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#004060] transition-colors font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h10a2 2 0 002-2V6a2 2 0 00-2-2H9a2 2 0 00-2 2v16a2 2 0 002 2z"></path>
                </svg>
                Print
            </button>
        </div>

        <!-- Payout Section -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-6">
            
            <!-- Filters Section -->
            <div class="mb-6 bg-gray-50 rounded-xl p-4 border border-gray-200" id="filtersSection">
                <div class="flex flex-col lg:flex-row items-start lg:items-center gap-4">
                    <!-- Technicians Dropdown (only for admin/receptionist) -->
                    <?php if (!$isTechnician): ?>
                    <div class="flex items-center gap-2 bg-white border border-gray-300 rounded-lg px-3 py-2 shadow-sm hover:border-[#003047] transition-colors">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <select id="technicianFilter" onchange="onTechnicianChange()" class="text-sm font-medium text-gray-700 border-0 focus:outline-none focus:ring-0 bg-transparent cursor-pointer">
                            <option value="">Select Technician</option>
                        </select>
                    </div>
                    <?php endif; ?>
                    
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
                    
                    <!-- Quick Preset Buttons (on the right) -->
                    <div class="flex items-center gap-2 flex-wrap ml-auto">
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
                            <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700">Total Service</th>
                            <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700">Tip</th>
                            <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700">Commission</th>
                            <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700">Total</th>
                            <th class="text-right py-3 px-4 text-sm font-semibold text-gray-700"></th>
                        </tr>
                    </thead>
                    <tbody id="payoutTableBody">
                        <!-- Payouts will be populated by JavaScript -->
                        <tr>
                            <td colspan="5" class="text-center py-8 text-gray-500">
                                <p>Please select a technician to view payouts</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script>
let payoutData = [];
let filteredPayoutData = [];
let dateRangeFrom = null;
let dateRangeTo = null;
let selectedTechnicianId = isTechnicianUser ? '1' : null; // Set Technician ID 1 as default for technicians
let techniciansList = [];

// Fetch payout data
async function fetchPayouts() {
    try {
        const [bookingsResponse, techniciansResponse] = await Promise.all([
            fetch('../json/booking.json'),
            fetch('../json/users.json')
        ]);
        
        const bookingsData = await bookingsResponse.json();
        const techniciansData = await techniciansResponse.json();
        
        const bookings = bookingsData.bookings || [];
        const technicians = techniciansData.users || [];
        
        // Filter technicians
        techniciansList = technicians.filter(tech => tech.role === 'technician');
        
        // Only check URL parameter if selectedTechnicianId is not already set (from dropdown change)
        if (!selectedTechnicianId) {
            const urlParams = new URLSearchParams(window.location.search);
            const urlTechnician = urlParams.get('technician');
            if (urlTechnician) {
                selectedTechnicianId = urlTechnician;
            }
        }
        
        // Create a map of technician names to IDs
        const technicianMap = {};
        techniciansList.forEach(tech => {
            const fullName = `${tech.firstName} ${tech.lastName}`;
            technicianMap[fullName] = tech;
        });
        
        // Populate dropdown after setting selectedTechnicianId
        populateTechnicianDropdown(techniciansList);
        
        // Don't process if no technician is selected
        if (!selectedTechnicianId) {
            payoutData = [];
            filteredPayoutData = [];
            renderPayouts();
            return;
        }
        
        // Process bookings and create individual transaction records
        const transactions = [];
        
        bookings.forEach(booking => {
            if (!booking.technician || !booking.bookingDate) return;
            
            const technicians = Array.isArray(booking.technician) ? booking.technician : [booking.technician];
            const total = parseFloat(booking.total) || 0;
            const tip = parseFloat(booking.tip) || (total * 0.15); // Use actual tip or calculate 15%
            
            technicians.forEach(techName => {
                // Filter by selected technician
                const tech = technicianMap[techName];
                if (!tech || tech.id.toString() !== selectedTechnicianId.toString()) {
                    return; // Skip if not the selected technician
                }
                
                // Create transaction record
                transactions.push({
                    time: booking.bookingTime || '00:00',
                    date: booking.bookingDate,
                    amount: total,
                    tip: tip
                });
            });
        });
        
        // Sort by date (newest first), then by time (newest first)
        payoutData = transactions.sort((a, b) => {
            const dateA = new Date(a.date);
            const dateB = new Date(b.date);
            if (dateB.getTime() !== dateA.getTime()) {
                return dateB - dateA;
            }
            // If same date, sort by time (descending)
            const timeA = a.time.split(':').map(Number);
            const timeB = b.time.split(':').map(Number);
            const timeAValue = timeA[0] * 60 + timeA[1];
            const timeBValue = timeB[0] * 60 + timeB[1];
            return timeBValue - timeAValue;
        });
        
        // Add sample data from December 1 to current date
        const today = new Date();
        const december1 = new Date(today.getFullYear(), 11, 1); // December 1 (month 11 = December)
        
        // Sample transaction templates with varying amounts
        const sampleTransactionTemplates = [
            { time: '19:23', amount: 70.00, tip: 0.00 },
            { time: '17:28', amount: 45.00, tip: 10.00 },
            { time: '16:34', amount: 50.00, tip: 0.00 },
            { time: '15:41', amount: 60.00, tip: 0.00 },
            { time: '14:59', amount: 45.00, tip: 0.00 },
            { time: '13:31', amount: 70.00, tip: 0.00 },
            { time: '12:00', amount: 145.00, tip: 0.00 },
            { time: '11:15', amount: 55.00, tip: 8.25 },
            { time: '10:30', amount: 80.00, tip: 12.00 },
            { time: '09:45', amount: 40.00, tip: 6.00 }
        ];
        
        // Generate sample data for each day from December 1 to today
        const currentDate = new Date(december1);
        while (currentDate <= today) {
            const dateStr = currentDate.toISOString().split('T')[0];
            
            // Check if we already have data for this date
            const hasDataForDate = payoutData.some(transaction => transaction.date === dateStr);
            
            // Add 1-5 random transactions per day (skip some days randomly for variety)
            if (!hasDataForDate && Math.random() > 0.2) { // 80% chance of having transactions on a day
                const numTransactions = Math.floor(Math.random() * 5) + 1; // 1-5 transactions per day
                
                for (let i = 0; i < numTransactions; i++) {
                    const template = sampleTransactionTemplates[Math.floor(Math.random() * sampleTransactionTemplates.length)];
                    // Add some variation to amounts (±20%)
                    const variation = 0.8 + (Math.random() * 0.4); // 0.8 to 1.2
                    const amount = Math.round(template.amount * variation * 100) / 100;
                    const tip = template.tip > 0 ? Math.round(template.amount * 0.15 * variation * 100) / 100 : 0;
                    
                    payoutData.push({
                        time: template.time,
                        date: dateStr,
                        amount: amount,
                        tip: tip
                    });
                }
            }
            
            // Move to next day
            currentDate.setDate(currentDate.getDate() + 1);
        }
        
        // Re-sort after adding samples
        payoutData = payoutData.sort((a, b) => {
            const dateA = new Date(a.date);
            const dateB = new Date(b.date);
            if (dateB.getTime() !== dateA.getTime()) {
                return dateB - dateA;
            }
            const timeA = a.time.split(':').map(Number);
            const timeB = b.time.split(':').map(Number);
            const timeAValue = timeA[0] * 60 + timeA[1];
            const timeBValue = timeB[0] * 60 + timeB[1];
            return timeBValue - timeAValue;
        });
        
        // Set default date range to today
        const urlFrom = urlParams.get('from');
        const urlTo = urlParams.get('to');
        const urlDateType = urlParams.get('datetype');
        
        if (urlFrom && urlTo) {
            dateRangeFrom = urlFrom;
            dateRangeTo = urlTo;
            
            document.getElementById('dateRangeFrom').value = dateRangeFrom;
            document.getElementById('dateRangeTo').value = dateRangeTo;
            
            if (urlDateType) {
                setTimeout(() => {
                    document.querySelectorAll('.date-preset-btn').forEach(btn => {
                        btn.classList.remove('bg-[#003047]', 'text-white', 'border-[#003047]');
                        btn.classList.add('bg-white', 'text-gray-700', 'border-gray-300');
                    });
                    
                    const activeButton = document.querySelector(`button[onclick*="setDateRange('${urlDateType}'"]`);
                    if (activeButton) {
                        activeButton.classList.remove('bg-white', 'text-gray-700', 'border-gray-300');
                        activeButton.classList.add('bg-[#003047]', 'text-white', 'border-[#003047]');
                    }
                }, 100);
            }
        } else {
            const today = new Date();
            dateRangeFrom = today.toISOString().split('T')[0];
            dateRangeTo = today.toISOString().split('T')[0];
            
            document.getElementById('dateRangeFrom').value = dateRangeFrom;
            document.getElementById('dateRangeTo').value = dateRangeTo;
            
            updateURLWithDateRange(dateRangeFrom, dateRangeTo, 'today');
            
            setTimeout(() => {
                const todayButton = document.querySelector('button[onclick*="today"]');
                if (todayButton) {
                    todayButton.classList.remove('bg-white', 'text-gray-700', 'border-gray-300');
                    todayButton.classList.add('bg-[#003047]', 'text-white', 'border-[#003047]');
                }
            }, 100);
        }
        
        // Apply filters to display data
        applyDateRangeFilter(true);
    } catch (error) {
        console.error('Error fetching payouts:', error);
        // Instead of showing error, show sample data from December 1 to today
        const today = new Date();
        const december1 = new Date(today.getFullYear(), 11, 1); // December 1
        
        // Sample transaction templates
        const sampleTransactionTemplates = [
            { time: '19:23', amount: 70.00, tip: 0.00 },
            { time: '17:28', amount: 45.00, tip: 10.00 },
            { time: '16:34', amount: 50.00, tip: 0.00 },
            { time: '15:41', amount: 60.00, tip: 0.00 },
            { time: '14:59', amount: 45.00, tip: 0.00 },
            { time: '13:31', amount: 70.00, tip: 0.00 },
            { time: '12:00', amount: 145.00, tip: 0.00 },
            { time: '11:15', amount: 55.00, tip: 8.25 },
            { time: '10:30', amount: 80.00, tip: 12.00 },
            { time: '09:45', amount: 40.00, tip: 6.00 }
        ];
        
        payoutData = [];
        const currentDate = new Date(december1);
        while (currentDate <= today) {
            const dateStr = currentDate.toISOString().split('T')[0];
            
            // Add 1-5 random transactions per day (skip some days randomly)
            if (Math.random() > 0.2) {
                const numTransactions = Math.floor(Math.random() * 5) + 1;
                
                for (let i = 0; i < numTransactions; i++) {
                    const template = sampleTransactionTemplates[Math.floor(Math.random() * sampleTransactionTemplates.length)];
                    const variation = 0.8 + (Math.random() * 0.4);
                    const amount = Math.round(template.amount * variation * 100) / 100;
                    const tip = template.tip > 0 ? Math.round(template.amount * 0.15 * variation * 100) / 100 : 0;
                    
                    payoutData.push({
                        time: template.time,
                        date: dateStr,
                        amount: amount,
                        tip: tip
                    });
                }
            }
            
            currentDate.setDate(currentDate.getDate() + 1);
        }
        
        // Sort by date (newest first), then by time (newest first)
        payoutData = payoutData.sort((a, b) => {
            const dateA = new Date(a.date);
            const dateB = new Date(b.date);
            if (dateB.getTime() !== dateA.getTime()) {
                return dateB - dateA;
            }
            const timeA = a.time.split(':').map(Number);
            const timeB = b.time.split(':').map(Number);
            const timeAValue = timeA[0] * 60 + timeA[1];
            const timeBValue = timeB[0] * 60 + timeB[1];
            return timeBValue - timeAValue;
        });
        
        filteredPayoutData = payoutData;
        renderPayouts();
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
    if (selectedTechnicianId) {
        url.searchParams.set('technician', selectedTechnicianId);
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
            const diff = today.getDate() - dayOfWeek;
            fromDate = new Date(today.getFullYear(), today.getMonth(), diff);
            toDate = new Date(fromDate);
            toDate.setDate(fromDate.getDate() + 6);
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
    
    updateURLWithDateRange(dateRangeFrom, dateRangeTo, range);
    
    document.querySelectorAll('.date-preset-btn').forEach(btn => {
        btn.classList.remove('bg-[#003047]', 'text-white', 'border-[#003047]');
        btn.classList.add('bg-white', 'text-gray-700', 'border-gray-300');
    });
    
    let activeButton = null;
    if (buttonElement) {
        activeButton = buttonElement;
    } else {
        activeButton = document.querySelector(`button[onclick*="setDateRange('${range}')"]`);
    }
    
    if (activeButton) {
        activeButton.classList.remove('bg-white', 'text-gray-700', 'border-gray-300');
        activeButton.classList.add('bg-[#003047]', 'text-white', 'border-[#003047]');
    }
    
    applyDateRangeFilter(true);
}

// Handle technician dropdown change
function onTechnicianChange() {
    const technicianSelect = document.getElementById('technicianFilter');
    if (!technicianSelect) return;
    
    const newTechnicianId = technicianSelect.value || null;
    
    // Prevent unnecessary fetch if same technician is selected
    if (newTechnicianId === selectedTechnicianId) {
        return;
    }
    
    // Update selected technician
    selectedTechnicianId = newTechnicianId;
    
    // Update URL
    const url = new URL(window.location);
    if (selectedTechnicianId) {
        url.searchParams.set('technician', selectedTechnicianId);
    } else {
        url.searchParams.delete('technician');
    }
    window.history.pushState({}, '', url);
    
    // If technician is selected, fetch data; otherwise clear display
    if (selectedTechnicianId) {
        fetchPayouts();
    } else {
        payoutData = [];
        filteredPayoutData = [];
        renderPayouts();
    }
}

// Populate technician dropdown
function populateTechnicianDropdown(technicians) {
    const dropdown = document.getElementById('technicianFilter');
    if (!dropdown) return;
    
    // Store current value to prevent triggering onchange
    const currentValue = dropdown.value;
    const wasEmpty = !currentValue || currentValue === '';
    
    // Temporarily remove onchange attribute to prevent infinite loop
    dropdown.removeAttribute('onchange');
    
    // Clear existing options
    dropdown.innerHTML = '<option value="">Select Technician</option>';
    
    // Add technician options
    technicians.forEach(tech => {
        const fullName = `${tech.firstName} ${tech.lastName}`;
        const option = document.createElement('option');
        option.value = tech.id;
        option.textContent = fullName;
        dropdown.appendChild(option);
    });
    
    // Set selected technician from variable or URL, or restore previous value
    if (selectedTechnicianId) {
        dropdown.value = selectedTechnicianId;
    } else if (!wasEmpty && currentValue) {
        // Restore previous value if it existed
        dropdown.value = currentValue;
    }
    
    // Restore onchange handler using setAttribute
    dropdown.setAttribute('onchange', 'onTechnicianChange()');
}

// Filter payouts by date range and technician
function applyDateRangeFilter(preserveButtonState = false) {
    const fromInput = document.getElementById('dateRangeFrom');
    const toInput = document.getElementById('dateRangeTo');
    const technicianSelect = document.getElementById('technicianFilter');
    
    if (!fromInput || !toInput) return;
    
    dateRangeFrom = fromInput.value;
    dateRangeTo = toInput.value;
    
    // For technicians, preserve the selectedTechnicianId (don't reset it)
    // For admin/receptionist, get from dropdown
    if (isTechnicianUser) {
        // Keep the existing selectedTechnicianId for technicians
        if (!selectedTechnicianId) {
            selectedTechnicianId = '1'; // Default to ID 1 for technicians
        }
    } else {
        selectedTechnicianId = technicianSelect ? technicianSelect.value : null;
    }
    
    // Don't show results if no technician is selected (only for non-technicians)
    if (!selectedTechnicianId && !isTechnicianUser) {
        filteredPayoutData = [];
        renderPayouts();
        
        // Update URL
        const url = new URL(window.location);
        url.searchParams.delete('technician');
        if (dateRangeFrom && dateRangeTo && !preserveButtonState) {
            url.searchParams.set('from', dateRangeFrom);
            url.searchParams.set('to', dateRangeTo);
        }
        window.history.pushState({}, '', url);
        return;
    }
    
    // Update URL
    if (dateRangeFrom && dateRangeTo && !preserveButtonState) {
        updateURLWithDateRange(dateRangeFrom, dateRangeTo, null);
    }
    
    const url = new URL(window.location);
    url.searchParams.set('technician', selectedTechnicianId);
    window.history.pushState({}, '', url);
    
    if (!preserveButtonState) {
        document.querySelectorAll('.date-preset-btn').forEach(btn => {
            btn.classList.remove('bg-[#003047]', 'text-white', 'border-[#003047]');
            btn.classList.add('bg-white', 'text-gray-700', 'border-gray-300');
        });
    }
    
    // Filter by date range if dates are provided
    if (dateRangeFrom && dateRangeTo && payoutData.length > 0) {
        const fromDate = new Date(dateRangeFrom);
        const toDate = new Date(dateRangeTo);
        toDate.setHours(23, 59, 59, 999); // Include the entire end date
        
        filteredPayoutData = payoutData.filter(transaction => {
            const transactionDate = new Date(transaction.date);
            return transactionDate >= fromDate && transactionDate <= toDate;
        });
    } else {
        // Show all results if no date range is set
        filteredPayoutData = payoutData;
    }
    
    renderPayouts();
}

// View payout details for a specific date
function viewPayoutDetails(dateKey) {
    // Filter transactions for the selected date
    const dateTransactions = filteredPayoutData.filter(transaction => transaction.date === dateKey);
    
    if (dateTransactions.length === 0) {
        alert('No transactions found for this date');
        return;
    }
    
    // Calculate totals for this date
    let dateTotal = 0;
    let dateTip = 0;
    dateTransactions.forEach(transaction => {
        dateTotal += transaction.amount;
        dateTip += transaction.tip;
    });
    
    // Format date as "19-Dec-2025"
    const date = new Date(dateKey);
    const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const month = monthNames[date.getMonth()];
    const day = date.getDate();
    const year = date.getFullYear();
    const formattedDate = `${day}-${month}-${year}`;
    
    // Get technician name
    const technician = techniciansList.find(tech => tech.id.toString() === selectedTechnicianId.toString());
    const technicianName = technician ? `${technician.firstName} ${technician.lastName}` : 'Technician';
    const technicianNameUpper = technicianName.toUpperCase();
    
    // Build transaction list
    let transactionsList = '';
    dateTransactions.forEach((transaction, index) => {
        const time = transaction.time || '00:00';
        const [hours, minutes] = time.split(':');
        const formattedTime = `${hours.padStart(2, '0')}:${minutes.padStart(2, '0')}`;
        
        // Format transaction date as "12/19/25"
        const transDate = new Date(transaction.date);
        const transMonth = String(transDate.getMonth() + 1).padStart(2, '0');
        const transDay = String(transDate.getDate()).padStart(2, '0');
        const transYear = String(transDate.getFullYear()).slice(-2);
        const formattedTransDate = `${transMonth}/${transDay}/${transYear}`;
        
        transactionsList += `
            <tr class="border-b border-gray-200">
                <td class="py-2 px-4 text-sm text-gray-900 border-r border-gray-200">${formattedTime} | ${formattedTransDate}</td>
                <td class="py-2 px-4 text-sm text-gray-900 text-right">$${transaction.amount.toFixed(2)} | $${transaction.tip.toFixed(2)}</td>
            </tr>
        `;
    });
    
    // Build modal content
    const modalContent = `
        <div class="p-6 bg-white border border-gray-300 rounded-lg">
            <!-- Business Header -->
            <div class="text-center mb-6 border-b border-gray-300 pb-4">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Dons Nail Spa</h1>
                <p class="text-sm text-gray-700">258 Hedrick St, Beckley, WV 25801</p>
                <p class="text-sm text-gray-700">Phone: 681-2077114</p>
                <p class="text-sm text-gray-700">Merchant ID (MID): 23420</p>
            </div>
            
            <!-- Report Title -->
            <div class="mb-4 border-b border-gray-200 pb-3 text-center">
                <h2 class="text-xl font-bold text-gray-900 mb-1">${technicianName} Daily Report</h2>
                <p class="text-sm text-gray-600">${formattedDate}</p>
            </div>
            
            <!-- Transactions Table -->
            <div class="mb-6 overflow-y-auto max-h-96 border border-gray-300 rounded-lg">
                <table class="w-full">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr class="border-b border-gray-300">
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-500 uppercase border-r border-gray-300">${technicianNameUpper}</th>
                            <th class="text-right py-3 px-4 text-sm font-semibold text-gray-500 uppercase">AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${transactionsList}
                    </tbody>
                </table>
            </div>
            
            <!-- Summary Section -->
            <div class="border-t border-gray-200 pt-4 mb-6">
                <div class="flex justify-between items-center mb-2 pb-2 border-b border-gray-200">
                    <span class="text-sm font-semibold text-gray-900">Total Amount:</span>
                    <span class="text-sm font-semibold text-gray-900">$${dateTotal.toFixed(2)}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm font-semibold text-gray-900">Total Tip:</span>
                    <span class="text-sm font-semibold text-gray-900">$${dateTip.toFixed(2)}</span>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 border-t border-gray-200 pt-4">
                <button onclick="window.print()" class="px-6 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#004060] transition-colors font-medium">
                    Print
                </button>
                <button onclick="closeModal()" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                    Close
                </button>
            </div>
        </div>
    `;
    
    if (typeof openModal === 'function') {
        openModal(modalContent, 'medium');
    }
}

// Render payouts table
function renderPayouts() {
    const tbody = document.getElementById('payoutTableBody');
    if (!tbody) return;
    
    // Show message if no technician is selected (only for non-technicians)
    if (!selectedTechnicianId) {
        if (!isTechnicianUser) {
            tbody.innerHTML = `
                <tr>
                            <td colspan="6" class="text-center py-8 text-gray-500">
                                <p>Please select a technician to view payouts</p>
                            </td>
                        </tr>
            `;
        } else {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center py-8 text-gray-500">
                        <p>Loading payouts...</p>
                    </td>
                </tr>
            `;
        }
        return;
    }
    
    if (!filteredPayoutData || filteredPayoutData.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-8 text-gray-500">
                    No payout data found for the selected date range
                </td>
            </tr>
        `;
        return;
    }
    
    // Group transactions by date
    const dailyTotals = {};
    let grandTotal = 0;
    let grandTip = 0;
    let grandCommission = 0;
    let grandTotalTipCommission = 0;
    
    filteredPayoutData.forEach(transaction => {
        const dateKey = transaction.date;
        
        if (!dailyTotals[dateKey]) {
            dailyTotals[dateKey] = {
                date: dateKey,
                total: 0,
                tip: 0,
                commission: 0,
                totalTipCommission: 0
            };
        }
        
        dailyTotals[dateKey].total += transaction.amount;
        dailyTotals[dateKey].tip += transaction.tip;
        
        grandTotal += transaction.amount;
        grandTip += transaction.tip;
    });
    
    // Calculate commission for each day (30% of total) and total (tip + commission)
    Object.keys(dailyTotals).forEach(dateKey => {
        dailyTotals[dateKey].commission = dailyTotals[dateKey].total * 0.30;
        dailyTotals[dateKey].totalTipCommission = dailyTotals[dateKey].tip + dailyTotals[dateKey].commission;
        grandCommission += dailyTotals[dateKey].commission;
        grandTotalTipCommission += dailyTotals[dateKey].totalTipCommission;
    });
    
    // Sort by date (newest first)
    const sortedDates = Object.keys(dailyTotals).sort((a, b) => {
        return new Date(b) - new Date(a);
    });
    
    let html = '';
    
    sortedDates.forEach(dateKey => {
        const daily = dailyTotals[dateKey];
        
        // Format date as "Dec 30, 2025"
        const date = new Date(daily.date);
        const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const month = monthNames[date.getMonth()];
        const day = date.getDate();
        const year = date.getFullYear();
        const formattedDate = `${month} ${day}, ${year}`;
        
        html += `
            <tr class="border-b border-gray-100 hover:bg-gray-50">
                <td class="py-3 px-4 text-sm text-gray-900">${formattedDate}</td>
                <td class="py-3 px-4 text-sm text-gray-900 text-right">$${daily.total.toFixed(2)}</td>
                <td class="py-3 px-4 text-sm text-gray-900 text-right">$${daily.tip.toFixed(2)}</td>
                <td class="py-3 px-4 text-sm font-semibold text-[#003047] text-right">$${daily.commission.toFixed(2)}</td>
                <td class="py-3 px-4 text-sm font-semibold text-gray-900 text-right">$${daily.totalTipCommission.toFixed(2)}</td>
                <td class="py-3 px-4">
                    <button onclick="viewPayoutDetails('${dateKey}')" class="px-3 py-1.5 bg-gray-500 text-white text-xs font-medium rounded hover:bg-gray-600 transition active:scale-95 flex items-center gap-1 ml-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                        </svg>
                        Print
                    </button>
                </td>
            </tr>
        `;
    });
    
    // Add total row at the bottom
    html += `
        <tr class="bg-gray-50 border-t-2 border-gray-300">
            <td class="py-3 px-4 text-sm font-semibold text-gray-900">Total</td>
            <td class="py-3 px-4 text-sm font-semibold text-gray-900 text-right">$${grandTotal.toFixed(2)}</td>
            <td class="py-3 px-4 text-sm font-semibold text-gray-900 text-right">$${grandTip.toFixed(2)}</td>
            <td class="py-3 px-4 text-sm font-semibold text-[#003047] text-right">$${grandCommission.toFixed(2)}</td>
            <td class="py-3 px-4 text-sm font-semibold text-gray-900 text-right">$${grandTotalTipCommission.toFixed(2)}</td>
            <td class="py-3 px-4"></td>
        </tr>
    `;
    
    tbody.innerHTML = html;
}

// Show print modal
function printPayoutReport() {
    if (!selectedTechnicianId) {
        alert('Please select a technician first');
        return;
    }
    
    if (!filteredPayoutData || filteredPayoutData.length === 0) {
        alert('No payout data to print');
        return;
    }
    
    // Get technician name
    const technician = techniciansList.find(tech => tech.id.toString() === selectedTechnicianId.toString());
    const technicianName = technician ? `${technician.firstName} ${technician.lastName}`.toUpperCase() : 'TECHNICIAN';
    
    // Format date range
    let dateRangeText = '';
    if (dateRangeFrom && dateRangeTo) {
        const fromDate = new Date(dateRangeFrom);
        const toDate = new Date(dateRangeTo);
        
        const formatDate = (date) => {
            const day = String(date.getDate()).padStart(2, '0');
            const monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const month = monthNames[date.getMonth()];
            const year = date.getFullYear();
            return `${day}-${month}-${year}`;
        };
        
        if (dateRangeFrom === dateRangeTo) {
            dateRangeText = formatDate(fromDate);
        } else {
            dateRangeText = `${formatDate(fromDate)} to ${formatDate(toDate)}`;
        }
    }
    
    // Group transactions by date and calculate daily totals
    const dailyTotals = {};
    let totalAmount = 0;
    let totalTip = 0;
    
    filteredPayoutData.forEach(transaction => {
        const dateKey = transaction.date;
        
        if (!dailyTotals[dateKey]) {
            dailyTotals[dateKey] = {
                date: dateKey,
                amount: 0,
                tip: 0,
                transactions: []
            };
        }
        
        dailyTotals[dateKey].amount += transaction.amount;
        dailyTotals[dateKey].tip += transaction.tip;
        dailyTotals[dateKey].transactions.push(transaction);
        
        totalAmount += transaction.amount;
        totalTip += transaction.tip;
    });
    
    // Calculate commission (30% of total amount)
    const totalCommission = totalAmount * 0.30;
    
    // Sort daily totals by date (newest first)
    const sortedDailyTotals = Object.values(dailyTotals).sort((a, b) => {
        return new Date(b.date) - new Date(a.date);
    });
    
    // Build transactions table HTML
    let transactionsHtml = '';
    sortedDailyTotals.forEach(daily => {
        // Get first transaction time for the day (or use 00:00)
        const firstTransaction = daily.transactions.sort((a, b) => {
            const timeA = a.time.split(':').map(Number);
            const timeB = b.time.split(':').map(Number);
            return (timeA[0] * 60 + timeA[1]) - (timeB[0] * 60 + timeB[1]);
        })[0];
        
        const time = firstTransaction ? firstTransaction.time : '00:00';
        const [hours, minutes] = time.split(':');
        const formattedTime = `${hours.padStart(2, '0')}:${minutes.padStart(2, '0')}`;
        
        // Format date (MM/DD/YY)
        const date = new Date(daily.date);
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const year = String(date.getFullYear()).slice(-2);
        const formattedDate = `${month}/${day}/${year}`;
        
        transactionsHtml += `
            <tr class="border-b border-gray-200">
                <td class="py-2 px-4 text-sm text-gray-900 border-r border-gray-200">${formattedTime} | ${formattedDate}</td>
                <td class="py-2 px-4 text-sm text-gray-900 text-right">$${daily.amount.toFixed(2)} | $${daily.tip.toFixed(2)}</td>
            </tr>
        `;
    });
    
    // Build modal content
    const modalContent = `
        <div class="p-6 bg-white border border-gray-300 rounded-lg">
            <!-- Business Header -->
            <div class="text-center mb-6 border-b border-gray-300 pb-4">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Dons Nail Spa</h1>
                <p class="text-sm text-gray-700">258 Hedrick St, Beckley, WV 25801</p>
                <p class="text-sm text-gray-700">681-2077114</p>
                <p class="text-sm text-gray-700">MID: 23420</p>
            </div>
            
            <!-- Report Title -->
            <div class="mb-4 border-b border-gray-200 pb-3">
                <h2 class="text-xl font-bold text-gray-900 mb-1">${technicianName} Daily Report</h2>
                <p class="text-sm text-gray-600">${dateRangeText}</p>
            </div>
            
            <!-- Transactions Table -->
            <div class="mb-6 overflow-y-auto max-h-96 border border-gray-300 rounded-lg">
                <table class="w-full">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr class="border-b border-gray-300">
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-900 border-r border-gray-300">${technicianName}</th>
                            <th class="text-right py-3 px-4 text-sm font-semibold text-gray-900">AMOUNT</th>
                        </tr>
                    </thead>
                    <tbody id="printTransactionsBody">
                        ${transactionsHtml}
                    </tbody>
                </table>
            </div>
            
            <!-- Summary Section -->
            <div class="border border-gray-300 rounded-lg p-4 mb-6 bg-gray-50">
                <div class="flex justify-between items-center mb-2 pb-2 border-b border-gray-200">
                    <span class="text-sm font-semibold text-gray-900">Total Amount:</span>
                    <span class="text-sm font-semibold text-gray-900">$${totalAmount.toFixed(2)}</span>
                </div>
                <div class="flex justify-between items-center mb-2 pb-2 border-b border-gray-200">
                    <span class="text-sm font-semibold text-gray-900">Total Tip:</span>
                    <span class="text-sm font-semibold text-gray-900">$${totalTip.toFixed(2)}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm font-semibold text-gray-900">Total Commission:</span>
                    <span class="text-sm font-semibold text-gray-900">$${totalCommission.toFixed(2)}</span>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex justify-end gap-3 border-t border-gray-200 pt-4">
                <button onclick="window.print()" class="px-6 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#004060] transition-colors font-medium">
                    Print
                </button>
                <button onclick="closeModal()" class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition-colors font-medium">
                    Close
                </button>
            </div>
        </div>
    `;
    
    if (typeof openModal === 'function') {
        openModal(modalContent, 'medium');
    }
}

// Load technicians list
async function loadTechnicians() {
    try {
        const techniciansResponse = await fetch('../json/users.json');
        const techniciansData = await techniciansResponse.json();
        const technicians = techniciansData.users || [];
        
        // Filter technicians
        techniciansList = technicians.filter(tech => tech.role === 'technician');
        
        // For technician role, auto-select technician ID 1
        if (isTechnicianUser) {
            selectedTechnicianId = '1'; // Set technician ID 1 as default
        } else {
            // Populate dropdown for admin/receptionist
            populateTechnicianDropdown(techniciansList);
        }
    } catch (error) {
        console.error('Error loading technicians:', error);
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set default date range to today if not in URL
    const urlParams = new URLSearchParams(window.location.search);
    const urlFrom = urlParams.get('from');
    const urlTo = urlParams.get('to');
    
    // Always set today as default date range
    const today = new Date();
    dateRangeFrom = today.toISOString().split('T')[0];
    dateRangeTo = today.toISOString().split('T')[0];
    
    const fromInput = document.getElementById('dateRangeFrom');
    const toInput = document.getElementById('dateRangeTo');
    if (fromInput) fromInput.value = dateRangeFrom;
    if (toInput) toInput.value = dateRangeTo;
    
    // Update URL with today as default (only if not technician or no URL params)
    if (!isTechnicianUser && (!urlFrom || !urlTo)) {
        updateURLWithDateRange(dateRangeFrom, dateRangeTo, 'today');
        
        // Set Today button as active (only if not technician)
        setTimeout(() => {
            const todayButton = document.querySelector('button[onclick*="setDateRange(\'today\'"]');
            if (todayButton) {
                document.querySelectorAll('.date-preset-btn').forEach(btn => {
                    btn.classList.remove('bg-[#003047]', 'text-white', 'border-[#003047]');
                    btn.classList.add('bg-white', 'text-gray-700', 'border-gray-300');
                });
                todayButton.classList.remove('bg-white', 'text-gray-700', 'border-gray-300');
                todayButton.classList.add('bg-[#003047]', 'text-white', 'border-[#003047]');
            }
        }, 100);
    }
    
    // Load technicians first, then fetch payouts
    loadTechnicians().then(() => {
        // For technician role, ensure Technician ID 1 is selected and fetch data
        if (isTechnicianUser) {
            selectedTechnicianId = '1'; // Ensure Technician ID 1 is set
            fetchPayouts();
        } else {
            // For admin/receptionist, check URL for technician
            const urlTechnician = urlParams.get('technician');
            if (urlTechnician) {
                selectedTechnicianId = urlTechnician;
                const dropdown = document.getElementById('technicianFilter');
                if (dropdown) {
                    dropdown.value = urlTechnician;
                }
                fetchPayouts();
            }
        }
    });
    
    window.addEventListener('popstate', function(event) {
        const urlParams = new URLSearchParams(window.location.search);
        const urlTechnician = urlParams.get('technician');
        if (urlTechnician) {
            selectedTechnicianId = urlTechnician;
            const dropdown = document.getElementById('technicianFilter');
            if (dropdown) {
                dropdown.value = urlTechnician;
            }
        } else {
            if (!isTechnicianUser) {
                selectedTechnicianId = null;
                const dropdown = document.getElementById('technicianFilter');
                if (dropdown) {
                    dropdown.value = '';
                }
            }
        }
        fetchPayouts();
    });
});
</script>

<?php include '../includes/footer.php'; ?>


<?php
$pageTitle = 'Dashboard';
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/modal.php';

// Get current role (default to 'admin')
$current_role = isset($_SESSION['selected_role']) ? $_SESSION['selected_role'] : 'admin';
?>

<!-- Main Content Area -->
<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Dashboard</h1>
            <p class="text-gray-600 text-sm sm:text-base"><?php echo date('l, d F Y'); ?></p>
        </div>

        <?php if ($current_role === 'receptionist'): ?>
        <!-- Receptionist Dashboard -->
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6">
            <!-- Today's Bookings -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Today's Bookings</p>
                        <p class="text-2xl font-bold text-gray-900">12</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Waiting -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Waiting</p>
                        <p class="text-2xl font-bold text-gray-900">5</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- In Progress -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">In Progress</p>
                        <p class="text-2xl font-bold text-gray-900">3</p>
                    </div>
                    <div class="w-12 h-12 bg-[#e6f0f3] rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#003047]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Completed -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Completed</p>
                        <p class="text-2xl font-bold text-gray-900">2</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Paid -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Paid</p>
                        <p class="text-2xl font-bold text-gray-900">8</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Available Technicians -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Available Technicians</p>
                        <p class="text-2xl font-bold text-gray-900">4</p>
                    </div>
                    <div class="w-12 h-12 bg-cyan-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions and Recent Activities -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Quick Actions Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
                <div class="grid grid-cols-2 gap-4">
                    <a href="../booking/booking.php" class="p-6 bg-[#e6f0f3] rounded-lg hover:bg-[#d1e4e9] transition text-center active:scale-95 border border-[#003047]">
                        <svg class="w-10 h-10 text-[#003047] mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-sm font-semibold text-gray-900">Start Booking</p>
                    </a>
                    <a href="../booking/" class="p-6 bg-white border border-[#003047] rounded-lg hover:bg-[#e6f0f3] transition text-center active:scale-95">
                        <svg class="w-10 h-10 text-[#003047] mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        <p class="text-sm font-semibold text-gray-900">Queue</p>
                    </a>
                    <a href="../booking/calendar.php" class="p-6 bg-white border border-[#003047] rounded-lg hover:bg-[#e6f0f3] transition text-center active:scale-95">
                        <svg class="w-10 h-10 text-[#003047] mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-sm font-semibold text-gray-900">Calendar</p>
                    </a>
                    <a href="../customers/" class="p-6 bg-white border border-[#003047] rounded-lg hover:bg-[#e6f0f3] transition text-center active:scale-95">
                        <svg class="w-10 h-10 text-[#003047] mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <p class="text-sm font-semibold text-gray-900">Customers</p>
                    </a>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Activities</h2>
                <div class="space-y-4">
                    <div class="flex items-center gap-3 pb-3 border-b border-gray-200">
                        <div class="w-10 h-10 bg-[#e6f0f3] rounded-full flex items-center justify-center">
                            <span class="text-xs font-bold text-[#003047]">SJ</span>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Sarah Johnson - Service Completed</p>
                            <p class="text-xs text-gray-500">2 minutes ago</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 pb-3 border-b border-gray-200">
                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                            <span class="text-xs font-bold text-purple-600">EC</span>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Emily Chen - Payment Processed</p>
                            <p class="text-xs text-gray-500">15 minutes ago</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 pb-3 border-b border-gray-200">
                        <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center">
                            <span class="text-xs font-bold text-amber-600">JM</span>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Jessica Martinez - New Walk-In</p>
                            <p class="text-xs text-gray-500">30 minutes ago</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-teal-100 rounded-full flex items-center justify-center">
                            <span class="text-xs font-bold text-teal-600">AT</span>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Amanda Taylor - Appointment Booked</p>
                            <p class="text-xs text-gray-500">1 hour ago</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php elseif ($current_role === 'technician'): ?>
        <!-- Technician Dashboard -->
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6">
            <!-- Today's Appointments -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Today's Appointments</p>
                        <p class="text-2xl font-bold text-gray-900">8</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Completed Today -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Completed Today</p>
                        <p class="text-2xl font-bold text-gray-900">5</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- In Progress -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">In Progress</p>
                        <p class="text-2xl font-bold text-gray-900">2</p>
                    </div>
                    <div class="w-12 h-12 bg-[#e6f0f3] rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#003047]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mb-6">
            <!-- Total -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Total</p>
                        <p class="text-2xl font-bold text-gray-900">$1,245.00</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Tip -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Tip</p>
                        <p class="text-2xl font-bold text-gray-900">$125.00</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Commission -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Commission</p>
                        <p class="text-2xl font-bold text-gray-900">$249.00</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions and Recent Activities -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Quick Actions Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Sarah Lee</h2>
                <div class="flex gap-6">
                    <!-- Left: Technician Photo -->
                    <div class="w-2/5">
                        <div class="w-full aspect-square bg-[#e6f0f3] rounded-lg flex items-center justify-center overflow-hidden border-2 border-[#003047]">
                            <svg class="w-3/4 h-3/4 text-[#003047]" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <!-- Right: Date/Time and Clock In Button -->
                    <div class="w-3/5 flex flex-col gap-3">
                        <div class="text-left text-2xl font-bold text-gray-900">
                            <div class="text-lg text-gray-600 mb-2" id="clockDate"></div>
                            <div class="text-4xl" id="clockTime"></div>
                        </div>
                        <button onclick="toggleTechnicianLogin()" id="technicianLoginBtn" class="w-full p-4 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95 flex items-center justify-start gap-4">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span id="technicianLoginText" class="font-semibold text-lg">Clock In</span>
                        </button>
                        <div id="clockInDateTimeDisplay" class="text-left text-sm text-gray-600 mb-1 hidden">
                            <span class="font-medium">Clocked In:</span><br> <span id="clockInDateTime"></span>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Activities</h2>
                <div class="space-y-4">
                    <div class="flex items-center gap-3 pb-3 border-b border-gray-200">
                        <div class="w-10 h-10 bg-[#e6f0f3] rounded-full flex items-center justify-center">
                            <span class="text-xs font-bold text-[#003047]">SJ</span>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Sarah Johnson - Service Completed</p>
                            <p class="text-xs text-gray-500">2 minutes ago</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 pb-3 border-b border-gray-200">
                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                            <span class="text-xs font-bold text-purple-600">EC</span>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Emily Chen - Service Started</p>
                            <p class="text-xs text-gray-500">15 minutes ago</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-teal-100 rounded-full flex items-center justify-center">
                            <span class="text-xs font-bold text-teal-600">JM</span>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Jessica Martinez - Appointment Started</p>
                            <p class="text-xs text-gray-500">1 hour ago</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php else: ?>
        <!-- Admin Dashboard (Original) -->

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6">
            <!-- Stat Card 1 -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Today's Revenue</p>
                        <p class="text-2xl font-bold text-gray-900">$1,245.00</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Stat Card 2 -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Active Services</p>
                        <p class="text-2xl font-bold text-gray-900">12</p>
                    </div>
                    <div class="w-12 h-12 bg-[#e6f0f3] rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#003047]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Stat Card 3 -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">Available Technicians</p>
                        <p class="text-2xl font-bold text-gray-900">4</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Stat Card 4 -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 mb-1">In Booking</p>
                        <p class="text-2xl font-bold text-gray-900">5</p>
                    </div>
                    <div class="w-12 h-12 bg-[#e6f0f3] rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#003047]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Quick Actions Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
                <div class="grid grid-cols-2 gap-3">
                    <a href="../booking/" class="p-4 bg-[#e6f0f3] rounded-lg hover:bg-[#d1e4e9] transition text-center active:scale-95">
                        <svg class="w-8 h-8 text-[#003047] mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                        <p class="text-sm font-medium text-gray-900">View Booking</p>
                    </a>
                    <a href="../services/" class="p-4 bg-purple-50 rounded-lg hover:bg-purple-100 transition text-center active:scale-95">
                        <svg class="w-8 h-8 text-purple-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                        </svg>
                        <p class="text-sm font-medium text-gray-900">Services</p>
                    </a>
                    <a href="../customers/" class="p-4 bg-teal-50 rounded-lg hover:bg-teal-100 transition text-center active:scale-95">
                        <svg class="w-8 h-8 text-teal-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <p class="text-sm font-medium text-gray-900">Customers</p>
                    </a>
                    <a href="../payments/" class="p-4 bg-blue-50 rounded-lg hover:bg-blue-100 transition text-center active:scale-95">
                        <svg class="w-8 h-8 text-blue-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                        </svg>
                        <p class="text-sm font-medium text-gray-900">Payments</p>
                    </a>
                    <a href="../users/" class="p-4 bg-indigo-50 rounded-lg hover:bg-indigo-100 transition text-center active:scale-95">
                        <svg class="w-8 h-8 text-indigo-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                        <p class="text-sm font-medium text-gray-900">Users</p>
                    </a>
                    <a href="../technicians/" class="p-4 bg-cyan-50 rounded-lg hover:bg-cyan-100 transition text-center active:scale-95">
                        <svg class="w-8 h-8 text-cyan-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-sm font-medium text-gray-900">Technician</p>
                    </a>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h2>
                <div class="space-y-4">
                    <div class="flex items-center gap-3 pb-3 border-b border-gray-200">
                        <div class="w-10 h-10 bg-[#e6f0f3] rounded-full flex items-center justify-center">
                            <span class="text-xs font-bold text-[#003047]">SJ</span>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Sarah Johnson - Service Completed</p>
                            <p class="text-xs text-gray-500">2 minutes ago</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 pb-3 border-b border-gray-200">
                        <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                            <span class="text-xs font-bold text-purple-600">EC</span>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Emily Chen - Payment Processed</p>
                            <p class="text-xs text-gray-500">15 minutes ago</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-teal-100 rounded-full flex items-center justify-center">
                            <span class="text-xs font-bold text-teal-600">JM</span>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-900">Jessica Martinez - New Appointment</p>
                            <p class="text-xs text-gray-500">1 hour ago</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</main>

<script>
// Technician Clock In/Clock Out functionality
function updateClockDateTime() {
    const clockDateElement = document.getElementById('clockDate');
    const clockTimeElement = document.getElementById('clockTime');
    if (clockDateElement && clockTimeElement) {
        const now = new Date();
        const date = now.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
        const time = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        clockDateElement.textContent = date;
        clockTimeElement.textContent = time;
    }
}

// Update clock display every second
setInterval(updateClockDateTime, 1000);
// Initial update
updateClockDateTime();

// Initialize technician login state from localStorage
function initializeTechnicianLoginState() {
    const isLoggedIn = localStorage.getItem('technician_dashboard_loggedIn') === 'true';
    const clockInDateTime = localStorage.getItem('technician_dashboard_clockInDateTime');
    const btn = document.getElementById('technicianLoginBtn');
    const text = document.getElementById('technicianLoginText');
    const clockInDisplay = document.getElementById('clockInDateTimeDisplay');
    const clockInDateTimeSpan = document.getElementById('clockInDateTime');
    
    if (btn && text) {
        if (isLoggedIn) {
            btn.classList.remove('bg-[#003047]', 'hover:bg-[#002535]');
            btn.classList.add('bg-red-600', 'hover:bg-red-700');
            text.textContent = 'Clock Out';
            
            // Show clock in date/time if available
            if (clockInDisplay && clockInDateTimeSpan && clockInDateTime) {
                clockInDateTimeSpan.textContent = clockInDateTime;
                clockInDisplay.classList.remove('hidden');
            }
        } else {
            btn.classList.remove('bg-red-600', 'hover:bg-red-700');
            btn.classList.add('bg-[#003047]', 'hover:bg-[#002535]');
            text.textContent = 'Clock In';
            
            // Hide clock in date/time
            if (clockInDisplay) {
                clockInDisplay.classList.add('hidden');
            }
        }
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeTechnicianLoginState();
});

function toggleTechnicianLogin() {
    const btn = document.getElementById('technicianLoginBtn');
    const text = document.getElementById('technicianLoginText');
    const clockInDisplay = document.getElementById('clockInDateTimeDisplay');
    const clockInDateTimeSpan = document.getElementById('clockInDateTime');
    
    if (!btn || !text) return;
    
    const isLoggedIn = text.textContent.trim() === 'Clock Out';
    const newState = !isLoggedIn;
    
    const now = new Date();
    const date = now.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    const time = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    const dateTime = `${date} ${time}`;
    
    // Save state to localStorage
    localStorage.setItem('technician_dashboard_loggedIn', newState.toString());
    
    if (newState) {
        btn.classList.remove('bg-[#003047]', 'hover:bg-[#002535]');
        btn.classList.add('bg-red-600', 'hover:bg-red-700');
        text.textContent = 'Clock Out';
        
        // Save clock in date/time to localStorage
        localStorage.setItem('technician_dashboard_clockInDateTime', dateTime);
        
        // Show clock in date/time above button
        if (clockInDisplay && clockInDateTimeSpan) {
            clockInDateTimeSpan.textContent = dateTime;
            clockInDisplay.classList.remove('hidden');
        }
        
        // Show success message with date and time
        showSuccessMessage('Clocked in successfully at ' + dateTime);
    } else {
        btn.classList.remove('bg-red-600', 'hover:bg-red-700');
        btn.classList.add('bg-[#003047]', 'hover:bg-[#002535]');
        text.textContent = 'Clock In';
        
        // Remove clock in date/time from localStorage
        localStorage.removeItem('technician_dashboard_clockInDateTime');
        
        // Hide clock in date/time
        if (clockInDisplay) {
            clockInDisplay.classList.add('hidden');
        }
        
        // Show success message with date and time
        showSuccessMessage('Clocked out successfully at ' + dateTime);
    }
    
    // Update the clock display
    updateClockDateTime();
}

function showSuccessMessage(message) {
    const successDiv = document.createElement('div');
    successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
    successDiv.textContent = message;
    document.body.appendChild(successDiv);
    setTimeout(() => successDiv.remove(), 3000);
}
</script>

<?php include '../includes/footer.php'; ?>

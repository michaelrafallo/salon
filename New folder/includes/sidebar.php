<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Handle role selection
if (isset($_GET['role'])) {
    $_SESSION['selected_role'] = $_GET['role'];
    // Redirect to remove the role parameter from URL
    $current_url = strtok($_SERVER["REQUEST_URI"], '?');
    header("Location: " . $current_url);
    exit;
}

// Get current role (default to 'admin')
$current_role = isset($_SESSION['selected_role']) ? $_SESSION['selected_role'] : 'admin';

// Define menu items and their access by role
$menu_items = [
    'dashboard' => ['admin', 'technician', 'receptionist'],
    'users' => ['admin'],
    'booking' => ['admin', 'receptionist'],
    'calendar' => ['admin', 'technician', 'receptionist'],
    'services' => ['admin', 'receptionist'],
    'customers' => ['admin', 'receptionist'],
    'technicians' => ['admin', 'receptionist'],
    'payments' => ['admin'],
    'settings' => ['admin']
];

// Function to check if menu item is accessible
function isMenuAccessible($menu_key, $role, $menu_items) {
    return isset($menu_items[$menu_key]) && in_array($role, $menu_items[$menu_key]);
}
?>

<!-- Left Sidebar -->
<aside class="w-64 bg-white border-r border-gray-200 flex flex-col fixed lg:relative h-screen lg:h-auto lg:translate-x-0 transform -translate-x-full transition-transform duration-300 z-50" id="sidebar">
    <!-- Logo -->
    <div class="p-6 border-b border-gray-200">
        <div class="flex items-center gap-2 mb-4">
            <div class="w-10 h-10 bg-[#003047] rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                </svg>
            </div>
            <span class="text-gray-900 font-bold text-xl">Nail Salon POS</span>
        </div>
        <!-- Role Tabs -->
        <div class="flex flex-wrap gap-2">
            <a href="?role=admin" class="px-3 py-1.5 text-xs font-medium rounded-lg transition <?php echo ($current_role === 'admin') ? 'bg-[#003047] text-white hover:bg-[#004d6b]' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                Admin
            </a>
            <a href="?role=receptionist" class="px-3 py-1.5 text-xs font-medium rounded-lg transition <?php echo ($current_role === 'receptionist') ? 'bg-[#003047] text-white hover:bg-[#004d6b]' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                Receptionist
            </a>
            <a href="?role=technician" class="px-3 py-1.5 text-xs font-medium rounded-lg transition <?php echo ($current_role === 'technician') ? 'bg-[#003047] text-white hover:bg-[#004d6b]' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'; ?>">
                Technician
            </a>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        <?php if (isMenuAccessible('dashboard', $current_role, $menu_items)): ?>
        <a href="../dashboard/" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition <?php echo (basename(dirname($_SERVER['PHP_SELF'])) == 'dashboard') ? 'bg-[#e6f0f3] text-[#003047]' : ''; ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
            </svg>
            <span class="font-medium">Dashboard</span>
        </a>
        <?php endif; ?>
        
        <?php if (isMenuAccessible('users', $current_role, $menu_items)): ?>
        <a href="../users/" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition <?php echo (basename(dirname($_SERVER['PHP_SELF'])) == 'users') ? 'bg-[#e6f0f3] text-[#003047]' : ''; ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <span class="font-medium">Staff</span>
        </a>
        <?php endif; ?>
        
        <?php if (isMenuAccessible('booking', $current_role, $menu_items)): ?>
        <a href="../booking/" class="flex items-center gap-3 px-4 py-3 rounded-lg font-medium <?php echo (basename(dirname($_SERVER['PHP_SELF'])) == 'booking' && basename($_SERVER['PHP_SELF']) == 'index.php') ? 'bg-[#e6f0f3] text-[#003047]' : 'text-gray-700 hover:bg-gray-50'; ?> transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
            </svg>
            <span class="font-medium">Queue</span>
        </a>
        <?php endif; ?>
        
        <?php if (isMenuAccessible('calendar', $current_role, $menu_items)): ?>
        <a href="../booking/calendar.php" class="flex items-center gap-3 px-4 py-3 rounded-lg font-medium <?php echo (basename($_SERVER['PHP_SELF']) == 'calendar.php' && basename(dirname($_SERVER['PHP_SELF'])) == 'booking') ? 'bg-[#e6f0f3] text-[#003047]' : 'text-gray-700 hover:bg-gray-50'; ?> transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <span class="font-medium">Calendar</span>
        </a>
        <?php endif; ?>
        
        <?php if (isMenuAccessible('services', $current_role, $menu_items)): ?>
        <a href="../services/" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition <?php echo (basename(dirname($_SERVER['PHP_SELF'])) == 'services') ? 'bg-[#e6f0f3] text-[#003047]' : ''; ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
            </svg>
            <span class="font-medium">Services</span>
        </a>
        <?php endif; ?>
        
        <?php if (isMenuAccessible('customers', $current_role, $menu_items)): ?>
        <a href="../customers/" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition <?php echo (basename(dirname($_SERVER['PHP_SELF'])) == 'customers') ? 'bg-[#e6f0f3] text-[#003047]' : ''; ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <span class="font-medium">Customers</span>
        </a>
        <?php endif; ?>
        
        <?php if (isMenuAccessible('technicians', $current_role, $menu_items)): ?>
        <a href="../technicians/" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition <?php echo (basename(dirname($_SERVER['PHP_SELF'])) == 'technicians') ? 'bg-[#e6f0f3] text-[#003047]' : ''; ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
            <span class="font-medium">Technicians</span>
        </a>
        <?php endif; ?>
        
        <?php if (isMenuAccessible('payments', $current_role, $menu_items)): ?>
        <a href="../payments/" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition <?php echo (basename(dirname($_SERVER['PHP_SELF'])) == 'payments') ? 'bg-[#e6f0f3] text-[#003047]' : ''; ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
            </svg>
            <span class="font-medium">Payments</span>
        </a>
        <?php endif; ?>
        
        <?php if (isMenuAccessible('settings', $current_role, $menu_items)): ?>
        <a href="../settings/" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition <?php echo (basename(dirname($_SERVER['PHP_SELF'])) == 'settings') ? 'bg-[#e6f0f3] text-[#003047]' : ''; ?>">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <span class="font-medium">Settings</span>
        </a>
        <?php endif; ?>
    </nav>

    <!-- User Profile -->
    <div class="p-4 border-t border-gray-200">
        <a href="../profile/" class="flex items-center gap-3 px-2 py-2 rounded-lg hover:bg-gray-50 transition <?php echo (basename(dirname($_SERVER['PHP_SELF'])) == 'profile') ? 'bg-[#e6f0f3]' : ''; ?>">
            <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center overflow-hidden">
                <svg class="w-6 h-6 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div class="flex-1">
                <p class="text-sm font-medium text-gray-900">Admin User</p>
                <p class="text-xs text-gray-500">Admin</p>
            </div>
        </a>
        <a href="../logout.php" class="flex items-center gap-2 px-2 py-2 mt-2 text-gray-600 hover:text-gray-900 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>
            <span class="text-sm">Logout</span>
        </a>
    </div>
</aside>

<!-- Mobile Sidebar Overlay -->
<div class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden" id="sidebarOverlay"></div>

<!-- Mobile Menu Button -->
<button class="lg:hidden fixed top-4 left-4 z-50 p-2 bg-white rounded-lg shadow-md" id="mobileMenuBtn">
    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
    </svg>
</button>

<script>
    // Mobile sidebar toggle
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');

    function toggleSidebar() {
        sidebar.classList.toggle('-translate-x-full');
        sidebarOverlay.classList.toggle('hidden');
    }

    mobileMenuBtn.addEventListener('click', toggleSidebar);
    sidebarOverlay.addEventListener('click', toggleSidebar);

    // Close sidebar on window resize to desktop
    window.addEventListener('resize', () => {
        if (window.innerWidth >= 1024) {
            sidebar.classList.remove('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
        }
    });
</script>

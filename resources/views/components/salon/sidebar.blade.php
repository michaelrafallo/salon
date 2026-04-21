@php
    $currentRole = session('salon_role', 'admin');
    $sidebarUser = \App\Models\User::where('email', session('salon_user_email'))->first();
    $sidebarUserName = $sidebarUser ? trim($sidebarUser->first_name . ' ' . $sidebarUser->last_name) : 'Admin User';
    $sidebarUserRole = ucfirst($sidebarUser->role ?? $currentRole);
    $sidebarInitials = $sidebarUser->initials ?? strtoupper(mb_substr($sidebarUserName, 0, 1) . mb_substr(strrchr($sidebarUserName . ' ', ' ') ?: 'U', 0, 1));
    $sidebarPhotoUrl = $sidebarUser?->profile_photo ? asset('storage/' . $sidebarUser->profile_photo) : null;
    $sidebarBusinessName = \App\Models\Setting::query()->where('option_key', 'business_name')->value('option_value');
    $sidebarBusinessLogo = \App\Models\Setting::query()->where('option_key', 'business_logo')->value('option_value');
    $sidebarLogoUrl = $sidebarBusinessLogo ? asset('storage/' . $sidebarBusinessLogo) : null;
    $menuItems = [
        'dashboard' => ['superadmin', 'admin', 'technician', 'receptionist'],
        'waiting_list' => ['superadmin', 'admin', 'receptionist'],
        'booking' => ['superadmin', 'admin', 'receptionist'],
        'calendar' => ['superadmin', 'admin', 'technician', 'receptionist'],
        'services' => ['superadmin', 'admin', 'receptionist'],
        'service_categories' => ['superadmin', 'admin'],
        'customers' => ['superadmin', 'admin', 'receptionist'],
        'technicians' => ['superadmin', 'admin', 'receptionist'],
        'turn_tracker' => ['superadmin', 'admin', 'receptionist'],
        'users' => ['superadmin', 'admin'],
        'payments' => ['superadmin', 'admin', 'receptionist'],
        'payout' => ['superadmin', 'admin', 'technician', 'receptionist'],
        'settings' => ['superadmin', 'admin'],
        'documentation' => ['superadmin', 'admin', 'technician', 'receptionist'],
    ];
    $canAccess = function ($key) use ($menuItems, $currentRole) {
        return isset($menuItems[$key]) && in_array($currentRole, $menuItems[$key]);
    };
@endphp

<aside class="w-64 bg-white border-r border-gray-200 flex flex-col fixed lg:relative h-screen lg:h-auto lg:translate-x-0 transform -translate-x-full transition-all duration-300 z-50" id="sidebar">
    <div class="p-4 border-b border-gray-200">
        <div class="flex flex-col items-center gap-2" id="sidebarBrandBlock">
            <div class="sidebar-logo rounded-lg flex items-center justify-center overflow-hidden flex-shrink-0 transition-all duration-300 {{ $sidebarLogoUrl ? '' : 'w-16 h-16 bg-[#003047]' }}" id="sidebarLogoWrap">
                @if($sidebarLogoUrl)
                    <img id="sidebarLogoImg" src="{{ $sidebarLogoUrl }}" alt="Logo" class="w-full h-auto object-contain">
                    <svg id="sidebarLogoIcon" class="w-8 h-8 text-white hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                @else
                    <img id="sidebarLogoImg" src="" alt="Logo" class="w-full h-auto object-contain hidden">
                    <svg id="sidebarLogoIcon" class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                @endif
            </div>
            <span class="sidebar-text text-gray-900 font-bold text-base text-center leading-tight" id="sidebarBusinessName">{{ $sidebarBusinessName ?: 'Nail Salon POS' }}</span>
        </div>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
        @if($canAccess('dashboard'))
        <a href="{{ route('salon.dashboard') }}" title="Dashboard" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition {{ request()->routeIs('salon.dashboard') ? 'bg-[#e6f0f3] text-[#003047]' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            <span class="sidebar-text font-medium">Dashboard</span>
        </a>
        @endif
        @if($canAccess('waiting_list'))
        <a href="{{ route('salon.booking.waiting-list') }}" title="Waiting List" class="flex items-center gap-3 px-4 py-3 rounded-lg font-medium {{ request()->routeIs('salon.booking.waiting-list') ? 'bg-[#e6f0f3] text-[#003047]' : 'text-gray-700 hover:bg-gray-50' }} transition">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="sidebar-text font-medium">Waiting List</span>
        </a>
        @endif
        @if($canAccess('booking'))
        <a href="{{ route('salon.booking.tickets') }}" title="Tickets" class="flex items-center gap-3 px-4 py-3 rounded-lg font-medium {{ request()->routeIs('salon.booking.tickets') ? 'bg-[#e6f0f3] text-[#003047]' : 'text-gray-700 hover:bg-gray-50' }} transition">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            <span class="sidebar-text font-medium">Tickets</span>
        </a>
        @endif
        @if($canAccess('calendar'))
        <a href="{{ route('salon.booking.calendar') }}" title="Calendar" class="flex items-center gap-3 px-4 py-3 rounded-lg font-medium {{ request()->routeIs('salon.booking.calendar') ? 'bg-[#e6f0f3] text-[#003047]' : 'text-gray-700 hover:bg-gray-50' }} transition">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            <span class="sidebar-text font-medium">Calendar</span>
        </a>
        @endif
        @if($canAccess('services'))
        <a href="{{ route('salon.services.index') }}" title="Services" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition {{ request()->routeIs('salon.services.*') ? 'bg-[#e6f0f3] text-[#003047]' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
            <span class="sidebar-text font-medium">Services</span>
        </a>
        @endif
        @if($canAccess('service_categories'))
        <a href="{{ route('salon.service-categories.index') }}" title="Service Categories" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition {{ request()->routeIs('salon.service-categories.*') ? 'bg-[#e6f0f3] text-[#003047]' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
            <span class="sidebar-text font-medium">Service Categories</span>
        </a>
        @endif
        @if($canAccess('customers'))
        <a href="{{ route('salon.customers.index') }}" title="Customers Data" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition {{ request()->routeIs('salon.customers.*') ? 'bg-[#e6f0f3] text-[#003047]' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            <span class="sidebar-text font-medium">Customers Data</span>
        </a>
        @endif
        @if($canAccess('technicians'))
        <a href="{{ route('salon.technicians.index') }}" title="Technicians" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition {{ request()->routeIs('salon.technicians.*') ? 'bg-[#e6f0f3] text-[#003047]' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
            <span class="sidebar-text font-medium">Technicians</span>
        </a>
        @endif
        @if($canAccess('turn_tracker'))
        <a href="{{ route('salon.turn-tracker.index') }}" title="Turn Tracker" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition {{ request()->routeIs('salon.turn-tracker.*') ? 'bg-[#e6f0f3] text-[#003047]' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
            <span class="sidebar-text font-medium">Turn Tracker</span>
        </a>
        @endif
        @if($canAccess('users'))
        <a href="{{ route('salon.users.index') }}" title="Users" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition {{ request()->routeIs('salon.users.*') ? 'bg-[#e6f0f3] text-[#003047]' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            <span class="sidebar-text font-medium">Users</span>
        </a>
        @endif
        @if($canAccess('payments'))
        <a href="{{ route('salon.payments.index') }}" title="Payments" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition {{ request()->routeIs('salon.payments.*') ? 'bg-[#e6f0f3] text-[#003047]' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
            <span class="sidebar-text font-medium">Payments</span>
        </a>
        @endif
        @if($canAccess('payout'))
        <a href="{{ route('salon.payout.index') }}" title="Payout" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition {{ request()->routeIs('salon.payout.*') ? 'bg-[#e6f0f3] text-[#003047]' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            <span class="sidebar-text font-medium">Payout</span>
        </a>
        @endif
        @if($canAccess('settings'))
        <a href="{{ route('salon.settings.index') }}" title="Settings" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition {{ request()->routeIs('salon.settings.*') ? 'bg-[#e6f0f3] text-[#003047]' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            <span class="sidebar-text font-medium">Settings</span>
        </a>
        @endif
        @if($canAccess('documentation'))
        <a href="{{ route('salon.documentation.index') }}" target="_blank" rel="noopener" title="Documentation" class="flex items-center gap-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition {{ request()->routeIs('salon.documentation.*') ? 'bg-[#e6f0f3] text-[#003047]' : '' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
            <span class="sidebar-text font-medium">Documentation</span>
        </a>
        @endif
    </nav>

    <div class="p-4 border-t border-gray-200">
        <a href="{{ route('salon.profile.index') }}" title="{{ $sidebarUserName }}" class="sidebar-footer-profile flex items-center gap-3 px-2 py-2 rounded-lg hover:bg-gray-50 transition {{ request()->routeIs('salon.profile.*') ? 'bg-[#e6f0f3]' : '' }}">
            <div class="w-10 h-10 rounded-full flex items-center justify-center overflow-hidden flex-shrink-0 {{ $sidebarPhotoUrl ? '' : 'bg-[#e6f0f3]' }}">
                @if($sidebarPhotoUrl)
                    <img src="{{ $sidebarPhotoUrl }}" alt="{{ $sidebarUserName }}" class="w-full h-full object-cover">
                @else
                    <span class="text-sm font-bold text-[#003047]">{{ $sidebarInitials }}</span>
                @endif
            </div>
            <div class="sidebar-text flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate">{{ $sidebarUserName }}</p>
                <p class="text-xs text-gray-500">{{ $sidebarUserRole }}</p>
            </div>
        </a>
        <div class="sidebar-footer-actions flex items-center mt-2">
            <a href="{{ route('salon.logout') }}" title="Logout" class="flex items-center gap-2 px-2 py-2 text-gray-600 hover:text-gray-900 transition">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                <span class="sidebar-text text-sm">Logout</span>
            </a>
        </div>
    </div>
</aside>

<button id="sidebarToggleBtn" title="Collapse sidebar" style="position:fixed; bottom:1.5rem; left:242px; z-index:51; display:none;">
    <svg class="w-3.5 h-3.5 text-gray-600 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
    </svg>
</button>

<div class="fixed inset-0 z-40 lg:hidden hidden" id="sidebarOverlay" style="background-color: #00000085;"></div>
<button type="button" class="lg:hidden fixed top-4 left-4 z-50 p-2 bg-white rounded-lg shadow-md" id="mobileMenuBtn" aria-label="Toggle menu">
    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
</button>

<style>
    #sidebar { transition: width 0.3s ease, transform 0.3s ease; }
    #sidebar.sidebar-collapsed { width: 4rem; }
    #sidebar.sidebar-collapsed .sidebar-text { display: none; }
    #sidebar.sidebar-collapsed .sidebar-logo { width: 2rem; }
    #sidebar.sidebar-collapsed .sidebar-logo img { width: 100%; height: auto; }
    #sidebar.sidebar-collapsed #sidebarBrandBlock { gap: 0; }
    #sidebar.sidebar-collapsed nav a { justify-content: center; padding-left: 0; padding-right: 0; }
    #sidebar.sidebar-collapsed nav a svg { margin: 0; }
    #sidebar.sidebar-collapsed .p-4 { padding: 0.75rem 0.5rem; }
    #sidebar.sidebar-collapsed .sidebar-footer-profile { justify-content: center; }
    #sidebar.sidebar-collapsed .sidebar-footer-actions { justify-content: center; }
    #sidebar.sidebar-collapsed .sidebar-footer-actions a { padding-left: 0; padding-right: 0; }
    #sidebarToggleBtn {
        width: 1.75rem;
        height: 1.75rem;
        border-radius: 50%;
        background: #fff;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        cursor: pointer;
        display: none;
        align-items: center;
        justify-content: center;
        transition: left 0.3s ease, background 0.15s;
    }
    #sidebarToggleBtn:hover { background: #f3f4f6; }
    #sidebarToggleBtn.toggle-collapsed svg { transform: rotate(180deg); }
    @media (min-width: 1024px) {
        #sidebarToggleBtn { display: flex; }
    }
</style>

<script>
(function() {
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const toggleBtn = document.getElementById('sidebarToggleBtn');
    if (!sidebar || !sidebarOverlay || !mobileMenuBtn) return;

    // Position toggle button at sidebar edge
    function positionToggleBtn() {
        if (!toggleBtn) return;
        if (window.innerWidth >= 1024) {
            toggleBtn.style.display = 'flex';
            var sidebarWidth = sidebar.classList.contains('sidebar-collapsed') ? 64 : 256;
            toggleBtn.style.left = (sidebarWidth - 14) + 'px';
        } else {
            toggleBtn.style.display = 'none';
        }
    }

    // Mobile toggle
    function toggleMobileSidebar() {
        sidebar.classList.toggle('-translate-x-full');
        sidebarOverlay.classList.toggle('hidden');
        mobileMenuBtn.classList.toggle('hidden');
    }
    mobileMenuBtn.addEventListener('click', toggleMobileSidebar);
    sidebarOverlay.addEventListener('click', toggleMobileSidebar);

    // Desktop collapse/expand toggle
    if (toggleBtn) {
        // Restore saved state
        if (localStorage.getItem('sidebarCollapsed') === '1') {
            sidebar.classList.add('sidebar-collapsed');
            toggleBtn.classList.add('toggle-collapsed');
            toggleBtn.title = 'Expand sidebar';
        }
        positionToggleBtn();

        toggleBtn.addEventListener('click', function() {
            sidebar.classList.toggle('sidebar-collapsed');
            toggleBtn.classList.toggle('toggle-collapsed');
            var isCollapsed = sidebar.classList.contains('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed ? '1' : '0');
            toggleBtn.title = isCollapsed ? 'Expand sidebar' : 'Collapse sidebar';
            positionToggleBtn();
        });
    }

    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) {
            sidebar.classList.remove('-translate-x-full');
            sidebarOverlay.classList.add('hidden');
        }
        positionToggleBtn();
    });
})();
</script>

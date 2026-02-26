@php
    $stats = $dashboardStats ?? [];
@endphp

<div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6">
    <a href="{{ route('salon.customers.index') }}" class="block bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md hover:border-gray-300 transition focus:outline-none focus:ring-2 focus:ring-[#003047] focus:ring-offset-2">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Customers</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format((int) ($stats['customers_total'] ?? 0)) }}</p>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-[#e6f0f3] rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-[#003047]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
            </div>
        </div>
    </a>

    <a href="{{ route('salon.turn-tracker.index') }}" class="block bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md hover:border-gray-300 transition focus:outline-none focus:ring-2 focus:ring-[#003047] focus:ring-offset-2">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Active Technicians</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format((int) ($stats['technicians_active'] ?? 0)) }}</p>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
        </div>
    </a>

    <a href="{{ route('salon.booking.waiting-list') }}?status=booked" class="block bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md hover:border-gray-300 transition focus:outline-none focus:ring-2 focus:ring-[#003047] focus:ring-offset-2">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Booked</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format((int) ($stats['waiting_booked'] ?? 0)) }}</p>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
        </div>
    </a>

    <a href="{{ route('salon.booking.waiting-list') }}?status=walk-in" class="block bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md hover:border-gray-300 transition focus:outline-none focus:ring-2 focus:ring-[#003047] focus:ring-offset-2">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Walk-in</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format((int) ($stats['waiting_walk_in'] ?? 0)) }}</p>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
            </div>
        </div>
    </a>
</div>

<div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-6">
    <a href="{{ route('salon.booking.tickets') }}?status=unpaid" class="block bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md hover:border-gray-300 transition focus:outline-none focus:ring-2 focus:ring-[#003047] focus:ring-offset-2">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Unpaid</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format((int) ($stats['tickets_unpaid'] ?? 0)) }}</p>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
    </a>

    <a href="{{ route('salon.booking.tickets') }}?status=paid" class="block bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md hover:border-gray-300 transition focus:outline-none focus:ring-2 focus:ring-[#003047] focus:ring-offset-2">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Paid</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format((int) ($stats['tickets_paid'] ?? 0)) }}</p>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>
    </a>

    <a href="{{ route('salon.booking.tickets') }}?status=cancelled" class="block bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md hover:border-gray-300 transition focus:outline-none focus:ring-2 focus:ring-[#003047] focus:ring-offset-2">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Cancelled</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format((int) ($stats['tickets_cancelled'] ?? 0)) }}</p>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </div>
        </div>
    </a>

    <a href="{{ route('salon.booking.tickets') }}?status=refunded" class="block bg-white rounded-lg shadow-sm border border-gray-200 p-5 hover:shadow-md hover:border-gray-300 transition focus:outline-none focus:ring-2 focus:ring-[#003047] focus:ring-offset-2">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 mb-1">Refunded</p>
                <p class="text-2xl font-bold text-gray-900">{{ number_format((int) ($stats['tickets_refunded'] ?? 0)) }}</p>
            </div>
            <div class="w-10 h-10 sm:w-12 sm:h-12 bg-rose-100 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
            </div>
        </div>
    </a>
</div>


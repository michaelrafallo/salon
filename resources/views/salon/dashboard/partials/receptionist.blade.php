@include('salon.dashboard.partials.stats-cards')
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-2 gap-4">
            <a href="{{ route('salon.booking.index') }}" class="p-6 bg-[#e6f0f3] rounded-lg hover:bg-[#d1e4e9] transition text-center active:scale-95 border border-[#003047]">
                <svg class="w-10 h-10 text-[#003047] mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <p class="text-sm font-semibold text-gray-900">New Booking</p>
            </a>
            <a href="{{ route('salon.booking.tickets') }}" class="p-6 bg-white border border-[#003047] rounded-lg hover:bg-[#e6f0f3] transition text-center active:scale-95">
                <svg class="w-10 h-10 text-[#003047] mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                <p class="text-sm font-semibold text-gray-900">Tickets</p>
            </a>
            <a href="{{ route('salon.booking.calendar') }}" class="p-6 bg-white border border-[#003047] rounded-lg hover:bg-[#e6f0f3] transition text-center active:scale-95">
                <svg class="w-10 h-10 text-[#003047] mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <p class="text-sm font-semibold text-gray-900">Calendar</p>
            </a>
            <a href="{{ route('salon.customers.index') }}" class="p-6 bg-white border border-[#003047] rounded-lg hover:bg-[#e6f0f3] transition text-center active:scale-95">
                <svg class="w-10 h-10 text-[#003047] mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                <p class="text-sm font-semibold text-gray-900">Customers</p>
            </a>
        </div>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Activities</h2>
        <div class="space-y-4">
            <div class="flex items-center gap-3 pb-3 border-b border-gray-200">
                <div class="w-10 h-10 bg-[#e6f0f3] rounded-full flex items-center justify-center"><span class="text-xs font-bold text-[#003047]">SJ</span></div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">Sarah Johnson - Service Completed</p>
                    <p class="text-xs text-gray-500">2 minutes ago</p>
                </div>
            </div>
            <div class="flex items-center gap-3 pb-3 border-b border-gray-200">
                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center"><span class="text-xs font-bold text-purple-600">EC</span></div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">Emily Chen - Payment Processed</p>
                    <p class="text-xs text-gray-500">15 minutes ago</p>
                </div>
            </div>
            <div class="flex items-center gap-3 pb-3 border-b border-gray-200">
                <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center"><span class="text-xs font-bold text-amber-600">JM</span></div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">Jessica Martinez - New Walk-In</p>
                    <p class="text-xs text-gray-500">30 minutes ago</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-teal-100 rounded-full flex items-center justify-center"><span class="text-xs font-bold text-teal-600">AT</span></div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-900">Amanda Taylor - Appointment Booked</p>
                    <p class="text-xs text-gray-500">1 hour ago</p>
                </div>
            </div>
        </div>
    </div>
</div>

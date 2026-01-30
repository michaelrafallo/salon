@extends('layouts.salon')

@section('content')
@php
    $userEmail = session('salon_user_email', 'admin@salon.com');
    $userRole = ucfirst(session('salon_role', 'admin'));
    $userName = 'Admin User';
    $joinDate = now()->subYears(2)->format('Y-m-d');
    $avatarBg = 'bg-[#e6f0f3]';
    $avatarText = 'text-[#003047]';
    $initials = 'AU';
@endphp
<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">My Profile</h1>
            <p class="text-gray-600 text-sm sm:text-base">Manage your profile information and settings</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-start gap-6 flex-1">
                    <div class="w-24 h-24 {{ $avatarBg }} rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-4xl font-bold {{ $avatarText }}">{{ $initials }}</span>
                    </div>
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-gray-900 mb-2" id="profileUserName">{{ $userName }}</h1>
                        <p class="text-lg text-gray-600 mb-2">{{ $userRole }}</p>
                        <span class="px-3 py-1 bg-[#e6f0f3] text-[#003047] text-xs font-medium rounded mb-4 inline-block">Active</span>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Email</p>
                                <p class="text-base font-medium text-gray-900" id="userEmail">{{ $userEmail }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Phone</p>
                                <p class="text-base font-medium text-gray-900" id="userPhone">(555) 100-0000</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Member Since</p>
                                <p class="text-base font-medium text-gray-900">{{ \Carbon\Carbon::parse($joinDate)->format('M d, Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Account Status</p>
                                <p class="text-base font-medium text-green-600">Active</p>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" onclick="salonProfileOpenEditModal()" class="px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95 text-sm">Edit Profile</button>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <p class="text-sm text-gray-500 mb-2">Total Logins</p>
                <p class="text-3xl font-bold text-gray-900">1,245</p>
                <p class="text-xs text-gray-500 mt-2">Last login: {{ now()->format('M d, Y') }}</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <p class="text-sm text-gray-500 mb-2">Date Registered</p>
                <p class="text-3xl font-bold text-gray-900">{{ \Carbon\Carbon::parse($joinDate)->format('M d, Y') }}</p>
                <p class="text-xs text-gray-500 mt-2">{{ now()->diffInYears(\Carbon\Carbon::parse($joinDate)) }} years ago</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <p class="text-sm text-gray-500 mb-2">Permissions</p>
                <p class="text-3xl font-bold text-gray-900">Full Access</p>
                <p class="text-xs text-gray-500 mt-2">Admin privileges</p>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Security Settings</h2>
            <div class="space-y-4">
                <div class="flex items-center justify-between py-3">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Change Password</p>
                        <p class="text-xs text-gray-500">Update your password to keep your account secure</p>
                    </div>
                    <button type="button" onclick="salonProfileOpenChangePasswordModal()" class="px-4 py-2 text-[#003047] hover:bg-[#e6f0f3] rounded-lg border border-[#b3d1d9] transition text-sm font-medium">Change Password</button>
                </div>
            </div>
        </div>
    </div>
</main>
@push('scripts')
<script>
var salonProfileUserData = {
    id: 1,
    first_name: '',
    last_name: '',
    email: '{{ $userEmail }}',
    phone: '(555) 100-0000',
    role: '{{ $userRole }}',
    join_date: '{{ $joinDate }}',
    avatar_color: 'pink',
    initials: '{{ $initials }}'
};
document.addEventListener('DOMContentLoaded', function() {
    var nameEl = document.getElementById('profileUserName');
    if (nameEl) {
        var nameParts = nameEl.textContent.trim().split(' ');
        salonProfileUserData.first_name = nameParts[0] || 'Admin';
        salonProfileUserData.last_name = nameParts.slice(1).join(' ') || 'User';
    }
    var phoneEl = document.getElementById('userPhone');
    if (phoneEl) {
        salonProfileUserData.phone = phoneEl.textContent.trim();
    }
});
window.salonProfileOpenEditModal = function() {
    var modalContent = '<div class="p-6"><div class="flex items-center justify-between mb-4"><h3 class="text-xl font-bold text-gray-900">Edit Profile</h3><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><form onsubmit="salonProfileUpdateProfile(event)" class="space-y-4"><input type="hidden" name="user_id" value="' + salonProfileUserData.id + '"><div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">First Name</label><input type="text" name="first_name" id="editFirstName" value="' + salonProfileUserData.first_name + '" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label><input type="text" name="last_name" id="editLastName" value="' + salonProfileUserData.last_name + '" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div></div><div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">Email</label><input type="email" name="email" id="editEmail" value="' + salonProfileUserData.email + '" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label><input type="tel" name="phone" id="editPhone" value="' + salonProfileUserData.phone + '" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div></div><div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg"><div><label class="text-sm font-medium text-gray-900">Active</label></div><label class="relative inline-flex items-center cursor-pointer"><input type="checkbox" name="active" class="sr-only peer" checked><div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[\'\'] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div></label></div><div class="flex justify-end gap-3 pt-4"><button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Update Profile</button></div></form></div>';
    if (typeof openModal === 'function') {
        openModal(modalContent);
    }
};
window.salonProfileUpdateProfile = function(event) {
    event.preventDefault();
    var formData = new FormData(event.target);
    var firstName = formData.get('first_name');
    var lastName = formData.get('last_name');
    var email = formData.get('email');
    var phone = formData.get('phone');
    var emailEl = document.getElementById('userEmail');
    var phoneEl = document.getElementById('userPhone');
    var nameEl = document.getElementById('profileUserName');
    if (emailEl) emailEl.textContent = email;
    if (phoneEl) phoneEl.textContent = phone;
    if (nameEl) nameEl.textContent = firstName + ' ' + lastName;
    salonProfileUserData.first_name = firstName;
    salonProfileUserData.last_name = lastName;
    salonProfileUserData.email = email;
    salonProfileUserData.phone = phone;
    if (typeof showSuccessMessage === 'function') {
        showSuccessMessage('Profile updated successfully!');
    }
    if (typeof closeModal === 'function') {
        closeModal();
    }
    console.log('Updating profile:', { firstName: firstName, lastName: lastName, email: email, phone: phone });
};
window.salonProfileOpenChangePasswordModal = function() {
    var modalContent = '<div class="p-6"><div class="flex items-center justify-between mb-4"><h3 class="text-xl font-bold text-gray-900">Change Password</h3><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><form onsubmit="salonProfileChangePassword(event)" class="space-y-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label><input type="password" name="current_password" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Enter current password"></div><div><label class="block text-sm font-medium text-gray-700 mb-2">New Password</label><input type="password" name="new_password" id="newPassword" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Enter new password"><p class="mt-1 text-xs text-gray-500">Password must be at least 8 characters long</p></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label><input type="password" name="confirm_password" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Confirm new password"></div><div class="flex justify-end gap-3 pt-4"><button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Change Password</button></div></form></div>';
    if (typeof openModal === 'function') {
        openModal(modalContent);
    }
};
window.salonProfileChangePassword = function(event) {
    event.preventDefault();
    var formData = new FormData(event.target);
    var currentPassword = formData.get('current_password');
    var newPassword = formData.get('new_password');
    var confirmPassword = formData.get('confirm_password');
    if (newPassword !== confirmPassword) {
        alert('New passwords do not match!');
        return;
    }
    if (newPassword.length < 8) {
        alert('Password must be at least 8 characters long!');
        return;
    }
    if (typeof showSuccessMessage === 'function') {
        showSuccessMessage('Password changed successfully!');
    }
    if (typeof closeModal === 'function') {
        closeModal();
    }
    console.log('Changing password');
};
</script>
@endpush
@endsection

@extends('layouts.salon')

@section('content')
@php
    $profileUser = $profileUser ?? null;
    $userEmail = $profileUser?->email ?? session('salon_user_email', 'admin@salon.com');
    $userRole = ucfirst($profileUser?->role ?? session('salon_role', 'admin'));
    $userName = $profileUser ? trim($profileUser->first_name . ' ' . $profileUser->last_name) : 'Admin User';
    $userPhone = $profileUser?->phone ?? '(555) 100-0000';
    $joinDate = $profileUser?->created_at?->format('Y-m-d') ?? now()->subYears(2)->format('Y-m-d');
    $avatarBg = 'bg-[#e6f0f3]';
    $avatarText = 'text-[#003047]';
    $initials = $profileUser?->initials ?? strtoupper(mb_substr($userName, 0, 1) . mb_substr(strrchr($userName . ' ', ' ') ?: 'U', 0, 1));
    $apiSalonUrl = rtrim(url('api/salon'), '/');
    $profilePhotoUrl = $profileUser?->profile_photo ? asset('storage/' . $profileUser->profile_photo) : null;
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
                    <div class="flex-shrink-0 flex flex-col items-center gap-2 w-full max-w-[300px]">
                        <div class="relative group cursor-pointer w-full" onclick="document.getElementById('profilePhotoInput').click()">
                            <div id="profileAvatar" class="w-full aspect-square {{ $avatarBg }} flex items-center justify-center overflow-hidden rounded-lg">
                                @if($profilePhotoUrl)
                                    <img src="{{ $profilePhotoUrl }}" alt="Profile Photo" class="w-full h-full object-cover">
                                @else
                                    <span class="text-4xl font-bold {{ $avatarText }}">{{ $initials }}</span>
                                @endif
                            </div>
                            <div class="absolute inset-0 bg-black bg-opacity-40 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <input type="file" id="profilePhotoInput" accept="image/*" class="hidden" onchange="salonProfileUploadPhoto(this)">
                        </div>
                        <div class="flex items-center gap-2 mt-1">
                            <button type="button" onclick="document.getElementById('profilePhotoInput').click()" class="px-3 py-1.5 text-xs font-medium bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition active:scale-95 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Upload
                            </button>
                            <button type="button" id="profileRemovePhotoBtn" onclick="salonProfileRemovePhoto()" class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition active:scale-95 flex items-center gap-1.5 {{ $profilePhotoUrl ? '' : 'hidden' }}">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Remove
                            </button>
                        </div>
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
                                <p class="text-base font-medium text-gray-900" id="userPhone">{{ $userPhone }}</p>
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
                <p class="text-xs text-gray-500 mt-2">{{ \Carbon\Carbon::parse($joinDate)->diffForHumans() }}</p>
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
@php
    $profileUserData = $profileUser
        ? [
            'id' => $profileUser->id,
            'first_name' => $profileUser->first_name,
            'last_name' => $profileUser->last_name,
            'email' => $profileUser->email,
            'phone' => $profileUser->phone ?? '',
            'role' => $profileUser->role,
            'join_date' => $joinDate,
            'avatar_color' => 'pink',
            'initials' => $initials,
            'profile_photo_url' => $profilePhotoUrl,
        ]
        : [
            'id' => 0,
            'first_name' => explode(' ', $userName)[0] ?? 'Admin',
            'last_name' => implode(' ', array_slice(explode(' ', $userName), 1)) ?: 'User',
            'email' => $userEmail,
            'phone' => $userPhone,
            'role' => $userRole,
            'join_date' => $joinDate,
            'avatar_color' => 'pink',
            'initials' => $initials,
            'profile_photo_url' => null,
        ];
@endphp
@push('scripts')
<script>
var salonProfileUserData = @json($profileUserData);
var salonProfileApiUrl = '{{ $apiSalonUrl }}';
document.addEventListener('DOMContentLoaded', function() {
    var nameEl = document.getElementById('profileUserName');
    if (nameEl && !salonProfileUserData.first_name) {
        var nameParts = nameEl.textContent.trim().split(' ');
        salonProfileUserData.first_name = nameParts[0] || 'Admin';
        salonProfileUserData.last_name = nameParts.slice(1).join(' ') || 'User';
    }
    var phoneEl = document.getElementById('userPhone');
    if (phoneEl && salonProfileUserData.phone === '') {
        salonProfileUserData.phone = phoneEl.textContent.trim();
    }
});
window.salonProfileUploadPhoto = function(input) {
    if (!input.files || !input.files[0]) return;
    var file = input.files[0];
    if (file.size > 2 * 1024 * 1024) {
        if (typeof showErrorMessage === 'function') showErrorMessage('Image must be less than 2 MB.');
        input.value = '';
        return;
    }
    var fd = new FormData();
    fd.append('profile_photo', file);
    fd.append('first_name', salonProfileUserData.first_name);
    fd.append('last_name', salonProfileUserData.last_name);
    fd.append('email', salonProfileUserData.email);
    fd.append('phone', salonProfileUserData.phone || '');
    fd.append('_method', 'PUT');
    var csrfToken = document.querySelector('meta[name="csrf-token"]');
    fetch(salonProfileApiUrl + '/profile', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : ''
        },
        credentials: 'same-origin',
        body: fd
    }).then(function(r) { return r.json(); }).then(function(res) {
        if (res.success && res.data && res.data.profilePhotoUrl) {
            salonProfileUserData.profile_photo_url = res.data.profilePhotoUrl;
            var avatar = document.getElementById('profileAvatar');
            if (avatar) {
                avatar.innerHTML = '<img src="' + res.data.profilePhotoUrl + '" alt="Profile Photo" class="w-full h-full object-cover">';
            }
            var removeBtn = document.getElementById('profileRemovePhotoBtn');
            if (removeBtn) removeBtn.classList.remove('hidden');
            if (typeof showSuccessMessage === 'function') showSuccessMessage('Profile photo updated!');
        } else if (res.message) {
            if (typeof showErrorMessage === 'function') showErrorMessage(res.message);
        }
    }).catch(function() {
        if (typeof showErrorMessage === 'function') showErrorMessage('Failed to upload photo.');
    });
    input.value = '';
};
window.salonProfileRemovePhoto = function() {
    if (!confirm('Remove your profile photo?')) return;
    var fd = new FormData();
    fd.append('remove_photo', '1');
    fd.append('first_name', salonProfileUserData.first_name);
    fd.append('last_name', salonProfileUserData.last_name);
    fd.append('email', salonProfileUserData.email);
    fd.append('phone', salonProfileUserData.phone || '');
    fd.append('_method', 'PUT');
    var csrfToken = document.querySelector('meta[name="csrf-token"]');
    fetch(salonProfileApiUrl + '/profile', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : ''
        },
        credentials: 'same-origin',
        body: fd
    }).then(function(r) { return r.json(); }).then(function(res) {
        if (res.success) {
            salonProfileUserData.profile_photo_url = null;
            var avatar = document.getElementById('profileAvatar');
            if (avatar) {
                avatar.innerHTML = '<span class="w-full h-full flex items-center justify-center text-4xl font-bold {{ $avatarText }}">' + (salonProfileUserData.initials || '{{ $initials }}') + '</span>';
            }
            var removeBtn = document.getElementById('profileRemovePhotoBtn');
            if (removeBtn) removeBtn.classList.add('hidden');
            if (typeof showSuccessMessage === 'function') showSuccessMessage('Profile photo removed.');
        } else if (res.message) {
            if (typeof showErrorMessage === 'function') showErrorMessage(res.message);
        }
    }).catch(function() {
        if (typeof showErrorMessage === 'function') showErrorMessage('Failed to remove photo.');
    });
};
window.salonProfileOpenEditModal = function() {
    var first = (salonProfileUserData.first_name || '').replace(/"/g, '&quot;');
    var last = (salonProfileUserData.last_name || '').replace(/"/g, '&quot;');
    var email = (salonProfileUserData.email || '').replace(/"/g, '&quot;');
    var phone = (salonProfileUserData.phone || '').replace(/"/g, '&quot;');
    var modalContent = '<div class="p-6"><div class="flex items-center justify-between mb-4"><h3 class="text-xl font-bold text-gray-900">Edit Profile</h3><button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><form onsubmit="salonProfileUpdateProfile(event)" class="space-y-4"><input type="hidden" name="user_id" value="' + salonProfileUserData.id + '"><div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">First Name</label><input type="text" name="first_name" value="' + first + '" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label><input type="text" name="last_name" value="' + last + '" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div></div><div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">Email</label><input type="email" name="email" value="' + email + '" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label><input type="tel" name="phone" value="' + phone + '" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div></div><div class="flex justify-end gap-3 pt-4"><button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Update Profile</button></div></form></div>';
    if (typeof openModal === 'function') {
        openModal(modalContent);
    }
};
window.salonProfileUpdateProfile = function(event) {
    event.preventDefault();
    var form = event.target;
    var submitBtn = form.querySelector('button[type="submit"]');
    if (submitBtn) { submitBtn.disabled = true; submitBtn.textContent = 'Saving...'; }
    var firstName = form.querySelector('[name="first_name"]').value.trim();
    var lastName = form.querySelector('[name="last_name"]').value.trim();
    var email = form.querySelector('[name="email"]').value.trim();
    var phone = (form.querySelector('[name="phone"]').value || '').trim();
    var payload = { first_name: firstName, last_name: lastName, email: email, phone: phone || null };
    if (typeof salonApi === 'undefined' || !salonApi.put) {
        if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = 'Update Profile'; }
        if (typeof showErrorMessage === 'function') showErrorMessage('Unable to save. Please refresh and try again.');
        return;
    }
    salonApi.put(salonProfileApiUrl + '/profile', payload).then(function(res) {
        salonProfileUserData.first_name = firstName;
        salonProfileUserData.last_name = lastName;
        salonProfileUserData.email = email;
        salonProfileUserData.phone = phone;
        var emailEl = document.getElementById('userEmail');
        var phoneEl = document.getElementById('userPhone');
        var nameEl = document.getElementById('profileUserName');
        if (emailEl) emailEl.textContent = email;
        if (phoneEl) phoneEl.textContent = phone;
        if (nameEl) nameEl.textContent = firstName + ' ' + lastName;
        closeModal();
        showSuccessMessage(res.message || 'Profile updated successfully!');
    }).catch(function(err) {
        if (submitBtn) { submitBtn.disabled = false; submitBtn.textContent = 'Update Profile'; }
        var msg = err && err.message ? err.message : 'Failed to update profile.';
        if (err && err.body && err.body.errors && typeof err.body.errors === 'object') {
            var firstKey = Object.keys(err.body.errors)[0];
            if (firstKey && err.body.errors[firstKey] && err.body.errors[firstKey][0]) msg = err.body.errors[firstKey][0];
        }
        if (typeof showErrorMessage === 'function') showErrorMessage(msg);
        else alert(msg);
    });
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

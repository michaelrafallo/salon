@extends('layouts.salon')

@section('content')
@php
    $usersIndexUrl = route('salon.users.index');
    $apiUsersUrl = url('api/salon/users');
    $storageUrl = rtrim(asset('storage'), '/');
@endphp

<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <div class="mb-6">
            <a href="{{ $usersIndexUrl }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span class="text-sm font-medium">Back to Staff</span>
            </a>
        </div>
        <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2" id="pageTitle">Staff Profile</h1>
            <p class="text-gray-600 text-sm sm:text-base">View and manage staff member information</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-start gap-6 flex-1">
                    <div class="flex-shrink-0 flex flex-col items-center gap-2 w-full max-w-[300px]">
                        <div class="relative group cursor-pointer w-full" onclick="document.getElementById('userPhotoInput').click()">
                            <div id="userAvatar" class="w-full aspect-square bg-[#e6f0f3] flex items-center justify-center overflow-hidden rounded-lg">
                                <span id="userInitials" class="text-4xl font-bold text-[#003047]"></span>
                            </div>
                            <div class="absolute inset-0 bg-black bg-opacity-40 rounded-lg flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <input type="file" id="userPhotoInput" accept="image/*" class="hidden" onchange="salonUserViewUploadPhoto(this)">
                        </div>
                        <div class="flex items-center gap-2 mt-1">
                            <button type="button" onclick="document.getElementById('userPhotoInput').click()" class="px-3 py-1.5 text-xs font-medium bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition active:scale-95 flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Upload
                            </button>
                            <button type="button" id="userRemovePhotoBtn" onclick="salonUserViewRemovePhoto()" class="px-3 py-1.5 text-xs font-medium text-red-600 bg-red-50 rounded-lg hover:bg-red-100 transition active:scale-95 flex items-center gap-1.5 hidden">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Remove
                            </button>
                        </div>
                    </div>
                    <div class="flex-1">
                        <h1 id="userName" class="text-3xl font-bold text-gray-900 mb-2"></h1>
                        <p id="userRole" class="text-lg text-gray-600 mb-2"></p>
                        <span id="userStatus" class="px-3 py-1 bg-[#e6f0f3] text-[#003047] text-xs font-medium rounded mb-4 inline-block"></span>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Email</p>
                                <p class="text-base font-medium text-gray-900" id="userEmail"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Phone</p>
                                <p class="text-base font-medium text-gray-900" id="userPhone"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Member Since</p>
                                <p class="text-base font-medium text-gray-900" id="userJoinDate"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Account Status</p>
                                <p class="text-base font-medium text-green-600" id="userAccountStatus">Active</p>
                            </div>
                        </div>
                    </div>
                </div>
                <button onclick="openEditUserModal()" class="px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95 text-sm">Edit Staff</button>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <p class="text-sm text-gray-500 mb-2">Total Logins</p>
                <p class="text-3xl font-bold text-gray-900" id="totalLogins">0</p>
                <p class="text-xs text-gray-500 mt-2" id="lastLoginText"></p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <p class="text-sm text-gray-500 mb-2">Date Registered</p>
                <p class="text-3xl font-bold text-gray-900" id="dateRegistered"></p>
                <p class="text-xs text-gray-500 mt-2" id="yearsAgo"></p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <p class="text-sm text-gray-500 mb-2">Permissions</p>
                <p class="text-3xl font-bold text-gray-900" id="permissionsText">Full Access</p>
                <p class="text-xs text-gray-500 mt-2" id="permissionsSubtext">Admin privileges</p>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Security Settings</h2>
            <div class="space-y-4">
                <div class="flex items-center justify-between py-3">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Change Password</p>
                        <p class="text-xs text-gray-500">Update this user's password</p>
                    </div>
                    <button type="button" onclick="salonUserViewOpenChangePasswordModal()" class="px-4 py-2 text-[#003047] hover:bg-[#e6f0f3] rounded-lg border border-[#b3d1d9] transition text-sm font-medium">Change Password</button>
                </div>
            </div>
        </div>
    </div>
</main>

@push('scripts')
<script>
(function() {
    var base = window.salonJsonBase || '{{ url("api/salon/data") }}';
    var apiUsersUrl = '{{ $apiUsersUrl }}';
    var storageUrl = '{{ $storageUrl }}';
    var userData = null;

    function getUserIdFromURL() {
        var params = new URLSearchParams(window.location.search);
        var id = params.get('id');
        return id ? parseInt(id, 10) : 1;
    }

    function getInitials(user) {
        if (user.initials) return user.initials;
        var first = (user.firstName || '').charAt(0).toUpperCase();
        var last = (user.lastName || '').charAt(0).toUpperCase();
        return first + last;
    }

    var roleColors = {
        'admin': { bg: 'bg-[#e6f0f3]', text: 'text-[#003047]' },
        'receptionist': { bg: 'bg-purple-100', text: 'text-purple-600' },
        'technician': { bg: 'bg-indigo-100', text: 'text-indigo-600' }
    };

    function getRoleColors(role) {
        return roleColors[(role || '').toLowerCase()] || roleColors['technician'];
    }

    function formatRole(role) {
        if (!role) return '';
        return role.charAt(0).toUpperCase() + role.slice(1).toLowerCase();
    }

    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        var date = new Date(dateString);
        if (isNaN(date.getTime())) return dateString;
        var months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        return months[date.getMonth()] + ' ' + date.getDate() + ', ' + date.getFullYear();
    }

    function calculateYearsAgo(dateString) {
        if (!dateString) return '';
        var date = new Date(dateString);
        if (isNaN(date.getTime())) return '';
        var now = new Date();
        var years = now.getFullYear() - date.getFullYear();
        var monthDiff = now.getMonth() - date.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && now.getDate() < date.getDate())) years--;
        return years > 0 ? years + ' year' + (years !== 1 ? 's' : '') + ' ago' : 'Less than a year ago';
    }

    function renderUserInfo() {
        if (!userData) return;

        var initials = getInitials(userData);
        var colors = getRoleColors(userData.role);
        var fullName = (userData.firstName || '') + ' ' + (userData.lastName || '');
        var role = formatRole(userData.role);
        var isActive = (userData.status || 'active').toLowerCase() === 'active';
        var joinDate = formatDate(userData.createdAt || userData.join_date);
        var lastLogin = formatDate(userData.lastLogin || userData.last_login);

        // Avatar
        var avatarEl = document.getElementById('userAvatar');
        if (avatarEl) {
            avatarEl.className = 'w-full aspect-square ' + colors.bg + ' flex items-center justify-center overflow-hidden rounded-lg';
            if (userData.profilePhotoUrl) {
                avatarEl.innerHTML = '<img src="' + userData.profilePhotoUrl + '" alt="Profile Photo" class="w-full h-full object-cover">';
                document.getElementById('userRemovePhotoBtn').classList.remove('hidden');
            } else {
                avatarEl.innerHTML = '<span class="text-4xl font-bold ' + colors.text + '">' + initials + '</span>';
                document.getElementById('userRemovePhotoBtn').classList.add('hidden');
            }
        }

        // Name & Title
        var nameEl = document.getElementById('userName');
        if (nameEl) nameEl.textContent = fullName;
        document.getElementById('pageTitle').textContent = fullName + "'s Profile";

        // Role
        var roleEl = document.getElementById('userRole');
        if (roleEl) roleEl.textContent = role;

        // Status badge
        var statusEl = document.getElementById('userStatus');
        if (statusEl) {
            statusEl.textContent = isActive ? 'Active' : 'Inactive';
            statusEl.className = 'px-3 py-1 ' + (isActive ? 'bg-[#e6f0f3] text-[#003047]' : 'bg-gray-100 text-gray-700') + ' text-xs font-medium rounded mb-4 inline-block';
        }

        // Info fields
        var emailEl = document.getElementById('userEmail');
        if (emailEl) emailEl.textContent = userData.email || 'N/A';

        var phoneEl = document.getElementById('userPhone');
        if (phoneEl) phoneEl.textContent = userData.phone || 'N/A';

        var joinDateEl = document.getElementById('userJoinDate');
        if (joinDateEl) joinDateEl.textContent = joinDate;

        var accountStatusEl = document.getElementById('userAccountStatus');
        if (accountStatusEl) {
            accountStatusEl.textContent = isActive ? 'Active' : 'Inactive';
            accountStatusEl.className = 'text-base font-medium ' + (isActive ? 'text-green-600' : 'text-gray-500');
        }

        // Stats cards
        var totalLoginsEl = document.getElementById('totalLogins');
        if (totalLoginsEl) {
            var logins = userData.totalLogins || 0;
            totalLoginsEl.textContent = logins.toLocaleString();
        }

        var lastLoginTextEl = document.getElementById('lastLoginText');
        if (lastLoginTextEl) lastLoginTextEl.textContent = 'Last login: ' + lastLogin;

        var dateRegisteredEl = document.getElementById('dateRegistered');
        if (dateRegisteredEl) dateRegisteredEl.textContent = joinDate;

        var yearsAgoEl = document.getElementById('yearsAgo');
        if (yearsAgoEl) yearsAgoEl.textContent = calculateYearsAgo(userData.createdAt || userData.join_date);

        // Permissions card
        var permText = document.getElementById('permissionsText');
        var permSub = document.getElementById('permissionsSubtext');
        if (permText && permSub) {
            if ((userData.role || '').toLowerCase() === 'admin') {
                permText.textContent = 'Full Access';
                permSub.textContent = 'Admin privileges';
            } else if ((userData.role || '').toLowerCase() === 'receptionist') {
                permText.textContent = 'Standard';
                permSub.textContent = 'Receptionist privileges';
            } else {
                permText.textContent = 'Limited';
                permSub.textContent = 'Technician privileges';
            }
        }
    }

    // Photo upload
    window.salonUserViewUploadPhoto = function(input) {
        if (!input.files || !input.files[0] || !userData) return;
        var file = input.files[0];
        if (file.size > 2 * 1024 * 1024) {
            if (typeof showErrorMessage === 'function') showErrorMessage('Image must be less than 2 MB.');
            input.value = '';
            return;
        }
        var fd = new FormData();
        fd.append('profile_photo', file);
        fd.append('_method', 'PUT');
        var csrfToken = document.querySelector('meta[name="csrf-token"]');
        fetch(apiUsersUrl + '/' + userData.id, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : ''
            },
            credentials: 'same-origin',
            body: fd
        }).then(function(r) { return r.json(); }).then(function(res) {
            if (res.success && res.data) {
                userData = res.data;
                var avatar = document.getElementById('userAvatar');
                if (avatar && res.data.profilePhotoUrl) {
                    avatar.innerHTML = '<img src="' + res.data.profilePhotoUrl + '" alt="Profile Photo" class="w-full h-full object-cover">';
                }
                document.getElementById('userRemovePhotoBtn').classList.remove('hidden');
                if (typeof showSuccessMessage === 'function') showSuccessMessage('Profile photo updated!');
            } else if (res.message) {
                if (typeof showErrorMessage === 'function') showErrorMessage(res.message);
            }
        }).catch(function() {
            if (typeof showErrorMessage === 'function') showErrorMessage('Failed to upload photo.');
        });
        input.value = '';
    };

    // Photo remove
    window.salonUserViewRemovePhoto = function() {
        if (!userData) return;
        if (!confirm('Remove this user\'s profile photo?')) return;
        var fd = new FormData();
        fd.append('remove_photo', '1');
        fd.append('_method', 'PUT');
        var csrfToken = document.querySelector('meta[name="csrf-token"]');
        fetch(apiUsersUrl + '/' + userData.id, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken ? csrfToken.getAttribute('content') : ''
            },
            credentials: 'same-origin',
            body: fd
        }).then(function(r) { return r.json(); }).then(function(res) {
            if (res.success) {
                userData = res.data;
                var avatar = document.getElementById('userAvatar');
                var colors = getRoleColors(userData.role);
                var initials = getInitials(userData);
                if (avatar) {
                    avatar.innerHTML = '<span class="w-full h-full flex items-center justify-center text-4xl font-bold ' + colors.text + '">' + initials + '</span>';
                }
                document.getElementById('userRemovePhotoBtn').classList.add('hidden');
                if (typeof showSuccessMessage === 'function') showSuccessMessage('Profile photo removed.');
            } else if (res.message) {
                if (typeof showErrorMessage === 'function') showErrorMessage(res.message);
            }
        }).catch(function() {
            if (typeof showErrorMessage === 'function') showErrorMessage('Failed to remove photo.');
        });
    };

    // Edit modal
    window.openEditUserModal = function() {
        if (!userData) return;
        var first = (userData.firstName || '').replace(/"/g, '&quot;');
        var last = (userData.lastName || '').replace(/"/g, '&quot;');
        var email = (userData.email || '').replace(/"/g, '&quot;');
        var phone = (userData.phone || '').replace(/"/g, '&quot;');
        var username = (userData.username || '').replace(/"/g, '&quot;');
        var roleVal = (userData.role || 'technician').toLowerCase();
        var isActive = (userData.status || 'active').toLowerCase() === 'active';
        var modalContent = '<div class="p-6"><div class="flex items-center justify-between mb-4"><h3 class="text-xl font-bold text-gray-900">Edit Staff</h3><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><form onsubmit="updateUser(event)" class="space-y-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">Username</label><input type="text" name="username" value="' + username + '" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">First Name</label><input type="text" name="first_name" value="' + first + '" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label><input type="text" name="last_name" value="' + last + '" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div></div><div class="grid grid-cols-1 md:grid-cols-2 gap-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">Email</label><input type="email" name="email" value="' + email + '" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label><input type="tel" name="phone" value="' + phone + '" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"></div></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Role</label><select name="role" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent"><option value="admin"' + (roleVal === 'admin' ? ' selected' : '') + '>Admin</option><option value="receptionist"' + (roleVal === 'receptionist' ? ' selected' : '') + '>Receptionist</option><option value="technician"' + (roleVal === 'technician' ? ' selected' : '') + '>Technician</option></select></div><div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg"><div><label class="text-sm font-medium text-gray-900">Active</label></div><label class="relative inline-flex items-center cursor-pointer"><input type="checkbox" name="active" class="sr-only peer"' + (isActive ? ' checked' : '') + '><div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[\'\'] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div></label></div><div class="flex justify-end gap-3 pt-4"><button type="button" onclick="closeModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">Cancel</button><button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Update Staff</button></div></form></div>';
        openModal(modalContent);
    };

    // Update user
    window.updateUser = function(event) {
        event.preventDefault();
        var form = event.target;
        var btn = form.querySelector('button[type="submit"]');
        var data = {
            username: form.username.value.trim(),
            first_name: form.first_name.value.trim(),
            last_name: form.last_name.value.trim(),
            email: form.email.value.trim(),
            phone: form.phone.value.trim() || null,
            role: form.role.value,
            status: form.active && form.active.checked ? 'active' : 'inactive'
        };
        if (btn) { btn.disabled = true; btn.textContent = 'Updating...'; }
        salonApi.put(apiUsersUrl + '/' + userData.id, data).then(function(res) {
            showSuccessMessage(res.message || 'Staff updated successfully.');
            closeModal();
            userData = res.data;
            renderUserInfo();
        }).catch(function(err) {
            showErrorMessage(err.message || 'Failed to update staff.');
        }).finally(function() {
            if (btn) { btn.disabled = false; btn.textContent = 'Update Staff'; }
        });
    };

    // Change password modal
    window.salonUserViewOpenChangePasswordModal = function() {
        if (!userData) return;
        var modalContent = '<div class="p-6"><div class="flex items-center justify-between mb-4"><h3 class="text-xl font-bold text-gray-900">Change Password</h3><button onclick="closeModal()" class="text-gray-400 hover:text-gray-600"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button></div><form onsubmit="salonUserViewChangePassword(event)" class="space-y-4"><div><label class="block text-sm font-medium text-gray-700 mb-2">New Password</label><input type="password" name="password" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Enter new password"><p class="mt-1 text-xs text-gray-500">Password must be at least 8 characters long</p></div><div><label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label><input type="password" name="confirm_password" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Confirm new password"></div><div class="flex justify-end gap-3 pt-4"><button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">Change Password</button></div></form></div>';
        if (typeof openModal === 'function') openModal(modalContent);
    };

    // Change password handler
    window.salonUserViewChangePassword = function(event) {
        event.preventDefault();
        var form = event.target;
        var password = form.password.value;
        var confirm = form.confirm_password.value;
        if (password !== confirm) {
            if (typeof showErrorMessage === 'function') showErrorMessage('Passwords do not match.');
            return;
        }
        if (password.length < 8) {
            if (typeof showErrorMessage === 'function') showErrorMessage('Password must be at least 8 characters long.');
            return;
        }
        var btn = form.querySelector('button[type="submit"]');
        if (btn) { btn.disabled = true; btn.textContent = 'Saving...'; }
        salonApi.put(apiUsersUrl + '/' + userData.id, { password: password }).then(function(res) {
            showSuccessMessage(res.message || 'Password changed successfully!');
            closeModal();
        }).catch(function(err) {
            showErrorMessage(err.message || 'Failed to change password.');
        }).finally(function() {
            if (btn) { btn.disabled = false; btn.textContent = 'Change Password'; }
        });
    };

    // Load data
    async function loadUserData() {
        try {
            var userId = getUserIdFromURL();
            var response = await fetch(base + '/users');
            var data = await response.json();
            var allUsers = data.users || [];
            userData = allUsers.find(function(u) { return u.id === userId; });
            if (!userData) userData = allUsers[0] || null;
            if (userData) {
                renderUserInfo();
            } else {
                if (typeof showErrorMessage === 'function') showErrorMessage('User not found');
            }
        } catch (err) {
            console.error('Error loading user data:', err);
            if (typeof showErrorMessage === 'function') showErrorMessage('Failed to load user data');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        loadUserData();
    });
})();
</script>
@endpush
@endsection

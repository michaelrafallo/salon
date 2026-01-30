@extends('layouts.salon')

@section('content')
@php
    $usersIndexUrl = route('salon.users.index');
@endphp

<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ $usersIndexUrl }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                <span class="text-sm font-medium">Back to Staff</span>
            </a>
        </div>

        <!-- Staff Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-start gap-6 flex-1">
                    <div id="userAvatar" class="w-24 h-24 bg-[#e6f0f3] rounded-full flex items-center justify-center flex-shrink-0">
                        <span id="userInitials" class="text-4xl font-bold text-[#003047]"></span>
                    </div>
                    <div class="flex-1">
                        <h1 id="userName" class="text-3xl font-bold text-gray-900 mb-2"></h1>
                        <p id="userRole" class="text-lg text-gray-600 mb-2 font-medium"></p>
                        <span id="userStatus" class="px-3 py-1 bg-green-100 text-green-700 text-xs font-medium rounded mb-4 inline-block"></span>
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
                                <p class="text-sm text-gray-500 mb-1">Date Registered</p>
                                <p class="text-base font-medium text-gray-900" id="userJoinDate"></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Last Login</p>
                                <p class="text-base font-medium text-gray-900" id="userLastLogin"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <button onclick="openEditUserModal()" class="px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95 text-sm">
                    Edit Staff
                </button>
            </div>
        </div>

        <!-- Statistics Cards -->
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
                <p class="text-sm text-gray-500 mb-2">Account Status</p>
                <p class="text-3xl font-bold" id="accountStatus"></p>
                <p class="text-xs text-gray-500 mt-2">Current account status</p>
            </div>
        </div>
    </div>
</main>

@push('scripts')
<script>
(function() {
    var base = window.salonJsonBase || '{{ url("json") }}';
    var userData = null;
    var allUsers = [];

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

    function getAvatarColor(user) {
        var colors = [
            { bg: 'bg-[#e6f0f3]', text: 'text-[#003047]' },
            { bg: 'bg-purple-100', text: 'text-purple-600' },
            { bg: 'bg-teal-100', text: 'text-teal-600' },
            { bg: 'bg-indigo-100', text: 'text-indigo-600' }
        ];
        var index = (user.id - 1) % colors.length;
        return colors[index];
    }

    function formatRole(role) {
        if (!role) return '';
        var r = role.toLowerCase();
        if (r === 'admin') return 'Admin';
        if (r === 'receptionist') return 'Receptionist';
        if (r === 'technician') return 'Technician';
        return role.charAt(0).toUpperCase() + role.slice(1);
    }

    function formatStatus(status) {
        if (!status) return 'Active';
        var s = status.toLowerCase();
        if (s === 'active') return 'Active';
        if (s === 'inactive') return 'Inactive';
        return status.charAt(0).toUpperCase() + status.slice(1);
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
        if (monthDiff < 0 || (monthDiff === 0 && now.getDate() < date.getDate())) {
            years--;
        }
        return years > 0 ? years + ' year' + (years !== 1 ? 's' : '') + ' ago' : 'Less than a year ago';
    }

    async function loadUserData() {
        try {
            var userId = getUserIdFromURL();
            var response = await fetch(base + '/users.json');
            var data = await response.json();
            allUsers = data.users || [];
            userData = allUsers.find(function(u) { return u.id === userId; });
            
            if (!userData) {
                userData = allUsers[0] || null;
            }
            
            if (userData) {
                renderUserInfo();
            } else {
                showErrorMessage('User not found');
            }
        } catch (err) {
            console.error('Error loading user data:', err);
            showErrorMessage('Failed to load user data');
        }
    }

    function renderUserInfo() {
        if (!userData) return;
        
        var initials = getInitials(userData);
        var color = getAvatarColor(userData);
        var fullName = (userData.firstName || '') + ' ' + (userData.lastName || '');
        var role = formatRole(userData.role);
        var status = formatStatus(userData.status);
        var joinDate = formatDate(userData.createdAt || userData.join_date);
        var lastLogin = formatDate(userData.lastLogin || userData.last_login);
        
        // Avatar
        var avatarEl = document.getElementById('userAvatar');
        var initialsEl = document.getElementById('userInitials');
        if (avatarEl && initialsEl) {
            avatarEl.className = 'w-24 h-24 ' + color.bg + ' rounded-full flex items-center justify-center flex-shrink-0';
            initialsEl.className = 'text-4xl font-bold ' + color.text;
            initialsEl.textContent = initials;
        }
        
        // Name
        var nameEl = document.getElementById('userName');
        if (nameEl) nameEl.textContent = fullName;
        
        // Role
        var roleEl = document.getElementById('userRole');
        if (roleEl) {
            roleEl.textContent = role;
            var roleColorClass = userData.role === 'admin' ? 'text-[#003047]' : 
                                 (userData.role === 'receptionist' ? 'text-blue-600' : 'text-purple-600');
            roleEl.className = 'text-lg ' + roleColorClass + ' mb-2 font-medium';
        }
        
        // Status
        var statusEl = document.getElementById('userStatus');
        if (statusEl) {
            var statusBgClass = status === 'Active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700';
            statusEl.className = 'px-3 py-1 ' + statusBgClass + ' text-xs font-medium rounded mb-4 inline-block';
            statusEl.textContent = status;
        }
        
        // Email
        var emailEl = document.getElementById('userEmail');
        if (emailEl) emailEl.textContent = userData.email || 'N/A';
        
        // Phone
        var phoneEl = document.getElementById('userPhone');
        if (phoneEl) phoneEl.textContent = userData.phone || 'N/A';
        
        // Join Date
        var joinDateEl = document.getElementById('userJoinDate');
        if (joinDateEl) joinDateEl.textContent = joinDate;
        
        // Last Login
        var lastLoginEl = document.getElementById('userLastLogin');
        if (lastLoginEl) lastLoginEl.textContent = lastLogin;
        
        // Statistics
        var totalLoginsEl = document.getElementById('totalLogins');
        if (totalLoginsEl) {
            var totalLogins = userData.totalLogins || Math.floor(Math.random() * 2000) + 500;
            totalLoginsEl.textContent = totalLogins.toLocaleString();
        }
        
        var lastLoginTextEl = document.getElementById('lastLoginText');
        if (lastLoginTextEl) {
            lastLoginTextEl.textContent = 'Last login: ' + lastLogin;
        }
        
        var dateRegisteredEl = document.getElementById('dateRegistered');
        if (dateRegisteredEl) dateRegisteredEl.textContent = joinDate;
        
        var yearsAgoEl = document.getElementById('yearsAgo');
        if (yearsAgoEl) {
            yearsAgoEl.textContent = calculateYearsAgo(userData.createdAt || userData.join_date);
        }
        
        var accountStatusEl = document.getElementById('accountStatus');
        if (accountStatusEl) {
            accountStatusEl.textContent = status;
            accountStatusEl.className = 'text-3xl font-bold ' + (status === 'Active' ? 'text-green-600' : 'text-gray-600');
        }
    }

    window.openEditUserModal = function() {
        if (!userData) return;
        
        var roleColorClass = userData.role === 'admin' ? 'text-[#003047]' : 
                           (userData.role === 'receptionist' ? 'text-blue-600' : 'text-purple-600');
        
        var modalContent = `
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xl font-bold text-gray-900">Edit Staff</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <form onsubmit="updateUser(event)" class="space-y-4">
                    <input type="hidden" name="user_id" value="${userData.id}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                            <input type="text" name="first_name" id="editFirstName" value="${userData.firstName || ''}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                            <input type="text" name="last_name" id="editLastName" value="${userData.lastName || ''}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" name="email" id="editEmail" value="${userData.email || ''}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                            <input type="tel" name="phone" id="editPhone" value="${userData.phone || ''}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                        <select name="role" id="editRole" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                            <option value="admin" ${userData.role === 'admin' ? 'selected' : ''}>Admin</option>
                            <option value="receptionist" ${userData.role === 'receptionist' ? 'selected' : ''}>Receptionist</option>
                            <option value="technician" ${userData.role === 'technician' ? 'selected' : ''}>Technician</option>
                        </select>
                    </div>
                    <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                        <div>
                            <label class="text-sm font-medium text-gray-900">Active</label>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="active" id="editActive" class="sr-only peer" ${(userData.status || '').toLowerCase() === 'active' ? 'checked' : ''}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div>
                        </label>
                    </div>
                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" onclick="closeModal()" class="px-6 py-3 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition font-medium active:scale-95">
                            Cancel
                        </button>
                        <button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">
                            Update Staff
                        </button>
                    </div>
                </form>
            </div>
        `;
        openModal(modalContent);
    };

    window.updateUser = function(event) {
        event.preventDefault();
        
        var formData = new FormData(event.target);
        var firstName = formData.get('first_name');
        var lastName = formData.get('last_name');
        var email = formData.get('email');
        var phone = formData.get('phone');
        var role = formData.get('role');
        var active = formData.get('active');
        
        // Update userData object
        userData.firstName = firstName;
        userData.lastName = lastName;
        userData.email = email;
        userData.phone = phone;
        userData.role = role;
        userData.status = active ? 'active' : 'inactive';
        
        // Re-render user info
        renderUserInfo();
        
        // Show success message
        showSuccessMessage('Staff updated successfully!');
        
        // Close modal
        closeModal();
        
        // In a real application, you would send this data to the server
        console.log('Updating user:', { firstName, lastName, email, phone, role, active });
    };

    function showSuccessMessage(message) {
        var successDiv = document.createElement('div');
        successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
        successDiv.textContent = message;
        document.body.appendChild(successDiv);
        setTimeout(function() { successDiv.remove(); }, 3000);
    }

    function showErrorMessage(message) {
        var errorDiv = document.createElement('div');
        errorDiv.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
        errorDiv.textContent = message;
        document.body.appendChild(errorDiv);
        setTimeout(function() { errorDiv.remove(); }, 3000);
    }

    document.addEventListener('DOMContentLoaded', function() {
        loadUserData();
    });
})();
</script>
@endpush
@endsection

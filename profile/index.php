<?php
// Mock user profile data - In a real application, this would come from a database
$user = [
    'id' => 1,
    'first_name' => 'Admin',
    'last_name' => 'User',
    'email' => 'admin@salon.com',
    'phone' => '(555) 100-0000',
    'role' => 'Admin',
    'join_date' => date('Y-m-d', strtotime('-2 years')),
    'avatar_color' => 'pink',
    'initials' => 'AU'
];

$pageTitle = 'My Profile';
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/modal.php';
?>

<!-- Main Content Area -->
<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">My Profile</h1>
            <p class="text-gray-600 text-sm sm:text-base">Manage your profile information and settings</p>
        </div>

        <!-- Profile Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-start gap-6 flex-1">
                    <?php
                    $avatarBgClass = $user['avatar_color'] === 'pink' ? 'bg-[#e6f0f3]' : 
                                     ($user['avatar_color'] === 'purple' ? 'bg-purple-100' : 
                                     ($user['avatar_color'] === 'teal' ? 'bg-teal-100' : 'bg-indigo-100'));
                    $avatarTextClass = $user['avatar_color'] === 'pink' ? 'text-[#003047]' : 
                                       ($user['avatar_color'] === 'purple' ? 'text-purple-600' : 
                                       ($user['avatar_color'] === 'teal' ? 'text-teal-600' : 'text-indigo-600'));
                    ?>
                    <div class="w-24 h-24 <?php echo $avatarBgClass; ?> rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-4xl font-bold <?php echo $avatarTextClass; ?>"><?php echo htmlspecialchars($user['initials']); ?></span>
                    </div>
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-gray-900 mb-2"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h1>
                        <p class="text-lg text-gray-600 mb-2"><?php echo htmlspecialchars($user['role']); ?></p>
                        <span class="px-3 py-1 bg-[#e6f0f3] text-[#003047] text-xs font-medium rounded mb-4 inline-block">Active</span>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Email</p>
                                <p class="text-base font-medium text-gray-900" id="userEmail"><?php echo htmlspecialchars($user['email']); ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Phone</p>
                                <p class="text-base font-medium text-gray-900" id="userPhone"><?php echo htmlspecialchars($user['phone']); ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Member Since</p>
                                <p class="text-base font-medium text-gray-900"><?php echo date('M d, Y', strtotime($user['join_date'])); ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Account Status</p>
                                <p class="text-base font-medium text-green-600">Active</p>
                            </div>
                        </div>
                    </div>
                </div>
                <button onclick="openEditProfileModal()" class="px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95 text-sm">
                    Edit Profile
                </button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <p class="text-sm text-gray-500 mb-2">Total Logins</p>
                <p class="text-3xl font-bold text-gray-900">1,245</p>
                <p class="text-xs text-gray-500 mt-2">Last login: <?php echo date('M d, Y'); ?></p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <p class="text-sm text-gray-500 mb-2">Date Registered</p>
                <p class="text-3xl font-bold text-gray-900"><?php echo date('M d, Y', strtotime($user['join_date'])); ?></p>
                <p class="text-xs text-gray-500 mt-2"><?php echo date('Y') - date('Y', strtotime($user['join_date'])); ?> years ago</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <p class="text-sm text-gray-500 mb-2">Permissions</p>
                <p class="text-3xl font-bold text-gray-900">Full Access</p>
                <p class="text-xs text-gray-500 mt-2">Admin privileges</p>
            </div>
        </div>

        <!-- Security Settings -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Security Settings</h2>
            <div class="space-y-4">
                <div class="flex items-center justify-between py-3">
                    <div>
                        <p class="text-sm font-medium text-gray-900">Change Password</p>
                        <p class="text-xs text-gray-500">Update your password to keep your account secure</p>
                    </div>
                    <button onclick="openChangePasswordModal()" class="px-4 py-2 text-[#003047] hover:bg-[#e6f0f3] rounded-lg border border-[#b3d1d9] transition text-sm font-medium">
                        Change Password
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
const userData = <?php echo json_encode($user); ?>;

function openEditProfileModal() {
    const modalContent = `
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">Edit Profile</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form onsubmit="updateProfile(event)" class="space-y-4">
                <input type="hidden" name="user_id" value="${userData.id}">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                        <input type="text" name="first_name" id="editFirstName" value="${userData.first_name}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                        <input type="text" name="last_name" id="editLastName" value="${userData.last_name}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" id="editEmail" value="${userData.email}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" name="phone" id="editPhone" value="${userData.phone}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                    </div>
                </div>
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <label class="text-sm font-medium text-gray-900">Active</label>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="active" class="sr-only peer" checked>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div>
                    </label>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">
                        Update Profile
                    </button>
                </div>
            </form>
        </div>
    `;
    openModal(modalContent);
}

function updateProfile(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const firstName = formData.get('first_name');
    const lastName = formData.get('last_name');
    const email = formData.get('email');
    const phone = formData.get('phone');
    
    // Update displayed information
    document.getElementById('userEmail').textContent = email;
    document.getElementById('userPhone').textContent = phone;
    document.querySelector('h1').textContent = firstName + ' ' + lastName;
    
    // Show success message
    showSuccessMessage('Profile updated successfully!');
    
    // Close modal
    closeModal();
    
    // In a real application, you would send this data to the server
    console.log('Updating profile:', { firstName, lastName, email, phone });
}

function openChangePasswordModal() {
    const modalContent = `
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">Change Password</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form onsubmit="changePassword(event)" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                    <input type="password" name="current_password" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Enter current password">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                    <input type="password" name="new_password" id="newPassword" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Enter new password">
                    <p class="mt-1 text-xs text-gray-500">Password must be at least 8 characters long</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                    <input type="password" name="confirm_password" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent" placeholder="Confirm new password">
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">
                        Change Password
                    </button>
                </div>
            </form>
        </div>
    `;
    openModal(modalContent);
}

function changePassword(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const currentPassword = formData.get('current_password');
    const newPassword = formData.get('new_password');
    const confirmPassword = formData.get('confirm_password');
    
    // Validate passwords match
    if (newPassword !== confirmPassword) {
        alert('New passwords do not match!');
        return;
    }
    
    // Validate password length
    if (newPassword.length < 8) {
        alert('Password must be at least 8 characters long!');
        return;
    }
    
    // Show success message
    showSuccessMessage('Password changed successfully!');
    
    // Close modal
    closeModal();
    
    // In a real application, you would send this data to the server
    console.log('Changing password');
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


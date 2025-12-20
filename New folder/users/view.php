<?php
// Get user ID from URL parameter
$userId = isset($_GET['id']) ? intval($_GET['id']) : 1;

// Mock user data - In a real application, this would come from a database
$users = [
    1 => [
        'id' => 1,
        'first_name' => 'Admin',
        'last_name' => 'User',
        'email' => 'admin@salon.com',
        'phone' => '(555) 100-0000',
        'role' => 'Admin',
        'status' => 'Active',
        'join_date' => date('Y-m-d', strtotime('-2 years')),
        'last_login' => date('Y-m-d'),
        'avatar_color' => 'pink',
        'initials' => 'AU'
    ],
    2 => [
        'id' => 2,
        'first_name' => 'John',
        'last_name' => 'Smith',
        'email' => 'john.s@salon.com',
        'phone' => '(555) 200-0000',
        'role' => 'Receptionist',
        'status' => 'Active',
        'join_date' => date('Y-m-d', strtotime('-1 year')),
        'last_login' => date('Y-m-d', strtotime('-2 days')),
        'avatar_color' => 'purple',
        'initials' => 'JS'
    ],
    3 => [
        'id' => 3,
        'first_name' => 'Emily',
        'last_name' => 'Wilson',
        'email' => 'emily.w@salon.com',
        'phone' => '(555) 300-0000',
        'role' => 'Receptionist',
        'status' => 'Active',
        'join_date' => date('Y-m-d', strtotime('-6 months')),
        'last_login' => date('Y-m-d', strtotime('-1 week')),
        'avatar_color' => 'teal',
        'initials' => 'EW'
    ],
    4 => [
        'id' => 4,
        'first_name' => 'Robert',
        'last_name' => 'Brown',
        'email' => 'robert.b@salon.com',
        'phone' => '(555) 400-0000',
        'role' => 'Technician',
        'status' => 'Inactive',
        'join_date' => date('Y-m-d', strtotime('-3 months')),
        'last_login' => date('Y-m-d', strtotime('-1 month')),
        'avatar_color' => 'indigo',
        'initials' => 'RB'
    ]
];

// Get user data
$user = isset($users[$userId]) ? $users[$userId] : $users[1];

$pageTitle = $user['first_name'] . ' ' . $user['last_name'];
include '../includes/header.php';
include '../includes/sidebar.php';
include '../includes/modal.php';
?>

<!-- Main Content Area -->
<main class="flex-1 overflow-y-auto bg-gray-50 lg:ml-0 pt-16 lg:pt-0">
    <div class="p-4 sm:p-6 lg:p-8">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="index.php" class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-900 transition">
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
                    <?php
                    $avatarBgClass = $user['avatar_color'] === 'pink' ? 'bg-[#e6f0f3]' : 
                                     ($user['avatar_color'] === 'purple' ? 'bg-purple-100' : 
                                     ($user['avatar_color'] === 'teal' ? 'bg-teal-100' : 'bg-indigo-100'));
                    $avatarTextClass = $user['avatar_color'] === 'pink' ? 'text-[#003047]' : 
                                       ($user['avatar_color'] === 'purple' ? 'text-purple-600' : 
                                       ($user['avatar_color'] === 'teal' ? 'text-teal-600' : 'text-indigo-600'));
                    $statusBgClass = $user['status'] === 'Active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700';
                    $roleColorClass = $user['role'] === 'Admin' ? 'text-[#003047]' : 
                                      ($user['role'] === 'Receptionist' ? 'text-blue-600' : 'text-purple-600');
                    ?>
                    <div class="w-24 h-24 <?php echo $avatarBgClass; ?> rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-4xl font-bold <?php echo $avatarTextClass; ?>"><?php echo htmlspecialchars($user['initials']); ?></span>
                    </div>
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-gray-900 mb-2"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h1>
                        <p class="text-lg text-gray-600 mb-2 <?php echo $roleColorClass; ?> font-medium"><?php echo htmlspecialchars($user['role']); ?></p>
                        <span class="px-3 py-1 <?php echo $statusBgClass; ?> text-xs font-medium rounded mb-4 inline-block"><?php echo htmlspecialchars($user['status']); ?></span>
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
                                <p class="text-sm text-gray-500 mb-1">Date Registered</p>
                                <p class="text-base font-medium text-gray-900"><?php echo date('M d, Y', strtotime($user['join_date'])); ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Last Login</p>
                                <p class="text-base font-medium text-gray-900"><?php echo date('M d, Y', strtotime($user['last_login'])); ?></p>
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
                <p class="text-3xl font-bold text-gray-900">1,245</p>
                <p class="text-xs text-gray-500 mt-2">Last login: <?php echo date('M d, Y', strtotime($user['last_login'])); ?></p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <p class="text-sm text-gray-500 mb-2">Date Registered</p>
                <p class="text-3xl font-bold text-gray-900"><?php echo date('M d, Y', strtotime($user['join_date'])); ?></p>
                <p class="text-xs text-gray-500 mt-2"><?php echo date('Y') - date('Y', strtotime($user['join_date'])); ?> years ago</p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <p class="text-sm text-gray-500 mb-2">Account Status</p>
                <p class="text-3xl font-bold <?php echo $user['status'] === 'Active' ? 'text-green-600' : 'text-gray-600'; ?>"><?php echo htmlspecialchars($user['status']); ?></p>
                <p class="text-xs text-gray-500 mt-2">Current account status</p>
            </div>
        </div>
    </div>
</main>

<script>
const userData = <?php echo json_encode($user); ?>;

function openEditUserModal() {
    const roleColorClass = userData.role === 'Admin' ? 'text-[#003047]' : 
                           (userData.role === 'Receptionist' ? 'text-blue-600' : 'text-purple-600');
    
    const modalContent = `
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
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                    <select name="role" id="editRole" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                        <option value="Admin" ${userData.role === 'Admin' ? 'selected' : ''}>Admin</option>
                        <option value="Receptionist" ${userData.role === 'Receptionist' ? 'selected' : ''}>Receptionist</option>
                        <option value="Technician" ${userData.role === 'Technician' ? 'selected' : ''}>Technician</option>
                    </select>
                </div>
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <label class="text-sm font-medium text-gray-900">Active</label>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="active" class="sr-only peer" ${userData.status === 'Active' ? 'checked' : ''}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div>
                    </label>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">
                        Update Staff
                    </button>
                </div>
            </form>
        </div>
    `;
    openModal(modalContent);
}

function updateUser(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const firstName = formData.get('first_name');
    const lastName = formData.get('last_name');
    const email = formData.get('email');
    const phone = formData.get('phone');
    const role = formData.get('role');
    const active = formData.get('active');
    
    // Update displayed information
    document.getElementById('userEmail').textContent = email;
    document.getElementById('userPhone').textContent = phone;
    document.querySelector('h1').textContent = firstName + ' ' + lastName;
    
    // Update role display
    const roleDisplay = document.querySelector('.text-lg.text-gray-600');
    if (roleDisplay) {
        roleDisplay.textContent = role;
    }
    
    // Show success message
    showSuccessMessage('Staff updated successfully!');
    
    // Close modal
    closeModal();
    
    // In a real application, you would send this data to the server
    console.log('Updating user:', { firstName, lastName, email, phone, role, active });
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



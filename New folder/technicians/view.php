<?php
// Get technician ID from URL parameter
$technicianId = isset($_GET['id']) ? intval($_GET['id']) : 1;

// Mock technician data - In a real application, this would come from a database
$technicians = [
    1 => [
        'id' => 1,
        'first_name' => 'Maria',
        'last_name' => 'Garcia',
        'email' => 'maria.g@salon.com',
        'phone' => '(555) 111-2222',
        'title' => 'Senior Technician',
        'status' => 'Available',
        'status_color' => 'green',
        'avatar_color' => 'pink',
        'initials' => 'MG',
        'today_services' => 8,
        'current_queue' => 2,
        'total_services' => 245,
        'total_earnings' => 12250.00,
        'customers' => 45,
        'total' => 280.00,
        'tip' => 45.00,
        'commission' => 84.00,
        'clock_in' => 'Nov. 23, 2025 8:00 AM',
        'clock_out' => '--',
    ],
    2 => [
        'id' => 2,
        'first_name' => 'Lisa',
        'last_name' => 'Wong',
        'email' => 'lisa.w@salon.com',
        'phone' => '(555) 222-3333',
        'title' => 'Technician',
        'status' => 'Busy',
        'status_color' => 'yellow',
        'avatar_color' => 'purple',
        'initials' => 'LW',
        'today_services' => 6,
        'current_queue' => 1,
        'total_services' => 180,
        'total_earnings' => 9000.00,
        'customers' => 38,
        'total' => 210.00,
        'tip' => 35.00,
        'commission' => 63.00,
        'clock_in' => 'Nov. 22, 2025 7:30 AM',
        'clock_out' => '--',
    ],
    3 => [
        'id' => 3,
        'first_name' => 'Anna',
        'last_name' => 'Kim',
        'email' => 'anna.k@salon.com',
        'phone' => '(555) 333-4444',
        'title' => 'Technician',
        'status' => 'Available',
        'status_color' => 'green',
        'avatar_color' => 'teal',
        'initials' => 'AK',
        'today_services' => 5,
        'current_queue' => 0,
        'total_services' => 165,
        'total_earnings' => 8250.00,
        'customers' => 32,
        'total' => 175.00,
        'tip' => 28.00,
        'commission' => 52.50,
        'clock_in' => 'Nov. 24, 2025 9:15 AM',
        'clock_out' => '--',
    ],
    4 => [
        'id' => 4,
        'first_name' => 'Sarah',
        'last_name' => 'Lee',
        'email' => 'sarah.l@salon.com',
        'phone' => '(555) 444-5555',
        'title' => 'Junior Technician',
        'status' => 'Available',
        'status_color' => 'green',
        'avatar_color' => 'indigo',
        'initials' => 'SL',
        'today_services' => 4,
        'current_queue' => 1,
        'total_services' => 95,
        'total_earnings' => 4750.00,
        'customers' => 28,
        'total' => 140.00,
        'tip' => 22.00,
        'commission' => 42.00,
        'clock_in' => 'Nov. 21, 2025 8:45 AM',
        'clock_out' => '--',
    ]
];

// Get technician data
$technician = isset($technicians[$technicianId]) ? $technicians[$technicianId] : $technicians[1];

$pageTitle = $technician['first_name'] . ' ' . $technician['last_name'];
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
                <span class="text-sm font-medium">Back to Technicians</span>
            </a>
        </div>

        <!-- Technician Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-start gap-6 flex-1">
                    <?php
                    $avatarBgClass = $technician['avatar_color'] === 'pink' ? 'bg-[#e6f0f3]' : 
                                     ($technician['avatar_color'] === 'purple' ? 'bg-purple-100' : 
                                     ($technician['avatar_color'] === 'teal' ? 'bg-teal-100' : 'bg-indigo-100'));
                    $avatarTextClass = $technician['avatar_color'] === 'pink' ? 'text-[#003047]' : 
                                       ($technician['avatar_color'] === 'purple' ? 'text-purple-600' : 
                                       ($technician['avatar_color'] === 'teal' ? 'text-teal-600' : 'text-indigo-600'));
                    $statusBgClass = $technician['status_color'] === 'green' ? 'bg-green-100 text-green-700' : 
                                     ($technician['status_color'] === 'yellow' ? 'bg-[#e6f0f3] text-[#003047]' : 'bg-gray-100 text-gray-700');
                    ?>
                    <div class="w-24 h-24 <?php echo $avatarBgClass; ?> rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-4xl font-bold <?php echo $avatarTextClass; ?>"><?php echo htmlspecialchars($technician['initials']); ?></span>
                    </div>
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-gray-900 mb-2"><?php echo htmlspecialchars($technician['first_name'] . ' ' . $technician['last_name']); ?></h1>
                        <p class="text-lg text-gray-600 mb-2"><?php echo htmlspecialchars($technician['title']); ?></p>
                        <span class="px-3 py-1 <?php echo $statusBgClass; ?> text-xs font-medium rounded mb-4 inline-block"><?php echo htmlspecialchars($technician['status']); ?></span>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Email</p>
                                <p class="text-base font-medium text-gray-900" id="technicianEmail"><?php echo htmlspecialchars($technician['email']); ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Phone</p>
                                <p class="text-base font-medium text-gray-900" id="technicianPhone"><?php echo htmlspecialchars($technician['phone']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <button onclick="openEditTechnicianModal()" class="px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95 text-sm">
                    Edit Technician
                </button>
            </div>
        </div>


        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <p class="text-sm text-gray-500 mb-2">Customers</p>
                <p class="text-3xl font-bold text-gray-900"><?php echo $technician['customers']; ?></p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <p class="text-sm text-gray-500 mb-2">Total</p>
                <p class="text-3xl font-bold text-gray-900">$<?php echo number_format($technician['total'], 2); ?></p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <p class="text-sm text-gray-500 mb-2">Tip</p>
                <p class="text-3xl font-bold text-gray-900">$<?php echo number_format($technician['tip'], 2); ?></p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <p class="text-sm text-gray-500 mb-2">Commission</p>
                <p class="text-3xl font-bold text-gray-900">$<?php echo number_format($technician['commission'], 2); ?></p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <p class="text-sm text-gray-500 mb-2">Clock</p>
                <div class="space-y-2">
                    <div>
                        <p class="text-xs text-gray-500">In:</p>
                        <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($technician['clock_in']); ?></p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500">Out:</p>
                        <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($technician['clock_out']); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
const technicianData = <?php echo json_encode($technician); ?>;

function openEditTechnicianModal() {
    const modalContent = `
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">Edit Technician</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form onsubmit="updateTechnician(event)" class="space-y-4">
                <input type="hidden" name="technician_id" value="${technicianData.id}">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                        <input type="text" name="first_name" id="editFirstName" value="${technicianData.first_name}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                        <input type="text" name="last_name" id="editLastName" value="${technicianData.last_name}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                    <input type="text" name="title" id="editTitle" value="${technicianData.title}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="email" id="editEmail" value="${technicianData.email}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                    <input type="tel" name="phone" id="editPhone" value="${technicianData.phone}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                </div>
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <label class="text-sm font-medium text-gray-900">Active</label>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="active" class="sr-only peer" ${technicianData.status === 'Available' || technicianData.status === 'Busy' ? 'checked' : ''}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-[#b3d1d9] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#003047]"></div>
                    </label>
                </div>
                <div class="flex justify-end gap-3 pt-4">
                    <button type="submit" class="px-6 py-3 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95">
                        Update Technician
                    </button>
                </div>
            </form>
        </div>
    `;
    openModal(modalContent);
}

function updateTechnician(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const firstName = formData.get('first_name');
    const lastName = formData.get('last_name');
    const email = formData.get('email');
    const phone = formData.get('phone');
    
    // Update displayed information
    document.getElementById('technicianEmail').textContent = email;
    document.getElementById('technicianPhone').textContent = phone;
    
    // Update page title
    document.querySelector('h1').textContent = firstName + ' ' + lastName;
    
    // Show success message
    showSuccessMessage('Technician details updated successfully!');
    
    // Close modal
    closeModal();
    
    // In a real application, you would send this data to the server
    // fetch('update.php', {
    //     method: 'POST',
    //     body: formData
    // }).then(response => response.json()).then(data => {
    //     showSuccessMessage('Technician details updated successfully!');
    //     closeModal();
    //     location.reload();
    // });
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


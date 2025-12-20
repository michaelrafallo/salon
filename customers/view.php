<?php
// Get customer ID from URL parameter
$customerId = isset($_GET['id']) ? intval($_GET['id']) : 1;

// Mock customer data - In a real application, this would come from a database
$customers = [
    1 => [
        'id' => 1,
        'first_name' => 'Sarah',
        'last_name' => 'Johnson',
        'email' => 'sarah.j@email.com',
        'phone' => '(555) 123-4567',
        'address' => '123 Main Street, City, State 12345',
        'total_visits' => 12,
        'last_visit' => date('Y-m-d', strtotime('-2 days')),
        'total_spent' => 660.00,
        'avatar_color' => 'pink',
        'initials' => 'SJ'
    ],
    2 => [
        'id' => 2,
        'first_name' => 'Emily',
        'last_name' => 'Chen',
        'email' => 'emily.c@email.com',
        'phone' => '(555) 234-5678',
        'address' => '456 Oak Avenue, City, State 12345',
        'total_visits' => 8,
        'last_visit' => date('Y-m-d', strtotime('-1 week')),
        'total_spent' => 440.00,
        'avatar_color' => 'purple',
        'initials' => 'EC'
    ],
    3 => [
        'id' => 3,
        'first_name' => 'Jessica',
        'last_name' => 'Martinez',
        'email' => 'jessica.m@email.com',
        'phone' => '(555) 345-6789',
        'address' => '789 Pine Road, City, State 12345',
        'total_visits' => 15,
        'last_visit' => date('Y-m-d'),
        'total_spent' => 825.00,
        'avatar_color' => 'teal',
        'initials' => 'JM'
    ],
    4 => [
        'id' => 4,
        'first_name' => 'Amanda',
        'last_name' => 'Taylor',
        'email' => 'amanda.t@email.com',
        'phone' => '(555) 456-7890',
        'address' => '321 Elm Street, City, State 12345',
        'total_visits' => 5,
        'last_visit' => date('Y-m-d', strtotime('-3 days')),
        'total_spent' => 275.00,
        'avatar_color' => 'indigo',
        'initials' => 'AT'
    ]
];

// Get customer data
$customer = isset($customers[$customerId]) ? $customers[$customerId] : $customers[1];

// Bookings mock data
$bookings = [
    [
        'booking_id' => 'BK-001',
        'date' => date('Y-m-d', strtotime('-2 days')),
        'date_display' => date('M d, Y', strtotime('-2 days')),
        'services' => [
            ['name' => 'Classic Manicure', 'price' => 35.00],
            ['name' => 'Gel Polish', 'price' => 20.00]
        ],
        'technicians' => ['Maria Garcia', 'Anna Kim'],
        'total_amount' => 55.00,
        'status' => 'Completed',
        'details' => 'Full nail care service with gel polish application'
    ],
    [
        'booking_id' => 'BK-002',
        'date' => date('Y-m-d', strtotime('-2 weeks')),
        'date_display' => date('M d, Y', strtotime('-2 weeks')),
        'services' => [
            ['name' => 'Spa Pedicure', 'price' => 55.00]
        ],
        'technicians' => ['Anna Kim'],
        'total_amount' => 55.00,
        'status' => 'Completed',
        'details' => 'Relaxing foot care with massage treatment'
    ],
    [
        'booking_id' => 'BK-003',
        'date' => date('Y-m-d', strtotime('-1 month')),
        'date_display' => date('M d, Y', strtotime('-1 month')),
        'services' => [
            ['name' => 'Gel Manicure', 'price' => 45.00]
        ],
        'technicians' => ['Lisa Wong'],
        'total_amount' => 45.00,
        'status' => 'Completed',
        'details' => 'Long-lasting gel polish application'
    ]
];

$pageTitle = $customer['first_name'] . ' ' . $customer['last_name'];
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
                <span class="text-sm font-medium">Back to Customers</span>
            </a>
        </div>

        <!-- Customer Header -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-start gap-6 flex-1">
                    <?php
                    $avatarBgClass = $customer['avatar_color'] === 'pink' ? 'bg-[#e6f0f3]' : 
                                     ($customer['avatar_color'] === 'purple' ? 'bg-purple-100' : 
                                     ($customer['avatar_color'] === 'teal' ? 'bg-teal-100' : 'bg-indigo-100'));
                    $avatarTextClass = $customer['avatar_color'] === 'pink' ? 'text-[#003047]' : 
                                       ($customer['avatar_color'] === 'purple' ? 'text-purple-600' : 
                                       ($customer['avatar_color'] === 'teal' ? 'text-teal-600' : 'text-indigo-600'));
                    ?>
                    <div class="w-24 h-24 <?php echo $avatarBgClass; ?> rounded-full flex items-center justify-center flex-shrink-0">
                        <span class="text-4xl font-bold <?php echo $avatarTextClass; ?>"><?php echo htmlspecialchars($customer['initials']); ?></span>
                    </div>
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-gray-900 mb-2"><?php echo htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name']); ?></h1>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Email</p>
                                <p class="text-base font-medium text-gray-900" id="customerEmail"><?php echo htmlspecialchars($customer['email']); ?></p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 mb-1">Phone</p>
                                <p class="text-base font-medium text-gray-900" id="customerPhone"><?php echo htmlspecialchars($customer['phone']); ?></p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-sm text-gray-500 mb-1">Address</p>
                                <p class="text-base font-medium text-gray-900" id="customerAddress"><?php echo htmlspecialchars($customer['address']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <button onclick="openEditCustomerModal()" class="px-4 py-2 bg-[#003047] text-white rounded-lg hover:bg-[#002535] transition font-medium active:scale-95 text-sm">
                    Edit Customer
                </button>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <p class="text-sm text-gray-500 mb-2">Total Visits</p>
                <p class="text-3xl font-bold text-gray-900"><?php echo $customer['total_visits']; ?></p>
                <p class="text-xs text-gray-500 mt-2">Last visit: <?php echo date('M d, Y', strtotime($customer['last_visit'])); ?></p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <p class="text-sm text-gray-500 mb-2">Total Spent</p>
                <p class="text-3xl font-bold text-gray-900">$<?php echo number_format($customer['total_spent'], 2); ?></p>
                <p class="text-xs text-gray-500 mt-2">Average per visit: $<?php echo number_format($customer['total_spent'] / max($customer['total_visits'], 1), 2); ?></p>
            </div>
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <p class="text-sm text-gray-500 mb-2">Customer Since</p>
                <p class="text-3xl font-bold text-gray-900"><?php echo date('M Y', strtotime('-6 months')); ?></p>
                <p class="text-xs text-gray-500 mt-2">Member for 6 months</p>
            </div>
        </div>

        <!-- Tickets -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Tickets</h2>
            <div class="space-y-4">
                <?php foreach ($bookings as $booking): ?>
                <div class="p-5 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <p class="font-semibold text-gray-900 text-lg">Ticket #<?php echo htmlspecialchars($booking['booking_id']); ?></p>
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-medium rounded"><?php echo htmlspecialchars($booking['status']); ?></span>
                            </div>
                            <p class="text-sm text-gray-600 mb-3"><?php echo htmlspecialchars($booking['details']); ?></p>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-2xl font-bold text-gray-900">$<?php echo number_format($booking['total_amount'], 2); ?></span>
                            <button onclick="printTicket('<?php echo htmlspecialchars($booking['booking_id']); ?>')" class="px-3 py-1.5 bg-[#003047] text-white text-xs font-medium rounded hover:bg-[#002535] transition active:scale-95 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                                Print
                            </button>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-3 border-t border-gray-200">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Date</p>
                            <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($booking['date_display']); ?></p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Technicians</p>
                            <p class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars(implode(', ', $booking['technicians'])); ?></p>
                        </div>
                        <div class="md:col-span-2">
                            <p class="text-xs text-gray-500 mb-2">Services</p>
                            <div class="space-y-1">
                                <?php foreach ($booking['services'] as $service): ?>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-900"><?php echo htmlspecialchars($service['name']); ?></span>
                                    <span class="text-sm font-medium text-gray-900">$<?php echo number_format($service['price'], 2); ?></span>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</main>

<script>
const customerData = <?php echo json_encode($customer); ?>;

function openEditCustomerModal() {
    const modalContent = `
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">Edit Customer</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <form onsubmit="updateCustomer(event)" class="space-y-4">
                <input type="hidden" name="customer_id" value="${customerData.id}">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                        <input type="text" name="first_name" id="editFirstName" value="${customerData.first_name}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                        <input type="text" name="last_name" id="editLastName" value="${customerData.last_name}" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="email" id="editEmail" value="${customerData.email}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="tel" name="phone" id="editPhone" value="${customerData.phone}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Address (optional)</label>
                    <input type="text" name="address" id="editAddress" value="${customerData.address}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#003047] focus:border-transparent">
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
                        Update Customer
                    </button>
                </div>
            </form>
        </div>
    `;
    openModal(modalContent);
}

function updateCustomer(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const firstName = formData.get('first_name');
    const lastName = formData.get('last_name');
    const email = formData.get('email');
    const phone = formData.get('phone');
    const address = formData.get('address');
    
    // Update displayed information
    document.getElementById('customerEmail').textContent = email;
    document.getElementById('customerPhone').textContent = phone;
    document.getElementById('customerAddress').textContent = address;
    
    // Update page title
    document.querySelector('h1').textContent = firstName + ' ' + lastName;
    
    // Show success message
    showSuccessMessage('Customer details updated successfully!');
    
    // Close modal
    closeModal();
    
    // In a real application, you would send this data to the server
    // fetch('update.php', {
    //     method: 'POST',
    //     body: formData
    // }).then(response => response.json()).then(data => {
    //     showSuccessMessage('Customer details updated successfully!');
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

function printTicket(bookingId) {
    window.print();
}
</script>

<?php include '../includes/footer.php'; ?>


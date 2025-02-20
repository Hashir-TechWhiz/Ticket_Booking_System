<?php
// admin/dashboard.php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

// Fetch admin details
$admin_id = $_SESSION['admin_id'];
$admin_query = "SELECT name FROM admins WHERE id = $admin_id";
$admin_result = $conn->query($admin_query);
if ($admin_result) {
    $admin = $admin_result->fetch_assoc();
    $admin_name = $admin['name'];
} else {
    die("Admin query failed: " . $conn->error);
}

// Fetch route buses for the specific admin
$sql_route_buses = "SELECT * FROM buses WHERE admin_id = $admin_id";
$route_buses_result = $conn->query($sql_route_buses);
if (!$route_buses_result) {
    die("Route buses query failed: " . $conn->error);
}

// Fetch trip buses for the specific admin
$sql_trip_buses = "SELECT * FROM trip_buses WHERE admin_id = $admin_id";
$trip_buses_result = $conn->query($sql_trip_buses);
if (!$trip_buses_result) {
    die("Trip buses query failed: " . $conn->error);
}

// Fetch booking data for the chart (only for the admin's buses)
$current_year = date("Y");

$sql_bookings = "SELECT MONTH(b.journey_date) as month, COUNT(*) as bookings FROM bookings b 
    INNER JOIN (SELECT id FROM buses WHERE admin_id = $admin_id 
    UNION ALL SELECT id FROM trip_buses WHERE admin_id = $admin_id) AS all_buses 
    ON b.bus_id = all_buses.id 
    WHERE YEAR(b.journey_date) = $current_year GROUP BY MONTH(b.journey_date)";
$bookings_result = $conn->query($sql_bookings);
if (!$bookings_result) {
    die("Bookings data query failed: " . $conn->error);
}

$booking_data = [];
while ($row = $bookings_result->fetch_assoc()) {
    $booking_data[$row['month']] = $row['bookings'];
}

// Fetch total bookings for route buses
$sql_route_bus_bookings = "SELECT COUNT(*) as total_route_bus_bookings FROM bookings b 
    INNER JOIN buses ON b.bus_id = buses.id 
    WHERE buses.admin_id = $admin_id";
$route_bus_bookings_result = $conn->query($sql_route_bus_bookings);
if (!$route_bus_bookings_result) {
    die("Route bus bookings query failed: " . $conn->error);
}
$route_bus_bookings = $route_bus_bookings_result->fetch_assoc()['total_route_bus_bookings'];

// Check if a month filter is set
$selected_month = isset($_GET['month']) ? $_GET['month'] : '';

// SQL query for fetching booking details with filter based on month
$sql_bookings_table = "SELECT 
        b.id AS booking_id, 
        u.name AS user_name, 
        u.phone AS user_contact, 
        u.email AS user_email, 
        bs.bus_name, 
        bse.time AS bus_time,
        b.seat_number, 
        b.journey_date, 
        b.payment_status
    FROM bookings b
    INNER JOIN users u ON b.user_id = u.id
    INNER JOIN (
        SELECT id, bus_name FROM buses WHERE admin_id = $admin_id
        UNION ALL
        SELECT id, bus_name FROM trip_buses WHERE admin_id = $admin_id
    ) AS bs ON b.bus_id = bs.id
    INNER JOIN buses bse ON b.bus_id = bse.id";

// Apply month filter if set
if ($selected_month) {
    $sql_bookings_table .= " WHERE MONTH(b.journey_date) = '$selected_month'";
}

$sql_bookings_table .= " ORDER BY b.journey_date ASC";

$result = $conn->query($sql_bookings_table);
if (!$result) {
    die("Bookings table query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const bookingData = <?php echo json_encode($booking_data); ?>;
    </script>
    <script src="../assets/js/chart.js"></script>


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>

<body class="bg-gray-100 min-h-screen bg-cover bg-center bg-no-repeat" style="background-image: url('../assets/images/AdminBg.jpg');">
    <div class="container mx-auto p-6">
        <!-- Header Section -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Welcome, <span class="text-[#4E71FF]"><?php echo htmlspecialchars($admin_name); ?>!</span></h1>
                <p class="text-gray-600 mt-2">Admin Dashboard</p>
            </div>
            <div class="flex space-x-4">
                <a href="logout.php" class="bg-red-600 hover:bg-red-500 text-white px-4 py-2 rounded-lg transition-colors">
                    <i class="fa fa-sign-out mr-2"></i>Logout
                </a>
            </div>
        </div>

        <!-- Navigation Menu -->
        <div class="bg-white rounded-lg shadow-sm p-4 mb-8 flex space-x-4">
            <a href="add_bus.php" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition-colors">
                <i class="fas fa-bus mr-2"></i>Add Route Bus
            </a>
            <a href="add_trip_bus.php" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md transition-colors">
                <i class="fas fa-car-side mr-2"></i>Add Trip Bus
            </a>
            <a href="trip_requests.php" class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-md transition-colors">
                <i class="fas fa-list-alt mr-2"></i>Trip Requests
            </a>
        </div>

        <!-- Stats and Chart Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Donut Chart -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Monthly Bookings (<?php echo $current_year; ?>)</h3>
                <canvas id="bookingsChart"></canvas>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white rounded-xl shadow-md p-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Quick Stats</h3>
                <div class="space-y-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <p class="text-gray-600">Total Route Buses</p>
                        <p class="text-2xl font-bold text-blue-600"><?php echo $route_buses_result->num_rows; ?></p>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <p class="text-gray-600">Total Route Bus Bookings</p>
                        <p class="text-2xl font-bold text-purple-600"><?php echo $route_bus_bookings; ?></p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <p class="text-gray-600">Total Trip Buses</p>
                        <p class="text-2xl font-bold text-green-600"><?php echo $trip_buses_result->num_rows; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Booking Details Table -->
        <div class="bg-white rounded-xl shadow-md mb-8 overflow-hidden">
            <div class="flex justify-between items-center w-full p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-[#4E71FF]">Booking Details</h3>

                <!-- Filter Dropdown (Automatically triggers page reload on change) -->
                <form method="GET" class="mb-4">
                    <label for="month" class="text-sm font-medium text-gray-700">Filter by Month:</label>
                    <select id="month" name="month" class="ml-2 p-2 border border-gray-300 rounded" onchange="this.form.submit()">
                        <option value="">All</option>
                        <option value="1" <?php echo $selected_month == '1' ? 'selected' : ''; ?>>January</option>
                        <option value="2" <?php echo $selected_month == '2' ? 'selected' : ''; ?>>February</option>
                        <option value="3" <?php echo $selected_month == '3' ? 'selected' : ''; ?>>March</option>
                        <option value="4" <?php echo $selected_month == '4' ? 'selected' : ''; ?>>April</option>
                        <option value="5" <?php echo $selected_month == '5' ? 'selected' : ''; ?>>May</option>
                        <option value="6" <?php echo $selected_month == '6' ? 'selected' : ''; ?>>June</option>
                        <option value="7" <?php echo $selected_month == '7' ? 'selected' : ''; ?>>July</option>
                        <option value="8" <?php echo $selected_month == '8' ? 'selected' : ''; ?>>August</option>
                        <option value="9" <?php echo $selected_month == '9' ? 'selected' : ''; ?>>September</option>
                        <option value="10" <?php echo $selected_month == '10' ? 'selected' : ''; ?>>October</option>
                        <option value="11" <?php echo $selected_month == '11' ? 'selected' : ''; ?>>November</option>
                        <option value="12" <?php echo $selected_month == '12' ? 'selected' : ''; ?>>December</option>
                    </select>
                </form>
            </div>

            <?php if ($result->num_rows > 0) { ?>
                <div class="overflow-x-auto max-h-96 overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0 z-10">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bus Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bus Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seat Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Journey Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php while ($row = $result->fetch_assoc()) { ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($row['user_name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($row['user_contact']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($row['user_email']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($row['bus_name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($row['bus_time']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($row['seat_number']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($row['journey_date']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 bg-green-600 text-white rounded-full text-sm"><?php echo htmlspecialchars($row['payment_status']); ?></span>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } else { ?>
                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200 text-center">
                    <p class="text-yellow-700">No bookings available.</p>
                </div>
            <?php } ?>
        </div>

        <!-- Route Buses Section -->
        <div class="bg-white rounded-xl shadow-md mb-8 overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-[#4E71FF]">Route Buses Management</h3>
            </div>

            <?php if ($route_buses_result->num_rows > 0) { ?>
                <div class="overflow-x-auto max-h-96 overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0 z-10">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bus Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Route</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seats</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php while ($bus = $route_buses_result->fetch_assoc()) { ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($bus['bus_name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($bus['bus_number']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($bus['contact_number']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="font-medium"><?php echo htmlspecialchars($bus['route_from']); ?></span>
                                        <span class="text-gray-400 mx-1">→</span>
                                        <span class="font-medium"><?php echo htmlspecialchars($bus['route_to']); ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($bus['time']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($bus['seats']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                                            <?php echo htmlspecialchars($bus['bus_type']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-green-600 font-semibold">Rs. <?php echo htmlspecialchars($bus['price']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap space-x-2">
                                        <a href="edit_bus.php?id=<?php echo $bus['id']; ?>"
                                            class="text-blue-500 hover:text-blue-700 transition-colors">
                                            <i class="fas fa-edit mr-1"></i>Edit
                                        </a>
                                        <a href="delete_bus.php?id=<?php echo $bus['id']; ?>"
                                            class="text-red-500 hover:text-red-700 transition-colors"
                                            onclick="return confirm('Are you sure? You want to delete this bus.')">
                                            <i class="fas fa-trash mr-1"></i>Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } else { ?>
                <div class="p-6">
                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200 text-center">
                        <p class="text-yellow-700">No Route buses added yet.</p>
                    </div>
                </div>
            <?php } ?>
        </div>


        <!-- Trip Buses Section -->
        <div class="bg-white rounded-xl shadow-md">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-xl font-semibold text-[#4E71FF]">Trip Buses Management</h3>
            </div>

            <?php if ($trip_buses_result->num_rows > 0) { ?>
                <div class="overflow-x-auto max-h-96 overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 sticky top-0 z-10">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bus Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Number</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Seats</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php while ($trip_bus = $trip_buses_result->fetch_assoc()) { ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($trip_bus['bus_name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($trip_bus['bus_number']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($trip_bus['contact_number']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap"><?php echo htmlspecialchars($trip_bus['seats']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                                            <?php echo htmlspecialchars($trip_bus['bus_type']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap space-x-2">
                                        <a href="edit_trip_bus.php?id=<?php echo $trip_bus['id']; ?>"
                                            class="text-blue-500 hover:text-blue-700 transition-colors">
                                            <i class="fas fa-edit mr-1"></i>Edit
                                        </a>
                                        <a href="delete_trip_bus.php?id=<?php echo $trip_bus['id']; ?>"
                                            class="text-red-500 hover:text-red-700 transition-colors"
                                            onclick="return confirm('Are you sure? You want to delete this trip bus.')">
                                            <i class="fas fa-trash mr-1"></i>Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } else { ?>
                <div class="p-6">
                    <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200 text-center">
                        <p class="text-yellow-700">No trip buses added yet.</p>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>

</body>

</html>
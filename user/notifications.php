<?php
// user/notifications.php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../user_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch trip requests for the logged-in user
$sql = "SELECT tr.id, tb.bus_name, tb.bus_number, tr.route_from, tr.route_to, tr.days, tr.status, tr.request_date 
        FROM trip_requests tr
        JOIN trip_buses tb ON tr.trip_bus_id = tb.id
        WHERE tr.user_id = $user_id
        ORDER BY tr.request_date DESC";

$result = $conn->query($sql);

// Check if the query was successful
if (!$result) {
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Notifications</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>

<body class="relative min-h-screen bg-cover bg-center bg-no-repeat px-[6%] py-5" style="background-image: url('../assets/images/Bg.jpg');">

    <div class="relative flex justify-center items-center bg-white rounded-xl p-2 shadow-xl w-full">
        <a href="dashboard.php" class="absolute left-3 flex items-center gap-2 text-blue-500"><img src="../assets/icons/Back.png" alt="Back" class="w-5 h-5"> Back</a>
        <h2 class="flex items-center justify-center text-2xl font-semibold text-[#4E71FF] w-full">Your Trip Request Notifications</h2>
    </div>

    <?php if ($result->num_rows > 0) { ?>
        <div class="overflow-x-auto mt-5 shadow-lg rounded-lg">
            <table class="w-full border-collapse border border-gray-300 bg-white">
                <thead class="bg-[#4E71FF] text-white">
                    <tr>
                        <th class="py-3 border border-gray-300">Bus Name</th>
                        <th class="py-3 border border-gray-300">Bus Number</th>
                        <th class="py-3 border border-gray-300">From</th>
                        <th class="py-3 border border-gray-300">To</th>
                        <th class="py-3 border border-gray-300">Days</th>
                        <th class="py-3 border border-gray-300">Status</th>
                        <th class="py-3 border border-gray-300">Request Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php while ($request = $result->fetch_assoc()) { ?>
                        <tr class="hover:bg-gray-100 text-center">
                            <td class="p-3 border border-gray-300"><?php echo htmlspecialchars($request['bus_name']); ?></td>
                            <td class="p-3 border border-gray-300"><?php echo htmlspecialchars($request['bus_number']); ?></td>
                            <td class="p-3 border border-gray-300"><?php echo htmlspecialchars($request['route_from']); ?></td>
                            <td class="p-3 border border-gray-300"><?php echo htmlspecialchars($request['route_to']); ?></td>
                            <td class="p-3 border border-gray-300"><?php echo htmlspecialchars($request['days']); ?></td>
                            <td class="p-3 border border-gray-300">
                                <?php
                                if ($request['status'] == 'Pending') {
                                    echo "<span class='px-3 py-1 text-sm font-semibold text-orange-600 bg-orange-100 rounded-full'>Pending</span>";
                                } elseif ($request['status'] == 'Approved') {
                                    echo "<span class='px-3 py-1 text-sm font-semibold text-green-600 bg-green-100 rounded-full'>Approved</span>";
                                } else {
                                    echo "<span class='px-3 py-1 text-sm font-semibold text-red-600 bg-red-100 rounded-full'>Rejected</span>";
                                }
                                ?>
                            </td>
                            <td class="py-3 px-4"><?php echo date('Y-m-d', strtotime($request['request_date'])); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } else { ?>
        <p class="text-gray-600 mt-4">No trip requests found.</p>
    <?php } ?>

</body>

</html>
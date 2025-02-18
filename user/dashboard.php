<?php
// user/dashboard.php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../user_login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$user_query = "SELECT name FROM users WHERE id = $user_id";
$user_result = $conn->query($user_query);
$user_name = "User";

if ($user_result->num_rows > 0) {
    $user_row = $user_result->fetch_assoc();
    $user_name = htmlspecialchars($user_row['name']);
}

// Fetch user bookings
$sql = "SELECT b.id, bu.bus_name, bu.bus_number, bu.route_from, bu.route_to, bu.time, b.seat_number, b.journey_date, bu.price, bu.bus_type 
        FROM bookings b
        JOIN buses bu ON b.bus_id = bu.id
        WHERE b.user_id = $user_id";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <script src="../assets/js/dateVal.js"></script>
</head>

<body class="relative min-h-screen bg-cover bg-center bg-no-repeat px-[6%] py-5" style="background-image: url('../assets/images/Bg.jpg');">


    <!-- Header -->
    <div class="flex justify-between items-center bg-white rounded-xl p-2 shadow-xl">
        <div class="flex items-center">
            <img src="../assets/images/Avatar.jpg" alt="Avatar" class="w-10 h-10">
            <h2 class="text-xl ml-2">
                Welcome, <span class="text-[#4E71FF] font-semibold"><?php echo $user_name; ?>!</span>
            </h2>
        </div>

        <div class="flex items-center gap-5 text-lg">
            <a href="trip_buses.php" class="flex gap-1 items-center"><img src="../assets/icons/Bus.png" alt="Bus" class="w-5 h-5">View Trip Buses</a> |
            <a href="notifications.php" class="flex gap-1 items-center"><img src="../assets/icons/Notifications.png" alt="Notifications" class="w-5 h-5">Notifications</a> |
            <a href="logout.php" class="flex gap-1 items-center"><img src="../assets/icons/Logout.png" alt="Logout" class="w-5 h-5">Logout</a>
        </div>
    </div>

    <!-- Bus Search Form -->
    <div class="mt-16 flex flex-col justify-center bg-white rounded-xl p-4 xl:max-w-[20%] mx-auto w-full shadow-xl">

        <h3 class="flex justify-center w-full text-xl font-semibold text-[#4E71FF]">Select Route & Date</h3>

        <form method="GET" action="select_route.php">
            <div class="flex flex-col mt-5">
                <label class="text-[#4E71FF] font-semibold">From:</label>
                <input type="text" name="from" required class="p-2 border border-blue-500 rounded-xl outline-none" placeholder="Enter departure city (e.g., Kandy)">
                <br>

                <label class="text-[#4E71FF] font-semibold">To:</label>
                <input type="text" name="to" required class="p-2 border border-blue-500 rounded-xl outline-none" placeholder="Enter destination city (e.g., Colombo)">
                <br>

                <label class="text-[#4E71FF] font-semibold">Date:</label>
                <input type="date" name="journey_date" required class="p-2 border border-blue-500 rounded-xl outline-none">
                <br>
                <input type="submit" value="Search Buses" class="w-full p-2 bg-blue-500 text-white rounded-xl cursor-pointer">
            </div>
        </form>

    </div>

    <?php if ($result->num_rows > 0) { ?>
        <h3 class="mt-5 text-xl font-semibold text-[#4E71FF]">Your Route Bus Bookings</h3>
        <div class="overflow-x-auto shadow-lg">
            <table class="w-full mt-3 border-collapse border border-gray-300 shadow-lg">
                <thead>
                    <tr class="bg-[#4E71FF] text-white">
                        <th class="p-3 border border-gray-300">Bus Name</th>
                        <th class="p-3 border border-gray-300">Bus Number</th>
                        <th class="p-3 border border-gray-300">From</th>
                        <th class="p-3 border border-gray-300">To</th>
                        <th class="p-3 border border-gray-300">Time</th>
                        <th class="p-3 border border-gray-300">Seat Number</th>
                        <th class="p-3 border border-gray-300">Journey Date</th>
                        <th class="p-3 border border-gray-300">Price</th>
                        <th class="p-3 border border-gray-300">Bus Type</th>
                        <th class="p-3 border border-gray-300">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <?php while ($booking = $result->fetch_assoc()) { ?>
                        <tr class="hover:bg-gray-100 text-center">
                            <td class="p-3 border border-gray-300"><?php echo $booking['bus_name']; ?></td>
                            <td class="p-3 border border-gray-300"><?php echo $booking['bus_number']; ?></td>
                            <td class="p-3 border border-gray-300"><?php echo $booking['route_from']; ?></td>
                            <td class="p-3 border border-gray-300"><?php echo $booking['route_to']; ?></td>
                            <td class="p-3 border border-gray-300"><?php echo $booking['time']; ?></td>
                            <td class="p-3 border border-gray-300"><?php echo $booking['seat_number']; ?></td>
                            <td class="p-3 border border-gray-300"><?php echo $booking['journey_date']; ?></td>
                            <td class="p-3 border border-gray-300">Rs. <?php echo $booking['price']; ?></td>
                            <td class="p-3 border border-gray-300"><?php echo $booking['bus_type']; ?></td>
                            <td class="p-3 border border-gray-300">
                                <a href="cancel_booking.php?id=<?php echo $booking['id']; ?>" onclick="return confirm('Are you sure you want to cancel this booking?')" class="text-red-500 hover:text-red-700 text-semibold">Cancel</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>

</body>

</html>
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
$user_name = "User"; // Default in case of an issue

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
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>

<body class="px-[6%] py-5">

    <!-- Header -->
    <div class="flex justify-between items-center bg-white rounded-xl p-2">
        <h2 class="md:text-2xl text-xs">Welcome, <?php echo $user_name; ?>!</h2>
        <div><a href="trip_buses.php">View Trip Buses</a> | <a href="notifications.php">Notifications</a> | <a href="logout.php">Logout</a></div>
    </div>

    <!-- Bus Search Form -->
    <div class="mt-5 flex flex-col justify-center bg-white rounded-xl p-2 md:max-w-[20%] mx-auto w-full">

        <h3 class="flex justify-center w-full">Select Route & Date</h3>

        <form method="GET" action="select_route.php" class="flex flex-col">
            <label>From</label>
            <input type="text" name="from" required class="p-2 border border-blue-500 rounded-xl outline-none"><br>

            <label>To</label>
            <input type="text" name="to" required class="p-2 border border-blue-500 rounded-xl outline-none"><br>

            <label>Date</label>
            <input type="date" name="journey_date" required class="p-2 border border-blue-500 rounded-xl outline-none"><br>
            <input type="submit" value="Search Buses" class="w-full p-2 bg-blue-500 text-white rounded-xl cursor-pointer">
        </form>

    </div>

    <?php if ($result->num_rows > 0) { ?>
        <h3 class="mt-5">Your Bookings</h3>
        <table border="1">
            <tr>
                <th>Bus Name</th>
                <th>Bus Number</th>
                <th>From</th>
                <th>To</th>
                <th>Time</th>
                <th>Seat Number</th>
                <th>Journey Date</th>
                <th>Price</th>
                <th>Bus Type</th>
                <th>Action</th>
            </tr>
            <?php while ($booking = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $booking['bus_name']; ?></td>
                    <td><?php echo $booking['bus_number']; ?></td>
                    <td><?php echo $booking['route_from']; ?></td>
                    <td><?php echo $booking['route_to']; ?></td>
                    <td><?php echo $booking['time']; ?></td>
                    <td><?php echo $booking['seat_number']; ?></td>
                    <td><?php echo $booking['journey_date']; ?></td>
                    <td>Rs. <?php echo $booking['price']; ?></td>
                    <td><?php echo $booking['bus_type']; ?></td>
                    <td>
                        <a href="cancel_booking.php?id=<?php echo $booking['id']; ?>" onclick="return confirm('Are you sure you want to cancel this booking?')">Cancel</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    <?php } ?>
</body>

</html>
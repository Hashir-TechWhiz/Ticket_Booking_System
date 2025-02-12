<?php
// user/ticket.php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['user_id']) || !isset($_GET['booking_id'])) {
    header("Location: dashboard.php");
    exit();
}

$booking_id = $_GET['booking_id'];

// Fetch all seats booked by the user for the same bus and journey date
$sql = "SELECT b.*, bus.bus_number, bus.route_from, bus.route_to, bus.time, bus.price
        FROM bookings b 
        JOIN buses bus ON b.bus_id = bus.id
        WHERE b.user_id = " . $_SESSION['user_id'] . " 
        AND b.journey_date = (SELECT journey_date FROM bookings WHERE id = $booking_id LIMIT 1)
        AND b.bus_id = (SELECT bus_id FROM bookings WHERE id = $booking_id LIMIT 1)";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $bookings = [];
    while ($row = $result->fetch_assoc()) {
        $bookings[] = $row;
    }
} else {
    die("Booking not found");
}

// Get bus details from the first record
$bus = $bookings[0];
$seat_numbers = array_column($bookings, 'seat_number');
$total_price = count($seat_numbers) * $bus['price'];

?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Your Ticket</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold text-center mb-4">Your Ticket</h2>

        <p class="text-lg"><strong>Bus Number:</strong> <?php echo $bus['bus_number']; ?></p>
        <p class="text-lg"><strong>Route:</strong> <?php echo $bus['route_from']; ?> to <?php echo $bus['route_to']; ?></p>
        <p class="text-lg"><strong>Departure Time:</strong> <?php echo $bus['time']; ?></p>
        <p class="text-lg"><strong>Seats:</strong> <?php echo implode(", ", $seat_numbers); ?></p>
        <p class="text-lg"><strong>Journey Date:</strong> <?php echo $bus['journey_date']; ?></p>
        <p class="text-lg font-bold"><strong>Total Price: $<?php echo $total_price; ?></strong></p>

        <a href="dashboard.php" class="block text-center bg-blue-500 text-white py-2 mt-4 rounded-md hover:bg-blue-700">
            Back to Dashboard
        </a>
    </div>
</body>

</html>
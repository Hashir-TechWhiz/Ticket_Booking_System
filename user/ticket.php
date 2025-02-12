<?php
// user/ticket.php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['user_id']) || !isset($_GET['booking_id'])) {
    header("Location: dashboard.php");
    exit();
}

$booking_id = $_GET['booking_id'];
$sql = "SELECT b.*, bus.bus_number, bus.route_from, bus.route_to, bus.time FROM bookings b 
        JOIN buses bus ON b.bus_id = bus.id
        WHERE b.id = $booking_id AND b.user_id = " . $_SESSION['user_id'];
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $booking = $result->fetch_assoc();
} else {
    die("Booking not found");
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Your Ticket</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <h2>Your Ticket</h2>
    <p><strong>Bus Number:</strong> <?php echo $booking['bus_number']; ?></p>
    <p><strong>Route:</strong> <?php echo $booking['route_from']; ?> to <?php echo $booking['route_to']; ?></p>
    <p><strong>Departure Time:</strong> <?php echo $booking['time']; ?></p>
    <p><strong>Seat Number:</strong> <?php echo $booking['seat_number']; ?></p>
    <p><strong>Journey Date:</strong> <?php echo $booking['journey_date']; ?></p>
    <p><strong>Payment Status:</strong> <?php echo $booking['payment_status']; ?></p>
    <a href="dashboard.php">Back to Dashboard</a>
</body>

</html>
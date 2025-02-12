<?php
// user/cancel_booking.php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$booking_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Delete the booking
$sql = "DELETE FROM bookings WHERE id = $booking_id AND user_id = $user_id";
if ($conn->query($sql) === TRUE) {
    header("Location: dashboard.php");
    exit();
} else {
    echo "Error: " . $conn->error;
}
?>

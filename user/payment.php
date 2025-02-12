<?php
// user/payment.php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['user_id']) || !isset($_SESSION['booking'])) {
    header("Location: dashboard.php");
    exit();
}

$booking = $_SESSION['booking'];

// Retrieve bus details for the summary
$sql = "SELECT * FROM buses WHERE id = " . $booking['bus_id'];
$result = $conn->query($sql);
$bus = $result->fetch_assoc();

if (isset($_POST['confirm_payment'])) {
    // Simulate payment processing and save the booking with payment status "Confirmed"
    $user_id    = $_SESSION['user_id'];
    $bus_id     = $booking['bus_id'];
    $seat_number = $booking['seat_number'];
    $journey_date = $booking['journey_date'];

    $sql = "INSERT INTO bookings (user_id, bus_id, seat_number, journey_date, payment_status)
            VALUES ($user_id, $bus_id, $seat_number, '$journey_date', 'Confirmed')";
    if ($conn->query($sql) === TRUE) {
        $booking_id = $conn->insert_id;
        unset($_SESSION['booking']);
        header("Location: ticket.php?booking_id=" . $booking_id);
        exit();
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Payment</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <h2>Payment Page</h2>
    <?php if (isset($error)) {
        echo "<p style='color:red;'>$error</p>";
    } ?>
    <p><strong>Bus Number:</strong> <?php echo $bus['bus_number']; ?></p>
    <p><strong>Seat Number:</strong> <?php echo $booking['seat_number']; ?></p>
    <p><strong>Journey Date:</strong> <?php echo $booking['journey_date']; ?></p>
    <form method="POST" action="">
        <input type="submit" name="confirm_payment" value="Confirm Payment">
    </form>
</body>

</html>
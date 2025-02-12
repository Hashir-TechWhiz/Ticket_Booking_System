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
$seat_price = $bus['price'];

// Convert selected seats string into an array
$selected_seats = explode(",", $booking['seat_number']);
$total_price = count($selected_seats) * $seat_price;

if (isset($_POST['confirm_payment'])) {
    $user_id = $_SESSION['user_id'];
    $bus_id = $booking['bus_id'];
    $journey_date = $booking['journey_date'];
    
    foreach ($selected_seats as $seat_number) {
        $sql = "INSERT INTO bookings (user_id, bus_id, seat_number, journey_date, payment_status)
                VALUES ($user_id, $bus_id, $seat_number, '$journey_date', 'Confirmed')";
        if (!$conn->query($sql)) {
            $error = "Error: " . $conn->error;
            break;
        }
    }

    if (!isset($error)) {
        unset($_SESSION['booking']);
        header("Location: ticket.php?booking_id=" . $conn->insert_id);
        exit();
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Payment</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-md">
        <h2 class="text-2xl font-bold text-center mb-4">Confirm Payment</h2>
        
        <?php if (isset($error)) {
            echo "<p class='text-red-500 text-center'>$error</p>";
        } ?>

        <p class="text-lg"><strong>Bus Number:</strong> <?php echo $bus['bus_number']; ?></p>
        <p class="text-lg"><strong>Seats:</strong> <?php echo implode(", ", $selected_seats); ?></p>
        <p class="text-lg"><strong>Journey Date:</strong> <?php echo $booking['journey_date']; ?></p>
        <p class="text-lg font-bold mt-4"><strong>Total Price: $<?php echo $total_price; ?></strong></p>

        <form method="POST" action="" class="mt-4">
            <button type="submit" name="confirm_payment" class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-700">
                Confirm Payment
            </button>
        </form>
    </div>
</body>

</html>

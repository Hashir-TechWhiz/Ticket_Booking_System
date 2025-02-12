<?php
// user/select_seat.php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../user_login.php");
    exit();
}

if (isset($_GET['bus_id']) && isset($_GET['journey_date'])) {
    $bus_id      = $_GET['bus_id'];
    $journey_date = $_GET['journey_date'];

    // Fetch bus details
    $sql = "SELECT * FROM buses WHERE id = $bus_id";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $bus = $result->fetch_assoc();
    } else {
        die("Bus not found");
    }
}

if (isset($_POST['select_seat'])) {
    $seat_number = $_POST['seat_number'];

    // Check if the seat is already booked for the selected bus and date
    $check_sql = "SELECT * FROM bookings WHERE bus_id = $bus_id AND seat_number = $seat_number AND journey_date = '$journey_date'";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        $error_message = "Sorry, this seat has already been booked. Please select a different seat.";
    } else {
        // Save booking details temporarily in session if the seat is available
        $_SESSION['booking'] = array(
            'bus_id'      => $bus_id,
            'seat_number' => $seat_number,
            'journey_date' => $journey_date
        );
        header("Location: payment.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Select Seat</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <h2>Select Seat for Bus <?php echo $bus['bus_number']; ?></h2>

    <?php if (isset($error_message)) {
        echo "<p style='color:red;'>$error_message</p>";
    } ?>

    <form method="POST" action="">
        <label>Seat Number (1 to <?php echo $bus['seats']; ?>):</label>
        <input type="number" name="seat_number" min="1" max="<?php echo $bus['seats']; ?>" required><br>
        <input type="submit" name="select_seat" value="Proceed to Payment">
    </form>
</body>

</html>
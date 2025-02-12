<?php
// user/trip_buses.php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../user_login.php");
    exit();
}

// Fetch available trip buses
$sql = "SELECT * FROM trip_buses";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Available Trip Buses</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="../assets/js/popup.js" defer></script>
</head>

<body>
    <h2>Available Trip Buses</h2>
    <a href="dashboard.php">Back to Dashboard</a>
    <table border="1">
        <tr>
            <th>Bus Name</th>
            <th>Bus Number</th>
            <th>Contact Number</th>
            <th>Seats</th>
            <th>Bus Type</th>
            <th>Action</th>
        </tr>
        <?php while ($bus = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($bus['bus_name']); ?></td>
                <td><?php echo htmlspecialchars($bus['bus_number']); ?></td>
                <td><?php echo htmlspecialchars($bus['contact_number']); ?></td>
                <td><?php echo htmlspecialchars($bus['seats']); ?></td>
                <td><?php echo htmlspecialchars($bus['bus_type']); ?></td>
                <td>
                    <button onclick="openPopup(<?php echo $bus['id']; ?>)">Request</button>
                </td>
            </tr>
        <?php } ?>
    </table>

    <!-- Popup Form for Trip Request -->
    <div id="popupForm" class="popup">
        <h3>Request a Trip</h3>
        <form method="POST" action="request_trip.php">
            <input type="hidden" name="trip_bus_id" id="tripBusId">
            <label>From:</label>
            <input type="text" name="from" required>
            <label>To:</label>
            <input type="text" name="to" required>
            <label>Date From:</label>
            <input type="date" name="date_from" id="dateFrom" required>
            <label>Date To:</label>
            <input type="date" name="date_to" id="dateTo" required>
            <label>Days:</label>
            <input type="number" name="days" id="days" min="1" required readonly>
            <button type="submit" name="request_trip">Send Request</button>
            <button type="button" class="close-btn" onclick="closePopup()">Cancel</button>
        </form>
    </div>

</body>

</html>
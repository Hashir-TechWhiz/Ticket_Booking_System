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
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Notifications</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <h2>Your Trip Request Notifications</h2>
    <a href="dashboard.php">Back to Dashboard</a>

    <?php if ($result->num_rows > 0) { ?>
        <table border="1">
            <tr>
                <th>Bus Name</th>
                <th>Bus Number</th>
                <th>From</th>
                <th>To</th>
                <th>Days</th>
                <th>Status</th>
                <th>Request Date</th>
            </tr>
            <?php while ($request = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($request['bus_name']); ?></td>
                    <td><?php echo htmlspecialchars($request['bus_number']); ?></td>
                    <td><?php echo htmlspecialchars($request['route_from']); ?></td>
                    <td><?php echo htmlspecialchars($request['route_to']); ?></td>
                    <td><?php echo htmlspecialchars($request['days']); ?></td>
                    <td>
                        <?php
                        if ($request['status'] == 'Pending') {
                            echo "<span style='color: orange;'>Pending</span>";
                        } elseif ($request['status'] == 'Approved') {
                            echo "<span style='color: green;'>Approved</span>";
                        } else {
                            echo "<span style='color: red;'>Rejected</span>";
                        }
                        ?>
                    </td>
                    <td><?php echo $request['request_date']; ?></td>
                </tr>
            <?php } ?>
        </table>
    <?php } else { ?>
        <p>No trip requests found.</p>
    <?php } ?>
</body>

</html>
<?php
session_start();
include("../includes/db.php");
include("send_email.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];
$sql = "SELECT tr.*, u.name AS user_name, u.email AS user_email, tb.bus_name, tb.bus_number 
        FROM trip_requests tr 
        JOIN users u ON tr.user_id = u.id 
        JOIN trip_buses tb ON tr.trip_bus_id = tb.id 
        WHERE tb.admin_id = $admin_id ORDER BY tr.request_date DESC";
$result = $conn->query($sql);

if (isset($_GET['action']) && isset($_GET['id'])) {
    $request_id = $_GET['id'];
    $action = $_GET['action'];

    if ($action == 'approve') {
        $status = 'Approved';
        $price = 5000;
        $contactNumber = "1234567890";
        $adminMessage = "";
    } elseif ($action == 'reject') {
        $status = 'Rejected';
        $adminMessage = "Bus is unavailable.";
        $price = null;
        $contactNumber = null;
    }

    $update_sql = "UPDATE trip_requests SET status = '$status' WHERE id = $request_id";
    if ($conn->query($update_sql) === TRUE) {
        // Fetch user email
        $user_query = "SELECT u.email, u.name FROM users u JOIN trip_requests tr ON u.id = tr.user_id WHERE tr.id = $request_id";
        $user_result = $conn->query($user_query);
        if ($user_result->num_rows > 0) {
            $user = $user_result->fetch_assoc();
            $toEmail = $user['email'];
            $toName = $user['name'];

            // Send email
            sendEmail($toEmail, $toName, $status, $adminMessage, $price, $contactNumber);
        }

        header("Location: trip_requests.php");
        exit();
    } else {
        $error = "Error updating request status: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Trip Requests</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <h2>Trip Requests</h2>
    <?php if (isset($error)) {
        echo "<p style='color:red;'>$error</p>";
    } ?>
    <?php if ($result->num_rows > 0) { ?>
        <table border="1">
            <tr>
                <th>User Name</th>
                <th>Bus Name</th>
                <th>Bus Number</th>
                <th>From</th>
                <th>To</th>
                <th>Date From</th>
                <th>Date To</th>
                <th>Days</th>
                <th>Status</th>
                <th>Request Date</th>
                <th>Actions</th>
            </tr>
            <?php while ($request = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $request['user_name']; ?></td>
                    <td><?php echo $request['bus_name']; ?></td>
                    <td><?php echo $request['bus_number']; ?></td>
                    <td><?php echo $request['route_from']; ?></td>
                    <td><?php echo $request['route_to']; ?></td>
                    <td><?php echo $request['date_from']; ?></td>
                    <td><?php echo $request['date_to']; ?></td>
                    <td><?php echo $request['days']; ?></td>
                    <td><?php echo $request['status']; ?></td>
                    <td><?php echo $request['request_date']; ?></td>
                    <td>
                        <?php if ($request['status'] == 'Pending') { ?>
                            <a href="?action=approve&id=<?php echo $request['id']; ?>">Approve</a> |
                            <a href="?action=reject&id=<?php echo $request['id']; ?>" onclick="return confirm('Are you sure you want to reject this request?')">Reject</a>
                        <?php } else { ?>
                            <span>-</span>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
    <?php } else {
        echo "<p>No trip requests found.</p>";
    } ?>
</body>

</html>
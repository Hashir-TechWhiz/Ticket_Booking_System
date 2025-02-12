<?php
// user/select_route.php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../user_login.php");
    exit();
}

$from         = $_GET['from'];
$to           = $_GET['to'];
$journey_date = $_GET['journey_date'];

// A simple search by matching the route details (adjust as needed)
$sql = "SELECT * FROM buses WHERE route_from = '$from' AND route_to = '$to'";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Available Buses</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <h2>Available Buses from <?php echo $from; ?> to <?php echo $to; ?></h2>
    <a href="dashboard.php">Back</a>
    <table border="1">
        <tr>
            <th>Bus Number</th>
            <th>Time</th>
            <th>Seats</th>
            <th>Action</th>
        </tr>
        <?php while ($bus = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $bus['bus_number']; ?></td>
                <td><?php echo $bus['time']; ?></td>
                <td><?php echo $bus['seats']; ?></td>
                <td>
                    <a href="select_seat.php?bus_id=<?php echo $bus['id']; ?>&journey_date=<?php echo $journey_date; ?>">Select Seat</a>
                </td>
            </tr>
        <?php } ?>
    </table>
</body>

</html>
<?php
// admin/dashboard.php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

// Fetch admin details
$admin_id = $_SESSION['admin_id'];
$admin_query = "SELECT name FROM admins WHERE id = $admin_id";
$admin_result = $conn->query($admin_query);
$admin = $admin_result->fetch_assoc();
$admin_name = $admin['name'];

// Fetch route buses
$sql_route_buses = "SELECT * FROM buses WHERE admin_id = $admin_id";
$route_buses_result = $conn->query($sql_route_buses);

// Fetch trip buses
$sql_trip_buses = "SELECT * FROM trip_buses WHERE admin_id = $admin_id";
$trip_buses_result = $conn->query($sql_trip_buses);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>

<body>
    <h2>Welcome, <?php echo htmlspecialchars($admin_name); ?>!</h2>
    <a href="add_bus.php">Add New Bus</a> | <a href="add_trip_bus.php">Add Trip Bus</a> | <a href="trip_requests.php">View Trip Requests</a> | <a href="logout.php">Logout</a>

    <!-- Route Buses Table -->
    <h3>Your Route Buses</h3>
    <table border="1">
        <tr>
            <th>Bus Name</th>
            <th>Bus Number</th>
            <th>Contact Number</th>
            <th>Seats</th>
            <th>Route From</th>
            <th>Route To</th>
            <th>Time</th>
            <th>Price</th>
            <th>Bus Type</th>
            <th>Actions</th>
        </tr>
        <?php while ($bus = $route_buses_result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($bus['bus_name']); ?></td>
                <td><?php echo htmlspecialchars($bus['bus_number']); ?></td>
                <td><?php echo htmlspecialchars($bus['contact_number']); ?></td>
                <td><?php echo htmlspecialchars($bus['seats']); ?></td>
                <td><?php echo htmlspecialchars($bus['route_from']); ?></td>
                <td><?php echo htmlspecialchars($bus['route_to']); ?></td>
                <td><?php echo htmlspecialchars($bus['time']); ?></td>
                <td><?php echo "Rs. " . htmlspecialchars($bus['price']); ?></td>
                <td><?php echo htmlspecialchars($bus['bus_type']); ?></td>
                <td>
                    <a href="edit_bus.php?id=<?php echo $bus['id']; ?>" class="btn btn-edit">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="delete_bus.php?id=<?php echo $bus['id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure?')">
                        <i class="fas fa-trash"></i> Delete
                    </a>
                </td>
            </tr>
        <?php } ?>
    </table>

    <!-- Trip Buses Table -->
    <h3>Your Trip Buses</h3>
    <?php if ($trip_buses_result->num_rows > 0) { ?>
        <table border="1">
            <tr>
                <th>Bus Name</th>
                <th>Bus Number</th>
                <th>Contact Number</th>
                <th>Seats</th>
                <th>Bus Type</th>
                <th>Actions</th>
            </tr>
            <?php while ($trip_bus = $trip_buses_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($trip_bus['bus_name']); ?></td>
                    <td><?php echo htmlspecialchars($trip_bus['bus_number']); ?></td>
                    <td><?php echo htmlspecialchars($trip_bus['contact_number']); ?></td>
                    <td><?php echo htmlspecialchars($trip_bus['seats']); ?></td>
                    <td><?php echo htmlspecialchars($trip_bus['bus_type']); ?></td>
                    <td>
                        <a href="edit_trip_bus.php?id=<?php echo $trip_bus['id']; ?>" class="btn btn-edit">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="delete_trip_bus.php?id=<?php echo $trip_bus['id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure?')">
                            <i class="fas fa-trash"></i> Delete
                        </a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    <?php } else { ?>
        <p>No trip buses added yet.</p>
    <?php } ?>
</body>

</html>
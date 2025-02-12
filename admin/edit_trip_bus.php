<?php
// admin/edit_trip_bus.php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

// Check if trip bus ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid request.");
}

$trip_bus_id = $_GET['id'];
$admin_id = $_SESSION['admin_id'];

// Fetch existing trip bus details
$sql = "SELECT * FROM trip_buses WHERE id = $trip_bus_id AND admin_id = $admin_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("Trip bus not found.");
}

$trip_bus = $result->fetch_assoc();

// Handle form submission for updating trip bus
if (isset($_POST['update_trip_bus'])) {
    $bus_name = $_POST['bus_name'];
    $bus_number = $_POST['bus_number'];
    $contact_number = $_POST['contact_number'];
    $seats = $_POST['seats'];
    $time = $_POST['time'];
    $bus_type = $_POST['bus_type'];

    $update_sql = "UPDATE trip_buses SET 
                   bus_name = '$bus_name', 
                   bus_number = '$bus_number', 
                   contact_number = '$contact_number', 
                   seats = $seats, 
                   bus_type = '$bus_type' 
                   WHERE id = $trip_bus_id AND admin_id = $admin_id";

    if ($conn->query($update_sql) === TRUE) {
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Error updating trip bus: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Edit Trip Bus</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <h2>Edit Trip Bus</h2>
    <form method="POST" action="">
        <label>Bus Name:</label>
        <input type="text" name="bus_name" value="<?php echo htmlspecialchars($trip_bus['bus_name']); ?>" required><br>

        <label>Bus Number:</label>
        <input type="text" name="bus_number" value="<?php echo htmlspecialchars($trip_bus['bus_number']); ?>" required><br>

        <label>Contact Number:</label>
        <input type="text" name="contact_number" value="<?php echo htmlspecialchars($trip_bus['contact_number']); ?>" required><br>

        <label>Seats:</label>
        <input type="number" name="seats" value="<?php echo htmlspecialchars($trip_bus['seats']); ?>" required><br>

        <label>Bus Type:</label>
        <select name="bus_type" required>
            <option value="AC" <?php echo ($trip_bus['bus_type'] == 'AC') ? 'selected' : ''; ?>>AC</option>
            <option value="Non-AC" <?php echo ($trip_bus['bus_type'] == 'Non-AC') ? 'selected' : ''; ?>>Non-AC</option>
        </select><br>

        <input type="submit" name="update_trip_bus" value="Update Trip Bus">
    </form>
</body>

</html>
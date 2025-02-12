<?php
// admin/add_bus.php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

if (isset($_POST['add_bus'])) {
    $bus_number     = $_POST['bus_number'];
    $bus_name       = $_POST['bus_name'];
    $contact_number = $_POST['contact_number'];
    $seats          = $_POST['seats'];
    $route_from     = $_POST['route_from'];
    $route_to       = $_POST['route_to'];
    $time           = $_POST['time'] . " " . $_POST['ampm'];
    $price          = $_POST['price'];
    $bus_type       = $_POST['bus_type'];
    $admin_id       = $_SESSION['admin_id'];

    $sql = "INSERT INTO buses (admin_id, bus_number, bus_name, contact_number, seats, route_from, route_to, time, price, bus_type) 
            VALUES ($admin_id, '$bus_number', '$bus_name', '$contact_number', $seats, '$route_from', '$route_to', '$time', $price, '$bus_type')";

    if ($conn->query($sql) === TRUE) {
        header("Location: dashboard.php");
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
    <title>Add Bus</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <h2>Add New Bus</h2>
    <?php if (isset($error)) {
        echo "<p style='color:red;'>$error</p>";
    } ?>
    <form method="POST" action="">
        <label>Bus Number:</label>
        <input type="text" name="bus_number" required><br>

        <label>Bus Name:</label>
        <input type="text" name="bus_name" required><br>

        <label>Contact Number:</label>
        <input type="text" name="contact_number" required><br>

        <label>Seats:</label>
        <input type="number" name="seats" required><br>

        <label>Route From:</label>
        <input type="text" name="route_from" required><br>

        <label>Route To:</label>
        <input type="text" name="route_to" required><br>

        <label>Time:</label>
        <input type="text" name="time" placeholder="e.g. 10:00" required>
        <select name="ampm" required>
            <option value="AM">AM</option>
            <option value="PM">PM</option>
        </select>
        <br>

        <label>Price:</label>
        <input type="number" name="price" step="0.01" required><br>

        <label>Bus Type:</label>
        <select name="bus_type" required>
            <option value="A/C">A/C</option>
            <option value="Non-A/C">Non-A/C</option>
        </select>
        <br>

        <input type="submit" name="add_bus" value="Add Bus">
    </form>
    <br>
    <a href="dashboard.php">Back to Dashboard</a>
</body>

</html>

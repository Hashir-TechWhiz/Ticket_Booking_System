<?php
// admin/add_trip_bus.php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

if (isset($_POST['add_trip_bus'])) {
    $bus_name = $_POST['bus_name'];
    $bus_number = $_POST['bus_number'];
    $contact_number = $_POST['contact_number'];
    $seats = $_POST['seats'];
    $bus_type = $_POST['bus_type'];

    $sql = "INSERT INTO trip_buses (admin_id, bus_name, bus_number, contact_number, seats, bus_type) 
            VALUES (" . $_SESSION['admin_id'] . ", '$bus_name', '$bus_number', '$contact_number', $seats, '$bus_type')";

    if ($conn->query($sql) === TRUE) {
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Error adding trip bus: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Add Trip Bus</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <h2>Add Trip Bus</h2>
    <?php if (isset($error)) {
        echo "<p style='color:red;'>$error</p>";
    } ?>
    <form method="POST" action="">
        <label>Bus Name:</label>
        <input type="text" name="bus_name" required><br>
        <label>Bus Number:</label>
        <input type="text" name="bus_number" required><br>
        <label>Contact Number:</label>
        <input type="text" name="contact_number" required><br>
        <label>Seats:</label>
        <input type="number" name="seats" required><br>
        <label>Bus Type:</label>
        <select name="bus_type" required>
            <option value="AC">AC</option>
            <option value="Non-AC">Non-AC</option>
        </select><br>
        <input type="submit" name="add_trip_bus" value="Add Trip Bus">
    </form>
    <br>
    <a href="dashboard.php">Back to Dashboard</a>
</body>

</html>
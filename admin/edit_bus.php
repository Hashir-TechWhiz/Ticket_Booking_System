<?php
// admin/edit_bus.php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

if (isset($_GET['id'])) {
    $bus_id = $_GET['id'];
    $sql = "SELECT * FROM buses WHERE id = $bus_id AND admin_id = " . $_SESSION['admin_id'];
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $bus = $result->fetch_assoc();
    } else {
        die("Bus not found");
    }
}

if (isset($_POST['update_bus'])) {
    $bus_number     = $_POST['bus_number'];
    $bus_name       = $_POST['bus_name'];
    $contact_number = $_POST['contact_number'];
    $seats          = $_POST['seats'];
    $route_from     = $_POST['route_from'];
    $route_to       = $_POST['route_to'];
    $time           = $_POST['time'] . " " . $_POST['ampm'];
    $price          = $_POST['price'];
    $bus_type       = $_POST['bus_type'];

    $sql = "UPDATE buses 
            SET bus_number='$bus_number', bus_name='$bus_name', contact_number='$contact_number', 
                seats=$seats, route_from='$route_from', route_to='$route_to', 
                time='$time', price='$price', bus_type='$bus_type'
            WHERE id = $bus_id AND admin_id = " . $_SESSION['admin_id'];

    if ($conn->query($sql) === TRUE) {
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Error updating bus: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Edit Bus</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <h2>Edit Bus</h2>
    <?php if (isset($error)) {
        echo "<p style='color:red;'>$error</p>";
    } ?>
    <form method="POST" action="">
        <label>Bus Name:</label>
        <input type="text" name="bus_name" value="<?php echo $bus['bus_name']; ?>" required><br>

        <label>Bus Number:</label>
        <input type="text" name="bus_number" value="<?php echo $bus['bus_number']; ?>" required><br>

        <label>Contact Number:</label>
        <input type="text" name="contact_number" value="<?php echo $bus['contact_number']; ?>" required><br>

        <label>Seats:</label>
        <input type="number" name="seats" value="<?php echo $bus['seats']; ?>" required><br>

        <label>Route From:</label>
        <input type="text" name="route_from" value="<?php echo $bus['route_from']; ?>" required><br>

        <label>Route To:</label>
        <input type="text" name="route_to" value="<?php echo $bus['route_to']; ?>" required><br>

        <label>Time:</label>
        <?php
        $time_parts = explode(" ", $bus['time']);
        $bus_time = $time_parts[0];
        $bus_ampm = $time_parts[1];
        ?>
        <input type="text" name="time" value="<?php echo $bus_time; ?>" required>
        <select name="ampm" required>
            <option value="AM" <?php if ($bus_ampm == "AM") echo "selected"; ?>>AM</option>
            <option value="PM" <?php if ($bus_ampm == "PM") echo "selected"; ?>>PM</option>
        </select>
        <br>

        <label>Price (Rs.):</label>
        <input type="number" name="price" value="<?php echo $bus['price']; ?>" required><br>

        <label>Bus Type:</label>
        <select name="bus_type" required>
            <option value="A/C" <?php if ($bus['bus_type'] == "A/C") echo "selected"; ?>>A/C</option>
            <option value="Non-A/C" <?php if ($bus['bus_type'] == "Non-A/C") echo "selected"; ?>>Non-A/C</option>
        </select>
        <br>

        <input type="submit" name="update_bus" value="Update Bus">
    </form>
    <br>
    <a href="dashboard.php">Back to Dashboard</a>
</body>

</html>
<?php
// user/request_trip.php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../user_login.php");
    exit();
}

if (isset($_POST['request_trip'])) {
    $user_id = $_SESSION['user_id'];
    $trip_bus_id = $_POST['trip_bus_id'];
    $route_from = $_POST['from'];
    $route_to = $_POST['to'];
    $date_from = $_POST['date_from'];
    $date_to = $_POST['date_to'];
    $days = $_POST['days'];

    // Check if the bus already has an approved trip for the requested dates
    $sql_check = "SELECT * FROM trip_requests WHERE trip_bus_id = '$trip_bus_id' AND status = 'Approved' AND (
(date_from BETWEEN '$date_from' AND '$date_to') OR 
(date_to BETWEEN '$date_from' AND '$date_to') OR 
('$date_from' BETWEEN date_from AND date_to) OR 
('$date_to' BETWEEN date_from AND date_to))";

    $result_check = $conn->query($sql_check);

    if ($result_check->num_rows > 0) {
        // If an approved trip exists, show an error message and redirect
        echo "<script>
                alert('This bus is already booked for the selected dates.');
                window.location.href = 'trip_buses.php';
            </script>";
        exit();
    }

    // If no approved trip exists, proceed with inserting the request
    $sql = "INSERT INTO trip_requests (user_id, trip_bus_id, route_from, route_to, date_from, date_to, days, status) 
            VALUES ('$user_id', '$trip_bus_id', '$route_from', '$route_to', '$date_from', '$date_to', '$days', 'Pending')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>
                alert('Request sent successfully!');
                window.location.href = 'trip_buses.php';
            </script>";
    } else {
        echo "<script>
                alert('Error sending request. Please try again.');
                window.location.href = 'trip_buses.php';
            </script>";
    }
    exit();
}
?>

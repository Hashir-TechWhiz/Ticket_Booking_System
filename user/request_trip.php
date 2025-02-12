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

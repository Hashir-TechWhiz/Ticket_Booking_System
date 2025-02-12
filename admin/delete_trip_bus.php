<?php
// admin/delete_trip_bus.php
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

// Ensure the trip bus belongs to the logged-in admin
$sql = "DELETE FROM trip_buses WHERE id = $trip_bus_id AND admin_id = $admin_id";

if ($conn->query($sql) === TRUE) {
    header("Location: dashboard.php");
    exit();
} else {
    die("Error deleting trip bus: " . $conn->error);
}

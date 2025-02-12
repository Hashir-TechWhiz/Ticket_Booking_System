<?php
// admin/delete_bus.php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

if (isset($_GET['id'])) {
    $bus_id = $_GET['id'];
    $sql = "DELETE FROM buses WHERE id = $bus_id AND admin_id = " . $_SESSION['admin_id'];
    $conn->query($sql);
}

header("Location: dashboard.php");
exit();

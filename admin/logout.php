<?php
session_start();

// Prevent caching to avoid showing the previous page after logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Destroy session and log out
session_destroy();

// Redirect to home page or login page after logout
header("Location: ../index.php");
exit();
?>

<?php
// admin_register.php
session_start();
include("includes/db.php");

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $phone          = $_POST['phone'];
    $email          = $_POST['email'];
    $password       = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO admins (name, phone, email, password) VALUES ('$name', '$phone', '$email', '$password')";
    if ($conn->query($sql) === TRUE) {
        $_SESSION['admin_id'] = $conn->insert_id;
        header("Location: admin/dashboard.php");
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
    <title>Admin Registration</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <h2>Admin Registration</h2>
    <?php if (isset($error)) {
        echo "<p style='color:red;'>$error</p>";
    } ?>
    <form method="POST" action="">
        <label>Bus Group Name:</label>
        <input type="text" name="name" required><br>
        <label>Phone:</label>
        <input type="text" name="phone" required><br>
        <label>Email:</label>
        <input type="email" name="email" required><br>
        <label>Password:</label>
        <input type="password" name="password" required><br>
        <input type="submit" name="register" value="Register">
    </form>
</body>

</html>
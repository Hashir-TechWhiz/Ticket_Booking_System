<?php
// admin_register.php
session_start();
include("includes/db.php");

if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Initialize error variables
    $nameError = $phoneError = $emailError = $passwordError = "";
    $hasError = false;

    // Name validation
    if (empty($name)) {
        $nameError = "Bus group name is required.";
        $hasError = true;
    }

    // Phone validation
    if (empty($phone)) {
        $phoneError = "Phone number is required.";
        $hasError = true;
    } elseif (!preg_match("/^[0-9]{10}$/", $phone)) {
        $phoneError = "Invalid phone number format.";
        $hasError = true;
    }

    // Email validation
    if (empty($email)) {
        $emailError = "Email is required.";
        $hasError = true;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailError = "Invalid email format.";
        $hasError = true;
    } else {
        // Check if email already exists
        $checkEmailQuery = "SELECT id FROM admins WHERE email = ?";
        $stmt = $conn->prepare($checkEmailQuery);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $emailError = "Email is already registered.";
            $hasError = true;
        }
        $stmt->close();
    }

    // Password validation
    if (empty($password)) {
        $passwordError = "Password is required.";
        $hasError = true;
    } elseif (strlen($password) < 6) {
        $passwordError = "Password must be at least 6 characters.";
        $hasError = true;
    }

    if (!$hasError) {
        // Hash password and insert data if no errors
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO admins (name, phone, email, password) VALUES ('$name', '$phone', '$email', '$passwordHash')";
        if ($conn->query($sql) === TRUE) {
            $_SESSION['admin_id'] = $conn->insert_id;
            header("Location: admin/dashboard.php");
            exit();
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Admin Registration</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>

<body class="relative flex flex-row bg-[#c9dcfa]">

    <section class="hidden lg:flex sticky h-40 w-full top-0 h-screen flex-1">
        <img src="assets/images/AdminRegister.jpg" alt="Bus" class="object-cover size-full">
    </section>

    <section class="my-auto flex h-full min-h-screen flex-1 items-center px-5 py-10">
        <div class="mx-auto flex flex-col gap-6 rounded-lg p-10 bg-white shadow-lg">
            <div class="flex flex-col gap-1 max-w-md">
                <h2 class="text-[#4E71FF] text-2xl font-bold">Create your Admin Account!</h2>
                <p class="text-gray-500">Register as an admin to manage the trip bookings and handle user requests seamlessly.</p>
            </div>

            <form method="POST" action="">
                <div class="flex flex-col">
                    <label class="text-[#4E71FF] font-semibold">Bus Group Name:</label>
                    <input type="text" name="name" value="<?php echo isset($name) ? $name : ''; ?>" required placeholder="Enter your group name" class="p-2 border border-blue-500 rounded-xl outline-none">
                    <span class="text-red-500">
                        <?php if (!empty($nameError)) {
                            echo $nameError;
                        } ?></span>
                    <br>

                    <label class="text-[#4E71FF] font-semibold">Phone:</label>
                    <input type="number" name="phone" value="<?php echo isset($phone) ? $phone : ''; ?>" required placeholder="Enter your phone number" class="p-2 border border-blue-500 rounded-xl outline-none">
                    <span class="text-red-500">
                        <?php if (!empty($phoneError)) {
                            echo $phoneError;
                        } ?></span>
                    <br>

                    <label class="text-[#4E71FF] font-semibold">Email:</label>
                    <input type="email" name="email" value="<?php echo isset($email) ? $email : ''; ?>" required placeholder="Enter your email" class="p-2 border border-blue-500 rounded-xl outline-none">
                    <span class="text-red-500">
                        <?php if (!empty($emailError)) {
                            echo $emailError;
                        } ?></span>
                    <br>

                    <label class="text-[#4E71FF] font-semibold">Password:</label>
                    <input type="password" name="password" value="<?php echo isset($password) ? $password : ''; ?>" placeholder="Enter your password" required class="p-2 border border-blue-500 rounded-xl outline-none">
                    <span class="text-red-500">
                        <?php if (!empty($passwordError)) {
                            echo $passwordError;
                        } ?></span>
                    <br>

                    <input type="submit" name="register" value="Register" class="mt-2 px-6 py-2 bg-[#4E71FF] hover:bg-[#4E71FF]/80 text-white rounded-lg text-md w-full cursor-pointer">
                </div>
            </form>

            <p class="pl-1">Already have an account? <a href="admin_login.php" class="text-[#4E71FF]">Log in</a></p>
        </div>
    </section>
</body>

</html>
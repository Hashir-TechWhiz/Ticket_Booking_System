<?php
session_start();
include("includes/db.php");

$nameError = $phoneError = $emailError = $passwordError = "";
$name = $phone = $email = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = trim($_POST['name']);
    $phone    = trim($_POST['phone']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    $hasError = false;

    // Name validation
    if (empty($name)) {
        $nameError = "Name is required.";
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
        $checkEmailQuery = "SELECT id FROM users WHERE email = ?";
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

    // If no errors, insert into database
    if (!$hasError) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $insertQuery = "INSERT INTO users (name, phone, email, password) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($insertQuery);
        $stmt->bind_param("ssss", $name, $phone, $email, $hashedPassword);

        if ($stmt->execute()) {
            $_SESSION['user_id'] = $stmt->insert_id;
            header("Location: user/dashboard.php");
            exit();
        } else {
            $emailError = "Something went wrong. Please try again.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>User Registration</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>

<body class="relative flex flex-row bg-[#c9dcfa]">

    <section class="hidden lg:flex sticky h-40 w-full top-0 h-screen flex-1">
        <div class="absolute inset-0 bg-black/40"></div>
        <img src="assets/images/Bus.png" alt="Bus" class="object-cover size-full">
    </section>

    <section class="my-auto flex h-full min-h-screen flex-1 items-center px-5 py-10">
        <div class="mx-auto flex flex-col gap-6 rounded-lg p-10 bg-white shadow-lg">
            <div class="flex flex-col gap-1 max-w-md">
                <h2 class="text-[#4E71FF] text-2xl font-bold">Create your Account!</h2>
                <p class="text-gray-500">Create an account to book your trips easily and enjoy a hassle-free travel experience.</p>
            </div>

            <form method="POST" action="">
                <div class="flex flex-col gap-3">
                    <label class="text-[#4E71FF] font-semibold">Name:</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($name); ?>" required placeholder="Enter your name" class="p-2 border border-blue-500 rounded-xl outline-none">
                    <span class="text-red-500 text-sm"><?php echo $nameError; ?></span>

                    <label class="text-[#4E71FF] font-semibold">Phone:</label>
                    <input type="number" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required placeholder="Enter your phone number" class="p-2 border border-blue-500 rounded-xl outline-none">
                    <span class="text-red-500 text-sm"><?php echo $phoneError; ?></span>

                    <label class="text-[#4E71FF] font-semibold">Email:</label>
                    <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required placeholder="Enter your email" class="p-2 border border-blue-500 rounded-xl outline-none">
                    <span class="text-red-500 text-sm"><?php echo $emailError; ?></span>

                    <label class="text-[#4E71FF] font-semibold">Password:</label>
                    <input type="password" name="password" required placeholder="Enter your password" class="p-2 border border-blue-500 rounded-xl outline-none">
                    <span class="text-red-500 text-sm"><?php echo $passwordError; ?></span>

                    <input type="submit" name="register" value="Register" class="mt-2 px-6 py-2 bg-[#4E71FF] hover:bg-[#4E71FF]/80 text-white rounded-lg text-md w-full cursor-pointer">
                </div>
            </form>

            <p class="pl-1">Already have an account? <a href="user_login.php" class="text-[#4E71FF]">Log in</a></p>
        </div>
    </section>
</body>

</html>
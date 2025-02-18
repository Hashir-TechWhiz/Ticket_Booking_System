<?php
// admin_login.php
session_start();
include("includes/db.php");

// Redirect logged-in admin away from the login page
if (isset($_SESSION['admin_id'])) {
    header("Location: admin/dashboard.php");
    exit();
}

$emailError = $passwordError = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Validation
    if (empty($email)) {
        $emailError = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailError = "Invalid email format.";
    }

    if (empty($password)) {
        $passwordError = "Password is required.";
    }

    if (empty($emailError) && empty($passwordError)) {
        // Use prepared statement for security
        $sql = "SELECT id, password FROM admins WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['password'])) {
                $_SESSION['admin_id'] = $row['id'];
                header("Location: admin/dashboard.php");
                exit();
            } else {
                $passwordError = "Incorrect password.";
            }
        } else {
            $emailError = "Admin not found.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Admin Login</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>

<body class="relative flex flex-row bg-[#c9dcfa]">
    <section class="my-auto flex h-full min-h-screen flex-1 items-center px-5 py-10">
        <div class="mx-auto flex flex-col gap-6 rounded-lg p-10 bg-white shadow-lg">

            <div class="flex flex-col gap-1 max-w-md">
                <h2 class="text-[#4E71FF] text-2xl font-bold">Welcome Back Admin!</h2>
                <p class="text-gray-500">Sign in to manage trips, bookings, and users with ease.</p>
            </div>

            <form method="POST" action="">
                <div class="flex flex-col gap-3">
                    <label class="text-[#4E71FF] font-semibold">Email:</label>
                    <input
                        type="email"
                        name="email"
                        value="<?php echo htmlspecialchars($email); ?>"
                        required
                        class="p-2 border border-blue-500 rounded-xl outline-none"
                        placeholder="Enter your email">
                    <span class="text-red-500 text-sm pl-1"><?php echo $emailError; ?></span>

                    <label class="text-[#4E71FF] font-semibold">Password:</label>
                    <input
                        type="password"
                        name="password"
                        required
                        class="p-2 border border-blue-500 rounded-xl outline-none"
                        placeholder="Enter your password">
                    <span class="text-red-500 text-sm"><?php echo $passwordError; ?></span>

                    <input
                        type="submit"
                        name="login"
                        value="Login"
                        class="mt-2 px-6 py-2 bg-[#4E71FF] hover:bg-[#4E71FF]/80 text-white rounded-lg text-md w-full cursor-pointer">
                </div>
            </form>

            <p>Don't have an account? <a href="admin_register.php" class="text-[#4E71FF]">Register Here</a></p>
        </div>
    </section>

    <section class="hidden lg:flex sticky h-40 w-full top-0 h-screen flex-1">
        <img src="assets/images/AdminLogin.jpg" alt="Bus" class="object-cover size-full">
    </section>

</body>

</html>
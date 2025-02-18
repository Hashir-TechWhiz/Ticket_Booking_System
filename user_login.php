<?php
// user_login.php
session_start();
include("includes/db.php");


if (isset($_SESSION['user_id'])) {
    header("Location: user/dashboard.php");
    exit();
}

$emailError = $passwordError = "";
$email = $password = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Prepared statement to prevent SQL injection
    $sql = "SELECT id, password FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Verify password using password_verify (assuming passwords are hashed)
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['id'];
            header("Location: user/dashboard.php");
            exit();
        } else {
            $passwordError = "Invalid password";
        }
    } else {
        $emailError = "User not found";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>User Login</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>

<body class="relative flex flex-row bg-[#c9dcfa]">
    <section class="my-auto flex h-full min-h-screen flex-1 items-center px-5 py-10">
        <div class="mx-auto flex flex-col gap-6 rounded-lg p-10 bg-white shadow-lg">

            <div class="flex flex-col gap-1 max-w-md">
                <h2 class="text-[#4E71FF] text-2xl font-bold">Welcome Back!</h2>
                <p class="text-gray-500">Login to continue your journey with us and explore seamless ticket booking.</p>
            </div>

            <form method="POST" action="">

                <div class="flex flex-col">

                    <label class="text-[#4E71FF] font-semibold">Email:</label>
                    <input
                        type="email"
                        name="email"
                        value="<?php echo htmlspecialchars($email); ?>"
                        required
                        class="p-2 border border-blue-500 rounded-xl outline-none"
                        placeholder="Enter your email">
                    <?php if (!empty($emailError)) {
                        echo "<p class='text-red-500'>$emailError</p>";
                    } ?>

                    <br>

                    <label class="text-[#4E71FF] font-semibold">Password:</label>
                    <input
                        type="password"
                        name="password"
                        required
                        class="p-2 border border-blue-500 rounded-xl outline-none"
                        placeholder="Enter your password">
                    <?php if (!empty($passwordError)) {
                        echo "<p class='text-red-500'>$passwordError</p>";
                    } ?>

                    <br>
                    <input
                        type="submit"
                        name="login"
                        value="Login"
                        class="px-6 py-2 bg-[#4E71FF] hover:bg-[#4E71FF]/80 text-white rounded-lg text-md w-full cursor-pointer">
                </div>

            </form>
            <p>Don't have an account? <a href="user_register.php" class="text-[#4E71FF]">Register Here</a></p>
        </div>
    </section>

    <section class="hidden lg:flex sticky h-40 w-full top-0 h-screen flex-1">
        <!-- Image -->
        <img src="assets/images/login.jpg" alt="Bus" class="object-cover size-full">
    </section>

</body>

</html>
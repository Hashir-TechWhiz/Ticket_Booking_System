<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Go Sri Lanka</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="assets/js/landingPopup.js"></script>
</head>

<body class="relative flex flex-col items-start justify-center h-screen bg-cover bg-center px-[6%]" style="background-image: url('assets/images/Bus.webp');">

    <!-- Dark Overlay -->
    <div class="absolute inset-0 bg-black/50"></div>

    <!-- Nav -->
    <nav class="absolute top-0 left-0 z-10 flex items-center justify-between bg-gray-900 w-full px-[6%] py-4 shadow-lg">
        <a href="index.php" class="flex items-center gap-2">
            <img src="assets/images/Logo.png" alt="logo" class="h-12 filter invert" />
            <h1 class="text-2xl font-bold text-white">Go Sri Lanka</h1>
        </a>


        <!-- Login/Register Button -->
        <div class="relative">
            <button id="loginButton" class="px-6 py-1 bg-[#4E71FF] hover:bg-white hover:text-blue-500 text-white rounded-3xl text-lg font-semibold">
                Login / Register
            </button>

            <!-- Pop-up menu -->
            <div id="loginPopup" class="absolute right-0 mt-2 w-48 bg-white text-black rounded-lg shadow-lg hidden">
                <a href="user_login.php" class="block px-4 py-2 hover:bg-gray-200 hover:rounded-lg">User</a>
                <a href="admin_login.php" class="block px-4 py-2 hover:bg-gray-200 hover:rounded-lg">Admin</a>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="relative flex flex-col gap-8 z-10 text-white max-w-4xl">
        <h1 class="text-5xl font-bold">Go Sri Lanka</h1>
        <p class="text-lg leading-relaxed text-white/80 text-left">
            Go Sri Lanka is your ultimate travel companion for booking bus tickets across the island. Whether you’re planning a quick city commute, exploring the vibrant landscapes, or embarking on a scenic road trip, we make booking your journey effortless and reliable. With just a few clicks, you can book a bus ticket for your desired destination, check availability, and secure your seat in advance. <br/> <br/>

            Our user-friendly platform ensures a seamless experience with easy navigation, real-time updates, and secure payment options. From the busy streets of Colombo to the peaceful beaches of the south and the breathtaking hill country, Go Sri Lanka connects you to your dream destinations. We’re here to ensure your journey is smooth, comfortable, and stress-free. Start your adventure today with Go Sri Lanka and enjoy hassle-free travel across the beautiful island!
        </p>
        <button class="px-6 py-2 bg-[#4E71FF] hover:bg-white hover:text-[#4E71FF] text-white rounded-lg text-md w-48">
            Why Choose Us?
        </button>
    </div>

</body>

</html>
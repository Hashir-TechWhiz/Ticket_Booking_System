<?php
// admin/add_bus.php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

$errors = [];

if (isset($_POST['add_bus'])) {
    $bus_number     = $_POST['bus_number'];
    $bus_name       = $_POST['bus_name'];
    $contact_number = $_POST['contact_number'];
    $seats          = $_POST['seats'];
    $route_from     = $_POST['route_from'];
    $route_to       = $_POST['route_to'];
    $time           = $_POST['time'] . " " . $_POST['ampm'];
    $price          = $_POST['price'];
    $bus_type       = $_POST['bus_type'];
    $admin_id       = $_SESSION['admin_id'];

    // Validation
    if (trim($bus_name) === '') {
        $errors['bus_name'] = "Bus name is required.";
    }

    if (!preg_match("/^[A-Z]{2} \d{4}$/", $bus_number)) {
        $errors['bus_number'] = "Format must be 'XX 0000' (e.g., NC 0001).";
    }

    if (!preg_match("/^\d{10}$/", $contact_number)) {
        $errors['contact_number'] = "Must be exactly 10 digits.";
    }

    if (!ctype_digit($seats) || (int)$seats < 49 || (int)$seats > 58) {
        $errors['seats'] = "Must be a number between 49 and 58.";
    }

    if (empty($errors)) {
        $sql = "INSERT INTO buses (admin_id, bus_number, bus_name, contact_number, seats, route_from, route_to, time, price, bus_type) 
                VALUES ($admin_id, '$bus_number', '$bus_name', '$contact_number', $seats, '$route_from', '$route_to', '$time', $price, '$bus_type')";

        if ($conn->query($sql) === TRUE) {
            header("Location: dashboard.php");
            exit();
        } else {
            $errors[] = "Error: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Add Bus</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function formatTimeInput(input) {
            let value = input.value.replace(/[^\d]/g, '');

            if (value.length > 2) {
                value = value.substring(0, 2) + ':' + value.substring(2, 4);
            }
            input.value = value;
        }
    </script>
</head>

<body class="relative flex flex-col items-center justify-start min-h-screen bg-cover bg-center bg-no-repeat px-[6%] py-5" style="background-image: url('../assets/images/AdminBg.jpg');">

    <div class="relative flex justify-center items-center bg-white rounded-xl p-2 shadow-xl w-full">
        <a href="javascript:window.history.back();" class="absolute left-3 flex items-center gap-2 text-blue-500">
            <img src="../assets/icons/Back.png" alt="Back" class="w-5 h-5"> Back
        </a>

        <h2 class="flex items-center justify-center text-2xl font-semibold text-[#4E71FF] w-full">
            Route Bus Management
        </h2>
    </div>

    <div class="bg-white rounded-xl shadow-lg p-8 w-full max-w-xl mt-10">
        <!-- Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-1">Add Route bus</h2>
            <p class="text-gray-600">Fill out the form to add a new route bus.</p>
        </div>

        <form method="POST" action="" class="space-y-4">
            <!-- Form Fields -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bus Number</label>
                <input type="text" name="bus_number" required value="<?php echo htmlspecialchars($_POST['bus_number'] ?? '') ?>"
                    class="w-full p-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <?php if (isset($errors['bus_number'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?php echo $errors['bus_number']; ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bus Name</label>
                <input type="text" name="bus_name" required value="<?php echo htmlspecialchars($_POST['bus_name'] ?? '') ?>"
                    class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <?php if (isset($errors['bus_name'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?php echo $errors['bus_name']; ?></p>
                <?php endif; ?>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Contact Number</label>
                    <input type="tel" name="contact_number" maxlength="10" required value="<?php echo htmlspecialchars($_POST['contact_number'] ?? '') ?>"
                        class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <?php if (isset($errors['contact_number'])): ?>
                        <p class="text-red-500 text-sm mt-1"><?php echo $errors['contact_number']; ?></p>
                    <?php endif; ?>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Seats</label>
                    <input type="number" name="seats" required value="<?php echo htmlspecialchars($_POST['seats'] ?? '') ?>"
                        class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <?php if (isset($errors['seats'])): ?>
                        <p class="text-red-500 text-sm mt-1"><?php echo $errors['seats']; ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Route From</label>
                    <input type="text" name="route_from" required value="<?php echo htmlspecialchars($_POST['route_from'] ?? '') ?>"
                        class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Route To</label>
                    <input type="text" name="route_to" required value="<?php echo htmlspecialchars($_POST['route_to'] ?? '') ?>"
                        class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Departure Time</label>
                <div class="flex gap-2">
                    <input type="text" name="time" placeholder="10:00" oninput="formatTimeInput(this)"
                        pattern="\d{2}:\d{2}" required
                        value="<?php echo htmlspecialchars($_POST['time'] ?? '') ?>"
                        class="w-1/2 p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <select name="ampm" required
                        class="w-1/2 p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="AM" <?= isset($_POST['ampm']) && $_POST['ampm'] === 'AM' ? 'selected' : '' ?>>AM</option>
                        <option value="PM" <?= isset($_POST['ampm']) && $_POST['ampm'] === 'PM' ? 'selected' : '' ?>>PM</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Price (Rs)</label>
                    <input type="number" name="price" step="0.01" required
                        value="<?php echo htmlspecialchars($_POST['price'] ?? '') ?>"
                        class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bus Type</label>
                    <select name="bus_type" required
                        class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="A/C" <?= isset($_POST['bus_type']) && $_POST['bus_type'] === 'A/C' ? 'selected' : '' ?>>A/C</option>
                        <option value="Non-A/C" <?= isset($_POST['bus_type']) && $_POST['bus_type'] === 'Non-A/C' ? 'selected' : '' ?>>Non-A/C</option>
                    </select>
                </div>
            </div>

            <button type="submit" name="add_bus"
                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-md transition-colors">
                Add Bus
            </button>
        </form>
    </div>
</body>

</html>
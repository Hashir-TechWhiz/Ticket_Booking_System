<?php
// admin/add_trip_bus.php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

if (isset($_POST['add_trip_bus'])) {
    $bus_name = $_POST['bus_name'];
    $bus_number = $_POST['bus_number'];
    $contact_number = $_POST['contact_number'];
    $seats = $_POST['seats'];
    $bus_type = $_POST['bus_type'];

    $sql = "INSERT INTO trip_buses (admin_id, bus_name, bus_number, contact_number, seats, bus_type) 
            VALUES (" . $_SESSION['admin_id'] . ", '$bus_name', '$bus_number', '$contact_number', $seats, '$bus_type')";

    if ($conn->query($sql) === TRUE) {
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Error adding trip bus: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Trip Bus</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-lg w-full max-w-2xl p-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800">Add Trip Bus</h2>
            <p class="text-gray-600">Fill out the form to add a new trip bus.</p>
        </div>

        <!-- Error Message -->
        <?php if (isset($error)) { ?>
            <div class="bg-red-50 border border-red-200 text-red-600 px-4 py-3 rounded-lg mb-6">
                <?php echo $error; ?>
            </div>
        <?php } ?>

        <!-- Add Trip Bus Form -->
        <form method="POST" action="" class="space-y-6">
            <!-- Bus Name -->
            <div>
                <label for="bus_name" class="block text-sm font-medium text-gray-700">Bus Name</label>
                <input type="text" name="bus_name" id="bus_name" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
            </div>

            <!-- Bus Number -->
            <div>
                <label for="bus_number" class="block text-sm font-medium text-gray-700">Bus Number</label>
                <input type="text" name="bus_number" id="bus_number" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
            </div>

            <!-- Contact Number -->
            <div>
                <label for="contact_number" class="block text-sm font-medium text-gray-700">Contact Number</label>
                <input type="text" name="contact_number" id="contact_number" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
            </div>

            <!-- Seats -->
            <div>
                <label for="seats" class="block text-sm font-medium text-gray-700">Seats</label>
                <input type="number" name="seats" id="seats" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
            </div>

            <!-- Bus Type -->
            <div>
                <label for="bus_type" class="block text-sm font-medium text-gray-700">Bus Type</label>
                <select name="bus_type" id="bus_type" required
                    class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition duration-200">
                    <option value="AC">AC</option>
                    <option value="Non-AC">Non-AC</option>
                </select>
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit" name="add_trip_bus"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Add Trip Bus
                </button>
            </div>
        </form>

        <!-- Back to Dashboard Link -->
        <div class="mt-6 text-center">
            <a href="dashboard.php" class="text-blue-600 hover:text-blue-800 transition duration-200">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
        </div>
    </div>
</body>

</html>
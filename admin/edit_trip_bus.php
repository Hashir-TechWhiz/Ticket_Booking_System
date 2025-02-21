<?php
// admin/edit_trip_bus.php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

// Check if trip bus ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid request.");
}

$trip_bus_id = $_GET['id'];
$admin_id = $_SESSION['admin_id'];

// Fetch existing trip bus details
$sql = "SELECT * FROM trip_buses WHERE id = $trip_bus_id AND admin_id = $admin_id";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die("Trip bus not found.");
}

$trip_bus = $result->fetch_assoc();

// Handle form submission for updating trip bus
if (isset($_POST['update_trip_bus'])) {
    $bus_name = trim($_POST['bus_name']);
    $bus_number = trim($_POST['bus_number']);
    $contact_number = trim($_POST['contact_number']);
    $seats = trim($_POST['seats']);
    $bus_type = $_POST['bus_type'];

    $errors = [];

    // Validations
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

    // If no errors, update the trip bus
    if (empty($errors)) {
        $update_sql = "UPDATE trip_buses SET bus_name = '$bus_name', bus_number = '$bus_number', contact_number = '$contact_number', seats = $seats, bus_type = '$bus_type' WHERE id = $trip_bus_id AND admin_id = $admin_id";

        if ($conn->query($update_sql) === TRUE) {
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Error updating trip bus: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Trip Bus</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="relative flex flex-col items-center justify-start min-h-screen bg-cover bg-center bg-no-repeat px-[6%] py-5" style="background-image: url('../assets/images/AdminBg.jpg');">

    <div class="relative flex justify-center items-center bg-white rounded-xl p-2 shadow-xl w-full">
        <a href="javascript:window.history.back();" class="absolute left-3 flex items-center gap-2 text-blue-500">
            <img src="../assets/icons/Back.png" alt="Back" class="w-5 h-5"> Back
        </a>

        <h2 class="flex items-center justify-center text-2xl font-semibold text-[#4E71FF] w-full">
            Trip Bus Management
        </h2>
    </div>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Trip Bus</h2>

            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="" class="space-y-6">
                <!-- Bus Name -->
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Bus Name:</label>
                    <input type="text" name="bus_name" value="<?php echo htmlspecialchars($trip_bus['bus_name']); ?>"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                    <?php if (isset($errors['bus_name'])): ?>
                        <p class="text-red-500 text-sm mt-1"><?php echo $errors['bus_name']; ?></p>
                    <?php endif; ?>
                </div>

                <!-- Bus Number -->
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Bus Number:</label>
                    <input type="text" name="bus_number" value="<?php echo htmlspecialchars($trip_bus['bus_number']); ?>"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                    <?php if (isset($errors['bus_number'])): ?>
                        <p class="text-red-500 text-sm mt-1"><?php echo $errors['bus_number']; ?></p>
                    <?php endif; ?>
                </div>

                <!-- Contact Number -->
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Contact Number:</label>
                    <input type="text" name="contact_number" maxlength="10" value="<?php echo htmlspecialchars($trip_bus['contact_number']); ?>"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                    <?php if (isset($errors['contact_number'])): ?>
                        <p class="text-red-500 text-sm mt-1"><?php echo $errors['contact_number']; ?></p>
                    <?php endif; ?>
                </div>

                <!-- Seats -->
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Seats:</label>
                    <input type="number" name="seats" value="<?php echo htmlspecialchars($trip_bus['seats']); ?>"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                    <?php if (isset($errors['seats'])): ?>
                        <p class="text-red-500 text-sm mt-1"><?php echo $errors['seats']; ?></p>
                    <?php endif; ?>
                </div>

                <!-- Bus Type -->
                <div>
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Bus Type:</label>
                    <select name="bus_type"
                        class="w-full px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                        <option value="AC" <?php echo ($trip_bus['bus_type'] == 'AC') ? 'selected' : ''; ?>>AC</option>
                        <option value="Non-AC" <?php echo ($trip_bus['bus_type'] == 'Non-AC') ? 'selected' : ''; ?>>Non-AC</option>
                    </select>
                </div>

                <!-- Submit Button -->
                <div>
                    <input type="submit" name="update_trip_bus" value="Update Trip Bus"
                        class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 px-4 rounded-lg transition duration-200">
                </div>
            </form>
        </div>
    </div>
</body>

</html>
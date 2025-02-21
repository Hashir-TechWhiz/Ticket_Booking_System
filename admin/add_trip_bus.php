<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

$errors = [];
$bus_name = $bus_number = $contact_number = $seats = $bus_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $bus_name = trim($_POST['bus_name'] ?? '');
    $bus_number = trim($_POST['bus_number'] ?? '');
    $contact_number = trim($_POST['contact_number'] ?? '');
    $seats = trim($_POST['seats'] ?? '');
    $bus_type = trim($_POST['bus_type'] ?? '');

    // Validation
    if ($bus_name === '') {
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

    if (!in_array($bus_type, ["AC", "Non-AC"])) {
        $errors['bus_type'] = "Invalid bus type.";
    }

    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO trip_buses (admin_id, bus_name, bus_number, contact_number, seats, bus_type) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssis", $_SESSION['admin_id'], $bus_name, $bus_number, $contact_number, $seats, $bus_type);

        if ($stmt->execute()) {
            header("Location: dashboard.php");
            exit();
        } else {
            $errors['general'] = "Database error: " . $conn->error;
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Add Trip Bus</title>
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

    <div class="bg-white rounded-lg shadow-lg w-full max-w-2xl p-8 mt-10">
        <h2 class="text-2xl font-bold text-gray-800">Add Trip Bus</h2>
        <p class="text-gray-600">Fill out the form to add a new trip bus.</p>

        <form method="POST" action="" class="space-y-6 mt-5">
            <div>
                <label class="block text-sm font-medium">Bus Name</label>
                <input type="text" name="bus_name" value="<?= htmlspecialchars($bus_name) ?>"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                <?php if (isset($errors['bus_name'])) { ?>
                    <p class="text-red-500 text-sm mt-1"><?= $errors['bus_name'] ?></p>
                <?php } ?>
            </div>

            <div>
                <label class="block text-sm font-medium">Bus Number</label>
                <input type="text" name="bus_number" value="<?= htmlspecialchars($bus_number) ?>"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                <?php if (isset($errors['bus_number'])) { ?>
                    <p class="text-red-500 text-sm mt-1"><?= $errors['bus_number'] ?></p>
                <?php } ?>
            </div>

            <div>
                <label class="block text-sm font-medium">Contact Number</label>
                <input type="tel" name="contact_number" maxlength="10" value="<?= htmlspecialchars($contact_number) ?>"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                <?php if (isset($errors['contact_number'])) { ?>
                    <p class="text-red-500 text-sm mt-1"><?= $errors['contact_number'] ?></p>
                <?php } ?>
            </div>

            <div>
                <label class="block text-sm font-medium">Seats</label>
                <input type="number" name="seats" value="<?= htmlspecialchars($seats) ?>" min="49" max="58"
                    class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                <?php if (isset($errors['seats'])) { ?>
                    <p class="text-red-500 text-sm mt-1"><?= $errors['seats'] ?></p>
                <?php } ?>
            </div>

            <div>
                <label class="block text-sm font-medium">Bus Type</label>
                <select name="bus_type" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="AC" <?= ($bus_type === "AC") ? "selected" : "" ?>>AC</option>
                    <option value="Non-AC" <?= ($bus_type === "Non-AC") ? "selected" : "" ?>>Non-AC</option>
                </select>
                <?php if (isset($errors['bus_type'])) { ?>
                    <p class="text-red-500 text-sm mt-1"><?= $errors['bus_type'] ?></p>
                <?php } ?>
            </div>

            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                Add Trip Bus
            </button>
        </form>
    </div>
</body>

</html>
<?php
// user/select_route.php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../user_login.php");
    exit();
}

if (!isset($_GET['from']) || !isset($_GET['to']) || !isset($_GET['journey_date'])) {
    die("Error: Missing required parameters.");
}

$from = $_GET['from'];
$to = $_GET['to'];
$journey_date = $_GET['journey_date'];

// Fetch buses matching the route
$sql = "SELECT bus_name, bus_number, contact_number, price, bus_type, seats, time, id 
        FROM buses 
        WHERE route_from = '$from' AND route_to = '$to'";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Available Buses</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>

<body class="relative min-h-screen bg-cover bg-center bg-no-repeat px-[6%] py-5"
    style="background-image: url('../assets/images/Bg.jpg');">

    <div class="relative flex justify-center items-center bg-white rounded-xl p-2 shadow-xl w-full">
        <a href="dashboard.php" class="absolute left-3 flex items-center gap-2 text-blue-500">
            <img src="../assets/icons/Back.png" alt="Back" class="w-5 h-5"> Back</a>
        <h2 class="flex items-center justify-center text-2xl font-semibold text-[#4E71FF] w-full">
            Available Buses from <?php echo htmlspecialchars($from); ?> to <?php echo htmlspecialchars($to); ?>
        </h2>
    </div>

    <?php if ($result->num_rows > 0) { ?>
        <!-- Table -->
        <div class="overflow-x-auto mt-5 shadow-lg rounded-lg">
            <table class="w-full border-collapse border border-gray-300 bg-white">
                <thead>
                    <tr class="bg-[#4E71FF] text-white">
                        <th class="p-3 border border-gray-300">Bus Name</th>
                        <th class="p-3 border border-gray-300">Bus Number</th>
                        <th class="p-3 border border-gray-300">Contact Number</th>
                        <th class="p-3 border border-gray-300">Bus Type</th>
                        <th class="p-3 border border-gray-300">Time</th>
                        <th class="p-3 border border-gray-300">Price</th>
                        <th class="p-3 border border-gray-300">Seats</th>
                        <th class="p-3 border border-gray-300">Action</th>
                    </tr>
                </thead>

                <tbody class="bg-white">
                    <?php while ($bus = $result->fetch_assoc()) { ?>
                        <tr class="hover:bg-gray-100 text-center">
                            <td class="p-3 border border-gray-300"><?php echo htmlspecialchars($bus['bus_name']); ?></td>
                            <td class="p-3 border border-gray-300"><?php echo htmlspecialchars($bus['bus_number']); ?></td>
                            <td class="p-3 border border-gray-300"><?php echo htmlspecialchars($bus['contact_number']); ?></td>
                            <td class="p-3 border border-gray-300"><?php echo htmlspecialchars($bus['bus_type']); ?></td>
                            <td class="p-3 border border-gray-300"><?php echo htmlspecialchars($bus['time']); ?></td> 
                            <td class="p-3 border border-gray-300"><?php echo htmlspecialchars($bus['price']); ?></td>
                            <td class="p-3 border border-gray-300"><?php echo htmlspecialchars($bus['seats']); ?></td>
                            <td class="p-3 border border-gray-300">
                                <a href="select_seat.php?bus_id=<?php echo urlencode($bus['id']); ?>&journey_date=<?php echo urlencode($journey_date); ?>"
                                    class="px-3 py-1 bg-green-500 text-white rounded-lg hover:bg-green-600">
                                    Select Seat
                                </a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } else { ?>
        <p class="text-center text-gray-600 mt-10 text-xl font-semibold">
            🚍 Sorry.. No buses available for this route.
        </p>
    <?php } ?>

</body>

</html>
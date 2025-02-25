<?php
// user/trip_buses.php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../user_login.php");
    exit();
}

// Fetch available trip buses
$sql = "SELECT * FROM trip_buses";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Available Trip Buses</title>
    <script src="../assets/js/popup.js" defer></script>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
        /* Styling for booked dates */
        .flatpickr-day.booked-date {
            background-color: #fecaca !important;
            /* Tailwind red-200 */
            color: #b91c1c !important;
            /* Tailwind red-700 */
            cursor: not-allowed;
        }

        /* Styling for past dates */
        .flatpickr-day.past-date {
            background-color: #e5e7eb !important;
            /* Tailwind gray-200 */
            color: #6b7280 !important;
            /* Tailwind gray-500 */
            cursor: not-allowed;
        }
    </style>
</head>

<body class="relative min-h-screen bg-cover bg-center bg-no-repeat px-[6%] py-5" style="background-image: url('../assets/images/Bg.jpg');">

    <div class="relative flex justify-center items-center bg-white rounded-xl p-2 shadow-xl w-full">
        <a href="dashboard.php" class="absolute left-3 flex items-center gap-2 text-blue-500">
            <img src="../assets/icons/Back.png" alt="Back" class="w-5 h-5"> Back</a>
        <h2 class="flex items-center justify-center text-2xl font-semibold text-[#4E71FF] w-full">Available Trip Buses</h2>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto mt-5 shadow-lg rounded-lg">
        <table class="w-full border-collapse border border-gray-300 bg-white">
            <thead>
                <tr class="bg-[#4E71FF] text-white">
                    <th class="p-3 border border-gray-300">Bus Name</th>
                    <th class="p-3 border border-gray-300">Bus Number</th>
                    <th class="p-3 border border-gray-300">Contact Number</th>
                    <th class="p-3 border border-gray-300">Seats</th>
                    <th class="p-3 border border-gray-300">Bus Type</th>
                    <th class="p-3 border border-gray-300">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white">
                <?php while ($bus = $result->fetch_assoc()) { ?>
                    <tr class="hover:bg-gray-100 text-center">
                        <td class="p-3 border border-gray-300"><?php echo htmlspecialchars($bus['bus_name']); ?></td>
                        <td class="p-3 border border-gray-300"><?php echo htmlspecialchars($bus['bus_number']); ?></td>
                        <td class="p-3 border border-gray-300"><?php echo htmlspecialchars($bus['contact_number']); ?></td>
                        <td class="p-3 border border-gray-300"><?php echo htmlspecialchars($bus['seats']); ?></td>
                        <td class="p-3 border border-gray-300"><?php echo htmlspecialchars($bus['bus_type']); ?></td>
                        <td class="p-3 border border-gray-300">
                            <button onclick="openPopup(<?php echo $bus['id']; ?>)" class="px-3 py-1 bg-green-500 text-white rounded-lg hover:bg-green-600">Request</button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Popup Form for Trip Request -->
    <div id="popupForm" class="fixed inset-0 hidden items-center justify-center bg-opacity-10 backdrop-blur-sm w-full h-full">
        <div class="bg-white p-5 rounded-lg shadow-lg w-full max-w-md">
            <h3 class="flex justify-center text-xl font-semibold text-[#4E71FF] w-full">Request a Trip</h3>

            <form method="POST" action="request_trip.php" class="mt-3">
                <input type="hidden" name="trip_bus_id" id="tripBusId">

                <div class="flex flex-col">
                    <label class="text-[#4E71FF] font-semibold">From:</label>
                    <input type="text" name="from" required class="p-2 border border-blue-500 rounded-xl outline-none" placeholder="Enter departure city (e.g., Kandy)">
                    <br />

                    <label class="text-[#4E71FF] font-semibold">To:</label>
                    <input type="text" name="to" required class="p-2 border border-blue-500 rounded-xl outline-none" placeholder="Enter destination city (e.g., Colombo)">
                    <br />

                    <label class="text-[#4E71FF] font-semibold">Date From:</label>
                    <!-- Changed type to text to work with flatpickr -->
                    <input type="text" name="date_from" id="dateFrom" required class="p-2 border border-blue-500 rounded-xl outline-none" placeholder="Select start date">
                    <br />

                    <label class="text-[#4E71FF] font-semibold">Date To:</label>
                    <!-- Changed type to text to work with flatpickr -->
                    <input type="text" name="date_to" id="dateTo" required class="p-2 border border-blue-500 rounded-xl outline-none" placeholder="Select end date">

                    <!-- Legend for Calendar -->
                    <div class="mt-3 flex gap-4">
                        <div class="flex items-center gap-2">
                            <span class="w-4 h-4 bg-[#fecaca] border border-[#b91c1c] rounded-sm"></span>
                            <span class="text-sm text-gray-700">Booked Dates</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-4 h-4 bg-gray-200 border border-gray-500 rounded-sm"></span>
                            <span class="text-sm text-gray-700">Past Dates</span>
                        </div>
                    </div>
                    <br />

                    <label class="text-[#4E71FF] font-semibold">Days:</label>
                    <input type="number" name="days" id="days" min="1" required readonly class="w-full p-2 border border-gray-300 rounded-lg outline-none bg-gray-100">
                    <br />

                    <div class="flex justify-between mt-4">
                        <button type="button" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 w-40" onclick="closePopup()">Cancel</button>
                        <button type="submit" name="request_trip" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 w-40">Send Request</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

</body>

</html>
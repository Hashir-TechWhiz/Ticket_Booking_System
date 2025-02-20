<?php
session_start();
include("../includes/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../user_login.php");
    exit();
}

if (!isset($_GET['bus_id']) || !isset($_GET['journey_date'])) {
    die("Invalid access!");
}

$bus_id = $_GET['bus_id'];
$journey_date = $_GET['journey_date'];

// Fetch bus details
$sql = "SELECT * FROM buses WHERE id = $bus_id";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    die("Bus not found");
}
$bus = $result->fetch_assoc();
$seat_price = $bus['price'];

// Fetch booked seats
$booked_seats = [];
$booked_sql = "SELECT seat_number FROM bookings WHERE bus_id = $bus_id AND journey_date = '$journey_date'";
$booked_result = $conn->query($booked_sql);
while ($row = $booked_result->fetch_assoc()) {
    $booked_seats[] = $row['seat_number'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Store selected seats in session
    $_SESSION['booking'] = [
        'bus_id' => $bus_id,
        'journey_date' => $journey_date,
        'seat_number' => $_POST['selected_seats']
    ];

    // Redirect to payment page
    header("Location: payment.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Seats</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex flex-col items-center justify-center min-h-screen bg-cover bg-center bg-no-repeat px-[6%] py-5" style="background-image: url('../assets/images/Bg.jpg');">
    <div class="relative flex justify-center items-center bg-white rounded-xl p-2 shadow-xl w-full">
        <a href="javascript:window.history.back();" class="absolute left-3 flex items-center gap-2 text-blue-500">
            <img src="../assets/icons/Back.png" alt="Back" class="w-5 h-5"> Back
        </a>

        <h2 class="flex items-center justify-center text-2xl font-semibold text-[#4E71FF] w-full">
            Book Your Seats
        </h2>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-lg mt-10">
        <p class="text-center text-gray-600 mb-2 text-xl">Bus Name: <strong><?php echo $bus['bus_name']; ?></strong></p>
        <p class="text-center text-gray-600 mb-4 text-xl">Bus Number: <strong><?php echo $bus['bus_number']; ?></strong></p>

        <form method="POST">
            <div class="bus-layout bg-gray-800 p-6 rounded-xl shadow-lg">
                <div class="driver-area bg-gray-700 text-white text-center py-3 rounded-t-lg mb-4 font-bold">
                    🚌 Driver's Cabin
                </div>

                <div class="seating-area flex flex-col gap-4">
                    <?php
                    $columns = 4;
                    $rows = ceil($bus['seats'] / $columns);

                    for ($row = 0; $row < $rows; $row++) : ?>
                        <div class="seat-row flex justify-center gap-2">
                            <?php for ($col = 0; $col < $columns; $col++) :
                                $seat_number = ($row * $columns) + $col + 1;
                                if ($seat_number > $bus['seats']) break;
                            ?>
                                <button type="button"
                                    class="seat w-10 h-10 flex items-center justify-center rounded-sm border-2 font-bold text-sm
                                    <?php echo in_array($seat_number, $booked_seats) ?
                                        'bg-gray-500 border-gray-600 cursor-not-allowed' :
                                        'bg-green-400 border-gray-300 hover:bg-green-600 text-gray-800'; ?>"
                                    data-seat="<?php echo $seat_number; ?>"
                                    <?php echo in_array($seat_number, $booked_seats) ? 'disabled' : ''; ?>>
                                    <?php echo $seat_number; ?>
                                </button>
                                <?php if ($col == 1) echo '<div class="aisle w-16 bg-gray-300 mx-2"></div>'; ?>
                            <?php endfor; ?>
                        </div>
                    <?php endfor; ?>
                </div>

                <div class="aisle-floor h-4 bg-gray-300 mt-4 rounded-b-lg"></div>
            </div>

            <!-- Seat Legend -->
            <div class="seat-legend mt-6 flex justify-center gap-4 text-sm">
                <div class="flex items-center gap-2">
                    <div class="w-5 h-5 bg-green-400 rounded-sm border-2 border-gray-300"></div>
                    <span>Available</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-5 h-5 bg-blue-500 rounded-sm border-2 border-gray-300"></div>
                    <span>Selected</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-5 h-5 bg-gray-500 rounded-sm border-2 border-gray-600"></div>
                    <span>Booked</span>
                </div>
            </div>

            <input type="hidden" name="bus_id" value="<?php echo $bus_id; ?>">
            <input type="hidden" name="journey_date" value="<?php echo $journey_date; ?>">
            <input type="hidden" name="selected_seats" id="selectedSeats">
            <p class="mt-4 text-center text-lg"><strong>Total Price: <span id="totalPrice">$0</span></strong></p>

            <button type="submit" id="proceedBtn" class="mt-4 w-full bg-blue-500 text-white p-3 rounded-lg hover:bg-blue-700 transition-colors duration-200" disabled>
                Proceed to Payment
            </button>
        </form>
    </div>

    <script>
        let selectedSeats = [];
        const seatPrice = <?php echo $seat_price; ?>;
        const seats = document.querySelectorAll(".seat");
        const totalPriceElem = document.getElementById("totalPrice");
        const selectedSeatsInput = document.getElementById("selectedSeats");
        const proceedBtn = document.getElementById("proceedBtn");

        seats.forEach(seat => {
            seat.addEventListener("click", function() {
                if (!this.classList.contains("bg-gray-500")) {
                    const seatNumber = this.dataset.seat;
                    if (this.classList.contains("bg-blue-500")) {
                        this.classList.remove("bg-blue-500", "text-white");
                        this.classList.add("bg-green-400", "text-gray-800");
                        selectedSeats = selectedSeats.filter(seat => seat !== seatNumber);
                    } else {
                        this.classList.remove("bg-green-400", "text-gray-800");
                        this.classList.add("bg-blue-500", "text-white");
                        selectedSeats.push(seatNumber);
                    }

                    selectedSeatsInput.value = selectedSeats.join(",");
                    totalPriceElem.textContent = `$${selectedSeats.length * seatPrice}`;
                    proceedBtn.disabled = selectedSeats.length === 0;
                }
            });
        });
    </script>
</body>

</html>
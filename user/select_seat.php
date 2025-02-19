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
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
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
            <div class="flex flex-col gap-3">
                <?php
                $columns = 4;
                $rows = ceil($bus['seats'] / $columns);

                for ($row = 0; $row < $rows; $row++) : ?>
                    <div class="flex justify-center gap-4">
                        <?php for ($col = 0; $col < $columns; $col++) :
                            $seat_number = ($row * $columns) + $col + 1;
                            if ($seat_number > $bus['seats']) break;
                        ?>
                            <button type="button"
                                class="seat w-12 h-12 flex items-center justify-center rounded-md border text-white font-bold
                                <?php echo in_array($seat_number, $booked_seats) ? 'bg-gray-500 cursor-not-allowed' : 'bg-green-500 hover:bg-green-700'; ?>"
                                data-seat="<?php echo $seat_number; ?>"
                                <?php echo in_array($seat_number, $booked_seats) ? 'disabled' : ''; ?>>
                                <?php echo $seat_number; ?>
                            </button>
                            <?php if ($col == 1) echo '<div class="w-8"></div>';
                            ?>
                        <?php endfor; ?>
                    </div>
                <?php endfor; ?>
            </div>

            <input type="hidden" name="bus_id" value="<?php echo $bus_id; ?>">
            <input type="hidden" name="journey_date" value="<?php echo $journey_date; ?>">
            <input type="hidden" name="selected_seats" id="selectedSeats">
            <p class="mt-4 text-center text-lg"><strong>Total Price: <span id="totalPrice">$0</span></strong></p>

            <button type="submit" id="proceedBtn" class="mt-4 w-full bg-blue-500 text-white p-2 rounded-md hover:bg-blue-700" disabled>
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
                        this.classList.remove("bg-blue-500");
                        this.classList.add("bg-green-500");
                        selectedSeats = selectedSeats.filter(seat => seat !== seatNumber);
                    } else {
                        this.classList.remove("bg-green-500");
                        this.classList.add("bg-blue-500");
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
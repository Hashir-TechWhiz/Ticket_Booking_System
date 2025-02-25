<?php
// user/payment.php
session_start();
include("../includes/db.php");
include("sendTicketMail.php");

if (!isset($_SESSION['user_id']) || !isset($_SESSION['booking'])) {
    header("Location: dashboard.php");
    exit();
}

$booking = $_SESSION['booking'];

// Retrieve bus details for the summary
$sql = "SELECT id, bus_number, price, time FROM buses WHERE id = " . $booking['bus_id'];
$result = $conn->query($sql);

if (!$result || $result->num_rows == 0) {
    die("Error: Bus not found.");
}

$bus = $result->fetch_assoc();
$seat_price = $bus['price'];
$departure_time = $bus['time']; // Fetch departure time

// Convert selected seats string into an array
$selected_seats = explode(",", $booking['seat_number']);
$total_price = count($selected_seats) * $seat_price;

if (isset($_POST['confirm_payment'])) {
    $user_id = $_SESSION['user_id'];
    $bus_id = $booking['bus_id'];
    $journey_date = $booking['journey_date'];

    foreach ($selected_seats as $seat_number) {
        $sql = "INSERT INTO bookings (user_id, bus_id, seat_number, journey_date, payment_status)
                VALUES ($user_id, $bus_id, $seat_number, '$journey_date', 'Confirmed')";
        if (!$conn->query($sql)) {
            $error = "Error: " . $conn->error;
            break;
        }
    }

    if (!isset($error)) {
        // Fetch user email
        $user_query = "SELECT email FROM users WHERE id = $user_id";
        $user_result = $conn->query($user_query);
        $user = $user_result->fetch_assoc();
        $user_email = $user['email'];

        // Prepare email content
        $subject = "Your Bus Ticket Confirmation";
        $body = "
        <h2>E-Ticket</h2>
        <p><strong>Bus Number:</strong> {$bus['bus_number']}</p>
        <p><strong>Seats:</strong> " . implode(", ", $selected_seats) . "</p>
        <p><strong>Journey Date:</strong> $journey_date</p>
        <p><strong>Departure Time:</strong> $departure_time</p>
        <p><strong>Total Price:</strong> $$total_price</p>
        <p>Thank you for booking with us!</p>
        <p>Need help finding your way? 🗺️ Explore the world with <a href='https://www.google.com/maps' style='color: #1a73e8; text-decoration: none; font-weight:   bold;'>Google Maps</a> and make your journey even smoother!</p>
";
        // Send Email
        if (sendTicketEmail($user_email, $subject, $body)) {
            unset($_SESSION['booking']);
            header("Location: dashboard.php?success=1");
            exit();
        } else {
            $error = "Payment confirmed, but failed to send email.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Payment Portal</title>
    <script src="../assets/js/cardVal.js"></script>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>

<body class="relative flex flex-col justify-start items-center min-h-screen bg-cover bg-center bg-no-repeat px-[6%] py-5"
    style="background-image: url('../assets/images/Bg.jpg');">

    <div class="relative flex justify-center items-center bg-white rounded-xl p-2 shadow-xl w-full">
        <a href="javascript:window.history.back();" class="absolute left-3 flex items-center gap-2 text-blue-500">
            <img src="../assets/icons/Back.png" alt="Back" class="w-5 h-5"> Back
        </a>

        <h2 class="flex items-center justify-center text-2xl font-semibold text-[#4E71FF] w-full">
            Booking Details & Payment
        </h2>
    </div>

    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-lg transition-all duration-300 hover:shadow-xl mt-5">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Payment Confirmation</h2>
            <div class="w-20 h-1 bg-blue-500 rounded-full mx-auto"></div>
        </div>

        <?php if (isset($error)) {
            echo "<div class='bg-red-50 p-3 rounded-lg mb-6 border border-red-200'>
                    <p class='text-red-600 text-center text-sm'>$error</p></div>";
        } ?>

        <!-- Booking Summary -->
        <div class="bg-gray-50 p-6 rounded-xl mb-8 border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Booking Summary</h3>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Bus Number:</span>
                    <span class="text-gray-800 font-medium"><?php echo $bus['bus_number']; ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Selected Seats:</span>
                    <span class="text-gray-800 font-medium"><?php echo implode(", ", $selected_seats); ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Journey Date:</span>
                    <span class="text-gray-800 font-medium"><?php echo $booking['journey_date']; ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Departure Time:</span>
                    <span class="text-gray-800 font-medium"><?php echo $departure_time; ?></span>
                </div>
                <div class="pt-4 mt-4 border-t border-gray-200">
                    <div class="flex justify-between">
                        <span class="text-lg font-bold text-gray-800">Total Amount:</span>
                        <span class="text-xl font-bold text-blue-600">$<?php echo $total_price; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment Form -->
        <form method="POST" action="" onsubmit="return validatePaymentForm()">
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cardholder Name</label>
                    <input type="text" name="cardholder_name" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                        placeholder="John Doe"
                        pattern="[A-Za-z ]+" title="Only letters and spaces allowed">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Card Number</label>
                    <input type="text" name="card_number" id="cardNumber" required
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                        placeholder="4242 4242 4242 4242"
                        maxlength="19"
                        oninput="formatCardNumber(this)">
                    <span id="cardError" class="text-red-500 text-sm hidden">Invalid card number</span>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Date</label>
                        <input type="text" name="expiry_date" id="expiryDate" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                            placeholder="MM/YY"
                            maxlength="5"
                            oninput="formatExpiryDate(this)">
                        <span id="expiryError" class="text-red-500 text-sm hidden">Invalid expiry date</span>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">CVV</label>
                        <input type="text" name="cvv" id="cvv" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all"
                            placeholder="123"
                            maxlength="3"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        <span id="cvvError" class="text-red-500 text-sm hidden">Invalid CVV</span>
                    </div>
                </div>

                <button type="submit" name="confirm_payment"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3.5 rounded-lg transition-all duration-300 transform hover:scale-[1.01] shadow-md hover:shadow-lg">
                    Confirm Payment
                </button>
            </div>
        </form>
    </div>
</body>

</html>
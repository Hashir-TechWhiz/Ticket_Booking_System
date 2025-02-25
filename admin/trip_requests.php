<?php
session_start();
include("../includes/db.php");
include("send_email.php");

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../admin_login.php");
    exit();
}

$admin_id = $_SESSION['admin_id'];
$sql = "SELECT tr.*, u.name AS user_name, u.email AS user_email, tb.bus_name, tb.bus_number 
        FROM trip_requests tr 
        JOIN users u ON tr.user_id = u.id 
        JOIN trip_buses tb ON tr.trip_bus_id = tb.id 
        WHERE tb.admin_id = $admin_id ORDER BY tr.request_date DESC";
$result = $conn->query($sql);

if (isset($_GET['action']) && isset($_GET['id'])) {
    $request_id = $_GET['id'];
    $action = $_GET['action'];

    if ($action == 'approve') {
        $status = 'Approved';
        $adminMessage = "Your trip request has been approved! 🎉 Please contact us at 123-456-7890 to confirm your booking and discuss pricing details.";
        // Fetch approved request details to use for updating same-day requests
        $approvedQuery = "SELECT trip_bus_id, date_from, date_to FROM trip_requests WHERE id = $request_id";
        $approvedResult = $conn->query($approvedQuery);
        if ($approvedResult && $approvedResult->num_rows > 0) {
            $approvedRequest = $approvedResult->fetch_assoc();
            $trip_bus_id = $approvedRequest['trip_bus_id'];
            $date_from = $approvedRequest['date_from'];
            $date_to = $approvedRequest['date_to'];
        }
    } elseif ($action == 'reject') {
        $status = 'Rejected';
        $adminMessage = "We regret to inform you that the requested bus is unavailable for your selected dates. 😔 Please try alternative dates or contact us for other options.";
    }

    $update_sql = "UPDATE trip_requests SET status = '$status' WHERE id = $request_id";
    if ($conn->query($update_sql) === TRUE) {
        // Additional logic for same-day requests: if a request is approved, automatically reject overlapping pending requests.
        if ($action == 'approve') {
            $otherUpdate_sql = "UPDATE trip_requests SET status = 'Rejected' WHERE trip_bus_id = $trip_bus_id AND date_from = '$date_from' AND date_to = '$date_to' AND status = 'Pending' AND id != $request_id";

            if ($conn->query($otherUpdate_sql) === TRUE) {
                // Fetch those other requests and send rejection emails.
                $otherQuery = "SELECT tr.id, u.email, u.name FROM trip_requests tr JOIN users u ON tr.user_id = u.id WHERE tr.trip_bus_id = $trip_bus_id AND tr.date_from = '$date_from' AND tr.date_to = '$date_to' AND tr.status = 'Rejected' AND tr.id != $request_id";

                $otherResult = $conn->query($otherQuery);
                if ($otherResult && $otherResult->num_rows > 0) {
                    while ($other = $otherResult->fetch_assoc()) {
                        sendEmail($other['email'], $other['name'], 'Rejected', "We regret to inform you that the requested bus is unavailable for your selected dates. 😔 Please try alternative dates or contact us for other options.");
                    }
                }
            }
        }

        // Fetch user email for the request that was directly approved/rejected.
        $user_query = "SELECT u.email, u.name FROM users u JOIN trip_requests tr ON u.id = tr.user_id WHERE tr.id = $request_id";
        $user_result = $conn->query($user_query);
        if ($user_result && $user_result->num_rows > 0) {
            $user = $user_result->fetch_assoc();
            $toEmail = $user['email'];
            $toName = $user['name'];

            // Send email for the action taken on the clicked request.
            sendEmail($toEmail, $toName, $status, $adminMessage);
        }

        header("Location: trip_requests.php");
        exit();
    } else {
        $error = "Error updating request status: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Trip Requests</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>

<body class="relative flex flex-col items-center justify-start min-h-screen bg-cover bg-center bg-no-repeat px-[6%] py-5" style="background-image: url('../assets/images/AdminBg.jpg');">

    <div class="relative flex justify-center items-center bg-white rounded-xl p-2 shadow-xl w-full">
        <a href="javascript:window.history.back();" class="absolute left-3 flex items-center gap-2 text-blue-500">
            <img src="../assets/icons/Back.png" alt="Back" class="w-5 h-5"> Back
        </a>

        <h2 class="flex items-center justify-center text-2xl font-semibold text-[#4E71FF] w-full">
            Trip Request Management
        </h2>
    </div>

    <div class="w-full">

        <!-- Error Message -->
        <?php if (isset($error)) { ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4">
                <?php echo $error; ?>
            </div>
        <?php } ?>

        <!-- Table -->
        <?php if ($result->num_rows > 0) { ?>
            <div class="bg-white rounded-xl shadow-md overflow-hidden mt-10">
                <div class="overflow-x-auto max-h-[700px] overflow-y-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 top-0 z-10 sticky">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bus Details</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Route</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dates</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Request Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php while ($request = $result->fetch_assoc()) { ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium text-gray-900"><?php echo $request['user_name']; ?></div>
                                        <div class="text-sm text-gray-500"><?php echo $request['user_email']; ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-medium"><?php echo $request['bus_name']; ?></div>
                                        <div class="text-sm text-gray-500">#<?php echo $request['bus_number']; ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <span class="font-medium"><?php echo $request['route_from']; ?></span>
                                            <span class="mx-2 text-gray-400">→</span>
                                            <span class="font-medium"><?php echo $request['route_to']; ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm">
                                            <div><?php echo substr($request['date_from'], 0, 10); ?></div>
                                            <div class="text-gray-400">to</div>
                                            <div><?php echo substr($request['date_to'], 0, 10); ?></div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                                            <?php echo $request['days']; ?> days
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php $statusColor = $request['status'] === 'Approved' ? 'green' : ($request['status'] === 'Rejected' ? 'red' : 'yellow'); ?>
                                        <span class="px-3 py-1 bg-<?php echo $statusColor; ?>-100 text-<?php echo $statusColor; ?>-800 rounded-full text-sm">
                                            <?php echo $request['status']; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo substr($request['request_date'], 0, 10); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <?php if ($request['status'] == 'Pending') { ?>
                                            <div class="flex space-x-2">
                                                <a href="?action=approve&id=<?php echo $request['id']; ?>"
                                                    class="text-green-600 hover:text-green-900 transition-colors text-xl"
                                                    title="Approve Request">
                                                    <i class="fas fa-check-circle"></i>
                                                </a>
                                                <a href="?action=reject&id=<?php echo $request['id']; ?>"
                                                    class="text-red-600 hover:text-red-900 transition-colors text-xl"
                                                    onclick="return confirm('Are you sure you want to reject this request?')"
                                                    title="Reject Request">
                                                    <i class="fas fa-times-circle"></i>
                                                </a>
                                            </div>
                                        <?php } else { ?>
                                            <span class="text-gray-400">-</span>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php } else { ?>
            <div class="bg-white p-6 rounded-xl shadow-md text-center text-gray-500 mt-10">
                <i class="fas fa-info-circle text-2xl mb-2"></i>
                <p>No trip requests found.</p>
            </div>
        <?php } ?>
    </div>
</body>

</html>
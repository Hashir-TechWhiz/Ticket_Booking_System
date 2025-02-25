<?php
// get_booked_dates.php
include("../includes/db.php");

$bus_id = $_GET['bus_id'];
$dates = array();

$sql = "SELECT date_from, date_to FROM trip_requests 
        WHERE trip_bus_id = '$bus_id' AND status = 'Approved'";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $start = new DateTime($row['date_from']);
    $end = new DateTime($row['date_to']);

    while ($start <= $end) {
        $dates[] = $start->format('Y-m-d');
        $start->modify('+1 day');
    }
}

echo json_encode(array_unique($dates));
?>

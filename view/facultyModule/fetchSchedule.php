<?php
include "../../model/dbconnection.php";

$sql = "SELECT * FROM schedules";
$result = $con->query($sql);

$schedule = [];
if ($result) { // Check if the query was successful
    while ($row = $result->fetch_assoc()) {
        $schedule[] = $row;
    }
    echo json_encode($schedule);
} else {
    // Return an error message
    http_response_code(500); // Internal Server Error
    echo json_encode(["error" => "Database query failed: " . $con->error]);
}

$con->close();
?>
<?php
include "../../model/dbconnection.php";

$sql = "SELECT * FROM schedules";
$result = $con->query($sql);

$schedule = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $schedule[] = $row;
    }
    echo json_encode($schedule);
} else {

    http_response_code(500);
    echo json_encode(["error" => "Database query failed: " . $con->error]);
}

$con->close();
?>
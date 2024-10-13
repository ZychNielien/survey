<?php

session_start();
include "../model/dbconnection.php";

if (isset($_POST['stime'])) {

    $stime = $_POST['stime'];
    $s = $stime + 1;
    $e = $stime + 5;

    $query_Etime = "SELECT time_id, time FROM time WHERE time_id BETWEEN '$s' AND '$e' AND time_id NOT IN (SELECT T.time_id FROM time T INNER JOIN assigned_subject A 
                    WHERE A.section_id = '$section' AND A.day_id = '$day' AND T.time_id BETWEEN A.S_time_id AND A.E_time_id)";

    $query_Etime_run = mysqli_query($con, $query_Etime);

    $check_Etime = mysqli_num_rows($query_Etime_run) > 0;

    if ($check_Etime) {
        echo '<option value="selected" selected disabled>---Select end time---</option>';
        while ($row_Etime = mysqli_fetch_array($query_Etime_run)) {

            echo '<option value="' . $row_Etime['time_id'] . '">' . $row_Etime['time'] . '</option>';

        }
    } else {
        echo '<option value="">No end times available</option>';
    }
}
<?php

session_start();
include "../model/dbconnection.php";

if (isset($_POST['Etime'])) {

    $Etime = $_POST['Etime'];
    $Stime = $_POST['Stime'];
    $day = $_POST['day'];
    $section = $_POST['section'];
    
    $query_Stime = "SELECT time_id, time FROM time WHERE time_id NOT IN (SELECT T.time_id FROM time T INNER JOIN assigned_subject A 
                    WHERE A.section_id = '$section' AND A.day_id = '$day' AND T.time_id BETWEEN A.S_time_id AND A.E_time_id) 
                    AND time_id NOT IN (SELECT time_id FROM time WHERE time_id BETWEEN '$Stime' AND '$Etime') ORDER BY time_id ASC";

    $query_Stime_run = mysqli_query($con, $query_Stime);

    $check_Stime = mysqli_num_rows($query_Stime_run) > 0;

    if ($check_Stime) {
        echo '<option value="selected" selected disabled>---Select start time---</option>';
        while ($row_Stime = mysqli_fetch_array($query_Stime_run)) {

          echo  '<option value="' .$row_Stime['time_id']. '">' .$row_Stime['time']. '</option>';

        }
    } else {
        echo '<option value="">No start times available</option>';
    }
}
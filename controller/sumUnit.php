<?php
session_start();
include "../model/dbconnection.php";
 
$srcode = $_GET['srcode'];

$query = "SELECT SUM(S.unit) AS TotalUnits FROM `enrolled_student` E INNER JOIN subject S ON E.subject_id = S.subject_id WHERE E.sr_code = '$srcode'";
$query_run = mysqli_query($con, $query);

        $data = array();

        if(mysqli_num_rows($query_run) > 0){
            while($row = mysqli_fetch_assoc($query_run)){
                $data[] = $row;
            }
        }
        echo json_encode($data);
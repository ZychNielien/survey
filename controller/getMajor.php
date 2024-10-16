<?php

session_start();
include "../model/dbconnection.php";

if (isset($_GET['srcode'])) {

    $srcode = $_GET['srcode'];

    $query = "SELECT COALESCE((SELECT major FROM student_major WHERE sr_code = '$srcode'), 0) AS major";
    $query_run = mysqli_query($con, $query);

    
    $data = array();

    if(mysqli_num_rows($query_run) > 0){
        while($row = mysqli_fetch_assoc($query_run)){
            $data[] = $row;
        }
    }
    echo json_encode($data);

}

<?php

session_start();
include "../model/dbconnection.php";


    $query = "SELECT semester FROM academic_year_semester";
    $query_run = mysqli_query($con, $query);

    
    $data = array();

    if(mysqli_num_rows($query_run) > 0){
        while($row = mysqli_fetch_assoc($query_run)){
            $data[] = $row;
        }
    }
    echo json_encode($data);

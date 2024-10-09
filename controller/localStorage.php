<?php
session_start();
include "../model/dbconnection.php";

    

        $srcode = $_GET['studentSRCode'];

        $query = "SELECT SBI.sr_code, SBI.lastname, SBI.firstname, SBI.email, SBI.contact, YL.year_level, S.status, SS.section, SS.course, SM.semester 
                  FROM student_basic_info SBI INNER JOIN student_status SS ON SBI.sr_code = SS.sr_code INNER JOIN status S ON SS.status_id = S.id 
                  INNER JOIN year_level YL ON SS.year_level = YL.year_id INNER JOIN semester SM ON SS.sem_id = SM.sem_id WHERE SBI.sr_code = '$srcode'";

        $query_run = mysqli_query($con, $query);

        $data = array();

        if(mysqli_num_rows($query_run) > 0){
            while($row = mysqli_fetch_assoc($query_run)){
                $data[] = $row;
            }
        }
        echo json_encode($data);
    
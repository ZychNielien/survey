<?php

include "../model/dbconnection.php";
if(isset($_GET['subject_id'])){
        $subID = $_GET['subject_id'];
        $srcode = $_GET['srcode'];

        $query = "WITH sec_count AS (
                    SELECT 
                        COUNT(section_id) AS SLOT, 
                        subject_id 
                            FROM enrolled_student 
                            GROUP BY subject_id),

                  unit_count AS (
                    SELECT 
                        SUM(S.unit) AS TotalUnits
                        , E.subject_id
                        FROM `enrolled_student` E 
                        INNER JOIN subject S 
                        ON E.subject_id = S.subject_id 
                        WHERE E.sr_code = '$srcode')

                    SELECT 
                        A.slot, 
                        COALESCE(SC.SLOT, 0) AS total_slot,
                        U.TotalUnits
                    FROM assigned_subject A 
                        LEFT JOIN sec_count SC 
                            ON A.subject_id = SC.subject_id 
                        LEFT JOIN unit_count U
                            ON A.subject_id = U.subject_id
                        WHERE A.id = $subID";

        $query_run = mysqli_query($con, $query);

        $data = array();

        if(mysqli_num_rows($query_run) > 0){
            while($row = mysqli_fetch_assoc($query_run)){
                $data[] = $row;
            }
        }
        echo json_encode($data);
}
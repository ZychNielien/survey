<?php

include "../model/dbconnection.php";
if (isset($_GET['year_level'])) {
    $yearlvl = $_GET['year_level'];
    $srcode = $_GET['srcode'];
    $sem = $_GET['semester'];

    $query = "WITH sec_count AS (
                    SELECT 
                        COUNT(section_id) AS SLOT, 
                        subject_id 
                    FROM enrolled_student 
                    GROUP BY subject_id
                ) 
                SELECT 
                    A.id, 
                    S.subject_code, 
                    S.subject, 
                    S.unit, 
                    SC.section, 
                    I.lastname,
                    A.slot AS max_slot, 
                    I.firstname, 
                    COALESCE(SCO.SLOT, 0) AS slot,
                    D.days, 
                    TS.time AS startTime, 
                    TE.time AS endTime, 
                    COALESCE(D2.days, 'N/A') AS Day2, 
                    COALESCE(TS2.time, 'N/A') AS startTime2, 
                    COALESCE(TE2.time, 'N/A') AS endTime2
                FROM assigned_subject A 
                INNER JOIN subject S 
                    ON A.subject_id = S.subject_id 
                INNER JOIN instructor I 
                    ON A.faculty_id = I.faculty_id 
                INNER JOIN section SC 
                    ON A.section_id = SC.id 
                INNER JOIN year_level YL 
                    ON S.year = YL.year_id 
                LEFT JOIN sec_count SCO 
                    ON A.subject_id = SCO.subject_id 
                INNER JOIN days D 
                    ON A.day_id = D.day_id 
                INNER JOIN time TS 
                    ON A.S_time_id = TS.time_id 
                INNER JOIN time TE 
                    ON A.E_time_id = TE.time_id 
                LEFT JOIN days D2 
                    ON A.day_id_2 = D2.day_id
                LEFT JOIN time TS2
                    ON A.S_time_id_2 = TS2.time_id
                LEFT JOIN time TE2
                    ON A.E_time_id_2 = TE2.time_id
                INNER JOIN semester SE
                    ON S.semester = SE.sem_id
                LEFT JOIN prereq_subject PS
                    ON A.subject_id = PS.subject_id
                LEFT JOIN failed_subject FS
                    ON PS.prereq_id = FS.subject_id AND FS.sr_code = '$srcode'
                WHERE YL.year_level = '$yearlvl' 
                    AND SE.semester = '$sem'
                    AND A.subject_id NOT IN (
                        SELECT subject_id 
                        FROM enrolled_student 
                        WHERE sr_code = '$srcode'
                    )
                    AND FS.subject_id IS NULL
            UNION ALL
                SELECT A.id, 
                    S.subject_code, 
                    S.subject, 
                    S.unit, 
                    SC.section, 
                    I.lastname,
                    A.slot AS max_slot, 
                    I.firstname, 
                    COALESCE(SCO.SLOT, 0) AS slot,
                    D.days, 
                    TS.time AS startTime, 
                    TE.time AS endTime, 
                    COALESCE(D2.days, 'N/A') AS Day2, 
                    COALESCE(TS2.time, 'N/A') AS startTime2, 
                    COALESCE(TE2.time, 'N/A') AS endTime2
                    FROM failed_subject F 
                    INNER JOIN subject S 
                    	ON F.subject_id = S.subject_id 
                    INNER JOIN assigned_subject A
                    	ON F.subject_id = A.subject_id
                    INNER JOIN instructor I 
                    	ON A.faculty_id = I.faculty_id 
                    INNER JOIN section SC 
                    	ON A.section_id = SC.id 
                    INNER JOIN year_level YL 
                        ON S.year = YL.year_id 
                    LEFT JOIN sec_count SCO 
                        ON A.subject_id = SCO.subject_id 
                    INNER JOIN days D 
                        ON A.day_id = D.day_id 
                    INNER JOIN time TS 
                        ON A.S_time_id = TS.time_id 
                    INNER JOIN time TE 
                        ON A.E_time_id = TE.time_id 
                    LEFT JOIN days D2 
                        ON A.day_id_2 = D2.day_id
                    LEFT JOIN time TS2
                        ON A.S_time_id_2 = TS2.time_id
                    LEFT JOIN time TE2
                        ON A.E_time_id_2 = TE2.time_id
                    INNER JOIN semester SE
                        ON S.semester = SE.sem_id
                    WHERE F.sr_code = '$srcode'
                    AND SE.semester = '$sem';";

    $query_run = mysqli_query($con, $query);

    $data = array();

    if (mysqli_num_rows($query_run) > 0) {
        while ($row = mysqli_fetch_assoc($query_run)) {
            $data[] = $row;
        }
    }
    echo json_encode($data);
}
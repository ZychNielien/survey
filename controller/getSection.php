<?php
session_start();
include "../model/dbconnection.php";

if(isset($_POST['sub_id'])){
    $sub_id = $_POST['sub_id'];

$query_sec = "SELECT SE.id, SE.section FROM subject SU 
                INNER JOIN section SE 
                ON SU.year = SE.year_id 
                AND SU.semester = SE.sem_id
                WHERE SU.subject_id = '$sub_id' AND SE.id NOT IN (SELECT section_id FROM assigned_subject WHERE subject_id = '$sub_id') ORDER BY section ASC";

$query_sec_run = mysqli_query($con, $query_sec);

$check_sec = mysqli_num_rows($query_sec_run) > 0;

if ($check_sec) {
    echo '<option value="selected" selected disabled>---Select Section---</option>';
    
    while ($row_sec = mysqli_fetch_array($query_sec_run)) {

        echo '<option value="' .$row_sec['id']. '">' .$row_sec['section']. '</option>';

    }
} else {
    echo '<option value="">No sections found</option>';
}
}

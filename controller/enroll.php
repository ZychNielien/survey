<?php
session_start();
include "../model/dbconnection.php";

if(isset($_POST['id']) && ($_POST['srcode'])){
    $id = $_POST['id'];
    $srcode = $_POST['srcode'];

    $query = "INSERT INTO enrolled_student (subject_id, sr_code, section_id) VALUES ((SELECT subject_id FROM assigned_subject WHERE id = '$id'), '$srcode', (SELECT section_id FROM assigned_subject WHERE id = '$id'));";
    $query_run = mysqli_query($con, $query);

}
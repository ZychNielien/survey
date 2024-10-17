<?php

session_start();
include "../model/dbconnection.php";

if (isset($_POST['course'])){
    $course = $_POST['course'];
    $name = $_POST['name'];
    $room = $_POST['room'];
    $faculty = $_POST['faculty'];
    $date = $_POST['date'];
    $stime = $_POST['stime'];
    $etime = $_POST['etime'];
    $slot = $_POST['slot'];
    $status = $_POST['status'];
    

    $query = "INSERT INTO sched_observation (course, name, room, faculty_id, date, stime, etime, slot, eval_status) VALUES ('$course', '$name', '$room', '$faculty', '$date', '$stime', '$etime', '$slot', '$status')";

    $query_run = mysqli_query($con, $query);
}
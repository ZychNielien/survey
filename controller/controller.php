<?php

session_start();
include "../model/dbconnection.php";

// ADD SUBJECTS 
if (isset($_POST['submit_sub'])) {
    $subject = $_POST['subject'];
    $unit = $_POST['unit'];
    $code = $_POST['code'];
    $sem = $_POST['sem'];
    $year = $_POST['year'];


    $query = "INSERT INTO subject (`subject`, `unit`, `year`, `semester`, `subject_code`) VALUES ('$subject', '$unit', '$year','$sem','$code')";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['status'] = "New Subject has been added Successfully";
        $_SESSION['status-code'] = "success";
        header("location:../view/test.php");
    } else {
        $_SESSION['status'] = "Something is wrong";
        $_SESSION['status-code'] = "error";
        header("location:../view/test.php");
    }
}

// ADD SECTION 
if (isset($_POST['submit_section'])) {
    $section = $_POST['addsection'];
    $year = $_POST['year'];
    $sem = $_POST['sem'];


    $query = "INSERT INTO section (`section`, `year_id`, `sem_id`) VALUES ('$section', '$year', '$sem')";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['status'] = "New Section has been added Successfully";
        $_SESSION['status-code'] = "success";
        header("location:../view/test.php");
    } else {
        $_SESSION['status'] = "Something is wrong";
        $_SESSION['status-code'] = "error";
        header("location:../view/test.php");
    }
}

// ASSIGNED INSTRUCTORS
if (isset($_POST['submit_assigned'])) {
    $sub_id = $_POST['sub_id'];
    $fclty_id = $_POST['fclty_id'];
    $sec_id = $_POST['sec_id'];
    $day = $_POST['day'];
    $Stime = $_POST['Stime'];
    $Etime = $_POST['Etime'];
    $day2 = $_POST['day2'];
    $Stime2 = $_POST['Stime2'];
    $Etime2 = $_POST['Etime2'];
    $slot = $_POST['slot'];



    $query = "INSERT INTO assigned_subject (`subject_id`, `faculty_id`, `section_id`, `day_id`, `S_time_id`, `E_time_id`, `day_id_2`, `S_time_id_2`, `E_time_id_2`, `slot`) VALUES ('$sub_id', '$fclty_id', '$sec_id', '$day', '$Stime', '$Etime', '$day2', '$Stime2', '$Etime2', '$slot')";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['status'] = "Save Successfully";
        $_SESSION['status-code'] = "success";
        header("location:../view/test.php");
    } else {
        $_SESSION['status'] = "Something is wrong";
        $_SESSION['status-code'] = "error";
        header("location:../view/test.php");
    }
}

//major input
if(isset($_POST['submitMajor'])){

    $srcode = $_POST['srcode'];
    $major = $_POST['major'];

    $query = "INSERT INTO student_major (sr_code, major) VALUES ('$srcode', '$major')";
    $query_run = mysqli_query($con, $query);

    if ($query_run) {
        $_SESSION['status'] = $major. " has been selected";
        $_SESSION['status-code'] = "success";
        header("location:../view/student_view.php");
    } else {
        $_SESSION['status'] = "Something is wrong";
        $_SESSION['status-code'] = "error";
        header("location:../view/student_view.phpstudent_view.php");
    }

}



// SUBMIT EVALUATION
// if (isset($_POST['submit_evaluation'])) {
//     $count = COUNT($_POST['qu_id']);
//     $qu_id = $_POST['qu_id'];
//     $sr_code = $_POST['sr-code'];
//     $facultyID = $_POST['facultyID'];
//     //  TEACHING EFFECTIVENESS
//     $TE1 = $_POST['1TE'];
//     $TE2 = $_POST['2TE'];
//     $TE3 = $_POST['3TE'];
//     $TE4 = $_POST['4TE'];
//     $TE5 = $_POST['5TE'];
//     $TE6 = $_POST['6TE'];

//     //  CLASSROOM MANAGEMENT
//     $CM7 = $_POST['7CM'];
//     $CM8 = $_POST['8CM'];
//     $CM9 = $_POST['9CM'];
//     $CM10 = $_POST['10CM'];
//     $CM11 = $_POST['11CM'];

//     //  CLASSROOM MANAGEMENT
//     $SE12 = $_POST['12SE'];
//     $SE13 = $_POST['13SE'];
//     $SE14 = $_POST['14SE'];
//     $SE15 = $_POST['15SE'];
//     $SE16 = $_POST['16SE'];

//     //  COMMUNICATION
//     $C17 = $_POST['17C'];
//     $C18 = $_POST['18C'];
//     $C19 = $_POST['19C'];
//     $C20 = $_POST['20C'];
//     $C21 = $_POST['21C'];

//     //  EMOTIONAL COMPETENCE
//     $EC22 = $_POST['22EC'];
//     $EC23 = $_POST['23EC'];
//     $EC24 = $_POST['24EC'];

//     $q_array = array($TE1, $TE2, $TE3, $TE4, $TE5, $TE6, $CM7, $CM8, $CM9, $CM10, $CM11, $SE12, $SE13, $SE14, $SE15, $SE16, $C17, $C18, $C19, $C20, $C21, $EC22, $EC23, $EC24);

//     var_dump($count);
//     var_dump($qu_id);
//     var_dump($q_array);
//     var_dump($sr_code);
//     var_dump($facultyID);

//     for ($i = 0; $i < $count; $i++) {
//         $query = "INSERT INTO evaluation (question_id, sr_code, faculty_id, rate) VALUES ('$qu_id[$i]', '$sr_code[$i]', '$facultyID[$i]', '$q_array[$i]')";
//         $query_run = mysqli_query($con, $query);

//         if ($query_run) {
//             $_SESSION['status'] = "Sent Successfully";
//             $_SESSION['status-code'] = "success";
//             header("location:../view/test.php");
//         } else {
//             $_SESSION['status'] = "Something is wrong";
//             $_SESSION['status-code'] = "error";
//             header("location:../view/test.php");
//         }
//     }
// }
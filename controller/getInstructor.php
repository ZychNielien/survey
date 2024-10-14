<?php

session_start();
include "../model/dbconnection.php";

if (isset($_POST['sub_id'])) {

    $sub_id = $_POST['sub_id'];

$query_fclty = "SELECT faculty_id, last_name, first_name FROM instructor";

    $query_fclty_run = mysqli_query($con, $query_fclty);

    $check_fclty = mysqli_num_rows($query_fclty_run) > 0;

    if ($check_fclty) {
        echo '<option value="selected" selected disabled>---Select Instructor---</option>';
        while ($row_fclty = mysqli_fetch_array($query_fclty_run)) {

        echo '<option value="' .$row_fclty['faculty_id']. '">' .$row_fclty['last_name']. ', ' .$row_fclty['first_name']. '</option>';

        }
    } else {
        echo '<option value="">No Instructor available</option>';
    }

}

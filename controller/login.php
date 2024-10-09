<?php

include "../model/dbconnection.php";

session_start(); 

    if(isset($_POST['studentLogin'])) {
        $studentSRCode = $_POST['studentSRCode'];
        $studentPass = $_POST['studentpass'];

        $sql = "SELECT * FROM studentlogin WHERE srcode = '$studentSRCode' AND password = '$studentPass'";

        $sql_query = mysqli_query($con, $sql);

        if($sql_query) {
            $numrows = mysqli_num_rows($sql_query);
            if($numrows == 1) {
                $_SESSION['status'] = "Login Successfully";
                $_SESSION['status-code'] = "success";
                while($row = mysqli_fetch_assoc($sql_query)) {

                    $_SESSION["studentSRCode"] = $row["srcode"]; 
                }

                    header("location: ../view/student_view.php");
                }else {
                    $_SESSION['status'] = "Authentication Failed";
                    $_SESSION['status-code'] = "error";
                    header("location: ../view/loginModule/index.php");
                }
        }else {
            die(mysqli_error($con));
        }
    }
    

?>

<?php
    session_start();

    include "../model/dbconnection.php";

    $studSrCode = $_SESSION["studentSRCode"];
    echo $studSrCode;

    $user = mysqli_query($con, "SELECT * FROM studentlogin WHERE srcode = '$studSrCode'");
    $dataUser = mysqli_fetch_array($user);

    if(isset($_POST['changePass'])) {
        $oldPassword = $_POST['oldpass'];
        $newPassword = $_POST['newpass'];
        $confirmPassword = $_POST['conpass'];

        function validate($data){
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }

        $old = validate($_POST['oldpass']);
        $new = validate($_POST['newpass']);
        $confirm = validate($_POST['conpass']);

        if(empty($old)) {
            $_SESSION['status'] = "Old Password Required";
            $_SESSION['status-code'] = "error";
            header('location:../view/student_view.php');
            exit();
        }else if($old !== $dataUser['password']){
            $_SESSION['status'] = "Incorrect old password";
            $_SESSION['status-code'] = "error";
            header('location:../view/student_view.php');
            exit();
        }else if(empty($new)){
            $_SESSION['status'] = "New Password Required";
            $_SESSION['status-code'] = "error";
            header('location:../view/student_view.php');
            exit();
        }else if($new !== $confirm){
            $_SESSION['status'] = "New password does not match";
            $_SESSION['status-code'] = "error";
            header('location:../view/student_view.php');
            exit();
        }else {

            $sql = "UPDATE studentlogin SET password = '$new' WHERE srcode = '$studSrCode' ";
            $sql_query = mysqli_query($con, $sql);

            if($sql_query) {
                $_SESSION['status'] = "Successfully changed the password";
                $_SESSION['status-code'] = "success";
                header('location:../view/student_view.php');
                exit();
            }else {
                $_SESSION['status'] = "Incorrect password";
                $_SESSION['status-code'] = "error";
                header('location:../view/student_view.php');
                exit();
            } 
        }
    }else {
        $_SESSION['status'] = "ERROR.";
        $_SESSION['status-code'] = "error";
        header('location:../view/student_view.php');
        exit();
    }

?>

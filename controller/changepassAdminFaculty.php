<?php
session_start();
include "../model/dbconnection.php";

$facultyID = $_SESSION["userid"];
$user = mysqli_query($con, "SELECT * FROM instructor WHERE faculty_Id = '$facultyID'");
$dataUser = mysqli_fetch_array($user);

function validate($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

if (isset($_POST['changePassAdmin']) || isset($_POST['changePassFaculty'])) {
    $oldPassword = validate($_POST['oldpass']);
    $newPassword = validate($_POST['newpass']);
    $confirmPassword = validate($_POST['conpass']);

    if (empty($oldPassword)) {
        $_SESSION['status'] = "Old Password Required";
        $_SESSION['status-code'] = "error";
    } elseif ($oldPassword !== $dataUser['password']) {
        $_SESSION['status'] = "Incorrect old password";
        $_SESSION['status-code'] = "error";
    } elseif (empty($newPassword)) {
        $_SESSION['status'] = "New Password Required";
        $_SESSION['status-code'] = "error";
    } elseif ($newPassword !== $confirmPassword) {
        $_SESSION['status'] = "New password does not match";
        $_SESSION['status-code'] = "error";
    } else {
        $sql = "UPDATE instructor SET password = '$newPassword' WHERE faculty_Id = '$facultyID'";
        $sql_query = mysqli_query($con, $sql);

        if ($sql_query) {
            $_SESSION['status'] = "Successfully changed the password";
            $_SESSION['status-code'] = "success";
        } else {
            $_SESSION['status'] = "Failed to change the password";
            $_SESSION['status-code'] = "error";
        }
    }
} else {
    $_SESSION['status'] = "ERROR.";
    $_SESSION['status-code'] = "error";
}

$redirectUrl = isset($_POST['changePassAdmin']) ? '../view/adminModule/admindashboard.php' : '../view/facultyModule/dashboard.php';
header("Location: $redirectUrl");
exit();
?>
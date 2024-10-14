<?php

session_start();
include "../../model/dbconnection.php";
$directoryURI = $_SERVER['REQUEST_URI'];
$path = parse_url($directoryURI, PHP_URL_PATH);
$components = explode("/", $path);
$page = $components[4];


if (!isset($_SESSION["userid"]) || $_SESSION["user"] !== "admin") {

    if (isset($_SESSION["user"]) && $_SESSION["user"] === "faculty") {
        header("location: ../facultyModule/dashboard.php");
        exit();
    } else if (isset($_SESSION["user"]) && $_SESSION["user"] === "student") {
        header("location: ../student_view.php");
        exit();
    }
}



$userId = $_SESSION["userid"];

$userId = mysqli_real_escape_string($con, $userId);

$usersql = "SELECT * FROM `instructor` WHERE faculty_Id = '$userId'";
$usersql_query = mysqli_query($con, $usersql);
$userRow = mysqli_fetch_assoc($usersql_query);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../../public/picture/cics.png" type="image/x-icon" />
    <link rel="stylesheet" href="../../public/css/navBar.css">
    <link rel="stylesheet" href="../../public/css/googleapis.css">
    <link rel="stylesheet" href="../../bootstrap/css/bootstrap.css">
    <script src="../../public/js/jquery-3.7.1.min.js"></script>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="separator profile">
            <div class="profileImg">
                <img src="../<?php echo $userRow['image']; ?>" style="border-radius: 10px;" alt="User Image">

            </div>
            <div class="profileInfo">
                <h5><?php echo $userRow['first_name'] . ' ' . $userRow['last_name'] ?></h5>
                <h6>Administrator</h6>
            </div>
        </div>
        <div class="separator">
            <ul class="navLinks" style="height: max-content;">
                <li>
                    <a href="admindashboard.php" class="
                    <?php
                    if ($page == "admindashboard.php") {
                        echo "linkName active";
                    } else {
                        echo "linkName";
                    }
                    ?>
                    ">
                        <i class="material-icons">home</i>
                        <span>Dashboard</span>
                    </a>
                    <ul class="subMenu blank">
                        <li>
                            <a href="admindashboard.php" class="linkName">
                                Dashboard
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="evaluate.php" class="
                    <?php
                    if ($page == "evaluate.php") {
                        echo "linkName active";
                    } else {
                        echo "linkName";
                    }
                    ?>
                    ">

                        <i class="material-icons">assignment_ind</i>
                        <span>Evaluate</span>
                    </a>
                    <ul class="subMenu blank">
                        <li>
                            <a href="#" class="linkName">
                                Evaluate
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="classObser.php" class="
                    <?php
                    if ($page == "classObser.php") {
                        echo "linkName active";
                    } else {
                        echo "linkName";
                    }
                    ?>
                    ">
                        <i class="material-icons">account_balance</i>
                        <span>Classroom Observation</span>
                    </a>
                    <ul class="subMenu blank">
                        <li>
                            <a href="#" class="linkName">
                                Classroom Observation
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="questionsTab.php" class="
                    <?php
                    if ($page == "questionsTab.php") {
                        echo "linkName active";
                    } else {
                        echo "linkName";
                    }
                    ?>
                    ">
                        <i class="material-icons">assignment_turned_in</i>
                        <span>Evaluation Tab</span>

                    </a>
                    <ul class="subMenu blank">
                        <li>
                            <a href="questionsTab.php" class="linkName">
                                Evaluation Tab
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="faculty.php" class="
                    <?php
                    if ($page == "faculty.php") {
                        echo "linkName active";
                    } else {
                        echo "linkName";
                    }
                    ?>
                    ">

                        <i class="material-icons">group</i>
                        <span>Faculty Member</span>

                    </a>
                    <ul class="subMenu blank">
                        <li>
                            <a href="faculty.php" class="linkName">
                                Faculty Member
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="evaluationResult.php" class="
                    <?php
                    if ($page == "evaluationResult.php") {
                        echo "linkName active";
                    } else {
                        echo "linkName";
                    }
                    ?>
                    ">
                        <i class="material-icons">assignment_turned_in</i>
                        <span>Evaluation Result</span>

                    </a>
                    <ul class="subMenu blank">
                        <li>
                            <a href="#" class="linkName">
                                Evaluation Result
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="peertopeer.php" class="
                    <?php
                    if ($page == "peertopeer.php") {
                        echo "linkName active";
                    } else {
                        echo "linkName";
                    }
                    ?>
                    ">

                        <i class="material-icons">group</i>
                        <span>Peer to Peer Evaluation Result</span>

                    </a>
                    <ul class="subMenu blank">
                        <li>
                            <a href="#" class="linkName">
                                Peer to Peer Evaluation Result
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#" data-bs-toggle="modal" data-bs-target="#changePassModal">
                        <i class="material-icons">vpn_key</i>
                        <span>Change Password</span>

                    </a>
                    <ul class="subMenu blank">
                        <li>
                            <a href="#" class="linkName" data-bs-toggle="modal" data-bs-target="#changePassModal">
                                Change Password
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="../../controller/logout.php">
                        <i class="material-icons">subdirectory_arrow_left</i>
                        <span>Sign-out</span>

                    </a>
                    <ul class="subMenu blank">
                        <li>
                            <a href="../../controller/logout.php" class="linkName">
                                Sign-out
                            </a>
                        </li>
                    </ul>
                </li>

            </ul>
        </div>
    </div>

    <!-- Change Password Modal-->
    <div class="modal fade" id="changePassModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="exampleModalLabel">Change
                        Password</h5>
                    <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="../../controller/changepassAdminFaculty.php" method="POST">
                        <div class="mb-3">
                            <label for="exampleInputPassword1" class="form-label">Current Password</label>
                            <input type="password" class="form-control" name="oldpass" id="exampleInputPassword1">
                        </div>
                        <div class="mb-3">
                            <label for="exampleInputPassword1" class="form-label">New Password</label>
                            <input type="password" class="form-control" name="newpass" id="exampleInputPassword2">
                        </div>
                        <div class="mb-3">
                            <label for="exampleInputPassword1" class="form-label">Re-enter New
                                Password</label>
                            <input type="password" class="form-control" name="conpass" id="exampleInputPassword3">
                        </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="changePassAdmin" class="btn btn-primary">Confirm</button>
                </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Header Section -->
    <section class="header-section">
        <div class="header-content">
            <div class="bsuIcon">
                <img src="../../public/picture/bsu.png" alt>
            </div>
            <div class="headerName">
                <h1 class="fw-bold">Faculty Evaluation Portal</h1>
            </div>
            <div class="bsuIcon">
                <img src="../../public/picture/cics.png" alt>
            </div>
        </div>
    </section>
    <!-- Menu Icon -->
    <section class="nav-section">
        <i class="material-icons menuBox">menu</i>
    </section>

    <!-- Sidebar -->
    <script>
        $(document).ready(function () {
            $(".arrow").on("click", function (e) {
                let arrowParent = $(this).parent().parent();
                arrowParent.toggleClass("showMenu");
            });

            $(".menuBox").on("click", function () {
                $(".sidebar").toggleClass("close");
            });
        });
    </script>
    <!-- BootStrap -->
    <script src="../../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>
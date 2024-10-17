<?php
session_start();
include "../../model/dbconnection.php";

$directoryURI = $_SERVER['REQUEST_URI'];
$path = parse_url($directoryURI, PHP_URL_PATH);
$components = explode("/", $path);
$page = $components[4];

if (!isset($_SESSION["userid"]) || $_SESSION["user"] !== "faculty") {

    if (isset($_SESSION["user"]) && $_SESSION["user"] === "admin") {
        header("location: ../adminModule/admindashboard.php");
        exit();
    } else if (isset($_SESSION["user"]) && $_SESSION["user"] === "student") {
        header("location: ../student_view.php");
        exit();
    }
}
if (!isset($_SESSION["userid"])) {
    header("location: ../loginModule\index.php");
    exit();
}

$userId = $_SESSION["userid"];

// Ensure the user ID is properly escaped to prevent SQL injection
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
                <h6>Instructor I</h6>
            </div>
        </div>
        <div class="separator">
            <ul class="navLinks">
                <li>
                    <a href="dashboard.php" class="
                    <?php
                    if ($page == "dashboard.php") {
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
                            <a href="dashboard.php" class="linkName">
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

            </ul>
        </div>
        <div class="separator last">
            <ul class="navLinks">
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
                    <h5 class="modal-title" id="exampleModalLabel">Change Password</h5>
                    <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="../../controller/changepassAdminFaculty.php" method="POST" class="needs-validation">
                        <div class="mb-3">
                            <label for="exampleInputPassword1" class="form-label">Current Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="oldpass" id="exampleInputPassword1">
                                <button type="button" class="btn btn-outline-secondary" id="toggleOldPass">Show</button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="validationPassword" class="form-label">New Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="newpass" id="validationPassword">
                                <button type="button" class="btn btn-outline-secondary" id="toggleNewPass">Show</button>
                            </div>
                            <div class="progress" style="height: 5px;">
                                <div id="progressbar" class="progress-bar progress-bar-striped progress-bar-animated"
                                    role="progressbar" style="width: 10%;" aria-valuenow="50" aria-valuemin="0"
                                    aria-valuemax="100">
                                </div>
                            </div>
                            <small id="passwordHelpBlock" class="form-text text-muted">
                                Your password must be 8-20 characters long, must contain special characters "!@#$%&*_?",
                                numbers, lower and upper letters only.
                            </small>
                            <div id="feedbackin" class="valid-feedback">
                                Strong Password!
                            </div>
                            <div id="feedbackirn" class="invalid-feedback">
                                At least 8 characters, Number, special character, Capital Letter, and Small letters.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="exampleInputPassword3" class="form-label">Re-enter New Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control" name="conpass" id="exampleInputPassword3"
                                    disabled>
                                <button type="button" class="btn btn-outline-secondary" id="toggleReEnterPass"
                                    disabled>Show</button>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="changePassFaculty" class="btn btn-primary">Confirm</button>
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

    <!-- SCRIPTS -->
    <!-- Jquery -->

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


            (function () {
                'use strict';
                window.addEventListener('load', function () {
                    const forms = document.getElementsByClassName('needs-validation');

                    Array.prototype.filter.call(forms, function (form) {
                        const passwordInput = form.validationPassword;
                        const feedbackIn = document.getElementById("feedbackin");
                        const feedbackIrn = document.getElementById("feedbackirn");
                        const progressBar = document.getElementById("progressbar");

                        passwordInput.addEventListener('keypress', function (event) {
                            const chr = String.fromCharCode(event.which);
                            const criteria = [
                                /[!@#$%&*_?]/,
                                /[A-Z]/,
                                /[0-9]/,
                                /[a-z]/
                            ];

                            const isValidInput = criteria.some(regex => regex.test(chr));
                            const isMaxLength = passwordInput.value.length < 20;

                            if (!isValidInput && isMaxLength) {
                                event.preventDefault();
                                event.stopPropagation();
                            }
                        });

                        passwordInput.addEventListener('keyup', function () {
                            const criteria = [
                                /[!@#$%&*_?]/,
                                /[A-Z]/,
                                /[0-9]/,
                                /[a-z]/
                            ];

                            const messages = [
                                "Special Character",
                                "Uppercase Letter",
                                "Number",
                                "Lowercase Letter"
                            ];

                            const validationResults = criteria.map(regex => regex.test(passwordInput.value));
                            const score = validationResults.reduce((sum, result) => sum + (result ? 1 : 0), 0);
                            const progressMessages = messages.filter((_, index) => !validationResults[index]);
                            const strengthLevels = ["Way too Weak", "Very Weak", "Weak", "Medium", "Strong"];
                            let strength = strengthLevels[Math.min(score, strengthLevels.length - 1)];
                            let progressValue = Math.min(score * 25, 100);

                            feedbackIn.textContent = strength + (progressMessages.length ? " You need: " + progressMessages.join(", ") : "");
                            progressBar.className = "progress-bar progress-bar-striped progress-bar-animated " + (score < 2 ? "bg-danger" : score < 4 ? "bg-warning" : "bg-success");
                            progressBar.style.width = progressValue + "%";

                            form.verifyPassword.disabled = !passwordInput.checkValidity();
                        });
                    });
                }, false);
            })();

        });
        document.addEventListener("DOMContentLoaded", function () {
            const oldPasswordInput = document.getElementById('exampleInputPassword1');
            const newPasswordInput = document.getElementById('validationPassword');
            const reEnterPasswordInput = document.getElementById('exampleInputPassword3');
            const toggleOldPass = document.getElementById('toggleOldPass');
            const toggleNewPass = document.getElementById('toggleNewPass');
            const toggleReEnterPass = document.getElementById('toggleReEnterPass');

            function isValidPassword(password) {
                const minLength = password.length >= 8 && password.length <= 20;
                const hasUpperCase = /[A-Z]/.test(password);
                const hasLowerCase = /[a-z]/.test(password);
                const hasNumbers = /\d/.test(password);
                const hasSpecialChars = /[!@#$%&*_?]/.test(password);
                return minLength && hasUpperCase && hasLowerCase && hasNumbers && hasSpecialChars;
            }

            toggleOldPass.addEventListener('click', function () {
                if (oldPasswordInput.type === "password") {
                    oldPasswordInput.type = "text";
                    toggleOldPass.textContent = "Hide";
                } else {
                    oldPasswordInput.type = "password";
                    toggleOldPass.textContent = "Show";
                }
            });

            toggleNewPass.addEventListener('click', function () {
                if (newPasswordInput.type === "password") {
                    newPasswordInput.type = "text";
                    toggleNewPass.textContent = "Hide";
                } else {
                    newPasswordInput.type = "password";
                    toggleNewPass.textContent = "Show";
                }
            });

            newPasswordInput.addEventListener('input', function () {
                if (newPasswordInput.value) {
                    if (isValidPassword(newPasswordInput.value)) {
                        toggleReEnterPass.disabled = false;
                        reEnterPasswordInput.disabled = false;
                    } else {
                        toggleReEnterPass.disabled = true;
                        reEnterPasswordInput.disabled = true;
                    }
                } else {
                    toggleReEnterPass.disabled = true;
                    reEnterPasswordInput.disabled = true;
                }
            });

            toggleReEnterPass.addEventListener('click', function () {
                if (reEnterPasswordInput.type === "password") {
                    reEnterPasswordInput.type = "text";
                    toggleReEnterPass.textContent = "Hide";
                } else {
                    reEnterPasswordInput.type = "password";
                    toggleReEnterPass.textContent = "Show";
                }
            });
        });
    </script>
    <!-- BootStrap -->
    <script src="../../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>
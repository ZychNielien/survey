<?php

session_start();
include "../model/dbconnection.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
    integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
    integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />

  <link rel="shortcut icon" href="../public/picture/cics.png" type="image/x-icon" />

  <link rel="stylesheet" href="../bootstrap/css/bootstrap.css" />

  <!-- SWEETALERT2 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!--  -->

  <!-- JQUERY CDN -->
  <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
    crossorigin="anonymous"></script>
  <!--  -->

  <!-- LOCALSTORAGE JS -->
  <script src="../public/js/get_ls.js"></script>
  <!--  -->

  <!-- DATATABLES -->
  <link rel="stylesheet" href="../public/DataTables/datatables.min.css">
  <script src="../public/DataTables/datatables.min.js"></script>
  <!--  -->

  <!-- SELECT2 -->
  <link rel="stylesheet" href="../public/select2/dist/css/select2.min.css">
  <script src="../public/select2/dist/js/select2.min.js"></script>

  <link rel="stylesheet" href="../public/select2-bootstrap-5/select2-bootstrap-5-theme.css">
  <link rel="stylesheet" href="../public/select2-bootstrap-5/select2-bootstrap-5-theme.min.css">
  <!--  -->
  <title>FEP-BSU</title>

  <style>
    .t_btn {
      box-shadow: inset gray 5px 8px 5px -5px;
    }

    .t_btn:hover {
      box-shadow: 5px 8px 5px -6px;
    }
  </style>

</head>

<body>
  <!-- START MAIN CONTENT -->
  <div class="container">
    <!-- START NAVBAR -->
    <nav class="navbar bg-danger d-flex align-items-center p-3 shadow">
      <div>
        <img src="../public/picture/bsu.png" alt style="width: 70px; height: 70px" />
      </div>
      <div>
        <h4 style="color: white">FACULTY EVALUATION PORTAL</h4>
      </div>
      <div>
        <img src="../public/picture/cics.png" alt style="width: 65px; height: 65px" />
      </div>
    </nav>
    <!-- END NAVBAR -->
    <!-- START PROFILE -->
    <div class="container-fluid p-3 d-flex align-items-center justify-content-between bg-light shadow">
      <div class="d-flex align-items-center">
        <div class="border p-2 bg-light">
          <img src="../public/picture/user.jpg" alt style="width: 150px; height: 150px" />
        </div>
        <div class="px-3">
          <h2 id="lastname"></h2>
          <h2 id="firstname"></h2>
        </div>
      </div>
      <div>
        <div class="row row">
          <div class="col">
            <p class="fw-bold">
              <i class="fa-solid fa-play text-muted"></i> <span id="course"></span> - <span id="year"></span> YEAR
            </p>
          </div>
          <div class="col-10">
            <p class="fw-bold">
              <i class="fa-solid fa-play text-muted"></i> <span id="semester"></span>
            </p>
          </div>
        </div>
        <div class="row">
          <div class="col">
            <p class="fw-bold">
              <i class="fa-solid fa-play text-muted"></i> SERVICE MANAGEMENT
            </p>
          </div>
        </div>
      </div>
    </div>
    <!-- END PROFILE -->
    <!-- START LOGOUT CHANGE PASS -->
    <div class="container-fluid bg-danger p-2 d-flex justify-content-between shadow">
      <div>

        <button class="t_btn text-danger btn btn-light rounded-pill border-0" data-bs-toggle="modal"
          data-bs-target="#exampleModal">
          <i class="fa-solid fa-key"></i> Change Password
        </button>

        <!-- START CHANGE PASSWORD MODAL -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header bg-danger">
                <h5 class="modal-title text-light" id="exampleModalLabel">Change
                  Password</h5>
                <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <form action="../controller/changepass.php" method="POST">
                  <div class="mb-3">
                    <label for="oldpass" class="form-label" style="font-family: monospace">Current Password:</label>
                    <input type="password" class="form-control" name="oldpass" id="exampleInputPassword1">
                  </div>
                  <div class="mb-3">
                    <label for="newpass" class="form-label" style="font-family: monospace">New Password:</label>
                    <input type="password" class="form-control" name="newpass" id="exampleInputPassword1">
                  </div>
                  <div class="mb-3">
                    <label for="conpass" class="form-label" style="font-family: monospace">Re-enter New
                      Password:</label>
                    <input type="password" class="form-control" name="conpass" id="exampleInputPassword1">
                  </div>

              </div>
              <div class="modal-footer d-flex justify-content-between bg-light">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i
                    class="fa-regular fa-circle-xmark"></i> Close</button>
                <button type="submit" name="changePass" class="btn btn-primary"><i
                    class="fa-regular fa-circle-check"></i> Confirm</button>
              </div>
              </form>
            </div>
          </div>
        </div>
        <!-- END CHANGE PASSWORD MODAL -->
      </div>

      <div>
        <a href="../controller/logout.php"><button class="t_btn text-danger btn btn-light rounded-pill border-0">
            <i class="fa-solid fa-power-off"></i> Log-out
          </button></a>

      </div>
    </div>
    <!-- END LOGOUT CHANGE PASS -->
    <!-- START SECOND NAVBAR -->
    <div class="container-fluid bg-light p-4 d-flex justify-content-around shadow">
      <div>
        <button type="button" id="enroll-btn" class="btn btn-success d-flex align-items-center"
          data-bs-target="#enroll_modal" data-bs-toggle="modal">
          <h2><i class="fa-solid fa-desktop"></i></h2>
          <h6 style="font-family: monospace;" class="px-2">Online
            Registration<br>for First Semester</h6 style="font-family: monospace;">
        </button>
      </div>
      <div>
        <button class="btn btn-success d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_subject">
          <h2><i class="fa-solid fa-plus"></i></h2>
          <h6 style="font-family: monospace;" class="px-2">Add subject</h6 style="font-family: monospace;">
        </button>
      </div>
      <div>
        <button class="btn btn-success d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addSec">
          <h2><i class="fa-solid fa-plus"></i></h2>
          <h6 style="font-family: monospace;" class="px-2">Add Section</h6 style="font-family: monospace;">
        </button>
      </div>
      <div>
        <button class="btn btn-success d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#assigned">
          <h2><i class="fa-solid fa-plus"></i></h2>
          <h6 style="font-family: monospace;" class="px-2">Assigned Instructor</h6 style="font-family: monospace;">
        </button>
      </div>
      <div>
        <button class="btn btn-success d-flex align-items-center">
          <h2><i class="fa-solid fa-clipboard-list"></i></h2>
          <h6 style="font-family: monospace;" class="px-2">Evaluate My
            Instructors</h6 style="font-family: monospace;">
        </button>
      </div>
    </div>
    <div>
      <button class="btn btn-success d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addSec">
        <h2><i class="fa-solid fa-plus"></i></h2>
        <h6 style="font-family: monospace;" class="px-2">Add Instructor</h6 style="font-family: monospace;">
      </button>
    </div>
    <!-- END SECOND NAVBAR -->
    <!-- START ENROLLED TABLE -->
    <div class="container bg-light d-flex align-items-center justify-content-center shadow" style="height: 60vh auto">
      <div class="p-3">
        <h1 class="text-center" style="font-family: monospace">
          ENROLLED SUBJECTS
        </h1>
        <div>
          <table class="table table-striped table-bordered text-center" style="font-family: monospace">
            <thead>
              <th>Code</th>
              <th>Description</th>
              <th>Unit</th>
              <th>Section</th>
              <th>Instructor</th>
              <th>Schedule</th>
              <th>Evaluate</th>
            </thead>
            <tbody>
              <tr>
                <td>IT 411</td>
                <td>Capstone Project 2</td>
                <td>3</td>
                <td>IT-SM-4101 / MALVAR</td>
                <td>GUILLO, JOSEPH RIZALDE</td>
                <td>MON - 05:00 PM-08:00 PM / CICS 202</td>
                <td><button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#eval_ins">Evaluate</button>
                </td>
              </tr>
              <tr>
                <td>CS 423</td>
                <td>Social Issues and Professional Practice</td>
                <td>3</td>
                <td>IT-SM-4101 / MALVAR</td>
                <td>ROSAL, MIGUEL EDWARD A.</td>
                <td>SAT - 10:00 AM-01:00 PM / CIT 404</td>
                <td><button class="btn btn-success" data-bs-toggle="modal"
                    data-bs-target="#evaluation">Evaluate</button></td>
              </tr>
              <tr>
                <td>SMT 405</td>
                <td>Principles of System Thinking</td>
                <td>3</td>
                <td>IT-SM-4101 / MALVAR</td>
                <td>TUD, RONALD C.</td>
                <td>
                  FRI - 07:00 AM-09:00 AM / CICS 501 | SAT - 07:00
                  AM-10:00 AM
                  / CIT 405
                </td>
                <td><button class="btn btn-success">Evaluate</button></td>
              </tr>
              <tr>
                <td>ENGG 405</td>
                <td>Technopreneurship</td>
                <td>3</td>
                <td>IT-SM-4101 / MALVAR</td>
                <td>CAMENTO, NICOLAS JOHN M.</td>
                <td>THU - 01:00 PM-04:00 PM / CICS 501</td>
                <td><button class="btn btn-success">Evaluate</button></td>
              </tr>
              <tr>
                <td>IT 413</td>
                <td>Advanced Information Assurance and Security</td>
                <td>3</td>
                <td>IT-SM-4101 / MALVAR</td>
                <td>CASTILLO, HOMER R.</td>
                <td>
                  THU - 01:00 PM-03:00 PM / COMP LAB OLD | SAT - 05:00
                  PM-08:00 PM / COMP LAB OLD
                </td>
                <td><button class="btn btn-success">Evaluate</button></td>
              </tr>
              <tr>
                <td>IT 414</td>
                <td>surance</td>
                <td>3</td>
                <td>IT-SM-4101 / MALVAR</td>
                <td>GARCIA, DONNA M.</td>
                <td>
                  WED - 09:00 AM-12:00 PM / CIT 403 | FRI - 01:00 PM-03:00
                  PM
                  / COMP LAB OLD
                </td>
                <td><button class="btn btn-success">Evaluate</button></td>
              </tr>
              <tr>
                <td>IT 412</td>
                <td>Platform Technologies</td>
                <td>3</td>
                <td>IT-SM-4101 / MALVAR</td>
                <td>CAMENTO, NICOLAS JOHN M.</td>
                <td>WED - 05:00 PM-08:00 PM / COMP LAB NEW</td>
                <td><button class="btn btn-success">Evaluate</button></td>
              </tr>
            </tbody>
          </table>
        </div>
        <!-- <h1>
            <i class="fa-solid fa-circle-exclamation"></i> NO ENROLLED
            SUBJECT!
          </h1> -->
      </div>
    </div>
    <!-- END ENROLLED TABLE -->

    <!-- END MAIN CONTENT -->
    <!-- START FOOTER -->
    <footer class="container bg-danger shadow">
      <div class="text-center d-flex align-items-center justify-content-center">
        <p class="pt-3" style="color: white;">Copyritght <i class="fa-regular fa-copyright"></i> Batangas State
          University
          | College of Informatics and Computing Sciences</p>
      </div>
      <div class="bg-light d-flex justify-content-center align-items-center shadow-lg">
        <p class="pt-2 fw-bold">Stay Updated by following us on:</p>
        <h3 class="px-3 py-2"><i class="fa-brands fa-facebook"></i> <i class="fa-brands fa-instagram"></i> <i
            class="fa-brands fa-youtube"></i></h6>
      </div>
    </footer>
    <!-- END FOOTER -->
  </div>

  <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../public/js/sweetalert.min.js"></script>

  <?php
  if (isset($_SESSION['status']) && $_SESSION['status'] != '') {
    ?>
    <script>
      swal({
        title: "<?php echo $_SESSION['status']; ?>",
        // text: "You clicked the button!",
        icon: "<?php echo $_SESSION['status-code']; ?>",
        button: "Ok",
      });
    </script>
    <?php
    unset($_SESSION['status']);
  }
  ?>

  <!-- START ENROLL MODAL -->
  <div class="modal fade" id="enroll_modal" tabindex="-1" aria-labelledby="enroll_modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header bg-danger">
          <h5 class="modal-title text-light" id="enroll_modalLabel">Online Registration</h5>
          <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="p-3">
            <div class="pb-3">
              <h4 class="text-warning">Total Unit: <span id="unit"></span>/23</h4>
            </div>
            <table id="enroll-table" class="table table-striped text-center">
              <thead>
                <tr>
                  <th class="text-center">Code</th>
                  <th class="text-center">Description</th>
                  <th class="text-center">Unit</th>
                  <th class="text-center">Section</th>
                  <th class="text-center">Instructor</th>
                  <th class="text-center">Schedule</th>
                  <th class="text-center">Slot</th>
                  <th class="text-center">Enroll</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i
              class="fa-regular fa-circle-xmark"></i> Close</button>
        </div>
      </div>
    </div>
  </div>
  <!-- END ENROLL MODAL -->

  <!-- START ADD SUBJECT MODAL -->
  <div class="modal fade" id="add_subject" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-danger">
          <h5 class="modal-title text-light" id="enroll_modalLabel">ADD SUBJECT</h5>
          <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="container">
            <form action="../controller/controller.php" method="POST">
              <div class="row mb-3">
                <div class="col">
                  <label for="subject" class="form-label">Subject:</label>
                  <input type="text" name="subject" id="subject" class="form-control" placeholder="Subject Name">
                </div>
                <div class="col">
                  <label for="unit" class="form-label">Unit:</label>
                  <input type="text" name="unit" id="unit" class="form-control" placeholder="Unit subject">
                </div>
                <div class="col">
                  <label for="code" class="form-label">Subject Code:</label>
                  <input type="text" name="code" id="code" class="form-control" placeholder="Subject Code">
                </div>
              </div>
              <div class="row mb-3">
                <div class="col">
                  <label for="sem" class="form-label">Semester:</label>
                  <Select name="sem" id="sem" class="form-select">
                    <option value="selected" selected disabled>---Select semester---</option>
                    <?php
                    $query_sem = "SELECT sem_id, semester FROM semester";

                    $query_sem_run = mysqli_query($con, $query_sem);

                    $check_sem = mysqli_num_rows($query_sem_run) > 0;

                    if ($check_sem) {
                      while ($row_sem = mysqli_fetch_array($query_sem_run)) {
                        ?>
                        <option value="<?php echo $row_sem['sem_id'] ?>"><?php echo $row_sem['semester'] ?> SEMESTER
                        </option>
                        <?php
                      }
                    }
                    ?>
                  </Select>
                </div>
                <div class="col">
                  <label for="year" class="form-label">Year level:</label>
                  <Select name="year" id="year" class="form-select">
                    <option value="selected" selected disabled>---Select year level---</option>
                    <?php
                    $query_year = "SELECT year_id, year_level FROM year_level";

                    $query_year_run = mysqli_query($con, $query_year);

                    $check_year = mysqli_num_rows($query_year_run) > 0;

                    if ($check_year) {
                      while ($row_year = mysqli_fetch_array($query_year_run)) {
                        ?>
                        <option value="<?php echo $row_year['year_id'] ?>"><?php echo $row_year['year_level'] ?> YEAR
                        </option>
                        <?php
                      }
                    }
                    ?>
                  </Select>
                </div>
              </div>
          </div>
        </div>
        <div class="modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i
              class="fa-regular fa-circle-xmark"></i> Close</button>
          <input type="submit" name="submit_sub" id="submit_sub" class="btn btn-success" value="Save">
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- END ADD SUBJECT MODAL -->

  <!-- START ASSIGNED INSTRUCTOR MODAL -->
  <div class="modal fade" id="assigned" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-danger">
          <h5 class="modal-title text-light" id="enroll_modalLabel">ASSIGNED INSTRUCTOR</h5>
          <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="container">
            <form action="../controller/controller.php" method="POST">
              <div>
                <div class="row mb-3">
                  <div class="col">
                    <label for="sub_id" class="form-label fw-bold">Subject:</label>
                    <Select name="sub_id" id="sub_id" class="form-select">
                      <option value="selected" selected disabled>---Select Subject---</option>
                      <!-- FIRST YEAR -->
                      <optgroup label="FIRST YEAR">
                      <optgroup label="First Semester">
                        <?php
                        $query_sub = "SELECT subject_id, subject FROM subject WHERE year = '1' AND semester = '1' ORDER BY year ASC";

                        $query_sub_run = mysqli_query($con, $query_sub);

                        $check_sub = mysqli_num_rows($query_sub_run) > 0;

                        if ($check_sub) {
                          while ($row_sub = mysqli_fetch_array($query_sub_run)) {
                            ?>
                            <option value="<?php echo $row_sub['subject_id'] ?>"><?php echo $row_sub['subject'] ?></option>
                            <?php
                          }
                        }
                        ?>
                      </optgroup>
                      <optgroup label="Second Semester">
                        <?php
                        $query_sub = "SELECT subject_id, subject FROM subject WHERE year = '1' AND semester = '2' ORDER BY year ASC";

                        $query_sub_run = mysqli_query($con, $query_sub);

                        $check_sub = mysqli_num_rows($query_sub_run) > 0;

                        if ($check_sub) {
                          while ($row_sub = mysqli_fetch_array($query_sub_run)) {
                            ?>
                            <option value="<?php echo $row_sub['subject_id'] ?>"><?php echo $row_sub['subject'] ?></option>
                            <?php
                          }
                        }
                        ?>
                      </optgroup>
                      </optgroup>
                      <!-- SECOND YEAR -->
                      <optgroup label="SECOND YEAR">
                      <optgroup label="First Semester">
                        <?php
                        $query_sub = "SELECT subject_id, subject FROM subject WHERE year = '2' AND semester = '1' ORDER BY year ASC";

                        $query_sub_run = mysqli_query($con, $query_sub);

                        $check_sub = mysqli_num_rows($query_sub_run) > 0;

                        if ($check_sub) {
                          while ($row_sub = mysqli_fetch_array($query_sub_run)) {
                            ?>
                            <option value="<?php echo $row_sub['subject_id'] ?>"><?php echo $row_sub['subject'] ?></option>
                            <?php
                          }
                        }
                        ?>
                      </optgroup>
                      <optgroup label="Second Semester">
                        <?php
                        $query_sub = "SELECT subject_id, subject FROM subject WHERE year = '2' AND semester = '2' ORDER BY year ASC";

                        $query_sub_run = mysqli_query($con, $query_sub);

                        $check_sub = mysqli_num_rows($query_sub_run) > 0;

                        if ($check_sub) {
                          while ($row_sub = mysqli_fetch_array($query_sub_run)) {
                            ?>
                            <option value="<?php echo $row_sub['subject_id'] ?>"><?php echo $row_sub['subject'] ?></option>
                            <?php
                          }
                        }
                        ?>
                      </optgroup>
                      </optgroup>
                      w
                      <!-- THIRD YEAR -->
                      <optgroup label="THIRD YEAR">
                      <optgroup label="First Semester">
                        <?php
                        $query_sub = "SELECT subject_id, subject FROM subject WHERE year = '3' AND semester = '1' ORDER BY year ASC";

                        $query_sub_run = mysqli_query($con, $query_sub);

                        $check_sub = mysqli_num_rows($query_sub_run) > 0;

                        if ($check_sub) {
                          while ($row_sub = mysqli_fetch_array($query_sub_run)) {
                            ?>
                            <option value="<?php echo $row_sub['subject_id'] ?>"><?php echo $row_sub['subject'] ?></option>
                            <?php
                          }
                        }
                        ?>
                      </optgroup>
                      <optgroup label="Second Semester">
                        <?php
                        $query_sub = "SELECT subject_id, subject FROM subject WHERE year = '3' AND semester = '2' ORDER BY year ASC";

                        $query_sub_run = mysqli_query($con, $query_sub);

                        $check_sub = mysqli_num_rows($query_sub_run) > 0;

                        if ($check_sub) {
                          while ($row_sub = mysqli_fetch_array($query_sub_run)) {
                            ?>
                            <option value="<?php echo $row_sub['subject_id'] ?>"><?php echo $row_sub['subject'] ?></option>
                            <?php
                          }
                        }
                        ?>
                      </optgroup>
                      </optgroup>
                      <!-- FOURTH YEAR -->
                      <optgroup label="FOURTH YEAR">
                      <optgroup label="First Semester">
                        <?php
                        $query_sub = "SELECT subject_id, subject FROM subject WHERE year = '4' AND semester = '1' ORDER BY year ASC";

                        $query_sub_run = mysqli_query($con, $query_sub);

                        $check_sub = mysqli_num_rows($query_sub_run) > 0;

                        if ($check_sub) {
                          while ($row_sub = mysqli_fetch_array($query_sub_run)) {
                            ?>
                            <option value="<?php echo $row_sub['subject_id'] ?>"><?php echo $row_sub['subject'] ?></option>
                            <?php
                          }
                        }
                        ?>
                      </optgroup>
                      <optgroup label="Second Semester">
                        <?php
                        $query_sub = "SELECT subject_id, subject FROM subject WHERE year = '4' AND semester = '2' ORDER BY year ASC";

                        $query_sub_run = mysqli_query($con, $query_sub);

                        $check_sub = mysqli_num_rows($query_sub_run) > 0;

                        if ($check_sub) {
                          while ($row_sub = mysqli_fetch_array($query_sub_run)) {
                            ?>
                            <option value="<?php echo $row_sub['subject_id'] ?>"><?php echo $row_sub['subject'] ?></option>
                            <?php
                          }
                        }
                        ?>
                      </optgroup>
                      </optgroup>
                    </Select>
                  </div>
                  <div class="col">
                    <label for="sec_id" class="form-label fw-bold">Section:</label>
                    <Select name="sec_id" id="sec_id" class="form-select">
                      <option value="selected" selected disabled>---Select Section---</option>

                    </Select>
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col">
                    <label for="fclty_id" class="form-label fw-bold">Instructor:</label>
                    <Select name="fclty_id" id="fclty_id" class="form-select">
                      <option value="selected" selected disabled>---Select Instructor---</option>

                    </Select>
                  </div>
                  <div class="col-3">
                    <label for="slot" class="form-label fw-bold">Slot:</label>
                    <input type="text" class="form-control" id="slot" name="slot" placeholder="slot per section">
                  </div>
                </div>
                <div class="row mb-3" id="sched_part" style="display: none;">
                  <div class="mb-3 d-flex justify-content-between align-items-center">
                    <h5>Schedule of the subject:</h5>
                    <div class="btn btn-secondary" id="sched2" disabled><i class="fa-solid fa-plus"></i> Another
                      Schedule</div>
                  </div>
                  <div class="col">
                    <label for="day" class="form-label fw-bold">Day:</label>
                    <Select name="day" id="day" class="form-select">
                      <option value="selected" selected disabled>---Select the day---</option>
                      <?php
                      $query_day = "SELECT day_id, days FROM days";

                      $query_day_run = mysqli_query($con, $query_day);

                      $check_day = mysqli_num_rows($query_day_run) > 0;

                      if ($check_day) {
                        while ($row_day = mysqli_fetch_array($query_day_run)) {
                          ?>
                          <option value="<?php echo $row_day['day_id'] ?>"><?php echo $row_day['days'] ?></option>
                          <?php
                        }
                      }
                      ?>
                    </Select>
                  </div>
                  <div class="col">
                    <label for="Stime" class="form-label fw-bold">Start Time:</label>
                    <Select name="Stime" id="Stime" class="form-select">
                      <option value="selected" selected disabled>---Select start time---</option>

                    </Select>
                  </div>
                  <div class="col">
                    <label for="Etime" class="form-label fw-bold">End Time:</label>
                    <Select name="Etime" id="Etime" class="form-select">
                      <option value="selected" selected disabled>---Select end time---</option>

                    </Select>
                  </div>
                </div>
                <div class="row mb-3" id="sched_part2" style="display: none;">
                  <div class="mb-3">
                    <h5>Second Schedule of the subject:</h5>
                  </div>
                  <div class="col">
                    <label for="day2" class="form-label fw-bold">Day:</label>
                    <Select name="day2" id="day2" class="form-select">
                      <option value="selected" selected disabled>---Select the day---</option>
                      <?php
                      $query_day = "SELECT day_id, days FROM days";

                      $query_day_run = mysqli_query($con, $query_day);

                      $check_day = mysqli_num_rows($query_day_run) > 0;

                      if ($check_day) {
                        while ($row_day = mysqli_fetch_array($query_day_run)) {
                          ?>
                          <option value="<?php echo $row_day['day_id'] ?>"><?php echo $row_day['days'] ?></option>
                          <?php
                        }
                      }
                      ?>
                    </Select>
                  </div>
                  <div class="col">
                    <label for="Stime2" class="form-label fw-bold">Start Time:</label>
                    <Select name="Stime2" id="Stime2" class="form-select">
                      <option value="selected" selected disabled>---Select start time---</option>

                    </Select>
                  </div>
                  <div class="col">
                    <label for="Etime2" class="form-label fw-bold">End Time:</label>
                    <Select name="Etime2" id="Etime2" class="form-select">
                      <option value="selected" selected disabled>---Select end time---</option>

                    </Select>
                  </div>
                </div>
              </div>
          </div>
        </div>
        <div class="modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i
              class="fa-regular fa-circle-xmark"></i> Close</button>
          <input type="submit" name="submit_assigned" id="submit_assigned" class="btn btn-success" value="Assigned">
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- END ASSIGNED INSTRUCTOR MODAL -->

  <!-- START ADD SECTION MODAL -->
  <div class="modal fade" id="addSec" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-danger">
          <h5 class="modal-title text-light" id="enroll_modalLabel">ADD SECTION</h5>
          <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="container">
            <form action="../controller/controller.php" method="POST">
              <div class="row mb-3">
                <div class="col">
                  <label for="addsection" class="form-label">Section: </label>
                  <input type="text" name="addsection" id="addsection" class="form-control" placeholder="ex. IT-1101">
                </div>
              </div>
              <div class="row mb-3">
                <div class="col">
                  <label for="year" class="form-label">Year level:</label>
                  <Select name="year" id="year2" class="form-select">
                    <option value="selected" selected disabled>---Select year level---</option>
                    <?php
                    $query_year = "SELECT year_id, year_level FROM year_level";

                    $query_year_run = mysqli_query($con, $query_year);

                    $check_year = mysqli_num_rows($query_year_run) > 0;

                    if ($check_year) {
                      while ($row_year = mysqli_fetch_array($query_year_run)) {
                        ?>
                        <option value="<?php echo $row_year['year_id'] ?>"><?php echo $row_year['year_level'] ?> YEAR
                        </option>
                        <?php
                      }
                    }
                    ?>
                  </Select>
                </div>
                <div class="col">
                  <label for="sem" class="form-label">Semester:</label>
                  <Select name="sem" id="sem2" class="form-select">
                    <option value="selected" selected disabled>---Select semester---</option>
                    <?php
                    $query_sem = "SELECT sem_id, semester FROM semester";

                    $query_sem_run = mysqli_query($con, $query_sem);

                    $check_sem = mysqli_num_rows($query_sem_run) > 0;

                    if ($check_sem) {
                      while ($row_sem = mysqli_fetch_array($query_sem_run)) {
                        ?>
                        <option value="<?php echo $row_sem['sem_id'] ?>"><?php echo $row_sem['semester'] ?> SEMESTER
                        </option>
                        <?php
                      }
                    }
                    ?>
                  </Select>
                </div>
              </div>
          </div>
        </div>
        <div class="modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i
              class="fa-regular fa-circle-xmark"></i> Close</button>
          <input type="submit" name="submit_section" id="submit_section" class="btn btn-success" value="Save">
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- END ADD SECTION MODAL -->

  <!-- START EVALUATION INSTRUCTION MODAL -->
  <div class="modal fade" id="eval_ins" tabindex="-1" aria-labelledby="enroll_modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-danger">
          <h5 class="modal-title text-light" id="enroll_modalLabel">PERFORMANCE EVALUATION INSTRUMENT FOR FACULTY
            DEVELOPMENT</h5>
          <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="container p-3">
            <div class="row">
              <div class="col-sm-5 border border-dark d-flex">
                <p class="fw-bold">Instructor: </p>
                <p>GUILLO, JOSEPH RIZALDE</p>
              </div>
              <div class="col border border-dark d-flex">
                <p class="fw-bold">Semester:</p>
                <p>FIRST</p>
              </div>
              <div class="col border border-dark d-flex">
                <p class="fw-bold">Academic Year:</p>
                <p>2024-2025</p>
              </div>
            </div>
            <div class="border border-dark row">
              <h6><span class="fw-bold">Instruction:</span> Please Evaluate the faculty member involved by selecting the
                star that corresponds to
                the given parameter/dimensions at the scale of 5, where five star is the perfect score and one star is
                the lowest
                score.</h6>
            </div>
            <div class="row text-center">
              <div class="col border border-dark">
                <p class="fw-bold">Numerical Rating</p>
              </div>
              <div class="col border border-dark">
                <p class="fw-bold">Descriptive Rating</p>
              </div>
              <div class="col-lg-7 border border-dark">
                <p class="fw-bold">Qualitative Description</p>
              </div>
            </div>
            <div class="row" style="font-family: monospace;">
              <div class="col border border-dark">
                <p class="text-center">5.0</p>
              </div>
              <div class="col border border-dark">
                <p class="text-center">Outstanding</p>
              </div>
              <div class="col-lg-7 border border-dark">
                <p>Exhibits the behavior described <span class="fw-bold"><u>at all times</u></span> when the occasion
                  occurs.</p>
              </div>
            </div>
            <div class="row" style="font-family: monospace;">
              <div class="col border border-dark">
                <p class="text-center">4.0</p>
              </div>
              <div class="col border border-dark">
                <p class="text-center">Very Satisfactory</p>
              </div>
              <div class="col-lg-7 border border-dark">
                <p>Exhibits the behavior described <span class="fw-bold"><u>most of the time</u></span> when the
                  occasion occurs.</p>
              </div>
            </div>
            <div class="row" style="font-family: monospace;">
              <div class="col border border-dark">
                <p class="text-center">3.0</p>
              </div>
              <div class="col border border-dark">
                <p class="text-center">Satisfactory</p>
              </div>
              <div class="col-lg-7 border border-dark">
                <p>Exhibits the behavior described <span class="fw-bold"><u>sometimes</u></span> when the occasion
                  occurs.</p>
              </div>
            </div>
            <div class="row" style="font-family: monospace;">
              <div class="col border border-dark">
                <p class="text-center">2.0</p>
              </div>
              <div class="col border border-dark">
                <p class="text-center">Fair</p>
              </div>
              <div class="col-lg-7 border border-dark">
                <p>Exhibits the behavior described <span class="fw-bold"><u>rarely</u></span> when the occasion occurs.
                </p>
              </div>
            </div>
            <div class="row" style="font-family: monospace;">
              <div class="col border border-dark">
                <p class="text-center">1.0</p>
              </div>
              <div class="col border border-dark">
                <p class="text-center">Poor</p>
              </div>
              <div class="col-lg-7 border border-dark">
                <p>Exhibits the behavior described <span class="fw-bold"><u>has not been exhibited at all
                      times</u></span> when the occasion occurs.</p>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i
              class="fa-regular fa-circle-xmark"></i> Close</button>
          <button type="button" class="btn btn-success" data-bs-target="#evaluation" data-bs-toggle="modal"
            data-bs-dismiss="modal"><i class="fa-regular fa-circle-right"></i> Proceed</button>
        </div>
      </div>
    </div>
  </div>
  <!-- END EVALUATION INSTRUCTION MODAL -->

  <!-- START ENROLL MODAL -->
  <div class="modal fade" id="evaluation" tabindex="-1" aria-labelledby="enroll_modalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header bg-danger">
          <h5 class="modal-title text-light" id="enroll_modalLabel">PERFORMANCE EVALUATION INSTRUMENT FOR FACULTY
            DEVELOPMENT</h5>
          <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="container p-3">

            <form action="../controller/controller.php" method="POST">
              <!-- START TEACHING EFFECTIVENESS -->
              <div class="border-dark border p-3">
                <h5 class="fw-bolder" style="font-family: serif;">I. TEACHING EFFECTIVENESS</h5>
                <?php
                $query_TE = "SELECT * FROM question WHERE q_group = '1'";

                $query_TE_run = mysqli_query($con, $query_TE);

                $check_TE = mysqli_num_rows($query_sem_run) > 0;

                if ($check_TE) {
                  while ($row_TE = mysqli_fetch_array($query_TE_run)) {
                    ?>
                    <h6 class="fw-bold"><i class="fa-solid fa-play"></i> <?php echo $row_TE['description'] ?></h6>
                    <input type="hidden" name="qu_id[]" value="<?php echo $row_TE['q_id'] ?>">
                    <input type="hidden" name="sr-code[]" value="19-61072">
                    <input type="hidden" name="facultyID[]" value="3">
                    <input type="radio" name="<?php echo $row_TE['q_id'] ?>TE" id="1" value="1">
                    <label for="1" class="form-label fw-bold">1</label>
                    <input type="radio" name="<?php echo $row_TE['q_id'] ?>TE" id="2" value="2">
                    <label for="2" class="form-label fw-bold">2</label>
                    <input type="radio" name="<?php echo $row_TE['q_id'] ?>TE" id="3" value="3">
                    <label for="3" class="form-label fw-bold">3</label>
                    <input type="radio" name="<?php echo $row_TE['q_id'] ?>TE" id="4" value="4">
                    <label for="4" class="form-label fw-bold">4</label>
                    <input type="radio" name="<?php echo $row_TE['q_id'] ?>TE" id="5" value="5">
                    <label for="5" class="form-label fw-bold">5</label>
                    <?php
                  }
                } else {
                  ?>
                  <h6 class="fw-bold">NO QUESTIONS</h6>
                  <?php
                }
                ?>
              </div>

              <!-- END TEACHING EFFECTIVENESS -->

              <!--START CLASSROOM MANAGEMENT -->
              <div class="border-dark border p-3">
                <h5 class="fw-bolder" style="font-family: serif;">II. CLASSROOM MANAGEMENT</h5>
                <?php
                $query_TE = "SELECT * FROM question WHERE q_group = '2'";

                $query_TE_run = mysqli_query($con, $query_TE);

                $check_TE = mysqli_num_rows($query_sem_run) > 0;

                if ($check_TE) {
                  while ($row_TE = mysqli_fetch_array($query_TE_run)) {
                    ?>
                    <h6 class="fw-bold"><i class="fa-solid fa-play"></i> <?php echo $row_TE['description'] ?></h6>
                    <input type="hidden" name="qu_id[]" value="<?php echo $row_TE['q_id'] ?>">
                    <input type="hidden" name="sr-code[]" value="19-61072">
                    <input type="hidden" name="facultyID[]" value="3">
                    <input type="radio" name="<?php echo $row_TE['q_id'] ?>CM" id="1" value="1">
                    <label for="1" class="form-label fw-bold">1</label>
                    <input type="radio" name="<?php echo $row_TE['q_id'] ?>CM" id="2" value="2">
                    <label for="2" class="form-label fw-bold">2</label>
                    <input type="radio" name="<?php echo $row_TE['q_id'] ?>CM" id="3" value="3">
                    <label for="3" class="form-label fw-bold">3</label>
                    <input type="radio" name="<?php echo $row_TE['q_id'] ?>CM" id="4" value="4">
                    <label for="4" class="form-label fw-bold">4</label>
                    <input type="radio" name="<?php echo $row_TE['q_id'] ?>CM" id="5" value="5">
                    <label for="5" class="form-label fw-bold">5</label>
                    <?php
                  }
                } else {
                  ?>
                  <h6 class="fw-bold">NO QUESTIONS</h6>
                  <?php
                }
                ?>
              </div>
              <!-- END CLASSROOM MANAGEMENT -->

              <!--START STUDENT ENGAGEMENT -->
              <div class="border-dark border p-3">
                <h5 class="fw-bolder" style="font-family: serif;">III. STUDENT ENGAGEMENT</h5>
                <?php
                $query_TE = "SELECT * FROM question WHERE q_group = '3'";

                $query_TE_run = mysqli_query($con, $query_TE);

                $check_TE = mysqli_num_rows($query_sem_run) > 0;

                if ($check_TE) {
                  while ($row_TE = mysqli_fetch_array($query_TE_run)) {
                    ?>
                    <h6 class="fw-bold"><i class="fa-solid fa-play"></i> <?php echo $row_TE['description'] ?></h6>
                    <input type="hidden" name="qu_id[]" value="<?php echo $row_TE['q_id'] ?>">
                    <input type="hidden" name="sr-code[]" value="19-61072">
                    <input type="hidden" name="facultyID[]" value="3">
                    <input type="radio" name="<?php echo $row_TE['q_id'] ?>SE" id="1" value="1">
                    <label for="1" class="form-label fw-bold">1</label>
                    <input type="radio" name="<?php echo $row_TE['q_id'] ?>SE" id="2" value="2">
                    <label for="2" class="form-label fw-bold">2</label>
                    <input type="radio" name="<?php echo $row_TE['q_id'] ?>SE" id="3" value="3">
                    <label for="3" class="form-label fw-bold">3</label>
                    <input type="radio" name="<?php echo $row_TE['q_id'] ?>SE" id="4" value="4">
                    <label for="4" class="form-label fw-bold">4</label>
                    <input type="radio" name="<?php echo $row_TE['q_id'] ?>SE" id="5" value="5">
                    <label for="5" class="form-label fw-bold">5</label>
                    <?php
                  }
                } else {
                  ?>
                  <h6 class="fw-bold">NO QUESTIONS</h6>
                  <?php
                }
                ?>
              </div>
              <!-- END STUDENT ENGAGEMENT -->

              <!--START COMMUNICATION -->
              <div class="border-dark border p-3">
                <h5 class="fw-bolder" style="font-family: serif;">IV. COMMUNICATION</h5>
                <?php
                $query_TE = "SELECT * FROM question WHERE q_group = '4'";

                $query_TE_run = mysqli_query($con, $query_TE);

                $check_TE = mysqli_num_rows($query_sem_run) > 0;

                if ($check_TE) {
                  while ($row_TE = mysqli_fetch_array($query_TE_run)) {
                    ?>
                    <h6 class="fw-bold"><i class="fa-solid fa-play"></i> <?php echo $row_TE['description'] ?></h6>
                    <input type="hidden" name="qu_id[]" value="<?php echo $row_TE['q_id'] ?>">
                    <input type="hidden" name="sr-code[]" value="19-61072">
                    <input type="hidden" name="facultyID[]" value="3">
                    <input type="radio" name="<?php echo $row_TE['q_id'] ?>C" id="1" value="1">
                    <label for="1" class="form-label fw-bold">1</label>
                    <input type="radio" name="<?php echo $row_TE['q_id'] ?>C" id="2" value="2">
                    <label for="2" class="form-label fw-bold">2</label>
                    <input type="radio" name="<?php echo $row_TE['q_id'] ?>C" id="3" value="3">
                    <label for="3" class="form-label fw-bold">3</label>
                    <input type="radio" name="<?php echo $row_TE['q_id'] ?>C" id="4" value="4">
                    <label for="4" class="form-label fw-bold">4</label>
                    <input type="radio" name="<?php echo $row_TE['q_id'] ?>C" id="5" value="5">
                    <label for="5" class="form-label fw-bold">5</label>
                    <?php
                  }
                } else {
                  ?>
                  <h6 class="fw-bold">NO QUESTIONS</h6>
                  <?php
                }
                ?>
              </div>
              <!-- END COMMUNICATION -->

              <!--START EMOTIONAL COMPETENCE -->
              <div class="border-dark border p-3">
                <h5 class="fw-bolder" style="font-family: serif;">V. EMOTIONAL COMPETENCE</h5>
                <?php
                $query_TE = "SELECT * FROM question WHERE q_group = '5'";

                $query_TE_run = mysqli_query($con, $query_TE);

                $check_TE = mysqli_num_rows($query_sem_run) > 0;

                if ($check_TE) {
                  while ($row_TE = mysqli_fetch_array($query_TE_run)) {
                    ?>
                    <h6 class="fw-bold"><i class="fa-solid fa-play"></i> <?php echo $row_TE['description'] ?></h6>
                    <input type="hidden" name="qu_id[]" value="<?php echo $row_TE['q_id'] ?>">
                    <input type="hidden" name="sr-code[]" value="19-61072">
                    <input type="hidden" name="facultyID[]" value="3">
                    <input type="radio" name="<?php echo $row_TE['q_id'] ?>EC" id="1" value="1">
                    <label for="1" class="form-label fw-bold">1</label>
                    <input type="radio" name="<?php echo $row_TE['q_id'] ?>EC" id="2" value="2">
                    <label for="2" class="form-label fw-bold">2</label>
                    <input type="radio" name="<?php echo $row_TE['q_id'] ?>EC" id="3" value="3">
                    <label for="3" class="form-label fw-bold">3</label>
                    <input type="radio" name="<?php echo $row_TE['q_id'] ?>EC" id="4" value="4">
                    <label for="4" class="form-label fw-bold">4</label>
                    <input type="radio" name="<?php echo $row_TE['q_id'] ?>EC" id="5" value="5">
                    <label for="5" class="form-label fw-bold">5</label>
                    <?php
                  }
                } else {
                  ?>
                  <h6 class="fw-bold">NO QUESTIONS</h6>
                  <?php
                }
                ?>
              </div>
              <!-- END EMOTIONAL COMPETENCE -->



          </div>
        </div>
        <div class="modal-footer d-flex justify-content-between">
          <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i
              class="fa-regular fa-circle-xmark"></i> Close</button>
          <button type="submit" name="submit_evaluation" id="submit_evaluation" class="btn btn-success"
            data-bs-target="#evaluation" data-bs-toggle="modal"><i class="fa-solid fa-paper-plane"></i> Submit</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- END ENROLL MODAL -->
</body>

</html>
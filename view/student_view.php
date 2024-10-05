<?php

session_start();

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

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


  <title>FEP-BSU</title>
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
          <h2><?php echo $_SESSION["studentSRCode"] ?></h2>
          <h2>Juan</h2>
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

        <button class="btn btn-light rounded-pill" data-bs-toggle="modal" data-bs-target="#exampleModal">
          <i class="fa-solid fa-key"></i> Change Password
        </button>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Change
                  Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <form action="../controller/changepass.php" method="POST">
                  <div class="mb-3">
                    <label for="exampleInputPassword1" class="form-label">Current Password</label>
                    <input type="password" class="form-control" name="oldpass" id="exampleInputPassword1">
                  </div>
                  <div class="mb-3">
                    <label for="exampleInputPassword1" class="form-label">New Password</label>
                    <input type="password" class="form-control" name="newpass" id="exampleInputPassword1">
                  </div>
                  <div class="mb-3">
                    <label for="exampleInputPassword1" class="form-label">Re-enter New Password</label>
                    <input type="password" class="form-control" name="conpass" id="exampleInputPassword1">
                  </div>

              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" name="changePass" class="btn btn-primary">Confirm</button>
              </div>
              </form>
            </div>
          </div>
        </div>
      </div>

      <div>
        <a href="../controller/logout.php" id="logout"><button class="btn btn-light rounded-pill">
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
        <button class="btn btn-success d-flex align-items-center">
          <h2><i class="fa-solid fa-clipboard-list"></i></h2>
          <h6 style="font-family: monospace;" class="px-2">Evaluate My
            Instructors</h6 style="font-family: monospace;">
        </button>
      </div>
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
            </thead>
            <tbody>
              <tr>
                <td>IT 411</td>
                <td>Capstone Project 2</td>
                <td>3</td>
                <td>IT-SM-4101 / MALVAR</td>
                <td>GUILLO, JOSEPH RIZALDE</td>
                <td>MON - 05:00 PM-08:00 PM / CICS 202</td>
              </tr>
              <tr>
                <td>CS 423</td>
                <td>Social Issues and Professional Practice</td>
                <td>3</td>
                <td>IT-SM-4101 / MALVAR</td>
                <td>ROSAL, MIGUEL EDWARD A.</td>
                <td>SAT - 10:00 AM-01:00 PM / CIT 404</td>
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
              </tr>
              <tr>
                <td>ENGG 405</td>
                <td>Technopreneurship</td>
                <td>3</td>
                <td>IT-SM-4101 / MALVAR</td>
                <td>CAMENTO, NICOLAS JOHN M.</td>
                <td>THU - 01:00 PM-04:00 PM / CICS 501</td>
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
              </tr>
              <tr>
                <td>IT 412</td>
                <td>Platform Technologies</td>
                <td>3</td>
                <td>IT-SM-4101 / MALVAR</td>
                <td>CAMENTO, NICOLAS JOHN M.</td>
                <td>WED - 05:00 PM-08:00 PM / COMP LAB NEW</td>
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

</body>

</html>
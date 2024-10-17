<?php

session_start();
include "../model/dbconnection.php";

if (!isset($_SESSION['studentSRCode'])) {
  header('Location: ../view/loginModule/index.php');
  exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <link rel="stylesheet" href="../fontawesome/css/all.min.css">

  <link rel="shortcut icon" href="../public/picture/cics.png" type="image/x-icon" />

  <link rel="stylesheet" href="../bootstrap/css/bootstrap.css" />
  <link rel="stylesheet" href="../fontawesome/css/all.min.css">
  <style>
    /* STAR RATING ON EVERY FORM */
    .rating {
      direction: rtl;
      unicode-bidi: bidi-override;
      color: #ddd;
      /* Personal choice */
    }

    .rating input {
      display: none;
    }

    .rating label,
    .rating input {
      margin: 0 10px;
    }

    .rating .ratingLabel:hover,
    .rating .ratingLabel:hover~.ratingLabel,
    .rating input:checked+.ratingLabel,
    .rating input:checked+.ratingLabel~.ratingLabel {
      color: gold;
      cursor: pointer;
    }
  </style>

  <!-- SWEETALERT2 -->
  <link rel="stylesheet" href="../public/css/sweetalert.min.css">

  <!-- SCRIPT -->
  <script src="../public/js/sweetalert2@11.js"></script>
  <!--  -->

  <!-- JQUERY CDN -->
  <script src="../public/js/jquery-3.7.1.min.js"></script>
  <script src="../bootstrap/js/bootstrap.min.js"></script>

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
            <p class="fw-bold" id="usermajor2">
              <i class="fa-solid fa-play text-muted"></i> <span id="usermajor"></span>
            </p>
          </div>
        </div>
      </div>
    </div>
    <!-- END PROFILE -->
    <!-- START LOGOUT CHANGE PASS -->
    <div class="container-fluid bg-danger p-2 d-flex justify-content-between shadow">
      <div>

        <button class="btn btn-light rounded-pill" data-bs-toggle="modal" data-bs-target="#changePassModal">
          <i class="fa-solid fa-key"></i> Change Password
        </button>

        <!-- Modal -->
        <div class="modal fade" id="changePassModal" tabindex="-1" aria-labelledby="changePassLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="changePassLabel">Change Password</h5>
                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <form action="../controller/changepass.php" method="POST" class="needs-validation">
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
                        role="progressbar" style="width: 10%;" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100">
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
                      <input type="password" class="form-control" name="conpass" id="exampleInputPassword3" disabled>
                      <button type="button" class="btn btn-outline-secondary" id="toggleReEnterPass"
                        disabled>Show</button>
                    </div>
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
            Registration<br>for <span id="getSem2"></span> Semester</h6 style="font-family: monospace;">
        </button>





      </div>
      <h1 style="display: none;"><?php echo $_SESSION['studentSRCode'] ?></h1>
      <div id="major">
        <button class="btn btn-success d-flex align-items-center" data-bs-target="#major_modal" data-bs-toggle="modal">
          <h2><i class="fa-solid fa-graduation-cap"></i>
            <h6 style="font-family: monospace;" class="px-2">Choose A Major</h6>
        </button>
      </div>
    </div>
    <!-- END SECOND NAVBAR -->
    <!-- START ENROLLED TABLE -->
    <div class="container bg-light d-flex align-items-center justify-content-center shadow"
      style="height: auto; min-height: 40vh">
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
              <th>Action</th>
            </thead>
            <tbody>
              <?php
              $srcode = $_SESSION['studentSRCode'];

              $query = "SELECT 
                              E.id,
                              S.subject_code, 
                              E.subject_id, 
                              S.unit, 
                              E.section_id, 
                              I.last_name, 
                              I.first_name, 
                              I.faculty_id, 
                              D.days, 
                              TS.time AS startTime, 
                              TE.time AS endTime, 
                              COALESCE(D2.days, 'N/A') AS Day2, 
                              COALESCE(TS2.time, 'N/A') AS startTime2, 
                              COALESCE(TE2.time, 'N/A') AS endTime2,
                              E.eval_status
                          FROM 
                              enrolled_subject E 
                          INNER JOIN 
                              subject S ON E.subject_id = S.subject 
                          INNER JOIN 
                              instructor I ON E.faculty_id = I.faculty_id 
                          LEFT JOIN 
                              enrolled_student ES ON S.subject_id = ES.subject_id 
                          LEFT JOIN 
                              assigned_subject A ON ES.subject_id = A.subject_id AND E.faculty_id = A.faculty_id AND ES.section_id = A.section_id
                          INNER JOIN 
                              days D ON A.day_id = D.day_id 
                          INNER JOIN 
                              time TS ON A.S_time_id = TS.time_id 
                          INNER JOIN 
                              time TE ON A.E_time_id = TE.time_id 
                          LEFT JOIN 
                              days D2 ON A.day_id_2 = D2.day_id 
                          LEFT JOIN 
                              time TS2 ON A.S_time_id_2 = TS2.time_id 
                          LEFT JOIN 
                              time TE2 ON A.E_time_id_2 = TE2.time_id 
                          WHERE 
                              E.sr_code = '$srcode'";

              $query_run = mysqli_query($con, $query);

              if (mysqli_num_rows($query_run) > 0) {
                while ($row = mysqli_fetch_assoc($query_run)) {
                  ?>
                  <tr>
                    <td><?php echo $row['subject_code'] ?></td>
                    <td><?php echo $row['subject_id'] ?></td>
                    <td><?php echo $row['unit'] ?></td>
                    <td><?php echo $row['section_id'] ?></td>
                    <td><?php echo $row['last_name'] ?>, <?php echo $row['first_name'] ?></td>
                    <?php if ($row['Day2'] == 'N/A') { ?>
                      <td><?php echo $row['days'] ?> - <?php echo $row['startTime'] ?> - <?php echo $row['endTime'] ?></td>
                      <?php
                    } else {
                      ?>
                      <td><?php echo $row['days'] ?> - <?php echo $row['startTime'] ?> - <?php echo $row['endTime'] ?> /
                        <?php echo $row['Day2'] ?> - <?php echo $row['startTime2'] ?> - <?php echo $row['endTime2'] ?>
                      </td>
                      <?php
                    } ?>
                    <?php if ($row['eval_status'] == 0) {
                      ?>
                      <td><button type="button" class="btn btn-success m-2 evaluate-btn" data-bs-toggle="modal"
                          data-bs-target="#evaluationForm" data-id="<?php echo $row['faculty_id'] ?>"
                          data-enrolled-id="<?php echo $row['id'] ?>" data-first-name="<?php echo $row['first_name'] ?>"
                          data-last-name="<?php echo $row['last_name'] ?>" date-subject-id="<?php echo $row['subject_id'] ?>">
                          Evaluate
                        </button></td>
                      <?php
                    } else {
                      ?>
                      <td><button class="btn btn-secondary" disabled>Evaluated</button></td>
                      <?php
                    } ?>

                  </tr>
                  <?php
                }
              } else {
                ?>
                <tr>
                  <td colspan="6">No Enrolled Subject!</td>
                </tr>
                <?php
              }
              ?>

            </tbody>
          </table>
        </div>




        <!-- <h1>
            <i class="fa-solid fa-circle-exclamation"></i> NO ENROLLED
            SUBJECT!
          </h1> -->
      </div>
    </div>


    <!-- STUDENTS FORM -->
    <div class="modal fade" id="evaluationForm" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header  bg-danger text-white">
            <h5 class="modal-title text-center text-white" id="exampleModalLabel">FACULTY
              PEER TO PEER EVALUATION INSTRUMENT
              FOR FACULTY DEVELOPMENT</h5>
            <button type="button" class="btn-close bg-white" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div id="container" class="container">
              <form id="multi-step-form" method="POST" action="../controller/criteria.php">
                <div class="step step-1">
                  <div class="instructions mb-3">
                    <span class="fw-bold">Instruction</span>:
                    Please Evaluate the faculty member
                    involved by encircling the number that
                    corresponds
                    to the given parameter/dimensions at the
                    scale of 5,
                    where <span class="fw-bold">five is the
                      perfect
                      score</span> and <span class="fw-bold">one
                      is the
                      lowest score.</span>
                  </div>


                  <?php
                  $enrollAYS = "SELECT * FROM `academic_year_semester` WHERE id =1";
                  $enrollAYS_query = mysqli_query($con, $enrollAYS);
                  $AYS = mysqli_fetch_assoc($enrollAYS_query);

                  ?>
                  <table class="table table-striped table-bordered text-center align-middle">
                    <thead class="">
                      <tr>
                        <th style="color: #000 !important;">
                          <div class="d-flex align-items-center">
                            <label for="courseInput" class="mr-2 mb-0">Faculty
                              Name:</label>
                            <input type="text" name="toFaculty" id="instructorName" readonly
                              class="form-control no-border" style="flex: 1;">
                          </div>
                        </th>
                        <th style="color: #000 !important;">
                          <div class="d-flex align-items-center">
                            <label for="semesterInput" class="mr-2 mb-0">Semester:</label>
                            <input type="text" id="semester" name="semester" class="form-control no-border" readonly
                              value="<?php echo $AYS['semester'] ?>" style="flex: 1;">
                          </div>
                        </th>
                        <th style="color: #000 !important;">
                          <div class="d-flex align-items-center">
                            <label for="academic" class="mr-2 mb-0">Academic
                              Year:</label>
                            <input type="text" id="academic" name="academic_year" class="form-control no-border"
                              readonly value="<?php echo $AYS['academic_year'] ?>" style="flex: 1;">
                          </div>
                        </th>
                      </tr>



                    </thead>
                  </table>
                  <table class="table table-striped table-bordered text-center align-middle">

                    <thead>
                      <tr class="bg-danger text-uppercase">
                        <th class="align-middle text-center">Numerical
                          Rating</th>
                        <th class="align-middle text-center">Descriptive
                          Rating</th>
                        <th class="align-middle text-center">Qualitative
                          Description</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td>5.0</td>
                        <td>Outstanding</td>
                        <td>Exhibits the behavior
                          described
                          at all
                          times when the occasion
                          occurs.</td>
                      </tr>
                      <tr>
                        <td>4.0</td>
                        <td>Very Satisfactory</td>
                        <td>Exhibits the behavior
                          described
                          most of
                          the time when the occasion
                          occurs.</td>
                      </tr>
                      <tr>
                        <td>3.0</td>
                        <td>Satisfactory</td>
                        <td>Exhibits the behavior
                          described
                          sometimes when the occasion
                          occurs.</td>
                      </tr>
                      <tr>
                        <td>2.0</td>
                        <td>Fair</td>
                        <td>EExhibits the behavior
                          described
                          rarely
                          when the occasion
                          occurs.</td>
                      </tr>
                      <tr>
                        <td>1.0</td>
                        <td>Poor</td>
                        <td>Exhibits the behavior
                          described
                          has not
                          been exhibited at all times
                          when
                          the
                          occasion occurs.</td>
                      </tr>

                    </tbody>
                  </table>
                </div>

                <!-- HIDDEN INPUTS -->
                <div style="display: none;">
                  <input type="text" name="fromStudents" value="">
                  <input type="text" id="facultyID" name="toFacultyID">
                  <input type="date" id="dateInput" name="date" required>
                  <input type="text" id="enroll" name="enrolled">
                  <input type="text" id="subjectID" name="subject">
                </div>

                <?php

                $sql = "SELECT * FROM `studentscategories`";
                $result = mysqli_query($con, $sql);


                if ($result) {
                  $totalCategories = mysqli_num_rows($result);
                } else {
                  die("Query failed: " . mysqli_error($con));
                }
                function sanitizeColumnName($name)
                {
                  return preg_replace('/[^a-zA-Z0-9_]/', '_', trim($name));
                }

                if ($result) {
                  $stepIndex = 2;
                  while ($row = mysqli_fetch_array($result)) {
                    $categories = $row['categories'];

                    echo '
                      <div class="step step-' . $stepIndex . '">
                        <h3>' . htmlspecialchars($categories) . '</h3>
                        <table class="table table-striped table-bordered text-center align-middle">
                          <thead>
                            <tr class="bg-danger text-uppercase">
                                <th class="text-justify">No.</th>
                                <th style="text-align: center; margin: 0;">Criteria</th>
                                <th class="text-justify">Interpretation</th>
                            </tr>
                          </thead>
                          <tbody>
                    ';

                    $sqlcriteria = "SELECT * FROM `studentscriteria` WHERE studentsCategories = '$categories'";
                    $resultCriteria = mysqli_query($con, $sqlcriteria);

                    if ($resultCriteria) {
                      $criteriaCount = 1;
                      while ($criteriaRow = mysqli_fetch_array($resultCriteria)) {

                        $columnName = sanitizeColumnName($criteriaRow['studentsCategories']) . $criteriaRow['id'];

                        echo '
                          <tr>
                            <td>' . $criteriaCount++ . '</td>
                            <td>
                              <p style="text-align: justify; margin: 0;">
                                ' . htmlspecialchars($criteriaRow['studentsCriteria']) . '
                              </p>

                              <div class="rating d-flex justify-content-center mt-1">
                                <input id="rating-5-' . $columnName . '" type="radio" name="' . htmlspecialchars($columnName) . '" value="5" />
                                <label for="rating-5-' . $columnName . '" class="ratingLabel"><i class="fas fa-3x fa-star fa-2xl my-3"></i></label>
                                                                
                                <input id="rating-4-' . $columnName . '" type="radio" name="' . htmlspecialchars($columnName) . '" value="4" />
                                <label for="rating-4-' . $columnName . '" class="ratingLabel"><i class="fas fa-3x fa-star fa-2xl my-3"></i></label>
                                                                
                                <input id="rating-3-' . $columnName . '" type="radio" name="' . htmlspecialchars($columnName) . '" value="3" />
                                <label for="rating-3-' . $columnName . '" class="ratingLabel"><i class="fas fa-3x fa-star fa-2xl my-3"></i></label>
                                                                
                                <input id="rating-2-' . $columnName . '" type="radio" name="' . htmlspecialchars($columnName) . '" value="2" />
                                <label for="rating-2-' . $columnName . '" class="ratingLabel"><i class="fas fa-3x fa-star fa-2xl my-3"></i></label>
                                                                
                                <input id="rating-1-' . $columnName . '" type="radio" name="' . htmlspecialchars($columnName) . '" value="1" />
                                <label for="rating-1-' . $columnName . '" class="ratingLabel"><i class="fas fa-3x fa-star fa-2xl my-3"></i></label>
                              </div>
                            </td>
                            <td>
                              <div id="ratingDisplay-' . $columnName . '" class="text-center">
                                <span id="ratingValue-' . $columnName . '">Rate First</span>
                              </div>
                            </td>
                          </tr>
                        ';
                      }
                    } else {
                      die("Error fetching criteria: " . mysqli_error($con));
                    }
                    echo '
                          </tbody>
                        </table>
                      </div>
                    ';
                    $stepIndex++;
                  }
                }
                ?>

                <div class="mb-3 comment">
                  <label for="">Comment:</label>
                  <textarea class="form-control" id="criteriaText" name="comment" rows="3" required></textarea>
                </div>

                <div class="navigation-buttons p-1">
                  <button type="button" class="btn btn-secondary close-step mx-2" data-bs-dismiss="modal">Close</button>
                  <button type="button" class="btn btn-secondary prev-step mx-2"
                    style="display: none;">Previous</button>
                  <button type="button" class="btn btn-primary next-step mx-2">Next</button>
                  <button type="submit" name="studentSubmit" class="btn btn-success mx-2"
                    style="display: none;">Submit</button>
                </div>

              </form>
            </div>
          </div>
        </div>
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
  <div class="modal fade" id="enroll_modal">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header bg-danger">
          <h5 class="modal-title text-light" id="enroll_modalLabel">Online Registration</h5>
          <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="p-3">
            <div class="pb-3">
              <h4 class="text-warning">Total Unit: <span id="unit"></span>/<span id="maxunit"></span></h4>
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

  <!-- START MAJOR MODAL -->
  <div class="modal fade" id="major_modal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-danger">
          <h5 class="modal-title text-light" id="enroll_modalLabel">Choose your Major</h5>
          <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="../controller/controller.php" method="POST">
            <div class="p-3">
              <div class="row mb-3">
                <label for="srcode" class="form-label fw-bold">SR_CODE:</label>
                <input type="text" name="srcode" class="form-control" value="<?php echo $_SESSION['studentSRCode'] ?>"
                  readonly>
              </div>
              <div class="row">
                <label for="major" class="form-label fw-bold">MAJOR:</label>
                <select name="major" class="form-select">
                  <option value="selected" selected>---Select Your major---</option>
                  <option value="Service Management">Service Management</option>
                  <option value="Network Technology">Network Technology</option>
                  <option value="Business Analytics">Business Analytics</option>
                </select>
              </div>
            </div>
        </div>
        <div class="modal-footer d-flex justify-content-between">

          <div>
            <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><i
                class="fa-regular fa-circle-xmark"></i> Close</button>
            </div>
            <div>
              <button type="submit" id="submitMajor" name="submitMajor" class="btn btn-success"><i class="fa-solid fa-floppy-disk"></i> Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- END MAJOR MODAL -->

  <script>
    $(document).ready(function () {
      $('input[type="radio"]').click(function () {
        const groupName = $(this).attr('name');
        const selectedValue = $(this).val();
        const displayText = $('#ratingValue-' + groupName);

        switch (selectedValue) {
          case '1':
            displayText.text('Poor');
            break;
          case '2':
            displayText.text('Fair');
            break;
          case '3':
            displayText.text('Satisfactory');
            break;
          case '4':
            displayText.text('Very Satisfactory');
            break;
          case '5':
            displayText.text('Outstanding');
            break;
          default:
            displayText.text('None');
        }
      });

      var currentStep = 1;
      var totalSteps = <?php echo $totalCategories + 1; ?>;

      $('#multi-step-form .step').slice(1).hide();

      function updateProgressBar() {
        var progressPercentage = ((currentStep - 1) / (totalSteps - 1)) * 100;
        $(".progress-bar").css("width", progressPercentage + "%").attr("aria-valuenow", progressPercentage);
      }

      function displayStep(stepNumber) {
        if (stepNumber >= 1 && stepNumber <= totalSteps) {
          $(".step").hide();
          $(".step-" + stepNumber).show();
          currentStep = stepNumber;
          updateProgressBar();
          $("#current-step").text("Step " + currentStep);


          if (currentStep === totalSteps) {
            $(".navigation-buttons .next-step").hide();
            $(".navigation-buttons .btn-success").show();
            $(".comment").show();
          } else {
            $(".navigation-buttons .next-step").show();
            $(".navigation-buttons .btn-success").hide();
            $(".comment").hide();
          }

          if (currentStep === 1) {
            $(".navigation-buttons .prev-step").hide();
            $(".navigation-buttons .close-step").show();
          } else {
            $(".navigation-buttons .prev-step").show();
            $(".navigation-buttons .close-step").hide();
          }
        }
      }

      $(".next-step").click(function () {
        if (currentStep < totalSteps) {
          currentStep++;
          displayStep(currentStep);
        }
      });

      $(".prev-step").click(function () {
        if (currentStep > 1) {
          currentStep--;
          displayStep(currentStep);
        }
      });

      displayStep(currentStep);

      $('.evaluate-btn').on('click', function () {
        var instructorFirstName = $(this).data('first-name');
        var instructorLastName = $(this).data('last-name');
        var enrolledId = $(this).data('enrolled-id');
        var facultyId = $(this).data('id');
        var subjectId = $(this).attr('date-subject-id');

        // Set values for form inputs
        $('#facultyID').val(facultyId);
        $('#subjectID').val(subjectId);
        $('#enroll').val(enrolledId);
        $('#instructorName').val(instructorFirstName + ' ' + instructorLastName);


      });
    });

    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');

    const formattedDate = `${year}-${month}-${day}`;

    document.getElementById('dateInput').value = formattedDate;

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


</body>

</html>
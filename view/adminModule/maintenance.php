<?php
// NAV BAR
include "components/navBar.php"

  ?>

<head>
  <link rel="stylesheet" href="../../fontawesome/css/all.min.css">

  <!-- SWEETALERT2 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!--  -->

  <!-- TITLE WEB PAGE -->
  <title>Peer to Peer Faculty Evaluation</title>

  <!-- ALL STYLES, CSS AND SCRIPTS -->
  <link rel="stylesheet" href="../../public/css/style.css">
  <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
  <script src="../../public/js/jquery-3.7.1.min.js"></script>
  <style>
    ul li {
      list-style: none;
    }

    .star {
      color: gold;
      font-size: 30px;
    }
  </style>
</head>

<!-- CONTENT CONTAINER -->
<section class="contentContainer">
  <!-- START TAB -->
  <ul class="nav nav-tabs" id="myTab" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="AddStudent-tab" data-bs-toggle="tab" data-bs-target="#AddStudent"
        type="button" role="tab" aria-controls="AddStudent" aria-selected="false"><i class="fa-solid fa-user-plus"></i>
        Add Student</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="AssignSub-tab" data-bs-toggle="tab" data-bs-target="#AssignSub" type="button"
        role="tab" aria-controls="AssignSub" aria-selected="true"><i class="fa-solid fa-book-open-reader"></i> Assign
        Subject</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="AddSub-tab" data-bs-toggle="tab" data-bs-target="#AddSub" type="button" role="tab"
        aria-controls="AddSub" aria-selected="false"><i class="fa-solid fa-book"></i> Add Subject</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="AddSec-tab" data-bs-toggle="tab" data-bs-target="#AddSec" type="button" role="tab"
        aria-controls="AddSec" aria-selected="false"><i class="fa-solid fa-users-between-lines"></i> Add
        Section</button>
    </li>
  </ul>
  <!-- END TAB -->

  <!-- START TAB BODY -->
  <div class="tab-content" id="myTabContent">

    <!--  -->
    <div class="tab-pane fade show active" id="AddStudent" role="tabpanel" aria-labelledby="AddStudent-tab">
    </div>
    <!--  -->

    <!-- START ASSIGN SUB -->
    <div class="tab-pane fade" id="AssignSub" role="tabpanel" aria-labelledby="AssignSub-tab">
      <div class="container p2">
        <div class="mt-3 d-flex justify-content-between">
          <h3 class=""><i class="fa-solid fa-book-open-reader"></i> Assign Subject</h3>
          <div>
            <button class="btn btn-success d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#assigned">
              <i class="fa-solid fa-plus"></i> Assign Subject
            </button>
          </div>
        </div>

        <table class="table table-striped table-bordered text-center mt-5" style="font-family: monospace">
          <thead>
            <tr class="text-dark">
              <th class="text-dark">Code</th>
              <th class="text-dark">Description</th>
              <th class="text-dark">Unit</th>
              <th class="text-dark">Section</th>
              <th class="text-dark">Instructor</th>
              <th class="text-dark">Schedule</th>
              <th class="text-dark">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php
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
                              time TE2 ON A.E_time_id_2 = TE2.time_id";

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
                  <td><button type="button" class="btn btn-primary"><i class="fa-solid fa-pen-to-square"></i> Edit</button>
                  </td>


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
    </div>
    <!-- START ASSIGNED SUBJECT MODAL -->
    <div class="modal fade" id="assigned" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header bg-danger">
            <h5 class="modal-title text-light" id="enroll_modalLabel">ASSIGNED INSTRUCTOR</h5>
            <button type="button" class="btn-close bg-light" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="container">
              <form action="../../controller/controller.php" method="POST">
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
                              <option value="<?php echo $row_sub['subject_id'] ?>"><?php echo $row_sub['subject'] ?>
                              </option>
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
                              <option value="<?php echo $row_sub['subject_id'] ?>"><?php echo $row_sub['subject'] ?>
                              </option>
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
                              <option value="<?php echo $row_sub['subject_id'] ?>"><?php echo $row_sub['subject'] ?>
                              </option>
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
                              <option value="<?php echo $row_sub['subject_id'] ?>"><?php echo $row_sub['subject'] ?>
                              </option>
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
                              <option value="<?php echo $row_sub['subject_id'] ?>"><?php echo $row_sub['subject'] ?>
                              </option>
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
                              <option value="<?php echo $row_sub['subject_id'] ?>"><?php echo $row_sub['subject'] ?>
                              </option>
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
                              <option value="<?php echo $row_sub['subject_id'] ?>"><?php echo $row_sub['subject'] ?>
                              </option>
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
                              <option value="<?php echo $row_sub['subject_id'] ?>"><?php echo $row_sub['subject'] ?>
                              </option>
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
    <!-- END ASSIGNED SUBJECT MODAL -->
    <!-- END ASSIGN SUB -->

    <!--  -->
    <div class="tab-pane fade" id="AddSub" role="tabpanel" aria-labelledby="AddSub-tab">
    </div>
    <!--  -->

    <!--  -->
    <div class="tab-pane fade" id="AddSec" role="tabpanel" aria-labelledby="AddSec-tab">
    </div>
    <!--  -->

  </div>
  <!-- END TAB BODY -->


</section>


<script src="../../public/js/jquery-3.7.1.min.js"></script>
<script>
  //START ASSIGN SUBJECT JS
  // GET SECTION DEPENDING ON WHAT IS SELECTED SUBJECT
  $("#sub_id").on("change", function () {
    var subject = $(this).val();

    if (subject) {
      $.ajax({
        url: "../../controller/getSection.php",
        type: "POST",
        data: { sub_id: subject },
        success: function (response) {
          $("#sec_id").html(response);
        },
      });
    } else {
      $("#sec_id").html(
        '<option value="selected" selected disabled>---Select Section---</option>'
      );
    }
  });

  //GET END TIME 3HRS MAX TIME
  $("#Stime").on("change", function () {
    var Stime = $(this).val();

    if (Stime) {
      $.ajax({
        url: "../../controller/getEtime.php",
        type: "POST",
        data: { stime: Stime },
        success: function (response) {
          $("#Etime").html(response);
        },
      });
    } else {
      $("#Etime").html(
        '<option value="selected" selected disabled>---Select end time---</option>'
      );
    }
  });

  //SHOW SCHEDULE PART IF SECTION DROPDOWN HAS BEEN SELECTED
  $("#sec_id").on("change", function () {
    $("#sched_part").css("display", "flex");
    $("#day, #day2").val("selected");
    $("#Stime, #Stime2").html(
      '<option value="selected" selected disabled>---Select start time---</option>'
    );
    $("#Etime, #Etime2").html(
      '<option value="selected" selected disabled>---Select end time---</option>'
    );
  });
  $("#sub_id").on("change", function () {
    $("#day, #day2").val("selected");
    $("#Stime, #Stime2").html(
      '<option value="selected" selected disabled>---Select start time---</option>'
    );
    $("#Etime, #Etime2").html(
      '<option value="selected" selected disabled>---Select end time---</option>'
    );
  });
  $("#sched2").on("click", function () {
    $("#sched_part2").css("display", "flex");
  });

  //CHECK FOR STRAT TIME IF ALREADY TAKEN
  $("#day").on("change", function () {
    var day = $(this).val();
    var section = $("#sec_id").val();

    if (day) {
      $.ajax({
        url: "../../controller/getStime.php",
        type: "POST",
        data: { day: day, section: section },
        success: function (response) {
          $("#Stime").html(response);
        },
      });
    } else {
      $("#Stime").html(
        '<option value="selected" selected disabled>---Select start time---</option>'
      );
    }
  });

  //GET INSTRUCTOR THAT DOES NOT BEING ASSIGNED ON THE SELECTED SUBJECT
  $("#sub_id").on("change", function () {
    var sub_id = $(this).val();

    if (sub_id) {
      $.ajax({
        url: "../../controller/getInstructor.php",
        type: "POST",
        data: { sub_id: sub_id },
        success: function (response) {
          $("#fclty_id").html(response);
        },
      });
    } else {
      $("#fclty_id").html(
        '<option value="selected" selected disabled>---Select Instructor---</option>'
      );
    }
  });

  //REMOVE THE SELECTED TIME ON THE FIRSTH SCHEDULE
  $("#day2").on("change", function () {
    var Etime = $("#Etime").val();
    var Stime = $("#Stime").val();
    var day = $("#day2").val();
    var section = $("#sec_id").val();

    // $('#sched2').attr('disabled', false);

    if (Etime) {
      $.ajax({
        url: "../../controller/getStime2.php",
        type: "POST",
        data: {
          Etime: Etime,
          Stime: Stime,
          day: day,
          section: section,
        },
        success: function (response) {
          $("#Stime2").html(response);
        },
      });
    } else {
      $("#Stime2").html(
        '<option value="selected" selected disabled>---Select start time---</option>'
      );
    }
  });
  $("#Stime2").on("change", function () {
    var Stime = $(this).val();

    if (Stime) {
      $.ajax({
        url: "../../controller/getEtime.php",
        type: "POST",
        data: { stime: Stime },
        success: function (response) {
          $("#Etime2").html(response);
        },
      });
    } else {
      $("#Etime2").html(
        '<option value="selected" selected disabled>---Select end time---</option>'
      );
    }
  });
  //END OF ASSIGN SUBJECT JS
</script>
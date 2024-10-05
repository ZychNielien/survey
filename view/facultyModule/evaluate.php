<?php

include "components/navBar.php";
include "../../model/dbconnection.php";
?>

<head>
    <title>Evaluation Result</title>
    <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../public/css/animate.min.css">
    <link rel="stylesheet" href="../../fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../../public/css/style.css">

    <script src="../../public/js/jquery-3.7.1.min.js"></script>
    <script src="../../bootstrap/js/bootstrap.min.js"></script>
</head>

<style>
    .no-border {
        border: none;
        /* Remove border */
        box-shadow: none;
        /* Remove shadow/outline on focus */
    }

    .form-control[readonly] {
        background-color: transparent;
        /* Set background color to transparent */
        color: #000;
        /* Maintain text color for readability */
    }

    .form-control:focus {
        box-shadow: none;
        /* Remove outline on focus */
        border-color: transparent;
        /* Ensure border stays transparent */
    }
</style>
<section class="contentContainer">
    <div class="contentTitle d-flex justify-content-center">
        <h2 class="text-danger fw-bold">Peer to Peer Evaluation</h2>
    </div>


    <nav>
        <div class="nav nav-tabs mb-3" id="nav-tab" role="tablist">
            <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home"
                type="button" role="tab" aria-controls="nav-home" aria-selected="true">Peer to Peer Evaluation</button>
            <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile"
                type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Archived Peer to Peer
                Evaluation</button>
        </div>
    </nav>
    <div class="tab-content p-3 border bg-light overflow-auto" id="nav-tabContent">


        <div class="tab-pane fade active show" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">
            <div class="contentTable">
                <table class="table table-striped table-bordered text-center align-middle w-100">
                    <thead>
                        <tr>
                            <th style=" background: #d0112b;
            color: #fff; letter-spacing: 2px;">Image</th>
                            <th style=" background: #d0112b;
            color: #fff; letter-spacing: 2px;">Faculty Member</th>
                            <th style=" background: #d0112b;
            color: #fff; letter-spacing: 2px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $facultyID = $userRow["faculty_Id"];

                        // Query to get random faculty IDs based on the user's faculty ID
                        $sqlrandom = "SELECT * FROM `randomfaculty` WHERE faculty_Id = '$facultyID' AND doneStatus=0";
                        $sqlrandom_query = mysqli_query($con, $sqlrandom);

                        if (mysqli_num_rows($sqlrandom_query)) {
                            while ($randomF = mysqli_fetch_assoc($sqlrandom_query)) {
                                $randomFID = $randomF['random_Id'];

                                // Query to get instructors based on the random faculty ID
                                $instructorsql = "SELECT * FROM `instructor` WHERE faculty_Id = '$randomFID'";
                                $instructorsql_query = mysqli_query($con, $instructorsql);

                                if (mysqli_num_rows($instructorsql_query) > 0) {
                                    while ($instructorRow = mysqli_fetch_assoc($instructorsql_query)) {
                                        // Output the table row for each instructor
                                        echo '
                                    <tr>
                                        <td><img src="../' . htmlspecialchars($instructorRow['image']) . '" style="height: 120px;"></td>
                                        <td>
                                            ' . htmlspecialchars($instructorRow['first_name']) . ' ' . htmlspecialchars($instructorRow['last_name']) . '
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-success m-2 evaluate-btn" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#evaluationForm" 
                                                data-id="' . htmlspecialchars($instructorRow['faculty_Id']) . '"
                                                data-semester="' . htmlspecialchars($randomF['semester']) . '"
                                                data-academic="' . htmlspecialchars($randomF['academic_year']) . '"
                                                data-first-name="' . htmlspecialchars($instructorRow['first_name']) . '"
                                                data-last-name="' . htmlspecialchars($instructorRow['last_name']) . '">
                                                Evaluate
                                            </button>
                                        </td>
                                    </tr>
                                ';
                                    }

                                }
                            }
                        } else {
                            echo '
                        <tr>
                            <td colspan="3" class="text-center py-5 text-danger"><h1>The peer-to-peer evaluation is CLOSED.</h1></td>
                        </tr>
                    ';
                        }
                        ?>
                    </tbody>
                </table>

                <div class="modal fade" id="evaluationForm" tabindex="-1" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header  bg-danger text-white">
                                <h5 class="modal-title text-center text-white" id="exampleModalLabel">FACULTY
                                    PEER TO PEER EVALUATION INSTRUMENT
                                    FOR FACULTY DEVELOPMENT</h5>
                                <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div id="container" class="container">

                                    <form id="multi-step-form" method="POST" action="../../controller/criteria.php">
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

                                            <table class="table table-striped table-bordered text-center align-middle">
                                                <thead class="">
                                                    <tr>
                                                        <th style="color: #000 !important;">
                                                            <div class="d-flex align-items-center">
                                                                <label for="courseInput" class="mr-2 mb-0">Faculty
                                                                    Name:</label>
                                                                <input type="text" name="toFaculty" id="instructorName"
                                                                    readonly class="form-control no-border"
                                                                    style="flex: 1;">
                                                            </div>
                                                        </th>
                                                        <th style="color: #000 !important;">
                                                            <div class="d-flex align-items-center">
                                                                <label for="semesterInput"
                                                                    class="mr-2 mb-0">Semester:</label>
                                                                <input type="text" id="semester" name="semester"
                                                                    class="form-control no-border" readonly
                                                                    style="flex: 1;">
                                                            </div>
                                                        </th>
                                                        <th style="color: #000 !important;">
                                                            <div class="d-flex align-items-center">
                                                                <label for="academic" class="mr-2 mb-0">Academic
                                                                    Year:</label>
                                                                <input type="text" id="academic" name="academic_year"
                                                                    class="form-control no-border" style="flex: 1;">
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
                                            <input type="text" class="form-control" name="doneStatus" value="1"
                                                readonly>
                                            <input type="text" class="form-control" name="random_Id" id="randomID"
                                                readonly>
                                            <input type="text" name="fromFaculty"
                                                value="<?php echo $userRow["first_name"] . ' ' . $userRow['last_name'] ?>">
                                            <input type="text" name="faculty_Id"
                                                value="<?php echo $userRow["faculty_Id"] ?>">

                                            <input type="date" id="dateInput" name="date" required>
                                        </div>
                                        <?php

                                        $sql = "SELECT * FROM `facultycategories`";
                                        $result = mysqli_query($con, $sql);

                                        // Check if query was successful
                                        if ($result) {
                                            $totalCategories = mysqli_num_rows($result); // Count total categories
                                        } else {
                                            die("Query failed: " . mysqli_error($con));
                                        }
                                        function sanitizeColumnName($name)
                                        {
                                            // Palitan ang whitespace at mga invalid characters
                                            return preg_replace('/[^a-zA-Z0-9_]/', '_', trim($name));
                                        }
                                        if ($result) {
                                            $stepIndex = 2; // Initialize step index
                                            while ($row = mysqli_fetch_array($result)) {
                                                $categories = $row['categories'];

                                                // Step for each category
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

                                                // Fetching criteria for the current category only once
                                                $sqlcriteria = "SELECT * FROM `facultycriteria` WHERE facultyCategories = '$categories'";
                                                $resultCriteria = mysqli_query($con, $sqlcriteria);

                                                if ($resultCriteria) {
                                                    $criteriaCount = 1; // Counter for criteria numbering
                                                    while ($criteriaRow = mysqli_fetch_array($resultCriteria)) {
                                                        // Use the criteria ID for concatenation
                                                        $columnName = sanitizeColumnName($criteriaRow['facultyCategories']) . $criteriaRow['id'];

                                                        echo '
                                                    <tr>
                                                        <td>' . $criteriaCount++ . '</td>
                                                        <td>
                                                            <p style="text-align: justify; margin: 0;">
                                                                ' . htmlspecialchars($criteriaRow['facultyCriteria']) . '
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

                                                $stepIndex++; // Increment step index
                                            }
                                        }
                                        ?>
                                        <div class="mb-3 comment">
                                            <label for="">Comment:</label>
                                            <textarea class="form-control" id="criteriaText" name="commentText" rows="3"
                                                required></textarea>
                                        </div>
                                        <div class="navigation-buttons p-1">
                                            <button type="button" class="btn btn-secondary close-step mx-2"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="button" class="btn btn-secondary prev-step mx-2"
                                                style="display: none;">Previous</button>
                                            <button type="button" class="btn btn-primary next-step mx-2">Next</button>
                                            <button type="submit" name="submitData" class="btn btn-success mx-2"
                                                style="display: none;">Submit</button>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-pane fade " id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">

            <div class="form-row mb-3 d-flex justify-content-center align-items-center">
                <div class="col-auto mx-3">
                    <input type="text" id="usernameInput" class="form-control" placeholder="Enter Full Name">
                </div>
                <div class="col-auto mx-3">
                    <select id="semesterSelect" class="form-control">
                        <option value="">Select Semester</option>
                        <option value="1st">1st Semester</option>
                        <option value="2nd">2nd Semester</option>
                    </select>
                </div>
                <div class="col-auto mx-3">
                    <select id="yearSelect" class="form-control">
                        <option value="">Select Academic Year</option>
                        <?php
                        $currentYear = date("Y");
                        $nextYear = $currentYear + 3;

                        for ($year = $currentYear; $year <= $nextYear; $year++) {
                            echo "<option value='$year-" . ($year + 1) . "'>$year - " . ($year + 1) . "</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-auto mx-3">
                    <button id="filterButton" class="btn btn-primary">Filter</button>
                </div>
            </div>



            <div class="overflow-auto" style="max-height: 520px">
                <table class="table table-striped table-bordered text-center align-middle w-100">
                    <thead>
                        <tr class="bg-danger text-white">
                            <th>Faculty Evaluated</th>
                            <th>Semester</th>
                            <th>Academic Year</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php
                        $UserID = $userRow['faculty_Id'];
                        $archivedSQL = "SELECT * FROM `peertopeerform` WHERE fromFacultyID = '$UserID'";
                        $archivedSQL_query = mysqli_query($con, $archivedSQL);

                        if (mysqli_num_rows($archivedSQL_query) > 0) {
                            while ($archivedRow = mysqli_fetch_assoc($archivedSQL_query)) {
                                echo '
                        <tr>
                            <td>' . $archivedRow['toFaculty'] . '</td>
                            <td>' . $archivedRow['semester'] . '</td>
                            <td>' . $archivedRow['academic_year'] . '</td>
                        </tr>
                        ';
                            }


                            ?>

                        </tbody>
                    </table>

                    <?php
                        } else {
                            echo '
                            <h1  class="text-center text-danger">There have been no peer-to-peer evaluations answered yet.</h1>
                            ';
                        }
                        ?>
                <h1 id="noResults" class="text-center text-danger" style="display: none;">No results found</h1>
            </div>

        </div>
    </div>


    <div class="modal fade bg-transparent opacity-1" id="officialviewmodal" style="opacity: transparent" tabindex="-1"
        role="dialog" aria-labelledby="officialviewmodalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">

                <div class="modal-body officialviewmodal">
                    <!-- Content will be populated by AJAX -->
                </div>

            </div>
        </div>
    </div>


























</section>


<script>
    $(document).ready(function () {

        // Handle radio button click events for rating display
        $('input[type="radio"]').click(function () {
            const groupName = $(this).attr('name');
            const selectedValue = $(this).val();
            const displayText = $('#ratingValue-' + groupName);

            // Update display text based on the selected value
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

        // Handle multi-step form logic
        var currentStep = 1;
        var totalSteps = <?php echo $totalCategories + 1; ?>; // PHP variable

        // Hide all steps except the first one
        $('#multi-step-form .step').slice(1).hide();

        function updateProgressBar() {
            var progressPercentage = ((currentStep - 1) / (totalSteps - 1)) * 100;
            $(".progress-bar").css("width", progressPercentage + "%").attr("aria-valuenow", progressPercentage);
        }

        function displayStep(stepNumber) {
            if (stepNumber >= 1 && stepNumber <= totalSteps) {
                $(".step").hide(); // Hide all steps
                $(".step-" + stepNumber).show(); // Show current step
                currentStep = stepNumber;
                updateProgressBar(); // Update progress bar
                $("#current-step").text("Step " + currentStep); // Update step display

                // Navigation button visibility
                if (currentStep === totalSteps) {
                    $(".navigation-buttons .next-step").hide();
                    $(".navigation-buttons .btn-success").show();
                    $(".comment").show(); // Show submit on last step
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

        // Handle next step click
        $(".next-step").click(function () {
            if (currentStep < totalSteps) {
                currentStep++;
                displayStep(currentStep);
            }
        });

        // Handle previous step click
        $(".prev-step").click(function () {
            if (currentStep > 1) {
                currentStep--;
                displayStep(currentStep);
            }
        });

        // Initialize display for the first step
        displayStep(currentStep);

        // Handle Evaluate button click to display instructor's name in the input field
        $('.evaluate-btn').on('click', function () {
            var instructorFirstName = $(this).data('first-name');
            var instructorLastName = $(this).data('last-name');
            var random = $(this).data('id');
            var semester = $(this).data('semester');
            var academic = $(this).data('academic');

            // Combine first and last names and set the value in the input field
            $('#instructorName').val(instructorFirstName + ' ' + instructorLastName);
            $('#randomID').val(random);
            $('#semester').val(semester);
            $('#academic').val(academic);
        });
    });

    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0'); // Months are zero-based
    const day = String(today.getDate()).padStart(2, '0');

    // Format the date as YYYY-MM-DD
    const formattedDate = `${year}-${month}-${day}`;

    // Set the value of the date input to today's date
    document.getElementById('dateInput').value = formattedDate;


    // FILTER 
    $("#filterButton").on("click", function () {
        var username = $("#usernameInput").val().toLowerCase();
        var semester = $("#semesterSelect").val();
        var year = $("#yearSelect").val();

        $("#tableBody tr").show(); // Show all rows initially
        $("#noResults").hide(); // Hide the no results message initially

        var filteredRows = $("#tableBody tr").filter(function () {
            var usernameMatch = username === "" || $(this).find("td:eq(0)").text().toLowerCase().indexOf(username) > -1;
            var semesterMatch = semester === "" || $(this).find("td:eq(1)").text().includes(semester);
            var yearMatch = year === "" || $(this).find("td:eq(2)").text() === year;
            return !(usernameMatch && semesterMatch && yearMatch);
        });

        filteredRows.hide(); // Hide rows that do not match

        // Check if there are any remaining visible rows
        if ($("#tableBody tr:visible").length === 0) {
            $("#noResults").show(); // Show the no results message
        }
    });
</script>
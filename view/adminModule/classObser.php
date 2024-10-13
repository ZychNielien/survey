<?php

include "components/navBar.php";

?>

<head>

    <!-- WEBSITE TITLE -->
    <title>View Classroom Observation</title>

    <!-- ALL STYLES OR CSS FILES -->
    <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/sweetalert.min.css">

    <!-- SCRIPT -->
    <script src="../../public/js/sweetalert2@11.js"></script>

    <style>
        .no-border {
            border: none;
            box-shadow: none;
        }

        .form-control[readonly] {
            background-color: transparent;
            color: #000;
            padding: 0 !important;
            margin: 0;
        }

        .form-control {
            background-color: transparent;
            color: #000;
            padding: 0 !important;
            margin: 0;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: transparent;
        }
    </style>

    <!-- ALL SCRIPTS -->
    <script src="../../public/js/jquery-3.7.1.min.js"></script>
    <script src="../../bootstrap/js/bootstrap.bundle.min.js"></script>

</head>

<!-- CONTENT CONTAINER -->
<section class="contentContainer px-1">


    <nav>
        <div class="nav nav-tabs mb-3" id="nav-tab" role="tablist">
            <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home"
                type="button" role="tab" aria-controls="nav-home" aria-selected="true">Classroom Observation</button>
            <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile"
                type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Classroom Observation
                Results</button>
        </div>
    </nav>
    <div class="tab-content p-3 border shadow-md overflow-auto" id="nav-tabContent">
        <div class="tab-pane fade active show" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">

            <!-- DATE SELECTION -->
            <div class="row justify-content-center align-items-center">
                <div class="row justify-content-center align-items-center ">
                    <div class="col-md-6 col-lg-4">
                        <div class="p-4 rounded-4">
                            <div class="text-center">
                                <label for="view-date-select" class="form-label fw-bold text-danger">Select
                                    Date:</label>
                                <input type="date"
                                    class="form-control form-control-lg shadow-sm rounded-3  border-2 border-danger"
                                    id="view-date-select" style="background-color: #ffe5e5;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CLASSROOM OBSERVATION TABLE -->
            <table id="view-reservation-table" class="table table-bordered mt-2 "
                style="text-align: center; vertical-align: middle;"></table>

            <!-- CLASSROOM OBSERVATION FORM -->
            <div class="modal fade" id="bookingDetailsModal" tabindex="-1" aria-labelledby="bookingDetailsModalLabel"
                aria-hidden="true">

                <div class="modal-dialog modal-xl modal-dialog-centered">

                    <div class="modal-content">

                        <!-- CLASSROOM OBSERVATION FORM HEADER -->
                        <div class="modal-header  bg-danger text-white">
                            <h5 class="modal-title text-center text-white" id="exampleModalLabel">
                                CLASSROOM OBSERVATION FORM</h5>
                            <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <!-- CLASSROOM OBSERVATION FORM CONTENT -->
                        <div class="modal-body">

                            <div id="container" class="container">

                                <form id="multi-step-form" method="POST" action="../../controller/criteria.php">

                                    <div class="step step-1">

                                        <table class="table table-bordered mt-2 "
                                            style="text-align: center; vertical-align: middle;">
                                            <thead>
                                                <tr>
                                                    <th colspan="2" class="col-6" style="color: #000 !important;">
                                                        <div class="d-flex justify-content-between">
                                                            <div class="tableText w-25 text-justify"
                                                                style="text-align: justify;">
                                                                <label for="courseInput">Course Title:</label>

                                                            </div>
                                                            <div class="tableInput w-75">
                                                                <input type="text" id="courseInput"
                                                                    class="tableInputs w-100 text-uppercase no-border form-control"
                                                                    name="courseTitle" readonly>
                                                            </div>
                                                        </div>
                                                    </th>
                                                    <th colspan="2" class="col-6" style="color: #000 !important;">
                                                        <div class="d-flex justify-content-between">
                                                            <div class="tableText w-25 text-justify"
                                                                style="text-align: justify;">
                                                                <label for="facultyInput">Instructor:</label>
                                                            </div>
                                                            <div class="tableInput w-75">
                                                                <input type="text"
                                                                    class="tableInputs w-100 text-uppercase no-border form-control"
                                                                    id="facultyInput" name="toFaculty" readonly>
                                                            </div>
                                                        </div>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th colspan="2" class="col-6" style="color: #000 !important;">
                                                        <div class="d-flex justify-content-between ">
                                                            <div class="tableText w-25 text-justify"
                                                                style="text-align: justify;">
                                                                <label for="lengthCourse">Length of Course:</label>

                                                            </div>
                                                            <div class="tableInput w-75">
                                                                <input type="text" id="lengthCourse"
                                                                    class="tableInputs w-100 text-uppercase no-border form-control"
                                                                    name="lengthOfCourse">
                                                            </div>
                                                        </div>
                                                    </th>
                                                    <th colspan="2" class="col-6" style="color: #000 !important;">
                                                        <div class="d-flex justify-content-between">
                                                            <div class="tableText w-50 text-justify"
                                                                style="text-align: justify;">
                                                                <label for="lengthObser">Length of
                                                                    Observation:</label>

                                                            </div>
                                                            <div class="tableInput w-50">
                                                                <input type="text" id="lengthObser"
                                                                    class="tableInputs w-100 text-uppercase no-border form-control"
                                                                    name="lengthOfObservation">
                                                            </div>
                                                        </div>
                                                    </th>
                                                </tr>
                                                <input type="hidden" id="slot-key">
                                                <input type="hidden" name="fromFacultyID"
                                                    value="<?php echo $userRow["faculty_Id"]; ?>">
                                                <input type="hidden" name="toFacultyID" id="toFacultyID">
                                                <tr>
                                                    <th colspan="2" class="col-6" style="color: #000 !important;">
                                                        <div class="d-flex justify-content-between">
                                                            <div class="tableText w-25 text-justify"
                                                                style="text-align: justify;">
                                                                <label for="observer">Observer:</label>

                                                            </div>
                                                            <div class="tableInput w-75">
                                                                <input type="text" id="observer"
                                                                    class="tableInputs w-100 text-uppercase no-border form-control"
                                                                    value="<?php echo $userRow["first_name"] . " " . $userRow["last_name"]; ?>"
                                                                    name="fromFaculty" readonly>
                                                            </div>
                                                        </div>
                                                    </th>
                                                    <th colspan="2" class="col-6" style="color: #000 !important;">
                                                        <div class="d-flex justify-content-between">
                                                            <div class="tableText w-25 text-justify"
                                                                style="text-align: justify;"
                                                                style="text-align: justify;">
                                                                <label for="dateInput">Date:</label>

                                                            </div>
                                                            <div class="tableInput w-75">
                                                                <input type="text" id="dateInput"
                                                                    class=" tableInputs w-100 no-border form-control"
                                                                    name="date" readonly>
                                                            </div>
                                                        </div>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th colspan="4" class="col-12" style="color: #000 !important;">
                                                        <div class="d-flex justify-content-between">
                                                            <div class="tableText w-25 text-justify"
                                                                style="text-align: justify;">
                                                                <label for="subjectMatter">Subject Matter Treated in
                                                                    Lesson:</label>

                                                            </div>
                                                            <div class="tableInput w-75">
                                                                <input type="text" id="subjectMatter"
                                                                    class="tableInputs w-100 no-border form-control"
                                                                    name="subjectMatter">
                                                            </div>
                                                        </div>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th colspan="4"
                                                        style="color: #000 !important; text-align: justify;">
                                                        <span class="fw-bold">Instruction</span>: Please evaluate
                                                        the
                                                        faculty member involved by encircling the number that
                                                        corresponds to the given parameter/dimensions at the scale
                                                        of 5,
                                                        where <span class="fw-bold">five is the perfect score</span>
                                                        and
                                                        <span class="fw-bold">one is the lowest score.</span>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th class="align-middle text-center"
                                                        style="color: #000 !important;">
                                                        Numerical Rating</th>
                                                    <th class="align-middle text-center"
                                                        style="color: #000 !important;">
                                                        Descriptive Rating</th>
                                                    <th class="align-middle text-center"
                                                        style="color: #000 !important;">
                                                        Qualitative Description</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>5.0</td>
                                                    <td>Outstanding</td>
                                                    <td>Exhibits the behavior described at all times when the
                                                        occasion
                                                        occurs.</td>
                                                </tr>
                                                <tr>
                                                    <td>4.0</td>
                                                    <td>Very Satisfactory</td>
                                                    <td>Exhibits the behavior described most of the time when the
                                                        occasion occurs.</td>
                                                </tr>
                                                <tr>
                                                    <td>3.0</td>
                                                    <td>Satisfactory</td>
                                                    <td>Exhibits the behavior described sometimes when the occasion
                                                        occurs.</td>
                                                </tr>
                                                <tr>
                                                    <td>2.0</td>
                                                    <td>Fair</td>
                                                    <td>Exhibits the behavior described rarely when the occasion
                                                        occurs.</td>
                                                </tr>
                                                <tr>
                                                    <td>1.0</td>
                                                    <td>Poor</td>
                                                    <td>Exhibits the behavior described has not been exhibited at
                                                        all
                                                        times when the occasion occurs.</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>

                                    <?php

                                    $sql = "SELECT * FROM `classroomcategories`";
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
                                            $columnComment = sanitizeColumnName($row['categories']) . $row['id'];
                                            $categories = $row['categories'];

                                            echo '
                                                <div class="step step-' . $stepIndex . '">
                                                    <h3>' . htmlspecialchars($categories) . '</h3>
                                                    <table class="table table-striped table-bordered text-center align-middle">
                                                        <thead>
                                                            <tr class="bg-danger text-uppercase">
                                                                <th class="text-justify">No.</th>
                                                                <th style="text-align: center; margin: 0;">Criteria</th>
                                                                <th class="text-center">Star Rating</th>
                                                                <th class="text-justify">Interpretation</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                            ';

                                            $sqlcriteria = "SELECT * FROM `classroomcriteria` WHERE classroomCategories = '$categories'";
                                            $resultCriteria = mysqli_query($con, $sqlcriteria);

                                            if ($resultCriteria) {
                                                $criteriaCount = 1;
                                                while ($criteriaRow = mysqli_fetch_array($resultCriteria)) {
                                                    $columnName = sanitizeColumnName($criteriaRow['classroomCategories']) . $criteriaRow['id'];

                                                    echo '
                                                        <tr>
                                                            <td>' . $criteriaCount++ . '</td>
                                                            <td>
                                                                <p style="text-align: justify; margin: 0;">
                                                                    ' . htmlspecialchars($criteriaRow['classroomCriteria']) . '
                                                                </p>
                                                            </td>
                                                            <td>
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
                                                    <div class="mb-3 comment">
                                                        <label for="">Comment:</label>
                                                        <textarea class="form-control" id="comment' . $columnComment . '" name="comment' . htmlspecialchars($columnComment) . '" rows="3"></textarea>
                                                    </div>

                                                </div>
                                            ';

                                            $stepIndex++;
                                        }
                                    }

                                    ?>

                                    <div class="step step-<?php echo $stepIndex; ?>">

                                        <h4>Additional Step Title</h4>

                                        <table class="table table-striped table-bordered text-center align-middle">

                                            <tbody>
                                                <?php

                                                $classroomQuestion = "SELECT * FROM `classroomquestions`";
                                                $classroomQuestion_query = mysqli_query($con, $classroomQuestion);

                                                if (mysqli_num_rows($classroomQuestion_query) > 0) {
                                                    $questionCount = 1;
                                                    while ($questionsRow = mysqli_fetch_assoc($classroomQuestion_query)) {
                                                        $questionName = sanitizeColumnName('QUESTIONNO') . $questionsRow['id'];
                                                        echo '
                                                            <tr style="text-align: justify;">
                                                                <td style="text-align: justify;">
                                                                    <div class="mb-3 text-justify" style="text-align: justify;">
                                                                        <label for="" class="text-justify" style="text-align: justify;">' . $questionCount++ . '. ' . $questionsRow['questions'] . '</label>
                                                                        <textarea class="form-control" id="QUESTIONNO-' . $questionsRow['id'] . '" name="' . htmlspecialchars($questionName) . '" rows="3"
                                                                            required></textarea>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        ';
                                                    }
                                                }

                                                ?>

                                            </tbody>

                                        </table>

                                    </div>

                                    <div class="navigation-buttons p-1">
                                        <button type="button" class="btn btn-secondary close-step mx-2"
                                            data-bs-dismiss="modal">Close</button>
                                        <button type="button" class="btn btn-secondary prev-step mx-2"
                                            style="display: none;">Previous</button>
                                        <button type="button" class="btn btn-primary next-step mx-2">Next</button>
                                        <button type="submit" name="classroomObservationSubmit"
                                            class="btn btn-success mx-2" style="display: none;"
                                            onclick="  );">Submit</button>
                                    </div>

                                </form>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

        <div class="tab-pane fade " id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">

            <form id="searchForm" class="d-flex flex-wrap justify-content-center align-items-center">

                <div class="form-group me-3 mb-3">

                    <label for="facultySelect">Select Instructor:</label>
                    <select name="facultySelect" id="facultySelect" class="form-select">
                        <option value="">Select Instructor</option>
                        <?php
                        $facultyQuery = "SELECT * FROM instructor WHERE usertype = 'faculty'";
                        $facultyResult = mysqli_query($con, $facultyQuery);
                        while ($facultyRow = mysqli_fetch_assoc($facultyResult)) {
                            echo '<option value="' . htmlspecialchars($facultyRow['faculty_Id'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($facultyRow['first_name'], ENT_QUOTES, 'UTF-8') . ' ' . htmlspecialchars($facultyRow['last_name'], ENT_QUOTES, 'UTF-8') . '</option>';

                        }
                        ?>
                    </select>

                </div>

                <div class="form-group me-3 mb-3">

                    <label for="adminSelect">Select Observer:</label>
                    <select name="adminSelect" id="adminSelect" class="form-select">
                        <option value="">Select Observer</option>
                        <?php
                        $adminQuery = "SELECT * FROM instructor WHERE usertype = 'admin'";
                        $adminResult = mysqli_query($con, $adminQuery);
                        while ($adminRow = mysqli_fetch_assoc($adminResult)) {
                            echo '<option value="' . $adminRow['faculty_Id'] . '">' . htmlspecialchars($adminRow['first_name'], ENT_QUOTES, 'UTF-8') . ' ' . htmlspecialchars($adminRow['last_name'], ENT_QUOTES, 'UTF-8') . '</option>';
                        }
                        ?>
                    </select>

                </div>

                <button type="button" id="searchButton" class="btn btn-primary mb-3">Search</button>

            </form>

            <div class="overflow-auto" style="max-height: 520px">

                <table class="table table-striped table-bordered text-center align-middle w-100">

                    <thead>

                        <tr class="bg-danger text-white">
                            <th>Observed Faculty Member</th>
                            <th>Date</th>
                            <th>Observation Conducted By</th>
                            <th>Action</th>
                        </tr>

                    </thead>

                    <tbody id="tableBody">

                        <h1 id="noResults" class="text-center text-danger" style="display: none;">No results found</h1>

                    </tbody>
            </div>

        </div>

    </div>

    <div class="modal fade bg-transparent opacity-1" id="officialviewmodal" style="opacity: transparent" tabindex="-1"
        role="dialog" aria-labelledby="officialviewmodalLabel" aria-hidden="true">

        <div class="modal-dialog" role="document">

            <div class="modal-content">

                <div class="modal-body officialviewmodal">
                </div>

            </div>

        </div>

    </div>

</section>


<script>

    <?php if (isset($_SESSION['status'])): ?>
        Swal.fire({
            title: '<?php echo $_SESSION['status']; ?>',
            icon: '<?php echo ($_SESSION['status-code'] == 'success' ? 'success' : 'error'); ?>',
            confirmButtonText: 'OK'
        });
        <?php unset($_SESSION['status']); ?>
    <?php endif; ?>

    let bookedSlots = {};

    $(document).ready(function () {

        loadTableData();

        $('#searchButton').on('click', function () {
            var facultySelect = $('#facultySelect').val();
            var adminSelect = $('#adminSelect').val();

            loadTableData(facultySelect, adminSelect);
        });

        function loadTableData(facultySelect = '', adminSelect = '') {

            let params = $.param({
                facultySelect: facultySelect,
                adminSelect: adminSelect
            });

            $.ajax({
                url: '../../controller/classroomObservation.php?' + params,
                type: 'GET',
                success: function (data) {
                    $('#tableBody').html(data);
                },
                error: function (xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        }

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

        $('#multi-step-form').on('submit', function (event) {


            const slotKey = $('#slot-key').val();
            const booking = bookedSlots[slotKey];

            if (booking) {
                const startTime = booking.startTime;
                const endTime = booking.endTime;


                for (let hour = startTime; hour < endTime; hour++) {
                    const timeSlotKey = `${hour}-${new Date(booking.selectedDate).getTime()}-${slotKey.split('-')[2]}`;
                    if (bookedSlots[timeSlotKey]) {
                        bookedSlots[timeSlotKey].isEvaluated = true;
                    }
                }

                saveBookings();

            }
        });

        var currentStep = 1;
        var totalSteps = <?php echo $totalCategories + 2; ?>;

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
                } else {
                    $(".navigation-buttons .next-step").show();
                    $(".navigation-buttons .btn-success").hide();
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

        const today = new Date().toISOString().split('T')[0];
        $('#view-date-select').val(today);
        loadBookings();
        createViewReservationTable();

        $('#view-date-select').change(createViewReservationTable);

        function loadBookings() {
            const storedBookings = localStorage.getItem('bookedSlots');
            if (storedBookings) {
                bookedSlots = JSON.parse(storedBookings);
            }
        }

        function saveBookings() {
            localStorage.setItem('bookedSlots', JSON.stringify(bookedSlots));
        }

        function createViewReservationTable() {
            const selectedDate = new Date($('#view-date-select').val());
            const days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];


            const headerRow = $('<tr>').css({
                'background-color': '#b71c1c',
                'color': 'white',
                'border': '2px solid white'
            }).append($('<th rowspan="2" style="vertical-align: middle">DATE / TIME</th>'));

            for (let i = 0; i < 2; i++) {
                const day = new Date(selectedDate);
                day.setDate(selectedDate.getDate() + i);
                const dateHeader = `${day.toLocaleString('default', { month: 'long' })} ${day.getDate()}, ${day.getFullYear()}`;
                const dayHeader = days[day.getDay()];

                const dateCell = $('<th>')
                    .attr('colspan', 2)
                    .html(`${dateHeader}<br>${dayHeader}`)
                    .css({
                        'background-color': '#b71c1c',
                        'color': 'white',
                        'border': '2px solid white'
                    });
                headerRow.append(dateCell);
            }

            const slotHeaderRow = $('<tr>').css({
                'background-color': '#b71c1c',
                'color': 'white',
                'border': '2px solid white'
            }).append($('<th style="display: none;"></th>'));
            for (let i = 0; i < 2; i++) {
                slotHeaderRow.append($('<th>').css({
                    'background-color': '#b71c1c',
                    'color': 'white',
                    'border': '2px solid white'
                }).text('Slot 1'))
                    .append($('<th>').css({
                        'background-color': '#b71c1c',
                        'color': 'white',
                        'border': '2px solid white'
                    }).text('Slot 2'));
            }

            $('#view-reservation-table').empty().append(headerRow).append(slotHeaderRow);

            for (let hour = 7; hour < 19; hour++) {
                const row = $('<tr>').append($('<td>').addClass('py-3').css({
                    'background-color': '#b71c1c',
                    'color': 'white',
                    'border': '2px solid white'
                }).text(`${hour > 12 ? hour - 12 : hour}:00 to ${hour + 1 > 12 ? hour + 1 - 12 : hour + 1}:00 ${hour >= 12 ? 'PM' : 'AM'}`));

                for (let i = 0; i < 2; i++) {
                    const dayOffset = new Date(selectedDate);
                    dayOffset.setDate(selectedDate.getDate() + i);
                    const slotKey1 = `${hour}-${dayOffset.getTime()}-1`;
                    const slotKey2 = `${hour}-${dayOffset.getTime()}-2`;

                    const cell1 = $('<td>').css({
                        'border': '2px solid #fff',
                        'color': '#000',
                        'background': '#c8e6c9'
                    }).text('Available');

                    const cell2 = $('<td>').css({
                        'border': '2px solid #fff',
                        'color': '#000',
                        'background': '#c8e6c9'
                    }).text('Available');

                    if (bookedSlots[slotKey1]) {
                        const booking1 = bookedSlots[slotKey1];
                        if (booking1.isEvaluated) {
                            cell1.addClass('py-3').css({
                                'border': '2px solid #fff',
                                'color': '#000',
                                'background': '#80deea'
                            }).text(booking1.name);
                        } else {
                            cell1.addClass('py-3 booked-slot').css({
                                'border': '2px solid #fff',
                                'color': '#000',
                                'background': '#ffab91'
                            }).html(`${booking1.name}<br>${booking1.room}`).data('slotKey', slotKey1);
                        }
                    }

                    if (bookedSlots[slotKey2]) {
                        const booking2 = bookedSlots[slotKey2];
                        if (booking2.isEvaluated) {
                            cell2.addClass('py-3').css({
                                'border': '2px solid #fff',
                                'color': '#000',
                                'background': '#80deea'
                            }).text(booking2.name);
                        } else {
                            cell2.addClass('py-3 booked-slot').css({
                                'border': '2px solid #fff',
                                'color': '#000',
                                'background': '#ffab91'
                            }).html(`${booking2.name}<br>${booking2.room}`).data('slotKey', slotKey2);
                        }
                    }


                    row.append(cell1).append(cell2);
                }

                $('#view-reservation-table').append(row);
            }
        }

        $(document).on('click', '.booked-slot', function () {
            const slotKey = $(this).data('slotKey');
            const booking = bookedSlots[slotKey];

            if (booking) {
                const today = new Date().toISOString().split('T')[0];
                const bookingDate = new Date(booking.selectedDate).toISOString().split('T')[0];

                if (bookingDate === today) {
                    if (!booking.isEvaluated) {
                        const dateConvert = new Date(booking.selectedDate);
                        const options = { year: 'numeric', month: 'long', day: 'numeric' };
                        const formattedDateText = dateConvert.toLocaleDateString('en-US', options);

                        $('#booking-name').text(booking.name);
                        $('#toFacultyID').val(booking.fromFacultyID);
                        $('#booking-room').text(booking.room);
                        $('#facultyInput').val(booking.name);
                        $('#courseInput').val(booking.course);
                        $('#slot-key').val(slotKey);
                        $('#dateInput').val(formattedDateText);
                        $('#cancel-booking-btn').data('slotKey', slotKey);
                        $('#bookingDetailsModal').modal('show');
                        console.log(booking);
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Already Evaluated',
                            text: 'This slot has already been evaluated and cannot be edited.',
                            confirmButtonText: 'OK'
                        });
                    }
                } else if (bookingDate > today) {
                    Swal.fire({
                        icon: 'info',
                        title: 'Observation Unavailable',
                        text: 'This classroom observation is scheduled for a future date. You can only manage observations set for today.',
                        confirmButtonText: 'Understood'
                    });
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'Observation Unavailable',
                        text: 'This observation has already passed.',
                        confirmButtonText: 'Understood'
                    });
                }
            }
        });

    });

    $('input[type="radio"]').click(function () {
        const groupName = $(this).attr('name');
        const selectedValue = $(this).val();
        const displayText = $('#' + groupName + 'Display');

        if (selectedValue) {
            displayText.text(selectedValue);
        } else {
            displayText.text('');
        }
    });

</script>
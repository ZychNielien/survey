<?php
include "components/navBar.php";
include "../../model/dbconnection.php";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle the action based on the request
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($action === 'assign') {
            // Get the semester and academic year from POST
            $semester = $_POST['semester'] ?? null;
            $academicYear = $_POST['academicYear'] ?? null;

            // Fetch all user IDs
            $sql = "SELECT faculty_Id FROM `instructor`";
            $result = $con->query($sql);

            if ($result->num_rows > 0) {
                $userIds = [];
                while ($row = $result->fetch_assoc()) {
                    $userIds[] = $row['faculty_Id'];
                }

                // Assign three random IDs to each user
                foreach ($userIds as $userId) {
                    // Remove current user ID from the list to avoid self-assignment
                    $filteredIds = array_filter($userIds, function ($id) use ($userId) {
                        return $id != $userId;
                    });

                    if (count($filteredIds) >= 3) {
                        // Choose 3 random IDs
                        $randomIds = array_rand(array_flip($filteredIds), 5);
                        foreach ($randomIds as $randomId) {
                            $insertStmt = $con->prepare("INSERT INTO randomfaculty (faculty_Id, random_Id, semester, academic_year) VALUES (?, ?, ?, ?)");
                            if ($insertStmt === false) {
                                echo json_encode(["status" => "error", "message" => "Error in prepare: " . $con->error]);
                                exit;
                            }
                            $insertStmt->bind_param("iiss", $userId, $randomId, $semester, $academicYear);
                            if (!$insertStmt->execute()) {
                                echo json_encode(["status" => "error", "message" => "Execute failed: " . $insertStmt->error]);
                                exit;
                            }
                        }
                    }
                }
                $_SESSION['toggle_state'] = 'assign'; // Update session state
                echo json_encode(["status" => "success", "message" => "Tatlong random IDs na-assign sa bawat user!"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Walang users na natagpuan."]);
            }
        } elseif ($_POST['action'] === 'clear') {
            $clearSql = "DELETE FROM randomfaculty";
            if ($con->query($clearSql) === TRUE) {
                $_SESSION['toggle_state'] = 'clear'; // Update session state
                // Clear selected semester and academic year from session

                echo json_encode(["status" => "success", "message" => "Nabura ang lahat ng random IDs at ang mga napiling Academic Year at Semester."]);
            } else {
                echo json_encode(["status" => "error", "message" => "Error: " . $con->error]);
            }
        }
        exit; // Terminate script after handling AJAX request
    }
}

// Set initial action based on session
$initialAction = isset($_SESSION['toggle_state']) ? $_SESSION['toggle_state'] : 'assign';

?>

<head>
    <title>Questions</title>
    <!-- CSS -->
    <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/sweetalert.min.css">
    <!-- SCRIPT -->
    <script src="../../public/js/sweetalert2@11.js"></script>

    <style>
        .btn-toggle {
            border: none;
            height: 2.5rem;
            width: 5rem;
            border-radius: 2.5rem;
            position: relative;
            background: #bdc1c8;
            color: #6b7381;
            transition: background-color 0.25s;
            cursor: pointer;
        }

        .handle {
            position: absolute;
            top: 0.3125rem;
            left: 0.3125rem;
            width: 1.875rem;
            height: 1.875rem;
            border-radius: 1.875rem;
            background: #fff;
            transition: left 0.25s;
        }

        .assigned {
            background-color: #29b5a8;
        }

        .assigned .handle {
            left: 2.8125rem;
        }

        .nav-link.active {
            font-weight: bold;
            color: #d0112b;
            /* Make the active font bold */
        }
    </style>
</head>

<section class="contentContainer">
    <nav>
        <div class="nav nav-tabs mb-3" id="nav-tab" role="tablist">
            <button class="nav-link active fs-5" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home"
                type="button" role="tab" aria-controls="nav-home" aria-selected="true">Faculty Peer to Peer
                Evaluation</button>
            <button class="nav-link fs-5" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile"
                type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Faculty Evaluation Criteria
                for Students</button>
        </div>
    </nav>
    <div class="tab-content p-3 border bg-light overflow-auto" id="nav-tabContent">

        <!-- Faculty Evaluation Panel -->
        <div class="tab-pane fade active show" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">

            <div class="d-flex justify-content-between mb-3 ">
                <form action="">
                    <div class="d-flex px-3">
                        <div class="d-flex flex-column align-items-center px-2">
                            <select id="semesterSelect" class="form-control">
                                <option value="">--Select Semester--</option>
                                <option value="1st">1st Semester</option>
                                <option value="2nd">2nd Semester</option>
                            </select>
                        </div>
                        <div class="d-flex flex-column align-items-center px-2">
                            <select id="academicYearSelect" class="form-control">
                                <option value="" disabled>Select Academic Year</option>
                                <?php
                                $currentYear = date("Y");
                                $nextYear = $currentYear + 5; // Extend five years into the future
                                
                                // Generate future academic year options
                                for ($year = $currentYear; $year <= $nextYear; $year++) {
                                    echo "<option value='$year-" . ($year + 1) . "'>$year - " . ($year + 1) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="d-flex flex-column align-items-center px-2">
                            <button type="button" class="btn btn-toggle" data-toggle="button" aria-pressed="false">
                                <div class="handle"></div>
                            </button>
                        </div>
                    </div>
                </form>

                <div>
                    <button class="btn btn-success float-right mx-3" data-bs-toggle="modal"
                        data-bs-target="#facultyCategoriesModal">+ Categories</button>
                    <button class="btn btn-success float-right" data-bs-toggle="modal"
                        data-bs-target="#facultyEvaluationModal">+ Criteria</button>
                </div>
            </div>
            <div>
                <h3 id="actionLabel" class="fw-bold text-center">Assigning Random IDs</h3>
            </div>


            <div class="overflow-auto" style="max-height: 580px">
                <?php
                $sql = "SELECT * FROM `facultycategories`";
                $result = mysqli_query($con, $sql);

                if ($result) {
                    while ($row = mysqli_fetch_array($result)) {
                        $categories = htmlspecialchars($row['categories']); // Prevent XSS
                        $counter = 1; // Initialize the counter for criteria
                
                        echo '
                                <table class="table table-striped table-bordered text-center align-middle">
                                    <thead>
                                        <tr class="bg-danger">
                                            <th class="text-justify">No.</th>
                                            <th style="text-align: center; margin: 0;">' . $categories . '</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                            ';

                        // Get criteria for the current category
                        $sqlcriteria = "SELECT * FROM `facultycriteria` WHERE facultyCategories = '$categories'";
                        $resultCriteria = mysqli_query($con, $sqlcriteria);

                        if ($resultCriteria) {
                            while ($criteriaRow = mysqli_fetch_array($resultCriteria)) {
                                echo '
                                        <tr>
                                            <td>' . $counter++ . '</td>
                                            <td>
                                                <p style="text-align: justify; margin: 0;">
                                                    ' . htmlspecialchars($criteriaRow['facultyCriteria']) . ' 
                                                </p>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-primary edit-btn" data-id="' . $criteriaRow['id'] . '" data-criteria="' . htmlspecialchars($criteriaRow['facultyCriteria']) . '" data-bs-toggle="modal" data-bs-target="#editCriteriaModal">Edit</button>
                                                    <a href="javascript:void(0);" class="btn btn-danger delete-btn" data-id="' . $criteriaRow['id'] . '" data-type="criteria">Delete</a>
                                                </div>
                                            </td>
                                        </tr>
                                    ';
                            }
                        }

                        echo '
                            <tr>
                                <td colspan="3">     
                                <a href="javascript:void(0);" class="btn btn-danger delete-btn" data-id="' . $row['id'] . '" data-type="category">Delete Categories</a>

                                </td>
                            </tr>
                        ';
                        echo '
                                </tbody>
                            </table>
                        ';
                    }
                }
                ?>
            </div>

        </div>
        <!-- Faculty Editing Modal -->
        <div class="modal fade" id="editCriteriaModal" tabindex="-1" aria-labelledby="editCriteriaModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header  bg-danger text-white">
                        <h5 class="modal-title" id="editCriteriaModalLabel">Edit Criteria</h5>
                        <button type="button" class="btn-close  bg-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editCriteriaForm">
                            <input type="hidden" id="criteriaId" name="criteriaId">
                            <div class="mb-3">
                                <textarea class="form-control" id="criteriaText" name="criteriaText" rows="3"
                                    required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Faculty Adding New Criteria -->
        <div class="modal fade" id="facultyEvaluationModal" tabindex="-1" aria-labelledby="facultyEvaluationModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="facultyEvaluationModalLabel">Add New Evaluation Criteria</h5>
                        <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form action="../../controller/criteria.php" method="POST">
                        <div class="modal-body">

                            <div class="form-group p-2 mb-2">
                                <label for="evaluation" class="form-label">Choose an evaluation category:</label>
                                <select id="evaluation" name="facultyEvaluationCategories" class="form-select">


                                    <?php
                                    $categorieSQL = "SELECT * FROM facultycategories";
                                    $categorieSQL_query = mysqli_query($con, $categorieSQL);
                                    while ($categorie = mysqli_fetch_array($categorieSQL_query)) {
                                        echo '
                                                <option value="' . $categorie['categories'] . '">' . $categorie['categories'] . '</option>
                                            ';
                                    }
                                    ?>


                                </select>
                            </div>
                            <div class="form-group p-2 mb-2">
                                <textarea class="form-control text-capitalize" name="facultyEvaluationCriteria"
                                    rows="3"></textarea>

                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="addFacultyCriteria" class="btn btn-success">ADD</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- Faculty Adding New Categories -->
        <div class="modal fade" id="facultyCategoriesModal" tabindex="-1" aria-labelledby="facultyCategoriesModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="facultyCategoriesModalLabel">Add New Evaluation Categorie</h5>
                        <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form action="../../controller/criteria.php" method="POST">
                        <div class="modal-body">

                            <div class="form-group p-2 mb-2">
                                <label for="newCategory" class="form-label">Add new evaluation category:</label>
                                <input type="text" id="newCategory" class="form-control text-uppercase"
                                    name="newCategory" aria-label="Username" aria-describedby="basic-addon1">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="addCategory" class="btn btn-success">ADD</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <!-- Student Evaluation Panel -->
        <div class="tab-pane fade " id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">

            <h3 class="text-danger fw-bold text-center">Faculty Evaluation Criteria for Students</h3>

            <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-success float-right mx-3" data-bs-toggle="modal"
                    data-bs-target="#studentCategoriesModal">+ Categories</button>
                <button class="btn btn-success float-right" data-bs-toggle="modal"
                    data-bs-target="#studentEvaluationModal">+ Criteria</button>
            </div>

            <!-- Student Evaluation Panel -->
            <div class="overflow-auto" style="max-height: 580px">
                <?php
                $sql = "SELECT * FROM `studentscategories`";
                $result = mysqli_query($con, $sql);

                if ($result) {
                    while ($row = mysqli_fetch_array($result)) {
                        $categories = htmlspecialchars($row['categories']); // Prevent XSS
                        $counter = 1; // Initialize the counter for criteria
                
                        echo '
                                <table class="table table-striped table-bordered text-center align-middle">
                                    <thead>
                                        <tr class="bg-danger">
                                            <th class="text-justify">No.</th>
                                            <th style="text-align: center; margin: 0;">' . $categories . '</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                            ';

                        // Get criteria for the current category
                        $sqlcriteria = "SELECT * FROM `studentscriteria` WHERE studentsCategories = '$categories'";
                        $resultCriteria = mysqli_query($con, $sqlcriteria);

                        if ($resultCriteria) {
                            while ($studentscriteriaRow = mysqli_fetch_array($resultCriteria)) {
                                echo '
                                        <tr>
                                            <td>' . $counter++ . '</td>
                                            <td>
                                                <p style="text-align: justify; margin: 0;">
                                                    ' . htmlspecialchars($studentscriteriaRow['studentsCriteria']) . ' 
                                                </p>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-primary student-edit-btn" 
                                                        data-studentsid="' . $studentscriteriaRow['id'] . '" 
                                                        data-studentscriteria="' . htmlspecialchars($studentscriteriaRow['studentsCriteria']) . '" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#editStudentCriteriaModal">Edit</button>

                                                    <a href="javascript:void(0);" class="btn btn-danger students-delete-btn" data-studentsid="' . $studentscriteriaRow['id'] . '" data-studentstype="criteria">Delete</a>
                                                </div>
                                            </td>
                                        </tr>
                                    ';
                            }
                        }

                        echo '
                            <tr>
                                <td colspan="3">     
                                <a href="javascript:void(0);" class="btn btn-danger students-delete-btn" data-studentsid="' . $row['id'] . '" data-studentstype="category">Delete Categories</a>

                                </td>
                            </tr>
                        ';
                        echo '
                                </tbody>
                            </table>
                        ';
                    }
                }
                ?>
            </div>

            <!-- Student Adding New Categories -->
            <div class="modal fade" id="studentCategoriesModal" tabindex="-1"
                aria-labelledby="studentCategoriesModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="studentEvaluationModalLabel">Add a New Faculty Evaluation
                                Category for Students</h5>
                            <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form action="../../controller/criteria.php" method="POST">
                            <div class="modal-body">

                                <div class="form-group p-2 mb-2">
                                    <label for="studentCategory" class="form-label">Category Name:</label>
                                    <input type="text" id="studentCategory" class="form-control text-uppercase"
                                        name="studentCategory" aria-label="Username" aria-describedby="basic-addon1">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" name="addstudentCategory" class="btn btn-success">ADD</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Students Adding New Criteria -->
            <div class="modal fade" id="studentEvaluationModal" tabindex="-1"
                aria-labelledby="studentEvaluationModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="studentEvaluationModalLabel">Add a New Faculty Evaluation
                                Criteria for Students</h5>
                            <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <form action="../../controller/criteria.php" method="POST">
                            <div class="modal-body">

                                <div class="form-group p-2 mb-2">
                                    <label for="evaluation" class="form-label">Choose an evaluation category:</label>
                                    <select id="evaluation" name="studentsEvaluationCategories" class="form-select">


                                        <?php
                                        $categorieSQL = "SELECT * FROM studentscategories";
                                        $categorieSQL_query = mysqli_query($con, $categorieSQL);
                                        while ($categorie = mysqli_fetch_array($categorieSQL_query)) {
                                            echo '
                                                <option value="' . $categorie['categories'] . '">' . $categorie['categories'] . '</option>
                                            ';
                                        }
                                        ?>


                                    </select>
                                </div>
                                <div class="form-group p-2 mb-2">
                                    <textarea class="form-control text-capitalize" name="studentsEvaluationCriteria"
                                        rows="3"></textarea>

                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" name="addStudentsCriteria" class="btn btn-success">ADD</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Faculty Editing Modal -->
            <div class="modal fade" id="editStudentCriteriaModal" tabindex="-1"
                aria-labelledby="editStudentCriteriaModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title" id="editStudentCriteriaModalLabel">Edit Students Criteria</h5>
                            <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="editStudentsCriteriaForm">
                                <input type="hidden" id="studentscriteriaId" name="studentscriteriaId">
                                <div class="mb-3">
                                    <textarea class="form-control" id="studentscriteriaText" name="studentscriteriaText"
                                        rows="3" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Save changes</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php

if (isset($_SESSION['success'])) {
    echo "
    <script>
        Swal.fire({
            title: 'Success!',
            text: '" . $_SESSION['success'] . "',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then(() => {
            // Clear the session variable after displaying the alert
            window.location.reload(); // Optionally reload the page
        });
    </script>
    ";

    // Clear the session variable after displaying the alert
    unset($_SESSION['success']);
}
?>



<script>
    $(document).ready(function () {
        $('.student-edit-btn').on('click', function () {
            const studentscriteriaId = $(this).data('studentsid');
            const studentscriteriaText = $(this).data('studentscriteria');

            // Populate the modal fields with the data
            $('#studentscriteriaId').val(studentscriteriaId);
            $('#studentscriteriaText').val(studentscriteriaText);
        });

        $('#editStudentsCriteriaForm').on('submit', function (e) {
            e.preventDefault();

            const formData = $(this).serialize();

            $.ajax({
                url: '../../controller/criteria.php',
                method: 'POST',
                data: formData,
                success: function (data) {
                    location.reload();
                },
                error: function (error) {
                    console.error('Error:', error);
                }
            });
        });

        $('.students-delete-btn').on('click', function () {
            const studentsid = $(this).data('studentsid');
            const studentstype = $(this).data('studentstype');
            const studentsaction = studentstype === 'category' ? 'deletestudentsCategoryid' : 'studentsdeleteid';

            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to the delete URL
                    window.location.href = `../../controller/criteria.php?${studentsaction}=` + studentsid;
                }
            });
        });


        $('.edit-btn').on('click', function () {
            const criteriaId = $(this).data('id');
            const criteriaText = $(this).data('criteria');

            $('#criteriaId').val(criteriaId);
            $('#criteriaText').val(criteriaText);
        });

        $('#editCriteriaForm').on('submit', function (e) {
            e.preventDefault();

            const formData = $(this).serialize();

            $.ajax({
                url: '../../controller/criteria.php',
                method: 'POST',
                data: formData,
                success: function (data) {
                    location.reload();
                },
                error: function (error) {
                    console.error('Error:', error);
                }
            });
        });

        $('.delete-btn').on('click', function () {
            const id = $(this).data('id');
            const type = $(this).data('type');
            const action = type === 'category' ? 'deleteCategoryid' : 'deleteid';

            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect to the delete URL
                    window.location.href = `../../controller/criteria.php?${action}=` + id;
                }
            });
        });

        const storedSemester = localStorage.getItem('semester');
        const storedAcademicYear = localStorage.getItem('academicYear');

        if (storedSemester) {
            $('#semesterSelect').val(storedSemester);
        }

        if (storedAcademicYear) {
            $('#academicYearSelect').val(storedAcademicYear);
        }

        // Save semester and academic year to local storage when changed
        $('#semesterSelect').on('change', function () {
            localStorage.setItem('semester', $(this).val());
        });

        $('#academicYearSelect').on('change', function () {
            localStorage.setItem('academicYear', $(this).val());
        });

        let currentAction = 'clear'; // Initialize action state

        // Load semester and academic year from localStorage on page load
        if (localStorage.getItem('action') === 'assign') {
            $('#semesterSelect').val(localStorage.getItem('semester'));
            $('#academicYearSelect').val(localStorage.getItem('academicYear'));
            currentAction = 'assign';
        } else {
            // Reset dropdowns if action is 'clear' or no action is stored
            $('#semesterSelect').val('');
            $('#academicYearSelect').val('');
        }

        // Set the toggle state based on the initial action
        $('.btn-toggle').toggleClass('assigned', currentAction === 'assign');
        updateActionLabel(currentAction); // Initialize label on load

        // Toggle button click event
        $('.btn-toggle').on('click', function () {
            currentAction = (currentAction === 'assign') ? 'clear' : 'assign';

            // Update the toggle class visually right away
            $('.btn-toggle').toggleClass('assigned', currentAction === 'assign');
            updateActionLabel(currentAction);

            if (currentAction === 'assign') {
                const semester = $('#semesterSelect').val();
                const academicYear = $('#academicYearSelect').val();

                if (!semester || !academicYear) {
                    Swal.fire({
                        title: 'Missing Selections',
                        text: 'Please select both a semester and an academic year before proceeding.',
                        icon: 'warning',
                        confirmButtonText: 'Okay'
                    });
                    currentAction = 'clear';
                    $('.btn-toggle').removeClass('assigned');
                    updateActionLabel(currentAction);
                    return;
                }

                // Store selections in localStorage when assigned
                localStorage.setItem('semester', semester);
                localStorage.setItem('academicYear', academicYear);
                localStorage.setItem('action', 'assign');

                Swal.fire({
                    title: 'Are you sure?',
                    text: `You have selected ${semester} Semester, Academic Year: ${academicYear}. Proceed with assigning random IDs?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, assign it!',
                    cancelButtonText: 'No, cancel!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        executeAction(currentAction, semester, academicYear);
                    } else {
                        // Reset to clear if cancelled
                        currentAction = 'clear';
                        $('.btn-toggle').removeClass('assigned');
                        updateActionLabel(currentAction);
                    }
                });
            } else {
                // Show confirmation before clearing
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This will clear all selections. Do you want to proceed?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, clear it!',
                    cancelButtonText: 'No, cancel!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Clear the selections for semester and academic year
                        $('#semesterSelect').val('');
                        $('#academicYearSelect').val('');
                        currentAction = 'clear';
                        localStorage.removeItem('semester');
                        localStorage.removeItem('academicYear');
                        localStorage.removeItem('action'); // Clear localStorage
                        executeAction(currentAction);
                    } else {
                        // Reset to assigned if cancelled
                        currentAction = 'assign';
                        $('.btn-toggle').addClass('assigned');
                        updateActionLabel(currentAction);
                    }
                });
            }
        });

        function executeAction(action, semester, academicYear) {
            const data = { action: action };
            if (semester && academicYear) {
                data.semester = semester;
                data.academicYear = academicYear;
            }

            $.post("", data, function (response) {
                var res = JSON.parse(response);
                Swal.fire({
                    title: 'Success',
                    text: res.message,
                    icon: 'success',
                    confirmButtonText: 'Okay'
                });

                if (action === 'assign') {
                    Swal.fire({
                        title: 'Random IDs Assigned',
                        text: 'Tatlong random IDs ay matagumpay na na-assign.',
                        icon: 'success',
                        confirmButtonText: 'Okay'
                    });
                }

                loadUsers();
            }).fail(function (xhr) {
                let errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : "Naganap ang isang error sa server.";
                Swal.fire({
                    title: 'Error',
                    text: errorMessage,
                    icon: 'error',
                    confirmButtonText: 'Okay'
                });
            });
        }

        function updateActionLabel(action) {
            const label = action === 'assign' ? 'The peer-to-peer evaluation is OPEN.' : 'The peer-to-peer evaluation is CLOSED.';
            $('#actionLabel').text(label);
            if (action === 'assign') {
                $('#actionLabel').css('color', 'green');
            } else {
                $('#actionLabel').css('color', 'red');
            }
        }

        function loadUsers() {
            $.get("", function (data) {
                const usersStart = data.indexOf("<ul>") + 4;
                const usersEnd = data.indexOf("</ul>") + 5;
                const usersHTML = data.substring(usersStart, usersEnd);
                $("#userList").html(usersHTML);
            });
        }

        function updateActionLabel(action) {
            const label = action === 'assign' ? 'The peer-to-peer evaluation is OPEN.' : 'The peer-to-peer evaluation is CLOSED.';
            $('#actionLabel').text(label);
            if (action === 'assign') {
                $('#actionLabel').css('color', 'green'); // Set color to green for assigning
            } else {
                $('#actionLabel').css('color', 'red'); // Set color to red for clearing
            }
        }

    });

</script>
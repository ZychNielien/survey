<?php
include "components/navBar.php";
include "../../model/dbconnection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'assign') {

        $semester = $_POST['semester'] ?? null;
        $academicYear = $_POST['academicYear'] ?? null;


        $sql = "SELECT faculty_Id FROM instructor";
        $result = $con->query($sql);

        if ($result->num_rows > 0) {
            $userIds = [];
            while ($row = $result->fetch_assoc()) {
                $userIds[] = $row['faculty_Id'];
            }

            foreach ($userIds as $userId) {

                $filteredIds = array_filter($userIds, function ($id) use ($userId) {
                    return $id != $userId;
                });

                if (count($filteredIds) >= 3) {

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
            $_SESSION['toggle_state'] = 'assign';
            echo json_encode(["status" => "success", "message" => "Tatlong random IDs na-assign sa bawat user!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Walang users na natagpuan."]);
        }
    } elseif ($_POST['action'] === 'clear') {
        $clearSql = "DELETE FROM randomfaculty";
        if ($con->query($clearSql) === TRUE) {
            $_SESSION['toggle_state'] = 'clear';

            echo json_encode(["status" => "success", "message" => "Nabura ang lahat ng random IDs at ang mga napiling Academic Year at Semester."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error: " . $con->error]);
        }
    }
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['studentaction'])) {

    $action = $_POST['studentaction'];

    if ($action === 'assign') {
        $semester = $_POST['semester'] ?? null;
        $academicYear = $_POST['academicYear'] ?? null;

        if (is_null($semester) || is_null($academicYear)) {
            echo json_encode(["status" => "error", "message" => "Semester and Academic Year are required."]);
            exit;
        }

        $updateSQL = "UPDATE `academic_year_semester` SET semester=?, academic_year=?, isOpen='1' WHERE id=1";
        $stmt = $con->prepare($updateSQL);
        if ($stmt) {
            $stmt->bind_param("ss", $semester, $academicYear);
            $success = $stmt->execute();
            $stmt->close();

            if ($success) {

                echo json_encode(["status" => "success", "message" => "Evaluation opened."]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to update semester and academic year."]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to prepare the SQL statement."]);
        }

    } elseif ($action === 'clear') {
        $clearSql = "UPDATE `academic_year_semester` SET semester = '', academic_year = '', isOpen = '0' WHERE id=1";
        if ($con->query($clearSql) === TRUE) {

            echo json_encode(["status" => "success", "message" => "Evaluation closed."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Error: " . $con->error]);
        }
    }
    exit;

}

?>

<head>

    <!-- PAGE TITLE -->
    <title>Questions</title>

    <!-- ALL STYLES AND CSS FILES -->
    <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/sweetalert.min.css">

    <style>
        .btn-toggle,
        .btn-studenttoggle {
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
        }
    </style>

    <!-- SCRIPT -->
    <script src="../../public/js/sweetalert2@11.js"></script>
    <script src="../../public/js/jquery-3.7.1.min.js"></script>

</head>

<!-- CONTENT CONTAINER -->
<section class="contentContainer">

    <!-- NAVIGATION TAB -->
    <nav>
        <div class="nav nav-tabs mb-3" id="nav-tab" role="tablist">
            <button class="nav-link active fs-5" id="nav-classroom-tab" data-bs-toggle="tab"
                data-bs-target="#nav-classroom" type="button" role="tab" aria-controls="nav-classroom"
                aria-selected="true">Classroom Observation
                Evaluation</button>
            <button class="nav-link fs-5" id="nav-faculty-tab" data-bs-toggle="tab" data-bs-target="#nav-faculty"
                type="button" role="tab" aria-controls="nav-faculty" aria-selected="true">Faculty Peer to Peer
                Evaluation</button>
            <button class="nav-link fs-5" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile"
                type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Faculty Evaluation Criteria
                for Students</button>
            <button class="nav-link fs-5" id="nav-links-tab" data-bs-toggle="tab" data-bs-target="#nav-links"
                type="button" role="tab" aria-controls="nav-links" aria-selected="false">Subject
                Recommendations</button>
            <button class="nav-link fs-5" id="nav-categoriesLinks-tab" data-bs-toggle="tab"
                data-bs-target="#nav-categoriesLinks" type="button" role="tab" aria-controls="nav-categoriesLinks"
                aria-selected="false">Categories
                Recommendations</button>
        </div>
    </nav>

    <!-- NAVIGATION TAB CONTAINER -->
    <div class="tab-content p-3 border bg-light overflow-auto" id="nav-tabContent">

        <!-- #################### CLASSROOM EVALUATION TAB #################### -->

        <!-- CLASSROOM EVALUATION PANEL -->
        <div class="tab-pane fade active show" id="nav-classroom" role="tabpanel" aria-labelledby="nav-classroom-tab">

            <div class="d-flex justify-content-between mb-3 ">
                <div>
                    <?php

                    $sqlSAYSelect = "SELECT * FROM academic_year_semester WHERE id =2";
                    $result = mysqli_query($con, $sqlSAYSelect);
                    $selectSAY = mysqli_fetch_assoc($result);

                    $semester = $selectSAY['semester'];
                    $academic_year = $selectSAY['academic_year'];
                    ?>

                    <form action="../../controller/criteria.php" method="POST">
                        <div class="d-flex px-3">
                            <div class="d-flex flex-column align-items-center px-2">
                                <select id="semesterSelectClassroom" name="semesterSelectClassroom" class="form-select"
                                    required>
                                    <option value="" disabled>Select Semester</option>
                                    <option value="FIRST" <?php echo (isset($semester) && $semester === 'FIRST') ? 'selected' : ''; ?>>1st Semester</option>
                                    <option value="SECOND" <?php echo (isset($semester) && $semester === 'SECOND') ? 'selected' : ''; ?>>2nd Semester</option>
                                </select>
                            </div>

                            <div class="d-flex flex-column align-items-center px-2">
                                <select id="academicYearSelectClassroom" name="academicYearSelectClassroom"
                                    class="form-select" required>
                                    <option value="" disabled>Select Academic Year</option>
                                    <?php
                                    $currentYear = date("Y");
                                    $nextYear = $currentYear + 3; // Extend three years into the future
                                    
                                    // Generate future academic year options
                                    for ($year = $currentYear; $year <= $nextYear; $year++) {
                                        // Check if this year is the same as the one from the database
                                        $value = "$year-" . ($year + 1);
                                        $selected = (isset($academic_year) && $academic_year === $value) ? 'selected' : '';
                                        echo "<option value='$value' $selected>$year - " . ($year + 1) . "</option>";
                                    } ?>
                                    </option>
                                </select>
                            </div>

                            <button class="btn btn-success float-right mx-3" type="submit"
                                name="setSAYClassroom">Set</button>
                        </div>

                    </form>

                </div>
                <div>
                    <button class="btn btn-success float-right mx-3" data-bs-toggle="modal"
                        data-bs-target="#classroomCategoriesModal">+ Categories</button>
                    <button class="btn btn-success float-right" data-bs-toggle="modal"
                        data-bs-target="#classroomEvaluationModal">+ Criteria</button>
                    <button class="btn btn-success float-right mx-3" data-bs-toggle="modal"
                        data-bs-target="#addQuestionsModal">+ Additional Questions</button>
                </div>
            </div>

            <div class="overflow-auto" style="max-height: 580px">
                <?php
                $sql = "SELECT * FROM `classroomcategories`";
                $result = mysqli_query($con, $sql);

                if ($result) {
                    while ($row = mysqli_fetch_array($result)) {
                        $categories = htmlspecialchars($row['categories']);
                        $counter = 1;

                        // Query to check if the category has criteria before generating the table
                        $sqlcriteria = "SELECT * FROM `classroomcriteria` WHERE classroomCategories = '$categories'";
                        $resultCriteria = mysqli_query($con, $sqlcriteria);
                        $hasCriteria = (mysqli_num_rows($resultCriteria) > 0) ? 'yes' : 'no'; // Set flag
                
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

                        if ($hasCriteria === 'yes') {
                            while ($criteriaRow = mysqli_fetch_array($resultCriteria)) {
                                echo '
                                    <tr>
                                        <td>' . $counter++ . '</td>
                                        <td>
                                            <p style="text-align: justify; margin: 0;">
                                                ' . htmlspecialchars($criteriaRow['classroomCriteria']) . ' 
                                            </p>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn btn-primary classroom-edit-btn mx-2" 
                                                data-classroomid="' . $criteriaRow['id'] . '" 
                                                data-classroomcriteria="' . htmlspecialchars($criteriaRow['classroomCriteria']) . '" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editclassroomModal">
                                                Edit</button>
                                                <a href="javascript:void(0);" 
                                                class="btn btn-danger classroom-delete-btn" 
                                                data-classroomid="' . $criteriaRow['id'] . '" 
                                                data-classroomtype="criteria">Delete</a>
                                            </div>
                                        </td>
                                    </tr>
                                ';
                            }
                        } else {
                            echo '
                                <tr> 

                                    <td rowspan="3"><h3>NO CRITERIA</h3></td>
                                </tr>
                            ';
                        }

                        // Delete category button
                        echo '
                            <tr>
                                <td colspan="3">     
                                    <a href="javascript:void(0);" class="btn btn-danger classroom-delete-btn" 
                                    data-classroomid="' . $row['id'] . '" 
                                    data-hascriteria="' . $hasCriteria . '" 
                                    data-classroomtype="classroomcategory">Delete Category</a>
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



                <table class="table table-striped table-bordered text-center align-middle">
                    <thead>
                        <tr class="bg-danger">
                            <th class="text-justify">No.</th>
                            <th style="text-align: center; margin: 0;">ADDITIONAL QUESTIONS</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        $questionsSQL = "SELECT * FROM `classroomquestions`";
                        $questionsSQL_query = mysqli_query($con, $questionsSQL);

                        if (mysqli_num_rows($questionsSQL_query) > 0) {
                            $counter = 1;
                            while ($questionsRow = mysqli_fetch_Assoc($questionsSQL_query)) {

                                echo '
                            <tr>
                            <td>' . $counter++ . '</td>
                                <td>
                                    <p style="text-align: justify; margin: 0;">' . htmlspecialchars($questionsRow['questions']) . '</p>
                                </td>
                                <td>
                                                <div class="btn-group" role="group">
                                                    <button class="btn btn-primary question-edit-btn mx-2" 
                                                    data-questionid="' . $questionsRow['id'] . '" 
                                                    data-question="' . htmlspecialchars($questionsRow['questions']) . '" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editquestionModal">
                                                    Edit</button>
                                                    <a href="javascript:void(0);" 
                                                    class="btn btn-danger question-delete-btn" 
                                                    data-questionid="' . $questionsRow['id'] . '">Delete</a>
                                                </div>
                                            </td>
                            </tr>
                        ';
                            }
                        } else {
                            echo '
                                <tr> 
                                    <td ></td>
                                    <td rowspan="3"><h3>NO ADDITIONAL QUESTIONS</h3></td>
                                    <td ></td>
                                </tr>
                            ';
                        }

                        ?>
                    </tbody>
                </table>

            </div>

        </div>

        <!-- CLASSROOM ADDING NEW CATEGORIES -->
        <div class="modal fade" id="classroomCategoriesModal" tabindex="-1"
            aria-labelledby="classroomCategoriesModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="classroomCategoriesModalLabel">Add New Evaluation Categories</h5>
                        <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form action="../../controller/criteria.php" method="POST">
                        <div class="modal-body">

                            <div class="form-group p-2 mb-2">
                                <label for="classroomCategory" class="form-label">Add new evaluation category:</label>
                                <input type="text" id="classroomCategory" class="form-control text-uppercase"
                                    name="newCategory" aria-label="Username" aria-describedby="basic-addon1">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="addClassroomCategory" class="btn btn-success">ADD</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- CLASSROOM ADDING NEW CRITERIA -->
        <div class="modal fade" id="classroomEvaluationModal" tabindex="-1"
            aria-labelledby="classroomEvaluationModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="classroomEvaluationModalLabel">Add New Evaluation Criteria</h5>
                        <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form action="../../controller/criteria.php" method="POST">
                        <div class="modal-body">
                            <div class="form-group p-2 mb-2">
                                <label for="classroomevaluation" class="form-label">Choose an evaluation
                                    category:</label>
                                <select id="classroomevaluation" name="classroomObservationCategories"
                                    class="form-select">
                                    <?php

                                    $categorieSQL = "SELECT * FROM classroomcategories";
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
                                <textarea class="form-control" name="classroomObservationCriteria" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="addClassroomCriteria" class="btn btn-success">ADD</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- CLASSROOM EDITING CRITERIA -->
        <div class="modal fade" id="editclassroomModal" tabindex="-1" aria-labelledby="editclassroomModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header  bg-danger text-white">
                        <h5 class="modal-title" id="editclassroomModalLabel">Edit Criteria</h5>
                        <button type="button" class="btn-close  bg-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editclassroomForm" method="post" action="../../controller/criteria.php">
                            <input type="hidden" id="classroomcriteriaId" name="classroomcriteriaId">
                            <div class="mb-3">
                                <textarea class="form-control" id="classroomcriteriaText" name="classroomcriteriaText"
                                    rows="3" required></textarea>
                            </div>
                            <button type="submit" name="updateClassroomCriteria" class="btn btn-primary">Save
                                changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- CLASSROOM ADDING NEW ADDITIONAL QUESTIONS -->
        <div class="modal fade" id="addQuestionsModal" tabindex="-1" aria-labelledby="addQuestionsModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="addQuestionsModalLabel">Add New Additional Questions </h5>
                        <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form action="../../controller/criteria.php" method="POST">
                        <div class="modal-body">
                            <div class="form-group p-2 mb-2">
                                <label for="additonalQuestions" class="form-label">Additional Questions :</label>
                                <textarea class="form-control " name="classroomAdditionalQuestions"
                                    id="additonalQuestions" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="addAdditionalQuestions" class="btn btn-success">ADD</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- CLASSROOM EDITING ADDITIONAL QUESTIONS -->
        <div class="modal fade" id="editquestionModal" tabindex="-1" aria-labelledby="editquestionModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header  bg-danger text-white">
                        <h5 class="modal-title" id="editquestionModalLabel">Edit Additional Questions</h5>
                        <button type="button" class="btn-close  bg-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editquestions">
                            <input type="hidden" id="questionid" name="questionid">
                            <div class="mb-3">
                                <textarea class="form-control" id="question" name="question" rows="3"
                                    required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Save
                                changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- #################### FACULTY EVALUATION TAB #################### -->

        <!-- FACULTY EVALUATION PANEL -->
        <div class="tab-pane fade" id="nav-faculty" role="tabpanel" aria-labelledby="nav-faculty-tab">

            <div class="d-flex justify-content-between mb-3 ">
                <form action="">
                    <div class="d-flex px-3">
                        <div class="d-flex flex-column align-items-center px-2">
                            <select id="semesterSelect" name="semesterSelect" class="form-control">
                                <option value="">--Select Semester--</option>
                                <option value="FIRST">1st Semester</option>
                                <option value="SECOND">2nd Semester</option>
                            </select>
                        </div>
                        <div class="d-flex flex-column align-items-center px-2">
                            <select id="academicYearSelect" name="academicYearSelect" class="form-control">
                                <option value="" disabled>Select Academic Year</option>
                                <?php
                                $currentYear = date("Y");
                                $nextYear = $currentYear + 3; // Extend five years into the future
                                
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
                        $categories = htmlspecialchars($row['categories']);
                        $counter = 1;

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

                        $sqlcriteria = "SELECT * FROM `facultycriteria` WHERE facultyCategories = '$categories'";
                        $resultCriteria = mysqli_query($con, $sqlcriteria);
                        $hasCriteria = (mysqli_num_rows($resultCriteria) > 0) ? 'yes' : 'no'; // Set flag
                
                        if ($hasCriteria === 'yes') {
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
                                                    <button class="btn btn-primary edit-btn  mx-2" data-id="' . $criteriaRow['id'] . '" data-criteria="' . htmlspecialchars($criteriaRow['facultyCriteria']) . '" data-bs-toggle="modal" data-bs-target="#editCriteriaModal">Edit</button>


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
                                    <a href="javascript:void(0);" class="btn btn-danger delete-btn" data-hascriteria="' . $hasCriteria . '"  data-id="' . $row['id'] . '" data-type="category">Delete Categories</a>

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

        <!-- FACULTY ADDING NEW CATEGORIES -->
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
                                <label for="facultyCategory" class="form-label">Add new evaluation category:</label>
                                <input type="text" id="facultyCategory" class="form-control text-uppercase"
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

        <!-- FACULTY ADDING NEW CRITERIA -->
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
                                <label for="facultyevaluation" class="form-label">Choose an evaluation category:</label>
                                <select id="facultyevaluation" name="facultyEvaluationCategories" class="form-select">
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
                                <textarea class="form-control " name="facultyEvaluationCriteria" rows="3"></textarea>
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

        <!-- FACULTY EDITING CRITERIA -->
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

        <!-- #################### STUDENT EVALUATION TAB #################### -->

        <!-- STUDENTS EVALUATION PANEL -->
        <div class="tab-pane fade " id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
            <div class="d-flex justify-content-between mb-3 ">
                <form id="evaluationForm">
                    <div class="d-flex px-3">
                        <div class="d-flex flex-column align-items-center px-2">
                            <select id="termSelect" class="form-control">
                                <option value="">--Select Semester--</option>
                                <option value="FIRST">1st Semester</option>
                                <option value="SECOND">2nd Semester</option>
                            </select>
                        </div>
                        <div class="d-flex flex-column align-items-center px-2">
                            <select id="yearSelect" class="form-control">
                                <option value="" disabled>Select Academic Year</option>
                                <?php
                                $currentAcademicYear = date("Y");
                                $futureYear = $currentAcademicYear + 3; // Extend five years into the future
                                
                                // Generate future academic year options
                                for ($year = $currentAcademicYear; $year <= $futureYear; $year++) {
                                    echo "<option value='$year-" . ($year + 1) . "'>$year - " . ($year + 1) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="d-flex flex-column align-items-center px-2">
                            <button type="button" class="btn btn-studenttoggle" data-toggle="button"
                                aria-pressed="false">
                                <div class="handle"></div>
                            </button>
                        </div>
                    </div>
                </form>




                <div class="d-flex justify-content-end mb-3">
                    <button class="btn btn-success float-right mx-3" data-bs-toggle="modal"
                        data-bs-target="#studentCategoriesModal">+ Categories</button>
                    <button class="btn btn-success float-right" data-bs-toggle="modal"
                        data-bs-target="#studentEvaluationModal">+ Criteria</button>
                </div>
            </div>

            <div>
                <h3 id="actionLabelStudent" class="fw-bold text-center">Assigning Random IDs</h3>
            </div>
            <!-- Student Evaluation Panel -->
            <div class="overflow-auto" style="max-height: 580px">
                <?php

                $sql = "SELECT * FROM `studentscategories`";
                $result = mysqli_query($con, $sql);

                if ($result) {
                    while ($row = mysqli_fetch_array($result)) {
                        $categories = htmlspecialchars($row['categories']);
                        $counter = 1;

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

                        $sqlcriteria = "SELECT * FROM `studentscriteria` WHERE studentsCategories = '$categories'";
                        $resultCriteria = mysqli_query($con, $sqlcriteria);
                        $hasCriteria = (mysqli_num_rows($resultCriteria) > 0) ? 'yes' : 'no'; // Set flag
                
                        if ($hasCriteria === 'yes') {
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
                                                    <button class="btn btn-primary student-edit-btn  mx-2" 
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
                                            <a href="javascript:void(0);" class="btn btn-danger students-delete-btn"                     data-hascriteria="' . $hasCriteria . '"  data-studentsid="' . $row['id'] . '" data-studentstype="category">Delete Categories</a>

                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            ';
                    }
                }
                ?>
            </div>
        </div>

        <!-- STUDENTS ADDING NEW CATEGORIES -->
        <div class="modal fade" id="studentCategoriesModal" tabindex="-1" aria-labelledby="studentCategoriesModalLabel"
            aria-hidden="true">
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

        <!-- STUDENTS ADDING NEW CRITERIA -->
        <div class="modal fade" id="studentEvaluationModal" tabindex="-1" aria-labelledby="studentEvaluationModalLabel"
            aria-hidden="true">
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
                                <label for="studentevaluation" class="form-label">Choose an evaluation
                                    category:</label>
                                <select id="studentevaluation" name="studentsEvaluationCategories" class="form-select">
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
                                <textarea class="form-control" name="studentsEvaluationCriteria" rows="3"></textarea>
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

        <!-- STUDENTS EDITING CRITERIA -->
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

        <!-- SUBJECT LINKS -->
        <div class="tab-pane fade " id="nav-links" role="tabpanel" aria-labelledby="nav-links-tab">
            <div class="d-flex justify-content-between mb-3">
                <div class="mb-3 mx-5  w-100">
                    <input type="text" id="searchSubject" class="form-control" placeholder="Search for subjects..." />
                </div>

            </div>
            <!-- Student Evaluation Panel -->
            <div class="overflow-auto" style="max-height: 580px">

                <!-- Search Input -->


                <table class="table table-striped table-bordered text-center align-middle" id="subjectTable">
                    <thead>
                        <tr class="bg-danger text-center align-middle">
                            <th>Subject Code</th>
                            <th>Subject</th>
                            <th>Link</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT * FROM `subject`";
                        $result = mysqli_query($con, $sql);

                        if ($result) {
                            while ($subjectResult = mysqli_fetch_assoc($result)) {
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($subjectResult['subject_code']); ?></td>
                                    <td><?php echo htmlspecialchars($subjectResult['subject']); ?></td>
                                    <td>
                                        <ul style="list-style: none; padding: 0; margin: 0;">
                                            <?php
                                            // Check and display linkOne
                                            if (!empty($subjectResult['linkOne'])) {
                                                ?>
                                                <li><a href="<?php echo htmlspecialchars($subjectResult['linkOne']); ?>"
                                                        target="_blank">Link One</a></li>
                                                <?php
                                            }

                                            // Check and display linkTwo
                                            if (!empty($subjectResult['linkTwo'])) {
                                                ?>
                                                <li><a href="<?php echo htmlspecialchars($subjectResult['linkTwo']); ?>"
                                                        target="_blank">Link Two</a></li>
                                                <?php
                                            }
                                            if (!empty($subjectResult['linkThree'])) {
                                                ?>
                                                <li><a href="<?php echo htmlspecialchars($subjectResult['linkThree']); ?>"
                                                        target="_blank">Link Three</a></li>
                                                <?php
                                            }
                                            if (empty($subjectResult['linkOne']) && empty($subjectResult['linkTwo'])) {
                                                echo 'No links available for this subject';
                                            }
                                            ?>
                                        </ul>
                                    </td>
                                    <td>
                                        <button class="btn btn-primary mx-2 editLinks-btn"
                                            data-subjectid="<?php echo htmlspecialchars($subjectResult['subject_id']); ?>"
                                            data-linkone="<?php echo htmlspecialchars($subjectResult['linkOne']); ?>"
                                            data-linktwo="<?php echo htmlspecialchars($subjectResult['linkTwo']); ?>"
                                            data-linkthree="<?php echo htmlspecialchars($subjectResult['linkThree']); ?>"
                                            data-bs-toggle="modal" data-bs-target="#linksModal">Update
                                        </button>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                        ?>
                    </tbody>
                </table>

            </div>
        </div>

        <!-- UPDATE SUBJECT LINKS MODAL -->
        <div class="modal fade" id="linksModal" tabindex="-1" aria-labelledby="linksModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="linksModalLabel">Update Faculty Evaluation Criteria for Students
                        </h5>
                        <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form action="../../controller/criteria.php" method="POST" id="linksForm">
                        <input type="hidden" id="linkId" name="linkId">
                        <div class="modal-body">
                            <div class="form-group p-2 mb-2">
                                <label for="link" class="form-label">Paste link One here:</label>
                                <textarea class="form-control" id="linkOne" name="linkOne" rows="3"></textarea>
                            </div>
                            <div class="form-group p-2 mb-2">
                                <label for="link" class="form-label">Paste link Two here:</label>
                                <textarea class="form-control" id="linkTwo" name="linkTwo" rows="3"></textarea>
                            </div>
                            <div class="form-group p-2 mb-2">
                                <label for="link" class="form-label">Paste link Three here:</label>
                                <textarea class="form-control" id="linkThree" name="linkThree" rows="3"></textarea>
                                <!-- Change this to 'linkThree' -->
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="updateLink" class="btn btn-success">Update</button>
                        </div>
                    </form>


                </div>
            </div>
        </div>

        <!-- CATEGORIES LINKS -->
        <div class="tab-pane fade " id="nav-categoriesLinks" role="tabpanel" aria-labelledby="nav-categoriesLinks-tab">

            <div class="overflow-auto" style="max-height: 580px">

                <!-- PEER TO PEER EVALUATION CATEGORIES LINKS -->
                <div class="my-3">
                    <h4 class="px-3 fw-bold">Peer to Peer Evaluation Links</h4>
                    <table class="table table-striped table-bordered text-center align-middle" id="subjectTable">
                        <thead>
                            <tr class="bg-danger text-center align-middle">
                                <th style="max-width: 200px !important; ">Category</th>
                                <th>Link</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM `facultycategories`";
                            $result = mysqli_query($con, $sql);

                            if ($result) {
                                while ($subjectResult = mysqli_fetch_assoc($result)) {
                                    ?>
                                    <tr>
                                        <td style="max-width: 200px !important; ">
                                            <?php echo htmlspecialchars($subjectResult['categories']); ?>
                                        </td>
                                        <td>
                                            <ul style="list-style: none; padding: 0; margin: 0;">
                                                <?php
                                                // Check and display linkOne
                                                if (!empty($subjectResult['linkOne'])) {
                                                    ?>
                                                    <li><a href="<?php echo htmlspecialchars($subjectResult['linkOne']); ?>"
                                                            target="_blank">Link One</a></li>
                                                    <?php
                                                }

                                                // Check and display linkTwo
                                                if (!empty($subjectResult['linkTwo'])) {
                                                    ?>
                                                    <li><a href="<?php echo htmlspecialchars($subjectResult['linkTwo']); ?>"
                                                            target="_blank">Link Two</a></li>
                                                    <?php
                                                }
                                                if (!empty($subjectResult['linkThree'])) {
                                                    ?>
                                                    <li><a href="<?php echo htmlspecialchars($subjectResult['linkThree']); ?>"
                                                            target="_blank">Link Three</a></li>
                                                    <?php
                                                }
                                                if (empty($subjectResult['linkOne']) && empty($subjectResult['linkTwo'])) {
                                                    echo 'No links available for this subject';
                                                }
                                                ?>
                                            </ul>
                                        </td>
                                        <td>
                                            <button class="btn btn-primary mx-2 peertopeerLinks-btn"
                                                data-peertopeerid="<?php echo htmlspecialchars($subjectResult['id']); ?>"
                                                data-peertopeerlinkone="<?php echo htmlspecialchars($subjectResult['linkOne']); ?>"
                                                data-peertopeerlinktwo="<?php echo htmlspecialchars($subjectResult['linkTwo']); ?>"
                                                data-peertopeerlinkthree="<?php echo htmlspecialchars($subjectResult['linkThree']); ?>"
                                                data-bs-toggle="modal" data-bs-target="#peertopeerModal">
                                                Update
                                            </button>

                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- STUDENTS EVALUATION CATEGORIES LINKS -->
                <div class="my-3">
                    <h4 class="px-3 fw-bold">Students Evaluation Links</h4>
                    <table class="table table-striped table-bordered text-center align-middle" id="subjectTable">
                        <thead>
                            <tr class="bg-danger text-center align-middle">
                                <th style="max-width: 300px;">Category</th>
                                <th>Link</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM `studentscategories` WHERE NOT categories = 'TEACHING EFFECTIVENESS' ";
                            $result = mysqli_query($con, $sql);

                            if ($result) {
                                while ($subjectResult = mysqli_fetch_assoc($result)) {
                                    ?>
                                    <tr>
                                        <td style="max-width: 300px;">
                                            <?php echo htmlspecialchars($subjectResult['categories']); ?>
                                        </td>
                                        <td>
                                            <ul style="list-style: none; padding: 0; margin: 0;">
                                                <?php
                                                // Check and display linkOne
                                                if (!empty($subjectResult['linkOne'])) {
                                                    ?>
                                                    <li><a href="<?php echo htmlspecialchars($subjectResult['linkOne']); ?>"
                                                            target="_blank">Link One</a></li>
                                                    <?php
                                                }

                                                // Check and display linkTwo
                                                if (!empty($subjectResult['linkTwo'])) {
                                                    ?>
                                                    <li><a href="<?php echo htmlspecialchars($subjectResult['linkTwo']); ?>"
                                                            target="_blank">Link Two</a></li>
                                                    <?php
                                                }
                                                if (!empty($subjectResult['linkThree'])) {
                                                    ?>
                                                    <li><a href="<?php echo htmlspecialchars($subjectResult['linkThree']); ?>"
                                                            target="_blank">Link Three</a></li>
                                                    <?php
                                                }
                                                if (empty($subjectResult['linkOne']) && empty($subjectResult['linkTwo'])) {
                                                    echo 'No links available for this subject';
                                                }
                                                ?>
                                            </ul>
                                        </td>
                                        <td>
                                            <button class="btn btn-primary mx-2 studentsLinks-btn"
                                                data-studentsid="<?php echo htmlspecialchars($subjectResult['id']); ?>"
                                                data-studentslinkone="<?php echo htmlspecialchars($subjectResult['linkOne']); ?>"
                                                data-studentslinktwo="<?php echo htmlspecialchars($subjectResult['linkTwo']); ?>"
                                                data-studentslinkthree="<?php echo htmlspecialchars($subjectResult['linkThree']); ?>"
                                                data-bs-toggle="modal" data-bs-target="#studentsModal">
                                                Update
                                            </button>

                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- CLASSROOM OBSERVATION CATEGORIES LINKS -->
                <div class="my-3">
                    <h4 class="px-3 fw-bold">Classroom Observation Links</h4>
                    <table class="table table-striped table-bordered text-center align-middle" id="subjectTable">
                        <thead>
                            <tr class="bg-danger text-center align-middle">
                                <th>Category</th>
                                <th>Link</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM `classroomcategories` WHERE NOT categories = 'CONTENT KNOWLEDGE AND RELEVANCE' ";
                            $result = mysqli_query($con, $sql);

                            if ($result) {
                                while ($subjectResult = mysqli_fetch_assoc($result)) {
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($subjectResult['categories']); ?></td>
                                        <td>
                                            <ul style="list-style: none; padding: 0; margin: 0;">
                                                <?php
                                                // Check and display linkOne
                                                if (!empty($subjectResult['linkOne'])) {
                                                    ?>
                                                    <li><a href="<?php echo htmlspecialchars($subjectResult['linkOne']); ?>"
                                                            target="_blank">Link One</a></li>
                                                    <?php
                                                }

                                                // Check and display linkTwo
                                                if (!empty($subjectResult['linkTwo'])) {
                                                    ?>
                                                    <li><a href="<?php echo htmlspecialchars($subjectResult['linkTwo']); ?>"
                                                            target="_blank">Link Two</a></li>
                                                    <?php
                                                }
                                                if (!empty($subjectResult['linkThree'])) {
                                                    ?>
                                                    <li><a href="<?php echo htmlspecialchars($subjectResult['linkThree']); ?>"
                                                            target="_blank">Link Three</a></li>
                                                    <?php
                                                }
                                                if (empty($subjectResult['linkOne']) && empty($subjectResult['linkTwo'])) {
                                                    echo 'No links available for this subject';
                                                }
                                                ?>
                                            </ul>
                                        </td>
                                        <td>
                                            <button class="btn btn-primary mx-2 classroomLinks-btn"
                                                data-classroomid="<?php echo htmlspecialchars($subjectResult['id']); ?>"
                                                data-classroomlinkone="<?php echo htmlspecialchars($subjectResult['linkOne']); ?>"
                                                data-classroomlinktwo="<?php echo htmlspecialchars($subjectResult['linkTwo']); ?>"
                                                data-classroomlinkthree="<?php echo htmlspecialchars($subjectResult['linkThree']); ?>"
                                                data-bs-toggle="modal" data-bs-target="#classroomModal">
                                                Update
                                            </button>

                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <!-- VC AA LINKS -->
                <div class="my-3">
                    <h4 class="px-3 fw-bold">VCAA Links</h4>
                    <table class="table table-striped table-bordered text-center align-middle" id="subjectTable">
                        <thead>
                            <tr class="bg-danger text-center align-middle">
                                <th>Category</th>
                                <th>Link</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM `vcaacategories` WHERE NOT categories = 'KNOWLEDGE OF THE SUBJECT' ";
                            $result = mysqli_query($con, $sql);

                            if ($result) {
                                while ($subjectResult = mysqli_fetch_assoc($result)) {
                                    ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($subjectResult['categories']); ?></td>
                                        <td>
                                            <ul style="list-style: none; padding: 0; margin: 0;">
                                                <?php
                                                if (!empty($subjectResult['linkOne'])) {
                                                    ?>
                                                    <li><a href="<?php echo htmlspecialchars($subjectResult['linkOne']); ?>"
                                                            target="_blank">Link One</a></li>
                                                    <?php
                                                }
                                                if (!empty($subjectResult['linkTwo'])) {
                                                    ?>
                                                    <li><a href="<?php echo htmlspecialchars($subjectResult['linkTwo']); ?>"
                                                            target="_blank">Link Two</a></li>
                                                    <?php
                                                }
                                                if (!empty($subjectResult['linkThree'])) {
                                                    ?>
                                                    <li><a href="<?php echo htmlspecialchars($subjectResult['linkThree']); ?>"
                                                            target="_blank">Link Three</a></li>
                                                    <?php
                                                }
                                                if (empty($subjectResult['linkOne']) && empty($subjectResult['linkTwo'])) {
                                                    echo 'No links available for this subject';
                                                }
                                                ?>
                                            </ul>
                                        </td>
                                        <td>
                                            <button class="btn btn-primary mx-2 vcaaLinks-btn"
                                                data-vcaaid="<?php echo htmlspecialchars($subjectResult['id']); ?>"
                                                data-vcaalinkone="<?php echo htmlspecialchars($subjectResult['linkOne']); ?>"
                                                data-vcaalinktwo="<?php echo htmlspecialchars($subjectResult['linkTwo']); ?>"
                                                data-vcaalinkthree="<?php echo htmlspecialchars($subjectResult['linkThree']); ?>"
                                                data-bs-toggle="modal" data-bs-target="#vcaaModal">
                                                Update
                                            </button>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- UPDATE MODAL FOR PEER TO PEER EVALUATION -->
        <div class="modal fade" id="peertopeerModal" tabindex="-1" aria-labelledby="peertopeerModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="peertopeerModalLabel">Update Recommendation Links for Peer to Peer
                            Evaluation
                        </h5>
                        <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form action="../../controller/criteria.php" method="POST" id="peertopeerlinksForm">
                        <input type="hidden" id="peertopeerlinkId" name="peertopeerlinkId">
                        <div class="modal-body">
                            <div class="form-group p-2 mb-2">
                                <label for="link" class="form-label">Paste link One here:</label>
                                <textarea class="form-control" id="peertopeerlinkOne" name="peertopeerlinkOne"
                                    rows="3"></textarea>
                            </div>
                            <div class="form-group p-2 mb-2">
                                <label for="link" class="form-label">Paste link Two here:</label>
                                <textarea class="form-control" id="peertopeerlinkTwo" name="peertopeerlinkTwo"
                                    rows="3"></textarea>
                            </div>
                            <div class="form-group p-2 mb-2">
                                <label for="link" class="form-label">Paste link Three here:</label>
                                <textarea class="form-control" id="peertopeerlinkThree" name="peertopeerlinkThree"
                                    rows="3"></textarea>
                                <!-- Change this to 'linkThree' -->
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="updatepeertopeerLink" class="btn btn-success">Update</button>
                        </div>
                    </form>


                </div>
            </div>
        </div>

        <!-- UPDATE MODAL FOR STUDENTS EVALUATION -->
        <div class="modal fade" id="studentsModal" tabindex="-1" aria-labelledby="studentsModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="studentsModalLabel">Update Recommendation Links for Faculty
                            Evaluation
                        </h5>
                        <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form action="../../controller/criteria.php" method="POST" id="studentslinksForm">
                        <input type="hidden" id="studentslinkId" name="studentslinkId">
                        <div class="modal-body">
                            <div class="form-group p-2 mb-2">
                                <label for="link" class="form-label">Paste link One here:</label>
                                <textarea class="form-control" id="studentslinkOne" name="studentslinkOne"
                                    rows="3"></textarea>
                            </div>
                            <div class="form-group p-2 mb-2">
                                <label for="link" class="form-label">Paste link Two here:</label>
                                <textarea class="form-control" id="studentslinkTwo" name="studentslinkTwo"
                                    rows="3"></textarea>
                            </div>
                            <div class="form-group p-2 mb-2">
                                <label for="link" class="form-label">Paste link Three here:</label>
                                <textarea class="form-control" id="studentslinkThree" name="studentslinkThree"
                                    rows="3"></textarea>
                                <!-- Change this to 'linkThree' -->
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="updatestudentsLink" class="btn btn-success">Update</button>
                        </div>
                    </form>


                </div>
            </div>
        </div>

        <!-- UPDATE MODAL FOR CLASSROOM OBSERVATION -->
        <div class="modal fade" id="classroomModal" tabindex="-1" aria-labelledby="classroomModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="classroomModalLabel">Update Recommendation Links for Classroom
                            Observation
                        </h5>
                        <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form action="../../controller/criteria.php" method="POST" id="classroomlinksForm">
                        <input type="hidden" id="classroomlinkId" name="classroomlinkId">
                        <div class="modal-body">
                            <div class="form-group p-2 mb-2">
                                <label for="link" class="form-label">Paste link One here:</label>
                                <textarea class="form-control" id="classroomlinkOne" name="classroomlinkOne"
                                    rows="3"></textarea>
                            </div>
                            <div class="form-group p-2 mb-2">
                                <label for="link" class="form-label">Paste link Two here:</label>
                                <textarea class="form-control" id="classroomlinkTwo" name="classroomlinkTwo"
                                    rows="3"></textarea>
                            </div>
                            <div class="form-group p-2 mb-2">
                                <label for="link" class="form-label">Paste link Three here:</label>
                                <textarea class="form-control" id="classroomlinkThree" name="classroomlinkThree"
                                    rows="3"></textarea>
                                <!-- Change this to 'linkThree' -->
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="updateclassroomLink" class="btn btn-success">Update</button>
                        </div>
                    </form>


                </div>
            </div>
        </div>

        <!-- UPDATE MODAL FOR VCAA -->
        <div class="modal fade" id="vcaaModal" tabindex="-1" aria-labelledby="vcaaModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title" id="vcaaModalLabel">Update Recommendation Links for VCAA
                        </h5>
                        <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <form action="../../controller/criteria.php" method="POST" id="vcaalinksForm">
                        <input type="hidden" id="vcaalinkId" name="vcaalinkId">
                        <div class="modal-body">
                            <div class="form-group p-2 mb-2">
                                <label for="link" class="form-label">Paste link One here:</label>
                                <textarea class="form-control" id="vcaalinkOne" name="vcaalinkOne" rows="3"></textarea>
                            </div>
                            <div class="form-group p-2 mb-2">
                                <label for="link" class="form-label">Paste link Two here:</label>
                                <textarea class="form-control" id="vcaalinkTwo" name="vcaalinkTwo" rows="3"></textarea>
                            </div>
                            <div class="form-group p-2 mb-2">
                                <label for="link" class="form-label">Paste link Three here:</label>
                                <textarea class="form-control" id="vcaalinkThree" name="vcaalinkThree"
                                    rows="3"></textarea>
                                <!-- Change this to 'linkThree' -->
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="updatevcaaLink" class="btn btn-success">Update</button>
                        </div>
                    </form>


                </div>
            </div>
        </div>
    </div>

</section>


<!-- SWEETALERT SESSION FOR SUCCESS -->
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

    unset($_SESSION['success']);
}

?>



<script>
    $(document).ready(function () {

        // CLASSROOM EVALUATION BUTTONS

        // CLASSROOM EVALUATION FOR EDITING DATA
        $('.classroom-edit-btn').on('click', function () {
            const classroomcriteriaId = $(this).data('classroomid');
            const classroomcriteriaText = $(this).data('classroomcriteria');

            $('#classroomcriteriaId').val(classroomcriteriaId);
            $('#classroomcriteriaText').val(classroomcriteriaText);
        });

        // CLASSROOM EVALUATION FOR EDITING THE FORM
        $('#editclassroomForm').on('submit', function (e) {
            e.preventDefault();

            const classroomformData = $(this).serialize();

            $.ajax({
                url: '../../controller/criteria.php',
                method: 'POST',
                data: classroomformData,
                success: function (data) {
                    location.reload();
                },
                error: function (error) {
                    console.error('Error:', error);
                }
            });
        });

        // CLASSROOM EVALUATION FOR DELETING CRITERIA AND CATEGORIES
        $(document).on('click', '.classroom-delete-btn', function () {
            var classroomId = $(this).data('classroomid');
            var hasCriteria = $(this).data('hascriteria');
            var classroomType = $(this).data('classroomtype');
            var classroomAction = classroomType === 'classroomcategory' ? 'deleteClassroomid' : 'classroomdeleteid';

            if (classroomType === 'classroomcategory' && hasCriteria === 'yes') {
                // If the category has criteria, show an alert and block the deletion
                Swal.fire({
                    title: 'Cannot Delete',
                    text: 'This category has criteria and cannot be deleted.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            } else {
                // If no criteria, show confirmation dialog
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
                        // Proceed with deletion if confirmed
                        window.location.href = `../../controller/criteria.php?${classroomAction}=` + classroomId;
                    }
                });
            }
        });


        // CLASSROOM EVALUATION FOR EDITING ADDITIONAL QUESTIONS
        $('.question-edit-btn').on('click', function () {
            const questionid = $(this).data('questionid');
            const question = $(this).data('question');

            $('#questionid').val(questionid);
            $('#question').val(question);
        });

        // CLASSROOM EVALUATION FOR EDITING ADDITIONAL QUESTIONS
        $('#editquestions').on('submit', function (e) {
            e.preventDefault();

            const questionsformData = $(this).serialize();

            $.ajax({
                url: '../../controller/criteria.php',
                method: 'POST',
                data: questionsformData,
                success: function (data) {
                    location.reload();
                },
                error: function (error) {
                    console.error('Error:', error);
                }
            });
        });

        $('.question-delete-btn').on('click', function () {
            const questionid = $(this).data('questionid');

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
                    // Redirect to the deletion script with the question ID
                    window.location.href = `../../controller/criteria.php?questionid=${questionid}`;
                }
            });
        });

        // STUDENTS EVALUATION BUTTONS

        // STUDENTS EVALUATION FOR EDITING DATA
        $('.student-edit-btn').on('click', function () {
            const studentscriteriaId = $(this).data('studentsid');
            const studentscriteriaText = $(this).data('studentscriteria');

            $('#studentscriteriaId').val(studentscriteriaId);
            $('#studentscriteriaText').val(studentscriteriaText);
        });

        // STUDENTS EVALUATION FOR EDITING THE FORM
        $('#editStudentsCriteriaForm').on('submit', function (e) {
            e.preventDefault();

            const studentsformData = $(this).serialize();

            $.ajax({
                url: '../../controller/criteria.php',
                method: 'POST',
                data: studentsformData,
                success: function (data) {
                    location.reload();
                },
                error: function (error) {
                    console.error('Error:', error);
                }
            });
        });

        // STUDENTS EVALUATION FOR DELETING CRITERIA AND CATEGORIES
        $('.students-delete-btn').on('click', function () {
            const studentsid = $(this).data('studentsid');
            const studentstype = $(this).data('studentstype');
            var hasCriteria = $(this).data('hascriteria');
            const studentsaction = studentstype === 'category' ? 'deletestudentsCategoryid' : 'studentsdeleteid';

            if (studentstype === 'category' && hasCriteria === 'yes') {
                // If the category has criteria, show an alert and block the deletion
                Swal.fire({
                    title: 'Cannot Delete',
                    text: 'This category has criteria and cannot be deleted.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            } else {
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
                        window.location.href = `../../controller/criteria.php?${studentsaction}=` + studentsid;
                    }
                });
            }
        });

        // FACULTY EVALUATION BUTTONS

        // FACULTY EVALUATION FOR EDITING DATA
        $('.edit-btn').on('click', function () {
            const criteriaId = $(this).data('id');
            const criteriaText = $(this).data('criteria');

            $('#criteriaId').val(criteriaId);
            $('#criteriaText').val(criteriaText);
        });

        // FACULTY EVALUATION FOR EDITING THE FORM
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

        // FACULTY EVALUATION FOR DELETING CRITERIA AND CATEGORIES
        $('.delete-btn').on('click', function () {
            const id = $(this).data('id');
            const type = $(this).data('type');
            var hasCriteria = $(this).data('hascriteria');
            const action = type === 'category' ? 'deleteCategoryid' : 'deletefacultyid';

            if (type === 'category' && hasCriteria === 'yes') {
                // If the category has criteria, show an alert and block the deletion
                Swal.fire({
                    title: 'Cannot Delete',
                    text: 'This category has criteria and cannot be deleted.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            } else {
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
                        window.location.href = `../../controller/criteria.php?${action}=` + id;
                    }
                });
            }
        });


        // LINKS FOR EDITING

        $('#searchSubject').on('keyup', function () {
            const value = $(this).val().toLowerCase();
            $('#subjectTable tbody tr').filter(function () {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });

        // SUBJECT EDIT BUTTON
        $('.editLinks-btn').on('click', function () {
            const linkId = $(this).data('subjectid');
            const linkEdit = $(this).data('linkone');
            const linkTextEdit = $(this).data('linktwo');
            const linkTextEdittwo = $(this).data('linkthree');

            $('#linkId').val(linkId);
            $('#linkOne').val(linkEdit);
            $('#linkTwo').val(linkTextEdit);
            $('#linkThree').val(linkTextEdittwo);
        });

        // SUBJECT SUBMIT FORM
        $('#linksForm').on('submit', function (e) {
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

        // PEER TO PEER EDIT BUTTON
        $('.peertopeerLinks-btn').on('click', function () {
            const linkId = $(this).data('peertopeerid');
            const linkOne = $(this).data('peertopeerlinkone');
            const linkTwo = $(this).data('peertopeerlinktwo');
            const linkThree = $(this).data('peertopeerlinkthree');

            $('#peertopeerlinkId').val(linkId);
            $('#peertopeerlinkOne').val(linkOne);
            $('#peertopeerlinkTwo').val(linkTwo);
            $('#peertopeerlinkThree').val(linkThree);
        });

        // PEER TO PEER SUBMIT FORM
        $('#peertopeerlinksForm').on('submit', function (e) {
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

        // STUDENT EDIT BUTTON
        $('.studentsLinks-btn').on('click', function () {
            const linkId = $(this).data('studentsid');
            const linkOne = $(this).data('studentslinkone');
            const linkTwo = $(this).data('studentslinktwo');
            const linkThree = $(this).data('studentslinkthree');

            $('#studentslinkId').val(linkId);
            $('#studentslinkOne').val(linkOne);
            $('#studentslinkTwo').val(linkTwo);
            $('#studentslinkThree').val(linkThree);
        });

        // STUDENT SUBMIT FORM
        $('#studentslinksForm').on('submit', function (e) {
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

        // CLASSROOM EDIT BUTTON
        $('.classroomLinks-btn').on('click', function () {
            const linkId = $(this).data('classroomid');
            const linkOne = $(this).data('classroomlinkone');
            const linkTwo = $(this).data('classroomlinktwo');
            const linkThree = $(this).data('classroomlinkthree');

            $('#classroomlinkId').val(linkId);
            $('#classroomlinkOne').val(linkOne);
            $('#classroomlinkTwo').val(linkTwo);
            $('#classroomlinkThree').val(linkThree);
        });

        // CLASSROOM SUBMIT FORM
        $('#classroomlinksForm').on('submit', function (e) {
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

        // CLASSROOM EDIT BUTTON
        $('.vcaaLinks-btn').on('click', function () {
            const linkId = $(this).data('vcaaid');
            const linkOne = $(this).data('vcaalinkone');
            const linkTwo = $(this).data('vcaalinktwo');
            const linkThree = $(this).data('vcaalinkthree');

            $('#vcaalinkId').val(linkId);
            $('#vcaalinkOne').val(linkOne);
            $('#vcaalinkTwo').val(linkTwo);
            $('#vcaalinkThree').val(linkThree);
        });

        // CLASSROOM SUBMIT FORM
        $('#vcaalinksForm').on('submit', function (e) {
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






        // LOCALSTORAGE FOR SEMESTER AND ACADEMIC YEAR
        const storedSemester = localStorage.getItem('semester');
        const storedAcademicYear = localStorage.getItem('academicYear');

        if (storedSemester) {
            $('#semesterSelect').val(storedSemester);
        }

        if (storedAcademicYear) {
            $('#academicYearSelect').val(storedAcademicYear);
        }

        $('#semesterSelect').on('change', function () {
            localStorage.setItem('semester', $(this).val());
        });

        $('#academicYearSelect').on('change', function () {
            localStorage.setItem('academicYear', $(this).val());
        });

        let currentAction = 'clear';

        if (localStorage.getItem('action') === 'assign') {
            $('#semesterSelect').val(localStorage.getItem('semester'));
            $('#academicYearSelect').val(localStorage.getItem('academicYear'));
            currentAction = 'assign';
        } else {
            $('#semesterSelect').val('');
            $('#academicYearSelect').val('');
        }

        $('.btn-toggle').toggleClass('assigned', currentAction === 'assign');
        updateActionLabel(currentAction);

        $('.btn-toggle').on('click', function () {
            currentAction = (currentAction === 'assign') ? 'clear' : 'assign';

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
                        currentAction = 'clear';
                        $('.btn-toggle').removeClass('assigned');
                        updateActionLabel(currentAction);
                    }
                });
            } else {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'This will clear all selections. Do you want to proceed?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, clear it!',
                    cancelButtonText: 'No, cancel!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $('#semesterSelect').val('');
                        $('#academicYearSelect').val('');
                        currentAction = 'clear';
                        localStorage.removeItem('semester');
                        localStorage.removeItem('academicYear');
                        localStorage.removeItem('action');
                        executeAction(currentAction);
                    } else {
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

        function updateActionLabel(action) {
            const label = action === 'assign' ? 'The peer-to-peer evaluation is OPEN.' : 'The peer-to-peer evaluation is CLOSED.';
            $('#actionLabel').text(label);
            if (action === 'assign') {
                $('#actionLabel').css('color', 'green');
            } else {
                $('#actionLabel').css('color', 'red');
            }
        }

        const termDropdown = $('#termSelect');
        const yearDropdown = $('#yearSelect');
        let currentStatus = 'clear';

        const savedTerm = localStorage.getItem('term');
        const savedYear = localStorage.getItem('academicYear');
        if (savedTerm) termDropdown.val(savedTerm);
        if (savedYear) yearDropdown.val(savedYear);

        if (localStorage.getItem('status') === 'assign') {
            termDropdown.val(savedTerm);
            yearDropdown.val(savedYear);
            currentStatus = 'assign';
        }

        updateToggleButtonState();
        updateStatusLabel(currentStatus);

        termDropdown.on('change', () => localStorage.setItem('term', termDropdown.val()));
        yearDropdown.on('change', () => localStorage.setItem('academicYear', yearDropdown.val()));

        $('.btn-studenttoggle').on('click', handleToggleClick);

        function handleToggleClick() {
            currentStatus = (currentStatus === 'assign') ? 'clear' : 'assign';
            updateToggleButtonState();
            updateStatusLabel(currentStatus);

            if (currentStatus === 'assign') {
                handleAssignProcess();
            } else {
                handleClearProcess();
            }
        }

        function handleAssignProcess() {
            const selectedTerm = termDropdown.val();
            const selectedYear = yearDropdown.val();

            if (!selectedTerm || !selectedYear) {
                displayAlert('Missing Selections', 'Please select both a term and an academic year before proceeding.', 'warning');
                currentStatus = 'clear';
                updateToggleButtonState();
                return;
            }

            localStorage.setItem('term', selectedTerm);
            localStorage.setItem('academicYear', selectedYear);
            localStorage.setItem('status', 'assign');

            Swal.fire({
                title: 'Are you sure?',
                text: `You have selected ${selectedTerm} Term, Academic Year: ${selectedYear}. Proceed with assigning?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, assign it!',
                cancelButtonText: 'No, cancel!'
            }).then((result) => {
                if (result.isConfirmed) {
                    executeProcess(currentStatus, selectedTerm, selectedYear);
                } else {
                    resetStatusState();
                }
            });
        }

        function handleClearProcess() {
            Swal.fire({
                title: 'Are you sure?',
                text: 'This will clear all selections. Do you want to proceed?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, clear it!',
                cancelButtonText: 'No, cancel!'
            }).then((result) => {
                if (result.isConfirmed) {
                    termDropdown.val('');
                    yearDropdown.val('');
                    currentStatus = 'clear';
                    localStorage.removeItem('term');
                    localStorage.removeItem('academicYear');
                    localStorage.removeItem('status');
                    executeProcess(currentStatus);
                } else {
                    currentStatus = 'assign';
                    updateToggleButtonState();
                }
            });
        }

        function executeProcess(status, term, year) {
            const requestData = { studentaction: status, semester: term, academicYear: year };

            $.post("", requestData, function (response) {
                const result = JSON.parse(response);
                displayAlert('Success', result.message, 'success');

                if (status === 'assign') {
                    displayAlert('Random IDs Assigned', 'Random IDs have been successfully assigned.', 'success');
                }

            }).fail(function (xhr) {
                const errorMessage = xhr.responseJSON?.message || "An error occurred on the server.";
                displayAlert('Error', errorMessage, 'error');
            });
        }

        function displayAlert(title, message, icon) {
            Swal.fire({ title, text: message, icon, confirmButtonText: 'Okay' });
        }

        function updateToggleButtonState() {
            $('.btn-studenttoggle').toggleClass('assigned', currentStatus === 'assign');
        }

        function updateStatusLabel(status) {
            const labelText = status === 'assign' ? 'The Faculty evaluation is OPEN.' : 'The Faculty evaluation is CLOSED.';
            $('#actionLabelStudent').text(labelText).css('color', status === 'assign' ? 'green' : 'red');
        }

        function resetStatusState() {
            currentStatus = 'clear';
            $('.btn-studenttoggle').removeClass('assigned');
            updateStatusLabel(currentStatus);
        }

    });

</script>
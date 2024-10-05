<?php
include "components/navBar.php";
include "../../model/dbconnection.php";


// TOGGLE FOR OPENING AND CLOSING EVALUATION FOR PEER TO PEER
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['action'])) {
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
}

$initialAction = isset($_SESSION['toggle_state']) ? $_SESSION['toggle_state'] : 'assign';

?>

<head>

    <!-- PAGE TITLE -->
    <title>Questions</title>

    <!-- ALL STYLES AND CSS FILES -->
    <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/sweetalert.min.css">

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
        </div>
    </nav>

    <!-- NAVIGATION TAB CONTAINER -->
    <div class="tab-content p-3 border bg-light overflow-auto" id="nav-tabContent">

        <!-- #################### CLASSROOM EVALUATION TAB #################### -->

        <!-- CLASSROOM EVALUATION PANEL -->
        <div class="tab-pane fade active show" id="nav-classroom" role="tabpanel" aria-labelledby="nav-classroom-tab">

            <div class="d-flex justify-content-end mb-3 ">
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

    });
</script>
<?php
session_start();

include "../model/dbconnection.php";

function sanitizeColumnName($name)
{
    return preg_replace('/[^a-zA-Z0-9_]/', '', trim($name));
}

// ADDING NEW CLASSROOM CATEGORY
if (isset($_POST['addClassroomCategory'])) {
    $newCategorie = strtoupper($_POST['newCategory']);

    $sql = "INSERT INTO `classroomcategories` (categories) VALUES ('$newCategorie')";
    $sql_query = mysqli_query($con, $sql);

    if ($sql_query) {
        $last_id = mysqli_insert_id($con);

        $new_column = 'comment' . sanitizeColumnName($newCategorie) . $last_id;

        $check_column = mysqli_query($con, "SHOW COLUMNS FROM `classroomobservation` LIKE '$new_column'");
        if (mysqli_num_rows($check_column) == 0) {
            $alter_sql = "ALTER TABLE `classroomobservation` ADD `$new_column` VARCHAR(255)";
            mysqli_query($con, $alter_sql);
        }
        // Set success message
        $_SESSION['success'] = 'Category added successfully!';
        header('location:../view/adminModule/questionsTab.php');
        exit();
    } else {
        echo "Error: " . mysqli_error($con);
    }
}

// DELETING CLASSROOM CATEGORY
if (isset($_GET['deleteClassroomid'])) {
    $id = $_GET['deleteClassroomid'];

    $result = mysqli_query($con, "SELECT categories FROM `classroomcategories` WHERE id = $id");
    $row = mysqli_fetch_assoc($result);
    $column_to_delete = 'comment' . sanitizeColumnName($row['categories']) . $id;

    $delete_sql = "DELETE FROM `classroomcategories` WHERE id = '$id'";
    if (mysqli_query($con, $delete_sql)) {
        $check_column = mysqli_query($con, "SHOW COLUMNS FROM `classroomobservation` LIKE '$column_to_delete'");
        if (mysqli_num_rows($check_column) > 0) {
            $drop_sql = "ALTER TABLE `classroomobservation` DROP COLUMN `$column_to_delete`";
            mysqli_query($con, $drop_sql);
        }
        $_SESSION['success'] = 'Category Deleted!';
        header('location:../view/adminModule/questionsTab.php');
        exit();
    } else {
        echo "Error: " . mysqli_error($con);
    }
}

// ADDING NEW CLASSROOM CRITERIA
if (isset($_POST['addClassroomCriteria'])) {
    $classCategories = mysqli_real_escape_string($con, $_POST['classroomObservationCategories']);
    $classCriteria = mysqli_real_escape_string($con, $_POST['classroomObservationCriteria']);

    // Insert new criteria
    $sql = "INSERT INTO `classroomcriteria` (classroomCategories, classroomCriteria) VALUES ('$classCategories', '$classCriteria')";
    $sql_query = mysqli_query($con, $sql);

    if ($sql_query) {
        // Get the last inserted ID
        $last_id = mysqli_insert_id($con);

        // Create unique column name by concatenating criteria and last ID
        $new_column = sanitizeColumnName($classCategories) . $last_id;

        // Check and create new column in peertopeerform
        $check_column = mysqli_query($con, "SHOW COLUMNS FROM `classroomobservation` LIKE '$new_column'");
        if (mysqli_num_rows($check_column) == 0) {
            $alter_sql = "ALTER TABLE `classroomobservation` ADD `$new_column` VARCHAR(255)";
            mysqli_query($con, $alter_sql);
        }

        // Set success message
        $_SESSION['success'] = 'Criteria added successfully!';
        header('location:../view/adminModule/questionsTab.php');
        exit();
    } else {
        echo "Error: " . mysqli_error($con);
    }
}

// DELETING CLASSROOM CRITERIA
if (isset($_GET["classroomdeleteid"])) {
    $id = $_GET['classroomdeleteid'];

    $result = mysqli_query($con, "SELECT classroomCategories FROM `classroomcriteria` WHERE id = $id");
    $row = mysqli_fetch_assoc($result);
    $column_to_delete = sanitizeColumnName($row['classroomCategories']) . $id;


    $delete_sql = "DELETE FROM `classroomcriteria` WHERE id = $id";
    if (mysqli_query($con, $delete_sql)) {
        $check_column = mysqli_query($con, "SHOW COLUMNS FROM `classroomobservation` LIKE '$column_to_delete'");

        if (mysqli_num_rows($check_column) > 0) {
            $drop_sql = "ALTER TABLE `classroomobservation` DROP COLUMN `$column_to_delete`";
            mysqli_query($con, $drop_sql);
        }
        $_SESSION['success'] = 'Criteria Deleted!';
        header('location:../view/adminModule/questionsTab.php');
        exit();
    } else {
        echo "Error: " . mysqli_error($con);
    }
}

// CREATE NEW AADITIONAL QUESTIONS QUERY
if (isset($_POST['addAdditionalQuestions'])) {
    $new_questions = mysqli_real_escape_string($con, $_POST['classroomAdditionalQuestions']);

    $addQuestionsSQL = "INSERT INTO `classroomquestions` (questions) VALUES ('$new_questions')";
    $addQuestionsSQL_query = mysqli_query($con, $addQuestionsSQL);

    if ($addQuestionsSQL_query) {
        // Get the last inserted ID
        $last_id = mysqli_insert_id($con);

        // Create unique column name by concatenating criteria and last ID
        $new_column = sanitizeColumnName('QUESTIONNO') . $last_id;

        // Check and create new column in peertopeerform
        $check_column = mysqli_query($con, "SHOW COLUMNS FROM `classroomobservation` LIKE '$new_column'");
        if (mysqli_num_rows($check_column) == 0) {
            $alter_sql = "ALTER TABLE `classroomobservation` ADD `$new_column` VARCHAR(255)";
            mysqli_query($con, $alter_sql);
        }

        // Set success message
        $_SESSION['success'] = 'Criteria added successfully!';
        header('location:../view/adminModule/questionsTab.php');
        exit();
    } else {
        echo "Error: " . mysqli_error($con);
    }

}

// DELETING CLASSROOM ADDITIONAL QUESTIONS
if (isset($_GET["questionid"])) {
    $id = $_GET['questionid'];

    $result = mysqli_query($con, "SELECT questions FROM `classroomquestions` WHERE id = $id");
    if (mysqli_num_rows($result) > 0) {

        $delete_sql = "DELETE FROM `classroomquestions` WHERE id = $id";
        if (mysqli_query($con, $delete_sql)) {

            $column_to_delete = 'QUESTIONNO' . $id;
            $check_column = mysqli_query($con, "SHOW COLUMNS FROM `classroomobservation` LIKE '$column_to_delete'");

            if (mysqli_num_rows($check_column) > 0) {
                $drop_sql = "ALTER TABLE `classroomobservation` DROP COLUMN `$column_to_delete`";
                mysqli_query($con, $drop_sql);
            }
            $_SESSION['success'] = 'Questions Deleted!';
            header('Location: ../view/adminModule/questionsTab.php');
            exit();
        } else {
            echo "Error deleting record: " . mysqli_error($con);
        }
    } else {
        echo "No question found with the provided ID.";
    }

    mysqli_close($con);
}





// ADDING NEW FACULTY CATEGORY
if (isset($_POST['addCategory'])) {
    $newCategorie = strtoupper($_POST['newCategory']);

    $sql = "INSERT INTO `facultycategories` (categories) VALUES ('$newCategorie')";
    $sql_query = mysqli_query($con, $sql);

    if ($sql_query) {
        // Set success message
        $_SESSION['success'] = 'Category added successfully!';
        header('location:../view/adminModule/questionsTab.php');
    }

}

// DELETING FACULTY CATEGORY
if (isset($_GET['deleteCategoryid'])) {
    $id = $_GET['deleteCategoryid'];
    $sql = "DELETE FROM `facultycategories` WHERE id = '$id'";
    $sql_query = mysqli_query($con, $sql);
    if ($sql_query) {
        $_SESSION['success'] = 'Category Deleted!';
        header('location:../view/adminModule/questionsTab.php');
    }
}

// ADDING NEW FACULTY CRITERIA
if (isset($_POST['addFacultyCriteria'])) {
    $feCategories = mysqli_real_escape_string($con, $_POST['facultyEvaluationCategories']);
    $feCriteria = mysqli_real_escape_string($con, $_POST['facultyEvaluationCriteria']);

    // Insert new criteria
    $sql = "INSERT INTO `facultycriteria` (facultyCategories, facultyCriteria) VALUES ('$feCategories', '$feCriteria')";
    $sql_query = mysqli_query($con, $sql);

    if ($sql_query) {
        // Get the last inserted ID
        $last_id = mysqli_insert_id($con);

        // Create unique column name by concatenating criteria and last ID
        $new_column = sanitizeColumnName($feCategories) . $last_id;

        // Check and create new column in peertopeerform
        $check_column = mysqli_query($con, "SHOW COLUMNS FROM `peertopeerform` LIKE '$new_column'");
        if (mysqli_num_rows($check_column) == 0) {
            $alter_sql = "ALTER TABLE `peertopeerform` ADD `$new_column` VARCHAR(255)";
            mysqli_query($con, $alter_sql);
        }

        // Set success message
        $_SESSION['success'] = 'Criteria added successfully!';
        header('location:../view/adminModule/questionsTab.php');
        exit();
    } else {
        echo "Error: " . mysqli_error($con);
    }
}

// DELETING FACULTY CRITERIA
if (isset($_GET["deletefacultyid"])) {
    $id = $_GET['deletefacultyid'];

    $result = mysqli_query($con, "SELECT facultyCategories FROM `facultycriteria` WHERE id = $id");
    $row = mysqli_fetch_assoc($result);
    $column_to_delete = sanitizeColumnName($row['facultyCategories']) . $id;

    $delete_sql = "DELETE FROM `facultycriteria` WHERE id = $id";
    if (mysqli_query($con, $delete_sql)) {
        $check_column = mysqli_query($con, "SHOW COLUMNS FROM `peertopeerform` LIKE '$column_to_delete'");

        if (mysqli_num_rows($check_column) > 0) {
            $drop_sql = "ALTER TABLE `peertopeerform` DROP COLUMN `$column_to_delete`";
            mysqli_query($con, $drop_sql);
        }
        $_SESSION['success'] = 'Criteria Deleted!';
        header('location:../view/adminModule/questionsTab.php');
        exit();
    } else {
        echo "Error: " . mysqli_error($con);
    }
}





// ADDING NEW STUDENT CATEGORY
if (isset($_POST['addstudentCategory'])) {
    $newCategorie = strtoupper($_POST['studentCategory']);

    $sql = "INSERT INTO `studentscategories` (categories) VALUES ('$newCategorie')";
    $sql_query = mysqli_query($con, $sql);

    if ($sql_query) {
        // Set success message
        $_SESSION['success'] = 'Category added successfully!';
        header('location:../view/adminModule/questionsTab.php');
    }
}

// DELETING STUDENT CATEGORY
if (isset($_GET['deletestudentsCategoryid'])) {
    $id = $_GET['deletestudentsCategoryid'];
    $sql = "DELETE FROM `studentscategories` WHERE id = '$id'";
    $sql_query = mysqli_query($con, $sql);
    if ($sql_query) {
        $_SESSION['success'] = 'Category Deleted!';
        header('location:../view/adminModule/questionsTab.php');
    }
}

// ADDING NEW STUDENT CRITERIA
if (isset($_POST['addStudentsCriteria'])) {
    $feCategories = mysqli_real_escape_string($con, $_POST['studentsEvaluationCategories']);
    $feCriteria = mysqli_real_escape_string($con, $_POST['studentsEvaluationCriteria']);

    // Insert new criteria
    $sql = "INSERT INTO `studentscriteria` (studentsCategories, studentsCriteria) VALUES ('$feCategories', '$feCriteria')";
    $sql_query = mysqli_query($con, $sql);

    if ($sql_query) {
        // Get the last inserted ID
        $last_id = mysqli_insert_id($con);

        // Create unique column name by concatenating criteria and last ID
        $new_column = sanitizeColumnName($feCategories) . $last_id;

        // Check and create new column in peertopeerform
        $check_column = mysqli_query($con, "SHOW COLUMNS FROM `studentsform` LIKE '$new_column'");
        if (mysqli_num_rows($check_column) == 0) {
            $alter_sql = "ALTER TABLE `studentsform` ADD `$new_column` VARCHAR(255)";
            mysqli_query($con, $alter_sql);
        }

        // Set success message
        $_SESSION['success'] = 'Criteria added successfully!';
        header('location:../view/adminModule/questionsTab.php');
        exit();
    } else {
        echo "Error: " . mysqli_error($con);
    }
}

// DELETING STUDENT CRITERIA
if (isset($_GET["studentsdeleteid"])) {
    $id = $_GET['studentsdeleteid'];

    $result = mysqli_query($con, "SELECT studentsCategories FROM `studentscriteria` WHERE id = $id");
    $row = mysqli_fetch_assoc($result);
    $column_to_delete = sanitizeColumnName($row['studentsCategories']) . $id;

    $delete_sql = "DELETE FROM `studentscriteria` WHERE id = $id";
    if (mysqli_query($con, $delete_sql)) {
        $check_column = mysqli_query($con, "SHOW COLUMNS FROM `studentsform` LIKE '$column_to_delete'");

        if (mysqli_num_rows($check_column) > 0) {
            $drop_sql = "ALTER TABLE `studentsform` DROP COLUMN `$column_to_delete`";
            mysqli_query($con, $drop_sql);
        }
        $_SESSION['success'] = 'Criteria Deleted!';
        header('location:../view/adminModule/questionsTab.php');
        exit();
    } else {
        echo "Error: " . mysqli_error($con);
    }
}





// UPDATE QUERY FOR CLASSROOM, FACULTY, STUDENTS AND QUESTIONS
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // UPDATING FACULTY EVALUATION CRITERIA
    if (isset($_POST['criteriaId']) && isset($_POST['criteriaText'])) {
        $criteriaId = $_POST['criteriaId'];
        $criteriaText = $_POST['criteriaText'];

        $sqlUpdate = "UPDATE `facultycriteria` SET facultyCriteria = ? WHERE id = ?";
        $stmt = mysqli_prepare($con, $sqlUpdate);
        mysqli_stmt_bind_param($stmt, 'si', $criteriaText, $criteriaId);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = 'Criteria Updated Successfully!';
        } else {
            echo "Error updating criteria: " . mysqli_error($con);
        }
        mysqli_stmt_close($stmt);
    }

    // UPDATING CLASSROOM EVALUATION CRITERIA
    if (isset($_POST['classroomcriteriaId']) && isset($_POST['classroomcriteriaText'])) {
        $criteriaId = $_POST['classroomcriteriaId'];
        $criteriaText = $_POST['classroomcriteriaText'];

        $sqlUpdate = "UPDATE `classroomcriteria` SET classroomCriteria = ? WHERE id = ?";
        $stmt = mysqli_prepare($con, $sqlUpdate);
        mysqli_stmt_bind_param($stmt, 'si', $criteriaText, $criteriaId);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = 'Criteria Updated Successfully!';
        } else {
            echo "Error updating criteria: " . mysqli_error($con);
        }
        mysqli_stmt_close($stmt);
    }

    // UPDATING STUDENT EVALUATION CRITERIA
    if (isset($_POST['studentscriteriaId']) && isset($_POST['studentscriteriaText'])) {
        $id = $_POST['studentscriteriaId'];
        $criteriaText = $_POST['studentscriteriaText'];

        $stmt = $con->prepare("UPDATE studentscriteria SET studentsCriteria = ? WHERE id = ?");
        $stmt->bind_param("si", $criteriaText, $id);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Criteria Updated Successfully!';
        } else {
            echo "Error updating criteria: " . mysqli_error($con);
        }

        $stmt->close();
    } else {
        error_log("Invalid input received");
        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    }

    // UPDATING ADDITIONAL QUESTIONS FOR CLASSROOM EVALUATION
    if (isset($_POST['questionid']) && isset($_POST['question'])) {
        $questionid = $_POST['questionid'];
        $question = $_POST['question'];

        // Prepare and execute the update query
        $sqlUpdate = "UPDATE `classroomquestions` SET questions = ? WHERE id = ?";
        $stmt = mysqli_prepare($con, $sqlUpdate);
        mysqli_stmt_bind_param($stmt, 'si', $question, $questionid);

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = 'Additonal Questions Updated Successfully!';
        } else {
            echo "Error updating Additonal Questions: " . mysqli_error($con);
        }
        mysqli_stmt_close($stmt);
    }

    if (isset($_POST['linkId']) && isset($_POST['linkOne']) && isset($_POST['linkTwo']) && isset($_POST['linkThree'])) {
        $linkId = $_POST['linkId'];
        $linkOne = $_POST['linkOne'];  // Correct variable
        $linkTwo = $_POST['linkTwo'];  // Correct variable
        $linkThree = $_POST['linkThree'];  // Use linkThree for the third link

        $sqlUpdate = "UPDATE `subject` SET linkOne = ?, linkTwo = ?, linkThree = ? WHERE subject_id = ?";
        $stmt = mysqli_prepare($con, $sqlUpdate);
        mysqli_stmt_bind_param($stmt, 'sssi', $linkOne, $linkTwo, $linkThree, $linkId);  // Use correct types and order

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['success'] = 'Link Updated Successfully!';
        } else {
            echo "Error updating Link: " . mysqli_error($con);
        }
        mysqli_stmt_close($stmt);
    }




}





// PEER TO PEER SUBMITION OF FORM QUERY
if (isset($_POST['submitData'])) {

    $columns = [];
    $values = [];

    $toFaculty = isset($_POST['toFaculty']) ? trim($_POST['toFaculty']) : '';
    $fromFaculty = isset($_POST['fromFaculty']) ? trim($_POST['fromFaculty']) : '';
    $date = isset($_POST['date']) ? trim($_POST['date']) : '';
    $semester = isset($_POST['semester']) ? trim($_POST['semester']) : '';
    $academic_year = isset($_POST['academic_year']) ? trim($_POST['academic_year']) : '';
    $doneStatus = isset($_POST['doneStatus']) ? trim($_POST['doneStatus']) : '';
    $commentText = isset($_POST['commentText']) ? trim($_POST['commentText']) : '';

    $random_Id = isset($_POST['random_Id']) ? trim($_POST['random_Id']) : '';
    $faculty_Id = isset($_POST['faculty_Id']) ? trim($_POST['faculty_Id']) : '';
    $status = isset($_POST['doneStatus']) ? trim($_POST['doneStatus']) : '';

    foreach ($_POST as $key => $value) {
        // Filter out unnecessary keys
        if ($key !== 'submitData' && $key !== 'date' && $key !== 'fromFaculty' && $key !== 'toFaculty' && $key !== 'semester' && $key !== 'academic_year' && $key !== 'doneStatus' && $key !== 'random_Id' && $key !== 'faculty_Id' && $key !== 'commentText') { // Exclude these keys
            // Remove underscores and trim whitespace from key and value
            $cleanKey = str_replace('_', '', trim($key));
            $cleanValue = trim($value);

            // Check if cleanKey is not empty
            if (!empty($cleanKey)) {
                $columns[] = mysqli_real_escape_string($con, $cleanKey);
                $values[] = mysqli_real_escape_string($con, $cleanValue);
            }
        }
    }


    // Create a dynamic query if we have valid columns
    if (!empty($columns) && !empty($values)) {
        // Construct the SQL query, adding doneStatus directly
        $sql = "INSERT INTO `peertopeerform` (" . implode(", ", $columns) . ", toFaculty, fromFaculty, date, doneStatus, commentText, semester, academic_year,toFacultyID,fromFacultyID) VALUES ('" . implode("', '", $values) . "', '$toFaculty', '$fromFaculty', '$date', '$doneStatus', '$commentText', '$semester', '$academic_year','$random_Id','$faculty_Id')";

        // Execute the query
        if (mysqli_query($con, $sql)) {


            $random_Id = isset($_POST['random_Id']) ? trim($_POST['random_Id']) : '';
            $faculty_Id = isset($_POST['faculty_Id']) ? trim($_POST['faculty_Id']) : '';
            $status = isset($_POST['doneStatus']) ? trim($_POST['doneStatus']) : '';

            $sqlrandom = "UPDATE randomfaculty SET doneStatus = '$status' WHERE faculty_Id = '$faculty_Id' AND random_Id = '$random_Id'";

            if (mysqli_query($con, $sqlrandom)) {
                $_SESSION['status'] = "Evaluation Completed Successfully";
                $_SESSION['status-code'] = "success";
                header('location:../view/facultyModule/evaluate.php');
                exit;
            } else {
                $_SESSION['status'] = "Error evaluation: " . mysqli_error($con);
                $_SESSION['status-code'] = "error";

            }
            exit;
        } else {
            $_SESSION['status'] = "Error evaluation: " . mysqli_error($con);
            $_SESSION['status-code'] = "error";
        }
    } else {
        $_SESSION['status'] = "No Data Inserted.";
        $_SESSION['status-code'] = "warning";
    }
}

if (isset($_POST['classroomObservationSubmit'])) {
    $columns = [];
    $values = [];

    // Trim and sanitize individual inputs
    $toFacultyID = isset($_POST['toFacultyID']) ? trim($_POST['toFacultyID']) : '';
    $fromFacultyID = isset($_POST['fromFacultyID']) ? trim($_POST['fromFacultyID']) : '';
    $courseTitle = isset($_POST['courseTitle']) ? trim($_POST['courseTitle']) : '';
    $toFaculty = isset($_POST['toFaculty']) ? trim($_POST['toFaculty']) : '';
    $lengthOfCourse = isset($_POST['lengthOfCourse']) ? trim($_POST['lengthOfCourse']) : '';
    $lengthOfObservation = isset($_POST['lengthOfObservation']) ? trim($_POST['lengthOfObservation']) : '';
    $fromFaculty = isset($_POST['fromFaculty']) ? trim($_POST['fromFaculty']) : '';
    $date = isset($_POST['date']) ? trim($_POST['date']) : '';
    $subjectMatter = isset($_POST['subjectMatter']) ? trim($_POST['subjectMatter']) : '';
    $doneStatus = isset($_POST['doneStatus']) ? trim($_POST['doneStatus']) : '';

    // Exclude keys that are not part of the dynamic insert
    $excludeKeys = ['classroomObservationSubmit', 'courseTitle', 'toFaculty', 'lengthOfCourse', 'lengthOfObservation', 'fromFaculty', 'date', 'subjectMatter', 'doneStatus', 'toFacultyID', 'fromFacultyID'];

    // Filter out unwanted keys using array_diff_key
    foreach (array_diff_key($_POST, array_flip($excludeKeys)) as $key => $value) {
        $cleanKey = str_replace('_', '', trim($key));
        $cleanValue = trim($value);

        if (!empty($cleanKey)) {
            $columns[] = $cleanKey;
            $values[] = $cleanValue;
        }
    }

    // Ensure we have columns to insert
    if (!empty($columns) && !empty($values)) {
        // Add static columns for the form fields
        $columns[] = 'courseTitle';
        $columns[] = 'toFaculty';
        $columns[] = 'lengthOfCourse';
        $columns[] = 'lengthOfObservation';
        $columns[] = 'fromFaculty';
        $columns[] = 'date';
        $columns[] = 'subjectMatter';
        $columns[] = 'doneStatus';
        $columns[] = 'toFacultyID';
        $columns[] = 'fromFacultyID';

        $values[] = $courseTitle;
        $values[] = $toFaculty;
        $values[] = $lengthOfCourse;
        $values[] = $lengthOfObservation;
        $values[] = $fromFaculty;
        $values[] = $date;
        $values[] = $subjectMatter;
        $values[] = $doneStatus;
        $values[] = $toFacultyID;
        $values[] = $fromFacultyID;

        // Construct the placeholders for the prepared statement
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));

        // Prepare the SQL statement
        $sql = "INSERT INTO `classroomobservation` (" . implode(", ", $columns) . ") VALUES ($placeholders)";

        // Prepare and execute the statement
        if ($stmt = mysqli_prepare($con, $sql)) {
            // Bind parameters dynamically
            $types = str_repeat('s', count($values)); // Assuming all values are strings
            mysqli_stmt_bind_param($stmt, $types, ...$values);

            if (mysqli_stmt_execute($stmt)) {
                $_SESSION['status'] = "Classroom Observation Completed Successfully";
                $_SESSION['status-code'] = "success";
                header('location:../view/adminModule/classObser.php');
                exit;
            } else {
                $_SESSION['status'] = "Error Classroom Observation : " . mysqli_error($con);
                $_SESSION['status-code'] = "error";
            }

            mysqli_stmt_close($stmt);
        } else {
            $_SESSION['status'] = "Failed to prepare SQL statement: " . mysqli_error($con);
            $_SESSION['status-code'] = "error";
        }
    } else {
        $_SESSION['status'] = "No Data Inserted.";
        $_SESSION['status-code'] = "warning";
    }

}

if (isset($_POST['studentSubmit'])) {

    $columns = [];
    $values = [];

    $toFaculty = isset($_POST['toFaculty']) ? trim($_POST['toFaculty']) : '';
    $toFacultyID = isset($_POST['toFacultyID']) ? trim($_POST['toFacultyID']) : '';
    $fromStudents = isset($_POST['fromStudents']) ? trim($_POST['fromStudents']) : '';
    $fromStudentID = isset($_POST['fromStudentID']) ? trim($_POST['fromStudentID']) : '';
    $semester = isset($_POST['semester']) ? trim($_POST['semester']) : '';
    $academic_year = isset($_POST['academic_year']) ? trim($_POST['academic_year']) : '';
    $date = isset($_POST['date']) ? trim($_POST['date']) : '';
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';
    $enrolled = isset($_POST['enrolled']) ? trim($_POST['enrolled']) : '';
    $subject = isset($_POST['subject']) ? trim($_POST['subject']) : '';

    foreach ($_POST as $key => $value) {
        // Filter out unnecessary keys
        if ($key !== 'studentSubmit' && $key !== 'toFaculty' && $key !== 'toFacultyID' && $key !== 'fromStudents' && $key !== 'fromStudentID' && $key !== 'semester' && $key !== 'academic_year' && $key !== 'date' && $key !== 'comment' && $key !== 'enrolled' && $key !== 'subject') { // Exclude these keys
            // Remove underscores and trim whitespace from key and value
            $cleanKey = str_replace('_', '', trim($key));
            $cleanValue = trim($value);

            // Check if cleanKey is not empty
            if (!empty($cleanKey)) {
                $columns[] = mysqli_real_escape_string($con, $cleanKey);
                $values[] = mysqli_real_escape_string($con, $cleanValue);
            }
        }
    }


    // Create a dynamic query if we have valid columns
    if (!empty($columns) && !empty($values)) {
        // Construct the SQL query, adding doneStatus directly
        $sql = "INSERT INTO `studentsform` (" . implode(", ", $columns) . ", toFaculty, toFacultyID, fromStudents, fromStudentID, semester, academic_year, date,comment, subject) VALUES ('" . implode("', '", $values) . "', '$toFaculty', '$toFacultyID', '$fromStudents', '$fromStudentID', '$semester', '$academic_year', '$date','$comment','$subject')";

        // Execute the query
        if (mysqli_query($con, $sql)) {
            $SQLUpdate = "UPDATE `enrolled_subject` SET eval_status = '1' WHERE id ='$enrolled'";
            $SQLUpdate_query = mysqli_query($con, $SQLUpdate);

            if ($SQLUpdate_query) {
                $_SESSION['status'] = "Evaluation Completed Successfully";
                $_SESSION['status-code'] = "success";
                header('location:../view/student_view.php');
                exit;
            }




        } else {
            $_SESSION['status'] = "Error evaluation: " . mysqli_error($con);
            $_SESSION['status-code'] = "error";

        }
    } else {
        $_SESSION['status'] = "No Data Inserted.";
        $_SESSION['status-code'] = "warning";
    }
}


?>
<?php
session_start();
include "../model/dbconnection.php";
ini_set('display_errors', 1);
header('Content-Type: application/json');


if (isset($_POST['preferredSched'])) {
    $faculty_Id = $_POST['faculty_Id'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $dayOfWeek = $_POST['dayOfWeek'];
    $startTimePreferred = $_POST['startTimePreferred'];
    $endTimePreferred = $_POST['endTimePreferred'];
    $dayOfWeekTwo = $_POST['dayOfWeekTwo'];
    $startTimeSecondary = $_POST['startTimeSecondary'];
    $endTimeSecondary = $_POST['endTimeSecondary'];
    $courseClassroom = $_POST['courseClassroom'];

    $checkQuery = "SELECT * FROM preferredschedule WHERE faculty_Id = ?";
    $stmt = $con->prepare($checkQuery);
    $stmt->bind_param("s", $faculty_Id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $updateQuery = "UPDATE preferredschedule SET 
            dayOfWeek = ?, 
            startTimePreferred = ?, 
            endTimePreferred = ?, 
            dayOfWeekTwo = ?, 
            startTimeSecondary = ?, 
            endTimeSecondary = ?, 
            courseClassroom = ? 
            WHERE faculty_Id = ?";

        $updateStmt = $con->prepare($updateQuery);
        $updateStmt->bind_param("sssssssi", $dayOfWeek, $startTimePreferred, $endTimePreferred, $dayOfWeekTwo, $startTimeSecondary, $endTimeSecondary, $courseClassroom, $faculty_Id);

        if ($updateStmt->execute()) {
            $_SESSION['status'] = "Preferred schedule updated successfully.";
            $_SESSION['status-code'] = "success";
        } else {
            $_SESSION['status'] = "Error updating preferred schedule: " . $con->error;
            $_SESSION['status-code'] = "error";
        }
        $updateStmt->close();
    } else {
        $insertQuery = "INSERT INTO preferredschedule (faculty_Id,first_name, last_name, dayOfWeek, startTimePreferred, endTimePreferred, dayOfWeekTwo, startTimeSecondary, endTimeSecondary,courseClassroom) VALUES (?,?,?, ?, ?, ?, ?, ?, ?)";

        $insertStmt = $con->prepare($insertQuery);
        $insertStmt->bind_param("ssssssssss", $faculty_Id, $first_name, $last_name, $dayOfWeek, $startTimePreferred, $endTimePreferred, $dayOfWeekTwo, $startTimeSecondary, $endTimeSecondary, $courseClassroom);

        if ($insertStmt->execute()) {
            $_SESSION['status'] = "Preferred schedule inserted successfully.";
            $_SESSION['status-code'] = "success";
        } else {
            $_SESSION['status'] = "Error inserting preferred schedule: " . $con->error;
            $_SESSION['status-code'] = "error";
        }
        $insertStmt->close();
    }

    header('Location: ../view/facultyModule/classObser.php');
    exit();
}





// Insert a Faculty Member
if (isset($_POST['addFaculty'])) {
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $file_name = $_FILES['image']['name'];
        $file_temp = $_FILES['image']['tmp_name'];
        $file_size = $_FILES['image']['size'];
        $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
        $name = date("Ymd") . time();
        $img_des = '../public/picture/facultyMembers/' . $name . "." . $file_ext;

        $clearWhiteSpaces = array_map(function ($value) {
            return preg_replace('/\s+/', '', $value);
        }, $_POST);

        // Get cleaned first and last names
        $trimFirstName = isset($clearWhiteSpaces['first_name']) ? $clearWhiteSpaces['first_name'] : '';
        $trimLastName = isset($clearWhiteSpaces['last_name']) ? $clearWhiteSpaces['last_name'] : '';

        // Create the G Suite email address with a dot separator
        $gsuite = strtolower($trimFirstName . '.' . $trimLastName . '@g.batstate-u.edu.ph');
        $firstname = trim($_POST['first_name']);
        $lastname = trim($_POST['last_name']);
        $password = strtoupper($trimLastName);
        $usertype = trim('faculty');

        if ($file_size > 2 * 1024 * 1024) { // Limit to 2MB
            $_SESSION['status'] = "File size exceeds 2MB limit.";
            $_SESSION['status-code'] = "error";
            header('Location: ../view/adminModule/faculty.php');
            exit();
        }


        if (move_uploaded_file($file_temp, $img_des)) {
            $stmt = $con->prepare("INSERT INTO instructor (image, first_name, last_name, gsuite, password, usertype) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt) {

                $stmt->bind_param("ssssss", $img_des, $firstname, $lastname, $gsuite, $password, $usertype);

                if ($stmt->execute()) {
                    $_SESSION['status'] = "Faculty Member Added Successfully";
                    $_SESSION['status-code'] = "success";
                } else {
                    $_SESSION['status'] = "Something went wrong: " . $stmt->error;
                    $_SESSION['status-code'] = "error";
                }
                $stmt->close();
            } else {
                $_SESSION['status'] = "Failed to prepare the SQL statement.";
                $_SESSION['status-code'] = "error";
            }
        } else {
            $_SESSION['status'] = "Failed to move uploaded file";
            $_SESSION['status-code'] = "error";
        }
    } else {
        $_SESSION['status'] = "Image upload is required.";
        $_SESSION['status-code'] = "error";
    }

    header('Location: ../view/adminModule/faculty.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];

        if ($_POST['action'] === 'update') {
            $id = $_POST['faculty_Id'];
            $firstname = $_POST['first_name'];
            $lastname = $_POST['last_name'];

            if (isset($_FILES['new_image']) && $_FILES['new_image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../public/picture/facultyMembers/';

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $fileTmpPath = $_FILES['new_image']['tmp_name'];
                $fileName = basename($_FILES['new_image']['name']);
                $newFilePath = $uploadDir . uniqid() . '-' . $fileName;

                if (move_uploaded_file($fileTmpPath, $newFilePath)) {
                    $image = $newFilePath;
                } else {
                    echo json_encode(['success' => false, 'message' => 'Image upload failed.']);
                    exit();
                }
            } else {
                $stmt = $con->prepare("SELECT image FROM instructor WHERE faculty_Id = ?");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $result = $stmt->get_result();
                $row = $result->fetch_assoc();
                $image = $row['image'];
                $stmt->close();
            }

            $stmt = $con->prepare("UPDATE instructor SET first_name = ?, last_name = ?, image = ? WHERE faculty_Id = ?");
            $stmt->bind_param("sssi", $firstname, $lastname, $image, $id);

            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Update failed: ' . $stmt->error]);
            }
            $stmt->close();
            exit();
        } elseif ($action === 'delete') {
            $id = $_POST['faculty_Id'];

            $stmt = $con->prepare("DELETE FROM instructor WHERE faculty_Id = ?");
            $stmt->bind_param("i", $id);

            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Delete failed: ' . $stmt->error]);
            }
            $stmt->close();
            exit();
        }
    }
}

?>
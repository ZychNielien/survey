<?php
session_start();
include "../model/dbconnection.php";
ini_set('display_errors', 1);
header('Content-Type: application/json');



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



        // Check file size (optional)
        if ($file_size > 2 * 1024 * 1024) { // Limit to 2MB
            $_SESSION['status'] = "File size exceeds 2MB limit.";
            $_SESSION['status-code'] = "error";
            header('Location: ../view/adminModule/faculty.php');
            exit();
        }

        // Move the uploaded file
        if (move_uploaded_file($file_temp, $img_des)) {
            // Use prepared statements for security
            $stmt = $con->prepare("INSERT INTO instructor (image, first_name, last_name, gsuite, password, usertype) VALUES (?, ?, ?, ?, ?, ?)");
            if ($stmt) {
                // Hash the password before storing
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
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

        if ($action === 'update') {
            // Update functionality
            $id = $_POST['faculty_Id'];
            $firstname = $_POST['first_name'];
            $lastname = $_POST['last_name'];
            $image = $_POST['image'];

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
            // Delete functionality
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
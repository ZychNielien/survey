<?php

if (isset($_POST['fromSemester']) || isset($_POST['toSemester'])) {
    $selectedFrom = $_POST['fromSemester'] ?? '';
    $selectedTo = $_POST['toSemester'] ?? '';

    // Initialize arrays to store results
    $filteredSemesters = [];
    $averages = [];
    $message = '';

    // If both selections are empty, fetch all records
    if (empty($selectedFrom) && empty($selectedTo)) {
        $sql = "SELECT semester, academic_year, combined_average 
                FROM faculty_averages 
                WHERE faculty_Id = ? 
                ORDER BY academic_year, semester";

        $stmt = $con->prepare($sql);
        $stmt->bind_param("i", $facultyID);
    } else {
        // Split the semester and academic year if selections are made
        if (!empty($selectedFrom)) {
            list($fromSemester, $fromYear) = explode(' ', $selectedFrom);
        }

        if (!empty($selectedTo)) {
            list($toSemester, $toYear) = explode(' ', $selectedTo);
        }

        // Build the SQL query for the selected range
        $sql = "SELECT semester, academic_year, combined_average 
                FROM faculty_averages 
                WHERE faculty_Id = ? 
                AND (academic_year > ? OR (academic_year = ? AND semester >= ?)) 
                AND (academic_year < ? OR (academic_year = ? AND semester <= ?))
                ORDER BY academic_year, semester";

        // Prepare the statement
        $stmt = $con->prepare($sql);

        // Check if from and to years are set, and bind the parameters accordingly
        if (!empty($selectedFrom) && !empty($selectedTo)) {
            // Now we have 7 placeholders, so we need to bind 7 parameters
            $stmt->bind_param(
                "issssss",
                $facultyID,
                $fromYear,
                $fromYear,
                $fromSemester,
                $toYear,
                $toYear, // Ensure this matches the expected parameter
                $toSemester
            );
        } elseif (!empty($selectedFrom)) {
            $stmt->bind_param("iss", $facultyID, $fromYear, $fromSemester);
        } elseif (!empty($selectedTo)) {
            $stmt->bind_param("iss", $facultyID, $toYear, $toSemester);
        }
    }

    // Execute the prepared statement
    if ($stmt->execute()) {
        $result = $stmt->get_result();

        // Check if there are results
        if ($result->num_rows > 0) {
            // Fetch the data
            while ($row = $result->fetch_assoc()) {
                $filteredSemesters[] = $row['semester'] . ' ' . $row['academic_year'];
                $averages[] = $row['combined_average'];
            }
        } else {
            $message = "No data available for the selected semester range.";
        }
    } else {
        // Handle execution error
        $message = "Error executing query: " . $stmt->error;
    }

    $stmt->close();

    $response = array(
        'semesters' => $filteredSemesters,
        'averages' => $averages,
        'message' => $message
    );

    // Return JSON
    echo json_encode($response);
    exit; // Terminate the script here to avoid any other output
} else {
    // Default case: Fetch all records if no filters are applied
    $sql = "SELECT semester, academic_year, combined_average 
            FROM faculty_averages 
            WHERE faculty_Id = ? 
            ORDER BY academic_year, semester";

    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $facultyID);
    $stmt->execute();
    $result = $stmt->get_result();

    // Initialize arrays to store results
    $filteredSemesters = [];
    $averages = [];
    $message = '';

    // Check if there are results
    if ($result->num_rows > 0) {
        // Fetch the data
        while ($row = $result->fetch_assoc()) {
            $filteredSemesters[] = $row['semester'] . ' ' . $row['academic_year'];
            $averages[] = $row['combined_average'];
        }
    } else {
        $message = "No data available.";
    }

    $response = array(
        'semesters' => $filteredSemesters,
        'averages' => $averages,
        'message' => $message
    );

    // Return JSON
    echo json_encode($response);
}

?>
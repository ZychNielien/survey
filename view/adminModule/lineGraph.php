<?php
session_start();
include "../../model/dbconnection.php";

$userId = $_SESSION["userid"];
$userId = mysqli_real_escape_string($con, $userId);

// Fetch faculty information
$usersql = "SELECT * FROM instructor WHERE faculty_Id = '$userId'";
$usersql_query = mysqli_query($con, $usersql);
$userRow = mysqli_fetch_assoc($usersql_query);
$facultyID = $userRow['faculty_Id'];

$sqlSAY = "SELECT * FROM `academic_year_semester`";
$sqlSAY_query = mysqli_query($con, $sqlSAY);

// Check if the query was successful
if (!$sqlSAY_query) {
    die("Database query failed: " . mysqli_error($con));
}

// Fetch the data from the result set
$SAY = mysqli_fetch_assoc($sqlSAY_query);

if (!$SAY) {
    die("No academic year and semester found.");
}

// Set the current semester and academic year
$nowSemester = $SAY['semester'];
$nowAcademicYear = $SAY['academic_year'];
// Prepare semester and academic year from the context
$semester = $SAY['semester'];
$academicYear = $SAY['academic_year'];


// Function to sanitize column names
function sanitizeColumnName($name)
{
    return preg_replace('/[^a-zA-Z0-9_]/', '', trim($name));
}

// Function to get the final average rating for a selected faculty, subject, semester, and academic year
function getFinalAverageRating($userId, $selectedSubject, $con, $nowSemester, $nowAcademicYear)
{
    $SQLFaculty = "
        SELECT 
            sf.*, 
            sc.studentsCategories, 
            sc.id as criteriaID
        FROM `studentsform` sf
        JOIN `studentscriteria` sc ON sc.studentsCategories = sf.subject
        WHERE sf.toFacultyID = ? 
        AND sf.subject = ? 
        AND sf.semester = ? 
        AND sf.academic_year = ?";

    $stmt = $con->prepare($SQLFaculty);
    $stmt->bind_param("isss", $userId, $selectedSubject, $nowSemester, $nowAcademicYear);
    $stmt->execute();
    $result = $stmt->get_result();

    $totalAverage = 0;
    $categoryCount = 0;

    // Process results
    while ($row = $result->fetch_assoc()) {
        $criteriaID = $row['criteriaID'];
        $columnName = sanitizeColumnName($row['studentsCategories']);
        $finalColumnName = $columnName . $criteriaID;

        // Get the criteria rating
        $criteriaRating = $row[$finalColumnName] ?? null;

        // Compute average if rating is valid
        if ($criteriaRating >= 1 && $criteriaRating <= 5) {
            $totalAverage += $criteriaRating;
            $categoryCount++;
        }
    }

    // Return the final average rating
    return $categoryCount > 0 ? round($totalAverage / $categoryCount, 2) : null;
}

// Fetch subjects for the faculty
$sqlSubject = "
    SELECT i.faculty_Id, s.subject 
    FROM instructor i
    JOIN assigned_subject a ON i.faculty_Id = a.faculty_Id
    JOIN subject s ON a.subject_id = s.subject_id
    WHERE i.faculty_Id = ?";

$stmt = $con->prepare($sqlSubject);
$stmt->bind_param("i", $userRow['faculty_Id']);
$stmt->execute();
$result = $stmt->get_result();

$subjectsData = [];
$averagesData = [];

// Loop through subjects and calculate final averages
while ($subject = $result->fetch_assoc()) {
    $selectedSubject = $subject['subject'];

    // Calculate the final average rating for the current subject, semester, and academic year
    $finalAverageRating = getFinalAverageRating($userRow['faculty_Id'], $selectedSubject, $con, $semester, $academicYear);

    if (is_numeric($finalAverageRating)) {
        // Combine average with another defined average
        $combinedAverage = ($finalAverageRating + $average) / 2; // Ensure $average is defined elsewhere

        $subjectsData[] = $selectedSubject;
        $averagesData[] = round($combinedAverage, 2); // Collect average data for the graph

        // Insert or update combined average in the database
        $stmt = $con->prepare("SELECT * FROM faculty_averages WHERE semester = ? AND academic_year = ? AND faculty_Id = ? AND subject = ? LIMIT 1");
        $stmt->bind_param("ssis", $semester, $academicYear, $userRow['faculty_Id'], $selectedSubject);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            // Update existing record
            $updateSql = "UPDATE faculty_averages SET combined_average = ? WHERE id = ?";
            $updateStmt = $con->prepare($updateSql);
            $updateStmt->bind_param("di", $combinedAverage, $row['id']);
            $updateStmt->execute();
            $updateStmt->close();
        } else {
            // Insert new record
            $insertSql = "INSERT INTO faculty_averages (faculty_Id, subject, combined_average, semester, academic_year) 
                          VALUES (?, ?, ?, ?, ?)";
            $insertStmt = $con->prepare($insertSql);
            $insertStmt->bind_param("issss", $userRow['faculty_Id'], $selectedSubject, $combinedAverage, $semester, $academicYear);
            $insertStmt->execute();
            $insertStmt->close();
        }
    }
}

?>
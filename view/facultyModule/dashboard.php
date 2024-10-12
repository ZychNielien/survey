<?php
include "components/navBar.php";
require '../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
include "../../model/dbconnection.php";

$message = ''; // Variable to hold messages
$average = 0; // Initialize average

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['excel_file']) && $_FILES['excel_file']['error'] == 0) {
        $fileTmpPath = $_FILES['excel_file']['tmp_name'];
        $originalFileName = $_FILES['excel_file']['name'];
        $uniqueFileName = time() . '_' . $originalFileName;
        $destinationPath = '../../public/excelFiles/' . $uniqueFileName;

        // Get the file extension
        $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);

        // Validate file extension
        if ($fileExtension !== 'xls' && $fileExtension !== 'xlsx') {
            $message = "Invalid file type. Only Excel files (.xls, .xlsx) are allowed.";
        } else {
            // Check MIME type (optional)
            $mimeType = mime_content_type($fileTmpPath);
            if ($mimeType !== 'application/vnd.ms-excel' && $mimeType !== 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                $message = "Invalid file type. Only Excel files (.xls, .xlsx) are allowed.";
            } else {
                // Get the current file from the database for the user
                $userId = $userRow['faculty_Id']; // Get the user ID from the session
                $result = $con->query("SELECT file_name, id FROM vcaaexcel WHERE faculty_Id = '$userId' LIMIT 1");

                // Check if there is an existing entry in the database for the user
                if ($row = $result->fetch_assoc()) {
                    // Existing entry, update it
                    $oldFileName = $row['file_name'];
                    $recordId = $row['id']; // Get the ID for update
                    // Delete the old file if it exists
                    if (file_exists('../../public/excelFiles/' . $oldFileName)) {
                        unlink('../../public/excelFiles/' . $oldFileName);
                    }

                    if (move_uploaded_file($fileTmpPath, $destinationPath)) {
                        try {
                            $spreadsheet = IOFactory::load($destinationPath);
                            $sheet = $spreadsheet->getActiveSheet();
                            $cellValue = $sheet->getCell('D50')->getCalculatedValue();

                            if ($cellValue !== null && $cellValue !== '') {
                                // Update the data in the database
                                $stmt = $con->prepare("UPDATE vcaaexcel SET file_name = ?, cell_value = ? WHERE id = ?");
                                $stmt->bind_param("ssi", $uniqueFileName, $cellValue, $recordId); // Update entry using ID

                                if ($stmt->execute()) {
                                    $message = "Your VCAA has been successfully updated.";
                                } else {
                                    $message = "Error: " . $stmt->error;
                                }
                                $stmt->close();
                            } else {
                                $message = "Cell value is empty. Data not saved to the database.";
                            }

                        } catch (Exception $e) {
                            $message = "Error loading file: " . $e->getMessage();
                        }
                    } else {
                        $message = "Error saving the uploaded file.";
                    }
                } else {
                    // No existing entry for the user, so insert a new one
                    if (move_uploaded_file($fileTmpPath, $destinationPath)) {
                        try {
                            $spreadsheet = IOFactory::load($destinationPath);
                            $sheet = $spreadsheet->getActiveSheet();
                            $cellValue = $sheet->getCell('D50')->getCalculatedValue();

                            if ($cellValue !== null && $cellValue !== '') {
                                // Insert new data into the database
                                $stmt = $con->prepare("INSERT INTO vcaaexcel (file_name, cell_value, faculty_Id) VALUES (?, ?, ?)");
                                $stmt->bind_param("ssi", $uniqueFileName, $cellValue, $userId); // Insert new entry with user ID

                                if ($stmt->execute()) {
                                    $message = "Your VCAA has been successfully uploaded.";
                                } else {
                                    $message = "Error: " . $stmt->error;
                                }
                                $stmt->close();
                            } else {
                                $message = "Cell value is empty. Data not saved to the database.";
                            }

                        } catch (Exception $e) {
                            $message = "Error loading file: " . $e->getMessage();
                        }
                    } else {
                        $message = "Error saving the uploaded file.";
                    }
                }
            }
        }
    } else {
        $message = "Please upload a valid Excel file.";
    }
}

$userId = $userRow['faculty_Id'];
$result = $con->query("SELECT * FROM vcaaexcel WHERE faculty_Id = '$userId' LIMIT 1");
if ($row = $result->fetch_assoc()) {
    $average = (float) $row['cell_value'];
} else {
    $average = 0;
}

function sanitizeColumnName($name)
{
    return preg_replace('/[^a-zA-Z0-9_]/', '', trim($name));
}
// FACULTY ID NG NAKALOGIN SA WEBSITE
$facultyID = $userRow['faculty_Id'];

// Initialize these variables outside the block to accumulate totals.
$totalAverage = 0;
$categoryCount = 0;

// Loop through each category.
$sql = "SELECT * FROM studentscategories";
$sql_query = mysqli_query($con, $sql);

if (mysqli_num_rows($sql_query) > 0) {

    while ($categoriesRow = mysqli_fetch_assoc($sql_query)) {
        $categories = $categoriesRow['categories'];

        // Initialize these variables for each category.
        $totalRatings = [0, 0, 0, 0, 0]; // Array to count ratings 1 to 5.
        $ratingCount = 0;

        // Get all criteria for the current category.
        $sqlcriteria = "SELECT * FROM studentscriteria WHERE studentsCategories = '$categories'";
        $resultCriteria = mysqli_query($con, $sqlcriteria);

        if (mysqli_num_rows($resultCriteria) > 0) {

            // Fetch all forms submitted for the current faculty.
            $SQLFaculty = "SELECT * FROM studentsform WHERE toFacultyID = '$facultyID'";
            $SQLFaculty_query = mysqli_query($con, $SQLFaculty);

            // Loop through each form submission.
            while ($ratingRow = mysqli_fetch_assoc($SQLFaculty_query)) {

                // Loop through each criterion for the current category.
                while ($criteriaRow = mysqli_fetch_assoc($resultCriteria)) {
                    $columnName = sanitizeColumnName($criteriaRow['studentsCategories']);
                    $finalColumnName = $columnName . $criteriaRow['id'];

                    // Get the rating for this criterion in the current form.
                    $criteriaRating = $ratingRow[$finalColumnName] ?? null;

                    if ($criteriaRating !== null && $criteriaRating >= 1 && $criteriaRating <= 5) {
                        // Increment the count for the specific rating (1 to 5).
                        $totalRatings[$criteriaRating - 1]++;
                        $ratingCount++; // Total number of ratings.
                    }
                }

                // Reset the criteria result pointer for the next form.
                mysqli_data_seek($resultCriteria, 0);
            }

            // Now calculate the average rating for this category.
            if ($ratingCount > 0) {
                // Calculate the weighted sum for the average rating.
                $categoryTotal = 0;
                for ($i = 0; $i < 5; $i++) {
                    $categoryTotal += ($i + 1) * $totalRatings[$i];
                }
                $averageRating = $categoryTotal / $ratingCount; // Average based on the total number of ratings.

                $totalAverage += $averageRating; // Accumulate total average for overall calculation.
                $categoryCount++; // Increment the category count.
            }
        }
    }

    // Final overall average across all categories.
    $finalAverageRating = 0;
    if ($categoryCount > 0) {
        $finalAverageRating = round($totalAverage / $categoryCount, 2); // Average of all category averages.
    } else {
        $finalAverageRating = 'No ratings available';
    }

} else {
    $finalAverageRating = 'No Categories Found';
}

// Output the final average rating
$combinedAverage = ($finalAverageRating + $average) / 2;
$semester = $_POST['semester'] ?? 'Fall'; // Default value if not provided
$academic_year = $_POST['academic_year'] ?? '2025-2026';
// Check if a record already exists for the current semester, academic year, and faculty ID
$sql = "SELECT * FROM faculty_averages WHERE semester = ? AND academic_year = ? AND faculty_Id = ? LIMIT 1";
$stmt = $con->prepare($sql);
$stmt->bind_param("ssi", $semester, $academic_year, $facultyID);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    // Record exists, update it
    $existingId = $row['id']; // Get the existing record ID
    $updateSql = "UPDATE faculty_averages SET combined_average = ? WHERE id = ?";
    $updateStmt = $con->prepare($updateSql);
    $updateStmt->bind_param("di", $combinedAverage, $existingId); // Update with the new average
    $updateStmt->execute();
    $updateStmt->close();
} else {
    // Record doesn't exist, insert new record
    $insertSql = "INSERT INTO faculty_averages (faculty_Id, semester, academic_year, combined_average) VALUES (?, ?, ?, ?)";
    $insertStmt = $con->prepare($insertSql);
    $insertStmt->bind_param("issi", $facultyID, $semester, $academic_year, $combinedAverage);
    $insertStmt->execute();
    $insertStmt->close();
}

// Fetching and displaying averages
$filteredSemesters = [];
$averages = [];
$message = ""; // Initialize message variable

// Fetch combined averages based on the selected semesters
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

    $stmt->close();
}

// Handle message display (if needed in HTML)
if (!empty($message)) {
    echo "<div class='alert alert-warning'>$message</div>";
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/sweetalert.min.css.css">

</head>

<body>
    <section class="contentContainer">

        <div class="graphContainer d-flex justify-content-between align-items-center">
            <div class="currentRating d-flex flex-column justify-content-center  align-items-center w-100">
                <form action="" method="POST" enctype="multipart/form-data" class="mt-4">
                    <div class="file-drop-area">
                        <label class="choose-file-button" for="excel_file">Choose Excel File</label>
                        <span class="file-message">or drag and drop files here</span>
                        <input type="file" name="excel_file" accept=".xlsx, .xls" required
                            class="form-control file-input" id="excel_file">
                    </div>
                    <button type="submit" class="btn btn-primary my-2">Upload</button>
                </form>

                <div class="chart-container  justify-content-center">
                    <h3 class="text-center">Your VCAA Rating</h3>

                    <canvas id="averageChart" style="max-width: 250px;"></canvas>
                </div>
            </div>

            <div class="allRating d-flex flex-column justify-content-center align-items-center">
                <h3 class="text-center my-2">VCAA Ratings per Semester and Academic Year</h3>
                <form method="POST" action="" class="d-flex">
                    <div class="d-flex flex-row justify-content-between align-items-center px-5">
                        <div class="form-group me-2"> <!-- Use Bootstrap margin class for spacing -->
                            <label for="fromSemester">From Semester:</label>
                            <select name="fromSemester" id="fromSemester" class="form-control" required>
                                <option value="">Select a Semester</option>
                                <?php
                                // Fetch available semesters and academic years for the dropdown
                                $semestersResult = $con->query("SELECT DISTINCT semester, academic_year FROM faculty_averages WHERE faculty_Id = '$facultyID' ORDER BY academic_year, semester");
                                while ($semesterRow = $semestersResult->fetch_assoc()) {
                                    $semester = $semesterRow['semester'];
                                    $academicYear = $semesterRow['academic_year'];
                                    echo "<option value=\"$semester $academicYear\">$semester $academicYear</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <div class="form-group me-2">
                            <label for="toSemester">To Semester:</label>
                            <select name="toSemester" id="toSemester" class="form-control" required>
                                <option value="">Select a Semester</option>
                                <?php
                                // Fetch available semesters and academic years for the dropdown
                                $semestersResult = $con->query("SELECT DISTINCT semester, academic_year FROM faculty_averages WHERE faculty_Id = '$facultyID' ORDER BY academic_year, semester");
                                while ($semesterRow = $semestersResult->fetch_assoc()) {
                                    $semester = $semesterRow['semester'];
                                    $academicYear = $semesterRow['academic_year'];
                                    echo "<option value=\"$semester $academicYear\">$semester $academicYear</option>";
                                }
                                ?>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary">Filter</button>
                    </div>
                </form>




                <canvas id="lineChart"></canvas>


            </div>

        </div>


    </section>

    <!-- Include Chart.js and Chart.js Datalabels Plugin -->
    <script src="../../public/js/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>


    <script>
        // Pass the average from PHP to JavaScript
        var averageRating = <?php echo json_encode($combinedAverage); ?>; // Get average value from PHP

        // Ensure averageRating is a number
        if (typeof averageRating !== 'number' || isNaN(averageRating)) {
            averageRating = 0; // Default to 0 if not a valid number
        }


        var remainingRating = 5 - averageRating; // Assuming ratings are out of 5

        var ctx = document.getElementById('averageChart').getContext('2d');
        var averageChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Average Rating', 'Remaining Rating'],
                datasets: [{
                    data: [averageRating, remainingRating],
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.6)', // Average rating color
                        'rgba(255, 99, 132, 0.6)'  // Remaining rating color
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false // Completely hide the default legend
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ' + context.raw.toFixed(2); // Two decimal places
                                }
                                return label;
                            }
                        }
                    }
                }
            },


            plugins: [{
                afterDraw: function (chart) {
                    var ctx = chart.ctx;
                    var fontSize = 24; // Font size for the average value
                    ctx.font = fontSize + "px Arial";
                    ctx.textBaseline = "middle";
                    ctx.textAlign = "center"; // Center the text horizontally

                    // Get the position of the center of the doughnut chart
                    var centerX = (chart.chartArea.left + chart.chartArea.right) / 2;
                    var centerY = (chart.chartArea.top + chart.chartArea.bottom) / 2;

                    // Ensure averageRating is a number
                    if (typeof averageRating === 'number') {
                        // Draw the average value in the center
                        ctx.fillStyle = "black"; // Color of the average number
                        ctx.fillText(averageRating.toFixed(2), centerX, centerY); // Display the average value
                    } else {
                        console.error("averageRating is not a number:", averageRating); // Log error if not a number
                    }
                }
            }]

        });
    </script>

    <script>
        // Data fetched from PHP
        const semesters = <?php echo json_encode($filteredSemesters); ?>;
        const averages = <?php echo json_encode($averages); ?>;

        // Create the line chart if there is data
        if (semesters.length > 0) {
            const ctxLine = document.getElementById('lineChart').getContext('2d');
            const lineChart = new Chart(ctxLine, {
                type: 'line',
                data: {
                    labels: semesters, // X-axis labels
                    datasets: [{
                        label: 'Combined Average',
                        data: averages, // Y-axis data
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        fill: true,
                        tension: 0.1 // Curve the line
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false // Remove legends
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true, // Start y-axis from 0
                            title: {
                                display: true,
                                text: 'Combined Average'
                            }
                        },
                        x: {
                            title: {
                                display: true,
                                text: 'Semester'
                            }
                        }
                    }
                }
            });
        } else {
            document.getElementById('lineChart').style.display = 'none'; // Hide chart if no data
            alert('No data available for the selected semester range.');
        }

    </script>

    <script src="../../bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../public/js/sweetalert2@11.js"></script>
    <script>
        $(document).ready(function () {
            // Show SweetAlert if there is a message
            <?php if (!empty($message)): ?>
                    Swal.fire({
                        icon: <?php echo strpos($message, 'Error') === 0 || strpos($message, 'Invalid') === 0 ? "'error'" : "'success'"; ?>,
                        title: '<?= htmlspecialchars($message) ?>',
                        showConfirmButton: true,
                        timer: 5000
                    });
            <?php endif; ?>

            // Change file input handling
            $(document).on('change', '.file-input', function () {
                var filesCount = $(this)[0].files.length;
                var textbox = $(this).prev();

                if (filesCount === 1) {
                    var fileName = $(this).val().split('\\').pop();
                    textbox.text(fileName);
                } else {
                    textbox.text(filesCount + ' files selected');
                }
            });
        });
    </script>

</body>

</html>
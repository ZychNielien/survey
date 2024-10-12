<?php
include "components/navBar.php";
require '../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
include "../../model/dbconnection.php";

function sanitizeColumnName($name)
{
    return preg_replace('/[^a-zA-Z0-9_]/', '', trim($name));
}

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['excel_file'])) {
    if ($_FILES['excel_file']['error'] == 0) {
        $fileTmpPath = $_FILES['excel_file']['tmp_name'];
        $originalFileName = $_FILES['excel_file']['name'];
        $uniqueFileName = time() . '_' . $originalFileName;
        $destinationPath = '../../public/excelFiles/' . $uniqueFileName;

        // Validate file extension
        $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);
        if (!in_array($fileExtension, ['xls', 'xlsx'])) {
            $message = "Invalid file type. Only Excel files (.xls, .xlsx) are allowed.";
        } elseif (
            !in_array(mime_content_type($fileTmpPath), [
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
            ])
        ) {
            $message = "Invalid file type. Only Excel files (.xls, .xlsx) are allowed.";
        } else {
            // Get existing entry for the user
            $userId = $userRow['faculty_Id'];
            $result = $con->query("SELECT file_name, id FROM vcaaexcel WHERE faculty_Id = '$userId' LIMIT 1");

            // Upload new file and process data
            if ($row = $result->fetch_assoc()) {
                $oldFileName = $row['file_name'];
                $recordId = $row['id'];

                // Delete old file if it exists
                if (file_exists('../../public/excelFiles/' . $oldFileName)) {
                    unlink('../../public/excelFiles/' . $oldFileName);
                }
            }

            if (move_uploaded_file($fileTmpPath, $destinationPath)) {
                try {
                    $spreadsheet = IOFactory::load($destinationPath);
                    $sheet = $spreadsheet->getActiveSheet();
                    $cellValue = $sheet->getCell('D50')->getCalculatedValue();

                    if ($cellValue !== null && $cellValue !== '') {
                        // Update or insert data into the database
                        if (isset($recordId)) {
                            $stmt = $con->prepare("UPDATE vcaaexcel SET file_name = ?, cell_value = ? WHERE id = ?");
                            $stmt->bind_param("ssi", $uniqueFileName, $cellValue, $recordId);
                        } else {
                            $stmt = $con->prepare("INSERT INTO vcaaexcel (file_name, cell_value, faculty_Id) VALUES (?, ?, ?)");
                            $stmt->bind_param("ssi", $uniqueFileName, $cellValue, $userId);
                        }

                        if ($stmt->execute()) {
                            $message = "Your VCAA has been successfully " . (isset($recordId) ? "updated." : "uploaded.");
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
    } else {
        $message = "Please upload a valid Excel file.";
    }
}

// Retrieve the average from the database
$userId = $userRow['faculty_Id'];
$result = $con->query("SELECT cell_value FROM vcaaexcel WHERE faculty_Id = '$userId' LIMIT 1");

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $average = (float) $row['cell_value']; // Now $row is defined
} else {
    $average = 0; // Default value if no result
}


function getFinalAverageRating($facultyID, $selectedSubject, $con)
{
    $totalAverage = 0;
    $categoryCount = 0;

    $sql = "SELECT * FROM `studentscategories`";
    $sql_query = mysqli_query($con, $sql);

    while ($categoriesRow = mysqli_fetch_assoc($sql_query)) {
        $categories = $categoriesRow['categories'];
        $totalRatings = [0, 0, 0, 0, 0]; // Array to count ratings 1 to 5
        $ratingCount = 0;

        // Get criteria for the current category
        $sqlcriteria = "SELECT * FROM `studentscriteria` WHERE studentsCategories = '$categories'";
        $resultCriteria = mysqli_query($con, $sqlcriteria);

        $SQLFaculty = "SELECT * FROM `studentsform` WHERE toFacultyID = '$facultyID' AND subject = '$selectedSubject'";
        $SQLFaculty_query = mysqli_query($con, $SQLFaculty);

        while ($ratingRow = mysqli_fetch_assoc($SQLFaculty_query)) {
            while ($criteriaRow = mysqli_fetch_assoc($resultCriteria)) {
                $columnName = sanitizeColumnName($criteriaRow['studentsCategories']);
                $finalColumnName = $columnName . $criteriaRow['id'];
                $criteriaRating = $ratingRow[$finalColumnName] ?? null;

                if ($criteriaRating >= 1 && $criteriaRating <= 5) {
                    $totalRatings[$criteriaRating - 1]++;
                    $ratingCount++;
                }
            }
            mysqli_data_seek($resultCriteria, 0); // Reset the resultCriteria pointer
        }

        // Calculate average rating for the category
        if ($ratingCount > 0) {
            $averageRating = array_sum(array_map(function ($count, $index) {
                return ($index + 1) * $count;
            }, $totalRatings, array_keys($totalRatings))) / $ratingCount;

            $totalAverage += $averageRating;
            $categoryCount++;
        }
    }

    return $categoryCount > 0 ? round($totalAverage / $categoryCount, 2) : null;
}

$sqlSubject = "SELECT i.faculty_Id, s.subject 
                FROM instructor i
                JOIN assigned_subject a ON i.faculty_Id = a.faculty_Id
                JOIN subject s ON a.subject_id = s.subject_id
                WHERE i.faculty_Id = " . $userRow['faculty_Id'];

$sqlSubject_query = mysqli_query($con, $sqlSubject);
$subjectsData = [];
$averagesData = [];

while ($subject = mysqli_fetch_assoc($sqlSubject_query)) {
    $selectedSubject = $subject['subject'];
    $finalAverageRating = getFinalAverageRating($userRow['faculty_Id'], $selectedSubject, $con);

    if (is_numeric($finalAverageRating)) {
        // Combined average calculation
        $combinedAverage = ($finalAverageRating + $average) / 2; // Ensure $average is defined

        $subjectsData[] = $selectedSubject;
        $averagesData[] = round($combinedAverage, 2); // Collect average data for the graph

        // Fetch the current semester and academic year
        $sqlSAY = "SELECT * FROM `academic_year_semester`";
        $sqlSAY_query = mysqli_query($con, $sqlSAY);
        if ($SQY = mysqli_fetch_assoc($sqlSAY_query)) {
            $semester = $SQY['semester'];
            $academic_year = $SQY['academic_year'];

            // Insert or update the combined average in the database
            $stmt = $con->prepare("SELECT * FROM faculty_averages WHERE semester = ? AND academic_year = ? AND faculty_Id = ? AND subject = ? LIMIT 1");
            $stmt->bind_param("ssis", $semester, $academic_year, $userRow['faculty_Id'], $selectedSubject);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                // Update existing record
                $updateSql = "UPDATE faculty_averages SET combined_average = ? WHERE id = ?";
                $updateStmt = $con->prepare($updateSql);
                $updateStmt->bind_param("di", $combinedAverage, $row['id']);
                if (!$updateStmt->execute()) {
                    // Handle error
                }
                $updateStmt->close();
            } else {
                // Insert new record
                $insertSql = "INSERT INTO faculty_averages (faculty_Id, subject, combined_average, semester, academic_year) VALUES (?, ?, ?, ?, ?)";
                $insertStmt = $con->prepare($insertSql);
                $insertStmt->bind_param("issss", $userRow['faculty_Id'], $selectedSubject, $combinedAverage, $semester, $academic_year);
                if (!$insertStmt->execute()) {
                    // Handle error
                }
                $insertStmt->close();
            }
            $stmt->close();
        }
    }
}

// Prepare data for the chart
$faculty_Id = mysqli_real_escape_string($con, $_SESSION["userid"]);
$sql = "SELECT subject, combined_average, semester, academic_year FROM faculty_averages WHERE faculty_Id = '$faculty_Id' ORDER BY academic_year, semester";
$result = mysqli_query($con, $sql);

$semesters = []; // To hold unique semester-academic year combinations
$subjectData = []; // To hold the combined averages for each subject per semester

while ($row = mysqli_fetch_assoc($result)) {
    $subject = $row['subject'];
    $semesterYear = $row['semester'] . ' ' . $row['academic_year'];  // Combined semester and academic year

    // Ensure unique semesterYear
    if (!in_array($semesterYear, $semesters)) {
        $semesters[] = $semesterYear; // Add to semesters if not present
    }

    if (!isset($subjectData[$subject])) {
        $subjectData[$subject] = array_fill(0, count($semesters), null);  // Array with nulls for each semester
    }

    foreach ($subjectData as $subj => $data) {
        if (count($data) < count($semesters)) {
            $subjectData[$subj] = array_pad($subjectData[$subj], count($semesters), null);
        }
    }

    // Get the index of the current semester
    $index = array_search($semesterYear, $semesters);
    if ($index !== false) {
        $subjectData[$subject][$index] = (float) $row['combined_average'];
    }
}

$subjectsJson = json_encode(array_keys($subjectData));
$subjectDataJson = json_encode($subjectData);
$semestersJson = json_encode($semesters);

$con->close(); // Close the database connection
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
    <style>
        .graphContainer {
            width: 100%;
            /* 100% na lapad ng container */
            display: flex;
            /* Gawing flex container */
            align-items: stretch;
        }

        .currentRating {
            max-width: 400px;
            /* Maximum width para sa div 1 */
            flex-shrink: 0;
        }

        .allRating {
            flex-grow: 1;
            padding: 0 50px;
        }

        .file-drop-area {
            position: relative;
            display: flex;
            align-items: center;
            width: 300px;
            max-width: 100%;
            padding: 10px;
            border: 1px dashed black;
            border-radius: 3px;
            transition: 0.2s;
        }

        .choose-file-button {
            flex-shrink: 0;
            background-color: rgba(255, 255, 255, 0.04);
            border: 1px solid black;
            border-radius: 3px;
            padding: 8px 5px;
            margin-right: 10px;
            font-size: 12px;
            text-transform: uppercase;
        }

        .file-message {
            font-size: small;
            font-weight: 300;
            line-height: 1.4;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .file-input {
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 100%;
            cursor: pointer;
            opacity: 0;
        }
    </style>

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

                <canvas id="lineChart" width="400" height="200"></canvas>


            </div>

        </div>


    </section>

    <!-- Include Chart.js and Chart.js Datalabels Plugin -->
    <script src="../../public/js/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>


    <script>
        // Pass the average from PHP to JavaScript
        var averageRating = <?php echo json_encode($average); ?>; // Get average value from PHP

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
        // JSON encoded data from PHP
        const subjects = <?php echo $subjectsJson; ?>;  // List of subjects
        const subjectData = <?php echo $subjectDataJson; ?>;  // Averages data per subject
        const semesters = <?php echo $semestersJson; ?>;  // List of semester-year combinations

        // Define colors for the subjects dynamically
        const subjectColors = {};
        subjects.forEach((subject, index) => {
            subjectColors[subject] = `hsl(${index * 360 / subjects.length}, 100%, 50%)`;  // Generate distinct colors
        });

        const datasets = subjects.map(subject => {
            let data = subjectData[subject];  // Access data for the current subject

            // Ensure data is an array; if not, initialize it as an empty array
            if (!Array.isArray(data)) {
                data = Array(semesters.length).fill(0);  // Fallback to an array of zeros for each semester
            }

            // Replace nulls with 0 or handle them as desired
            const cleanData = data.map(value => value !== null ? value : 0);

            return {
                label: subject,  // Use the current subject as the label
                data: cleanData,  // Use the clean data
                borderColor: subjectColors[subject] || 'rgba(0, 0, 0, 0.5)',  // Set a color for each subject
                fill: false,
                tension: 0.1
            };
        });

        // Create the line chart
        const ctxLine = document.getElementById('lineChart').getContext('2d');
        const lineChart = new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: semesters,  // X-axis labels (semesters with academic year)
                datasets: datasets  // Dataset for each subject
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 5  // Assuming the combined average is out of 5
                    }
                }
            }
        });

    </script>

    <script src="../../bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../public/js/sweetalert2@11.js"></script>
    <script>
        $(document).ready(function () {

            <?php if (isset($_SESSION['status'])): ?>
                Swal.fire({
                    title: '<?php echo $_SESSION['status']; ?>',
                    icon: '<?php echo ($_SESSION['status-code'] == 'success' ? 'success' : 'error'); ?>',
                    confirmButtonText: 'OK'
                });
                <?php unset($_SESSION['status']); ?>
            <?php endif; ?>


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
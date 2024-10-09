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

// Fetch all data from the database for the user and display it
$userId = $userRow['faculty_Id']; // Get the user ID from the session
$result = $con->query("SELECT * FROM vcaaexcel WHERE faculty_Id = '$userId' LIMIT 1"); // Limit to 1 record for user

// Retrieve the average value from the database
// Retrieve the average value from the database
if ($row = $result->fetch_assoc()) {
    $average = (float) $row['cell_value']; // Cast to float to ensure it's a number
} else {
    $average = 0; // Set a default value if no record is found
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
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        h2 {
            margin: 50px 0;
        }

        .file-drop-area {
            position: relative;
            display: flex;
            align-items: center;
            width: 450px;
            max-width: 100%;
            padding: 25px;
            border: 1px dashed black;
            border-radius: 3px;
            transition: 0.2s;
        }

        .choose-file-button {
            flex-shrink: 0;
            background-color: rgba(255, 255, 255, 0.04);
            border: 1px solid black;
            border-radius: 3px;
            padding: 8px 15px;
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

        canvas {
            max-width: 350px;
            max-height: 350px;
            width: 350px;
        }
    </style>
</head>

<body>
    <section class="contentContainer">
        <div class="container mt-5">
            <h2 class="text-center">Upload Excel File</h2>
            <form action="" method="POST" enctype="multipart/form-data" class="mt-4">
                <div class="file-drop-area">
                    <label class="choose-file-button" for="excel_file">Choose Excel File</label>
                    <span class="file-message">or drag and drop files here</span>
                    <input type="file" name="excel_file" accept=".xlsx, .xls" required class="form-control file-input"
                        id="excel_file">
                </div>
                <button type="submit" class="btn btn-primary">Upload</button>
            </form>

            <div class="chart-container">
                <canvas id="averageChart" style="max-width: 350px;"></canvas>
            </div>
        </div>
    </section>

    <!-- Include Chart.js and Chart.js Datalabels Plugin -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                        position: 'top',
                        labels: {
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
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

    <script src="../../bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    <script src="../../bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../public/js/script.js"></script>
</body>

</html>
<?php
// Start output buffering
ob_start();

include "components/navBar.php";
require '../../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;
include "../../model/dbconnection.php";

function sanitizeColumnName($name)
{
    return preg_replace('/[^a-zA-Z0-9_]/', '', trim($name));
}

$sqlSAY = "SELECT * FROM `academic_year_semester`";
$sqlSAY_query = mysqli_query($con, $sqlSAY);
$SAY = mysqli_fetch_assoc($sqlSAY_query);

$nowSemester = $SAY['semester'];
$nowAcademicYear = $SAY['academic_year'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['excel_file'])) {
    if ($_FILES['excel_file']['error'] == 0) {
        $fileTmpPath = $_FILES['excel_file']['tmp_name'];
        $originalFileName = $_FILES['excel_file']['name'];
        $uniqueFileName = time() . '_' . $originalFileName;
        $destinationPath = '../../public/excelFiles/' . $uniqueFileName;

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
            $userId = $_POST['teacher_id'];
            $result = $con->query("SELECT file_name, id FROM vcaaexcel WHERE faculty_Id = '$userId' AND semester = '$nowSemester' AND academic_year = '$nowAcademicYear' LIMIT 1");

            if ($row = $result->fetch_assoc()) {
                $oldFileName = $row['file_name'];
                $recordId = $row['id'];

                if (file_exists('../../public/excelFiles/' . $oldFileName)) {
                    unlink('../../public/excelFiles/' . $oldFileName);
                }
            }

            if (move_uploaded_file($fileTmpPath, $destinationPath)) {
                try {
                    // Load the spreadsheet
                    $spreadsheet = IOFactory::load($destinationPath);
                    $sheet = $spreadsheet->getActiveSheet();

                    // Read the necessary cell values
                    $cellValue = $sheet->getCell('D50')->getCalculatedValue();
                    $categoryOne = $sheet->getCell('D51')->getCalculatedValue();
                    $categoryTwo = $sheet->getCell('D52')->getCalculatedValue();
                    $categoryThree = $sheet->getCell('D53')->getCalculatedValue();
                    $categoryFour = $sheet->getCell('D54')->getCalculatedValue();
                    $categoryFive = $sheet->getCell('D55')->getCalculatedValue();

                    if ($cellValue !== null && $cellValue !== '') {
                        if (isset($recordId)) {
                            // Update the existing record
                            $stmt = $con->prepare("UPDATE vcaaexcel SET file_name = ?, cell_value = ?, categoryOne = ?, categoryTwo = ?, categoryThree = ?, categoryFour = ?, categoryFive = ? WHERE id = ? AND semester = ? AND academic_year = ?");
                            $stmt->bind_param("sssssssiis", $uniqueFileName, $cellValue, $categoryOne, $categoryTwo, $categoryThree, $categoryFour, $categoryFive, $recordId, $nowSemester, $nowAcademicYear);
                        } else {
                            // Insert a new record
                            $stmt = $con->prepare("INSERT INTO vcaaexcel (file_name, cell_value, faculty_Id, semester, academic_year, categoryOne, categoryTwo, categoryThree, categoryFour, categoryFive) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                            $stmt->bind_param("ssisssssss", $uniqueFileName, $cellValue, $userId, $nowSemester, $nowAcademicYear, $categoryOne, $categoryTwo, $categoryThree, $categoryFour, $categoryFive);
                        }

                        // Execute the statement
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

// End output buffering and send the buffered output

$average = isset($average) ? $average : 0;
ob_end_flush();
?>

<head>
    <title>Evaluation Result</title>
    <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../public/css/animate.min.css">
    <link rel="stylesheet" href="../../fontawesome/css/all.min.css">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/sweetalert.min.css">

    <!-- SCRIPT -->
    <script src="../../public/js/sweetalert2@11.js"></script>
    <script src="../../public/js/jquery-3.7.1.min.js"></script>
    <script src="../../bootstrap/js/bootstrap.min.js"></script>
</head>

<style>
    .file-drop-area {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
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

    .modal-body {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 400px;
    }

    .chart-container {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 0;
        margin: 0;
        height: 100%;
    }
</style>
<section class="contentContainer">
    <div class="container">
        <table class="table table-striped table-bordered text-center align-middle w-100">
            <thead>
                <tr class="bg-danger">
                    <th>Full Name</th>
                    <th>VCAA Rating</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php

                $sql = "SELECT * FROM `instructor`";
                $sql_query = mysqli_query($con, $sql);
                if ($sql_query) {
                    while ($vcaaRow = mysqli_fetch_assoc($sql_query)) {
                        $facultyID = $vcaaRow['faculty_Id'];

                        $getVCAA = "SELECT * FROM `vcaaexcel` WHERE faculty_Id = '$facultyID'";
                        $getVCAA_query = mysqli_query($con, $getVCAA);
                        $vcaaValue = mysqli_fetch_assoc($getVCAA_query);

                        $displayRating = isset($vcaaValue['cell_value']) && !is_null($vcaaValue['cell_value'])
                            ? $vcaaValue['cell_value']
                            : 'No VCAA rating';
                        ?>


                        <tr>
                            <td><?php echo $vcaaRow['first_name'] . ' ' . $vcaaRow['last_name'] ?></td>
                            <td><?php echo $displayRating; ?></td>
                            <td> <button class="btn btn-primary edit-vcaa-btn" data-bs-toggle="modal"
                                    data-bs-target="#exampleModal" data-id="<?php echo $vcaaRow['faculty_Id']; ?>"
                                    data-average="<?php echo isset($vcaaValue['cell_value']) ? $vcaaValue['cell_value'] : 0; ?>"
                                    data-name="<?php echo $vcaaRow['first_name'] . ' ' . $vcaaRow['last_name']; ?>">Update</button>
                            </td>
                        </tr>

                        <?php


                    }
                }

                ?>
            </tbody>
        </table>



    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                    <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="currentRating d-flex flex-column justify-content-center align-items-center w-100">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="file-drop-area">
                                <label class="choose-file-button" for="excel_file">Choose Excel File</label>
                                <span class="file-message">or drag and drop files here</span>
                                <input type="file" name="excel_file" accept=".xlsx, .xls" required
                                    class="form-control file-input" id="excel_file">
                            </div>
                            <input type="hidden" name="teacher_id" id="teacherId" readonly>
                            <input type="hidden" name="teachername" id="teachername" readonly>

                            <h3 class="text-center mt-3">VCAA Rating</h3>

                            <!-- Modal body or container for the chart -->
                            <div class="chart-container">
                                <canvas id="averageChart" style="max-width: 300px"></canvas>
                            </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary my-2">Upload</button>
                </div>
            </div>
        </div>
        </form>
    </div>

</section>


<script src="../../public/js/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

<script>
    let averageChart;
    $(document).ready(function () {
        $('.edit-vcaa-btn').on('click', function () {
            var teacherId = $(this).data('id');
            var teacherName = $(this).data('name');
            var averageRating = parseFloat($(this).data('average'));

            if (isNaN(averageRating)) {
                averageRating = 0;
            }

            var remainingRating = 5 - averageRating;

            $('#teacherId').val(teacherId);
            $('#teachername').val(teacherName);
            var averageRating = parseFloat($(this).data('average'));
            $('#exampleModal').data('averageRating', averageRating);
        });

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

    $('#exampleModal').on('shown.bs.modal', function () {
        var averageRating = $(this).data('averageRating');
        var remainingRating = 5 - averageRating;

        if (averageChart) {
            averageChart.destroy();
        }

        var ctx = document.getElementById('averageChart').getContext('2d');

        averageChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Average Rating', 'Remaining Rating'],
                datasets: [{
                    data: [averageRating, remainingRating],
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(255, 99, 132, 0.6)'
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
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ' + context.raw.toFixed(2);
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
                    var fontSize = 24;
                    ctx.font = fontSize + "px Arial";
                    ctx.textBaseline = "middle";
                    ctx.textAlign = "center";

                    var centerX = (chart.chartArea.left + chart.chartArea.right) / 2;
                    var centerY = (chart.chartArea.top + chart.chartArea.bottom) / 2;

                    ctx.fillStyle = "black";
                    ctx.fillText(averageRating.toFixed(2), centerX, centerY);
                }
            }]
        });
    });

    $('#exampleModal').on('hidden.bs.modal', function () {
        if (averageChart) {
            averageChart.destroy();
            averageChart = null;
        }
    });


</script>
<?php
include "components/navBar.php";

include "../../model/dbconnection.php";


$sqlSAY = "SELECT * FROM `academic_year_semester`";
$sqlSAY_query = mysqli_query($con, $sqlSAY);
$SAY = mysqli_fetch_assoc($sqlSAY_query);

$nowSemester = $SAY['semester'];
$nowAcademicYear = $SAY['academic_year'];

$userId = $userRow['faculty_Id'];
$result = $con->query("SELECT cell_value FROM vcaaexcel WHERE faculty_Id = '$userId' AND semester = '$nowSemester' AND academic_year = '$nowAcademicYear' LIMIT 1");

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $average = (float) $row['cell_value'];
} else {
    $average = 0;
}

function getFinalAverageRating($facultyID, $semester, $academic_year, $selectedSubject, $con)
{
    $totalAverage = 0;
    $categoryCount = 0;

    $sql = "SELECT * FROM `studentscategories`";
    $sql_query = mysqli_query($con, $sql);

    while ($categoriesRow = mysqli_fetch_assoc($sql_query)) {
        $categories = $categoriesRow['categories'];
        $totalRatings = [0, 0, 0, 0, 0];
        $ratingCount = 0;

        $sqlcriteria = "SELECT * FROM `studentscriteria` WHERE studentsCategories = '$categories'";
        $resultCriteria = mysqli_query($con, $sqlcriteria);

        $SQLFaculty = "SELECT * FROM `studentsform` WHERE toFacultyID = '$facultyID' AND semester = '$semester' AND academic_year = '$academic_year' AND subject = '$selectedSubject' ";
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
            mysqli_data_seek($resultCriteria, 0);
        }

        if ($ratingCount > 0) {
            $averageRating = array_sum(array_map(function ($count, $index) {
                return ($index + 1) * $count;
            }, $totalRatings, array_keys($totalRatings))) / $ratingCount;

            $totalAverage += $averageRating;
            $categoryCount++;
        }
    }

    return $categoryCount > 0 ? round($totalAverage / $categoryCount, 2) : 0;
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

    $sqlSAY = "SELECT * FROM `academic_year_semester`";
    $sqlSAY_query = mysqli_query($con, $sqlSAY);
    if ($SQY = mysqli_fetch_assoc($sqlSAY_query)) {
        $semester = $SQY['semester'];
        $academic_year = $SQY['academic_year'];
        $finalAverageRating = getFinalAverageRating($userRow['faculty_Id'], $semester, $academic_year, $selectedSubject, $con);

        if (is_numeric($finalAverageRating)) {
            $finalAverageRating = $finalAverageRating ?? 0;
            $average = $average ?? 0;

            $combinedAverage = ($finalAverageRating + $average) / 2;

            $stmt = $con->prepare("SELECT * FROM faculty_averages WHERE semester = ? AND academic_year = ? AND faculty_Id = ? AND subject = ? LIMIT 1");
            $stmt->bind_param("ssis", $semester, $academic_year, $userRow['faculty_Id'], $selectedSubject);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($row = $result->fetch_assoc()) {
                $updateSql = "UPDATE faculty_averages SET combined_average = ? WHERE id = ?";
                $updateStmt = $con->prepare($updateSql);
                $updateStmt->bind_param("di", $combinedAverage, $row['id']);
                $updateStmt->execute();
                $updateStmt->close();
            } else {
                $insertSql = "INSERT INTO faculty_averages (faculty_Id, subject, combined_average, semester, academic_year) VALUES (?, ?, ?, ?, ?)";
                $insertStmt = $con->prepare($insertSql);
                $insertStmt->bind_param("issss", $userRow['faculty_Id'], $selectedSubject, $combinedAverage, $semester, $academic_year);
                $insertStmt->execute();
                $insertStmt->close();
            }

            $subjectsData[] = $selectedSubject;
            $averagesData[] = $combinedAverage;
        }
    }
}

$faculty_Id = mysqli_real_escape_string($con, $_SESSION["userid"]);
$sql = "SELECT subject, combined_average, semester, academic_year FROM faculty_averages WHERE faculty_Id = '$faculty_Id' ORDER BY academic_year, semester";
$result = mysqli_query($con, $sql);

$semesters = [];
$subjectData = [];

while ($row = mysqli_fetch_assoc($result)) {
    $subject = $row['subject'];
    $semesterYear = $row['semester'] . ' ' . $row['academic_year'];

    if (!in_array($semesterYear, $semesters)) {
        $semesters[] = $semesterYear;
    }

    if (!isset($subjectData[$subject])) {
        $subjectData[$subject] = array_fill(0, count($semesters), null);
    }

    foreach ($subjectData as $subj => $data) {
        if (count($data) < count($semesters)) {
            $subjectData[$subj] = array_pad($subjectData[$subj], count($semesters), null);
        }
    }

    $index = array_search($semesterYear, $semesters);
    if ($index !== false) {
        $subjectData[$subject][$index] = (float) $row['combined_average'];
    }
}

$subjectsJson = json_encode(array_keys($subjectData));
$subjectDataJson = json_encode($subjectData);
$semestersJson = json_encode($semesters);

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
            display: flex;
            align-items: stretch;
        }

        .currentRating {
            max-width: 400px;
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

                <div class="chart-container  justify-content-center">

                    <div class="d-flex justify-content-center my-3">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#vcaaResults">
                            VCAA Results
                        </button>
                    </div>

                    <h3 class="text-center">Your VCAA Rating</h3>

                    <canvas id="averageChart" style="max-width: 250px;"></canvas>

                </div>

            </div>

            <div class="allRating d-flex flex-column justify-content-center align-items-center">

                <h3 class="text-center my-2">Evaluation of VCAA Ratings Across Semesters and Academic Years</h3>

                <div class="container my-3 d-flex justify-content-evenly">

                    <div class="form-group">

                        <label for="startSemesterFilter">Select Start Semester:</label>
                        <select id="startSemesterFilter" class="form-control">
                            <option value="">Select Start Semester</option>
                            <?php foreach ($semesters as $semester): ?>
                                <option value="<?php echo $semester; ?>"><?php echo $semester; ?></option>
                            <?php endforeach; ?>
                        </select>

                    </div>

                    <div class="form-group">

                        <label for="endSemesterFilter">Select End Semester:</label>
                        <select id="endSemesterFilter" class="form-control">
                            <option value="">Select End Semester</option>
                        </select>

                    </div>

                    <div class="form-group">

                        <label for="subjectFilter">Select Subject:</label>
                        <select id="subjectFilter" class="form-control">
                            <option value="all">All Subjects</option>
                            <?php foreach (array_keys($subjectData) as $subject): ?>
                                <option value="<?php echo $subject; ?>"><?php echo $subject; ?></option>
                            <?php endforeach; ?>
                        </select>

                    </div>

                    <div class="form-group">

                        <button class="btn btn-success" id="printBtn">Print</button>

                    </div>

                </div>

                <canvas id="lineChart"></canvas>

            </div>



        </div>

    </section>

    <div class="modal fade" id="vcaaResults" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="vcaaResultsLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title " id="vcaaResultsLabel">VCAA Results</h5>
                    <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php

                    function sanitizeColumnName($name)
                    {
                        return preg_replace('/[^a-zA-Z0-9_]/', '', trim($name));
                    }

                    $FacultyID = $userRow['faculty_Id'];
                    $sqlSAY = "SELECT DISTINCT  sf.semester, sf.academic_year 
        FROM vcaaexcel sf
        JOIN instructor i ON sf.faculty_Id = i.faculty_Id
        WHERE sf.faculty_Id = '$FacultyID'";

                    $sqlSAY_query = mysqli_query($con, $sqlSAY);

                    $semesters = [];
                    $academicYears = [];

                    while ($academicYearSemester = mysqli_fetch_assoc($sqlSAY_query)) {
                        $semesters[] = $academicYearSemester['semester'];
                        $academicYears[] = $academicYearSemester['academic_year'];
                    }

                    $selectedSemester = isset($_POST['semester']) ? $_POST['semester'] : '';
                    $selectedAcademicYear = isset($_POST['academic_year']) ? $_POST['academic_year'] : '';

                    ?>

                    <!-- FILTER FOR SEMESTER AND ACADEMIC YEAR -->
                    <form method="POST" action="" class="mb-4 d-flex justify-content-evenly text-center">
                        <div class="mb-3">
                            <label for="academic_year" class="form-label">Academic Year:</label>
                            <select id="academic_year" name="academic_year" class="form-select">
                                <option value="">Select Academic Year</option>
                                <?php foreach (array_unique($academicYears) as $year): ?>
                                    <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="semester" class="form-label">Semester:</label>
                            <select id="semester" name="semester" class="form-select">
                                <option value="">Select Semester</option>
                                <?php foreach (array_unique($semesters) as $semester): ?>
                                    <option value="<?php echo $semester; ?>"><?php echo $semester; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <button type="button" class="btn btn-success" onclick="printPartOfPage('result')">Print
                                Content</button>
                        </div>
                    </form>

                    <div class="vcaaRecommendation mt-2">
                        <div id="result"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../../public/js/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>

    <script>
        function printPartOfPage(elementId) {
            var printContent = document.getElementById(elementId);
            var windowUrl = 'about:blank';
            var uniqueName = new Date();
            var windowName = 'Print' + uniqueName.getTime();
            var printWindow = window.open(windowUrl, windowName, 'width=1000,height=1000');

            printWindow.document.write(`
        <!DOCTYPE html>
        <html>
            <head>
                <title>Print</title>
                <style>
                    table {
                        width:100% !important;
                        border-collapse: collapse !important;
                        text-align: center !important;
                    }
                    table tr {
                        background-color: white !important;
                        color: black !important;
                    }
                    th, td  {
                        border: 1px solid black !important;
                    }
                    th:last-child,
                    td:last-child {
                        display: none !important;
                    }
                    .ulo {
                        width: 100% !important;
                        display: flex !important;
                        justify-content:  space-evenly !important;
                    }
                    .ulo h5 {
                        font-size: 18px !important;
                        text-align: center !important;   
                    }
                </style>
            </head>
            <body>
                <h2 style="text-align: center;">VCAA Evaluation Results</h2>
                <h3 >Faculty : <?php echo $userRow['first_name'] . ' ' . $userRow['last_name'] ?></h3>
                ${printContent.innerHTML}
            </body>
        </html>
    `);
            printWindow.document.close();
            printWindow.focus();
            printWindow.print();

            // Close the print window after printing
            printWindow.close();
        }
    </script>
    <script>



        var averageRating = <?php echo json_encode($average); ?>;

        if (typeof averageRating !== 'number' || isNaN(averageRating)) {
            averageRating = 0;
        }

        var remainingRating = 5 - averageRating;

        var ctx = document.getElementById('averageChart').getContext('2d');
        var averageChart = new Chart(ctx, {
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

                    if (typeof averageRating === 'number') {
                        ctx.fillStyle = "black";
                        ctx.fillText(averageRating.toFixed(2), centerX, centerY);
                    } else {
                        console.error("averageRating is not a number:", averageRating);
                    }
                }
            }]

        });

    </script>
    <script>
        const subjects = <?php echo $subjectsJson; ?>;
        const subjectData = <?php echo $subjectDataJson; ?>;
        const semesters = <?php echo $semestersJson; ?>;

        const colorPalette = [
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
            'rgba(255, 159, 64, 1)',
        ];

        function filterEndSemesters() {
            const startSemester = document.getElementById('startSemesterFilter').value;
            const endSemesterFilter = document.getElementById('endSemesterFilter');

            endSemesterFilter.innerHTML = '<option value="">Select End Semester</option>';

            if (!startSemester) return;

            const endSemesters = semesters.filter(semester => semesters.indexOf(semester) > semesters.indexOf(startSemester));

            endSemesters.forEach(semester => {
                const option = document.createElement('option');
                option.value = semester;
                option.text = semester;
                endSemesterFilter.appendChild(option);
            });
        }

        function createFilteredDatasets(selectedSubject) {
            const startSemester = document.getElementById('startSemesterFilter').value;
            const endSemester = document.getElementById('endSemesterFilter').value;

            const startIndex = semesters.indexOf(startSemester);
            const endIndex = endSemester ? semesters.indexOf(endSemester) : semesters.length - 1;

            return subjects
                .filter(subject => selectedSubject === 'all' || subject === selectedSubject)
                .map((subject, index) => {
                    let data = subjectData[subject];

                    if (!Array.isArray(data)) {
                        data = Array(semesters.length).fill(0);
                    }

                    const cleanData = data.slice(startIndex, endIndex + 1).map((value) => {
                        return value !== null ? value : 0;
                    });

                    return {
                        label: subject,
                        data: cleanData,
                        borderColor: colorPalette[index % colorPalette.length],
                        backgroundColor: colorPalette[index % colorPalette.length],
                        fill: false,
                        tension: 0.1
                    };
                });
        }

        function updateChart() {
            const selectedSubject = document.getElementById('subjectFilter').value;

            const startSemester = document.getElementById('startSemesterFilter').value;
            const endSemester = document.getElementById('endSemesterFilter').value;

            const startIndex = semesters.indexOf(startSemester);
            const endIndex = endSemester ? semesters.indexOf(endSemester) : semesters.length - 1;

            const filteredLabels = semesters.slice(startIndex, endIndex + 1);

            const datasets = createFilteredDatasets(selectedSubject);

            lineChart.data.labels = filteredLabels;
            lineChart.data.datasets = datasets;
            lineChart.update();
        }


        document.getElementById('startSemesterFilter').addEventListener('change', () => {
            filterEndSemesters();
            updateChart();
        });

        document.getElementById('subjectFilter').addEventListener('change', function () {
            const selectedSubject = this.value;
            updateChart();
        });

        document.getElementById('endSemesterFilter').addEventListener('change', updateChart);

        const ctxLine = document.getElementById('lineChart').getContext('2d');
        const lineChart = new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: semesters,
                datasets: createFilteredDatasets('all')
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 5
                    }
                }
            }
        });

        document.getElementById('startSemesterFilter').value = semesters[0];
        filterEndSemesters();
        document.getElementById('endSemesterFilter').value = semesters[semesters.length - 1];
        updateChart();

    </script>
    <script src="../../bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../../public/js/sweetalert2@11.js"></script>

    <script>
        $(document).ready(function () {

            fetchFilteredResults();

            $('#academic_year, #semester').change(function () {
                fetchFilteredResults();
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

            function printPartOfPage(elementId) {
                var originalCanvas = document.getElementById(elementId);

                var windowUrl = 'about:blank';
                var uniqueName = new Date();
                var windowName = 'Print' + uniqueName.getTime();
                var printWindow = window.open(windowUrl, windowName, 'width=800,height=600');

                printWindow.document.write('<html><head><title>Print Canvas</title></head><body>');
                printWindow.document.write('<canvas id="printCanvas" width="500" height="300" style="border:1px solid #ccc;"></canvas>');
                printWindow.document.write('</body></html>');
                printWindow.document.close();
                printWindow.focus();

                var printCanvas = printWindow.document.getElementById('printCanvas');
                var printCtx = printCanvas.getContext('2d');

                const scaleFactor = 0.65;
                printCanvas.width = originalCanvas.width * scaleFactor;
                printCanvas.height = originalCanvas.height * scaleFactor;

                printCtx.scale(scaleFactor, scaleFactor);
                printCtx.drawImage(originalCanvas, 0, 0);

                setTimeout(function () {
                    printWindow.print();
                    printWindow.close();
                }, 100);
            }

            $('#printBtn').click(function () {
                printPartOfPage('lineChart');
            });
        });

        function fetchFilteredResults() {
            var semester = $('#semester').val();
            var academicYear = $('#academic_year').val();

            if (semester === '' && academicYear === '') {
                $.ajax({
                    type: 'POST',
                    url: 'filtervcaa.php',
                    data: {
                        fetchAll: true
                    },
                    success: function (data) {
                        $('#result').html(data);
                    },

                });
            } else {
                $.ajax({
                    type: 'POST',
                    url: 'filtervcaa.php',
                    data: {
                        semester: semester,
                        academic_year: academicYear
                    },
                    success: function (data) {
                        $('#result').html(data);
                    },
                });
            }
        }

    </script>

</body>

</html>
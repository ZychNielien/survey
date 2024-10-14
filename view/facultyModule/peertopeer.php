<?php

// NAV BAR
include "components/navBar.php"

    ?>

<head>
    <!-- TITLE WEB PAGE -->
    <title>Peer to Peer Faculty Evaluation</title>

    <!-- ALL STYLES, CSS AND SCRIPTS -->
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
    <script src="../../public/js/jquery-3.7.1.min.js"></script>
    <style>
        ul li {
            list-style: none;
        }

        .star {
            color: gold;
            font-size: 30px;
        }
    </style>
</head>

<!-- CONTENT CONTAINER -->
<section class="contentContainer">

    <!-- CONTENT SHADOW CONTAINER -->
    <div class="card p-3 shadow " style="min-height: 720px;">

        <!-- NAVIGATION FOR TAB LIST -->
        <nav>
            <div class="nav nav-tabs mb-3" id="nav-tab" role="tablist">
                <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home"
                    type="button" role="tab" aria-controls="nav-home" aria-selected="true">Peer to Peer
                    Evaluation Result</button>
                <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile"
                    type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Peer to Peer
                    Evaluation Feedbacks</button>
            </div>
        </nav>

        <!-- CONTENT OF A TAB LIST -->
        <div class="tab-content p-3 border overflow-auto" id="nav-tabContent">

            <!-- TAB LIST FIRST TAB -->
            <div class="tab-pane fade active show" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">

                <?php

                function sanitizeColumnName($name)
                {
                    return preg_replace('/[^a-zA-Z0-9_]/', '', trim($name));
                }

                $FacultyID = $userRow['faculty_Id'];
                $sqlSAY = "SELECT DISTINCT  sf.semester, sf.academic_year 
                            FROM peertopeerform sf
                            JOIN instructor i ON sf.toFacultyID = i.faculty_Id
                            WHERE i.faculty_Id = '$FacultyID'";

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
                </form>

                <div class="overflow-auto" style="max-height: 500px">
                    <!-- RESULT OF DATA FROM THE STUDENTS EVALUATION -->
                    <div id="result"></div>
                </div>

            </div>

            <!-- TAB LIST SECOND TAB -->
            <div class="tab-pane fade" id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">
                <?php
                $o_id = $userRow['faculty_Id'];
                $query = "SELECT * FROM peertopeerform WHERE toFacultyID='$o_id' ORDER BY date DESC";
                $query_run = mysqli_query($con, $query);

                $ratings = [];
                while ($ratingRow = mysqli_fetch_assoc($query_run)) {
                    $ratings[] = $ratingRow;
                }

                $sql = "SELECT * FROM facultycategories";
                $sql_query = mysqli_query($con, $sql);

                $semesters_query = "SELECT DISTINCT semester FROM peertopeerform WHERE toFacultyID='$o_id'";
                $semesters_result = mysqli_query($con, $semesters_query);

                $academic_years_query = "SELECT DISTINCT academic_year FROM peertopeerform WHERE toFacultyID='$o_id'";
                $academic_years_result = mysqli_query($con, $academic_years_query);

                if (mysqli_num_rows($sql_query)) {
                    ?>
                    <div class="filter-section d-flex justify-content-evenly">
                        <select id="rating-filter" class="form-select" style="width: 150px;">
                            <option value="all">All Ratings</option>
                            <option value="1">1 ⭐</option>
                            <option value="2">2 ⭐</option>
                            <option value="3">3 ⭐</option>
                            <option value="4">4 ⭐</option>
                            <option value="5">5 ⭐</option>
                        </select>

                        <select id="semester-filter" class="form-select" style="width: 150px;">
                            <option value="all">All Semesters</option>
                            <?php while ($row = mysqli_fetch_assoc($semesters_result)): ?>
                                <option value="<?php echo $row['semester']; ?>"><?php echo $row['semester']; ?></option>
                            <?php endwhile; ?>
                        </select>

                        <select id="academic-year-filter" class="form-select" style="width: 150px;">
                            <option value="all">All Academic Years</option>
                            <?php while ($row = mysqli_fetch_assoc($academic_years_result)): ?>
                                <option value="<?php echo $row['academic_year']; ?>"><?php echo $row['academic_year']; ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div id="ratings-container">
                        <?php
                        foreach ($ratings as $ratingRow) {
                            $totalAverage = 0;
                            $categoryCount = 0;

                            while ($categoryRow = mysqli_fetch_assoc($sql_query)) {
                                $categories = $categoryRow['categories'];
                                $totalRatings = [0, 0, 0, 0, 0];
                                $ratingCount = 0;

                                $sqlcriteria = "SELECT * FROM facultycriteria WHERE facultyCategories = '$categories'";
                                $resultCriteria = mysqli_query($con, $sqlcriteria);

                                if (mysqli_num_rows($resultCriteria) > 0) {
                                    while ($criteriaRow = mysqli_fetch_array($resultCriteria)) {
                                        $columnName = sanitizeColumnName($criteriaRow['facultyCategories']);
                                        $finalColumnName = $columnName . $criteriaRow['id'];

                                        $criteriaRating = $ratingRow[$finalColumnName] ?? null;

                                        if ($criteriaRating !== null) {
                                            $totalRatings[$criteriaRating - 1]++;
                                            $ratingCount++;
                                        }
                                    }

                                    if ($ratingCount > 0) {
                                        $averageRating = 0;
                                        for ($i = 0; $i < 5; $i++) {
                                            $averageRating += ($i + 1) * $totalRatings[$i];
                                        }
                                        $averageRating /= $ratingCount;
                                        $totalAverage += $averageRating;
                                        $categoryCount++;
                                    }
                                }
                            }

                            $finalAverageRating = ($categoryCount > 0) ? round($totalAverage / $categoryCount, 2) : 0;

                            // VERBAL INTERPRETATION NG FINAL AVERAGE RATING
                            $verbalInterpretation = '';
                            switch (true) {
                                case ($finalAverageRating >= 0 && $finalAverageRating < 1):
                                    $verbalInterpretation = '';
                                    break;
                                case ($finalAverageRating >= 1 && $finalAverageRating < 2):
                                    $verbalInterpretation = '<span class="star">⭐</span>';
                                    break;
                                case ($finalAverageRating >= 2 && $finalAverageRating < 3):
                                    $verbalInterpretation = '<span class="star">⭐⭐</span>';
                                    break;
                                case ($finalAverageRating >= 3 && $finalAverageRating < 4):
                                    $verbalInterpretation = '⭐⭐⭐';
                                    break;
                                case ($finalAverageRating >= 4 && $finalAverageRating < 5):
                                    $verbalInterpretation = '⭐⭐⭐⭐';
                                    break;
                                case ($finalAverageRating == 5):
                                    $verbalInterpretation = '⭐⭐⭐⭐⭐';
                                    break;
                                default:
                                    $verbalInterpretation = 'Invalid Rating';
                                    break;
                            }

                            $datetoString = $ratingRow['date'];
                            $date = new DateTime($datetoString);
                            $formattedDate = $date->format('F j, Y');

                            echo '
                <div class="rating-row" 
                     data-average="' . $finalAverageRating . '" 
                     data-semester="' . $ratingRow['semester'] . '" 
                     data-academic-year="' . $ratingRow['academic_year'] . '" 
                     style="display:flex; justify-content: center;">
                    <div class="border rounded-3 m-5 p-2 border-danger flex-column" style="width: 700px;">
                        <div class="d-flex justify-content-center align-items-center">
                            <span style="font-size: 30px;">Anonymous</span>
                        </div>
                        <div class="d-flex justify-content-evenly align-items-center m-2">
                            <span>Semester: ' . $ratingRow['semester'] . ' </span>
                            <span>Academic Year: ' . $ratingRow['academic_year'] . '</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span style="font-size: 30px;">' . $verbalInterpretation . '</span>
                            <span class="text-secondary">' . $formattedDate . '</span>
                        </div>
                        <p class="mx-5 my-3 py-0 text-center">' . $ratingRow['commentText'] . '</p>
                    </div>
                </div>
                ';
                            mysqli_data_seek($sql_query, 0);
                        }
                        ?>
                        <div id="no-results-message" style="display: none; text-align: center; margin-top: 20px;">
                            <h1 style="color: red;">No results found for the selected filter.</h1>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

    </div>



    </div>
    </div>
    </div>

</section>


<script src="../public/js/jquery-3.7.1.min.js"></script>
<script>
    $(document).ready(function () {
        fetchFilteredResults();

        $('#academic_year, #semester').change(function () {
            fetchFilteredResults();
        });

        // STAR RATING FEEDBACK FILTER
        $('#rating-filter, #semester-filter, #academic-year-filter').on('change', function () {
            var selectedRating = $('#rating-filter').val();
            var selectedSemester = $('#semester-filter').val();
            var selectedAcademicYear = $('#academic-year-filter').val();

            let visibleCount = 0;

            $('.rating-row').each(function () {
                var averageRating = parseFloat($(this).data('average'));
                var flooredRating = Math.floor(averageRating);
                var rowSemester = $(this).data('semester');
                var rowAcademicYear = $(this).data('academic-year');

                var ratingMatch = (selectedRating === 'all' || flooredRating == selectedRating);
                var semesterMatch = (selectedSemester === 'all' || rowSemester == selectedSemester);
                var academicYearMatch = (selectedAcademicYear === 'all' || rowAcademicYear == selectedAcademicYear);

                if (ratingMatch && semesterMatch && academicYearMatch) {
                    $(this).show();
                    visibleCount++;
                } else {
                    $(this).hide();
                }
            });

            if (visibleCount === 0) {
                $('#no-results-message').show();
            } else {
                $('#no-results-message').hide();
            }
        });

        $('#no-results-message').hide();
    });

    // FILTER FOR SEMESTER AND ACADEMIC YEAR
    function fetchFilteredResults() {
        var semester = $('#semester').val();
        var academicYear = $('#academic_year').val();

        if (semester === '' && academicYear === '') {
            $.ajax({
                type: 'POST',
                url: 'filterpeertopeer.php',
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
                url: 'filterpeertopeer.php',
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
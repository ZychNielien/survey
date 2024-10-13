<?php

include "components/navBar.php";
include "../../model/dbconnection.php";

?>

<head>
    <title>Evaluation Result</title>
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
    <script src="../../public/js/jquery-3.7.1.min.js"></script>
</head>

<section class="contentContainer">

    <div class="card p-3 shadow " style="min-height: 720px;">

        <!-- NAVIGATION TAB -->
        <nav>
            <div class="nav nav-tabs mb-3" id="nav-tab" role="tablist">
                <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home"
                    type="button" role="tab" aria-controls="nav-home" aria-selected="true">Results of Student
                    Evaluations</button>
                <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile"
                    type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Feedbacks</button>
            </div>
        </nav>

        <div class="tab-content p-3 border bg-light overflow-auto" id="nav-tabContent">

            <!-- RESULTS OF EVALUATION FROM STUDENT'S EVALUATION -->
            <div class="tab-pane fade active show" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">

                <?php

                function sanitizeColumnName($name)
                {
                    return preg_replace('/[^a-zA-Z0-9_]/', '', trim($name));
                }

                $sqlSAY = "SELECT DISTINCT semester, academic_year FROM studentsform";
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

            <!-- FEEDBACK FROM STUDENT'S EVALUATION -->
            <div class="tab-pane fade " id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">

                <?php

                $o_id = $userRow['faculty_Id'];
                $query = "SELECT * FROM studentsform WHERE toFacultyID='$o_id'";
                $query_run = mysqli_query($con, $query);

                $ratings = [];

                while ($ratingRow = mysqli_fetch_assoc($query_run)) {
                    $ratings[] = $ratingRow;
                }

                $sql = "SELECT * FROM studentscategories";
                $sql_query = mysqli_query($con, $sql);

                if (mysqli_num_rows($sql_query)) {
                    ?>

                    <!-- FILTER FOR STAR RATING FEEDBACK -->
                    <div class="filter-section d-flex justify-content-evenly">
                        <select id="rating-filter" class="form-select" style="width: 200px;">
                            <option value="all">All Ratings</option>
                            <option value="1">1 ⭐</option>
                            <option value="2">2 ⭐</option>
                            <option value="3">3 ⭐</option>
                            <option value="4">4 ⭐</option>
                            <option value="5">5 ⭐</option>
                        </select>
                        <button id="apply-filter" class="btn btn-primary">Apply Filter</button>
                    </div>

                    <!-- FEEDBACK RATING -->
                    <div id="ratings-container">
                        <?php
                        $categoriesArray = [];
                        while ($categoryRow = mysqli_fetch_assoc($sql_query)) {
                            $categoriesArray[] = $categoryRow;
                        }

                        foreach ($ratings as $ratingRow) {
                            $totalAverage = 0;
                            $categoryCount = 0;

                            foreach ($categoriesArray as $categoryRow) {
                                $categories = $categoryRow['categories'];
                                $totalRatings = [0, 0, 0, 0, 0];
                                $ratingCount = 0;

                                $sqlcriteria = "SELECT * FROM studentscriteria WHERE studentsCategories = '$categories'";
                                $resultCriteria = mysqli_query($con, $sqlcriteria);

                                if (mysqli_num_rows($resultCriteria) > 0) {
                                    while ($criteriaRow = mysqli_fetch_array($resultCriteria)) {
                                        $columnName = sanitizeColumnName($criteriaRow['studentsCategories']);
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

                            // FORMULA KUNG PAANO KINUHA YUNG FINAL AVERAGE PER EVALUATION FORM
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
                                <div class="rating-row" data-average="' . $finalAverageRating . '" style="display:flex; justify-content: center;">
                                    <div class="border rounded-3 m-5 p-2 border-danger flex-column" style="width: 700px;">
                                        <div class="d-flex justify-content-center align-items-center">
                                            <span style="font-size: 30px;">' . $ratingRow['subject'] . '</span>
                                        </div>
                                        <div class="d-flex justify-content-evenly align-items-center m-2">
                                            <span>Semester: ' . $ratingRow['semester'] . ' </span>
                                            <span>Academic Year:' . $ratingRow['academic_year'] . '</span>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span style="font-size: 30px;">' . $verbalInterpretation . '</span>
                                            <span class="text-secondary">' . $formattedDate . '</span>
                                        </div>
                                        <p class="mx-5 my-3 py-0 text-center">' . $ratingRow['comment'] . '</p>
                                    </div>
                                </div>';

                            mysqli_data_seek($sql_query, 0);

                        }
                }

                ?>
                    <!-- IF NO RESULT ITO YUNG LALABAS -->
                    <div id="no-results-message" style="display: none; text-align: center; margin-top: 20px;">
                        <h1 style="color: red;">No results found for the selected filter.</h1>
                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

<script>
    $(document).ready(function () {
        fetchFilteredResults();

        $('#academic_year, #semester').change(function () {
            fetchFilteredResults();
        });


        // STAR RATING FEEDBACK FILTER
        $('#apply-filter').on('click', function () {
            var selectedRating = $('#rating-filter').val();
            console.log("Selected Rating:", selectedRating);

            let visibleCount = 0;

            $('.rating-row').each(function () {
                var averageRating = parseFloat($(this).data('average'));
                var flooredRating = Math.floor(averageRating);
                console.log("Average Rating (floored):", flooredRating);

                if (selectedRating === 'all' || flooredRating == selectedRating) {
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
                url: 'filter.php',
                data: {
                    fetchAll: true
                },
                success: function (data) {
                    console.log(data);
                    $('#result').html(data);
                },

            });
        } else {
            $.ajax({
                type: 'POST',
                url: 'filter.php',
                data: {
                    semester: semester,
                    academic_year: academicYear
                },
                success: function (data) {
                    console.log(data);
                    $('#result').html(data);
                },
            });
        }
    }

</script>
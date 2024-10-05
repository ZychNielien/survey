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

                <!-- FIRST TAB CONTENT -->
                <div class="overflow-auto" style="max-height: 580px">

                    <!-- FIRST TAB TABLE -->
                    <table class="table table-striped table-bordered text-center align-middle w-100">

                        <thead>
                            <tr style="background: #d0112b; color: #fff;">
                                <th>Area</th>
                                <th>APS</th>
                                <th>Description</th>
                                <th>Recommendation</th>
                            </tr>
                        </thead>

                        <tbody>

                            <!-- PHP CODE FOR FIRST TAB FETCHING THE AVERAGE -->
                            <?php

                            // FUNCTION SA PAGTANGGAL NG MGA SPECIAL CHARACTERS SA COLUMN
                            function sanitizeColumnName($name)
                            {
                                return preg_replace('/[^a-zA-Z0-9_]/', '', trim($name));
                            }

                            // FACULTY ID NG NAKALOGIN SA WEBSITE, NAKAFETCH SIYA SA NAV NA PHP FILE
                            $facultyID = $userRow['faculty_Id'];


                            // KAILANGAN GUMAWA NG VARIABLE OUTSIDE THE BLOCK PARA MA CALL MO OUTSIDE NG PHP TAG
                            $averageRatings = [];
                            $totalAverage = 0;
                            $categoryCount = 0;

                            // SQL QUERY PARA MAFETCH LAHAT NG CATEGORIES NG FACULTY PEER TO PEER
                            $sql = "SELECT * FROM `facultycategories`";
                            $sql_query = mysqli_query($con, $sql);

                            // CONDITION KUNG SAAN BINIBILANG ANG LAMAN NG QUERY IF MORE THAN 0, ITO YUNG LALABAS
                            if (mysqli_num_rows($sql_query) > 0) {

                                // PAGKUHA NG MGA DATA SA ROW GAMIT ANG MYSQLI_FETCH_ASSOC NA FUNCTION
                                while ($categoriesRow = mysqli_fetch_assoc($sql_query)) {

                                    // ITO YUNG LAHAT NG CATEGORIES NG FACULTY
                                    $categories = $categoriesRow['categories'];

                                    // ITO YUNG RATING 1 - 5 DITO MALALAGAY
                                    $totalRatings = [0, 0, 0, 0, 0];

                                    // ITO YUNG PAGBILANG NG RATINGS SA CATEGORY
                                    $ratingCount = 0;

                                    // SQL QUERY PARA MAKUHA LAHAT NG CRITERIA NG FACULTY
                                    $sqlcriteria = "SELECT * FROM `facultycriteria` WHERE facultyCategories = '$categories'";
                                    $resultCriteria = mysqli_query($con, $sqlcriteria);

                                    // CONDITION KUNG SAAN BINIBILANG ANG LAMAN NG QUERY IF MORE THAN 0, ITO YUNG LALABAS
                                    if (mysqli_num_rows($resultCriteria) > 0) {

                                        // FETCH ALL THE FORMS NA NAGSAGOT SA FAULTY NA NAKALOGIN
                                        $SQLFaculty = "SELECT * FROM `peertopeerform` WHERE toFacultyID = '$facultyID' ";
                                        $SQLFaculty_query = mysqli_query($con, $SQLFaculty);

                                        // PAGKUHA NG MGA DATA SA ROW GAMIT ANG MYSQLI_FETCH_ASSOC NA FUNCTION
                                        while ($ratingRow = mysqli_fetch_assoc($SQLFaculty_query)) {

                                            // PAGKUHA NG MGA DATA SA ROW GAMIT ANG MYSQLI_FETCH_ASSOC NA FUNCTION
                                            while ($criteriaRow = mysqli_fetch_array($resultCriteria)) {
                                                // CALL NG SANITIZE FUNCTION PARA MAWALA YUNG MGA SPECIAL CHARACTERS SA COLUMN NAME
                                                $columnName = sanitizeColumnName($criteriaRow['facultyCategories']);

                                                // PINAGSAMA YUNG COLUMN NAME NG CATEGORIES AT LAST ID SA CRITERIA PARA SA BAGONG COLUMN SA PEER TO PEER
                                                $finalColumnName = $columnName . $criteriaRow['id'];

                                                // PAGKUHA NG RATING SA BAWAT CRITERIA
                                                $criteriaRating = $ratingRow[$finalColumnName] ?? null;

                                                if ($criteriaRating !== null) {
                                                    // INCREMENT THE RATING NUMBER PARA MAKUHA LAHAT NG RATING
                                                    $totalRatings[$criteriaRating - 1]++;
                                                    $ratingCount++;
                                                }
                                            }
                                            // RESET NG CRITERIA PARA MAGPROCEED SA SUNOD NA FORM
                                            mysqli_data_seek($resultCriteria, 0);
                                        }

                                        // CALCULATE THE AVERAGE RATING PARA SA CATEGORY
                                        $averageRating = 0;
                                        if ($ratingCount > 0) {
                                            for ($i = 0; $i < 5; $i++) {
                                                $averageRating += ($i + 1) * $totalRatings[$i];
                                            }
                                            $averageRating /= $ratingCount;
                                            $averageRatings[$categories] = round($averageRating, 2);
                                            $totalAverage += $averageRating;
                                            $categoryCount++;
                                        } else {
                                            $averageRatings[$categories] = 'No ratings';
                                        }

                                        // FINAL OVERALL AVERAGE
                                        $finalAverageRating = 0;
                                        if ($categoryCount > 0) {
                                            // AVARAGE NG LAHAT NG CATEGORY
                                            $finalAverageRating = round($totalAverage / $categoryCount, 2);
                                        } else {
                                            $finalAverageRating = 'No ratings available';
                                        }

                                        // VERBAL INTERPRETATION NG STAR RATING
                                        $verbalInterpretation = '';
                                        switch (true) {
                                            case ($finalAverageRating >= 0 && $finalAverageRating < 1):
                                                $verbalInterpretation = 'None';
                                                $linksHere =
                                                    '<ul>
                                                <li><a href="#">Link here 1</a></li>
                                                <li><a href="#">Link here 2</a></li>
                                                <li><a href="#">Link here 3</a></li>
                                                </ul>';
                                                break;
                                            case ($finalAverageRating >= 1 && $finalAverageRating < 2):
                                                $verbalInterpretation = 'Poor';
                                                $linksHere =
                                                    '<ul>
                                                <li><a href="#">Link here 1</a></li>
                                                <li><a href="#">Link here 2</a></li>
                                                <li><a href="#">Link here 3</a></li>
                                                </ul>';
                                                break;
                                            case ($finalAverageRating >= 2 && $finalAverageRating < 3):
                                                $verbalInterpretation = 'Fair';
                                                $linksHere =
                                                    '<ul>
                                            <li><a href="#">Link here 1</a></li>
                                            <li><a href="#">Link here 2</a></li>
                                            <li><a href="#">Link here 3</a></li>
                                            </ul>';
                                                break;
                                            case ($finalAverageRating >= 3 && $finalAverageRating < 4):
                                                $verbalInterpretation = 'Satisfactory';
                                                $linksHere =
                                                    '<ul>
                                                <li><a href="#">Link here 1</a></li>
                                                <li><a href="#">Link here 2</a></li>
                                                <li><a href="#">Link here 3</a></li>
                                                </ul>';
                                                break;
                                            case ($finalAverageRating >= 4 && $finalAverageRating < 5):
                                                $verbalInterpretation = 'Very Satisfactory';
                                                $linksHere =
                                                    '<ul>
                                                <li><a href="#">Link here 1</a></li>
                                                <li><a href="#">Link here 2</a></li>
                                                <li><a href="#">Link here 3</a></li>
                                                </ul>';
                                                break;
                                            case ($finalAverageRating == 5):
                                                $verbalInterpretation = 'Outstanding';
                                                $linksHere =
                                                    '<ul>
                                                <li><a href="#">Link here 1</a></li>
                                                <li><a href="#">Link here 2</a></li>
                                                <li><a href="#">Link here 3</a></li>
                                                </ul>';
                                                break;
                                            default:
                                                $verbalInterpretation = 'No description';
                                                $linksHere = 'No links';
                                                break;
                                        }

                                        // RESULTS
                                        echo '
                                            <tr>
                                                <td>' . $categoriesRow['categories'] . '</td>
                                                <td>' . $finalAverageRating . '</td>
                                                <td>' . $verbalInterpretation . '</td>
                                                <td>' . $linksHere . '</td>
                                            </tr>
                                        ';
                                    } else {
                                        echo '<tr><td colspan="2" class="text-center">No Categories Found</td></tr>';
                                    }
                                }
                            } else {
                                echo 'No Categories';
                            }

                            ?>

                        </tbody>

                    </table>

                </div>

            </div>

            <!-- TAB LIST SECOND TAB -->
            <div class="tab-pane fade " id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">

                <!-- PHP CODE FOR SECOND TAB FETCHING THE FEEBACKS -->
                <?php

                // FACULTY ID NG NAKALOGIN SA WEBSITE, NAKAFETCH SIYA SA NAV NA PHP FILE
                $o_id = $userRow['faculty_Id'];

                // SQL QUERY PARA MAFETCH LAHAT NG FORM SA PEER TO PEER
                $query = "SELECT * FROM peertopeerform WHERE toFacultyID='$o_id'";
                $query_run = mysqli_query($con, $query);

                // FETCH ALL RATINGS
                $ratings = [];
                while ($ratingRow = mysqli_fetch_assoc($query_run)) {
                    $ratings[] = $ratingRow;
                }

                // SQL QUERY PARA MAFETCH LAHAT NG CATEGORIES SA FACULTY
                $sql = "SELECT * FROM facultycategories";
                $sql_query = mysqli_query($con, $sql);


                // CHINECHECK KUNG MAY LAMAN ANG QUERY
                if (mysqli_num_rows($sql_query)) {
                    ?>

                    <!-- FILTER NG STAR RATINGS -->
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

                    <!-- DITO MAKIKITA YUNG RATINGS AND FEECBACKS -->
                    <div id="ratings-container">

                        <!-- PHP CODE PARA MAKITA YUNG MGA RATINGS -->
                        <?php


                        foreach ($ratings as $ratingRow) {

                            // SINET SA 0 ANG AVERAGE RATING
                            $totalAverage = 0;

                            // SINET ANG BILANG NG CATEGORY SA 0
                            $categoryCount = 0;

                            // PAULIT ULIT SA LAHAT NG CRITERIA NA MAYROONG SAME NA CATEGORY
                            while ($categoryRow = mysqli_fetch_assoc($sql_query)) {

                                // ITO YUNG MGA CATEGORIES SA FACULTY PEER TO PEER
                                $categories = $categoryRow['categories'];

                                // PARA BILANGIN YUNG MGA RATINGS MULA 1 HANGGANG 5
                                $totalRatings = [0, 0, 0, 0, 0];

                                // BILANG NG CRITERIA SA BAWAT CATEGORY
                                $ratingCount = 0;

                                // SQL QUERY PARA MAFETCH LAHAT NG CRITERIA SA FACULTY CATEGORIES
                                $sqlcriteria = "SELECT * FROM facultycriteria WHERE facultyCategories = '$categories'";
                                $resultCriteria = mysqli_query($con, $sqlcriteria);

                                // TINITINGNAN KUNG MAY LAMAN YUNG SQL QUERY
                                if (mysqli_num_rows($resultCriteria) > 0) {

                                    // PAGULIT ULIT NG MGA CRITERIA SA LOOB NG CATEGORY
                                    while ($criteriaRow = mysqli_fetch_array($resultCriteria)) {

                                        $columnName = sanitizeColumnName($criteriaRow['facultyCategories']);
                                        $finalColumnName = $columnName . $criteriaRow['id']; // Gumawa ng final column name
                    
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
            <div class="d-flex justify-content-between align-items-center">
                <span style="font-size: 30px;">' . $verbalInterpretation . '</span>
                <span class="text-secondary">' . $formattedDate . '</span>
            </div>
            <p class="mx-5 my-3 py-0 text-center">' . $ratingRow['commentText'] . '</p>
        </div>
    </div>';
                            mysqli_data_seek($sql_query, 0);
                        }
                }
                ?>
                    <div id="no-results-message" style="display: none; text-align: center; margin-top: 20px;">
                        <h1 style="color: red;">No results found for the selected filter.</h1>
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
        $('#apply-filter').on('click', function () {
            var selectedRating = $('#rating-filter').val();

            let visibleCount = 0;

            $('.rating-row').each(function () {
                var averageRating = parseFloat($(this).data('average'));
                var flooredRating = Math.floor(averageRating);

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


</script>
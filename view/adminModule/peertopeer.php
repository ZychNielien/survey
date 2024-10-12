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

                            function getVerbalInterpretationAndLinks($averageRating)
                            {
                                $result = ['interpretation' => '', 'links' => ''];



                                if ($averageRating >= 0 && $averageRating < 1) {
                                    $result['interpretation'] = 'None';
                                    $result['links'] = '<ul><li><a href="#">Link for None 1</a></li><li><a href="#">Link for None 2</a></li></ul>';
                                } elseif ($averageRating >= 1 && $averageRating < 2) {
                                    $result['interpretation'] = 'Poor';
                                    $result['links'] = '<ul><li><a href="#">Link for Poor 1</a></li><li><a href="#">Link for Poor 2</a></li></ul>';
                                } elseif ($averageRating >= 2 && $averageRating < 3) {
                                    $result['interpretation'] = 'Fair';
                                    $result['links'] = 'No recommendation needed'; // No links for ratings >= 2.
                                } elseif ($averageRating >= 3 && $averageRating < 4) {
                                    $result['interpretation'] = 'Satisfactory';
                                    $result['links'] = 'No recommendation needed'; // No links for ratings >= 2.
                                } elseif ($averageRating >= 4 && $averageRating < 5) {
                                    $result['interpretation'] = 'Very Satisfactory';
                                    $result['links'] = 'No recommendation needed'; // No links for ratings >= 2.
                                } elseif ($averageRating == 5) {
                                    $result['interpretation'] = 'Outstanding';
                                    $result['links'] = 'No recommendation needed'; // No links for ratings >= 2.
                                } else {
                                    $result['interpretation'] = 'No description';
                                    $result['links'] = 'No links available';
                                }

                                return $result;
                            }
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

                                        $interpretationData = getVerbalInterpretationAndLinks($averageRating);

                                        // RESULTS
                                        echo '
                                            <tr>
                                                <td>' . $categoriesRow['categories'] . '</td>
                                                <td>' . $finalAverageRating . '</td>
                                                            <td>' . $interpretationData['interpretation'] . '</td> 
                    <td>' . $interpretationData['links'] . '</td>
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
                <?php
                $o_id = $userRow['faculty_Id'];
                $query = "SELECT * FROM peertopeerform WHERE toFacultyID='$o_id'";
                $query_run = mysqli_query($con, $query);

                // Fetch all ratings
                $ratings = [];
                while ($ratingRow = mysqli_fetch_assoc($query_run)) {
                    $ratings[] = $ratingRow; // Store each row of ratings in an array
                }

                $sql = "SELECT * FROM facultycategories";
                $sql_query = mysqli_query($con, $sql);

                if (mysqli_num_rows($sql_query)) {
                    ?>
                    <!-- Filter Dropdown for Final Average Rating or Stars -->
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

                                    // Calculate average rating
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

                            // Determine verbal interpretation
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
                            mysqli_data_seek($sql_query, 0); // Reset categories query
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
        // Handle filtering on button click
        $('#apply-filter').on('click', function () {
            // Get the selected rating from the dropdown
            var selectedRating = $('#rating-filter').val();
            console.log("Selected Rating:", selectedRating); // Debugging

            // Track if any rows are visible after filtering
            let visibleCount = 0;

            // Loop through each rating row
            $('.rating-row').each(function () {
                var averageRating = parseFloat($(this).data('average'));
                var flooredRating = Math.floor(averageRating); // Apply Math.floor to round down
                console.log("Average Rating (floored):", flooredRating); // Debugging

                // Show or hide rows based on matching the selected rating
                if (selectedRating === 'all' || flooredRating == selectedRating) {
                    $(this).show(); // Show rows that match
                    visibleCount++; // Increment visible count
                } else {
                    $(this).hide(); // Hide rows that don't match
                }
            });

            // Show or hide "No Results" message based on visible rows count
            if (visibleCount === 0) {
                $('#no-results-message').show();
            } else {
                $('#no-results-message').hide();
            }
        });

        // Initially hide the "No Results Found" message
        $('#no-results-message').hide();
    });


</script>
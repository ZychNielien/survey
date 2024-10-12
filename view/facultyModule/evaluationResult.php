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
        <nav>
            <div class="nav nav-tabs mb-3" id="nav-tab" role="tablist">
                <button class="nav-link active" id="nav-home-tab" data-bs-toggle="tab" data-bs-target="#nav-home"
                    type="button" role="tab" aria-controls="nav-home" aria-selected="true">Evaluation Result</button>
                <button class="nav-link" id="nav-profile-tab" data-bs-toggle="tab" data-bs-target="#nav-profile"
                    type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Feedbacks</button>
            </div>
        </nav>
        <div class="tab-content p-3 border bg-light overflow-auto" id="nav-tabContent">
            <div class="tab-pane fade active show" id="nav-home" role="tabpanel" aria-labelledby="nav-home-tab">

                <h3 class="text-danger fw-bold text-center">Evaluation Result From Student and Classroom Observations
                </h3>


                <div class="overflow-auto" style="max-height: 580px">
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

                            // Verbal interpretation based on rating.
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



                            // FACULTY ID NG NAKALOGIN SA WEBSITE, NAKAFETCH SIYA SA NAV NA PHP FILE
                            $facultyID = $userRow['faculty_Id'];


                            // KAILANGAN GUMAWA NG VARIABLE OUTSIDE THE BLOCK PARA MA CALL MO OUTSIDE NG PHP TAG
// Initialize these variables outside the loops to accumulate totals.
                            $totalAverage = 0;
                            $categoryCount = 0;
                            $totalCriteriaCount = 0; // Added to count criteria across categories.
                            
                            // Loop through each category.
                            $sql = "SELECT * FROM `studentscategories`";
                            $sql_query = mysqli_query($con, $sql);

                            if (mysqli_num_rows($sql_query) > 0) {

                                while ($categoriesRow = mysqli_fetch_assoc($sql_query)) {
                                    $categories = $categoriesRow['categories'];

                                    // Initialize these variables for each category.
                                    $totalRatings = [0, 0, 0, 0, 0]; // Array to count ratings 1 to 5.
                                    $ratingCount = 0;
                                    $criteriaCount = 0; // Initialize criteria count for each category.
                            
                                    // Get all criteria for the current category.
                                    $sqlcriteria = "SELECT * FROM `studentscriteria` WHERE studentsCategories = '$categories'";
                                    $resultCriteria = mysqli_query($con, $sqlcriteria);

                                    if (mysqli_num_rows($resultCriteria) > 0) {

                                        // Fetch all forms submitted for the current faculty.
                                        $SQLFaculty = "SELECT * FROM `studentsform` WHERE toFacultyID = '$facultyID'";
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
                                                $criteriaCount++; // Count the number of criteria for this category.
                                            }

                                            // Reset the criteria result pointer for the next form.
                                            mysqli_data_seek($resultCriteria, 0);
                                        }

                                        // Now calculate the average rating for this category.
                                        $averageRating = 0;
                                        if ($ratingCount > 0) {
                                            for ($i = 0; $i < 5; $i++) {
                                                $averageRating += ($i + 1) * $totalRatings[$i];
                                            }
                                            $averageRating /= $ratingCount; // Average based on the total number of ratings.
                            
                                            $averageRatings[$categories] = round($averageRating, 2);
                                            $totalAverage += $averageRating;
                                            $categoryCount++; // Increment the category count.
                                            $totalCriteriaCount += $criteriaCount; // Total criteria count across all categories.
                                        } else {
                                            $averageRatings[$categories] = 'No ratings';
                                        }

                                        $interpretationData = getVerbalInterpretationAndLinks($averageRating);


                                        // Output the average rating for this category.
                                        echo '
                <tr>
                    <td>' . $categoriesRow['categories'] . '</td>
                    <td>' . round($averageRating, 2) . '</td>
                    <td>' . $interpretationData['interpretation'] . '</td> 
                    <td>' . $interpretationData['links'] . '</td>
                </tr>
            ';
                                    } else {
                                        echo '<tr><td colspan="2" class="text-center">No Criteria Found for ' . $categories . '</td></tr>';
                                    }
                                }

                                // Final overall average across all categories.
                                $finalAverageRating = 0;
                                if ($categoryCount > 0) {
                                    $finalAverageRating = round($totalAverage / $categoryCount, 2); // Average of all categories.
                                } else {
                                    $finalAverageRating = 'No ratings available';
                                }


                                echo '<tr><td colspan="2">Overall Average: ' . $finalAverageRating . '</td></tr>';

                            } else {
                                echo '<tr><td colspan="2">No Categories Found</td></tr>';
                            }

                            ?>
                        </tbody>
                    </table>
                </div>
            </div>


            <div class="tab-pane fade " id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">

                <?php
                $o_id = $userRow['faculty_Id'];
                $query = "SELECT * FROM studentsform WHERE toFacultyID='$o_id'";
                $query_run = mysqli_query($con, $query);

                // Fetch all ratings
                $ratings = [];
                while ($ratingRow = mysqli_fetch_assoc($query_run)) {
                    $ratings[] = $ratingRow; // Store each row of ratings in an array
                }

                $sql = "SELECT * FROM studentscategories";
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
            <p class="mx-5 my-3 py-0 text-center">' . $ratingRow['comment'] . '</p>
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

</section>

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
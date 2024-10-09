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
                    <table class="table ">
                        <thead>
                            <tr style="background: #d0112b; color: #fff;">
                                <th>Area</th>
                                <th>APS</th>
                                <th>Description</th>
                                <th>Recommendation</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php

                            $facultyID = $userRow['faculty_Id'];

                            $SQLFaculty = "SELECT * FROM `peertopeerform` WHERE toFacultyID = '$facultyID'";
                            $SQLFaculty_query = mysqli_query($con, $SQLFaculty);

                            $averageRatings = [];
                            $totalAverage = 0;
                            $categoryCount = 0;


                            $sql = "SELECT * FROM `facultycategories`";
                            $sql_query = mysqli_query($con, $sql);

                            if (mysqli_num_rows($sql_query) > 0) {
                                while ($categoriesRow = mysqli_fetch_assoc($sql_query)) {
                                    $categories = $categoriesRow['categories'];

                                    $totalRatings = [0, 0, 0, 0, 0];
                                    $ratingCount = 0;


                                    $sqlcriteria = "SELECT * FROM `facultycriteria` WHERE facultyCategories = '$categories'";
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
                                        $finalAverageRating = 0;
                                        if ($categoryCount > 0) {
                                            $finalAverageRating = round($totalAverage / $categoryCount, 2);
                                        } else {
                                            $finalAverageRating = 'No ratings available';
                                        }


                                        $verbalInterpretation = '';

                                        switch (true) {
                                            case ($finalAverageRating >= 0 && $finalAverageRating < 1):
                                                $verbalInterpretation = 'None';
                                                break;
                                            case ($finalAverageRating >= 1 && $finalAverageRating < 2):
                                                $verbalInterpretation = 'Poor';
                                                break;
                                            case ($finalAverageRating >= 2 && $finalAverageRating < 3):
                                                $verbalInterpretation = 'Fair';
                                                break;
                                            case ($finalAverageRating >= 3 && $finalAverageRating < 4):
                                                $verbalInterpretation = 'Satisfactory';
                                                break;
                                            case ($finalAverageRating >= 4 && $finalAverageRating < 5):
                                                $verbalInterpretation = 'Very Satisfactory';
                                                break;
                                            case ($finalAverageRating == 5):
                                                $verbalInterpretation = 'Outstanding';
                                                break;
                                            default:
                                                $verbalInterpretation = 'Invalid Rating';
                                                break;
                                        }
                                    } else {
                                        echo '<tr><td colspan="2" class="text-center">No Categories Found</td></tr>';
                                    }


                                    echo '
                                            <tr>
                                            <td>' . $categoriesRow['categories'] . '</td>
                                            <td>' . $finalAverageRating . '</td>
                                            <td>' . $verbalInterpretation . '</td>
                                            </tr>
                                        
                                        ';
                                }
                            } else {
                                echo 'No Categories';
                            }

                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane fade " id="nav-profile" role="tabpanel" aria-labelledby="nav-profile-tab">

                <h3 class="text-danger fw-bold text-center">Feedbacks From Student and Classroom Observations</h3>

                <div class="d-flex justify-content-evenly my-3 mx-5">
                    <div class="sort">
                        <button id="sortButton" class="btn btn-primary">
                            Sort: Newest to Oldest
                        </button>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-success dropdown-toggle" type="button" id="dropdownMenuButton"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Select Stars
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <li><a class="dropdown-item" href="#" data-value="1">1 Star</a></li>
                            <li><a class="dropdown-item" href="#" data-value="2">2 Stars</a></li>
                            <li><a class="dropdown-item" href="#" data-value="3">3 Stars</a></li>
                            <li><a class="dropdown-item" href="#" data-value="4">4 Stars</a></li>
                            <li><a class="dropdown-item" href="#" data-value="5">5 Stars</a></li>
                        </ul>
                    </div>
                </div>
                <div class="overflow-auto" style="max-height: 520px">
                    <table class="table table-striped table-bordered text-center align-middle w-100">
                        <tbody>
                            <tr>
                                <td>
                                    <div class="d-flex justify-content-between mx-3">
                                        <h6 class="fw-bold">Anonymous</h6>
                                    </div>
                                    <div class="mx-5">
                                        <span>Lorem ipsum dolor sit amet consectetur adipisicing elit.
                                            Earum
                                            libero iusto
                                            dignissimos dolores at vel reprehenderit sed esse, doloribus
                                            blanditiis!</span>
                                    </div>
                                    <div class="d-flex justify-content-end mx-3">
                                        <span class="text-secondary">November 10, 2000</span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex justify-content-between mx-3">
                                        <h6 class="fw-bold">Anonymous</h6>
                                    </div>
                                    <div class="mx-5">
                                        <span>Lorem ipsum dolor sit amet consectetur adipisicing elit.
                                            Earum
                                            libero iusto
                                            dignissimos dolores at vel reprehenderit sed esse, doloribus
                                            blanditiis!</span>
                                    </div>
                                    <div class="d-flex justify-content-end mx-3">
                                        <span class="text-secondary">November 10, 2000</span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex justify-content-between mx-3">
                                        <h6 class="fw-bold">Anonymous</h6>
                                    </div>
                                    <div class="mx-5">
                                        <span>Lorem ipsum dolor sit amet consectetur adipisicing elit.
                                            Earum
                                            libero iusto
                                            dignissimos dolores at vel reprehenderit sed esse, doloribus
                                            blanditiis!</span>
                                    </div>
                                    <div class="d-flex justify-content-end mx-3">
                                        <span class="text-secondary">November 10, 2000</span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex justify-content-between mx-3">
                                        <h6 class="fw-bold">Anonymous</h6>
                                    </div>
                                    <div class="mx-5">
                                        <span>Lorem ipsum dolor sit amet consectetur adipisicing elit.
                                            Earum
                                            libero iusto
                                            dignissimos dolores at vel reprehenderit sed esse, doloribus
                                            blanditiis!</span>
                                    </div>
                                    <div class="d-flex justify-content-end mx-3">
                                        <span class="text-secondary">November 10, 2000</span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex justify-content-between mx-3">
                                        <h6 class="fw-bold">Anonymous</h6>
                                    </div>
                                    <div class="mx-5">
                                        <span>Lorem ipsum dolor sit amet consectetur adipisicing elit.
                                            Earum
                                            libero iusto
                                            dignissimos dolores at vel reprehenderit sed esse, doloribus
                                            blanditiis!</span>
                                    </div>
                                    <div class="d-flex justify-content-end mx-3">
                                        <span class="text-secondary">November 10, 2000</span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex justify-content-between mx-3">
                                        <h6 class="fw-bold">Anonymous</h6>
                                    </div>
                                    <div class="mx-5">
                                        <span>Lorem ipsum dolor sit amet consectetur adipisicing elit.
                                            Earum
                                            libero iusto
                                            dignissimos dolores at vel reprehenderit sed esse, doloribus
                                            blanditiis!</span>
                                    </div>
                                    <div class="d-flex justify-content-end mx-3">
                                        <span class="text-secondary">November 10, 2000</span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex justify-content-between mx-3">
                                        <h6 class="fw-bold">Anonymous</h6>
                                    </div>
                                    <div class="mx-5">
                                        <span>Lorem ipsum dolor sit amet consectetur adipisicing elit.
                                            Earum
                                            libero iusto
                                            dignissimos dolores at vel reprehenderit sed esse, doloribus
                                            blanditiis!</span>
                                    </div>
                                    <div class="d-flex justify-content-end mx-3">
                                        <span class="text-secondary">November 10, 2000</span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex justify-content-between mx-3">
                                        <h6 class="fw-bold">Anonymous</h6>
                                    </div>
                                    <div class="mx-5">
                                        <span>Lorem ipsum dolor sit amet consectetur adipisicing elit.
                                            Earum
                                            libero iusto
                                            dignissimos dolores at vel reprehenderit sed esse, doloribus
                                            blanditiis!</span>
                                    </div>
                                    <div class="d-flex justify-content-end mx-3">
                                        <span class="text-secondary">November 10, 2000</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</section>


<script>
    $(document).ready(function () {
        $('.dropdown-item').on('click', function (e) {
            e.preventDefault();
            const selectedValue = $(this).data('value');
            const displayText = `${selectedValue} Star${selectedValue > 1 ? 's' : ''}`;
            $('#dropdownMenuButton').text(displayText);
        });
    });
</script>
<?php
include "../../model/dbconnection.php";

if (isset($_POST['checking_viewbtn'])) {
    $o_id = $_POST['official_id'];

    $query = "SELECT * FROM peertopeerform WHERE id='$o_id'";
    $query_run = mysqli_query($con, $query);

    if ($query_run->num_rows > 0) {
        while ($ratingRow = $query_run->fetch_assoc()) {
            ?>

            <div id="143" class="printDiv" hidden>
                <style>
                    .printDiv {
                        font-size: 13px;
                    }

                    table {
                        width: 100%;
                        border-collapse: collapse;

                    }

                    thead tr {
                        text-align: center;
                    }

                    td,
                    th {
                        border: 1px solid #ccc;
                        padding: 10px;
                    }

                    td.wide-column {
                        min-width: 100px;
                        max-width: 50px;
                    }

                    .container {
                        display: flex;
                        flex-wrap: wrap;
                        width: 100%;
                        height: 100%;
                        align-items: center;
                        justify-content: center;

                    }

                    .part {
                        flex: 1 1 16.66%;
                        text-align: center;
                        box-sizing: border-box;
                        vertical-align: middle;
                        font-size: 14px;
                        padding: 0;
                        margin: 0;
                    }
                </style>
                <table>
                    <thead>
                        <tr>
                            <td><img src="../../public/picture/bsu.png" style="height: 50px;" srcset></td>
                            <td>Reference No.: </td>
                            <td>Effectivity Date: March 15, 2024</td>
                            <td style="min-width: 105px">Revision No.:</td>
                        </tr>
                        <tr>
                            <td colspan="4" style="font-size: 18px; font-weight: bold; padding: 2px;">FACULTY PEER TO PEER
                                EVALUATION INSTRUMENT FOR FACULTY DEVELOPMENT
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding: 0 5px;">
                                <div class="text-justify" style="text-align: justify;">
                                    <span style="margin-right: 5px;">Faculty Name</span><span
                                        style="font-weight: bold;"><?php echo $ratingRow['toFaculty']; ?></span>
                                </div>
                            </td>
                            <td colspan="1" style="padding: 0 10px;">
                                <div class="text-justify" style="text-align: justify;">
                                    <span style="margin-right: 20px;">Semester</span><span
                                        style="font-weight: bold;"><?php echo $ratingRow['semester']; ?></span>
                                </div>
                            </td>
                            <td colspan="1" style="padding: 0 10px;">
                                <div class="text-left" style="text-align: left;">
                                    <span style="margin-right: 10px;">Academic Year</span><span
                                        style="font-weight: bold;"><?php echo $ratingRow['academic_year']; ?></span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="padding: 0 10px;">
                                <div class="text-justify" style="text-align: justify;">
                                    <span style="margin-right: 20px;">Academic Rank</span>
                                    <div style="display: flex; justify-content: space-evenly; flex-direction: row; width: 100%;">
                                        <div>Professor</div>
                                        <div>Associate Professor</div>
                                        <div>Assistant Professor</div>
                                        <div>Instructor</div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="padding: 0 10px;">
                                <div class="text-justify" style="text-align: justify;">
                                    <span style="margin-right: 20px; font-size: 16px;">
                                        Instruction: Please evaluate the faculty member involved by encircling the number that
                                        corresponds to the given parameter/dimensions at the scale of 5, where
                                        <span class="fw-bold">five is the perfect score</span> and
                                        <span class="fw-bold">one is the lowest score.</span>
                                    </span>

                                </div>
                            </td>
                        </tr>
                    </thead>
                </table>
                <table style="padding: 0;">
                    <tr>
                        <td style="padding: 0; text-align: center;">Numerical Rating</td>
                        <td style="padding: 0; text-align: center;">Descriptive Rating Rating</td>
                        <td style="padding: 0; text-align: center;">Qualitative Description Rating</td>
                    </tr>
                    <tr>
                        <td style="padding: 0; text-align: center;">5.0</td>
                        <td style="padding: 0; text-align: center;">Outstanding</td>
                        <td style="padding: 0;">Exhibits the behavior described at all times when the occasion occurs.</td>
                    </tr>
                    <tr>
                        <td style="padding: 0; text-align: center;">4.0</td>
                        <td style="padding: 0; text-align: center;">Very Satisfactory</td>
                        <td style="padding: 0;">Exhibits the behavior described most of the time when the occasion occurs.</td>
                    </tr>
                    <tr>
                        <td style="padding: 0; text-align: center;">3.0</td>
                        <td style="padding: 0; text-align: center;">Satisfactory</td>
                        <td style="padding: 0;">Exhibits the behavior described sometimes when the occasion occurs.</td>
                    </tr>
                    <tr>
                        <td style="padding: 0; text-align: center;">2.0</td>
                        <td style="padding: 0; text-align: center;">Fair</td>
                        <td style="padding: 0;">Exhibits the behavior described rarely when the occasion occurs.</td>
                    </tr>
                    <tr>
                        <td style="padding: 0; text-align: center;">1.0</td>
                        <td style="padding: 0; text-align: center;">Poor</td>
                        <td style="padding: 0;">Exhibits the behavior described has not been exhibited at all times when the
                            occasion occurs.</td>
                    </tr>
                </table>

                <?php
                $sql = "SELECT * FROM `facultycategories`";
                $sql_query = mysqli_query($con, $sql);

                function sanitizeColumnName($name)
                {
                    return preg_replace('/[^a-zA-Z0-9_]/', '', trim($name));
                }

                $criteriaCount = 1;
                $averageRatings = [];
                $totalAverage = 0;
                $categoryCount = 0; // Track the number of categories
                $firstCategory = true; // Flag to check if it's the first category
    
                if (mysqli_num_rows($sql_query)) {
                    while ($categoryRow = mysqli_fetch_assoc($sql_query)) {
                        $categories = $categoryRow['categories'];
                        echo '
            <table class="table table-striped table-bordered text-center align-middle">
                <thead>
                    <tr>
                        <th colspan="2" class="text-center" style="padding: 0 10px;">' . $categories . '</th>
                        <th colspan="5" class="text-center" style="padding: 0 10px;"></th>
        ';

                        // Only show APS header for the first category
                        if ($firstCategory) {
                            echo '<th class="text-center" style="padding: 0 10px;">AVERAGE POINT SCORE (APS)</th>';
                            $firstCategory = false; // Set flag to false after the first category
                        } else {
                            echo '<th class="text-center" style="padding: 0 50px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>'; // Empty header for subsequent categories
                        }

                        echo '
                    </tr>
                </thead>
                <tbody>
        ';

                        $sqlcriteria = "SELECT * FROM `facultycriteria` WHERE facultyCategories = '$categories'";
                        $resultCriteria = mysqli_query($con, $sqlcriteria);

                        if (mysqli_num_rows($resultCriteria) > 0) {
                            $totalRatings = [0, 0, 0, 0, 0];
                            $ratingCount = 0;

                            while ($criteriaRow = mysqli_fetch_array($resultCriteria)) {
                                $columnName = sanitizeColumnName($criteriaRow['facultyCategories']);
                                $finalColumnName = $columnName . $criteriaRow['id'];

                                $criteriaRating = $ratingRow[$finalColumnName] ?? null;

                                // Handle ratings and output for the table
                                $output = ''; // Initialize an empty variable to store the output
    
                                switch ($criteriaRating) {
                                    case '':
                                    case 1:
                                        $output = '1';
                                        break;
                                    case 2:
                                        $output = '2';
                                        break;
                                    case 3:
                                        $output = '3';
                                        break;
                                    case 4:
                                        $output = '4';
                                        break;
                                    case 5:
                                        $output = '5';
                                        break;
                                    default:
                                        $output = ''; // If there's no match, output an empty string
                                        break;
                                }

                                echo '
                <tr>
                    <td style="padding: 0 10px;">' . $criteriaCount++ . '.</td>
                    <td class="text-justify" style="padding: 0 10px;">' . $criteriaRow['facultyCriteria'] . '</td>
                    <td class="text-center" style="padding: 0 10px;">5</td>
                    <td class="text-center" style="padding: 0 10px;">4</td>
                    <td class="text-center" style="padding: 0 10px;">3</td>
                    <td class="text-center" style="padding: 0 10px;">2</td>
                    <td class="text-center" style="padding: 0 10px;">1</td>
                    <td style="text-align: center;">' . $output . '</td>
                </tr>
                ';

                                if ($criteriaRating !== null) {
                                    $totalRatings[$criteriaRating - 1]++;
                                    $ratingCount++;
                                }
                            }

                            // Calculate the average rating for this category
                            $averageRating = 0;
                            if ($ratingCount > 0) {
                                for ($i = 0; $i < 5; $i++) {
                                    $averageRating += ($i + 1) * $totalRatings[$i];
                                }
                                $averageRating /= $ratingCount;
                                $totalAverage += $averageRating; // Add to total average
                                $categoryCount++; // Increment the category count for overall average
                            }
                        } else {
                            echo '<tr><td colspan="8" class="text-center">NO CRITERIA</td></tr>';
                        }

                        echo '</tbody></table>'; // Close the table for this category
                    }

                    // Calculate final average rating for all categories
                    $finalAverageRating = $categoryCount > 0 ? round($totalAverage / $categoryCount, 2) : 'No ratings available';

                    // Display the total average rating in a separate table
                    echo '
    <table class="table">
        <thead>
            <tr>
                <th style="text-align: right; min-width: 165px">AVERAGE</th>
                <th style="font-weight: bold; ">' . $finalAverageRating . '</th>
            </tr>
        </thead>
        <tbody>
        <tr>
        <td colspan="2"><span style="font-weight: bold; ">Comment : </span> </br><span style="margin: 0 20px; text-align:justify;">' . $ratingRow['commentText'] . '</span></td>
        </tr>
                <tr>
        <td colspan="2"><span>Note:  </span> </br><span style="font-size: 15px; margin: 0 20px; text-align:justify;">Adopted from: Lerchenfeldt, S., & Ah, T. (2020). Best Practices in Peer Assessment: Training Tomorrow’s Physicians to Obtain and Provide Quality Feedback. <text>https://doi.org/10.2147/AMEP.S25076</text></span></td>
        </tr></tbody>
    </table>';
                } else {
                    echo '<tr><td colspan="2" class="text-center">No Categories Found</td></tr>';
                }

                echo '<script>';
                echo 'const averageRatings = ' . json_encode($averageRatings) . ';';
                echo '</script>';
        }
    }
}
?>
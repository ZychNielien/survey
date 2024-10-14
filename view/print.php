<?php
include "../model/dbconnection.php";

function sanitizeColumnName($name)
{
    return preg_replace('/[^a-zA-Z0-9_]/', '', trim($name));
}

if (isset($_POST['checking_viewbtn'])) {


    $o_id = mysqli_real_escape_string($con, $_POST['official_id']);
    $query = "SELECT * FROM studentsform WHERE id = '$o_id'";

    $query_run = mysqli_query($con, $query);

    if ($query_run && mysqli_num_rows($query_run) > 0) {
        while ($ratingRow = mysqli_fetch_assoc($query_run)) {
            ?>

            <div id="143" class="printDiv" hiden>
                <style>
                    .printDiv {
                        font-size: 13px;
                    }

                    table {
                        width: 100%;
                        border-collapse: collapse;
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
                        align-items: center;
                        justify-content: center;
                        width: 100%;
                        height: 100%;
                    }

                    .part {
                        flex: 1 1 16.66%;
                        text-align: center;
                        box-sizing: border-box;
                        font-size: 14px;
                        padding: 0;
                        margin: 0;
                    }
                </style>

                <table>
                    <thead>
                        <tr style="text-align: center;">
                            <td><img src="../public/picture/bsu.png" style="height: 50px;" alt="Logo"></td>
                            <td>Reference No.: </td>
                            <td>Effectivity Date: March 15, 2024</td>
                            <td style="min-width: 105px">Revision No.:</td>
                        </tr>
                        <tr>
                            <td colspan="4" style="font-size: 18px; font-weight: bold; padding: 2px; text-align: center;">FACULTY
                                PEER TO PEER
                                EVALUATION INSTRUMENT FOR FACULTY DEVELOPMENT</td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding: 0 5px;">
                                <div style="text-align: justify;">
                                    <span>Faculty Name: </span><span
                                        style="font-weight: bold;"><?php echo htmlspecialchars($ratingRow['toFaculty']); ?></span>
                                </div>
                            </td>
                            <td colspan="1" style="padding: 0 10px;">
                                <div style="text-align: justify;">
                                    <span>Semester: </span><span
                                        style="font-weight: bold;"><?php echo htmlspecialchars($ratingRow['semester']); ?></span>
                                </div>
                            </td>
                            <td colspan="1" style="padding: 0 10px;">
                                <div style="text-align: left;">
                                    <span>Academic Year: </span><span
                                        style="font-weight: bold;"><?php echo htmlspecialchars($ratingRow['academic_year']); ?></span>
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
                $sql = "SELECT * FROM `studentscategories`";
                $sql_query = mysqli_query($con, $sql);

                $criteriaCount = 1;
                $totalAverage = 0;
                $categoryCount = 0;
                $firstCategory = true;
                if ($sql_query && mysqli_num_rows($sql_query) > 0) {
                    while ($categoryRow = mysqli_fetch_assoc($sql_query)) {
                        $categories = htmlspecialchars($categoryRow['categories']);
                        echo '
                        <table class="table table-striped table-bordered text-center align-middle">
                            <thead>
                                <tr>
                                    <th colspan="2" class="text-center" style="padding: 0 10px;">' . $categories . '</th>
                                    <th colspan="5" class="text-center" style="padding: 0 10px;"></th>
                    ';

                        if ($firstCategory) {
                            echo '<th class="text-center" style="padding: 0 10px;">AVERAGE POINT SCORE (APS)</th>';
                            $firstCategory = false;
                        } else {
                            echo '<th class="text-center" style="padding: 0 60px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th>';
                        }

                        echo '
                            </thead>
                            <tbody>';

                        $sqlcriteria = "SELECT * FROM `studentscriteria` WHERE studentsCategories = '$categories'";
                        $resultCriteria = mysqli_query($con, $sqlcriteria);

                        if ($resultCriteria && mysqli_num_rows($resultCriteria) > 0) {
                            $totalRatings = [0, 0, 0, 0, 0];
                            $ratingCount = 0;

                            while ($criteriaRow = mysqli_fetch_array($resultCriteria)) {
                                $columnName = sanitizeColumnName($criteriaRow['studentsCategories']);
                                $finalColumnName = $columnName . $criteriaRow['id'];

                                $criteriaRating = $ratingRow[$finalColumnName] ?? null;
                                $output = $criteriaRating ? $criteriaRating : 'N/A';

                                echo '
                                <tr >
                                    <td>' . $criteriaCount++ . '.</td>
                                    <td>' . htmlspecialchars($criteriaRow['studentsCriteria']) . '</td>
                                    <td class="text-center">5</td>
                                    <td class="text-center">4</td>
                                    <td class="text-center">3</td>
                                    <td class="text-center">2</td>
                                    <td class="text-center">1</td>
                                    <td style="text-align: center;">' . $output . '</td>
                                </tr>';

                                if ($criteriaRating) {
                                    $totalRatings[$criteriaRating - 1]++;
                                    $ratingCount++;
                                }
                            }

                            $averageRating = array_sum(array_map(function ($count, $rating) {
                                return $count * ($rating + 1);
                            }, $totalRatings, array_keys($totalRatings))) / max($ratingCount, 1);

                            $totalAverage += $averageRating;
                            $categoryCount++;
                        } else {
                            echo '<tr><td colspan="8" class="text-center">NO CRITERIA</td></tr>';
                        }

                        echo '</tbody></table>';
                    }

                    $finalAverageRating = $categoryCount > 0 ? round($totalAverage / $categoryCount, 2) : 'No ratings available';
                    echo '
                    <table class="table">
                        <thead>
                            <tr>
                                <th style="text-align: right; min-width: 165px">AVERAGE</th>
                                <th style="font-weight: bold;">' . $finalAverageRating . '</th>
                            </tr>
                        </thead>
                    
                    </table>';
                } else {
                    echo '<tr><td colspan="2" class="text-center">No Categories Found</td></tr>';
                }

                echo '<script>';
                echo 'const averageRatings = ' . json_encode($averageRatings) . ';';
                echo '</script>';
                ?>
                <table style="margin-top:50px; text-align: center">
                    <thead>
                        <tr>
                            <th colspan="3">
                                TOPIC EVALUATION COMMITTEE <br>
                                The topic abstract has been thoroughly reviewed by the Topic Evaluation Committee.
                            </th>
                        </tr>
                        <tr>
                            <th></th>
                            <th>Signature</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><span style="font-weight: bold">Assoc. Prof. ROSANA C. LAT </span><br>
                                Vice Chancellor for Research,<br>
                                Development and Extension Services
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td> <span style="font-weight: bold">Asst. Prof. SHIELA MARIE G. GARCIA</span><br>
                                Dean, CICS

                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><span style="font-weight: bold">Mr. JOHNREY N. MANZANAL</span><br>
                                Associate Dean, CICS
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td><span style="font-weight: bold">Ms. DONNA M. GARCIA</span> <br>
                                Faculty Expert/Adviser
                            </td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <div class="">
                    <span style="float: right;"><i>Tracking No. ____________</i>
                    </span>
                </div>

            </div>
            <?php

        }
    }
}
?>
<?php
include "../../model/dbconnection.php";

if (isset($_POST['checking_viewbtn'])) {
    $o_id = $_POST['official_id'];

    $query = "SELECT * FROM classroomobservation WHERE id='$o_id'";
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
                            <td>Reference No.: BatStateU-FO-COL-10</td>
                            <td>Effectivity Date: November 13, 2023</td>
                            <td>Revision No.: 02</td>
                        </tr>
                        <tr>
                            <td colspan="4" style="font-size: 18px; font-weight: bold; padding: 2px;">CLASSROOM OBSERVATION FORM
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding: 0 10px;">
                                <div class="text-justify" style="text-align: justify;">
                                    <span style="margin-right: 20px;">Course Title</span><span
                                        style="font-weight: bold;"><?php echo $ratingRow['courseTitle']; ?></span>
                                </div>
                            </td>
                            <td colspan="2" style="padding: 0 10px;">
                                <div class="text-justify" style="text-align: justify;">
                                    <span style="margin-right: 20px;">Instructor</span><span
                                        style="font-weight: bold;"><?php echo $ratingRow['toFaculty']; ?></span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding: 0 10px;">
                                <div class="text-justify" style="text-align: justify;">
                                    <span style="margin-right: 20px;">Length of Course</span><span
                                        style="font-weight: bold;"><?php echo $ratingRow['lengthOfCourse']; ?></span>
                                </div>
                            </td>
                            <td colspan="2" style="padding: 0 10px;">
                                <div class="text-justify" style="text-align: justify;">
                                    <span style="margin-right: 20px;">Length of Observation</span><span
                                        style="font-weight: bold;"><?php echo $ratingRow['lengthOfObservation']; ?></span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding: 0 10px;">
                                <div class="text-justify" style="text-align: justify;">
                                    <span style="margin-right: 20px;">Observer</span><span
                                        style="font-weight: bold;"><?php echo $ratingRow['fromFaculty']; ?></span>
                                </div>
                            </td>
                            <td colspan="2" style="padding: 0 10px;">
                                <div class="text-justify" style="text-align: justify;">
                                    <span style="margin-right: 20px;">Date:</span><span
                                        style="font-weight: bold;"><?php echo $ratingRow['date']; ?></span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="padding: 0 10px;">
                                <div class="text-justify" style="text-align: justify;">
                                    <span style="margin-right: 20px;">Subject Matter Treated in Lesson</span><span
                                        style="font-weight: bold;"><?php echo $ratingRow['subjectMatter']; ?></span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="padding: 0 10px;">
                                <div class="text-justify" style="text-align: justify;">
                                    <span style="margin-right: 20px; font-size: 16px;">Directions: Below is the list of Instructor
                                        behaviors indicative of effective teaching that may occur within a given class or course.
                                        Please use it as a guide to making observations, not as a list of required characteristics.
                                        Use the rating scale below to measure the level of quality the instructor demonstrates the
                                        following behaviors.</span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <div class="container" style="font-weight: bold;" style="padding: 0 ; margin: 0;">
                                    <div class=" part" style="padding: 0 ;">Outstanding <br> 5</div>
                                    <div class="part" style="padding: 0;">Very Satisfactory <br> 4</div>
                                    <div class="part" style="padding: 0;">Satisfactory <br> 3</div>
                                    <div class="part" style="padding: 0;">Unsatisfactory <br> 2</div>
                                    <div class="part" style="padding: 0;">Poor <br> 1</div>
                                    <div class="part" style="padding: 0;">Not Applicable</div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4">
                                <div class="text-justify" style="text-align: justify; padding: 0;">
                                    <span style="margin-right: 20px; padding: 0;">Use NA if an item is not relevant for the
                                        class or for this instructor.</span>
                                </div>
                            </td>
                        </tr>
                    </thead>
                </table>

                <?php
                $sql = "SELECT * FROM `classroomcategories`";
                $sql_query = mysqli_query($con, $sql);

                function sanitizeColumnName($name)
                {
                    return preg_replace('/[^a-zA-Z0-9_]/', '', trim($name));
                }
                $criteriaCount = 1;
                $averageRatings = [];
                if (mysqli_num_rows($sql_query)) {
                    while ($categoryRow = mysqli_fetch_assoc($sql_query)) {
                        $categories = $categoryRow['categories'];
                        echo '
                            <table class="table table-striped table-bordered text-center align-middle">
                                <thead>
                                    <tr >
                                        <th colspan="2" class="text-center" style="padding: 0 10px;">' . $categoryRow['categories'] . '</th>
                                        <th class="text-center"  style="padding: 0 10px;">5</th>
                                        <th class="text-center" style="padding: 0 10px;">4</th>
                                        <th class="text-center" style="padding: 0 10px;">3</th>
                                        <th class="text-center" style="padding: 0 10px;">2</th>
                                        <th class="text-center" style="padding: 0 10px;">1</th>
                                        <th class="text-center" style="padding: 0 10px;">NA</th>
  
                                    </tr>
                                </thead>
                                <tbody>
                    ';
                        $sqlcriteria = "SELECT * FROM `classroomcriteria` WHERE classroomCategories = '$categories'";
                        $resultCriteria = mysqli_query($con, $sqlcriteria);

                        if (mysqli_num_rows($resultCriteria) > 0) {

                            $totalRatings = [0, 0, 0, 0, 0];
                            $ratingCount = 0;

                            while ($criteriaRow = mysqli_fetch_array($resultCriteria)) {

                                $columnName = sanitizeColumnName($criteriaRow['classroomCategories']);
                                $finalColumnName = $columnName . $criteriaRow['id'];



                                $commentColumn = sanitizeColumnName($categoryRow['categories']) . $categoryRow['id'];

                                $finalColumnComment = 'comment' . $commentColumn;

                                $criteriaRating = $ratingRow[$finalColumnName] ?? null;

                                echo '
                                <tr>
                                    <td style="padding: 0 10px;">' . $criteriaCount++ . '.</td>
                                    <td class="text-justify" style="padding: 0 10px;">' . $criteriaRow['classroomCriteria'] . '</td>
                                    <td class="text-center" style="padding: 0 10px;">' . ($criteriaRating == 5 ? '✔️' : '') . '</td>
                                    <td class="text-center" style="padding: 0 10px;">' . ($criteriaRating == 4 ? '✔️' : '') . '</td>
                                    <td class="text-center" style="padding: 0 10px;">' . ($criteriaRating == 3 ? '✔️' : '') . '</td>
                                    <td class="text-center" style="padding: 0 10px;">' . ($criteriaRating == 2 ? '✔️' : '') . '</td>
                                    <td class="text-center" style="padding: 0 10px;">' . ($criteriaRating == 1 ? '✔️' : '') . '</td>
                                    <td class="text-center" style="padding: 0 10px;">' . ($criteriaRating == '' ? '✔️' : '') . '</td>
                                </tr>
                            ';

                                if ($criteriaRating !== null) {
                                    $totalRatings[$criteriaRating - 1]++;
                                    $ratingCount++;
                                }
                            }
                        } else {
                            echo 'NO CRITERIA';
                        }
                        $averageRating = 0;
                        if ($ratingCount > 0) {
                            for ($i = 0; $i < 5; $i++) {
                                $averageRating += ($i + 1) * $totalRatings[$i];
                            }
                            $averageRating /= $ratingCount;
                        }

                        $averageRatings[$categories] = round($averageRating, 2);

                        echo '
                        <tr>
                            <td  colspan="7" style="text-align: right;padding: 0 10px; font-weight: bold;">Average</td>
                            <td  colspan="1" style="padding: 0 10px; font-weight: bold;">' . round($averageRating, 2) . '</td>
                        </tr>
                        <tr>
                            <td class="text-left " colspan="8" >
                                <div style="min-height: 45px; ">
                                    <span style="font-weight: bold;">Comments: </span>
                                    <span style="text-indent: 50px; "> ' . $ratingRow[$finalColumnComment] . '</span>                  
                                </div>
                           </td>
                        </tr>
                   </tbody>
            </table>
              
                    
                    ';
                    }
                }
                echo '<script>';
                echo 'const averageRatings = ' . json_encode($averageRatings) . ';';
                echo '</script>';
                ?>
                <table class="table table-striped table-bordered text-center align-middle">
                    <tbody>

                        <?php
                        $classroomQuestion = "SELECT * FROM `classroomquestions`";
                        $classroomQuestion_query = mysqli_query($con, $classroomQuestion);

                        if (mysqli_num_rows($classroomQuestion_query) > 0) {
                            $questionCount = 1;
                            while ($questionsRow = mysqli_fetch_assoc($classroomQuestion_query)) {
                                $questionName = sanitizeColumnName('QUESTIONNO') . $questionsRow['id'];

                                echo '
                                <tr style="text-align: justify;">
                                    <td class="text-left " colspan="8" >
                                        <div style="min-height: 110px; ">
                                            <span style="font-weight: bold;">' . $criteriaCount++ . '. ' . $questionsRow['questions'] . '</span><br>
                                            <span style="margin: 0 50px;"> ' . $ratingRow[$questionName] . '</span>
                                        </div>

                                    </td>
                                </tr>
                            ';
                            }
                        }
                        ?>
                    </tbody>
                </table>
                <table>
                    <tr>
                        <td><span>Note:</span> <br>
                            <div class="text-justify mx-5">
                                <span> Adopted from: Centra, John A., Reflective Faculty Evaluation:
                                    Enhancing Teaching and Determining
                                    Faculty Effectiveness, Jossey-Bass Publishers, San Francisco (1993)</span>
                            </div>

                        </td>
                    </tr>
                </table>
                <div style="display: flex; justify-content: center; margin: 20px 30px;">
                    <table>
                        <thead>
                            <tr>
                                <td colspan="2" style="font-weight: bold; padding: 0 10px;">Summary of Rating</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center w-50" style="font-weight: bold; padding: 0 10px; text-align: center">
                                    Component</td>
                                <td class="text-center w-50" style="font-weight: bold; padding: 0 10px; text-align: center">Average
                                </td>
                            </tr>
                            <?php
                            $sql = "SELECT * FROM `classroomcategories`";
                            $sql_query = mysqli_query($con, $sql);

                            $averageRatings = [];
                            $totalAverage = 0;
                            $categoryCount = 0;

                            if (mysqli_num_rows($sql_query)) {
                                while ($categoryRow = mysqli_fetch_assoc($sql_query)) {
                                    $categories = $categoryRow['categories'];

                                    $totalRatings = [0, 0, 0, 0, 0];
                                    $ratingCount = 0;

                                    $sqlcriteria = "SELECT * FROM `classroomcriteria` WHERE classroomCategories = '$categories'";
                                    $resultCriteria = mysqli_query($con, $sqlcriteria);

                                    if (mysqli_num_rows($resultCriteria) > 0) {
                                        while ($criteriaRow = mysqli_fetch_array($resultCriteria)) {
                                            $columnName = sanitizeColumnName($criteriaRow['classroomCategories']);
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
                                    }

                                    echo '
                <tr>
                    <td style="text-transform: capitalize; padding: 0 10px; " >' . $categoryRow['categories'] . '</td>
                    <td class="text-center" style="padding: 0 10px; text-align: center">' . (isset($averageRatings[$categories]) ? $averageRatings[$categories] : 'N/A') . '</td>
                </tr>
            ';
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
                            ?>

                            <tr>
                                <td style="font-weight: bold; text-transform: uppercase; padding: 0 10px;">Final Rating</td>
                                <td class="text-center" style="font-weight: bold; padding: 0 10px; text-align: center"
                                    id="finalRating">
                                    <?php echo $finalAverageRating; ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold;  text-transform: uppercase; padding: 0 10px;">Verbal
                                    Interpretation</td>
                                <td class="text-center" style="font-weight: bold; padding: 0 10px; text-align: center">
                                    <?php echo $verbalInterpretation; ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <style>
                    .raterFaculty {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        text-align: center;
                        margin-top: 70px;
                    }

                    .raterFaculty .fixedText {
                        font-weight: bold;
                    }

                    .phpRow {
                        text-transform: uppercase;
                    }
                </style>
                <div class="raterFaculty">
                    <div class="phpData">
                        <span
                            class="phpRow"><u>&nbsp;&nbsp;&nbsp;<?php echo $ratingRow['fromFaculty']; ?>&nbsp;&nbsp;&nbsp;</u></span><br>
                        <span class="fixedText">Signature over Printed Name of
                            Rater/Supervisor</span>
                    </div>
                    <div class="phpData">
                        <span><u><?php echo $ratingRow['date']; ?></u></span><br>
                        <span class="fixedText">Date</span>
                    </div>
                </div>


                <div class="d-flex justify-content-start mx-5" style="margin: 60px 0">
                    <div>
                        <div class="underscore text-center">___________________</div>
                        <div class="text-center" style="font-weight: bold; "><span>Designation/Position</span></div>
                    </div>
                </div>

                <div class=" mx-5" style="margin: 60px 0">
                    <div>
                        <div class="text-left" style="font-weight: bold; "><span>Conforme:</span></div>
                    </div>
                </div>

                <div class="raterFaculty">
                    <div class="phpData">
                        <span
                            class="phpRow"><u>&nbsp;&nbsp;&nbsp;<?php echo $ratingRow['toFaculty']; ?>&nbsp;&nbsp;&nbsp;</u></span><br>
                        <span class="fixedText">Signature over Printed Name of Ratee/Faculty</span>
                    </div>
                    <div class="phpData">
                        <span><u><?php echo $ratingRow['date']; ?></u></span><br>
                        <span class="fixedText">Date</span>
                    </div>
                </div>


                <div style="text-align:center; margin-top: 40px;font-style: italic;">
                    <span class="font-italic">* Final Rating shall be computed by adding the average ratings of
                        teaching
                        practices divided by
                        five(5).</span>
                </div>


                <?php
        }
    }
}

?>
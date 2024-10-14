<?php

include "../../model/dbconnection.php";

session_start();

$userId = $_SESSION["userid"];
$usersql = "SELECT * FROM `instructor` WHERE faculty_Id = '$userId'";
$usersql_query = mysqli_query($con, $usersql);
$userRow = mysqli_fetch_assoc($usersql_query);

$FacultyID = $userRow['faculty_Id'];

// FUNCTION FOR VERBAL INTERPRETATION
function getVerbalInterpretationAndLinks($averageRating, $linkOne, $linkTwo, $linkThree)
{
    $result = [
        'interpretation' => '',
        'links' => []
    ];

    if ($averageRating >= 0 && $averageRating < 1) {
        $result['interpretation'] = 'None';
    } elseif ($averageRating >= 1 && $averageRating < 2) {
        $result['interpretation'] = 'Poor';
    } elseif ($averageRating >= 2 && $averageRating < 3) {
        $result['interpretation'] = 'Fair';
    } elseif ($averageRating >= 3 && $averageRating < 4) {
        $result['interpretation'] = 'Satisfactory';
    } elseif ($averageRating >= 4 && $averageRating < 5) {
        $result['interpretation'] = 'Very Satisfactory';
    } elseif ($averageRating == 5) {
        $result['interpretation'] = 'Outstanding';
    } else {
        $result['interpretation'] = 'No description';
    }

    if ($averageRating < 2) {
        if (!empty($linkOne)) {
            $result['links'][] = [
                'text' => 'Link One',
                'url' => htmlspecialchars($linkOne)
            ];
        }
        if (!empty($linkTwo)) {
            $result['links'][] = [
                'text' => 'Link Two',
                'url' => htmlspecialchars($linkTwo)
            ];
        }
    }

    if (empty($result['links'])) {
        $result['links'] = [['text' => 'No links available for this subject', 'url' => '']];
    }

    return $result;
}

// FUNCTION FOR REMOVING UNDESIRABLE CHARACTERS
function sanitizeColumnName($name)
{
    return preg_replace('/[^a-zA-Z0-9_]/', '', trim($name));
}

$selectedSemester = isset($_POST['semester']) ? $_POST['semester'] : '';
$selectedAcademicYear = isset($_POST['academic_year']) ? $_POST['academic_year'] : '';

$sqlSubject = "
    SELECT DISTINCT 
        s.subject, 
        sf.semester, 
        sf.academic_year, 
        s.linkOne, 
        s.linkTwo, 
        s.linkThree
    FROM instructor i
    JOIN assigned_subject a ON i.faculty_Id = a.faculty_Id
    JOIN subject s ON a.subject_id = s.subject_id
    JOIN studentsform sf ON sf.toFacultyID = a.faculty_Id
    WHERE i.faculty_Id = '$FacultyID'
";

if (!empty($selectedAcademicYear)) {
    $sqlSubject .= " AND sf.academic_year = '$selectedAcademicYear'";
}
if (!empty($selectedSemester)) {
    $sqlSubject .= " AND sf.semester = '$selectedSemester'";
}

$sqlSubject .= " GROUP BY s.subject, sf.semester, sf.academic_year ORDER BY sf.semester, sf.academic_year DESC";

$sqlSubject_query = mysqli_query($con, $sqlSubject);
if (mysqli_num_rows($sqlSubject_query) > 0) {
    while ($subject = mysqli_fetch_assoc($sqlSubject_query)) {
        $facultyID = $userRow['faculty_Id'];
        $totalAverage = 0;
        $categoryCount = 0;

        // Check if there are ratings available for this subject
        $SQLFaculty = "SELECT * FROM `studentsform` WHERE toFacultyID = '$facultyID' 
                       AND subject = '" . htmlspecialchars($subject['subject']) . "' 
                       AND semester = '" . htmlspecialchars($subject['semester']) . "' 
                       AND academic_year = '" . htmlspecialchars($subject['academic_year']) . "'";

        $SQLFaculty_query = mysqli_query($con, $SQLFaculty);

        // Flag to check if we found any ratings
        $ratingsAvailable = false;

        if (mysqli_num_rows($SQLFaculty_query) > 0) {
            $ratingsAvailable = true; // Set flag if ratings are available
        } else {
            // Skip to the next subject if no ratings are available
            continue; // Use continue here to skip the rest of this iteration
        }

        // Output subject details
        ?>
        <div class="d-flex justify-content-between">
            <h5><?php echo htmlspecialchars($subject['subject']) ?></h5>
            <h5>(Semester:
                <?php echo htmlspecialchars($subject['semester']) ?>,
                Academic Year :
                <?php echo htmlspecialchars($subject['academic_year']) ?> )
            </h5>
        </div>

        <table class="table table-striped table-bordered text-center align-middle mb-5">
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
                $sql = "SELECT * FROM `studentscategories`";
                $sql_query = mysqli_query($con, $sql);

                if (mysqli_num_rows($sql_query) > 0) {
                    while ($categoriesRow = mysqli_fetch_assoc($sql_query)) {
                        $categories = $categoriesRow['categories'];

                        $totalRatings = [0, 0, 0, 0, 0];
                        $ratingCount = 0;

                        $sqlcriteria = "SELECT * FROM `studentscriteria` WHERE studentsCategories = '$categories'";
                        $resultCriteria = mysqli_query($con, $sqlcriteria);

                        if (mysqli_num_rows($resultCriteria) > 0) {
                            $selectedSubject = $subject['subject'];
                            $selectedSemester = $subject['semester'];
                            $selectedAcademicYear = $subject['academic_year'];

                            while ($ratingRow = mysqli_fetch_assoc($SQLFaculty_query)) {
                                while ($criteriaRow = mysqli_fetch_assoc($resultCriteria)) {
                                    $columnName = sanitizeColumnName($criteriaRow['studentsCategories']);
                                    $finalColumnName = $columnName . $criteriaRow['id'];

                                    $criteriaRating = $ratingRow[$finalColumnName] ?? null;

                                    if ($criteriaRating !== null && $criteriaRating >= 1 && $criteriaRating <= 5) {
                                        $totalRatings[$criteriaRating - 1]++;
                                        $ratingCount++;
                                    }
                                }

                                mysqli_data_seek($resultCriteria, 0);
                            }

                            $averageRating = 0;
                            if ($ratingCount > 0) {
                                for ($i = 0; $i < 5; $i++) {
                                    $averageRating += ($i + 1) * $totalRatings[$i];
                                }
                                $averageRating /= $ratingCount;

                                $totalAverage += $averageRating;
                                $categoryCount++;

                                $linkOne = $subject['linkOne'];
                                $linkTwo = $subject['linkTwo'];
                                $linkThree = $subject['linkThree'];
                                $interpretationData = getVerbalInterpretationAndLinks($averageRating, $linkOne, $linkTwo, $linkThree);
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($categories); ?></td>
                                    <td><?php echo number_format((float) $averageRating, 2, '.', ''); ?></td>
                                    <td><?php echo htmlspecialchars($interpretationData['interpretation']); ?></td>
                                    <td>
                                        <?php
                                        if ($averageRating < 2) {
                                            if (is_array($interpretationData['links'])) {
                                                echo "<ul style='list-style: none; padding: 0; margin: 0;'>";
                                                foreach ($interpretationData['links'] as $link) {
                                                    if (!empty($link['url'])) {
                                                        echo "<li><a href=\"" . htmlspecialchars($link['url']) . "\" target=\"_blank\">" . htmlspecialchars($link['text']) . "</a></li>";
                                                    } else {
                                                        echo "<li>" . htmlspecialchars($link['text']) . "</li>";
                                                    }
                                                }
                                                echo "</ul>";
                                            } else {
                                                echo htmlspecialchars($interpretationData['links']);
                                            }
                                        } else {
                                            echo "No recommendation needed.";
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <?php
                            }
                        }
                    }

                    // CALCULATE OVERALL AVERAGE FOR THE SUBJECT
                    if ($categoryCount > 0) {
                        $finalAverageRating = $totalAverage / $categoryCount;
                        ?>
                        <tr>
                            <th>Total Average</th>
                            <th><?php echo number_format((float) $finalAverageRating, 2, '.', ''); ?></th>
                        </tr>
                        <?php
                    }
                }
    }
    ?>
        </tbody>
    </table>
    <?php
} else {
    echo '<p>No subjects found.</p>';
}
?>

?>
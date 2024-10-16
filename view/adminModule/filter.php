<?php

include "../../model/dbconnection.php";

session_start();

$userId = $_SESSION["userid"];
$usersql = "SELECT * FROM `instructor` WHERE faculty_Id = '$userId'";
$usersql_query = mysqli_query($con, $usersql);
$userRow = mysqli_fetch_assoc($usersql_query);

$FacultyID = $userRow['faculty_Id'];

function getVerbalInterpretationAndLinks($averageRating, $category, $selectedSubject, $con)
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

    $sqlSubjectLinks = "SELECT * FROM subject WHERE subject = '$selectedSubject'";
    $sqlSubjectLinks_query = mysqli_query($con, $sqlSubjectLinks);
    $subjectLinks = mysqli_fetch_assoc($sqlSubjectLinks_query);



    $sqlCategoryLinks = "SELECT * FROM studentscategories";
    $sqlCategoryLinks_query = mysqli_query($con, $sqlCategoryLinks);

    $categoryLinks = [];

    while ($categoryLinksRow = mysqli_fetch_assoc($sqlCategoryLinks_query)) {
        $dbCategory = $categoryLinksRow['categories'];

        $categoryLinks[$dbCategory] = [
            'linkOne' => $categoryLinksRow['linkOne'],
            'linkTwo' => $categoryLinksRow['linkTwo'],
            'linkThree' => $categoryLinksRow['linkThree'],
        ];
        $categoryLinks['TEACHING EFFECTIVENESS'] = [
            'linkOne' => $subjectLinks['linkOne'],
            'linkTwo' => $subjectLinks['linkTwo'],
            'linkThree' => $subjectLinks['linkThree'],
        ];
    }

    // Add relevant links for ratings less than 2
    if ($averageRating < 2) {
        if (!empty($categoryLinks[$category])) {

            if (!empty($categoryLinks[$category]['linkOne'])) {
                $result['links'][] = [
                    'text' => 'Category Link One',
                    'url' => htmlspecialchars($categoryLinks[$category]['linkOne'])
                ];
            }

            if (!empty($categoryLinks[$category]['linkTwo'])) {
                $result['links'][] = [
                    'text' => 'Category linkTwo',
                    'url' => htmlspecialchars($categoryLinks[$category]['linkTwo'])
                ];
            }
            if (!empty($categoryLinks[$category]['linkThree'])) {
                $result['links'][] = [
                    'text' => 'Category linkThree',
                    'url' => htmlspecialchars($categoryLinks[$category]['linkThree'])
                ];
            }

        }

    }

    // If no links are added, provide a fallback
    if (empty($result['links'])) {
        $result['links'][] = ['text' => 'No links available for this category', 'url' => ''];
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
                $facultyID = $userRow['faculty_Id'];
                $totalAverage = 0;
                $categoryCount = 0;

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

                            $SQLFaculty = "SELECT * FROM `studentsform` WHERE toFacultyID = '$facultyID' 
                            AND subject = '$selectedSubject' 
                            AND semester = '$selectedSemester' 
                            AND academic_year = '$selectedAcademicYear'";

                            $SQLFaculty_query = mysqli_query($con, $SQLFaculty);

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

                                // Get interpretation and links based on the rating and category
                                $interpretationData = getVerbalInterpretationAndLinks($averageRating, $categories, $selectedSubject, $con);
                                ?>
                                <tr>
                                    <!-- Category -->
                                    <td><?php echo htmlspecialchars($categories); ?></td>

                                    <!-- Average Rating -->
                                    <td><?php echo number_format((float) $averageRating, 2, '.', ''); ?></td>

                                    <!-- Verbal Interpretation -->
                                    <td><?php echo htmlspecialchars($interpretationData['interpretation']); ?></td>

                                    <!-- Links/Recommendations -->
                                    <td>
                                        <?php
                                        // Only show recommendations for ratings less than 2
                                        if ($averageRating < 2) {
                                            // Check if links exist and are in array format
                                            if (is_array($interpretationData['links'])) {
                                                echo "<ul style='list-style: none; padding: 0; margin: 0;'>";

                                                // Loop through each link and display it
                                                foreach ($interpretationData['links'] as $link) {
                                                    if (!empty($link['url'])) {
                                                        // Display clickable link
                                                        echo "<li><a href=\"" . htmlspecialchars($link['url']) . "\" target=\"_blank\">" . htmlspecialchars($link['text']) . "</a></li>";
                                                    } else {
                                                        // Display just the text if URL is empty
                                                        echo "<li>" . htmlspecialchars($link['text']) . "</li>";
                                                    }
                                                }

                                                echo "</ul>";
                                            } else {
                                                // Fallback if links are not in array format
                                                echo htmlspecialchars($interpretationData['links']);
                                            }
                                        } else {
                                            // For ratings of 2 or above, no recommendation is needed
                                            echo "No recommendation needed.";
                                        }
                                        ?>
                                    </td>
                                </tr>


                                <?php
                            }
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
                } else {
                    ?>
                    <tr>
                        <td colspan="4">No ratings available for this subject.</td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        <?php
    }
} else {
    echo "No subjects found for this instructor.";
}
?>
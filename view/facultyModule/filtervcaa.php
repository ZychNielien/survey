<?php

include "../../model/dbconnection.php";

session_start();

$userId = $_SESSION["userid"];
$usersql = "SELECT * FROM `instructor` WHERE faculty_Id = '$userId'";
$usersql_query = mysqli_query($con, $usersql);
$userRow = mysqli_fetch_assoc($usersql_query);

$FacultyID = $userRow['faculty_Id'];
function getInterpretation($rating)
{
    if ($rating >= 0 && $rating < 1) {
        return 'None';
    } elseif ($rating >= 1 && $rating < 2) {
        return 'Poor';
    } elseif ($rating >= 2 && $rating < 3) {
        return 'Fair';
    } elseif ($rating >= 3 && $rating < 4) {
        return 'Satisfactory';
    } elseif ($rating >= 4 && $rating < 5) {
        return 'Very Satisfactory';
    } elseif ($rating == 5) {
        return 'Outstanding';
    } else {
        return 'No description';
    }
}

function fetchLinksForCategory($category, $con)
{
    $sqlLink = "SELECT * FROM vcaacategories WHERE categories = '$category'";
    $sqlLink_query = mysqli_query($con, $sqlLink);
    if ($sqlLink_query && mysqli_num_rows($sqlLink_query) > 0) {
        return mysqli_fetch_assoc($sqlLink_query);
    }
    return null;
}

function getVerbalInterpretationAndLinks($facultyID, $category, $selectedSubject, $con)
{
    $result = ['interpretation' => 'No interpretation', 'links' => 'No Links Available'];

    $sql = "SELECT * FROM vcaaexcel WHERE faculty_Id = '$facultyID'";
    $sql_query = mysqli_query($con, $sql);

    if ($sql_query && mysqli_num_rows($sql_query) > 0) {
        $sqlRow = mysqli_fetch_assoc($sql_query);

        switch ($category) {
            case "COMMITMENT":
                $rating = $sqlRow['categoryOne'];
                break;
            case "KNOWLEDGE OF THE SUBJECT":
                $rating = $sqlRow['categoryTwo'];
                break;
            case "TEACHING FOR INDEPENDENT LEARNING":
                $rating = $sqlRow['categoryThree'];
                break;
            case "MANAGEMENT OF LEARNING":
                $rating = $sqlRow['categoryFour'];
                break;
            case "EMOTIONAL COMPETENCE":
                $rating = $sqlRow['categoryFive'];
                break;
            default:
                $rating = null;
        }

        $result['rating'] = number_format($rating, 2);
        $result['interpretation'] = getInterpretation($rating);

        if ($rating < 2) {
            $linkRow = fetchLinksForCategory($category, $con);

            if ($linkRow) {
                $result['links'] = [
                    $linkRow['linkOne'],
                    $linkRow['linkTwo'],
                    $linkRow['linkThree']
                ];
            }

            if ($category === "KNOWLEDGE OF THE SUBJECT") {
                $sqlSubjectLinks = "SELECT * FROM subject WHERE subject = '$selectedSubject'";
                $sqlSubjectLinks_query = mysqli_query($con, $sqlSubjectLinks);

                if ($sqlSubjectLinks_query && mysqli_num_rows($sqlSubjectLinks_query) > 0) {
                    $subjectLinks = mysqli_fetch_assoc($sqlSubjectLinks_query);
                    $result['links'] = [
                        $subjectLinks['linkOne'],
                        $subjectLinks['linkTwo'],
                        $subjectLinks['linkThree']
                    ];
                }
            }
        }
    }

    return $result;
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
    JOIN vcaaexcel sf ON sf.faculty_Id = a.faculty_Id
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
            <h5>(Semester: <?php echo htmlspecialchars($subject['semester']) ?>, Academic Year:
                <?php echo htmlspecialchars($subject['academic_year']) ?>)
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
                $sql = "SELECT * FROM `vcaacategories`";
                $sql_query = mysqli_query($con, $sql);

                if (mysqli_num_rows($sql_query) > 0) {
                    while ($categoriesRow = mysqli_fetch_assoc($sql_query)) {
                        $categories = $categoriesRow['categories'];

                        $interpretationData = getVerbalInterpretationAndLinks($facultyID, $categories, $subject['subject'], $con);
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($categories); ?></td>
                            <td><?php echo htmlspecialchars($interpretationData['rating']); ?></td>
                            <td><?php echo htmlspecialchars($interpretationData['interpretation']); ?></td>
                            <td>
                                <?php
                                if ($interpretationData['rating'] < 2) {
                                    foreach ($interpretationData['links'] as $link) {
                                        if (!empty($link)) {
                                            echo "<a href='" . htmlspecialchars($link) . "'>Visit Link</a><br>";
                                        }
                                    }
                                } else {
                                    echo "No recommendations needed";
                                }
                                ?>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </tbody>
        </table>
        <?php
    }
} else {
    echo "<h2 style='text-align: center; color: red;'>No subjects found for this instructor.</h2>";
}
?>
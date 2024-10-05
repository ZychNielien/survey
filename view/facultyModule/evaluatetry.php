<?php

include "components/navBar.php"

    ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Filter Faculty Evaluation Records</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <h2>Filter Faculty Evaluation Records</h2>

        <div class="form-row mb-3">
            <div class="col">
                <input type="text" id="usernameInput" class="form-control" placeholder="Enter Username">
            </div>
            <div class="col">
                <select id="semesterSelect" class="form-control">
                    <option value="">Select Semester</option>
                    <option value="1st">1st Semester</option>
                    <option value="2nd">2nd Semester</option>
                    <option value="Summer">Summer</option>
                </select>
            </div>
            <div class="col">
                <select id="yearSelect" class="form-control">
                    <option value="">Select Academic Year</option>
                    <option value="2022-2023">2022-2023</option>
                    <option value="2023-2024">2023-2024</option>
                    <option value="2024-2025">2024-2025</option>
                    <!-- Add more years as needed -->
                </select>
            </div>
            <div class="col">
                <button id="filterButton" class="btn btn-primary">Filter</button>
            </div>
        </div>

        <table class="table table-striped table-bordered text-center align-middle w-100">
            <thead>
                <tr class="bg-danger text-white">
                    <th>Faculty Evaluated</th>
                    <th>Semester</th>
                    <th>Academic Year</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <?php
                $UserID = $userRow['faculty_Id'];
                $archivedSQL = "SELECT * FROM `peertopeerform` WHERE fromFacultyID = '$UserID'";
                $archivedSQL_query = mysqli_query($con, $archivedSQL);

                if ($archivedSQL_query) {
                    while ($archivedRow = mysqli_fetch_assoc($archivedSQL_query)) {
                        echo '
                        <tr>
                            <td>' . $archivedRow['toFaculty'] . '</td>
                            <td>' . $archivedRow['semester'] . '</td>
                            <td>' . $archivedRow['academic_year'] . '</td>
                        </tr>
                        ';
                    }
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function () {
            $("#filterButton").on("click", function () {
                var username = $("#usernameInput").val().toLowerCase();
                var semester = $("#semesterSelect").val();
                var year = $("#yearSelect").val();

                $("#tableBody tr").filter(function () {
                    var usernameMatch = username === "" || $(this).find("td:eq(0)").text().toLowerCase().indexOf(username) > -1;
                    var semesterMatch = semester === "" || $(this).find("td:eq(1)").text() === semester;
                    var yearMatch = year === "" || $(this).find("td:eq(2)").text() === year;
                    return usernameMatch && semesterMatch && yearMatch;
                }).show();

                $("#tableBody tr").filter(function () {
                    var usernameMatch = username === "" || $(this).find("td:eq(0)").text().toLowerCase().indexOf(username) > -1;
                    var semesterMatch = semester === "" || $(this).find("td:eq(1)").text() === semester;
                    var yearMatch = year === "" || $(this).find("td:eq(2)").text() === year;
                    return !(usernameMatch && semesterMatch && yearMatch);
                }).hide();
            });
        });
    </script>
</body>

</html>
<?php

session_start();

include "../model/dbconnection.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve booking data from POST request
    $course = $_POST['course'];
    $name = $_POST['name'];
    $room = $_POST['room'];
    $selectedDate = new DateTime($_POST['selected_date']);
    $startTime = intval($_POST['start_time']);
    $endTime = intval($_POST['end_time']);
    $selectedSlot = $_POST['selected_slot'];
    $evaluationStatus = $_POST['evaluation_status'];

    // Format date and time
    $formattedDate = $selectedDate->format('F d, Y'); // e.g., September 09, 2023
    $formattedStartTime = date('g:i A', strtotime("$startTime:00")); // e.g., 7:00 AM
    $formattedEndTime = date('g:i A', strtotime("$endTime:00")); // e.g., 8:00 AM

    // Prepare the email notification
    $url = "https://script.google.com/macros/s/AKfycbyxelEgiJLf-a-EuL6qdg5QZOaZC6L-EzYNQ4akLi2lImaPvVtavLbgNotMVijqv-g9wA/exec";
    $recipient = 'cicsmalvarevaluation@gmail.com'; // Change to your admin email address
    $subject = 'New Classroom Observation Booking';
    $body = "
    Dear Admin,

    We are pleased to inform you that a new classroom observation has been successfully booked by $name for the course titled $course. The observation is scheduled to take place in room $room on $formattedDate from $formattedStartTime to $formattedEndTime.

    Please review the Classroom Observation for more information.

    Best regards,
    Your Booking System
    ";

    // Initialize cURL
    $ch = curl_init($url);

    // Set cURL options
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query([
            "recipient" => $recipient,
            "subject" => $subject,
            "body" => $body
        ])
    ]);

    // Execute cURL request
    $result = curl_exec($ch);

    // Check for cURL errors
    if ($result === false) {
        echo 'cURL Error: ' . curl_error($ch);
        exit; // Exit if there's an error
    }

    // Close cURL session
    curl_close($ch);

    // Respond with JSON
    echo json_encode(['status' => 'success', 'message' => 'Booking created successfully. Notification sent to admin.']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}



// Get the query parameters sent from JavaScript
$facultySelect = isset($_GET['facultySelect']) ? $_GET['facultySelect'] : '';
$adminSelect = isset($_GET['adminSelect']) ? $_GET['adminSelect'] : '';

// Base query
$archivedSQL = "SELECT * FROM classroomobservation WHERE 1";

// Apply filters if faculty or admin is selected
if (!empty($facultySelect)) {
    $archivedSQL .= " AND toFacultyID = '$facultySelect'";
}

if (!empty($adminSelect)) {
    $archivedSQL .= " AND fromFacultyID = '$adminSelect'";
}

// Execute query
$archivedSQL_query = mysqli_query($con, $archivedSQL);

if (!$archivedSQL_query) {
    die("Database query failed: " . mysqli_error($con));
}

// Return the filtered table rows or all results if no filters are applied
if (mysqli_num_rows($archivedSQL_query) > 0) {
    while ($archivedRow = mysqli_fetch_assoc($archivedSQL_query)) {
        $toFaculty = htmlspecialchars($archivedRow['toFaculty'], ENT_QUOTES, 'UTF-8');
        $date = htmlspecialchars($archivedRow['date'], ENT_QUOTES, 'UTF-8');
        $fromFaculty = htmlspecialchars($archivedRow['fromFaculty'], ENT_QUOTES, 'UTF-8');
        $officialId = htmlspecialchars($archivedRow['id'], ENT_QUOTES, 'UTF-8');

        echo '
        <tr>
            <td class="official_id" hidden>' . $officialId . '</td>
            <td>' . $toFaculty . '</td>
            <td data-toFaculty="' . $archivedRow['toFacultyID'] . '" hidden>' . $archivedRow['toFacultyID'] . '</td>
            <td>' . $date . '</td>
            <td>' . $fromFaculty . '</td>
            <td data-fromFaculty="' . $archivedRow['fromFacultyID'] . '" hidden>' . $archivedRow['fromFacultyID'] . '</td>
            <td><a href="#" class="view-btn btn btn-success">Print</a></td>
        </tr>';
    }
} else {
    echo '<tr><td colspan="5">No records found.</td></tr>';
}
?>

<script>
    function printPartOfPage(elementId) {
        var printContent = document.getElementById(elementId);
        var windowUrl = 'about:blank';
        var uniqueName = new Date();
        var windowName = 'Print' + uniqueName.getTime();
        var printWindow = window.open(windowUrl, windowName, 'width=1000,height=1000');


        printWindow.document.write(printContent.innerHTML);

        printWindow.document.close();
        printWindow.focus();

        // Trigger print
        printWindow.print();

        // Close the print window and modal immediately
        printWindow.close();
        $('#officialviewmodal').modal('hide');
    }

    // SweetAlert Function
    function showSweetAlert(facultyName) {
        Swal.fire({
            title: `The Classroom Observation Form Has Been Successfully Generated`,
            text: 'Do you want to print?',
            icon: 'success',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Print',
            cancelButtonText: 'Close',
        }).then((result) => {
            if (result.isConfirmed) {
                printPartOfPage(143); // Call your print function
            } else {
                $('#officialviewmodal').modal('hide'); // Hide modal if closed
            }
        });
    }

    // Show View Modal for Resident
    $('.view-btn').click(function (e) {
        e.preventDefault();

        // Get the official_id and faculty name from the table row
        var official_id = $(this).closest('tr').find('.official_id').text();
        const facultyName = $(this).closest('tr').find('td[data-faculty]').data('faculty');

        // Perform the AJAX request
        $.ajax({
            type: "POST",
            url: "../../view/adminModule/printClassroomObservation.php",
            data: {
                'checking_viewbtn': true,
                'official_id': official_id,
            },
            success: function (response) {
                // Update modal content and show the modal
                $('.officialviewmodal').html(response);
                $('#officialviewmodal').modal('show');

                // Call SweetAlert after showing the modal
                showSweetAlert(facultyName); // Pass the faculty name to the SweetAlert function
            },
            error: function (xhr, status, error) {
                // Handle errors
                console.error("AJAX Error: " + status + ": " + error);
                alert("An error occurred while processing your request. Please try again.");
            }
        });
    });
</script>
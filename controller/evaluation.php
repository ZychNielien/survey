<?php
session_start();
include "../model/dbconnection.php";


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


        printWindow.print();


        printWindow.close();
        $('#officialviewmodal').modal('hide');
    }


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
                printPartOfPage(143);
            } else {
                $('#officialviewmodal').modal('hide');
            }
        });
    }


    $('.view-btn').click(function (e) {
        e.preventDefault();

        var official_id = $(this).closest('tr').find('.official_id').text();
        const facultyName = $(this).closest('tr').find('td[data-faculty]').data('faculty');

        $.ajax({
            type: "POST",
            url: "../../view/adminModule/printClassroomObservation.php",
            data: {
                'checking_viewbtn': true,
                'official_id': official_id,
            },
            success: function (response) {
                $('.officialviewmodal').html(response);
                $('#officialviewmodal').modal('show');

                showSweetAlert(facultyName);
            },
            error: function (xhr, status, error) {
                console.error("AJAX Error: " + status + ": " + error);
                alert("An error occurred while processing your request. Please try again.");
            }
        });
    });
</script>
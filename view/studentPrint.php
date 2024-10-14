<?php
include '../model/dbconnection.php';
session_start();
$srCode = $_SESSION["studentSRCode"];

?>

<link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" href="../public/css/sweetalert.min.css">

<!-- SCRIPT -->
<script src="../public/js/sweetalert2@11.js"></script>
<script src="../public/js/jquery-3.7.1.min.js"></script>
<script src="../bootstrap/js/bootstrap.min.js"></script>


<div class="modal fade" id="officialviewmodal" tabindex="-1" role="dialog" aria-labelledby="officialviewmodalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body officialviewmodal">
                talaga tol?
            </div>
        </div>
    </div>
</div>

<table class="table table-striped table-bordered text-center align-middle mb-5">
    <thead>
        <tr class="bg-danger">
            <th>Full Name</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $srCode = mysqli_real_escape_string($con, $srCode);


        $sql = "SELECT * FROM studentsform WHERE fromStudentID = '$srCode'";
        $sql_query = mysqli_query($con, $sql);

        if ($sql_query) {
            while ($row = mysqli_fetch_Assoc($sql_query)) {
                $officialId = htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8');
                ?>
                <tr>

                    <td class="official_id" style="display:none;"><?php echo $officialId ?></td>
                    <td data-tofaculty="<?php echo htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?>" style="display:none;">
                    </td>
                    <td><?php echo htmlspecialchars($row['toFaculty'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><a href="#" class="print-btn btn btn-success">Print</a></td>
                </tr>
                <?php
            }
        }
        ?>
    </tbody>
</table>



<script>
    $('.print-btn').click(function (e) {
        e.preventDefault();

        var official_id = $(this).closest('tr').find('.official_id').text();
        const facultyName = $(this).closest('tr').find('td[data-tofaculty]').data('tofaculty');
        console.log(facultyName);
        $.ajax({
            type: "POST",
            url: "print.php",
            data: {
                'checking_viewbtn': true,
                'official_id': official_id,
            },
            success: function (response) {
                console.log(response);
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

    function showSweetAlert(facultyName) {
        Swal.fire({
            title: 'The Classroom Observation Form Has Been Successfully Generated',
            text: 'Do you want to print?',
            icon: 'success',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Print',
            cancelButtonText: 'Close',
        }).then((result) => {
            if (result.isConfirmed) {
                printPartOfPage('officialviewmodal');
            } else {
                $('#officialviewmodal').modal('hide');
            }
        });
    }

    function printPartOfPage(elementId) {
        var printContent = document.getElementById(elementId).innerHTML;
        var windowUrl = 'about:blank';
        var uniqueName = new Date();
        var windowName = 'Print' + uniqueName.getTime();
        var printWindow = window.open(windowUrl, windowName, 'width=1000,height=1000');

        printWindow.document.write(printContent);
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        printWindow.close();

        $('#officialviewmodal').modal('hide');
    }

</script>
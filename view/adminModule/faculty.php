<?php

// NAVIGATION BAR
include "components/navBar.php";
// DATABASE CONNECTION
include "../../model/dbconnection.php";

?>

<head>

    <!-- PAGE TITLE -->
    <title>Faculty Member</title>

    <!-- STYLESHEETS OR CSS FILES -->
    <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/sweetalert.min.css">

    <!-- SCRIPT -->
    <script src="../../public/js/sweetalert2@11.js"></script>
    <script src="../../public/js/jquery-3.7.1.min.js"></script>
    <script src="../../bootstrap/js/bootstrap.bundle.min.js"></script>

</head>

<!-- CONTENT CONTAINER -->
<section class="contentContainer ">

    <!-- CONTAINER -->
    <div class="container">

        <!-- CONTENT HEADER -->
        <h3 class="fw-bold text-danger text-center">Faculty Member</h3>

        <!-- ADDING NEW FACULTY BUTTON -->
        <div class="d-flex justify-content-end flex-row align-items-center mb-3">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addFacultyModal">+ Faculty</button>
        </div>

        <!-- FACULTY TABLE -->
        <table class="table table-striped table-bordered text-center align-middle">

            <!-- THEAD -->
            <thead class="bg-danger text-white">

                <!-- TABLE ROW -->
                <tr class="text-uppercase fw-bold">
                    <td>Image</td>
                    <td>Full Name</td>
                    <td>Actions</td>
                </tr>

            </thead>

            <!-- TBODY -->
            <tbody>

                <!-- QUERY TO GET ALL FACULTY MEMBERS IN INSTRUCTOR TABLE -->
                <?php

                $sql = "SELECT * FROM `instructor`";
                $sql_query = mysqli_query($con, $sql);

                if ($sql_query) {
                    while ($row = mysqli_fetch_array($sql_query)) {
                        echo '
                                <tr>
                                    <td class="faculty_Id" hidden>' . $row['faculty_Id'] . '</td>
                                    <td><img src="../' . htmlspecialchars($row['image']) . '" style="height: 130px;"></td>
                                    <td>' . htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) . '</td>
                                    <td>
     <a href="#" class="btn btn-primary edit-btn" 
   data-faculty-id="' . $row['faculty_Id'] . '"
   data-first-name="' . htmlspecialchars($row['first_name']) . '"
   data-last-name="' . htmlspecialchars($row['last_name']) . '"
   data-image="../' . htmlspecialchars($row['image']) . '">Edit</a>
<button class="btn btn-danger delete-btn" data-faculty-id="' . $row['faculty_Id'] . '">Delete</button>

                                    </td>
                                </tr>
                            ';
                    }
                } else {
                    echo '<tr><td colspan="4">No instructors found.</td></tr>';
                }
                ?>

            </tbody>

        </table>

    </div>

    <!-- NEW FACULTY MODAL -->
    <div class="modal fade" id="addFacultyModal" tabindex="-1" aria-labelledby="addFacultyModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="addFacultyModalLabel">Add a New Faculty</h5>
                    <button type="button" class="btn-close bg-white text-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <form id="myForm" method="POST" action="../../controller/facultyQuery.php"
                    enctype="multipart/form-data">
                    <div class="modal-body">
                        <!-- IMAGE DIV -->
                        <div class="d-flex justify-content-center mb-3">
                            <img id="imagePreview" alt="Image Preview" src="https://via.placeholder.com/300"
                                style="max-height: 130px; cursor: pointer;" onclick="selectImage();" />
                        </div>
                        <div class="d-flex justify-content-evenly mt-4">
                            <!-- FIRST NAME DIV -->
                            <div class="form-group">
                                <label for="firstName">First Name</label>
                                <input type="text" class="form-control my-1" id="firstName"
                                    placeholder="Enter your first name" name="first_name" required>
                            </div>
                            <!-- LAST NAME DIV -->
                            <div class="form-group">
                                <label for="lastName">Last Name</label>
                                <input type="text" class="form-control my-1" id="lastName"
                                    placeholder="Enter your last name" name="last_name" required>
                            </div>
                        </div>
                        <!-- GSUITE DIV, NAKAHIDE PINAGCONCAT KO YUNG FIRST NAME AT LAST NAME -->
                        <div class="form-group mx-3" style="display:none;">
                            <label for="gsuite">Gsuite</label>
                            <input type="text" class="form-control my-1" id="gsuite" placeholder="Enter your Gsuite"
                                name="gsuite">
                        </div>
                        <!-- PASSWORD DIV, NAKAHIDE ANG GINAWA KONG DEFAULT PASSWORD IS LAST NAME ALL CAPS -->
                        <div class="form-group mx-3" style="display:none;">
                            <label for="password">Password</label>
                            <input type="password" class="form-control my-1" id="password"
                                placeholder="Enter your password" name="password">
                        </div>
                        <!-- USER TYPE DIV, NAKAHIDE MATIK LAHAT FACULTY ANG USER TYPE TATLO LANG ANG ADMIN -->
                        <div class="form-group mx-3" style="display:none;">
                            <label for="password">type</label>
                            <input type="password" class="form-control my-1" id="password"
                                placeholder="Enter your password" name="type">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" id="addFaculty" name="addFaculty" class="btn btn-primary">Submit</button>
                    </div>
                    <!-- INPUT NI IMAGE -->
                    <input type="file" id="imageInput" name="image" accept="image/*" style="display: none;"
                        onchange="previewImage();">
                </form>
            </div>
        </div>
    </div>

    <!-- EDIT FACULTY MODAL -->
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Instructor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm" enctype="multipart/form-data">
                        <input type="hidden" id="faculty_Id" name="faculty_Id">

                        <!-- Image Display -->
                        <div class="mb-3 text-center">
                            <img src="" alt="Faculty Image" id="facultyImage" class="img-fluid mb-3"
                                style="display: none; max-width: 100%; max-height: 150px; text-align:center;">
                        </div>

                        <!-- Image Upload Option -->
                        <div class="mb-3">
                            <label for="new_image" class="form-label">Upload New Image</label>
                            <input type="file" class="form-control" id="new_image" name="new_image">
                        </div>

                        <!-- First Name Field -->
                        <div class="mb-3">
                            <label for="first_name" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" required>
                        </div>

                        <!-- Last Name Field -->
                        <div class="mb-3">
                            <label for="last_name" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveChanges">Save changes</button>
                </div>
            </div>
        </div>
    </div>





</section>

<!-- SWEETALERT -->
<?php if (isset($_SESSION['success'])): ?>
    <script>
        Swal.fire({
            title: 'Success!',
            text: '<?php echo $_SESSION['success']; ?>',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.reload();
        });
    </script>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<script>

    $(document).ready(function () {

        <?php if (isset($_SESSION['status'])): ?>
            Swal.fire({
                title: '<?php echo $_SESSION['status']; ?>',
                icon: '<?php echo ($_SESSION['status-code'] == 'success' ? 'success' : 'error'); ?>',
                confirmButtonText: 'OK'
            });
            <?php unset($_SESSION['status']); ?>
        <?php endif; ?>

        // VALIDATION KAPAG HINDI NAKAPAGINPUT NG IMAGE SI ADMIN
        $('#myForm').on('submit', function (event) {
            var imageInput = $('#imageInput');

            if (imageInput[0].files.length === 0) {
                event.preventDefault();
                Swal.fire({
                    icon: 'warning',
                    title: 'Oops...',
                    text: 'Please upload a picture before submitting.',
                    confirmButtonText: 'Okay'
                });
                imageInput.focus();
            }
        });

        $('#new_image').on('change', function () {
            var input = this;

            // Ensure the input has a file
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                // Set the preview image source when file is loaded
                reader.onload = function (e) {
                    $('#facultyImage').attr('src', e.target.result).show(); // Show the image with new source
                }

                // Read the image file
                reader.readAsDataURL(input.files[0]);
            }
        });

        // PARA MAKUHA YUNG MGA DATA NI FACULTY AT MAPUNTA SA EDIT MODAL
        $('.edit-btn').on('click', function () {
            // Populate form fields
            $('#faculty_Id').val($(this).data('faculty-id'));
            $('#first_name').val($(this).data('first-name'));
            $('#last_name').val($(this).data('last-name'));

            // Set the image URL and display the image
            var imageUrl = $(this).data('image');
            if (imageUrl) {
                $('#facultyImage').attr('src', imageUrl).show();
            } else {
                $('#facultyImage').attr('src', '').hide();
            }

            var editModal = new bootstrap.Modal(document.getElementById('editModal'));
            editModal.show();
        });


        // HANDLE NG DELETE BUTTON
        $('.delete-btn').on('click', function () {
            const facultyId = $(this).data('faculty-id');

            Swal.fire({
                title: 'Are you sure?',
                text: "This will permanently delete the faculty member.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '../../controller/facultyQuery.php',
                        data: { faculty_Id: facultyId, action: 'delete' },
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                Swal.fire('Deleted!', 'Faculty member has been deleted.', 'success').then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: response.message,
                                });
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('AJAX error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'An unexpected error occurred.',
                            });
                        }
                    });
                }
            });
        });

        $('#saveChanges').on('click', function () {
            var form = $('#editForm')[0]; // Get the form element
            var formData = new FormData(form); // Create FormData object

            // Append additional data if needed
            formData.append('action', 'update');

            $.ajax({
                type: 'POST',
                url: '../../controller/facultyQuery.php',
                data: formData, // Use FormData object
                processData: false, // Prevent jQuery from automatically processing the data
                contentType: false, // Set content type to false for FormData
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Updated!',
                            text: 'Instructor details updated successfully.',
                        }).then(() => {
                            location.reload(); // Reload the page after success
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: response.message,
                        });
                    }
                },
                error: function (xhr, status, error) {
                    console.error('AJAX error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'An unexpected error occurred.',
                    });
                }
            });
        });

    });

    // HIDDEN INPUT NI IMAGE
    function selectImage() {
        $('#imageInput').click();
    }

    // IMAGE PREVIEW PARA KAPAG PUMILI NG IMAGE MAGDISPLAY AGAD
    function previewImage() {
        const file = document.getElementById('imageInput').files[0];
        const reader = new FileReader();

        reader.onload = function (e) {
            document.getElementById('imagePreview').src = e.target.result;
        }

        if (file) {
            reader.readAsDataURL(file);
        }
    }

    // CONFIRMATION NG DELETE
    function confirmDelete(id) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This will permanently delete the faculty member.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '../../controller/facultyQuery.php?faculty_Id=' + id;
            }
        });
    }

</script>
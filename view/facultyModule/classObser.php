<?php
include "components/navBar.php";


?>

<head>
    <title>Classroom Observation</title>
    <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../public/css/style.css">
    <link rel="stylesheet" href="../../public/css/sweetalert.min.css">
    <script src="../../public/js/sweetalert2@11.js"></script>
    <script src="../../public/js/jquery-3.7.1.min.js"></script>
    <script src="../../bootstrap/js/bootstrap.bundle.min.js"></script>
</head>

<section class="contentContainer px-3">

    <h3 class="fw-bold text-danger text-center">Classroom Observation</h3>

    <div class="d-flex justify-content-evenly align-items-center">
        <div class="form-group">
            <label for="date-select">Select Date:</label>
            <input type="date" class="form-control" id="date-select">
        </div>

        <div class="form-group">
            <label for="start-time-select">Start Time:</label>
            <select class="form-control" id="start-time-select">
                <option value>Select Start Time</option>
            </select>
        </div>

        <div class="form-group">
            <label for="end-time-select">End Time:</label>
            <select class="form-control" id="end-time-select" disabled>
                <option value>Select End Time</option>
            </select>
        </div>

        <div class="form-group">
            <label for="slot-select">Select Slot:</label>
            <select class="form-control" id="slot-select" disabled>
                <option value>Select Slot</option>
                <option value="1">Slot 1</option>
                <option value="2">Slot 2</option>
            </select>
        </div>

        <button class="btn btn-success" id="book-btn" disabled>Book</button>
        <!-- <button class="btn btn-danger" id="clear-btn">Clear All Reservations</button> -->
    </div>

    <table id="reservation-table" class="table table-bordered mt-2 "
        style="text-align: center; vertical-align: middle;"></table>

    <!-- Booking Modal -->
    <div class="modal fade" id="reservationModal" tabindex="-1" aria-labelledby="reservationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reservationModalLabel">Reservation Form</h5>
                    <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form" method="POST">
                        <div class="form-group">
                            <label for="course">Course Title:</label>
                            <input type="text" class="form-control" id="course" required>
                        </div>
                        <div class="form-group">
                            <label for="name">Instructor:</label>
                            <input type="text" class="form-control" id="name"
                                value="<?php echo $userRow["first_name"] . ' ' . $userRow["last_name"]; ?>" required>
                            <input type="text" class="form-control" id="fromFacultyID"
                                value="<?php echo $userRow["faculty_Id"]; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="room">Room:</label>
                            <input type="text" class="form-control" id="room" required>
                        </div>
                        <div class="form-group" style="display:none;">
                            <input type="text" value="0" id="evaluationStatus">
                        </div>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancel Booking Modal -->

    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelModalLabel">Cancel Booking</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to cancel this booking?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="confirm-cancel-btn">Yes, Cancel</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Keep It</button>
                </div>
            </div>
        </div>
    </div>

</section>




<script>
    let bookedSlots = {};
    let slotToCancel = null;

    $(document).ready(function () {
        const today = new Date().toISOString().split('T')[0];
        $('#date-select').attr('min', today).val(today);
        loadBookings();
        createReservationTable();
        updateStartTimeOptions();

        $('#date-select').change(function () {
            createReservationTable();
            updateStartTimeOptions();
            updateSlotOptions();
        });

        $('#start-time-select').change(function () {
            updateEndTimeOptions();
            checkStartTimeAvailability(); // Check availability when start time changes
            updateSlotOptions();
        });

        $('#end-time-select').change(checkSlotAvailability); // Check availability when end time changes
        $('#slot-select').change(checkSlotAvailability);
        $('#book-btn').click(openForm);
        $('#form').submit(submitForm);
        // $('#clear-btn').click(clearBookings);
        $(document).on('click', '#confirm-cancel-btn', confirmCancelBooking);
    });

    function loadBookings() {
        const storedBookings = localStorage.getItem('bookedSlots');
        if (storedBookings) {
            bookedSlots = JSON.parse(storedBookings);
        }
    }

    function saveBookings() {
        localStorage.setItem('bookedSlots', JSON.stringify(bookedSlots));
    }

    function createReservationTable() {
        const selectedDate = new Date($('#date-select').val());
        const days = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
        const headerRow = $('<tr>').addClass('bg-danger text-white py-3').css('border', '2px solid white').append($('<th rowspan="2" style="vertical-align: middle">DATE / TIME</th>'));

        for (let i = 0; i < 2; i++) {
            const day = new Date(selectedDate);
            day.setDate(selectedDate.getDate() + i);
            const dateHeader = `${day.toLocaleString('default', { month: 'long' })} ${day.getDate()}, ${day.getFullYear()}`;
            const dayHeader = days[day.getDay()];

            const dateCell = $('<th>').attr('colspan', 2).html(`${dateHeader}<br>${dayHeader}`).addClass('py-3').css({
                'border': '2px solid white',
                'letter-spacing': '2px'
            });
            headerRow.append(dateCell);
        }

        const slotHeaderRow = $('<tr>').append($('<th style="display: none;"></th>'));
        for (let i = 0; i < 2; i++) {
            slotHeaderRow.append($('<th>').addClass('bg-danger text-white py-3').css('border', '2px solid white').text('Slot 1')).append($('<th>').addClass('bg-danger text-white py-3').css('border', '2px solid white').text('Slot 2'));
        }

        $('#reservation-table').empty().append(headerRow).append(slotHeaderRow);

        for (let hour = 7; hour < 19; hour++) {
            const row = $('<tr>').addClass('bg-danger text-white').css('border', '2px solid white').append($('<td>').text(`${hour > 12 ? hour - 12 : hour}:00 to ${hour + 1 > 12 ? hour + 1 - 12 : hour + 1}:00 ${hour >= 12 ? 'PM' : 'AM'}`));

            for (let i = 0; i < 2; i++) {
                const dayOffset = new Date(selectedDate);
                dayOffset.setDate(selectedDate.getDate() + i);
                const slotKey1 = `${hour}-${dayOffset.getTime()}-1`;
                const slotKey2 = `${hour}-${dayOffset.getTime()}-2`;

                const cell1 = $('<td>').css({
                    'border': '2px solid #fff',
                    'color': '#000',
                    'background': '#c1d7b5'
                }).text('Available');

                const cell2 = $('<td>').css({
                    'border': '2px solid #fff',
                    'color': '#000',
                    'background': '#c1d7b5'
                }).text('Available');

                if (bookedSlots[slotKey1]) {
                    cell1.addClass('py-3 booked-slot').css({
                        'border': '2px solid #fff',
                        'color': '#000',
                        'background': '#f2b2b2'
                    }).html(`${bookedSlots[slotKey1].name}<br>${bookedSlots[slotKey1].room}`)
                        .click(() => openCancelModal(slotKey1));
                } else {
                    cell1.addClass(' py-3').css({
                        'border': '2px solid #fff',
                        'color': '#000',
                        'background': '#c1d7b5'
                    }).text('Available');
                }

                if (bookedSlots[slotKey2]) {
                    cell2.addClass('py-3 booked-slot').css({
                        'border': '2px solid #fff',
                        'color': '#000',
                        'background': '#f2b2b2'
                    }).html(`${bookedSlots[slotKey2].name}<br>${bookedSlots[slotKey2].room}`)
                        .click(() => openCancelModal(slotKey2));
                } else {
                    cell2.addClass(' py-3').css({
                        'border': '2px solid #fff',
                        'color': '#000',
                        'background': '#c1d7b5'
                    }).text('Available');
                }

                row.append(cell1).append(cell2);
            }
            $('#reservation-table').append(row);
        }
    }

    function updateStartTimeOptions() {
        const startTimeSelect = $('#start-time-select');
        startTimeSelect.empty().append('<option value>Select Start Time</option>');
        for (let hour = 7; hour < 19; hour++) {
            startTimeSelect.append(`<option value="${hour}">${hour > 12 ? hour - 12 : hour}:00 ${hour >= 12 ? 'PM' : 'AM'}</option>`);
        }
    }

    function updateEndTimeOptions() {
        const endTimeSelect = $('#end-time-select');
        const startTime = parseInt($('#start-time-select').val());
        endTimeSelect.empty().append('<option value>Select End Time</option>');

        // Limit end time to 3 hours from start time
        for (let hour = startTime + 1; hour <= startTime + 5 && hour <= 19; hour++) {
            endTimeSelect.append(`<option value="${hour}">${hour > 12 ? hour - 12 : hour}:00 ${hour >= 12 ? 'PM' : 'AM'}</option>`);
        }

        endTimeSelect.prop('disabled', endTimeSelect.children().length === 1); // Disable if no options are available
    }

    function updateSlotOptions() {
        const slotSelect = $('#slot-select');
        const endTime = parseInt($('#end-time-select').val());
        slotSelect.prop('disabled', endTime <= parseInt($('#start-time-select').val()));
    }

    function checkStartTimeAvailability() {
        const selectedDate = new Date($('#date-select').val());
        const startTime = parseInt($('#start-time-select').val());

        // Check if the start time is valid
        if (isNaN(startTime)) {
            $('#book-btn').prop('disabled', true); // Disable book button if time is invalid
            $('#slot-select').prop('disabled', true); // Disable slot selection if time is invalid
            return;
        }

        // Construct keys for checking both slots
        const slotKey1 = `${startTime}-${selectedDate.getTime()}-1`;
        const slotKey2 = `${startTime}-${selectedDate.getTime()}-2`;

        // Check if either of the slots is booked
        const isSlot1Booked = bookedSlots[slotKey1];
        const isSlot2Booked = bookedSlots[slotKey2];

        // Call the slot availability check
        checkSlotAvailability();
    }

    function checkSlotAvailability() {
        const selectedDate = new Date($('#date-select').val());
        const startTime = parseInt($('#start-time-select').val());
        const endTime = parseInt($('#end-time-select').val());

        // Check if start and end times are valid
        if (isNaN(startTime) || isNaN(endTime) || endTime <= startTime) {
            $('#book-btn').prop('disabled', true); // Disable book button if times are invalid
            $('#slot-select').prop('disabled', true); // Disable slot selection if times are invalid
            return;
        }

        // Construct keys for checking the slots
        const slotKey1 = `${startTime}-${selectedDate.getTime()}-1`;
        const slotKey2 = `${startTime}-${selectedDate.getTime()}-2`;

        // Check if either of the slots for the selected start and end times is booked
        const isSlot1Booked = bookedSlots[slotKey1];
        const isSlot2Booked = bookedSlots[slotKey2];

        // Disable slots based on availability
        if (isSlot1Booked && isSlot2Booked) {
            $('#slot-select').prop('disabled', true); // Disable both if both are booked
            $('#book-btn').prop('disabled', true);
            swal("Error!", "Both slots are already booked.", "error");
        } else {
            // Enable the slot selection based on bookings
            $('#slot-select').prop('disabled', false);
            $('#book-btn').prop('disabled', false);

            // Update slot options based on bookings
            $('#slot-select option').each(function () {
                const slotValue = $(this).val();
                if ((slotValue === '1' && isSlot1Booked) || (slotValue === '2' && isSlot2Booked)) {
                    $(this).prop('disabled', true); // Disable the booked slot option
                } else {
                    $(this).prop('disabled', false); // Enable the available slot option
                }
            });
        }
    }
    function openForm() {
        $('#reservationModal').modal('show');
    }

    function submitForm(event) {
        event.preventDefault();
        const course = $('#course').val();
        const name = $('#name').val();
        const room = $('#room').val();
        const fromFacultyID = $('#fromFacultyID').val();
        const selectedDate = new Date($('#date-select').val());
        const startTime = parseInt($('#start-time-select').val());
        const endTime = parseInt($('#end-time-select').val());
        const selectedSlot = $('#slot-select').val();
        const evaluationStatus = $('#evaluationStatus').val();

        // Validate end time
        if (isNaN(endTime) || endTime <= startTime) {
            Swal.fire("Error!", "Please select a valid end time.", "error");
            return;
        }

        let allAvailable = true;

        // Check availability of the selected time slots
        for (let hour = startTime; hour < endTime; hour++) {
            const slotKey = `${hour}-${selectedDate.getTime()}-${selectedSlot}`;

            if (bookedSlots[slotKey]) {
                allAvailable = false;
                break;
            }
        }

        if (!allAvailable) {
            Swal.fire("Error!", "This slot is already booked.", "error");
            return;
        }

        // Create the booking and set isEvaluated to false
        for (let hour = startTime; hour < endTime; hour++) {
            const slotKey = `${hour}-${selectedDate.getTime()}-${selectedSlot}`;
            bookedSlots[slotKey] = {
                name,
                course,
                room,
                selectedDate,
                fromFacultyID,
                startTime,
                endTime,
                evaluationStatus,
                isEvaluated: false // Add the isEvaluated property
            };
        }
        // Save bookings to localStorage or your backend
        saveBookings();

        Swal.fire("Success!", "Booking has been successfully made!", "success").then(() => {
            location.reload();
            createReservationTable();
        });

        $('#reservationModal').modal('hide');
        // Prepare data for POST request
        const bookingData = {
            course: course,
            name: name,
            room: room,
            selected_date: selectedDate.toISOString().split('T')[0], // Format date as YYYY-MM-DD
            start_time: startTime,
            end_time: endTime,
            selected_slot: selectedSlot,
            evaluation_status: evaluationStatus
        };

        // Send booking data to the PHP backend
        $.ajax({
            type: 'POST',
            url: '../../controller/classroomObservation.php', // Change this to the path of your PHP script
            data: bookingData,
            success: function (response) {
                Swal.fire("Success!", "Booking has been successfully made!", "success").then(() => {
                    location.reload();
                    createReservationTable();
                });
            },
            error: function (xhr, status, error) {
                Swal.fire("Error!", "There was an error processing your request: " + error, "error");
            }
        });

        $('#reservationModal').modal('hide');
    }





    function openCancelModal(slotKey) {
        slotToCancel = slotKey; // Store the slot key to be cancelled

        if (bookedSlots[slotKey]) {
            const booking = bookedSlots[slotKey];

            const bookedName = "<?php echo htmlspecialchars($userRow['first_name'] . ' ' . $userRow['last_name'], ENT_QUOTES); ?>";

            if (booking.name === bookedName) {
                Swal.fire({
                    title: "Are you sure?",
                    text: "Once canceled, you will not be able to recover this booking!",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "Yes, cancel it!",
                    cancelButtonText: "No, keep it!",
                    reverseButtons: true,
                    focusCancel: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        cancelBooking(slotToCancel); // Call the function to cancel the booking
                    }
                });
            } else {
                Swal.fire("Error!", "You cannot cancel this booking.", "error");
            }
        }

        function cancelBooking(slotKey) {
            // Logic to remove the booking using the slotKey
            if (bookedSlots[slotKey]) {
                const booking = bookedSlots[slotKey];

                // Get the booked name from PHP and ensure it's properly quoted
                const bookedName = "<?php echo htmlspecialchars($userRow['first_name'] . ' ' . $userRow['last_name'], ENT_QUOTES); ?>";


                // Check if the booking name matches the user's G Suite account
                if (booking.name === bookedName) {
                    delete bookedSlots[slotKey]; // Remove the booking
                    saveBookings(); // Save the updated bookings
                    createReservationTable(); // Refresh the reservation table
                    Swal.fire("Success!", "Booking has been canceled.", "success");
                } else {
                    Swal.fire("Error!", "You cannot cancel this booking.", "error");
                }
            } else {
                Swal.fire("Error!", "No booking found for this slot.", "error");
            }
        }
    }


    // $(document).ready(function () {
    //     $('#clear-btn').on('click', clearBookings);
    // });
    function clearBookings() {
        Swal.fire({
            title: "Are you sure?",
            text: "Once cleared, you will not be able to recover your bookings!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, clear them!",
            cancelButtonText: "No, cancel!",
            reverseButtons: true,
            focusCancel: true,
        }).then((result) => {
            if (result.isConfirmed) {
                bookedSlots = {}; // Clear all bookings
                saveBookings(); // Save the changes (you should define this function)
                createReservationTable(); // Refresh the reservation table (you should define this function)
                Swal.fire("Success!", "All bookings cleared.", "success");
            }
        });
    }
</script>
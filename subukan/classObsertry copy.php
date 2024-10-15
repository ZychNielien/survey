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

    <div class="autobook my-3 d-flex justify-content-end">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
            data-bs-target="#preferredSchedule">Preffered
            Schedule for Autobooking</button>
    </div>

    <?php
    // Fetch preferred schedule from the database
    $preferredScheduleQuery = "SELECT * FROM preferredschedule WHERE faculty_Id = ?";
    $stmt = $con->prepare($preferredScheduleQuery);
    $stmt->bind_param("s", $userRow['faculty_Id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $preferredSchedule = $result->fetch_assoc();
    ?>

    <div class="modal fade" id="preferredSchedule" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="preferredScheduleLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-center text-white" id="preferredScheduleLabel">Preferred Schedule</h5>
                    <button type="button" class="btn-close bg-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="../../controller/facultyQuery.php">
                        <div hidden>
                            <input type="text" name="faculty_Id" value="<?php echo $userRow['faculty_Id'] ?>">
                            <input type="text" name="first_name" value="<?php echo $userRow['first_name'] ?>">
                            <input type="text" name="last_name" value="<?php echo $userRow['last_name'] ?>">
                        </div>
                        <h5 class="mt-3 fw-bold text-center">Please select your primary preferred schedule.</h5>
                        <div class="d-flex justify-content-between">
                            <div class="mb-3">
                                <select class="form-select" id="dayOfWeek" name="dayOfWeek" required>
                                    <option selected disabled value="">Select Day</option>
                                    <?php
                                    $daysOfWeek = ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday"];
                                    foreach ($daysOfWeek as $day) {
                                        $selected = (isset($preferredSchedule['dayOfWeek']) && $preferredSchedule['dayOfWeek'] == $day) ? 'selected' : '';
                                        echo "<option value=\"$day\" $selected>$day</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <select class="form-select" id="startTimePreferred" name="startTimePreferred" required>
                                    <option selected disabled value="">Select Start Time</option>
                                    <?php for ($i = 7; $i <= 19; $i++): ?>
                                        <option value="<?php echo $i; ?>" <?php echo (isset($preferredSchedule['startTimePreferred']) && $preferredSchedule['startTimePreferred'] == $i) ? 'selected' : ''; ?>>
                                            <?php echo date("g:i A", strtotime("$i:00")); ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <select class="form-select" id="endTimePreferred" name="endTimePreferred" required>
                                    <option selected disabled value="">Select End Time</option>
                                    <?php for ($i = 7; $i <= 19; $i++): ?>
                                        <option value="<?php echo $i; ?>" <?php echo (isset($preferredSchedule['endTimePreferred']) && $preferredSchedule['endTimePreferred'] == $i) ? 'selected' : ''; ?>>
                                            <?php echo date("g:i A", strtotime("$i:00")); ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>

                        <h5 class="mt-3 fw-bold text-center">Please select your secondary preferred schedule.</h5>
                        <div class="d-flex justify-content-between">
                            <div class="mb-3">
                                <select class="form-select" id="dayOfWeekTwo" name="dayOfWeekTwo" required>
                                    <option selected disabled value="">Select Day</option>
                                    <?php
                                    foreach ($daysOfWeek as $day) {
                                        $selected = (isset($preferredSchedule['dayOfWeekTwo']) && $preferredSchedule['dayOfWeekTwo'] == $day) ? 'selected' : '';
                                        echo "<option value=\"$day\" $selected>$day</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <select class="form-select" id="startTimeSecondary" name="startTimeSecondary" required>
                                    <option selected disabled value="">Select Start Time</option>
                                    <?php for ($i = 7; $i <= 19; $i++): ?>
                                        <option value="<?php echo $i; ?>" <?php echo (isset($preferredSchedule['startTimeSecondary']) && $preferredSchedule['startTimeSecondary'] == $i) ? 'selected' : ''; ?>>
                                            <?php echo date("g:i A", strtotime("$i:00")); ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <select class="form-select" id="endTimeSecondary" name="endTimeSecondary" required>
                                    <option selected disabled value="">Select End Time</option>
                                    <?php for ($i = 7; $i <= 19; $i++): ?>
                                        <option value="<?php echo $i; ?>" <?php echo (isset($preferredSchedule['endTimeSecondary']) && $preferredSchedule['endTimeSecondary'] == $i) ? 'selected' : ''; ?>>
                                            <?php echo date("g:i A", strtotime("$i:00")); ?>
                                        </option>
                                    <?php endfor; ?>
                                </select>
                            </div>
                        </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" name="preferredSched">Save</button>
                </div>
                </form>
            </div>
        </div>
    </div>



    <div class="d-flex justify-content-evenly align-items-center">
        <div class="form-group">
            <label for="date-select">Select Date:</label>
            <input type="date" class="form-control" id="date-select">
        </div>

        <div class="form-group">
            <label for="date-select">Select Date:</label>
            <input type="date" class="form-control" id="date-select-auto">
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
        <button class="btn btn-danger" id="clear-btn">Clear All Reservations</button>
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
                    <form id="form">
                        <div class="form-group">
                            <label for="course">Course Title:</label>
                            <input type="text" class="form-control" id="course" required>
                        </div>
                        <div class="form-group">
                            <label for="name">Instructor:</label>
                            <input type="text" class="form-control" id="name"
                                value="<?php echo $userRow["first_name"] . ' ' . $userRow["last_name"]; ?>" required>
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
    $(document).ready(function () {
        function populateEndTime(startTimeSelect, endTimeSelect) {
            $(startTimeSelect).change(function () {
                var selectedStartTime = parseInt($(this).val());

                // Clear existing options in the end time dropdown
                endTimeSelect.find('option').not(':first').remove();

                // Populate end time options based on the selected start time
                for (var i = selectedStartTime + 1; i <= 19; i++) { // Start from selectedStartTime + 1
                    endTimeSelect.append('<option value="' + i + '">' +
                        (i === 12 ? '12:00 PM' :
                            (i < 12 ? i + ':00 AM' : (i - 12) + ':00 PM')) +
                        '</option>');
                }
            });
        }

        // Populate end times for primary schedule
        populateEndTime('#startTimePreferred', $('#endTimePreferred'));

        // Populate end times for secondary schedule
        populateEndTime('#startTimeSecondary', $('#endTimeSecondary'));
    });
</script>


<script>




    const facultySchedules = [
        <?php
        $preferredScheduleSQL = "SELECT * FROM `preferredschedule`";
        $preferredScheduleSQL_query = mysqli_query($con, $preferredScheduleSQL);

        // Check if the query was successful and has rows
        if ($preferredScheduleSQL_query && mysqli_num_rows($preferredScheduleSQL_query) > 0) {
            while ($preferredScheduleRow = mysqli_fetch_assoc($preferredScheduleSQL_query)) {
                ?>
                                            {
                    name: "<?php echo $preferredScheduleRow['first_name'] . ' ' . $preferredScheduleRow['last_name'] ?>",
                    schedule: {
                        "<?php echo $preferredScheduleRow['dayOfWeek'] ?>": [
                            { start: <?php echo $preferredScheduleRow['startTimePreferred'] ?>, end: <?php echo $preferredScheduleRow['endTimePreferred'] ?> } // 11 AM to 4 PM
                        ],
                        "<?php echo $preferredScheduleRow['dayOfWeekTwo'] ?>": [
                            { start: <?php echo $preferredScheduleRow['startTimeSecondary'] ?>, end: <?php echo $preferredScheduleRow['endTimeSecondary'] ?> } // 9 AM to 3 PM
                        ]
                    }
                },
                <?php
            }
        }

        // Add other default schedules if there are no rows from the database
        ?>
    {
            name: "Jane Smith",
            schedule: {
                "Tuesday": [
                    { start: 9, end: 12 } // 10 AM to 2 PM
                ]
            }
        },
        {
            name: "May Smith",
            schedule: {
                "Tuesday": [
                    { start: 7, end: 11 } // 10 AM to 2 PM
                ]
            }
        },
        {
            name: "Sad",
            schedule: {
                "Tuesday": [
                    { start: 7, end: 9 } // 10 AM to 2 PM
                ],
                "Wednesday": [
                    { start: 9, end: 12 } // 9 AM to 3 PM
                ]
            }
        }
    ];



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
            checkStartTimeAvailability();
            updateSlotOptions();
        });

        $('#end-time-select').change(checkSlotAvailability);
        $('#slot-select').change(checkSlotAvailability);
        $('#book-btn').click(openForm);
        $('#form').submit(submitForm);
        $('#clear-btn').click(clearBookings);
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
        const days = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
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

                const cell1 = $('<td>').css('border', '2px solid white');
                const cell2 = $('<td>').css('border', '2px solid white');

                if (bookedSlots[slotKey1]) {
                    cell1.addClass('bg-danger text-white py-3').html(`${bookedSlots[slotKey1].name}<br>${bookedSlots[slotKey1].room}`)
                        .click(() => openCancelModal(slotKey1));
                } else {
                    cell1.addClass('bg-success text-white py-3').text('Available');
                }

                if (bookedSlots[slotKey2]) {
                    cell2.addClass('bg-danger text-white py-3').html(`${bookedSlots[slotKey2].name}<br>${bookedSlots[slotKey2].room}`)
                        .click(() => openCancelModal(slotKey2));
                } else {
                    cell2.addClass('bg-success text-white py-3').text('Available');
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

        for (let hour = startTime + 1; hour <= startTime + 3 && hour <= 19; hour++) {
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


        if (isNaN(startTime)) {
            $('#book-btn').prop('disabled', true);
            $('#slot-select').prop('disabled', true);
            return;
        }


        const slotKey1 = `${startTime}-${selectedDate.getTime()}-1`;
        const slotKey2 = `${startTime}-${selectedDate.getTime()}-2`;


        const isSlot1Booked = bookedSlots[slotKey1];
        const isSlot2Booked = bookedSlots[slotKey2];

        checkSlotAvailability();
    }

    function checkSlotAvailability() {
        const selectedDate = new Date($('#date-select').val());
        const startTime = parseInt($('#start-time-select').val());
        const endTime = parseInt($('#end-time-select').val());


        if (isNaN(startTime) || isNaN(endTime) || endTime <= startTime) {
            $('#book-btn').prop('disabled', true);
            $('#slot-select').prop('disabled', true);
            return;
        }


        const slotKey1 = `${startTime}-${selectedDate.getTime()}-1`;
        const slotKey2 = `${startTime}-${selectedDate.getTime()}-2`;


        const isSlot1Booked = bookedSlots[slotKey1];
        const isSlot2Booked = bookedSlots[slotKey2];


        if (isSlot1Booked && isSlot2Booked) {
            $('#slot-select').prop('disabled', true);
            $('#book-btn').prop('disabled', true);
            swal("Error!", "Both slots are already booked.", "error");
        } else {

            $('#slot-select').prop('disabled', false);
            $('#book-btn').prop('disabled', false);


            $('#slot-select option').each(function () {
                const slotValue = $(this).val();
                if ((slotValue === '1' && isSlot1Booked) || (slotValue === '2' && isSlot2Booked)) {
                    $(this).prop('disabled', true);
                } else {
                    $(this).prop('disabled', false);
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
        const selectedDate = new Date($('#date-select').val());
        const startTime = parseInt($('#start-time-select').val());
        const endTime = parseInt($('#end-time-select').val());
        const selectedSlot = $('#slot-select').val();
        const evaluationStatus = $('#evaluationStatus').val();


        if (isNaN(endTime) || endTime <= startTime) {
            Swal.fire("Error!", "Please select a valid end time.", "error");
            return;
        }

        let allAvailable = true;


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


        for (let hour = startTime; hour < endTime; hour++) {
            const slotKey = `${hour}-${selectedDate.getTime()}-${selectedSlot}`;
            bookedSlots[slotKey] = {
                name,
                course,
                room,
                selectedDate,
                startTime,
                endTime,
                evaluationStatus,
                isEvaluated: false
            };
        }


        saveBookings();

        Swal.fire("Success!", "Booking has been successfully made!", "success").then(() => {
            location.reload();
            createReservationTable();
        });

        $('#reservationModal').modal('hide');
    }

    function hasExistingBookingBeforeDate(date) {

        const userName = "<?php echo htmlspecialchars($userRow['first_name'] . ' ' . $userRow['last_name'], ENT_QUOTES); ?>";


        for (let key in bookedSlots) {
            const bookingDate = new Date(parseInt(key.split('-')[1]));

            if (bookingDate < date && bookedSlots[key].name === userName) {
                return true;
            }
        }
        return false;
    }

    function hasBookingForSelectedDate(selectedDate) {
        const userName = "<?php echo htmlspecialchars($userRow['first_name'] . ' ' . $userRow['last_name'], ENT_QUOTES); ?>";


        for (let key in bookedSlots) {
            const bookingDate = new Date(parseInt(key.split('-')[1]));
            if (bookingDate.toDateString() === selectedDate.toDateString() && bookedSlots[key].name === userName) {
                return true;
            }
        }
        return false;
    }

    $(document).ready(function () {
        // Set the value of #date-select-auto to today's date
        const today = new Date();
        const todayString = today.toISOString().split("T")[0];
        $('#date-select-auto');

        // Trigger auto-booking on page load
        checkAndAutoBook();

        // Re-trigger auto-booking when the date changes
        $('#date-select-auto').on('change', function () {
            checkAndAutoBook();
        });
    });

    function checkAndAutoBook() {
        const selectedDate = new Date($('#date-select-auto').val());

        facultySchedules.forEach(faculty => {
            if (!hasBookingForSelectedDate(selectedDate, faculty.name)) {
                console.log("Triggering auto-booking for", faculty.name, "on", selectedDate.toDateString());
                autoBookFullRange(selectedDate, faculty.name);
            } else {
                console.log("Booking already exists for", faculty.name, "on", selectedDate.toDateString());
            }
        });
    }

    function hasBookingForSelectedDate(selectedDate, facultyName) {
        for (let key in bookedSlots) {
            const bookingDate = new Date(parseInt(key.split('-')[1]));
            if (bookingDate.toDateString() === selectedDate.toDateString() && bookedSlots[key].name === facultyName) {
                return true;
            }
        }
        return false;
    }

    function isBookedInRange(facultyName, startDate, endDate) {
        return Object.entries(bookedSlots).some(([key, slot]) => {
            const bookedDate = new Date(parseInt(key.split('-')[0]));
            return slot.name === facultyName && bookedDate >= startDate && bookedDate <= endDate;
        });
    }

    function isBookedThisWeek(facultyName, selectedDate) {
        const startOfWeek = new Date(selectedDate);
        startOfWeek.setDate(startOfWeek.getDate() - startOfWeek.getDay()); // Set to Sunday
        const endOfWeek = new Date(startOfWeek);
        endOfWeek.setDate(startOfWeek.getDate() + 6); // Set to Saturday

        console.log(`Checking bookings for ${facultyName} from ${startOfWeek.toDateString()} to ${endOfWeek.toDateString()}`);

        for (let date = startOfWeek; date <= endOfWeek; date.setDate(date.getDate() + 1)) {
            const slotKey1 = `${date.getTime()}-1`;
            const slotKey2 = `${date.getTime()}-2`;

            if ((bookedSlots[slotKey1]?.name === facultyName) || (bookedSlots[slotKey2]?.name === facultyName)) {
                console.log(`${facultyName} is already booked this week.`);
                return true;
            }
        }
        return false;
    }

    function autoBookFullRange(selectedDate, facultyName) {
        if (isBookedThisWeek(facultyName, selectedDate)) {
            console.log(`${facultyName} cannot be booked again this week.`);
            return;
        }

        const faculty = facultySchedules.find(f => f.name === facultyName);
        if (!faculty) {
            console.log(`Faculty ${facultyName} not found.`);
            return;
        }

        let hasBookedThisWeek = false;
        let scheduleFound = false;

        for (let i = 0; i <= 6; i++) {
            if (hasBookedThisWeek) break;

            const currentDate = new Date(selectedDate);
            currentDate.setDate(selectedDate.getDate() + i);
            const dayOfWeek = currentDate.toLocaleString('default', { weekday: 'long' });

            console.log(`Attempting to book for ${facultyName} on ${dayOfWeek} (${currentDate.toDateString()})`);

            for (const [day, slots] of Object.entries(faculty.schedule)) {
                if (day === dayOfWeek) {
                    scheduleFound = true;
                    let slot1Booked = false;

                    let canBookInSlot1 = true;
                    let bookedHoursSlot1 = [];

                    for (const slot of slots) {
                        const { start, end } = slot;
                        for (let hour = start; hour < end; hour++) {
                            const slotKey1 = `${hour}-${currentDate.getTime()}-1`;
                            if (bookedSlots[slotKey1]) {
                                canBookInSlot1 = false;
                                break;
                            }
                            bookedHoursSlot1.push(hour);
                        }
                        if (!canBookInSlot1) break;
                    }

                    if (canBookInSlot1) {
                        for (const hour of bookedHoursSlot1) {
                            const slotKey1 = `${hour}-${currentDate.getTime()}-1`;
                            bookedSlots[slotKey1] = {
                                name: facultyName,
                                room: "Auto Booked",
                                selectedDate: currentDate,
                                startTime: hour,
                                endTime: hour + 1,
                                evaluationStatus: 'Pending',
                                isEvaluated: false
                            };
                        }
                        slot1Booked = true;
                        console.log(`Booked ${facultyName} in Slot 1 on ${currentDate.toDateString()} from ${bookedHoursSlot1[0]} to ${bookedHoursSlot1[bookedHoursSlot1.length - 1] + 1}.`);
                        hasBookedThisWeek = true;
                        break;
                    }

                    if (!slot1Booked) {
                        let bookedHoursSlot2 = [];
                        for (const slot of slots) {
                            const { start, end } = slot;
                            for (let hour = start; hour < end; hour++) {
                                const slotKey2 = `${hour}-${currentDate.getTime()}-2`;
                                if (!bookedSlots[slotKey2]) {
                                    bookedHoursSlot2.push(hour);
                                }
                            }
                        }

                        if (bookedHoursSlot2.length > 0) {
                            for (const hour of bookedHoursSlot2) {
                                const slotKey2 = `${hour}-${currentDate.getTime()}-2`;
                                bookedSlots[slotKey2] = {
                                    name: facultyName,
                                    room: "Auto Booked",
                                    selectedDate: currentDate,
                                    startTime: hour,
                                    endTime: hour + 1,
                                    evaluationStatus: 'Pending',
                                    isEvaluated: false
                                };
                            }
                            console.log(`Booked ${facultyName} in Slot 2 on ${currentDate.toDateString()} from ${bookedHoursSlot2[0]} to ${bookedHoursSlot2[bookedHoursSlot2.length - 1] + 1}.`);
                            hasBookedThisWeek = true;
                            break;
                        }
                    }
                }
            }

            if (!scheduleFound) {
                console.log(`No schedule found for ${facultyName} on ${dayOfWeek}.`);
            }
        }

        if (!hasBookedThisWeek) {
            console.log(`${facultyName} was not able to be booked for any day this week.`);
        }

        saveBookings();
        createReservationTable();
    }

    function bookThreeHourSlot(startHour, slotNumber, selectedDate) {
        const endHour = startHour + 3; // Book for 3 hours

        for (let hour = startHour; hour < endHour; hour++) {
            const slotKey = `${hour}-${selectedDate.getTime()}-${slotNumber}`;
            bookedSlots[slotKey] = {
                name: "<?php echo htmlspecialchars($userRow['first_name'] . ' ' . $userRow['last_name'], ENT_QUOTES); ?>",
                course: "Auto Booked",
                room: "Default Room",
                selectedDate: selectedDate,
                startTime: startHour,
                endTime: endHour,
                evaluationStatus: "Pending",
                isEvaluated: false
            };
        }

        saveBookings(); // Save the new booking
    }



    function openCancelModal(slotKey) {
        slotToCancel = slotKey;

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
                        cancelBooking(slotToCancel);
                    }
                });
            } else {
                Swal.fire("Error!", "You cannot cancel this booking.", "error");
            }
        }

        function cancelBooking(slotKey) {

            if (bookedSlots[slotKey]) {
                const booking = bookedSlots[slotKey];


                const bookedName = "<?php echo htmlspecialchars($userRow['first_name'] . ' ' . $userRow['last_name'], ENT_QUOTES); ?>";



                if (booking.name === bookedName) {
                    delete bookedSlots[slotKey];
                    saveBookings();
                    createReservationTable();
                    Swal.fire("Success!", "Booking has been canceled.", "success");
                } else {
                    Swal.fire("Error!", "You cannot cancel this booking.", "error");
                }
            } else {
                Swal.fire("Error!", "No booking found for this slot.", "error");
            }
        }
    }


    $(document).ready(function () {
        $('#clear-btn').on('click', clearBookings);
    });
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
                bookedSlots = {};
                saveBookings();
                createReservationTable();
                Swal.fire("Success!", "All bookings cleared.", "success");
            }
        });
    }




</script>
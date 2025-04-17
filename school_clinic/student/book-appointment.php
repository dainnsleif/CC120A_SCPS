<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

redirectIfNotLoggedIn();
if ($_SESSION['user_type'] != 'student') {
    header("Location: ../index.php");
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $appointment_date = $_POST['appointment_date'] . ' ' . $_POST['appointment_time'];
    $reason = $_POST['reason'];
    
    // Insert appointment
    $stmt = $conn->prepare("INSERT INTO appointments (user_id, appointment_date, reason) VALUES (?, ?, ?)");
    if ($stmt->execute([$_SESSION['user_id'], $appointment_date, $reason])) {
        $_SESSION['success_message'] = "Appointment booked successfully!";
        header("Location: dashboard.php");
        exit();
    } else {
        $error_message = "Failed to book appointment. Please try again.";
    }
}

// Get available time slots
$available_hours = ['09:00', '10:00', '11:00', '13:00', '14:00', '15:00'];
?>

<?php require_once '../includes/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card mt-5">
                <div class="card-header">
                    <h3>Book Appointment</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="appointment_date">Date</label>
                            <input type="date" class="form-control datepicker" id="appointment_date" name="appointment_date" required 
                                   min="<?php echo date('Y-m-d'); ?>" 
                                   max="<?php echo date('Y-m-d', strtotime('+30 days')); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="appointment_time">Time Slot</label>
                            <select class="form-control" id="appointment_time" name="appointment_time" required>
                                <option value="">Select Time Slot</option>
                                <?php foreach ($available_hours as $hour): ?>
                                    <option value="<?php echo $hour; ?>"><?php echo $hour; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="reason">Reason for Appointment</label>
                            <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Book Appointment</button>
                        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Disable weekends in datepicker
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        daysOfWeekDisabled: [0,6], // Disable Sunday and Saturday
        startDate: '+1d', // Start from tomorrow
        endDate: '+30d' // Only allow booking within 30 days
    });
    
    // Check available slots when date changes
    $('#appointment_date').change(function() {
        const selectedDate = $(this).val();
        if (selectedDate) {
            $.ajax({
                url: '../includes/check_availability.php',
                method: 'POST',
                data: { date: selectedDate },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const timeSelect = $('#appointment_time');
                        timeSelect.empty();
                        timeSelect.append('<option value="">Select Time Slot</option>');
                        
                        // Get all available hours
                        const allHours = <?php echo json_encode($available_hours); ?>;
                        
                        // Filter out booked slots
                        const bookedSlots = response.booked_slots;
                        const availableSlots = allHours.filter(hour => !bookedSlots.includes(hour));
                        
                        if (availableSlots.length > 0) {
                            availableSlots.forEach(function(slot) {
                                timeSelect.append(`<option value="${slot}">${slot}</option>`);
                            });
                        } else {
                            timeSelect.append('<option value="" disabled>No available slots for this date</option>');
                        }
                    }
                },
                error: function() {
                    alert('Error checking availability');
                }
            });
        }
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>
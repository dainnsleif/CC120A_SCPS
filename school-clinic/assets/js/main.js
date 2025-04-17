// Document ready function
$(document).ready(function() {
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();
    
    // Initialize datepickers
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true
    });
    
    // Initialize timepickers
    $('.timepicker').timepicker({
        showMeridian: false,
        minuteStep: 15
    });
    
    // Form validation
    $('.needs-validation').on('submit', function(e) {
        if (!this.checkValidity()) {
            e.preventDefault();
            e.stopPropagation();
        }
        $(this).addClass('was-validated');
    });
    
    // Appointment cancellation confirmation
    $('.cancel-appointment').on('click', function() {
        return confirm('Are you sure you want to cancel this appointment?');
    });
    
    // Ajax for dynamic content loading
    $('.nav-link').on('click', function(e) {
        e.preventDefault();
        const page = $(this).attr('href');
        $('#main-content').load(page);
    });
});

// Function to handle appointment booking
function bookAppointment() {
    const appointmentDate = $('#appointment_date').val();
    const reason = $('#reason').val();
    
    if (!appointmentDate || !reason) {
        alert('Please fill all required fields');
        return;
    }
    
    $.ajax({
        url: 'book-appointment.php',
        method: 'POST',
        data: {
            appointment_date: appointmentDate,
            reason: reason
        },
        success: function(response) {
            if (response.success) {
                alert('Appointment booked successfully!');
                window.location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        },
        error: function() {
            alert('An error occurred. Please try again.');
        }
    });
}
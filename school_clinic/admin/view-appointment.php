<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

// Ensure only admin can access this page
if (!isAdmin()) {
    header('Location: ../login.php');
    exit();
}

// Get appointment ID from URL
$appointment_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$appointment_id) {
    header('Location: manage-appointments.php');
    exit();
}

// Get appointment details
$query = "SELECT a.*, u.first_name, u.last_name, u.user_type, u.email, u.phone 
          FROM appointments a 
          JOIN users u ON a.user_id = u.user_id 
          WHERE a.appointment_id = " . $appointment_id;
$result = mysqli_query($conn, $query);
$appointment = mysqli_fetch_assoc($result);

if (!$appointment) {
    header('Location: manage-appointments.php');
    exit();
}

// Handle status updates
if (isset($_POST['update_status'])) {
    $new_status = $_POST['new_status'];
    $update_query = "UPDATE appointments SET status = '" . mysqli_real_escape_string($conn, $new_status) . "' 
                     WHERE appointment_id = " . $appointment_id;
    mysqli_query($conn, $update_query);
    header('Location: view-appointment.php?id=' . $appointment_id);
    exit();
}

$page_title = "View Appointment";
require_once '../includes/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Appointment Details</h2>
        <a href="manage-appointments.php" class="btn btn-secondary">Back to Appointments</a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Appointment Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Date & Time:</strong>
                        </div>
                        <div class="col-md-8">
                            <?php echo date('F j, Y h:i A', strtotime($appointment['appointment_date'])); ?>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Status:</strong>
                        </div>
                        <div class="col-md-8">
                            <form method="POST" class="d-inline">
                                <select name="new_status" class="form-select form-select-sm d-inline-block w-auto" onchange="this.form.submit()">
                                    <option value="pending" <?php echo $appointment['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                    <option value="approved" <?php echo $appointment['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                    <option value="completed" <?php echo $appointment['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="cancelled" <?php echo $appointment['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                                <input type="hidden" name="update_status" value="1">
                            </form>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Reason:</strong>
                        </div>
                        <div class="col-md-8">
                            <?php echo nl2br(htmlspecialchars($appointment['reason'])); ?>
                        </div>
                    </div>
                    <?php if ($appointment['notes']): ?>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Notes:</strong>
                        </div>
                        <div class="col-md-8">
                            <?php echo nl2br(htmlspecialchars($appointment['notes'])); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Patient Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Name:</strong><br>
                        <?php echo $appointment['first_name'] . ' ' . $appointment['last_name']; ?>
                    </div>
                    <div class="mb-3">
                        <strong>Type:</strong><br>
                        <?php echo ucfirst($appointment['user_type']); ?>
                    </div>
                    <div class="mb-3">
                        <strong>Email:</strong><br>
                        <?php echo $appointment['email']; ?>
                    </div>
                    <div class="mb-3">
                        <strong>Phone:</strong><br>
                        <?php echo $appointment['phone']; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 
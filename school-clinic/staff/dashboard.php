<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

redirectIfNotLoggedIn();
if ($_SESSION['user_type'] != 'staff') {
    header("Location: ../index.php");
    exit();
}

// Get staff information
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$staff = $stmt->fetch(PDO::FETCH_ASSOC);

// Get today's appointments
$today = date('Y-m-d');
$stmt = $conn->prepare("SELECT a.*, u.first_name, u.last_name, u.student_id, u.faculty_id 
                        FROM appointments a 
                        JOIN users u ON a.user_id = u.user_id 
                        WHERE DATE(a.appointment_date) = ? AND a.status = 'confirmed'
                        ORDER BY a.appointment_date ASC");
$stmt->execute([$today]);
$todays_appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get pending appointments
$stmt = $conn->prepare("SELECT a.*, u.first_name, u.last_name, u.student_id, u.faculty_id 
                        FROM appointments a 
                        JOIN users u ON a.user_id = u.user_id 
                        WHERE a.status = 'pending'
                        ORDER BY a.appointment_date ASC");
$stmt->execute();
$pending_appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Count different types of users
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM users WHERE user_type = 'student'");
$stmt->execute();
$student_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM users WHERE user_type = 'faculty'");
$stmt->execute();
$faculty_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM appointments WHERE status = 'pending'");
$stmt->execute();
$pending_appointment_count = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
?>

<?php require_once '../includes/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Staff Dashboard</h2>
            <hr>
        </div>
    </div>
    
    <div class="row">
        <!-- Quick Stats -->
        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Students</h5>
                            <h2 class="mb-0"><?php echo $student_count; ?></h2>
                        </div>
                        <i class="fas fa-user-graduate fa-3x"></i>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="students.php" class="text-white">View All <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Faculty</h5>
                            <h2 class="mb-0"><?php echo $faculty_count; ?></h2>
                        </div>
                        <i class="fas fa-chalkboard-teacher fa-3x"></i>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="faculty.php" class="text-white">View All <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Pending Appointments</h5>
                            <h2 class="mb-0"><?php echo $pending_appointment_count; ?></h2>
                        </div>
                        <i class="fas fa-calendar-check fa-3x"></i>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="appointments.php?status=pending" class="text-white">View All <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card text-white bg-info mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Today's Appointments</h5>
                            <h2 class="mb-0"><?php echo count($todays_appointments); ?></h2>
                        </div>
                        <i class="fas fa-calendar-day fa-3x"></i>
                    </div>
                </div>
                <div class="card-footer">
                    <a href="appointments.php?date=<?php echo $today; ?>" class="text-white">View All <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Today's Appointments -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h4>Today's Appointments</h4>
                </div>
                <div class="card-body">
                    <?php if (count($todays_appointments) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Time</th>
                                        <th>Patient</th>
                                        <th>ID</th>
                                        <th>Reason</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($todays_appointments as $appointment): ?>
                                        <tr>
                                            <td><?php echo date('h:i A', strtotime($appointment['appointment_date'])); ?></td>
                                            <td><?php echo $appointment['first_name'] . ' ' . $appointment['last_name']; ?></td>
                                            <td>
                                                <?php if ($appointment['student_id']): ?>
                                                    <?php echo $appointment['student_id']; ?>
                                                <?php elseif ($appointment['faculty_id']): ?>
                                                    <?php echo $appointment['faculty_id']; ?>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo strlen($appointment['reason']) > 20 ? substr($appointment['reason'], 0, 20) . '...' : $appointment['reason']; ?></td>
                                            <td>
                                                <a href="view-appointment.php?id=<?php echo $appointment['appointment_id']; ?>" class="btn btn-sm btn-primary">View</a>
                                                <a href="medical-record.php?appointment_id=<?php echo $appointment['appointment_id']; ?>" class="btn btn-sm btn-success">Add Record</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">No appointments scheduled for today.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Pending Appointments -->
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-warning text-white">
                    <h4>Pending Appointments</h4>
                </div>
                <div class="card-body">
                    <?php if (count($pending_appointments) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Patient</th>
                                        <th>Reason</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pending_appointments as $appointment): ?>
                                        <tr>
                                            <td><?php echo date('M j, h:i A', strtotime($appointment['appointment_date'])); ?></td>
                                            <td><?php echo $appointment['first_name'] . ' ' . $appointment['last_name']; ?></td>
                                            <td><?php echo strlen($appointment['reason']) > 20 ? substr($appointment['reason'], 0, 20) . '...' : $appointment['reason']; ?></td>
                                            <td>
                                                <a href="approve-appointment.php?id=<?php echo $appointment['appointment_id']; ?>" class="btn btn-sm btn-success">Approve</a>
                                                <a href="reject-appointment.php?id=<?php echo $appointment['appointment_id']; ?>" class="btn btn-sm btn-danger">Reject</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">No pending appointments.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
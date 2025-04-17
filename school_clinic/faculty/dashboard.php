<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

redirectIfNotLoggedIn();
if ($_SESSION['user_type'] != 'faculty') {
    header("Location: ../index.php");
    exit();
}

// Get faculty information
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$faculty = $stmt->fetch(PDO::FETCH_ASSOC);

// Get upcoming appointments
$stmt = $conn->prepare("SELECT a.*, u.first_name, u.last_name 
                        FROM appointments a 
                        LEFT JOIN users u ON a.staff_id = u.user_id 
                        WHERE a.user_id = ? AND a.status != 'completed' 
                        ORDER BY a.appointment_date ASC");
$stmt->execute([$_SESSION['user_id']]);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get medical records
$stmt = $conn->prepare("SELECT mr.*, u.first_name, u.last_name 
                        FROM medical_records mr 
                        LEFT JOIN users u ON mr.staff_id = u.user_id 
                        WHERE mr.user_id = ? 
                        ORDER BY mr.record_date DESC LIMIT 5");
$stmt->execute([$_SESSION['user_id']]);
$medical_records = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get medical history
$stmt = $conn->prepare("SELECT * FROM medical_history WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$medical_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php require_once '../includes/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Faculty Dashboard</h2>
            <hr>
        </div>
    </div>
    
    <div class="row">
        <!-- Faculty Profile -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h4>My Profile</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <img src="../assets/images/default-avatar.png" class="rounded-circle" width="150" height="150" alt="Profile Image">
                    </div>
                    <h4><?php echo $faculty['first_name'] . ' ' . $faculty['last_name']; ?></h4>
                    <p><strong>Faculty ID:</strong> <?php echo $faculty['faculty_id']; ?></p>
                    <p><strong>Email:</strong> <?php echo $faculty['email']; ?></p>
                    <p><strong>Phone:</strong> <?php echo $faculty['phone'] ? $faculty['phone'] : 'Not provided'; ?></p>
                    <a href="profile.php" class="btn btn-outline-primary btn-block">Edit Profile</a>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h4>Quick Actions</h4>
                </div>
                <div class="card-body">
                    <a href="book-appointment.php" class="btn btn-success btn-block mb-2">Book Appointment</a>
                    <a href="medical-records.php" class="btn btn-info btn-block mb-2">View Medical Records</a>
                    <a href="medical-history.php" class="btn btn-warning btn-block">Update Medical History</a>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-8">
            <!-- Upcoming Appointments -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h4>Upcoming Appointments</h4>
                </div>
                <div class="card-body">
                    <?php if (count($appointments) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>With</th>
                                        <th>Reason</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($appointments as $appointment): ?>
                                        <tr>
                                            <td><?php echo date('M j, Y h:i A', strtotime($appointment['appointment_date'])); ?></td>
                                            <td>
                                                <?php if ($appointment['staff_id']): ?>
                                                    <?php echo $appointment['first_name'] . ' ' . $appointment['last_name']; ?>
                                                <?php else: ?>
                                                    Not assigned
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo $appointment['reason']; ?></td>
                                            <td>
                                                <span class="badge 
                                                    <?php echo $appointment['status'] == 'confirmed' ? 'badge-success' : 
                                                          ($appointment['status'] == 'pending' ? 'badge-warning' : 'badge-danger'); ?>">
                                                    <?php echo ucfirst($appointment['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="view-appointment.php?id=<?php echo $appointment['appointment_id']; ?>" class="btn btn-sm btn-primary">View</a>
                                                <?php if ($appointment['status'] == 'pending' || $appointment['status'] == 'confirmed'): ?>
                                                    <a href="cancel-appointment.php?id=<?php echo $appointment['appointment_id']; ?>" class="btn btn-sm btn-danger">Cancel</a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p>No upcoming appointments found.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Recent Medical Records -->
            <div class="card mb-4">
                <div class="card-header bg-warning text-white">
                    <h4>Recent Medical Records</h4>
                </div>
                <div class="card-body">
                    <?php if (count($medical_records) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Diagnosis</th>
                                        <th>Treatment</th>
                                        <th>By</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($medical_records as $record): ?>
                                        <tr>
                                            <td><?php echo date('M j, Y', strtotime($record['record_date'])); ?></td>
                                            <td><?php echo strlen($record['diagnosis']) > 30 ? substr($record['diagnosis'], 0, 30) . '...' : $record['diagnosis']; ?></td>
                                            <td><?php echo strlen($record['treatment']) > 30 ? substr($record['treatment'], 0, 30) . '...' : $record['treatment']; ?></td>
                                            <td><?php echo $record['first_name'] . ' ' . $record['last_name']; ?></td>
                                            <td><a href="view-record.php?id=<?php echo $record['record_id']; ?>" class="btn btn-sm btn-info">View Details</a></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-right">
                            <a href="medical-records.php" class="btn btn-sm btn-outline-warning">View All Records</a>
                        </div>
                    <?php else: ?>
                        <p>No medical records found.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Medical History -->
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h4>Medical History</h4>
                </div>
                <div class="card-body">
                    <?php if (count($medical_history) > 0): ?>
                        <ul class="list-group">
                            <?php foreach ($medical_history as $history): ?>
                                <li class="list-group-item">
                                    <strong><?php echo $history['condition_name']; ?></strong> 
                                    (<?php echo ucfirst($history['condition_type']); ?>)
                                    <?php if ($history['severity']): ?>
                                        - Severity: <?php echo ucfirst($history['severity']); ?>
                                    <?php endif; ?>
                                    <?php if ($history['diagnosis_date']): ?>
                                        <br>Diagnosed on: <?php echo date('M j, Y', strtotime($history['diagnosis_date'])); ?>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No medical history recorded.</p>
                    <?php endif; ?>
                    <div class="mt-3">
                        <a href="medical-history.php" class="btn btn-sm btn-outline-danger">Update Medical History</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
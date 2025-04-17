<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

redirectIfNotLoggedIn();
if ($_SESSION['user_type'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

// Get total counts
$stmt = $conn->query("SELECT 
    (SELECT COUNT(*) FROM users WHERE user_type = 'student') as student_count,
    (SELECT COUNT(*) FROM users WHERE user_type = 'faculty') as faculty_count,
    (SELECT COUNT(*) FROM users WHERE user_type = 'staff') as staff_count,
    (SELECT COUNT(*) FROM appointments WHERE status = 'pending') as pending_appointments,
    (SELECT COUNT(*) FROM medical_records) as total_records
");
$counts = $stmt->fetch(PDO::FETCH_ASSOC);

// Get recent appointments
$stmt = $conn->query("SELECT a.*, u.first_name, u.last_name, u.user_type 
                      FROM appointments a 
                      LEFT JOIN users u ON a.user_id = u.user_id 
                      WHERE a.status = 'pending' 
                      ORDER BY a.appointment_date ASC 
                      LIMIT 5");
$recent_appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get recent registrations
$stmt = $conn->query("SELECT * FROM users 
                      WHERE registration_date >= DATE_SUB(NOW(), INTERVAL 7 DAY) 
                      ORDER BY registration_date DESC 
                      LIMIT 5");
$recent_registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php require_once '../includes/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Admin Dashboard</h2>
            <hr>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Students</h5>
                    <h2 class="card-text"><?php echo $counts['student_count']; ?></h2>
                    <a href="manage-users.php?type=student" class="btn btn-light btn-sm">Manage Students</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Faculty</h5>
                    <h2 class="card-text"><?php echo $counts['faculty_count']; ?></h2>
                    <a href="manage-users.php?type=faculty" class="btn btn-light btn-sm">Manage Faculty</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">Staff</h5>
                    <h2 class="card-text"><?php echo $counts['staff_count']; ?></h2>
                    <a href="manage-users.php?type=staff" class="btn btn-light btn-sm">Manage Staff</a>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">Pending Appointments</h5>
                    <h2 class="card-text"><?php echo $counts['pending_appointments']; ?></h2>
                    <a href="manage-appointments.php" class="btn btn-light btn-sm">View All</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Quick Actions -->
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Quick Actions</h4>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="add-user.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-user-plus"></i> Add New User
                        </a>
                        <a href="manage-appointments.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-calendar-check"></i> Manage Appointments
                        </a>
                        <a href="view-medical-records.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-file-medical"></i> View Medical Records
                        </a>
                        <a href="reports.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-chart-bar"></i> Generate Reports
                        </a>
                        <a href="system-settings.php" class="list-group-item list-group-item-action">
                            <i class="fas fa-cog"></i> System Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Appointments -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-warning text-white">
                    <h4 class="mb-0">Recent Pending Appointments</h4>
                </div>
                <div class="card-body">
                    <?php if (count($recent_appointments) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Patient</th>
                                        <th>Type</th>
                                        <th>Reason</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_appointments as $appointment): ?>
                                        <tr>
                                            <td><?php echo date('M j, Y h:i A', strtotime($appointment['appointment_date'])); ?></td>
                                            <td><?php echo $appointment['first_name'] . ' ' . $appointment['last_name']; ?></td>
                                            <td><?php echo ucfirst($appointment['user_type']); ?></td>
                                            <td><?php echo strlen($appointment['reason']) > 30 ? substr($appointment['reason'], 0, 30) . '...' : $appointment['reason']; ?></td>
                                            <td>
                                                <a href="view-appointment.php?id=<?php echo $appointment['appointment_id']; ?>" class="btn btn-info btn-sm">View</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end">
                            <a href="manage-appointments.php" class="btn btn-warning btn-sm">View All Appointments</a>
                        </div>
                    <?php else: ?>
                        <p class="mb-0">No pending appointments.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Registrations -->
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">Recent Registrations</h4>
                </div>
                <div class="card-body">
                    <?php if (count($recent_registrations) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Type</th>
                                        <th>Email</th>
                                        <th>Registration Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_registrations as $user): ?>
                                        <tr>
                                            <td><?php echo $user['first_name'] . ' ' . $user['last_name']; ?></td>
                                            <td><?php echo ucfirst($user['user_type']); ?></td>
                                            <td><?php echo $user['email']; ?></td>
                                            <td><?php echo date('M j, Y', strtotime($user['registration_date'])); ?></td>
                                            <td>
                                                <a href="view-user.php?id=<?php echo $user['user_id']; ?>" class="btn btn-info btn-sm">View</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end">
                            <a href="manage-users.php" class="btn btn-success btn-sm">View All Users</a>
                        </div>
                    <?php else: ?>
                        <p class="mb-0">No recent registrations.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 
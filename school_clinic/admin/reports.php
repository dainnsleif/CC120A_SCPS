<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

// Ensure only admin can access this page
if (!isAdmin()) {
    header('Location: ../login.php');
    exit();
}

// Get report parameters
$report_type = isset($_GET['report_type']) ? $_GET['report_type'] : '';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$user_type = isset($_GET['user_type']) ? $_GET['user_type'] : 'all';
$status = isset($_GET['status']) ? $_GET['status'] : 'all';

// Initialize variables
$report_data = [];
$report_title = '';
$report_description = '';

// Generate report based on type
if ($report_type && $date_from && $date_to) {
    switch ($report_type) {
        case 'appointments':
            $report_title = 'Appointments Report';
            $report_description = 'Report of appointments from ' . date('M j, Y', strtotime($date_from)) . ' to ' . date('M j, Y', strtotime($date_to));
            
            // Build query
            $query = "SELECT a.*, u.first_name, u.last_name, u.user_type, u.student_id 
                      FROM appointments a 
                      JOIN users u ON a.user_id = u.user_id 
                      WHERE a.appointment_date BETWEEN ? AND ?";
            $params = [$date_from, $date_to];
            
            if ($user_type !== 'all') {
                $query .= " AND u.user_type = ?";
                $params[] = $user_type;
            }
            
            if ($status !== 'all') {
                $query .= " AND a.status = ?";
                $params[] = $status;
            }
            
            $query .= " ORDER BY a.appointment_date ASC";
            $stmt = $conn->prepare($query);
            $stmt->execute($params);
            $report_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
            
        case 'medical_records':
            $report_title = 'Medical Records Report';
            $report_description = 'Report of medical records from ' . date('M j, Y', strtotime($date_from)) . ' to ' . date('M j, Y', strtotime($date_to));
            
            // Build query
            $query = "SELECT mr.*, u.first_name, u.last_name, u.user_type, u.student_id 
                      FROM medical_records mr 
                      JOIN users u ON mr.user_id = u.user_id 
                      WHERE mr.record_date BETWEEN ? AND ?";
            $params = [$date_from, $date_to];
            
            if ($user_type !== 'all') {
                $query .= " AND u.user_type = ?";
                $params[] = $user_type;
            }
            
            $query .= " ORDER BY mr.record_date ASC";
            $stmt = $conn->prepare($query);
            $stmt->execute($params);
            $report_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            break;
            
        case 'user_statistics':
            $report_title = 'User Statistics Report';
            $report_description = 'Report of user statistics as of ' . date('M j, Y', strtotime($date_to));
            
            // Get user counts by type
            $query = "SELECT user_type, COUNT(*) as count 
                      FROM users 
                      GROUP BY user_type";
            $stmt = $conn->prepare($query);
            $stmt->execute();
            $user_counts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get appointment counts by status
            $query = "SELECT status, COUNT(*) as count 
                      FROM appointments 
                      WHERE appointment_date <= ? 
                      GROUP BY status";
            $stmt = $conn->prepare($query);
            $stmt->execute([$date_to]);
            $appointment_counts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Get medical record counts by user type
            $query = "SELECT u.user_type, COUNT(*) as count 
                      FROM medical_records mr 
                      JOIN users u ON mr.user_id = u.user_id 
                      WHERE mr.record_date <= ? 
                      GROUP BY u.user_type";
            $stmt = $conn->prepare($query);
            $stmt->execute([$date_to]);
            $record_counts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $report_data = [
                'user_counts' => $user_counts,
                'appointment_counts' => $appointment_counts,
                'record_counts' => $record_counts
            ];
            break;
    }
}

$page_title = "Generate Reports";
require_once '../includes/header.php';
?>

<div class="container mt-4">
    <h2>Generate Reports</h2>
    
    <!-- Report Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Report Parameters</h5>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Report Type</label>
                    <select name="report_type" class="form-select" required>
                        <option value="">Select Report Type</option>
                        <option value="appointments" <?php echo $report_type === 'appointments' ? 'selected' : ''; ?>>Appointments Report</option>
                        <option value="medical_records" <?php echo $report_type === 'medical_records' ? 'selected' : ''; ?>>Medical Records Report</option>
                        <option value="user_statistics" <?php echo $report_type === 'user_statistics' ? 'selected' : ''; ?>>User Statistics Report</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">From Date</label>
                    <input type="date" name="date_from" class="form-control" value="<?php echo $date_from; ?>" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">To Date</label>
                    <input type="date" name="date_to" class="form-control" value="<?php echo $date_to; ?>" required>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">User Type</label>
                    <select name="user_type" class="form-select">
                        <option value="all" <?php echo $user_type === 'all' ? 'selected' : ''; ?>>All</option>
                        <option value="student" <?php echo $user_type === 'student' ? 'selected' : ''; ?>>Student</option>
                        <option value="faculty" <?php echo $user_type === 'faculty' ? 'selected' : ''; ?>>Faculty</option>
                        <option value="staff" <?php echo $user_type === 'staff' ? 'selected' : ''; ?>>Staff</option>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">Appointment Status</label>
                    <select name="status" class="form-select">
                        <option value="all" <?php echo $status === 'all' ? 'selected' : ''; ?>>All</option>
                        <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="approved" <?php echo $status === 'approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="completed" <?php echo $status === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?php echo $status === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>
                
                <div class="col-md-4">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary d-block w-100">Generate Report</button>
                </div>
            </form>
        </div>
    </div>
    
    <?php if ($report_type && !empty($report_data)): ?>
    <!-- Report Results -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"><?php echo $report_title; ?></h5>
            <button onclick="window.print()" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-print"></i> Print Report
            </button>
        </div>
        <div class="card-body">
            <p class="text-muted"><?php echo $report_description; ?></p>
            
            <?php if ($report_type === 'appointments'): ?>
            <!-- Appointments Report -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Patient</th>
                            <th>Type</th>
                            <th>ID Number</th>
                            <th>Reason</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($report_data as $appointment): ?>
                            <tr>
                                <td><?php echo date('M j, Y h:i A', strtotime($appointment['appointment_date'])); ?></td>
                                <td><?php echo htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']); ?></td>
                                <td><?php echo ucfirst(htmlspecialchars($appointment['user_type'])); ?></td>
                                <td><?php echo htmlspecialchars($appointment['student_id'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($appointment['reason']); ?></td>
                                <td><?php echo ucfirst(htmlspecialchars($appointment['status'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
            
            <?php if ($report_type === 'medical_records'): ?>
            <!-- Medical Records Report -->
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Patient</th>
                            <th>Type</th>
                            <th>ID Number</th>
                            <th>Diagnosis</th>
                            <th>Treatment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($report_data as $record): ?>
                            <tr>
                                <td><?php echo date('M j, Y', strtotime($record['record_date'])); ?></td>
                                <td><?php echo htmlspecialchars($record['first_name'] . ' ' . $record['last_name']); ?></td>
                                <td><?php echo ucfirst(htmlspecialchars($record['user_type'])); ?></td>
                                <td><?php echo htmlspecialchars($record['student_id'] ?? 'N/A'); ?></td>
                                <td><?php echo htmlspecialchars($record['diagnosis']); ?></td>
                                <td><?php echo htmlspecialchars($record['treatment']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
            
            <?php if ($report_type === 'user_statistics'): ?>
            <!-- User Statistics Report -->
            <div class="row">
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">User Counts</h5>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>User Type</th>
                                        <th>Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($report_data['user_counts'] as $count): ?>
                                        <tr>
                                            <td><?php echo ucfirst(htmlspecialchars($count['user_type'])); ?></td>
                                            <td><?php echo $count['count']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Appointment Status</h5>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Status</th>
                                        <th>Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($report_data['appointment_counts'] as $count): ?>
                                        <tr>
                                            <td><?php echo ucfirst(htmlspecialchars($count['status'])); ?></td>
                                            <td><?php echo $count['count']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Medical Records by User Type</h5>
                        </div>
                        <div class="card-body">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>User Type</th>
                                        <th>Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($report_data['record_counts'] as $count): ?>
                                        <tr>
                                            <td><?php echo ucfirst(htmlspecialchars($count['user_type'])); ?></td>
                                            <td><?php echo $count['count']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?> 
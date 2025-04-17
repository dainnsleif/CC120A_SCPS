<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

// Ensure only admin can access this page
if (!isAdmin()) {
    header('Location: ../login.php');
    exit();
}

// Get filter parameters
$status = isset($_GET['status']) ? $_GET['status'] : 'all';
$user_type = isset($_GET['user_type']) ? $_GET['user_type'] : 'all';
$date_from = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$date_to = isset($_GET['date_to']) ? $_GET['date_to'] : '';

// Build query
$query = "SELECT a.*, u.first_name, u.last_name, u.user_type 
          FROM appointments a 
          JOIN users u ON a.user_id = u.user_id 
          WHERE 1=1";
$params = [];

if ($status !== 'all') {
    $query .= " AND a.status = ?";
    $params[] = $status;
}
if ($user_type !== 'all') {
    $query .= " AND u.user_type = ?";
    $params[] = $user_type;
}
if ($date_from) {
    $query .= " AND a.appointment_date >= ?";
    $params[] = $date_from;
}
if ($date_to) {
    $query .= " AND a.appointment_date <= ?";
    $params[] = $date_to;
}

$query .= " ORDER BY a.appointment_date DESC";
$stmt = $conn->prepare($query);
$stmt->execute($params);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle status updates
if (isset($_POST['update_status'])) {
    $appointment_id = $_POST['appointment_id'];
    $new_status = $_POST['new_status'];
    $update_stmt = $conn->prepare("UPDATE appointments SET status = ? WHERE appointment_id = ?");
    $update_stmt->execute([$new_status, $appointment_id]);
    header('Location: manage-appointments.php');
    exit();
}

$page_title = "Manage Appointments";
require_once '../includes/header.php';
?>

<div class="container mt-4">
    <h2>Manage Appointments</h2>
    
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="all" <?php echo $status === 'all' ? 'selected' : ''; ?>>All</option>
                        <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="approved" <?php echo $status === 'approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="completed" <?php echo $status === 'completed' ? 'selected' : ''; ?>>Completed</option>
                        <option value="cancelled" <?php echo $status === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">User Type</label>
                    <select name="user_type" class="form-select">
                        <option value="all" <?php echo $user_type === 'all' ? 'selected' : ''; ?>>All</option>
                        <option value="student" <?php echo $user_type === 'student' ? 'selected' : ''; ?>>Student</option>
                        <option value="faculty" <?php echo $user_type === 'faculty' ? 'selected' : ''; ?>>Faculty</option>
                        <option value="staff" <?php echo $user_type === 'staff' ? 'selected' : ''; ?>>Staff</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">From Date</label>
                    <input type="date" name="date_from" class="form-control" value="<?php echo $date_from; ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">To Date</label>
                    <input type="date" name="date_to" class="form-control" value="<?php echo $date_to; ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary d-block w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Appointments Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Patient</th>
                            <th>Type</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $appointment): ?>
                            <tr>
                                <td><?php echo date('M j, Y h:i A', strtotime($appointment['appointment_date'])); ?></td>
                                <td><?php echo $appointment['first_name'] . ' ' . $appointment['last_name']; ?></td>
                                <td><?php echo ucfirst($appointment['user_type']); ?></td>
                                <td><?php echo strlen($appointment['reason']) > 30 ? substr($appointment['reason'], 0, 30) . '...' : $appointment['reason']; ?></td>
                                <td>
                                    <form method="POST" class="d-inline">
                                        <input type="hidden" name="appointment_id" value="<?php echo $appointment['appointment_id']; ?>">
                                        <select name="new_status" class="form-select form-select-sm" onchange="this.form.submit()">
                                            <option value="pending" <?php echo $appointment['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                                            <option value="approved" <?php echo $appointment['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                                            <option value="completed" <?php echo $appointment['status'] === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                            <option value="cancelled" <?php echo $appointment['status'] === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                        </select>
                                        <input type="hidden" name="update_status" value="1">
                                    </form>
                                </td>
                                <td>
                                    <a href="view-appointment.php?id=<?php echo $appointment['appointment_id']; ?>" class="btn btn-info btn-sm">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 
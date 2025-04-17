<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';

redirectIfNotLoggedIn();
if ($_SESSION['user_type'] != 'staff') {
    header("Location: ../../index.php");
    exit();
}

// Get filter parameters
$status = isset($_GET['status']) ? $_GET['status'] : '';
$date = isset($_GET['date']) ? $_GET['date'] : '';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build query based on filters
$query = "SELECT a.*, u.first_name, u.last_name, u.student_id, u.faculty_id 
          FROM appointments a 
          JOIN users u ON a.user_id = u.user_id 
          WHERE 1=1";
$params = [];

if (!empty($status)) {
    $query .= " AND a.status = ?";
    $params[] = $status;
}

if (!empty($date)) {
    $query .= " AND DATE(a.appointment_date) = ?";
    $params[] = $date;
}

if (!empty($search)) {
    $query .= " AND (u.first_name LIKE ? OR u.last_name LIKE ? OR u.student_id LIKE ? OR u.faculty_id LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

$query .= " ORDER BY a.appointment_date DESC";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php require_once '../../includes/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Appointment Management</h2>
            <hr>
        </div>
    </div>
    
    <!-- Filter Form -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Filter Appointments</h4>
                </div>
                <div class="card-body">
                    <form method="GET" action="">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="status">Status</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="">All Statuses</option>
                                        <option value="pending" <?php echo $status == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="confirmed" <?php echo $status == 'confirmed' ? 'selected' : ''; ?>>Confirmed</option>
                                        <option value="completed" <?php echo $status == 'completed' ? 'selected' : ''; ?>>Completed</option>
                                        <option value="cancelled" <?php echo $status == 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="date">Date</label>
                                    <input type="date" class="form-control" id="date" name="date" value="<?php echo $date; ?>">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="search">Search Patient</label>
                                    <input type="text" class="form-control" id="search" name="search" placeholder="Search by name or ID" value="<?php echo $search; ?>">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">Filter</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Appointments Table -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4>Appointments</h4>
                        <a href="add-appointment.php" class="btn btn-success">Add New Appointment</a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (count($appointments) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Patient</th>
                                        <th>ID</th>
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
                                            <td>
                                                <?php if ($appointment['student_id']): ?>
                                                    <?php echo $appointment['student_id']; ?>
                                                <?php elseif ($appointment['faculty_id']): ?>
                                                    <?php echo $appointment['faculty_id']; ?>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo $appointment['reason']; ?></td>
                                            <td>
                                                <span class="badge 
                                                    <?php echo $appointment['status'] == 'confirmed' ? 'badge-success' : 
                                                          ($appointment['status'] == 'pending' ? 'badge-warning' : 
                                                          ($appointment['status'] == 'completed' ? 'badge-info' : 'badge-danger')); ?>">
                                                    <?php echo ucfirst($appointment['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="view-appointment.php?id=<?php echo $appointment['appointment_id']; ?>" class="btn btn-sm btn-primary">View</a>
                                                <?php if ($appointment['status'] == 'pending'): ?>
                                                    <a href="approve-appointment.php?id=<?php echo $appointment['appointment_id']; ?>" class="btn btn-sm btn-success">Approve</a>
                                                    <a href="reject-appointment.php?id=<?php echo $appointment['appointment_id']; ?>" class="btn btn-sm btn-danger">Reject</a>
                                                <?php elseif ($appointment['status'] == 'confirmed'): ?>
                                                    <a href="medical-record.php?appointment_id=<?php echo $appointment['appointment_id']; ?>" class="btn btn-sm btn-info">Add Record</a>
                                                    <a href="complete-appointment.php?id=<?php echo $appointment['appointment_id']; ?>" class="btn btn-sm btn-secondary">Complete</a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">No appointments found matching your criteria.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../../includes/footer.php'; ?>
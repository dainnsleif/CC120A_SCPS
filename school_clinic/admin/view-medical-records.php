<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Ensure only admin can access this page
if (!isAdmin()) {
    header('Location: ../login.php');
    exit();
}

// Get filter parameters
$user_type = isset($_GET['user_type']) ? $_GET['user_type'] : 'all';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

try {
    // Build query
    $query = "SELECT mr.*, u.first_name, u.last_name, u.user_type, u.student_id 
              FROM medical_records mr 
              JOIN users u ON mr.user_id = u.user_id 
              WHERE 1=1";
    $params = [];

    if ($user_type !== 'all') {
        $query .= " AND u.user_type = ?";
        $params[] = $user_type;
    }

    if ($search) {
        $query .= " AND (u.first_name LIKE ? OR u.last_name LIKE ? OR u.student_id LIKE ?)";
        $search_param = "%$search%";
        $params[] = $search_param;
        $params[] = $search_param;
        $params[] = $search_param;
    }

    $query .= " ORDER BY mr.record_date DESC";
    $stmt = $conn->prepare($query);
    $stmt->execute($params);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Log the error
    error_log("Database Error: " . $e->getMessage());
    $error_message = "An error occurred while fetching medical records. Please try again later.";
    $records = [];
}

$page_title = "View Medical Records";
require_once '../includes/header.php';
?>

<div class="container mt-4">
    <h2>Medical Records</h2>
    
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>
    
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">User Type</label>
                    <select name="user_type" class="form-select">
                        <option value="all" <?php echo $user_type === 'all' ? 'selected' : ''; ?>>All</option>
                        <option value="student" <?php echo $user_type === 'student' ? 'selected' : ''; ?>>Student</option>
                        <option value="faculty" <?php echo $user_type === 'faculty' ? 'selected' : ''; ?>>Faculty</option>
                        <option value="staff" <?php echo $user_type === 'staff' ? 'selected' : ''; ?>>Staff</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" placeholder="Search by name or ID" value="<?php echo htmlspecialchars($search); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary d-block w-100">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Medical Records Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Patient Name</th>
                            <th>Type</th>
                            <th>ID Number</th>
                            <th>Diagnosis</th>
                            <th>Treatment</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($records)): ?>
                            <?php foreach ($records as $record): ?>
                                <tr>
                                    <td><?php echo date('M j, Y', strtotime($record['record_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($record['first_name'] . ' ' . $record['last_name']); ?></td>
                                    <td><?php echo ucfirst(htmlspecialchars($record['user_type'])); ?></td>
                                    <td><?php echo htmlspecialchars($record['student_id'] ?? 'N/A'); ?></td>
                                    <td><?php echo strlen($record['diagnosis']) > 30 ? htmlspecialchars(substr($record['diagnosis'], 0, 30)) . '...' : htmlspecialchars($record['diagnosis']); ?></td>
                                    <td><?php echo strlen($record['treatment']) > 30 ? htmlspecialchars(substr($record['treatment'], 0, 30)) . '...' : htmlspecialchars($record['treatment']); ?></td>
                                    <td>
                                        <a href="view-record.php?id=<?php echo $record['record_id']; ?>" class="btn btn-info btn-sm">View Details</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">No medical records found</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 
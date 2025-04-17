<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

redirectIfNotLoggedIn();
if ($_SESSION['user_type'] != 'student') {
    header("Location: ../index.php");
    exit();
}

$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_condition'])) {
        $condition_name = $_POST['condition_name'];
        $condition_type = $_POST['condition_type'];
        $severity = $_POST['severity'];
        $diagnosis_date = $_POST['diagnosis_date'];
        $notes = $_POST['notes'];

        try {
            $stmt = $conn->prepare("INSERT INTO medical_history (user_id, condition_name, condition_type, severity, diagnosis_date, notes) 
                                  VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $condition_name, $condition_type, $severity, $diagnosis_date, $notes]);
            $success_message = "Medical condition added successfully.";
        } catch (PDOException $e) {
            $error_message = "Error adding medical condition. Please try again.";
        }
    } elseif (isset($_POST['delete_condition'])) {
        $history_id = $_POST['history_id'];
        
        try {
            $stmt = $conn->prepare("DELETE FROM medical_history WHERE history_id = ? AND user_id = ?");
            $stmt->execute([$history_id, $_SESSION['user_id']]);
            $success_message = "Medical condition removed successfully.";
        } catch (PDOException $e) {
            $error_message = "Error removing medical condition. Please try again.";
        }
    }
}

// Get medical history
$stmt = $conn->prepare("SELECT * FROM medical_history WHERE user_id = ? ORDER BY diagnosis_date DESC");
$stmt->execute([$_SESSION['user_id']]);
$medical_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php require_once '../includes/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Medical History</h2>
                <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
            </div>
            <hr>
        </div>
    </div>

    <?php if ($success_message): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <div class="row">
        <!-- Add New Condition Form -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Add Medical Condition</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="condition_name" class="form-label">Condition Name</label>
                            <input type="text" class="form-control" id="condition_name" name="condition_name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="condition_type" class="form-label">Type</label>
                            <select class="form-control" id="condition_type" name="condition_type" required>
                                <option value="allergy">Allergy</option>
                                <option value="chronic">Chronic Condition</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="severity" class="form-label">Severity</label>
                            <select class="form-control" id="severity" name="severity">
                                <option value="">Select Severity</option>
                                <option value="mild">Mild</option>
                                <option value="moderate">Moderate</option>
                                <option value="severe">Severe</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="diagnosis_date" class="form-label">Diagnosis Date</label>
                            <input type="date" class="form-control" id="diagnosis_date" name="diagnosis_date">
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                        
                        <button type="submit" name="add_condition" class="btn btn-primary">Add Condition</button>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Medical History List -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h4 class="mb-0">Current Medical History</h4>
                </div>
                <div class="card-body">
                    <?php if (count($medical_history) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Condition</th>
                                        <th>Type</th>
                                        <th>Severity</th>
                                        <th>Diagnosis Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($medical_history as $history): ?>
                                        <tr>
                                            <td>
                                                <?php echo $history['condition_name']; ?>
                                                <?php if ($history['notes']): ?>
                                                    <i class="fas fa-info-circle text-info" data-bs-toggle="tooltip" title="<?php echo htmlspecialchars($history['notes']); ?>"></i>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo ucfirst($history['condition_type']); ?></td>
                                            <td><?php echo $history['severity'] ? ucfirst($history['severity']) : 'N/A'; ?></td>
                                            <td><?php echo $history['diagnosis_date'] ? date('M j, Y', strtotime($history['diagnosis_date'])) : 'N/A'; ?></td>
                                            <td>
                                                <form method="POST" action="" style="display: inline;">
                                                    <input type="hidden" name="history_id" value="<?php echo $history['history_id']; ?>">
                                                    <button type="submit" name="delete_condition" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to remove this condition?')">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <p class="mb-0">No medical history recorded. Use the form to add your medical conditions.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>

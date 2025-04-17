<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

redirectIfNotLoggedIn();
if ($_SESSION['user_type'] != 'student') {
    header("Location: ../index.php");
    exit();
}

// Get all medical records for the student
$stmt = $conn->prepare("SELECT mr.*, u.first_name, u.last_name 
                        FROM medical_records mr 
                        LEFT JOIN users u ON mr.staff_id = u.user_id 
                        WHERE mr.user_id = ? 
                        ORDER BY mr.record_date DESC");
$stmt->execute([$_SESSION['user_id']]);
$medical_records = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php require_once '../includes/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Medical Records</h2>
                <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
            </div>
            <hr>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <?php if (count($medical_records) > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Diagnosis</th>
                                        <th>Treatment</th>
                                        <th>Prescribed Medication</th>
                                        <th>Staff</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($medical_records as $record): ?>
                                        <tr>
                                            <td><?php echo date('M j, Y', strtotime($record['record_date'])); ?></td>
                                            <td><?php echo strlen($record['diagnosis']) > 50 ? substr($record['diagnosis'], 0, 50) . '...' : $record['diagnosis']; ?></td>
                                            <td><?php echo strlen($record['treatment']) > 50 ? substr($record['treatment'], 0, 50) . '...' : $record['treatment']; ?></td>
                                            <td><?php echo $record['prescribed_medication'] ? (strlen($record['prescribed_medication']) > 50 ? substr($record['prescribed_medication'], 0, 50) . '...' : $record['prescribed_medication']) : 'None'; ?></td>
                                            <td><?php echo $record['first_name'] . ' ' . $record['last_name']; ?></td>
                                            <td>
                                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#recordModal<?php echo $record['record_id']; ?>">
                                                    View Details
                                                </button>
                                            </td>
                                        </tr>

                                        <!-- Modal for record details -->
                                        <div class="modal fade" id="recordModal<?php echo $record['record_id']; ?>" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Medical Record Details</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <h6>Date:</h6>
                                                            <p><?php echo date('F j, Y', strtotime($record['record_date'])); ?></p>
                                                        </div>
                                                        <div class="mb-3">
                                                            <h6>Diagnosis:</h6>
                                                            <p><?php echo nl2br($record['diagnosis']); ?></p>
                                                        </div>
                                                        <div class="mb-3">
                                                            <h6>Treatment:</h6>
                                                            <p><?php echo nl2br($record['treatment']); ?></p>
                                                        </div>
                                                        <div class="mb-3">
                                                            <h6>Prescribed Medication:</h6>
                                                            <p><?php echo $record['prescribed_medication'] ? nl2br($record['prescribed_medication']) : 'None'; ?></p>
                                                        </div>
                                                        <div class="mb-3">
                                                            <h6>Notes:</h6>
                                                            <p><?php echo $record['notes'] ? nl2br($record['notes']) : 'No additional notes'; ?></p>
                                                        </div>
                                                        <div class="mb-3">
                                                            <h6>Attending Staff:</h6>
                                                            <p><?php echo $record['first_name'] . ' ' . $record['last_name']; ?></p>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <p class="mb-0">No medical records found.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 
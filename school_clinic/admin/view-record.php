<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

// Ensure only admin can access this page
if (!isAdmin()) {
    header('Location: ../login.php');
    exit();
}

// Get record ID from URL
$record_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$record_id) {
    header('Location: view-medical-records.php');
    exit();
}

// Get medical record details
$query = "SELECT mr.*, u.first_name, u.last_name, u.user_type, u.student_id, u.email, u.phone, u.date_of_birth, u.gender 
          FROM medical_records mr 
          JOIN users u ON mr.user_id = u.user_id 
          WHERE mr.record_id = ?";
$stmt = $conn->prepare($query);
$stmt->execute([$record_id]);
$record = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$record) {
    header('Location: view-medical-records.php');
    exit();
}

$page_title = "View Medical Record";
require_once '../includes/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Medical Record Details</h2>
        <a href="view-medical-records.php" class="btn btn-secondary">Back to Records</a>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Medical Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Date:</strong>
                        </div>
                        <div class="col-md-8">
                            <?php echo date('F j, Y', strtotime($record['record_date'])); ?>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Diagnosis:</strong>
                        </div>
                        <div class="col-md-8">
                            <?php echo nl2br(htmlspecialchars($record['diagnosis'])); ?>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Treatment:</strong>
                        </div>
                        <div class="col-md-8">
                            <?php echo nl2br(htmlspecialchars($record['treatment'])); ?>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Prescription:</strong>
                        </div>
                        <div class="col-md-8">
                            <?php echo nl2br(htmlspecialchars($record['prescription'] ?? 'No prescription')); ?>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <strong>Notes:</strong>
                        </div>
                        <div class="col-md-8">
                            <?php echo nl2br(htmlspecialchars($record['notes'] ?? 'No additional notes')); ?>
                        </div>
                    </div>
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
                        <?php echo $record['first_name'] . ' ' . $record['last_name']; ?>
                    </div>
                    <div class="mb-3">
                        <strong>Type:</strong><br>
                        <?php echo ucfirst($record['user_type']); ?>
                    </div>
                    <div class="mb-3">
                        <strong>ID Number:</strong><br>
                        <?php echo $record['student_id'] ?? 'N/A'; ?>
                    </div>
                    <div class="mb-3">
                        <strong>Email:</strong><br>
                        <?php echo $record['email']; ?>
                    </div>
                    <div class="mb-3">
                        <strong>Phone:</strong><br>
                        <?php echo $record['phone']; ?>
                    </div>
                    <div class="mb-3">
                        <strong>Date of Birth:</strong><br>
                        <?php echo date('F j, Y', strtotime($record['date_of_birth'])); ?>
                    </div>
                    <div class="mb-3">
                        <strong>Gender:</strong><br>
                        <?php echo ucfirst($record['gender']); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?> 
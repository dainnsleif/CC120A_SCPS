<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

redirectIfNotLoggedIn();
if ($_SESSION['user_type'] != 'staff') {
    header("Location: ../index.php");
    exit();
}

// Check if appointment_id is provided
$appointment_id = isset($_GET['appointment_id']) ? $_GET['appointment_id'] : null;

// Get appointment details if appointment_id exists
$appointment = null;
if ($appointment_id) {
    $stmt = $conn->prepare("SELECT a.*, u.first_name, u.last_name, u.user_id 
                            FROM appointments a 
                            JOIN users u ON a.user_id = u.user_id 
                            WHERE a.appointment_id = ?");
    $stmt->execute([$appointment_id]);
    $appointment = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get patient's medical history
$medical_history = [];
if ($appointment) {
    $stmt = $conn->prepare("SELECT * FROM medical_history WHERE user_id = ?");
    $stmt->execute([$appointment['user_id']]);
    $medical_history = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $diagnosis = $_POST['diagnosis'];
    $treatment = $_POST['treatment'];
    $prescribed_medication = $_POST['prescribed_medication'];
    $notes = $_POST['notes'];
    
    // Insert medical record
    $stmt = $conn->prepare("INSERT INTO medical_records (user_id, diagnosis, treatment, prescribed_medication, notes, staff_id) 
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$user_id, $diagnosis, $treatment, $prescribed_medication, $notes, $_SESSION['user_id']]);
    
    // Update appointment status if it was from an appointment
    if ($appointment_id) {
        $stmt = $conn->prepare("UPDATE appointments SET status = 'completed' WHERE appointment_id = ?");
        $stmt->execute([$appointment_id]);
    }
    
    $_SESSION['success_message'] = "Medical record added successfully!";
    header("Location: medical-records.php");
    exit();
}

require_once '../includes/header.php';
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2><?php echo $appointment ? 'Add Medical Record for Appointment' : 'Add New Medical Record'; ?></h2>
            <hr>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header">
                    <h4>Patient Information</h4>
                </div>
                <div class="card-body">
                    <?php if ($appointment): ?>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Name:</strong> <?php echo $appointment['first_name'] . ' ' . $appointment['last_name']; ?></p>
                                <p><strong>Appointment Date:</strong> <?php echo date('M j, Y h:i A', strtotime($appointment['appointment_date'])); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Reason for Visit:</strong> <?php echo $appointment['reason']; ?></p>
                            </div>
                        </div>
                    <?php else: ?>
                        <form id="patient-search-form">
                            <div class="form-group">
                                <label for="patient-search">Search Patient</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="patient-search" placeholder="Search by name or ID">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button" id="search-patient">Search</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div id="patient-results" style="display: none;">
                            <h5>Select Patient:</h5>
                            <div class="list-group" id="patient-list">
                                <!-- Patient results will appear here -->
                            </div>
                        </div>
                        <div id="selected-patient" style="display: none;">
                            <hr>
                            <h5>Selected Patient:</h5>
                            <div class="card">
                                <div class="card-body">
                                    <p id="selected-patient-info"></p>
                                    <input type="hidden" name="user_id" id="selected-user-id">
                                </div>
                            </div>
                        </div>
                        <!-- Add this inside the form, after the selected patient section -->
                        <div id="patient-history-container" class="mt-3"></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if (!empty($medical_history)): ?>
                <div class="card mt-4">
                    <div class="card-header">
                        <h4>Patient Medical History</h4>
                    </div>
                    <div class="card-body">
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
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="card mt-4">
                <div class="card-header">
                    <h4>Medical Record</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <?php if ($appointment): ?>
                            <input type="hidden" name="user_id" value="<?php echo $appointment['user_id']; ?>">
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label for="diagnosis">Diagnosis</label>
                            <textarea class="form-control" id="diagnosis" name="diagnosis" rows="3" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="treatment">Treatment</label>
                            <textarea class="form-control" id="treatment" name="treatment" rows="3" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="prescribed_medication">Prescribed Medication</label>
                            <textarea class="form-control" id="prescribed_medication" name="prescribed_medication" rows="2"></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Save Record</button>
                        <a href="<?php echo $appointment_id ? 'appointments.php' : 'medical-records.php'; ?>" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Patient search functionality
    $('#search-patient').click(function() {
        const searchTerm = $('#patient-search').val().trim();
        if (searchTerm.length < 2) {
            alert('Please enter at least 2 characters to search');
            return;
        }
        
        $.ajax({
            url: '../../includes/search_patient.php',
            method: 'POST',
            data: { search: searchTerm },
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data.length > 0) {
                    const patientList = $('#patient-list');
                    patientList.empty();
                    
                    response.data.forEach(function(patient) {
                        const type = patient.user_type === 'student' ? 
                            `Student ID: ${patient.student_id}` : 
                            `Faculty ID: ${patient.faculty_id}`;
                            
                        patientList.append(`
                            <a href="#" class="list-group-item list-group-item-action select-patient" 
                               data-id="${patient.user_id}" 
                               data-name="${patient.first_name} ${patient.last_name}"
                               data-type="${type}">
                                ${patient.first_name} ${patient.last_name} (${type})
                            </a>
                        `);
                    });
                    
                    $('#patient-results').show();
                } else {
                    $('#patient-results').hide();
                    alert('No patients found matching your search');
                }
            },
            error: function() {
                alert('Error searching for patients');
            }
        });
    });
    
    // Patient selection
    $(document).on('click', '.select-patient', function(e) {
        e.preventDefault();
        const userId = $(this).data('id');
        const userName = $(this).data('name');
        const userType = $(this).data('type');
        
        $('#selected-patient-info').html(`<strong>${userName}</strong><br>${userType}`);
        $('#selected-user-id').val(userId);
        $('#patient-results').hide();
        $('#selected-patient').show();
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>
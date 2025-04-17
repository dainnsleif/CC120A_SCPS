<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

redirectIfNotLoggedIn();
if ($_SESSION['user_type'] != 'student') {
    header("Location: ../index.php");
    exit();
}

// Get student information
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    
    // Update profile
    $stmt = $conn->prepare("UPDATE users SET 
                          first_name = ?, 
                          last_name = ?, 
                          phone = ?, 
                          address = ?, 
                          date_of_birth = ?, 
                          gender = ? 
                          WHERE user_id = ?");
    if ($stmt->execute([$first_name, $last_name, $phone, $address, $date_of_birth, $gender, $_SESSION['user_id']])) {
        $_SESSION['success_message'] = "Profile updated successfully!";
        $_SESSION['first_name'] = $first_name;
        $_SESSION['last_name'] = $last_name;
        header("Location: dashboard.php");
        exit();
    } else {
        $error_message = "Failed to update profile. Please try again.";
    }
}
?>

<?php require_once '../includes/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card mt-5">
                <div class="card-header">
                    <h3>My Profile</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="first_name">First Name</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" 
                                           value="<?php echo htmlspecialchars($student['first_name']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="last_name">Last Name</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" 
                                           value="<?php echo htmlspecialchars($student['last_name']); ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" class="form-control" id="email" 
                                   value="<?php echo htmlspecialchars($student['email']); ?>" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label for="student_id">Student ID</label>
                            <input type="text" class="form-control" id="student_id" 
                                   value="<?php echo htmlspecialchars($student['student_id']); ?>" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" 
                                   value="<?php echo htmlspecialchars($student['phone']); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="3"><?php echo htmlspecialchars($student['address']); ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date_of_birth">Date of Birth</label>
                                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                                           value="<?php echo htmlspecialchars($student['date_of_birth']); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="gender">Gender</label>
                                    <select class="form-control" id="gender" name="gender">
                                        <option value="">Select Gender</option>
                                        <option value="male" <?php echo $student['gender'] == 'male' ? 'selected' : ''; ?>>Male</option>
                                        <option value="female" <?php echo $student['gender'] == 'female' ? 'selected' : ''; ?>>Female</option>
                                        <option value="other" <?php echo $student['gender'] == 'other' ? 'selected' : ''; ?>>Other</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                    
                    <hr>
                    
                    <h4>Change Password</h4>
                    <form id="change-password-form">
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input type="password" class="form-control" id="current_password" required>
                        </div>
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" class="form-control" id="new_password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password</label>
                            <input type="password" class="form-control" id="confirm_password" required>
                        </div>
                        <button type="submit" class="btn btn-warning">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Change password form submission
    $('#change-password-form').submit(function(e) {
        e.preventDefault();
        
        const current_password = $('#current_password').val();
        const new_password = $('#new_password').val();
        const confirm_password = $('#confirm_password').val();
        
        if (new_password !== confirm_password) {
            alert('New passwords do not match');
            return;
        }
        
        $.ajax({
            url: '../includes/change_password.php',
            method: 'POST',
            data: {
                current_password: current_password,
                new_password: new_password
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    alert('Password changed successfully!');
                    $('#current_password').val('');
                    $('#new_password').val('');
                    $('#confirm_password').val('');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Error changing password');
            }
        });
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>
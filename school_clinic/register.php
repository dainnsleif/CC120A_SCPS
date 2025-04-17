<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    redirectBasedOnUserType();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $response = registerUser($_POST);
    if ($response['success']) {
        $_SESSION['success_message'] = $response['message'];
        header("Location: login.php");
        exit();
    } else {
        $error_message = $response['message'];
    }
}
?>

<?php require_once 'includes/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card mt-5">
                <div class="card-header">
                    <h3>Register Account</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="user_type">I am a:</label>
                            <select class="form-control" id="user_type" name="user_type" required>
                                <option value="">Select User Type</option>
                                <option value="student">Student</option>
                                <option value="faculty">Faculty</option>
                                <option value="staff">Staff</option>
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="first_name">First Name</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="last_name">Last Name</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        
                        <div class="form-group" id="student_id_group" style="display: none;">
                            <label for="student_id">Student ID</label>
                            <input type="text" class="form-control" id="student_id" name="student_id">
                        </div>
                        
                        <div class="form-group" id="staff_id_group" style="display: none;">
                            <label for="staff_id">Staff ID</label>
                            <input type="text" class="form-control" id="staff_id" name="staff_id">
                        </div>
                        
                        <div class="form-group" id="faculty_id_group" style="display: none;">
                            <label for="faculty_id">Faculty ID</label>
                            <input type="text" class="form-control" id="faculty_id" name="faculty_id">
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="confirm_password">Confirm Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date_of_birth">Date of Birth</label>
                                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select class="form-control" id="gender" name="gender">
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-block">Register</button>
                    </form>
                    
                    <div class="text-center mt-3">
                        <p>Already have an account? <a href="login.php">Login here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('user_type').addEventListener('change', function() {
    const studentIdGroup = document.getElementById('student_id_group');
    const staffIdGroup = document.getElementById('staff_id_group');
    const facultyIdGroup = document.getElementById('faculty_id_group');
    
    // Hide all ID groups first
    studentIdGroup.style.display = 'none';
    staffIdGroup.style.display = 'none';
    facultyIdGroup.style.display = 'none';
    
    // Remove required attribute from all ID fields
    document.getElementById('student_id').required = false;
    document.getElementById('staff_id').required = false;
    document.getElementById('faculty_id').required = false;
    
    // Show and set required for the selected user type
    if (this.value === 'student') {
        studentIdGroup.style.display = 'block';
        document.getElementById('student_id').required = true;
    } else if (this.value === 'staff') {
        staffIdGroup.style.display = 'block';
        document.getElementById('staff_id').required = true;
    } else if (this.value === 'faculty') {
        facultyIdGroup.style.display = 'block';
        document.getElementById('faculty_id').required = true;
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
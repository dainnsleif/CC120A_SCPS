<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

redirectIfNotLoggedIn();
if ($_SESSION['user_type'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $user_type = $_POST['user_type'];
    $student_id = $_POST['student_id'] ?? null;
    $faculty_id = $_POST['faculty_id'] ?? null;
    $phone = $_POST['phone'] ?? null;
    $address = $_POST['address'] ?? null;
    $date_of_birth = $_POST['date_of_birth'] ?? null;
    $gender = $_POST['gender'] ?? null;

    // Validate required fields
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($user_type)) {
        $error_message = "Please fill in all required fields.";
    } else {
        // Check if email exists
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $error_message = "Email already exists.";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            try {
                $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, user_type, student_id, faculty_id, phone, address, date_of_birth, gender) 
                                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $first_name,
                    $last_name,
                    $email,
                    $hashed_password,
                    $user_type,
                    $student_id,
                    $faculty_id,
                    $phone,
                    $address,
                    $date_of_birth,
                    $gender
                ]);
                
                $success_message = "User added successfully.";
                
                // Clear form data
                $_POST = array();
            } catch (PDOException $e) {
                $error_message = "Error adding user. Please try again.";
            }
        }
    }
}
?>

<?php require_once '../includes/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Add New User</h2>
                <a href="manage-users.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Users
                </a>
            </div>
            <hr>
        </div>
    </div>

    <?php if ($error_message): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <?php if ($success_message): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="first_name" class="form-label">First Name *</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo $_POST['first_name'] ?? ''; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="last_name" class="form-label">Last Name *</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo $_POST['last_name'] ?? ''; ?>" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo $_POST['email'] ?? ''; ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="password" class="form-label">Password *</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="user_type" class="form-label">User Type *</label>
                                <select class="form-control" id="user_type" name="user_type" required onchange="toggleIdField()">
                                    <option value="">Select Type</option>
                                    <option value="student" <?php echo (isset($_POST['user_type']) && $_POST['user_type'] == 'student') ? 'selected' : ''; ?>>Student</option>
                                    <option value="faculty" <?php echo (isset($_POST['user_type']) && $_POST['user_type'] == 'faculty') ? 'selected' : ''; ?>>Faculty</option>
                                    <option value="staff" <?php echo (isset($_POST['user_type']) && $_POST['user_type'] == 'staff') ? 'selected' : ''; ?>>Staff</option>
                                    <option value="admin" <?php echo (isset($_POST['user_type']) && $_POST['user_type'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div id="student_id_field" style="display: none;">
                                    <label for="student_id" class="form-label">Student ID</label>
                                    <input type="text" class="form-control" id="student_id" name="student_id" value="<?php echo $_POST['student_id'] ?? ''; ?>">
                                </div>
                                <div id="faculty_id_field" style="display: none;">
                                    <label for="faculty_id" class="form-label">Faculty ID</label>
                                    <input type="text" class="form-control" id="faculty_id" name="faculty_id" value="<?php echo $_POST['faculty_id'] ?? ''; ?>">
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo $_POST['phone'] ?? ''; ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="date_of_birth" class="form-label">Date of Birth</label>
                                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="<?php echo $_POST['date_of_birth'] ?? ''; ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <textarea class="form-control" id="address" name="address" rows="2"><?php echo $_POST['address'] ?? ''; ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-control" id="gender" name="gender">
                                <option value="">Select Gender</option>
                                <option value="male" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'male') ? 'selected' : ''; ?>>Male</option>
                                <option value="female" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'female') ? 'selected' : ''; ?>>Female</option>
                                <option value="other" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Add User</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleIdField() {
    const userType = document.getElementById('user_type').value;
    const studentIdField = document.getElementById('student_id_field');
    const facultyIdField = document.getElementById('faculty_id_field');
    
    studentIdField.style.display = 'none';
    facultyIdField.style.display = 'none';
    
    if (userType === 'student') {
        studentIdField.style.display = 'block';
    } else if (userType === 'faculty') {
        facultyIdField.style.display = 'block';
    }
}

// Call on page load to set initial state
toggleIdField();
</script>

<?php require_once '../includes/footer.php'; ?> 
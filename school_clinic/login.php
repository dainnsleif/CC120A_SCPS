<?php
require_once 'includes/config.php';
require_once 'includes/auth.php';

if (isLoggedIn()) {
    redirectBasedOnUserType();
}

$error_message = '';
$is_admin_login = isset($_GET['admin']) && $_GET['admin'] === 'true';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error_message = 'Please fill in all fields';
    } else {
        if (loginUser($email, $password)) {
            // If this is an admin login attempt, check if the user is an admin
            if ($is_admin_login && $_SESSION['user_type'] !== 'admin') {
                // Log out the user if they're not an admin
                session_destroy();
                $error_message = 'Access denied. This login is for administrators only.';
            } else {
                redirectBasedOnUserType();
            }
        } else {
            $error_message = 'Invalid email or password';
        }
    }
}
?>

<?php require_once 'includes/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-md-6 mx-auto">
            <div class="card mt-5">
                <div class="card-header <?php echo $is_admin_login ? 'bg-danger text-white' : ''; ?>">
                    <h3><?php echo $is_admin_login ? 'Admin Login' : 'Login'; ?></h3>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['success_message'])): ?>
                        <div class="alert alert-success">
                            <?php 
                            echo $_SESSION['success_message'];
                            unset($_SESSION['success_message']);
                            ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($error_message): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="form-group mb-3">
                            <label for="email">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        
                        <div class="form-group mb-3">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        
                        <button type="submit" class="btn <?php echo $is_admin_login ? 'btn-danger' : 'btn-primary'; ?> btn-block w-100">
                            <?php echo $is_admin_login ? 'Admin Login' : 'Login'; ?>
                        </button>
                    </form>
                    
                    <?php if (!$is_admin_login): ?>
                    <div class="text-center mt-3">
                        <p>Don't have an account? <a href="register.php">Register here</a></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>

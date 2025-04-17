<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Clinic Management System</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <?php
    // Determine the correct path to assets based on the current directory
    $assets_path = 'assets/css/style.css';
    if (strpos($_SERVER['PHP_SELF'], '/admin/') !== false || 
        strpos($_SERVER['PHP_SELF'], '/student/') !== false || 
        strpos($_SERVER['PHP_SELF'], '/staff/') !== false || 
        strpos($_SERVER['PHP_SELF'], '/faculty/') !== false) {
        $assets_path = '../assets/css/style.css';
    }
    ?>
    <link href="<?php echo $assets_path; ?>" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <?php
            // Determine the correct path to index.php based on the current directory
            $home_path = 'index.php';
            if (strpos($_SERVER['PHP_SELF'], '/admin/') !== false || 
                strpos($_SERVER['PHP_SELF'], '/student/') !== false || 
                strpos($_SERVER['PHP_SELF'], '/staff/') !== false || 
                strpos($_SERVER['PHP_SELF'], '/faculty/') !== false) {
                $home_path = '../index.php';
            }
            ?>
            <a class="navbar-brand" href="<?php echo $home_path; ?>">School Clinic</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $_SESSION['user_type']; ?>/dashboard.php">Dashboard</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-danger text-white ms-2" href="#" onclick="confirmLogout()">Logout</a>
                        </li>
                    <?php else: ?>
                        <?php
                        // Determine the correct path to login.php and register.php
                        $login_path = 'login.php';
                        $register_path = 'register.php';
                        if (strpos($_SERVER['PHP_SELF'], '/admin/') !== false || 
                            strpos($_SERVER['PHP_SELF'], '/student/') !== false || 
                            strpos($_SERVER['PHP_SELF'], '/staff/') !== false || 
                            strpos($_SERVER['PHP_SELF'], '/faculty/') !== false) {
                            $login_path = '../login.php';
                            $register_path = '../register.php';
                        }
                        ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $login_path; ?>">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo $register_path; ?>">Register</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link btn btn-danger text-white ms-2" href="<?php echo $login_path; ?>?admin=true">Admin Login</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main class="py-4"> 
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script>
        function confirmLogout() {
            if (confirm('Are you sure you want to logout?')) {
                // Get the current directory path
                const currentPath = window.location.pathname;
                let logoutPath = 'logout.php';
                
                // Check which section we're in and set the appropriate logout path
                if (currentPath.includes('/admin/')) {
                    logoutPath = 'logout.php';  // We're already in the admin directory
                } else if (currentPath.includes('/student/')) {
                    logoutPath = 'logout.php';  // We're already in the student directory
                } else if (currentPath.includes('/staff/')) {
                    logoutPath = 'logout.php';  // We're already in the staff directory
                } else if (currentPath.includes('/faculty/')) {
                    logoutPath = 'logout.php';  // We're already in the faculty directory
                }
                
                window.location.href = logoutPath;
            }
        }
    </script>
</body>
</html> 
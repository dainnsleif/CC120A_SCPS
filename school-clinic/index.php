<?php require_once 'includes/header.php'; ?>

<div class="container">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card mt-5">
                <div class="card-header">
                    <h3>Welcome to School Clinic Management System</h3>
                </div>
                <div class="card-body">
                    <p class="lead">Efficient management of student and faculty health records and appointments.</p>
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="feature-box">
                                <i class="fas fa-calendar-check fa-3x"></i>
                                <h4>Appointment Management</h4>
                                <p>Schedule and manage clinic appointments with ease.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="feature-box">
                                <i class="fas fa-file-medical fa-3x"></i>
                                <h4>Medical Records</h4>
                                <p>Digital storage and management of health records.</p>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-4">
                        <a href="login.php" class="btn btn-primary btn-lg mr-3">Login</a>
                        <a href="register.php" class="btn btn-success btn-lg">Register</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
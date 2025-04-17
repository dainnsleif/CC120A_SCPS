<?php
// Display all errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Try to include the config file
try {
    require_once 'includes/config.php';
} catch (Exception $e) {
    die("Error including config file: " . $e->getMessage());
}

// Check if the script is being run from the command line or directly
$is_cli = php_sapi_name() === 'cli';
$is_direct_access = isset($_GET['direct_access']) && $_GET['direct_access'] === 'true';

if (!$is_cli && !$is_direct_access) {
    die("This script can only be run from the command line or with direct_access=true parameter.");
}

// Check if database connection was established
if (!isset($conn) || !$conn) {
    echo "Database connection not established. Trying alternative connection method...<br>";
    
    // Try alternative connection method
    try {
        $conn = new PDO("mysql:host=127.0.0.1;dbname=school_clinic", "root", "");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Alternative connection successful!<br>";
    } catch(PDOException $e) {
        die("Alternative connection also failed: " . $e->getMessage() . "<br>
             Please check if MySQL is running and the database 'school_clinic' exists.<br>
             You can start MySQL using XAMPP Control Panel or by running 'net start mysql' in Command Prompt.");
    }
}

// Admin user details
$first_name = "Admin";
$last_name = "User";
$email = "admin@gmail.com";
$password = "admin12345";
$user_type = "admin";
$phone = "";
$address = "";
$date_of_birth = null;
$gender = "";

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Check if admin already exists
try {
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        echo "An admin user with this email already exists. Please use the existing admin account or modify this script to use a different email.<br>";
        exit;
    }
} catch (PDOException $e) {
    die("Error checking for existing admin: " . $e->getMessage());
}

// Insert admin user
try {
    $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, password, user_type, phone, address, date_of_birth, gender) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $first_name,
        $last_name,
        $email,
        $hashed_password,
        $user_type,
        $phone,
        $address,
        $date_of_birth,
        $gender
    ]);
    
    echo "Admin user created successfully!<br>";
    echo "Email: " . $email . "<br>";
    echo "Password: " . $password . "<br>";
    echo "Please change the password after logging in.<br>";
} catch (PDOException $e) {
    die("Error creating admin user: " . $e->getMessage());
}
?> 
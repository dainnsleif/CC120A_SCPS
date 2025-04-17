<?php
require_once 'config.php';

function registerUser($data) {
    global $conn;
    
    // Validate input
    $required = ['first_name', 'last_name', 'email', 'password', 'user_type'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            return ['success' => false, 'message' => "$field is required"];
        }
    }
    
    // Check if email exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->execute([$data['email']]);
    if ($stmt->rowCount() > 0) {
        return ['success' => false, 'message' => 'Email already exists'];
    }
    
    // Hash password
    $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
    
    // Prepare query based on user type
    if ($data['user_type'] == 'student') {
        $sql = "INSERT INTO users (first_name, last_name, email, password, user_type, student_id, phone, date_of_birth, gender) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params = [
            $data['first_name'], 
            $data['last_name'], 
            $data['email'], 
            $hashed_password, 
            $data['user_type'],
            $data['student_id'],
            $data['phone'],
            $data['date_of_birth'],
            $data['gender']
        ];
    } else if ($data['user_type'] == 'faculty') {
        $sql = "INSERT INTO users (first_name, last_name, email, password, user_type, faculty_id, phone, date_of_birth, gender) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params = [
            $data['first_name'], 
            $data['last_name'], 
            $data['email'], 
            $hashed_password, 
            $data['user_type'],
            $data['faculty_id'],
            $data['phone'],
            $data['date_of_birth'],
            $data['gender']
        ];
    } else if ($data['user_type'] == 'staff') {
        // Check if staff_id column exists
        try {
            $check_column = $conn->query("SHOW COLUMNS FROM users LIKE 'staff_id'");
            if ($check_column->rowCount() > 0) {
                $sql = "INSERT INTO users (first_name, last_name, email, password, user_type, staff_id, phone, date_of_birth, gender) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $params = [
                    $data['first_name'], 
                    $data['last_name'], 
                    $data['email'], 
                    $hashed_password, 
                    $data['user_type'],
                    $data['staff_id'],
                    $data['phone'],
                    $data['date_of_birth'],
                    $data['gender']
                ];
            } else {
                // If staff_id column doesn't exist, insert without it
                $sql = "INSERT INTO users (first_name, last_name, email, password, user_type, phone, date_of_birth, gender) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $params = [
                    $data['first_name'], 
                    $data['last_name'], 
                    $data['email'], 
                    $hashed_password, 
                    $data['user_type'],
                    $data['phone'],
                    $data['date_of_birth'],
                    $data['gender']
                ];
            }
        } catch (PDOException $e) {
            // If there's an error checking the column, insert without staff_id
            $sql = "INSERT INTO users (first_name, last_name, email, password, user_type, phone, date_of_birth, gender) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $params = [
                $data['first_name'], 
                $data['last_name'], 
                $data['email'], 
                $hashed_password, 
                $data['user_type'],
                $data['phone'],
                $data['date_of_birth'],
                $data['gender']
            ];
        }
    }
    
    // Execute query
    $stmt = $conn->prepare($sql);
    if ($stmt->execute($params)) {
        return ['success' => true, 'message' => 'Registration successful'];
    } else {
        return ['success' => false, 'message' => 'Registration failed'];
    }
}

function loginUser($email, $password) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_type'] = $user['user_type'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];
        return true;
    }
    
    return false;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function redirectIfNotLoggedIn() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

function redirectBasedOnUserType() {
    if (isLoggedIn()) {
        $user_type = $_SESSION['user_type'];
        switch ($user_type) {
            case 'admin':
                header("Location: admin/dashboard.php");
                break;
            case 'staff':
                header("Location: staff/dashboard.php");
                break;
            case 'student':
                header("Location: student/dashboard.php");
                break;
            case 'faculty':
                header("Location: faculty/dashboard.php");
                break;
            default:
                header("Location: index.php");
        }
        exit();
    }
}

function isAdmin() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}

?>
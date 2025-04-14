<?php
require_once 'config.php';

header('Content-Type: application/json');

$response = array();

// Login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $username = $data['username'];
    $password = $data['password'];
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        if (password_verify($password, $user['password'])) {
            // Update last login
            $update_stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?");
            $update_stmt->bind_param("i", $user['user_id']);
            $update_stmt->execute();
            $update_stmt->close();
            
            // Start session and set user data
            session_start();
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            
            $response = array(
                'status' => 'success',
                'message' => 'Login successful',
                'user' => array(
                    'user_id' => $user['user_id'],
                    'full_name' => $user['full_name'],
                    'role' => $user['role']
                )
            );
        } else {
            $response = array(
                'status' => 'error',
                'message' => 'Invalid password'
            );
        }
    } else {
        $response = array(
            'status' => 'error',
            'message' => 'User not found'
        );
    }
    $stmt->close();
}

// Logout
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_start();
    session_unset();
    session_destroy();
    
    $response = array(
        'status' => 'success',
        'message' => 'Logged out successfully'
    );
}

echo json_encode($response);
$conn->close();
?>
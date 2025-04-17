<?php
require_once 'includes/config.php';

// Function to create a default user
function createDefaultUser($conn, $firstName, $lastName, $email, $password, $userType, $idField = null, $idValue = null) {
    // Check if user already exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        echo "User $email already exists. Skipping.<br>";
        return;
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Prepare SQL based on whether an ID field is provided
    if ($idField) {
        $sql = "INSERT INTO users (first_name, last_name, email, password, user_type, $idField, phone, date_of_birth, gender, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params = [
            $firstName,
            $lastName,
            $email,
            $hashedPassword,
            $userType,
            $idValue,
            '1234567890',
            '1990-01-01',
            'male',
            'active'
        ];
    } else {
        $sql = "INSERT INTO users (first_name, last_name, email, password, user_type, phone, date_of_birth, gender, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params = [
            $firstName,
            $lastName,
            $email,
            $hashedPassword,
            $userType,
            '1234567890',
            '1990-01-01',
            'male',
            'active'
        ];
    }
    
    // Execute query
    $stmt = $conn->prepare($sql);
    if ($stmt->execute($params)) {
        echo "Created $userType user: $email with password: $password<br>";
    } else {
        echo "Failed to create $userType user: $email<br>";
    }
}

// Create default users
echo "<h2>Creating Default Users</h2>";

// Admin user
createDefaultUser($conn, 'Admin', 'User', 'admin@gmail.com', 'admin12345', 'admin');

// Staff user
createDefaultUser($conn, 'Staff', 'User', 'staff@gmail.com', 'admin12345', 'staff', 'staff_id', 'STAFF001');

// Faculty user
createDefaultUser($conn, 'Faculty', 'User', 'faculty@gmail.com', 'faculty12345', 'faculty', 'faculty_id', 'FAC001');

// Student user
createDefaultUser($conn, 'Student', 'User', 'student@gmail.com', 'student12345', 'student', 'student_id', 'STU001');

echo "<h3>Default Users Created Successfully!</h3>";
echo "<p>You can now log in with any of these accounts:</p>";
echo "<ul>";
echo "<li>Admin: admin@gmail.com / admin12345</li>";
echo "<li>Staff: staff@gmail.com / admin12345</li>";
echo "<li>Faculty: faculty@gmail.com / faculty12345</li>";
echo "<li>Student: student@gmail.com / student12345</li>";
echo "</ul>";
echo "<p><a href='login.php'>Go to Login Page</a></p>";
?> 
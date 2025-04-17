<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) {
    $searchTerm = '%' . $_POST['search'] . '%';
    
    try {
        // Search for patients (students and faculty) by name or ID
        $stmt = $conn->prepare("SELECT user_id, first_name, last_name, email, user_type, 
                               student_id, faculty_id 
                               FROM users 
                               WHERE (user_type = 'student' OR user_type = 'faculty')
                               AND (first_name LIKE ? OR last_name LIKE ? 
                               OR student_id LIKE ? OR faculty_id LIKE ?)
                               LIMIT 20");
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if ($patients) {
            echo json_encode([
                'success' => true,
                'data' => $patients
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No patients found matching your search'
            ]);
        }
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Database error occurred'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
}
?>
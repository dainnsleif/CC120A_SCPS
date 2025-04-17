<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['user_id'])) {
    $user_id = $_POST['user_id'];
    
    try {
        // Verify the user exists and is a patient (student or faculty)
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = ? AND (user_type = 'student' OR user_type = 'faculty')");
        $stmt->execute([$user_id]);
        
        if ($stmt->rowCount() === 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Patient not found'
            ]);
            exit();
        }
        
        // Get patient's medical history
        $stmt = $conn->prepare("SELECT * FROM medical_history WHERE user_id = ? ORDER BY condition_name");
        $stmt->execute([$user_id]);
        $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format dates for display
        foreach ($history as &$record) {
            if ($record['diagnosis_date']) {
                $record['diagnosis_date'] = date('M j, Y', strtotime($record['diagnosis_date']));
            }
        }
        
        echo json_encode([
            'success' => true,
            'data' => $history
        ]);
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
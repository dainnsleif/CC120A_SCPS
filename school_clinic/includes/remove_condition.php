<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $condition_id = $_POST['id'];
    
    // Verify the condition belongs to the current user
    $stmt = $conn->prepare("SELECT user_id FROM medical_history WHERE history_id = ?");
    $stmt->execute([$condition_id]);
    $condition = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$condition || $condition['user_id'] != $_SESSION['user_id']) {
        echo json_encode([
            'success' => false,
            'message' => 'Unauthorized action'
        ]);
        exit();
    }
    
    // Delete the condition
    $stmt = $conn->prepare("DELETE FROM medical_history WHERE history_id = ?");
    if ($stmt->execute([$condition_id])) {
        echo json_encode([
            'success' => true,
            'message' => 'Condition removed successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to remove condition'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
}
?>
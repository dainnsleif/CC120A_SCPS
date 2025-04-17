<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['date'])) {
    $date = $_POST['date'];
    
    // Get booked time slots for the selected date
    $stmt = $conn->prepare("SELECT TIME(appointment_date) as time 
                           FROM appointments 
                           WHERE DATE(appointment_date) = ? 
                           AND status IN ('pending', 'confirmed')");
    $stmt->execute([$date]);
    $booked_slots = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Convert times to simple hour format (e.g., "09:00:00" to "09:00")
    $booked_slots = array_map(function($time) {
        return substr($time, 0, 5);
    }, $booked_slots);
    
    echo json_encode([
        'success' => true,
        'booked_slots' => $booked_slots
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
}
?>
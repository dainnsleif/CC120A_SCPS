<?php
require_once 'config.php';

header('Content-Type: application/json');

$response = array();

// Get all appointments
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $status = isset($_GET['status']) ? $_GET['status'] : null;
    $date = isset($_GET['date']) ? $_GET['date'] : null;
    
    $sql = "SELECT a.*, CONCAT(s.first_name, ' ', s.last_name) AS student_name, s.grade_level, s.section
            FROM appointments a
            JOIN students s ON a.student_id = s.student_id";
            
    $conditions = array();
    $params = array();
    $types = '';
    
    if ($status) {
        $conditions[] = "a.status = ?";
        $params[] = $status;
        $types .= 's';
    }
    
    if ($date) {
        $conditions[] = "a.appointment_date = ?";
        $params[] = $date;
        $types .= 's';
    }
    
    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }
    
    $sql .= " ORDER BY a.appointment_date, a.appointment_time";
    
    $stmt = $conn->prepare($sql);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    $appointments = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $appointments[] = $row;
        }
    }
    
    $response = array(
        'status' => 'success',
        'data' => $appointments
    );
    $stmt->close();
}

// Add new appointment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $student_id = $data['student_id'];
    $appointment_date = $data['appointment_date'];
    $appointment_time = $data['appointment_time'];
    $reason = $data['reason'];
    
    $stmt = $conn->prepare("INSERT INTO appointments (student_id, appointment_date, appointment_time, reason) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $student_id, $appointment_date, $appointment_time, $reason);
    
    if ($stmt->execute()) {
        $response = array(
            'status' => 'success',
            'message' => 'Appointment scheduled successfully',
            'appointment_id' => $stmt->insert_id
        );
    } else {
        $response = array(
            'status' => 'error',
            'message' => 'Error scheduling appointment: ' . $conn->error
        );
    }
    $stmt->close();
}

// Update appointment status
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $appointment_id = $data['appointment_id'];
    $status = $data['status'];
    
    $stmt = $conn->prepare("UPDATE appointments SET status=? WHERE appointment_id=?");
    $stmt->bind_param("si", $status, $appointment_id);
    
    if ($stmt->execute()) {
        $response = array(
            'status' => 'success',
            'message' => 'Appointment status updated successfully'
        );
    } else {
        $response = array(
            'status' => 'error',
            'message' => 'Error updating appointment status: ' . $conn->error
        );
    }
    $stmt->close();
}

// Delete appointment
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $appointment_id = $_GET['id'];
    
    $stmt = $conn->prepare("DELETE FROM appointments WHERE appointment_id=?");
    $stmt->bind_param("i", $appointment_id);
    
    if ($stmt->execute()) {
        $response = array(
            'status' => 'success',
            'message' => 'Appointment cancelled successfully'
        );
    } else {
        $response = array(
            'status' => 'error',
            'message' => 'Error cancelling appointment: ' . $conn->error
        );
    }
    $stmt->close();
}

echo json_encode($response);
$conn->close();
?>
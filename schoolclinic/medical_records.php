<?php
require_once 'config.php';

header('Content-Type: application/json');

$response = array();

// Get all medical records for a student
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $student_id = isset($_GET['student_id']) ? $_GET['student_id'] : null;
    
    if ($student_id) {
        $sql = "SELECT mr.*, CONCAT(s.first_name, ' ', s.last_name) AS student_name 
                FROM medical_records mr
                JOIN students s ON mr.student_id = s.student_id
                WHERE mr.student_id = ?
                ORDER BY mr.visit_date DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $student_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $records = array();
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $records[] = $row;
            }
        }
        
        $response = array(
            'status' => 'success',
            'data' => $records
        );
        $stmt->close();
    } else {
        $response = array(
            'status' => 'error',
            'message' => 'Student ID is required'
        );
    }
}

// Add new medical record
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $student_id = $data['student_id'];
    $symptoms = $data['symptoms'];
    $diagnosis = $data['diagnosis'];
    $treatment = $data['treatment'];
    $prescription = $data['prescription'];
    $notes = $data['notes'];
    $follow_up_date = $data['follow_up_date'];
    
    $stmt = $conn->prepare("INSERT INTO medical_records (student_id, symptoms, diagnosis, treatment, prescription, notes, follow_up_date) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $student_id, $symptoms, $diagnosis, $treatment, $prescription, $notes, $follow_up_date);
    
    if ($stmt->execute()) {
        $response = array(
            'status' => 'success',
            'message' => 'Medical record added successfully',
            'record_id' => $stmt->insert_id
        );
    } else {
        $response = array(
            'status' => 'error',
            'message' => 'Error adding medical record: ' . $conn->error
        );
    }
    $stmt->close();
}

// Update medical record
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $record_id = $data['record_id'];
    
    $symptoms = $data['symptoms'];
    $diagnosis = $data['diagnosis'];
    $treatment = $data['treatment'];
    $prescription = $data['prescription'];
    $notes = $data['notes'];
    $follow_up_date = $data['follow_up_date'];
    
    $stmt = $conn->prepare("UPDATE medical_records SET symptoms=?, diagnosis=?, treatment=?, prescription=?, notes=?, follow_up_date=? WHERE record_id=?");
    $stmt->bind_param("ssssssi", $symptoms, $diagnosis, $treatment, $prescription, $notes, $follow_up_date, $record_id);
    
    if ($stmt->execute()) {
        $response = array(
            'status' => 'success',
            'message' => 'Medical record updated successfully'
        );
    } else {
        $response = array(
            'status' => 'error',
            'message' => 'Error updating medical record: ' . $conn->error
        );
    }
    $stmt->close();
}

// Delete medical record
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $record_id = $_GET['id'];
    
    $stmt = $conn->prepare("DELETE FROM medical_records WHERE record_id=?");
    $stmt->bind_param("i", $record_id);
    
    if ($stmt->execute()) {
        $response = array(
            'status' => 'success',
            'message' => 'Medical record deleted successfully'
        );
    } else {
        $response = array(
            'status' => 'error',
            'message' => 'Error deleting medical record: ' . $conn->error
        );
    }
    $stmt->close();
}

echo json_encode($response);
$conn->close();
?>
<?php
require_once 'config.php';

header('Content-Type: application/json');

$response = array();

// Get all students
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $sql = "SELECT * FROM students ORDER BY last_name, first_name";
    $result = $conn->query($sql);
    
    $students = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
    }
    
    $response = array(
        'status' => 'success',
        'data' => $students
    );
}

// Add new student
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $first_name = $data['first_name'];
    $last_name = $data['last_name'];
    $grade_level = $data['grade_level'];
    $section = $data['section'];
    $birth_date = $data['birth_date'];
    $gender = $data['gender'];
    $address = $data['address'];
    $contact_number = $data['contact_number'];
    $blood_type = $data['blood_type'];
    $allergies = $data['allergies'];
    $guardian_name = $data['guardian_name'];
    $guardian_contact = $data['guardian_contact'];
    
    $stmt = $conn->prepare("INSERT INTO students (first_name, last_name, grade_level, section, birth_date, gender, address, contact_number, blood_type, allergies, guardian_name, guardian_contact) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssssss", $first_name, $last_name, $grade_level, $section, $birth_date, $gender, $address, $contact_number, $blood_type, $allergies, $guardian_name, $guardian_contact);
    
    if ($stmt->execute()) {
        $response = array(
            'status' => 'success',
            'message' => 'Student added successfully',
            'student_id' => $stmt->insert_id
        );
    } else {
        $response = array(
            'status' => 'error',
            'message' => 'Error adding student: ' . $conn->error
        );
    }
    $stmt->close();
}

// Update student
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $student_id = $data['student_id'];
    
    $first_name = $data['first_name'];
    $last_name = $data['last_name'];
    $grade_level = $data['grade_level'];
    $section = $data['section'];
    $birth_date = $data['birth_date'];
    $gender = $data['gender'];
    $address = $data['address'];
    $contact_number = $data['contact_number'];
    $blood_type = $data['blood_type'];
    $allergies = $data['allergies'];
    $guardian_name = $data['guardian_name'];
    $guardian_contact = $data['guardian_contact'];
    
    $stmt = $conn->prepare("UPDATE students SET first_name=?, last_name=?, grade_level=?, section=?, birth_date=?, gender=?, address=?, contact_number=?, blood_type=?, allergies=?, guardian_name=?, guardian_contact=? WHERE student_id=?");
    $stmt->bind_param("ssssssssssssi", $first_name, $last_name, $grade_level, $section, $birth_date, $gender, $address, $contact_number, $blood_type, $allergies, $guardian_name, $guardian_contact, $student_id);
    
    if ($stmt->execute()) {
        $response = array(
            'status' => 'success',
            'message' => 'Student updated successfully'
        );
    } else {
        $response = array(
            'status' => 'error',
            'message' => 'Error updating student: ' . $conn->error
        );
    }
    $stmt->close();
}

// Delete student
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $student_id = $_GET['id'];
    
    $stmt = $conn->prepare("DELETE FROM students WHERE student_id=?");
    $stmt->bind_param("i", $student_id);
    
    if ($stmt->execute()) {
        $response = array(
            'status' => 'success',
            'message' => 'Student deleted successfully'
        );
    } else {
        $response = array(
            'status' => 'error',
            'message' => 'Error deleting student: ' . $conn->error
        );
    }
    $stmt->close();
}

echo json_encode($response);
$conn->close();
?>
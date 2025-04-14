<?php
require_once 'config.php';

header('Content-Type: application/json');

$response = array();

// Get report data
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $report_type = $_GET['type'];
    $start_date = isset($_GET['start_date']) ? $_GET['start_date'] : null;
    $end_date = isset($_GET['end_date']) ? $_GET['end_date'] : null;
    
    switch ($report_type) {
        case 'visits':
            $sql = "SELECT DATE(visit_date) AS date, COUNT(*) AS count 
                    FROM medical_records";
                    
            if ($start_date && $end_date) {
                $sql .= " WHERE DATE(visit_date) BETWEEN ? AND ?";
            }
            
            $sql .= " GROUP BY DATE(visit_date) ORDER BY date";
            
            $stmt = $conn->prepare($sql);
            
            if ($start_date && $end_date) {
                $stmt->bind_param("ss", $start_date, $end_date);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            $data = array();
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
            }
            
            $response = array(
                'status' => 'success',
                'data' => $data,
                'report_type' => 'Daily Visits'
            );
            $stmt->close();
            break;
            
        case 'common_conditions':
            $sql = "SELECT diagnosis, COUNT(*) AS count 
                    FROM medical_records 
                    WHERE diagnosis IS NOT NULL AND diagnosis != ''";
                    
            if ($start_date && $end_date) {
                $sql .= " AND DATE(visit_date) BETWEEN ? AND ?";
            }
            
            $sql .= " GROUP BY diagnosis ORDER BY count DESC LIMIT 10";
            
            $stmt = $conn->prepare($sql);
            
            if ($start_date && $end_date) {
                $stmt->bind_param("ss", $start_date, $end_date);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            $data = array();
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
            }
            
            $response = array(
                'status' => 'success',
                'data' => $data,
                'report_type' => 'Common Conditions'
            );
            $stmt->close();
            break;
            
        case 'grade_level_stats':
            $sql = "SELECT s.grade_level, COUNT(*) AS count 
                    FROM medical_records mr
                    JOIN students s ON mr.student_id = s.student_id";
                    
            if ($start_date && $end_date) {
                $sql .= " WHERE DATE(mr.visit_date) BETWEEN ? AND ?";
            }
            
            $sql .= " GROUP BY s.grade_level ORDER BY s.grade_level";
            
            $stmt = $conn->prepare($sql);
            
            if ($start_date && $end_date) {
                $stmt->bind_param("ss", $start_date, $end_date);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();
            
            $data = array();
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
            }
            
            $response = array(
                'status' => 'success',
                'data' => $data,
                'report_type' => 'Visits by Grade Level'
            );
            $stmt->close();
            break;
            
        default:
            $response = array(
                'status' => 'error',
                'message' => 'Invalid report type'
            );
    }
}

echo json_encode($response);
$conn->close();
?>
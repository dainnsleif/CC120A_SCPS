<?php
require_once 'config.php';

header('Content-Type: application/json');

$response = array();

// Get all inventory items
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $low_stock = isset($_GET['low_stock']) ? $_GET['low_stock'] : null;
    
    $sql = "SELECT * FROM inventory";
    
    if ($low_stock) {
        $sql .= " WHERE quantity <= reorder_level OR reorder_level IS NULL";
    }
    
    $sql .= " ORDER BY item_name";
    
    $result = $conn->query($sql);
    
    $items = array();
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $items[] = $row;
        }
    }
    
    $response = array(
        'status' => 'success',
        'data' => $items
    );
}

// Add new inventory item
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $item_name = $data['item_name'];
    $description = $data['description'];
    $quantity = $data['quantity'];
    $unit = $data['unit'];
    $expiry_date = $data['expiry_date'];
    $reorder_level = $data['reorder_level'];
    
    $stmt = $conn->prepare("INSERT INTO inventory (item_name, description, quantity, unit, expiry_date, reorder_level) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisss", $item_name, $description, $quantity, $unit, $expiry_date, $reorder_level);
    
    if ($stmt->execute()) {
        $response = array(
            'status' => 'success',
            'message' => 'Inventory item added successfully',
            'item_id' => $stmt->insert_id
        );
    } else {
        $response = array(
            'status' => 'error',
            'message' => 'Error adding inventory item: ' . $conn->error
        );
    }
    $stmt->close();
}

// Update inventory item
if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents('php://input'), true);
    $item_id = $data['item_id'];
    
    $item_name = $data['item_name'];
    $description = $data['description'];
    $quantity = $data['quantity'];
    $unit = $data['unit'];
    $expiry_date = $data['expiry_date'];
    $reorder_level = $data['reorder_level'];
    
    $stmt = $conn->prepare("UPDATE inventory SET item_name=?, description=?, quantity=?, unit=?, expiry_date=?, reorder_level=? WHERE item_id=?");
    $stmt->bind_param("ssisssi", $item_name, $description, $quantity, $unit, $expiry_date, $reorder_level, $item_id);
    
    if ($stmt->execute()) {
        $response = array(
            'status' => 'success',
            'message' => 'Inventory item updated successfully'
        );
    } else {
        $response = array(
            'status' => 'error',
            'message' => 'Error updating inventory item: ' . $conn->error
        );
    }
    $stmt->close();
}

// Delete inventory item
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $item_id = $_GET['id'];
    
    $stmt = $conn->prepare("DELETE FROM inventory WHERE item_id=?");
    $stmt->bind_param("i", $item_id);
    
    if ($stmt->execute()) {
        $response = array(
            'status' => 'success',
            'message' => 'Inventory item deleted successfully'
        );
    } else {
        $response = array(
            'status' => 'error',
            'message' => 'Error deleting inventory item: ' . $conn->error
        );
    }
    $stmt->close();
}

echo json_encode($response);
$conn->close();
?>
<?php
require_once 'db_connection.php';

header('Content-Type: application/json');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $sql = "SELECT e.*, 
                   r.building_name, 
                   r.room_no, 
                   rt.name as room_type,
                   rt.id as room_type_id,
                   e.room_allotted
            FROM employees e 
            LEFT JOIN rooms r ON e.id = r.employee_id 
            LEFT JOIN room_types rt ON r.type_id = rt.id
            WHERE e.id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $result->num_rows > 0) {
        $employee = $result->fetch_assoc();
        echo json_encode($employee);
    } else {
        echo json_encode(["error" => "Employee not found"]);
    }
    $stmt->close();
} else {
    echo json_encode(["error" => "Invalid ID"]);
}

$conn->close();
?>
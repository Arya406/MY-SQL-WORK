<?php
require_once 'db_connection.php';

$employee_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($employee_id > 0) {
    $sql = "SELECT e.*, r.building_name, r.room_no, rt.name as room_type 
            FROM employees e 
            LEFT JOIN rooms r ON e.id = r.employee_id 
            LEFT JOIN room_types rt ON r.type_id = rt.id
            WHERE e.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $employee_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $employee = $result->fetch_assoc();
    $stmt->close();
} else {
    header("Location: view_employees.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Employee Details</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background-color: #2c3e50;
            color: white;
            padding: 20px 0;
            text-align: center;
            margin-bottom: 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        h1 {
            margin-bottom: 10px;
        }
        
        .employee-card {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .employee-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .employee-photo {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 4px;
            margin-right: 30px;
        }
        
        .no-photo {
            width: 150px;
            height: 150px;
            background-color: #eee;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 30px;
            color: #777;
        }
        
        .employee-info h2 {
            margin-bottom: 10px;
            color: #2c3e50;
        }
        
        .employee-info p {
            margin-bottom: 5px;
            color: #555;
        }
        
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 20px;
        }
        
        .detail-group {
            margin-bottom: 15px;
        }
        
        .detail-label {
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 5px;
            display: block;
        }
        
        .detail-value {
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 4px;
            border-left: 3px solid #3498db;
        }
        
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #3498db;
            text-decoration: none;
            font-weight: 500;
        }
        
        .back-link:hover {
            text-decoration: underline;
        }
        
        .action-btns {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }
        
        .action-btn {
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
        }
        
        .edit-btn {
            background-color: #f39c12;
            color: white;
        }
        
        .print-btn {
            background-color: #3498db;
            color: white;
        }
        
        .room-details {
            grid-column: span 2;
            background-color: #e8f4fc;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        
        .room-details h3 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Employee Details</h1>
    </header>
    
    <div class="container">
        <?php if ($employee): ?>
        <div class="employee-card">
            <div class="employee-header">
                <?php if (!empty($employee['photo'])): ?>
                    <img src="<?= htmlspecialchars($employee['photo']) ?>" class="employee-photo" alt="Employee Photo">
                <?php else: ?>
                    <div class="no-photo">No Photo Available</div>
                <?php endif; ?>
                
                <div class="employee-info">
                    <h2><?= htmlspecialchars($employee['name']) ?></h2>
                    <p><strong>Designation:</strong> <?= htmlspecialchars($employee['designation']) ?></p>
                    <p><strong>Employee ID:</strong> <?= htmlspecialchars($employee['id']) ?></p>
                    <p><strong>IC Number:</strong> <?= htmlspecialchars($employee['ic_no']) ?></p>
                </div>
            </div>
            
            <?php if (!empty($employee['room_no'])): ?>
                <div class="room-details">
                    <h3>Room Allocation</h3>
                    <div class="details-grid">
                        <div class="detail-group">
                            <span class="detail-label">Building</span>
                            <div class="detail-value"><?= htmlspecialchars($employee['building_name']) ?></div>
                        </div>
                        <div class="detail-group">
                            <span class="detail-label">Room Number</span>
                            <div class="detail-value"><?= htmlspecialchars($employee['room_no']) ?></div>
                        </div>
                        <div class="detail-group">
                            <span class="detail-label">Room Type</span>
                            <div class="detail-value"><?= htmlspecialchars($employee['room_type'] ?? 'N/A') ?></div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <div class="details-grid">
                <div class="detail-group">
                    <span class="detail-label">Date of Birth</span>
                    <div class="detail-value"><?= htmlspecialchars($employee['dob']) ?></div>
                </div>
                
                <div class="detail-group">
                    <span class="detail-label">Mobile Number</span>
                    <div class="detail-value"><?= htmlspecialchars($employee['mobile_no']) ?></div>
                </div>
                
                <div class="detail-group">
                    <span class="detail-label">Aadhar Number</span>
                    <div class="detail-value"><?= htmlspecialchars($employee['aadhar_no']) ?></div>
                </div>
                
                <div class="detail-group">
                    <span class="detail-label">PAN Number</span>
                    <div class="detail-value"><?= htmlspecialchars($employee['pan_no']) ?></div>
                </div>
                
                <div class="detail-group">
                    <span class="detail-label">Bank Account Number</span>
                    <div class="detail-value"><?= htmlspecialchars($employee['bank_acc_no']) ?></div>
                </div>
                
                <div class="detail-group">
                    <span class="detail-label">Room Allotted</span>
                    <div class="detail-value"><?= $employee['room_allotted'] == 'Yes' ? 'Yes' : 'No' ?></div>
                </div>
            </div>
            
            <div class="detail-group" style="grid-column: span 2;">
                <span class="detail-label">Temporary Address</span>
                <div class="detail-value"><?= nl2br(htmlspecialchars($employee['temp_address'])) ?></div>
            </div>
            
            <div class="detail-group" style="grid-column: span 2;">
                <span class="detail-label">Permanent Address</span>
                <div class="detail-value"><?= nl2br(htmlspecialchars($employee['perm_address'])) ?></div>
            </div>
            
            <div class="action-btns">
                <a href="edit_employee.php?id=<?= $employee['id'] ?>" class="action-btn edit-btn">Edit Details</a>
                <a href="javascript:window.print()" class="action-btn print-btn">Print Details</a>
            </div>
        </div>
        <?php else: ?>
            <div class="error">Employee not found</div>
        <?php endif; ?>
        
        <a href="view_employees.php" class="back-link">← Back to Employee List</a>
    </div>
</body>
</html>
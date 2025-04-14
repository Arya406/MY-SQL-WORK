<?php
require_once 'db_connection.php';

$message = '';
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate required fields
    $required = ['building_name', 'room_no', 'employee_id', 'room_type'];
    $missing = array();
    
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            $missing[] = $field;
        }
    }
    
    if (!empty($missing)) {
        $message = "<div class='error'>Please fill all required fields: " . implode(', ', $missing) . "</div>";
    } else {
        // Sanitize inputs
        $building_name = $conn->real_escape_string(trim($_POST["building_name"]));
        $room_no = $conn->real_escape_string(trim($_POST["room_no"]));
        $employee_id = intval($_POST["employee_id"]);
        $type_id = intval($_POST["room_type"]);
        
        // Get room type details
        $type_query = $conn->query("SELECT is_shared, capacity FROM room_types WHERE id = $type_id");
        if (!$type_query) {
            $message = "<div class='error'>Error: " . $conn->error . "</div>";
        } else {
            $type_data = $type_query->fetch_assoc();
            
            // Check capacity if room is shared and has capacity limit
            if ($type_data['is_shared'] && $type_data['capacity']) {
                $occupancy_query = $conn->query("SELECT COUNT(*) as count FROM rooms 
                                                WHERE building_name = '$building_name' 
                                                AND room_no = '$room_no'");
                $occupancy = $occupancy_query->fetch_assoc();
                
                if ($occupancy['count'] >= $type_data['capacity']) {
                    $message = "<div class='error'>This room has reached maximum capacity ({$type_data['capacity']} people)</div>";
                }
            }
            
            if (empty($message)) {
                // Begin transaction
                $conn->begin_transaction();
                
                try {
                    // Check if employee already has a room assignment
                    $existing_room = $conn->query("SELECT id FROM rooms WHERE employee_id = $employee_id FOR UPDATE");
                    
                    if ($existing_room->num_rows > 0) {
                        // Update existing room assignment
                        $sql = "UPDATE rooms SET building_name = ?, room_no = ?, type_id = ? WHERE employee_id = ?";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ssii", $building_name, $room_no, $type_id, $employee_id);
                    } else {
                        // Create new room assignment
                        $sql = "INSERT INTO rooms (building_name, room_no, employee_id, type_id) VALUES (?, ?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("ssii", $building_name, $room_no, $employee_id, $type_id);
                        
                        // Update employee's room_allotted status
                        $conn->query("UPDATE employees SET room_allotted = 'Yes' WHERE id = $employee_id");
                    }
                    
                    if ($stmt->execute()) {
                        $conn->commit();
                        $message = "<div class='success'>Room allotted successfully in $building_name (Room $room_no)</div>";
                        $success = true;
                    } else {
                        throw new Exception($stmt->error);
                    }
                } catch (Exception $e) {
                    $conn->rollback();
                    $message = "<div class='error'>Error: " . $e->getMessage() . "</div>";
                }
                
                if (isset($stmt)) {
                    $stmt->close();
                }
            }
        }
    }
}

// Fetch employees with their current room assignments
$employees = array();
$sql = "SELECT e.id, e.name, e.designation, 
               IFNULL(CONCAT(r.building_name, ' - Room ', r.room_no), 'Not allotted') as room_info,
               IFNULL(rt.name, 'N/A') as room_type
        FROM employees e
        LEFT JOIN rooms r ON e.id = r.employee_id
        LEFT JOIN room_types rt ON r.type_id = rt.id
        ORDER BY e.name ASC";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $employees[] = $row;
}

// Fetch available buildings
$buildings = ['Main Building', 'Annex Building', 'Research Wing', 'Administration Block', 'Guest House', 'Crystal Tower'];

// Fetch room types
$room_types = array();
$result = $conn->query("SELECT * FROM room_types ORDER BY name ASC");
while ($row = $result->fetch_assoc()) {
    $room_types[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Room Allotment System</title>
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
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background-color: #ffffff;
            color: rgb(0, 0, 0);
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .logo-container {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .logo {
            height: 60px;
        }
        
        .company-name {
            font-size: 24px;
            font-weight: bold;
            color: #000000;
        }
        
        .datetime {
            font-size: 16px;
            text-align: right;
            color:black;
        }
        
        h1 {
            margin-bottom: 10px;
        }
        
        .form-container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        input[type="text"],
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border 0.3s;
        }
        
        input[type="text"]:focus,
        select:focus {
            border-color: #3498db;
            outline: none;
        }
        
        .employee-details {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #3498db;
        }
        
        .employee-photo {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        
        .btn {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 12px 20px;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
            width: 100%;
        }
        
        .btn:hover {
            background-color: #2980b9;
        }
        
        .success {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
        }
        
        .error {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            text-align: center;
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
        
        .building-room {
            display: flex;
            gap: 15px;
        }
        
        .building-room .form-group {
            flex: 1;
        }
        
        .room-type-info {
            font-size: 0.9em;
            color: #666;
            margin-top: 5px;
            font-style: italic;
        }
        
        .capacity-warning {
            color: #e74c3c;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <header>
    <div class="header-content">
            <div class="logo-container">
                <img src="Offial_DRDO_LOGO.png" alt="Company Logo" class="logo">
            </div>
            
            <div class="company-name">DRDO SPPL</div>
            
            <div class="datetime" id="datetime">
                <!-- JavaScript will populate this -->
            </div>
            
            <div class="logo-container">
                <img src="SSPL_LOGO.png" alt="DRDO Logo" class="logo">
            </div>
        </div>
    </header>
    
    <div class="container">
        <?php if (!empty($message)) echo $message; ?>
        
        <div class="form-container">
            <form method="post" id="roomAllotmentForm">
                <div class="form-group">
                    <label for="employee_id">Select Employee:</label>
                    <select name="employee_id" id="employee_id" required onchange="fetchEmployeeDetails(this.value)">
                        <option value="">Select Employee</option>
                        <?php foreach ($employees as $emp): ?>
                            <option value="<?= $emp['id'] ?>" 
                                    data-building="<?= isset($emp['building_name']) ? $emp['building_name'] : '' ?>"
                                    data-room="<?= isset($emp['room_no']) ? $emp['room_no'] : '' ?>"
                                    data-type="<?= isset($emp['room_type']) ? $emp['room_type'] : '' ?>">
                                <?= htmlspecialchars($emp['name']) ?> 
                                (<?= $emp['room_info'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div id="employeeDetails" class="employee-details" style="display: none;">
                    <img id="employeePhoto" class="employee-photo" src="" alt="Employee Photo" style="display: none;">
                    <p><strong>Name:</strong> <span id="employeeName"></span></p>
                    <p><strong>Designation:</strong> <span id="employeeDesignation"></span></p>
                    <p><strong>Mobile No:</strong> <span id="employeeMobile"></span></p>
                    <p><strong>Current Room:</strong> <span id="employeeRoom"></span></p>
                </div>
                
                <div class="building-room">
                    <div class="form-group">
                        <label for="building_name">Building Name:</label>
                        <select name="building_name" id="building_name" required>
                            <option value="">Select Building</option>
                            <?php foreach ($buildings as $building): ?>
                                <option value="<?= htmlspecialchars($building) ?>"><?= htmlspecialchars($building) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="room_no">Room Number:</label>
                        <input type="text" name="room_no" id="room_no" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="room_type">Room Type:</label>
                    <select name="room_type" id="room_type" required>
                        <option value="">Select Room Type</option>
                        <?php foreach ($room_types as $type): ?>
                            <option value="<?= $type['id'] ?>"
                                    data-shared="<?= $type['is_shared'] ?>"
                                    data-capacity="<?= $type['capacity'] ?>"
                                    data-description="<?= htmlspecialchars($type['description']) ?>">
                                <?= htmlspecialchars($type['name']) ?>
                                <?php if ($type['is_shared']): ?>
                                    (Shared<?= $type['capacity'] ? ', max '.$type['capacity'] : '' ?>)
                                <?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div id="typeDescription" class="room-type-info"></div>
                    <div id="capacityWarning" class="room-type-info capacity-warning" style="display: none;"></div>
                </div>
                
                <button type="submit" class="btn">Allot Room</button>
            </form>
            
            <a href="index.html" class="back-link">← Back to Home</a>
        </div>
    </div>

    <script>
    function fetchEmployeeDetails(employeeId) {
        const detailsDiv = document.getElementById('employeeDetails');
        const photoImg = document.getElementById('employeePhoto');
        
        if (!employeeId) {
            detailsDiv.style.display = 'none';
            return;
        }
        
        // Get data from the selected option
        const selectedOption = document.querySelector(`#employee_id option[value="${employeeId}"]`);
        const currentBuilding = selectedOption.getAttribute('data-building');
        const currentRoom = selectedOption.getAttribute('data-room');
        const currentType = selectedOption.getAttribute('data-type');
        
        // Set current values in form
        document.getElementById('building_name').value = currentBuilding || '';
        document.getElementById('room_no').value = currentRoom || '';
        
        // Try to match room type if exists
        if (currentType) {
            const typeOptions = document.getElementById('room_type').options;
            for (let i = 0; i < typeOptions.length; i++) {
                if (typeOptions[i].text.includes(currentType)) {
                    document.getElementById('room_type').value = typeOptions[i].value;
                    updateTypeInfo();
                    break;
                }
            }
        }
        
        // Fetch additional details via AJAX
        fetch(`fetch_employee.php?id=${employeeId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('employeeName').textContent = data.name;
                document.getElementById('employeeDesignation').textContent = data.designation;
                document.getElementById('employeeMobile').textContent = data.mobile_no;
                
                // Display current room assignment if exists
                let roomInfo = 'Not allotted';
                if (data.building_name && data.room_no) {
                    roomInfo = `${data.building_name} - Room ${data.room_no}`;
                    if (data.room_type) {
                        roomInfo += ` (${data.room_type})`;
                    }
                }
                document.getElementById('employeeRoom').textContent = roomInfo;
                
                if (data.photo) {
                    photoImg.src = data.photo;
                    photoImg.style.display = 'block';
                } else {
                    photoImg.style.display = 'none';
                }
                
                detailsDiv.style.display = 'block';
            })
            .catch(error => {
                console.error('Error fetching employee details:', error);
            });
    }
    
    function updateTypeInfo() {
        const typeSelect = document.getElementById('room_type');
        const selectedOption = typeSelect.options[typeSelect.selectedIndex];
        const descElement = document.getElementById('typeDescription');
        const warningElement = document.getElementById('capacityWarning');
        
        if (selectedOption.value) {
            const isShared = selectedOption.dataset.shared === '1';
            const capacity = selectedOption.dataset.capacity;
            const description = selectedOption.dataset.description;
            
            let infoText = '';
            if (description) {
                infoText = description;
            }
            
            if (isShared) {
                infoText += (infoText ? ' ' : '') + `This is a shared space`;
                if (capacity) {
                    infoText += ` with maximum capacity of ${capacity} people.`;
                    
                    // Show warning if approaching capacity
                    warningElement.textContent = `Warning: This room type has capacity restrictions`;
                    warningElement.style.display = 'block';
                } else {
                    warningElement.style.display = 'none';
                }
            } else {
                infoText += (infoText ? ' ' : '') + `This is a private space.`;
                warningElement.style.display = 'none';
            }
            
            descElement.textContent = infoText;
        } else {
            descElement.textContent = '';
            warningElement.style.display = 'none';
        }
    }
    
    // Initialize event listeners
    document.getElementById('room_type').addEventListener('change', updateTypeInfo);
    
    // Form validation
    document.getElementById('roomAllotmentForm').addEventListener('submit', function(e) {
        const building = document.getElementById('building_name').value.trim();
        const roomNo = document.getElementById('room_no').value.trim();
        const roomType = document.getElementById('room_type').value;
        
        if (!building || !roomNo || !roomType) {
            alert('Please fill all required fields');
            e.preventDefault();
        }
    });
    
    // Initialize type info if page reloaded with values
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('room_type').value) {
            updateTypeInfo();
        }
    });
    function updateDateTime() {
            const now = new Date();
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
            };
            document.getElementById('datetime').textContent = now.toLocaleDateString('en-US', options);
        }
        
        // Update immediately
        updateDateTime();
        
        // Update every second
        setInterval(updateDateTime, 1000);
</script>
</body>
</html>
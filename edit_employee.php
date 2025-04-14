<?php
require_once 'db_connection.php';

$message = '';
$employee = null;

// Check if employee ID is provided in URL
if (isset($_GET['id'])) {
    $employee_id = intval($_GET['id']);
    
    // Fetch employee data
    $sql = "SELECT e.*, r.building_name, r.room_no 
            FROM employees e 
            LEFT JOIN rooms r ON e.id = r.employee_id 
            WHERE e.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $employee_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $employee = $result->fetch_assoc();
    } else {
        $message = "<div class='error'>Employee not found</div>";
    }
    $stmt->close();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["employee_id"])) {
    $employee_id = intval($_POST["employee_id"]);
    
    // Sanitize inputs
    $ic_no = $conn->real_escape_string(trim($_POST["ic_no"]));
    $name = $conn->real_escape_string(trim($_POST["name"]));
    $designation = $conn->real_escape_string(trim($_POST["designation"]));
    $dob = $conn->real_escape_string($_POST["dob"]);
    $mobile_no = $conn->real_escape_string(trim($_POST["mobile_no"]));
    $temp_address = $conn->real_escape_string(trim($_POST["temp_address"]));
    $perm_address = $conn->real_escape_string(trim($_POST["perm_address"]));
    $aadhar_no = $conn->real_escape_string(trim($_POST["aadhar_no"]));
    $pan_no = $conn->real_escape_string(trim($_POST["pan_no"]));
    $bank_acc_no = $conn->real_escape_string(trim($_POST["bank_acc_no"]));
    $room_no = isset($_POST["room_no"]) ? $conn->real_escape_string(trim($_POST["room_no"])) : null;
    $building_name = isset($_POST["building_name"]) ? $conn->real_escape_string(trim($_POST["building_name"])) : null;
    
    // Handle photo upload
    $photo_update = "";
    if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        $file_ext = strtolower(pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION));
        
        if (in_array($file_ext, $allowed_types)) {
            $photo_name = uniqid() . '.' . $file_ext;
            $photo_path = $target_dir . $photo_name;
            
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $photo_path)) {
                $photo_update = ", photo = '" . $conn->real_escape_string($photo_path) . "'";
                
                // Delete old photo if exists
                if (!empty($employee['photo']) && file_exists($employee['photo'])) {
                    unlink($employee['photo']);
                }
            }
        }
    }
    
    // Update employee details
    $sql = "UPDATE employees SET 
            ic_no = ?, name = ?, designation = ?, dob = ?, mobile_no = ?,
            temp_address = ?, perm_address = ?, aadhar_no = ?, pan_no = ?, bank_acc_no = ?
            $photo_update
            WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssssi", $ic_no, $name, $designation, $dob, $mobile_no,
                     $temp_address, $perm_address, $aadhar_no, $pan_no, $bank_acc_no, $employee_id);
    
    if ($stmt->execute()) {
        // Handle room assignment if room number and building name provided
        if ($room_no && $building_name) {
            // Check if employee already has a room
            $check_room = $conn->query("SELECT id FROM rooms WHERE employee_id = $employee_id");
            
            if ($check_room->num_rows > 0) {
                // Update existing room
                $conn->query("UPDATE rooms SET building_name = '$building_name', room_no = '$room_no' WHERE employee_id = $employee_id");
            } else {
                // Create new room assignment
                $conn->query("INSERT INTO rooms (building_name, room_no, employee_id) VALUES ('$building_name', '$room_no', $employee_id)");
                $conn->query("UPDATE employees SET room_allotted = 'Yes' WHERE id = $employee_id");
            }
        } elseif ($room_no === '' && $building_name === '') {
            // Remove room assignment if both fields are empty
            $conn->query("DELETE FROM rooms WHERE employee_id = $employee_id");
            $conn->query("UPDATE employees SET room_allotted = 'No' WHERE id = $employee_id");
        }
        
        $message = "<div class='success'>Employee details updated successfully</div>";
        
        // Refresh employee data with building name
        $employee = $conn->query("SELECT e.*, r.building_name, r.room_no FROM employees e LEFT JOIN rooms r ON e.id = r.employee_id WHERE e.id = $employee_id")->fetch_assoc();
    } else {
        $message = "<div class='error'>Error updating employee: " . $stmt->error . "</div>";
    }
    $stmt->close();
}

$conn->close();
?>

<!-- REST OF THE HTML REMAINS THE SAME -->

<!DOCTYPE html>
<html>
<head>
    <title>Edit Employee</title>
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
        }
        
        h1 {
            margin-bottom: 10px;
        }
        
        .form-container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
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
        input[type="date"],
        input[type="file"],
        select,
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border 0.3s;
        }
        
        input[type="text"]:focus,
        input[type="date"]:focus,
        select:focus,
        textarea:focus {
            border-color: #3498db;
            outline: none;
        }
        
        textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .employee-photo {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 15px;
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
        
        .checkbox-label {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        
        #same_address {
            width: auto;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <header>
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
        
        <?php if ($employee): ?>
        <div class="form-container">
            <form method="post" enctype="multipart/form-data" id="editEmployeeForm">
                <input type="hidden" name="employee_id" value="<?= $employee['id'] ?>">
                
                <div class="form-group">
                    <label>Employee Photo:</label>
                    <?php if (!empty($employee['photo'])): ?>
                        <img src="<?= htmlspecialchars($employee['photo']) ?>" class="employee-photo" alt="Employee Photo">
                    <?php else: ?>
                        <div style="width:150px; height:150px; background:#eee; display:flex; align-items:center; justify-content:center; margin-bottom:15px;">
                            No Photo
                        </div>
                    <?php endif; ?>
                    <label for="photo">Upload New Photo:</label>
                    <input type="file" name="photo" id="photo" accept="image/*">
                </div>
                
                <div class="form-group">
                    <label for="name">Full Name:</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($employee['name']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="dob">Date of Birth:</label>
                    <input type="date" id="dob" name="dob" value="<?= htmlspecialchars($employee['dob']) ?>" required max="<?= date('Y-m-d'); ?>">
                </div>
                
                <div class="form-group">
                    <label for="mobile_no">Mobile Number:</label>
                    <input type="text" id="mobile_no" name="mobile_no" value="<?= htmlspecialchars($employee['mobile_no']) ?>" required 
                           pattern="[0-9]{10}" title="10 digit mobile number">
                </div>
                
                <div class="form-group">
                    <label for="ic_no">IC Number:</label>
                    <input type="text" id="ic_no" name="ic_no" value="<?= htmlspecialchars($employee['ic_no']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="designation">Designation:</label>
                    <select id="designation" name="designation" required>
                        <option value="">Select Designation</option>
                        <option value="Scientist B" <?= $employee['designation'] == 'Scientist B' ? 'selected' : '' ?>>Scientist B</option>
                        <option value="Scientist C" <?= $employee['designation'] == 'Scientist C' ? 'selected' : '' ?>>Scientist C</option>
                        <option value="Scientist D" <?= $employee['designation'] == 'Scientist D' ? 'selected' : '' ?>>Scientist D</option>
                        <option value="Scientist E" <?= $employee['designation'] == 'Scientist E' ? 'selected' : '' ?>>Scientist E</option>
                        <option value="Scientist F" <?= $employee['designation'] == 'Scientist F' ? 'selected' : '' ?>>Scientist F</option>
                        <option value="Scientist G" <?= $employee['designation'] == 'Scientist G' ? 'selected' : '' ?>>Scientist G</option>
                        <option value="Technical Officer A" <?= $employee['designation'] == 'Technical Officer A' ? 'selected' : '' ?>>Technical Officer A</option>
                        <option value="Technical Officer B" <?= $employee['designation'] == 'Technical Officer B' ? 'selected' : '' ?>>Technical Officer B</option>
                        <option value="Technical Officer C" <?= $employee['designation'] == 'Technical Officer C' ? 'selected' : '' ?>>Technical Officer C</option>
                        <option value="Senior Technical Assistant" <?= $employee['designation'] == 'Senior Technical Assistant' ? 'selected' : '' ?>>Senior Technical Assistant</option>
                        <option value="Chief Account Officer" <?= $employee['designation'] == 'Chief Account Officer' ? 'selected' : '' ?>>Chief Account Officer</option>
                        <option value="Accounts Officer" <?= $employee['designation'] == 'Accounts Officer' ? 'selected' : '' ?>>Accounts Officer</option>
                        <option value="Administrative Officer" <?= $employee['designation'] == 'Administrative Officer' ? 'selected' : '' ?>>Administrative Officer</option>
                        <option value="Admin Assistant" <?= $employee['designation'] == 'Admin Assistant' ? 'selected' : '' ?>>Admin Assistant</option>
                        <option value="JRF" <?= $employee['designation'] == 'JRF' ? 'selected' : '' ?>>JRF</option>
                        <option value="SRF" <?= $employee['designation'] == 'SRF' ? 'selected' : '' ?>>SRF</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="temp_address">Temporary Address:</label>
                    <textarea id="temp_address" name="temp_address" required><?= htmlspecialchars($employee['temp_address']) ?></textarea>
                </div>
                
                <div class="checkbox-label">
                    <input type="checkbox" id="same_address" onclick="copyAddress()">
                    <label for="same_address" style="margin-bottom: 0;">Same as Temporary Address</label>
                </div>
                
                <div class="form-group">
                    <label for="perm_address">Permanent Address:</label>
                    <textarea id="perm_address" name="perm_address" required><?= htmlspecialchars($employee['perm_address']) ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="aadhar_no">Aadhar Card Number:</label>
                    <input type="text" id="aadhar_no" name="aadhar_no" value="<?= htmlspecialchars($employee['aadhar_no']) ?>" required 
                           pattern="[0-9]{12}" title="12 digit Aadhar number">
                </div>
                
                <div class="form-group">
                    <label for="pan_no">PAN Card Number:</label>
                    <input type="text" id="pan_no" name="pan_no" value="<?= htmlspecialchars($employee['pan_no']) ?>" required 
                           pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}" title="Enter valid PAN (e.g. ABCDE1234F)">
                </div>
                
                <div class="form-group">
                    <label for="bank_acc_no">Bank Account Number:</label>
                    <input type="text" id="bank_acc_no" name="bank_acc_no" value="<?= htmlspecialchars($employee['bank_acc_no']) ?>" required>
                </div>
                <div class="form-group">
    <label for="building_name">Building Name:</label>
    <select name="building_name" id="building_name">
        <option value="">Select Building</option>
        <?php foreach (['Main Building', 'Annex Building', 'Research Wing', 'Administration Block', 'Guest House','Crystal Tower'] as $building): ?>
            <option value="<?= htmlspecialchars($building) ?>" <?= $employee['building_name'] == $building ? 'selected' : '' ?>>
                <?= htmlspecialchars($building) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>
                
                <div class="form-group">
                    <label for="room_no">Room Number:</label>
                    <input type="text" id="room_no" name="room_no" value="<?= !empty($employee['room_no']) ? htmlspecialchars($employee['room_no']) : '' ?>">
                </div>
                
                <button type="submit" class="btn">Save Changes</button>
            </form>
            
            <a href="view_employees.php" class="back-link">← Back to Employee List</a>
        </div>
        <?php else: ?>
            <div class="error">No employee selected or employee not found</div>
            <a href="view_employees.php" class="back-link">← Back to Employee List</a>
        <?php endif; ?>
    </div>

    <script>
        // Copy temporary address to permanent address if checkbox is checked
        function copyAddress() {
            const checkbox = document.getElementById('same_address');
            const tempAddress = document.getElementById('temp_address');
            const permAddress = document.getElementById('perm_address');
            
            if (checkbox.checked) {
                permAddress.value = tempAddress.value;
                permAddress.readOnly = true;
            } else {
                permAddress.readOnly = false;
            }
        }
        
        // Preview new photo before upload
        document.getElementById('photo').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.createElement('img');
                    preview.src = e.target.result;
                    preview.className = 'employee-photo';
                    preview.alt = 'New Photo Preview';
                    
                    const photoContainer = document.querySelector('.form-group:first-child');
                    const oldPhoto = photoContainer.querySelector('img, div');
                    if (oldPhoto) {
                        photoContainer.replaceChild(preview, oldPhoto);
                    } else {
                        photoContainer.appendChild(preview);
                    }
                }
                reader.readAsDataURL(file);
            }
        });
        
        // Form validation
        document.getElementById('editEmployeeForm').addEventListener('submit', function(e) {
            const dob = new Date(document.getElementById('dob').value);
            const today = new Date();
            
            if (dob >= today) {
                alert('Date of birth must be in the past');
                e.preventDefault();
            }
            
            // Add more validation as needed
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
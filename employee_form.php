<?php
require_once 'db_connection.php';

$message = '';
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize inputs
    $required_fields = ['ic_no', 'name', 'designation', 'dob', 'mobile_no', 'temp_address', 'perm_address', 'aadhar_no', 'pan_no', 'bank_acc_no'];
    
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $message = "<div class='error'>Please fill all required fields</div>";
            break;
        }
    }

    if (empty($message)) {
        // Prepare data
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
        $photo = '';

        // Handle file upload
        if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] == UPLOAD_ERR_OK) {
            $target_dir = "uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0755, true);
            }
            
            // Validate image
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            $file_ext = strtolower(pathinfo($_FILES["photo"]["name"], PATHINFO_EXTENSION));
            
            if (in_array($file_ext, $allowed_types)) {
                $photo_name = uniqid() . '.' . $file_ext;
                $photo_path = $target_dir . $photo_name;
                
                if (move_uploaded_file($_FILES["photo"]["tmp_name"], $photo_path)) {
                    $photo = $photo_path;
                }
            }
        }

        // Insert into database using prepared statement
        $stmt = $conn->prepare("INSERT INTO employees (ic_no, name, designation, dob, mobile_no, photo, temp_address, perm_address, aadhar_no, pan_no, bank_acc_no) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssssss", $ic_no, $name, $designation, $dob, $mobile_no, $photo, $temp_address, $perm_address, $aadhar_no, $pan_no, $bank_acc_no);
        
        if ($stmt->execute()) {
            $message = "<div class='success'>Employee added successfully!</div>";
            $success = true;
        } else {
            $message = "<div class='error'>Error: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Employee</title>
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
        
        .photo-preview {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 4px;
            margin-bottom: 15px;
            display: none;
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
        
        #same_address {
            width: auto;
            margin-right: 10px;
        }
        
        .checkbox-label {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
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
            <form method="post" enctype="multipart/form-data" id="employeeForm">
                <div class="form-group">
                    <label for="name">Full Name:</label>
                    <input type="text" id="name" name="name" required style="text-transform: capitalize;">
                </div>
                
                <div class="form-group">
                    <label for="dob">Date of Birth:</label>
                    <input type="date" id="dob" name="dob" required max="<?= date('Y-m-d'); ?>">
                </div>
                
                <div class="form-group">
                    <label for="mobile_no">Mobile Number:</label>
                    <input type="text" id="mobile_no" name="mobile_no" required 
                           pattern="[0-9]{10}" title="10 digit mobile number">
                </div>
                
                <div class="form-group">
                    <label for="ic_no">IC Number:</label>
                    <input type="text" id="ic_no" name="ic_no" required>
                </div>
                
                <div class="form-group">
                    <label for="designation">Designation:</label>
                    <select id="designation" name="designation" required>
                        <option value="">Select Designation</option>
                        <option value="Scientist B">Scientist B</option>
                        <option value="Scientist C">Scientist C</option>
                        <option value="Scientist D">Scientist D</option>
                        <option value="Scientist E">Scientist E</option>
                        <option value="Scientist F">Scientist F</option>
                        <option value="Scientist G">Scientist G</option>
                        <option value="Technical Officer A">Technical Officer A</option>
                        <option value="Technical Officer B">Technical Officer B</option>
                        <option value="Technical Officer C">Technical Officer C</option>
                        <option value="Senior Technical Assistant">Senior Technical Assistant</option>
                        <option value="Chief Account Officer">Chief Account Officer</option>
                        <option value="Accounts Officer">Accounts Officer</option>
                        <option value="Administrative Officer">Administrative Officer</option>
                        <option value="Admin Assistant">Admin Assistant</option>
                        <option value="JRF">JRF</option>
                        <option value="SRF">SRF</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="temp_address">Temporary Address:</label>
                    <textarea id="temp_address" name="temp_address" required></textarea>
                </div>
                
                <div class="checkbox-label">
                    <input type="checkbox" id="same_address" onclick="copyAddress()">
                    <label for="same_address" style="margin-bottom: 0;">Same as Temporary Address</label>
                </div>
                
                <div class="form-group">
                    <label for="perm_address">Permanent Address:</label>
                    <textarea id="perm_address" name="perm_address" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="aadhar_no">Aadhar Card Number:</label>
                    <input type="text" id="aadhar_no" name="aadhar_no" required 
                           pattern="[0-9]{12}" title="12 digit Aadhar number">
                </div>
                
                <div class="form-group">
                    <label for="pan_no">PAN Card Number:</label>
                    <input type="text" id="pan_no" name="pan_no" required 
                           pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}" title="Enter valid PAN (e.g. ABCDE1234F)">
                </div>
                
                <div class="form-group">
                    <label for="bank_acc_no">Bank Account Number:</label>
                    <input type="text" id="bank_acc_no" name="bank_acc_no" required>
                </div>
                
                <div class="form-group">
                    <label for="photo">Upload Photo:</label>
                    <input type="file" id="photo" name="photo" accept="image/*" onchange="previewPhoto(event)">
                    <img id="photoPreview" class="photo-preview" alt="Photo Preview">
                </div>
                
                <input type="hidden" name="room_allotted" value="No">
                
                <button type="submit" class="btn">Save Employee</button>
            </form>
            
            <a href="index.html" class="back-link">← Back to Home</a>
        </div>
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
        
        // Preview photo before upload
        function previewPhoto(event) {
            const preview = document.getElementById('photoPreview');
            const file = event.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                
                reader.readAsDataURL(file);
            }
        }
        
        // Form validation
        document.getElementById('employeeForm').addEventListener('submit', function(e) {
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
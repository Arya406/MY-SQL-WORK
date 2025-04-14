<?php
require_once 'db_connection.php';

// Initialize search variables
$search_term = '';
$search_results = [];

// Check if search form was submitted
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_term = $conn->real_escape_string(trim($_GET['search']));
    
    // Search query for employees matching IC No or Name
    $sql_search = "SELECT e.*, r.building_name, r.room_no 
                   FROM employees e
                   LEFT JOIN rooms r ON e.id = r.employee_id
                   WHERE e.ic_no LIKE '%$search_term%' OR e.name LIKE '%$search_term%'
                   ORDER BY e.name ASC";
    $search_results = $conn->query($sql_search);
}

// Fetch allotted employees (only if not searching)
if (empty($search_term)) {
    $sql_allotted = "SELECT e.id, e.ic_no, e.name, e.designation, e.dob, e.mobile_no, e.photo, 
                    r.building_name, r.room_no, e.temp_address, e.perm_address, e.aadhar_no, e.pan_no, e.bank_acc_no
                    FROM employees e
                    INNER JOIN rooms r ON e.id = r.employee_id
                    ORDER BY e.name ASC";
    $result_allotted = $conn->query($sql_allotted);

    // Fetch not allotted employees
    $sql_not_allotted = "SELECT e.id, e.ic_no, e.name, e.designation, e.dob, e.mobile_no, e.photo,
                        e.temp_address, e.perm_address, e.aadhar_no, e.pan_no, e.bank_acc_no
                        FROM employees e
                        LEFT JOIN rooms r ON e.id = r.employee_id
                        WHERE r.employee_id IS NULL
                        ORDER BY e.name ASC";
    $result_not_allotted = $conn->query($sql_not_allotted);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee List</title>
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
max-width: 1200px;
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

.tab-container {
margin-bottom: 30px;
}

.tab-buttons {
display: flex;
border-bottom: 1px solid #ddd;
margin-bottom: 20px;
}

.tab-btn {
padding: 12px 20px;
background: none;
border: none;
cursor: pointer;
font-size: 16px;
font-weight: 600;
color: #555;
border-bottom: 3px solid transparent;
transition: all 0.3s;
}

.tab-btn.active {
color: #3498db;
border-bottom: 3px solid #3498db;
}

.tab-content {
display: none;
}

.tab-content.active {
display: block;
}

table {
width: 100%;
border-collapse: collapse;
margin-bottom: 30px;
background-color: white;
box-shadow: 0 0 20px rgba(0,0,0,0.1);
}

th, td {
padding: 12px 15px;
text-align: left;
border-bottom: 1px solid #ddd;
}

th {
background-color: #3498db;
color: white;
font-weight: 600;
}

tr:hover {
background-color: #f5f5f5;
}

.employee-photo {
width: 50px;
height: 50px;
object-fit: cover;
border-radius: 50%;
}

.no-photo {
width: 50px;
height: 50px;
background-color: #eee;
border-radius: 50%;
display: flex;
align-items: center;
justify-content: center;
color: #777;
font-size: 12px;
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
display: flex;
gap: 5px;
}

.action-btn {
padding: 5px 10px;
border: none;
border-radius: 3px;
cursor: pointer;
font-size: 12px;
}

.edit-btn {
background-color: #f39c12;
color: white;
}

.delete-btn {
background-color: #e74c3c;
color: white;
}

.view-btn {
background-color: #3498db;
color: white;
}
        
        .search-container {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }
        
        .search-input {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        .search-btn {
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        
        .search-btn:hover {
            background-color: #2980b9;
        }
        
        .clear-search {
            padding: 10px 15px;
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
        }
        
        .clear-search:hover {
            background-color: #c0392b;
        }
        
        .search-results {
            margin-bottom: 30px;
        }
        
        .search-info {
            margin-bottom: 15px;
            font-size: 18px;
            color: #2c3e50;
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
        <!-- Search Form -->
        <form method="GET" action="view_employees.php" class="search-container">
            <input type="text" name="search" class="search-input" placeholder="Search by IC No or Name" value="<?= htmlspecialchars($search_term) ?>">
            <button type="submit" class="search-btn">Search</button>
            <?php if (!empty($search_term)): ?>
                <a href="view_employees.php" class="clear-search">Clear Search</a>
            <?php endif; ?>
        </form>

        <?php if (!empty($search_term)): ?>
            <!-- Search Results -->
            <div class="search-results">
                <h2 class="search-info">Search Results for "<?= htmlspecialchars($search_term) ?>"</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Designation</th>
                            <th>IC No</th>
                            <th>Mobile No</th>
                            <th>Building</th>
                            <th>Room No</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($search_results && $search_results->num_rows > 0): ?>
                            <?php while ($row = $search_results->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id']) ?></td>
                                <td>
                                    <?php if (!empty($row['photo'])): ?>
                                        <img src="<?= htmlspecialchars($row['photo']) ?>" alt="Photo" class="employee-photo">
                                    <?php else: ?>
                                        <div class="no-photo">No Photo</div>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['designation']) ?></td>
                                <td><?= htmlspecialchars($row['ic_no']) ?></td>
                                <td><?= htmlspecialchars($row['mobile_no']) ?></td>
                                <td><?= !empty($row['building_name']) ? htmlspecialchars($row['building_name']) : 'N/A' ?></td>
                                <td><?= !empty($row['room_no']) ? htmlspecialchars($row['room_no']) : 'N/A' ?></td>
                                <td class="action-btns">
                                    <button class="action-btn view-btn">View</button>
                                    <button class="action-btn edit-btn">Edit</button>
                                    <button class="action-btn delete-btn">Delete</button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" style="text-align: center;">No employees found matching your search</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <!-- Regular Tabbed View -->
            <div class="tab-container">
                <div class="tab-buttons">
                    <button class="tab-btn active" onclick="openTab('allotted')">Employees with Rooms</button>
                    <button class="tab-btn" onclick="openTab('notAllotted')">Employees without Rooms</button>
                </div>
                
                <div id="allotted" class="tab-content active">
                    <h2>Employees With Room Allotment</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Photo</th>
                                <th>Name</th>
                                <th>Designation</th>
                                <th>IC No</th>
                                <th>Mobile No</th>
                                <th>Building</th>
                                <th>Room No</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result_allotted->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id']) ?></td>
                                <td>
                                    <?php if (!empty($row['photo'])): ?>
                                        <img src="<?= htmlspecialchars($row['photo']) ?>" alt="Photo" class="employee-photo">
                                    <?php else: ?>
                                        <div class="no-photo">No Photo</div>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['designation']) ?></td>
                                <td><?= htmlspecialchars($row['ic_no']) ?></td>
                                <td><?= htmlspecialchars($row['mobile_no']) ?></td>
                                <td><?= htmlspecialchars($row['building_name']) ?></td>
                                <td><?= htmlspecialchars($row['room_no']) ?></td>
                                <td class="action-btns">
                                    <button class="action-btn view-btn">View</button>
                                    <button class="action-btn edit-btn">Edit</button>
                                    <button class="action-btn delete-btn">Delete</button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                
                <div id="notAllotted" class="tab-content">
                    <h2>Employees Without Room Allotment</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Photo</th>
                                <th>Name</th>
                                <th>Designation</th>
                                <th>IC No</th>
                                <th>Mobile No</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result_not_allotted->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id']) ?></td>
                                <td>
                                    <?php if (!empty($row['photo'])): ?>
                                        <img src="<?= htmlspecialchars($row['photo']) ?>" alt="Photo" class="employee-photo">
                                    <?php else: ?>
                                        <div class="no-photo">No Photo</div>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['designation']) ?></td>
                                <td><?= htmlspecialchars($row['ic_no']) ?></td>
                                <td><?= htmlspecialchars($row['mobile_no']) ?></td>
                                <td class="action-btns">
                                    <button class="action-btn view-btn">View</button>
                                    <button class="action-btn edit-btn">Edit</button>
                                    <button class="action-btn delete-btn">Delete</button>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
        
        <a href="index.html" class="back-link">← Back to Home</a>
    </div>

    <script>
        function openTab(tabId) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Remove active class from all buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Show the selected tab
            document.getElementById(tabId).classList.add('active');
            event.currentTarget.classList.add('active');
        }
        
        // Add event listeners for action buttons
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const row = this.closest('tr');
                const id = row.cells[0].textContent;
                
                if (this.classList.contains('view-btn')) {
                    window.location.href = `employee_details.php?id=${id}`;
                } else if (this.classList.contains('edit-btn')) {
                    window.location.href = `edit_employee.php?id=${id}`;
                } else if (this.classList.contains('delete-btn')) {
                    if (confirm('Are you sure you want to delete this employee?')) {
                        window.location.href = `delete_employee.php?id=${id}`;
                    }
                }
            });
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
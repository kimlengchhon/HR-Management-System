<?php
require_once 'config/database.php';
// checkLogin();

// Get all departments with employee count
$departments = $conn->query("
    SELECT d.*, COUNT(e.emp_id) as employee_count
    FROM tb_department d
    LEFT JOIN tb_employee e ON d.dept_id = e.dept_id AND e.status='Active'
    GROUP BY d.dept_id
    ORDER BY d.dept_name
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Departments - HR System</title>
    <link rel="stylesheet" href="css/common.css">
    <style>
        .dept-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .dept-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            border-left: 4px solid #667eea;
            transition: transform 0.3s;
        }
        .dept-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.12);
        }
        .dept-card h3 {
            color: #2d3748;
            margin-bottom: 15px;
            font-size: 20px;
        }
        .dept-info {
            color: #718096;
            font-size: 14px;
            margin-bottom: 8px;
        }
        .dept-actions {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <h2>HR Management System</h2>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="employees.php">Employees</a>
            <a href="departments.php">Departments</a>
            <a href="recruitment.php">Recruitment</a>
            <a href="attendance.php">Attendance</a>
            <a href="leave.php">Leave</a>
            <a href="payroll.php">Payroll</a>
            <a href="performance.php">Performance</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h1>Department Management</h1>
            <a href="department_add.php" class="btn btn-primary">➕ Add Department</a>
        </div>
        
        <div class="dept-grid">
            <?php foreach ($departments as $dept): ?>
            <div class="dept-card">
                <h3><?php echo $dept['dept_name']; ?></h3>
                <div class="dept-info">
                    👥 <strong><?php echo $dept['employee_count']; ?></strong> Employees
                </div>
                <div class="dept-info">
                    📅 Created: <?php echo date('M d, Y', strtotime($dept['created_at'])); ?>
                </div>
                <div class="dept-actions">
                    <a href="department_view.php?id=<?php echo $dept['dept_id']; ?>" 
                       class="btn btn-primary" 
                       style="padding: 8px 15px; font-size: 13px; flex: 1; text-align: center;">
                        View Details
                    </a>
                    <a href="department_add.php?id=<?php echo $dept['dept_id']; ?>" 
                       class="btn" 
                       style="padding: 8px 15px; font-size: 13px; background: #e2e8f0;">
                        Edit
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>
</html>
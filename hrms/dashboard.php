<?php
require_once 'config/database.php';
checkLogin();

// Get statistics
$total_employees = $conn->query("SELECT COUNT(*) FROM tb_employee WHERE status='Active'")->fetchColumn();
$total_present = $conn->query("SELECT COUNT(DISTINCT emp_id) FROM tb_attendance WHERE att_date = CURDATE() AND status='Present'")->fetchColumn();
$pending_leaves = $conn->query("SELECT COUNT(*) FROM tb_leave WHERE status='Pending'")->fetchColumn();
$total_departments = $conn->query("SELECT COUNT(*) FROM tb_department")->fetchColumn();

// Recent employees
$recent_employees = $conn->query("SELECT * FROM tb_employee ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - HR System</title>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/dashboard.css">
</head>
<body>
    <nav class="navbar">
        <h2>HR Management System</h2>
        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="employees.php">Employees</a>
            <a href="attendance.php">Attendance</a>
            <a href="leave.php">Leave</a>
            <a href="payroll.php">Payroll</a>
            <a href="performance.php">Performance</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="container">
        <h1 style="margin-bottom: 30px;">Dashboard</h1>
        
        <div class="stats-grid">
            <div class="stat-card purple">
                <h3>Total Employees</h3>
                <div class="number"><?php echo $total_employees; ?></div>
            </div>
            
            <div class="stat-card green">
                <h3>Present Today</h3>
                <div class="number"><?php echo $total_present; ?></div>
            </div>
            
            <div class="stat-card orange">
                <h3>Pending Leaves</h3>
                <div class="number"><?php echo $pending_leaves; ?></div>
            </div>
            
            <div class="stat-card blue">
                <h3>Departments</h3>
                <div class="number"><?php echo $total_departments; ?></div>
            </div>
        </div>

        <div class="card">
            <h3 style="margin-bottom: 20px;">Quick Actions</h3>
            <div class="quick-actions">
                <a href="employee_add.php" class="action-btn">➕ Add Employee</a>
                <a href="attendance_checkin.php" class="action-btn">✓ Check In/Out</a>
                <a href="leave_request.php" class="action-btn">📝 Request Leave</a>
                <a href="payroll_generate.php" class="action-btn">💰 Generate Payroll</a>
            </div>
        </div>

        <div class="card">
            <h3 style="margin-bottom: 20px;">Recent Employees</h3>
            <table>
                <thead>
                    <tr>
                        <th>Employee Code</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recent_employees as $emp): ?>
                    <tr>
                        <td><?php echo $emp['emp_code']; ?></td>
                        <td><?php echo $emp['full_name']; ?></td>
                        <td><?php echo $emp['email']; ?></td>
                        <td><span class="badge badge-success"><?php echo $emp['status']; ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
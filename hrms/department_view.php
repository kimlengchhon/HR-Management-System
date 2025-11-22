<?php
require_once 'config/database.php';
// checkLogin();

if (!isset($_GET['id'])) {
    header('Location: departments.php');
    exit();
}

$dept_id = $_GET['id'];

// Get department info
$stmt = $conn->prepare("SELECT * FROM tb_department WHERE dept_id = ?");
$stmt->execute([$dept_id]);
$department = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$department) {
    header('Location: departments.php');
    exit();
}

// Get employees in this department
$stmt = $conn->prepare("
    SELECT e.*, p.pos_name 
    FROM tb_employee e
    LEFT JOIN tb_position p ON e.pos_id = p.pos_id
    WHERE e.dept_id = ? AND e.status='Active'
    ORDER BY e.full_name
");
$stmt->execute([$dept_id]);
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get statistics
$total_employees = count($employees);
$stmt = $conn->prepare("SELECT COUNT(DISTINCT emp_id) FROM tb_attendance WHERE emp_id IN (SELECT emp_id FROM tb_employee WHERE dept_id = ?) AND att_date = CURDATE()");
$stmt->execute([$dept_id]);
$present_today = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $department['dept_name']; ?> - HR System</title>
    <link rel="stylesheet" href="css/common.css">
    <link rel="stylesheet" href="css/dashboard.css">
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
            <h1><?php echo $department['dept_name']; ?> Department</h1>
            <div style="display: flex; gap: 10px;">
                <a href="department_add.php?id=<?php echo $dept_id; ?>" class="btn btn-primary">Edit Department</a>
                <a href="departments.php" class="btn" style="background: #e2e8f0;">Back</a>
            </div>
        </div>

        <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: 30px;">
            <div class="stat-card purple">
                <h3>Total Employees</h3>
                <div class="number"><?php echo $total_employees; ?></div>
            </div>
            
            <div class="stat-card green">
                <h3>Present Today</h3>
                <div class="number"><?php echo $present_today; ?></div>
            </div>
            
            <div class="stat-card blue">
                <h3>Created</h3>
                <div class="number" style="font-size: 18px;">
                    <?php echo date('M d, Y', strtotime($department['created_at'])); ?>
                </div>
            </div>
        </div>
        
        <div class="card">
            <h3 style="margin-bottom: 20px;">Department Employees</h3>
            
            <?php if (empty($employees)): ?>
                <p style="text-align: center; color: #718096; padding: 40px;">No employees in this department</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Employee Code</th>
                            <th>Full Name</th>
                            <th>Position</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Hire Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($employees as $emp): ?>
                        <tr>
                            <td><?php echo $emp['emp_code']; ?></td>
                            <td><?php echo $emp['full_name']; ?></td>
                            <td><?php echo $emp['pos_name']; ?></td>
                            <td><?php echo $emp['email']; ?></td>
                            <td><?php echo $emp['phone']; ?></td>
                            <td><?php echo date('M d, Y', strtotime($emp['hire_date'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
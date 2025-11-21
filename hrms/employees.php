<?php
require_once 'config/database.php';
// checkLogin();

$employees = $conn->query("
    SELECT e.*, d.dept_name, p.pos_name 
    FROM tb_employee e
    LEFT JOIN tb_department d ON e.dept_id = d.dept_id
    LEFT JOIN tb_position p ON e.pos_id = p.pos_id
    ORDER BY e.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employees - HR System</title>
    <link rel="stylesheet" href="css/common.css">
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
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h1>Employee Management</h1>
            <a href="employee_add.php" class="btn btn-primary">➕ Add Employee</a>
        </div>
        
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Department</th>
                        <th>Position</th>
                        <th>Hire Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($employees as $emp): ?>
                    <tr>
                        <td><?php echo $emp['emp_code']; ?></td>
                        <td><?php echo $emp['full_name']; ?></td>
                        <td><?php echo $emp['email']; ?></td>
                        <td><?php echo $emp['dept_name']; ?></td>
                        <td><?php echo $emp['pos_name']; ?></td>
                        <td><?php echo $emp['hire_date']; ?></td>
                        <td>
                            <span class="badge <?php echo $emp['status'] == 'Active' ? 'badge-success' : 'badge-danger'; ?>">
                                <?php echo $emp['status']; ?>
                            </span>
                        </td>
                        <td>
                            <a href="employee_add.php?id=<?php echo $emp['emp_id']; ?>" class="btn btn-primary" style="padding: 5px 10px; font-size: 12px;">Edit</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
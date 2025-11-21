<?php
require_once 'config/database.php';
// checkLogin();

// Handle payment status update
if (isset($_GET['pay']) && isset($_GET['id'])) {
    $stmt = $conn->prepare("UPDATE tb_payroll SET payment_status = 'Paid' WHERE payroll_id = ?");
    $stmt->execute([$_GET['id']]);
    header('Location: payroll.php');
    exit();
}

// Get all payroll records
$payrolls = $conn->query("
    SELECT p.*, e.emp_code, e.full_name 
    FROM tb_payroll p
    JOIN tb_employee e ON p.emp_id = e.emp_id
    ORDER BY p.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Management - HR System</title>
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
            <h1>Payroll Management</h1>
            <a href="payroll_generate.php" class="btn btn-success">💰 Generate Payroll</a>
        </div>
        
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Month</th>
                        <th>Base Salary</th>
                        <th>Deductions</th>
                        <th>Allowances</th>
                        <th>Net Salary</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($payrolls)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; color: #718096; padding: 40px;">
                            No payroll records found
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($payrolls as $payroll): ?>
                        <tr>
                            <td><?php echo $payroll['emp_code'] . ' - ' . $payroll['full_name']; ?></td>
                            <td><?php echo $payroll['month']; ?></td>
                            <td>$<?php echo number_format($payroll['base_salary'], 2); ?></td>
                            <td>$<?php echo number_format($payroll['deductions'], 2); ?></td>
                            <td>$<?php echo number_format($payroll['allowances'], 2); ?></td>
                            <td><strong>$<?php echo number_format($payroll['net_salary'], 2); ?></strong></td>
                            <td>
                                <span class="badge <?php echo $payroll['payment_status'] == 'Paid' ? 'badge-success' : 'badge-warning'; ?>">
                                    <?php echo $payroll['payment_status']; ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($payroll['payment_status'] == 'Unpaid'): ?>
                                    <a href="?pay=1&id=<?php echo $payroll['payroll_id']; ?>" 
                                       class="btn btn-success" 
                                       style="padding: 5px 10px; font-size: 12px;"
                                       onclick="return confirm('Mark as paid?')">
                                        Mark Paid
                                    </a>
                                <?php else: ?>
                                    <span style="color: #48bb78; font-size: 12px;">✓ Paid</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
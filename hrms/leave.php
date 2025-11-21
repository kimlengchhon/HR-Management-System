<?php
require_once 'config/database.php';
// checkLogin();

// Handle approve/reject
if (isset($_GET['action']) && isset($_GET['id'])) {
    $leave_id = $_GET['id'];
    $new_status = ($_GET['action'] == 'approve') ? 'Approved' : 'Rejected';
    
    $stmt = $conn->prepare("UPDATE tb_leave SET status = ? WHERE leave_id = ?");
    $stmt->execute([$new_status, $leave_id]);
    
    header('Location: leave.php');
    exit();
}

// Get all leave requests
$leaves = $conn->query("
    SELECT l.*, e.emp_code, e.full_name 
    FROM tb_leave l
    JOIN tb_employee e ON l.emp_id = e.emp_id
    ORDER BY l.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leave Management - HR System</title>
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
            <h1>Leave Management</h1>
            <a href="leave_request.php" class="btn btn-primary">📝 Request Leave</a>
        </div>
        
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Employee</th>
                        <th>Leave Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($leaves)): ?>
                    <tr>
                        <td colspan="7" style="text-align: center; color: #718096; padding: 40px;">
                            No leave requests found
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($leaves as $leave): ?>
                        <tr>
                            <td><?php echo $leave['emp_code'] . ' - ' . $leave['full_name']; ?></td>
                            <td><?php echo $leave['leave_type']; ?></td>
                            <td><?php echo $leave['start_date']; ?></td>
                            <td><?php echo $leave['end_date']; ?></td>
                            <td><?php echo $leave['reason']; ?></td>
                            <td>
                                <?php
                                $badge_class = 'badge-warning';
                                if ($leave['status'] == 'Approved') $badge_class = 'badge-success';
                                if ($leave['status'] == 'Rejected') $badge_class = 'badge-danger';
                                ?>
                                <span class="badge <?php echo $badge_class; ?>"><?php echo $leave['status']; ?></span>
                            </td>
                            <td>
                                <?php if ($leave['status'] == 'Pending'): ?>
                                    <a href="?action=approve&id=<?php echo $leave['leave_id']; ?>" 
                                       class="btn btn-success" 
                                       style="padding: 5px 10px; font-size: 12px; margin-right: 5px;"
                                       onclick="return confirm('Approve this leave request?')">
                                        Approve
                                    </a>
                                    <a href="?action=reject&id=<?php echo $leave['leave_id']; ?>" 
                                       class="btn btn-danger" 
                                       style="padding: 5px 10px; font-size: 12px;"
                                       onclick="return confirm('Reject this leave request?')">
                                        Reject
                                    </a>
                                <?php else: ?>
                                    <span style="color: #718096; font-size: 12px;">-</span>
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
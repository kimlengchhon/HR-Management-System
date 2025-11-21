<?php
require_once 'config/database.php';
// checkLogin();

// Get today's attendance
$today = date('Y-m-d');
$attendance = $conn->query("
    SELECT a.*, e.emp_code, e.full_name 
    FROM tb_attendance a
    JOIN tb_employee e ON a.emp_id = e.emp_id
    WHERE a.att_date = '$today'
    ORDER BY a.check_in DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance - HR System</title>
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
            <h1>Attendance Records</h1>
            <a href="attendance_checkin.php" class="btn btn-success">✓ Check In/Out</a>
        </div>
        
        <div class="card">
            <h3 style="margin-bottom: 20px;">Today's Attendance (<?php echo date('F d, Y'); ?>)</h3>
            
            <?php if (empty($attendance)): ?>
                <p style="text-align: center; color: #718096; padding: 40px;">No attendance records for today</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Employee Code</th>
                            <th>Full Name</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attendance as $att): ?>
                        <tr>
                            <td><?php echo $att['emp_code']; ?></td>
                            <td><?php echo $att['full_name']; ?></td>
                            <td><?php echo $att['check_in'] ?? '-'; ?></td>
                            <td><?php echo $att['check_out'] ?? '-'; ?></td>
                            <td>
                                <?php
                                $badge_class = 'badge-success';
                                if ($att['status'] == 'Late') $badge_class = 'badge-warning';
                                if ($att['status'] == 'Absent') $badge_class = 'badge-danger';
                                ?>
                                <span class="badge <?php echo $badge_class; ?>"><?php echo $att['status']; ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
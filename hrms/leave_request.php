<?php
require_once 'config/database.php';
// checkLogin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $emp_id = $_POST['emp_id'];
    $leave_type = $_POST['leave_type'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $reason = $_POST['reason'];
    
    $stmt = $conn->prepare("INSERT INTO tb_leave (emp_id, leave_type, start_date, end_date, reason, status) VALUES (?, ?, ?, ?, ?, 'Pending')");
    $stmt->execute([$emp_id, $leave_type, $start_date, $end_date, $reason]);
    
    header('Location: leave.php');
    exit();
}

// Get all active employees
$employees = $conn->query("SELECT emp_id, emp_code, full_name FROM tb_employee WHERE status='Active' ORDER BY full_name")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Leave - HR System</title>
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
        <h1 style="margin-bottom: 30px;">Request Leave</h1>
        
        <div class="card">
            <form method="POST">
                <div class="form-group">
                    <label>Select Employee *</label>
                    <select name="emp_id" required>
                        <option value="">Choose employee...</option>
                        <?php foreach ($employees as $emp): ?>
                        <option value="<?php echo $emp['emp_id']; ?>">
                            <?php echo $emp['emp_code'] . ' - ' . $emp['full_name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Leave Type *</label>
                    <select name="leave_type" required>
                        <option value="">Choose type...</option>
                        <option value="Sick">Sick Leave</option>
                        <option value="Vacation">Vacation</option>
                        <option value="Personal">Personal Leave</option>
                        <option value="Emergency">Emergency</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Start Date *</label>
                    <input type="date" name="start_date" required>
                </div>
                
                <div class="form-group">
                    <label>End Date *</label>
                    <input type="date" name="end_date" required>
                </div>
                
                <div class="form-group">
                    <label>Reason *</label>
                    <textarea name="reason" rows="4" required placeholder="Please provide reason for leave..."></textarea>
                </div>
                
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                    <a href="leave.php" class="btn" style="background: #e2e8f0;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
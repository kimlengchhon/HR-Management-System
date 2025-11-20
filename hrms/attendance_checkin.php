<?php
require_once 'config/database.php';
checkLogin();

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $emp_id = $_POST['emp_id'];
    $action = $_POST['action'];
    $today = date('Y-m-d');
    $current_time = date('H:i:s');
    
    // Check if already checked in today
    $stmt = $conn->prepare("SELECT * FROM tb_attendance WHERE emp_id = ? AND att_date = ?");
    $stmt->execute([$emp_id, $today]);
    $existing = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($action == 'checkin') {
        if ($existing) {
            $message = 'Already checked in today!';
            $message_type = 'error';
        } else {
            // Determine status (Late if after 9:00 AM)
            $status = (strtotime($current_time) > strtotime('09:00:00')) ? 'Late' : 'Present';
            
            $stmt = $conn->prepare("INSERT INTO tb_attendance (emp_id, att_date, check_in, status) VALUES (?, ?, ?, ?)");
            $stmt->execute([$emp_id, $today, $current_time, $status]);
            
            $message = 'Check-in successful at ' . $current_time;
            $message_type = 'success';
        }
    } elseif ($action == 'checkout') {
        if (!$existing) {
            $message = 'No check-in record found for today!';
            $message_type = 'error';
        } elseif ($existing['check_out']) {
            $message = 'Already checked out today!';
            $message_type = 'error';
        } else {
            $stmt = $conn->prepare("UPDATE tb_attendance SET check_out = ? WHERE att_id = ?");
            $stmt->execute([$current_time, $existing['att_id']]);
            
            $message = 'Check-out successful at ' . $current_time;
            $message_type = 'success';
        }
    }
}

// Get all active employees
$employees = $conn->query("SELECT emp_id, emp_code, full_name FROM tb_employee WHERE status='Active' ORDER BY full_name")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check In/Out - HR System</title>
    <link rel="stylesheet" href="css/common.css">
    <style>
        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
            font-weight: 500;
        }
        .message.success {
            background: #c6f6d5;
            color: #22543d;
        }
        .message.error {
            background: #fed7d7;
            color: #742a2a;
        }
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 20px;
        }
        .action-buttons button {
            flex: 1;
            padding: 15px;
            font-size: 16px;
        }
    </style>
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
        <h1 style="margin-bottom: 30px;">Check In / Check Out</h1>
        
        <div class="card">
            <?php if ($message): ?>
                <div class="message <?php echo $message_type; ?>"><?php echo $message; ?></div>
            <?php endif; ?>
            
            <form method="POST" id="attendanceForm">
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
                
                <input type="hidden" name="action" id="action" value="">
                
                <div class="action-buttons">
                    <button type="submit" class="btn btn-success" onclick="setAction('checkin')">
                        ✓ Check In
                    </button>
                    <button type="submit" class="btn btn-danger" onclick="setAction('checkout')">
                        ✗ Check Out
                    </button>
                </div>
            </form>
            
            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e2e8f0;">
                <p style="text-align: center; color: #718096;">
                    Current Time: <strong><?php echo date('H:i:s'); ?></strong> | 
                    Date: <strong><?php echo date('F d, Y'); ?></strong>
                </p>
            </div>
        </div>
    </div>

    <script>
        function setAction(action) {
            document.getElementById('action').value = action;
        }
    </script>
</body>
</html>
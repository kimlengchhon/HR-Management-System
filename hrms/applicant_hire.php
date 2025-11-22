<?php
require_once 'config/database.php';
// checkLogin();

if (!isset($_GET['id'])) {
    header('Location: recruitment.php');
    exit();
}

$applicant_id = $_GET['id'];

// Get applicant details
$stmt = $conn->prepare("
    SELECT a.*, j.dept_id, j.pos_id, j.job_title
    FROM tb_applicant a
    JOIN tb_job_posting j ON a.job_id = j.job_id
    WHERE a.applicant_id = ?
");
$stmt->execute([$applicant_id]);
$applicant = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$applicant || $applicant['status'] != 'Passed') {
    header('Location: recruitment.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $emp_code = $_POST['emp_code'];
    $hire_date = $_POST['hire_date'];
    
    // Check if employee code already exists
    $stmt = $conn->prepare("SELECT * FROM tb_employee WHERE emp_code = ?");
    $stmt->execute([$emp_code]);
    
    if ($stmt->rowCount() > 0) {
        $error = "Employee code already exists!";
    } else {
        // Insert into employee table
        $stmt = $conn->prepare("INSERT INTO tb_employee (emp_code, full_name, email, phone, dept_id, pos_id, hire_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Active')");
        $stmt->execute([
            $emp_code,
            $applicant['full_name'],
            $applicant['email'],
            $applicant['phone'],
            $applicant['dept_id'],
            $applicant['pos_id'],
            $hire_date
        ]);
        
        // Update applicant status
        $conn->prepare("UPDATE tb_applicant SET status='Hired' WHERE applicant_id=?")->execute([$applicant_id]);
        
        header('Location: employees.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hire Employee - <?php echo $applicant['full_name']; ?></title>
    <link rel="stylesheet" href="css/common.css">
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
        <h1 style="margin-bottom: 30px;">🎉 Hire Employee</h1>
        
        <div class="card">
            <?php if (isset($error)): ?>
                <div style="background: #fed7d7; color: #742a2a; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <div style="background: #c6f6d5; padding: 20px; border-radius: 8px; margin-bottom: 30px;">
                <h3 style="color: #22543d; margin-bottom: 10px;">Converting Applicant to Employee</h3>
                <p style="color: #22543d;"><strong>Name:</strong> <?php echo $applicant['full_name']; ?></p>
                <p style="color: #22543d;"><strong>Email:</strong> <?php echo $applicant['email']; ?></p>
                <p style="color: #22543d;"><strong>Applied For:</strong> <?php echo $applicant['job_title']; ?></p>
            </div>
            
            <form method="POST">
                <div class="form-group">
                    <label>Employee Code * (Must be unique)</label>
                    <input type="text" name="emp_code" required placeholder="e.g. EMP001" value="EMP<?php echo str_pad($applicant_id, 4, '0', STR_PAD_LEFT); ?>">
                    <small style="color: #718096;">This code will be used to identify the employee</small>
                </div>
                
                <div class="form-group">
                    <label>Hire Date *</label>
                    <input type="date" name="hire_date" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                
                <div style="background: #f7fafc; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                    <h4 style="margin-bottom: 15px; color: #4a5568;">Employee Details (Auto-filled)</h4>
                    <p><strong>Full Name:</strong> <?php echo $applicant['full_name']; ?></p>
                    <p><strong>Email:</strong> <?php echo $applicant['email']; ?></p>
                    <p><strong>Phone:</strong> <?php echo $applicant['phone']; ?></p>
                </div>
                
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-success">✓ Confirm & Hire</button>
                    <a href="applicant_view.php?id=<?php echo $applicant_id; ?>" class="btn" style="background: #e2e8f0;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
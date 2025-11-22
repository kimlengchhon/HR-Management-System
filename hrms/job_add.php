<?php
require_once 'config/database.php';
// checkLogin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $job_title = $_POST['job_title'];
    $dept_id = $_POST['dept_id'];
    $pos_id = $_POST['pos_id'];
    $description = $_POST['description'];
    $requirements = $_POST['requirements'];
    $salary_range = $_POST['salary_range'];
    $posted_date = $_POST['posted_date'];
    $deadline = $_POST['deadline'];
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("INSERT INTO tb_job_posting (job_title, dept_id, pos_id, description, requirements, salary_range, posted_date, deadline, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$job_title, $dept_id, $pos_id, $description, $requirements, $salary_range, $posted_date, $deadline, $status]);
    
    header('Location: recruitment.php');
    exit();
}

$departments = $conn->query("SELECT * FROM tb_department ORDER BY dept_name")->fetchAll(PDO::FETCH_ASSOC);
$positions = $conn->query("SELECT * FROM tb_position ORDER BY pos_name")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post New Job - HR System</title>
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
        <h1 style="margin-bottom: 30px;">Post New Job Opening</h1>
        
        <div class="card">
            <form method="POST">
                <div class="form-group">
                    <label>Job Title *</label>
                    <input type="text" name="job_title" required placeholder="e.g. Senior Software Developer">
                </div>
                
                <div class="form-group">
                    <label>Department *</label>
                    <select name="dept_id" required>
                        <option value="">Select Department</option>
                        <?php foreach ($departments as $dept): ?>
                        <option value="<?php echo $dept['dept_id']; ?>"><?php echo $dept['dept_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Position *</label>
                    <select name="pos_id" required>
                        <option value="">Select Position</option>
                        <?php foreach ($positions as $pos): ?>
                        <option value="<?php echo $pos['pos_id']; ?>"><?php echo $pos['pos_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Job Description *</label>
                    <textarea name="description" rows="4" required placeholder="Describe the role and responsibilities..."></textarea>
                </div>
                
                <div class="form-group">
                    <label>Requirements *</label>
                    <textarea name="requirements" rows="4" required placeholder="List qualifications and skills needed..."></textarea>
                </div>
                
                <div class="form-group">
                    <label>Salary Range</label>
                    <input type="text" name="salary_range" placeholder="e.g. $3000 - $5000">
                </div>
                
                <div class="form-group">
                    <label>Posted Date *</label>
                    <input type="date" name="posted_date" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Application Deadline *</label>
                    <input type="date" name="deadline" required>
                </div>
                
                <div class="form-group">
                    <label>Status *</label>
                    <select name="status" required>
                        <option value="Open">Open</option>
                        <option value="Closed">Closed</option>
                    </select>
                </div>
                
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary">Post Job</button>
                    <a href="recruitment.php" class="btn" style="background: #e2e8f0;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
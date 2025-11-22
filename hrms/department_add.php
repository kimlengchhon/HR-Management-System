<?php
require_once 'config/database.php';
// checkLogin();

$edit_mode = false;
$department = null;

if (isset($_GET['id'])) {
    $edit_mode = true;
    $stmt = $conn->prepare("SELECT * FROM tb_department WHERE dept_id = ?");
    $stmt->execute([$_GET['id']]);
    $department = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $dept_name = $_POST['dept_name'];
    
    if ($edit_mode) {
        $stmt = $conn->prepare("UPDATE tb_department SET dept_name=? WHERE dept_id=?");
        $stmt->execute([$dept_name, $_GET['id']]);
    } else {
        $stmt = $conn->prepare("INSERT INTO tb_department (dept_name) VALUES (?)");
        $stmt->execute([$dept_name]);
    }
    
    header('Location: departments.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $edit_mode ? 'Edit' : 'Add'; ?> Department - HR System</title>
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
        <h1 style="margin-bottom: 30px;"><?php echo $edit_mode ? 'Edit' : 'Add New'; ?> Department</h1>
        
        <div class="card">
            <form method="POST">
                <div class="form-group">
                    <label>Department Name *</label>
                    <input type="text" name="dept_name" value="<?php echo $department['dept_name'] ?? ''; ?>" required placeholder="e.g. IT, HR, Finance">
                </div>
                
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary">
                        <?php echo $edit_mode ? 'Update' : 'Save'; ?> Department
                    </button>
                    <a href="departments.php" class="btn" style="background: #e2e8f0;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
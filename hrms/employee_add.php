<?php
require_once 'config/database.php';
// checkLogin();

$edit_mode = false;
$employee = null;

if (isset($_GET['id'])) {
    $edit_mode = true;
    $stmt = $conn->prepare("SELECT * FROM tb_employee WHERE emp_id = ?");
    $stmt->execute([$_GET['id']]);
    $employee = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $emp_code = $_POST['emp_code'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $dept_id = $_POST['dept_id'];
    $pos_id = $_POST['pos_id'];
    $hire_date = $_POST['hire_date'];
    $status = $_POST['status'];
    
    if ($edit_mode) {
        $stmt = $conn->prepare("UPDATE tb_employee SET emp_code=?, full_name=?, email=?, phone=?, dept_id=?, pos_id=?, hire_date=?, status=? WHERE emp_id=?");
        $stmt->execute([$emp_code, $full_name, $email, $phone, $dept_id, $pos_id, $hire_date, $status, $_GET['id']]);
    } else {
        $stmt = $conn->prepare("INSERT INTO tb_employee (emp_code, full_name, email, phone, dept_id, pos_id, hire_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$emp_code, $full_name, $email, $phone, $dept_id, $pos_id, $hire_date, $status]);
    }
    
    header('Location: employees.php');
    exit();
}

$departments = $conn->query("SELECT * FROM tb_department")->fetchAll(PDO::FETCH_ASSOC);
$positions = $conn->query("SELECT * FROM tb_position")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $edit_mode ? 'Edit' : 'Add'; ?> Employee - HR System</title>
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
        <h1 style="margin-bottom: 30px;"><?php echo $edit_mode ? 'Edit' : 'Add New'; ?> Employee</h1>
        
        <div class="card">
            <form method="POST">
                <div class="form-group">
                    <label>Employee Code *</label>
                    <input type="text" name="emp_code" value="<?php echo $employee['emp_code'] ?? ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="full_name" value="<?php echo $employee['full_name'] ?? ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?php echo $employee['email'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" value="<?php echo $employee['phone'] ?? ''; ?>">
                </div>
                
                <div class="form-group">
                    <label>Department *</label>
                    <select name="dept_id" required>
                        <option value="">Select Department</option>
                        <?php foreach ($departments as $dept): ?>
                        <option value="<?php echo $dept['dept_id']; ?>" <?php echo ($employee['dept_id'] ?? '') == $dept['dept_id'] ? 'selected' : ''; ?>>
                            <?php echo $dept['dept_name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Position *</label>
                    <select name="pos_id" required>
                        <option value="">Select Position</option>
                        <?php foreach ($positions as $pos): ?>
                        <option value="<?php echo $pos['pos_id']; ?>" <?php echo ($employee['pos_id'] ?? '') == $pos['pos_id'] ? 'selected' : ''; ?>>
                            <?php echo $pos['pos_name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Hire Date *</label>
                    <input type="date" name="hire_date" value="<?php echo $employee['hire_date'] ?? ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Status *</label>
                    <select name="status" required>
                        <option value="Active" <?php echo ($employee['status'] ?? 'Active') == 'Active' ? 'selected' : ''; ?>>Active</option>
                        <option value="Inactive" <?php echo ($employee['status'] ?? '') == 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-primary"><?php echo $edit_mode ? 'Update' : 'Save'; ?> Employee</button>
                    <a href="employees.php" class="btn" style="background: #e2e8f0;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
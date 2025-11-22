<?php
require_once 'config/database.php';
// checkLogin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $emp_id = $_POST['emp_id'];
    $month = $_POST['month'];
    $base_salary = $_POST['base_salary'];
    $deductions = $_POST['deductions'];
    $allowances = $_POST['allowances'];
    
    // Calculate net salary
    $net_salary = $base_salary - $deductions + $allowances;
    
    // Check if payroll already exists for this month
    $stmt = $conn->prepare("SELECT * FROM tb_payroll WHERE emp_id = ? AND month = ?");
    $stmt->execute([$emp_id, $month]);
    
    if ($stmt->rowCount() > 0) {
        $error = "Payroll already exists for this employee in " . $month;
    } else {
        $stmt = $conn->prepare("INSERT INTO tb_payroll (emp_id, month, base_salary, deductions, allowances, net_salary, payment_status) VALUES (?, ?, ?, ?, ?, ?, 'Unpaid')");
        $stmt->execute([$emp_id, $month, $base_salary, $deductions, $allowances, $net_salary]);
        
        header('Location: payroll.php');
        exit();
    }
}

// Get employees with their positions for salary info
$employees = $conn->query("
    SELECT e.emp_id, e.emp_code, e.full_name, p.base_salary 
    FROM tb_employee e
    LEFT JOIN tb_position p ON e.pos_id = p.pos_id
    WHERE e.status='Active' 
    ORDER BY e.full_name
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Payroll - HR System</title>
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
        <h1 style="margin-bottom: 30px;">Generate Payroll</h1>
        
        <div class="card">
            <?php if (isset($error)): ?>
                <div style="background: #fed7d7; color: #742a2a; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" id="payrollForm">
                <div class="form-group">
                    <label>Select Employee *</label>
                    <select name="emp_id" id="emp_id" required onchange="updateSalary()">
                        <option value="">Choose employee...</option>
                        <?php foreach ($employees as $emp): ?>
                        <option value="<?php echo $emp['emp_id']; ?>" data-salary="<?php echo $emp['base_salary']; ?>">
                            <?php echo $emp['emp_code'] . ' - ' . $emp['full_name']; ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Month (YYYY-MM) *</label>
                    <input type="month" name="month" value="<?php echo date('Y-m'); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Base Salary *</label>
                    <input type="number" name="base_salary" id="base_salary" step="0.01" required>
                </div>
                
                <div class="form-group">
                    <label>Deductions</label>
                    <input type="number" name="deductions" id="deductions" step="0.01" value="0" onchange="calculateNet()">
                    <small style="color: #718096;">Tax, insurance, absences, etc.</small>
                </div>
                
                <div class="form-group">
                    <label>Allowances</label>
                    <input type="number" name="allowances" id="allowances" step="0.01" value="0" onchange="calculateNet()">
                    <small style="color: #718096;">Bonus, overtime, benefits, etc.</small>
                </div>
                
                <div class="form-group">
                    <label>Net Salary (Auto-calculated)</label>
                    <input type="text" id="net_salary" readonly style="background: #f7fafc; font-weight: bold; font-size: 18px; color: #2d3748;">
                </div>
                
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-success">Generate Payroll</button>
                    <a href="payroll.php" class="btn" style="background: #e2e8f0;">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function updateSalary() {
            const select = document.getElementById('emp_id');
            const selectedOption = select.options[select.selectedIndex];
            const salary = selectedOption.getAttribute('data-salary') || 0;
            document.getElementById('base_salary').value = salary;
            calculateNet();
        }

        function calculateNet() {
            const base = parseFloat(document.getElementById('base_salary').value) || 0;
            const deductions = parseFloat(document.getElementById('deductions').value) || 0;
            const allowances = parseFloat(document.getElementById('allowances').value) || 0;
            const net = base - deductions + allowances;
            document.getElementById('net_salary').value = '$' + net.toFixed(2);
        }
    </script>
</body>
</html>
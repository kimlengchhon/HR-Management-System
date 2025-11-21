<?php
require_once 'config/database.php';
// checkLogin();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $emp_id = $_POST['emp_id'];
    $review_date = $_POST['review_date'];
    $rating = $_POST['rating'];
    $comments = $_POST['comments'];
    $reviewer_id = $_SESSION['user_id'];
    
    $stmt = $conn->prepare("INSERT INTO tb_performance (emp_id, review_date, rating, comments, reviewer_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$emp_id, $review_date, $rating, $comments, $reviewer_id]);
    
    header('Location: performance.php');
    exit();
}

// Get all performance records
$performances = $conn->query("
    SELECT p.*, e.emp_code, e.full_name 
    FROM tb_performance p
    JOIN tb_employee e ON p.emp_id = e.emp_id
    ORDER BY p.review_date DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Get active employees for form
$employees = $conn->query("SELECT emp_id, emp_code, full_name FROM tb_employee WHERE status='Active' ORDER BY full_name")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance Evaluation - HR System</title>
    <link rel="stylesheet" href="css/common.css">
    <style>
        .rating-stars {
            font-size: 20px;
            color: #fbbf24;
        }
        .performance-form {
            background: #f7fafc;
            padding: 25px;
            border-radius: 8px;
            margin-bottom: 30px;
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
        <h1 style="margin-bottom: 30px;">Performance Evaluation</h1>
        
        <div class="card">
            <h3 style="margin-bottom: 20px;">Add Performance Review</h3>
            
            <form method="POST" class="performance-form">
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
                    <label>Review Date *</label>
                    <input type="date" name="review_date" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Rating (1-5) *</label>
                    <select name="rating" required>
                        <option value="">Select rating...</option>
                        <option value="5">⭐⭐⭐⭐⭐ Excellent (5)</option>
                        <option value="4">⭐⭐⭐⭐ Very Good (4)</option>
                        <option value="3">⭐⭐⭐ Good (3)</option>
                        <option value="2">⭐⭐ Fair (2)</option>
                        <option value="1">⭐ Poor (1)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Comments *</label>
                    <textarea name="comments" rows="4" required placeholder="Performance evaluation comments..."></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Submit Evaluation</button>
            </form>
        </div>

        <div class="card">
            <h3 style="margin-bottom: 20px;">Performance History</h3>
            
            <?php if (empty($performances)): ?>
                <p style="text-align: center; color: #718096; padding: 40px;">No performance records found</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Review Date</th>
                            <th>Rating</th>
                            <th>Comments</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($performances as $perf): ?>
                        <tr>
                            <td><?php echo $perf['emp_code'] . ' - ' . $perf['full_name']; ?></td>
                            <td><?php echo date('M d, Y', strtotime($perf['review_date'])); ?></td>
                            <td>
                                <span class="rating-stars">
                                    <?php echo str_repeat('⭐', $perf['rating']); ?>
                                </span>
                                <span style="color: #718096; font-size: 14px;">(<?php echo $perf['rating']; ?>/5)</span>
                            </td>
                            <td><?php echo $perf['comments']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
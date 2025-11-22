<?php
require_once 'config/database.php';
// checkLogin();

if (!isset($_GET['id'])) {
    header('Location: recruitment.php');
    exit();
}

$job_id = $_GET['id'];

// Get job details
$stmt = $conn->prepare("
    SELECT j.*, d.dept_name, p.pos_name
    FROM tb_job_posting j
    LEFT JOIN tb_department d ON j.dept_id = d.dept_id
    LEFT JOIN tb_position p ON j.pos_id = p.pos_id
    WHERE j.job_id = ?
");
$stmt->execute([$job_id]);
$job = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$job) {
    header('Location: recruitment.php');
    exit();
}

// Get applicants for this job
$stmt = $conn->prepare("SELECT * FROM tb_applicant WHERE job_id = ? ORDER BY applied_date DESC");
$stmt->execute([$job_id]);
$applicants = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle adding manual applicant
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $cover_letter = $_POST['cover_letter'];
    $applied_date = $_POST['applied_date'];
    
    $stmt = $conn->prepare("INSERT INTO tb_applicant (job_id, full_name, email, phone, cover_letter, applied_date, status) VALUES (?, ?, ?, ?, ?, ?, 'Applied')");
    $stmt->execute([$job_id, $full_name, $email, $phone, $cover_letter, $applied_date]);
    
    header("Location: job_applicants.php?id=$job_id");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applicants - <?php echo $job['job_title']; ?></title>
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
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <div>
                <h1><?php echo $job['job_title']; ?></h1>
                <p style="color: #718096; margin-top: 5px;">
                    <?php echo $job['dept_name']; ?> - <?php echo $job['pos_name']; ?> | 
                    Deadline: <?php echo date('M d, Y', strtotime($job['deadline'])); ?>
                </p>
            </div>
            <a href="recruitment.php" class="btn" style="background: #e2e8f0;">Back</a>
        </div>

        <!-- Add Applicant Form -->
        <div class="card" style="margin-bottom: 30px;">
            <h3 style="margin-bottom: 20px;">➕ Add Manual Applicant</h3>
            <form method="POST">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div class="form-group">
                        <label>Full Name *</label>
                        <input type="text" name="full_name" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" name="phone">
                    </div>
                    
                    <div class="form-group">
                        <label>Applied Date *</label>
                        <input type="date" name="applied_date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Cover Letter</label>
                    <textarea name="cover_letter" rows="3"></textarea>
                </div>
                
                <button type="submit" class="btn btn-success">Add Applicant</button>
            </form>
        </div>

        <!-- Applicants List -->
        <div class="card">
            <h3 style="margin-bottom: 20px;">Applicants (<?php echo count($applicants); ?>)</h3>
            
            <?php if (empty($applicants)): ?>
                <p style="text-align: center; color: #718096; padding: 40px;">No applicants yet</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Applied Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applicants as $applicant): ?>
                        <tr>
                            <td><strong><?php echo $applicant['full_name']; ?></strong></td>
                            <td><?php echo $applicant['email']; ?></td>
                            <td><?php echo $applicant['phone']; ?></td>
                            <td><?php echo date('M d, Y', strtotime($applicant['applied_date'])); ?></td>
                            <td>
                                <?php
                                $badge = 'badge-info';
                                if ($applicant['status'] == 'Shortlisted') $badge = 'badge-warning';
                                if ($applicant['status'] == 'Passed') $badge = 'badge-success';
                                if ($applicant['status'] == 'Rejected') $badge = 'badge-danger';
                                if ($applicant['status'] == 'Hired') $badge = 'badge-success';
                                ?>
                                <span class="badge <?php echo $badge; ?>"><?php echo $applicant['status']; ?></span>
                            </td>
                            <td>
                                <a href="applicant_view.php?id=<?php echo $applicant['applicant_id']; ?>" 
                                   class="btn btn-primary" 
                                   style="padding: 5px 10px; font-size: 12px;">
                                    Process
                                </a>
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
<?php
require_once 'config/database.php';
// checkLogin();

if (!isset($_GET['id'])) {
    header('Location: recruitment.php');
    exit();
}

$applicant_id = $_GET['id'];

// Handle status updates
if (isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action == 'shortlist') {
        $stmt = $conn->prepare("UPDATE tb_applicant SET status='Shortlisted' WHERE applicant_id=?");
        $stmt->execute([$applicant_id]);
    } elseif ($action == 'schedule_interview') {
        $interview_date = $_POST['interview_date'];
        $interview_time = $_POST['interview_time'];
        $interviewer = $_POST['interviewer'];
        $location = $_POST['location'];
        
        $stmt = $conn->prepare("INSERT INTO tb_interview (applicant_id, interview_date, interview_time, interviewer, location, result) VALUES (?, ?, ?, ?, ?, 'Pending')");
        $stmt->execute([$applicant_id, $interview_date, $interview_time, $interviewer, $location]);
        
        $conn->prepare("UPDATE tb_applicant SET status='Interview' WHERE applicant_id=?")->execute([$applicant_id]);
    } elseif ($action == 'pass') {
        $stmt = $conn->prepare("UPDATE tb_applicant SET status='Passed' WHERE applicant_id=?");
        $stmt->execute([$applicant_id]);
    } elseif ($action == 'reject') {
        $stmt = $conn->prepare("UPDATE tb_applicant SET status='Rejected' WHERE applicant_id=?");
        $stmt->execute([$applicant_id]);
    }
    
    header("Location: applicant_view.php?id=$applicant_id");
    exit();
}

// Get applicant details
$stmt = $conn->prepare("
    SELECT a.*, j.job_title, j.dept_id, j.pos_id, d.dept_name, p.pos_name
    FROM tb_applicant a
    JOIN tb_job_posting j ON a.job_id = j.job_id
    LEFT JOIN tb_department d ON j.dept_id = d.dept_id
    LEFT JOIN tb_position p ON j.pos_id = p.pos_id
    WHERE a.applicant_id = ?
");
$stmt->execute([$applicant_id]);
$applicant = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$applicant) {
    header('Location: recruitment.php');
    exit();
}

// Get interview history
$stmt = $conn->prepare("SELECT * FROM tb_interview WHERE applicant_id = ? ORDER BY interview_date DESC");
$stmt->execute([$applicant_id]);
$interviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Process Applicant - <?php echo $applicant['full_name']; ?></title>
    <link rel="stylesheet" href="css/common.css">
    <style>
        .process-steps {
            display: flex;
            justify-content: space-between;
            margin: 30px 0;
            padding: 20px;
            background: #f7fafc;
            border-radius: 8px;
        }
        .step {
            text-align: center;
            flex: 1;
            padding: 15px;
            position: relative;
        }
        .step::after {
            content: '→';
            position: absolute;
            right: -20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
            color: #cbd5e0;
        }
        .step:last-child::after {
            display: none;
        }
        .step.active {
            background: #667eea;
            color: white;
            border-radius: 8px;
        }
        .step.completed {
            background: #48bb78;
            color: white;
            border-radius: 8px;
        }
        .action-section {
            background: #f7fafc;
            padding: 20px;
            border-radius: 8px;
            margin-top: 20px;
        }
    </style>
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
            <h1><?php echo $applicant['full_name']; ?></h1>
            <a href="job_applicants.php?id=<?php echo $applicant['job_id']; ?>" class="btn" style="background: #e2e8f0;">Back</a>
        </div>

        <!-- Process Steps -->
        <div class="process-steps">
            <div class="step <?php echo in_array($applicant['status'], ['Applied']) ? 'active' : 'completed'; ?>">
                <strong>Applied</strong>
            </div>
            <div class="step <?php echo $applicant['status'] == 'Shortlisted' ? 'active' : ($applicant['status'] == 'Applied' ? '' : 'completed'); ?>">
                <strong>Shortlisted</strong>
            </div>
            <div class="step <?php echo $applicant['status'] == 'Interview' ? 'active' : (in_array($applicant['status'], ['Passed', 'Hired']) ? 'completed' : ''); ?>">
                <strong>Interview</strong>
            </div>
            <div class="step <?php echo $applicant['status'] == 'Passed' ? 'active' : ($applicant['status'] == 'Hired' ? 'completed' : ''); ?>">
                <strong>Passed</strong>
            </div>
            <div class="step <?php echo $applicant['status'] == 'Hired' ? 'active' : ''; ?>">
                <strong>Hired</strong>
            </div>
        </div>

        <!-- Applicant Details -->
        <div class="card">
            <h3 style="margin-bottom: 20px;">Applicant Information</h3>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <p><strong>Applied For:</strong> <?php echo $applicant['job_title']; ?></p>
                    <p><strong>Department:</strong> <?php echo $applicant['dept_name']; ?></p>
                    <p><strong>Position:</strong> <?php echo $applicant['pos_name']; ?></p>
                </div>
                <div>
                    <p><strong>Email:</strong> <?php echo $applicant['email']; ?></p>
                    <p><strong>Phone:</strong> <?php echo $applicant['phone']; ?></p>
                    <p><strong>Applied Date:</strong> <?php echo date('M d, Y', strtotime($applicant['applied_date'])); ?></p>
                </div>
            </div>
            
            <?php if ($applicant['cover_letter']): ?>
            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #e2e8f0;">
                <strong>Cover Letter:</strong>
                <p style="margin-top: 10px; color: #4a5568;"><?php echo nl2br($applicant['cover_letter']); ?></p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Interview History -->
        <?php if (!empty($interviews)): ?>
        <div class="card">
            <h3 style="margin-bottom: 20px;">Interview History</h3>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Interviewer</th>
                        <th>Location</th>
                        <th>Result</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($interviews as $interview): ?>
                    <tr>
                        <td><?php echo date('M d, Y', strtotime($interview['interview_date'])); ?></td>
                        <td><?php echo date('h:i A', strtotime($interview['interview_time'])); ?></td>
                        <td><?php echo $interview['interviewer']; ?></td>
                        <td><?php echo $interview['location']; ?></td>
                        <td>
                            <?php
                            $badge = 'badge-warning';
                            if ($interview['result'] == 'Passed') $badge = 'badge-success';
                            if ($interview['result'] == 'Failed') $badge = 'badge-danger';
                            ?>
                            <span class="badge <?php echo $badge; ?>"><?php echo $interview['result']; ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- Actions -->
        <div class="card">
            <h3 style="margin-bottom: 20px;">Actions</h3>
            
            <?php if ($applicant['status'] == 'Applied'): ?>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="shortlist">
                    <button type="submit" class="btn btn-success">✓ Shortlist Candidate</button>
                </form>
                <form method="POST" style="display: inline;">
                    <input type="hidden" name="action" value="reject">
                    <button type="submit" class="btn btn-danger" onclick="return confirm('Reject this applicant?')">✗ Reject</button>
                </form>
            
            <?php elseif ($applicant['status'] == 'Shortlisted' || $applicant['status'] == 'Interview'): ?>
                <div class="action-section">
                    <h4 style="margin-bottom: 15px;">Schedule Interview</h4>
                    <form method="POST">
                        <input type="hidden" name="action" value="schedule_interview">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                            <div class="form-group">
                                <label>Interview Date *</label>
                                <input type="date" name="interview_date" required>
                            </div>
                            <div class="form-group">
                                <label>Interview Time *</label>
                                <input type="time" name="interview_time" required>
                            </div>
                            <div class="form-group">
                                <label>Interviewer *</label>
                                <input type="text" name="interviewer" required>
                            </div>
                            <div class="form-group">
                                <label>Location *</label>
                                <input type="text" name="location" required placeholder="e.g. Office Room 301">
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Schedule Interview</button>
                    </form>
                </div>
                
                <div style="margin-top: 15px;">
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="pass">
                        <button type="submit" class="btn btn-success">✓ Mark as Passed</button>
                    </form>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="reject">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Reject this applicant?')">✗ Reject</button>
                    </form>
                </div>
            
            <?php elseif ($applicant['status'] == 'Passed'): ?>
                <a href="applicant_hire.php?id=<?php echo $applicant_id; ?>" class="btn btn-success" style="font-size: 16px; padding: 12px 24px;">
                    ✓ Hire as Employee
                </a>
            
            <?php elseif ($applicant['status'] == 'Hired'): ?>
                <div style="padding: 20px; background: #c6f6d5; border-radius: 8px; color: #22543d; text-align: center;">
                    <strong>✓ This applicant has been hired as an employee</strong>
                </div>
            
            <?php elseif ($applicant['status'] == 'Rejected'): ?>
                <div style="padding: 20px; background: #fed7d7; border-radius: 8px; color: #742a2a; text-align: center;">
                    <strong>✗ This applicant has been rejected</strong>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
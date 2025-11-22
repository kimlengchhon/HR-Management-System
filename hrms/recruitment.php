<?php
require_once 'config/database.php';
// checkLogin();

// Get all job postings with applicant count
$jobs = $conn->query("
    SELECT j.*, d.dept_name, p.pos_name, COUNT(a.applicant_id) as applicant_count
    FROM tb_job_posting j
    LEFT JOIN tb_department d ON j.dept_id = d.dept_id
    LEFT JOIN tb_position p ON j.pos_id = p.pos_id
    LEFT JOIN tb_applicant a ON j.job_id = a.job_id
    GROUP BY j.job_id
    ORDER BY j.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Get recent applicants
$applicants = $conn->query("
    SELECT a.*, j.job_title
    FROM tb_applicant a
    JOIN tb_job_posting j ON a.job_id = j.job_id
    ORDER BY a.created_at DESC
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recruitment - HR System</title>
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
            <h1>Recruitment Management</h1>
            <a href="job_add.php" class="btn btn-primary">➕ Post New Job</a>
        </div>
        
        <div class="card">
            <h3 style="margin-bottom: 20px;">Job Openings</h3>
            
            <?php if (empty($jobs)): ?>
                <p style="text-align: center; color: #718096; padding: 40px;">No job postings yet</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Job Title</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Salary Range</th>
                            <th>Deadline</th>
                            <th>Applicants</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($jobs as $job): ?>
                        <tr>
                            <td><strong><?php echo $job['job_title']; ?></strong></td>
                            <td><?php echo $job['dept_name']; ?></td>
                            <td><?php echo $job['pos_name']; ?></td>
                            <td><?php echo $job['salary_range']; ?></td>
                            <td><?php echo date('M d, Y', strtotime($job['deadline'])); ?></td>
                            <td>
                                <span class="badge badge-info"><?php echo $job['applicant_count']; ?> Applied</span>
                            </td>
                            <td>
                                <span class="badge <?php echo $job['status'] == 'Open' ? 'badge-success' : 'badge-danger'; ?>">
                                    <?php echo $job['status']; ?>
                                </span>
                            </td>
                            <td>
                                <a href="job_applicants.php?id=<?php echo $job['job_id']; ?>" 
                                   class="btn btn-primary" 
                                   style="padding: 5px 10px; font-size: 12px;">
                                    View Applicants
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <div class="card">
            <h3 style="margin-bottom: 20px;">Recent Applicants</h3>
            
            <?php if (empty($applicants)): ?>
                <p style="text-align: center; color: #718096; padding: 40px;">No applicants yet</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Applied For</th>
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
                            <td><?php echo $applicant['full_name']; ?></td>
                            <td><?php echo $applicant['job_title']; ?></td>
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
                                    View
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
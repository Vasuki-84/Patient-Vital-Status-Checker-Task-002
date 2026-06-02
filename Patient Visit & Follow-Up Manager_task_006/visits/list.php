<?php

require_once '../config/db.php';

// SQL calculates days since visit, overdue/upcoming status
$stmt = $pdo->prepare("
    SELECT 
        v.*,
        p.name as patient_name,
        DATEDIFF(CURDATE(), v.visit_date) as days_since_visit,
        CASE 
            WHEN v.follow_up_due < CURDATE() AND NOT EXISTS (
                SELECT 1 FROM visits v2 
                WHERE v2.patient_id = v.patient_id AND v2.visit_date > v.follow_up_due
            ) THEN 'Overdue'
            WHEN v.follow_up_due BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) THEN 'Upcoming'
            WHEN v.follow_up_due >= CURDATE() THEN 'Scheduled'
            ELSE 'Past'
        END as follow_up_status
    FROM visits v
    JOIN patients p ON v.patient_id = p.patient_id
    ORDER BY v.visit_date DESC
");

$stmt->execute();
$visits = $stmt->fetchAll();

include '../includes/header.php';
?>

<div class="card shadow">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h4 class="mb-0"><i class="bi bi-list-check"></i> All Visits</h4>
        <a href="add.php" class="btn btn-primary btn-custom">
            <i class="bi bi-plus-circle"></i> New Visit
        </a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Patient</th>
                        <th>Visit Date</th>
                        <th>Consultation</th>
                        <th>Lab Fee</th>
                        <th>Total</th>
                        <th>Days Since</th>
                        <th>Follow-up Due</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($visits as $visit): ?>
                    <tr>
                        <td><?php echo $visit['visit_id']; ?></td>
                        <td><?php echo htmlspecialchars($visit['patient_name']); ?></td>
                        <td><?php echo date('Y-m-d', strtotime($visit['visit_date'])); ?></td>
                        <td>$<?php echo number_format($visit['consultation_fee'], 2); ?></td>
                        <td>$<?php echo number_format($visit['lab_fee'], 2); ?></td>
                        <td><strong>$<?php echo number_format($visit['consultation_fee'] + $visit['lab_fee'], 2); ?></strong></td>
                        <td><?php echo $visit['days_since_visit']; ?> days</td>
                        <td><?php echo $visit['follow_up_due']; ?></td>
                        <td>
                            <span class="badge bg-<?php 
                                echo $visit['follow_up_status'] == 'Overdue' ? 'danger' : 
                                    ($visit['follow_up_status'] == 'Upcoming' ? 'warning' : 'secondary'); 
                            ?>">
                                <?php echo $visit['follow_up_status']; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
<?php
require_once '../config/db.php';

// Full summary report with all calculated fields
$stmt = $pdo->prepare("
    SELECT 
        p.name,
        p.patient_id,
        TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) as age,
        COUNT(v.visit_id) as total_visits,
        MAX(v.visit_date) as last_visit_date,
        DATEDIFF(CURDATE(), MAX(v.visit_date)) as days_since_last_visit,
        (SELECT follow_up_due FROM visits v2 
         WHERE v2.patient_id = p.patient_id 
         ORDER BY v2.visit_date DESC LIMIT 1) as next_follow_up,
        CASE 
            WHEN COUNT(v.visit_id) = 0 THEN 'No Visits'
            WHEN (SELECT follow_up_due FROM visits v2 
                  WHERE v2.patient_id = p.patient_id 
                  ORDER BY v2.visit_date DESC LIMIT 1) < CURDATE() 
            THEN 'Overdue'
            WHEN (SELECT follow_up_due FROM visits v2 
                  WHERE v2.patient_id = p.patient_id 
                  ORDER BY v2.visit_date DESC LIMIT 1) >= CURDATE() 
            THEN 'Scheduled'
            ELSE 'N/A'
        END as follow_up_status
    FROM patients p
    LEFT JOIN visits v ON p.patient_id = v.patient_id
    GROUP BY p.patient_id
    ORDER BY days_since_last_visit DESC
");

$stmt->execute();
$summary = $stmt->fetchAll();

include '../includes/header.php';
?>

<div class="card shadow">
    <div class="card-header bg-white">
        <h4 class="mb-0"><i class="bi bi-file-text-fill"></i> Complete Patient Summary Report</h4>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover" id="summaryTable">
                <thead class="table-dark">
                    <tr>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Total Visits</th>
                        <th>Last Visit</th>
                        <th>Days Since Visit</th>
                        <th>Next Follow-up</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($summary as $patient): ?>
                    <tr>
                        <td>
                            <a href="../patients/view.php?id=<?php echo $patient['patient_id']; ?>" class="text-decoration-none">
                                <?php echo htmlspecialchars($patient['name']); ?>
                            </a>
                        </td>
                        <td><?php echo $patient['age']; ?></td>
                        <td><?php echo $patient['total_visits']; ?></td>
                        <td><?php echo $patient['last_visit_date'] ?: 'Never'; ?></td>
                        <td><?php echo $patient['days_since_last_visit'] ?: 'N/A'; ?></td>
                        <td><?php echo $patient['next_follow_up'] ?: 'None'; ?></td>
                        <td>
                            <span class="badge bg-<?php 
                                echo $patient['follow_up_status'] == 'Overdue' ? 'danger' : 
                                    ($patient['follow_up_status'] == 'Scheduled' ? 'success' : 'secondary'); 
                            ?>">
                                <?php echo $patient['follow_up_status']; ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- jQuery ready function -->
<script>
$(document).ready(function() {
    $('#summaryTable').DataTable({
        pageLength: 25,
        responsive: true
    });
});
</script>

<?php include '../includes/footer.php'; ?>
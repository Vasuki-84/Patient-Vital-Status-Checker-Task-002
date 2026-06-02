<?php
require_once '../config/db.php';

// Get encoded ID from URL and decode it
$encoded_id = isset($_GET['id']) ? $_GET['id'] : 0;
$patient_id = base64_decode($encoded_id);

// Validate if decoding was successful and ID is numeric
if (!$patient_id || !is_numeric($patient_id)) {
    $_SESSION['error'] = "Invalid patient ID";
    redirect('../patients/list.php');
}

// Get patient info
$stmt = $pdo->prepare("SELECT name FROM patients WHERE patient_id = ?");
$stmt->execute([$patient_id]);
$patient = $stmt->fetch();

if (!$patient) {
    redirect('../patients/list.php');
}

// SQL calculates total visits and days between first and last visit
$stmt = $pdo->prepare("
    SELECT 
        v.*,
        DATEDIFF(CURDATE(), v.visit_date) as days_since_visit,
        (SELECT COUNT(*) FROM visits WHERE patient_id = ?) as total_visits,
        DATEDIFF(MAX(v.visit_date), MIN(v.visit_date)) as days_between_first_last
    FROM visits v
    WHERE v.patient_id = ?
    GROUP BY v.visit_id
    ORDER BY v.visit_date DESC
");

$stmt->execute([$patient_id, $patient_id]);
$visits = $stmt->fetchAll();

$stats = $visits ? $visits[0] : null;

include '../includes/header.php';
?>

<div class="card shadow">
    <div class="card-header bg-white">
        <h4 class="mb-0">
            <i class="bi bi-clock-history"></i> 
            Visit History: <?php echo htmlspecialchars($patient['name']); ?>
        </h4>
    </div>
    <div class="card-body">
        <?php if ($stats): ?>
            <div class="alert alert-success mb-3">
                <strong>Summary:</strong> 
                Total Visits: <?php echo $stats['total_visits']; ?> | 
                Days between first & last visit: <?php echo $stats['days_between_first_last']; ?> days
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle"></i> 
                No visits recorded for this patient yet.
            </div>
        <?php endif; ?>
        
        <?php if(count($visits) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Visit Date</th>
                            <th>Consultation Fee</th>
                            <th>Lab Fee</th>
                            <th>Total</th>
                            <th>Days Since</th>
                            <th>Follow-up Due</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($visits as $visit): ?>
                        <tr>
                            <td><?php echo date('F d, Y', strtotime($visit['visit_date'])); ?></td>
                            <td>$<?php echo number_format($visit['consultation_fee'], 2); ?></td>
                            <td>$<?php echo number_format($visit['lab_fee'], 2); ?></td>
                            <td><strong>$<?php echo number_format($visit['consultation_fee'] + $visit['lab_fee'], 2); ?></strong></td>
                            <td><?php echo $visit['days_since_visit']; ?> days</td>
                            <td><?php echo date('F d, Y', strtotime($visit['follow_up_due'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        
        <div class="mt-3">
            <a href="../patients/view.php?id=<?php echo $encoded_id; ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Patient
            </a>
            <a href="../visits/add.php?patient_id=<?php echo $encoded_id; ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add New Visit
            </a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
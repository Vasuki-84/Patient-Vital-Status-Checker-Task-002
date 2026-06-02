<?php
require_once '../config/db.php';

// Upcoming follow-ups (next 7 days)
$stmt = $pdo->prepare("
    SELECT 
        p.name,
        p.phone,
        v.follow_up_due,
        v.visit_date as last_visit_date,
        DATEDIFF(v.follow_up_due, CURDATE()) as days_until_due
    FROM visits v
    JOIN patients p ON v.patient_id = p.patient_id
    WHERE v.follow_up_due BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
    ORDER BY v.follow_up_due ASC
");
$stmt->execute();
$upcoming = $stmt->fetchAll();

// Overdue follow-ups 
$stmt = $pdo->prepare("
    SELECT 
        p.name,
        p.phone,
        v.follow_up_due,
        v.visit_date as last_visit_date,
        DATEDIFF(CURDATE(), v.follow_up_due) as days_overdue
    FROM visits v
    JOIN patients p ON v.patient_id = p.patient_id
    WHERE v.follow_up_due < CURDATE() 
    AND NOT EXISTS (
        SELECT 1 FROM visits v2 
        WHERE v2.patient_id = v.patient_id AND v2.visit_date > v.follow_up_due
    )
    ORDER BY v.follow_up_due ASC
");
$stmt->execute();
$overdue = $stmt->fetchAll();

// Missed follow-ups (no visit after due date)
$stmt = $pdo->prepare("
    SELECT 
        p.name,
        p.phone,
        v.follow_up_due,
        v.visit_date as last_visit_date,
        DATEDIFF(CURDATE(), v.follow_up_due) as days_missed
    FROM visits v
    JOIN patients p ON v.patient_id = p.patient_id
    WHERE v.follow_up_due < CURDATE() 
    AND v.patient_id NOT IN (
        SELECT DISTINCT patient_id FROM visits 
        WHERE visit_date > v.follow_up_due
    )
    ORDER BY v.follow_up_due DESC
    LIMIT 20
");
$stmt->execute();
$missed = $stmt->fetchAll();

include '../includes/header.php';
?>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card shadow">
            <div class="card-header bg-warning">
                <h5 class="mb-0"><i class="bi bi-bell"></i> Upcoming (Next 7 Days)</h5>
            </div>
            <div class="card-body">
                <?php if(count($upcoming) > 0): ?>
                    <div class="list-group">
                        <?php foreach($upcoming as $item): ?>
                            <div class="list-group-item">
                                <strong><?php echo htmlspecialchars($item['name']); ?></strong><br>
                                Due: <?php echo $item['follow_up_due']; ?> 
                                (in <?php echo $item['days_until_due']; ?> days)<br>
                                <small>Last visit: <?php echo $item['last_visit_date']; ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No upcoming follow-ups</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card shadow">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Overdue</h5>
            </div>
            <div class="card-body">
                <?php if(count($overdue) > 0): ?>
                    <div class="list-group">
                        <?php foreach($overdue as $item): ?>
                            <div class="list-group-item list-group-item-danger">
                                <strong><?php echo htmlspecialchars($item['name']); ?></strong><br>
                                Due: <?php echo $item['follow_up_due']; ?> 
                                (<?php echo $item['days_overdue']; ?> days overdue)<br>
                                <small>Last visit: <?php echo $item['last_visit_date']; ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No overdue follow-ups</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card shadow">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="bi bi-x-circle"></i> Missed Follow-ups</h5>
            </div>
            <div class="card-body">
                <?php if(count($missed) > 0): ?>
                    <div class="list-group">
                        <?php foreach($missed as $item): ?>
                            <div class="list-group-item">
                                <strong><?php echo htmlspecialchars($item['name']); ?></strong><br>
                                Missed since: <?php echo $item['follow_up_due']; ?> 
                                (<?php echo $item['days_missed']; ?> days)<br>
                                <small>Phone: <?php echo $item['phone']; ?></small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No missed follow-ups</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
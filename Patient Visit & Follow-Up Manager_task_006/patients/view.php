<?php
require_once '../config/db.php';

// Get encoded ID from URL and decode it
$encoded_id = isset($_GET['id']) ? $_GET['id'] : 0;
$id = base64_decode($encoded_id);

// Validate if decoding was successful and ID is numeric
if (!$id || !is_numeric($id)) {
    $_SESSION['error'] = "Invalid patient ID";
    redirect('../patients/list.php');
}

// Convert to integer for safety
$id = (int)$id;

// SQL calculates age, days since last visit, next follow-up, overdue status
$stmt = $pdo->prepare("
    SELECT 
        p.*,
        TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) as age_years,
        CONCAT(
            TIMESTAMPDIFF(YEAR, p.dob, CURDATE()), ' years, ',
            TIMESTAMPDIFF(MONTH, p.dob, CURDATE()) % 12, ' months'
        ) as age_full,
        MAX(v.visit_date) as last_visit_date,
        DATEDIFF(CURDATE(), MAX(v.visit_date)) as days_since_last_visit,
        (SELECT follow_up_due FROM visits v2 
         WHERE v2.patient_id = p.patient_id 
         ORDER BY v2.visit_date DESC LIMIT 1) as next_follow_up,
        CASE 
            WHEN (SELECT follow_up_due FROM visits v2 
                  WHERE v2.patient_id = p.patient_id 
                  ORDER BY v2.visit_date DESC LIMIT 1) < CURDATE() 
            THEN 'Overdue'
            WHEN (SELECT follow_up_due FROM visits v2 
                  WHERE v2.patient_id = p.patient_id 
                  ORDER BY v2.visit_date DESC LIMIT 1) >= CURDATE() 
            THEN 'Upcoming'
            ELSE 'No Follow-up'
        END as follow_up_status
    FROM patients p
    LEFT JOIN visits v ON p.patient_id = v.patient_id
    WHERE p.patient_id = ?
    GROUP BY p.patient_id
");

$stmt->execute([$id]);
$patient = $stmt->fetch();

if (!$patient) {
    $_SESSION['error'] = "Patient not found";
    redirect('../patients/list.php');
}

include '../includes/header.php';
?>

<div class="card shadow">
    <div class="card-header bg-white">
        <h4 class="mb-0"><i class="bi bi-person-badge"></i> Patient Details</h4>
    </div>
    <div class="card-body">
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless">
                    <tr>
                        <th width="150">Name:</th>
                        <td><?php echo htmlspecialchars($patient['name']); ?></td>
                    </tr>
                    <tr>
                        <th>Age:</th>
                        <td><?php echo $patient['age_full']; ?></td>
                    </tr>
                    <tr>
                        <th>Date of Birth:</th>
                        <td><?php echo date('F d, Y', strtotime($patient['dob'])); ?></td>
                    </tr>
                    <tr>
                        <th>Join Date:</th>
                        <td><?php echo date('F d, Y', strtotime($patient['join_date'])); ?></td>
                    </tr>
                    <tr>
                        <th>Phone:</th>
                        <td><?php echo htmlspecialchars($patient['phone']); ?></td>
                    </tr>
                    <tr>
                        <th>Address:</th>
                        <td><?php echo nl2br(htmlspecialchars($patient['address'])); ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <div class="alert alert-info">
                    <h6><i class="bi bi-calendar"></i> Visit Information</h6>
                    <hr>
                    <p><strong>Last Visit Date:</strong> <?php echo $patient['last_visit_date'] ? date('F d, Y', strtotime($patient['last_visit_date'])) : 'No visits'; ?></p>
                    <p><strong>Days Since Last Visit:</strong> <?php echo $patient['days_since_last_visit'] ?: 'N/A'; ?></p>
                    <p><strong>Next Follow-up:</strong> <?php echo $patient['next_follow_up'] ? date('F d, Y', strtotime($patient['next_follow_up'])) : 'No follow-up scheduled'; ?></p>
                    <p><strong>Follow-up Status:</strong> 
                        <span class="badge bg-<?php echo $patient['follow_up_status'] == 'Overdue' ? 'danger' : 'success'; ?>">
                            <?php echo $patient['follow_up_status']; ?>
                        </span>
                    </p>
                </div>
            </div>
        </div>
        
        <div class="mt-3">
            <a href="list.php" class="btn btn-secondary">Back to List</a>
            <?php 
            // Encode the ID again for the Add Visit link
            $encoded_id_for_visit = base64_encode($id);
            ?>
            <a href="../visits/add.php?patient_id=<?php echo $encoded_id_for_visit; ?>" class="btn btn-primary">Add Visit</a>


           
        </div>
     
    </div>
</div>

<?php include '../includes/footer.php'; ?>
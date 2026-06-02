<?php
require_once __DIR__ . '/../config/db.php';
include __DIR__ . '/../includes/header.php';

$can_edit = canEditPatients();
$can_delete = canDelete();


// Show messages
if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php
$stmt = $pdo->prepare("
    SELECT 
        p.patient_id,
        p.name,
        p.dob,
        p.join_date,
        p.phone,
        TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) as age_years,
        CONCAT(
            TIMESTAMPDIFF(YEAR, p.dob, CURDATE()), ' years, ',
            TIMESTAMPDIFF(MONTH, p.dob, CURDATE()) % 12, ' months'
        ) as age_full,
        DATE_FORMAT(p.join_date, '%Y') as join_year,
        DATE_FORMAT(p.join_date, '%M') as join_month,
        COUNT(v.visit_id) as total_visits
    FROM patients p
    LEFT JOIN visits v ON p.patient_id = v.patient_id
    GROUP BY p.patient_id
    ORDER BY p.name
");
$stmt->execute();
$patients = $stmt->fetchAll();
?>

<div class="card shadow">
    <div class="card-header bg-white d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h4 class="mb-0"><i class="bi bi-people"></i> Patient List</h4>
        <?php if($can_edit): ?>
        <a href="add.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add Patient
        </a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <!-- Desktop Table -->
        <div class="table-responsive d-none d-md-block">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr><th>ID</th><th>Name</th><th>Age</th><th>Full Age</th><th>Joined</th><th>Visits</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php foreach($patients as $patient): 
                        $encoded_id = base64_encode($patient['patient_id']);
                    ?>
                    <tr>
                        <td><?php echo $patient['patient_id']; ?></td>
                        <td><?php echo htmlspecialchars($patient['name']); ?></td>
                        <td><?php echo $patient['age_years']; ?> years</td>
                        <td><?php echo $patient['age_full']; ?></td>
                        <td><?php echo $patient['join_month'] . ' ' . $patient['join_year']; ?></td>
                        <td><span class="badge bg-info"><?php echo $patient['total_visits']; ?></span></td>
                        <td>
                            <a href="view.php?id=<?php echo $encoded_id; ?>" class="btn btn-sm btn-outline-info">View</a>
                            <?php if($can_edit): ?>
                            <a href="edit.php?id=<?php echo $encoded_id; ?>" class="btn btn-sm btn-outline-warning">Edit</a>
                            <?php endif; ?>
                           
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Mobile Cards -->
        <div class="d-md-none">
            <?php foreach($patients as $patient): 
                $encoded_id = base64_encode($patient['patient_id']);
            ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($patient['name']); ?></h5>
                    <p class="card-text mb-1"><strong>Age:</strong> <?php echo $patient['age_full']; ?></p>
                    <p class="card-text mb-1"><strong>Joined:</strong> <?php echo $patient['join_month'] . ' ' . $patient['join_year']; ?></p>
                    <p class="card-text"><strong>Visits:</strong> <?php echo $patient['total_visits']; ?></p>
                    <div class="btn-group w-100">
                        <a href="view.php?id=<?php echo $encoded_id; ?>" class="btn btn-sm btn-outline-info">View</a>
                        <?php if($can_edit): ?>
                        <a href="edit.php?id=<?php echo $encoded_id; ?>" class="btn btn-sm btn-outline-warning">Edit</a>
                        <?php endif; ?>
                        <?php if($can_delete): ?>
                        <a href="?delete_id=<?php echo $encoded_id; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this patient?')">Delete</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
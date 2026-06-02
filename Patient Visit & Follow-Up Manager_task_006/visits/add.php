<?php
require_once '../config/db.php';

// Add permission check - only Doctor and Admin can add visits
checkPermission('add');

// Get patients for dropdown
$patients = $pdo->query("SELECT patient_id, name FROM patients ORDER BY name")->fetchAll();

// Check if user submitted the form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = (int)$_POST['patient_id'];
    $visit_date = $_POST['visit_date'];
    $consultation_fee = (float)$_POST['consultation_fee'];
    $lab_fee = (float)$_POST['lab_fee'];
    
    // SQL calculates follow_up_due (visit_date + 7 days)
    $stmt = $pdo->prepare("
        INSERT INTO visits (patient_id, visit_date, consultation_fee, lab_fee, follow_up_due) 
        VALUES (?, ?, ?, ?, DATE_ADD(?, INTERVAL 7 DAY))
    ");
    
    if ($stmt->execute([$patient_id, $visit_date, $consultation_fee, $lab_fee, $visit_date])) {
        $_SESSION['success'] = "Visit added successfully";
        // Fix the redirect - use full path or proper relative path
        header("Location: list.php");
        exit();
    }
}

include '../includes/header.php';
?>

<div class="card shadow">
    <div class="card-header bg-white">
        <h4 class="mb-0"><i class="bi bi-calendar-plus"></i> Add New Visit</h4>
    </div>
    <div class="card-body">
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php 
                echo $_SESSION['success']; 
                unset($_SESSION['success']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Patient *</label>
                    <select name="patient_id" class="form-control" required>
                        <option value="">Select Patient</option>
                        <?php foreach($patients as $p): ?>
                            <option value="<?php echo $p['patient_id']; ?>" 
                                <?php echo (isset($_GET['patient_id']) && $_GET['patient_id'] == $p['patient_id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($p['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Visit Date *</label>
                    <input type="date" name="visit_date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Consultation Fee ($)</label>
                    <input type="number" step="0.01" name="consultation_fee" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Lab Fee ($)</label>
                    <input type="number" step="0.01" name="lab_fee" class="form-control" value="0">
                </div>
                <div class="col-12">
                    <div class="alert alert-secondary">
                        <strong>Note:</strong> Follow-up date will be automatically set to 7 days after visit date (calculated by SQL)
                    </div>
                    <button type="submit" class="btn btn-primary">Save Visit</button>
                    <a href="list.php" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
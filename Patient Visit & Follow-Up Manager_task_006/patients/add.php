<?php
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name']);
    $dob = $_POST['dob'];
    $join_date = $_POST['join_date'];
    $phone = sanitizeInput($_POST['phone']);
    $address = sanitizeInput($_POST['address']);
    
    // Validation
    $errors = [];
    if (strtotime($dob) > time()) $errors[] = "DOB cannot be future date";
    if (empty($join_date)) $errors[] = "Join date is required";
    
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO patients (name, dob, join_date, phone, address) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $dob, $join_date, $phone, $address])) {
            $_SESSION['success'] = "Patient added successfully";
            redirect('list.php');
        }
    }
}

include '../includes/header.php';
?>

<div class="card shadow">
    <div class="card-header bg-white">
        <h4 class="mb-0"><i class="bi bi-person-plus"></i> Add New Patient</h4>
    </div>
    <div class="card-body">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach($errors as $error): ?>
                    <div><?php echo $error; ?></div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Date of Birth *</label>
                    <input type="date" name="dob" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Join Date *</label>
                    <input type="date" name="join_date" class="form-control" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control">
                </div>
                <div class="col-12 mb-3">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="3"></textarea>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Save Patient</button>
                    <a href="list.php" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
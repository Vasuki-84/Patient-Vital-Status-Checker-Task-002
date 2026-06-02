<?php
require_once '../config/db.php';
include '../includes/header.php';

// SIMPLIFIED VERSION 1: Get birthdays in next 30 days using two conditions
$stmt = $pdo->prepare("
    SELECT 
        p.name,
        p.dob,
        p.phone,
        DATE_FORMAT(p.dob, '%M %d') as birthday,
        TIMESTAMPDIFF(YEAR, p.dob, CURDATE()) as current_age,
        CASE 
            WHEN DATE_ADD(p.dob, INTERVAL YEAR(CURDATE()) - YEAR(p.dob) YEAR) >= CURDATE()
            THEN DATE_ADD(p.dob, INTERVAL YEAR(CURDATE()) - YEAR(p.dob) YEAR)
            ELSE DATE_ADD(p.dob, INTERVAL YEAR(CURDATE()) + 1 - YEAR(p.dob) YEAR)
        END as next_birthday_date
    FROM patients p
    HAVING next_birthday_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
    ORDER BY MONTH(p.dob), DAY(p.dob)
");
$stmt->execute();
$upcoming_birthdays = $stmt->fetchAll();

// Patients turning specific ages this year (40, 50, 60)
$stmt = $pdo->prepare("
    SELECT 
        name,
        dob,
        TIMESTAMPDIFF(YEAR, dob, CURDATE()) as current_age,
        TIMESTAMPDIFF(YEAR, dob, DATE(CONCAT(YEAR(CURDATE()), '-12-31'))) as age_by_year_end
    FROM patients 
    HAVING age_by_year_end IN (40, 50, 60)
");
$stmt->execute();
$turning_ages = $stmt->fetchAll();

?>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="bi bi-gift-fill"></i> Birthdays in Next 30 Days</h5>
            </div>
            <div class="card-body">
                <?php if(count($upcoming_birthdays) > 0): ?>
                    <div class="list-group">
                        <?php foreach($upcoming_birthdays as $birthday): ?>
                            <div class="list-group-item list-group-item-info">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?php echo htmlspecialchars($birthday['name']); ?></strong><br>
                                        Birthday: <?php echo $birthday['birthday']; ?><br>
                                        <small>Current age: <?php echo $birthday['current_age']; ?> (Turning <?php echo $birthday['current_age'] + 1; ?>)</small><br>
                                        <?php if($birthday['phone']): ?>
                                            <small>Phone: <?php echo htmlspecialchars($birthday['phone']); ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <i class="bi bi-calendar-heart fs-1"></i>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No birthdays in the next 30 days</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-stars"></i> Milestone Birthdays (40, 50, 60)</h5>
            </div>
            <div class="card-body">
                <?php if(count($turning_ages) > 0): ?>
                    <div class="list-group">
                        <?php foreach($turning_ages as $person): ?>
                            <div class="list-group-item">
                                <strong><?php echo htmlspecialchars($person['name']); ?></strong><br>
                                Will turn <?php echo $person['age_by_year_end']; ?> years old this year<br>
                                <small>Date of Birth: <?php echo date('F d, Y', strtotime($person['dob'])); ?></small><br>
                                <small>Current age: <?php echo $person['current_age']; ?> years</small>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted">No milestone birthdays (40, 50, or 60) this year</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
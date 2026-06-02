<?php
require_once '../config/db.php';

// Visits per month (last 12 months)
$stmt = $pdo->prepare("
    SELECT 
        DATE_FORMAT(visit_date, '%Y-%m') as month,
        DATE_FORMAT(visit_date, '%M %Y') as month_name,
        COUNT(*) as visits_count,
        SUM(consultation_fee + lab_fee) as total_revenue
    FROM visits 
    WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
    GROUP BY DATE_FORMAT(visit_date, '%Y-%m')
    ORDER BY month DESC
");
$stmt->execute();
$monthly_visits = $stmt->fetchAll();

// Patients joined per month
$stmt = $pdo->prepare("
    SELECT 
        DATE_FORMAT(join_date, '%Y-%m') as month,
        DATE_FORMAT(join_date, '%M %Y') as month_name,
        COUNT(*) as patients_joined
    FROM patients 
    WHERE join_date >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
    GROUP BY DATE_FORMAT(join_date, '%Y-%m')
    ORDER BY month DESC
");
$stmt->execute();
$monthly_joins = $stmt->fetchAll();

// Patients grouped by join month (all time)
$stmt = $pdo->prepare("
    SELECT 
        DATE_FORMAT(join_date, '%M') as month_name,
        MONTH(join_date) as month_num,
        COUNT(*) as count
    FROM patients 
    GROUP BY MONTH(join_date)
    ORDER BY month_num
");
$stmt->execute();
$join_groups = $stmt->fetchAll();

include '../includes/header.php';
?>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header bg-white">
                <h5 class="mb-0">📈 Monthly Visits (Last 12 Months)</h5>
            </div>
            <div class="card-body">
                <canvas id="monthlyVisitsChart" height="300"></canvas>
                <div class="table-responsive mt-3">
                    <table class="table table-sm">
                        <thead>
                            <tr><th>Month</th><th>Visits</th><th>Revenue</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach($monthly_visits as $data): ?>
                            <tr>
                                <td><?php echo $data['month_name']; ?></td>
                                <td><?php echo $data['visits_count']; ?></td>
                                <td>$<?php echo number_format($data['total_revenue'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header bg-white">
                <h5 class="mb-0">👥 Patients Joined by Month</h5>
            </div>
            <div class="card-body">
                <canvas id="monthlyJoinsChart" height="300"></canvas>
                <div class="table-responsive mt-3">
                    <table class="table table-sm">
                        <thead>
                            <tr><th>Month</th><th>Patients Joined</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach($monthly_joins as $data): ?>
                            <tr>
                                <td><?php echo $data['month_name']; ?></td>
                                <td><?php echo $data['patients_joined']; ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header bg-white">
        <h5 class="mb-0">📊 Patients Grouped by Join Month (All Time)</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr><th>Month</th><th>Number of Patients</th></tr>
                </thead>
                <tbody>
                    <?php foreach($join_groups as $group): ?>
                    <tr>
                        <td><?php echo $group['month_name']; ?></td>
                        <td><?php echo $group['count']; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Monthly visits chart
const visitsCtx = document.getElementById('monthlyVisitsChart').getContext('2d');
new Chart(visitsCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode(array_column(array_reverse($monthly_visits), 'month_name')); ?>,
        datasets: [{
            label: 'Number of Visits',
            data: <?php echo json_encode(array_column(array_reverse($monthly_visits), 'visits_count')); ?>,
            backgroundColor: 'rgba(102, 126, 234, 0.6)',
            borderColor: '#667eea',
            borderWidth: 1
        }]
    }
});

// Monthly joins chart
const joinsCtx = document.getElementById('monthlyJoinsChart').getContext('2d');
new Chart(joinsCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_column(array_reverse($monthly_joins), 'month_name')); ?>,
        datasets: [{
            label: 'Patients Joined',
            data: <?php echo json_encode(array_column(array_reverse($monthly_joins), 'patients_joined')); ?>,
            borderColor: '#28a745',
            backgroundColor: 'rgba(40, 167, 69, 0.1)',
            tension: 0.4,
            fill: true
        }]
    }
});
</script>

<?php include '../includes/footer.php'; ?>
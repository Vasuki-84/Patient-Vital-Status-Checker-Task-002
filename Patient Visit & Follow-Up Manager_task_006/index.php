<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

require_once __DIR__ . '/config/db.php';
include __DIR__ . '/includes/header.php';

// Add role-based content control - show different things based on role
$currentUser = getCurrentUser();

// All SQL calculations for dashboard stats
$stats = [];    // Creates empty array to store dashboard statistics

// Total patients
$stmt = $pdo->query("SELECT COUNT(*) as total FROM patients");
$stats['total_patients'] = $stmt->fetch()['total'];

// Total visits
$stmt = $pdo->query("SELECT COUNT(*) as total FROM visits");
$stats['total_visits'] = $stmt->fetch()['total'];

// Overdue follow-ups
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM visits WHERE follow_up_due < CURDATE()");
$stmt->execute();
$stats['overdue'] = $stmt->fetch()['total'];

// Upcoming follow-ups (next 7 days)
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM visits WHERE follow_up_due BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)");
$stmt->execute();
$stats['upcoming'] = $stmt->fetch()['total'];

// Recent visits (last 30 days)
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM visits WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
$stmt->execute();
$stats['recent_visits'] = $stmt->fetch()['total'];

// Role-based message
if (isset($_SESSION['role']) && $_SESSION['role'] == 'admin') {
    // Show admin-specific content
    $roleMessage = "You have full administrative access to manage all features.";
} elseif (isset($_SESSION['role']) && $_SESSION['role'] == 'doctor') {
    // Show doctor-specific content  
    $roleMessage = "You can manage patient visits and follow-ups.";
} else {
    // Show staff-specific content
    $roleMessage = "You can view patient information and manage records.";
}
?>

<div class="alert alert-info alert-dismissible fade show mb-4" role="alert">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <strong><i class="bi bi-person-circle"></i> Welcome, <?php echo htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username']); ?>!</strong>
            <span class="badge bg-primary ms-2"><?php echo ucfirst($_SESSION['role'] ?? 'Staff'); ?></span>
            <div class="mt-1">
                <small><?php echo $roleMessage; ?></small>
            </div>
        </div>
        <div>
            <small class="text-muted">
                <i class="bi bi-clock"></i> Login: <?php echo isset($_SESSION['login_time']) ? date('H:i:s', $_SESSION['login_time']) : date('H:i:s'); ?>
            </small>
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>

<div class="row mb-4">
    <div class="col-12 col-sm-6 col-xl-3 mb-3">
        <div class="card card-stats bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1">Total Patients</h6>
                        <h2 class="mb-0"><?php echo $stats['total_patients']; ?></h2>
                    </div>
                    <i class="bi bi-people fs-1"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3 mb-3">
        <div class="card card-stats bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1">Total Visits</h6>
                        <h2 class="mb-0"><?php echo $stats['total_visits']; ?></h2>
                    </div>
                    <i class="bi bi-calendar-heart fs-1"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3 mb-3">
        <div class="card card-stats bg-warning text-dark h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1">Upcoming Follow-ups</h6>
                        <h2 class="mb-0"><?php echo $stats['upcoming']; ?></h2>
                    </div>
                    <i class="bi bi-bell-fill fs-1"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3 mb-3">
        <div class="card card-stats bg-danger text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-1">Overdue Follow-ups</h6>
                        <h2 class="mb-0"><?php echo $stats['overdue']; ?></h2>
                    </div>
                    <i class="bi bi-exclamation-triangle-fill fs-1"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12 col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-graph-up"></i> Monthly Visits Trend</h5>
            </div>
            <div class="card-body">
                <canvas id="visitsChart" height="250" style="max-width: 100%; height: auto;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Critical Alerts</h5>
            </div>
            <div class="card-body">
                <?php
                // Inactive patients (180+ days)
                $stmt = $pdo->prepare("
                    SELECT p.name, p.patient_id, MAX(v.visit_date) as last_visit, 
                    DATEDIFF(CURDATE(), IFNULL(MAX(v.visit_date), '1900-01-01')) as days_inactive
                    FROM patients p 
                    LEFT JOIN visits v ON p.patient_id = v.patient_id 
                    GROUP BY p.patient_id 
                    HAVING days_inactive >= 180 OR MAX(v.visit_date) IS NULL
                    LIMIT 5
                ");
                $stmt->execute();
                $inactive = $stmt->fetchAll();
                
                if(count($inactive) > 0): ?>
                    <div class="alert alert-warning mb-3">
                        <strong><i class="bi bi-hourglass-split"></i> Inactive Patients (180+ days):</strong>
                        <ul class="mb-0 mt-2">
                            <?php foreach($inactive as $patient): ?>
                                <li>
                                    <?php echo htmlspecialchars($patient['name']); ?> - 
                                    <?php echo $patient['days_inactive'] >= 180 ? $patient['days_inactive'] . ' days ago' : 'Never visited'; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php else: ?>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle"></i> No inactive patients found!
                    </div>
                <?php endif; ?>
                
                <?php
                // Zero-visit patients
                $stmt = $pdo->query("
                    SELECT name, patient_id FROM patients 
                    WHERE patient_id NOT IN (SELECT DISTINCT patient_id FROM visits WHERE patient_id IS NOT NULL) 
                    LIMIT 5
                ");
                $zeroVisits = $stmt->fetchAll();
                if(count($zeroVisits) > 0): ?>
                    <div class="alert alert-info mb-0">
                        <strong><i class="bi bi-eye-slash"></i> Patients with No Visits:</strong>
                        <ul class="mb-0 mt-2">
                            <?php foreach($zeroVisits as $patient): ?>
                                <li><?php echo htmlspecialchars($patient['name']); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Monthly visits chart data from SQL
<?php
$stmt = $pdo->query("
    SELECT 
        DATE_FORMAT(visit_date, '%M %Y') as month,
        DATE_FORMAT(visit_date, '%Y-%m') as month_sort,
        COUNT(*) as count
    FROM visits 
    WHERE visit_date >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(visit_date, '%Y-%m'), DATE_FORMAT(visit_date, '%M %Y')
    ORDER BY month_sort ASC
");

$chartData = $stmt->fetchAll();
$months = [];
$counts = [];
foreach($chartData as $data) {
    $months[] = $data['month'];
    $counts[] = $data['count'];
}
?>

const ctx = document.getElementById('visitsChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($months); ?>,
        datasets: [{
            label: 'Number of Visits',
            data: <?php echo json_encode($counts); ?>,
            borderColor: '#667eea',
            backgroundColor: 'rgba(102, 126, 234, 0.1)',
            borderWidth: 2,
            tension: 0.4,
            fill: true,
            pointBackgroundColor: '#667eea',
            pointBorderColor: '#fff',
            pointBorderWidth: 2,
            pointRadius: 4,
            pointHoverRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    font: {
                        size: 12
                    }
                }
            },
            tooltip: {
                mode: 'index',
                intersect: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1,
                    font: {
                        size: 11
                    }
                },
                grid: {
                    color: 'rgba(0,0,0,0.05)'
                }
            },
            x: {
                ticks: {
                    font: {
                        size: 11
                    },
                    rotation: window.innerWidth < 768 ? 45 : 0
                },
                grid: {
                    display: false
                }
            }
        }
    }
});

// Make chart responsive on window resize
window.addEventListener('resize', function() {
    if (window.myChart) {
        window.myChart.resize();
    }
});
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
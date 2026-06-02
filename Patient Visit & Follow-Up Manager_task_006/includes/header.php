<?php
// includes/header.php - Pure Bootstrap version
// DO NOT include config/db.php here - it's already included in each page

if (!isset($_SESSION)) {
    session_start();
}

$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$current_page = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Healthcare Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">

<div class="container-fluid p-0">
    <div class="row g-0 min-vh-100">
        
        <!-- Sidebar - Bootstrap Offcanvas for mobile, regular column for desktop -->
        <?php if ($isLoggedIn): ?>
        <!-- Mobile Hamburger Button -->
        <div class="d-lg-none position-fixed top-0 start-0 m-3" style="z-index: 1050;">
            <button class="btn btn-primary rounded-3 shadow" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas">
                <i class="bi bi-list fs-4"></i>
            </button>
        </div>
        
        <!-- Desktop Sidebar (visible on lg and up) -->
        <div class="col-lg-3 col-xl-2 d-none d-lg-block bg-dark min-vh-100 p-3">
            <div class="text-center mb-4">
                <i class="bi bi-hospital fs-1 text-white"></i>
                <h5 class="text-white mt-2">HealthCare</h5>
                <small class="text-white-50">Patient Manager</small>
            </div>
            <hr class="bg-secondary">
            <nav class="nav flex-column">
                <a class="nav-link text-white-50 <?php echo ($current_page == 'index.php') ? 'active bg-primary text-white rounded' : ''; ?>" 
                   href="/Capminds-Tasks/Patient%20Visit%20&%20Follow-Up%20Manager_task_006/index.php">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
                <a class="nav-link text-white-50 <?php echo ($current_dir == 'patients') ? 'active bg-primary text-white rounded' : ''; ?>" 
                   href="/Capminds-Tasks/Patient%20Visit%20&%20Follow-Up%20Manager_task_006/patients/list.php">
                    <i class="bi bi-people me-2"></i> Patients
                </a>
                <a class="nav-link text-white-50 <?php echo ($current_dir == 'visits' && $current_page != 'add.php') ? 'active bg-primary text-white rounded' : ''; ?>" 
                   href="/Capminds-Tasks/Patient%20Visit%20&%20Follow-Up%20Manager_task_006/visits/list.php">
                    <i class="bi bi-calendar-check me-2"></i> All Visits
                </a>
                <a class="nav-link text-white-50 <?php echo ($current_page == 'add.php' && $current_dir == 'visits') ? 'active bg-primary text-white rounded' : ''; ?>" 
                   href="/Capminds-Tasks/Patient%20Visit%20&%20Follow-Up%20Manager_task_006/visits/add.php">
                    <i class="bi bi-plus-circle me-2"></i> New Visit
                </a>
                <hr class="bg-secondary">
                <a class="nav-link text-white-50 <?php echo ($current_page == 'followups.php') ? 'active bg-primary text-white rounded' : ''; ?>" 
                   href="/Capminds-Tasks/Patient%20Visit%20&%20Follow-Up%20Manager_task_006/reports/followups.php">
                    <i class="bi bi-bell me-2"></i> Follow-ups
                </a>
                <a class="nav-link text-white-50 <?php echo ($current_page == 'monthly.php') ? 'active bg-primary text-white rounded' : ''; ?>" 
                   href="/Capminds-Tasks/Patient%20Visit%20&%20Follow-Up%20Manager_task_006/reports/monthly.php">
                    <i class="bi bi-graph-up me-2"></i> Monthly Report
                </a>
                <a class="nav-link text-white-50 <?php echo ($current_page == 'birthdays.php') ? 'active bg-primary text-white rounded' : ''; ?>" 
                   href="/Capminds-Tasks/Patient%20Visit%20&%20Follow-Up%20Manager_task_006/reports/birthdays.php">
                    <i class="bi bi-gift me-2"></i> Birthdays
                </a>
                <a class="nav-link text-white-50 <?php echo ($current_page == 'summary.php') ? 'active bg-primary text-white rounded' : ''; ?>" 
                   href="/Capminds-Tasks/Patient%20Visit%20&%20Follow-Up%20Manager_task_006/reports/summary.php">
                    <i class="bi bi-file-text me-2"></i> Full Summary
                </a>
            </nav>
        </div>
        
        <!-- Mobile Offcanvas Sidebar -->
        <div class="offcanvas offcanvas-start bg-dark" tabindex="-1" id="sidebarOffcanvas">
            <div class="offcanvas-header border-bottom border-secondary">
                <div>
                    <i class="bi bi-hospital fs-3 text-white"></i>
                    <h5 class="text-white d-inline-block ms-2">HealthCare</h5>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body p-0">
                <nav class="nav flex-column p-3">
                    <a class="nav-link text-white-50 mb-2 <?php echo ($current_page == 'index.php') ? 'active bg-primary text-white rounded' : ''; ?>" 
                       href="/Capminds-Tasks/Patient%20Visit%20&%20Follow-Up%20Manager_task_006/index.php">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                    <a class="nav-link text-white-50 mb-2 <?php echo ($current_dir == 'patients') ? 'active bg-primary text-white rounded' : ''; ?>" 
                       href="/Capminds-Tasks/Patient%20Visit%20&%20Follow-Up%20Manager_task_006/patients/list.php">
                        <i class="bi bi-people me-2"></i> Patients
                    </a>
                    <a class="nav-link text-white-50 mb-2 <?php echo ($current_dir == 'visits' && $current_page != 'add.php') ? 'active bg-primary text-white rounded' : ''; ?>" 
                       href="/Capminds-Tasks/Patient%20Visit%20&%20Follow-Up%20Manager_task_006/visits/list.php">
                        <i class="bi bi-calendar-check me-2"></i> All Visits
                    </a>
                    <a class="nav-link text-white-50 mb-2 <?php echo ($current_page == 'add.php' && $current_dir == 'visits') ? 'active bg-primary text-white rounded' : ''; ?>" 
                       href="/Capminds-Tasks/Patient%20Visit%20&%20Follow-Up%20Manager_task_006/visits/add.php">
                        <i class="bi bi-plus-circle me-2"></i> New Visit
                    </a>
                    <hr class="bg-secondary">
                    <a class="nav-link text-white-50 mb-2 <?php echo ($current_page == 'followups.php') ? 'active bg-primary text-white rounded' : ''; ?>" 
                       href="/Capminds-Tasks/Patient%20Visit%20&%20Follow-Up%20Manager_task_006/reports/followups.php">
                        <i class="bi bi-bell me-2"></i> Follow-ups
                    </a>
                    <a class="nav-link text-white-50 mb-2 <?php echo ($current_page == 'monthly.php') ? 'active bg-primary text-white rounded' : ''; ?>" 
                       href="/Capminds-Tasks/Patient%20Visit%20&%20Follow-Up%20Manager_task_006/reports/monthly.php">
                        <i class="bi bi-graph-up me-2"></i> Monthly Report
                    </a>
                    <a class="nav-link text-white-50 mb-2 <?php echo ($current_page == 'birthdays.php') ? 'active bg-primary text-white rounded' : ''; ?>" 
                       href="/Capminds-Tasks/Patient%20Visit%20&%20Follow-Up%20Manager_task_006/reports/birthdays.php">
                        <i class="bi bi-gift me-2"></i> Birthdays
                    </a>
                    <a class="nav-link text-white-50 mb-2 <?php echo ($current_page == 'summary.php') ? 'active bg-primary text-white rounded' : ''; ?>" 
                       href="/Capminds-Tasks/Patient%20Visit%20&%20Follow-Up%20Manager_task_006/reports/summary.php">
                        <i class="bi bi-file-text me-2"></i> Full Summary
                    </a>
                </nav>
            </div>
        </div>
        
        <div class="col-lg-9 col-xl-10">
            <!-- Top Navbar -->
            <nav class="navbar navbar-light bg-white shadow-sm px-2 px-md-4 py-2 py-md-3 sticky-top">
                <div class="container-fluid p-0">
                    <div class="d-flex align-items-center justify-content-between w-100 gap-2 gap-md-4">
                        
                        <!-- Title Section -->
                        <div class="flex-grow-1 flex-lg-grow-0">
                            <div class="d-flex align-items-center justify-content-start">
                                <i class="bi bi-clipboard-pulse text-primary me-1 me-md-2 d-none d-sm-inline-block"></i>
                                <span class="fw-semibold d-inline-block" style="font-size: clamp(0.7rem, 3.5vw, 1.1rem); line-height: 1.3;">
                                    <span class="d-none d-sm-inline">Hospital Management System</span>
                                    <span class="d-inline d-sm-none">HMS</span>
                                </span>
                            </div>
                        </div>
                        
                        <!-- User Dropdown -->
                        <div class="dropdown flex-shrink-0">
                            <button class="btn btn-outline-secondary dropdown-toggle rounded-pill px-2 px-sm-3 py-1 py-sm-2" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i>
                                <span class="d-none d-md-inline-block ms-1"><?php echo htmlspecialchars($_SESSION['full_name'] ?? $_SESSION['username'] ?? 'User'); ?></span>
                                <span class="badge bg-primary ms-1"><?php echo htmlspecialchars($_SESSION['role'] ?? 'staff'); ?></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profile</a></li>
                                <li><a class="dropdown-item" href="/Capminds-Tasks/Patient%20Visit%20&%20Follow-Up%20Manager_task_006/change_password.php"><i class="bi bi-shield-lock me-2"></i>Change Password</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item text-danger" href="/Capminds-Tasks/Patient%20Visit%20&%20Follow-Up%20Manager_task_006/logout.php"><i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
            
            <!-- Content Wrapper -->
            <div class="p-3 p-md-4">
<?php endif; ?>
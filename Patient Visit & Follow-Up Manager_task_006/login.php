<?php
session_start();
require_once 'config/db.php';

// Redirect if already logged in
if (isset($_SESSION['user_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: index.php");
    exit();
}

$error = '';
$success = '';



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password";
    } else {
        try {
            // Fetch user from database
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND is_active = 1");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Verify hash password
                if (password_verify($password, $user['password'])) {
                    // Password is correct - set session
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['full_name'] = $user['full_name'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['logged_in'] = true;
                    $_SESSION['login_time'] = time();
                    
                    // Update last login time
                    $updateStmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?");
                    $updateStmt->execute([$user['user_id']]);
                    
                    // Redirect to dashboard
                    header("Location: index.php");
                    exit();
                } else {
                    $error = "Invalid password. Please try again.";
                }
            } else {
                $error = "Username not found. Please check your username.";
            }
        } catch (PDOException $e) {
            $error = "Database error. Please try again later.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Healthcare Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-primary-subtle bg-gradient min-vh-100 d-flex align-items-center justify-content-center p-3">
    <div class="card border-0 shadow-lg rounded-4" style="max-width: 450px; width: 100%;">
        <div class="card-header bg-primary bg-gradient text-white text-center border-0 rounded-top-4 p-4">
            <i class="bi bi-hospital fs-1 mb-2 d-block"></i>
            <h3 class="mb-0 fw-semibold">Healthcare Management System</h3>
            <p class="mb-0 mt-2 opacity-75">Patient Visit & Follow-Up Manager</p>
        </div>
        <div class="card-body p-4">
            <?php if($success): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                    <i class="bi bi-check-circle me-2"></i> <?php echo htmlspecialchars($success); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i> <?php echo htmlspecialchars($error); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Username</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="bi bi-person text-primary"></i>
                        </span>
                        <input type="text" name="username" class="form-control py-2" 
                               placeholder="Enter username" required autofocus>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label fw-semibold">Password</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light">
                            <i class="bi bi-lock text-primary"></i>
                        </span>
                        <input type="password" name="password" class="form-control py-2" 
                               placeholder="Enter password" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary bg-gradient w-100 py-2 fw-semibold">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Login
                </button>
            </form>
            <hr class="my-4">
            <div class="text-center">
                <small class="text-secondary">
                    <strong>Demo Credentials:</strong><br>
                    Username: <code class="bg-light px-1 rounded">admin</code> | Password: <code class="bg-light px-1 rounded">password</code><br>
                    Username: <code class="bg-light px-1 rounded">doctor</code> | Password: <code class="bg-light px-1 rounded">password</code><br>
                    Username: <code class="bg-light px-1 rounded">staff</code> | Password: <code class="bg-light px-1 rounded">password</code>
                </small>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
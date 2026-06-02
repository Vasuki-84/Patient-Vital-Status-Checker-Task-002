<?php
// config/db.php - Database configuration
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = 'localhost';
$port = '3308';
$dbname = 'healthcare';
$username = 'root';
$password = '';

// Define BASE_URL
if (!defined('BASE_URL')) {
    define('BASE_URL', '/Capminds-Tasks/Patient%20Visit%20&%20Follow-Up%20Manager_task_006');
}

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Session Management Functions
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: " . BASE_URL . "/login.php");
        exit();
    }
}

function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'user_id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'full_name' => $_SESSION['full_name'],
            'role' => $_SESSION['role']
        ];
    }
    return null;
}

// Role checking functions
function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

function isAdmin() {
    return hasRole('admin');
}

function isDoctor() {
    return hasRole('doctor');
}

function isStaff() {
    return hasRole('staff');
}

function canEditPatients() {
    return isAdmin() || isDoctor();
}

function canDelete() {
    return isAdmin();
}

// ADD THIS MISSING FUNCTION
function checkPermission($action) {
    switch($action) {
        case 'add':
        case 'edit':
            if (!canEditPatients()) {
                $_SESSION['error'] = "You don't have permission to perform this action";
                header("Location: " . BASE_URL . "/index.php");
                exit();
            }
            break;
        case 'delete':
            if (!canDelete()) {
                $_SESSION['error'] = "Only admin can delete records";
                header("Location: " . BASE_URL . "/index.php");
                exit();
            }
            break;
    }
}

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function redirect($url) {
    header("Location: " . BASE_URL . "/$url");
    exit();
}

// Check authentication for protected pages
$current_file = basename($_SERVER['PHP_SELF']);
if ($current_file != 'login.php' && $current_file != 'logout.php') {
    requireLogin();
}
?>
<?php
session_start();  // session resumes

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$username = $_SESSION['username'];
$email = $_SESSION['email'];
$theme = $_SESSION['theme'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="<?php echo $theme; ?>">

<div class="container d-flex justify-content-center  mt-5">
    <div class="card p-4 w-100" style="max-width:500px;">
        <h2>Welcome, <?php echo $username; ?></h2>
        <p>Email: <?php echo $email; ?></p>
        <p>Theme: <?php echo $theme; ?></p>

        <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
</div>

<style>
.dark { background-color:#121212; color:white; }
.warm { background-color:#f5e6d3; }
.light { background-color:#ffffff; }
</style>

</body>
</html>
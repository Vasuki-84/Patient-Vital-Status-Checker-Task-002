<?php
session_start();  // session starts

$username = $_COOKIE['remember_username'] ?? ""; // NULL coalesing 
$theme = $_COOKIE['user_theme'] ?? "light"; // Read browser cookies

$error = $_SESSION['error'] ?? "";  // Store server-side data
unset($_SESSION['error']);  // Flash message = temporary message shown only for one request
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="<?php echo $theme; ?>">

<div class="container-fluid">
    <div class="row min-vh-100">

        <!-- LEFT PANEL -->
        <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center left-panel">
            <div class="text-center text-white px-5">
                <h1 class="fw-bold">Welcome Back 👋</h1>
                <p class="mt-3">
                    Login to access your personalized dashboard, saved preferences, and theme settings.
                </p>
            </div>
        </div>

        <!-- RIGHT PANEL -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center">
            <div class="w-100 px-4" style="max-width:400px;">

                <div class="card shadow-lg border-0 p-4 rounded-4">
                    <h3 class="text-center mb-4 fw-bold">Login</h3>

                    <?php if($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form method="POST" action="auth.php">

                        <input type="text" name="username"
                               class="form-control mb-3 rounded-3"
                               placeholder="Username"
                               value="<?php echo $username; ?>">

                        <input type="email" name="email"
                               class="form-control mb-3 rounded-3"
                               placeholder="Email">

                        <input type="password" name="password"
                               class="form-control mb-3 rounded-3"
                               placeholder="Password"  autocomplete="new-password">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="remember" class="form-check-input">
                                <label class="form-check-label">Remember Me</label>
                            </div>
                        </div>

                        <button class="btn btn-primary w-100 rounded-3 fw-semibold">
                            Login
                        </button>

                    </form>
                </div>

            </div>
        </div>

    </div>
</div>

<style>

/* THEMES */
.dark {
    background-color:#121212;
    color:white;
}
.dark .card {
    background:#1e1e1e;
    color:white;
}
.dark input {
    background:#2a2a2a;
    color:white;
    border:none;
}

/* Warm Theme */
.warm {
    background-color:#f5e6d3;
}
.warm .left-panel {
    background:linear-gradient(135deg,#ff9a9e,#fad0c4);
}

/* Light Theme */
.light {
    background-color:#ffffff;
}
.light .left-panel {
    background:linear-gradient(135deg,#667eea,#764ba2);
}

/* LEFT PANEL COMMON */
.left-panel {
    background:linear-gradient(135deg,#667eea,#764ba2);
}

/* INPUT FOCUS */
input:focus {
    box-shadow:none !important;
    border:1px solid #667eea;
}

/* RESPONSIVE FIX */
@media(max-width: 991px) {
    .left-panel {
        display:none;
    }
}

</style>

</body>
</html>


<!-- http://localhost/Capminds-Tasks/login_system-task_004/login.php -->
<?php
session_start();
require "includes/validation.php";

// Get POST data
$username = $_POST['username'] ?? "";
$email = $_POST['email'] ?? "";
$password = $_POST['password'] ?? "";
$remember = isset($_POST['remember']);

// Validation
$errors = [];

if ($msg = validateUsername($username)) $errors[] = $msg;
if ($msg = validateEmail($email)) $errors[] = $msg;
if ($msg = validatePassword($password)) $errors[] = $msg;

if (!empty($errors)) {
    $_SESSION['error'] = implode("<br>", $errors);
    header("Location: login.php");
    exit;
}

//  Dummy Users (Multiple Users)
$users = [
    "user1" => [
        "email" => "user1@gmail.com",
        "password" => "User@123",
        "theme" => "dark"
    ],
    "user2" => [
        "email" => "user2@gmail.com",
        "password" => "User@123",
        "theme" => "warm"
    ],
    "user3" => [
        "email" => "user3@gmail.com",
        "password" => "User@123",
        "theme" => "light"
    ]
];

//  Authentication Check
if (isset($users[$username])) {

    $user = $users[$username];

    if ($email === $user['email'] && $password === $user['password']) {

        //  Assign theme
        $theme = $user['theme'];

        // Store session
        $_SESSION['username'] = $username;
        $_SESSION['email'] = $email;
        $_SESSION['theme'] = $theme;

        // Cookie logic (60 sec for testing / change to 7 days later)
        if ($remember) {
            setcookie("remember_username", $username, time() + 60);
            setcookie("user_theme", $theme, time() + 60);
        } else {
            setcookie("remember_username", "", time() - 3600);
        }

        //  Redirect to dashboard
        header("Location: dashboard.php");
        exit;

    } else {
        $_SESSION['error'] = "Invalid email or password";
        header("Location: login.php");
        exit;
    }

} else {
    $_SESSION['error'] = "User not found";
    header("Location: login.php");
    exit;
}
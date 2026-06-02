<?php

function validateUsername($username) {
    if (empty($username)) {
        return "Username is required";
    }
    if (strlen($username) < 3) {
        return "Username must be at least 3 characters";
    }
    return "";
}

function validateEmail($email) {
    if (empty($email)) {
        return "Email is required";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Invalid email format";
    }
    return "";
}

function validatePassword($password) {
    if (empty($password)) {
        return "Password is required";
    }
    if (strlen($password) < 6) {
        return "Password must be at least 6 characters";
    }
    return "";
}
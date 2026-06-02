<?php

namespace Utils;

class Validator {
    public function validateUsername($username) { //  Parameter receives the value sent to method.
        return strlen($username) >= 3 ? "Valid" : "Invalid";  // Ternary operator like if else
    }

    public function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) ? "Valid" : "Invalid"; // PHP checks whether email format is correct.
    }

    public function validatePassword($password) {
        return strlen($password) >= 6 ? "Strong Valid Password" : "Weak";
    }
}
?>
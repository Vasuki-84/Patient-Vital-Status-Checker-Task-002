<?php

namespace Utils;

class User {

   // By using public we access from anywhere
    public $username, $email, $password;

    // Store incoming value inside object
    public function __construct($username, $email, $password) {
        $this->username = $username;    // $this->username = "Rahul"
        $this->email = $email;
        $this->password = $password;
    }

    // This method returns only the username
    public function displayUser() {
        return "User: {$this->username}";
    }
}
?>
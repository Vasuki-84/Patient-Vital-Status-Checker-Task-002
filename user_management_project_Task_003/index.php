<?php

require_once "utils/User.php";
require_once "utils/Validator.php";
include_once "utils/helpers.php";

$users = require_once "data/users.php";


use Utils\User;                        // use keyword for optimization
use Utils\Validator as UserValidator; // rename using Alias to avoid conflicts and improve readability.

$validator = new UserValidator();     // Creates object from Validator class

foreach ($users as $data) {           // Alias to avoid conflicts and improve readability.
   $user = new User($data["username"], $data["email"], $data["password"]); //  create object, call constructor automatically, store values inside object
    
    // Access displayUser with Arrow operator
    echo $user->displayUser() . "<br>";

    echo "Username: " . $validator->validateUsername($user->username) . "<br>";   // An argument is the actual value passed when calling the function.
    echo "Email: " . $validator->validateEmail($user->email) . "<br>";
    echo "Password: " . $validator->validatePassword($user->password);

    line();
}


?>
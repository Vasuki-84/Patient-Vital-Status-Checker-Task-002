<?php

$host = "127.0.0.1";
$username = "root";
$password = "";
$database = "hospital_db";
$port = 3308;

$conn = mysqli_connect(   // Built-in PHP function, Used to connect MySQL
    $host,  // Connect to 127.0.0.1
    $username,   // Login as root
    $password,   // Use empty password
    $database,   // Use hospital_db database
    $port        // Connect through port 3308
);

if (!$conn) {
    die("Connection Failed: " . mysqli_connect_error());  // die()- Stop the program immediately
}

?>
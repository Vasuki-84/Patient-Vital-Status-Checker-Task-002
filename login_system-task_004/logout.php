<?php
session_start();  // session resumes

session_unset(); // Removes all session variables
session_destroy();  // Completely destroys session

header("Location: login.php");
exit;
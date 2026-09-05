<?php

session_start();

// Remove all session variables
$_SESSION = array();

// Destroy the current session
session_destroy();

// Send the user back to the login page
header("Location: login.php");
exit();

?>
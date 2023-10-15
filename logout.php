<?php
session_start();

// Resetting session variables
$_SESSION = array();

session_destroy();

header("Location: index.html");
exit;
?>
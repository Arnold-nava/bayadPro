<?php
session_start();
require_once("../../config/root.php");

// clear session data
$_SESSION = [];

// destroy session
session_destroy();

// redirect properly
header("Location: " . BASE_URL . "app/views/client/register.php");
exit();
?>
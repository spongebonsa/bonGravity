<?php
session_start();

// Clear all session data
$_SESSION = [];
session_unset();
session_destroy();

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Redirect to login or homepage
header("Location: login.php");
exit();

?>

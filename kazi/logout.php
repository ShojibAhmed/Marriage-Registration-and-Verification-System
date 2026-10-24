<?php

session_start();

// Destroy all session data
$_SESSION = array();

session_destroy();

// Go to Home Page
header("Location: ../index.php");
exit();

?>
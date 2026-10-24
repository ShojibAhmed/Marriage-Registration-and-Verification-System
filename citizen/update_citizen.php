<?php
session_start();
require_once "../includes/security.php";
require_kazi_auth();

// Modification is disabled by project specifications
header("Location: view_citizens.php?notice=locked");
exit();

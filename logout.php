<?php
require_once('setting.php');
require_once('guard.php');
require_once('killSession.php');
 
// Redirect to login page
header("location: login.php");
exit;
?>
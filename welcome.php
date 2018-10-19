<?php
require_once('setting.php');
require_once('guard.php');
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
/*
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
*/
require_once('template/html/header.html');
require_once('template/html/welcome.html');
require_once('template/html/footer.html');
?>
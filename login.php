<?php
require_once('setting.php');
require_once('guard.php');
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: welcome.php");
    exit;
}

// Include config file
require_once "setting.php";
 
// Define variables and initialize with empty values
$mid = $apiKey = "";
$mid_err = $apiKey_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if mid is empty
    if(empty(trim($_POST["mid"]))){
        $mid_err = "Please enter Merchant Id.";
    } else{
        $mid = trim($_POST["mid"]);
    }
    
    // Check if apiKey is empty
    if(empty(trim($_POST["apiKey"]))){
        $apiKey_err = "Please enter your API key.";
    } else{
        $apiKey = trim($_POST["apiKey"]);
    }
    
    // Validate credentials
    if(empty($mid_err) && empty($apiKey_err)){
       $getApiKey = $_updatedb->getApiKeyByMid($mid);
       //if valid inputs
       if (($getApiKey) && ($getApiKey === $apiKey)) {
            session_start();
                                
            // Store data in session variables
            $_SESSION["loggedin"] = true;
            $_SESSION["id"] = $id;
            $_SESSION["mid"] = $mid;                            
            $success = true;
            
            // Redirect user to welcome page
            header("location: welcome.php");
       } else {
           $success = false;
       }
    }
}

    require_once('template/html/header.html');
    require_once('setting.php');
    require_once('template/html/login.html'); 
    require_once('template/html/footer.html')
?>
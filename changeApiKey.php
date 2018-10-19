<?php
    require_once('setting.php');
    require_once('guard.php');
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
$newApiKey = -1;
$posted = (isset($_POST['walletAddress']) || isset($_POST['secretKey'])) ? 1 : 0;
if ($posted) {
    $walletAddress  = $_POST['walletAddress'];
    $secretKey      = $_POST['secretKey'];
    $newApiKey      = $_updatedb->getNewApiKey($walletAddress, $secretKey);
}
render('changeApikey.html');
?>
<script>
    isSuccess('<?php echo $newApiKey ?>');
</script>
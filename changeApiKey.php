<?php
require_once('setting.php');
require_once('guard.php');
// Initialize the session
session_start();
 
$newApiKey = -1;
$posted = (isset($_POST['walletAddress']) || isset($_POST['hash']) || isset($_POST['seed'])) ? 1 : 0;
if ($posted) {
    $walletAddress  = $_POST['walletAddress'];
    $seed           = $_POST['seed'];
    $hash           = $_POST['hash'];
    
    $newApiKey = $_updatedb->getNewApiKey($walletAddress, $seed, $hash);
    if ($newApiKey > 0) {require_once('killSession.php');} //logout after update API Key
}
 require_once('template/html/header.html');
 require_once('template/html/genapikey.html'); 
 require_once('template/html/footer.html');
//render('changeApikey.html');
?>
<script >
    isSuccess('<?php echo $newApiKey; ?>');    
</script>
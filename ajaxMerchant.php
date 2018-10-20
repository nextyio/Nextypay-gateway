<?php 

error_reporting(E_ERROR | E_PARSE);
require_once('setting.php');

if ($_POST['service'] == 'addMerchant') {
    $isMobile = $_POST['isMobile'];
    $wallet = $_POST['wallet'];
    $merchantName = $_POST['merchantName'];
    $url = $_POST['url'];
    $email = $_POST['email'];
    $output = $_updatedb->addMerchant($wallet, $merchantName, $url, $email, $gatewayWallet, $_functions, $isMobile); 
    if ($output) echo $output;
} else 
if ($_POST['service'] == 'checkStatus') {
    $wallet = $_POST['wallet'];
    $status = $_updatedb->getMerchantStatus($wallet);
    if ($status == 'Pending') {
        echo $status;
    } else {
        $pKey = $_updatedb->getMerchantKey($wallet);
        $apiKey = $_updatedb->getApiKey($wallet);
        $mid = $_updatedb->getMidByWallet($wallet);
        //echo "mid : $mid secret key : $key api key : $apiKey";
        echo $mid . " " . $apiKey . " " . $pKey;
    }
}
?>
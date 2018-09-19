<?php 

error_reporting(E_ERROR | E_PARSE);
require_once('setting.php');
$_url = $mainnet;

$npdb = new npdb($DBUSER, $DBPASSWORD, $DBNAME, $DBHOST);

$_updatedb->set_url($_url);
$_updatedb->set_connection($npdb);
$_updatedb->set_includes($_blockchain,$_functions);
$_updatedb->set_gatewayWallet($gatewayWallet);

///$returnUrl= $_updatedb->getReturnUrl($reqId);
//if ($returnUrl) echo $returnUrl;
//echo "ajaxMerchant called";
if ($_POST['service'] == 'addMerchant') {
    $wallet = $_POST['wallet'];
    $merchantName = $_POST['merchantName'];
    $url = $_POST['url'];
    $email = $_POST['email'];
    echo $_updatedb->addMerchant($wallet, $merchantName, $url, $email, $gatewayWallet, $_functions);
} else 
if ($_POST['service'] == 'checkStatus') {
    $wallet = $_POST['wallet'];
    echo $_updatedb->getMerchantStatus($wallet);
}
?>
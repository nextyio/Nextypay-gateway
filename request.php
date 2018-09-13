<?php
require_once('lib/npdb.php');

require_once('lib/nextypayblockchain.php');
require_once('lib/nextypayexchange.php');
require_once('lib/nextypayfunctions.php');
require_once('lib/nextypayupdatedb.php');


$_db_prefix='';
$_updatedb=new Nextypayupdatedb;
$_blockchain= new Nextypayblockchain;
$_exchange= new Nextypayexchange;
$_functions= new Nextypayfunctions;

$testnet = "http://125.212.250.61:11111";
$local = "http://127.0.0.1:8545";
$mainnet = 'http://13.228.68.50:8545';

$_url = $mainnet;

$DBUSER = "root";
$DBPASSWORD = "root123";
$DBNAME = "nextypay";
$DBHOST = "127.0.0.1";
$npdb = new npdb($DBUSER, $DBPASSWORD, $DBNAME, $DBHOST);

$_updatedb->set_url($_url);
$_updatedb->set_connection($npdb);
$_updatedb->set_includes($_blockchain,$_functions); 
    // $postfields['extraData'] = '0x0abc';
    // $postfields['callbackUrl'] = $systemUrl . '/modules/gateways/callback/' . $moduleName . '.php';
    // $postfields['returnUrl'] = $returnUrl;
    // $postfields['shopId'] = 2;
    // $postfields['orderId'] = $invoiceId;
    // $postfields['minBlockDistance'] = 10;
    // $postfields['toWallet'] = '0xbf878162f34a11c832339adb0cccddb1b091c1e5';
    // $postfields['wallet'] = '0xbf878162f34a11c832339adb0cccddb1b091c1e5';

    $callbackUrl = isset($_POST['callbackUrl']) ? utf8_decode(urldecode($_POST['callbackUrl'])) : '';
    $returnUrl = isset($_POST['returnUrl']) ? utf8_decode(urldecode($_POST['returnUrl'])) : '';
    $shopId = isset($_POST['shopId']) ? $_POST['shopId'] : '';
    $orderId = isset($_POST['orderId']) ? $_POST['orderId'] : '';
    $minBlockDistance = isset($_POST['minBlockDistance']) ? $_POST['minBlockDistance'] : '';
    $toWallet = isset($_POST['toWallet']) ? $_POST['toWallet'] : '';
    $wallet = isset($_POST['wallet']) ? $_POST['wallet'] : '';
    $amount = isset($_POST['amount']) ? $_POST['amount']: 0;
    $currency = isset($_POST['currency']) ? $_POST['currency']: 'nty';
    $_exchange->set_store_currency_code($currency);
    $ntyAmount = $_exchange->coinmarketcap_exchange($amount);
    $ntyAmount = 1; //TESTING

    $startTime = NULL;
    $endTime = NULL;
    $fromWallet = NULL;

    $QRText ='{"walletaddress":"'.$toWallet.'","uoid":"'.$orderId.'","amount":"'.$ntyAmount.'"}';
    $QRTextHex="0x".$_functions->strToHex($QRText);
    $extraData = $QRTextHex;
    $QRTextEncode= urlencode ( $QRText );

    $_updatedb->addRequest($shopId, $orderId, $extraData, $callbackUrl, $returnUrl, $ntyAmount, 
    $minBlockDistance, $startTime, $endTime, $fromWallet, $toWallet, $wallet ) ;

    echo "<br> $QRText <br>";
    echo "<br> $QRTextHex <br>";

    echo 'Waiting for your Payment... Page will be redirected after the payment. <br>';
    //echo wpautop( wptexturize( "<img style ='width:30px; display: inline ' src = '".get_site_url()."/wp-content/plugins/nextypay/images/Loading.gif'/>" ) );
    //echo wpautop( wptexturize( "<img style ='width:30px; display: inline ' src = 'wp-includes/js/tinymce/skins/lightgray/img/loader.gif'/>" ) );
    //Apps Link
    echo '<p><a href="https://play.google.com/store/apps/details?id=io.nexty.wallet">Click here to download Android payment app</a></p>';
    echo '<p><a href="https://nexty.io/ios">Click here to download IOS payment app</a></p>';

    //QR
    echo '<img src="https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl='
    .$QRTextEncode.'&choe=UTF-8" title="Link to Google.com" />';
    foreach ($_POST as $key => $value) {
        echo "Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>";
    }
?>
<?php
    require_once('template/header.html');
?>
    <link rel="stylesheet" href="template/css/request.css"/>  
<?php
    require_once('setting.php');

    $_url = $mainnet;

    $npdb = new npdb($DBUSER, $DBPASSWORD, $DBNAME, $DBHOST);

    $_updatedb->set_url($_url);
    $_updatedb->set_connection($npdb);
    $_updatedb->set_includes($_blockchain,$_functions); 

    $callbackUrl = isset($_POST['callbackUrl']) ? utf8_decode(urldecode($_POST['callbackUrl'])) : '';
    $returnUrl = isset($_POST['returnUrl']) ? utf8_decode(urldecode($_POST['returnUrl'])) : '';
    $shopId = isset($_POST['shopId']) ? $_POST['shopId'] : '';
    $orderId = isset($_POST['orderId']) ? $_POST['orderId'] : '';
    $minBlockDistance = isset($_POST['minBlockDistance']) ? $_POST['minBlockDistance'] : 1;
    $toWallet = isset($_POST['toWallet']) ? $_POST['toWallet'] : '';
    $wallet = isset($_POST['wallet']) ? $_POST['wallet'] : '';
    $amount = isset($_POST['amount']) ? $_POST['amount']: 0;
    $currency = isset($_POST['currency']) ? $_POST['currency']: 'nty';
    $_exchange->set_store_currency_code($currency);
    $ntyAmount = $_exchange->coinmarketcap_exchange($amount);
    $ntyAmount = $amount; //TESTING

    $startTime = NULL;
    $endTime = NULL;
    $fromWallet = NULL;

    //$postFormat = $callbackUrl && $shopId && $orderId && $toWallet && $wallet && $ntyAmount;

    $QRText ='{"walletaddress":"'.$toWallet.'","uoid":"'.$orderId.'","amount":"'.$ntyAmount.'"}';
    $QRTextHex="0x".$_functions->strToHex($QRText);
    $extraData = $QRTextHex;
    $QRTextEncode= urlencode ( $QRText );

    $reqId = $_updatedb->addRequest($shopId, $orderId, $extraData, $callbackUrl, $returnUrl, $ntyAmount, 
    $minBlockDistance, $startTime, $endTime, $fromWallet, $toWallet, $wallet ) ;

    $merchantName = "test Merchant Name";

    foreach ($_POST as $key => $value) {
        //echo "Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>"; //TEST
    }
    if ($reqId)
        require_once('template/request.html'); else require_once('template/error.html');
    require_once('template/footer.html')
?>
    <script>
        call_ajax(new Date(), <?php echo $reqId; ?>,600,3 );
    </script>
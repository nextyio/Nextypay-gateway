<?php
    require_once('setting.php');
    require_once('guard.php');
    require_once('template/html/header.html');
?>
    <link rel="stylesheet" href="template/css/request.css"/>  
<?php

    $callbackUrl = isset($_GET['callbackUrl']) ? utf8_decode(urldecode($_GET['callbackUrl'])) : '';
    $returnUrl = isset($_GET['returnUrl']) ? utf8_decode(urldecode($_GET['returnUrl'])) : '';
    $shopId = isset($_GET['shopId']) ? $_GET['shopId'] : '';
    $orderId = isset($_GET['orderId']) ? $_GET['orderId'] : '';
    $minBlockDistance = isset($_GET['minBlockDistance']) ? $_GET['minBlockDistance'] : 1;
    $mid = isset($_GET['mid']) ? $_GET['mid'] : '';
    $wallet = $_updatedb->getWalletByMid($mid);
    $apiKey = $_updatedb->getApiKeyByMid($mid);
    if ($apiKey != $_GET['apiKey']) {require_once('template/html/error.html');require_once('template/html/footer.html'); exit;}
    $toWallet = (isset($_GET['toWallet']) && ($_GET['toWallet'])) ? $_GET['toWallet'] : $wallet;
    $amount = isset($_GET['amount']) ? $_GET['amount']: 0;
    $currency = isset($_GET['currency']) ? $_GET['currency']: 'nty';
    $_exchange->set_store_currency_code($currency);
    $ntyAmount = $_exchange->coinmarketcap_exchange($amount);
    $ntyAmount = 1; //TESTING

    $startTime = NULL;
    $endTime = NULL;
    $fromWallet = NULL;

    //$postFormat = $callbackUrl && $shopId && $orderId && $toWallet && $wallet && $ntyAmount;

    $QRText ='{"walletaddress":"'.$toWallet.'","uoid":"'.$orderId.'","amount":"'.$ntyAmount.'"}';
    //echo $QRText;
    $QRTextHex="0x".$_functions->strToHex($QRText);
    $extraData = $QRTextHex;
    $QRTextEncode= urlencode ( $QRText );

    $reqId = $_updatedb->addRequest($shopId, $orderId, $extraData, $callbackUrl, $returnUrl, $amount, $currency, $ntyAmount, 
    $minBlockDistance, $startTime, $endTime, $fromWallet, $toWallet, $wallet ) ;
    //echo $reqId;
    $merchantName = isset($_GET['merchantName']) ? $_GET['merchantName']: $_updatedb->getNameByMid($mid);

    foreach ($_GET as $key => $value) {
        //echo "Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>"; //TEST
    }
    if ($reqId)
        require_once('template/html/request.html'); else require_once('template/html/error.html');
    require_once('template/html/footer.html')
?>
    <script>
        call_ajax(new Date(), <?php echo $reqId; ?>,600,3 );
    </script>
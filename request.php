<?php
    require_once('setting.php');
    require_once('guard.php');
    require_once('template/html/header.html');
?>
    <link rel="stylesheet" href="template/css/request.css"/>  
<?php

    $callbackUrl = isset($_POST['callbackUrl']) ? utf8_decode(urldecode($_POST['callbackUrl'])) : '';
    $returnUrl = isset($_POST['returnUrl']) ? utf8_decode(urldecode($_POST['returnUrl'])) : '';
    $shopId = isset($_POST['shopId']) ? $_POST['shopId'] : '';
    $orderId = isset($_POST['orderId']) ? $_POST['orderId'] : '';
    $minBlockDistance = isset($_POST['minBlockDistance']) ? $_POST['minBlockDistance'] : 1;
    $mid = isset($_POST['mid']) ? $_POST['mid'] : '';
    $wallet = $_updatedb->getWalletByMid($mid);
    $apiKey = $_updatedb->getApiKeyByMid($mid);
    if ($apiKey != $_POST['apiKey']) {require_once('template/error.html'); exit;}
    $toWallet = (isset($_POST['toWallet']) && ($_POST['toWallet'])) ? $_POST['toWallet'] : $wallet;
    $amount = isset($_POST['amount']) ? $_POST['amount']: 0;
    $currency = isset($_POST['currency']) ? $_POST['currency']: 'nty';
    $_exchange->set_store_currency_code($currency);
    $ntyAmount = $_exchange->coinmarketcap_exchange($amount);
    //$ntyAmount = 1; //TESTING

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

    $merchantName = isset($_POST['merchantName']) ? $_POST['merchantName']: $_updatedb->getNameByMid($mid);

    foreach ($_POST as $key => $value) {
        //echo "Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>"; //TEST
    }
    if ($reqId)
        require_once('template/html/request.html'); else require_once('template/html/error.html');
    require_once('template/html/footer.html')
?>
    <script>
        call_ajax(new Date(), <?php echo $reqId; ?>,600,3 );
    </script>
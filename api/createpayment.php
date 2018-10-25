<?php 
    $callbackUrl = isset($params['callbackUrl']) ? utf8_decode(urldecode($params['callbackUrl'])) : '';
    $returnUrl = isset($params['returnUrl']) ? utf8_decode(urldecode($params['returnUrl'])) : '';
    $shopId = isset($params['shopId']) ? $params['shopId'] : '';
    $orderId = isset($params['orderId']) ? $params['orderId'] : '';
    $minBlockDistance = isset($params['minBlockDistance']) ? $params['minBlockDistance'] : 1;
    //$mid = isset($params['mid']) ? $params['mid'] : '';
    $wallet = $_updatedb->getWalletByMid($mid);
    //$apiKey = $_updatedb->getApiKeyByMid($mid);
    //if ($apiKey != $params['apiKey']) {require_once('template/html/error.html');require_once('template/html/footer.html'); exit;}
    $toWallet = (isset($params['toWallet']) && ($params['toWallet'])) ? $params['toWallet'] : $wallet;
    $amount = isset($params['amount']) ? $params['amount']: 0;
    $currency = isset($params['currency']) ? $params['currency']: 'nty';
    $_exchange->set_store_currency_code($currency);
    $ntyAmount = $_exchange->coinmarketcap_exchange($amount);
    $ntyAmount = 1; //TESTING

    $startTime = NULL;
    $endTime = NULL;
    $fromWallet = NULL;

    $QRText ='{"walletaddress":"'.$toWallet.'","uoid":"'.$orderId.'","amount":"'.$ntyAmount.'"}';
    //echo $QRText;
    $QRTextHex="0x".$_functions->strToHex($QRText);
    $extraData = $QRTextHex;
    //$QRTextEncode= urlencode ( $QRText );
    
    $reqId = $_updatedb->getReqId($shopId,$orderId,$wallet);
    //already exist, recomfirm from merchant
    if ($reqId) {
        $output['success'] = true;
        $output['status'] = 'approved';
        $output['reqId'] = $reqId;
        $_updatedb->recomfirm($reqId);
        echo json_response($output, 200);
    } else {
        $reqId = $_updatedb->addRequest($shopId, $orderId, $extraData, $callbackUrl, $returnUrl, $amount, $currency, $ntyAmount, 
        $minBlockDistance, $startTime, $endTime, $fromWallet, $toWallet, $wallet ) ;
        //echo (int)$reqId;

        $output['success'] = (!$reqId) ? false : true; 
        $output['reqId'] = $reqId;

        $output['status'] = (!$reqId) ? 'denied' : 'approved';
        $headerCode = (!$reqId) ? 400 : 200;
        echo json_response($output, $headerCode);
    }
?>
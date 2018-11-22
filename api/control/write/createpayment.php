<?php 
    //CLEAN
    //https://gateway.nexty.io/api/payments/create/&mid=1&apiKey=jrbkzoqtsg0a2rwwuxpvzg38y0iirlq4&orderId=3&amount=10&currency=usd
    function createpayment($data) {
        global $_functions;
        global $_updatedb;
        global $_exchange;
        global $TESTMODE;

        if (!isset($data['wallet']))
        $data['wallet']     = $_updatedb->getWalletByMid($data['mid']);

        $apiKey = $_updatedb->getApiKeyByMid($data['mid']);

        if ($apiKey !== $data['apiKey']) {
            return array(
                'status' => 'failed', 
                'msg' => 'wrong API Key'
            );
        }

        if ($data['currency'] != 'nty') {
            $_exchange->set_store_currency_code($data['currency']);
            $data['ntyAmount'] = $_exchange->coinmarketcap_exchange($data['amount']);
        } else {
            $data['ntyAmount'] = $data['amount'];
        }

        if ($TESTMODE) $data['ntyAmount'] = 1; //TESTING

        //uoid = shopId . 'xxxx' . orderId
        $data['uoid']       = $data['shopId'] . 'xxxx' . $data['orderId'];
        $QRText             = $_functions->getQRText($data['toWallet'], $data['uoid'], $data['ntyAmount']);
        $QRTextHex          = $_functions->getQRHex($QRText);
        $data['extraData']  = $QRTextHex;

        $outputs            = $data;
        $outputs['reqId']   = $_updatedb->createReq($data);
        $reqId              = $outputs['reqId'];
        if ($data['status'] != 'undefined'){
            $outputs['status'] = $outputs['reqId'] ? 'success' : 'failed';
        }
        if (!$outputs['reqId']) return $outputs;

        $reqInfo    = $_updatedb->getReqInfo($outputs['reqId']);
        $reqToken   = $reqInfo['reqToken'];

        $paymentUrl = 'https://gateway.nexty.io/payment.php?reqToken='. $reqToken .'&reqId='.$reqId.'';
        //$arr['reqInfo'] = $reqInfo;
        $outputs['paymentUrl'] = $paymentUrl;
        return $outputs;
    }
?>
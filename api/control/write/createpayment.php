<?php 
    //Requires:
    function createpayment($data) {
    global $_functions;
    global $_updatedb;
    global $_exchange;
    if ($data['currency'] != 'nty') {
        $_exchange->set_store_currency_code($data['currency']);
        $data['ntyAmount'] = $_exchange->coinmarketcap_exchange($data['amount']);
    } else {
        $data['ntyAmount'] = $data['amount'];
    }
    $data['ntyAmount'] = 1; //TESTING
    $data['uoid'] = $data['shopId'] . 'xxxx' . $data['orderId'];
    $QRText = $_functions->getQRText($data['toWallet'], $data['uoid'], $data['ntyAmount']);
    $QRTextHex=$_functions->getQRHex($QRText);

    $data['extraData'] = $QRTextHex;
    echo json_encode($data);
    $reqId = $_updatedb->createReq($data);
    $arr = $data;
    $arr['status'] = $reqId ? 'success' : 'failed';
    if (!$reqId) return $arr;
    $arr['reqId'] = $reqId;
    $reqInfo = $_updatedb->getReqInfo($reqId);
    $arr['reqInfo'] = $reqInfo;
    return $arr;
    }
?>
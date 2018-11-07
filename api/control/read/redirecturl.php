<?php 
    //CLEAN
    function redirecturl($data) {
        global $_functions;
        global $_updatedb;
        global $_exchange;

        $reqId      = $_updatedb->getReqId($data['shopId'],$data['orderId'],$data['wallet']);

        if (!$reqId) {
            return array(
                'status' => 'failed', 
                'msg' => 'request not found' 
            );
        }

        $reqInfo    = $_updatedb->getReqInfo($reqId);
        $reqToken   = $reqInfo['reqToken'];
        $redirectUrl        = 'https://gateway.nexty.io/payment.php?reqToken='. $reqToken .'&reqId='.$reqId.'';
        return array(
            'status' => 'success',
            'url' => $redirectUrl
        );
    }
?>
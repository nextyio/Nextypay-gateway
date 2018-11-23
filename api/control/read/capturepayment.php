<?php 
    //CLEAN
    //http://localhost/api/?path=0/payments/settlements/&mid=1&shopId=0&orderId=2
    function capturepayment($data){
        global $_functions;
        global $_updatedb;
        global $_exchange;

        if (!isset($data['wallet']))
        $data['wallet']     = $_updatedb->getWalletByMid($data['mid']);

        
        $output = $data;
        //CHECK REQTOKEN

        // echo json_encode($data);
        // exit;
        if (!isset($data['reqId']))
        $reqId = $_updatedb->getReqId($data['shopId'],$data['orderId'],$data['wallet']);
 
        $output['status'] = $reqId ? 'success' : 'failed';
        if (!$reqId) return $output;

        $reqInfo = $_updatedb->getReqInfo($reqId);
        echo json_encode($reqInfo);
        exit;
        $output['reqInfo'] = $reqInfo;
        $output['transferedAmount'] = $_updatedb->getTransfered($reqId);
        $output['reqId'] = $reqId;
        return $output;
    }
?>
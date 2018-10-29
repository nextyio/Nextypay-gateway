<?php 
    //http://localhost/api/?path=0/payments/settlements/&mid=1&shopId=0&orderId=2
    //Requires:
    function capturepayment($data){
        global $_functions;
        global $_updatedb;
        global $_exchange;

        $arr = $data;
        
        echo $reqId.'<br>';
        $reqId = $_updatedb->getReqId($data['shopId'],$data['orderId'],$data['wallet']);
        echo json_encode($data);
        echo '<br>'.$reqId.'<br>';
        $arr['status'] = $reqId ? 'success' : 'failed';
        if (!$reqId) return $arr;

        $arr['transferedAmount'] = $_updatedb->getTransfered($reqId);
        $arr['reqId'] = $reqId;
        $reqInfo = $_updatedb->getReqInfo($reqId);
        $arr['reqInfo'] = $reqInfo;
        return $arr;
    }
?>
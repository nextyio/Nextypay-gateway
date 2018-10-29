<?php 
    function getInputs($_path) {
        global $headers;
        $data['apiKey'] = isset($header['X-VTEX-API-AppKey']) ? $headers['X-VTEX-API-AppKey'] : '{{}}';
        $data['mid']    = isset($header['X-VTEX-API-AppToken']) ? $headers['X-VTEX-API-AppToken'] : '{{}}';

        //CUT '{{' and '}}'
        $data['apiKey'] = trim($data['apiKey'], '{');
        $data['apiKey'] = trim($data['apiKey'], '}');
        $data['mid']    = trim($data['mid'], '{');
        $data['mid']    = trim($data['mid'], '}');
        $path = explode('vtex/', $_path)[1];

        //Payment Methods
        if ($path == 'payment-methods') {
            $data['reqCode'] = '0001';
            return $data;
        }

        //Create Payment
        if ($path == 'payments') {
            $data = $_POST;
            $data['reqCode'] = '1001';
            $data['orderId'] = $_POST['paymentId'];
            $data['amount'] = $_POST['value'];
            //$data['currency']
            return $data;
        }

        //Cancel Payment
        if (strpos($path, 'cancellations')) {
            //$path payments/{paymentId}/cancellations
            $data = $_POST;
            $data['reqCode'] = '1002';
            $data['orderId'] = explode('/', $path)[1];
            echo json_encode($data); exit;
            return $data;
        }

        //Capture Payment
        if (strpos($path, 'settlements')) {
            //$path payments/{paymentId}/settlements
            $data = $_POST;
            $data['reqCode'] = '0002';
            $data['orderId'] = explode('/', $path)[1];
            echo json_encode($data); exit;
            return $data;
        }

        //Refund Payment
        if (strpos($path, 'refunds')) {
            //$path payments/{paymentId}/refunds

            $data = $_POST;
            $data['reqCode'] = '1003';
            $data['orderId'] = explode('/', $path)[1];
            //echo json_encode($data); exit;
            return $data;
        }
        $data['reqCode'] = '0000';
        echo json_encode($data); exit;
        return $data;
    }

    function getResponse($data, $outputs) {
        $code = $data['reqCode'];
        $res = $outputs;
        //access denied cause invalid reqCode
        if ($code == '0000') {
            require_once(__DIR__. '/../access/denied.php');
            echo  denied();
            exit;
        }

        //Payment Methods
        if ($code == '0001') {
            echo json_encode($res);
            exit;
        }

        //Create Payment
        if ($code == '1001') {

        }

        //Cancel Payment
        if ($code == '1002') {
            
        }

        //Capture Payment
        if ($code == '0002') {
            
        }

        //Refund Payment
        if ($code == '1003') {
            //unset($res);
            $res['refundId'] = $data['orderId'];
            $res['value'] = 0;
            $res['responses'] = array('msg' => 'we dont support this method');
            echo json_response($res, 200);
            exit;
        }
    }
?>
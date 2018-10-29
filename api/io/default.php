<?php 
    function getInputs($_path) {
        global $headers;
        global $_updatedb;
        global $_functions;

        $paths = explode('/', $_path);
        
        //$path = str_replace("\/","/", $path);
        //echo json_encode($paths);

        //Payment Methods
        if ($paths[1] == 'payment-methods') {
            $data['reqCode'] = '0001';
            return $data;
        }

        //Accept both request methods POST GET
        if (isset($_POST['mid'])) {
            $data = $_POST;
            $data['reqMethod'] = 'POST';
        } else {
            $data = $_GET;
            $data['reqMethod'] = 'GET';
        }

        //all inputs with lowercase
        foreach ($data as $key => $value) {
            $data[$key] = strtolower($value);
        }
        $data['oriGet'] = $_GET;
        $data['oriPost'] = $_POST;
        //echo json_encode($data);

        //Accept both mid and walletAddress params
        if (isset($data['wallet'])) {
            $data['mid'] = $_updatedb->getMidByWallet($data['wallet']);
        } else {
            $data['mid']    = isset($data['mid']) ? $data['mid'] : '';
        }
        //echo $data['mid'];
        //$data['apiKey'] = isset($data['apiKey']) ? $data['apiKey'] : '';
        //$data['shopId']    = isset($data['shopId']) ? $data['shopId'] : 0;
        //$data['orderId']    = isset($data['orderId']) ? $data['orderId'] : '';
        $data['callbackUrl']    = isset($data['callbackUrl']) ? $data['callbackUrl'] : '';
        $data['returnUrl']    = isset($data['returnUrl']) ? $data['returnUrl'] : '';
        $data['minBlockDistance']    = isset($data['minBlockDistance']) ? $data['minBlockDistance'] : 1;
        $data['wallet']    = $_updatedb->getWalletByMid($data['mid']);
        $data['toWallet']    = (isset($data['toWallet']) && ($data['toWallet'])) ? $data['toWallet'] : $data['wallet'];
        $data['amount']    = isset($data['amount']) ? $data['amount'] : 0;
        $data['currency']    = isset($data['currency']) ? $data['currency'] : 'nty';


        $pathLength = count($paths);
        echo json_encode($paths);

        //Payment Methods
        // if ($path == 'payment-methods') {
        //     $data['reqCode'] = '0001';
        //     return $data;
        // }

        //Create Payment
        if (($paths[1] == 'payments') && ($paths[2] == 'create'))  {
            $data['reqCode'] = '1001';
            //$data['currency']
            return $data;
        }

        //Cancel Payment
        if (($paths[1] == 'payments') && ($paths[2] == 'cancel')) {
            //$path payments/{paymentId}/cancellations
            $data['reqCode'] = '1002';
            //$data['orderId'] = explode('/', $path)[1];
            echo json_encode($data); exit;
            return $data;
        }

        //Capture Payment
        if (($paths[1] == 'payments') && ($paths[2] == 'settlements')) {
            //$path payments/{paymentId}/settlements
            $data['reqCode'] = '0002';
            echo json_encode($data);
            //$data['orderId'] = $paths[2];
            return $data;
        }

        if ($paths[1] == 'redirecturl') {
            //$path payments/{paymentId}/settlements
            $data['reqCode'] = '0003';
            echo json_encode($data);
            //$data['orderId'] = $paths[2];
            return $data;
        }

        if ($paths[1] == 'refundurl') {
            //$path payments/{paymentId}/settlements
            $data['reqCode'] = '0004';
            echo json_encode($data);
            //$data['orderId'] = $paths[2];
            return $data;
        }

        //Refund Payment
        if (($paths[1] == 'payments') && ($paths[2] == 'refund')) {
            //$path payments/{paymentId}/refunds

            $data['reqCode'] = '1003';
            $data['orderId'] = explode('/', $path)[1];
            //echo json_encode($data); exit;
            return $data;
        }
        $data['reqCode'] = '0000';
        //echo json_encode($data); exit;
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
            echo json_encode($res);
        }

        //Cancel Payment
        if ($code == '1002') {
            
        }

        //Capture Payment
        if ($code == '0002') {
            echo json_encode($res);
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
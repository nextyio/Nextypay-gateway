<?php 
    //CLEAN
    function getInputs($_path) {
        global $headers;
        global $_updatedb;
        global $_functions;

        $paths = explode('/', $_path);
        //echo json_encode($paths);
        //Payment Methods
        if ($paths[2] == 'payment-methods') {
            $data['reqCode'] = '0001';
            //echo json_encode($data);
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
            $data[$key]     = strtolower($value);
        }

        //save original inputs
        $data['oriGet']     = $_GET;
        $data['oriPost']    = $_POST;

        //Accept both mid and walletAddress params
        if (isset($data['wallet'])) {
            $data['mid']    = $_updatedb->getMidByWallet($data['wallet']);
        } else {
            $data['mid']    = isset($data['mid']) ? $data['mid'] : '';
        }

        //echo $data['mid'];
        $data['minBlockDistance']    = isset($data['minBlockDistance']) ? $data['minBlockDistance'] : 1;
        $data['apiKey']     = isset($data['apiKey']) ? $data['apiKey'] : 0;
        $data['shopId']     = isset($data['shopId']) ? $data['shopId'] : 0;
        $data['orderId']    = isset($data['orderId']) ? $data['orderId'] : 0;
        $data['callbackUrl']= isset($data['callbackUrl']) ? $data['callbackUrl'] : '';
        $data['returnUrl']  = isset($data['returnUrl']) ? $data['returnUrl'] : '';

        if (!isset($data['wallet']))
        $data['wallet']     = $_updatedb->getWalletByMid($data['mid']);

        $data['toWallet']   = (isset($data['toWallet']) && ($data['toWallet'])) ? $data['toWallet'] : $data['wallet'];
        $data['amount']     = isset($data['amount']) ? $data['amount'] : 0;
        $data['currency']   = isset($data['currency']) ? $data['currency'] : 'nty';

        //Create Payment
        if (($paths[2] == 'payments') && ($paths[3] == 'create'))  {
            //https://gateway.nexty.io/api/payments/create
            $data['reqCode'] = '1001';
            return $data;
        }

        //Cancel Payment
        if (($paths[2] == 'payments') && ($paths[3] == 'cancel')) {
            //https://gateway.nexty.io/api/payments/cancel
            $data['reqCode'] = '1002';
            return $data;
        }

        //Capture Payment
        if (($paths[2] == 'payments') && ($paths[3] == 'capture')) {
            //https://gateway.nexty.io/api/payments/capture
            $data['reqCode'] = '0002';
            return $data;
        }

        if  (($paths[2] == 'payments') && ($paths[3] == 'redirecturl')) {
            //https://gateway.nexty.io/api/payments/redirecturl
            $data['reqCode'] = '0003';
            return $data;
        }

        if (($paths[2] == 'payments') && ($paths[3] == 'refund')) {
            //https://gateway.nexty.io/api/payments/refundurl
            $data['reqCode'] = '0004';
            return $data;
        }

        //request not found
        $data['reqCode'] = '0000';
        return $data;
    }

    function getResponse($data, $outputs) {
        $code = $data['reqCode'];
        //access denied cause invalid reqCode
        if ($data['reqCode'] == '0000') {
            echo  denied();
            exit;
        }
        echo json_response_with_headers ($outputs, 200, $headers);
        //echo json_response($outputs, 200);
    }
?>
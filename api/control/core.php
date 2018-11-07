<?php 
    //CLEAN
    /*
        Read mode: 
            api/payment-methods/
            api/payments/capture/
            api/payments/redirecturl
            api/payments/refundurl
            api/payments/exchange

        Write mode:
            api/payments/create
            api/payments/cancel
            api/payments/refund
    */
    function getOutputs($data) {
        $code = $data['reqCode'];
        switch ($code) {
            case '0001' :
                require_once __DIR__.'/read/paymentmethods.php';
                return paymentMethods($data);
            case '0002' :
                require_once __DIR__.'/read/capturepayment.php';
                return capturepayment($data);
            case '0003' :
                require_once __DIR__.'/read/redirecturl.php';
                return redirecturl($data);
            case '0004' :
                require_once __DIR__.'/read/refundurl.php';
                return refundurl($data);
            case '0005' :
                require_once __DIR__.'/read/exchange.php';
                return exchange($data);

            case '1001' :
                require_once __DIR__.'/write/createpayment.php';
                return createpayment($data);
            case '1002' :
                require_once __DIR__.'/write/cancelpayment.php';
                return cancelpayment($data);
            case '1003' :
                require_once __DIR__.'/write/refundpayment.php';
                return refundpayment($data);

            default :
                require_once(__DIR__. '/../access/denied.php');
                return denied();
                //exit;
        }
    }
?>
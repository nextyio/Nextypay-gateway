<?php 
    //Codes:
    //0000  paymentmethods  paymentmethods.php  
    //0001  processpayment  processpayment.php 
    //0002  capturepayment  capturepayment.php
    //0003  redirecturl     redirecturl.php
    //0004  refundurl       refundurl.php
    //0005  exchange        exchange.php
    //
    

    //1000  createpayment   createpayment.php
    //1001  cancelpayment   cancelpayment.php
    //1002  refundpayment   refundpayment.php

    //IO => Inputs
    //$apiKey
    //$mid

    $output = $params;
    $access = true;
    $reqCode = $params['reqCode'] ;
    if ($reqCode != '0000') {
        require_once (__DIR__ .'/../setting.php');
        $_apiKey = $_updatedb->getApiKeyByMid($mid);
        $access = ($_apiKey) && (strtolower($apiKey) == strtolower($_apiKey));
    }
?>
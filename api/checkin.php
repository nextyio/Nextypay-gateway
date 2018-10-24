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

    //ARE YOU KIDDING ME?
    if (!isset($reqCode) && !isset($_GET['reqCode']) && !isset($_POST['reqCode'])) {
        require_once('accessdenied.php');
        exit;
    }

    require_once ('headers.php');
    //$mid = '14';
    //$apiKey = 'wqpvepmzctldgdrcgtlsxnxxhpiaa8ib';

    $reqMethod = isset($_POST['reqCode']) ? 'POST' : 'GET';
    $params = $reqMethod == 'POST' ? $_POST : $_GET;
    $output = $params;
    $access = true;
    if (!isset($reqCode)) $reqCode = $params['reqCode'];
    echo "req Code $reqCode";
    if ($reqCode != '0000') {
        require_once ('../setting.php');
        $_apiKey = $_updatedb->getApiKeyByMid($mid);
        $access = ($_apiKey) && (strtolower($apiKey) == strtolower($_apiKey));
    }

    //test
    // $mid = '14';
    // $apikey = 'wqpvepmzctldgdrcgtlsxnxxhpiaa8ib1';
    // $access = true;
?>
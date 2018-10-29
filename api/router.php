<?php 
    /*Outputs: 
        $reqMethod  = 'GET' or 'POST'
        $io         = 'default', 'vtex'
    */

    /*
    Codes:

        //Read mode

        0001  paymentmethods  paymentmethods.php  
        0002  capturepayment  capturepayment.php
        0003  redirecturl     redirecturl.php
        0004  refundurl       refundurl.php
        0005  exchange        exchange.php
        
        //Write mode

        1001  createpayment   createpayment.php
        1002  cancelpayment   cancelpayment.php
        1003  refundpayment   refundpayment.php
    */
    require_once (__DIR__."/../setting.php");
    require_once (__DIR__."/lib/json_response.php");
    require_once (__DIR__."/lib/headers.php");

    //Nginx htaccess
    //gateway.nexty.io/api/{string} -> gateway.nexty.io/api/?path={string}

    //default   :   gateway.nexty.io/api/?{GET String}
    //           -> gateway.nexty.io/api/?path=0?{GET String}        

    //vtex      :   gateway.nexty.io/api/vtex/?{GET String} -> 
    //           -> gateway.nexty.io/api/?path=0vtex?{GET String}   

    $path = $_GET['path'];
    
    function getIoFromPath($path) {
        $pos = strpos($path, '0/');
        if ($pos === 0) return 'default';
        $pos = strpos($path, 'vtex');
        if ($pos == 1) return 'vtex';
        return 'unknown';
    }

    $io = getIoFromPath($path);
    //echo $io;
    switch ($io) {
        case 'default' :
            require_once(__DIR__. '/io/default.php');
            break;
        case 'vtex' :
            require_once(__DIR__. '/io/vtex.php');
            break;
        default :
            require_once(__DIR__. '/access/denied.php');
            exit;
    }

    $data = getInputs($path);

    require_once(__DIR__. '/control/core.php');    

    $outputs = getOutputs($data);
    //echo json_encode($outputs); exit;
    getResponse($data, $outputs);
?>
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
    require_once ("json_response.php");
    require_once ('checkin.php'); //get $access, $reqCode

    //access denied
    if (!$access) {
        require_once('accessdenied.php');
        exit;
    }

    switch ($reqCode) {
        case '0000':
            require_once ('paymentmethods.php');
            exit;

        case '0001':
            require_once ('processpayment.php');
            exit;

        case '0002':
            require_once ('capturepayment.php');
            exit;

        case '0003':
            require_once ('redirecturl.php');
            exit;

        case '0004':
            require_once ('refundurl.php');
            exit;

        case '0005':
            require_once ('exchange.php');
            exit;
            case '0000':
            require_once ('paymentmethods.php');
            exit;

        case '1000':
            require_once ('createpayment.php');
            exit;

        case '1001':
            require_once ('cancelpayment.php');
            exit;

        case '1002':
            require_once ('refundpayment.php');
            exit;

        default:
            require_once ('accessdenied.php');
            exit;
    }

?>
<?php
    require_once('setting.php');
    require_once('guard.php');
    require_once('template/html/header.html');
?>
    <link rel="stylesheet" href="template/css/payment.css"/>  
<?php
    //gateway.nexty.io/payment/{reqId}/{reqToken}
    //-->
    //gateway.nexty.io/router.php?path=payment/{reqId}/{reqToken}
    $path = $_GET['path'];
    $paths = explode('/', $path);
    
    $reqId = $paths[1];
    $reqToken = $paths[2];

    {
        $data = $_GET;
        $data['reqMethod'] = 'GET';
    }

    $data['reqId'] = $reqId;
    $data['reqInfo'] = $_updatedb->getReqInfo($data['reqId']);
    $reqInfo = $data['reqInfo'];
    $data['reqToken'] = $reqToken;

    if ($reqToken != $reqInfo['reqToken']) {
        require_once('template/html/error.html');
        require_once('template/html/footer.html'); 
        exit;
    }

    $data['wallet'] = $reqInfo['wallet'];
    $data['mid'] = $_updatedb->getMidByWallet($data['wallet']);
    $QRTexHex = $reqInfo['extraData'];
    $QRTexHex = substr($QRTexHex,2); //remove 0x
    $QRText=$_functions->hexToStr($QRTexHex);
    $QRTextEncode= urlencode ( $QRText );

    $reqInfo['merchantName'] = isset($data['merchantName']) ? $data['merchantName']: $_updatedb->getNameByMid($data['mid']);
    //echo json_encode($data);
    if ($data['reqInfo'])
        require_once('template/html/payment.html'); else require_once('template/html/error.html');
    require_once('template/html/footer.html')
?>
    <script>
        call_ajax(new Date(), <?php echo $data['reqId']; ?>,600,3 );
    </script>
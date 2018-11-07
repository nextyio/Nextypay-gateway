<?php
    //CLEAN
    require_once('setting.php');
    require_once('guard.php');
    require_once('template/html/header.html');
?>
    <link rel="stylesheet" href="template/css/payment.css"/>  
<?php
    //example URL:
    //https://gateway.nexty.io//payment.php?reqToken=sozxhc3og9akileyg9faugsfvjm7o1wm&reqId=2

    $data = $_GET;
    $data['reqMethod'] = 'GET';

    $data['reqInfo'] = $_updatedb->getReqInfo($data['reqId']);
    $reqInfo = $data['reqInfo'];

    if ($data['reqToken'] != $reqInfo['reqToken']) {
        require_once('template/html/error.html');
        require_once('template/html/footer.html'); 
        exit;
    }

    $data['wallet']     = $reqInfo['wallet'];
    $data['mid']        = $_updatedb->getMidByWallet($data['wallet']);
    $QRTexHex           = $reqInfo['extraData'];
    $QRTexHex           = substr($QRTexHex,2); //remove 0x
    $QRText             = $_functions->hexToStr($QRTexHex);
    $QRTextEncode       = urlencode ( $QRText );

    $reqInfo['merchantName'] = isset($data['merchantName']) ? $data['merchantName'] : $_updatedb->getNameByMid($data['mid']);
    if ($data['reqInfo']) {
        require_once('template/html/payment.html'); 
    } else {
        require_once('template/html/error.html');
    }

    require_once('template/html/footer.html')
?>
    <script>
        call_ajax(<?php echo $data['reqId']; ?>, '<?php echo $data['reqToken']; ?>', new Date(), 600, 3 );
    </script>
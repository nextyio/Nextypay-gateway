<?php
    //require_once('template/header.html');
?>
<?php
    require_once('setting.php');
    require_once('lib/nextypaypos.php');
    $data = $_POST;
    $_url = $mainnet;

    $npdb = new npdb($DBUSER, $DBPASSWORD, $DBNAME, $DBHOST);

    $_updatedb->set_url($_url);
    $_updatedb->set_connection($npdb);
    $_updatedb->set_includes($_blockchain,$_functions); 

    $proto_fg = isset($_POST['proto_fg']) ? $_POST['proto_fg'] : null;
    $reqCode = $proto_fg;

    $mid = isset($_POST['mid']) ? $_POST['mid'] : null;
    //echo $_POST['mid'];
    //if (!$mid) exit;

    $startTime = null;
    $endTime = null;
    $fromWallet = null;
    $callbackUrl = null;
    $returnUrl = null;

    $pos_no = isset($_POST['pos_no']) ? $_POST['pos_no'] : null;  //shopId
    $shopId = $pos_no;
    
    $trace_no = isset($_POST['trace_no']) ? $_POST['trace_no'] : null; //orderId
    $orderId = $trace_no;
    
    $payment_method = isset($_POST['payment_method']) ? $_POST['payment_method'] : null;
    $paymentMethod = $payment_method;
    
    $currency_cd = isset($_POST['currency_cd']) ? $_POST['currency_cd']: 'nty';
    $currency = $currency_cd;

    $req_amt = isset($_POST['req_amt']) ? $_POST['req_amt'] : null;
    $amount = $req_amt;

    $svc_amt = isset($_POST['svc_amt']) ? $_POST['svc_amt'] : null;
    $svcAmt = $svc_amt;

    $tax = isset($_POST['tax']) ? $_POST['tax'] : null;
    $tax = $tax;

    $exp_time = isset($_POST['exp_time']) ? $_POST['exp_time'] : 0;
    $expTime = $exp_time;

    $rep_msg = isset($_POST['rep_msg']) ? $_POST['rep_msg'] : null;
    $repMsg = $rep_msg;

    $out_trade_no = isset($_POST['out_trade_no']) ? $_POST['out_trade_no'] : null;
    $out_trade_no = $out_trade_no;

    $org_out_trade_no = isset($_POST['org_out_trade_no']) ? $_POST['org_out_trade_no'] : null;
    $org_out_trade_no = $org_out_trade_no;

    $org_app_date = isset($_POST['org_app_date']) ? $_POST['org_app_date'] : null;
    $org_app_date = $org_app_date;

    $org_app_no = isset($_POST['org_app_no']) ? $_POST['org_app_no'] : null;
    $org_app_no = $org_app_no;

    $barcode_no = isset($_POST['barcode_no']) ? $_POST['barcode_no'] : null;
    $barcodeNo = $barcode_no;

    $minBlockDistance = 1;

    $wallet = $_updatedb->getWalletByMid($mid);
    $toWallet = $wallet;
    $_exchange->set_store_currency_code($currency);

    //ADD TAX ???
    $ntyAmount = $_exchange->coinmarketcap_exchange($amount);
    //$ntyAmount = $amount; 

    $reqTime = date("Y-m-d H:i:s");

    $startTime = $reqTime;
    $endTime = date("Y-m-d H:i:s", time() + $expTime);


    $uoid = $shopId.'_'.$orderId; //.$reqTime ???


    //$postFormat = $callbackUrl && $shopId && $orderId && $toWallet && $wallet && $ntyAmount;

    $QRText ='{"walletaddress":"'.$toWallet.'","uoid":"'.$uoid.'","amount":"'.$ntyAmount.'"}';
    //echo $QRText;
    $QRTextHex="0x".$_functions->strToHex($QRText);
    $extraData = $QRTextHex;
    $QRTextEncode= urlencode ( $QRText );
    $src="https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl='.$QRTextEncode.'&choe=UTF-8";
    $data['qr_url'] = $src;
    $data['qr_url_len'] = strlen($src);
    $originalDate = $startTime;

    $data['proto_ver']      = '10';
    $data['res_date'] = date("Ymd", strtotime($originalDate));
    $data['res_time'] = date("His", strtotime($originalDate));
    $data['res_cd'] = 'TEST_RES_CD';
    $data['res_msg'] = 'TEST_RES_MSG';

    $reqId = $_updatedb->addRequest(
                                    $shopId, 
                                    $orderId, 
                                    $extraData, 
                                    $callbackUrl, 
                                    $returnUrl, 
                                    $amount,
                                    $currency,
                                    $ntyAmount, 
                                    $minBlockDistance, 
                                    $startTime, 
                                    $endTime, 
                                    $fromWallet, 
                                    $toWallet, 
                                    $wallet ) ;

    //$data['out_trade_no'] = $reqId;

    $merchantName = "test Merchant Name";

    /////////////////////////////5010//////////////////////////////////////
    // $response.= autofill($data['res_cd'], 4);
    // $response.= autofill($data['res_msg'], 50);
    // $response.= autofill($data['status_cd'], 4);
    // $response.= autofill($data['status_msg'], 50);
    // $response.= autofill($data['iss_cd'], 4);
    // $response.= autofill($data['iss_name'], 20);
    // $response.= autofill($data['aco_cd'], 4);
    // $response.= autofill($data['aco_nm'], 20);
    // $response.= autofill($data['app_date'], 8);
    // $response.= autofill($data['app_time'], 6);
    // $response.= autofill($data['app_no'], 10);
    // $response.= autofill($data['res_time'], 6);
    $reqId = $data['out_trade_no'];
    
    $status = $_updatedb->getReqStatus($reqId);
    switch ($status) {
        case false:
            $statusCode = 0;
            break;
        case "Pending":
            $statusCode = 1;
            break;
        case "Paid":
            $statusCode = 2;
            break;
        default:
            $statusCode = 3;
    }
    $data['res_cd'] = 'TEST_RES_CD';
    $data['res_msg'] = 'TEST_RES_MSG';
    $statusMsg = $status ? $status : 'request_not_found';

    $originalDate = $startTime;

    $data['proto_ver']      = '10';
    $data['status_cd'] = $statusCode;
    $data['status_msg'] = $statusMsg;
    $data['iss_cd'] = '0';
    $data['iss_name'] = 'test_iss_name';
    $data['aco_cd'] = '0';
    $data['aco_nm'] = 'test_aco_nm';
    $data['app_date'] = date("Ymd", strtotime($originalDate));
    $data['app_time'] = date("His", strtotime($originalDate));
    $data['app_no'] = '0';
    $data['res_time'] = date("His", strtotime($originalDate));

/*
    foreach ($_POST as $key => $value) {
        //echo "Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>"; //TEST
    }
    if (isset($_POST['mid']))
        require_once('template/request.html'); else require_once('template/error.html');
    require_once('template/footer.html');
*/
/*
echo "<table>";
echo "<tr>";
echo "<td>";
echo 'PARAM';
echo "</td>";
echo "<td>";
echo "Value";
echo "</td>";
echo "</tr>";

    foreach ($_POST as $key => $value) {
        echo "<tr>";
        echo "<td>";
        echo $key;
        echo "</td>";
        echo "<td>";
        echo $value;
        echo "</td>";
        echo "</tr>";
    }
*/

//echo "</table>";
switch ($reqCode) {
    case "1010":
        $response = ReqAPV1_1010($data, $_updatedb);
        break;
    case "5010":
        $response = ReqAPV1_5010($data, $_updatedb);
        break;
    default:
        $response = "invalid request";
}
echo $response;
//echo strlen($response);
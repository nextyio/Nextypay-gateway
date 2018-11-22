<?php 

function testCallback($url) {

$fields = array(
        'status'=> 'approved'
);

$postvars='';
$sep='';
foreach($fields as $key=>$value)
{
        $postvars.= $sep.urlencode($key).'='.urlencode($value);
        $sep='&';
}

$ch = curl_init();

curl_setopt($ch,CURLOPT_URL,$url);
curl_setopt($ch,CURLOPT_POST,count($fields));
curl_setopt($ch,CURLOPT_POSTFIELDS,$postvars);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);

$result = curl_exec($ch);

curl_close($ch);

//echo $result;
}
    //NOT CLEANED
    //echo 'entered Vtex';
    //entered Vtex["","api","vtex","payment-methods"]
    function getInputs($_path) {
        global $headers;

        $paths = explode('/', $_path);
        // echo json_encode($paths);
        // exit;
        //Payment Methods
        if ($paths[3] == 'payment-methods') {
            $data['reqCode'] = '0001';
            return $data;
        }
        // $outputs['originPost'] = $_POST;
        // $outputs['originGet'] = $_GET;
        // $data1 = json_decode(file_get_contents('php://input'), true);
        // $outputs['originReq'] = $data1;
        // $outputs['headers'] = $headers;
        // $outputs['path'] = $path;
        // echo json_encode($outputs); exit;
        $data = json_decode(file_get_contents('php://input'), true);
        $data['headers']= $headers;
        $midText = 'X-Vtex-Api-Apptoken';
        $apiKeyText = 'X-Vtex-Api-Appkey';
        $data['apiKey'] = isset($headers[$apiKeyText]) ? $headers[$apiKeyText] : '{{}}';
        $data['mid']    = isset($headers[$midText]) ? $headers[$midText] : '{{}}';

        //CUT '{{' and '}}'
        $data['apiKey'] = trim($data['apiKey'], '{');
        $data['apiKey'] = trim($data['apiKey'], '}');
        $data['mid']    = trim($data['mid'], '{');
        $data['mid']    = trim($data['mid'], '}');
        $path = explode('vtex/', $_path)[1];
        $data['shopId'] = 0;
        $data['minBlockDistance'] = 1;
        //Payment Methods
        if ($paths[3] == 'payment-methods') {
            $data['reqCode'] = '0001';
            return $data;
        }

        //Create Payment
        // if ($path == 'payments') {
        //     $paymentMethodsList = array('bitcoin', 'nextypay');
        //     $paymentmethod = strtolower($data['paymentMethod']);

        //     if (!in_array($paymentmethod, $paymentMethodsList)) {

        //         $data['reqCode'] = '0000';
        //         denied();
        //         return $data;
        //     }

        //     $data['reqCode'] = '1001';
        //     $data['orderId'] = $data['paymentId'];
        //     $data['amount'] = $data['value'];
        //     //$data['currency']
        //     return $data;
        // }

        //Cancel Payment
        if ($paths[5] == 'cancellations') {
            //$path payments/{paymentId}/cancellations
            $data['reqCode'] = '1002';
            $data['paymentId'] = $paths[4];
            $data['orderId'] = $paths[4];
            return $data;
        }

        //Capture Payment
        if ($paths[5] == 'settlements') {
            $data['reqCode'] = '0002';
            $data['paymentId'] = $paths[4];
            $data['orderId'] = $paths[4];
            return $data;
        }

        //Refund Payment
        if ($paths[5] == 'refunds') {
            //$path payments/{paymentId}/refunds

            // $data['reqCode'] = '1003';
            // $data['paymentId'] = $paths[4];
            // $data['orderId'] = $paths[4];
            // $data['reqId'] = $data['settleId'];
            // //echo json_encode($data); exit;
            // return $data;
            $res = $data;
            $res['refundId'] = $data['reqId'];
            $res['value'] = 0;
            $res['responses'] = array('msg' => 'we dont support this method');
            echo json_response_with_headers($res, 200, $headers);
            exit;
        }

        if ($paths[3] == 'payments') {
            $paymentMethodsList = array('bitcoin', 'nty');
            $paymentmethod = strtolower($data['paymentMethod']);

            if (!in_array($paymentmethod, $paymentMethodsList)) {

                $data['reqCode'] = '0000';
                denied();
                return $data;
            }

            $data['reqCode'] = '1001';
            $data['orderId'] = $data['paymentId'];
            $data['amount'] = $data['value'];
            //$data['currency']
            return $data;
        }

        $data['reqCode'] = '0000';
        echo json_encode($data); exit;
        return $data;
    }

    function getResponse($data, $outputs) {
        global $headers;
        $code = $data['reqCode'];
        //$res = $outputs;
        //access denied cause invalid reqCode
        if ($code == '0000') {
            //require_once(__DIR__. '/../access/denied.php');
            //echo  json_response(denied());
            exit;
        }

        //Payment Methods
        if ($code == '0001') {
            //if ($res['status'] == 'success') $res['status'] = 'approved';
            echo json_encode($outputs);
            exit;
        }

        //Create Payment
        if ($code == '1001') {
            $res['status'] = 'approved';
            $res['paymentId'] = $outputs['paymentId'];
            $res['authorizationId'] = $outputs['paymentId'];
            $res['bankIssueInvoiceUrl'] = null;
            $res['nsu'] = null;
            $res['tid'] = $outputs['reqId'];
            $res['acquirer'] = $outputs['merchantName'];
            $res['redirectUrl'] = $outputs['paymentUrl'];
            $res['code'] = null;
            $res['message'] = null;
            //$res['delayToAutoSettle'] = 120;
            //$res['delayToCancel'] = 600;

            echo json_response_with_headers ($res, 200, $headers);

//TEST REDIRECT
//testCallback($outputs['callbackUrl']);
//echo $outputs['callbackUrl'];

            exit;
        }

        //Cancel Payment
        if ($code == '1002') {
            $res['requestId'] = $outputs['requestId'];
            $res['paymentId'] = $outputs['paymentId'];
            $res['cancellationId'] = $outputs['paymentId'];
            $res['status'] = 'cancelled';
            $res['code'] = null;
            $res['message'] = "Sucessfully Cancelled";
            echo json_response_with_headers ($res, 200, $headers);
            exit;
        }

        //Capture Payment
        if ($code == '0002') {
            $res['settleId'] = $outputs['reqId'];
            $res['value'] = $outputs['transferedAmount'];
            $res['paymentId'] = $outputs['paymentId'];
            $res['tid'] = $outputs['reqId'];
            $res['responses'] = null;
            echo json_response_with_headers ($res, 200, $headers);
            exit;
        }

        //Refund Payment
        if ($code == '1003') {
            //echo json_encode($outputs);exit;
            //unset($res);
            $res['refundId'] = $data['reqId'];
            $res['value'] = 0;
            $res['responses'] = array('msg' => 'we dont support this method');
            echo json_response_with_headers($res, 200, $headers);
            exit;
        }
    }
?>
<?php 
    require_once __DIR__ . '/../../api/json_response.php';
    $request = $_GET['request'];
    $arr = explode("/vtex/", $request);
    $request = $arr[1];
    //https://{providerBaseUrl}/payment-methods
    //https://{providerBaseUrl}/payments
    //https://{providerBaseUrl}/payments/{paymentId}/cancellations
    //https://{providerBaseUrl}/payments/{paymentId}/settlements
    //https://{providerBaseUrl}/payments/{paymentId}/refunds

    function getRequestHeaders() {
        $headers = array();
        foreach($_SERVER as $key => $value) {
            if (substr($key, 0, 5) <> 'HTTP_') {
                continue;
            }
            $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
            $headers[$header] = $value;
        }
        return $headers;
    }
    $mid = '1';
    $pKey = 'fdsgfds';
    $headers = getRequestHeaders();
    $params['reqCode'] = 'xxxx';
    if ($request == 'payment-methods') {        $arr = array("paymentMethods" => array("Diners", "Elo", "Nextypay" ));
        echo json_response($arr, 200);exit;};
    if ($request == 'payments') {$params = $_POST; $params['reqCode'] = '1000';};
    require_once (__DIR__ . '/../../api/filter.php');
    
?>
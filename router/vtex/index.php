<?php 
// header('Content-Type: application/json');
// header('Status: 200 OK');
foreach ($_GET as $key => $value) {
    echo "Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>"; //TEST
}

    require_once ('../../lib/json_response.php');
    //require_once ('request.php');

    foreach ($_GET as $key => $value) {
        echo "Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>"; //TEST
    }
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
    
    $headers = getRequestHeaders();

    if ($request == 'payment-methods') {$reqCode = '0000'};
    if ($request == 'payments') {$reqCode = '1000'};
    //require_once ('../../api/filter.php');
    */
?>
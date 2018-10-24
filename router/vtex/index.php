<?php 
// header('Content-Type: application/json');
// header('Status: 200 OK');
require_once ('json_response.php');
foreach ($_GET as $key => $value) {
    //echo "Field ".htmlspecialchars($key)." is ".htmlspecialchars($value)."<br>"; //TEST
}
$request = $_GET['request'];
$arr = explode("/vtex/", $request);
$request = $arr[1];
function getPaymentMethods() {
    $arr = array("paymentMethods" => array("Diners", "Elo", "Nextypay" ));
    echo json_response($arr, 200);
}
if ($request == 'payment-methods') {getPaymentMethods(); exit;}
?>
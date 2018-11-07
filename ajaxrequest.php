<?php 

error_reporting(E_ERROR | E_PARSE);
require_once('setting.php');
require_once('guard.php');
require_once('lib/npdb.php');
require_once('lib/json_response.php');

$reqId      = $_POST['reqId'];
$reqToken   = $_POST['reqToken'];
$reqInfo    = $_updatedb->getReqInfo($reqId);
if ((!$reqToken) || ($reqToken != $reqInfo['reqToken'])) {
    $outputs['status'] = 'failed';
}

$outputs['status'] = 'success';
$outputs['data'] = $reqInfo;

echo json_response($outputs, 200);

?>
<?php 

error_reporting(E_ERROR | E_PARSE);
require_once('setting.php');
require_once('guard.php');
require_once('lib/npdb.php');

$reqId = $_POST['reqId'];

$returnUrl= $_updatedb->getReturnUrl($reqId);
if ($returnUrl) echo $returnUrl;

?>
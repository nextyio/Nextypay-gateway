<?php 

error_reporting(E_ERROR | E_PARSE);
require_once('lib/npdb.php');

require_once('lib/nextypayblockchain.php');
require_once('lib/nextypayexchange.php');
require_once('lib/nextypayfunctions.php');
require_once('lib/nextypayupdatedb.php');
$reqId = $_POST['reqId'];

$_db_prefix='';
$_updatedb=new Nextypayupdatedb;
$_blockchain= new Nextypayblockchain;
$_functions= new Nextypayfunctions;

$testnet = "http://125.212.250.61:11111";
$local = "http://127.0.0.1:8545";
$mainnet = 'http://13.228.68.50:8545';

$_url = $mainnet;

$DBUSER = "root";
$DBPASSWORD = "root123";
$DBNAME = "nextypay";
$DBHOST = "localhost";
$npdb = new npdb($DBUSER, $DBPASSWORD, $DBNAME, $DBHOST);

$_updatedb->set_url($_url);
$_updatedb->set_connection($npdb);
$_updatedb->set_includes($_blockchain,$_functions);
$returnUrl= $_updatedb->getReturnUrl($reqId);
if ($returnUrl) echo $returnUrl;

?>
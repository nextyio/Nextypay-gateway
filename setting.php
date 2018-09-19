<?php
error_reporting(E_ERROR | E_PARSE);

$gatewayWallet = '0x6f53c8502bb884775e422c7c34be681554cee2ba';
require_once('lib/npdb.php');
require_once('lib/nextypayblockchain.php');
require_once('lib/nextypayexchange.php');
require_once('lib/nextypayfunctions.php');
require_once('lib/nextypayupdatedb.php');
require_once('lib/helper.php');
//require_once('request.html');


$_db_prefix='';
$_updatedb=new Nextypayupdatedb;
$_blockchain= new Nextypayblockchain;
$_exchange= new Nextypayexchange;
$_functions= new Nextypayfunctions;

$testnet = "http://125.212.250.61:11111";
$local = "http://127.0.0.1:8545";
$mainnet = 'http://13.228.68.50:8545';
$DBUSER = "root";
$DBPASSWORD = "Root123!";
$DBNAME = "nextypay";
$DBHOST = "localhost";
?>
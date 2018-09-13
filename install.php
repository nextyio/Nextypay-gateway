<?php
require_once('lib/npdb.php');

require_once('lib/nextypayblockchain.php');
require_once('lib/nextypayexchange.php');
require_once('lib/nextypayfunctions.php');
require_once('lib/nextypayupdatedb.php');
require_once('lib/nextypaysetup.php');

$DBUSER = "root";
$DBPASSWORD = "Root123!";
$DBNAME = "nextypay";
$DBHOST = "localhost";
$npdb = new npdb($DBUSER, $DBPASSWORD, $DBNAME, $DBHOST);
$setup = new Nextypaysetup();
$setup->uninstall();
$npdb = new npdb($DBUSER, $DBPASSWORD, $DBNAME, $DBHOST);
$setup->install();

$_db_prefix='';
$_updatedb=new Nextypayupdatedb;
$_blockchain= new Nextypayblockchain;
$_functions= new Nextypayfunctions;

$testnet = "http://125.212.250.61:11111";
$local = "http://127.0.0.1:8545";
$mainnet = 'http://13.228.68.50:8545';

$_url = $mainnet;

$_updatedb->set_url($_url);
$_updatedb->set_connection($npdb);
$_updatedb->set_includes($_blockchain,$_functions);
//$_updatedb->init_blocks_table_db(3780307);
//$_updatedb->init_blocks_table_db(7158740);
//$_updatedb->init_blocks_table_db(7168740);
//$_updatedb->init_blocks_table_db(7164330);
$_updatedb->init_blocks_table_db(0);
//TESTING

//Add merchant

$wallet = "0xBF878162F34A11c832339ADB0CcCdDb1b091C1E5";
$name = "test merchant name";
$url = "test merchant url";
$tokenKey = "test Token";
$_updatedb->addMerchant($wallet, $name, $url, $tokenKey);

//Add request

$extraData = "0x0aaa";
$callbackUrl = "google.com";
$returnUrl = "google.com.vn";
$ntyAmount = 5e18;
$shopId = 1;
$orderId = 2;
$amount = 1;
$minBlockDistance = 10;
$currency = "NTY";
$startTime = "";
$endTime = "";
$status = "Pending";
$fromWallet = "";
$toWallet = "0xBF878162F34A11c832339ADB0CcCdDb1b091C1E5";
$wallet = "0xBF878162F34A11c832339ADB0CcCdDb1b091C1E5";
$_updatedb->addRequest($shopId, $orderId, $extraData, $callbackUrl, $returnUrl, $ntyAmount, $minBlockDistance, $startTime, $endTime, $fromWallet, $toWallet, $wallet );

//3778554
?>
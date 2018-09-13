<?php

require_once('lib/npdb.php');

require_once('lib/nextypayblockchain.php');
require_once('lib/nextypayexchange.php');
require_once('lib/nextypayfunctions.php');
require_once('lib/nextypayupdatedb.php');


$_db_prefix='';
$_updatedb=new Nextypayupdatedb;
$_blockchain= new Nextypayblockchain;
$_functions= new Nextypayfunctions;

$testnet = "http://125.212.250.61:11111";
$local = "http://127.0.0.1:8545";
$mainnet = 'http://13.228.68.50:8545';

$_url = $mainnet;

$DBUSER = "root";
$DBPASSWORD = "Root123!";
$DBNAME = "nextypay";
$DBHOST = "localhost";
$npdb = new npdb($DBUSER, $DBPASSWORD, $DBNAME, $DBHOST);

$_updatedb->set_url($_url);
$_updatedb->set_connection($npdb);
$_updatedb->set_includes($_blockchain,$_functions);
//$_updatedb->init_blocks_table_db();
$_updatedb->updatedb();
echo $_updatedb->getTransfered(0);
/*
             $max_block_number = $_blockchain->get_max_block_number($_url);
             $hex_max_block_number="0x".strval(dechex($max_block_number));
             $block=$_blockchain->get_block_by_number($_url,$hex_max_block_number);
             $block_content=$block['result'];
             $_updatedb->insert_block_db($block_content);
*/
//$_updatedb->updatedb();

 // Always die in functions echoing ajax content


?>
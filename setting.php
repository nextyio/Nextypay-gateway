<?php
error_reporting(E_ERROR | E_PARSE);
//ini_set('display_errors',1);
//error_reporting(E_ALL);
//merchants register validator
$gatewayWallet = '0x6f53c8502bb884775e422c7c34be681554cee2ba';

require_once('lib/npdb.php');
$DBUSER     = "root";
$DBPASSWORD = "Root123!";
$DBNAME     = "nextypay";
$DBHOST     = "localhost";

/* Attempt to connect to MySQL database */
$link = mysqli_connect($DBHOST , $DBUSER, $DBPASSWORD, $DBNAME);
 
// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
$npdb       = new npdb($DBUSER, $DBPASSWORD, $DBNAME, $DBHOST);

require_once('lib/nextypayblockchain.php');
require_once('lib/nextypayexchange.php');
require_once('lib/nextypayfunctions.php');
require_once('lib/nextypayupdatedb.php');
require_once('lib/helper.php');

$_db_prefix='';
$_updatedb=new Nextypayupdatedb;
$_blockchain= new Nextypayblockchain;
$_exchange= new Nextypayexchange;
$_functions= new Nextypayfunctions;

$testnet    = "http://125.212.250.61:11111";
$local      = "http://127.0.0.1:8545";
$mainnet    = "http://13.228.68.50:8545";

$_url = $mainnet;

$_updatedb->set_url($_url);
$_updatedb->set_connection($npdb);
$_updatedb->set_includes($_blockchain,$_functions);
$_updatedb->set_gatewayWallet($gatewayWallet);

function render($page) {
    $htmlFolder = 'template/html/';
    //echo $htmlFolder.$page;
    require_once($htmlFolder . 'header.html');
    require_once($htmlFolder . $page);
    require_once($htmlFolder . 'footer.html');
}
?>
<?php
//exit; //uncomment for production version or move to control folder
require_once('setting.php');
require_once('guard.php');
require_once('lib/nextypaysetup.php');

$npdb   = new npdb($DBUSER, $DBPASSWORD, $DBNAME, $DBHOST);
$setup  = new Nextypaysetup();
$setup->uninstall();
$npdb   = new npdb($DBUSER, $DBPASSWORD, $DBNAME, $DBHOST);
$setup->install();

$_db_prefix = '';
$_updatedb  = new Nextypayupdatedb;
$_blockchain= new Nextypayblockchain;
$_functions = new Nextypayfunctions;

$_url = $mainnet;

$_updatedb->set_url($_url);
$_updatedb->set_connection($npdb);
$_updatedb->set_includes($_blockchain,$_functions);
$_updatedb->init_blocks_table_db(0);
echo $_updatedb->getMaxMid();

?>
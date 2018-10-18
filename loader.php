<?php
//move to control folder, access local only after testing phase
require_once('setting.php');

$_url = $mainnet;

$npdb = new npdb($DBUSER, $DBPASSWORD, $DBNAME, $DBHOST);
$_updatedb->set_url($_url);
$_updatedb->set_connection($npdb);
$_updatedb->set_includes($_blockchain,$_functions);
$_updatedb->set_gatewayWallet($gatewayWallet);
$_updatedb->updatedb();
?>
<?php
    /*
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Credentials: true ");
    header("Access-Control-Allow-Methods: OPTIONS, GET, POST");
    header("Access-Control-Allow-Headers: Content-Type, Depth, User-Agent, X-File-Size, X-Requested-With, If-Modified-Since, X-File-Name, Cache-Control");
    */
    require_once('setting.php');
    require_once('guard.php');
    require_once('killSession.php');
    require_once('template/html/header.html');
    require_once('template/html/register.html'); 
    require_once('template/html/footer.html');
?>
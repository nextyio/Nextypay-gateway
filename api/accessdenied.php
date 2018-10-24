<?php
    require_once (__DIR__ . "/json_response.php");
    echo json_response(['status' => 'access denied'], 400);
    exit;
?>
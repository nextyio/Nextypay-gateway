<?php
    require_once ("json_response.php");
    echo json_response(['status' => 'access denied'], 400);
    exit;
?>
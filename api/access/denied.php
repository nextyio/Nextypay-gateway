<?php
    function denied() {
        echo json_encode(array("status" => "denied"));
        exit;
    }
?>
<?php
    function denied() {
        return json_response(['status' => 'access denied'], 400);
    }
?>
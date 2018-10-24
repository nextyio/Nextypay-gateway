<?php 
    function paymentMethods() {
        $arr = array("paymentMethods" => array("Diners", "Elo", "Nextypay" ));
        echo json_response($arr, 200);
    }

    paymentMethods();
?>
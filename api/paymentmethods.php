<?php 
    function paymentMethods() {
        $arr = array("paymentMethods" => array("Diners", "Nextypay" ));
        echo json_response($arr, 200);
    }

    paymentMethods();
?>
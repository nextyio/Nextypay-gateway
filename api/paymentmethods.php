<?php 
    function paymentMethods() {
        $arr = array("paymentMethods" => array("Diners", "Elo", "Nextypay" ));
        echo json_encode($arr);
    }

    paymentMethods();
?>
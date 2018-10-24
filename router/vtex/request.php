<?php 
    function paymentMethods() {
        $arr = array("paymentMethods" => array("Diners", "Elo", "Nextypay" ));
        echo json_response($arr, 200);
    }

    function payments( $data, $headers) {
        /*
        //callbackUrl 
        //returnUrl
        //value
        //currency
        //paymentId
        //merchantName
        //paymentMethod

        {
            "paymentId": "F5C1A4E20D3B4E07B7E871F5B5BC9F91", // orderId
            "status": "approved",
            "authorizationId": "F5C1A4E20D3B4E07B7E871F5B5BC9F91", (optional)
            "bankIssueInvoiceUrl": null, $explorer transaction hash
            "nsu": "NsuC123", (optional)
            "tid": "Tid1578324421", =reqID
            "acquirer": "Cielo",
            "redirectUrl": null, =requestUrl with GET
            "code": null,  (optional)
            "message": null, (optional)
            "delayToAutoSettle": 120, 5seconds (optional)
            "delayToCancel": 600 (optional)
          }
          */

        $key = $headers['X-VTEX-API-AppKey'];
        $token = $headers['X-VTEX-API-AppToken'];

        $key = substr($key, 2);
        $token = substr($token, 0, -2);
    }
?>
<?php 
function autofill($str, $fixedLength) {
    if ($fixedLength == 0) return '<br>' . $str;
    $l = strlen($str);
    $result = $str;
    for ($i = 0; $i < $fixedLength - $l; $i++) $result = '0' . $result;
    return $result;
}

//request qr url
function ReqAPV1_1010($data) {
/*
Function name ReqAPV1_4010 
Declaration section int ReqAPV1_4010 ( 
    char * mid, 
    char * trace_no, 
    char * pos_no, 
    char * payment_method, 
    char * currency_cd, 
    long req_amt, 
    long svc_amt, 
    long tax, 
    char * org_out_trade_no, 
    char * org_app_date, 
    char * org_app_no, 
    char * rep_msg)
Response
1011 (4) + mid (12) + proto ver (2) + trace no (10) + pos no (10) +
payment method (4) + res cd (4) + res msg (50) + out trade no (20)
+ res date (8) + res time (6) + qr url len (4) + qr url
*/
    $response  = autofill('1011', 4);
    $response .= autofill($data['mid'], 12);
    $response .= autofill($data['proto_ver'], 2);
    $response .= autofill($data['trace_no'], 10);
    $response .= autofill($data['pos_no'], 10);
    $response .= autofill($data['payment_method'], 4);
    $response .= autofill($data['res_cd'], 4);
    $response .= autofill($data['res_msg'], 50);
    $response .= autofill($data['out_trade_no'], 20);
    $response .= autofill($data['res_date'], 8);
    $response .= autofill($data['res_time'], 6);
    $response .= autofill($data['qr_url_len'], 4);
    $response .= autofill($data['qr_url'], 0);

    return $response;
}

//request view trans status
function ReqAPV1_5010($data) {
    /*
    Function name ReqAPV1_5010 
    Declaration section int ReqAPV1_5010 ( 
        char * mid, 
        char * trace_no, 
        char * pos_no, char * 
        payment_method, char * 
        currency_cd, long req_amt, 
        long svc_amt, 
        long tax, 
        char * out_trade_no, 
        char * rep_msg)
    Response
        5011 (4) + mid (12) + proto ver (2) + trace no (10) + pos no (10) +
        payment method (4) + svc amt (10) + tax (10) + res cd (4) + res msg
        (50) + status cd (4) + status msg (50) + iss cd (4) + iss name (20)
        + aco cd (4) + aco nm (20) + app date (8) + app time (6) + app no
        (10) + res time (6) + 11 cells free
    */

    $response  = autofill('5011', 4);
    $response .= autofill($data['mid'], 12);
    $response .= autofill($data['proto_ver'], 2);
    $response .= autofill($data['trace_no'], 10);
    $response .= autofill($data['pos_no'], 10);
    $response .= autofill($data['payment_method'], 4);
    $response .= autofill($data['svc_amt'], 10);
    $response .= autofill($data['tax'], 10);
    $response .= autofill($data['res_cd'], 4);
    $response .= autofill($data['res_msg'], 50);
    $response .= autofill($data['status_cd'], 4);
    $response .= autofill($data['status_msg'], 50);
    $response .= autofill($data['iss_cd'], 4);
    $response .= autofill($data['iss_name'], 20);
    $response .= autofill($data['aco_cd'], 4);
    $response .= autofill($data['aco_nm'], 20);
    $response .= autofill($data['app_date'], 8);
    $response .= autofill($data['app_time'], 6);
    $response .= autofill($data['app_no'], 10);
    $response .= autofill($data['res_time'], 6);
    
    return $response;
}
?>
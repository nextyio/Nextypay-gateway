
<?php
/**
 * Redirect with POST data.
 *
 * @param string $url URL.
 * @param array $post_data POST data. Example: array('foo' => 'var', 'id' => 123)
 * @param array $headers Optional. Extra headers to send.
 */
function redirect_post($url, array $data, array $headers = null) {
    $params = array(
        'http' => array(
            'method' => 'POST',
            'content' => http_build_query($data)
        )
    );
    if (!is_null($headers)) {
        $params['http']['header'] = '';
        foreach ($headers as $k => $v) {
            $params['http']['header'] .= "$k: $v\n";
        }
    }
    $ctx = stream_context_create($params);
    $fp = @fopen($url, 'rb', false, $ctx);
    if ($fp) {
        echo @stream_get_contents($fp);
        die();
    } else {
        // Error
        throw new Exception("Error loading '$url', $php_errormsg");
    }
}

$url = 'posRequest.php';

$data['mid'] = 1;
$data['pos_no'] = 1;
$data['trace_no'] = 1;
$data['payment_method'] = 1;
$data['currency_cd'] = 1;
$data['req_amt'] = 1;
$data['svc_amt'] = 1;
$data['tax'] = 1;
$data['exp_time'] = 1;
$data['rep_msg'] = 1;

$data['out_trade_no'] = 1;
$data['org_out_trade_no'] = 1;
$data['org_app_date'] = 1;
$data['org_app_no'] = 1;
$data['barcode_no'] = 1;
?>
<form id="virtualPos" action="posRequest.php" target="_blank" method="post">
<?php
    foreach ($data as $a => $b) {
        echo '<input type="hidden" name="'.htmlentities($a).'" value="'.htmlentities($b).'">';
    }
?>
</form>
<script type="text/javascript">
    document.getElementById('virtualPos').submit();
</script>

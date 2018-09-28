
<?php

$url = 'posRequest.php';

$data['mid'] = 1;
$data['proto_fg'] = 1010;
$data['pos_no'] = 1;
$data['trace_no'] = rand(1,1000);
$data['payment_method'] = 1;
$data['currency_cd'] = 'usd';
$data['req_amt'] = 1;
$data['svc_amt'] = 1;
$data['tax'] = 1;
$data['exp_time'] = 30;
$data['rep_msg'] = 1;

$data['out_trade_no'] = 1;
$data['org_out_trade_no'] = 1;
$data['org_app_date'] = 1;
$data['org_app_no'] = 1;
$data['barcode_no'] = 1;
?>
<form id="virtualPos" action="<?php echo $url; ?>" target="_blank" method="post">
<?php
    foreach ($data as $a => $b) {
        echo '<input type="hidden" name="'.htmlentities($a).'" value="'.htmlentities($b).'">';
    }
?>
</form>
<script type="text/javascript">
    document.getElementById('virtualPos').submit();
</script>

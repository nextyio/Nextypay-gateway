
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
<table>
<?php
    echo "<tr>";
    echo "<td>";
    echo 'PARAM';
    echo "</td>";
    echo "<td>";
    echo "Value";
    echo "</td>";
    echo "</tr>";

        foreach ($data as $key => $value) {
            echo "<tr>";
            echo "<td>";
            echo $key;
            echo "</td>";
            echo "<td>";
            echo '<input name="'.htmlentities($key).'" value="'.htmlentities($value).'">';
            echo "</td>";
            echo "</tr>";
        }
?>
</table>
<button name='submit' type = 'submit'>submit</button>
</form>
<script type="text/javascript">
    //document.getElementById('virtualPos').submit();
</script>

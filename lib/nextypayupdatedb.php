<?php
class Nextypayupdatedb{
    public static $instance;
    //inputs list

    public $_url;
    public $_functions;
    public $_blockchain;

    public $_connection;

    public $_db_prefix;
    public $_store_currency;
    public $_admin_wallet_address;
    public $_order_id_prefix;
    public $_min_blocks_saved_db;
    public $_max_blocks_saved_db;
    public $_blocks_loaded_each_request;
    public $_gatewayWallet;

    /**
        * @param  object  $registry  Registry Object
    */

    public static function get_instance($registry) {
        if (is_null(static::$instance)) {
        static::$instance   = new static($registry);
        }
        return static::$instance;
    }

    public function __construct() {
        $this->setting();
    }

    public function set_url($url){
        $this->_url=$url;
    }

    public function set_gatewayWallet($wallet) {
        $this->_gatewayWallet = $wallet;
    }

    public function set_connection($connection){
        $this->_connection=$connection;
    }

    public function set_includes($obj_blockchain,$obj_functions){
        $this->_functions=$obj_functions;
        $this->_blockchain=$obj_blockchain;
    }

    public function setting(){
        $this->_blocks_loaded_each_request = 30;
    }

    ////////////////////API Key Generator
    private function findRandom() {
        $mRandom = rand(48, 122);
        return $mRandom;
    }
    
    /**
     * Checks if $random equals ranges 48:57, 56:90, or 97:122.
     * <p>
     * This function is being used to filter $random so that when used in:
     * '&#' . $random . ';' it will generate the ASCII characters for ranges
     * 0:8, a-z (lowercase), or A-Z (uppercase).
     * <p>
     * @param int $mRandom Non-cryptographically generated random number.
     * @return int 0 if not within range, else $random is returned. 
     */
    private function isRandomInRange($mRandom) {
        if(($mRandom >=58 && $mRandom <= 64) ||
                (($mRandom >=91 && $mRandom <= 96))) {
            return 0;
        } else {
            return $mRandom;
        }
    }   
    
    //32 chars
    private function APIKeyGen() {
        $output = null;
        for($loop = 0; $loop <= 31; $loop++) {
            for($isRandomInRange = 0; $isRandomInRange === 0;){
                $isRandomInRange = $this->isRandomInRange($this->findRandom());
            }
            $output .= html_entity_decode('&#' . $isRandomInRange . ';');
        }
        return strtolower($output);
    }
 
    ////////////////////sql query DB,depending on Framework

    public function query_db($sql){
        //global $npdb;
        return $this->_connection->query($sql);
    }

    private function get_value_query_db($sql){
        //global $wpdb;
        $result= $this->_connection->query($sql);
        if (empty($result)) return false;
        $value = mysqli_fetch_object($result);
        return $value->val;
    }

    private function get_values_query_db($sql){
        //global $wpdb;
        $results= $this->_connection->query($sql);
        return $results;
    }

    //GET Functions

    private function get_merchants_table_name(){
        return 'merchants';
    }

    private function get_requests_table_name(){
        return 'requests';
    }

    private function get_transactions_table_name(){
        return 'transactions';
    }

    //Check exist

    private function transaction_exist($hash){
        $table_name=$this->get_transactions_table_name();
        $sql= "SELECT hash AS val FROM $table_name WHERE hash='$hash'";
        $result = $this->get_value_query_db($sql);
        if ($result) return true;
        return false;
    }

    public function update_max_block($number) {
        $sql  = "INSERT INTO vars VALUES (0,$number) ON DUPLICATE KEY UPDATE maxBlock='$number';";
        //echo "updating max block number loaded <br>";
        //echo $sql."<br>";
        return $this->query_db($sql);
    }

    public function getMaxBlock() {
        $sql  = "select maxBlock as val FROM vars WHERE id = 0 limit 1;";
        $val = $this->get_value_query_db($sql);
        return $val;
    }

    public function init_blocks_table_db($startBlock){
        $max_block_number = $startBlock;
        if ($startBlock == 0)
        $max_block_number = $this->_blockchain->get_max_block_number($this->_url);
        $this->update_max_block($max_block_number);
    }

    public function getMaxMid() {
        $table = $this->get_merchants_table_name();
        $sql = "SELECT COALESCE(MAX(mid), 0) as val 
                FROM $table";
        $result = $this->get_value_query_db($sql);
        return $result;
    }

    public function getWalletByMid($mid) {
        $table = $this->get_merchants_table_name();
        $sql = "SELECT COALESCE(wallet, '0x0') as val 
                FROM $table
                WHERE mid = '$mid'";
        $result = $this->get_value_query_db($sql);
        return $result;
    }

    public function getMidByWallet($_wallet) {
        $wallet = strtolower($_wallet);
        $table = $this->get_merchants_table_name();
        $sql = "SELECT mid as val 
                FROM $table
                WHERE wallet = '$wallet'";
        $result = $this->get_value_query_db($sql);
        return $result;
    }

    public function getNameByMid($mid) {
        $table = $this->get_merchants_table_name();
        $sql = "SELECT name as val 
                FROM $table
                WHERE mid = '$mid'";
        $result = $this->get_value_query_db($sql);
        return $result;
    }

    public function addMerchant($wallet, $merchantName, $url, $email, $gatewayWallet, $_functions, $isMobile) {
        $_wallet = strtolower($wallet);
        $privateKey = $this->APIKeyGen(); // secret key
        $publicKey = $this->APIKeyGen(); //api key
        $comfirmAmount = rand(1,10);
        $weiAmount = $comfirmAmount *1e18;
        $table_name = $this->get_merchants_table_name();

        $sql = "DELETE FROM $table_name
        WHERE wallet = '$_wallet' AND status = 'Pending'";
        $this->query_db($sql);

        $mid = $this->getMaxMid() + 1;

        $sql = "INSERT INTO " . $table_name . "(mid, wallet, name, url, email, totalRequest, totalAmount, publicKey, privateKey, comfirmAmount, status) VALUES
            ('$mid', '$_wallet', '$merchantName', '$url', '$email', 0, 0, '$publicKey', '$privateKey', $weiAmount, 'Pending')";
        $result = $this->query_db($sql);
        if (!$result) return false;
        $QRText ='{"walletaddress":"'.$gatewayWallet.'","uoid":"addMerchant","amount":"'.$comfirmAmount.'"}';
        $QRTextHex="0x".$_functions->strToHex($QRText);
        $extraData = $QRTextHex;
        $QRTextEncode= urlencode ( $QRText );
        $QRUrl = "https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=".$QRTextEncode."&choe=UTF-8";
        $androidUrl = "nextywallet://user_id=addMerchant&amount=$comfirmAmount&address=$gatewayWallet&app_name=nextypayGateway', '_system'";
        if ($isMobile == 'true') return $androidUrl;
        return $QRUrl;
    }

    public function getMerchant($wallet) {
        $table_name = $this->get_merchants_table_name();
        $sql = "SELECT * FROM $table_name 
                WHERE wallet = '$wallet'";
        $result =$this->query_db($sql);
    }

    public function getMerchantStatus($wallet) {
        $table_name=$this->get_merchants_table_name();
        $sql= "SELECT status AS val FROM $table_name WHERE wallet='$wallet'";
        $result = $this->get_value_query_db($sql);
        if ($result) return $result;
        return false;
    }

    public function getMerchantKey($wallet) {
        $table_name=$this->get_merchants_table_name();
        $sql= "SELECT privateKey AS val FROM $table_name WHERE wallet='$wallet'";
        $result = $this->get_value_query_db($sql);
        if ($result) return $result;
        return false;
    }

    public function getApiKey($wallet) {
        $table_name=$this->get_merchants_table_name();
        $sql= "SELECT publicKey AS val FROM $table_name WHERE wallet='$wallet'";
        $result = $this->get_value_query_db($sql);
        if ($result) return $result;
        return false;
    }

    private function getPrivateKey($wallet, $seed, $hash) {
        $table_name=$this->get_merchants_table_name();
        $sql= "SELECT privateKey AS val FROM $table_name WHERE wallet='$wallet'";
        $pKey = $this->get_value_query_db($sql);
        if (!$pKey) return false;
        $str = $wallet.$pKey.$seed;
        if (md5(strtolower($str)) != $hash) return false;
        return $pKey;
    }

    public function getNewApiKey($wallet, $seed, $hash) {
        $pKey = $this->getPrivateKey($wallet, $seed, $hash);
        if (!$pKey) return 0;
        $table_name=$this->get_merchants_table_name();
        $_wallet = strtolower($wallet);
        $newApiKey = $this->APIKeyGen();
        $sql = "UPDATE " . $table_name . " SET publicKey = '$newApiKey' WHERE wallet='$_wallet' AND privateKey='$pKey'";
        $result = $this->query_db($sql);
        return $newApiKey;
    }

    public function getApiKeyByMid($mid) {
        $table_name=$this->get_merchants_table_name();
        $sql= "SELECT publicKey AS val FROM $table_name WHERE mid='$mid'";
        $result = $this->get_value_query_db($sql);
        if ($result) return $result;
        return false;
    }

    private function requestExist($wallet, $shopId, $orderId) {
        $table_name=$this->get_requests_table_name();
        $sql= "SELECT id AS val FROM $table_name WHERE hash='$hash'";
        $result = $this->get_value_query_db($sql);
        if ($result) return true;
        return false;
    }

    public function getReqId($shopId,$orderId,$wallet) {
        $table_name=$this->get_requests_table_name();
        $sql= "SELECT id AS val FROM $table_name WHERE shopId = '$shopId' AND orderId = '$orderId' AND wallet = '$wallet'";
        //echo $sql;
        $result = $this->get_value_query_db($sql);
        if ($result) return $result;
        return false;
    }

    public function addRequest(
                                $shopId, 
                                $orderId, 
                                $extraData, 
                                $callbackUrl, 
                                $returnUrl, 
                                $amount, 
                                $currency, 
                                $ntyAmount, 
                                $minBlockDistance, 
                                $startTime, 
                                $endTime, 
                                $fromWallet, 
                                $toWallet, 
                                $wallet ) {
        $_wallet = strtolower($wallet);
        $_extraData = strtolower($extraData);
        $_fromWallet = strtolower($fromWallet);
        //$_toWallet = strtolower($toWallet);
        $_toWallet = ((!$toWallet) || ($toWallet == 'default')) ? $_wallet : strtolower($toWallet);
        $_weiAmount = $ntyAmount *1e18;

        $table_name = $this->get_requests_table_name();

        $sql = "INSERT INTO " . $table_name . "(shopId, orderId, extraData, callbackUrl, returnUrl, amount, currency, ntyAmount,
            minBlockDistance, status, fromWallet, toWallet, wallet) VALUES

            ('$shopId', '$orderId', '$_extraData', '$callbackUrl', '$returnUrl', '$amount', '$currency' , '$_weiAmount', '$minBlockDistance', 
            'Pending', '$_fromWallet', '$_toWallet', '$_wallet')";
        //echo "adding request : <br>";
        //echo $sql;
        if ($this->query_db($sql)) return $this->_connection->conn->insert_id; else
        {
            //echo "ADDED REQUEST";
            return $this->getReqId($shopId,$orderId,$wallet);}
    }

    
    public function searchReqId($transaction){
        $toWallet = $transaction['to'];
        $extraData = $transaction['input'];
        $table_name = $this->get_requests_table_name();
        //check requests, extraData = extraData, wallet = toWallet return request id
        $sql = "SELECT id as val FROM $table_name
                WHERE toWallet = '$toWallet' AND extraData = '$extraData' AND status = 'Pending'
                LIMIT 1";
        echo $sql."<br>";
        $val = $this->get_value_query_db($sql);
        if (!$val) return -1;
        return $val;
    }

    public function getTransfered($reqId) {
        $table_name = $this->get_transactions_table_name();
        $sql = "SELECT SUM(ntyAmount) as val FROM $table_name
        WHERE reqId = '$reqId' AND status = 'Accepted'";
        $val = $this->get_value_query_db($sql);
        if (empty($val)) return 0;
        return $val;
    }

    public function getGasUsed($reqId) {
        $table_name = $this->get_transactions_table_name();
        $sql = "SELECT SUM(gasUsed) as val FROM $table_name
        WHERE reqId = '$reqId' AND status = 'Accepted'";
        $val = $this->get_value_query_db($sql);
        if (empty($val)) return 0;
        return $val;
    }

    public function searchMerchant($transaction, $fromWallet, $toWallet, $ntyAmount) {
        $_fromWallet = strtolower($fromWallet);
        //echo "<br>gatewayWallet = $this->_gatewayWallet from $fromWallet to $toWallet <br>";
        if (strtolower($this->_gatewayWallet) != strtolower($toWallet)) return false;
        $table = $this->get_merchants_table_name();
        //check requests, extraData = extraData, wallet = toWallet return request id
        $sql = "UPDATE $table
                SET status='Comfirmed' 
                WHERE status = 'Pending' AND comfirmAmount = '$ntyAmount' AND wallet= '$_fromWallet' ";
        //echo "<br>$sql <br>";
        return $this->query_db($sql);
    }

    private function insert_transactions_db($transactions){
        //search request by toWallet and extraData
        $table_name=$this->get_transactions_table_name();
        foreach ($transactions as $transaction) {
            $toWallet = $transaction['to'];
            $fromWallet = $transaction['from'];
            $ntyAmount = hexdec($transaction['value']);
            $extraData=$transaction['input'];
            $reqId = $this->searchReqId($transaction);
            $merchantId = $this->searchMerchant($transaction, $fromWallet, $toWallet, $ntyAmount);

            if ($reqId >= 0){

                $block_hash = $transaction['blockHash'];
                $blockNumber = hexdec($transaction['blockNumber']);
                //echo $blockNumber;

                $gasUsed = hexdec($transaction['gas']);
                $status = "Pending";

                $hash=strtolower($transaction['hash']);
                //echo "hash = " . json_encode($transaction) . "<br>";
                //echo "ntyAmount = " . $ntyAmount;

                if (!$this->transaction_exist($hash)){
                    $sql = "INSERT INTO " . $table_name . "(hash, fromWallet, toWallet, ntyAmount, gasUsed, blockNumber, reqId, status) VALUES
                        ('$hash', '$fromWallet', '$toWallet', '$ntyAmount', '$gasUsed', '$blockNumber', '$reqId', '$status')";
                        //echo $sql."<br>";
                    $this->query_db($sql);
                }

            }
        }
    }
    
    public function scan_block_db($block_content){
        //if block still unavaiable
        if (!$block_content) return false;

        $transactions=$block_content['transactions'];

        $this->insert_transactions_db($transactions);
        return true;
    }


    public function updateTransactions() {
        $currentBlock = $this->getMaxBlock();
        $tTable = $this->get_transactions_table_name();
        $rTable = $this->get_requests_table_name();
        /*
        $sql = "SELECT DISTINCT $tTable.reqId as val FROM $tTable
        join $rTable on $tTable.reqId = $rTable.id 
        WHERE $tTable.status= 'Pending' AND $tTable.blockNumber + $rTable.minBlockDistance < $currentBlock";
        $result = $this->get_values_query_db($sql);
        echo $sql;
        while($row = mysqli_fetch_assoc($result)) {
        echo "<br>" . $row['val'] . "<br>";
        }*/
        //echo json_encode($result);
        //Ultimate
        $sql = "UPDATE $tTable
            join $rTable on $tTable.reqId = $rTable.id 
            SET $tTable.status='Accepted' 
            WHERE $tTable.status= 'Pending' AND $tTable.blockNumber + $rTable.minBlockDistance < $currentBlock";
        //echo "<br>" . $sql;
        $this->query_db($sql);
    }

    function updateRequests() {
        //echo "<br> Updating Requests <br>";
        $tTable = $this->get_transactions_table_name();
        $rTable = $this->get_requests_table_name();
        $sql = "UPDATE $rTable 
                SET status = 'Paid'
                WHERE status='Pending' AND ntyAmount <= GET_TRANSFERED(id)";
        //echo $sql;
        $result = $this->query_db($sql);
    }

    private function reqComplete($reqId) {
        $rTable = $this->get_requests_table_name();
        $sql = "UPDATE $rTable 
                SET status = 'Comfirmed'
                WHERE id = '$reqId'";
        //echo $sql;
        $result = $this->query_db($sql);
    }

    public function recomfirm($reqId) {
        $rTable = $this->get_requests_table_name();
        $sql = "UPDATE $rTable 
                SET status = 'Pending'
                WHERE id = '$reqId'";
        //echo $sql;
        $result = $this->query_db($sql);
    }

    private function callback($reqId, $privateKey, $callbackUrl, $shopId, $orderId, $status, $extraData, $amount, $currency, $ntyAmount, $gas) {
        $hash = md5($orderId . $reqId . $amount . $privateKey);
        //echo $privateKey;
		$fields = array(
			'shopId' => $shopId,
            'orderId' => $orderId,
            'reqId' => $reqId,
            'status' => $status,
            'hash' => $hash,
            'fee' => 0,
            'extraData' => $extraData,
            'amount' => $amount,
            'gas' => $gas,
            'success' => true
        );
        //echo json_encode($fields);
        /*
        $secretKey = $nextypayParams['secretKey'];
if ($hash != md5($invoiceId . $transactionId . $paymentAmount . $secretKey)) {
    $transactionStatus = 'Hash Verification Failure';
    $success = false;
}
*/
        

        $postvars='';
        $sep='';
        foreach($fields as $key=>$value)
        {
                $postvars.= $sep.urlencode($key).'='.urlencode($value);
                $sep='&';
        }
        
        $ch = curl_init();
        
        curl_setopt($ch,CURLOPT_URL,$callbackUrl);
        curl_setopt($ch,CURLOPT_POST,count($fields));
        curl_setopt($ch,CURLOPT_POSTFIELDS,$postvars);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        
        $result = curl_exec($ch);
        echo $result;
        echo 'test <br>';
        curl_close($ch);
        
        return $result;
    }

    public function getReturnUrl($reqId) {
        $table_name=$this->get_requests_table_name();
        $sql= "SELECT returnUrl AS val FROM $table_name WHERE id ='$reqId' AND status ='Comfirmed'";
        $val = $this->get_value_query_db($sql);
        if ($val) return $val;
        return false;
    }

    public function getReqStatus($reqId) {
        $table_name=$this->get_requests_table_name();
        $sql= "SELECT status AS val FROM $table_name WHERE id ='$reqId'";
        $val = $this->get_value_query_db($sql);
        //echo $sql;
        if ($val) return $val;
        return false;
    }

    function callbackVerify($response) {
        echo $response;
        if ($response == 'Ok') return true;
        return false;
    }

    function comfirmRequests() {
        $rTable = $this->get_requests_table_name();
        $sql = "SELECT id, extraData, callbackUrl, shopId, orderId, amount, currency, ntyAmount, wallet FROM $rTable
                WHERE status = 'Paid' "; 
        //echo $sql;
        $result = $this->get_values_query_db($sql);
        while($row = mysqli_fetch_assoc($result)) {
            $reqId = $row['id'];
            $extraData = $row['extraData'];
            $callbackUrl = $row['callbackUrl'];
            echo $callbackUrl;
            $shopId = $row['shopId'];
            $orderId = $row['orderId'];
            $status = 'Paid';
            $amount = $row['amount'];
            $currency = $row['currency'];
            $weiAmount = $this->getTransfered($reqId);
            $ntyAmount = $weiAmount *1e-18;
            $gas = $this->getGasUsed($reqId);
            $wallet = $row['wallet'];
            $privateKey = $this->getMerchantKey($wallet);
            // echo "<br> id " . $row['id'] . "<br>";
            // echo "<br> extraData " . $row['extraData'] . "<br>";
            //echo "<br> callbackUrl " . $row['callbackUrl'] . "<br>";
            $response = $this->callback($reqId, 
                                        $privateKey, 
                                        $callbackUrl, 
                                        $shopId, 
                                        $orderId, 
                                        $status, 
                                        $extraData, 
                                        $amount,
                                        $currency,
                                        $ntyAmount,
                                        $gas);
            if ($this->callbackVerify($response)) $this->reqComplete($reqId);
        }
    }

    function cleanUp() {
        $duration = 10;//seconds
    }

    public function updatedb(){
        //$this->init_blocks_table_db();
        $this->updateTransactions();
        $this->updateRequests();
        $this->comfirmRequests();
        //echo "last Block loaded : ". $this->getMaxBlock() . "<br>";
        $total=$this->_blocks_loaded_each_request;
        //scan from this block number
        $from = $this->getMaxBlock() + 1;
        //scan to this block number - 1
        $to = $from + $total;
        
        for ($scan = $from; $scan < $to; $scan++){
            $hex = "0x".strval(dechex($scan)); //convert to hex
            $block = $this->_blockchain->get_block_by_number($this->_url,$hex);	//get Block by number with API
            if (isset($block['result'])) $blockContent = $block['result']; else exit;
            if (!$this->scan_block_db($blockContent)) break;
            $this->update_max_block($scan-1);
        }
    }

}
?>

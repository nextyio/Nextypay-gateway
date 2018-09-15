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
        echo "updating max block number loaded <br>";
        echo $sql."<br>";
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

    public function addMerchant($wallet, $name, $url, $tokenKey) {
        $_wallet = strtolower($wallet);
        $table_name = $this->get_merchants_table_name();
        $sql = "INSERT INTO " . $table_name . "(wallet, name, url, totalRequest, totalAmount, tokenKey) VALUES
            ('$_wallet', '$name', '$url', 0, 0, '$tokenKey')";
        $this->query_db($sql);
    }

    private function requestExist($wallet, $shopId, $orderId) {
        $table_name=$this->get_requests_table_name();
        $sql= "SELECT id AS val FROM $table_name WHERE hash='$hash'";
        $result = $this->get_value_query_db($sql);
        if ($result) return true;
        return false;
    }

    public function addRequest($shopId, $orderId, $extraData, $callbackUrl, $returnUrl, $ntyAmount, 
                                $minBlockDistance, $startTime, $endTime, $fromWallet, $toWallet, $wallet ) {

        $_extraData = strtolower($extraData);
        $_fromWallet = strtolower($fromWallet);
        $_toWallet = strtolower($toWallet);
        $_wallet = strtolower($wallet);

        $table_name = $this->get_requests_table_name();
        $sql = "INSERT INTO " . $table_name . "(shopId, orderId, extraData, callbackUrl, returnUrl, ntyAmount,
            minBlockDistance, status, fromWallet, toWallet, wallet) VALUES

            ('$shopId', '$orderId', '$_extraData', '$callbackUrl', '$returnUrl', '$ntyAmount', '$minBlockDistance', 
            'Pending', '$_fromWallet', '$_toWallet', '$_wallet')";
        echo "adding request : <br>";
        echo $sql;
        $this->query_db($sql);

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
        echo $sql."<br>";
        $val = $this->get_value_query_db($sql);
        if (empty($val)) return 0;
        return $val;
    }

    private function insert_transactions_db($transactions){
        //search request by toWallet and extraData
        $table_name=$this->get_transactions_table_name();
        foreach ($transactions as $transaction) {
            $extraData=$transaction['input'];
            $reqId = $this->searchReqId($transaction);

            if ($reqId >= 0){

                $block_hash = $transaction['blockHash'];
                $blockNumber = hexdec($transaction['blockNumber']);
                echo $blockNumber;

                $fromWallet = $transaction['from'];
                $toWallet = $transaction['to'];
                $ntyAmount = hexdec($transaction['value']);
                $gasUsed = hexdec($transaction['gas']);
                $status = "Pending";

                $hash=strtolower($transaction['hash']);
                echo "hash = " . json_encode($transaction) . "<br>";
                echo "ntyAmount = " . $ntyAmount;
                $extraData=$transaction['input'];

                if (!$this->transaction_exist($hash)){
                    $sql = "INSERT INTO " . $table_name . "(hash, fromWallet, toWallet, ntyAmount, gasUsed, blockNumber, reqId, status) VALUES
                        ('$hash', '$fromWallet', '$toWallet', '$ntyAmount', '$gasUsed', '$blockNumber', '$reqId', '$status')";
                        echo $sql."<br>";
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

    function updateRequest($reqId) {

    }

    public function updateTransactions() {
        $currentBlock = $this->getMaxBlock();
        $tTable = $this->get_transactions_table_name();
        $rTable = $this->get_requests_table_name();
        $sql = "SELECT DISTINCT $tTable.reqId as val FROM $tTable
        join $rTable on $tTable.reqId = $rTable.id 
        WHERE $tTable.status= 'Pending' AND $tTable.blockNumber + $rTable.minBlockDistance < $currentBlock";
        $result = $this->get_values_query_db($sql);
        echo $sql;
        while($row = mysqli_fetch_assoc($result)) {
        echo "<br>" . $row['val'] . "<br>";
        }
        //echo json_encode($result);
        //Ultimate
        $sql = "UPDATE $tTable
            join $rTable on $tTable.reqId = $rTable.id 
            SET $tTable.status='Accepted' 
            WHERE $tTable.status= 'Pending' AND $tTable.blockNumber + $rTable.minBlockDistance < $currentBlock";
        echo "<br>" . $sql;
        $this->query_db($sql);
    }

    function updateRequests() {
        echo "<br> Updating Requests <br>";
        $tTable = $this->get_transactions_table_name();
        $rTable = $this->get_requests_table_name();
        $sql = "UPDATE $rTable 
                SET status = 'Paid'
                WHERE status='Pending' AND ntyAmount <= GET_TRANSFERED(id)";
        echo $sql;
        $result = $this->query_db($sql);
    }

    private function callback($callbackUrl, $shopId, $orderId, $status, $extraData) {
		$fields = array(
			'shopId' => $shopId,
			'orderId' => $orderId,
			'status' => $status,
            'extraData' => $extraData,
        );
        
        $url = $callbackUrl;

		$data_string = json_encode($fields);
        $ch = curl_init($url);
        
        //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        //curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen($data_string))
		);

        $result = curl_exec($ch);
        //echo $result;
        $json_result = json_decode($result,true);
        echo "<br>doing callback<br>";
        echo $json_result;
        return $json_result;
    }

    function comfirmRequests() {
        $rTable = $this->get_requests_table_name();
        $sql = "SELECT id, extraData, callbackUrl, shopId, orderId FROM $rTable
                WHERE status = 'Paid' "; 
        echo $sql;
        $result = $this->get_values_query_db($sql);
        while($row = mysqli_fetch_assoc($result)) {
            $reqId = $row['id'];
            $extraData = $row['extraData'];
            $callbackUrl = $row['callbackUrl'];
            $shopId = $row['shopId'];
            $orderId = $row['orderId'];
            $status = 'Paid';
            // echo "<br> id " . $row['id'] . "<br>";
            // echo "<br> extraData " . $row['extraData'] . "<br>";
            // echo "<br> callbackUrl " . $row['callbackUrl'] . "<br>";
            $response = $this->callback($callbackUrl, $shopId, $orderId, $status, $extraData);
        }
    }

    function cleanUp() {
        $duration = 10;//seconds
    }

    public function updatedb(){
        //$this->init_blocks_table_db();
        echo "last Block loaded : ". $this->getMaxBlock() . "<br>";
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
        $this->updateTransactions();
        $this->updateRequests();
        $this->comfirmRequests();
    }

}
?>

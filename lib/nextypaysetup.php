<?php
//wallet 40 chars
//hash 60 chars
class NextypaySetup {

    private $merchantsTable     = "merchants";
    private $requestsTable      = "requests";
    private $transactionsTable  = "transactions";
    private $undefinedTxTable   = "undefinedtxs";
    private $varsTable          = "vars";

    private function createMerchantsTable(){
        global $npdb;
        $table = $this->merchantsTable;
        $sql =  "
                CREATE TABLE IF NOT EXISTS " . "$table" . "(
                    mid bigint(20) default 1,
                    wallet char(50) ,
                    name char(50),
                    url char(50),
                    email char(50),
                    totalRequest bigint(20) DEFAULT 0,
                    totalAmount bigint(20) DEFAULT 0,
                    publicKey char(50),
                    privateKey char(50),
                    comfirmAmount decimal(60,0) ,
                    status enum('Pending', 'Comfirmed', 'Accepted'),

                    PRIMARY KEY (wallet)
                ) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;";

        $npdb->query($sql);

        //TEST INIT
        $mid = 1;
        $_wallet = strtolower('0x95EF1b2632857730D1eE99C64417209a8F51889D');
        $privateKey = 'xxxx'; // secret key
        $publicKey = 'xxxx'; //api key
        $comfirmAmount = rand(1,10);
        $weiAmount = $comfirmAmount *1e18;
        //$merchantName, $url, $email, $gatewayWallet
        $merchantName = 'test Lucky';
        $url = 'http://207.148.119.222:3003/api/callback/deposit';
        $email = 'test@luckly.mail';
        $gatewayWallet = '0x0';

        
        $sql = "INSERT INTO " . $table . " (mid, wallet, name, url, email, totalRequest, totalAmount, publicKey, privateKey, comfirmAmount, status) VALUES
        ('$mid', '$_wallet', '$merchantName', '$url', '$email', 0, 0, '$publicKey', '$privateKey', $weiAmount, 'Comfirmed')";
        echo $sql;
        $npdb->query($sql);
    }
    
    private function createRequestsTable(){
        global $npdb;
        $table = $this->requestsTable;
        $sql = "
                CREATE TABLE IF NOT EXISTS " . "$table" . "(
                    id bigint(20) AUTO_INCREMENT,
                    extraData text ,
                    callbackUrl text ,
                    shopId varchar(255),
                    orderId varchar(255),
                    returnUrl text ,
                    amount text ,
                    currency text ,
                    ntyAmount decimal(60,0) ,
                    minBlockDistance bigint(10) DEFAULT 0,
                    startTime datetime,
                    endTime datetime,
                    status enum('Pending', 'Paid', 'Comfirmed'),
                    nextCall datetime DEFAULT CURRENT_TIMESTAMP,
                    totalCalls int DEFAULT 0,

                    fromWallet char(50) DEFAULT NULL,
                    toWallet char(50) ,
                    reqToken char(50),

                    PRIMARY KEY (id),
                    wallet char(50)  ,
                    FOREIGN KEY (wallet) REFERENCES merchants(wallet)
                    ON DELETE CASCADE ON UPDATE CASCADE,
                    UNIQUE (wallet, shopId, orderId)
                ) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;";

        $npdb->query($sql);
    }

    private function createTransactionsTable(){
        global $npdb;
        $table = $this->transactionsTable;
        $sql = "
                CREATE TABLE IF NOT EXISTS " . "$table" . "(
                    hash char(70) ,
                    fromWallet char(50) ,
                    toWallet char(50) ,
                    ntyAmount decimal(60,0) ,
                    gasUsed bigint(20) DEFAULT NULL,
                    blockNumber bigint(20) ,
                    reqId bigint(20)  ,
                    FOREIGN KEY (reqId)REFERENCES requests(id) 
                    ON DELETE CASCADE ON UPDATE CASCADE,
                    status enum('Pending', 'Accepted'),

                    PRIMARY KEY (hash)
                ) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;";
        $npdb->query($sql);
    }

    private function createUndefinedTxTable(){
        global $npdb;
        $table = $this->undefinedTxTable;
        $sql = "
                CREATE TABLE IF NOT EXISTS " . "$table" . "(
                    hash char(70) ,
                    fromWallet char(50) ,
                    toWallet char(50) ,
                    ntyAmount decimal(60,0) ,
                    gasUsed bigint(20) DEFAULT NULL,
                    blockNumber bigint(20) ,
                    mWallet char(50)  ,
                    FOREIGN KEY (mWallet) REFERENCES merchants(wallet) 
                    ON DELETE CASCADE ON UPDATE CASCADE,
                    status enum('Pending', 'Comfirmed', 'Failed'),
                    nextCall datetime DEFAULT CURRENT_TIMESTAMP,
                    totalCalls int DEFAULT 0,

                    PRIMARY KEY (hash)
                ) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;";
        $npdb->query($sql);
    }

    private function createVarsTable(){
        global $npdb;
        $table = $this->varsTable;
        $sql = "
                CREATE TABLE IF NOT EXISTS " . $table. " (
                id bigint(9)  PRIMARY KEY,
                maxBlock bigint(9) 
                ) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;";

        $npdb->query($sql);
        //echo $sql;
    }

    private function createFunction() {
        global $npdb;
        $sql = "
                CREATE FUNCTION GET_TRANSFERED (REQUESTID bigint ) RETURNS decimal(60,0)  RETURN (
                SELECT COALESCE(SUM(ntyAmount), 0)
                FROM $this->transactionsTable
                WHERE reqId = REQUESTID AND status = 'Accepted')";

        $npdb->query($sql);
        //echo $sql;
    }

    public function install() {
        $this->createMerchantsTable();
        $this->createRequestsTable();
        $this->createTransactionsTable();
        $this->createUndefinedTxTable();
        $this->createVarsTable();
        $this->createFunction();
    }

    public function uninstall() {
        global $npdb;
        $sql = 'DROP DATABASE nextypay';
        $npdb->query($sql);
    }
}

?>

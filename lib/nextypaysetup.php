<?php
//wallet 40 chars
//hash 60 chars
class NextypaySetup {

    private $merchantsTable     = "merchants";
    private $requestsTable      = "requests";
    private $transactionsTable  = "transactions";
    private $varsTable          = "vars";

    private function createMerchantsTable(){
        global $npdb;
        $table = $this->merchantsTable;
        $sql =  "
                CREATE TABLE IF NOT EXISTS " . "$table" . "(
                    mid mediumint(20) default 1,
                    wallet char(50) ,
                    name char(50),
                    url char(50),
                    email char(50),
                    totalRequest mediumint(20) DEFAULT 0,
                    totalAmount mediumint(20) DEFAULT 0,
                    publicKey char(50),
                    privateKey char(50),
                    comfirmAmount decimal(60,0) ,
                    status enum('Pending', 'Comfirmed', 'Accepted'),

                    PRIMARY KEY (wallet)
                ) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;";

        $npdb->query($sql);
    }
    
    private function createRequestsTable(){
        global $npdb;
        $table = $this->requestsTable;
        $sql = "
                CREATE TABLE IF NOT EXISTS " . "$table" . "(
                    id mediumint(20) AUTO_INCREMENT,
                    extraData text ,
                    callbackUrl text ,
                    shopId mediumint(20),
                    orderId mediumint(20),
                    returnUrl text ,
                    amount text ,
                    currency text ,
                    ntyAmount decimal(60,0) ,
                    minBlockDistance mediumint(10) DEFAULT 0,
                    startTime datetime,
                    endTime datetime,
                    status enum('Pending', 'Paid', 'Comfirmed'),

                    fromWallet char(50) DEFAULT NULL,
                    toWallet char(50) ,

                    PRIMARY KEY (id),
                    wallet char(50)  ,
                    FOREIGN KEY (wallet) REFERENCES merchants(wallet),
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
                    toWallet char(50)  ,FOREIGN KEY (toWallet) REFERENCES merchants(wallet),
                    ntyAmount decimal(60,0) ,
                    gasUsed mediumint(20) DEFAULT NULL,
                    blockNumber mediumint(20) ,
                    reqId mediumint(20)  ,FOREIGN KEY (reqId)REFERENCES requests(id),
                    status enum('Pending', 'Accepted'),

                    PRIMARY KEY (hash)
                ) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;";
        $npdb->query($sql);
    }

    private function createVarsTable(){
        global $npdb;
        $table = $this->varsTable;
        $sql = "
                CREATE TABLE IF NOT EXISTS " . $table. " (
                id mediumint(9)  PRIMARY KEY,
                maxBlock mediumint(9) 
                ) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;";

        $npdb->query($sql);
        //echo $sql;
    }

    private function createFunction() {
        global $npdb;
        $sql = "
                CREATE FUNCTION GET_TRANSFERED (REQUESTID mediumint ) RETURNS decimal(60,0)  RETURN (
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

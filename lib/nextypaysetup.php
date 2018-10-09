<?php
//wallet 40 chars
//hash 60 chars
class NextypaySetup {

    public $merchants_table_name  = "merchants";
    public $requests_table_name  = "requests";
    public $transactions_table_name  = "transactions";

    public function create_merchants_table_db(){
        global $npdb;
        $table_name = $this->merchants_table_name;
        $sql="
        CREATE TABLE IF NOT EXISTS " . "$table_name" . "(
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
    // amount mediumint(20) ,
    // transfered mediumint(20)  DEFAULT 0,
    //currency char(10) ,
    public function create_requests_table_db(){
        global $npdb;
        $table_name = $this->requests_table_name;
        $sql="
        CREATE TABLE IF NOT EXISTS " . "$table_name" . "(
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

    public function create_transactions_table_db(){
        global $npdb;
        $table_name = $this->transactions_table_name;
        $sql="
        CREATE TABLE IF NOT EXISTS " . "$table_name" . "(
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

    public function create_vars_table_db(){
        global $npdb;
        $table_name = "vars";
        $sql="
            CREATE TABLE IF NOT EXISTS " . $table_name. " (
            id mediumint(9)  PRIMARY KEY,
            maxBlock mediumint(9) 
            ) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;";
        $npdb->query($sql);
        echo $sql;
    }

    public function create_function() {
        global $npdb;
        $sql="CREATE FUNCTION GET_TRANSFERED (REQUESTID mediumint ) RETURNS decimal(60,0)  RETURN (
                SELECT COALESCE(SUM(ntyAmount), 0)
                FROM $this->transactions_table_name
                WHERE reqId = REQUESTID AND status = 'Accepted')";
        $npdb->query($sql);
        echo $sql;
    }

    public function install() {
        $this->create_merchants_table_db();
        $this->create_requests_table_db();
        $this->create_transactions_table_db();
        $this->create_vars_table_db();
        $this->create_function();
    }

    public function uninstall() {
        global $npdb;
        $sql = 'DROP DATABASE nextypay';
        $npdb->query($sql);
    }
}

?>

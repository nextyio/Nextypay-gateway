<?php

class npdb {

	protected $dbuser;
	protected $dbpassword;
	protected $dbname;
	protected $dbhost;
	public $conn;

	public $prefix;

	public $result;

	public function __construct( $dbuser, $dbpassword, $dbname, $dbhost ) {

		$this->dbuser 		= $dbuser;
		$this->dbpassword 	= $dbpassword;
		$this->dbname 		= $dbname;
		$this->dbhost 		= $dbhost;
		$this->prefix 		= "np";

		$this->conn = $this->db_connect();
	}

	private function db_connect() {
		// Create connection
		$conn = new mysqli($this->dbhost, $this->dbuser, $this->dbpassword);
		$db_selected = mysqli_select_db($conn, $this->dbname);

		if (!$db_selected) {
				// If we couldn't, then it either doesn't exist, or we can't see it.
			echo $this->dbname;
			$sql = "CREATE DATABASE $this->dbname";
			
			if (mysqli_query($conn, $sql )) {
				return new mysqli($this->dbhost, $this->dbuser, $this->dbpassword, $this->dbname);
			} else {
				echo 'Error creating database: ' . mysqli_error() . "\n";
			}
		}

		return $conn;
	}

	public function query( $query ) {
		return $this->result = mysqli_query( $this->conn, $query );
	}
}

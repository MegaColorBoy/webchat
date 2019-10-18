<?php
//Database connection class
include_once('config.php');

class DB_CONNECT
{
	private $conn;

	function __construct(){}

	//Connect to database -- if it's null, it'll use the main DB, else it'll use the DB that you have specified
	function connect($db_name=null)
	{		
		if(!is_null($db_name))
		{
			$this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, $db_name);
		}
		else
		{
			$this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
		}

		//If there's any error, display error
		if(mysqli_connect_errno())
		{
			echo "Failed to connect to MySQL Database: " . mysqli_connect_error();
		}

		return $this->conn;
	}
}
?>
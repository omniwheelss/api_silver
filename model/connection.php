<?php
/*
DB Connection file to connect the database

*/
class Database
{   
	private $host = "localhost";
	private $db_name = "track";
	private $username = "root";
	private $password = "";
	public $conn;

	public function dbConnection()
	{
		$this->conn = null;    
		$con_status =  $log_data = null;
		
		try{
			$this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name, $this->username, $this->password);
			$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);	
		}
		catch(PDOException $e){
			$con_status =  'error';
			$log_data = "|DB Connection exception|".$e->getMessage()."".CONFIG::NEWLINE_ERROR."|";
			include 'views/screen/500.php';
			exit;
		}
		return $this->conn;
	}
}

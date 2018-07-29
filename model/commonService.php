<?php

/**
	Includes all other controllers and models
**/
require_once 'controller/includes.php';

class CommonService
{	
	private $conn;
	
	public function __construct()
	{
		$database = new Database();
		$db = $database->dbConnection();
		$this->conn = $db;
	}


	/**
	
	log data function to insert the activity on the app
	
	**/
	public function dataLoggerDB($userAccountID, $logData, $dateTime)
	{
		try {
			$dbValues = array($userAccountID,''.$logData.'',''.$dateTime.'');	
			$stmt = $this->conn->prepare("INSERT INTO logs (user_account_id,activity,date_stamp) VALUES (?,?,?)");
			$stmt->execute($dbValues);
			$logData = "successfully logged";
			$logDataStatus = 'success';
			return true;
		}
		catch (PDOException $e) {
			$logDataStatus =  'error';
			$logData = "|insert logs exception|".$e->getMessage()."".CONFIG::NEWLINE_ERROR."|";
		}
		return array($userAccountID, $logData, $logDataStatus);
	}
	

}
?>
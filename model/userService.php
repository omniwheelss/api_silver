<?php
/**
	Includes all other controllers and models
**/
require_once 'controller/includes.php';

class UserService
{	
	private $conn;
	
	public function __construct()
	{
		$database = new Database();
		$db = $database->dbConnection();
		$this->conn = $db;
	}
	
	
	/**
		user login function to check valid or invalid
	**/
	public function doLogin($getDataFromJson)
	{
		# Variable declaration
		$message = $userAccountID = $dataStatus = $data = null;
		$dataStatus = 'failure';
		try {
			$email = $getDataFromJson['email'];
			$password = md5($getDataFromJson['password']);

			$stmt = $this->conn->prepare("CALL spSelUsersForLogin(?,?)");
			$stmt->bindParam(1,$email, PDO::PARAM_STR);
			$stmt->bindParam(2,$password, PDO::PARAM_STR);
			$stmt->execute();
			
			if($stmt->rowCount() == 1) {
				
				$resultRow = $stmt->fetch(PDO::FETCH_ASSOC);
				if (strtolower($resultRow['account_status']) == 'active') {
					//Expiry Checking
					if ($resultRow['valid_to'] >= date("Y-m-d H:i:s")) {
						$message = "user successfully logged";
						$userAccountID = $resultRow['user_account_id'];
						$dataStatus = 'success';
						$data = $resultRow;
					}
					else {
						$message = "Oops!!! your account was expired";
					}						
				}
				else {
					$message = "Oops!!! your account was locked";
				}					
			}
			else {
				$message = "emailid/password wrong";
			}
		}
		catch (PDOException $e) {
			$dataStatus =  'error';
			$message = "|user login exception|".$e->getMessage()."".CONFIG::NEWLINE_ERROR."|";
		}
		return array($userAccountID, $dataStatus, $message, $data);
	}
	
	
		
	/**
		select Account ID by IMEI
	**/
	public function getAccountIDByIMEI($imei)
	{
		try {
			$selectStatus = $message = $userAccountID = null;
			$stmt = $this->conn->prepare("CALL spSelAccountIDByIMEI(?)");
			$stmt->bindParam(1,$imei, PDO::PARAM_STR);
			$stmt->execute();
			$selectStatus = 'failure';
			if ($stmt->rowCount() == 1) {
				$resultRow=$stmt->fetch(PDO::FETCH_ASSOC);
				$userAccountID = $resultRow['user_account_id'];
				$selectStatus = 'success';
			}
		}
		catch (PDOException $e) {
			$selectStatus = 'error';
			$message = "|get account id by imei exception|".$e->getMessage()."".CONFIG::NEWLINE_ERROR."|";
		}
		return array($selectStatus, $userAccountID);
	}		

	
	/**
		Users Screen
	**/	
	public function usersScreen($aid)
	{
		# Variable declaration
		$message = $dataStatus = $data = null;
		
		try {
			$stmt = $this->conn->prepare("CALL SelUsersScreenByAccountID(?)");
			$stmt->bindParam(1,$aid, PDO::PARAM_INT);
			$stmt->execute();
			if($stmt->rowCount() >= 1) {
				while($resultRow = $stmt->fetch(PDO::FETCH_ASSOC)){
					$data[] = $resultRow;
				}
				$message = "user screen data fetched";
				$dataStatus = 'success';
			}
			else {
				$message = "no data available for the Account ID ".$aid."";
				$dataStatus = 'failure';
			}
		}
		catch (PDOException $e) {
			$dataStatus =  'error';
			$logData = "|user screen status exception|".$e->getMessage()."".CONFIG::NEWLINE_ERROR."|";
		}
		return array($aid, $dataStatus, $message, $data);
	}	

	
	
	/**
		Add User information
	**/
	public function addUser($getDataFromJson)
	{
		# Variable declaration
		$message = $userAccountID = $dataStatus = $data = null;
		$dataStatus = 'failure';
		
		try {
			$db_values = array($getDataFromJson['firstName'],$getDataFromJson['lastName']);	
			$stmt = $this->conn->prepare("INSERT INTO test (Textbox_Name,Password_Name) VALUES (?,?)");
			$stmt->execute($db_values);
			$log_data = "successfully logged";
			$log_data_status = 'success';
			return true;
		}
		catch (PDOException $e) {
			$dataStatus =  'error';
			$message = "|user login exception|".$e->getMessage()."".CONFIG::NEWLINE_ERROR."|";
		}
		return array($userAccountID, $dataStatus, $message, $data);
	}
			
}
?>
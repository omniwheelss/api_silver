<?php

/**
	Includes all other controllers and models
**/
require_once 'includes.php';
require_once 'includesModels.php';

class UserController
{
	private $userService = NULL;
	
	/**
		Invoke model to access all the methods
	**/	
	public function __construct()
	{
		$this->userService = new UserService();
		$this->helper = new HelperController();
	}
	
	
	/**
		check user login function to check valid user or not	
	**/
	public function doLogin($method, $format) {

		$do = isset($_REQUEST['do'])?$_REQUEST['do']:NULL;	
		
		$userAccountID = $status = $message = $datas = $dataResult = null;
		
		# once user submitted
		if ($method == 'POST') {
			
			$dataFromJson = json_decode(file_get_contents('php://input'), true);
			$dataResult = $this->userService->doLogin($dataFromJson);		

			# Json data output array
			$userAccountID = $dataResult[0];
			$status = $dataResult[1];
			$message = $dataResult[2];
			$datas = $dataResult[3];
			$allDataResult[] = $datas;
		}	
		else{
			$message = "cant call the method through GET";
			$status = "failure";
		}	
		
		# Data logger	
		$this->helper->dataLoggerFileAndDB('user login', $do, $userAccountID, $message, $status);
		
		# Include view file also checking if user already logged in or not
		include	Config::VIEWS_PATH."/auth.php";
		exit;
	}	
	
	
	
	/**
		Users Screen - Access for the web page
	**/
	public function usersScreen($aid, $format) {

		$do = isset($_REQUEST['do'])?$_REQUEST['do']:NULL;	
		
		$dataResult = $message = $status = $datas = null;
		
		if ($aid != NULL) {
			
			$dataResult = $this->userService->usersScreen($aid);
			$status = $dataResult[1];
			$message = $dataResult[2];
			$allDataResult = $dataResult[3];
		}
		else{
			$message = "Parameter not met expectation";
		}

		# Data logger	
		$this->helper->dataLoggerFileAndDB('user screen list', $do, $aid, $message, $status);
		
		# Include view file also checking if user already logged in or not
		include Config::VIEWS_PATH."/userScreen.php";
		return array($status, $datas, $message);
	}	
	
	
	/**
		Add User Details
	**/
	public function addUser($method, $format) {

		$do = isset($_REQUEST['do'])?$_REQUEST['do']:NULL;	
		
		$userAccountID = $status = $message = $datas = $dataResult = null;
		
		# once user posted the data
		if ($method == 'POST') {
			
			$dataFromJson = json_decode(file_get_contents('php://input'), true);
			
			$dataResult = $this->userService->addUser($dataFromJson);		
			$allDataResult[] = $dataResult;
			# Json data output array
			/*$userAccountID = $dataResult[0];
			$status = $dataResult[1];
			$message = $dataResult[2];
			$datas = $dataResult[3];
			$allDataResult[] = $datas;*/
		}	
		else{
			$message = "Paramter not met expectation";
			$status = "failure";
		}
		
		# Data logger	
		$this->helper->dataLoggerFileAndDB('Add User', $do, $userAccountID, $message, $status);
		
		# Include view file also checking if user already logged in or not
		include	Config::VIEWS_PATH."/addUser.php";
		exit;
	}
		
}

?>
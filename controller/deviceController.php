<?php

/**
	Includes all other controllers and models
**/
require_once 'includes.php';
require_once 'includesModels.php';

class DeviceController
{

	private $commonService = NULL;
	private $deviceService = NULL;
	
	/**
		Invoke model to access all the methods
	**/	
	public function __construct()
	{
		$this->commonService = new CommonService();		
		$this->deviceService = new DeviceService();
		$this->userService = new UserService();
		$this->helper = new HelperController();
	}

	
	/**
		check device status
	**/
	public function deviceCurrentStatusForSingle($imei, $format) {
			
		$do = isset($_REQUEST['do'])?$_REQUEST['do']:NULL;	
		
		$userAccountID = $dataResult = $message = $status = $datas = null;
		
		if ($imei != NULL) {
			
			$dataResult = $this->deviceService->deviceCurrentStatusForSingle($imei);
			$datas = $dataResult[3];
			$status = $dataResult[1];
			$alertMsgCode = explode("|", $datas['alert_msg_code']);
			
			if($status == 'success'){
				$deviceStatusTextResult = $this->getDeviceStatusInText($alertMsgCode[0], $datas['device_date_stamp'], $datas['live_data'], $datas['speed'], $datas['ign']);

				$datas['deviceStatus'] = $deviceStatusTextResult[0];
				$datas['ignStatus'] = $deviceStatusTextResult[1];
				$datas['statusIcon'] = $deviceStatusTextResult[2];
			}
			
			$message = $dataResult[2];
			$allDataResult[] = $datas;
			
			# Getting account id based on IMEI
			$userResult = $this->userService->getAccountIDByIMEI($imei);
			$userAccountID = $userResult[1];
		}
		else{
			$message = "Parameter not met expectation";
		}

		# Data logger	
		$this->helper->dataLoggerFileAndDB('device status', $do, $userAccountID, $message, $status);
		
		# Include view file also checking if user already logged in or not
		include Config::VIEWS_PATH."/deviceStatus.php";
		return array($status, $datas, $message);
	}



	/**
		check device status in Text
	**/
	public function getDeviceStatusInText($alertMsgCode, $deviceDateStamp, $liveData, $speed, $ign) {
			
		$Device_Epoch_Diff = $deviceStatus =  $ignStatus = $statusIcon = null;
		
		if ($deviceDateStamp != NULL) {
			
			# Get difference 
			$Device_Epoch_Diff = $this->helper->epochDiff($deviceDateStamp);

			# Unknown Status
			if($Device_Epoch_Diff >= 1800){
				$deviceStatus = "Unknown";
				$ignStatus = "NA";
				$speed = "NA";
				$statusIcon = "grey.png";
			}
			# Moving Status
			else if($liveData == 1 && $speed > 10 && $ign == 1 ){
				$deviceStatus = "Moving";
				$ignStatus = "On";
				$statusIcon = "green.png";
			}
			# Stopped Status
			else if($liveData == 1 && $speed == 0 && $ign == 0){
				$deviceStatus = "Stopped";
				$ignStatus = "Off";
				$statusIcon = "red.png";
			}
			# Idle Status
			else if(($liveData == 1 && $speed <= 10 && $ign == 1) || $alertMsgCode == 'VI'){
				$deviceStatus = "Idle";
				$ignStatus = "On";
				$statusIcon = "orange.png";
			}				
		}
		return array($deviceStatus, $ignStatus, $statusIcon);
	}


	
	/**
		check device status for all the device associated with Account ID
	**/
	public function deviceCurrentStatusForAll($aid, $format) {
			
		$do = isset($_REQUEST['do'])?$_REQUEST['do']:NULL;	
		
		$userAccountID = $deviceResult = $dataResult = $message = $status = $Datas = $datas = $totalDeviceCount = null;
		$checkAtleastOneSuccess = false;
		
		if ($aid != NULL) {
			
			$deviceResult = $this->deviceService->deviceListByAccountID($aid);
			$status = $deviceResult[1];
			$message = $deviceResult[2];
			$deviceDatas = $deviceResult[3];
			$totalDeviceCount = count($deviceDatas);

			if($totalDeviceCount > 0 && $status == 'success'){
				# Checking Device status for each one
				foreach($deviceDatas as $deviceSingle){
					
					$datas = null;
					$imei = $deviceSingle['imei'];
					
					$dataResult = $this->deviceService->deviceCurrentStatusForSingle($imei);
					$datas = $dataResult[3];

					# Assing the value when there is no success 
					if(!$checkAtleastOneSuccess){
						$status = $dataResult[1];
						$message = $dataResult[2];
					}
					
					$alertMsgCode = explode("|", $datas['alert_msg_code']);

					if($status == 'success'){
						$deviceStatusTextResult = $this->getDeviceStatusInText($alertMsgCode[0], $datas['device_date_stamp'], $datas['live_data'], $datas['speed'], $datas['ign']);
						$datas['deviceStatus'] = $deviceStatusTextResult[0];
						$datas['ignStatus'] = $deviceStatusTextResult[1];
						$datas['statusIcon'] = $deviceStatusTextResult[2];
						$messageOut = $dataResult[2];
						$checkAtleastOneSuccess = true;
					}	
					$allDataResult[] = $datas;
				}
			}
			else{
				$messageOut = "No devices mapped with this account";
			}
		}
		else{
			$messageOut = "Parameter not met expectation";
		}
		# Data logger	
		$this->helper->dataLoggerFileAndDB('device status', $do, $aid, $message, $status);
		# Include view file also checking if user already logged in or not
		include Config::VIEWS_PATH."/deviceStatusAll.php";
	}



	/**
		device daily summary details
	**/
	public function deviceDailySummaryForSingle($imei, $fromDate, $toDate, $format) {
			
		$do = isset($_REQUEST['do'])?$_REQUEST['do']:NULL;	
		
		$userAccountID = $dataResult = $message = $status = $statusDatas = $datas = null;
		
		if ($imei != NULL) {
			
			$dataResult = $this->deviceService->deviceDailySummaryForSingle($imei, $fromDate, $toDate);
			$allDataResult = $datas = $dataResult[3];
			$status = $dataResult[1];
			$alertMsgCode = explode("|", $datas['alert_msg_code']);
			
			if($status == 'success'){
				$deviceStatusTextResult = $this->getDeviceStatusInText($alertMsgCode[0], $datas['device_date_stamp'], $datas['live_data'], $datas['speed'], $datas['ign']);

				$datas['deviceStatus'] = $deviceStatusTextResult[0];
				$datas['ignStatus'] = $deviceStatusTextResult[1];
				$datas['statusIcon'] = $deviceStatusTextResult[2];
			}
			
			$message = $dataResult[2];
			$allDataResult[] = $datas;
			
			# Getting account id based on IMEI
			$userResult = $this->userService->getAccountIDByIMEI($imei);
			$userAccountID = $userResult[1];
		}
		else{
			$message = "Parameter not met expectation";
		}

		# Data logger	
		$this->helper->dataLoggerFileAndDB('device status', $do, $userAccountID, $message, $status);
		
		# Include view file also checking if user already logged in or not
		include Config::VIEWS_PATH."/deviceStatus.php";
		return array($status, $datas, $message);
	}
	

	/**
		
		check user logged or not
		Created : 17/09/2016
		Modified : 09/12/2016
	
	**/
	public function is_loggedin($do)
	{
		if(!isset($_COOKIE[Config::COOKIE_NAME])) {
			$this->helper->redirect('index.php?do=login');
			exit;
		}
		return true;
	}

	
	/**
	
		user logout function to check valid or invalid
		Created : 17/09/2016
		Modified : 11/11/2016
	
	**/
	public function do_logout($userAccountID)
	{
		$log_message = "successfully logged out";
		$logData = "|user login|".$this->helper->get_client_ip()."|".$userAccountID."|".$log_message."|";
		$this->helper->data_logger($logData,Config::LOGS_PATH,Config::DAILY_LOG,"");
		$this->commonService->dataLoggerDB($userAccountID, $logData, date("Y-m-d H:i:s"));
		setcookie(Config::COOKIE_NAME,"",time()-345);
		$this->helper->redirect('index.php');
		return;
	}	

	/**
		Device list by Account ID
	**/
	public function deviceListByAccountID($aid, $format) {
			
		$do = isset($_REQUEST['do'])?$_REQUEST['do']:NULL;	
		
		$userAccountID = $deviceResult = $dataResult = $message = $status = $Datas = $datas = $totalDeviceCount = null;
		$checkAtleastOneSuccess = false;
		
		if ($aid != NULL) {
			
			$deviceResult = $this->deviceService->deviceListByAccountID($aid);
			$status = $deviceResult[1];
			$message = $deviceResult[2];
			$deviceDatas = $deviceResult[3];
			$totalDeviceCount = count($deviceDatas);

			if($totalDeviceCount > 0 && $status == 'success'){
				$allDataResult = $deviceResult[3];
			}
			else{
				$messageOut = "No devices mapped with this account";
			}
		}
		else{
			$messageOut = "Parameter not met expectation";
		}
		# Data logger	
		$this->helper->dataLoggerFileAndDB('device list', $do, $aid, $message, $status);
		# Include view file also checking if user already logged in or not
		include Config::VIEWS_PATH."/deviceStatusAll.php";
	}

	
			
}

?>
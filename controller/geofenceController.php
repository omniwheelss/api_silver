<?php

/**
	Includes all other controllers and models
**/
require_once 'includes.php';
require_once 'includesModels.php';

class GeofenceController
{

	private $userService = NULL;
	private $geofenceService = NULL;
	
	/**
		Invoke model to access all the methods
	**/	
	public function __construct()
	{
		$this->userService = new UserService();		
		$this->geofenceService = new GeofenceService();
		$this->dailySummaryController = new DailySummaryController();
		$this->helper = new HelperController();
	}

	
	/**
		check device status
	**/
	public function getGeofenceAlerts1($imei, $fromDate, $toDate, $format) {
			
		$do = isset($_REQUEST['do'])?$_REQUEST['do']:NULL;	
		
		$userAccountID = $dataResult = $message = $status = $datas = null;
		
		if ($imei != NULL) {
			
			$dataResult = $this->geofenceService->getGeofenceAlerts($imei, $fromDate, $toDate);
			$allDataResult = $datas = $dataResult[3];
			print_r($dataResult[3]);
			exit;
			/* foreach($dataResult[3] as $dataResultVal){
				
				$speed = $dataResultVal['speed'];
				$IGN = $dataResultVal['ign'];
				$alertMsgCode = $dataResultVal['alert_msg_code'];
				$dataCurrentEpochTime = $dataResultVal['device_epoch_time'];
				$odometerValue = $dataResultVal['odometer'];
				$PocDiffRecord = 0;

				// Current Status for the raw data
				$dataCurrentStatus = $this->helper->deviceCurrentStatusFromPocket($speed, $IGN, $alertMsgCode);
			} */			
			
			$status = $dataResult[1];

			$message = $dataResult[2];
			
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
		include Config::VIEWS_PATH."/geofenceData.php";
		return array($status, $datas, $message);
	}
	
			
	/**
		check device status
	**/
	public function getGeofenceAlerts($imei, $fromDate, $toDate, $format) {
			
		$do = isset($_REQUEST['do'])?$_REQUEST['do']:NULL;	
		$checkAtleastOneSuccess = false;
		$dataResult = null;
		$datas = null;		
		$dataDailySummaryResult = null;
		
		if ($imei != NULL) {

			//Converting the date as acceptable format
			$dataResult = $this->helper->validateDate($fromDate, $toDate);
			$dateValidateMessage = $dataResult[2];
			
			if($dateValidateMessage == 'success'){
				
				$fromDate = $dataResult[0];
				$toDate = $dataResult[1];

				foreach($datesResult as $datesResultVal){
					
					$datas = null;
					// Getting Geofence data
					$dataResult = $this->geofenceService->getGeofenceAlerts($imei, $datesResultVal, $datesResultVal);
					$datas = $dataResult[2];
					
					# Assing the value when there is no success 
					if(!$checkAtleastOneSuccess){
						$status = $dataResult[0];
						$message = $dataResult[1];
					}
					
					if($status == 'success'){
						// Getting daily summary by date
						$dataDailySummaryResult = $this->dailySummaryController->dailySummaryDetailsByDate($imei, $datesResultVal, $datesResultVal);
						$datas['summary'] = $dataDailySummaryResult[2];
						$checkAtleastOneSuccess = true;
					}				
					$allDataResult[] = $datas;				
				}	
				$status = $dataResult[0];
				$message = $dataResult[1];
				// For returning the function
				$datas = $dataResult[2];
			}
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
		include Config::VIEWS_PATH."/geofenceData.php";
		return array($status, $datas, $message);
	}			
}

?>
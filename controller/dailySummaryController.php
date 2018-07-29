<?php

/**
	Includes all other controllers and models
**/
require_once 'includes.php';
require_once 'includesModels.php';

class DailySummaryController
{

	private $userService = NULL;
	private $geofenceService = NULL;
	
	/**
		Invoke model to access all the methods
	**/	
	public function __construct()
	{
		$this->userService = new UserService();		
		$this->dailySummaryService = new DailySummaryService();
		$this->helper = new HelperController();
	}

	
	/**
		check device status
	**/
	public function dailySummaryDetails($imei, $fromDate, $toDate, $format) {
			
		$do = isset($_REQUEST['do'])?$_REQUEST['do']:NULL;	
		$checkAtleastOneSuccess = false;
		$dataResult = null;
		$datas = null;
		$message = null;
		$status = null;
		$dateValidateMessage = null;
		
		if ($imei != NULL) {

			//Converting the date as acceptable format
			$dataResult = $this->helper->validateDate($fromDate, $toDate);
			$dateValidateMessage = $dataResult[2];
			
			if($dateValidateMessage == 'success'){
				
				$fromDate = $dataResult[0];
				$toDate = $dataResult[1];
				$datesResult = $this->helper->datesBetweenTwoDates($fromDate, $toDate);

				foreach($datesResult as $datesResultVal){

					// Getting daily summary by date
					$dataResult = $this->dailySummaryDetailsByDate($imei, $datesResultVal, $datesResultVal);
					$datas = $dataResult[2];
					
					# Assing the value when there is no success 
					if(!$checkAtleastOneSuccess){
						$status = $dataResult[0];
					}
					
					if($status == 'success'){
						$checkAtleastOneSuccess = true;
					}				
					$allDataResult[] = $datas;				
				}	
				$status = $dataResult[0];
				$message = $dataResult[1];
				// For returning the function
				$datas = $dataResult[2];
			}
			$message = $dateValidateMessage;
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
		check Daily Summary status by date
	**/
	public function dailySummaryDetailsByDate($imei, $fromDate, $toDate){
		
		$dataResult = null;
		$allDataResult = null;
		$datas = null;
		
		//Variable declaration
		$userAccountID = $dataResult = $message = $status = $datas = $speed = $dataPreviousStatus = $dataCurrentStatus = $PocDiffRecord = null;
		$epochDiffBtwPrevAndCurSummaryForMoving = array();
		$epochDiffBtwPrevAndCurSummaryForStopped = array();
		$epochDiffBtwPrevAndCurSummaryForIdle = array();
		$epochDiffBtwPrevAndCurSummaryForUnknown = array();
		$decisionMakerPocketDiffArray = array();
		$makerDecision = null;
		$PocDiffRecord = 0;
		$dataPreviousEpochTime = 0;
		$epochDiffBtwPrevAndCurForMoving = 0;
		$epochDiffBtwPrevAndCurForStopped = 0;
		$epochDiffBtwPrevAndCurForIdle = 0;
		$epochDiffBtwPrevAndCurForUnknown = 0;
		$epochDiffBtwPrevAndCurSummary = array();
		$epochDiffBtwPrevAndCurForDiffSummary = array();
		
		$totalUptime = 0;
		$totalMovingtime = 0;
		$totalStoppedtime = 0;
		$totalIdletime = 0;
		$totalUnknowntime = 0;
		$totalDifftime = 0;
		$totalKMTravelled = 0;


		// Get Summary date from device_data
		$dataResult = $this->dailySummaryService->getDailySummaryDetails($imei, $fromDate, $toDate);
		
		if($dataResult[1] == 'success'){
			
			foreach($dataResult[3] as $dataResultVal){

				$speed = $dataResultVal['speed'];
				$IGN = $dataResultVal['ign'];
				$alertMsgCode = $dataResultVal['alert_msg_code'];
				$dataCurrentEpochTime = $dataResultVal['device_epoch_time'];
				$odometerValue = $dataResultVal['odometer'];
				$PocDiffRecord = 0;

				// Current Status for the raw data
				$dataCurrentStatus = $this->helper->deviceCurrentStatusFromPocket($speed, $IGN, $alertMsgCode);

				// Checking Record is different and assign flag
				if($dataCurrentStatus != $dataPreviousStatus && !empty($dataPreviousStatus)){
					$PocDiffRecord = 1;

					// Different Between Data
					$epochDiffBtwPrevAndCurForDiff = $this->helper->epochDiffBetweenData($dataCurrentEpochTime, $dataPreviousEpochTime, $dataCurrentStatus);
					$epochDiffBtwPrevAndCurForDiffSummary[] = $epochDiffBtwPrevAndCurForDiff;
					
					// Decision Maker
					$decisionMakerPocketDiffArray = $this->helper->decisionMakerPocketDiff($dataCurrentStatus, $dataPreviousStatus, $epochDiffBtwPrevAndCurForDiff);
					$makerDecision = $decisionMakerPocketDiffArray[4];
					
					//Push Difference Pocket into respective status
					if($makerDecision == 'Moving'){
						array_push($epochDiffBtwPrevAndCurSummaryForMoving, $decisionMakerPocketDiffArray[0]);
					}
					else if($makerDecision == 'Stopped'){
						array_push($epochDiffBtwPrevAndCurSummaryForStopped, $decisionMakerPocketDiffArray[1]);
					}
					else if($makerDecision == 'Idle'){
						array_push($epochDiffBtwPrevAndCurSummaryForIdle, $decisionMakerPocketDiffArray[2]);
					}
					else if($makerDecision == 'Unknown'){
						array_push($epochDiffBtwPrevAndCurSummaryForUnknown, $decisionMakerPocketDiffArray[3]);
					}						
				} 
				
				// For the sequence Pocket with same status
				if($dataPreviousEpochTime != 0 && $PocDiffRecord == 0)
				{
					// Different Between Data
					$epochDiffBtwPrevAndCur = $this->helper->epochDiffBetweenData($dataCurrentEpochTime, $dataPreviousEpochTime, $dataCurrentStatus);
					$epochDiffBtwPrevAndCurSummary[] = $epochDiffBtwPrevAndCur;
					
					// Different Between Data for Moving
					if($dataCurrentStatus == 'Moving'){
						$epochDiffBtwPrevAndCurForMoving = $this->helper->epochDiffBetweenData($dataCurrentEpochTime, $dataPreviousEpochTime, $dataCurrentStatus);
						$epochDiffBtwPrevAndCurSummaryForMoving[] = $epochDiffBtwPrevAndCurForMoving;
					}
					// Different Between Data for Stopped
					else if($dataCurrentStatus == 'Stopped'){
						$epochDiffBtwPrevAndCurForStopped = $this->helper->epochDiffBetweenData($dataCurrentEpochTime, $dataPreviousEpochTime, $dataCurrentStatus);
						$epochDiffBtwPrevAndCurSummaryForStopped[] = $epochDiffBtwPrevAndCurForStopped;
					}
					// Different Between Data for Idle
					else if($dataCurrentStatus == 'Idle'){
						$epochDiffBtwPrevAndCurForIdle = $this->helper->epochDiffBetweenData($dataCurrentEpochTime, $dataPreviousEpochTime, $dataCurrentStatus);
						$epochDiffBtwPrevAndCurSummaryForIdle[] = $epochDiffBtwPrevAndCurForIdle;
					}
					// Different Between Data for Unknown
					else if($dataCurrentStatus == 'Unknown'){
						$epochDiffBtwPrevAndCurForUnknown = $this->helper->epochDiffBetweenData($dataCurrentEpochTime, $dataPreviousEpochTime, $dataCurrentStatus);
						$epochDiffBtwPrevAndCurSummaryForUnknown[] = $epochDiffBtwPrevAndCurForUnknown;
					}
				}
				
				// Calculation for Total KM Travelled		
				if(!empty($odometerPreviousValue))
					$totalKMTravelled+= $odometerValue - $odometerPreviousValue;
				
				// Assigning Previous values		
				$dataPreviousStatus = $dataCurrentStatus;
				$dataPreviousEpochTime = $dataCurrentEpochTime;
				$odometerPreviousValue = $odometerValue;
			}
			
			// Result with Time format
			$totalUptime = $this->helper->epochToTime(array_sum($epochDiffBtwPrevAndCurSummary) + array_sum($epochDiffBtwPrevAndCurForDiffSummary));
			$totalMovingtime = $this->helper->epochToTime(array_sum($epochDiffBtwPrevAndCurSummaryForMoving));
			$totalStoppedtime = $this->helper->epochToTime(array_sum($epochDiffBtwPrevAndCurSummaryForStopped));
			$totalIdletime = $this->helper->epochToTime(array_sum($epochDiffBtwPrevAndCurSummaryForIdle));
			$totalUnknowntime = $this->helper->epochToTime(array_sum($epochDiffBtwPrevAndCurSummaryForUnknown));
			$totalDifftime = $this->helper->epochToTime(array_sum($epochDiffBtwPrevAndCurForDiffSummary));

			// Result with epoch time 
			$totalUpEpoch = array_sum($epochDiffBtwPrevAndCurSummary) + array_sum($epochDiffBtwPrevAndCurForDiffSummary);
			$totalMovingEpoch = array_sum($epochDiffBtwPrevAndCurSummaryForMoving);
			$totalStoppedEpoch = array_sum($epochDiffBtwPrevAndCurSummaryForStopped);
			$totalIdleEpoch = array_sum($epochDiffBtwPrevAndCurSummaryForIdle);
			$totalUnknownEpoch = array_sum($epochDiffBtwPrevAndCurSummaryForUnknown);
			$totalDiffEpoch = array_sum($epochDiffBtwPrevAndCurForDiffSummary);

			$datas = array(
				'generationDate' => $fromDate,
				'totalUptime' => $totalUptime,
				'totalMovingtime' => $totalMovingtime,
				'totalStoppedtime' => $totalStoppedtime,
				'totalIdletime' => $totalIdletime,
				'totalUnknowntime' => $totalUnknowntime,
				'totalDifftime' => $totalDifftime,
				
				'totalUpEpoch' => $totalUpEpoch,
				'totalMovingEpoch' => $totalMovingEpoch,
				'totalStoppedEpoch' => $totalStoppedEpoch,
				'totalIdleEpoch' => $totalIdleEpoch,
				'totalUnknownEpoch' => $totalUnknownEpoch,
				'totalDiffEpoch' => $totalDiffEpoch,
				'totalKMTravelled' => $totalKMTravelled
				
			);
			$status = $dataResult[1];
			$message = $dataResult[2];
			$allDataResult = $datas;
		}
		return array($status, $message, $allDataResult);
	}
}

?>
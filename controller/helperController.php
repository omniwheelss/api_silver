<?php

/**
	Includes all other controllers and models
**/
require_once 'includes.php';
require_once 'includesModels.php';

class HelperController
{

	private $commonService = NULL;	
	
	/**
		__construct function
	**/
	public function __construct()
	{
		$this->commonService = new CommonService();
	}
	
		
	/**
		data logger
	**/
	public function dataLoggerFileAndDB($fileName, $do, $userAccountID, $logData, $logDataStatus)
	{
		try {
			
			$logData = "|".$fileName."|".$do."|".$this->getClientIP()."|".$userAccountID."|".$logData."|";
			($logDataStatus == 'error')?$logPrefix = Config::ERROR_LOG : $logPrefix = Config::DAILY_LOG;
			$this->dataLogger($logData, Config::LOGS_PATH, $logPrefix,"");
			$this->commonService->dataLoggerDB($userAccountID, $logData, date("Y-m-d H:i:s"));			
		}
		catch (PDOException $e) {
			$logDataStatus =  'error';
			$logData = "|insert logs exception|".$e->getMessage()."".CONFIG::NEWLINE_ERROR."|";
		}
		return array($userAccountID, $logData, $logDataStatus);
	}
	


	/**
		logger to store log information for all the store
	**/	
	static function dataLogger($data, $path, $logPrefix, $extraData) {
	
		if (empty($logPrefix))
			$logPrefix = "";
		else
			$logPrefix = $logPrefix."_";

		$filePath = $path."/".$logPrefix."".@date("dmY").".log";
		$handle = fopen($filePath, 'a+');
		chmod($filePath, 0777);
		shell_exec("sudo chmod 777 ".$filePath."");
		$logFileRead = @file($filePath);
		$logFileReadCount = count($logFileRead);
		if (($logFileReadCount%2) == 0)
				$logFileCount = ($logFileReadCount/2)+1;

		$finalData = "".@date("d-m-Y H:i:s")." ".$data."".$extraData."#";
		if(!fwrite($handle, "".$finalData."".CONFIG::NEWLINE."")) die("couldn't write to file. : Check the Folder permisson for (".$filePath.")");
		else
			return "<div id='error_text'><div class='Db_Error'>".$logPrefix." written done.</div></div>";
	}
	

			
	/**
		get ip address for the user
	**/	
	public function getClientIP()
	{
		$ipAddress = '';
		if (getenv('HTTP_CLIENT_IP'))
			$ipAddress = getenv('HTTP_CLIENT_IP');
		else if (getenv('HTTP_X_FORWARDED_FOR'))
			$ipAddress = getenv('HTTP_X_FORWARDED_FOR');
		else if (getenv('HTTP_X_FORWARDED'))
			$ipAddress = getenv('HTTP_X_FORWARDED');
		else if (getenv('HTTP_FORWARDED_FOR'))
			$ipAddress = getenv('HTTP_FORWARDED_FOR');
		else if (getenv('HTTP_FORWARDED'))
		   $ipAddress = getenv('HTTP_FORWARDED');
		else if (getenv('REMOTE_ADDR'))
			$ipAddress = getenv('REMOTE_ADDR');
		else
			$ipAddress = 'UNKNOWN';
			
		return $ipAddress;
	}


	/**
		redirection of the page
	**/	
	public function redirect($url)
	{
		header("Location: ".$url);
		exit;
	}
		
		
	/**	
		Redirects to views as per the input parameter
	**/
	public function gotoView($do)
	{
		include Config::VIEWS_PATH."/500.php";
		exit;
	}
			
			
	/**	
		Redirects to page as per the input parameter
	**/
	
	public function goto_page($do, $message)
	{
		$filename = "/".$do.".php";
		include Config::VIEWS_PATH."".$filename;
		exit;
	}

	/**
		Epoch Time Difference
	**/
	function epochDiff($Date_Stamp){
		
		$Output= null;
		$Date_Stamp1_Epoch = strtotime($Date_Stamp);
		$Date_Stamp2_Epoch = time();
		$Output = $Date_Stamp2_Epoch - $Date_Stamp1_Epoch;
		return $Output;
	}	

	/**	
		Epoch To Time
	**/	
	
	function epochToTime($epochTime)
	{
		$time = $epochTime;
		$preMin = $preDay = $preHour = $timeShift = $pmin = 0;

		if($time>=0 && $time<=59) {
			// Seconds
			if($preMin[0] > 0 || $time > 0)
			$timeShift = $preDay[0].' : '.$preHour[0].' : '.$preMin[0].' min '.$time.' sec ';
		}
		elseif($time>=60 && $time<=3599) {
			// Minutes + Seconds
			$pmin = $time / 60;
			$preMin = explode('.', $pmin);

			$presec = $pmin-$preMin[0];
			$sec = $presec*60;

			$timeShift = $preMin[0].' min '.round($sec,0).' sec ';
		}
		elseif($time>=3600 && $time<=86399) {
			// Hours + Minutes
			$phour = $time / 3600;
			$preHour = explode('.',$phour);

			$preMin = $phour-$preHour[0];
			$min = explode('.',$preMin*60);

			$presec = '0.'.$min[0];
			$sec = $presec*60;

			$timeShift = $preHour[0].' hrs '.$min[0].' min '.round($sec,0).' sec ';
		}
		elseif($time>=86400) {
			// Days + Hours + Minutes
			$pday = $time / 86400;
			$preDay = explode('.',$pday);

			$phour = $pday-$preDay[0];
			$preHour = explode('.',$phour*24);

			$preMin = ($phour*24)-$preHour[0];

			$min = explode('.',$preMin*60);

			$presec = '0.'.$min[1];
			$sec = $presec*60;

			$timeShift = $preDay[0].' days '.$preHour[0].' hrs '.$min[0].' min '.round($sec,0).' sec ';
		}
		return $timeShift;
	}	


	/**	
		Device status from the pocket
	**/	

	function deviceCurrentStatusFromPocket($speed, $IGN, $alertMsgCode){
		$alertMsgCode = explode("|",$alertMsgCode);

		$Result = null;
		// Moving Status
		if($speed > 10 && $IGN == 1){
			$status = "Moving";
		}
		// Stopped Status
		else if($speed == 0 && $IGN == 0){
			$status = "Stopped";
		}
		// Idle Status
		else if(($speed <= 10 && $IGN == 1) || $alertMsgCode[0] == 'VI'){
			$status = "Idle";
		}
		return $Result = $status;
	}
	

	/**	
		Epoch Difference between data
	**/	

	function epochDiffBetweenData($dataCurrentEpochTime, $dataPreviousEpochTime, $dataCurrentStatus){
		
		$epochResult = $dataCurrentEpochTime - $dataPreviousEpochTime;
		
		if($dataCurrentStatus == 'Moving' || $dataCurrentStatus == 'Idle' ||  $dataCurrentStatus == 'Unknown'){
			if($epochResult > 120)
				$epochResult = 60;
		}
		else if($dataCurrentStatus == 'Stopped'){
			if($epochResult > 600)
				$epochResult = 300;
		}
		return $epochResult;			
	}

	
	/**	
		Decision maker for the pocket difference with different status
	**/	
	function decisionMakerPocketDiff($dataCurrentStatus, $dataPreviousStatus, $epochDiffBtwPrevAndCurForDiff){
		
		$makerDecisionResult = null;
		$movingAdditionalDiff = 0;
		$idleAdditionalDiff = 0;
		$stoppedAdditionalDiff = 0;
		$unknownAdditionalDiff = 0;
		
		// For Moving
		if($dataPreviousStatus == 'Moving' && $dataCurrentStatus == 'Idle' && $epochDiffBtwPrevAndCurForDiff <= 60){
			$movingAdditionalDiff = $epochDiffBtwPrevAndCurForDiff;
			$makerDecision = "Moving";
		}
		else if($dataPreviousStatus == 'Moving' && $dataCurrentStatus == 'Idle' && $epochDiffBtwPrevAndCurForDiff > 60){
			$idleAdditionalDiff = $epochDiffBtwPrevAndCurForDiff;
			$makerDecision = "Idle";
		}
		else if($dataPreviousStatus == 'Moving' && $dataCurrentStatus == 'Stopped' && $epochDiffBtwPrevAndCurForDiff <= 60){
			$stoppedAdditionalDiff = $epochDiffBtwPrevAndCurForDiff;
			$makerDecision = "Stopped";
		}
		else if($dataPreviousStatus == 'Moving' && $dataCurrentStatus == 'Stopped' && $epochDiffBtwPrevAndCurForDiff > 60){
			$stoppedAdditionalDiff = $epochDiffBtwPrevAndCurForDiff;
			$makerDecision = "Stopped";
		}
		
		// For Stopped
		else if($dataPreviousStatus == 'Stopped' && $dataCurrentStatus == 'Moving' && $epochDiffBtwPrevAndCurForDiff <= 60){
			$movingAdditionalDiff = $epochDiffBtwPrevAndCurForDiff;
			$makerDecision = "Moving";
		}
		else if($dataPreviousStatus == 'Stopped' && $dataCurrentStatus == 'Moving' && $epochDiffBtwPrevAndCurForDiff > 60){
			$stoppedAdditionalDiff = $epochDiffBtwPrevAndCurForDiff;
			$makerDecision = "Stopped";
		}
		else if($dataPreviousStatus == 'Stopped' && $dataCurrentStatus == 'Idle' && $epochDiffBtwPrevAndCurForDiff <= 60){
			$idleAdditionalDiff = $epochDiffBtwPrevAndCurForDiff;
			$makerDecision = "Idle";
		}
		else if($dataPreviousStatus == 'Stopped' && $dataCurrentStatus == 'Idle' && $epochDiffBtwPrevAndCurForDiff > 60){
			$stoppedAdditionalDiff = $epochDiffBtwPrevAndCurForDiff;
			$makerDecision = "Stopped";
		}
		
		// For Idle
		else if($dataPreviousStatus == 'Idle' && $dataCurrentStatus == 'Moving' && $epochDiffBtwPrevAndCurForDiff <= 60){
			$movingAdditionalDiff = $epochDiffBtwPrevAndCurForDiff;
			$makerDecision = "Moving";
		}
		else if($dataPreviousStatus == 'Idle' && $dataCurrentStatus == 'Moving' && $epochDiffBtwPrevAndCurForDiff > 60){
			$idleAdditionalDiff = $epochDiffBtwPrevAndCurForDiff;
			$makerDecision = "Idle";
		}
		else if($dataPreviousStatus == 'Idle' && $dataCurrentStatus == 'Stopped' && $epochDiffBtwPrevAndCurForDiff <= 60){
			$idleAdditionalDiff = $epochDiffBtwPrevAndCurForDiff;
			$makerDecision = "Idle";
		}
		else if($dataPreviousStatus == 'Idle' && $dataCurrentStatus == 'Stopped' && $epochDiffBtwPrevAndCurForDiff > 60){
			$stoppedAdditionalDiff = $epochDiffBtwPrevAndCurForDiff;
			$makerDecision = "Stopped";
		}
		else{
			$unknownAdditionalDiff = $epochDiffBtwPrevAndCurForDiff;
			$makerDecision = "Unknown";
		}
		$makerDecisionResult = array($movingAdditionalDiff, $stoppedAdditionalDiff, $idleAdditionalDiff, $unknownAdditionalDiff, $makerDecision);
		
		return $makerDecisionResult;

	}	
	
	/**	
		Dates between two dates
	**/	
	function datesBetweenTwoDates($fromDate, $toDate) {

		$datesResult = array();
		$step = '+1 day';
		$outputFormat = 'Y-m-d h:i:s';
		$fromDateEpoch = strtotime($fromDate);
		$toDateEpoch = strtotime($toDate);

		while( $fromDateEpoch <= $toDateEpoch ) {

			$datesResult[] = date($outputFormat, $fromDateEpoch);
			$fromDateEpoch = strtotime($step, $fromDateEpoch);
		}
		return $datesResult;
	}	
	
	
	/**	
		Validate dates
	**/	
	function validateDate($fromDate, $toDate) {

		$datesResult = 0;
		$step = '+1 day';
		$outputFormat = 'Y-m-d';
		$message = null;
		
		$fromDateLength = strlen($fromDate);
		$toDateLength = strlen($toDate);
		if($fromDateLength >= 10 && $toDateLength >= 10){
			$fromDate = date($fromDateLength == 10? "Y-m-d" : "Y-m-d h:i:s", strtotime(str_replace("/", "-", $fromDate)));
			$toDate = date($toDateLength == 10? "Y-m-d" : "Y-m-d h:i:s", strtotime(str_replace("/", "-", $toDate)));		
			
			if(strlen(strstr($fromDate, "1970-01-01")) == 0 && strlen(strstr($toDate, "1970-01-01")) == 0){
				$d = DateTime::createFromFormat($outputFormat, $fromDate);
				//The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
				$datesResult = $d && $d->format($outputFormat) === $fromDate;
				if($datesResult == 1){
					$fromDate = date("Y-m-d", strtotime($fromDate));
					$fromDate = $fromDate. " 00:00:00";
					$toDate = date("Y-m-d", strtotime($toDate));
					$toDate = $toDate. " 23:59:59";
					$message = "success";
				}
			}
			else{
				$fromDate = null;
				$toDate = null;
				$message = "either one of the date value was wrong";
			}
		}
		return array($fromDate, $toDate, $message);
	}		

}

?>
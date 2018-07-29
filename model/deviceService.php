<?php

/**
	Includes all other controllers and models
**/
require_once 'controller/includes.php';

class DeviceService
{	
	private $conn;
	
	public function __construct()
	{
		$database = new Database();
		$db = $database->dbConnection();
		$this->conn = $db;
	}
	
	/**
		device status
	**/	
	public function deviceCurrentStatusForSingle($imei)
	{
		# Variable declaration
		$message = $dataStatus = $data = null;
		
		try {
			$stmt = $this->conn->prepare("CALL selDeviceCurrentStatusByIMEI(?)");
			$stmt->bindParam(1,$imei, PDO::PARAM_STR);
			$stmt->execute();
			if($stmt->rowCount() == 1) {
				$resultRow = $stmt->fetch(PDO::FETCH_ASSOC);
				$data = $resultRow;
				$message = "device data fetched";
				$dataStatus = 'success';
			}
			else {
				$message = "no data available for the imei ".$imei."";
				$dataStatus = 'failure';
			}
		}
		catch (PDOException $e) {
			$dataStatus =  'error';
			$logData = "|device status exception|".$e->getMessage()."".CONFIG::NEWLINE_ERROR."|";
		}
		return array($imei, $dataStatus, $message, $data);
	}	
	

	
	/**
		device list by account ID 
	**/	
	public function deviceListByAccountID($aid)
	{
		# Variable declaration
		$message = $dataStatus = $data = null;		
		$userAccountID = $aid;
		
		try {
			$stmt = $this->conn->prepare("CALL selDeviceDetailsByAccountID(?)");
			$stmt->bindParam(1,$userAccountID, PDO::PARAM_INT);
			$stmt->execute();
			if($stmt->rowCount() > 0) {

				while($resultRow = $stmt->fetch(PDO::FETCH_ASSOC)){
					$data[] = $resultRow;
				}
				
				$message = 'devices list fetched';	
				$dataStatus = 'success';
			}
			else {
				$message = "no data available for the account id ".$aid."";
				$dataStatus = 'failure';	
			}
		}
		catch (PDOException $e) {
			$dataStatus =  'error';
			$message = "|device list exception|".$e->getMessage()."".CONFIG::NEWLINE_ERROR."|";
		}
		return array($userAccountID, $dataStatus, $message, $data);
	}

	
	
	/**
		Get Device Summary
	**/		
	function deviceDailySummaryForSingle($imei, $fromDate, $fromDate){
		
		$Result = null;
		$fromDate = $fromDate. " 00:00:00";
		$toDate = $fromDate. " 23:59:59";

		$Mysql_Query = "select * from device_data where imei = '".$imei."' and device_date_stamp between '".$fromDate."' and '".$toDate."' and alert_msg_code != 'IN|0' order by device_date_stamp asc";
		$Mysql_Query_Result = mysql_query($Mysql_Query) or die(mysql_error());
		$Row_Count = mysql_num_rows($Mysql_Query_Result);
		if($Row_Count >=1){
			$i = 1;
			$Decision_Maker_All_Diff = array();
			$Decision_Maker_Moving_Diff = array();
			$Decision_Maker_Stopped_Diff = array();
			$Decision_Maker_Idle_Diff = array();
			$Decision_Maker_Unknown_Diff = array();
			
			while($Result_Array = mysql_fetch_array($Mysql_Query_Result)){
				
				$Diff_Record = 0;
				$Speed_Array[] = $Result_Array['speed'];
				$Device_Stamp_All_Array[] = $Result_Array['device_date_stamp'];
				$GPS_Move_Status = $Result_Array['gps_move_status'];
				$IGN = $Result_Array['ign'];
				$Speed = $Result_Array['speed'];
				$Alert_Msg_Code = $Result_Array['alert_msg_code'];
				
				// Current Status Check
				$Data_Cur_Status = Data_Current_Status($GPS_Move_Status, $Speed, $IGN, $Alert_Msg_Code);
				$Data_Cur_Status_Val = $Data_Cur_Status[0];
				$Data_Pre_Status_Val = $Data_Pre_Array[0];
				
				// Checking Record is different and assign flag
				if($Data_Pre_Status_Val != $Data_Cur_Status_Val && !empty($Data_Pre_Status_Val)){
					$Diff_Record = 1;
				}
				
				$Pre_Cur_Diff_Array = array($Data_Pre_Array[1], $Result_Array['device_epoch_time']);
				// Calucalte only equal record - not diff record
				if($Diff_Record == 0){
					// All data sequence
					$Pre_Cur_Diff_Val = Diff_Between_Records('epoch', $Pre_Cur_Diff_Array, $Data_Pre_Status_Val, $Data_Cur_Status_Val, $Diff_Record);
					$Pre_Cur_Diff_Sum = array_sum($Pre_Cur_Diff_Val);
					$All_DateTime_Diff[] = $Pre_Cur_Diff_Sum;

					// Data by status
					// Moving
					if($Data_Cur_Status_Val  == 'Moving'){
						$Device_Stamp_Moving_Array[] = $Result_Array['device_epoch_time'];
						$Result_Array['device_date_stamp'] = "Moving--".$Result_Array['device_date_stamp'];
						$Pre_Cur_Diff_Moving_Val = Diff_Between_Records('epoch', $Pre_Cur_Diff_Array, $Data_Pre_Status_Val, $Data_Cur_Status_Val, $Diff_Record);
						$Pre_Cur_Diff_Moving_Sum = array_sum($Pre_Cur_Diff_Moving_Val);
						$DateTime_Moving_Diff[] = $Pre_Cur_Diff_Moving_Sum;

					}
					//Stopped
					else if($Data_Cur_Status_Val == 'Stopped'){
						$Device_Stamp_Stopped_Array[] = $Result_Array['device_epoch_time'];
						$Result_Array['device_date_stamp'] = "Stopped--".$Result_Array['device_date_stamp'];
						$Pre_Cur_Diff_Stopped_Val = Diff_Between_Records('epoch', $Pre_Cur_Diff_Array, $Data_Pre_Status_Val, $Data_Cur_Status_Val, $Diff_Record);
						$Pre_Cur_Diff_Stopped_Sum = array_sum($Pre_Cur_Diff_Stopped_Val);
						$DateTime_Stopped_Diff[] = $Pre_Cur_Diff_Stopped_Sum;
					}
					//Idle
					else if($Data_Cur_Status_Val == 'Idle'){
						$Device_Stamp_Idle_Array[] = $Result_Array['device_epoch_time'];
						$Result_Array['device_date_stamp'] = "Idle--".$Result_Array['device_date_stamp'];
						$Pre_Cur_Diff_Idle_Val = Diff_Between_Records('epoch', $Pre_Cur_Diff_Array, $Data_Pre_Status_Val, $Data_Cur_Status_Val, $Diff_Record);
						$Pre_Cur_Diff_Idle_Sum = array_sum($Pre_Cur_Diff_Idle_Val);
						$DateTime_Idle_Diff[] = $Pre_Cur_Diff_Idle_Sum;
					}
					//Unknown
					else{
						$Device_Stamp_Unknown_Array[] = $Result_Array['device_epoch_time'];
						$Result_Array['device_date_stamp'] = "Unknown--".$Result_Array['device_date_stamp'];
						$Pre_Cur_Diff_Unknown_Val = Diff_Between_Records('epoch', $Pre_Cur_Diff_Array, $Data_Pre_Status_Val, $Data_Cur_Status_Val, $Diff_Record);
						$Pre_Cur_Diff_Unknown_Sum = array_sum($Pre_Cur_Diff_Unknown_Val);
						$DateTime_Unknown_Diff[] = $Pre_Cur_Diff_Unknown_Sum;
					}
					
				}
				else if($Diff_Record == 1){
					// All data diff
					$Pre_Cur_Diff_Val = Diff_Between_Records('epoch', $Pre_Cur_Diff_Array, $Data_Pre_Status_Val, $Data_Cur_Status_Val, $Diff_Record);
					$Pre_Cur_Diff_Sum = array_sum($Pre_Cur_Diff_Val);
					$All_DateTime_NE_Diff[] = $Pre_Cur_Diff_Sum;

					// Decide whom to assign the difference 		
					$Decision_Maker_Pocket_Diff = Decision_Maker_Pocket_Diff($Data_Pre_Status_Val, $Data_Cur_Status_Val, $Pre_Cur_Diff_Sum);
					$Maker_Decision = $Decision_Maker_Pocket_Diff[4];

					if($Maker_Decision == 'Moving'){
						array_push($Decision_Maker_Moving_Diff, $Decision_Maker_Pocket_Diff[0]);
					}
					else if($Maker_Decision == 'Stopped'){
						array_push($Decision_Maker_Stopped_Diff, $Decision_Maker_Pocket_Diff[1]);
					}
					else if($Maker_Decision == 'Idle'){
						array_push($Decision_Maker_Idle_Diff, $Decision_Maker_Pocket_Diff[2]);
					}
					else if($Maker_Decision == 'Unknown'){
						array_push($Decision_Maker_Unknown_Diff, $Decision_Maker_Pocket_Diff[3]);
					}
					
					// Just for debug 
					// Moving
					if($Data_Cur_Status_Val  == 'Moving'){
						$Result_Array['device_date_stamp'] = "Moving--".$Result_Array['device_date_stamp'];
					}
					//Stopped
					else if($Data_Cur_Status_Val == 'Stopped'){
						$Result_Array['device_date_stamp'] = "Stopped--".$Result_Array['device_date_stamp'];
					}
					//Idle
					else if($Data_Cur_Status_Val == 'Idle'){
						$Result_Array['device_date_stamp'] = "Idle--".$Result_Array['device_date_stamp'];
					}
					//Unknown
					else{
						$Result_Array['device_date_stamp'] = "Unknown--".$Result_Array['device_date_stamp'];
					}
				}

				// Calculation for Total KM Travelled		
				$Total_KM_Value+= Diff_Between_Odameter($KM_Pre_Value, $Result_Array['odometer']);
				
				// Assigning the previous value
				$Data_Pre_Array = array($Data_Cur_Status_Val, $Result_Array['device_epoch_time']);
				$KM_Pre_Value = $Result_Array['odometer'];
				
				// For debug only
				//echo $i."-----".$Result_Array['device_date_stamp']."<br />";
				
				$i++;
			}
		}	    
		
		$Final_Result = Add_Vehicle_Status_Diff_AddDiff($Speed_Array, $All_DateTime_Diff, $All_DateTime_NE_Diff, $DateTime_Moving_Diff, $DateTime_Stopped_Diff, $DateTime_Idle_Diff, $DateTime_Unknown_Diff, $Decision_Maker_Moving_Diff, $Decision_Maker_Stopped_Diff, $Decision_Maker_Idle_Diff, $Decision_Maker_Unknown_Diff,$Total_KM_Value);
		
		return $Final_Result;
	}	
	

}
?>
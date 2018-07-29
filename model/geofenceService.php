<?php
/**
	Includes all other controllers and models
**/
require_once 'controller/includes.php';

class GeofenceService
{	
	private $conn;
	
	public function __construct()
	{
		$database = new Database();
		$db = $database->dbConnection();
		$this->conn = $db;
	}
	
	
	/**
		geofence alerts
	**/
	public function getGeofenceAlerts($imei, $fromDate, $toDate)
	{
		# Variable declaration
		$message = $dataStatus = $data = null;

		try {
			$stmt = $this->conn->prepare("CALL spSelGeofenceAlerts(?,?,?)");
			$stmt->bindParam(1,$imei, PDO::PARAM_INT);
			$stmt->bindParam(2,$fromDate, PDO::PARAM_STR);
			$stmt->bindParam(3,$toDate, PDO::PARAM_STR);
			$stmt->execute();
			if($stmt->rowCount() > 0) {

				while($resultRow = $stmt->fetch(PDO::FETCH_ASSOC)){
					$data[] = $resultRow;
				}
				$message = 'devices list fetched';	
				$dataStatus = 'success';
			}
			else {
				$message = "no data available for the imei : ".$imei."";
				$dataStatus = 'failure';	
			}
		}
		catch (PDOException $e) {
			$dataStatus =  'error';
			$message = "|device list exception|".$e->getMessage()."".CONFIG::NEWLINE_ERROR."|";
		}
		return array($dataStatus, $message, $data);
	}
			
}
?>
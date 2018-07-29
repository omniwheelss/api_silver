<?php
/**
	Includes all other controllers and models
**/
require_once 'controller/includes.php';

class DailySummaryService
{	
	private $conn;
	
	public function __construct()
	{
		$database = new Database();
		$db = $database->dbConnection();
		$this->conn = $db;
	}
	
	
	/**
		Daily Summary Details
	**/
	public function getDailySummaryDetails($imei, $fromDate, $toDate)
	{
		# Variable declaration
		$message = $dataStatus = $data = null;
		
		try {
			$stmt = $this->conn->prepare("CALL spSelDailySummaryDetailsByDateAndIMEI(?,?,?)");
			$stmt->bindParam(1,$imei, PDO::PARAM_INT);
			$stmt->bindParam(2,$fromDate, PDO::PARAM_STR);
			$stmt->bindParam(3,$toDate, PDO::PARAM_STR);
			$stmt->execute();
			if($stmt->rowCount() > 0) {

				while($resultRow = $stmt->fetch(PDO::FETCH_ASSOC)){
					$data[] = $resultRow;
				}
				$message = 'daily summary fetched';	
				$dataStatus = 'success';
			}
			else {
				$message = "no data available for the imei : ".$imei."";
				$dataStatus = 'failure';	
			}
		}
		catch (PDOException $e) {
			$dataStatus =  'error';
			$message = "|daily summary list exception|".$e->getMessage()."".CONFIG::NEWLINE_ERROR."|";
		}
		return array($imei, $dataStatus, $message, $data);
	}
	
}
?>
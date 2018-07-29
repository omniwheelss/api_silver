<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Methods: 'GET,PUT,POST,DELETE'");


	if(empty($message))
		$message = null; 
	
	if(empty($status))
		$status = null; 
	
	if(empty($allDataResult))
		$allDataResult = null; 
	
	if(empty($response))
		$response = null; 

    $response = array(
        "status" => $status,
		"data" => $allDataResult,
		"msg" => $message
    );

// for Json format
if($format == 'json'){
	echo json_encode($response);//,JSON_PRETTY_PRINT
}
?>
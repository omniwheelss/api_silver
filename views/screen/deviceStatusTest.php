<?php
header('Access-Control-Allow-Origin: *'); 
header("Content-Type: application/json");

	if(empty($message))
		$message = null; 
	
	if(empty($status))
		$status = null; 
	
	if(empty($datas))
		$datas = null; 
	
	if(empty($response))
		$response = null; 

	print_r($_REQUEST);
	exit;
	$response = array(
		'status' => $status,
		'data' => $datas,
		'msg' => $message
	);

	
	// for Json format
	if($format == 'json'){
		echo json_encode($response);
		
	}
?>
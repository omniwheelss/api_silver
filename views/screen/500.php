<?php
header('Access-Control-Allow-Origin: *'); 
header("Content-Type: application/json");

	$message = "Internal Server Error. We track these errors automatically, but if the problem persists feel free to contact us. In the meantime, try refreshing. <a href='#'>admin@site.com</a>";

	if(empty($message))
		$message = null; 
	
	if(empty($status))
		$status = null; 
	
	if(empty($datas))
		$datas = null; 
	
	if(empty($response))
		$response = null; 

    $response = array ("container" => array(
        'status' => $status,
		'data' => $datas,
		'msg' => $message
    ));

echo json_encode($response);
?>

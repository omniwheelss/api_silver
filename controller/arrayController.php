<?php

require_once 'model/userService.php';

class ArrayController
{

	private $UserService = NULL;
	
	//Invoke model to access all the methods
	public function __construct()
	{
		$this->UserService = new UserService();
	}

	/**		
		Arrays to use for all the pages
		Created : 20/10/2016
		Modified : 17/11/2016
		
	**/	
	public function array_func($do) {
		
		$general_array[] = null;
		// Example data to pull the data from database
		switch ($do) {
			
			case 'user_type':
				$general_array = $this->UserService->array_func_db('id','user_type','user_type','');
			break; 
			
			case 'user_wol':
				$general_array = $this->UserService->array_func_db("user_account_id","concat(firstname,' ',lastname)","vw_users", "account_status ='active' and user_type_id is null ");
			break; 
			
			case 'users':
				$general_array = $this->UserService->array_func_db("user_account_id","concat(firstname,' ',lastname)","vw_users", "");
			break; 
		
			case 'user_expiry':
				$general_array = $this->UserService->array_func_db("user_account_id","id","user_expiry", "status = 'active' order by id desc limit 1");
			break; 
		
			case 'gender_array':
				$general_array = array( 
					'Male' => 'Male',
					'Female' => 'Female'
				);
			case 'status_array':
				$general_array = array (
				
					'inactive' => 'Inactive',
					'active' => 'Active',
					'pending' => 'Pending',
					'deleted' => 'Deleted'
					
				);				
			break;	
		
		}	
		return $general_array;		
	}
	
	
	/**	
	
		Status column
		Created : 17/09/2016
		Modified : 06/02/2018
		
	**/
	
	public function status_lable_array($do)
	{
		$status_lable_result = null;
		
		$status_lable_array = array (
		
			'pending' => 'warning',
			'active' => 'success',
			'inactive' => 'danger',
			'deleted' => 'danger'
			
		);
		if(array_key_exists($do,$status_lable_array)){
			$status_lable_result = $status_lable_array[$do];
		}	
		
		return $status_lable_result;
	}

	
		
}

?>
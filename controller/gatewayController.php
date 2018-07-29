<?php

/**
	Includes all other controllers and models
**/
require_once 'includes.php';


class GatewayController
{
	private static $common;
	public $do;
	

	/**
		__construct function
	**/
	public function __construct()
	{
		$this->user = new UserController();
		$this->device = new DeviceController();
		$this->geofence = new GeofenceController();
		$this->dailySummary = new DailySummaryController();
		$this->helper = new HelperController();
		
		$this->do = isset($_REQUEST['do'])?$_REQUEST['do']:NULL;		
		$this->format =  isset($_REQUEST['format'])?$_REQUEST['format']:'json';
		$this->method = $_SERVER['REQUEST_METHOD'];
	}
	
	/**
		function to get the login & SEO values after setting it
	**/	
	public function getInstance()
	{
		if (!isset(self::$common))
		{
			$class = __CLASS__;
			self::$common = new $class();
		}
		return self::$common;
	}

	
		
	/**
		function to check query string to handle the page direction
	**/
	public function handleRequest()
	{
		# Getting the values from getInstance
		$common = GatewayController::getInstance();
		
		# Redirection for all the pages
		switch ($common->do) {
			
			/*
				User Services
			*/
			case 'auth':
				$this->user->doLogin($this->method, $this->format);
			break;

			case 'usersScreen':
				$aid =  isset($_REQUEST['aid'])?$_REQUEST['aid']:NULL;
				$this->user->usersScreen($aid, $this->format);
			break;


			/*
				Device Services
			*/
			case 'deviceCurrentStatusForSingle':
				$imei =  isset($_REQUEST['imei'])?$_REQUEST['imei']:NULL;
				$this->device->deviceCurrentStatusForSingle($imei, $this->format);
			break;

			case 'deviceCurrentStatusForAll':
				$aid =  isset($_REQUEST['aid'])?$_REQUEST['aid']:NULL;
				$this->device->deviceCurrentStatusForAll($aid, $this->format);
			break;

			case 'deviceDailySummaryForSingle':
				$imei =  isset($_REQUEST['imei'])?$_REQUEST['imei']:NULL;
				$fromDate =  isset($_REQUEST['fromDate'])?$_REQUEST['fromDate']:NULL;
				$toDate =  isset($_REQUEST['toDate'])?$_REQUEST['toDate']:NULL;
				$this->device->deviceDailySummaryForSingle($imei, $fromDate, $toDate, $this->format);
			break;

			case 'deviceList':
				$aid =  isset($_REQUEST['aid'])?$_REQUEST['aid']:NULL;
				$this->device->deviceListByAccountID($aid, $this->format);
			break;

			case 'addUser':
				$this->user->addUser($this->method,$this->format);
			break;

			/*
				Geofence Services
			*/
			case 'geofenceAlerts':
				$imei =  isset($_REQUEST['imei'])?$_REQUEST['imei']:NULL;
				$fromDate =  isset($_REQUEST['fromDate'])?$_REQUEST['fromDate']:NULL;
				$toDate =  isset($_REQUEST['toDate'])?$_REQUEST['toDate']:NULL;
				$this->geofence->getGeofenceAlerts($imei, $fromDate, $toDate, $this->format);
			break;			
			
			/*
				Daily Summary Services
			*/
			case 'dailySummary':
				$imei =  isset($_REQUEST['imei'])?$_REQUEST['imei']:NULL;
				$fromDate =  isset($_REQUEST['fromDate'])?$_REQUEST['fromDate']:NULL;
				$toDate =  isset($_REQUEST['toDate'])?$_REQUEST['toDate']:NULL;
				$this->dailySummary->dailySummaryDetails($imei, $fromDate, $toDate, $this->format);
			break;			
			
			default:
			$this->helper->gotoView($common->do);
			break;
		}
	}		
}

?>
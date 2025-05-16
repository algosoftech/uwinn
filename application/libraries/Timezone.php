<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Timezone
{
	// hold CI intance 
	private $CI;
	
	public function __construct()
	{
		$this->CI = & get_instance();
	}

	//Working
    public function utc_date($format='Y-m-d')
	{
		$utc 	= 	new DateTime("now", new DateTimeZone('UTC'));
		return $utc->format($format);
	}

	//Not use
	public function utc_time()
	{
		$utc 	= 	new DateTime("now", new DateTimeZone('UTC'));
		return $utc->format('U');
	}

	//Working
	public function current_date($format='Y-m-d',$timezone='Asia/Dubai')
	{
		$utc 	= 	new DateTime("now", new DateTimeZone('UTC'));
		$utc->setTimezone(new DateTimeZone($timezone));
		return $utc->format($format);
	}

	//Working
	public function current_time($timezone='Asia/Dubai')
	{
		$utc 	= 	new DateTime("now", new DateTimeZone('UTC'));
		$utc->setTimezone(new DateTimeZone($timezone));
		return $utc->format('U');
	}

	//Working
	public function prev_next_time($format='Y-m-d',$type='+1 day',$timezone='Asia/Dubai')
	{
		$utc 	= 	new DateTimeImmutable($type, new DateTimeZone('UTC'));
		$utc->setTimezone(new DateTimeZone($timezone));
		return $utc->format($format);
	}

	//Working
	public function location_date($curdate='now',$format='Y-m-d',$timezone='Asia/Dubai')
	{ 
		if($curdate):
			if(strpos($curdate,"-")):
				$curdate 		=	$curdate;
			elseif(strpos($curdate,"/")):
				$curdate 		=	str_rteplace('/','-',$curdate);
			else:
				$curdate 		=	'@'.$curdate;//date('Y-m-d H:i:s',$curdate);
			endif;
			if($timezone == ''): $timezone = 'Asia/Kolkata'; endif;
			$utc 	= 	new DateTime($curdate, new DateTimeZone('UTC'));
			$utc->setTimezone(new DateTimeZone($timezone));
			return $utc->format($format);
		else:
			return false;
		endif;
	}

	//Not use
	public function location_time($curdate='now',$timezone='Asia/Dubai')
	{
		if($curdate):
			if(strpos($curdate,"-")):
				$curdate 		=	$curdate;
			elseif(strpos($curdate,"/")):
				$curdate 		=	str_rteplace('/','-',$curdate);
			else:
				$curdate 		=	'@'.$curdate;//date('Y-m-d H:i:s',$curdate);
			endif;
			if($timezone == ''): $timezone = 'Asia/Kolkata'; endif;
			$utc 	= 	new DateTime($curdate, new DateTimeZone('UTC'));
			$utc->setTimezone(new DateTimeZone($timezone));
			return $utc->format('U');
		else:
			return false;
		endif;
	}

	//Not use
	public function DDMMYY_to_utc_date($curdate='',$format='Y-m-d',$timezone='Asia/Dubai')
	{
		if($curdate):
			if(strpos($curdate,"-")):
				$curdate			=	explode('-',$curdate);
				$curdate 			=	$curdate[2].'-'.$curdate[1].'-'.$curdate[0];
			elseif(strpos($curdate,"/")):
				$curdate			=	explode('/',$curdate);
				$curdate 			=	$curdate[2].'-'.$curdate[1].'-'.$curdate[0];
			endif;
			$utc 	= 	new DateTime($curdate, new DateTimeZone($timezone));
			$utc->setTimezone(new DateTimeZone('UTC'));
			return $utc->format($format);
		else:
			return false;
		endif;
	}

	//Working
	public function DDMMYY_to_utc_time($curdate='',$timezone='Asia/Dubai')
	{
		if($curdate):
			if(strpos($curdate,"-")):
				$curdate			=	explode('-',$curdate);
				$curdate 			=	$curdate[2].'-'.$curdate[1].'-'.$curdate[0];
			elseif(strpos($curdate,"/")):
				$curdate			=	explode('/',$curdate);
				$curdate 			=	$curdate[2].'-'.$curdate[1].'-'.$curdate[0];
			endif;  
			$utc 	= 	new DateTime($curdate, new DateTimeZone($timezone));
			$utc->setTimezone(new DateTimeZone('UTC'));
			return $utc->format('U');
		else:
			return false;
		endif;
	}

	//Not use
	public function calender_datetime_to_utc_date($curdate='',$format='Y-m-d',$timezone='Asia/Dubai')
	{	
		if($curdate):
			$utc 	= 	new DateTime($curdate, new DateTimeZone($timezone));
			$utc->setTimezone(new DateTimeZone('UTC'));
			return $utc->format($format);
		else:
			return false;
		endif;
	}

	//Working
	public function calender_datetime_to_utc_date_time($curdate='',$timezone='Asia/Dubai')
	{
		if($curdate):
			$utc 	= 	new DateTime($curdate, new DateTimeZone($timezone));
			$utc->setTimezone(new DateTimeZone('UTC'));
			return $utc->format('U');
		else:
			return false;
		endif;
	}

	//Not use
	public function calender_datetime_to_utc_datetime($curdate='',$timezone='Asia/Dubai')
	{
		if($curdate): 
			$utc 	= 	new DateTime($curdate, new DateTimeZone($timezone));
			$utc->setTimezone(new DateTimeZone('UTC'));
			return $utc->format("c");
		else:
			return false;
		endif;
	}

	//Working
	public function fullcalender_date_range($startdate='',$enddate='',$timezone='Asia/Dubai')
	{
		$returnArray	=	array();
		$begin 			= 	new DateTime($startdate, new DateTimeZone($timezone));
		$end 			= 	new DateTime($enddate, new DateTimeZone($timezone));
		$end 			= 	$end->modify('+1 hour');
		$interval 		= 	new DateInterval('PT1H');
		$daterange 		= 	new DatePeriod($begin,$interval,$end);
		foreach($daterange as $date){
		    array_push($returnArray,$date->format('Y-m-d').'_____'.$this->DDMMYY_to_utc_time($date->format('Y-m-d'),$timezone).'_____'.$date->format('U'));
		}
		return $returnArray;
	}

	//Working
	public function current_timezone_date($timezone='Asia/Dubai')
	{
		$utc 	= 	new DateTime("now", new DateTimeZone('UTC'));
        $utc->setTimeZone(new DateTimeZone($timezone));
		return $utc->format('Y-m-d');
	}

	//Not use
	public function current_timezone_time($timezone='Asia/Dubai')
	{
		$utc 	= 	new DateTime("now", new DateTimeZone('UTC'));
        $utc->setTimeZone(new DateTimeZone($timezone));
		return $utc->format('U');
	}

	//Working
	public  function range_between_to_two_date($startdate='',$enddate='',$timezone='Asia/Dubai')
	{
		$returnArray	=	array();
		$begin 			= 	new DateTime($startdate, new DateTimeZone($timezone));
		$end 			= 	new DateTime($enddate, new DateTimeZone($timezone));
		$end 			= 	$end->modify('+1 day');
		$interval 		= 	new DateInterval('P1D');
		$daterange 		= 	new DatePeriod($begin,$interval,$end);
		foreach($daterange as $date){
		    array_push($returnArray,$date->format('Y-m-d'));
		}
		return $returnArray;
	}
}
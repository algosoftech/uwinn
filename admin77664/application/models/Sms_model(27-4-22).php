<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Sms_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct(); 
	}

	/* * *********************************************************************
	 * * Function name : sendMessageFunction 
	 * * Developed By : Manoj Kumar
	 * * Purpose  : This function use for send Message Function
	 * * Date : 08 APRIL 2021
	 * * **********************************************************************/
	public function sendMessageFunction($phone='',$message='',$type='') {
		if(!empty($phone) && !empty($message) && (SMS_SEND_STATUS == 'YES' || $type =='default')):
			$mobileno	=	$phone;
			$sentmessage=	urlencode($message);
			$authkey	=	SMS_AUTH_KEY;
			$url		=	"http://api.msg91.com/api/sendhttp.php";
			$hiturl		=	$url."?country=".SMS_COUNTRY_CODE."&sender=".SMS_SENDER."&route=4&mobiles=".$mobileno."&authkey=".$authkey."&message=".$sentmessage;	
			$ch = curl_init();
			// set URL and other appropriate options
			curl_setopt($ch, CURLOPT_URL, $hiturl);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			// grab URL and pass it to the browser
			curl_exec($ch);
			// close cURL resource, and free up system resources
			curl_close($ch);
			//$homepage = file_get_contents($hiturl);
			//return $homepage;
		endif;
	}

	/***********************************************************************
	** Function name : sendLoginOtpSmsToUser
	** Developed By : Manoj Kumar
	** Purpose  : This is use for send Login Otp Sms To User
	** Date : 08 APRIL 2021
	************************************************************************/
	function sendLoginOtpSmsToUser($mobileNumber='',$otp='') {  
		if($mobileNumber && $otp):  
			$message		=	"Your One Time Password for Login is ".$otp.".";
			$returnMessage	=	$this->sendMessageFunction($mobileNumber,$message);
			return $returnMessage;
		endif;
	}

	/***********************************************************************
	** Function name : sendForgotPasswordOtpSmsToUser
	** Developed By : Manoj Kumar
	** Purpose  : This is use for send Forgot Password Otp Sms To User
	** Date : 08 APRIL 2021
	************************************************************************/
	function sendForgotPasswordOtpSmsToUser($mobileNumber='',$otp='') {  
		if($mobileNumber && $otp):
			$message		=	"Your One Time Password for Forgot Password is ".$otp.".";
			$returnMessage	=	$this->sendMessageFunction($mobileNumber,$message);
			return $returnMessage;
		endif;
	}

	/***********************************************************************
	** Function name : sendChangePasswordOtpSmsToUser
	** Developed By : Manoj Kumar
	** Purpose  : This is use for send Change Password Otp Sms To User
	** Date : 08 APRIL 2021
	************************************************************************/
	function sendChangePasswordOtpSmsToUser($mobileNumber='',$otp='') {  
		if($mobileNumber && $otp):
			$message		=	"Your One Time Password for Change Password is ".$otp.".";
			$returnMessage	=	$this->sendMessageFunction($mobileNumber,$message);
			return $returnMessage;
		endif;
	}
}	
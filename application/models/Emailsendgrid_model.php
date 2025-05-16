<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
require_once 'sendgridmail/library/sendgrid-php.php';

use SendGrid\Mail\Mail;

class Emailsendgrid_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct(); 
	}
	
	/***********************************************************************
	** Function name : accountVerifyOTP
	** Developed By  : Dilip Halder
	** Purpose  	 : This is send email.
	** Date 		 : 26 June 2024
	************************************************************************/
	function accountVerifyOTP($to='',$otp )
	{	
	 	$html = "Your 4 digit OTP is. $otp";
		 // echo $email;
		if($html <> ""):  
		#... message section....#
		try {

			if($to):
				$email = new Mail();
				$email->setFrom(MAIL_FROM_MAIL, MAIL_SITE_FULL_NAME);
				$email->setSubject('OTP Verification');
				$email->addTo($to);
				$email->addContent("text/html",$html);
				
				$sendgrid = new \SendGrid(SENDGRID_KEY);
				$response = $sendgrid->send($email);
				return true;

			else:
				throw new Exception('Email id required');
			endif;
			
		} catch (Exception $e) {
			return false;
		}
				
		endif;
	} 

	/***********************************************************************
	** Function name : sendForgotpasswordMailToUser
	** Developed By  : Dilip Halder
	** Purpose  	 : This is send email.
	** Date 		 : 26 June 2024
	************************************************************************/
	function sendForgotpasswordMailToUser($to='',$otp='' )
	{	
	 	$html = "Your OTP is ".$otp.".";
		 // echo $email;
		if($html <> ""):  
		#... message section....#
		try {

			if($to):
				$email = new Mail();
				$email->setFrom(MAIL_FROM_MAIL, MAIL_SITE_FULL_NAME);
				$email->setSubject('OTP Verification');
				$email->addTo($to);
				$email->addContent("text/html",$html);
				
				$sendgrid = new \SendGrid(SENDGRID_KEY);
				$response = $sendgrid->send($email);
				return true;

			else:
				throw new Exception('Email id required');
			endif;
			
		} catch (Exception $e) {
			return false;
		}
				
		endif;
	} 

	/***********************************************************************
	** Function name : sendSuccessResetPasswordMailToUser
	** Developed By  : Dilip Halder
	** Purpose  	 : This is send email.
	** Date 		 : 23 August 2024
	************************************************************************/
	function sendSuccessResetPasswordMailToUser($to='')
	{	
	 	$html = "Your password reset successfully.";
		 // echo $email;
		if($html <> ""):  
		#... message section....#
		try {

			if($to):
				$email = new Mail();
				$email->setFrom(MAIL_FROM_MAIL, MAIL_SITE_FULL_NAME);
				$email->setSubject('Password Reset successfully');
				$email->addTo($to);
				$email->addContent("text/html",$html);
				
				$sendgrid = new \SendGrid(SENDGRID_KEY);
				$response = $sendgrid->send($email);
				return true;

			else:
				throw new Exception('Email id required');
			endif;
			
		} catch (Exception $e) {
			return false;
		}
				
		endif;
	} 

	/***********************************************************************
	** Function name : sendSuccessRegistrationMailToUser
	** Developed By  : Dilip Halder
	** Purpose  	 : This is send email.
	** Date 		 : 12 December 2024
	************************************************************************/
	function sendSuccessRegistrationMailToUser($userDetails='')
	{	
		$to = $userDetails['users_email'];
	 	$html = "Your account verified successfully.";
		 // echo $email;
		if($html <> ""):  
		#... message section....#
		try {

			if($to):
				$email = new Mail();
				$email->setFrom(MAIL_FROM_MAIL, MAIL_SITE_FULL_NAME);
				$email->setSubject('Password Reset successfully');
				$email->addTo($to);
				$email->addContent("text/html",$html);
				
				$sendgrid = new \SendGrid(SENDGRID_KEY);
				$response = $sendgrid->send($email);
				return true;

			else:
				throw new Exception('Email id required');
			endif;
			
		} catch (Exception $e) {
			return false;
		}
				
		endif;
	} 
}	
?>
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
	 	// $html = "Your 4 digit OTP is. $otp";
		//  // echo $email;
		// if($html <> ""):  
		// #... message section....#
		// try {

		// 	if($to):
		// 		$email = new Mail();
		// 		$email->setFrom(MAIL_FROM_MAIL, MAIL_SITE_FULL_NAME);
		// 		$email->setSubject('OTP Verification');
		// 		$email->addTo($to);
		// 		$email->addContent("text/html",$html);
				
		// 		$sendgrid = new \SendGrid(SENDGRID_KEY);
		// 		$response = $sendgrid->send($email);
		// 		return true;

		// 	else:
		// 		throw new Exception('Email id required');
		// 	endif;
			
		// } catch (Exception $e) {
		// 	return false;
		// }
				
		// endif;

		try {
			if(!empty($to) && !empty($otp)):
 				$html     = "Your 4 digit OTP is ".$otp.'.';
 				$subject  = "OTP Verification";
 				$curl = curl_init();
				curl_setopt_array($curl, array(
				  CURLOPT_URL => 'https://api.mailjet.com/v3.1/send',
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => '',
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => 'POST',
				  CURLOPT_POSTFIELDS =>'{
						"Messages":[
							{
								"From": {
										"Email": "info@u-winn.com",
										"Name": "Uwinn"
								},
								"To": [
										{
										  "Email": "'.$to.'"
										}
								],
								"Subject" : "'.$subject.'",
								"TextPart": "'.$html.'",
								"HTMLPart": "'.$html.'"
							}
						]
					}',
				  CURLOPT_HTTPHEADER => array(
				    'Content-Type: application/json',
				    'Authorization: Basic '.MAILJET
				  ),
				));
				$response = curl_exec($curl);
				curl_close($curl);
				$result   = json_decode($response ,true);
				if(!empty($result['Messages'][0]['Status']) && $result['Messages'][0]['Status'] != 'success'):
					throw new Exception("Email not send", 1);
				elseif($result['StatusCode'] == 400):
					throw new Exception("Email not send", 1);
				endif;
			else:
				throw new Exception("Email not send", 1);
			endif; 
			
		} catch (Exception $e) {
			echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);die();
		}
	} 

	/***********************************************************************
	** Function name : sendForgotpasswordMailToUser
	** Developed By  : Dilip Halder
	** Purpose  	 : This is send email.
	** Date 		 : 26 June 2024
	************************************************************************/
	function sendForgotpasswordMailToUser($to='',$otp='' )
	{	
	 	// $html = "Your OTP is ".$otp.".";
		//  // echo $email;
		// if($html <> ""):  
		// #... message section....#
		// try {

		// 	if($to):
		// 		$email = new Mail();
		// 		$email->setFrom(MAIL_FROM_MAIL, MAIL_SITE_FULL_NAME);
		// 		$email->setSubject('OTP Verification');
		// 		$email->addTo($to);
		// 		$email->addContent("text/html",$html);
				
		// 		$sendgrid = new \SendGrid(SENDGRID_KEY);
		// 		$response = $sendgrid->send($email);
		// 		return true;

		// 	else:
		// 		throw new Exception('Email id required');
		// 	endif;
			
		// } catch (Exception $e) {
		// 	return false;
		// }
				
		// endif;

		try {
			if(!empty($to) && !empty($otp)):
				$html     = "Your OTP is ".$otp.".";
				$subject  = "OTP Verification";
				$curl = curl_init();
				curl_setopt_array($curl, array(
				  CURLOPT_URL => 'https://api.mailjet.com/v3.1/send',
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => '',
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => 'POST',
				  CURLOPT_POSTFIELDS =>'{
						"Messages":[
							{
								"From": {
										"Email": "info@u-winn.com",
										"Name": "Uwinn"
								},
								"To": [
										{
										  "Email": "'.$to.'"
										}
								],
								"Subject" : "'.$subject.'",
								"TextPart": "'.$html.'",
								"HTMLPart": "'.$html.'"
							}
						]
					}',
				  CURLOPT_HTTPHEADER => array(
				    'Content-Type: application/json',
				    'Authorization: Basic '.MAILJET
				  ),
				));
				$response = curl_exec($curl);
				curl_close($curl);
				$result   = json_decode($response ,true);
				if(!empty($result['Messages'][0]['Status']) && $result['Messages'][0]['Status'] != 'success'):
					throw new Exception("Email not send", 1);
				elseif($result['StatusCode'] == 400):
					throw new Exception("Email not send", 1);
				endif;
			else:
				throw new Exception("Email not send", 1);
			endif; 
			
		} catch (Exception $e) {
			echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);die();
		}


	} 

	/***********************************************************************
	** Function name : sendSuccessResetPasswordMailToUser
	** Developed By  : Dilip Halder
	** Purpose  	 : This is send email.
	** Date 		 : 23 August 2024
	************************************************************************/
	function sendSuccessResetPasswordMailToUser($to='')
	{	
	 	// $html = "Your password reset successfully.";
		//  // echo $email;
		// if($html <> ""):  
		// #... message section....#
		// try {

		// 	if($to):
		// 		$email = new Mail();
		// 		$email->setFrom(MAIL_FROM_MAIL, MAIL_SITE_FULL_NAME);
		// 		$email->setSubject('Password Reset successfully');
		// 		$email->addTo($to);
		// 		$email->addContent("text/html",$html);
				
		// 		$sendgrid = new \SendGrid(SENDGRID_KEY);
		// 		$response = $sendgrid->send($email);
		// 		return true;

		// 	else:
		// 		throw new Exception('Email id required');
		// 	endif;
			
		// } catch (Exception $e) {
		// 	return false;
		// }
				
		// endif;
		$to = $to['users_email'];
		try {
			if(!empty($to)):
 				$subject  = "Password Reset successfully";
 				$html = "Your password reset successfully.";
 				$curl = curl_init();
				curl_setopt_array($curl, array(
				  CURLOPT_URL => 'https://api.mailjet.com/v3.1/send',
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => '',
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => 'POST',
				  CURLOPT_POSTFIELDS =>'{
						"Messages":[
							{
								"From": {
										"Email": "info@u-winn.com",
										"Name": "Uwinn"
								},
								"To": [
										{
										  "Email": "'.$to.'"
										}
								],
								"Subject" : "'.$subject.'",
								"TextPart": "'.$html.'",
								"HTMLPart": "'.$html.'"
							}
						]
					}',
				  CURLOPT_HTTPHEADER => array(
				    'Content-Type: application/json',
				    'Authorization: Basic '.MAILJET
				  ),
				));
				$response = curl_exec($curl);
				curl_close($curl);
				$result   = json_decode($response ,true);
				if(!empty($result['Messages'][0]['Status']) && $result['Messages'][0]['Status'] != 'success'):
					throw new Exception("Email not send", 1);
				elseif($result['StatusCode'] == 400):
					throw new Exception("Email not send", 1);
				endif;
			else:
				throw new Exception("Email not send", 1);
			endif; 
				
		} catch (Exception $e) {
			echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);die();
		}

	} 

	/***********************************************************************
	** Function name : sendSuccessRegistrationMailToUser
	** Developed By  : Dilip Halder
	** Purpose  	 : This is send email.
	** Date 		 : 12 December 2024
	************************************************************************/
	function sendSuccessRegistrationMailToUser($userDetails='')
	{	
		// $to = $userDetails['users_email'];
	 	// $html = "Your account verified successfully.";
		//  // echo $email;
		// if($html <> ""):  
		// #... message section....#
		// try {

		// 	if($to):
		// 		$email = new Mail();
		// 		$email->setFrom(MAIL_FROM_MAIL, MAIL_SITE_FULL_NAME);
		// 		$email->setSubject('Password Reset successfully');
		// 		$email->addTo($to);
		// 		$email->addContent("text/html",$html);
				
		// 		$sendgrid = new \SendGrid(SENDGRID_KEY);
		// 		$response = $sendgrid->send($email);
		// 		return true;

		// 	else:
		// 		throw new Exception('Email id required');
		// 	endif;
			
		// } catch (Exception $e) {
		// 	return false;
		// }
				
		// endif;


		$to = $userDetails['users_email'];
		try {
			if(!empty($to)):

 				$subject  = "Password Reset successfully";
 				$html = "Your account verified successfully.";
 				$curl = curl_init();
				curl_setopt_array($curl, array(
				  CURLOPT_URL => 'https://api.mailjet.com/v3.1/send',
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => '',
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => 'POST',
				  CURLOPT_POSTFIELDS =>'{
						"Messages":[
							{
								"From": {
										"Email": "info@u-winn.com",
										"Name": "Uwinn"
								},
								"To": [
										{
										  "Email": "'.$to.'"
										}
								],
								"Subject" : "'.$subject.'",
								"TextPart": "'.$html.'",
								"HTMLPart": "'.$html.'"
							}
						]
					}',
				  CURLOPT_HTTPHEADER => array(
				    'Content-Type: application/json',
				    'Authorization: Basic '.MAILJET
				  ),
				));
				$response = curl_exec($curl);
				curl_close($curl);
				$result   = json_decode($response ,true);
				if(!empty($result['Messages'][0]['Status']) && $result['Messages'][0]['Status'] != 'success'):
					throw new Exception("Email not send", 1);
				elseif($result['StatusCode'] == 400):
					throw new Exception("Email not send", 1);
				endif;
			else:
				throw new Exception("Email not send", 1);
			endif; 
				
		} catch (Exception $e) {
			echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);die();
		}

	} 
}	
?>
<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
// require_once 'Twilio/autoload.php';
use Twilio\Rest\Client;

class Sms_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct(); 
	}

	/* * *********************************************************************
	 * * Function name : sendMessageFunction 
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function use for send Message Function
	 * * Date : 08 APRIL 2021
	 * * **********************************************************************/
	public function sendMessageFunction($phone='',$message='',$sender_id='') {

		
		try {
			if(!empty($phone) && !empty($message) && !empty($sender_id)):
				//Please Enter Your Details
				$user=SMSCOUNTRYUSER; //your username
				$password=SMSCOUNTRYPASSWORD; //your password
				$mobilenumbers=substr($phone,1); //enter Mobile numbers comma seperated
				$message = $message; //enter Your Message
				$senderid=$sender_id; //Your senderid
				$messagetype="N"; //Type Of Your Message
				$DReports="Y"; //Delivery Reports
				$url="http://www.smscountry.com/SMSCwebservice_Bulk.aspx";
				$message = urlencode($message);
				$ch = curl_init();
				if (!$ch){die("Couldn't initialize a cURL handle");}
				$ret = curl_setopt($ch, CURLOPT_URL,$url);
				curl_setopt ($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
				curl_setopt ($ch, CURLOPT_POSTFIELDS,
				"User=$user&passwd=$password&mobilenumber=$mobilenumbers&message=$message&sid=$senderid&mtype=$messagetype&DR=$DReports");
				$ret = curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				//If you are behind proxy then please uncomment below line and provide your proxy ip with port.
				// $ret = curl_setopt($ch, CURLOPT_PROXY, "PROXY IP ADDRESS:PORT");
				$curlresponse = curl_exec($ch); // execute
 
				if(curl_errno($ch))
				return "ERROR";
				//echo 'curl error : '. curl_error($ch);
				if (empty($ret)) {
					return "some kind of an error happened";
				// some kind of an error happened
				//die(curl_error($ch));
				curl_close($ch); // close cURL handler
				} else {
				$info = curl_getinfo($ch);
				curl_close($ch); // close cURL handler
				return $curlresponse; //echo "Message Sent Succesfully" ;
				}
			endif;
		} catch (\Throwable $th) {
			return "FAIL";
		}
		
	}


	public function sendMessageDigitizebirdFunction($phone='',$message='',$senderid='')
	{
		try {
			if(!empty($phone) && !empty($message) && !empty($senderid)):
				
				//old api key $ApiKey 		= 'ybG+HgfvR2YzK/LOlwwBXU7YRhKu+LK5Vi6Mfg5N5AI=';
				$ApiKey 		= 'r8J3+a64Tni3MRp/0VDKEHPL2D4iu+Q/7LlLgL01f9c=';
				$ClientId 		= '3cb6faf0-b21c-4094-8409-cd3a0b3e03de';
				$CompanyId 		= '7';
				$message = urlencode($message);
				$url = "https://user.digitizebirdsms.com/api/v2/SendSMS?SenderId=$senderid&Is_Unicode=false&Is_Flash=true&Message=$message&MobileNumbers=$phone&ApiKey=$ApiKey&ClientId=$ClientId&CompanyId=$CompanyId";
				
				$curl = curl_init();
				curl_setopt_array($curl, array(
					CURLOPT_URL => $url,
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_SSL_VERIFYPEER => false,
					CURLOPT_CUSTOMREQUEST => 'GET',
					CURLOPT_HTTPHEADER => array( 'accept: text/plain' ),
				));
				$response = curl_exec($curl);
				curl_close($curl);
				return $response; 
			endif;
		} catch (\Throwable $th) {
			return "FAIL";
		}
	}

	/***********************************************************************
	** Function name 	: sendForgotPasswordOtpSmsToUser
	** Developed By 	: Dilip Halder
	** Purpose  		: This is use for send Forgot Password Otp Sms To User
	** Date 			: 08 January 2024
	************************************************************************/
	function accountVerifyOTP($countryCode='+971',$mobile='',$otp='') {  
		$enableSMS    = $this->common_model->getData('single','uw_enablesms');
		$mobileNumber =   $countryCode.$mobile;
		// Finding country code and sending sms using sms country.
        if($enableSMS['smscountry'] == "enable"):
        	$SMSCOUNTRY = explode(',', $enableSMS['sms_country_available_country']);

			// Removed extra space from country code ...
			foreach ($SMSCOUNTRY as $key => $item):
				$SMSCOUNTRY[$key] = trim($item);
			endforeach;


            // checking country code exist or Not...
			if(in_array($countryCode, $SMSCOUNTRY) ):
				if($mobileNumber && $otp):

					$message		= "Your OTP is ".$otp.".";
					$senderid		= "B2DTLLC";
					$returnMessage	= $this->sendMessageFunction($mobileNumber,$message,$senderid);
					return $returnMessage;
				endif;
			endif; 
        endif;


        // Finding country code and sending sms using digitizebird.
        if($enableSMS['digitizebird'] == "enable"):

        	$SMSCOUNTRY1 = explode(',', $enableSMS['digitizebird_available_country']);
			
			// Removed extra space from country code ...
			foreach ($SMSCOUNTRY1 as $key => $item1):
				$SMSCOUNTRY1[$key] = trim($item1);
			endforeach;

            // checking country code exist or Not...
			if(in_array($countryCode, $SMSCOUNTRY1) ):
				if($mobileNumber && $otp):
					$message		=	"Your OTP is ".$otp.".";
					$senderid 		= 'EVBNS';
					$returnMessage	=	$this->sendMessageDigitizebirdFunction($mobileNumber,$message,$senderid);
					return $returnMessage;
				endif;
			endif; 
        endif;
		
	} //END OF FUNCTION

	/***********************************************************************
	** Function name 	: accountWhatsappVerifyOTP
	** Developed By 	: Dilip Halder
	** Purpose  		: This is use for to send whatsapp otp.
	** Date 			: 15 July 2025
	************************************************************************/
 	function accountWhatsappVerifyOTP($countryCode='+971',$mobile='',$otp='')
 	{
	 	// Set your credentials and message info
		$apiKey     = '6876026f3efcc78baa8a405d';
		$apiSecret  = '15b64cbab271477c980bbdaa7b10457e';
		$channelId  = '68720fc7904c8c7ddb0c57bf';


		$countryCode = str_replace('+','', $countryCode);
		// echo "<pre>";print_r($countryCode);die();

		// Create the payload
		$data = array(
		    'channelId'   => $channelId,
		    'channelType' => 'whatsapp',
		    'recipient'   => [ 'name'  => $name, 'phone' => $phone ],
		    'whatsapp'    => [
		        'text' => [
		            'body' => $message
		        ],
		        'type' => 'text'
		    ]
		);

		$postFields = array(
		    "channelId" => $channelId,
		    "channelType" => "whatsapp",
		    "recipient" => array("phone" => $countryCode.$mobile  ),
		    "whatsapp" => array(
		        "type" => "template",
		        "template" => array(
		            "templateName" => "otp_verification",
		            "bodyValues" => array(
		                "otp" => $otp
		            )
		        )
		    )
		);

		// Initialize cURL
		$ch = curl_init('https://server.gallabox.com/devapi/messages/whatsapp');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, [
		    "apiKey: $apiKey",
		    "apiSecret: $apiSecret",
		    'Content-Type: application/json'
		]);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postFields));

		// Execute and handle response
		$response = curl_exec($ch);
		curl_close($ch);

		// echo $response;
 	} 

	/***********************************************************************
	** Function name 	: sendSuccessResetPasswordSmsToUser
	** Developed By 	: Dilip Halder
	** Purpose  		: This is use for send success reset password Sms To User
	** Date 			: 08 January 2024
	************************************************************************/
	function sendSuccessResetPasswordSmsToUser($mobileNumber='',$country_code='+971') {  
		
		$enableSMS = $this->common_model->getData('single','uw_enablesms');
		
		// Finding country code and sending sms using sms country.
        if($enableSMS['smscountry'] == "enable"):

        	$SMSCOUNTRY = explode(',', $enableSMS['sms_country_available_country']);

			// Removed extra space from country code ...
			foreach ($SMSCOUNTRY as $key => $item):
				$SMSCOUNTRY[$key] = trim($item);
			endforeach;

            // checking country code exist or Not...
			if(in_array($country_code, $SMSCOUNTRY) ):
				if($mobileNumber):
					$message		=	"Your UWINN password has been reset successfully";
					$senderid		=	"EVBNS";
					$returnMessage	=	$this->sendMessageFunction($mobileNumber,$message,$senderid);
					return $returnMessage;
				endif;
			endif; 
        endif;

        // Finding country code and sending sms using digitizebird.
        if($enableSMS['digitizebird'] == "enable"):

        	$SMSCOUNTRY1 = explode(',', $enableSMS['digitizebird_available_country']);
			
			// Removed extra space from country code ...
			foreach ($SMSCOUNTRY1 as $key => $item1):
				$SMSCOUNTRY1[$key] = trim($item1);
			endforeach;

            // checking country code exist or Not...
			if(in_array($country_code, $SMSCOUNTRY1) ):
				if($mobileNumber):
					$message		=	"Your UWINN password has been reset successfully";
					$senderid		=	"EVBNS";
					$returnMessage	=	$this->sendMessageDigitizebirdFunction($mobileNumber,$message,$senderid);
					return $returnMessage;
				endif;
			endif; 
        endif;
	} //END OF FUNCTION

	/***********************************************************************
	** Function name 	: sendSuccessRgistrationSmsToUser
	** Developed By 	: Dilip Halder
	** Purpose  		: This is use for send success reset password Sms To User
	** Date 			: 08 January 2024
	************************************************************************/
	function sendSuccessRgistrationSmsToUser($mobile='',$name='', $country_code='+971') {  
		
		$enableSMS = $this->common_model->getData('single','uw_enablesms');
		
		// Finding country code and sending sms using sms country.
        if($enableSMS['smscountry'] == "enable"):

        	$SMSCOUNTRY = explode(',', $enableSMS['sms_country_available_country']);
        	$message	=	"Your account verified successfully.";
			// Removed extra space from country code ...
			foreach ($SMSCOUNTRY as $key => $item):
				$SMSCOUNTRY[$key] = trim($item);
			endforeach;

            // checking country code exist or Not...
			if(in_array($country_code, $SMSCOUNTRY) ):
				if($mobileNumber):
					
					$senderid		=	"EVBNS";
					$returnMessage	=	$this->sendMessageFunction($mobileNumber,$message,$senderid);
					return $returnMessage;
				endif;
			endif; 
        endif;

        // Finding country code and sending sms using digitizebird.
        if($enableSMS['digitizebird'] == "enable"):

        	$SMSCOUNTRY1 = explode(',', $enableSMS['digitizebird_available_country']);
			
			// Removed extra space from country code ...
			foreach ($SMSCOUNTRY1 as $key => $item1):
				$SMSCOUNTRY1[$key] = trim($item1);
			endforeach;

            // checking country code exist or Not...
			if(in_array($country_code, $SMSCOUNTRY1) ):
				if($mobileNumber):
					
					$senderid		=	"EVBNS";
					$returnMessage	=	$this->sendMessageDigitizebirdFunction($mobileNumber,$message,$senderid);
					return $returnMessage;
				endif;
			endif; 
        endif;
	} //END OF FUNCTION
}	
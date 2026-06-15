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

	/* * *********************************************************************
	 * * Function name : sendMessageDigitizebirdFunction 
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function use for send Message Function
	 * * Date : 08 APRIL 2021
	 * * **********************************************************************/
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

	/* * *********************************************************************
	 * * Function name : raffleWinnersSms 
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function use for resend Message Function
	 * * Date : 22 October 2025
	 * * **********************************************************************/
	public function raffleWinnersSms($phone='',$message='',$senderid='')
	{
		try {
			// Enhanced validation
			if(empty($phone) || empty($message) || empty($senderid)) {
				return array('status' => 'FAIL', 'error' => 'Missing required parameters: phone, message, or senderid');
			}

			// Validate phone number format (should contain only digits and + sign)
			$phone = trim($phone);
			if(!preg_match('/^\+?[1-9]\d{1,14}$/', $phone)) {
				return array('status' => 'FAIL', 'error' => 'Invalid phone number format');
			}

			// Validate message length (SMS typically limited to 160 characters)
			$message = trim($message);
			if(strlen($message) > 160) {
				return array('status' => 'FAIL', 'error' => 'Message too long. Maximum 160 characters allowed');
			}

			if(strlen($message) < 1) {
				return array('status' => 'FAIL', 'error' => 'Message cannot be empty');
			}

			// Validate sender ID format (alphanumeric, max 11 characters)
			$senderid = trim($senderid);
			if(!preg_match('/^[A-Z0-9]{1,11}$/', $senderid)) {
				return array('status' => 'FAIL', 'error' => 'Invalid sender ID format. Must be alphanumeric, max 11 characters');
			}

			//old api key $ApiKey 		= 'ybG+HgfvR2YzK/LOlwwBXU7YRhKu+LK5Vi6Mfg5N5AI=';
			$ApiKey 		= '26b+uKslzUhFgUz9+OSK0uyVD0kL3WKQqwuvfzGjhIM=';
			$ClientId 		= 'c310faf3-f103-4e29-a170-a4e02940907c';
			$CompanyId 		= '7';
			
			// Validate API credentials
			if(empty($ApiKey) || empty($ClientId) || empty($CompanyId)) {
				return array('status' => 'FAIL', 'error' => 'SMS service configuration incomplete');
			}

			$encodedMessage = urlencode($message);
			$url = "https://user.digitizebirdsms.com/api/v2/SendSMS?SenderId=$senderid&Is_Unicode=false&Is_Flash=true&Message=$encodedMessage&MobileNumbers=$phone&ApiKey=$ApiKey&ClientId=$ClientId&CompanyId=$CompanyId";
			
			$curl = curl_init();
			curl_setopt_array($curl, array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30, // Set reasonable timeout
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_SSL_VERIFYPEER => false,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => array( 'accept: text/plain' ),
			));
			
			$response = curl_exec($curl);
			$httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			$curlError = curl_error($curl);
			curl_close($curl);

			// Check for cURL errors
			if($curlError) {
				return array('status' => 'FAIL', 'error' => 'cURL Error: ' . $curlError);
			}

			// Check HTTP response code
			if($httpCode !== 200) {
				return array('status' => 'FAIL', 'error' => 'HTTP Error: ' . $httpCode);
			}

			// Parse response to check if SMS was sent successfully
			$responseData = json_decode($response, true);
			if($responseData && isset($responseData['status'])) {
				return array('status' => $responseData['status'], 'response' => $responseData);
			}

			return array('status' => 'SUCCESS', 'response' => $response);

		} catch (\Throwable $th) {
			return array('status' => 'FAIL', 'error' => 'Exception: ' . $th->getMessage());
		}
	}

	
	/***********************************************************************
	** Function name 	: sendSMS
	** Developed By 	: Dilip Halder
	** Purpose  		: Send order confirmation via selected gateway (SMS Country or Digitizebird)
	** Date 			: 07 February 2026
	************************************************************************/
	public function sendSMS($senderDetails)
	{
		$gateway = $senderDetails['gateway'];
		$phone   = $senderDetails['country_code'].$senderDetails['users_mobile'];
		$message = $senderDetails['message'];

		$phone = trim($phone); 
		$gateway = strtolower(trim($gateway));

		// SMS gateways
		$senderid_smscountry   = 'B2DTLLC';
		$senderid_digitizebird = 'UWINNAPP';
		$senderid_ndm          = 'UWNTRD';
		
		if ($gateway === 'smscountry') {
			$res = $this->sendMessageFunction($phone, $message, $senderid_smscountry);
			if(str_contains($res , "OK:")):
				$result['status'] = 'Success';
				$result['error']  = "";
				return json_encode($result);
			else:
				$result['status'] = 'Failed';
				$result['error']  = $res['ErrorDescription'];
				return json_encode($result); 
			endif;
		}

		if ($gateway === 'digitizebird') {
			$res = $this->sendMessageDigitizebirdFunction($phone, $message, $senderid_digitizebird);
			$res = json_decode($res , true);;
			if($res['ErrorDescription']):
				$result['status'] = 'Failed';
				$result['error']  = $res['ErrorDescription'];
				return json_encode($result);
			else:
				$result['status'] = 'Success';
				$result['error']  = "";
				return json_encode($result);
			endif;
		}

		if ($gateway === 'ndm') {
			$res = $this->sendMessageNDMFunction($phone, $message, $senderid_ndm);
			if($res):
				$result['status'] = 'Success';
				$result['error']  = "";
				return json_encode($result);
			endif;
		}

		return array('status' => 'FAIL', 'error' => 'Unknown gateway.');
	}

	/***********************************************************************
	** Function name 	: sendMessageNDMFunction
	** Developed By 	: Dilip Halder
	** Purpose  		: This is use for send NDM Sms To User
	** Date 			: 09 February 2026
	************************************************************************/
	function sendMessageNDMFunction($phone='',$message='',$senderid='') {

		try {	

			$url = "https://ndm-solutions.com/sms/api?action=send-sms&api_key=VVdJTiBUUkFOOiQyeSQxMCR2bVFtYm44ejJBQkdZamdVQy96bldPMGg0aGZldG9OaGN4MXIzUWRiMXJTYkVmRDFzUW9NLg==&to=PhoneNumber&from=SenderID&sms=YourMessage&response=json";
			$url = str_replace('PhoneNumber', $phone, $url);
			$url = str_replace('SenderID', $senderid, $url);
			$url = str_replace('YourMessage', urlencode($message), $url);
			$curl = curl_init();
			curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
				'Cookie: laravel_session=eyJpdiI6Ik5xRVNiVFwvXC9yT0lSNjhkQnJCRVdyUT09IiwidmFsdWUiOiJrVXFFRTEyVHB0TVpvNnZhTmlLQWR0OVwvZWVcL0g4VFZtWWdGbHRKeU1CTHg3V1NRTDd5d0Y3MWNXTFpONGxTUWlYYk16eFdXK241dWxqbTdzY1RnQzNBPT0iLCJtYWMiOiJmZWRlODMzMmEwM2Y2MDY0YjQ2YWIzNzAxM2RkZmY0MWNhZTllMTQ5N2E0Y2Y1MWEwMDVhMDBjNjMwYWVmNWFkIn0%3D'
				),
				));
				
			$response = curl_exec($curl);
			$response = json_decode($response, true);
			curl_close($curl);
			if($response['code'] == 'ok'):
				$result['status'] = 'Success';
				$result['error']  = "";
				return json_encode($result);
			else:
				$result['status'] = 'Failed';
				$result['error']  = $response['message'];
				return json_encode($result);
			endif;


		} catch (\Throwable $th) {
			return array('status' => 'FAIL', 'error' => $th->getMessage());
		}
		

	}

	/***********************************************************************
	** Function name 	: sendWhatsAppMessage
	** Developed By 	: Dilip Halder
	** Purpose  		: Send message via WhatsApp using configured API
	** Date 			: 07 February 2026
	************************************************************************/
	public function sendWhatsAppMessage($senderDetails)
	{
		try {

			$COUNTRY_CODE  = str_replace('+', '', $senderDetails['country_code']);
			$PHONE         = $COUNTRY_CODE.$senderDetails['users_mobile'];
			$ORDERID       = $senderDetails['ORDERID'];
			$CAMPAIGNAME   = $senderDetails['CAMPAIGNAME'];
			$CouponDetails = $senderDetails['CouponDetails'];
			$DDATE         = $senderDetails['DDATE'];
			$LINK          = $senderDetails['LINK'];

			// Set your credentials and message info
			$apiKey       = '6876026f3efcc78baa8a405d';
			$apiSecret    = '15b64cbab271477c980bbdaa7b10457e';
			$channelId    = '698495fdeb02ed5dbb3cf8aa';
			$templateName = 'uwinn_campigns';
			
			$postFields = [
				"channelId" => $channelId,
				"channelType" => "whatsapp",
				"recipient" => ["phone" => $PHONE ],
				"whatsapp" => [
					"type" => "template",
					"template" => [
						"templateName" => $templateName,
						"bodyValues" => [
							"ORDERID" => $ORDERID,
							"CAMPAIGNAME" => $CAMPAIGNAME,
							"COUPONDETAILS" => $CouponDetails,
							"DDATE"       => $DDATE,
							"INVOICELINK" => $LINK
						]
					]
				]
			];

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
			$response = json_decode($response, true);
			if($response['status'] == 'ACCEPTED'):
				$result['status'] = 'Success';
				$result['error']  = "";
				return json_encode($result);
			else:
				$result['status'] = 'Failed';
				$result['error']  = $response['error'];
				return json_encode($result);
			endif;
		} catch (\Throwable $th) {
			return array('status' => 'FAIL', 'error' => $th->getMessage());
		}
	}

	

}	
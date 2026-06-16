<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {
	
	var $postdata;
	var $user_agent;
	var $request_url; 
	var $method_name;
	
	public function  __construct() 	
	{ 
		parent:: __construct();
		error_reporting(E_ALL ^ E_NOTICE);  
		$this->load->model(array('sms_model','notification_model','emailtemplate_model','emailsendgrid_model'));
		$this->lang->load('statictext', 'api');
		$this->load->helper('apidata');
		$this->load->model(array('geneal_model','common_model'));

		$this->user_agent 		= 	$_SERVER['HTTP_USER_AGENT'];
		$this->request_url 		= 	$_SERVER['REDIRECT_URL'];
		$this->method_name 		= 	$_SERVER['REDIRECT_QUERY_STRING'];

		$this->load->library('generatelogs',array('type'=>'users'));
	} 

	/* * *********************************************************************
	 * * Function name : checkEmail
	 * * Developed By  : Dilip Halder
	 * * Purpose       : This function used for check Email
	 * * Date 		   : 07 FEBRUARY 2024
	 * * **********************************************************************/
	public function checkEmail()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):
			
			if($this->input->post('users_email') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMAIL_EMPTY'),$result);
			else:

			 	$where = ['users_email' => $this->input->post('users_email') ];
			    $query = $this->geneal_model->checkDuplicate('uw_users',$where);
			    if (empty($query)):
			    	$result['users_email']    =   $this->input->post('users_email');
			    	echo outPut(1,lang('SUCCESS_CODE'),lang('EMAIL_AVAILABLE'),$result);
			    else:
			    	echo outPut(0,lang('SUCCESS_CODE'),lang('EMAIL_ALREADY_EXIST'),$result);
			    endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : checkMobile
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for check Mobile
	 * * Date : 13 JUNE 2022
	 * * **********************************************************************/
	public function checkMobile()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):
			
			if($this->input->post('users_mobile') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('PHONE_EMPTY'),$result);
			else:
				if(is_numeric($this->input->post('users_mobile') )):
					$where = ['users_mobile' => (int)$this->input->post('users_mobile') ];
				else:
			    	echo outPut(0,lang('SUCCESS_CODE'),lang('Invalid_Character'),$result);die();
				endif;
			 	
			    $query = $this->geneal_model->checkDuplicate('uw_users',$where);
			    if (empty($query)):
			    	$result['users_mobile']    =   $this->input->post('users_mobile');
			    	echo outPut(1,lang('SUCCESS_CODE'),lang('PHONE_AAVAILABLE'),$result);
			    else:
			    	echo outPut(0,lang('SUCCESS_CODE'),lang('PHONE_ALREADY_EXIST'),$result);
			    endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name  : signup
	 * * Developed By 	: Dilip Halder
	 * * Purpose        : This function used for signup
	 * * Date           : 13 JUNE 2022
	 * * Updated By     : Dilip halder
	 * * Date           : 07 FEBRUARY 2024
	 * * **********************************************************************/
	// public function signup()
	// {	
	// 	$apiHeaderData 		=	getApiHeaderData();
	// 	$this->generatelogs->putLog('APP',logOutPut($_POST));
	// 	$result 			= 	array();	
	// 	if(requestAuthenticate(APIKEY,'POST')):
			

	// 		if($this->input->post('users_name') == ''): 
	// 			echo outPut(0,lang('SUCCESS_CODE'),lang('NAME_EMPTY'),$result);
	// 		// elseif($this->input->post('last_name') == ''): 
	// 		// 	echo outPut(0,lang('SUCCESS_CODE'),lang('LASTNAME_EMPTY'),$result);
	// 		// elseif($this->input->post('users_email') == ''): 
	// 		// 	echo outPut(0,lang('SUCCESS_CODE'),lang('EMAIL_EMPTY'),$result);
	// 		elseif($this->input->post('country_code') == ''): 
	// 			echo outPut(0,lang('SUCCESS_CODE'),lang('COUNTRY_CODE_EMPTY'),$result);
	// 		elseif($this->input->post('users_mobile') == ''): 
	// 			echo outPut(0,lang('SUCCESS_CODE'),lang('PHONE_EMPTY'),$result);
	// 		elseif($this->input->post('users_password') == ''): 
	// 			echo outPut(0,lang('SUCCESS_CODE'),lang('PASSWORD_EMPTY'),$result);
	// 		else:

	// 			$users_name  	 = $this->input->post('users_name');
	// 			$last_name  	 = $this->input->post('last_name');
	// 			$email   	 	 = $this->input->post('users_email');
	// 			$country_code    = $this->input->post('country_code');
	// 			$mobile    		 = $this->input->post('users_mobile');
	// 			$users_password  = $this->input->post('users_password');
	// 			$device_type 	 = $this->input->post('device_type');
	// 			$app_name  	 	 = $this->input->post('app_name');
	// 			$app_version 	 = $this->input->post('app_version');
	// 			$otp      		 = (int)rand(1000,9999);
	// 			$referredBy 	 = $this->input->post('referred_by');
	// 			$users_lat 	 	 = $this->input->post('users_lat');
	// 			$users_long 	 = $this->input->post('users_long');
	// 			$users_address 	 = $this->input->post('users_address');

	// 			$tblName   = 'uw_user_varification';
	// 				$Field = 'users_mobile';
	// 				$value = (int)$mobile;
	// 			if($email):
	// 				$Field = 'users_email';
	// 				$value = $email;
	// 			endif;

	// 			// Added email id validation...
	// 			$allowedDomains = array('gmail.com', 'yahoo.com', 'yahoo.co.in');
	// 			$emailDomain    = substr(strrchr($email, "@"), 1);
	// 			if (!in_array(strtolower($emailDomain), $allowedDomains)):
	// 				echo outPut(0,lang('SUCCESS_CODE'),'Email id is invalid',$result);die();
	// 			endif;

	// 			//Removed Old Entries..
	// 			$this->common_model->deleteData($tblName,$Field,$value);

	// 			/*--------------------------Mobile Number Validation start----------------------------------*/
	// 			$tblUSERName 		= 'uw_users';
	// 			$whereCon['where']  = array('country_code'=>$country_code , 'users_mobile' => (int)$mobile);
	// 			$MobileExist 		= $this->common_model->getData('count',$tblUSERName,$whereCon );

	// 			if($MobileExist == 1):
	// 				echo outPut(0,lang('SUCCESS_CODE'),lang('PHONE_ALREADY_EXIST'),$result);die();
	// 			endif;
	// 			/*--------------------------Mobile Number Validation end----------------------------------*/

	// 			/*--------------------------Email Validation start----------------------------------*/
	// 			$tblUSERName 		= 'uw_users';
	// 			$whereCon['where']  = array('users_email'=>$email);
	// 			$EmailExist 		= $this->common_model->getData('count',$tblUSERName,$whereCon );

	// 			if($EmailExist == 1):
	// 				echo outPut(0,lang('SUCCESS_CODE'),lang('EMAIL_ALREADY_EXIST'),$result);die();
	// 			endif;
	// 			/*--------------------------Email Validation end----------------------------------*/


	// 			/*--------------------------Mobile Number Validation----------------------------------*/
	// 			$param["verification_id"] = (int)$this->geneal_model->getNextSequence('uw_user_varification');
	// 			$param["users_id"] 		  = (int)$this->geneal_model->getNextSequence('uw_users');
	// 			$param['users_name']	  = addslashes($users_name);
	// 			$param['last_name']	      = addslashes($last_name);
	// 			$param['users_type']	  = addslashes('Users');
	// 			$param['users_email']	  = addslashes($email);
	// 			$param['country_code']	  = addslashes($country_code);
	// 			$param['users_mobile']	  = (int)$mobile;
	// 			$param['password']	      = md5($this->input->post('users_password'));
	// 			$param['is_varified']	  = addslashes('N');
	// 			$param['status']	      = addslashes('I');
	// 			$param['created_date']	  = date('Y-m-d H:i:s');
	// 			$param['otp'] 			  = $otp;
	// 			$param['device_type'] 	  = $device_type;
	// 			$param['app_name'] 		  = $app_name;
	// 			$param['app_version'] 	  = $app_version;
	// 			$param['latitude']		  = $users_lat;
	// 			$param['longitude']		  = $users_long;
	// 			$param['address']		  = $users_address;
				
	// 			if($device_type == 'ios' || $device_type == 'android' ):
	// 				$param['bind_person_id'] 	=  (int)"100000000000001";
	// 				$param['bind_user_type'] 	=  "Admin";
	// 				$param['bind_person_name'] 	=  "Admin";
	// 			endif;
	// 			$param['client_details']  = $_SERVER;
	// 			$param['login_token']  	  = $this->geneal_model->generatetoken();
	// 			if(!empty($referredBy)):

	// 				$tblName           = "uw_users";
	// 				$Fieldslist		   = '_id';
	// 				$referredBY = (is_numeric($referredBy)) ? (int)$referredBy : $referredBy;
	// 				$GetReferralData   = $this->common_model->getPaticularFieldByFields($Fieldslist,$tblName,"referral_code" ,$referredBY );
	// 				if(!empty($GetReferralData)):
	// 					$GetReferralDID = $GetReferralData->{'$id'};
	// 					$param['referred_by'] 	  = new MongoDB\BSON\ObjectId($GetReferralDID);
	// 					// $param['referrel_amount'] = 25;
	// 				else:
	//             		echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_REFERRAL_CODE'),$result);die();
	// 				endif;
	// 			endif;
	// 			$tableName = 'uw_user_varification';
	// 			$Data 	   = $this->common_model->addData($tableName,$param);

	// 			  $this->sms_model->accountVerifyOTP($country_code,$mobile,$otp);
	// 			if($email):
	// 			  $this->emailsendgrid_model->accountVerifyOTP($email,$otp);
	// 			endif;
				
	// 			$result_data['users_type'] 		= 'Users';
	// 			$result_data['users_id']   		= $param["users_id"];
	// 			$result_data['users_name'] 		= $param['users_name'];
	// 			$result_data['last_name']  		= $param['last_name'];
	// 			$result_data['country_code']  	= $param['country_code'];
	// 			$result_data['users_mobile']  	= $param['users_mobile'];
	// 			$result_data['users_email']  	= $param['users_email'];
	// 			$result_data['status']  		= 'I';
	// 			$result_data['is_verify']  		= 'N';
	// 			$result_data['login_token']  	= $param['login_token'];
	// 			$result['userData'] 			=	$result_data;
    //             echo outPut(1,lang('SUCCESS_CODE'),lang('REGISTRATION_SUCCESS'),$result);
	// 		endif;
	// 	else:
	// 		echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
	// 	endif;
	// }
	 public function signup()
	 {
		try {

			$apiHeaderData  = getApiHeaderData();
			$this->generatelogs->putLog('APP',logOutPut($_POST));
			$result         = array();	
			if(requestAuthenticate(APIKEY,'POST')):

				$usersName      = $this->input->post('users_name');
				$lastName  	    = $this->input->post('last_name');
				$email   	    = $this->input->post('users_email');
				$countryCode    = $this->input->post('country_code');
				$mobile    	    = $this->input->post('users_mobile');
				$usersPassword  = $this->input->post('users_password');
				$deviceType 	= $this->input->post('device_type');
				$appName  	 	= $this->input->post('app_name');
				$appVersion 	= $this->input->post('app_version');
				$referredBy 	= $this->input->post('referred_by');
				$usersLat 	 	= $this->input->post('users_lat');
				$usersLong 		= $this->input->post('users_long');
				$usersAddress 	= $this->input->post('users_address');
				$posDeviceID 	= $this->input->post('pos_device_id');

			    // checked required empty inputs.. 
				if(empty($usersName)):
				    throw new Exception(lang('EMPRT_FIRST_NAME'), 1);
				elseif(empty($lastName)):
				    throw new Exception(lang('EMPTY_LAST_NAME'), 1);
				// elseif(empty($email)):
				    // throw new Exception(lang('EMAIL_EMPTY'), 1);
				elseif(empty($countryCode) && !empty($mobile) ):
				    throw new Exception(lang('EMPTY_COUNTRYCODE'), 1);
				elseif(!empty($countryCode) && empty($mobile)):
				    throw new Exception(lang('EMPTY_USERMOBILE'), 1);
				elseif(empty($usersPassword)):
				    throw new Exception(lang('PASSWORD_EMPTY'), 1);
				elseif(empty($deviceType)):
				    throw new Exception(lang('EMPTY_DEVICE_TYPE'), 1);
				elseif(empty($appName)):
				    throw new Exception(lang('EMPTY_APPNAME'), 1);
				elseif(empty($appVersion)):
				    throw new Exception(lang('EMPTY_APP_VERSION'), 1);
				else:

					if(!empty($posDeviceID)):
						$posWhere['where'] = array('pos_device_id' => $posDeviceID);
						$posData = $this->common_model->getData('count','uw_users',$posWhere);
						if(!empty($posData)):
					    	throw new Exception(lang('POS_DEVICE_RESTRICTION'), 1);
							die();
						endif;
					endif;


				    // checked required empty inputs.. 
					if((empty($countryCode) && empty($mobile)) && empty($email)):
				    	throw new Exception(lang('EMPTY_MOBILE_EMAIL'), 1);
				    // Email and mobile number vefied and duplicate checked..
					elseif(( !empty($countryCode) && !empty($mobile) )  || !empty($email) ):

						// Mobile number verification..
						if(!empty($countryCode) && !empty($mobile) ):
							/* Existing users validation.. */ 
							$tblUSERName 		= 'uw_users';
							$whereCon['where']  = array('country_code'=>$countryCode , 'users_mobile' => (int)$mobile);
							$MobileExist 		= $this->common_model->getData('count',$tblUSERName,$whereCon );
							if($MobileExist == 1):
					    		throw new Exception(lang('PHONE_ALREADY_EXIST'), 1);
							endif;

						// Email verification..
						endif;

						if(!empty($email)):

							// Added email id validation...
							$allowedDomains = array('gmail.com', 'yahoo.com', 'yahoo.co.in');
							$emailDomain    = substr(strrchr($email, "@"), 1);
							if (!in_array(strtolower($emailDomain), $allowedDomains) || substr_count($email, ".") > 2  ):
					    		throw new Exception(lang('INVALID_EMAILID'), 1);
							endif;

							/* Existing users validation.. */ 
							$tblUSERName 		= 'uw_users';
							$whereCon['where']  = array('users_email'=>$email);
							$EmailExist 		= $this->common_model->getData('count',$tblUSERName,$whereCon );
							if($EmailExist == 1):
					    		throw new Exception(lang('EMAIL_ALREADY_EXIST'), 1);
							endif;
						endif;

						// Added validation for entered data..
				    	$tblName = "uw_users_verify";
				    	$mobileWhereCon['where']['country_code'] = $countryCode;  
				    	$mobileWhereCon['where']['users_mobile'] = (int)$mobile; 
				    	// $whereCon['where']['is_verified']  = "Y"; 
						$mobileVerified = $this->common_model->getData('single',$tblName ,$mobileWhereCon);

						// Added validation for entered data..
				    	$tblName = "uw_users_verify";
				    	$emailWhereCon['where']['users_email']  = $email; 
				    	// $whereCon['where']['is_verified']  = "Y"; 
						$emailVerified = $this->common_model->getData('single',$tblName ,$emailWhereCon);

						if( ( (!empty($emailVerified) && $emailVerified['is_verified'] == "N")  &&  (!empty($mobileVerified) && $mobileVerified['is_verified'] == "N")  )  || empty($emailVerified) && empty($mobileVerified)     ):
				    		throw new Exception(lang('MOBILE_EMAIL_NOT_VERIFIED'), 1);
						elseif(( !empty($emailVerified['is_verified']) &&  $emailVerified['is_verified'] == "N" ) && empty($mobileVerified) ):
				    	   throw new Exception(lang('EMAIL_NOT_VERIFIED'), 1);
				    	elseif( (!empty($mobileVerified['is_verified']) &&  $mobileVerified['is_verified'] == "N" ) && empty($emailVerified) ):
				    	   throw new Exception(lang('MOBILE_NUMBER_NOT_VERIFIED'), 1);
						endif;
					endif;

					// Saving Mobile/Email ..
					if( !empty($mobileVerified) || !empty($emailVerified) ):

						$Field = 'users_mobile';
						$value = (int)$mobile;
						if($email):
							$Field = 'users_email';
							$value = $email;
						endif;

						$param["users_id"] 	   = (int)$this->common_model->getNextSequence('uw_users');
						$param['users_seq_id'] = $this->geneal_model->getNextIdSequence('users_seq_id', 'Users');
						$param['referral_code']= $param['users_seq_id'];
						$param['pos_number']   = $param['users_seq_id'];
						$param['users_name']   = addslashes($usersName);
						$param['last_name']    = addslashes($lastName);
						$param['users_type']   = addslashes('Users');
						$param['users_email']  = addslashes($email);
						$param['country_code'] = addslashes($countryCode);
						$param['users_mobile'] = (int)$mobile;;
						$param['password'] 	   = md5($usersPassword);
						$param['totalArabianPoints'] 	 = 0;
						$param['availableArabianPoints'] = 0;
						$param['is_verified']  = 'Y';
						$param['is_mobile_verified']  = $mobileVerified['is_verified']?$mobileVerified['is_verified']:'N';
						$param['is_email_verified']  = $emailVerified['is_verified']?$emailVerified['is_verified']:'N';
						$param['device_type']     = $deviceType;
						$param['pos_device_id']   = $posDeviceID;

						// if($deviceType == 'ios' || $deviceType == 'android' ):
							$param['bind_person_id'] 	=  (int)"100000000000001";
							$param['bind_user_type'] 	=  "Admin";
							$param['bind_person_name'] 	=  "Admin";
						// endif;

						if($referredBy):
							$referralData =  $this->referredByfunction($referredBy);
							$param['referred_by'] =  new MongoDB\BSON\ObjectId($referralData);
							// $param['referrel_amount'] =  $USERDATA['referrel_amount'];
						endif;

						$param['latitude']	   = $usersLat;
						$param['longitude']	   = $usersLong;
						$param['address']	   = $usersAddress;
						$param['app_version']  = $appVersion;
						$param['login_token']  = $this->geneal_model->generatetoken();
						$param['token']        = base64_encode($USERDATA['users_id']);
					    $param['status']       = 'A';
						$param['created_at']   = date('Y-m-d H:i:s');;
						$param['creation_ip']  = $this->input->ip_address();

						$tableName = 'uw_users';
						$newUser   = $this->common_model->addData($tableName,$param);

						if(!empty($newUser)):
							if((!empty($emailVerified) && $emailVerified['is_verified'] == "Y")  &&  (!empty($mobileVerified) && $mobileVerified['is_verified'] == "Y")    ):
								// OVERWRITED DATA...
								$newUser['referred_by'] = $referralData;
								$referralData = $this->common_model->bonusPoints($newUser);
							endif;

					        $result_data['users_type'] 		= 'Users';
							$result_data['users_id']   		= $param["users_id"];
							$result_data['users_name'] 		= $param['users_name'];
							$result_data['last_name']  		= $param['last_name'];
							$result_data['country_code']  	= $param['country_code'];
							$result_data['users_mobile']  	= $param['users_mobile'];
							$result_data['users_email']  	= $param['users_email'];
							$result_data['status']  		= $param['status'];
							$result_data['is_verified']  	= $param['is_verified'];
							$result_data['login_token']  	= $param['login_token'];
							$result['userData'] 			= $result_data;
			                echo outPut(1,lang('SUCCESS_CODE'),lang('SIGNUP_SUCCESFULLY'),$result);
						else:
			    	   		throw new Exception(lang('USER_NOT_CREATED'), 1);
						endif;
					endif;
				endif;
			endif;
			
		} catch (Exception $e) {
			echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);
		}
	 }

	 /* * *********************************************************************
	 * * Function name  : referredByfunction
	 * * Developed By 	: Dilip Halder
	 * * Purpose        : This function used for signup
	 * * Date           : 13 JUNE 2022
	 * * Updated By     : Dilip halder
	 * * Date           : 07 FEBRUARY 2024
	 * * **********************************************************************/
	private function referredByfunction($referredBy ="")
	{	

		if(!empty($referredBy) ):

			$tblName     = "uw_users";
			$Fieldslist	 = array('_id','raffered_count','raffered_date','users_type','users_id');
			$referredBY  = (is_numeric($referredBy)) ? (int)$referredBy : $referredBy;
			$whereCon['where'] = array("referral_code" => $referredBY);
			$whereCon['where'] = array("status" => "A");
			$getReferralData   = $this->common_model->getParticularFieldByMultipleCondition($Fieldslist,$tblName,$whereCon);
			// $getReferralData['raffered_date'] = "2025-07-07";
			if($getReferralData['users_type'] == 'Users' && !empty($getReferralData['raffered_count']) && $getReferralData['raffered_date']  == date('Y-m-d') ):
	    		echo outPut(0,lang('SUCCESS_CODE'),lang('RAFFERALLED_LIMIT_REACHED'),$result);die();
			elseif($getReferralData['users_type'] == 'Users' && $getReferralData['raffered_date']  ): // == date('Y-m-d')
				$btcPrarm['raffered_count'] = (int)$getReferralData['raffered_count']+1 ;
				$btcPrarm['raffered_date']  = date('Y-m-d');
				$this->geneal_model->editData('uw_users',$btcPrarm,'users_id',(int)$getReferralData['users_id']);
			endif;

			if(!empty($getReferralData)):
				$GetReferralDID = $getReferralData['_id']['$id'];
				// $param['referred_by'] = new MongoDB\BSON\ObjectId($GetReferralDID);
				return $GetReferralDID;
				// $param['referrel_amount'] = 25;
			else:
	    		echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_REFERRAL_CODE'),$result);die();
			endif;
		endif;
	}


	/* * *********************************************************************
	 * * Function name : login
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for login
	 * * Date : 13 JUNE 2022
	 * * Updated By : Dilip Halder
	 * * Updated Date : 23-06-2023
	 * * Updated : added token variable.
	 * * **********************************************************************/
	public function login()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):
			
			if($this->input->post('users_email') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMAIL_EMPTY'),$result);
			elseif($this->input->post('users_password') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('PASSWORD_EMPTY'),$result);
			else:

				$where 			= array('users_mobile' => 'test');
				$UserInput 		= $this->input->post('users_email');
				$tblName 		=  'uw_users';
				$where 			=  is_numeric($UserInput) ? array('users_mobile' => (int)$UserInput)  : array('users_email'=> $UserInput );
				$userDetails 	=  $this->geneal_model->getOnlyOneData($tblName, $where);
				
 				if(!empty($userDetails) && isset($userDetails['password']) && md5($this->input->post('users_password')) == $userDetails['password'] && $userDetails['users_type'] == 'Users'):

					if($userDetails['status'] == 'A'):

						$posDeviceID = $this->input->post('pos_device_id');
						
						if(!empty($userDetails['pos_device_id'])  && $userDetails['pos_device_id'] !=  $posDeviceID && !empty($posDeviceID) ):
							echo outPut(0,lang('NOT_LOGIN_ACTION'),lang('POS_USER_DIFFERENT'),$result);die();
						endif;

						if(!empty($userDetails['users_device_id']) && $this->input->post('users_device_id') != $userDetails['users_device_id'] || $userDetails['users_device_id'] != "" && $this->input->post('users_device_id') == '' ):
							echo outPut(0,lang('NOT_LOGIN_ACTION'),lang('POS_NOT_MATCHED'),$result);die();
						endif;

						$param['login_token']			= 	$this->geneal_model->generatetoken();

						$users_device_id  				=	$this->input->post('device_id');
						$users_lat  					=	$this->input->post('users_lat');
						$users_long  					=	$this->input->post('users_long');
						$param["device_type"] 			=	$this->input->post('device_type');
					    $param["app_version"] 			=	$this->input->post('app_version');
						
						if(empty($userDetails['pos_device_id']) && $this->input->post('pos_device_id') ):
							$param['pos_device_id']			= 	$this->input->post('pos_device_id');
					    endif;
					    if(empty($userDetails['device_id']) && $this->input->post('device_id') ):
							$param['device_id']			= 	$users_device_id;
					    endif;

						$param['device_id']				= 	$users_device_id;
						$param['latitude']				= 	$users_lat;
						$param['longitude']				= 	$users_long;
						$param['token']					= 	base64_encode($userDetails['users_id']);

						$this->geneal_model->editData('uw_users',$param,'users_id',(int)$userDetails['users_id']);

						$tblName 		=  'uw_users';
						$where 			=  is_numeric($UserInput) ? array('users_mobile' => (int)$UserInput)  : array('users_email'=> $UserInput );
						$userResult 	=  $this->geneal_model->getOnlyOneData($tblName, $where);
						$result['userData'] 				=	$userResult;
						echo outPut(1,lang('SUCCESS_CODE'),lang('LOGIN_SUCCESS'),$result);
					elseif($userDetails['status'] == 'I' && $userDetails['is_verify'] == 'N'):
						$otp  		  = (int)rand(100000,999999);
						$mobile_no    = $userDetails['users_mobile'];
						$country_code = $userDetails['country_code'];
						$param['users_otp'] = 	$otp;
						$this->geneal_model->editData('uw_users',$param,'users_id',(int)$userDetails['users_id']);
						$this->sms_model->accountVerifyOTP($country_code, $mobile_no,$otp);
						if($userDetails['users_email']){
							$this->emailsendgrid_model->accountVerifyOTP($userDetails['users_email'],$otp );
						}
						$result['userData'] 				=	$userDetails;
						echo outPut(1,lang('SUCCESS_CODE'),lang('ACCOUNT_NOT_VERIFY'),$result);
					elseif($userDetails['status'] == 'I' && $userDetails['is_verify'] == 'Y'):
						echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_INACIVE'),$result);
					else:
						echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$result);
					endif;
				elseif(!empty($userDetails) && $userDetails['users_type'] != 'Users'):
					echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_LOGIN'),$result);
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('MOBILE_OR_PASS_INCORRECT'),$result);
				endif;

			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : login_New
	 * * Developed By  : Dilip Halder
	 * * Purpose       : This function used for login
	 * * Date          : 29 JUNE 2024
	 * * **********************************************************************/
	public function login_New()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 			= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):
			
			// if($this->input->post('users_email') == ''): 
			// 	echo outPut(0,lang('SUCCESS_CODE'),lang('EMAIL_EMPTY'),$result);
			// elseif($this->input->post('users_password') == ''): 
			// 	echo outPut(0,lang('SUCCESS_CODE'),lang('PASSWORD_EMPTY'),$result);
				$country_code 	= $this->input->post('country_code');
				$users_mobile 	= $this->input->post('users_mobile');
				$users_email 	= $this->input->post('users_email');
				$password       = md5($this->input->post('users_password'));

			if(
				$country_code == '' && $users_mobile != "" || 
				$country_code != '' && $users_mobile == "" ||   
				$country_code == '' && $users_mobile == "" && $users_email == ''
			) :

				echo outPut(0,lang('SUCCESS_CODE'),lang('USERDETAIL_EMPTY'),$result);

			elseif($password ==''):
				echo outPut(0,lang('SUCCESS_CODE'),lang('PASSWORD_EMPTY'),$result);
			else:
				
				if(!empty($country_code)):
					$where['where']['country_code'] = $country_code;
					$where['where']['users_mobile'] = (int)$users_mobile;
				else:
					$where['where']['users_email'] = $users_email ;
				endif;
					$where['where']['password'] = $password ;

				$Fieldslist   = array('users_type','users_id','users_name','last_name','country_code','users_mobile','users_email','status','login_token','token','store_name','password');
				$tblName      =  'uw_users';
				$userDetails  =  $this->common_model->getParticularFieldByMultipleCondition($Fieldslist ,$tblName, $where);

				if(!empty($userDetails) && isset($userDetails['password']) &&  $password == $userDetails['password'] && $userDetails['users_type'] !='Users' ):

					if($country_code == '+971' && $users_mobile == '545423435'):
					 $otp = (int)1111;
					else:
					 $otp = (int)rand(1000,9999);
					endif;
					$param['users_otp'] = 	$otp;
					$this->geneal_model->editData('uw_users',$param,'users_id',(int)$userDetails['users_id']);
					
					// OTP sending to number and Email...
					if($country_code !='' && $users_mobile !=''):
						$this->sms_model->accountVerifyOTP($country_code,$users_mobile,$otp);
					elseif($users_email):
					    $this->emailsendgrid_model->accountVerifyOTP($users_email,$otp);
					endif;

					echo outPut(1,lang('SUCCESS_CODE'),lang('OTP_SENT'),$result);
				elseif(!empty($userDetails) && $userDetails['users_type'] =='Users'):
					echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_LOGIN'),$result);
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('MOBILE_OR_PASS_INCORRECT'),$result);
				endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}
	
	/* * *********************************************************************
	 * * Function name : verifyOTP
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for OTP varification.
	 * * Date : 07 February 2024
	 * * **********************************************************************/
	public function verifyOTP()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):
 
				$country_code 	= $this->input->post('country_code');
				$users_mobile 	= $this->input->post('users_mobile');
				$users_email 	= $this->input->post('users_email');
				$otp       		= $this->input->post('otp');
				$password       = md5($this->input->post('users_password'));

			if(
				$country_code == '' && $users_mobile != "" || 
				$country_code != '' && $users_mobile == "" ||   
				$country_code == '' && $users_mobile == "" && $users_email == ''
			) :

				echo outPut(0,lang('SUCCESS_CODE'),lang('USERDETAIL_EMPTY'),$result);

			elseif($password ==''):
				echo outPut(0,lang('SUCCESS_CODE'),lang('PASSWORD_EMPTY'),$result);
			elseif($otp ==''):
				echo outPut(0,lang('SUCCESS_CODE'),lang('OTP_EMPTY'),$result);
			else:
				
				if(!empty($country_code)):
					$where['where']['country_code'] = $country_code;
					$where['where']['users_mobile'] = (int)$users_mobile;
				else:
					$where['where']['users_email']  = $users_email ;
				endif;
					$where['where']['users_otp']    = (int)$otp ;
					$where['where']['password']     = $password ;
				
				$Fieldslist   = array('users_type','users_id','users_name','last_name','country_code','users_mobile','users_email','status','login_token','token','store_name','password','pos_device_id');
				$tblName      =  'uw_users';
				$userDetails  =  $this->common_model->getParticularFieldByMultipleCondition($Fieldslist ,$tblName, $where);

				if(!empty($userDetails) && isset($userDetails['password']) &&  $password == $userDetails['password'] && !empty($otp)):


					$posDeviceID = $this->input->post('pos_device_id');
						
					if( $users_mobile != '545423435'):
						if(!empty($userDetails['pos_device_id'])  && $userDetails['pos_device_id'] !=  $posDeviceID && !empty($posDeviceID) ):
							echo outPut(0,lang('NOT_LOGIN_ACTION'),lang('POS_USER_DIFFERENT'),$result);die();
						endif;
					endif;

					$token 				  	= $this->geneal_model->generatetoken();
					$param['users_otp']   	= '';
					$param['token'] 	  	= $token;
					$param['login_token']   = $token;
					$param['pos_device_id'] = $posDeviceID;
					$param['app_name'] 	  	= $this->input->post('app_name');
					$param['device_type']	= $this->input->post('device_type');
					$param['app_version'] 	= $this->input->post('app_version');
					$param['last_login']  	= date('Y-m-d H:i:s');;
					$param['creation_ip'] 	= currentIp();
					$this->geneal_model->editData('uw_users',$param,'users_id',(int)$userDetails['users_id']);
					
					$Fieldslist   = array('users_type','users_id','users_name','last_name','country_code','users_mobile','users_email','status','login_token','token','store_name');

					if(!empty($country_code)):
						$where1['where']['country_code'] = $country_code;
						$where1['where']['users_mobile'] = (int)$users_mobile;
					else:
						$where1['where']['users_email']  = $users_email ;
					endif;
						$where1['where']['password']     = $password ;
					$userDetails  = $this->common_model->getParticularFieldByMultipleCondition($Fieldslist ,$tblName, $where1);

					$result       = $userDetails;
					echo outPut(1,lang('SUCCESS_CODE'),lang('OTP_VERIFIED'),$result);

				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_OTP'),$result);
				endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}
	
	// public function login1()
	// {	
	// 	$apiHeaderData 		=	getApiHeaderData();
	// 	$this->generatelogs->putLog('APP',logOutPut($_POST));
	// 	$result 							= 	array();	
	// 	if(requestAuthenticate(APIKEY,'POST')):
			
	// 		if($this->input->post('users_email') == ''): 
	// 			echo outPut(0,lang('SUCCESS_CODE'),lang('EMAIL_EMPTY'),$result);
	// 		elseif($this->input->post('users_password') == ''): 
	// 			echo outPut(0,lang('SUCCESS_CODE'),lang('PASSWORD_EMPTY'),$result);
	// 		else:

	// 			$userData = array();
	// 			if(is_numeric($this->input->post('users_email'))):
	// 				$where 			=	[ 'users_mobile' => (int)$this->input->post('users_email') ];
	// 				$tblName 		=	'uw_users';
	// 				$userDetails 	=	$this->geneal_model->getOnlyOneData($tblName, $where);
	// 				if(!empty($userDetails)): $userData = $userDetails; endif;
	// 			else:
	// 				$where 			=	[ 'users_email' => $this->input->post('users_email') ];
	// 				$tblName 		=	'uw_users';
	// 				$userDetails 	=	$this->geneal_model->getOnlyOneData($tblName, $where);
	// 				if(!empty($userDetails)): $userData = $userDetails; endif;
	// 			endif;

	// 			if(!empty($userData) && isset($userData['password']) && md5($this->input->post('users_password')) == $userData['password']):
	// 				if($userData['status'] == 'A'):

	// 					$posDeviceID = $this->input->post('pos_device_id');
						
	// 					if(!empty($userDetails['pos_device_id'])  && $userDetails['pos_device_id'] !=  $posDeviceID && !empty($posDeviceID) ):
	// 						echo outPut(0,lang('NOT_LOGIN_ACTION'),lang('POS_USER_DIFFERENT'),$result);die();
	// 					endif;

	// 					if(!empty($userData['users_device_id']) && $this->input->post('users_device_id') != $userData['users_device_id'] || $userData['users_device_id'] != "" && $this->input->post('users_device_id') == '' ):
	// 						echo outPut(0,lang('NOT_LOGIN_ACTION'),lang('POS_NOT_MATCHED'),$result);die();
	// 					endif;

	// 					$param['login_token']			= 	$this->geneal_model->generatetoken();

	// 					if($this->input->post('device_id') || $this->input->post('pos_device_id')):

	// 						$users_device_id  				=	$this->input->post('device_id');
	// 						$users_lat  					=	$this->input->post('users_lat');
	// 						$users_long  					=	$this->input->post('users_long');
	// 						$param["device_type"] 			=	$this->input->post('device_type');
	// 					    $param["app_version"] 			=	$this->input->post('app_version');
	// 						if(empty($userData['pos_device_id']) && $this->input->post('pos_device_id') ):
	// 							$param['pos_device_id']			= 	$this->input->post('pos_device_id');
	// 					    endif;
	// 					    if(empty($userData['device_id']) && $this->input->post('device_id') ):
	// 							$param['device_id']			= 	$users_device_id;
	// 					    endif;
	// 						$param['device_id']				= 	$users_device_id;
	// 						$param['latitude']				= 	$users_lat;
	// 						$param['longitude']				= 	$users_long;
	// 						$param['token']					= 	base64_encode($userData['users_id']);
	// 						$this->geneal_model->editData('uw_users',$param,'users_id',(int)$userData['users_id']);
	// 					endif;

	// 					$result['userData'] 				=	$userData;
	// 					echo outPut(1,lang('SUCCESS_CODE'),lang('LOGIN_SUCCESS'),$result);
	// 				elseif($userData['status'] == 'I' && $userData['is_verify'] == 'N'):
	// 					$otp  					= (int)rand(100000,999999);
	// 					$mobile_no = $userData['country_code'].$userData['users_mobile'];
	// 					$param['users_otp']				= 	$otp;
	// 					$this->geneal_model->editData('uw_users',$param,'users_id',(int)$userData['users_id']);
	// 					$this->sms_model->sendForgotPasswordOtpSmsToUser($mobile_no,$otp, $userData['country_code']);
	// 					if($userData['users_email']){
	// 						$this->emailsendgrid_model->sendRegistrationMailToUser($insert_data);
	// 					}
	// 					$result['userData'] 				=	$userData;
	// 					echo outPut(1,lang('SUCCESS_CODE'),lang('ACCOUNT_NOT_VERIFY'),$result);
	// 				elseif($userData['status'] == 'I' && $userData['is_verify'] == 'Y'):
	// 					echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_INACIVE'),$result);
	// 				else:
	// 					echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$result);
	// 				endif;
	// 			else:
	// 				echo outPut(0,lang('SUCCESS_CODE'),lang('MOBILE_OR_PASS_INCORRECT'),$result);
	// 			endif;
	// 		endif;
	// 	else:
	// 		echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
	// 	endif;
	// }

	/* * *********************************************************************
	 * * Function name : forgotPassword
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for forgot Password
	 * * Date : 07 February 2024
	 * * Updated By : Dilip Halder
	 * * Updated Date : 23-12-2022
	 * * **********************************************************************/
	// public function forgotPassword()
	// {	
	// 	$apiHeaderData 		=	getApiHeaderData();
	// 	$this->generatelogs->putLog('APP',logOutPut($_POST));
	// 	$result 							= 	array();	
	// 	if(requestAuthenticate(APIKEY,'POST')):
			
	// 		if($this->input->post('users_email') == ''): 
	// 			echo outPut(0,lang('SUCCESS_CODE'),lang('EMAIL_EMPTY'),$result);
	// 		else:
	// 			if(is_numeric($this->input->post('users_email'))):
	// 				$forget_by 	=	'MOBILE';
	// 				if(strlen($this->input->post('users_email')) >= 10){
	// 					$where 			=	[ 'users_mobile' => (int)$this->input->post('users_email') ];
	// 				}else{
	// 					$where 			=	[ 'users_mobile' => (float)$this->input->post('users_email') ];
	// 				}
	// 			else:
	// 				$where 			=	[ 'users_email' => $this->input->post('users_email') ];
	// 				$forget_by 		=	'EMAIL';
	// 			endif;
				
	// 			$tblName 		=	'uw_users';
	// 			$userDetails 	=	$this->geneal_model->getOnlyOneData($tblName, $where);
				
	// 			if(!empty($userDetails)):
	// 				if($userDetails['status'] == 'A'):
	// 					$param['users_otp']	  = (int)rand(1000,9999); 
	// 					$this->geneal_model->editData('uw_users',$param,'users_id',(int)$userDetails['users_id']);

	// 					$tableName	 = "uw_users";
	// 				    $Fields 	 = array('_id','users_id','users_email','country_code','users_mobile');
	// 				    $finalres 	 = $this->common_model->getSingleDataByParticularField($Fields,$tableName,'users_id',(int)$userDetails['users_id']);

	// 					//$this->emailtemplate_model->sendForgotpasswordMailToUser($finalres);
	// 					if($userDetails['users_email']):
	// 						$this->emailsendgrid_model->accountVerifyOTP($userDetails['users_email'],$param['users_otp']);
	// 					endif;
						
	// 					$country_code = $this->input->post('country_code');

	// 					if(empty($country_code)):
	// 						$country_code = $userDetails['country_code'];
	// 					endif;
						
	// 					$mobile_no 	= $userDetails['users_mobile'];
	// 					$this->sms_model->accountVerifyOTP($country_code , $mobile_no,$param['users_otp']);

	// 					$result['userData'] 				=	$finalres;
	// 					echo outPut(1,lang('SUCCESS_CODE'),lang('OTP_SENT').$this->input->post('users_email'),$result);
	// 				else:
	// 					echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$result);
	// 				endif;
	// 			else:
	// 				if($forget_by == 'MOBILE'):
	// 					echo outPut(0,lang('SUCCESS_CODE'),lang('RECHARGE_PHONE_ERROR'),$result);
	// 				else:
	// 					echo outPut(0,lang('SUCCESS_CODE'),lang('RECHARGE_EMAIL_ERROR'),$result);
	// 				endif;
	// 			endif;

	// 		endif;
	// 	else:
	// 		echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
	// 	endif;
	// }
	public function forgotPassword()
	{
		try {

			$apiHeaderData 		=	getApiHeaderData();
			$this->generatelogs->putLog('APP',logOutPut($_POST));
			$result 							= 	array();	
			if(requestAuthenticate(APIKEY,'POST')):
				
				//input Fields..
				$usersMobileEmail = $this->input->post('users_email');      // Before used only this input fields.
				$countryCode      = $this->input->post('country_code');     // added new 
				$usersMobile       = $this->input->post('mobile');           // added new 
				$vericationType   = $this->input->post('verificationType'); // added new 
				
				if(empty($usersMobileEmail) && (empty($countryCode) && empty($usersMobile))):
					throw new Exception(lang('EMPTY_NUMBER_EMAIL'), 1);
				elseif(empty($usersMobileEmail) && (empty($countryCode) && !empty($usersMobile)) ):
					throw new Exception(lang('EMPTY_COUNTRYCODE'), 1);
				elseif(empty($usersMobileEmail) && (!empty($countryCode) && empty($usersMobile)) ):
					throw new Exception(lang('EMPTY_USERMOBILE'), 1);
				else:

				  if(!empty($usersMobileEmail)):
				  	$where =  is_numeric($usersMobileEmail) ?  array('users_mobile' =>  (int)$usersMobileEmail) : array('users_email' => $usersMobileEmail) ;
				  	$whereCon['where'] = $where;
				  elseif(empty($usersMobileEmail) && (!empty($countryCode) && !empty($usersMobile))):
			  		$whereCon['where']['country_code'] = $countryCode;
			  		$whereCon['where']['users_mobile'] = (int)$usersMobile;
				  endif;

				    $tblName     = 'uw_users';
				    $userDetails = $this->common_model->getData('single',$tblName,$whereCon);
				    if( !empty($userDetails) && $userDetails['status'] == "A" ):

				    	$otp = (int)rand(1000,9999); ;
				    	$param['users_otp']	 = $otp;
						$this->geneal_model->editData('uw_users',$param,'users_id',(int)$userDetails['users_id']);

				    	if(!empty($countryCode) && !empty($usersMobile) && $vericationType == 'Whatsapp' ):
				    		$this->sms_model->accountWhatsappVerifyOTP($countryCode,$usersMobile,$otp);
				    	elseif((!empty($usersMobileEmail)) && $vericationType == "Email" ):
						   $this->emailsendgrid_model->accountVerifyOTP($usersMobileEmail,$otp);
				    	elseif(!empty($countryCode) && !empty($usersMobile) && $vericationType == 'SMS'):
							$this->sms_model->accountVerifyOTP($countryCode,$usersMobile,$otp);
						elseif(!empty($usersMobileEmail) && is_numeric($usersMobileEmail) ):
							$this->sms_model->accountVerifyOTP($countryCode,$usersMobile,$otp);
				    	endif;

				    	$tableName	   = "uw_users";
					    $Fields 	 = array('_id','users_id','users_email','country_code','users_mobile');
					    $finalres 	 = $this->common_model->getSingleDataByParticularField($Fields,$tableName,'users_id',(int)$userDetails['users_id']);
				    	$result['userData'] =	$finalres;
						echo outPut(1,lang('SUCCESS_CODE'),lang('OTP_SENT').$this->input->post('users_email'),$result);

				    elseif( !empty($userDetails) &&  ( $userDetails['status'] == "I" || $userDetails['status'] == "D" || $userDetails['status'] == "B"   )  ):
						throw new Exception(lang('ACCOUNT_BLOCKED'), 1);
				    else:
						throw new Exception(lang('INVALID_LOGIN'), 1);
				    endif;

				endif;
			else:
				echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
			endif;
			
		} catch (Exception $e) {
			echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);
		}
	}

	/* * *********************************************************************
	 * * Function name : resetPassword
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for reset Password
	 * * Date : 07 February 2024
	 * * Updated By : Dilip Halder
	 * * Updated Date : 23-12-2022
	 * * **********************************************************************/
	// public function resetPassword()
	// {	
	// 	$apiHeaderData 		=	getApiHeaderData();
	// 	$this->generatelogs->putLog('APP',logOutPut($_POST));
	// 	$result 							= 	array();	
	// 	if(requestAuthenticate(APIKEY,'POST')):
			
	// 		if($this->input->post('users_email') == ''): 
	// 			echo outPut(0,lang('SUCCESS_CODE'),lang('EMAIL_EMPTY'),$result);
	// 		elseif($this->input->post('users_otp') == ''): 
	// 			echo outPut(0,lang('SUCCESS_CODE'),lang('OTP_EMPTY'),$result);
	// 		elseif($this->input->post('new_password') == ''): 
	// 			echo outPut(0,lang('SUCCESS_CODE'),lang('PASSWORD_EMPTY'),$result);
	// 		else:
	// 			if(is_numeric($this->input->post('users_email'))):
	// 				if(strlen($this->input->post('users_email')) >= 10):
	// 					$where 			=	[ 'users_mobile' => (int)$this->input->post('users_email') ];
	// 				else:
	// 					$where 			=	[ 'users_mobile' => (float)$this->input->post('users_email') ];
	// 				endif;
	// 			else:
	// 				$where 			=	[ 'users_email' => $this->input->post('users_email') ];
	// 			endif;
	// 			$tblName 		=	'uw_users';
	// 			$userDetails 	=	$this->geneal_model->getOnlyOneData($tblName, $where);
	// 			$userDetails['password'] = "";
	// 			//print_r($userDetails);die();
	// 			if(!empty($userDetails) && isset($userDetails['users_otp']) && $this->input->post('users_otp') == $userDetails['users_otp']):
	// 				if($userDetails['status'] == 'A'):
	// 					$param['users_otp']		= 	'';	
	// 					$param['password']		=	md5($this->input->post('new_password'));
	// 					$param['login_token']	= 	$this->geneal_model->generatetoken();
	// 					$this->geneal_model->editData('uw_users',$param,'users_id',(int)$userDetails['users_id']);

	// 					$w['where'] = array('users_id'=>(int)$userDetails['users_id']);
	// 					$finalres = $this->geneal_model->getData2('single','uw_users',$w);
	// 					$finalres['password'] = "";
	// 					$result['userData'] 	=	$finalres;
	// 					if($finalres['users_email']):
	// 						$this->emailsendgrid_model->sendSuccessResetPasswordMailToUser($finalres);
	// 					endif;
	// 					$mobile_no = $finalres['country_code'].$finalres['users_mobile'];
	// 					$this->sms_model->sendSuccessResetPasswordSmsToUser($mobile_no);
	// 					echo outPut(1,lang('SUCCESS_CODE'),lang('PASS_CHANGE_SUCCESS'),$result);
	// 				else:
	// 					echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$result);
	// 				endif;
	// 			else:
	// 				echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_email_otp'),$result);
	// 			endif;
	// 		endif;
	// 	else:
	// 		echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
	// 	endif;
	// }

	/* * *********************************************************************
	 * * Function name : resetPassword
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for reset Password
	 * * Date : 07 February 2024
	 * * Updated By : Dilip Halder
	 * * Updated Date : 23-12-2022
	 * * **********************************************************************/
	public function resetPassword()
	{
		try {

			$apiHeaderData 		=	getApiHeaderData();
			$this->generatelogs->putLog('APP',logOutPut($_POST));
			$result 							= 	array();	
			if(requestAuthenticate(APIKEY,'POST')):

				//input Fields..
				$usersMobileEmail = $this->input->post('users_email');      // Before used only this input fields.
				$countryCode      = $this->input->post('country_code');     // added new 
				$usersMobile      = $this->input->post('mobile');           // added new 
				$usersOtp         = $this->input->post('users_otp');          
				$newPassword      = $this->input->post('new_password');           
				

				
				if(empty($usersMobileEmail) && (empty($countryCode) && empty($usersMobile))):
					throw new Exception(lang('EMPTY_NUMBER_EMAIL'), 1);
				elseif(empty($usersMobileEmail) && (empty($countryCode) && !empty($usersMobile)) ):
					throw new Exception(lang('EMPTY_COUNTRYCODE'), 1);
				elseif(empty($usersMobileEmail) && (!empty($countryCode) && empty($usersMobile)) ):
					throw new Exception(lang('EMPTY_USERMOBILE'), 1);
				else:

				  if(!empty($usersMobileEmail)):
				  	$where =  is_numeric($usersMobileEmail) ?  array('users_mobile' =>  (int)$usersMobileEmail) : array('users_email' => $usersMobileEmail) ;
				  	$whereCon['where'] = $where;
				  elseif(empty($usersMobileEmail) && (!empty($countryCode) && !empty($usersMobile))):
			  		$whereCon['where']['country_code'] = $countryCode;
			  		$whereCon['where']['users_mobile'] = (int)$usersMobile;
				  endif;

				    $tblName     = 'uw_users';
				    $userDetails = $this->common_model->getData('single',$tblName,$whereCon);
				    if(!empty($userDetails) && $userDetails['status'] == "A"  && $usersOtp == $userDetails['users_otp']):
				    	$param['users_otp'] = "";
				    	$param['password']  = md5($newPassword);
						$this->common_model->editData('uw_users',$param,'users_id',(int)$userDetails['users_id']);
 

				    	$tableName	   = "uw_users";
					    $Fields 	 = array('_id','users_id','users_email','country_code','users_mobile');
					    $finalres 	 = $this->common_model->getSingleDataByParticularField($Fields,$tableName,'users_id',(int)$userDetails['users_id']);
						$finalres['password'] = "";
				    	$result['userData']   =	$finalres;
						
				    	if($finalres['users_email']):
							$this->emailsendgrid_model->sendSuccessResetPasswordMailToUser($finalres);
						endif;
						$mobile_no = $finalres['country_code'].$finalres['users_mobile'];
						if($mobile_no):
							$this->sms_model->sendSuccessResetPasswordSmsToUser($mobile_no);
						endif;
						echo outPut(1,lang('SUCCESS_CODE'),lang('PASS_CHANGE_SUCCESS'),$result);
					elseif(!empty($userDetails) && $userDetails['status'] == "A"  && $usersOtp != $userDetails['users_otp']):
						throw new Exception(lang('INVALID_OTP'), 1);
				    elseif( !empty($userDetails) &&  ( $userDetails['status'] == "I" || $userDetails['status'] == "D" || $userDetails['status'] == "B"   )  ):
						throw new Exception(lang('ACCOUNT_BLOCKED'), 1);
				    else:
						throw new Exception(lang('INVALID_LOGIN'), 1);
				    endif;
				endif;
			else:
				throw new Exception(lang('FORBIDDEN_MSG'), 1);
			endif;
				 
		} catch (Exception $e) {
			echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);
		}
	}

	/* * *********************************************************************
	 * * Function name  : getProfileData
	 * * Developed By 	: Dilip Halder
	 * * Purpose  		: This function used for get Profile Data
	 * * Date 			: 07 February 2024
	 * * **********************************************************************/
	public function getProfileData()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'GET')):
			
			if($this->input->get('users_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			else:

				// $Fieldslist     = array('users_type','users_name','last_name','users_email','country_code','users_mobile','pickup_point_holder','area','commission_percentage','store_name','bind_person_id','bind_person_name','bind_user_type','pos_number','pos_device_id','device_id','users_device_id','totalArabianPoints','availableArabianPoints','referral_code','users_id','users_seq_id','creation_ip','created_at','created_by','is_verify','status','update_date','update_ip','updated_by','app_version','device_type','latitude','login_token','longitude','token','redeemed_points','updated_at','users_otp','app_name','last_login','show_raffle_campaign','is_mobile_verified','is_email_verified','enable_raffle_entries','otp_generated_at','summarypin_verified_at','enable_summary_otp','show_lotto_campaign');
				$Fieldslist     = array(
					'users_type','availableReachargePoints','users_name','last_name','users_email','country_code','users_mobile','pickup_point_holder','area','commission_percentage','store_name',
					'bind_person_id','bind_person_name','bind_user_type','pos_number','pos_device_id','device_id','users_device_id','totalArabianPoints','availableArabianPoints',
					'referral_code','users_id','users_seq_id','creation_ip','created_at','created_by','is_verify','status','update_date','update_ip','updated_by','app_version','device_type',
					'latitude','login_token','longitude','token','redeemed_points','updated_at','users_otp','app_name','last_login','show_raffle_campaign','is_mobile_verified','is_email_verified',
					'enable_raffle_entries','otp_generated_at','summarypin_verified_at','enable_summary_otp','enable_tambola_games','enable_hourly_games','show_lotto_campaign'
				);
				$tblName 		= 'uw_users';
				$userDetails 	= $this->common_model->getSingleDataByParticularField($Fieldslist,$tblName,'users_id', (int)$this->input->get('users_id'));
				$count 			= 0;
				if($this->input->get('users_id')):
					$tblName 					=	'uw_emirate_collection_point';
					$user_id 					=	$this->input->get('users_id');
					$wcon['where']				=	array('users_id' => (int)$user_id); 
					$collectionPointList		=	$this->geneal_model->getData2('multiple', $tblName, $wcon);
	
					if(empty($collectionPointList)){
						$count = 0;
					}
					//$orWhere = array();
					
					
					foreach ($collectionPointList as $key => $items) {
						$where6['where']		=	array(
													'collection_point_id' => (string)$items['collection_point_id'],
													'collection_status' => 'Pending to collect'
												);
						$short = array('collection_status'=> -1 ,'sequence_id' => -1);
						$orderlist				=	$this->geneal_model->getproductrequestList('count','uw_orders',$where6,$short);
						$count = $count + $orderlist;
					}
				endif;

				if(!empty($userDetails)):
					if($userDetails['status'] == 'A'):
						$result['userData'] 				=	$userDetails;
						$result['product_request_count'] 	= $count;
						echo outPut(1,lang('SUCCESS_CODE'),lang('get_profile_success'),$result);
					else:
						echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$result);
					endif;
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_INCORRECT'),$result);
				endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	 
	/* * *********************************************************************
	 * * Function name : updateProfile
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for update Profile
	 * * Date : 07 February 2024
	 * * Updated By : Dilip Halder
	 * * Date : 23 February 2023
	 * * **********************************************************************/
	// public function updateProfile()
	// {	
	// 	try {
	// 			$apiHeaderData = getApiHeaderData();
	// 			$this->generatelogs->putLog('APP',logOutPut($_POST));
	// 			$result 	   = array();	
	// 			if(requestAuthenticate(APIKEY,'POST')):

	// 				$userID     		= $this->input->get('users_id');
	// 				$firstName   		= $this->input->post('users_name');
	// 				$lastName    		= $this->input->post('last_name');
	// 				$usersEmail 		= $this->input->post('users_email');
	// 				$countryCode 		= $this->input->post('country_code');
	// 				$usersMobile 	    = $this->input->post('users_mobile');
	// 				$notification       = $this->input->post('notification');
	// 				$smsNotification   = $this->input->post('sms_notification');
	// 				$emailNotification = $this->input->post('email_notification');

	// 				if(empty($userID)): 
	// 					throw new Exception(lang('USER_ID_EMPTY'), 1);
	// 				elseif($firstName == ''): 
	// 					throw new Exception(lang('NAME_EMPTY'), 1);
	// 				// elseif($lastName == ''): 
	// 					// throw new Exception(lang('LASTNAME_EMPTY'), 1);
	// 				elseif($usersEmail == ''): 
	// 					throw new Exception(lang('EMAIL_EMPTY'), 1);
	// 				elseif($countryCode == ''): 
	// 					throw new Exception(lang('COUNTRY_CODE_EMPTY'), 1);
	// 				elseif($usersMobile == ''): 
	// 					throw new Exception(lang('PHONE_EMPTY'), 1);
	// 				else:

	// 					$tblName 	 = 'uw_users';
	// 					$where 		 = array('users_id' => (int)$userID);
	// 					$userDetails = $this->geneal_model->getOnlyOneData($tblName, $where);

	// 					if(!empty($userDetails) && $userDetails['status']  == 'A' ):
	// 						$mobileVerified = "N";
	// 						$emailVerified  = "N";
							
	// 						if( !empty($usersEmail) &&  $usersEmail != $userDetails['users_email'] || $usersEmail == $userDetails['users_email'] ):

	// 							$emailWhere1['users_id']    =  array('$ne' => (int)$userID);
	// 							$emailWhere1['users_email'] = $usersEmail;
	// 		   	 				$duplicateEmail = $this->common_model->checkDuplicate('uw_users',$emailWhere1);

	// 		   	 				$emailWhere['users_email'] = $usersEmail;
	// 		   	 				$emailWhere['is_verified'] = "Y";
	// 		   	 				$emailVerified = $this->common_model->checkDuplicate('uw_users_verify',$emailWhere);

	// 						endif;
							
	// 						if(!empty($usersMobile) &&  $usersMobile != $userDetails['users_mobile'] || $usersMobile == $userDetails['users_mobile'] ):
	// 							$mobileWhere1['users_id']     = array('$ne' => (int)$userID);
	// 							$mobileWhere1['country_code'] = $countryCode;
	// 							$mobileWhere1['users_mobile'] = (int)$usersMobile;
	// 		   	 				$duplicateMobile = $this->common_model->checkDuplicate('uw_users',$mobileWhere1);

	// 		   	 				$mobileWhere['country_code'] = $countryCode;
	// 		   	 				$mobileWhere['users_mobile'] = (int)$usersMobile;
	// 		   	 				$mobileWhere['is_verified']  = "Y";
	// 		   	 				$mobileVerified = $this->common_model->checkDuplicate('uw_users_verify',$mobileWhere);
	// 		   	 				// Added validation for entered data..
	// 						endif;

	// 						if($mobileVerified == '0' && $emailVerified == '0'):
	// 							throw new Exception(lang('MOBILE_EMAIL_NOT_VERIFIED'), 1);
	// 						endif;

	// 						if($duplicateEmail >= 1):
	// 							throw new Exception(lang('EMAIL_ALREADY_EXIST'), 1);
	// 					  	else:
	// 						   $is_email_verified =  $emailVerified == 1 ? 'Y': 'N';
	// 						   $param['users_email'] = $usersEmail;
	// 						   $param['is_email_verified'] = $is_email_verified;
	// 						endif;

	// 						if($duplicateMobile >= 1):
	// 							throw new Exception(lang('PHONE_ALREADY_EXIST'), 1);
	// 						else:
	// 						   $param['users_mobile']       = (int)$usersMobile;
	// 						   $is_mobile_verified = $mobileVerified == 1 ? 'Y': 'N';
	// 						   $param['is_mobile_verified'] = $is_mobile_verified;
	// 						endif;

	// 						    if($firstName)       : $param['users_name']       = $firstName;  endif;
	// 						    if($lastName)        : $param['last_name']        = $lastName;  endif;
	// 						    if($countryCode)     : $param['country_code']     = $countryCode;  endif;
	// 						    if($notification)    : $param['notification']     = $notification; endif;
	// 						    if($smsNotification) : $param['sms_notification'] = $smsNotification; endif;
	// 						    if($emailNotification) : $param['email_notification'] = $emailNotification; endif;
	// 						   $param['updated_at']  = date('Y-m-d H:i');
	// 						   $param['updated_ip']  = currentIp();

	// 						   $this->common_model->editData('uw_users',$param,'users_id',(int)$userID );
	// 						   $Fieldslist         = array('users_id','referral_code','pos_number','users_name','last_name','users_type','users_email','country_code','users_mobile','totalArabianPoints','availableArabianPoints','is_verified','is_mobile_verified','is_email_verified','status','login_token','token');
	// 						   $finalres           = $this->common_model->getSingleDataByParticularField($Fieldslist,'uw_users','users_id',(int)$userID);
	// 						   $result['userData'] = $finalres;
	// 						   echo outPut(1,lang('SUCCESS_CODE'),lang('PROFILE_UPDATED'),$result);
	// 					elseif($userDetails['status']  == 'I' || $userDetails['status']  == 'D' ):
	// 						throw new Exception(lang('USER_ID_INCORRECT'), 1);
	// 					else:
	// 						throw new Exception(lang('USER_ID_INCORRECT'), 1);
	// 					endif;
	// 				endif;
	// 			else:
	// 			   throw new Exception(lang('FORBIDDEN_MSG'), 1);
	// 			endif;
	// 	} catch (Exception $e) {
	// 		echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);
	// 	}
	// }
	
	/* * *********************************************************************
	 * * Function name : updateProfile
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for update Profile
	 * * Date : 07 February 2024
	 * * Updated By : Dilip Halder
	 * * Date : 23 February 2023
	 * * **********************************************************************/
	public function updateProfile()
	{	
		try {
				$apiHeaderData = getApiHeaderData();
				$this->generatelogs->putLog('APP',logOutPut($_POST));
				$result 	   = array();	
				if(requestAuthenticate(APIKEY,'POST')):

					$userID     		= $this->input->get('users_id');
					$firstName   		= $this->input->post('users_name');
					$lastName    		= $this->input->post('last_name');
					$usersEmail 		= $this->input->post('users_email');
					$countryCode 		= $this->input->post('country_code');
					$usersMobile 	    = $this->input->post('users_mobile');
					$notification       = $this->input->post('notification');
					$smsNotification   = $this->input->post('sms_notification');
					$emailNotification = $this->input->post('email_notification');

					if(empty($userID)): 
						throw new Exception(lang('USER_ID_EMPTY'), 1);
					elseif($firstName == ''): 
						throw new Exception(lang('NAME_EMPTY'), 1);
					// elseif($lastName == ''): 
						// throw new Exception(lang('LASTNAME_EMPTY'), 1);
					elseif($usersEmail == ''): 
						throw new Exception(lang('EMAIL_EMPTY'), 1);
					elseif($countryCode == ''): 
						throw new Exception(lang('COUNTRY_CODE_EMPTY'), 1);
					elseif($usersMobile == ''): 
						throw new Exception(lang('PHONE_EMPTY'), 1);
					else:

						$allowedDomains = array('gmail.com', 'yahoo.com', 'yahoo.co.in');
						$emailDomain    = substr(strrchr($usersEmail, "@"), 1);
						if (!in_array(strtolower($emailDomain), $allowedDomains)  || substr_count($usersEmail, ".") > 2 ):
							throw new Exception(lang('INVALID_EMAILID'), 1);
						endif;

						$tblName 	 = 'uw_users';
						$where 		 = array('users_id' => (int)$userID);
						$userDetails = $this->geneal_model->getOnlyOneData($tblName, $where);

						if(!empty($userDetails) && $userDetails['status']  == 'A' ):
							$mobileVerified = "N";
							$emailVerified  = "N";
							if(
								!empty($usersEmail) &&  $usersEmail != $userDetails['users_email'] || 
								$usersEmail == $userDetails['users_email'] && $userDetails['is_email_verified'] == 'N'
							):

								$emailWhere1['users_id']    =  array('$ne' => (int)$userID);
								$emailWhere1['users_email'] = $usersEmail;
			   	 				$duplicateEmail = $this->common_model->checkDuplicate('uw_users',$emailWhere1);

			   	 				$emailWhere['users_email'] = $usersEmail;
			   	 				$emailWhere['is_verified'] = "Y";
			   	 				$emailVerified = $this->common_model->checkDuplicate('uw_users_verify',$emailWhere);

							endif;
							
							if(!empty($usersMobile) &&  $usersMobile != $userDetails['users_mobile'] ||
								$usersMobile == $userDetails['users_mobile'] && $userDetails['is_mobile_verified'] == 'N'
							):
								$mobileWhere1['users_id']     = array('$ne' => (int)$userID);
								$mobileWhere1['country_code'] = $countryCode;
								$mobileWhere1['users_mobile'] = (int)$usersMobile;
			   	 				$duplicateMobile = $this->common_model->checkDuplicate('uw_users',$mobileWhere1);

			   	 				$mobileWhere['country_code'] = $countryCode;
			   	 				$mobileWhere['users_mobile'] = (int)$usersMobile;
			   	 				$mobileWhere['is_verified']  = "Y";
			   	 				$mobileVerified = $this->common_model->checkDuplicate('uw_users_verify',$mobileWhere);
			   	 				// Added validation for entered data..
							endif;

							if($mobileVerified == '0' && $emailVerified == '0'):
								throw new Exception(lang('MOBILE_EMAIL_NOT_VERIFIED'), 1);
							endif;

							if($duplicateEmail >= 1):
								throw new Exception(lang('EMAIL_ALREADY_EXIST'), 1);
						  	elseif($usersEmail != $userDetails['users_email'] || $userDetails['is_email_verified'] == "N"):
							   $is_email_verified =  $emailVerified == 1 ? 'Y': 'N';
							   $param['users_email'] = $usersEmail;
							   $param['is_email_verified'] = $is_email_verified;
							endif;

							if($duplicateMobile >= 1):
								throw new Exception(lang('PHONE_ALREADY_EXIST'), 1);
							elseif($usersMobile != $userDetails['users_mobile'] || $userDetails['is_mobile_verified'] == "N"):
							   $param['users_mobile']       = (int)$usersMobile;
							   $is_mobile_verified = $mobileVerified == 1 ? 'Y': 'N';
							   $param['is_mobile_verified'] = $is_mobile_verified;
							endif;

							    if($firstName)       : $param['users_name']       = $firstName;  endif;
							    if($lastName)        : $param['last_name']        = $lastName;  endif;
							    if($countryCode)     : $param['country_code']     = $countryCode;  endif;
							    if($notification)    : $param['notification']     = $notification; endif;
							    if($smsNotification) : $param['sms_notification'] = $smsNotification; endif;
							    if($emailNotification) : $param['email_notification'] = $emailNotification; endif;
							   $param['updated_at']  = date('Y-m-d H:i');
							   $param['updated_ip']  = currentIp();

							   $this->common_model->editData('uw_users',$param,'users_id',(int)$userID );
							   $Fieldslist         = array('users_id','referral_code','pos_number','users_name','last_name','users_type','users_email','country_code','users_mobile','totalArabianPoints','availableArabianPoints','is_verified','is_mobile_verified','is_email_verified','status','login_token','token');
							   $finalres           = $this->common_model->getSingleDataByParticularField($Fieldslist,'uw_users','users_id',(int)$userID);
							   $result['userData'] = $finalres;
							   echo outPut(1,lang('SUCCESS_CODE'),lang('PROFILE_UPDATED'),$result);
						elseif($userDetails['status']  == 'I' || $userDetails['status']  == 'D' ):
							throw new Exception(lang('USER_ID_INCORRECT'), 1);
						else:
							throw new Exception(lang('USER_ID_INCORRECT'), 1);
						endif;
					endif;
				else:
				   throw new Exception(lang('FORBIDDEN_MSG'), 1);
				endif;
		} catch (Exception $e) {
			echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);
		}
	}

	/* * *********************************************************************
	 * * Function name : updateNetworkUsage
	 * * Developed By  : Dilip Halder
	 * * Purpose       : This function used for update Profile
	 * * Date          : 07 February 2024
	 * * **********************************************************************/
	public function updateNetworkUsage()
	{	
		try {
			 $apiHeaderData = getApiHeaderData();
			 $this->generatelogs->putLog('APP',logOutPut($_POST));
			 $result 	   = array();	
			 if(requestAuthenticate(APIKEY,'POST')):

				$userID     		= $this->input->post('user_id');
				$wifiUsage 			= $this->input->post('wifi_usage');
				$mobileDataUsage 	= $this->input->post('mobile_data_usage');
				$currentDataMode 	= $this->input->post('current_datamode');

				if(empty($userID)): 
					throw new Exception(lang('USER_ID_EMPTY'), 1);
				elseif(empty($currentDataMode)): 
					throw new Exception(lang('EMPTY_CURRENT_DATAMODE'), 1);
				elseif( $currentDataMode == "wifi" && empty($wifiUsage)): 
					throw new Exception(lang('EMPTY_WIFIUSAGE'), 1);
				elseif( $currentDataMode == "mobile" && empty($mobileDataUsage)): 
					throw new Exception(lang('EMPTY_MOBILEDATAUSAGE'), 1);
				else:
					$tblName 	 = 'uw_users';
					$where 		 = array('users_id' => (int)$userID);
					$userDetails = $this->geneal_model->getOnlyOneData($tblName, $where);
					// echo "<pre>";print_r($userDetails);die();
					if(!empty($userDetails) && $userDetails['status']  == 'A' ):
					   // Updated In users profile..
					    $param['current_datamode']  = $currentDataMode;
					    $this->common_model->editData('uw_users',$param ,'users_id',(int)$userID);
					    
					    $param['users_id']          = (int)$userID;
					    $param['wifi_usage']        = (float)$wifiUsage;
					    $param['mobile_data_usage'] = (float)$mobileDataUsage;
					    $param['created_at']        = date('Y-m-d H:i');
					    $this->common_model->addData('uw_data_usage',$param );
					    echo outPut(1,lang('SUCCESS_CODE'),lang('PROFILE_UPDATED'),$result);
						
					elseif($userDetails['status']  == 'I' || $userDetails['status']  == 'D' ):
						throw new Exception(lang('USER_ID_INCORRECT'), 1);
					else:
						throw new Exception(lang('USER_ID_INCORRECT'), 1);
					endif;
				endif;
			 else:
			   throw new Exception(lang('FORBIDDEN_MSG'), 1);
			 endif;
		} catch (Exception $e) {
			echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);
		}
	}

	/* * *********************************************************************
	 * * Function name : changePassword
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for change Password
	 * * Date : 07 February 2024
	 * * **********************************************************************/
	public function changePassword()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):
			
			if($this->input->get('users_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			elseif($this->input->post('old_password') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('OLD_PASSWORD_EMPTY'),$result);
			elseif($this->input->post('new_password') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('PASSWORD_EMPTY'),$result);
			else:

				$where 			=	[ 'users_id' => (int)$this->input->get('users_id') ];
				$tblName 		=	'uw_users';
				$userDetails 	=	$this->geneal_model->getOnlyOneData($tblName, $where);

				if(!empty($userDetails)):
					if($userDetails['status'] == 'A'):

						if(isset($userDetails['password']) && md5($this->input->post('old_password')) == $userDetails['password']):

							$param['password']		=	md5($this->input->post('new_password'));
							$this->geneal_model->editData('uw_users',$param,'users_id',(int)$this->input->get('users_id'));

							$finalres = $this->geneal_model->getDataByParticularField('uw_users','users_id',(int)$this->input->get('users_id'));
							$result['userData'] 		=	$finalres;
							echo outPut(1,lang('SUCCESS_CODE'),lang('PASS_CHANGE_SUCCESS'),$result);
						else:
							echo outPut(0,lang('SUCCESS_CODE'),lang('CHANGE_PASS_ERROR'),$result);
						endif;
					else:
						echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$result);
					endif;
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_INCORRECT'),$result);
				endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : refreshPoint
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for refresh Point
	 * * Date : 07 February 2024
	 * * **********************************************************************/
	public function refreshPoint()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'GET')):
			
			if($this->input->get('users_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			else:

				$where 			=	[ 'users_id' => (int)$this->input->get('users_id') ];
				$tblName 		=	'uw_users';
				$userDetails 	=	$this->geneal_model->getOnlyOneData($tblName, $where);

				if(!empty($userDetails)):
					if($userDetails['status'] == 'A'):
						$result['userData'] 				=	$userDetails;
						echo outPut(1,lang('SUCCESS_CODE'),lang('refresh_point_success'),$result);
					else:
						echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$result);
					endif;
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_INCORRECT'),$result);
				endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : getAddress
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for get Address
	 * * Date : 07 February 2024
	 * * **********************************************************************/
	public function getAddress()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'GET')):
			
			if($this->input->get('users_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			else:

				$where 			=	[ 'users_id' => (int)$this->input->get('users_id') ];
				$tblName 		=	'uw_users';
				$userDetails 	=	$this->geneal_model->getOnlyOneData($tblName, $where);

				if(!empty($userDetails)):
					if($userDetails['status'] == 'A'):
						$where1 		=	[ 'user_id' => (int)$this->input->get('users_id') ];
						$tblName1 		=	'uw_diliveryAddress';
						$order1 		=	[];
						$userAddress 	=	$this->geneal_model->getData($tblName1, $where1, $order1);

						$result['userAddress'] 	=	$userAddress;
						echo outPut(1,lang('SUCCESS_CODE'),lang('get_address_success'),$result);
					else:
						echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$result);
					endif;
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_INCORRECT'),$result);
				endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : addAddress
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for add Address
	 * * Date : 07 February 2024
	 * * **********************************************************************/
	public function addAddress()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):
			
			if($this->input->get('users_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			elseif($this->input->post('address_type') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('ADDRESS_TYPE_EMPTY'),$result);
			elseif($this->input->post('name') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('NAME_EMPTY'),$result);
			elseif($this->input->post('village') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('VILLAGE_EMPTY'),$result);
			elseif($this->input->post('street') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('STREET_EMPTY'),$result);
			//elseif($this->input->post('area') == ''): 
			//	echo outPut(0,lang('SUCCESS_CODE'),lang('AREA_EMPTY'),$result);
			elseif($this->input->post('city') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('CITY_EMPTY'),$result);
			//elseif($this->input->post('pincode') == ''): 
			//	echo outPut(0,lang('SUCCESS_CODE'),lang('PINCODE_EMPTY'),$result);
			elseif($this->input->post('country') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('COUNTRY_EMPTY'),$result);
			elseif($this->input->post('mobile') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('MOBILE_EMPTY'),$result);
			else:

				$where 			=	[ 'users_id' => (int)$this->input->get('users_id') ];
				$tblName 		=	'uw_users';
				$userDetails 	=	$this->geneal_model->getOnlyOneData($tblName, $where);

				if(!empty($userDetails)):
					if($userDetails['status'] == 'A'):

						$insert_data = array(
											'id'				=>	(int)$this->geneal_model->getNextSequence('uw_diliveryAddress'),
											'user_id'			=>	(int)$this->input->get('users_id'),
											'address_type'		=>	$this->input->post('address_type'),
											'name'				=>	$this->input->post('name'),
											'village'			=>	$this->input->post('village'),
											'street'			=>	$this->input->post('street'),
											'area'				=>	'',//$this->input->post('area'),
											'city'				=>	$this->input->post('city'),
											'pincode'			=>	'',//$this->input->post('pincode'),
											'country'			=>	$this->input->post('country'),
											'mobile'			=>	$this->input->post('mobile'),
											'created_at'		=>	date('Y-m-d H:i'),
											'created_ip'		=>	currentIp()
											);
						$this->geneal_model->addData('uw_diliveryAddress', $insert_data);

						$where1 		=	[ 'user_id' => (int)$this->input->get('users_id') ];
						$tblName1 		=	'uw_diliveryAddress';
						$order1 		=	[];
						$userAddress 	=	$this->geneal_model->getData($tblName1, $where1, $order1);

						$result['userAddress'] 	=	$userAddress;
						echo outPut(1,lang('SUCCESS_CODE'),lang('address_success'),$result);
					else:
						echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$result);
					endif;
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_INCORRECT'),$result);
				endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : editAddress
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for edit Address
	 * * Date : 07 February 2024
	 * * **********************************************************************/
	public function editAddress()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):
			
			if($this->input->get('users_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			elseif($this->input->get('address_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('ADDRESS_ID_EMPTY'),$result);
			elseif($this->input->post('address_type') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('ADDRESS_TYPE_EMPTY'),$result);
			elseif($this->input->post('name') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('NAME_EMPTY'),$result);
			elseif($this->input->post('village') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('VILLAGE_EMPTY'),$result);
			elseif($this->input->post('street') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('STREET_EMPTY'),$result);
			//elseif($this->input->post('area') == ''): 
			//	echo outPut(0,lang('SUCCESS_CODE'),lang('AREA_EMPTY'),$result);
			elseif($this->input->post('city') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('CITY_EMPTY'),$result);
			//elseif($this->input->post('pincode') == ''): 
			//	echo outPut(0,lang('SUCCESS_CODE'),lang('PINCODE_EMPTY'),$result);
			elseif($this->input->post('country') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('COUNTRY_EMPTY'),$result);
			elseif($this->input->post('mobile') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('MOBILE_EMPTY'),$result);
			else:

				$where 			=	[ 'users_id' => (int)$this->input->get('users_id') ];
				$tblName 		=	'uw_users';
				$userDetails 	=	$this->geneal_model->getOnlyOneData($tblName, $where);

				if(!empty($userDetails)):
					if($userDetails['status'] == 'A'):

						$edit_data = array(
											'address_type'		=>	$this->input->post('address_type'),
											'name'				=>	$this->input->post('name'),
											'village'			=>	$this->input->post('village'),
											'street'			=>	$this->input->post('street'),
											'area'				=>	'',//$this->input->post('area'),
											'city'				=>	$this->input->post('city'),
											'pincode'			=>	'',//$this->input->post('pincode'),
											'country'			=>	$this->input->post('country'),
											'mobile'			=>	$this->input->post('mobile'),
											'updated_at'		=>	date('Y-m-d H:i'),
											'updated_ip'		=>	currentIp()
											);
						$this->geneal_model->editData('uw_diliveryAddress', $edit_data, 'id', (int)$this->input->get('address_id'));

						$where1 		=	[ 'user_id' => (int)$this->input->get('users_id') ];
						$tblName1 		=	'uw_diliveryAddress';
						$order1 		=	[];
						$userAddress 	=	$this->geneal_model->getData($tblName1, $where1, $order1);

						$result['userAddress'] 	=	$userAddress;
						echo outPut(1,lang('SUCCESS_CODE'),lang('address_update'),$result);
					else:
						echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$result);
					endif;
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_INCORRECT'),$result);
				endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : deleteAddress
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for delete Address
	 * * Date : 07 February 2024
	 * * **********************************************************************/
	public function deleteAddress()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'DELETE')):
			
			if($this->input->get('users_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			elseif($this->input->get('address_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('ADDRESS_ID_EMPTY'),$result);
			else:

				$where 			=	[ 'users_id' => (int)$this->input->get('users_id') ];
				$tblName 		=	'uw_users';
				$userDetails 	=	$this->geneal_model->getOnlyOneData($tblName, $where);

				if(!empty($userDetails)):
					if($userDetails['status'] == 'A'):

						$this->geneal_model->deleteData('uw_diliveryAddress', 'id', (int)$this->input->get('address_id'));

						$where1 		=	[ 'user_id' => (int)$this->input->get('users_id') ];
						$tblName1 		=	'uw_diliveryAddress';
						$order1 		=	[];
						$userAddress 	=	$this->geneal_model->getData($tblName1, $where1, $order1);

						$result['userAddress'] 	=	$userAddress;
						echo outPut(1,lang('SUCCESS_CODE'),lang('address_delete'),$result);
					else:
						echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$result);
					endif;
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_INCORRECT'),$result);
				endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : getCoupons
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for get Coupons
	 * * Date : 07 February 2024
	 * * **********************************************************************/
	public function getCoupons()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'GET')):
			
			if($this->input->get('users_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			else:

				$where 			=	[ 'users_id' => (int)$this->input->get('users_id') ];
				$tblName 		=	'uw_users';
				$userDetails 	=	$this->geneal_model->getOnlyOneData($tblName, $where);

				if(!empty($userDetails)):
					if($userDetails['status'] == 'A'):
						$where1 		=	[ 'users_id' => (int)$this->input->get('users_id') ,'coupon_status'=>'Live' ];
						//$where1 		=	[ 'users_id' => (int)$this->input->get('users_id') ];
						$tblName1 		=	'uw_coupons';
						$order1 		=	array('created_at'=> 'desc' );
						$userCoupons 	=	$this->geneal_model->getData($tblName1, $where1, $order1);
						$couponsData = [];
						foreach ($userCoupons as $key => $value) {
							$whereCon1['where']		=	array('products_id'=>(int)$value['product_id']);
							$productData 		= 	$this->common_model->getData('single','uw_products',$whereCon1,$order1);
							$draw_date = $productData['draw_date'].' '.$productData['draw_time'];
							$value['draw_date'] = $draw_date;
							array_push($couponsData, $value);
						}
						
						//$userCoupons['draw_date'] = $draw_date;
						$result['userCoupons'] 	=	$couponsData;//$userCoupons;
						echo outPut(1,lang('SUCCESS_CODE'),lang('get_coupons_success'),$result);
					else:
						echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$result);
					endif;
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_INCORRECT'),$result);
				endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : redeemCoupon
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for redeem Coupon
	 * * Date : 07 February 2024
	 * * Update Date : 08 09 2023
	 * * Update By : Dilip Halder
	 * * **********************************************************************/
	public function redeemCoupon()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):
			
			if($this->input->get('users_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			elseif($this->input->post('coupon_code') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('COUPON_CODE_ID_EMPTY'),$result);
			else:

				$where 			=	[ 'users_id' => (int)$this->input->get('users_id') ];
				$tblName 		=	'uw_users';
				$userDetails 	=	$this->geneal_model->getOnlyOneData($tblName, $where);

				if(!empty($userDetails)):
					if($userDetails['status'] == 'A'):

						// $where 						= 	['coupon_code'=>$this->input->post('coupon_code'),'coupon_code_statys'=>'Active'];
					    // $chkCoupon 					= 	$this->geneal_model->getOnlyOneData('uw_coupon_code_only', $where );
					    
					    $where['where'] 			= 	['coupon_code'=>$this->input->post('coupon_code')];
					    $chkCoupon 					= 	$this->geneal_model->getData2('single','uw_coupon_code_only', $where );

					    $where['where'] 			= 	['voucher_code'=>$this->input->post('coupon_code')];
	    				$quickCoupon 				= 	$this->geneal_model->getData2('single','uw_ticket_coupons', $where );

	    				if(!empty($quickCoupon)):
					    	$ticket_order_id 		= $quickCoupon['ticket_order_id'];
					    	$where['where'] 		= 	array('ticket_order_id'=> $ticket_order_id);
					    	$quickOrder 			= 	$this->geneal_model->getData2('single','uw_ticket_orders', $where );
					    endif;

					    // passing value in $chkCoupon variable to overlaps coupon validation.
						if($quickCoupon):
							if($quickOrder['status'] == 'CL'):
						    	$chkCoupon["coupon_code_statys"] = "Inactive";
						    else:
						    	$chkCoupon["coupon_code_statys"] = $quickCoupon['coupon_code_statys'];
						    endif;
								$chkCoupon['coupon_code_amount'] = $quickCoupon['total_price'];
						 endif;

					    if($chkCoupon && $chkCoupon["coupon_code_statys"] == "Redeem"):
					    	echo outPut(0,lang('SUCCESS_CODE'),lang('REDDEM_COUPON'),$result);
					    elseif(empty($chkCoupon) || $chkCoupon["coupon_code_statys"] == "Inactive"  ):
					    	echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_COUPON'),$result);
					   	else:
					    	/* Check sales person and retailser available points */
							$wcon_user['where'] = array('users_id' => (int)$this->input->get('users_id'));
	        				$userData = $this->geneal_model->getData2('single',"uw_users",$wcon_user);
					        $whereCon 				 		=	['users_id' => (int)$this->input->get('users_id')];
					        $availableArabianPoints  		=  $this->geneal_model->getOnlyOneData('uw_users', $whereCon );
					        if($availableArabianPoints):
								$availableArabianPoints 	= 	((float)$availableArabianPoints['availableArabianPoints'] + (float)$chkCoupon['coupon_code_amount']);
						        $updatefield        		= 	array( 'availableArabianPoints' => (float)$availableArabianPoints );
						    else:
						    	$availableArabianPoints 	= 	(float)$chkCoupon['coupon_code_amount'];
						        $updatefield        		= 	array( 'availableArabianPoints' => (float)$availableArabianPoints );
						    endif;
					        $this->geneal_model->editData('uw_users', $updatefield, 'users_id', (int)$this->input->get('users_id'));

					        /* Load Balance Table -- from redeem coupon*/
						    $fromuserparam["load_balance_id"]	=	(int)$this->geneal_model->getNextSequence('uw_loadBalance');
							$fromuserparam["user_id_cred"] 		=	(int)$this->input->get('users_id');
							$fromuserparam["user_id_deb"]		=	(int)0;
							$fromuserparam["user_id_to"]		=	(int)$this->input->get('users_id');
							$fromuserparam["arabian_points"] 	=	(float)$chkCoupon['coupon_code_amount'];
							$fromuserparam["availableArabianPoints"] =	(float)$userData['availableArabianPoints'];
							$fromuserparam["end_balance"] 		=	(float)$userData['availableArabianPoints'] + (float)$chkCoupon['coupon_code_amount'];
						    $fromuserparam["record_type"] 		=	'Credit';
						    $fromuserparam["arabian_points_from"]=	'Reddem Coupon';
						    $fromuserparam["coupon_code"]		=	$this->input->post('coupon_code');
						    $fromuserparam["creation_ip"] 		=	$this->input->ip_address();
						    $fromuserparam["created_at"] 		=	date('Y-m-d H:i');
						    $fromuserparam["created_by"] 		=	(int)$this->input->get('users_id');
						    $fromuserparam["status"] 			=	"A";
						    
						    $this->geneal_model->addData('uw_loadBalance', $fromuserparam);
						    /* End */

							if($quickCoupon):
							   /* update coupon status */
							    $update_coupon 	= 	array(
							    	'availableArabianPoints'=>	(float)$userDetails['availableArabianPoints'],
									'end_balance' 			=>	(float)$userDetails['availableArabianPoints'] + (float)$chkCoupon['coupon_code_amount'],
							    	'coupon_code_statys' => 'Redeem',
							    	'redeemed_by_whom'   =>  $userDetails['users_email'],
							    	'redeemed_date' 	 =>  date('Y-m-d H:i')
							    );
								$this->geneal_model->editData('uw_ticket_coupons', $update_coupon, 'voucher_code', $this->input->post('coupon_code'));
						    else:
							    /* update coupon status */
							    $update_coupon 	= 	array(
							    	'availableArabianPoints'=>	(float)$userDetails['availableArabianPoints'],
									'end_balance' 			=>	(float)$userDetails['availableArabianPoints'] + (float)$chkCoupon['coupon_code_amount'],
							    	'coupon_code_statys' => 'Redeem',
							    	'redeemed_by_whom'   =>  $userDetails['users_email'],
							    	'redeemed_by_user_id'=>  (int)$userDetails['users_id'],
									'redeemed_by_mobile' =>  (int)$userDetails['users_mobile'],
							    	'redeemed_date' 	 =>  date('Y-m-d H:i')
							    );
								$this->geneal_model->editData('uw_coupon_code_only', $update_coupon, 'coupon_code', $this->input->post('coupon_code'));
						    endif;

							$whereCon1 							=	['users_id' => (int)$this->input->get('users_id')];
					        $userDetails1  						=  $this->geneal_model->getOnlyOneData('uw_users', $whereCon1 );
					    	$result['userData'] 				=	$userDetails1;
							echo outPut(1,lang('SUCCESS_CODE'),lang('REDEEM_VOUCHER_SUCCESS'),$result);
					    	
					    endif;
					else:
						echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$result);
					endif;
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_INCORRECT'),$result);
				endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : getWishlist
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for get Wishlist
	 * * Date : 07 February 2024
	 * * **********************************************************************/
	public function getWishlist()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'GET')):
			
			if($this->input->get('users_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			else:

				$where 			=	[ 'users_id' => (int)$this->input->get('users_id') ];
				$tblName 		=	'uw_users';
				$userDetails 	=	$this->geneal_model->getOnlyOneData($tblName, $where);

				if(!empty($userDetails)):
					if($userDetails['status'] == 'A'):
						$userWishlist 		=	array();
						$where1 			=	[ 'users_id' => (int)$this->input->get('users_id') ];
						$tblName1 			=	'uw_wishlist';
						$order1 			=	array('_id'=> -1 );
						$userWishlistData 	=	$this->geneal_model->getData($tblName1, $where1, $order1);
						if($userWishlistData):
							foreach($userWishlistData as $userWishlistInfo):
								$wherepro['where'] 			= 	['products_id'=>(int)$userWishlistInfo['product_id'],'clossingSoon' => 'N','status'=>'A'];
					    	
								$productData = $this->geneal_model->getProductWithPrizeDetails('single','uw_products', $wherepro, $order1);
								//print_r($productData);die();
					    		$userWishlistInfo['products_name']		= $productData['title'];
					    		$userWishlistInfo['products_image']		= $productData['product_image'];
					    		$userWishlistInfo['products_desc']		= $productData['description'];
					    		$userWishlistInfo['products_price']		= $productData['adepoints'];
								$userWishlistInfo['draw_date']			= $productData['draw_date'].' '.$productData['draw_time'];
								$userWishlistInfo['is_show_closing']	= $productData['is_show_closing'];
								//$userWishlistInfo['draw_time']			= $productData['draw_time'];
								
					    		$userWishlistInfo['soldout_status']		= $productData['soldout_status'];
					    		$userWishlistInfo['stock']				= $productData['stock'];
					    		$userWishlistInfo['totalStock']			= $productData['totalStock'];
								$userWishlistInfo['color_size_details']	= $productData['color_size_details'];
								

								$userWishlistInfo['prize_title']		= $productData['prizeDetails'][0]['title'];
								$userWishlistInfo['prize_image']		= $productData['prizeDetails'][0]['prize_image'];
								$userWishlistInfo['prize_id']			= $productData['prizeDetails'][0]['prize_id'];
								$userWishlistInfo['prize1']				= $productData['prizeDetails'][0]['prize1'];
								$userWishlistInfo['prize2']				= $productData['prizeDetails'][0]['prize2'];
								$userWishlistInfo['prize3']				= $productData['prizeDetails'][0]['prize3'];
					    		array_push($userWishlist,$userWishlistInfo);
							endforeach;
						endif;

						$result['userWishlist'] 	=	$userWishlist;
						echo outPut(1,lang('SUCCESS_CODE'),lang('GET_WISHLIST_DATA'),$result);
					else:
						echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$result);
					endif;
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_INCORRECT'),$result);
				endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : addToWishlist
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for add To Wishlist
	 * * Date : 07 February 2024
	 * * **********************************************************************/
	public function addToWishlist()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'GET')):
			
			if($this->input->get('users_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			elseif($this->input->get('product_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('PRODUCT_ID_EMPTY'),$result);
			
			else:

				$where 			=	[ 'users_id' => (int)$this->input->get('users_id') ];
				$tblName 		=	'uw_users';
				$userDetails 	=	$this->geneal_model->getOnlyOneData($tblName, $where);

				if(!empty($userDetails)):
					if($userDetails['status'] == 'A'):
						$returnMessage 			=	'';

						$prowhere['where']		=	array('users_id'=>(int)$this->input->get('users_id'),'product_id'=>(int)$this->input->get('product_id'));
						$prodData				=	$this->common_model->getData('single','uw_wishlist',$prowhere);
						if($prodData == ""):
							$param['wishlist_id']				=	(int)$this->common_model->getNextSequence('uw_wishlist');
							$param['users_id']					=	(int)$this->input->get('users_id');
							$param['product_id']				=	(int)$this->input->get('product_id');
							$param['creation_date']				=   date('Y-m-d H:i');
							$param['creation_ip']				=   currentIp();
							$param['wishlist_product']        	=   'Y';
							$this->common_model->addData('uw_wishlist',$param);
							$returnMessage 						=	lang('add_to_wishlist');
						else:
							$this->common_model->deleteData('uw_wishlist','_id',new MongoDB\BSON\ObjectID($prodData['_id']->{'$id'}));
							$returnMessage 						=	lang('remove_from_wishlist');
						endif;

						$userWishlist 		=	array();
						$where1 			=	[ 'users_id' => (int)$this->input->get('users_id') ];
						$tblName1 			=	'uw_wishlist';
						$order1 			=	array('_id'=> -1 );
						$userWishlistData 	=	$this->geneal_model->getData($tblName1, $where1, $order1);
						if($userWishlistData):
							foreach($userWishlistData as $userWishlistInfo):
								$wherepro 			= 	['products_id'=>(int)$userWishlistInfo['product_id'],'clossingSoon' => 'N','status'=>'A'];
					    		$productData = $this->geneal_model->getOnlyOneData('uw_products', $wherepro );
					    		$userWishlistInfo['products_name']	= $productData['title'];
					    		$userWishlistInfo['products_image']	= $productData['product_image'];
					    		$userWishlistInfo['products_desc']	= $productData['description'];
					    		$userWishlistInfo['products_price']	= $productData['adepoints'];

					    		$userWishlistInfo['soldout_status']	= $productData['soldout_status'];
					    		$userWishlistInfo['stock']			= $productData['stock'];
					    		$userWishlistInfo['totalStock']		= $productData['totalStock'];
					    		array_push($userWishlist,$userWishlistInfo);
							endforeach;
						endif;

						$result['userWishlist'] 	=	$userWishlist;
						echo outPut(1,lang('SUCCESS_CODE'),$returnMessage,$result);
					else:
						echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$result);
					endif;
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_INCORRECT'),$result);
				endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : deleteFromWishlist
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for delete From Wishlist
	 * * Date : 07 February 2024
	 * * **********************************************************************/
	public function deleteFromWishlist()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'DELETE')):
			
			if($this->input->get('users_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			elseif($this->input->get('product_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('PRODUCT_ID_EMPTY'),$result);
			
			else:

				$where 			=	[ 'users_id' => (int)$this->input->get('users_id') ];
				$tblName 		=	'uw_users';
				$userDetails 	=	$this->geneal_model->getOnlyOneData($tblName, $where);

				if(!empty($userDetails)):
					if($userDetails['status'] == 'A'):
						$returnMessage 			=	'';

						$prowhere['where']		=	array('users_id'=>(int)$this->input->get('users_id'),'product_id'=>(int)$this->input->get('product_id'));
						$prodData				=	$this->common_model->getData('single','uw_wishlist',$prowhere);
						if(!empty($prodData)):
							$this->common_model->deleteData('uw_wishlist','_id',new MongoDB\BSON\ObjectID($prodData['_id']->{'$id'}));
							$returnMessage 						=	lang('remove_from_wishlist');
						endif;

						$userWishlist 		=	array();
						$where1 			=	[ 'users_id' => (int)$this->input->get('users_id') ];
						$tblName1 			=	'uw_wishlist';
						$order1 			=	array('_id'=> -1 );
						$userWishlistData 	=	$this->geneal_model->getData($tblName1, $where1, $order1);
						if($userWishlistData):
							foreach($userWishlistData as $userWishlistInfo):
								$wherepro 			= 	['products_id'=>(int)$userWishlistInfo['product_id'],'clossingSoon' => 'N','status'=>'A'];
					    		$productData = $this->geneal_model->getOnlyOneData('uw_products', $wherepro );
					    		$userWishlistInfo['products_name']	= $productData['title'];
					    		$userWishlistInfo['products_image']	= $productData['product_image'];
					    		$userWishlistInfo['products_desc']	= $productData['description'];
					    		$userWishlistInfo['products_price']	= $productData['adepoints'];

					    		$userWishlistInfo['soldout_status']	= $productData['soldout_status'];
					    		$userWishlistInfo['stock']			= $productData['stock'];
					    		$userWishlistInfo['totalStock']		= $productData['totalStock'];
					    		array_push($userWishlist,$userWishlistInfo);
							endforeach;
						endif;

						$result['userWishlist'] 	=	$userWishlist;
						echo outPut(1,lang('SUCCESS_CODE'),$returnMessage,$result);
					else:
						echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$result);
					endif;
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_INCORRECT'),$result);
				endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : getEarnings
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for get Earnings
	 * * Date : 07 February 2024
	 * * **********************************************************************/
	public function getEarnings()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'GET')):
			
			if($this->input->get('users_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			else:

				$where 			=	[ 'users_id' => (int)$this->input->get('users_id') ];
				$tblName 		=	'uw_users';
				$userDetails 	=	$this->geneal_model->getOnlyOneData($tblName, $where);

				if(!empty($userDetails)):
					if($userDetails['status'] == 'A'):

						// Get total signup bonus
						$where['where'] 		=	array('user_id_cred'=>(int)$this->input->get('users_id'),'record_type'=>'Credit','arabian_points_from'=>'Signup Bonus');	
						$signupBonusData 		= 	$this->geneal_model->getData2('multiple','uw_loadBalance',$where);
						$signupBonus = 0;
						if(!empty($signupBonusData)):
							foreach ($signupBonusData as $key => $value):
								$signupBonus += (float)$value['arabian_points'];
							endforeach;	
						endif;

						// Get total cashback
						$where1['where'] 		=	array('user_id_cred'=>(int)$this->input->get('users_id'),'record_type'=>'Credit','arabian_points_from'=>'Membership Cashback');	
						$cashbackData 			= 	$this->geneal_model->getData2('multiple','uw_loadBalance',$where1);
						$cashback = 0;
						if(!empty($cashbackData)):
							foreach ($cashbackData as $key1 => $value1):
								$cashback += (float)$value1['arabian_points'];
							endforeach;	
						endif;

						// Get total topup
						$where2['where'] 		=	array('user_id_cred'=>(int)$this->input->get('users_id'),'record_type'=>'Credit','arabian_points_from'=>'Recharge');	
						$topupData 				= 	$this->geneal_model->getData2('multiple','uw_loadBalance',$where2);
						$topup = 0;
						if(!empty($topupData)):
							foreach ($topupData as $key2 => $value2):
								$topup += (int)$value2['arabian_points'];
							endforeach;	
						endif;

						// Get total referral
						$where3['where'] 		=	array('user_id_cred'=>(int)$this->input->get('users_id'),'record_type'=>'Credit','arabian_points_from'=>'Referral');	
						$referalData 			= 	$this->geneal_model->getData2('multiple','uw_loadBalance',$where3);
						$referral = 0;
						if(!empty($referalData)):
							foreach ($referalData as $key3 => $value3):
								$referral += (float)$value3['arabian_points'];
							endforeach;	
						endif;

						$earningData['signupBonus'] 	= 	$signupBonus;
						$earningData['cashback'] 		= 	$cashback;
						$earningData['topup'] 			= 	$topup;
						$earningData['referral'] 		= 	$referral;
						$earningData['totalEarned'] 	= 	($signupBonus+$cashback+$topup+$referral);
					
						$result['userEarning'] 			=	$earningData;


						$tblName = 'uw_dueManagement';
						$shortField = array('due_management_id' => -1);
						$whereCon4['where']			 	= 	array( 'user_id_to' => (int)$this->input->get('users_id') ,'record_type' => 'Debit');
						
						$result['DueManagement'] 		=	$this->geneal_model->duemanagement('multiple',$tblName,$whereCon4,$shortField);

						$ourCampaigns 					=	array();
						$tbl 							=	'uw_products';
						$wcon['where']          		=  	array( 'stock'=> array('$ne'=> 0,), 'clossingSoon' => 'N', 'status' => 'A' );
						$shortField 					=	['creation_date' => -1];

						$productsData					=	$this->geneal_model->getProductWithPrizeDetails('multiple', $tbl, $wcon, $shortField);
						if($productsData):
							foreach ($productsData as $info2):
								$valid = $info2['validuptodate'].' '.$info2['validuptotime'].':0';
								$today = date('Y-m-d H:i:s');
								if(strtotime($valid) > strtotime($today)):
									$sharewhere['where']=	array('users_id'=>(int)$this->input->get('users_id'),'products_id'=>(int)$info2['products_id']);
									$shareCount			=	$this->common_model->getData('count','uw_product_share',$sharewhere,'','0','0');
									$accumulatedPint    =	((($info2['adepoints']*$info2['share_percentage_first'])/100)*$shareCount);
									$info2['accumulatedPint']  				= 	$accumulatedPint;
									
									if($this->input->get('users_id')):
										$USRwhere 							=	[ 'users_id' => (int)$this->input->get('users_id') ];
										$USRtblName 						=	'uw_users';
										$userDetails 						=	$this->geneal_model->getOnlyOneData($USRtblName, $USRwhere);
										if($userDetails):
											$productShareUrl  				= 	generateProductShareUrl($info2['products_id'],$this->input->get('users_id'),$userDetails['referral_code']);
											$info2['share_url']  			= 	$productShareUrl;
										else:	
											$info2['share_url']  			= 	'';
										endif;
									else:
										$info2['share_url']  				= 	'';
									endif;

									if($this->input->get('users_id')):
										$prowhere['where']	=	array('users_id'=>(int)$this->input->get('users_id'),'product_id'=>(int)$info2['products_id']);
										$prodData			=	$this->common_model->getData('single','uw_wishlist',$prowhere);
										if($prodData):
											if($prodData['wishlist_product'] == 'Y'):
												$info2['wishlist_product']  = 'Y';
											else:
												$info2['wishlist_product']  = 'N';
											endif; 
										else:
											$info2['wishlist_product']  	= 'N';
										endif;
									else:
										$info2['wishlist_product']  		= 'N';
									endif;

									array_push($ourCampaigns,$info2);
								endif;
							endforeach;	
						endif;

						$result['ourCampaigns'] 	=	$ourCampaigns;
						echo outPut(1,lang('SUCCESS_CODE'),lang('get_earnings_success'),$result);
					else:
						echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$result);
					endif;
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_INCORRECT'),$result);
				endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : getOrders
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for get Orders
	 * * Date : 21 JUNE 2022
	 * * **********************************************************************/
	public function getOrders()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'GET')):
			
			if($this->input->get('users_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			else:

				$where 			=	[ 'users_id' => (int)$this->input->get('users_id') ];
				$tblName 		=	'uw_users';
				$userDetails 	=	$this->geneal_model->getOnlyOneData($tblName, $where);

				if(!empty($userDetails)):
					if($userDetails['status'] == 'A'):
						$where1['where'] 	=	[ 'user_id' => (int)$this->input->get('users_id') , 'order_status' => 'Success' ];
						$tblName1 			=	'uw_orders';
						$order1 			=	array('_id'=> -1 );
						$userOrderList 		=	$this->geneal_model->getordersList('multiple',$tblName1,$where1,$order1);

						$result['userOrderList'] 	=	$userOrderList;
						echo outPut(1,lang('SUCCESS_CODE'),lang('get_orders_success'),$result);
					else:
						echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$result);
					endif;
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_INCORRECT'),$result);
				endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : getOrdersDetails
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for get Orders Details
	 * * Date : 21 JUNE 2022
	 * * **********************************************************************/
	public function getOrdersDetails()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'GET')):
			
			if($this->input->get('users_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			elseif($this->input->get('order_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('ORDER_ID_EMPTY'),$result);
			else:

				$where 			=	[ 'users_id' => (int)$this->input->get('users_id') ];
				$tblName 		=	'uw_users';
				$userDetails 	=	$this->geneal_model->getOnlyOneData($tblName, $where);

				if(!empty($userDetails)):
					if($userDetails['status'] == 'A'):


						$where1 		=	[ 'user_id' => (int)$this->input->get('users_id'),'order_id' => $this->input->get('order_id') ];
						$tblName1 		=	'uw_orders';
						$userOrderData 	=	$this->geneal_model->getOnlyOneData($tblName1, $where1);
						if(!empty($userOrderData)):
							$result['userOrderData'] 	=	$userOrderData;

							$where2 			=	[ 'user_id' => (int)$this->input->get('users_id'),'order_id' => $this->input->get('order_id') ];
							$tblName2 			=	'uw_orders_details';
							$order2 			=	array('_id'=> -1 );
							$userOrderDetails 	=	$this->geneal_model->getData($tblName2, $where2, $order2);
							$result['userOrderDetails'] 	=	$userOrderDetails;

							echo outPut(1,lang('SUCCESS_CODE'),lang('get_orders_success'),$result);
						else:
							echo outPut(0,lang('SUCCESS_CODE'),lang('ORDER_ID_INCORRECT'),$result);
						endif;
					else:
						echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$result);
					endif;
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_INCORRECT'),$result);
				endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : getVoucherHistory
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for VoucherHistory
	 * * Date : 28 JANUARY 2023
	 * * **********************************************************************/
	public function getVoucherHistory()
	{	
		$apiHeaderData 		=	getApiHeaderData();

		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		

		if(requestAuthenticate(APIKEY,'POST')):

			
			
			if($this->input->post('users_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			else:

				$where 			=	[ 'users_id' => (int)$this->input->post('users_id') ];
				$tblName 		=	'uw_users';
				$userDetails 	=	$this->geneal_model->getOnlyOneData($tblName, $where);
				
				if(!empty($userDetails)):
					if($userDetails['status'] == 'A'):

						// $where1 		=	[ 'users_id' => (int)$this->input->post('users_id') ,'coupon_status' => 'Live'];
						$where 		=	[ 'user_id_cred' => (int)$this->input->post('users_id') ,"record_type" => "Credit", "arabian_points_from" => "Reddem Coupon" ];
						$tblName 		=	'uw_loadBalance';

						$order 		=	array('_id'=> -1 );

						$VoucherHistory 	=	$this->geneal_model->getData($tblName, $where, $order);

						$result['VoucherHistory'] 	=	$VoucherHistory;
						
						echo outPut(1,lang('SUCCESS_CODE'),lang('REDEEM_VOUCHER_HISTORY_SUCCESS'),$result);
				
					else:
						echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$result);
					endif;
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_INCORRECT'),$result);
				endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : getNotification
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for get Notification
	 * * Date : 11 JULY 2022
	 * * **********************************************************************/
	public function getNotification()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'GET')):
			
			if($this->input->get('users_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			else:

				$where 			=	[ 'users_id' => (int)$this->input->get('users_id') ];
				$tblName 		=	'uw_users';
				$userDetails 	=	$this->geneal_model->getOnlyOneData($tblName, $where);

				if(!empty($userDetails)):
					if($userDetails['status'] == 'A'):
						$where1 		=	[ 'users_id' => (int)$this->input->get('users_id') ];
						$tblName1 		=	'uw_notifications_details';
						$order1 		=	array('_id'=> -1 );
						$userAddress 	=	$this->geneal_model->getData($tblName1, $where1, $order1);

						$where2['where']	=	array('users_id' => (int)$this->input->get('users_id'), 'is_read' => 'N');
						$not_read_data 		=	$this->geneal_model->getData2('count',$tblName1,$where2,$order1);

						$result['not_read_userNotification_count'] = $not_read_data;

						$result['userNotification'] 	=	$userAddress;
						echo outPut(1,lang('SUCCESS_CODE'),lang('get_notification_success'),$result);
					else:
						echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$result);
					endif;
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_INCORRECT'),$result);
				endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}
	/* * *********************************************************************
	 * * Function name : getMembershipDetails
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for get Membership Details
	 * * Date : 21 JULY 2022
	 * * **********************************************************************/
	public function getMembershipDetails()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'GET')):
			
			if($this->input->get('users_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			else:

				$where 			=	[ 'users_id' => (int)$this->input->get('users_id') ];
				$tblName 		=	'uw_users';
				$userDetails 	=	$this->geneal_model->getOnlyOneData($tblName, $where);

				if(!empty($userDetails)):
					if($userDetails['status'] == 'A'):

						$expIN = date('Y-m-d', strtotime($userDetails['created_at']. ' +12 months'));
						$today = strtotime(date('Y-m-d'));
						$dat = strtotime($expIN) - $today;
						$Tdate =  round($dat / (60 * 60 * 24));

						$result 				=	$this->geneal_model->getMembership((int)$userDetails['totalArabianPoints']);
						$result['expiringIn'] 	=	'Expiring in '.$Tdate.' Days';
						echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);

					else:
						echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$result);
					endif;
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_INCORRECT'),$result);
				endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}
	/* * *********************************************************************
	 * * Function name : deleteAccount
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for delete Account
	 * * Date : 07 February 2024
	 * * **********************************************************************/
	public function deleteAccount()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'DELETE')):
			
			if($this->input->get('users_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			else:

				$where 			=	[ 'users_id' => (int)$this->input->get('users_id') ];
				$tblName 		=	'uw_users';
				$userDetails 	=	$this->geneal_model->getOnlyOneData($tblName, $where);

				if(!empty($userDetails)):
					if($userDetails['status'] == 'A'):

						// $this->geneal_model->deleteData('uw_users', 'users_id', (int)$this->input->get('users_id'));
						$param['status'] 	= 'D';
						$param['token'] 	= '';
						$param['updated_at']= date('Y-m-d H:i:s');

						$this->geneal_model->editData('uw_users', $param, 'users_id', (int)$this->input->get('users_id'));
						
						echo outPut(1,lang('SUCCESS_CODE'),lang('account_delete'),$result);
					else:
						echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$result);
					endif;
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_INCORRECT'),$result);
				endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}
	/* * *********************************************************************
	 * * Function name : verifyaccount
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for Active Account
	 * * Date : 23 DEC 2022
	 * * **********************************************************************/
	// public function verifyaccount()
	// {	
	// 	$apiHeaderData 		=	getApiHeaderData();
	// 	$this->generatelogs->putLog('APP',logOutPut($_POST));
	// 	$result 							= 	array();	
	// 	if(requestAuthenticate(APIKEY,'POST')):
	// 		if($this->input->get('users_id') == ''): 
	// 			echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
	// 		elseif($this->input->post('otp') == ''): 
	// 			echo outPut(0,lang('SUCCESS_CODE'),lang('OTP_EMPTY'),$result);
	// 		else:
				 
	// 			$users_id 	 = $this->input->get('users_id');
	// 			$verify_otp  = $this->input->post('otp');

	// 			$tableName          = 'uw_user_varification';
	// 			$whereCon['where']  = array('users_id' => (int)$users_id);
	// 			$USERDATA  			= $this->common_model->getParticularFieldByMultipleCondition($FieldList,$tableName,$whereCon);

	// 			if(!empty($verify_otp) && $verify_otp == $USERDATA['otp']):

	// 				$param['users_id']     = (int)$USERDATA['users_id'];
	// 				$param['users_seq_id'] = $this->geneal_model->getNextIdSequence('users_seq_id', 'Users');
	// 				$param['pos_number']   = $param['users_seq_id'];
	// 				$param['users_name']   = $USERDATA['users_name'];
	// 				$param['last_name']    = $USERDATA['last_name'];
	// 				$param['users_type']   = $USERDATA['users_type'];
	// 				$param['users_email']  = $USERDATA['users_email'];
	// 				$param['country_code'] = $USERDATA['country_code'];
	// 				$param['users_mobile'] = (int)$USERDATA['users_mobile'];
	// 				$param['password'] 	   = $USERDATA['password'];
	// 				$param['totalArabianPoints'] 	 = (float)$USERDATA['totalArabianPoints'];
	// 				$param['availableArabianPoints'] = (float)$USERDATA['availableArabianPoints'];
	// 				$param['password'] 	   = $USERDATA['password'];
	// 				$param['is_varified']  = 'Y';
	// 				$param['device_type']  = $USERDATA['device_type'];

	// 				if($USERDATA['device_type'] == 'ios' || $USERDATA['device_type'] == 'android' ):
	// 					$param['bind_person_id'] 	=  (int)$USERDATA['bind_person_id'];
	// 					$param['bind_user_type'] 	=  "Admin";
	// 					$param['bind_person_name'] 	=  "Admin";
	// 				endif;

	// 				if($USERDATA['referred_by']):
	// 					$param['referred_by'] 	  =  new MongoDB\BSON\ObjectId($USERDATA['referred_by']['$oid']);
	// 					$param['referrel_amount'] =  $USERDATA['referrel_amount'];
	// 				endif;
	// 				$param['latitude']	   = $USERDATA['latitude'];
	// 				$param['longitude']	   = $USERDATA['longitude'];
	// 				$param['address']	   = $USERDATA['address'];
	// 				$param['app_version']  = $USERDATA['app_version'];
	// 				$param['login_token']  = $USERDATA['login_token'];
	// 				$param['token']        = base64_encode($USERDATA['users_id']);
	// 			    $param['status']       = 'A';
	// 			    $param['users_otp']    = '';   
	// 				$param['created_at']   = date('Y-m-d H:i:s');;
	// 				$param['creation_ip']  = $this->input->ip_address();
	// 				$tableName = 'uw_users';
	// 				$Data 	   = $this->common_model->addData($tableName,$param);
	// 				$this->common_model->deleteData('uw_user_varification','verification_id',(int)$USERDATA['verification_id']);

	// 				//Sign up loadBalance entry...
	// 				$loadbaWhere 	=	array( 'users_id' => (int)$users_id );
	// 				$loadbalaceData	=	$this->geneal_model->getOnlyOneData('uw_loadBalance', $loadbaWhere);
	// 				if(empty($loadbalaceData)):
	// 					$user_OId 	 = $USERDATA['_id']->{'$id'};
	// 					$signupBonus = SIGNUPBONUS;
	// 	                /* Load Balance Table -- after Sign Up*/
	// 	                $Cashbparam["load_balance_id"]      = (int)$this->geneal_model->getNextSequence('uw_loadBalance');
	// 	                $Cashbparam["user_oid"]        		= new MongoDB\BSON\ObjectId($user_OId);
	// 	                $Cashbparam["user_id_cred"]         = (int)$USERDATA['users_id'];
	// 	                $Cashbparam["upoints"]       		= (float)$signupBonus;
	// 	                $Cashbparam["record_type"]          = 'Credit';
	// 	                $Cashbparam["narration"]  			= 'Signup Bonus';
	// 	                $Cashbparam["remarks"]  			= '';
	// 	                $Cashbparam["availableUPoints"] 	= (float)'0';
	// 	                $Cashbparam["end_balance"] 			= (float)signupBonus;
	// 	                $Cashbparam["creation_ip"]          = currentIp();
	// 	                $Cashbparam["created_at"]           = date('Y-m-d H:i');
	// 	                $Cashbparam["created_by"]           = (int)$USERDATA['users_id'];
	// 	                $Cashbparam["status"]               = "A";
	// 	                $this->geneal_model->addData('uw_loadBalance', $Cashbparam);
	// 				endif;
	// 				//End

	// 				$finalres = $this->geneal_model->getDataByParticularField('uw_users','users_id',(int)$users_id );
	// 				$finalres['password'] =	"";
	// 				$result['userData']   =	$finalres;

	// 				//Sent Notification
	// 				if($finalres['device_id']):
	// 					$this->notification_model->sentSignupBonusNotification($result);
	// 				endif;
	// 				if($USERDATA['users_email']):
	// 					$this->emailsendgrid_model->sendSuccessRegistrationMailToUser($USERDATA);
	// 				endif;
	// 				$mobile_no = $USERDATA['country_code'].$USERDATA['users_mobile'];
	// 				$this->sms_model->sendSuccessRgistrationSmsToUser($mobile_no,$USERDATA['users_name'],$USERDATA['country_code']);
	// 				echo outPut(1,lang('SUCCESS_CODE'),lang('ACCOUNT_VERIFY'),$result);
	// 			else:
	// 				echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_OTP'),$result);
	// 			endif;

	// 		endif;
	// 	else:
	// 		echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
	// 	endif;
	// }

	/* * *********************************************************************
	 * * Function name : verifyaccount
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for Active Account
	 * * Date : 23 DEC 2022
	 * * **********************************************************************/
 	 public function verifyaccount()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):
			if($this->input->get('users_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			elseif($this->input->post('otp') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('OTP_EMPTY'),$result);
			else:
				 
				$users_id 	 = $this->input->get('users_id');
				$verify_otp  = $this->input->post('otp');

				$tableName          = 'uw_user_varification';
				$whereCon['where']  = array('users_id' => (int)$users_id);
				$USERDATA  			= $this->common_model->getParticularFieldByMultipleCondition($FieldList,$tableName,$whereCon);
				// echo "<pre>";print_r($USERDATA);die();

				if(!empty($verify_otp) && $verify_otp == $USERDATA['otp']):
					$param['users_id']     = (int)$USERDATA['users_id'];
					$param['users_seq_id'] = $this->geneal_model->getNextIdSequence('users_seq_id', 'Users');
					$param['referral_code']= $param['users_seq_id'];
					$param['pos_number']   = $param['users_seq_id'];
					$param['users_name']   = $USERDATA['users_name'];
					$param['last_name']    = $USERDATA['last_name'];
					$param['users_type']   = $USERDATA['users_type'];
					$param['users_email']  = $USERDATA['users_email'];
					$param['country_code'] = $USERDATA['country_code'];
					$param['users_mobile'] = (int)$USERDATA['users_mobile'];
					$param['password'] 	   = $USERDATA['password'];
					// $param['totalArabianPoints'] 	 = (float)SIGNUPBONUS;
					// $param['availableArabianPoints'] = (float)SIGNUPBONUS;

					$param['totalArabianPoints'] 	 = (float)0;
					$param['availableArabianPoints'] = (float)0;

					$param['password'] 	   = $USERDATA['password'];
					$param['is_varified']  = 'Y';
					$param['device_type']  = $USERDATA['device_type'];

					if($USERDATA['device_type'] == 'ios' || $USERDATA['device_type'] == 'android' ):
						$param['bind_person_id'] 	=  (int)$USERDATA['bind_person_id'];
						$param['bind_user_type'] 	=  "Admin";
						$param['bind_person_name'] 	=  "Admin";
					endif;

					if($USERDATA['referred_by']):
						$param['referred_by'] 	  =  new MongoDB\BSON\ObjectId($USERDATA['referred_by']['$oid']);
						// $param['referrel_amount'] =  $USERDATA['referrel_amount'];
					endif;
					
					$param['latitude']	   = $USERDATA['latitude'];
					$param['longitude']	   = $USERDATA['longitude'];
					$param['address']	   = $USERDATA['address'];
					$param['app_version']  = $USERDATA['app_version'];
					$param['login_token']  = $USERDATA['login_token'];
					$param['token']        = base64_encode($USERDATA['users_id']);
				    $param['status']       = 'A';
				    $param['users_otp']    = '';   
					$param['created_at']   = date('Y-m-d H:i:s');;
					$param['creation_ip']  = $this->input->ip_address();

					$tableName       = 'uw_users';
					$CreatedUserData = $this->common_model->addData($tableName,$param);
					$this->common_model->deleteData('uw_user_varification','verification_id',(int)$USERDATA['verification_id']);
					$user_OId 	 = $CreatedUserData['_id']->{'$id'};

					// if($USERDATA['referred_by']):
						
					// 	$RaffletblName = 'uw_users';
					// 	$referredWhere['where']  =	array( '_id' => $param['referred_by'] );
					// 	$RaferData				 =	$this->common_model->getData('single',$RaffletblName, $referredWhere);
					// 	if(!empty($RaferData)):
					// 		$RaferDataParam['availableArabianPoints'] = $RaferData['availableArabianPoints'] + REFFERLBONUS;
					// 		$this->common_model->editData($RaffletblName, $RaferDataParam ,'_id', $param['referred_by']);

					// 		// First Commission amount  releasing for btb user..
					// 		$raffleParam["load_balance_id"]		 	= (int)$this->geneal_model->getNextSequence('uw_loadBalance');
					// 		$raffleParam["user_oid"] 			 	= new MongoDB\BSON\ObjectId($RaferData['_id']->{'$id'});
					// 		$raffleParam["request_id"] 			 	= (int)$CreatedUserData['users_id'];
					// 		$raffleParam["request_oid"] 			= new MongoDB\BSON\ObjectId($user_OId);
					// 		$raffleParam["user_id_deb"]			 	= (int)0;
					// 		$raffleParam["user_id_cred"] 			= (int)$RaferData['users_id'];
					// 		$raffleParam["upoints"] 			    = (float)REFFERLBONUS;
					// 		$raffleParam["availableArabianPoints"]  = (float)$RaferData['availableArabianPoints'];
					// 		$raffleParam["end_balance"] 		 	= (float)$RaferData['availableArabianPoints'] + REFFERLBONUS;
					// 		$raffleParam["record_type"] 		 	= 'Credit';
					// 		$raffleParam["narration"]			 	= 'Referrel Commission';
					// 		$raffleParam["remarks"]				 	= 'Referral Signup of ('.$USERDATA['users_mobile'].')';
					// 		$raffleParam["creation_ip"] 	 	 	= currentIp();
					// 		$raffleParam["created_at"] 			 	= date('Y-m-d H:i');
					// 		$raffleParam["created_by"] 			 	= (int)$UserData['users_id'];
					// 		$raffleParam["status"] 				 	= "A";
					// 		// echo "<pre>";print_r($raffleParam);die();
					// 		$FirstpurchaseinsertResult = $this->common_model->addData('uw_loadBalance', $raffleParam);
					// 	endif;
					// 	// $param['referrel_amount'] =  $USERDATA['referrel_amount'];
					// endif;

					//Sign up loadBalance entry...
					$loadbaWhere 	=	array( 'user_oid' =>  new MongoDB\BSON\ObjectId($user_OId) );
					$loadbalaceData	=	$this->geneal_model->getOnlyOneData('uw_loadBalance', $loadbaWhere);
					// if(empty($loadbalaceData)):
						
					// 	$signupBonus = SIGNUPBONUS;
		            //     /* Load Balance Table -- after Sign Up*/
		            //     $Cashparam["load_balance_id"]       = (int)$this->geneal_model->getNextSequence('uw_loadBalance');
		            //     $Cashparam["user_oid"]        		= new MongoDB\BSON\ObjectId($user_OId);
		            //     $Cashparam["user_id_cred"]          = (int)$CreatedUserData['users_id'];
		            //     $Cashparam["upoints"]       		= (float)SIGNUPBONUS;
		            //     $Cashparam["record_type"]           = 'Credit';
		            //     $Cashparam["narration"]  			= 'Signup Bonus';
		            //     $Cashparam["remarks"]  				= '';
		            //     $Cashparam["availableUPoints"] 		= (float)'0';
		            //     $Cashparam["end_balance"] 			= (float)SIGNUPBONUS;
		            //     $Cashparam["creation_ip"]          = currentIp();
		            //     $Cashparam["created_at"]           = date('Y-m-d H:i');
		            //     $Cashparam["created_by"]           = (int)$CreatedUserData['users_id'];
		            //     $Cashparam["status"]               = "A";
		            //     $this->geneal_model->addData('uw_loadBalance', $Cashparam);
					// endif;
					//End

					$finalres = $this->geneal_model->getDataByParticularField('uw_users','users_id',(int)$users_id );
					$finalres['password'] =	"";
					$result['userData']   =	$finalres;

					//Sent Notification
					if($finalres['device_id']):
						$this->notification_model->sentSignupBonusNotification($result);
					endif;
					if($USERDATA['users_email']):
						$this->emailsendgrid_model->sendSuccessRegistrationMailToUser($USERDATA);
					endif;
					$mobile_no = $USERDATA['country_code'].$USERDATA['users_mobile'];
					$this->sms_model->sendSuccessRgistrationSmsToUser($mobile_no,$USERDATA['users_name'],$USERDATA['country_code']);
					echo outPut(1,lang('SUCCESS_CODE'),lang('ACCOUNT_VERIFY'),$result);
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_OTP'),$result);
				endif;

			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}
	
	/* * *********************************************************************
	 * * Function name 	: rsendotp
	 * * Developed By 	: Dilip Halder
	 * * Purpose  		: This function used for rsend otp
	 * * Date 			: 07 February 2024
	 * * Updated By 	: Dilip Halder
	 * * Updated Date 	: 09 October 2024
	 * * **********************************************************************/
	public function rsendotp()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 			= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):
			if($this->input->post('users_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			else:
				
				$country_code   = $this->input->post('country_code');
				$mobile_number  = (int)$this->input->post('mobile_number');
				if(!empty($country_code) &&  empty($mobile_number) ):
					echo outPut(0,lang('SUCCESS_CODE'),lang('MOBILE_EMPTY'),$result);
				endif;

				$where['users_id'] 			= (int)$this->input->post('users_id');
				if(!empty($country_code)):
					$where['country_code'] 	= $this->input->post('country_code');
					$where['users_mobile'] 	= (int)$this->input->post('mobile_number');
				endif;

				$tblName 	  = 'uw_users';
				$userDetails  = $this->geneal_model->getOnlyOneData($tblName, $where);
				if(!empty($userDetails)):
					
					// $result['userData']	=	$userDetails;
					if($userDetails['is_verify'] == 'N' || !empty($country_code) && !empty($mobile_number)):
						
						$otp  				 = (int)rand(100000,999999);		
						$param['users_otp']  = $otp;   
						$param['updated_at'] = date('Y-m-d H:i');
						$param['updated_ip'] = $this->input->ip_address();
						$this->common_model->editData('uw_users',$param,'users_id',(int)$userDetails['users_id']);
						if($userDetails['users_email']):
							$this->emailsendgrid_model->accountVerifyOTP($userDetails,$otp);
						endif;

						$country_code = $userDetails['country_code'];
						$users_mobile = $userDetails['users_mobile'];
						$this->sms_model->accountVerifyOTP($country_code,$users_mobile,$otp);
						echo outPut(1,lang('SUCCESS_CODE'),lang('OTP_SENT'),$result);

					else:
						echo outPut(1,lang('SUCCESS_CODE'),lang('ACCOUNT_ALREADY_VERIFIED'),$result);
					endif;

				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('INCORRECT_DETAILS'),$result);
				endif;


			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	} //END OF FUNCTION

	/* * *********************************************************************
	 * * Function name : read_all_notifications
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for read all notifications
	 * * Date : 27 DEC 2022
	 * * **********************************************************************/
	public function read_all_notifications()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):
			
			if($this->input->post('users_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			else:

				$where 			=	[ 'users_id' => (int)$this->input->post('users_id') ];
				$tblName 		=	'uw_notifications_details';
				$userDetails 	=	$this->geneal_model->getOnlyOneData($tblName, $where);

				if(!empty($userDetails)):
					if($userDetails['status'] == 'A'):
						$param['is_read'] 	=	'Y';
						//print_r($userDetails['users_id']);die();
						$this->geneal_model->editData('uw_notifications_details',$param,'users_id',(int)$userDetails['users_id']);
						echo outPut(1,lang('SUCCESS_CODE'),lang('READ_NOTIFICATIONS'),$result);
					else:
						echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$result);
					endif;
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_INCORRECT'),$result);
				endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	} // END OF FUNCTION

	/* * *********************************************************************
	 * * Function name : UpdateNotification
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for update Notification
	 * * Date : 07 February 2024
	 * * **********************************************************************/
	public function updateNotification()
	{	

		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	

		if(requestAuthenticate(APIKEY,'POST')):

			if($this->input->get('users_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			else:
				$where 			=	[ 'users_id' => (int)$this->input->get('users_id') ];
				$tblName 		=	'uw_users';
				$userDetails 	=	$this->geneal_model->getOnlyOneData($tblName, $where);

				if(!empty($userDetails)):
					if($userDetails['status'] == 'A'):

						if($this->input->post('sms_notification') !=''):
							$data['sms_notification'] = $this->input->post('sms_notification');
						elseif($this->input->post('email_notification') !=''):
							$data['email_notification'] = $this->input->post('email_notification');
						elseif($this->input->post('notification') !=''):
							$data['notification'] = $this->input->post('notification');
						endif;

						$this->geneal_model->editData('uw_users',$data,'users_id',(int)$this->input->get('users_id'));

						$finalres = $this->geneal_model->getDataByParticularField('uw_users','users_id',(int)$this->input->get('users_id'));
						$result['userData'] 		=	$finalres;
						echo outPut(1,lang('SUCCESS_CODE'),lang('NOTIFICATION_UPDATED'),$result);
					else:
						echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$result);
					endif;
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_INCORRECT'),$result);
				endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}


	/* * *********************************************************************
	 * * Function name : addeditAppname
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for add/edit app name
	 * * Date 	  : 25 Novermber 2023
	 * * **********************************************************************/
	public function addeditAppname()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):

			if( $this->input->post('users_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			elseif($this->input->post('app_name') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_APPNAME'),$result);
			else:
				$updateParams = array('app_name' => $this->input->post('app_name'));
				$USerList = $this->geneal_model->editData('uw_users', $updateParams, 'users_id', (int)$this->input->post('users_id'));
				if($USerList == 1):
					echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
				endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : updteSummeryPin
	 * * Developed By  : Dilip Halder
	 * * Purpose       : This function used to update summery pin.
	 * * Date 		   : 09 october 2024
	 * * **********************************************************************/
	public function updateSummeryPin()
	{	
		$apiHeaderData 		= getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 			= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):
			$userId  				= $this->input->post('users_id');
			$otp  		  			= $this->input->post('otp');
			$summery_pin  			= $this->input->post('summery_pin');
			$confirm_summery_pin  	= $this->input->post('confirm_summery_pin');
			
			if($userId == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			elseif($otp == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('OTP_EMPTY'),$result);
			elseif($summery_pin == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_SUMMERY_PIN'),$result);
			elseif($confirm_summery_pin == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_CONFIRM_SUMMERY_PIN'),$result);
			else:
				
				$whereCon['where']   = array('users_id' => (int)$userId , 'users_otp' => (int)$otp ,'status'=> "A");
				$tblName 	  		 = 'uw_users';
				$userExist  		 = $this->common_model->getData('count',$tblName, $whereCon);
				if($userExist == 1):
					$param['users_otp']   	= '';
					$param['summery_pin']   = $this->input->post('summery_pin');
					$param['updated_at'] 	= date('Y-m-d H:i');
					$param['updated_ip'] 	= $this->input->ip_address();
					$this->common_model->editData('uw_users',$param,'users_id',(int)$userId);
					echo outPut(1,lang('SUCCESS_CODE'),lang('UPDATED_SUMMERY_PIN'),$result);
				else:
					echo outPut(0,lang('FORBIDDEN_CODE'),lang('INVALID_OTP'),$result);
				endif;
		    endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : verifySummeryPin
	 * * Developed By  : Dilip Halder
	 * * Purpose       : This function used to verify Summery Pin.
	 * * Date 		   : 09 october 2024
	 * * **********************************************************************/
	public function verifySummeryPin()
	{	
		$apiHeaderData 		= getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 			= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):
			$userId  				= $this->input->post('users_id');
			$summery_pin  			= $this->input->post('summery_pin');

			if($userId == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			elseif($summery_pin == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_SUMMERY_PIN'),$result);
			else:
				$wcon_assessSummery['where'] = array('users_id' => (int)$userId ,'summery_pin' => $summery_pin ,'status' => 'A'  );
				$assessSummery				 = $this->common_model->getData('count', 'uw_users', $wcon_assessSummery);
				if($assessSummery == 1):
					echo outPut(0,lang('SUCCESS_CODE'),lang('SUMMERY_PIN_VERIFIED'),$result);die();
				else:
					echo outPut(0,lang('FORBIDDEN_CODE'),lang('INVALID_SUMMERY_PIN'),$result);
				endif;
		    endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : SendSummaryPinOTP
	 * * Developed By  : Dilip Halder
	 * * Purpose       : This function used to SendSummaryPinOTP.
	 * * Date 		   : 29 November 2025
	 * * **********************************************************************/
	public function SendSummaryPinOTP()
	{	
		try {

			$apiHeaderData 		= getApiHeaderData();
			$this->generatelogs->putLog('APP',logOutPut($_POST));
			$result 			= 	array();	
			if(requestAuthenticate(APIKEY,'POST')):
				$userId  				= $this->input->post('users_id');
				if($userId == ''): 
					throw new Exception(lang('USER_ID_EMPTY') , 1);
				else:
					$whereCon['where'] = array('users_id' => (int)$userId ,'status' => 'A');
					$userDetails       = $this->common_model->getData('single', 'uw_users', $whereCon);
					if(!empty($userDetails)):
						$otp                 = (int)rand(100000,999999);
						$param['users_otp']        = $otp;
						$param['otp_generated_at'] = strtotime(date('Y-m-d'));
						$param['updated_at'] = date('Y-m-d H:i');
						$param['updated_ip'] = $this->input->ip_address();
						$this->common_model->editData('uw_users',$param,'users_id',(int)$userId);
						$this->sms_model->accountVerifyOTP($userDetails['country_code'],$userDetails['users_mobile'],$otp ,'summarypin');
						echo outPut(1,lang('SUCCESS_CODE'),lang('OTP_SENT'),$result);die();
					else:
						throw new Exception(lang('USER_ID_INCORRECT') , 1);
					endif;
			    endif;
			else:
				echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
			endif;
		} catch (Exception $e) {
			echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);
		}
	}

	/* * *********************************************************************
	 * * Function name : VerifySummaryPin
	 * * Developed By  : Dilip Halder
	 * * Purpose       : This function used to VerifySummaryPin.
	 * * Date 		   : 29 November 2025
	 * * **********************************************************************/
	public function VerifySummaryPin()
	{	
		try {

			$apiHeaderData 		= getApiHeaderData();
			$this->generatelogs->putLog('APP',logOutPut($_POST));
			$result 			= 	array();	
			if(requestAuthenticate(APIKEY,'POST')):
				$userId = $this->input->post('users_id');
				$OTP    = $this->input->post('otp');

				if(empty($userId)):
					throw new Exception(lang('USER_ID_EMPTY') , 1);
				elseif(empty($OTP)):
					throw new Exception(lang('OTP_EMPTY') , 1);
				else:
					$whereCon['where'] = array('users_id' => (int)$userId ,'users_otp' => (int)$OTP ,'status' => 'A');
					$userDetails       = $this->common_model->getData('single', 'uw_users', $whereCon);
					if(!empty($userDetails)):
						// $param['users_otp']  = '';
						$param['summarypin_verified_at'] = strtotime(date('Y-m-d'));
						$param['updated_at'] = date('Y-m-d H:i');
						$param['updated_ip'] = $this->input->ip_address();
						$this->common_model->editData('uw_users',$param,'users_id',(int)$userId);
						echo outPut(1,lang('SUCCESS_CODE'),lang('SUMMERY_PIN_VERIFIED'),$param);die();
					else:
						throw new Exception(lang('INVALID_OTP') , 1);
					endif;
				endif;
			else:
				echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
			endif;
		} catch (Exception $e) {
			echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);
		}
	}

	/* * *********************************************************************
	 * * Function name : sendotpOTPMobileEmail
	 * * Developed By  : Dilip Halder
	 * * Purpose       : This function used to send OTP
	 * * Date 		   : 07 February 2024
	 * * Updated By    : Dilip Halder
	 * * Date          : 25 March 2023
	 * * **********************************************************************/
	public function sendotpOTPMobileEmail()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):
			
			$UserID       = $this->input->post('users_id');
			$users_email  = $this->input->post('users_email');
			$country_code = $this->input->post('country_code');
			$users_mobile = $this->input->post('users_mobile');

			if(empty($UserID)): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			else:
				
				$tblName        	= 'uw_users';
				$Fieldslist     	= array('users_email','status','users_mobile','country_code');
				$whereCon1['where'] = array('users_id' => (int)$UserID );
				$userDetails		= $this->common_model->getParticularFieldByMultipleCondition($Fieldslist,$tblName,$whereCon1);
				// echo "<pre>";print_r($userDetails);die();
				if(!empty($userDetails) && $userDetails['status']  == "A"):

					$otp  	 = (int)rand(100000,999999);	
					// Updating existing user's details.
					// Email
					if(!empty($users_email) && $userDetails['users_email'] != $users_email ):
						$Fieldslist     = array('users_email');
						$duplicateEmail	= $this->common_model->getSingleDataByParticularField($Fieldslist,$tblName,'users_email',$users_email);
						if(empty($duplicateEmail)):
							$this->emailsendgrid_model->accountVerifyOTP($users_email,$otp);
						else:
							echo outPut(0,lang('SUCCESS_CODE'),lang('EMAIL_ALREADY_EXIST'),$result);die();
						endif;
					endif;

					// Mobile
					if(!empty($users_mobile) && $userDetails['users_mobile'] != $users_mobile ):
						$Fieldslist      = array('users_mobile');
						$duplicateMobile = $this->common_model->getSingleDataByParticularField($Fieldslist,$tblName,'users_mobile',$users_mobile);
						if(empty($duplicateMobile)):
							$this->sms_model->accountVerifyOTP($country_code, $users_mobile,$otp);
						else:
							echo outPut(0,lang('SUCCESS_CODE'),lang('PHONE_ALREADY_EXIST'),$result);die();
						endif;
					endif;
					$param2['users_otp']  = $otp;
					$param2['updated_at'] = date('Y-m-d H:i');
					$param2['updated_ip'] = currentIp();
					$this->common_model->editData($tblName,$param2,'users_id',(int)$UserID);
					$result = [];
					echo outPut(1,lang('SUCCESS_CODE'),lang('OTP_SENT'),$result);die();

				elseif(!empty($userDetails) && $userDetails['status']  == "B"):	
					echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$result);

				elseif(!empty($userDetails) && $userDetails['status']  == "I"):	
					echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_INACIVE'),$result);

				elseif(!empty($userDetails) && $userDetails['status']  == "D"):	
					echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_DELETED'),$result);
				else:
		  			 echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_OTP'),$result);
				endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : updateMobileEmail
	 * * Developed By  : Dilip Halder
	 * * Purpose       : This function used for update Profile
	 * * Date 		   : 07 February 2024
	 * * Updated By    : Dilip Halder
	 * * Date          : 25 March 2023
	 * * **********************************************************************/
	public function updateMobileEmail()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):
			
			$UserID       = $this->input->post('users_id');
			$users_email  = $this->input->post('users_email');
			$country_code = $this->input->post('country_code');
			$users_mobile = $this->input->post('users_mobile');
			$otp          = $this->input->post('otp');

			if(empty($UserID)): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			elseif(empty($otp)): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('OTP_EMPTY'),$result);
			else:
				
				$tblName        	= 'uw_users';
				$Fieldslist     	= array('users_email','status','users_mobile','country_code' ,'users_otp');
				$whereCon1['where'] = array('users_id' => (int)$UserID , 'users_otp' => (int)$otp );
				$userDetails		= $this->common_model->getParticularFieldByMultipleCondition($Fieldslist,$tblName,$whereCon1);
				// echo "<pre>";print_r($userDetails);die();
				if(!empty($userDetails) && $userDetails['status']  == "A"):

					// Updating existing user's details.
					
					// Email
					if(!empty($users_email) && $userDetails['users_email'] != $users_email ):
						$Fieldslist     = array('users_email');
						$duplicateEmail	= $this->common_model->getSingleDataByParticularField($Fieldslist,$tblName,'users_email',$users_email);
						if(empty($duplicateEmail)):
							$param1['users_otp']   = '';
							$param1['users_email'] = trim($users_email);
							$param1['updated_at']  = date('Y-m-d H:i');
							$param1['updated_ip']  = currentIp();
							$this->common_model->editData($tblName,$param1,'users_id',(int)$UserID);
						else:
							echo outPut(0,lang('SUCCESS_CODE'),lang('EMAIL_ALREADY_EXIST'),$result);die();
						endif;
					endif;

					// Mobile
					if(!empty($users_mobile) && $userDetails['users_mobile'] != $users_mobile ):
						$Fieldslist      = array('users_mobile');
						$duplicateMobile = $this->common_model->getSingleDataByParticularField($Fieldslist,$tblName,'users_mobile',$users_mobile);
						if(empty($duplicateMobile)):
							$param2['users_otp']     = '';
							$param2['country_code']  = trim($country_code);
							$param2['users_mobile']  = trim($users_mobile);
							$param2['updated_at']    = date('Y-m-d H:i');
							$param2['updated_ip']    = currentIp();
							$this->common_model->editData($tblName,$param2,'users_id',(int)$UserID);
						else:
							echo outPut(0,lang('SUCCESS_CODE'),lang('PHONE_ALREADY_EXIST'),$result);die();
						endif;
					endif;
					$result = [];
					echo outPut(1,lang('SUCCESS_CODE'),lang('PROFILE_UPDATED'),$result);die();

				elseif(!empty($userDetails) && $userDetails['status']  == "B"):	
					echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$result);

				elseif(!empty($userDetails) && $userDetails['status']  == "I"):	
					echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_INACIVE'),$result);

				elseif(!empty($userDetails) && $userDetails['status']  == "D"):	
					echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_DELETED'),$result);
				else:
		  			 echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_OTP'),$result);
				endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : updateSimNo
	 * * Developed By  : Megha
	 * * Purpose       : This function used to update SIM No.
	 * * Date 		   : 08 June 2026
	 * * **********************************************************************/
	public function updateSimNo()
	{	
		$apiHeaderData 		= getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 			= array();	
		if(requestAuthenticate(APIKEY,'POST')):
			$userId = $this->input->post('users_id');
			$simNo  = trim((string) $this->input->post('sim_no'));

			if($userId == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			else:
				$where       = array('users_id' => (int)$userId);
				$tblName     = 'uw_users';
				$userDetails = $this->geneal_model->getOnlyOneData($tblName, $where);

				if(!empty($userDetails)):
					if($userDetails['status'] == 'A'):
						if($simNo != ''):
							$clearParam = array(
								'sim_no'     => '',
								'updated_at' => date('Y-m-d H:i'),
								'updated_ip' => currentIp(),
							);
							$this->common_model->editDataByMultipleCondition('uw_users', $clearParam, array(
								'sim_no'   => $simNo,
								'users_id' => array('$ne' => (int)$userId),
							));
						endif;

						$param['sim_no']     = $simNo;
						$param['updated_at'] = date('Y-m-d H:i');
						$param['updated_ip'] = currentIp();
						$this->common_model->editData('uw_users',$param,'users_id',(int)$userId);

						$result['users_id'] = (int)$userId;
						$result['sim_no']   = $simNo;
						echo outPut(1,lang('SUCCESS_CODE'),lang('SIM_NO_UPDATED'),$result);
					else:
						echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_INCORRECT'),$result);
					endif;
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_INCORRECT'),$result);
				endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Btc extends CI_Controller {
    
    var $postdata;
    var $user_agent;
    var $request_url; 
    var $method_name;
    
    public function  __construct()  
    { 
        parent:: __construct();
        error_reporting(E_ALL ^ E_NOTICE);  
        $this->load->model(array('sms_model','emailsendgrid_model','notification_model','emailtemplate_model'));
        $this->lang->load('statictext', 'api');
        $this->load->helper('apidata');
        $this->load->model(array('geneal_model','common_model'));

        $this->user_agent       =   $_SERVER['HTTP_USER_AGENT'];
        $this->request_url      =   $_SERVER['REDIRECT_URL'];
        $this->method_name      =   $_SERVER['REDIRECT_QUERY_STRING'];

        $this->load->library('generatelogs',array('type'=>'common'));
    } 


    /* * *********************************************************************
	 * * Function name : login
	 * * Developed By  : Dilip Halder
	 * * Purpose       : This function used for login
	 * * Date          : 24 February 2025
	 * * **********************************************************************/
	// public function login()
	// {	
	// 	$apiHeaderData 		=	getApiHeaderData();
	// 	$this->generatelogs->putLog('APP',logOutPut($_POST));
	// 	$result 			= 	array();	
	// 	if(requestAuthenticate(APIKEY,'POST')):
			
	// 		$country_code 	= $this->input->post('country_code');
	// 		$users_mobile 	= $this->input->post('users_mobile');
	// 		$users_email 	= $this->input->post('users_email');
	// 		$password       = md5($this->input->post('users_password'));
			
	// 		$users_long 	= $this->input->post('users_long');
	// 		$users_lat 		= $this->input->post('users_lat');
	// 		$device_id 		= $this->input->post('device_id');
	// 		$users_address 	= $this->input->post('users_address');

	// 		if(
	// 			$country_code == '' && $users_mobile != "" || 
	// 			$country_code != '' && $users_mobile == "" ||   
	// 			$country_code == '' && $users_mobile == "" && $users_email == ''
	// 		) :
	// 			echo outPut(0,lang('SUCCESS_CODE'),lang('USERDETAIL_EMPTY'),$result);
	// 		elseif($password ==''):
	// 			echo outPut(0,lang('SUCCESS_CODE'),lang('PASSWORD_EMPTY'),$result);
	// 		else:
				
	// 			if(!empty($country_code)):
	// 				$where['where']['country_code'] = $country_code;
	// 				$where['where']['users_mobile'] = (int)$users_mobile;
	// 			else:
	// 				$where['where']['users_email'] = $users_email ;
	// 			endif;
	// 				$where['where']['password'] = $password ;

	// 			$Fieldslist   = array('users_type','users_id','users_name','last_name','country_code','users_mobile','users_email','status','login_token','token','store_name','password');
	// 			$tblName      =  'uw_users';
	// 			$userDetails  =  $this->common_model->getParticularFieldByMultipleCondition($Fieldslist ,$tblName, $where);

	// 			if($users_long)    : $param['longitude']  = $users_long;    endif;
	// 			if($users_lat)     : $param['latitude']   = $users_lat;     endif;
	// 			if($device_id)     : $param['device_id']  = $device_id;     endif;
	// 			if($users_address) : $param['address']    = $users_address; endif;

	// 			// if(!empty($userDetails) && isset($userDetails['password']) &&  $password == $userDetails['password'] ):
	// 			if(!empty($userDetails) && isset($userDetails['password']) && md5($this->input->post('users_password')) == $userDetails['password'] && $userDetails['users_type'] == 'Users' && $userDetails['status'] == "A" ):	
					
	// 				if($userDetails['users_mobile'] == '556633485' || $userDetails['users_email'] == 'akashtest@gmail.com' ):
	// 				 $otp  = (int)1234;
	// 				else:
	// 				 $otp  = (int)rand(1000,9999);
	// 				endif;
					
	// 				$param['users_otp'] = 	$otp;
	// 				$this->geneal_model->editData('uw_users',$param,'users_id',(int)$userDetails['users_id']);
	// 				// OTP sending to number and Email...
	// 				if($country_code !='' && $users_mobile !=''):
	// 					$this->sms_model->accountVerifyOTP($country_code,$users_mobile,$otp);
	// 				elseif($users_email):
	// 				    $this->emailsendgrid_model->accountVerifyOTP($users_email,$otp);
	// 				endif;
	// 				echo outPut(1,lang('SUCCESS_CODE'),lang('OTP_SENT'),$result);
	// 			elseif($userDetails['status'] == "I" || $userDetails['status'] == "D" ):
	// 				echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_DELETED'),$result);
	// 			elseif(!empty($userDetails)):
	// 				echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_LOGIN'),$result);
	// 			else:
	// 				echo outPut(0,lang('SUCCESS_CODE'),lang('MOBILE_OR_PASS_INCORRECT'),$result);
	// 			endif;
	// 		endif;
	// 	else:
	// 		echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
	// 	endif;
	// }
    public function login()
	{
		try {

			$apiHeaderData  = getApiHeaderData();
			$this->generatelogs->putLog('APP',logOutPut($_POST));
			$result 	    = array();	
			if(requestAuthenticate(APIKEY,'POST')):

				$countryCode   = $this->input->post('country_code');
				$usersMobile   = $this->input->post('users_mobile');
				$usersEmail    = $this->input->post('users_email');
				$password      = md5($this->input->post('users_password'));
				$usersDeviceID = $this->input->post('users_device_id');
				$deviceID 	   = $this->input->post('device_id');
				$appName 	   = $this->input->post('app_name');
				$deviceType    = $this->input->post('device_type');
				$appVersion    = $this->input->post('app_version');

				$usersAddress  = $this->input->post('users_address');
				$usersLong 	   = $this->input->post('users_long');
				$usersLat 	   = $this->input->post('users_lat');
				$vericationType= $this->input->post('verification_type');

				if(!empty($usersMobile) && empty($countryCode) || empty($usersMobile) && empty($countryCode) && empty($usersEmail) ):
					throw new Exception(lang('USERDETAIL_EMPTY'), 1);
				elseif(empty($password)):
					throw new Exception(lang('PASSWORD_EMPTY'), 1);
				else:

					if(!empty($countryCode)):
						$where['where']['country_code'] = $countryCode;
						$where['where']['users_mobile'] = (int)$usersMobile;
					else:
						$where['where']['users_email']  = $usersEmail ;
					endif;
						$where['where']['password']     = $password ;

						$Fieldslist   = array('users_type','users_id','users_name','last_name','country_code','users_mobile','users_email','status','login_token','token','password','status');
						$tblName      =  'uw_users';
						$userDetails  =  $this->common_model->getParticularFieldByMultipleCondition($Fieldslist ,$tblName, $where);
						// echo "<pre>";print_r($userDetails);die();
						if(!empty($userDetails) && ( $password == $userDetails['password'] ) && $userDetails['users_type'] == 'Users' && $userDetails['status'] == "A"):

							if($userDetails['users_mobile'] == '556633485' || $userDetails['users_email'] == 'akashtest@gmail.com' ):
							 $otp  = (int)1234;
							else:
							 $otp  = (int)rand(1000,9999);
							endif;
							$param['otp_verication_type'] = $vericationType;
							$param['users_otp'] = $otp;
							$this->geneal_model->editData('uw_users',$param,'users_id',(int)$userDetails['users_id']);

							// OTP sending to number and Email...
							if($countryCode !='' && $usersMobile !='' && $vericationType == 'Whatsapp'):
								$this->sms_model->accountWhatsappVerifyOTP($countryCode,$usersMobile,$otp);
							elseif($countryCode !='' && $usersMobile !='' && $vericationType == 'SMS'  || (!empty($countryCode) && !empty($usersMobile)) ):
								$this->sms_model->accountVerifyOTP($countryCode,$usersMobile,$otp);
							
							elseif($usersEmail && $vericationType == 'Email' || !empty($usersEmail) ):
							    $this->emailsendgrid_model->accountVerifyOTP($usersEmail,$otp);
							endif;
							
							echo outPut(1,lang('SUCCESS_CODE'),lang('OTP_SENT'),$result); die();
						elseif($userDetails['status'] == "I" || $userDetails['status'] == "D" ):
							throw new Exception(lang('ACCOUNT_DELETED'), 1);
						elseif(!empty($userDetails) && $userDetails['users_type'] != 'Users' ):
							throw new Exception(lang('INVALID_LOGIN'), 1);
						else:
							echo outPut(0,lang('SUCCESS_CODE'),lang('MOBILE_OR_PASS_INCORRECT'),$result);
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
	* * Function name : verifyEmail
	* * Developed By  : Dilip Halder
	* * Purpose       : This function used for verify email
 	* * Date          : 07 July 2025
 	* * **********************************************************************/
	public function verifyEmail()
	{	
		try {

			$apiHeaderData  = getApiHeaderData();
			$this->generatelogs->putLog('APP',logOutPut($_POST));
			$result 	    = array();	

			if(requestAuthenticate(APIKEY,'POST')):
				
				$usersID    = $this->input->post('users_id');
				$userEmail 	= $this->input->post('email');
				if(empty($userEmail)):
				    throw new Exception(lang('EMPTY_USER_EMAIL'), 1);
				else:

					if(!empty($usersID)):
						$tableName1        = "uw_users";
						$exitUserWhereCon['where'] = array('users_id' => (int)$usersID);
						$Fieldslist1 	   = array('users_id','status','users_email');
						$exitingUserData   = $this->common_model->getParticularFieldByMultipleCondition($Fieldslist1,$tableName1,$exitUserWhereCon);
						// echo "<pre>";print_r($exitingUserData);die();
						if($exitingUserData['status'] == 'A' && ($userEmail != $exitingUserData['users_email'])):
							$emailWhere['users_email'] = $userEmail;
		   	 				$duplicateEmail = $this->common_model->checkDuplicate('uw_users',$emailWhere);
		   	 				if($duplicateEmail  >= 1):
								throw new Exception(lang('EMAIL_ALREADY_EXIST'), 1);
		   	 				endif;
						elseif( $exitingUserData['status'] == 'I' || $exitingUserData['status'] == 'D' ):
					    	throw new Exception(lang('ACCOUNT_INACIVE'), 1);
					    elseif($userEmail == $exitingUserData['users_email'] && $exitingUserData['is_email_verified'] == "Y" ):
					    	throw new Exception(lang('EMAIL_ALREADY_VERIFIED'), 1);
				    	elseif(empty($exitingUserData)):
					    	throw new Exception(lang('DATA_NOT_FOUND'), 1);
						endif;
					endif;

					// Added email id validation...
					$allowedDomains = array('gmail.com', 'yahoo.com', 'yahoo.co.in');
					$emailDomain    = substr(strrchr($userEmail, "@"), 1);
					if (!in_array(strtolower($emailDomain), $allowedDomains)):
			    		throw new Exception(lang('INVALID_EMAILID'), 1);
					endif;

					$emailWhere['users_email'] = $userEmail;
   	 				$duplicateEmail = $this->common_model->checkDuplicate('uw_users',$emailWhere);
					// echo "<pre>";print_r($duplicateEmail);die();

   	 				if(( !empty($usersID) && $duplicateEmail >1 ) || ( empty($usersID) && $duplicateEmail >=1 )  ):
						throw new Exception(lang('EMAIL_ALREADY_EXIST'), 1);
   	 				endif;

					// Added existing user delete user..
					$tableName  = "uw_users_verify";
					$this->common_model->deleteData($tableName,'users_email',$userEmail);


					//Email verification [parameters]...
					// $otp  		= (int)1234;
					$otp  		= (int)rand(1000,9999);
					$this->emailsendgrid_model->accountVerifyOTP($userEmail,$otp);

					/* Added verification input fields.. */ 
					$param['users_email']  = $userEmail;
					$param['users_otp']    = $otp;
					$param["creation_ip"]  = currentIp();
					$param["created_at"]   = date('Y-m-d H:i');
					$param["status"]       = "I";
					$param['is_verified']  = 'N';
					$users = $this->common_model->addData($tableName,$param);
					echo outPut(0,lang('SUCCESS_CODE'),lang('OTP_SENT_TO_EMAIL'),$result);
				endif;
			else:

			endif;
			
		} catch (Exception $e) {
			echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);
		}
	}

	/* * *********************************************************************
	* * Function name : verifyEmailCode
	* * Developed By  : Dilip Halder
	* * Purpose       : This function used for verify email code
 	* * Date          : 07 July 2025
 	* * **********************************************************************/
	public function verifyEmailCode()
	{	
		try {

			$apiHeaderData  = getApiHeaderData();
			$this->generatelogs->putLog('APP',logOutPut($_POST));
			$result 	    = array();	

			if(requestAuthenticate(APIKEY,'POST')):
				$userEmail 	= $this->input->post('email');
				$otp   	    = $this->input->post('otp');
				$usersID      = $this->input->post('users_id');
				if(empty($userEmail)):
				    throw new Exception(lang('EMPTY_USER_EMAIL'), 1);
				elseif(empty($otp)):
				    throw new Exception(lang('EMPTY_OTP'), 1);
				else:

					$Fieldslist        = array('users_email','users_otp');
					$tableName  	   = "uw_users_verify";
					$whereCon['where'] = array('users_email' => $userEmail);
					$userData          = $this->common_model->getParticularFieldByMultipleCondition($Fieldslist,$tableName,$whereCon);

					if(!empty($usersID)):

						$tableName1        = "uw_users";
						$exitUserWhereCon['where'] = array('users_id' => (int)$usersID);
						$Fieldslist1 	   = array();
						$exitingUserData   = $this->common_model->getParticularFieldByMultipleCondition($Fieldslist1,$tableName1,$exitUserWhereCon);
						// echo "<pre>";print_r($exitingUserData);die();

						if($exitingUserData['is_email_verified'] == 'N' &&  $otp == $userData['users_otp'] ):

							$param['users_otp']   = '';
							$param['is_verified'] = 'Y';
							$param['status'] 	  = 'A';
							$param['creation_ip'] = currentIp();
							$this->geneal_model->editDataByMultipleCondition($tableName,$param,$whereCon['where']);

							$exitUserParam['is_email_verified'] = 'Y';
							$this->geneal_model->editDataByMultipleCondition($tableName1,$exitUserParam,$exitUserWhereCon['where']);
							$exitingUserData['referred_by'] = $exitingUserData['referred_by']['$oid'];
							// Addeding varifing bonus to user's accounts...
							$this->common_model->bonusPoints($exitingUserData);
							echo outPut(1,lang('SUCCESS_CODE'),lang('OTP_VERIFIED'),$result);die();
						
						elseif($exitingUserData['is_email_verified'] == 'Y'):
					    	throw new Exception(lang('EMAIL_ALREADY_VERIFIED'), 1);
						elseif($otp != $userData['users_otp']):
					    	throw new Exception(lang('WRONG_OTP'), 1);
				    	elseif(empty($exitingUserData)):
					    	throw new Exception(lang('DATA_NOT_FOUND'), 1);
						endif;
					else:
						if(!empty($userData) && $userData['users_email'] == $userEmail && $userData['users_otp'] == $otp ):
							$param['users_otp']   = '';
							$param['is_verified'] = 'Y';
							$param['status'] 	  = 'A';
							$param['creation_ip'] = currentIp();
							$this->geneal_model->editDataByMultipleCondition($tableName,$param,$whereCon['where']);
							echo outPut(1,lang('SUCCESS_CODE'),lang('OTP_VERIFIED'),$result);die();
						elseif($userData['users_otp'] != $otp ):
					    	throw new Exception(lang('WRONG_OTP'), 1);
						elseif($userData['users_email'] != $userEmail ):
					    	throw new Exception(lang('INVALID_EMAILID'), 1);
					    else:
					    	throw new Exception(lang('DATA_NOT_FOUND'), 1);
						endif;
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
	* * Function name : verifyMobile
	* * Developed By  : Dilip Halder
	* * Purpose       : This function used for verify mobile
 	* * Date          : 07 July 2025
 	* * **********************************************************************/
	public function verifyMobile()
	{	
		try {

			$apiHeaderData  = getApiHeaderData();
			$this->generatelogs->putLog('APP',logOutPut($_POST));
			$result 	    = array();	

			if(requestAuthenticate(APIKEY,'POST')):
				
				$usersID      = $this->input->post('users_id');
				$countryCode  = $this->input->post('country_code');
				$userMobile   = $this->input->post('mobile');
				
				if(empty($countryCode)):
				    throw new Exception(lang('EMPTY_COUNTRYCODE'), 1);
				elseif(empty($userMobile)):
				    throw new Exception(lang('EMPTY_USERMOBILE'), 1);
				else:

					if(!empty($usersID)):
						$tableName1        = "uw_users";
						$exitUserWhereCon['where'] = array('users_id' => (int)$usersID);
						$Fieldslist1 	   = array('users_id','status','country_code','users_mobile','is_mobile_verified');
						$exitingUserData   = $this->common_model->getParticularFieldByMultipleCondition($Fieldslist1,$tableName1,$exitUserWhereCon);
						// echo "<pre>";print_r($exitingUserData);die();
						if( $exitingUserData['status'] == 'A' && ($userMobile != $exitingUserData['users_mobile']) ):
							$mobileWhere['users_mobile'] = (int)$userMobile;
		   	 				$duplicateMobile = $this->common_model->checkDuplicate('uw_users',$mobileWhere);
		   	 				if($duplicateMobile  >= 1):
								throw new Exception(lang('PHONE_ALREADY_EXIST'), 1);
		   	 				endif;
						elseif( $exitingUserData['status'] == 'I' || $exitingUserData['status'] == 'D' ):
					    	throw new Exception(lang('ACCOUNT_INACIVE'), 1);
					    elseif($userMobile == $exitingUserData['users_mobile'] && $exitingUserData['is_mobile_verified'] == "Y"):
					    	throw new Exception(lang('MOBILE_ALREADY_VERIFIED'), 1);
				    	elseif(empty($exitingUserData)):
					    	throw new Exception(lang('DATA_NOT_FOUND'), 1);
						endif;
					endif;

					$mobileWhere['users_mobile'] = (int)$userMobile;
   	 				$duplicateMobile = $this->common_model->checkDuplicate('uw_users',$mobileWhere);
   	 				if(( !empty($usersID) && $duplicateMobile >1 ) || ( empty($usersID) && $duplicateMobile >=1 )  ):
						throw new Exception(lang('PHONE_ALREADY_EXIST'), 1);
   	 				endif;

					// Added existing user delete user..
					$tableName = "uw_users_verify";
					$this->common_model->deleteData($tableName,'users_mobile',(int)$userMobile);

					//Mobile verification [parameters]...
					$otp  = (int)rand(1000,9999);
					// $otp  		= (int)1234;
					$this->sms_model->accountVerifyOTP($countryCode,$userMobile,$otp);

					/* Added verification input fields.. */ 
					$param['country_code'] = $countryCode;
					$param['users_mobile'] = (int)$userMobile;
					$param['users_otp']    = $otp;
					$param["creation_ip"]  = currentIp();
					$param["created_at"]   = date('Y-m-d H:i');
					$param["status"]       = "I";
					$param['is_verified']  = 'N';
					$users = $this->common_model->addData($tableName,$param);
					echo outPut(0,lang('SUCCESS_CODE'),lang('OTP_SENT_TO_MOBILE'),$result);

				endif;
			else:
				echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
			endif;
			
		} catch (Exception $e) {
			echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);
		}
	}

	/* * *********************************************************************
	* * Function name : verifyMobileCode
	* * Developed By  : Dilip Halder
	* * Purpose       : This function used for verify mobile code
 	* * Date          : 07 July 2025
 	* * **********************************************************************/
	public function verifyMobileCode()
	{	
		try {

			$apiHeaderData  = getApiHeaderData();
			$this->generatelogs->putLog('APP',logOutPut($_POST));
			$result 	    = array();	

			if(requestAuthenticate(APIKEY,'POST')):
				$countryCode  = $this->input->post('country_code');
				$userMobile   = $this->input->post('mobile');
				$otp   		  = $this->input->post('otp');
				$usersID      = $this->input->post('users_id');
				
				if(empty($countryCode)):
				    throw new Exception(lang('EMPTY_COUNTRYCODE'), 1);
				elseif(empty($userMobile)):
				    throw new Exception(lang('EMPTY_USERMOBILE'), 1);
				elseif(empty($otp)):
				    throw new Exception(lang('EMPTY_OTP'), 1);
				else:

					$Fieldslist        = array('country_code','users_mobile','users_otp');
					$tableName  	   = "uw_users_verify";
					$whereCon['where'] = array('country_code' => $countryCode , 'users_mobile' => (int)$userMobile);
					$userData          = $this->common_model->getParticularFieldByMultipleCondition($Fieldslist,$tableName,$whereCon);
						
					if(!empty($usersID)):

						$tableName1        = "uw_users";
						$exitUserWhereCon['where'] = array('users_id' => (int)$usersID );
						$Fieldslist 	   = array( );
						$exitingUserData   = $this->common_model->getParticularFieldByMultipleCondition($Fieldslist,$tableName1,$exitUserWhereCon);
						// echo "<pre>";print_r($exitingUserData);die();

						if( $exitingUserData['is_mobile_verified'] == 'N' &&  $otp == $userData['users_otp'] ):

							$param['users_otp']   = '';
							$param['is_verified'] = 'Y';
							$param['status'] 	  = 'A';
							$param['creation_ip'] = currentIp();
							$this->geneal_model->editDataByMultipleCondition($tableName,$param,$whereCon['where']);

							$exitUserParam['is_mobile_verified'] = 'Y';
							$this->geneal_model->editDataByMultipleCondition($tableName1,$exitUserParam,$exitUserWhereCon['where']);

							// Addeding varifing bonus to user's accounts...
							$exitingUserData['referred_by'] = $exitingUserData['referred_by']['$oid'];
							$this->common_model->bonusPoints($exitingUserData);
							echo outPut(1,lang('SUCCESS_CODE'),lang('OTP_VERIFIED'),$result);die();
						elseif($exitingUserData['is_mobile_verified'] == 'Y'):
					    	throw new Exception(lang('MOBILE_ALREADY_VERIFIED'), 1);
						elseif($otp != $userData['users_otp']):
					    	throw new Exception(lang('WRONG_OTP'), 1);
				    	elseif(empty($exitingUserData)):
					    	throw new Exception(lang('DATA_NOT_FOUND'), 1);
						endif;

					else:
						if(!empty($userData) && $userData['country_code'] == $countryCode && $userData['users_mobile'] == $userMobile && $userData['users_otp'] == $otp ):
							$param['users_otp']   = '';
							$param['is_verified'] = 'Y';
							$param['status'] 	  = 'A';
							$param['creation_ip'] = currentIp();
							$this->geneal_model->editDataByMultipleCondition($tableName,$param,$whereCon['where']);
							echo outPut(1,lang('SUCCESS_CODE'),lang('OTP_VERIFIED'),$result);die();
						elseif($userData['users_otp'] != $otp ):
					    	throw new Exception(lang('WRONG_OTP'), 1);
						elseif($userData['country_code'] != $countryCode ):
					    	throw new Exception(lang('INVALID_COUNTRYCODE'), 1);
						elseif($userData['users_mobile'] != $userMobile ):
					    	throw new Exception(lang('INVALID_MOBILENUMBER'), 1);
					    else:
					    	throw new Exception(lang('DATA_NOT_FOUND'), 1);
						endif;
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
	* * Function name : verifyWhatsapp
	* * Developed By  : Dilip Halder
	* * Purpose       : This function used for verify Whatsapp code
 	* * Date          : 14 July 2025
 	* * **********************************************************************/
	public function verifyWhatsapp()
	{ 
		try {

			$apiHeaderData 		=	getApiHeaderData();
			$this->generatelogs->putLog('APP',logOutPut($_POST));
			$result 							= 	array();	
			if(requestAuthenticate(APIKEY,'POST')):

				$usersID      = $this->input->post('users_id');
				$countryCode  = $this->input->post('country_code');
				$userMobile   = $this->input->post('mobile');
				
				if(empty($countryCode)):
				    throw new Exception(lang('EMPTY_COUNTRYCODE'), 1);
				elseif(empty($userMobile)):
				    throw new Exception(lang('EMPTY_USERMOBILE'), 1);
				else:

					if(!empty($usersID)):
						$tableName1        = "uw_users";
						$exitUserWhereCon['where'] = array('users_id' => (int)$usersID);
						$Fieldslist1 	   = array('users_id','status','country_code','users_mobile','is_mobile_verified');
						$exitingUserData   = $this->common_model->getParticularFieldByMultipleCondition($Fieldslist1,$tableName1,$exitUserWhereCon);
						// echo "<pre>";print_r($exitingUserData);die();
						if( $exitingUserData['status'] == 'A' && ($userMobile != $exitingUserData['users_mobile']) ):
							$mobileWhere['users_mobile'] = (int)$userMobile;
		   	 				$duplicateMobile = $this->common_model->checkDuplicate('uw_users',$mobileWhere);
		   	 				if($duplicateMobile  >= 1):
								throw new Exception(lang('PHONE_ALREADY_EXIST'), 1);
		   	 				endif;
						elseif( $exitingUserData['status'] == 'I' || $exitingUserData['status'] == 'D' ):
					    	throw new Exception(lang('ACCOUNT_INACIVE'), 1);
					    elseif($userMobile == $exitingUserData['users_mobile'] && $exitingUserData['is_mobile_verified'] == "Y"):
					    	throw new Exception(lang('MOBILE_ALREADY_VERIFIED'), 1);
				    	elseif(empty($exitingUserData)):
					    	throw new Exception(lang('DATA_NOT_FOUND'), 1);
						endif;
					endif;

					$mobileWhere['users_mobile'] = (int)$userMobile;
   	 				$duplicateMobile = $this->common_model->checkDuplicate('uw_users',$mobileWhere);
   	 				if(( !empty($usersID) && $duplicateMobile >1 ) || ( empty($usersID) && $duplicateMobile >=1 )  ):
						throw new Exception(lang('PHONE_ALREADY_EXIST'), 1);
   	 				endif;

					// Added existing user delete user..
					$tableName = "uw_users_verify";
					$this->common_model->deleteData($tableName,'users_mobile',(int)$userMobile);

					//Mobile verification [parameters]...
					// $otp  		= (int)1234;
					$otp  = (int)rand(1000,9999);
					$this->sms_model->accountWhatsappVerifyOTP($countryCode,$userMobile,$otp);


					/* Added verification input fields.. */ 
					$param['country_code'] = $countryCode;
					$param['users_mobile'] = (int)$userMobile;
					$param['users_otp']    = $otp;
					$param['verification_type'] = 'whatsapp';
					$param["creation_ip"]  = currentIp();
					$param["created_at"]   = date('Y-m-d H:i');
					$param["status"]       = "I";
					$param['is_verified']  = 'N';
					$users = $this->common_model->addData($tableName,$param);
					echo outPut(0,lang('SUCCESS_CODE'),lang('OTP_SENT_TO_MOBILE'),$result);

				endif;
			else:
				echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
			endif;
			
		} catch (Exception $e) {
			echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);
		}

	}

	/* * *********************************************************************
	 * * Function name : verifyOTP
	 * * Developed By  : Dilip Halder
	 * * Purpose       : This function used for OTP varification.
	 * * Date          : 24 February 2025
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

					$token 				  	= $this->geneal_model->generatetoken();
					$param['users_otp']   	= '';
					$param['token'] 	  	= $token;
					$param['login_token']   = $token;
					$param['device_id'] 	  = $this->input->post('device_id');
					$param['users_device_id'] = $this->input->post('users_device_id');
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

	
	/* * *********************************************************************
	 * * Function name : moveToWallet
	 * * Developed By  : Dilip Halder
	 * * Purpose       : This function used for OTP varification.
	 * * Date          : 24 February 2025
	 * * **********************************************************************/
	public function moveToWallet()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):
			
			/* Added post variable getting from post request */
			$userID       = $this->input->post('users_id');
			$orderID      = $this->input->post('order_id');
			$redeemStatus = $this->input->post('redeem_status');
			$redeemByMode = $this->input->post('redeem_by_mode');

			/* Added conditions to prevent empty required post datas. */ 
			if(empty($userID)){
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			}elseif(empty($orderID)){
				echo outPut(0,lang('SUCCESS_CODE'),lang('ORDER_ID_EMPTY'),$result);
			}else{
					
				/* Start function main section*/
				try {

					/* Checked User Validation */
					$requestFrom 	  = "app";
					$validationResult = $this->common_model->userValidate($userID,$requestFrom);
					
					// Checking coupon in collection.
					$tableName 			= "uw_uwin_winner";
					$whereCon['where']  = array('order_id' => $orderID , 'status' => (int)1 );
				 	$WinnerList 	    = $this->common_model->getData( 'multiple',$tableName,$whereCon );
					
					if(!empty($WinnerList)):

						$tableName	  = "uw_users";
					    $Fields 	  = array('_id','users_id' ,'availableArabianPoints','redeeming_amount_limit','totalArabianPoints');
				 	    $userDetails  = $this->common_model->getSingleDataByParticularField($Fields,$tableName,'users_id',(int)$userID);
				 	    // if(!empty($userDetails['redeeming_amount_limit'])):
				 	   	// 	$redeeming_amount_limit =  $userDetails['redeeming_amount_limit']; 
				 	    // else:
				 	   	// 	$redeeming_amount_limit =  999; 
				 	    // endif;

				 	    $totalPrizeAmount    = 0;
					 	foreach ($WinnerList as $key => $items) :
					 	 $totalPrizeAmount   = $totalPrizeAmount+ $items['amount'];
						 	if(!empty($items['redeem_status']) && $items['redeem_status'] == 'paid'):
						 		echo outPut(0,lang('SUCCESS_CODE'),lang('ALREADY_REDEEM'),$result);die();
						 	endif;
						 	// if((int)$totalPrizeAmount >= $redeeming_amount_limit &&  $users_id != 100000000000110 ):
						 	// 	echo outPut(0,lang('SUCCESS_CODE'),lang('BIG_WINNER_TEXT'),$result);die();
						 	// endif;
					 	endforeach;

					 	if($WinnerList):
						 	$updateParams['redeem_status'] 	= $redeemStatus;
							$updateParams['redeem_by_mode'] = $redeemByMode;
							$updateParams["modified_at"]    = date('Y-m-d H:i');
							$updateParams['seller_id'] 		= (int)$userID;
							$updateParams['created_ip'] 	= currentIp();
							$WInnner_whereCon   = array('order_id' => $orderID, 'status' => (int)'1','redeem_status' => array('$ne' => 'paid') );
							$winnerRedeemResult = $this->common_model->editMultipleDataByMultipleCondition('uw_uwin_winner', $updateParams,$WInnner_whereCon);
							
							if($winnerRedeemResult > 0 ):
								//Credting user's winning prize amount to users account.
								$userParam['totalArabianPoints']      = (float)$userDetails['totalArabianPoints']      + $totalPrizeAmount;
								$userParam['availableArabianPoints']  = (float) $userDetails['availableArabianPoints'] + $totalPrizeAmount;
								$userParam["update_date"]    		  = date('Y-m-d H:i');
								$userResult = $this->common_model->editData('uw_users', $userParam,'users_id',(int)$userID);

								if(!empty($userResult)):
									/* Load Balance Table -- after Sign Up*/
									$Redeemparam["load_balance_id"]          =   (int)$this->geneal_model->getNextSequence('uw_loadBalance');
									$Redeemparam["user_oid"]        	     =   new MongoDB\BSON\ObjectId($userDetails['_id']['$id']);
									// $Redeemparam["order_oid"]       		 =   new MongoDB\BSON\ObjectId($order_oid);
									$Redeemparam["order_id"]       		     =   $orderID;
									$Redeemparam["product_id"]       		 =   (int)$productID;
									$Redeemparam["user_id_deb"]              =   (int)0;
									$Redeemparam["user_id_cred"]             =   (int)$userID;
									$Redeemparam["upoints"]       		     =   (float)$totalPrizeAmount;
									$Redeemparam["record_type"]              =   'Credit';
									$Redeemparam["narration"]  			     =   'Moved winning Prize';
									$Redeemparam["remarks"]  			     =   "Prize for ( ".$orderID." ) transferred to wallet.";
									$Redeemparam["availableArabianPoints"] 	 =   (float)$userDetails['availableArabianPoints'];
									$Redeemparam["end_balance"] 		 	 =   (float)$userDetails['availableArabianPoints']+$totalPrizeAmount;
									$Redeemparam["creation_ip"]         	 =   currentIp();
									$Redeemparam["created_at"]          	 =   date('Y-m-d H:i');
									$Redeemparam["created_by"]         	  	 =   (int)$userID;
									$Redeemparam["status"]               	 =   "A";
									$this->geneal_model->addData('uw_loadBalance', $Redeemparam);
									$result = array('payment_date' => date('Y-m-d H:i'));
							  	 	echo outPut(1,lang('SUCCESS_CODE'),lang('COUPON_REDEEMED_SUCCESFULLY'),$result);die();
						  	 	else:
							  	 	echo outPut(1,lang('SUCCESS_CODE'),lang('BALANCE_TRANSERFER_EORROR'),$result);die();
								endif;

							else:
								echo outPut(0,lang('SUCCESS_CODE'),lang('ALREADY_REDEEM'),$result);
							endif;

					 	endif;
					else:
						echo outPut(0,lang('SUCCESS_CODE'),lang('ORDER_ID_EMPTY'),$result);
					endif;

				} catch (Exception $e) {
        			echo outPut(0, lang('SUCCESS_CODE'), lang('ERROR_OCCURRED'), $e->getMessage());
				}

			}
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}
	
	/* * *********************************************************************
	 * * Function name : winningOrders
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used get winning orders
	 * * Date 		   : 20 May 2025
	 * * **********************************************************************/
	public function winningOrders()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_GET));
		$result 			= 	array();

		if(requestAuthenticate(APIKEY,'POST')):
			
			$usersId    = $this->input->post('users_id');
			$startDate  = $this->input->post('start_date');
			$endDate    = $this->input->post('end_date');

			if(empty($usersId)):
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
			// elseif(empty($startDate)):
			// 	echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_START_DATE'),$result);die();
			// elseif(empty($endDate)):
			// 	echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_END_DATE'),$result);die();
			else:
					
				$tbl_name  = 'uw_lotto_orders';
				$whereCon['where']['user_id']    = (int)$usersId;
				if($startDate):
					$whereCon['where']['created_at']['$gte'] = date('Y-m-d H:i' , strtotime($startDate));
				endif;
				if($endDate):
					$whereCon['where']['created_at']['$lte'] = date('Y-m-d H:i' , strtotime($endDate));
				endif;
				$OrderData = $this->common_model->getFieldInArray('order_id',$tbl_name,$whereCon);
				$where2['where']['order_id']      = array('$in' => $OrderData );
				$where2['where']['redeem_status'] = array('$nin' => array('paid','pending') );
				$where2['where']['status'] 		  = (int)1;
				// echo "<pre>";print_r($where2);die();
				$result = $this->geneal_model->getOrderHistory($where2);
				if(!empty($result)):
		 			echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$result);	
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('NOT_WINNER'),$result);
				endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : bankTranser
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used trnsfer winning amount..
	 * * Date 		   : 21 May 2025
	 * * **********************************************************************/
	public function bankTranser()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_GET));
		$result 			= 	array();

		if(requestAuthenticate(APIKEY,'POST')):
			
			$userID        		= $this->input->post('users_id');
			$orderIds       	= $this->input->post('order_ids');
			$accountHolderName  = $this->input->post('account_holder_name');
			$bankName  			= $this->input->post('bank_name');
			$accountNumber  	= $this->input->post('account_number');
			$swiftBicCode  		= $this->input->post('swift_bic_code');
			$iben  				= $this->input->post('iben');
			$amount  			= $this->input->post('amount');

			if(empty($userID)):
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
			
			elseif(empty($orderIds)):
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_ORDERIDS'),$result);die();

			elseif(empty($accountHolderName)):
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_ACCOUNT_HOLDER_NAME'),$result);die();
			
			elseif(empty($bankName)):
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_BANK_NAME'),$result);die();
			
			elseif(empty($accountNumber)):
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_ACCOUNT_NUMBER'),$result);die();
			
			elseif(empty($swiftBicCode)):
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_SWIFT_BIC_CODE'),$result);die();
			
			elseif(empty($iben)):
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_IBEN'),$result);die();
			
			elseif(empty($amount)):
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_AMOUNT'),$result);die();
			else:

				try {

						$orderArray  = explode(',', $orderIds);
						$orderId 	 = array_map('trim', $orderArray);

						$tblName 	 = 'uw_lotto_orders';
						$Fieldslist  = array('user_id','user_oid');
						$OrderData	 = $this->common_model->getSingleDataByParticularField( $Fieldslist , $tblName , 'order_id',$orderId[0]);
						// Checking order id..
						if(!empty($OrderData['user_id']) && $OrderData['user_id'] == $userID ):

							$where22['where']['order_id']      = array('$in' => $orderId );
							// $where22['where']['redeem_status'] = array('$nin' => array('paid','pending') );
							$pendingData = $this->geneal_model->getOrderHistory($where22);
							if(!empty($pendingData)):
								//Prize Amount Cross Check.
								foreach ($pendingData as $key => $item1):
									if(in_array('pending', $item1['redeem_status'])):
										$error = "The winning amount is already in the process of being redeemed for order ID " . $item1['order_id'];
						 		 		throw new Exception($error);
									endif;
								endforeach;
							endif;

							$where2['where']['order_id']      = array('$in' => $orderId );
							$where2['where']['redeem_status'] = array('$nin' => array('paid','pending') );
							$winningData = $this->geneal_model->getOrderHistory($where2);
							// echo "<pre>";print_r($winningData);die();
							if(!empty($winningData)):

								//Prize Amount Cross Check.
								$totalWinningAmount = 0; 
								foreach ($winningData as $key => $item):
									if(in_array('unpaid', $item['redeem_status'])):
										$totalWinningAmount += $item['total_amount'];

										$WinningOrderData['order_id'][]     = $item['order_id'];
										$WinningOrderData['total_amount'][] = $item['total_amount'];
									endif;
									if(in_array('pending', $item['redeem_status'])):
										$error = "The winning amount is already in the process of being redeemed for order ID " . $item['order_id'];
						 		 		throw new Exception($error);
									endif;
								endforeach;

								if($totalWinningAmount == $amount):
									
									$updateParams['redeem_status'] 	= 'pending';
									$updateParams['redeem_by_mode'] = 'bank_transfer';
									$updateParams["modified_at"]    = date('Y-m-d H:i');
									$updateParams['seller_id'] 		= (int)$userID;
									$updateParams['created_ip'] 	= currentIp();
									
									$whereCondition['order_id']      = array('$in' => $orderId); 
									$whereCondition['redeem_status'] = array('$ne' => 'paid'); 
									$result = $this->common_model->editMultipleDataByMultipleCondition('uw_uwin_winner',$updateParams,$whereCondition);
									$result2 = $this->common_model->editMultipleDataByMultipleCondition('uw_raffle_winner',$updateParams,$whereCondition);
								 	    
									if($result > 0 || $result2 > 0):
										
										$tableName	  = "uw_users";
									    $Fields 	  = array('_id','users_id' ,'availableArabianPoints','redeeming_amount_limit','totalArabianPoints');
								 	    $userDetails  = $this->common_model->getSingleDataByParticularField($Fields,$tableName,'users_id',(int)$userID);
								 	    $userOid      = $userDetails['_id']['$id'];



									 	$prams['withdraw_id']  			= (int)$this->geneal_model->getNextSequence('uw_loadBalance');
								 	    $prams['request_id']   			= (int)$this->geneal_model->getNextSequence('uw_withdraw_requests');
								        $prams['user_id']     			= (int)$userID;
								        $prams['user_oid']     			= new MongoDB\BSON\ObjectId($userOid);
									 	$prams['type']                  = 'Bank Transfer';
							            $prams['amount']                = (float)$amount;
							            $prams['account_holder_name']   = $accountHolderName;
							            $prams['bank_name']             = $bankName;
							            $prams['account_no']            = base64_encode($accountNumber);
							            $prams['swiftBicCode']          = base64_encode($swiftBicCode);
							            $prams['iben']                  = $iben;
							            $prams['orderIds']              = $orderIds;
							            $prams["orderData"]       		= $WinningOrderData;
									 	$prams['creation_ip']  			= $this->input->ip_address();
								        $prams['created_at']   			= date('Y-m-d H:i');
								        $prams['created_by']  			= (int)$userID;
								        $prams['status']       			= 'P';
								        $result = $this->geneal_model->addData('uw_withdraw_requests', $prams);
									 
									 	/* Load Balance Table -- after Sign Up*/
										$Redeemparam["load_balance_id"]          = (int)$this->geneal_model->getNextSequence('uw_loadBalance');
										$Redeemparam["user_oid"]        	     = new MongoDB\BSON\ObjectId($userOid);
										$Redeemparam['request_id'] 				 = new MongoDB\BSON\ObjectId($result['_id']->{'$id'});
										$Redeemparam["order_id"]       		     = $orderIds;
										$Redeemparam["user_id_deb"]              = (int)0;
										$Redeemparam["user_id_cred"]             = (int)$userID;
										$Redeemparam["upoints"]       		     = (float)$totalWinningAmount;
										$Redeemparam["record_type"]              = 'Credit';
										$Redeemparam["narration"]  			     = 'Bank Transfer';
										$Redeemparam["remarks"]  			     = "Pending"."( ".$orderIds." )";
										$Redeemparam["availableArabianPoints"] 	 = (float)$userDetails['availableArabianPoints'];
										$Redeemparam["end_balance"] 		 	 = (float)$userDetails['availableArabianPoints'];
										$Redeemparam["creation_ip"]         	 = currentIp();
										$Redeemparam["created_at"]          	 = date('Y-m-d H:i');
										$Redeemparam["created_by"]         	  	 = (int)$userID;
										$Redeemparam["status"]               	 = "A";
										$this->geneal_model->addData('uw_loadBalance', $Redeemparam);
										$result = array('payment_date' => date('Y-m-d H:i'));
								  	 	echo outPut(1,lang('SUCCESS_CODE'),lang('BANK_TRANSER_REQUEST'),$result);die();
									else:
						 		 		throw new Exception(lang('FORBIDDEN_MSG'));
									endif;
								else:
						 		 	throw new Exception(lang('INVALID_AMOUNT'));
								endif;
							else:
							  throw new Exception(lang('NOT_WINNER'));
							endif;
						else:
							 throw new Exception(lang('INVALID_USER_ID'));
						endif;

					} catch (Exception $e) {
					 	echo outPut(0, lang('SUCCESS_CODE'), $e->getMessage(), $result);
        			 	die();
					}
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : crytoTranser
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used trnsfer winning amount..
	 * * Date 		   : 21 May 2025
	 * * **********************************************************************/
	public function crytoTranser()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_GET));
		$result 			= 	array();

		if(requestAuthenticate(APIKEY,'POST')):
			
			$userID        	= $this->input->post('users_id');
			$orderIds       = $this->input->post('order_ids');
			$crytoAccountID = $this->input->post('cryto_account_id');
			$amount  		= $this->input->post('amount');

			if(empty($userID)):
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
			
			elseif(empty($orderIds)):
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_ORDERIDS'),$result);die();
			
			elseif(empty($crytoAccountID)):
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_IBEN'),$result);die();
			
			elseif(empty($amount)):
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_AMOUNT'),$result);die();
			else:

				try {

						$orderArray  = explode(',', $orderIds);
						$orderId 	 = array_map('trim', $orderArray);

						$tblName 	 = 'uw_lotto_orders';
						$Fieldslist  = array('user_id','user_oid');
						$OrderData	 = $this->common_model->getSingleDataByParticularField( $Fieldslist , $tblName , 'order_id',$orderId[0]);
						// Checking order id..
						if(!empty($OrderData['user_id']) && $OrderData['user_id'] == $userID ):

							$where22['where']['order_id']      = array('$in' => $orderId );
							// $where22['where']['redeem_status'] = array('$nin' => array('paid','pending') );
							$pendingData = $this->geneal_model->getOrderHistory($where22);
							if(!empty($pendingData)):
								//Prize Amount Cross Check.
								foreach ($pendingData as $key => $item1):
									if(in_array('pending', $item1['redeem_status'])):
										$error = "The winning amount is already in the process of being redeemed for order ID " . $item1['order_id'];
						 		 		throw new Exception($error);
									endif;
								endforeach;
							endif;

							$where2['where']['order_id']      = array('$in' => $orderId );
							$where2['where']['redeem_status'] = array('$nin' => array('paid','pending') );
							$winningData = $this->geneal_model->getOrderHistory($where2);
							// echo "<pre>";print_r($winningData);die();
							if(!empty($winningData)):

								//Prize Amount Cross Check.
								$totalWinningAmount = 0; 
								foreach ($winningData as $key => $item):
									if(in_array('unpaid', $item['redeem_status'])):
										$totalWinningAmount += $item['total_amount'];

										$WinningOrderData['order_id'][]     = $item['order_id'];
										$WinningOrderData['total_amount'][] = $item['total_amount'];
									endif;
									if(in_array('pending', $item['redeem_status'])):
										$error = "The winning amount is already in the process of being redeemed for order ID " . $item['order_id'];
						 		 		throw new Exception($error);
									endif;
								endforeach;

								// $winnerJson = json_encode($WinningOrderData);


								if($totalWinningAmount == $amount):
									
									$updateParams['redeem_status'] 	= 'pending';
									$updateParams['redeem_by_mode'] = 'cryto_transfer';
									$updateParams["modified_at"]    = date('Y-m-d H:i');
									$updateParams['seller_id'] 		= (int)$userID;
									$updateParams['created_ip'] 	= currentIp();
									
									$whereCondition['order_id']      = array('$in' => $orderId); 
									$whereCondition['redeem_status'] = array('$ne' => 'paid'); 
									$result = $this->common_model->editMultipleDataByMultipleCondition('uw_uwin_winner',$updateParams,$whereCondition);
									$result2 = $this->common_model->editMultipleDataByMultipleCondition('uw_raffle_winner',$updateParams,$whereCondition);
								 	    
									if($result > 0 || $result2 > 0):
										
										$tableName	  = "uw_users";
									    $Fields 	  = array('_id','users_id' ,'availableArabianPoints','redeeming_amount_limit','totalArabianPoints');
								 	    $userDetails  = $this->common_model->getSingleDataByParticularField($Fields,$tableName,'users_id',(int)$userID);
								 	    $userOid      = $userDetails['_id']['$id'];

									 	$prams['withdraw_id']  			= (int)$this->geneal_model->getNextSequence('uw_loadBalance');
								 	    $prams['request_id']   			= (int)$this->geneal_model->getNextSequence('uw_withdraw_requests');
								        $prams['user_id']     			= (int)$userID;
								        $prams['user_oid']     			= new MongoDB\BSON\ObjectId($userOid);
									 	$prams['type']                  = 'Cryto Transfer';
							            $prams['amount']                = (float)$amount;
							            $prams['cryto_account_id']      = base64_encode($crytoAccountID);
							            $prams['orderIds']              = $orderIds;
							            $prams["orderData"]       		= $WinningOrderData;
									 	$prams['creation_ip']  			= $this->input->ip_address();
								        $prams['created_at']   			= date('Y-m-d H:i');
								        $prams['created_by']  			= (int)$userID;
								        $prams['status']       			= 'P';
								        $result = $this->geneal_model->addData('uw_withdraw_requests', $prams);
									 
									 	/* Load Balance Table -- after Sign Up*/
										$Redeemparam["load_balance_id"]          = (int)$this->geneal_model->getNextSequence('uw_loadBalance');
										$Redeemparam["user_oid"]        	     = new MongoDB\BSON\ObjectId($userOid);
										$Redeemparam['request_id'] 				 = new MongoDB\BSON\ObjectId($result['_id']->{'$id'});
										$Redeemparam["order_id"]       		     = $orderIds;
										$Redeemparam["user_id_deb"]              = (int)0;
										$Redeemparam["user_id_cred"]             = (int)$userID;
										$Redeemparam["upoints"]       		     = (float)$totalWinningAmount;
										$Redeemparam["record_type"]              = 'Credit';
										$Redeemparam["narration"]  			     = 'Cryto Transfer';
										$Redeemparam["remarks"]  			     = "Pending"."( ".$orderIds." )";
										$Redeemparam["availableArabianPoints"] 	 = (float)$userDetails['availableArabianPoints'];
										$Redeemparam["end_balance"] 		 	 = (float)$userDetails['availableArabianPoints'];
										$Redeemparam["creation_ip"]         	 = currentIp();
										$Redeemparam["created_at"]          	 = date('Y-m-d H:i');
										$Redeemparam["created_by"]         	  	 = (int)$userID;
										$Redeemparam["status"]               	 = "A";
										$this->geneal_model->addData('uw_loadBalance', $Redeemparam);
										$result = array('payment_date' => date('Y-m-d H:i'));
								  	 	echo outPut(1,lang('SUCCESS_CODE'),lang('CRYTO_TRANSER_REQUEST'),$result);die();
									else:
						 		 		throw new Exception(lang('FORBIDDEN_MSG'));
									endif;
								else:
						 		 	throw new Exception(lang('INVALID_AMOUNT'));
								endif;
							else:
							  throw new Exception(lang('NOT_WINNER'));
							endif;
						else:
							 throw new Exception(lang('INVALID_USER_ID'));
						endif;

					} catch (Exception $e) {
					 	echo outPut(0, lang('SUCCESS_CODE'), $e->getMessage(), $result);
        			 	die();
					}
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}
 
}
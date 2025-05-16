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
		$this->load->model(array('sms_model','notification_model','emailsendgrid_model'));
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
	public function signup()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):
			
			if($this->input->post('users_name') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('NAME_EMPTY'),$result);
			// elseif($this->input->post('last_name') == ''): 
			// 	echo outPut(0,lang('SUCCESS_CODE'),lang('LASTNAME_EMPTY'),$result);
			// elseif($this->input->post('users_email') == ''): 
			// 	echo outPut(0,lang('SUCCESS_CODE'),lang('EMAIL_EMPTY'),$result);
			elseif($this->input->post('country_code') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('COUNTRY_CODE_EMPTY'),$result);
			elseif($this->input->post('users_mobile') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('PHONE_EMPTY'),$result);
			elseif($this->input->post('users_password') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('PASSWORD_EMPTY'),$result);
			else:
				$error = 'NO';
				if(strlen($this->input->post('users_mobile')) >= 8):
					$where = array('users_mobile' => (int)$this->input->post('users_mobile'));
				else:
					$where = array('users_mobile' => (float)$this->input->post('users_mobile'));
				endif;
		
			    $query = $this->geneal_model->checkDuplicate('uw_users',$where);

				if(!empty($query)):
					$error = "MOBILE_ERROR";
				endif;
				if($this->input->post('users_email') && $error='NO'):
					$where = array('users_email' => strtolower($this->input->post('users_email')));
			    	$count = $this->geneal_model->checkDuplicate('uw_users',$where);
					if($count > 0):
						$error = "EMAIL_ERROR";
					endif;
				endif;

			    if ($error == 'NO'):
			    	$where1 = array('users_mobile' => (int)$this->input->post('users_mobile') );
				    $query1 = $this->geneal_model->checkDuplicate('uw_users',$where1);
				    if (empty($query1)):
				    	
						$signupBonus            =   SIGNUPBONUS;//USER_SIGNUP_BONUS;
						$otp  					= (int)rand(100000,999999);
						if($this->input->post('users_email')):
							$email = $this->input->post('users_email');
						else:
							$email = '';
						endif;
						$insert_data = array(
							"users_type"        =>  'Users',
							"users_id"          =>  (int)$this->geneal_model->getNextSequence('uw_users'),
							"users_seq_id"      =>  $this->geneal_model->getNextIdSequence('users_seq_id', 'Users'),
							"referral_code"     =>   strtoupper(uniqid(16)),
							"users_name"        =>  $this->input->post('users_name'),
							"last_name"         =>  $this->input->post('last_name'),
							"country_code"      =>  $this->input->post('country_code'),
							"users_mobile"      =>  (int)$this->input->post('users_mobile'),
							"users_email"       =>  $this->input->post('users_email'),
							"password"          =>  md5($this->input->post('users_password')),
							"status"            =>  'I',
							"is_verify"         =>  'N',     
							'users_otp'         =>  $otp,
							"device_id"         =>  $this->input->post('device_id'),
							"latitude"          =>  '',
							"longitude"         =>  '',
							'created_at'        =>  date('Y-m-d h:i'),   
							'created_ip'        =>  currentIp(),
							'created_by'        =>  'Self',
							"created_from" 		=>  'App',
		    				"area" 				=>	$this->input->post('area'),
							"device_type" 		=>	$this->input->post('device_type'),
		    				"app_version" 		=>	$this->input->post('app_version'),
		    				"app_name" 			=>	$this->input->post('app_name'),
							'totalArabianPoints' =>  (float)$signupBonus,
							'availableArabianPoints' => (float)$signupBonus,
							'login_token' 		  => $this->geneal_model->generatetoken(),
							"term_condition"       =>  'Yes'
						);

						if($insert_data):

			                $result1 = $this->geneal_model->addData('uw_users', $insert_data);
			                $result_data = array(
								"users_type"        =>  'Users',
								"users_id"          =>  $insert_data['users_id'],
								"users_name"        =>  $this->input->post('users_name'),
								"last_name"         =>  $this->input->post('last_name'),
								"country_code"      =>  $this->input->post('country_code'),
								"users_mobile"      =>  (int)$this->input->post('users_mobile'),
								"users_email"       =>  $this->input->post('users_email'),
								"status"            =>  'I',
								"is_verify"         =>  'N',     
								'login_token' 		=> $insert_data['login_token']
							);


							// $user_OId = $result1['_id']->{'$id'};
			                // /* Load Balance Table -- after Sign Up*/
			                // $Cashbparam["load_balance_id"]      =   (int)$this->geneal_model->getNextSequence('uw_loadBalance');
			                // $Cashbparam["user_oid_cred"]        =   new MongoDB\BSON\ObjectId($user_OId);
			                // $Cashbparam["user_id_cred"]         =   (int)$insert_data['users_id'];
			                // $Cashbparam["arabian_points"]       =   (float)$signupBonus;
			                // $Cashbparam["record_type"]          =   'Credit';
			                // $Cashbparam["arabian_points_from"]  =   'Signup Bonus';
			                // $Cashbparam["creation_ip"]          =   currentIp();
			                // $Cashbparam["created_at"]           =   date('Y-m-d H:i');
			                // $Cashbparam["created_by"]           =   (int)$insert_data['users_id'];
			                // $Cashbparam["status"]               =   "A";
			                // $this->geneal_model->addData('uw_loadBalance', $Cashbparam);
			                /* End */

							$mobile_no = $insert_data['country_code'].$insert_data['users_mobile'];
			                $this->sms_model->sendForgotPasswordOtpSmsToUser($mobile_no,$otp,$insert_data['country_code']);
							if($email){
								$this->emailsendgrid_model->sendRegistrationMailToUser($insert_data,$otp);
							}
			                $result['userData'] 				=	$result_data;
			                echo outPut(1,lang('SUCCESS_CODE'),lang('REGISTRATION_SUCCESS'),$result);
			            else:
			            	echo outPut(0,lang('SUCCESS_CODE'),lang('NOT_LOGIN_MSG'),$result);
			            endif;
				    else:
				    	echo outPut(0,lang('SUCCESS_CODE'),lang('PHONE_ALREADY_EXIST'),$result);
				    endif;
			    else:
					if($error == 'EMAIL_ERROR'):
						echo outPut(0,lang('SUCCESS_CODE'),lang('EMAIL_ALREADY_EXIST'),$result);
					else:
						echo outPut(0,lang('SUCCESS_CODE'),lang('PHONE_ALREADY_EXIST'),$result);
					endif;
			    endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
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

				$userData = array();
				if(is_numeric($this->input->post('users_email'))):
					$where 			=	[ 'users_mobile' => (int)$this->input->post('users_email') ];
					$tblName 		=	'uw_users';
					$userDetails 	=	$this->geneal_model->getOnlyOneData($tblName, $where);
					if(!empty($userDetails)): $userData = $userDetails; endif;
				else:
					$where 			=	[ 'users_email' => $this->input->post('users_email') ];
					$tblName 		=	'uw_users';
					$userDetails 	=	$this->geneal_model->getOnlyOneData($tblName, $where);
					if(!empty($userDetails)): $userData = $userDetails; endif;
				endif;

				if(!empty($userData) && isset($userData['password']) && md5($this->input->post('users_password')) == $userData['password']):
					if($userData['status'] == 'A'):

						$posDeviceID = $this->input->post('pos_device_id');
						
						if(!empty($userDetails['pos_device_id'])  && $userDetails['pos_device_id'] !=  $posDeviceID && !empty($posDeviceID) ):
							echo outPut(0,lang('NOT_LOGIN_ACTION'),lang('POS_USER_DIFFERENT'),$result);die();
						endif;

						if(!empty($userData['users_device_id']) && $this->input->post('users_device_id') != $userData['users_device_id'] || $userData['users_device_id'] != "" && $this->input->post('users_device_id') == '' ):
							echo outPut(0,lang('NOT_LOGIN_ACTION'),lang('POS_NOT_MATCHED'),$result);die();
						endif;

						$param['login_token']			= 	$this->geneal_model->generatetoken();

						if($this->input->post('device_id') || $this->input->post('pos_device_id')):

							$users_device_id  				=	$this->input->post('device_id');
							$users_lat  					=	$this->input->post('users_lat');
							$users_long  					=	$this->input->post('users_long');
							$param["device_type"] 			=	$this->input->post('device_type');
						    $param["app_version"] 			=	$this->input->post('app_version');
							if(empty($userData['pos_device_id']) && $this->input->post('pos_device_id') ):
								$param['pos_device_id']			= 	$this->input->post('pos_device_id');
						    endif;
						    if(empty($userData['device_id']) && $this->input->post('device_id') ):
								$param['device_id']			= 	$users_device_id;
						    endif;
							$param['device_id']				= 	$users_device_id;
							$param['latitude']				= 	$users_lat;
							$param['longitude']				= 	$users_long;
							$param['token']					= 	base64_encode($userData['users_id']);
							$this->geneal_model->editData('uw_users',$param,'users_id',(int)$userData['users_id']);
						endif;

						$result['userData'] 				=	$userData;
						echo outPut(1,lang('SUCCESS_CODE'),lang('LOGIN_SUCCESS'),$result);
					elseif($userData['status'] == 'I' && $userData['is_verify'] == 'N'):
						$otp  					= (int)rand(100000,999999);
						$mobile_no = $userData['country_code'].$userData['users_mobile'];
						$param['users_otp']				= 	$otp;
						$this->geneal_model->editData('uw_users',$param,'users_id',(int)$userData['users_id']);
						$this->sms_model->sendForgotPasswordOtpSmsToUser($mobile_no,$otp, $userData['country_code']);
						if($userData['users_email']){
							$this->emailsendgrid_model->sendRegistrationMailToUser($insert_data);
						}
						$result['userData'] 				=	$userData;
						echo outPut(1,lang('SUCCESS_CODE'),lang('ACCOUNT_NOT_VERIFY'),$result);
					elseif($userData['status'] == 'I' && $userData['is_verify'] == 'Y'):
						echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_INACIVE'),$result);
					else:
						echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$result);
					endif;
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('MOBILE_OR_PASS_INCORRECT'),$result);
				endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : forgotPassword
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for forgot Password
	 * * Date : 07 February 2024
	 * * Updated By : Dilip Halder
	 * * Updated Date : 23-12-2022
	 * * **********************************************************************/
	public function forgotPassword()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):
			
			if($this->input->post('users_email') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMAIL_EMPTY'),$result);
			else:
				if(is_numeric($this->input->post('users_email'))):
					$forget_by 	=	'MOBILE';
					if(strlen($this->input->post('users_email')) >= 10){
						$where 			=	[ 'users_mobile' => (int)$this->input->post('users_email') ];
					}else{
						$where 			=	[ 'users_mobile' => (float)$this->input->post('users_email') ];
					}
				else:
					$where 			=	[ 'users_email' => $this->input->post('users_email') ];
					$forget_by 		=	'EMAIL';
				endif;
				
				$tblName 		=	'uw_users';
				$userDetails 	=	$this->geneal_model->getOnlyOneData($tblName, $where);
				
				if(!empty($userDetails)):
					if($userDetails['status'] == 'A'):

						$param['users_otp']		= (int)rand(1000,9999); 
						$this->geneal_model->editData('uw_users',$param,'users_id',(int)$userDetails['users_id']);

						$finalres = $this->geneal_model->getDataByParticularField('uw_users','users_id',(int)$userDetails['users_id']);

						if($userDetails['users_email']):
							$this->emailsendgrid_model->accountVerifyOTP($userDetails['users_email'],$param['users_otp'] );
						endif;
						
						$country_code = $this->input->post('country_code');

						if(empty($country_code)):
							$country_code = $userDetails['country_code'];
						endif;
						
						$mobile_no 	= $userDetails['users_mobile'];
						$this->sms_model->accountVerifyOTP($country_code,$mobile_no,$param['users_otp']);
						$result['userData'] 				=	$finalres;
						echo outPut(1,lang('SUCCESS_CODE'),lang('OTP_SENT').$this->input->post('users_email'),$result);
					else:
						echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$result);
					endif;
				else:
					if($forget_by == 'MOBILE'):
						echo outPut(0,lang('SUCCESS_CODE'),lang('RECHARGE_PHONE_ERROR'),$result);
					else:
						echo outPut(0,lang('SUCCESS_CODE'),lang('RECHARGE_EMAIL_ERROR'),$result);
					endif;
				endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

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
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):
			
			if($this->input->post('users_email') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMAIL_EMPTY'),$result);
			elseif($this->input->post('users_otp') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('OTP_EMPTY'),$result);
			elseif($this->input->post('new_password') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('PASSWORD_EMPTY'),$result);
			else:
				if(is_numeric($this->input->post('users_email'))):
					if(strlen($this->input->post('users_email')) >= 10):
						$where 			=	[ 'users_mobile' => (int)$this->input->post('users_email') ];
					else:
						$where 			=	[ 'users_mobile' => (float)$this->input->post('users_email') ];
					endif;
				else:
					$where 			=	[ 'users_email' => $this->input->post('users_email') ];
				endif;
				$tblName 		=	'uw_users';
				$userDetails 	=	$this->geneal_model->getOnlyOneData($tblName, $where);
				$userDetails['password'] = "";
				//print_r($userDetails);die();
				if(!empty($userDetails) && isset($userDetails['users_otp']) && $this->input->post('users_otp') == $userDetails['users_otp']):
					if($userDetails['status'] == 'A'):
						$param['users_otp']		= 	'';	
						$param['password']		=	md5($this->input->post('new_password'));
						$param['login_token']	= 	$this->geneal_model->generatetoken();
						$this->geneal_model->editData('uw_users',$param,'users_id',(int)$userDetails['users_id']);

						$w['where'] = array('users_id'=>(int)$userDetails['users_id']);
						$finalres = $this->geneal_model->getData2('single','uw_users',$w);
						$finalres['password'] = "";
						$result['userData'] 	=	$finalres;
						if($finalres['users_email']):
							$this->emailsendgrid_model->sendSuccessResetPasswordMailToUser($finalres);
						endif;
						$mobile_no = $finalres['country_code'].$finalres['users_mobile'];
						$this->sms_model->sendSuccessResetPasswordSmsToUser($mobile_no);
						echo outPut(1,lang('SUCCESS_CODE'),lang('PASS_CHANGE_SUCCESS'),$result);
					else:
						echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$result);
					endif;
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_email_otp'),$result);
				endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
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

				$where 			=	[ 'users_id' => (int)$this->input->get('users_id') ];
				$tblName 		=	'uw_users';
				$userDetails 	=	$this->geneal_model->getOnlyOneData($tblName, $where);
				$count = 0;
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
	public function updateProfile()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):
			
			if($this->input->get('users_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			elseif($this->input->post('users_name') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('NAME_EMPTY'),$result);
			// elseif($this->input->post('last_name') == ''): 
			// 	echo outPut(0,lang('SUCCESS_CODE'),lang('LASTNAME_EMPTY'),$result);
			elseif($this->input->post('users_email') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMAIL_EMPTY'),$result);
			elseif($this->input->post('country_code') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('COUNTRY_CODE_EMPTY'),$result);
			elseif($this->input->post('users_mobile') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('PHONE_EMPTY'),$result);
			else:

				$where 			=	[ 'users_id' => (int)$this->input->get('users_id') ];
				$tblName 		=	'uw_users';
				$userDetails 	=	$this->geneal_model->getOnlyOneData($tblName, $where);
				

				if($this->input->post('users_email') == $userDetails['users_email']):
					$duplicate_email = 'N';
				else:
					$where2 = ['users_email' => $this->input->post('users_email') ,'users_mobile' => $this->input->post('users_mobile') ];
			   	 	$query = $this->geneal_model->checkDuplicate('uw_users',$where2);
					if($query > 0 ){
						$duplicate_email = 'Y';
					}else{
						$duplicate_email = 'N';
					}
				endif;

				if($duplicate_email == 'N'):
					if(!empty($userDetails)):
						if($userDetails['status'] == 'A'):

							$update_data = array(
												'users_name'	=>	$this->input->post('users_name'),
												"last_name"     =>  $this->input->post('last_name'),
												"country_code"  =>  $this->input->post('country_code'),
												//'users_mobile'	=>	(int)$this->input->post('users_mobile'),
												'users_email'	=>	$this->input->post('users_email'),
												'sms_notification'	=>	$this->input->post('sms_notification'),
												'email_notification'	=>	$this->input->post('email_notification'),
												'notification'	=>	$this->input->post('notification'),
												'updated_at'	=>	date('Y-m-d H:i'),
												'updated_ip'	=>	currentIp(),
												);

							$this->geneal_model->editData('uw_users',$update_data,'users_id',(int)$this->input->get('users_id'));

							$finalres = $this->geneal_model->getDataByParticularField('uw_users','users_id',(int)$this->input->get('users_id'));
							$result['userData'] 		=	$finalres;
							echo outPut(1,lang('SUCCESS_CODE'),lang('PROFILE_UPDATED'),$result);
						else:
							echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$result);
						endif;
					else:
						echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_INCORRECT'),$result);
					endif;
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('EMAIL_ALREADY_EXIST'),$result);
				endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
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
				$where 			=	[ 'users_id' => (int)$this->input->get('users_id') ];
				$tblName 		=	'uw_users';
				$userDetails 	=	$this->geneal_model->getOnlyOneData($tblName, $where);

				if(!empty($userDetails)):
					if($userDetails['is_verify'] == 'N'):
						$otp = (int)$this->input->post('otp');
						if($otp == $userDetails['users_otp']):
						  $param['token']        =   base64_encode($userDetails['users_id']);
						  $param['status']       =   'A';
						  $param['is_verify']    =   'Y'; 
						  $param['users_otp']    =   '';   
						  $param['updated_at']   =   date('Y-m-d H:i');
						  $param['updated_ip']   =   $this->input->ip_address();
						  $this->common_model->editData('uw_users',$param,'users_id',(int)$userDetails['users_id']);

							//Sign up loadBalance entry...
							$loadbaWhere 	=	array( 'users_id' => (int)$this->input->get('users_id') );
							$loadbalaceData	=	$this->geneal_model->getOnlyOneData('uw_loadBalance', $loadbaWhere);
							if(empty($loadbalaceData)):
								$user_OId 	 = $userDetails['_id']->{'$id'};
								$signupBonus = SIGNUPBONUS;
				                /* Load Balance Table -- after Sign Up*/
				                $Cashbparam["load_balance_id"]      =   (int)$this->geneal_model->getNextSequence('uw_loadBalance');
				                $Cashbparam["user_oid"]        		=   new MongoDB\BSON\ObjectId($user_OId);
				                $Cashbparam["user_id_cred"]         =   (int)$userDetails['users_id'];
				                $Cashbparam["upoints"]       		=   (float)$signupBonus;
				                $Cashbparam["record_type"]          =   'Credit';
				                $Cashbparam["narration"]  			=   'Signup Bonus';
				                $Cashbparam["remarks"]  			=   '';
				                $Cashbparam["availableUPoints"] 	=   (float)'0';
				                $Cashbparam["end_balance"] 			=   (float)signupBonus;
				                $Cashbparam["creation_ip"]          =   currentIp();
				                $Cashbparam["created_at"]           =   date('Y-m-d H:i');
				                $Cashbparam["created_by"]           =   (int)$userDetails['users_id'];
				                $Cashbparam["status"]               =   "A";
				                $this->geneal_model->addData('uw_loadBalance', $Cashbparam);
							endif;
							//End

							$finalres = $this->geneal_model->getDataByParticularField('uw_users','users_id',(int)$this->input->get('users_id'));
							$finalres['password']		=	"";
							$result['userData'] 		=	$finalres;

							//Sent Notification
							if($finalres['device_id']):
								$this->notification_model->sentSignupBonusNotification($result);
							endif;
							if($userDetails['users_email']):
								$this->emailsendgrid_model->sendSuccessRegistrationMailToUser($userDetails);
							endif;
							$mobile_no = $userDetails['country_code'].$userDetails['users_mobile'];
							$this->sms_model->sendSuccessRgistrationSmsToUser($mobile_no,$userDetails['users_name'],$userDetails['country_code']);
							
							echo outPut(1,lang('SUCCESS_CODE'),lang('ACCOUNT_VERIFY'),$result);
						else:
							echo outPut(0,lang('SUCCESS_CODE'),lang('WRONG_OTP'),$result);
						endif;
					else:
						echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_ALREADY_VERIFIED'),$result);
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
	 * * Function name 	: rsendotp
	 * * Developed By 	: Dilip Halder
	 * * Purpose  		: This function used for rsend otp
	 * * Date 			: 07 February 2024
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
				$where 			=	[ 'users_id' => (int)$this->input->post('users_id') ];
				$tblName 		=	'uw_users';
				$userDetails 	=	$this->geneal_model->getOnlyOneData($tblName, $where);
				
				if(!empty($userDetails)):
					$result['userData']	=	$userDetails;
					if($userDetails['is_verify'] == 'N'):
						$otp  		= (int)rand(100000,999999);		
						$param['users_otp']     =   $otp;   
						$param['updated_at']    =   date('Y-m-d H:i');
						$param['updated_ip']    =   $this->input->ip_address();
						$this->common_model->editData('uw_users',$param,'users_id',(int)$userDetails['users_id']);
						if($userDetails['users_email']):
							$this->emailsendgrid_model->sendRegistrationMailToUser($userDetails);
						endif;
						$mobile_no = $userDetails['country_code'].$userDetails['users_mobile'];
						$this->sms_model->sendForgotPasswordOtpSmsToUser($mobile_no,$otp);
						echo outPut(1,lang('SUCCESS_CODE'),lang('OTP_SENT2'),$result);
					else:
						echo outPut(1,lang('SUCCESS_CODE'),lang('ACCOUNT_ALREADY_VERIFIED'),$result);
					endif;
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_INCORRECT'),$result);
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

	
}
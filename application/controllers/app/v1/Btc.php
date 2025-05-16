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
	public function login()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 			= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):
			
			$country_code 	= $this->input->post('country_code');
			$users_mobile 	= $this->input->post('users_mobile');
			$users_email 	= $this->input->post('users_email');
			$password       = md5($this->input->post('users_password'));
			
			$users_long 	= $this->input->post('users_long');
			$users_lat 		= $this->input->post('users_lat');
			$device_id 		= $this->input->post('device_id');
			$users_address 	= $this->input->post('users_address');

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

				if($users_long)    : $param['longitude']  = $users_long;    endif;
				if($users_lat)     : $param['latitude']   = $users_lat;     endif;
				if($device_id)     : $param['device_id']  = $device_id;     endif;
				if($users_address) : $param['address']    = $users_address; endif;

				// if(!empty($userDetails) && isset($userDetails['password']) &&  $password == $userDetails['password'] ):
				if(!empty($userDetails) && isset($userDetails['password']) && md5($this->input->post('users_password')) == $userDetails['password'] && $userDetails['users_type'] == 'Users'):	
					
					if($userDetails['users_mobile'] == '556633485' || $userDetails['users_email'] == 'akashtest@gmail.com' ):
					 $otp  = (int)1234;
					else:
					 $otp  = (int)rand(1000,9999);
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
				elseif(!empty($userDetails)):
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
	

 
}
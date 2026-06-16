<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller {

	public function  __construct() 
	{ 
		parent:: __construct();
		error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_USER_DEPRECATED);  
		$this->load->model(array('admin_model','emailtemplate_model','emailsendgrid_model','sms_model','notification_model'));
		$this->lang->load('statictext', 'admin');
		$this->load->helper('common');
	} 
	
	/* * *********************************************************************
	 * * Function name 	: login
	 * * Developed By 	: Dilip Halder
	 * * Purpose  		: This function for login
	 * * Date 			: 06 February 2024
	 * * Updated By		: Dilip Halder
	 * * Updated Date	: Dilip Halder
	 * * **********************************************************************/
	public function index()
	{	
		if($this->session->userdata('UW_ADMIN_ID')) redirect($this->session->userdata('UW_ADMIN_CURRENT_PATH').'maindashboard');
		$data['error'] 						= 	'';
		/*-----------------------------------Login ---------------*/
		if($this->input->post('loginFormSubmit')):	
			//Set rules
			$this->form_validation->set_rules('userEmail', 'email', 'trim|required');
			$this->form_validation->set_rules('userPassword', 'password', 'trim|required');
			
			if($this->form_validation->run()):	
				$result		=	$this->admin_model->Authenticate($this->input->post('userEmail')); 
				// $param['admin_password_otp']		=	(int)'4321';//(int)generateRandomString(4,'n');
				// $this->common_model->editData('uw_admin',$param,'admin_id',(int)$result['admin_id']);
				// redirect(getCurrentBasePath().'login-verify-otp');

				//Generating Logs
				$this->common_model->generateLogs();

				if($result): 
					if($this->admin_model->decryptsPassword($result['admin_password']) != $this->input->post('userPassword')):
						$data['error'] = lang('invalidpassword');	
					elseif($result['status'] != 'A'):	
						$data['error'] = lang('accountblock');	
					else:	
						// $param['admin_password_otp']		=	(int)'4321';//(int)generateRandomString(4,'n');
						$param['admin_password_otp']		=	rand(1111,9999);

						$this->common_model->editData('uw_admin',$param,'admin_id',(int)$result['admin_id']);
					   // $this->sms_model->sendForgotPinOtpSmsToUser($result['admin_email'],$param['admin_password_otp']);
							
						$Country_code = $result['country_code']? $result['country_code'] : '+971';
						$this->sms_model->accountVerifyOTP($Country_code,$result['admin_phone'],$param['admin_password_otp']);
					    $this->emailsendgrid_model->sendOtpVarification( $result['admin_email'],(int)$param['admin_password_otp']);
						
						redirect(getCurrentBasePath().'login-verify-otp');
					endif;
				else:
					$data['error'] = lang('invalidlogindetails');
				endif;			
			endif;
		endif;
		
		$this->layouts->set_title('Login | UWINN');
		$this->layouts->admin_view('account/login',array(),$data,'login');
	}

	/* * *********************************************************************
	 * * Function name : loginverifyotp
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for admin password recover
	 * * Date : 06 February 2024
	 * * **********************************************************************/
	public function loginverifyotp()
	{	
		if($this->session->userdata('UW_ADMIN_ID')) redirect($this->session->userdata('UW_CURRENT_PATH').'maindashboard');
		$data['error'] 						= 	'';

		/*-----------------------------------recover password ---------------*/
		
		
		if($this->input->post('otpVerificationFormSubmit')):	
			//Set rules
			
			$this->form_validation->set_rules('userOtp', 'otp', 'trim|required|min_length[4]|max_length[4]');
			
			
			if($this->form_validation->run()):	
				$result		=	$this->admin_model->checkOTP((int)$this->input->post('userOtp'));
				$menuData   =   $this->admin_model->getMenuModuleNew();    
	
				if($result): 
				  
					$this->session->unset_userdata(array('otpType','otpAdminId','otpAdminMobile'));

					$param['last_login_ip']				=	currentIp();
					$param['last_login_date']			=	(int)$this->timezone->utc_time();//currentDateTime();
					$param['admin_password_otp']		=	'';
					$this->common_model->editData('uw_admin',$param,'admin_id',(int)$result['admin_id']);

					############	LOGOUT IN PRIVIOUS SYSTEM 	#######
					/*
					$logoutParam['admin_token']			=	'';
					$logoutParam['login_status']		=	'Logout';
					$logoutParam['logout_datetime']		=	(int)$this->timezone->utc_time();//currentDateTime();
					$logoutParam['logout_ip']			=	currentIp();

					$logoutuWhere['login_status']		=	'Login';
					$logoutuWhere['admin_id']			=	(int)$result['admin_id'];
					$this->common_model->editDataByMultipleCondition('uw_admin_login_log',$logoutParam,$logoutuWhere);	
					*/
					############	LOGIN IN NEW SYSTEM 	############
					$loginParam['login_log_id']			=	(int)$this->common_model->getNextSequence('uw_admin_login_log');
					$loginParam['admin_id']				=	(int)$result['admin_id'];
					$loginParam['admin_token']			=	generateToken();
					$loginParam['login_status']			=	'Login';
					$loginParam['login_datetime']		=	(int)$this->timezone->utc_time();//currentDateTime();
					$loginParam['login_ip']				=	currentIp();
					$logininsertId						=	$this->common_model->addData('uw_admin_login_log',$loginParam);

					$currentPath 		=	getCurrentBasePath();
					$this->session->set_userdata(array(
										'UW_ADMIN_LOGGED_IN'		=>	true,
										'UW_ADMIN_ID'			=>	$result['admin_id'],
										'UW_ADMIN_TITLE'			=>	$result['admin_title'],
										'UW_ADMIN_FIRST_NAME'	=>	$result['admin_first_name'],
										'UW_ADMIN_MIDDLE_NAME'	=>	$result['admin_middle_name'],
										'UW_ADMIN_LAST_NAME'		=>	$result['admin_last_name'],
										'UW_ADMIN_EMAIL'			=>	$result['admin_email'],
										'UW_ADMIN_MOBILE'		=>	$result['admin_phone'],
										'UW_ADMIN_IMAGE'			=>	$result['admin_image'],
										'UW_ADMIN_ADDRESS'		=>	$result['admin_address'],
										'UW_ADMIN_CITY'			=>	$result['admin_city'],
										'UW_ADMIN_STATE'			=>	$result['admin_state'],
										'UW_ADMIN_COUNTRY'		=>	$result['admin_country'],
										'UW_ADMIN_ZIPCODE'		=>	$result['admin_pincode'],
										'UW_ADMIN_TYPE'			=>	$result['admin_type'],
										'UW_ADMIN_CURRENT_PATH'	=>	$currentPath,
										'UW_ADMIN_USER_TYPE'		=>	'',
										'UW_ADMIN_LAST_LOGIN'	=>	$result['last_login_date'].' ('.$result['last_login_ip'].')'));

					setcookie('UW_ADMIN_LOGIN_TOKEN',$loginParam['admin_token'],time()+60*60*24*100,'/');

					//Generating Logs
					$this->common_model->generateLogs();

					if($_COOKIE['UW_ADMIN_REFERENCE_PAGES']):
						redirect(base_url().$_COOKIE['UW_ADMIN_REFERENCE_PAGES']);
					else:
						redirect($currentPath.'maindashboard');
					endif;
				else:
					$data['recovererror'] = lang('invalidotp');
				endif;
			endif;
		endif;
		
		$this->layouts->set_title('OTP Verification | UWINN');
		$this->layouts->admin_view('account/loginverifyotp',array(),$data,'login');
	}	// END OF FUNCTION

	/* * *********************************************************************
	 * * Function name : resendotp
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for resend otp
	 * * Date : 06 February 2024
	 * * **********************************************************************/
	public function resendotp()
	{	
		if(sessionData('otpType') && sessionData('otpAdminId') && sessionData('otpAdminMobile')):
			$param['admin_password_otp']	=	(int)'4321';//(int)generateRandomString(4,'n');
			$this->common_model->editData('uw_admin',$param,'admin_id',(int)sessionData('otpAdminId'));

			if(sessionData('otpType') == 'Login'):
				$this->sms_model->sendLoginOtpSmsToUser(sessionData('otpAdminMobile'),$param['admin_password_otp']);
			elseif(sessionData('otpType') == 'Forgot Password'):
				$this->sms_model->sendForgotPasswordOtpSmsToUser(sessionData('otpAdminMobile'),$param['admin_password_otp']);
			elseif(sessionData('otpType') == 'Change Password'):
				$this->sms_model->sendChangePasswordOtpSmsToUser(sessionData('otpAdminMobile'),$param['admin_password_otp']);
			endif;

			$this->session->set_flashdata('alert_success',lang('sendotptomobile').sessionData('otpAdminMobile'));
		endif;
		redirect($_SERVER['HTTP_REFERER']);
	}	// END OF FUNCTION
	
	
	/* * *********************************************************************
	 * * Function name : resendpin
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for resend otp
	 * * Date : 06 February 2024
	 * * **********************************************************************/
	public function resendpin()
	{
	    //echo"<pre>"; print_r($this->session->userdata());die;
		if(sessionData('otpType') && sessionData('otpAdminId') ):
			$param['admin_password_otp']	=	(int)generateRandomString(4,'n');//(int)'1234';
			$this->common_model->editData('uw_admin',$param,'admin_id',(int)sessionData('otpAdminId'));
        //  $this->sms_model->sendForgotPinOtpSmsToUser(sessionData('otpAdminEmail'),$param['admin_password_otp']);
      
         	$finalres = $this->common_model->getDataByParticularField('uw_admin','admin_email',sessionData('otpAdminEmail'));
         
         	
         //	print_r($finalres);die;
	           $this->emailtemplate_model->sendForgotpinMailToUser($finalres);

			$this->session->set_flashdata('alert_success',lang('sendotptoemail').sessionData('otpAdminEmail'));
		endif;
		redirect($_SERVER['HTTP_REFERER']);
	}	// END OF FUNCTION
	
	/* * *********************************************************************
	 * * Function name : forgotpassword
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for admin forgot password
	 * * Date : 06 February 2024
	 * * **********************************************************************/
	public function forgotpassword()
	{	
		if($this->session->userdata('UW_ADMIN_ID')) redirect($this->session->userdata('UW_ADMIN_CURRENT_PATH').'maindashboard');
		$data['error'] 						= 	'';

		/*-----------------------------------Forgot password ---------------*/
		if($this->input->post('recoverformSubmit')):	
			//Set rules
			$this->form_validation->set_rules('forgotMobile', 'Mobile', 'trim|required|min_length[9]|max_length[9]');
			
			if($this->form_validation->run()):	
				$result		=	$this->common_model->getDataByParticularField('uw_admin','admin_phone',(int)$this->input->post('forgotMobile'));
				if($result):
					if($result['status'] != 'A'):	
						$data['forgoterror'] = lang('accountblock');	
					else:

						if(empty($result['admin_country_code'])):
							$result['admin_country_code'] = '+971';
						endif;

						// $param['admin_password_otp']		=	(int)'4321';//(int)generateRandomString(4,'n');
						$param['admin_password_otp']		=	rand(1111,9999);
						$this->common_model->editData('uw_admin',$param,'admin_id',(int)$result['admin_id']);
						$this->sms_model->accountVerifyOTP($result['admin_country_code'],$result['admin_phone'],$param['admin_password_otp']);

						$this->emailsendgrid_model->sendOtpVarification( $result['admin_email'],(int)$param['admin_password_otp']);
						
						$this->session->set_userdata(array('otpType'=>'Forgot Password','otpAdminId'=>$result['admin_id'],'otpAdminMobile'=>$result['admin_phone']));

						$this->session->set_flashdata('alert_success',lang('sendotptomobile').$result['admin_country_code'].$result['admin_phone']);
						redirect(getCurrentBasePath().'password-recover');
					endif;
				else:
					$data['forgoterror'] = lang('invalidmobile');
				endif;
			endif;
		endif;
		
		$this->layouts->set_title('Forgot Password | UWINN');
		$this->layouts->admin_view('account/forgotpassword',array(),$data,'login');
	}	// END OF FUNCTION

	/* * *********************************************************************
	 * * Function name : forgotpin
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for admin forgot password
	 * * Date : 06 February 2024
	 * * **********************************************************************/
	public function forgotpin()
	{	
		if($this->session->userdata('UW_ADMIN_ID')) redirect($this->session->userdata('UW_ADMIN_CURRENT_PATH').'maindashboard');
		$data['error'] 						= 	'';

		/*-----------------------------------Forgot pin ---------------*/
		if($this->input->post('recoverformSubmit')):	
			//Set rules
			$this->form_validation->set_rules('forgotEmail', 'Email', 'trim|required');
			
			if($this->form_validation->run()):	
				$result		=	$this->common_model->getDataByParticularField('uw_admin','admin_email',$this->input->post('forgotEmail'));
		  	    $this->session->set_userdata('recoveryemail',$this->input->post('forgotEmail'));
			
				if($result):
					if($result['status'] != 'A'):	
						$data['forgoterror'] = lang('accountblock');	
					else:
						$param['admin_password_otp']		= 	(int)generateRandomString(4,'n'); //(int)'4321';
						$this->common_model->editData('uw_admin',$param,'admin_id',(int)$result['admin_id']);
						$finalres = $this->common_model->getDataByParticularField('uw_admin','admin_email',$this->input->post('forgotEmail'));
                         // echo($this->session->userdata('recoveryemail'));die; 
                        // print_r($finalres);die;
						$this->emailtemplate_model->sendForgotpinMailToUser($finalres);
						
			//	print_r($result);die;

					$this->session->set_userdata(array('otpType'=>'Forgot Pin','otpAdminId'=>$result['admin_id'],'otpAdminEmail'=>$result['admin_email']));

						$this->session->set_flashdata('alert_success',lang('sendotptoemail').$result['admin_email']);
						redirect(getCurrentBasePath().'pin-recover');
					endif;
				else:
					$data['forgoterror'] = lang('invalidemail');
				endif;
			endif;
		endif;
		
		$this->layouts->set_title('Forgot Pin | UWINN');
		$this->layouts->admin_view('account/forgotpin',array(),$data,'login');
	}	// END OF FUNCTION

    /* * *********************************************************************
	 * * Function name : passwordrecover
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for admin password recover
	 * * Date : 06 February 2024
	 * * **********************************************************************/
	
    public function passwordrecover()
	{	
		if($this->session->userdata('UW_ADMIN_ID')) redirect($this->session->userdata('UW_ADMIN_CURRENT_PATH').'maindashboard');
		$data['error'] 						= 	'';

		/*-----------------------------------recover password ---------------*/
		if($this->input->post('passwordRecoverFormSubmit')):	
			//Set rules
			$this->form_validation->set_rules('userOtp', 'otp', 'trim|required|min_length[4]|max_length[4]');
			$this->form_validation->set_rules('userPassword', 'New password', 'trim|required|min_length[6]|max_length[25]');
			$this->form_validation->set_rules('userConfPassword', 'Confirm password', 'trim|required|min_length[6]|matches[userPassword]');
			
			if($this->form_validation->run()):	
				$result		=	$this->admin_model->checkOTP((int)$this->input->post('userOtp'));
				if($result):
					$this->session->unset_userdata(array('otpType','otpAdminId','otpAdminMobile'));

					$param['admin_password']		=	$this->admin_model->encriptPassword($this->input->post('userPassword'));
					$param['admin_password_otp']	=	'';
					$this->common_model->editData('uw_admin',$param,'admin_id',(int)$result['admin_id']);
							
					$this->session->set_flashdata('alert_success',lang('passrecoversuccess'));
					redirect(getCurrentBasePath().'login');
				else:
					$data['recovererror'] = lang('invalidotp');
				endif;
			endif;
		endif;
		
		$this->layouts->set_title('Password Recover | UWINN');
		$this->layouts->admin_view('account/passwordrecover',array(),$data,'login');
	}	// END OF FUNCTION

	/* * *********************************************************************
	 * * Function name : pinrecover
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for admin password recover
	 * * Date : 06 February 2024
	 * * **********************************************************************/
	public function pinrecover()
	{	
	    if($this->session->userdata('UW_ADMIN_ID')) redirect($this->session->userdata('UW_ADMIN_CURRENT_PATH').'maindashboard');
		$data['error'] 						= 	'';

		/*-----------------------------------recover pin ---------------*/
		if($this->input->post('pinRecoverFormSubmit')):	
			//Set rules
			 
			$this->form_validation->set_rules('userOtp', 'otp', 'trim|required|min_length[4]|max_length[4]');
			$this->form_validation->set_rules('userPin', 'New pin', 'trim|required|min_length[4]|max_length[25]');
			$this->form_validation->set_rules('userConfPin', 'Confirm pin', 'trim|required|min_length[4]|matches[userPin]');
			
			if($this->form_validation->run()):	
			$result		=	$this->common_model->getDataByParticularField('uw_admin','admin_email',$this->session->userdata('recoveryemail'));
	
	        if($result):
	           //print($result);die;
		            $checkOTP	            =	$this->admin_model->checkOTP((int)$this->input->post('userOtp'));
		        
                
                	if($checkOTP):
                	$param['admin_pin']		=	(int)$this->input->post('userPin');
					$param['admin_password_otp']	=	'';
					$this->common_model->editData('uw_admin',$param,'admin_id',(int)$result['admin_id']);
					$this->session->set_flashdata('alert_success',lang('pinrecoversuccess'));
					redirect(getCurrentBasePath().'login');
				else:
					$data['recovererror'] = lang('invalidotp');
				endif;
			endif;
		endif;
		endif;
		$this->layouts->set_title('Pin Recover | UWINN');
		$this->layouts->admin_view('account/pinrecover',array(),$data,'login');
	}	// END OF FUNCTION
	
		/***********************************************************************
	** Function name : logout
	** Developed By : Dilip Halder
	** Purpose  : This function used for logout
	** Date : 06 February 2024
	************************************************************************/
	function logout()
	{	
		$logoutParam['admin_token']			=	'';
		$logoutParam['login_status']		=	'Logout';
		$logoutParam['logout_datetime']		=	(int)$this->timezone->utc_time();//currentDateTime();
		$logoutParam['logout_ip']			=	currentIp();

		$logoutuWhere['login_status']		=	'Login';
		$logoutuWhere['admin_token']		=	isset($_COOKIE['UW_ADMIN_LOGIN_TOKEN']) ? $_COOKIE['UW_ADMIN_LOGIN_TOKEN'] : '';
		$logoutuWhere['admin_id']			=	(int)$this->session->userdata('UW_ADMIN_ID');
		$this->common_model->editDataByMultipleCondition('uw_admin_login_log',$logoutParam,$logoutuWhere);

		setcookie('UW_ADMIN_LOGIN_TOKEN','',time()-60*60*24*100,'/');
		setcookie('UW_ADMIN_REFERENCE_PAGES','',time()-60*60*24*100,'/');
		
		$this->session->unset_userdata(array('otpType','otpAdminId','otpAdminMobile','changeNewPassword'));
		$this->session->unset_userdata(array('UW_ADMIN_LOGGED_IN',
											 'UW_ADMIN_ID',
											 'UW_ADMIN_TITLE',
											 'UW_ADMIN_FIRST_NAME',
											 'UW_ADMIN_MIDDLE_NAME',
											 'UW_ADMIN_LAST_NAME',
											 'UW_ADMIN_EMAIL',
											 'UW_ADMIN_MOBILE',
											 'UW_ADMIN_IMAGE',
											 'UW_ADMIN_ADDRESS',
											 'UW_ADMIN_CITY',
											 'UW_ADMIN_STATE',
											 'UW_ADMIN_COUNTRY',
											 'UW_ADMIN_ZIPCODE',
											 'UW_ADMIN_TYPE',
											 'UW_ADMIN_CURRENT_PATH',
											 'UW_ADMIN_USER_TYPE',
											 'UW_ADMIN_LAST_LOGIN'));
		redirect(getCurrentBasePath().'login');
	}	// END OF FUNCTION
}
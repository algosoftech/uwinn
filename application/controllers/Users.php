<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {
	public function  __construct() 
	{ 
		parent:: __construct();
		error_reporting(0);
		$this->load->model(array('geneal_model','common_model','emailsendgrid_model','sms_model'));
		$this->load->helper(array('common_helper'));
		$this->lang->load('statictext','front');
	}
	 
	/***********************************************************************
	** Function name 	: myprofile
	** Developed By 	: Dilip Halder
	** Purpose 			: This function used to get profile details
	** Date 			: 04 June 2024
	************************************************************************/ 	
	public function myProfile()
	{  	
		$data 					= array();
		$login_token = $this->session->userdata('login_token');
		$users_id    = $this->session->userdata('users_id');

		$whereCon['where']['users_id']    = (int)$users_id;
		$whereCon['where']['login_token'] = $login_token;
		$FieldList   		  = array('users_id','users_name','last_name','users_type','users_email','country_code','users_mobile','availableArabianPoints','is_varified','status','profile'); 
		$tableName            = 'uw_users';
		$data['userDetails']  = $this->common_model->getParticularFieldByMultipleCondition($FieldList,$tableName,$whereCon);
	 	$data['country_code'] = countryCodeList();

	 	if($this->input->post('SaveChanges')):
	 		
			$error					=	'NO';
			$this->form_validation->set_error_delimiters('', '');
   	 		$this->form_validation->set_message('is_unique', 'The %s is already taken');
			$this->form_validation->set_rules('first_name', 'First Name', 'trim|required');
			$this->form_validation->set_rules('last_name', 'Last Name', 'trim|required');
			// $this->form_validation->set_rules('country_code', 'Country Code', 'trim|required');
			// $this->form_validation->set_rules('mobile', 'Mobile', 'trim|required|is_unique[uw_users.users_mobile]');

			if($data['userDetails']['users_email'] != $this->input->post('email')):
			  $this->form_validation->set_rules('email', 'Email', 'trim|required|is_unique[uw_users.users_email]');
			endif;

			if($this->form_validation->run() && $error == 'NO'): 
				
				$profile 	   = $_FILES['profile'];
				$profile1 	   = $_FILES['profile1'];

				$users_name    = $this->input->post('first_name');
				$last_name     = $this->input->post('last_name');
				$users_email   = $this->input->post('email');
				// $users_mobile  = $this->input->post('mobile');
				// $country_code  = $this->input->post('country_code');

				if($profile['name']):
					$ufileName    = $profile['name'];
					$utmpName	  = $profile['tmp_name'];
					$ufileExt     = pathinfo($ufileName);
					$unewFileName = $this->common_model->random_strings(8).'.'.$ufileExt['extension'];
					$this->load->library("upload_crop_img");
					$uimageLink	  =	$this->upload_crop_img->_upload_image($ufileName, $utmpName, 'profileImage', $unewFileName, '');
					if($uimageLink != 'UPLODEERROR'):
						$imageName = $data['userDetails']['profile'];
						if($imageName):
							$this->load->library("upload_crop_img");
							$this->upload_crop_img->_delete_image(trim($imageName)); 	
						endif;
						$param['profile']		= 	$uimageLink;
					endif;
				endif;

				if($profile1['name']):
					$ufileName    = $profile1['name'];
					$utmpName	  = $profile1['tmp_name'];
					$ufileExt     = pathinfo($ufileName);
					$unewFileName = $this->common_model->random_strings(8).'.'.$ufileExt['extension'];
					$this->load->library("upload_crop_img");
					$uimageLink	  =	$this->upload_crop_img->_upload_image($ufileName, $utmpName, 'profileImage', $unewFileName, '');
					if($uimageLink != 'UPLODEERROR'):
						$imageName = $data['userDetails']['profile'];
						if($imageName):
							$this->load->library("upload_crop_img");
							$this->upload_crop_img->_delete_image(trim($imageName)); 	
						endif;
						$param['profile']		= 	$uimageLink;
					endif;
				endif;

				if($users_name)   :  $param['users_name']    =  $users_name;     	endif;
				if($last_name)    :  $param['last_name']     =  $last_name;         endif;
				if($users_email)  :  $param['users_email']   =  $users_email;       endif;
				// if($country_code) :  $param['country_code']  =  $country_code;      endif;
				// if($users_mobile) :  $param['users_mobile']  =  (int)$users_mobile; endif;
				$param['update_date'] = date('Y-m-d H:i:s');

				$this->geneal_model->editData('uw_users',$param,'users_id',(int)$users_id);
				$this->session->set_flashdata('alert_success',lang('UPDATED'));
				redirect('my-profile');
			endif;
		endif;

		$this->layouts->set_title('My Profile');
		$data['page']			= 'My Profile';
		$device_type = checkview();
		if($device_type == 'mobile'):
			$this->layouts->front_view('users/my-profile',array(),$data,'mobileview');
		else:
			$this->layouts->front_view('users/my-profile',array(),$data,'userview');
		endif;
	} 

	/***********************************************************************
	** Function name 	: checkout
	** Developed By 	: Dilip Halder
	** Purpose 			: This function used for checkout
	** Date 			: 24 June 2024
	************************************************************************/ 	
	public function myTicket($pageno='')
	{  	
		$data 					= array();
		
		
		$usersId      = $this->session->userdata('users_id');
		$itemsPerPage = $this->input->post('itemsPerPage')?$this->input->post('itemsPerPage'):5;
		$pageno 	  = $pageno?$pageno: 1;

		if($this->input->post()):
			$draw_date = $this->input->post('draw_date');
			if(!empty($draw_date)):
				$data['draw_date'] = $draw_date;
				$from = date('Y-m-d 00:01',strtotime($draw_date));
				$to   = date('Y-m-d 23:59',strtotime($draw_date));
				$PostFields['from'] = $from;
				$PostFields['to']   = $to;
			endif;


			$CampaignName  = $this->input->post('CampaignName');
			if(!empty($CampaignName) && $CampaignName != 'All' ):
				$PostFields['search_by']     = 'product_title';
				$PostFields['search_value']  = $CampaignName;

				$data['product'] = $CampaignName;
			endif;
		endif;

		$PostFields['user_id']       = $usersId;
		$PostFields['itemsPerPage']  = $itemsPerPage;
		$PostFields['page']  		 = $pageno;

		// $PostFields['search_by']     = '';
		// $PostFields['search_value']  = '';
		
		$Url  = base_url('api/v1/app/order/order-history');
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $Url ,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => $PostFields,
		  CURLOPT_HTTPHEADER => array(
		    'Apikey: c9d58f135dab835ecf44e7c64b978599',
		    'Apidate: 2022-06-13',
		    'Cookie: MainLoad=web1|ZofST|ZofQ0; ci_session=tc8hlhsrf7dltpnk06ephdsr29tfju22; ci_session=2vaa0paklp5dfk29hfeh14qg6dpohc2l; ci_session=hv6b9b9akpvke01fuae3tpir4v8melmv'
		  ),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		$apiresponce = json_decode($response);

		$data['current_page'] = $apiresponce->result->current_page;
		$totalPages   		  = $apiresponce->result->total_page;
		$data['OrderDetails'] = $apiresponce->result->OrderDetails;
		
		$totalRows			  = $itemsPerPage*$totalPages;
		$baseUrl   			  = base_url('my-ticket');
	    $data['pagination']   =	Pagination($baseUrl,$pageno,$totalPages ,$itemsPerPage,$totalRows);

	 	//Our Campaigns
	 	$SelectFields  		  = array('title'=>1);
		$data['ourCampaigns'] = $this->geneal_model->getProductWithPrizeDetails($whereCon,$SelectFields);
		// echo "<pre>";print_r($data);die();



		$this->layouts->set_title('My Raffle ID');
		$data['page']			= 'My Raffle ID';
		$device_type = checkview();
		if($device_type == 'mobile'):
			$this->layouts->front_view('users/my-tickets',array(),$data,'mobileview');
		else:
			$this->layouts->front_view('users/my-tickets',array(),$data,'userview');
		endif;

	} 

	/***********************************************************************
	** Function name 	: my_ticket_view
	** Developed By 	: Dilip Halder
	** Purpose 			: This function used for veiw tickect details
	** Date 			: 13-09-2024
	************************************************************************/ 	
	public function my_ticket_view($Orderid='')
	{  	
		$data 					= array();
	 
		
		$usersId      = $this->session->userdata('users_id');
		$itemsPerPage = $this->input->post('itemsPerPage')?$this->input->post('itemsPerPage'):5;
		$pageno 	  = $pageno?$pageno: 1;

		if($this->input->post()):
			$draw_date = $this->input->post('draw_date');
			if(!empty($draw_date)):
				$data['draw_date'] = $draw_date;
				$from = date('Y-m-d 00:01',strtotime($draw_date));
				$to   = date('Y-m-d 23:59',strtotime($draw_date));
				$PostFields['from'] = $from;
				$PostFields['to']   = $to;
			endif;
		endif;

		$PostFields['user_id']       = $usersId;
		$PostFields['itemsPerPage']  = $itemsPerPage;
		$PostFields['page']  		 = $pageno;

		$PostFields['search_by']     = 'order_id';
		$PostFields['search_value']  = $Orderid;
		 
		
		$Url  = base_url('api/v1/app/order/order-history');
		$curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $Url ,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS => $PostFields,
		  CURLOPT_HTTPHEADER => array(
		    'Apikey: c9d58f135dab835ecf44e7c64b978599',
		    'Apidate: 2022-06-13',
		    'Cookie: MainLoad=web1|ZofST|ZofQ0; ci_session=tc8hlhsrf7dltpnk06ephdsr29tfju22; ci_session=2vaa0paklp5dfk29hfeh14qg6dpohc2l; ci_session=hv6b9b9akpvke01fuae3tpir4v8melmv'
		  ),
		));
		
		$response = curl_exec($curl);
		curl_close($curl);
		$apiresponce 		  = json_decode($response);
		$data['OrderDetails'] = $apiresponce->result->OrderDetails;


		$this->layouts->set_title('Raffle Details');
		$data['page']			= 'Raffle Details';
		$device_type = checkview();
		if($device_type == 'mobile'):
			$this->layouts->front_view('users/view-mytickets.php',array(),$data,'mobileview');
		else:
			$this->layouts->front_view('users/view-mytickets.php',array(),$data,'userview');
		endif;


	} 


	/***********************************************************************
	** Function name 	: wallets
	** Developed By 	: Dilip Halder
	** Purpose 			: This function used for wallets
	** Date 			: 24 June 2024
	************************************************************************/ 
	public function wallets($pageno='')
	{	

		$USERID = $this->session->userdata('users_id');

		$data 				= array();
		$data['page']		= 'Wallets';

        $winningDetails     = $this->common_model->winningBalance($USERID);
        
        $data['play_balance'] 	 	= $winningDetails['availableArabianPoints'];
        $data['winning_balance'] 	= $winningDetails['winningBalance']?$winningDetails['winningBalance']:0;

		// transaction code start..
        $searchData['pageno'] 		= $pageno;
        $searchData['itemsPerPage'] = 5;
        $result = $this->common_model->transactionHistory($USERID,$searchData);
		$data['transactionHistory'] = $result['transactionHistory'];
		$data['pagination']         = $result['pagination'];
		// echo "<pre>";print_r($data);die();
		// transaction code end..

		// cash voucher code start ..
		$searchData2['pageno'] 		 = $pageno;
        $searchData2['itemsPerPage'] = 5;
		$result2 				 	 = $this->common_model->vocherHistory($USERID,$searchData2);
        $data['cash_vouchers']		 = $result2['CashVouchers'];	
		$data['voucherPagination']   = $result2['pagination'];
		// cash voucher code end ..
		// echo "<pre>";print_r($result2);die();

		$this->layouts->set_title('Wallet');
		$data['page']			= 'Wallet';
		$device_type = checkview();
		if($device_type == 'mobile'):
			$this->layouts->front_view('users/wallets',array(),$data,'mobileview');
		else:
			$this->layouts->front_view('users/wallets',array(),$data,'userview');
		endif;

	}

	/***********************************************************************
	** Function name 	: walletSubmit
	** Developed By 	: Dilip Halder
	** Purpose 			: This function used for walletSubmit
	** Date 			: 24 June 2024
	************************************************************************/ 
	public function walletSubmit()
	{
		$USERID 	 = $this->session->userdata('users_id');
		$wallet_type = $this->input->post('wallet_type');
		$SaveChanges = $this->input->post('SaveChanges');

		$tableName	 = "uw_users";
	    $Fields 	 = array('_id','users_id' ,'totalArabianPoints','availableArabianPoints','winningBalance','status');
	    $userDetails = $this->common_model->getSingleDataByParticularField($Fields,$tableName,'users_id',(int)$USERID);

	    $winning_amount 			= $this->input->post('winning_amount')? $this->input->post('winning_amount') : $this->input->post('amount');
	    $availableWinningBalance	= $userDetails['winningBalance'];
	    $availableArabianPoints		= $userDetails['availableArabianPoints'];
	    $totalArabianPoints			= $userDetails['totalArabianPoints'];
	    $user_OId 	 				= $userDetails['_id']['$id'];
	    $plateform   				= 'web';

	    if($wallet_type == 'voucher'):
	    	$coupon   = $this->input->post('coupon_code');
			$responce = $this->common_model->redeemCouponVoucher($USERID,$coupon,$plateform);
		elseif($wallet_type == 'online-topup'):

	    	$amount   = $this->input->post('amount');
			$responce = $this->common_model->topUpAmount($USERID,$amount,$plateform);

		elseif($wallet_type  == "transfer-amount"):
			
			$winning_amount = $this->input->post('winning_amount');
			$winningDetails = $this->common_model->redeemwinningAMount($USERID,$winning_amount,$plateform);

		elseif( !empty($userDetails) && $userDetails['status'] == 'A'  && $availableWinningBalance >= $winning_amount && $winning_amount >= 100 ):

			if($wallet_type == 'Cash'):
				$this->common_model->generateWinnerVouvcher($USERID,$winning_amount,$plateform);
			elseif($wallet_type  == "Bank"  || $wallet_type  == "Cripto" ):
				$POSTDATA['amount']              = $winning_amount;
                $POSTDATA['type']                = $this->input->post('wallet_type');
                $POSTDATA['cripto_id']           = $this->input->post('cripto_id');
                $POSTDATA['account_holder_name'] = $this->input->post('account_holder_name');
                $POSTDATA['bank_name']           = $this->input->post('bank_name');
                $POSTDATA['account_no']          = $this->input->post('account_no');
                $POSTDATA['ifsc_code']           = $this->input->post('ifsc_code');
				$this->common_model->withdrawWinningBalance($USERID,$POSTDATA,$plateform);
			endif;

		elseif($userDetails['status'] === 'I' || $userDetails['status'] === 'B' || $userDetails['status'] === 'D' ):
			$this->session->set_flashdata('alert_error', lang('ACCOUNT_INACIVE'));
		  	redirect('/wallets');
		elseif($availableWinningBalance < $winning_amount ):
        	$error_msg = str_replace('###AMOUNT###', $winning_amount ,  lang('LOW_AVAILABLE_WINNING_BALANCE'));
				$this->session->set_flashdata('alert_error', $error_msg);
			  	redirect('/wallets');
		else:
			$this->session->set_flashdata('alert_error', lang('MIN_WITHDRAW_AMOUNT_ERROR'));
		  	redirect('/wallets');
		endif;

	}

	/***********************************************************************
	** Function name 	: paymentStatus
	** Developed By 	: Dilip Halder
	** Purpose 			: This function used for paymentStatus
	** Date 			: 31 August 2024
	************************************************************************/ 
	public function paymentStatus($URL="")
	{
		$USERID 	 = $this->session->userdata('users_id');
		
		$decodeData = base64_decode($URL);
		$funalData  = json_decode($decodeData, true);

		$status 	 = $funalData['status'];
		$users_id 	 = $funalData['users_id'];
		$request_id  = $funalData['users_id'];
		$device_type = $funalData['users_id'];

		if($status == 'C'):
			$this->session->set_flashdata('alert_error', lang('RECHARE_SUCCESSFULLY'));
		  	redirect('/wallets');

		elseif($status == 'CL'):
			$this->session->set_flashdata('alert_success', lang('PAYMENT_CANCELLED'));
		  	redirect('/wallets');

		elseif($status == 'F'):
			$this->session->set_flashdata('alert_error', lang('PAYMENT_FAILED'));
		  	redirect('/wallets');

		elseif($status == 'P'):
			$this->session->set_flashdata('alert_error', lang('SOMETHINGWENT_WRONG'));
		  	redirect('/wallets');

		else:
			$this->session->set_flashdata('alert_error', lang('SOMETHINGWENT_WRONG'));
		  	redirect('/wallets');
		  	
		endif;
	}

	/***********************************************************************
	** Function name 	: userDeleteRequest
	** Developed By 	: Dilip Halder
	** Purpose 			: This function used for userDeleteRequest
	** Date 			: 03 September 2024
	************************************************************************/ 
	public function userDeleteRequest()
	{
		$data 					= array();
		$data['page']			= 'Delete Request';

	 	if($this->input->post('SaveChanges')):

			$error					=	'NO';
			$this->form_validation->set_error_delimiters('', '');
   	 		$this->form_validation->set_message('is_unique', 'The %s is already taken');
			$this->form_validation->set_rules('email',  'Email',   'trim|required|callback_email_exists');
			$this->form_validation->set_rules('mobile', 'Mobile',  'trim|callback_mobile_exists');
			$this->form_validation->set_rules('reason', 'reason',  'trim|required');

			if($this->form_validation->run() && $error == 'NO'): 
				
				$email   = $this->input->post('email');
				$mobile  = $this->input->post('mobile');
				$reason  = $this->input->post('reason');

				$tableName 			   = 'uw_users';
			    $whereCon['where']     = array('$or'=> array(
				    							array('users_email' => $email),
				    							array('users_mobile' => (int)$mobile)
										));
			    $UserData  			   = $this->common_model->getData('single',$tableName,$whereCon);
			    
			    if(empty($UserData)):
		    		$this->session->set_flashdata('alert_error', lang('INVALID_DATA_ENTERED'));
			    elseif($UserData):

			    	if($email == "info@u-winn.com"):
			     		$OTP  	 = (int)4321;
			    	else:
			     		$OTP  	 = (int)rand(1000,9999);
			    	endif;

			     	$param['users_otp']     = (int)$OTP;
			     	$param['delete_reason'] = $reason;
					$param['update_date'] 	= date('Y-m-d H:i:s');
					$this->geneal_model->editData('uw_users',$param,'users_id',(int)$UserData['users_id']);
			    endif;

			    $users_email  = $UserData['users_email'];
			    $country_code = $UserData['country_code'];
			    $users_mobile = $UserData['users_mobile'];

			    if($users_email):
					$this->emailsendgrid_model->accountVerifyOTP($users_email,$OTP);
			   	endif;

			   	if($users_mobile):
	                $this->sms_model->accountVerifyOTP($country_code,$users_mobile,$OTP);
			   	endif;

			    $this->session->set_userdata('request_email',  $users_email);
		   		$this->session->set_userdata('request_mobile', $users_mobile);
				$this->session->set_flashdata('alert_success',lang('VERIFY_OTP'));
				redirect('/verify-otp');
			endif;
		endif;
		
		$data['step']  = 'delete-request';
		$this->layouts->set_title('Delete Request');
		$this->layouts->front_view('users/delete-request',array(),$data,'onlyview');
	}

	/***********************************************************************
	** Function name 	: verifyUserRequestOTP
	** Developed By 	: Dilip Halder
	** Purpose 			: This function used for verifyUserRequestOTP
	** Date 			: 03 September 2024
	************************************************************************/ 
	public function verifyUserRequestOTP()
	{
		$data 					= array();
		$data['page']			= 'Delete Request';

	 	if($this->input->post('SaveChanges')):

	 		$request_email  = $this->session->userdata('request_email');
	 		$request_mobile = $this->session->userdata('request_mobile');
	 		$otp 			= $this->input->post('otp');

	 		$tableName 			   = 'uw_users';
		    $whereCon['where']     = array('$or'=> array(
			    							array('users_email' => $request_email),
			    							array('users_mobile' => (int)$request_mobile)
									));
		    $UserData  			   		= $this->common_model->getData('single',$tableName,$whereCon);
		    if($UserData['users_otp'] == $otp):
		    	$param['request_id']    = (int)$this->geneal_model->getNextSequence('request_id');
				$param['users_email']   = htmlspecialchars($request_email, ENT_QUOTES, 'UTF-8');
				$param['users_mobile']  = (int)$request_mobile;
				$param['reason']   	 	= htmlspecialchars($UserData['delete_reason'], ENT_QUOTES, 'UTF-8');  
				$param['created_date']  = date('Y-m-d H:i:s');
				$this->geneal_model->addData('uw_users_request',$param);
				
		    	//Account Deleting Code..
		    	$param['status'] 		= 'D';
		    	$param['users_otp'] 	= '';
				$this->geneal_model->editData('uw_users',$param,'users_id',(int)$UserData['users_id']);
				$this->session->set_flashdata('alert_success',lang('ACCOUNT_DELETED_SUCCESSFULLY'));
		    	redirect('delete-request');
		    endif;
 
		endif;
		$data['step']  = 'verify-otp';
		$this->layouts->set_title('Delete Request');
		$this->layouts->front_view('users/delete-request',array(),$data,'onlyview');
	}

	// Callback function to check if email exists
	public function email_exists($email)
	{

	    $tableName 			= 'uw_users';
	    $whereCon['where']  = array('users_email' => $email );
	    $UserData  			= $this->common_model->getData('single',$tableName,$whereCon);

	    if ($UserData['users_email'] == $email) {
	        return TRUE;
	    } else {
	        $this->form_validation->set_message('email_exists', 'The provided email does not exist.');
	        return FALSE;
	    }
	}

	// Callback function to check if mobile exists
	public function mobile_exists($mobile)
	{
	    $tableName 			= 'uw_users';
	    $whereCon['where']  = array('users_mobile' => (int)$mobile);
	    $UserData  			= $this->common_model->getData('single',$tableName,$whereCon);

	    if ($UserData['users_mobile'] == $mobile) {
	        return TRUE;
	    } else {
	        $this->form_validation->set_message('mobile_exists', 'The provided mobile number does not exist.');
	        return FALSE;
	    }
	}
	
	/***********************************************************************
	** Function name 	: deleteProfileImage
	** Developed By 	: Dilip Halder
	** Purpose 			: This function used for deleteProfileImage
	** Date 			: 12 September 2024
	************************************************************************/ 
	public function deleteProfileImage()
	{
		$imageName = $this->input->post('imageName');
		$usersID   =  Decript($this->input->post('id'));

	    $FieldList   		  = array('users_id','status','profile'); 
		$tableName            = 'uw_users';
	    $whereCon['where']    = array('users_id' => (int)$usersID );
		$userDetails 		  = $this->common_model->getParticularFieldByMultipleCondition($FieldList,$tableName,$whereCon);

	 	if($userDetails['profile']):
			$this->load->library("upload_crop_img");
		 	$this->upload_crop_img->_delete_image(trim($imageName)); 
			$param['profile']  =	''; 
			$result = $this->common_model->editData($tableName,$param,'users_id',(int)$usersID);
		endif;

		echo  $result;
	}


	public function resetPassword($value='')
  	{
	  	$data 					= array();
		$data['page']			= 'Reset Password';
		
		$users_id     = $this->session->userdata('users_id');
		$country_code = $this->session->userdata('country_code');
		$users_mobile = $this->session->userdata('users_mobile');
		$users_email  = $this->session->userdata('users_email');

		$tableName 			 = 'uw_users';
	    $whereCon['where']   = array('$or'=> array( array('users_email' => $users_email), array('users_mobile' => (int)$users_mobile) ));
	    $UserData  			 = $this->common_model->getData('single',$tableName,$whereCon);

		if($this->input->post('SaveChanges')):
		 	// Set validation rules
	        $this->form_validation->set_rules('otp', 'OTP', 'required|numeric|exact_length[4]');
	        $this->form_validation->set_rules('new_password', 'New Password', 'required|min_length[8]');
	        $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'required|matches[new_password]');
	        
	        if ($this->form_validation->run() == TRUE):
	        	if($UserData['users_otp'] == $this->input->post('otp')):
		            $param['users_otp']   = '';
		            $param['password']    = md5($this->input->post('new_password'));
		            $param['login_token'] = md5( rand(0001,9999).'_'.$userDetails['users_id']);
			 		$param['update_ip']   = currentIp();
			 		$param["update_date"] = date('Y-m-d H:i');
		            $this->geneal_model->editData('uw_users',$param,'users_id',(int)$users_id);
		            $this->session->set_flashdata('alert_success', lang('PASS_CHANGE_SUCCESS'));
		            redirect('my-profile');
		        else:
	 				$this->session->set_flashdata('alert_error',lang('Invalid_OTP'));
		        endif;
	        endif;

	 	else:
	 		
		    if(empty($UserData)):
	    		$this->session->set_flashdata('alert_error', lang('INVALID_DATA_ENTERED'));
		    elseif($UserData):
	     		$OTP  	 			  = (int)rand(1000,9999);
		     	$param['users_otp']   = (int)$OTP;
				$param['update_date'] = date('Y-m-d H:i:s');
				$this->geneal_model->editData('uw_users',$param,'users_id',(int)$UserData['users_id']);
		    endif;

		    if($users_email):
				$this->emailsendgrid_model->accountVerifyOTP($users_email,$OTP);
		   	endif;

		   	if($users_mobile):
	            $this->sms_model->accountVerifyOTP($country_code,$users_mobile,$OTP);
		   	endif;
	 		$this->session->set_flashdata('alert_success',lang('OTP_SENT'));
	 	endif;

		$this->layouts->set_title('Reset Password');
		$this->layouts->front_view('users/reset-password',array(),$data,'userview');
  	}  

}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Account extends CI_Controller {

	public function  __construct() 
	{ 
		parent:: __construct();
		error_reporting(E_ALL ^ E_NOTICE);  
		$this->load->model(array('admin_model','emailtemplate_model','sms_model','notification_model','geneal_model'));
		$this->lang->load('statictext', 'admin');
		$this->load->helper('common');
	} 

	/* * *********************************************************************
	 * * Function name : maindashboard
	 * * Developed By : DIlip Halder
	 * * Purpose  : This function used for main dashboard
	 * * Date : 06 FEBRUARY 2024
	 * * **********************************************************************/
	public function maindashboard()
	{	
		$this->admin_model->authCheck();
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'';
		$data['activeSubMenu'] 				= 	'';
		$data['orderTargetData'] 			= 	array();
		$data['ordersponsoredData'] 		= 	array();

		$data['moduleData']					=	$this->admin_model->getMenuModule('main'); 
		$this->session->unset_userdata('ALLTICKETSDATA');

		$this->layouts->set_title('Dashboard | Admin | UWINN');
		$this->layouts->admin_view('account/maindashboard',array(),$data);

	}	// END OF FUNCTION

	/* * *********************************************************************
	 * * Function name : profile
	 * * Developed By : DIlip Halder
	 * * Purpose  : This function used for admin profile
	 * * Date : 06 FEBRUARY 2024
	 * * **********************************************************************/
	public function profile()
	{	
		$this->admin_model->authCheck('view_data');
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'';
		$data['activeSubMenu'] 				= 	'';
		
		$whereCon['where']			 		= 	array('admin_id'=>(int)$this->session->userdata('UW_ADMIN_ID'));		
		$shortField 						= 	array('admin_id'=>'ASC');
		
		$this->load->library('pagination');
		$config['base_url'] 				= 	$this->session->userdata('UW_ADMIN_CURRENT_PATH').$this->router->fetch_class().'/profile';
		$tblName 							= 	'uw_admin';
		$con 								= 	'';
		$config['total_rows'] 				= 	$this->common_model->getData('count',$tblName,$whereCon,$shortField,'0','0');
		$config['per_page']	 				= 	10;
		$config['uri_segment'] 				= 	getUrlSegment();
       $this->pagination->initialize($config);

       if ($this->uri->segment(getUrlSegment())):
           $page = $this->uri->segment(getUrlSegment());
       else:
           $page = 0;
       endif;
		
		$data['ADMINDATA'] 					= 	$this->common_model->getData('single',$tblName,$whereCon,$shortField,$config['per_page'],$page); 

		$this->layouts->set_title('Profile');
		$this->layouts->admin_view('account/profile',array(),$data);
	}	// END OF FUNCTION

	/* * *********************************************************************
	 * * Function name : editprofile
	 * * Developed By : DIlip Halder
	 * * Purpose  : This function used for admin editprofile
	 * * Date : 06 FEBRUARY 2024
	 * * **********************************************************************/
	public function editprofile($editId='')
	{	
		$this->admin_model->authCheck('edit_data');
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'';
		$data['activeSubMenu'] 				= 	'';
		
		$data['profileuserdata']			=	$this->common_model->getDataByParticularField('uw_admin','admin_id',(int)$editId); 
		if($data['profileuserdata'] == ''):
			redirect($this->session->userdata('UW_ADMIN_CURRENT_PATH').'maindashboard');
		endif;
		
		if($this->input->post('SaveChanges')):
			$error							=	'NO';
			$this->form_validation->set_rules('admin_title', 'Title', 'trim|required|max_length[64]');
			$this->form_validation->set_rules('admin_first_name', 'First Name', 'trim|required|max_length[64]');
			$this->form_validation->set_rules('admin_middle_name', 'Middle Name', 'trim|max_length[64]');
			$this->form_validation->set_rules('admin_last_name', 'Last Name', 'trim|required|max_length[64]');
			$this->form_validation->set_rules('admin_email', 'E-Mail', 'trim|required|valid_email|max_length[64]|is_unique[hcap_admin.admin_email.string]');			
			$this->form_validation->set_rules('admin_country_code', 'Country Code', 'trim|required');
			$this->form_validation->set_rules('admin_phone', 'Mobile number', 'trim|required|min_length[9]|max_length[15]|is_unique[hcap_admin.admin_phone.integer]');
			$testmobile		=	str_replace(' ','',$this->input->post('admin_phone'));
			if($this->input->post('admin_phone') && !preg_match('/^(\d[\s-]?)?[\(\[\s-]{0,2}?\d{3}[\)\]\s-]{0,2}?\d{3}[\s-]?\d{4}$/i',$testmobile)):
				if(!preg_match("/^((\+){0,1}91(\s){0,1}(\-){0,1}(\s){0,1})?([0-9]{10})$/",$testmobile)):
					$error						=	'YES';
					$data['mobileerror'] 		= 	'Please Eneter Correct Number';
				endif;
			endif;
			$this->form_validation->set_rules('admin_address', 'Address', 'trim|max_length[512]');
			$this->form_validation->set_rules('admin_city', 'City', 'trim');
			$this->form_validation->set_rules('admin_state', 'State', 'trim');
			$this->form_validation->set_rules('admin_pincode', 'Zipcode', 'trim');

			 if($this->input->post('admin_pin')){
			$this->form_validation->set_rules('admin_pin', 'Pin', 'trim|max_length[4]');
			 }
			if($this->form_validation->run() && $error == 'NO'):
			 
				$param['admin_title']				= 	addslashes($this->input->post('admin_title'));
				$param['admin_first_name']			= 	addslashes($this->input->post('admin_first_name'));
				$param['admin_middle_name']			= 	addslashes($this->input->post('admin_middle_name'));
				$param['admin_last_name']			= 	addslashes($this->input->post('admin_last_name'));
				$param['admin_email']				= 	addslashes($this->input->post('admin_email'));
				$param['admin_country_code']		= 	$this->input->post('admin_country_code');
				$param['admin_phone']				= 	(int)($this->input->post('admin_phone'));
				$param['admin_address']				= 	addslashes($this->input->post('admin_address'));
				$param['admin_city']				= 	addslashes($this->input->post('admin_city'));
				$param['admin_state']				= 	addslashes($this->input->post('admin_state'));
				$param['admin_pincode']				= 	(int)$this->input->post('admin_pincode');
				$param['admin_pin']				    = 	(int)$this->input->post('admin_pin');
				$param['update_ip']					=	currentIp();
				$param['update_date']				=	(int)$this->timezone->utc_time();//currentDateTime();
				$param['updated_by']				=	(int)$this->session->userdata('UW_ADMIN_ID');
				$this->common_model->editData('uw_admin',$param,'admin_id',(int)$this->input->post('CurrentDataID'));
				
				$result								=	$this->admin_model->Authenticate($param['admin_email']);
				if($result):
					$this->session->set_userdata(array('UW_ADMIN_TITLE'			=>	$result['admin_title'],
													   'UW_ADMIN_FIRST_NAME'		=>	$result['admin_first_name'],
													   'UW_ADMIN_MIDDLE_NAME'	=>	$result['admin_middle_name'],
													   'UW_ADMIN_LAST_NAME'		=>	$result['admin_last_name'],
													   'UW_ADMIN_EMAIL'			=>	$result['admin_email'],
													   'UW_ADMIN_MOBILE'			=>	$result['admin_phone'],
													   'UW_ADMIN_ADDRESS'		=>	$result['admin_address'],
													   'UW_ADMIN_CITY'			=>	$result['admin_city'],
													   'UW_ADMIN_STATE'			=>	$result['admin_state'],
													   'UW_ADMIN_ZIPCODE'		=>	$result['admin_pincode'],
													   'UW_ADMIN_PIN'		=>	$result['admin_pin']));
											
					$this->session->set_flashdata('alert_success',lang('updatesuccess'));
					redirect($this->session->userdata('UW_ADMIN_CURRENT_PATH').'profile');
				endif;
			endif;
		endif;
		
		$this->layouts->set_title('Edit Profile');
		$this->layouts->admin_view('account/editprofile',array(),$data);
	}	// END OF FUNCTION

	/* * *********************************************************************
	 * * Function name : changepassword
	 * * Developed By : DIlip Halder
	 * * Purpose  : This function used for change password
	 * * Date : 06 FEBRUARY 2024
	 * * **********************************************************************/
	public function changepassword($editId='')
	{	
		$this->admin_model->authCheck('edit_data');
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'';
		$data['activeSubMenu'] 				= 	'';
		
		$data['EDITDATA']					=	$this->common_model->getDataByParticularField('uw_admin','admin_id',(int)$editId);  
		if($data['EDITDATA'] == ''):
			redirect($this->session->userdata('UW_ADMIN_CURRENT_PATH').'maindashboard');
		endif; 
		$data['OLDPASSWORD']				=	$this->admin_model->decryptsPassword($data['EDITDATA']['admin_password']);

		if($this->input->post('SaveChanges')):
			$error					=	'NO';
			$this->form_validation->set_rules('old_password', 'Old Password', 'trim');
			$this->form_validation->set_rules('current_password', 'Current Password', 'trim|required|min_length[6]|matches[old_password]');
			$this->form_validation->set_rules('new_password', 'New Password', 'trim|required|min_length[6]|max_length[25]');
			$this->form_validation->set_rules('conf_password', 'Confirm Password', 'trim|required|min_length[6]|matches[new_password]');
			
			if($this->form_validation->run() && $error == 'NO'):  
			//	$param['admin_password_otp']		=	(int)'4321';//(int)generateRandomString(4,'n');
				$this->common_model->editData('uw_admin',$param,'admin_id',(int)$data['EDITDATA']['admin_id']);

				//$this->sms_model->sendChangePasswordOtpSmsToUser($data['EDITDATA']['admin_phone'],$param['admin_password_otp']);

				//$this->session->set_userdata(array('otpType'=>'Change Password','otpAdminId'=>$data['EDITDATA']['admin_id'],'otpAdminMobile'=>$data['EDITDATA']['admin_phone'],'changeNewPassword'=>$this->input->post('new_password')));

				//$this->session->set_flashdata('alert_success',lang('sendotptomobile').$data['EDITDATA']['admin_phone']);
				redirect($this->session->userdata('UW_ADMIN_CURRENT_PATH').'change-password-verify-otp');
			endif;
		endif;
		
		$this->layouts->set_title('Change password');
		$this->layouts->admin_view('account/changepassword',array(),$data);
	}	// END OF FUNCTION

	/* * *********************************************************************
	 * * Function name : changepasswordverifyotp
	 * * Developed By : DIlip Halder
	 * * Purpose  : This function used for change password verify otp
	 * * Date : 06 FEBRUARY 2024
	 * * **********************************************************************/
	public function changepasswordverifyotp()
	{	
		$this->admin_model->authCheck('edit_data');
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'';
		$data['activeSubMenu'] 				= 	'';

		/*-----------------------------------change password verify otp---------------*/
		if($this->input->post('SaveChanges')):	
			//Set rules
			$this->form_validation->set_rules('userOtp', 'otp', 'trim|required|min_length[4]|max_length[4]');
			
			if($this->form_validation->run()):	
				$result		=	$this->admin_model->checkOTP((int)$this->input->post('userOtp'));
				if($result): 
					$param['admin_password']		= 	$this->admin_model->encriptPassword(sessionData('changeNewPassword'));
					$param['update_ip']				=	currentIp();
					$param['update_date']			=	(int)$this->timezone->utc_time();//currentDateTime();
					$param['updated_by']			=	(int)sessionData('UW_ADMIN_ID');
					$this->common_model->editData('uw_admin',$param,'admin_id',(int)sessionData('UW_ADMIN_ID'));

					$this->session->unset_userdata(array('otpType','otpAdminId','otpAdminMobile','changeNewPassword'));

					$this->session->set_flashdata('alert_success',lang('passwordchangesuccess'));
					redirect($this->session->userdata('UW_ADMIN_CURRENT_PATH').'profile');
				else:
					$data['recovererror'] = lang('invalidotp');
				endif;
			endif;
		endif;
		
		$this->layouts->set_title('Change password - Verify OTP');
		$this->layouts->admin_view('account/changepasswordverifyotp',array(),$data);
	}	// END OF FUNCTION

	public function getDBquery(){
		$prodFields 		=  	array('$project' => array('_id'=>0,'title'=>1,'products_id'=>1,'stock'=>1,'totalStock'=>1,'target_stock'=>1,
														  'category_name'=>1,'sub_category_name'=>1,'product_image'=>1,'clossingSoon'=>1,'product_seq_id'=>1,'validuptodate'=>1,'validuptotime'=>1,
														  'actual_product_name'=>'$from_prize.title','actual_product_image'=>'$from_prize.prize_image'
														 ));
		$prodCon			=	array(array('clossingSoon'=>'N')
									  //,array('stock'=>array('$gt'=> 0)) 
									);
		$prodQuery			=	array(array('$lookup'=>array('from'=>'da_prize','localField'=>'products_id','foreignField'=>'product_id','as'=>'from_prize')),
									  $prodFields,
									  array('$match'=>array('$and'=>$prodCon)),
									  array('$sort'=>array('creation_date'=>-1)));	
		$prodData			=	$this->common_model->getDataByMultipleAndCondition('da_products',$prodQuery);
		return $prodData;
	}

	//Get ticket dashboard data
	public function getTicketData(){
		//echo 'working';die();
		$whereCon['where']			 		= 	array('status' => 'A');		
		$shortField 						= 	array('created_at'=> -1);
		$tblName 							= 	'da_tickets_sequence';
		$ticket_stock						= 	$this->common_model->getTicketCount('multiple',$tblName,$whereCon,$shortField,5,'');
		$MHTML = '';
		foreach ($ticket_stock as $key => $items) {
			if( !empty($items['coupon_sold_number'])): 
				$stok =  stripslashes($items['coupon_sold_number']);													 
			 else: 
				$stok = '0'; 
			 endif; 

			$MHTML	.=	'<tr>';
			$MHTML	.=		'<td>'.stripslashes( substr($items['product_id'], -5)).'</td>';
			$MHTML	.=		'<td>'.stripslashes($items['product_title']).'</td>';
			$MHTML	.=		'<td>'.$stok.'</td>';
			$MHTML	.=		'<td>'.stripslashes($items['tickets_sequence_end']- $items['tickets_sequence_start'] ).'</td>';
			$MHTML	.=		'<td>'.date('d-M-Y H:i A', strtotime($items['created_at'])).'</td>';
			$MHTML	.=	'</tr>';
		}
		echo $MHTML;
	}

	//Get Campaign sales dashboard data
	public function getCampaignSalesData(){
		$orderTargetData  = [];
		$prodData = $this->getDBquery();
		if($prodData):	
			$count  		=	0;
			foreach($prodData as $prodInfo):
				if($count <=10):
					$dataFound   			=	'N';

					$ordTarFields 			=  	array('$project' => array('_id'=>0,'product_id'=>1,'quantity'=>1,'order_status'=>'$from_order.order_status'));
					$ordTarCon				=	array(array('product_id'=>$prodInfo['products_id'],'order_status'=>'Success'));
					$ordTarQuery			=	array(array('$lookup'=>array('from'=>'da_orders','localField'=>'order_id','foreignField'=>'order_id','as'=>'from_order')),
											  	$ordTarFields,
											  	array('$match'=>array('$and'=>$ordTarCon)),
											  	array('$group' => array('_id' => '$product_id','count'=>array('$sum' => '$quantity'))),
											  	array('$sort'=>array('creation_date'=>-1)));	
					$ordTarData				=	$this->common_model->getDataByMultipleAndCondition('da_orders_details',$ordTarQuery);
					if($ordTarData):
						$prodInfo['total_sale'] 	=	$ordTarData[0]['count'];
						array_push($orderTargetData,$prodInfo);
						$prodInfo['sponsored'] 		=	0;
						$prodInfo['not_sponsored'] 	=	0;
					endif;	
				endif;
				$count++;
			endforeach;
		endif;

		$MHTML = '';
		foreach ($orderTargetData as $key => $items):
			$MHTML	.=	'<tr>';
			$MHTML	.=		'<td>'.stripslashes($items['product_seq_id']).'</td>';
			$MHTML	.=		'<td>'.stripslashes($items['title']).'</td>';
			$MHTML	.=		'<td>'.stripslashes($items['target_stock']).'</td>';
			$MHTML	.=		'<td>'.stripslashes(stripslashes($items['total_sale'])).'</td>';
			$MHTML	.=		'<td>'.date('d-M-Y H:i A', strtotime($items['validuptodate'].' '.$items['validuptotime'])).'</td>';
			$MHTML	.=	'</tr>';
		endforeach;
		echo $MHTML;
	}

	//Get sponsored dashboard data
	public function getSponsoredData(){
		$ordersponsoredData  = [];
		$prodData = $this->getDBquery();
		if($prodData):	
			$count  		=	0;
			foreach($prodData as $prodInfo):
				if($count <=10):
					$dataFound   			=	'N';

					$ordDonateFields 			=  	array('$project' => array('_id'=>0,'product_id'=>1,'quantity'=>1,'is_donated'=>1,'order_status'=>'$from_order.order_status'));
					$ordDonateCon				=	array(array('product_id'=>$prodInfo['products_id'],'order_status'=>'Success','is_donated'=>'Y'));
					$ordDonateQuery				=	array(array('$lookup'=>array('from'=>'da_orders','localField'=>'order_id','foreignField'=>'order_id','as'=>'from_order')),
												    $ordDonateFields,
												    array('$match'=>array('$and'=>$ordDonateCon)),
												    array('$group' => array('_id' => '$product_id','count'=>array('$sum' => '$quantity'))),
												    array('$sort'=>array('creation_date'=>-1)));	
					$ordDonateData				=	$this->common_model->getDataByMultipleAndCondition('da_orders_details',$ordDonateQuery);
					if($ordDonateData): 
						$data['sponsored'] 	=	$ordDonateData[0]['count'];
						$dataFound   			=	'Y';
					endif;

					$ordNotDonateFields 			=  	array('$project' => array('_id'=>0,'product_id'=>1,'quantity'=>1,'is_donated'=>1,'order_status'=>'$from_order.order_status'));
					$ordNotNotDonateCon				=	array(array('product_id'=>$prodInfo['products_id'],'order_status'=>'Success','is_donated'=>'N'));
					$ordNotDonateQuery				=	array(array('$lookup'=>array('from'=>'da_orders','localField'=>'order_id','foreignField'=>'order_id','as'=>'from_order')),
													    $ordNotDonateFields,
													    array('$match'=>array('$and'=>$ordNotNotDonateCon)),
													    array('$group' => array('_id' => '$product_id','count'=>array('$sum' => '$quantity'))),
													    array('$sort'=>array('creation_date'=>-1)));	
					$ordNotDonateData				=	$this->common_model->getDataByMultipleAndCondition('da_orders_details',$ordNotDonateQuery);
					if($ordNotDonateData):
						$prodInfo['not_sponsored'] 	=	$ordNotDonateData[0]['count'];
						$dataFound   				=	'Y';
					endif;

					if($dataFound == 'Y'):
						array_push($ordersponsoredData,$prodInfo);
					endif;

				endif;
				$count++;
			endforeach;
		endif;

		$MHTML = '';
		foreach ($ordersponsoredData as $key => $items):
			$MHTML	.=	'<tr>';
			$MHTML	.=		'<td>'.stripslashes($items['product_seq_id']).'</td>';
			$MHTML	.=		'<td>'.stripslashes($items['title']).'</td>';
			$MHTML	.=		'<td>'.stripslashes($items['not_sponsored']).'</td>';
			$MHTML	.=	'</tr>';
		endforeach;
		echo $MHTML;
	}

	//Get referral dashboard data
	public function getRefferalData(){
		$referralWhere['where']				=	array('status' => 'A');
		$referralField 						= 	array('created_at'=> -1);
		$referralData				=	$this->common_model->getReferralList('multiple','referral_product',$referralWhere,$referralField,0,5);

		$MHTML = '';
		foreach ($referralData as $key => $items) { 
			$MHTML	.=	'<tr>';
			$MHTML	.=		'<td>'.stripslashes($items['receiver_name']).'</td>';
			$MHTML	.=		'<td>'.stripslashes($items['product_name']).'</td>';
			$MHTML	.=		'<td>'.number_format($items['referral_amount'],2).'</td>';
			$MHTML	.=		'<td>'.stripslashes($items['sender_name']).'</td>';
			$MHTML	.=		'<td>'.date('M d, Y h:i A', strtotime($items['created_at'])).'</td>';
			$MHTML	.=	'</tr>';
		}
		echo $MHTML;
	}

	public function getSignupBonusData(){
		$signupBonusWhere['where']				=	array('status' => 'A','arabian_points_from'=>'Signup Bonus');
		$signupBonusField 						= 	array('created_at'=> -1);
		$signupBonusData				=	$this->common_model->getsignupBonusList('multiple','da_loadBalance',$signupBonusWhere,$signupBonusField,0,5);

		$MHTML = '';
		foreach ($signupBonusData as $key => $items) { 
			$MHTML	.=	'<tr>';
			$MHTML	.=		'<td>'.date('M d, Y h:i A',strtotime($items['created_at'])).'</td>';
			$MHTML	.=		'<td>'.stripslashes($items['users_name']).'</td>';
			$MHTML	.=		'<td>'.number_format($items['arabian_points'],2).'</td>';
			$MHTML	.=	'</tr>';
		}
		echo $MHTML;
	}

	public function getMembershipData(){
		$membershipCashbackFields 			=  	array('$project' => array('_id'=>0 ,'arabian_points'=>1,'created_at' => 1 ,'status'=>1,'arabian_points_from'=>1,'record_type'=>1,'users_name'=>'$from_users.users_name','users_type'=>'$from_users.users_type' ));
		
		$membershipCashbackCon				=	array(array('status' => 'A','arabian_points_from' => 'Membership Cashback','record_type'=>'Credit'));

		$membershipCashbackQuery			=	array(array('$lookup'=>array('from'=>'da_users','localField'=>'user_id_cred','foreignField'=>'users_id','as'=>'from_users')),
											  	$membershipCashbackFields,
											  	array('$match'=>array('$and'=>$membershipCashbackCon)),
											  	array('$limit' => 5),
											  	array('$sort'=>array('created_at'=>-1)));	
	 	$membershipCashbackData	=	$this->common_model->getDataByMultipleAndCondition('da_loadBalance',$membershipCashbackQuery);

		 $MHTML = '';
		 foreach ($membershipCashbackData as $key => $items) { 
			 $MHTML	.=	'<tr>';
			 $MHTML	.=		'<td>'.date('M d, Y h:i A',strtotime($items['created_at'])).'</td>';
			 $MHTML	.=		'<td>'.stripslashes($items['users_type']).'</td>';
			 $MHTML	.=		'<td>'.stripslashes($items['users_name']).'</td>';
			 $MHTML .=		'<td>'.number_format($items['arabian_points'],2).'</td>';
			 $MHTML	.=	'</tr>';
		 }
		 echo $MHTML;
	}

	public function getRechargeData(){
		$reWhere['where']		 			= 	array('arabian_points_from' => 'Recharge');	
		$reShortField 						= 	array('created_at'=> -1);
		$rechargeData 				= 	$this->common_model->getDataByNewQuery('*','multiple','da_loadBalance',$reWhere,$reShortField ,5,0);

		$MHTML = '';
		if($rechargeData):
		 foreach ($rechargeData as $key => $items) { 
			if($items['record_type'] == 'Debit'){ 
				$id = $items['user_id_to'];
			  }elseif ($items['record_type'] == 'Credit' && $items['created_by'] != 'ADMIN') {
				$id = $items['user_id_cred'];
			  }elseif ($items['record_type'] == 'Credit' && $items['created_by'] == 'ADMIN'){
				$id = $items['user_id_cred'];
			  }
			  $users_type = $this->common_model->getPaticularFieldByFields('users_type', 'da_users', 'users_id', (int)$id);
			  $users_email = $this->common_model->getPaticularFieldByFields('users_email', 'da_users', 'users_id', (int)$id);
			 $MHTML	.=	'<tr>';
			 $MHTML	.=		'<td>'.stripslashes($items['arabian_points']).'</td>';
			 $MHTML	.=		'<td>'.stripslashes($items['record_type']).'</td>';
			 $MHTML .=		'<td>'.$users_type.'</td>';
			 $MHTML .=		'<td>'.$users_email.'</td>';
			 $MHTML .=		'<td>'.date('d-F-Y h:i A',strtotime($items['created_at'])).'</td>';
			 $MHTML	.=	'</tr>';
		 }
		endif;
		 echo $MHTML;

	}
}
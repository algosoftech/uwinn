<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Generaldata extends CI_Controller {

	public function  __construct() 
	{ 
		parent:: __construct();
		error_reporting(E_ALL ^ E_NOTICE);  
		$this->load->model(array('admin_model','emailtemplate_model','sms_model','notification_model'));
		$this->lang->load('statictext', 'admin');
		$this->load->helper('common');
	} 

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name 	: index
	 + + Developed By 	: Ashish Umrao
	 + + Purpose  		: This function used for index
	 + + Date 			: 31 MARCH 2022
	 + + Updated Date 	: 29 January 2024
	 + + Updated By   	: Dilip Halder
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index()
	{	
		$this->admin_model->authCheck();
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'cms';
		$data['activeSubMenu'] 				= 	'generaldata';
		
		if($this->input->get('searchField') && $this->input->get('searchValue')):
			$sField							=	$this->input->get('searchField');
			$sValue							=	$this->input->get('searchValue');
			$whereCon['like']			 	= 	array('0'=>trim($sField),'1'=>trim($sValue));
			$data['searchField'] 			= 	$sField;
			$data['searchValue'] 			= 	$sValue;
		else:
			$whereCon['like']		 		= 	"";
			$data['searchField'] 			= 	'';
			$data['searchValue'] 			= 	'';
		endif;
				
		$whereCon['where']		 			= 	'';		
		$shortField 						= 	array('testi_name'=>'ASC');
		
		$baseUrl 							= 	getCurrentControllerPath('index');
		$this->session->set_userdata('CMSGENERALDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 	'uw_general_data';
		$con 								= 	'';
		$totalRows 							= 	$this->common_model->getData('count',$tblName,$whereCon,$shortField,'0','0');
		
		if($this->input->get('showLength') == 'All'):
			$perPage	 					= 	$totalRows;
			$data['perpage'] 				= 	$this->input->get('showLength');  
		elseif($this->input->get('showLength')):
			$perPage	 					= 	$this->input->get('showLength'); 
			$data['perpage'] 				= 	$this->input->get('showLength'); 
		else:
			$perPage	 					= 	SHOW_NO_OF_DATA;
			$data['perpage'] 				= 	SHOW_NO_OF_DATA; 
		endif;
		$uriSegment 						= 	getUrlSegment();
	    $data['PAGINATION']					=	adminPagination($baseUrl,$suffix,$totalRows,$perPage,$uriSegment);
	    //echo "<pre>"; print_r($uriSegment); die;
       if ($this->uri->segment(getUrlSegment())):
           $page = $this->uri->segment(getUrlSegment());
       else:
           $page = 0;
       endif;
		
		$data['forAction'] 					= 	$baseUrl; 
		if($totalRows):
			$first							=	(int)($page)+1;
			$data['first']					=	$first;
			$last							=	((int)($page)+$data['perpage'])>$totalRows?$totalRows:((int)($page)+$data['perpage']);
			$data['noOfContent']			=	'Showing '.$first.'-'.$last.' of '.$totalRows.' items';
		else:
			$data['first']					=	1;
			$data['noOfContent']			=	'';
		endif;
		
		$data['ALLDATA'] 					= 	$this->common_model->getData('multiple',$tblName,$whereCon,$shortField,$perPage,$page);

		$this->layouts->set_title('General Data | CMS | UWINN');
		$this->layouts->admin_view('cms/generaldata/index',array(),$data);
	}	// END OF FUNCTION
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name : addeditdata
	 + + Developed By  : Ashish Umrao
	 + + Purpose  	   : This function used for Add Edit data
	 + + Date 		   : 31 MARCH 2022
	 + + Updated Date  : 29 January 2024
	 + + Updated By    : Dilip Halder
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function addeditdata($editId='')
	{		
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'cms';
		$data['activeSubMenu'] 				= 	'generaldata';
		
		if($editId):
			$this->admin_model->authCheck('edit_data');
			$data['EDITDATA']		=	$this->common_model->getDataByParticularField('uw_general_data','general_data_id',(int)$editId);
		else:
			$this->admin_model->authCheck('add_data');
		endif;
		
		if($this->input->post('SaveChanges')): 
			$error					=	'NO';
			$this->form_validation->set_rules('image', 'Logo', 'trim');
			$this->form_validation->set_rules('alt_text', 'Alt Text', 'trim|required');
			$this->form_validation->set_rules('email_id', 'Name', 'trim|required');
			$this->form_validation->set_rules('contact_no', 'description', 'trim');
			$this->form_validation->set_rules('address', 'Name', 'trim|required');
			$this->form_validation->set_rules('facebook_link', 'description', 'trim');
			$this->form_validation->set_rules('linkedin_link', 'Name', 'trim|required');
			$this->form_validation->set_rules('twitter_link', 'description', 'trim');
			$this->form_validation->set_rules('insta_link', 'Name', 'trim|required');
			$this->form_validation->set_rules('you_tube', 'YouTube', 'trim|required');
			$this->form_validation->set_rules('slider_type', 'Slider Type', 'trim|required');
			$this->form_validation->set_rules('whatsapp_no', 'Whatsapp Number', 'trim|required');
			$this->form_validation->set_rules('whatsapp_authorization_key', 'WhatsApp Authorization Key', 'trim|required');
			$this->form_validation->set_rules('android_version', 'Andooid Version', 'trim|required');
			$this->form_validation->set_rules('ios_version', 'IOS Version', 'trim|required');
			$this->form_validation->set_rules('app_url', 'Application URL', 'trim|required');
			$this->form_validation->set_rules('draw_time_start', 'Draw Time Start', 'trim|required');
			$this->form_validation->set_rules('draw_time_end', 'Draw Time End', 'trim|required');
			$this->form_validation->set_rules('delivery_charge', 'Delivery Charge', 'trim|required');
			$this->form_validation->set_rules('drawdata_pin', 'Draw data Pin', 'trim|required');
			$this->form_validation->set_rules('comming_soon_pro_btn', 'Comming soon Product Button', 'trim');
			$this->form_validation->set_rules('comming_soon_text', 'Comming soon Product', 'trim');
			$this->form_validation->set_rules('home_botttom_slider_header', 'Slider Heading', 'trim');
			$this->form_validation->set_rules('prize_title', 'Prize Title', 'trim');
			$this->form_validation->set_rules('hourly_game_blank_page_title', 'Hourly Game Blank Page Title', 'trim');
			$this->form_validation->set_rules('enable_u_points_in_pos', 'Enable U Points in POS', 'trim');
			$this->form_validation->set_rules('show_merchant_name', 'Show Merchant Name', 'trim|required');
			$this->form_validation->set_rules('show_merchant_id', 'Show Merchant Id', 'trim|required');

			$this->form_validation->set_rules('show_bank_widhdrawal', 'Show B2C bank withdrawal', 'trim|required');
			$this->form_validation->set_rules('btc_bank_withdrawal', 'B2C bank withdrawal ', 'trim|required');
			$this->form_validation->set_rules('show_crypto', 'Show Crypto withdrawal ', 'trim|required');
			$this->form_validation->set_rules('btc_crypto_limit', 'B2C Crypto withdrawal ', 'trim|required');
			$this->form_validation->set_rules('summary_time', 'Summary Time', 'trim|required');
			$this->form_validation->set_rules('global_freezing', 'Global Freezing', 'trim|required');
			$this->form_validation->set_rules('recharge_topup_btn', 'Recharge Topup Btn', 'trim|required');
			$this->form_validation->set_rules('recharge_topup_start_time', 'Recharge Topup End Time', 'trim|required');
			$this->form_validation->set_rules('recharge_topup_end_time', 'Recharge Topup End Time', 'trim|required');
			$this->form_validation->set_rules('recharge_topup_msg', 'Recharge Topup Message', 'trim|required');
			$this->form_validation->set_rules('enable_raffle_entries', 'Enable Raffle Entries', 'trim|required');

			$this->form_validation->set_rules('b2b_mobile_qr_code','Show QR Code', 'trim|required');
			$this->form_validation->set_rules('b2b_mobile_qr_code_time_sec','Show QR Time', 'trim|required');

			$this->form_validation->set_rules('website_raffle_title', 'Website Raffle Title', 'trim|required');
			$this->form_validation->set_rules('alt_text_raffle', 'Alt Text Raffle', 'trim|required');
			$this->form_validation->set_rules('email_raffle', 'Email Raffle', 'trim|required');
			$this->form_validation->set_rules('contact_number_raffle', 'Contact Number Raffle', 'trim|required');
			$this->form_validation->set_rules('address_raffle', 'Address Raffle', 'trim|required');

			if($this->form_validation->run() && $error == 'NO'): 

				if($_FILES['image']['name']):
						$ufileName				= 	$_FILES['image']['name'];
						$utmpName				= 	$_FILES['image']['tmp_name'];
						$ufileExt         	= 	pathinfo($ufileName);
						$unewFileName 			= 	time().'.'.$ufileExt['extension'];
						$this->load->library("upload_crop_img");
						$uimageLink				=	$this->upload_crop_img->_upload_image_from_app($ufileName,$utmpName,$unewFileName,'generaldata','');
					$param['logo']				= 	$uimageLink;
				endif;

				
				//echo "<pre>";print_r($param);die;
				$param['website_name']			= 	stripslashes($this->input->post('website_name'));
				$param['alt_text']				= 	stripslashes($this->input->post('alt_text'));
				$param['email_id']				= 	stripslashes($this->input->post('email_id'));
				$param['contact_no']			= 	stripslashes($this->input->post('contact_no'));
				$param['address']				= 	stripslashes($this->input->post('address'));
				$param['facebook_link']			= 	stripslashes($this->input->post('facebook_link'));
				$param['linkedin_link']			= 	stripslashes($this->input->post('linkedin_link'));
				$param['twitter_link']			= 	stripslashes($this->input->post('twitter_link'));
				$param['insta_link']			= 	stripslashes($this->input->post('insta_link'));
				$param['you_tube']		    	= 	stripslashes($this->input->post('you_tube'));
				$param['slider_type']		    = 	stripslashes($this->input->post('slider_type'));
				$param['whatsapp_no']		    = 	stripslashes($this->input->post('whatsapp_no'));
				$param['whatsapp_authorization_key'] = 	stripslashes($this->input->post('whatsapp_authorization_key'));
				$param['android_version']		= 	stripslashes($this->input->post('android_version'));
				$param['ios_version']		    = 	stripslashes($this->input->post('ios_version'));
				$param['app_url']		    	= 	stripslashes($this->input->post('app_url'));
				$param['draw_time_start']		= 	stripslashes($this->input->post('draw_time_start'));
				$param['draw_time_end']		    = 	stripslashes($this->input->post('draw_time_end'));
				$param['delivery_charge']		= 	stripslashes($this->input->post('delivery_charge'));
				$param['drawdata_pin']			= 	stripslashes($this->input->post('drawdata_pin'));
				$param['comming_soon_pro_btn']	= 	stripslashes($this->input->post('comming_soon_pro_btn'));
				$param['prize_title'] 				   = stripslashes($this->input->post('prize_title'));
				$param['hourly_game_blank_page_title'] = stripslashes($this->input->post('hourly_game_blank_page_title'));
				$param['comming_soon_text']			 = 	stripslashes($this->input->post('comming_soon_text'));
				$param['home_botttom_slider_header'] = 	stripslashes($this->input->post('home_botttom_slider_header'));
				$param['enable_u_points_in_pos'] 	 = 	stripslashes($this->input->post('enable_u_points_in_pos'));
				$param['show_merchant_id'] 	 		 = 	stripslashes($this->input->post('show_merchant_id'));
				$param['show_merchant_name'] 	 	 = 	stripslashes($this->input->post('show_merchant_name'));

				$param['btc_bank_withdrawal'] 	 	 = 	(float)$this->input->post('btc_bank_withdrawal');
				$param['show_bank_widhdrawal'] 	 	 = 	stripslashes($this->input->post('show_bank_widhdrawal'));
				$param['global_freezing'] 	 	     = 	$this->input->post('global_freezing');
				$param['summary_time'] 	 	 		 = 	$this->input->post('summary_time');
				$param['btc_crypto_limit'] 	 	 	 = 	(float)$this->input->post('btc_crypto_limit');
				$param['show_crypto'] 	 	 		 = 	stripslashes($this->input->post('show_crypto'));
				$param['recharge_topup_btn'] 	 	 = $this->input->post('recharge_topup_btn');
				$param['recharge_topup_start_time']  = $this->input->post('recharge_topup_start_time');
				$param['recharge_topup_end_time'] 	 = $this->input->post('recharge_topup_end_time');
				$param['recharge_topup_msg'] 	     = $this->input->post('recharge_topup_msg');
				$param['enable_raffle_entries'] 	 = $this->input->post('enable_raffle_entries');
				$param['b2b_mobile_qr_code'] 	      = $this->input->post('b2b_mobile_qr_code');
				$param['b2b_mobile_qr_code_time_sec'] = (int)$this->input->post('b2b_mobile_qr_code_time_sec');

				$param['website_raffle_title'] = stripslashes($this->input->post('website_raffle_title'));
				$param['alt_text_raffle'] = stripslashes($this->input->post('alt_text_raffle'));
				$param['email_raffle'] = stripslashes($this->input->post('email_raffle'));
				$param['contact_number_raffle'] = stripslashes($this->input->post('contact_number_raffle'));
				$param['address_raffle'] = stripslashes($this->input->post('address_raffle'));
				
				if($this->input->post('CurrentDataID') ==''):
						$param['general_data_id']		=	(int)$this->common_model->getNextSequence('uw_general_data');
						$param['creation_ip']			=	currentIp();
						$param['creation_date']			=	(int)$this->timezone->utc_time();//currentDateTime();
						$param['created_by']				=	(int)$this->session->userdata('UW_ADMIN_ID');
						$param['status']					=	'A';
						$alastInsertId						=	$this->common_model->addData('uw_general_data',$param);
						$this->session->set_flashdata('alert_success',lang('addsuccess'));
					else:
						$generaldataId					=	$this->input->post('CurrentDataID');
						$param['update_ip']			=	currentIp();
						$param['update_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
						$param['updated_by']			=	(int)$this->session->userdata('UW_ADMIN_ID');
						$this->common_model->editData('uw_general_data',$param,'general_data_id',(int)$generaldataId);
						$this->session->set_flashdata('alert_success',lang('updatesuccess'));
					endif;
					redirect(correctLink('CMSGENERALDATA',$this->session->userdata('UW_ADMIN_CURRENT_PATH').$this->router->fetch_class().'/index'));
				endif;
		endif;
		
		$this->layouts->set_title('Edit General Data | CMS | UWINN');
		$this->layouts->admin_view('cms/generaldata/addeditdata',array(),$data);
	}	// END OF FUNCTION	

	/***********************************************************************
	** Function name 	: ImageDelete
	** Developed By 	: Tejaswi
	** Purpose  		: This function used to delete image
	** Date 			: 31 MARCH 2022
	** Updated 			: 
	************************************************************************/
	function ImageDelete()
	{  
		$imageName			=	$this->input->post('imageName');
		$id 				=	$this->input->post('id');
		//echo $id;die;
		$param['logo']		=	''; 
		if($imageName):
			$this->load->library("upload_crop_img");
			$return	=	$this->upload_crop_img->_delete_image(trim($imageName)); 
			$this->common_model->editData('uw_general_data',$param,'general_data_id',(int)$id);
		endif;
		$returnArray  		= 	array('status'=>1,'message'=>'Image deleted.');
		header('Content-type: application/json');
		echo json_encode($returnArray); die;
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name 	: videoDelete
	** Developed By 	: Tejaswi
	** Purpose  		: This function used to delete image
	** Date 			: 31 MARCH 2022
	** Updated 			: 
	************************************************************************/
	function videoDelete()
	{  
		$imageName				=	$this->input->post('imageName');
		$id 					=	$this->input->post('id');
		//echo $id;die;
		$param['seller_tutorial']		=	''; 
		if($imageName):
			$this->load->library("upload_crop_img");
			$return	=	$this->upload_crop_img->_delete_image(trim($imageName)); 
			$this->common_model->editData('uw_general_data',$param,'general_data_id',(int)$id);
		endif;
		$returnArray  		= 	array('status'=>1,'message'=>'Image deleted.');
		header('Content-type: application/json');
		echo json_encode($returnArray); die;
	}	// END OF FUNCTION
	
}
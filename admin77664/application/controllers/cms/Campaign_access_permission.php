<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Campaign_access_permission extends CI_Controller {

	public function  __construct() 
	{ 
		parent:: __construct();
		error_reporting(E_ALL ^ E_NOTICE);  
		$this->load->model(array('admin_model'));
		$this->lang->load('statictext', 'admin');
		$this->load->helper('common');
	} 
 
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name : index
	 + + Developed By  : Dilip Halder
	 + + Purpose  	   : This function used for index
	 + + Date 		   : 18 April 2024
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index()
	{		
		$data['error'] 				= 	'';
		$data['activeMenu'] 		= 	'cms';
		$data['activeSubMenu'] 		= 	'campaign_access_permission';
		$whereCon['where'] 			=   array( 'status'=> 'A');
		$data['EDITDATA']			=	$this->common_model->getData('single','uw_allowed_campaigns_permission',$whereCon);
		
		// product Details..
		$tblName 					=  'uw_products';
		$Fields 					=  array('title','products_id');
		$wcon['where'] 				=  array( 'stock'=> array('$gt'=> 0),'status' => 'A','remarks'=> 'lotto-products');
		$shortField 				=	array('seq_order' => 1);
		$data['productDetails']	    =  $this->common_model->getDataByNewQuery($Fields,'multiple',$tblName,$wcon,$shortField);

		$seleted_campaign = $this->input->post('seleted_campaign');
		$seleted_users    = $this->input->post('seleted_users');

		if($this->input->post('SaveChanges')):
			
			$param['seleted_campaign']		=  array_map('intval', $seleted_campaign);
			$param['seleted_users']			=  array_map('intval', $seleted_users);
		
			$error					=	'NO';
			$this->form_validation->set_rules('seleted_campaign[]', 'Seleted Campaign', 'trim|required');
			$this->form_validation->set_rules('seleted_users[]', 'Seleted Users', 'trim');

			if($this->form_validation->run() && $error == 'NO'): 
				$param['seleted_campaign']		=  array_map('intval', $seleted_campaign);
				$param['seleted_users']			=  array_map('intval', $seleted_users);

				if($this->input->post('CurrentDataID') ==''):
					$param['allowd_user_id']	=	(int)$this->common_model->getNextSequence('uw_allowed_campaigns_permission');
					$param['creation_ip']		=	currentIp();
					$param['creation_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
					$param['created_by']		=	(int)$this->session->userdata('UW_ADMIN_ID');
					$param['status']			=	'A';
					$alastInsertId				=	$this->common_model->addData('uw_allowed_campaigns_permission',$param);
					$this->session->set_flashdata('alert_success',lang('addsuccess'));
				else:
					$DATAID						=	$this->input->post('CurrentDataID');
					$param['update_ip']			=	currentIp();
					$param['update_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
					$param['updated_by']		=	(int)$this->session->userdata('UW_ADMIN_ID');
					
					$this->common_model->editData('uw_allowed_campaigns_permission',$param,'allowd_user_id',(int)$DATAID);
					$this->session->set_flashdata('alert_success',lang('updatesuccess'));
				endif;
				redirect(correctLink('CMSCAMPAIGNACCESSPERMISSION',getCurrentControllerPath('index')));
			endif;
		endif;
		
		
		$this->layouts->set_title('App Add/Edit Uwin Campaign Access Permission | CMS | UWINN');
		$this->layouts->admin_view('cms/campaign_access_permission/addeditdata',array(),$data);
	}	// END OF FUNCTION	
}
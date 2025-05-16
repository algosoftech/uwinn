<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Campaign_freezing extends CI_Controller {

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
	 + + Function name : addeditdata
	 + + Developed By  : Dilip Halder
	 + + Purpose  	   : This function used for Add Edit data
	 + + Date 		   : 10 February 2024
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index()
	{		
		$this->admin_model->authCheck('view_data');
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'uwin';
		$data['activeSubMenu'] 				= 	'Campaign_freezing';
		
		$tbl_name 		  = 'uw_campaign_freezing';
		$data['EDITDATA'] =	$this->common_model->getData('single',$tbl_name);
		if($this->input->post('SaveChanges')):
			$error					=	'NO';
			$this->form_validation->set_rules('campaign_freezing', 'Campaign Freezing', 'trim');
			if($this->form_validation->run() && $error == 'NO'): 
				$param['campaign_freezing'] 	 = 	stripslashes($this->input->post('campaign_freezing'));
				$param['freezing_title']         = 	stripslashes($this->input->post('freezing_title'));
				$param['auto_campaign_freezing'] = 	stripslashes($this->input->post('auto_campaign_freezing'));
				$param['Freezing_time_start']    = 	stripslashes($this->input->post('Freezing_time_start'));
				if($this->input->post('CurrentDataID') ==''):
					$param['campaign_id']	=	(int)$this->common_model->getNextSequence('uw_campaign_freezing');
					$param['creation_ip']		=	currentIp();
					$param['creation_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
					$param['created_by']		=	(int)$this->session->userdata('UW_ADMIN_ID');
					$param['status']			=	'A';
					$alastInsertId				=	$this->common_model->addData('uw_campaign_freezing',$param);
					$this->session->set_flashdata('alert_success',lang('addsuccess'));
				else:
					$DATAID						=	$this->input->post('CurrentDataID');
					$param['update_ip']			=	currentIp();
					$param['update_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
					$param['updated_by']		=	(int)$this->session->userdata('UW_ADMIN_ID');
					$this->common_model->editData('uw_campaign_freezing',$param,'campaign_id',(int)$DATAID);
					$this->session->set_flashdata('alert_success',lang('updatesuccess'));
				endif;
				redirect(correctLink('UWINNCAMPAIGNFREZING',getCurrentControllerPath('index')));
			endif;
		endif;
		
		$this->layouts->set_title('App- Enable/Disable Campaign Freezing | UWINN');
		$this->layouts->admin_view('uwin/campaign_freezing/addeditdata',array(),$data);
	}	// END OF FUNCTION	
}
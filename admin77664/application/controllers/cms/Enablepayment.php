<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Enablepayment extends CI_Controller {

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
	 + + Date 		   : 28 JULY 2021
	 + + Updated Date  : 
	 + + Updated By    :
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index()
	{		
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'cms';
		$data['activeSubMenu'] 				= 	'Enable Payment';
		
		$tbl_name = 'uw_paymentmode';
		$data['EDITDATA']				=	$this->common_model->getData('single',$tbl_name);
		

		if($this->input->post('SaveChanges')):
			$error					=	'NO';

			

			$this->form_validation->set_rules('stripe', 'Stripe', 'trim');
			$this->form_validation->set_rules('telr', 'Telr', 'trim');
			$this->form_validation->set_rules('noon', 'Noon', 'trim');
			$this->form_validation->set_rules('title_stripe', 'Stripe Title', 'trim');
			$this->form_validation->set_rules('title_telr', 'Telr Title', 'trim');
			$this->form_validation->set_rules('title_noon', 'Noon Title', 'trim');

			if($this->form_validation->run() && $error == 'NO'): 

				$param['stripe']				= 	stripslashes($this->input->post('stripe'));
				$param['telr']					= 	stripslashes($this->input->post('telr'));
				$param['noon']					= 	stripslashes($this->input->post('noon'));
				$param['title_stripe']			= 	stripslashes($this->input->post('title_stripe'));
				$param['title_telr']			= 	stripslashes($this->input->post('title_telr'));
				$param['title_noon']			= 	stripslashes($this->input->post('title_noon'));



				if($this->input->post('CurrentDataID') ==''):
					$param['paymentmode_id']			=	(int)$this->common_model->getNextSequence('uw_paymentmode');
					$param['creation_ip']		=	currentIp();
					$param['creation_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
					$param['created_by']		=	(int)$this->session->userdata('UW_ADMIN_ID');
					$param['status']			=	'A';
					$alastInsertId				=	$this->common_model->addData('uw_paymentmode',$param);
					$this->session->set_flashdata('alert_success',lang('addsuccess'));
				else:
					$aboutId					=	$this->input->post('CurrentDataID');
					$param['update_ip']			=	currentIp();
					$param['update_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
					$param['updated_by']		=	(int)$this->session->userdata('UW_ADMIN_ID');
					
					$this->common_model->editData('uw_paymentmode',$param,'paymentmode_id',(int)$aboutId);
					$this->session->set_flashdata('alert_success',lang('updatesuccess'));
				endif;
				redirect(correctLink('CMSENABLEPAYMENT',getCurrentControllerPath('index')));
			endif;
		endif;
		
		$this->layouts->set_title('Add/Edit enable payment | CMS | UWINN');
		$this->layouts->admin_view('cms/enablepayment/addeditdata',array(),$data);
	}	// END OF FUNCTION	
}
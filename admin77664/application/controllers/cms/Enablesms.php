<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Enablesms extends CI_Controller {

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
		$data['activeSubMenu'] 				= 	'Enable SMS';
		
		$tbl_name = 'uw_enablesms';
		$data['EDITDATA']				=	$this->common_model->getData('single',$tbl_name);
		
		if($this->input->post('SaveChanges')):
			$error					=	'NO';

			$this->form_validation->set_rules('digitizebird', 'Digitizebird', 'trim');
			$this->form_validation->set_rules('smscountry', 'Smscountry', 'trim');
			$this->form_validation->set_rules('sms_country_available_country', 'Country code', 'trim');
			$this->form_validation->set_rules('digitizebird_available_country', 'Country Code', 'trim');

			

			if($this->form_validation->run() && $error == 'NO'): 

				$param['digitizebird']				= 	stripslashes($this->input->post('digitizebird'));
				$param['smscountry']				= 	stripslashes($this->input->post('smscountry'));
				$param['sms_country_available_country']			= 	stripslashes($this->input->post('sms_country_available_country'));
				$param['digitizebird_available_country']			= 	stripslashes($this->input->post('digitizebird_available_country'));

				if($this->input->post('CurrentDataID') ==''):
					$param['enablesms_id']	=	(int)$this->common_model->getNextSequence('uw_enablesms');
					$param['creation_ip']		=	currentIp();
					$param['creation_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
					$param['created_by']		=	(int)$this->session->userdata('UW_ADMIN_ID');
					$param['status']			=	'A';
					$alastInsertId				=	$this->common_model->addData('uw_enablesms',$param);
					$this->session->set_flashdata('alert_success',lang('addsuccess'));
				else:
					$aboutId					=	$this->input->post('CurrentDataID');
					$param['update_ip']			=	currentIp();
					$param['update_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
					$param['updated_by']		=	(int)$this->session->userdata('UW_ADMIN_ID');
					
					$this->common_model->editData('uw_enablesms',$param,'enablesms_id',(int)$aboutId);
					$this->session->set_flashdata('alert_success',lang('updatesuccess'));
				endif;
				redirect(correctLink('CMSENABLEPAYMENT',getCurrentControllerPath('index')));
			endif;
		endif;
		
		$this->layouts->set_title('Add/Edit Enable SMS | CMS | UWINN');
		$this->layouts->admin_view('cms/enablesms/addeditdata',array(),$data);
	}	// END OF FUNCTION	
}
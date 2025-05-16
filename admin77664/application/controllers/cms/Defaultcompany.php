<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Defaultcompany extends CI_Controller {

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
		$data['activeSubMenu'] 				= 	'Quick Buy';
		
		$tbl_name = 'uw_default_company';
		$data['EDITDATA']				=	$this->common_model->getData('single',$tbl_name);
		
		if($this->input->post('SaveChanges')):
			$error					=	'NO';
			
			$this->form_validation->set_rules('enable_default_company', 'Defalut Company', 'trim|required');
			$this->form_validation->set_rules('first_company_name', 'First Company Name', 'trim|required');
			$this->form_validation->set_rules('first_company_address', 'First Company Address', 'trim|required');
			$this->form_validation->set_rules('second_company_name', 'Second Company Name', 'trim|required');
			$this->form_validation->set_rules('second_company_address', 'Second Company Address', 'trim|required');

			if($this->form_validation->run() && $error == 'NO'): 

				$param['enable_default_company']	= 	$this->input->post('enable_default_company');
				$param['first_company_name']		= 	$this->input->post('first_company_name');
				$param['first_company_address']		= 	$this->input->post('first_company_address');
				$param['second_company_name']		= 	$this->input->post('second_company_name');
				$param['second_company_address']	= 	$this->input->post('second_company_address');

				if($this->input->post('CurrentDataID') ==''):
					$param['default_company_id']		=	(int)$this->common_model->getNextSequence('uw_default_company');
					$param['creation_ip']		=	currentIp();
					$param['creation_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
					$param['created_by']		=	(int)$this->session->userdata('UW_ADMIN_ID');
					$param['status']			=	'A';
					$alastInsertId				=	$this->common_model->addData('uw_default_company',$param);
					$this->session->set_flashdata('alert_success',lang('addsuccess'));
				else:
					$aboutId					=	$this->input->post('CurrentDataID');
					$param['update_ip']			=	currentIp();
					$param['update_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
					$param['updated_by']		=	(int)$this->session->userdata('UW_ADMIN_ID');
					
					$this->common_model->editData('uw_default_company',$param,'default_company_id',(int)$aboutId);
					$this->session->set_flashdata('alert_success',lang('updatesuccess'));
				endif;
				redirect(correctLink('CMSDEFAULTCOMPANY',getCurrentControllerPath('index')));
			endif;
		endif;
		
		$this->layouts->set_title('Defaultcompany | CMS | UWINN');
		$this->layouts->admin_view('cms/defaultcompany/addeditdata',array(),$data);
	}	// END OF FUNCTION	
}
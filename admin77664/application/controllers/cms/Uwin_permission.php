<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Uwin_permission extends CI_Controller {

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
		
		$tbl_name = 'uw_uwin_allowed_user';
		$data['EDITDATA']				=	$this->common_model->getData('single',$tbl_name);
		

		if($this->input->post('SaveChanges')):
			$error					=	'NO';
			$this->form_validation->set_rules('uwin_permission', 'Uwin Permission', 'trim');
			$this->form_validation->set_rules('uwin_allowd_user_type[]', 'Select User Type', 'trim');

			if($this->form_validation->run() && $error == 'NO'): 

				$param['uwin_permission']				= 	stripslashes($this->input->post('uwin_permission'));
				$param['uwin_allowd_user_type']			= 	$this->input->post('uwin_allowd_user_type');
				
				if($this->input->post('CurrentDataID') ==''):
					$param['allowd_user_id']	=	(int)$this->common_model->getNextSequence('uw_uwin_allowed_user');
					$param['creation_ip']		=	currentIp();
					$param['creation_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
					$param['created_by']		=	(int)$this->session->userdata('UW_ADMIN_ID');
					$param['status']			=	'A';
					$alastInsertId				=	$this->common_model->addData('uw_uwin_allowed_user',$param);
					$this->session->set_flashdata('alert_success',lang('addsuccess'));
				else:
					$DATAID					=	$this->input->post('CurrentDataID');
					$param['update_ip']			=	currentIp();
					$param['update_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
					$param['updated_by']		=	(int)$this->session->userdata('UW_ADMIN_ID');
					
					$this->common_model->editData('uw_uwin_allowed_user',$param,'allowd_user_id',(int)$DATAID);
					$this->session->set_flashdata('alert_success',lang('updatesuccess'));
				endif;
				redirect(correctLink('CMSALLOWEDUSERS',getCurrentControllerPath('index')));
			endif;
		endif;
		
		$this->layouts->set_title('App Add/Edit Uwin Permission | CMS | UWINN');
		$this->layouts->admin_view('cms/uwin_permission/addeditdata',array(),$data);
	}	// END OF FUNCTION	
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Emailtemplates extends CI_Controller {

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
	 + + Updated Date 	: 
	 + + Updated By   	: 
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index()
	{	
		$this->admin_model->authCheck();
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'cms';
		$data['activeSubMenu'] 				= 	'emailtemplates';
		
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
				
		$whereCon['where']		 			= 	"";		
		$shortField 						= 	array('email_template_id'=>'ASC');
		
		$baseUrl 							= 	getCurrentControllerPath('index');
		$this->session->set_userdata('TGPEMAILTEMPLATEDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 	'uw_email_templates';
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

       if ($this->uri->segment(getUrlSegment())):
           //$page = $this->uri->segment(getUrlSegment());
       	$page = 0;
           
       else:
           $page = 0;
       endif;
		
		$data['forAction'] 					= 	$baseUrl; 
		if($totalRows):
			$first							=	($page)+1;
			$data['first']					=	$first;
			if($this->input->get('showLength') <> "All"):
			$last							=	((int)($page)+$data['perpage'])>$totalRows?$totalRows:((int)($page)+$data['perpage']);
			$data['noOfContent']			=	'Showing '.$first.'-'.$last.' of '.$totalRows.' items';
			else:
			$data['noOfContent']			=	'Showing All of '.$totalRows.' items';
			endif;
		else:
			$data['first']					=	1;
			$data['noOfContent']			=	'';
		endif;
		
		$data['ALLDATA'] 					= 	$this->common_model->getData('multiple',$tblName,$whereCon,$shortField,$perPage,$page); 
		//echo "<pre>";print_r($data['ALLDATA']);die;
		$this->layouts->set_title('Email Template | CMS | UWINN');
		$this->layouts->admin_view('cms/emailtemplates/index',array(),$data);
	}	// END OF FUNCTION

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name : addeditdata
	 + + Developed By  : Ashish Umrao
	 + + Purpose  	   : This function used for Add Edit data
	 + + Date 		   : 31 MRACH 2022
	 + + Updated Date  : 
	 + + Updated By    :
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function addeditdata($editId='')
	{		
		
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'cms';
		$data['activeSubMenu'] 				= 	'emailtemplates';
		
		if($editId):
			$this->admin_model->authCheck('edit_data');
			$data['EDITDATA']		=	$this->common_model->getDataByParticularField('uw_email_templates','email_template_id',(int)$editId);
		else:
			$this->admin_model->authCheck('add_data');
		endif;
		
		if($this->input->post('SaveChanges')): //echo "<pre>"; print_r($_POST); die;
			$error					=	'NO';
			$this->form_validation->set_rules('mail_type', 'Mail Type', 'trim|required|is_unique[uw_email_templates.mail_type]');
			$this->form_validation->set_rules('from_email', 'From Email', 'trim|required');
			$this->form_validation->set_rules('to_email', 'To Email', 'trim');
			$this->form_validation->set_rules('bcc_email', 'BCC', 'trim');
			$this->form_validation->set_rules('subject', 'Subject', 'trim|required');
			$this->form_validation->set_rules('mail_header', 'Mail Header', 'trim|required');
			$this->form_validation->set_rules('mail_body', 'Mail Body', 'trim|required');
			$this->form_validation->set_rules('mail_footer', 'Mail Footer', 'trim|required');
			$this->form_validation->set_rules('html', 'Html', 'trim|required');
			//$this->form_validation->set_rules('mail_type_data', 'Mail Type Data', 'trim|required');
			if($this->form_validation->run() && $error == 'NO'):  
			
				$param['mail_type']				= 	addslashes($this->input->post('mail_type'));
				$param['from_email']			= 	addslashes($this->input->post('from_email'));
				$param['to_email']				= 	addslashes($this->input->post('to_email'));
				$param['bcc_email']				= 	addslashes($this->input->post('bcc_email'));
				$param['subject']				= 	addslashes($this->input->post('subject'));
				$param['mail_header']			= 	addslashes($this->input->post('mail_header'));
				$param['mail_body']				= 	addslashes($this->input->post('mail_body'));
				$param['mail_footer']			= 	addslashes($this->input->post('mail_footer'));
				$param['html']					= 	addslashes($this->input->post('html'));
				//$param['mail_type_data']		= 	addslashes($this->input->post('mail_type_data'));
				
				if($this->input->post('CurrentDataID') ==''):
					$param['email_template_id']		=	(int)$this->common_model->getNextSequence('uw_email_templates');
					$param['creation_ip']		=	currentIp();
					$param['creation_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
					$param['created_by']		=	(int)$this->session->userdata('UW_ADMIN_ID');
					$param['status']			=	'A';
					//echo "<pre>"; print_r($param); die;
					$alastInsertId				=	$this->common_model->addData('uw_email_templates',$param);
					$this->session->set_flashdata('alert_success',lang('addsuccess'));
				else:
					$settingId					=	$this->input->post('CurrentDataID');
					$param['update_ip']			=	currentIp();
					$param['update_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
					$param['updated_by']		=	(int)$this->session->userdata('UW_ADMIN_ID');
					$this->common_model->editData('uw_email_templates',$param,'email_template_id',(int)$settingId);
					$this->session->set_flashdata('alert_success',lang('updatesuccess'));
				endif;
				
				redirect(correctLink('TGPEMAILTEMPLATEDATA',$this->session->userdata('UW_ADMIN_CURRENT_PATH').$this->router->fetch_class().'/index'));
			endif;
		endif;
		
		$this->layouts->set_title('Add/Edit Email Template | CMS | UWINN');
		$this->layouts->admin_view('cms/emailtemplates/addeditdata',array(),$data);

	}	// END OF FUNCTION	
	
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name : changestatus
	 + + Developed By : Ashish Umrao
	 + + Purpose  : This function used for change status
	 + + Date : 13 DECEMBER 2020
	 + + Updated Date :  
	 + + Updated By   :  
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	function changestatus($changeStatusId='',$statusType='')
	{  
		$this->admin_model->authCheck('edit_data');
		$param['status']		=	$statusType;
		$this->common_model->editData('uw_email_templates',$param,'email_template_id',(int)$changeStatusId);
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		
		redirect(correctLink('TGPEMAILTEMPLATEDATA',$this->session->userdata('UW_ADMIN_CURRENT_PATH').$this->router->fetch_class().'/index'));
	}

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name : deletedata
	 + + Developed By : Ashish Umrao
	 + + Purpose  : This function used for delete data
	 + + Date : 13 DECEMBER 2020
	 + + Updated Date :  
	 + + Updated By   :  
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	function deletedata($deleteId='')
	{  
		$this->admin_model->authCheck('delete_data');
		$this->common_model->deleteData('uw_email_templates','email_template_id',(int)$deleteId);
		$this->session->set_flashdata('alert_success',lang('deletesuccess'));
		
		redirect(correctLink('TGPEMAILTEMPLATEDATA',$this->session->userdata('UW_ADMIN_CURRENT_PATH').$this->router->fetch_class().'/index'));
	}
		
}
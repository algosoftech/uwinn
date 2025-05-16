<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Help extends CI_Controller {

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
 + + Developed By 	: Afsar Ali
 + + Purpose  		: This function used for index
 + + Date 			: 20 APRIL 2022
 + + Updated Date 	: 
 + + Updated By   	:
 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
public function index()
{	
	$this->admin_model->authCheck();
	$data['error'] 						= 	'';
	$data['activeMenu'] 				= 	'cms';
	$data['activeSubMenu'] 				= 	'help';
	
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
			
	$whereCon['where']		 			= 	array('page_name'=>'Help');	
	$shortField 						= 	array('title_name'=>'ASC');
	
	$baseUrl 							= 	getCurrentControllerPath('index');
	$this->session->set_userdata('CMSHELP',currentFullUrl());
	$qStringdata						=	explode('?',currentFullUrl());
	$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
	$tblName 							= 	'uw_cms';
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

   if($this->uri->segment(getUrlSegment())):
       $page = $this->uri->segment(getUrlSegment());
   else:
       $page = 0;
   endif;
	
	$data['forAction'] 					= 	$baseUrl; 
	if($totalRows):
		$first							=	(int)($page)+1;
		$data['first']					=	$first;
		
		if($data['perpage'] == 'All'):
			$pageData 					=	$totalRows;
		else:
			$pageData 					=	$data['perpage'];
		endif;
		
		$last							=	((int)($page)+$pageData)>$totalRows?$totalRows:((int)($page)+$pageData);
		$data['noOfContent']			=	'Showing '.$first.'-'.$last.' of '.$totalRows.' items';
	else:
		$data['first']					=	1;
		$data['noOfContent']			=	'';
	endif;
	
	$data['ALLDATA'] 					= 	$this->common_model->getData('multiple',$tblName,$whereCon,$shortField,$perPage,$page);

	//print_r($data['ALLDATA']); die();

	$this->layouts->set_title('Help | CMS | UWINN');
	$this->layouts->admin_view('cms/help/index',array(),$data);
}	// END OF FUNCTION

/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 + + Function name : addeditdata
 + + Developed By  : Afsar Ali
 + + Purpose  	   : This function used for Add Edit data
 + + Date 		   : 20 APRIl 2021
 + + Updated Date  : 
 + + Updated By    :
 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
public function addeditdata($editId='')
{		
	$data['error'] 						= 	'';
	$data['activeMenu'] 				= 	'cms';
	$data['activeSubMenu'] 				= 	'help';
	
	if($editId):
		$this->admin_model->authCheck('edit_data');
		$data['EDITDATA']				=	$this->common_model->getDataByParticularField('uw_cms','help_id',(int)$editId);
	else:
		$this->admin_model->authCheck('add_data');
	endif;
	
	if($this->input->post('SaveChanges')):
		$error					=	'NO';

		$this->form_validation->set_rules('question', 'Question', 'trim|required');
		$this->form_validation->set_rules('answer', 'Answer', 'trim|required');

		if($this->form_validation->run() && $error == 'NO'): 

			$param['page_name']			= 	'Help';
			$param['question']			= 	stripslashes($this->input->post('question'));
			$param['answer']			= 	stripslashes($this->input->post('answer'));

			//echo "<pre>";print_r($param);die;
			if($this->input->post('CurrentDataID') ==''):
				$param['help_id']			=	(int)$this->common_model->getNextSequence('uw_cms');
				$param['creation_ip']		=	currentIp();
				$param['creation_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
				$param['created_by']		=	(int)$this->session->userdata('UW_ADMIN_ID');
				$param['status']			=	'A';
				$alastInsertId				=	$this->common_model->addData('uw_cms',$param);
				$this->session->set_flashdata('alert_success',lang('addsuccess'));
			else:
				$aboutId					=	$this->input->post('CurrentDataID');
				$param['update_ip']			=	currentIp();
				$param['update_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
				$param['updated_by']		=	(int)$this->session->userdata('UW_ADMIN_ID');
				$this->common_model->editData('uw_cms',$param,'help_id',(int)$aboutId);
				$this->session->set_flashdata('alert_success',lang('updatesuccess'));
			endif;
			redirect(correctLink('CMSHELP',getCurrentControllerPath('index')));
		endif;
	endif;
	
	$this->layouts->set_title('Add/Edit About Us | CMS | UWINN');
	$this->layouts->admin_view('cms/help/addeditdata',array(),$data);
}	// END OF FUNCTION

}
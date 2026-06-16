<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Designation extends CI_Controller {

	public function  __construct() 
	{ 
		parent:: __construct();
		$this->load->model(array('admin_model','emailtemplate_model','sms_model','notification_model'));
		$this->lang->load('statictext', 'admin');
		$this->load->helper('common');
	} 

	/* * *********************************************************************
	 * * Function name : designation
	 * * Developed By : Manoj Kumar
	 * * Purpose  : This function used for designation
	 * * Date : 14 JUNE 2021
	 * * **********************************************************************/
	public function index()
	{	
		$this->admin_model->authCheck('view_data');
		$data 								= 	array();
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'subadmin';
		$data['activeSubMenu'] 				= 	'designation';
		$data['searchField']				=	'';
		$data['searchValue']				=	'';
		$whereCon							=	array();
		$this->admin_model->getPermissionType($data);
		
		if($this->input->get('searchField') && $this->input->get('searchValue')):
			$sField							=	$this->input->get('searchField');
			$sValue							=	$this->input->get('searchValue');
			$whereCon['like']			 	= 	array('0'=>trim($sField),'1'=>trim($sValue));
			$data['searchField'] 			= 	$sField;
			$data['searchValue'] 			= 	$sValue;
		endif;
		
		$shortField 						= 	array('designation_name'=>'ASC');
		
		$baseUrl 							= 	getCurrentControllerPath('index');
		$this->session->set_userdata('designationAdminCMPOPData',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	(!empty($qStringdata[1])) ? '?'.$qStringdata[1] : '';
		$tblName 							= 	'uw_admin_designation';
		$con 								= 	'';
		$totalRows 							= 	$this->common_model->getData('count',$tblName,$whereCon,$shortField,'0','0');
		
		if($this->input->get('showLength') == 'All'):
			$perPage	 					= 	$totalRows > 0 ? $totalRows : SHOW_NO_OF_DATA;
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
		
		$allData 							= 	$this->common_model->getData('multiple',$tblName,$whereCon,$shortField,$perPage,$page);
		$data['ALLDATA'] 					= 	is_array($allData) ? $allData : array();

		$this->layouts->set_title('Designation | Sub Admin | UWINN');
		$this->layouts->admin_view('subadmin/designation/index',array(),$data);
	}	// END OF FUNCTION
	
	/* * *********************************************************************
	 * * Function name : addeditdata
	 * * Developed By : Manoj Kumar
	 * * Purpose  : This function used for add edit data
	 * * Date : 14 JUNE 2021
	 * * **********************************************************************/
	public function addeditdata($editId='')
	{		
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'subadmin';
		$data['activeSubMenu'] 				= 	'designation';
		
		if($editId):
			$this->admin_model->authCheck('edit_data');
			$data['EDITDATA']		=	$this->common_model->getDataByParticularField('uw_admin_designation','designation_id',(int)$editId);
		else:
			$this->admin_model->authCheck('add_data');
		endif;
		
		if($this->input->post('SaveChanges')):
			$error					=	'NO';
			$this->form_validation->set_rules('designation_name', 'Name', 'trim|required|is_unique[uw_admin_designation.designation_name.string]');
			
			if($this->form_validation->run() && $error == 'NO'):   
			
				$param['designation_name']		= 	addslashes($this->input->post('designation_name'));
				$param['designation_slug']		= 	url_title(strtolower($this->input->post('designation_name')));
				
				if($this->input->post('CurrentDataID') ==''):
					$param['designation_id']	=	(int)$this->common_model->getNextSequence('uw_admin_designation');
					$param['designation_used']	=	'N';
					$param['creation_ip']		=	currentIp();
					$param['creation_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
					$param['created_by']		=	(int)$this->session->userdata('UW_ADMIN_ID');
					$param['status']			=	'A';
					$alastInsertId				=	$this->common_model->addData('uw_admin_designation',$param);
					$this->session->set_flashdata('alert_success',lang('addsuccess'));
				else:
					$departmentId				=	$this->input->post('CurrentDataID');
					$param['update_ip']			=	currentIp();
					$param['update_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
					$param['updated_by']		=	(int)$this->session->userdata('UW_ADMIN_ID');
					$this->common_model->editData('uw_admin_designation',$param,'designation_id',(int)$departmentId);
					$this->session->set_flashdata('alert_success',lang('updatesuccess'));
				endif;
				
				redirect(correctLink('designationAdminCMPOPData',getCurrentControllerPath('index')));
			endif;
		endif;
		
		$this->layouts->set_title('Add/Edit Designation | Sub Admin | UWINN');
		$this->layouts->admin_view('subadmin/designation/addeditdata',array(),$data);
	}	// END OF FUNCTION	
	
	/***********************************************************************
	** Function name : changestatus
	** Developed By : Manoj Kumar
	** Purpose  : This function used for change status
	** Date : 14 JUNE 2021
	************************************************************************/
	function changestatus($changeStatusId='',$statusType='')
	{  
		$this->admin_model->authCheck('edit_data');
		$param['status']		=	$statusType;
		$this->common_model->editData('uw_admin_designation',$param,'designation_id',(int)$changeStatusId);
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		
		redirect(correctLink('designationAdminCMPOPData',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name : deletedata
	** Developed By : Manoj Kumar
	** Purpose  : This function used for delete data
	** Date : 14 JUNE 2021
	************************************************************************/
	function deletedata($deleteId='')
	{  
		$this->admin_model->authCheck('delete_data');
		$this->common_model->deleteData('uw_admin_designation','designation_id',(int)$deleteId);
		$this->session->set_flashdata('alert_success',lang('deletesuccess'));
		
		redirect(correctLink('designationAdminCMPOPData',getCurrentControllerPath('index')));
	}
}
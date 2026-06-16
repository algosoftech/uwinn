<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Department extends CI_Controller {

	public function  __construct() 
	{ 
		parent:: __construct();
		$this->load->model(array('admin_model','emailtemplate_model','sms_model','notification_model'));
		$this->lang->load('statictext', 'admin');
		$this->load->helper('common');
	} 

	/* * *********************************************************************
	 * * Function name : department
	 * * Developed By : Manoj Kumar
	 * * Purpose  : This function used for department
	 * * Date : 14 JUNE 2021
	 * * **********************************************************************/
	public function index()
	{	
		$this->admin_model->authCheck('view_data');
		$data 								= 	array();
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'subadmin';
		$data['activeSubMenu'] 				= 	'department';
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
		
		$shortField 						= 	array('department_name'=>'ASC');
		
		$baseUrl 							= 	getCurrentControllerPath('index');
		$this->session->set_userdata('subadminDepartmentCMPOPData',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	(!empty($qStringdata[1])) ? '?'.$qStringdata[1] : '';
		$tblName 							= 	'uw_admin_department';
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

		$this->layouts->set_title('Department | Sub Admin | UWINN');
		$this->layouts->admin_view('subadmin/department/index',array(),$data);
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
		$data['activeSubMenu'] 				= 	'department';
		
		if($editId):
			$this->admin_model->authCheck('edit_data');
			$data['EDITDATA']		=	$this->common_model->getDataByParticularField('uw_admin_department','department_id',(int)$editId);
		else:
			$this->admin_model->authCheck('add_data');
		endif;
		
		if($this->input->post('SaveChanges')):
			$error					=	'NO';
			$this->form_validation->set_rules('department_name', 'Department Name', 'trim|required|is_unique[uw_admin_department.department_name.string]');
			
			if($this->form_validation->run() && $error == 'NO'):   
			
				$param['department_name']	= 	addslashes($this->input->post('department_name'));
				$param['department_slug']	= 	url_title(strtolower($this->input->post('department_name')));
				
				if($this->input->post('CurrentDataID') ==''):
					$param['department_id']		=	(int)$this->common_model->getNextSequence('uw_admin_department');
					$param['department_used']	=	'N';
					$param['creation_ip']		=	currentIp();
					$param['creation_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
					$param['created_by']		=	(int)$this->session->userdata('UW_ADMIN_ID');
					$param['status']			=	'A';
					$alastInsertId				=	$this->common_model->addData('uw_admin_department',$param);
					$this->session->set_flashdata('alert_success',lang('addsuccess'));
				else:
					$departId					=	$this->input->post('CurrentDataID');
					$param['update_ip']			=	currentIp();
					$param['update_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
					$param['updated_by']		=	(int)$this->session->userdata('UW_ADMIN_ID');
					$this->common_model->editData('uw_admin_department',$param,'department_id',(int)$departId);
					$this->session->set_flashdata('alert_success',lang('updatesuccess'));
				endif;
				
				redirect(correctLink('subadminDepartmentCMPOPData',getCurrentControllerPath('index')));
			endif;
		endif;
		
		$this->layouts->set_title('Add/Edit Department | Sub Admin | UWINN');
		$this->layouts->admin_view('subadmin/department/addeditdata',array(),$data);
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
		$this->common_model->editData('uw_admin_department',$param,'department_id',(int)$changeStatusId);
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		
		redirect(correctLink('subadminDepartmentCMPOPData',getCurrentControllerPath('index')));
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
		$this->common_model->deleteData('uw_admin_department','department_id',(int)$deleteId);
		$this->session->set_flashdata('alert_success',lang('deletesuccess'));
		
		redirect(correctLink('subadminDepartmentCMPOPData',getCurrentControllerPath('index')));
	}
}
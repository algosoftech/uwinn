<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Allsubcategory extends CI_Controller {

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
	 + + Developed By 	: Dilip Halder
	 + + Purpose  		: This function used for index
	 + + Date 			: 04 April 2022
	 + + Updated Date 	: 
	 + + Updated By   	:
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index()
	{	
		$this->admin_model->authCheck();
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'category';
		$data['activeSubMenu'] 				= 	'subcategory';
		
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
				
		$whereCon['where']		 			= 	'';		
		$shortField 						= 	array('sub_category_name'=>'ASC');
		
		$baseUrl 							= 	getCurrentControllerPath('index');
		$this->session->set_userdata('ALLSUBCATEGORYDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 	'uw_sub_category';
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

		$this->layouts->set_title('Sub Category | Sub Category | UWINN');
		$this->layouts->admin_view('category/subcategory/index',array(),$data);
	}	// END OF FUNCTION

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name : addeditdata
	 + + Developed By  : AFSAR ALI
	 + + Purpose  	   : This function used for Add Edit data
	 + + Date 		   : 04 APRIL 2022
	 + + Updated Date  : 
	 + + Updated By    :
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function addeditdata($editId='')
	{		
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'shopping';
		$data['activeSubMenu'] 				= 	'subcategory';
		
		if($editId):
			$this->admin_model->authCheck('edit_data');
			$data['EDITDATA']				=	$this->common_model->getDataByParticularField('uw_sub_category','sub_category_id',(int)$editId);
		else:
			$this->admin_model->authCheck('add_data');
		endif;
		
		if($this->input->post('SaveChanges')):
			$error					=	'NO';
			$this->form_validation->set_rules('sub_category', 'Sub Category', 'trim');
			if (empty($_FILES['sub_cat_image']['name'])):
			    $this->form_validation->set_rules('sub_cat_image', 'Image', 'trim');
			endif;
			$this->form_validation->set_rules('sub_cat_image_alt', 'Alt', 'trim');
			

			if($this->form_validation->run() && $error == 'NO'): 

				$categoryData						=	explode('_____',$this->input->post('category_id'));
				$param['category_id']			= 	(int)$categoryData[0];
				$param['category_name']		= 	addslashes($categoryData[1]);
				$param['category_slug']		= 	url_title(strtolower($param['category_name']));
				
				$param['sub_category']	= 	addslashes($this->input->post('sub_category'));
				$param['sub_category_slug']	= 	url_title(strtolower($param['sub_category']));

				if($_FILES['sub_cat_image']['name']):
					$ufileName						= 	$_FILES['sub_cat_image']['name'];
					$utmpName						= 	$_FILES['sub_cat_image']['tmp_name'];
					$ufileExt         				= 	pathinfo($ufileName);
					$unewFileName 					= 	$this->common_model->microseconds().'.'.$ufileExt['extension'];
					$this->load->library("upload_crop_img");
					$uimageLink						=	$this->upload_crop_img->_upload_image($ufileName,$utmpName,'subCategoryImage',$unewFileName,'');
					if($uimageLink != 'UPLODEERROR'):
						$param['sub_cat_image']		= 	$uimageLink;
					endif;
				endif;
				$param['sub_cat_image_alt']	= 	addslashes($this->input->post('sub_cat_image_alt'));

				//echo '<pre>';print_r($param);die;
				if($this->input->post('CurrentDataID') ==''):
					$param['sub_category_id']	=	(int)$this->common_model->getNextSequence('uw_sub_category');
					
					$param['creation_ip']			=	currentIp();
					$param['creation_date']			=	(int)$this->timezone->utc_time();//currentDateTime();
					$param['created_by']			=	(int)$this->session->userdata('SCHW_ADMIN_ID');
					$param['status']				=	'A';

					$check 		= array();
					$tblName 	= 'uw_sub_category';
					$whereCon 	= ['category_name' => $param['category_name'],
									'sub_category' => $param['sub_category']
									];
					//print_r($whereCon); die();

					$check 	= 	$this->common_model->checkDuplicate($tblName,$whereCon);
					if($check == 0):
						$alastInsertId					=	$this->common_model->addData('uw_sub_category',$param);
					$this->session->set_flashdata('alert_success',lang('addsuccess'));
					else:
						$this->session->set_flashdata('alert_error','Already Exist!');
					endif;


				else:
					$subCategoryId					=	$this->input->post('CurrentDataID');
					$param['update_ip']				=	currentIp();
					$param['update_date']			=	(int)$this->timezone->utc_time();//currentDateTime();
					$param['updated_by']			=	(int)$this->session->userdata('SCHW_ADMIN_ID');
					$this->common_model->editData('uw_sub_category',$param,'sub_category_id',(int)$subCategoryId);
					$this->session->set_flashdata('alert_success',lang('updatesuccess'));
				endif;

				redirect(correctLink('ALLSUBCATEGORYDATA',getCurrentControllerPath('index')));
			endif;
		endif;
		
		$this->layouts->set_title('Add/Edit Sub Category | UWINN');
		$this->layouts->admin_view('category/subcategory/addeditdata',array(),$data);
	}	// END OF FUNCTION	

	/***********************************************************************
	** Function name : changestatus
	** Developed By : Dilip Halder
	** Purpose  : This function used for change status
	** Date : 21 JUNE 2021
	************************************************************************/
	function changestatus($changeStatusId='',$statusType='')
	{  
		$this->admin_model->authCheck('edit_data');
		$param['status']		=	$statusType;
		$this->common_model->editData('uw_sub_category',$param,'sub_category_id',(int)$changeStatusId);
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		
		redirect(correctLink('ALLSUBCATEGORYDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name : deletedata
	** Developed By : Dilip Halder
	** Purpose  : This function used for delete data
	** Date : 21 JUNE 2021
	************************************************************************/
	function deletedata($deleteId='')
	{  
		$this->admin_model->authCheck('delete_data');
		$this->common_model->deleteData('uw_sub_category','sub_category_id',(int)$deleteId);
		$this->session->set_flashdata('alert_success',lang('deletesuccess'));
		
		redirect(correctLink('ALLSUBCATEGORYDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name 	: imageDelete
	** Developed By 	: Ravi
	** Purpose  		: This function used to delete image
	** Date 			: 23 SEP 2021
	************************************************************************/
	function imageDelete()
	{  
		$imageName			=	$this->input->post('imageName');
		$id 				=	$this->input->post('id');
		$typ 				=	$this->input->post('typ');
		//echo $typ;die;
		if($typ == 'web'):
			$param['sub_cat_image']		=	''; 
		elseif($typ == 'app'):
			$param['shop_cat_detail_image']		=	''; 
		endif;

		if($imageName):
			$this->load->library("upload_crop_img");
			$return	=	$this->upload_crop_img->_delete_image(trim($imageName)); 
			$this->common_model->editData('uw_sub_category',$param,'sub_category_id',(int)$id);
		endif;
		if($typ == 'web'):
			$returnArray  		= 	array('status'=>1,'message'=>'Image deleted.');
		elseif($typ == 'app'):
			$returnArray  		= 	array('status'=>2,'message'=>'Image deleted.');
		endif;
		header('Content-type: application/json');
		echo json_encode($returnArray); die;
	}	// END OF FUNCTION
}
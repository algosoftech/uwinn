<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Testimonials extends CI_Controller {

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
	 + + Function name 	: 	index
	 + + Developed By 	:	Dilip Kumar
	 + + Purpose  		: 	This function used to show testimonial list.
	 + + Date 			:	25 October 2023
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index()
	{	
		$this->admin_model->authCheck();
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'cms';
		$data['activeSubMenu'] 				= 	'testimonials';
		
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
				
		$whereCon['where']		 			= 	array('page_name'=>'testimonials');	
		$shortField 						= 	array('title_name'=>'ASC');
		
		$baseUrl 							= 	getCurrentControllerPath('index');
		$this->session->set_userdata('CMSTESTIMONIALS',currentFullUrl());
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

		$this->layouts->set_title('Term & Conditions | CMS | UWINN');
		$this->layouts->admin_view('cms/testimonials/index',array(),$data);
	}	// END OF FUNCTION
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name : addeditdata
	 + + Developed By  : Dilip Halder
	 + + Purpose  	   : This function used for Add Edit data
	 + + Date 		   : 25 October 2023
	 + + Updated Date  : 
	 + + Updated By    :
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function addeditdata($editId='')
	{		
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'cms';
		$data['activeSubMenu'] 				= 	'testimonials';
		
		if($editId):
			$this->admin_model->authCheck('edit_data');
			$data['EDITDATA']				=	$this->common_model->getDataByParticularField('uw_cms','testimonial_id',(int)$editId);
		else:
			$this->admin_model->authCheck('add_data');
		endif;

		if($this->input->post('SaveChanges')):
		    $error					=	'NO';

		    // $this->form_validation->set_rules('title', 'Title', 'trim|required');
			// $this->form_validation->set_rules('description', 'Description', 'trim|required');
			// $this->form_validation->set_rules('ratings', 'Rating', 'trim');

			 $this->form_validation->set_rules('image', 'Image', 'trim|required');
		     // $this->form_validation->set_rules('image_alt', 'Image ALT TEXT', 'trim|required');

			$param['page_name']				= 	'testimonials';
			// $param['title']					= 	$this->input->post('title');
			// $param['description']			= 	$this->input->post('description');
			// $param['ratings']				= 	$this->input->post('ratings');

			// if($this->input->post('image_alt')):
			// 	$param['image_alt']			= 	$this->input->post('image_alt');
			// endif;

			if(!empty($_FILES['image']['name'])):
				// $ufileName						= 	$_FILES['image']['name'];
				$ufileName						= 	str_replace(" ","_",$_FILES['image']['name']);
				$utmpName						= 	$_FILES['image']['tmp_name'];
				$ufileExt         				= 	pathinfo($ufileName);
				// $unewFileName 					= 	$this->common_model->microseconds().'.'.$ufileExt['extension'];

				$unewFileName 					= 	$_FILES['image']['name'];
				$filePath =  fileFCPATH .'assets/testimonialImage/'.$_FILES['image']['name'];

				if(file_exists($filePath)):
					$unewFileName 				=	$ufileExt['filename'] .'_'.$this->common_model->random_strings(8).'.'.$ufileExt['extension'];
				endif;

				$this->load->library("upload_crop_img");
				$uimageLink						=	$this->upload_crop_img->_upload_image($ufileName,$utmpName,'testimonialImage',$unewFileName,'');
				if($uimageLink != 'UPLODEERROR'):
					$param['image']		= 	$uimageLink;
					$param['image_alt']		= 	$this->input->post('image_alt');
				endif;
			endif;

			if($this->input->post('CurrentDataID') ==''):
				$param['testimonial_id']    =   (int)$this->common_model->getNextSequence('testimonial_id');
				$param['creation_ip']		=	currentIp();
				$param['creation_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
				$param['created_by']		=	(int)$this->session->userdata('HCAP_ADMIN_ID');
				$param['status']			=	'A';
				$alastInsertId				=	$this->common_model->addData('uw_cms',$param);
				$this->session->set_flashdata('alert_success',lang('addsuccess'));
			else:
				$SEQId						=	$this->input->post('CurrentDataID');
				$param['update_ip']			=	currentIp();
				$param['update_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
				$param['updated_by']		=	(int)$this->session->userdata('HCAP_ADMIN_ID');
				$this->common_model->editData('uw_cms',$param,'testimonial_id',(int)$SEQId);
				$this->session->set_flashdata('alert_success',lang('updatesuccess'));
			endif;
			redirect(correctLink('CMSTESTIMONIALS',getCurrentControllerPath('index')));
		endif;
		
		$this->layouts->set_title('Add/Edit Terms & conditions | CMS | UWINN');
		$this->layouts->admin_view('cms/testimonials/addeditdata',array(),$data);
	}	// END OF FUNCTION	


	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name : changestatus
	 + + Developed By : Dilip Halder
	 + + Purpose  : This function used for change status
	 + + Date : 04 October 2023
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	function changestatus($changeStatusId='',$statusType='')
	{  
		$this->admin_model->authCheck('edit_data');
		$param['status']		=	$statusType;
		$this->common_model->editData('uw_cms',$param,'testimonial_id',(int)$changeStatusId);
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		
		redirect(correctLink('CMSTESTIMONIALS',$this->session->userdata('HCAP_ADMIN_CURRENT_PATH').$this->router->fetch_class().'/index'));
	}

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name : deletedata
	 + + Developed By : Dilip Halder
	 + + Purpose  : This function used for Delete Data
	 + + Date : 04 October 2023
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	function deletedata($deleteId='')
	{  	

		if($deleteId):
			$this->admin_model->authCheck('edit_data');
			$data			=	$this->common_model->getDataByParticularField('uw_cms','testimonial_id',(int)$deleteId);
			$imageName = $data['image'];
			$this->load->library("upload_crop_img");
			$this->upload_crop_img->_delete_image(trim($imageName)); 
		endif;

		$this->admin_model->authCheck('delete_data');
		$this->common_model->deleteData('uw_cms','testimonial_id',(int)$deleteId);
		
		$this->session->set_flashdata('alert_success',lang('deletesuccess'));
		
		redirect(correctLink('CMSTESTIMONIALS',$this->session->userdata('HCAP_ADMIN_CURRENT_PATH').$this->router->fetch_class().'/index'));
	}	


	/***********************************************************************
	** Function name 	: imageDelete
	** Developed By 	: Dilip	 Halder
	** Purpose  		: This function used to delete image
	** Date 			: 04 October 2023
	************************************************************************/
	function imageDelete()
	{  
		$imageName			=	$this->input->post('imageName');
		$id 				=	$this->input->post('id');
		$typ 				=	$this->input->post('typ');
		//echo $typ;die;
		if($typ == 'web'):
			$param['image']		=	''; 
		elseif($typ == 'app'):
			$param['image']		=	''; 
		endif;

		if($imageName):
			$this->load->library("upload_crop_img");
			$return	=	$this->upload_crop_img->_delete_image(trim($imageName)); 
			$this->common_model->editData('uw_cms',$param,'testimonial_id',(int)$id);
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
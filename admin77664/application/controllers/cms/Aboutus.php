<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Aboutus extends CI_Controller {

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
	 + + Date 			: 26 march 2024
	 + + Updated Date 	: 
	 + + Updated By   	:
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index()
	{	
		$this->admin_model->authCheck();
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'cms';
		$data['activeSubMenu'] 				= 	'aboutus';
		
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
				
		$whereCon['where']		 			= 	array('page_name'=>'About Us');	
		$shortField 						= 	array('title_name'=>'ASC');
		
		$baseUrl 							= 	getCurrentControllerPath('index');
		$this->session->set_userdata('CMSABOUTUD',currentFullUrl());
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

		$this->layouts->set_title('About us | CMS | UWINN');
		$this->layouts->admin_view('cms/aboutus/index',array(),$data);
	}	// END OF FUNCTION

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name : addeditdata
	 + + Developed By  : Dilip Halder
	 + + Purpose  	   : This function used for Add Edit data
	 + + Date 		   : 26 March 2024
	 + + Updated Date  : 
	 + + Updated By    :
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
 	public function addeditdata($editId='')
	{		
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'cms';
		$data['activeSubMenu'] 				= 	'aboutus';
		
		if($editId):
			$this->admin_model->authCheck('edit_data');
			$data['EDITDATA']				=	$this->common_model->getDataByParticularField('uw_cms','about_id',(int)$editId);
		else:
			$this->admin_model->authCheck('add_data');
		endif;
		// echo "<pre>"; print_r($data);die();

		
		if($this->input->post('SaveChanges')):
			// echo "<pre>";print_r($_POST);die();
			$error	=	'NO';
			$titles 	  = $this->input->post('title');
			$descriptions = $this->input->post('description');
			$oldImage 	  = $this->input->post('old_image');
			$images 	  = $_FILES['image'];
			// echo "<pre>";print_r($images);die();

			$this->form_validation->set_rules('title[]', 'Title',  'trim');
			$this->form_validation->set_rules('description[]', 'Description', 'trim|required');
			if($this->form_validation->run() && $error == 'NO'): 
				$sections = array();
				foreach ($titles as $key => $title):
					$section = array();
					if($images['name'][$key]):
						$ufileName    = 	$images['name'][$key];
						$utmpName	  = 	$images['tmp_name'][$key];
						$ufileExt     = 	pathinfo($ufileName);
						$unewFileName = $this->common_model->random_strings(8).'.'.$ufileExt['extension'];
						$this->load->library("upload_crop_img");
						$uimageLink	  =	$this->upload_crop_img->_upload_image($ufileName, $utmpName, 'homepageSliderImage', $unewFileName, '');
						if($uimageLink != 'UPLODEERROR'):
							$imageName = $data['EDITDATA']['sections'][$key]->image;
							if($imageName):
								$this->load->library("upload_crop_img");
								$this->upload_crop_img->_delete_image(trim($imageName)); 	
							endif;
							$section['image']		= 	$uimageLink;
						else:
							$section['image']		= 	'';
						endif;
					else:

						$imageName = $data['EDITDATA']['sections'][$key]->image;
						if($oldImage[$key] == "" && ($data['EDITDATA']['sections'][$key]->image != '' )):
							$this->load->library("upload_crop_img");
							$this->upload_crop_img->_delete_image(trim($imageName)); 	
						endif;
						$section['image']   =   $oldImage[$key];
						
					endif;
					$section['title'] 	    = stripslashes($title);
					$section['description'] = stripslashes($descriptions[$key]);
					$sections[] 		    = $section;
				endforeach;
				$param['page_name'] = 'About Us';
				$param['sections'] = $sections;
			    // echo "<pre>";print_r($param);die();
				if($this->input->post('CurrentDataID') ==''):
					$param['about_id'] = (int)$this->common_model->getNextSequence('uw_cms');
					$param['creation_ip'] = currentIp();
					$param['creation_date'] = (int)$this->timezone->utc_time();
					$param['created_by'] = (int)$this->session->userdata('UW_ADMIN_ID');
					$param['status'] = 'A';
					$alastInsertId = $this->common_model->addData('uw_cms', $param);
					$this->session->set_flashdata('alert_success', lang('addsuccess'));
				else:
					$aboutId = $this->input->post('CurrentDataID');
					$param['update_ip'] = currentIp();
					$param['update_date'] = (int)$this->timezone->utc_time();
					$param['updated_by'] = (int)$this->session->userdata('UW_ADMIN_ID');
					$this->common_model->editData('uw_cms', $param, 'about_id', (int)$aboutId);
					$this->session->set_flashdata('alert_success', lang('updatesuccess'));
				endif;

				redirect(correctLink('CMSABOUTUD', getCurrentControllerPath('index')));
			endif;
		endif;
		
		$this->layouts->set_title('Add/Edit About Us | CMS | UWINN');
		$this->layouts->admin_view('cms/aboutus/addeditdata', array(), $data);
	}	


}
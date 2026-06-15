<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Allcontents extends CI_Controller {

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
	 + + Purpose  		: 	This function used to show U Win Winner's list.
	 + + Date 			:	29 January 2024
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index()
	{	
		$this->admin_model->authCheck('view_data');
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'uwin';
		$data['activeSubMenu'] 				= 	'allcontents';
		
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
		
		$whereCon['where']		 			= 	array();	
		$shortField 						= 	array('position'=> 1);
		
		$baseUrl 							= 	getCurrentControllerPath('index');
		$this->session->set_userdata('CMSCONTENTS',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 	'uw_contents';
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
		// echo "<pre>"; print_r($whereCon);die();

		$this->layouts->set_title('All Contents | UWINN');
		$this->layouts->admin_view('uwin/allcontents/index',array(),$data);
	}	// END OF FUNCTION
	
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name : addeditdata
	 + + Developed By  : Dilip Halder
	 + + Purpose  	   : This function used for Add Edit data
	 + + Date 		   : 29 January 2024
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function addeditdata($editId='')
	{		
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'uwin';
		$data['activeSubMenu'] 				= 	'allcontents';
	 	
		
		$data['EDITDATA'] = array(
			'content_id' => '',
			'upload_type' => '',
			'position' => '',
			'new_position' => '',
			'live_date_time' => '',
			'live_date_time_later' => '',
			'image' => '',
			'video' => '',
			'link_thumbnail' => '',
			'link_url' => '',
			'link_title' => '',
			'game_type' => '',
			'added_for' => array(),
			'added_for_top_banner' => array(),
			'added_for_recent_winners' => array(),
			'added_for_result_page' => array(),
			'added_for_winner_gallery' => array(),
		);

		if($editId):
			$this->admin_model->authCheck('edit_data');
			$data['EDITDATA'] = $this->common_model->getDataByParticularField('uw_contents', '_id', new MongoDB\BSON\ObjectId($editId));
		else:
			$this->admin_model->authCheck('add_data');
		endif;

		if($this->input->post('SaveChanges')):
		    $error					=	'NO';
 
		 	$this->form_validation->set_rules('added_for[]', 'Added For', 'trim|required');
		 	$this->form_validation->set_rules('position','Position','trim|required');
		 	$this->form_validation->set_rules('live_date_time','Live Date Time','trim|required');
		 	$this->form_validation->set_rules('new_position','New Position','trim');
		 	$this->form_validation->set_rules('live_date_time_later','Live Date Time','trim');
		    $upload_type = $this->input->post('upload_type');
			$originalFileUpdated = false;

		    /* 1 Image Section  Code Start*/
		    if($upload_type == 'image_section'):
			    $images = $_FILES['image'];
			    // echo "<pre>";print_r($images);die();
				if($images['name']):
					$ufileName    = 	$images['name'];
					$utmpName	  = 	$images['tmp_name'];
					$ufileExt     = 	pathinfo($ufileName);
					$unewFileName = $this->common_model->random_strings(8).'.'.$ufileExt['extension'];
					$this->load->library("upload_crop_img");
					$uimageLink	  =	$this->upload_crop_img->_upload_image($ufileName, $utmpName, 'uwin-winnerImage', $unewFileName, '');
					if($uimageLink != 'UPLODEERROR'):
						$imageName = $data['EDITDATA']['image'];
						if($imageName):
							$this->load->library("upload_crop_img");
							$this->upload_crop_img->_delete_image(trim($imageName)); 	
						endif;
						$param['image']		= 	$uimageLink;
						$param['original_file_name'] = $ufileName;
						$originalFileUpdated = true;
					else:
						$param['image']		= 	'';
					endif;
				endif;
			else:
			 	$imageName = $data['EDITDATA']['image'];
				if($imageName):
					$this->load->library("upload_crop_img");
					$this->upload_crop_img->_delete_image(trim($imageName)); 	
				endif;	
				$param['image']	= '';
			endif;
		    /* 1 Image Section  Code End*/

		    /* 2 Video Section Code Start*/
		    if($upload_type == 'video_section'):
			    $videos = $_FILES['video'];
			    // echo "<pre>";print_r($videos);die();
   				if(!empty($videos['name'])):
   					$path 						= 'assets/uwin-winnerImage/';
					$filePath  					= fileFCPATH .$path;
					$ufileExt     				= pathinfo($videos['name']);
					$FileName  					= base64_encode(rand()).'.'.$ufileExt['extension'];
					$config['upload_path']      = $filePath;
			        $config['allowed_types']    = 'mp4|avi|flv|wmv|mov';
	                $config['file_name']        = $FileName;
	                $config['encrypt_name']     = TRUE;
	                $this->load->library('upload', $config);

	                 if(!$this->upload->do_upload('video')):
                    	$error		= $this->upload->display_errors();
						$uimageLink = 'UPLODEERROR';
						$this->session->set_flashdata('alert_error',$error);
						$this->layouts->set_title('Add/Edit Contents');
						redirect('uwin/allcontents/addeditdata/'.$editId);
	                else:
	                	//Getting Uploaded Image Details.
	                	$UploadedData   =   $this->upload->data();
			            $FileName       = $UploadedData['file_name'];
			            $uimageLink  	= $path.$FileName;
						$param['video']	= 	$uimageLink;
						$param['original_file_name'] = $videos['name'];
						$originalFileUpdated = true;
	                endif;
				endif;
			else:
			 	$FileName = $data['EDITDATA']['video'];
				if($FileName):
					$this->load->library("upload_crop_img");
					$this->upload_crop_img->_delete_image(trim($FileName)); 	
				endif;	
				$param['video']	= '';
			endif;
		    /* 2 Video Section Code End*/

		    /* 3 Video Section Code Start*/
		    if($upload_type == 'link_section'):
			    $images = $_FILES['link_thumbnail'];
			    // echo "<pre>";print_r($images);die();
				if($images['name']):
					$ufileName    = 	$images['name'];
					$utmpName	  = 	$images['tmp_name'];
					$ufileExt     = 	pathinfo($ufileName);
					$unewFileName = $this->common_model->random_strings(8).'.'.$ufileExt['extension'];
					$this->load->library("upload_crop_img");
					$uimageLink	  =	$this->upload_crop_img->_upload_image($ufileName, $utmpName, 'uwin-winnerImage', $unewFileName, '');
					if($uimageLink != 'UPLODEERROR'):
						$imageName = $data['EDITDATA']['link_thumbnail'];
						if($imageName):
							$this->load->library("upload_crop_img");
							$this->upload_crop_img->_delete_image(trim($imageName)); 	
						endif;
						$param['link_thumbnail']		= $uimageLink;
						$param['link_url']				= $this->input->post('link_url');
						$param['link_title']			= $this->input->post('link_title');
						$param['game_type']				= $this->input->post('game_type');
						$param['original_file_name']	= $ufileName;
						$originalFileUpdated = true;
					endif;
				endif;
			else:
			 	$imageName = $data['EDITDATA']['link_thumbnail'];
				if($imageName):
					$this->load->library("upload_crop_img");
					$this->upload_crop_img->_delete_image(trim($imageName)); 	
				endif;	
				$param['link_thumbnail'] = '';
				$param['link_url']		 = '';
				$param['link_title']	 = '';
				$param['game_type']		 = '';
			endif;
		    /* 3 Video Section Code End*/

			if(!$originalFileUpdated && !empty($data['EDITDATA']) && $upload_type != ($data['EDITDATA']['upload_type'] ?? '')):
				$param['original_file_name'] = '';
			endif;

			$param['upload_type'] = $upload_type;
			$param['added_for']	  = $this->input->post('added_for');
			$param['position']	     = (int)$this->input->post('position');
			$param['live_date_time'] = strtotime($this->input->post('live_date_time'));
			$param['new_position']   = (int)$this->input->post('new_position');
			$param['live_date_time_later']     = strtotime($this->input->post('live_date_time_later'));
			$param['added_for_top_banner']	   = $this->input->post('added_for_top_banner');
			$param['added_for_result_page']	   = $this->input->post('added_for_result_page');
			$param['added_for_recent_winners'] = $this->input->post('added_for_recent_winners');
			$param['added_for_winner_gallery'] = $this->input->post('added_for_winner_gallery');
	    	// echo "<pre>";print_r($param);die();
			
			if($this->input->post('CurrentDataID') ==''):
				$param['content_id']    =   (int)$this->common_model->getNextSequence('content_id');
				$param['creation_ip']	=	currentIp();
				$param['creation_date']	=	(int)$this->timezone->utc_time();//currentDateTime();
				$param['created_date']	=	date('Y-m-d',$param['creation_date']);
				$param['created_by']	=	(int)$this->session->userdata('UW_ADMIN_ID');
				$param['status']		=	'A';
				$alastInsertId			=	$this->common_model->addData('uw_contents',$param);
				$this->session->set_flashdata('alert_success',lang('addsuccess'));
			else:
				$Date = date('Y-m-d', $data['EDITDATA']['creation_date']);
				$SEQId						=	$this->input->post('CurrentDataID');
				$param['created_date']		=	$Date;
				$param['update_ip']			=	currentIp();
				$param['update_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
				$param['updated_by']		=	(int)$this->session->userdata('UW_ADMIN_ID');
				$this->common_model->editData('uw_contents',$param,'content_id',(int)$SEQId);
				$this->session->set_flashdata('alert_success',lang('updatesuccess'));
			endif;

			redirect(correctLink('CMSCONTENTS',getCurrentControllerPath('index')));
		endif;
		
		$this->layouts->set_title('Add/Edit Contents | UWINN');
		$this->layouts->admin_view('uwin/allcontents/addeditdata',array(),$data);
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
		$this->common_model->editData('uw_contents',$param,'_id', new MongoDB\BSON\ObjectId($changeStatusId) );
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		redirect(correctLink('CMSCONTENTS',$this->session->userdata('UW_ADMIN_CURRENT_PATH').$this->router->fetch_class().'/index'));
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
		$this->admin_model->authCheck('delete_data');
		if($deleteId):
			$this->deleteContentById($deleteId);
		endif;
		
		$this->session->set_flashdata('alert_success',lang('deletesuccess'));
		
		redirect(correctLink('CMSCONTENTS',$this->session->userdata('UW_ADMIN_CURRENT_PATH').$this->router->fetch_class().'/index'));
	}

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name : bulkdeletedata
	 + + Developed By  : Dilip Halder
	 + + Purpose       : Delete multiple contents by selected checkboxes
	 + + Date          : 14 May 2026
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	function bulkdeletedata()
	{
		$this->admin_model->authCheck('delete_data');

		$ids = $this->input->post('ids');
		if(empty($ids) || !is_array($ids)):
			if($this->input->is_ajax_request()):
				header('Content-Type: application/json');
				echo json_encode(array('status' => false, 'message' => 'Please select at least one row to delete.'));
				die;
			endif;
			$this->session->set_flashdata('alert_error', 'Please select at least one row to delete.');
			redirect(correctLink('CMSCONTENTS', $this->session->userdata('UW_ADMIN_CURRENT_PATH').$this->router->fetch_class().'/index'));
		endif;

		$deletedCount = 0;
		foreach($ids as $deleteId):
			$deleteId = trim((string)$deleteId);
			if($deleteId === ''):
				continue;
			endif;
			if($this->deleteContentById($deleteId)):
				$deletedCount++;
			endif;
		endforeach;

		if($this->input->is_ajax_request()):
			header('Content-Type: application/json');
			echo json_encode(array(
				'status'  => $deletedCount > 0,
				'message' => $deletedCount > 0 ? $deletedCount.' item(s) deleted successfully.' : 'No items could be deleted.',
				'count'   => $deletedCount
			));
			die;
		endif;

		if($deletedCount > 0):
			$this->session->set_flashdata('alert_success', lang('deletesuccess'));
		else:
			$this->session->set_flashdata('alert_error', 'No items could be deleted.');
		endif;

		redirect(correctLink('CMSCONTENTS', $this->session->userdata('UW_ADMIN_CURRENT_PATH').$this->router->fetch_class().'/index'));
	}

	private function deleteContentById($deleteId='')
	{
		if($deleteId === ''):
			return false;
		endif;

		try {
			$objectId = new MongoDB\BSON\ObjectId($deleteId);
		} catch (Exception $e) {
			return false;
		}

		$data = $this->common_model->getDataByParticularField('uw_contents', '_id', $objectId);
		if(empty($data)):
			return false;
		endif;

		$this->load->library('upload_crop_img');
		if(!empty($data['image'])):
			$this->upload_crop_img->_delete_image(trim($data['image']));
		endif;
		if(!empty($data['link_thumbnail'])):
			$this->upload_crop_img->_delete_image(trim($data['link_thumbnail']));
		endif;
		if(!empty($data['video'])):
			$this->upload_crop_img->_delete_image(trim($data['video']));
		endif;

		$this->common_model->deleteData('uw_contents', '_id', $objectId);
		return true;
	}


	/***********************************************************************
	** Function name 	: imageDelete
	** Developed By 	: Dilip	 Halder
	** Purpose  		: This function used to delete image
	** Date 			: 04 October 2023
	************************************************************************/
	function imageDelete()
	{  
		$this->admin_model->authCheck('edit_data');
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
			$this->common_model->editData('uw_contents',$param,'content_id',(int)$id);
		endif;
		if($typ == 'web'):
			$returnArray  		= 	array('status'=>1,'message'=>'Image deleted.');
		elseif($typ == 'app'):
			$returnArray  		= 	array('status'=>2,'message'=>'Image deleted.');
		endif;
		header('Content-type: application/json');
		echo json_encode($returnArray); die;
	}	// END OF FUNCTION

	private function generate_random_name($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString . time(); // Append timestamp to avoid collisions
    }

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name 	: settings
	 + + Developed By 	: Dilip Halder
	 + + Purpose  		: This function used for Update Order via Drag and Drop
	 + + Date 			: 25 November 2025
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	function settings()
	{  
		$this->admin_model->authCheck('edit_data');
		$orderData = json_decode($this->input->post('order'), true);
		if(empty($orderData) || !is_array($orderData)):
			echo json_encode(array('status' => false, 'message' => 'Invalid order data'));
			return;
		endif;
		$successCount = 0;
		foreach($orderData as $item):
			if(!empty($item['id']) && !empty($item['position'])):
				$param = array();
				$param['position'] = (int)$item['position'];
				$result = $this->common_model->editData('uw_contents',$param,'_id', new MongoDB\BSON\ObjectId($item['id']) );
				if(!empty($result)):
					$successCount++;
				endif;
			endif;
		endforeach;
		
		if($successCount == count($orderData)):
			echo json_encode(array('status' => true, 'message' => 'Order updated successfully'));
		else:
			echo json_encode(array('status' => false, 'message' => 'Some items could not be updated'));
		endif;
	}

} 
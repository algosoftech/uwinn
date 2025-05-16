<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\ColumnDimension;
use PhpOffice\PhpSpreadsheet\Worksheet;


class Winners extends CI_Controller {

	public function  __construct() 
	{ 
		parent:: __construct();
		error_reporting(E_ALL ^ E_NOTICE);  
		$this->load->model(array('admin_model','emailtemplate_model','emailsendgrid_model','sms_model','notification_model','order_model'));
		$this->lang->load('statictext', 'admin');
		$this->load->helper('common');
	} 

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name 	: index
	 + + Developed By 	: DILIP HALDER
	 + + Purpose  		: This function used for index
	 + + Date 			: 07 February 2024
	 + + Updated Date 	: 15 June 2023
	 + + Updated By   	:
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index()
	{	
		$this->admin_model->authCheck();
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'cms';
		$data['activeSubMenu'] 				= 	'winners';

		if($this->input->get('searchField') == 'status'):
			if($this->input->get('fromDate')):
				$data['fromDate'] 				=   date('Y-m-d H:i', strtotime($this->input->get('fromDate')));  //2023-03-16 15:13
				$whereCon['where_gte'] 			= 	array(array("update_date",strtotime($data['fromDate'])));
			endif;

			if($this->input->get('toDate')):
				$data['toDate'] 				=   date('Y-m-d H:i', strtotime($this->input->get('toDate')));  //2023-03-16 15:13
				$whereCon['where_lte'] 			= 	array(array("update_date",strtotime($data['toDate'])));
			endif;
		else:
			if($this->input->get('fromDate')):
				$data['fromDate'] 				=   date('Y-m-d H:i', strtotime($this->input->get('fromDate')));  //2023-03-16 15:13
				$whereCon['where_gte'] 			= 	array(array("created_at",$data['fromDate']));
			endif;

			if($this->input->get('toDate')):
				$data['toDate'] 				=   date('Y-m-d H:i', strtotime($this->input->get('toDate')));  //2023-03-16 15:13
				$whereCon['where_lte'] 			= 	array(array("created_at",$data['toDate']));
			endif;
		endif;

		// Where conditions section.
		if($this->input->get('searchField') && $this->input->get('searchValue')):
			$sField							=	$this->input->get('searchField');
			$sValue							=	$this->input->get('searchValue');
			$data['searchField'] 			= 	$sField;
			$data['searchValue'] 			= 	$sValue;

			if(is_numeric($sValue)):
				$whereCon['where']		 		= 	array($sField => (int)$sValue );	
			else:
				$whereCon['where']		 		= 	array($sField => $sValue );	
			endif;
		else:
			$whereCon['where']		 		= 	array();	
		endif;
				
		$shortField 						= 	array('sequence_id'=> -1);
		$baseUrl 							= 	getCurrentControllerPath('index');
		
		$this->session->set_userdata('ALLWINNERDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 	'uw_winners_gallery';
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

		$data['ALLDATA']  = 	$this->common_model->getData('multiple',$tblName,$whereCon,$shortField,$perPage,$page);
		// echo '<pre>';print_r($data);die();
		
		$this->layouts->set_title('All Winners | CMS | UWINN');
		$this->layouts->admin_view('cms/winners/index',array(),$data);
	}	// END OF FUNCTION

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name : addeditdata
	 + + Developed By  : DILIP HALDER
	 + + Purpose  	   : This function used for Add Edit data
	 + + Date 		   : 07 February 2024
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function addeditdata($editId='')
	{		
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'cms';
		$data['activeSubMenu'] 				= 	'winners';
		
		if($editId):
			$this->admin_model->authCheck('edit_data');
			$data['EDITDATA']				=	$this->common_model->getDataByParticularField('uw_winners_gallery','section_id',(int)$editId);
		else:
			$this->admin_model->authCheck('add_data');
		endif;

		if($this->input->post('SaveChanges')):
			$error	=	'NO';
			$this->form_validation->set_rules('order_id', 'Ticket_id', 'trim|required');
			
			if($this->form_validation->run() && $error == 'NO'): 
				// Winner Uplaoding syntex.. 
				if($_FILES['winner_image']['name']):
					$filePath  = fileFCPATH .'assets/winnerImage/';
					$FileName  = base64_encode(rand()).'.webp';
					$config['upload_path']      = $filePath;
		            $config['allowed_types']    = '*';
		            $config['file_name']        = $FileName;
		            $config['encrypt_name']     = TRUE;
		            $this->load->library('upload', $config);
		            
		            if(!$this->upload->do_upload('winner_image')):
		            	$error		= $this->upload->display_errors();
						$uimageLink = 'UPLODEERROR';
						$this->session->set_flashdata('alert_error',$error);
						$this->layouts->set_title('Add/Edit Products');
						redirect('cms/winners/addeditdata/'.$editId);
		            else:
		            	//Getting Uploaded Image Details.
		            	$UploadedData   =   $this->upload->data();

		            	$config['image_library']    = 'gd2';
		            	$config['source_image']     = $UploadedData['full_path'];
		            	$config['maintain_ratio']   = FALSE;
		            	$config['width']     		= 450;
		            	$config['height']     		= 450;
						$this->load->library('image_lib');
			            $this->image_lib->clear();
			            $this->image_lib->initialize($config);
			            $this->image_lib->resize();

			            $FileName = $UploadedData['file_name'];
			            $uimageLink   = './assets/winnerImage/'.$FileName;
			            // echo "<pre>"; print_r($UploadedData); die();
						$param['winner_image'] = 	$uimageLink;
		            endif;
				endif;
				$param['order_id']			    = $this->input->post('order_id');
				if($this->input->post('CurrentDataID') ==''):
					$param['section_id']		= (int)$this->common_model->getNextSequence('uw_winners_gallery');
					$param['status']			= 'A';
					$param['creation_ip']		= currentIp();
					$param['created_date']		= date('Y-m-d H:i');//currentDateTime();
					$param['creation_date']	    = (int)$this->timezone->utc_time();//currentDateTime();
					$param['created_by']		= (int)$this->session->userdata('UW_ADMIN_ID');
					$alastInsertId				= $this->common_model->addData('uw_winners_gallery',$param);
					$this->session->set_flashdata('alert_success',lang('addsuccess'));
				else:
					$categoryId					= $this->input->post('CurrentDataID');
					$param['update_ip']			= currentIp();
					$param['update_date']		= (int)$this->timezone->utc_time();//currentDateTime();
					$param['updated_by']		= (int)$this->session->userdata('UW_ADMIN_ID');
					$this->common_model->editData('uw_winners_gallery',$param,'section_id',(int)$categoryId);
					$this->session->set_flashdata('alert_success',lang('updatesuccess'));
				endif;
			    redirect(correctLink('ALLWINNERDATA',getCurrentControllerPath('index')));
			endif;
		endif;
		$this->layouts->set_title('Winners Gallery | CMS | UWINN');
		$this->layouts->admin_view('cms/winners/addeditdata',array(),$data);

	}	// END OF FUNCTION

	/***********************************************************************
	** Function name 	: changestatus
	** Developed By 	: DILIP HALDER
	** Purpose  		: This function used for change status
	** Date 			: 07 February 2024
	************************************************************************/
	function changestatus($changeStatusId='',$statusType='')
	{  
		$this->admin_model->authCheck('edit_data');
		$param['status']		=	$statusType;
		$this->common_model->editData('uw_winners_gallery',$param,'section_id',(int)$changeStatusId);
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		redirect(correctLink('ALLWINNERDATA',getCurrentControllerPath('index')));
	}

	 
	/***********************************************************************
	** Function name 	: deletedata
	** Developed By 	: DILIP HALDER
	** Purpose  		: This function used for delete data
	** Date 			: 07 February 2024
	************************************************************************/
	function deletedata($deleteId='')
	{  
		$Fileds     = array('winner_image');
		$Data       = $this->common_model->getSingleDataByParticularField($Fileds,'uw_winners_gallery','section_id',(int)$deleteId);
		$imageName  = $Data['winner_image'];
		if($imageName):
			$this->load->library("upload_crop_img");
			$this->upload_crop_img->_delete_image(trim($imageName)); 
		endif;
		$this->admin_model->authCheck('delete_data');
		$this->common_model->deleteData('uw_winners_gallery','section_id',(int)$deleteId);
		$this->session->set_flashdata('alert_success',lang('deletesuccess'));
		redirect(correctLink('ALLWINNERDATA',getCurrentControllerPath('index')));
	}

 	/***********************************************************************
	** Function name 	: imageDelete
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used to delete image
	** Date 			: 23 May 2024
	************************************************************************/
	function imageDelete()
	{  
		$imageName			=	$this->input->post('imageName');
		$id 				=	$this->input->post('id');
		$typ 				=	$this->input->post('typ');
		//echo $typ;die;
		if($typ == 'web'):
			$param['winner_image']		=	''; 
		elseif($typ == 'app'):
			$param['winner_image']		=	''; 
		endif;

		if($imageName):
			$this->load->library("upload_crop_img");
			$return	=	$this->upload_crop_img->_delete_image(trim($imageName)); 
			$this->common_model->editData('uw_winners_gallery',$param,'section_id',(int)$id);
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
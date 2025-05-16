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

class Currency extends CI_Controller {

	public function  __construct() 
	{ 
		parent:: __construct();
		error_reporting(E_ALL ^ E_NOTICE);  
		error_reporting(0); 
		$this->load->model(array('admin_model','emailtemplate_model','sms_model','notification_model'));
		$this->lang->load('statictext', 'admin');
		$this->load->helper('common');
	} 

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name 	: index
	 + + Developed By 	: AFSAR ALI
	 + + Purpose  		: This function used for index
	 + + Date 			: 04 JULY 2022
	 + + Updated Date 	: 
	 + + Updated By   	:
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index()
	{	
        $this->admin_model->authCheck();
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'conversion';
		$data['activeSubMenu'] 				= 	'currency';
		
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
		$shortField 						= 	array('_id'=>'desc');
		
		$baseUrl 							= 	getCurrentControllerPath('index');
		$this->session->set_userdata('ALLPRODUCTSDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 	'uw_conversions';
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
        // echo '<pre>'; print_r($data['ALLDATA']);die();
		
		$this->layouts->set_title('Currency Conversion | U-Winn');
		$this->layouts->admin_view('conversion/currency/index',array(),$data);
    } //End of Function

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name : addeditdata
	 + + Developed By  : Dilip Halder
	 + + Purpose  	   : This function used for Add Edit data
	 + + Date 		   : 31 MARCH 2021
	 + + Updated Date  : 
	 + + Updated By    :
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	 public function addeditdata($editId='')
	 {		
		 $data['error'] 						= 	'';
		 $data['activeMenu'] 					= 	'conversion';
		 $data['activeSubMenu'] 				= 	'currency';
		 
		 if($editId):
			 $this->admin_model->authCheck('edit_data');
			 $data['EDITDATA']				=	$this->common_model->getDataByParticularField('uw_conversions','seq_id',(int)$editId);//echo '<pre>';print_r($data['EDITDATA']);die;
		 else:
			 $this->admin_model->authCheck('add_data');
		 endif;
		 
		 if($this->input->post('SaveChanges')):
			 $error					=	'NO';
 
			 $this->form_validation->set_rules('country', 'Country', 'trim');
			 $this->form_validation->set_rules('country_code', 'Country Code', 'trim');
			 $this->form_validation->set_rules('phone_code', 'Phone Code Code', 'trim');
			 $this->form_validation->set_rules('currency', 'Currency', 'trim');
			 $this->form_validation->set_rules('currency_symbol', 'Currency Symbol', 'trim');
			 $this->form_validation->set_rules('conversion_rate', 'Conversion Rate', 'trim|required|greater_than[0]');
			 $this->form_validation->set_rules('time_zone', 'TimeZone', 'trim|required');
			 //  if (empty($_FILES['category_image']['name'])):
			 // 	 $this->form_validation->set_rules('category_image', 'Image', 'trim');
			 //  endif;
 
			 if($this->form_validation->run() && $error == 'NO'): 
 
			 	$param['country']			= $this->input->post('country');
				$param['slug']				= $this->input->post('country');
				$param['country_code']		= $this->input->post('country_code');
				$param['phone_code']		= $this->input->post('phone_code');
				$param['currency']			= $this->input->post('currency');
				$param['currency_symbol']	= $this->input->post('currency_symbol');
				$param['conversion_rate']	= (float)$this->input->post('conversion_rate');
				$param['time_zone']			= $this->input->post('time_zone');

				if($_FILES['image']['name']):
					$ufileName		= $_FILES['image']['name'];
					$utmpName	    = $_FILES['image']['tmp_name'];
					$ufileExt         				= 	pathinfo($ufileName);
					
					$unewFileName 	= $_FILES['image']['name'];
					
					$filePath =  fileFCPATH .'assets/flags/'.$_FILES['image']['name'];
					//  echo $filePath;die();
					if(file_exists($filePath)):
						$unewFileName =	$ufileExt['filename'] .'_'.$this->common_model->random_strings(8).'.'.$ufileExt['extension'];
					endif;

					$this->load->library("upload_crop_img");
					$uimageLink						=	$this->upload_crop_img->_upload_image($ufileName,$utmpName,'flags',$unewFileName,'');
					if($uimageLink != 'UPLODEERROR'):
						$param['image']		= 	$uimageLink;
					endif;
				endif;
				//  echo '<pre>';print_r($param);die();
				 if($this->input->post('CurrentDataID') ==''):
					 $param['seq_id']			=	(int)$this->common_model->getNextSequence('uw_conversions');
					 $param['creation_ip']		=	currentIp();
					 $param['creation_date']	=	(int)$this->timezone->utc_time();//currentDateTime();
					 $param['created_by']		=	(int)$this->session->userdata('KW_ADMIN_ID');
					 $param['status']			=	'A';
					 $check 					= 	array();
					 $tblName 					= 	'uw_conversions';
					 $whereCon 					= 	['country' => $param['country']];
 
					 $check 	= 	$this->common_model->checkDuplicate($tblName,$whereCon);
					 if($check == 0):
						 $alastInsertId				=	$this->common_model->addData('uw_conversions',$param);
						 $this->session->set_flashdata('alert_success',lang('addsuccess'));
					 else:
						 $this->session->set_flashdata('alert_error','Already Exist!');
					 endif;
				 else:
					 $categoryId					=	$this->input->post('CurrentDataID');
					 $param['update_ip']			=	currentIp();
					 $param['update_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
					 $param['updated_by']		=	(int)$this->session->userdata('KW_ADMIN_ID');
					 $this->common_model->editData('uw_conversions',$param,'seq_id',(int)$categoryId);
					 $this->session->set_flashdata('alert_success',lang('updatesuccess'));
				 endif;
 
				 redirect(correctLink('ALLPRODUCTSDATA',getCurrentControllerPath('index')));
			 endif;
		 endif;
		 
		 $this->layouts->set_title('Add/Edit Currency Conversion | U-WINN');
		 $this->layouts->admin_view('conversion/currency/addeditdata',array(),$data);
	 }	// END OF FUNCTION	

    /***********************************************************************
	** Function name 	: changestatus
	** Developed By 	: Afsar Ali
	** Purpose  		: This function used for change status
	** Date 			: 04-07-2024
	************************************************************************/
	function changestatus($changeStatusId='',$statusType='')
	{  
		$this->admin_model->authCheck('edit_data');
		$param['status']		=	$statusType;
		$this->common_model->editData('uw_conversions',$param,'seq_id',(int)$changeStatusId);
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		
		redirect(correctLink('ALLPRODUCTSDATA',getCurrentControllerPath('index')));
	} //End of Function

}
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

class Subwinner extends CI_Controller {

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
	 + + Date 			: 19 January 2024
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index()
	{	
		$this->admin_model->authCheck();
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'sub_winners';
		$data['activeSubMenu'] 				= 	'voucher';
		
		if(!empty($_FILES["csvFile"])):
		    // Check if a file was uploaded
		    if (isset($_FILES["csvFile"]) && $_FILES["csvFile"]["error"] == 0):
		        $uploadedFile = $_FILES["csvFile"]["tmp_name"];
		        // Open the uploaded CSV file
		        $handle = fopen($uploadedFile, "r");
		        if($handle !== false):
		            // Read the CSV file line by line
		            $i = 0;
		            $param = array();
		            while(($data = fgetcsv($handle, 1000, ",")) !== false) {
		            	if($i > 0):

		            		$Date = date("Y-m-d 00:00:00",strtotime($data[5]));

		            		$param['voucher_id']		=	(int)$this->common_model->getNextSequence('wn_draw_winners');
		            		$param['order_id']			=	$data[0]?$data[0]:'N/A';
		            		$param['first_name']		=	$data[1];
		            		$param['last_name']			=	$data[2];
		            		$param['code']				=	$data[3];
		            		$param['amount']			=	$data[4];
						    $param["status"] 			=	(int)1;
						    $param["created_at"] 		=	$data[5]?$Date:date('Y-m-d H:i:s'); //"2024/01/22"
						    $param["created_by"] 		=	"Admin";
						    $param["modified_at"] 		=	date('Y-m-d H:i:s');
						    $param["modified_by"] 		=	"";
		            		$param["creation_ip"] 		=	$this->input->ip_address();
		            		$param['soft_delete']		=	(int)0;
		            		$this->common_model->addData('wn_draw_winners', $param);
		            	endif;
		                $i++;
		            }
		            fclose($handle);

		            $success = "Data Uplaoded Succesfully.";
				    $this->session->set_flashdata('alert_success',$success);
					redirect(correctLink('ALLVOUCHERDATA',getCurrentControllerPath('index')));

		        else:
					$error = "Error opening the CSV file";
				    $this->session->set_flashdata('alert_success',$error);
					// redirect(correctLink('ALLVOUCHERDATA',getCurrentControllerPath('index')));
		        endif;
		    else:
				$error = "Error uploading the file";
			    $this->session->set_flashdata('alert_success',$error);
				redirect(correctLink('ALLVOUCHERDATA',getCurrentControllerPath('index')));
		    endif;
		endif;

		if($this->input->get('searchField') && $this->input->get('searchValue')):

			$sField							= $this->input->get('searchField');
			$sValue							= $this->input->get('searchValue');
			$data['searchField'] 			= $sField;
			$data['searchValue'] 			= $sValue;

			if($sField =='status'):
				if($sValue == 'Inactive'):
					$sFieldState = (int)"0";
				else:
					$sFieldState = (int)"1";
				endif;
				$whereCon['where']				= array($sField => $sFieldState);
			else:
				$whereCon['like']			 	= array('0'=>trim($sField),'1'=>trim($sValue));
			endif;
		else:
			$data['searchField'] 				= '';
			$data['searchValue'] 				= '';
			$whereCon['where']					= array('soft_delete'=>array('$ne'=>1));
		endif;

		$shortField 						= 	array('_id'=> -1);
		$baseUrl 							= 	getCurrentControllerPath('index');
		$this->session->set_userdata('ALLVOUCHERDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 	'wn_draw_winners';
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
		$data['selected_module']  			=   "Sub Winners";

		$this->layouts->set_title(' Daily Winners | Dealz Arabia');
		$this->layouts->admin_view('draws/voucher/index',array(),$data); 
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name 	: changestatus
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for change status
	** Date 			: 21 January 2024
	************************************************************************/
	function changestatus($changeStatusId='',$statusType='')
	{   
		// checking editing permission..
		$this->admin_model->authCheck('edit_data');
	 	$param['status'] 	 = (int)$statusType;
		//Updating status
		$tblName1 			 = 'wn_daily_winners';
		$this->common_model->editData('wn_daily_winners',$param,'voucher_id' , (int)$changeStatusId);
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		redirect(correctLink('ALLVOUCHERDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name 	: deletedata
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for delete data
	** Date 			: 21 January 2024
	************************************************************************/
	function deletedata($deleteId='')
	{  
		$this->admin_model->authCheck('delete_data');
		$param['soft_delete'] = (int)1;
		$this->common_model->editData('wn_daily_winners',$param,'voucher_id' ,(int)$deleteId);
		$this->session->set_flashdata('alert_success',lang('deletesuccess'));
		redirect(correctLink('ALLVOUCHERDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name 	: upload_subwinners
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for upload subwinners
	** Date 			: 23 January 2024
	************************************************************************/
	public function upload_subwinners($value='')
	{	
		$this->admin_model->authCheck();
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'sub_winners';
		$data['activeSubMenu'] 				= 	'voucher';
		
		if(!empty($_FILES["csvFile"])):
		    // Check if a file was uploaded
		    if (isset($_FILES["csvFile"]) && $_FILES["csvFile"]["error"] == 0):
		        $uploadedFile = $_FILES["csvFile"]["tmp_name"];
		        // Open the uploaded CSV file
		        $handle = fopen($uploadedFile, "r");
		        if($handle !== false):
		            // Read the CSV file line by line
		            $i = 0;
		            $param = array();
		            while(($data = fgetcsv($handle, 1000, ",")) !== false) {
		            	if($i > 0):
		            		$Date = date("Y-m-d 00:00:00",strtotime($data[5]));
		            		$param['voucher_id']		=	(int)$this->common_model->getNextSequence('wn_test_draw_winners');
		            		$param['order_id']			=	$data[0]?$data[0]:'N/A';
		            		$param['first_name']		=	$data[1];
		            		$param['last_name']			=	$data[2];
		            		$param['code']				=	$data[3];
		            		$param['amount']			=	$data[4];
						    $param["status"] 			=	(int)1;
						    $param["created_at"] 		=	$data[5]?$Date:date('Y-m-d H:i:s'); //"2024/01/22"
						    $param["created_by"] 		=	"Admin";
						    $param["modified_at"] 		=	date('Y-m-d H:i:s');
						    $param["modified_by"] 		=	"";
		            		$param["creation_ip"] 		=	$this->input->ip_address();
		            		$param['soft_delete']		=	(int)0;
		            		$this->common_model->addData('wn_test_draw_winners', $param);
		            	endif;
		                $i++;
		            }
		            fclose($handle);

		            $success = "Data Uplaoded Succesfully.";
				    $this->session->set_flashdata('alert_success',$success);
					redirect(correctLink('ALLVOUCHERDATA',getCurrentControllerPath('index')));

		        else:
					$error = "Error opening the CSV file";
				    $this->session->set_flashdata('alert_success',$error);
					// redirect(correctLink('ALLVOUCHERDATA',getCurrentControllerPath('index')));
		        endif;
		    else:
				$error = "Error uploading the file";
			    $this->session->set_flashdata('alert_success',$error);
				redirect(correctLink('ALLVOUCHERDATA',getCurrentControllerPath('index')));
		    endif;
		endif;

		if($this->input->get('searchField') && $this->input->get('searchValue')):

			$sField							= $this->input->get('searchField');
			$sValue							= $this->input->get('searchValue');
			$data['searchField'] 			= $sField;
			$data['searchValue'] 			= $sValue;

			if($sField =='status'):
				if($sValue == 'Inactive'):
					$sFieldState = (int)"0";
				else:
					$sFieldState = (int)"1";
				endif;
				$whereCon['where']				= array($sField => $sFieldState);
			else:
				$whereCon['like']			 	= array('0'=>trim($sField),'1'=>trim($sValue));
			endif;
		else:
			$data['searchField'] 				= '';
			$data['searchValue'] 				= '';
			$whereCon['where']					= array('soft_delete'=>array('$ne'=>1));
		endif;

		$shortField 						= 	array('_id'=> -1);
		$baseUrl 							= 	getCurrentControllerPath('index');
		$this->session->set_userdata('ALLVOUCHERDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 	'wn_draw_winners';
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
		$this->layouts->set_title(' Daily Winners | Dealz Arabia');
		$this->layouts->admin_view('draws/voucher/index',array(),$data);
	}	// END OF FUNCTION


	 

}
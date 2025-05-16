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


class Allcollectionpoint extends CI_Controller {

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
	 + + Developed By 	: Manoj Kumar
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
		$data['activeMenu'] 				= 	'emirate';
		$data['activeSubMenu'] 				= 	'allcollectionpoint';
		
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
		$shortField 						= 	array('collection_point_name'=>'ASC');
		
		$baseUrl 							= 	getCurrentControllerPath('index');
		$this->session->set_userdata('ALLCOLLECTIONPOINTDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 	'da_emirate_collection_point';
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
		$this->layouts->set_title('Collection Point | Collection Point | Dealz Arabia');
		$this->layouts->admin_view('emirate/collectionpoint/index',array(),$data);
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
		$data['activeMenu'] 				= 	'emirate';
		$data['activeSubMenu'] 				= 	'allcollectionpoint';
		
		if($editId):
			$this->admin_model->authCheck('edit_data');
			$data['EDITDATA']				=	$this->common_model->getDataByParticularField('da_emirate_collection_point','collection_point_id',(int)$editId);
		else:
			$this->admin_model->authCheck('add_data');
		endif;
		
		if($this->input->post('SaveChanges')):
			$error					=	'NO';
			$this->form_validation->set_rules('emirate_id', 'Emirate', 'trim|required');
			$this->form_validation->set_rules('area_id', 'Emirate Area', 'trim|required');
			$this->form_validation->set_rules('email_id', 'Email ID / Phone No.', 'trim|required');
			$this->form_validation->set_rules('collection_point_name', 'Collection Point', 'trim|required');
			
			if(is_numeric($this->input->post('email_id'))){
				if(strlen($this->input->post('email_id')) >= 10){
					$where['where']		=	['users_mobile' => (int)$this->input->post('email_id'), 'pickup_point_holder' => 'Y', 'status' => 'A'];
				}else{
					$where['where']		=	['users_mobile' => (float)$this->input->post('email_id'), 'pickup_point_holder' => 'Y', 'status' => 'A'];
				}
			}else{
				$where['where']		=	['users_email' => $this->input->post('email_id'), 'pickup_point_holder' => 'Y', 'status' => 'A'];
			}
			$userCheck = $this->common_model->getParticularFieldByMultipleCondition(array('users_id', 'users_email', 'users_mobile'),'da_users', $where);
			if(empty($userCheck)){
				$error = 'YES';
				$this->session->set_flashdata('alert_error','This user account not assign as pickup point holder.');
				$data['email_id_error'] = "This user account not assign as pickup point holder.";
			}
			if($this->form_validation->run() && $error == 'NO'): 

				$emirateData						=	explode('_____',$this->input->post('emirate_id'));
				$areaData							=	explode('_____',$this->input->post('area_id'));
				$param['users_id']					= 	(int)$userCheck['users_id'];
				$param['users_email']				=	$userCheck['users_email'] ? $userCheck['users_email'] : '';
				$param['users_mobile']				=	$userCheck['users_mobile'] ? $userCheck['users_mobile'] : '';

				$param['emirate_id']				= 	(int)$emirateData[0];
				$param['emirate_name']				= 	addslashes($emirateData[1]);
				$param['emirate_slug']				= 	url_title(strtolower($param['emirate_name']));

				$param['area_id']					= 	(int)$areaData[0];
				$param['area_name']					= 	addslashes($areaData[1]);
				$param['area_slug']					= 	url_title(strtolower($param['area_name']));
				
				$param['collection_point_name']		= 	addslashes($this->input->post('collection_point_name'));
				$param['collection_point_slug']		= 	url_title(strtolower($param['collection_point_name']));
				
				if($this->input->post('CurrentDataID') ==''):
					$param['collection_point_id']	=	(int)$this->common_model->getNextSequence('da_emirate_collection_point');
					
					$param['creation_ip']			=	currentIp();
					$param['creation_date']			=	(int)$this->timezone->utc_time();//currentDateTime();
					$param['created_by']			=	(int)$this->session->userdata('SCHW_ADMIN_ID');
					$param['status']				=	'A';

					$check 		= array();
					$tblName 	= 'da_emirate_collection_point';
					$whereCon 	= ['emirate_name' => $param['emirate_name'],
									'collection_point_name' => $param['collection_point_name']
									];
					$check 	= 	$this->common_model->checkDuplicate($tblName,$whereCon);
					if($check == 0):
						$alastInsertId				=	$this->common_model->addData('da_emirate_collection_point',$param);
					$this->session->set_flashdata('alert_success',lang('addsuccess'));
					else:
						$this->session->set_flashdata('alert_error','Already Exist!');
					endif;
				else:
					$collectionpointId				=	$this->input->post('CurrentDataID');
					$param['update_ip']				=	currentIp();
					$param['update_date']			=	(int)$this->timezone->utc_time();//currentDateTime();
					$param['updated_by']			=	(int)$this->session->userdata('SCHW_ADMIN_ID');
					$this->common_model->editData('da_emirate_collection_point',$param,'collection_point_id',(int)$collectionpointId);
					$this->session->set_flashdata('alert_success',lang('updatesuccess'));
				endif;

				redirect(correctLink('ALLCOLLECTIONPOINTDATA',getCurrentControllerPath('index')));
			endif;
		endif;
		
		$this->layouts->set_title('Add/Edit Collection Point | Dealz Arabia');
		$this->layouts->admin_view('emirate/collectionpoint/addeditdata',array(),$data);
	}	// END OF FUNCTION	

	/***********************************************************************
	** Function name : changestatus
	** Developed By : Manoj Kumar
	** Purpose  : This function used for change status
	** Date : 21 JUNE 2021
	************************************************************************/
	function changestatus($changeStatusId='',$statusType='')
	{  
		$this->admin_model->authCheck('edit_data');
		$param['status']		=	$statusType;
		$this->common_model->editData('da_emirate_collection_point',$param,'collection_point_id',(int)$changeStatusId);
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		
		redirect(correctLink('ALLCOLLECTIONPOINTDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name : deletedata
	** Developed By : Manoj Kumar
	** Purpose  : This function used for delete data
	** Date : 21 JUNE 2021
	************************************************************************/
	function deletedata($deleteId='')
	{  
		$this->admin_model->authCheck('delete_data');
		$this->common_model->deleteData('da_emirate_collection_point','collection_point_id',(int)$deleteId);
		$this->session->set_flashdata('alert_success',lang('deletesuccess'));
		
		redirect(correctLink('ALLCOLLECTIONPOINTDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name : getmaratArea
	** Developed By : Afsar Ali
	** Purpose  : This function used for delete data
	** Date : 11 NOV 2022
	************************************************************************/
	function getmaratArea($emaratId='')
	{  
		$emarate_id = $this->input->post('emirate_id');
		$emarateData = explode('_____',$emarate_id);
		$area = $this->admin_model->getEmiratesArea($emarateData[0]);
		print_r($area);
		
	}

	/***********************************************************************
	** Function name : checkRetailer
	** Developed By : Afsar Ali
	** Purpose  : This function used for delete data
	** Date : 11 NOV 2022
	************************************************************************/
	function checkRetailer($retailerID='')
	{  
		$retailerData = $this->input->post('retailerID');
		if(is_numeric($retailerData)){
			if(strlen($this->input->post('email_id')) >= 10){
				$whereCon		=	['users_mobile' => (int)$retailerData, 'pickup_point_holder' => 'Y', 'status' => 'A'];
			}else{
				$whereCon		=	['users_mobile' => (float)$retailerData, 'pickup_point_holder' => 'Y', 'status' => 'A'];
			}
		}else{
			$whereCon		=	['users_email' => $retailerData,'pickup_point_holder' => 'Y', 'status' => 'A'];
		}
		$count = $this->common_model->checkDuplicate('da_users', $whereCon);
		if($count == 0){
			$data = 'error';
		}else{
			$data = 'success';
		}
		echo $data;
	} // END OF FUNCTION



	/***********************************************************************
	** Function name 	: exportexcel
	** Developed By 	: Dilip halder
	** Purpose  		: This function used for export  All Collection points
	** Date 			: 01 FEB 2023
	** Updated Date 	: 
	** Updated By   	: 
	************************************************************************/
	function exportexcel()
	{  
		/* Export excel button code */
		$whereCon['where']		= 	'';		
		$shortField 			= 	array('created_at'=> -1);
		$tblName 				= 	'da_emirate_collection_point';


		if($this->input->get('searchField') && $this->input->get('searchValue')):
			$sField							=	$this->input->get('searchField');
			$sValue							=	$this->input->get('searchValue');
			if($sField == 'recharge_by'):
				$users_id = $this->common_model->getPaticularFieldByFields('users_id', 'da_users', 'users_email', trim($sValue));
				$whereCon['where']['user_id_deb'] = (int)$users_id;
			elseif($sField == 'recharge_to'):
				$users_id = $this->common_model->getPaticularFieldByFields('users_id', 'da_users', 'users_email', trim($sValue));
				$whereCon['where']['user_id_cred'] = (int)$users_id;
			elseif($sField == 'record_type'):
				$whereCon['like']			 = 	array('0'=>trim($sField),'1'=>trim($sValue));
			endif;
		else:
			$whereCon['like']		 		= 	"";
		endif;

		if($this->input->get('fromDate')){
			$data['fromDate'] 				= 	$this->input->get('fromDate');
			$whereCon['where_gte'] 			= 	array(array("created_at",$data['fromDate']));
		}
		if($this->input->get('toDate')){
			$data['toDate'] 				= 	$this->input->get('toDate');
			$whereCon['where_lte'] 			= 	array(array("created_at",$data['toDate']));
		}

		$data        						=   $this->common_model->getDataByNewQuery('*','multiple',$tblName,$whereCon,$shortField,0,0);
		
        $spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'Sl.No');
		$sheet->setCellValue('B1', 'EMIRATE');
		$sheet->setCellValue('C1', 'EMIRATE AREA');
		$sheet->setCellValue('D1', 'RETAILER EMAIL / MOBILE NO.');
		$sheet->setCellValue('E1', 'COLLECTION POINT ADDRESS');
		$sheet->setCellValue('F1', 'CREATED DATE');

		
		$slno = 1;
		$start = 2;
		foreach($data as $d){

			if(isset($d['users_email']) && isset($d['users_mobile'])): 
			$email =  'Email ID :'. stripslashes($d['users_email']).PHP_EOL; 

			$mobile = 'Mobile No. :' .stripslashes($d['users_mobile']) ;

			elseif(isset($d['users_email'])): 

			$email = 'Email ID :'. stripslashes($d['users_email']); 
			
			else: 

			$mobile = 'Mobile No. :'. stripslashes($d['users_mobile']);
			endif;

            
			$sheet->setCellValue('A'.$start, $slno);
			$sheet->setCellValue('B'.$start, $d['emirate_name']);
			$sheet->setCellValue('C'.$start, $d['area_name']);
			$sheet->setCellValue('D'.$start, $email . $mobile );
			$sheet->setCellValue('E'.$start, $d['collection_point_name']);
			$sheet->setCellValue('F'.$start, date('d-F-Y h:i A', $d['creation_date']));	
			

			// echo '<pre>';
			// print_r(date('d-F-Y h:i A',$d['creation_date']));
			// die();
			$start = $start+1;
			$slno = $slno+1;
			
		}

		$styleThinBlackBorderOutline = [
					'borders' => [
						'allBorders' => [
							'borderStyle' => Border::BORDER_THIN,
							'color' => ['argb' => 'FF000000'],
						],
					],
				];
		//Font BOLD
		$sheet->getStyle('A1:F1')->getFont()->setBold(true);		
		$sheet->getStyle('A1:F1')->applyFromArray($styleThinBlackBorderOutline);
		//Alignment
		//fONT SIZE
		//$sheet->getStyle('A1:D10')->getFont()->setSize(12);
		//$sheet->getStyle('A1:D2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
		//$sheet->getStyle('A2:D1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		//Custom width for Individual Columns
		$sheet->getColumnDimension('A')->setWidth(5);
		$sheet->getColumnDimension('B')->setWidth(30);
		$sheet->getColumnDimension('C')->setWidth(30);
		$sheet->getColumnDimension('D')->setWidth(30);
		$sheet->getColumnDimension('E')->setWidth(30);
		$sheet->getColumnDimension('F')->setWidth(40);
		

		$curdate = date('d-m-Y H:i:s');
		$writer = new Xlsx($spreadsheet);
		$filename = 'All-Collection-Point-list'.$curdate;
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
		//endif;
		/* Export excel END */
	}
	
}
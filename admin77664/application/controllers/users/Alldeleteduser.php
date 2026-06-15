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

class Alldeleteduser extends CI_Controller {

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
	 + + Date 			: 30 MARCH 2022
	 + + Updated Date 	: 
	 + + Updated By   	: 
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index()
	{	
		$this->admin_model->authCheck('view_data');
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'users';
		$data['activeSubMenu'] 				= 	'allcustomer';
		
		if($this->input->get('searchField') && $this->input->get('searchValue')):
			$sField						= $this->input->get('searchField');
			$sValue						= $this->input->get('searchValue');
			$whereCon['where'][$sField]	= is_numeric($sValue)?(int)$sValue : $sValue;		
			$data['searchField'] 		= $sField;
			$data['searchValue'] 		= $sValue;
		else:
			$whereCon['like']		 	= '';
			$data['searchField'] 		= '';
			$data['searchValue'] 		= '';
		endif;
				
		$whereCon['where']['status']		= "D";				
		$shortField        					= array('updated_at' => -1 );
		
		$baseUrl 							= 	getCurrentControllerPath('index');
		$this->session->set_userdata('ALLDELETEDUSERDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 	'uw_users';
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
		
		$this->layouts->set_title('Deleted Users | Users | UWINN');
		$this->layouts->admin_view('users/alldeleteduser/index',array(),$data);
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name 	: changestatus
	** Developed By 	: Manoj Kumar
	** Purpose  		: This function used for change status
	** Date 			: 30 MARCH 2022
	** Updated Date 	: 
	** Updated By   	: 
	************************************************************************/
	function changestatus($changeStatusId='',$statusType='')
	{  
		$this->admin_model->authCheck('edit_data');
		$param['status']		=	$statusType;
		$this->common_model->editData('uw_users',$param,'users_id',(int)$changeStatusId);
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		
		redirect(correctLink('ALLDELETEDUSERDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name 	: exportexcel
	** Developed By 	: Ravi Negi
	** Purpose  		: This function used for export deleted users data
	** Date 			: 30 MARCH 2022
	** Updated Date 	: 
	** Updated By   	: 
	************************************************************************/
	// function exportexcel()
	// {  
	// 	$this->admin_model->authCheck('view_data');
	// 	//Generating Logs
	// 	$this->common_model->generateLogs();

	// 	/* Export excel button code */
	// 	$whereCon['where'] = array('status'=>'D');		
	// 	$shortField 	   = array('users_id'=>'ASC');
	// 	$data        	   = $this->common_model->getData('multiple','uw_users',$whereCon,$shortField,$perPage,$page);

    //     $spreadsheet = new Spreadsheet();
	// 	$sheet = $spreadsheet->getActiveSheet();
	// 	$sheet->setCellValue('A1', 'Sl.No');
	// 	$sheet->setCellValue('B1', 'USER ID');
	// 	$sheet->setCellValue('C1', 'USER UNIQUE ID');
	// 	$sheet->setCellValue('D1', 'NAME');
	// 	$sheet->setCellValue('E1', 'EMAIL');
	// 	$sheet->setCellValue('F1', 'PHONE');
	// 	$sheet->setCellValue('G1', 'ACCOUNT VERIFIED');
	// 	$sheet->setCellValue('H1', 'CREATION DATE/TIME');
	// 	$sheet->setCellValue('I1', 'CREATION IP');
	// 	$slno = 1;
	// 	$start = 2;
	// 	foreach($data as $d){
	// 		$sheet->setCellValue('A'.$start, $slno);
	// 		$sheet->setCellValue('B'.$start, $d['users_id']);
	// 		$sheet->setCellValue('C'.$start, $d['users_sequence_id']);
	// 		$sheet->setCellValue('D'.$start, $d['users_name']);
	// 		$sheet->setCellValue('E'.$start, $d['users_email']);
	// 		$sheet->setCellValue('F'.$start, $d['users_mobile']);
	// 		$sheet->setCellValue('G'.$start, $d['users_account_verified']);
	// 		$sheet->setCellValue('H'.$start, date('d-m-Y H:i:s', $d['creation_date']));
	// 		$sheet->setCellValue('I'.$start, $d['creation_ip']);
	// 	$start = $start+1;
	// 	$slno = $slno+1;
	// 	}
	// 	$styleThinBlackBorderOutline = [
	// 				'borders' => [
	// 					'allBorders' => [
	// 						'borderStyle' => Border::BORDER_THIN,
	// 						'color' => ['argb' => 'FF000000'],
	// 					],
	// 				],
	// 			];
	// 	//Font BOLD
	// 	$sheet->getStyle('A1:I1')->getFont()->setBold(true);		
	// 	$sheet->getStyle('A1:I1000')->applyFromArray($styleThinBlackBorderOutline);
	// 	//Alignment
	// 	//fONT SIZE
	// 	$sheet->getStyle('A1:D10')->getFont()->setSize(12);
	// 	$sheet->getStyle('A1:D2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
	// 	$sheet->getStyle('A2:D100')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
	// 	//Custom width for Individual Columns
	// 	$sheet->getColumnDimension('A')->setWidth(5);
	// 	$sheet->getColumnDimension('B')->setWidth(15);
	// 	$sheet->getColumnDimension('C')->setWidth(30);
	// 	$sheet->getColumnDimension('D')->setWidth(30);
	// 	$sheet->getColumnDimension('E')->setWidth(15);
	// 	$sheet->getColumnDimension('F')->setWidth(15);
	// 	$sheet->getColumnDimension('G')->setWidth(15);
	// 	$sheet->getColumnDimension('H')->setWidth(30);
	// 	$sheet->getColumnDimension('I')->setWidth(30);
		

	// 	$curdate = date('d-m-Y H:i:s');
	// 	$writer = new Xlsx($spreadsheet);
	// 	$filename = 'Deleted-Users'.$curdate;
	// 	ob_end_clean();
	// 	header('Content-Type: application/vnd.ms-excel');
	// 	header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
	// 	header('Cache-Control: max-age=0');
	// 	$writer->save('php://output');
	// 	//endif;
	// 	/* Export excel END */
	// }
	/***********************************************************************
	** Function name 	: exportexcel
	** Developed By 	: Ravi Negi
	** Purpose  		: This function used for export deleted users data
	** Date 			: 30 MARCH 2022
	** Updated Date 	: 
	** Updated By   	: 
	************************************************************************/
	function exportexcel()
	{  
		$this->admin_model->authCheck('view_data');
		
		//Generating Logs
		$this->common_model->generateLogs();
		$this->session->set_userdata('ALLDELETEDUSERDATA',currentFullUrl());
		
		/* Export excel button code */
		$resultType   = "count";
		$tblName 	  = "uw_users";
		$whereCon['where'] = array('status'=>'D');		
		$totalRows 	  = $this->common_model->getData('count',$tblName,$whereCon); 
		// echo "<pre>";print_r($totalRows);die();

		$itemsPerPage = 5000;
		// ---------------------------------------------

		$longArray    = $totalRows;
		$pageno       = $this->input->get('page');

		// Current page number (received from URL query parameter, e.g., ?page=2)
		$page        = isset($pageno) ? (int)$pageno : 1;
		// Calculate total number of pages
		$totalPages  = ceil($longArray / $itemsPerPage);
		$totalpage   = array();
		// Pagination links
		for ($i = 1; $i <= $totalPages; $i++) {
		    if ($i == $page) {
		         $current_page = $i;
		         $totalpage[] = $i;
		    } else {
		         $totalpage[] = $i;
		    }
		}
 		
 		$startIndex  = ($page - 1) * $itemsPerPage;
		$totalpage 				= count($totalpage);
		$data['current_page']   = $current_page;
		$data['total_page'] 	= $totalpage;
		
		$data['searchField'] 	=   $searchField;
		$data['searchValue'] 	=   $searchValue;
		$data['fromDate'] 		=   $fromDate;
		$data['toDate'] 		=   $toDate;
		// echo "<pre>";print_r($data);die();
		$this->layouts->set_title('Export CSV | UWINN');
		$this->layouts->admin_view('users/alldeleteduser/exportexcel',array(),$data);
		// -----------------------------------------------------------------------------//
       
	}


	/***********************************************************************
	** Function name 	: exportexcelApi
	** Developed By 	: Dilip halder
	** Purpose  		: This function used for export order data
	** Date 			: 18 July 2025
	************************************************************************/
	function exportexcelApi(){
		
		$this->admin_model->authCheck('view_data');
		 
		// -----------------------------------------------------------------------------//
		// $page = $this->input->post('pageno');
		$page              = $this->input->post('pageno');
		// $page = 1;
 		$itemsPerPage      = 5000;
 		$startIndex        = ($page - 1)*$itemsPerPage;
		$tblName 	  	   = "uw_users";
		$whereCon['where'] = array('status'=>'D');		
		$shortField        = array('updated_at' => -1 );
		$UserData 	       = $this->common_model->getData('multiple',$tblName,$whereCon ,$shortField); 

		$CSVData  = array();
		foreach($UserData as $index => $itemsArray):
			
			if($itemsArray['status'] == "A"):
				$status = "Active";
			elseif($itemsArray['status'] == "I"):
				$status = "Inactive";
			elseif($itemsArray['status'] == "D"):
				$status = "Deleted";
			endif;

			if($itemsArray['users_type'] == 'Users'):
				$itemsArray['bindwith_first_name'] = 'Admin';
				$seller_Store = $itemsArray['users_name'];
			else:
				$seller_Store = $itemsArray['store_name'];
			endif;
			$CSVData[$index]['POS NUMBER']  = !empty($itemsArray['pos_number']) ? $itemsArray['pos_number'] : 'N/A';
			$CSVData[$index]['FIRST NAME']    = !empty($itemsArray['users_name'])   ? $itemsArray['users_name'] : 'N/A';
			$CSVData[$index]['LAST NAME']     = !empty($itemsArray['last_name'])    ? $itemsArray['last_name'] : 'N/A';
			$CSVData[$index]['PHONE']         = !empty($itemsArray['users_mobile'])    ? $itemsArray['users_mobile'] : 'N/A';
			$CSVData[$index]['EMAIL']         = !empty($itemsArray['users_email'])    ? $itemsArray['users_email'] : 'N/A';
			$CSVData[$index]['TOTAL ARABIAN POINTS']     = !empty($itemsArray['totalArabianPoints'])     ? $itemsArray['totalArabianPoints'] : 'N/A';
			$CSVData[$index]['AVAILABLE ARABIAN POINTS'] = !empty($itemsArray['availableArabianPoints']) ? $itemsArray['availableArabianPoints'] : 'N/A';
			$CSVData[$index]['USER TYPE']           = !empty($itemsArray['users_type']) ? $itemsArray['users_type'] : 'N/A';
			$CSVData[$index]['BIND WITH']           = !empty($itemsArray['bind_person_name']) ? $itemsArray['bind_person_name'] : 'N/A';
			$CSVData[$index]['BIND WITH USER TYPE'] = !empty($itemsArray['bind_user_type']) ? $itemsArray['bind_user_type'] : 'N/A';
			$CSVData[$index]['Store Name']          = !empty($itemsArray['store_name']) ? $itemsArray['store_name'] : 'N/A';
			$CSVData[$index]['CREATION DATE']       = !empty(date('d-M-Y ', strtotime($d['created_at']))) ? date('d-M-Y ', strtotime($itemsArray['created_at'])) : 'N/A';
			$CSVData[$index]['Deleted DATE']        = !empty(date('d-M-Y ', strtotime($d['updated_at']))) ? date('d-M-Y ', strtotime($itemsArray['updated_at'])) : 'N/A';
			$CSVData[$index]['STATUS Name']         = !empty($status) ? $status : 'N/A';

		endforeach;
		echo json_encode($CSVData);
		die();
	}
	
}
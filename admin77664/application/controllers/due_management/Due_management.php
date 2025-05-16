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

class Due_management extends CI_Controller {

	public function  __construct() 
	{ 
		parent:: __construct();
		error_reporting(E_ALL ^ E_NOTICE);  
		$this->load->model(array('admin_model','emailtemplate_model','sms_model','notification_model','order_model'));
		$this->lang->load('statictext', 'admin');
		$this->load->helper('common');
	} 


	public function index()
	{	
		$this->admin_model->authCheck();
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'due_management';
		$data['activeSubMenu'] 				= 	'due_management';

		if($this->input->get('fromDate')):
			$data['fromDate'] 				=   date('Y-m-d 00:00 ', strtotime($this->input->get('fromDate')));  //2023-03-16 15:13
			$whereCon['where_gte'] 			= 	array(array("created_at",$data['fromDate']));
		endif;

		if($this->input->get('toDate')):
			$data['toDate'] 				=   date('Y-m-d 23:59 ', strtotime($this->input->get('toDate')));  //2023-03-16 15:13
			$whereCon['where_lte'] 			= 	array(array("created_at",$data['toDate']));
		endif;


		if($this->input->get('searchField') && $this->input->get('searchValue')):
				
			$sField							=	$this->input->get('searchField');
			$sValue							=	$this->input->get('searchValue');
			
			if($sField == 'users_mobile' || $sField ==  'sender_users_mobile' || $sField ==  'recharge_amt' || $sField ==  'cash_collected' || $sField ==  'advanced_amount' ):
			$whereCon['where']			 	= 	array( $sField => (int)$sValue ,'record_type' => 'Debit');
			$data['searchField'] 			= 	$sField;
			$data['searchValue'] 			= 	$sValue;
			else:
			$whereCon['search']			 	= 	array('0'=>trim($sField),'1'=>trim($sValue));
			$data['searchField'] 			= 	$sField;
			$data['searchValue'] 			= 	$sValue;
			endif;
		else:
			$whereCon['where']					=	array('record_type' => 'Debit');
			$whereCon['search']		 		= 	"";
			$data['searchField'] 			= 	'';
			$data['searchValue'] 			= 	'';
		endif;

		$shortField 						= 	array('due_management_id'=> -1 );

		$baseUrl 							= 	getCurrentControllerPath('index');
		$this->session->set_userdata('ALLORDERSDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 	'da_dueManagement';
		$con 								= 	'';
		$totalRows 							= 	$this->common_model->duemanagement('count',$tblName,$whereCon,$shortField,'0','0');

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

		if($this->input->get('searchField') && $this->input->get('searchValue')):
			$DueManagement 						=	$this->common_model->duemanagement('multiple',$tblName,$whereCon,$shortField,$page,$perPage);
			sort($DueManagement);
			if($DueManagement[0]['created_by'] == "Super Salesperson"):
				$tblName 				= 	'da_users';
				$shortField 			= 	'';
				$whereCon['where']		=	array('users_type' => 'Sales Person');
				$salesPersonList 		= 	$this->common_model->getData('multiple',$tblName,$whereCon,$shortField);
				$data['salespersonList'] = $salesPersonList;
			endif;
		endif;
		// Second search for saleperson view start.
		$salesperson = $this->input->get('salesperson');
		if(!empty($salesperson)):
			$data['salesperson'] = $salesperson;

			if(is_numeric($salesperson)):
				$sValue = (int)$salesperson;
				$whereCon1['where']	 = 	array( 'sender_users_mobile' => (int)$sValue ,'record_type' => 'Debit','user_type' => 'Promoter');
			else:
				$sValue = $salesperson;
				// $whereCon1['where']	 = 	array( 'sender_users_email' => (int)$sValue ,'record_type' => 'Debit');
				$whereCon1['where']	 = 	array( 'sender_users_email' => $sValue ,'record_type' => 'Debit','user_type' => 'Promoter');

			endif;
			$tblName 			 = 	'da_dueManagement';
			$shortField 		 = 	array('due_management_id'=> -1 );
			$Salesperson_Due  	 =	$this->common_model->duemanagement('multiple',$tblName,$whereCon1,$shortField,$page,$perPage);
			sort($Salesperson_Due);
		endif;
		// Second search for saleperson view end.
		$data['Salesperson_Due']    = $Salesperson_Due;
		$data['DueManagement'] 		= $DueManagement;
		// echo "<pre>"; print_r($filteredDueData); die();

		$this->layouts->set_title('Due Management | Dealz Arabia');
		$this->layouts->admin_view('due_management/index',array(),$data);
	}	// END OF FUNCTION

	public function view_due_management($id,$userid)
	{	
		$this->admin_model->authCheck();
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'due_management';
		$data['activeSubMenu'] 				= 	'due_management';

		// $tblName 					=	'da_dueManagement';
		// $shortField 				= 	array('due_management_id'=> -1 );
		// $whereCon['where']			=	array('user_id_to' => (int)$id,'record_type' => 'Debit',"recharge_type" => "Advanced Cash");
		$data['id'] = $id;
		$data['userid'] = $userid;

		if(!empty($this->input->get('fromDate'))):

			$fromDate			=	date('Y-m-d 00:01 ', strtotime($this->input->get('fromDate')));  
			$whereCon['where_gte']			 	= 	array( array('created_at',$fromDate) );
			$data['fromDate'] 			= 	$fromDate;

		endif;

		if(!empty($this->input->get('toDate'))):

			$toDate			=	date('Y-m-d 23:59 ', strtotime($this->input->get('toDate')));  
			$whereCon['where_lte']			 	= 	array( array('created_at',$toDate) );

			$data['toDate'] 			= 	$toDate;

		endif;
		
		
		if($this->input->get('searchField') && $this->input->get('searchValue')):
				
			$sField							=	$this->input->get('searchField');
			$sValue							=	$this->input->get('searchValue');

			if($sField == 'users_mobile' || $sField ==  'sender_users_mobile' || $sField == 'recharge_amt' || $sField == 'recharge_amt' || $sField == 'cash_collected' || $sField == 'advanced_amount' ):
			$whereCon['where']			 	= 	array( $sField => (int)$sValue ,'record_type' => 'Debit',"recharge_type" => "Advanced Cash",'user_id_to' => (int)$id);
			$data['searchField'] 			= 	$sField;
			$data['searchValue'] 			= 	$sValue;
			else:
			$whereCon['search']			 	= 	array('0'=>trim($sField),'1'=>trim($sValue));
			$data['searchField'] 			= 	$sField;
			$data['searchValue'] 			= 	$sValue;
			endif;
		else:
			$whereCon['where']				=	array('user_id_to' => (int)$id, 'user_id_deb' => (int)$userid );
			$data['searchField'] 			= 	'';
			$data['searchValue'] 			= 	'';
		endif;

		$shortField 						= 	array('due_management_id'=> -1 );

		$baseUrl 							= 	getCurrentControllerPath('due_management/due_management/view_due_management/'.$id.'/'.$userid);

		$this->session->set_userdata('ALLVIEWDUEMANAGEMENTDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 	'da_dueManagement';
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

       if($this->uri->segment(6)):
           $page = $this->uri->segment(6);
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

		$data['DueManagement'] 		=	$this->common_model->getData('multiple',$tblName,$whereCon,$shortField,$page,$perPage);

		$this->layouts->set_title('Due Management | Dealz Arabia');
		$this->layouts->admin_view('due_management/viewduemanagement',array(),$data);
	}		// END OF FUNCTION



	/***********************************************************************
	** Function name 	: exportexcel
	** Developed By 	: Dilip halder
	** Purpose  		: This function used for export order data
	** Date 			: 02 Apirl 2023
	** Updated Date 	:
	** Updated By   	: 
	************************************************************************/
	function exportexcel($load_balance_id='')
	{  
		
		$this->admin_model->authCheck('admin_view');
		//Generating Logs
		$this->common_model->generateLogs();
		
       	if($this->input->post('fromDate')):
			$data['fromDate'] 				=   date('Y-m-d 00:00 ', strtotime($this->input->post('fromDate')));  //2023-03-16 15:13
			$whereCon['where_gte'] 			= 	array(array("created_at",$data['fromDate']));
		endif;

		if($this->input->post('toDate')):
			$data['toDate'] 				=   date('Y-m-d 23:59 ', strtotime($this->input->post('toDate')));  //2023-03-16 15:13
			$whereCon['where_lte'] 			= 	array(array("created_at",$data['toDate']));
		endif;
	   
		if($this->input->post('searchField') && $this->input->post('searchValue')):
				
			$sField							=	$this->input->post('searchField');
			$sValue							=	$this->input->post('searchValue');
			
			if($sField == 'users_mobile' || $sField ==  'sender_users_mobile'):
				$whereCon['where']			 	= 	array( $sField => (int)$sValue ,'record_type' => 'Debit');
				$data['searchField'] 			= 	$sField;
				$data['searchValue'] 			= 	$sValue;
			else:
				$whereCon['search']			 	= 	array('0'=>trim($sField),'1'=>trim($sValue));
				$data['searchField'] 			= 	$sField;
				$data['searchValue'] 			= 	$sValue;
			endif;
		else:
			$whereCon['where']					=	array('record_type' => 'Debit');
			$whereCon['search']		 		= 	"";
			$data['searchField'] 			= 	'';
			$data['searchValue'] 			= 	'';
		endif;

		$shortField 						= 	array('due_management_id'=> -1 );
		$tblName 							= 	'da_dueManagement';
		
		$DueManagement 						=	$this->common_model->duemanagement('multiple',$tblName,$whereCon,$shortField);
		
        $spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'Sl.No');
		$sheet->setCellValue('B1', 'Recharge By (Name)');
		$sheet->setCellValue('C1', 'Recharge By (Mobile Number)');
		$sheet->setCellValue('D1', 'Recharge By (Email)');
		$sheet->setCellValue('E1', "User (Name)");
		$sheet->setCellValue('F1', "User (Mobile Number)");
		$sheet->setCellValue('G1', "User (Email)");
		$sheet->setCellValue('H1', 'Total Recharges');
		$sheet->setCellValue('I1', 'Recharge Amount');
		$sheet->setCellValue('J1', 'Cash Collected  Amount');
		$sheet->setCellValue('K1', 'Due  Amount');
		$sheet->setCellValue('L1', 'Advance Amount');
		$slno = 1;
		$start = 2;
		foreach($DueManagement as $items){
			 	
			$sender_user_name 	= 	$items['sender_users_name'].' ' .$items['sender_last_name'];
   			$sender_mobile 	=  	$items['sender_country_code'].' ' .$items['sender_users_mobile'];  
    	    $sender_email 		= 	$items['sender_users_email']; 

    	    $user_name 	= 	$items['users_name'].' ' .$items['last_name'];
   			$mobile 	=  	$items['country_code'].' ' .$items['users_mobile'];  
    	    $Email 		= 	$items['users_email']; 

			//echo $productName;die();
			$sheet->setCellValue('A'.$start, $slno);
			$sheet->setCellValue('B'.$start, $sender_user_name);
			$sheet->setCellValue('C'.$start, $sender_mobile);
			$sheet->setCellValue('D'.$start, $sender_email);
			$sheet->setCellValue('E'.$start, $user_name);
			$sheet->setCellValue('F'.$start, $mobile);
			$sheet->setCellValue('G'.$start, $Email);
			$sheet->setCellValue('H'.$start, $items['count']);
			$sheet->setCellValue('I'.$start, $items['recharge_amt']);
			$sheet->setCellValue('J'.$start, $items['cash_collected']);
			$sheet->setCellValue('K'.$start, $items['due_amount']);
			$sheet->setCellValue('L'.$start, $items['advanced_amount']);
			
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
		$sheet->getStyle('A1:L1')->getFont()->setBold(true);		
		$sheet->getStyle('A1:L1')->applyFromArray($styleThinBlackBorderOutline);
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
		$sheet->getColumnDimension('F')->setWidth(25);
		$sheet->getColumnDimension('G')->setWidth(30);
		$sheet->getColumnDimension('H')->setWidth(30);
		$sheet->getColumnDimension('I')->setWidth(25);
		$sheet->getColumnDimension('J')->setWidth(25);
		$sheet->getColumnDimension('K')->setWidth(30);
		$sheet->getColumnDimension('L')->setWidth(30);
		

		$curdate = date('d-m-Y H:i:s');
		$writer = new Xlsx($spreadsheet);
		$filename = 'Due Mangement'.$curdate;
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
		//endif;
		/* Export excel END */
		//redirect('orders/allorders/index');
	}

	/***********************************************************************
	** Function name 	: exportexcel
	** Developed By 	: Afsar Ali
	** Purpose  		: This function used for export order data
	** Date 			: 07 JAN 2023
	** Updated Date 	: 21 February 2022
	** Updated By   	: Dilip Halder
	************************************************************************/
	function viewexportexcel($id='')
	{  
		
	   
		if(!empty($this->input->post('fromDate'))):

			$fromDate			=	date('Y-m-d 00:01 ', strtotime($this->input->post('fromDate')));  
			$whereCon['where_gte']			 	= 	array( array('created_at',$fromDate) );
			$data['fromDate'] 			= 	$fromDate;

		endif;

		if(!empty($this->input->post('toDate'))):

			$toDate			=	date('Y-m-d 23:59 ', strtotime($this->input->post('toDate')));  
			$whereCon['where_lte']			 	= 	array( array('created_at',$toDate) );

			$data['toDate'] 			= 	$toDate;

		endif;
		
		
		if($this->input->post('searchField') && $this->input->post('searchValue')):
				
			$sField							=	$this->input->post('searchField');
			$sValue							=	$this->input->post('searchValue');

			if($sField == 'users_mobile' || $sField ==  'sender_users_mobile' || $sField == 'recharge_amt' || $sField == 'recharge_amt' || $sField == 'cash_collected' || $sField == 'advanced_amount' ):
			$whereCon['where']			 	= 	array( $sField => (int)$sValue ,'record_type' => 'Debit',"recharge_type" => "Advanced Cash",'user_id_to' => (int)$id);
			$data['searchField'] 			= 	$sField;
			$data['searchValue'] 			= 	$sValue;
			else:
			$whereCon['search']			 	= 	array('0'=>trim($sField),'1'=>trim($sValue));
			$data['searchField'] 			= 	$sField;
			$data['searchValue'] 			= 	$sValue;
			endif;
		else:
			$whereCon['where']				=	array('user_id_to' => (int)$id,'record_type' => 'Debit',"recharge_type" => "Advanced Cash");
			$data['searchField'] 			= 	'';
			$data['searchValue'] 			= 	'';
		endif;

		$shortField 						= 	array('due_management_id'=> -1 );
		$tblName 							= 	'da_dueManagement';
		
		$DueManagement 						=	$this->common_model->getData('multiple',$tblName,$whereCon,$shortField);
		

      	$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'Sl.No');
	
		$sheet->setCellValue('B1', 'Recharge Amount');
		$sheet->setCellValue('C1', 'Cash Collected  Amount');
		$sheet->setCellValue('D1', 'Due Amount');
		$sheet->setCellValue('E1', 'Advance Amount');
		$sheet->setCellValue('F1', 'Recharge Date');
		$sheet->setCellValue('G1', 'Recharge Time');
		$sheet->setCellValue('H1', 'Paid Date');
		$sheet->setCellValue('I1', 'Paid Time');
		$slno = 1;
		$start = 2;
		foreach($DueManagement as $items){

			if($items['update_date']):
				$paid_date = date('Y-m-d' , strtotime($items['created_at']) );
				$paid_time = date('h:i' , strtotime($items['created_at']) );
			else:
				$paid_date = '-';
				$paid_time = '-';
			endif;


			$sheet->setCellValue('A'.$start, $slno);
			$sheet->setCellValue('B'.$start, $items['recharge_amt']);
			$sheet->setCellValue('C'.$start, $items['cash_collected']);
			$sheet->setCellValue('D'.$start, $items['due_amount']);
			$sheet->setCellValue('E'.$start, $items['advanced_amount']);

			$sheet->setCellValue('F'.$start, date('Y-m-d' , strtotime($items['created_at']) ));
			$sheet->setCellValue('G'.$start, date('h:i' , strtotime($items['created_at']) ));
			
			$sheet->setCellValue('H'.$start, $paid_date );
			$sheet->setCellValue('I'.$start, $paid_time );
			
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
		$sheet->getStyle('A1:k1')->getFont()->setBold(true);		
		$sheet->getStyle('A1:R1')->applyFromArray($styleThinBlackBorderOutline);
		//Alignment
		//fONT SIZE
		//$sheet->postStyle('A1:D10')->postFont()->setSize(12);
		//$sheet->postStyle('A1:D2')->postAlignment()->setVertical(Alignment::VERTICAL_CENTER);
		//$sheet->postStyle('A2:D1')->postAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		//Custom width for Individual Columns
		$sheet->getColumnDimension('A')->setWidth(5);
		$sheet->getColumnDimension('B')->setWidth(30);
		$sheet->getColumnDimension('C')->setWidth(30);
		$sheet->getColumnDimension('D')->setWidth(30);
		$sheet->getColumnDimension('E')->setWidth(30);
		$sheet->getColumnDimension('F')->setWidth(25);
		$sheet->getColumnDimension('G')->setWidth(30);
		$sheet->getColumnDimension('H')->setWidth(30);
		$sheet->getColumnDimension('I')->setWidth(25);
		

		$curdate = date('d-m-Y H:i:s');
		$writer = new Xlsx($spreadsheet);
		$filename = 'Due Mangement User Report'.$curdate;
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
		//endif;
		/* Export excel END */
		//redirect('orders/allorders/index');
	}						
				
}

?>
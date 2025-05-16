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

class Allrechargevoucher extends CI_Controller {

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
	 + + Purpose  		: This function used for Recharge Vocher view.
	 + + Date 			: 03 February 2023
	 + + Updated Date 	: 
	 + + Updated By   	:
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index($rcId="")
	{	

		$this->admin_model->authCheck();
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'recharge';
		$data['activeSubMenu'] 				= 	'allrechargecoupons';

		if($this->input->get('searchField') && $this->input->get('searchValue')):
			$sField							=  $this->input->get('searchField');
			$sValue							=  $this->input->get('searchValue');
			$data['searchField'] 			=  $sField;
			$data['searchValue'] 			=  $sValue;
			if(is_numeric($sValue)):
				$whereCon['where'][$sField]	=  (int)$sValue;
			else:
				$whereCon['like']	        =  array(0=> $sField,1=> $sValue );
			endif;
		endif;

		$whereCon['where']['batch_id']	    = (int)$rcId;		
		$shortField 						= array('created_date'=>'desc');
		
		$baseUrl 							= 	getCurrentControllerPath('index');
		$this->session->set_userdata('ALLCOUPONSVOUCHERDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 	'uw_coupon_code_only';
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




       if($this->uri->segment(5)):
           $page = $this->uri->segment(5);
       else:
           $page = 0;
       endif;
		
		$data['forAction'] 					= 	$baseUrl."/".$rcId; 
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
		
		// $data['ALLDATA'] 					= 	$this->common_model->getData('multiple',$tblName,$whereCon,$shortField,$perPage,$page);
		
		$tblName      = 'uw_coupon_code_only';
		$data['ALLDATA'] = $this->common_model->getrechargeDetails($tblName,$whereCon);

		$this->layouts->set_title('All Copons | Copons | UWINN');
		$this->layouts->admin_view('rechargecoupons/allrechargecoupons/viewvoucher',array(),$data);
	}	// END OF FUNCTION

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name : addeditdata
	 + + Developed By  : AFSAR ALI
	 + + Purpose  	   : This function used for Add Edit data
	 + + Date 		   : 08 APRIL 2022
	 + + Updated Date  : 
	 + + Updated By    :
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	

	/***********************************************************************
	** Function name 	: changestatus
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for change status
	** Date 			: 03 Februray 2023
	************************************************************************/
	function changestatus($rc_id='',$coupon='',$statusType='')
	{  

		$this->admin_model->authCheck('edit_data');
		$whereCondition =  array('rc_id' => $rc_id ,'coupon_code' => (int)$coupon);

		if($statusType == 'A'):
			$param['coupon_code_statys']	=	"Active";
		elseif($statusType == 'E'):
			$param['coupon_code_statys']	=	"Expire";
		else:
			$param['coupon_code_statys']	=	"Inactive";
		endif;

		$this->common_model->editDataByMultipleCondition('uw_coupon_code_only',$param,$whereCondition);
		
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		
		redirect(correctLink('ALLCOUPONSVOUCHERDATA',getCurrentControllerPath('index')));
	}

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name 	: index
	 + + Developed By 	: Dilip Halder
	 + + Purpose  		: This function used for Recharge Vocher view.
	 + + Date 			: 03 February 2023
	 + + Updated Date 	: 
	 + + Updated By   	:
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function viewredeemcoupons()
	{	

		$this->admin_model->authCheck();
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'recharge';
		$data['activeSubMenu'] 				= 	'allrechargecoupons';

		if($this->input->get('searchField') && $this->input->get('searchValue')):
			$sField							=	$this->input->get('searchField');
			$sValue							=	$this->input->get('searchValue');

			$USERwhereCon['where_or'] = array( 'users_mobile' => (int)$sValue ,'users_email' => $sValue);
			$UserDetails = $this->common_model->getData('single','uw_users',$USERwhereCon);
			$whereCon['where']		 			= 	array('coupon_code_statys'=> 'Redeem','redeemed_by_user_id' => (int)$UserDetails['users_id'] );		

			$data['searchField'] 			= 	$sField;
			$data['searchValue'] 			= 	$sValue;
		else:
			// $whereCon['like']		 		= 	"";
			$whereCon['where']		 			= 	array('coupon_code_statys'=> 'Redeem');		
			$data['searchField'] 			= 	'';
			$data['searchValue'] 			= 	'';
		endif;
				
		$shortField 						= 	array('redeemed_date'=>'desc');

		$baseUrl 							= 	base_url('recharge/allrechargevoucher/viewredeemcoupons');
		$this->session->set_userdata('ALLVIEWREDEEMCOUPONSDATE',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 	'uw_coupon_code_only';
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




       if($this->uri->segment(4)):
           $page = $this->uri->segment(4);
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
		// echo '<pre>';print_r($data['ALLDATA']);die();
		$this->layouts->set_title('All Copons | Copons | UWINN');
		$this->layouts->admin_view('rechargecoupons/allrechargecoupons/viewredeemcoupons',array(),$data);
	}	// END OF FUNCTION


	/***********************************************************************
	** Function name : exportexcel
	** Developed By  : Dilip Halder
	** Purpose  	 : This function used for export data
	** Date 		 : 10 January 2025
	************************************************************************/
	function exportexcel($batch_id = "")
	{  

		$this->admin_model->authCheck('view_data');
		// $data          	    = $this->common_model->getData('multiple','uw_coupon_code_only',$whereCon);

		$tblName      		= 'uw_coupon_code_only';
		$whereCon['where'] 	=  array('batch_id'  => (int)$batch_id); 	 
		$data 				= $this->common_model->getrechargeDetails($tblName,$whereCon);
		

		// echo "<pre>";
		// print_r($data);
		// die();


		/* Export excel button code */
        $spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'Sl.No');
		$sheet->setCellValue('B1', 'Serial No.');
		$sheet->setCellValue('C1', 'Coupon Code');
		$sheet->setCellValue('D1', 'Coupon Amount');
		$sheet->setCellValue('E1', 'Generate For');
		$sheet->setCellValue('F1', 'Created At');
		$sheet->setCellValue('G1', 'Redeemed Date');
		$sheet->setCellValue('H1', 'Redeemed By (Mobile)');
		$sheet->setCellValue('I1', 'Redeemed By (Email)');
		$sheet->setCellValue('J1', 'Coupon Status');
		
		$slno = 1;
		$start = 2;
			foreach($data as $d){
				$sheet->setCellValue('A'.$start, $slno);
				$sheet->setCellValue('B'.$start, $d['rc_id']);
				$sheet->setCellValue('C'.$start, $d['coupon_code']);
				$sheet->setCellValue('D'.$start, $d['coupon_code_amount']);
				$sheet->setCellValue('E'.$start, $d['generate_for']);

				$created_date = $d['created_date']? date('Y-m-d H:i',$d['created_date']) :'N/A' ;
				$modified_at  = $d['modified_at']? date('Y-m-d H:i',strtotime($d['modified_at'])) :'N/A' ;
				$sheet->setCellValue('F'.$start, $created_date);
				$sheet->setCellValue('G'.$start, $modified_at);
				$sheet->setCellValue('H'.$start, $d['redeemedBy_by_user_mobile']);
				$sheet->setCellValue('I'.$start, $d['redeemedBy_by_user_email']);
				$sheet->setCellValue('J'.$start, $d['coupon_code_statys']);
				
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
		$sheet->getStyle('A1:I1')->getFont()->setBold(true);		
		$sheet->getStyle('A1:I1000')->applyFromArray($styleThinBlackBorderOutline);
		//Alignment
		//fONT SIZE
		$sheet->getStyle('A1:D10')->getFont()->setSize(12);
		$sheet->getStyle('A1:D2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
		$sheet->getStyle('A2:D100')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		//Custom width for Individual Columns
		$sheet->getColumnDimension('A')->setWidth(5);
		$sheet->getColumnDimension('B')->setWidth(15);
		$sheet->getColumnDimension('C')->setWidth(30);
		$sheet->getColumnDimension('D')->setWidth(30);
		$sheet->getColumnDimension('E')->setWidth(15);
		$sheet->getColumnDimension('F')->setWidth(15);
		$sheet->getColumnDimension('G')->setWidth(15);
		$sheet->getColumnDimension('H')->setWidth(30);
		$sheet->getColumnDimension('I')->setWidth(30);
		$sheet->getColumnDimension('J')->setWidth(30);
		

		$curdate = date('d-m-Y H:i:s');
		$writer = new Xlsx($spreadsheet);
		$filename = 'Recharge-voucher-list'.$curdate;
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
		//endif;
		/* Export excel END */
	}


}

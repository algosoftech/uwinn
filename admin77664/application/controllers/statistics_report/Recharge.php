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

class Recharge extends CI_Controller {
	public function  __construct() 
	{ 
		parent:: __construct();
		error_reporting(E_ALL ^ E_NOTICE);  
		$this->load->model(array('admin_model','emailtemplate_model','sms_model','notification_model'));
		$this->lang->load('statictext', 'admin');
		$this->load->helper('common');
	} 
	/* * *********************************************************************
	* * Function name	: index
	* * Developed By 	: Afsar Ali
	* * Purpose  		: This function used for statistics list
	* * Date 			: 01 NOV 2022
	* * **********************************************************************/
	public function index()
	{	
		$this->admin_model->authCheck('view_data');
		$this->admin_model->getPermissionType($data); 
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'statistics_report';
		$data['activeSubMenu'] 				= 	'recharge';
		if($this->input->get('clearAllSearch')){
			redirect(correctLink('MASTERDATARECHARGETYPE',getCurrentControllerPath('index')));
		}
		if($this->input->get('fromDate') && $this->input->get('toDate')):
			$data['month']	 				= 	'';
		elseif($this->input->get('showLength')):
			$whereCon['where']		 		= 	["arabian_points_from" => 'Recharge'];
			$data['month']	 				= 	$this->input->get('showLength');
		else:
			$whereCon['like']		 		= 	"";
			$data['searchField'] 			= 	'';
			$data['searchValue'] 			= 	'';
			$whereCon['where']		 		= 	["arabian_points_from" => 'Recharge'];
			$data['month']	 				= 	date('m');
		endif;
		switch($data['month']){
			case 1:
				$whereCon['where_gte'] 			= 	array(array("created_at", date('Y').'-01-01'));
				$whereCon['where_lte'] 			= 	array(array("created_at", date('Y').'-02-01'));
			break;
			case 2:
				$whereCon['where_gte'] 			= 	array(array("created_at", date('Y').'-02-01'));
				$whereCon['where_lte'] 			= 	array(array("created_at", date('Y').'-03-01'));
			break;
			case 3:
				$whereCon['where_gte'] 			= 	array(array("created_at", date('Y').'-03-01'));
				$whereCon['where_lte'] 			= 	array(array("created_at", date('Y').'-04-01'));
			break;
			case 4:
				$whereCon['where_gte'] 			= 	array(array("created_at", date('Y').'-04-01'));
				$whereCon['where_lte'] 			= 	array(array("created_at", date('Y').'-05-01'));
			break;
			case 5:
				$whereCon['where_gte'] 			= 	array(array("created_at", date('Y').'-05-01'));
				$whereCon['where_lte'] 			= 	array(array("created_at", date('Y').'-06-01'));
			break;
			case 6:
				$whereCon['where_gte'] 			= 	array(array("created_at", date('Y').'-06-01'));
				$whereCon['where_lte'] 			= 	array(array("created_at", date('Y').'-07-01'));
			break;
			case 7:
				$whereCon['where_gte'] 			= 	array(array("created_at", date('Y').'-07-01'));
				$whereCon['where_lte'] 			= 	array(array("created_at", date('Y').'-08-01'));
			break;
			case 8:
				$whereCon['where_gte'] 			= 	array(array("created_at", date('Y').'-08-01'));
				$whereCon['where_lte'] 			= 	array(array("created_at", date('Y').'-09-01'));
			break;
			case 9:
				$whereCon['where_gte'] 			= 	array(array("created_at", date('Y').'-09-01'));
				$whereCon['where_lte'] 			= 	array(array("created_at", date('Y').'-10-01'));
			break;
			case 10:
				$whereCon['where_gte'] 			= 	array(array("created_at", date('Y').'-10-01'));
				$whereCon['where_lte'] 			= 	array(array("created_at", date('Y').'-11-01'));
			break;
			case 11:
				$whereCon['where_gte'] 			= 	array(array("created_at", date('Y').'-11-01'));
				$whereCon['where_lte'] 			= 	array(array("created_at", date('Y').'-12-01'));
			break;
			case 12:
				$whereCon['where_gte'] 			= 	array(array("created_at", date('Y').'-12-01'));
				$whereCon['where_lte'] 			= 	array(array("created_at", date('Y',strtotime('+1 year')).'-01-01'));
			break;
			default:
				$data['fromDate'] 				= 	$this->input->get('fromDate');
				$whereCon['where_gte'] 			= 	array(array("created_at",$data['fromDate']));
				$data['toDate'] 				= 	$this->input->get('toDate');
				$whereCon['where_lte'] 			= 	array(array("created_at",date('Y-m-d', strtotime($data['toDate'].' +1 day'))));
			break;
		}
		$data['ALLDATA']					=	$this->common_model->getRechargeStatistics($whereCon);
		$this->layouts->set_title('Recharge | Statistics | Dealz Arabia');
		$this->layouts->admin_view('statistics/recharge/index',array(),$data);
	}	// END OF FUNCTION
	/* * *********************************************************************
	 * * Function name	: getStatisticsByUserID
	 * * Developed By 	: Afsar Ali
	 * * Purpose  		: This function used for statistics list by user id
	 * * Date 			: 02 NOV 2022
	 * * **********************************************************************/
	public function getStatisticsByUserID()
	{
		$this->admin_model->authCheck();
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'statistics_report';
		$data['activeSubMenu'] 				= 	'recharge';

		if($this->input->get('clearAllSearch')){
			redirect(correctLink('MASTERDATARECHARGETYPE',getCurrentControllerPath('statistics_report/recharge/getStatisticsByUserID')));
		}
		if($this->input->get()){
			$data['showEntry'] 	= 	$this->input->get('shortBy');
			$data['email']		=	$this->input->get('email');
			$users_id 			= 	$this->common_model->getPaticularFieldByFields('users_id', 'da_users', 'users_email', trim($data['email']));
			
			if($this->input->get('shortBy') == 'Both' && strtoupper($data['email']) == 'ADMIN'){
				$whereCon['where']		 			= 	array(
															'arabian_points_from' => 'Recharge',
															'created_by' => strtoupper($this->input->get('email')),
															);
			}elseif($this->input->get('shortBy') == 'Credit' && strtoupper($data['email']) == 'ADMIN'){
				$whereCon['where']		 			= 	array(
															'arabian_points_from' => 'Recharge',
															'record_type' => $this->input->get('shortBy'),
															'created_by' => strtoupper($this->input->get('email')),
															);
			}elseif($this->input->get('shortBy') == 'Debit' && $data['email'] == 'ADMIN'){
				$whereCon['where']		 			= 	array(
															'arabian_points_from' => 'Recharge',
															'record_type' => $this->input->get('shortBy'),
															'created_by' => strtoupper($this->input->get('email')),
															);
			}elseif($this->input->get('shortBy') == 'Both' && $data['email'] !== 'ADMIN'){
				$whereCon['where']		 			= 	array(
															'arabian_points_from' => 'Recharge',
															'$or' => array( 
																array('user_id_to' => (int)$users_id, 'record_type' => 'Debit'), 
																array('user_id_cred' => (int)$users_id,'record_type' => 'Credit'), 
															));
			}elseif($this->input->get('shortBy') == 'Credit' && $data['email'] !== 'ADMIN'){
				$whereCon['where']		 			= 	array(
															'arabian_points_from' => 'Recharge',
															'record_type' => $this->input->get('shortBy'),
															'user_id_cred' => (int)$users_id,
														);
			}elseif($this->input->get('shortBy') == 'Debit' && $data['email'] !== 'ADMIN'){
				$whereCon['where']		 			= 	array(
															'arabian_points_from' => 'Recharge',
															'record_type' => $this->input->get('shortBy'),
															'user_id_to' => (int)$users_id,
														);
			}else{
				$whereCon['where']		 			= 	array(
															'arabian_points_from' => 'Recharge',
															'record_type' => $this->input->get('shortBy'),
															'$or' => array( 
																array('user_id_deb' => (int)$users_id), 
																array('user_id_cred' => (int)$users_id), 
															));
			}
		}else{
			$data['showEntry']					=	'Both';
			$data['email']						=	'ADMIN';
			$whereCon['where']		 			= 	array('arabian_points_from' => 'Recharge', 'created_by' => 'ADMIN');
		}
		if($this->input->get('searchField') && $this->input->get('searchValue')):
			$sField							=	$this->input->get('searchField');
			$sValue							=	$this->input->get('searchValue');
			$data['excelExportCondition'] 	.=	'&searchField='.$sField.'&searchValue='.$sValue;
			if($sField == 'recharge_by'):
				$users_id = $this->common_model->getPaticularFieldByFields('users_id', 'da_users', 'users_email', trim($sValue));
				$whereCon['where']['user_id_deb'] = (int)$users_id;
			elseif($sField == 'recharge_to'):
				$users_id = $this->common_model->getPaticularFieldByFields('users_id', 'da_users', 'users_email', trim($sValue));
				$whereCon['where']['user_id_cred'] = (int)$users_id;
			elseif($sField == 'record_type'):
				$whereCon['like']			 = 	array('0'=>trim($sField),'1'=>trim($sValue));
			endif;
			$data['searchField'] 			= 	$sField;
			$data['searchValue'] 			= 	$sValue;
		else:
			$whereCon['like']		 		= 	"";
			$data['searchField'] 			= 	'';
			$data['searchValue'] 			= 	'';
		endif;
		
		if($this->input->get('fromDate')){
			$data['fromDate'] 				= 	$this->input->get('fromDate');
			$whereCon['where_gte'] 			= 	array(array("created_at",$data['fromDate']));
		}
		if($this->input->get('toDate')){
			$data['toDate'] 				= 	$this->input->get('toDate');
			$whereCon['where_lte'] 			= 	array(array("created_at",$data['toDate']));
		}
		$shortField 						= 	array('created_at'=> -1);
		$baseUrl 							= 	getCurrentControllerPath('statistics_report/recharge/getStatisticsByUserID');
		$this->session->set_userdata('ALLRECHARGEDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 	'da_loadBalance';
		$con 								= 	'';
		
		$totalRows 							= 	$this->common_model->getDataByNewQuery('*','count',$tblName,$whereCon,$shortField,'0','0');
		
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
		//$data['forAction'] 					= 	$baseUrl; 
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
		$data['excelExportCondition']		= 	base64_encode(json_encode($whereCon));
		$data['ALLDATA'] 					= 	$this->common_model->getDataByNewQuery('*','multiple',$tblName,$whereCon,$shortField,$perPage,$page);
		$this->layouts->set_title('Recharge | Users Statistics | Dealz Arabia');
		$this->layouts->admin_view('statistics/recharge/recharge_list',array(),$data);
		//echo 'working';die();
	}// END OF FUNCTION

	/***********************************************************************
	** Function name 	: exportexcel
	** Developed By 	: Afsar Ali
	** Purpose  		: This function used for export deleted users data
	** Date 			: 09 APRIL 2022
	** Updated Date 	: 
	** Updated By   	: 
	************************************************************************/
	function exportexcel($load_balance_id='')
	{  
		/* Export excel button code */
		$data = base64_decode($load_balance_id);
		$where = json_decode($data,true);
		$shortField 			= 	array('created_at'=> -1);
		$tblName 				= 	'da_loadBalance';
		$data        						=   $this->common_model->getDataByNewQuery('*','multiple',$tblName,(array)$where,$shortField,0,0);
        $spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'Sl.No');
		$sheet->setCellValue('B1', 'Arabian Points');
		$sheet->setCellValue('C1', 'Record Type');
		$sheet->setCellValue('C1', 'User Type');
		$sheet->setCellValue('D1', 'Email ID');
		$sheet->setCellValue('E1', 'Recharge At');
		
		$slno = 1;
		$start = 2;
			foreach($data as $d){
				if($d['record_type'] == 'Debit'){ 
					$id = $d['user_id_to'];
				}elseif ($d['record_type'] == 'Credit' && $d['created_by'] != 'ADMIN') {
					$id = $d['user_id_cred'];
				}elseif ($d['record_type'] == 'Credit' && $d['created_by'] == 'ADMIN'){
					$id = $d['user_id_cred'];
				}
				$users_type = $this->common_model->getPaticularFieldByFields('users_type', 'da_users', 'users_id', (int)$id);
				$email = $this->common_model->getPaticularFieldByFields('users_email', 'da_users', 'users_id', (int)$id);

				$sheet->setCellValue('A'.$start, $slno);
				$sheet->setCellValue('B'.$start, $d['arabian_points']);
				$sheet->setCellValue('C'.$start, $d['record_type']);
				$sheet->setCellValue('D'.$start, $users_type);
				$sheet->setCellValue('D'.$start, $email);
				$sheet->setCellValue('E'.$start, date('d-F-Y h:i A',strtotime($d['created_at'])));	
				
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
		$sheet->getStyle('A1:E1')->getFont()->setBold(true);		
		$sheet->getStyle('A1:E1')->applyFromArray($styleThinBlackBorderOutline);
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
		$sheet->getColumnDimension('E')->setWidth(15);


		$curdate = date('d-m-Y H:i:s');
		$writer = new Xlsx($spreadsheet);
		$filename = 'Recharge-Coupon-list'.$curdate;
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
		//endif;
		/* Export excel END */
	}


}
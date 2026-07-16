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

class Orders extends CI_Controller {

	public function  __construct() 
	{ 
		parent:: __construct();
		error_reporting(E_ALL ^ E_NOTICE);  
		$this->load->model(array('admin_model','emailtemplate_model','sms_model','notification_model'));
		$this->lang->load('statictext', 'admin');
		$this->load->helper('common');
	} 

	/* * *********************************************************************
	 * * Function name : index
	 * * Developed By  : Dilip Halder
	 * * Purpose       : This function used for show order Details.
	 * * **********************************************************************/
	public function index()
	{	

		$this->admin_model->authCheck();
		$this->admin_model->getPermissionType($data); 
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'statistics_report';
		$data['activeSubMenu'] 				= 	'orders';
		

		//Product List
		$wherePCon['where']['status']  = "A";
		$wherePCon['where']['enable_raffle_ticket']  = array('$ne'=> 'Enable' );
		$data['ALLPRODUCT'] = $this->common_model->getData('multiple','uw_products',$wherePCon,array('title' => -1));
		// echo "<pre>";print_r($data['ALLPRODUCT']);die();

		
		$whereHGCon['where']['status'] = 'A';
		$data['ALLHOURLYGAME'] = $this->common_model->getData('multiple','uw_hourly_games',$whereHGCon,array('title' => 1));
		
		if($this->input->get('clearAllSearch')){
			redirect(correctLink('MASTERDATAORDERTYPE',getCurrentControllerPath('index')));
		}

		// Initialize filters and mode
		$whereCondition        = array();
		$data['productIds']    = array();
		$data['hourlyGameIds'] = array();
		$searchMode       = 'default';
		$activeFilterType = strtolower(trim((string)$this->input->get('activeFilterType')));

		// echo "<pre>";
		// print_r($activeFilterType);
		// die();


		
		if($this->input->get('productIds')){
			$productIds = $this->input->get('productIds') ? $this->input->get('productIds') : array();
			$productIds = array_values(array_filter((array)$productIds, function($id){
				return $id !== '' && $id !== null;
			}));
			$productIds = array_map(function($id) {
				return ($id <= PHP_INT_MAX) ? (int)$id : $id;
			}, $productIds);
			$data['productIds'] = $productIds;
		} 
		if($this->input->get('hourlyGameIds')){
			$hourlyGameIds = $this->input->get('hourlyGameIds') ? $this->input->get('hourlyGameIds') : array();
			$data['hourlyGameIds'] = array_values(array_filter((array)$hourlyGameIds, function($id){
				return $id !== '' && $id !== null;
			}));
		}
		

		// Strict filter isolation by active type from UI.
		if($activeFilterType === 'hourly' && !empty($data['hourlyGameIds'])) {
			$searchMode = 'hourly';
		} elseif($activeFilterType === 'product' && !empty($data['productIds'])) {
			$searchMode = 'product';
		} elseif(!empty($data['productIds'])) {
			$searchMode = 'product';
		} elseif(!empty($data['hourlyGameIds'])) {
			$searchMode = 'hourly';
		}

		if($searchMode === 'product') {
			$whereCondition['product_id']['$in'] = $data['productIds'];
		} elseif($searchMode === 'hourly') {
			$hourlyFilterIds = $data['hourlyGameIds'];
			$coutHrGame = count($hourlyFilterIds);
			

			$hourlyGamePID = array();
			foreach($data['ALLHOURLYGAME'] as $hourlyGame):
				if(in_array($hourlyGame['products_id'], $hourlyFilterIds)):
					$hourlyGamePID[] = $hourlyGame['_id']->{'$id'};
				endif;
			endforeach;

			if($coutHrGame > 1):
				$whereCondition['products_oid'] = array('$in' => array_map(function ($id) {
					return new \MongoDB\BSON\ObjectId($id);
				}, $hourlyGamePID));
			else:
				$whereCondition['products_oid'] = new \MongoDB\BSON\ObjectId($hourlyGamePID[0]);
			endif;
		}

		
		
		
		// Where conditions section.
		if($this->input->get('searchField') && $this->input->get('searchValue')):
			
			$sField							=	$this->input->get('searchField');
			$sValue							=	$this->input->get('searchValue');
			$data['searchField'] 			= 	$sField;
			$data['searchValue'] 			= 	$sValue;
			
			if($sField == 'bind_with'):

				if(is_numeric($sValue)):
				  $BindsField = 'users_mobile';
				  $BindsValue = (int)$sValue;
				else:
				  $BindsField = 'users_email';
				  $BindsValue = $sValue;
				endif;
		 	   $tblName      = "uw_users";
			   $UserId       =  $this->common_model->getPaticularFieldByFields('users_id', $tblName,$BindsField,$BindsValue);
			  
			   $BindwhereCondition['where']['bind_person_id'] =  (string)$UserId;	
			   $BindwhereCondition['status'] =  (string)'A';	
			  
			   $field 		 = 'users_id'; 	
			   $UsersList	 =  $this->common_model->getFieldInArray($field, $tblName,$BindwhereCondition);
			   $whereCondition['user_id'] =  array('$in' => array_map('intval', $UsersList));

			elseif($sField == 'app_version'):
			  	  $whereCondition[$sField] = 	$sValue;	
			else:
				if(is_numeric($sValue)):
				  $whereCondition[$sField] = 	(int)$sValue;	
				else:
				  $whereCondition[$sField] = 	$sValue;	
				endif;
			endif;
		else:
		    if($searchMode === 'hourly'):
		    	// Hourly order status values can be A / REDEEMED / CL in this collection.
		    	$whereCondition['status'] = array('$in' => array('A', 'Redeemed'));
		    else:
		    	$whereCondition['order_status'] = "Success";
		    	$whereCondition['status'] = 'A';
		    endif;
		endif;

		

		// Initialize date variables
		$StartDate = '';
		$EndDate = '';
		$data['fromDate'] = '';
		$data['toDate'] = '';
		
		if($this->input->get('fromDate')):
			$normalizedFromDate = date('Y-m-d H:i', strtotime($this->input->get('fromDate')));  // 2023-03-16 15:13
			if($searchMode === 'hourly'):
				$normalizedFromDate = date('Y-m-d H:i:00', strtotime($normalizedFromDate));
			endif;
			$data['fromDate'] 	= date('Y-m-d\TH:i:s', strtotime($normalizedFromDate)); // For datetime-local input display
			$StartDate 			= $normalizedFromDate;
		endif;
		if($this->input->get('toDate')):
			$normalizedToDate 	= date('Y-m-d H:i', strtotime($this->input->get('toDate')));  // 2023-03-16 15:13
			if($searchMode === 'hourly'):
				$normalizedToDate = date('Y-m-d H:i:59', strtotime($normalizedToDate));
			endif;
			$data['toDate'] 	= date('Y-m-d\TH:i:s', strtotime($normalizedToDate)); // For datetime-local input display
			$EndDate 			= $normalizedToDate;
		endif;

		// Hourly mode base filter by game window range
		// if($searchMode === 'hourly' && $StartDate && $EndDate):
		// 	$whereCondition['start_date'] = array('$lte' => strtotime($EndDate));
		// 	$whereCondition['expiry_date'] = array('$gte' => strtotime($StartDate));
		// endif;
		$dateArray = array();
		
		if($StartDate && $EndDate):
		//    $hours = round((strtotime($EndDate) - strtotime($StartDate) ) / (60 * 60));
			$hours =  (strtotime($EndDate) - strtotime($StartDate) ) / (60 * 60);
			if(is_float($hours)):
			$hours = (int) ceil($hours);
			else:
			$hours = (int) $hours;
			endif;
		  for($i =0; $i <=$hours; $i++):
	    	
		  	$Minutes = date('i', strtotime($StartDate) ) ;

		  	if($Minutes <=30 ):
		  		$format = 'Y-m-d H:00';
		  	else:
		  		$format = 'Y-m-d H:30';
			endif;

			if($i == 0){
				$format = date('Y-m-d H:i', strtotime($StartDate));
			}

			if($i == $hours){
				$format = date('Y-m-d H:i', strtotime($EndDate));
			}
			 
    		$date  = date($format, strtotime($StartDate . "{$i} hours") );
			$dateArray[] = $date;
		  endfor;
		

		else:
		  for($i =0; $i<=24; $i++):
		  	$Time 		 = '21:30';
		  	$CurrentTime =  date('H:i');
		  	if($CurrentTime > $Time):
	    		$date   = date('Y-m-d H:i', strtotime("{$i} hours 09:30 PM"));
		  	else:
	    		$date   = date('Y-m-d H:i', strtotime("-1 day +{$i} hours 09:30 PM"));
		  	endif;
			$dateArray[] = $date;
		  endfor;
		endif;

		// echo "<pre>";
		// print_r($dateArray);
		// die();

		$pairs = array();
		$HourReport = array();
		$tblName = ($searchMode === 'hourly') ? "uw_hourly_orders" : "uw_lotto_orders";
		$data['forAction'] = getCurrentControllerPath('index');
		for ($i = 0; $i < count($dateArray) - 1; $i++):
		    // $pairs[] = array($dateArray[$i], $dateArray[$i + 1]);
			$SelectFields = array(
			  'status' =>  1,
			//   'product_qty' => 1,
			  "product_qty"=> array(
					'$convert' => array(
						'input' => '$product_qty',
						'to' => 'int',
						'onError' => 0,
						'onNull' => 0,
					),
				),
			  'created_at' => 1,
			  'total_price'=> 1,
			  'product_id' => 1,
			  'products_id' => 1,
			  'products_oid' => 1,
			  'product_title' => 1,
			  'products_name' => 1,
			  'start_date' => 1,
			  'expiry_date' => 1,
			  'user_id' => 1,
			  'user_phone' => 1,
			  'user_email' => 1,
			  'store_name' => 1,
			  'pos_number' => 1,
			  'app_version'=> 1,
			  'order_status'=> 1
			);
			
			// Create a copy of whereCondition for this iteration and add date range
			$iterWhereCondition = $whereCondition;
			if($searchMode === 'hourly'):
				$StartDateTime = strtotime($dateArray[$i]);


				if($hours-$i == 1):
					$EndDateTime = strtotime($dateArray[$i + 1] );
				else:
					$EndDateTime = strtotime($dateArray[$i + 1] . ' -1 minutes');
				endif;
				
				// $EndDateTime = strtotime($dateArray[$i + 1] . ' -1 minutes');
				
				$stDateTime = date('Y-m-d H:i:00', $StartDateTime);	;
				$edDateTime = date('Y-m-d H:i:59', $EndDateTime);
				$stDateTime = strtotime($stDateTime); //comment for testing
				$edDateTime = strtotime($edDateTime); //comment for testing
				$iterWhereCondition['winner_uploaded_at'] = array('$gte' =>  $stDateTime, '$lte' => $edDateTime );
			else:
				// if($i == 0){
				// 	$StartDateTime = date('Y-m-d H:i', strtotime($dateArray[$i]));
				// 	$EndDateTime = $dateArray[$i + 1];
				// }
				// if($i == $hours){
				// 	echo "last";
				// 	$StartDateTime = date('Y-m-d H:i', strtotime($dateArray[$i] . ' +1 minutes'));
				// 	$EndDateTime = $dateArray[$i + 1];
				// }
				
				$EndDateTime = $dateArray[$i + 1];
				if($i == 0):
					$iterWhereCondition['created_at'] = array(
						'$gte' => date('Y-m-d H:i', strtotime($dateArray[$i])),
						'$lte' => $EndDateTime
					);
				else:
					$iterWhereCondition['created_at'] = array(
						'$gt' => $dateArray[$i],
						'$lte' => $EndDateTime
					);
				endif;

				
				
			endif;

			$groupBy = array(
	            '_id' => '$status', 
	            // 'total_order' => array('$sum' => 1) ,
	            'total_order' => array('$sum' => '$product_qty') ,
	            'sales' => array('$sum' => '$total_price')  
        	);

		if($searchMode === 'hourly'):
			$SelectFields = array(
				'status' =>  1,
				'qty' => 1,
				'created_at' => 1,
				'total_price'=> 1,
				'products_oid' => 1,
				'products_name' => 1,
				'start_date' => 1,
				'expiry_date' => 1,
				'user_id' => 1,
				'is_winner' => 1,
				'winning_amount' => 1,
			);
			$groupBy = array(
				'_id'         => '$products_name', 
				'total_order' => array('$sum' => '$qty') ,
				'product_name' => array('$first' => '$products_name'),
				'sales'       => array('$sum' => '$total_price'),
				'winning_amount' => array('$sum' => array(
					'$cond' => array(
						'if' => array('$eq' => array('$is_winner', 'Y')),
						'then' => array('$ifNull' => array('$winning_amount', 0)),
						'else' => 0
					)
				))
			);
		endif;

        	$sortBy       = array('_id' => -1);
			$hourResult = $this->geneal_model->GetGroupData($tblName,$SelectFields,$iterWhereCondition,$groupBy,$sortBy);
			if(is_array($hourResult)):
				$bucketStartTime = ($i == 0)
					? date('Y-m-d H:i', strtotime($dateArray[$i]))
					: $dateArray[$i];
				foreach($hourResult as $hrKey => $hrRow):
					$hourResult[$hrKey]['start_time'] = $bucketStartTime;
					$hourResult[$hrKey]['end_time'] = $EndDateTime;
				endforeach;
			endif;
			$HourReport[] = $hourResult;
			
			
		endfor;
		
		// echo "<pre>";
		// print_r($iterWhereCondition);
		// die();
		
		$HourReport = array_filter($HourReport);
		$data['HourReport']	= $HourReport;
		$data['searchMode']	= $searchMode;
		$this->layouts->set_title('Statistics Report | Order | UWINN');
		$this->layouts->admin_view('statistics/orders/index',array(),$data);
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
		$data['activeSubMenu'] 				= 	'orders';

		if($this->input->get('clearAllSearch')){
			redirect(correctLink('MASTERDATAORDERTYPE',getCurrentControllerPath('statistics_report/orders/getStatisticsByUserID')));
		}
		if($this->input->get()){
			$data['showEntry'] 	= 	$this->input->get('shortBy');
			$data['email']		=	$this->input->get('email');		
			$whereCon['where']		 			= 	array('user_email' => $data['email']);
		}else{
			$whereCon['where']		 			= 	array('user_email' => 'abcd');
		}
		
		if($this->input->get('fromDate')){
			$data['fromDate'] 				= 	$this->input->get('fromDate');
			$whereCon['where_gte'] 			= 	array(array("created_at",$data['fromDate']));
		}
		if($this->input->get('toDate')){
			$data['toDate'] 				= 	$this->input->get('toDate');
			$whereCon['where_lte'] 			= 	array(array("created_at",date('Y-m-d',strtotime($data['toDate'].'+1 Days'))));
		}
		$shortField 						= 	array('created_at'=> -1);
		$baseUrl 							= 	getCurrentControllerPath('statistics_report/orders/getStatisticsByUserID');
		$this->session->set_userdata('ALLRECHARGEDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	!empty($qStringdata[1]) ? '?'.$qStringdata[1] : '';
		$tblName 							= 	'da_orders';
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
		$data['excelExportCondition']		= 	base64_encode(json_encode($whereCon));
		$data['ALLDATA'] 					= 	$this->common_model->getDataByNewQuery('*','multiple',$tblName,$whereCon,$shortField,$perPage,$page);
		//echo '<pre>'; print_r($data);die();
		$this->layouts->set_title('Recharge | Users Statistics | UWINN');
		$this->layouts->admin_view('statistics/orders/order_list',array(),$data);
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
		//echo '<pre>';
		//print_r($where);die;
		$shortField 			= 	array('created_at'=> -1);
		$tblName 				= 	'da_orders';
		$data        						=   $this->common_model->getDataByNewQuery('*','multiple',$tblName,(array)$where,$shortField,0,0);
		//echo '<pre>';print_r($data);die();
        $spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'Sl.No');
		$sheet->setCellValue('B1', 'Order ID');
		$sheet->setCellValue('C1', 'Product Name');
		$sheet->setCellValue('D1', 'QTY');
		$sheet->setCellValue('E1', 'Donated');
		$sheet->setCellValue('F1', 'Purchase Date');
		$sheet->setCellValue('G1', 'Total Amount');
		
		$slno = 1;
		$start = 2;
			foreach($data as $d){
				$ordDetails = $this->common_model->getDataByParticularField('da_orders_details', 'order_id', $d['order_id']);
				$sheet->setCellValue('A'.$start, $slno);
				$sheet->setCellValue('B'.$start, $d['order_id']);
				$sheet->setCellValue('C'.$start, $ordDetails['product_name']);
				$sheet->setCellValue('D'.$start, $ordDetails['quantity']);
				if($ordDetails['is_donated'] == 'Y'){
					$sheet->setCellValue('E'.$start, 'Yes');
				}else{
					$sheet->setCellValue('E'.$start, 'No');
				}
				$sheet->setCellValue('F'.$start, date('d-F-Y h:i A',strtotime($d['created_at'])));	
				$sheet->setCellValue('G'.$start, $d['total_price']);
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
		$sheet->getStyle('A1:G1')->getFont()->setBold(true);		
		$sheet->getStyle('A1:G1')->applyFromArray($styleThinBlackBorderOutline);
		//Alignment
		//fONT SIZE
		//$sheet->getStyle('A1:D10')->getFont()->setSize(12);
		//$sheet->getStyle('A1:D2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
		//$sheet->getStyle('A2:D1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		//Custom width for Individual Columns
		$sheet->getColumnDimension('A')->setWidth(5);
		$sheet->getColumnDimension('B')->setWidth(30);
		$sheet->getColumnDimension('C')->setWidth(30);
		$sheet->getColumnDimension('D')->setWidth(10);
		$sheet->getColumnDimension('E')->setWidth(15);
		$sheet->getColumnDimension('F')->setWidth(25);
		$sheet->getColumnDimension('G')->setWidth(15);


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
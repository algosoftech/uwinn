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
use PhpOffice\PhpSpreadsheet\Style\Protectiexportexcelon;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\ColumnDimension;
use PhpOffice\PhpSpreadsheet\Worksheet;


class Alllottoorders extends CI_Controller {

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
	 + + Developed By 	: Dilip Halder
	 + + Purpose  		: This function used for index
	 + + Date 			: 30 January 2024
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index()
	{	
		$this->admin_model->authCheck('view_data');
		$data['error'] 		   = '';
		$data['activeMenu']    = 'uwin';
		$data['activeSubMenu'] = 'alllottooders';

		if($this->input->get('fromDate')):
			$fromDate	 = date('Y-m-d H:i', strtotime($this->input->get('fromDate')));
		else:
			$fromDate    = date('Y-m-d 22:01', strtotime('-1 day'));
		endif;
		if($this->input->get('toDate')):
			$toDate	 	 = date('Y-m-d H:i', strtotime($this->input->get('toDate')));
		else:
			$toDate	 	 = date('Y-m-d 22:00');
		endif;
		$data['combinedFromDate'] = date('Y-m-d 16:00', strtotime('-1 day'));
		$data['combinedToDate']   = date('Y-m-d 22:00');
		
		$searchField   = $this->input->get('searchField');
		$searchValue   = $this->input->get('searchValue');
		 

		if($searchField == 'status'):
			if($fromDate):
				$whereCon['where']['update_date']['$gte']  =  strtotime($fromDate);
			endif;
			if($toDate):
				$whereCon['where']['update_date']['$lte']  =  strtotime($toDate);
			endif;
		else:
			if($fromDate):
				$whereCon['where']['created_at']['$gte']  =  $fromDate;
			endif;
			if($toDate):
				$whereCon['where']['created_at']['$lte']  =  $toDate;
			endif;

		endif;

		$data['searchField'] 			= $searchField;
		$data['searchValue'] 			= $searchValue;
		$data['fromDate'] 				= $fromDate;  
		$data['toDate'] 				= $toDate;

		// Where conditions section.
		if(!empty($searchField) && !empty($searchValue)):
			if($searchField == 'ticket'):
			 	$whereCon['where']	 = 	array($searchField=> "[[".$searchValue."]]" );
			elseif($searchField == 'order_code'):
				$whereCon['where']		 	= 	array($searchField=>  base64_encode($searchValue));	
			else:
			  	$whereCon['where'][$searchField] =  is_numeric($searchValue)?(int)$searchValue:$searchValue;
			endif;

		else:
			$whereCon['where']['order_status'] = array('$ne' => 'Initialize');
		endif;
			$whereCon['where']['raffle_mode'] = array('$ne' => 'Y');
		$shortField 						= 	array('sequence_id'=> -1);
		$baseUrl 							= 	getCurrentControllerPath('index');
		
		$this->session->set_userdata('ALLORDERSDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 	'uw_lotto_orders';
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
	 
		$OrdersDetails	= $data['ALLDATA']  = 	$this->common_model->getData('multiple',$tblName,$whereCon,$shortField,$perPage,$page);

		//Product List (Lotto)
		$wherePCon['where']['status']  = "A";
		$wherePCon['where']['enable_raffle_ticket']  = array('$ne'=> 'Enable' );
		$data['ALLPRODUCT'] = $this->common_model->getData('multiple','uw_products',$wherePCon,array('title' => -1));

		$whereHGCon['where']['status'] = 'A';
		$data['ALLHOURLYGAME'] = $this->common_model->getData('multiple','uw_hourly_games',$whereHGCon,array('title' => 1));

		// echo '<pre>';print_r($data);die();
		$this->layouts->set_title('U Win | UWINN');
		$this->layouts->admin_view('uwin/allorders/index',array(),$data);
	}	// END OF FUNCTION

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name : addeditdata
	 + + Developed By  : Dilip Halder
	 + + Purpose  	   : This function used for Add Edit data
	 + + Date 		   : 30 January 2024
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function addeditdata($editId='')
	{		
		$data['error'] 		   = '';
		$data['activeMenu']    = 'uwin';
		$data['activeSubMenu'] = 'alllottooders';
		
		if($editId):
			$data['orderData']				=	$this->common_model->getDataByParticularField('uw_lotto_orders','order_id',$editId);
		else:
			redirect(correctLink('ALLORDERSDATA',getCurrentControllerPath('index')));
		endif;
		
		$this->layouts->set_title('U Win | UWINN');
		$this->layouts->admin_view('uwin/allorders/addeditdata',array(),$data);
	}	// END OF FUNCTION	

	/***********************************************************************
	** Function name 	: changestatus
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for change status
	** Date 			: 30 January 2024
	************************************************************************/
	function changestatus($changeStatusId='',$statusType='')
	{  
		$this->admin_model->authCheck('edit_data');
		$param['status']		=	$statusType;
		$this->common_model->editData('uw_category',$param,'category_id',(int)$changeStatusId);
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		
		redirect(correctLink('ALLORDERSDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name 	: cancelationorder
	** Developed By 	: Dilip Kumar Halder
	** Purpose  		: This function used for order cancelation by admin.
	** Date 			: 30 January 2024
	************************************************************************/
	function cancelationorder($changeStatusId='')
	{  
		
		$this->admin_model->authCheck('edit_data');
		// $this->common_model->editData('uw_category',$param,'category_id',(int)$changeStatusId);
		$tblName 				= 'uw_orders';
		$whereCon['where']		=	 array('order_id' => $changeStatusId );
		$shortField 			= array('sequence_id' => -1);
		$cancleOrderData 		= 	$this->common_model->getData('single',$tblName,$whereCon,$shortField,'0','0');

		if($cancleOrderData['status']  == 'CL'):
			$this->session->set_flashdata('alert_error',lang('ALREADY_CANCELLED'));
			redirect(correctLink('ALLORDERSDATA',getCurrentControllerPath('index')));
		endif;
		
			//Adding calcelation variable and value.
			$param1['status']			= 'CL';
			$param1['update_ip']		=	currentIp();
			$param1['update_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
			$param1['refund_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
			$param1['updated_by']		=	(int)$this->session->userdata('UW_ADMIN_ID');


			$this->common_model->editData('uw_lotto_orders',$param1,'order_id',$changeStatusId);
			$whereConCancel	= array('order_id' => $cancleOrderData['order_id']);
			$this->common_model->editMultipleDataByMultipleCondition('uw_raffle_eligible_orders',$param1,$whereConCancel);		

			// Checking Sender User.
			$userid = $cancleOrderData['user_id'];
			
			$tblName 				=   'uw_users';
			$whereCon['where']		=	array('users_id' => $userid , 'status'=> 'A' );
			$shortField 			=   array('users_id' => -1);
			$UserData 			= 	$this->common_model->getData('single',$tblName,$whereCon,$shortField,'0','0');
			if($cancleOrderData['user_type'] == "Users"){
				$message = 'Order ID '.$cancleOrderData['order_id'].' has been canceled as the order was incomplete.';
				$title = 'Order Canceled: Incomplete Details ('.$cancleOrderData['order_id'].')';
				$this->common_model->saveNotifications($userid,$title,$message,$cancleOrderData['order_id']);
			}
			
			$refund_amount = $cancleOrderData['total_price'];
			/* Load Balance Table -- after buy product*/
		    $refundparam["load_balance_id"]			=	(int)$this->common_model->getNextSequence('uw_loadBalance');
		    $refundparam['order_id']				=	$cancleOrderData['order_id'];
			$refundparam["user_id_cred"] 			=	(int)$userid;
			$refundparam["user_id_deb"]				=	(int)0;
			$refundparam["arabian_points"] 			=	(float)$refund_amount;
			$refundparam["availableArabianPoints"] 	=	(float)$UserData["availableArabianPoints"];
	        $refundparam["end_balance"] 			=	(float)$UserData["availableArabianPoints"] + (float)$refund_amount ;
		    $refundparam["arabian_points_from"] 	=	'Refund';
		    $refundparam["record_type"] 			=	'Credit';
		    $refundparam["remarks"]					=	"Arabian points added from admin.";
		    $refundparam["creation_ip"] 			=	$this->input->ip_address();
		    $refundparam["created_at"] 				=	date('Y-m-d H:i');
		    $refundparam["created_by"] 				=	"Admin";
		    $refundparam["status"] 					=	"A";
		    $refundparam["created_user_id"] 		=	 (int)$this->session->userdata('UW_ADMIN_ID');

		    $this->common_model->addData('uw_loadBalance', $refundparam);

			// Refunded Sender Cancelation Order Amount.
			$param['availableArabianPoints'] = $UserData['availableArabianPoints'] + $cancleOrderData['total_price'];
			$this->common_model->editData('uw_users',$param,'users_id',(int)$userid);	

			$this->session->set_flashdata('alert_success',lang('ordercenclesuccess'));
			redirect(correctLink('ALLORDERSDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name 	: deletedata
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for delete data
	** Date 			: 14 JULY 2022
	************************************************************************/
	function deletedata($deleteId='')
	{  
		
		$this->admin_model->authCheck('delete_data');
		$this->common_model->deleteData('uw_category','category_id',(int)$deleteId);
		$this->session->set_flashdata('alert_success',lang('deletesuccess'));
		
		redirect(correctLink('ALLORDERSDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name 	: exportexcel
	** Developed By 	: Dilip halder
	** Purpose  		: This function used for export order data
	** Date 			: 26 January 2024
	** Updated By     	: Dilip Halder
	** Updated Date     : 26 January 2024
	************************************************************************/
	// function exportexcel()
	// {	
	// 	$this->admin_model->authCheck('view_data');
	// 	//Generating Logs
	// 	$this->common_model->generateLogs();

	// 	// ---------------------------------Date query start---------------------------------//
	// 	if($this->input->post('fromDate')):
	// 		$fromDate	 = date('Y-m-d H:i', strtotime($this->input->post('fromDate')));
	// 	endif;
	// 	if($this->input->post('toDate')):
	// 		$toDate	 	 = date('Y-m-d H:i', strtotime($this->input->post('toDate')));
	// 	endif;
	// 	$searchField     = $this->input->post('searchField');
	// 	$searchValue     = $this->input->post('searchValue');
	// 	$cancelled_order = $this->input->post('cancelled_order');
	// 	$draw_one 			 = $this->input->post('draw_time_one');
	// 	$draw_two 			 = $this->input->post('draw_time_two');
	// 	if($searchField == 'status'):
	// 		if($fromDate):
	// 			$whereCondition['where']['update_date']['$gte']  =  strtotime($fromDate);
	// 		endif;
	// 		if($toDate):
	// 			$whereCondition['where']['update_date']['$lte']  =  strtotime($toDate);
	// 		endif;
	// 	else:
	// 		if($fromDate):
	// 			$whereCondition['where']['created_at']['$gte']  =  $fromDate;
	// 		endif;
	// 		if($toDate):
	// 			$whereCondition['where']['created_at']['$lte']  =  $toDate;
	// 		endif;
	// 	endif;

	// 	if($cancelled_order == 'on'):
	// 		$whereCondition['where']['status']['$eq']  =  'CL';
	// 	else:
	// 		$whereCondition['where']['status']['$ne']  =  'CL';
	// 	endif;

	// 	// ------------------------------Draw TIme Filter------------------------------------//
	// 	if($draw_one === 'on' || $draw_two === 'on'):
	// 		$drawDetailsOption['where']['creation_date']['$gte']  =  strtotime($fromDate);
	// 		$drawDetailsOption['where']['creation_date']['$lte']  =  strtotime($toDate);
	// 		if($draw_one === 'on'):
	// 			$drawDetailsOption['where']['draw_time']  =  '22:00';
	// 		else:
	// 			$drawDetailsOption['where']['draw_time']  =  '23:30';
	// 		endif;
	// 		$drawDetailsData	 =  $this->common_model->getData('multiple','uw_products_draw_records',$drawDetailsOption);
	// 		if($drawDetailsData):
	// 			$drawIds = array_column($drawDetailsData, "draw_id");
	// 			$whereCondition['where']['draw_id']['$in']  =  $drawIds;
	// 		endif;
	// 	endif;
	// 	// -----------------------------------------------------------------------------//

		

	// 	// ---------------------------------Date query end---------------------------------//
	// 	if(!empty($searchField) && !empty($searchValue)):
	// 		if($searchField == 'ticket'):
	// 			$whereCondition['where']		 	   =  array($searchField => "[[".$searchValue."]]" );

	// 		elseif($searchField == "available_coupon"):
	// 			// $whereCon['where']		 			= 	array($sField=> "[[".$sValue."]]" );	

	// 			$tblName 	 		=  'uw_uwin_available_coupons';
	// 			$shortField  		=  array('products_id'=> -1);
	// 			$whereCon['where']  =  array('products_id' => (int)$sValue);
	// 			$result	 			=  $this->common_model->getData('single',$tblName,$whereCon,$shortField);	
	// 		else:
	// 			$whereCondition['where'][$searchField] =  is_numeric($searchValue)?(int)$searchValue:$searchValue;
	// 		endif;
	// 	else:
	// 		$whereCondition['where']['order_status']   = array('$ne' => 'Initialize');
	// 	endif;
	// 		$whereCondition['where']['raffle_mode'] = array('$ne' => 'Y');

	// 	// echo "<pre>";
	// 	// print_r($whereCondition);
	// 	// die();
		
	// 	$resultType   = "count";
	// 	$totalRows 	  = $this->common_model->getOrderDetails($resultType,$whereCondition);
	// 	$itemsPerPage = 5000;
	// 	// ---------------------------------------------

	// 	$longArray = $totalRows;
		
	// 	$pageno       = $this->input->get('page');
	// 	// Current page number (received from URL query parameter, e.g., ?page=2)
	// 	$page = isset($pageno) ? (int)$pageno : 1;

	// 	// Calculate total number of pages
	// 	$totalPages = ceil($longArray / $itemsPerPage);
	// 	$totalpage= array();
	// 	// Pagination links
	// 	for ($i = 1; $i <= $totalPages; $i++) {
	// 	    if ($i == $page) {
	// 	         $current_page = $i;
	// 	         $totalpage[] = $i;
	// 	    } else {
	// 	         $totalpage[] = $i;
	// 	    }
	// 	}
 		
 	// 	$startIndex  = ($page - 1) * $itemsPerPage;
 	// 	// $resultType  = '';
	// 	// $OrderData 	 = $this->common_model->getOrderDetails($resultType,$whereCondition,$startIndex,$itemsPerPage);
		
	// 	$totalpage 				 = count($totalpage);
	// 	$data['current_page']    = $current_page;
	// 	$data['total_page'] 	 = $totalpage;
	// 	$data['searchField'] 	 =   $searchField;
	// 	$data['searchValue'] 	 =   $searchValue;
	// 	$data['fromDate'] 		 =   $fromDate;
	// 	$data['toDate'] 		 =   $toDate;
	// 	$data['cancelled_order'] =   $cancelled_order;
	// 	$data['draw_one'] 		 =   $draw_one;
	// 	$data['draw_two'] 		 =   $draw_two;
	// 	// echo "<pre>";print_r($data);die();
	// 	$this->layouts->set_title('Export CSV | UWINN');
	// 	$this->layouts->admin_view('uwin/allorders/exportexcel',array(),$data);		 

	// }	// END OF FUNCTION

	/***********************************************************************
	** Function name 	: exportexcelApi
	** Developed By 	: Dilip halder
	** Purpose  		: This function used for export order data
	** Date 			: 24 July 2024
	** Updated By     	: Dilip Halder
	** Updated Date     : 26 January 2024
	************************************************************************/
	// function exportexcelApi(){
	// 	$this->admin_model->authCheck('view_data');

	// 	// ---------------------------------Date query start---------------------------------//
	// 	if($this->input->post('fromDate')):
	// 		$fromDate	 = date('Y-m-d H:i', strtotime($this->input->post('fromDate')));
	// 	endif;
	// 	if($this->input->post('toDate')):
	// 		$toDate	 	 = date('Y-m-d H:i', strtotime($this->input->post('toDate')));
	// 	endif;
	// 	$searchField     = $this->input->post('searchField');
	// 	$searchValue     = $this->input->post('searchValue');
	// 	$cancelled_order = $this->input->post('cancelled_order');
	// 	$draw_one 			 = $this->input->post('draw_time_one');
	// 	$draw_two 			 = $this->input->post('draw_time_two');
	// 	if($searchField == 'status'):
	// 		if($fromDate):
	// 			$whereCondition['where']['update_date']['$gte']  =  strtotime($fromDate);
	// 		endif;
	// 		if($toDate):
	// 			$whereCondition['where']['update_date']['$lte']  =  strtotime($toDate);
	// 		endif;
	// 	else:
	// 		if($fromDate):
	// 			$whereCondition['where']['created_at']['$gte']  =  $fromDate;
	// 		endif;
	// 		if($toDate):
	// 			$whereCondition['where']['created_at']['$lte']  =  $toDate;
	// 		endif;
	// 	endif;

	// 	if($cancelled_order == 'on'):
	// 		$whereCondition['where']['status']['$eq']  =  'CL';
	// 	else:
	// 		$whereCondition['where']['status']['$ne']  =  'CL';
	// 	endif;

	// 	// ------------------------------Draw TIme Filter------------------------------------//
	// 	if($draw_one === 'on' || $draw_two === 'on'):
	// 		$drawDetailsOption['where']['creation_date']['$gte']  =  strtotime($fromDate);
	// 		$drawDetailsOption['where']['creation_date']['$lte']  =  strtotime($toDate);
	// 		if($draw_one === 'on'):
	// 			$drawDetailsOption['where']['draw_time']  =  '22:00';
	// 		else:
	// 			$drawDetailsOption['where']['draw_time']  =  '23:30';
	// 		endif;
	// 		$drawDetailsData	 =  $this->common_model->getData('multiple','uw_products_draw_records',$drawDetailsOption);
	// 		if($drawDetailsData):
	// 			$drawIds = array_column($drawDetailsData, "draw_id");
	// 			$whereCondition['where']['draw_id']['$in']  =  $drawIds;
	// 		endif;
	// 	endif;
	// 	// -----------------------------------------------------------------------------//

		

	// 	// ---------------------------------Date query end---------------------------------//
	// 	if(!empty($searchField) && !empty($searchValue)):
	// 		if($searchField == 'ticket'):
	// 			$whereCondition['where']		 	   =  array($searchField => "[[".$searchValue."]]" );

	// 		elseif($searchField == "available_coupon"):
	// 			// $whereCon['where']		 			= 	array($sField=> "[[".$sValue."]]" );	

	// 			$tblName 	 		=  'uw_uwin_available_coupons';
	// 			$shortField  		=  array('products_id'=> -1);
	// 			$whereCon['where']  =  array('products_id' => (int)$sValue);
	// 			$result	 			=  $this->common_model->getData('single',$tblName,$whereCon,$shortField);	
	// 		else:
	// 			$whereCondition['where'][$searchField] =  is_numeric($searchValue)?(int)$searchValue:$searchValue;
	// 		endif;
	// 	else:
	// 		$whereCondition['where']['order_status']   = array('$ne' => 'Initialize');
	// 	endif;
	// 		$whereCondition['where']['raffle_mode'] = array('$ne' => 'Y');


	// 	// $page = $this->input->post('pageno');
	// 	$page = $this->input->post('pageno');
	// 	// $page = 1;
 	// 	$itemsPerPage = 5000;
 	// 	$startIndex   = ($page - 1)*$itemsPerPage;
 	// 	$resultType   = '';
	// 	$OrderData 	  = $this->common_model->getOrderDetails($resultType,$whereCondition,$startIndex,$itemsPerPage);

	// 	$CSVData 	  = array();
	// 	$sno = 1;
	// 	foreach($OrderData as $index => $itemsArray):

	// 		if($itemsArray['status'] == "CL"):
	// 			$createdAt   = date('Y-m-d H:i', $itemsArray['update_date']);	
	// 			$OrderStatus = "Cancelled";
	// 		elseif($itemsArray['order_status']):
	// 			$createdAt   = $itemsArray['created_at'];
	// 			$OrderStatus = $itemsArray['order_status'];
	// 		endif;

	// 		// $ticket   		   = json_decode($itemsArray['ticket']);
	// 		$selection_values  = json_decode($itemsArray['selection_values']);
	// 		$selection_values  = json_decode($itemsArray['selection_values']);
	// 		$Ticket = str_replace('[[', '', $itemsArray['ticket']);
    //         $Ticket = str_replace(']]', '/', $Ticket);
    //         $Ticket = str_replace('],[', '/', $Ticket);
    //         $ticket = array_filter(explode('/', $Ticket));

    //         if($itemsArray['super_ball_mode'] == 'Y'):
    //              $Tickect2 = str_replace('[', '', $itemsArray['sb_tickect']);
    //              $Tickect2 = str_replace(']', '', $Tickect2);
    //              $Tickect2 = array_filter(explode(',', $Tickect2));
    //         endif;

	// 		if($ticket):
	// 			foreach ($ticket as $subindex => $item):
	// 				if($itemsArray['super_ball_mode'] == 'Y'):
	// 			  		$coupon = $item.','.$Tickect2[$subindex];
	// 				else:
	// 			  	   $coupon = $item;
	// 				endif;
	// 		  		$coupon = rtrim($coupon, ",");
	// 		  		$coupon = str_replace(' ', '', $coupon);

	// 			  	// $coupon = implode(',', $item);
	// 			  	if(!empty($itemsArray['selection_values'])):
	// 			  		$straight = $selection_values[$subindex][0]?1:0;
	// 				  	$rumble   = $selection_values[$subindex][1]?1:0;
	// 				  	$reverse  = $selection_values[$subindex][2]?1:0;
	// 			  	else:
	// 				  	$straight = $itemsArray['straight_add_on_amount']?1:0;
	// 				  	$rumble   = $itemsArray['rumble_add_on_amount']?1:0;
	// 				  	$reverse  = $itemsArray['reverse_add_on_amount']?1:0;
	// 			  	endif;
				   
	// 			  	if($itemsArray['seller_details']):
	// 			    	$seller_details = json_decode($itemsArray['seller_details']);
	// 			    	$words = explode(' ', $seller_details->Country);
	//                     $initials = '';
	//                     $countryPrefrx = '';
	//                     foreach ($words as $word):
	//                      $countryPrefrx .= $word[0];
	//                     endforeach;

	// 			  	else:
	// 			  		$seller_details = '';
	// 			  	endif;

	// 				if($itemsArray['users_type'] == 'Users'):
	// 					$itemsArray['bindwith_first_name'] = 'Admin';
	// 					$seller_Store = $itemsArray['users_name'];
	// 				else:
	// 					$seller_Store = $itemsArray['store_name'];
	// 				endif;

	// 		  		$seller_POS 		  = isset($seller_details->posid)    ? $countryPrefrx.'_'.$seller_details->posid : (isset($itemsArray['pos_number']) ? $itemsArray['pos_number'] : 'N/A');
	// 		  		$seller_Name 		  = isset($seller_details->Name)     ? $seller_details->Name : (isset($itemsArray['users_name']) ? $itemsArray['users_name'] : 'N/A');
	// 		  		$seller_Mobile 		  = isset($seller_details->FoMobile) ? $seller_details->FoMobile : (isset($itemsArray['user_phone']) ? $itemsArray['user_phone'] : 'N/A');
	// 		  		$seller_Store 		  = isset($seller_details->Name) 	 ? $seller_details->Name : (isset($itemsArray['store_name']) ? $itemsArray['store_name'] : 'N/A');
	// 		  		$seller_Bindwith_Name = isset($seller_details->FoName)   ? $seller_details->FoName : (isset($itemsArray['bindwith_first_name']) ? $itemsArray['bindwith_first_name'] : 'N/A');

	// 		  		if( $seller_Bindwith_Name == 'N/A' &&  !empty($itemsArray['admin_bindwith_users_type']) ):
	// 		  			$seller_Bindwith_Name = $itemsArray['admin_bindwith_users_type'];
	// 		  		endif;

	// 				if($itemsArray['users_type'] == 'Users'):
	// 					$seller_Store = $itemsArray['users_name'];
	// 				endif;
 
	// 	  		    // $CSVData1['Sl.No']              = $sno++;
	// 			    $CSVData1['POS No.']            = !empty($itemsArray['pos_number']) ? $itemsArray['pos_number'] : 'N/A';
	// 				$CSVData1['Order ID']           = !empty($itemsArray['order_id']) ? $itemsArray['order_id'] : 'N/A';
	// 				$CSVData1['Product Name']       = !empty($itemsArray['product_name']) ? $itemsArray['product_name'] : 'N/A';
	// 				$CSVData1['Store Name']         = !empty($seller_Store) ? $seller_Store : 'N/A';
	// 				if($itemsArray['users_type'] == 'Users'):
	// 				 $CSVData1['Seller Name']        = !empty($itemsArray['users_address']) ? $itemsArray['users_address'] : 'N/A';
	// 				else:
	// 				 // $CSVData1['Seller Name']        = !empty($seller_Name) ? $seller_Name : 'N/A';
	// 				 $CSVData1['Seller Name']        = !empty($itemsArray['users_area']) ? $itemsArray['users_area'] : 'N/A';
	// 				endif;
	// 				 // $CSVData1['Seller Name']        = !empty($seller_Name) ? $seller_Name : 'N/A';
	// 				$CSVData1['Seller Mobile']      = !empty($seller_Mobile) ? $seller_Mobile : 'N/A';
	// 				$CSVData1['Bind With']          = !empty($seller_Bindwith_Name) ? $seller_Bindwith_Name : 'N/A';
	// 				$CSVData1['Straight Amount']    = !empty($straight) ? $straight : '0';
	// 				$CSVData1['Rumble Amount']      = !empty($rumble)   ? $rumble   : '0';
	// 				$CSVData1['Chance Amount']      = !empty($reverse)  ? $reverse  : '0';
	// 				$CSVData1['Payment Status']     = !empty($OrderStatus) ? $OrderStatus : 'N/A';
	// 				$CSVData1['Purchase Date']      = !empty($createdAt) ? $createdAt : 'N/A';
	// 				$CSVData1['Coupons']      	    = !empty($coupon) ? $coupon : 'N/A';
	// 				array_push($CSVData, $CSVData1);
	// 			endforeach;
	// 		endif;
	// 	endforeach;

	// 	echo json_encode($CSVData);
	// 	die();
	// }

		/***********************************************************************
	** Function name 	: exportexcel
	** Developed By 	: Dilip halder
	** Purpose  		: This function used for export order data
	** Date 			: 26 January 2024
	** Updated By     	: Dilip Halder
	** Updated Date     : 26 January 2024
	************************************************************************/
	private function buildLottoExportFilterFromPost()
	{
		$whereCondition = array('where' => array());
		$fromDate = '';
		$toDate = '';

		if($this->input->post('fromDate')):
			$fromDate = date('Y-m-d H:i', strtotime($this->input->post('fromDate')));
		endif;
		if($this->input->post('toDate')):
			$toDate = date('Y-m-d H:i', strtotime($this->input->post('toDate')));
		endif;

		$searchField     = $this->input->post('searchField');
		$searchValue     = $this->input->post('searchValue');
		$cancelled_order = $this->input->post('cancelled_order');
		$productIds = isset($_POST['productIds']) ? (array)$_POST['productIds'] : array();
		$productIds = array_map(function($id) { return ($id <= PHP_INT_MAX) ? (int)$id : $id; }, $productIds);

		if($searchField == 'status'):
			if($fromDate):
				$whereCondition['where']['update_date']['$gte'] = strtotime($fromDate);
			endif;
			if($toDate):
				$whereCondition['where']['update_date']['$lte'] = strtotime($toDate);
			endif;
		else:
			if($fromDate):
				$whereCondition['where']['created_at']['$gte'] = $fromDate;
			endif;
			if($toDate):
				$whereCondition['where']['created_at']['$lte'] = $toDate;
			endif;
		endif;

		if(!empty($searchField) && !empty($searchValue)):
			if($searchField == 'ticket'):
				$whereCondition['where'] = array($searchField => "[[".$searchValue."]]");
			elseif($searchField == "available_coupon"):
				// keep existing behavior untouched for this special case
			else:
				$whereCondition['where'][$searchField] = is_numeric($searchValue) ? (int)$searchValue : $searchValue;
			endif;
		else:
			$whereCondition['where']['order_status'] = array('$ne' => 'Initialize');
		endif;

		$whereCondition['where']['raffle_mode'] = array('$ne' => 'Y');
		if($cancelled_order == 'on'):
			$whereCondition['where']['status']['$eq'] = 'CL';
		else:
			$whereCondition['where']['status']['$ne'] = 'CL';
		endif;
		if(!empty($productIds)):
			$whereCondition['where']['product_id']['$in'] = $productIds;
		endif;

		return array(
			'whereCondition' => $whereCondition,
			'fromDate' => $fromDate,
			'toDate' => $toDate,
			'searchField' => $searchField,
			'searchValue' => $searchValue,
		);
	}

	private function buildLottoSheetRows(array $whereCondition, $page = null, $startSno = 1)
	{
		$rows = array();
		$sno = (int)$startSno;
		$itemsPerPage = 5000;
		$currentPage = ($page !== null) ? (int)$page : 1;
		$maxPage = ($page !== null) ? (int)$page : null;
		while(true):
			$startIndex = ($currentPage - 1) * $itemsPerPage;
			$orderData = $this->common_model->getOrderDetails('', $whereCondition, $startIndex, $itemsPerPage);
			if(!is_array($orderData) || empty($orderData)):
				break;
			endif;

			foreach($orderData as $itemsArray):
			if(($itemsArray['status'] ?? '') == "CL"):
				$createdAt = !empty($itemsArray['update_date']) ? date('Y-m-d H:i', $itemsArray['update_date']) : 'N/A';
				$orderStatus = "Cancelled";
			else:
				$createdAt = $itemsArray['created_at'] ?? 'N/A';
				$orderStatus = $itemsArray['order_status'] ?? 'N/A';
			endif;

			$selection_values = $itemsArray['selection_values'] ?? array();
			if (!is_array($selection_values)):
				$selection_values = json_decode((string)$selection_values, true);
			endif;
			if (!is_array($selection_values)):
				$selection_values = array();
			endif;

			$ticketString = str_replace(array('[[', ']]', '],['), array('', '/', '/'), (string)($itemsArray['ticket'] ?? ''));
			$ticket = array_filter(explode('/', $ticketString));
			$Tickect2 = array();
			if(($itemsArray['super_ball_mode'] ?? '') == 'Y'):
				$Tickect2 = array_filter(explode(',', str_replace(array('[', ']'), '', (string)($itemsArray['sb_tickect'] ?? ''))));
			endif;

			if(empty($ticket)):
				continue;
			endif;

			foreach ($ticket as $subindex => $item):
				$coupon = ($itemsArray['super_ball_mode'] ?? '') == 'Y' ? ($item.','.($Tickect2[$subindex] ?? '')) : $item;
				$coupon = str_replace(' ', '', rtrim((string)$coupon, ','));

				if(!empty($selection_values) && isset($selection_values[$subindex]) && is_array($selection_values[$subindex])):
					$straight = !empty($selection_values[$subindex][0]) ? 1 : 0;
					$rumble   = !empty($selection_values[$subindex][1]) ? 1 : 0;
					$reverse  = !empty($selection_values[$subindex][2]) ? 1 : 0;
				else:
					$straight = !empty($itemsArray['straight_add_on_amount']) ? 1 : 0;
					$rumble   = !empty($itemsArray['rumble_add_on_amount']) ? 1 : 0;
					$reverse  = !empty($itemsArray['reverse_add_on_amount']) ? 1 : 0;
				endif;

				$seller_details = !empty($itemsArray['seller_details']) ? json_decode($itemsArray['seller_details']) : null;
				$seller_Mobile = isset($seller_details->FoMobile) ? $seller_details->FoMobile : (($itemsArray['user_phone'] ?? '') ?: 'N/A');
				$seller_Store = isset($seller_details->Name) ? $seller_details->Name : (($itemsArray['store_name'] ?? '') ?: 'N/A');
				$seller_Bindwith_Name = isset($seller_details->FoName) ? $seller_details->FoName : (($itemsArray['bindwith_first_name'] ?? '') ?: 'N/A');
				if($seller_Bindwith_Name == 'N/A' && !empty($itemsArray['admin_bindwith_users_type'])):
					$seller_Bindwith_Name = $itemsArray['admin_bindwith_users_type'];
				endif;
				if(($itemsArray['users_type'] ?? '') == 'Users'):
					$seller_Store = $itemsArray['users_name'] ?? 'N/A';
				endif;

				$rows[] = array(
					'Sl.No' => $sno++,
					'POS No.' => !empty($itemsArray['pos_number']) ? $itemsArray['pos_number'] : 'N/A',
					'Order ID' => !empty($itemsArray['order_id']) ? $itemsArray['order_id'] : 'N/A',
					'Product Name' => !empty($itemsArray['product_name']) ? $itemsArray['product_name'] : 'N/A',
					'Store Name' => $seller_Store,
					'Seller Name' => ($itemsArray['users_type'] ?? '') == 'Users'
						? (!empty($itemsArray['users_address']) ? $itemsArray['users_address'] : 'N/A')
						: (!empty($itemsArray['users_area']) ? $itemsArray['users_area'] : 'N/A'),
					'Seller Mobile' => $seller_Mobile,
					'Bind With' => $seller_Bindwith_Name,
					'Straight Amount' => $straight,
					'Rumble Amount' => $rumble,
					'Chance Amount' => $reverse,
					'Payment Status' => $orderStatus,
					'Purchase Date' => $createdAt,
					'Coupons' => $coupon !== '' ? $coupon : 'N/A',
				);
			endforeach;
			endforeach;

			if($maxPage !== null || count($orderData) < $itemsPerPage):
				break;
			endif;
			$currentPage++;
		endwhile;

		return $rows;
	}

	private function buildHourlyWinnerExportDateRange()
	{
		$fromInput = $this->normalizeCombinedExportDatetimeInput($this->input->post('fromDate'));
		$toInput = $this->normalizeCombinedExportDatetimeInput($this->input->post('toDate'));
		if($fromInput !== ''):
			$fromDate = date('Y-m-d H:i:00', strtotime($fromInput));
		else:
			$fromDate = date('Y-m-d H:i:00', strtotime(date('Y-m-d 16:00', strtotime('-1 day'))));
		endif;
		if($toInput !== ''):
			$toDate = date('Y-m-d H:i:59', strtotime($toInput));
		else:
			$toDate = date('Y-m-d H:i:59', strtotime(date('Y-m-d 22:00')));
		endif;
		return array('fromDate' => $fromDate, 'toDate' => $toDate);
	}

	private function normalizeCombinedProductIdsFromPost($fieldName)
	{
		$raw = $this->input->post($fieldName);
		if($raw === null || $raw === ''):
			return array();
		endif;
		if(is_string($raw)):
			$decoded = json_decode($raw, true);
			if(is_array($decoded)):
				$raw = $decoded;
			else:
				$raw = array($raw);
			endif;
		endif;
		if(!is_array($raw)):
			$raw = array($raw);
		endif;
		return array_values(array_filter(array_map('intval', $raw), function($id) {
			return $id > 0;
		}));
	}

	private function getCombinedHourlyProductIdsFromPost()
	{
		return $this->normalizeCombinedProductIdsFromPost('hourlyProductIds');
	}

	private function getCombinedLottoProductIdsFromPost()
	{
		return $this->normalizeCombinedProductIdsFromPost('productIds');
	}

	private function getCombinedHourlyProductNamesByIds(array $hourlyProductIds)
	{
		$names = array();
		foreach($hourlyProductIds as $productId):
			$game = $this->common_model->getData('single', 'uw_hourly_games', array(
				'where' => array(
					'products_id' => (int)$productId,
					'status' => 'A',
				),
			));
			if(!empty($game['title'])):
				$names[] = trim(stripslashes($game['title']));
			endif;
		endforeach;
		return array_values(array_unique(array_filter($names)));
	}

	private function getBigWinnersCombinedExportHeaders()
	{
		return array(
			'SL.NO',
			'ORDER ID',
			'RETAILER',
			'POS NUMBER',
			'DRAW DATE',
			'GAME NAME',
			'PRIZE MONEY',
			'PURCHASE DATE',
			'AREA',
			'BIND WITH',
		);
	}

	private function normalizeCombinedExportAmount($value)
	{
		if($value === null || $value === ''):
			return 0.0;
		endif;
		if(is_int($value) || is_float($value)):
			return (float)$value;
		endif;
		$valueStr = trim(str_replace(',', '', (string)$value));
		if($valueStr === '' || strcasecmp($valueStr, 'N/A') === 0):
			return 0.0;
		endif;
		return is_numeric($valueStr) ? (float)$valueStr : 0.0;
	}

	private function formatCombinedExportDrawDate($value)
	{
		if($value === null || $value === '' || $value === 'N/A'):
			return 'N/A';
		endif;
		if(is_numeric($value)):
			return date('d-m-Y', (int)$value);
		endif;
		$timestamp = strtotime((string)$value);
		return $timestamp ? date('d-m-Y', $timestamp) : (string)$value;
	}

	private function formatCombinedExportPurchaseDateTime($value)
	{
		if($value === null || $value === '' || $value === 'N/A'):
			return 'N/A';
		endif;
		if(is_numeric($value)):
			return date('d-m-Y H:i', (int)$value);
		endif;
		$timestamp = strtotime((string)$value);
		return $timestamp ? date('d-m-Y H:i', $timestamp) : (string)$value;
	}

	private function normalizeCombinedExportCellValue($header, $value)
	{
		if($header === 'PRIZE MONEY'):
			return $this->normalizeCombinedExportAmount($value);
		endif;
		if($header === 'POS NUMBER'):
			if($value === null || $value === '' || $value === 'N/A'):
				return 'N/A';
			endif;
			return is_numeric($value) ? (int)$value : (string)$value;
		endif;
		if($header === 'SL.NO'):
			return is_numeric($value) ? (int)$value : $value;
		endif;
		return $value ?? '';
	}

	private function mapHourlyWinnerToBigWinnersExportRow(array $itemsArray)
	{
		$purchaseTs = !empty($itemsArray['created_at']) ? (int)$itemsArray['created_at'] : 0;
		$purchaseDate = $purchaseTs > 0 ? date('d-m-Y H:i', $purchaseTs) : 'N/A';
		$drawDate = $purchaseTs > 0 ? date('d-m-Y', $purchaseTs) : 'N/A';
		$retailer = !empty($itemsArray['seller_store_name'])
			? $itemsArray['seller_store_name']
			: ($itemsArray['seller_users_name'] ?? 'N/A');
		$posNumber = !empty($itemsArray['seller_pos_number']) ? $itemsArray['seller_pos_number'] : 'N/A';

		return array(
			'ORDER ID' => !empty($itemsArray['order_id']) ? $itemsArray['order_id'] : 'N/A',
			'RETAILER' => $retailer !== 'N/A' ? ucwords($retailer) : 'N/A',
			'POS NUMBER' => is_numeric($posNumber) ? (int)$posNumber : $posNumber,
			'DRAW DATE' => $drawDate,
			'GAME NAME' => !empty($itemsArray['products_name']) ? $itemsArray['products_name'] : 'N/A',
			'PRIZE MONEY' => $this->normalizeCombinedExportAmount($itemsArray['winning_amount'] ?? 0),
			'PURCHASE DATE' => $purchaseDate,
			'AREA' => !empty($itemsArray['area']) ? $itemsArray['area'] : 'N/A',
			'BIND WITH' => !empty($itemsArray['seller_users_bind_person_name']) ? $itemsArray['seller_users_bind_person_name'] : 'N/A',
		);
	}

	private function isCombinedExportRowMeaningful(array $row)
	{
		foreach($row as $key => $value):
			if($key === 'Sl.No' || $key === 'SL.NO'):
				continue;
			endif;
			if($value === null):
				continue;
			endif;
			$valueStr = trim((string)$value);
			if($valueStr === '' || $valueStr === 'N/A' || $valueStr === '0' || $valueStr === '0.00'):
				continue;
			endif;
			return true;
		endforeach;
		return false;
	}

	private function buildHourlyExportBaseFilter()
	{
		$dateRange = $this->buildHourlyWinnerExportDateRange();
		$searchField = $this->input->post('searchField');
		$searchValue = $this->input->post('searchValue');
		$whereCondition = array('where' => array(
			'created_at' => array(
				'$gte' => (int) strtotime($dateRange['fromDate']),
				'$lte' => (int) strtotime($dateRange['toDate']),
			),
		));
		$hourlyProductIds = $this->getCombinedHourlyProductIdsFromPost();
		if(!empty($hourlyProductIds)):
			$productNames = $this->getCombinedHourlyProductNamesByIds($hourlyProductIds);
			if(!empty($productNames)):
				$whereCondition['where']['products_name']['$in'] = $productNames;
			else:
				$whereCondition['where']['$or'] = array(
					array('product_id' => array('$in' => $hourlyProductIds)),
					array('products_id' => array('$in' => $hourlyProductIds)),
				);
			endif;
		endif;
		$this->applyHourlySearchFieldsToWhere($whereCondition, $searchField, $searchValue);
		return $whereCondition;
	}

	private function buildHourlyWinnerExportFilter()
	{
		$whereCondition = $this->buildHourlyExportBaseFilter();
		$whereCondition['where']['is_winner'] = 'Y';
		return $whereCondition;
	}

	private function applyHourlySearchFieldsToWhere(array &$whereCondition, $searchField, $searchValue)
	{
		if(empty($searchField) || $searchValue === '' || $searchValue === null):
			return;
		endif;
		if($searchField === 'order_id' || $searchField === 'users.store_name'):
			$whereCondition['where'][$searchField] = array('$regex' => (string)$searchValue, '$options' => 'i');
		elseif($searchField === 'winning_status'):
			$whereCondition['where']['winning_status'] = strtolower((string)$searchValue);
		elseif($searchField === 'users_mobile'):
			$user = $this->common_model->getData('single', 'uw_users', array('where' => array('users_mobile' => (int)$searchValue)));
			if(!empty($user['_id'])):
				$whereCondition['where']['users_oid'] = new MongoDB\BSON\ObjectID($user['_id']->{'$id'});
			endif;
		elseif($searchField === 'users_email'):
			$user = $this->common_model->getData('single', 'uw_users', array('where' => array('users_email' => (string)$searchValue)));
			$whereCondition['where']['users_oid'] = !empty($user['_id'])
				? new MongoDB\BSON\ObjectID($user['_id']->{'$id'})
				: 'default';
		elseif($searchField === 'pos_number'):
			$user = $this->common_model->getData('single', 'uw_users', array('where' => array('pos_number' => (int)$searchValue)));
			$whereCondition['where']['users_oid'] = !empty($user['_id'])
				? new MongoDB\BSON\ObjectID($user['_id']->{'$id'})
				: 'default';
		elseif($searchField === 'settler_pos_number'):
			$user = $this->common_model->getData('single', 'uw_users', array('where' => array('pos_number' => is_numeric($searchValue) ? (int)$searchValue : $searchValue)));
			$whereCondition['where']['settler_users_oid'] = !empty($user['_id'])
				? new MongoDB\BSON\ObjectID($user['_id']->{'$id'})
				: 'default';
		elseif($searchField === 'settler_mobile'):
			$user = $this->common_model->getData('single', 'uw_users', array('where' => array('users_mobile' => (int)$searchValue)));
			$whereCondition['where']['settler_users_oid'] = !empty($user['_id'])
				? new MongoDB\BSON\ObjectID($user['_id']->{'$id'})
				: 'default';
		else:
			$whereCondition['where'][$searchField] = is_numeric($searchValue) ? (int)$searchValue : $searchValue;
		endif;
	}

	private function mapHourlyWinnerExportRow(array $itemsArray)
	{
		$status = 'N/A';
		if(isset($itemsArray['status'])):
			if($itemsArray['status'] === 'CL'):
				$status = 'Cancelled';
			elseif($itemsArray['status'] === 'A'):
				$status = 'Success';
			elseif($itemsArray['status'] === 'Redeemed'):
				$status = 'Redeemed';
			endif;
		endif;
		$redeemingDate = !empty($itemsArray['redeemed_at']) ? date('d-m-Y H:i', (int)$itemsArray['redeemed_at']) : 'N/A';
		return array(
			'POS No.' => !empty($itemsArray['seller_pos_number']) ? $itemsArray['seller_pos_number'] : 'N/A',
			'Order ID' => !empty($itemsArray['order_id']) ? $itemsArray['order_id'] : 'N/A',
			'Product Name' => !empty($itemsArray['products_name']) ? $itemsArray['products_name'] : 'N/A',
			'Store Name' => !empty($itemsArray['seller_store_name']) ? $itemsArray['seller_store_name'] : 'N/A',
			'Seller Name' => !empty($itemsArray['seller_users_name']) ? $itemsArray['seller_users_name'] : 'N/A',
			'Seller Mobile' => !empty($itemsArray['seller_users_mobile']) ? $itemsArray['seller_users_mobile'] : 'N/A',
			'Bind With' => !empty($itemsArray['seller_users_bind_person_name']) ? $itemsArray['seller_users_bind_person_name'] : 'N/A',
			'Total Amount' => !empty($itemsArray['total_price']) ? (float)$itemsArray['total_price'] : 0.00,
			'Payment Status' => $status,
			'Winner Type' => !empty($itemsArray['winner_type']) ? $itemsArray['winner_type'] : 'N/A',
			'Winning Amount' => !empty($itemsArray['winning_amount']) ? (float)$itemsArray['winning_amount'] : 0.00,
			'Winning Status' => !empty($itemsArray['winning_status']) ? $itemsArray['winning_status'] : 'unpaid',
			'Redeemed By' => !empty($itemsArray['settler_full_name']) ? $itemsArray['settler_full_name'] : 'N/A',
			'Redeemed POS ID' => !empty($itemsArray['settler_pos_number']) ? $itemsArray['settler_pos_number'] : 'N/A',
			'Redeemed Date' => $redeemingDate,
			'Purchase Date' => !empty($itemsArray['created_at']) ? date('d-m-Y H:i', (int)$itemsArray['created_at']) : 'N/A',
			'Area' => !empty($itemsArray['area']) ? $itemsArray['area'] : 'N/A',
		);
	}

	private function buildHourlyWinnerRowsFromOrderPage($page)
	{
		$whereCondition = $this->buildHourlyWinnerExportFilter();
		$itemsPerPage = 5000;
		$page = max(1, (int)$page);
		$startIndex = ($page - 1) * $itemsPerPage;
		$orderData = $this->common_model->getHourlyGameOrderData(
			'multiple',
			'uw_hourly_orders',
			$whereCondition,
			array('created_at' => -1),
			$itemsPerPage,
			$startIndex
		);
		$rows = array();
		if(!is_array($orderData)):
			return $rows;
		endif;
		foreach($orderData as $itemsArray):
			$mappedRow = $this->mapHourlyWinnerToBigWinnersExportRow($itemsArray);
			if($this->isCombinedExportRowMeaningful($mappedRow)):
				$rows[] = $mappedRow;
			endif;
		endforeach;
		return $rows;
	}

	private function buildHourlyWinnerRows(array $whereCondition, $page = null, $startSno = 1)
	{
		$rows = array();
		$itemsPerPage = 5000;
		$currentPage = ($page !== null) ? (int)$page : 1;
		$maxPage = ($page !== null) ? (int)$page : null;
		while(true):
			$startIndex = ($currentPage - 1) * $itemsPerPage;
			$orderData = $this->common_model->getHourlyGameOrderData('multiple', 'uw_hourly_orders', $whereCondition, array('created_at' => -1), $itemsPerPage, $startIndex);
			if(!is_array($orderData) || empty($orderData)):
				break;
			endif;

			foreach($orderData as $itemsArray):
				$rows[] = $this->mapHourlyWinnerToBigWinnersExportRow($itemsArray);
			endforeach;

			if($maxPage !== null || count($orderData) < $itemsPerPage):
				break;
			endif;
			$currentPage++;
		endwhile;

		return $rows;
	}

	private function normalizeCombinedExportDatetimeInput($value)
	{
		$value = trim((string)$value);
		if($value === ''):
			return '';
		endif;
		return str_replace('T', ' ', $value);
	}

	private function unwrapMongoAggregateResult($result)
	{
		if(!is_array($result) || empty($result)):
			return array();
		endif;
		if(isset($result[0]['cursor']['firstBatch']) && is_array($result[0]['cursor']['firstBatch'])):
			return $result[0]['cursor']['firstBatch'];
		endif;
		if(isset($result[0]['ok']) && isset($result[0]['cursor'])):
			return isset($result[0]['cursor']['firstBatch']) && is_array($result[0]['cursor']['firstBatch'])
				? $result[0]['cursor']['firstBatch']
				: array();
		endif;
		return $result;
	}

	private function applyCombinedExportSessionFilters()
	{
		$savedFilters = $this->session->userdata('COMBINED_EXCEL_FILTERS');
		if(empty($savedFilters) || !is_array($savedFilters)):
			return;
		endif;
		$fields = array('fromDate', 'toDate', 'searchField', 'searchValue', 'cancelled_order');
		foreach($fields as $field):
			$postVal = $this->input->post($field);
			$isEmpty = ($postVal === null || $postVal === '');
			if($isEmpty && isset($savedFilters[$field]) && $savedFilters[$field] !== '' && $savedFilters[$field] !== null):
				if($field === 'fromDate' || $field === 'toDate'):
					$_POST[$field] = $this->normalizeCombinedExportDatetimeInput($savedFilters[$field]);
				else:
					$_POST[$field] = $savedFilters[$field];
				endif;
			endif;
		endforeach;
		$_POST['productIds'] = array();
		$_POST['hourlyProductIds'] = array();
	}

	private function getVoucherWinnerExportDateRange()
	{
		$fromInput = $this->normalizeCombinedExportDatetimeInput($this->input->post('fromDate'));
		$toInput = $this->normalizeCombinedExportDatetimeInput($this->input->post('toDate'));
		if($fromInput !== ''):
			$fromDateStr = date('Y-m-d H:i:00', strtotime($fromInput));
		else:
			$fromDateStr = date('Y-m-d H:i:00', strtotime(date('Y-m-d 16:00', strtotime('-1 day'))));
		endif;
		if($toInput !== ''):
			$toDateStr = date('Y-m-d H:i:59', strtotime($toInput));
		else:
			$toDateStr = date('Y-m-d H:i:59', strtotime(date('Y-m-d 22:00')));
		endif;
		return array($fromDateStr, $toDateStr);
	}

	private function getCombinedExportFilename()
	{
		$fromInput = $this->normalizeCombinedExportDatetimeInput($this->input->post('fromDate'));
		if($fromInput !== ''):
			$datePart = date('Y-m-d', strtotime($fromInput));
		else:
			$datePart = date('Y-m-d', strtotime(date('Y-m-d 16:00', strtotime('-1 day'))));
		endif;
		return 'Big Winners '.$datePart.'.xlsx';
	}

	private function buildVoucherWinnerExportPipeline($page = null, $itemsPerPage = 5000)
	{
		list($fromDateStr, $toDateStr) = $this->getVoucherWinnerExportDateRange();
		$pipeline = array();
		if($fromDateStr && $toDateStr):
			$pipeline[] = array(
				'$match' => array(
					'status' => 1,
					'soft_delete' => 0,
					'created_at' => array(
						'$gte' => $fromDateStr,
						'$lte' => $toDateStr,
					),
				),
			);
		endif;
		$pipeline[] = array('$sort' => array('amount' => -1));
		if($page !== null):
			$startIndex = (max(1, (int)$page) - 1) * (int)$itemsPerPage;
			$pipeline[] = array('$skip' => $startIndex);
			$pipeline[] = array('$limit' => (int)$itemsPerPage);
		endif;
		$pipeline[] = array(
			'$lookup' => array(
				'from' => 'uw_lotto_orders',
				'localField' => 'order_id',
				'foreignField' => 'order_id',
				'as' => 'order_data',
			),
		);
		$pipeline[] = array(
			'$unwind' => array(
				'path' => '$order_data',
				'preserveNullAndEmptyArrays' => true,
			),
		);
		$pipeline[] = array(
			'$lookup' => array(
				'from' => 'uw_users',
				'let' => array('user_oid' => '$order_data.user_oid'),
				'pipeline' => array(
					array(
						'$match' => array(
							'$expr' => array(
								'$eq' => array('$_id', array('$toObjectId' => '$$user_oid')),
							),
						),
					),
				),
				'as' => 'user_data',
			),
		);
		$pipeline[] = array(
			'$unwind' => array(
				'path' => '$user_data',
				'preserveNullAndEmptyArrays' => true,
			),
		);
		$pipeline[] = array(
			'$lookup' => array(
				'from' => 'uw_products_draw_records',
				'localField' => 'order_data.draw_id',
				'foreignField' => 'draw_id',
				'as' => 'draw_data',
			),
		);
		$pipeline[] = array(
			'$unwind' => array(
				'path' => '$draw_data',
				'preserveNullAndEmptyArrays' => true,
			),
		);
		$pipeline[] = array(
			'$project' => array(
				'_id' => 0,
				'order_id' => 1,
				'retailer' => '$seller_first_name',
				'seller_name' => '$seller_last_name',
				'product_title' => array('$ifNull' => array('$order_data.product_title', 'N/A')),
				'status' => 1,
				'amount' => 1,
				'created_at' => array('$ifNull' => array('$order_data.created_at', 'N/A')),
				'draw_date' => array('$ifNull' => array('$draw_data.draw_date', 'N/A')),
				'bind_person_name' => array('$ifNull' => array('$user_data.bind_person_name', 'N/A')),
				'pos_number' => array('$ifNull' => array('$user_data.pos_number', 'N/A')),
			),
		);

		return $pipeline;
	}

	private function sortVoucherWinnerExportRows(array &$winnerData)
	{
		usort($winnerData, function ($a, $b) {
			return (float)($b['amount'] ?? 0) <=> (float)($a['amount'] ?? 0);
		});
	}

	private function mapVoucherWinnerRowForCombined(array $row)
	{
		$posNumber = $row['pos_number'] ?? 'N/A';
		return array(
			'ORDER ID' => !empty($row['order_id']) ? $row['order_id'] : 'N/A',
			'RETAILER' => !empty($row['retailer']) ? ucwords($row['retailer']) : 'N/A',
			'POS NUMBER' => is_numeric($posNumber) ? (int)$posNumber : $posNumber,
			'DRAW DATE' => $this->formatCombinedExportDrawDate($row['draw_date'] ?? 'N/A'),
			'GAME NAME' => !empty($row['product_title']) ? $row['product_title'] : 'N/A',
			'PRIZE MONEY' => $this->normalizeCombinedExportAmount($row['amount'] ?? 0),
			'PURCHASE DATE' => $this->formatCombinedExportPurchaseDateTime($row['created_at'] ?? 'N/A'),
			'AREA' => !empty($row['seller_name']) ? $row['seller_name'] : 'N/A',
			'BIND WITH' => !empty($row['bind_person_name']) ? $row['bind_person_name'] : 'N/A',
		);
	}

	private function countVoucherWinnerExportRows()
	{
		list($fromDateStr, $toDateStr) = $this->getVoucherWinnerExportDateRange();
		$this->mongo_db->where(array(
			'status' => 1,
			'soft_delete' => 0,
			'created_at' => array(
				'$gte' => $fromDateStr,
				'$lte' => $toDateStr,
			),
		));
		return (int)$this->mongo_db->count('uw_uwin_winner');
	}

	private function buildVoucherLottoExportRowsPage($page)
	{
		$itemsPerPage = 5000;
		$pipeline = $this->buildVoucherWinnerExportPipeline($page, $itemsPerPage);
		$winnerData = $this->mongo_db->aggregate('uw_uwin_winner', $pipeline, array('batchSize' => 500));
		$winnerData = $this->unwrapMongoAggregateResult($winnerData);
		if(!is_array($winnerData) || empty($winnerData)):
			return array();
		endif;
		$rows = array();
		foreach($winnerData as $row):
			$mappedRow = $this->mapVoucherWinnerRowForCombined($row);
			if($this->isCombinedExportRowMeaningful($mappedRow)):
				$rows[] = $mappedRow;
			endif;
		endforeach;
		return $rows;
	}

	private function buildVoucherLottoExportRowsAll()
	{
		$rows = array();
		$page = 1;
		$itemsPerPage = 5000;
		while(true):
			$chunk = $this->buildVoucherLottoExportRowsPage($page);
			if(empty($chunk)):
				break;
			endif;
			$rows = array_merge($rows, $chunk);
			if(count($chunk) < $itemsPerPage):
				break;
			endif;
			$page++;
		endwhile;
		return $rows;
	}

	private function buildCombinedSheetRows(array $hourlyRows, array $bigWinnerRows)
	{
		$headers = $this->getBigWinnersCombinedExportHeaders();
		$dataHeaders = array_values(array_filter($headers, function($header) {
			return $header !== 'SL.NO';
		}));
		$allRows = array();
		foreach(array_merge($hourlyRows, $bigWinnerRows) as $row):
			if(!is_array($row)):
				continue;
			endif;
			$normalized = array();
			foreach($dataHeaders as $header):
				$normalized[$header] = $this->normalizeCombinedExportCellValue($header, $row[$header] ?? '');
			endforeach;
			if($this->isCombinedExportRowMeaningful($normalized)):
				$allRows[] = $normalized;
			endif;
		endforeach;
		if(empty($allRows)):
			return array();
		endif;

		usort($allRows, function($a, $b) {
			return (float)($b['PRIZE MONEY'] ?? 0) <=> (float)($a['PRIZE MONEY'] ?? 0);
		});

		$merged = array();
		$slNo = 1;
		foreach($allRows as $row):
			$line = array('SL.NO' => $slNo++);
			foreach($dataHeaders as $header):
				$line[$header] = $this->normalizeCombinedExportCellValue($header, $row[$header] ?? '');
			endforeach;
			$merged[] = $line;
		endforeach;
		return $merged;
	}

	private function ensurePhpSpreadsheetLoaded()
	{
		if(class_exists('PhpOffice\\PhpSpreadsheet\\Spreadsheet', false)):
			return;
		endif;

		$autoloadPaths = array(
			FCPATH.'vendor/autoload.php',
			APPPATH.'vendor/autoload.php',
		);
		foreach($autoloadPaths as $autoloadPath):
			if(is_readable($autoloadPath)):
				require_once $autoloadPath;
				break;
			endif;
		endforeach;

		$psrCache = FCPATH.'vendor/psr/simple-cache/src/CacheInterface.php';
		if(is_readable($psrCache)):
			require_once $psrCache;
		endif;

		if(!class_exists('PhpOffice\\PhpSpreadsheet\\Spreadsheet', false)):
			throw new \RuntimeException('PhpSpreadsheet library not found. Please ensure admin77664/vendor is installed on the server.');
		endif;
	}

	private function writeRowsToSheet($sheet, array $rows)
	{
		if(empty($rows)):
			$sheet->setCellValue('A1', 'No data available');
			return;
		endif;
		$headers = array_keys($rows[0]);
		$col = 'A';
		foreach($headers as $header):
			$sheet->setCellValue($col.'1', $header);
			$col++;
		endforeach;
		$rowNum = 2;
		foreach($rows as $row):
			$col = 'A';
			foreach($headers as $header):
				$cellValue = $row[$header] ?? '';
				if($header === 'PRIZE MONEY'):
					$sheet->setCellValue($col.$rowNum, $this->normalizeCombinedExportAmount($cellValue));
				elseif($header === 'POS NUMBER' && is_numeric($cellValue)):
					$sheet->setCellValue($col.$rowNum, (int)$cellValue);
				elseif($header === 'SL.NO' && is_numeric($cellValue)):
					$sheet->setCellValue($col.$rowNum, (int)$cellValue);
				else:
					$sheet->setCellValue($col.$rowNum, $this->normalizeSpreadsheetValue($cellValue));
				endif;
				$col++;
			endforeach;
			$rowNum++;
		endforeach;
	}

	private function normalizeSpreadsheetValue($value)
	{
		if(is_array($value) || is_object($value)):
			$value = json_encode($value);
		endif;
		if($value === null):
			return '';
		endif;
		if(is_bool($value)):
			return $value ? '1' : '0';
		endif;
		if(is_int($value) || is_float($value)):
			return $value;
		endif;

		$value = (string)$value;
		$normalized = @iconv('UTF-8', 'UTF-8//IGNORE', $value);
		if($normalized !== false):
			$value = $normalized;
		endif;
		return trim($value);
	}

	public function combinedexportexcel()
	{
		$this->admin_model->authCheck('view_data');
		if((string)$this->input->get('download') === '1'):
			$getFromDate = trim((string)$this->input->get('fromDate'));
			$getToDate = trim((string)$this->input->get('toDate'));
			$getProductIds = trim((string)$this->input->get('productIds'));
			if($getFromDate !== '' || $getToDate !== '' || $getProductIds !== ''):
				$_POST['fromDate'] = $getFromDate;
				$_POST['toDate'] = $getToDate;
				$_POST['productIds'] = $getProductIds !== ''
					? array_values(array_filter(array_map('intval', explode(',', $getProductIds))))
					: array();
			endif;
			$this->combinedexportexcelDownload();
			return;
		endif;
		$this->common_model->generateLogs();

		// Combined export: date filter only — all lotto/hourly games in range
		$productIds = array();
		$hourlyProductIds = array();
		$_POST['productIds'] = array();
		$_POST['hourlyProductIds'] = array();
		$fromDatePost = $this->normalizeCombinedExportDatetimeInput($this->input->post('fromDate'));
		$toDatePost = $this->normalizeCombinedExportDatetimeInput($this->input->post('toDate'));
		if($fromDatePost !== ''):
			$_POST['fromDate'] = $fromDatePost;
		endif;
		if($toDatePost !== ''):
			$_POST['toDate'] = $toDatePost;
		endif;

		$this->session->set_userdata('COMBINED_EXCEL_FILTERS', array(
			'fromDate' => $this->input->post('fromDate'),
			'toDate' => $this->input->post('toDate'),
			'searchField' => $this->input->post('searchField'),
			'searchValue' => $this->input->post('searchValue'),
			'cancelled_order' => $this->input->post('cancelled_order'),
			'productIds' => $productIds,
			'hourlyProductIds' => $hourlyProductIds,
		));

		$data = array();
		$data['error'] = '';
		$data['activeMenu'] = 'uwin';
		$data['activeSubMenu'] = 'alllottooders';
		$data['fromDate'] = $this->input->post('fromDate');
		$data['toDate'] = $this->input->post('toDate');
		list($voucherFrom, $voucherTo) = $this->getVoucherWinnerExportDateRange();
		$data['voucher_from_display'] = $voucherFrom;
		$data['voucher_to_display'] = $voucherTo;
		$data['server_download_url'] = getCurrentControllerPath('combinedexportexcel?download=1');
		$data['api_url'] = getCurrentControllerPath('combinedexportexcelApi');
		$data['productIds'] = $productIds;
		$data['hourlyProductIds'] = $hourlyProductIds;
		$data['searchField'] = $this->input->post('searchField');
		$data['searchValue'] = $this->input->post('searchValue');
		$data['cancelled_order'] = $this->input->post('cancelled_order');

		$hourlyWinnerWhere = $this->buildHourlyWinnerExportFilter();
		$itemsPerPage = 5000;
		$bigWinnersCount = $this->countVoucherWinnerExportRows();
		$hourlyWinnerCount = (int)$this->common_model->getHourlyGameOrderData('count', 'uw_hourly_orders', $hourlyWinnerWhere);
		$data['hourly_count'] = $hourlyWinnerCount;
		$data['lotto_count'] = $bigWinnersCount;
		$data['big_winners_count'] = $bigWinnersCount;
		$data['hourly_total_page'] = $hourlyWinnerCount > 0
			? max(1, (int)ceil($hourlyWinnerCount / $itemsPerPage))
			: 0;
		$data['lotto_total_page'] = $bigWinnersCount > 0
			? max(1, (int)ceil($bigWinnersCount / $itemsPerPage))
			: 0;
		$data['big_winners_total_page'] = $data['lotto_total_page'];
		$data['total_page'] = max(1, $data['hourly_total_page'] + $data['lotto_total_page']);

		$this->layouts->set_title('Export Combined Excel | UWINN');
		$this->layouts->admin_view('uwin/allorders/combinedexportexcel', array(), $data);
	}

	public function combinedexportexcelDownload()
	{
		$this->admin_model->authCheck('view_data');

		try {
			@set_time_limit(0);
			@ini_set('memory_limit', '1024M');

			$this->applyCombinedExportSessionFilters();
			$savedFilters = $this->session->userdata('COMBINED_EXCEL_FILTERS');
			if(!empty($savedFilters) && is_array($savedFilters)):
				if(!empty($savedFilters['fromDate'])):
					$_POST['fromDate'] = $this->normalizeCombinedExportDatetimeInput($savedFilters['fromDate']);
				endif;
				if(!empty($savedFilters['toDate'])):
					$_POST['toDate'] = $this->normalizeCombinedExportDatetimeInput($savedFilters['toDate']);
				endif;
				$_POST['searchField'] = $savedFilters['searchField'] ?? '';
				$_POST['searchValue'] = $savedFilters['searchValue'] ?? '';
				$_POST['cancelled_order'] = $savedFilters['cancelled_order'] ?? '';
			endif;
			$_POST['productIds'] = array();
			$_POST['hourlyProductIds'] = array();
			if(!isset($_POST['fromDate']) || $_POST['fromDate'] === ''):
				$_POST['fromDate'] = (string)$this->input->get('fromDate');
			endif;
			if(!isset($_POST['toDate']) || $_POST['toDate'] === ''):
				$_POST['toDate'] = (string)$this->input->get('toDate');
			endif;
			$bigWinnerRows = $this->buildVoucherLottoExportRowsAll();
			$hourlyWhere = $this->buildHourlyWinnerExportFilter();
			$hourlyRows = $this->buildHourlyWinnerRows($hourlyWhere);
			if((string)$this->input->get('format') === 'json'):
				$flags = defined('JSON_INVALID_UTF8_SUBSTITUTE') ? JSON_INVALID_UTF8_SUBSTITUTE : 0;
				$this->output->set_content_type('application/json');
				$this->output->set_output(json_encode(array(
					'hourly' => $hourlyRows,
					'big_winners' => $bigWinnerRows,
					'lotto' => $bigWinnerRows,
				), $flags));
				return;
			endif; 

			$combinedRows = $this->buildCombinedSheetRows($hourlyRows, $bigWinnerRows);
			$this->ensurePhpSpreadsheetLoaded();
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();
			$sheet->setTitle('Combined Report');
			$this->writeRowsToSheet($sheet, $combinedRows);
			$filename = $this->getCombinedExportFilename();
			while(ob_get_level() > 0):
				ob_end_clean();
			endwhile;
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			header('Cache-Control: max-age=0');
			header('Pragma: public');
			$writer = new Xlsx($spreadsheet);
			$writer->save('php://output');
			exit;
		} catch (\Throwable $e) {
			log_message('error', 'Combined export failed: '.$e->getMessage().' @ '.$e->getFile().':'.$e->getLine());
			while(ob_get_level() > 0):
				ob_end_clean();
			endwhile;
			header('HTTP/1.1 500 Internal Server Error');
			header('Content-Type: text/plain; charset=UTF-8');
			echo 'Combined export failed: '.$e->getMessage().' @ line '.$e->getLine();
			exit;
		}
	}

	public function combinedexportexcelApi()
	{
		$this->admin_model->authCheck('view_data');
		@set_time_limit(300);
		@ini_set('memory_limit', '512M');
		$this->applyCombinedExportSessionFilters();

		$source = (string)$this->input->post('source');
		$page = max(1, (int)$this->input->post('pageno'));

		try {
			if($source === 'big_winners' || $source === 'lotto'):
				$rows = $this->buildVoucherLottoExportRowsPage($page);
			else:
				$rows = $this->buildHourlyWinnerRowsFromOrderPage($page);
			endif;
			$flags = defined('JSON_INVALID_UTF8_SUBSTITUTE') ? JSON_INVALID_UTF8_SUBSTITUTE : 0;
			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode($rows, $flags));
		} catch (\Throwable $e) {
			log_message('error', 'Combined export API failed: '.$e->getMessage());
			$this->output->set_status_header(500);
			$this->output->set_content_type('application/json');
			$this->output->set_output(json_encode(array('error' => $e->getMessage())));
		}
		return;
	}

	function exportexcel()
	{	
		$data['error'] 		   = '';
		$data['activeMenu']    = 'uwin';
		$data['activeSubMenu'] = 'alllottoorders';
		$this->admin_model->authCheck('view_data');
		//Generating Logs
		// $this->common_model->generateLogs();

		// ---------------------------------Date query start---------------------------------//
		if($this->input->post('fromDate')):
			$fromDate	 = date('Y-m-d H:i', strtotime($this->input->post('fromDate')));
		endif;
		if($this->input->post('toDate')):
			$toDate	 	 = date('Y-m-d H:i', strtotime($this->input->post('toDate')));
		endif;
		$productIds = isset($_POST['productIds']) ? $_POST['productIds'] : [];
		$productIds = array_map(function($id) {
			return ($id <= PHP_INT_MAX) ? (int) $id : $id;
		}, $productIds);
		// echo '<pre>'; print_r($productIds); die();
		// -----------------------------------------------------------------------------//

		$searchField     = $this->input->post('searchField');
		$searchValue     = $this->input->post('searchValue');
		$cancelled_order = $this->input->post('cancelled_order');
		$draw_one 			 = $this->input->post('draw_time_one');
		$draw_two 			 = $this->input->post('draw_time_two');
		if($searchField == 'status'):
			if($fromDate):
				$whereCondition['where']['update_date']['$gte']  =  strtotime($fromDate);
			endif;
			if($toDate):
				$whereCondition['where']['update_date']['$lte']  =  strtotime($toDate);
			endif;
		else:
			if($fromDate):
				$whereCondition['where']['created_at']['$gte']  =  $fromDate;
			endif;
			if($toDate):
				$whereCondition['where']['created_at']['$lte']  =  $toDate;
			endif;
		endif;
		// ---------------------------------Date query end---------------------------------//
		if(!empty($searchField) && !empty($searchValue)):
			if($searchField == 'ticket'):
				$whereCondition['where']		 	   =  array($searchField => "[[".$searchValue."]]" );

			elseif($searchField == "available_coupon"):
				// $whereCon['where']		 			= 	array($sField=> "[[".$sValue."]]" );	

				$tblName 	 		=  'uw_uwin_available_coupons';
				$shortField  		=  array('products_id'=> -1);
				$whereCon['where']  =  array('products_id' => (int)$sValue);
				$result	 			=  $this->common_model->getData('single',$tblName,$whereCon,$shortField);	
			else:
				$whereCondition['where'][$searchField] =  is_numeric($searchValue)?(int)$searchValue:$searchValue;
			endif;
		else:
			$whereCondition['where']['order_status']   = array('$ne' => 'Initialize');
		endif;

		$whereCondition['where']['raffle_mode'] = array('$ne' => 'Y');

		if($cancelled_order == 'on'):
			$whereCondition['where']['status']['$eq']  =  'CL';
		else:
			$whereCondition['where']['status']['$ne']  =  'CL';
		endif;

		if($productIds <> "" && count($productIds) > 0):
			$whereCondition['where']['product_id']['$in']  =  $productIds;
		endif;

		// ------------------------------Draw TIme Filter------------------------------------//
		if($draw_one === 'on' || $draw_two === 'on'):
			$drawDetailsOption['where']['creation_date']['$gte']  =  strtotime($fromDate);
			$drawDetailsOption['where']['creation_date']['$lte']  =  strtotime($toDate);
			if($draw_one === 'on'):
				$drawDetailsOption['where']['draw_time']  =  '22:00';
			else:
				$drawDetailsOption['where']['draw_time']  =  '23:30';
			endif;
			$drawDetailsData	 =  $this->common_model->getData('multiple','uw_products_draw_records',$drawDetailsOption);
			if($drawDetailsData):
				$drawIds = array_column($drawDetailsData, "draw_id");
				$whereCondition['where']['draw_id']['$in']  =  $drawIds;
			endif;
		endif;
		// -----------------------------------------------------------------------------//
		$resultType   = "count";
		$totalRows 	  = $this->common_model->getOrderDetails($resultType,$whereCondition);
		$itemsPerPage = 5000;
		// ---------------------------------------------
		$longArray    = $totalRows;
		$pageno       = $this->input->get('page');
		// Current page number (received from URL query parameter, e.g., ?page=2)
		$page = isset($pageno) ? (int)$pageno : 1;

		// Calculate total number of pages
		$totalPages = ceil($longArray / $itemsPerPage);
		$totalpage= array();
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
 		// $resultType  = '';
		// $OrderData 	 = $this->common_model->getOrderDetails($resultType,$whereCondition,$startIndex,$itemsPerPage);
		
		$totalpage 				 = count($totalpage);
		$data['current_page']    = $current_page;
		$data['total_page'] 	 = $totalpage;
		$data['searchField'] 	 =   $searchField;
		$data['searchValue'] 	 =   $searchValue;
		$data['fromDate'] 		 =   $fromDate;
		$data['toDate'] 		 =   $toDate;
		$data['cancelled_order'] =   $cancelled_order;
		$data['draw_one'] 		 =   $draw_one;
		$data['draw_two'] 		 =   $draw_two;
		$data['productIds']		 = 	json_encode($productIds);
		$this->layouts->set_title('Export CSV | UWINN');
		$this->layouts->admin_view('uwin/allorders/exportexcel',array(),$data);		 

	}	// END OF FUNCTION

	/***********************************************************************
	** Function name 	: exportexcelApi
	** Developed By 	: Dilip halder
	** Purpose  		: This function used for export order data
	** Date 			: 24 July 2024
	** Updated By     	: Dilip Halder
	** Updated Date     : 26 January 2024
	************************************************************************/
	function exportexcelApi(){
		$this->admin_model->authCheck('view_data');

		// ---------------------------------Date query start---------------------------------//
		if($this->input->post('fromDate')):
			$fromDate	 = date('Y-m-d H:i', strtotime($this->input->post('fromDate')));
		endif;
		if($this->input->post('toDate')):
			$toDate	 	 = date('Y-m-d H:i', strtotime($this->input->post('toDate')));
		endif;
		$productIds = isset($_POST['productIds']) ? json_decode($_POST['productIds'], true) : [];
		$productIds = array_map(function($id) {
			return ($id <= PHP_INT_MAX) ? (int) $id : $id;
		}, $productIds);
		// -----------------------------------------------------------------------------//

		$searchField 	 = $this->input->post('searchField');
		$searchValue 	 = $this->input->post('searchValue');
		$cancelled_order = $this->input->post('cancelled_order');
		$draw_one 			 = $this->input->post('draw_time_one');
		$draw_two 			 = $this->input->post('draw_time_two');

		if($searchField == 'status'):
			if($fromDate):
				$whereCondition['where']['update_date']['$gte']  =  strtotime($fromDate);
			endif;
			if($toDate):
				$whereCondition['where']['update_date']['$lte']  =  strtotime($toDate);
			endif;
		else:
			if($fromDate):
				$whereCondition['where']['created_at']['$gte']  =  $fromDate;
			endif;
			if($toDate):
				$whereCondition['where']['created_at']['$lte']  =  $toDate;
			endif;
		endif;
		
		// ---------------------------------Date query end---------------------------------//
		if(!empty($searchField) && !empty($searchValue)):
			if($searchField == 'ticket'):
				$whereCondition['where']		 	   =  array($searchField => "[[".$searchValue."]]" );

			elseif($searchField == "available_coupon"):
				// $whereCon['where']		 			= 	array($sField=> "[[".$sValue."]]" );	

				$tblName 	 		=  'uw_uwin_available_coupons';
				$shortField  		=  array('products_id'=> -1);
				$whereCon['where']  =  array('products_id' => (int)$sValue);
				$result	 			=  $this->common_model->getData('single',$tblName,$whereCon,$shortField);	
			else:
				$whereCondition['where'][$searchField] =  is_numeric($searchValue)?(int)$searchValue:$searchValue;
			endif;
		else:
			$whereCondition['where']['order_status']   = array('$ne' => 'Initialize');
		endif;
			$whereCondition['where']['raffle_mode'] = array('$ne' => 'Y');

		if($cancelled_order == 'on'):
			$whereCondition['where']['status']['$eq']  =  'CL';
		else:
			$whereCondition['where']['status']['$ne']  =  'CL';
		endif;

		if($productIds <> "" && count($productIds) > 0):
			$whereCondition['where']['product_id']['$in']  =  $productIds;
		endif;

		// ------------------------------Draw TIme Filter------------------------------------//
		if($draw_one === 'on' || $draw_two === 'on'):
			$drawDetailsOption['where']['creation_date']['$gte']  =  strtotime($fromDate);
			$drawDetailsOption['where']['creation_date']['$lte']  =  strtotime($toDate);
			if($draw_one === 'on'):
				$drawDetailsOption['where']['draw_time']  =  '22:00';
			else:
				$drawDetailsOption['where']['draw_time']  =  '23:30';
			endif;
			$drawDetailsData	 =  $this->common_model->getData('multiple','uw_products_draw_records',$drawDetailsOption);
			if($drawDetailsData):
				$drawIds = array_column($drawDetailsData, "draw_id");
				$whereCondition['where']['draw_id']['$in']  =  $drawIds;
			endif;
		endif;

		// $page = $this->input->post('pageno');
		$page = $this->input->post('pageno');
		// $page = 1;
 		$itemsPerPage = 5000;
 		$startIndex   = ($page - 1)*$itemsPerPage;
 		$resultType   = '';
		$OrderData 	  = $this->common_model->getOrderDetails($resultType,$whereCondition,$startIndex,$itemsPerPage);

		$CSVData 	  = array();
		$sno = 1;
		foreach($OrderData as $index => $itemsArray):

			if($itemsArray['status'] == "CL"):
				$createdAt   = date('Y-m-d H:i', $itemsArray['update_date']);	
				$OrderStatus = "Cancelled";
			elseif($itemsArray['order_status']):
				$createdAt   = $itemsArray['created_at'];
				$OrderStatus = $itemsArray['order_status'];
			endif;

			// $ticket   		   = json_decode($itemsArray['ticket']);
			$selection_values  = json_decode($itemsArray['selection_values']);
			$selection_values  = json_decode($itemsArray['selection_values']);
			$selection_values = $itemsArray['selection_values'];

			if (!is_array($selection_values)) {
				$selection_values = json_decode($selection_values, true);
			}
			$Ticket = str_replace('[[', '', $itemsArray['ticket']);
            $Ticket = str_replace(']]', '/', $Ticket);
            $Ticket = str_replace('],[', '/', $Ticket);
            if (is_string($Ticket)) {
				$ticket = array_filter(explode('/', $Ticket));
			} elseif (is_array($Ticket)) {
				$ticket = $Ticket; // already array hai
			} else {
				$ticket = []; // fallback empty array
			}
			
            if($itemsArray['super_ball_mode'] == 'Y'):
                 $Tickect2 = str_replace('[', '', $itemsArray['sb_tickect']);
                 $Tickect2 = str_replace(']', '', $Tickect2);
                 $Tickect2 = array_filter(explode(',', $Tickect2));
            endif;

			if($ticket):
				foreach ($ticket as $subindex => $item):
					if($itemsArray['super_ball_mode'] == 'Y'):
				  		$coupon = $item.','.$Tickect2[$subindex];
					else:
				  	   $coupon = $item;
					endif;
			  		$coupon = rtrim($coupon, ",");
			  		$coupon = str_replace(' ', '', $coupon);
					
				  	// $coupon = implode(',', $item);
				  	if(!empty($itemsArray['selection_values'])):
				  		$straight = $selection_values[$subindex][0]?1:0;
					  	$rumble   = $selection_values[$subindex][1]?1:0;
					  	$reverse  = $selection_values[$subindex][2]?1:0;
				  	else:
					  	$straight = $itemsArray['straight_add_on_amount']?1:0;
					  	$rumble   = $itemsArray['rumble_add_on_amount']?1:0;
					  	$reverse  = $itemsArray['reverse_add_on_amount']?1:0;
				  	endif;
				   
				  	if($itemsArray['seller_details']):
				    	$seller_details = json_decode($itemsArray['seller_details']);
				    	$words = explode(' ', $seller_details->Country);
	                    $initials = '';
	                    $countryPrefrx = '';
	                    foreach ($words as $word):
	                     $countryPrefrx .= $word[0];
	                    endforeach;

				  	else:
				  		$seller_details = '';
				  	endif;

			  		$seller_POS 		  = isset($seller_details->posid)    ? $countryPrefrx.'_'.$seller_details->posid : (isset($itemsArray['pos_number']) ? $itemsArray['pos_number'] : 'N/A');
			  		$seller_Name 		  = isset($seller_details->Name)     ? $seller_details->Name : (isset($itemsArray['users_name']) ? $itemsArray['users_name'] : 'N/A');
			  		$seller_Mobile 		  = isset($seller_details->FoMobile) ? $seller_details->FoMobile : (isset($itemsArray['user_phone']) ? $itemsArray['user_phone'] : 'N/A');
			  		$seller_Store 		  = isset($seller_details->Name) 	 ? $seller_details->Name : (isset($itemsArray['store_name']) ? $itemsArray['store_name'] : 'N/A');
			  		$seller_Bindwith_Name = isset($seller_details->FoName)   ? $seller_details->FoName : (isset($itemsArray['bindwith_first_name']) ? $itemsArray['bindwith_first_name'] : 'N/A');

			  		if( $seller_Bindwith_Name == 'N/A' &&  !empty($itemsArray['admin_bindwith_users_type']) ):
			  			$seller_Bindwith_Name = $itemsArray['admin_bindwith_users_type'];
			  		endif;

			  		if($itemsArray['users_type'] == 'Users'):
						$itemsArray['bindwith_first_name'] = 'Admin';
						$seller_Store = $itemsArray['users_name'];
					else:
						$seller_Store = $itemsArray['store_name'];
					endif;

		  		    // $CSVData1['Sl.No']              = $sno++;
				    $CSVData1['POS No.']            = !empty($itemsArray['pos_number']) ? $itemsArray['pos_number'] : 'N/A';
					$CSVData1['Order ID']           = !empty($itemsArray['order_id']) ? $itemsArray['order_id'] : 'N/A';
					$CSVData1['Product Name']       = !empty($itemsArray['product_name']) ? $itemsArray['product_name'] : 'N/A';
					$CSVData1['Store Name']         = !empty($seller_Store) ? $seller_Store : 'N/A';
					if($itemsArray['users_type'] == 'Users'):
					 $CSVData1['Seller Name']        = !empty($itemsArray['users_address']) ? $itemsArray['users_address'] : 'N/A';
					else:
					 // $CSVData1['Seller Name']        = !empty($seller_Name) ? $seller_Name : 'N/A';
					 $CSVData1['Seller Name']        = !empty($itemsArray['users_area']) ? $itemsArray['users_area'] : 'N/A';
					endif;
					 // $CSVData1['Seller Name']        = !empty($seller_Name) ? $seller_Name : 'N/A';
					$CSVData1['Seller Mobile']      = !empty($seller_Mobile) ? $seller_Mobile : 'N/A';
					$CSVData1['Bind With']          = !empty($seller_Bindwith_Name) ? $seller_Bindwith_Name : 'N/A';
					$CSVData1['Straight Amount']    = !empty($straight) ? $straight : '0';
					$CSVData1['Rumble Amount']      = !empty($rumble)   ? $rumble   : '0';
					$CSVData1['Chance Amount']      = !empty($reverse)  ? $reverse  : '0';
					$CSVData1['Payment Status']     = !empty($OrderStatus) ? $OrderStatus : 'N/A';
					$CSVData1['Purchase Date']      = !empty($createdAt) ? $createdAt : 'N/A';
					$CSVData1['Coupons']      	    = !empty($coupon) ? $coupon : 'N/A';
					array_push($CSVData, $CSVData1);
				endforeach;
			endif;
		endforeach;

		echo json_encode($CSVData);
		die();
	}
	
	 

	/***********************************************************************
	** Function name 	: exportexcel
	** Developed By 	: Dilip halder
	** Purpose  		: This function used for export order data
	** Date 			: 26 January 2024
	** Updated By     	: Dilip Halder
	** Updated Date     : 26 January 2024
	************************************************************************/
	// function exportexcel()
	// {	
	// 	$this->admin_model->authCheck('view_data');
	// 	//Generating Logs
	// 	$this->common_model->generateLogs();

	// 	// ---------------------------------Date query start---------------------------------//
	// 	if($this->input->post('fromDate')):
	// 		$fromDate	 = date('Y-m-d H:i', strtotime($this->input->post('fromDate')));
	// 	endif;
	// 	if($this->input->post('toDate')):
	// 		$toDate	 	 = date('Y-m-d H:i', strtotime($this->input->post('toDate')));
	// 	endif;
	// 	$searchField     = $this->input->post('searchField');
	// 	$searchValue     = $this->input->post('searchValue');
	// 	$cancelled_order = $this->input->post('cancelled_order');

	// 	if($searchField == 'status'):
	// 		if($fromDate):
	// 			$whereCondition['where']['update_date']['$gte']  =  strtotime($fromDate);
	// 		endif;
	// 		if($toDate):
	// 			$whereCondition['where']['update_date']['$lte']  =  strtotime($toDate);
	// 		endif;
	// 	else:
	// 		if($fromDate):
	// 			$whereCondition['where']['created_at']['$gte']  =  $fromDate;
	// 		endif;
	// 		if($toDate):
	// 			$whereCondition['where']['created_at']['$lte']  =  $toDate;
	// 		endif;
	// 	endif;
	// 	// ---------------------------------Date query end---------------------------------//
	// 	if(!empty($searchField) && !empty($searchValue)):
	// 		if($searchField == 'ticket'):
	// 			$whereCondition['where']		 	   =  array($searchField => "[[".$searchValue."]]" );

	// 		elseif($searchField == "available_coupon"):
	// 			// $whereCon['where']		 			= 	array($sField=> "[[".$sValue."]]" );	

	// 			$tblName 	 		=  'uw_uwin_available_coupons';
	// 			$shortField  		=  array('products_id'=> -1);
	// 			$whereCon['where']  =  array('products_id' => (int)$sValue);
	// 			$result	 			=  $this->common_model->getData('single',$tblName,$whereCon,$shortField);	
	// 		else:
	// 			$whereCondition['where'][$searchField] =  is_numeric($searchValue)?(int)$searchValue:$searchValue;
	// 		endif;
	// 	else:
	// 		$whereCondition['where']['order_status']   = array('$ne' => 'Initialize');
	// 	endif;
	// 		$whereCondition['where']['raffle_mode'] = array('$ne' => 'Y');

	// 	if($cancelled_order == 'on'):
	// 		$whereCondition['where']['status']['$eq']  =  'CL';
	// 	else:
	// 		$whereCondition['where']['status']['$ne']  =  'CL';
	// 	endif;
			
	// 	// -----------------------------------------------------------------------------//
	// 	$resultType   = "count";
	// 	$totalRows 	  = $this->common_model->getOrderDetails($resultType,$whereCondition);
	// 	$itemsPerPage = 5000;
	// 	// ---------------------------------------------

	// 	$longArray = $totalRows;
		
	// 	$pageno       = $this->input->get('page');
	// 	// Current page number (received from URL query parameter, e.g., ?page=2)
	// 	$page = isset($pageno) ? (int)$pageno : 1;

	// 	// Calculate total number of pages
	// 	$totalPages = ceil($longArray / $itemsPerPage);
	// 	$totalpage= array();
	// 	// Pagination links
	// 	for ($i = 1; $i <= $totalPages; $i++) {
	// 	    if ($i == $page) {
	// 	         $current_page = $i;
	// 	         $totalpage[] = $i;
	// 	    } else {
	// 	         $totalpage[] = $i;
	// 	    }
	// 	}
 		
 	// 	$startIndex  = ($page - 1) * $itemsPerPage;
 	// 	// $resultType  = '';
	// 	// $OrderData 	 = $this->common_model->getOrderDetails($resultType,$whereCondition,$startIndex,$itemsPerPage);
		
	// 	$totalpage 				 = count($totalpage);
	// 	$data['current_page']    = $current_page;
	// 	$data['total_page'] 	 = $totalpage;
	// 	$data['searchField'] 	 =   $searchField;
	// 	$data['searchValue'] 	 =   $searchValue;
	// 	$data['fromDate'] 		 =   $fromDate;
	// 	$data['toDate'] 		 =   $toDate;
	// 	$data['cancelled_order'] =   $cancelled_order;
	// 	// echo "<pre>";print_r($data);die();
	// 	$this->layouts->set_title('Export CSV | UWINN');
	// 	$this->layouts->admin_view('uwin/allorders/exportexcel',array(),$data);		 

	// }	// END OF FUNCTION

	/***********************************************************************
	** Function name 	: exportexcelApi
	** Developed By 	: Dilip halder
	** Purpose  		: This function used for export order data
	** Date 			: 24 July 2024
	** Updated By     	: Dilip Halder
	** Updated Date     : 26 January 2024
	************************************************************************/
	// function exportexcelApi(){
	// 	$this->admin_model->authCheck('view_data');

	// 	// ---------------------------------Date query start---------------------------------//
	// 	if($this->input->post('fromDate')):
	// 		$fromDate	 = date('Y-m-d H:i', strtotime($this->input->post('fromDate')));
	// 	endif;
	// 	if($this->input->post('toDate')):
	// 		$toDate	 	 = date('Y-m-d H:i', strtotime($this->input->post('toDate')));
	// 	endif;
	// 	$searchField 	 = $this->input->post('searchField');
	// 	$searchValue 	 = $this->input->post('searchValue');
	// 	$cancelled_order = $this->input->post('cancelled_order');

	// 	if($searchField == 'status'):
	// 		if($fromDate):
	// 			$whereCondition['where']['update_date']['$gte']  =  strtotime($fromDate);
	// 		endif;
	// 		if($toDate):
	// 			$whereCondition['where']['update_date']['$lte']  =  strtotime($toDate);
	// 		endif;
	// 	else:
	// 		if($fromDate):
	// 			$whereCondition['where']['created_at']['$gte']  =  $fromDate;
	// 		endif;
	// 		if($toDate):
	// 			$whereCondition['where']['created_at']['$lte']  =  $toDate;
	// 		endif;
	// 	endif;
	// 	// ---------------------------------Date query end---------------------------------//
	// 	if(!empty($searchField) && !empty($searchValue)):
	// 		if($searchField == 'ticket'):
	// 			$whereCondition['where']		 	   =  array($searchField => "[[".$searchValue."]]" );

	// 		elseif($searchField == "available_coupon"):
	// 			// $whereCon['where']		 			= 	array($sField=> "[[".$sValue."]]" );	

	// 			$tblName 	 		=  'uw_uwin_available_coupons';
	// 			$shortField  		=  array('products_id'=> -1);
	// 			$whereCon['where']  =  array('products_id' => (int)$sValue);
	// 			$result	 			=  $this->common_model->getData('single',$tblName,$whereCon,$shortField);	
	// 		else:
	// 			$whereCondition['where'][$searchField] =  is_numeric($searchValue)?(int)$searchValue:$searchValue;
	// 		endif;
	// 	else:
	// 		$whereCondition['where']['order_status']   = array('$ne' => 'Initialize');
	// 	endif;
	// 		$whereCondition['where']['raffle_mode'] = array('$ne' => 'Y');

	// 	if($cancelled_order == 'on'):
	// 		$whereCondition['where']['status']['$eq']  =  'CL';
	// 	else:
	// 		$whereCondition['where']['status']['$ne']  =  'CL';
	// 	endif;

	// 	// $page = $this->input->post('pageno');
	// 	$page = $this->input->post('pageno');
	// 	// $page = 1;
 	// 	$itemsPerPage = 5000;
 	// 	$startIndex   = ($page - 1)*$itemsPerPage;
 	// 	$resultType   = '';
	// 	$OrderData 	  = $this->common_model->getOrderDetails($resultType,$whereCondition,$startIndex,$itemsPerPage);

	// 	$CSVData 	  = array();
	// 	$sno = 1;
	// 	foreach($OrderData as $index => $itemsArray):

	// 		if($itemsArray['status'] == "CL"):
	// 			$createdAt   = date('Y-m-d H:i', $itemsArray['update_date']);	
	// 			$OrderStatus = "Cancelled";
	// 		elseif($itemsArray['order_status']):
	// 			$createdAt   = $itemsArray['created_at'];
	// 			$OrderStatus = $itemsArray['order_status'];
	// 		endif;

	// 		// $ticket   		   = json_decode($itemsArray['ticket']);
	// 		$selection_values  = json_decode($itemsArray['selection_values']);
	// 		$selection_values  = json_decode($itemsArray['selection_values']);
	// 		$Ticket = str_replace('[[', '', $itemsArray['ticket']);
    //         $Ticket = str_replace(']]', '/', $Ticket);
    //         $Ticket = str_replace('],[', '/', $Ticket);
    //         $ticket = array_filter(explode('/', $Ticket));
	// 		if($ticket):
	// 			foreach ($ticket as $subindex => $item):
	// 			  	$coupon = $item;
	// 			  	// $coupon = implode(',', $item);
	// 			  	if(!empty($itemsArray['selection_values'])):
	// 			  		$straight = $selection_values[$subindex][0]?1:0;
	// 				  	$rumble   = $selection_values[$subindex][1]?1:0;
	// 				  	$reverse  = $selection_values[$subindex][2]?1:0;
	// 			  	else:
	// 				  	$straight = $itemsArray['straight_add_on_amount']?1:0;
	// 				  	$rumble   = $itemsArray['rumble_add_on_amount']?1:0;
	// 				  	$reverse  = $itemsArray['reverse_add_on_amount']?1:0;
	// 			  	endif;
				   
	// 			  	if($itemsArray['seller_details']):
	// 			    	$seller_details = json_decode($itemsArray['seller_details']);
	// 			    	$words = explode(' ', $seller_details->Country);
	//                     $initials = '';
	//                     $countryPrefrx = '';
	//                     foreach ($words as $word):
	//                      $countryPrefrx .= $word[0];
	//                     endforeach;

	// 			  	else:
	// 			  		$seller_details = '';
	// 			  	endif;

	// 		  		$seller_POS 		  = isset($seller_details->posid)    ? $countryPrefrx.'_'.$seller_details->posid : (isset($itemsArray['pos_number']) ? $itemsArray['pos_number'] : 'N/A');
	// 		  		$seller_Name 		  = isset($seller_details->Name)     ? $seller_details->Name : (isset($itemsArray['users_name']) ? $itemsArray['users_name'] : 'N/A');
	// 		  		$seller_Mobile 		  = isset($seller_details->FoMobile) ? $seller_details->FoMobile : (isset($itemsArray['user_phone']) ? $itemsArray['user_phone'] : 'N/A');
	// 		  		$seller_Store 		  = isset($seller_details->Name) 	 ? $seller_details->Name : (isset($itemsArray['store_name']) ? $itemsArray['store_name'] : 'N/A');
	// 		  		$seller_Bindwith_Name = isset($seller_details->FoName)   ? $seller_details->FoName : (isset($itemsArray['bindwith_first_name']) ? $itemsArray['bindwith_first_name'] : 'N/A');

	// 		  		if( $seller_Bindwith_Name == 'N/A' &&  !empty($itemsArray['admin_bindwith_users_type']) ):
	// 		  			$seller_Bindwith_Name = $itemsArray['admin_bindwith_users_type'];
	// 		  		endif;

	// 		  		if($itemsArray['users_type'] == 'Users'):
	// 					$itemsArray['bindwith_first_name'] = 'Admin';
	// 					$seller_Store = $itemsArray['users_name'];
	// 				else:
	// 					$seller_Store = $itemsArray['store_name'];
	// 				endif;

	// 	  		    // $CSVData1['Sl.No']              = $sno++;
	// 			    $CSVData1['POS No.']            = !empty($itemsArray['pos_number']) ? $itemsArray['pos_number'] : 'N/A';
	// 				$CSVData1['Order ID']           = !empty($itemsArray['order_id']) ? $itemsArray['order_id'] : 'N/A';
	// 				$CSVData1['Product Name']       = !empty($itemsArray['product_name']) ? $itemsArray['product_name'] : 'N/A';
	// 				$CSVData1['Store Name']         = !empty($seller_Store) ? $seller_Store : 'N/A';
	// 				if($itemsArray['users_type'] == 'Users'):
	// 				 $CSVData1['Seller Name']        = !empty($itemsArray['users_address']) ? $itemsArray['users_address'] : 'N/A';
	// 				else:
	// 				 // $CSVData1['Seller Name']        = !empty($seller_Name) ? $seller_Name : 'N/A';
	// 				 $CSVData1['Seller Name']        = !empty($itemsArray['users_area']) ? $itemsArray['users_area'] : 'N/A';
	// 				endif;
	// 				 // $CSVData1['Seller Name']        = !empty($seller_Name) ? $seller_Name : 'N/A';
	// 				$CSVData1['Seller Mobile']      = !empty($seller_Mobile) ? $seller_Mobile : 'N/A';
	// 				$CSVData1['Bind With']          = !empty($seller_Bindwith_Name) ? $seller_Bindwith_Name : 'N/A';
	// 				$CSVData1['Straight Amount']    = !empty($straight) ? $straight : '0';
	// 				$CSVData1['Rumble Amount']      = !empty($rumble)   ? $rumble   : '0';
	// 				$CSVData1['Chance Amount']      = !empty($reverse)  ? $reverse  : '0';
	// 				$CSVData1['Payment Status']     = !empty($OrderStatus) ? $OrderStatus : 'N/A';
	// 				$CSVData1['Purchase Date']      = !empty($createdAt) ? $createdAt : 'N/A';
	// 				$CSVData1['Coupons']      	    = !empty($coupon) ? $coupon : 'N/A';
	// 				array_push($CSVData, $CSVData1);
	// 			endforeach;
	// 		endif;
	// 	endforeach;

	// 	echo json_encode($CSVData);
	// 	die();
	// }
	
	/***********************************************************************
	** Function name 	: generatecoupons
	** Developed By 	: Dilip halder
	** Purpose  		: This function used for generate coupons from admin panel.
	** Date 			: 20 July 2023
	** Updated Date 	:  
	** Updated By   	:  
	************************************************************************/
	public function generatecoupons()
	{	
		$this->admin_model->authCheck('add_data');
		$oid = $this->input->post('order_id');
		// $oid = "LZIDN3160191";
		//Get current order of user.
		$wcon['where']					=	[ 'order_id' => $oid ];
		$data['orderData'] 				=	$this->common_model->getData('single', 'uw_orders', $wcon);
		
		if($data['orderData']['user_id'] === 0):
			$user_phone  =  $data['orderData']['user_phone'];
			// $url = "http://localhost/d-arabia/api/telrOrderSuccess?user_phone=$user_phone&order_id=$oid";
			$url = "https://dealzarabia.com/api/telrOrderSuccess?user_phone=$user_phone&order_id=$oid";
			
		else:
			$users_id  =  $data['orderData']['user_id'];
			$url = "https://dealzarabia.com/api/telrOrderSuccess?users_id=$users_id&order_id=$oid";
			// $url = "http://localhost/d-arabia/api/telrOrderSuccess?users_id=$users_id&order_id=$oid";
		endif;

		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'GET',
			CURLOPT_HTTPHEADER => array(
				'Apikey: c9d58f135dab835ecf44e7c64b978599',
				'Apidate: 2022-06-13',
				'Cookie: ci_session=2t9td2m3ihodk6ptpp5ml3s3vuni65p4; ci_session=r4n3192ojb4ng4mo30m5gdstb4hu5n06; MainLoad=web2|ZLaPN|ZLaPN; MainLoad=web1|ZLkzn|ZLkzj'
			),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		$result = json_decode($response);

		if($result->message == 'Your order placed successfully'):
			$this->session->set_flashdata('alert_success',lang('Coupon_Generaion'));
			redirect(correctLink('ALLORDERSDATA',getCurrentControllerPath('index')));
		else:
			$this->session->set_flashdata('alert_success',lang('Coupon_Not_Generaion'));
			redirect(correctLink('ALLORDERSDATA',getCurrentControllerPath('index')));
		endif;
	}


	/***********************************************************************
	** Function name 	: CheckAvailableUWinCoupons
	** Developed By 	: Dilip halder
	** Purpose  		: This function used for check available coupons.
	** Date 			: 29 January 2024
	************************************************************************/
	public function CheckAvailableUWinCoupons($products_id ='')
	{
		$this->admin_model->authCheck('view_data');

		// Product Details
	 	$tblName 	 	   = 'uw_products';
		$whereCon['where'] = array('products_id' => (int)$products_id ,'status' => 'A' );
		$productDetails    = $this->common_model->getData('single',$tblName,$whereCon,$shortField);

		

		// lotto Order Details
		$tblName 	 	   = 'uw_lotto_orders';
		$whereCon['where'] = array('product_id' => (int)$products_id ,'status' => 'A' );
		$Field 			   = array('ticket','status');
		$orderDetails      = $this->common_model->getDataByNewQuery($Field,'multiple',$tblName,$whereCon);

		$soldoutNumber = array();

		if($orderDetails):
			foreach($orderDetails as $item):
				if($item && $item['status'] == 'A'):
					$tickets = json_decode($item['ticket']);
					foreach($tickets as $items):
						$soldoutNumber[] = $items;
					endforeach;
				endif;
			endforeach;
		endif;
		$result = $this->AvailableUWinCoupons($soldoutNumber ,$productDetails );

		return $result;

	}


	public function AvailableUWinCoupons($soldoutNumber='', $productDetails='')
	{
		$this->admin_model->authCheck('view_data');

		$lotto_type      = $productDetails['lotto_type'];
		$lotto_range     = $productDetails['lotto_range'];
		$products_id 	 = $productDetails['products_id'];

		$numbeUnique     = "Y";
		$required_ticket = 500;

		$resultNumbers = array();
		for ($i=0; $i <$required_ticket; $i++):
			$resultNumbers[] = $this->lottogenerate($lotto_type,$lotto_range ,$numbeUnique,$required_ticket);
		endfor;

		$UniqueCoupons = $this->checkresult($resultNumbers,$soldoutNumber);
		$result['unique_coupons'] = $UniqueCoupons;

		if($UniqueCoupons):

			$tblName 	 		= 	'uw_uwin_available_coupons';
			$shortField  		= 	array('products_id'=> -1);
			$whereCon['where']  =  array('products_id' => (int)$products_id);
			$existdata	 		=	$this->common_model->getData('count',$tblName,$whereCon,$shortField);

			$param['products_id']		=	(int)$products_id;
			$param['available_coupons']	=	$UniqueCoupons;
			if($existdata):
				$param['update_ip']		=	currentIp();
				$param['update_date']	=	(int)$this->timezone->utc_time();//currentDateTime();
				$param['updated_by']	=	(int)$this->session->userdata('UW_ADMIN_ID');
				$this->common_model->editData('uw_uwin_available_coupons',$param,'products_id',(int)$products_id);
			else:
				$param['creation_ip']	=	currentIp();
				$param['creation_date']	=	(int)$this->timezone->utc_time();//currentDateTime();
				$param['created_by']	=	(int)$this->session->userdata('UW_ADMIN_ID');
				$param['status']		=	'A';
				$alastInsertId			=	$this->common_model->addData('uw_uwin_available_coupons',$param);
			endif;
		endif;
		return $result;
	}


	public function lottogenerate($lotto_type,$ticket_range ,$numbeUnique)
	{
		$uniqueNumbers = [];
		// Generate 5 unique random numbers and add them to the array

			while (count($uniqueNumbers) < $lotto_type):
			    $randomNumber = rand(1, $ticket_range); // Change the range as per your requirement
			    
			    if($numbeUnique == "Y" && !in_array($randomNumber, $uniqueNumbers) ):
			        $uniqueNumbers[] = $randomNumber;
			    endif;

			 	if($numbeUnique == "N"):
			        $uniqueNumbers[] = $randomNumber;
			    endif;
			endwhile;
			return $uniqueNumbers;
	}

	public function checkresult($resultNumbers,$soldoutNumbers='')
	{

	 	foreach ($soldoutNumbers as $key => $soldoutNumber):

	        if (count($soldoutNumber) == count($resultNumbers) && array_search($resultNumbers, $soldoutNumbers) !== false):
	            return true;
	        else:
				return $resultNumbers;
	        endif;

    	endforeach;
	}

}
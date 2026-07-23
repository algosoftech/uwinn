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


class Allorders extends CI_Controller {

	private $shopExportPosCache = array();

	public function  __construct() 
	{ 
		parent:: __construct();
		error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);  
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
	// public function index()
	// {	
	// 	$this->admin_model->authCheck('view_data');
	// 	$data['error'] 						= 	'';
	// 	$data['activeMenu'] 				= 	'orders';
	// 	$data['activeSubMenu'] 				= 	'alllottooders';


	// 	if($this->input->get('fromDate')):
	// 		$fromDate	 = date('Y-m-d H:i', strtotime($this->input->get('fromDate')));
	// 	else:
	// 		$fromDate    = date('Y-m-d 22:01', strtotime('-1 day'));
	// 	endif;
	// 	if($this->input->get('toDate')):
	// 		$toDate	 	 = date('Y-m-d H:i', strtotime($this->input->get('toDate')));
	// 	else:
	// 		$toDate	 	 = date('Y-m-d 22:00');
	// 	endif;
		
	// 	$searchField   = $this->input->get('searchField');
	// 	$searchValue   = $this->input->get('searchValue');
		 

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

	// 	$whereCondition['where']['raffle_mode']   = array('$ne' => 'Y');

	// 	$data['searchField'] 			= $searchField;
	// 	$data['searchValue'] 			= $searchValue;
	// 	$data['fromDate'] 				= $fromDate;  
	// 	$data['toDate'] 				= $toDate;

	// 	// Where conditions section.
	// 	if(!empty($searchField) && !empty($searchValue)):
	// 		if($searchField == 'ticket'):
	// 		 	$whereCondition['where']	 = 	array($searchField=> "[[".$searchValue."]]" );
	// 		elseif($searchField == 'order_code'):
	// 			$whereCondition['where'][$searchField] = base64_encode($searchValue);	
	// 		else:
	// 		  	$whereCondition['where'][$searchField] = is_numeric($searchValue)?(int)$searchValue:$searchValue;
	// 		endif;

	// 	else:
	// 		$whereCondition['where']['order_status'] = array('$ne' => 'Initialize');
	// 	endif;

	// 	$baseUrl 							= 	getCurrentControllerPath('index');
		
	// 	$this->session->set_userdata('ALLORDERSDATA',currentFullUrl());
	// 	$qStringdata						=	explode('?',currentFullUrl());
	// 	$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
	// 	$tblName 							= 	'uw_lotto_orders';

	// 	$totalRows 							= 	$this->common_model->getData('count',$tblName,$whereCondition,$shortField,'0','0');
		
	// 	if($this->input->get('showLength') == 'All'):
	// 		$perPage	 					= 	$totalRows;
	// 		$data['perpage'] 				= 	$this->input->get('showLength');  
	// 	elseif($this->input->get('showLength')):
	// 		$perPage	 					= 	$this->input->get('showLength'); 
	// 		$data['perpage'] 				= 	$this->input->get('showLength'); 
	// 	else:
	// 		$perPage	 					= 	SHOW_NO_OF_DATA;
	// 		$data['perpage'] 				= 	SHOW_NO_OF_DATA; 
	// 	endif;

	// 	$uriSegment 						= 	getUrlSegment();
	//     $data['PAGINATION']					=	adminPagination($baseUrl,$suffix,$totalRows,$perPage,$uriSegment);

    //    if($this->uri->segment(getUrlSegment())):
    //        $page = $this->uri->segment(getUrlSegment());
    //    else:
    //        $page = 0;
    //    endif;
		
	// 	$data['forAction'] 					= 	$baseUrl; 
	// 	if($totalRows):
	// 		$first							=	(int)($page)+1;
	// 		$data['first']					=	$first;
			
	// 		if($data['perpage'] == 'All'):
	// 			$pageData 					=	$totalRows;
	// 		else:
	// 			$pageData 					=	$data['perpage'];
	// 		endif;
			
	// 		$last							=	((int)($page)+$pageData)>$totalRows?$totalRows:((int)($page)+$pageData);
	// 		$data['noOfContent']			=	'Showing '.$first.'-'.$last.' of '.$totalRows.' items';
	// 	else:
	// 		$data['first']					=	1;
	// 		$data['noOfContent']			=	'';
	// 	endif;

	// 	$startIndex   = $page;
	// 	$itemsPerPage = $pageData;
	// 	// $itemsPerPage = 1;
	// 	$resultType  			= '';
	// 	$data['ALLDATA']  		= $this->common_model->getOrderDetails($resultType,$whereCondition,$startIndex,$itemsPerPage);
	// 	// echo "<pre>";print_r($data);die();

	// 	$this->layouts->set_title('Orders | UWINN');
	// 	$this->layouts->admin_view('orders/allorders/index',array(),$data);
	// }	// END OF FUNCTION


	 public function index()
	{	
		$this->admin_model->authCheck('view_data');
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'orders';
		$data['activeSubMenu'] 				= 	'alllottooders';


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
				$whereCon['where'][$searchField] = base64_encode($searchValue);	
			else:
			  	$whereCon['where'][$searchField]   =  is_numeric($searchValue)?(int)$searchValue:$searchValue;
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
	 
		$data['ALLDATA']  = 	$this->common_model->getData('multiple',$tblName,$whereCon,$shortField,$perPage,$page);
		$data['ALLPRODUCT']  = 	$this->common_model->getData('multiple','uw_products',array('status'=> 'A'),array('title' => -1));
		// echo "<pre>";print_r($data);die();

		$this->layouts->set_title('Orders | UWINN');
		$this->layouts->admin_view('orders/allorders/index',array(),$data);
	}	// END OF FUNCTION


	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name : addeditdata
	 + + Developed By  : DILIP HALDER
	 + + Purpose  	   : This function used for Add Edit data
	 + + Date 		   : 07 February 2024
	 + + Updated By    : DILIP HALDER
	 + + Updated Date  : 17 June 2024
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function addeditdata($editId='')
	{		
		$data['error'] 			= 	'';
		$data['activeMenu'] 	= 	'orders';
		$data['activeSubMenu'] 	= 	'allorders';
		
		$this->admin_model->authCheck('edit_data');
		if($editId):
			$data['orderData']	  =	$this->common_model->getDataByParticularField('uw_lotto_orders','order_id',$editId);
            
            $drawId 			  = $data['orderData']['draw_id'];
            $serchfields 		  = array('draw_date','draw_time','products_id');
            $tblName     		  = 'uw_products_draw_records';
            $wcon['where']        = array('draw_id' => (int)$drawId);
            $data['drawDetails']  = $this->common_model->getParticularFieldByMultipleCondition($serchfields,$tblName ,$wcon);
		else:
			redirect(correctLink('ALLORDERSDATA',getCurrentControllerPath('index')));
		endif;

		$this->layouts->set_title('Orders | UWINN');
		$this->layouts->admin_view('orders/allorders/addeditdata',array(),$data);
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
		$this->common_model->editData('uw_category',$param,'category_id',(int)$changeStatusId);
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		
		redirect(correctLink('ALLORDERSDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name 	: cancelationorder
	** Developed By 	: Dilip Kumar Halder
	** Purpose  		: This function used for order cancelation by admin.
	** Date 			: 05 June 2023
	************************************************************************/
	// function cancelationorder($changeStatusId='')
	// {  
		
	// 	// $array = array();
	// 	// for ($i=0; $i <count($array) ; $i++) { 

	// 	// 	$orderID = $array[$i];

	// 	// 	$tblName 				= 'uw_lotto_orders';
	// 	// 	$whereCon['where']		= array('order_id' => $orderID  );
	// 	// 	$shortField 			= array('sequence_id' => -1);
	// 	// 	$cancleOrderData 		= $this->common_model->getData('single',$tblName,$whereCon,$shortField,'0','0');

	// 	// 	if($cancleOrderData['status']  == 'A'):


	// 	// 		$param1['status']			= 'CL';
	// 	// 		$param1['update_ip']		=	currentIp();
	// 	// 		$param1['update_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
	// 	// 		$param1['refund_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
	// 	// 		$param1['updated_by']		=	(int)$this->session->userdata('UW_ADMIN_ID');
				
	// 	// 		// echo "<pre>";print_r($param1);die();
	// 	// 		$this->common_model->editData('uw_lotto_orders',$param1,'order_id',$orderID );

	// 	// 		// Checking Sender User.
	// 	// 		$userid = $cancleOrderData['user_id'];
				
	// 	// 		$tblName 			=   'uw_users';
	// 	// 		$whereCon['where']	=	array('users_id' => $userid , 'status'=> 'A' );
	// 	// 		$shortField 		=   array('users_id' => -1);
	// 	// 		$UserData 			= 	$this->common_model->getData('single',$tblName,$whereCon,$shortField,'0','0');
	// 	// 		if($cancleOrderData['user_type'] == "Users"){
	// 	// 			// $message = 'Order ID '.$cancleOrderData['order_id'].' has been canceled as the order was incomplete.';
	// 	// 			// $title = 'Order Canceled: Incomplete Details ('.$cancleOrderData['order_id'].')';
	// 	// 			// $this->common_model->saveNotifications($userid,$title,$message,$cancleOrderData['order_id']);
	// 	// 		}
	// 	// 		// Refund wallet statement Code start here..
	// 	// 		$user_oid 			   					 = $UserData['_id']->{'$id'};
	// 	// 		$commission_amount                       = (float)$cancleOrderData['total_price'];

	// 	// 		$refundparam["load_balance_id"]		     =	(int)$this->common_model->getNextSequence('uw_loadBalance');
	// 	// 		$refundparam["order_oid"] 			     =	new MongoDB\BSON\ObjectId($cancleOrderData['_id']->{'$id'});
	// 	// 		$refundparam["user_oid"] 				 =	new MongoDB\BSON\ObjectId($user_oid);
	// 	// 		$refundparam["user_id_cred"] 			 =	(int)$UserData['users_id'];
	// 	// 		$refundparam["user_id_deb"]			 	 =	(int)0;
	// 	// 		$refundparam["order_id"] 				 =	$cancleOrderData['order_id'];
	// 	// 		$refundparam["upoints"] 				 =	(float)$commission_amount;
	// 	// 		$refundparam["availableArabianPoints"] 	 =	(float)$UserData['availableArabianPoints'];
	// 	// 		$refundparam["end_balance"] 			 =	(float)$UserData['availableArabianPoints'] + (float)$cancleOrderData['total_price'];
	// 	// 	    $refundparam["record_type"] 			 =	'Credit';
	// 	// 	    $refundparam["narration"]				 =	'Order Cancelled';
	// 	// 	    $refundparam["remarks"]				 =	'Ticket ID : '.$cancleOrderData['order_id'];
	// 	// 	    $refundparam["creation_ip"] 			 =	$this->input->ip_address();
	// 	// 	    $refundparam["created_at"] 			 =	date('Y-m-d H:i');
	// 	// 	    $refundparam["created_by"] 			 =	(int)$this->input->get('users_id');
	// 	// 	    $refundparam["status"] 				 =	"A";
	// 	// 	    $this->common_model->addData('uw_loadBalance', $refundparam);

	// 	// 	   	$tblName 			=   'uw_loadBalance';
	// 	// 		$whereCon['where']	=	array('status'=> 'A' , 'narration'=> 'Commission' , 'order_oid' => new MongoDB\BSON\ObjectId($cancleOrderData['_id']->{'$id'}) );
	// 	// 		$commissionList 	= 	$this->common_model->getData('single',$tblName,$whereCon,$shortField);
	// 	// 		$commissionAmount 	= $commissionList['upoints'];
	// 	// 		// Refund wallet statement Code End here..

	// 	// 		if($cancleOrderData['user_type'] != "Users"):	
	// 	// 		    // Commission capturing in order uw_loadbalance table.. // BTB
	// 	// 		    $commissionParam["load_balance_id"]		 	 =	(int)$this->common_model->getNextSequence('uw_loadBalance');
	// 	// 			$commissionParam["order_oid"] 			 	 =	new MongoDB\BSON\ObjectId($cancleOrderData['_id']->{'$id'});
	// 	// 			$commissionParam["user_oid"] 				 =	new MongoDB\BSON\ObjectId($user_oid);
	// 	// 			$commissionParam["user_id_deb"]			 	 =	(int)$UserData['users_id'];
	// 	// 			$commissionParam["user_id_cred"] 			 =	(int)0;
	// 	// 			$commissionParam["order_id"] 				 =	$cancleOrderData['order_id'];
	// 	// 			$commissionParam["upoints"] 				 =	(float)$commissionAmount;
	// 	// 			$commissionParam["availableArabianPoints"] 	 =	(float)$refundparam["end_balance"];
	// 	// 			$commissionParam["end_balance"] 			 =	(float)$refundparam["end_balance"] - $commissionAmount;
	// 	// 		    $commissionParam["record_type"] 			 =	'Debit';
	// 	// 		    $commissionParam["narration"]				 =	'Commission Reverted';
	// 	// 		    $commissionParam["remarks"]				 	 =	'Ticket ID : '.$cancleOrderData['order_id'];
	// 	// 		    $commissionParam["creation_ip"] 			 =	$this->input->ip_address();
	// 	// 		    $commissionParam["created_at"] 				 =	date('Y-m-d H:i');
	// 	// 		    $commissionParam["created_by"] 			 	 =	(int)$this->input->get('users_id');
	// 	// 		    $commissionParam["status"] 				 	 =	"A";
	// 	// 	    	$this->common_model->addData('uw_loadBalance', $commissionParam);
	// 	// 	    	// Credit the purchesed points and get available arabian points of user.
	// 	// 			// Refunded Sender Cancelation Order Amount.
	// 	// 			if($UserData['availableArabianPoints']):
	// 	// 				$param['availableArabianPoints'] = $commissionParam["end_balance"];
	// 	// 				$this->common_model->editData('uw_users',$param,'users_id',(int)$userid);	
	// 	// 			endif;
	// 	// 	    else:
	// 	// 	    	// Refunded Sender Cancelation Order Amount. // BTC
	// 	// 			if($UserData['availableArabianPoints']):
	// 	// 				$param['availableArabianPoints'] = $refundparam["end_balance"];
	// 	// 				$this->common_model->editData('uw_users',$param,'users_id',(int)$userid);	
	// 	// 			endif;
	// 	// 	    endif;

	// 	// 	endif;
	// 	// }
		
	// 	$this->admin_model->authCheck('edit_data');
	// 	// $this->common_model->editData('uw_category',$param,'category_id',(int)$changeStatusId);
	// 	$tblName 				= 'uw_lotto_orders';
	// 	$whereCon['where']		= array('_id' => new MongoDB\BSON\ObjectId($changeStatusId) );
	// 	$shortField 			= array('sequence_id' => -1);
	// 	$cancleOrderData 		= $this->common_model->getData('single',$tblName,$whereCon,$shortField,'0','0');
		

	// 	if($changeStatusId == '6979bbda5d79ab11ac02c294'):

	// 		try {

	// 			$tblName 				= 'uw_lotto_orders';
	// 			$whereCon['where']		= array('_id' => new MongoDB\BSON\ObjectId($changeStatusId) );
	// 			$shortField 			= array('sequence_id' => -1);
	// 			$cancleOrderData 		= $this->common_model->getData('single',$tblName,$whereCon,$shortField,'0','0');

	// 			if(empty($cancleOrderData)):
	// 				throw new Exception(lang('ORDER_NOT_FOUND'), 1);
	// 			elseif($cancleOrderData['status']  == 'CL'):
	// 				throw new Exception(lang('ALREADY_CANCELLED'));
	// 			else:

	// 				$this->load->library('mongodb_client');
	// 				$this->session->sess_regenerate();
	// 				$session = $this->mongodb_client->client->startSession();
	// 				$session->startTransaction();

	// 				//Adding calcelation variable and value.
	// 				// $param1['status']	   = 'CL';
	// 				$param1['update_ip']   = currentIp();
	// 				$param1['update_date'] = (int)$this->timezone->utc_time();//currentDateTime();
	// 				$param1['refund_date'] = (int)$this->timezone->utc_time();//currentDateTime();
	// 				$param1['updated_by']  = (int)$this->session->userdata('UW_ADMIN_ID');
	// 				$cancleOrderWhereCon   = array('_id' => new MongoDB\BSON\ObjectId($changeStatusId));
	// 				$update1 = $this->mongodb_client->updateDocument($tblName, $cancleOrderWhereCon,  ['$set' => $param1],$session);


	// 				// Updating raffle 
	// 				$tblName1 = 'uw_raffle_eligible_orders';
	// 				$whereConCancel	= array('order_id' => $cancleOrderData['order_id']);
	// 				$update2 = $this->mongodb_client->updateDocument($tblName1, $whereConCancel,  ['$set' => $param1],$session);


	// 				// Checking Sender User.
	// 				$tblName 			=   'uw_users';
	// 				$whereCon['where']	=	array('users_id' => $cancleOrderData['user_id'] , 'status'=> 'A' );
	// 				$shortField 		=   array('users_id' => -1);
	// 				$UserData 			= 	$this->common_model->getData('single',$tblName,$whereCon,$shortField,'0','0');

	// 				// Sending Notification alert..
	// 				$userid = $cancleOrderData['user_id'];
	// 				if($cancleOrderData['user_type'] == "Users"){
	// 					$message = 'Order ID '.$cancleOrderData['order_id'].' has been canceled as the order was incomplete.';
	// 					$title = 'Order Canceled: Incomplete Details ('.$cancleOrderData['order_id'].')';
	// 					$this->common_model->saveNotifications($userid,$title,$message,$cancleOrderData['order_id']);
	// 				}


	// 				// Refund wallet statement Code start here..
	// 				$user_oid 			   					 = $UserData['_id']->{'$id'};
	// 				$commission_amount                       = (float)$cancleOrderData['total_price'];
	// 				$refundparam["load_balance_id"]		     =	(int)$this->common_model->getNextSequence('uw_loadBalance');
	// 				$refundparam["order_oid"] 			     =	new MongoDB\BSON\ObjectId($cancleOrderData['_id']->{'$id'});
	// 				$refundparam["user_oid"] 				 =	new MongoDB\BSON\ObjectId($user_oid);
	// 				$refundparam["user_id_cred"] 			 =	(int)$UserData['users_id'];
	// 				$refundparam["user_id_deb"]			 	 =	(int)0;
	// 				$refundparam["order_id"] 				 =	$cancleOrderData['order_id'];
	// 				$refundparam["upoints"] 				 =	(float)$commission_amount;
	// 				$refundparam["availableArabianPoints"] 	 =	(float)$UserData['availableArabianPoints'];
	// 				$refundparam["end_balance"] 			 =	(float)$UserData['availableArabianPoints'] + (float)$cancleOrderData['total_price'];
	// 				$refundparam["record_type"] 			 =	'Credit';
	// 				$refundparam["narration"]				 =	'Order Cancelled';
	// 				$refundparam["remarks"]				 =	'Ticket ID : '.$cancleOrderData['order_id'];
	// 				$refundparam["creation_ip"] 			 =	$this->input->ip_address();
	// 				$refundparam["created_at"] 			 =	date('Y-m-d H:i');
	// 				$refundparam["created_by"] 			 =	(int)$this->input->get('users_id');
	// 				$refundparam["status"] 				 =	"A";
	// 				$update3 = $this->mongodb_client->insertDocument('uw_loadBalance', $refundparam, $session);


	// 				$tblName 			=   'uw_loadBalance';
	// 				$whereCon['where']	=	array('status'=> 'A' , 'narration'=> 'Commission' , 'order_oid' => new MongoDB\BSON\ObjectId($cancleOrderData['_id']->{'$id'}) );
	// 				$commissionList 	= 	$this->common_model->getData('single',$tblName,$whereCon,$shortField);
	// 				$commissionAmount 	= $commissionList['upoints'];

	// 				if($cancleOrderData['user_type'] != "Users"):	
	// 					// Commission capturing in order uw_loadbalance table.. // BTB
	// 					$commissionParam["load_balance_id"]		 	 =	(int)$this->common_model->getNextSequence('uw_loadBalance');
	// 					$commissionParam["order_oid"] 			 	 =	new MongoDB\BSON\ObjectId($cancleOrderData['_id']->{'$id'});
	// 					$commissionParam["user_oid"] 				 =	new MongoDB\BSON\ObjectId($user_oid);
	// 					$commissionParam["user_id_deb"]			 	 =	(int)$UserData['users_id'];
	// 					$commissionParam["user_id_cred"] 			 =	(int)0;
	// 					$commissionParam["order_id"] 				 =	$cancleOrderData['order_id'];
	// 					$commissionParam["upoints"] 				 =	(float)$commissionAmount;
	// 					$commissionParam["availableArabianPoints"] 	 =	(float)$refundparam["end_balance"];
	// 					$commissionParam["end_balance"] 			 =	(float)$refundparam["end_balance"] - $commissionAmount;
	// 					$commissionParam["record_type"] 			 =	'Debit';
	// 					$commissionParam["narration"]				 =	'Commission Reverted';
	// 					$commissionParam["remarks"]				 	 =	'Ticket ID : '.$cancleOrderData['order_id'];
	// 					$commissionParam["creation_ip"] 			 =	$this->input->ip_address();
	// 					$commissionParam["created_at"] 				 =	date('Y-m-d H:i');
	// 					$commissionParam["created_by"] 			 	 =	(int)$this->input->get('users_id');
	// 					$commissionParam["status"] 				 	 =	"A";
	// 					$update4 = $this->mongodb_client->insertDocument('uw_loadBalance', $commissionParam, $session);

						
						
						
						
	// 					// Credit the purchesed points and get available arabian points of user.
	// 					// Refunded Sender Cancelation Order Amount.
	// 					$param['availableArabianPoints'] = $commissionParam["end_balance"];
	// 					$this->common_model->editData('uw_users',$param,'users_id',(int)$userid);
						
	// 					$creditwhereWhereCon = ara
	// 					$update4 = $this->mongodb_client->updateDocument('uw_users', $creditwhereWhereCon,  ['$set' => $param],$session);




	// 					if($UserData['availableArabianPoints']):
	// 					endif;
	// 				else:
	// 					// Refunded Sender Cancelation Order Amount. // BTC
	// 					$param['availableArabianPoints'] = $refundparam["end_balance"];
	// 					$this->common_model->editData('uw_users',$param,'users_id',(int)$userid);	
	// 					if($UserData['availableArabianPoints']):
	// 					endif;
	// 				endif;










	// 				echo "<pre>";print_r($commissionList);die();


					
	// 			endif;


	// 			// echo "<pre>";print_r($cancleOrderData);die();



	// 			//code...
	// 		} catch (\Throwable $th) {

	// 			echo "<pre>";print_r($th);die();
	// 			//throw $th;
	// 		}


	// 		// $this->load->library('mongodb_client');

	// 		echo "<pre>";
	// 		print_r($cancleOrderData);
	// 		die();
			
	// 	endif;

	// 	if($cancleOrderData['status']  == 'CL'):
	// 		$this->session->set_flashdata('alert_error',lang('ALREADY_CANCELLED'));
	// 		redirect(correctLink('ALLORDERSDATA',getCurrentControllerPath('index')));
	// 	endif;
		
	// 		//Adding calcelation variable and value.
	// 		$param1['status']			= 'CL';
	// 		$param1['update_ip']		=	currentIp();
	// 		$param1['update_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
	// 		$param1['refund_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
	// 		$param1['updated_by']		=	(int)$this->session->userdata('UW_ADMIN_ID');
			
	// 		// echo "<pre>";print_r($param1);die();
	// 		$this->common_model->editData('uw_lotto_orders',$param1,'_id',new MongoDB\BSON\ObjectId($changeStatusId));

	// 		$whereConCancel	= array('order_id' => $cancleOrderData['order_id']);
	// 		$this->common_model->editMultipleDataByMultipleCondition('uw_raffle_eligible_orders',$param1,$whereConCancel);

	// 		// Checking Sender User.
	// 		$userid = $cancleOrderData['user_id'];
			
	// 		$tblName 			=   'uw_users';
	// 		$whereCon['where']	=	array('users_id' => $userid , 'status'=> 'A' );
	// 		$shortField 		=   array('users_id' => -1);
	// 		$UserData 			= 	$this->common_model->getData('single',$tblName,$whereCon,$shortField,'0','0');
	// 		if($cancleOrderData['user_type'] == "Users"){
	// 			$message = 'Order ID '.$cancleOrderData['order_id'].' has been canceled as the order was incomplete.';
	// 			$title = 'Order Canceled: Incomplete Details ('.$cancleOrderData['order_id'].')';
	// 			$this->common_model->saveNotifications($userid,$title,$message,$cancleOrderData['order_id']);
	// 		}
	// 		// Refund wallet statement Code start here..
	// 		$user_oid 			   					 = $UserData['_id']->{'$id'};
	// 		$commission_amount                       = (float)$cancleOrderData['total_price'];

	// 		$refundparam["load_balance_id"]		     =	(int)$this->common_model->getNextSequence('uw_loadBalance');
	// 		$refundparam["order_oid"] 			     =	new MongoDB\BSON\ObjectId($cancleOrderData['_id']->{'$id'});
	// 		$refundparam["user_oid"] 				 =	new MongoDB\BSON\ObjectId($user_oid);
	// 		$refundparam["user_id_cred"] 			 =	(int)$UserData['users_id'];
	// 		$refundparam["user_id_deb"]			 	 =	(int)0;
	// 		$refundparam["order_id"] 				 =	$cancleOrderData['order_id'];
	// 		$refundparam["upoints"] 				 =	(float)$commission_amount;
	// 		$refundparam["availableArabianPoints"] 	 =	(float)$UserData['availableArabianPoints'];
	// 		$refundparam["end_balance"] 			 =	(float)$UserData['availableArabianPoints'] + (float)$cancleOrderData['total_price'];
	// 	    $refundparam["record_type"] 			 =	'Credit';
	// 	    $refundparam["narration"]				 =	'Order Cancelled';
	// 	    $refundparam["remarks"]				 =	'Ticket ID : '.$cancleOrderData['order_id'];
	// 	    $refundparam["creation_ip"] 			 =	$this->input->ip_address();
	// 	    $refundparam["created_at"] 			 =	date('Y-m-d H:i');
	// 	    $refundparam["created_by"] 			 =	(int)$this->input->get('users_id');
	// 	    $refundparam["status"] 				 =	"A";
	// 	    $this->common_model->addData('uw_loadBalance', $refundparam);

	// 	   	$tblName 			=   'uw_loadBalance';
	// 		$whereCon['where']	=	array('status'=> 'A' , 'narration'=> 'Commission' , 'order_oid' => new MongoDB\BSON\ObjectId($cancleOrderData['_id']->{'$id'}) );
	// 		$commissionList 	= 	$this->common_model->getData('single',$tblName,$whereCon,$shortField);
	// 		$commissionAmount 	= $commissionList['upoints'];
	// 		// Refund wallet statement Code End here..

	// 		if($cancleOrderData['user_type'] != "Users"):	
	// 		    // Commission capturing in order uw_loadbalance table.. // BTB
	// 		    $commissionParam["load_balance_id"]		 	 =	(int)$this->common_model->getNextSequence('uw_loadBalance');
	// 			$commissionParam["order_oid"] 			 	 =	new MongoDB\BSON\ObjectId($cancleOrderData['_id']->{'$id'});
	// 			$commissionParam["user_oid"] 				 =	new MongoDB\BSON\ObjectId($user_oid);
	// 			$commissionParam["user_id_deb"]			 	 =	(int)$UserData['users_id'];
	// 			$commissionParam["user_id_cred"] 			 =	(int)0;
	// 			$commissionParam["order_id"] 				 =	$cancleOrderData['order_id'];
	// 			$commissionParam["upoints"] 				 =	(float)$commissionAmount;
	// 			$commissionParam["availableArabianPoints"] 	 =	(float)$refundparam["end_balance"];
	// 			$commissionParam["end_balance"] 			 =	(float)$refundparam["end_balance"] - $commissionAmount;
	// 		    $commissionParam["record_type"] 			 =	'Debit';
	// 		    $commissionParam["narration"]				 =	'Commission Reverted';
	// 		    $commissionParam["remarks"]				 	 =	'Ticket ID : '.$cancleOrderData['order_id'];
	// 		    $commissionParam["creation_ip"] 			 =	$this->input->ip_address();
	// 		    $commissionParam["created_at"] 				 =	date('Y-m-d H:i');
	// 		    $commissionParam["created_by"] 			 	 =	(int)$this->input->get('users_id');
	// 		    $commissionParam["status"] 				 	 =	"A";
	// 	    	$this->common_model->addData('uw_loadBalance', $commissionParam);
	// 	    	// Credit the purchesed points and get available arabian points of user.
	// 			// Refunded Sender Cancelation Order Amount.
	// 			$param['availableArabianPoints'] = $commissionParam["end_balance"];
	// 			$this->common_model->editData('uw_users',$param,'users_id',(int)$userid);	
	// 			if($UserData['availableArabianPoints']):
	// 			endif;
	// 	    else:
	// 	    	// Refunded Sender Cancelation Order Amount. // BTC
	// 			$param['availableArabianPoints'] = $refundparam["end_balance"];
	// 			$this->common_model->editData('uw_users',$param,'users_id',(int)$userid);	
	// 			if($UserData['availableArabianPoints']):
	// 			endif;
	// 	    endif;
		    
	// 		// User detail  used for email and sms  
	// 		// $this->emailsendgrid_model->sendlottoOrderMailToUser($refundparam['order_id']);
	// 		// $this->sms_model->sendLottoTicketDetails($refundparam['order_id']);

	// 	$this->session->set_flashdata('alert_success',lang('ordercenclesuccess'));
		
	// 	redirect(correctLink('ALLORDERSDATA',getCurrentControllerPath('index')));
	// }

	/***********************************************************************
	** Function name 	: cancelationorder
	** Developed By 	: Dilip Kumar Halder
	** Purpose  		: This function used for order cancelation by admin.
	** Date 			: 05 June 2023
	************************************************************************/
	function cancelationorder($changeStatusId='')
	{  
		try {

			$tblName 				= 'uw_lotto_orders';
			$whereCon['where']		= array('_id' => new MongoDB\BSON\ObjectId($changeStatusId) );
			$shortField 			= array('sequence_id' => -1);
			$cancleOrderData 		= $this->common_model->getData('single',$tblName,$whereCon,$shortField,'0','0');

			if(empty($cancleOrderData)):
				throw new Exception(lang('ORDER_NOT_FOUND'), 1);
			elseif($cancleOrderData['status']  == 'CL'):
				throw new Exception(lang('ALREADY_CANCELLED'));
			else:

				$this->load->library('mongodb_client');
				$this->session->sess_regenerate();
				$session = $this->mongodb_client->client->startSession();
				$session->startTransaction();

				//Adding calcelation variable and value.
				$param1['status']	   = 'CL';
				$param1['update_ip']   = currentIp();
				$param1['update_date'] = (int)$this->timezone->utc_time();//currentDateTime();
				$param1['refund_date'] = (int)$this->timezone->utc_time();//currentDateTime();
				$param1['updated_by']  = (int)$this->session->userdata('UW_ADMIN_ID');
				$param1['cancel_reason'] = 'Admin Cancel';
				$param1['admin_id']      = (int)$this->session->userdata('UW_ADMIN_ID');
				$cancleOrderWhereCon   = array('_id' => new MongoDB\BSON\ObjectId($changeStatusId));
				$update1 = $this->mongodb_client->updateDocument($tblName, $cancleOrderWhereCon,  ['$set' => $param1],$session);


				// Updating raffle 
				$tblName1 = 'uw_raffle_eligible_orders';
				$whereConCancel	= array('order_id' => $cancleOrderData['order_id']);
				$update2 = $this->mongodb_client->updateDocument($tblName1, $whereConCancel,  ['$set' => $param1],$session);


				// Checking Sender User.
				$tblName 			=   'uw_users';
				$whereCon['where']	=	array('users_id' => $cancleOrderData['user_id'] , 'status'=> 'A' );
				$shortField 		=   array('users_id' => -1);
				$UserData 			= 	$this->common_model->getData('single',$tblName,$whereCon,$shortField,'0','0');

				// Sending Notification alert..
				$userid = $cancleOrderData['user_id'];
				if($cancleOrderData['user_type'] == "Users"){
					$message = 'Order ID '.$cancleOrderData['order_id'].' has been canceled as the order was incomplete.';
					$title = 'Order Canceled: Incomplete Details ('.$cancleOrderData['order_id'].')';
					$this->common_model->saveNotifications($userid,$title,$message,$cancleOrderData['order_id']);
				}


				// Refund wallet statement Code start here..
				$user_oid 			   					 = $UserData['_id']->{'$id'};
				$commission_amount                       = (float)$cancleOrderData['total_price'];
				$refundparam["load_balance_id"]		     =	(int)$this->common_model->getNextSequence('uw_loadBalance');
				$refundparam["order_oid"] 			     =	new MongoDB\BSON\ObjectId($cancleOrderData['_id']->{'$id'});
				$refundparam["user_oid"] 				 =	new MongoDB\BSON\ObjectId($user_oid);
				$refundparam["user_id_cred"] 			 =	(int)$UserData['users_id'];
				$refundparam["user_id_deb"]			 	 =	(int)0;
				$refundparam["order_id"] 				 =	$cancleOrderData['order_id'];
				$refundparam["upoints"] 				 =	(float)$commission_amount;
				$refundparam["availableArabianPoints"] 	 =	(float)$UserData['availableArabianPoints'];
				$refundparam["end_balance"] 			 =	(float)$UserData['availableArabianPoints'] + (float)$cancleOrderData['total_price'];
				$refundparam["record_type"] 			 =	'Credit';
				$refundparam["narration"]				 =	'Order Cancelled';
				$refundparam["remarks"]				 =	'Ticket ID : '.$cancleOrderData['order_id'];
				$refundparam["creation_ip"] 			 =	$this->input->ip_address();
				$refundparam["created_at"] 			 =	date('Y-m-d H:i');
				$refundparam["created_by"] 			 =	(int)$this->input->get('users_id');
				$refundparam["status"] 				 =	"A";
				$update3 = $this->mongodb_client->insertDocument('uw_loadBalance', $refundparam, $session);


				$tblName 			=   'uw_loadBalance';
				$whereCon['where']	=	array('status'=> 'A' , 'narration'=> 'Commission' , 'order_oid' => new MongoDB\BSON\ObjectId($cancleOrderData['_id']->{'$id'}) );
				$commissionList 	= 	$this->common_model->getData('single',$tblName,$whereCon,$shortField);
				$commissionAmount 	= $commissionList['upoints'];

				if($cancleOrderData['user_type'] != "Users"):	
					// Commission capturing in order uw_loadbalance table.. // BTB
					$commissionParam["load_balance_id"]		 	 =	(int)$this->common_model->getNextSequence('uw_loadBalance');
					$commissionParam["order_oid"] 			 	 =	new MongoDB\BSON\ObjectId($cancleOrderData['_id']->{'$id'});
					$commissionParam["user_oid"] 				 =	new MongoDB\BSON\ObjectId($user_oid);
					$commissionParam["user_id_deb"]			 	 =	(int)$UserData['users_id'];
					$commissionParam["user_id_cred"] 			 =	(int)0;
					$commissionParam["order_id"] 				 =	$cancleOrderData['order_id'];
					$commissionParam["upoints"] 				 =	(float)$commissionAmount;
					$commissionParam["availableArabianPoints"] 	 =	(float)$refundparam["end_balance"];
					$commissionParam["end_balance"] 			 =	(float)$refundparam["end_balance"] - $commissionAmount;
					$commissionParam["record_type"] 			 =	'Debit';
					$commissionParam["narration"]				 =	'Commission Reverted';
					$commissionParam["remarks"]				 	 =	'Ticket ID : '.$cancleOrderData['order_id'];
					$commissionParam["creation_ip"] 			 =	$this->input->ip_address();
					$commissionParam["created_at"] 				 =	date('Y-m-d H:i');
					$commissionParam["created_by"] 			 	 =	(int)$this->input->get('users_id');
					$commissionParam["status"] 				 	 =	"A";
					$update4 = $this->mongodb_client->insertDocument('uw_loadBalance', $commissionParam, $session);

					
					// Credit the purchesed points and get available arabian points of user.
					// Refunded Sender Cancelation Order Amount.
					$param = array('availableArabianPoints' =>  $commissionParam["end_balance"]);
					$creditwhereWhereCon = array('users_id' => (int)$userid);
					$update4 = $this->mongodb_client->updateDocument('uw_users', $creditwhereWhereCon,  ['$set' => $param],$session);
				else:
					// Refunded Sender Cancelation Order Amount. // BTC
					$creditwhereWhereCon = array('users_id' => (int)$userid);
					$param   = array('availableArabianPoints' =>  $refundparam["end_balance"]);
					$update4 = $this->mongodb_client->updateDocument('uw_users', $creditwhereWhereCon,  ['$set' => $param],$session);
				endif;

				if($update1):
					$session->commitTransaction();
					$this->session->set_flashdata('alert_success',lang('ORDER_CANCELLED'));
					redirect(correctLink('ALLORDERSDATA',getCurrentControllerPath('index')));
				else:
					$session->abortTransaction();
					throw new Exception(lang('ORDER_CANCEL_FAILED'), 1);
				endif;


			endif;
		} catch (Exception $e) {
			$this->session->set_flashdata('alert_error', $e->getMessage());
			redirect(correctLink('ALLORDERSDATA',getCurrentControllerPath('index')));
		}
	}

	/***********************************************************************
	** Function name 	: deletedata
	** Developed By 	: DILIP HALDER
	** Purpose  		: This function used for delete data
	** Date 			: 07 February 2024
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
	************************************************************************/
	function exportexcel()
	{	
		$this->admin_model->authCheck('view_data');
		$data['error'] 			 = '';
		$data['activeMenu'] 	 = 'orders';
		$data['activeSubMenu'] 	 = 'allorders';
		
		//Generating Logs
	    $this->common_model->generateLogs();

		// -----------------------------------------------------------------------------//
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
		// -----------------------------------------------------------------------------//
		$searchField = $this->input->post('searchField');
		$searchValue = $this->input->post('searchValue');
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

		$whereCondition['where']['raffle_mode']   = array('$ne' => 'Y');
		
		
		// -----------------------------------------------------------------------------//
		if(!empty($searchField) && !empty($searchValue)):
			
			if($searchField == 'order_code'):
				$whereCondition['where']		 	   = 	array($searchField =>  base64_encode($searchValue));	
			elseif($searchField == 'ticket'):
				$whereCondition['where']		 	   =  array($searchField => "[[".$searchValue."]]" );	
			else:
				$whereCondition['where'][$searchField] =  is_numeric($searchValue)?(int)$searchValue:$searchValue;
			endif;
		else:
			$whereCondition['where']['order_status']   = array('$ne' => 'Initialize');
		endif;
			$whereCondition['where']['raffle_mode']    = array('$ne' => 'Y');
		if($productIds <> "" && count($productIds) > 0):
			$whereCondition['where']['product_id']['$in']  =  $productIds;
		endif;
		// -----------------------------------------------------------------------------//
		$resultType   = "count";
		$tblName 	  = "uw_lotto_orders";
		$totalRows 	  = $this->common_model->getOrderDetails($resultType,$whereCondition,'','',$tblName,true);
		 
		$itemsPerPage = 5000;
		// ---------------------------------------------

		$longArray = $totalRows;
		
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
		
		$totalpage 				= count($totalpage);
		$data['current_page']   = $current_page;
		$data['total_page'] 	= $totalpage;
		
		$data['searchField'] 	=   $searchField;
		$data['searchValue'] 	=   $searchValue;
		$data['fromDate'] 		=   $fromDate;
		$data['toDate'] 		=   $toDate;
		$data['productIds']		= 	json_encode($productIds);
		// $data['OrderData'] 		= $OrderData?$OrderData:array();

		// echo "<pre>";
		// print_r($data);
		// die();


		// echo "<pre>";print_r($data);die();
		$this->layouts->set_title('Export CSV | UWINN');
		$this->layouts->admin_view('orders/allorders/exportexcel',array(),$data);
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name 	: exportexcelApi
	** Developed By 	: Dilip halder
	** Purpose  		: This function used for export order data
	** Date 			: 23 July 2024
	************************************************************************/
	function exportexcelApi(){
		
		$this->admin_model->authCheck('view_data');
		$data['error'] 		   = '';
		$data['activeMenu']    = 'orders';
		$data['activeSubMenu'] = 'allorders';
		
		// -----------------------------------------------------------------------------//
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
		$searchField = $this->input->post('searchField');
		$searchValue = $this->input->post('searchValue');
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

		// -----------------------------------------------------------------------------//
		if(!empty($searchField) && !empty($searchValue)):
			if($searchField == 'order_code'):
				$whereCondition['where']		 	   = 	array($searchField =>  base64_encode($searchValue));	
			elseif($searchField == 'ticket'):
				$whereCondition['where']		 	   =  array($searchField => "[[".$searchValue."]]" );	
			else:
				$whereCondition['where'][$searchField] =  is_numeric($searchValue)?(int)$searchValue:$searchValue;
			endif;
		else:
			$whereCondition['where']['order_status']   = array('$ne' => 'Initialize');
		endif;
			$whereCondition['where']['raffle_mode']    = array('$ne' => 'Y');
		if($productIds <> "" && count($productIds) > 0):
			$whereCondition['where']['product_id']['$in']  =  $productIds;
		endif;
		// $page = $this->input->post('pageno');
		$page = $this->input->post('pageno');
		// $page = 1;
 		$itemsPerPage = 5000;
 		$startIndex  = ($page - 1)*$itemsPerPage;
 		$resultType  = '';
 		$tblName     = 'uw_lotto_orders';
		$OrderData 	 = $this->common_model->getOrderDetails($resultType,$whereCondition,$startIndex,$itemsPerPage,$tblName,true);

		$CSVData = array();
		foreach($OrderData as $index => $itemsArray):

			if($itemsArray['status'] == "CL"):
				$order_status = 'Cancelled';
			else:
				$order_status = $itemsArray['order_status'];
			endif;

			
			if($itemsArray['users_type'] == 'Users'):
				$itemsArray['bindwith_first_name'] = 'Admin';
				$seller_Store = $itemsArray['users_name'];
			else:
				$seller_Store = $itemsArray['store_name'];
			endif;


		    $CSVData[$index]['POS No.']            = !empty($itemsArray['pos_number']) ? $itemsArray['pos_number'] : 'N/A';
			$CSVData[$index]['Order ID']           = !empty($itemsArray['order_id']) ? $itemsArray['order_id'] : 'N/A';
			$CSVData[$index]['Product Name']       = !empty($itemsArray['product_name']) ? $itemsArray['product_name'] : 'N/A';
			$CSVData[$index]['Quantity']           = !empty($itemsArray['product_qty']) ? $itemsArray['product_qty'] : 'N/A';
			$CSVData[$index]['Store Name']         = !empty($seller_Store) ? $seller_Store : 'N/A';
			if($itemsArray['users_type'] == 'Users'):
			 $CSVData[$index]['Seller Name']        = !empty($itemsArray['users_address']) ? $itemsArray['users_address'] : 'N/A';
			else:
			 // $CSVData1['Seller Name']        = !empty($seller_Name) ? $seller_Name : 'N/A';
			 $CSVData[$index]['Seller Name']        = !empty($itemsArray['users_area']) ? $itemsArray['users_area'] : 'N/A';
			endif;
			$CSVData[$index]['Seller Mobile']      = !empty($itemsArray['user_phone']) ? $itemsArray['user_phone'] : 'N/A';
			$CSVData[$index]['Bind With']          = !empty($itemsArray['bindwith_first_name']) ? $itemsArray['bindwith_first_name'] : 'N/A';
			$CSVData[$index]['Purchase Date']      = !empty($itemsArray['created_at']) ? $itemsArray['created_at'] : 'N/A';
			// $CSVData[$index]['Straight Amount']    = !empty($itemsArray['straight_add_on_amount']) ? $itemsArray['straight_add_on_amount'] : 'N/A';
			// $CSVData[$index]['Rumble Amount']      = !empty($itemsArray['rumble_add_on_amount']) ? $itemsArray['rumble_add_on_amount'] : 'N/A';
			// $CSVData[$index]['Chance Amount']      = !empty($itemsArray['reverse_add_on_amount']) ? $itemsArray['reverse_add_on_amount'] : 'N/A';
			$CSVData[$index]['Total Amount']       = !empty($itemsArray['total_price']) ? $itemsArray['total_price'] : 'N/A';
			$CSVData[$index]['Payment Mode']       = !empty($itemsArray['payment_mode']) ? $itemsArray['payment_mode'] : 'N/A';
			$CSVData[$index]['Payment Status']     = !empty($order_status) ? $order_status : 'N/A';
			
		endforeach;

		echo json_encode($CSVData);
		die();
	}

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
		$data['orderData'] 				=	$this->common_model->getData('single', 'uw_lotto_orders', $wcon);
		
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
	** Function name 	: sendsms
	** Developed By 	: Dilip halder
	** Purpose  		: This function used for send sms to user.
	** Date 			: 07 February 2026
	************************************************************************/
	public function sendsms($orderId='')
	{	
		$this->admin_model->authCheck('view_data');
		$data['error'] 		   = '';
		$data['activeMenu']    = 'orders';
		$data['activeSubMenu'] = 'allorders';

		if($orderId):
			$this->admin_model->authCheck('edit_data');
			$whereCondition['where'] = array('_id' => new MongoDB\BSON\ObjectId($orderId));
			$data['orderData'] = $this->common_model->getData('single', 'uw_lotto_orders', $whereCondition);
		else:
			if (empty($data['orderData'])) {
				$this->session->set_flashdata('alert_error', 'Order not found.');
				redirect(correctLink('ALLORDERSDATA', getCurrentControllerPath('index')));
			}
		endif;
		
		if($this->input->post('SaveChanges')):
			$error = 'NO';
			$this->form_validation->set_rules('gateway', 'Gateway', 'trim|required');
			$gateway  = $this->input->post('gateway');
			if($this->form_validation->run() && $error == 'NO'): 
				
				$userOID     = (string)$data['orderData']['user_oid'];
				$whereCondition['where'] = array('_id' => new MongoDB\BSON\ObjectId($userOID));
				$userDetails = $this->common_model->getData('single', 'uw_users', $whereCondition);
				
				$buyerMobile = $data['orderData']['buyer_mobile']?? "";
				$buyerEmail  = $data['orderData']['buyer_email']?? "";
				$buyerCountryCode = $data['orderData']['buyer_country_code']?? "";
				
				$senderDetails['gateway'] = $gateway;
				if(!empty($buyerMobile) && !empty($buyerCountryCode) || !empty($buyerEmail)):
					$senderDetails['user_type']    = 'Buyer';
					$senderDetails['country_code'] = $buyerCountryCode;
					$senderDetails['country_code'] = $buyerCountryCode;
					$senderDetails['users_mobile'] = $buyerMobile;
					$senderDetails['users_email']  = $buyerEmail;
				else:
					$senderDetails['user_type']    = $userDetails['users_type'];
					$senderDetails['country_code'] = $userDetails['country_code'];
					$senderDetails['users_mobile'] = $userDetails['users_mobile'];
					$senderDetails['users_email']  = $userDetails['users_email'];
				endif;

				$drawId = $data['orderData']['draw_id'];
				if($drawId):
					$whereCondition['where'] = array('draw_id' => (int)$drawId);
					$drawDetails  = $this->common_model->getParticularFieldByMultipleCondition(array('draw_date', 'draw_time'), 'uw_products_draw_records', $whereCondition);
					if (is_array($drawDetails)) {
						$drawDate = isset($drawDetails['draw_date']) ? $drawDetails['draw_date'] : '';
						$drawTime = isset($drawDetails['draw_time']) ? $drawDetails['draw_time'] : '';
					}
				endif;
				
				$ticket          = $data['orderData']['ticket'];
				$selectionValues = $data['orderData']['selection_values'];
				$sbTickect       = $data['orderData']['sb_tickect'];
				$drawDateNTime   = $drawDate.' '.$drawTime;
				
				$message = "";
				if(!empty($ticket) && !empty($selectionValues)):
					$ticketLIST      = json_decode($ticket, true);
					$selectionValues = json_decode($selectionValues, true);
					$sbTickectList   = json_decode($sbTickect, true);
					$ORDERID         = $data['orderData']['order_id'];
					$CAMPAIGNAME     = $data['orderData']['product_title'];
					$LINK            = 'https://tktinvoice.com/uwin-download-invoice/'.$ORDERID;

					$output        = [];
					$CouponDetails = '';
					$map = ['S', 'R', 'C'];
					foreach ($ticketLIST as $key => $tickets) {

						// Ticket numbers
						$line = implode(',', $tickets);
						// Append super ball for this ticket if present
						if (!empty($sbTickectList) && isset($sbTickectList[$key])) {
							$line .= ' + ' . $sbTickectList[$key];
						}
						$line .= ' (';

						// Selection letters
						$selected = [];
						foreach ($selectionValues[$key] as $sKey => $sVal) {
							if ($sVal > 0) {
								$selected[] = $map[$sKey];
							}
						}

						$line .= implode(',', $selected) . ')';
						$output[] = $line;
					}

					$CouponDetails = implode('. ', $output);
					$drawDate = date('d.m.Y h:iA', strtotime($drawDateNTime));
					$message  = 'Order ID '.$ORDERID.' of '.$CAMPAIGNAME.' with coupons '.$CouponDetails.' Ddate '.$drawDate.' You can download the invoice here '.$LINK;
					$senderDetails['message'] = $message;
				endif;

				if( !empty($message) && !empty($senderDetails['country_code']) && !empty($senderDetails['users_mobile']) && 
				    (
						$senderDetails['gateway'] == 'smscountry'   || 
						$senderDetails['gateway'] == 'digitizebird' ||
						$senderDetails['gateway'] == 'ndm'   
					)
				):
					$result = $this->sms_model->sendSMS($senderDetails);
				elseif( !empty($message) && !empty($senderDetails['country_code']) && !empty($senderDetails['users_mobile']) && $senderDetails['gateway'] == 'whatsapp' ):
					$senderDetails['ORDERID']       = $ORDERID;
					$senderDetails['CAMPAIGNAME']   = $CAMPAIGNAME;
					$senderDetails['CouponDetails'] = $CouponDetails;
					$senderDetails['DDATE']         = $drawDate;
					$senderDetails['LINK']          = $LINK;
					$result = $this->sms_model->sendWhatsAppMessage($senderDetails);
				elseif(!empty($message) && !empty($buyerEmail) && $senderDetails['gateway'] == 'email'):
					$subject= "Order Confirmation";
					$result = $this->email_model->sendEmail($buyerEmail,$subject,$message);
				else:
					$result = false;
				endif;
				$result = json_decode($result, true);

				

				

				if($result['status'] == "Success"):
					$this->session->set_flashdata('alert_success', 'Order SMS sent successfully.');
					redirect(correctLink('ALLORDERSDATA', getCurrentControllerPath('index')));
				else:
					$error = 'Failed to send order SMS. '.$result['error'];
					$this->session->set_flashdata('alert_error',$error);
					redirect(correctLink('ALLORDERSDATA', getCurrentControllerPath('index')));
				endif;
			else:
				$this->session->set_flashdata('alert_error', validation_errors());
				redirect(correctLink('ALLORDERSDATA', getCurrentControllerPath('index')));
			endif;
		endif;

		$drawId = isset($data['orderData']['draw_id']) ? $data['orderData']['draw_id'] : 0;
		$data['drawDetails'] = array('draw_date' => '', 'draw_time' => '');
		if ($drawId) {
			$serchfields = array('draw_date', 'draw_time', 'products_id');
			$wcon['where'] = array('draw_id' => (int)$drawId);
			$data['drawDetails'] = $this->common_model->getParticularFieldByMultipleCondition($serchfields, 'uw_products_draw_records', $wcon);
			if (!is_array($data['drawDetails'])) {
				$data['drawDetails'] = array('draw_date' => '', 'draw_time' => '');
			}
		}

		$data['gateways'] = array();
		$enableSMS = $this->common_model->getData('single', 'uw_enablesms');
		 
		$data['gateways']['smscountry']   = 'SMS Country';
		$data['gateways']['digitizebird'] = 'Digitizebird';
		$data['gateways']['ndm'] 		  = 'NDM';
		$data['gateways']['whatsapp']     = 'WhatsApp';
		$data['gateways']['email']        = 'Email';

		unset($data['gateways'][$enableSMS['default_sms']]);
		$this->layouts->set_title('Send Order SMS | UWINN');
		$this->layouts->admin_view('orders/allorders/sendsms', array(), $data);
	}
	/***********************************************************************
	** Function name 	: getShopExportFilterData
	** Purpose  		: Build filters for shop excel export
	** Date 			: 15 May 2026
	************************************************************************/
	private function getShopExportFilterData($useJsonProductIds = false)
	{
		// Shop excel default range = current date full day.
		$fromDate = date('Y-m-d 00:00:00');
		$toDate = date('Y-m-d 23:59:59');
		$whereCondition = array('where' => array());

		if($this->input->post('fromDate')):
			$fromDate = date('Y-m-d H:i:s', strtotime($this->input->post('fromDate')));
		endif;
		if($this->input->post('toDate')):
			$toDate = date('Y-m-d H:i:s', strtotime($this->input->post('toDate')));
		endif;

		if($useJsonProductIds):
			$rawProductIds = $this->input->post('productIds');
			if(is_array($rawProductIds)):
				$productIds = $rawProductIds;
			elseif(is_string($rawProductIds) && $rawProductIds !== ''):
				$decodedProductIds = json_decode($rawProductIds, true);
				$productIds = is_array($decodedProductIds) ? $decodedProductIds : array();
			else:
				$productIds = array();
			endif;
		else:
			$productIds = isset($_POST['productIds']) ? (array)$_POST['productIds'] : array();
		endif;
		$productIds = is_array($productIds) ? $productIds : array();
		$productIds = array_values(array_filter($productIds, function($id) {
			return $id !== '' && $id !== null;
		}));
		$productIds = array_map(function($id) {
			return ($id <= PHP_INT_MAX) ? (int)$id : $id;
		}, $productIds);

		$searchField = $this->input->post('searchField');
		$searchValue = $this->input->post('searchValue');
		$cancelled_order = $this->input->post('cancelled_order');

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

		$whereCondition['where']['raffle_mode'] = array('$ne' => 'Y');

		if(!empty($searchField) && !empty($searchValue)):
			if($searchField == 'order_code'):
				$whereCondition['where'] = array($searchField => base64_encode($searchValue));
			elseif($searchField == 'ticket'):
				$whereCondition['where'] = array($searchField => '[['.$searchValue.']]');
			else:
				$whereCondition['where'][$searchField] = is_numeric($searchValue) ? (int)$searchValue : $searchValue;
			endif;
		else:
			$whereCondition['where']['order_status'] = array('$ne' => 'Initialize');
		endif;

		if($cancelled_order != 'on'):
			$whereCondition['where']['status'] = array('$ne' => 'CL');
			$whereCondition['where']['order_status'] = 'Success';
		endif;

		if(!empty($productIds)):
			$whereCondition['where']['product_id']['$in'] = $productIds;
		endif;

		$products = $this->common_model->getData('multiple', 'uw_products', array('where' => array('status' => 'A')), array('title' => 1));
		if(!empty($productIds)):
			$products = array_values(array_filter($products, function($product) use ($productIds) {
				return in_array((int)$product['products_id'], $productIds, true);
			}));
		endif;

		$productTitles = array();
		$productTitleMap = array();
		foreach($products as $product):
			$title = trim((string)$product['title']);
			if($title === ''):
				continue;
			endif;
			$productTitles[] = $title;
			$productTitleMap[strtoupper($title)] = $title;
		endforeach;

		return compact('fromDate', 'toDate', 'whereCondition', 'productIds', 'searchField', 'searchValue', 'cancelled_order', 'productTitles', 'productTitleMap');
	}

	/***********************************************************************
	** Function name 	: resolveShopRetailerInfo
	** Purpose  		: Resolve shop name, region, supervisor when user lookup is empty
	** Date 			: 15 May 2026
	************************************************************************/
	private function resolveShopRetailerInfo($itemsArray)
	{
		$storeName = trim((string)($itemsArray['store_name'] ?? ''));
		$region = trim((string)($itemsArray['users_area'] ?? ''));
		$supervisor = trim((string)($itemsArray['bindwith_first_name'] ?? ''));
		$pos = isset($itemsArray['pos_number']) && $itemsArray['pos_number'] !== '' && $itemsArray['pos_number'] !== null
			? $itemsArray['pos_number'] : '';

		if(!empty($itemsArray['seller_details'])):
			$seller = $itemsArray['seller_details'];
			if(is_string($seller)):
				$seller = json_decode($seller, true);
			endif;
			if(is_object($seller)):
				$seller = json_decode(json_encode($seller), true);
			endif;
			if(is_array($seller)):
				if($storeName === '' && !empty($seller['StoreName'])):
					$storeName = trim((string)$seller['StoreName']);
				endif;
				if($storeName === '' && !empty($seller['Name'])):
					$storeName = trim((string)$seller['Name']);
				endif;
				if($region === '' && !empty($seller['Country'])):
					$countryParts = preg_split('/\s+/', trim((string)$seller['Country']));
					if(!empty($countryParts[0])):
						$region = $countryParts[0];
					endif;
				endif;
				if($supervisor === '' && !empty($seller['FoName'])):
					$supervisor = trim((string)$seller['FoName']);
				endif;
			endif;
		endif;

		if(($storeName === '' || $region === '' || $supervisor === '') && $pos !== '' && $pos !== 'N/A'):
			$posKey = (string)$pos;
			if(!array_key_exists($posKey, $this->shopExportPosCache)):
				$posQuery = is_numeric($pos) ? (int)$pos : $pos;
				$retailer = $this->common_model->getDataByParticularField('uw_users', 'pos_number', $posQuery);
				if(empty($retailer) && is_numeric($pos)):
					$retailer = $this->common_model->getDataByParticularField('uw_users', 'pos_number', (string)$pos);
				endif;
				$this->shopExportPosCache[$posKey] = $retailer ? $retailer : array();
			endif;
			$retailer = $this->shopExportPosCache[$posKey];
			if(!empty($retailer)):
				if($storeName === '' && !empty($retailer['store_name'])):
					$storeName = trim((string)$retailer['store_name']);
				endif;
				if($region === '' && !empty($retailer['area'])):
					$region = trim((string)$retailer['area']);
				endif;
				if($supervisor === '' && !empty($retailer['bind_person_name'])):
					$supervisor = trim((string)$retailer['bind_person_name']);
				endif;
			endif;
		endif;

		return array(
			'pos' => ($pos !== '' && $pos !== 'N/A') ? $pos : 'N/A',
			'shop_name' => $storeName !== '' ? $storeName : 'N/A',
			'region' => $region,
			'supervisor' => $supervisor
		);
	}

	/***********************************************************************
	** Function name 	: aggregateShopExportRows
	** Purpose  		: Aggregate order rows into shop-wise export rows
	** Date 			: 15 May 2026
	************************************************************************/
	private function aggregateShopExportRows($orderData, $productTitles, $productTitleMap)
	{
		$shopData = array();

		if(empty($orderData)):
			return $shopData;
		endif;

		foreach($orderData as $itemsArray):
			if(($itemsArray['users_type'] ?? '') === 'Users'):
				continue;
			endif;

			$shopInfo = $this->resolveShopRetailerInfo($itemsArray);
			$pos = $shopInfo['pos'];
			$shopName = $shopInfo['shop_name'];
			$region = $shopInfo['region'];
			$supervisor = $shopInfo['supervisor'];
			$shopKey = $pos.'|'.$shopName.'|'.$region.'|'.$supervisor;

			if(!isset($shopData[$shopKey])):
				$shopData[$shopKey] = array(
					'shopKey' => $shopKey,
					'POS' => $pos,
					'SHOP NAME' => $shopName,
					'Region' => $region,
					'Supervisor' => $supervisor
				);
				foreach($productTitles as $title):
					$shopData[$shopKey][$title] = 0;
				endforeach;
			endif;

			$productName = trim((string)($itemsArray['product_name'] ?? ''));
			$qty = (int)($itemsArray['product_qty'] ?? 0);
			if($productName === '' || $qty <= 0):
				continue;
			endif;

			$matchedTitle = '';
			$upperName = strtoupper($productName);
			if(isset($productTitleMap[$upperName])):
				$matchedTitle = $productTitleMap[$upperName];
			else:
				foreach($productTitles as $title):
					if(strcasecmp($title, $productName) === 0):
						$matchedTitle = $title;
						break;
					endif;
				endforeach;
			endif;

			if($matchedTitle !== ''):
				$shopData[$shopKey][$matchedTitle] += $qty;
			endif;
		endforeach;

		return array_values($shopData);
	}

	/***********************************************************************
	** Function name 	: exportshopexcel
	** Purpose  		: Shop-wise export progress page (same flow as exportexcel)
	** Date 			: 14 May 2026
	************************************************************************/
	public function exportshopexcel()
	{
		$this->admin_model->authCheck('view_data');
		$this->common_model->generateLogs();

		$filterData = $this->getShopExportFilterData(false);
		extract($filterData);

		$itemsPerPage = 5000;
		$tblName = 'uw_lotto_orders';
		$totalRows = $this->common_model->getOrderDetails('count', $whereCondition, '', '', $tblName);
		$totalPages = $totalRows > 0 ? (int)ceil($totalRows / $itemsPerPage) : 1;

		$data['error'] = '';
		$data['activeMenu'] = 'orders';
		$data['activeSubMenu'] = 'allorders';
		$data['current_page'] = 1;
		$data['total_page'] = $totalPages;
		$data['searchField'] = $searchField;
		$data['searchValue'] = $searchValue;
		$data['fromDate'] = $fromDate;
		$data['toDate'] = $toDate;
		$data['cancelled_order'] = $cancelled_order;
		$data['productIds'] = json_encode($productIds);
		$data['productTitles'] = $productTitles;

		$this->layouts->set_title('Export Shop Excel | UWINN');
		$this->layouts->admin_view('orders/allorders/exportshopexcel', array(), $data);
	}

	/***********************************************************************
	** Function name 	: exportshopexcelApi
	** Purpose  		: Shop-wise export data API (paginated)
	** Date 			: 15 May 2026
	************************************************************************/
	public function exportshopexcelApi()
	{
		$this->admin_model->authCheck('view_data');

		header('Content-Type: application/json');
		try {
			$this->shopExportPosCache = array();
			$filterData = $this->getShopExportFilterData(true);
			extract($filterData);

			$page = (int)$this->input->post('pageno');
			if($page < 1):
				$page = 1;
			endif;
			$itemsPerPage = 5000;
			$startIndex = ($page - 1) * $itemsPerPage;
			$tblName = 'uw_lotto_orders';

			$orderData = $this->common_model->getOrderDetails('', $whereCondition, $startIndex, $itemsPerPage, $tblName, true);
			if(!is_array($orderData)):
				$orderData = array();
			endif;

			$shopRows = $this->aggregateShopExportRows($orderData, $productTitles, $productTitleMap);
			echo json_encode($shopRows);
		} catch (\Throwable $th) {
			header('HTTP/1.1 500 Internal Server Error');
			echo json_encode(array('error' => $th->getMessage()));
		}
		exit;
	}
}
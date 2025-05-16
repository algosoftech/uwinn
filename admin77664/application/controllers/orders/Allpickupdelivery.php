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


class Allpickupdelivery extends CI_Controller {

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
	 + + Developed By 	: DILIP HALDER
	 + + Purpose  		: This function used for index
	 + + Date 			: 29 November 2024
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index()
	{	
		$this->admin_model->authCheck('view_data');
		$data['error'] 		   = '';
		$data['activeMenu']    = 'orders';
		$data['activeSubMenu'] = 'allpickupdelivery';

		if($this->input->get('fromDate')):
			$fromDate	 = date('Y-m-d H:i', strtotime($this->input->get('fromDate')));
		else:
			$fromDate    = date('Y-m-d 21:31', strtotime('-1 day'));
		endif;
		if($this->input->get('toDate')):
			$toDate	 	 = date('Y-m-d H:i', strtotime($this->input->get('toDate')));
		else:
			$toDate	 	 = date('Y-m-d 21:30');
		endif;
		
		$searchField   = $this->input->get('searchField');
		$searchValue   = $this->input->get('searchValue');

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

		$data['searchField'] 			= $searchField;
		$data['searchValue'] 			= $searchValue;
		$data['fromDate'] 				= $fromDate;  
		$data['toDate'] 				= $toDate;

		// Where conditions section.
		if(!empty($searchField) && !empty($searchValue)):
			if($searchField == 'ticket'):
			 	$whereCondition['where']	 = 	array($searchField=> "[[".$searchValue."]]" );
			elseif($searchField == 'order_code'):
				$whereCondition['where']		 	= 	array($searchField=>  base64_encode($searchValue));	
			else:
			  	$whereCondition['where'][$searchField] =  is_numeric($searchValue)?(int)$searchValue:$searchValue;
			endif;

		else:
			$whereCondition['where']['order_status'] = array('$ne' => 'Initialize');
		endif;
			$whereCondition['where']['raffle_mode']  = array('$ne' => 'Y');
		$whereCondition['where']['$or'] =   array(
	        array('pickup_point' => array('$nin' => array('', null))),
	        array('delivery_address' => array('$nin' => array('', null)))
		);

		$baseUrl 							= 	getCurrentControllerPath('index');
		$this->session->set_userdata('ALLPICKUP&DELIVERY',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 	'uw_lotto_orders';

		// echo "<pre>";print_r($whereCondition);die();
		$totalRows 							= 	$this->common_model->getData('count',$tblName,$whereCondition,$shortField,'0','0');

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

		$startIndex   = $page;
		$itemsPerPage = $pageData;
		// $itemsPerPage = 1;
		$resultType  			= '';

		// echo "<pre>";print_r($whereCondition);die();

		$data['ALLDATA']  		= $this->common_model->getOrderDetails($resultType,$whereCondition,$startIndex,$itemsPerPage);
		// echo "<pre>";print_r($data);die();

		$this->layouts->set_title('Orders | UWINN');
		$this->layouts->admin_view('orders/allorders/pickup-delivery-index',array(),$data);
	}	// END OF FUNCTION

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name 	: viewdata
	 + + Developed By 	: DILIP HALDER
	 + + Purpose  		: This function used for view data.
	 + + Date 			: 29 November 2024
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function viewdata($oid='')
	{
		$data['error'] 			= 	'';
		$data['activeMenu'] 	= 	'orders';
		$data['activeSubMenu'] 	= 	'allpickupdelivery';
		$this->admin_model->authCheck('view_data');

		$tblName     	   = 'uw_lotto_orders';
		$wcon['where']     = array('_id' => new MongoDB\BSON\ObjectId($oid) );
		$data['viewData']  = $this->common_model->getParticularFieldByMultipleCondition($serchfields,$tblName ,$wcon);
		// echo "<pre>";print_r($data);die();

		$this->layouts->set_title('Orders | view | UWINN');
		$this->layouts->admin_view('orders/allorders/viewdata',array(),$data);
	}

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name 	: changestatus
	 + + Developed By 	: DILIP HALDER
	 + + Purpose  		: This function used for change status.
	 + + Date 			: 29 November 2024
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	function changestatus($changeStatusId='',$statusType='')
	{

		$this->admin_model->authCheck('edit_data');
		$tblName     	   = 'uw_lotto_orders';
		$param['status']   = $statusType;
		
		if($this->input->post('reason')):
		 $param['reason']  = $this->input->post('reason');
		endif;
		$viewData 		   = $this->common_model->editData('uw_lotto_orders',$param,'_id', new MongoDB\BSON\ObjectId($changeStatusId) );

		if($viewData == 1):
			// $serchfields 	= array('draw_date','draw_time');
	        $whereCondition['where']  = array('_id' => new MongoDB\BSON\ObjectId($changeStatusId) );
	        // $ViewData  		= $this->common_model->getParticularFieldByMultipleCondition($serchfields,$tblName ,$whereCondition);


	        $resultData   = $this->common_model->getOrderDetails($resultType,$whereCondition,$startIndex,$itemsPerPage);


	        $ViewData        = $resultData[0];	
	        $delivery_charge = $ViewData['delivery_charge'];
	        // echo "<pre>";
	        // print_r($delivery_charge);
	        // die();

	        $user_oid  = (string)$ViewData['users_oid'];
	        $order_oid = $ViewData['_id']->{'$id'};
	       
	        if($statusType == 'C'):
		    	$deliveryStatus = 'Completed';
		    elseif($statusType == 'Dlvi'):
		    	$deliveryStatus = 'Delivered';
		    elseif($statusType == 'Disp'):
		    	$deliveryStatus = 'Dispatched';
		    elseif($statusType == 'P'):
		    	$deliveryStatus = 'Pending';
		    elseif($statusType == 'RJ'):
		    	$deliveryStatus = 'Rejected';
		    endif;

	        // Order capturing in order uw_loadbalance table..
		    // $fromuserparam["load_balance_id"]		 =	(int)$this->common_model->getNextSequence('uw_loadBalance');
			// $fromuserparam["order_oid"] 			 =	new MongoDB\BSON\ObjectId($order_oid);
			// $fromuserparam["user_oid"] 				 =	new MongoDB\BSON\ObjectId($user_oid);
			// $fromuserparam["user_id_deb"]			 =	(int)$ViewData['user_id'];
			// $fromuserparam["order_id"] 				 =	$ViewData['order_id'];
			// $fromuserparam["user_id_cred"] 			 =	(int)0;
			// $fromuserparam["upoints"] 				 =	(float)$ViewData['total_price'];
			// $fromuserparam["availableArabianPoints"] =	(float)$ViewData['users_availableArabianPoints'];
			// $fromuserparam["end_balance"] 			 =	(float)$ViewData['users_availableArabianPoints'];
		    // $fromuserparam["record_type"] 			 =	$deliveryStatus;
		    // $fromuserparam["narration"]				 =	'Order';
     		// $fromuserparam["remarks"]				 =	'Ticket ID : '.$ViewData['order_id'].'. '.
		    //  											'Delivery charge : '.$delivery_charge.'. '.
		    //  											'Delivery reason : '.$this->input->post('reason');
		    // $fromuserparam["creation_ip"] 	 		 =  currentIp();
		    // $fromuserparam["created_at"] 			 =	date('Y-m-d H:i');
		    // $fromuserparam["created_by"] 			 =	(int)$this->session->userdata('UW_ADMIN_ID');
		    // $fromuserparam["created_by_mobile"] 	 =	(int)$this->session->userdata('KW_ADMIN_MOBILE');
		    // $fromuserparam["status"] 				 =	"A";
	    	// $this->common_model->addData('uw_loadBalance', $fromuserparam);

		endif;

		redirect(correctLink('ALLPICKUP&DELIVERY',getCurrentControllerPath('index')));
		die();
		// $this->layouts->set_title('Orders | view | UWINN');
		// $this->layouts->admin_view('orders/allorders/viewdata',array(),$data);

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
		$data['error'] 		   = '';
		$data['activeMenu']    = 'orders';
		$data['activeSubMenu'] = 'allpickupdelivery';

		//Generating Logs
		$this->common_model->generateLogs();

		if($this->input->post('fromDate')):
			$fromDate	 = date('Y-m-d H:i', strtotime($this->input->post('fromDate')));
		else:
			$fromDate    = date('Y-m-d 21:31', strtotime('-1 day'));
		endif;
		if($this->input->post('toDate')):
			$toDate	 	 = date('Y-m-d H:i', strtotime($this->input->post('toDate')));
		else:
			$toDate	 	 = date('Y-m-d 21:30');
		endif;
		
		$searchField   = $this->input->post('searchField');
		$searchValue   = $this->input->post('searchValue');

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

		$data['searchField'] 			= $searchField;
		$data['searchValue'] 			= $searchValue;
		$data['fromDate'] 				= $fromDate;  
		$data['toDate'] 				= $toDate;

		// Where conditions section.
		if(!empty($searchField) && !empty($searchValue)):
			if($searchField == 'ticket'):
			 	$whereCondition['where']	 = 	array($searchField=> "[[".$searchValue."]]" );
			elseif($searchField == 'order_code'):
				$whereCondition['where']		 	= 	array($searchField=>  base64_encode($searchValue));	
			else:
			  	$whereCondition['where'][$searchField] =  is_numeric($searchValue)?(int)$searchValue:$searchValue;
			endif;

		else:
			$whereCondition['where']['order_status'] = array('$ne' => 'Initialize');
		endif;
			$whereCondition['where']['raffle_mode']  = array('$ne' => 'Y');
		$whereCondition['where']['$or'] =   array(
	        array('pickup_point' => array('$nin' => array('', null))),
	        array('delivery_address' => array('$nin' => array('', null)))
		);

		// -----------------------------------------------------------------------------//
		$resultType   = "count";
		$tblName 	  = "uw_lotto_orders";
		$totalRows 	  = $this->common_model->getOrderDetails($resultType,$whereCondition,'','',$tblName);
		// echo "<pre>";print_r($totalRows);die();

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
		
		$totalpage 				= count($totalpage);
		$data['current_page']   = $current_page;
		$data['total_page'] 	= $totalpage;
		
		$data['searchField'] 	=   $searchField;
		$data['searchValue'] 	=   $searchValue;
		$data['fromDate'] 		=   $fromDate;
		$data['toDate'] 		=   $toDate;


		// echo "<pre>";print_r($data);die();
		$this->layouts->set_title('Export CSV | UWINN');
		$this->layouts->admin_view('orders/allorders/allpickupdelivery-exportexcel',array(),$data);
	}	// END OF FUNCTION

 	/***********************************************************************
	** Function name 	: exportexcelApi
	** Developed By 	: Dilip halder
	** Purpose  		: This function used for export order data
	** Date 			: 23 July 2024
	************************************************************************/
	function exportexcelApi(){
		
		$this->admin_model->authCheck('view_data');
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'orders';
		$data['activeSubMenu'] 				= 	'allpickupdelivery';
		
		
		if($this->input->post('fromDate')):
			$fromDate	 = date('Y-m-d H:i', strtotime($this->input->post('fromDate')));
		else:
			$fromDate    = date('Y-m-d 21:31', strtotime('-1 day'));
		endif;
		if($this->input->post('toDate')):
			$toDate	 	 = date('Y-m-d H:i', strtotime($this->input->post('toDate')));
		else:
			$toDate	 	 = date('Y-m-d 21:30');
		endif;
		
		$searchField   = $this->input->post('searchField');
		$searchValue   = $this->input->post('searchValue');

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

		$data['searchField'] 			= $searchField;
		$data['searchValue'] 			= $searchValue;
		$data['fromDate'] 				= $fromDate;  
		$data['toDate'] 				= $toDate;

		// Where conditions section.
		if(!empty($searchField) && !empty($searchValue)):
			if($searchField == 'ticket'):
			 	$whereCondition['where']	 = 	array($searchField=> "[[".$searchValue."]]" );
			elseif($searchField == 'order_code'):
				$whereCondition['where']		 	= 	array($searchField=>  base64_encode($searchValue));	
			else:
			  	$whereCondition['where'][$searchField] =  is_numeric($searchValue)?(int)$searchValue:$searchValue;
			endif;

		else:
			$whereCondition['where']['order_status'] = array('$ne' => 'Initialize');
		endif;
			$whereCondition['where']['raffle_mode']  = array('$ne' => 'Y');
		$whereCondition['where']['$or'] =   array(
	        array('pickup_point' => array('$nin' => array('', null))),
	        array('delivery_address' => array('$nin' => array('', null)))
		);

		// $page = $this->input->post('pageno');
		$page = $this->input->post('pageno');
		// $page = 1;
 		$itemsPerPage = 5000;
 		$startIndex  = ($page - 1)*$itemsPerPage;
 		$resultType  = '';
 		$tblName     = 'uw_lotto_orders';
		$OrderData 	 = $this->common_model->getOrderDetails($resultType,$whereCondition,$startIndex,$itemsPerPage,$tblName);

		$CSVData = array();
		$sno = 1;
		foreach($OrderData as $index => $itemsArray):
			if($itemsArray['status'] == "CL"):
				$order_status = 'Cancelled';
			else:
				$order_status = $itemsArray['order_status'];
			endif;
			$CSVData[$index]['Seial No.']          = $sno++;
			$CSVData[$index]['Order ID']           = !empty($itemsArray['order_id']) ? $itemsArray['order_id'] : 'N/A';
			$CSVData[$index]['Product Name']       = !empty($itemsArray['product_name']) ? $itemsArray['product_name'] : 'N/A';
			$CSVData[$index]['Quantity']           = !empty($itemsArray['product_qty']) ? $itemsArray['product_qty'] : 'N/A';
			$CSVData[$index]['Name']       		   = !empty($itemsArray['users_name']) ? $itemsArray['users_name'] : 'N/A';
			$CSVData[$index]['Mobile']             = !empty($itemsArray['user_phone']) ? $itemsArray['user_phone'] : 'N/A';
			$CSVData[$index]['Bind With']          = !empty($itemsArray['bindwith_first_name']) ? $itemsArray['bindwith_first_name'] : 'N/A';
			$CSVData[$index]['Purchase Date']      = !empty($itemsArray['created_at']) ? $itemsArray['created_at'] : 'N/A';
			$CSVData[$index]['Total Amount']       = !empty($itemsArray['total_price']) ? $itemsArray['total_price'] : 'N/A';
			$CSVData[$index]['Delivery Status']    = !empty($itemsArray['status']) ? $itemsArray['status'] : 'N/A';
			$CSVData[$index]['Delivery Reason']    = !empty($itemsArray['reason']) ? $itemsArray['reason'] : 'N/A';
			$CSVData[$index]['Payment Status']     = !empty($order_status) ? $order_status : 'N/A';
		endforeach;

		echo json_encode($CSVData);
		die();
	}

}
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
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'uwin';
		$data['activeSubMenu'] 				= 	'alllottooders';

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
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'orders';
		$data['activeSubMenu'] 				= 	'alllottooders';
		
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


			$this->common_model->editData('uw_orders',$param1,'order_id',$changeStatusId);
			$this->common_model->editData('uw_orders_details',$param1,'order_id',$changeStatusId);		

			// Checking Sender User.
			$userid = $cancleOrderData['user_id'];
			
			$tblName 				=   'uw_users';
			$whereCon['where']		=	array('users_id' => $userid , 'status'=> 'A' );
			$shortField 			=   array('users_id' => -1);
			$UserData 			= 	$this->common_model->getData('single',$tblName,$whereCon,$shortField,'0','0');

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
			if($UserData['availableArabianPoints']):
				$param['availableArabianPoints'] = $UserData['availableArabianPoints'] + $cancleOrderData['total_price'];
				$this->common_model->editData('uw_users',$param,'users_id',(int)$userid);	
			endif;

			// User detail  used for email and sms  
			// $this->emailsendgrid_model->sendOrderMailToUser($refundparam['order_id']);
			// $this->sms_model->sendTicketDetails($refundparam['order_id']);

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
	function exportexcel()
	{	
		$this->admin_model->authCheck('view_data');
		//Generating Logs
		$this->common_model->generateLogs();

		// ---------------------------------Date query start---------------------------------//
		if($this->input->post('fromDate')):
			$fromDate	 = date('Y-m-d H:i', strtotime($this->input->post('fromDate')));
		endif;
		if($this->input->post('toDate')):
			$toDate	 	 = date('Y-m-d H:i', strtotime($this->input->post('toDate')));
		endif;
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
			
		// -----------------------------------------------------------------------------//
		$resultType   = "count";
		$totalRows 	  = $this->common_model->getOrderDetails($resultType,$whereCondition);
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
		// echo "<pre>";print_r($data);die();
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
		// $page = $this->input->post('pageno');
		$page = $this->input->post('pageno');
		// $page = 1;
 		$itemsPerPage = 5000;
 		$startIndex   = ($page - 1)*$itemsPerPage;
 		$resultType   = '';
		$OrderData 	  = $this->common_model->getOrderDetails($resultType,$whereCondition,$startIndex,$itemsPerPage);

		$CSVData 	  = array();
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
			$Ticket = str_replace('[[', '', $itemsArray['ticket']);
            $Ticket = str_replace(']]', '/', $Ticket);
            $Ticket = str_replace('],[', '/', $Ticket);
            $ticket = array_filter(explode('/', $Ticket));
			if($ticket):
				foreach ($ticket as $subindex => $item):
				  	$coupon = $item;
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

				    $CSVData1['POS No.']            = !empty($seller_POS)  ? $seller_POS : 'N/A';
					$CSVData1['Order ID']           = !empty($itemsArray['order_id']) ? $itemsArray['order_id'] : 'N/A';
					$CSVData1['Product Name']       = !empty($itemsArray['product_name']) ? $itemsArray['product_name'] : 'N/A';
					$CSVData1['Store Name']         = !empty($seller_Store) ? $seller_Store : 'N/A';
					// if($itemsArray['users_type'] == 'Users'):
					//  $CSVData1['Seller Name']        = !empty($itemsArray['users_address']) ? $itemsArray['users_address'] : 'N/A';
					// else:
					//  $CSVData1['Seller Name']        = !empty($seller_Name) ? $seller_Name : 'N/A';
					// endif;
					 $CSVData1['Seller Name']        = !empty($seller_Name) ? $seller_Name : 'N/A';
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
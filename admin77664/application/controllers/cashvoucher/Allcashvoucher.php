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

class Allcashvoucher extends CI_Controller {

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
	 + + Purpose  		: This function used for index
	 + + Date 			: 23 November 2024
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index()
	{	
		$this->admin_model->authCheck();
		$data['error'] 					= 	'';
		$data['activeMenu'] 			= 	'cashvoucher';
		$data['activeSubMenu'] 			= 	'allcashvoucher';

		if($this->input->get('fromDate')):
			$data['fromDate'] 				=   date('Y-m-d H:i', strtotime($this->input->get('fromDate')));  //2023-03-16 15:13
			$whereCon['where_gte'] 			= 	array(array("created_date",strtotime($this->input->get('fromDate'))));
		endif;

		if($this->input->get('toDate')):
			$data['toDate'] 				=   date('Y-m-d H:i', strtotime($this->input->get('toDate')));  //2023-03-16 15:13
			$whereCon['where_lte'] 			= 	array(array("created_date",  strtotime($this->input->get('toDate'))));
		endif;

		if($this->input->get('searchField') && $this->input->get('searchValue')):
			$sField						=	$this->input->get('searchField');
			$sValue						=	$this->input->get('searchValue');
			$data['searchField'] 		= 	$sField;
			$data['searchValue'] 		= 	$sValue;

			//Coupon generating details start..
			if($sField  == 'generated_by'):
				$whereCon2['where_or'] 	=  array('users_mobile'  => (int)$sValue  ,'users_email'=> $sValue ); 	 
				$result 				= $this->common_model->getData('single','uw_users',$whereCon2);
				if($result):
					$sField = 'created_by';
					$sValue = $result['users_id'];
				endif;
			endif;
			//Coupon generating details end..

			//Redeemed By details start..
			if($sField  == 'redeemed_by'):
				$whereCon2['where_or'] 	=  
								array(
									'users_mobile'  => (int)$sValue,
									// 'users_email'	=> $sValue,
									// 'pos_device_id'	=> $sValue,
									'pos_number'	=> (int)$sValue 
								); 	
				$result = $this->common_model->getData('single','uw_users',$whereCon2);
				if($result):
					$sField = 'seller_id';
					$sValue = (int)$result['users_id'];
				endif;
			endif;
			//Redeemed By details end..

			if(is_numeric($sValue)):
				$whereCon['where'] 	=  array($sField  => (int)$sValue); 	 
			else:
				$whereCon['like'] 	=  array('0'=>$sField , '1' => $sValue); 	 
			endif;
		endif;

		$shortField 						= 	array('created_date'=>-1);
		$baseUrl 							= 	getCurrentControllerPath('index');
		$this->session->set_userdata('ALLCOUPONSDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 	'uw_cash_vouchers';
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
           $page 							= $this->uri->segment(getUrlSegment());
       else:
           $page 							= 0;
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

		$this->layouts->set_title('Cash Voucher | UWINN');
		$this->layouts->admin_view('cashvoucher/allcashvoucher/index',array(),$data);
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name 	: changestatus
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for change status
	** Date 			: 23 November 2024 
	************************************************************************/
	function changestatus($changeStatusId='',$statusType='')
	{  

		$this->admin_model->authCheck();
		$data['error'] 					= 	'';
		$data['activeMenu'] 			= 	'cashvoucher';
		$data['activeSubMenu'] 			= 	'allcashvoucher';

		if($statusType == 'I'):
		  $whereCondition   =  array('_id' => new MongoDB\BSON\ObjectId($changeStatusId));
		  $param1['status'] =	"I";
		elseif($statusType == 'A'):
		  $whereCondition   =  array('_id' => new MongoDB\BSON\ObjectId($changeStatusId));
		  $param1['status']	=	"A";
		endif;
	  	$this->common_model->editMultipleDataByMultipleCondition('uw_cash_vouchers',$param1,$whereCondition);
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		redirect(correctLink('ALLCOUPONSDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name 	: exportexcel
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for export data
	** Date 			: 23 November 2024 
	************************************************************************/
	function exportexcel()
	{  
		$this->admin_model->authCheck('admin_view');
		//Generating Logs
	    $this->common_model->generateLogs();
	    
		$data['error'] 					= 	'';
		$data['activeMenu'] 			= 	'cashvoucher';
		$data['activeSubMenu'] 			= 	'allcashvoucher';

		/* Export excel button code */
		if($this->input->post('fromDate')):
			$fromDate 				= strtotime($this->input->post('fromDate'));   
			$data['fromDate'] 		= date('Y-m-d 00:01', strtotime($this->input->post('fromDate')));;			
			$whereCon['where_gte'] 	= array(array("created_date",strtotime($this->input->post('fromDate'))));
		endif;

		if($this->input->post('toDate')):
			$toDate 				= strtotime($this->input->post('toDate'));
			$data['toDate'] 		= date('Y-m-d 23:59', strtotime($this->input->post('toDate')));			
			$whereCon['where_lte']  = array(array("created_date",  strtotime($this->input->post('toDate'))));
		endif;

		if($this->input->post('searchField') && $this->input->post('searchValue')):
			$sField		  =	$this->input->post('searchField');
			$sValue		  =	$this->input->post('searchValue');
			$searchField  = 	$sField;
			$searchValue  = 	$sValue;

			//Coupon generating details start..
			if($sField  == 'generated_by'):
				$whereCon2['where_or'] 	=  array('users_mobile'  => (int)$sValue  ,'users_email'=> $sValue ); 	 
				$result 				= $this->common_model->getData('single','uw_users',$whereCon2);
				if($result):
					$sField = 'created_by';
					$sValue = $result['users_id'];
				endif;
			endif;
			//Coupon generating details end..

			//Redeemed By details start..
			if($sField  == 'redeemed_by'):
				$whereCon2['where_or'] 	=  
								array(
									'users_mobile'  => (int)$sValue,
									// 'users_email'	=> $sValue,
									// 'pos_device_id'	=> $sValue,
									'pos_number'	=> (int)$sValue 
								); 	
				$result = $this->common_model->getData('single','uw_users',$whereCon2);
				if($result):
					$sField = 'seller_id';
					$sValue = (int)$result['users_id'];
				endif;
			endif;
			//Redeemed By details end..

			if(is_numeric($sValue)):
				$whereCon['where'] 	=  array($sField  => (int)$sValue); 	 
			else:
				$whereCon['like'] 	=  array('0'=>$sField , '1' => $sValue); 	 
			endif;

		endif;

		$baseUrl 	  = getCurrentControllerPath('exportexcel');
		$totalRows    = $this->common_model->getData('count','uw_cash_vouchers',$wcon);

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
		$data['searchField'] 	= $sField;
		$data['searchValue'] 	= $sValue;
		$data['fromDate'] 		= $fromDate;
		$data['toDate'] 		= $toDate;

		// echo "<pre>";print_r($data);die();
		$this->layouts->set_title('Export CSV | UWINN');
		$this->layouts->admin_view('cashvoucher/allcashvoucher/exportexcel',array(),$data);
		 
	}

	/***********************************************************************
	** Function name 	: exportexcelApi
	** Developed By 	: Dilip halder
	** Purpose  		: This function used for export order data
	** Date 			: 23 July 2024
	************************************************************************/
	function exportexcelApi(){

		$this->admin_model->authCheck();
		$data['error'] 					= 	'';
		$data['activeMenu'] 			= 	'cashvoucher';
		$data['activeSubMenu'] 			= 	'allcashvoucher';

		/* Export excel button code */
		if($this->input->post('fromDate')):
			$fromDate 				= strtotime($this->input->post('fromDate'));   
			$data['fromDate'] 		= date('Y-m-d 00:01', strtotime($this->input->post('fromDate')));;			
			$whereCon['where_gte'] 	= array(array("created_date",strtotime($this->input->post('fromDate'))));
		endif;

		if($this->input->post('toDate')):
			$toDate 				= strtotime($this->input->post('toDate'));
			$data['toDate'] 		= date('Y-m-d 23:59', strtotime($this->input->post('toDate')));			
			$whereCon['where_lte']  = array(array("created_date",  strtotime($this->input->post('toDate'))));
		endif;

		if($this->input->post('searchField') && $this->input->post('searchValue')):
			$sField		  =	$this->input->post('searchField');
			$sValue		  =	$this->input->post('searchValue');
			if(is_numeric($sValue)):
				$whereCon['where'] 	=  array($sField  => (int)$sValue); 	 
			else:
				$whereCon['like'] 	=  array('0'=>$sField , '1' => $sValue); 	 
			endif;

		endif;


		$baseUrl 	  = getCurrentControllerPath('exportexcel');
		$page 		  = $this->input->post('pageno');
		// $page = 1;
 		$itemsPerPage = 5000;
 		$startIndex   = ($page - 1)*$itemsPerPage;
 		$resultType   = '';
		// $rechagreData = $this->common_model->getData('multiple',$tblName,$whereCon);
 		$tblName      	 = 'uw_cash_vouchers';
		$cashVoucherData = $this->common_model->getCashVouchers($tblName,$whereCon);
		// echo "<pre>";print_r($cashVoucherData);die();

		$CSVData = array();
		foreach($cashVoucherData as $index => $itemsArray):
			$CSVData1['Coupon Code']                   = !empty($itemsArray['coupon_code']) ? $itemsArray['coupon_code'] : 'N/A';
			$CSVData1['Coupon Amount']                 = !empty($itemsArray['amount']) ? $itemsArray['amount'] : 'N/A';
			$CSVData1['Created By']                    = !empty($itemsArray['created_by_user']) ? $itemsArray['created_by_user'] : 'N/A';
			$CSVData1['Created By - Email']            = !empty($itemsArray['created_by_email']) ? $itemsArray['created_by_email'] : 'N/A';
			$CSVData1['Created By - Mobile']           = !empty($itemsArray['created_by_mobile']) ? $itemsArray['created_by_mobile'] : 'N/A';
			
			$CSVData1['Redeemed By']                   = !empty($itemsArray['redeemed_by_user_type']) ? $itemsArray['redeemed_by_user_type'] : 'N/A';
			$CSVData1['Redeemed By - POS Number']      = !empty($itemsArray['redeemed_by_pos_number']) ? $itemsArray['redeemed_by_pos_number'] : 'N/A';
			$CSVData1['Redeemed By - Mobile']          = !empty($itemsArray['redeemed_by_user_mobile']) ? $itemsArray['redeemed_by_user_mobile'] : 'N/A';
			$CSVData1['Redeemed Date']                 = !empty($itemsArray['redeemed_date']) ? $itemsArray['redeemed_date'] : 'N/A';
			
			$CSVData1['Status']                        = !empty($itemsArray['status']) ? $itemsArray['status'] : 'N/A';
			$CSVData1['Created Date']                  = !empty($itemsArray['created_date']) ? date('Y-m-d H:i', $itemsArray['created_date']) : 'N/A';
			array_push($CSVData, $CSVData1);
		endforeach;
		echo json_encode($CSVData);
		die();
	}


	/***********************************************************************
	** Function name 	: cancellation
	** Developed By 	: Dilip halder
	** Purpose  		: This function used for export order data
	** Date 			: 21 November 2024
	************************************************************************/
	public function cancellation($oid="")
	{
		// new MongoDB\BSON\ObjectId($changeStatusId)
		$whereCon['where'] = array('_id' => New MongoDB\BSON\ObjectId($oid));
		$tblName           = "uw_cash_vouchers";
	 	// $VoucherData       = $this->common_model->getCashVouchers('single',$tblName,$whereCon,$shortField,$perPage,$page);
	 	$VoucherData       = $this->common_model->getCashVouchers($tblName,$whereCon);
	 	$VoucherData = $VoucherData[0];

		if(!empty($VoucherData) && $VoucherData['status'] == "A" ):

			//Fetching values from reachrge data in below variables.
			$availableArabianPoints = $VoucherData['created_availableArabianPoints'];
			$USERID 				= $VoucherData['created_by'];
			$request_oid 			= $VoucherData['_id']->{'$id'};
			$code					= $VoucherData['coupon_code'];
			$coupon_amount  		= $VoucherData['amount'];
			$user_oid 				= New MOngoDB\BSON\ObjectId((string)$VoucherData['user_oid']);
			
				//generating laodbalance for deduted amount for respected user..
			  	$Param["load_balance_id"]		 =	(int)$this->common_model->getNextSequence('uw_loadBalance');
				$Param["user_oid"] 				 =	new MongoDB\BSON\ObjectId($user_oid);
		        $Param['request_id']      		 =  $rc_id;
	            $Param['request_oid']     		 =  new MongoDB\BSON\ObjectId($request_oid);
				$Param["user_id_cred"] 			 =	(int)$USERID;
				$Param["user_id_deb"]			 =	(int)0;
				$Param["upoints"] 				 =	(float)$coupon_amount;
				$Param["availableArabianPoints"] =	(float)$availableArabianPoints;
				$Param["end_balance"] 			 =	(float)$availableArabianPoints+ $coupon_amount;
			    $Param["record_type"] 			 =	'Credit';
			    $Param["narration"]				 =	'Cash Voucher Cancelled';
			    $Param["remarks"]				 =	"Cancelled cash voucher amount of AED $coupon_amount has been credited to your UPoints for coupon code $code.";
			    $Param["creation_ip"] 			 =	$this->input->ip_address();
			    $Param["created_at"] 			 =	date('Y-m-d H:i');
			    $Param["created_by"] 			 =	(int)$this->session->userdata('UW_ADMIN_ID');
			    $Param["status"] 				 =	"A";
		    	$loadbalanceResponce 			 = $this->common_model->addData('uw_loadBalance', $Param);
			     
                //cancelling recharged coupon by user... 
				$rechagreParam['status'] = 'CL';		
				$this->common_model->editData($tblName,$rechagreParam ,'_id' , New MongoDB\BSON\ObjectId($oid));

				//Created credited recharge amount..
				$balanceparam    = array(
					'totalArabianPoints'     => +(float)$coupon_amount, 
					'availableArabianPoints' => +(float)$coupon_amount, 
					'winningBalance'         => +(float)$coupon_amount, 
				);
				
				//Updated Recharge coupon status..
				$tblName  			 = 'uw_users';
				$UpdatedUSerBalance  = $this->common_model->manageBalance($tblName,$balanceparam,'_id', new MongoDB\BSON\ObjectId($user_oid));
				$this->session->set_flashdata('alert_success',lang('rechagecenclesuccess'));
					
		elseif(!empty($VoucherData) && $VoucherData['coupon_code_statys'] == "Active" && $VoucherData['coupon_code_statys'] != "Admin"  ):
			
			//cancelling recharged coupon created by Admin... 
			$rechagreParam['status'] 			 = 'CL';		
			$rechagreParam['coupon_code_statys'] = 'Cancelled';
			$rechagreParam["created_by"] 		 =	(int)$this->session->userdata('UW_ADMIN_ID');
			$this->common_model->editData($tblName,$rechagreParam ,'_id' , New MongoDB\BSON\ObjectId($oid));
			$this->session->set_flashdata('alert_success',lang('rechagecenclesuccess'));

		elseif(!empty($VoucherData) && $VoucherData['coupon_code_statys'] == "Redeemed"):
			$this->session->set_flashdata('alert_error',lang('CancellationDenied'));
		elseif(!empty($VoucherData) && $VoucherData['coupon_code_statys'] == "Cancelled" || $VoucherData['status'] == "CL"):
			$this->session->set_flashdata('alert_error',lang('ALREADY_CANCELLED'));
		else:
			$this->session->set_flashdata('alert_error','No data Found');
		endif;
		
		redirect(correctLink('ALLCOUPONSDATA',getCurrentControllerPath('index')));
	}
	 
}

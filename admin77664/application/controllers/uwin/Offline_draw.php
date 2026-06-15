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

class Offline_draw extends CI_Controller {

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
	 + + Function name 	: 	index
	 + + Developed By 	:	Dilip Kumar
	 + + Purpose  		: 	This function used to show offline winner List.
	 + + Date 			:	31 January 2024
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index()
	{	
		$this->admin_model->authCheck('view_data');
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'uwin';
		$data['activeSubMenu'] 				= 	'offline_draw';
		
		if($this->input->get('searchField') && $this->input->get('searchValue')):
			$sField							=	$this->input->get('searchField');
			$sValue							=	$this->input->get('searchValue');

			if($sField == 'amount'):
				$whereCon['where']		     = array($sField => $sValue);

			elseif($sField == 'settler_mobile'):
				// $Seller_whereCon['where']	 = array($sField => (int)$sValue);
				$tblName		      = "uw_users";
			    $Fields 		      = array('_id','user_oid');
		 	    $UsersID 	      = $this->common_model->getLastOrderByFields('users_id',$tblName,'users_mobile',(int)$sValue);
		 	    $whereCon['where']['seller_id'] = (int)$UsersID;
		 	
		 	elseif($sField == 'seller_mobile'):
				$Seller_tblName		      		= "uw_lotto_orders";
				$Seller_whereCon['where'] 		= array('user_phone' => (int)$sValue,'status' => 'A');
		 	    $orderID 	      		  		= $this->common_model->getFieldInArray('order_id',$Seller_tblName,$Seller_whereCon);
		 	    $whereCon['where']['order_id'] =  array('$in' => $orderID);

			elseif(is_numeric($sValue)):
				$whereCon['where']			 	= 	array($sField =>(int)$sValue);
			else:
				if($sField == 'redeem_status' && $sValue == 'unpaid'):
					$whereCon['where']			 	= 	array( 'redeem_status' => array('$in'=> array('',null)) );
				else:
					$whereCon['where']			 	= 	array(trim($sField) =>trim($sValue) );
				endif;
			endif;

			$data['searchField'] 			= 	$sField;
			$data['searchValue'] 			= 	$sValue;
		else:
			$whereCon['where']		 		= 	array();	
			$data['searchField'] 			= 	'';
			$data['searchValue'] 			= 	'';
		endif;

		if($this->input->get('fromDate')):
			$data['fromDate'] 				=   date('Y-m-d H:i', strtotime($this->input->get('fromDate')));  //2023-03-16 15:13
			$whereCon['where_gte'] 			= 	array(array("modified_at",$data['fromDate']));
		else:
			$data['fromDate'] 				=   date('Y-m-d 21:31', strtotime($this->input->get('fromDate') . ' -1 day')); //2023-03-16 15:13
			$whereCon['where_gte'] 			= 	array(array("modified_at",$data['fromDate']));
		endif;

		if($this->input->get('toDate')):
			$data['toDate'] 				=   date('Y-m-d H:i', strtotime($this->input->get('toDate')));  //2023-03-16 15:13
			$whereCon['where_lte'] 			= 	array(array("modified_at",$data['toDate']));
		else:
			$data['toDate'] 				=   date('Y-m-d 21:30');  //2023-03-16 15:13
			$whereCon['where_lte'] 			= 	array(array("modified_at",$data['toDate']));
		endif;
		
		$shortField 						= 	array('modified_at'=>'DESC');
		$baseUrl 							= 	getCurrentControllerPath('index');
		$this->session->set_userdata('CMSTESTIMONIALS',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 	'uw_uwin_winner';
		$con 								= 	'';
		$totalRows 							= 	$this->common_model->getData('count',$tblName,$whereCon,$shortField,'0','0');
		

		// echo "<pre>";
		// print_r($totalRows);
		// die();
		
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
		$this->layouts->set_title('Offline Draw List | U WIN');
		$this->layouts->admin_view('uwin/offline_draw/index',array(),$data);
	}	// END OF FUNCTION
	

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name : changestatus
	 + + Developed By  : Dilip Halder
	 + + Purpose  	   : This function used for change status
	 + + Date 		   : 31 January 2024
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	 function changestatus($changeStatusId='',$statusType='')
	 {  
		$this->admin_model->authCheck('edit_data');

		$loggedInEMAIL = $this->session->userdata('UW_ADMIN_EMAIL');
		if($loggedInEMAIL != 'ugesh@debross.com' && $statusType == 'unpaid'){
		   $this->session->set_flashdata('alert_error',"You Don't have access.");
		  
		   redirect(correctLink('CMSTESTIMONIALS',$this->session->userdata('UW_ADMIN_CURRENT_PATH').$this->router->fetch_class().'/index'));
		}

		$tblName  		   = 'uw_uwin_winner';
		$WinnerList 	   = $this->common_model->getDataByParticularField($tblName,'_id',new MongoDB\BSON\ObjectId($changeStatusId));

 	    $tblName		   = "uw_lotto_orders";
	    $Fields 		   = array('_id','user_oid','user_id');
 	    $orderDetails 	   = $this->common_model->getSingleDataByParticularField($Fields,$tblName,'order_id',$WinnerList['order_id']);
		
		// Data 	  	
	  	$user_OId 	 = $orderDetails['user_oid']['$oid'];
	  	$order_oid   = $orderDetails['_id']['$id'];
	  	$users_id    = $WinnerList['seller_id'];

		$tableName	 = "uw_users";
	    $Fields 	 = array('_id','users_id' ,'totalArabianPoints','availableArabianPoints','redeemed_points');
 	    $userDetails = $this->common_model->getSingleDataByParticularField($Fieldsd,$tableName,'users_id',(int)$users_id);

		if($statusType == "unpaid" && $userDetails['users_type'] == "Users" && $WinnerList['amount'] > $userDetails['availableArabianPoints'] ):
			$this->session->set_flashdata('alert_error',lang('INSUFFICIENT_BALANCE'). " ( ".$userDetails['availableArabianPoints']." ).");

 	    elseif($statusType == "unpaid" ):
 	    	
 	    	//Updated payment
 	    	$changeParam['pos_device_id']  = "";
			$changeParam['redeem_by_mode'] = "";
			$changeParam['redeem_status']  = "";
			$changeParam['seller_id']      = "";
			$changeParam['user_type']      = "";
			$this->common_model->editData('uw_uwin_winner',$changeParam,'_id',new MongoDB\BSON\ObjectId($changeStatusId));
 	    	
 	    	//Updated payment status ..
 	    	if($userDetails['users_type'] == "Users" && $WinnerList['amount'] <= $userDetails['availableArabianPoints'] ):
	 	    	$updateParams['totalArabianPoints']     = $userDetails['totalArabianPoints']     - $WinnerList['amount'];
	 	    	$updateParams['availableArabianPoints'] = $userDetails['availableArabianPoints'] - $WinnerList['amount'];
	 	    	$this->common_model->editData('uw_users', $updateParams, 'users_id', (int)$userDetails['users_id'] );
 	    	endif;

 	    	$whereCon['where']["order_id"]  = $WinnerList['order_id'];
 	    	$whereCon['where']['narration'] = array('$in' => array("Redeem Prize","Redeem Prize Commission","Moved winning Prize" ));

			$tblName     = 'uw_loadBalance';
 	    	$shortField  = array('_id' => -1);
 	    	$winningData = $this->common_model->getData('single',$tblName,$whereCon,$sort);

 	    	if(!empty($winningData)):
 	    		$this->common_model->deleteByMultipleCondition($tblName,$whereCon['where']);
 	    	endif;
			$this->session->set_flashdata('alert_success',lang('statussuccess'));

		elseif($WinnerList['redeem_status'] == "paid" && !empty($WinnerList['redeem_by_mode']) && $statusType == "settle"):

			$param['settle_by']			=  "admin";
			$param['seller_id']			=  $this->session->userdata('UW_ADMIN_ID');
			$this->common_model->editData('uw_uwin_winner',$param,'_id',new MongoDB\BSON\ObjectId($changeStatusId));

			/* Load Balance Table -- after Sign Up*/
			$Redeemparam["load_balance_id"]          =   (int)$this->common_model->getNextSequence('uw_loadBalance');
			$Redeemparam["user_oid"]        	     =   new MongoDB\BSON\ObjectId($user_OId);
			$Redeemparam["order_oid"]       		 =   new MongoDB\BSON\ObjectId($order_oid);
			$Redeemparam["order_id"]       		     =   $WinnerList['order_id'];
			$Redeemparam["user_id_deb"]              =   (int)$userDetails['users_id'];
			$Redeemparam["user_id_cred"]             =   (int)0;
			$Redeemparam["upoints"]       		     =   (float)$WinnerList['amount'];
			$Redeemparam["record_type"]              =   'Credit';
			$Redeemparam["narration"]  			     =   'Redeem Settled';
			$Redeemparam["remarks"]  			     =   "Settled redeem amount ".$WinnerList['amount']." AED in cash";
			$Redeemparam["availableArabianPoints"] 	 =   (float)$userDetails['availableArabianPoints'];
			$Redeemparam["end_balance"] 		 	 =   (float)$userDetails['availableArabianPoints'];
			$Redeemparam["creation_ip"]         	 =   currentIp();
			$Redeemparam["created_at"]          	 =   date('Y-m-d H:i');
			$Redeemparam["created_by"]         	  	 =   (int)$userDetails['users_id'];
			$Redeemparam["status"]               	 =   "A";
			$this->common_model->addData('uw_loadBalance', $Redeemparam);
 	    endif;
		redirect(correctLink('CMSTESTIMONIALS',$this->session->userdata('UW_ADMIN_CURRENT_PATH').$this->router->fetch_class().'/index'));
	 }

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name 	: deletedata
	 + + Developed By 	: Dilip Halder
	 + + Purpose  		: This function used for Delete Data
	 + + Date 			: 31 January 2024
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	function deletedata($deleteId='')
	{  	
		if($deleteId):
			$this->admin_model->authCheck('edit_data');
			$data			=	$this->common_model->getDataByParticularField('uw_uwin_winner','testimonial_id',(int)$deleteId);
			$imageName = $data['image'];
			$this->load->library("upload_crop_img");
			$this->upload_crop_img->_delete_image(trim($imageName)); 
		endif;

		$this->admin_model->authCheck('delete_data');
		$this->common_model->deleteData('uw_uwin_winner','testimonial_id',(int)$deleteId);
		
		$this->session->set_flashdata('alert_success',lang('deletesuccess'));
		
		redirect(correctLink('CMSTESTIMONIALS',$this->session->userdata('UW_ADMIN_CURRENT_PATH').$this->router->fetch_class().'/index'));
	}	

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name 	: exportexcel
	 + + Developed By 	: Dilip Halder
	 + + Purpose  		: This function used for download Excel report.
	 + + Date 			: 16 February 2024
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function exportexcel()
	{	
		$this->admin_model->authCheck('view_data');
		//Generating Logs
		$this->common_model->generateLogs();

		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'uwin';
		$data['activeSubMenu'] 				= 	'offline_draw';

		if($this->input->post('searchField') && $this->input->post('searchValue')):
			$sField							=	$this->input->post('searchField');
			$sValue							=	$this->input->post('searchValue');

			if($sField == 'amount'):
				$whereCon['where']		     = array($sField => $sValue);
 			elseif($sField == 'settler_mobile'):
				$tblName		      = "uw_users";
			    $Fields 		      = array('_id','user_oid');
		 	    $UsersID 	      = $this->common_model->getLastOrderByFields('users_id',$tblName,'users_mobile',(int)$sValue);
		 	    $whereCon['where']['seller_id'] = (int)$UsersID;
		 	elseif($sField == 'seller_mobile'):
				$Seller_tblName		      		= "uw_lotto_orders";
				$Seller_whereCon['where'] 		= array('user_phone' => (int)$sValue,'status' => 'A');
		 	    $orderID 	      		  		= $this->common_model->getFieldInArray('order_id',$Seller_tblName,$Seller_whereCon);
		 	    $whereCon['where']['order_id'] =  array('$in' => $orderID);
			elseif(is_numeric($sValue)):
				$whereCon['where']			 	= 	array($sField =>(int)$sValue);
			else:
				if($sField == 'redeem_status' && $sValue == 'unpaid'):
					$whereCon['where']			 	= 	array( 'redeem_status' => array('$in'=> array('',null)) );
				else:
					$whereCon['where']			 	= 	array(trim($sField) =>trim($sValue) );
				endif;
			endif;

			$data['searchField'] 			= 	$sField;
			$data['searchValue'] 			= 	$sValue;
			$searchField   = $sField;
			$searchValue   = $sValue;

		else:
			$whereCon['where']		 		= 	array();	
			$data['searchField'] 			= 	'';
			$data['searchValue'] 			= 	'';
		endif;

		if($this->input->post('fromDate')):
			$data['fromDate'] 				=   date('Y-m-d H:i', strtotime($this->input->post('fromDate')));  //2023-03-16 15:13
			$fromDate        				=   date('Y-m-d H:i', strtotime($this->input->post('fromDate')));  //2023-03-16 15:13
			$whereCon['where_gte'] 			= 	array(array("modified_at",$data['fromDate']));
		else:
			$data['fromDate'] 				=   date('Y-m-d 21:31', strtotime($this->input->post('fromDate') . ' -1 day')); //2023-03-16 15:13
			$fromDate 						=   date('Y-m-d 21:31', strtotime($this->input->post('fromDate') . ' -1 day')); //2023-03-16 15:13
			$whereCon['where_gte'] 			= 	array(array("modified_at",$data['fromDate']));
		endif;

		if($this->input->post('toDate')):
			$data['toDate'] 				=   date('Y-m-d H:i', strtotime($this->input->post('toDate')));  //2023-03-16 15:13
			$toDate  						=   date('Y-m-d H:i', strtotime($this->input->post('toDate')));  //2023-03-16 15:13
			$whereCon['where_lte'] 			= 	array(array("modified_at",$data['toDate']));
		else:
			$data['toDate'] 				=   date('Y-m-d 21:30');  //2023-03-16 15:13
			$toDate 						=   date('Y-m-d 21:30');
			$whereCon['where_lte'] 			= 	array(array("modified_at",$data['toDate']));
		endif;

		$tblName 							= 	'uw_uwin_winner';
		$shortField 						= 	array('modified_at'=>'DESC');
		$totalRows 							= 	$this->common_model->getData('count',$tblName,$whereCon,$shortField);
		$itemsPerPage 						=   5000;
		// echo "<pre>";print_r($totalRows);die();

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
		
		$totalpage 				 = count($totalpage);
		$data['current_page']    = $current_page;
		$data['total_page'] 	 = $totalpage;
		$data['searchField'] 	 = $searchField;
		$data['searchValue'] 	 = $searchValue;
		$data['fromDate'] 		 = $fromDate;
		$data['toDate'] 		 = $toDate;
		// echo "<pre>";print_r($data);die();
		$this->layouts->set_title('Export CSV | U WIN');
		$this->layouts->admin_view('uwin/offline_draw/exportexcel',array(),$data);
 
		//endif;
		/* Export excel END */
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
		//Generating Logs
		$this->common_model->generateLogs();

		$data['error'] 						= '';
		$data['activeMenu'] 				= 'uwin';
		$data['activeSubMenu'] 				= 'offline_draw';

		if($this->input->post('searchField') && $this->input->post('searchValue')):
			$sField							=	$this->input->post('searchField');
			$sValue							=	$this->input->post('searchValue');

			if($sField == 'amount'):
				$whereCon['where']		     = array($sField => $sValue);
 			elseif($sField == 'settler_mobile'):
				$tblName		      = "uw_users";
			    $Fields 		      = array('_id','user_oid');
		 	    $UsersID 	      = $this->common_model->getLastOrderByFields('users_id',$tblName,'users_mobile',(int)$sValue);
		 	    $whereCon['where']['seller_id'] = (int)$UsersID;
		 	elseif($sField == 'seller_mobile'):
				$Seller_tblName		      		= "uw_lotto_orders";
				$Seller_whereCon['where'] 		= array('user_phone' => (int)$sValue,'status' => 'A');
		 	    $orderID 	      		  		= $this->common_model->getFieldInArray('order_id',$Seller_tblName,$Seller_whereCon);
		 	    $whereCon['where']['order_id'] =  array('$in' => $orderID);
			elseif(is_numeric($sValue)):
				$whereCon['where']			 	= 	array($sField =>(int)$sValue);
			else:
				if($sField == 'redeem_status' && $sValue == 'unpaid'):
					$whereCon['where']			 	= 	array( 'redeem_status' => array('$in'=> array('',null)) );
				else:
					$whereCon['where']			 	= 	array(trim($sField) =>trim($sValue) );
				endif;
			endif;

			$data['searchField'] 			= 	$sField;
			$data['searchValue'] 			= 	$sValue;
		else:
			$whereCon['where']		 		= 	array();	
			$data['searchField'] 			= 	'';
			$data['searchValue'] 			= 	'';
		endif;

		if($this->input->post('fromDate')):
			$data['fromDate'] 				=   date('Y-m-d H:i', strtotime($this->input->post('fromDate')));  //2023-03-16 15:13
			$whereCon['where_gte'] 			= 	array(array("modified_at",$data['fromDate']));
		else:
			$data['fromDate'] 				=   date('Y-m-d 21:31', strtotime($this->input->post('fromDate') . ' -1 day')); //2023-03-16 15:13
			$whereCon['where_gte'] 			= 	array(array("modified_at",$data['fromDate']));
		endif;

		if($this->input->post('toDate')):
			$data['toDate'] 				=   date('Y-m-d H:i', strtotime($this->input->post('toDate')));  //2023-03-16 15:13
			$whereCon['where_lte'] 			= 	array(array("modified_at",$data['toDate']));
		else:
			$data['toDate'] 				=   date('Y-m-d 21:30');  //2023-03-16 15:13
			$whereCon['where_lte'] 			= 	array(array("modified_at",$data['toDate']));
		endif;

		$page = $this->input->post('pageno');
		$tblName 		= 'uw_uwin_winner';
		$shortField 	= array('modified_at'=>'DESC');
 		$itemsPerPage 	= 5000;
 		$startIndex   	= ($page - 1)*$itemsPerPage;
		$RedeemlistData = 	$this->common_model->getData('multiple',$tblName,$whereCon,$shortField,$itemsPerPage,$startIndex);
		  
		$CSVData 	  = array();
		if($RedeemlistData):
			foreach($RedeemlistData as $index => $itemsArray):
				$redeemStatus = !empty($itemsArray['redeem_status']) ? $itemsArray['redeem_status'] : 'Due' ;
		 		$Status 	  = ($itemsArray['status'] == 1 )? 'Active':'Inactive';

				if($itemsArray['redeem_status'] != 'paid' && $itemsArray['redeem_status'] != 'settled'): 
		            $order_id = "UWINNXXXXXX";
	         	else: 
		         	$order_id = stripslashes($itemsArray['order_id']);
	         	endif;


         	 	if($itemsArray['settle_by'] == 'admin'):
		          $tblName            =  "uw_admin";
		          $whereCon1['where'] =  array('admin_id' => (int)$itemsArray['seller_id'] );
		          $userDetails        =  $this->common_model->getData('single',$tblName,$whereCon1,$shortField,$perPage,$page);
		       
		          $userDetails['users_name'] = $userDetails['admin_first_name'];
		          $userDetails['last_name']  = $userDetails['admin_last_name'];
		     	else:
			      $tblName            =  "uw_users";
			      $whereCon1['where'] =  array('users_id' => (int)$itemsArray['seller_id']);
			      $userDetails        =  $this->common_model->getData('single',$tblName,$whereCon1,$shortField,$perPage,$page);
	     	 	endif; 
			 	$CSVData1['TICKET ID']          = !empty($order_id) ? $order_id : 'N/A';
				$CSVData1['COUPON CODE']        = !empty($itemsArray['code']) ? $itemsArray['code'] : 'N/A';
				$CSVData1['Settler First Name'] = !empty($userDetails['users_name']) ? $userDetails['users_name'] : 'N/A';
				$CSVData1['Settler Last Name']  = !empty($userDetails['last_name'])  ? $userDetails['last_name']  : 'N/A';
				$CSVData1['Store Name'] 		= !empty($userDetails['store_name']) ? $userDetails['store_name'] : 'N/A';
				$CSVData1['Bind with'] 			= !empty($userDetails['bind_person_name']) ? $userDetails['bind_person_name'] : 'N/A';
				$CSVData1['Settled Amount']     = !empty($itemsArray['amount']) ? $itemsArray['amount']: 'N/A';
				$CSVData1['Settled Date']       = !empty($itemsArray['modified_at']) ? $itemsArray['modified_at'] : 'N/A';
				$CSVData1['Seller First Name']  = !empty($itemsArray['seller_first_name']) ? $itemsArray['seller_first_name'] : 'N/A';
				$CSVData1['Seller Last Name']   = !empty($itemsArray['seller_last_name']) ? $itemsArray['seller_last_name'] : 'N/A';
				$CSVData1['Settled Status']     = !empty($redeemStatus) ? $redeemStatus : 'N/A';
				$CSVData1['Status']       		= !empty($Status) ? $Status : 'N/A';
				array_push($CSVData, $CSVData1);
			endforeach;
		endif;

		echo json_encode($CSVData);
		die();
		
	}
	 

}
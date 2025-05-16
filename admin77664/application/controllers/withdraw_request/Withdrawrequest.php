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

class Withdrawrequest extends CI_Controller {

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
	 + + Developed By 	: AFSAR ALI
	 + + Purpose  		: This function used for index
	 + + Date 			: 03 JULY 2022
	 + + Updated Date 	: 
	 + + Updated By   	:
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index()
	{	
        $this->admin_model->authCheck();
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'withdraw_request';
		$data['activeSubMenu'] 				= 	'withdrawrequest';
		
		if($this->input->get('fromDate')):
			$fromDate	 = date('Y-m-d H:i', strtotime($this->input->get('fromDate')));
			$whereCondition['where']['created_at']['$gte']  =  $fromDate;
		else:
			$fromDate    = date('Y-m-d 21:31', strtotime('-1 day'));
			$whereCondition['where']['created_at']['$gte']  =  $fromDate;
		endif;

		if($this->input->get('toDate')):
			$toDate	 	 = date('Y-m-d H:i', strtotime($this->input->get('toDate')));
			$whereCondition['where']['created_at']['$lte']  =  $toDate;
		else:
			$toDate	 	 = date('Y-m-d 21:30');
			$whereCondition['where']['created_at']['$lte']  =  $toDate;
		endif;
		$searchField   = $this->input->get('searchField');
		$searchValue   = $this->input->get('searchValue');
		if(!empty($searchField) && !empty($searchValue)):
		  	$whereCondition['where'][$searchField] =  is_numeric($searchValue)?(int)$searchValue:$searchValue;
			$data['searchField'] 			= $searchField;
			$data['searchValue'] 			= $searchValue;
		endif;
		$data['fromDate'] 				= $fromDate;  
		$data['toDate'] 				= $toDate;
		// Where conditions section.
		
		$shortField 						= 	array('_id'=>'desc');
		$baseUrl 							= 	getCurrentControllerPath('index');
		$this->session->set_userdata('ALLPRODUCTSDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 	'uw_withdraw_requests';
		$con 								= 	'';
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
		
		// $data['ALLDATA'] 					= 	$this->common_model->getData('multiple',$tblName,$whereCon,$shortField,$perPage,$page);
		$startIndex   = $page;
		$itemsPerPage = $pageData;
		$resultType  						=   '';
		$data['ALLDATA']  					=   $this->common_model->getWithdrawRequestDetails($resultType,$whereCondition,$startIndex,$itemsPerPage,$tblName);

        // echo '<pre>'; print_r($data['ALLDATA']);die();
		$this->layouts->set_title('All Withdraw Request | Dealz Arabia');
		$this->layouts->admin_view('withdraw_request/allrequest/index',array(),$data);
    } //End of Function

    /***********************************************************************
	** Function name 	: changestatus
	** Developed By 	: Afsar Ali
	** Purpose  		: This function used for change status
	** Date 			: 03-07-2024
	************************************************************************/
	function changestatus($changeStatusId='',$statusType='')
	{  
		$this->admin_model->authCheck('edit_data');
		$param['status']		=	$statusType;
		$param['completed_at']	=	date('Y-m-d h:i');

		if($changeStatusId):
			$tblName  		   = 'uw_withdraw_requests';
			$whereCon['where'] = array( 'request_id' => (int)$changeStatusId );
			$RequestData 	   = $this->common_model->getData('single',$tblName,$whereCon,$shortField);

			if(!empty($RequestData)):
				$amount = $RequestData['amount'];
				$type = $RequestData['type'];

				$tableName	 = "uw_users";
			    $Fields 	 = array('_id','users_id' ,'availableArabianPoints','totalwinningBalance','winningBalance');
			    $userDetails = $this->common_model->getSingleDataByParticularField($Fields,$tableName,'users_id',(int)$RequestData['user_id']);
			    if(!empty($userDetails)):
			    	$debitRecord["load_balance_id"] = (int)$this->common_model->getNextSequence('uw_loadBalance');
		            $debitRecord['user_oid']        = new MongoDB\BSON\ObjectId($userDetails['_id']['$id']);
		            $debitRecord['request_id']      = $RequestData['request_id'];
		            $debitRecord['request_oid']     = new MongoDB\BSON\ObjectId($RequestData['_id']->{'$id'});
		            $debitRecord['user_id_deb']     = (int)0;
		            $debitRecord['user_id_cred']    = (int)$RequestData['user_id'];
		            $debitRecord["availableArabianPoints"] = (float)$userDetails['availableArabianPoints'];
					$debitRecord["end_balance"]     = (float)$userDetails['availableArabianPoints'];
		            $debitRecord['record_type']     = 'Credit';
		            $debitRecord['narration']       = 'Transfer Amount';
		            $debitRecord['remarks']         = 'Winning amount '.$amount.' aed credited in '.$type;
		            $debitRecord['upoints']         = (float)$amount;
		            $debitRecord['creation_ip']     = $this->input->ip_address();;
		            $debitRecord['created_at']      = date('Y-m-d H:i');
		            $debitRecord['created_by']      = (int)$this->session->userdata('UW_ADMIN_ID');
		            $debitRecord['status']          = 'A';
		            $this->common_model->addData('uw_loadBalance', $debitRecord);
		            // echo "<pre>";print_r($debitRecord);die();
			    endif;

        	endif;
		endif;

		$this->common_model->editData('uw_withdraw_requests',$param,'request_id',(int)$changeStatusId);
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		
		redirect(correctLink('ALLPRODUCTSDATA',getCurrentControllerPath('index')));
	} //End of Function

    /***********************************************************************
	** Function name 	: rejectrequest
	** Developed By 	: Afsar Ali
	** Purpose  		: This function used for change status
	** Date 			: 03-07-2024
	************************************************************************/
	function rejectrequest()
	{  
		$this->admin_model->authCheck('edit_data');
		$req_id = $this->input->post('req_id');
		$reason = $this->input->post('reason');
        if($req_id):
        	$tblName  		   = 'uw_withdraw_requests';
			$whereCon['where'] = array( 'request_id' => (int)$req_id );
			$RequestData 	   = $this->common_model->getData('single',$tblName,$whereCon,$shortField);

			if(!empty($RequestData)):
				$amount = $RequestData['amount'];
				$type = $RequestData['type'];

				$tableName	 = "uw_users";
			    $Fields 	 = array('_id','users_id' ,'availableArabianPoints','totalwinningBalance','winningBalance');
			    $userDetails = $this->common_model->getSingleDataByParticularField($Fields,$tableName,'users_id',(int)$RequestData['user_id']);
			    if(!empty($userDetails)):
			    	$debitRecord["load_balance_id"] = (int)$this->common_model->getNextSequence('uw_loadBalance');
		            $debitRecord['user_oid']        = new MongoDB\BSON\ObjectId($userDetails['_id']['$id']);
		            $debitRecord['request_id']      = $RequestData['request_id'];
		            $debitRecord['request_oid']     = new MongoDB\BSON\ObjectId($RequestData['_id']->{'$id'});
		            $debitRecord['user_id_deb']     = (int)0;
		            $debitRecord['user_id_cred']    = (int)$RequestData['user_id'];
				 	$debitRecord["availableArabianPoints"] = (float)$userDetails['availableArabianPoints'];
					$debitRecord["end_balance"]     	   = (float)$userDetails['availableArabianPoints']+$amount;
		            $debitRecord['record_type']     = 'Credit';
		            $debitRecord['narration']       = 'Cancelled Withdraw Request';
		            $debitRecord['remarks']         =  'Withdraw request cancelled to credit in '.$type;
		            $debitRecord['upoints']         = (float)$amount;
		            $debitRecord['creation_ip']     = $this->input->ip_address();;
		            $debitRecord['created_at']      = date('Y-m-d H:i');
		            $debitRecord['created_by']      = (int)$this->session->userdata('UW_ADMIN_ID');
		            $debitRecord['status']          = 'A';
		            $this->common_model->addData('uw_loadBalance', $debitRecord);
		            // echo "<pre>";print_r($debitRecord);die();

		            $param1['availableArabianPoints'] = +(float)$amount;
                    $this->common_model->manageBalance($tblName,$param1,'users_id',(int)$RequestData['users_id']);
			    endif;
        	endif;

            $param['status']		=	'R';
            $param['reason']	    =	$reason;
            $param['reject_at']	    =	date('Y-m-d h:i');
            // echo'<pre>'; print_r($param);die();
            $this->common_model->editData('uw_withdraw_requests',$param,'request_id',(int)$this->input->post('req_id'));
            $this->session->set_flashdata('alert_success',lang('statussuccess'));
        else: 
            $this->session->set_flashdata('alert_error',lang('accessstatusdenied'));
        endif;
		redirect(correctLink('ALLPRODUCTSDATA',getCurrentControllerPath('index')));
	} //End of Function


	/***********************************************************************
	** Function name 	: exportexcel
	** Developed By 	: Dilip halder
	** Purpose  		: This function used for export order data
	** Date 			: 26 January 2024
	************************************************************************/
	function exportexcel()
	{	
		$this->admin_model->authCheck('admin_view');
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'withdraw_request';
		$data['activeSubMenu'] 				= 	'withdrawrequest';
		
		//Generating Logs
	    $this->common_model->generateLogs();
		
		// -----------------------------------------------------------------------------//
		if($this->input->post('fromDate')):
			$fromDate	 = date('Y-m-d H:i', strtotime($this->input->post('fromDate')));
		endif;
		if($this->input->post('toDate')):
			$toDate	 	 = date('Y-m-d H:i', strtotime($this->input->post('toDate')));
		endif;

		// -----------------------------------------------------------------------------//
		$searchField = $this->input->post('searchField');
		$searchValue = $this->input->post('searchValue');
		 
		if($fromDate):
			$whereCondition['where']['created_at']['$gte']  =  $fromDate;
		endif;
		if($toDate):
			$whereCondition['where']['created_at']['$lte']  =  $toDate;
		endif;
		 

		// -----------------------------------------------------------------------------//
		if(!empty($searchField) && !empty($searchValue)):
			$whereCondition['where'][$searchField] =  is_numeric($searchValue)?(int)$searchValue:$searchValue;
		endif;

		// -----------------------------------------------------------------------------//
		$tblName 	= 	'uw_withdraw_requests';
		$con 		= 	'';
		$totalRows	= 	$this->common_model->getData('count',$tblName,$whereCondition,$shortField,'0','0');

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
		$data['OrderData'] 		= $OrderData?$OrderData:array();

		// echo "<pre>";print_r($data);die();
		$this->layouts->set_title('Export CSV | UWINN');
		$this->layouts->admin_view('withdraw_request/allrequest/exportexcel',array(),$data);
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name 	: exportexcelApi
	** Developed By 	: Dilip halder
	** Purpose  		: This function used for export order data
	** Date 			: 23 July 2024
	************************************************************************/
	function exportexcelApi(){
		
		$this->admin_model->authCheck();
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'withdraw_request';
		$data['activeSubMenu'] 				= 	'withdrawrequest';
		
		// -----------------------------------------------------------------------------//
		if($this->input->post('fromDate')):
			$fromDate	 = date('Y-m-d H:i', strtotime($this->input->post('fromDate')));
		endif;
		if($this->input->post('toDate')):
			$toDate	 	 = date('Y-m-d H:i', strtotime($this->input->post('toDate')));
		endif;

		// -----------------------------------------------------------------------------//
		$searchField = $this->input->post('searchField');
		$searchValue = $this->input->post('searchValue');
		 
		$whereCondition = array();
		if($fromDate):
			$whereCondition['where']['created_at']['$gte']  =  $fromDate;
		endif;
		if($toDate):
			$whereCondition['where']['created_at']['$lte']  =  $toDate;
		endif;
		 

		// -----------------------------------------------------------------------------//
		if(!empty($searchField) && !empty($searchValue)):
			$whereCondition['where'][$searchField] =  is_numeric($searchValue)?(int)$searchValue:$searchValue;
		endif; 

		// $page = $this->input->post('pageno');
		$page = $this->input->post('pageno');
		// $page = 1;
 		$itemsPerPage = 5000;
 		$startIndex  = ($page - 1)*$itemsPerPage;
 		$resultType  = '';
		
 		$tblName     = 'uw_withdraw_requests';
		$OrderData = $this->common_model->getWithdrawRequestDetails($resultType,$whereCondition,$startIndex,$itemsPerPage,$tblName);

		$CSVData = array();
		foreach($OrderData as $index => $itemsArray):

			if($itemsArray['status'] == "CL"):
				$order_status = 'Cancelled';
			else:
				$order_status = $itemsArray['order_status'];
			endif;
			$CSVData[$index]['IFSC Code']			= !empty($itemsArray['request_id'])  ? $itemsArray['request_id'] : 'N/A';
		    $CSVData[$index]['Withdraw ID']         = !empty($itemsArray['withdraw_id']) ? $itemsArray['withdraw_id'] : 'N/A';
		    $CSVData[$index]['Type']            	= !empty($itemsArray['type'])  		 ? $itemsArray['type'] : 'N/A';
			$CSVData[$index]['Amount']     			= !empty($itemsArray['amount'])      ? $itemsArray['amount'] : 'N/A';
			$CSVData[$index]['Account Holder']		= !empty($itemsArray['account_holder_name']) ? $itemsArray['account_holder_name'] : 'N/A';
			$CSVData[$index]['Bank Name']			= !empty($itemsArray['bank_name']) 	 ? $itemsArray['bank_name'] : 'N/A';
			$CSVData[$index]['Account No']			= !empty($itemsArray['account_no'])  ? $itemsArray['account_no'] : 'N/A';
			$CSVData[$index]['IFSC Code']			= !empty($itemsArray['ifsc_code']) 	 ? $itemsArray['ifsc_code'] : 'N/A';

			$CSVData[$index]['Cripto Id']			= !empty($itemsArray['cripto_id'])   ? $itemsArray['cripto_id'] : 'N/A';
			$CSVData[$index]['First Name']			= !empty($itemsArray['users_name'])  ? $itemsArray['users_name'] : 'N/A';
			$CSVData[$index]['Last Name']			= !empty($itemsArray['last_name'])   ? $itemsArray['last_name'] : 'N/A';
			$CSVData[$index]['User Email']			= !empty($itemsArray['user_email'])  ? $itemsArray['user_email'] : 'N/A';
			$CSVData[$index]['User Mobile']			= !empty($itemsArray['user_mobile']) ? $itemsArray['user_mobile'] : 'N/A';
			$CSVData[$index]['User Type']			= !empty($itemsArray['users_type'])  ? $itemsArray['users_type'] : 'N/A';
			$CSVData[$index]['Status']				= !empty($itemsArray['status'])      ? $itemsArray['status'] : 'N/A';
		endforeach;
		echo json_encode($CSVData);
		die();
	}



}
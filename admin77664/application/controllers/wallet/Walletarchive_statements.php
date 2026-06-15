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

class Walletarchive_statements extends CI_Controller {

	public function  __construct() 
	{ 
		parent:: __construct();
		error_reporting(E_ALL ^ E_NOTICE);  
		$this->load->model(array('admin_model','emailtemplate_model','sms_model','notification_model','order_model'));
		$this->lang->load('statictext', 'admin');
		$this->load->helper('common');
	} 

	/* * *********************************************************************
	 * * Function name : index
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used for  get Summary Report
	 * * Date 		   	 : 31 March 2023
	 * * Updated Date  : Dilip Halder
	 * * Updated By 	 : 20 June 2024
	 * * **********************************************************************/
	public function index()
	{
		$this->admin_model->authCheck('view_data');
		$data['error'] 				= 	'';
		$data['activeMenu'] 		= 	'wallet';
		$data['activeSubMenu'] 		= 	'walletarchive_statements';

		$searchField = $this->input->get('searchField');
		$searchValue = $this->input->get('searchValue');

		if($this->input->get('fromDate')):
		 	$fromDate = $this->input->get('fromDate');
			$hours = date('H:i',strtotime($fromDate));
			if($hours == '00:00'):
				$start_date      = date( "Y-m-d 00:01" ,strtotime($fromDate));
			else:
				$start_date      = date( "Y-m-d H:i" ,strtotime($fromDate));
			endif;
			$data['fromDate']  =   $start_date;  //2023-03-16 15:13
		endif;

		if($this->input->get('toDate')):
			$toDate = $this->input->get('toDate');
			$hours = date('H:i',strtotime($toDate));
			if($hours == '00:00'):
				$end_date      = date( "Y-m-d 23:59" ,strtotime($toDate));
			else:
				$end_date	   = date( "Y-m-d H:i" ,strtotime($toDate));
			endif;
			$data['toDate']    =   $end_date;  //2023-03-16 15:13
		endif;
		$data['searchField']   =   $searchField;  
		$data['searchValue']   =   $searchValue; 
		
		if($searchField && $searchValue):
			if($searchField == 'users_mobile'):
				$whereCon['where'] = array($searchField => (int)$searchValue );
			elseif($searchField == 'pos_number'):
				$whereCon['where'] = array($searchField => is_numeric($searchValue)?(int)$searchValue:$searchValue );
			else:
				$whereCon['where'] = array($searchField => $searchValue );
			endif;

			$shortField   = array('_id'=> -1 );
			$tblName 	  = 'uw_users';
			$userdetails  = $this->common_model->getData('single',$tblName, $whereCon, $shortField);
			// echo "<pre>"; print_r($userdetails); die();
			$user_OId     = $userdetails['_id']->{'$id'};
			$walletwhereCon['where']  = array('user_oid' => new MongoDB\BSON\ObjectId($user_OId));

			// Statements query start...
			if($start_date):
				$walletwhereCon['where_gte'] = 	array(array('0' => 'created_at', '1' => trim($start_date)));
			endif;
			if($end_date):
				$walletwhereCon['where_lte'] = 	array(array('0' => 'created_at', '1' => trim($end_date)));
			endif;
		endif;

		$this->session->set_userdata('ALLWALLETARCHIVESTATEMENTDATA',currentFullUrl());
		$baseUrl 	   = getCurrentControllerPath('index');
		$qStringdata   = explode('?',currentFullUrl());
		$suffix		   = $qStringdata[1]?'?'.$qStringdata[1]:'';
		$con 		   = '';

		$tblName 	   = 'uw_loadbalance_archives';
		$shortField    = array('load_balance_id'=> -1 );
		if(!empty($walletwhereCon)):
			$totalRows = $this->common_model->getData('count',$tblName, $walletwhereCon, $shortField);
		else:
			$totalRows = 0;
		endif;
		/* pagination start  */ 
		if($this->input->get('showLength') == 'All'):
			$perPage	 			= 	$totalRows;
			$data['perpage'] 		= 	$this->input->get('showLength');  
		elseif($this->input->get('showLength')):
			$perPage	 					= 	$this->input->get('showLength'); 
			$data['perpage'] 		= 	$this->input->get('showLength'); 
		else:
			$perPage	 			= 	SHOW_NO_OF_DATA;
			$data['perpage'] 		= 	50; 
		endif;

		if($this->uri->segment(getUrlSegment())):
       		$page = $this->uri->segment(getUrlSegment());
     	else:
       	 	$page = 0;
     	endif;

  		$data['forAction'] 		 = 	$baseUrl; 
		if($totalRows):
			$first				 =	(int)($page)+1;
			$data['first']		 =	$first;
			
			if($data['perpage'] == 'All'):
				$pageData 	     = $totalRows;
			else:
				$pageData 		 = $data['perpage'];
			endif;
			
			$last				 = ((int)($page)+$pageData)>$totalRows?$totalRows:((int)($page)+$pageData);
			$data['noOfContent'] = 'Showing '.$first.'-'.$last.' of '.$totalRows.' items';
		else:
			$data['first']		 = 1;
			$data['noOfContent'] = '';
		endif;

		$uriSegment 		  =	getUrlSegment();
		$data['PAGINATION']	  =	adminPagination($baseUrl,$suffix,$totalRows,$perPage,$uriSegment);
		$shortField 		  = array('load_balance_id' => -1);
		if(!empty($walletwhereCon)):
			$walletstatements	  = $this->common_model->getData('multiple',$tblName,$walletwhereCon,$shortField,$perPage,$page);
		endif;

		$data['ALLDATA']      = $walletstatements;
		$data['users_type']   = $userdetails['users_type'];
		// echo "<pre>";print_r($data);die();
		
		$this->layouts->set_title('Wallet Statements | UWINN');
		$this->layouts->admin_view('wallet/index',array(),$data);
	}

	/***********************************************************************
	** Function name : exportexcel
	** Developed By  : Dilip halder
	** Purpose       : This function used for export wallet statements
	** Date 		 : 30-05-2023
	** Updated Date  : Dilip halder
	** Updated By    : 20 June 2024 
	************************************************************************/
	function exportexcel(){	

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
		
		if($fromDate):
			$whereCondition['where']['created_at']['$gte']  =  $fromDate;
		endif;
		if($toDate):
			$whereCondition['where']['created_at']['$lte']  =  $toDate;
		endif;

		$searchField = $this->input->post('searchField');
		$searchValue = $this->input->post('searchValue');
		// ---------------------------------Date query end---------------------------------//
		if(!empty($searchField) && !empty($searchValue)):
			$whereCon['where'][$searchField] =  is_numeric($searchValue)?(int)$searchValue:$searchValue;
			$shortField   = array('_id'=> -1 );
			$tblName 	  = 'uw_users';
			$userdetails  = $this->common_model->getData('single',$tblName, $whereCon, $shortField);
			$data['page_name'] = $userdetails['users_name'];
			
			$user_OId     = $userdetails['_id']->{'$id'};
			$whereCondition['where']['user_oid'] =  new MongoDB\BSON\ObjectId($user_OId);
			// echo "<pre>";print_r($whereCondition);die();
		endif;

		// -----------------------------------------------------------------------------//
		$resultType   = "count";
		$shortField   = array('load_balance_id' => -1);
		if(!empty($whereCondition)):
		   $totalRows = $this->common_model->getData('count','uw_loadbalance_archives',$whereCondition,$shortField);
		else:
		   $totalRows = 0;
		endif;
		// echo "<pre>";print_r($totalRows);die();
		$itemsPerPage = 5000;
		// ---------------------------------------------

		$longArray = $totalRows;
		$pageno      = $this->input->get('page');
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
		$this->layouts->admin_view('wallet/exportexcel',array(),$data);		 

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
		
		if($fromDate):
			$whereCondition['where']['created_at']['$gte']  =  $fromDate;
		endif;
		if($toDate):
			$whereCondition['where']['created_at']['$lte']  =  $toDate;
		endif;

		$searchField = $this->input->post('searchField');
		$searchValue = $this->input->post('searchValue');
		// ---------------------------------Date query end---------------------------------//
		if(!empty($searchField) && !empty($searchValue)):
			$whereCon['where'][$searchField] =  is_numeric($searchValue)?(int)$searchValue:$searchValue;
			$shortField   = array('_id'=> -1 );
			$tblName 	  = 'uw_users';
			$userdetails  = $this->common_model->getData('single',$tblName, $whereCon, $shortField);
			$user_OId     = $userdetails['_id']->{'$id'};
			$whereCondition['where']['user_oid'] =  new MongoDB\BSON\ObjectId($user_OId);
			// echo "<pre>";print_r($whereCondition);die();
			
			// $page = $this->input->post('pageno');
			$page = $this->input->post('pageno');
			// $page = 1;
	 		$itemsPerPage = 5000;
	 		$startIndex   = ($page - 1)*$itemsPerPage;
	 		$shortField   = array('load_balance_id' => -1);
			$WalletData   = $this->common_model->getData('multiple','uw_loadbalance_archives',$whereCondition,$shortField,$itemsPerPage,$startIndex);

		endif; 
 		// echo "<pre>";print_r($WalletData);die();

		$CSVData 	  = array();
		if($WalletData):
			foreach($WalletData as $index => $itemsArray):

				if($itemsArray['narration'] == "Redeem Prize"):
			      $remarks  =   "Ticket ID :- ".$itemsArray['order_id'].'. ' .$itemsArray['remarks'];
		  		else:
			  	  $remarks  =  $itemsArray['remarks'];
			    endif;

			    if($itemsArray['record_type'] == 'Credit' ): 
		          $CREDIT = $itemsArray['upoints']; 
		        else: 
		          $CREDIT = '--'; 
		        endif; 

		        if($itemsArray['record_type'] == 'Debit' ): 
		          $DEBIT = $itemsArray['upoints']; 
		        else: 
		          $DEBIT = '--'; 
		        endif; 
				 
			 	$CSVData1['RECORD TYPE']     = !empty($itemsArray['narration']) ? $itemsArray['narration'] : 'N/A';
				$CSVData1['NARRATION']       = !empty($remarks) ? $remarks : 'N/A';
				$CSVData1['STATUS']      	= !empty($itemsArray['record_type']) ? $itemsArray['record_type'] : 'N/A';
				$CSVData1['CREATED']         = !empty($itemsArray['created_at']) ? date('d M Y h:i:s A', strtotime($itemsArray['created_at'])) : 'N/A';
				$CSVData1['OPENING BALANCE'] = !empty($itemsArray['availableArabianPoints']) ? $itemsArray['availableArabianPoints'] : 'N/A';
				$CSVData1['CREDIT']          = !empty($CREDIT) ? $CREDIT : 'N/A';
				$CSVData1['DEBIT']           = !empty($DEBIT) ? $DEBIT : 'N/A';
				$CSVData1['CLOSING BALANCE'] = !empty($itemsArray['end_balance']) ? $itemsArray['end_balance'] : '0';
				array_push($CSVData, $CSVData1);
			endforeach;
		endif;

		echo json_encode($CSVData);
		die();
	}


}

?>
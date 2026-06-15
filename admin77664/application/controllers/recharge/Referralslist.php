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

class Referralslist extends CI_Controller {

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
	 + + Date 			: 12 June 2025
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index()
	{	
		$this->admin_model->authCheck('view_data');
		$data['error'] 			 = '';
		$data['activeMenu'] 	 = 'recharge';
		$data['activeSubMenu'] 	 = 'referralslist';
		
		$searchField = $this->input->get('searchField');
		$searchValue = $this->input->get('searchValue');

		if(!empty($searchField) && !empty($searchValue)):
			$data['searchField'] = $searchField;
			$data['searchValue'] = $searchValue;

			if($searchField == 'referral_given_by'):
				$sField 		= "users_mobile";
				$sValue			= (int)$searchValue;
				$userDetails    = $this->common_model->getSingleDataByParticularField('_id','uw_users',$sField,$sValue);
				$users_oid		= $userDetails['_id']['$id'];
				// echo "<pre>";print_r($users_oid);die();
				$whereCon['where']['user_oid']	 = new MongoDB\BSON\ObjectId($users_oid);

			elseif($searchField == 'referral_given_usertype'):
				$whereCon['where']['referralUser.users_type']   = $searchValue;
			elseif($searchField == 'referral_used_by'):
				
				$sField 			 = "users_mobile";
				$sValue				 = (int)$searchValue;

				$userDetails    = $this->common_model->getSingleDataByParticularField('_id','uw_users',$sField,$sValue);
				$users_oid		= $userDetails['_id']['$id'];
				// echo "<pre>";print_r($users_oid);die();
				$whereCon['where']['request_oid']	 = new MongoDB\BSON\ObjectId($users_oid);

			elseif($searchField == 'referral_code'):
				$userWhereCon['where']  = array('$or' => array(
					array( "referral_code" => $searchValue ),
					array( "referral_code" => (int)$searchValue ),
				));

				$fileList = array('_id');
				$userDetails    = $this->common_model->getParticularFieldByMultipleCondition($fileList,'uw_users',$userWhereCon );
				$users_oid		= $userDetails['_id']['$id'];
				$whereCon['where']['user_oid']	 = new MongoDB\BSON\ObjectId($users_oid);
			endif;

				// echo "<pre>";print_r($whereCon);die();
		endif;

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

		if($fromDate):
			$whereCon['where']['created_at']['$gte']  =  $fromDate;
			$data['fromDate'] 				= $fromDate;  
		endif;
		if($toDate):
			$whereCon['where']['created_at']['$lte']  =  $toDate;
			$data['toDate'] 				= $toDate;
		endif;
		
		$whereCon['where']['narration']                 = "Referrel Commission";

		$shortField 			 = array('_id'=> -1);
		$baseUrl 				 = getCurrentControllerPath('index');
		$this->session->set_userdata('RAFFLEDATA',currentFullUrl());
		$qStringdata			 = explode('?',currentFullUrl());
		$suffix					 = $qStringdata[1]?'?'.$qStringdata[1]:'';
		$con 					 = '';
		$totalRows 				 = $this->common_model->getReferrelDetails('',$whereCon,$shortField);
		$totalRows               = count($totalRows);


		if($this->input->get('showLength') == 'All'):
			$perPage	 		 = $totalRows;
			$data['perpage'] 	 = $this->input->get('showLength');  
		elseif($this->input->get('showLength')):
			$perPage	 		 = $this->input->get('showLength'); 
			$data['perpage'] 	 = $this->input->get('showLength'); 
		else:
			$perPage	 		 = SHOW_NO_OF_DATA;
			$data['perpage'] 	 = SHOW_NO_OF_DATA; 
		endif;

		$uriSegment 			 = getUrlSegment();
	    $data['PAGINATION']		 = adminPagination($baseUrl,$suffix,$totalRows,$perPage,$uriSegment);

       if($this->uri->segment(getUrlSegment())):
           $page = $this->uri->segment(getUrlSegment());
       else:
           $page = 0;
       endif;
		
		$data['forAction'] 		 = $baseUrl; 
		if($totalRows):
			$first				 = (int)($page)+1;
			$data['first']		 = $first;
			
			if($data['perpage'] == 'All'):
				$pageData 		 = $totalRows;
			else:
				$pageData 		 = $data['perpage'];
			endif;
			
			$last				 = ((int)($page)+$pageData)>$totalRows?$totalRows:((int)($page)+$pageData);
			$data['noOfContent'] = 'Showing '.$first.'-'.$last.' of '.$totalRows.' items';
		else:
			$data['first']		 = 1;
			$data['noOfContent'] = '';
		endif;
		
		$data['ALLDATA']		 = $this->common_model->getReferrelDetails('',$whereCon,$shortField,$perPage,$page);

		$this->layouts->set_title('Referrals | List | UWINN');
		$this->layouts->admin_view('referrals/referralslist/index',array(),$data);
	}	// END OF FUNCTION

	 
	/***********************************************************************
	** Function name 	: exportexcel
	** Developed By 	: Dilip halder
	** Purpose  		: This function used for exportexcel order data
	** Date 			: 16 June 2025
	************************************************************************/
	function exportexcel()
	{	
		$this->admin_model->authCheck('view_data');
		$data['error'] 		   = '';
		$data['activeMenu']    = 'recharge';
		$data['activeSubMenu'] = 'referralslist';
		
		//Generating Logs
	    $this->common_model->generateLogs();

	    $searchField = $this->input->post('searchField');
		$searchValue = $this->input->post('searchValue');

		if(!empty($searchField) && !empty($searchValue)):
			$data['searchField'] = $searchField;
			$data['searchValue'] = $searchValue;

			if($searchField == 'referral_given_by'):
				
				$sField 			 = "users_mobile";
				$sValue				 = (int)$searchValue;

				$userDetails    = $this->common_model->getSingleDataByParticularField('_id','uw_users',$sField,$sValue);
				$users_oid		= $userDetails['_id']['$id'];
				// echo "<pre>";print_r($users_oid);die();
				$whereCon['where']['user_oid']	 = new MongoDB\BSON\ObjectId($users_oid);
			elseif($searchField == 'referral_given_usertype'):
				$whereCon['where']['referralUser.users_type']   = $searchValue;
			elseif($searchField == 'referral_used_by'):
				
				$sField 			 = "users_mobile";
				$sValue				 = (int)$searchValue;

				$userDetails    = $this->common_model->getSingleDataByParticularField('_id','uw_users',$sField,$sValue);
				$users_oid		= $userDetails['_id']['$id'];
				// echo "<pre>";print_r($users_oid);die();
				$whereCon['where']['request_oid']	 = new MongoDB\BSON\ObjectId($users_oid);

			elseif($searchField == 'referral_code'):
				$userWhereCon['where']  = array('$or' => array(
					array( "referral_code" => $searchValue ),
					array( "referral_code" => (int)$searchValue ),
				));

				$fileList = array('_id');
				$userDetails    = $this->common_model->getParticularFieldByMultipleCondition($fileList,'uw_users',$userWhereCon );
				$users_oid		= $userDetails['_id']['$id'];
				$whereCon['where']['user_oid']	 = new MongoDB\BSON\ObjectId($users_oid);
			endif;

				// echo "<pre>";print_r($whereCon);die();
		endif;

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

		if($fromDate):
			$whereCon['where']['created_at']['$gte']  =  $fromDate;
			$data['fromDate'] 				= $fromDate;  
		endif;
		if($toDate):
			$whereCon['where']['created_at']['$lte']  =  $toDate;
			$data['toDate'] 				= $toDate;
		endif;
		
		$whereCon['where']['narration'] = "Referrel Commission";

		// -----------------------------------------------------------------------------//
		// $resultType   = "count";
		$totalRows    = $this->common_model->getReferrelDetails($resultType,$whereCon,$shortField);
		$itemsPerPage = 5000;
		$totalRows    = count($totalRows);
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
 		
 		$startIndex         = ($page - 1) * $itemsPerPage;
		$totalpage 				= count($totalpage);
		$data['current_page']   = $current_page;
		$data['total_page'] 	= $totalpage;
		
		$data['searchField'] 	=   $searchField;
		$data['searchValue'] 	=   $searchValue;
		$data['fromDate'] 		=   $fromDate;
		$data['toDate'] 		=   $toDate;

		// echo "<pre>";print_r($data);die();
		$this->layouts->set_title('Export CSV | Referrals List | UWINN');
		$this->layouts->admin_view('referrals/referralslist/exportexcel',array(),$data);
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name 	: exportexcelApi
	** Developed By 	: Dilip halder
	** Purpose  		: This function used for export order data
	** Date 			: 16 June 2025
	************************************************************************/
	function exportexcelApi(){
		
		 $this->admin_model->authCheck('view_data');
		$data['error'] 		   = '';
		$data['activeMenu']    = 'recharge';
		$data['activeSubMenu'] = 'referralslist';
		
		//Generating Logs
	    $this->common_model->generateLogs();

	    $searchField = $this->input->post('searchField');
		$searchValue = $this->input->post('searchValue');

		if(!empty($searchField) && !empty($searchValue)):
			$data['searchField'] = $searchField;
			$data['searchValue'] = $searchValue;

			if($searchField == 'referral_given_by'):
				
				$sField 			 = "users_mobile";
				$sValue				 = (int)$searchValue;

				$userDetails    = $this->common_model->getSingleDataByParticularField('_id','uw_users',$sField,$sValue);
				$users_oid		= $userDetails['_id']['$id'];
				// echo "<pre>";print_r($users_oid);die();
				$whereCon['where']['user_oid']	 = new MongoDB\BSON\ObjectId($users_oid);
			elseif($searchField == 'referral_given_usertype'):
				$whereCon['where']['referralUser.users_type']   = $searchValue;
			elseif($searchField == 'referral_used_by'):
				
				$sField 			 = "users_mobile";
				$sValue				 = (int)$searchValue;

				$userDetails    = $this->common_model->getSingleDataByParticularField('_id','uw_users',$sField,$sValue);
				$users_oid		= $userDetails['_id']['$id'];
				// echo "<pre>";print_r($users_oid);die();
				$whereCon['where']['request_oid']	 = new MongoDB\BSON\ObjectId($users_oid);

			elseif($searchField == 'referral_code'):
				$userWhereCon['where']  = array('$or' => array(
					array( "referral_code" => $searchValue ),
					array( "referral_code" => (int)$searchValue ),
				));

				$fileList = array('_id');
				$userDetails    = $this->common_model->getParticularFieldByMultipleCondition($fileList,'uw_users',$userWhereCon );
				$users_oid		= $userDetails['_id']['$id'];
				$whereCon['where']['user_oid']	 = new MongoDB\BSON\ObjectId($users_oid);
			endif;

				// echo "<pre>";print_r($whereCon);die();
		endif;

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

		if($fromDate):
			$whereCon['where']['created_at']['$gte']  =  $fromDate;
			$data['fromDate'] 				= $fromDate;  
		endif;
		if($toDate):
			$whereCon['where']['created_at']['$lte']  =  $toDate;
			$data['toDate'] 				= $toDate;
		endif;
		
		$whereCon['where']['narration'] = "Referrel Commission";

		// $page = $this->input->post('pageno');
		$page = $this->input->post('pageno')?$this->input->post('pageno'):1;
		// $page = 1;
 		$itemsPerPage = 5000;
 		$startIndex  = ($page - 1)*$itemsPerPage;
 		$resultType  = '';
		$shortField  = array('_id'=> -1);
		$RaffleData  = $this->common_model->getReferrelDetails($resultType,$whereCon,$shortField,$itemsPerPage,$startIndex);

		$CSVData     = array();
		foreach($RaffleData as $index => $itemsArray):

	     	$CSVData[$index]['Referral code given by User (Name)']    = !empty($itemsArray['referralUser']->users_name)    ? $itemsArray['referralUser']->users_name .' '.$itemsArray['referralUser']->last_name : 'N/A';
	     	$CSVData[$index]['Referral code given by User (Mobile)']  = !empty($itemsArray['referralUser']->users_mobile)  ? $itemsArray['referralUser']->users_mobile : 'N/A';
	     	$CSVData[$index]['Referral code given by User (Type)']    = !empty($itemsArray['referralUser']->users_type)    ? $itemsArray['referralUser']->users_type : 'N/A';
	     	$CSVData[$index]['Referral code given by User (BindWith)']= !empty($itemsArray['referralUser']->bind_person_name)? $itemsArray['referralUser']->bind_person_name : 'N/A';
		    $CSVData[$index]['Referral code']            	          = !empty($itemsArray['referralUser']->pos_number)    ? $itemsArray['referralUser']->pos_number : 'N/A';
		    $CSVData[$index]['Referral used by User (Name)']       	  = !empty($itemsArray['referredUser']->users_name)    ? $itemsArray['referredUser']->users_name .' '.$itemsArray['referredUser']->last_name : 'N/A';
		    $CSVData[$index]['Referral used by User (Mobile)']        = !empty($itemsArray['referredUser']->users_mobile)  ? $itemsArray['referredUser']->users_mobile : 'N/A';
		    $CSVData[$index]['Referral used by User (Email)']         = !empty($itemsArray['referredUser']->users_email)   ? $itemsArray['referredUser']->users_email : 'N/A';
		    $CSVData[$index]['Referral used by User (Type)']          = !empty($itemsArray['referredUser']->users_type)    ? $itemsArray['referredUser']->users_type : 'N/A';
		    $CSVData[$index]['Date and Time']                         = !empty($itemsArray['created_at'])                  ? $itemsArray['created_at'] : 'N/A';
		    $CSVData[$index]['Referrel Commission']                   = !empty($itemsArray['upoints'])                     ? $itemsArray['upoints'] : 'N/A';
			
		endforeach;

		echo json_encode($CSVData);
		die();
	}

}
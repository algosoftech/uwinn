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

class Datausage extends CI_Controller {

	public function  __construct() 
	{ 
		parent:: __construct();
		error_reporting(E_ALL ^ E_NOTICE);  
		$this->load->model(array('admin_model','emailtemplate_model','emailsendgrid_model','sms_model','notification_model'));
		$this->lang->load('statictext', 'admin');
		$this->load->helper('common');
	} 

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name 	: index
	 + + Developed By 	: Dilip Halder
	 + + Purpose  		: This function used for index
	 + + Date 			: 26 September 2025
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index()
	{	
		$this->admin_model->authCheck('view_data');
		$data['error'] 		   = '';
		$data['activeMenu']    = 'data';
		$data['activeSubMenu'] = 'datausage';
		
		if($this->input->get('searchField') && $this->input->get('searchValue')):
			
			$searchField = $this->input->get('searchField');
			$searchValue = $this->input->get('searchValue');
			$data['searchField'] = 	$searchField;
			$data['searchValue'] = 	$searchValue;

			$userSearchBy = array('users_mobile','users_email','pos_number');
			if(in_array($searchField, $userSearchBy)):
				$userWhereCon['where'][$searchField] = is_numeric($searchValue)?(int)$searchValue :$searchValue;
				$usarData = $this->common_model->getData('single','uw_users',$userWhereCon);
				if(!empty($usarData)):
					$whereCon['where']['users_id'] = $usarData['users_id'];
				endif;
				// echo "<pre>";print_r($whereCon);die();
			endif;
			// $whereCon['where'][$searchField] = is_numeric($searchValue)?(int)$searchValue:"";
		else:
			$whereCon['where']['test'] = '';
		endif;

		$shortField 			 = array('_id'=> -1);
		$baseUrl 				 = getCurrentControllerPath('index');
		$this->session->set_userdata('ALLDATAUSAGE',currentFullUrl());
		$qStringdata			 = explode('?',currentFullUrl());
		$suffix					 = $qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 				 = 'uw_data_usage';
		$con 					 = '';
		$totalRows 				 = $this->common_model->getData('count',$tblName,$whereCon,$shortField,'0','0');
		// echo '<pre>';print_r($totalRows);die();
		
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
		// echo '<pre>';print_r($data['ALLDATA']);die();
		$this->layouts->set_title('Data Users List | Users | UWINN');
		$this->layouts->admin_view('data/datausage/index',array(),$data);
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name : exportexcel
	** Developed By  : Dilip Halder
	** Purpose       : This function used for export deleted users data
	** Date          : 26 September 2025
	************************************************************************/
	function exportexcel()
	{  
		$this->admin_model->authCheck('view_data');
		$data['error'] 		   = '';
		$data['activeMenu']    = 'users';
		$data['activeSubMenu'] = 'allusers';

		/* Export excel button code */
		// -----------------------------------------------------------------------------//
		if($this->input->post('fromDate')):
			$fromDate	 = date('Y-m-d H:i', strtotime($this->input->post('fromDate')));
		endif;
		if($this->input->post('toDate')):
			$toDate	 	 = date('Y-m-d H:i', strtotime($this->input->post('toDate')));
		endif;
		$searchField = $this->input->post('searchField');
		$searchValue = $this->input->post('searchValue');

		if(!empty($searchField) && !empty($searchValue)):
			$data['searchField'] = 	$searchField;
			$data['searchValue'] = 	$searchValue;

			$userSearchBy = array('users_mobile','users_email','pos_number');
			if(in_array($searchField, $userSearchBy)):
				$userWhereCon['where'][$searchField] = is_numeric($searchValue)?(int)$searchValue :$searchValue;
				$usarData = $this->common_model->getData('single','uw_users',$userWhereCon);
				if(!empty($usarData)):
					$whereCon['where']['users_id'] = $usarData['users_id'];
				endif;
				// echo "<pre>";print_r($whereCon);die();
			endif;
			// $whereCon['where'][$searchField] = is_numeric($searchValue)?(int)$searchValue:"";
		else:
			$whereCon['where']['test'] = '';
		endif;
		 
		/* Export excel button code */
		// -----------------------------------------------------------------------------//
		$resultType   = "count";
		$tblName 	  = "uw_data_usage";
		$totalRows 	  = $this->common_model->getData('count',$tblName,$whereCon); 
		// echo "<pre>";print_r($totalRows);die();

		$itemsPerPage = 5000;
		// ---------------------------------------------

		$longArray    = $totalRows;
		$pageno       = $this->input->get('page');

		// Current page number (received from URL query parameter, e.g., ?page=2)
		$page        = isset($pageno) ? (int)$pageno : 1;
		// Calculate total number of pages
		$totalPages  = ceil($longArray / $itemsPerPage);
		$totalpage   = array();
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
		$this->layouts->admin_view('data/datausage/exportexcel',array(),$data);
		// -----------------------------------------------------------------------------//
	}

	/***********************************************************************
	** Function name 	: exportexcelApi
	** Developed By 	: Dilip halder
	** Purpose  		: This function used for export order data
	** Date 			: 26 September 2025
	************************************************************************/
	function exportexcelApi(){
		
		$this->admin_model->authCheck('view_data');
		$data['error'] 		   = '';
		$data['activeMenu']    = 'users';
		$data['activeSubMenu'] = 'allusers';
		// -----------------------------------------------------------------------------//
 		
 		/* Export excel button code */
		// -----------------------------------------------------------------------------//
		if($this->input->post('fromDate')):
			$fromDate	 = date('Y-m-d H:i', strtotime($this->input->post('fromDate')));
		endif;
		if($this->input->post('toDate')):
			$toDate	 	 = date('Y-m-d H:i', strtotime($this->input->post('toDate')));
		endif;
		$searchField = $this->input->post('searchField');
		$searchValue = $this->input->post('searchValue');

		if(!empty($searchField) && !empty($searchValue)):
			$data['searchField'] = 	$searchField;
			$data['searchValue'] = 	$searchValue;

			$userSearchBy = array('users_mobile','users_email','pos_number');
			if(in_array($searchField, $userSearchBy)):
				$userWhereCon['where'][$searchField] = is_numeric($searchValue)?(int)$searchValue :$searchValue;
				$usarData = $this->common_model->getData('single','uw_users',$userWhereCon);
				if(!empty($usarData)):
					$whereCon['where']['users_id'] = $usarData['users_id'];
				endif;
				// echo "<pre>";print_r($whereCon);die();
			endif;
			// $whereCon['where'][$searchField] = is_numeric($searchValue)?(int)$searchValue:"";
		else:
			$whereCon['where']['test'] = '';
		endif;
		 
		// -----------------------------------------------------------------------------//
		// $page = $this->input->post('pageno');
		$page = $this->input->post('pageno');
		// $page = 1;
 		$itemsPerPage = 5000;
 		$startIndex   = ($page - 1)*$itemsPerPage;
		$tblName 	  = "uw_data_usage";
		$shortField   = array("_id" => -1);
		$UserData 	  = $this->common_model->getData('multiple',$tblName,$whereCon,$shortField); 
		$CSVData  = array();
		foreach($UserData as $index => $itemsArray):
			$usage = $ALLDATAINFO['current_datamode'] == 'WiFi'?$itemsArray['wifi_usage'] :$itemsArray['mobile_data_usage'] ; 
			
			$CSVData[$index]['Usage Mode']          = !empty($itemsArray['current_datamode']) ? $itemsArray['current_datamode'] : 'N/A';
			$CSVData[$index]['Usage']               = !empty($usage) ? $usage : 'N/A';
			$CSVData[$index]['CREATION DATE']       = !empty($itemsArray['created_at']) ? date('d-M-Y ', strtotime($itemsArray['created_at'])) : 'N/A';
		endforeach;
		echo json_encode($CSVData);
		die();
	}

		
}
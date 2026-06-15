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


class Allhourlygameresult extends CI_Controller {

	public function  __construct() 
	{ 
		parent:: __construct();
		error_reporting(E_ALL ^ E_NOTICE);  
		$this->load->model(array('admin_model'));
		$this->lang->load('statictext', 'admin');
		$this->load->helper(['common']);
	} 

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name 	: index
	 + + Developed By 	: Dilip Halder
	 + + Purpose  		: This function used for index
	 + + Date 			: 06 November 2025
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index()
	{	
		$this->admin_model->authCheck('view_data');
		$data['error'] 		   = '';
		$data['activeMenu']    = 'hourlygame';
		$data['activeSubMenu'] = 'allhourlygameresult';
		$toDate                = '';
		$whereCon              = array();
		
		$searchField = $this->input->get('searchField');
		$searchValue = $this->input->get('searchValue');
		$resultDate  = $this->input->get('result_date');

		if($this->input->get('fromDate')):
			$fromDate	 = date('Y-m-d H:i', strtotime($this->input->get('fromDate')));
			$whereCon['where']['creation_date']['$gte']  =  strtotime($fromDate);
		endif;
		if($this->input->get('toDate')):
			$toDate	 	 = date('Y-m-d H:i', strtotime($this->input->get('toDate')));
			$whereCon['where']['creation_date']['$lte']  =  strtotime($toDate);
		endif;
		
		$data['searchField']  = $searchField;
		$data['searchValue']  = $searchValue;
		$data['fromDate'] 	  = $resultDate;  
		$data['toDate'] 	  = $toDate;
		$data['resultDate']   = $resultDate;
		 
		// Where conditions section.
		if(!empty($searchField) && !empty($searchValue)):
			$whereCon['where'][$searchField] = is_numeric($searchValue)?(int)$searchValue:$searchValue;
		endif;
		if(!empty($resultDate)):
			$whereCon['where']['_id'] = (int)strtotime($resultDate);
		endif;

		$shortField 						 = array('_id'=> -1);
		$baseUrl 							 = getCurrentControllerPath('index');
		$this->session->set_userdata('ALLRESULTSDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	isset($qStringdata[1]) && $qStringdata[1] ? '?'.$qStringdata[1] : '';
		$tblName 							= 	'uw_hourly_game_draw_result';
		$con 								= 	'';
		$totalRows 							= 	$this->common_model->fetchresultData('count',$tblName,$whereCon,$shortField,'0','0');
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
	 
		$data['ALLDATA']  =  $this->common_model->fetchresultData('multiple',$tblName,$whereCon,$shortField,$perPage,$page);
		// echo "<pre>";print_r($data['ALLDATA']); die();
		$this->session->unset_userdata('resultDate');

		$this->layouts->set_title('All Hourly Game Result | UWINN');
		$this->layouts->admin_view('hourlygame/allhourlygameresult/index',array(),$data);
	}	// END OF FUNCTION
	
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name : addeditdata
	 + + Developed By  : Dilip Halder
	 + + Purpose  	   : This function used for Add Edit data
	 + + Date 		   : 26 moCTOBER 2024
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function addeditdata($editId='')
	{		
		$data['error'] 		   = '';
		$data['activeMenu']    = 'hourlygame';
		$data['activeSubMenu'] = 'allhourlygameresult';
		$data['EDITDATA']      = array();

		$tblName            = "uw_hourly_games";
		$shortField         = array('_id'=> -1 );
		$whereCon['where']  = array('status' => "A");         
		$data['allproduct'] = $this->common_model->getData('multiple',$tblName,$whereCon,$shortField);


		if(!empty($data['allproduct'])):
			foreach($data['allproduct'] as $productKey => $product):
				$hourlyTimeOptions = array();
				$startDate  = (int)$product['start_date'];
				$expiryDate = (int)$product['expiry_date'];
				while($startDate < $expiryDate):
					$startDate = strtotime('+1 hour', $startDate);
					if($startDate <= $expiryDate):
						$hourlyTimeOptions[] = date('Y-m-d H:i:s', $startDate);
					else:
						$expiryDateSeconds = date('s',$expiryDate);
						if($expiryDateSeconds =='59'):
							$expiryDate = strtotime('+1 second', $expiryDate);
						endif;
						$hourlyTimeOptions[] = date('Y-m-d H:i:s', $expiryDate);
						break;
					endif;
				endwhile;
				$data['allproduct'][$productKey]['draw_time_options'] = $hourlyTimeOptions;
			endforeach;
		endif;

		if(!empty($editId)):
			$data['EDITDATA'] = $this->common_model->getDataByParticularField(
				'uw_hourly_game_draw_result','_id', new MongoDB\BSON\ObjectId($editId)
			);
		endif;
		
		if($this->input->post('SaveChanges')):

			$error			 = 'NO';
			$this->form_validation->set_rules('result_date' , 'Result Date', 'trim|required');
			$this->form_validation->set_rules('product_details' ,'Products', 'trim|required');
			$this->form_validation->set_rules('hourly_game_time' ,'Hourly Game Time', 'trim|required');
			$this->form_validation->set_rules('draw_result' , 'Draw Result', 'trim|required');
			$this->form_validation->set_rules('SaveChanges', 'SaveChanges', 'trim|required');
			
			if($this->form_validation->run() && $error == 'NO'):
				
				$productetails = explode('_____',$this->input->post('product_details'));
				$selectedHourlyGameTime = $this->input->post('hourly_game_time');
				$selectedHourlyGameTimeTs = strtotime($selectedHourlyGameTime);

				$productWhereCon = array();
				$productWhereCon['where']['products_id'] = (int)$productetails[0];
				$productData = $this->common_model->getData('single', 'uw_hourly_games', $productWhereCon);
				$productStartTs = !empty($productData['start_date']) ? (int)$productData['start_date'] : 0;
				$productExpiryTs = !empty($productData['expiry_date']) ? (int)$productData['expiry_date'] : 0;

				// if(
				// 	empty($productData) ||
				// 	empty($selectedHourlyGameTimeTs) ||
				// 	$selectedHourlyGameTimeTs < $productStartTs ||
				// 	$selectedHourlyGameTimeTs > $productExpiryTs
				// ):
				// 	$this->session->set_flashdata('alert_error', 'Selected draw time is outside campaign start/expiry limit.');
				// 	redirect(currentFullUrl());
				// endif;

				$param['result_date'] = (int)strtotime($this->input->post('result_date'));
				$param['products_id'] = (int)$productetails[0];
				$param['product_name'] = stripslashes($productetails[1]);
				$param['draw_result_time'] = date('H:i:s', strtotime($selectedHourlyGameTime));
				if(!empty($selectedHourlyGameTime)):
					$param['result_date'] = (int)strtotime(date('Y-m-d', strtotime($selectedHourlyGameTime)));
				endif;

				$tblName2 = 'uw_hourly_game_draw_result';
				$whereCondition['where'] = $param;
				$getResultDraw           = $this->common_model->getData('single',$tblName2 ,$whereCondition);
				$param['draw_result']    = $this->input->post('draw_result');

				if($this->input->post('CurrentDataID') ==''):
					// if(!empty($getResultDraw)){
					// 	$this->session->set_flashdata('alert_error',lang('ALREADY_ADDED'));
					// 	redirect(correctLink('ALLRESULTSDATA',getCurrentControllerPath('index')));
					// }
					
					$param['status']	    = 'A';
					$param['result_id']		= (int)$this->common_model->getNextSequence('uw_hourly_game_draw_result');
					$param['creation_ip']	= currentIp();
					$param['creation_date']	= (int)$this->timezone->utc_time();//currentDateTime();
					$param['created_by']	= (int)$this->session->userdata('UW_ADMIN_ID');
					
					
					$alastInsertId			= $this->common_model->addData($tblName2,$param);
					$this->session->set_flashdata('alert_success',lang('addsuccess'));
				else:
					$resultId				= $this->input->post('CurrentDataID');
					$param['update_ip']		= currentIp();
					$param['update_date']	= (int)$this->timezone->utc_time();//currentDateTime();
					$param['updated_by']	= (int)$this->session->userdata('UW_ADMIN_ID');
					$this->common_model->editData($tblName2,$param,'_id',new MongoDB\BSON\ObjectId($resultId));
					$this->session->set_flashdata('alert_success',lang('updatesuccess'));
				endif;
				redirect(correctLink('ALLRESULTSDATA',getCurrentControllerPath('index')));
			endif;
		endif;

		$this->layouts->set_title('Add Hourly Game Result | UWINN');
		$this->layouts->admin_view('hourlygame/allhourlygameresult/addeditdata',array(),$data);
	}	// END OF FUNCTION	


	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name : viewdetails
	 + + Developed By  : Dilip Halder
	 + + Purpose  	   : This function used for View details data
	 + + Date 		   : 07 November 2025
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function viewdata($editId='')
	{
		$data['error'] 		   = '';
		$data['activeMenu']    = 'hourlygame';
		$data['activeSubMenu'] = 'allhourlygameresult';
		$data['EDITDATA']      = array();
		$this->session->set_userdata('resultDate',date('Y-m-d',$editId));
		$whereCon['where']['result_date'] = (int)$editId;
		$data['EDITDATA'] = $this->common_model->getData('multiple','uw_hourly_game_draw_result',$whereCon);
		// echo "<pre>";print_r($data['EDITDATA']); die();

		$baseUrl = getCurrentControllerPath('index');
		$this->session->set_userdata('ALLRESULTSDATA',currentFullUrl());
		
		$this->layouts->set_title('View Hourly Game Result Details | UWINN');
		$this->layouts->admin_view('hourlygame/allhourlygameresult/viewdetails',array(),$data);
	}
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
		$this->common_model->editData('uw_hourly_game_draw_result',$param,'_id',new MongoDB\BSON\ObjectId($changeStatusId));
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		redirect(correctLink('ALLRESULTSDATA',getCurrentControllerPath('index')));
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
		$this->common_model->deleteData('uw_hourly_game_draw_result','_id',new MongoDB\BSON\ObjectId($deleteId));
		$this->session->set_flashdata('alert_success',lang('deletesuccess'));
		redirect(correctLink('ALLRESULTSDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name 	: bulkdeletedata
	** Developed By 	: Dilip Halder
	** Purpose  		: Delete multiple result dates from list
	** Date 			: 02 June 2026
	************************************************************************/
	function bulkdeletedata()
	{
		$this->admin_model->authCheck('delete_data');

		$ids = $this->input->post('ids');
		if(empty($ids) || !is_array($ids)):
			if($this->input->is_ajax_request()):
				header('Content-Type: application/json');
				echo json_encode(array('status' => false, 'message' => 'Please select at least one row to delete.'));
				exit;
			endif;
			$this->session->set_flashdata('alert_error', 'Please select at least one row to delete.');
			redirect(correctLink('ALLRESULTSDATA', getCurrentControllerPath('index')));
		endif;

		$resultDates = array();
		foreach($ids as $resultDate):
			$resultDate = (int)$resultDate;
			if($resultDate > 0):
				$resultDates[] = $resultDate;
			endif;
		endforeach;
		$resultDates = array_values(array_unique($resultDates));

		$deletedCount = 0;
		if(!empty($resultDates)):
			$this->mongo_db->where_in('result_date', $resultDates);
			$this->mongo_db->delete_all('uw_hourly_game_draw_result');
			$deletedCount = count($resultDates);
		endif;

		if($this->input->is_ajax_request()):
			header('Content-Type: application/json');
			echo json_encode(array(
				'status'  => $deletedCount > 0,
				'message' => $deletedCount > 0 ? $deletedCount.' result date(s) deleted successfully.' : 'No items could be deleted.',
				'count'   => $deletedCount
			));
			exit;
		endif;

		if($deletedCount > 0):
			$this->session->set_flashdata('alert_success', lang('deletesuccess'));
		else:
			$this->session->set_flashdata('alert_error', 'No items could be deleted.');
		endif;
		redirect(correctLink('ALLRESULTSDATA', getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name 	: settings
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for settings data
	** Date 			: 06 November 2025
	************************************************************************/	
	public function settings($editId='')
	{
		$data['error'] 		   = '';
		$data['activeMenu']    = 'hourlygame';
		$data['activeSubMenu'] = 'allhourlygameresult';
		
		$data['EDITDATA'] = array();
		
		if($editId):
			$this->admin_model->authCheck('edit_data');
			$data['EDITDATA'] = $this->common_model->getDataByParticularField('uw_hourly_game_draw_result_settings','_id',new MongoDB\BSON\ObjectId($editId));
		else:
			$data['EDITDATA'] = $this->common_model->getData('single','uw_hourly_game_draw_result_settings');
			$this->admin_model->authCheck('view_data');
		endif;

		
		if($this->input->post('SaveChanges')):
			$error = 'NO';
			$this->form_validation->set_rules('range_value', 'Range Value', 'trim|required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]');
			$this->form_validation->set_rules('SaveChanges', 'SaveChanges', 'trim|required');
			
			if($this->form_validation->run() && $error == 'NO'):
				$param['range_value'] = (int)$this->input->post('range_value');
				
				if($this->input->post('CurrentDataID') == ''):
					$param['status'] = 'A';
					$param['result_id'] = (int)$this->common_model->getNextSequence('uw_hourly_game_draw_result_settings');
					$param['creation_ip'] = currentIp();
					$param['creation_date'] = (int)$this->timezone->utc_time();
					$param['created_by'] = (int)$this->session->userdata('UW_ADMIN_ID');
					$this->common_model->addData('uw_hourly_game_draw_result_settings',$param);
					$this->session->set_flashdata('alert_success',lang('addsuccess'));
				else:
					$resultId = $this->input->post('CurrentDataID');
					$param['update_ip'] = currentIp();
					$param['update_date'] = (int)$this->timezone->utc_time();
					$param['updated_by'] = (int)$this->session->userdata('UW_ADMIN_ID');
					$this->common_model->editData('uw_hourly_game_draw_result_settings',$param,'_id',new MongoDB\BSON\ObjectId($resultId));
					$this->session->set_flashdata('alert_success',lang('updatesuccess'));
				endif;
				redirect(correctLink('ALLRESULTSDATA',getCurrentControllerPath('index')));
			endif;
		endif;
		
		$this->layouts->set_title('Hourly Game Result Settings | UWINN');
		$this->layouts->admin_view('hourlygame/allhourlygameresult/settings',array(),$data);
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
		$data['error'] 		   = '';
		$data['activeMenu']    = 'hourlygame';
		$data['activeSubMenu'] = 'allhourlygameresult';
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
		$data['searchField'] 	 = $searchField;
		$data['searchValue'] 	 = $searchValue;
		$data['fromDate'] 		 = $fromDate;
		$data['toDate'] 		 = $toDate;
		$data['cancelled_order'] = $cancelled_order;
		$data['draw_one'] 		 = $draw_one;
		$data['draw_two'] 		 = $draw_two;
		$data['productIds']		 = json_encode($productIds);
		$this->layouts->set_title('Export CSV | UWINN');
		$this->layouts->admin_view('hourlygame/allhourlygameresult/exportexcel',array(),$data);		 

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
	
}
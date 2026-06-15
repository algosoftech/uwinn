<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Alleligiblewinners extends CI_Controller {

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
	 + + Date 			: 16 October 2025
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index()
	{	
		$this->admin_model->authCheck('view_data');
		$data['error'] 	       = '';
		$data['activeMenu']    = 'uwinn_raffle';
		$data['activeSubMenu'] = 'alleligiblewinners';
		
		if($this->input->get('searchField') && $this->input->get('searchValue')):
			$sField					= $this->input->get('searchField');
			$sValue					= $this->input->get('searchValue');
			$whereCon['where']		= array($sField => trim($sValue));
		endif;

		if($this->input->get('fromDate')):
			$fromDate	 = date('Y-m-d H:i', strtotime($this->input->get('fromDate')));
		else:
			$fromDate    = date('Y-m-d 00:01', strtotime('-1 day'));
		endif;
		if($this->input->get('toDate')):
			$toDate	 	 = date('Y-m-d H:i', strtotime($this->input->get('toDate')));
		else:
			$toDate	 	 = date('Y-m-d 23:59');
		endif;
		
		if($fromDate):
			$whereCon['where']['created_at']['$gte']  =  $fromDate;
		endif;
		if($toDate):
			$whereCon['where']['created_at']['$lte']  =  $toDate;
		endif;

		$data['searchField']    = $sField;
		$data['searchValue']    = $sValue;
		$data['fromDate'] 		= $fromDate;  
		$data['toDate'] 		= $toDate;
		
		$shortField 				= array('_id'=> -1);
		$baseUrl 					= getCurrentControllerPath('index');
		$this->session->set_userdata('ELIGIBLERAFFLEWINNERS',currentFullUrl());
		$qStringdata				= explode('?',currentFullUrl());
		$suffix						= $qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 					= 'uw_raffle_eligible_orders';
		$con 							= "";
		$totalRows 					= $this->common_model->getData('count',$tblName,$whereCon,$shortField,'0','0');
		// echo "<pre>";print_r($totalRows);die();

		if($this->input->get('showLength') == 'All'):
			$perPage	 				= $totalRows;
			$data['perpage'] 	   = $this->input->get('showLength');  
		elseif($this->input->get('showLength')):
			$perPage	 				= $this->input->get('showLength'); 
			$data['perpage'] 		= $this->input->get('showLength'); 
		else:
			$perPage	 				= SHOW_NO_OF_DATA;
			$data['perpage'] 		= SHOW_NO_OF_DATA; 
		endif;

		$uriSegment 				= getUrlSegment();
	   $data['PAGINATION']		= adminPagination($baseUrl,$suffix,$totalRows,$perPage,$uriSegment);

    	if($this->uri->segment(getUrlSegment())):
        	$page = $this->uri->segment(getUrlSegment());
    	else:
     		$page = 0;
 		endif;
		
		$data['forAction'] 	   = $baseUrl; 
		if($totalRows):
			$first					= (int)($page)+1;
			$data['first']			= $first;
			
			if($data['perpage'] == 'All'):
				$pageData 			= $totalRows;
			else:
				$pageData 			= $data['perpage'];
			endif;
			
			$last						= ((int)($page)+$pageData)>$totalRows?$totalRows:((int)($page)+$pageData);
			$data['noOfContent']	= 'Showing '.$first.'-'.$last.' of '.$totalRows.' items';
		else:
			$data['first']			= 1;
			$data['noOfContent']	= "";
		endif;
		$data['ALLDATA'] 			= $this->common_model->getData('multiple',$tblName,$whereCon,$shortField,$perPage,$page);
		// echo "<pre>";print_r($data);die();

		$this->layouts->set_title('Raffle EligibleWinner | UWINN');
		$this->layouts->admin_view('raffle/eligiblewinners/index',array(),$data);
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name 	: changestatus
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for change status
	** Date 			: 21 January 2024
	************************************************************************/
	function changestatus($orderID='')
	{   
		// checking editing permission..
		$this->admin_model->authCheck('edit_data');

		$tblName 				 = "uw_raffle_eligible_orders";
		$whereCondition['where'] = array('order_id' => $orderID);
		$winnerData              = $this->common_model->getData('single',$tblName , $whereCondition);


		if(!empty($winnerData)):
			$param1['status'] = $winnerData['status'] == "A"?"I":"A";
			$whereCondition1  = array('order_id' => $orderID);
			$this->common_model->editMultipleDataByMultipleCondition('uw_raffle_eligible_orders',$param1,$whereCondition1);
			$this->session->set_flashdata('alert_success',lang('statussuccess'));
		else:
			$this->session->set_flashdata('alert_success',lang('adderror'));
		endif;
		redirect(correctLink('ELIGIBLERAFFLEWINNERS',getCurrentControllerPath('index')));
	}

		/***********************************************************************
	** Function name 	: exportexcel
	** Developed By 	: Dilip halder
	** Purpose  		: This function used for export order data
	** Date 			: 16 October 2025
	** Updated By     	: Dilip Halder
	** Updated Date     : 16 October 2025
	************************************************************************/
	function exportexcel()
	{	
		$data['error'] 	       = '';
		$data['activeMenu']    = 'uwinn_raffle';
		$data['activeSubMenu'] = 'alleligiblewinners';

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

		$searchField     = $this->input->post('searchField');
		$searchValue     = $this->input->post('searchValue');

		// ---------------------------------Date query end---------------------------------//
		if(!empty($searchField) && !empty($searchValue)):
			$whereCondition['where'][$searchField] =  is_numeric($searchValue)?(int)$searchValue:$searchValue;
		endif;

		if($fromDate):
			$whereCondition['where']['created_at']['$gte'] = $fromDate;
		endif;
		if($toDate):
			$whereCondition['where']['created_at']['$lte'] = $toDate;
		endif;

		$tblName 	  = 'uw_raffle_eligible_orders';
		$con 		  = "";
		$totalRows 	  = $this->common_model->getData('count',$tblName,$whereCon,$shortField,'0','0');
		$itemsPerPage = 5000;
		// ---------------------------------------------

		$longArray  = $totalRows;
		$pageno     = $this->input->get('page');
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
		
		$totalpage 				 = count($totalpage);
		$data['current_page']    = $current_page;
		$data['total_page'] 	 = $totalpage;
		$data['searchField'] 	 = $searchField;
		$data['searchValue'] 	 = $searchValue;
		$data['fromDate'] 		 = $fromDate;
		$data['toDate'] 		 = $toDate;
		$this->layouts->set_title('Export CSV | UWINN');
		$this->layouts->admin_view('raffle/eligiblewinners/exportexcel',array(),$data);		 

	}	// END OF FUNCTION

	/***********************************************************************
	** Function name 	: exportexcelApi
	** Developed By 	: Dilip halder
	** Purpose  		: This function used for export order data
	** Date 			: 16 October 2025
	** Updated By     	: Dilip Halder
	** Updated Date     : 16 October 2025
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
			$whereCondition['where']['created_at']['$gte'] = $fromDate;
		endif;
		if($toDate):
			$whereCondition['where']['created_at']['$lte'] = $toDate;
		endif;

		$searchField     = $this->input->post('searchField');
		$searchValue     = $this->input->post('searchValue');

		// ---------------------------------Date query end---------------------------------//
		if(!empty($searchField) && !empty($searchValue)):
			$whereCondition['where'][$searchField] =  is_numeric($searchValue)?(int)$searchValue:$searchValue;
		endif;
		
		$page = $this->input->post('pageno');
 		$itemsPerPage = 5000;
 		$startIndex   = ($page - 1)*$itemsPerPage;
		$tblName 	  = 'uw_raffle_eligible_orders';
		$shortField   = array('_id'=> -1);
		$winnerData   = $this->common_model->getData('multiple',$tblName,$whereCondition,$shortField,$itemsPerPage,$startIndex);

		$CSVData 	  = array();
		$sno = $startIndex + 1; // Calculate proper serial number based on pagination
		
		foreach($winnerData as $index => $itemsArray):
			// Determine status
            if($itemsArray['status']     == "A"):
                $status = 'Active';
			elseif($itemsArray['status'] == "I"): 
				$status = 'Inactive';
			elseif($itemsArray['status'] == "CL"): 
				$status = 'Cancelled';
            endif;
            
            // Format contact number as country_code + mobile_number
            $contactNumber = '';
            if(!empty($itemsArray['country_code']) && !empty($itemsArray['mobile_number'])):
                $contactNumber = $itemsArray['country_code'] . ' ' . $itemsArray['mobile_number'];
            else:
                $contactNumber = 'N/A';
            endif;
            
            // Create CSV row data matching the table structure
			$CSVData[] = array(
                'S.No.' => $sno,
                'Order ID' => !empty($itemsArray['order_id']) ? $itemsArray['order_id'] : 'N/A',
                'Added By' => !empty($itemsArray['pos_number']) ? $itemsArray['pos_number'] : 'N/A',
                'Name'     => !empty($itemsArray['first_name'].' '.$itemsArray['last_name']) ? $itemsArray['first_name'].' '.$itemsArray['last_name'] : 'N/A',
                'Contact Number' => $contactNumber,
                'Status' => $status,
                'Created At' => !empty($itemsArray['created_at']) ? $itemsArray['created_at'] : 'N/A'
            );
            
            $sno++;
		endforeach;

		echo json_encode($CSVData);
		die();
	}
	 
	/***********************************************************************
	** Function name 	: resendconfirmation
	** Developed By 	: Dilip halder
	** Purpose  		: This function used for resendconfirmation
	** Date 			: 22 October 2025
	************************************************************************/
	function resendconfirmation($orderId='')
	{
		$this->admin_model->authCheck('edit_data');

		$tblName 				 = "uw_lotto_orders";
		$whereCondition['where'] = array('order_id' => $orderId );
		$winnerData              = $this->common_model->getData('single',$tblName , $whereCondition);

		$tblName 				 = "uw_raffle_eligible_orders";
		$whereCondition['where'] = array('order_id' => $orderId );
		$raffleOrderData              = $this->common_model->getData('single',$tblName , $whereCondition);

		$countryCode  = $raffleOrderData['country_code'];
		$mobileNumber = $raffleOrderData['mobile_number'];

		
		$productId               = $winnerData['product_id'];
		$productWhereCondition['where'] = array('products_id' => (int)$productId );
		$productDetails          = $this->common_model->getData('single','uw_products',$productWhereCondition);
		// echo "<pre>";print_r($productDetails['raffle_draw_announcement_date']);die();

		$drawDate  = date("d/m/Y",strtotime($productDetails['raffle_draw_announcement_date']));
		$message   = "Your Order ID: ".$orderId." has been successfully added to the ".$drawDate." campaign.";
		$mobileNumber = $countryCode.$mobileNumber;
		$this->sms_model->raffleWinnersSms($mobileNumber,$message,"UWINN");
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		redirect(correctLink('ELIGIBLERAFFLEWINNERS',getCurrentControllerPath('index')));
	}
 

}
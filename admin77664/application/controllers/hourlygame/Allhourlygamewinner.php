<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 

class Allhourlygamewinner extends CI_Controller {

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
	 + + Date 			: 02 April 2026
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index()
	{	
		$this->admin_model->authCheck('view_data');
		$data['error'] 		   = '';
		$data['activeMenu']    = 'hourlygame';
		$data['activeSubMenu'] = 'hourlygamewinner';
		$whereCon              = array('where' => array());
		$data['fromDate']      = $this->input->get('fromDate') ? $this->input->get('fromDate') : '';
		$data['isOrderSearch'] = false;

		if($this->input->get('searchField') && $this->input->get('searchValue') !== '' && $this->input->get('searchValue') !== null):
			$sField				  = $this->input->get('searchField');
			$sValue				  = trim($this->input->get('searchValue'));
			$data['searchField']  = $sField;
			$data['searchValue']  = $sValue;
			if($sField == 'order_id'):
				$data['isOrderSearch'] = true;
				$whereCon['where']['order_id'] = $sValue;
			else:
				$whereCon['where'][$sField] = is_numeric($sValue) ? (int)$sValue : $sValue;
			endif;
		else:
			$data['searchField'] 		= '';
			$data['searchValue'] 		= '';
		endif;

		if($this->input->get('fromDate')):
			$fromDate = $this->input->get('fromDate');
			$hours = $this->input->get('hours');
			if($hours == '00:00'):
				$data['fromDate'] 				=   date('Y-m-d', strtotime($fromDate));
				$whereCon['where']["created_at"]  = array('$gte' => $data['fromDate'].' 00:01' , '$lte' => $data['fromDate'].' 23:59') ;
			else:
				$whereCon['where']["created_at"]  = array('$eq' => $data['fromDate']) ;
			endif;

			$data['fromDate'] =   $fromDate;
		endif;
		if(empty($data['isOrderSearch'])):
			$whereCon['where']['is_winner'] = "Y";
		endif;

		$baseUrl 							= 	getCurrentControllerPath('index');
		$this->session->set_userdata('ALLLHOURLYGAMEDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$shortField                         = array('_id' => -1);
		$tblName                            = 'uw_hourly_orders';

		if($data['isOrderSearch']):
			$countWhere = $whereCon;
			$totalRows = $this->common_model->getHourlyGameOrderData('count', $tblName, $countWhere, $shortField, 0, 0);
			if(!is_numeric($totalRows)):
				$totalRows = 0;
			endif;
		else:
			$resultType   = 'count';
			$totalRows    = $this->common_model->getHourlyGameGroupByData($whereCon, $resultType);
		endif;

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

		if($data['isOrderSearch']):
			$pageData = ($data['perpage'] == 'All') ? $totalRows : $data['perpage'];
			$allRows = $totalRows
				? $this->common_model->getHourlyGameOrderData('multiple', $tblName, $whereCon, $shortField, $pageData, $page)
				: array();
		else:
			$resultType = "multiple";
			$allRows = $this->common_model->getHourlyGameGroupByData($whereCon,$resultType);
			if(!empty($allRows) && is_array($allRows)):
				usort($allRows, function($a, $b){
					$aTime = isset($a['winner_uploaded_at']) ? $a['winner_uploaded_at'] : (isset($a['created_at']) ? $a['created_at'] : 0);
					$bTime = isset($b['winner_uploaded_at']) ? $b['winner_uploaded_at'] : (isset($b['created_at']) ? $b['created_at'] : 0);

					$aTs = is_numeric($aTime) ? (int)$aTime : strtotime((string)$aTime);
					$bTs = is_numeric($bTime) ? (int)$bTime : strtotime((string)$bTime);

					return $bTs <=> $aTs;
				});
			endif;
			$totalRows = !empty($allRows) && is_array($allRows) ? count($allRows) : 0;
			$pageData = ($data['perpage'] == 'All') ? $totalRows : $data['perpage'];
			$allRows = $totalRows ? array_slice($allRows, (int)$page, (int)$pageData) : array();
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
		
		$data['ALLDATA'] = !empty($allRows) ? $allRows : array();

		$this->layouts->set_title('Hourly Game Winner List | UWINN');
		$this->layouts->admin_view('hourlygame/allhourlygamewinner/index',array(),$data);
	}
	// public function index()
	// {	
	// 	$this->admin_model->authCheck('view_data');
	// 	$data['error'] 		   = '';
	// 	$data['activeMenu']    = 'hourlygame';
	// 	$data['activeSubMenu'] = 'hourlygamewinner';
		
	// 	if($this->input->get('searchField') && $this->input->get('searchValue')):
	// 		$sField				  = $this->input->get('searchField');
	// 		$sValue				  = $this->input->get('searchValue');
	// 		$data['searchField']  = $sField;
	// 		$data['searchValue']  = $sValue;
	// 		$whereCon['where'][$sField] = is_numeric($sValue)?(int)$sValue:$sValue;
	// 	else:
	// 		$data['searchField'] 		= '';
	// 		$data['searchValue'] 		= '';
	// 	endif;

	// 	if($this->input->get('fromDate')):
	// 		$fromDate = $this->input->get('fromDate');
	// 		$hours = $this->input->get('hours');
	// 		if($hours == '00:00'):
	// 			$data['fromDate'] 				=   date('Y-m-d', strtotime($fromDate));  //2023-03-16 15:13
	// 			$whereCon['where']["created_at"]  = array('$gte' => $data['fromDate'].' 00:01' , '$lte' => $data['fromDate'].' 23:59') ;
	// 		else:
	// 			$whereCon['where']["created_at"]  = array('$eq' => $data['fromDate']) ;
	// 		endif;

	// 		$data['fromDate'] =   $fromDate;
	// 	endif;
	// 	$whereCon['where']['is_winner'] = "Y";
		
	// 	// $shortField 						= 	array('_id'=> -1);
	// 	$shortField 						= 	array('winner_uploaded_at'=> -1, '_id'=> -1);
	// 	$baseUrl 							= 	getCurrentControllerPath('index');
	// 	$this->session->set_userdata('ALLLHOURLYGAMEDATA',currentFullUrl());
	// 	$qStringdata						=	explode('?',currentFullUrl());
	// 	$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
	// 	$con 								= 	'';
	// 	$resultType   = 'count'; 
	// 	$totalRows     				   =  $this->common_model->getHourlyGameGroupByData($whereCon,$resultType,$shortField,$page,$pageData);

	// 	if($this->input->get('showLength') == 'All'):
	// 		$perPage	 					= 	$totalRows;
	// 		$data['perpage'] 				= 	$this->input->get('showLength');  
	// 	elseif($this->input->get('showLength')):
	// 		$perPage	 					= 	$this->input->get('showLength'); 
	// 		$data['perpage'] 				= 	$this->input->get('showLength'); 
	// 	else:
	// 		$perPage	 					= 	SHOW_NO_OF_DATA;
	// 		$data['perpage'] 				= 	SHOW_NO_OF_DATA; 
	// 	endif;

	// 	$uriSegment 						= 	getUrlSegment();
	// 	$data['PAGINATION']					=	adminPagination($baseUrl,$suffix,$totalRows,$perPage,$uriSegment);

    //    if($this->uri->segment(getUrlSegment())):
    //        $page = $this->uri->segment(getUrlSegment());
    //    else:
    //        $page = 0;
    //    endif;
		
	// 	$data['forAction'] 					= 	$baseUrl; 
	// 	if($totalRows):
	// 		$first							=	(int)($page)+1;
	// 		$data['first']					=	$first;
			
	// 		if($data['perpage'] == 'All'):
	// 			$pageData 					=	$totalRows;
	// 		else:
	// 			$pageData 					=	$data['perpage'];
	// 		endif;
			
	// 		$last							=	((int)($page)+$pageData)>$totalRows?$totalRows:((int)($page)+$pageData);
	// 		$data['noOfContent']			=	'Showing '.$first.'-'.$last.' of '.$totalRows.' items';
	// 	else:
	// 		$data['first']					=	1;
	// 		$data['noOfContent']			=	'';
	// 	endif;
		
	// 	$resultType = "multiple";
	// 	$data['ALLDATA']     				= $this->common_model->getHourlyGameGroupByData($whereCon,$resultType,$shortField,$page,$pageData);
	// 	// echo '<pre>';print_r($data);die();

	// 	$this->layouts->set_title('Hourly Game Winner List | UWINN');
	// 	$this->layouts->admin_view('hourlygame/allhourlygamewinner/index',array(),$data);
	// }	// END OF FUNCTION

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name 	: index
	 + + Developed By 	: Dilip Halder
	 + + Purpose  		: This function used for index
	 + + Date 			: 27 January 2024
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	 public function checkpreview()
	 {	
		$this->admin_model->authCheck('view_data');
		$data['error'] 		   = '';
		$data['activeMenu']    = 'hourlygame';
		$data['activeSubMenu'] = 'hourlygamewinner';
	
		$this->session->set_userdata('ALLLHOURLYGAMEDATA',currentFullUrl('index'));
		$DataArray    = array();
		$uploadedFile = $_FILES["csvFile"]["tmp_name"];
		if (($open = fopen($uploadedFile, "r")) !== false  && !empty($_FILES["csvFile"])):
			while(($data = fgetcsv($open, 1000, ",")) !== false):
				$DataArray[] = $data;
			endwhile;
			fclose($open);
			$result = [];
			$param['batch_id'] = (int)$this->common_model->getNextSequence('uw_hourly_count');
			foreach ($DataArray as $itemkey => $itemArray):
				if($itemkey == 0):
					$FindColumn      		= 'TICKET ID';
					$orderIndex      		= array_search($FindColumn, $itemArray);
				 
					$FindColumn      		= 'COUPONS';
					$couponsIndex   		= array_search($FindColumn, $itemArray);

					$FindColumn      		= 'MATCHING NUMBER';
					$machingIndex     		= array_search($FindColumn, $itemArray);
					
					$FindColumn      		= 'WINNER TYPE';
					$winnerTypeIndex     	= array_search($FindColumn, $itemArray);

					$FindColumn      		= 'WINNING AMOUNT';
					$WinningAmountIndex     = array_search($FindColumn, $itemArray);


				else:
					$COUPON          = $itemArray[$couponsIndex]?$itemArray[$couponsIndex]:'';
					$MATCHING_COUPON = $itemArray[$machingIndex]?$itemArray[$machingIndex]:'';

					$couponCount = count(explode(',', $COUPON));
					$param['order_id']  	  =	$itemArray[$orderIndex]?$itemArray[$orderIndex]:'N/A';
					$param['coupon_code']  	  =	$itemArray[$couponsIndex]?$itemArray[$couponsIndex]:'N/A';
					$param['matching_coupon'] =	$MATCHING_COUPON?$MATCHING_COUPON:'N/A';
					$param['winner_type'] 	  =	$itemArray[$winnerTypeIndex]?$itemArray[$winnerTypeIndex]:'N/A';;
					$param['winning_amount']  =	$itemArray[$WinningAmountIndex]?$itemArray[$WinningAmountIndex]:'N/A';
			        $param['csv_name'] 		  = $_FILES['csvFile']['name'];
					array_push($result, $param);
				endif;
			endforeach;
		else:
			$this->session->set_flashdata('alert_error','Please upload a valid CSV file');
			redirect(correctLink('ALLLHOURLYGAMEDATA',getCurrentControllerPath('index')));
		endif;
		$data['ALLDATA']  =  $result;
		$this->layouts->set_title('Hourly Game Winner Check Preview | UWINN');
		$this->layouts->admin_view('hourlygame/allhourlygamewinner/checkpreview',array(),$data);
	 }


	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name 	: uploadVoucher
	 + + Developed By 	: Dilip Halder
	 + + Purpose  		: This function used to upload vouchers..
	 + + Date 			: 28 January 2024
	 
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
 	public function uploadVoucher()
	{
	 	$this->admin_model->authCheck('add_data');
		// Support direct batch upload from `checkpreview.php` (chunked by 500 rows).
		$orderIds = $this->input->post('order_id');
		$uploadCount = 0;
		if(is_array($orderIds) && !empty($orderIds)):
			$batchIds        = $this->input->post('batch_id');
			$csvNames        = $this->input->post('csv_name');
			$couponCodes     = $this->input->post('coupon_code');
			$matchingCoupons = $this->input->post('matching_coupon');
			$winnerTypes     = $this->input->post('winner_type');
			$winningAmounts  = $this->input->post('winning_amount');

			$groupedWinners = array();
			for($i = 0; $i < count($orderIds); $i++):
				$order_id = $orderIds[$i];
				if(empty($order_id)):
					continue;
				endif;

				$batch_id    	 = isset($batchIds[$i])        ? (int)$batchIds[$i] 	      : 	0;
				$csv_name    	 = isset($csvNames[$i])        ? (string)$csvNames[$i] 		  : 	'';
				$coupon_code 	 = isset($couponCodes[$i])     ? (string)$couponCodes[$i] 	  : 	'';
				$matching_coupon = isset($matchingCoupons[$i]) ? (string)$matchingCoupons[$i] : 	'';
				$winner_type     = isset($winnerTypes[$i])     ? (string)$winnerTypes[$i]     : 	'N/A';
				$amount          = isset($winningAmounts[$i])  ? (float)$winningAmounts[$i]   : 	0;

				if(!isset($groupedWinners[$order_id])):
					$groupedWinners[$order_id] = array(
						'batch_id'        => $batch_id,
						'csv_name'        => $csv_name,
						'total_amount'    => 0,
						'winning_details' => array()
					);
				endif;

				$groupedWinners[$order_id]['total_amount'] += $amount;
				$groupedWinners[$order_id]['winning_details'][] = array(
					'coupon_code'      => $coupon_code,
					'matching_coupons' => $matching_coupon,
					'winner_type'      => $winner_type,
					'winning_amount'   => $amount,
					'batch_id'         => $batch_id,
					'csv_name'         => $csv_name,
					'status'           =>  'A'
				);
			endfor;

			foreach($groupedWinners as $order_id => $winnerInfo):
				$allCouponCodes = array();
				$allMatchingCoupons = array();
				foreach($winnerInfo['winning_details'] as $winningDetail):
					$allCouponCodes[] = isset($winningDetail['coupon_code']) ? $winningDetail['coupon_code'] : '';
					$allMatchingCoupons[] = isset($winningDetail['matching_coupons']) ? $winningDetail['matching_coupons'] : (isset($winningDetail['matching_coupon']) ? $winningDetail['matching_coupon'] : '');
				endforeach;

				$updateParam = [
					'coupon_code'        => implode(' / ', $allCouponCodes),
					'matching_coupons'   => implode(' / ', $allMatchingCoupons),
					'csv_name'           => $winnerInfo['csv_name'],
					'winner_type'        => isset($winnerInfo['winning_details'][0]['winner_type']) ? $winnerInfo['winning_details'][0]['winner_type'] : 'N/A',
					'winning_amount'     => $winnerInfo['total_amount'],
					'winning_details'    => $winnerInfo['winning_details'],
					'batch_id'			 => $winnerInfo['batch_id'],
					'is_winner'			 => 'Y',
					'winning_status'	 => 'unpaid',
					'winner_uploaded_at' => strtotime(date('Y-m-d H:i:s'))
				];

				$this->common_model->editData('uw_hourly_orders', $updateParam, 'order_id', $order_id);

				// Send winner notification (picked up by push cron via push_status=0)
				$orderWhere = array('where' => array('order_id' => $order_id));
				$orderWhere['select'] = array('order_id', 'users_id', 'winning_amount', 'products_name');
				$orderData = $this->common_model->getData('single', 'uw_hourly_orders', $orderWhere);
				if(!empty($orderData) && !empty($orderData['users_id'])):
					$winAmount = number_format((float)$winnerInfo['total_amount'], 2);
					$gameName  = !empty($orderData['products_name']) ? $orderData['products_name'] : 'Hourly Game';
					$title     = 'Congratulations';
					$message   = 'Congratulations! You have won '.$winAmount.' AED prize in '.$gameName.' for your order: '.$order_id;
					$this->common_model->saveNotifications((int)$orderData['users_id'], $title, $message, $order_id);
				endif;

				$uploadCount++;
			endforeach;
		endif;

		echo json_encode([
		    'status' => true,
		    'message' => $uploadCount . " order(s) uploaded successfully."
		]);	
		die();
	}



	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name 	: view
	 + + Developed By 	: Dilip Halder
	 + + Purpose  		: This function used for index
	 + + Date 			: 19 January 2024
	 + + Updated By 	: Dilip Halder
	 + + Updated Date 	: 26 February 2024.
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	 public function addeditdata($bid='')
	 {	
		$this->admin_model->authCheck('view_data');
		$data['error'] 		   = '';
		$data['activeMenu']    = 'hourlygame';
		$data['activeSubMenu'] = 'allhourlygamewinner';

		$searchField   = $this->input->get('searchField');
		$searchValue   = $this->input->get('searchValue');
		$whereCon['where']['batch_id']      = (int)$bid;
		if(!empty($searchField) && !empty($searchValue)):
			if($searchField == 'status'):
				$whereCon['where']['winning_status'] = 	strtolower($searchValue);
			else:
				$whereCon['where'][$searchField] = is_numeric($searchValue)?(int)$searchValue:$searchValue;
			endif;
		endif;
		 

		$data['searchField'] 			= $searchField;
		$data['searchValue'] 			= $searchValue;
		$data['fromDate'] 				= $fromDate;  
		$data['toDate'] 				= $toDate;

		$shortField 						= array('_id'=> -1);
		$baseUrl 							= getCurrentControllerPath('addeditdata/'.$bid);
		$this->session->set_userdata('ALLLHOURLYGAMEDATA',currentFullUrl());
		$qStringdata						= explode('?',currentFullUrl());
		$suffix								= $qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 'uw_hourly_orders';
		$con 								=  '';
		$totalRows 							= $this->common_model->getData('count',$tblName,$whereCon);

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

		$uriSegment 						= 	5;
	    $data['PAGINATION']					=	adminPagination($baseUrl,$suffix,$totalRows,$perPage,$uriSegment);

       if($this->uri->segment(getUrlSegment())):
		   $page = $this->uri->segment(5);
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
	 
		$data['ALLDATA']  = $this->common_model->getHourlyGameOrderData('multiple',$tblName,$whereCon,$shortField,$perPage,$page);
		// echo '<pre>';print_r($data);die();


		$this->layouts->set_title('Hourly Game Winner Uploaded | UWINN');
		$this->layouts->admin_view('hourlygame/allhourlygamewinner/view_index',array(),$data);
	 }	// END OF FUNCTION

	/***********************************************************************
	** Function name 	: changestatus
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for change status
	** Date 			: 21 January 2024
	************************************************************************/
	function changestatus($changeStatusId='',$statusType='')
	{   
		// checking editing permission..
		$this->admin_model->authCheck('edit_data');
	 	$param['winning_status']  = $statusType== 'I' ? 'Inactive' : 'unpaid';
		$whereCon = array('_id' => new MongoDB\BSON\ObjectID($changeStatusId));
		$this->common_model->editDataByMultipleCondition('uw_hourly_orders',$param,$whereCon);
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		redirect(correctLink('ALLLHOURLYGAMEDATA',getCurrentControllerPath('addeditdata/'.$bid)));
	}
 
	/***********************************************************************
	** Function name 	: changestatus
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for change status
	** Date 			: 21 January 2024
	************************************************************************/
	function changestatusbatch($changeStatusId='',$statusType='')
	{   
		// checking editing permission..
		$this->admin_model->authCheck('edit_data');
		//Updating status
		$tblName = 'uw_hourly_orders';
		$whereCon['batch_id']       = (int)$changeStatusId;
		$whereCon['winning_status'] = array('$in' => array('unpaid', 'inactive','Inactive'));
		$param['winning_status']    = (string)$statusType== 'N' ? 'Inactive' : 'unpaid';
		// echo '<pre>';print_r($whereCon);die();
		$this->common_model->editMultipleDataByMultipleCondition($tblName,$param,$whereCon);
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		redirect(correctLink('ALLLHOURLYGAMEDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name 	: deletedata
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for delete data
	** Date 			: 21 May 2024
	************************************************************************/
	function deletebatchdata($batchId='')
	{  
		// $this->admin_model->authCheck('delete_data');
		// $param['soft_delete'] = (int)1;
		// $this->common_model->deleteParticularData('uw_uwin_winner','batch_id' ,(int)$batchId);
		// $this->session->set_flashdata('alert_success',lang('deletesuccess'));
		// redirect(correctLink('ALLLHOURLYGAMEDATA',getCurrentControllerPath('index')));

		$this->admin_model->authCheck('edit_data');
	 	$param['status'] 	  = (int)'0';
	 	$param['soft_delete'] = (int)'1';
		//Updating status
		$tblName1 			 = 'uw_uwin_winner';
		$whereCon = array('batch_id' => (int)$batchId);
		$this->common_model->editMultipleDataByMultipleCondition('uw_uwin_winner',$param,$whereCon);
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		redirect(correctLink('ALLLHOURLYGAMEDATA',getCurrentControllerPath('index')));
	}


	/***********************************************************************
	** Function name 	: deletedata
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for delete data
	** Date 			: 21 January 2024
	************************************************************************/
	function deletedata($deleteId='')
	{  
		$this->admin_model->authCheck('delete_data');
		$param['status'] 	  = (int)'0';
	 	$param['soft_delete'] = (int)'1';
		$this->common_model->editData('uw_uwin_winner',$param,'voucher_id' ,(int)$deleteId);
		// $this->common_model->deleteData('uw_uwin_winner','voucher_id' ,(int)$deleteId);
		$this->session->set_flashdata('alert_success',lang('deletesuccess'));
		redirect(correctLink('ALLLHOURLYGAMEDATA',getCurrentControllerPath('index')));
	}

 	

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name 	: index
	 + + Developed By 	: Dilip Halder
	 + + Purpose  		: This function used for index
	 + + Date 			: 27 January 2024
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	 public function checkInactivepreview()
	 {	
		 $this->admin_model->authCheck('view_data');
		 $data['error'] 	    = "";
		 $data['activeMenu'] 	= "hourlygame";
		 $data['activeSubMenu'] = "hourlygamewinner";
	  
		 $DataArray = array();
		 $uploadedFile = $_FILES["csvFile"]["tmp_name"];
 
		 if (($open = fopen($uploadedFile, "r")) !== false):
			 while(($data = fgetcsv($open, 1000, ",")) !== false):
				 $DataArray[] = $data;
			 endwhile;
			 fclose($open);
 
				 $result = [];
				  foreach ($DataArray as $itemkey => $itemArray):
					 if($itemkey == 0):
						  
						  $FindColumn      = 'TICKET ID';
						  $orderIndex      = array_search($FindColumn, $itemArray);
 
						  $FindColumn      = 'COUPON CODE';
						  $CouponcodeIndex = array_search($FindColumn, $itemArray);
 
						  $FindColumn      = 'Settled Amount';
						  $settledAmountIndex = array_search($FindColumn, $itemArray);
 
						  $FindColumn      = 'Draw Date';
						  $DrawDateIndex   = array_search($FindColumn, $itemArray);
 
						  $FindColumn      = 'SETTELD STATUS';
						  $SettedStatusIndex = array_search($FindColumn, $itemArray);
 
						  $FindColumn      = 'STATUS';
						  $StatusIndex     = array_search($FindColumn, $itemArray);
 
					 else:
						   $param['order_id']		    =  $itemArray[$orderIndex]?$itemArray[$orderIndex]:'N/A';
						   $param['coupon_code']	    =  $itemArray[$CouponcodeIndex]?$itemArray[$CouponcodeIndex]:'N/A';
						   $param['settled_amount']    =  $itemArray[$settledAmountIndex]?$itemArray[$settledAmountIndex]:'N/A';
						   $param['draw_date'] 		=  $itemArray[$DrawDateIndex]?$itemArray[$DrawDateIndex]:'N/A';
						   $param['setted_status'] 	=  $itemArray[$SettedStatusIndex]?$itemArray[$SettedStatusIndex]:'N/A';
						   $param['setted_status'] 	=  $itemArray[$SettedStatusIndex]?$itemArray[$SettedStatusIndex]:'N/A';
						   $param['status'] 			=  $itemArray[$StatusIndex]?$itemArray[$StatusIndex]:'N/A';
						   array_push($result, $param);
					 endif;
				  endforeach;
			 endif;
 
			 // echo "<pre>";
			 // 	print_r($result);
			 // 	die();
		
		 $data['ALLDATA']  =  $result;
		 $this->layouts->set_title('Hourly Game Winner Uplaoding | UWINN');
		 $this->layouts->admin_view('hourlygame/allhourlygamewinner/checkInactivePreview',array(),$data);
	 }	// END OF FUNCTION
 
	/***********************************************************************
	** Function name 	: changestatusByorderID
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for changestatusByorderID status
	** Date 			: 01 August 2024
	************************************************************************/
	function changestatusByorderID()
	{   
		// checking editing permission..
		$this->admin_model->authCheck('edit_data');
	 	$whereCondition['order_id']  = $this->input->post('order_id');
	 	$param['status'] 	 		 = (int)$status;
		$tblName 			 		 = 'uw_uwin_winner';
		$this->common_model->editDataByMultipleCondition($tblName,$param,$whereCondition);
		$successMessage = $successCount . "orders inactived successfully.";
	    return $successMessage;
	}

    /***********************************************************************
	** Function name 	: exportexcel 
	** Developed By 	: Ashif Iqbal
	** Purpose  		: This function used for export winner 
	** Date 			: 07 July 2025s
	************************************************************************/
	public function exportexcel(){
		try {
			$matchStage = [];
			$fromDateStr =  date('Y-m-d H:i:00', strtotime($_POST['fromDate']));//$_POST['fromDate'];
			$toDateStr   =date('Y-m-d H:i:59', strtotime($_POST['toDate'])); //$_POST['toDate'];
			
			// If from/to date are provided, add match filter on winner.created_at
			if ($fromDateStr && $toDateStr) {
				$matchStage = [
					'$match' => [
						'status'=>1,
						'soft_delete'=>0,
						'created_at' => [
							'$gte' => $fromDateStr,
							'$lte' => $toDateStr
						]
					]
				];
			}

			// Build pipeline with optional match
			$pipeline = [];

			if (!empty($matchStage)) {
				$pipeline[] = $matchStage;
			}
			
			$pipeline[] = [
				'$lookup' => [
					'from' => 'uw_lotto_orders',
					'localField' => 'order_id',
					'foreignField' => 'order_id',
					'as' => 'order_data'
				]
			];
			$pipeline[] = [
				'$unwind' => [
					'path' => '$order_data',
					'preserveNullAndEmptyArrays' => true
				]
			];
			$pipeline[] = [
				'$lookup' => [
					'from' => 'uw_users',
					'let' => [ 'user_oid' => '$order_data.user_oid' ],
					'pipeline' => [
						[
							'$match' => [
								'$expr' => [
									'$eq' => ['$_id', [ '$toObjectId' => '$$user_oid' ]]
								]
							]
						]
					],
					'as' => 'user_data'
				]
			];
			$pipeline[] = [
				'$unwind' => [
					'path' => '$user_data',
					'preserveNullAndEmptyArrays' => true
				]
			];
			$pipeline[] = [
				'$addFields' => [
					'amount_numeric' => [ '$toDouble' => '$amount' ]
				]
			];
			$pipeline[] = [
				'$project' => [
					'_id' => 0,
					'order_id' => 1,
					'retailer' => '$seller_first_name',
					'seller_name' => '$seller_last_name',
					'product_title'=>[
						'$ifNull' => ['$order_data.product_title', 'N/A']
					],
					'status' => 1,
					'amount'=>1,
					'created_at' => [
						'$ifNull' => ['$order_data.created_at', 'N/A']
					],
					'draw_date' => [
						'$ifNull' => ['$order_data.draw_date', 'N/A']
					],
					'bind_person_name' => [
						'$ifNull' => ['$user_data.bind_person_name', 'N/A']
					],
					'pos_number' => [
						'$ifNull' => ['$user_data.pos_number', 'N/A']
					]
				]
			];
		
		// Filter by date
		$pipeline[] = [
			'$sort' => [
				'amount' => -1
			]
		];
		$winnerData = $this->mongo_db->aggregate('uw_uwin_winner', $pipeline, ['batchSize' => 4]);
		usort($winnerData, function ($a, $b) {
			// Ensure both are treated as numbers
			return (float)$b['amount'] <=> (float)$a['amount'];
		});

		// echo$fromDateStr.'---'.$toDateStr."<pre>";print_r($winnerData);die();
		// dd($winnerData);
		require_once FCPATH . 'vendor/psr/simple-cache/src/CacheInterface.php';
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'SL.NO');
		$sheet->setCellValue('B1', 'ORDER ID');
		$sheet->setCellValue('C1', 'RETAILER');
		$sheet->setCellValue('D1', 'POS NUMBER');
		$sheet->setCellValue('E1', 'DRAW DATE');
		$sheet->setCellValue('F1', 'GAME NAME');
		$sheet->setCellValue('G1', 'PRIZE MONEY');
		$sheet->setCellValue('H1', 'PURCHASE DATE');
		$sheet->setCellValue('I1', 'AREA');
		$sheet->setCellValue('J1', 'BIND WITH');
		$slno = 1;
		$start = 2;
		foreach ($winnerData as $key => $d) {
			$sheet->setCellValue('A'.$start, $slno);
			$sheet->setCellValue('B'.$start, $d['order_id']);
			$sheet->setCellValue('C'.$start, ucwords( $d['retailer']));
			$sheet->setCellValue('D'.$start, $d['pos_number']);
			$sheet->setCellValue('E'.$start,$d['draw_date']);
			$sheet->setCellValue('F'.$start, $d['product_title']);
			$sheet->setCellValue('G'.$start, $d['amount']);
			$sheet->setCellValue('H'.$start, $d['created_at']);
			$sheet->setCellValue('I'.$start, $d['seller_name']);
			$sheet->setCellValue('J'.$start, $d['bind_person_name']);
			$start = $start+1;
			$slno = $slno+1;
		}
		$styleThinBlackBorderOutline = [
					'borders' => [
						'allBorders' => [
							'borderStyle' => Border::BORDER_THIN,
							'color' => ['argb' => 'FF000000'],
						],
					],
				];
		$sheet->getStyle('A1:J1')->getFont()->setBold(true);		
		$sheet->getStyle('A1:J'.count($winnerData))->applyFromArray($styleThinBlackBorderOutline);
		//Alignment
		//fONT SIZE
		$sheet->getStyle('A1:J10')->getFont()->setSize(12);
		$sheet->getStyle('A1:J2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
		$sheet->getStyle('A2:J'.count($winnerData))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		$sheet->getColumnDimension('A')->setWidth(5);
		$sheet->getColumnDimension('B')->setWidth(15);
		$sheet->getColumnDimension('C')->setWidth(30);
		$sheet->getColumnDimension('D')->setWidth(30);
		$sheet->getColumnDimension('E')->setWidth(15);
		$sheet->getColumnDimension('F')->setWidth(15);
		$sheet->getColumnDimension('G')->setWidth(15);
		$sheet->getColumnDimension('H')->setWidth(30);
		$sheet->getColumnDimension('I')->setWidth(30);
		$sheet->getColumnDimension('J')->setWidth(30);
		$curdate = date('d-m-Y H:i:s');
		$writer = new Xlsx($spreadsheet);
		$filename = 'Big Winners '.$_POST['fromDate'].'__'.$_POST['toDate'];
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
		} catch (\Throwable $th) {
			//throw $th;
		}
	}
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Voucher extends CI_Controller {

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
	 + + Date 			: 19 January 2024
	 + + Updated By 	: Dilip Halder
	 + + Updated Date 	: 26 February 2024.
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index()
	{	
		$this->admin_model->authCheck('view_data');
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'uwin';
		$data['activeSubMenu'] 				= 	'voucher';
		
		if($this->input->get('searchField') && $this->input->get('searchValue')):

			$sField							= $this->input->get('searchField');
			$sValue							= $this->input->get('searchValue');
			$data['searchField'] 			= $sField;
			$data['searchValue'] 			= $sValue;

			if($sField =='status'):
				if($sValue == 'Inactive'):
					$sFieldState = (int)"0";
				else:
					$sFieldState = (int)"1";
				endif;
				$whereCon['where'][$sField]	    = $sFieldState;
			else:
				if(is_numeric($sValue)):
				 $whereCon['where'][$sField]	    = (int)$sValue;
				else:
				 $whereCon['where'][$sField]	    = $sValue;
				endif;
			endif;
		else:
			$data['searchField'] 				= '';
			$data['searchValue'] 				= '';
		endif;

		if($this->input->get('fromDate')):
			$fromDate = $this->input->get('fromDate');
			$hours = date('H:i',strtotime($fromDate));
			if($hours == '00:00'):
				$data['fromDate'] 				=   date('Y-m-d', strtotime($fromDate));  //2023-03-16 15:13
				$whereCon['where']["created_at"]  = array('$gte' => $data['fromDate'].' 00:01' , '$lte' => $data['fromDate'].' 23:59') ;
			else:
				$whereCon['where']["created_at"]  = array('$eq' => $data['fromDate']) ;
			endif;

			$data['fromDate'] =   $fromDate;
		endif;


		// echo "<pre>";
		// print_r($whereCon);
		// die();

		
		$shortField 						= 	array('_id'=> -1);
		$baseUrl 							= 	getCurrentControllerPath('index');
		$this->session->set_userdata('ALLUWINVOUCHERDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$con 								= 	'';
		$resultType   = 'count';
		$totalRows    = $this->geneal_model->GetWinnerGroupByData($whereCon,$resultType);
		// echo "<pre>";print_r($totalRows);die();
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
		// echo $page;
		// echo '<br/>';
		// echo $pageData;die();
		$resultType = "";
		$data['ALLDATA']     				= $this->geneal_model->GetWinnerGroupByData($whereCon,$resultType,$page,$pageData);

		// echo "<pre>";
		// print_r($data['ALLDATA']);
		// die();

		$this->layouts->set_title('UWinn Winner Uploading | UWINN');
		$this->layouts->admin_view('uwin/voucher/index',array(),$data);
	}	// END OF FUNCTION


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
		$this->admin_model->authCheck('edit_data');
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'uwin';
		$data['activeSubMenu'] 				= 	'voucher';


		if(empty($bid)):
			$this->session->set_flashdata('alert_error',lang('Empty_Batch_ID'));
			redirect(correctLink('ALLUWINVOUCHERDATA',getCurrentControllerPath('index')));
		endif;

		if($this->input->get('searchField') && $this->input->get('searchValue')):
			$sField							= $this->input->get('searchField');
			$sValue							= $this->input->get('searchValue');
			$data['searchField'] 			= $sField;
			$data['searchValue'] 			= $sValue;

			if($sField =='status'):
				if($sValue == 'Inactive'):
					$sFieldState = (int)"0";
				else:
					$sFieldState = (int)"1";
				endif;
				$whereCon['where']				= array($sField => $sFieldState);
			else:
				$whereCon['like']			 	= array('0'=>trim($sField),'1'=>trim($sValue));
			endif;
		else:
			$data['searchField'] 				= '';
			$data['searchValue'] 				= '';
			$whereCon['where']					= array('soft_delete'=>array('$ne'=>1));
		endif;

		$whereCon['where']					= array('batch_id'=> (int)$bid);

		if($this->input->get('fromDate')):
			$data['fromDate'] 				=   date('Y-m-d 00:01', strtotime($this->input->get('fromDate')));  //2023-03-16 15:13
			$whereCon['where_gte'] 			= 	array(array("created_at",$data['fromDate']));
		endif;

		$whereCon11['where'] = array('batch_id'=> (int)$bid);
		$resultType = "";
		$data['batchData']     				= $this->geneal_model->GetWinnerGroupByData($whereCon11,$resultType,$page,$pageData);

		$shortField 						= 	array('_id'=> -1);
		$baseUrl 							= 	getCurrentControllerPath('addeditdata/'.$bid);
		$this->session->set_userdata('ALLUWINVOUCHERDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 	'uw_uwin_winner';
		$resultType   = 'count';
		$totalRows    = $this->common_model->getData('count',$tblName,$whereCon,$shortField,'0','0');
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
		// echo $baseUrl.'------'.$suffix.'-----------------'.$totalRows.'------------'.$perPage.'---------------'.$uriSegment;die();
	    $data['PAGINATION']					=	adminPagination($baseUrl,$suffix,$totalRows,$perPage,$uriSegment);

       if($this->uri->segment(5)):
           $page = $this->uri->segment(5);
       else:
           $page = 0;
       endif;
	//    $data['forAction'] 					= 	$baseUrl; 
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
	    $data['forAction'] 					= 	getCurrentControllerPath('addeditdata').'/'.$bid; 
	    $tblName 							= 	'uw_uwin_winner';
	    // $data['ALLDATA'] 					= 	$this->common_model->getData('multiple',$tblName,$whereCon,$shortField);
		$data['ALLDATA'] 					= $this->common_model->getData('multiple',$tblName,$whereCon,$shortField,$perPage,$page);
		$this->layouts->set_title('UWinn Winner Uploading | UWINN');
		$this->layouts->admin_view('uwin/voucher/view_index',array(),$data);
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
	 	$param['status'] 	 = (int)$statusType;
		$whereCon = array('_id' =>  new \MongoDB\BSON\ObjectId($changeStatusId));
		$this->common_model->editMultipleDataByMultipleCondition('uw_uwin_winner',$param,$whereCon);
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		redirect(correctLink('ALLUWINVOUCHERDATA',getCurrentControllerPath('index')));
	}
	public function multiplechangestatus()
{
    // Permission check
    $this->admin_model->authCheck('edit_data');

    // Read JSON input from frontend
    $json = file_get_contents('php://input');
    $voucherList = json_decode($json, true);

    if (!is_array($voucherList) || empty($voucherList)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
        return;
    }

    // Extract all voucher_ids
    $voucher_ids = array_column($voucherList, 'voucher_id');
    $status = $voucherList[0]['status'] ?? null;

    if (empty($voucher_ids) || $status === null) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Missing voucher_id or status']);
        return;
    }

    // Define update and condition
    $param = ['status' => (int)$status];
    $whereCon = ['voucher_id' => ['$in' => array_map('intval', $voucher_ids)]];

    // Use your existing reusable function
    $this->common_model->editMultipleDataByMultipleCondition('uw_uwin_winner', $param, $whereCon);

    // Return response
    echo json_encode(['status' => 'success', 'message' => 'Status updated for selected vouchers']);
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
	 	$param['status'] 	 = (int)$statusType;
	 	// $soft_delete 		 = ($statusType == 1) ? 0 : 1 ;
	 	// $param['soft_delete']= (int)$soft_delete;
		//Updating status
		$tblName1 			 = 'uw_uwin_winner';
		$whereCon = array('batch_id' => (int)$changeStatusId);
		$this->common_model->editMultipleDataByMultipleCondition('uw_uwin_winner',$param,$whereCon);
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		redirect(correctLink('ALLUWINVOUCHERDATA',getCurrentControllerPath('index')));
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
		// redirect(correctLink('ALLUWINVOUCHERDATA',getCurrentControllerPath('index')));

		$this->admin_model->authCheck('edit_data');
	 	$param['status'] 	  = (int)'0';
	 	$param['soft_delete'] = (int)'1';
		//Updating status
		$tblName1 			 = 'uw_uwin_winner';
		$whereCon = array('batch_id' => (int)$batchId);
		$this->common_model->editMultipleDataByMultipleCondition('uw_uwin_winner',$param,$whereCon);
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		redirect(correctLink('ALLUWINVOUCHERDATA',getCurrentControllerPath('index')));
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
		redirect(correctLink('ALLUWINVOUCHERDATA',getCurrentControllerPath('index')));
	}

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
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'sub_winners';
		$data['activeSubMenu'] 				= 	'voucher';
	 
		$DataArray = array();
		$uploadedFile = $_FILES["csvFile"]["tmp_name"];
		if (($open = fopen($uploadedFile, "r")) !== false):
		    while(($data = fgetcsv($open, 1000, ",")) !== false):
		        $DataArray[] = $data;
		    endwhile;
		    fclose($open);
			$result = [];
			$param['batch_id'] 			= (int)$this->common_model->getNextSequence('lotto_winners');
			foreach ($DataArray as $itemkey => $itemArray):
				if($itemkey == 0):
						
					$FindColumn      		= 'TICKET ID';
					$orderIndex      		= array_search($FindColumn, $itemArray);

					$FindColumn      		= 'SELLER FIRST NAME';
					$sellerFirstNameIndex 	= array_search($FindColumn, $itemArray);

					$FindColumn      		= 'SELLER LAST NAME';
					$sellerLastNameIndex 	= array_search($FindColumn, $itemArray);

					$FindColumn      		= 'SELLER MOBILE';
					$sellerMobileIndex 		= array_search($FindColumn, $itemArray);

					$FindColumn      		= 'COUPONS';
					$couponsIndex   		= array_search($FindColumn, $itemArray);

					$FindColumn      		= 'STRAIGHT AMOUNT';
					$straightAmountIndex 	= array_search($FindColumn, $itemArray);

					$FindColumn      		= 'RUMBLE AMOUNT';
					$rumbleAmountIndex     	= array_search($FindColumn, $itemArray);

					$FindColumn      		= 'CHANCE AMOUNT';
					$chanceAmountIndex     	= array_search($FindColumn, $itemArray);

					$FindColumn      		= 'WINNER TYPE';
					$winnerTypeIndex     	= array_search($FindColumn, $itemArray);

					$FindColumn      		= 'WINNING AMOUNT';
					$WinningAmountIndex     = array_search($FindColumn, $itemArray);

					$FindColumn      		= 'MATCHING NUMBER';
					$machingIndex     		= array_search($FindColumn, $itemArray);

				else:
					$COUPON = $itemArray[$couponsIndex]?$itemArray[$couponsIndex]:'';
					$couponCount = count(explode(',', $COUPON));

					$param['order_id']		    	=	$itemArray[$orderIndex]?$itemArray[$orderIndex]:'N/A';
					$param['csv_name'] 		     	=	$_FILES['csvFile']['name'];

					// $param['seller_first_name'] 	 =	$itemArray[$sellerFirstNameIndex]? $itemArray[$sellerFirstNameIndex] :'N/A';
					// $param['seller_last_name']  	 =	$itemArray[$sellerLastNameIndex]?$itemArray[$sellerLastNameIndex]:'N/A';
					$param['seller_first_name'] = isset($itemArray[$sellerFirstNameIndex])? mb_convert_encoding($itemArray[$sellerFirstNameIndex], 'UTF-8', 'UTF-8')
						: 'N/A';

					$param['seller_last_name'] = isset($itemArray[$sellerLastNameIndex]) ? mb_convert_encoding($itemArray[$sellerLastNameIndex], 'UTF-8', 'UTF-8')
						: 'N/A';

					$param['seller_mobile']  	 	 =	$itemArray[$sellerMobileIndex]?$itemArray[$sellerMobileIndex]:'';
					$param['code']  	 	 		 =	$itemArray[$machingIndex]?$itemArray[$machingIndex]:'0';
					$param['coupons'] 				 =	$COUPON;
					$param['amount'] 			     =	$itemArray[$WinningAmountIndex]?$itemArray[$WinningAmountIndex]:'0';
					if($couponCount <= 5):
						$param['straight_add_on_amount'] =	$itemArray[$straightAmountIndex]?$itemArray[$straightAmountIndex]:'N/A';
						$param['rumble_add_on_amount']   =	$itemArray[$rumbleAmountIndex]?$itemArray[$rumbleAmountIndex]:'N/A';
						$param['reverse_add_on_amount']  =	$itemArray[$chanceAmountIndex]?$itemArray[$chanceAmountIndex]:'N/A';
						$param['winner_type']  			 =	$itemArray[$winnerTypeIndex]?$itemArray[$winnerTypeIndex]:'N/A';;
						$param['created_date'] 			 =	date('Y-m-d H:i');
						// $param['order_date'] 		     =	$formatted_date;
					else:
						$param['straight_add_on_amount'] =	'0';
						$param['rumble_add_on_amount']   =	'1';
						$param['reverse_add_on_amount']  =	'0';
						// $param['winner_type']  			 =	'Rumble Prize';
						$param['winner_type']  			 =	$itemArray[$winnerTypeIndex]?$itemArray[$winnerTypeIndex]:'N/A';;
						$param['created_date'] 			 =	date('Y-m-d H:i');
						// $param['order_date'] 		     =	$formatted_date;
					endif;
					array_push($result, $param);
				endif;
			endforeach;
		endif;
		// echo '<pre>';
		// print_r($result);
		// die();
		$datain = [
			'status'=>false,
			'data' => $result
		];
		$rr = $this->common_model->addData('temp_uw_uwin_winner', $datain);
		$data['ALLDATA']  =  $result;
		$data['temp_id']  = new MongoDB\BSON\ObjectId($rr['_id']->{'$id'});
		$this->layouts->set_title('UWinn Winner Uploading | UWINN');
		$this->layouts->admin_view('uwin/voucher/checkpreview',array(),$data);
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
		 $data['error'] 						= 	'';
		 $data['activeMenu'] 				= 	'sub_winners';
		 $data['activeSubMenu'] 				= 	'voucher';
	  
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
		 $this->layouts->set_title('UWinn Winner Uploading | UWINN');
		 $this->layouts->admin_view('uwin/voucher/checkInactivePreview',array(),$data);
	 }	// END OF FUNCTION

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
		// $input = json_decode(trim(file_get_contents('php://input')), true);
		// $batchData = isset($input['batch']) ? $input['batch'] : [];
		$temp_id =  new \MongoDB\BSON\ObjectId($this->input->post('temp_id')); // Assuming 'batch' contains an array of data entries
		$whereCon['where'] = array('_id'=> $temp_id);
		$tempdata = $this->common_model->getParticularFieldByMultipleCondition($fields, 'temp_uw_uwin_winner', $whereCon);
		// print_r($tempdata);
		$this->common_model->deleteData('temp_uw_uwin_winner','_id',$temp_id);
		$batchData =$tempdata['data'];
		// print_r($batchData);
		// die(); 
		// $dataToInsert = [];
		
		// foreach ($batchData as $data) {
		// 	// $order_id = $data['order_id'];
		// 	// $fields = 'created_at';
		// 	// $tableName = 'uw_lotto_orders';
		// 	// $OrderDate = $this->common_model->getPaticularFieldByFields($fields, $tableName, 'order_id', $order_id);
		// 	// $timestamp = strtotime(str_replace('/', '-', $OrderDate));
		// 	// $formatted_date = date('d M Y h:i A', $timestamp);

		// 	$order_id = $data['order_id'];
		// 	$fields = array('created_at', 'store_name','user_id');
		// 	$tableName = 'uw_lotto_orders';
		// 	$whereCon['where'] = array('order_id'=> $order_id);
		// 	$OrderData = $this->common_model->getParticularFieldByMultipleCondition($fields, $tableName, $whereCon);

		// 	$ufields = array('users_type');
		// 	$utableName = 'uw_users';
		// 	$uwhereCon['where'] = array('users_id'=> $OrderData['user_id']);
		// 	$UsersData = $this->common_model->getParticularFieldByMultipleCondition($ufields, $utableName, $uwhereCon);


		// 	$timestamp = strtotime(str_replace('/', '-', $OrderData['created_at']));
		// 	$formatted_date = date('d M Y h:i A', $timestamp);

		// 	// Prepare the data array for insertion
		// 	$param['voucher_id'] = (int)$this->common_model->getNextSequence('uw_uwin_winner');
		// 	$param['batch_id'] = (int)$data['batch_id'];
		// 	$param['csv_name'] = $data['csv_name'];
		// 	$param['order_id'] = $data['order_id'];
		// 	$param['seller_first_name'] = $data['seller_first_name'];
		// 	$param['seller_last_name'] = $data['seller_last_name'];
		// 	$param['store_name'] = $OrderData['store_name']?$OrderData['store_name']:$data['seller_first_name'];
		// 	$param['code'] = $data['code'];
		// 	$param['coupons'] = $data['coupons'];
		// 	$param['amount'] = $data['amount'];
		// 	$param["order_date"]  = $formatted_date;
		// 	$param["winner_type"] = $data['winner_type'];
		// 	$param['products_id'] = (int)$data['products_id'];
		// 	$param["status"] = (int)1;
		// 	$param["created_at"] = date('Y-m-d H:i:s');
		// 	$param["created_by"] = "Admin";
		// 	$param["modified_at"] = date('Y-m-d H:i:s');
		// 	$param["modified_by"] = "";
		// 	$param["creation_ip"] = $this->input->ip_address();
		// 	$param['soft_delete'] = (int)0;

		// 	$dataToInsert[] = $param;
		// 	if($UsersData['users_type'] == 'Users'){
		// 		$title = 'Congratulations';
		// 		$message = "Congratulations! You have won ".$data['amount']." AED prize for your order: ".$data['order_id'];
		// 		$this->common_model->saveNotifications($OrderData['user_id'],$title,$message,$data['order_id']);
		// 	}
			
		// }
		// // echo '<pre>';
		// // print_r($dataToInsert); die();
		// // Perform the batch insert
		// $rrr = $this->common_model->addManyData('uw_uwin_winner', $dataToInsert);
		$dataToInsert = [];
		$notificationsToInsert = [];

		// Initialize next sequence just once
		$nextVoucherId = (int) $this->common_model->getNextSequence('uw_uwin_winner');
		$now = date('Y-m-d H:i:s');
		$ip = $this->input->ip_address();

		foreach ($batchData as $index => $data) {
			$order_id = $data['order_id'];

			// Fetch order data
			$whereCon = ['where' => ['order_id' => $order_id]];
			$OrderData = $this->common_model->getParticularFieldByMultipleCondition(
				['created_at', 'store_name', 'user_id'],
				'uw_lotto_orders',
				$whereCon
			);

			// Fetch user type only if user_id exists
			$UsersData = [];
			if (!empty($OrderData['user_id'])) {
				$uwhereCon = ['where' => ['users_id' => $OrderData['user_id']]];
				$UsersData = $this->common_model->getParticularFieldByMultipleCondition(
					['users_type'],
					'uw_users',
					$uwhereCon
				);
			}

			// Format date
			$timestamp = strtotime(str_replace('/', '-', $OrderData['created_at'] ?? ''));
			$formatted_date = $timestamp ? date('d M Y h:i A', $timestamp) : '';

			// Prepare insert data
			$param = [
				'voucher_id'     => $nextVoucherId++,
				'batch_id'       => (int) $data['batch_id'],
				'csv_name'       => $data['csv_name'],
				'order_id'       => $data['order_id'],
				'seller_first_name' => $data['seller_first_name'],
				'seller_last_name'  => $data['seller_last_name'],
				'store_name'     => $OrderData['store_name'] ?? $data['seller_first_name'],
				'code'           => $data['code'],
				'coupons'        => $data['coupons'],
				'amount'         => $data['amount'],
				'order_date'     => $formatted_date,
				'winner_type'    => $data['winner_type'],
				'products_id'    => (int) $data['products_id'],
				'status'         => 1,
				'created_at'     => $now,
				'created_by'     => "Admin",
				'modified_at'    => $now,
				'modified_by'    => "",
				'creation_ip'    => $ip,
				'soft_delete'    => 0
			];

			$dataToInsert[] = $param;

			// Queue notification if needed
			if (($UsersData['users_type'] ?? '') === 'Users') {
				$notificationsToInsert[] = [
					'user_id' => $OrderData['user_id'],
					'title'   => 'Congratulations',
					'message' => "Congratulations! You have won {$data['amount']} AED prize for your order: {$data['order_id']}",
					'order_id' => $data['order_id'],
					'created_at' => $now,
				];
			} 
		}

		// Batch insert
		$rrr = $this->common_model->addManyData('uw_uwin_winner', $dataToInsert);

		// Optional: batch insert notifications
		foreach ($notificationsToInsert as $note) {
			$this->common_model->saveNotifications(
				$note['user_id'],
				$note['title'],
				$note['message'],
				$note['order_id']
			);
		}
		$successMessage = count($dataToInsert) . " items uploaded successfully.";
		 return $successMessage;
	}

	// public function uploadVoucher()
 	// {
	// 	    // $order_dates        = $this->input->post('order_date');
	// 		$order_id = $this->input->post('order_id');
	// 		$fields        = 'created_at';
	// 		$tableName     ='uw_lotto_orders';
	// 		$OrderDate     = $this->common_model->getPaticularFieldByFields($fields,$tableName,'order_id',$order_id);  
	// 		$timestamp = strtotime(str_replace('/', '-', $OrderDate));
	// 		$formatted_date = date('d M Y h:i A', $timestamp);

	// 	    // Data Storing in win_winner..
	//         $param['voucher_id']         = (int)$this->common_model->getNextSequence('uw_uwin_winner');
	//         $param['batch_id']           = (int)$this->input->post('batch_id');
	//         $param['csv_name']           = $this->input->post('csv_name');
	//         $param['order_id']           = $this->input->post('order_id');
	//         $param['seller_first_name']  = $this->input->post('seller_first_name');
	//         $param['seller_last_name']   = $this->input->post('seller_last_name');
	//         $param['code']               = $this->input->post('code');
	//         $param['amount']             = $this->input->post('amount');
	//         $param["order_date"]         = $formatted_date;
	//         $param['products_id']        = (int)$this->input->post('products_id');
	//         $param["status"]             = (int)1;
	//         $param["created_at"]         = date('Y-m-d H:i:s');
	//         $param["created_by"]         = "Admin";
	//         $param["modified_at"]        = date('Y-m-d H:i:s');
	//         $param["modified_by"]        = "";
	//         $param["creation_ip"]        = $this->input->ip_address();
	//         $param['soft_delete']        = (int)0;
			
	//         $this->common_model->addData('uw_uwin_winner', $param);
	// 	    $successMessage = $successCount . " items uploaded successfully.";
	// 	    return $successMessage;
	// }


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
			$this->admin_model->authCheck('view_data');
			
			$matchStage = [];
			$fromDateStr = date('Y-m-d H:i:00', strtotime($_POST['fromDate']));
			$toDateStr = date('Y-m-d H:i:59', strtotime($_POST['toDate']));
			
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
									'$eq' => [
										'$_id',
										[
											'$convert' => [
												'input' => '$$user_oid',
												'to' => 'objectId',
												'onError' => null,
												'onNull' => null,
											]
										]
									]
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
				'$lookup' => [
					'from' => 'uw_products_draw_records',
					'localField' => 'order_data.draw_id',
					'foreignField' => 'draw_id',
					'as' => 'draw_data'
				]
			];
			$pipeline[] = [
				'$unwind' => [
					'path' => '$draw_data',
					'preserveNullAndEmptyArrays' => true
				]
			];
			$pipeline[] = [
				'$addFields' => [
					'amount_numeric' => [
						'$convert' => [
							'input' => '$amount',
							'to' => 'double',
							'onError' => 0,
							'onNull' => 0,
						]
					],
					'_lookup_products_id' => [
						'$let' => [
							'vars' => [
								'winnerPid' => ['$ifNull' => ['$products_id', 0]],
								'orderPid' => ['$ifNull' => [
									'$order_data.products_id',
									['$ifNull' => ['$order_data.product_id', 0]],
								]],
							],
							'in' => [
								'$convert' => [
									'input' => [
										'$cond' => [
											['$gt' => [
												['$convert' => [
													'input' => '$$winnerPid',
													'to' => 'double',
													'onError' => 0,
													'onNull' => 0,
												]],
												0,
											]],
											'$$winnerPid',
											'$$orderPid',
										],
									],
									'to' => 'int',
									'onError' => 0,
									'onNull' => 0,
								],
							],
						],
					],
				]
			];
			$pipeline[] = [
				'$lookup' => [
					'from' => 'uw_products',
					'localField' => '_lookup_products_id',
					'foreignField' => 'products_id',
					'as' => 'product_data'
				]
			];
			$pipeline[] = [
				'$unwind' => [
					'path' => '$product_data',
					'preserveNullAndEmptyArrays' => true
				]
			];
			$pipeline[] = [
				'$project' => [
					'_id' => 0,
					'order_id' => 1,
					'batch_id' => 1,
					'retailer' => '$seller_first_name',
					'seller_name' => '$seller_last_name',
					'product_title' => [
						'$let' => [
							'vars' => [
								'fromOrder' => ['$ifNull' => ['$order_data.product_title', '']],
								'fromProduct' => ['$ifNull' => ['$product_data.title', '']],
							],
							'in' => [
								'$cond' => [
									['$and' => [
										['$ne' => ['$$fromOrder', '']],
										['$ne' => ['$$fromOrder', 'N/A']],
									]],
									'$$fromOrder',
									[
										'$cond' => [
											['$ne' => ['$$fromProduct', '']],
											'$$fromProduct',
											'N/A',
										],
									],
								],
							],
						],
					],
					'status' => 1,
					'amount'=>1,
					'created_at' => [
						'$ifNull' => [
							'$order_data.created_at',
							['$ifNull' => ['$created_at', 'N/A']],
						]
					],
					'draw_date' => [
						'$ifNull' => ['$draw_data.draw_date', 'N/A']
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
					'amount_numeric' => -1
				]
			];
			
			$winnerData = $this->mongo_db->aggregate('uw_uwin_winner', $pipeline, ['batchSize' => 5000]);
			$winnerData = is_array($winnerData) ? $winnerData : [];
			if (isset($winnerData[0]['cursor']['firstBatch']) && is_array($winnerData[0]['cursor']['firstBatch'])) {
				$winnerData = $winnerData[0]['cursor']['firstBatch'];
			}

			// Backfill missing GAME NAME from same batch majority title
			$batchCounts = [];
			foreach ($winnerData as $d) {
				$batchId = isset($d['batch_id']) ? (string)$d['batch_id'] : '';
				$title = trim(stripslashes((string)($d['product_title'] ?? '')));
				if ($batchId === '' || $title === '' || strcasecmp($title, 'N/A') === 0) {
					continue;
				}
				if (!isset($batchCounts[$batchId][$title])) {
					$batchCounts[$batchId][$title] = 0;
				}
				$batchCounts[$batchId][$title]++;
			}
			$batchBest = [];
			foreach ($batchCounts as $batchId => $counts) {
				arsort($counts);
				$batchBest[$batchId] = (string)key($counts);
			}
			foreach ($winnerData as &$d) {
				$title = trim(stripslashes((string)($d['product_title'] ?? '')));
				$batchId = isset($d['batch_id']) ? (string)$d['batch_id'] : '';
				if (($title === '' || strcasecmp($title, 'N/A') === 0) && $batchId !== '' && isset($batchBest[$batchId])) {
					$d['product_title'] = $batchBest[$batchId];
				} else {
					$d['product_title'] = $title !== '' ? $title : 'N/A';
				}
			}
			unset($d);

			usort($winnerData, function ($a, $b) {
				// Ensure both are treated as numbers
				return (float)($b['amount'] ?? 0) <=> (float)($a['amount'] ?? 0);
			});

			// Generate CSV export
			$filename = 'Big Winners ' . $_POST['fromDate'] . '__' . $_POST['toDate'] . '.csv';
			
			// Set headers for CSV download
			ob_end_clean();
			header('Content-Type: text/csv; charset=UTF-8');
			header('Content-Disposition: attachment;filename="' . $filename . '"');
			header('Cache-Control: max-age=0');
			
			// Add BOM for UTF-8 Excel compatibility
			echo "\xEF\xBB\xBF";
			
			// Open output stream
			$output = fopen('php://output', 'w');
			
			// Add CSV headers
			$headers = [
				'SL.NO',
				'ORDER ID',
				'RETAILER',
				'POS NUMBER',
				'DRAW DATE',
				'GAME NAME',
				'PRIZE MONEY',
				'PURCHASE DATE',
				'AREA',
				'BIND WITH'
			];
			fputcsv($output, $headers);
			
			// Add data rows
			$slno = 1;
			foreach ($winnerData as $d) {
				$row = [
					$slno,
					$d['order_id'] ?? 'N/A',
					ucwords($d['retailer'] ?? 'N/A'),
					$d['pos_number'] ?? 'N/A',
					$d['draw_date'] ?? 'N/A',
					!empty($d['product_title']) ? stripslashes($d['product_title']) : 'N/A',
					$d['amount'] ?? '0',
					$d['created_at'] ?? 'N/A',
					$d['seller_name'] ?? 'N/A',
					$d['bind_person_name'] ?? 'N/A'
				];
				fputcsv($output, $row);
				$slno++;
			}
			
			fclose($output);
			exit;
			
		} catch (\Throwable $th) {
			log_message('error', 'Export failed: ' . $th->getMessage());
			$this->session->set_flashdata('alert_error', 'Export failed: ' . $th->getMessage());
			redirect(correctLink('ALLUWINVOUCHERDATA', getCurrentControllerPath('index')));
		}
	}
}
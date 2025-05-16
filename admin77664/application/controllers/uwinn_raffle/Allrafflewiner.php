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

class Allrafflewiner extends CI_Controller {

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
	 + + Date 			: 24 January 2024
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index()
	{	
		$this->admin_model->authCheck('view_data');
		$data['error'] 						= '';
		$data['activeMenu'] 				= 'uwinn_raffle';
		$data['activeSubMenu'] 				= 'allrafflewiner';
		
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
		$totalRows    = $this->geneal_model->GetRaffleWinnerGroupByData($whereCon,$resultType);
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
		$resultType = "";
		$data['ALLDATA']     				= $this->geneal_model->GetRaffleWinnerGroupByData($whereCon,$resultType,$page,$pageData);
		// echo "<pre>";print_r($data['ALLDATA']);die();
		$this->layouts->set_title('Raffle Winner Uploading | UWINN');
		$this->layouts->admin_view('raffle/winner/index',array(),$data);
	}	// END OF FUNCTION


	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name 	: view
	 + + Developed By 	: Dilip Halder
	 + + Purpose  			: This function used for viewData
	 + + Date 				: 25 January 2025
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function addeditdata($bid='')
 	{	
		$this->admin_model->authCheck('edit_data');
		$data['error'] 			 = '';
		$data['activeMenu'] 	 	 = 'uwinn_raffle';
		$data['activeSubMenu'] 	 = 'allrafflewiner';
		// echo "<pre>";print_r($bid);die();

		if(empty($bid)):
			$this->session->set_flashdata('alert_error',lang('Empty_Batch_ID'));
			redirect(correctLink('ALLUWINVOUCHERDATA',getCurrentControllerPath('index')));
		endif;

		if($this->input->get('searchField') && $this->input->get('searchValue')):
			$sField				 = $this->input->get('searchField');
			$sValue				 = $this->input->get('searchValue');
			$data['searchField'] = $sField;
			$data['searchValue'] = $sValue;
			$whereCon['where'][trim($sField)] =  is_numeric($sValue)?(int)$sValue:$sValue;
		endif;

		if($this->input->get('fromDate')):
			$data['fromDate'] 		   = date('Y-m-d 00:01', strtotime($this->input->post('fromDate')));  //2023-03-16 15:13
			$whereCon['where_gte'] 	   = array(array("created_at",$data['fromDate']));
		endif;
		$whereCon['where']['batch_id'] = (int)$bid;

		$this->session->set_userdata('ALLUWINVOUCHERDATA',currentFullUrl());

		$shortField 						= 	array('_id'=>-1);
		
		$baseUrl 							= 	getCurrentControllerPath('addeditdata/'.$bid);
		$this->session->set_userdata('ALLLOTOPRODUCTSDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 	'uw_raffle_winner';
		$con 									= 	'';
		$totalRows 							= 	$this->common_model->getData('count',$tblName,$whereCon,$shortField,'0','0');
		// echo "<pre>";print_r($totalRows);die();
		if($this->input->get('showLength') == 'All'):
			$perPage	 						= 	$totalRows;
			$data['perpage'] 				= 	$this->input->get('showLength');  
		elseif($this->input->get('showLength')):
			$perPage	 						= 	$this->input->get('showLength'); 
			$data['perpage'] 				= 	$this->input->get('showLength'); 
		else:
			$perPage	 					  	= 	SHOW_NO_OF_DATA;
			$data['perpage'] 				= 	SHOW_NO_OF_DATA; 
		endif;

		$uriSegment 						= 	getUrlSegment();
	    $data['PAGINATION']				=	adminPagination($baseUrl,$suffix,$totalRows,$perPage,$uriSegment);

        if($this->uri->segment(5)):
           $page = $this->uri->segment(5);
        else:
           $page = 0;
        endif;
		
		$data['forAction'] 				= 	$baseUrl; 
		if($totalRows):
			$first							=	(int)($page)+1;
			$data['first']					=	$first;
			
			if($data['perpage'] == 'All'):
				$pageData 					=	$totalRows;
			else:
				$pageData 					=	$data['perpage'];
			endif;
			
			$last								=	((int)($page)+$pageData)>$totalRows?$totalRows:((int)($page)+$pageData);
			$data['noOfContent']			=	'Showing '.$first.'-'.$last.' of '.$totalRows.' items';
		else:
			$data['first']					=	1;
			$data['noOfContent']			=	'';
		endif;

		$RaffleWhereCon['where'] = array('batch_id'=> (int)$bid );
		$data['batchData']     	 = $this->geneal_model->GetRaffleWinnerGroupByData($RaffleWhereCon,$resultType,$page,$pageData);
		// echo "<pre>";print_r($data);die();
		$data['ALLDATA']     	 = $this->common_model->getData('multiple',$tblName,$whereCon,$shortField,$pageData,$page);
		$this->layouts->set_title('Raffle Winner list | UWINN');
		$this->layouts->admin_view('raffle/winner/view_index',array(),$data);
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
		//Updating status
		$tblName1 			 = 'uw_uwin_winner';
		$this->common_model->editData('uw_uwin_winner',$param,'voucher_id' , (int)$changeStatusId);
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		redirect(correctLink('ALLUWINVOUCHERDATA',getCurrentControllerPath('index')));
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
		$data['error'] 			 = '';
		$data['activeMenu'] 	 = 'uwinn_raffle';
		$data['activeSubMenu'] 	 = 'allrafflewiner';
	 
		$DataArray = array();
		$uploadedFile = $_FILES["csvFile"]["tmp_name"];
		if (($open = fopen($uploadedFile, "r")) !== false):
		    while(($data = fgetcsv($open, 1000, ",")) !== false):
		        $DataArray[] = $data;
		    endwhile;
		    fclose($open);
			$result = [];
			$param['batch_id'] 			= (int)$this->common_model->getNextSequence('batch_count');
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

					$FindColumn      		= 'WINNER TYPE';
					$winnerTypeIndex     	= array_search($FindColumn, $itemArray);

					$FindColumn      		= 'WINNING AMOUNT';
					$WinningAmountIndex     = array_search($FindColumn, $itemArray);

					$FindColumn      		= 'COUPONS';
					$couponsIndex   		  = array_search($FindColumn, $itemArray);

					$FindColumn      		= 'POS NUMBER';
					$posnumberIndex   		= array_search($FindColumn, $itemArray);

					$FindColumn      		= 'STORE NAME';
					$storenameIndex   		= array_search($FindColumn, $itemArray);
				else:
					$param['csv_name'] 		      =	$_FILES['csvFile']['name'];
					$param['order_id']		      =	$itemArray[$orderIndex]?$itemArray[$orderIndex]:'N/A';
					$param['seller_first_name']   =	$itemArray[$sellerFirstNameIndex]?$itemArray[$sellerFirstNameIndex]:'N/A';
					$param['seller_last_name']    =	$itemArray[$sellerLastNameIndex]?$itemArray[$sellerLastNameIndex]:'N/A';
					$param['seller_mobile']  	  =	$itemArray[$sellerMobileIndex]?$itemArray[$sellerMobileIndex]:'';
					$param['winner_type']  	 	  =	$itemArray[$winnerTypeIndex]?$itemArray[$winnerTypeIndex]:'';
					$param['amount'] 			  =	$itemArray[$WinningAmountIndex]?$itemArray[$WinningAmountIndex]:'0';
					$param['coupons'] 			  =	$itemArray[$couponsIndex]?$itemArray[$couponsIndex]:'';
					$param['store_name'] 		  =	$itemArray[$storenameIndex]?$itemArray[$storenameIndex]:'';
					$param['pos_number'] 		  =	$itemArray[$posnumberIndex]?$itemArray[$posnumberIndex]:'';
					array_push($result, $param);
				endif;
			endforeach;
		endif;
		// echo '<pre>';print_r($result);die();
		$data['ALLDATA']  =  $result;
		$this->layouts->set_title('Raffle winner Preview | UWINN');
		$this->layouts->admin_view('raffle/winner/checkpreview',array(),$data);
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
		 $this->layouts->set_title('UWinn Winner Uplaoding | UWINN');
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
		$batchData = $this->input->post('batch'); // Assuming 'batch' contains an array of data entries
		$dataToInsert = [];
		
		// Prepare the data array for insertion
		foreach($batchData as $data):
			$param['voucher_id'] 		= (int)$this->common_model->getNextSequence('uw_uwin_winner');
			$param['batch_id'] 			= (int)$data['batch_id'];
			$param['csv_name'] 			= $data['csv_name'];
			$param['order_id'] 			= $data['order_id'];
			$param['winner_type'] 		= $data['winner_type'];
			$param['amount'] 				 = $data['amount'];
			$param['amount_in_number']  = (float)$data['amount_in_number'];
			$param['seller_first_name'] = $data['seller_first_name'];
			$param['seller_last_name']  = $data['seller_last_name'];
			$param['seller_mobile']     = (int)$data['seller_mobile'];
			$param['store_name']        = $data['store_name'];
			$param['pos_number']     	 = (int)$data['pos_number'];
			$param['coupons'] 			 = $data['coupons'];
			$param["status"] 			    = "A";
			$param["created_at"] 		 = date('Y-m-d H:i:s');
			$param["created_by"] 		 = "Admin";
			$dataToInsert[] 			    = $param;
		endforeach;

		// Perform the batch insert
		$rrr = $this->common_model->addManyData('uw_raffle_winner', $dataToInsert);
		
		$successMessage = count($dataToInsert) . " items uploaded successfully.";
	}

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


}
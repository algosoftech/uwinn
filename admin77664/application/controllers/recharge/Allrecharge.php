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

class Allrecharge extends CI_Controller {

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
	 + + Date 			: 12 February 2024
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index()
	{	
		$this->admin_model->authCheck('view_data');
		$data['error'] 		   = "";
		$data['activeMenu']    = "recharge";
		$data['activeSubMenu'] = "allrecharge";

		$searchField = $this->input->get('searchField');
		$searchValue = $this->input->get('searchValue');
		$fromDate 	 = $this->input->get('fromDate');
		$toDate 	 = $this->input->get('toDate');

		$data['searchField']	= $searchField;
		$data['searchValue']	= $searchValue;
		$data['fromDate']		= $fromDate;  
		$data['toDate']			= $toDate;

		if($fromDate):
			$fromDate	 = date('Y-m-d H:i', strtotime($this->input->get('fromDate')));
			$whereCon['where']['created_at']['$gte']  =  $fromDate;
		endif;
		if($toDate):
			$toDate	 = date('Y-m-d H:i', strtotime($this->input->get('toDate')));
			$whereCon['where']['created_at']['$lte']  =  $toDate;
		endif;

		$whereCon['where']['narration'] 	= 'Recharge';
		$whereCon['where']['record_type'] 	= 'Credit';

		if($searchField == 'recharge_to'  && !empty($searchValue) ):
				
			if(is_numeric($searchValue)):	
			  $Userwhere['users_mobile'] = (int)$searchValue;	
			else:
			  $Userwhere['users_email']  = $searchValue;	
			endif;
			
			$tblName 			   	  = 'uw_users';
			$UserwhereCon['where'] 	  = $Userwhere;
			$userData 				  = $this->common_model->getData('single',$tblName,$UserwhereCon,$status);

			$whereCon['where']['user_oid']	= new MongoDB\BSON\ObjectId($userData['_id']->{'$id'});
			// $RechargeWhereCon['narration']	= 'Recharge';
			$whereCon['where']['$or']	=  array(
											    array('narration' => 'Recharge'),
											    array('narration' => 'Reverse Recharge'),
											);
		
		elseif($searchField == 'recharge_by'  && !empty($searchValue)):

			if(is_numeric($searchValue)):	
			  $Userwhere['users_mobile'] = (int)$searchValue;	
			else:
			  $Userwhere['users_email']  = $searchValue;	
			endif;

			$tblName 			   	  = 'uw_users';
			$UserwhereCon['where'] 	  = $Userwhere;
			$userData 				  = $this->common_model->getData('single',$tblName,$UserwhereCon,$status);

			if($userData):
				$whereCon['where']['user_id_deb']	= (int)$userData['users_id'];
				// $RechargeWhereCon['narration']	= 'Recharge';
				$whereCon['where']['$or']	=  array(
												    array('narration' => 'Recharge'),
												    array('narration' => 'Reverse Recharge'),
												);
			else:

				if(is_numeric($searchValue)):	
				  $Userwhere1['admin_phone'] = (int)$searchValue;	
				else:
				  $Userwhere1['admin_email']  = $searchValue;	
				endif;

				$tblName 			   	  = 'uw_admin';
				$UserwhereCon['where'] 	  = $Userwhere;
				$AdminData 				  = $this->common_model->getData('single',$tblName,$Userwhere1,$status);

				if(!empty($AdminData)):
					$whereCon['where']['user_id_deb']	= (int)$AdminData['admin_id'];
					$whereCon['where']['created_by']	= 'ADMIN';
				else:
					$whereCon['where']['user_id_deb']	= (int)'99999';
				endif;
					$whereCon['where']['$or']			=  array(
													    array('narration' => 'Recharge'),
													    array('narration' => 'Reverse Recharge'),
													);
			endif;
		endif;

		$this->session->set_userdata('ALLRECHARGEDATA',currentFullUrl());
		$baseUrl 		= 	getCurrentControllerPath('index');
		$qStringdata	=	explode('?',currentFullUrl());
		$suffix			= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		
		$tblName 		= 	'uw_loadBalance';
		$shortField 	= 	array('created_at'=> -1);
		$totalRows 		=   $this->common_model->getRechargeTopupData('count',$whereCon,$startIndex,$itemsPerPage,$tblName);
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
		
		$data['ALLDATA'] 					=   $this->common_model->getRechargeTopupData('',$whereCon,$page,$perPage,$tblName);
		// echo "<pre>";print_r($data['ALLDATA']);die();
		$this->layouts->set_title('All Recharge | Recharge | UWINN');
		$this->layouts->admin_view('recharge/allrecharge/index',array(),$data);
	}	// END OF FUNCTION

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name : addeditdata
	 + + Developed By  : Dilip Halder
	 + + Purpose  	   : This function used for Add Edit data
	 + + Date 		   : 07 February 2024
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function addeditdata($editId='')
	{		
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'recharge';
		$data['activeSubMenu'] 				= 	'addeditdata';

		if($editId):
			$this->admin_model->authCheck('edit_data');
			$data['EDITDATA']				=	$this->common_model->getDataByParticularField('uw_loadBalance','load_balance_id',(int)$editId);
		else:
			$this->admin_model->authCheck('add_data');
		endif;
		if($this->input->post('SaveChanges')):
			$error					=	'NO';
			//echo '<pre>';print_r($this->input->post());die();
			$this->form_validation->set_rules('user', 'Email ID / Mobile No.', 'trim');
			$this->form_validation->set_rules('userID', 'Error.', 'trim');
			$this->form_validation->set_rules('addUpoints', 'Add UPoints', 'trim');
			if($this->form_validation->run() && $error == 'NO'): 
				$user 			= 	$_POST['user'];
				$rechange_for 	=   $this->input->post('rechange_for');
				$addpoints		=	(int)$this->input->post('addUpoints');

				if (is_numeric($user) && $rechange_for == 'Mobile No.') {
					$user_data = $this->common_model->getDataByParticularField('uw_users', 'users_mobile', (int)$user);
				}else{
					$user_data = $this->common_model->getDataByParticularField('uw_users', 'users_email', $user);
				}
				// echo '<pre>';print_r($user_data);die();
				if($user_data['status'] == 'I'):
					$this->session->set_flashdata('alert_error',lang('ACCOUNT_BLOCKED'));
				elseif(!empty($user_data)):
					//echo $this->input->post('percentage');die();
					if($this->input->post('percentage')):
						$percentage 				=	$this->input->post('percentage');
						$percentageAmt				=	$addpoints*$percentage/100;
						$totalRechargeAmount		=	$addpoints + $percentageAmt;
						$totalPoints 				=	$user_data['totalArabianPoints'] + $totalRechargeAmount;
						$avlPoints					=	$user_data['totalArabianPoints'] + $totalRechargeAmount;
						$rechargeDetails 			=	array(
															'percentage'	=>	(float)$percentage,
															'amount'		=>	(float)$percentageAmt,
															);
					else:
						$totalRechargeAmount		=	$addpoints;
					endif;

					$param["user_oid"] 				=	new MongoDB\BSON\ObjectId($user_data['_id']->{'$id'});
					$param["user_id_cred"] 			=	(int)$user_data['users_id'];
					$param['user_id_deb']			=	(int)$this->session->userdata('UW_ADMIN_ID');
					$param['upoints']				=	(float)$addpoints;
					$param['sum_arabian_points']	=	(float)$totalRechargeAmount;
					$param['availableArabianPoints']=   (float)$user_data['availableArabianPoints'];
					$param["end_balance"] 			=	(float)$user_data["availableArabianPoints"] + (float)$totalRechargeAmount ;
					if($this->input->post('percentage')):
						$param['rechargeDetails']	=	$rechargeDetails;
					endif;	
					$param['record_type']			=	'Credit';
					$param["narration"] 			=	'Recharge';
					$param['remarks']				=	$this->input->post('remarks');
					$param['load_balance_id']	=	(int)$this->common_model->getNextSequence('uw_loadBalance');
					$param['creation_ip']		=	currentIp();
					$param['created_at']		=	date('Y-m-d H:i');//currentDateTime();
					$param['created_by']		=	'ADMIN';
					$param['status']			=	'A';
					$param["created_user_id"] 	=	(int)$this->session->userdata('UW_ADMIN_ID');
					$param['device_type']		=	'ios';
					$alastInsertId				=	$this->common_model->addData('uw_loadBalance',$param);

					if(!empty($alastInsertId)):
						if($this->input->post('percentage')):
							$percentage 				=	$this->input->post('percentage');
							$percentageAmt				=	$addpoints*$percentage/100;
							$totalRechargeAmount		=	$param['upoints'] + $percentageAmt;
							$totalPoints 				=	$user_data['totalArabianPoints'] + $totalRechargeAmount;
							$avlPoints					=	$user_data['availableArabianPoints'] + $totalRechargeAmount;
							$rechargeDetails 			=	array(
																'percentage'	=>	(float)$percentage,
																'amount'		=>	(float)$percentageAmt,
																);
						else:
							$totalPoints  = $user_data['totalArabianPoints'] + $param['upoints'];
							$avlPoints 	 = $user_data['availableArabianPoints'] + $param['upoints'];
						endif;
						$udateData = array(
							'totalArabianPoints'		=>	$totalPoints,
							'availableArabianPoints'	=>	$avlPoints
						);
						$isEdit = $this->common_model->editData('uw_users', $udateData, 'users_id', $user_data['users_id']);
					endif;
					
					//Send Creadited Notification to user
					// if($user_data['users_id']):
					// 	$data 		=	array(
					// 		'arabianpoint'	=>	(int)$totalRechargeAmount,
					// 		'name'			=>	'UWINN',
					// 		'user_id'		=>	$user_data["users_id"],
					// 		'device_id'		=>	$user_data["device_id"]
					// 		);
					// 	$rtn = $this->notification_model->rceivedArabianPointNotification($data);
					// endif;
					//END
					$this->session->set_flashdata('alert_success',lang('RECHARGE_SUCCESFULLY'));

				endif;
					
				//echo '<pre>'; print_r($param);die();

				redirect(correctLink('MASTERDATARECHARGETYPE',getCurrentControllerPath('index')));
			endif;
		endif;
		$this->layouts->set_title('Add/Edit Recharge System | UWINN');
		$this->layouts->admin_view('recharge/allrecharge/addeditdata',array(),$data);
	}	// END OF FUNCTION	

	/***********************************************************************
	** Function name 	: changestatus
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for change status
	** Date 			: 07 February 2024
	************************************************************************/
	function changestatus($changeStatusId='',$statusType='')
	{  
		$this->admin_model->authCheck('edit_data');
		$param['status']		=	$statusType;
		$this->common_model->editData('uw_loadBalance',$param,'load_balance_id',(int)$changeStatusId);
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		
		redirect(correctLink('ALLRECHARGEDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name 	: deletedata
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for delete data
	** Date 			: 08 APRIL 2022
	************************************************************************/
	function deletedata($deleteId='')
	{  
		$this->admin_model->authCheck('delete_data');
		$this->common_model->deleteData('uw_loadBalance','load_balance_id',(int)$deleteId);
		$this->common_model->deleteData('uw_coupon_code_only','load_balance_id',(int)$deleteId);
		$this->session->set_flashdata('alert_success',lang('deletesuccess'));
		
		redirect(correctLink('ALLRECHARGEDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name 	: exportexcel
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for export deleted users data
	** Date 			: 09 APRIL 2022
	** Updated Date 	: 
	** Updated By   	: 
	************************************************************************/
	function exportexcel($load_balance_id='')
	{  
		$this->admin_model->authCheck('view_data');
		//Generating Logs
		$this->common_model->generateLogs();
		
		$searchField    = $this->input->post('searchField');
		$searchValue    = $this->input->post('searchValue');
		$fromDate 	    = $this->input->post('fromDate');
		$toDate 	    = $this->input->post('toDate');

		if($fromDate):
			$fromDate	 = date('Y-m-d H:i', strtotime($this->input->post('fromDate')));
			$whereCon['where']['created_at']['$gte']  =  $fromDate;
		endif;
		if($toDate):
			$toDate	     = date('Y-m-d H:i', strtotime($this->input->post('toDate')));
			$whereCon['where']['created_at']['$lte']  =  $toDate;
		endif;

		$whereCon['where']['narration'] 	= 'Recharge';
		$whereCon['where']['record_type'] 	= 'Credit';

		if($searchField == 'recharge_to'  && !empty($searchValue) ):
				
			if(is_numeric($searchValue)):	
			  $Userwhere['users_mobile'] = (int)$searchValue;	
			else:
			  $Userwhere['users_email']  = $searchValue;	
			endif;
			
			$tblName 			   	  = 'uw_users';
			$UserwhereCon['where'] 	  = $Userwhere;
			$userData 				  = $this->common_model->getData('single',$tblName,$UserwhereCon,$status);

			$whereCon['where']['user_oid']	= new MongoDB\BSON\ObjectId($userData['_id']->{'$id'});
			// $RechargeWhereCon['narration']	= 'Recharge';
			$whereCon['where']['$or']	=  array(
											    array('narration' => 'Recharge'),
											    array('narration' => 'Reverse Recharge'),
											);
		
		elseif($searchField == 'recharge_by'  && !empty($searchValue)):

			if(is_numeric($searchValue)):	
			  $Userwhere['users_mobile'] = (int)$searchValue;	
			else:
			  $Userwhere['users_email']  = $searchValue;	
			endif;

			$tblName 			   	  = 'uw_users';
			$UserwhereCon['where'] 	  = $Userwhere;
			$userData 				  = $this->common_model->getData('single',$tblName,$UserwhereCon,$status);

			if($userData):
				$whereCon['where']['user_id_deb']	= (int)$userData['users_id'];
				// $RechargeWhereCon['narration']	= 'Recharge';
				$whereCon['where']['$or']	=  array(
												    array('narration' => 'Recharge'),
												    array('narration' => 'Reverse Recharge'),
												);
			else:

				if(is_numeric($searchValue)):	
				  $Userwhere1['admin_phone'] = (int)$searchValue;	
				else:
				  $Userwhere1['admin_email']  = $searchValue;	
				endif;

				$tblName 			   	  = 'uw_admin';
				$UserwhereCon['where'] 	  = $Userwhere;
				$AdminData 				  = $this->common_model->getData('single',$tblName,$Userwhere1,$status);

				if(!empty($AdminData)):
					$whereCon['where']['user_id_deb']	= (int)$AdminData['admin_id'];
					$whereCon['where']['created_by']	= 'ADMIN';
				else:
					$whereCon['where']['user_id_deb']	= (int)'99999';
				endif;
					$whereCon['where']['$or']			=  array(
													    array('narration' => 'Recharge'),
													    array('narration' => 'Reverse Recharge'),
													);
			endif;
		endif;

		$bindWithAdmin 	= $this->input->post('bind_with_admin');
		if(!empty($bindWithAdmin)  &&  $bindWithAdmin == 'on'){
			$whereCon['where']['users.bind_person_name']  = 'Admin';
		}

		$baseUrl 	  = getCurrentControllerPath('exportexcel');
		// $totalRows    = $this->common_model->getData('count','uw_coupon_code_only',$whereCon);
		$tblName 	  = 'uw_loadBalance';
		$shortField   =  array('created_at'=> -1);
		$totalRows 	  =  count($this->common_model->getRechargeTopupData('',$whereCon,$startIndex,$itemsPerPage,$tblName));
		// $totalRows 	  =  $this->common_model->getRechargeTopupData('',$whereCon,$startIndex,$itemsPerPage,$tblName);
		// echo "<pre>";print_r($totalRows);die();

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
 		
 		$startIndex  			= ($page - 1) * $itemsPerPage;
		$totalpage 				= count($totalpage);
		$data['current_page']   = $current_page;
		$data['total_page'] 	= $totalpage;
		$data['searchField'] 	= $searchField;
		$data['searchValue'] 	= $searchValue;
		$data['fromDate'] 		= $fromDate;
		$data['toDate'] 		= $toDate;
		$data['bindWithAdmin'] 	= $bindWithAdmin;

		// echo "<pre>";
		// print_r($data);
		// die();

		$this->layouts->set_title('Export CSV | UWINN');
		$this->layouts->admin_view('recharge/allrecharge/exportexcel',array(),$data);

	}



	/***********************************************************************
	** Function name 	: exportexcelApi
	** Developed By 	: Dilip halder
	** Purpose  		: This function used for export order data
	** Date 			: 23 July 2024
	************************************************************************/
	function exportexcelApi(){
		
		$this->admin_model->authCheck('view_data');

	
		$searchField    = $this->input->post('searchField');
		$searchValue    = $this->input->post('searchValue');
		$fromDate 	    = $this->input->post('fromDate');
		$toDate 	    = $this->input->post('toDate');

		if($fromDate):
			$fromDate	 = date('Y-m-d H:i', strtotime($this->input->post('fromDate')));
			$whereCon['where']['created_at']['$gte']  =  $fromDate;
		endif;
		if($toDate):
			$toDate	     = date('Y-m-d H:i', strtotime($this->input->post('toDate')));
			$whereCon['where']['created_at']['$lte']  =  $toDate;
		endif;

		$whereCon['where']['narration'] 	= 'Recharge';
		$whereCon['where']['record_type'] 	= 'Credit';

		if($searchField == 'recharge_to'  && !empty($searchValue) ):
				
			if(is_numeric($searchValue)):	
			  $Userwhere['users_mobile'] = (int)$searchValue;	
			else:
			  $Userwhere['users_email']  = $searchValue;	
			endif;
			
			$tblName 			   	  = 'uw_users';
			$UserwhereCon['where'] 	  = $Userwhere;
			$userData 				  = $this->common_model->getData('single',$tblName,$UserwhereCon,$status);

			$whereCon['where']['user_oid']	= new MongoDB\BSON\ObjectId($userData['_id']->{'$id'});
			// $RechargeWhereCon['narration']	= 'Recharge';
			$whereCon['where']['$or']	=  array(
											    array('narration' => 'Recharge'),
											    array('narration' => 'Reverse Recharge'),
											);
		
		elseif($searchField == 'recharge_by'  && !empty($searchValue)):

			if(is_numeric($searchValue)):	
			  $Userwhere['users_mobile'] = (int)$searchValue;	
			else:
			  $Userwhere['users_email']  = $searchValue;	
			endif;

			$tblName 			   	  = 'uw_users';
			$UserwhereCon['where'] 	  = $Userwhere;
			$userData 				  = $this->common_model->getData('single',$tblName,$UserwhereCon,$status);

			if($userData):
				$whereCon['where']['user_id_deb']	= (int)$userData['users_id'];
				// $RechargeWhereCon['narration']	= 'Recharge';
				$whereCon['where']['$or']	=  array(
												    array('narration' => 'Recharge'),
												    array('narration' => 'Reverse Recharge'),
												);
			else:

				if(is_numeric($searchValue)):	
				  $Userwhere1['admin_phone'] = (int)$searchValue;	
				else:
				  $Userwhere1['admin_email']  = $searchValue;	
				endif;

				$tblName 			   	  = 'uw_admin';
				$UserwhereCon['where'] 	  = $Userwhere;
				$AdminData 				  = $this->common_model->getData('single',$tblName,$Userwhere1,$status);

				if(!empty($AdminData)):
					$whereCon['where']['user_id_deb']	= (int)$AdminData['admin_id'];
					$whereCon['where']['created_by']	= 'ADMIN';
				else:
					$whereCon['where']['user_id_deb']	= (int)'99999';
				endif;
					$whereCon['where']['$or']			=  array(
													    array('narration' => 'Recharge'),
													    array('narration' => 'Reverse Recharge'),
													);
			endif;
		endif;

		$bindWithAdmin 	= $this->input->post('bindWithAdmin');
		if(!empty($bindWithAdmin)  &&  $bindWithAdmin == 'on'){
			$whereCon['where']['users.bind_person_name']  = 'Admin';
		}


		$page 		  = $this->input->post('pageno');
		// $page = 1;
 		$itemsPerPage = 5000;
 		$startIndex   = ($page - 1)*$itemsPerPage;
 		$resultType   = '';


 		$tblName 	  = 'uw_loadBalance';
		$shortField   =  array('created_at'=> -1);
		$rechagreData =  $this->common_model->getRechargeTopupData('',$whereCon,$startIndex,$itemsPerPage,$tblName);
		// echo "<pre>";print_r($rechagreData);die();

		$CSVData = array();
		foreach($rechagreData as $index => $itemsArray):
			$CSVData1['Reciever Phone']          = !empty($itemsArray['users_mobile']) ? $itemsArray['users_mobile'] : 'N/A';
			$CSVData1['Reciever Name']           = !empty($itemsArray['users_name']) ? $itemsArray['users_name'].' '.$itemsArray['last_name'] : 'N/A';
			$CSVData1['Receiver POS ID']         = !empty($itemsArray['pos_number']) ? $itemsArray['pos_number'] : 'N/A';
			$CSVData1['Receiver Bind With']      = !empty($itemsArray['bind_person_name']) ? $itemsArray['bind_person_name'] : 'N/A';
			$CSVData1['User Type']               = !empty($itemsArray['users_type']) ? $itemsArray['users_type'] : 'N/A';
			$CSVData1['UPoints']                 = !empty($itemsArray['upoints']) ? $itemsArray['upoints'] : 'N/A';
			$CSVData1['Remarks']                 = !empty($itemsArray['remarks']) ? $itemsArray['remarks'] : 'N/A';
			
			if($itemsArray['created_by']== 'ADMIN'):
				$CSVData1['Sender Name']         = !empty($itemsArray['seller_first_name']) ? $itemsArray['seller_first_name'] : 'N/A';
			else:
				$CSVData1['Sender Name']         = !empty($itemsArray['bindwith_first_name']) ? $itemsArray['bindwith_first_name'] :  'N/A';

			endif;

			if($itemsArray['created_by']== 'ADMIN'):
				$CSVData1['Sender Phone']        = !empty($itemsArray['seller_mobile']) ? $itemsArray['seller_mobile'] : 'N/A';
			else:
				$CSVData1['Sender Phone']         = !empty($itemsArray['bindwith_mobile']) ? $itemsArray['bindwith_mobile'] : 'N/A';
			endif;


			if($itemsArray['created_by']== 'ADMIN'):
				$CSVData1['Sender Type']         = !empty($itemsArray['seller_type']) ? $itemsArray['seller_type'] : 'N/A';
			else:
				$CSVData1['Sender Type']         = !empty($itemsArray['bindwith_users_type']) ? $itemsArray['bindwith_users_type'] : 'N/A';
			endif;
				$CSVData1['Sender Pos No']         = !empty($itemsArray['bindwith_pos_number']) ? $itemsArray['bindwith_pos_number'] : 'N/A';

			$CSVData1['Date']                    = !empty($itemsArray['created_at']) ? date('Y-m-d',strtotime($itemsArray['created_at'])) : 'N/A';
			$CSVData1['Time']                    = !empty($itemsArray['created_at']) ? date('H:i',strtotime($itemsArray['created_at'])) : 'N/A';
			array_push($CSVData, $CSVData1);
		endforeach;
		echo json_encode($CSVData);
		die();
	}



public function checkDeplicacy(){
	//echo $_POST['user']; die();

	$user 	= $_POST['user'];

	if (is_numeric($user)) {
		$user_data = $this->common_model->getDataByParticularField('uw_users', 'users_mobile', (int)$user);
	}else{		
		$user_data = $this->common_model->getDataByParticularField('uw_users', 'users_email', $user);	
	}
	if($user_data['availableArabianPoints'] !== false){
		echo $user_data['users_name'].' (' .strtolower($user_data['users_type']). ') available arabian points is '.number_format($user_data['availableArabianPoints'],2).'__'.$user_data['users_id']; 
	}else{
		echo "Email ID / Mobile is not registered.";
	}

	die();

}

	/***********************************************************************
	** Function name 	: reverse
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for reverse recharge
	** Date 			: 29 DEC 2022
	** Updated Date 	: 
	** Updated By   	: 
	************************************************************************/
	public function reverse($id=''){
		
		$error = 'NO';
		if($id == ''):
			$this->session->set_flashdata('alert_error','Data not found.');
			redirect('recharge/allrecharge/index');
		endif;
		$wcon['where']  = array('load_balance_id'=>(int)$id,'narration'=>'Recharge');
		$data			= $this->common_model->getData('single','uw_loadBalance',$wcon);
		if(!empty($data)):
			$this->admin_model->authCheck('edit_data');

			// Updating reverse entries in laodbalance..
			$whereCondition['user_id_cred'] = (int)$data['user_id_cred'];
			$whereCondition['user_id_deb']  = (int)$data['user_id_deb'];
			$whereCondition['narration']    = 'Recharge';
			$whereCondition['created_at']['$gte'] = date('Y-m-d H:i', strtotime($data['created_at']));
			$whereCondition['created_at']['$lte'] = date('Y-m-d H:i', strtotime($data['created_at'] . ' +2 seconds'));
			$tableName 			 = 'uw_loadBalance';
			$udateData['status'] = 'R'; 
		 	$this->common_model->editMultipleDataByMultipleCondition($tableName, $udateData,$whereCondition);

			// Fetching credited users details and removing UPoints...
			$tblName = 'uw_users';
			$whereCon['where']  = array('users_id' => (int)$data['user_id_cred']);
			$userdata 			=	$this->common_model->getData('single',$tblName,$whereCon);

			$param1['availableArabianPoints'] =  -$data['upoints'];
			$balanceCredit = $this->common_model->manageBalance($tblName,$param1,'users_id',(int)$data['user_id_cred']);
			
			if($data['created_by'] == 'ADMIN'):
				if(!empty($userdata)):
					// userdata
					if($balanceCredit):
						$param['load_balance_id']		=	(int)$this->common_model->getNextSequence('uw_loadBalance');
						$param["user_oid"] 				=	new MongoDB\BSON\ObjectId(($userdata['_id']->{'$id'} ));
						$param["user_id_deb"] 			=	(int)$userdata['users_id'];
						$param['user_id_cred']			=	(int)$data['user_id_deb'];
						$param['upoints']				=	(float)$data['sum_arabian_points'];
						$param['sum_arabian_points']	=	(float)$data['sum_arabian_points'];
						$param['availableArabianPoints']=   (float)$userdata['availableArabianPoints'];
						$param["end_balance"] 			=	(float)$userdata["availableArabianPoints"] - (float)$data['sum_arabian_points'] ;
						$param['record_type']			=	'Debit';
						$param["narration"] 			=	'Reverse Recharge';
						$param['remarks']				=	"Recharge has been reversed to ".$userdata['users_mobile'];
						$param['creation_ip']			=	currentIp();
						$param['created_at']			=	date('Y-m-d H:i:s');//currentDateTime();
						$param['created_by']			=	'ADMIN';
						$param['update_date']			=	date('Y-m-d H:i:s');//currentDateTime();
						$param['status']				=	'R';
						$param["created_user_id"] 		=	(int)$this->session->userdata('UW_ADMIN_ID');
						$alastInsertId					=	$this->common_model->addData('uw_loadBalance',$param);

						// Updated session data..
						$this->session->set_flashdata('alert_success','Recharge Reverse Successfully.');
						redirect('recharge/allrecharge/index');
					else:
						$this->session->set_flashdata('alert_error','User not found');
					endif;
				else:
					$this->session->set_flashdata('alert_error','User not found');
				endif;
			else:

				// Crediting balance to BTB User..	
				$Fields     = array('availableArabianPoints','totalArabianPoints','_id');
				$sellerData = $this->common_model->getSingleDataByParticularField($Fields,$tblName,'users_id',(int)$data['user_id_deb']);

				if(!empty($sellerData)):

					// Getting commission details..
					$from   = date('Y-m-d H:i', strtotime($data['created_at']));
					$to     = date('Y-m-d H:i', strtotime($data['created_at'] . ' +1 minutes'));
					$wcon1['where']['user_id_cred']  	= (int)$data['user_id_deb'];	  
					$wcon1['where']['record_type']  	= 'Credit';	  
					$wcon1['where']['narration']  	    = 'Recharge Commission';	  
					$wcon1['where']['created_at']  	    =  array('$gte' => $from , '$lte' => $to); 
					$commissionData = $this->common_model->getData('single','uw_loadBalance',$wcon1);

					if(!empty($commissionData)):

						// Added cancellection Commission status.
						$tableName 			      = 'uw_loadBalance';
						$can_Commission['status'] = 'R'; 
						$Can_Where_Commission     = array( 'load_balance_id' => (int)$commissionData['load_balance_id'] );
					 	$this->common_model->editMultipleDataByMultipleCondition($tableName, $can_Commission,$Can_Where_Commission);


						// Seller Balance Updating..
						$SellerBalaneParam['availableArabianPoints'] = +$data['upoints']-$commissionData['upoints'];
						$balanceCredit = $this->common_model->manageBalance($tblName,$SellerBalaneParam,'users_id',(int)$data['user_id_deb']);


						$user_oid = (string)$commissionData['user_oid'];
					    // Generating loadBalance for Commission amount deducting..
		                $commisionBalance['load_balance_id'] =  (int)$this->common_model->getNextSequence('uw_loadBalance');
		                $commisionBalance['user_oid']        =  new MongoDB\BSON\ObjectId($user_oid);
		                $commisionBalance['request_id']      =  $data['load_balance_id'];
		                $commisionBalance['request_oid']     =  new MongoDB\BSON\ObjectId($data['_id']->{'$id'});
		                $commisionBalance['user_id_deb']     =  (int)$data['user_id_deb'];
		                $commisionBalance['user_id_cred']    =  (int)0;
		                $commisionBalance["availableArabianPoints"]  =	(float)$sellerData["availableArabianPoints"];
						$commisionBalance["end_balance"] 			 =	(float)$sellerData["availableArabianPoints"] - $commissionData['upoints'];
		                $commisionBalance['record_type']     = 'Debit';
		                $commisionBalance['narration']       = 'Recharge Commission Reverted';
		                $commisionBalance['remarks']         = "Commission reverted for recharge worth AED ".$data['upoints'].".";
		                $commisionBalance['upoints']         = (float)$commissionData['upoints'];
		                $commisionBalance['creation_ip']     = $this->input->ip_address();;
		                $commisionBalance['created_at']      = date('Y-m-d H:i');
		                $commisionBalance['created_by']      = (int)$data['user_id_deb'];
		                $commisionBalance['status']          = 'A';
				    	$commissionResponce = $this->common_model->addData('uw_loadBalance', $commisionBalance);

				    	if(!empty($commissionResponce)):
				    		$commisionBalance1['load_balance_id'] =  (int)$this->common_model->getNextSequence('uw_loadBalance');
			                $commisionBalance1['user_oid']        =  new MongoDB\BSON\ObjectId($user_oid);
			                $commisionBalance1['request_id']      =  $data['load_balance_id'];
			                $commisionBalance1['request_oid']     =  new MongoDB\BSON\ObjectId($data['_id']->{'$id'});
			                $commisionBalance1['user_id_deb']     =  (int)0;
			                $commisionBalance1['user_id_cred']    =  (int)$data['user_id_deb'];
			                $commisionBalance1["availableArabianPoints"]  =	(float)$commissionResponce["end_balance"];
							$commisionBalance1["end_balance"] 			  =	(float)$commisionBalance["end_balance"] + $data['upoints'];
			                $commisionBalance1['record_type']     = 'Credit';
			                $commisionBalance1['narration']       = 'Reverse Recharge';
			                $commisionBalance1['remarks']         = "Recharge has been reversed to ".$userdata['users_mobile'];
			                $commisionBalance1['upoints']         = (float)$data['upoints'];
			                $commisionBalance1['creation_ip']     = $this->input->ip_address();;
			                $commisionBalance1['created_at']      = date('Y-m-d H:i');
			                $commisionBalance1['created_by']      = (int)$data['user_id_deb'];
			                $commisionBalance1['status']          = 'A';
					    	$this->common_model->addData('uw_loadBalance', $commisionBalance1);


						    $param111['load_balance_id']		= (int)$this->common_model->getNextSequence('uw_loadBalance');
							$param111["user_oid"] 				= new MongoDB\BSON\ObjectId(($userdata['_id']->{'$id'} ));
			                $param111['request_id']      		= $data['load_balance_id'];
			                $param111['request_oid']     		= new MongoDB\BSON\ObjectId($data['_id']->{'$id'});
							$param111["user_id_deb"] 			= (int)$userdata['users_id'];
							$param111['user_id_cred']			= (int)$data['user_id_deb'];
							$param111['upoints']				= (float)$data['upoints'];
							$param111["availableArabianPoints"] = (float)$userdata["availableArabianPoints"];
							$param111["end_balance"] 			= (float)$userdata["availableArabianPoints"] - (float)$data['upoints'] ;
							$param111['record_type']			= 'Debit';
							$param111["narration"] 				= 'Reverse Recharge';
							$param111['remarks']				= "Recharge has been reversed to ".$userdata['users_mobile'];
							$param111['creation_ip']			= currentIp();
							$param111['created_at']				= date('Y-m-d H:i:s');//currentDateTime();
							$param111['created_by']				= 'ADMIN';
							$param111['update_date']			= date('Y-m-d H:i:s');//currentDateTime();
							$param111['status']					= 'R';
							$param111["created_user_id"] 		= (int)$this->session->userdata('UW_ADMIN_ID');
							$alastInsertId						= $this->common_model->addData('uw_loadBalance',$param111);

							$this->session->set_flashdata('alert_success','Recharge Reverse Successfully.');
							redirect('recharge/allrecharge/index');
					    	// Credit the purchesed points and get available arabian points of user.
						else:
							$this->session->set_flashdata('alert_error','Commission not Removed');
							redirect('recharge/allrecharge/index');
						endif;
					endif;
				else:
					$this->session->set_flashdata('alert_error','Commission not Removed');
					redirect('recharge/allrecharge/index');
				endif;
			endif;
		else:
			$this->session->set_flashdata('alert_error','Data not found.');
			redirect('recharge/allrecharge/index');
		endif;
	} 

	/***********************************************************************
	** Function name 	: checkpreview
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for checkpreview data
	** Date 			: 22 March 2024
	************************************************************************/
	public function checkpreview()
	{	
		$this->admin_model->authCheck();
		$data['error'] 						= '';
		$data['activeMenu'] 				= 'sub_winners';
		$data['activeSubMenu'] 				= 'voucher';

		if(!empty($_FILES["csvFile"])):
		    // Check if a file was uploaded
		    if (isset($_FILES["csvFile"]) && $_FILES["csvFile"]["error"] == 0):
		        $uploadedFile = $_FILES["csvFile"]["tmp_name"];
		        // Open the uploaded CSV file
		        $handle = fopen($uploadedFile, "r");
		        if($handle !== false):
		            // Read the CSV file line by line
		            $i = 0;
		            $param = array();

		            $rechargeList  = array();
		            while(($data = fgetcsv($handle, 1000, ",")) !== false) {
		            	//if date is not pass than picking current date & time... 
		            	$date = $data[6]?$data[6]:date('d M Y h:i A');
		            	$timestamp = strtotime(str_replace('/', '-',$date));
						$formatted_date = date('d M Y h:i A', $timestamp);

		            	if($i > 0):
		            		$param['sl_no']      	=	$data[0];
		            		$param['pos_id']        =	$data[1];
		            		$param['store_name']    =	$data[2];
		            		$param['bind_with'] 	=	$data[3];
		            		$param['mobile_no'] 	=	$data[4];
		            		$param['topup'] 	    =	$data[5];
		            		$param['created_date'] 	=	$formatted_date;
		            		array_push($rechargeList, $param);
		            	endif;
		                $i++;
		            }
		            fclose($handle);

		            // echo "<pre>";
		            // print_r($rechargeList);
		            // die();
		            
		            
		        else:
					$error = "Error opening the CSV file";
				    $this->session->set_flashdata('alert_success',$error);
					// redirect(correctLink('ALLUWINVOUCHERDATA',getCurrentControllerPath('index')));
		        endif;
		    else:
				$error = "Error uploading the file";
			    $this->session->set_flashdata('alert_success',$error);
				redirect(correctLink('ALLUWINVOUCHERDATA',getCurrentControllerPath('index')));
		    endif;
		endif;

		$data['ALLDATA']  =  $rechargeList;
		$this->layouts->set_title('Bulk Recharge | Dealz Arabia');
		$this->layouts->admin_view('recharge/allrecharge/checkpreview',array(),$data);
	}	// END OF FUNCTION

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name 	: uploadVoucher
	 + + Developed By 	: Dilip Halder
	 + + Purpose  		: This function used to upload vouchers..
	 + + Date 			: 22 March 2024
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function uploadVoucher()
 	{	
		$this->admin_model->authCheck('view_data');
 		$sl_no        = $this->input->post('sl_no');
 		$pos_id       = $this->input->post('pos_id');
 		$store_name   = $this->input->post('store_name');
 		$bind_with    = $this->input->post('bind_with');
 		$mobile_no    = $this->input->post('mobile_no');
 		$topup        = $this->input->post('topup');
 		// $created_date = $this->input->post('created_date');
 		$created_date = date('Y-m-d H:i');

 		if($mobile_no):
			$user_data = $this->common_model->getDataByParticularField('uw_users', 'users_mobile', (int)$mobile_no);
		endif;
		
		if(!empty($user_data)):
			$this->admin_model->authCheck('add_data');
			$param['load_balance_id']        =	(int)$this->common_model->getNextSequence('uw_loadBalance');
			$param["user_oid"] 				 =	new MongoDB\BSON\ObjectId($user_data['_id']->{'$id'});
			$param["user_id_cred"] 			 =	(int)$user_data['users_id'];
			$param['user_id_deb']			 =	(int)0;
			$param['upoints']				 =	(float)$topup;
			$param['sum_arabian_points']	 =	(float)$topup;
			$param["availableArabianPoints"] =	(float)$user_data["availableArabianPoints"];
			$param["end_balance"] 			 =	(float)$user_data["availableArabianPoints"] + (float)$topup ;
			$param['record_type']			 =	'Credit';
			$param["narration"] 			 =	'Recharge';
			$param['remarks']				 =  'Recharge amount is ' .(float)$topup .' AED' ;
			$param['store_name']			 =	$store_name;
			$param['bind_with']				 =	$bind_with;
			$param['creation_ip']		     =	currentIp();
			$param['created_at']		     =	date('Y-m-d H:i');//currentDateTime();
			$param['status']			     =	'A';
			$param['created_by']		     =	'ADMIN';

			$param["created_user_id"] 	     =	(int)$this->session->userdata('UW_ADMIN_ID');
			$alastInsertId				     =	$this->common_model->addData('uw_loadBalance',$param);
			
			if(!empty($alastInsertId)):
				 
				$totalPoints  = $user_data['totalArabianPoints']     + $param['upoints'];
				$avlPoints 	  = $user_data['availableArabianPoints'] + $param['upoints'];
				
				$udateData    = array(
					'totalArabianPoints'		=>	$totalPoints,
					'availableArabianPoints'	=>	$avlPoints
				);
				$isEdit = $this->common_model->editData('uw_users', $udateData, 'users_id', $user_data['users_id']);

				//Send Creadited Notification to user
				// if($user_data['users_id']):
				// 	$data 		=	array(
				// 		'arabianpoint'	=>	(int)$totalRechargeAmount,
				// 		'name'			=>	'DealzArabia',
				// 		'user_id'		=>	$user_data["users_id"],
				// 		'device_id'		=>	$user_data["device_id"]
				// 		);
				// 	$rtn = $this->notification_model->rceivedArabianPointNotification($data);
				// endif;
			endif;
		endif;
	    $successMessage = $successCount . " items uploaded successfully.";
	    return $successMessage;
	}


}

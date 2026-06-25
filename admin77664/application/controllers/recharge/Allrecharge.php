<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Allrecharge extends CI_Controller {

	public function  __construct() 
	{ 
		parent:: __construct();
		error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_USER_DEPRECATED);
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
		$whereCon              = array();

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
		$dingWhereCon   =   $this->_buildDingRechargeWhereCon($whereCon);
		$shortField 	= 	array('created_at'=> -1);
		$uwCount        =   (int) $this->common_model->getRechargeTopupData('count', $whereCon, 0, 0, $tblName);
		$dingCount      =   (int) $this->common_model->getRechargeTopupData('count', $dingWhereCon, 0, 0, 'uw_loadBalance');
		$totalRows 		=   $uwCount + $dingCount;
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
		
		$data['ALLDATA'] = array();
		if ($totalRows > 0) {
			$fetchLimit = max((int) $totalRows, 1);
			$uwList     = $this->common_model->getRechargeTopupData('', $whereCon, 0, $fetchLimit, $tblName);
			$dingList   = $this->common_model->getRechargeTopupData('', $dingWhereCon, 0, $fetchLimit, 'uw_loadBalance');
			$merged     = $this->_mergeRechargeHistoryLists($uwList, $dingList);
			$data['ALLDATA'] = array_slice($merged, (int) $page, (int) $perPage);
		}
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
		$data['activeSubMenu'] 				= 	'allrechargeUser';

		if($editId):
			$this->admin_model->authCheck('edit_data');
			$editData = $this->common_model->getDataByParticularField('uw_loadBalance','load_balance_id',(int)$editId);
			$data['EDITDATA'] = is_array($editData) ? $editData : array();
		else:
			$this->admin_model->authCheck('add_data');
			$data['EDITDATA'] = array();
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
				if(empty($user_data)):
					$this->session->set_flashdata('alert_error', 'Email ID / Mobile is not registered.');
				elseif($user_data['status'] == 'I'):
					$this->session->set_flashdata('alert_error',lang('ACCOUNT_BLOCKED'));
				elseif(!empty($user_data)):
					$isDingRecharge = ($this->input->post('recharge_type') == 'recharge_point' || $this->input->post('ding_recharge') == '1');
					if($this->input->post('percentage')):
						$percentage 				=	$this->input->post('percentage');
						$percentageAmt				=	$addpoints*$percentage/100;
						$totalRechargeAmount		=	$addpoints + $percentageAmt;
						$rechargeDetails 			=	array(
															'percentage'	=>	(float)$percentage,
															'amount'		=>	(float)$percentageAmt,
															);
					else:
						$totalRechargeAmount		=	$addpoints;
					endif;

					if($isDingRecharge):
						$remarks = $this->input->post('remarks');
						if($remarks == ''):
							$remarks = 'Ding Recharge topup of '.$totalRechargeAmount.' points to '.$user_data['users_name'];
						endif;

						$param['load_balance_id']	  	= (int)$this->common_model->getNextSequence('uw_loadBalance');
						$param['users_id']			    = (int)$user_data['users_id'];
						$param['users_oid'] 			= new MongoDB\BSON\ObjectId($user_data['_id']->{'$id'});
						$param['user_id_cred'] 			= (int)$user_data['users_id'];
						$param['user_id_deb']			= (int)0;
						$param['upoints']				= (float)$totalRechargeAmount;
						$param['availableArabianPoints']= (float)$user_data['availableArabianPoints'];
						$param['end_balance'] 			= (float)$user_data['availableArabianPoints'];
						$param['availableReachargePoints'] = (float)($user_data['availableReachargePoints'] ?? 0);
						$param['end_balance_recharge']  = (float)($user_data['availableReachargePoints'] ?? 0) + (float)$totalRechargeAmount;
						$param['record_type']			= 'Credit';
						$param['narration'] 			= 'Ding Recharge Topup';
						$param['remarks']				= $remarks;
						if($this->input->post('percentage')):
							$param['rechargeDetails']	= $rechargeDetails;
						endif;
						$param['creation_ip']		    = currentIp();
						$param['created_at']		    = strtotime(date('Y-m-d H:i:s'));
						$param['created_by']			= (int)$this->session->userdata('UW_ADMIN_ID');
						$param['status']				= 'A';
						$alastInsertId					= $this->common_model->addData('uw_loadBalance', $param);

						if(!empty($alastInsertId)):
							$udateData = array(
								'totalReachargePoints'      => (float)($user_data['totalReachargePoints'] ?? 0) + (float)$totalRechargeAmount,
								'availableReachargePoints'  => (float)($user_data['availableReachargePoints'] ?? 0) + (float)$totalRechargeAmount,
							);
							$this->common_model->editData('uw_users', $udateData, '_id', new MongoDB\BSON\ObjectID($user_data['_id']->{'$id'}));
						endif;
					else:
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
		$dingWhereCon = $this->_buildDingRechargeWhereCon($whereCon);
		$shortField   =  array('created_at'=> -1);
		$uwCount      = (int) $this->common_model->getRechargeTopupData('count',$whereCon,0,0,$tblName);
		$dingCount    = (int) $this->common_model->getRechargeTopupData('count',$dingWhereCon,0,0,$tblName);
		$totalRows 	  =  $uwCount + $dingCount;
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
		$dingWhereCon = $this->_buildDingRechargeWhereCon($whereCon);
		$shortField   =  array('created_at'=> -1);
		$fetchLimit   = max((int)($startIndex + $itemsPerPage), 1);
		$uwList       = $this->common_model->getRechargeTopupData('',$whereCon,0,$fetchLimit,$tblName);
		$dingList     = $this->common_model->getRechargeTopupData('',$dingWhereCon,0,$fetchLimit,$tblName);
		$merged       = $this->_mergeRechargeHistoryLists($uwList, $dingList);
		$rechagreData = array_slice($merged, $startIndex, $itemsPerPage);
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
	$user = $_POST['user'];
	$isDingRecharge = (!empty($_POST['recharge_type']) && $_POST['recharge_type'] == 'recharge_point') || !empty($_POST['ding_recharge']);

	if (is_numeric($user)) {
		$user_data = $this->common_model->getDataByParticularField('uw_users', 'users_mobile', (int)$user);
	}else{		
		$user_data = $this->common_model->getDataByParticularField('uw_users', 'users_email', $user);	
	}
	if(!empty($user_data)):
		if($isDingRecharge):
			$rechargePoints = isset($user_data['availableReachargePoints']) ? (float)$user_data['availableReachargePoints'] : 0;
			echo $user_data['users_name'].' (' .strtolower($user_data['users_type']). ') available recharge points is '.number_format($rechargePoints, 2).'__'.$user_data['users_id'];
		elseif($user_data['availableArabianPoints'] !== false):
			echo $user_data['users_name'].' (' .strtolower($user_data['users_type']). ') available arabian points is '.number_format($user_data['availableArabianPoints'],2).'__'.$user_data['users_id']; 
		else:
			echo "Email ID / Mobile is not registered.";
		endif;
	else:
		echo "Email ID / Mobile is not registered.";
	endif;

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
	public function reverse($id='', $type='upoint'){
		
		$error = 'NO';
		if($id == ''):
			$this->session->set_flashdata('alert_error','Data not found.');
			redirect('recharge/allrecharge/index');
		endif;

		if ($type === 'ding') {
			$this->_reverseDingRecharge((int) $id);
			return;
		}

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
		$data['activeMenu'] 				= 'recharge';
		$data['activeSubMenu'] 				= 'allrecharge';
		$rechargeList                       = array();
		$rechargeType                       = $this->input->post('recharge_type');
		if ($rechargeType !== 'recharge_point') {
			$rechargeType = 'upoint';
		}
		$data['recharge_type']              = $rechargeType;

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
		            while(($csvRow = fgetcsv($handle, 1000, ",")) !== false) {
		            	//if date is not pass than picking current date & time... 
		            	$date = !empty($csvRow[6]) ? $csvRow[6] : date('d M Y h:i A');
		            	$timestamp = strtotime(str_replace('/', '-',$date));
						$formatted_date = date('d M Y h:i A', $timestamp);

		            	if($i > 0):
		            		$param['sl_no']      	=	$csvRow[0];
		            		$param['pos_id']        =	$csvRow[1];
		            		$param['store_name']    =	$csvRow[2];
		            		$param['bind_with'] 	=	$csvRow[3];
		            		$param['mobile_no'] 	=	$csvRow[4];
		            		$param['topup'] 	    =	$csvRow[5];
		            		$param['created_date'] 	=	$formatted_date;
		            		$param['recharge_type'] =	$rechargeType;
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
 		$rechargeType = $this->input->post('recharge_type');
 		$isDingRecharge = ($rechargeType === 'recharge_point');

 		if($mobile_no):
			$user_data = $this->common_model->getDataByParticularField('uw_users', 'users_mobile', (int)$mobile_no);
		endif;
		
		if(!empty($user_data)):
			$this->admin_model->authCheck('add_data');
			$topupAmount = (float) $topup;

			if ($isDingRecharge) {
				$param['load_balance_id']          = (int) $this->common_model->getNextSequence('uw_loadBalance');
				$param['users_id']                 = (int) $user_data['users_id'];
				$param['users_oid']                = new MongoDB\BSON\ObjectId($user_data['_id']->{'$id'});
				$param['user_id_cred']             = (int) $user_data['users_id'];
				$param['user_id_deb']              = (int) 0;
				$param['upoints']                  = $topupAmount;
				$param['availableArabianPoints']   = (float) ($user_data['availableArabianPoints'] ?? 0);
				$param['end_balance']              = (float) ($user_data['availableArabianPoints'] ?? 0);
				$param['availableReachargePoints'] = (float) ($user_data['availableReachargePoints'] ?? 0);
				$param['end_balance_recharge']     = (float) ($user_data['availableReachargePoints'] ?? 0) + $topupAmount;
				$param['record_type']              = 'Credit';
				$param['narration']                = 'Ding Recharge Topup';
				$param['remarks']                  = 'Bulk ding recharge amount is ' . $topupAmount . ' AED';
				$param['store_name']               = $store_name;
				$param['bind_with']                = $bind_with;
				$param['creation_ip']              = currentIp();
				$param['created_at']               = strtotime(date('Y-m-d H:i:s'));
				$param['created_by']               = (int) $this->session->userdata('UW_ADMIN_ID');
				$param['status']                   = 'A';
				$alastInsertId                     = $this->common_model->addData('uw_loadBalance', $param);

				if (!empty($alastInsertId)) {
					$udateData = array(
						'totalReachargePoints'     => (float) ($user_data['totalReachargePoints'] ?? 0) + $topupAmount,
						'availableReachargePoints' => (float) ($user_data['availableReachargePoints'] ?? 0) + $topupAmount,
					);
					$this->common_model->editData('uw_users', $udateData, '_id', new MongoDB\BSON\ObjectID($user_data['_id']->{'$id'}));
				}
			} else {
			$param['load_balance_id']        =	(int)$this->common_model->getNextSequence('uw_loadBalance');
			$param["user_oid"] 				 =	new MongoDB\BSON\ObjectId($user_data['_id']->{'$id'});
			$param["user_id_cred"] 			 =	(int)$user_data['users_id'];
			$param['user_id_deb']			 =	(int)0;
			$param['upoints']				 =	$topupAmount;
			$param['sum_arabian_points']	 =	$topupAmount;
			$param["availableArabianPoints"] =	(float)$user_data["availableArabianPoints"];
			$param["end_balance"] 			 =	(float)$user_data["availableArabianPoints"] + $topupAmount ;
			$param['record_type']			 =	'Credit';
			$param["narration"] 			 =	'Recharge';
			$param['remarks']				 =  'Recharge amount is ' .$topupAmount .' AED' ;
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
			endif;
			}
		endif;
	    echo 'OK';
	    die();
	}

	private function _getRechargeHistorySortTime($row)
	{
		$createdAt = isset($row['created_at']) ? $row['created_at'] : 0;
		if (is_numeric($createdAt)) {
			return (int) $createdAt;
		}
		return (int) strtotime((string) $createdAt);
	}

	private function _mergeRechargeHistoryLists($uwList, $dingList)
	{
		$uwList   = is_array($uwList) ? $uwList : array();
		$dingList = is_array($dingList) ? $dingList : array();

		foreach ($uwList as &$row) {
			$row['recharge_type']       = 'upoint';
			$row['recharge_type_label'] = 'UPOINT';
		}
		unset($row);

		foreach ($dingList as &$row) {
			$row['recharge_type']       = 'ding';
			$row['recharge_type_label'] = 'Ding';
			if (!isset($row['created_user_id']) && isset($row['created_by'])) {
				$row['created_user_id'] = (int) $row['created_by'];
			}
			$row['created_by'] = 'ADMIN';
		}
		unset($row);

		$merged = array_merge($uwList, $dingList);
		usort($merged, function ($a, $b) {
			return $this->_getRechargeHistorySortTime($b) - $this->_getRechargeHistorySortTime($a);
		});
		return $merged;
	}

	private function _buildDingRechargeWhereCon($uwWhereCon)
	{
		$dingWhereCon = array(
			'where' => array(
				'$or' => array(
					array(
						'record_type' => 'Credit',
						'narration'   => 'Ding Recharge Topup',
					),
					array(
						'record_type' => 'Debit',
						'narration'   => 'Reverse Ding Recharge Topup',
					),
				),
			),
		);

		if (empty($uwWhereCon['where']) || !is_array($uwWhereCon['where'])) {
			return $dingWhereCon;
		}

		foreach ($uwWhereCon['where'] as $key => $value) {
			if ($key === 'narration' || $key === '$or') {
				continue;
			}
			if ($key === 'record_type') {
				$dingWhereCon['where']['$or'] = $this->_getDingRechargeNarrationFiltersByRecordType($value);
				continue;
			}
			if ($key === 'created_at') {
				if (isset($value['$gte'])) {
					$dingWhereCon['where']['created_at']['$gte'] = is_numeric($value['$gte']) ? (int) $value['$gte'] : strtotime($value['$gte']);
				}
				if (isset($value['$lte'])) {
					$dingWhereCon['where']['created_at']['$lte'] = is_numeric($value['$lte']) ? (int) $value['$lte'] : strtotime($value['$lte']);
				}
				continue;
			}
			if ($key === 'user_id_deb') {
				$dingWhereCon['where']['created_by'] = (int) $value;
				continue;
			}
			$dingWhereCon['where'][$key] = $value;
		}

		return $dingWhereCon;
	}

	private function _getDingRechargeNarrationFiltersByRecordType($recordType)
	{
		if ($recordType === 'Credit') {
			return array(
				array(
					'record_type' => 'Credit',
					'narration'   => 'Ding Recharge Topup',
				),
			);
		}
		if ($recordType === 'Debit') {
			return array(
				array(
					'record_type' => 'Debit',
					'narration'   => 'Reverse Ding Recharge Topup',
				),
			);
		}

		return array(
			array(
				'record_type' => 'Credit',
				'narration'   => 'Ding Recharge Topup',
			),
			array(
				'record_type' => 'Debit',
				'narration'   => 'Reverse Ding Recharge Topup',
			),
		);
	}

	private function _reverseDingRecharge($id)
	{
		$this->admin_model->authCheck('edit_data');

		$wcon['where'] = array(
			'load_balance_id' => (int) $id,
			'narration'       => 'Ding Recharge Topup',
			'record_type'     => 'Credit',
			'status'          => 'A',
		);
		$data = $this->common_model->getData('single', 'uw_loadBalance', $wcon);
		if (empty($data)) {
			$this->session->set_flashdata('alert_error', 'Data not found.');
			redirect('recharge/allrecharge/index');
			return;
		}

		$reverseAmount = (float) $data['upoints'];
		$tblName         = 'uw_users';
		$whereCon        = array('where' => array('users_id' => (int) $data['user_id_cred']));
		$userdata        = $this->common_model->getData('single', $tblName, $whereCon);

		if (empty($userdata)) {
			$this->session->set_flashdata('alert_error', 'User not found');
			redirect('recharge/allrecharge/index');
			return;
		}

		$this->common_model->editMultipleDataByMultipleCondition('uw_loadBalance', array('status' => 'R'), array(
			'load_balance_id' => (int) $id,
			'narration'       => 'Ding Recharge Topup',
		));

		$availableRecharge = (float) ($userdata['availableReachargePoints'] ?? 0);
		$totalRecharge     = (float) ($userdata['totalReachargePoints'] ?? 0);
		$this->common_model->editData('uw_users', array(
			'availableReachargePoints' => max(0, $availableRecharge - $reverseAmount),
			'totalReachargePoints'     => max(0, $totalRecharge - $reverseAmount),
		), '_id', new MongoDB\BSON\ObjectID($userdata['_id']->{'$id'}));

		$param = array(
			'load_balance_id'            => (int) $this->common_model->getNextSequence('uw_loadBalance'),
			'users_id'                   => (int) $userdata['users_id'],
			'users_oid'                  => new MongoDB\BSON\ObjectId($userdata['_id']->{'$id'}),
			'user_id_cred'               => (int) 0,
			'user_id_deb'                => (int) $userdata['users_id'],
			'upoints'                    => $reverseAmount,
			'availableArabianPoints'     => (float) ($userdata['availableArabianPoints'] ?? 0),
			'end_balance'                => (float) ($userdata['availableArabianPoints'] ?? 0),
			'availableReachargePoints'   => max(0, $availableRecharge - $reverseAmount),
			'end_balance_recharge'       => max(0, $availableRecharge - $reverseAmount),
			'record_type'                => 'Debit',
			'narration'                  => 'Reverse Ding Recharge Topup',
			'remarks'                    => 'Ding recharge reversed for ' . $userdata['users_mobile'],
			'creation_ip'                => currentIp(),
			'created_at'                 => strtotime(date('Y-m-d H:i:s')),
			'created_by'                 => (int) $this->session->userdata('UW_ADMIN_ID'),
			'status'                     => 'R',
		);
		$this->common_model->addData('uw_loadBalance', $param);

		$this->session->set_flashdata('alert_success', 'Ding Recharge Reverse Successfully.');
		redirect('recharge/allrecharge/index');
	}


}

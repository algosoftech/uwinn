<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ding_account_recharge extends CI_Controller {

	public function  __construct() 
	{ 
		parent:: __construct();
		error_reporting(E_ALL ^ E_NOTICE);  
		$this->load->model(array('admin_model','emailtemplate_model','sms_model','notification_model','geneal_model'));
		$this->lang->load('statictext', 'admin');
		$this->load->helper('common');
	} 

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name 	: index
	 + + Developed By 	: Dilip Halder
	 + + Purpose  		: This function used for index
	 + + Date 			: 20  March 2026
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	 public function index()
	{	
		$this->admin_model->authCheck('view_data');
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'campaigns';
		$data['activeSubMenu'] 				= 	'alltambolagame';
		
		if($this->input->get('searchField') && $this->input->get('searchValue')):
			$sField							=	trim((string) $this->input->get('searchField'));
			$sValue							=	trim((string) $this->input->get('searchValue'));
			
			if($sField === 'users_first_name' || $sField === 'users_name' || $sField === 'users_mobile'):
				$userWhere = array();
				if($sField === 'users_mobile' && is_numeric($sValue)):
					$userWhere['where'] = array('users_mobile' => (int) $sValue);
				else:
					$nameField = ($sField === 'users_first_name') ? 'users_name' : $sField;
					$userWhere['like'] = array('0'=>$nameField,'1'=>$sValue);
				endif;
				$usersData = $this->common_model->getData('multiple','uw_users',$userWhere);
				$userIds = array();
				if(!empty($usersData) && is_array($usersData)):
					foreach($usersData as $u):
						if(isset($u['users_id'])):
							$userIds[] = (int) $u['users_id'];
						endif;
					endforeach;
				endif;
				$userIds = array_values(array_unique($userIds));
				if(!empty($userIds)):
					$whereCon['where_in'] = array('users_id', $userIds);
				else:
					$whereCon['where'] = array('users_id' => -1);
				endif;
			else:
				if(is_numeric($sValue)):
					$whereCon['where'] = array($sField => (int)$sValue);		
				else:
					$whereCon['like'] = array('0'=>$sField,'1'=>$sValue);
				endif;
			endif;

			$data['searchField'] 			= 	$sField;
			$data['searchValue'] 			= 	$sValue;
		else:
			$whereCon['like']		 		= 	"";
			$data['searchField'] 			= 	'';
			$data['searchValue'] 			= 	'';
		endif;
		$whereCon['where']['narration']	    =   'International Recharge';
		$shortField 						= 	array('_id'=>-1);
		$baseUrl 							= 	getCurrentControllerPath('index');
		$this->session->set_userdata('ALLTOMBOLAGAMEDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	isset($qStringdata[1]) && $qStringdata[1] ? '?'.$qStringdata[1] : '';
		$tblName 							= 	'loadBalance';
		$con 								= 	'';
		
		$totalRows 							= 	$this->common_model->getData('count',$tblName,$whereCon,$shortField,'0','0');
		
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
		//  echo '<pre>';print_r($data['ALLDATA']);die;
		$this->layouts->set_title('All Ding Account Recharge | International Recharge | Instwin');
		$this->layouts->admin_view('international_recharge/ding/ding_account_recharge/index',array(),$data);
	}	// END OF FUNCTION

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name : addeditdata
	 + + Developed By  : Dilip Halder
	 + + Purpose  	   : This function used for Add Edit data
	 + + Date 		   : 30 January 2026
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	 public function addeditdata($editId='')
	{		
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'recharge';
		$data['activeSubMenu'] 				= 	'addeditdata';

		if($editId):
			$this->admin_model->authCheck('edit_data');
			$data['EDITDATA']				=	$this->common_model->getDataByParticularField('loadBalance','load_balance_id',(int)$editId);
		else:
			$this->admin_model->authCheck('add_data');
		endif;
		if($this->input->post('SaveChanges')):
			$error					=	'NO';
			//echo '<pre>';print_r($this->input->post());die();
			$this->form_validation->set_rules('user', 'Email ID / Mobile No.', 'trim|required');
			$this->form_validation->set_rules('userID', 'Invalid User ID', 'trim|required');
			$this->form_validation->set_rules('rechange_for', 'Invalid Rechange For', 'trim|required');
			$this->form_validation->set_rules('addRechargePoints', 'Add Recharge Points', 'trim|required');

			if($this->form_validation->run() && $error == 'NO'): 
				$user 			= $this->input->post('user');
				$rechange_for 	= $this->input->post('rechange_for');
				$addPoints      = (int)$this->input->post('addRechargePoints');

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
					if($this->input->post('remarks')):
						$remarks = 'International Recharge of '.$addPoints.' points to '.$user_data['users_name'].' ('.(is_numeric($user_data['users_mobile']) ? $user_data['users_mobile'] : $user_data['users_email']).') - '.$this->input->post('remarks');
					else:
						$remarks = 'International Recharge of '.$addPoints.' points to '.$user_data['users_name'].' ('.(is_numeric($user_data['users_mobile']) ? $user_data['users_mobile'] : $user_data['users_email']).')';
					endif;
					
					$param['load_balance_id']	  	= (int)$this->common_model->getNextSequence('loadBalance');
					$param["users_id"] 			    = (int)$user_data['users_id'];
					$param["users_oid"] 			= new MongoDB\BSON\ObjectId($user_data['_id']->{'$id'});
					$param["user_id_cred"] 			= (int)$user_data['users_id'];
					$param['user_id_deb']			= (int)0;
					$param['upoints']				= (float)$addPoints;
					$param['availableArabianPoints']= (float)$user_data['availableArabianPoints'];
					$param["end_balance"] 			= (float)$user_data["availableArabianPoints"] ;
					$param['availablerechargeArabianPoints'] = (float)$user_data['availableReachargePoints'];
					$param["end_balance_recharge"]  = (float)$user_data["availableReachargePoints"]+ (float)$addPoints;
					$param['record_type']			= 'Credit';
					$param["narration"] 			= 'International Recharge';
					$param['remarks']				= $remarks;
					$param['creation_ip']		    = currentIp();
					$param['created_at']		    = date('Y-m-d H:i:s');
					$param['created_by']			= $this->session->userdata('ADMIN_NAME');
					$param['status']				= 'A';

					$alastInsertId					= $this->common_model->addData('loadBalance',$param);

					if(!empty($alastInsertId)):
						$udateData['totalReachargePoints']       = (float)$user_data['totalReachargePoints']  + (float)$addPoints;
						$udateData['availableReachargePoints']   = $user_data['availableReachargePoints'] + $addPoints;
						$isEdit = $this->common_model->editData('uw_users', $udateData, '_id', new MongoDB\BSON\ObjectID($user_data['_id']->{'$id'}));
					endif;
					$this->session->set_flashdata('alert_success',lang('RECHARGE_SUCCESFULLY'));

				endif;
					
				//echo '<pre>'; print_r($param);die();

				redirect(correctLink('MASTERDATARECHARGETYPE',getCurrentControllerPath('index')));
			endif;
		endif;
		$this->layouts->set_title('Add/Edit Recharge System | Instwin');
		$this->layouts->admin_view('international_recharge/ding/ding_account_recharge/addeditdata',array(),$data);
	}	// END OF FUNCTION			

	/***********************************************************************
	** Function name 	: changestatus
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for change status
	** Date 			: 30 January 2026
	************************************************************************/
	function changestatus($changeStatusId='',$statusType='')
	{  
		$this->admin_model->authCheck('edit_data');
		$param['status'] =	$statusType;
		$this->common_model->editData('tambola_games',$param,'_id', new MongoDB\BSON\ObjectID($changeStatusId));
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		
		redirect(correctLink('DINGACCOUNTRECHARGEDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name 	: deletedata
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for delete data
	** Date 			: 30 January 2026
	************************************************************************/
	function deletedata($deleteId='')
	{  
		$this->admin_model->authCheck('delete_data');
		$this->common_model->deleteData('tambola_games','_id', new MongoDB\BSON\ObjectID($deleteId));
		$this->session->set_flashdata('alert_success',lang('deletesuccess'));
		redirect(correctLink('DINGACCOUNTRECHARGEDATA',getCurrentControllerPath('index')));
	}
	
	/***********************************************************************
	** Function name : exportexcel
	** Developed By  : Dilip Halder
	** Purpose       : This function used for export deleted users data
	** Date          : 30 January 2026
	************************************************************************/
	function exportexcel()
	{  
		$this->admin_model->authCheck('view_data');
		$data['error'] 		   = '';
		$data['activeMenu']    = 'international_recharge';
		$data['activeSubMenu'] = 'ding_account_recharge';
		$this->common_model->generateLogs();

		$fromDateRaw = trim((string) $this->input->post('fromDate'));
		$toDateRaw = trim((string) $this->input->post('toDate'));
		$searchField = trim((string) $this->input->post('searchField'));
		$searchValue = trim((string) $this->input->post('searchValue'));
		$fromDate = $fromDateRaw !== '' ? date('Y-m-d H:i:s', strtotime($fromDateRaw)) : '';
		$toDate = $toDateRaw !== '' ? date('Y-m-d H:i:s', strtotime($toDateRaw)) : '';

		$whereCondition = $this->dingAccountRechargeExportWhere($fromDate, $toDate, $searchField, $searchValue);
		$totalRows = (int) $this->common_model->getData('count', 'loadBalance', $whereCondition);
		$itemsPerPage = 5000;
		$totalPages = $totalRows > 0 ? (int) ceil($totalRows / $itemsPerPage) : 1;

		$data['current_page'] = 1;
		$data['total_page'] = $totalPages;
		$data['searchField'] = $searchField;
		$data['searchValue'] = $searchValue;
		$data['fromDate'] = $fromDate;
		$data['toDate'] = $toDate;
		$data['cancelled_order'] = '';

		$this->layouts->set_title('Export CSV | Ding Account Recharge | International Recharge | Instwin');
		$this->layouts->admin_view('international_recharge/ding/ding_account_recharge/exportexcel', array(), $data);
	}

	function exportexcelApi()
	{
		$this->admin_model->authCheck('view_data');
		$fromDate = trim((string) $this->input->post('fromDate'));
		$toDate = trim((string) $this->input->post('toDate'));
		$searchField = trim((string) $this->input->post('searchField'));
		$searchValue = trim((string) $this->input->post('searchValue'));
		$whereCondition = $this->dingAccountRechargeExportWhere($fromDate, $toDate, $searchField, $searchValue);

		$page = (int) $this->input->post('pageno');
		if ($page < 1) {
			$page = 1;
		}
		$itemsPerPage = 5000;
		$startIndex = ($page - 1) * $itemsPerPage;
		$shortField = array('_id' => -1);
		$rows = $this->common_model->getData('multiple', 'loadBalance', $whereCondition, $shortField, $itemsPerPage, $startIndex);
		if (!is_array($rows)) {
			$rows = array();
		}

		$CSVData = array();
		foreach ($rows as $itemsArray):
			$statusLabel = 'N/A';
			if (isset($itemsArray['status'])):
				if ($itemsArray['status'] === 'A'):
					$statusLabel = 'Active';
				elseif ($itemsArray['status'] === 'I'):
					$statusLabel = 'Inactive';
				elseif ($itemsArray['status'] === 'D'):
					$statusLabel = 'Deleted';
				else:
					$statusLabel = (string) $itemsArray['status'];
				endif;
			endif;

			$CSVData1['Users ID'] = !empty($itemsArray['users_id']) ? (int)$itemsArray['users_id'] : 'N/A';
			$CSVData1['Remarks'] = !empty($itemsArray['remarks']) ? $itemsArray['remarks'] : 'N/A';
			$CSVData1['Amount'] = isset($itemsArray['upoints']) ? number_format((float)$itemsArray['upoints'], 2, '.', '') : '0.00';
			$CSVData1['Available Recharge Amount'] = isset($itemsArray['availablerechargeArabianPoints']) ? number_format((float)$itemsArray['availablerechargeArabianPoints'], 2, '.', '') : '0.00';
			$CSVData1['End Recharge Amount'] = isset($itemsArray['end_balance_recharge']) ? number_format((float)$itemsArray['end_balance_recharge'], 2, '.', '') : '0.00';
			$CSVData1['Created Date'] = !empty($itemsArray['created_at']) ? date('d-M-Y h:i A', strtotime((string)$itemsArray['created_at'])) : 'N/A';
			$CSVData1['Status'] = $statusLabel;
			$CSVData[] = $CSVData1;
		endforeach;

		echo json_encode($CSVData);
		die();
	}

	private function dingAccountRechargeExportWhere($fromDate = '', $toDate = '', $searchField = '', $searchValue = '')
	{
		$whereCon = array();
		$whereCon['where']['narration'] = 'International Recharge';

		if ($searchField !== '' && $searchValue !== ''):
			if ($searchField === 'users_first_name' || $searchField === 'users_name' || $searchField === 'users_mobile'):
				$userWhere = array();
				if ($searchField === 'users_mobile' && is_numeric($searchValue)):
					$userWhere['where'] = array('users_mobile' => (int) $searchValue);
				else:
					$nameField = ($searchField === 'users_first_name') ? 'users_name' : $searchField;
					$userWhere['like'] = array('0' => $nameField, '1' => $searchValue);
				endif;
				$usersData = $this->common_model->getData('multiple', 'uw_users', $userWhere);
				$userIds = array();
				if (!empty($usersData) && is_array($usersData)):
					foreach ($usersData as $u):
						if (isset($u['users_id'])):
							$userIds[] = (int) $u['users_id'];
						endif;
					endforeach;
				endif;
				$userIds = array_values(array_unique($userIds));
				if (!empty($userIds)):
					$whereCon['where_in'] = array('users_id', $userIds);
				else:
					$whereCon['where']['users_id'] = -1;
				endif;
			else:
				if (is_numeric($searchValue)):
					$whereCon['where'][$searchField] = (int)$searchValue;
				else:
					$whereCon['like'] = array('0' => $searchField, '1' => $searchValue);
				endif;
			endif;
		endif;

		if ($fromDate !== ''):
			$whereCon['where_gte'] = array(array('created_at', date('Y-m-d H:i:s', strtotime($fromDate))));
		endif;
		if ($toDate !== ''):
			$whereCon['where_lte'] = array(array('created_at', date('Y-m-d H:i:s', strtotime($toDate))));
		endif;

		return $whereCon;
	}


	/***********************************************************************
	** Function name : checkDeplicacy
	** Developed By  : Dilip Halder
	** Purpose       : This function used for check duplicate entry
	** Date          : 27 March 2026
	************************************************************************/
	function checkDeplicacy()
	{
	
		$userData 	= $this->input->post('user');
		if (is_numeric($userData)) {
			$user_data = $this->common_model->getDataByParticularField('uw_users', 'users_mobile', (int)$userData);
		}else{		
			$user_data = $this->common_model->getDataByParticularField('uw_users', 'users_email', $userData);	
		}

		if(!empty($user_data) && ($user_data['availableReachargePoints'] !== false)){
			$availableRechargePoints = abs((float) $user_data['availableReachargePoints']);
			echo $user_data['users_name'].' (' .strtolower($user_data['users_type']). ') available recharge amount is '.number_format($availableRechargePoints,2).'__'.$user_data['users_id']; 
		}else{
			echo "Email ID / Mobile is not registered.";
		} 

		die();
	}

}
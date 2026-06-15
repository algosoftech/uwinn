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

class Allrechargecoupons extends CI_Controller {

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
	 + + Date 			: 28 March 2024
	 + + Updated Date 	: 
	 + + Updated By   	:
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index()
	{	
		$this->admin_model->authCheck('view_data');
		$data['error'] 					= 	'';
		$data['activeMenu'] 			= 	'recharge';
		$data['activeSubMenu'] 			= 	'allrechargecoupons';

		if($this->input->get('fromDate')):
			$data['fromDate'] 				=   date('Y-m-d H:i', strtotime($this->input->get('fromDate')));  //2023-03-16 15:13
			$whereCon['where_gte'] 			= 	array(array("created_date",strtotime($this->input->get('fromDate'))));
		endif;

		if($this->input->get('toDate')):
			$data['toDate'] 				=   date('Y-m-d H:i', strtotime($this->input->get('toDate')));  //2023-03-16 15:13
			$whereCon['where_lte'] 			= 	array(array("created_date",  strtotime($this->input->get('toDate'))));
		endif;

		if($this->input->get('searchField') && $this->input->get('searchValue')):
			$sField						=	$this->input->get('searchField');
			$sValue						=	$this->input->get('searchValue');
			$data['searchField'] 		= 	$sField;
			$data['searchValue'] 		= 	$sValue;

			//Coupon generating details start..

			 	if($sField  == 'generated_by'):
				 	if(is_numeric($sValue)):
						$whereCon1['where'] 	=  array('admin_phone'  => (int)$sValue); 	 
					else:
						$whereCon1['like'] 	=  array('0'=>'admin_email','1' => $sValue); 	 
					endif;
					$result = $this->common_model->getData('single','uw_admin',$whereCon1);
					if($result):
					  $sField = 'created_by';
					  $sValue = (int)$result['admin_id'];
					endif;
				endif;

				if($sField  == 'generated_by' && empty($result)):
					if(is_numeric($sValue)):
						$whereCon2['where_or'] 	=  array( 'pos_number'    => (int)$sValue , 'users_mobile'  => (int)$sValue 
						
						); 	 
					else:
						$whereCon2['like'] 	    =  array('0'=>'pos_device_id','1' => $sValue); 	 
					endif;
					
					$result = $this->common_model->getData('single','uw_users',$whereCon2);
					if($result):
						$sField = 'created_by';
						$sValue = (int)$result['users_id'];
					endif;
				endif;

			//Coupon generating details end..

			//Redeemed By details start..

				if($sField  == 'redeemed_by'):
				 	if(is_numeric($sValue)):
						$whereCon2['where'] 	=  array('users_mobile'  => (int)$sValue); 	 
					else:
						$whereCon2['like'] 	=  array('0'=>'users_email','1' => $sValue); 	 
					endif;
					
					$result = $this->common_model->getData('single','uw_users',$whereCon2);
					if($result):
						$sField = 'redeemed_by';
						$sValue = $result['users_id'];
					endif;
				endif;

			//Redeemed By details end..

			if(is_numeric($sValue)):
				$whereCon['where'] 	=  array($sField  => (int)$sValue); 	 
			else:
				$whereCon['where'] 	=  array($sField  => $sValue); 	 
				// $whereCon['like'] 	=  array('0'=>$sField , '1' => $sValue); 	 
			endif;

		endif;

		$baseUrl 							= 	getCurrentControllerPath('index');
		$this->session->set_userdata('ALLCOUPONSDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';



		$shortField 						= 	array('_id' => -1 );
		$tblName 							= 	'uw_coupon_code_only';
		$totalRows 							= 	$this->common_model->getData('count',$tblName,$whereCon,$shortField,'0','0');
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
           $page 							= $this->uri->segment(getUrlSegment());
       else:
           $page 							= 0;
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
		// echo "<pre>";print_r($data['ALLDATA']);die();
		
		$this->layouts->set_title('All Coupons | UWINN');
		$this->layouts->admin_view('rechargecoupons/allrechargecoupons/index',array(),$data);
	}	// END OF FUNCTION

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name : addeditdata
	 + + Developed By  : Dilip Halder
	 + + Purpose  	   : This function used for Add recharge
	 + + Date 		   : 19 November 2024
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function addeditdata($editId='')
	{		
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'recharge';
		$data['activeSubMenu'] 				= 	'allrechargecoupons';
		// $userWcon['where']					=	array('users_type' => array('$ne' => 'Users'), 'status' => 'A');
		// $data['ALLUSERS']					=	$this->common_model->getData('multiple','uw_users',$userWcon,array('users_name'=>'asc'));

		if($editId):
			$this->admin_model->authCheck('edit_data');
			$data['EDITDATA']				=	$this->common_model->getDataByParticularField('uw_rechargecoupons','rc_id',(int)$editId);
		else:
			$this->admin_model->authCheck('add_data');
		endif;
		
		if($this->input->post('SaveChanges')):
			$error					=	'NO';
			//echo 'Oops!! Work in progress. Please wait.';die();
			$this->form_validation->set_rules('aed', 'AED', 'trim');
			$this->form_validation->set_rules('upoints', 'uPoints', 'trim');
			$this->form_validation->set_rules('qty', 'Quantity', 'trim');

			if($this->form_validation->run() && $error == 'NO'): 
				 
				// getting detail for generaing users wise.
				// if(!empty($this->input->post('users'))):
				// 	$userData = $this->common_model->getDataByParticularField('uw_users','users_id',(int)$this->input->post('users')); //Update 05-07-2023
				// endif;	

				if($this->input->post('CurrentDataID') ==''):

					$qty 		 =	$this->input->post('qty');
					A:
					$insertParam = [];
					$couponData  = [];

					$batch_id = $this->common_model->getNextSequence('batch_id');


					for($i=0; $i < $qty; $i++){
						$whereCon['coupon_code']	=	'';
						if($this->input->post('coupon_length')):
							$coupon_code	= (int)$this->input->post('coupon_length');							
						else:
							$coupon_code	= 8;
						endif;
						$code			    = generateRandomString2($coupon_code,"n");

						array_push($couponData,$code);
						$couponParam['batch_id'] 			= 	(int)$batch_id;
						$couponParam['rc_id']				= 	'UTP'.$this->common_model->generateSerialNo('uw_rechargecoupons');
						$couponParam['coupon_code'] 		= 	(int)$code;
						$couponParam['generate_for']		= 	addslashes($this->input->post('generate_for'));
						$couponParam['aed'] 				= 	(int)addslashes($this->input->post('aed'));
						$couponParam['coupon_code_amount'] 	= 	(int)addslashes($this->input->post('upoints'));
						$couponParam['coupon_code_statys']	=	'Active';	//	Redeem / Expired
						if($userData['users_id']):
						 $couponParam['created_for_user_id']=	(int)$userData['users_id'];
						endif;
						$couponParam['status']		        =	"A";
						$couponParam['created_user']		=	"Admin";
						$couponParam['created_by']			=	(int)$this->session->userdata('UW_ADMIN_ID');;
						$couponParam['created_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
						$couponParam['expair_date']		    =	date('Y-m-d',strtotime('+1 years'));
						array_push($insertParam,$couponParam);
					}

					$check 	= 	$this->common_model->checkBulkDuplicate('uw_coupon_code_only',$couponData);
					if($check == 0){
						$this->common_model->addManyData('uw_coupon_code_only',$insertParam);
					}else{
						goto A;
					}
					$this->session->set_flashdata('alert_success',lang('addsuccess'));
				else:
					$couponId					=	$this->input->post('CurrentDataID');
					$param['update_ip']			=	currentIp();
					$param['update_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
					$param['updated_by']		=	(int)$this->session->userdata('UW_ADMIN_ID');
					$this->common_model->editData('uw_rechargecoupons',$param,'rc_id',(int)$couponId);
					$this->session->set_flashdata('alert_success',lang('updatesuccess'));
				endif;

				// redirect(correctLink('MASTERDATAPRIZETYPE',getCurrentControllerPath('index')));
				redirect('recharge/allrechargecoupons/index');
			endif;
		endif;
		
		$this->layouts->set_title('Add Coupons | UWINN');
		$this->layouts->admin_view('rechargecoupons/allrechargecoupons/addeditdata',array(),$data);
	}	// END OF FUNCTION	

	/***********************************************************************
	** Function name 	: changestatus
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for change status
	** Date 			: 08 APRIL 
	** updated by  		: Dilip Halder
	** Date 			: 31 March 2023
	************************************************************************/
	function changestatus($changeStatusId='',$statusType='')
	{  
		$this->admin_model->authCheck('edit_data');
		if($statusType == 'I'):
		  $whereCondition =  array('_id' => new MongoDB\BSON\ObjectId($changeStatusId), 'coupon_code_statys' => 'Active');
		  $param1['coupon_code_statys']	=	"Inactive";
		  $param1['status']				=	"I";
		elseif($statusType == 'A'):
		  $whereCondition =  array('_id' => new MongoDB\BSON\ObjectId($changeStatusId), 'coupon_code_statys' => 'Inactive');
		  $param1['coupon_code_statys']	=	"Active";
		  $param1['status']				=	"A";
		endif;
	  	$this->common_model->editMultipleDataByMultipleCondition('uw_coupon_code_only',$param1,$whereCondition);
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		redirect(correctLink('ALLCOUPONSDATA',getCurrentControllerPath('index')));
	}


	/***********************************************************************
	** Function name 	: deletedata
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for delete data
	** Date 			: 28 March 2024
	************************************************************************/
	function deletedata($deleteId='')
	{  
		$this->admin_model->authCheck('delete_data');
		$this->common_model->deleteData('uw_rechargecoupons','rc_id',(int)$deleteId);
		$this->session->set_flashdata('alert_success',lang('deletesuccess'));
		
		redirect(correctLink('ALLCOUPONSDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name 	: exportexcel
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for export data
	** Date 			: 14 November 2024
	** Updated Date 	: 
	************************************************************************/
	function exportexcel()
	{  
		
		$this->admin_model->authCheck('view_data');
		$data['error'] 		   = '';
		$data['activeMenu']    = 'recharge';
		$data['activeSubMenu'] = 'allrechargecoupons';

		//Generating Logs
		$this->common_model->generateLogs();


		$this->admin_model->authCheck('view_data');
		$data['error'] 					= 	'';
		$data['activeMenu'] 			= 	'recharge';
		$data['activeSubMenu'] 			= 	'allrechargecoupons';

		if($this->input->post('fromDate')):
			$data['fromDate'] 				=   date('Y-m-d H:i', strtotime($this->input->post('fromDate')));  //2023-03-16 15:13
			$whereCon['where_gte'] 			= 	array(array("created_date",strtotime($this->input->post('fromDate'))));
		endif;

		if($this->input->post('toDate')):
			$data['toDate'] 				=   date('Y-m-d H:i', strtotime($this->input->post('toDate')));  //2023-03-16 15:13
			$whereCon['where_lte'] 			= 	array(array("created_date",  strtotime($this->input->post('toDate'))));
		endif;

		if($this->input->post('searchField') && $this->input->post('searchValue')):
			$sField						=	$this->input->post('searchField');
			$sValue						=	$this->input->post('searchValue');
			$data['searchField'] 		= 	$sField;
			$data['searchValue'] 		= 	$sValue;

			//Coupon generating details start..

			 	if($sField  == 'generated_by'):
				 	if(is_numeric($sValue)):
						$whereCon1['where'] 	=  array('admin_phone'  => (int)$sValue); 	 
					else:
						$whereCon1['like'] 	=  array('0'=>'admin_email','1' => $sValue); 	 
					endif;
					$result = $this->common_model->getData('single','uw_admin',$whereCon1);
					if($result):
					  $sField = 'created_by';
					  $sValue = (int)$result['admin_id'];
					endif;
				endif;

				if($sField  == 'generated_by' && empty($result)):
					if(is_numeric($sValue)):
						$whereCon2['where_or'] 	=  array( 'pos_number'    => (int)$sValue , 'users_mobile'  => (int)$sValue 
						
						); 	 
					else:
						$whereCon2['like'] 	    =  array('0'=>'pos_device_id','1' => $sValue); 	 
					endif;
					
					$result = $this->common_model->getData('single','uw_users',$whereCon2);
					if($result):
						$sField = 'created_by';
						$sValue = (int)$result['users_id'];
					endif;
				endif;

			//Coupon generating details end..

			//Redeemed By details start..

				if($sField  == 'redeemed_by'):
				 	if(is_numeric($sValue)):
						$whereCon2['where'] 	=  array('users_mobile'  => (int)$sValue); 	 
					else:
						$whereCon2['like'] 	=  array('0'=>'users_email','1' => $sValue); 	 
					endif;
					
					$result = $this->common_model->getData('single','uw_users',$whereCon2);
					if($result):
						$sField = 'redeemed_by';
						$sValue = $result['users_id'];
					endif;
				endif;

			//Redeemed By details end..

			if(is_numeric($sValue)):
				$whereCon['where'] 	=  array($sField  => (int)$sValue); 	 
			else:
				$whereCon['where'] 	=  array($sField  => $sValue); 	 
				// $whereCon['like'] 	=  array('0'=>$sField , '1' => $sValue); 	 
			endif;

		endif;

		$baseUrl 	  = getCurrentControllerPath('exportexcel');
		$totalRows    = $this->common_model->getData('count','uw_coupon_code_only',$whereCon);

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
 		
		
		$totalpage 				= count($totalpage);
		$data['current_page']   = $current_page;
		$data['total_page'] 	= $totalpage;
		$data['searchField'] 	= $sField;
		$data['searchValue'] 	= $sValue;

		// echo "<pre>";print_r($data);die();
		$this->layouts->set_title('Export CSV | UWINN');
		$this->layouts->admin_view('rechargecoupons/allrechargecoupons/exportexcel',array(),$data);
		 
	}

	/***********************************************************************
	** Function name 	: exportexcelApi
	** Developed By 	: Dilip halder
	** Purpose  		: This function used for export order data
	** Date 			: 23 July 2024
	************************************************************************/
	function exportexcelApi(){
		
		$data['error'] 		   = '';
		$data['activeMenu']    = 'recharge';
		$data['activeSubMenu'] = 'allrechargecoupons';

		$this->admin_model->authCheck('view_data');
		$data['error'] 					= 	'';
		$data['activeMenu'] 			= 	'recharge';
		$data['activeSubMenu'] 			= 	'allrechargecoupons';

		if($this->input->post('fromDate')):
			$data['fromDate']   = date('Y-m-d H:i', strtotime($this->input->post('fromDate')));  //2023-03-16 15:13
			$whereCon['where']['created_date']['$gte']  =  strtotime($this->input->post('fromDate'));
		endif;

		if($this->input->post('toDate')):
			$data['toDate'] 	=   date('Y-m-d H:i', strtotime($this->input->post('toDate')));  //2023-03-16 15:13
			$whereCon['where']['created_date']['$lte']  =  strtotime($this->input->post('toDate'));
		endif;

		if($this->input->post('searchField') && $this->input->post('searchValue')):
			$sField						=	$this->input->post('searchField');
			$sValue						=	$this->input->post('searchValue');
			$data['searchField'] 		= 	$sField;
			$data['searchValue'] 		= 	$sValue;

			//Coupon generating details start..

			 	if($sField  == 'generated_by'):
				 	if(is_numeric($sValue)):
						$whereCon1['where'] 	=  array('admin_phone'  => (int)$sValue); 	 
					else:
						$whereCon1['like'] 	=  array('0'=>'admin_email','1' => $sValue); 	 
					endif;
					$result = $this->common_model->getData('single','uw_admin',$whereCon1);
					if($result):
					  $sField = 'created_by';
					  $sValue = (int)$result['admin_id'];
					endif;
				endif;

				if($sField  == 'generated_by' && empty($result)):
					if(is_numeric($sValue)):
						$whereCon2['where_or'] 	=  array( 'pos_number'    => (int)$sValue , 'users_mobile'  => (int)$sValue 
						
						); 	 
					else:
						$whereCon2['like'] 	    =  array('0'=>'pos_device_id','1' => $sValue); 	 
					endif;
					
					$result = $this->common_model->getData('single','uw_users',$whereCon2);
					if($result):
						$sField = 'created_by';
						$sValue = (int)$result['users_id'];
					endif;
				endif;

			//Coupon generating details end..

			//Redeemed By details start..

				if($sField  == 'redeemed_by'):
				 	if(is_numeric($sValue)):
						$whereCon2['where'] 	=  array('users_mobile'  => (int)$sValue); 	 
					else:
						$whereCon2['like'] 	=  array('0'=>'users_email','1' => $sValue); 	 
					endif;
					
					$result = $this->common_model->getData('single','uw_users',$whereCon2);
					if($result):
						$sField = 'redeemed_by';
						$sValue = $result['users_id'];
					endif;
				endif;

			//Redeemed By details end..

			if(is_numeric($sValue)):
				$whereCon['where'][$sField] 	=  (int)$sValue; 	 
			else:
				$whereCon['where'][$sField] 	=  $sValue; 	 
			endif;

		endif;

		

		$baseUrl 	        = getCurrentControllerPath('exportexcel');
		$page 		  		= $this->input->post('pageno');
		// $page = 1;
 		$itemsPerPage 		= 5000;
 		$startIndex   		= ($page - 1)*$itemsPerPage;
 		$resultType   		= '';
		
 		$tblName            = 'uw_coupon_code_only';
		$rechagreData       = $this->common_model->getrechargeDetails($tblName,$whereCon);
		// echo "<pre>";print_r($rechagreData); die();

		$CSVData = array();
		foreach($rechagreData as $index => $itemsArray):

			if($itemsArray['status'] == 'A'):
				$status = 'Active';
			elseif($itemsArray['status'] == 'C'):
				$status = 'Completed';
			elseif($itemsArray['status'] == 'CL'):
				$status = 'Cancelled';
			elseif($itemsArray['status'] == 'I'):
				$status = 'Inactive';
			endif;

			$CSVData1['Coupon Serial No']              = !empty($itemsArray['rc_id']) ? $itemsArray['rc_id'] : 'N/A';
			$CSVData1['Coupon Code']                   = !empty($itemsArray['coupon_code']) ? $itemsArray['coupon_code'] : 'N/A';
			$CSVData1['Generated For']                 = !empty($itemsArray['generate_for']) ? $itemsArray['generate_for'] : 'N/A';
			$CSVData1['Coupon Amount']                 = !empty($itemsArray['coupon_code_amount']) ? $itemsArray['coupon_code_amount'] : 'N/A';

			$CSVData1['Created By']                    = !empty($itemsArray['created_user']) ? $itemsArray['created_user'] : 'N/A';
			$CSVData1['Created By - Email']            = !empty($itemsArray['created_by_email']) ? $itemsArray['created_by_email'] : 'N/A';
			$CSVData1['Created By - Mobile']           = !empty($itemsArray['created_by_mobile']) ? $itemsArray['created_by_mobile'] : 'N/A';
			$CSVData1['Created By - Name']             = !empty($itemsArray['created_by_first_name']) ? $itemsArray['created_by_first_name'].' '.$itemsArray['created_by_last_name'] : 'N/A';
			$CSVData1['Created By - Pos ID']           = !empty($itemsArray['created_by_pos_no']) ? $itemsArray['created_by_pos_no'] : 'N/A';

			// $CSVData1['Redeemed By']                   = !empty($itemsArray['redeemedBy_by_user_type']) ? $itemsArray['redeemedBy_by_user_type'] : 'N/A';
			// $CSVData1['Redeemed By - POS Device ID']   = !empty($itemsArray['redeemedBy_by_pos_device_id']) ? $itemsArray['redeemedBy_by_pos_device_id'] : 'N/A';
			// $CSVData1['Redeemed By - POS Number']      = !empty($itemsArray['redeemedBy_by_pos_number']) ? $itemsArray['redeemedBy_by_pos_number'] : 'N/A';
			$CSVData1['Redeemed By - Mobile']          = !empty($itemsArray['redeemedBy_by_user_mobile']) ? $itemsArray['redeemedBy_by_user_mobile'] : 'N/A';
			$CSVData1['Redeemed By - Email']           = !empty($itemsArray['redeemedBy_by_user_email']) ? $itemsArray['redeemedBy_by_user_email'] : 'N/A';
			$CSVData1['Redeemed By - Name']            = !empty($itemsArray['redeemedBy_by_user_first_name']) ? $itemsArray['redeemedBy_by_user_first_name'].' '.$itemsArray['redeemedBy_by_user_last_name']: 'N/A';
			$CSVData1['Redeemed Date']                 = !empty($itemsArray['modified_at']) ? $itemsArray['modified_at'] : 'N/A';

			$CSVData1['Status']                        = !empty($itemsArray['status']) ? $status  : 'N/A';
			$CSVData1['Coupon Status']                 = !empty($itemsArray['coupon_code_statys']) ? $itemsArray['coupon_code_statys'] : 'N/A';
			$CSVData1['Created Date']                  = !empty($itemsArray['created_date']) ? date('Y-m-d H:i', $itemsArray['created_date']) : 'N/A';
			$CSVData1['Expiring Date']                 = !empty($itemsArray['expair_date']) ? $itemsArray['expair_date'] : 'N/A';
			array_push($CSVData, $CSVData1);
		endforeach;
		echo json_encode($CSVData);
		die();
	}


	/***********************************************************************
	** Function name 	: cancellation
	** Developed By 	: Dilip halder
	** Purpose  		: This function used for export order data
	** Date 			: 21 November 2024
	************************************************************************/
	public function cancellation($oid="")
	{
		$this->admin_model->authCheck('view_data');
		// new MongoDB\BSON\ObjectId($changeStatusId)
		$whereCon['where'] = array('_id' => New MongoDB\BSON\ObjectId($oid));
		$tblName           = 'uw_coupon_code_only';
		$rechagreDataArray = $this->common_model->getrechargeDetails($tblName,$whereCon);
		$rechagreData = $rechagreDataArray[0];

		if(!empty($rechagreData) && $rechagreData['coupon_code_statys'] == "Active" && $rechagreData['created_user'] != "Admin"  ):

				$this->admin_model->authCheck('edit_data');
				// Added cancellection Commission status.
				$tableName 			      = 'uw_loadBalance';
				$can_Commission['status'] = 'R'; 
				$Can_Where_Commission     = array( 'request_id' => $rechagreData['rc_id']);
			 	$data = $this->common_model->editMultipleDataByMultipleCondition($tableName, $can_Commission,$Can_Where_Commission);

				//Fetching values from reachrge data in below variables.
				$availableArabianPoints = $rechagreData['availableArabianPoints'];
				$USERID 				= $rechagreData['created_by'];
				$commission_amount 		= $rechagreData['commission_amount'];
				$request_oid 			= $rechagreData['_id']->{'$id'};
				$rc_id 					= $rechagreData['rc_id'];
				$code					= $rechagreData['coupon_code'];
				$coupon_amount  		= $rechagreData['coupon_code_amount'];
				$user_oid 				= New MOngoDB\BSON\ObjectId((string)$rechagreData['user_oid']);

				//generating laodbalance for deduted amount for respected user..
			  	$Param["load_balance_id"]		 =	(int)$this->common_model->getNextSequence('uw_loadBalance');
				$Param["user_oid"] 				 =	new MongoDB\BSON\ObjectId($user_oid);
		        $Param['request_id']      		 =  $rc_id;
	            $Param['request_oid']     		 =  new MongoDB\BSON\ObjectId($request_oid);
				$Param["user_id_cred"] 			 =	(int)$USERID;
				$Param["user_id_deb"]			 =	(int)0;
				$Param["upoints"] 				 =	(float)$coupon_amount;
				$Param["availableArabianPoints"] =	(float)$availableArabianPoints;
				$Param["end_balance"] 			 =	(float)$availableArabianPoints+ $coupon_amount;
			    $Param["record_type"] 			 =	'Credit';
			    $Param["narration"]				 =	'Recharge Coupon Cancelled';
		     	$Param["remarks"]				 =	"Serial No. ".$rechagreData['rc_id'];
			    $Param["creation_ip"] 			 =	$this->input->ip_address();
			    $Param["created_at"] 			 =	date('Y-m-d H:i');
			    $Param["created_by"] 			 =	(int)$this->session->userdata('UW_ADMIN_ID');
			    $Param["status"] 				 =	"A";
		    	$loadbalanceResponce 			 = $this->common_model->addData('uw_loadBalance', $Param);

		    	// Generating loadBalance for Commission amount deducting..
                $commisionBalance['load_balance_id'] =  (int)$this->common_model->getNextSequence('uw_loadBalance');
                $commisionBalance['user_oid']        =  new MongoDB\BSON\ObjectId($user_oid);
                $commisionBalance['request_id']      =  $rc_id;
                $commisionBalance['request_oid']     =  new MongoDB\BSON\ObjectId($request_oid);
                $commisionBalance['user_id_deb']     =  (int)$USERID;
                $commisionBalance['user_id_cred']    =  (int)0;
                $commisionBalance["availableArabianPoints"] = (float)$availableArabianPoints + $coupon_amount;
                $commisionBalance["end_balance"]            = (float)($availableArabianPoints + $coupon_amount) - $commission_amount;
                $commisionBalance['record_type']     = 'Debit';
                $commisionBalance['narration']       = 'Recharge Commission Reverted';
                $commisionBalance['remarks']         = "Serial No. ".$rechagreData['rc_id'];
                $commisionBalance['upoints']         = (float)$commission_amount;
                $commisionBalance['creation_ip']     = $this->input->ip_address();;
                $commisionBalance['created_at']      = date('Y-m-d H:i');
                $commisionBalance['created_by']      = (int)$USERID;
                $commisionBalance['status']          = 'A';
                $this->common_model->addData('uw_loadBalance', $commisionBalance);

                //cancelling recharged coupon by user... 
				$rechagreParam['status'] = 'CL';		
				$rechagreParam['coupon_code_statys'] = 'Cancelled';
				$rechagreParam['modified_at'] 		 = date('Y-m-d H:i');
				$this->common_model->editData($tblName,$rechagreParam ,'_id' , New MongoDB\BSON\ObjectId($oid));

				//Created credited recharge amount..
				$finalAmount		 =  $coupon_amount - $commission_amount;
				$balanceparam    	 = array('totalArabianPoints' => +(float)$finalAmount  , 'availableArabianPoints' => +(float)$finalAmount);
				
				//Updated Recharge coupon status..
				$tblName  			 = 'uw_users';
				$UpdatedUSerBalance  = $this->common_model->manageBalance($tblName,$balanceparam,'_id', new MongoDB\BSON\ObjectId($user_oid));

				// Third party api call for cancel coupon.
				if($rechagreData['created_user'] == 'Api User'):
					$curl = curl_init();

					$POSTDATA = array(
						"coupon_code" => $code
					);
					$POSTDATA = json_encode($POSTDATA);

					curl_setopt_array($curl, array(
					CURLOPT_URL => 'https://api.wataniya.online/v1/call-back/cancel-point-coupons',
					CURLOPT_RETURNTRANSFER => true,
					CURLOPT_ENCODING => '',
					CURLOPT_MAXREDIRS => 10,
					CURLOPT_TIMEOUT => 0,
					CURLOPT_FOLLOWLOCATION => true,
					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
					CURLOPT_CUSTOMREQUEST => 'POST',
					CURLOPT_POSTFIELDS =>$POSTDATA,
					CURLOPT_HTTPHEADER => array(
						'key: d42a0d190464a2be90977c3996382811',
						'Content-Type: application/json'
					),
					));

					$response = curl_exec($curl);
					curl_close($curl);
				endif;
				
				
				$this->session->set_flashdata('alert_success',lang('rechagecenclesuccess'));
					
		elseif(!empty($rechagreData) && $rechagreData['coupon_code_statys'] == "Active" && $rechagreData['coupon_code_statys'] != "Admin"  ):
			
			//cancelling recharged coupon created by Admin... 
			$rechagreParam['status'] 			 = 'CL';		
			$rechagreParam['coupon_code_statys'] = 'Cancelled';
			$rechagreParam['modified_at'] 		 = date('Y-m-d H:i');
			$rechagreParam["created_by"] 		 = (int)$this->session->userdata('UW_ADMIN_ID');
			$this->common_model->editData($tblName,$rechagreParam ,'_id' , New MongoDB\BSON\ObjectId($oid));
			$this->session->set_flashdata('alert_success',lang('rechagecenclesuccess'));

		elseif(!empty($rechagreData) && $rechagreData['coupon_code_statys'] == "Redeemed"):
			$this->session->set_flashdata('alert_error',lang('CancellationDenied'));
		elseif(!empty($rechagreData) && $rechagreData['coupon_code_statys'] == "Cancelled" || $rechagreData['status'] == "CL"):
			$this->session->set_flashdata('alert_error',lang('ALREADY_CANCELLED'));
		else:
			$this->session->set_flashdata('alert_error','No data Found');
		endif;
		
		redirect(correctLink('ALLCOUPONSDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name 	: adminRecharges
	** Developed By 	: Dilip halder
	** Purpose  		: This function used for export order data
	** Date 			: 21 November 2024
	************************************************************************/
	public function adminRecharges()
	{	
		$this->admin_model->authCheck('view_data');
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'recharge';
		$data['activeSubMenu'] 				= 	'allrechargecoupons';
		
		if($this->input->get('searchField') && $this->input->get('searchValue')):
			
			if($this->input->get('searchField') == "coupon_code"):

				$sField							=	$this->input->get('searchField');
				$sValue							=	$this->input->get('searchValue');
				$data['searchField'] 			= 	$sField;
				$data['searchValue'] 			= 	$sValue;
				$whereCon1['where'][$this->input->get('searchField')] =	$this->input->get('searchValue');

			    $tblName1							= 	'uw_coupon_code_only';
				$ResulCoupontData 					= 	$this->common_model->getData('single',$tblName1,$whereCon1,$shortField,'0','0');
				$whereCon['where']	 = 	array('rc_id' =>  (int)$ResulCoupontData['rc_id']);	
			elseif($this->input->get('searchField') == "created_for_mobile"):
				$sField							=	$this->input->get('searchField');
				$sValue							=	(int)$this->input->get('searchValue');
				$whereCon['like']			 	= 	array('0'=>trim($sField),'1'=>trim($sValue));
				$data['searchField'] 			= 	$sField;
				$data['searchValue'] 			= 	$sValue;
			else:	
				$sField							=	$this->input->get('searchField');
				$sValue							=	$this->input->get('searchValue');
				$whereCon['like']			 	= 	array('0'=>trim($sField),'1'=>trim($sValue));
				$data['searchField'] 			= 	$sField;
				$data['searchValue'] 			= 	$sValue;
			
			endif;
		else:
			$whereCon['like']		 		= 	"";
			$data['searchField'] 			= 	'';
			$data['searchValue'] 			= 	'';
		endif;
		$shortField 						= 	array('rc_seq_id'=>'desc');
			
		$baseUrl 							= 	getCurrentControllerPath('index');
		$this->session->set_userdata('ALLCOUPONSDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';



		$tblName							= 'uw_coupon_code_only';
		$resultType							= 'count';
		$whereCon['where']['batch_id']['$nin']  = array('',null);
		$whereCon['where']['created_user']     	= 'Admin';
		$totalRows 							    = $this->common_model->getRechargeData($resultType,$whereCon,$startIndex,$itemsPerPage,$tblName);
		

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
		
		$resultType 						= '';
		$data['ALLDATA'] 				    = $this->common_model->getRechargeData($resultType,$whereCon,$startIndex,$itemsPerPage,$tblName);

		// echo "<pre>";print_r($data['ALLDATA']);die();
		

		$this->layouts->set_title('All Copons | Copons | UWINN');
		$this->layouts->admin_view('rechargecoupons/allrechargecoupons/admin-index',array(),$data);

	}	// END OF FUNCTION
	 
}

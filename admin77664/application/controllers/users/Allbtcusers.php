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

class Allbtcusers extends CI_Controller {

	public function  __construct() 
	{ 
		parent:: __construct();
		error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_USER_DEPRECATED);  
		$this->load->model(array('admin_model','emailtemplate_model','emailsendgrid_model','sms_model','notification_model'));
		$this->lang->load('statictext', 'admin');
		$this->load->helper('common');
	} 

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name 	: index
	 + + Developed By 	: Dilip Halder
	 + + Purpose  		: This function used for index
	 + + Date 			: 06 January 2024
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index()
	{	
		$this->admin_model->authCheck('view_data');
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'users';
		$data['activeSubMenu'] 				= 	'allbtcusers';
		$data['searchField']				=	'';
		$data['searchValue']				=	'';
		$data['fromDate']					=	'';
		$data['toDate']						=	'';
		$whereCon							=	array();

		if($this->input->get('searchField') && $this->input->get('searchValue')):
			
			$searchField = $this->input->get('searchField');
			$searchValue = $this->input->get('searchValue');
			$data['searchField'] = 	$searchField;
			$data['searchValue'] = 	$searchValue;

			if(is_numeric($searchValue)):
				$whereCon['where'][$searchField] =  (int)$searchValue;
			else:
				$whereCon['like']  = array('0'=>trim($searchField),'1'=>trim($searchValue));
			endif;
		endif;

		$whereCon['where']['users_type']    = 'Users';
		// $shortField 						= 	array('users_id'=> -1);
		$shortField 						= 	array('_id'=> -1);
		
		$baseUrl 							= 	getCurrentControllerPath('index');
		$this->session->set_userdata('ALLUSERSDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	(!empty($qStringdata[1])) ? '?'.$qStringdata[1] : '';
		$tblName 							= 	'uw_users';
		$con 								= 	'';
		$totalRows 							= 	$this->common_model->getData('count',$tblName,$whereCon,$shortField,'0','0');
		
		if($this->input->get('showLength') == 'All'):
			$perPage	 					= 	$totalRows > 0 ? $totalRows : SHOW_NO_OF_DATA;
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
		
		$allData 							= 	$this->common_model->getData('multiple',$tblName,$whereCon,$shortField,$perPage,$page);
		$data['ALLDATA'] 					= 	is_array($allData) ? $allData : array();
		// echo '<pre>';print_r($data['ALLDATA']);die();
		$this->layouts->set_title('All Users | Users | UWINN');
		$this->layouts->admin_view('users/allusers/index',array(),$data);
	}	// END OF FUNCTION

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name : addeditdata
	 + + Developed By  : Dilip Halder
	 + + Purpose  	   : This function used for Add Edit data
	 + + Date 		   : 29 APRIL 2022
	 + + Updated Date  : 
	 + + Updated By    :
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function addeditdata($editId='')
	{		
		//echo $editId; die();
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'users';
		$data['activeSubMenu'] 				= 	'allusers';
		
		if(!empty($editId)):
			$this->admin_model->authCheck('edit_data');
			$data['EDITDATA'] =	$this->common_model->getDataByParticularField('uw_users','users_id',(int)$editId);
			// echo '<pre>';print_r($data['EDITDATA']);die;
		else:
			$this->admin_model->authCheck('add_data');
		endif;
		
		if($this->input->post('SaveChanges')):
			$error					=	'NO';
			$this->form_validation->set_rules('name', 'Name', 'trim');
			//$this->form_validation->set_rules('email', 'Email', 'trim|is_unique[uw_users.users_email]');
			$this->form_validation->set_rules('country_code', 'Country Code', 'trim');
			$this->form_validation->set_rules('mobile', 'Mobile', 'trim|is_unique[uw_users.users_mobile]');
			$this->form_validation->set_rules('arabianPoints', 'Arabian Points', 'trim');
			$this->form_validation->set_rules('users_type', 'Users Type', 'trim');

			if($this->input->post('password')){
				$this->form_validation->set_rules('password', 'Password', 'trim');
				$this->form_validation->set_rules('cpassword', 'Confirm Password', 'trim|matches[password]');
			}

			$this->form_validation->set_message('is_unique', 'The %s is already taken');
			if($this->input->post('user_type') == 'Retailer' || $this->input->post('user_type') == 'Promoter' || $this->input->post('user_type') == 'Sales Person'){
				$this->form_validation->set_rules('store_name', 'Store Name', 'trim');
				$this->form_validation->set_rules('sales_person', 'Sales Person', 'trim');
				$this->form_validation->set_rules('commission_percentage', 'Commission Percentage', 'trim|required');
			}

			if($this->form_validation->run() && $error == 'NO'): 
				$param['users_type']	    	= 	$this->input->post('user_type');
				$param['users_name']	    	= 	addslashes($this->input->post('users_name'));
				$param['last_name']	    		= 	addslashes($this->input->post('last_name'));
				$param['users_email']	    	= 	$this->input->post('users_email');
				$param['country_code']	    	= 	$this->input->post('country_code');
				$param['users_mobile']	    	= 	(int)$this->input->post('users_mobile');
				$param['area']					= 	$this->input->post('area');
				
				//Adding bindwith as per user type.
				if( $this->input->post('user_type') != 'Users'):
					$sales_person 					 = explode('|',$this->input->post('sales_person'));
					$param['pickup_point_holder']	 = $this->input->post('pickup_point_holder');
					$param['commission_percentage']	 = $this->input->post('commission_percentage');
					$param['store_name']	    	 = addslashes($this->input->post('store_name'));
					$param['bind_person_id']		 = $sales_person['0'];
					$param['bind_person_name']		 = $sales_person['1'];
					$param['bind_user_type']		 = $this->input->post('bind_user_type');
					$param['pos_number']			 = (int)$this->input->post('pos_number');
					$param['pos_device_id']			 = $this->input->post('pos_device_id');

					// Updated pos_number for all usertpes except users..
					if($this->input->post('pos_number') == "" || $this->input->post('pos_number') == 0 ):
						$param['pos_number']		 =	(int)$this->common_model->getNextPOSId('posId');
					endif;

				else:
					$param['bind_person_id']		 = (int)$this->session->userdata('UW_ADMIN_ID');
					$param['bind_person_name']		 = "Admin";
					$param['bind_user_type']		 = "Admin";

					// Removed if case of user_type is Users..
					if(!empty($data['EDITDATA'])):
					//   $param['pos_number']	  = "";
					  $param['pos_device_id'] = "";
					endif;

				endif;

				if(!empty($data['EDITDATA']) && $this->input->post('pos_device_id') == ''):
					$param['device_id']		  =	'';
					$param['users_device_id'] =	'';
				endif;

				//Change Password syntex start here...
				if($this->input->post('Checkbox_password')  == 'on' ):
					$param['password']	   = md5($this->input->post('password'));
					$param['login_token']  = "";
				endif;
				//Change Password syntex end here...
				if($this->input->post('CurrentDataID') ==''):
					$userType  = $this->input->post('user_type');
					$param['users_seq_id']	  		 = $this->common_model->getNextIdSequence('users_seq_id',$userType);
					$param['totalArabianPoints']     = (int)$this->input->post('totalArabianPoints');
					$param['availableArabianPoints'] = (int)$this->input->post('availableArabianPoints');
					$param['referral_code']			 = strtoupper(uniqid(16));
					$param['password']		    	 = md5($this->input->post('password'));
					$param['users_id']			     = (int)$this->common_model->getNextSequence('uw_users');
					$param['creation_ip']			 = currentIp();
					$param['created_at']			 = date('Y-m-d H:i');
					$param['created_by']			 = (int)$this->session->userdata('UW_ADMIN_ID');
					$param["is_verify"] 			 = "Y";
					$param['status']				 = 'A';
					// echo "<pre>"; print_r($param);die();
					$alastInsertId					 =	$this->common_model->addData('uw_users',$param);
					if(!empty($alastInsertId)):
						$loadbalenceData = array(
						'uw_users'				=>	$param['users_email'],
						'user_id_cred'			=>	$param['users_id'],
						'user_id_deb'			=>	(int)0,
						'record_type'			=>	'Credit',
						'arabian_points'		=>	(float)$param['availableArabianPoints'],
						'arabian_points_from'	=>	'Recharge',
						'record_type'			=>	'Credit',
						'load_balance_id'		=>	(int)$this->common_model->getNextSequence('uw_loadBalance'),
						'creation_ip'			=>	currentIp(),
						'created_at'			=>	date('Y-m-d H:i'),
						'created_by'			=>	'ADMIN',
						'created_user_id'	=>	(int)$this->session->userdata('UW_ADMIN_ID'),
						'status'				=>	'A'
						);
						$this->common_model->addData('uw_loadBalance',$loadbalenceData);
					endif;
					$this->session->set_flashdata('alert_success',lang('addsuccess'));
				else:
					$categoryId					= $this->input->post('CurrentDataID');
					$param['update_ip']			= currentIp();
					$param['update_date']		= date('Y-m-d h:i');
					$param['updated_by']		= (int)$this->session->userdata('UW_ADMIN_ID');
					$this->common_model->editData('uw_users',$param,'users_id',(int)$categoryId);
					$this->session->set_flashdata('alert_success',lang('updatesuccess'));
				endif;
				redirect(correctLink('MASTERDATAUSERSTYPE',getCurrentControllerPath('index')));
			endif;
		endif;

		$where['where'] 		=	array(
			'status'=>'A',
			'$or'	=>	array(
				array('users_type' => 'Sales Person'),
				array('users_type' => 'Freelancer'),
			));
		$shortField 			=	array('_id'=> -1);
		$fields 				=	array('users_id','users_name','users_mobile','users_type');
		$data['sales_man_list'] = $this->common_model->getDataByNewQuery($fields,'multiple','uw_users',$where,$shortField);

		$where1['where'] 		=	array(
										'status'=>'A',
										'users_type' => 'Freelancer');
		$shortField 			=	array('_id'=> -1);
		$fields 				=	array('users_id','users_name','users_mobile','users_type');
		$data['freelancer_list'] = $this->common_model->getDataByNewQuery($fields,'multiple','uw_users',$where1,$shortField);
		$data['countryCodeData']    =   countryCodeList();

		$this->layouts->set_title('Add/Edit Sales Person');
		$this->layouts->admin_view('users/allusers/addeditdata',array(),$data);
	}	// END OF FUNCTION	


	/***********************************************************************
	** Function name 	: changestatus
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for change status
	** Date 			: 29 APRIL 2022
	** Updated By 		: Dilip Halder
	** Updated Date 	: 02 JULY 2024
	************************************************************************/
	function changestatus($changeStatusId='',$statusType='')
	{  
		$this->admin_model->authCheck('edit_data');
		$param['status']		= $statusType;
		$param['token']			= '';
		$param['login_token']	= '';
		$param['updated_at']    = date('Y-m-d H:i:s');
		//print_r($param);die();
		$this->common_model->editData('uw_users',$param,'users_id',(int)$changeStatusId);
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		
		redirect(correctLink('ALLUSERSDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name 	: deletedata
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for delete data
	** Date 			: 29 JUNE 2021
	************************************************************************/
	function deletedata($deleteId='')
	{  
		$this->admin_model->authCheck('delete_data');

		$param['status'] 	    = 'D';
		$param['token'] 	    = '';
		$param['login_token'] 	= '';
		$param['updated_at']    = date('Y-m-d H:i:s');
		$this->common_model->editData('uw_users',$param,'users_id',(int)$deleteId);
		// $this->common_model->deleteData('uw_users','users_id',(int)$deleteId);
		$this->session->set_flashdata('alert_success',lang('deletesuccess'));
		
		redirect(correctLink('ALLUSERSDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name : exportexcel
	** Developed By : Ravi Negi
	** Purpose  : This function used for export deleted users data
	** Date : 31 AUG 2021
	** Updated Date : 05 June 2023
	** Updated By   : Dilip Halder
	************************************************************************/
	// function exportexcel()
	// {  
	// 	$this->admin_model->authCheck('view_data');
	// 	//Generating Logs
	// 	$this->common_model->generateLogs();

	// 	/* Export excel button code */
	// 	if($this->input->post('fromDate')){
	// 		$fromDate 				= 	$this->input->post('fromDate');
	// 		$data['fromDate']	 = date('Y-m-d 00:00',strtotime($fromDate));

	// 		$whereCon['where_gte'] 			= 	array(array("created_at",$data['fromDate']));
	// 	}

	// 	if($this->input->post('toDate')){
	// 		$toDate 				= 	$this->input->post('toDate');
	// 		$data['toDate']	 = date('Y-m-d 23:59',strtotime($toDate));

	// 		$whereCon['where_lte'] 			= 	array(array("created_at",$data['toDate']));
	// 	}

	// 	if( $this->input->post('searchField') != "pos_users" && $this->input->post('searchField') != "balance_less_than" &&  $this->input->post('searchField') != "balance_equal_to" && $this->input->post('searchField') != "balance_greater_than" &&  $this->input->post('searchField') != "created_at" && $this->input->post('searchField') != "users_mobile" && $this->input->post('searchField') != "" ):
	// 		$sField							=	$this->input->post('searchField');
	// 		$sValue							=	$this->input->post('searchValue');
	// 		$whereCon['like']			 	= 	array('0'=>trim($sField),'1'=>trim($sValue));
				
	// 		$data['searchField'] 			= 	$sField;
	// 		$data['searchValue'] 			= 	$sValue;
		
	// 	endif;
	// 	$whereCon['where']['users_type']  ='Users';
	// 	if($this->input->post('searchField') == "pos_users"):
			 
	// 		if($this->input->post('searchValue') == 'Y'):
	// 			$whereCon['where']  = array('$and'	=>	array(
	// 														array('pos_device_id' => array( '$ne' => null )),
	// 														array('pos_device_id' =>  array( '$ne' => "" ))
	// 													));

	// 		else:
	// 			$whereCon['where']  = array('$or'	=>	array(
	// 														array('pos_device_id' => array( '$eq' => null )),
	// 														array('pos_device_id' =>  array( '$eq' => "" ))
	// 													));
	// 		endif;

	// 	endif;
		
	// 	if($this->input->post('searchField') == "users_mobile"):
	// 		$sField							=	$this->input->post('searchField');
	// 		$sValue							=	$this->input->post('searchValue');
	// 		$whereCon['where']			 	= 	array($sField =>(int)$sValue);
	// 	endif;

	// 	if($this->input->post('searchField1') == "balance_less_than" || $this->input->post('searchField1') != "balance_equal_to" || $this->input->post('searchField1') != "balance_greater_than"):
	// 		$sField							=	$this->input->post('searchField1');
	// 		$sValue							=	$this->input->post('searchValue2');

	// 		if($sField == 'balance_equal_to'):
	// 			$whereCon['where'] 				= 	array('availableArabianPoints'=> (float)$sValue);

	// 		elseif($sField == 'balance_less_than'):
	// 			$whereCon['where_lte'] 			= 	array(array('availableArabianPoints', (int)$sValue));

	// 		elseif($sField == 'balance_greater_than'):
	// 			$whereCon['where_gte'] 			= 	array(array('availableArabianPoints',(int)$sValue));
	// 		endif;
	// 	endif;
		

	// 	// echo "<pre>";
	// 	// print_r($whereCon);
	// 	// die();

	// 	/* Export excel button code */
	// 	$data        			=   $this->common_model->getData('multiple','uw_users',$whereCon);
	// 	// echo '<pre>';print_r($data);die;

    //     $spreadsheet = new Spreadsheet();
	// 	$sheet = $spreadsheet->getActiveSheet();
	// 	$sheet->setCellValue('A1', 'Sl.No');
	// 	$sheet->setCellValue('B1', 'POS No.');
	// 	$sheet->setCellValue('C1', 'USERS SEQ ID');
	// 	$sheet->setCellValue('D1', 'FIRST NAME');
	// 	$sheet->setCellValue('E1', 'LAST NAME');
	// 	$sheet->setCellValue('F1', 'PHONE');
	// 	$sheet->setCellValue('G1', 'EMAIL');
	// 	$sheet->setCellValue('H1', 'TOTAL ARABIAN POINTS');
	// 	$sheet->setCellValue('I1', 'AVAILABLE ARABIAN POINTS');
	// 	$sheet->setCellValue('J1', 'USER TYPE');
	// 	$sheet->setCellValue('K1', 'BIND WITH');
	// 	$sheet->setCellValue('L1', 'BIND WITH USER ID');
	// 	$sheet->setCellValue('M1', 'BIND WITH USER TYPE');
	// 	$sheet->setCellValue('N1', 'Store Name');
	// 	$sheet->setCellValue('O1', 'Area');
	// 	$sheet->setCellValue('P1', 'CREATION DATE');
	// 	$sheet->setCellValue('Q1', 'DEVICE TYPE');
	// 	$sheet->setCellValue('R1', 'APP VERSION');
	// 	$sheet->setCellValue('S1', 'POS Device ID');
	// 	$sheet->setCellValue('T1', 'STATUS');
		
	// 	$slno = 1;
	// 	$start = 2;
	// 	foreach($data as $d){
	// 		$sheet->setCellValue('A'.$start, $slno);
	// 		$sheet->setCellValue('B'.$start, $d['pos_number']);
	// 		$sheet->setCellValue('C'.$start, $d['users_seq_id']);
	// 		$sheet->setCellValue('D'.$start, ucwords($d['users_name']));
	// 		$sheet->setCellValue('E'.$start, ucwords($d['last_name']));
	// 		$sheet->setCellValue('F'.$start, $d['users_mobile']);
	// 		$sheet->setCellValue('G'.$start, $d['users_email']);
	// 		$sheet->setCellValue('H'.$start, $d['totalArabianPoints']);
	// 		$sheet->setCellValue('I'.$start, $d['availableArabianPoints']);
	// 		$sheet->setCellValue('J'.$start, $d['users_type']);
	// 		$sheet->setCellValue('K'.$start, $d['bind_person_name']);
	// 		$sheet->setCellValue('L'.$start, $d['bind_person_id']);
	// 		$sheet->setCellValue('M'.$start, $d['bind_user_type']);
	// 		$sheet->setCellValue('N'.$start, $d['store_name']);
	// 		$sheet->setCellValue('O'.$start, $d['area']);
	// 		$sheet->setCellValue('P'.$start, date('d-M-Y', strtotime($d['created_at'])));
	// 		$sheet->setCellValue('Q'.$start, $d['device_type'] );
	// 		$sheet->setCellValue('R'.$start, $d['app_version'] );
	// 		$sheet->setCellValue('S'.$start, $d['pos_device_id'] );
	// 		if($d['status'] == 'A'){
	// 		    $sheet->setCellValue('T'.$start, 'Active');    
	// 		}else{
	// 		    $sheet->setCellValue('T'.$start, 'Inactive');    
	// 		}

			
			
	// 	$start = $start+1;
	// 	$slno = $slno+1;
	// 		}
	// 	$styleThinBlackBorderOutline = [
	// 				'borders' => [
	// 					'allBorders' => [
	// 						'borderStyle' => Border::BORDER_THIN,
	// 						'color' => ['argb' => 'FF000000'],
	// 					],
	// 				],
	// 			];
	// 	//Font BOLD
	// 	$sheet->getStyle('A1:T1')->getFont()->setBold(true);		
	// 	$sheet->getStyle('A1:T1000')->applyFromArray($styleThinBlackBorderOutline);
	// 	//Alignment
	// 	//fONT SIZE
	// 	$sheet->getStyle('A1:T10')->getFont()->setSize(12);
	// 	$sheet->getStyle('A1:T2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
	// 	$sheet->getStyle('A2:T100')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
	// 	//Custom width for Individual Columns
	// 	$sheet->getColumnDimension('A')->setWidth(5);
	// 	$sheet->getColumnDimension('B')->setWidth(15);
	// 	$sheet->getColumnDimension('C')->setWidth(30);
	// 	$sheet->getColumnDimension('D')->setWidth(30);
	// 	$sheet->getColumnDimension('E')->setWidth(15);
	// 	$sheet->getColumnDimension('F')->setWidth(15);
	// 	$sheet->getColumnDimension('G')->setWidth(15);
	// 	$sheet->getColumnDimension('H')->setWidth(30);
	// 	$sheet->getColumnDimension('I')->setWidth(30);
	// 	$sheet->getColumnDimension('J')->setWidth(30);
	// 	$sheet->getColumnDimension('K')->setWidth(30);
	// 	$sheet->getColumnDimension('L')->setWidth(30);
	// 	$sheet->getColumnDimension('M')->setWidth(30);
	// 	$sheet->getColumnDimension('N')->setWidth(30);
	// 	$sheet->getColumnDimension('O')->setWidth(30);
	// 	$sheet->getColumnDimension('P')->setWidth(30);
	// 	$sheet->getColumnDimension('Q')->setWidth(30);
	// 	$sheet->getColumnDimension('R')->setWidth(30);
	// 	$sheet->getColumnDimension('S')->setWidth(30);
	// 	$sheet->getColumnDimension('T')->setWidth(30);


	// 	$curdate = date('d-m-Y H:i:s');
	// 	$writer = new Xlsx($spreadsheet);
	// 	$filename = 'UWINN-users'.$curdate;
	// 	ob_end_clean();
	// 	header('Content-Type: application/vnd.ms-excel');
	// 	header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
	// 	header('Cache-Control: max-age=0');
	// 	$writer->save('php://output');
	// 	//endif;
	// 	/* Export excel END */
	// }
function exportexcel()
	{	
		$this->admin_model->authCheck('view_data');
		$data['error'] 			 = '';
		$data['activeMenu'] 	 = 'orders';
		$data['activeSubMenu'] 	 = 'allorders';
		
		//Generating Logs
	    $this->common_model->generateLogs();

		// -----------------------------------------------------------------------------//
		if($this->input->post('fromDate')):
			$fromDate	 = date('Y-m-d H:i', strtotime($this->input->post('fromDate')));
		endif;
		if($this->input->post('toDate')):
			$toDate	 	 = date('Y-m-d H:i', strtotime($this->input->post('toDate')));
		endif;

		// -----------------------------------------------------------------------------//
		$searchField = $this->input->post('searchField');
		$searchValue = $this->input->post('searchValue');
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

		// $whereCondition['where']['raffle_mode']   = array('$ne' => 'Y');
		
		
		// -----------------------------------------------------------------------------//
		if($this->input->post('searchField') && $this->input->post('searchValue')):
			
			
			$data['searchField'] = 	$searchField;
			$data['searchValue'] = 	$searchValue;

			if(is_numeric($searchValue)):
				$whereCondition['where'][$searchField] =  (int)$searchValue;
			else:
				$whereCondition['like']  = array('0'=>trim($searchField),'1'=>trim($searchValue));
			endif;
		endif;

		$whereCondition['where']['users_type']    = 'Users';
		$shortField 						= 	array('users_id'=> -1);
		// -----------------------------------------------------------------------------//
		$resultType   = "count";
		$tblName 	  = "uw_users";
		$totalRows 	  = $this->common_model->getData('count',$tblName,$whereCondition,'','0','0');
		 
		$itemsPerPage = 1000;
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
 		
 		$startIndex  = ($page - 1) * $itemsPerPage;
 		// $resultType  = '';
		// $OrderData 	 = $this->common_model->getOrderDetails($resultType,$whereCondition,$startIndex,$itemsPerPage);
		
		$totalpage 				= count($totalpage);
		$data['current_page']   = $current_page;
		$data['total_page'] 	= $totalpage;
		
		$data['searchField'] 	=   $searchField;
		$data['searchValue'] 	=   $searchValue;
		$data['fromDate'] 		=   $fromDate;
		$data['toDate'] 		=   $toDate;
		// $data['OrderData'] 		= $OrderData?$OrderData:array();

		// echo "<pre>";
		// print_r($data);
		// die();


		// echo "<pre>";print_r($data);die();
		$this->layouts->set_title('Export CSV | UWINN');
		$this->layouts->admin_view('users/allusers/exportexcel',array(),$data);
	}
// 		public function exportexcel() {
//     require_once FCPATH . 'vendor/psr/simple-cache/src/CacheInterface.php';
//     $this->admin_model->authCheck('view_data');
//     $this->common_model->generateLogs();

//     $whereCon = [];

//     if ($this->input->post('fromDate')) {
//         $fromDate = $this->input->post('fromDate');
//         $data['fromDate'] = date('Y-m-d 00:00', strtotime($fromDate));
//         $whereCon['where_gte'][] = ['created_at', $data['fromDate']];
//     }

//     if ($this->input->post('toDate')) {
//         $toDate = $this->input->post('toDate');
//         $data['toDate'] = date('Y-m-d 23:59', strtotime($toDate));
//         $whereCon['where_lte'][] = ['created_at', $data['toDate']];
//     }

//     $searchField = $this->input->post('searchField');
//     $searchValue = $this->input->post('searchValue');
//     $searchField1 = $this->input->post('searchField1');
//     $searchValue2 = $this->input->post('searchValue2');

//     if ($searchField && !in_array($searchField, ["pos_users", "balance_less_than", "balance_equal_to", "balance_greater_than", "created_at", "users_mobile"])) {
//         $whereCon['like'] = [trim($searchField), trim($searchValue)];
//     }

//     if ($searchField === "pos_users") {
//         $isPosUser = $searchValue === 'Y';
//         $whereCon['where'] = $isPosUser ?
//             ['$and' => [['pos_device_id' => ['$ne' => null]], ['pos_device_id' => ['$ne' => ""]]]] :
//             ['$or' => [['pos_device_id' => ['$eq' => null]], ['pos_device_id' => ['$eq' => ""]]]];
//     }

//     if ($searchField === "users_mobile") {
//         $whereCon['where']['users_mobile'] = (int)$searchValue;
//     }

//     if (in_array($searchField1, ['balance_less_than', 'balance_equal_to', 'balance_greater_than'])) {
//         $balanceVal = (float)$searchValue2;
//         if ($searchField1 === 'balance_equal_to') {
//             $whereCon['where']['availableArabianPoints'] = $balanceVal;
//         } elseif ($searchField1 === 'balance_less_than') {
//             $whereCon['where_lte'][] = ['availableArabianPoints', $balanceVal];
//         } elseif ($searchField1 === 'balance_greater_than') {
//             $whereCon['where_gte'][] = ['availableArabianPoints', $balanceVal];
//         }
//     }
// 	$whereCon['where']['users_type']  ='Users';
//    $selectFields = [
// 		'users_id', 'users_seq_id', 'users_name', 'last_name', 'users_mobile', 
// 		'users_email', 'totalArabianPoints', 'availableArabianPoints', 
// 		'users_type', 'bind_person_name', 'bind_person_id', 'bind_user_type',
// 		'store_name', 'area', 'created_at', 'device_type', 
// 		'app_version', 'pos_device_id', 'status','_id', 'pos_number','address'		
// 	];

// 	// Fetch users data
// 	$users = $this->common_model->getDataByNewQuery($selectFields,'multiple', 'uw_users', $whereCon);
// 	// echo "<pre>";print_r($users);die();
//     if (empty($users)) {
//         echo "No data found.";
//         return;
//     }

//     // Prepare user_id and user_oid list
//     $userIds = array_column($users, 'users_id');
//     // $userOids = array_map(function($u) { return new MongoDB\BSON\ObjectId($u->_id->{'$id'});}, $users);
// 	$userOids = array_column(array_map(fn($u) => isset($u['_id']['$id']) ?  new MongoDB\BSON\ObjectId($u['_id']['$id']) : null, $users), null);
	


//     // Fetch Lotto Orders in bulk
//     $lottoOrders = $this->common_model->getData('multiple', 'uw_lotto_orders', [
//         'where_in' => ['user_id', $userIds],
//         'where' => ['status' => 'A']
//     ]);

//     // Fetch Winners in bulk
//     $orderIds = array_column($lottoOrders, 'order_id');
//     $winners = $this->common_model->getData('multiple', 'uw_uwin_winner', [
//         'where_in' => ['order_id', $orderIds],
//         'where' => ['status' => 1]
//     ]);
// 	// Fetch Load Balance in bulk
//     $loadBalances = $this->common_model->getData('multiple', 'uw_loadBalance', [
//         'where_in' => ['user_oid', $userOids],
//         'where' => ['status' => 'A']
//     ]);
// 	// echo "<pre>";print_r($loadBalances);die();
//     // Process lotto orders
//     $userStats = [];
//     foreach ($lottoOrders as $lo) {
//         $uid = $lo['user_id'];
//         $userStats[$uid]['total_ticket'] = ($userStats[$uid]['total_ticket'] ?? 0) + (int)$lo['product_qty'];
//         $userStats[$uid]['orders'][] = $lo['order_id'];
//     }
//     // Process winners
//     if(!empty($winners)){

// 		foreach ($winners as $win) {
// 			foreach ($userStats as $uid => &$stat) {
// 				if (in_array($win['order_id'], $stat['orders'] ?? [])) {
// 					$stat['total_winn'] = ($stat['total_winn'] ?? 0) + (int)$win['amount'];
// 				}
// 			}
// 		}
// 	}
	

//     // Process load balance
//    foreach ($loadBalances as $lb) {
//     $oid = (string) $lb['user_oid'];  // object access
//     foreach ($users as $u) {
		
//         if ($u['_id']['$id'] === $oid) {
// 			$uid = $u['users_id'];
//             $stat = &$userStats[$uid];
//             $points = (int) $lb['upoints'];
//             if ($lb['record_type'] === 'Debit') {
//                 $stat['totalspent'] = ($stat['totalspent'] ?? 0) + $points;
//             }
//             if ($lb['narration'] === 'Online recharge' && $lb['record_type'] === 'Credit') {
//                 $stat['online_purchase'] = ($stat['online_purchase'] ?? 0) + $points;
//             }
//             if ($lb['narration'] === 'Recharge Coupon' && $lb['record_type'] === 'Credit') {
//                 $stat['voucher_topups'] = ($stat['voucher_topups'] ?? 0) + $points;
//             }
//             if ($lb['narration'] === 'Moved winning Prize' && $lb['record_type'] === 'Credit') {
//                 $stat['transfer_wallet'] = ($stat['transfer_wallet'] ?? 0) + $points;
//             }
//         }
//     }
// }
// // Begin Excel export
//     $spreadsheet = new Spreadsheet();
//     $sheet = $spreadsheet->getActiveSheet();

//     $headers = [
//         'Sl.No', 'POS No.', 'USERS SEQ ID', 'FIRST NAME', 'LAST NAME', 'PHONE', 'EMAIL', 'TOTAL ARABIAN POINTS',
//         'AVAILABLE ARABIAN POINTS', 'USER TYPE', 'BIND WITH', 'BIND WITH USER ID', 'BIND WITH USER TYPE',
//         'Store Name', 'Area', 'CREATION DATE', 'DEVICE TYPE', 'APP VERSION', 'POS Device ID',
//         'Total Tickets', 'Total Spent', 'Total Winning', 'Voucher Topups', 'Transfer Wallet',
//         'Online Purchase', 'STATUS'
//     ];

//     foreach ($headers as $i => $heading) {
//         $sheet->setCellValueByColumnAndRow($i + 1, 1, $heading);
//     }

//     $row = 2;
//     $sl = 1;
//     foreach ($users as $user) {
//         $uid = $user['users_id'];
//         $stats = $userStats[$uid] ?? [];

//      	if($user['address']):
//         	$area = $user['area'].$user['address'];
//         elseif($user['area']):
//         	$area = $user['area'];
//         endif;

//         $sheet->fromArray([
//             $sl++,
//             $user['pos_number'] ?? '',
//             $user['users_seq_id'] ?? '',
//             ucwords($user['users_name'] ?? ''),
//             ucwords($user['last_name'] ?? ''),
//             $user['users_mobile'] ?? '',
//             $user['users_email'] ?? '',
//             $user['totalArabianPoints'] ?? 0,
//             $user['availableArabianPoints'] ?? 0,
//             $user['users_type'] ?? '',
//             $user['bind_person_name'] ?? '',
//             $user['bind_person_id'] ?? '',
//             $user['bind_user_type'] ?? '',
//             $user['store_name'] ?? '',
//             $area  ?$area : '',
//             // $user['area'] ?? '',
//             date('d-M-Y', strtotime($user['created_at'] ?? '')),
//             $user['device_type'] ?? '',
//             $user['app_version'] ?? '',
//             $user['pos_device_id'] ?? '',
//             $stats['total_ticket'] ?? 0,
//             $stats['totalspent'] ?? 0,
//             $stats['total_winn'] ?? 0,
//             $stats['voucher_topups'] ?? 0,
//             $stats['transfer_wallet'] ?? 0,
//             $stats['online_purchase'] ?? 0,
//             $user['status'] === 'A' ? 'Active' : 'Inactive'
//         ], NULL, 'A' . $row);
//         $row++;
//     }

//     // Format styles
//     $sheet->getStyle('A1:Z1')->getFont()->setBold(true);
//     $sheet->getStyle('A1:Z' . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
//     foreach (range('A', 'Z') as $col) {
//         $sheet->getColumnDimension($col)->setAutoSize(true);
//     }

//     // Output file
//     $filename = 'UWINN-users-' . date('d-m-Y-His') . '.xlsx';
//     $writer = new Xlsx($spreadsheet);
//     ob_end_clean();
//     header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
//     header("Content-Disposition: attachment; filename=\"$filename\"");
//     header('Cache-Control: max-age=0');
//     $writer->save('php://output');
// }
public function exportexcelApi() {
    require_once FCPATH . 'vendor/psr/simple-cache/src/CacheInterface.php';
    $this->admin_model->authCheck('view_data');
    $this->common_model->generateLogs();

    $whereCon = [];

    if ($this->input->post('fromDate')) {
        $fromDate = $this->input->post('fromDate');
        $data['fromDate'] = date('Y-m-d 00:00', strtotime($fromDate));
        $whereCon['where_gte'][] = ['created_at', $data['fromDate']];
    }

    if ($this->input->post('toDate')) {
        $toDate = $this->input->post('toDate');
        $data['toDate'] = date('Y-m-d 23:59', strtotime($toDate));
        $whereCon['where_lte'][] = ['created_at', $data['toDate']];
    }

    $searchField = $this->input->post('searchField');
    $searchValue = $this->input->post('searchValue');
    $searchField1 = $this->input->post('searchField1');
    $searchValue2 = $this->input->post('searchValue2');

    if ($searchField && !in_array($searchField, ["pos_users", "balance_less_than", "balance_equal_to", "balance_greater_than", "created_at", "users_mobile"])) {
        $whereCon['like'] = [trim($searchField), trim($searchValue)];
    }

    if ($searchField === "pos_users") {
        $isPosUser = $searchValue === 'Y';
        $whereCon['where'] = $isPosUser ?
            ['$and' => [['pos_device_id' => ['$ne' => null]], ['pos_device_id' => ['$ne' => ""]]]] :
            ['$or' => [['pos_device_id' => ['$eq' => null]], ['pos_device_id' => ['$eq' => ""]]]];
    }

    if ($searchField === "users_mobile") {
        $whereCon['where']['users_mobile'] = (int)$searchValue;
    }

    if (in_array($searchField1, ['balance_less_than', 'balance_equal_to', 'balance_greater_than'])) {
        $balanceVal = (float)$searchValue2;
        if ($searchField1 === 'balance_equal_to') {
            $whereCon['where']['availableArabianPoints'] = $balanceVal;
        } elseif ($searchField1 === 'balance_less_than') {
            $whereCon['where_lte'][] = ['availableArabianPoints', $balanceVal];
        } elseif ($searchField1 === 'balance_greater_than') {
            $whereCon['where_gte'][] = ['availableArabianPoints', $balanceVal];
        }
    }
	$whereCon['where']['users_type']  ='Users';
	$page = $this->input->post('pageno');
		// $page = 1;
	$itemsPerPage = 1000;
	$startIndex  = ($page - 1)*$itemsPerPage;
    $users = $this->common_model->getData('multiple', 'uw_users', $whereCon,'',$itemsPerPage,$startIndex);

    if (empty($users)) {
        echo "No data found.";
        return;
    }

    // Prepare user_id and user_oid list
    $userIds = array_column($users, 'users_id');
    // $userOids = array_map(function($u) { return new MongoDB\BSON\ObjectId($u->_id->{'$id'});}, $users);
	$userOids = array_column(array_map(fn($u) => isset($u['_id']->{'$id'}) ?  new MongoDB\BSON\ObjectId($u['_id']->{'$id'}) : null, $users), null);



    // Fetch Lotto Orders in bulk
    $lottoOrders = $this->common_model->getData('multiple', 'uw_lotto_orders', [
        'where_in' => ['user_id', $userIds],
        'where' => ['status' => 'A']
    ]);

    // Fetch Winners in bulk
    $orderIds = array_column($lottoOrders, 'order_id');
    $winners = $this->common_model->getData('multiple', 'uw_uwin_winner', [
        'where_in' => ['order_id', $orderIds],
        'where' => ['status' => 1]
    ]);

    // Fetch Load Balance in bulk
    $loadBalances = $this->common_model->getData('multiple', 'uw_loadBalance', [
        'where_in' => ['user_oid', $userOids],
        'where' => ['status' => 'A']
    ]);
	// echo "<pre>";print_r($loadBalances);die();
    // Process lotto orders
    $userStats = [];
    if (!empty($lottoOrders)) {
		# code...
		foreach ($lottoOrders as $lo) {
			$uid = $lo['user_id'];
			$userStats[$uid]['total_ticket'] = ($userStats[$uid]['total_ticket'] ?? 0) + (int)$lo['product_qty'];
			$userStats[$uid]['orders'][] = $lo['order_id'];
		}
	}

    // Process winners
   if (!empty($winners)) {
		# code...
		foreach ($winners as $win) {
			foreach ($userStats as $uid => &$stat) {
				if (in_array($win['order_id'], $stat['orders'] ?? [])) {
					$stat['total_winn'] = ($stat['total_winn'] ?? 0) + (int)$win['amount'];
				}
			}
		}
	}

    // Process load balance
   foreach ($loadBalances as $lb) {
    $oid = (string) $lb['user_oid'];  // object access
    foreach ($users as $u) {
		
        if ($u['_id']->{'$id'} === $oid) {
            $uid = $u['users_id'];
            $stat = &$userStats[$uid];
            $points = (int) $lb['upoints'];
            if ($lb['record_type'] === 'Debit') {
                $stat['totalspent'] = ($stat['totalspent'] ?? 0) + $points;
            }
            if ($lb['narration'] === 'Online recharge' && $lb['record_type'] === 'Credit') {
                $stat['online_purchase'] = ($stat['online_purchase'] ?? 0) + $points;
            }
            if ($lb['narration'] === 'Recharge Coupon' && $lb['record_type'] === 'Credit') {
                $stat['voucher_topups'] = ($stat['voucher_topups'] ?? 0) + $points;
            }
            if ($lb['narration'] === 'Moved winning Prize' && $lb['record_type'] === 'Credit') {
                $stat['transfer_wallet'] = ($stat['transfer_wallet'] ?? 0) + $points;
            }
        }
    }
}
// echo"hii<pre>"; print_r($stat);die();

    // Begin Excel export
    // $spreadsheet = new Spreadsheet();
    // $sheet = $spreadsheet->getActiveSheet();

    // $headers = [
    //     'Sl.No', 'POS No.', 'USERS SEQ ID', 'FIRST NAME', 'LAST NAME', 'PHONE', 'EMAIL', 'TOTAL ARABIAN POINTS',
    //     'AVAILABLE ARABIAN POINTS', 'USER TYPE', 'BIND WITH', 'BIND WITH USER ID', 'BIND WITH USER TYPE',
    //     'Store Name', 'Area', 'CREATION DATE', 'DEVICE TYPE', 'APP VERSION', 'POS Device ID',
    //     'Total Tickets', 'Total Spent', 'Total Winning', 'Voucher Topups', 'Transfer Wallet',
    //     'Online Purchase', 'STATUS'
    // ];

    // foreach ($headers as $i => $heading) {
    //     $sheet->setCellValueByColumnAndRow($i + 1, 1, $heading);
    // }

    $row = 2;
    $sl = 1;
    foreach ($users as $index => $user) {
        $uid = $user['users_id'];
        $stats = $userStats[$uid] ?? [];
		$CSVData[$index]['Sl.No']            = $sl++;
		$CSVData[$index]['POS No.']          = $user['pos_number'] ?? '';
		$CSVData[$index]['USERS SEQ ID']     = $user['users_seq_id'] ?? '';
		$CSVData[$index]['FIRST NAME']       = ucwords($user['users_name'] ?? '');
		$CSVData[$index]['LAST NAME']        = ucwords($user['last_name'] ?? '');
		$CSVData[$index]['COUNTRY CODE']     = $user['country_code'] ?? '';
		$CSVData[$index]['PHONE']            = $user['users_mobile'] ?? '';
		$CSVData[$index]['EMAIL']            = $user['users_email'] ?? '';
		$CSVData[$index]['TOTAL ARABIAN POINTS'] = $user['totalArabianPoints'] ?? 0;
		$CSVData[$index]['AVAILABLE ARABIAN POINTS'] = $user['availableArabianPoints'] ?? 0;
		$CSVData[$index]['USER TYPE']        = $user['users_type'] ?? '';
		$CSVData[$index]['BIND WITH']        = $user['bind_person_name'] ?? '';
		$CSVData[$index]['BIND WITH USER ID']= $user['bind_person_id'] ?? '';
		$CSVData[$index]['BIND WITH USER TYPE']= $user['bind_user_type'] ?? '';
		$CSVData[$index]['Store Name']       = $user['store_name'] ?? '';
		$CSVData[$index]['ADDRESS']          = $user['address'] ?? '';
		$CSVData[$index]['CREATION DATE']    = date('d-M-Y', strtotime($user['created_at'])) ?? '';
		$CSVData[$index]['DEVICE TYPE']      = $user['device_type'] ?? '';
		$CSVData[$index]['APP VERSION']      = $user['app_version'] ?? '';
		$CSVData[$index]['POS Device ID']    = $user['pos_device_id'] ?? '';
		$CSVData[$index]['Total Tickets']    = isset($stats['total_ticket']) ? (int)$stats['total_ticket'] : 0;
		$CSVData[$index]['Total Spent']      = isset($stats['totalspent']) ? (int)$stats['totalspent'] : 0;
		$CSVData[$index]['Total Winning']    = isset($stats['total_winn']) ? (int)$stats['total_winn'] : 0;
		$CSVData[$index]['Voucher Topups'] = isset($stats['voucher_topups']) ? (int)$stats['voucher_topups'] : 0;
		$CSVData[$index]['Transfer Wallet'] = isset($stats['transfer_wallet'])		? (int)$stats['transfer_wallet'] : 0;
		$CSVData[$index]['Online Purchase'] = isset($stats['online_purchase']) ? (int)$stats['online_purchase'] : 0;
		$CSVData[$index]['STATUS']           = $user['status'] === 'A' ? 'Active' : 'Inactive';

		// Set values in the sheet
		
        // $sheet->fromArray([
        //     $sl++,
        //     $user['pos_number'] ?? '',
        //     $user['users_seq_id'] ?? '',
        //     ucwords($user['users_name'] ?? ''),
        //     ucwords($user['last_name'] ?? ''),
        //     $user['users_mobile'] ?? '',
        //     $user['users_email'] ?? '',
        //     $user['totalArabianPoints'] ?? 0,
        //     $user['availableArabianPoints'] ?? 0,
        //     $user['users_type'] ?? '',
        //     $user['bind_person_name'] ?? '',
        //     $user['bind_person_id'] ?? '',
        //     $user['bind_user_type'] ?? '',
        //     $user['store_name'] ?? '',
        //     $user['area'] ?? '',
        //     date('d-M-Y', strtotime($user['created_at'] ?? '')),
        //     $user['device_type'] ?? '',
        //     $user['app_version'] ?? '',
        //     $user['pos_device_id'] ?? '',
        //     $stats['total_ticket'] ?? 0,
        //     $stats['totalspent'] ?? 0,
        //     $stats['total_winn'] ?? 0,
        //     $stats['voucher_topups'] ?? 0,
        //     $stats['transfer_wallet'] ?? 0,
        //     $stats['online_purchase'] ?? 0,
        //     $user['status'] === 'A' ? 'Active' : 'Inactive'
        // ], NULL, 'A' . $row);
        $row++;
    }
	echo json_encode($CSVData);
	die();
    // Format styles
    // $sheet->getStyle('A1:Z1')->getFont()->setBold(true);
    // $sheet->getStyle('A1:Z' . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
    // foreach (range('A', 'Z') as $col) {
    //     $sheet->getColumnDimension($col)->setAutoSize(true);
    // }

    // Output file
    // $filename = 'UWINN-users-' . date('d-m-Y-His') . '.xlsx';
    // $writer = new Xlsx($spreadsheet);
    // ob_end_clean();
    // header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    // header("Content-Disposition: attachment; filename=\"$filename\"");
    // header('Cache-Control: max-age=0');
    // $writer->save('php://output');
}

	/***********************************************************************
	** Function name 	: checkDeplicacy
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for Check duplicate entry
	** Date 			: 23 MAY 2022
	** Updated Date 	: 
	** Updated By   	: 
	************************************************************************/
	public function checkDeplicacy(){
		$user 	= $_POST['user'];

		if (is_numeric($user)) {
			//echo 'Numeric'; die();
			$user_data = $this->common_model->getDataByParticularField('uw_users', 'users_mobile', (int)$user);
			$smsg = 'This mobile id availabel'; 
			$emsg = 'This mobile id is already taken.'; 
		}else{
			//echo 'String'; die();		
			$user_data = $this->common_model->getDataByParticularField('uw_users', 'users_email', $user);
			$smsg = 'This email id availabel'; 
			$emsg = 'This email id is already taken.'; 
		}
		//print_r($user_data); die();

		if(empty($user_data)){
			//echo $smsg;
		}else{
			echo $emsg;
		}

		die();

	}// END FO FUNCTION


	/***********************************************************************
	** Function name : exportAllUsers
	** Developed By : Dilip Halder
	** Purpose  : This function used for export deleted users data
	** Date : 10 May 2023
	** Updated Date :  
	** Updated By   :  
	************************************************************************/
	public function exportAllUsers()
	{  
		 
		/* Export excel button code */
		$data  = $this->common_model->getData('multiple','uw_users',$whereCon);
		//Generating Logs
		$this->common_model->generateLogs();
		// echo '<pre>';print_r($data);die;

	    $spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'Sl.No');
		$sheet->setCellValue('B1', 'USERS SEQ ID');
		$sheet->setCellValue('C1', 'FIRST NAME');
		$sheet->setCellValue('D1', 'LAST NAME');
		$sheet->setCellValue('E1', 'COUNTRY CODE');
		$sheet->setCellValue('F1', 'PHONE');
		$sheet->setCellValue('G1', 'EMAIL');
		$sheet->setCellValue('H1', 'TOTAL ARABIAN POINTS');
		$sheet->setCellValue('I1', 'AVAILABLE ARABIAN POINTS');
		$sheet->setCellValue('J1', 'USER TYPE');
		$sheet->setCellValue('K1', 'BIND WITH');
		$sheet->setCellValue('L1', 'BIND WITH USER ID');
		$sheet->setCellValue('M1', 'BIND WITH USER TYPE');
		$sheet->setCellValue('N1', 'Store Name');
		$sheet->setCellValue('O1', 'AREA');
		$sheet->setCellValue('P1', 'CREATION DATE');
		$sheet->setCellValue('Q1', 'STATUS');
		
		$slno = 1;
		$start = 2;
		foreach($data as $d){
			$sheet->setCellValue('A'.$start, $slno);
			$sheet->setCellValue('B'.$start, $d['users_seq_id']);
			$sheet->setCellValue('C'.$start, ucwords($d['users_name']));
			$sheet->setCellValue('D'.$start, ucwords($d['last_name']));
			$sheet->setCellValue('E'.$start, $d['country_code']);
			$sheet->setCellValue('F'.$start, $d['users_mobile']);
			$sheet->setCellValue('G'.$start, $d['users_email']);
			$sheet->setCellValue('H'.$start, $d['totalArabianPoints']);
			$sheet->setCellValue('I'.$start, $d['availableArabianPoints']);
			$sheet->setCellValue('J'.$start, $d['users_type']);
			$sheet->setCellValue('K'.$start, $d['bind_person_name']);
			$sheet->setCellValue('L'.$start, $d['bind_person_id']);
			$sheet->setCellValue('M'.$start, $d['bind_user_type']);
			$sheet->setCellValue('N'.$start, $d['store_name']);
			$sheet->setCellValue('O'.$start, $d['area']);
			$sheet->setCellValue('P'.$start, date('d-M-Y ', strtotime($d['created_at'])));
			if($d['status'] == 'A'){
				$sheet->setCellValue('Q'.$start,  'Active' );	
			}else{
				$sheet->setCellValue('Q'.$start, 'Inactive');	
			}
			
			
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
		//Font BOLD
		$sheet->getStyle('A1:O1')->getFont()->setBold(true);		
		$sheet->getStyle('A1:O1000')->applyFromArray($styleThinBlackBorderOutline);
		//Alignment
		//fONT SIZE
		$sheet->getStyle('A1:D10')->getFont()->setSize(12);
		$sheet->getStyle('A1:D2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
		$sheet->getStyle('A2:D100')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		//Custom width for Individual Columns
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
		$sheet->getColumnDimension('K')->setWidth(30);
		$sheet->getColumnDimension('L')->setWidth(30);
		$sheet->getColumnDimension('M')->setWidth(30);
		$sheet->getColumnDimension('N')->setWidth(30);
		$sheet->getColumnDimension('O')->setWidth(30);

		$curdate 	= date('d-m-Y H:i:s');
		$filename 	= str_replace(' ', '_', 'UWINN-users'.$curdate.'.xlsx');
		$writer 	= IOFactory::createWriter($spreadsheet, 'Xlsx');
		$writer->save('./assets/excel_sheet/'.$filename);

		$SendAttachment = $this->emailsendgrid_model->SendUserList($filename);

		if($SendAttachment == 1):
			 $file =  fileFCPATH.'admin/assets/excel_sheet/'.$filename;
			 unlink($file);
		endif;
	}

	/***********************************************************************
	** Function name 	: changeRechargeCouponRedeemstatus
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for change status
	** Date 			: 06 JULY 2023
	************************************************************************/
	function changeRechargeCouponRedeemstatus($changeStatusId='',$statusType='')
	{  
		$this->admin_model->authCheck('edit_data');
		$param['redeem_attempt_count']		=	$statusType;
		$this->common_model->editData('uw_users',$param,'users_id',(int)$changeStatusId);
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		
		redirect(correctLink('ALLUSERSDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name 	: reverseAmount
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for reverse amount
	** Date 			: 21 September 2023
	************************************************************************/
	function reverseAmount($changeStatusId='',$availableArabianPoints='')
	{  

		$this->admin_model->authCheck('edit_data');
		
		/* Load Balance Table -- after buy product*/
	    $Buyparam["load_balance_id"]	=	(int)$this->common_model->getNextSequence('uw_loadBalance');
		$Buyparam["user_id_cred"] 		=	'';
		$Buyparam["user_id_deb"] 		=	(int)$changeStatusId;
		$Buyparam["arabian_points"] 	=	(float)$availableArabianPoints;
	    $Buyparam["record_type"] 		=	'Debit';
	    $Buyparam["arabian_points_from"]=	'Reverse Amount';
	    $Buyparam["creation_ip"] 		=	$this->input->ip_address();
	    $Buyparam["created_at"] 		=	date('Y-m-d H:i');
	    $Buyparam["created_by"] 		=	(int)$this->session->userdata('UW_ADMIN_ID');
	    $Buyparam["status"] 			=	"A";
	    
	    $this->common_model->addData('uw_loadBalance', $Buyparam);
		
		$param['availableArabianPoints']		=	(int)0;
		$this->common_model->editData('uw_users',$param,'users_id',(int)$changeStatusId);
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		
		redirect(correctLink('ALLUSERSDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name 	: updatequickuser
	** Developed By 	: Dilip	Halder
	** Purpose  		: This function used for enable/disable Quick purchase.
	** Date 			: 06 JULY 2023
	************************************************************************/
	function updatequickuser($editId='')
	{		
		//echo $editId; die();
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'users';
		$data['activeSubMenu'] 				= 	'allusers';
		
		if($editId):
			$this->admin_model->authCheck('edit_data');
			$data['EDITDATA']				=	$this->common_model->getDataByParticularField('uw_users','users_id',(int)$editId);
			//echo $editId; die();
			//echo '<pre>';print_r($data['EDITDATA']);die;
		else:
			$this->admin_model->authCheck('add_data');
		endif;
		
		$this->session->set_userdata('UpdateQuickUSER',currentFullUrl());

		if($this->input->post('SaveChanges')):
			// echo '<pre>';print_r($_POST);die;
			
			$error					=	'NO';
			$this->form_validation->set_rules('buy_ticket', 'Buy ticket', 'trim|required');
			$this->form_validation->set_rules('buy_voucher', 'Buy voucher', 'trim|required');
			$this->form_validation->set_rules('company_name', 'Company Name', 'trim');
			$this->form_validation->set_rules('company_address', 'Company Address', 'trim');

			if($this->input->post('buy_ticket')== 'N' && $this->input->post('buy_voucher')== 'N'):
				$param['quick_user']	    	= 	'N';
			endif;

			if($this->form_validation->run() && $error == 'NO'): 
				$param['buy_ticket']	    	= 	$this->input->post('buy_ticket');
				$param['buy_voucher']	    	= 	$this->input->post('buy_voucher');
				$param['company_name']	    	= 	$this->input->post('company_name');
				$param['company_address']	    = 	$this->input->post('company_address');
				$param['quick_user']	    	= 	'Y';
				
				if($this->input->post('CurrentDataID') !=''):
					$categoryId					=	$this->input->post('CurrentDataID');
					$param['update_ip']			=	currentIp();
					$param['update_date']		=	date('Y-m-d h:i');
					$param['updated_by']		=	(int)$this->session->userdata('UW_ADMIN_ID');
					
					// echo "<pre>";print_r($_POST);die();
					$this->common_model->editData('uw_users',$param,'users_id',(int)$categoryId);
					$this->session->set_flashdata('alert_success',lang('updatesuccess'));

				endif;

				redirect(correctLink('UpdateQuickUSER',getCurrentControllerPath('index')));
			endif;
		endif;

		$this->layouts->set_title('Enable/Disable Quick Purchase');
		$this->layouts->admin_view('users/allusers/addeditquickdata',array(),$data);
	}	// END OF FUNCTION	

	/***********************************************************************
	** Function name 	: generatePosNumber
	** Developed By 	: Dilip	Halder
	** Purpose  		: This function used for generate Pos Number.
	** Date 			: 14 February 2024.
	************************************************************************/
	public function generatePosNumber()
	{
		
		$counterID = $this->common_model->getNextPOSId('posId');
		$counter = json_encode(array('counter' => $counterID));
		echo $counter;
		die();
	}

}
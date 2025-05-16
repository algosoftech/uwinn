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

class Assignusers extends CI_Controller {

	public function  __construct() 
	{ 
		parent:: __construct();
		error_reporting(E_ALL ^ E_NOTICE);  
		$this->load->model(array('admin_model','emailtemplate_model','emailsendgrid_model','sms_model','notification_model'));
		$this->lang->load('statictext', 'admin');
		$this->load->helper('common');
	} 

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name 	: index
	 + + Developed By 	: Dilip Halder
	 + + Purpose  		: This function used for index
	 + + Date 			: 25 January 2024
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index()
	{	
		$this->admin_model->authCheck();
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'pos';
		$data['activeSubMenu'] 				= 	'assignusers';

		$whereCon['where'] = array('status' => 'A');
		
		$shortField 						= 	array('users_id'=> -1);
		$baseUrl 							= 	getCurrentControllerPath('index');
		$this->session->set_userdata('ALLUSERSDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 	'uw_assign_users';
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
		// echo '<pre>';print_r($data['ALLDATA']);die();
		$this->layouts->set_title('All assign User List | POS | UWINN');
		$this->layouts->admin_view('pos/allassignusers/index',array(),$data);
	}	// END OF FUNCTION

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name : addeditdata
	 + + Developed By  : Dilip Halder
	 + + Purpose  	   : This function used for Add Edit data
	 + + Date 		   : 25 January 2023
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function addeditdata($editId='')
	{		
		//echo $editId; die();
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'pos';
		$data['activeSubMenu'] 				= 	'assignusers';
		
		if($editId):
			$this->admin_model->authCheck('edit_data');
			$data['EDITDATA']				=	$this->common_model->getDataByParticularField('uw_users','users_id',(int)$editId);
		else:
			$this->admin_model->authCheck('add_data');
		endif;
		
		if($this->input->post('SaveChanges')):
			$error					=	'NO';
			$this->form_validation->set_rules('name', 'Name', 'trim');
			$this->form_validation->set_message('is_unique', 'The %s is already taken');
			if($this->form_validation->run() && $error == 'NO'): 
				$param['users_type']	    	= 	$this->input->post('user_type');
				$param['users_name']	    	= 	addslashes($this->input->post('users_name'));
				$param['last_name']	    		= 	addslashes($this->input->post('last_name'));
				$param['users_email']	    	= 	$this->input->post('users_email');
				$param['country_code']	    	= 	$this->input->post('country_code');
				$param['users_mobile']	    	= 	(int)$this->input->post('users_mobile');
				$param['pickup_point_holder']	= 	$this->input->post('pickup_point_holder');
				$param['area']					= 	$this->input->post('area');
				//For Bind Freelancer to sales person
				if( $this->input->post('user_type') == 'Retailer' && $this->input->post('bind_user_type') == 'Sales Person' || 
					$this->input->post('user_type') == 'Promoter' && $this->input->post('bind_user_type') == 'Sales Person') {
					$sales_person 					=	explode('|',$this->input->post('sales_person'));
					$param['store_name']	    	= 	addslashes($this->input->post('store_name'));
					$param['bind_person_id']		=	$sales_person['0'];
					$param['bind_person_name']		=	$sales_person['1'];
					$param['bind_user_type']		=	$this->input->post('bind_user_type');

					$param['quick_user']		=	'Y';
					$param['company_name']		=	'Arabian Plus Trading LLC';
					$param['company_address']	=	'Golden Business Centre , Port Saeed , Dubai';
					$param['buy_ticket']		=	'Y';
					$param['buy_voucher']		=	'Y';
					
				}
				//End
				//For Bind Freelancer to Freelancer
				if( $this->input->post('user_type') == 'Retailer' && $this->input->post('bind_user_type') == 'Freelancer' || 
					$this->input->post('user_type') == 'Promoter' && $this->input->post('bind_user_type') == 'Freelancer' ||
					$this->input->post('user_type') == 'Freelancer Promoter' && $this->input->post('bind_user_type') == 'Freelancer' ){
					$sales_person 					=	explode('|',$this->input->post('freelancer_person'));
					$param['store_name']	    	= 	addslashes($this->input->post('store_name'));
					$param['bind_person_id']		=	$sales_person['0'];
					$param['bind_person_name']		=	$sales_person['1'];
					$param['bind_user_type']		=	$this->input->post('bind_user_type');
				}
				//End
				//For Bind Freelancer to sales person
				if($this->input->post('user_type') == 'Freelancer' && $this->input->post('bind_user_type') == 'Sales Person'){
					$sales_person 					=	explode('|',$this->input->post('sales_person'));
					$param['bind_person_id']		=	$sales_person['0'];
					$param['bind_person_name']		=	$sales_person['1'];
					$param['bind_user_type']		=	$this->input->post('bind_user_type');
				} 
				//End

				if($this->input->post('user_type') == 'Freelancer' || $this->input->post('user_type') == 'Sales Person' || $this->input->post('user_type') == 'Retailer' || $this->input->post('user_type') == 'Promoter'):
					$param['pos_number']			=	$this->input->post('pos_number');
					$param['pos_device_id']			=	$this->input->post('pos_device_id');
				endif;

				if($this->input->post('pos_device_id') == ''):
					$param['device_id']				=	'';
					$param['users_device_id']			=	'';
				endif;

				//For user type users and sales person
				if($this->input->post('user_type') == 'Users' || $this->input->post('user_type') == 'Sales Person'){
					$param['bind_person_id']		=	'';
					$param['bind_person_name']		=	'';
					$param['bind_user_type']		=	'';
				}
				//End

				if($this->input->post('Checkbox_password')  == 'on' ):
					$param['password']		    	= 	md5($this->input->post('password'));
				endif;

				if($this->input->post('CurrentDataID') ==''):
					$param['password']		    	= 	md5($this->input->post('password'));
					$param['totalArabianPoints']    = 	(int)$this->input->post('totalArabianPoints');
					$param['availableArabianPoints']= 	(int)$this->input->post('availableArabianPoints');
					$param['referral_code']	=	strtoupper(uniqid(16));
					$param['users_id']		=	(int)$this->common_model->getNextSequence('uw_users');
					if($param['users_type'] == 'Sales Person'){
						$param['users_seq_id']	=	$this->common_model->getNextIdSequence('users_seq_id','Sales Person');
					}elseif($param['users_type'] == 'Retailer'){
						$param['users_seq_id']	=	$this->common_model->getNextIdSequence('users_seq_id','Retailer');
					}elseif($param['users_type'] == 'Promoter'){
						$param['users_seq_id']	=	$this->common_model->getNextIdSequence('users_seq_id','Promoter');
					}elseif($param['users_type'] == 'Freelancer'){
						$param['users_seq_id']	=	$this->common_model->getNextIdSequence('users_seq_id','Freelancer');
					}else{
						$param['users_seq_id']	=	$this->common_model->getNextIdSequence('users_seq_id','Users');
					} 
					
					$param['creation_ip']		=	currentIp();
					$param['created_at']		=	date('Y-m-d h:i');
					$param['created_by']		=	(int)$this->session->userdata('UW_ADMIN_ID');
					$param["is_verify"] 		=	"Y";
					$param['status']			=	'A';
					$alastInsertId				=	$this->common_model->addData('uw_users',$param);
					if(!empty($alastInsertId)){
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
					}

					$this->session->set_flashdata('alert_success',lang('addsuccess'));
				else:

					$categoryId					=	$this->input->post('CurrentDataID');
					$param['update_ip']			=	currentIp();
					$param['update_date']		=	date('Y-m-d h:i');
					$param['updated_by']		=	(int)$this->session->userdata('UW_ADMIN_ID');
					$this->common_model->editData('uw_users',$param,'users_id',(int)$categoryId);
					$this->session->set_flashdata('alert_success',lang('updatesuccess'));
				endif;

				redirect(correctLink('MASTERDATAUSERSTYPE',getCurrentControllerPath('index')));
			endif;
		endif;

		$tableName 		= "uw_users";
		$where['where'] =	array(
			  'status'=>'A',
			  'users_type'=> 'Sales Person',
			);
		$shortField 			=	array('_id'=> -1);
		$fields 				=	array('users_id','users_name','users_mobile','users_email','users_type');
		$data['salesPerson'] = $this->common_model->getDataByNewQuery($fields,'multiple','uw_users',$where,$shortField);

		

		$this->layouts->set_title('Add/Edit Sales Person');
		$this->layouts->admin_view('pos/allassignusers/addeditdata',array(),$data);
	}	// END OF FUNCTION	


	/***********************************************************************
	** Function name 	: changestatus
	** Developed By 	: Afsar Ali
	** Purpose  		: This function used for change status
	** Date 			: 29 APRIL 2022
	************************************************************************/
	function changestatus($changeStatusId='',$statusType='')
	{  
		$this->admin_model->authCheck('edit_data');
		$param['status']		=	$statusType;
		//print_r($param);die();
		$this->common_model->editData('uw_users',$param,'users_id',(int)$changeStatusId);
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		
		redirect(correctLink('ALLUSERSDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name 	: deletedata
	** Developed By 	: Afsar ALi
	** Purpose  		: This function used for delete data
	** Date 			: 29 JUNE 2021
	************************************************************************/
	function deletedata($deleteId='')
	{  
		$this->admin_model->authCheck('delete_data');

		$param['status'] 	= 'D';
		$param['token'] 	= '';
		$param['updated_at']= date('Y-m-d H:i:s');
		
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
	function exportexcel()
	{  

		/* Export excel button code */
		if($this->input->post('fromDate')){
			$fromDate 				= 	$this->input->post('fromDate');
			$data['fromDate']	 = date('Y-m-d 00:00',strtotime($fromDate));

			$whereCon['where_gte'] 			= 	array(array("created_at",$data['fromDate']));
		}

		if($this->input->post('toDate')){
			$toDate 				= 	$this->input->post('toDate');
			$data['toDate']	 = date('Y-m-d 23:59',strtotime($toDate));

			$whereCon['where_lte'] 			= 	array(array("created_at",$data['toDate']));
		}

		if( $this->input->post('searchField') != "pos_users" && $this->input->post('searchField') != "balance_less_than" &&  $this->input->post('searchField') != "balance_equal_to" && $this->input->post('searchField') != "balance_greater_than" &&  $this->input->post('searchField') != "created_at" && $this->input->post('searchField') != "users_mobile" && $this->input->post('searchField') != "" ):
			$sField							=	$this->input->post('searchField');
			$sValue							=	$this->input->post('searchValue');
			$whereCon['like']			 	= 	array('0'=>trim($sField),'1'=>trim($sValue));
				
			$data['searchField'] 			= 	$sField;
			$data['searchValue'] 			= 	$sValue;
		
		endif;

		if($this->input->post('searchField') == "pos_users"):
			 
			if($this->input->post('searchValue') == 'Y'):
				$whereCon['where']  = array('$and'	=>	array(
															array('pos_device_id' => array( '$ne' => null )),
															array('pos_device_id' =>  array( '$ne' => "" ))
														));

			else:
				$whereCon['where']  = array('$or'	=>	array(
															array('pos_device_id' => array( '$eq' => null )),
															array('pos_device_id' =>  array( '$eq' => "" ))
														));
			endif;

		endif;
		
		if($this->input->post('searchField') == "users_mobile"):
			$sField							=	$this->input->post('searchField');
			$sValue							=	$this->input->post('searchValue');
			$whereCon['where']			 	= 	array($sField =>(int)$sValue);
		endif;

		if($this->input->post('searchField1') == "balance_less_than" || $this->input->post('searchField1') != "balance_equal_to" || $this->input->post('searchField1') != "balance_greater_than"):
			$sField							=	$this->input->post('searchField1');
			$sValue							=	$this->input->post('searchValue2');

			if($sField == 'balance_equal_to'):
				$whereCon['where'] 				= 	array('availableArabianPoints'=> (float)$sValue);

			elseif($sField == 'balance_less_than'):
				$whereCon['where_lte'] 			= 	array(array('availableArabianPoints', (int)$sValue));

			elseif($sField == 'balance_greater_than'):
				$whereCon['where_gte'] 			= 	array(array('availableArabianPoints',(int)$sValue));
			endif;
		endif;
		

		// echo "<pre>";
		// print_r($whereCon);
		// die();

		/* Export excel button code */
		$data        			=   $this->common_model->getData('multiple','uw_users',$whereCon);
		// echo '<pre>';print_r($data);die;

        $spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'Sl.No');
		$sheet->setCellValue('B1', 'USERS SEQ ID');
		$sheet->setCellValue('C1', 'FIRST NAME');
		$sheet->setCellValue('D1', 'LAST NAME');
		$sheet->setCellValue('E1', 'PHONE');
		$sheet->setCellValue('F1', 'EMAIL');
		$sheet->setCellValue('G1', 'TOTAL ARABIAN POINTS');
		$sheet->setCellValue('H1', 'AVAILABLE ARABIAN POINTS');
		$sheet->setCellValue('I1', 'USER TYPE');
		$sheet->setCellValue('J1', 'BIND WITH');
		$sheet->setCellValue('K1', 'BIND WITH USER ID');
		$sheet->setCellValue('L1', 'BIND WITH USER TYPE');
		$sheet->setCellValue('M1', 'Store Name');
		$sheet->setCellValue('N1', 'Area');
		$sheet->setCellValue('O1', 'CREATION DATE');
		$sheet->setCellValue('P1', 'DEVICE TYPE');
		$sheet->setCellValue('Q1', 'APP VERSION');
		$sheet->setCellValue('R1', 'POS Device ID');
		$sheet->setCellValue('S1', 'STATUS');
		
		$slno = 1;
		$start = 2;
		foreach($data as $d){
			$sheet->setCellValue('A'.$start, $slno);
			$sheet->setCellValue('B'.$start, $d['users_seq_id']);
			$sheet->setCellValue('C'.$start, ucwords($d['users_name']));
			$sheet->setCellValue('D'.$start, ucwords($d['last_name']));
			$sheet->setCellValue('E'.$start, $d['users_mobile']);
			$sheet->setCellValue('F'.$start, $d['users_email']);
			$sheet->setCellValue('G'.$start, $d['totalArabianPoints']);
			$sheet->setCellValue('H'.$start, $d['availableArabianPoints']);
			$sheet->setCellValue('I'.$start, $d['users_type']);
			$sheet->setCellValue('J'.$start, $d['bind_person_name']);
			$sheet->setCellValue('K'.$start, $d['bind_person_id']);
			$sheet->setCellValue('L'.$start, $d['bind_user_type']);
			$sheet->setCellValue('M'.$start, $d['store_name']);
			$sheet->setCellValue('N'.$start, $d['area']);
			$sheet->setCellValue('O'.$start, date('d-M-Y ', strtotime($d['created_at'])));
			$sheet->setCellValue('P'.$start, $d['device_type'] );
			$sheet->setCellValue('Q'.$start, $d['app_version'] );
			$sheet->setCellValue('R'.$start, $d['pos_device_id'] );
			if($d['status'] == 'A'){
				$sheet->setCellValue('S'.$start, 'Active');	
			}else{
				$sheet->setCellValue('S'.$start, 'Inactive');	
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
		$sheet->getStyle('A1:S1')->getFont()->setBold(true);		
		$sheet->getStyle('A1:S1000')->applyFromArray($styleThinBlackBorderOutline);
		//Alignment
		//fONT SIZE
		$sheet->getStyle('A1:S10')->getFont()->setSize(12);
		$sheet->getStyle('A1:S2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
		$sheet->getStyle('A2:S100')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
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
		$sheet->getColumnDimension('P')->setWidth(30);
		$sheet->getColumnDimension('Q')->setWidth(30);
		$sheet->getColumnDimension('R')->setWidth(30);
		$sheet->getColumnDimension('S')->setWidth(30);


		$curdate = date('d-m-Y H:i:s');
		$writer = new Xlsx($spreadsheet);
		$filename = 'UWINN-users'.$curdate;
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
		//endif;
		/* Export excel END */
	}

/***********************************************************************
** Function name 	: checkDeplicacy
** Developed By 	: AFSAR ALI
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
	// echo '<pre>';print_r($data);die;

    $spreadsheet = new Spreadsheet();
	$sheet = $spreadsheet->getActiveSheet();
	$sheet->setCellValue('A1', 'Sl.No');
	$sheet->setCellValue('B1', 'USERS SEQ ID');
	$sheet->setCellValue('C1', 'FIRST NAME');
	$sheet->setCellValue('D1', 'LAST NAME');
	$sheet->setCellValue('E1', 'PHONE');
	$sheet->setCellValue('F1', 'EMAIL');
	$sheet->setCellValue('G1', 'TOTAL ARABIAN POINTS');
	$sheet->setCellValue('H1', 'AVAILABLE ARABIAN POINTS');
	$sheet->setCellValue('I1', 'USER TYPE');
	$sheet->setCellValue('J1', 'BIND WITH');
	$sheet->setCellValue('K1', 'BIND WITH USER ID');
	$sheet->setCellValue('L1', 'BIND WITH USER TYPE');
	$sheet->setCellValue('M1', 'Store Name');
	$sheet->setCellValue('N1', 'CREATION DATE');
	$sheet->setCellValue('O1', 'STATUS');
	
	$slno = 1;
	$start = 2;
	foreach($data as $d){
		$sheet->setCellValue('A'.$start, $slno);
		$sheet->setCellValue('B'.$start, $d['users_seq_id']);
		$sheet->setCellValue('C'.$start, ucwords($d['users_name']));
		$sheet->setCellValue('D'.$start, ucwords($d['last_name']));
		$sheet->setCellValue('E'.$start, $d['users_mobile']);
		$sheet->setCellValue('F'.$start, $d['users_email']);
		$sheet->setCellValue('G'.$start, $d['totalArabianPoints']);
		$sheet->setCellValue('H'.$start, $d['availableArabianPoints']);
		$sheet->setCellValue('I'.$start, $d['users_type']);
		$sheet->setCellValue('J'.$start, $d['bind_person_name']);
		$sheet->setCellValue('K'.$start, $d['bind_person_id']);
		$sheet->setCellValue('L'.$start, $d['bind_user_type']);
		$sheet->setCellValue('M'.$start, $d['store_name']);
		$sheet->setCellValue('N'.$start, date('d-M-Y ', strtotime($d['created_at'])));
		if($d['status'] == 'A'){
			$sheet->setCellValue('O'.$start,  'Active' );	
		}else{
			$sheet->setCellValue('O'.$start, 'Inactive');	
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
	$filename 	= str_replace(' ', '_', 'DealzArabia-users'.$curdate.'.xlsx');
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
** Developed By 	: Afsar Ali
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

}
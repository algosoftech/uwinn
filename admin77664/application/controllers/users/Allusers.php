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

class Allusers extends CI_Controller {

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
	 + + Date 			: 06 January 2024
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index()
	{	
		$this->admin_model->authCheck('view_data');
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'users';
		$data['activeSubMenu'] 				= 	'allusers';
		
		if($this->input->get('searchField') && $this->input->get('searchValue')):
			
			$searchField = $this->input->get('searchField');
			$searchValue = $this->input->get('searchValue');
			$data['searchField'] = 	$searchField;
			$data['searchValue'] = 	$searchValue;

			if($searchField == 'pos_device_id'):
				$whereCon['where'][$searchField] = $searchValue;
			elseif(is_numeric($searchValue)):
				$whereCon['where'][$searchField] = (int)$searchValue;
			else:
				$whereCon['like']  = array('0'=>trim($searchField),'1'=>trim($searchValue));
			endif;

		else:
			$whereCon['like']		 		= 	"";
			$data['searchField'] 			= 	'';
			$data['searchValue'] 			= 	'';	
		endif;
		$whereCon['where']['users_type'] 	=   array('$ne' => 'Users');
		$shortField 						= 	array('users_id'=> -1);
		
		$baseUrl 							= 	getCurrentControllerPath('index');
		$this->session->set_userdata('ALLUSERSDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 	'uw_users';
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
				$param['show_raffle_campaign']	= 	$this->input->post('show_raffle_campaign');
				
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
					$param['referral_code']			 = (int)$this->input->post('pos_number');
					$param['pos_device_id']			 = $this->input->post('pos_device_id');

					// Updated pos_number for all usertpes except users..
					if($this->input->post('pos_number') == "" || $this->input->post('pos_number') == 0 ):
						$param['pos_number']		 = (int)$this->common_model->getNextPOSId('posId');
						$param['referral_code']		 = $param['pos_number'];
					endif;

				else:
					$param['bind_person_id']		 = (int)$this->session->userdata('UW_ADMIN_ID');
					$param['bind_person_name']		 = "Admin";
					$param['bind_user_type']		 = "Admin";

					// Removed if case of user_type is Users..
					// if(!empty($data['EDITDATA'])):
					//   $param['pos_number']	  = "";
					//   $param['pos_device_id'] = "";
					// endif;

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
					if( $this->input->post('user_type') != 'Users'):
						$param['summery_pin']        = '1111';
					endif;


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
		$param['status']		=	$statusType;
		$param['token']			=	'';
		$param['login_token']	=	'';
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
	function exportexcel()
	{  
		$this->admin_model->authCheck('view_data');
		/* Export excel button code */

		//Generating Logs
		$this->common_model->generateLogs();

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
		$sheet->setCellValue('B1', 'POS No.');
		$sheet->setCellValue('C1', 'USERS SEQ ID');
		$sheet->setCellValue('D1', 'FIRST NAME');
		$sheet->setCellValue('E1', 'LAST NAME');
		$sheet->setCellValue('F1', 'PHONE');
		$sheet->setCellValue('G1', 'EMAIL');
		$sheet->setCellValue('H1', 'TOTAL ARABIAN POINTS');
		$sheet->setCellValue('I1', 'AVAILABLE ARABIAN POINTS');
		$sheet->setCellValue('J1', 'USER TYPE');
		$sheet->setCellValue('K1', 'BIND WITH');
		$sheet->setCellValue('L1', 'BIND WITH USER ID');
		$sheet->setCellValue('M1', 'BIND WITH USER TYPE');
		$sheet->setCellValue('N1', 'Store Name');
		$sheet->setCellValue('O1', 'Area');
		$sheet->setCellValue('P1', 'CREATION DATE');
		$sheet->setCellValue('Q1', 'DEVICE TYPE');
		$sheet->setCellValue('R1', 'APP VERSION');
		$sheet->setCellValue('S1', 'POS Device ID');
		$sheet->setCellValue('T1', 'STATUS');
		
		$slno = 1;
		$start = 2;
		foreach($data as $d){

			if($d['users_type'] == 'Users'):
				$area = $d['address'];
			else:
				$area = $d['area'];
			endif;

			$sheet->setCellValue('A'.$start, $slno);
			$sheet->setCellValue('B'.$start, $d['pos_number']);
			$sheet->setCellValue('C'.$start, $d['users_seq_id']);
			$sheet->setCellValue('D'.$start, ucwords($d['users_name']));
			$sheet->setCellValue('E'.$start, ucwords($d['last_name']));
			$sheet->setCellValue('F'.$start, $d['users_mobile']);
			$sheet->setCellValue('G'.$start, $d['users_email']);
			$sheet->setCellValue('H'.$start, $d['totalArabianPoints']);
			$sheet->setCellValue('I'.$start, $d['availableArabianPoints']);
			$sheet->setCellValue('J'.$start, $d['users_type']);
			$sheet->setCellValue('K'.$start, $d['bind_person_name']);
			$sheet->setCellValue('L'.$start, $d['bind_person_id']);
			$sheet->setCellValue('M'.$start, $d['bind_user_type']);
			$sheet->setCellValue('N'.$start, $d['store_name']);
			$sheet->setCellValue('O'.$start, $area);
			$sheet->setCellValue('P'.$start, date('d-M-Y', strtotime($d['created_at'])));
			$sheet->setCellValue('Q'.$start, $d['device_type'] );
			$sheet->setCellValue('R'.$start, $d['app_version'] );
			$sheet->setCellValue('S'.$start, $d['pos_device_id'] );
			if($d['status'] == 'A'){
			    $sheet->setCellValue('T'.$start, 'Active');    
			}else{
			    $sheet->setCellValue('T'.$start, 'Inactive');    
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
		$sheet->getStyle('A1:T1')->getFont()->setBold(true);		
		$sheet->getStyle('A1:T1000')->applyFromArray($styleThinBlackBorderOutline);
		//Alignment
		//fONT SIZE
		$sheet->getStyle('A1:T10')->getFont()->setSize(12);
		$sheet->getStyle('A1:T2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
		$sheet->getStyle('A2:T100')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
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
		$sheet->getColumnDimension('T')->setWidth(30);


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

		$this->admin_model->authCheck('view_data');
		/* Export excel button code */
		$data  = $this->common_model->getData('multiple','uw_users',$whereCon);
		// echo '<pre>';print_r($data);die;

		//Generating Logs
		$this->common_model->generateLogs();

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

	/***********************************************************************
	** Function name 	: redeeminglimit
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for change status
	** Date 			: 06 JULY 2023
	************************************************************************/
	function redeeminglimit($editId='')
	{		
		//echo $editId; die();
		$data['error'] 		    = '';
		$data['activeMenu'] 	= 'users';
		$data['activeSubMenu']  = 'allusers';
		
		if($editId):
			$this->admin_model->authCheck('edit_data');
			$data['EDITDATA']				=	$this->common_model->getDataByParticularField('uw_users','users_id',(int)$editId);
			// echo $editId; die();
			// echo '<pre>';print_r($data['EDITDATA']);die;
		else:
			$this->admin_model->authCheck('add_data');
		endif;
		
		$this->session->set_userdata('REDEEMINGAMOUNTLIMIT',currentFullUrl());

		if($this->input->post('SaveChanges')):
			// echo '<pre>';print_r($_POST);die;
			
			$error					=	'NO';
			$this->form_validation->set_rules('redeeming_amount_limit', 'Redeeming Amount Limit', 'trim|required');
			if($this->form_validation->run() && $error == 'NO'): 
				$param['redeeming_amount_limit'] = 	$this->input->post('redeeming_amount_limit');
				
				if($this->input->post('CurrentDataID') !=''):
					$categoryId					=	$this->input->post('CurrentDataID');
					$param['update_ip']			=	currentIp();
					$param['update_date']		=	date('Y-m-d h:i');
					$param['updated_by']		=	(int)$this->session->userdata('UW_ADMIN_ID');
					
					// echo "<pre>";print_r($_POST);die();
					$this->common_model->editData('uw_users',$param,'users_id',(int)$categoryId);
					$this->session->set_flashdata('alert_success',lang('updatesuccess'));
				endif;
				redirect(correctLink('REDEEMINGAMOUNTLIMIT',getCurrentControllerPath('index')));
			endif;
		endif;

		$this->layouts->set_title('Add/Edit - Redeeming Limit');
		$this->layouts->admin_view('users/allusers/addeditredeeminglimitdata',array(),$data);
	}	// END OF FUNCTION	
}
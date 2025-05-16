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
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'recharge';
		$data['activeSubMenu'] 				= 	'allrecharge';

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
		$totalRows  	= 	$this->common_model->getData('count',$tblName,$whereCon,$shortField);
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
		
		$data['ALLDATA'] 					= 	$this->common_model->getData('multiple',$tblName,$whereCon,$shortField,$perPage,$page);
 		

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
		
		$searchField = $this->input->post('searchField');
		$searchValue = $this->input->post('searchValue');
		$fromDate 	 = $this->input->post('fromDate');
		$toDate 	 = $this->input->post('toDate');

		$data['searchField']	= $searchField;
		$data['searchValue']	= $searchValue;
		$data['fromDate']		= $fromDate;  
		$data['toDate']			= $toDate;

		if($fromDate):
			$fromDate	 = date('Y-m-d H:i', strtotime($this->input->post('fromDate')));
			$whereCon['where']['created_at']['$gte']  =  $fromDate;
		endif;
		if($toDate):
			$toDate	 = date('Y-m-d H:i', strtotime($this->input->post('toDate')));
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
			$whereCon['where']['user_id_deb']	= (int)$userData['users_id'];
			// $RechargeWhereCon['narration']	= 'Recharge';
			$whereCon['where']['$or']	=  array(
											    array('narration' => 'Recharge'),
											    array('narration' => 'Reverse Recharge'),
											);
		endif;

		/* Export excel button code */
		$shortField 	 = array('created_at'=> -1);
		$tblName 		 = 'uw_loadBalance';
		$data        	 = $this->common_model->getdata('multiple',$tblName,$whereCon,$shortField);
		// echo '<pre>';print_r($data);die();

        $spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'Sl.No');
		$sheet->setCellValue('B1', 'Reciever Phone');
		$sheet->setCellValue('C1', 'Reciever Name');
		$sheet->setCellValue('D1', 'User Type');
		$sheet->setCellValue('E1', 'UPoints');
		$sheet->setCellValue('F1', 'Remarks');
		$sheet->setCellValue('G1', 'Sender Phone');
		$sheet->setCellValue('H1', 'Sender Name');
		$sheet->setCellValue('I1', 'Sender Type');
		$sheet->setCellValue('J1', 'Receiver Bind With');
		$sheet->setCellValue('K1', 'Date');
		$sheet->setCellValue('L1', 'Time');
		
		$slno  = 1;
		$start = 2;
		foreach($data as $d){
			  if($d['record_type'] == 'Debit'){ 
	            $id = $d['user_id_to'];
	          }elseif ($d['record_type'] == 'Credit' && $d['created_by'] != 'ADMIN') {
	            $id = $d['user_id_cred'];
	          }elseif ($d['record_type'] == 'Credit' && $d['created_by'] == 'ADMIN'){
	            $id = $d['user_id_cred'];
	          }
	          $receiver_users_type   = $this->common_model->getPaticularFieldByFields('users_type', 'uw_users', 'users_id', (int)$id);
	          $receiver_users_name   = $this->common_model->getPaticularFieldByFields('users_name', 'uw_users', 'users_id', (int)$id);
	          $receiver_last_name    = $this->common_model->getPaticularFieldByFields('last_name', 'uw_users', 'users_id', (int)$id);
	          $receiver_users_mobile = $this->common_model->getPaticularFieldByFields('users_mobile', 'uw_users', 'users_id', (int)$id);
	         
	          $receiver_bind_with    = $this->common_model->getPaticularFieldByFields('bind_person_name', 'uw_users', 'users_id', (int)$id);

	          if($receiver_last_name):
	          	$receiver_users_name = $receiver_users_name .' '.$receiver_last_name; 
	          endif;

			 if($d['created_by'] == "ADMIN"):
	          	$wcon['where']  = array('admin_id'=> (int)$d['created_user_id'] );
				$Details	    = $this->common_model->getData('single','uw_admin',$wcon);
		     else:
		     	$wcon['where']  = array('users_id'=> (int)$d['created_by'] );
				$Details		= $this->common_model->getData('single','uw_users',$wcon);
			 endif;

			 // Username
			 if($Details['users_name']):
			 	if($Details['last_name']  ):
			 		$sender_users_name = $Details['users_name'].' '. $Details['last_name'];
			 	else:
			 		$sender_users_name = $Details['users_name'];
			 	endif;
			 elseif($Details['admin_first_name']):
	 			$sender_users_name = $Details['admin_first_name'].' '. $Details['admin_last_name'];
			 else:
		 		$sender_users_name = "-";
			 endif;

			 // User Type
			 if($Details['users_type']):
		 			$sender_users_type =	$Details['users_type'];
			 elseif($Details['admin_type']):
		 			$sender_users_type =	$Details['admin_type'];
		 	 else:
		 			$sender_users_type =	"-";
			 endif;

		  	 // Uusername
			 if($Details['users_mobile']):
		 		$sender_users_mobile =	$Details['users_mobile'];
			 else:
	 			$sender_users_mobile =	$Details['admin_phone'];;
			 endif;

			$sheet->setCellValue('A'.$start, $slno);
			$sheet->setCellValue('B'.$start, $receiver_users_mobile);
			$sheet->setCellValue('C'.$start, $receiver_users_name);
			$sheet->setCellValue('D'.$start, $receiver_users_type);
			$sheet->setCellValue('E'.$start, $d['upoints']);
			$sheet->setCellValue('F'.$start, $d['remarks']);
			$sheet->setCellValue('G'.$start, $sender_users_mobile);
			$sheet->setCellValue('H'.$start, $sender_users_name);
			$sheet->setCellValue('I'.$start, $sender_users_type);
			$sheet->setCellValue('J'.$start, $receiver_bind_with);
			$sheet->setCellValue('K'.$start, date('d-F-Y',strtotime($d['created_at'])));	
			$sheet->setCellValue('L'.$start, date('h:i A',strtotime($d['created_at'])));	
			
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
		$sheet->getStyle('A1:Q1')->getFont()->setBold(true);		
		$sheet->getStyle('A1:Q1')->applyFromArray($styleThinBlackBorderOutline);
		//Alignment
		//fONT SIZE
		//$sheet->getStyle('A1:D10')->getFont()->setSize(12);
		//$sheet->getStyle('A1:D2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
		//$sheet->getStyle('A2:D1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		//Custom width for Individual Columns
		$sheet->getColumnDimension('A')->setWidth(5);
		$sheet->getColumnDimension('B')->setWidth(30);
		$sheet->getColumnDimension('C')->setWidth(30);
		$sheet->getColumnDimension('D')->setWidth(25);
		$sheet->getColumnDimension('E')->setWidth(20);
		$sheet->getColumnDimension('F')->setWidth(20);
		$sheet->getColumnDimension('G')->setWidth(20);
		$sheet->getColumnDimension('H')->setWidth(20);
		$sheet->getColumnDimension('I')->setWidth(30);
		$sheet->getColumnDimension('J')->setWidth(20);
		$sheet->getColumnDimension('K')->setWidth(30);
		$sheet->getColumnDimension('L')->setWidth(30);
		$sheet->getColumnDimension('M')->setWidth(30);
		$sheet->getColumnDimension('N')->setWidth(30);
		$sheet->getColumnDimension('O')->setWidth(30);
		$sheet->getColumnDimension('P')->setWidth(15);
		$sheet->getColumnDimension('Q')->setWidth(10);


		$curdate = date('d-m-Y H:i:s');
		$writer = new Xlsx($spreadsheet);
		$filename = 'Recharge-Coupon-list'.$curdate;
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
		//endif;
		/* Export excel END */
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
				$param11['availableArabianPoints'] = +$data['upoints'];
				$balanceCredit 					   = $this->common_model->manageBalance($tblName,$param11,'users_id',(int)$data['user_id_deb']);

				$from = date('Y-m-d H:i', strtotime($data['created_at']));
				$to = date('Y-m-d H:i', strtotime($data['created_at'] . ' +1 minutes'));

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
					
					// Crediting balance to BTB User..
					$tblName 			= 'uw_users';
					$whereCon['where']  = array('users_id' => (int)$data['user_id_deb']);
					$BTBUserData 		=	$this->common_model->getData('single',$tblName,$whereCon);

					if(!empty($BTBUserData)):
						//Created credited recharge amount and removed commisison..
						$finalAmount		 					 =  $data['upoints'] - $commissionData['upoints'];
						// $param11['totalArabianPoints']    	 	 =  -(float)$finalAmount;
						$param11['availableArabianPoints']    	 =  +(float)$finalAmount;

						$balanceCredit 					   		 = $this->common_model->manageBalance($tblName,$param11,'users_id',(int)$data['user_id_deb']);

						$user_oid = (string)$commissionData['user_oid'];
					    // Generating loadBalance for Commission amount deducting..
		                $commisionBalance['load_balance_id'] =  (int)$this->common_model->getNextSequence('uw_loadBalance');
		                $commisionBalance['user_oid']        =  new MongoDB\BSON\ObjectId($user_oid);
		                $commisionBalance['request_id']      =  $data['load_balance_id'];
		                $commisionBalance['request_oid']     =  new MongoDB\BSON\ObjectId($data['_id']->{'$id'});
		                $commisionBalance['user_id_deb']     =  (int)$data['user_id_deb'];
		                $commisionBalance['user_id_cred']    =  (int)0;
		                $commisionBalance["availableArabianPoints"]  =	(float)$BTBUserData["availableArabianPoints"];
						$commisionBalance["end_balance"] 			 =	(float)$BTBUserData["availableArabianPoints"] - $commissionData['upoints'];
		                $commisionBalance['record_type']     = 'Debit';
		                $commisionBalance['narration']       = 'Recharge Commission Reverted';
		                $commisionBalance['remarks']         = "Commission reverted for recharge worth AED ".$data['upoints'].".";
		                $commisionBalance['upoints']         = (float)$commissionData['upoints'];
		                $commisionBalance['creation_ip']     = $this->input->ip_address();;
		                $commisionBalance['created_at']      = date('Y-m-d H:i');
		                $commisionBalance['created_by']      = (int)$data['user_id_deb'];
		                $commisionBalance['status']          = 'A';



				    	$this->common_model->addData('uw_loadBalance', $commisionBalance);

				    	$commisionBalance1['load_balance_id'] =  (int)$this->common_model->getNextSequence('uw_loadBalance');
		                $commisionBalance1['user_oid']        =  new MongoDB\BSON\ObjectId($user_oid);
		                $commisionBalance1['request_id']      =  $data['load_balance_id'];
		                $commisionBalance1['request_oid']     =  new MongoDB\BSON\ObjectId($data['_id']->{'$id'});
		                $commisionBalance1['user_id_deb']     =  (int)0;
		                $commisionBalance1['user_id_cred']    =  (int)$data['user_id_deb'];
		                $commisionBalance1["availableArabianPoints"]  =	(float)$commisionBalance["end_balance"];
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

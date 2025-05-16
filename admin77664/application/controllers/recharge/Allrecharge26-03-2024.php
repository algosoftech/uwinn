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
		$this->admin_model->authCheck();
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'recharge';
		$data['activeSubMenu'] 				= 	'allrecharge';

		$searchField = $this->input->get('searchField');
		$searchValue = $this->input->get('searchValue');
		$fromDate 	 = $this->input->get('fromDate');
		$toDate 	 = $this->input->get('toDate');

		$data['searchField'] = $searchField;
		$data['searchValue'] = $searchValue;

		$whereCon['where'] 		  		= array('users_id'=>'');
		if($searchField == 'recharge_to'):
				
			if(is_numeric($searchValue)):	
			  $Userwhere['users_mobile'] = (int)$searchValue;	
			else:
			  $Userwhere['users_email']  = $searchValue;	
			endif;
			
			$tblName 			   	  = 'uw_users';
			$UserwhereCon['where'] 	  =  $Userwhere;
			$userData 				  = $this->common_model->getData('single',$tblName,$UserwhereCon,$status);

			$RechargeWhereCon['user_oid']	= new MongoDB\BSON\ObjectId($userData['_id']->{'$id'});
			// $RechargeWhereCon['narration']	= 'Recharge';
			$RechargeWhereCon['$or']	=  array(
											    array('narration' => 'Recharge'),
											    array('narration' => 'Reverse Recharge'),
											);
			$whereCon['where'] 		  		= $RechargeWhereCon;

		endif;
		 
		$this->session->set_userdata('ALLRECHARGEDATA',currentFullUrl());
		$baseUrl 		= 	getCurrentControllerPath('index');
		$qStringdata	=	explode('?',currentFullUrl());
		$suffix			= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		
		$tblName 		= 	'uw_loadBalance';
		$shortField 	= 	array('created_at'=> -1);
		$totalRows  	= 	$this->common_model->getDataByNewQuery('*','count',$tblName,$whereCon,$shortField,'0','0');
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
		// echo '<pre>';print_r($data);die();
 		

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
		$data['activeSubMenu'] 				= 	'allrecharge';

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
					$param['user_id_deb']			=	(int)0;
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
		/* Export excel button code */

					if($this->input->post('searchField') && $this->input->post('searchValue')):
			$sField							=	$this->input->post('searchField');
			$sValue							=	$this->input->post('searchValue');

			
			if($sField == 'recharge_by'):
				
				$recharge_by_id = $this->common_model->getPaticularFieldByFields('users_id', 'uw_users', 'users_email', trim($sValue));
				
				if(empty($recharge_by_id)):
					$recharge_by_id = $this->common_model->getPaticularFieldByFields('admin_id', 'uw_admin', 'admin_email', trim($sValue));
					if(empty($recharge_by_id)):
						$recharge_by_id = 'N/A';
					endif;
				endif;

			elseif($sField == 'recharge_to'):


				if(is_numeric($sValue)):
					
					$recharge_to_id = $users_id = $this->common_model->getPaticularFieldByFields('users_id', 'uw_users', 'users_mobile', (int)($sValue));

				else:
					$recharge_to_id = $users_id = $this->common_model->getPaticularFieldByFields('users_id', 'uw_users', 'users_email', trim($sValue));
				endif;
				// $users_id = $this->common_model->getPaticularFieldByFields('users_id', 'uw_users', 'users_email', trim($sValue));
				$where1 = array('user_id_cred' => (int)$users_id );
				
				if(empty($recharge_to_id)):
					$recharge_to_id = 'N/A';
				endif;

			else:
				$whereCon['like']			 = 	array('0'=>trim($sField),'1'=>trim($sValue));
			endif;

			$data['searchField'] 			= 	$sField;
			$data['searchValue'] 			= 	$sValue;

		else:
			$whereCon['like']		 		= 	"";
			$data['searchField'] 			= 	'';
			$data['searchValue'] 			= 	'';
		endif;

		if($this->input->post('fromDate')){
			$data['fromDate'] 				= 	$this->input->post('fromDate');
			$start_date = date('Y-m-d 00:00',strtotime($data['fromDate']));
			$whereCon['where_gte'] 			= 	array(array("created_at",$start_date));
		}
		if($this->input->post('toDate')){
			$data['toDate'] 				= 	$this->input->post('toDate');
			$end_date = date('Y-m-d 23:59',strtotime($data['toDate']));
			$whereCon['where_lte'] 			= 	array(array("created_at",$end_date));
		}

		if($recharge_by_id):

		$whereCon['where']		 			= 	array(
													'$or'=>array(
																array('narration' => 'Recharge'),
																array('narration' => 'Reverse Recharge')
																),
													'created_user_id' => $recharge_by_id
													);	

		elseif($recharge_to_id):


		$whereCon['where']		 			= 	array(
													'$or'=>array(
																array('narration' => 'Recharge'),
																array('narration' => 'Reverse Recharge')
																),
													'user_id_to' => $recharge_to_id
													);	

		else:
			$whereCon['where']		 			= 	array( 
														'$or'=>array(
																	array('narration' => 'Recharge'),
																	array('narration' => 'Reverse Recharge')
																	)
														);	
		endif;

		$shortField 						= 	array('created_at'=> -1);

		$tblName 							= 	'uw_loadBalance';
		$data        						=   $this->common_model->getDataByNewQuery('*','multiple',$tblName,$whereCon,$shortField,0,0);
		// echo '<pre>';print_r($data);die();


        $spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'Sl.No');
		$sheet->setCellValue('B1', 'Reciever Phone');
		$sheet->setCellValue('C1', 'Reciever Name');
		$sheet->setCellValue('D1', 'User Type');
		$sheet->setCellValue('E1', 'Recharge AP');
		$sheet->setCellValue('F1', 'Margin percentage');
		$sheet->setCellValue('G1', 'Margin Amount');
		$sheet->setCellValue('H1', 'Record Type');
		$sheet->setCellValue('I1', 'Recharged Amount');
		$sheet->setCellValue('J1', 'Remarks');
		$sheet->setCellValue('K1', 'Sender Phone');
		$sheet->setCellValue('L1', 'Sender Name');
		$sheet->setCellValue('M1', 'Sender Type');
		$sheet->setCellValue('N1', 'Receiver Bind With');
		$sheet->setCellValue('O1', 'Receiver Store Name');
		$sheet->setCellValue('P1', 'Date');
		$sheet->setCellValue('Q1', 'Time');
		
		$slno = 1;
		$start = 2;
		foreach($data as $d){
			  if($d['record_type'] == 'Debit'){ 
	            $id = $d['user_id_to'];
	          }elseif ($d['record_type'] == 'Credit' && $d['created_by'] != 'ADMIN') {
	            $id = $d['user_id_cred'];
	          }elseif ($d['record_type'] == 'Credit' && $d['created_by'] == 'ADMIN'){
	            $id = $d['user_id_cred'];
	          }
	          $receiver_users_type = $this->common_model->getPaticularFieldByFields('users_type', 'uw_users', 'users_id', (int)$id);
	          $receiver_users_name = $this->common_model->getPaticularFieldByFields('users_name', 'uw_users', 'users_id', (int)$id);
	          $receiver_last_name = $this->common_model->getPaticularFieldByFields('last_name', 'uw_users', 'users_id', (int)$id);
	          $receiver_users_mobile = $this->common_model->getPaticularFieldByFields('users_mobile', 'uw_users', 'users_id', (int)$id);
	          $receiver_store_name = $this->common_model->getPaticularFieldByFields('store_name', 'uw_users', 'users_id', (int)$id);
	          $receiver_bind_with = $this->common_model->getPaticularFieldByFields('bind_person_name', 'uw_users', 'users_id', (int)$id);

	          if($receiver_last_name):
	          	$receiver_users_name = $receiver_users_name .' '.$receiver_last_name; 
	          endif;

			 if($d['created_by'] == "ADMIN"):

	          	$wcon['where']  = array('admin_id'=> (int)$d['created_user_id'] );
				$Details	=	$this->common_model->getData('single','uw_admin',$wcon);
		     
		     else:

		     	$wcon['where']  = array('users_id'=> (int)$d['created_user_id'] );
				$Details	=	$this->common_model->getData('single','uw_users',$wcon);

			 endif;

			 // Username
			 if($Details['users_name']):

			 	if($Details['last_name']  ):
			 	
			 		$sender_users_name =	$Details['users_name'].' '. $Details['last_name'];
			 	
			 	else:
			 		$sender_users_name =	$Details['users_name'];
			 	endif;

			 elseif($Details['admin_first_name']):
		 			$sender_users_name =	$Details['admin_first_name'].' '. $Details['admin_last_name'];
			 else:
			 		$sender_users_name =	"-";
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

			 // feacting bind with dettails.
			 $ReceiverID = $d['created_user_id'];

			 $wcon['where']  = array('users_id'=> (int)$ReceiverID );
			 $ReceivrDetails	=	$this->common_model->getData('single','uw_users',$wcon);
			 if($ReceivrDetails['bind_person_name']):
			 	$bind_with = $ReceivrDetails['bind_person_name'];
			 else:
	          	$bind_with = "-";
			 endif;

			 // if(!empty($sender_users_mobile) && !empty($sender_users_name)):
	         //  	$bind_with = $sender_users_name;
	         //  elseif(!empty($sender_users_mobile)):
	         //  	$bind_with = $sender_users_mobile;
	         //  else:
	         //  	$bind_with = "-";
	         //  endif;

	         
            if($d['rechargeDetails']):
              $margin_percentage = $d['upoints'].' + '.$d['rechargeDetails']['percentage'].'% = '.$d['sum_arabian_points'];
            else:
              $margin_percentage = '-';
            endif;

			$sheet->setCellValue('A'.$start, $slno);
			$sheet->setCellValue('B'.$start, $receiver_users_mobile);
			$sheet->setCellValue('C'.$start, $receiver_users_name);
			$sheet->setCellValue('D'.$start, $receiver_users_type);
			$sheet->setCellValue('E'.$start, $d['upoints']);

			if($d['rechargeDetails']['percentage']):
				$recharagePercentage =  $d['rechargeDetails']['percentage'].'%';
			else:
				$recharagePercentage = '0%';
			endif;

			$sheet->setCellValue('F'.$start, $recharagePercentage);
			$sheet->setCellValue('G'.$start, $d['rechargeDetails']['amount']);
			$sheet->setCellValue('H'.$start, $d['record_type']);
			$sheet->setCellValue('I'.$start, $d['sum_arabian_points']);
			$sheet->setCellValue('J'.$start, $d['remarks']);
			$sheet->setCellValue('K'.$start, $sender_users_mobile);
			$sheet->setCellValue('L'.$start, $sender_users_name);
			$sheet->setCellValue('M'.$start, $sender_users_type);
			$sheet->setCellValue('N'.$start, $receiver_bind_with);
			$sheet->setCellValue('O'.$start, $receiver_store_name);
			$sheet->setCellValue('P'.$start, date('d-F-Y',strtotime($d['created_at'])));	
			$sheet->setCellValue('Q'.$start, date('h:i A',strtotime($d['created_at'])));	
			
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
		
		if($data <> ''):
			if($data['record_type'] == 'Debit' && $data['created_by'] != 'ADMIN'):
				//Reverse debited recharge 
				$whereCon['where']  = array('users_id' => (int)$data['user_id_deb']);
				$userdata 	=	$this->common_model->getData('single','uw_users',$whereCon);
				$arabianPoint = $userdata['availableArabianPoints'] + $data['upoints'];
				$DRparam['narration']		=	'Reverse Recharge';
				$DRparam['status']					=	'R';
				$this->common_model->editData('uw_loadBalance',$DRparam,'load_balance_id',(int)$data['load_balance_id']); //Change load balence status
				//Add arabian point to debited user's account
				$DParams['availableArabianPoints']	=	(int)$arabianPoint;
				$this->common_model->editData('uw_users',$DParams,'users_id',(int)$data['user_id_deb']);
				// END
				//Reverse Credited Recharge
				$wcon1['where']						= 	array(
														'user_id_cred'			=>	(int)$data['user_id_to'],
														'created_user_id' 		=>	(int)$data['created_user_id'],
														'upoints' 				=>	(int)$data['upoints'],
														'record_type'			=>	'Credit', 
														'narration'	=>	'Recharge');
				$wcon1['like']						=	array('created_at', date('Y-m-d',strtotime($data['created_at'])));
				$creaditedData						=	$this->common_model->getData('single','uw_loadBalance',$wcon1);
				if($creaditedData <> ''):
					//Change Creadit Status
					$CRparam['narration']		=	'Reverse Recharge';
					$CRparam['status']					=	'R';
					$this->common_model->editData('uw_loadBalance',$DRparam,'load_balance_id',(int)$creaditedData['load_balance_id']); //Change load balence status
					$wcon2['where']					=	array('users_id' => $creaditedData['user_id_cred']);
					$creaditedUserData 				=	$this->common_model->getData('single','uw_users',$wcon2);
				
					$arabianPoint2 					=	 $creaditedUserData['availableArabianPoints'] - $creaditedData['upoints'];
					//Deduct arabian point to debited user's account
					$CParams['availableArabianPoints']	=	(int)$arabianPoint2;
					$this->common_model->editData('uw_users',$CParams,'users_id',(int)$creaditedData['user_id_cred']);
					// END
				endif;
				$this->session->set_flashdata('alert_success','Recharge Reverse Successfully.');
				redirect('recharge/allrecharge/index');
			elseif($data['record_type'] == 'Credit' && $data['created_by'] != 'ADMIN'):
				//Reverse Creadet recharge 
				$whereCon['where']  = array('users_id' => (int)$data['user_id_cred']);
				$userdata 	=	$this->common_model->getData('single','uw_users',$whereCon);
				$arabianPoint = $userdata['availableArabianPoints'] - $data['upoints'];
				$DRparam['narration']		=	'Reverse Recharge';
				$DRparam['status']					=	'R';
				$this->common_model->editData('uw_loadBalance',$DRparam,'load_balance_id',(int)$data['load_balance_id']); //Change load balence status
				
				//Deduct arabian point to Creadeted user's account
				$DParams['availableArabianPoints']	=	(int)$arabianPoint;
				// $this->common_model->editData('uw_users',$DParams,'users_id',(int)$data['user_id_cred']);
				

				// END
				//Reverse Debited Recharge
				$wcon1['where']						= 	array(
															'user_id_to'			=>	(int)$data['user_id_cred'],
															'created_user_id' 		=>	(int)$data['created_user_id'],
															'upoints' 		=>	(int)$data['upoints'],
															'record_type'			=>	'Debit', 
															'narration'	=>	'Recharge');
				$wcon1['like']						=	array('created_at', date('Y-m-d',strtotime($data['created_at'])));
				$debitedData						=	$this->common_model->getData('single','uw_loadBalance',$wcon1);
				if($debitedData <> ''):
					//Change Debited Status
					$DRparam['narration']		=	'Reverse Recharge';
					$DRparam['status']					=	'R';
					$this->common_model->editData('uw_loadBalance',$DRparam,'load_balance_id',(int)$debitedData['load_balance_id']); //Change load balence status
					$wcon2['where']					=	array('users_id' => $debitedData['user_id_deb']);
					$debitedUserData 				=	$this->common_model->getData('single','uw_users',$wcon2);
					$arabianPoint2 					=	 $debitedUserData['availableArabianPoints'] + $debitedData['upoints'];
					//Deduct arabian point to debited user's account
					$CParams['availableArabianPoints']	=	(int)$arabianPoint2;
					$this->common_model->editData('uw_users',$CParams,'users_id',(int)$debitedData['user_id_deb']);
					// END
				endif;
				$this->session->set_flashdata('alert_success','Recharge Reverse Successfully.');
				redirect('recharge/allrecharge/index');
			elseif($data['created_by'] == 'ADMIN'):


				$whereCon['where']  = array('users_id' => (int)$data['user_id_cred']);
				$userdata 			=	$this->common_model->getData('single','uw_users',$whereCon);
				

				$param["user_oid"] 				=	new MongoDB\BSON\ObjectId(($userdata['_id']->{'$id'} ));
				$param["user_id_deb"] 			=	(int)$userdata['users_id'];
				$param['user_id_cred']			=	(int)0;
				$param['upoints']				=	(float)$data['sum_arabian_points'];
				$param['sum_arabian_points']	=	(float)$data['sum_arabian_points'];
				$param['availableArabianPoints']=   (float)$userdata['availableArabianPoints'];
				$param["end_balance"] 			=	(float)$userdata["availableArabianPoints"] - (float)$data['sum_arabian_points'] ;
				if($this->input->post('percentage')):
					$param['rechargeDetails']	=	$rechargeDetails;
				endif;	
				$param['record_type']			=	'Debit';
				$param["narration"] 			=	'Reverse Recharge';
				$param['remarks']				=	"Recharge reversed by Admin";
				$param['load_balance_id']		=	(int)$this->common_model->getNextSequence('uw_loadBalance');
				$param['creation_ip']			=	currentIp();
				$param['created_at']			=	date('Y-m-d H:i:s');//currentDateTime();
				$param['created_by']			=	'ADMIN';
				$param['update_date']			=	date('Y-m-d H:i:s');//currentDateTime();
				$param['status']				=	'R';
				$param["created_user_id"] 		=	(int)$this->session->userdata('UW_ADMIN_ID');
				$alastInsertId					=	$this->common_model->addData('uw_loadBalance',$param);

				//Deduct arabian point to Creadeted user's account
				$DParams['availableArabianPoints']	=	(float)$param["end_balance"];
				$this->common_model->editData('uw_users',$DParams,'users_id',(int)$data['user_id_cred']);
				// END
				$this->session->set_flashdata('alert_success','Recharge Reverse Successfully.');
				redirect('recharge/allrecharge/index');
			endif;
		else:
			$this->session->set_flashdata('alert_error','Data not found.');
			redirect('recharge/allrecharge/index');
		endif;
	} 


}

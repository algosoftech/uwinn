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

class Registration extends CI_Controller {

	public function  __construct() 
	{ 
		parent:: __construct();
		error_reporting(E_ALL ^ E_NOTICE);  
		$this->load->model(array('admin_model','emailtemplate_model','sms_model','notification_model'));
		$this->lang->load('statictext', 'admin');
		$this->load->helper('common');
	} 

	/* * *********************************************************************
	 * * Function name  : Subadmin
	 * * Developed By 	: Manoj Kumar
	 * * Purpose  		: This function used for Subadmin
	 * * Date 			: 14 JUNE 2021
	 * * updated By     : Dilip  Halder
	 * * updated Date 	: 18 Sep 2023
	 * * **********************************************************************/
	public function index()
	{	
		$this->admin_model->authCheck('view_data');
		$this->admin_model->getPermissionType($data); 
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'statistics_report';
		$data['activeSubMenu'] 				= 	'registration';
		if($this->input->get('clearAllSearch')){
			redirect(correctLink('MASTERDATARECHARGETYPE',getCurrentControllerPath('index')));
		}
		if($this->input->get('fromDate') && $this->input->get('toDate')):
			$data['month']	 				= 	'';

			$fromDate =  $this->input->get('fromDate');
			$toDate =  $this->input->get('toDate');
			$data['fromDate'] 			= 	$fromDate.' 00:01';
			$data['toDate'] 			= 	$toDate.' 23:59';

			$whereCon['where_gte'] 			= 	array(array("created_at", $fromDate));
			$whereCon['where_lte'] 			= 	array(array("created_at", $toDate));

		elseif($this->input->get('fromDate')):
			$data['month']	 				= 	'';

			$fromDate =  $this->input->get('fromDate');
			$data['fromDate'] 			= 	$fromDate.' 00:01';
			// $data['toDate'] 			= 	$toDate;

			$whereCon['where_gte'] 			= 	array(array("created_at", $fromDate));
			$whereCon['where_lte'] 			= 	array(array("created_at",date('Y-m-d')));

		elseif($this->input->get('showLength')):
			//$whereCon['where']		 		= 	["arabian_points_from" => 'Recharge'];
			$data['month']	 				= 	$this->input->get('showLength');
		else:
			//echo 'working';die();
			$whereCon['like']		 		= 	"";
			$data['searchField'] 			= 	'';
			$data['searchValue'] 			= 	'';
			//$whereCon['where']		 		= 	["arabian_points_from" => 'Recharge'];
			// $data['month']	 				= 	date('m');
		endif;
		//echo $data['month'];die();
		if($data['month']):
			switch($data['month']){
				case 1:
					$whereCon['where_gte'] 			= 	array(array("created_at", date('Y').'-01-01'));
					$whereCon['where_lte'] 			= 	array(array("created_at", date('Y').'-02-01'));
				break;
				case 2:
					$whereCon['where_gte'] 			= 	array(array("created_at", date('Y').'-02-01'));
					$whereCon['where_lte'] 			= 	array(array("created_at", date('Y').'-03-01'));
				break;
				case 3:
					$whereCon['where_gte'] 			= 	array(array("created_at", date('Y').'-03-01 00:00'));
					$whereCon['where_lte'] 			= 	array(array("created_at", date('Y').'-04-01 00:00'));
				break;
				case 4:
					$whereCon['where_gte'] 			= 	array(array("created_at", date('Y').'-04-01'));
					$whereCon['where_lte'] 			= 	array(array("created_at", date('Y').'-05-01'));
				break;
				case 5:
					$whereCon['where_gte'] 			= 	array(array("created_at", date('Y').'-05-01'));
					$whereCon['where_lte'] 			= 	array(array("created_at", date('Y').'-06-01'));
				break;
				case 6:
					$whereCon['where_gte'] 			= 	array(array("created_at", date('Y').'-06-01'));
					$whereCon['where_lte'] 			= 	array(array("created_at", date('Y').'-07-01'));
				break;
				case 7:
					$whereCon['where_gte'] 			= 	array(array("created_at", date('Y').'-07-01'));
					$whereCon['where_lte'] 			= 	array(array("created_at", date('Y').'-08-01'));
				break;
				case 8:
					$whereCon['where_gte'] 			= 	array(array("created_at", date('Y').'-08-01'));
					$whereCon['where_lte'] 			= 	array(array("created_at", date('Y').'-09-01'));
				break;
				case 9:
					$whereCon['where_gte'] 			= 	array(array("created_at", date('Y').'-09-01'));
					$whereCon['where_lte'] 			= 	array(array("created_at", date('Y').'-10-01'));
				break;
				case 10:
					$whereCon['where_gte'] 			= 	array(array("created_at", date('Y').'-10-01'));
					$whereCon['where_lte'] 			= 	array(array("created_at", date('Y').'-11-01'));
				break;
				case 11:
					$whereCon['where_gte'] 			= 	array(array("created_at", date('Y').'-11-01'));
					$whereCon['where_lte'] 			= 	array(array("created_at", date('Y').'-12-01'));
				break;
				case 12:
					$whereCon['where_gte'] 			= 	array(array("created_at", date('Y').'-12-01'));
					$whereCon['where_lte'] 			= 	array(array("created_at", date('Y',strtotime('+1 year')).'-01-01'));
				break;
				default:
					$data['fromDate'] 				= 	$this->input->get('fromDate');
					$whereCon['where_gte'] 			= 	array(array("created_at",$data['fromDate']));
					$data['toDate'] 				= 	$this->input->get('toDate');
					$whereCon['where_lte'] 			= 	array(array("created_at",date('Y-m-d', strtotime($data['toDate'].' +1 day'))));
				break;
			}
		endif;
		//echo '<pre>'; print_r($whereCon);
		$whereCon['where'] = ['users_type' => 'Users'];
		$data['ALLDATA']['total_user']		=	$this->common_model->getRegistrationStatistics($whereCon);

		$whereCon['where'] = ['users_type' => 'Users' , 'status' => 'A'];
		$data['ALLDATA']['total_active_user']		=	$this->common_model->getRegistrationStatistics($whereCon);
		
		$whereCon['where'] = array('users_type' => 'Users' ,
									'$or' => array( 
												array('status' => 'I'), 
												array('status' => 'D') 
									));

		$data['ALLDATA']['total_inactive_user']		=	$this->common_model->getRegistrationStatistics($whereCon);

		$whereCon['where'] = array('users_type' => 'Users' ,
									'$and' => array( 
										array('device_type' =>   array('$ne' => 'android' )), 
										array('device_type' =>   array('$ne' => 'ios' ))
									));

		$data['ALLDATA']['total_web_user']		=	$this->common_model->getRegistrationStatistics($whereCon);
		

		$whereCon['where'] = ['users_type' => 'Users' , 'device_type' => 'ios'];
		$data['ALLDATA']['total_ios_user']		=	$this->common_model->getRegistrationStatistics($whereCon);

		$whereCon['where'] = ['users_type' => 'Users' , 'device_type' => 'android'];
		$data['ALLDATA']['total_android_user']		=	$this->common_model->getRegistrationStatistics($whereCon);

		$whereCon['where'] = ['users_type' => 'Users' , 'device_type' => 'android' ,'app_name' => 'Arabian plus' ];
		$data['ALLDATA']['total_arabianplus_user']		=	$this->common_model->getRegistrationStatistics($whereCon);

		$whereCon['where'] = ['users_type' => 'Users' , 'device_type' => 'android' ,'app_name' => 'Huawei' ];
		$data['ALLDATA']['total_huawei_user']		=	$this->common_model->getRegistrationStatistics($whereCon);

		$whereCon['where'] = ['users_type' => 'Retailer'];
		$data['ALLDATA']['total_retailser']		=	$this->common_model->getRegistrationStatistics($whereCon);

		$whereCon['where'] = ['users_type' => 'Sales Person'];
		$data['ALLDATA']['total_sales']		=	$this->common_model->getRegistrationStatistics($whereCon);
		$this->layouts->set_title('Recharge | Statistics | Dealz Arabia');
		$this->layouts->admin_view('statistics/registration/index',array(),$data);
	}	// END OF FUNCTION
	
	// /* * *********************************************************************
	//  * * Function name : addeditdata
	//  * * Developed By : Manoj Kumar
	//  * * Purpose  : This function used for add edit data
	//  * * Date : 14 JUNE 2021
	//  * * **********************************************************************/
	// public function registrationListByEmail()
	// {
	// 	$this->admin_model->authCheck();
	// 	$data['error'] 						= 	'';
	// 	$data['activeMenu'] 				= 	'statistics_report';
	// 	$data['activeSubMenu'] 				= 	'registration';

	// 	if($this->input->get('clearAllSearch')){
	// 		redirect(correctLink('MASTERDATARECHARGETYPE',getCurrentControllerPath('statistics_report/registration/registrationListByEmail')));
	// 	}
	// 	if($this->input->get()){
	// 		$data['showEntry'] 	= 	$this->input->get('shortBy');
	// 		$data['email']		=	$this->input->get('email');
	// 		$data['shortBy']	=	$this->input->get('shortBy'); 
	// 		if($this->input->get('email')){
	// 			if($this->input->get('shortBy') == 'Admin' || $this->input->get('shortBy') == 'Sub Admin'):
	// 				$user_details 	= 	$this->common_model->getParticularFieldByMultipleCondition(array('admin_id', 'admin_email','admin_type'), 'hcap_admin', $whe);
	// 				$user_data	=	array(
	// 					'user_email' 	=>	$user_details['admin_email'],
	// 					'user_id'		=>	$user_details['admin_id'],
	// 					'user_type'		=>	$user_details['admin_type']
	// 				);
	// 			else:
	// 				$user_details 			= 	$this->common_model->getParticularFieldByMultipleCondition(array('users_id', 'users_email','users_type'), 'da_users', ['users_email' => $data['email']]);
	// 				//echo '<pre>'; print_r($user_details);die();
	// 				$user_data	=	array(
	// 					'user_email' 	=>	$user_details['users_email'],
	// 					'user_id'		=>	$user_details['users_id'],
	// 					'user_type'		=>	$user_details['users_type']
	// 				);
	// 			endif;
	// 			$whereCon['where']		 			= 	array(
	// 														'created_by' => (int)$user_data['user_id'],
	// 														'created_by_urse_type' => $user_data['user_type'],
	// 														);
	// 		}else{
	// 			$data['created_by']					=	$this->session->userdata('HCAP_ADMIN_ID');
	// 			$whereCon['where']					=	array(
	// 														'created_by' => $data['created_by'],
	// 														'$or' => array( 
	// 															array('created_by_urse_type' => 'Sub Admin'), 
	// 															array('created_by_urse_type' => 'Super Admin'),		
	// 														));
	// 		}
	// 	}else{
	// 		$data['email']						=	$this->session->userdata('HCAP_ADMIN_EMAIL');
	// 		$data['shortBy']					=	'Admin';
	// 		$whereCon['where']					=	array(
	// 													'created_by' => $this->session->userdata('HCAP_ADMIN_ID'),
	// 													'$or' => array( 
	// 														array('created_by_urse_type' => 'Sub Admin'), 
	// 														array('created_by_urse_type' => 'Super Admin'),		
	// 													));
	// 	}
	// 	if($this->input->get('fromDate')){
	// 		$data['fromDate'] 				= 	$this->input->get('fromDate');
	// 		$whereCon['where_gte'] 			= 	array(array("created_at",$data['fromDate']));
	// 	}
	// 	if($this->input->get('toDate')){
	// 		$data['toDate'] 				= 	$this->input->get('toDate');
	// 		$whereCon['where_lte'] 			= 	array(array("created_at",date('Y-m-d', strtotime($data['toDate'].'+1 days'))));
	// 	}
	// 	$shortField 						= 	array('created_at'=> -1);
	// 	$baseUrl 							= 	getCurrentControllerPath('statistics_report/registration/registrationListByEmail');
	// 	$this->session->set_userdata('ALLRECHARGEDATA',currentFullUrl());
	// 	$qStringdata						=	explode('?',currentFullUrl());
	// 	$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
	// 	$tblName 							= 	'da_users';
	// 	$con 								= 	'';
		
	// 	$totalRows 							= 	$this->common_model->getDataByNewQuery('*','count',$tblName,$whereCon,$shortField,'0','0');
	// 	if($this->input->get('showLength') == 'All'):
	// 		$perPage	 					= 	$totalRows;
	// 		$data['perpage'] 				= 	$this->input->get('showLength');  
	// 	elseif($this->input->get('showLength')):
	// 		$perPage	 					= 	$this->input->get('showLength'); 
	// 		$data['perpage'] 				= 	$this->input->get('showLength'); 
	// 	else:
	// 		$perPage	 					= 	SHOW_NO_OF_DATA;
	// 		$data['perpage'] 				= 	SHOW_NO_OF_DATA; 
	// 	endif;

	// 	$uriSegment 						= 	getUrlSegment();
	//     $data['PAGINATION']					=	adminPagination($baseUrl,$suffix,$totalRows,$perPage,$uriSegment);

    //    if($this->uri->segment(getUrlSegment())):
    //        $page = $this->uri->segment(getUrlSegment());
    //    else:
    //        $page = 0;
    //    endif;
	// 	//$data['forAction'] 					= 	$baseUrl; 
	// 	if($totalRows):
	// 		$first							=	(int)($page)+1;
	// 		$data['first']					=	$first;
			
	// 		if($data['perpage'] == 'All'):
	// 			$pageData 					=	$totalRows;
	// 		else:
	// 			$pageData 					=	$data['perpage'];
	// 		endif;
			
	// 		$last							=	((int)($page)+$pageData)>$totalRows?$totalRows:((int)($page)+$pageData);
	// 		$data['noOfContent']			=	'Showing '.$first.'-'.$last.' of '.$totalRows.' items';
	// 	else:
	// 		$data['first']					=	1;
	// 		$data['noOfContent']			=	'';
	// 	endif;
	// 	$data['excelExportCondition']		= 	base64_encode(json_encode($whereCon));
	// 	$data['ALLDATA'] 					= 	$this->common_model->getDataByNewQuery('*','multiple',$tblName,$whereCon,$shortField,$perPage,$page);
	// 	//echo'<pre>'; print_r($data['ALLDATA']);die();
	// 	$this->layouts->set_title('Recharge | Users Statistics | Dealz Arabia');
	// 	$this->layouts->admin_view('statistics/registration/registration_list',array(),$data);
	// 	//echo 'working';die();
	// }// END OF FUNCTION	


	/* * *********************************************************************
	 * * Function name : addeditdata
	 * * Developed By : Manoj Kumar
	 * * Purpose  : This function used for add edit data
	 * * Date : 14 JUNE 2021
	 * * **********************************************************************/
	public function registrationListByEmail($email = '')
	{
		$this->admin_model->authCheck();
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'statistics_report';
		$data['activeSubMenu'] 				= 	'registration';

		if($this->input->get('clearAllSearch')){
			redirect(correctLink('MASTERDATARECHARGETYPE',getCurrentControllerPath('statistics_report/registration/registrationListByEmail')));
		}
		if($email == ''){
			redirect(correctLink('MASTERDATARECHARGETYPE',getCurrentControllerPath('statistics_report/registration/index')));
		}
		$wcon['where']	=	array('users_email' => $email);
		$user_details 			= 	$this->common_model->getData('single', 'da_users',$wcon);
					//echo '<pre>'; print_r($user_details);die();
					$user_data	=	array(
						'user_email' 	=>	$user_details['users_email'],
						'user_id'		=>	$user_details['users_id'],
						'user_type'		=>	$user_details['users_type']
					);
		
		$whereCon['where']				= 	array(
											'created_by' => (int)$user_data['user_id'],
											'created_by_urse_type' => $user_data['user_type'],
											);
		// if($this->input->get()){
		// 	$data['showEntry'] 	= 	$this->input->get('shortBy');
		// 	$data['email']		=	$this->input->get('email');
		// 	$data['shortBy']	=	$this->input->get('shortBy'); 
		// 	if($this->input->get('email')){
		// 		if($this->input->get('shortBy') == 'Admin' || $this->input->get('shortBy') == 'Sub Admin'):
		// 			$user_details 	= 	$this->common_model->getParticularFieldByMultipleCondition(array('admin_id', 'admin_email','admin_type'), 'hcap_admin', $whe);
		// 			$user_data	=	array(
		// 				'user_email' 	=>	$user_details['admin_email'],
		// 				'user_id'		=>	$user_details['admin_id'],
		// 				'user_type'		=>	$user_details['admin_type']
		// 			);
		// 		else:
		// 			$user_details 			= 	$this->common_model->getParticularFieldByMultipleCondition(array('users_id', 'users_email','users_type'), 'da_users', ['users_email' => $data['email']]);
		// 			//echo '<pre>'; print_r($user_details);die();
		// 			$user_data	=	array(
		// 				'user_email' 	=>	$user_details['users_email'],
		// 				'user_id'		=>	$user_details['users_id'],
		// 				'user_type'		=>	$user_details['users_type']
		// 			);
		// 		endif;
		// 		$whereCon['where']		 			= 	array(
		// 													'created_by' => (int)$user_data['user_id'],
		// 													'created_by_urse_type' => $user_data['user_type'],
		// 													);
		// 	}else{
		// 		$data['created_by']					=	$this->session->userdata('HCAP_ADMIN_ID');
		// 		$whereCon['where']					=	array(
		// 													'created_by' => $data['created_by'],
		// 													'$or' => array( 
		// 														array('created_by_urse_type' => 'Sub Admin'), 
		// 														array('created_by_urse_type' => 'Super Admin'),		
		// 													));
		// 	}
		// }else{
		// 	$data['email']						=	$this->session->userdata('HCAP_ADMIN_EMAIL');
		// 	$data['shortBy']					=	'Admin';
		// 	$whereCon['where']					=	array(
		// 												'created_by' => $this->session->userdata('HCAP_ADMIN_ID'),
		// 												'$or' => array( 
		// 													array('created_by_urse_type' => 'Sub Admin'), 
		// 													array('created_by_urse_type' => 'Super Admin'),		
		// 												));
		// }
		if($this->input->get('fromDate')){
			$data['fromDate'] 				= 	$this->input->get('fromDate');
			$whereCon['where_gte'] 			= 	array(array("created_at",$data['fromDate']));
		}
		if($this->input->get('toDate')){
			$data['toDate'] 				= 	$this->input->get('toDate');
			$whereCon['where_lte'] 			= 	array(array("created_at",date('Y-m-d', strtotime($data['toDate'].'+1 days'))));
		}

		$shortField 						= 	array('created_at'=> -1);
		$baseUrl 							= 	getCurrentControllerPath('statistics_report/registration/registrationListByEmail');
		$this->session->set_userdata('ALLRECHARGEDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 	'da_users';
		$con 								= 	'';
		// echo '<pre>';echo $email; print_r($wcon); print_r($user_details);
		// echo '<pre>'; print_r($whereCon);

		$totalRows 							= 	$this->common_model->getDataByNewQuery('*','count',$tblName,$whereCon,$shortField,'0','0');
		//echo $totalRows;die();
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
		//$data['forAction'] 					= 	$baseUrl; 
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
		$data['excelExportCondition']		= 	base64_encode(json_encode($whereCon));
		$data['ALLDATA'] 					= 	$this->common_model->getDataByNewQuery('*','multiple',$tblName,$whereCon,$shortField,$perPage,$page);
		//echo'<pre>'; print_r($data['ALLDATA']);die();
		$this->layouts->set_title('Recharge | Users Statistics | Dealz Arabia');
		$this->layouts->admin_view('statistics/registration/registration_list',array(),$data);
		//echo 'working';die();
	}// END OF FUNCTION	
	
	/* * *********************************************************************
	 * * Function name : addeditdata
	 * * Developed By : AFSAR ALI
	 * * Purpose  : This function used for add edit data
	 * * Date : 06 DEC 2021
	 * * **********************************************************************/
	public function users_list()
	{
		$this->admin_model->authCheck();
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'statistics_report';
		$data['activeSubMenu'] 				= 	'registration';

		if($this->input->get('clearAllSearch')){
			redirect(correctLink('MASTERDATARECHARGETYPE',getCurrentControllerPath('statistics_report/registration/registrationListByEmail')));
		}
		if($this->input->get()){
			$data['showEntry'] 	= 	$this->input->get('shortBy');
			//$data['email']		=	$this->input->get('email');
			$data['shortBy']	=	$this->input->get('shortBy'); 
			//$data['shortBy']					=	'All';
			if($data['shortBy'] == 'All'){
				$whereCon['where']					=	array(
															'$or' => array( 
																array('users_type' => 'Retailer'), 
																array('users_type' => 'Sales Person'),		
															));
			}else{
				$whereCon['where']					=	array( 'users_type' => 'Retailer', 'users_type' => $data['shortBy']);
			}
			
		}else{
			//$data['email']						=	$this->session->userdata('HCAP_ADMIN_EMAIL');
			$data['shortBy']					=	'All';
			$whereCon['where']					=	array(
														'$or' => array( 
															array('users_type' => 'Retailer'), 
															array('users_type' => 'Sales Person'),		
														));
		}

		$shortField 						= 	array('users_type'=> 1);
		$baseUrl 							= 	getCurrentControllerPath('statistics_report/registration/registrationListByEmail');
		$this->session->set_userdata('ALLRECHARGEDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 	'da_users';
		$con 								= 	'';
		
		$totalRows 							= 	$this->common_model->getDataByNewQuery('*','count',$tblName,$whereCon,$shortField,'0','0');
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
		//$data['forAction'] 					= 	$baseUrl; 
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
		$data['excelExportCondition']		= 	base64_encode(json_encode($whereCon));
		$data['ALLDATA'] 					= 	$this->common_model->getDataByNewQuery('*','multiple',$tblName,$whereCon,$shortField,$perPage,$page);
		//echo'<pre>'; print_r($data['ALLDATA']);die();
		$this->layouts->set_title('Recharge | Users Statistics | Dealz Arabia');
		$this->layouts->admin_view('statistics/registration/users_list',array(),$data);
		//echo 'working';die();
	}// END OF FUNCTION	
	
	/***********************************************************************
	** Function name 	: exportexcel
	** Developed By 	: Afsar Ali
	** Purpose  		: This function used for export deleted users data
	** Date 			: 09 APRIL 2022
	** Updated Date 	: 
	** Updated By   	: 
	************************************************************************/
	function exportexcel($where='')
	{  
		/* Export excel button code */
		$data = base64_decode($where);
		$where = json_decode($data,true);
		$shortField 			= 	array('created_at'=> -1);
		$tblName 				= 	'da_users';
		$data        						=   $this->common_model->getDataByNewQuery('*','multiple',$tblName,(array)$where,$shortField,0,0);
		//echo'<pre>';print_r($data);die();
        $spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'Sl.No');
		$sheet->setCellValue('B1', 'User ID');
		$sheet->setCellValue('C1', 'Name');
		$sheet->setCellValue('C1', 'Email ID');
		$sheet->setCellValue('D1', 'User Type');
		$sheet->setCellValue('E1', 'Total Arabian Points');
		$sheet->setCellValue('F1', 'Available Arabian Points');
		$sheet->setCellValue('G1', 'Created Date & Time');
		
		$slno = 1;
		$start = 2;
			foreach($data as $d){
				$sheet->setCellValue('A'.$start, $slno);
				$sheet->setCellValue('B'.$start, $d['users_seq_id']);
				$sheet->setCellValue('C'.$start, $d['users_name']);
				$sheet->setCellValue('D'.$start, $d['users_email']);
				$sheet->setCellValue('E'.$start, $d['totalArabianPoints']);
				$sheet->setCellValue('F'.$start, $d['availableArabianPoints']);
				$sheet->setCellValue('G'.$start, date('d-F-Y h:i A',strtotime($d['created_at'])));	
				
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
		$sheet->getStyle('A1:G1')->getFont()->setBold(true);		
		$sheet->getStyle('A1:G1')->applyFromArray($styleThinBlackBorderOutline);
		//Custom width for Individual Columns
		$sheet->getColumnDimension('A')->setWidth(5);
		$sheet->getColumnDimension('B')->setWidth(30);
		$sheet->getColumnDimension('C')->setWidth(30);
		$sheet->getColumnDimension('D')->setWidth(30);
		$sheet->getColumnDimension('E')->setWidth(15);


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
}
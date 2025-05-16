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

class Pos_device_lists extends CI_Controller {

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
	 + + Developed By 	: Dilip	Halder
	 + + Purpose  		: This function used for index
	 + + Date 			: 27 December 2023
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index()
	{	
		$this->admin_model->authCheck('view_data');
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'pos';
		$data['activeSubMenu'] 				= 	'pos_device_lists';
		
		if($this->input->get('searchField') && $this->input->get('searchValue')):


			$searchField 					= 	$this->input->get('searchField');
			$searchValue 					= 	$this->input->get('searchValue');

			$data['searchField'] 			= 	$searchField;
			$data['searchValue'] 			= 	$searchValue;
			 
			 if(is_numeric($searchValue)):

			 	$whereCon['where']		 	= 	array(
			 									 $searchField => (int)$searchValue,
												'$and' => array(
													array('pos_device_id' => array('$ne' => null)),
													array('pos_device_id' => array('$ne' => ""))
											));

			 else:

			 	$whereCon['where']		 	= 	array(
			 									$searchField => $searchValue,
												'$and' => array(
													array('pos_device_id' => array('$ne' => null)),
													array('pos_device_id' => array('$ne' => "")),
													array('users_type' => array('$ne' => "Sales Person")),
													array('users_type' => array('$ne' => "Users"))
											));

			 endif;

		else:

			$whereCon['where']		 		= 	array(
												'$and' => array(
													array('pos_device_id' => array('$ne' => null)),
													array('pos_device_id' => array('$ne' => "")),
													// array('users_type' => array('$ne' => "Sales Person")),
													array('users_type' => array('$ne' => "Users"))
												),
													

											);
		endif;

			
		
		
		$shortField 						= 	array('users_id'=> -1);
		
		$baseUrl 							= 	getCurrentControllerPath('index');
		$this->session->set_userdata('ALLPOSUSERSDATA',currentFullUrl());
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
		$data['selectedCampaign'] 			= 	$this->common_model->getData('single',"uw_selected_campaign");
		// echo '<pre>';print_r($data);die();
		$this->layouts->set_title('All Users | Users | UWINN');
		$this->layouts->admin_view('pos/index',array(),$data);
	}	// END OF FUNCTION

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name : addeditdata
	 + + Developed By  : AFSAR ALI
	 + + Purpose  	   : This function used for Add Edit data
	 + + Date 		   : 29 APRIL 2022
	 + + Updated Date  : 06 December 2023
	 + + Updated By    : Dilip Halder
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function addeditdata($editId='')
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
		
		if($this->input->post('SaveChanges')):
		// echo '<pre>';print_r($_POST);die;
			$error					=	'NO';

			$this->form_validation->set_rules('name', 'Name', 'trim');
			//$this->form_validation->set_rules('email', 'Email', 'trim|is_unique[uw_users.users_email]');
			$this->form_validation->set_rules('country_code', 'Country Code', 'trim');
			$this->form_validation->set_rules('mobile', 'Mobile', 'trim|is_unique[uw_users.users_mobile]');
			$this->form_validation->set_rules('arabianPoints', 'Arabian Points', 'trim');

			if($this->input->post('password')){
				$this->form_validation->set_rules('password', 'Password', 'trim');
				$this->form_validation->set_rules('cpassword', 'Confirm Password', 'trim|matches[password]');
			}

			$this->form_validation->set_message('is_unique', 'The %s is already taken');

			if($this->input->post('user_type') == 'Retailer' || $this->input->post('user_type') == 'Promoter' || $this->input->post('user_type') == 'Promoter' || $this->input->post('user_type') == 'Freelancer Promoter' ){
				$this->form_validation->set_rules('store_name', 'Store Name', 'trim');
				$this->form_validation->set_rules('sales_person', 'Sales Person', 'trim');
			}


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
					$param['created_by']		=	(int)$this->session->userdata('HCAP_ADMIN_ID');
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
						'created_user_id'	=>	(int)$this->session->userdata('HCAP_ADMIN_ID'),
						'status'				=>	'A'
						);
						$this->common_model->addData('uw_loadBalance',$loadbalenceData);
					}

					$this->session->set_flashdata('alert_success',lang('addsuccess'));
				else:

					$categoryId					=	$this->input->post('CurrentDataID');
					$param['update_ip']			=	currentIp();
					$param['update_date']		=	date('Y-m-d h:i');
					$param['updated_by']		=	(int)$this->session->userdata('HCAP_ADMIN_ID');
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

		$this->layouts->set_title('Add/Edit Sales Person | UWINN');
		$this->layouts->admin_view('pos/addeditdata',array(),$data);
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
		
		redirect(correctLink('ALLPOSUSERSDATA',getCurrentControllerPath('index')));
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
		
		redirect(correctLink('ALLPOSUSERSDATA',getCurrentControllerPath('index')));
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
		$this->admin_model->authCheck('view_data');
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
	** Function name 	: assigncapiagn
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for assign campaigns
	** Date 			: 28 December 2023
	************************************************************************/
	public function assigncapiagn($editId)
	{		
		//echo $editId; die();
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'pos';
		$data['activeSubMenu'] 				= 	'pos_device_lists';
		
		if($editId):
		    
			$this->admin_model->authCheck('edit_data');
			$data['EDITDATA']				=	$this->common_model->getDataByParticularField('uw_users','users_id',(int)$editId);
			//echo $editId; die();
			
			//echo '<pre>';print_r($data['EDITDATA']);die;
		else:
			$this->admin_model->authCheck('add_data');
		endif;

		$whereCon['where']  	= array('status' =>'A');
		$whereCon['where_gte']  = array(array('0'=>'draw_date' , '1' => date('Y-m-d' )));
		$tblName 				= "uw_products";
		$shortField   			= array('products_id' => -1);
		$data['productlist'] 	= $this->common_model->getData('multiple',$tblName, $whereCon,$shortField);
		
		

		if($this->input->post('SaveChanges')):
		 
			$error					=	'NO';
			$this->form_validation->set_rules('selected_campaign_list[]', 'selected_campaign_list', 'trim');
			$this->form_validation->set_message('is_unique', 'The %s is already taken');

			if($this->form_validation->run() && $error == 'NO'): 
  
				if($this->input->post('CurrentDataID') !=''):
					$categoryId						 =	$this->input->post('CurrentDataID');
					$param['selected_campaign_list'] =	array_map('intval', $this->input->post('selected_campaign_list'));  
					$param['isSelectedCampaign'] 	 =	"option1";  
					$param['update_ip']				 =	currentIp();
					$param['update_date']		     =	date('Y-m-d h:i');
					$param['updated_by']		     =	(int)$this->session->userdata('HCAP_ADMIN_ID');
					$this->common_model->editData('uw_users',$param,'users_id',(int)$categoryId);
					$this->session->set_flashdata('alert_success',lang('updatesuccess'));
					redirect('pos/pos_device_lists/assigncapiagn/'.$editId);
				endif;
			endif;
		endif;

		$this->layouts->set_title('Add/Edit Sales Person');
		$this->layouts->admin_view('pos/assign_campaign',array(),$data);
	}	// END OF FUNCTION	

	/***********************************************************************
	** Function name 	: addOption
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used to add option
	** Date 			: 28 December 2023
	************************************************************************/
 	public function addOption()
 	{
		$this->admin_model->authCheck('add_data');
 		$userId = $this->input->post('userId');
 		$isSelectedCampaign  =$this->input->post('isSelectedCampaign');

 		$param['isSelectedCampaign']     =	$isSelectedCampaign;  
		$param['update_ip']				 =	currentIp();
		$param['update_date']		     =	date('Y-m-d h:i');
		$param['updated_by']		     =	(int)$this->session->userdata('HCAP_ADMIN_ID');
		$editdata = $this->common_model->editData('uw_users',$param,'users_id',(int)$userId);
		$data['status'] = 'success';
		$data['responce'] = $editdata;
		echo json_encode($data);
		die();
 	}
 
}

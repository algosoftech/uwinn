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

class Online_draw extends CI_Controller {

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
	 + + Developed By 	:Dilip Halder
	 + + Purpose  		: This function used for index
	 + + Date 			: 20 December 2023
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index()
	{	
		$this->admin_model->authCheck();
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'sub_winners';
		$data['activeSubMenu'] 				= 	'online_draw';
		
		if($this->input->get('searchField') && $this->input->get('searchValue')):
			if($this->input->get('searchField') == 'collection_status'):
				$sField							=	$this->input->get('searchField');
				$sValue							=	$this->input->get('searchValue');
				if(strtoupper($sValue) == 'ZERO'):
					$sValue = 0;
				endif;
				$whereCon['where']				=	array('soft_delete'=>array('$ne'=>1), 'collection_status' => (int)$sValue);
				$data['searchField'] 			= 	$sField;
				$data['searchValue'] 			= 	$sValue;

			elseif($this->input->get('searchField') =='draw_date'):
				$searchValue 					= date('Y-m-d',strtotime($this->input->get('searchValue')));
				$whereCon['where']				= array('draw_date'=> $searchValue);
			else:
				$sField							= $this->input->get('searchField');
				$sValue							= $this->input->get('searchValue');
				$whereCon['like']			 	= array('0'=>trim($sField),'1'=>trim($sValue));
				$data['searchField'] 			= $sField;
				$data['searchValue'] 			= $sValue;
			endif;
		else:
			
		$whereCon['where']			= array(
									  'soft_delete'=>array('$eq'=>1)
								    );

		$whereCon['where_or'] 		= array(
									  'redeemeByArabianPoint' => 'Y',
									  'redeembycash' => 'Y'
									); 
		endif;
		
		$shortField 						= 	array('_id'=> -1);
		$baseUrl 							= 	getCurrentControllerPath('index');
		$this->session->set_userdata('ALLDRAWWINNERDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 	'wn_draw_winners';
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
		$this->layouts->set_title(' Daily Winners | Dealz Arabia');
		$this->layouts->admin_view('subwinners/draw_winners/index',array(),$data);
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name 	: changestatus
	** Developed By 	: Afsar Ali
	** Purpose  		: This function used for change status
	** Date 			: 08 JULY 2023
	** Updatead By 		: Dilip halder
	** Date 			; 23 December 2023
	************************************************************************/
	function changestatus($changeStatusId='',$statusType='')
	{   
		$this->admin_model->authCheck('edit_data');
		//Checking total order count.
		$tblName1 			 = 'wn_draw_winners';
		$whereCon1['where']  = array('order_id' =>  $changeStatusId);

		if($statusType == "A"):
			$param['redeem_status']			=	'redeemed';
			$param['redeemeByArabianPoint'] =	'Y';
			$param['setteld_status']		=	(int)1;

		elseif($statusType == "R"):
			$param['redeem_status']			=	'pending';
			$param['redeemeByArabianPoint'] =	'Y';
			$param['setteld_status']		=	(int)0;

		endif;
		$this->common_model->editData('wn_draw_winners',$param,'order_id' , $changeStatusId);

		$DrawData   		   = $this->common_model->getData('single',$tblName1,$whereCon1,$shortField2);
		$whereUserCon['where'] = array('users_id' =>  (int)$DrawData['users_id']);
		$UserDetails    	   = $this->common_model->getData('single','da_users',$whereUserCon);

		if($statusType == "A"):
			$totalArabianPoints 	=  $UserDetails['totalArabianPoints']+ $DrawData['amount'];
			$availableArabianPoints =  $UserDetails['totalArabianPoints']+ $DrawData['amount'];
		elseif($statusType == "R"):
			$totalArabianPoints 	=  $UserDetails['totalArabianPoints']- $DrawData['amount'];
			$availableArabianPoints =  $UserDetails['totalArabianPoints']- $DrawData['amount'];
		endif;

		$param 	= array('totalArabianPoints' => (float)$totalArabianPoints , 'availableArabianPoints' => (float)$availableArabianPoints) ; 
		$this->common_model->editData('da_users',$param,'users_id',(int)$DrawData['users_id']);
		

		$this->sms_model->creditAp($UserDetails,$DrawData,$statusType);
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		
		redirect(correctLink('ALLDRAWWINNERDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name 	: userdetails
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for change status
	** Date 			: 23 December 2023
	************************************************************************/
	public function userdetails($orderid='',$userId='')
	{	
		$this->admin_model->authCheck();
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'sub_winners';
		$data['activeSubMenu'] 				= 	'draw_winners';
		//Checking User Details
		$tblName 			 = 'da_users';
		$whereCon['where']   = array('users_id' => (int)$userId, 'status' => 'A');
		$shortField 		 = array('users_id' => -1); 
		$data['userdetails'] = $this->common_model->getData('single',$tblName,$whereCon,$shortField);
		//Checking total order count.
		$tblName1 			 = 'da_orders';
		$whereCon1['where']  = array('user_id' => (int)$userId,'order_status' => 'Success' ,'status' => array('$ne' => 'CL'));
		$shortField1 		 = array('user_id' => -1); 
		$data['order_count'] 		 = $this->common_model->getData('count',$tblName1,$whereCon1,$shortField1);
		//Checking total order count.
		$tblName2 			 = 'da_ticket_orders';
		$whereCon2['where']  = array('user_id' => (int)$userId,'order_status' => 'Success' ,'status' => array('$ne' => 'CL'));
		$shortField2 		 = array('user_id' => -1); 
		$data['quick_order_count'] 		 = $this->common_model->getData('count',$tblName2,$whereCon2,$shortField2);
		
		$baseUrl 			 = 	getCurrentControllerPath('index');
		$this->session->set_userdata('ALLDRAWWINNERDATA',currentFullUrl());

		// echo "<pre>"; print_r($data); die();

		$this->layouts->set_title(' Draw Winners - User Details | Dealz Arabia');
		$this->layouts->admin_view('subwinners/draw_winners/addeditdata',array(),$data);
	}	// END OF FUNCTION

}
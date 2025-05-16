<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Test extends CI_Controller {
	
	var $postdata;
	var $user_agent;
	var $request_url; 
	var $method_name;
	
	public function  __construct() 	
	{ 
		parent:: __construct();
		error_reporting(E_ALL ^ E_NOTICE);  
		$this->load->model(array('sms_model','emailsendgrid_model','notification_model','emailtemplate_model'));
		$this->lang->load('statictext', 'api');
		$this->load->helper('apidata');
		$this->load->model(array('geneal_model','common_model'));

		$this->user_agent 		= 	$_SERVER['HTTP_USER_AGENT'];
		$this->request_url 		= 	$_SERVER['REDIRECT_URL'];
		$this->method_name 		= 	$_SERVER['REDIRECT_QUERY_STRING'];

		$this->load->library('generatelogs',array('type'=>'common'));
	} 

	/* * *********************************************************************
	 * * Function name : SummaryReportSearch
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to fetch order history.
	 * * Date 		   : 27 October 2023
	 * * **********************************************************************/
	
	public function SummaryReportSearch()
	{

		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();

		if(requestAuthenticate(APIKEY,'GET')):
			
			if( $this->input->get('users_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			else:
			  	$usersId = $this->input->get('users_id');

			  	$tbl 			= 'uw_lotto_orders';
			 	$product_title  = $this->input->get('product_title');
			  	$start_date     = date('Y-m-d 21:31' ,strtotime($this->input->get('start_date')) );
		      	$end_date 		= date('Y-m-d 21:30' ,strtotime($this->input->get('end_date')));

			 	if(!empty($product_title)):
			 		$where['product_title'] = $product_title;
			 	endif;

			 	if($this->input->get('start_date') && $this->input->get('end_date') ):
			 		$where['created_at'] = array('$gte' => $start_date ,'$lte' => $end_date );
			 	elseif($this->input->get('start_date')):
			 		$where['created_at'] = array('$gte' => $start_date);
			 	elseif($this->input->get('end_date')):
			 		$where['created_at'] = array('$lte' => $end_date);
			 	endif;

				$shortField 			=	['coupon_id' => -1];
				$wcon1['where']         =  array('users_id' => (int)$usersId ,'status' => array('$ne'=> 'CL'));
				$userResult				=  $this->geneal_model->getData2('single', 'uw_users', $wcon1);

				$user_oid 					=  $userResult['_id']->{'$id'};
				// CommissionAmount 
				$commissionAmount 			=  $this->common_model->commissionList($user_oid ,$where);
				$totalcancelOrder			=  $this->common_model->cancelOraderList($user_oid,$where);
				
				

				$result['totalArabianPoints'] 	  = $userResult['totalArabianPoints'];
				$result['availableArabianPoints'] = $userResult['availableArabianPoints'];
				$result['redeemed_points']		  = $userResult['redeemed_points']?$userResult['redeemed_points']:0;
				// $result['total_profits']		  = $commissionAmount?$commissionAmount:0;
				$result['total_profits']		  = 0;
				$result['cancelled_orders']		  = $totalcancelOrder?$totalcancelOrder:0;

			 	$wcon1['where']  = array();
				$whereCon['user_id']         =  (int)$usersId;
				$whereCon['status']          =  array('$ne'=> 'CL');
				if($where['created_at']):
					$whereCon['created_at']  =  $where['created_at'];
				endif;
				if($where['created_at']):
					$whereCon['created_at']  =  $where['created_at'];
				endif;
				if(!empty($product_title)):
			 		$whereCon['product_title'] = $product_title;
			 	endif;

				$tbl 				= 'uw_lotto_orders';
				$wcon1['where']  	= $whereCon;
				$result['products']	=	$this->geneal_model->GetQuickOrderTotalCount('multiple', $tbl, $wcon1,$shortField);

				if($this->input->get('product_title') || $this->input->get('start_date')  || $this->input->get('end_date') ):
					$result['totalArabianPoints'] 	  =	$result['products'][0]['availableArabianPoints'];
					$result['availableArabianPoints'] =	$result['products'][0]['end_balance'];
				endif;

				if(!empty($result)):
					$results = $result;
					echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);	
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('DATA_NOT_FOUND'),$results);	
				endif;

			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : reconcileRequest
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to send reuest to admin to cancel request.
	 * * Date 		   : 09 February 2024
	 * * **********************************************************************/
	public function reconcileRequest()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	

		$USerData 		     = $this->common_model->UserAuthCheck('POST');
		if($USerData):

				$users_id 			=  $this->input->post('users_id');
				$tickect_id 		=  $this->input->post('tickect_id');
				$remark 			=  $this->input->post('remark');


				echo "<pre>";
				print_r($_POST);
				die();




				if(empty($users_id)):
					echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
				elseif(empty($tickect_id)):
					echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_TICKET'),$result);die();
				elseif(empty($remark)):
					echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_REMARK'),$result);die();
				else:
				 
				endif;
			 	// echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);	
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	 
	
}
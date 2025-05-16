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
	 * * Function name : checkSingleCampaignFreezing
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to check Single Campaign Freezing.
	 * * Date 		   : 28 February 2024
	 * * **********************************************************************/
	public function checkSingleCampaignFreezing()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 			= 	array();


		if(requestAuthenticate(APIKEY,'POST')):
				$usersId    = $this->input->get('users_id');
				$productId  = $this->input->post('product_id');
				
				$tblName  		   = 'uw_users';
				$whereCon['where'] = array('users_id' => (int)$usersId );
				$UserData          = $this->common_model->getData('single',$tblName,$whereCon);
				
				if(empty($UserData)):
					echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_USER_ID'),$results);	
				elseif(!empty($UserData) && $UserData['status'] != "A" ):
					echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$results);
				elseif(!empty($UserData) && $UserData['status'] == "A"):
					
				 $tblName  		    = 'uw_products';
				 $whereCon['where'] = array('products_id' => (int)$productId );
				 $productData       = $this->common_model->getData('single',$tblName,$whereCon);
					if(empty($productData)):
						echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_PRODUCT_ID'),$results);	
					elseif(!empty($productData) && $productData['status'] != "A" ):
						echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_PRODUCT'),$results);
					elseif(!empty($productData) && $productData['status'] == "A"):
						// Api Data Responce 
						$result['title'] 					    = $productData['title'];
						$result['status'] 					    = $productData['status'];
						$result['draw_date'] 					= $productData['draw_date'];
						$result['draw_time'] 					= $productData['draw_time'];
						$result['campaign_auto_freezing_mode']  = $productData['campaign_auto_freezing_mode'];
						$result['campaign_freezing_start_time'] = $productData['campaign_freezing_start_time'];
						$result['campaign_freezing_end_time']   = $productData['campaign_freezing_end_time'];
						$results = $result;
						echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);	
					endif;
				endif;

			// endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}
	

	 
	
}
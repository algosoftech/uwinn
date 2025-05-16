<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends CI_Controller {
	
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
	 * * Function name  : productList
	 * * Developed By 	: Afsar Ali
	 * * Purpose  		: This function used for get product list
	 * * Date 			: 27 JUNE 2024
	 * * **********************************************************************/
	public function productList()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'GET')):
			$result['products']    		=   array();
			$bannerWhereCon['where'] 	=	array( 'draw_date' => array('$gte' => date('Y-m-d')) );
			$result['products'] 			=	$this->geneal_model->getProductWithPrizeDetails($bannerWhereCon);
			echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}	
	/* * *********************************************************************
	 * * Function name  : drawResult
	 * * Developed By 	: Afsar Ali
	 * * Purpose  		: This function used for get winner results
	 * * Date 			: 27 JUNE 2024
	 * * **********************************************************************/
	public function drawResult()
	{	
		try {
			$apiHeaderData 		=	getApiHeaderData();
			$this->generatelogs->putLog('APP',logOutPut($_POST));
			$result 							= 	array();	
			if(requestAuthenticate(APIKEY,'GET')):
				$page 						= 	$this->input->get('page');
	
				$limit 						=	10;
				$skip						=	$page == 1 ? 0 : $page * 10;
	
	
				$result['draw_result'] 		=   array();
				$latestWhereCon['where'] 	=	array('soft_delete' => 0);
				$latestOrder 				=	array('_id' => -1);
				$draw_result 				=	$this->geneal_model->getData2('multiple','uw_uwin_winner', $latestWhereCon,$latestOrder,$skip,$limit);
				$count 						=	$this->geneal_model->getData2('count','uw_uwin_winner', $latestWhereCon,$latestOrder);
				if($count === 0){ 
					$total_page = 1;
				} else{
					$total_page = (int)$count / $limit;
					// if(is_float($total_page)){ $total_page = (int)$total_page + 1; }
				}
				$result['total_page'] = (int)$total_page;
				$result['current_page'] = (int)$page;
				$result['draw_result'] = $draw_result;
				echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
			else:
				echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
			endif;
		} catch (\Throwable $th) {
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),array());
		}
	}
	/* * *********************************************************************
	 * * Function name  : winnerlist
	 * * Developed By 	: Afsar Ali
	 * * Purpose  		: This function used for get winner list
	 * * Date 			: 03 JULY 2024
	 * * **********************************************************************/
	public function winnerlist()
	{	
		try {
			$apiHeaderData 		=	getApiHeaderData();
			$this->generatelogs->putLog('APP',logOutPut($_POST));
			$result 							= 	array();	
			if(requestAuthenticate(APIKEY,'GET')):
				$page 						= 	$this->input->get('page');
	
				$limit 						=	10;
				$skip						=	$page == 1 ? 0 : $page * 10;
	
	
				$result['draw_result'] 		=   array();
				$latestWhereCon['where'] 	=	array();
				$latestOrder 				=	array('_id' => -1);
				$draw_result 				=	$this->geneal_model->getData2('multiple','uw_winners_gallery', $latestWhereCon,$latestOrder,$skip,$limit);
				$count 						=	$this->geneal_model->getData2('count','uw_winners_gallery', $latestWhereCon,$latestOrder);
				if($count === 0){ 
					$total_page = 1;
				} else{
					$total_page = (int)$count / $limit;
					// if(is_float($total_page)){ $total_page = (int)$total_page + 1; }
				}
				$result['total_page'] = (int)$total_page == 0?1:$total_page;
				$result['current_page'] = (int)$page;
				$result['draw_result'] = $draw_result?$draw_result:[];
				echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
			else:
				echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
			endif;
		} catch (\Throwable $th) {
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),array());
		}
	}
}
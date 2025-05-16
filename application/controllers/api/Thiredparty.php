<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Thiredparty extends CI_Controller {
	
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
	 * * Function name : drawDataList
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used get draw data List
	 * * Date 		   : 14 October 2024
	 * * **********************************************************************/
	public function drawDataList()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_GET));
		$result 			= 	array();

		if(requestAuthenticate(APIKEY,'POST')):
			
			$reportPin  	  = $this->input->post('reportPin');
			$draw_start_date  = $this->input->post('draw_start_date');
			$draw_end_date    = $this->input->post('draw_end_date');

			if(empty($reportPin)):
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_REPORT_PIN'),$result);die();

			elseif(empty($draw_start_date)  && !empty($draw_end_date)):
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_START_DRAW_DATE'),$result);die();

			elseif(!empty($draw_start_date)  && empty($draw_end_date) ):
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_END_DRAW_DATE'),$result);die();
			else: 

				$tblName 	 = 'uw_general_data';
				$Fields 	 = array('drawdata_pin','draw_time_start','draw_time_end');
				$GeneralData = $this->common_model->getParticularFieldByMultipleCondition($Fields , $tblName , '');
				// echo "<pre>";print_r($GeneralData);die();
				if($GeneralData['drawdata_pin']  === $reportPin ):
					
					$results  = $this->geneal_model->getDrawData($GeneralData);
					if($results):
						echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);	
					else:
						echo outPut(0,lang('SUCCESS_CODE'),lang('DATA_NOT_FOUND'),$results);	
					endif;
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('WRONG_REPORT_PIN'),$result);die();
				endif;

			endif;
			 	
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}
	
	/* * *********************************************************************
	 * * Function name : hourlyReport
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used get hourlyReport
	 * * Date 		   : 14 October 2024
	 * * **********************************************************************/
	public function hourlyReport()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_GET));
		$result 			= 	array();

		if(requestAuthenticate(APIKEY,'POST')):
			$reportPin  	  = $this->input->post('reportPin');
			if(empty($reportPin)):
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_REPORT_PIN'),$result);die();
			else: 
				$tblName 	 = 'uw_general_data';
				$Fields 	 = array('drawdata_pin');
				$GeneralData = $this->common_model->getParticularFieldByMultipleCondition($Fields , $tblName);
				// echo "<pre>";print_r($GeneralData);die();

				if($GeneralData['drawdata_pin']  === $reportPin ):

					
					$results  = $this->geneal_model->gethourlyReport($GeneralData);
					if($results):
						echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);	
					else:
						echo outPut(0,lang('SUCCESS_CODE'),lang('DATA_NOT_FOUND'),$results);	
					endif;
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('WRONG_REPORT_PIN'),$result);die();
				endif;
			endif;
			 	
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pdf extends CI_Controller {
	public function  __construct() 
	{ 
		parent:: __construct();
		error_reporting(0);
		$this->load->model(array('geneal_model','common_model','emailsendgrid_model','sms_model'));
		$this->lang->load('statictext','front');
	} 
	
	/* * *********************************************************************
	 * * Function name 	: download_uwin_invoice
	 * * Developed By 	: Dilip
	 * * Purpose  		: This function used for download invoice
	 * * Date 			: 09 February 2024
	 * * **********************************************************************/
	public function download_uwin_invoice($oid ='')
	{
		$this->load->library('Mpdf');
		
		$tblName 				= 'uw_lotto_orders';
		$shortField 			= array('_id'=> -1 );
		$whereCon['where']		= array('order_id'=>$oid);
		$orderData = $this->geneal_model->getOrderData($whereCon);
		$data['orderData'] 		= $orderData[0];
		// echo "<pre>";print_r($data);die();

		// if($orderData[0]['raffle_mode'] == 'Y'){
		// 	$this->load->view('web_api/raffle_order_template', $data);
		// }else{
		// 	$this->load->view('web_api/pos_order_template', $data);
		// }

		$is_hourly_game = 'N';
		if(empty($orderData)):
			$is_hourly_game = 'Y';
			$whereCon['where']		= array('order_id'=>$oid);
			$orderData = $this->common_model->getHourlyGameOrderHistory($whereCon,$shortField,'','');
			if(!empty($orderData)):
				$data['orderData'] = $orderData[0];
			endif;
		endif;
		
		// echo "<pre>";print_r($data);die();
		
		if($is_hourly_game == 'Y'):
			$this->load->view('web_api/hourly_game_order_template', $data);
		elseif($orderData[0]['raffle_mode'] == 'Y'):
			$this->load->view('web_api/raffle_order_template', $data);
		else:
			$this->load->view('web_api/pos_order_template', $data);
		endif;
		// return;
	}

}	

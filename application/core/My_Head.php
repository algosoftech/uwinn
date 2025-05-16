<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class My_Head extends CI_Controller {
public function  __construct() 
{ 
	parent:: __construct();

	 $Url = $this->uri->segment(1);
	 //$oid = $this->uri->segment(2);
	if($this->session->userdata('CURRENT_ORDER_ID')){
		$oid = $this->session->userdata('CURRENT_ORDER_ID');
	}else{
		$whereGetOrderID['where']		=	[ 'user_id' => (int)$this->session->userdata('DZL_USERID') ];
		$currentOrderData 				=	$this->geneal_model->getData2('single', 'da_orders', $whereGetOrderID);
		if(!empty($currentOrderData)):
			$oid = $currentOrderData['order_id'];
		endif;
	}

	 if($Url == 'order-success'):

		// getting user order details
		$wcon['where']			=	array( 'order_id' => $oid );
		$orderData 				=	$this->geneal_model->getData2('single', 'da_orders', $wcon);
		
		if(empty($orderData)):
			redirect('login');
		endif;
		
		// getting user Details
		$where			=	array( 'users_id' => (int)$orderData['user_id']);
		$tblName 		=	'da_users';
		$userDetails 	=	$this->geneal_model->getOnlyOneData($tblName, $where);

		if(!empty($userDetails)):

			if($userDetails['status'] == "A" ):
					if(!empty($userDetails)): $data = $userDetails; endif;

				    $this->session->set_userdata('DZL_USERID', $data['users_id']);
					$this->session->set_userdata('DZL_USERNAME', $data['users_name']);
					$this->session->set_userdata('DZL_USEREMAIL', $data['users_email']);
					//$this->session->set_userdata('DZL_SEQID', $data['users_sequence_id']);
					$this->session->set_userdata('DZL_USERMOBILE', $data['users_mobile']);
					$this->session->set_userdata('DZL_TOTALPOINTS', $data['totalArabianPoints']);
					$this->session->set_userdata('DZL_AVLPOINTS', $data['availableArabianPoints']);
					$this->session->set_userdata('DZL_USERSTYPE', $data['users_type']);
					if(!empty($data['referral_code'])){
						$this->session->set_userdata('DZL_USERS_REFERRAL_CODE', $data['referral_code']);
					}
					$this->session->set_userdata('DZL_USERS_COUNTRY_CODE', $data['country_code']);
					
					$expIN = date('Y-m-d', strtotime($data['created_at']. ' +12 months'));
					$today = strtotime(date('Y-m-d'));
					$dat = strtotime($expIN) - $today;
					$Tdate =  round($dat / (60 * 60 * 24));


					$this->session->set_userdata('DZL_EXPIRINGIN', $Tdate);

			endif;
		endif;

		$this->session->set_userdata('DZL_USERID',$orderData['user_id']);

	else:
		if( empty($this->session->userdata('Quick_buy')) && empty($this->session->userdata('DZL_USERID')) && $this->uri->segment(2) != 'download-invoice' && $Url != 'lotto'):
			redirect('login');
		endif;
	endif;
} 

}
?>
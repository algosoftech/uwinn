<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once (APPPATH . 'third_party/stripe-php-master/init.php');

class Stripe extends CI_Controller {
 
    
    public function  __construct()  
    { 
        parent:: __construct();
        error_reporting(E_ALL ^ E_NOTICE);  
        $this->load->model(array('sms_model','emailsendgrid_model','notification_model','emailtemplate_model'));
        $this->lang->load('statictext', 'api');
        $this->load->helper('apidata');
        $this->load->model(array('geneal_model','common_model','stripe_model'));
    } 

    /* * *********************************************************************
	 * * Function name  : initilizeOrder
	 * * Developed By 	: Dilip Halder
	 * * Purpose  		: This function used for get winner results
	 * * Date 			: 16 April 2024
	 * * **********************************************************************/
	 public function initilizeOrder()
	 {	
		$apiHeaderData 		=	getApiHeaderData();
		$result 			= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):

			try {
				$userID   = $this->input->post('user_id');
				$currency = $this->input->post('currency');
				$amount   = $this->input->post('amount');

				if(empty($userID)) :
					throw new Exception(lang('USER_ID_EMPTY'), 1);
				elseif(empty($currency)) :
					throw new Exception(lang('EMPTY_CURRENCY'), 1);
				elseif(empty($amount)):
					throw new Exception(lang('EMPTY_Amount'), 1);
				else:

					$tblName1     = 'uw_general_data';
					$Fieldslist   = array('recharge_topup_btn','recharge_topup_end_time','recharge_topup_start_time','recharge_topup_msg');
					$generalData  =  $this->common_model->getParticularFieldByMultipleCondition($Fieldslist ,$tblName1, $where);
					
					if($generalData):
						$currentTime  = strtotime(date('H:i'));
						$starTime     = strtotime($generalData['recharge_topup_start_time']);
						$endTime      = strtotime($generalData['recharge_topup_end_time']);
					endif;

					if(!empty($generalData) && $generalData['recharge_topup_btn'] === 'Y' && ($currentTime >= $startTime && $currentTime <= $endTime)):

						$where['where']['users_id'] = (int)$userID;
						$Fieldslist   = array('users_name','last_name','users_type','country_code','users_mobile', 'users_email','status','users_id','stripe_customer_id');
						$tblName      =  'uw_users';
						$userDetails  =  $this->common_model->getParticularFieldByMultipleCondition($Fieldslist ,$tblName, $where);
						// echo "<pre>";print_r($userDetails);die();

						if(!empty($userDetails) && $userDetails['status'] == 'A' && $userDetails['users_type'] == 'Users' ):

							// Creating customer in stripe..
							$CustomerData = $this->stripe_model->createCustomers($userDetails);
							$IDS  					= $this->transactionIDS();
							$intentData['currency'] = $currency;
							$intentData['amount']   = $amount;
							$intentData['mobile']   = $userDetails['users_mobile'];
							$intentData['tranasactionID'] = $IDS['tranasactionID'];
							$intentData['customer']       = $userDetails['stripe_customer_id']?$userDetails['stripe_customer_id']:$CustomerData['id'] ;
							$intentData['idempotencyKEY'] = $IDS['idempotencyKEY'];
							$intentData['metadata']['first_name'] = $userDetails['users_name'];
							$intentData['metadata']['last_name']  = $userDetails['last_name'];
							$intentData['metadata']['mobile'] 	  = $userDetails['users_mobile'];
							$result = $this->stripe_model->paymentIntent($intentData);

							/* Adding payment details in initilize payment gateway api*/ 
					        $tableName                = 'uw_transactions';
					        $Param['tranasactionID']  = $intentData['tranasactionID'];
					        $Param['users_id']        = (int)$userID;
							$Param['currency']        = $intentData['currency'];
							$Param['amount']          = (int)$intentData['amount'];
					        $Param['first_name']        = $userDetails['users_name'];
							$Param['last_name']          = $userDetails['last_name'];
					        $Param['mobile']          = (int)$intentData['mobile'];
							$Param['created_at']          = date('Y-m-d H:i:s');
					        $Param['status']          = 'pending';
					      	$this->common_model->addData($tableName,$Param);

							echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
						elseif(!empty($userDetails) && $userDetails['status'] == 'A' && $userDetails['users_type'] != 'Users' ):
							throw new Exception(lang('STRIPE_AVAILABLE_ONLY_IN_APP'), 1);
						elseif(empty($userDetails)):
							throw new Exception(lang('INVALID_LOGIN'), 1);
						else:
							throw new Exception(lang('FORBIDDEN_MSG'), 1);
						endif;

					else:
						$errorMsg = $generalData['recharge_topup_msg']? $generalData['recharge_topup_msg'] :lang('RECHARGE_TOPUP_MSG');
						throw new Exception($errorMsg, 1);
					endif;
					
				endif;
				
			} catch (Exception $e) {
				echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);
				
			}
			
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	 }
	
	/* * *********************************************************************
	 * * Function name  : stripeDetails
	 * * Developed By 	: Dilip Halder
	 * * Purpose  		: This function used for get winner results
	 * * Date 			: 16 April 2024
	 * * **********************************************************************/
	public function stripeDetails()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$result 			= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):
			
			$userID         = $this->input->post('user_id');
			if(empty($userID)) :
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			else:
				 
				$where['where']['users_id'] = (int)$userID;
				$Fieldslist   = array('users_name','last_name','users_type','country_code','users_mobile', 'users_email','status','users_id');
				$tblName      =  'uw_users';
				$userDetails  =  $this->common_model->getParticularFieldByMultipleCondition($Fieldslist ,$tblName, $where);
				// echo "<pre>";print_r($userDetails);die();

				if(!empty($userDetails) && $userDetails['status'] == 'A' && $userDetails['users_type'] == 'Users'):
					$params = array(
				        "payment_mode"      =>  STRIPE_PAYMENT_MODE,
				        "private_live_key"  =>  STRIPE_LIVE_SK,
				        "public_live_key"   =>  STRIPE_LIVE_PK,
				        "private_test_key"  =>  STRIPE_TEST_SK,
				        "public_test_key"   =>  STRIPE_TEST_PK
				    );

					$publishable_key     = $params['payment_mode'] === "test"  ? $params['public_test_key'] : $params['public_live_key'];
				    $merchant_identifier = MERCHENT_IDENTIFIER;

					$result['publishable_key']       = $publishable_key;
					$result['merchant_identifier']   = $merchant_identifier;

					echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
				elseif(!empty($userDetails) && $userDetails['status'] == 'A' && $userDetails['users_type'] != 'Users' ):
					echo outPut(1,lang('SUCCESS_CODE'),lang('STRIPE_AVAILABLE_ONLY_IN_APP'),$result);

				elseif(empty($userDetails)):
					echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_LOGIN'),$result);
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('FORBIDDEN_MSG'),$result);
				endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	 /* * *********************************************************************
	 * * Function name  : transactionIDS
	 * * Developed By   : Dilip Halder
	 * * Purpose        : This function is used for transactionIDS 
	 * * Date           : 16 April 2025
	 * * **********************************************************************/
	function transactionIDS($data = null) {
	 	$tranasactionID  = 'tranx_'.uniqid();
		$data            = $tranasactionID ?? random_bytes(16);
	    assert(strlen($data) == 16);
	    // Set version to 0100
	    $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
	    // Set bits 6-7 to 10
	    $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

	    // Output the 36 character UUID.
     	$result['tranasactionID'] = $tranasactionID;
     	$result['idempotencyKEY'] = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
	     return $result;
	}

	public function test()
	{
		$params = array(
	        "payment_mode"      =>  STRIPE_PAYMENT_MODE,
	        "private_live_key"  =>  STRIPE_LIVE_SK,
	        "public_live_key"   =>  STRIPE_LIVE_PK,
	        "private_test_key"  =>  STRIPE_TEST_SK,
	        "public_test_key"   =>  STRIPE_TEST_PK
	    );
		$privateKey = $params['payment_mode'] === "test"  ? $params['private_test_key'] : $params['private_live_key'];


		$nonce['customer'] =  'cus_S8hxUco9zf3BzT';
		$requestData['stripe_version'] = '2022-08-01';
		// Set your secret key. Remember to switch to your live secret key in production.
		// See your keys here: https://dashboard.stripe.com/apikeys
		$stripe = new \Stripe\StripeClient($privateKey);
	    $ephemeralKey = $stripe->ephemeralKeys->create($nonce,$requestData);
		echo "<pre>";
		print_r($ephemeralKey);
		die();
	}


	/* * *********************************************************************
	 * * Function name  : paymentsuccess
	 * * Developed By   : Dilip Halder
	 * * Purpose        : This function is used for paymentsuccess 
	 * * Date           : 16 April 2025
	 * * **********************************************************************/
	public function paymentsuccess($value='')
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$result 			= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):
			
			$userID         = $this->input->post('user_id');
			$transactionID  = $this->input->post('transaction_id');
			$status         = $this->input->post('status');

			if(empty($userID)) :
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			elseif(empty($transactionID)) :
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_TRANSACTION_ID'),$result);
			elseif(empty($status)) :
				echo outPut(0,lang('SUCCESS_CODE'),lang('STATUS_EMPTY'),$result);
			else:
				 
				$where['where']['users_id'] = (int)$userID;
				$Fieldslist   = array('users_name','last_name','users_type','country_code','users_mobile', 'users_email','status','users_id','availableArabianPoints','totalArabianPoints');
				$tblName1      =  'uw_users';
				$userDetails  =  $this->common_model->getParticularFieldByMultipleCondition($Fieldslist ,$tblName1, $where);
				// echo "<pre>";print_r($userDetails);die();

				if(!empty($userDetails) && $userDetails['status'] == 'A' && $userDetails['users_type'] == 'Users'):
					
					$whereCon['where']['users_id']       =  (int)$userID;
					$whereCon['where']['tranasactionID'] = $transactionID;

					$Fieldslist   = array('tranasactionID','users_id','status','amount');
					$tblName      =  'uw_transactions';
					$orderDetails =  $this->common_model->getParticularFieldByMultipleCondition($Fieldslist ,$tblName, $whereCon);
					// echo "<pre>";print_r($orderDetails);die();
					if(!empty($orderDetails) && $orderDetails['status'] == 'pending'):

						// Added balance in account..
						$UserParam['totalArabianPoints']     =  (float)$userDetails['totalArabianPoints']+$orderDetails['amount'];
						$UserParam['availableArabianPoints'] =  (float)$userDetails['availableArabianPoints']+$orderDetails['amount'];
				      	$this->common_model->editDataByMultipleCondition($tblName1,$UserParam ,$where['where']);

						// Updating payment responce.
				        $updateParam['status']   = $status;
				      	$this->common_model->editDataByMultipleCondition($tblName,$updateParam ,$whereCon['where']);

				      	// Adding LoadBalance for status.
				      	$loadBalance['load_balance_id'] = (int)$this->geneal_model->getNextSequence('uw_loadBalance');
                        $loadBalance['user_oid']        = new MongoDB\BSON\ObjectId($userDetails['_id']['$id']);
                        $loadBalance['request_id']      = $orderDetails['tranasactionID'];
                        $loadBalance['request_oid']     = new MongoDB\BSON\ObjectId($orderDetails['_id']->{'$id'});
                        $loadBalance['user_id_deb']     = (int)$userID;
                        $loadBalance['user_id_cred']    = (int)0;
                        $loadBalance["availableArabianPoints"] = (float)$userDetails['availableArabianPoints'];
                        $loadBalance["end_balance"]            = (float)$userDetails['availableArabianPoints']+$orderDetails['amount'];
                        $loadBalance['record_type']     = 'Credit';
                        $loadBalance['narration']       = 'Online recharge';
                        $loadBalance['remarks']         = 'Online rechagred '.$orderDetails['amount'].' aed.';
                        $loadBalance['upoints']         = (float)$orderDetails['amount'];
                        $loadBalance['creation_ip']     = $this->input->ip_address();;
                        $loadBalance['created_at']      = date('Y-m-d H:i');
                        $loadBalance['created_by']      = (int)$userID;
                        $loadBalance['status']          = 'A';
                        $this->geneal_model->addData('uw_loadBalance', $loadBalance);

						echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESSFUL_TRANSACTION'),$result);
				      	// echo "<pre>";print_r($createCustomer);die();
					else:
						echo outPut(0,lang('SUCCESS_CODE'),lang('DATA_NOT_FOUND'),$result);
					endif;
				elseif(!empty($userDetails) && $userDetails['status'] == 'A' && $userDetails['users_type'] != 'Users' ):
					echo outPut(1,lang('SUCCESS_CODE'),lang('STRIPE_AVAILABLE_ONLY_IN_APP'),$result);
				elseif(empty($userDetails)):
					echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_LOGIN'),$result);
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('FORBIDDEN_MSG'),$result);
				endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

}
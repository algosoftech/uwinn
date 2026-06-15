<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Btctest extends CI_Controller {
    
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

        $this->user_agent       =   $_SERVER['HTTP_USER_AGENT'];
        $this->request_url      =   $_SERVER['REDIRECT_URL'];
        $this->method_name      =   $_SERVER['REDIRECT_QUERY_STRING'];

        $this->load->library('generatelogs',array('type'=>'common'));
	    $this->load->library('mongodb_client');

    } 


    public function moveToWallet()
	{

	    $result = [];

	    if (!requestAuthenticate(APIKEY, 'POST')) {
	        echo outPut(0, lang('FORBIDDEN_CODE'), lang('FORBIDDEN_MSG'), $result);
	        return;
	    }

	    try {

		        /*===============================
		           POST DATA
		        =============================== */

		        $userID        = (int)$this->input->post('users_id');
		        $orderID       = trim($this->input->post('order_id'));
		        $redeemStatus  = $this->input->post('redeem_status');
		        $redeemByMode  = $this->input->post('redeem_by_mode');

		        if (empty($userID)) {
		            echo outPut(0, lang('SUCCESS_CODE'), lang('USER_ID_EMPTY'), $result);
		            return;
		        }elseif (empty($orderID)) {
		            echo outPut(0, lang('SUCCESS_CODE'), lang('ORDER_ID_EMPTY'), $result);
		            return;
		        }else{

	        	 	/*===============================
			           USER VALIDATION
			        =============================== */
			        $this->common_model->userValidate($userID, 'app');

			        /* ===============================
			           FETCH WINNER DATA
			        =============================== */
			        $winnerWhere['where']['order_id'] = $orderID;
			        $winnerWhere['where']['status']   = 1;
			        $WinnerList  = $this->common_model->getData( 'multiple', 'uw_uwin_winner', $winnerWhere );

			        if (empty($WinnerList)) {
			            echo outPut(0, lang('SUCCESS_CODE'), lang('ORDER_ID_EMPTY'), $result);
			            return;
			        }else{


			        	 	/*===============================
				                 FETCH USER DATA
					        =============================== */
					        $Fields = [ '_id', 'users_id', 'availableArabianPoints', 'totalArabianPoints','status' ];
					        $userDetails = $this->common_model ->getSingleDataByParticularField( $Fields, 'uw_users', 'users_id', $userID );
					        if(empty($userDetails)) {
					            throw new Exception('User not found');
					        }elseif($userDetails['status'] != "A") {
					            throw new Exception('ACCOUNT_INACIVE');
					        }else{

						        /*===============================
						           CALCULATE TOTAL PRIZE
						        =============================== */

						        $totalPrizeAmount = 0;

						        foreach ($WinnerList as $item) {
						            if (!empty($item['redeem_status']) && $item['redeem_status'] === 'paid') {
						                echo outPut(0, lang('SUCCESS_CODE'), lang('ALREADY_REDEEM'), $result);
						                return;
						            }
						            $totalPrizeAmount += (float)$item['amount'];
						        }

					         	/* ===============================
					             	START MONGODB TRANSACTION
						        =============================== */

						        // $client  = $this->mongodb_client->getClient();
						        // $session = $client->startSession();
						        // $session->startTransaction();


						        /* ===============================
						           UPDATE WINNER STATUS
						        =============================== */

						        $winnerUpdate = [
						            // 'redeem_status'  => $redeemStatus,
						            // 'redeem_by_mode' => $redeemByMode,
						            'modified_at'    => date('Y-m-d H:i'),
						            'seller_id'      => $userID,
						            'created_ip'     => currentIp()
						        ];

						        $winnerCondition = [
						            'order_id'      => $orderID,
						            'status'        => 1,
						            'redeem_status' => ['$ne' => 'paid']
						        ];

				         	 	$winnerResult = $this->mongodb_client->updateDocument(
			                 	   'uw_uwin_winner',
				                    $winnerCondition,
				                    ['$set' => $winnerUpdate],
				                    $session
				                );

			         	 	 	/* ===============================
						           UPDATE USER WALLET
						        =============================== */

						        $userUpdate = [
						            'totalArabianPoints'     => (float)$userDetails['totalArabianPoints'] + $totalPrizeAmount,
						            'availableArabianPoints' => (float)$userDetails['availableArabianPoints'] + $totalPrizeAmount,
						            'update_date'            => date('Y-m-d H:i')
						        ];


					        }

				         	


					        





		          


			        /* ===============================
			           UPDATE USER WALLET
			        =============================== */

			        $userUpdate = [
			            'totalArabianPoints'     => (float)$userDetails['totalArabianPoints'] + $totalPrizeAmount,
			            'availableArabianPoints' => (float)$userDetails['availableArabianPoints'] + $totalPrizeAmount,
			            'update_date'            => date('Y-m-d H:i')
			        ];

			        $userResult = $this->common_model
			            ->editData(
			                'uw_users',
			                $userUpdate,
			                'users_id',
			                $userID,
			                ['session' => $session]
			            );

			        if (empty($userResult)) {
			            throw new Exception('Wallet update failed');
			        }

			        /* ===============================
			           INSERT LOAD BALANCE
			        =============================== */

			        $Redeemparam = [
			            'load_balance_id'        => (int)$this->geneal_model->getNextSequence('uw_loadBalance'),
			            'user_oid'               => new MongoDB\BSON\ObjectId($userDetails['_id']['$id']),
			            'order_id'               => $orderID,
			            'user_id_deb'            => 0,
			            'user_id_cred'           => $userID,
			            'upoints'                => (float)$totalPrizeAmount,
			            'record_type'            => 'Credit',
			            'narration'              => 'Moved winning Prize',
			            'remarks'                => "Prize for ($orderID) transferred to wallet.",
			            'availableArabianPoints' => (float)$userDetails['availableArabianPoints'],
			            'end_balance'            => (float)$userDetails['availableArabianPoints'] + $totalPrizeAmount,
			            'creation_ip'            => currentIp(),
			            'created_at'             => date('Y-m-d H:i'),
			            'created_by'             => $userID,
			            'status'                 => 'A'
			        ];

			        $this->geneal_model
			            ->addData(
			                'uw_loadBalance',
			                $Redeemparam,
			                ['session' => $session]
			            );

			        /* ===============================
			           COMMIT TRANSACTION
			        =============================== */

			        $session->commitTransaction();

			        echo outPut(
			            1,
			            lang('SUCCESS_CODE'),
			            lang('COUPON_REDEEMED_SUCCESFULLY'),
			            ['payment_date' => date('Y-m-d H:i')]
			        );


















			        }

			       
		        }

	       

	    } catch (Throwable $e) {

	        if (isset($session)) {
	            $session->abortTransaction();
	        }

	        log_message('error', 'moveToWallet error: ' . $e->getMessage());

	        echo outPut(
	            0,
	            lang('SUCCESS_CODE'),
	            lang('ERROR_OCCURRED'),
	            ['error' => $e->getMessage()]
	        );
	    }
	}

	/* * *********************************************************************
	 * * Function name  : orderCancellation
	 * * Developed By 	: Dilip Halder
	 * * Purpose  		: This function used for order Cancellation
	 * * Date 			: 11 July 2024
	 * * **********************************************************************/
	public function orderCancellation()
	{
		try {

			$apiHeaderData = getApiHeaderData();
			$this->generatelogs->putLog('APP',logOutPut($_POST));
			$result 	   = array();	
			if(requestAuthenticate(APIKEY,'POST')):

				$userId  = $this->input->post('user_id');
				$orderId = $this->input->post('order_id');
				if(empty($userId)):
					throw new Exception(lang('USER_ID_EMPTY'), 1);
				elseif(empty($orderId)):
					throw new Exception(lang('ORDER_ID_EMPTY'), 1);
				else:

					/*--------------------------------------------- Starting of Transaction ---------------------------------------------*/ 
					$this->session->sess_regenerate();
					$session = $this->mongodb_client->client->startSession();
					$session->startTransaction();
					
					$tblName             = 'uw_lotto_orders';
					$whereCon1['select'] = ['_id', 'order_id', 'user_id', 'draw_date', 'draw_time','ticket','total_price','status'];
					$whereCon1['where']  = array('order_id' => $orderId , 'user_id' => (int)$userId );
					$orderDetails      	 = $this->common_model->getData('single',$tblName,$whereCon1);
					if(empty($orderDetails)):
						throw new Exception(lang('ORDET_ID_INVALID'), 1);
					elseif($orderDetails['status'] == 'CL'):
						throw new Exception(lang('ORDER_ALREADY_CANCELLED'), 1);
					elseif(!empty($orderDetails) && $orderDetails['status'] == 'A'):
						$currentTime        = strtotime(date('H:i'));
						$drawDateTime       = strtotime($orderDetails['draw_date'].' '.$orderDetails['draw_time']);
						$drawDateTimeMinus5 = $drawDateTime - (5 * 60);
						if($currentTime >= $drawDateTimeMinus5 && $currentTime <= $drawDateTime):
							throw new Exception(lang('CANCELLATION_STOP_DRAW_UNDERWAY'), 1);
						else:

							$whereCon['where'] = array('users_id' => (int)$userId);
							$UserData 	   	   = $this->common_model->getParticularFieldByMultipleCondition($FieldList,'uw_users',$whereCon );
							
							$user_OId          = $UserData['_id']['$id'];
							/* updated order status */
							$CancellationID         = $orderDetails['_id']->{'$id'};
							$param1['status']		= 'CL';
							$param1['update_ip']	= currentIp();
							$param1['update_date']  = (int)$this->timezone->utc_time();//currentDateTime();
							$param1['refund_date']	= (int)$this->timezone->utc_time();//currentDateTime();
							$param1['updated_by']	= (int)$userId;
							$orderWhereCon          = array('_id' => new MongoDB\BSON\ObjectId($CancellationID));
							$update1 = $this->mongodb_client->updateDocument('uw_lotto_orders',$orderWhereCon, ['$set' => $param1],$session);
							
							/* Generating order cancellation record in loadbalance */
							$refundparam["load_balance_id"]		     =	(int)$this->common_model->getNextSequence('uw_loadBalance');
							$refundparam["order_oid"] 			     =	new MongoDB\BSON\ObjectId($orderDetails['_id']->{'$id'});
							$refundparam["user_oid"] 				 =	new MongoDB\BSON\ObjectId($user_OId);
							$refundparam["user_id_cred"] 			 =	(int)$UserData['users_id'];
							$refundparam["user_id_deb"]			 	 =	(int)0;
							$refundparam["order_id"] 				 =	$orderDetails['order_id'];
							$refundparam["upoints"] 				 =	(float)$orderDetails['total_price'];
							$refundparam["availableArabianPoints"] 	 =	(float)$UserData['availableArabianPoints'];
							$refundparam["end_balance"] 			 =	(float)$UserData['availableArabianPoints'] + (float)$orderDetails['total_price'];
							$refundparam["record_type"] 			 =	'Credit';
							$refundparam["narration"]				 =	'Order Cancelled';
							$refundparam["remarks"]				 	 =	'Ticket ID : '.$orderDetails['order_id'];
							$refundparam["creation_ip"] 			 =	$this->input->ip_address();
							$refundparam["created_at"] 			 	 =	date('Y-m-d H:i');
							$refundparam["created_by"] 			 	 =	(int)$UserData['users_id'];
							$refundparam["status"] 				 	 =	"A";
							// echo "<pre>";print_r($refundparam);die();
							$update2 = $this->mongodb_client->insertDocument('uw_loadBalance', $refundparam, $session);
							
							/* Balance Updated.. */
							$updateBalance = array('availableArabianPoints' =>  $refundparam["end_balance"]);
							$update3 = $this->mongodb_client->updateDocument(
								'uw_users',         				// Collection name
								['users_id' => (int)$userId],       // Filter / condition for which document to update
								['$set' => $updateBalance],         // Proper MongoDB update syntax
								$session                            // MongoDB session (optional)
							);

							$session->commitTransaction();
							$this->mongodb_client->commitTransaction($session);
							echo outPut(1,lang('SUCCESS_CODE'),lang('ORDER_CANCELLED_SUCCESSFULLY'),$result);
						 
						endif;
					endif;
				endif;
			else:
				throw new Exception(lang('FORBIDDEN_MSG'), 1);
			endif;
			
		} catch (Exception $e) {
			echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);die();
		}
	}
    
}
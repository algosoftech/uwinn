<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Scratchcards extends CI_Controller {
	
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
		$this->load->library('mongodb_client');
	} 

	/* * *********************************************************************
	 * * Function name : getGameList
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to getGameList
	 * * Date 		   : 22 July 2026
	 * * **********************************************************************/
	public function getGameList()
	{
		$apiHeaderData = getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 	   = array();
		try {
			if(requestAuthenticate(APIKEY,'POST')):

				$session = $this->mongodb_client->client->startSession();
				$session->startTransaction();

				$usersId  = $this->input->post('users_id');
				$gameMode = $this->input->post('game_mode');

				if(empty($usersId)):
					throw new Exception(lang('USER_ID_EMPTY'), 1);
				elseif(empty($gameMode)):
					throw new Exception(lang('GAME_MODE_EMPTY'), 1);
				else:

					// $apcu_key  = 'scratch_&_win_games_list';
					// $resultData = apcu_fetch($apcu_key);
					// // echo "<pre>";print_r($resultData);die();
					// if(empty($resultData)):
						// apcu_store($apcu_key, $resultData, 3600);
					// else:
					// endif;

					$tblName    = 'uw_scratch_win_games';
					$whereCon['where']['status']         = "A";
					$whereCon['where']['start_date']     = array('$lte' => strtotime(date('Y-m-d H:i:s')));
					$whereCon['where']['expiry_date']    = array('$gt' => strtotime(date('Y-m-d H:i:s')));
					$whereCon['where']['prize_setting']  = "enabled";
					$whereCon['where']['game_mode']      = $gameMode;
					$shortField = array('seq_order' => 1);
					$resultData = $this->mongodb_client->getDocument('multiple',$tblName,$whereCon['where'],$session,'','','',$shortField);
					if(empty($resultData)):
						throw new Exception(lang('DATA_NOT_FOUND'), 1);
					else:
						foreach($resultData as $key => $items):
							unset($resultData[$key]['rtp_config']);
							unset($resultData[$key]['rtp_prize_slabs']);
							unset($resultData[$key]['prize_slab_mode']);
						endforeach;
						$this->mongodb_client->commitTransaction($session);
						$result['campaignlist'] = $resultData;
						echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'), (object) $result);
					endif;
					
				endif;
			else:
				throw new Exception(lang('FORBIDDEN_MSG'),1);
			endif;
		} catch (Exception $e) {
			$this->mongodb_client->abortTransaction($session);
			echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);	
		}
	}

	/* * *********************************************************************
	 * * Function name : orderCreate
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to create order for scratch & win games
	 * * Date 		   : 30 July 2026
	 * * **********************************************************************/
	public function orderCreate()
	{
		$apiHeaderData = getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 	   = array();
		try {
			if(requestAuthenticate(APIKEY,'POST')):
				$usersId    = $this->input->post('users_id');
				$productsId = $this->input->post('products_id');
				$qty        = $this->input->post('qty')?:1;
				$tickets    = $this->input->post('tickets');
				$txnID      = $this->input->post('txn_id');

 				$smsType          = $this->input->post('otp_sent');
				$buyerCountryCode = $this->input->post('buyer_country_code');
				$buyerMobile      = $this->input->post('buyer_mobile');
				$buyerEmail       = $this->input->post('buyer_email');
				
				if(empty($usersId)):
					throw new Exception(lang('USER_ID_EMPTY'), 1);
				elseif(empty($productsId)):
					throw new Exception(lang('PRODUCT_ID_EMPTY'), 1);
				else:
					
					$tblName           = 'users';
					$whereCon['where'] = array('users_id' => (int)$usersId);
					$userData          = $this->mongodb_client->getDocument('single',$tblName,$whereCon['where'],$session);
					if(empty($userData)):
						throw new Exception(lang('INVALID_USER'), 1);
					elseif($userData['status'] != "A"):
						throw new Exception(lang('ACCOUNT_BLOCKED'), 1);
					else:

						// Use MongoDB transaction for cancellation (refund + status update)
						$this->session->sess_regenerate();
						$session = $this->mongodb_client->client->startSession();
						$session->startTransaction();


						// check game data
						$tblName           = 'tambola_games';
						$whereCon['where'] = array();
						$whereCon['where']['_id']                   = new MongoDB\BSON\ObjectID($productsId);
						$whereCon['where']['status']                = "A";
						$whereCon['where']['expiry_date']           = array('$gt' => strtotime(date('Y-m-d H:i:s')));
						$whereCon['where']['prize_setting']         = "enabled";

						$fieldList  = array('price','game_type','maximum_payout_per_day','payout_configurations','number_repeat','draw_range_count','number_range_start','number_range_end','_id');
						$fieldList  = array();
						$gameData   = $this->common_model->getParticularFieldByMultipleCondition($fieldList,$tblName,$whereCon);
						// echo "<pre>";print_r($gameData);die();	
						if(empty($gameData)):
							throw new Exception(lang('DATA_NOT_FOUND'), 1);
						elseif($userData['availableArabianPoints'] < $gameData['price'] * $qty):
							throw new Exception(lang('LOW_BALANCE'), 1);
						elseif($gameData['coupon_selection_type'] == 'manual' && empty($tickets)):
							throw new Exception(lang('COUPON_NOT_SELECTED'), 1);
						else:
							
							
							//Buffering time order duplication check.. START
							if(!empty($txnID)):
								// $checkWhereCon1['where']['users_oid']    = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
								// $checkWhereCon1['where']['products_oid'] = new MongoDB\BSON\ObjectID($productsId);
								// $checkWhereCon1['where']['status']       = "A";
								$checkWhereCon1['where']['txn_id']       = $txnID;
								// $checkWhereCon1['where']['tickets']      = array('$in'  => json_decode($tickets, true));
								// $checkWhereCon1['where']['created_at']   = array('$gte' => strtotime(date('Y-m-d H:i:s', strtotime('-10 seconds')))  );
								$resultData = $this->mongodb_client->getDocument('single','tambola_orders',$checkWhereCon1['where'] , $session);
								if(!empty($resultData)):
									echo outPut(1, lang('SUCCESS_CODE'), lang('ALREADY_ORDER_PLACED'), $resultData);
									die();
								endif;

								if(empty($resultData)):
									$checkWhereCon2['where']['users_oid']    = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
									$checkWhereCon2['where']['products_oid'] = new MongoDB\BSON\ObjectID($productsId);
									// $checkWhereCon2['where']['tickets']      = array('$in'  => json_decode($tickets, true));
									$checkWhereCon2['where']['created_at']   = array('$gte' => strtotime(date('Y-m-d H:i:s', strtotime('-2 seconds')))  );
									$resultData2 = $this->mongodb_client->getDocument('single','tambola_orders',$checkWhereCon2['where'] , $session);
									if(!empty($resultData2)):
										echo outPut(1, lang('SUCCESS_CODE'), lang('ALREADY_ORDER_PLACED'), $resultData2);
										die();
									endif;
								endif;
							endif;
							//Buffering time order duplication check.. END

							// APCU buffering duplicate check (2 seconds).. START
							if (function_exists('apcu_fetch') && function_exists('apcu_store')):

								$duplicateKey = 'tambola_order_data_' . (int)$usersId;
								$currentData = array(
									'current_time'       => time(),
									'current_time_stamp' => date('Y-m-d H:i:s'),
									'users_id'           => (int)$usersId,
									'products_id'        => (string)$productsId,
									'qty'                => (int)$qty,
									'tickets'            => $this->input->post('tickets'),
									// 'txn_id'             => (string)$txnID,
								);

								$cachedData = apcu_fetch($duplicateKey);

								if ($cachedData !== false):
									// $compareKeys = array('users_id', 'products_id', 'qty', 'tickets', 'txn_id');
									$compareKeys = array('users_id', 'products_id', 'qty', 'tickets');
									$isSameRequest = true;
									foreach ($compareKeys as $compareKey):
										if (!isset($cachedData[$compareKey]) || $cachedData[$compareKey] != $currentData[$compareKey]):
											$isSameRequest = false;
											break;
										endif;
									endforeach;
									if ($isSameRequest):
										throw new Exception(lang('ALREADY_ORDER_PLACED'), 1);
									endif;
								endif;

								apcu_store($duplicateKey, $currentData, 40);
								$duplicateLocked = true;
							endif;
							// APCU buffering duplicate check (2 seconds).. END

							if(!empty($tickets)):
							    $tickets = json_decode($tickets, true);
							else:
								$tickets = $this->generateTambolaTickets($gameData, $qty, $ticketData);
							endif;

							$winningnumbersArray = $this->generateWinningNumbers($gameData, $tickets, $session);

							$winningnumbers = isset($winningnumbersArray['winning_numbers']) && is_array($winningnumbersArray['winning_numbers'])
								? $winningnumbersArray['winning_numbers'] : array();
							$candidateWinningType = isset($winningnumbersArray['winning_type']) && $winningnumbersArray['winning_type'] !== ''
								? $winningnumbersArray['winning_type'] : 'normal_prize';

							// winning_status = ticket overlap with draw; winning_type only when user has a matching number
							$winningStatus = 'N';
							if (!empty($tickets) && !empty($winningnumbers)):
								$winningNumbersList = array_column($winningnumbers, 'numbers');
								foreach ($winningNumbersList as $winningNumber):
									foreach ($tickets as $ticketValue):
										if (is_array($ticketValue) && in_array($winningNumber, $ticketValue, true)):
											$winningStatus = 'Y';
											break 2;
										endif;
									endforeach;
								endforeach;
							endif;

							$winningType = ($winningStatus === 'Y') ? $candidateWinningType : '';
							$winningAmount = 0.0;
							if ($winningStatus === 'Y' && !empty($tickets) && !empty($winningnumbers)):
								foreach ($winningnumbers as $winRow):
									$winRow = is_object($winRow) ? (array) $winRow : $winRow;
									if (!is_array($winRow)):
										continue;
									endif;
									$drawnNumber = isset($winRow['numbers']) ? (int)$winRow['numbers'] : 0;
									$drawnPrize  = isset($winRow['prize']) && is_numeric($winRow['prize']) ? (float)$winRow['prize'] : 0.0;
									if ($drawnNumber <= 0 || $drawnPrize <= 0):
										continue;
									endif;
									foreach ($tickets as $ticketValue):
										if (is_array($ticketValue) && in_array($drawnNumber, $ticketValue, true)):
											$winningAmount += $drawnPrize;
										endif;
									endforeach;
								endforeach;
							endif;

							$winningType = ($winningStatus === 'Y') ? $candidateWinningType : '';

							$orderSeq = floor((microtime(true) * 1000)).rand(100,999);
							$totalPrice     = $gameData['price'] * $qty;
							$param['order_id']      = "KEN".$orderSeq;
							$param['txn_id']        = $txnID;
							$param['users_id']      = (int)$usersId;
							$param['users_oid']     = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
							$param['products_oid']  = new MongoDB\BSON\ObjectID($productsId);
							$param['products_name'] = $gameData['title'];
							$param['qty']           = (int)$qty;
							$param['total_price']   = (float)$totalPrice;
							$param['winning_status']=  $winningStatus;
							$param['winning_type']    = $winningType;
							if ($winningAmount > 0):
								$param['winning_amount'] = (float)$winningAmount;
							endif;
							$param['tickets']         = $tickets;
							$param['winning_numbers'] = $winningnumbers;

                           $param['sms_type']           = $smsType;
							$param['buyer_country_code'] = $buyerCountryCode;
							$param['buyer_mobile']       = (int)$buyerMobile;
							$param['buyer_email']        = $buyerEmail;

							$param['created_at']    = (int)$this->timezone->utc_time();
							// $param['created_at']    = strtotime(date('Y-m-11 H:i:s'));

							$param['created_by']    = (int)$usersId;
							$param['status']        = 'A';
							$param['used_rtp']      = $winningnumbersArray['used_rtp'];
							$result = $this->common_model->addData('tambola_orders',$param);
							// $result = $this->mongodb_client->insertDocument('tambola_orders',$param, $session);


							$loadBalanceParam['users_id']      = (int)$usersId;
							$loadBalanceParam['users_oid']     = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
							$loadBalanceParam['load_balance_id'] = $this->common_model->getNextSequence('loadBalance');
							$loadBalanceParam['order_oid']     = new MongoDB\BSON\ObjectID($result['_id']->{'$id'});
							$loadBalanceParam['product_oid']   = new MongoDB\BSON\ObjectID($productsId);
							$loadBalanceParam['user_id_deb']   = (int)$usersId;
							$loadBalanceParam['user_id_cred']  = (int)0;
							$loadBalanceParam['order_id']      = $result['order_id'];
							$loadBalanceParam['upoints']       = (float)$totalPrice;
							$loadBalanceParam['availableArabianPoints'] = (float)$userData['availableArabianPoints'];
							$loadBalanceParam['end_balance']   = (float)$userData['availableArabianPoints'] - (float)$totalPrice;
							$loadBalanceParam['record_type']   = 'Debit';
							$loadBalanceParam['narration']     = 'Tambola Order';
							$loadBalanceParam['remarks']       = 'Order ID: '.$result['order_id'];
							$loadBalanceParam['created_at']    = strtotime(date('Y-m-d H:i:s'));
							$loadBalanceParam['created_by']    = (int)$usersId;
							$loadBalanceParam['status'] = 'A';	
							// $result2 = $this->common_model->addData('loadBalance',$loadBalanceParam);
							$result2 = $this->mongodb_client->insertDocument('loadBalance',$loadBalanceParam, $session);


							$commission_percentage = $userData['tambola_commission_percentage']?$userData['tambola_commission_percentage']:15;
							$commission_amount     = ($totalPrice * $commission_percentage) / 100;
							
							$availableArabianPoints = (float)$userData['availableArabianPoints'] - (float)$totalPrice;
							
							if($commission_amount > 0):
								$commissionParam['users_id']      = (int)$usersId;
								$commissionParam['users_oid']     = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
								$commissionParam['load_balance_id'] = $this->common_model->getNextSequence('loadBalance');
								$commissionParam['order_oid']     = new MongoDB\BSON\ObjectID($result['_id']->{'$id'});
								$commissionParam['product_oid']   = new MongoDB\BSON\ObjectID($productsId);
								$commissionParam['user_id_deb']   = (int)0;
								$commissionParam['user_id_cred']  = (int)$usersId;
								$commissionParam['order_id']      = $result['order_id'];
								$commissionParam['upoints']       = (float)$commission_amount;
								$commissionParam['availableArabianPoints'] = (float)$loadBalanceParam['end_balance'];
								$commissionParam['end_balance']   = (float)$loadBalanceParam['end_balance']+(float)$commission_amount;
								$commissionParam['record_type']   = 'Credit';
								$commissionParam['narration']     = 'Tambola Commission';
								$commissionParam['remarks']       = 'Order ID: '.$result['order_id'];
								$commissionParam['created_at']    = strtotime(date('Y-m-d H:i:s'));
								$commissionParam['created_by']    = (int)$usersId;
								$commissionParam['status']        = 'A';	
								// $result3 = $this->common_model->addData('loadBalance',$commissionParam);
								$result3 = $this->mongodb_client->insertDocument('loadBalance',$commissionParam, $session);

								$availableArabianPoints = ((float)$userData['availableArabianPoints'] - (float)$totalPrice ) + (float)$commission_amount;
								
							endif;
							
							/* Update user availableArabianPoints */
							$userParam['availableArabianPoints'] = (float)$availableArabianPoints;
							$userParam['updated_at'] = date('Y-m-d H:i:s');
							$userParam['updated_by'] = (int)$usersId;
							// $this->common_model->editData('users',$userParam,'_id', new MongoDB\BSON\ObjectID($userData['_id']->{'$id'}));
							$this->mongodb_client->updateDocument('users',['_id' => new MongoDB\BSON\ObjectID($userData['_id']->{'$id'})],['$set' => $userParam], $session);

							$session->commitTransaction();
							$this->mongodb_client->commitTransaction($session);

                             $message = "";
							if(!empty($tickets) && ( ( !empty($buyerCountryCode) && !empty($buyerMobile) ) || !empty($buyerEmail) )  ):
								$output        = [];
								$CouponDetails = '';
								$map = ['S', 'R', 'C'];
								foreach ($tickets as $key => $tickectitem) {
									if (!is_array($tickectitem)):
										continue;
									endif;

								
									if (isset($tickectitem['ticket'])):
										$line = is_array($tickectitem['ticket']) ? implode(',', $tickectitem['ticket']) : $tickectitem['ticket'];
										$line .= ' (';
										if($tickectitem['type'] == "straight"):
											$line .= 'S';
										elseif($tickectitem['type'] == "rumble"):
											$line .= 'R';
										elseif($tickectitem['type'] == "chance"):
											$line .= 'C';
										endif;
										$line .= ')';
										$output[] = $line;
									else:
										// Tambola auto tickets: [1,5,12,...]
										$output[] = implode(',', array_map('strval', $tickectitem));
									endif;
								}

								$CouponDetails = implode('. ', $output);

								if(!empty($gameData['draw_time'])):
									$drawTimeTs = is_numeric($gameData['draw_time']) ? (int)$gameData['draw_time'] : strtotime($gameData['draw_time']);
								elseif(!empty($gameData['expiry_date'])):
									$drawTimeTs = (int)$gameData['expiry_date'];
								else:
									$drawTimeTs = 0;
								endif;

								if(!empty($drawTimeTs)):
									$drawDate = date('d.m.Y h:iA', $drawTimeTs);
								else:
									$drawDate = '';
								endif;

								$drawDate = date('d.m.Y h:iA');
								$message = 'Order ID '.$result['order_id'].' of '.$gameData['title'].' with coupons '.$CouponDetails.' purchased on '.$drawDate.'. You can download the invoice here https://tktinvoice.com/uwin-download-invoice/'.$result['order_id'];

								if(!empty($buyerCountryCode) && !empty($buyerMobile) && !empty($message) && $smsType == "SMS"):

									$enableSmsFields   = ['default_sms'];
									$enableTblName     = 'enablesms';
									$enableSMSData     = $this->common_model->getSingleDataByParticularField($enableSmsFields,$enableTblName, 'status', 'A');
									$defaultSMSGateway = $enableSMSData['default_sms'];
									
									$senderDetails['gateway']       = $defaultSMSGateway;
									$senderDetails['users_mobile']  = $buyerMobile;
									$senderDetails['country_code']  = $buyerCountryCode;
									$senderDetails['message']       = $message;
									$this->sms_model->sendSMS($senderDetails);

								elseif(!empty($buyerCountryCode) && !empty($buyerMobile) && !empty($message) && $smsType == "WhatsApp"):
									$senderDetails['country_code']  = $buyerCountryCode;
									$senderDetails['users_mobile']  = $buyerMobile;
									$senderDetails['message']       = $message;
									$senderDetails['ORDERID']       = $result["order_id"];
									$senderDetails['CAMPAIGNAME']   = $gameData["title"];
									$senderDetails['CouponDetails'] = $CouponDetails;
									$senderDetails['DDATE']         = $drawDate;
									$senderDetails['LINK']          = 'https://instinvoice.net/instwin-download-invoice/'.$result["order_id"];
									// $senderDetails['LINK']          = 'https://staging.u-winn.net/uwin-download-invoice/'.$result["order_id"];
									$senderDetails['TEMPLATE_NAME'] = 'instwin_iw_campigns';
									$senderDetails['CHANNEL_ID']    = '6a59deab2943c0a34da79f68';
									$this->sms_model->sendWhatsAppMessage($senderDetails);
								elseif(!empty($buyerEmail) && !empty($message) && $smsType == "EMAIL"):
									$subject = "Order Confirmation";
									$this->emailsendgrid_model->sendEmail($buyerEmail,$subject,$message);
								endif;	
							endif;

							echo outPut(1, lang('SUCCESS_CODE'), lang('SUCCESS_ACTION'), $result);
						endif;
					endif;
					
				endif;

			else:
				throw new Exception(lang('FORBIDDEN_MSG'),1);
			endif;
		} catch (Exception $e) {
			echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);	
		}
	}


	 
	 
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Hourlygames extends CI_Controller {
	
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
	 * * Date 		   : 15 April 2026
	 * * **********************************************************************/
	public function getGameList()
	{
		$apiHeaderData = getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_GET));
		$result 	   = array();
		try {
			if(requestAuthenticate(APIKEY,'GET')):


				$usersId = $this->input->get('users_id');
				
				$tblName    = 'uw_hourly_games';
				$whereCon['where']['status']         = "A";
				$whereCon['where']['start_date']     = array('$lte' => strtotime(date('Y-m-d H:i:s')));
				$whereCon['where']['expiry_date']    = array('$gt' => strtotime(date('Y-m-d H:i')));
				$whereCon['where']['prize_setting']  = "enabled";
				
				$shortField = array('seq_order' => 1);
				$resultData = $this->common_model->getData('multiple',$tblName,$whereCon,$shortField);

				// if($usersId  == 100000000000016 || $usersId  == 100000000001252 || $usersId  == 2010194 || $usersId  == 100000000000514  || $usersId  == 100000000000843):
					// foreach($resultData as $key => $items):
					// 	// if($items['is_24_hours'] != "Y" && empty($items['title']) == "ROCKET 6/24"):
					// 	if($items['title'] !== "ROCKET 6/24"):
					// 		unset($resultData[$key]);
					// 	endif;
					// endforeach;
				// else:
				// 	foreach($resultData as $key => $items):
				// 		if($items['title'] == "ROCKET 6/24"):
				// 			unset($resultData[$key]);
				// 		endif;
				// 	endforeach;
				// endif;

				if($resultData):
					$dd = array();
					$current_date = strtotime(date('Y-m-d H:i:s'));
					foreach($resultData as $key => $items):
						$start_date  = (int)$items['start_date'];
						$expiry_date = (int)$items['expiry_date'];
						$intervalSeconds = 3600; // hourly draw slot

						$nextDrawTs = $start_date;
						if($current_date > $start_date):
							$elapsed   = $current_date - $start_date;
							$nextIndex = (int)floor($elapsed / $intervalSeconds) + 1;
							$nextDrawTs = $start_date + ($nextIndex * $intervalSeconds);
						endif;
						if($nextDrawTs > $expiry_date):
							$nextDrawTs = $expiry_date;
						endif;

						$dd[$key] = $items;
						$dd[$key]['total_hours'] = max(1, (int)ceil(($expiry_date - $start_date) / 3600));
						$dd[$key]['draw_time']   = $nextDrawTs;
						$dd[$key]['draw_date_string']   = date('Y-m-d H:i:s', $nextDrawTs);

						if($nextDrawTs != $items['draw_time']):
							$oid = $items['_id']->{'$id'};
							$this->common_model->editData('uw_hourly_games',['draw_time' => $nextDrawTs],'_id', new MongoDB\BSON\ObjectID($oid));
						endif;
					endforeach;
				endif;

				if(empty($resultData)):
					throw new Exception(lang('DATA_NOT_FOUND'), 1);
				else:
					$result['campaignlist'] = $dd;
					echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'), (object) $result);
				endif;
			else:
				throw new Exception(lang('FORBIDDEN_MSG'),1);
			endif;
		} catch (Exception $e) {
			echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);	
		}
	}

	/* * *********************************************************************
	 * * Function name : orderCreate
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to orderCreate
	 * * Date 		   : 31 March 2026
	 * * **********************************************************************/
	// public function orderCreate()
	// {
	// 	$apiHeaderData = getApiHeaderData();
	// 	$this->generatelogs->putLog('APP',logOutPut($_POST));
	// 	$result 	   = array();
	// 	try {
	// 		if(requestAuthenticate(APIKEY,'POST')):
	// 			$usersId    = $this->input->post('users_id');
	// 			$productsId = $this->input->post('products_id');
	// 			$qty        = $this->input->post('qty')?:1;
	// 			$modes      = json_decode($this->input->post('modes'), true);
	// 			$tickets    = json_decode($this->input->post('tickets'), true);
	// 			$smsType 	      = $this->input->post('otp_sent');
	// 			$buyerCountryCode = $this->input->post('buyer_country_code');
	// 			$buyerMobile 	  = $this->input->post('buyer_mobile');
	// 			$buyerEmail 	  = $this->input->post('buyer_email');
	// 			$isCouponsRequired = $this->input->post('is_coupons_required');

	// 			// optional fields
	// 			$deliveryAddress = $this->input->post('delivery_address');
	// 			$deliveryCharge  = $this->input->post('delivery_charge');
	// 			$usersAddress    = $this->input->post('users_address');
	// 			$usersLong       = $this->input->post('users_long');
	// 			$usersLat        = $this->input->post('users_lat');
	// 			$appName         = $this->input->post('app_name');
	// 			$appVersion      = $this->input->post('app_version');
	// 			$duplicateCheck  = $this->input->post('duplicate_check');
	// 			$txnID           = $this->input->post('txn_id');
	// 			$is24Hours       = $this->input->post('is_24_hours');
	// 			$drawTime        = $this->input->post('draw_time');
	// 			$drawTimeString  = $this->input->post('draw_time_string');

	// 			if(empty($usersId)):
	// 				throw new Exception(lang('USER_ID_EMPTY'), 1);
	// 			elseif(empty($productsId)):
	// 				throw new Exception(lang('PRODUCT_ID_EMPTY'), 1);
	// 			else:
					
	// 				$tblName           = 'uw_users';
	// 				$whereCon['where'] = array('users_id' => (int)$usersId);
	// 				$userData          = $this->common_model->getData('single',$tblName,$whereCon);
	// 				// echo "<pre>";print_r($userData);die();
					
	// 				if(empty($userData)):
	// 					throw new Exception(lang('INVALID_USER'), 1);
	// 				elseif($userData['status'] != "A"):
	// 					throw new Exception(lang('ACCOUNT_BLOCKED'), 1);
	// 				elseif($userData['enable_hourly_games'] == "N"):
	// 					throw new Exception("HOURLY_GAME_DISABLED", 1);
	// 				elseif($is24Hours == "Y" && empty($drawTimeString)):
	// 					throw new Exception(lang('DRAW_TIME_REQUIRED'), 1);
	// 				else:
						
	// 					$this->session->sess_regenerate();
	// 					$session = $this->mongodb_client->client->startSession();
	// 					$session->startTransaction();

	// 					// check game data
	// 					$tblName           = 'uw_hourly_games';
	// 					$whereCon['where'] = array();
	// 					$whereCon['where']['_id']                   = new MongoDB\BSON\ObjectID($productsId);
						
	// 					if($usersId  == 100000000000016 || $usersId  == 100000000001252 || $usersId  == 2010194 || $usersId  == 100000000000514  || $usersId  == 100000000000843):
	// 						$whereCon['where']['is_24_hours']    = "Y";
	// 					else:
	// 						$whereCon['where']['status']         = "A";
	// 					endif;
	// 					$whereCon['where']['expiry_date']           = array('$gt' => strtotime(date('Y-m-d H:i:s')));
	// 					$whereCon['where']['prize_setting']         = "enabled";
	// 					$fieldList  = array();
	// 					$gameData   = $this->common_model->getParticularFieldByMultipleCondition($fieldList,$tblName,$whereCon);

	// 					// commented code --- 02 July 2026 ---
	// 					// if(strtotime('-10 seconds',$gameData['draw_time']) < strtotime(date('Y-m-d H:i:s'))):
	// 					// 	throw new Exception('Campaign expired. Please refresh the page and try again.');
	// 					// endif;

	// 					// if($userData['users_type'] == "Users" && strtotime('-2 minutes',$gameData['draw_time']) < strtotime(date('Y-m-d H:i:s'))):
	// 					// 	throw new Exception('Campaign expired. Please refresh the page and try again.');
	// 					// elseif( $userData['users_type'] != "Users" && strtotime('-10 seconds',$gameData['draw_time']) < strtotime(date('Y-m-d H:i:s'))):
	// 					// 	throw new Exception('Campaign expired. Please refresh the page and try again.');
	// 					// endif;
	// 					// ------------------------------------------------------------


	// 					if( empty($is24Hours) || $is24Hours != 'Y'):
	// 						if(strtotime('-10 seconds',$gameData['draw_time']) < strtotime(date('Y-m-d H:i:s'))):
	// 							throw new Exception('Campaign expired. Please refresh the page and try again.');
	// 						endif;
	
	// 						if($userData['users_type'] == "Users" && strtotime('-2 minutes',$gameData['draw_time']) < strtotime(date('Y-m-d H:i:s'))):
	// 							throw new Exception('Campaign expired. Please refresh the page and try again.');
	// 						elseif( $userData['users_type'] != "Users" && strtotime('-10 seconds',$gameData['draw_time']) < strtotime(date('Y-m-d H:i:s'))):
	// 							throw new Exception('Campaign expired. Please refresh the page and try again.');
	// 						endif;
	// 					endif;
						
	// 					if(empty($isCouponsRequired)):
	// 						$isCouponsRequired = "Y";
	// 					endif;
						
	// 					// if(empty($gameData)):
	// 					// 	throw new Exception(lang('DATA_NOT_FOUND'), 1);
	// 					// elseif($userData['availableArabianPoints'] < $gameData['price'] * $qty):
	// 					// 	throw new Exception(lang('LOW_BALANCE'), 1);
	// 					// elseif($gameData['coupon_selection_type'] == 'manual' && empty($tickets)):
	// 					// 	throw new Exception(lang('COUPON_NOT_SELECTED'), 1);
	// 					// else:

	// 					if(empty($gameData)):
	// 						throw new Exception(lang('DATA_NOT_FOUND'), 1);
	// 					elseif($userData['availableArabianPoints'] < $gameData['price'] * $qty):
	// 						throw new Exception(lang('LOW_BALANCE'), 1);
	// 					elseif($gameData['coupon_selection_type'] == 'manual' && empty($tickets) && $isCouponsRequired == "Y"):
	// 						throw new Exception(lang('COUPON_NOT_SELECTED'), 1);
	// 					else:
							

	// 						//Buffering time order duplication check.. START
	// 						$checkWhereCon['where']['users_oid']    = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
	// 						$checkWhereCon['where']['products_oid'] = new MongoDB\BSON\ObjectID($productsId);
	// 						$checkWhereCon['where']['created_at'] =  array(  '$gte' => strtotime(date('Y-m-d H:i:s', strtotime('-10 seconds')))  );
	// 						$checkStartIndex    = 0;
	// 						$checkitemsPerPage  = 1;
	// 						$checkShortField = array('_id' => -1);
	// 						$resultData = $this->common_model->getHourlyGameOrderHistory($checkWhereCon,$checkShortField,$checkitemsPerPage,$checkStartIndex);
	// 						if(!empty($resultData)):
	// 							$resultData = $resultData[0];
	// 							if(!empty($resultData['ticketData'])):
	// 								$PreviewsTickets = array_column($resultData['ticketData'], 'ticket');
	// 								$currentTickets = array_column($tickets, 'ticket');
	// 								if(!array_diff($currentTickets, $PreviewsTickets)):
	// 									echo outPut(1, lang('SUCCESS_CODE'), lang('SUCCESS_ACTION'), $resultData);
	// 									die();
	// 								endif;
	// 							endif;
	// 						endif;

	// 						if(!empty($txnID)):
	// 							$checkWhereCon['where']['users_oid']    = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
	// 							$checkWhereCon['where']['products_oid'] = new MongoDB\BSON\ObjectID($productsId);
	// 							$checkWhereCon['where']['status']       = "A";
	// 							$checkWhereCon['where']['txn_id']       = $txnID;
	// 							$checkWhereCon['where']['created_at']   = array('$gte' => strtotime(date('Y-m-d H:i:s', strtotime('-1 minute')))  );
	// 							$checkStartIndex    = 0;
	// 							$checkitemsPerPage  = 1;
	// 							$checkShortField = array('_id' => -1);
	// 							$resultData = $this->common_model->getHourlyGameOrderHistory($checkWhereCon,$checkShortField,$checkitemsPerPage,$checkStartIndex);
	// 							if(!empty($resultData)):
	// 								$resultData = $resultData[0];
	// 								echo outPut(1, lang('SUCCESS_CODE'), lang('ALREADY_ORDER_PLACED'), $resultData);
	// 								die();
	// 							endif;
	// 						endif;

	// 						//Buffering time order duplication check.. END

	// 						$currentTime = strtotime(date('H:00:00'));

	// 						A :
	// 						$status = $isCouponsRequired == "Y" ? "A" : "INI";
	// 						$drawTime = $is24Hours == "Y" ? $drawTimeString : $gameData['draw_time'];
							
	// 						$currentDate = strtotime(date('Y-m-d H:i:s'));
	// 						$drawTi      = date('Y-m-d 17:00:00');
	// 						if(!empty($is24Hours) && $is24Hours == "Y" && !empty($drawTimeString)):
	// 							$drawTimeTs     = strtotime($drawTimeString);
	// 							$drawTimeString = $drawTimeString;
	// 						elseif(empty($is24Hours) && empty($drawTimeString) && $currentDate <= strtotime($drawTi)):
	// 							$drawTimeTs     = strtotime($drawTi);
	// 							$drawTimeString = $drawTi;
	// 						else:
	// 							if($currentTime >= '17:00:00' && $currentTime <= strtotime('23:59:59')):
	// 								$drawTi = date('Y-m-d H:00:00' , strtotime('+1 hour'));
	// 							endif;
	// 							$drawTimeTs     = strtotime($drawTi);
	// 							$drawTimeString = $drawTi;
	// 						endif;
							

	// 						$orderSeq      = floor((microtime(true) * 1000)).rand(100,999);
	// 						$totalPrice    = $gameData['price'] * $qty;
	// 						$param['order_id']        = "UWINN".$orderSeq;
	// 						$param['users_id']        = (int)$usersId;
	// 						$param['users_oid']       = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
	// 						$param['products_oid']    = new MongoDB\BSON\ObjectID($productsId);
	// 						$param['products_name']   = $gameData['title'];
	// 						$param['start_date']      = $gameData['start_date'];
	// 						$param['expiry_date']     = $gameData['expiry_date'];
	// 						// $param['draw_time']       = $gameData['draw_time'];
	// 						// $param['draw_time_string']= date('Y-m-d H:i:s', $gameData['draw_time']);
	// 						$param['draw_time']       = $drawTimeTs;
	// 						$param['draw_time_string']= $drawTimeString;
	// 						$param['qty']             = (int)$qty;
	// 						$param['total_price']     = (float)$totalPrice;
	// 						$param['sms_type']           = $smsType;
	// 						$param['buyer_country_code'] = $buyerCountryCode;
	// 						$param['buyer_mobile']       = (int)$buyerMobile;
	// 						$param['buyer_email']        = $buyerEmail;
	// 						$param['created_at']      = strtotime(date('Y-m-d H:i:s'));
	// 						$param['created_by']      = (int)$usersId;
	// 						// $param['status']       = 'A';
	// 						$param['is_24_hours']     = $is24Hours?$is24Hours:"N";
	// 						$param['status']          = $status;
	// 						$result = $this->common_model->addData('uw_hourly_orders',$param);
	// 						if(empty($result)):
	// 							goto A;
	// 						endif;
	// 						$orderOid = $result['_id']->{'$id'};

	// 						if(!empty($tickets) && $isCouponsRequired == "Y"):
	// 							foreach($tickets as $index => $item):
	// 								$ticketParam['order_oid']       = new MongoDB\BSON\ObjectID($orderOid);
	// 								$ticketParam['users_id']        = (int)$usersId;
	// 								$ticketParam['users_oid']       = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
	// 								$ticketParam['products_oid']    = new MongoDB\BSON\ObjectID($productsId);
	// 								$ticketParam['products_name']   = $gameData['title'];
	// 								$ticketParam['start_date']      = $gameData['start_date'];
	// 								$ticketParam['expiry_date']     = $gameData['expiry_date'];
	// 								$ticketParam['type']   			= $item['type'];
	// 								$ticketParam['ticket'] 			= $item['ticket'];
	// 								$ticketParam['points']          = $item['points'];
	// 								$ticketParam['created_at']      = strtotime(date('Y-m-d H:i:s'));
	// 								$ticketParam['created_by']      = (int)$usersId;
	// 								$ticketParam['status']          = 'A';
	// 								// $resultTicket = $this->common_model->addData('uw_hourly_tickets',$ticketParam);
	// 								$resultTicket = $this->mongodb_client->insertDocument('uw_hourly_tickets',$ticketParam, $session);

	// 							endforeach;
	// 						endif;

	// 						$loadBalanceParam['users_id']      = (int)$usersId;
	// 						$loadBalanceParam['user_oid']     = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
	// 						$loadBalanceParam['load_balance_id'] = $this->common_model->getNextSequence('loadBalance');
	// 						$loadBalanceParam['order_oid']     = new MongoDB\BSON\ObjectID($result['_id']->{'$id'});
	// 						$loadBalanceParam['product_oid']   = new MongoDB\BSON\ObjectID($productsId);
	// 						$loadBalanceParam['user_id_deb']   = (int)$usersId;
	// 						$loadBalanceParam['user_id_cred']  = (int)0;
	// 						$loadBalanceParam['order_id']      = $result['order_id'];
	// 						$loadBalanceParam['upoints']       = (float)$totalPrice;
	// 						$loadBalanceParam['availableArabianPoints'] = (float)$userData['availableArabianPoints'];
	// 						$loadBalanceParam['end_balance']   = (float)$userData['availableArabianPoints'] - (float)$totalPrice;
	// 						$loadBalanceParam['record_type']   = 'Debit';
	// 						$loadBalanceParam['narration']     = 'Hourly Game Order';
	// 						$loadBalanceParam['remarks']       = 'Order ID: '.$result['order_id'];
	// 						$loadBalanceParam['created_at']    = date('Y-m-d H:i:s');
	// 						$loadBalanceParam['created_by']    = (int)$usersId;
	// 						$loadBalanceParam['status']        = 'A';	
	// 						// $result2 = $this->common_model->addData('uw_loadBalance',$loadBalanceParam);
	// 						$result2 = $this->mongodb_client->insertDocument('uw_loadBalance',$loadBalanceParam, $session);


	// 						$commission_percentage = $userData['hourly_games_commission_percentage']?$userData['hourly_games_commission_percentage']:10;
	// 						$commission_amount     = ($totalPrice * $commission_percentage) / 100;
	// 						$availableArabianPoints = (float)$userData['availableArabianPoints'] - (float)$totalPrice;
							
	// 						if($commission_amount > 0 && $userData['users_type'] != "Users"):
	// 							$commissionParam['users_id']      = (int)$usersId;
	// 							$commissionParam['user_oid']     = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
	// 							$commissionParam['load_balance_id'] = $this->common_model->getNextSequence('loadBalance');
	// 							$commissionParam['order_oid']     = new MongoDB\BSON\ObjectID($result['_id']->{'$id'});
	// 							$commissionParam['product_oid']   = new MongoDB\BSON\ObjectID($productsId);
	// 							$commissionParam['user_id_deb']   = (int)0;
	// 							$commissionParam['user_id_cred']  = (int)$usersId;
	// 							$commissionParam['order_id']      = $result['order_id'];
	// 							$commissionParam['upoints']       = (float)$commission_amount;
	// 							$commissionParam['availableArabianPoints'] = (float)$loadBalanceParam['end_balance'];
	// 							$commissionParam['end_balance']   = (float)$loadBalanceParam['end_balance']+(float)$commission_amount;
	// 							$commissionParam['record_type']   = 'Credit';
	// 							$commissionParam['narration']     = 'Hourly Game Commission';
	// 							$commissionParam['remarks']       = 'Order ID: '.$result['order_id'];
	// 							$commissionParam['created_at']    = date('Y-m-d H:i:s');
	// 							$commissionParam['created_by']    = (int)$usersId;
	// 							$commissionParam['status']        = 'A';	
	// 							// $result3                          = $this->common_model->addData('uw_loadBalance',$commissionParam);
	// 							$result3 = $this->mongodb_client->insertDocument('uw_loadBalance',$commissionParam, $session);
	// 							$availableArabianPoints = ((float)$userData['availableArabianPoints'] - (float)$totalPrice ) + (float)$commission_amount;
	// 						endif;
							
	// 						/* Update user availableArabianPoints */
	// 						$userParam['availableArabianPoints'] = (float)$availableArabianPoints;
	// 						$userParam['updated_at']             = date('Y-m-d H:i:s');
	// 						$userParam['updated_by']             = (int)$usersId;
	// 						// $this->common_model->editData('uw_users',$userParam,'_id', new MongoDB\BSON\ObjectID($userData['_id']->{'$id'}));
	// 						$this->mongodb_client->updateDocument('uw_users',['_id' => new MongoDB\BSON\ObjectID($userData['_id']->{'$id'})],['$set' => $userParam], $session);

	// 						$session->commitTransaction();
	// 						$this->mongodb_client->commitTransaction($session);

	// 						$message = "";
	// 						if(!empty($tickets) && ( ( !empty($buyerCountryCode) && !empty($buyerMobile) ) || !empty($buyerEmail) )  ):
	// 							$output        = [];
	// 							$CouponDetails = '';
	// 							$map = ['S', 'R', 'C'];
	// 							foreach ($tickets as $key => $tickectitem) {
	// 								// Ticket numbers
	// 								$line = $tickectitem['ticket'];
	// 								$line .= ' (';
	// 								if($tickectitem['type'] == "straight"):
	// 									$line .= 'S';
	// 								elseif($tickectitem['type'] == "rumble"):
	// 									$line .= 'R';
	// 								elseif($tickectitem['type'] == "chance"):
	// 									$line .= 'C';
	// 								endif;
	// 								$line .= ')';
									 
	// 								$output[] = $line;
	// 							}

	// 							$CouponDetails = implode('. ', $output);
								
	// 							if(!empty($$drawTimeTs)):
	// 								$drawDate = date('d.m.Y h:iA', $$drawTimeTs);
	// 							else:
	// 								$drawDate = date('d.m.Y h:iA', $gameData['draw_time']);
	// 							endif;
								
	// 							$drawDate = date('d.m.Y h:iA', $gameData['draw_time']);
	// 							$message = 'Order ID '.$result['order_id'].' of '.$gameData['title'].' with coupons '.$CouponDetails.' Ddate '.$drawDate.' You can download the invoice here https://tktinvoice.com/uwin-download-invoice/'.$result['order_id'];

	// 							if(!empty($buyerCountryCode) && !empty($buyerMobile) && !empty($message) && $smsType == "SMS"):

	// 								$enableSmsFields   = ['default_sms'];
	// 								$enableTblName     = 'uw_enablesms';
	// 								$enableSMSData     = $this->common_model->getSingleDataByParticularField($enableSmsFields,$enableTblName, 'status', 'A');
	// 								$defaultSMSGateway = $enableSMSData['default_sms'];
									
	// 								$senderDetails['gateway']       = $defaultSMSGateway;
	// 								$senderDetails['users_mobile']  = $buyerMobile;
	// 								$senderDetails['country_code']  = $buyerCountryCode;
	// 								$senderDetails['message']       = $message;
	// 								$this->sms_model->sendSMS($senderDetails);

	// 							elseif(!empty($buyerCountryCode) && !empty($buyerMobile) && !empty($message) && $smsType == "WhatsApp"):
	// 								$senderDetails['country_code']  = $buyerCountryCode;
	// 								$senderDetails['users_mobile']  = $buyerMobile;
	// 								$senderDetails['message']       = $message;
	// 								$senderDetails['ORDERID']       = $result["order_id"];
	// 								$senderDetails['CAMPAIGNAME']   = $gameData["title"];
	// 								$senderDetails['CouponDetails'] = $CouponDetails;
	// 								$senderDetails['DDATE']         = $drawDate;
	// 								$senderDetails['LINK']          = 'https://tktinvoice.com/uwin-download-invoice/'.$result["order_id"];
	// 								// $senderDetails['LINK']          = 'https://staging.u-winn.net/uwin-download-invoice/'.$result["order_id"];
	// 								$this->sms_model->sendWhatsAppMessage($senderDetails);
	// 							elseif(!empty($buyerEmail) && !empty($message) && $smsType == "EMAIL"):
	// 								$subject = "Order Confirmation";
	// 								$this->emailsendgrid_model->sendEmail($buyerEmail,$subject,$message);
	// 							endif;	
	// 						endif;

	// 						echo outPut(1, lang('SUCCESS_CODE'), lang('SUCCESS_ACTION'), $result);
	// 					endif;
	// 				endif;
					
	// 			endif;

	// 		else:
	// 			throw new Exception(lang('FORBIDDEN_MSG'),1);
	// 		endif;
	// 	} catch (Exception $e) {
	// 		echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);	
	// 	}
	// }
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
				$modes      = json_decode($this->input->post('modes'), true);
				$tickets    = json_decode($this->input->post('tickets'), true);
				$smsType 	      = $this->input->post('otp_sent');
				$buyerCountryCode = $this->input->post('buyer_country_code');
				$buyerMobile 	  = $this->input->post('buyer_mobile');
				$buyerEmail 	  = $this->input->post('buyer_email');
				$isCouponsRequired = $this->input->post('is_coupons_required');

				// optional fields
				$deliveryAddress = $this->input->post('delivery_address');
				$deliveryCharge  = $this->input->post('delivery_charge');
				$usersAddress    = $this->input->post('users_address');
				$usersLong       = $this->input->post('users_long');
				$usersLat        = $this->input->post('users_lat');
				$appName         = $this->input->post('app_name');
				$appVersion      = $this->input->post('app_version');
				$duplicateCheck  = $this->input->post('duplicate_check');
				$txnID           = $this->input->post('txn_id');
				$is24Hours       = $this->input->post('is_24_hours');
				$drawTime        = $this->input->post('draw_time');
				$drawTimeString  = $this->input->post('draw_time_string');
				$postedDrawTimeString = $drawTimeString;

				$ddd['user_data'] = $_POST;
				$this->common_model->addData('uw_debug',$ddd);

				if(empty($usersId)):
					throw new Exception(lang('USER_ID_EMPTY'), 1);
				elseif(empty($productsId)):
					throw new Exception(lang('PRODUCT_ID_EMPTY'), 1);
				else:
					
					$tblName           = 'uw_users';
					$whereCon['where'] = array('users_id' => (int)$usersId);
					$userData          = $this->common_model->getData('single',$tblName,$whereCon);
					// echo "<pre>";print_r($userData);die();
					
					if(empty($userData)):
						throw new Exception(lang('INVALID_USER'), 1);
					elseif($userData['status'] != "A"):
						throw new Exception(lang('ACCOUNT_BLOCKED'), 1);
					elseif($userData['enable_hourly_games'] == "N"):
						throw new Exception("HOURLY_GAME_DISABLED", 1);
					elseif($is24Hours == "Y" && empty($drawTimeString)):
						throw new Exception(lang('DRAW_TIME_REQUIRED'), 1);
					else:
						
						$this->session->sess_regenerate();
						$session = $this->mongodb_client->client->startSession();
						$session->startTransaction();

						// check game data
						$tblName           = 'uw_hourly_games';
						$whereCon['where'] = array();
						$whereCon['where']['_id']                   = new MongoDB\BSON\ObjectID($productsId);
						
						if($usersId  == 100000000000016 || $usersId  == 100000000001252 || $usersId  == 2010194 || $usersId  == 100000000000514  || $usersId  == 100000000000843):
							$whereCon['where']['is_24_hours']    = "Y";
						else:
							$whereCon['where']['status']         = "A";
						endif;
						$whereCon['where']['expiry_date']           = array('$gt' => strtotime(date('Y-m-d H:i:s')));
						$whereCon['where']['prize_setting']         = "enabled";
						$fieldList  = array();
						$gameData   = $this->common_model->getParticularFieldByMultipleCondition($fieldList,$tblName,$whereCon);

						// commented code --- 02 July 2026 ---
						// if(strtotime('-10 seconds',$gameData['draw_time']) < strtotime(date('Y-m-d H:i:s'))):
						// 	throw new Exception('Campaign expired. Please refresh the page and try again.');
						// endif;

						// if($userData['users_type'] == "Users" && strtotime('-2 minutes',$gameData['draw_time']) < strtotime(date('Y-m-d H:i:s'))):
						// 	throw new Exception('Campaign expired. Please refresh the page and try again.');
						// elseif( $userData['users_type'] != "Users" && strtotime('-10 seconds',$gameData['draw_time']) < strtotime(date('Y-m-d H:i:s'))):
						// 	throw new Exception('Campaign expired. Please refresh the page and try again.');
						// endif;
						// ------------------------------------------------------------

					    // commented code on 20-08-26 to add new 3 min restriction
						if($userData['users_type'] ==  "Users"){
							// Restrict purchasing within last 3 minutes before draw time
							$purchaseCutoffMsg = 'The draw has expired. Please choose a different draw time.';

							if( empty($is24Hours) || $is24Hours != 'Y'):
								$nowTs = strtotime(date('Y-m-d H:i:s'));
								if(strtotime('-3 minutes', $gameData['draw_time']) < $nowTs):
									throw new Exception($purchaseCutoffMsg);
								endif;
							endif;
						} else {
							if( empty($is24Hours) || $is24Hours != 'Y'):
								if(strtotime('-10 seconds',$gameData['draw_time']) < strtotime(date('Y-m-d H:i:s'))):
									throw new Exception('Campaign expired. Please refresh the page and try again.');
								endif;
							
								if($userData['users_type'] == "Users" && strtotime('-2 minutes',$gameData['draw_time']) < strtotime(date('Y-m-d H:i:s'))):
									throw new Exception('Campaign expired. Please refresh the page and try again.');
								elseif( $userData['users_type'] != "Users" && strtotime('-10 seconds',$gameData['draw_time']) < strtotime(date('Y-m-d H:i:s'))):
									throw new Exception('Campaign expired. Please refresh the page and try again.');
								endif;
							endif;
						}
						
						
						if(empty($isCouponsRequired)):
							$isCouponsRequired = "Y";
						endif;
						
						// if(empty($gameData)):
						// 	throw new Exception(lang('DATA_NOT_FOUND'), 1);
						// elseif($userData['availableArabianPoints'] < $gameData['price'] * $qty):
						// 	throw new Exception(lang('LOW_BALANCE'), 1);
						// elseif($gameData['coupon_selection_type'] == 'manual' && empty($tickets)):
						// 	throw new Exception(lang('COUPON_NOT_SELECTED'), 1);
						// else:

						if(empty($gameData)):
							throw new Exception(lang('DATA_NOT_FOUND'), 1);
						elseif($userData['availableArabianPoints'] < $gameData['price'] * $qty):
							throw new Exception(lang('LOW_BALANCE'), 1);
						elseif($gameData['coupon_selection_type'] == 'manual' && empty($tickets) && $isCouponsRequired == "Y"):
							throw new Exception(lang('COUPON_NOT_SELECTED'), 1);
						else:
							

							//Buffering time order duplication check.. START
							$checkWhereCon['where']['users_oid']    = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
							$checkWhereCon['where']['products_oid'] = new MongoDB\BSON\ObjectID($productsId);
							$checkWhereCon['where']['created_at'] =  array(  '$gte' => strtotime(date('Y-m-d H:i:s', strtotime('-10 seconds')))  );
							$checkStartIndex    = 0;
							$checkitemsPerPage  = 1;
							$checkShortField = array('_id' => -1);
							$resultData = $this->common_model->getHourlyGameOrderHistory($checkWhereCon,$checkShortField,$checkitemsPerPage,$checkStartIndex);
							if(!empty($resultData)):
								$resultData = $resultData[0];
								if(!empty($resultData['ticketData'])):
									$PreviewsTickets = array_column($resultData['ticketData'], 'ticket');
									$currentTickets = array_column($tickets, 'ticket');
									if(!array_diff($currentTickets, $PreviewsTickets)):
										echo outPut(1, lang('SUCCESS_CODE'), lang('SUCCESS_ACTION'), $resultData);
										die();
									endif;
								endif;
							endif;

							if(!empty($txnID)):
								$txnWhereCon['where']['users_oid']    = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
								$txnWhereCon['where']['products_oid'] = new MongoDB\BSON\ObjectID($productsId);
								$txnWhereCon['where']['txn_id']       = $txnID;
								$txnShortField = array('_id' => -1);
								$duplicateOrderData = $this->common_model->getData('single','uw_hourly_orders',$txnWhereCon,$txnShortField);
								if(!empty($duplicateOrderData)):
									echo outPut(1, lang('SUCCESS_CODE'), lang('ALREADY_ORDER_PLACED'), $duplicateOrderData);
									die();
								endif;
							endif;

							//Buffering time order duplication check.. END

							$currentTime = strtotime(date('H:00:00'));

							A :
							$status = $isCouponsRequired == "Y" ? "A" : "INI";
							$drawTime = $is24Hours == "Y" ? $drawTimeString : $gameData['draw_time'];
							
							$currentDate = strtotime(date('Y-m-d H:i:s'));
							$drawTi      = date('Y-m-d 17:00:00');
							if(!empty($drawTimeString)):
								$drawTimeTs     = strtotime($drawTimeString);
							elseif(empty($is24Hours) && $currentDate <= strtotime($drawTi)):
								$drawTimeTs     = strtotime($drawTi);
								$drawTimeString = $drawTi;
							else:
								if($currentTime >= '17:00:00' && $currentTime <= strtotime('23:59:59')):
									$drawTi = date('Y-m-d H:00:00' , strtotime('+1 hour'));
								endif;
								$drawTimeTs     = strtotime($drawTi);
								$drawTimeString = $drawTi;
							endif;

							if(!empty($postedDrawTimeString)):
								$now = time();
								if(empty($drawTimeTs) || $drawTimeTs <= 0):
									throw new Exception(lang('DRAW_TIME_REQUIRED'), 1);
								endif;
								if($userData['users_type'] ==  "Users"){
									// Restrict purchasing within last 3 minutes before selected draw time
									if(strtotime('-3 minutes', $drawTimeTs) < $now):
										throw new Exception('The draw has expired. Please choose a different draw time.');
									endif;
								} else {
									if(strtotime('-10 seconds', $drawTimeTs) < $now):
										throw new Exception('Campaign expired. Please refresh the page and try again.');
									endif;
								}
							endif;

							$orderSeq      = floor((microtime(true) * 1000)).rand(100,999);
							$totalPrice    = $gameData['price'] * $qty;
							$param['order_id']        = "UWINN".$orderSeq;
							$param['users_id']        = (int)$usersId;
							$param['users_oid']       = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
							$param['products_oid']    = new MongoDB\BSON\ObjectID($productsId);
							$param['products_name']   = $gameData['title'];
							$param['start_date']      = $gameData['start_date'];
							$param['expiry_date']     = $gameData['expiry_date'];
							// $param['draw_time']       = $gameData['draw_time'];
							// $param['draw_time_string']= date('Y-m-d H:i:s', $gameData['draw_time']);
							$param['draw_time']       = $drawTimeTs;
							$param['draw_time_string']= $drawTimeString;
							$param['qty']             = (int)$qty;
							$param['total_price']     = (float)$totalPrice;
							$param['sms_type']           = $smsType;
							$param['buyer_country_code'] = $buyerCountryCode;
							$param['buyer_mobile']       = (int)$buyerMobile;
							$param['buyer_email']        = $buyerEmail;
							$param['created_at']      = strtotime(date('Y-m-d H:i:s'));
							$param['created_by']      = (int)$usersId;
							// $param['status']       = 'A';
							$param['is_24_hours']     = $is24Hours?$is24Hours:"N";
							$param['status']          = $status;
							$param['txn_id']          = $txnID;
							// $result = $this->common_model->addData('uw_hourly_orders',$param);
							$result = $this->mongodb_client->insertDocument('uw_hourly_orders',$param, $session);
							if(empty($result)):
								goto A;
							endif;
							// $orderOid = $result['_id']->{'$id'};
							$orderOid = $result['_id'];

							if(!empty($tickets) && $isCouponsRequired == "Y"):
								foreach($tickets as $index => $item):
									// $ticketParam['order_oid']       = new MongoDB\BSON\ObjectID($orderOid);
									$ticketParam['order_oid']       = $orderOid;
									$ticketParam['users_id']        = (int)$usersId;
									$ticketParam['users_oid']       = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
									$ticketParam['products_oid']    = new MongoDB\BSON\ObjectID($productsId);
									$ticketParam['products_name']   = $gameData['title'];
									$ticketParam['start_date']      = $gameData['start_date'];
									$ticketParam['expiry_date']     = $gameData['expiry_date'];
									$ticketParam['type']   			= $item['type'];
									$ticketParam['ticket'] 			= $item['ticket'];
									$ticketParam['points']          = $item['points'];
									$ticketParam['created_at']      = strtotime(date('Y-m-d H:i:s'));
									$ticketParam['created_by']      = (int)$usersId;
									$ticketParam['status']          = 'A';
									// $resultTicket = $this->common_model->addData('uw_hourly_tickets',$ticketParam);
									$resultTicket = $this->mongodb_client->insertDocument('uw_hourly_tickets',$ticketParam, $session);

								endforeach;
							endif;

							$loadBalanceParam['users_id']      = (int)$usersId;
							$loadBalanceParam['user_oid']     = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
							$loadBalanceParam['load_balance_id'] = $this->common_model->getNextSequence('loadBalance');
							// $loadBalanceParam['order_oid']     = new MongoDB\BSON\ObjectID($result['_id']->{'$id'});
							$loadBalanceParam['order_oid']     = $result['_id'];
							$loadBalanceParam['product_oid']   = new MongoDB\BSON\ObjectID($productsId);
							$loadBalanceParam['user_id_deb']   = (int)$usersId;
							$loadBalanceParam['user_id_cred']  = (int)0;
							$loadBalanceParam['order_id']      = $result['order_id'];
							$loadBalanceParam['upoints']       = (float)$totalPrice;
							$loadBalanceParam['availableArabianPoints'] = (float)$userData['availableArabianPoints'];
							$loadBalanceParam['end_balance']   = (float)$userData['availableArabianPoints'] - (float)$totalPrice;
							$loadBalanceParam['record_type']   = 'Debit';
							$loadBalanceParam['narration']     = 'Hourly Game Order';
							$loadBalanceParam['remarks']       = 'Order ID: '.$result['order_id'];
							$loadBalanceParam['created_at']    = date('Y-m-d H:i:s');
							$loadBalanceParam['created_by']    = (int)$usersId;
							$loadBalanceParam['status']        = 'A';	
							// $result2 = $this->common_model->addData('uw_loadBalance',$loadBalanceParam);
							$result2 = $this->mongodb_client->insertDocument('uw_loadBalance',$loadBalanceParam, $session);


							$commission_percentage = $userData['hourly_games_commission_percentage']?$userData['hourly_games_commission_percentage']:10;
							$commission_amount     = ($totalPrice * $commission_percentage) / 100;
							$availableArabianPoints = (float)$userData['availableArabianPoints'] - (float)$totalPrice;
							
							if($commission_amount > 0 && $userData['users_type'] != "Users"):
								$commissionParam['users_id']      = (int)$usersId;
								$commissionParam['user_oid']     = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
								$commissionParam['load_balance_id'] = $this->common_model->getNextSequence('loadBalance');
								// $commissionParam['order_oid']     = new MongoDB\BSON\ObjectID($result['_id']->{'$id'});
								$commissionParam['order_oid'] 	  = $result['_id'];
								$commissionParam['product_oid']   = new MongoDB\BSON\ObjectID($productsId);
								$commissionParam['user_id_deb']   = (int)0;
								$commissionParam['user_id_cred']  = (int)$usersId;
								$commissionParam['order_id']      = $result['order_id'];
								$commissionParam['upoints']       = (float)$commission_amount;
								$commissionParam['availableArabianPoints'] = (float)$loadBalanceParam['end_balance'];
								$commissionParam['end_balance']   = (float)$loadBalanceParam['end_balance']+(float)$commission_amount;
								$commissionParam['record_type']   = 'Credit';
								$commissionParam['narration']     = 'Hourly Game Commission';
								$commissionParam['remarks']       = 'Order ID: '.$result['order_id'];
								$commissionParam['created_at']    = date('Y-m-d H:i:s');
								$commissionParam['created_by']    = (int)$usersId;
								$commissionParam['status']        = 'A';	
								// $result3                          = $this->common_model->addData('uw_loadBalance',$commissionParam);
								$result3 = $this->mongodb_client->insertDocument('uw_loadBalance',$commissionParam, $session);
								$availableArabianPoints = ((float)$userData['availableArabianPoints'] - (float)$totalPrice ) + (float)$commission_amount;
							endif;
							
							/* Update user availableArabianPoints */
							$userParam['availableArabianPoints'] = (float)$availableArabianPoints;
							$userParam['updated_at']             = date('Y-m-d H:i:s');
							$userParam['updated_by']             = (int)$usersId;
							// $this->common_model->editData('uw_users',$userParam,'_id', new MongoDB\BSON\ObjectID($userData['_id']->{'$id'}));
							$this->mongodb_client->updateDocument('uw_users',['_id' => new MongoDB\BSON\ObjectID($userData['_id']->{'$id'})],['$set' => $userParam], $session);

							$session->commitTransaction();
							$this->mongodb_client->commitTransaction($session);

							$message = "";
							if(!empty($tickets) && ( ( !empty($buyerCountryCode) && !empty($buyerMobile) ) || !empty($buyerEmail) )  ):
								$output        = [];
								$CouponDetails = '';
								$map = ['S', 'R', 'C'];
								foreach ($tickets as $key => $tickectitem) {
									// Ticket numbers
									$line = $tickectitem['ticket'];
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
								}

								$CouponDetails = implode('. ', $output);

								if(!empty($drawTimeTs)):
									$drawDate = date('d.m.Y h:iA', $drawTimeTs);
								else:
									$drawDate = date('d.m.Y h:iA', $gameData['draw_time']);
								endif;

								$message = 'Order ID '.$result['order_id'].' of '.$gameData['title'].' with coupons '.$CouponDetails.' Ddate '.$drawDate.' You can download the invoice here https://tktinvoice.com/uwin-download-invoice/'.$result['order_id'];

								if(!empty($buyerCountryCode) && !empty($buyerMobile) && !empty($message) && $smsType == "SMS"):

									$enableSmsFields   = ['default_sms'];
									$enableTblName     = 'uw_enablesms';
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
									$senderDetails['LINK']          = 'https://tktinvoice.com/uwin-download-invoice/'.$result["order_id"];
									// $senderDetails['LINK']          = 'https://staging.u-winn.net/uwin-download-invoice/'.$result["order_id"];
									$this->sms_model->sendWhatsAppMessage($senderDetails);
								elseif(!empty($buyerEmail) && !empty($message) && $smsType == "EMAIL"):
									$subject = "Order Confirmation";
									$this->emailsendgrid_model->sendEmail($buyerEmail,$subject,$message);
								endif;	
							endif;

							if (isset($result['_id']) && $result['_id'] instanceof MongoDB\BSON\ObjectId) {
							    $result['_id'] = [
							        '$id' => (string)$result['_id']
							    ];
							}
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

	/* * *********************************************************************
	 * * Function name : updateOrder
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to update order details.
	 * * Date 		   : 20 May 2026
	 * * **********************************************************************/
	public function updateOrder()
	{
		$apiHeaderData = getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 	   = array();
		try {
			if(requestAuthenticate(APIKEY,'POST')):
				$usersId    = $this->input->post('users_id');
				$productsId = $this->input->post('products_id');
				$orderOId   = $this->input->post('order_oid');
				$modes      = json_decode($this->input->post('modes'), true);
				$tickets    = json_decode($this->input->post('tickets'), true);

				if(empty($usersId)):
					throw new Exception(lang('USER_ID_EMPTY'), 1);
				elseif(empty($productsId)):
					throw new Exception(lang('PRODUCT_ID_EMPTY'), 1);
				else:
					
					$tblName           = 'uw_users';
					$whereCon['where'] = array('users_id' => (int)$usersId);
					$userData          = $this->common_model->getData('single',$tblName,$whereCon);
					// echo "<pre>";print_r($userData);die();
					
					if(empty($userData)):
						throw new Exception(lang('INVALID_USER'), 1);
					elseif($userData['status'] != "A"):
						throw new Exception(lang('ACCOUNT_BLOCKED'), 1);
					elseif($userData['enable_hourly_games'] == "N"):
						throw new Exception("HOURLY_GAME_DISABLED", 1);
					else:
						
						// $this->session->sess_regenerate();
						// $session = $this->mongodb_client->client->startSession();
						// $session->startTransaction();

						// check game data
						$tblName           = 'uw_hourly_games';
						$whereCon['where'] = array();
						$whereCon['where']['_id']                   = new MongoDB\BSON\ObjectID($productsId);
						$whereCon['where']['status']                = "A";
						$whereCon['where']['expiry_date']           = array('$gt' => strtotime(date('Y-m-d H:i:s')));
						$whereCon['where']['prize_setting']         = "enabled";
						$fieldList  = array();
						$gameData   = $this->common_model->getParticularFieldByMultipleCondition($fieldList,$tblName,$whereCon);
						// $gameData = $this->mongodb_client->getDocument($tblName,$whereCon,$fieldList,$session);
						 
						if(empty($isCouponsRequired)):
							$isCouponsRequired = "Y";
						endif;
						
						if(empty($gameData)):
							throw new Exception(lang('DATA_NOT_FOUND'), 1);
						elseif($userData['availableArabianPoints'] < $gameData['price'] * $qty):
							throw new Exception(lang('LOW_BALANCE'), 1);
						elseif($gameData['coupon_selection_type'] == 'manual' && empty($tickets) && $isCouponsRequired == "Y"):
							throw new Exception(lang('COUPON_NOT_SELECTED'), 1);
						else:

							$tblName = 'uw_hourly_orders';
							$whereCon['where'] = array();
							$whereCon['where']['_id'] = new MongoDB\BSON\ObjectID($orderOId);
							$result  = $this->common_model->getData('single',$tblName,$whereCon);
							// $result  = $this->mongodb_client->getDocument('single',$tblName,$whereCon,$session);
							$orderOid = $result['_id']->{'$id'};
							$qty      = $result['qty'];
							$totalQty = count($tickets);
							if($totalQty == $qty && $result['status'] == 'INI'):

								//Updating status in order table
								$param['status']      = 'A';
								$param['update_ip']   = currentIp();
								$param['update_date'] = (int)$this->timezone->utc_time();//currentDateTime();
								$param['updated_by']  = (int)$usersId;
								$update1 = $this->common_model->editData($tblName ,$param,'_id', new MongoDB\BSON\ObjectID($orderOid));
								// $orderWhereCon = array('_id' => new MongoDB\BSON\ObjectId($orderOid));
								// $update1 = $this->mongodb_client->updateDocument('uw_hourly_orders',$orderWhereCon, ['$set' => $param],$session);

								if(!empty($tickets) && $isCouponsRequired == "Y"):
									foreach($tickets as $index => $item):
										$ticketParam['order_oid']       = new MongoDB\BSON\ObjectID($orderOid);
										$ticketParam['users_id']        = (int)$usersId;
										$ticketParam['users_oid']       = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
										$ticketParam['products_oid']    = new MongoDB\BSON\ObjectID($productsId);
										$ticketParam['products_name']   = $gameData['title'];
										$ticketParam['start_date']      = $gameData['start_date'];
										$ticketParam['expiry_date']     = $gameData['expiry_date'];
										$ticketParam['type']   			= $item['type'];
										$ticketParam['ticket'] 			= $item['ticket'];
										$ticketParam['points']          = $item['points'];
										$ticketParam['created_at']      = strtotime(date('Y-m-d H:i:s'));
										$ticketParam['created_by']      = (int)$usersId;
										$ticketParam['status']          = 'A';
										$resultTicket = $this->common_model->addData('uw_hourly_tickets',$ticketParam);
										// $resultTicket = $this->mongodb_client->insertDocument('uw_hourly_tickets',$ticketParam, $session);
									endforeach;
								endif;

								// $session->commitTransaction();
								// $this->mongodb_client->commitTransaction($session);
								echo outPut(1, lang('SUCCESS_CODE'), lang('SUCCESS_ACTION'), $result);
							else:
								throw new Exception(lang('INVALID_QUANTITY'), 1);
							endif;
							
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


	/* * *********************************************************************
	 * * Function name : cancellationOrder
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to cancellation order details.
	 * * Date 		   : 20 May 2026
	 * * **********************************************************************/
	// public function cancellationOrder()
	// {
	// 	$apiHeaderData = getApiHeaderData();
	// 	$this->generatelogs->putLog('APP',logOutPut($_POST));
	// 	$result 	   = array();
	// 	try {
	// 		if(requestAuthenticate(APIKEY,'POST')):
	// 			$usersId    = $this->input->post('users_id');
	// 			$orderOId   = $this->input->post('order_oid');

	// 			// $this->session->sess_regenerate();
	// 			// $session = $this->mongodb_client->startSession();
	// 			// $session->startTransaction();

	// 			if(empty($usersId)):
	// 				throw new Exception(lang('USER_ID_EMPTY'), 1);
	// 			elseif(empty($orderOId)):
	// 				throw new Exception(lang('ORDER_ID_EMPTY'), 1);
	// 			else:

	// 				$whereCon['where'] = array('users_id' => (int)$usersId);
	// 				$FieldList = array('users_id', 'status', 'availableArabianPoints');
	// 				$UserData   = $this->common_model->getParticularFieldByMultipleCondition($FieldList,'uw_users',$whereCon );

	// 				if(empty($UserData)):
	// 					throw new Exception(lang('USER_NOT_FOUND'), 1);
	// 				elseif($UserData['status'] != 'A'):
	// 					throw new Exception(lang('INVALID_USER'), 1);
	// 				else:

	// 					$tblName = 'uw_hourly_orders';
	// 					$whereCon['where'] = array();
	// 					$whereCon['where']['_id'] = new MongoDB\BSON\ObjectID($orderOId);
	// 					$orderDetails = $this->common_model->getData('single',$tblName,$whereCon);

	// 					$currentDateTime = date('Y-m-d H:i:s');
	// 					$currentDateTime = strtotime($currentDateTime);
						
	// 					if(empty($orderDetails)):
	// 						throw new Exception(lang('DATA_NOT_FOUND'), 1);
	// 					elseif($orderDetails['status'] == 'CL'):
	// 						throw new Exception(lang('ORDER_ALREADY_CANCELLED'), 1);
	// 					elseif($currentDateTime >= $orderDetails['expiry_date'] ):
	// 						throw new Exception(lang('CANNOT_CANCEL_ORDER'), 1);
	// 					elseif($orderDetails['status'] == 'A'):

	// 						/* updated order status */
	// 						$param1['status']		= 'CL';
	// 						$param1['update_ip']	= currentIp();
	// 						$param1['update_date']  = (int)$this->timezone->utc_time();//currentDateTime();
	// 						$param1['refund_date']	= (int)$this->timezone->utc_time();//currentDateTime();
	// 						$param1['updated_by']	= (int)$usersId;
	// 						$param1['cancel_reason']= 'android api Hourly Game';
	// 						$orderWhereCon          = array('_id' => new MongoDB\BSON\ObjectId($orderOId));
	// 						$update1 = $this->common_model->editData('uw_hourly_orders',$param1,'_id', new MongoDB\BSON\ObjectID($orderOId));
	// 						$update2 = $this->common_model->editMultipleDataByMultipleCondition('uw_hourly_tickets', $param1, $orderWhereCon);
	// 						// $update1 = $this->mongodb_client->updateDocument('uw_hourly_orders',$orderWhereCon, ['$set' => $param1],$session);
	// 						// $update2 = $this->mongodb_client->updateDocument('uw_hourly_tickets',$orderWhereCon, ['$set' => $param1],$session);

	// 						/* Generating order cancellation record in loadbalance */
	// 						$loadBalanceParam['users_id']        = (int)$usersId;
	// 						$loadBalanceParam['user_oid']        = new MongoDB\BSON\ObjectID($UserData['_id']['$id']);
	// 						$loadBalanceParam['load_balance_id'] = $this->common_model->getNextSequence('loadBalance');
	// 						$loadBalanceParam['order_oid']     = new MongoDB\BSON\ObjectID($orderOId);
	// 						$loadBalanceParam['product_oid']   = new MongoDB\BSON\ObjectID($orderDetails['products_oid']);
	// 						$loadBalanceParam['user_id_deb']   = (int)0;
	// 						$loadBalanceParam['user_id_cred']  = (int)$usersId;
	// 						$loadBalanceParam['order_id']      = $orderDetails['order_id'];
	// 						$loadBalanceParam['upoints']       = (float)$orderDetails['total_price'];
	// 						$loadBalanceParam['availableArabianPoints'] = (float)$UserData['availableArabianPoints'];
	// 						$loadBalanceParam['end_balance']   = (float)$UserData['availableArabianPoints'] + (float)$orderDetails['total_price'];
	// 						$loadBalanceParam['record_type']   = 'Credit';
	// 						$loadBalanceParam['narration']     = 'Hourly Game Order Cancelled';
	// 						$loadBalanceParam['remarks']       = 'Order ID: '.$orderDetails['order_id'];
	// 						$loadBalanceParam['created_at']    = date('Y-m-d H:i:s');
	// 						$loadBalanceParam['created_by']    = (int)$usersId;
	// 						$loadBalanceParam['status']        = 'A';	
	// 						// echo "<pre>";print_r($loadBalanceParam);die();
	// 						$update2 = $this->common_model->addData('uw_loadBalance',$loadBalanceParam);
	// 						// $update2 = $this->mongodb_client->insertDocument('uw_loadBalance', $loadBalanceParam, $session);
							
	// 						/* Balance Updated.. */
	// 						$updateBalance['availableArabianPoints'] = (float)$loadBalanceParam['end_balance'];
	// 						$update3 = $this->common_model->editData('uw_users',$updateBalance,'users_id',(int)$usersId);
	// 						// $update3 = $this->mongodb_client->updateDocument(
	// 						// 	'uw_users',         				// Collection name
	// 						// 	['users_id' => (int)$usersId],       // Filter / condition for which document to update
	// 						// 	['$set' => $updateBalance],         // Proper MongoDB update syntax
	// 						// 	$session                            // MongoDB session (optional)
	// 						// );

	// 						// $session->commitTransaction();
	// 						// $this->mongodb_client->commitTransaction($session);
	// 						echo outPut(1,lang('SUCCESS_CODE'),lang('ORDER_CANCELLED_SUCCESSFULLY'),$result);
	// 					endif;
	// 				endif;
	// 			endif;
	// 		else:
	// 			throw new Exception(lang('FORBIDDEN_MSG'),1);
	// 		endif;
	// 	} catch (Exception $e) {
	// 		echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);	
	// 	}
	// }
	public function cancellationOrder()
	{
		$apiHeaderData = getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 	   = array();
		try {
			if(requestAuthenticate(APIKEY,'POST')):
				$usersId    = $this->input->post('users_id');
				$orderOId   = trim((string)$this->input->post('order_oid'));

				if(empty($usersId)):
					throw new Exception(lang('USER_ID_EMPTY'), 1);
				elseif(empty($orderOId)):
					throw new Exception(lang('ORDER_ID_EMPTY'), 1);
				elseif(!preg_match('/^[a-f0-9]{24}$/i', $orderOId)):
					throw new Exception(lang('ORDET_ID_INVALID'), 1);
				else:

					$whereCon['where'] = array('users_id' => (int)$usersId);
					$FieldList = array('users_id', 'status', 'availableArabianPoints', '_id');
					$UserData   = $this->common_model->getParticularFieldByMultipleCondition($FieldList,'uw_users',$whereCon );

					if(empty($UserData)):
						throw new Exception(lang('USER_NOT_FOUND'), 1);
					elseif($UserData['status'] != 'A'):
						throw new Exception(lang('INVALID_USER'), 1);
					else:

						$tblName = 'uw_hourly_orders';
						$whereCon['where'] = array(
							'_id' => new MongoDB\BSON\ObjectID($orderOId),
							'users_id' => (int)$usersId
						);
						$orderDetails = $this->common_model->getData('single',$tblName,$whereCon);

						$currentDateTime = strtotime(date('Y-m-d H:i:s'));
						
						if(empty($orderDetails)):
							throw new Exception(lang('DATA_NOT_FOUND'), 1);
						elseif($orderDetails['status'] == 'CL'):
							throw new Exception(lang('ORDER_ALREADY_CANCELLED'), 1);
						elseif($currentDateTime >= (int)$orderDetails['expiry_date']):
							throw new Exception(lang('CANNOT_CANCEL_ORDER'), 1);
						elseif(in_array($orderDetails['status'], array('A', 'INI'), true)):

							$userOid = '';
							if (isset($UserData['_id']['$id'])) {
								$userOid = (string)$UserData['_id']['$id'];
							} elseif (isset($UserData['_id']['$oid'])) {
								$userOid = (string)$UserData['_id']['$oid'];
							} elseif (is_string($UserData['_id'])) {
								$userOid = $UserData['_id'];
							}
							if (empty($userOid)) {
								throw new Exception(lang('USER_NOT_FOUND'), 1);
							}

							$productOid = $orderDetails['products_oid'];
							if (is_object($productOid) && method_exists($productOid, '__toString')) {
								$productOid = (string)$productOid;
							} elseif (is_array($productOid)) {
								$productOid = isset($productOid['$id']) ? $productOid['$id'] : (isset($productOid['$oid']) ? $productOid['$oid'] : '');
							}

							$refundAmount = (float)$orderDetails['total_price'];
							$openingBalance = (float)$UserData['availableArabianPoints'];
							$endBalance = $openingBalance + $refundAmount;

							/* updated order status */
							$param1['status']		= 'CL';
							$param1['update_ip']	= currentIp();
							$param1['update_date']  = (int)$this->timezone->utc_time();
							$param1['refund_date']	= (int)$this->timezone->utc_time();
							$param1['updated_by']	= (int)$usersId;
							$param1['cancel_reason']= 'android api Hourly Game';
							$orderWhereCon          = array('_id' => new MongoDB\BSON\ObjectId($orderOId));
							$this->common_model->editData('uw_hourly_orders',$param1,'_id', new MongoDB\BSON\ObjectID($orderOId));
							$this->common_model->editMultipleDataByMultipleCondition('uw_hourly_tickets', $param1, $orderWhereCon);

							/* Generating order cancellation record in loadbalance */
							$loadBalanceParam['users_id']        = (int)$usersId;
							$loadBalanceParam['user_oid']        = new MongoDB\BSON\ObjectID($userOid);
							$loadBalanceParam['load_balance_id'] = $this->common_model->getNextSequence('loadBalance');
							$loadBalanceParam['order_oid']     = new MongoDB\BSON\ObjectID($orderOId);
							$loadBalanceParam['product_oid']   = new MongoDB\BSON\ObjectID($productOid);
							$loadBalanceParam['user_id_deb']   = (int)0;
							$loadBalanceParam['user_id_cred']  = (int)$usersId;
							$loadBalanceParam['order_id']      = $orderDetails['order_id'];
							$loadBalanceParam['upoints']       = $refundAmount;
							$loadBalanceParam['availableArabianPoints'] = $openingBalance;
							$loadBalanceParam['end_balance']   = $endBalance;
							$loadBalanceParam['record_type']   = 'Credit';
							$loadBalanceParam['narration']     = 'Hourly Game Order Cancelled';
							$loadBalanceParam['remarks']       = 'Order ID: '.$orderDetails['order_id'];
							$loadBalanceParam['created_at']    = date('Y-m-d H:i:s');
							$loadBalanceParam['created_by']    = (int)$usersId;
							$loadBalanceParam['status']        = 'A';
							$this->common_model->addData('uw_loadBalance',$loadBalanceParam);
							
							/* Balance Updated.. */
							$updateBalance['availableArabianPoints'] = $endBalance;
							$this->common_model->editData('uw_users',$updateBalance,'users_id',(int)$usersId);

							echo outPut(1,lang('SUCCESS_CODE'),lang('ORDER_CANCELLED_SUCCESSFULLY'),$result);
							die();
						else:
							throw new Exception(lang('CANNOT_CANCEL_ORDER'), 1);
						endif;
					endif;
				endif;
			else:
				throw new Exception(lang('FORBIDDEN_MSG'),1);
			endif;
		} catch (\Throwable $e) {
			echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);
			die();
		}
	}

	/* * *********************************************************************
	 * * Function name : OrderHistory
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : Get the order history for the lotto game tickets
	 * * Date 		   : 5 Feb 2026
	 * * **********************************************************************/
	public function OrderHistory()
	{
		$apiHeaderData = getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_GET));
		$result 	   = array();
		try {
			if(requestAuthenticate(APIKEY,'POST')):
				
				$usersId      = $this->input->post('users_id');
				$startDate    = $this->input->post('start_date');
				$endDate      = $this->input->post('end_date');

				$searchBy     = $this->input->post('search_by');
				$searchValue  = $this->input->post('search_value');
				$pageno       = $this->input->post('page_no');
				$itemsPerPage = $this->input->post('itemsPerPage');

				if(empty($usersId)):
					throw new Exception(lang('USER_ID_EMPTY'), 1);
				elseif(empty($startDate)):
					throw new Exception(lang('EMPTY_START_DATE'), 1);
				elseif(empty($endDate)):
					throw new Exception(lang('EMPTY_END_DATE'), 1);
				elseif(empty($pageno)):
					throw new Exception(lang('EMPTY_PAGE_NO'), 1);
				elseif(empty($itemsPerPage)):
					throw new Exception(lang('EMPTY_ITEMPERPAGE'), 1);
				else:

					$tblName  = 'uw_users';
					$whereCon['where']['users_id'] = (int)$usersId;
					$userData = $this->common_model->getData('single',$tblName,$whereCon);
					if(empty($userData)):
						throw new Exception(lang('USER_NOT_FOUND'), 1);
					elseif($userData['status'] != "A"):
						throw new Exception(lang('ACCOUNT_BLOCKED'), 1);
					else:

						$startDate = date('Y-m-d H:i:01', strtotime($startDate));
						$endDate = date('Y-m-d H:i:59', strtotime($endDate));

						$tblName  = 'uw_hourly_orders';
						$whereCon = array();
						$whereCon['where']['users_oid'] = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
						$whereCon['where']['created_at'] = array(
							'$gte' => strtotime($startDate),
							'$lte' => strtotime($endDate)
						);

						if(!empty($searchBy) && !empty($searchValue)):
							$whereCon['where'][$searchBy] = is_numeric($searchValue) ? (int)$searchValue : $searchValue;
						endif;
						$totalcount = $this->common_model->getData('count',$tblName,$whereCon);

						 
						// Current page number (received from URL query parameter, e.g., ?page=2)
						$page = isset($pageno) ? (int)$pageno : 1;
						// Calculate total number of pages
						$totalPages = ceil($totalcount / $itemsPerPage);
						$totalpage= array();
						// Pagination links
						for ($i = 1; $i <= $totalPages; $i++) {
							if ($i == $page) {
								$current_page = $i;
								$totalpage[] = $i;
							} else {
								$totalpage[] = $i;
							}
						}

						$startIndex = ($page - 1) * $itemsPerPage;
						$shortField = array('_id' => -1);
						$resultData = $this->common_model->getHourlyGameOrderHistory($whereCon,$shortField,$itemsPerPage,$startIndex);
						$currentDateTime = strtotime(date('Y-m-d H:i:s'));
						if(!empty($resultData)):
							foreach($resultData as $key => $item):
								if($currentDateTime < $item['draw_time']):
									$resultData[$key]['winning_status'] =  str_replace('###DRAWTIME###', date('h:i A', $item['draw_time']), lang('HOURLY_GAME_DRAW_ALERT'));
								elseif($currentDateTime >= $item['draw_time'] && $item['is_winner'] != "Y"):
									$resultData[$key]['winning_status'] =  lang('NOT_WINNER');
								elseif($item['is_winner'] == "Y"):
									$resultData[$key]['winning_status'] = "Won";
								endif;
								$resultData[$key]['draw'] = $item['winning_details']??[];
							endforeach;
						endif;
						
						if(!empty($resultData)):
							$result['total_count']  = $totalcount;
							$result['current_page'] = $page;
							$result['total_pages']  = ceil($totalcount / $itemsPerPage);
							$result['PageData']     = $resultData;
							echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$result);die();
						else:
							echo outPut(0,lang('FORBIDDEN_CODE'),lang('DATA_NOT_FOUND'),$result);die();
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
	
	// public function OrderHistory()
	// {
	// 	$apiHeaderData = getApiHeaderData();
	// 	$this->generatelogs->putLog('APP',logOutPut($_GET));
	// 	$result 	   = array();
	// 	try {
	// 		if(requestAuthenticate(APIKEY,'POST')):
				
	// 			$usersId      = $this->input->post('users_id');
	// 			$startDate    = $this->input->post('start_date');
	// 			$endDate      = $this->input->post('end_date');

	// 			$searchBy     = $this->input->post('search_by');
	// 			$searchValue  = $this->input->post('search_value');
	// 			$pageno       = $this->input->post('page_no');
	// 			$itemsPerPage = $this->input->post('itemsPerPage');

	// 			if(empty($usersId)):
	// 				throw new Exception(lang('USER_ID_EMPTY'), 1);
	// 			elseif(empty($startDate)):
	// 				throw new Exception(lang('EMPTY_START_DATE'), 1);
	// 			elseif(empty($endDate)):
	// 				throw new Exception(lang('EMPTY_END_DATE'), 1);
	// 			elseif(empty($pageno)):
	// 				throw new Exception(lang('EMPTY_PAGE_NO'), 1);
	// 			elseif(empty($itemsPerPage)):
	// 				throw new Exception(lang('EMPTY_ITEMPERPAGE'), 1);
	// 			else:

	// 				$tblName  = 'uw_users';
	// 				$whereCon['where']['users_id'] = (int)$usersId;
	// 				$userData = $this->common_model->getData('single',$tblName,$whereCon);
	// 				if(empty($userData)):
	// 					throw new Exception(lang('USER_NOT_FOUND'), 1);
	// 				elseif($userData['status'] != "A"):
	// 					throw new Exception(lang('ACCOUNT_BLOCKED'), 1);
	// 				else:

	// 					$startDate = date('Y-m-d H:i:01', strtotime($startDate));
	// 					$endDate = date('Y-m-d H:i:59', strtotime($endDate));

	// 					$tblName  = 'uw_hourly_orders';
	// 					$whereCon = array();
	// 					$whereCon['where']['users_oid'] = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
	// 					$whereCon['where']['created_at'] = array(
	// 						'$gte' => strtotime($startDate),
	// 						'$lte' => strtotime($endDate)
	// 					);

	// 					if(!empty($searchBy) && !empty($searchValue)):
	// 						$whereCon['where'][$searchBy] = is_numeric($searchValue) ? (int)$searchValue : $searchValue;
	// 					endif;
	// 					$totalcount = $this->common_model->getData('count',$tblName,$whereCon);

						 
	// 					// Current page number (received from URL query parameter, e.g., ?page=2)
	// 					$page = isset($pageno) ? (int)$pageno : 1;
	// 					// Calculate total number of pages
	// 					$totalPages = ceil($totalcount / $itemsPerPage);
	// 					$totalpage= array();
	// 					// Pagination links
	// 					for ($i = 1; $i <= $totalPages; $i++) {
	// 						if ($i == $page) {
	// 							$current_page = $i;
	// 							$totalpage[] = $i;
	// 						} else {
	// 							$totalpage[] = $i;
	// 						}
	// 					}

	// 					$startIndex = ($page - 1) * $itemsPerPage;
	// 					$shortField = array('_id' => -1);
	// 					$resultData = $this->common_model->getHourlyGameOrderHistory($whereCon,$shortField,$itemsPerPage,$startIndex);
	// 					if(!empty($resultData)):
	// 						$result['total_count']  = $totalcount;
	// 						$result['current_page'] = $page;
	// 						$result['total_pages']  = ceil($totalcount / $itemsPerPage);
	// 						$result['PageData']     = $resultData;
	// 						echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$result);die();
	// 					else:
	// 						echo outPut(0,lang('FORBIDDEN_CODE'),lang('DATA_NOT_FOUND'),$result);die();
	// 					endif;
	// 				endif;
					
	// 			endif;
	// 		else:
	// 			throw new Exception(lang('FORBIDDEN_MSG'),1);
	// 		endif;
	// 	} catch (Exception $e) {
	// 		echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);	
	// 	}
	// }

	/* * *********************************************************************
	 * * Function name : OrderRedeem
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : Redeem the order for the tambola game tickets
	 * * Date 		   : 5 Feb 2026
	 * * **********************************************************************/
	public function OrderRedeem()
	{
		$apiHeaderData = getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_GET));
		$result 	   = array();
		try {
			if(requestAuthenticate(APIKEY,'POST')):
				
				$usersId = $this->input->post('users_id');
				$orderId = $this->input->post('order_id');
				$amount  = $this->input->post('amount');

				if(empty($usersId)):
					throw new Exception(lang('USER_ID_EMPTY'), 1);
				elseif(empty($orderId)):
					throw new Exception(lang('EMPTY_ITEMPERPAGE'), 1);
				else:

					$tblName = 'users';
					$whereCon['where']['users_id'] = (int)$usersId;
					$userData = $this->common_model->getData('single',$tblName,$whereCon);
					
					if(empty($userData)):
						throw new Exception(lang('USER_NOT_FOUND'), 1);
					elseif($userData['status'] != "A"):
						throw new Exception(lang('ACCOUNT_BLOCKED'), 1);
					else:
						// Use MongoDB transaction for redeem (update status + insert loadBalance)
						$this->session->sess_regenerate();
						$session = $this->mongodb_client->client->startSession();
						$session->startTransaction();

						$tblName  = 'tambola_orders';
						$whereCon = array();
						$whereCon['where']['order_id'] = $orderId;
						$orderData = $this->common_model->getData('single',$tblName,$whereCon);
						if(empty($orderData)):
							throw new Exception(lang('INVALID_ORDER_ID'), 1);
						elseif($orderData['status'] == "CL"):
							throw new Exception(lang('ORDER_ALREADY_CANCELLED'), 1);
						elseif($orderData['status'] == "REDEEMED"):
							throw new Exception(lang('ALREADY_REDEEM'), 1);
						else:

							$winningAmountList = $this->checkwinningamount($orderData);
							if(!empty($winningAmountList)):
								$result['winning_amount_list'] = $winningAmountList;
								$totalWinningAmount = 0;
								foreach($winningAmountList as $winning):
									$totalWinningAmount += isset($winning['prize']) ? $winning['prize'] : 0;
								endforeach;
								$result['total_winning_amount'] = $totalWinningAmount;
							else:
								$result['winning_amount_list'] = array();
								$result['total_winning_amount'] = 0;
							endif;
							$result['products_name'] = $orderData['products_name'];

							if($totalWinningAmount > 0):
								$redeemlimit = !empty($userData['redeeming_amount_limit'])? $userData['redeeming_amount_limit']:1000;
								if($totalWinningAmount >  $redeemlimit):
									$errorMessage = str_replace('####AMOUNT####', $totalWinningAmount, lang('BIG_WINNER_TEXT'));
									throw new Exception($errorMessage, 1);
								endif;

								$param['status']     = "REDEEMED";  
								$param['updated_at'] = strtotime(date('Y-m-d H:i:s'));
								$param['updated_by'] = (int)$usersId;
								// $this->common_model->editData($tblName,$param,'_id', new MongoDB\BSON\ObjectID($orderData['_id']->{'$id'}));
								$this->mongodb_client->updateDocument($tblName,['_id' => new MongoDB\BSON\ObjectID($orderData['_id']->{'$id'})],['$set' => $param], $session);
								
								$redeemParam = array();
								$redeemParam['load_balance_id'] = (int)$this->geneal_model->getNextSequence('loadBalance');
								$redeemParam['users_oid']   = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
								$redeemParam['order_oid']   = new MongoDB\BSON\ObjectID($orderData['_id']->{'$id'});
								$redeemParam['product_oid'] = new MongoDB\BSON\ObjectID((string)$orderData['products_oid']);
								$redeemParam['seller_oid']  = new MongoDB\BSON\ObjectID((string)$orderData['users_oid']);
								$redeemParam['products_name']= $orderData['products_name'];
								$redeemParam['order_id']    =  $orderId;
								$redeemParam['upoints']     =  (float)$totalWinningAmount;
								$redeemParam['record_type'] = 'Debit';
								$redeemParam['narration']   = 'Tambola Prize Redeemed';
								$redeemParam['remarks']     = 'Prize'.$totalWinningAmount.' AED'.' redeemed for order id '.$orderId;
								$redeemParam['availableArabianPoints'] = (float)$userData['availableArabianPoints'];
								$redeemParam['end_balance']  = (float)$userData['availableArabianPoints'];
								$redeemParam['creation_ip']  = currentIp();
								$redeemParam['created_at']   = strtotime(date('Y-m-d H:i:s'));
								$redeemParam['status']       = "A";
								$redeemParam['created_by']   = (int)$usersId;
								// $this->common_model->addData('loadBalance',$redeemParam);
								$this->mongodb_client->insertDocument('loadBalance',$redeemParam, $session);

								$session->commitTransaction();
								$this->mongodb_client->commitTransaction($session);
								$session->endSession();
								
								echo outPut(1,lang('SUCCESS_CODE'),lang('REDEEMED_SUCCESSFULLY'),$result);die();

							else:
								throw new Exception(lang('NO_WINNING_AMOUNT_FOUND'), 1);
							endif;
							
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

	/* * *********************************************************************
	 * * Function name : OrderRedeemHistory
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : Get the redeem history for the tambola game tickets
	 * * Date 		   : 16 March 2026
	 * * **********************************************************************/
	public function OrderRedeemHistory()
	{
		$apiHeaderData = getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_GET));
		$result 	   = array();
		try {
			if(requestAuthenticate(APIKEY,'POST')):
				$usersId      = $this->input->post('users_id');
				$startDate    = $this->input->post('start_date');
				$endDate      = $this->input->post('end_date');

				$searchBy     = $this->input->post('search_by');
				$searchValue  = $this->input->post('search_value');
				$pageno       = $this->input->post('page_no');
				$itemsPerPage = $this->input->post('itemsPerPage');

				if(empty($usersId)):
					throw new Exception(lang('USER_ID_EMPTY'), 1);
				elseif(empty($startDate)):
					throw new Exception(lang('EMPTY_START_DATE'), 1);
				elseif(empty($endDate)):
					throw new Exception(lang('EMPTY_END_DATE'), 1);
				elseif(empty($pageno)):
					throw new Exception(lang('EMPTY_PAGE_NO'), 1);
				elseif(empty($itemsPerPage)):
					throw new Exception(lang('EMPTY_ITEMPERPAGE'), 1);
				else:

					$tblName = 'users';
					$whereCon['where']['users_id'] = (int)$usersId;
					$userData = $this->common_model->getData('single',$tblName,$whereCon);
					
					if(empty($userData)):
						throw new Exception(lang('USER_NOT_FOUND'), 1);
					elseif($userData['status'] != "A"):
						throw new Exception(lang('ACCOUNT_BLOCKED'), 1);
					else:

						$tblName  = 'loadBalance';
						$whereCon = array();
						$whereCon['where']['narration']  = "Tambola Prize Redeemed";
						$whereCon['where']['users_oid']  = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
						$whereCon['where']['created_at'] = array(
							'$gte' => strtotime(date('Y-m-d H:i:s',strtotime($startDate))),
							'$lte' => strtotime(date('Y-m-d 23:i:s',strtotime($endDate)))
						);

						if(!empty($searchBy) && !empty($searchValue)):
							$whereCon['where'][$searchBy] = is_numeric($searchValue) ? (int)$searchValue : $searchValue;
						endif;

						$totalcount = $this->common_model->getData('count',$tblName,$whereCon);
						 
						// Current page number (received from URL query parameter, e.g., ?page=2)
						$page = isset($pageno) ? (int)$pageno : 1;
						// Calculate total number of pages
						$totalPages = ceil($totalcount / $itemsPerPage);
						$totalpage= array();
						// Pagination links
						for ($i = 1; $i <= $totalPages; $i++) {
							if ($i == $page) {
								$current_page = $i;
								$totalpage[] = $i;
							} else {
								$totalpage[] = $i;
							}
						}

						$startIndex = ($page - 1) * $itemsPerPage;
						$shortField = array('_id' => -1);
						$resultData = $this->common_model->getData('multiple',$tblName,$whereCon,$shortField,$itemsPerPage,$startIndex);
						if(!empty($resultData)):
							$result['total_count']  = $totalcount;
							$result['current_page'] = $page;
							$result['total_pages']  = ceil($totalcount / $itemsPerPage);
							$result['PageData']     = $resultData;
							echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$result);die();
						else:
							echo outPut(0,lang('FORBIDDEN_CODE'),lang('DATA_NOT_FOUND'),$result);die();
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

	private function checkwinningamount($orderData = array())
	{
		$winningAmountList = array();
		
		// Get tickets and winning numbers from order data
		$tickets = isset($orderData['tickets']) ? $orderData['tickets'] : array();
		$winningNumbers = isset($orderData['winning_numbers']) ? $orderData['winning_numbers'] : array();
		
		if(empty($tickets) || empty($winningNumbers)):
			return array();
		endif;
		
		// Loop through each ticket
		foreach($tickets as $ticketIndex => $ticketNumbers):
			// Loop through each number in the ticket
			foreach($ticketNumbers as $ticketNumber):
				// Check if this ticket number matches any winning number
				foreach($winningNumbers as $winningNumberObj):
					// Handle both object and array formats
					$winningNumber = is_object($winningNumberObj) ? $winningNumberObj->numbers : (isset($winningNumberObj['numbers']) ? $winningNumberObj['numbers'] : null);
					$prize = is_object($winningNumberObj) ? $winningNumberObj->prize : (isset($winningNumberObj['prize']) ? $winningNumberObj['prize'] : 0);
					
					if($ticketNumber == $winningNumber):
						// Add to winning amount list
						$winningAmountList[] = array(
							'ticket_index' => $ticketIndex,
							'winning_number' => $winningNumber,
							'prize' => $prize
						);
						break; // Found match, move to next ticket number
					endif;
				endforeach;
			endforeach;
		endforeach;
		
		return $winningAmountList;
	}	

	/* * *********************************************************************
	 * * Function name : checkWinner
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : Check the winner for the tambola game tickets
	 * * Date 		   : 5 Feb 2026
	 * * **********************************************************************/
	public function checkWinner()
	{
		$apiHeaderData = getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_GET));
		$result 	   = array();
		try {
			if(requestAuthenticate(APIKEY,'POST')):
				
				$usersId = $this->input->post('users_id');
				$orderId = $this->input->post('order_id');
				$amount  = $this->input->post('amount');

				if(empty($usersId)):
					throw new Exception(lang('USER_ID_EMPTY'), 1);
				elseif(empty($orderId)):
					throw new Exception(lang('EMPTY_ITEMPERPAGE'), 1);
				else:

					$tblName = 'users';
					$whereCon['where']['users_id'] = (int)$usersId;
					$userData = $this->common_model->getData('single',$tblName,$whereCon);
					
					if(empty($userData)):
						throw new Exception(lang('USER_NOT_FOUND'), 1);
					elseif($userData['status'] != "A"):
						throw new Exception(lang('ACCOUNT_BLOCKED'), 1);
					else:

						$tblName  = 'tambola_orders';
						$whereCon = array();
						$whereCon['where']['order_id'] = $orderId;
						$orderData = $this->common_model->getData('single',$tblName,$whereCon);
						if(empty($orderData)):
							throw new Exception(lang('INVALID_ORDER_ID'), 1);
						elseif($orderData['status'] == "CL"):
							throw new Exception(lang('ORDER_ALREADY_CANCELLED'), 1);

						else:

							$winningAmountList = $this->checkwinningamount($orderData);
							if(!empty($winningAmountList)):
								$result['status']              = $orderData['status'];
								$result['winning_amount_list'] = $winningAmountList;
								$totalWinningAmount = 0;
								foreach($winningAmountList as $winning):
									$totalWinningAmount += isset($winning['prize']) ? $winning['prize'] : 0;
								endforeach;
								$result['total_winning_amount'] = $totalWinningAmount;
							else:
								$result['winning_amount_list'] = array();
								$result['total_winning_amount'] = 0;
							endif;

							if($totalWinningAmount > 0):
                                $redeemlimit = !empty($userData['redeeming_amount_limit'])? $userData['redeeming_amount_limit']:1000;
								if($totalWinningAmount >  $redeemlimit):
									$errorMessage = str_replace('####AMOUNT####', $totalWinningAmount, lang('BIG_WINNER_TEXT'));
								else:
									$errorMessage =  lang('SUCCESS_ACTION');
								endif;
								echo outPut(1,lang('SUCCESS_CODE'),$errorMessage,$result);die();
							else:
								throw new Exception(lang('NO_WINNING_AMOUNT_FOUND'), 1);
							endif;
							
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

	/* * *********************************************************************
	 * * Function name : ReportSummary
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : Get the report summary for the tambola game tickets
	 * * Date 		   : 5 Feb 2026
	 * * **********************************************************************/
	public function ReportSummary(){
		$apiHeaderData = getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 	   = array();
		try {
			if(requestAuthenticate(APIKEY,'POST')):
				$usersId      = $this->input->post('users_id');
				$startDate    = $this->input->post('start_date');
				$endDate      = $this->input->post('end_date');
				$productTitle = $this->input->post('product_title');
				
				if(empty($usersId)):
					throw new Exception(lang('USER_ID_EMPTY'), 1);
				elseif(empty($startDate)):
					throw new Exception(lang('EMPTY_START_DATE'), 1);
				elseif(empty($endDate)):
					throw new Exception(lang('EMPTY_END_DATE'), 1);
				else:

					$tblName = 'uw_users';
					$whereCon['where']['users_id'] = (int)$usersId;
					$userData = $this->common_model->getData('single',$tblName,$whereCon);
					if(empty($userData)):
						throw new Exception(lang('USER_NOT_FOUND'), 1);
					elseif($userData['status'] != "A"):
						throw new Exception(lang('ACCOUNT_BLOCKED'), 1);
					else:
						$startDate = date('Y-m-d H:i:01', strtotime($startDate));
						$endDate   = date('Y-m-d H:i:59', strtotime($endDate));
						$startTs   = strtotime($startDate);
						$endTs     = strtotime($endDate);

						$summaryData = $this->getHourlyReportSummary($userData, $startTs, $endTs, $startDate, $endDate, $productTitle);
						$result = $summaryData;
						echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$result);
					endif;
				endif;
			else:
				throw new Exception(lang('FORBIDDEN_MSG'),1);
			endif;
		} catch (Exception $e) {
			echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);	
		}
	}

	private function getHourlyReportSummary($userData=array(), $startTs=0, $endTs=0, $startDate='', $endDate='', $productTitle='')
	{
		$userOid = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
		$orders   = array();
		$whereCon = array();
		$whereCon['where']['users_oid'] = $userOid;
		$whereCon['where']['created_at'] = array('$gte' => (int)$startTs, '$lte' => (int)$endTs);
		$orderslist = $this->common_model->getData('multiple', 'uw_hourly_orders', $whereCon);
		 
		$wheresellConSettler['where']['settler_users_oid'] = $userOid;
		$wheresellConSettler['where']['redeemed_at'] = array('$gte' => (int)$startTs, '$lte' => (int)$endTs);
		$ordersettler = $this->common_model->getData('multiple', 'uw_hourly_orders', $wheresellConSettler);
		if(!empty($orderslist) && !empty($ordersettler) ):
			$orders = array_merge($orderslist, $ordersettler);
			$orders = array_unique($orders, SORT_REGULAR);
		elseif(!empty($orderslist)):
			$orders = $orderslist;
		elseif(!empty($ordersettler)):
			$orders = $ordersettler;
		endif;
		
		$productMap = array();
		$cancelledProductOrders = array();
		$allOrderIds = array();
		foreach($orders as $order):
			if(!empty($productTitle)):
				$orderProductName = isset($order['products_name']) ? (string)$order['products_name'] : '';
				if(stripos($orderProductName, $productTitle) === false):
					continue;
				endif;
			endif;

			$productKey = '';
			if(!empty($order['products_oid']) && isset($order['products_oid']->{'$id'})):
				$productKey = $order['products_oid']->{'$id'};
			else:
				$productKey = isset($order['products_name']) ? $order['products_name'] : 'N/A';
			endif;

			if(!isset($productMap[$productKey])):
				$productMap[$productKey] = array(
					'_id' => isset($order['products_name']) ? $order['products_name'] : 'N/A',
					'price' => 0,
					'sales_count' => 0,
					'totalSalesCount' => 0,
					'sales' => 0,
					'product_image' => '',
					'product_id' => isset($order['products_id']) ? (int)$order['products_id'] : 0,
					'draw_date' => '',
					'draw_time' => '',
					'commissionAmount' => 0,
					'totalcancelOrderAmount' => 0,
					'totalCustomerPaid' => 0,
					'order_ids' => array(),
					'products_oid_str' => (!empty($order['products_oid']) && isset($order['products_oid']->{'$id'})) ? (string)$order['products_oid']->{'$id'} : '',
					'products_name' => isset($order['products_name']) ? (string)$order['products_name'] : ''
				);
			endif;

			$isCancelledOrder = (isset($order['status']) && $order['status'] == 'CL');
			if($isCancelledOrder):
				if(!isset($cancelledProductOrders[$productKey])):
					$cancelledProductOrders[$productKey] = array(
						'order_ids' => array()
					);
				endif;
				if(!empty($order['order_id'])):
					$cancelledProductOrders[$productKey]['order_ids'][] = $order['order_id'];
				endif;
			else:

				
				if( (string)$order['users_oid']  == $userOid &&  $order['created_at'] > $startTs  && $order['created_at'] < $endTs):
					$productMap[$productKey]['sales_count'] += $order['qty'];
					$productMap[$productKey]['totalSalesCount'] += 1;
					$productMap[$productKey]['sales'] += (float)($order['total_price'] ?? 0);
				endif;
				if(!empty($order['order_id'])):
					$productMap[$productKey]['order_ids'][] = $order['order_id'];
					$allOrderIds[] = $order['order_id'];
				endif;
			endif;

			if(empty($productMap[$productKey]['draw_date']) && !empty($order['expiry_date']) && is_numeric($order['expiry_date'])):
				$productMap[$productKey]['draw_date'] = date('Y-m-d', (int)$order['expiry_date']);
				$productMap[$productKey]['draw_time'] = date('H:i', (int)$order['expiry_date']);
			endif;
		endforeach;

		if(!empty($cancelledProductOrders)):
			foreach($cancelledProductOrders as $cKey => $cItem):
				if(isset($productMap[$cKey])):
					$productMap[$cKey]['order_ids'] = array_merge($productMap[$cKey]['order_ids'], $cItem['order_ids']);
				else:
					if(!empty($cItem['order_ids'])):
						$firstCancelledOrderId = $cItem['order_ids'][0];
						$cancelOrderCon['where']['order_id'] = $firstCancelledOrderId;
						$cancelOrderInfo = $this->common_model->getData('single', 'uw_hourly_orders', $cancelOrderCon);
						if(!empty($cancelOrderInfo)):
							$productMap[$cKey] = array(
								'_id' => isset($cancelOrderInfo['products_name']) ? $cancelOrderInfo['products_name'] : 'N/A',
								'price' => 0,
								'sales_count' => 0,
								'totalSalesCount' => 0,
								'sales' => 0,
								'product_image' => '',
								'product_id' => isset($cancelOrderInfo['products_id']) ? (int)$cancelOrderInfo['products_id'] : 0,
								'draw_date' => '',
								'draw_time' => '',
								'commissionAmount' => 0,
								'totalcancelOrderAmount' => 0,
								'totalCustomerPaid' => 0,
								'order_ids' => $cItem['order_ids'],
								'products_oid_str' => (!empty($cancelOrderInfo['products_oid']) && isset($cancelOrderInfo['products_oid']->{'$id'})) ? (string)$cancelOrderInfo['products_oid']->{'$id'} : '',
								'products_name' => isset($cancelOrderInfo['products_name']) ? (string)$cancelOrderInfo['products_name'] : ''
							);
							if(!empty($cancelOrderInfo['expiry_date']) && is_numeric($cancelOrderInfo['expiry_date'])):
								$productMap[$cKey]['draw_date'] = date('Y-m-d', (int)$cancelOrderInfo['expiry_date']);
								$productMap[$cKey]['draw_time'] = date('H:i', (int)$cancelOrderInfo['expiry_date']);
							endif;
						endif;
					endif;
				endif;
			endforeach;
		endif;

		foreach($productMap as $key => $item):
			$gameDetails = array();
			if(!empty($item['products_oid_str']) && strlen($item['products_oid_str']) == 24):
				$gameDetails = $this->common_model->getDataByParticularField('uw_hourly_games','_id',new MongoDB\BSON\ObjectID($item['products_oid_str']));
			elseif(!empty($item['product_id'])):
				$gameDetails = $this->common_model->getDataByParticularField('uw_hourly_games','products_id',(int)$item['product_id']);
			elseif(!empty($item['products_name'])):
				$gameWhereCon['where']['title'] = $item['products_name'];
				$gameDetails = $this->common_model->getData('single','uw_hourly_games',$gameWhereCon);
			endif;

			if(!empty($gameDetails)):
				$productMap[$key]['_id'] = isset($gameDetails['title']) ? $gameDetails['title'] : $productMap[$key]['_id'];
				$productMap[$key]['price'] = isset($gameDetails['price']) ? (float)$gameDetails['price'] : $productMap[$key]['price'];
				$productMap[$key]['product_id'] = isset($gameDetails['products_id']) ? (int)$gameDetails['products_id'] : $productMap[$key]['product_id'];
				$productMap[$key]['product_image'] = isset($gameDetails['game_image']) ? $gameDetails['game_image'] : (isset($gameDetails['product_image']) ? $gameDetails['product_image'] : $productMap[$key]['product_image']);
				if((empty($productMap[$key]['draw_date']) || empty($productMap[$key]['draw_time'])) && !empty($gameDetails['draw_time']) && is_numeric($gameDetails['draw_time'])):
					$productMap[$key]['draw_date'] = date('Y-m-d', (int)$gameDetails['draw_time']);
					$productMap[$key]['draw_time'] = date('H:i', (int)$gameDetails['draw_time']);
				endif;
			endif;
		endforeach;

		$totalProductDetails = array_values($productMap);
		if(!empty($productTitle)):
			$totalProductDetails = array_values(array_filter($totalProductDetails, function($item) use ($productTitle){
				$title = isset($item['_id']) ? (string)$item['_id'] : '';
				return stripos($title, $productTitle) !== false;
			}));
		endif;
		$loadBalanceWhere = array(
			'user_oid' => $userOid,
			'created_at' => array('$gte' => $startDate, '$lte' => $endDate)
		);

		$totalSales = 0;
		$totalSalesCount = 0;
		$totalCommission = 0;
		$totalCancel = 0;
		$totalPaid = 0;

		foreach($totalProductDetails as $idx => $product):
			$orderIds = array_values(array_unique($product['order_ids']));
			$productLoadWhere = $loadBalanceWhere;
			if(!empty($orderIds)):
				$productLoadWhere['order_id'] = array('$in' => $orderIds);
			endif;

			$productLoadCon['where'] = $productLoadWhere;
			$loadRows = $this->common_model->getData('multiple','uw_loadBalance',$productLoadCon);
			$commissionAmount = 0;
			$cancelAmount = 0;
			$customerPaid = 0;

			foreach($loadRows as $row):
				$narration = isset($row['narration']) ? $row['narration'] : '';
				$upoints = (float)($row['upoints'] ?? 0);
				if($narration == 'Hourly Game Commission'):
					$commissionAmount += $upoints;
				elseif($narration == 'Hourly Game Commission Cancelled'):
					$commissionAmount -= $upoints;
				elseif($narration == 'Hourly Game Order Cancelled'):
					$cancelAmount += $upoints;
				elseif($narration == 'Hourly Game Prize Redeemed'):
					$customerPaid += $upoints;
				endif;
			endforeach;

			$totalProductDetails[$idx]['commissionAmount'] = round($commissionAmount, 2);
			$totalProductDetails[$idx]['totalcancelOrderAmount'] = round($cancelAmount, 2);
			$totalProductDetails[$idx]['totalCustomerPaid'] = round($customerPaid, 2);

			// Skip products with no activity in selected range.
			if(
				(int)$totalProductDetails[$idx]['sales_count'] === 0 &&
				(float)$totalProductDetails[$idx]['sales'] == 0 &&
				(float)$totalProductDetails[$idx]['commissionAmount'] == 0 &&
				(float)$totalProductDetails[$idx]['totalcancelOrderAmount'] == 0 &&
				(float)$totalProductDetails[$idx]['totalCustomerPaid'] == 0
			):
				unset($totalProductDetails[$idx]);
				continue;
			endif;

			unset($totalProductDetails[$idx]['order_ids']);
			unset($totalProductDetails[$idx]['products_oid_str']);
			unset($totalProductDetails[$idx]['products_name']);

			$totalSalesCount += (int)$totalProductDetails[$idx]['sales_count'];
			$totalSales += (float)$totalProductDetails[$idx]['sales'];
			$totalCommission += (float)$totalProductDetails[$idx]['commissionAmount'];
			$totalCancel += (float)$totalProductDetails[$idx]['totalcancelOrderAmount'];
			$totalPaid += (float)$totalProductDetails[$idx]['totalCustomerPaid'];
		endforeach;
		$totalProductDetails = array_values($totalProductDetails);

		$dueBalance = $totalSales - $totalCommission - $totalPaid;
		return array(
			'totalSalesCount' => (string)$totalSalesCount,
			'totalSales' => (string)round($totalSales, 2),
			'commissionAmount' => (string)round($totalCommission, 2),
			'totalcancelOrderAmount' => (string)round($totalCancel, 2),
			'totalCustomerPaid' => (string)round($totalPaid, 2),
			'dueBalance' => (string)round($dueBalance, 2),
			'totalProductDetails' => $totalProductDetails
		);
	}

	/* * *********************************************************************
	 * * Function name : hourlyOrderList
	 *  * * Developed By  : Megha Kumari
	 * * Purpose  	   : Public hourly order list with tickets
	 * * Date 		   : 22 April 2026
	 * * **********************************************************************/
	public function hourlyOrderList()
	{
		$apiHeaderData = getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 	   = array();
		try {
			if(requestAuthenticate(APIKEY,'POST')):
				 
				$startDate    = $this->input->post('start_date');
				$endDate      = $this->input->post('end_date');
				$productName  = $this->input->post('product_name');
				$pageNo       = (int)$this->input->post('page_no');
				$itemsPerPage = (int)$this->input->post('itemsPerPage');
				
				if(empty($startDate)):
					throw new Exception(lang('EMPTY_START_DATE'), 1);
				elseif(empty($endDate)):
					throw new Exception(lang('EMPTY_END_DATE'), 1);
				elseif(empty($productName)):
					throw new Exception(lang('EMPTY_PRODUCT_NAME'), 1);
				elseif($pageNo <= 0):
					throw new Exception(lang('EMPTY_PAGE_NO'), 1);
				elseif($itemsPerPage <= 0):
					throw new Exception(lang('EMPTY_ITEMPERPAGE'), 1);
				else:
					$startIndex   = ($pageNo - 1) * $itemsPerPage;
					$shortField   = array('created_at' => -1);
					$resultType   = 'multiple';
					$tblName      = "uw_hourly_orders";
					$whereCondition = array();

					if(!empty($productName)):
						$productName = str_replace('_', '/', $productName);
						$whereConProduct['where']['title'] = $productName;
						$whereConProduct['select']         = array('_id' => 1);
						$productData = $this->common_model->getData('single','uw_hourly_games',$whereConProduct);
						 
						if(!empty($productData)):
							$whereCondition['where']['products_oid'] = new MongoDB\BSON\ObjectID($productData['_id']->{'$id'});
						else:
							throw new Exception(lang('PRODUCT_NOT_FOUND'), 1);
						endif;
					endif;

					// $whereCondition['where']['created_at'] = array(
					// 	'$gte' => strtotime(date('Y-m-d H:i:00', strtotime($startDate))),
					// 	'$lte' => strtotime(date('Y-m-d H:i:59', strtotime($endDate)))
					// );
					$whereCondition['where']['draw_time'] =  strtotime($startDate);
					// $whereCondition['where']['status'] = "A";
					$whereCondition['where']['status'] = array('$in' => array('A', 'Redeemed'));

					$totalCount = 0;
					$totalPages = 0;
					 
					$shortField  = array('created_at' => -1);
					$resultType  = 'multiple';
					$tblName   = "uw_hourly_orders";
					$OrderData = $this->common_model->getHourlyGameOrderData($resultType, $tblName, $whereCondition, $shortField, '', '');

					$CSVData = array();
					if (is_array($OrderData)) {
						foreach ($OrderData as $index => $itemsArray) {
							$status = 'N/A';
							if (isset($itemsArray['status'])) {
								if ($itemsArray['status'] === 'CL') {
									$status = 'Cancelled';
								} elseif ($itemsArray['status'] === 'A') {
									$status = 'Success';
								} elseif ($itemsArray['status'] === 'REDEEMED' || $itemsArray['status'] === 'Redeemed') {
									$status = 'Redeemed';
								}
							}

							$baseRow = array();
							$baseRow['POS No.']       = !empty($itemsArray['seller_pos_number']) ? $itemsArray['seller_pos_number'] : 'N/A';
							$baseRow['Order ID']      = !empty($itemsArray['order_id']) ? $itemsArray['order_id'] : 'N/A';
							$baseRow['Product Name']  = !empty($itemsArray['products_name']) ? $itemsArray['products_name'] : 'N/A';
							$baseRow['Store Name']    = !empty($itemsArray['seller_store_name']) ? $itemsArray['seller_store_name'] : 'N/A';
							$baseRow['Seller Name']   = !empty($itemsArray['seller_users_name']) ? $itemsArray['seller_users_name'] : 'N/A';
							$baseRow['Seller Mobile'] = !empty($itemsArray['seller_users_mobile']) ? $itemsArray['seller_users_mobile'] : 'N/A';
							$baseRow['Seller POS']    = !empty($itemsArray['seller_pos_number']) ? $itemsArray['seller_pos_number'] : 'N/A';
							$baseRow['Bind With']     = !empty($itemsArray['seller_users_bind_person_name']) ? $itemsArray['seller_users_bind_person_name'] : 'N/A';
							$baseRow['Area']          = !empty($itemsArray['seller_store_area']) ? $itemsArray['seller_store_area'] : 'N/A';
							$baseRow['Straight Amount'] = 0;
							$baseRow['Rumble Amount']   = 0;
							$baseRow['Chance Amount']   = 0;
							$baseRow['Payment Status']  = $status;
							$baseRow['Purchase Date']   = !empty($itemsArray['created_at']) ? date('d-m-Y H:i', $itemsArray['created_at']) : 'N/A';

							$ticketRows = array();
							if (!empty($itemsArray['tickets']) && is_array($itemsArray['tickets'])) {
								foreach ($itemsArray['tickets'] as $ticketRow) {
									$ticketValue = 'N/A';
									$pointsValue = 'N/A';
									$couponTypeValue = 'N/A';
									$normalizedCouponType = '';

									$ticketValue     = preg_replace('/\s+/', '', (string) $ticketRow->ticket);
									$couponTypeValue = strtolower(trim((string) $ticketRow->type));

									$csvRow['Straight Amount'] = '0';
									$csvRow['Rumble Amount']   = '0';
									$csvRow['Chance Amount']   = '0';
									$csvRow = $baseRow;
									if ($couponTypeValue === 'straight') {
										$csvRow['Straight Amount'] = '1';
									} elseif ($couponTypeValue === 'rumble') {
										$csvRow['Rumble Amount'] = '1';
									} elseif ($couponTypeValue === 'chance') {
										$csvRow['Chance Amount'] = '1';
									}
									
									$csvRow['Coupons'] = $ticketValue;
									
									$ticketRows[] = $csvRow;
								}
							}

							if (!empty($ticketRows)) {
								$CSVData = array_merge($CSVData, $ticketRows);
							}
						}
					}
					$totalCount = count($CSVData);
					$totalPages = $totalCount > 0 ? (int)ceil($totalCount / $itemsPerPage) : 0;
					if($totalPages > 0 && $pageNo > $totalPages):
						$pageNo = $totalPages;
					endif;
					$startIndex = ($pageNo - 1) * $itemsPerPage;
					$resultData = array_slice($CSVData, $startIndex, $itemsPerPage);
					if(!empty($resultData)):
						$result['total_count']  = $totalCount;
						$result['current_page'] = $pageNo;
						$result['total_pages']  = $totalPages;
						$result['itemsPerPage'] = $itemsPerPage;
						$result['has_next_page'] = ($pageNo < $totalPages);
						$result['PageData']     = $resultData;
						echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$result);die();
					endif;
					$result['total_count']  = (int)$totalCount;
					$result['current_page'] = (int)$pageNo;
					$result['total_pages']  = (int)$totalPages;
					$result['itemsPerPage'] = (int)$itemsPerPage;
					$result['has_next_page'] = false;
					$result['PageData']     = array();
					echo outPut(0,lang('SUCCESS_CODE'),lang('DATA_NOT_FOUND'),$result);die();
				endif;
			else:
				throw new Exception(lang('FORBIDDEN_MSG'),1);
			endif;
		} catch (Exception $e) {
			echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);	
		}
		 
	}

	/* * *********************************************************************
	 * * Function name : hourlyDrawResult
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to hourlyDrawResult
	 * * Date 		   : 21 April 2026
	 * * **********************************************************************/
	public function hourlyDrawResult()
	{
		$apiHeaderData = getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 	   = array();
		try {
			if(requestAuthenticate(APIKEY,'POST')):
				$usersId  = $this->input->post('users_id');
				$DrawDate = $this->input->post('draw_date');

				if(empty($usersId)):
					throw new Exception(lang('USER_ID_EMPTY'), 1);
				elseif(empty($DrawDate)):
					throw new Exception(lang('EMPTY_DRAW_DATE'), 1);
				else:
					$tblName    = 'uw_users';
					$fieldList  = array('users_id','status','users_type');
					$whereCon['where']['users_id'] = (int)$usersId;
					$userData   = $this->common_model->getParticularFieldByMultipleCondition($fieldList ,$tblName,$whereCon);
					
					if(empty($userData)   || $userData['status'] != "A" || $userData['users_type'] != "Retailer" ):
						throw new Exception(lang('INVALID_USER'), 1);
					elseif(!empty($userData) && $userData['status'] == "A" && $userData['users_type'] == "Retailer" ):
						$tblName    = 'uw_hourly_game_draw_result';
						$drawDateCon['where']['result_date'] = strtotime($DrawDate);
						$drawDateCon['where']['status']      = "A";
						$shortField = array('draw_result_time' => -1);
						$resultData = $this->common_model->getData('multiple',$tblName,$drawDateCon,$shortField);						
						if(empty($resultData)):
							throw new Exception(lang('DATA_NOT_FOUND'), 1);
						else:
							$result = $resultData;
							echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$result);
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

	/* * *********************************************************************
	 * * Function name : moveToWallet
	 * * Developed By  : Sumit Bedwal
	 * * Purpose  	   : This function used to moveToWallet
	 * * Date 		   : 01 May 2026
	 * * Updated By    : Dilip Halder --added order calcelled status alert only line no -- 2316-2317
	 * * **********************************************************************/
	public function moveToWallet() { 
		$apiHeaderData = getApiHeaderData(); 
		$this->generatelogs->putLog('APP',logOutPut($_POST)); 
		$result = array(); 
		try { 
			if(requestAuthenticate(APIKEY,'POST')):
				$usersId = $this->input->post('users_id'); 
				$orderID = $this->input->post('order_id'); 
				$redeemStatus = $this->input->post('redeem_status'); 
				$redeemByMode = $this->input->post('redeem_by_mode');

				if(empty($usersId)): 
					throw new Exception(lang('USER_ID_EMPTY'), 1); 
				elseif(empty($orderID)): 
					throw new Exception(lang('ORDER_ID_EMPTY'), 1); 
				else: 
					// checking order_id is winner or not 
					$tableName = "uw_hourly_orders"; 
					$whereCon['where'] = array('order_id' => $orderID); 
					$orderData = $this->common_model->getData('single',$tableName,$whereCon); 

					if($orderData && $orderData['is_winner'] === 'Y' && !empty($orderData['winning_details'])): 
						if((isset($orderData['winning_status']) && $orderData['winning_status'] == 'paid') || (isset($orderData['redeem_status']) && $orderData['redeem_status'] == 'paid')):
							// throw new Exception(lang('ALREADY_REDEEM'));
							echo outPut(0,lang('SUCCESS_CODE'),lang('ALREADY_REDEEM'),$result);die();
						endif;
						// get user details 
						$tblName = 'uw_users'; 
						$fieldList = array('users_id','status','users_type','totalArabianPoints','availableArabianPoints'); 
						$UserwhereCon['where']['users_id'] = (int)$usersId;

						$userData = $this->common_model->getParticularFieldByMultipleCondition($fieldList ,$tblName,$UserwhereCon); 

						if(empty($userData) || $userData['status'] != "A"): 
							throw new Exception(lang('INVALID_USER'), 1);
						elseif($orderData['status'] == 'CL'): 
							throw new Exception(lang('ORDER_CANCELLED'), 1);
						else: 
							// update uw_hourly_orders table 
							$updateParams['winning_status']   = 'paid';
							$updateParams['status']           = 'Redeemed';
							$updateParams['redeem_status'] 	  = $redeemStatus; 
							$updateParams['redeem_by_mode']   = $redeemByMode; 
							$updateParams['redeemed_at']      =  strtotime(date('Y-m-d H:i'));
							$updateParams["modified_at"] 	  = date('Y-m-d H:i'); 
							$updateParams['settler_users_id'] = (int)$usersId;
							$updateParams['settler_users_oid'] = new MongoDB\BSON\ObjectId($userData['_id']['$id']);
							// $updateParams['seller_id'] 		= (int)$usersId; 
							$updateParams['update_ip']     		= currentIp(); 
							
							$WInnner_whereCon = array('order_id' => $orderID,'redeem_status' => array('$ne' => 'paid') ); 
							$winnerRedeemResult = $this->common_model->editMultipleDataByMultipleCondition('uw_hourly_orders', $updateParams,$WInnner_whereCon);

							if($winnerRedeemResult > 0 ): 
								$totalPrizeAmount = $orderData['winning_amount'] ?? 0; 

								//Credting user's winning prize amount to users account. 
								$userParam['totalArabianPoints'] = (float)$userData['totalArabianPoints'] + $totalPrizeAmount; 
								$userParam['availableArabianPoints'] = (float) $userData['availableArabianPoints'] + $totalPrizeAmount; 
								$userParam["update_date"] = date('Y-m-d H:i'); 
								
								$userResult = $this->common_model->editData('uw_users', $userParam,'users_id',(int)$usersId); 
						
								if(!empty($userResult)): 
									/* Load Balance Table -- after Sign Up*/ 
									$Redeemparam["load_balance_id"] = (int)$this->geneal_model->getNextSequence('uw_loadBalance'); 
									$Redeemparam["user_oid"] = new MongoDB\BSON\ObjectId($userData['_id']['$id']); 
									$Redeemparam["order_oid"] = new MongoDB\BSON\ObjectId($orderData['_id']->{'$id'}); 
									$Redeemparam["order_id"] = $orderID;
									$Redeemparam["product_id"] = isset($orderData['products_id']) ? (int)$orderData['products_id'] : 0; // $orderData['product_id'] ?? 0; 
									$Redeemparam["user_id_deb"] = (int)0; 
									$Redeemparam["user_id_cred"] = (int)$usersId; 
									$Redeemparam["upoints"] = (float)$totalPrizeAmount; 
									$Redeemparam["record_type"] = 'Credit'; 
									// $Redeemparam["narration"] = 'Moved winning Prize';
									$Redeemparam["narration"] = 'Hourly Game Prize Redeemed'; 
									$Redeemparam["remarks"] = "Hourly Game Prize for ( ".$orderID." ) transferred to wallet."; 
									$Redeemparam["availableArabianPoints"] = (float)$userData['availableArabianPoints']; 
									$Redeemparam["end_balance"] = (float)$userData['availableArabianPoints']+$totalPrizeAmount; 
									$Redeemparam["creation_ip"] = currentIp(); 
									$Redeemparam["created_at"] = date('Y-m-d H:i'); 
									$Redeemparam["created_by"] = (int)$usersId; 
									$Redeemparam["status"] = "A"; 

									$this->geneal_model->addData('uw_loadBalance', $Redeemparam); 
									$result = array('payment_date' => date('Y-m-d H:i')); 

									echo outPut(1,lang('SUCCESS_CODE'),lang('COUPON_REDEEMED_SUCCESFULLY'),$result);die(); 
								else: 
									echo outPut(1,lang('SUCCESS_CODE'),lang('BALANCE_TRANSERFER_EORROR'),$result);die(); 
								endif; 
							else: 
								echo outPut(0,lang('SUCCESS_CODE'),lang('ALREADY_REDEEM'),$result); 
							endif; 
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

	/* * *********************************************************************
	 * * Function name : drawSlots
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to drawSlots
	 * * Date 		   : 29 June 2026
	 * * **********************************************************************/
	public function drawSlots()
	{
		$apiHeaderData = getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_GET));
		$result = array();
		try {
			if(requestAuthenticate(APIKEY,'GET')):
				$drawConfig = $this->common_model->getData(
					'single',
					'uw_hourly_draw_time',
					array('where' => array('status' => 'A')),
					array('creation_date' => -1)
				);

				if(empty($drawConfig)):
					throw new Exception(lang('DATA_NOT_FOUND'), 1);
				endif;

				$timeStart        = $drawConfig['draw_time_start'];
				$timeEnd          = $drawConfig['draw_time_end'];
				$currentTs        = strtotime(date('Y-m-d H:i:s'));
				$currentHourStart = strtotime(date('Y-m-d H:00:00', $currentTs));
				$today            = date('Y-m-d', $currentTs);
				$currentTime      = date('H:i:s', $currentTs);
				$isOvernight      = strtotime($timeEnd) <= strtotime($timeStart);
				$isAtEndHour      = (date('H:i', $currentTs) === date('H:i', strtotime('1970-01-01 ' . $timeEnd)));
				$isInOvernightTail = $isOvernight
					&& strtotime($currentTime) < strtotime($timeStart)
					&& !$isAtEndHour;

				if(!$isOvernight):
					$currentDrawDate = $today;
				elseif($isAtEndHour):
					$currentDrawDate = $today;
				elseif(strtotime($currentTime) <= strtotime($timeEnd)):
					$currentDrawDate = date('Y-m-d', strtotime($today . ' -1 day'));
				else:
					$currentDrawDate = $today;
				endif;

				
				

				$slots     = array();
				$cycleStart = strtotime($currentDrawDate . ' ' . $timeStart);
				if($isOvernight):
					$cycleEnd = strtotime(date('Y-m-d', strtotime($currentDrawDate . ' +1 day')) . ' ' . $timeEnd);
				else:
					$cycleEnd = strtotime($currentDrawDate . ' ' . $timeEnd);
				endif;
				$nextDayTs = strtotime($currentDrawDate . ' +1 day');

				for($ts = $cycleStart; $ts <= $cycleEnd; $ts += 3600):
					if($ts <= $currentHourStart):
						continue;
					endif;
					$slots[] = array(
						'draw_date'        => $currentDrawDate,
						'draw_time'        => $ts,
						'draw_time_string' => date('Y-m-d H:i', $ts),
						'slot_time'        => date('H:i', $ts),
						'slot_date'        => date('Y-m-d', $ts),
						'is_next_day_slot' => ($ts >= $nextDayTs) ? 'Y' : 'N',
					);
				endfor;

				

				if(!$isInOvernightTail && !($isOvernight && $isAtEndHour)):
					$nextDrawDate = date('Y-m-d', strtotime($currentDrawDate . ' +1 day'));
					$currentDayTimes = array();
					foreach($slots as $slot):
						if($slot['slot_date'] === $currentDrawDate):
							$currentDayTimes[$slot['slot_time']] = true;
						endif;
					endforeach;
 
					$nextCycleStart = strtotime($nextDrawDate . ' ' . $timeStart);
					// echo '<pre>';
					// print_r(date('Y-m-d H:i:s', $nextCycleStart));
					// die();
					
					if($isOvernight):
						$nextCycleEnd = strtotime(date('Y-m-d', strtotime($nextDrawDate . ' +1 day')) . ' ' . $timeEnd);
					else:
						$nextCycleEnd = strtotime($nextDrawDate . ' ' . $timeEnd);
					endif;

					for($ts = $nextCycleStart; $ts <= $nextCycleEnd; $ts += 3600):
						if($ts <= $currentHourStart):
							continue;
						endif;
						$slotTime = date('H:i', $ts);
						$slotDate = date('Y-m-d', $ts);
						if($slotDate !== $nextDrawDate):
							continue;
						endif;
						if(isset($currentDayTimes[$slotTime])):
							continue;
						endif;
						$slots[] = array(
							'draw_date'        => $nextDrawDate,
							'draw_time'        => $ts,
							'draw_time_string' => date('Y-m-d H:i', $ts),
							'slot_time'        => $slotTime,
							'slot_date'        => $slotDate,
							'is_next_day_slot' => 'Y',
						);
					endfor;
				endif;

				$seen = array();
				$finalSlots = array();
				foreach($slots as $slot):
					if(isset($seen[$slot['draw_time']])):
						continue;
					endif;
					$seen[$slot['draw_time']] = true;
					$finalSlots[] = $slot;
				endforeach;

				if(empty($finalSlots)):
					throw new Exception(lang('DATA_NOT_FOUND'), 1);
				endif;

				$result['draw_time_start'] = $timeStart;
				$result['draw_time_end']   = $timeEnd;
				$result['draw_date']       = $currentDrawDate;
				$result['slots']           = $finalSlots;
				echo outPut(1, lang('SUCCESS_CODE'), lang('SUCCESS_ACTION'), (object) $result);
			else:
				throw new Exception(lang('FORBIDDEN_MSG'),1);
			endif;
		} catch (Exception $e) {
			echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);
		}
	}
	
}

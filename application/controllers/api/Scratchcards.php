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
		$session       = null;
		try {
			if(requestAuthenticate(APIKEY,'POST')):

				$session = $this->mongodb_client->client->startSession();
				$session->startTransaction();

				$usersId  = $this->input->post('users_id');
				$gameMode = trim((string)$this->input->post('game_mode'));

				if(empty($usersId)):
					throw new Exception(lang('USER_ID_EMPTY'), 1);
				elseif($gameMode === ''):
					throw new Exception(lang('GAME_MODE_EMPTY'), 1);
				elseif(!in_array($gameMode, array('scratch_win', 'buy_win'), true)):
					throw new Exception(lang('EMPTY_GAME_MODE'), 1);
				else:

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
			if ($session !== null) {
				$this->mongodb_client->abortTransaction($session);
			}
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
		$session       = null;
		try {
			if(requestAuthenticate(APIKEY,'POST')):
				$usersId    = $this->input->post('users_id');
				$productsId = $this->input->post('products_id');
				$qty        = $this->input->post('qty')?:1;
				$txnID      = $this->input->post('txn_id');
				// $gameMode   = trim((string)$this->input->post('game_mode'));

 				$smsType          = $this->input->post('otp_sent');
				$buyerCountryCode = $this->input->post('buyer_country_code');
				$buyerMobile      = $this->input->post('buyer_mobile');
				$buyerEmail       = $this->input->post('buyer_email');
		         
				if(empty($usersId)):
					throw new Exception(lang('USER_ID_EMPTY'), 1);
				elseif(empty($productsId)):
					throw new Exception(lang('PRODUCT_ID_EMPTY'), 1);
				else:

					$this->session->sess_regenerate();
					$session = $this->mongodb_client->client->startSession();
					$session->startTransaction();
					
					$tblName           = 'uw_users';
					$whereCon['where'] = array('users_id' => (int)$usersId);
					$userData          = $this->mongodb_client->getDocument('single',$tblName,$whereCon['where'],$session);
					if(empty($userData)):
						throw new Exception(lang('INVALID_USER'), 1);
					elseif($userData['status'] != "A"):
						throw new Exception(lang('ACCOUNT_BLOCKED'), 1);
					elseif(empty($userData['enable_scratch_win']) || $userData['enable_scratch_win'] !== 'Y'):
						throw new Exception('Scratch & Win is disabled for this account.', 1);
					elseif(empty($userData['enable_buy_win']) || $userData['enable_buy_win'] !== 'Y'):
						throw new Exception('Buy & Win is disabled for this account.', 1);
					else:

						

						$tblName           = 'uw_scratch_win_games';
						$whereCon['where'] = array();
						$whereCon['where']['_id']            = new MongoDB\BSON\ObjectID($productsId);
						$whereCon['where']['status']         = "A";
						$whereCon['where']['start_date']     = array('$lte' => strtotime(date('Y-m-d H:i:s')));
						$whereCon['where']['expiry_date']    = array('$gt' => strtotime(date('Y-m-d H:i:s')));
						$whereCon['where']['prize_setting']  = "enabled";

						$fieldList  = array();
						$gameData   = $this->mongodb_client->getDocument('single',$tblName,$whereCon['where'],$session,'','','',$fieldList);
						if(empty($gameData)):
							throw new Exception(lang('DATA_NOT_FOUND'), 1);
						elseif($userData['availableArabianPoints'] < $gameData['price'] * $qty):
							throw new Exception(lang('LOW_BALANCE'), 1);
						else: 
							
							if(!empty($txnID)):
								$checkWhereCon1['where']['txn_id'] = $txnID;
								$resultData = $this->mongodb_client->getDocument('single','uw_scratch_win_orders',$checkWhereCon1['where'] , $session);
								if(!empty($resultData)):
									echo outPut(1, lang('SUCCESS_CODE'), lang('ALREADY_ORDER_PLACED'), $resultData);
									die();
								endif;

								$checkWhereCon2['where']['users_oid']    = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
								$checkWhereCon2['where']['products_oid'] = new MongoDB\BSON\ObjectID($productsId);
								$checkWhereCon2['where']['created_at']   = array('$gte' => strtotime(date('Y-m-d H:i:s', strtotime('-2 seconds'))));
								$resultData2 = $this->mongodb_client->getDocument('single','uw_scratch_win_orders',$checkWhereCon2['where'] , $session);
								if(!empty($resultData2)):
									echo outPut(1, lang('SUCCESS_CODE'), lang('ALREADY_ORDER_PLACED'), $resultData2);
									die();
								endif;
							endif;

							if (function_exists('apcu_fetch') && function_exists('apcu_store')):
								$duplicateKey = 'scratch_buy_win_order_' . (int)$usersId;
								$currentData = array(
									'current_time'       => time(),
									'current_time_stamp' => date('Y-m-d H:i:s'),
									'users_id'           => (int)$usersId,
									'products_id'        => (string)$productsId,
									'game_mode'          => $gameMode,
									'qty'                => (int)$qty,
								);

								$cachedData = apcu_fetch($duplicateKey);
								if ($cachedData !== false):
									$compareKeys = array('users_id', 'products_id', 'game_mode', 'qty');
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
							endif;

							$prizeResult = $this->generateScratchWinPrize($gameData, $session);
							$winningStatus = isset($prizeResult['winning_status']) ? $prizeResult['winning_status'] : 'N';
							$winningType   = isset($prizeResult['winning_type']) ? $prizeResult['winning_type'] : '';
							$winningAmount = isset($prizeResult['winning_amount']) ? (float)$prizeResult['winning_amount'] : 0.0;

							$orderSeq       = floor((microtime(true) * 1000)).rand(100,999);
							$totalPrice     = $gameData['price'] * $qty;
							$param['order_id']       = "UWINN".$orderSeq;
							$param['txn_id']         = $txnID;
							$param['users_id']       = (int)$usersId;
							$param['users_oid']      = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
							$param['products_oid']   = new MongoDB\BSON\ObjectID($productsId);
							$param['products_name']  = $gameData['title'];
							$param['game_mode']      = $gameData['game_mode'];
							$param['qty']            = (int)$qty;
							$param['total_price']    = (float)$totalPrice;
							$param['winning_status'] = $winningStatus;
							$param['winning_type']   = $winningType;
							if ($winningAmount > 0):
								$param['winning_amount'] = (float)$winningAmount;
							endif;
							if (!empty($prizeResult['scratch_result'])):
								$param['scratch_result'] = $prizeResult['scratch_result'];
							endif;

							$param['sms_type']           = $smsType;
							$param['buyer_country_code'] = $buyerCountryCode;
							$param['buyer_mobile']       = (int)$buyerMobile;
							$param['buyer_email']        = $buyerEmail;
							$param['created_at']         = (int)$this->timezone->utc_time();
							$param['created_by']         = (int)$usersId;
							$param['status']             = 'A';
							$param['used_rtp']           = isset($prizeResult['used_rtp']) ? $prizeResult['used_rtp'] : 0;
							$result = $this->common_model->addData('uw_scratch_win_orders',$param);

							$orderNarration = $this->getScratchWinNarration($gameMode, 'order');
							$commissionNarration = $this->getScratchWinNarration($gameMode, 'commission');

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
							$loadBalanceParam['narration']     = $orderNarration;
							$loadBalanceParam['remarks']       = 'Order ID: '.$result['order_id'];
							$loadBalanceParam['created_at']    = strtotime(date('Y-m-d H:i:s'));
							$loadBalanceParam['created_by']    = (int)$usersId;
							$loadBalanceParam['status']        = 'A';
							$this->mongodb_client->insertDocument('loadBalance',$loadBalanceParam, $session);

							$commission_percentage = !empty($userData['scratch_card_commission_percentage']) ? $userData['scratch_card_commission_percentage'] : 15;
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
								$commissionParam['narration']     = $commissionNarration;
								$commissionParam['remarks']       = 'Order ID: '.$result['order_id'];
								$commissionParam['created_at']    = strtotime(date('Y-m-d H:i:s'));
								$commissionParam['created_by']    = (int)$usersId;
								$commissionParam['status']        = 'A';
								$this->mongodb_client->insertDocument('loadBalance',$commissionParam, $session);

								$availableArabianPoints = ((float)$userData['availableArabianPoints'] - (float)$totalPrice ) + (float)$commission_amount;
							endif;
							
							$userParam['availableArabianPoints'] = (float)$availableArabianPoints;
							$userParam['updated_at'] = date('Y-m-d H:i:s');
							$userParam['updated_by'] = (int)$usersId;
							$this->mongodb_client->updateDocument('users',['_id' => new MongoDB\BSON\ObjectID($userData['_id']->{'$id'})],['$set' => $userParam], $session);

							$session->commitTransaction();
							$this->mongodb_client->commitTransaction($session);

							$message = "";
							if(( !empty($buyerCountryCode) && !empty($buyerMobile) ) || !empty($buyerEmail)):
								$purchaseDate = date('d.m.Y h:iA');
								$message = 'Order ID '.$result['order_id'].' of '.$gameData['title'].' purchased on '.$purchaseDate.'. You can download the invoice here https://tktinvoice.com/uwin-download-invoice/'.$result['order_id'];

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
									$senderDetails['CouponDetails'] = '';
									$senderDetails['DDATE']         = $purchaseDate;
									$senderDetails['LINK']          = 'https://tktinvoice.com/uwin-download-invoice/'.$result["order_id"];
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
			if ($session !== null) {
				try {
					$this->mongodb_client->abortTransaction($session);
				} catch (Exception $abortException) {
				}
			}
			echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);	
		}
	}

	private function getScratchWinNarration($gameMode = 'scratch_win', $type = 'order')
	{
		if ($gameMode === 'buy_win') {
			return ($type === 'commission') ? 'Buy & Win Commission' : 'Buy & Win Order';
		}
		return ($type === 'commission') ? 'Scratch & Win Commission' : 'Scratch & Win Order';
	}

	private function generateScratchWinPrize($gameData = array(), $session = null)
	{
		$rtpSettings = $this->loadGlobalRtpSettingsForOrder($gameData, $session);
		$rtpScope = isset($rtpSettings['rtp_scope']) ? (string)$rtpSettings['rtp_scope'] : 'global';
		$useRtpEngine = false;
		if ($rtpScope === 'game') {
			$useRtpEngine = (isset($gameData['prize_slab_mode']) && (string)$gameData['prize_slab_mode'] === 'rtp_based_prize');
		} else {
			$useRtpEngine = !empty($rtpSettings['is_rtp_enabled']);
		}

		if ($useRtpEngine) {
			$resultData = $this->prizeCalculationScratchRtp($gameData, $rtpSettings, $session);
			if ($rtpScope === 'game'):
				$resultData['used_rtp'] = isset($gameData['rtp_config']['target_rtp_percent'])
					? $gameData['rtp_config']['target_rtp_percent']
					: (isset($rtpSettings['rtp_config']['target_rtp_percent']) ? $rtpSettings['rtp_config']['target_rtp_percent'] : 0);
			else:
				$resultData['used_rtp'] = isset($rtpSettings['rtp_config']['target_rtp_percent'])
					? $rtpSettings['rtp_config']['target_rtp_percent']
					: 0;
			endif;
			return $resultData;
		}

		$pityResult = $this->applyConsecutiveLossPityWin($gameData, $session);
		if (!empty($pityResult)) {
			return $pityResult;
		}

		return array(
			'winning_status' => 'N',
			'winning_type'   => '',
			'winning_amount' => 0.0,
			'used_rtp'       => 0,
			'scratch_result' => array(),
		);
	}

	private function applyConsecutiveLossPityWin($gameData = array(), $session = null)
	{
		$usersId = (int)$this->input->post('users_id');
		$lossStreak = $this->getUserConsecutiveLossCount($usersId);
		if ($lossStreak < $this->rtpMaxConsecutiveLosses($gameData)) {
			return null;
		}

		$minPrize = $this->getMinimumGamePrizeAmount($gameData);
		if ($minPrize <= 0) {
			return null;
		}

		return array(
			'winning_status' => 'Y',
			'winning_type'   => 'normal_prize',
			'winning_amount' => (float)$minPrize,
			'used_rtp'       => 0,
			'scratch_result' => array(
				'prize_amount'     => (float)$minPrize,
				'pool_type'        => 'regular',
				'allocation_pass'  => 'consecutive_loss_pity',
				'rtp_loss_streak'  => $lossStreak,
			),
		);
	}

	private function getMinimumGamePrizeAmount($gameData = array(), $rtpSettings = array())
	{
		$minPrize = 0.0;
		$gameType = isset($gameData['game_type']) ? (int)$gameData['game_type'] : 0;
		if ($gameType <= 0) {
			$gameType = 10;
		}

		for ($i = 1; $i <= $gameType; $i++) {
			$key = 'straight_prize_' . $i;
			if (!empty($gameData[$key]) && is_numeric($gameData[$key])) {
				$amount = (float)$gameData[$key];
				if ($amount > 0 && ($minPrize <= 0 || $amount < $minPrize)) {
					$minPrize = $amount;
				}
			}
		}

		if ($minPrize <= 0) {
			$slabs = isset($rtpSettings['rtp_prize_slabs']) ? $rtpSettings['rtp_prize_slabs'] : array();
			if (!empty($gameData['rtp_prize_slabs'])) {
				$slabs = $gameData['rtp_prize_slabs'];
			}
			foreach ((array)$slabs as $slab) {
				$slab = $this->rtpNormalizeSlabRow($this->rtpNormalizeDocument($slab));
				if (empty($slab['is_active']) || (float)$slab['prize_amount'] <= 0) {
					continue;
				}
				if ($minPrize <= 0 || (float)$slab['prize_amount'] < $minPrize) {
					$minPrize = (float)$slab['prize_amount'];
				}
			}
		}

		return $minPrize;
	}

	private function prizeCalculationScratchRtp($gameData = array(), $rtpSettings = array(), $session = null)
	{
		$awardPlan = $this->buildRtpAwardPlan($gameData, $rtpSettings, $session);

		if (!empty($awardPlan['should_win']) && (float)$awardPlan['prize_amount'] > 0) {
			$prizeAmountData = $this->mongodb_client->getDocument(
				'single',
				'uw_scratch_win_prize_amounts',
				array(
					'products_oid' => new MongoDB\BSON\ObjectID($gameData['_id']['$id']),
					'created_at'   => array(
						'$gte' => strtotime(date('Y-m-d 00:00:00')),
						'$lte' => strtotime(date('Y-m-d 23:59:59')),
					),
				),
				$session
			);

			$this->gamePrizeCalculation(
				is_array($prizeAmountData) ? $prizeAmountData : array(),
				$gameData,
				array('prize' => (float)$awardPlan['prize_amount']),
				$session
			);
		}

		$winningStatus = !empty($awardPlan['should_win']) ? 'Y' : 'N';
		$winningAmount = ($winningStatus === 'Y') ? (float)$awardPlan['prize_amount'] : 0.0;

		return array(
			'winning_status' => $winningStatus,
			'winning_type'   => isset($awardPlan['winning_type']) ? $awardPlan['winning_type'] : 'normal_prize',
			'winning_amount' => $winningAmount,
			'scratch_result' => array(
				'prize_amount'      => $winningAmount,
				'pool_type'         => isset($awardPlan['pool_type']) ? $awardPlan['pool_type'] : 'regular',
				'allocation_pass'   => isset($awardPlan['allocation_pass']) ? $awardPlan['allocation_pass'] : 'rtp_loss',
				'rtp_global_sales'  => isset($awardPlan['global_sales']) ? $awardPlan['global_sales'] : 0,
				'rtp_loss_streak'   => isset($awardPlan['loss_streak']) ? $awardPlan['loss_streak'] : 0,
			),
		);
	}

	private function gamePrizeCalculation($prizeAmountData = array(), $gameData = array(), $winningNumbers = array(), $session = null)
	{
		$tblName = 'uw_scratch_win_prize_amounts';
		$winningAmount = isset($winningNumbers['prize']) ? (int)$winningNumbers['prize'] : 0;
		if($winningAmount <= 0):
			return;
		endif;

		$a = isset($prizeAmountData['prize_repeat_details']) && is_array($prizeAmountData['prize_repeat_details'])
			? $prizeAmountData['prize_repeat_details']
			: array();

		$newTs        = strtotime(date('Y-m-d H:i:s'));
		$tierMerged   = array();
		foreach ($a as $prizeItem):
			$existingWa = is_object($prizeItem)
				? (int)$prizeItem->winning_amount
				: (int)(isset($prizeItem['winning_amount']) ? $prizeItem['winning_amount'] : 0);
			$repeatCnt = is_object($prizeItem)
				? (int)$prizeItem->prize_repeat_count
				: (int)(isset($prizeItem['prize_repeat_count']) ? $prizeItem['prize_repeat_count'] : 0);
			$rowStatus = is_object($prizeItem) && isset($prizeItem->status)
				? (string)$prizeItem->status
				: (isset($prizeItem['status']) ? (string)$prizeItem['status'] : 'A');
			$createdAt = is_object($prizeItem) && isset($prizeItem->created_at)
				? (int)$prizeItem->created_at
				: (int)(isset($prizeItem['created_at']) ? $prizeItem['created_at'] : $newTs);
			$tierKey = (string)$existingWa;
			if (!isset($tierMerged[$tierKey])):
				$tierMerged[$tierKey] = array(
					'winning_amount'     => $existingWa,
					'prize_repeat_count' => $repeatCnt,
					'status'             => $rowStatus,
					'created_at'         => $createdAt,
				);
			else:
				$tierMerged[$tierKey]['prize_repeat_count'] += $repeatCnt;
			endif;
		endforeach;

		$newKey = (string)(int)$winningAmount;
		if(isset($tierMerged[$newKey])):
			$tierMerged[$newKey]['prize_repeat_count'] = (int)$tierMerged[$newKey]['prize_repeat_count'] + 1;
			$tierMerged[$newKey]['created_at']         = $newTs;
		else:
			$tierMerged[$newKey] = array(
				'winning_amount'     => (int)$winningAmount,
				'prize_repeat_count' => 1,
				'status'             => 'A',
				'created_at'         => $newTs,
			);
		endif;

		ksort($tierMerged, SORT_NUMERIC);
		$rangelist = array_values($tierMerged);

		$param['prize_repeat_details'] = $rangelist;
		$currentAmount = isset($prizeAmountData['amount']) ? (int)$prizeAmountData['amount'] : 0;
		$param['amount'] = $currentAmount + (int)$winningAmount;
		if(!empty($prizeAmountData) && !empty($prizeAmountData['_id']) && isset($prizeAmountData['_id']['$id'])):
			$param['updated_at'] = strtotime(date('Y-m-d H:i:s'));
			if ($session !== null) {
				$this->mongodb_client->updateDocument($tblName, ['_id' => new MongoDB\BSON\ObjectID($prizeAmountData['_id']['$id'])], ['$set' => $param], $session);
			} else {
				$this->geneal_model->editData($tblName, $param, '_id', new MongoDB\BSON\ObjectID($prizeAmountData['_id']['$id']));
			}
		else:
			$param['products_oid'] = new MongoDB\BSON\ObjectID($gameData['_id']['$id']);
			$param['created_at']   = strtotime(date('Y-m-d H:i:s'));
			$param['status']       = 'A';
			if ($session !== null) {
				$this->mongodb_client->insertDocument($tblName, $param, $session);
			} else {
				$this->common_model->addData($tblName, $param);
			}
		endif;
		
	}
	private function rtpMaxConsecutiveLosses($gameData='')
	{	
		$notWinningCount = isset($gameData['not_winning_count']) ? $gameData['not_winning_count'] : null;
		if(!empty($notWinningCount)):
			$validCounts = array_values(array_filter($notWinningCount, 'is_numeric'));
			if (!empty($validCounts)) {
				shuffle($validCounts);
				return (int) $validCounts[0];
			}
		endif;
		return 10;
	}

	private function getActivePityPrizeCandidates($slabs = array())
	{
		$candidates = array();
		if (!is_array($slabs)) {
			return $candidates;
		}

		foreach ($slabs as $slabRow) {
			$slabRow = $this->rtpNormalizeDocument($slabRow);
			if (!is_array($slabRow) || empty($slabRow['is_active']) || (float)$slabRow['prize_amount'] <= 0) {
				continue;
			}
			$candidates[] = array(
				'pool_type'    => isset($slabRow['pool_type']) ? $slabRow['pool_type'] : 'regular',
				'prize_amount' => (float)$slabRow['prize_amount'],
				'remaining'    => 1,
				'target'       => 1,
				'is_mandatory' => !empty($slabRow['is_mandatory']) ? 1 : 0,
			);
		}

		return $candidates;
	}

	private function loadGlobalRtpSettingsForOrder($gameData = array(), $session = null)
	{
		$row = $this->mongodb_client->getDocument(
			'single',
			'uw_scratch_win_rtp_settings',
			array('settings_key' => 'global'),
			$session
		);
		$row = $this->rtpNormalizeDocument($row);
		if (!is_array($row) || empty($row)) {
			return array('is_rtp_enabled' => 0);
		}

		$rtpConfig = isset($row['rtp_config']) ? $this->rtpNormalizeDocument($row['rtp_config']) : array();
		if (!is_array($rtpConfig)) {
			$rtpConfig = array();
		}

		$isRtpEnabled = 0;
		if (isset($row['is_rtp_enabled'])) {
			$isRtpEnabled = !empty($row['is_rtp_enabled']) ? 1 : 0;
		} elseif (isset($rtpConfig['is_rtp_enabled'])) {
			$isRtpEnabled = !empty($rtpConfig['is_rtp_enabled']) ? 1 : 0;
		}

		$slabs = isset($row['rtp_prize_slabs']) ? $row['rtp_prize_slabs'] : array();
		if (is_object($slabs)) {
			$slabs = (array)$slabs;
		}
		$normalizedSlabs = array();
		if (is_array($slabs)) {
			foreach ($slabs as $slab) {
				$normalizedSlabs[] = $this->rtpNormalizeSlabRow($this->rtpNormalizeDocument($slab));
			}
		}

		if(isset($row['rtp_scope']) && $row['rtp_scope'] == 'game'):
			if (!empty($gameData['rtp_config'])) {
				$rtpConfig = $this->rtpNormalizeDocument($gameData['rtp_config']);
			}
			if (!empty($gameData['rtp_prize_slabs'])) {
				$gameSlabs = $this->rtpNormalizeDocument($gameData['rtp_prize_slabs']);
				if (is_object($gameSlabs)) {
					$gameSlabs = (array)$gameSlabs;
				}
				$normalizedSlabs = array();
				if (is_array($gameSlabs)) {
					foreach ($gameSlabs as $slab) {
						$normalizedSlabs[] = $this->rtpNormalizeSlabRow($this->rtpNormalizeDocument($slab));
					}
				}
			}
		endif;

		return array(
			'is_rtp_enabled'  => $isRtpEnabled,
			'rtp_scope'	      => isset($row['rtp_scope']) ? (string)$row['rtp_scope'] : 'global',
			'rtp_config'      => $this->rtpNormalizeRtpConfig($rtpConfig),
			'rtp_prize_slabs' => $normalizedSlabs,
		);

	}

	private function rtpNormalizeDocument($value)
	{
		if ($value instanceof MongoDB\Model\BSONDocument || $value instanceof ArrayObject) {
			return $value->getArrayCopy();
		}
		if (is_object($value)) {
			return json_decode(json_encode($value), true);
		}
		return $value;
	}

	private function rtpNormalizeRtpConfig($config)
	{
		$config = $this->rtpNormalizeDocument($config);
		if (!is_array($config)) {
			$config = array();
		}
		unset($config['is_rtp_enabled'], $config['average_daily_sales']);

		return array(
			'ticket_price'                 => (float)(isset($config['ticket_price']) ? $config['ticket_price'] : 3),
			'target_rtp_percent'           => (float)(isset($config['target_rtp_percent']) ? $config['target_rtp_percent'] : 70),
			'regular_pool_percent'           => (float)(isset($config['regular_pool_percent']) ? $config['regular_pool_percent'] : 70),
			'reserve_pool_percent'           => (float)(isset($config['reserve_pool_percent']) ? $config['reserve_pool_percent'] : 20),
			'big_pool_percent'               => (float)(isset($config['big_pool_percent']) ? $config['big_pool_percent'] : 10),
			'regular_pool_release_percent'   => (float)(isset($config['regular_pool_release_percent']) ? $config['regular_pool_release_percent'] : 100),
			'reserve_pool_release_percent'   => (float)(isset($config['reserve_pool_release_percent']) ? $config['reserve_pool_release_percent'] : 100),
			'big_pool_release_percent'       => (float)(isset($config['big_pool_release_percent']) ? $config['big_pool_release_percent'] : 100),
		);
	}

	private function rtpNormalizeSlabRow($slab)
	{
		$slab = $this->rtpNormalizeDocument($slab);
		if (!is_array($slab)) {
			$slab = array();
		}
		$poolType = isset($slab['pool_type']) ? trim((string)$slab['pool_type']) : 'regular';
		if (!in_array($poolType, array('regular', 'reserve', 'big'), true)) {
			$poolType = 'regular';
		}

		return array(
			'pool_type'               => $poolType,
			'prize_amount'            => (float)(isset($slab['prize_amount']) ? $slab['prize_amount'] : 0),
			'distribution_percentage' => (float)(isset($slab['distribution_percentage']) ? $slab['distribution_percentage'] : 0),
			'is_high_prize'           => !empty($slab['is_high_prize']) ? 1 : ($poolType === 'reserve' ? 1 : 0),
			'is_big_prize'            => !empty($slab['is_big_prize']) ? 1 : ($poolType === 'big' ? 1 : 0),
			'is_mandatory'            => !empty($slab['is_mandatory']) ? 1 : 0,
			'is_active'               => !isset($slab['is_active']) || !empty($slab['is_active']) ? 1 : 0,
		);
	}

	private function rtpGroupSlabsByPool($slabs)
	{
		$grouped = array('regular' => array(), 'reserve' => array(), 'big' => array());
		if (!is_array($slabs)) {
			return $grouped;
		}
		foreach ($slabs as $slab) {
			$slab = $this->rtpNormalizeSlabRow($slab);
			$grouped[$slab['pool_type']][] = $slab;
		}
		return $grouped;
	}

	private function rtpRound2($value)
	{
		return round((float)$value, 2);
	}

	private function rtpSlabKey($prizeAmount)
	{
		return (string)$this->rtpRound2($prizeAmount);
	}

	private function rtpAggregateScratchWinSales($dateStart, $dateEnd ,$gameData= array(),$session = null)
	{
		$saleDouble = array('$toDouble' => array('$ifNull' => array('$total_price', 0)));
		$query = array(
			array('$match' => array(
				'created_at' => array('$gte' => (int)$dateStart, '$lte' => (int)$dateEnd),
				'products_oid' => new MongoDB\BSON\ObjectID($gameData['_id']['$id'])
			)),
			array('$group' => array(
				'_id'         => null,
				'order_count' => array('$sum' => array('$cond' => array(
					array('$ne' => array('$status', 'CL')),
					1,
					0,
				))),
				'daily_sales' => array('$sum' => array('$cond' => array(
					array('$ne' => array('$status', 'CL')),
					$saleDouble,
					0,
				))),
			)),
		);

		$rows = $this->common_model->mongo_db->aggregate('uw_scratch_win_orders', $query, array('batchSize' => 4));
		if (!is_array($rows) || empty($rows[0])) {
			return array('daily_sales' => 0.0, 'order_count' => 0);
		}
		$row = $this->rtpNormalizeDocument($rows[0]);
		return array(
			'daily_sales' => $this->rtpRound2(max(0, (float)(isset($row['daily_sales']) ? $row['daily_sales'] : 0))),
			'order_count' => max(0, (int)(isset($row['order_count']) ? $row['order_count'] : 0)),
		);
	}

	private function rtpAggregateGlobalDistributedWinners($gameData="",$session="")
	{
		$dateStart = strtotime(date('Y-m-d 00:00:00'));
		$dateEnd = strtotime(date('Y-m-d 23:59:59'));
		$whereCon = array(
				'where' => array(
					'products_oid' => new MongoDB\BSON\ObjectID($gameData['_id']['$id']),
					'created_at'   => array(
						'$gte' => strtotime(date('Y-m-d 00:00:00')),
						'$lte' => strtotime(date('Y-m-d 23:59:59')),
					),
				),
			);

		$rows = $this->mongodb_client->getDocument('multiple','uw_scratch_win_prize_amounts',$whereCon['where'] , $session);
		 
		if (!is_array($rows)) {
			$rows = array();
		}

		$byPrize = array();
		$totalPayout = 0.0;

		foreach ($rows as $row) {
			$row = $this->rtpNormalizeDocument($row);
			if (!is_array($row)) {
				continue;
			}
			$repeatDetails = isset($row['prize_repeat_details']) ? $row['prize_repeat_details'] : array();
			if (is_object($repeatDetails)) {
				$repeatDetails = (array)$repeatDetails;
			}
			if (!is_array($repeatDetails)) {
				continue;
			}
			foreach ($repeatDetails as $tier) {
				$tier = $this->rtpNormalizeDocument($tier);
				if (!is_array($tier)) {
					continue;
				}
				$prizeAmount = (float)(isset($tier['winning_amount']) ? $tier['winning_amount'] : 0);
				$winners = (int)(isset($tier['prize_repeat_count']) ? $tier['prize_repeat_count'] : 0);
				if ($prizeAmount <= 0 || $winners <= 0) {
					continue;
				}
				$key = $this->rtpSlabKey($prizeAmount);
				if (!isset($byPrize[$key])) {
					$byPrize[$key] = array('prize_amount' => $prizeAmount, 'winners' => 0, 'payout' => 0.0);
				}
				$byPrize[$key]['winners'] += $winners;
				$byPrize[$key]['payout'] = $this->rtpRound2($byPrize[$key]['payout'] + ($prizeAmount * $winners));
				$totalPayout = $this->rtpRound2($totalPayout + ($prizeAmount * $winners));
			}

			$bigPrizes = isset($row['big_prize']) ? $row['big_prize'] : array();
			if (is_object($bigPrizes)) {
				$bigPrizes = (array)$bigPrizes;
			}
			if (is_array($bigPrizes)) {
				foreach ($bigPrizes as $bigPrize) {
					$bigPrize = $this->rtpNormalizeDocument($bigPrize);
					if (!is_array($bigPrize)) {
						continue;
					}
					$prizeAmount = (float)(isset($bigPrize['winning_amount']) ? $bigPrize['winning_amount'] : 0);
					if ($prizeAmount <= 0) {
						continue;
					}
					$bigSource = isset($bigPrize['source']) ? strtolower(trim((string)$bigPrize['source'])) : '';
					// Manual / attack-mode should not consume RTP Big Pool slab quotas.
					if ($bigSource === 'manual' || $bigSource === 'attack_mode') {
						continue;
					}
					$key = $this->rtpSlabKey($prizeAmount);
					if (!isset($byPrize[$key])) {
						$byPrize[$key] = array('prize_amount' => $prizeAmount, 'winners' => 0, 'payout' => 0.0);
					}
					$byPrize[$key]['winners'] += 1;
					$byPrize[$key]['payout'] = $this->rtpRound2($byPrize[$key]['payout'] + $prizeAmount);
					$totalPayout = $this->rtpRound2($totalPayout + $prizeAmount);
				}
			}
		}

		return array(
			'by_prize'      => $byPrize,
			'total_payout'  => $totalPayout,
			'total_winners' => array_sum(array_map(function ($entry) {
				return (int)$entry['winners'];
			}, $byPrize)),
		);
	}

	private function rtpAllocateGroupWinnerCounts(&$group, $poolBudget)
	{
		$poolBudget = (float)$poolBudget;
		if (empty($group) || $poolBudget <= 0) {
			foreach ($group as &$slab) {
				$slab['winners'] = 0;
			}
			unset($slab);
			return;
		}

		$pctSum = 0.0;
		foreach ($group as $slab) {
			if (empty($slab['is_active'])) {
				continue;
			}
			$pctSum += (float)$slab['distribution_percentage'];
		}
		if ($pctSum <= 0) {
			foreach ($group as &$slab) {
				$slab['winners'] = 0;
			}
			unset($slab);
			return;
		}

		$infos = array();
		foreach ($group as $idx => &$slab) {
			if (empty($slab['is_active']) || (float)$slab['distribution_percentage'] <= 0) {
				$slab['winners'] = 0;
				continue;
			}
			$prize = (float)$slab['prize_amount'];
			if ($prize <= 0) {
				$slab['winners'] = 0;
				continue;
			}
			$share = (float)$slab['distribution_percentage'] / $pctSum;
			$ideal = ($share * $poolBudget) / $prize;
			$base = (int)floor($ideal);
			$slab['winners'] = $base;
			$infos[] = array('idx' => $idx, 'remainder' => $ideal - $base);
		}
		unset($slab);

		$leftover = $poolBudget;
		foreach ($group as $slab) {
			if (empty($slab['is_active'])) {
				continue;
			}
			$leftover -= (int)$slab['winners'] * (float)$slab['prize_amount'];
		}
		$leftover = $this->rtpRound2($leftover);

		$guard = 0;
		while ($leftover > 0 && $guard < 1000) {
			$guard++;
			usort($infos, function ($a, $b) use ($group, $leftover) {
				$prizeA = (float)$group[$a['idx']]['prize_amount'];
				$prizeB = (float)$group[$b['idx']]['prize_amount'];
				if ($prizeA > $leftover + 0.0001 && $prizeB <= $leftover + 0.0001) {
					return 1;
				}
				if ($prizeB > $leftover + 0.0001 && $prizeA <= $leftover + 0.0001) {
					return -1;
				}
				if ($b['remainder'] !== $a['remainder']) {
					return ($b['remainder'] > $a['remainder']) ? 1 : -1;
				}
				return ($prizeA < $prizeB) ? -1 : 1;
			});

			$picked = null;
			foreach ($infos as $info) {
				$prize = (float)$group[$info['idx']]['prize_amount'];
				if ($prize > 0 && $prize <= $leftover + 0.0001) {
					$picked = $info;
					break;
				}
			}
			if ($picked === null) {
				break;
			}

			$group[$picked['idx']]['winners'] = (int)$group[$picked['idx']]['winners'] + 1;
			$leftover = $this->rtpRound2($leftover - (float)$group[$picked['idx']]['prize_amount']);
			foreach ($infos as &$info) {
				if ($info['idx'] === $picked['idx']) {
					$info['remainder'] -= 1;
				}
			}
			unset($info);
		}
	}

	private function rtpComputePoolAllocation($releasedBudget, $slabs)
	{
		$group = array();
		foreach ($slabs as $idx => $slab) {
			if (empty($slab['is_active']) || (float)$slab['distribution_percentage'] <= 0) {
				continue;
			}
			$group[$idx] = array(
				'prize_amount'            => (float)$slab['prize_amount'],
				'distribution_percentage' => (float)$slab['distribution_percentage'],
				'is_active'               => 1,
				'winners'                 => 0,
			);
		}

		$this->rtpAllocateGroupWinnerCounts($group, $releasedBudget);

		$results = array();
		foreach ($slabs as $idx => $slab) {
			$winners = isset($group[$idx]['winners']) ? (int)$group[$idx]['winners'] : 0;
			$results[] = array(
				'pool_type'               => $slab['pool_type'],
				'prize_amount'            => (float)$slab['prize_amount'],
				'distribution_percentage' => (float)$slab['distribution_percentage'],
				'is_mandatory'            => !empty($slab['is_mandatory']) ? 1 : 0,
				'is_active'               => !empty($slab['is_active']) ? 1 : 0,
				'winners'                 => $winners,
				'payout'                  => $this->rtpRound2($winners * (float)$slab['prize_amount']),
			);
		}

		return $results;
	}

	private function rtpComputeScenario($salesAmount, $rtpConfig, $slabsByPool)
	{
		$salesAmount = (float)$salesAmount;
		$rtpConfig = $this->rtpNormalizeRtpConfig($rtpConfig);
		$totalBudget = $this->rtpRound2($salesAmount * ($rtpConfig['target_rtp_percent'] / 100));

		$poolMeta = array(
			'regular' => array('percent' => $rtpConfig['regular_pool_percent'], 'release_percent' => $rtpConfig['regular_pool_release_percent']),
			'reserve' => array('percent' => $rtpConfig['reserve_pool_percent'], 'release_percent' => $rtpConfig['reserve_pool_release_percent']),
			'big'     => array('percent' => $rtpConfig['big_pool_percent'], 'release_percent' => $rtpConfig['big_pool_release_percent']),
		);

		$allSlabs = array();
		$totalWinners = 0;
		$totalPayout = 0.0;

		foreach (array('regular', 'reserve', 'big') as $poolKey) {
			$meta = $poolMeta[$poolKey];
			$poolBudget = $this->rtpRound2($totalBudget * ($meta['percent'] / 100));
			$releasePct = max(0, min(100, (float)$meta['release_percent']));
			$releasedBudget = $this->rtpRound2($poolBudget * ($releasePct / 100));
			$poolSlabs = isset($slabsByPool[$poolKey]) ? $slabsByPool[$poolKey] : array();
			$poolResult = $this->rtpComputePoolAllocation($releasedBudget, $poolSlabs);
			foreach ($poolResult as $slabRow) {
				$allSlabs[] = $slabRow;
				$totalWinners += (int)$slabRow['winners'];
				$totalPayout += (float)$slabRow['payout'];
			}
		}

		return array(
			'total_budget'  => $totalBudget,
			'total_winners' => $totalWinners,
			'total_payout'  => $this->rtpRound2($totalPayout),
			'slabs'         => $allSlabs,
		);
	}

	private function getUserConsecutiveLossCount($usersId)
	{
		$usersId = (int)$usersId;
		if ($usersId <= 0) {
			return 0;
		}

		$whereCon = array('where' => array(
			'users_id' => $usersId,
			'status'   => array('$in' => array('A', 'REDEEMED')),
		));
		$orders = $this->common_model->getData(
			'multiple',
			'uw_scratch_win_orders',
			$whereCon,
			array('_id' => -1),
			10,
			0
		);
		if (!is_array($orders) || empty($orders)) {
			return 0;
		}

		$streak = 0;
		foreach ($orders as $order) {
			$order = $this->rtpNormalizeDocument($order);
			if (!is_array($order)) {
				continue;
			}
			$winStatus = isset($order['winning_status']) ? (string)$order['winning_status'] : 'N';
			$winAmount = isset($order['winning_amount']) ? (float)$order['winning_amount'] : 0.0;
			if ($winStatus !== 'Y' || $winAmount <= 0) {
				$streak++;
			} else {
				break;
			}
		}
		return $streak;
	}

	private function buildRtpAwardPlan($gameData, $rtpSettings, $session = null)
	{
		$orderQty = max(1, (int)($this->input->post('qty') ?: 1));
		$orderAmount = (float)(isset($gameData['price']) ? $gameData['price'] : 0) * $orderQty;
		$dateStart = strtotime(date('Y-m-d 00:00:00'));
		$dateEnd = strtotime(date('Y-m-d 23:59:59'));

		$salesSummary = $this->rtpAggregateScratchWinSales($dateStart, $dateEnd ,$gameData ,$session);
		$globalSales  = (float)(isset($salesSummary['daily_sales']) ? $salesSummary['daily_sales'] : 0) + $orderAmount;
		$distributed  = $this->rtpAggregateGlobalDistributedWinners($gameData ,$session); 
		
		$distributedByPrize = isset($distributed['by_prize']) ? $distributed['by_prize'] : array();
		$distributedPayout = (float)(isset($distributed['total_payout']) ? $distributed['total_payout'] : 0);
		$distributedWinners = (int)(isset($distributed['total_winners']) ? $distributed['total_winners'] : 0);

		$rtpConfig = isset($rtpSettings['rtp_config']) ? $rtpSettings['rtp_config'] : array();
		if($rtpSettings['rtp_scope'] === 'game' && !empty($gameData['rtp_config']['target_rtp_percent'])):
			$rtpConfig['target_rtp_percent'] = $gameData['rtp_config']['target_rtp_percent'];
		endif;
		
		$slabsByPool = $this->rtpGroupSlabsByPool(isset($rtpSettings['rtp_prize_slabs']) ? $rtpSettings['rtp_prize_slabs'] : array());
		$scenario = $this->rtpComputeScenario($globalSales, $rtpConfig, $slabsByPool);
     
		$slabCandidates = array();
		foreach ($scenario['slabs'] as $slabRow) {
			if (empty($slabRow['is_active']) || (float)$slabRow['prize_amount'] <= 0) {
				continue;
			}
			$key = $this->rtpSlabKey($slabRow['prize_amount']);
			$targetWinners = (int)(isset($slabRow['winners']) ? $slabRow['winners'] : 0);
			$givenWinners = isset($distributedByPrize[$key]['winners']) ? (int)$distributedByPrize[$key]['winners'] : 0;
			$remaining = $targetWinners - $givenWinners;
			if ($remaining <= 0) {
				continue;
			}
			$slabCandidates[] = array(
				'pool_type'    => $slabRow['pool_type'],
				'prize_amount' => (float)$slabRow['prize_amount'],
				'remaining'    => $remaining,
				'target'       => $targetWinners,
				'is_mandatory' => !empty($slabRow['is_mandatory']) ? 1 : 0,
			);
		}

		$usersId = (int)$this->input->post('users_id');
		$lossStreak = $this->getUserConsecutiveLossCount($usersId);
		$forcePityWin = ($lossStreak >= $this->rtpMaxConsecutiveLosses($gameData));

		$plan = array(
			'should_win'     => false,
			'prize_amount'   => 0.0,
			'pool_type'      => 'regular',
			'winning_type'   => 'normal_prize',
			'allocation_pass'=> 'rtp_loss',
			'global_sales'   => $globalSales,
			'loss_streak'    => $lossStreak,
		);

		
		if (empty($slabCandidates) && !$forcePityWin) {
			return $plan;
		}

		$selected = null;
		if ($forcePityWin) {
			$pityPool = array_values(array_filter($slabCandidates, function ($row) {
				return $row['pool_type'] === 'regular';
			}));
			if (empty($pityPool)) {
				$pityPool = $slabCandidates;
			}
			if (empty($pityPool)) {
				$pityPool = array_values(array_filter(
					$this->getActivePityPrizeCandidates(isset($scenario['slabs']) ? $scenario['slabs'] : array()),
					function ($row) {
						return $row['pool_type'] === 'regular';
					}
				));
			}
			if (empty($pityPool)) {
				$pityPool = $this->getActivePityPrizeCandidates(isset($scenario['slabs']) ? $scenario['slabs'] : array());
			}
			if (!empty($pityPool)) {
				usort($pityPool, function ($a, $b) {
					if ($a['prize_amount'] === $b['prize_amount']) {
						return $b['remaining'] <=> $a['remaining'];
					}
					return $a['prize_amount'] <=> $b['prize_amount'];
				});
				$selected = $pityPool[0];
				$plan['allocation_pass'] = 'rtp_consecutive_loss';
			} else {
				$fallbackPrize = $this->getMinimumGamePrizeAmount($gameData, $rtpSettings);
				if ($fallbackPrize > 0) {
					$selected = array(
						'pool_type'    => 'regular',
						'prize_amount' => $fallbackPrize,
					);
					$plan['allocation_pass'] = 'consecutive_loss_pity';
				}
			}
		}

		if ($selected === null && !empty($slabCandidates)) {
			$targetTotalWinners = (int)(isset($scenario['total_winners']) ? $scenario['total_winners'] : 0);
			if ($distributedWinners < $targetTotalWinners) {
				usort($slabCandidates, function ($a, $b) {
					$scoreA = (int)$a['remaining'];
					$scoreB = (int)$b['remaining'];
					if (!empty($a['is_mandatory']) && $a['pool_type'] === 'big') {
						$scoreA += 1000;
					}
					if (!empty($b['is_mandatory']) && $b['pool_type'] === 'big') {
						$scoreB += 1000;
					}
					if ($scoreB !== $scoreA) {
						return $scoreB <=> $scoreA;
					}
					return $a['prize_amount'] <=> $b['prize_amount'];
				});
				$selected = $slabCandidates[0];
				$plan['allocation_pass'] = ($selected['pool_type'] === 'big') ? 'rtp_big_pool' : 'rtp_slab_quota';
			}
		}

		if ($selected === null) {
			return $plan;
		}

		$prizeAmount = (float)$selected['prize_amount'];
		$totalBudget = (float)(isset($scenario['total_budget']) ? $scenario['total_budget'] : 0);
		if (!$forcePityWin && ($distributedPayout + $prizeAmount) > ($totalBudget + 0.0001)) {
			return $plan;
		}

		$plan['should_win'] = true;
		$plan['prize_amount'] = $prizeAmount;
		$plan['pool_type'] = $selected['pool_type'];
		$plan['winning_type'] = 'normal_prize';
		return $plan;
	}

}

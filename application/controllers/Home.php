<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {
public function  __construct() 
{ 
	parent:: __construct();
	error_reporting(0);
	// $this->load->model(array('geneal_model','common_model','emailsendgrid_model','sms_model'));
	$this->load->model(array('geneal_model','common_model'));
	$this->lang->load('statictext','front');
} 
	/***********************************************************************
	** Function name 	: index
	** Developed By 	: Dilip Halder
	** Purpose 			: This function used for index
	** Date 			: 05 February 2024
	************************************************************************/ 	
	public function index()
	{  
		// $data 					= array();
		// $data['page']			= 'Home';
		// $this->layouts->set_title('Home');
		// $this->layouts->front_view('index',array(),$data);

		echo "<h1 style='text-align:center'>Coming Soon</h1>";

	} // END OF FUNCTION


	/***********************************************************************
	** Function name 	: changeWinnerStatus
	** Developed By 	: Dilip Halder
	** Purpose 			: This function used for changeWinnerStatus
	** Date 			: 08 March 2025
	************************************************************************/ 	
	
	public function changeWinnerStatus()
	{

		
		
		// $tblName 		    					 = 'uw_uwin_winner';
		// $whereCon['where']['status'] 	         = 0;		
		// $whereCon['where']['created_at']['$lte'] = date('Y-m-d',strtotime('-60 days'));		
		// $shortField  = array('voucher_id'=> 1);
		// $winnerList  = $this->common_model->getData('count',$tblName,$whereCon,$shortField);
		// echo "<pre>";
		// print_r($winnerList);
		// die();
		
		$tblName 		    		    = 'uw_uwin_winner';
		$whereCon['status'] 	        = 1;		
		$whereCon['created_at']['$lte'] = date('Y-m-d',strtotime('-60 days'));		
		$shortField  = array('voucher_id'=> 1);

		$param['status'] = '0';
		$winnerList  = $this->common_model->editMultipleDataByMultipleCondition($tblName,$param,$whereCon);

		
	}
	
	public function SendPushNotificationDelayFive(){
		try {
			$startTime = time();
			$endTime = $startTime + (5 * 60); // run for 5 minutes

			while (time() < $endTime) {
				$this->pendingorder();
				$this->pendingordersendpusheveryonehour();

				$tblName = 'uw_notifications_details';
				$query = [
					['$match' => ['push_status' => 0, 'status' => 'A']],
					['$lookup' => [
						'from' => 'uw_users',
						'localField' => 'users_id',
						'foreignField' => 'users_id',
						'as' => 'user_info'
					]],
					['$unwind' => [
						'path' => '$user_info',
						'preserveNullAndEmptyArrays' => false
					]],
					['$match' => [
						'user_info.device_type' => ['$ne' => 'ios'],
						'user_info.device_id' => ['$exists' => true, '$nin' => [null, '']]
					]],
					['$sort' => ['creation_date' => 1]],
					['$limit' => 1000],
					['$project' => [
						'_id' => 0,
						'notific_title' => 1,
						'notific_message' => 1,
						'notification_details_id' => 1,
						'creation_date' => 1,
						'device_id' => '$user_info.device_id'
					]]
				];

				$notifications = $this->common_model->getDataByMultipleAndCondition($tblName, $query);

				if (empty($notifications)) {
					echo 'No pending notifications'.PHP_EOL;
				} else {
					$this->load->helper('firebase');
					foreach ($notifications as $value) {
						$deviceId = isset($value['device_id']) ? $value['device_id'] : '';
						$notificationId = (int)$value['notification_details_id'];
						if (empty($deviceId) || empty($notificationId)) {
							continue;
						}

						$sent = sendNotification(
							$value['notific_title'],
							$value['notific_message'],
							[$deviceId],
							'',
							$notificationId
						);

						// Mark as pushed even on FCM failure to avoid infinite retry spam;
						// only keep pending if helper returns hard false due to missing token.
						if ($sent !== false) {
							$whereCon = ['notification_details_id' => $notificationId];
							$param = ['push_status' => 1, 'pushed_at' => time()];
							$this->common_model->editMultipleDataByMultipleCondition('uw_notifications_details', $param, $whereCon);
						}
					}
				}

				echo "Checked at: " . date('Y-m-d H:i:s') . PHP_EOL;
				sleep(5);
			}

			echo 'Cron execution completed after 5 minutes!';
		} catch (\Throwable $th) {
			echo 'Cron execution failed: '.$th->getMessage();
			log_message('error', '[SendPushNotificationDelayFive] '.$th->getMessage());
		}
	}

	public function triggerNotificationJob() {
		$phpBinary = defined('PHP_BINARY') && PHP_BINARY ? PHP_BINARY : 'php';
		$indexPath = rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . 'index.php';
		$cmd = escapeshellarg($phpBinary).' '.escapeshellarg($indexPath).' home SendPushNotificationDelayFive > /dev/null 2>&1 &';
		exec($cmd);
		echo json_encode(['status' => 'success', 'message' => 'Notification job started']);
	}

	public function AutoCancelNotificationJob() {
		$phpBinary = defined('PHP_BINARY') && PHP_BINARY ? PHP_BINARY : 'php';
		$indexPath = rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . 'index.php';
		$cmd = escapeshellarg($phpBinary).' '.escapeshellarg($indexPath).' home AutoCancelOrder > /dev/null 2>&1 &';
		exec($cmd);
		echo json_encode(['status' => 'success', 'message' => 'Auto Cancel Notification job started']);
	}
	// public function AutoCancelOrder(){
	// 	$startTime = time(); // Current timestamp
	// 	$endTime = $startTime + (9 * 60); // 5 minutes later
	
	// 	while (time() < $endTime) {
	// 		// $wcon['where'] =['ticket' => new MongoDB\BSON\Regex('-1', 'i')];
	// 		$wcon['where']['ticket']     = new MongoDB\BSON\Regex('-1', 'i');
	// 		$wcon['where']['created_at'] = array('$gte' => strtotime('-2 days', strtotime(date('Y-m-d 00:00'))) );
	// 		$orderdata =	$this->common_model->getData('multiple','uw_lotto_orders',$wcon);
	// 		foreach ($orderdata as $key => $value) {
	// 			$userId 		=  $value['user_id'];
	// 			$orderId 		=  $value['order_id'];
	// 			$whereCon['where']['users_id']   = (int)$userId;
	// 			$whereCon['where']['users_type'] = "Users";
	// 			$UserData 	   	   = $this->common_model->getParticularFieldByMultipleCondition($FieldList,'uw_users',$whereCon );

	// 				$user_OId  = $UserData['_id']['$id'];
	// 				// echo "<pre>";print_r($UserData);die();

	// 				$tblName           = 'uw_lotto_orders';
	// 				$whereCon['where'] = array('order_id' => $orderId , 'user_id' => (int)$userId );
	// 				$orderDetails      = $this->common_model->getData('single',$tblName,$whereCon);
	// 				// echo "<pre>";print_r($orderDetails);die();

	// 				// if(!empty($orderDetails) && $orderDetails['status'] == 'CL'):
	// 				// 	echo outPut(0,lang('SUCCESS_CODE'),lang('ORDER_CANCELLED'),$result);die();
	// 				if($orderDetails['status'] == 'A' && !isset($orderDetails['raffle_mode'])):
						
	// 					$DrawDateTime     = strtotime($orderDetails['draw_date'].' '.$orderDetails['draw_time']);
						
	// 					$DrawDateTimeMinus5 = $DrawDateTime - (5 * 60);
	// 					$currentDateTime  = strtotime(date('Y-m-d H:i'));
	// 					if($DrawDateTimeMinus5 <= $currentDateTime && $DrawDateTime > $currentDateTime):

	// 						/* updated order status */
	// 						$CancellationID = $orderDetails['_id']->{'$id'};
	// 						$param1['status']			= 'CL';
	// 						$param1['update_ip']		= currentIp();
	// 						$param1['update_date']		= (int)$this->timezone->utc_time();//currentDateTime();
	// 						$param1['refund_date']		= (int)$this->timezone->utc_time();//currentDateTime();
	// 						$param1['updated_by']		= (int)$userId;
	// 						$this->common_model->editData('uw_lotto_orders',$param1,'_id',new MongoDB\BSON\ObjectId($CancellationID));
	// 						// echo "<pre>";print_r($param1);die();

	// 						/* Generating order cancellation record in loadbalance */
	// 						$refundparam["load_balance_id"]		     =	(int)$this->common_model->getNextSequence('uw_loadBalance');
	// 						$refundparam["order_oid"] 			     =	new MongoDB\BSON\ObjectId($orderDetails['_id']->{'$id'});
	// 						$refundparam["user_oid"] 				 =	new MongoDB\BSON\ObjectId($user_OId);
	// 						$refundparam["user_id_cred"] 			 =	(int)$UserData['users_id'];
	// 						$refundparam["user_id_deb"]			 	 =	(int)0;
	// 						$refundparam["order_id"] 				 =	$orderDetails['order_id'];
	// 						$refundparam["upoints"] 				 =	(float)$orderDetails['total_price'];
	// 						$refundparam["availableArabianPoints"] 	 =	(float)$UserData['availableArabianPoints'];
	// 						$refundparam["end_balance"] 			 =	(float)$UserData['availableArabianPoints'] + (float)$orderDetails['total_price'];
	// 						$refundparam["record_type"] 			 =	'Credit';
	// 						$refundparam["narration"]				 =	'Order Cancelled';
	// 						$refundparam["remarks"]				 	 =	'Ticket ID : '.$orderDetails['order_id'];
	// 						$refundparam["creation_ip"] 			 =	$this->input->ip_address();
	// 						$refundparam["created_at"] 			 	 =	date('Y-m-d H:i');
	// 						$refundparam["created_by"] 			 	 =	(int)$UserData['users_id'];
	// 						$refundparam["status"] 				 	 =	"A";
	// 						// echo "<pre>";print_r($refundparam);die();
	// 						$this->common_model->addData('uw_loadBalance', $refundparam);

	// 						/* Balance Updated.. */
	// 						$updateBalance['availableArabianPoints'] =  +(float)$orderDetails['total_price'];;
	// 						$balanceCredit = $this->common_model->manageBalance('uw_users',$updateBalance,'users_id',(int)$userId );
	// 						$message = "order ID ".$orderDetails['order_id']." has been canceled as the order was incomplete.";
	// 						$title = "Order Canceled: Incomplete Details (".$orderDetails['order_id'].")";
	// 						$this->common_model->saveNotifications($userId,$title,$message,$orderDetails['order_id']);
	// 						// echo outPut(1,lang('SUCCESS_CODE'),lang('ORDER_CANCELLED_SUCCESSFULLY'),$result);die();
						

	// 					endif;
					
	// 				endif;
	// 			}
	// 		sleep(5);
	// 	}
		
	// }

	public function AutoCancelOrder()
	{
		$currentTime = time();
		$twoDaysAgo  = date('Y-m-d H:i', strtotime('-2 days 00:00'));

		// Safe numeric cast (Mongo Int64/Decimal128 -> float)
		$toFloat = function ($value) {
			if (is_object($value) && method_exists($value, '__toString')) {
				return (float)(string)$value;
			}
			return (float)$value;
		};
		$getMongoId = function ($idField) {
			if (is_object($idField) && isset($idField->{'$id'})) {
				return (string)$idField->{'$id'};
			}
			if (is_array($idField) && isset($idField['$id'])) {
				return (string)$idField['$id'];
			}
			return (string)$idField;
		};

		// Step 1: Active incomplete orders (last 2 days, non-raffle)
		$where = array();
		$where['where'] = [
			'ticket'      => new MongoDB\BSON\Regex('-1', 'i'),
			'status'      => 'A',
			'created_at'  => ['$gte' => $twoDaysAgo],
			'raffle_mode' => ['$exists' => false]
		];
		$where['select'] = ['_id', 'order_id', 'user_id', 'user_oid', 'draw_id', 'draw_date', 'draw_time', 'draw_date_time', 'ticket', 'total_price'];
		$orders = $this->common_model->getData('multiple', 'uw_lotto_orders', $where);

		if (empty($orders)) {
			log_message('info', '[AutoCancelOrder] No pending orders found.');
			return;
		}

		// Step 2-4: Users lookup
		$userIds = array_values(array_unique(array_filter(array_map(function ($id) {
			return (int)$id;
		}, array_column($orders, 'user_id')))));

		$userMap = [];
		if (!empty($userIds)) {
			$userWhere = array();
			$userWhere['where_in'] = ['users_id', $userIds];
			$users = $this->common_model->getData('multiple', 'uw_users', $userWhere);
			if (!empty($users)) {
				foreach ($users as $u) {
					$userMap[(int)$u['users_id']] = $u;
					$userMap[(int)$u['users_id']]['availableArabianPoints'] = $toFloat($u['availableArabianPoints'] ?? 0);
				}
			}
		}

		$todayStart = strtotime(date('Y-m-d 00:00'));

		foreach ($orders as $order) {
			try {
				$userId = (int)$order['user_id'];
				if ($userId <= 0 || !isset($userMap[$userId])) {
					log_message('error', '[AutoCancelOrder] User not found for order '.$order['order_id'].' user_id='.$userId);
					continue;
				}

				$refundAmount = $toFloat($order['total_price'] ?? 0);
				if ($refundAmount <= 0) {
					log_message('error', '[AutoCancelOrder] Invalid refund amount for order '.$order['order_id']);
					continue;
				}

				$drawWhere = array();
				$drawWhere['where'] = ['draw_id' => (int)$order['draw_id']];
				$drawResult = $this->common_model->getData('single', 'uw_products_draw_records', $drawWhere);

				$drawDateTime = 0;
				if (!empty($drawResult['draw_date']) && !empty($drawResult['draw_time'])) {
					$drawDateTime = strtotime($drawResult['draw_date'].' '.$drawResult['draw_time']);
				} elseif (!empty($order['draw_date']) && !empty($order['draw_time'])) {
					$drawDateTime = strtotime($order['draw_date'].' '.$order['draw_time']);
				}

				if (empty($drawDateTime)) {
					continue;
				}

				$drawDateTimeMinus5 = $drawDateTime - (5 * 60);
				$shouldCancel = false;

				if ($drawDateTime < $todayStart) {
					// Previous day draw → cancel
					$shouldCancel = true;
				} elseif ($drawDateTime < $currentTime) {
					// Draw already passed today → cancel
					$shouldCancel = true;
				} elseif ($drawDateTimeMinus5 <= $currentTime && $drawDateTime > $currentTime) {
					// Within 5 minutes before draw → cancel
					$shouldCancel = true;
				}

				if (!$shouldCancel) {
					continue;
				}

				$cancelId = $getMongoId($order['_id']);
				$userOid  = $getMongoId($userMap[$userId]['_id']);
				$openingBalance = $toFloat($userMap[$userId]['availableArabianPoints']);
				$endBalance     = $openingBalance + $refundAmount;

				// 1) Cancel order
				$cancelData = [
					'status'        => 'CL',
					'update_ip'     => currentIp(),
					'update_date'   => (int)$this->timezone->utc_time(),
					'refund_date'   => (int)$this->timezone->utc_time(),
					'updated_by'    => $userId,
					'cancel_reason' => 'Incomplete Details'
				];
				$this->common_model->editData(
					'uw_lotto_orders',
					$cancelData,
					'_id',
					new MongoDB\BSON\ObjectId($cancelId)
				);

				// 2) Credit wallet (reverse deducted amount)
				$updateBalance = ['availableArabianPoints' => $refundAmount];
				$this->common_model->manageBalance('uw_users', $updateBalance, 'users_id', $userId);

				// Keep in-memory balance correct for multiple refunds of same user
				$userMap[$userId]['availableArabianPoints'] = $endBalance;

				// 3) Load balance ledger (same structure as manual Order Cancelled)
				$refundData = [
					'load_balance_id'        => (int)$this->common_model->getNextSequence('uw_loadBalance'),
					'order_oid'              => new MongoDB\BSON\ObjectId($cancelId),
					'user_oid'               => new MongoDB\BSON\ObjectId($userOid),
					'user_id_cred'           => (int)$userId,
					'user_id_deb'            => (int)0,
					'order_id'               => $order['order_id'],
					'upoints'                => $refundAmount,
					'availableArabianPoints' => $openingBalance,
					'end_balance'            => $endBalance,
					'record_type'            => 'Credit',
					'narration'              => 'Order Cancelled (Auto)',
					'remarks'                => 'Ticket ID : '.$order['order_id'],
					'creation_ip'            => $this->input->ip_address(),
					'created_at'             => date('Y-m-d H:i'),
					'created_by'             => (int)$userId,
					'status'                 => 'A'
				];
				$this->common_model->addData('uw_loadBalance', $refundData);

				// 4) Notification
				$title   = "Order Cancelled: Incomplete Details (".$order['order_id'].")";
				$message = "Order ID ".$order['order_id']." was automatically cancelled 5 minutes before the draw because it was incomplete.";
				$this->common_model->saveNotifications($userId, $title, $message, $order['order_id']);

				log_message('info', "[AutoCancelOrder] Order {$order['order_id']} cancelled and refunded {$refundAmount} to user {$userId}");
			} catch (\Throwable $e) {
				log_message('error', '[AutoCancelOrder] Failed for order '.($order['order_id'] ?? '').' : '.$e->getMessage());
			}
		}

		log_message('info', '[AutoCancelOrder] Completed run at '.date('Y-m-d H:i:s'));
	}

	public function pendingorder(){
		$fourMinutesAgo = date('Y-m-d H:i', strtotime('-4 minutes'));
		$currentDate = date('Y-m-d');
		$wcon = array();
		$wcon['where'] = [
			'ticket'      => new MongoDB\BSON\Regex('-1', 'i'),
			'status'      => 'A',
			'raffle_mode' => ['$exists' => false],
			'push_status' => ['$ne' => 1],
			'created_at'  => ['$lte' => $fourMinutesAgo],
			'draw_date'   => ['$gte' => $currentDate]
		];

		$orderdata = $this->common_model->getData('multiple', 'uw_lotto_orders', $wcon);
		if (empty($orderdata)) {
			log_message('info', '[pendingorder] No incomplete orders found for notification.');
			return;
		}

		foreach ($orderdata as $value) {
			try {
				$userId = (int)$value['user_id'];
				$orderId = $value['order_id'];
				if ($userId <= 0 || empty($orderId)) {
					continue;
				}

				$title = "Action Required: Complete Your Order ".$orderId;
				$message = "Your order ID ".$orderId." is incomplete. Please select a coupon number to proceed";
				$this->common_model->saveNotifications($userId, $title, $message, $orderId);

				$whereCon = ['order_id' => $orderId];
				$param = ['push_status' => 1];
				$this->common_model->editMultipleDataByMultipleCondition('uw_lotto_orders', $param, $whereCon);

				log_message('info', '[pendingorder] Notification created for incomplete order '.$orderId);
			} catch (\Throwable $e) {
				log_message('error', '[pendingorder] Failed for order '.($value['order_id'] ?? '').' : '.$e->getMessage());
			}
		}
	}

	public function pendingordersendpusheveryonehour(){
		$currentDate = date('Y-m-d');
		$ctime = date('H:i');
		$nowTs = time();

		$wcon = array();
		$wcon['where'] = [
			'ticket'      => new MongoDB\BSON\Regex('-1', 'i'),
			'status'      => 'A',
			'raffle_mode' => ['$exists' => false],
			'draw_date'   => ['$gte' => $currentDate],
			'draw_time'   => ['$gte' => $ctime]
		];

		$orderdata = $this->common_model->getData('multiple', 'uw_lotto_orders', $wcon);
		if (empty($orderdata)) {
			return;
		}

		foreach ($orderdata as $value) {
			try {
				$wcn = array();
				$wcn['where'] = [
					'order_id' => $value['order_id'],
					'notific_title' => new MongoDB\BSON\Regex('Action Required', 'i')
				];
				$shortField = array('creation_date' => -1);
				$notificationsdata = $this->common_model->getData('single', 'uw_notifications_details', $wcn, $shortField);
				if (empty($notificationsdata) || empty($notificationsdata['creation_date'])) {
					continue;
				}

				$creationDate = (int)$notificationsdata['creation_date'];
				$oneHourLater = $creationDate + (60 * 60);
				$fiftyFiveMinLater = $creationDate + (55 * 60);

				// Reminder window: 55 to 60 minutes after last incomplete-order notification
				if ($nowTs >= $fiftyFiveMinLater && $nowTs <= $oneHourLater) {
					$title = "Action Required: Complete Your Order ".$value['order_id'];
					$message = "Your order ID ".$value['order_id']." is incomplete. Please select a coupon number to proceed";
					$this->common_model->saveNotifications((int)$value['user_id'], $title, $message, $value['order_id']);
					log_message('info', '[pendingordersendpusheveryonehour] Hourly reminder for '.$value['order_id']);
				}
			} catch (\Throwable $e) {
				log_message('error', '[pendingordersendpusheveryonehour] '.$e->getMessage());
			}
		}
	}
	public function deleteOldNotificationsData()
	{
		// Step 1: Get current UTC timestamp
		$now = (int)$this->timezone->utc_time();

		// Step 2: Subtract 2 days
		$cutoffTimestamp = $now - (7 * 86400);

		// Step 3: Build raw MongoDB filter
		$filter = ['creation_date' => ['$lt' => $cutoffTimestamp]];

		// Step 4: Delete from uw_notifications
		$delete1 = $this->mongo_db->where($filter)->delete_all('uw_notifications');

		// Step 5: Delete from uw_notifications_details
		$delete2 = $this->mongo_db->where($filter)->delete_all('uw_notifications_details');

		// Step 6: Output result
		echo "✅ Deleted records older than " . date('Y-m-d H:i:s', $cutoffTimestamp) . " UTC<br>";
		echo "🗑️ uw_notifications delete result: ";
		print_r($delete1);
		echo "<br>🗑️ uw_notifications_details delete result: ";
		print_r($delete2);
	}


	// public function test()
	// {
	// 	echo "<pre>";
	// 	print_r('a');
	// 	die();
	// }


}	

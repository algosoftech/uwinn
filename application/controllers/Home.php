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
			$startTime = time(); // Current timestamp
		$endTime = $startTime + (5 * 60); // 5 minutes later
	
		while (time() < $endTime) {
			$this->pendingorder();
			$this->pendingordersendpusheveryonehour();
			$currentTime = new DateTime('now', new DateTimeZone('UTC')); 
			$currentTime->modify('-4 minutes');
			$currentTimestamp = $currentTime->getTimestamp();
			$tblName = 'uw_notifications_details';
			
			$query = [
				['$match' => ['push_status' => 0]], // Filter pending notifications
				['$lookup' => [
					'from' => 'uw_users',
					'localField' => 'users_id',
					'foreignField' => 'users_id',
					'as' => 'user_info'
				]],
				['$match' => ['user_info.device_type' => ['$ne' => 'ios']]],
				['$unwind' => '$user_info'],
				['$sort' => ['creation_date' => 1]], // Sort by creation_date ASC
				['$limit' => 1000], // Limit to 100 records
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
			
			if (!$notifications) {
				echo 'No pending notifications'.PHP_EOL;
			} else {
				$this->load->helper('firebase');
				foreach ($notifications as $value) {
					// $created_date = $value['creation_date']; // Assuming UNIX timestamp
						sendNotification($value['notific_title'], $value['notific_message'], [$value['device_id']],'',(int)$value['notification_details_id']);
						$whereCon['notification_details_id'] = (int)$value['notification_details_id'];      
						$param['push_status'] = 1;
					
	
					
				}
			}
			
			echo "Checked at: " . date('Y-m-d H:i:s') . PHP_EOL;
			sleep(5); // Wait for 5 seconds before checking again
		}
		
		echo 'Cron execution completed after 5 minutes!';
			} catch (\Throwable $th) {
				echo 'Cron execution completed after 5 minutes!'.$th;
				
			//throw $th;
		}
		
	}
	public function triggerNotificationJob() {
		// Run the PHP script in background
		exec("php /var/www/html/u-win/index.php home SendPushNotificationDelayFive > /dev/null 2>&1 &");
	
		// Send immediate response
		echo json_encode(['status' => 'success', 'message' => 'Notification job started']);
	}
	public function AutoCancelNotificationJob() {
		// Run the PHP script in background
		exec("php /var/www/html/u-win/index.php home AutoCancelOrder > /dev/null 2>&1 &");
	
		// Send immediate response
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
		// 🕒 Current time & time range setup
		$currentTime      = time();
		$fiveMinutesLater = $currentTime + (5 * 60);
		$twoDaysAgo = date('Y-m-d H:i', strtotime('-2 days 00:00'));
		// 🧩 Step 1: Fetch all active, incomplete orders whose draw is within the next 5 minutes
		$where['where'] = [
			'ticket'      => new MongoDB\BSON\Regex('-1', 'i'),   // incomplete ticket
			'status'      => 'A',                                 // active orders only
			'created_at'  => ['$gte' => $twoDaysAgo],             // last 2 days
			'raffle_mode' => ['$exists' => false]                 // skip raffle mode
		];
		$where['select'] = ['_id', 'order_id', 'user_id', 'draw_id', 'draw_date', 'draw_time', 'draw_date_time', 'ticket','total_price'];
		$orders = $this->common_model->getData('multiple', 'uw_lotto_orders', $where);
		// echo"working<pre>";print_r($where);
		// echo"<pre>";print_r($orders);
		if (empty($orders)) {
			log_message('info', '[AutoCancelOrder] No pending orders found.');
			return;
		}

		// 🧩 Step 2: Collect all unique user IDs
		$userIds = array_unique(array_column($orders, 'user_id'));
		$userIds = array_values(array_map('intval', array_filter($userIds))); // 🔥 important
		// echo"<pre>";print_r($userIds);
		// 🧩 Step 3: Fetch all user data in one go
		// $userWhere['where'] = ['users_id' => ['$in' => array_map('intval', $userIds)]];
		$userWhere['where_in'] = ['users_id', array_map('intval', $userIds)];
		$users = $this->common_model->getData('multiple', 'uw_users', $userWhere);

		// 🧩 Step 4: Create quick lookup for users
		$userMap = [];
		foreach ($users as $u) {
			$userMap[$u['users_id']] = $u;
		}

		// 🧩 Step 5: Process each order
		foreach ($orders as $order) {
			$userId = (int)$order['user_id'];
			// echo $order['order_id'].'---->'.$order['ticket'].'<br>';
			// Skip if user not found
			if (!isset($userMap[$userId])) continue;

			$user = $userMap[$userId];
			
			$drawWhere['where'] = ['draw_id' => (int)$order['draw_id'] ];
			$drawResult = $this->common_model->getData('single', 'uw_products_draw_records', $drawWhere);
			// Draw time logic
			$drawDateTime      = strtotime($drawResult['draw_date'].' '.$drawResult['draw_time']);
			$drawDateTimeMinus5 = $drawDateTime - (5 * 60);
			$todayStart = strtotime(date('Y-m-d 00:00'));
			$todayEnd   = strtotime(date('Y-m-d 23:59:59'));
			
			// echo $order['order_id'].'-5'.$drawDateTimeMinus5.'---------ccc'.$currentTime.'----------draw'.$drawDateTime.'<br>';
			if ($drawDateTime < $todayStart) {
				// echo 'previous------------------->'.$order['order_id'].'---->'.$order['ticket'].'<br>';
				// 🔴 Case 1: Draw date is from a previous day → cancel immediately
				$shouldCancel = true;

			} elseif ($drawDateTime < $currentTime) {
				// echo 'previous------------------->'.$order['order_id'].'---->'.$order['ticket'].'<br>';
				// 🔴 Case 2: Draw already happened today → cancel immediately
				$shouldCancel = true;

			} elseif ($drawDateTimeMinus5 <= $currentTime && $drawDateTime > $currentTime) {
				// echo 'current------------------->'.$order['order_id'].'---->'.$order['ticket'].'<br>';
				// 🟡 Case 3: Draw starting within next 5 minutes → cancel automatically
				$shouldCancel = true;

			} else {
				$shouldCancel = false;
			}
			// continue;	
			// Cancel if within the 5-minute window
			if ($shouldCancel) {

				$cancelId = $order['_id']->{'$id'};
				// echo"Cancel Id->".$order['order_id'].'--->'.$cancelId;
				// 🛑 Step 6: Update order status to Cancelled
				$cancelData = [
					'status'       => 'CL',
					'update_ip'    => currentIp(),
					'update_date'  => (int)$this->timezone->utc_time(),
					'refund_date'  => (int)$this->timezone->utc_time(),
					'updated_by'   => $userId,
					'cancel_reason' => 'Incomplete Details'
				];
				$this->common_model->editData(
					'uw_lotto_orders',
					$cancelData,
					'_id',
					new MongoDB\BSON\ObjectId($cancelId)
				);

				// 💵 Step 8: Update user balance
				$updateBalance['availableArabianPoints'] = (float)$order['total_price'];
				$this->common_model->manageBalance('uw_users', $updateBalance, 'users_id', (int)$userId);
				
				// updated local users available arabian points after prev cancelled order refund // new 
				$userMap[$userId]['availableArabianPoints'] += (float)$order['total_price'];

				// 💰 Step 7: Add refund record to loadBalance
				$refundData = [
					'load_balance_id'         => (int)$this->common_model->getNextSequence('uw_loadBalance'),
					'order_oid'               => new MongoDB\BSON\ObjectId($cancelId),
					'user_oid'                => new MongoDB\BSON\ObjectId($user['_id']->{'$id'}),
					'user_id_cred'            => (int)$user['users_id'],
					'user_id_deb'             => 0,
					'order_id'                => $order['order_id'],
					'upoints'                 => (float)$order['total_price'],
					'availableArabianPoints'  => (float)$user['availableArabianPoints'],
					// 'end_balance'             => (float)$user['availableArabianPoints'] + (float)$order['total_price'],
					'end_balance' 			  => $userMap[$userId]['availableArabianPoints'],
					'record_type'             => 'Credit',
					'narration'               => 'Order Cancelled (Auto)',
					'remarks'                 => 'Ticket ID: '.$order['order_id'],
					'creation_ip'             => $this->input->ip_address(),
					'created_at'              => date('Y-m-d H:i'),
					'created_by'              => (int)$user['users_id'],
					'status'                  => 'A',
					'show_on' 				  => ["Web"]
				];
				$this->common_model->addData('uw_loadBalance', $refundData);

				// 🔔 Step 9: Send notification
				$title   = "Order Cancelled: Incomplete Details (".$order['order_id'].")";
				$message = "Order ID ".$order['order_id']." was automatically cancelled 5 minutes before the draw because it was incomplete.";
				$this->common_model->saveNotifications($userId, $title, $message, $order['order_id']);

				// 🪵 Log success
				log_message('info', "[AutoCancelOrder] Order {$order['order_id']} cancelled and refunded to user {$userId}");
			}
		}

		log_message('info', '[AutoCancelOrder] Completed run at '.date('Y-m-d H:i:s'));
	}

	public function pendingorder(){
		$fiveMinutesAgo = date('Y-m-d H:i', strtotime('-4 minutes')); // 5 minutes ago
		$currentDate = date('Y-m-d'); // Today's date
		$wcon['where'] = [
			'$and' => [
				['ticket' => new MongoDB\BSON\Regex('-1', 'i')],
				['status' => 'A'],
				['raffle_mode' => ['$exists' => false]],
				['push_status' => ['$ne' => 1]],
				['created_at' => ['$lt' => $fiveMinutesAgo]], // Between 10 min ago and 5 min ago
				['draw_date' => ['$gte' => $currentDate]]  // draw_date >= today
			]
		];
	
		$orderdata = $this->common_model->getData('multiple', 'uw_lotto_orders', $wcon);
		if($orderdata ){
			foreach ($orderdata as $key => $value) {
				$title = "Action Required: Complete Your Order ".$value['order_id'];
				$message = "Your order ID ".$value['order_id']." is incomplete. Please select a coupon number to proceed";
				$this->common_model->saveNotifications((int)$value['user_id'],$title,$message,$value['order_id']);
				$whereCon['order_id'] = $value['order_id'];
				$param['push_status'] = 1;
				$this->common_model->editMultipleDataByMultipleCondition('uw_lotto_orders', $param, $whereCon);
			}
		}
		
		
	}

	public function pendingordersendpusheveryonehour(){
		// $fiveMinutesAgo = date('Y-m-d H:i', strtotime('-4 minutes')); // 5 minutes ago

		$currentDate = date('Y-m-d'); // Today's date
		$ctime = date('H:i');
		$wcon['where'] = [
			'$and' => [
				['ticket' => new MongoDB\BSON\Regex('-1', 'i')],
				['status' => 'A'],
				['raffle_mode' => ['$exists' => false]],
				// ['push_status' => ['$ne' => 1]],
				// ['created_at' => ['$lt' => $fiveMinutesAgo]], // Between 10 min ago and 5 min ago
				['draw_date' => ['$gte' => $currentDate]],  // draw_date >= today
				['draw_time' => ['$gte' => $ctime]]
			]
		];
	
		$orderdata = $this->common_model->getData('multiple', 'uw_lotto_orders', $wcon);
		if($orderdata ){
			foreach ($orderdata as $key => $value) {
				$wcn['where']=[
					'$and' => [
						['order_id' =>$value['order_id'] ],
						['notific_title' =>  new MongoDB\BSON\Regex('Action Required', 'i')],
					]
				];
				$shortField 	= array('creation_date' => -1); 
				$notificationsdata = $this->common_model->getData('single', 'uw_notifications_details', $wcn,$shortField);
				if($notificationsdata){
					
					$onehour = $notificationsdata['creation_date'] + (60 * 60);
					$firtyfivemin = $notificationsdata['creation_date'] + (55 * 60);
					// $ctime = time();
					if($onehour >= $ctime && $firtyfivemin <=strtotime($ctime)){
						$title = "Action Required: Complete Your Order ".$value['order_id'];
						$message = "Your order ID ".$value['order_id']." is incomplete. Please select a coupon number to proceed";
						$this->common_model->saveNotifications((int)$value['user_id'],$title,$message,$value['order_id']);
					}
					
				}
				
				// $whereCon['order_id'] = $value['order_id'];
				// $param['push_status'] = 1;
				// $this->common_model->editMultipleDataByMultipleCondition('uw_lotto_orders', $param, $whereCon);
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

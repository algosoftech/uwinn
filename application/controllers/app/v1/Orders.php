<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orders extends CI_Controller {
	
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
	 * * Function name  : initilize
	 * * Developed By 	: Dilip Halder
	 * * Purpose  		: This function to initialize orders.
	 * * Date 			: 08 February 2024
	 * * **********************************************************************/
	public function initialize_order()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):
			
			$user_id 				 	 = $this->input->post('user_id');
			$products_id 				 = $this->input->post('products_id');
			$quantity 	 				 = $this->input->post('quantity');
			$prize_title 	     		 = $this->input->post('prize_title');
			$straight_add_on_amount 	 = $this->input->post('straight_add_on_amount');
			$rumble_add_on_amount 	 	 = $this->input->post('rumble_add_on_amount');
			$reverse_add_on_amount 	     = $this->input->post('reverse_add_on_amount');
			$selection_values 	     	 = $this->input->post('selection_values');
			$vat_amount 	     		 = $this->input->post('vat_amount');
			$subtotal 	     		 	 = $this->input->post('subtotal');
			$total_price 	     		 = $this->input->post('total_price');
			$draw_date 	     		 	 = $this->input->post('draw_date');
			$draw_time 	     		 	 = $this->input->post('draw_time');
			$lotto_type 	     		 = $this->input->post('lotto_type');
			$users_email 	     		 = $this->input->post('users_email');
			$country_code 	     		 = $this->input->post('country_code');
			$users_mobile 	     		 = $this->input->post('users_mobile');
			$device_type 	     		 = $this->input->post('device_type');
			$app_name 	     		 	 = $this->input->post('app_name');
			$app_version 	     		 = $this->input->post('app_version');
			$ticket 	     		 	 = $this->input->post('ticket');
			$payment_mode 	     		 = $this->input->post('payment_mode');
			$pickup_point 	     	     = $this->input->post('pickup_point');
			$delivery_address 	     	 = $this->input->post('delivery_address');
			$delivery_charge 	     	 = $this->input->post('delivery_charge');
			$users_lat  				 = $this->input->post('users_lat');
			$users_long  				 = $this->input->post('users_long');
			$users_address 	 			 = $this->input->post('users_address');
			$raffle_mode 	 			 = $this->input->post('raffle_mode');

			if(empty($user_id)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
			elseif(empty($products_id)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('PRODUCT_ID_EMPTY'),$result);die();
            elseif(empty($quantity)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_PRODUCT_QTY'),$result);die();
            elseif(empty($straight_add_on_amount) && empty($rumble_add_on_amount) && empty($reverse_add_on_amount) || empty($selection_values) ):
                  echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_GAME_MODE'),$result);die();
           	// elseif(empty($vat_amount)):
            //     echo outPut(0,lang('SUCCESS_CODE'),lang('VAT_AMOUNT_EMPTY'),$result);die();
         	elseif(empty($subtotal)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('SUBTOTAL_EMPTY'),$result);die();
            elseif(empty($total_price)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('CAPTURE_AMOUNT_EMPTY'),$result);die();
            elseif(empty($draw_date)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_DRAW_DATE'),$result);die();
            elseif(empty($draw_time)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_DRAW_TIME'),$result);die();
            elseif(empty($lotto_type)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_LOTTO_TYPE'),$result);die();
         	elseif(empty($users_mobile)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_USERMOBILE'),$result);die();
            elseif(empty($device_type)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_DEVICE_TYPE'),$result);die();
            elseif(empty($app_name)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_APPNAME'),$result);die();
            elseif(empty($app_version)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_APP_VERSION'),$result);die();
            elseif(empty($ticket)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_TICKET'),$result);die();
            elseif(empty($payment_mode)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_PAYMENT_MODE'),$result);die();
            else:
            	if($payment_mode == "UPoints"):
            		$accesstype     = "";
					$balance = $this->common_model->checkBalance($accesstype,$user_id,$total_price);
				endif;

				//Draw date validation
				$fields = array('draw_date','draw_time');
				$tableName     = 'uw_products';
				$wcon['where'] = array('products_id' => (int)$products_id);
				$ProductDATA   = $this->common_model->getParticularFieldByMultipleCondition($fields,$tableName,$wcon);
				$DrawDateTime = date('Y-m-d H:i',strtotime($draw_date.' '.$draw_time));
				$TableaDrawDateTime = date('Y-m-d H:i',strtotime($ProductDATA['draw_date'].' '.$ProductDATA['draw_time']));

				if($DrawDateTime  != $TableaDrawDateTime):
					echo outPut(0,lang('FORBIDDEN_CODE'),lang('INVALID_DRAWDATE'),$result);die();
				endif;

				if(empty($pickup_point) && empty($delivery_address)):
					$pickup_point = "Pick Up From Office";
				endif;

				// Getting users detail start here ..
				$FieldList   		= array('users_name','last_name','pos_number'); 
				$tableName          = 'uw_users';
			    $whereCon['where']  = array('users_id' => (int)$user_id );
				$userDetails 		= $this->common_model->getParticularFieldByMultipleCondition($FieldList,$tableName,$whereCon);
				$first_name 		= $userDetails['users_name'];
				$last_name  		= $userDetails['last_name'];
				$pos_number  		= $userDetails['pos_number'];
				// Getting users detail end here ..
				$orderInsertID = $this->common_model->order_initialize($user_id,$products_id,$quantity,$straight_add_on_amount,$rumble_add_on_amount,$reverse_add_on_amount,$selection_values,$subtotal,$total_price,$draw_date,$draw_time,$lotto_type,$country_code,$users_mobile,$users_email,$device_type,$app_name,$app_version,$ticket,$payment_mode,$prize_title,$pickup_point,$delivery_address,$delivery_charge,$first_name,$last_name,$users_lat,$users_long,$users_address,$pos_number,$raffle_mode);

			    if(!empty($orderInsertID)):
			    	$result = $orderInsertID;
                	echo outPut(1,lang('SUCCESS_CODE'),lang('ORDER_INITIALIZED'),$result);die();
			    else:
					echo outPut(0,lang('FORBIDDEN_CODE'),lang('TRY_AGAIN'),$result);die();
			    endif;

			endif;
			echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}	

	/* * *********************************************************************
	 * * Function name  : paymentCapture
	 * * Developed By 	: Dilip Halder
	 * * Purpose  		: This function used for get Country Code
	 * * Date 			: 08 February 2024
	 * * **********************************************************************/
	public function paymentCapture()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 			= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):

			$users_id 			= $this->input->post('user_id');
			$order_id 			= $this->input->post('order_id');
			$payment_mode 		= $this->input->post('payment_mode');
			$transaction_id 	= $this->input->post('transaction_id');
			$status 			= $this->input->post('status');

			if(empty($users_id)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
            elseif(empty($order_id)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('ORDER_ID_EMPTY'),$result);die();
            elseif(empty($payment_mode)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_PAYMENT_MODE'),$result);die();
            elseif(empty($transaction_id)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_TRANSACTION_ID'),$result);die();
            elseif(empty($status)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_ORDER_STATUS'),$result);die();
            else:

        		$accesstype = '';
            	if($payment_mode == "UPoints"):
					$balance   = $this->common_model->checkBalance($accesstype,$users_id,$total_price);
				endif;
            	$orderInsertID = $this->common_model->paymentCapture($accesstype,$users_id,$order_id,$payment_mode,$transaction_id,$status);

			    if(!empty($orderInsertID)):
			    	$result = $orderInsertID;
                	echo outPut(1,lang('SUCCESS_CODE'),lang('ORDER_SUCCESS'),$result);die();
			    else:
					echo outPut(0,lang('FORBIDDEN_CODE'),lang('TRY_AGAIN'),$result);die();
			    endif;

			endif;
			echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : updateOrder
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to update order details.
	 * * Date 		   : 30 November 2024
	 * * **********************************************************************/
	public function updateOrder()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_GET));
		$result 			= 	array();

		if(requestAuthenticate(APIKEY,'POST')):

			$order_id = $this->input->post('order_id');
			$user_id  = $this->input->post('user_id');
			if(empty($user_id)):
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
			elseif(empty($order_id)):
				echo outPut(0,lang('SUCCESS_CODE'),lang('ORDER_ID_EMPTY'),$result);die();
			else:
				//getting variable from post data.
				$tblName  		   = 'uw_lotto_orders';
				$whereCon['where'] = array('order_id' => $order_id , 'user_id' => (int)$user_id );
				$orderData         = $this->common_model->getData('count',$tblName , $whereCon);
				if($orderData == 1):
				 	$updateParams["ticket"] 				 = $this->input->post('ticket');
			        $updateParams["straight_add_on_amount"]  = (float)$this->input->post('straight_add_on_amount');
			        $updateParams["rumble_add_on_amount"]    = (float)$this->input->post('rumble_add_on_amount');
			        $updateParams["reverse_add_on_amount"]   = (float)$this->input->post('reverse_add_on_amount');
					$updateParams["selection_values"] 		 = $this->input->post('selection_values');
					$updateParams["vat_amount"] 		     = (float)$this->input->post('vat_amount');
					$updateParams["total_price"] 		     = (float)$this->input->post('total_price');
					$updateParams["ticket_updated_at"] 		 = date('Y-m-d H:i');
					$this->common_model->editData($tblName , $updateParams, 'order_id', $order_id);

					$tblName  		   = 'uw_lotto_orders';
					$whereCon['where'] = array('order_id' => $order_id , 'user_id' => (int)$user_id );
					$results         = $this->common_model->getData('single',$tblName , $whereCon);

		 			echo outPut(1,lang('SUCCESS_CODE'),lang('ORDER_SUCCESS'),$results);	
				else:
		 			echo outPut(0,lang('SUCCESS_CODE'),lang('DATA_NOT_FOUND'),$results);	
				endif;

			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name  : orderHistory
	 * * Developed By 	: Dilip Halder
	 * * Purpose  		: This function used for get order History
	 * * Date 			: 11 July 2024
	 * * **********************************************************************/
	public function orderHistory()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 			= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):

			$user_id 			= $this->input->post('user_id');
			$searchBy 			= $this->input->post('search_by');
			$searchValue 		= $this->input->post('search_value');
			$itemsPerPage 		= $this->input->post('itemsPerPage');
			$pageno 			= $this->input->post('page');

			// data filter
			$date['from']		= $this->input->post('from');
			$date['to']			= $this->input->post('to');

			if(empty($user_id)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
            elseif(empty($this->input->post('itemsPerPage'))):
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_ITEMPERPAGE'),$result);die();
            elseif(empty($this->input->post('page'))):
            	echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_PAGE_NO'),$result);die();
            else:
            		
	    		$requestFrom = 'app';
				$USERDATA    = $this->common_model->userValidate($user_id,$requestFrom);
	    		$resultType  = 'count';
				$totalcount  = $this->common_model->orderDetails($user_id,$searchBy,$searchValue,$itemsPerPage,$startIndex,$resultType,$date);
				
				$itemsPerPage = $this->input->post('itemsPerPage');
				$pageno 	  = $this->input->post('page');

				// Sample long array with data
				$longArray = $totalcount;
				// Current page number (received from URL query parameter, e.g., ?page=2)
				$page = isset($pageno) ? (int)$pageno : 1;

				// Calculate total number of pages
				$totalPages = ceil($longArray / $itemsPerPage);
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

				$startIndex    = ($page - 1) * $itemsPerPage;
		 		$resultType    = '';
		 		$OrderDetails  = $this->common_model->orderDetails($user_id,$searchBy,$searchValue,$itemsPerPage,$startIndex,$resultType,$date);

			    if(!empty($OrderDetails)):
			    	$totalpage 				    = count($totalpage);
					$result['OrderDetails'] 	= $OrderDetails?$OrderDetails:array();
					$result['current_page']     = $current_page;
					$result['total_page'] 	    =   $totalpage;
                	echo outPut(1,lang('SUCCESS_CODE'),lang('ORDER_SUCCESS'),$result);die();
			    else:
					echo outPut(0,lang('FORBIDDEN_CODE'),lang('DATA_NOT_FOUND'),$result);die();
			    endif;

			endif;
			echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name  : orderCancellation
	 * * Developed By 	: Dilip Halder
	 * * Purpose  		: This function used for order Cancellation
	 * * Date 			: 11 July 2024
	 * * **********************************************************************/
	public function orderCancellation()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 			= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):

			$userId 		= $this->input->post('user_id');
			$orderId 		= $this->input->post('order_id');
			if(empty($userId)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
			elseif(empty($orderId)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('ORDER_ID_EMPTY'),$result);die();
            else:

            	$whereCon['where'] = array('users_id' => (int)$userId);
				$UserData 	   	   = $this->common_model->getParticularFieldByMultipleCondition($FieldList,'uw_users',$whereCon );

				$user_OId  = $UserData['_id']['$id'];
				// echo "<pre>";print_r($UserData);die();

            	$tblName           = 'uw_lotto_orders';
            	$whereCon['where'] = array('order_id' => $orderId , 'user_id' => (int)$userId );
            	$orderDetails      = $this->common_model->getData('single',$tblName,$whereCon);
				// echo "<pre>";print_r($orderDetails);die();

            	if(!empty($orderDetails) && $orderDetails['status'] == 'CL'):
					echo outPut(0,lang('SUCCESS_CODE'),lang('ORDER_CANCELLED'),$result);die();
				elseif($orderDetails['status'] == 'A'):
					
					$DrawDateTime     = strtotime($orderDetails['draw_date'].' '.$orderDetails['draw_time']);
					$currentDateTime  = strtotime(date('Y-m-d H:i'));
					if($DrawDateTime > $currentDateTime):

					    /* updated order status */
					    $CancellationID = $orderDetails['_id']->{'$id'};
				 	 	$param1['status']			= 'CL';
						$param1['update_ip']		= currentIp();
						$param1['update_date']		= (int)$this->timezone->utc_time();//currentDateTime();
						$param1['refund_date']		= (int)$this->timezone->utc_time();//currentDateTime();
						$param1['updated_by']		= (int)$userId;
						$this->common_model->editData('uw_lotto_orders',$param1,'_id',new MongoDB\BSON\ObjectId($CancellationID));
					 	// echo "<pre>";print_r($param1);die();

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
					    $refundparam["narration"]				 =	'Order Cancalled';
					    $refundparam["remarks"]				 	 =	'Ticket ID : '.$orderDetails['order_id'];
					    $refundparam["creation_ip"] 			 =	$this->input->ip_address();
					    $refundparam["created_at"] 			 	 =	date('Y-m-d H:i');
					    $refundparam["created_by"] 			 	 =	(int)$UserData['users_id'];
					    $refundparam["status"] 				 	 =	"A";
					 	// echo "<pre>";print_r($refundparam);die();
					    $this->common_model->addData('uw_loadBalance', $refundparam);

					    /* Balance Updated.. */
  						$updateBalance['availableArabianPoints'] =  +(float)$orderDetails['total_price'];;
 						$balanceCredit = $this->common_model->manageBalance('uw_users',$updateBalance,'users_id',(int)$userId );
						echo outPut(1,lang('SUCCESS_CODE'),lang('ORDER_CANCELLED_SUCCESSFULLY'),$result);die();
					else:
						echo outPut(0,lang('SUCCESS_CODE'),lang('ORDET_ID_INVALID'),$result);die();
					endif;
            	else:
					echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);die();
            	endif;
            endif;
			echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name  : transactionHistory
	 * * Developed By 	: Dilip Halder
	 * * Purpose  		: This function used for get order History
	 * * Date 			: 12 July 2024
	 * * **********************************************************************/
	public function transactionHistory()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 			= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):

			$user_id 			= $this->input->post('user_id');
			$searchBy 			= $this->input->post('search_by');
			$searchValue 		= $this->input->post('search_value');
			$itemsPerPage 		= $this->input->post('itemsPerPage');
			$pageno 			= $this->input->post('page');

			// data filter
			$date['from']		= $this->input->post('from');
			$date['to']			= $this->input->post('to');

			if(empty($user_id)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
            elseif(empty($this->input->post('itemsPerPage'))):
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_ITEMPERPAGE'),$result);die();
            elseif(empty($this->input->post('page'))):
            	echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_PAGE_NO'),$result);die();
            else:
            		
	    		$requestFrom = 'app';
				$USERDATA    = $this->common_model->userValidate($user_id,$requestFrom);

	    		//getting usersID
				$tblName     = 'uw_users';
				$userID      = $this->common_model->getSingleDataByParticularField(array('_id'),$tblName, 'users_id', (int)$user_id);
				$uoid        = $userID['_id']['$id'];
				// -----------------------------------------------

	    		$resultType  = 'count';
				$totalcount  = $this->common_model->transactionDetails($user_id,$searchBy,$searchValue,$itemsPerPage,$startIndex,$resultType,$date,$uoid);

				$itemsPerPage = $this->input->post('itemsPerPage');
				$pageno 	  = $this->input->post('page');

				// Sample long array with data
				$longArray = $totalcount;
				// Current page number (received from URL query parameter, e.g., ?page=2)
				$page = isset($pageno) ? (int)$pageno : 1;

				// Calculate total number of pages
				$totalPages = ceil($longArray / $itemsPerPage);
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

				$startIndex    = ($page - 1) * $itemsPerPage;
		 		$resultType    = '';
		 		$OrderDetails  = $this->common_model->transactionDetails($user_id,$searchBy,$searchValue,$itemsPerPage,$startIndex,$resultType,$date,$uoid);

			    if(!empty($OrderDetails)):
			    	$totalpage 				    	= count($totalpage);
					$result['transactionHistory'] 	= $OrderDetails?$OrderDetails:array();
					$result['current_page']     	= $current_page;
					$result['total_page'] 	    	=   $totalpage;
                	echo outPut(1,lang('SUCCESS_CODE'),lang('ORDER_SUCCESS'),$result);die();
			    else:
					echo outPut(0,lang('FORBIDDEN_CODE'),lang('DATA_NOT_FOUND'),$result);die();
			    endif;

			endif;
			echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name  : winningHistory
	 * * Developed By 	: Dilip Halder
	 * * Purpose  		: This function used for get winning History
	 * * Date 			: 13 July 2024
	 * * **********************************************************************/
	public function winningHistory()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 			= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):

			$user_id 			= $this->input->post('user_id');
			$searchBy 			= $this->input->post('search_by');
			$searchValue 		= $this->input->post('search_value');
			$itemsPerPage 		= $this->input->post('itemsPerPage');
			$pageno 			= $this->input->post('page');

			// data filter
			$date['from']		= $this->input->post('from');
			$date['to']			= $this->input->post('to');

			if(empty($user_id)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
            elseif(empty($this->input->post('itemsPerPage'))):
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_ITEMPERPAGE'),$result);die();
            elseif(empty($this->input->post('page'))):
            	echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_PAGE_NO'),$result);die();
            else:
            		

	    		$requestFrom = 'app';
				$USERDATA    = $this->common_model->userValidate($user_id,$requestFrom);

	    		$resultType  = 'count';
				$totalcount  = $this->common_model->winningHistory($user_id,$searchBy,$searchValue,$itemsPerPage,$startIndex,$resultType,$date);

				$itemsPerPage = $this->input->post('itemsPerPage');
				$pageno 	  = $this->input->post('page');

				// Sample long array with data
				$longArray = $totalcount;
				// Current page number (received from URL query parameter, e.g., ?page=2)
				$page = isset($pageno) ? (int)$pageno : 1;

				// Calculate total number of pages
				$totalPages = ceil($longArray / $itemsPerPage);
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

				$startIndex    = ($page - 1) * $itemsPerPage;
		 		$resultType    = '';
		 		$OrderDetails  = $this->common_model->winningHistory($user_id,$searchBy,$searchValue,$itemsPerPage,$startIndex,$resultType,$date);

			    if(!empty($OrderDetails)):
			    	$totalpage 				    	= count($totalpage);
					$result['winnerHistory'] 		= $OrderDetails?$OrderDetails:array();
					$result['current_page']     	= $current_page;
					$result['total_page'] 	    	=   $totalpage;
                	echo outPut(1,lang('SUCCESS_CODE'),lang('ORDER_SUCCESS'),$result);die();
			    else:
					echo outPut(0,lang('FORBIDDEN_CODE'),lang('DATA_NOT_FOUND'),$result);die();
			    endif;

			endif;
			echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name  : winnerGallery
	 * * Developed By 	: Dilip Halder
	 * * Purpose  		: This function used for get winner Gallery 
	 * * Date 			: 15 July 2024
	 * * **********************************************************************/
	public function winnerGallery()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 			= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):

			$user_id 			= $this->input->post('user_id');
			$searchBy 			= $this->input->post('search_by');
			$searchValue 		= $this->input->post('search_value');
			$itemsPerPage 		= $this->input->post('itemsPerPage');
			$pageno 			= $this->input->post('page');

			// data filter
			$date['from']		= $this->input->post('from');
			$date['to']			= $this->input->post('to');

			if(empty($user_id)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
            elseif(empty($this->input->post('itemsPerPage'))):
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_ITEMPERPAGE'),$result);die();
            elseif(empty($this->input->post('page'))):
            	echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_PAGE_NO'),$result);die();
            else:
            		
	    		$requestFrom = 'app';
				$USERDATA    = $this->common_model->userValidate($user_id,$requestFrom);

	    		$resultType  = 'count';
				$totalcount  = $this->common_model->winningGallery($user_id,$searchBy,$searchValue,$itemsPerPage,$startIndex,$resultType,$date);

				$itemsPerPage = $this->input->post('itemsPerPage');
				$pageno 	  = $this->input->post('page');

				// Sample long array with data
				$longArray = $totalcount;
				// Current page number (received from URL query parameter, e.g., ?page=2)
				$page = isset($pageno) ? (int)$pageno : 1;

				// Calculate total number of pages
				$totalPages = ceil($longArray / $itemsPerPage);
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

				$startIndex    = ($page - 1) * $itemsPerPage;
		 		$resultType    = '';
		 		$OrderDetails  = $this->common_model->winningGallery($user_id,$searchBy,$searchValue,$itemsPerPage,$startIndex,$resultType,$date);

			    if(!empty($OrderDetails)):
			    	$totalpage 				    	= count($totalpage);
					$result['winnerGallery'] 		= $OrderDetails?$OrderDetails:array();
					$result['current_page']     	= $current_page;
					$result['total_page'] 	    	=   $totalpage;
                	echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);die();
			    else:
					echo outPut(0,lang('FORBIDDEN_CODE'),lang('DATA_NOT_FOUND'),$result);die();
			    endif;

			endif;
			echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}



}
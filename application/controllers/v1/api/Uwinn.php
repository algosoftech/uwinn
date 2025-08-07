<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class uwinn extends CI_Controller {
	
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
	 * * Function name 	: getLottoProductListPageData
	 * * Developed By 	: Dilip Halder
	 * * Purpose  		: This function used for get Product List Page Data
	 * * Date 			: 23 October 2023
	 * * **********************************************************************/
	public function getProductListPageData()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	

		$USerData 		   = $this->common_model->UserAuthCheck('GET');
		if($USerData):
			
			$ourCampaigns 				=	array();
			$tblName2 					=	'uw_products';
			if($this->input->get('show_on')):
				$where2['where']['show_on'] = $this->input->get('show_on');
			else:
				$where2['where']['show_on'] = array('$in' => array('POS'));
			endif;
				$where2['where']['enable_raffle_ticket'] =  array('$ne' => 'Enable');
			$where2['where']['stock'] = array('$gt'=> 0);
			$order2 				  =	array('seq_order' => -1);
			$data2					  =	$this->geneal_model->getData2('multiple',$tblName2,$where2,$order2);

			foreach ($data2 as $key => $PItems):
				$data2[$key]['product_image'] = base_url($PItems['product_image']);

				if(!empty($USerData['conversion'])):
					if(!empty($USerData['conversion'])):
						$data2[$key]['campaign_valid_for']    	=  $USerData['country'];
						$data2[$key]['adepoints']    			=  $USerData['conversion']*$PItems['adepoints'];
						$data2[$key]['straight_add_on_amount']  =  $USerData['conversion']*$PItems['straight_add_on_amount'];
						$data2[$key]['rumble_add_on_amount']    =  $USerData['conversion']*$PItems['rumble_add_on_amount'];
						$data2[$key]['reverse_add_on_amount']   =  $USerData['conversion']*$PItems['reverse_add_on_amount'];
						$data2[$key]['reverse_add_on_amount']   =  $USerData['conversion']*$PItems['reverse_add_on_amount'];
					endif;
					// if(!empty($USerData['time_zone'])):
					// 	date_default_timezone_set($USerData['time_zone']);
					// 	$FormatedDrawDate = date('Y-m-d',strtotime($PItems['draw_date']));
					// 	$FormatedDrawTime = date('H:i'  ,strtotime($PItems['draw_time']));
					// 	$data2[$key]['draw_date'] = $FormatedDrawDate;
					// 	$data2[$key]['draw_time'] = $FormatedDrawTime;
					// endif;
				endif;
			endforeach;

			$USERID = $this->input->get('users_id');
			// if($USERID):
				$whereConCamp['where'] 		=   array( 'status'=> 'A');
				$PermissionDetails		=	$this->common_model->getData('single','uw_allowed_campaigns_permission',$whereConCamp);
				
				$productData  			= array();
				foreach($data2 as $iTems):
					if(in_array($USERID, $PermissionDetails['seleted_users'])  &&  in_array($iTems['products_id'] , $PermissionDetails['seleted_campaign']) ):
						 $productData[] = $iTems;
					elseif(!in_array($USERID, $PermissionDetails['seleted_users'])  &&  !in_array($iTems['products_id'] , $PermissionDetails['seleted_campaign']) ):
						 $productData[] = $iTems;
					endif;
				endforeach;
				
			// endif;


			if($productData):
				foreach($productData as $info2):
					$info2['product_name'] = $info2['title'];
					
					$valid2 			= 	$info2['validuptodate'].' '.$info2['validuptotime'].':0';
					$drawDate2 			= 	$info2['draw_date'].' '.$info2['draw_time'].':0';
					$today2 			= 	date('Y-m-d H:i:s');
					if(strtotime($valid2) > strtotime($today2) && strtotime($drawDate2) > strtotime($today2)):
						if($this->input->post('users_id')):
							$prowhere['where']	=	array('users_id'=>(int)$this->input->post('users_id'),'product_id'=>(int)$info2['products_id']);
							$prodData			=	$this->common_model->getData('single','uw_wishlist',$prowhere);
							if($prodData):
								if($prodData['wishlist_product'] == 'Y'):
									$info2['wishlist_product']  = 'Y';
								else:
									$info2['wishlist_product']  = 'N';
								endif; 
							else:
								$info2['wishlist_product']  	= 'N';
							endif;
						else:
							$info2['wishlist_product']  		= 'N';
						endif;

						if($this->input->post('users_id')):
							$USRwhere 							=	[ 'users_id' => (int)$this->input->post('users_id') ];
							$USRtblName 						=	'uw_users';
							$userDetails 						=	$this->geneal_model->getOnlyOneData($USRtblName, $USRwhere);
							if($userDetails):
								$productShareUrl  				= 	generateProductShareUrl($info2['products_id'],$this->input->post('users_id'),$userDetails['referral_code']);
								$info2['share_url']  			= 	$productShareUrl;
							else:	
								$info2['share_url']  			= 	'';
							endif;
						else:
							$info2['share_url']  				= 	'';
						endif;

						$info2['draw_time'] = $info2['draw_time'].'(UAE)';

						$product_prise_data 			= 	$this->geneal_model->getParticularDataByParticularField('prize_image','uw_prize', 'product_id', $info2['products_id']);
						$product_prise_data['prize_image'] = base_url($product_prise_data['prize_image']);
						if($product_prise_data <> ''):
							$info2['product_prise_data']  = $product_prise_data;
						else:
							$info2['product_prise_data']  = '';
						endif;

						array_push($ourCampaigns,$info2);
					endif;
				endforeach;
			endif;

			 
			$NewourCampaigns = array();
			foreach ($ourCampaigns as $key => $items):
				 if(!empty($items['product_prise_data'])):
			 		$NewourCampaigns[] = $items;
				 endif;
			endforeach;
			$ourCampaigns = $NewourCampaigns;
			$result['ourCampaigns'] 	=	$ourCampaigns;
			echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}


	/* * *********************************************************************
	 * * Function name  : checkOut
	 * * Developed By   : Dilip Halder
	 * * Purpose    	: This function used for get Country Code
	 * * Date 			: 27 October 2023
	 * * **********************************************************************/
	public function paymentCapture()
	{	
		$apiHeaderData 		= getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 			= array();	
		$USerData 		    = $this->common_model->UserAuthCheck('POST');
		if($USerData):

			// Campaign Freezing code start here..
			  $whereCon['where']  = array('status' => 'A');
		 	  $campaignFreezing 	= $this->common_model->getData('single','uw_campaign_freezing',$whereCon);
		 	  if($campaignFreezing['campaign_freezing'] == 'enable'):
		 		echo outPut(0,lang('SUCCESS_CODE'),$campaignFreezing['freezing_title'],$result);die();
		 	  endif;
			// Campaign Freezing code end here..
			
			if( $this->input->get('users_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);

			elseif( $this->input->post('prize_title') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);

			// elseif( $this->input->post('first_name') == ''): 
			// 	echo outPut(0,lang('SUCCESS_CODE'),lang('EMPRT_FIRST_NAME'),$result);

			// elseif( $this->input->post('last_name') == ''): 
			// 	echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_LAST_NAME'),$result);

			// elseif( $this->input->post('product_is_donate') == ''): 
			// 	echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_IS_DONATE'),$result);
			
			elseif( $this->input->post('product_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('PRODUCT_ID_EMPTY'),$result);

			elseif( $this->input->post('product_title') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_PRODUCT_TITLE'),$result);

			elseif( $this->input->post('product_qty') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_PRODUCT_QTY'),$result);

			elseif( $this->input->post('draw_date') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_DRAW_DATE'),$result);

			elseif( $this->input->post('lotto_type') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_LOTTO_TYPE'),$result);

			// elseif( $this->input->post('users_email') == ''): 
			// 	echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_USER_EMAIL'),$result);

			elseif( $this->input->post('subtotal') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_SUBTOTAL'),$result);

			// elseif( $this->input->post('country_code') == ''): 
			// 	echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_COUNTRYCODE'),$result);

			// elseif( $this->input->post('users_mobile') == ''): 
			// 	echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_USERMOBILE'),$result);

			// elseif( $this->input->post('SMS') == ''): 
			// 	echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_SMS'),$result);

			elseif( $this->input->post('device_type') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_DEVICE_TYPE'),$result);

			elseif( $this->input->post('app_version') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_APP_VERSION'),$result);

			elseif( $this->input->post('ticket') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_TICKET'),$result);

			elseif( $this->input->post('request_no') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('REQUEST_NO'),$result);
			// elseif( $this->input->post('seller_details') == ''): 
			// 	echo outPut(0,lang('SUCCESS_CODE'),lang('SELLER_DETAIL_MISSING'),$result);
			else:

				$draw_date         = $this->input->post('draw_date');
				$product_id  	   = $this->input->post('product_id');
				$tableName 		   = 'uw_products';
				$whereCon['where'] = array('products_id'=> (int)$product_id ,'status' => "A");
				$shortField 	   = array('products_id' => -1);
				$ProductData       = $this->common_model->getData('single',$tableName, $whereCon, $shortField);

				$DrawDateNTime     = $ProductData['draw_date'].' '.$ProductData['draw_time'];
				$currentDatentime  = date('Y-m-d H:i');
				 
				if($ProductData['products_id'] != $product_id  || strtotime($DrawDateNTime) < strtotime($currentDatentime) || strtotime($ProductData['draw_date']) != strtotime($draw_date) ):
					echo outPut(0,lang('SUCCESS_CODE'),lang('RESTART_APP'),$result);die();
				endif;
				
				// Check Product availability
				$productID 			= $this->input->post('product_id');
				$productQty 		= $this->input->post('product_qty');
				$productIsDonated 	= $this->input->post('product_id');
				$Plateform 			= 'app';
				$CouponGenerate 	= '';
				$USER['USERID']		= $this->input->get('users_id');
				$USER['total_price']= $this->input->post('total_price');

				// Test campaign restriction for not allowed users.
				$whereConCam['where'] 			=   array( 'status'=> 'A');
				$TestCampaignData			=	$this->common_model->getData('single','uw_allowed_campaigns_permission',$whereConCam);
				if(in_array($productID, $TestCampaignData['seleted_campaign']) && !in_array($USER['USERID'], $TestCampaignData['seleted_users'])):
					echo outPut(0,lang('SUCCESS_CODE'),lang('DEMO_CAMPAIGN'),$result);die();
				endif;

				//complete validation..
				$result 			=  $this->common_model->CheckAvailableTickets($productID,$productQty ,$productIsDonated,$Plateform,$CouponGenerate,$USER);


				//Update stock
				// $this->geneal_model->updateStock($productID,$productQty);

				$tbl_name  		= 'uw_users';
				$whereCon  		= array('users_id'  =>(int)$USER['USERID'] ,'status'=> 'A');
				$sellerDetails  = $this->geneal_model->getOnlyOneData($tbl_name, $whereCon);
				if($sellerDetails['app_version'] != $this->input->post('app_version')):
					$updateParams['app_version'] 	= $this->input->post('app_version');
					$updateParams["updated_at"]     = date('Y-m-d H:i');
					$this->common_model->editData('uw_users', $updateParams, 'users_id', (int)$sellerDetails['users_id']);
				endif;

				$user_oid 			   = $sellerDetails['_id']->{'$id'};
				$commission_percentage = $sellerDetails['commission_percentage'];
				 
				$errorLog['data']   = $_POST;
				$this->geneal_model->addData('uw_paykart_error', $errorLog); 
				$sellerArray 		= $this->input->post('seller_details');
				
				$ORparam["sequence_id"]		    		=	(int)$this->geneal_model->getNextSequence('uw_lotto_orders');
		        $ORparam["user_oid"] 					=	new MongoDB\BSON\ObjectId($user_oid);
		        $ORparam["order_id"]		        	=	$this->geneal_model->getNextUWINOrderId();
		        $ORparam["draw_id"]		    			=	(int)$ProductData['draw_id']; 
		        $ORparam["draw_date"]		    		=	$ProductData['draw_date']; 
		        $ORparam["order_code"]		    		=	base64_encode(rand(1000,9999)); 
		        $ORparam["user_id"] 					=	(int)$this->input->get('users_id');
		        $ORparam["user_type"] 					=	$sellerDetails['users_type']; 
	        	$ORparam["user_email"] 					=   $sellerDetails['users_email'];	
			 	$ORparam["user_phone"] 					=	$sellerDetails['users_mobile'];	
			 	$ORparam["store_name"] 					=	$sellerDetails['store_name'];
			 	$ORparam["request_no"] 					=	(int)$this->input->post('request_no');;
			 	$ORparam["seller_details"] 				=   $sellerArray;
			 	$ORparam["pos_number"] 					=	(int)$sellerDetails['pos_number'];
		     	$ORparam["product_id"] 					=	(int)$this->input->post('product_id');
		     	$ORparam["product_title"] 				=	$this->input->post('product_title');
		     	$ORparam["product_qty"] 				=	$this->input->post('product_qty');
		     	$ORparam["prize_title"] 				=	$this->input->post('prize_title');
		        $ORparam["vat_amount"] 					=	(float)$this->input->post('vat_amount');
		        $ORparam["straight_add_on_amount"] 		=	(float)$this->input->post('straight_add_on_amount');
		        $ORparam["rumble_add_on_amount"] 		=	(float)$this->input->post('rumble_add_on_amount');
		        $ORparam["reverse_add_on_amount"] 		=	(float)$this->input->post('reverse_add_on_amount');
		        $ORparam["subtotal"] 					=	(float)$this->input->post('subtotal');
		        $ORparam["total_price"] 				=	(float)$this->input->post('total_price');
		        $ORparam["availableArabianPoints"] 		=	(float)$sellerDetails["availableArabianPoints"];
				$ORparam["end_balance"] 				=	(float)$sellerDetails["availableArabianPoints"] - (float)$ORparam["total_price"] ;
			    $ORparam["payment_mode"] 				=	'UPoints';
		        $ORparam["product_is_donate"] 			=	$this->input->post('product_is_donate'); //$this->input->post('product_is_donate');
			    $ORparam["order_status"] 				=	"Success";
			    $ORparam["device_type"] 				=	$this->input->post('device_type');
   				$ORparam["app_name"] 					=	$this->input->post('app_name');
   				$ORparam["app_version"] 				=	$this->input->post('app_version');
   				$ORparam["ticket"] 						=	$this->input->post('ticket');
			    $ORparam["status"] 						=	"A";
		     	$ORparam["order_first_name"] 			=	$this->input->post('first_name');
		     	$ORparam["order_last_name"] 			=	$this->input->post('last_name');
		     	$ORparam["order_users_country_code"] 	=	$this->input->post('country_code');
		     	$ORparam["order_users_mobile"] 			=	$this->input->post('users_mobile');
		     	$ORparam["order_users_email"] 			=	$this->input->post('users_email');
		        $ORparam["commission_percentage"] 		=	$commission_percentage; 
		     	$ORparam["SMS"] 						=	$this->input->post('SMS');
			    $ORparam["creation_ip"] 				=	$this->input->ip_address();
			    $ORparam["created_at"] 					=	date('Y-m-d H:i');
			    //Saving order details for Ticket
			    $orderInsertID 							=	$this->geneal_model->addData('uw_lotto_orders', $ORparam);
			  	
			  	// Deduct the purchesed points and get available arabian points of user.
				$currentBal 							= 	$this->geneal_model->debitPointsByAPI($USER['total_price'],$USER['USERID']); 

		     	// Order capturing in order uw_loadbalance table..
			    $fromuserparam["load_balance_id"]		 =	(int)$this->geneal_model->getNextSequence('uw_loadBalance');
				$fromuserparam["order_oid"] 			 =	new MongoDB\BSON\ObjectId($orderInsertID['_id']->{'$id'});
				$fromuserparam["user_oid"] 				 =	new MongoDB\BSON\ObjectId($user_oid);
				$fromuserparam["user_id_deb"]			 =	(int)$this->input->get('users_id');
				$fromuserparam["order_id"] 				 =	$orderInsertID['order_id'];
				$fromuserparam["user_id_cred"] 			 =	(int)0;
				$fromuserparam["upoints"] 				 =	(float)$orderInsertID['total_price'];
				$fromuserparam["availableArabianPoints"] =	(float)$orderInsertID['availableArabianPoints'];
				$fromuserparam["end_balance"] 			 =	(float)$orderInsertID['end_balance'];
			    $fromuserparam["record_type"] 			 =	'Debit';
			    $fromuserparam["narration"]				 =	'Order';
			    $fromuserparam["remarks"]				 =	'Ticket ID : '.$orderInsertID['order_id'];
			    $fromuserparam["creation_ip"] 			 =	$this->input->ip_address();
			    $fromuserparam["created_at"] 			 =	date('Y-m-d H:i');
			    $fromuserparam["created_by"] 			 =	(int)$this->input->get('users_id');
			    $fromuserparam["status"] 				 =	"A";
		    	$this->geneal_model->addData('uw_loadBalance', $fromuserparam);
		    	/* Order capturing code start here.  End */


		    	//Commission code start here.
			    	$totalPrice 			 = (float)$orderInsertID['total_price'];
					$commission_percentage   = $sellerDetails['commission_percentage'];
					// Calculate commission amount
					$commission_amount 		 = ($totalPrice * $commission_percentage) / 100;

			     	// Commission capturing in order uw_loadbalance table..
				    $commissionParam["load_balance_id"]		 	 =	(int)$this->geneal_model->getNextSequence('uw_loadBalance');
					$commissionParam["order_oid"] 			 	 =	new MongoDB\BSON\ObjectId($orderInsertID['_id']->{'$id'});
					$commissionParam["user_oid"] 				 =	new MongoDB\BSON\ObjectId($user_oid);
					$commissionParam["user_id_cred"] 			 =	(int)$this->input->get('users_id');
					$commissionParam["user_id_deb"]			 	 =	(int)0;
					$commissionParam["order_id"] 				 =	$orderInsertID['order_id'];
					$commissionParam["upoints"] 				 =	(float)$commission_amount;
					$commissionParam["availableArabianPoints"] 	 =	(float)$orderInsertID['end_balance'];
					$commissionParam["end_balance"] 			 =	(float)$orderInsertID['end_balance']+$commission_amount;
				    $commissionParam["record_type"] 			 =	'Credit';
				    $commissionParam["narration"]				 =	'Commission';
				    $commissionParam["remarks"]				 	 =	'Ticket ID : '.$orderInsertID['order_id'];
				    $commissionParam["creation_ip"] 			 =	$this->input->ip_address();
				    $commissionParam["created_at"] 				 =	date('Y-m-d H:i');
				    $commissionParam["created_by"] 			 	 =	(int)$this->input->get('users_id');
				    $commissionParam["status"] 				 	 =	"A";
			    	$this->geneal_model->addData('uw_loadBalance', $commissionParam);
			    	// Credit the purchesed points and get available arabian points of user.
					
					$this->geneal_model->creaditPoints($commission_amount,$orderInsertID["user_id"]); 
		    		/*  Commission code start here.  End */

			     	 $resultsData["sequence_id"]                =   $ORparam["sequence_id"] ; 
					$resultsData["user_oid"]                    =   $ORparam["user_oid"];
					$resultsData["order_id"]                    =   $ORparam["order_id"];
					$resultsData["draw_id"]                     =   $ORparam["draw_id"];  
					$resultsData["order_code"]                  =   $ORparam["order_code"];  
					$resultsData["user_id"]                     =   $ORparam["user_id"];  
					$resultsData["user_type"]                   =   $ORparam["user_type"];  
					$resultsData["user_email"]                  =   $ORparam["user_email"];  
					$resultsData["user_phone"]                  =   $ORparam["user_phone"];  
					$resultsData["store_name"]                  =   $ORparam["store_name"];  
					$resultsData["request_no"]                  =   $ORparam["request_no"];  
					$resultsData["pos_number"]                  =   $ORparam["pos_number"];  
					$resultsData["product_id"]                  =   $ORparam["product_id"];  
					$resultsData["product_title"]               =   $ORparam["product_title"];  
					$resultsData["product_qty"]                 =   $ORparam["product_qty"];  
					$resultsData["prize_title"]                 =   $ORparam["prize_title"];  
					$resultsData["vat_amount"]                  =   $ORparam["vat_amount"];  
					$resultsData["straight_add_on_amount"]      =   $ORparam["straight_add_on_amount"];  
					$resultsData["rumble_add_on_amount"]        =   $ORparam["rumble_add_on_amount"];  
					$resultsData["reverse_add_on_amount"]       =   $ORparam["reverse_add_on_amount"];  
					$resultsData["subtotal"]                    =   $ORparam["subtotal"];  
					$resultsData["total_price"]                 =   $ORparam["total_price"];  
					$resultsData["availableArabianPoints"]      =   $ORparam["availableArabianPoints"]; 
					$resultsData["end_balance"]                 =   $ORparam["end_balance"];
					$resultsData["payment_mode"]                =   $ORparam["payment_mode"];  
					$resultsData["product_is_donate"]           =   $ORparam["product_is_donate"];  
					$resultsData["order_status"]                =   $ORparam["order_status"];  
					$resultsData["device_type"]                 =   $ORparam["device_type"];  
					$resultsData["app_name"]                    =   $ORparam["app_name"];  
					$resultsData["app_version"]                 =   $ORparam["app_version"];  
					$resultsData["ticket"]                      =   $ORparam["ticket"];  
					$resultsData["status"]                      =   $ORparam["status"];  
					$resultsData["order_first_name"]            =   $ORparam["order_first_name"];  
					$resultsData["order_last_name"]             =   $ORparam["order_last_name"];  
					$resultsData["order_users_country_code"]    =   $ORparam["order_users_country_code"];  
					$resultsData["order_users_mobile"]          =   $ORparam["order_users_mobile"];  
					$resultsData["order_users_email"]           =   $ORparam["order_users_email"];  
					$resultsData["commission_percentage"]       =   $ORparam["commission_percentage"];  
					$resultsData["SMS"]                         =   $ORparam["SMS"];  
					$resultsData["creation_ip"]                 =   $ORparam["creation_ip"];  
					$resultsData["created_at"]                  =   $ORparam["created_at"]; 
					$resultsData["draw_date"]                   =   date('Y-m-d H:i:s', strtotime( $ProductData['draw_date'].' '.$ProductData['draw_time'] ));
			    	$result = $resultsData;
				echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);

			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}


	public function paymentCapture_test()
	{	
		$apiHeaderData 		= getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 			= array();	
		$USerData 		    = $this->common_model->UserAuthCheck('POST');
		if($USerData):

			// Campaign Freezing code start here..
			  $whereCon['where']  = array('status' => 'A');
		 	  $campaignFreezing 	= $this->common_model->getData('single','uw_campaign_freezing',$whereCon);
		 	  if($campaignFreezing['campaign_freezing'] == 'enable'):
		 		echo outPut(0,lang('SUCCESS_CODE'),$campaignFreezing['freezing_title'],$result);die();
		 	  endif;
			// Campaign Freezing code end here..
			
			if( $this->input->get('users_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);

			elseif( $this->input->post('prize_title') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);

			// elseif( $this->input->post('first_name') == ''): 
			// 	echo outPut(0,lang('SUCCESS_CODE'),lang('EMPRT_FIRST_NAME'),$result);

			// elseif( $this->input->post('last_name') == ''): 
			// 	echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_LAST_NAME'),$result);

			// elseif( $this->input->post('product_is_donate') == ''): 
			// 	echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_IS_DONATE'),$result);
			
			elseif( $this->input->post('product_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('PRODUCT_ID_EMPTY'),$result);

			elseif( $this->input->post('product_title') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_PRODUCT_TITLE'),$result);

			elseif( $this->input->post('product_qty') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_PRODUCT_QTY'),$result);

			elseif( $this->input->post('draw_date') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_DRAW_DATE'),$result);

			elseif( $this->input->post('lotto_type') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_LOTTO_TYPE'),$result);

			// elseif( $this->input->post('users_email') == ''): 
			// 	echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_USER_EMAIL'),$result);

			elseif( $this->input->post('subtotal') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_SUBTOTAL'),$result);

			// elseif( $this->input->post('country_code') == ''): 
			// 	echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_COUNTRYCODE'),$result);

			// elseif( $this->input->post('users_mobile') == ''): 
			// 	echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_USERMOBILE'),$result);

			// elseif( $this->input->post('SMS') == ''): 
			// 	echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_SMS'),$result);

			elseif( $this->input->post('device_type') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_DEVICE_TYPE'),$result);

			elseif( $this->input->post('app_version') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_APP_VERSION'),$result);

			elseif( $this->input->post('ticket') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_TICKET'),$result);

			elseif( $this->input->post('request_no') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('REQUEST_NO'),$result);
			// elseif( $this->input->post('seller_details') == ''): 
			// 	echo outPut(0,lang('SUCCESS_CODE'),lang('SELLER_DETAIL_MISSING'),$result);
			else:

				$draw_date         = $this->input->post('draw_date');
				$product_id  	   = $this->input->post('product_id');
				$tableName 		   = 'uw_products';
				$whereCon['where'] = array('products_id'=> (int)$product_id ,'status' => "A");
				$shortField 	   = array('products_id' => -1);
				$ProductData       = $this->common_model->getData('single',$tableName, $whereCon, $shortField);

				$DrawDateNTime     = $ProductData['draw_date'].' '.$ProductData['draw_time'];
				$currentDatentime  = date('Y-m-d H:i');
				 
				if($ProductData['products_id'] != $product_id  || strtotime($DrawDateNTime) < strtotime($currentDatentime) || strtotime($ProductData['draw_date']) != strtotime($draw_date)):
					echo outPut(0,lang('SUCCESS_CODE'),lang('RESTART_APP'),$result);die();
				endif;
				
				// Check Product availability
				$productID 			= $this->input->post('product_id');
				$productQty 		= $this->input->post('product_qty');
				$productIsDonated 	= $this->input->post('product_id');
				$Plateform 			= 'app';
				$CouponGenerate 	= '';
				$USER['USERID']		= $this->input->get('users_id');
				$USER['total_price']= $this->input->post('total_price');


				// Test campaign restriction for not allowed users.
				$whereConCam['where'] 			=   array( 'status'=> 'A');
				$TestCampaignData			=	$this->common_model->getData('single','uw_allowed_campaigns_permission',$whereConCam);
				if(in_array($productID, $TestCampaignData['seleted_campaign']) && !in_array($USER['USERID'], $TestCampaignData['seleted_users'])):
					echo outPut(0,lang('SUCCESS_CODE'),lang('DEMO_CAMPAIGN'),$result);die();
				endif;

				//complete validation..
				$result 			=  $this->common_model->CheckAvailableTickets($productID,$productQty ,$productIsDonated,$Plateform,$CouponGenerate,$USER);

				

				//Update stock
				// $this->geneal_model->updateStock($productID,$productQty);

				$tbl_name  		= 'uw_users';
				$whereCon  		= array('users_id'  =>(int)$USER['USERID'] ,'status'=> 'A');
				$sellerDetails  = $this->geneal_model->getOnlyOneData($tbl_name, $whereCon);
				if($sellerDetails['app_version'] != $this->input->post('app_version')):
					$updateParams['app_version'] 	= $this->input->post('app_version');
					$updateParams["updated_at"]     = date('Y-m-d H:i');
					$this->common_model->editData('uw_users', $updateParams, 'users_id', (int)$sellerDetails['users_id']);
				endif;
				

				$user_oid 			   = $sellerDetails['_id']->{'$id'};
				$commission_percentage = $sellerDetails['commission_percentage'];



				 
				$errorLog['data']   = $_POST;
				$this->geneal_model->addData('uw_paykart_error', $errorLog); 
				
				$sellerArray = $this->input->post('seller_details');

				$ORparam["sequence_id"]		    		=	(int)$this->geneal_model->getNextSequence('uw_lotto_orders');
		        $ORparam["user_oid"] 					=	new MongoDB\BSON\ObjectId($user_oid);
		        $ORparam["order_id"]		        	=	$this->geneal_model->getNextUWINOrderId();
		        $ORparam["draw_id"]		    			=	(int)$ProductData['draw_id']; 
		        $ORparam["order_code"]		    		=	base64_encode(rand(1000,9999)); 
		        $ORparam["user_id"] 					=	(int)$this->input->get('users_id');
		        $ORparam["user_type"] 					=	$sellerDetails['users_type']; 
	        	$ORparam["user_email"] 					=   $sellerDetails['users_email'];	
			 	$ORparam["user_phone"] 					=	$sellerDetails['users_mobile'];	
			 	$ORparam["store_name"] 					=	$sellerDetails['store_name'];
			 	$ORparam["request_no"] 					=	(int)$this->input->post('request_no');;
			 	$ORparam["seller_details"] 				=   $sellerArray;
			 	$ORparam["pos_number"] 					=	(int)$sellerDetails['pos_number'];
		     	$ORparam["product_id"] 					=	(int)$this->input->post('product_id');
		     	$ORparam["product_title"] 				=	$this->input->post('product_title');
		     	$ORparam["product_qty"] 				=	$this->input->post('product_qty');
		     	$ORparam["prize_title"] 				=	$this->input->post('prize_title');
		        $ORparam["vat_amount"] 					=	(float)$this->input->post('vat_amount');
		        $ORparam["straight_add_on_amount"] 		=	(float)$this->input->post('straight_add_on_amount');
		        $ORparam["rumble_add_on_amount"] 		=	(float)$this->input->post('rumble_add_on_amount');
		        $ORparam["reverse_add_on_amount"] 		=	(float)$this->input->post('reverse_add_on_amount');
		        $ORparam["subtotal"] 					=	(float)$this->input->post('subtotal');
		        $ORparam["total_price"] 				=	(float)$this->input->post('total_price');
		        $ORparam["availableArabianPoints"] 		=	(float)$sellerDetails["availableArabianPoints"];
				$ORparam["end_balance"] 				=	(float)$sellerDetails["availableArabianPoints"] - (float)$ORparam["total_price"] ;
			    $ORparam["payment_mode"] 				=	'UPoints';
		        $ORparam["product_is_donate"] 			=	$this->input->post('product_is_donate'); //$this->input->post('product_is_donate');
			    $ORparam["order_status"] 				=	"Success";
			    $ORparam["device_type"] 				=	$this->input->post('device_type');
   				$ORparam["app_name"] 					=	$this->input->post('app_name');
   				$ORparam["app_version"] 				=	$this->input->post('app_version');
   				$ORparam["ticket"] 						=	$this->input->post('ticket');
			    $ORparam["status"] 						=	"A";
		     	$ORparam["order_first_name"] 			=	$this->input->post('first_name');
		     	$ORparam["order_last_name"] 			=	$this->input->post('last_name');
		     	$ORparam["order_users_country_code"] 	=	$this->input->post('country_code');
		     	$ORparam["order_users_mobile"] 			=	$this->input->post('users_mobile');
		     	$ORparam["order_users_email"] 			=	$this->input->post('users_email');
		        $ORparam["commission_percentage"] 		=	$commission_percentage; 
		     	$ORparam["SMS"] 						=	$this->input->post('SMS');
			    $ORparam["creation_ip"] 				=	$this->input->ip_address();
			    $ORparam["created_at"] 					=	date('Y-m-d H:i');
			    //Saving order details for Ticket
			    $orderInsertID 							=	$this->geneal_model->addData('uw_lotto_orders', $ORparam);
			  	
			  	// Deduct the purchesed points and get available arabian points of user.
				$currentBal 							= 	$this->geneal_model->debitPointsByAPI($USER['total_price'],$USER['USERID']); 



		     	// Order capturing in order uw_loadbalance table..
				    $fromuserparam["load_balance_id"]		 =	(int)$this->geneal_model->getNextSequence('uw_loadBalance');
					$fromuserparam["order_oid"] 			 =	new MongoDB\BSON\ObjectId($orderInsertID['_id']->{'$id'});
					$fromuserparam["user_oid"] 				 =	new MongoDB\BSON\ObjectId($user_oid);
					$fromuserparam["user_id_deb"]			 =	(int)$this->input->get('users_id');
					$fromuserparam["order_id"] 				 =	$orderInsertID['order_id'];
					$fromuserparam["user_id_cred"] 			 =	(int)0;
					$fromuserparam["upoints"] 				 =	(float)$orderInsertID['total_price'];
					$fromuserparam["availableArabianPoints"] =	(float)$orderInsertID['availableArabianPoints'];
					$fromuserparam["end_balance"] 			 =	(float)$orderInsertID['end_balance'];
				    $fromuserparam["record_type"] 			 =	'Debit';
				    $fromuserparam["narration"]				 =	'Order';
				    $fromuserparam["remarks"]				 =	'Ticket ID : '.$orderInsertID['order_id'];
				    $fromuserparam["creation_ip"] 			 =	$this->input->ip_address();
				    $fromuserparam["created_at"] 			 =	date('Y-m-d H:i');
				    $fromuserparam["created_by"] 			 =	(int)$this->input->get('users_id');
				    $fromuserparam["status"] 				 =	"A";
			    	$this->geneal_model->addData('uw_loadBalance', $fromuserparam);
		    	/* Order capturing code start here.  End */


		    	//Commission code start here.
			    	$totalPrice 			 = (float)$orderInsertID['total_price'];
					$commission_percentage   = $sellerDetails['commission_percentage'];
					// Calculate commission amount
					$commission_amount 		 = ($totalPrice * $commission_percentage) / 100;

			     	// Commission capturing in order uw_loadbalance table..
				    $commissionParam["load_balance_id"]		 	 =	(int)$this->geneal_model->getNextSequence('uw_loadBalance');
					$commissionParam["order_oid"] 			 	 =	new MongoDB\BSON\ObjectId($orderInsertID['_id']->{'$id'});
					$commissionParam["user_oid"] 				 =	new MongoDB\BSON\ObjectId($user_oid);
					$commissionParam["user_id_cred"] 			 =	(int)$this->input->get('users_id');
					$commissionParam["user_id_deb"]			 	 =	(int)0;
					$commissionParam["order_id"] 				 =	$orderInsertID['order_id'];
					$commissionParam["upoints"] 				 =	(float)$commission_amount;
					$commissionParam["availableArabianPoints"] 	 =	(float)$orderInsertID['end_balance'];
					$commissionParam["end_balance"] 			 =	(float)$orderInsertID['end_balance']+$commission_amount;
				    $commissionParam["record_type"] 			 =	'Credit';
				    $commissionParam["narration"]				 =	'Commission';
				    $commissionParam["remarks"]				 	 =	'Ticket ID : '.$orderInsertID['order_id'];
				    $commissionParam["creation_ip"] 			 =	$this->input->ip_address();
				    $commissionParam["created_at"] 				 =	date('Y-m-d H:i');
				    $commissionParam["created_by"] 			 	 =	(int)$this->input->get('users_id');
				    $commissionParam["status"] 				 	 =	"A";
			    	$this->geneal_model->addData('uw_loadBalance', $commissionParam);
			    	// Credit the purchesed points and get available arabian points of user.
					
					$this->geneal_model->creaditPoints($commission_amount,$orderInsertID["user_id"]); 
		    		/*  Commission code start here.  End */

			    $result = $orderInsertID;
				echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);

			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : orderHistory
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used to fetch order history.
	 * * Date : 27 October 2023
	 * * **********************************************************************/
	public function orderHistory()
	{

		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();

		$USerData 		   = $this->common_model->UserAuthCheck('GET');
		if($USerData):
			if( $this->input->get('users_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			else:
				
				$tbl 					=	'uw_lotto_orders';
				$wcon['where']          =  array('user_id' => (int)$this->input->get('users_id'));
				$Sortdata 				=	array('sequence_id' => -1);
				
				$fields = array('order_id','product_qty', 'product_title','prize_title','status','ticket','created_at','total_price');
	 					
				// getDataByNewQuery($fields=array(),$action='',$tbl_name='',$wcon='',$shortField='',$num_page='',$cnt='')
				$userOrderList	=	$this->common_model->getDataByNewQuery($fields,'multiple', $tbl, $wcon,$Sortdata);

				if(!empty($userOrderList)):
					$results = $userOrderList;
					echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);	
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('DATA_NOT_FOUND'),$results);	
				endif;

			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : SummaryReportSearch
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to fetch order history.
	 * * Date 		   : 27 October 2023
	 * * **********************************************************************/
	public function summaryReportSearch()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 			= 	array();

		$USerData 		    = $this->common_model->UserAuthCheck('GET');
		if($USerData):
			
			if( $this->input->get('users_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			else:
			  	$usersId 		= $this->input->get('users_id');
			  	$start_date     = date('Y-m-d H:i' ,strtotime($this->input->get('start_date')));
		      	$end_date 		= date('Y-m-d H:i' ,strtotime($this->input->get('end_date')));
		      	$product_title 	= $this->input->get('product_title');

			 	if($this->input->get('start_date') && $this->input->get('end_date') ):
			 		$where['created_at'] = array('$gte' => $start_date ,'$lte' => $end_date );
			 	elseif($this->input->get('start_date')):
			 		$where['created_at'] = array('$gte' => $start_date);
			 	elseif($this->input->get('end_date')):
			 		$where['created_at'] = array('$lte' => $end_date);
			 	endif;

				$shortField 			=	['coupon_id' => -1];
				$wcon1['where']         =  array('users_id' => (int)$usersId ,'status' => 'A');
				$userResult				=  $this->geneal_model->getData2('single', 'uw_users', $wcon1);
				$user_oid 				=  $userResult['_id']->{'$id'};

				// CommissionAmount 
				$totalSales 			=  $this->common_model->totalSales($user_oid ,$where);
				$commissionAmount 		=  $this->common_model->commissionList($user_oid ,$where);
				$totalcancelOrderAmount	=  $this->common_model->cancelOraderList($user_oid,$where);
				$totalCustomerPaid		=  $this->common_model->totalCustomerPaid($user_oid,$where);
				$dueBalance				=  (float)$totalSales-$commissionAmount-$totalCustomerPaid;

				if($product_title):
				$where['product_title'] = $product_title;
				endif;
				$where['user_id'] 		= (int)$usersId;
				$where['status'] 		= 'A';
				$wcon['where'] = $where;

				// $totalProductDetails	=  $this->common_model->totalProductDetails($user_oid,$where,$usersId);
				$tblName2 				= 'uw_lotto_orders';
				$shortField   			= array('sequence_id' => -1 );
				$totalProductDetails	= $this->common_model->getsummeryReport('multiple',$tblName2 , $wcon , $shortField,$num_page,$cnt);


				$result['totalSales'] 	  		    = (string)$totalSales;
				$result['commissionAmount'] 	    = (string)$commissionAmount;
				$result['totalcancelOrderAmount'] 	= (string)$totalcancelOrderAmount;
				$result['totalCustomerPaid'] 	    = (string)$totalCustomerPaid;
				$result['dueBalance'] 	    		= (string)$dueBalance;
				$result['totalProductDetails'] 	    = $totalProductDetails;

				if(!empty($result)):
					$results = $result;
					echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);	
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('DATA_NOT_FOUND'),$results);	
				endif;

			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : totalSalesReports
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to fetch order history.
	 * * Date 		   : 22 February 2024
	 * * **********************************************************************/
	public function totalSalesReports()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 			= 	array();

		$USerData 		    = $this->common_model->UserAuthCheck('GET');
		if($USerData):
			
			if( $this->input->get('users_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			else:
			  	$usersId 		= $this->input->get('users_id');
			  	$start_date     = date('Y-m-d 21:31' ,strtotime($this->input->get('start_date')));
		      	$end_date 		= date('Y-m-d 21:30' ,strtotime($this->input->get('end_date')));

			 	if($this->input->get('start_date') && $this->input->get('end_date') ):
			 		$where['created_at'] = array('$gte' => $start_date ,'$lte' => $end_date );
			 	elseif($this->input->get('start_date')):
			 		$where['created_at'] = array('$gte' => $start_date);
			 	elseif($this->input->get('end_date')):
			 		$where['created_at'] = array('$lte' => $end_date);
			 	endif;

				$shortField 			=	['coupon_id' => -1];
				$wcon1['where']         =  array('users_id' => (int)$usersId ,'status' => 'A');
				$userResult				=  $this->geneal_model->getData2('single', 'uw_users', $wcon1);
				$user_oid 				=  $userResult['_id']->{'$id'};

				// CommissionAmount 
				$totalSales 			=  $this->common_model->totalSales($user_oid ,$where);
				$commissionAmount 		=  $this->common_model->commissionList($user_oid ,$where);
				$totalcancelOrderAmount	=  $this->common_model->cancelOraderList($user_oid,$where);
				$totalCustomerPaid		=  $this->common_model->totalCustomerPaid($user_oid,$where);
				// $dueBalance				=  $this->common_model->dueBalance($user_oid,$where);

				$result['totalSales'] 	  		    = (string)$totalSales;
				$result['commissionAmount'] 	    = (string)$commissionAmount;
				$result['totalcancelOrderAmount'] 	= (string)$totalcancelOrderAmount;
				$result['totalCustomerPaid'] 	    = (string)$totalCustomerPaid;

				if(!empty($result)):
					$results = $result;
					echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);	
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('DATA_NOT_FOUND'),$results);	
				endif;

			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	public function getWinner()
	{

		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 			= 	array();

		if(requestAuthenticate(APIKEY,'GET')):
		 	$AvailableWinnerCouponList 	=	$this->common_model->getData('multiple','uw_lotto_winners',$whereCon);
		 	if($AvailableWinnerCouponList):
			 	
		 		foreach ($AvailableWinnerCouponList as $key => $list):
				 	$product_id = $list['product_id'];
				 	$tableName = 'uw_prize';
				 	$whereCon['where'] = array('product_id' => (int)$product_id);
				 	$PrizeData = $this->common_model->getData('single',$tableName , $whereCon);

				 	$whereConproduct['where'] = array('products_id' => (int)$product_id);
				 	$productData = $this->common_model->getFieldInArray('title','uw_products' , $whereConproduct);
				 	$AvailableWinnerCouponList[$key]['title'] = $productData[0];

				 	if($PrizeData):
				 		$AvailableWinnerCouponList[$key]['prizeData'] = $PrizeData;
					else:
					 	$AvailableWinnerCouponList[$key]['prizeData'] = '';
					endif;

		 		endforeach;
		 		$results = $AvailableWinnerCouponList;
		 		echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);	
		 	else:
		 		$results = [];
		 		echo outPut(0,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);die();	
		 	endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}
	
	/* * *********************************************************************
	 * * Function name : productSettings
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to fetch order history.
	 * * Date 		   : 29 January 2024
	 * * **********************************************************************/
	public function productSettings()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_GET));
		$result 			= 	array();
		$USerData 		    = $this->common_model->UserAuthCheck('GET');
		if($USerData):
		 	$whereCon['where']  = array('status' => 'A');
		 	$Product_Settings 	= $this->common_model->getData('multiple','uw_settings',$whereCon);
		 	if($Product_Settings):
		 		$results = $Product_Settings;
		 	else:
		 		$results = [];
		 	endif;
		 	echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);	
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : winnerTestimonial
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to get winner's Testimonial.
	 * * Date 		   : 29 January 2024
	 * * **********************************************************************/
	public function winnerTestimonial()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_GET));
		$result 			= 	array();
		$USerData 		    = $this->common_model->UserAuthCheck('GET');
		if($USerData):
		 	$whereCon['where']  = array('status' => 'A');
		 	$Sortdata 			= array('testimonial_id' => -1);
		 	$Product_Settings 	= $this->common_model->getData('multiple','uw_uwin_testimonials',$whereCon,$Sortdata);
		 	foreach ($Product_Settings as $key => $PItems):
		 		$PItems_image = str_replace(' ', '%20' , $PItems['image']);
				$Product_Settings[$key]['image'] = base_url($PItems_image);
			endforeach;
		 	
		 	if($Product_Settings):
		 		$results = $Product_Settings;
		 		echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);	
		 	else:
		 		$results = [];
		 		echo outPut(0,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);	
		 	endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : checkWinner
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to check winner.
	 * * Date 		   : 02 February 2024
	 * * **********************************************************************/
	public function checkWinner()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 			= 	array();

		$USerData 		    = $this->common_model->UserAuthCheck('POST');
		if($USerData):
			$users_id 	=  $this->input->get('users_id');
			$tickect_id =  $this->input->post('tickect_id');

			if(empty($users_id)):
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
			elseif(empty($tickect_id)):
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_TICKET'),$result);die();
			else:
				
				// Checking coupon in collection.
				$tableName 			= "uw_uwin_winner";
				$whereCon['where']  = array('order_id' => $tickect_id ,'status' => (int)1);
			 	$CheckWinner 	    = $this->common_model->getData('multiple',$tableName,$whereCon);

			 	if($CheckWinner):
			 		$winnerStatus  = array();
				 	foreach ($CheckWinner as $key => $winnerItem):
				 		if($winnerItem['redeem_status'] == 'paid'):
				 			$winnerStatus[] = 'P';
				 		elseif($winnerItem['redeem_status'] != 'paid'):
				 			$winnerStatus[] = 'N';
				 		endif;
				 	endforeach;
			 	endif;

			 	if(!in_array('N', $winnerStatus)):
			 		$query = array('order_id' => $tickect_id ,'status' => (int)1 );
			 	else:
			 		$query = array('order_id' => $tickect_id ,'status' => (int)1  , "redeem_status" => array('$ne' => "paid") );
			 	endif;
			 	
				$tableName 			= "uw_uwin_winner";
				$whereCon['where']  = $query;
			 	$WinnerList 	    = $this->common_model->getData('multiple',$tableName,$whereCon);

			 	$tblName		   = "uw_lotto_orders";
			    $Fields 		   = array('_id','created_at');
		 	    $orderDetails 	   = $this->common_model->getSingleDataByParticularField($Fields,$tblName,'order_id',$tickect_id);
		 	    $order_date  	   = $orderDetails['created_at'];
		 	    
			 	if($WinnerList):
				 	$totalPrizeAmount   = 0;
				 	$totalCode 			= array();
				 	foreach ($WinnerList as $key => $items) :
				 	 $totalPrizeAmount   = $totalPrizeAmount+ $items['amount'];
				 	 $totalCode[]   = $items['code'];
				 	endforeach;
				 	
				 	$totalCode = implode(' / ', $totalCode);
				 	
				 	$winerList['voucher_id'] 		= $items['voucher_id'];
				 	$winerList['order_id']   		= $items['order_id'];
				 	$winerList['seller_first_name'] = $items['seller_first_name'];
				 	$winerList['seller_last_name'] 	= $items['seller_last_name'];
				 	$winerList['code'] 				= $totalCode;
				 	$winerList['amount'] 			= (string)$totalPrizeAmount;
				 	$winerList['status'] 			= $items['status'];
				 	$winerList['products_id'] 		= $items['products_id'];
				 	$winerList['created_at'] 		= $items['created_at'];
				 	// $winerList['created_by'] 		= $items['created_by'];
					$winerList["created_at"]        =   date('Y-m-d h:i A',strtotime($order_date)) ;
					$winerList["modified_at"]       =   date('Y-m-d h:i A',strtotime($items['modified_at'])) ;
				 	if($items['modified_by']):
				 		$winerList['modified_by'] 		= $items['modified_by'];
				 	endif;
				 	if($items['redeem_by_mode']):
				 		$winerList['redeem_by_mode'] 	= $items['redeem_by_mode'];
				 	endif;
				 	if($items['redeem_status']):
				 		$winerList['redeem_status'] 	= $items['redeem_status'];
				 	endif;
				 	if($items['seller_id']):
				 		$winerList['seller_id'] 		= $items['seller_id'];
				 	endif;
			 		$results = array($winerList);
			 	else:
			 		echo outPut(0,lang('SUCCESS_CODE'),lang('NOT_WINNER'),$result);die();
			 	endif;
			endif;
		 	echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);	
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

 
	/* * *********************************************************************
	 * * Function name : redeemByCash
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to Redeem Coupons.
	 * * Date 		   : 02 February 2024
	 * * Updated By    : Dilip Halder
	 * * Updated Date  : 24 February 2024
	 * * **********************************************************************/
	public function redeemByMode()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	

		$USerData 		     = $this->common_model->UserAuthCheck('POST');
		if($USerData):
				$users_id 			=  $this->input->post('users_id');
				$voucher_id 		=  $this->input->post('voucher_id');
				$tickect_id 		=  $this->input->post('tickect_id');
				$redeem_status 		=  $this->input->post('redeem_status');
				$redeem_by_mode 	=  $this->input->post('redeem_by_mode');

				if(empty($users_id)):
					echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
				elseif(empty($tickect_id)):
					echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_TICKET'),$result);die();
				elseif(empty($voucher_id)):
					echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_VOUCHER'),$result);die();
				elseif(empty($redeem_status)):
					echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_REDEEMSTATUS'),$result);die();
				elseif(empty($redeem_by_mode)):
					echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_RedeemByMode'),$result);die();
				else:

					// Checking coupon in collection.
					$tableName 			= "uw_uwin_winner";
					$whereCon['where']  = array('order_id' => $tickect_id , 'status' => (int)1 ,'redeem_status' => array('$ne' => 'paid') );
				 	$WinnerList 	    = $this->common_model->getData('multiple',$tableName,$whereCon);

				 	$totalPrizeAmount   = 0;
				 	$totalCode  		= array();
				 	foreach ($WinnerList as $key => $items) :
				 	 $totalPrizeAmount   = $totalPrizeAmount+ $items['amount'];
				 	 $totalCode[]   = $items['code'];
					 	if(!empty($items['redeem_status']) && $items['redeem_status'] == 'paid'):
					 		echo outPut(0,lang('SUCCESS_CODE'),lang('ALREADY_REDEEM'),$result);die();
					 	elseif((int)$totalPrizeAmount >= 1000 &&  $users_id != 100000000000110 ):
					 		echo outPut(0,lang('SUCCESS_CODE'),lang('BIG_WINNER_TEXT'),$result);die();
					 	endif;
				 	endforeach;

				 	$totalCode = implode(' / ', $totalCode);
					if($WinnerList):
						$updateParams['pos_device_id'] 	= $pos_device_id;
					 	$updateParams['redeem_status'] 	= $redeem_status;
						$updateParams['redeem_by_mode'] = $redeem_by_mode;
						$updateParams["modified_at"]    = date('Y-m-d H:i');
						$updateParams['seller_id'] 		= (int)$users_id;
						$updateParams['created_ip'] 	= $ip_address;
						$WInnner_whereCon  = array('order_id' => $this->input->post('tickect_id'), 'status' => (int)'1','redeem_status' => array('$ne' => 'paid') );
						$RedeemStatus 	   =  $this->common_model->editMultipleDataByMultipleCondition('uw_uwin_winner', $updateParams,$WInnner_whereCon);
						
						if($RedeemStatus >= 1):

							$tableName		   = "uw_users";
						    $Fields 		   = array('_id','users_id' ,'availableArabianPoints','store_name');
					 	    $userDetails 	   = $this->common_model->getSingleDataByParticularField($Fields,$tableName,'users_id',(int)$users_id);

					 	    $tblName		   = "uw_lotto_orders";
						    $Fields 		   = array('_id','order_id','product_id','total_price','created_at');
					 	    $orderDetails 	   = $this->common_model->getSingleDataByParticularField($Fields,$tblName,'order_id',$this->input->post('tickect_id'));

					 	    //Error Log generating for unmatched users...
					 	    if((int)$userDetails['users_id']  != (int)$users_id):
								$errorLog["order_id"]         = (int)$tickect_id;
								$errorLog["amount"]           = (int)$totalPrizeAmount;
								$errorLog["users_id"]         = (int)$users_id;
								$errorLog["fetched_users_id"] = (int)$userDetails['users_id'];
								$errorLog['client_details']   = json_encode($_SERVER);
								$errorLog["status"]           = "A";
								$this->geneal_model->addData('uw_error_log', $errorLog );

								$availableArabianPoints 	 =   (float)'0';
								// $end_balance 		 	     =   (float)'0';
								$user_OId 	 = '';
							  	$order_oid   = '';
							  	$productID   = '';
							  	$orderID     = $tickect_id;
							else:
							    $availableArabianPoints  =   (float)$userDetails['availableArabianPoints'];
								// $end_balance 		 	     =   (float)$userDetails['availableArabianPoints'];
							  	$user_OId 	 = $userDetails['_id']['$id'];
							  	$order_oid   = $orderDetails['_id']['$id'];
							  	$orderID     = $orderDetails['order_id'];
							  	$productID   = $orderDetails['product_id'];
					 	    endif;

							/* Load Balance Table -- after Sign Up*/
							$Redeemparam["load_balance_id"]          =   (int)$this->geneal_model->getNextSequence('uw_loadBalance');
							$Redeemparam["user_oid"]        	     =   new MongoDB\BSON\ObjectId($user_OId);
							$Redeemparam["order_oid"]       		 =   new MongoDB\BSON\ObjectId($order_oid);
							$Redeemparam["order_id"]       		     =   $orderID;
							$Redeemparam["product_id"]       		 =   (int)$productID;
							$Redeemparam["user_id_deb"]              =   (int)$users_id;
							$Redeemparam["user_id_cred"]             =   (int)0;
							$Redeemparam["upoints"]       		     =   (float)$totalPrizeAmount;
							$Redeemparam["record_type"]              =   'Debit';
							$Redeemparam["narration"]  			     =   'Redeem Prize';
							$Redeemparam["remarks"]  			     =   "Redeemed Prize ".$totalPrizeAmount." AED in cash";
							$Redeemparam["availableArabianPoints"] 	 =   (float)$availableArabianPoints;
							$Redeemparam["end_balance"] 		 	 =   (float)$availableArabianPoints;
							$Redeemparam["creation_ip"]         	 =   currentIp();
							$Redeemparam["created_at"]          	 =   date('Y-m-d H:i');
							$Redeemparam["created_by"]         	  	 =   (int)$users_id;
							$Redeemparam["status"]               	 =   "A";
							$this->geneal_model->addData('uw_loadBalance', $Redeemparam);
					   		$this->geneal_model->addRedeem_Cash_Amount_TO_Seller($users_id,(float)$totalPrizeAmount);

							   $result['seller_first_name'] = $WinnerList[0]['seller_first_name'];
							   $result['seller_last_name']  = $WinnerList[0]['seller_last_name'];
							   $result['order_id']          = $Redeemparam['order_id'];
							   $result['amount']     		= (float)$totalPrizeAmount;
							   $result['order_price'] 		= (float)$orderDetails['total_price'];
							   $result['order_date'] 		= date('Y-m-d h:i A', strtotime($orderDetails['created_at']));
							   $result['store_name']    	= $userDetails['store_name'];
							   $result['remarks']    		= $Redeemparam['remarks'];
							   $result['status']     		= $Redeemparam['status'];
						 	   $result['code'] 		 		= $totalCode;
							   $result['created_at'] 		= $Redeemparam['created_at'];
				  	 	   	   echo outPut(1,lang('SUCCESS_CODE'),lang('COUPON_REDEEMED_SUCCESFULLY'),$result);die();
				  	 	else:
					   	   	echo outPut(0,lang('FORBIDDEN_CODE'),lang('TRY_AGAIN'),$result);die();
						endif;
					else:
					 	echo outPut(0,lang('SUCCESS_CODE'),lang('ALREADY_REDEEM'),$result);die();
				 	endif;
				 	
				endif;
			 	// echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);	
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : uwinAllowedUser
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to check permission.
	 * * Date 		   : 02 February 2024
	 * * **********************************************************************/
	public function uwinAllowedUser()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_GET));
		$result 			= 	array();

		if(requestAuthenticate(APIKEY,'GET')):
			 	$whereCon['where']  = array('status' => 'A');
			 	$UwinPermission 	= $this->common_model->getData('multiple','uw_uwin_allowed_user',$whereCon);
			 	if($UwinPermission):
			 		$results = $UwinPermission;
			 	else:
			 		$results = [];
			 	endif;

			 	echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);	
			// endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : cancelOrderRequest
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to send reuest to admin to cancel request.
	 * * Date 		   : 09 February 2024
	 * * **********************************************************************/
	public function cancelOrderRequest()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 			= 	array();

		if(requestAuthenticate(APIKEY,'POST')):

				$users_id 			=  $this->input->post('users_id');
				$tickect_id 		=  $this->input->post('tickect_id');
				$remark 			=  $this->input->post('remark');

				if(empty($users_id)):
					echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
				elseif(empty($tickect_id)):
					echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_TICKET'),$result);die();
				elseif(empty($remark)):
					echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_REMARK'),$result);die();
				else:

					// Checking coupon in collection.
					$tableName 			= "uw_lotto_orders";
					$whereCon['where']  = array('order_id' => $tickect_id , 'user_id' => (int)$users_id );
				 	$orderDeatail 	    = $this->common_model->getData('single',$tableName,$whereCon);
					
					if( $orderDeatail['order_id'] === $tickect_id ):
						 
						$currentDate           = date('Y-m-d h:m:s');
						$orderDate 	 		   = $orderDeatail['created_at'];
						$CancallationDateLimit = date('Y-m-d h:m:s', strtotime($orderDate . ' +1 day'));

						echo "orderDate= ".$orderDate;
			 			echo "<br>";
						echo "CancallationDateLimit= ".$CancallationDateLimit;
			 			echo "<br>";
			 			echo "currentDate= ".$currentDate;
			 			die();

						$currentDate 		   = strtotime($currentDate);
						$CancallationDateLimit = strtotime($CancallationDateLimit);

						echo "orderDate= ".$orderDate;
			 			echo "<br>";
						echo "CancallationDateLimit= ".$CancallationDateLimit;
			 			echo "<br>";
			 			echo "currentDate= ".$currentDate;
			 			die();

				 		if($CancallationDateLimit >= $currentDate):
				 			echo "CancallationDateLimit= ".$CancallationDateLimit;
			 			echo "<br>";
			 			echo "currentDate= ".$currentDate;
			 			die();
				 		endif;

				 			// echo "CancallationDateLimit= ".$CancallationDateLimit;
				 			// echo "<br>";
				 			// echo "currentDate= ".$currentDate;

					 	die();
					 	

					 	 
					 	 


					endif;


					die();



					// if(!empty($WinnerList['redeem_status']) && $WinnerList['redeem_status'] == 'paid'):
				 	// 	echo outPut(0,lang('SUCCESS_CODE'),lang('ALREADY_REDEEM'),$result);die();

				 	// elseif((int)$WinnerList['amount']>= 1000):
				 	// 	echo outPut(0,lang('SUCCESS_CODE'),lang('BIG_WINNER_TEXT'),$result);die();
					// else:

				 	//   $updateParams['redeem_status'] 	= $redeem_status;
					//   $updateParams['redeem_by_mode'] = $redeem_by_mode;
					//   $updateParams['seller_id'] 		= (int)$users_id;
					//   $this->common_model->editData('uw_uwin_winner', $updateParams, 'voucher_id', (int)$voucher_id);

					//   $this->geneal_model->addRedeem_Cash_Amount_TO_Seller($users_id,(float)$WinnerList['amount']);

				  	//  echo outPut(0,lang('SUCCESS_CODE'),lang('COUPON_REDEEMED_SUCCESFULLY'),$result);die();
				 	// endif;
				endif;
			 	// echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);	
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : campaignFreezing
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to campaign Freezing.
	 * * Date 		   : 10 February 2024
	 * * **********************************************************************/
	public function campaignFreezing()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_GET));
		$result 			= 	array();

		if(requestAuthenticate(APIKEY,'GET')):
			 	$whereCon['where']  = array('status' => 'A');
			 	$campaignFreezing 	= $this->common_model->getData('multiple','uw_campaign_freezing',$whereCon);
			 	if($campaignFreezing):
			 		$results = $campaignFreezing;
			 	else:
			 		$results = [];
			 	endif;

			 	echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);	
			// endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : reconcileRequest
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to send reuest to admin to cancel request.
	 * * Date 		   : 12 March 2024
	 * * **********************************************************************/
	public function reconcileRequest()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	

		$USerData 		     = $this->common_model->UserAuthCheck('POST');
		if($USerData):

			$users_id 		=  $this->input->get('users_id');
			$request_no 	=  $this->input->post('request_no');

			if(empty($users_id)):
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
			elseif(empty($request_no)):
				echo outPut(0,lang('SUCCESS_CODE'),lang('REQUEST_NO'),$result);die();
			else:
				$tblName  			= 'uw_lotto_orders';
				$whereCon['where']  = array('request_no' => (int)$request_no , 'user_id' => (int)$users_id );
				$orderDetail        = $this->common_model->getData('single', $tblName , $whereCon);

				if($orderDetail['status'] == "A"):
					$results = $orderDetail; 
		 			echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);	
				elseif($orderDetail['status'] == "CL"):
	 				echo outPut(0,lang('SUCCESS_CODE'),lang('ORDER_CANCELLED'),$results);	
				else:
	 				echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_REQUEST_NO'),$results);	
				endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	
}
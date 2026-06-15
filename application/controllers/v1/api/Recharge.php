<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Recharge extends CI_Controller {
	
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
     * * Function name  : generateRechargeCoupon
     * * Developed By   : Sumit Bedwal
     * * Purpose        : This function used for generateRechargeCoupon 
     * * Date           : 04 Feb 2024
     * * **********************************************************************/
    public function generateRechargeCoupon()
    {
        try {

			$apiHeaderData 		=	getApiHeaderData();
			$this->generatelogs->putLog('APP',logOutPut($_POST));
			$result 							= 	array();

			$USerData 		   = $this->common_model->UserAuthCheck('POST');

			if($USerData):

				$usersID      = $this->input->get('users_id');
				$coupon_amount    = $this->input->post('coupon_amount');
				$request_no    = $this->input->post('request_no');
				
				if(empty($usersID)): 
					throw new Exception(lang('USER_ID_EMPTY'), 1);
				elseif(empty($coupon_amount)): 
					throw new Exception(lang('EMPTY_Amount'), 1);
				elseif(empty($request_no)): 
					throw new Exception(lang('REQUEST_NO_EMPTY'), 1);
				else:
					$tblName           = 'uw_users'; 
                	$whereCon['where'] =  array('users_id'=> (int)$usersID);
                	$UserData          = $this->common_model->getData('single',$tblName,$whereCon);

                	if(!empty($UserData) && $UserData['status'] == 'A'):

                	    if($UserData['availableArabianPoints'] >= $coupon_amount):
						
                	            $coupon_code_length = 12; // Length of the coupon code
                	            $code = generateRandomString($coupon_code_length, "n");
                	            $isDuplicate = true; // Flag to track duplicates

                	            while ($isDuplicate) {
                	                $whereCon['where'] = array('coupon_code' => (int)$code);
                	                $DuplicateCoupn = $this->common_model->getData('single', 'uw_coupon_code_only', $whereCon);

                	                if (empty($DuplicateCoupn)) {
                	                    $isDuplicate = false; // No duplicate found
                	                } else {
                	                    // Regenerate the code
                	                    $code = generateRandomString($coupon_code_length, "n");
                	                }
                	            }

                	            // Debug output
                	            $param['rc_id']                 = 'UTP'.$this->common_model->generateSerialNo('uw_rechargecoupons');
								$param['request_no']			= $request_no;
                	            $param['coupon_code']           = (int)$code;
                	            $param['aed']                   = (float)$coupon_amount;
                	            $param['coupon_code_amount']    = (float)$coupon_amount;
                	            $param['coupon_code_statys']    = "Active";
                	            $param['status']                = "A";
                	            $param['created_user']          = $UserData['users_type'];
                	            $param['created_by']            = (int)$usersID;
                	            $param['created_at']            = date('Y-m-d H:i');
                	            $param['modified_at']           = date('Y-m-d H:i');
                	            $param['created_date']          = date('Y-m-d H:i');;
                	            $param['expair_date']           = date('Y-m-d',strtotime('+1 years'));
                	            $param['user_oid']              = new MongoDB\BSON\ObjectId($UserData['_id']->{'$id'});
                	            $param['creation_ip']           = currentIp();
                	            $result                         = $this->geneal_model->addData('uw_coupon_code_only', $param);

                	            //Voucher Createing section start here ..
                	            if($result):
								
                	                // $commission_percentage = $UserData['recharge_commission_percentage']?$UserData['recharge_commission_percentage']:15;

                	                $commission_percentage = 15; // Default value
                	                if (!empty($UserData['recharge_commission_percentage'])) {
                	                    $commission_percentage = $UserData['recharge_commission_percentage'];
                	                } elseif ($UserData['recharge_commission_percentage'] === 0) {
                	                    $commission_percentage = 0;
                	                }


                	                $commission_amount     = $coupon_amount*$commission_percentage/100;
								
                	                // if($commission_amount > 0):
                	                    $availableArabianPoints             = ($UserData['availableArabianPoints'] - $coupon_amount) + $commission_amount;
                	                    // Updating remaing Upoints after Generating coupons.
                	                    $uparam['availableArabianPoints']   = (float)$availableArabianPoints;
                	                    $uparam['update_date']              = date('Y-m-d h:m');
                	                    $UserDetails                        = $this->common_model->editData('uw_users',$uparam, 'users_id',(int)$usersID);
									
                	                    // generating loadBalance for recharge coupon creation.. 
                	                    $loadBalance['load_balance_id'] =  (int)$this->geneal_model->getNextSequence('uw_loadBalance');
                	                    $loadBalance['user_oid']        =  new MongoDB\BSON\ObjectId($UserData['_id']->{'$id'});
                	                    $loadBalance['request_id']      =  $param['rc_id'];
										$loadBalance['request_no']	    =  $param['request_no'];
                	                    $loadBalance['request_oid']     =  new MongoDB\BSON\ObjectId($result['_id']->{'$id'});
                	                    $loadBalance['user_id_deb']     =  (int)$usersID;
                	                    $loadBalance['user_id_cred']    =  (int)0;
                	                    $loadBalance["availableArabianPoints"] =   (float)$UserData['availableArabianPoints'];
                	                    $loadBalance["end_balance"]            =   (float)$UserData['availableArabianPoints']-$coupon_amount;
                	                    $loadBalance['record_type']     = 'Debit';
                	                    $loadBalance['narration']       = 'Recharge Coupon';
                	                    $loadBalance['remarks']         = "Serial no. ".$param['rc_id'].'.';
                	                    $loadBalance['upoints']         = (float)$coupon_amount;
                	                    $loadBalance['creation_ip']     = currentIp();
                	                    $loadBalance['created_at']      = date('Y-m-d H:i');
                	                    $loadBalance['created_by']      = (int)$usersID;
                	                    $loadBalance['status']          = 'A';
                	                    $this->geneal_model->addData('uw_loadBalance', $loadBalance);

                	                    if($commission_percentage):
                	                        // Generating loadBalance for Commission amount adding..
                	                        $commisionBalance['load_balance_id'] =  (int)$this->geneal_model->getNextSequence('uw_loadBalance');
                	                        $commisionBalance['user_oid']        =  new MongoDB\BSON\ObjectId($UserData['_id']->{'$id'});
                	                        $commisionBalance['request_id']      =  $param['rc_id'];
											$commisionBalance['request_no']	     =  $param['request_no'];
                	                        $commisionBalance['request_oid']     =  new MongoDB\BSON\ObjectId($result['_id']->{'$id'});
                	                        $commisionBalance['user_id_deb']     =  (int)0;
                	                        $commisionBalance['user_id_cred']    =  (int)$usersID;
                	                        $commisionBalance["availableArabianPoints"] =   (float)$UserData['availableArabianPoints']-$coupon_amount;
                	                        $commisionBalance["end_balance"]            =   (float)$availableArabianPoints;
                	                        $commisionBalance['record_type']     = 'Credit';
                	                        $commisionBalance['narration']       = 'Recharge Commission';
                	                        $commisionBalance['remarks']         = "Commisssion added for recharge serial no. ".$param['rc_id'].'.';
                	                        $commisionBalance['upoints']         = (float)$commission_amount;
                	                        $commisionBalance['creation_ip']     = currentIp();
                	                        $commisionBalance['created_at']      = date('Y-m-d H:i');
                	                        $commisionBalance['created_by']      = (int)$usersID;
                	                        $commisionBalance['status']          = 'A';
                	                        $this->geneal_model->addData('uw_loadBalance', $commisionBalance);
                	                    endif;
                	                // endif;
                	                    echo outPut(1,lang('SUCCESS_CODE'),lang('VOUCHER_CREATED_SUCCESSFULLY'),$result);
									
                	            else:
                	                 echo outPut(0,lang('SUCCESS_CODE'),lang('FORBIDDEN_MSG'),$result);die();
                	            endif;
                	        //Voucher Createing section end here ..
							
                	    else:
                	        $error_msg = str_replace('###AMOUNT###', $coupon_amount ,  lang('LOW_AVAILABLE_BALANCE'));
                	        echo outPut(0,lang('FORBIDDEN_CODE'),$error_msg ,$result);die();
                	    endif;
                	else:
                	    echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_USER_ID'),$result);die();
                	endif;
				endif;
			else:
				throw new Exception(lang('FORBIDDEN_MSG'), 1);
			endif;
			
		} catch (\Throwable $th) {
			echo outPut(0,lang('SUCCESS_CODE'),$th->getMessage(),$result);
		}
    }


	/* * *********************************************************************
	 * * Function name : rechargeCouponList
	 * * Developed By  : Sumit Bedwal
	 * * Purpose  	   : Get the list data from uw_coupon_code_only
	 * * Date 		   : 5 Feb 2026
	 * * **********************************************************************/
	public function rechargeCouponList()
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
				// print_r($this->input->post()); die;

				if(empty($usersId)):
					throw new Exception(lang('USER_ID_EMPTY'), 1);
				elseif(empty($pageno)):
					throw new Exception(lang('EMPTY_PAGE_NO'), 1);
				elseif(empty($itemsPerPage)):
					throw new Exception(lang('EMPTY_ITEMPERPAGE'), 1);
				else:

					$tblName = 'uw_users';
					$whereCon['where']['users_id'] = (int)$usersId;
					$userData = $this->common_model->getData('single',$tblName,$whereCon);
					if(empty($userData)):
						print_r('user not found'); die;
						throw new Exception(lang('USER_NOT_FOUND'), 1);
					elseif($userData['status'] != "A"):
						throw new Exception(lang('ACCOUNT_BLOCKED'), 1);
					else:

						$tblName  = 'uw_coupon_code_only';
						$whereCon = array();
						$whereCon['where']['status'] = "A";
						$whereCon['where']['user_oid'] = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});

						if(!empty($startDate) && !empty($endDate)):
							$whereCon['where']['created_at'] = array(
								'$gte' => date('Y-m-d H:i',strtotime($startDate)),
								'$lte' => date('Y-m-d H:i',strtotime($endDate))
							);
						endif;
						// echo '<pre>';
						// print_r($whereCon); die;

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

	/* * *********************************************************************
	 * * Function name : cancelRechargeCoupon
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : Cancel the recharge coupon
	 * * Date 		   : 11 Feb 2026
	 * * **********************************************************************/
	public function cancelRechargeCoupon()
	{
		try {

			$apiHeaderData 		=	getApiHeaderData();
			$this->generatelogs->putLog('APP',logOutPut($_POST));
			$result 							= 	array();

			$USerData 		   = $this->common_model->UserAuthCheck('POST');
			if($USerData):

				$usersID    = $this->input->get('users_id');
				$requestNo  = $this->input->post('request_no');
				$couponCode = $this->input->post('coupon_code');
				
				if(empty($usersID)): 
					throw new Exception(lang('USER_ID_EMPTY'), 1);
				elseif(empty($requestNo)): 
					throw new Exception(lang('REQUEST_NO_EMPTY'), 1);
				elseif(empty($couponCode)): 
					throw new Exception(lang('COUPON_CODE_ID_EMPTY'), 1);
				else:

					$tableName = 'uw_users';
					$whereCon['where']['users_id'] = (int)$usersID;
					$whereCon['where']['status'] = 'A';
					$userDetails = $this->common_model->getData('single',$tableName,$whereCon);
					if(empty($userDetails)):
						throw new Exception(lang('INVALID_USER'), 1);
					elseif($userDetails['availableArabianPoints'] < $amount):
						$message = str_replace('###AMOUNT###', $amount, lang('LOW_RECHARGE_AVAILABLE_BALANCE'));
						throw new Exception($message, 1);
					else:

						$RtableName = 'uw_coupon_code_only';
						$whereCon  = array();
						$whereCon['where']['coupon_code'] = (int)$couponCode;
						$rechargeCoupon = $this->common_model->getData('single',$RtableName,$whereCon);

						if(empty($rechargeCoupon)):
							$message = str_replace('###COUPON_CODE###', $couponCode, lang('INVALID_COUPON_CODE'));
							throw new Exception($message, 1);
						elseif($rechargeCoupon['status'] == "CL"):
							$message = str_replace('###COUPON_CODE###', $couponCode, lang('COUPON_ALREADY_CANCELLED'));
							throw new Exception($message, 1);
						else:

							$this->session->sess_regenerate();
							$session = $this->mongodb_client->client->startSession();
							$session->startTransaction();

							$rechargeCouponId   = $rechargeCoupon['_id']->{'$id'};
							$rechargeCouponRcId = $rechargeCoupon['rc_id'];
							$userId             = $rechargeCoupon['created_by'];
							
							// 1st step update user available arabian points
							$amount = (float)$userDetails['availableArabianPoints']+(float)$rechargeCoupon['coupon_code_amount'];
							$usersParam['availableArabianPoints'] = (float)$amount;
							$usersWhereCon = array( 'users_id' => (int)$userId);
							$user  = $this->mongodb_client->updateDocument('uw_users', $usersWhereCon, ['$set' => $usersParam], $session);
							
							// 2nd step update load balance table status to cancelled
							$tableName  = 'uw_loadBalance';
							$rechargeCouponParam['status'] = 'R'; 
							$rechargeCouponWhereCon        = array( 'request_oid' => new MongoDB\BSON\ObjectId($rechargeCouponId));
							$data  = $this->mongodb_client->updateDocument($tableName, $rechargeCouponWhereCon, ['$set' => $rechargeCouponParam], $session);
							
							$RCouponParam['status']             = 'CL'; 
							$RCouponParam['coupon_code_statys'] = 'Cancelled'; 
							$RCouponWhereCon  = array( '_id' => new MongoDB\BSON\ObjectId($rechargeCouponId));
							$data11  = $this->mongodb_client->updateDocument($RtableName, $RCouponWhereCon, ['$set' => $RCouponParam], $session);

							// 3rd step update load balance table status to cancelled
							$Param["load_balance_id"]		 =	(int)$this->common_model->getNextSequence('uw_loadBalance');
							$Param["user_oid"] 				 =	new MongoDB\BSON\ObjectId($userDetails['_id']->{'$id'});
							$Param['request_id']      		 =  $rechargeCouponRcId;
							$Param['request_oid']     		 =  new MongoDB\BSON\ObjectId($rechargeCouponId);
							$Param["user_id_cred"] 			 =	(int)$userId;
							$Param["user_id_deb"]			 =	(int)0;
							$Param["upoints"] 				 =	(float)$rechargeCoupon['coupon_code_amount'];
							$Param["availableArabianPoints"] =	(float)$userDetails['availableArabianPoints'];
							$Param["end_balance"] 			 =	(float)$amount;
							$Param["record_type"] 			 =	'Credit';
							$Param["narration"]				 =	'Recharge Coupon Cancelled';
							$Param["remarks"]				 =	"Serial No. ".$rechargeCouponRcId;
							$Param["creation_ip"] 			 =	$this->input->ip_address();
							$Param["created_at"] 			 =	date('Y-m-d H:i');
							$Param["created_user"] 			 =	$userDetails['users_type'];
							$Param["created_by"] 			 =	(int)$userId;
							$Param["status"] 				 =	"A";
							$orderInsertID                   = $this->mongodb_client->insertDocument('uw_loadBalance', $Param, $session);	

							$session->commitTransaction();
							$this->mongodb_client->commitTransaction($session);
							 
							echo outPut(1,lang('SUCCESS_CODE'),lang('RECHARGE_COUPON_CANCELLED_SUCCESSFULLY'),$result);
						endif;
					endif;
				endif;
			else:
				throw new Exception(lang('FORBIDDEN_MSG'), 1);
			endif;
			
		} catch (\Throwable $th) {
			echo outPut(0,lang('SUCCESS_CODE'),$th->getMessage(),$result);
		}
	}

	/* * *********************************************************************
	 * * Function name : createRecharge
	 * * Developed By  : Dilip Halder
	 * * Purpose       : This function used to create recharge.
	 * * Date          : 02 February 2026
	 * * **********************************************************************/
	public function createRecharge()
	{
		try {

			$apiHeaderData 		=	getApiHeaderData();
			$this->generatelogs->putLog('APP',logOutPut($_POST));
			$result 							= 	array();

			$USerData 		   = $this->common_model->UserAuthCheck('POST');
			if($USerData):

				$usersID      = $this->input->get('users_id');
				$requestNo    = $this->input->post('request_no');
				$countryCode  = $this->input->post('country_code');
				$mobileNo     = $this->input->post('mobile_no');
				$amount       = $this->input->post('amount');
				
				if(empty($usersID)): 
					throw new Exception(lang('USER_ID_EMPTY'), 1);
				elseif(empty($requestNo)): 
					throw new Exception(lang('REQUEST_NO_EMPTY'), 1);
				elseif(empty($countryCode)): 
					throw new Exception(lang('COUNTRY_EMPTY'), 1);
				elseif(empty($mobileNo)): 
					throw new Exception(lang('MOBILE_EMPTY'), 1);
				elseif(empty($amount)): 
					throw new Exception(lang('RECHARGE_AMOUNT_EMPTY'), 1);
				else:
					
					$tableName = 'uw_users';
					$whereCon['where']['users_id'] = (int)$usersID;
					$whereCon['where']['status'] = 'A';
					$userDetails = $this->common_model->getData('single',$tableName,$whereCon);
					
					if(empty($userDetails)):
						throw new Exception(lang('INVALID_USER'), 1);
					elseif($userDetails['availableArabianPoints'] < $amount):
						$message = str_replace('###AMOUNT###', $amount, lang('LOW_RECHARGE_AVAILABLE_BALANCE'));
						throw new Exception($message, 1);
					else:

						$tableName = 'uw_users';
						$whereCon = array();
						$whereCon['where']['country_code'] = $countryCode;
						$whereCon['where']['users_mobile'] = (int)$mobileNo;
						$whereCon['where']['status'] = 'A';
						$RechargeUser = $this->common_model->getData('single',$tableName,$whereCon);
                        if(empty($RechargeUser)):
							$message = str_replace('###MOBILE###', $countryCode.$mobileNo, lang('INVALID_RECHARGEUSER'));
							throw new Exception($message, 1);
						else:

							$this->session->sess_regenerate();
							$session = $this->mongodb_client->client->startSession();
							$session->startTransaction();
							
							$updateData       = array();
							$rechargeAmount   = (float)$RechargeUser['availableArabianPoints'] + (float)$amount;
							$RechargeParam    = array('availableArabianPoints' => (float)$rechargeAmount);
							$RechargeWhereCon = array('users_id' => (int)$RechargeUser['users_id']);
							$record = $this->mongodb_client->updateDocument( $tableName, $RechargeWhereCon, ['$set' => $RechargeParam], $session);
							
							/* Load Balance Table -- BTB user*/
							$rechargeFormParam["load_balance_id"]    = (int)$this->geneal_model->getNextSequence('uw_loadBalance');
							$rechargeFormParam["user_oid"]           = new MongoDB\BSON\ObjectId($userDetails['_id']->{'$id'});
							$rechargeFormParam["request_no"]         = (int)$requestNo;
							$rechargeFormParam["user_id_cred"]       = (int)$RechargeUser['users_id'];
							$rechargeFormParam["user_id_deb"]        = (int)$userDetails['users_id'];
							$rechargeFormParam["user_id_to"]         = (int)$RechargeUser["users_id"];
							$rechargeFormParam["upoints"]            = (float)$amount;
							$rechargeFormParam["record_type"]        = 'Debit';
							$rechargeFormParam["narration"]          = 'Recharge';
							$rechargeFormParam["remarks"]            = $amount.' AED recharged to '.$RechargeUser['users_mobile'];
							$rechargeFormParam["availableArabianPoints"]   = (float)$userDetails['availableArabianPoints'];
							$rechargeFormParam["end_balance"]              = (float)$userDetails['availableArabianPoints']-$amount;
							$rechargeFormParam["creation_ip"]        = currentIp();
							$rechargeFormParam["created_at"]         = date('Y-m-d H:i');
							$rechargeFormParam["created_by"]         = (int)$userDetails['users_id'];
							$rechargeFormParam["status"]             = "A";
							$orderInsertID  = $this->mongodb_client->insertDocument('uw_loadBalance', $rechargeFormParam, $session);	


							 /* Load Balance Table -- BTC user*/
							 $rechargeToUserParam["load_balance_id"]     = (int)$this->geneal_model->getNextSequence('uw_loadBalance');
							 $rechargeToUserParam["request_no"]          = (int)$requestNo;
							 $rechargeToUserParam["user_oid"]            = new MongoDB\BSON\ObjectId($RechargeUser['_id']->{'$id'});
							 $rechargeToUserParam["user_id_cred"]        = (int)$RechargeUser["users_id"];
							 $rechargeToUserParam["user_id_deb"]         = (int)$userDetails['users_id'];
							 $rechargeToUserParam["upoints"]             = (float)$amount;
							 $rechargeToUserParam["record_type"]         = 'Credit';
							 $rechargeToUserParam["narration"]           = 'Recharge';
							 $rechargeToUserParam["remarks"]             = $amount.' AED recharged';
							 $rechargeToUserParam["availableArabianPoints"]  = (float)$RechargeUser['availableArabianPoints'];
							 $rechargeToUserParam["end_balance"]             = (float)$RechargeUser['availableArabianPoints']+$amount;
							 $rechargeToUserParam["creation_ip"]         = currentIp();
							 $rechargeToUserParam["created_at"]          = date('Y-m-d H:i');
							 $rechargeToUserParam["created_by"]          = (int)$userDetails['users_id'];
							 $rechargeToUserParam["created_user_id"]     = (int)$userDetails['users_id'];
							 $rechargeToUserParam["status"]              = "A";
							$orderInsertID  = $this->mongodb_client->insertDocument('uw_loadBalance', $rechargeToUserParam, $session);	
							/* End */
							$session->commitTransaction();
							$this->mongodb_client->commitTransaction($session);

							$result  = array(
								'request_no'   => (int)$rechargeToUserParam["load_balance_id"],
								'upoints'      => $amount,
								'record_type'  => 'Debit',
								'narration'    => 'Recharge',
								'created_at'   => $rechargeToUserParam["created_at"],
								'users_name'   => $RechargeUser['users_name']." ".$RechargeUser['last_name'],
								'users_email'  => $RechargeUser['users_email'],
								'country_code' => $RechargeUser['country_code'],
								'users_mobile' => $RechargeUser['users_mobile'],
							);
							echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$result);
						endif;
					endif;
				endif;
			else:
				throw new Exception(lang('FORBIDDEN_MSG'), 1);
			endif;
			
		} catch (\Throwable $th) {
			echo outPut(0,lang('SUCCESS_CODE'),$th->getMessage(),$result);
		}
	
	}

	/* * *********************************************************************
	 * * Function name : reverseRecharge
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : Reverse the recharge
	 * * Date 		   : 06 Mar 2026
	 * * **********************************************************************/
	public function reverseRecharge()
	{
		try {
			$apiHeaderData 		=	getApiHeaderData();
			$this->generatelogs->putLog('APP',logOutPut($_POST));
			$result 							= 	array();

			$USerData 		   = $this->common_model->UserAuthCheck('POST');
			if($USerData):
				$usersID    = $this->input->get('users_id');
				$requestNo  = $this->input->post('request_no');
				if(empty($usersID)): 
					throw new Exception(lang('USER_ID_EMPTY'), 1);
				elseif(empty($requestNo)): 
					throw new Exception(lang('REQUEST_NO_EMPTY'), 1);
				else:

					$tableName = 'uw_users';
					$whereCon['where']['users_id'] = (int)$usersID;
					$whereCon['where']['status']   = 'A';
					$sellerDetails = $this->common_model->getData('single',$tableName,$whereCon);
					if(empty($sellerDetails)):
						throw new Exception(lang('INVALID_USER'), 1);
					else:
						
						$RtableName      = 'uw_loadBalance';
						$whereCon        = array();
						$whereCon['where']['request_no']  = (int)$requestNo;
						$whereCon['where']['record_type'] = 'Debit';
						$loadBalanceData = $this->common_model->getData('single',$RtableName,$whereCon);
						if(empty($loadBalanceData) || count($loadBalanceData) == 0):
							throw new Exception(lang('INVALID_REQUEST_NO'), 1);
						else:

							$whereCon = array();
							$whereCon['where']['users_id'] = (int)$loadBalanceData['user_id_cred'];
							$whereCon['where']['status'] = 'A';
							$customerDetails = $this->common_model->getData('single',$tableName,$whereCon);
						    if(empty($customerDetails)):
								throw new Exception(lang('RECHARGE_CANT_BE_REVERSED'), 1);
							elseif($customerDetails['availableArabianPoints'] < $loadBalanceData['upoints'] ):
								$message = str_replace('###AMOUNT###', $loadBalanceData['upoints'], lang('LOW_REVERSE_RECHARGE_AVAILABLE_BALANCE'));
								throw new Exception($message, 1);
							else:

                                $this->session->sess_regenerate();
								$session = $this->mongodb_client->client->startSession();
								$session->startTransaction();
                                
								// SELLER BALANCE DETAILS
								$amount = (float)$sellerDetails['availableArabianPoints']+(float)$loadBalanceData['upoints'];
								$sellerParam['availableArabianPoints'] = (float)$amount;
								$sellerWhereCon = array( 'users_id' => (int)$sellerDetails['users_id']);
								$seller  = $this->mongodb_client->updateDocument('uw_users', $sellerWhereCon, ['$set' => $sellerParam], $session);

								// CUSTOMER BALANCE DETAILS
								$amount = (float)$customerDetails['availableArabianPoints']-(float)$loadBalanceData['upoints'];
								$customerParam['availableArabianPoints'] = (float)$amount;
								$customerWhereCon = array( 'users_id' => (int)$customerDetails['users_id']);
								$customer  = $this->mongodb_client->updateDocument('uw_users', $customerWhereCon, ['$set' => $customerParam], $session);

								// Added cancellection balance details
								// 2nd step update load balance table status to cancelled
								$loadBalanceParam['status']      = 'R'; 
								$loadBalanceParam['update_date'] = date('Y-m-d H:i:s'); 
								$loadBalanceParam['updated_by']  = (int)$usersID;
								$loadBalanceParam['remarks']     = "Recharge amount ".$loadBalanceData['upoints']." AED has been reversed to ".$customerDetails['users_mobile'];
								$loadBalanceWhereCon             = array( 'request_no' => (int)$requestNo);
								$oldLoadBalanceData =  $this->mongodb_client->updateDocument($RtableName, $loadBalanceWhereCon, ['$set' => $loadBalanceParam], $session);
								

								$sellerParam['load_balance_id'] =  (int)$this->common_model->getNextSequence('uw_loadBalance');
								$sellerParam['user_oid']        =  new MongoDB\BSON\ObjectId($sellerDetails['_id']->{'$id'});
								$sellerParam['request_id']      =  $loadBalanceData['load_balance_id'];
								$sellerParam['request_oid']     =  new MongoDB\BSON\ObjectId($loadBalanceData['_id']->{'$id'});
								$sellerParam['user_id_deb']     =  (int)$customerDetails['users_id'];
								$sellerParam['user_id_cred']    =  (int)$sellerDetails['users_id'];
								$sellerParam["availableArabianPoints"]  =	(float)$sellerDetails['availableArabianPoints'];
								$sellerParam["end_balance"] 	= (float)$sellerDetails['availableArabianPoints'] + (float)$loadBalanceData['upoints'];
								$sellerParam['record_type']     = 'Credit';
								$sellerParam['narration']       = 'Reverse Recharge';
								$sellerParam['remarks']         = "Recharge has been reversed to ".$customerDetails['users_mobile'];
								$sellerParam['upoints']         = (float)$loadBalanceData['upoints'];
								$sellerParam['creation_ip']     = $this->input->ip_address();;
								$sellerParam['created_at']      = date('Y-m-d H:i');
								$sellerParam['created_by']      = (int)$sellerDetails['users_id'];
								$sellerParam['status']          = 'A';
								$sellerLoadBalance = $this->mongodb_client->insertDocument('uw_loadBalance', $sellerParam, $session);
								
								$customerParam['load_balance_id']		= (int)$this->common_model->getNextSequence('uw_loadBalance');
								$customerParam["user_oid"] 				= new MongoDB\BSON\ObjectId($customerDetails['_id']->{'$id'});
								$customerParam['request_id']      		= $loadBalanceData['load_balance_id'];
								$customerParam['request_oid']     		= new MongoDB\BSON\ObjectId($loadBalanceData['_id']->{'$id'});
								$customerParam["user_id_deb"] 			= (int)$customerDetails['users_id'];
								$customerParam['user_id_cred']			= (int)$sellerDetails['users_id'];
								$customerParam['upoints']				= (float)$loadBalanceData['upoints'];
								$customerParam["availableArabianPoints"]= (float)$customerDetails['availableArabianPoints'];
								$customerParam["end_balance"] 			= (float)$customerDetails['availableArabianPoints'] - (float)$loadBalanceData['upoints'] ;
								$customerParam['record_type']			= 'Debit';
								$customerParam["narration"] 			= 'Reverse Recharge';
								$customerParam['remarks']				= "Recharge has been reversed to ".$customerDetails['users_mobile'];
								$customerParam['creation_ip']			= currentIp();
								$customerParam['created_at']		    = date('Y-m-d H:i:s');//currentDateTime();
								$customerParam['created_by']		    = (int)$sellerDetails['users_id'];
								$customerParam['update_date']			= date('Y-m-d H:i:s');//currentDateTime();
								$customerParam['status']				= 'R';
								$customerParam["created_user_id"] 		= (int)$sellerDetails['users_id'];
								$customerLoadBalance = $this->mongodb_client->insertDocument('uw_loadBalance', $customerParam, $session);
								 
								$session->commitTransaction();
								$this->mongodb_client->commitTransaction($session);

								$result = array(
									'request_no'   => (int)$requestNo,
									'upoints'      => $loadBalanceData['upoints'],
									'record_type'  => 'Debit',
									'narration'    => 'Reverse Recharge',
								);

								echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$result);
							endif;
						endif;

					endif;

				endif;
			else:
				throw new Exception(lang('FORBIDDEN_MSG'), 1);
			endif;
		} catch (\Throwable $th) {
			echo outPut(0,lang('SUCCESS_CODE'),$th->getMessage(),$result);
		}
	}
	 
}
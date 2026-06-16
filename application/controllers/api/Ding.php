<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ding extends CI_Controller {
	
	var $postdata;
	var $user_agent;
	var $request_url; 
	var $method_name;
	
	public function  __construct() 	
	{ 
		parent:: __construct();
		// error_reporting(E_ALL ^ E_NOTICE); 
        	// error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);   
			error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_WARNING);
		@ini_set('display_errors', '0');
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
	 * * Function name : getCountryList
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to getCountryList
	 * * Date 		   : 20 March 2026
	 * * **********************************************************************/
	public function getCountryList()
	{
		$apiHeaderData = getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_GET));
		$result 	   = array();

		try {
			if(requestAuthenticate(APIKEY,'GET')):
			   $usersId = $this->input->get('users_id');
			   if(empty($usersId)):
					throw new Exception(lang('USER_ID_EMPTY'), 1);
			   else:
					$tblName = 'uw_users';
					$whereCon['where']['users_id'] = (int)$usersId;
					$userData = $this->common_model->getData('single',$tblName,$whereCon);
					if(empty($userData)):
						throw new Exception(lang('INVALID_USER'), 1);
					elseif($userData['status'] != 'A'):
						throw new Exception(lang('ACCOUNT_BLOCKED'), 1);
					else:
						$tblName    = 'ding_country_list';
						$whereCon1['where']['status'] = 'A';
						$whereCon1['select'] = array('CountryIso','CountryName','InternationalDialingInformation','RegionCodes' );
						$resultData = $this->common_model->getData('multiple',$tblName,$whereCon1);
						if(empty($resultData)):
							throw new Exception(lang('DATA_NOT_FOUND'), 1);
						else:
							(object) $result['countryList'] = $resultData;
							echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$result);
						endif; 
					endif;
			   endif;
			else:
				throw new Exception(lang('FORBIDDEN_MSG'),1);
			endif;
		} catch (Exception $e) {
			$errorMessage = trim((string)$e->getMessage());
			if($errorMessage === ''):
				$errorMessage = lang('TRY_AGAIN');
			endif;
			echo outPut(0,lang('SUCCESS_CODE'),$errorMessage,$result);	
		}
	}

	/* * *********************************************************************
	 * * Function name : getaccount
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to getaccount
	 * * Date 		   : 23 March 2026
	 * * **********************************************************************/
	public function getaccount()
	{
		$apiHeaderData = getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 	   = array();

		try {
			if(requestAuthenticate(APIKEY,'POST')):
				$usersId   = $this->input->post('users_id');
				$accountno = $this->input->post('accountno');
				if(empty($usersId)):
					throw new Exception(lang('USER_ID_EMPTY'), 1);
				else:
					$tblName = 'uw_users';
					$whereCon['where']['users_id'] = (int)$usersId;
					$userData = $this->common_model->getData('single',$tblName,$whereCon);
					if(empty($userData)):
						throw new Exception(lang('INVALID_USER'), 1);
					elseif($userData['status'] != 'A'):
						throw new Exception(lang('ACCOUNT_BLOCKED'), 1);
					else:

						$tblName     = 'ding_settings';
						$whereCon1['where']['status'] = 'A';
						$whereCon1['select'] = array('api_key');
						$apiSettings = $this->common_model->getData('single',$tblName,$whereCon1);
						$apiKey      = $apiSettings['api_key'];	

						$curl = curl_init();
						curl_setopt_array($curl, array(
							CURLOPT_URL => 'https://api.dingconnect.com/api/V1/GetAccountLookup?accountNumber='.$accountno,
							CURLOPT_RETURNTRANSFER => true,
							CURLOPT_ENCODING => '',
							CURLOPT_MAXREDIRS => 10,
							CURLOPT_TIMEOUT => 0,
							CURLOPT_FOLLOWLOCATION => true,
							CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
							CURLOPT_CUSTOMREQUEST => 'GET',
							CURLOPT_HTTPHEADER => array(
							  'api_key: '.$apiKey,
							),
						));
						  
						$response      = curl_exec($curl);
						$responseArray = json_decode($response, true);

						$countryIso   = $responseArray['CountryIso'];
						$providerCode = $responseArray['Items'][0]['ProviderCode'];
						curl_close($curl);

						$tblName      = 'ding_provider_list';
						$whereProviderCon['where']['CountryIso']   = $countryIso;
					    //   $whereProviderCon['where']['ProviderCode'] = $providerCode;
						$whereProviderCon['where']['status']       = 'A';
						$whereProviderCon['where']['PaymentTypes'] = 'Prepaid';
						$whereProviderCon['select'] = array('Name','ValidationRegex','Name','LogoUrl','markup_commission','ProviderCode');
						$providerList = $this->common_model->getData('multiple',$tblName,$whereProviderCon);

						$tblName = 'ding_products_list';
						$whereProductCon['where']['ProviderCode'] = $providerCode;
						$whereProductCon['where']['status']       = 'A';
						$planList = $this->common_model->getData('multiple',$tblName,$whereProductCon);
						if(!empty($planList)):
						foreach($planList as $key => $plan):
							$LocalizationKey = $plan['LocalizationKey'];
							$tblName         = 'ding_products_description_list';
							$whereProductDetailCon['where']['LocalizationKey'] = $LocalizationKey;
							$whereProductDetailCon['where']['status']       = 'A';
							$planDetail = $this->common_model->getData('single',$tblName,$whereProductDetailCon);
							$planList[$key]['planDetail'] = $planDetail;
						endforeach;
						endif;

						$resultData['accountinfo']  = $responseArray;
						$resultData['providerList'] = $providerList;
						$resultData['planList']     = $planList;
						if(empty($resultData)):
						throw new Exception(lang('DATA_NOT_FOUND'), 1);
						else:
						(object) $result['list'] = $resultData;
						echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$result);
						endif;
					endif;
				endif;
			else:
				throw new Exception(lang('FORBIDDEN_MSG'),1);
			endif;
		} catch (Exception $e) {
			$errorMessage = trim((string)$e->getMessage());
			if($errorMessage === ''):
				$errorMessage = lang('TRY_AGAIN');
			endif;
			echo outPut(0,lang('SUCCESS_CODE'),$errorMessage,$result);	
		}
	}

	/* * *********************************************************************
	 * * Function name : getPlanList
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to getPlanList
	 * * Date 		   : 26 March 2026
	 * * **********************************************************************/
	public function getPlanList()
	{
		$apiHeaderData = getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 	   = array();
		
		try {
			if(requestAuthenticate(APIKEY,'POST')):
				$usersId      = $this->input->post('users_id');
			    $countryIso   = $this->input->post('country_iso');
			    $providerCode = $this->input->post('provider_code');
			    if(empty($usersId)):
					throw new Exception(lang('USER_ID_EMPTY'), 1);
				elseif(empty($countryIso)):
					throw new Exception(lang('EMPTY_COUNTRY_ISO'), 1);
			    else:
					$tblName = 'uw_users';
					$whereCon['where']['users_id'] = (int)$usersId;
					$userData = $this->common_model->getData('single',$tblName,$whereCon);
					if(empty($userData)):
						throw new Exception(lang('INVALID_USER'), 1);
					elseif($userData['status'] != 'A'):
						throw new Exception(lang('ACCOUNT_BLOCKED'), 1);
					else:
						$tblName    = 'ding_products_list';
						$whereCon1['where']['status'] = 'A';
						$whereCon1['where']['RegionCode'] = $countryIso;
						if(!empty($providerCode)):
							$whereCon1['where']['ProviderCode'] = $providerCode;
						endif;
						$resultData = $this->common_model->getData('multiple',$tblName,$whereCon1);
						if(!empty($resultData)):
							foreach($resultData as $key => $plan):
								$LocalizationKey = $plan['LocalizationKey'];
								$tblName         = 'ding_products_description_list';
								$whereProductDetailCon['where']['LocalizationKey'] = $LocalizationKey;
								$whereProductDetailCon['where']['status']       = 'A';
								$planDetail = $this->common_model->getData('single',$tblName,$whereProductDetailCon);
								$resultData[$key]['planDetail'] = $planDetail;
							endforeach;
						endif;
						if(empty($resultData)):
							throw new Exception(lang('DATA_NOT_FOUND'), 1);
						else:
							(object) $result['planList'] = $resultData;
							echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$result);
						endif; 
					endif;
			    endif;
			else:
				throw new Exception(lang('FORBIDDEN_MSG'),1);
			endif;
		} catch (Exception $e) {
			$errorMessage = trim((string)$e->getMessage());
			if($errorMessage === ''):
				$errorMessage = lang('TRY_AGAIN');
			endif;
			echo outPut(0,lang('SUCCESS_CODE'),$errorMessage,$result);	
		}
	}

	/* * *********************************************************************
	 * * Function name : createRecharge
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to createRecharge
	 * * Date 		   : 26 March 2026
	 * * **********************************************************************/
	public function createRecharge()
	{
		$apiHeaderData = getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 	   = array();
		
		try {
			if(requestAuthenticate(APIKEY,'POST')):
				$usersId         = $this->input->post('users_id');
				$skuCode         = $this->input->post('sku_code');
				$sendValue       = $this->input->post('send_value');
				$accountNumber   = $this->input->post('account_number');
				$sendCurrencyIso = $this->input->post('send_currency_iso');
				
				if(empty($usersId)):
					throw new Exception(lang('USER_ID_EMPTY'), 1);
				elseif(empty($skuCode)):
					throw new Exception(lang('EMPTY_SKU_CODE'), 1);
				elseif(empty($sendValue)):
					throw new Exception(lang('EMPTY_SEND_VALUE'), 1);
				elseif(empty($accountNumber)):
					throw new Exception(lang('EMPTY_ACCOUNT_NUMBER'), 1);
				elseif(empty($sendCurrencyIso)):
					throw new Exception(lang('EMPTY_SEND_CURRENCY_ISO'), 1);
				else:
					$tblName = 'uw_users';
					$whereCon['where']['users_id'] = (int)$usersId;
					$userData = $this->common_model->getData('single',$tblName,$whereCon);
					if(empty($userData)):
						throw new Exception(lang('INVALID_USER'), 1);
					elseif($userData['status'] != 'A'):
						throw new Exception(lang('ACCOUNT_BLOCKED'), 1);
					else:


						$tblName     = 'ding_products_list';
						$whereCon11['where']['status'] = 'A';
						$whereCon11['where']['SkuCode'] = $skuCode;
						$productData  = $this->common_model->getData('single',$tblName,$whereCon11);
						$providerData = $this->common_model->getData('single','ding_provider_list',array('where'=>array('ProviderCode'=>$productData['ProviderCode'],'status'=>'A')));

						if(empty($productData)):
							throw new Exception(lang('INVALID_SKU_CODE'), 1);
						else:

							$maximum = $productData['Maximum'] ?? null;
							if(is_object($maximum)):
								$planValue = $maximum->SendValue ?? '';
							elseif(is_array($maximum)):
								$planValue = $maximum['SendValue'] ?? '';
							else:
								$planValue = '';
							endif;
							$planValue = round((float)$planValue, 2);
							$sendValue = round((float)$sendValue, 2);
							if($planValue <= 0 || $planValue != $sendValue):
								throw new Exception(lang('INVALID_SEND_VALUE'), 1);
							else:

								// Get API Settings
								$tblName = 'ding_settings';
								$whereCon1['where']['status'] = 'A';
								$whereCon1['select'] = array( 'api_key','api_mode','markup_commission_percentage');
								$apiSettings = $this->common_model->getData('single',$tblName,$whereCon1);
								$apiKey                     = $apiSettings['api_key'];
								
								if(!empty($providerData['markup_commission']) && $providerData['markup_commission'] > 0):
									$markupCommissionPercentage = $providerData['markup_commission'];
								else:
									$markupCommissionPercentage = $apiSettings['markup_commission_percentage'];
								endif;
								$MarkupCommissionAmount = ($sendValue * $markupCommissionPercentage) / 100;
								$totalAmount            = $sendValue + $MarkupCommissionAmount;
								$MarkupCommissionAmount = round((float)$MarkupCommissionAmount, 2);
								$totalAmount            = round((float)$totalAmount, 2);

								if(!empty($providerData['agent_commission']) && $providerData['agent_commission'] > 0):
									$commissionAmount = $totalAmount * $providerData['agent_commission'] / 100;
								else:
									$commissionAmount = $totalAmount * (int)$userData['ding_commission_percentage'] / 100;
								endif;
								$commissionAmount = round((float)$commissionAmount, 2);

								if($userData['availableReachargePoints'] < $totalAmount):
									throw new Exception(lang('INSUFFICIENT_BALANCE'), 1);
								endif;

								if($apiSettings['api_mode'] == 'live'):
									$ValidateOnly = false;
								elseif($apiSettings['api_mode'] == 'test' || empty($apiSettings['api_mode'])):
									$ValidateOnly = true;
								endif;


								$curl     = curl_init();
								$orderSeq = floor((microtime(true) * 1000)).rand(100,999);
								$DistributorRef = "INST".$orderSeq;
								$postData = array(
									"SkuCode"         => $skuCode,
									"SendValue"       => $sendValue,
									"AccountNumber"   => $accountNumber,
									"DistributorRef"  => $DistributorRef,
									"ValidateOnly"    => $ValidateOnly,
									"SendCurrencyIso" => $sendCurrencyIso
								);

								$postData = json_encode($postData);

								curl_setopt_array($curl, array(
								CURLOPT_URL => 'https://api.dingconnect.com/api/V1/SendTransfer',
								CURLOPT_RETURNTRANSFER => true,
								CURLOPT_ENCODING => '',
								CURLOPT_MAXREDIRS => 10,
								CURLOPT_TIMEOUT => 0,
								CURLOPT_FOLLOWLOCATION => true,
								CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
								CURLOPT_CUSTOMREQUEST => 'POST',
								CURLOPT_POSTFIELDS =>$postData,
								CURLOPT_HTTPHEADER => array(
									'Content-Type: application/json',
									'api_key: '.$apiKey,
									'Cookie: __cf_bm=DvzsbEbu4oWzC3Q_QfsoIGx3dZOcJDw6JECajGaqj5M-1774525070.6142814-1.0.1.1-Y7xNeSwDspUZ2CqQcBNb5TYYUh5SljxcIqMtgs5HZn.1WVMmyn4ucygMVO5NvZujuV.W.e.JtKCPC6Q53zo9sASvPqc9vWm7WemToHSIAwAZmF1aCQSr92c6ELuXeSeO'
								),
								));

								$response = curl_exec($curl);
								curl_close($curl);
								
								$responseArray = json_decode($response, true);
								if(!is_array($responseArray)):
									throw new Exception(lang('DING_RECHARGE_FAILED'), 1);
								endif;
								if(!empty($responseArray['ErrorCode']) || !empty($responseArray['errorCode'])):
									$errorMessage = !empty($responseArray['Message'])
										? $responseArray['Message']
										: (!empty($responseArray['message']) ? $responseArray['message'] : lang('DING_RECHARGE_FAILED'));
									throw new Exception($errorMessage, 1);
								endif;
								$processingState = $responseArray['TransferRecord']['ProcessingState'] ?? '';
								if($processingState !== 'Complete'):
									$errorMessage = !empty($responseArray['TransferRecord']['ReceiptText'])
										? $responseArray['TransferRecord']['ReceiptText']
										: lang('DING_RECHARGE_FAILED');
									throw new Exception($errorMessage, 1);
								endif;

								if($processingState === 'Complete'):

									$recahrgeParam['transaction_id']    = $responseArray['TransferRecord']['TransferId']['TransferRef'];
									$recahrgeParam['order_id']          = $responseArray['TransferRecord']['TransferId']['DistributorRef'];
									$recahrgeParam['users_id']          = (int)$usersId;
									$recahrgeParam['users_oid']         = new MongoDB\BSON\ObjectId($userData['_id']->{'$id'});
									$recahrgeParam['sku_code']          = $skuCode;
									$recahrgeParam['send_value']        = $sendValue;
									$recahrgeParam['provider_name']     = $providerData['Name'];
									$recahrgeParam['provider_logo']     = $providerData['LogoUrl'];
									$recahrgeParam['country_iso']       = $providerData['CountryIso'];
									$recahrgeParam['account_number']    = $accountNumber;
									$recahrgeParam['send_currency_iso'] = $sendCurrencyIso;
									$recahrgeParam['amount']            = $totalAmount;
									$recahrgeParam['markupcommission']  = $MarkupCommissionAmount;
									$recahrgeParam['created_at']        = strtotime(date('Y-m-d H:i:s'));
									$recahrgeParam['created_by']        = (int)$usersId;
									$recahrgeParam['status']            = 'A';
									$recahrgeParam['recharge_state']    = $responseArray['TransferRecord']['ProcessingState'];
									$recahrgeParam['response']          = $response;
									$rechrgeResponce  = $this->common_model->addData('ding_recharge_history', $recahrgeParam);
									
									
									$loadBalance['load_balance_id'] =  (int)$this->geneal_model->getNextSequence('loadBalance');
                                    $loadBalance['users_oid']        =  new MongoDB\BSON\ObjectId($userData['_id']->{'$id'});
                                    $loadBalance['request_oid']     =  new MongoDB\BSON\ObjectId($rechrgeResponce['_id']->{'$id'});
                                    $loadBalance['user_id_deb']     =  (int)$usersId;
                                    $loadBalance['user_id_cred']    =  (int)0;
									$loadBalance["availableArabianPoints"] =   (float)$userData['availableArabianPoints'];
									$loadBalance["end_balance"]              =   (float)$userData['availableArabianPoints'];
                                    $loadBalance["availableReachargePoints"] =   (float)$userData['availableReachargePoints'];
                                    $loadBalance["end_balance_recharge"]     =   (float)$userData['availableReachargePoints']-$totalAmount;
                                    $loadBalance['record_type']     = 'Debit';
                                    $loadBalance['narration']       = 'Ding Recharge';
                                    $loadBalance['remarks']         = "Added Recharge Amount: ".$totalAmount." ".$sendCurrencyIso. " to ".$accountNumber;
                                    $loadBalance['upoints']         = (float)$totalAmount;
                                    $loadBalance['creation_ip']     = $this->input->ip_address();;
                                    $loadBalance['created_at']      = strtotime(date('Y-m-d H:i:s'));
                                    $loadBalance['created_by']      = (int)$usersId;
                                    $loadBalance['status']          = 'A';
                                    $this->geneal_model->addData('loadBalance', $loadBalance);
									
									if($commissionAmount > 0):
										$commissionParam['load_balance_id'] =  (int)$this->geneal_model->getNextSequence('loadBalance');
                                        $commissionParam['users_oid']        =  new MongoDB\BSON\ObjectId($userData['_id']->{'$id'});
                                        $commissionParam['request_oid']     =  new MongoDB\BSON\ObjectId($rechrgeResponce['_id']->{'$id'});
                                        $commissionParam['user_id_deb']     =  (int)0;
                                        $commissionParam['user_id_cred']    =  (int)$usersId;
                                        $commissionParam["availableArabianPoints"]   =   (float)$userData['availableArabianPoints'];
                                        $commissionParam["end_balance"]              =   (float)$userData['availableArabianPoints'];
                                        $commissionParam["availableReachargePoints"] =   (float)$userData['availableReachargePoints']-$totalAmount;
                                        $commissionParam["end_balance_recharge"]     =   (float)($userData['availableReachargePoints']-$totalAmount)+$commissionAmount;
                                        $commissionParam['record_type']     = 'Credit';
                                        $commissionParam['narration']       = 'Ding Recharge Commission';
                                        $commissionParam['remarks']         = "Added Commission Amount: ".$commissionAmount." ".$sendCurrencyIso. " to ".$accountNumber;
                                        $commissionParam['upoints']         = (float)$commissionAmount;
                                        $commissionParam['creation_ip']     = $this->input->ip_address();;
                                        $commissionParam['created_at']      = strtotime(date('Y-m-d H:i:s'));
                                        $commissionParam['created_by']      = (int)$usersId;
                                        $commissionParam['status']          = 'A';
                                        $this->geneal_model->addData('loadBalance', $commissionParam);
									endif;

									$userParam['availableReachargePoints'] = (float)($userData['availableReachargePoints']-$totalAmount)+$commissionAmount;
									$userParam['update_date'] = date('Y-m-d H:i:s');
									$userParam['update_by']   = (int)$usersId;
									$userParam['status']      = 'A';
									$this->common_model->editData('uw_users', $userParam, '_id', new MongoDB\BSON\ObjectId($userData['_id']->{'$id'}));
									
									$result['rechargeResponse'] = $responseArray;
							        echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),(object)$result);
								endif;
							endif;
						endif;
					endif;
				endif;
			else:
				throw new Exception(lang('FORBIDDEN_MSG'),1);
			endif;
		} catch (Exception $e) {
			$errorMessage = trim((string)$e->getMessage());
			if($errorMessage === ''):
				$errorMessage = lang('TRY_AGAIN');
			endif;
			echo outPut(0,lang('SUCCESS_CODE'),$errorMessage,$result);	
		}
	}

	/* * *********************************************************************
	 * * Function name : rechargeHistory
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to rechargeHistory
	 * * Date 		   : 01 April 2026
	 * * **********************************************************************/	
	public function rechargeHistory()
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

						$tblName  = 'ding_recharge_history';
						$whereCon = array();
						$whereCon['where']['users_oid'] = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});

						if(!empty($searchBy) && !empty($searchValue)):
							$whereCon['where'][$searchBy] = is_numeric($searchValue) ? (int)$searchValue : $searchValue;
						endif;

						if(!empty($startDate) && !empty($endDate)):
							$whereCon['where']['created_at'] = array(
								'$gte' => strtotime($startDate),
								'$lte' => strtotime($endDate)
							);
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
						// $resultData = $this->common_model->getDingRechargeHistory($whereCon,$shortField,$itemsPerPage,$startIndex);
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
			$errorMessage = trim((string)$e->getMessage());
			if($errorMessage === ''):
				$errorMessage = lang('TRY_AGAIN');
			endif;
			echo outPut(0,lang('SUCCESS_CODE'),$errorMessage,$result);	
		}
	}

	/* * *********************************************************************
	 * * Function name : rechargeSummary
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to rechargeSummary
	 * * Date 		   : 01 April 2026
	 * * **********************************************************************/
	 
	public function rechargeSummary(){
		$apiHeaderData = getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 	   = array();
		try {
			if(requestAuthenticate(APIKEY,'POST')):
				$usersId   = $this->input->post('users_id');
				$startDate = $this->input->post('start_date');
				$endDate   = $this->input->post('end_date');
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

						$mainWhereCon = array();
						$mainWhereCon['where']['users_oid'] = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
						$mainWhereCon['where']['created_at'] = array(
							'$gte' => strtotime($startDate), 
							'$lte' => strtotime($endDate)
						);

						$orderSummary = $this->getRechargeSummary($mainWhereCon);

						$commission_amount = number_format($orderSummary['commission_amount'] -$orderSummary['commission_amount_cancelled'], 2);
						
						$result['sales_amount'] = (float)(($orderSummary['sales_amount']-$orderSummary['sales_amount_cancelled'])??0);
						$result['sales_amount_cancelled'] = (float)($orderSummary['sales_amount_cancelled']??0);
						$result['commission_amount'] = (float)($commission_amount??0);
						$result['commission_amount_cancelled'] = (float)($orderSummary['commission_amount_cancelled']??0);
						$result['redeemed_amount']   = (float)($orderSummary['redeemed_amount']??0);
						$result['due_amount'] = (float)(($result['sales_amount'] - $result['sales_amount_cancelled'] - $result['commission_amount']) ?? 0);

						$result['date_range'] = array(
							'start_date' => $startDate,
							'end_date' => $endDate
						);
						echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$result);
					endif;
				endif;
			else:
				throw new Exception(lang('FORBIDDEN_MSG'),1);
			endif;
		} catch (Exception $e) {
			$errorMessage = trim((string)$e->getMessage());
			if($errorMessage === ''):
				$errorMessage = lang('TRY_AGAIN');
			endif;
			echo outPut(0,lang('SUCCESS_CODE'),$errorMessage,$result);	
		}
	}
 
	private function getRechargeSummary($whereCon=array())
	{
		$result = array();
		$tblName = 'loadBalance';

	   if(empty($whereCon) || !isset($whereCon['where'])):
		return $result;
	   endif;

	   if (isset($whereCon['where'])):
		   $whereCondition = $whereCon['where'];
	   endif;

	    $SelectFields = array(
			'_id'                    => 1,
			'sales_amount'           => 1,
			'sales_amount_cancelled' => 1,
			'commission_amount' 	 => 1,
			'commission_amount_cancelled' => 1,
			'total_count'            => 1,
		);

	    $groupBy   = array(
			'_id' 		 => '$users_oid',
			'sales_amount'=> array(
				'$sum' => array(
					'$cond' => array(
						array(
							'$eq' => array('$narration', 'Ding Recharge'),
						),
						'$upoints',
						0
					)
				),
			),
			'sales_amount_cancelled'=> array(
				'$sum' => array(
					'$cond' => array(
						array(
							'$eq' => array('$narration', 'Ding Recharge Cancelled'),
						),
						'$upoints',
						0
					)
				),
			),
			'commission_amount'=> array(
				'$sum' => array(
					'$cond' => array(
						array(
							'$eq' => array('$narration', 'Ding Recharge Commission'),
						),
						'$upoints',
						0
					)
				),
			),

			'commission_amount_cancelled'=> array(
				'$sum' => array(
					'$cond' => array(
						array(
							'$eq' => array('$narration', 'Ding Recharge Commission Cancelled'),
						),
						'$upoints',
						0
					)
				),
			),
	    );

	   $sortBy    = array('_id' => -1);
	   $result = $this->common_model->getAggregateData(
		   $tblName,
		   $SelectFields,
		   $whereCondition,
		   $groupBy,
		   $sortBy,
		   $lookup,
		   $unwind,
		   $resultType,
		   $startIndex,
		   $itemsPerPage
	   );
	   
	   return $result[0];

	}

}

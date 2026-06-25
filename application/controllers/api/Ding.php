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
private function dingMoneyField($node, $field)
	{
		if(is_object($node)):
			return isset($node->{$field}) ? $node->{$field} : '';
		endif;
		if(is_array($node)):
			return isset($node[$field]) ? $node[$field] : '';
		endif;
		return '';
	}

		private function dingExtractFailureMessage($responseArray)
	{
		if(!is_array($responseArray)):
			return lang('DING_RECHARGE_FAILED');
		endif;

		$dingErrorCode = '';
		$dingContext   = '';
		if(!empty($responseArray['ErrorCodes']) && is_array($responseArray['ErrorCodes'])):
			foreach($responseArray['ErrorCodes'] as $dingError):
				if($dingErrorCode === '' && !empty($dingError['Code'])):
					$dingErrorCode = (string)$dingError['Code'];
				endif;
				if($dingContext === '' && !empty($dingError['Context'])):
					$dingContext = (string)$dingError['Context'];
				endif;
			endforeach;
		endif;

		$rawMessage = '';
		if(!empty($responseArray['Message'])):
			$rawMessage = (string)$responseArray['Message'];
		elseif(!empty($responseArray['message'])):
			$rawMessage = (string)$responseArray['message'];
		endif;

		if($this->dingIsSendValueError($dingErrorCode, $dingContext, $rawMessage)):
			return 'Recharge could not be completed for this mobile number and plan. Please use getaccount API and select a plan that matches the mobile operator.';
		endif;
		if($dingErrorCode === 'AccountNumberFailedRegex' || $rawMessage === 'AccountNumberFailedRegex'):
			return 'Mobile number format is invalid for this operator/plan. Use getaccount API first and recharge only with plans returned for that number.';
		endif;
		if($dingErrorCode === 'ProductUnavailable'):
			return 'This plan is not available on Ding right now. Sync products in admin or choose another plan.';
		endif;
		if($dingErrorCode === 'RechargeNotAllowed'):
			return 'This plan cannot be used for the given mobile number. Use getaccount API and match operator to plan (e.g. RJIN=Jio, BLIN=BSNL).';
		endif;
		if($dingContext !== '' && !in_array($dingContext, array('SendValue', 'AccountNumber'), true)):
			return $dingContext;
		endif;
		if($rawMessage !== '' && !in_array($rawMessage, array('SendValue', 'AccountNumber'), true)):
			return $rawMessage;
		endif;
		if($dingErrorCode !== '' && !in_array($dingErrorCode, array('SendValue', 'ParameterInvalid', 'ParameterOutOfRange'), true)):
			return $dingErrorCode;
		endif;

		$processingState = $responseArray['TransferRecord']['ProcessingState'] ?? '';
		if(!empty($responseArray['TransferRecord']['ReceiptText'])):
			return (string)$responseArray['TransferRecord']['ReceiptText'];
		endif;
		if($processingState !== '' && $processingState !== 'Complete'):
			return 'Ding transfer state: '.$processingState;
		endif;
		return lang('DING_RECHARGE_FAILED');
	}

	private function dingIsSendValueError($dingErrorCode, $dingContext, $rawMessage)
	{
		if($dingErrorCode === 'SendValue' || $rawMessage === 'SendValue'):
			return true;
		endif;
		if(stripos($dingContext, 'SendValue') !== false):
			return true;
		endif;
		if(in_array($dingErrorCode, array('ParameterInvalid', 'ParameterOutOfRange'), true) && stripos($dingContext, 'SendValue') !== false):
			return true;
		endif;
		return false;
	}

	private function dingValidateAccountNumber($accountNumber, $providerData, $productData)
	{
		$regex = '';
		if(!empty($providerData['ValidationRegex'])):
			$regex = trim((string)$providerData['ValidationRegex']);
		endif;
		if($regex === ''):
			return;
		endif;
		$accountNumber = (string)$accountNumber;
		$matched = @preg_match('/'.$regex.'/', $accountNumber);
		if($matched === false):
			$matched = @preg_match('#'.$regex.'#', $accountNumber);
		endif;
		if($matched === 0):
			$providerName = !empty($providerData['Name']) ? (string)$providerData['Name'] : 'selected operator';
			throw new Exception('Mobile number is not valid for '.$providerName.'. Use getaccount API and recharge with a matching plan for this number.', 1);
		endif;
	}

	private function dingIsTransferSuccess($responseArray, $validateOnly = false)
	{
		if(!is_array($responseArray) || empty($responseArray['TransferRecord'])):
			return false;
		endif;
		$resultCode = isset($responseArray['ResultCode']) ? (int)$responseArray['ResultCode'] : 0;
		if($resultCode !== 1):
			return false;
		endif;
		if(!empty($responseArray['ErrorCode']) || !empty($responseArray['errorCode'])):
			return false;
		endif;
		if(!empty($responseArray['ErrorCodes']) && is_array($responseArray['ErrorCodes'])):
			foreach($responseArray['ErrorCodes'] as $dingError):
				if(!empty($dingError['Code'])):
					return false;
				endif;
			endforeach;
		endif;
		$processingState = $responseArray['TransferRecord']['ProcessingState'] ?? '';
		$failedStates = array('Failed', 'Cancelled', 'Cancelling');
		if(in_array($processingState, $failedStates, true)):
			return false;
		endif;
		if($processingState === 'Complete'):
			return true;
		endif;
		if($validateOnly === true):
			return true;
		endif;
		return false;
	}

	private function customRound($value)
	{
		$value = (float) $value;
		if ($value <= 0) {
			return 0.0;
		}

		$base = (int) $value;
		$decimal = $value - $base;

		if ($decimal == 0) {
			return (float) $base;
		}
		if ($decimal <= 0.25) {
			return $base + 0.25;
		}
		if ($decimal <= 0.50) {
			return $base + 0.50;
		}
		if ($decimal <= 0.75) {
			return $base + 0.75;
		}
		return $base + 1.00;
	}

	private function dingCalculateCommission($totalAmount, $commissionPercent)
	{
		if ((float) $commissionPercent <= 0) {
			return 0.0;
		}
		$amountMillis = (int) floor(((float) $totalAmount * 1000) + 0.00001);
		$commissionMillis = (int) floor((($amountMillis * (float) $commissionPercent) / 100) + 0.00001);
		return round($commissionMillis / 1000, 3);
	}

	private function dingStoreMoney($amount, $decimals = 2)
	{
		return round((float) $amount, (int) $decimals);
	}

	private function dingFormatCommission($amount)
	{
		return number_format($this->dingStoreMoney($amount, 3), 3, '.', '');
	}

	private function dingIsCommissionNarration($narration)
	{
		$narration = (string) $narration;
		return (strpos($narration, 'Ding Recharge Commission') !== false);
	}

	private function dingHistoryOidString($idField)
	{
		if (empty($idField)) {
			return '';
		}
		$idNorm = json_decode(json_encode($idField), true);
		if (!is_array($idNorm)) {
			return '';
		}
		if (isset($idNorm['$id'])) {
			return (string) $idNorm['$id'];
		}
		if (isset($idNorm['$oid'])) {
			return (string) $idNorm['$oid'];
		}
		return '';
	}

	private function dingHistoryIsCancelled($row)
	{
		$state = isset($row['recharge_state']) ? (string) $row['recharge_state'] : '';
		return ($state === 'Cancelled' || !empty($row['cancelled_at']));
	}

	private function dingFetchCommissionMapByHistoryOids($historyOids, $narration)
	{
		$commissionMap = array();
		if (empty($historyOids)) {
			return $commissionMap;
		}
		$oidObjects = array();
		foreach ($historyOids as $oidStr) {
			try {
				$oidObjects[] = new MongoDB\BSON\ObjectId($oidStr);
			} catch (Exception $e) {
				// ignore invalid ids
			}
		}
		if (empty($oidObjects)) {
			return $commissionMap;
		}
		$whereLb = array(
			'where' => array(
				'narration' => $narration,
				'status'    => 'A',
			),
			'where_in' => array('request_oid', $oidObjects),
		);
		$lbRows = $this->common_model->getData('multiple', 'uw_loadBalance', $whereLb);
		if (empty($lbRows) || !is_array($lbRows)) {
			return $commissionMap;
		}
		foreach ($lbRows as $lb) {
			$x = is_object($lb) ? json_decode(json_encode($lb), true) : $lb;
			if (!is_array($x) || empty($x['request_oid'])) {
				continue;
			}
			$ridStr = $this->dingHistoryOidString($x['request_oid']);
			if ($ridStr === '') {
				continue;
			}
			if (!isset($commissionMap[$ridStr])) {
				$commissionMap[$ridStr] = (float) ($x['upoints'] ?? 0);
			}
		}
		return $commissionMap;
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
			echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);	
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
						curl_close($curl);

						$countryIso = isset($responseArray['CountryIso']) ? (string)$responseArray['CountryIso'] : '';
						$providerCodes = array();
						if(!empty($responseArray['Items']) && is_array($responseArray['Items'])):
							foreach($responseArray['Items'] as $lookupItem):
								$code = isset($lookupItem['ProviderCode']) ? trim((string)$lookupItem['ProviderCode']) : '';
								if($code !== '' && !in_array($code, $providerCodes, true)):
									$providerCodes[] = $code;
								endif;
							endforeach;
						endif;

						$tblName      = 'ding_provider_list';
						$whereProviderCon['where']['CountryIso']   = $countryIso;
					    //   $whereProviderCon['where']['ProviderCode'] = $providerCode;
						$whereProviderCon['where']['status']       = 'A';
						$whereProviderCon['where']['PaymentTypes'] = 'Prepaid';
						$whereProviderCon['select'] = array('Name','ValidationRegex','Name','LogoUrl','markup_commission','ProviderCode');
						$providerList = $this->common_model->getData('multiple',$tblName,$whereProviderCon);

						$planList = array();
						if(!empty($providerCodes)):
							$tblName = 'ding_products_list';
							$whereProductCon['where']['status'] = 'A';
							$whereProductCon['where_in'] = array('ProviderCode', $providerCodes);
							$planList = $this->common_model->getData('multiple',$tblName,$whereProductCon);
						endif;
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
			echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);	
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
			echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);	
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

							if(empty($productData)):
								throw new Exception(lang('INVALID_SKU_CODE'), 1);
							else:
								$providerData = $this->common_model->getData('single','ding_provider_list',array('where'=>array('ProviderCode'=>$productData['ProviderCode'],'status'=>'A')));
								if(empty($providerData)):
									throw new Exception(lang('INVALID_SKU_CODE'), 1);
								endif;

								$maximum = $productData['Maximum'] ?? null;
								$minimum = $productData['Minimum'] ?? null;
								if(empty($minimum) && !empty($maximum)):
									$minimum = $maximum;
								endif;
								$maxSendValue = round((float)$this->dingMoneyField($maximum, 'SendValue'), 2);
								$minSendValue = round((float)$this->dingMoneyField($minimum, 'SendValue'), 2);
								if(empty($sendCurrencyIso)):
									$sendCurrencyIso = (string)$this->dingMoneyField($maximum, 'SendCurrencyIso');
									if($sendCurrencyIso === ''):
										$sendCurrencyIso = (string)$this->dingMoneyField($minimum, 'SendCurrencyIso');
									endif;
								endif;
								$sendValue = round((float)$sendValue, 2);
								$planSendValue = $maxSendValue > 0 ? $maxSendValue : $minSendValue;
								if($planSendValue <= 0 || $sendValue != $planSendValue):
									throw new Exception('Invalid send value for selected plan. Use SendValue '.$planSendValue.' '.$sendCurrencyIso.' for sku '.$skuCode.'.', 1);
								endif;
								$sendValue = $planSendValue;

								$this->dingValidateAccountNumber($accountNumber, $providerData, $productData);

								if(true):

									// Get API Settings
									$tblName = 'ding_settings';
									$whereCon1['where']['status'] = 'A';
									$whereCon1['select'] = array( 'api_key','api_mode','markup_commission_percentage');
									$apiSettings = $this->common_model->getData('single',$tblName,$whereCon1);
									$apiKey                     = $apiSettings['api_key'];
									
									if(!empty($providerData['markup_commission']) && $providerData['markup_commission'] > 0):
										$markupCommissionPercentage = $providerData['markup_commission'];
									else:
										$markupCommissionPercentage = 0;
										// $markupCommissionPercentage = $apiSettings['markup_commission_percentage'];
									endif;
									$MarkupCommissionAmount = ($sendValue * $markupCommissionPercentage) / 100;
									$totalAmount            = $sendValue + $MarkupCommissionAmount;
									if((float)$markupCommissionPercentage > 0):
										$totalAmount = $this->customRound($totalAmount);
									else:
										$totalAmount = round((float)$sendValue, 2);
									endif;
									$MarkupCommissionAmount = round($totalAmount - $sendValue, 2);

									$userFields = json_decode(json_encode($userData), true);
									$hasUserDingPct = array_key_exists('ding_commission_percentage', $userFields)
										&& $userFields['ding_commission_percentage'] !== ''
										&& $userFields['ding_commission_percentage'] !== null;
									// if(!empty($providerData['markup_commission']) && (float)$providerData['markup_commission'] > 0):
									// 	$commissionPercent = (float)$providerData['markup_commission'];
									// elseif($hasUserDingPct):
									// 	$commissionPercent = (float)$userFields['ding_commission_percentage'];
									// else:
									// 	$commissionPercent = (float)($apiSettings['markup_commission_percentage'] ?? 0);
									// endif;
									if(!empty($providerData['agent_commission']) && (float)$providerData['agent_commission'] > 0):
										$commissionPercent = (float)$providerData['agent_commission'];
									elseif($hasUserDingPct):
										$commissionPercent = (float)$userFields['ding_commission_percentage'];
									else:
										$commissionPercent = (float)($userFields['commission_percentage'] ?? 0);
									endif;
									$totalAmount = $this->dingStoreMoney($totalAmount, 2);
									$commissionAmount = $this->dingCalculateCommission($totalAmount, $commissionPercent);
									$commissionDisplay = $this->dingFormatCommission($commissionAmount);
									$openingRechargeBalance = $this->dingStoreMoney($userData['availableReachargePoints'], 2);
									$afterDebitBalance = $this->dingStoreMoney($openingRechargeBalance - $totalAmount, 2);
									$afterCommissionBalance = $this->dingStoreMoney($afterDebitBalance + $commissionAmount, 2);


									if($openingRechargeBalance < $totalAmount):
										throw new Exception(lang('INSUFFICIENT_BALANCE'), 1);
									endif;

									if($apiSettings['api_mode'] == 'live'):
										$ValidateOnly = false;
									elseif($apiSettings['api_mode'] == 'test' || empty($apiSettings['api_mode'])):
										$ValidateOnly = true;
									endif;


									$curl     = curl_init();
									$orderSeq = str_pad((string)mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT);
									$DistributorRef = 'UW'.$orderSeq;
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
									$this->generatelogs->putLog('APP', 'Ding SendTransfer response: '.$response);
									
									$responseArray = json_decode($response, true);
									if(!is_array($responseArray)):
										throw new Exception(lang('DING_RECHARGE_FAILED'), 1);
									endif;
									if(!$this->dingIsTransferSuccess($responseArray, $ValidateOnly === true)):
										$result['dingResponse'] = $responseArray;
										throw new Exception($this->dingExtractFailureMessage($responseArray), 1);
									endif;

									$processingState = $responseArray['TransferRecord']['ProcessingState'] ?? '';
									if($processingState === '' && $ValidateOnly === true):
										$processingState = 'Validated';
									endif;

									if(true):

										$transferRef = $responseArray['TransferRecord']['TransferId']['TransferRef'] ?? '';
										if($transferRef === '' || $transferRef === '0'):
											$transferRef = 'VAL-'.$DistributorRef;
										endif;
										$recahrgeParam['transaction_id']    = $transferRef;
										$recahrgeParam['order_id']          = $responseArray['TransferRecord']['TransferId']['DistributorRef'] ?? $DistributorRef;
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
										$recahrgeParam['commission_amount'] = $commissionAmount;
										$recahrgeParam['commission_percent'] = $commissionPercent;
										$recahrgeParam['created_at']        = strtotime(date('Y-m-d H:i:s'));
										$recahrgeParam['created_by']        = (int)$usersId;
										$recahrgeParam['status']            = 'A';
										$recahrgeParam['recharge_state']    = $processingState;
										$recahrgeParam['response']          = $response;
										$rechrgeResponce  = $this->common_model->addData('ding_recharge_history', $recahrgeParam);
										
										
										$loadBalance['load_balance_id'] =  (int)$this->geneal_model->getNextSequence('uw_loadBalance');
										$loadBalance['users_oid']        =  new MongoDB\BSON\ObjectId($userData['_id']->{'$id'});
										$loadBalance['request_oid']     =  new MongoDB\BSON\ObjectId($rechrgeResponce['_id']->{'$id'});
										$loadBalance['user_id_deb']     =  (int)$usersId;
										$loadBalance['user_id_cred']    =  (int)0;
										$loadBalance["availableArabianPoints"] =   (float)$userData['availableArabianPoints'];
										$loadBalance["end_balance"]              =   (float)$userData['availableArabianPoints'];
										$loadBalance["availableReachargePoints"] =   $openingRechargeBalance;
										$loadBalance["end_balance_recharge"]     =   $afterDebitBalance;
										$loadBalance['record_type']     = 'Debit';
										$loadBalance['narration']       = 'Ding Recharge';
										$loadBalance['remarks']         = "Added Recharge Amount: ".$totalAmount." ".$sendCurrencyIso. " to ".$accountNumber;
										$loadBalance['upoints']         = $totalAmount;
										$loadBalance['creation_ip']     = $this->input->ip_address();;
										$loadBalance['created_at']      = strtotime(date('Y-m-d H:i:s'));
										$loadBalance['created_by']      = (int)$usersId;
										$loadBalance['status']          = 'A';
										$this->geneal_model->addData('uw_loadBalance', $loadBalance);
										
										if($commissionAmount > 0):
											$commissionParam['load_balance_id'] =  (int)$this->geneal_model->getNextSequence('uw_loadBalance');
											$commissionParam['users_oid']        =  new MongoDB\BSON\ObjectId($userData['_id']->{'$id'});
											$commissionParam['request_oid']     =  new MongoDB\BSON\ObjectId($rechrgeResponce['_id']->{'$id'});
											$commissionParam['user_id_deb']     =  (int)0;
											$commissionParam['user_id_cred']    =  (int)$usersId;
											$commissionParam["availableArabianPoints"]   =   (float)$userData['availableArabianPoints'];
											$commissionParam["end_balance"]              =   (float)$userData['availableArabianPoints'];
											$commissionParam["availableReachargePoints"] =   $afterDebitBalance;
											$commissionParam["end_balance_recharge"]     =   $afterCommissionBalance;
											$commissionParam['record_type']     = 'Credit';
											$commissionParam['narration']       = 'Ding Recharge Commission';
											$commissionParam['remarks']         = "Added Commission Amount: ".$commissionDisplay." ".$sendCurrencyIso. " to ".$accountNumber;
											$commissionParam['upoints']         = $commissionAmount;
											$commissionParam['creation_ip']     = $this->input->ip_address();;
											$commissionParam['created_at']      = strtotime(date('Y-m-d H:i:s'));
											$commissionParam['created_by']      = (int)$usersId;
											$commissionParam['status']          = 'A';
											$this->geneal_model->addData('uw_loadBalance', $commissionParam);
										endif;

										$userParam['availableReachargePoints'] = $afterCommissionBalance;
										$userParam['update_date'] = date('Y-m-d H:i:s');
										$userParam['update_by']   = (int)$usersId;
										$userParam['status']      = 'A';
										$this->common_model->editData('uw_users', $userParam, '_id', new MongoDB\BSON\ObjectId($userData['_id']->{'$id'}));
										
										$result['rechargeResponse'] = $responseArray;
										$result['selling_amount'] = $totalAmount;
										$result['commission_percent'] = $commissionPercent;
										$result['commission_amount'] = $commissionAmount;
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
				echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);	
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
							$userFields = json_decode(json_encode($userData), true);
							$defaultDingPct = (float)($userFields['ding_commission_percentage'] ?? 0);
							$pageData = array();
							foreach($resultData as $row):
								$r = is_object($row) ? json_decode(json_encode($row), true) : $row;
								if(!is_array($r)):
									continue;
								endif;
								$sellingAmount = round((float)($r['amount'] ?? 0), 2);
								$commissionAmount = $this->dingFormatApiCommission($r['commission_amount'] ?? 0);
								$commissionPercent = round((float)($r['commission_percent'] ?? 0), 2);
								if($commissionPercent <= 0 && $sellingAmount > 0 && $commissionAmount > 0):
									$commissionPercent = round(($commissionAmount / $sellingAmount) * 100, 2);
								elseif($commissionPercent <= 0 && $defaultDingPct > 0):
									$commissionPercent = $defaultDingPct;
								endif;
								$r['selling_amount'] = $sellingAmount;
								$r['commission_amount'] = $commissionAmount;
								$r['commission_percent'] = $commissionPercent;
								$pageData[] = $r;
							endforeach;
							$result['total_count']  = $totalcount;
							$result['current_page'] = $page;
							$result['total_pages']  = ceil($totalcount / $itemsPerPage);
							$result['PageData']     = $pageData;
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
						$mainWhereCon['where']['users_id'] = (int) $usersId;
						$mainWhereCon['where']['created_at'] = array(
							'$gte' => strtotime($startDate), 
							'$lte' => strtotime($endDate)
						);

						$orderSummary = $this->getRechargeSummary($mainWhereCon);

						$salesAmount = (float) ($orderSummary['sales_amount'] ?? 0);
						$salesCancelled = (float) ($orderSummary['sales_amount_cancelled'] ?? 0);
						$commissionAmount = (float) ($orderSummary['commission_amount'] ?? 0);
						$commissionCancelled = (float) ($orderSummary['commission_amount_cancelled'] ?? 0);

						$result['sales_amount'] = round($salesAmount, 2);
						$result['sales_amount_cancelled'] = round($salesCancelled, 2);
						$result['commission_amount'] = (float) ($orderSummary['commission_amount'] ?? 0);
						$result['commission_amount_cancelled'] = (float) ($orderSummary['commission_amount_cancelled'] ?? 0);
						$result['redeemed_amount'] = round((float) ($orderSummary['redeemed_amount'] ?? 0), 2);
						$result['due_amount'] = round($result['sales_amount'] - $result['commission_amount'], 2);

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
			echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);	
		}
	}
 
	private function getRechargeSummary($whereCon=array())
	{
		$result = array(
			'sales_amount'                => 0.0,
			'sales_amount_cancelled'      => 0.0,
			'commission_amount'           => 0.0,
			'commission_amount_cancelled' => 0.0,
			'redeemed_amount'             => 0.0,
		);

		if (empty($whereCon) || empty($whereCon['where']) || !is_array($whereCon['where'])) {
			return $result;
		}

		$whereHistory = array('where' => $whereCon['where']);
		if (!isset($whereHistory['where']['status'])) {
			$whereHistory['where']['status'] = 'A';
		}
		$historyRows = $this->common_model->getData('multiple', 'ding_recharge_history', $whereHistory, array('created_at' => -1));
		if (empty($historyRows) || !is_array($historyRows)) {
			return $result;
		}

		$activeOids = array();
		$cancelledOids = array();
		$commissionCents = 0;
		$commissionCancelledCents = 0;
		foreach ($historyRows as $row) {
			$r = is_object($row) ? json_decode(json_encode($row), true) : $row;
			if (!is_array($r)) {
				continue;
			}
			$oidStr = $this->dingHistoryOidString($r['_id'] ?? null);
			$amount = round((float) ($r['amount'] ?? 0), 2);
			$rowCommissionCents = $this->dingFormatApiCommissionCents($r['commission_amount'] ?? 0);
			if ($this->dingHistoryIsCancelled($r)) {
				$result['sales_amount_cancelled'] += $amount;
				if ($rowCommissionCents > 0) {
					$commissionCancelledCents += $rowCommissionCents;
				} elseif ($oidStr !== '') {
					$cancelledOids[] = $oidStr;
				}
			} else {
				$result['sales_amount'] += $amount;
				if ($rowCommissionCents > 0) {
					$commissionCents += $rowCommissionCents;
				} elseif ($oidStr !== '') {
					$activeOids[] = $oidStr;
				}
			}
		}

		$activeCommissionMap = $this->dingFetchCommissionMapByHistoryOids($activeOids, 'Ding Recharge Commission');
		foreach ($activeOids as $oidStr) {
			if (!isset($activeCommissionMap[$oidStr])) {
				continue;
			}
			$commissionCents += $this->dingFormatApiCommissionCents($activeCommissionMap[$oidStr]);
		}

		$cancelledCommissionMap = $this->dingFetchCommissionMapByHistoryOids($cancelledOids, 'Ding Recharge Commission Cancelled');
		$cancelledOriginalCommissionMap = $this->dingFetchCommissionMapByHistoryOids($cancelledOids, 'Ding Recharge Commission');
		foreach ($cancelledOids as $oidStr) {
			if (isset($cancelledCommissionMap[$oidStr])) {
				$commissionCancelledCents += $this->dingFormatApiCommissionCents($cancelledCommissionMap[$oidStr]);
			} elseif (isset($cancelledOriginalCommissionMap[$oidStr])) {
				$commissionCancelledCents += $this->dingFormatApiCommissionCents($cancelledOriginalCommissionMap[$oidStr]);
			}
		}

		$result['sales_amount'] = round($result['sales_amount'], 2);
		$result['sales_amount_cancelled'] = round($result['sales_amount_cancelled'], 2);
		$result['commission_amount'] = $commissionCents / 100;
		$result['commission_amount_cancelled'] = $commissionCancelledCents / 100;

		return $result;
	}
public function setEnableDing()
	{
		$this->generatelogs->putLog('APP', logOutPut($_POST));
		$result = array();

		try {
			if (!requestAuthenticate(APIKEY, 'POST')):
				throw new Exception(lang('FORBIDDEN_MSG'), 1);
			endif;

			$usersId = $this->input->post('users_id');
			$enableRaw = $this->input->post('enable_ding');
			if ($enableRaw === null || $enableRaw === ''):
				$enableRaw = $this->input->post('ding_enabled');
			endif;

			if (empty($usersId)):
				throw new Exception(lang('USER_ID_EMPTY'), 1);
			endif;
			if ($enableRaw === null || $enableRaw === ''):
				throw new Exception(lang('EMPTY_ENABLE_DING_FLAG'), 1);
			endif;

			$enableRaw = strtoupper(trim($enableRaw));
			if ($enableRaw == 'Y'):
				$flag = 'Y';
			elseif ($enableRaw == 'N'):
				$flag = 'N';
			else:
				throw new Exception(lang('INVALID_ENABLE_DING_FLAG'), 1);
			endif;

			$tblName = 'uw_users';
			$whereCon['where']['users_id'] = (int) $usersId;
			$userData = $this->common_model->getData('single', $tblName, $whereCon);
			if (empty($userData)):
				throw new Exception(lang('INVALID_USER'), 1);
			endif;

			$param = array('enable_ding' => $flag);
			$mongoId = new MongoDB\BSON\ObjectId($userData['_id']->{'$id'});
           $this->common_model->editData('uw_users', $param, '_id', $mongoId);

			$result['users_id'] = (int) $usersId;
			$result['enable_ding'] = $flag;
			echo outPut(1, lang('SUCCESS_CODE'), lang('ENABLE_DING_UPDATED'), $result);
		} catch (Exception $e) {
			echo outPut(0, lang('SUCCESS_CODE'), $e->getMessage(), $result);
		}
	}

	private function dingFormatApiCommissionCents($amount)
	{
		$millis = (int) floor(((float) $amount * 1000) + 0.00001);
		return (int) round($millis / 10, 0, PHP_ROUND_HALF_UP);
	}

	/** Convert stored 3-decimal commission to 2-decimal for API display. */
	private function dingFormatApiCommission($amount)
	{
		return $this->dingFormatApiCommissionCents($amount) / 100;
	}
}


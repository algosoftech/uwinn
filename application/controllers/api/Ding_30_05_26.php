<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ding extends CI_Controller {

	var $postdata;
	var $user_agent;
	var $request_url;
	var $method_name;

	public function __construct()
	{
		parent::__construct();
		error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT);
		$this->lang->load('statictext', 'api');
		$this->load->helper('apidata');
		$this->load->model(array('geneal_model', 'common_model'));

		$this->user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
		$this->request_url = $_SERVER['REDIRECT_URL'] ?? ($_SERVER['REQUEST_URI'] ?? '');
		$this->method_name = $_SERVER['REDIRECT_QUERY_STRING'] ?? '';

		$this->load->library('generatelogs', array('type' => 'common'));
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
		$this->generatelogs->putLog('APP', logOutPut($_POST));
		$result = array();

		try {
			if(requestAuthenticate(APIKEY, 'POST')):
				$usersId = $this->input->post('users_id');
				$accountno = $this->input->post('accountno');
				if(empty($usersId)):
					throw new Exception(lang('USER_ID_EMPTY'), 1);
				elseif(empty($accountno)):
					throw new Exception(lang('ACCOUNTNO_EMPTY'), 1);
				else:
					$tblName = 'uw_users';
					$whereCon['where']['users_id'] = (int)$usersId;
					$userData = $this->common_model->getData('single', $tblName, $whereCon);
					if(empty($userData)):
						throw new Exception(lang('INVALID_USER'), 1);
					elseif($userData['status'] != 'A'):
						throw new Exception(lang('ACCOUNT_BLOCKED'), 1);
					else:

						$tblName = 'ding_settings';
						$whereCon1['where']['status'] = 'A';
						$whereCon1['select'] = array('api_key');
						$apiSettings = $this->common_model->getData('single', $tblName, $whereCon1);
						if(empty($apiSettings) || empty($apiSettings['api_key'])):
							throw new Exception(lang('DATA_NOT_FOUND'), 1);
						endif;
						$apiKey = $apiSettings['api_key'];

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

						$response = curl_exec($curl);
						$responseArray = json_decode($response, true);
						curl_close($curl);

						if(empty($responseArray['CountryIso']) || empty($responseArray['Items'][0]['ProviderCode'])):
							$apiMsg = !empty($responseArray['Message']) ? $responseArray['Message'] : lang('DATA_NOT_FOUND');
							throw new Exception($apiMsg, 1);
						endif;

						$countryIso = $responseArray['CountryIso'];
						$providerCode = $responseArray['Items'][0]['ProviderCode'];

						$tblName = 'ding_provider_list';
						$whereProviderCon['where']['CountryIso'] = $countryIso;
						$whereProviderCon['where']['status'] = 'A';
						$whereProviderCon['where']['PaymentTypes'] = 'Prepaid';
						$whereProviderCon['select'] = array('Name', 'ValidationRegex', 'Name', 'LogoUrl', 'markup_commission', 'ProviderCode');
						$providerList = $this->common_model->getData('multiple', $tblName, $whereProviderCon);

						$tblName = 'ding_products_list';
						$whereProductCon['where']['ProviderCode'] = $providerCode;
						$whereProductCon['where']['status'] = 'A';
						$planList = $this->common_model->getData('multiple', $tblName, $whereProductCon);
						if(!empty($planList)):
							foreach($planList as $key => $plan):
								$LocalizationKey = $plan['LocalizationKey'];
								$tblName = 'ding_products_description_list';
								$whereProductDetailCon['where']['LocalizationKey'] = $LocalizationKey;
								$whereProductDetailCon['where']['status'] = 'A';
								$planDetail = $this->common_model->getData('single', $tblName, $whereProductDetailCon);
								$planList[$key]['planDetail'] = $planDetail;
							endforeach;
						endif;

						$resultData['accountinfo'] = $responseArray;
						$resultData['providerList'] = $providerList;
						$resultData['planList'] = $planList;
						if(empty($resultData)):
							throw new Exception(lang('DATA_NOT_FOUND'), 1);
						else:
							(object) $result['list'] = $resultData;
							echo outPut(1, lang('SUCCESS_CODE'), lang('SUCCESS_ACTION'), $result);
						endif;
					endif;
				endif;
			else:
				throw new Exception(lang('FORBIDDEN_MSG'), 1);
			endif;
		} catch (Exception $e) {
			echo outPut(0, lang('SUCCESS_CODE'), $e->getMessage(), $result);
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
}

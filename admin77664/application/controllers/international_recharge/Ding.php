<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ding extends CI_Controller {

	public function  __construct() 
	{ 
		parent:: __construct();
		error_reporting(E_ALL ^ E_NOTICE);  
		$this->load->model(array('admin_model','emailtemplate_model','sms_model','notification_model','geneal_model'));
		$this->lang->load('statictext', 'admin');
		$this->load->helper('common');
	}

	private function dingSettingsRedirectError($message)
	{
		$this->session->set_flashdata('alert_error', $message);
		redirect(correctLink('DINGRECHARGEDATA', getCurrentControllerPath('settings')));
		exit;
	}

	/**
	 * Round Ding money to nearest 0.05 AED slab (e.g. 2.825 => 2.85).
	 */
	private function _ding_round_amount($amount)
	{
		return round((float) $amount * 20, 0, PHP_ROUND_HALF_UP) / 20;
	}

	private function _ding_format_display_amount($amount)
	{
		return number_format($this->_ding_round_amount($amount), 2, '.', '');
	}

	/**
	 * Normalize Mongo / BSON money fields to float (never use markupcommission for display).
	 */
	private function _ding_money_value($value)
	{
		if ($value === null || $value === '') {
			return 0.0;
		}
		if (is_object($value)) {
			if ($value instanceof MongoDB\BSON\Decimal128) {
				return (float) (string) $value;
			}
			if (method_exists($value, '__toString')) {
				return (float) (string) $value;
			}
		}
		if (is_array($value)) {
			if (isset($value['$numberDecimal'])) {
				return (float) $value['$numberDecimal'];
			}
			if (isset($value['$numberDouble'])) {
				return (float) $value['$numberDouble'];
			}
		}
		return (float) $value;
	}

	private function _ding_apply_display_amounts(&$row)
	{
		if (!is_array($row)) {
			return;
		}
		if (isset($row['amount']) && $row['amount'] !== '') {
			$row['display_amount'] = $this->_ding_format_display_amount($this->_ding_money_value($row['amount']));
		}
		$agentCommission = isset($row['commission_amount']) ? $this->_ding_money_value($row['commission_amount']) : 0.0;
		$row['commission_amount'] = $agentCommission;
		$row['display_commission_amount'] = number_format($agentCommission, 3, '.', '');
	}

	private function dingExtractApiErrorMessage($response)
	{
		if(!is_array($response)):
			return 'Invalid response from Ding Connect API.';
		endif;
		if(!empty($response['Message'])):
			return (string)$response['Message'];
		endif;
		if(!empty($response['ErrorMessage'])):
			return (string)$response['ErrorMessage'];
		endif;
		if(!empty($response['ErrorCode'])):
			return 'Ding API error: '.$response['ErrorCode'];
		endif;
		return 'No data returned from Ding API. Save a valid API key and check sandbox/live mode.';
	}

	private function dingBuildSyncParamFromItems($response, $listId, $extraForNew = array())
	{
		$param = array();
		if(empty($response['Items']) || !is_array($response['Items'])):
			return array('param' => $param, 'error' => $this->dingExtractApiErrorMessage($response));
		endif;
		foreach($response['Items'] as $key => $item):
			$param[$key] = $item;
			if(empty($listId)):
				$param[$key]['status'] = 'A';
				$param[$key]['creation_ip'] = currentIp();
				$param[$key]['creation_date'] = (int)$this->timezone->utc_time();
				$param[$key]['created_by'] = (int)$this->session->userdata('ADMIN_ID');
				foreach($extraForNew as $field => $value):
					$param[$key][$field] = $value;
				endforeach;
			else:
				$param[$key]['update_ip'] = currentIp();
				$param[$key]['update_date'] = (int)$this->timezone->utc_time();
				$param[$key]['updated_by'] = (int)$this->session->userdata('ADMIN_ID');
			endif;
		endforeach;
		return array('param' => $param, 'error' => '');
	}

	private function dingSaveSyncParam($tblName, $param, $listId, $uniqueField, $syncError = '')
	{
		if($syncError !== '' || empty($param)):
			$this->dingSettingsRedirectError($syncError !== '' ? $syncError : 'Nothing to sync from Ding API.');
		endif;
		if(empty($listId)):
			$this->common_model->addManyData($tblName, $param);
			$this->session->set_flashdata('alert_success', lang('addsuccess'));
		else:
			foreach($param as $item):
				if(!isset($item[$uniqueField]) || $item[$uniqueField] === ''):
					continue;
				endif;
				$existing = $this->common_model->getData('single', $tblName, array('where' => array($uniqueField => $item[$uniqueField])));
				if(empty($existing)):
					$item['status'] = 'A';
					$item['creation_ip'] = currentIp();
					$item['creation_date'] = (int)$this->timezone->utc_time();
					$item['created_by'] = (int)$this->session->userdata('ADMIN_ID');
					$this->common_model->addData($tblName, $item);
				else:
					$item['update_ip'] = currentIp();
					$item['update_date'] = (int)$this->timezone->utc_time();
					$item['updated_by'] = (int)$this->session->userdata('ADMIN_ID');
					$this->common_model->editData($tblName, $item, $uniqueField, $item[$uniqueField]);
				endif;
			endforeach;
			$this->session->set_flashdata('alert_success', lang('updatesuccess'));
		endif;
		redirect(correctLink('DINGRECHARGEDATA', getCurrentControllerPath('settings')));
	}

	private function dingUpdateAllProviderMarkupCommission($markupCommissionPercentage)
	{
		$providerParam = array(
			'markup_commission' => (float) $markupCommissionPercentage,
			'update_ip'         => currentIp(),
			'update_date'       => (int) $this->timezone->utc_time(),
			'updated_by'        => (int) $this->session->userdata('ADMIN_ID'),
		);
		$this->common_model->editMultipleDataByMultipleCondition('ding_provider_list', $providerParam, array());
	}

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name 	: index
	 + + Developed By 	: Dilip Halder
	 + + Purpose  		: This function used for index
	 + + Date 			: 20  March 2026
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	 public function index()
	{	
		$this->admin_model->authCheck('view_data');
		$data['error'] 						= '';
		$data['activeMenu'] 				= 'international_recharge';
		$data['activeSubMenu'] 				= 'ding_countries';
		
		if($this->input->get('searchField') && $this->input->get('searchValue')):
			$sField							= trim((string) $this->input->get('searchField'));
			$sValue							= trim((string) $this->input->get('searchValue'));
			$whereCon                       = $this->_ding_build_list_where_con($sField, $sValue);
			$data['searchField'] 			= $sField;
			$data['searchValue'] 			= $sValue;
		else:
			$whereCon                       = array('like' => '');
			$data['searchField'] 			= "";
			$data['searchValue'] 			= "";
		endif;

		$data['fromDate'] = '';
		$data['toDate'] = '';
				
		$shortField 						= 	array('created_at' => -1);
		$baseUrl 							= 	getCurrentControllerPath('index');
		$this->session->set_userdata('ALLDINGCOUNTRYDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	(isset($qStringdata[1]) && $qStringdata[1] !== '') ? '?'.$qStringdata[1] : '';
		$tblName 							= 	'ding_recharge_history';
		$con 								= 	'';
		$totalRows 							= 	(int) $this->common_model->getData('count',$tblName,$whereCon,$shortField,'0','0');
		
		if($this->input->get('showLength') == 'All'):
			$perPage	 					= 	$totalRows > 0 ? $totalRows : 1;
			$data['perpage'] 				= 	$this->input->get('showLength');  
		elseif($this->input->get('showLength')):
			$perPage	 					= 	(int) $this->input->get('showLength'); 
			$data['perpage'] 				= 	$this->input->get('showLength'); 
		else:
			$perPage	 					= 	(int) SHOW_NO_OF_DATA;
			$data['perpage'] 				= 	SHOW_NO_OF_DATA; 
		endif;

		$uriSegment 						= 	getUrlSegment();
	    $data['PAGINATION']					=	adminPagination($baseUrl,$suffix,$totalRows,$perPage,$uriSegment);

       if($this->uri->segment(getUrlSegment())):
           $page = $this->uri->segment(getUrlSegment());
       else:
           $page = 0;
       endif;
		
		$data['forAction'] 					= 	$baseUrl; 
		if($totalRows):
			$first							=	(int)($page)+1;
			$data['first']					=	$first;
			
			if($data['perpage'] == 'All'):
				$pageData 					=	$totalRows;
			else:
				$pageData 					=	$data['perpage'];
			endif;
			
			$last							=	((int)($page)+$pageData)>$totalRows?$totalRows:((int)($page)+$pageData);
			$data['noOfContent']			=	'Showing '.$first.'-'.$last.' of '.$totalRows.' items';
		else:
			$data['first']					=	1;
			$data['noOfContent']			=	'';
		endif;
		
		$data['ALLDATA'] 					= 	$this->common_model->getData('multiple',$tblName,$whereCon,$shortField,$perPage,$page);
		if (!empty($data['ALLDATA']) && is_array($data['ALLDATA'])) {
			$data['ALLDATA'] = $this->_ding_attach_seller_details($data['ALLDATA']);
			$data['ALLDATA'] = $this->_ding_attach_commission_amount($data['ALLDATA']);
			foreach ($data['ALLDATA'] as $k => $row) {
				$r = is_object($row) ? json_decode(json_encode($row), true) : $row;
				if (!is_array($r)) {
					continue;
				}
				$this->_ding_apply_display_amounts($r);
				$data['ALLDATA'][$k] = $r;
			}
		}
		//  echo "<pre>";print_r($data['ALLDATA']);die();
		$this->layouts->set_title('All Ding Recharge History | International Recharge | Instwin');
		$this->layouts->admin_view('international_recharge/ding/index',array(),$data);
	}
    // END OF FUNCTION

	/**
	 * Build list/export filters for ding_recharge_history.
	 * Seller fields are resolved via uw_users because they are joined after fetch.
	 */
	private function _ding_build_list_where_con($sField, $sValue)
	{
		$whereCon = array('like' => '');
		$sField = trim((string) $sField);
		$sValue = trim((string) $sValue);
		if ($sField === '' || $sValue === '') {
			return $whereCon;
		}

		$sellerFieldMap = array(
			'seller_users_mobile'     => 'users_mobile',
			'seller_users_type'       => 'users_type',
			'seller_users_pos_number' => 'pos_number',
		);
		if (isset($sellerFieldMap[$sField])) {
			$userField = $sellerFieldMap[$sField];
			$userWhere = array();
			if ($userField === 'users_mobile' || $userField === 'pos_number') {
				if (!ctype_digit($sValue)) {
					$whereCon['where'] = array('users_id' => -1);
					return $whereCon;
				}
				$userWhere['where'] = array($userField => (int) $sValue);
			} else {
				$userWhere['like'] = array('0' => $userField, '1' => $sValue);
			}
			$users = $this->common_model->getData('multiple', 'uw_users', $userWhere, array('_id' => -1));
			$userIds = array();
			if (!empty($users) && is_array($users)) {
				foreach ($users as $u) {
					$u = is_object($u) ? json_decode(json_encode($u), true) : $u;
					if (!empty($u['users_id'])) {
						$userIds[] = (int) $u['users_id'];
					}
				}
			}
			$userIds = array_values(array_unique($userIds));
			if (empty($userIds)) {
				$whereCon['where'] = array('users_id' => -1);
			} elseif (count($userIds) === 1) {
				$whereCon['where'] = array('users_id' => $userIds[0]);
			} else {
				$whereCon['where_in'] = array('users_id', $userIds);
			}
			return $whereCon;
		}

		if ($sField === 'transaction_id') {
			if (ctype_digit($sValue)) {
				$whereCon['where'] = array('transaction_id' => (int) $sValue);
			} else {
				$whereCon['like'] = array('0' => 'transaction_id', '1' => $sValue);
			}
		} elseif ($sField === 'account_number') {
			$whereCon['like'] = array('0' => 'account_number', '1' => $sValue);
		} elseif (is_numeric($sValue)) {
			$whereCon['where'] = array($sField => (int) $sValue);
		} else {
			$whereCon['like'] = array('0' => $sField, '1' => $sValue);
		}

		return $whereCon;
	}

	private function _ding_created_at_bounds($fromRaw, $toRaw)
	{
		$fromRaw = trim((string) $fromRaw);
		$toRaw = trim((string) $toRaw);
		if ($fromRaw === '' && $toRaw === '') {
			$startDateStr = date('Y-m-d 00:00');
			$endDateStr = date('Y-m-d 23:59');
		} elseif ($fromRaw !== '' && $toRaw !== '') {
			$startDateStr = date('Y-m-d H:i', strtotime(str_replace('T', ' ', $fromRaw)));
			$endDateStr = date('Y-m-d H:i', strtotime(str_replace('T', ' ', $toRaw)));
		} elseif ($fromRaw !== '') {
			$startDateStr = date('Y-m-d H:i', strtotime(str_replace('T', ' ', $fromRaw)));
			$endDateStr = date('Y-m-d 23:59', strtotime($startDateStr));
		} else {
			$endDateStr = date('Y-m-d H:i', strtotime(str_replace('T', ' ', $toRaw)));
			$startDateStr = date('Y-m-d 00:00', strtotime($endDateStr));
		}
		$startTs = strtotime(str_replace('T', ' ', $startDateStr));
		$endTs = strtotime(str_replace('T', ' ', $endDateStr));
		if ($startTs === false) {
			$startTs = strtotime(date('Y-m-d 00:00'));
		}
		if ($endTs === false) {
			$endTs = time();
		}
		$endTsInclusive = (int) $endTs + 59;

		return array($startDateStr, $endDateStr, (int) $startTs, $endTsInclusive);
	}

	/**
	 * Build Mongo where + date bounds for Ding export (same filters as list / old exportexcel).
	 *
	 * @return array{whereCon:array,fromDate:mixed,toDate:mixed,searchField:string,searchValue:string}
	 */
	private function _ding_build_export_where_from_inputs($fromPost, $toPost, $searchField, $searchValue)
	{
		$whereCon = array();
		$sField = trim((string) $searchField);
		$sValue = trim((string) $searchValue);
		if ($sField !== '' && $sValue !== '') {
			$whereCon = $this->_ding_build_list_where_con($sField, $sValue);
		} else {
			$whereCon['like'] = '';
		}
		list($startDateStr, $endDateStr, $startTs, $endTs) = $this->_ding_created_at_bounds(
			trim((string) $fromPost),
			trim((string) $toPost)
		);
		unset($startDateStr, $endDateStr);
		$whereCon['where_gte'] = array(array('created_at', (int) $startTs));
		$whereCon['where_lte'] = array(array('created_at', (int) $endTs));

		return array(
			'whereCon' => $whereCon,
			'fromDate' => $fromPost,
			'toDate' => $toPost,
			'searchField' => $sField,
			'searchValue' => $sValue,
		);
	}

	private function _ding_export_where_from_post()
	{
		return $this->_ding_build_export_where_from_inputs(
			$this->input->post('fromDate'),
			$this->input->post('toDate'),
			$this->input->post('searchField'),
			$this->input->post('searchValue')
		);
	}

	/**
	 * ding_recharge_history stores users_id only; seller POS/type/mobile come from users.
	 *
	 * @param array $rows Rows from ding_recharge_history
	 * @return array
	 */
	private function _ding_attach_seller_details($rows)
	{
		if (empty($rows) || !is_array($rows)) {
			return $rows;
		}
		$userIds = array();
		foreach ($rows as $row) {
			$r = is_object($row) ? json_decode(json_encode($row), true) : $row;
			if (!empty($r['users_id'])) {
				$userIds[] = (int) $r['users_id'];
			}
		}
		$userIds = array_values(array_unique($userIds));
		if (empty($userIds)) {
			return $rows;
		}
		$uw = array('where_in' => array('users_id', $userIds));
		$users = $this->common_model->getData('multiple', 'uw_users', $uw);
		$userMap = array();
		if (!empty($users)) {
			foreach ($users as $u) {
				$u = is_object($u) ? json_decode(json_encode($u), true) : $u;
				if (isset($u['users_id'])) {
					$userMap[(int) $u['users_id']] = $u;
				}
			}
		}
		$out = array();
		foreach ($rows as $row) {
			$r = is_object($row) ? json_decode(json_encode($row), true) : $row;
			if (!is_array($r)) {
				continue;
			}
			$uid = isset($r['users_id']) ? (int) $r['users_id'] : 0;
			if ($uid > 0 && isset($userMap[$uid])) {
				$u = $userMap[$uid];
				$r['seller_users_pos_number'] = isset($u['pos_number']) ? $u['pos_number'] : '';
				$r['seller_users_type'] = isset($u['users_type']) ? $u['users_type'] : '';
				$r['seller_users_mobile'] = isset($u['users_mobile']) ? (string) $u['users_mobile'] : '';
			}
			$out[] = $r;
		}
		return $out;
	}

	/**
	 * Attach credited commission amount per recharge history row (from loadBalance).
	 *
	 * @param array $rows Rows from ding_recharge_history
	 * @return array
	 */
	private function _ding_attach_commission_amount($rows)
	{
		if (empty($rows) || !is_array($rows)) {
			return $rows;
		}
		$oidMap = array();
		foreach ($rows as $row) {
			$r = is_object($row) ? json_decode(json_encode($row), true) : $row;
			if (!is_array($r) || empty($r['_id'])) {
				continue;
			}
			$idNorm = json_decode(json_encode($r['_id']), true);
			$oidStr = '';
			if (is_array($idNorm) && isset($idNorm['$id'])) {
				$oidStr = (string) $idNorm['$id'];
			} elseif (is_array($idNorm) && isset($idNorm['$oid'])) {
				$oidStr = (string) $idNorm['$oid'];
			}
			if ($oidStr === '' || isset($oidMap[$oidStr])) {
				continue;
			}
			try {
				$oidMap[$oidStr] = new MongoDB\BSON\ObjectId($oidStr);
			} catch (Exception $e) {
				// ignore invalid ids
			}
		}
		if (empty($oidMap)) {
			return $rows;
		}

		$whereLb = array(
			'where' => array(
				'narration' => 'Ding Recharge Commission',
				'status' => 'A',
			),
			'where_in' => array('request_oid', array_values($oidMap)),
		);
		$lbRows = $this->common_model->getData('multiple', 'uw_loadBalance', $whereLb);
		$commissionMap = array();
		if (!empty($lbRows) && is_array($lbRows)) {
			foreach ($lbRows as $lb) {
				$x = is_object($lb) ? json_decode(json_encode($lb), true) : $lb;
				if (!is_array($x) || empty($x['request_oid'])) {
					continue;
				}
				$ridNorm = json_decode(json_encode($x['request_oid']), true);
				$ridStr = '';
				if (is_array($ridNorm) && isset($ridNorm['$id'])) {
					$ridStr = (string) $ridNorm['$id'];
				} elseif (is_array($ridNorm) && isset($ridNorm['$oid'])) {
					$ridStr = (string) $ridNorm['$oid'];
				}
				if ($ridStr === '') {
					continue;
				}
				if (!isset($commissionMap[$ridStr])) {
					$commissionMap[$ridStr] = $this->_ding_money_value($x['upoints'] ?? 0);
				}
			}
		}

		$out = array();
		foreach ($rows as $row) {
			$r = is_object($row) ? json_decode(json_encode($row), true) : $row;
			if (!is_array($r)) {
				continue;
			}
			$idNorm = !empty($r['_id']) ? json_decode(json_encode($r['_id']), true) : array();
			$oidStr = '';
			if (is_array($idNorm) && isset($idNorm['$id'])) {
				$oidStr = (string) $idNorm['$id'];
			} elseif (is_array($idNorm) && isset($idNorm['$oid'])) {
				$oidStr = (string) $idNorm['$oid'];
			}
			$storedCommission = isset($r['commission_amount']) ? $this->_ding_money_value($r['commission_amount']) : 0.0;
			if ($storedCommission > 0) {
				$r['commission_amount'] = $storedCommission;
			} elseif ($oidStr !== '' && isset($commissionMap[$oidStr])) {
				$r['commission_amount'] = $this->_ding_money_value($commissionMap[$oidStr]);
			} else {
				$r['commission_amount'] = 0.0;
			}
			$out[] = $r;
		}
		return $out;
	}

	/**
	 * Cancel a Ding recharge: reverse user recharge balance and commission (loadBalance),
	 * update history, so sales summary (Ding Recharge / Cancelled narrations) stays consistent.
	 */
	public function cancel($historyId = '')
	{
		$this->admin_model->authCheck('edit_data');
		if (strtoupper((string) $this->input->server('REQUEST_METHOD')) !== 'POST') {
			$this->session->set_flashdata('alert_error', 'Invalid cancel request.');
			redirect(correctLink('DINGRECHARGEDATA', getCurrentControllerPath('international_recharge/ding/index')));
			return;
		}
		$cancelNarration = trim((string) $this->input->post('cancel_narration'));
		if ($cancelNarration === '') {
			$this->session->set_flashdata('alert_error', 'Cancel narration is required.');
			redirect(correctLink('DINGRECHARGEDATA', getCurrentControllerPath('international_recharge/ding/index')));
			return;
		}
		$historyId = trim((string) $historyId);
		if ($historyId === '') {
			$this->session->set_flashdata('alert_error', 'Invalid recharge record.');
			redirect(correctLink('DINGRECHARGEDATA', getCurrentControllerPath('international_recharge/ding/index')));
			return;
		}

		try {
			$oid = new MongoDB\BSON\ObjectId($historyId);
		} catch (Exception $e) {
			$this->session->set_flashdata('alert_error', 'Invalid recharge id.');
			redirect(correctLink('DINGRECHARGEDATA', getCurrentControllerPath('international_recharge/ding/index')));
			return;
		}

		$tblHistory = 'ding_recharge_history';
		$whereH = array('where' => array('_id' => $oid));
		$history = $this->common_model->getData('single', $tblHistory, $whereH);
		if (empty($history)) {
			$this->session->set_flashdata('alert_error', 'Recharge record not found.');
			redirect(correctLink('DINGRECHARGEDATA', getCurrentControllerPath('international_recharge/ding/index')));
			return;
		}

		$state = isset($history['recharge_state']) ? (string) $history['recharge_state'] : '';
		if ($state === 'Cancelled' || !empty($history['cancelled_at'])) {
			$this->session->set_flashdata('alert_error', 'This recharge is already cancelled.');
			redirect(correctLink('MASTERDATARECHARGETYPE', getCurrentControllerPath('international_recharge/ding/index')));
			return;
		}

		if ($state !== 'Complete') {
			$this->session->set_flashdata('alert_error', 'Only completed recharges with a settled balance can be cancelled here.');
			redirect(correctLink('MASTERDATARECHARGETYPE', getCurrentControllerPath('international_recharge/ding/index')));
			return;
		}

		$usersId = isset($history['users_id']) ? (int) $history['users_id'] : 0;
		if ($usersId < 1) {
			$this->session->set_flashdata('alert_error', 'Invalid user on this record.');
			redirect(correctLink('MASTERDATARECHARGETYPE', getCurrentControllerPath('international_recharge/ding/index')));
			return;
		}

		$whereDup = array(
			'where' => array(
				'request_oid' => $oid,
				'narration'   => 'Ding Recharge Cancelled',
				'status'      => 'A',
			),
		);
		if ((int) $this->common_model->getData('count', 'uw_loadBalance', $whereDup) > 0) {
			$this->session->set_flashdata('alert_error', 'Cancellation was already applied for this recharge.');
			redirect(correctLink('MASTERDATARECHARGETYPE', getCurrentControllerPath('international_recharge/ding/index')));
			return;
		}

		$whereLb = array(
			'where' => array(
				'request_oid' => $oid,
				'status'      => 'A',
			),
		);
		$lbRows = $this->common_model->getData('multiple', 'uw_loadBalance', $whereLb, array('_id' => 1));
		$mainUpoints = 0.0;
		$commissionUpoints = 0.0;
		if (!empty($lbRows)) {
			foreach ($lbRows as $lb) {
				$lb = is_object($lb) ? json_decode(json_encode($lb), true) : $lb;
				if (!is_array($lb)) {
					continue;
				}
				$n = isset($lb['narration']) ? (string) $lb['narration'] : '';
				$rt = isset($lb['record_type']) ? (string) $lb['record_type'] : '';
				if ($n === 'Ding Recharge') {
					$u = (float) ($lb['upoints'] ?? 0);
					// Prefer original debit entry amount when available.
					if ($rt === 'Debit' && $u > 0) {
						$mainUpoints = $u;
					} elseif ($mainUpoints <= 0) {
						$mainUpoints = $u;
					}
				} elseif ($n === 'Ding Recharge Commission') {
					$commissionUpoints = $this->_ding_money_value($lb['upoints'] ?? 0);
				}
			}
		}
		if ($mainUpoints <= 0 && isset($history['amount'])) {
			$mainUpoints = $this->_ding_money_value($history['amount']);
		}
		if ($commissionUpoints <= 0 && isset($history['commission_amount'])) {
			$commissionUpoints = $this->_ding_money_value($history['commission_amount']);
		}
		// Keep reversal aligned with recharge amount slabs (e.g. 2.825 => 2.85).
		$mainUpoints = round($mainUpoints * 20, 0, PHP_ROUND_HALF_UP) / 20;
		if ($mainUpoints <= 0) {
			$this->session->set_flashdata('alert_error', 'Could not determine recharge amount to reverse.');
			redirect(correctLink('MASTERDATARECHARGETYPE', getCurrentControllerPath('international_recharge/ding/index')));
			return;
		}

		$whereUser = array('where' => array('users_id' => $usersId));
		$userData = $this->common_model->getData('single', 'uw_users', $whereUser);
		if (empty($userData)) {
			$this->session->set_flashdata('alert_error', 'User not found.');
			redirect(correctLink('MASTERDATARECHARGETYPE', getCurrentControllerPath('international_recharge/ding/index')));
			return;
		}

		$userOidStr = '';
		if (!empty($userData['_id'])) {
			$idNorm = json_decode(json_encode($userData['_id']), true);
			if (is_array($idNorm) && isset($idNorm['$id'])) {
				$userOidStr = (string) $idNorm['$id'];
			}
		}
		if ($userOidStr === '') {
			$this->session->set_flashdata('alert_error', 'Invalid user reference.');
			redirect(correctLink('MASTERDATARECHARGETYPE', getCurrentControllerPath('international_recharge/ding/index')));
			return;
		}
		$userOid = new MongoDB\BSON\ObjectId($userOidStr);

		$curRecharge = (float) ($userData['availableReachargePoints'] ?? 0);
		$curArabian = (float) ($userData['availableArabianPoints'] ?? 0);
		$afterMain = $curRecharge + $mainUpoints;
		$afterCommission = $afterMain - $commissionUpoints;
		$ip = method_exists($this->input, 'ip_address') ? $this->input->ip_address() : '';
		$adminName = (string) $this->session->userdata('ADMIN_NAME');
		$ts = strtotime(date('Y-m-d H:i:s'));
		$destNumber = '';
		if (isset($history['account_number'])) {
			$destNumber = trim((string) $history['account_number']);
		}
		$sendCurrencyIso = !empty($history['send_currency_iso']) ? trim((string) $history['send_currency_iso']) : 'AED';
		$toNumber = ($destNumber !== '') ? ' to '.$destNumber : '';
		$mainDisplay = $this->_ding_format_display_amount($mainUpoints);
		$commissionDisplay = number_format($commissionUpoints, 3, '.', '');

		// 1) Refund main Ding debit (matches API Ding + getRechargeSummary "Ding Recharge Cancelled")
		$lb1['load_balance_id'] = (int) $this->common_model->getNextSequence('uw_loadBalance');
		$lb1['users_oid'] = $userOid;
		$lb1['request_oid'] = $oid;
		$lb1['user_id_deb'] = 0;
		$lb1['user_id_cred'] = $usersId;
		$lb1['availableArabianPoints'] = $curArabian;
		$lb1['end_balance'] = $curArabian;
		$lb1['availableReachargePoints'] = $curRecharge;
		$lb1['end_balance_recharge'] = $afterMain;
		$lb1['record_type'] = 'Credit';
		$lb1['narration'] = 'Ding Recharge Cancelled';
		$lb1['remarks'] = 'Cancelled Recharge Amount: '.$mainDisplay.' '.$sendCurrencyIso.$toNumber.'. Narration: '.$cancelNarration;
		if ($destNumber !== '') {
			$lb1['destination_number'] = $destNumber;
		}
		$lb1['upoints'] = $mainUpoints;
		$lb1['creation_ip'] = $ip;
		$lb1['created_at'] = $ts;
		$lb1['created_by'] = $adminName;
		$lb1['status'] = 'A';
		$this->common_model->addData('uw_loadBalance', $lb1);

		// 2) Reverse commission credited on the original transaction
		if ($commissionUpoints > 0) {
			$lb2['load_balance_id'] = (int) $this->common_model->getNextSequence('uw_loadBalance');
			$lb2['users_oid'] = $userOid;
			$lb2['request_oid'] = $oid;
			$lb2['user_id_deb'] = $usersId;
			$lb2['user_id_cred'] = 0;
			$lb2['availableArabianPoints'] = $curArabian;
			$lb2['end_balance'] = $curArabian;
			$lb2['availableReachargePoints'] = $afterMain;
			$lb2['end_balance_recharge'] = $afterCommission;
			$lb2['record_type'] = 'Debit';
			$lb2['narration'] = 'Ding Recharge Commission Cancelled';
			$lb2['remarks'] = 'Cancelled Commission Amount: '.$commissionDisplay.' '.$sendCurrencyIso.$toNumber.'. Narration: '.$cancelNarration;
			if ($destNumber !== '') {
				$lb2['destination_number'] = $destNumber;
			}
			$lb2['upoints'] = $commissionUpoints;
			$lb2['creation_ip'] = $ip;
			$lb2['created_at'] = $ts;
			$lb2['created_by'] = $adminName;
			$lb2['status'] = 'A';
			$this->common_model->addData('uw_loadBalance', $lb2);
		}

		$userParam['availableReachargePoints'] = $afterCommission;
		$userParam['update_date'] = date('Y-m-d H:i:s');
		$userParam['update_by'] = $adminName;
		$this->common_model->editData('uw_users', $userParam, '_id', $userOid);

		$histUp['recharge_state'] = 'Cancelled';
		$histUp['cancelled_at'] = $ts;
		$histUp['cancelled_by'] = $adminName;
		$histUp['cancel_narration'] = $cancelNarration;
		$this->common_model->editData('ding_recharge_history', $histUp, '_id', $oid);

		$this->session->set_flashdata('alert_success', 'Recharge cancelled; balance and commission have been reversed.');
		redirect(correctLink('DINGRECHARGEDATA', getCurrentControllerPath('international_recharge/ding/index')));
	}

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name : addeditdata
	 + + Developed By  : Dilip Halder
	 + + Purpose  	   : This function used for Add Edit data
	 + + Date 		   : 30 January 2026
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function addeditdata($editId='')
	{		
		$data['error'] 		   = '';
		$data['activeMenu']    = 'internation_recharge';
		$data['activeSubMenu'] = 'dingplanlist';

		$tblName 	= 'ding_provider_list';

		// This document holds the full provider `list` array (as in Ding API response).
		$whereCon['where'] 	= array('status' => 'A');
		$doc 				= $this->common_model->getData('single',$tblName,$whereCon);

		$docId 		= '';
		$providers 	= array();
		if(!empty($doc)):
			if(is_array($doc)):
				$idField = isset($doc['_id']) ? $doc['_id'] : null;
				if(is_array($idField) && isset($idField['$id'])):
					$docId = (string)$idField['$id'];
				elseif(is_object($idField) && isset($idField->{'$id'})):
					$docId = (string)$idField->{'$id'};
				endif;
				$providers = (isset($doc['list']) && is_array($doc['list'])) ? $doc['list'] : array();
			elseif(is_object($doc)):
				$idField = isset($doc->_id) ? $doc->_id : null;
				if(is_object($idField) && isset($idField->{'$id'})):
					$docId = (string)$idField->{'$id'};
				endif;
				$providers = (isset($doc->list) && is_array($doc->list)) ? $doc->list : array();
			endif;
		endif;

		$isEdit = !empty($editId);

		if($isEdit):
			$this->admin_model->authCheck('edit_data');
			$data['EDITDATA'] = array();
			// Pre-fill provider code even if provider details aren't found.
			$data['EDITDATA']['ProviderCode'] = (string)$editId;
			foreach($providers as $p):
				$pCode = is_object($p) ? ($p->ProviderCode ?? '') : ($p['ProviderCode'] ?? '');
				if((string)$pCode === (string)$editId):
					$data['EDITDATA'] = is_object($p) ? json_decode(json_encode($p), true) : $p;
					break;
				endif;
			endforeach;
		else:
			$this->admin_model->authCheck('add_data');
			$data['EDITDATA'] = array(
				'ProviderCode' => '',
				'CountryIso' => '',
				'Name' => '',
				'ValidationRegex' => '',
				'CustomerCareNumber' => '',
				'RegionCodes' => array(),
				'PaymentTypes' => array('Prepaid'),
				'LogoUrl' => '',
			);
		endif;

		if($this->input->post('SaveChanges')):
			$error = 'NO';
			$this->form_validation->set_rules('CountryIso'        , 'Country ISO'        , 'trim|required');
			$this->form_validation->set_rules('Name'              , 'Provider Name'      , 'trim|required');
			$this->form_validation->set_rules('ValidationRegex'  , 'Validation Regex'   , 'trim|required');
			$this->form_validation->set_rules('SaveChanges'      , 'SaveChanges'        , 'trim|required');
			// Provider code is required for add. For edit, it comes from URL param ($editId).
			if(!$isEdit):
				$this->form_validation->set_rules('ProviderCode' , 'Provider Code'      , 'trim|required');
			endif;

			if($this->form_validation->run() && $error == 'NO'):
				$providerCode = $isEdit ? (string)$editId : trim((string)$this->input->post('ProviderCode'));
				$countryIso   = strtoupper(trim((string)$this->input->post('CountryIso')));
				$name         = addslashes(trim((string)$this->input->post('Name')));
				$regex        = trim((string)$this->input->post('ValidationRegex'));

				$customerCareNumber = trim((string)$this->input->post('CustomerCareNumber'));
				$logoUrl             = trim((string)$this->input->post('LogoUrl'));

				$paymentTypes = $this->input->post('PaymentTypes');
				if(is_string($paymentTypes)):
					$paymentTypes = array($paymentTypes);
				endif;
				if(empty($paymentTypes) || !is_array($paymentTypes)):
					$paymentTypes = array('Prepaid');
				endif;
				$paymentTypes = array_values(array_map('trim', $paymentTypes));

				$regionCodesInput = trim((string)$this->input->post('RegionCodes'));
				$regionCodes = array();
				if($regionCodesInput !== ''):
					$parts = array_map('trim', explode(',', $regionCodesInput));
					foreach($parts as $part):
						if($part !== '') $regionCodes[] = $part;
					endforeach;
				endif;
				if(empty($regionCodes)):
					$regionCodes = array($countryIso);
				endif;

				$newProvider = array(
					'ProviderCode' => $providerCode,
					'CountryIso' => $countryIso,
					'Name' => $name,
					'ValidationRegex' => $regex,
					'CustomerCareNumber' => $customerCareNumber,
					'RegionCodes' => $regionCodes,
					'PaymentTypes' => $paymentTypes,
					'LogoUrl' => $logoUrl
				);

				$updated = false;
				foreach($providers as $idx => $p):
					$pCode = is_object($p) ? ($p->ProviderCode ?? '') : ($p['ProviderCode'] ?? '');
					if((string)$pCode === (string)$providerCode):
						$providers[$idx] = $newProvider;
						$updated = true;
						break;
					endif;
				endforeach;
				if(!$updated):
					$providers[] = $newProvider;
				endif;

				$param = array('list' => $providers);
				if(empty($docId)):
					$param['status']        = 'A';
					$param['creation_ip']   = currentIp();
					$param['creation_date'] = (int)$this->timezone->utc_time();
					$param['created_by']    = (int)$this->session->userdata('ADMIN_ID');
					$this->common_model->addData($tblName, $param);
					$this->session->set_flashdata('alert_success',lang('addsuccess'));
				else:
					$param['update_ip']   = currentIp();
					$param['update_date'] = (int)$this->timezone->utc_time();
					$param['updated_by']  = (int)$this->session->userdata('ADMIN_ID');
					$this->common_model->editData($tblName, $param, '_id', new MongoDB\BSON\ObjectID($docId));
					$this->session->set_flashdata('alert_success',lang('updatesuccess'));
				endif;

				redirect(correctLink('DINGRECHARGEDATA',getCurrentControllerPath('index')));
			endif;
		endif;

		$this->layouts->set_title($isEdit ? 'Edit Ding Provider | International Recharge | Instwin' : 'Add Ding Provider | International Recharge | Instwin');
		$this->layouts->admin_view('international_recharge/ding/addeditprovider',array(),$data);
	}	// END OF FUNCTION			

	/***********************************************************************
	** Function name 	: changestatus
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for change status
	** Date 			: 30 January 2026
	************************************************************************/
	function changestatus($changeStatusId='',$statusType='')
	{  
		$this->admin_model->authCheck('edit_data');
		$param['status'] =	$statusType;
		$this->common_model->editData('tambola_games',$param,'_id', new MongoDB\BSON\ObjectID($changeStatusId));
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		
		redirect(correctLink('DINGRECHARGEDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name 	: deletedata
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for delete data
	** Date 			: 30 January 2026
	************************************************************************/
	function deletedata($deleteId='')
	{  
		$this->admin_model->authCheck('delete_data');
		$this->common_model->deleteData('tambola_games','_id', new MongoDB\BSON\ObjectID($deleteId));
		$this->session->set_flashdata('alert_success',lang('deletesuccess'));
		redirect(correctLink('DINGRECHARGEDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name 	: getsub_categoryData
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for delete data
	** Date 			: 30 January 2026
	************************************************************************/
	public function getsubcategoryData(){
		
		$categoryId        = $this->input->post('category_oid');
		$subCategoryId     = $this->input->post('_id');
		$whereCon['where'] = array('category_oid' => new MongoDB\BSON\ObjectId($categoryId));	
		$shortField        = array('sub_category_name' => 'ASC');
		$subCategoryData  = $this->common_model->getData('multiple','sub_category',$whereCon,$shortField);
		 
		if(!empty($subCategoryData)):
			$html = '<option value="">Select Sub Category</option>';
			 
			foreach($subCategoryData as $subCategoryData):
				if($subCategoryId == $subCategoryData['_id']->{'$id'}): $select = 'selected="selected"'; else: $select = ''; endif;
		        $html .='<option '.$select.' value="'.$subCategoryData["_id"]->{'$id'}.'">'.stripslashes($subCategoryData["sub_category"]).'</option>';
	         endforeach;
	    else:
	    	$html = '<option value="">No Sub Category</option>';
	    endif;
        echo $html;
        die();
	}

	/**
	 * One export row for JSON / SheetJS (same columns as legacy PhpSpreadsheet export).
	 */
	private function _ding_export_row_assoc($r, $slNo)
	{
		$r = is_object($r) ? json_decode(json_encode($r), true) : $r;
		if (!is_array($r)) {
			return null;
		}
		$rechargeState = (string) ($r['recharge_state'] ?? '');
		$isReversed = ($rechargeState === 'Cancelled' || !empty($r['cancelled_at']));
		if ($isReversed) {
			$displayStatus = 'Cancelled';
		} elseif ($rechargeState === 'Complete') {
			$displayStatus = 'Success';
		} else {
			$displayStatus = $rechargeState !== '' ? $rechargeState : '—';
		}
		$created = '';
		if (isset($r['created_at'])) {
			$created = date('Y-m-d H:i:s', (int) $r['created_at']);
		}
		return array(
			'Sl.No' => (int) $slNo,
			'Transaction ID' => (string) ($r['transaction_id'] ?? ''),
			'Provider' => (string) ($r['provider_name'] ?? ''),
			'Recharge Number' => (string) ($r['account_number'] ?? ''),
			'Amount' => isset($r['amount']) ? $this->_ding_format_display_amount($r['amount']) : '',
			'Commission' => (string) ($r['display_commission_amount'] ?? '0.00'),
			'Currency' => (string) ($r['send_currency_iso'] ?? ''),
			'Seller POS' => (string) ($r['seller_users_pos_number'] ?? ''),
			'Seller Type' => (string) ($r['seller_users_type'] ?? ''),
			'Seller Mobile' => (string) ($r['seller_users_mobile'] ?? ''),
			'Created At' => $created,
			'Recharge State' => $displayStatus,
			'Cancel Narration' => (string) ($r['cancel_narration'] ?? ''),
		);
	}

	/***********************************************************************
	** Function name : exportexcel
	** Purpose       : Chunked export landing page (same flow as Tambola orders — SheetJS, no PHP Excel).
	************************************************************************/
	function exportexcel()
	{
		$this->admin_model->authCheck('view_data');
		$this->common_model->generateLogs();

		$built = $this->_ding_export_where_from_post();
		$tblName = 'ding_recharge_history';
		$shortField = array('created_at' => -1);
		$totalRows = (int) $this->common_model->getData('count', $tblName, $built['whereCon'], $shortField, '0', '0');
		$itemsPerPage = 5000;
		$totalPages = $totalRows > 0 ? max(1, (int) ceil($totalRows / $itemsPerPage)) : 1;

		$data['error'] = '';
		$data['activeMenu'] = 'international_recharge';
		$data['activeSubMenu'] = 'ding_countries';
		$data['current_page'] = 1;
		$data['total_page'] = $totalPages;
		$data['searchField'] = $built['searchField'];
		$data['searchValue'] = $built['searchValue'];
		$data['fromDate'] = $this->input->post('fromDate');
		$data['toDate'] = $this->input->post('toDate');

		$this->layouts->set_title('Export | Ding Recharge | Instwin');
		$this->layouts->admin_view('international_recharge/ding/exportexcel', array(), $data);
	}

	/***********************************************************************
	** Function name : exportexcelApi
	** Purpose       : JSON chunks for SheetJS (5000 rows per request).
	************************************************************************/
	function exportexcelApi()
	{
		$this->admin_model->authCheck('view_data');

		$built = $this->_ding_export_where_from_post();
		$whereCon = $built['whereCon'];

		$page = (int) $this->input->post('pageno');
		if ($page < 1) {
			$page = 1;
		}
		$itemsPerPage = 5000;
		$startIndex = ($page - 1) * $itemsPerPage;
		$shortField = array('created_at' => -1);
		$tblName = 'ding_recharge_history';
		$rows = $this->common_model->getData('multiple', $tblName, $whereCon, $shortField, $itemsPerPage, $startIndex);
		if (empty($rows)) {
			$rows = array();
		} elseif (!is_array($rows)) {
			$rows = json_decode(json_encode($rows), true);
		}
		if (!empty($rows)) {
			$rows = $this->_ding_attach_seller_details($rows);
			$rows = $this->_ding_attach_commission_amount($rows);
			foreach ($rows as $k => $row) {
				$r = is_object($row) ? json_decode(json_encode($row), true) : $row;
				if (!is_array($r)) {
					continue;
				}
				$this->_ding_apply_display_amounts($r);
				$rows[$k] = $r;
			}
		}

		$CSVData = array();
		$sl = $startIndex;
		foreach ($rows as $d) {
			$sl++;
			$row = $this->_ding_export_row_assoc($d, $sl);
			if ($row !== null) {
				$CSVData[] = $row;
			}
		}

		$this->output->set_content_type('application/json');
		echo json_encode($CSVData);
		die();
	}

	/***********************************************************************
	** Function name 	: orderstatus
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for change order status
	** Date 			: 30 January 2026
	************************************************************************/
	function orderstatus($changeStatusId='',$statusType='')
	{  
		//echo $changeStatusId; die();
		$this->admin_model->authCheck('edit_data');
		$param['status']		=	$statusType;
		$this->common_model->editData('tambola_games',$param,'_id', new MongoDB\BSON\ObjectID($changeStatusId));
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		redirect(correctLink('DINGRECHARGEDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name 	: settings
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for settings
	** Date 			: 20 March 2026
	************************************************************************/
	public function settings()
	{		
		$data['error'] 		   = '';
		$data['activeMenu']    = 'international_recharge';
		$data['activeSubMenu'] = 'dingplanlist';

		$tblName  			   = 'ding_settings';
		$whereCon['where'] 	   = array('status' => 'A');
		$data['EDITDATA'] 	   = $this->common_model->getData('single',$tblName,$whereCon);
		
		$this->session->set_userdata('DINGRECHARGEDATA',currentFullUrl());
		$saveChanges = $this->input->post('SaveChanges');
		if(!empty($saveChanges)):
			if(empty($data['EDITDATA']) || empty($data['EDITDATA']['api_key'])):
				$this->dingSettingsRedirectError('Save Ding API key in settings first.');
			endif;
			$apiKey = trim((string)$data['EDITDATA']['api_key']);

			if($saveChanges == 'Yes'):
 
				$apiKey  = $this->input->post('api_key');
				$apiMode = $this->input->post('api_mode');
				$markupCommissionPercentage = $this->input->post('markup_commission_percentage');
				$param['api_key']                      = $apiKey; 
				$param['api_mode']                     = $apiMode;
				$param['markup_commission_percentage'] = $markupCommissionPercentage;

				if(!empty($data['EDITDATA'])):
					$ListId = $data['EDITDATA']['_id']->{'$id'};
					$param['update_ip']   = currentIp();
					$param['update_date'] = (int)$this->timezone->utc_time();//currentDateTime();
					$param['updated_by']  = (int)$this->session->userdata('ADMIN_ID');
					$this->common_model->editData($tblName, $param, '_id', new MongoDB\BSON\ObjectID($ListId));
					$this->dingUpdateAllProviderMarkupCommission($markupCommissionPercentage);
					$this->session->set_flashdata('alert_success',lang('updatesuccess'));
				else:
					$param['status']		 = 'A';
					$param['creation_ip']	 = currentIp();
					$param['creation_date']	 = (int)$this->timezone->utc_time();//currentDateTime();
					$param['created_by']	 = (int)$this->session->userdata('ADMIN_ID');
					$alastInsertId			 =	$this->common_model->addData($tblName,$param);
					$this->dingUpdateAllProviderMarkupCommission($markupCommissionPercentage);
					$this->session->set_flashdata('alert_success',lang('addsuccess'));
				endif;
				redirect(correctLink('DINGRECHARGEDATA',getCurrentControllerPath('settings')));
			
			elseif($saveChanges == 'currency_list'):
 
				$curl = curl_init();
				curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://api.dingconnect.com/api/V1/GetCurrencies',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => array(
					'api_key: '.$apiKey,
					'Cookie: __cf_bm=WVnhgqkVZoTLYFA5zp4LBFJXcrsh1YAaFt7LWktmixk-1773996877.797355-1.0.1.1-dccjhyNfRr6tD47M.ps6M8iF_KfinDE3WVv8OMOSJuIc_O1RTbUJDWi5RdyewC1b4QPLWvpQ4mBIUl94Za3F_veo8pHQ.DA4g_yGVP89a8DBvBZGOf9.vTnORdlUU9lo'
				),
				));

				$response = curl_exec($curl);
				$response = json_decode($response, true);
				curl_close($curl);

				$tblName = 'ding_currency_list';
                $List    = $this->common_model->getData('single',$tblName);
				$ListId = !empty($List) ? $List['_id']->{'$id'} : '';
				$built = $this->dingBuildSyncParamFromItems($response, $ListId);
				$this->dingSaveSyncParam($tblName, $built['param'], $ListId, 'CurrencyIso', $built['error']);
				
			elseif($saveChanges == 'country_code'):

				$curl = curl_init();
				curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://api.dingconnect.com/api/V1/GetCountries',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => array(
					'api_key: '.$apiKey,
					'Cookie: __cf_bm=tJf3mLxOgIos1k.lALNdZpZCwmnn0sqmINkHl8uRUtI-1773998832.1748617-1.0.1.1-519MsXYUbBu6LFHNj501a5g7qGyXaqhIVHbalM.rnGpMJAkOEQNPiQM7tE.rZsEeci1X_gJHzeklZfbI4u2HkE14eIsgkKPAqFVPpRcLNAmzSr.Eo88WZR2un99bAWuk'
				),
				));

				$response = curl_exec($curl);
				curl_close($curl);
				$response = json_decode($response, true);

				$tblName = 'ding_country_list';
                $List    = $this->common_model->getData('single',$tblName);
				$ListId = !empty($List) ? $List['_id']->{'$id'} : '';
				$built = $this->dingBuildSyncParamFromItems($response, $ListId);
				$this->dingSaveSyncParam($tblName, $built['param'], $ListId, 'CountryIso', $built['error']);

			
			elseif($saveChanges == 'region_list'):

				$curl = curl_init();
				curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://api.dingconnect.com/api/V1/GetRegions',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => 'GET',
				CURLOPT_HTTPHEADER => array(
					'api_key: '.$apiKey,
					'Cookie: __cf_bm=vr_75y0TJ1dZTW4CvOZpytjy_J44GF66jQzbzsqVf0c-1773999866.6394-1.0.1.1-DpvMLlNxa1FmF8oOeYhr8j6M70WhC.BhGPpzjYpwRcpYvSTpkrGlH_7n9dFyi9LHKpcmlWbryxfyonzJCccyyt7xgy8kiu6kwQXQQOxZfiXbjeFzGt.l1wD4_cZeZ0sH'
				),
				));

				$response = curl_exec($curl);

				curl_close($curl);
				$response = json_decode($response, true);

				$tblName = 'ding_region_list';
                $List    = $this->common_model->getData('single',$tblName);
				$ListId = !empty($List) ? $List['_id']->{'$id'} : '';
				$built = $this->dingBuildSyncParamFromItems($response, $ListId);
				$this->dingSaveSyncParam($tblName, $built['param'], $ListId, 'RegionCode', $built['error']);
			elseif($saveChanges == 'provider_list'):


				$curl = curl_init();

				curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://api.dingconnect.com/api/V1/GetProviders',
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
				curl_close($curl);
				$response = json_decode($response, true);
 

				$tblName = 'ding_provider_list';
                $List    = $this->common_model->getData('single',$tblName);
				$ListId = !empty($List) ? $List['_id']->{'$id'} : '';
				$extraForNew = array(
					'markup_commission' => (int)$data['EDITDATA']['markup_commission_percentage'],
				);
				$built = $this->dingBuildSyncParamFromItems($response, $ListId, $extraForNew);
				$this->dingSaveSyncParam($tblName, $built['param'], $ListId, 'ProviderCode', $built['error']);

			elseif($saveChanges == 'products_list'):
 
				$curl = curl_init();
				curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://api.dingconnect.com/api/V1/GetProducts',
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
				curl_close($curl);
				$response = json_decode($response, true);
				$tblName = 'ding_products_list';
                $List    = $this->common_model->getData('single',$tblName);
				$ListId = !empty($List) ? $List['_id']->{'$id'} : '';
				$built = $this->dingBuildSyncParamFromItems($response, $ListId);
				$param = $built['param'];
				if($built['error'] !== '' || empty($param)):
					$this->dingSettingsRedirectError($built['error'] !== '' ? $built['error'] : 'Nothing to sync from Ding API.');
				endif;
				if(empty($ListId)):
					$this->common_model->addManyData($tblName, $param);
					$this->session->set_flashdata('alert_success',lang('addsuccess'));
				else:
					// Collect SkuCodes returned by Ding in this sync so we can mark
					// removed/discontinued products as Inactive afterward. This prevents
					// stale SKUs (no longer offered by Ding) from being used in purchases.
					$liveSkuCodes = array();
					foreach($param as $key => $item):
						$itemSku = isset($item['SkuCode']) ? trim((string)$item['SkuCode']) : '';
						if ($itemSku === ''):
							continue;
						endif;
						$liveSkuCodes[] = $itemSku;

						$existing = $this->common_model->getData('single', $tblName, array('where' => array('SkuCode' => $itemSku)));
						if(empty($existing)):
							$item['status']        = 'A';
							$item['creation_ip']   = currentIp();
							$item['creation_date'] = (int)$this->timezone->utc_time();
							$item['created_by']    = (int)$this->session->userdata('ADMIN_ID');
							$this->common_model->addData($tblName, $item);
						else:
							$item['status']     = 'A';
							$item['update_ip']  = currentIp();
							$item['update_date']= (int)$this->timezone->utc_time();
							$item['updated_by'] = (int)$this->session->userdata('ADMIN_ID');
							$this->common_model->editData($tblName, $item, 'SkuCode', $itemSku);
						endif;
					endforeach;

					// Any active SKU in DB but NOT in latest Ding response = discontinued.
					if (!empty($liveSkuCodes)):
						$this->mongo_db->where(array('status' => 'A'));
						$this->mongo_db->where_not_in('SkuCode', $liveSkuCodes);
						$this->mongo_db->set(array(
							'status'      => 'I',
							'update_ip'   => currentIp(),
							'update_date' => (int)$this->timezone->utc_time(),
							'updated_by'  => (int)$this->session->userdata('ADMIN_ID'),
						));
						$this->mongo_db->update_all($tblName);
					endif;

					$this->session->set_flashdata('alert_success',lang('updatesuccess'));
				endif;
				redirect(correctLink('DINGRECHARGEDATA',getCurrentControllerPath('settings')));
 
			elseif($saveChanges == 'product_description_list'):
				 

				$curl = curl_init();
				curl_setopt_array($curl, array(
				CURLOPT_URL => 'https://api.dingconnect.com/api/V1/GetProductDescriptions',
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
				
				curl_close($curl);
				$response = json_decode($response, true);

				$tblName  = 'ding_products_description_list';
                $List     = $this->common_model->getData('single',$tblName);
				$ListId = !empty($List) ? $List['_id']->{'$id'} : '';
				$built = $this->dingBuildSyncParamFromItems($response, $ListId);
				$this->dingSaveSyncParam($tblName, $built['param'], $ListId, 'LocalizationKey', $built['error']);

			endif;

			
		endif;
		
		if($editId):
			$this->admin_model->authCheck('edit_data');
			$data['EDITDATA']	= $this->common_model->getDataByParticularField($tblName,'_id', new MongoDB\BSON\ObjectID($editId));
		endif;

		$this->layouts->set_title('Ding Settings | International Recharge | Instwin');
		$this->layouts->admin_view('international_recharge/ding/settings',array(),$data);
	}
	// END OF FUNCTION		

}
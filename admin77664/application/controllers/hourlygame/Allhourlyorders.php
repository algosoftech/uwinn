<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Allhourlyorders extends CI_Controller {

	private $hourlyShopExportUserCache = array();
	private $hourlyShopExportPosCache = array();

	public function __construct()
	{
		parent::__construct();
		error_reporting(E_ALL ^ E_NOTICE);
		$this->load->model(array('admin_model', 'emailtemplate_model', 'emailsendgrid_model', 'sms_model', 'notification_model', 'order_model'));
		$this->lang->load('statictext', 'admin');
		$this->load->helper('common');
		$this->load->library('mongodb_client');
	}

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name  : index
	 + + Developed By   : DILIP HALDER
	 + + Purpose        : List Tambola orders (tambola_orders)
	 + + Date           : 02 February 2026
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index()
	{
		$this->admin_model->authCheck('view_data');
		$data['error']           = '';
		$data['activeMenu']      = 'hourlygame';
		$data['activeSubMenu']   = 'allhourlyorders';

		if ($this->input->get('fromDate') !== null && $this->input->get('fromDate') !== '') {
			$fromDate = date('Y-m-d H:i:00', strtotime($this->input->get('fromDate')));
		} else {
			$fromDate = date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' 00:00:00'));
		}
		if ($this->input->get('toDate') !== null && $this->input->get('toDate') !== '') {
			$toDate = date('Y-m-d H:i:59', strtotime($this->input->get('toDate')));
		} else {
			$toDate = date('Y-m-d H:i', strtotime(date('Y-m-d') . ' 23:59:59'));
		}
		
		$searchField = $this->input->get('searchField');
		$searchValue = $this->input->get('searchValue');
		$drawTimeSearch = $this->input->get('drawTimeSearch');
		if ($searchField === 'draw_time_string' && $searchValue !== '' && $searchValue !== null && ($drawTimeSearch === '' || $drawTimeSearch === null)) {
			$drawTimeSearch = $searchValue;
		}

		$whereCon = array('where' => array(
			'created_at' => array(
				'$gte' => (int) strtotime($fromDate),
				'$lte' => (int) strtotime($toDate),
			),
		));

		if (!empty($searchField) && $searchValue !== '' && $searchValue !== null && $searchField !== 'draw_time_string') {
			if ($searchField === 'order_id' || $searchField === 'users.store_name') {
				$whereCon['where'][$searchField] = array('$regex' => $searchValue, '$options' => 'i');
			}elseif($searchField == 'winning_status'){
				$whereCon['where']['winning_status'] =  strtolower($searchValue);
			}

			elseif($searchField == 'users_mobile'){
				$whereConUser['where']['users_mobile'] =  (int) $searchValue;
				$userData = $this->common_model->getData('single', 'uw_users', $whereConUser);
				if(!empty($userData)){
					$whereCon['where']['users_oid'] = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
				}
			}
			elseif($searchField == 'users_email'){
				$whereConUser['where']['users_email'] =  $searchValue;
				$userData = $this->common_model->getData('single', 'uw_users', $whereConUser);
				if(!empty($userData)){
					$whereCon['where']['users_oid'] = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
				}else{
					$whereCon['where']['users_oid'] = 'default';
				}
			}
			elseif($searchField == 'pos_number'){
				$whereConUser['where']['pos_number'] =  (int)$searchValue;
				$userData = $this->common_model->getData('single', 'uw_users', $whereConUser);
				if(!empty($userData)){
					$whereCon['where']['users_oid'] = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
				}else{
					$whereCon['where']['users_oid'] = 'default';
				}
			}
			elseif($searchField == 'settler_pos_number'){
				$whereConUser['where']['pos_number'] = is_numeric($searchValue) ? (int) $searchValue : $searchValue;
				$userData = $this->common_model->getData('single', 'uw_users', $whereConUser);
				if(!empty($userData)){
					$whereCon['where']['settler_users_oid'] = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
				}else{
					$whereCon['where']['settler_users_oid'] = 'default';
				}
			
			} 
			elseif($searchField == 'settler_mobile'){
				$whereConUser['where']['users_mobile'] =  (int) $searchValue;
				$userData = $this->common_model->getData('single', 'uw_users', $whereConUser);
				if(!empty($userData)){
					$whereCon['where']['settler_users_oid'] = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
				}else{
					$whereCon['where']['settler_users_oid'] = 'default';
				}
			}
			else {
				$whereCon['where'][$searchField] = is_numeric($searchValue) ? (int) $searchValue : $searchValue;
			}
		}

		if (!empty($drawTimeSearch)) {
			$this->applyDrawTimeWhereCondition($whereCon, $drawTimeSearch);
		}
		// echo "<pre>"; print_r($whereCon);die();
		$data['searchField'] = $searchField;
		$data['searchValue'] = $searchValue;
		$data['drawTimeSearch'] = $drawTimeSearch;
		$data['fromDate']    = $fromDate;
		$data['toDate']      = $toDate;

		$shortField   = array('created_at' => -1);
		$baseUrl      = getCurrentControllerPath('index');
		$tblName      = 'uw_hourly_orders';

		$this->session->set_userdata('ALLHOURLYORDERDATA', currentFullUrl());
		$qStringdata  = explode('?', currentFullUrl());
		$suffix       = isset($qStringdata[1]) && $qStringdata[1] ? '?' . $qStringdata[1] : '';
		$totalRows    = $this->common_model->getHourlyGameOrderData('count', $tblName, $whereCon);
		
		if($this->input->get('showLength') == 'All'):
			$perPage	 					= 	$totalRows;
			$data['perpage'] 				= 	$this->input->get('showLength');  
		elseif($this->input->get('showLength')):
			$perPage	 					= 	$this->input->get('showLength'); 
			$data['perpage'] 				= 	$this->input->get('showLength'); 
		else:
			$perPage	 					= 	SHOW_NO_OF_DATA;
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
		$data['ALLDATA'] = $this->common_model->getHourlyGameOrderData('multiple', $tblName, $whereCon, $shortField, $perPage, $page);
		$data['ALLHOURLYGAMES'] = $this->common_model->getData('multiple', 'uw_hourly_games', array('where' => array('status' => 'A')), array('title' => 1));
		$this->layouts->set_title('Hourly Game Orders | UWINN');
		$this->layouts->admin_view('hourlygame/allhourlyorders/index', array(), $data);
	}
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name  : addeditdata (view single order)
	 + + Developed By   : DILIP HALDER
	 + + Purpose        : View Tambola order details
	 + + Date           : 02 February 2026
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function addeditdata($orderId = '')
	{
		$this->admin_model->authCheck('view_data');
		$data['error']         = '';
		$data['activeMenu']    = 'hourlygame';
		$data['activeSubMenu'] = 'allhourlyorders';

		if (empty($orderId)) {
			$this->session->set_flashdata('error', 'Order ID required.');
			redirect(getCurrentControllerPath('index'));
			return;
		}

		$shortField = array('created_at' => -1);
		$whereCon['where']['_id'] =  new MongoDB\BSON\ObjectID($orderId);
		$tblName   = 'uw_hourly_orders';	
		$orderData = $this->common_model->getHourlyGameOrderData('single', $tblName, $whereCon);
		$data['order'] =$orderData[0];
		$this->layouts->set_title('Hourly Game Order Details | UWINN');
		$this->layouts->admin_view('hourlygame/allhourlyorders/addeditdata', array(), $data);
	}

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name  : cancelationorder
	 + + Developed By   : DILIP HALDER
	 + + Purpose        : Update Tambola order status (CL = Cancelled)
	 + + Date           : 02 February 2026
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function cancelationorder($orderId = '', $statusType = '')
	{
		try {

			$this->admin_model->authCheck('edit_data');
			$tblName = 'uw_hourly_orders';
			$orderData  = $this->common_model->getDataByParticularField($tblName,'_id',new MongoDB\BSON\ObjectID($orderId));
			// echo "<pre>"; print_r($orderData);die();
 
			// If cancelling (CL), check if already cancelled and handle refund
			if ($statusType === 'CL') {
				if (isset($orderData['status']) && $orderData['status'] === 'CL') {
					throw new Exception('Order is already cancelled.');
				}
 
				// Use MongoDB transaction for cancellation (refund + status update)
				// $this->session->sess_regenerate();
				// $session = $this->mongodb_client->client->startSession();
				// $session->startTransaction();

				// Update order status to CL
				$param1 = array(
					'status'      => 'CL',
					'update_ip'   => currentIp(),
					'update_date' => (int) $this->timezone->utc_time(),
					'refund_date' => (int) $this->timezone->utc_time(),
					'updated_by'  => (int) $this->session->userdata('ADMIN_ID'),
				);
				
				$update1 = $this->common_model->editData($tblName, $param1, '_id', new MongoDB\BSON\ObjectID($orderId));
				// $cancelWhereCon = array('_id' => new MongoDB\BSON\ObjectID($orderId) );
				// $update1 = $this->mongodb_client->updateDocument($tblName, $cancelWhereCon, ['$set' => $param1], $session);
				$cancelWhereCon2 = array('order_oid' => new MongoDB\BSON\ObjectID($orderId));
				$update2 = $this->common_model->editMultipleDataByMultipleCondition('uw_hourly_tickets', $param1, $cancelWhereCon2);
				// $update2 =$this->mongodb_client->updateDocument($tblName, $cancelWhereCon, ['$set' => $param1], $session);

				// Get user data
				$tblNameUser = 'uw_users';
				$whereConUser['where'] = array('users_id' => (int) $orderData['users_id'], 'status' => 'A');
				$shortFieldUser = array('users_id' => -1);
				$userData = $this->common_model->getData('single', $tblNameUser, $whereConUser, $shortFieldUser, '0', '0');

				// if (empty($userData)) {
				// 	$session->abortTransaction();
				// 	throw new Exception('User not found or inactive.');
				// }

				// Send notification
				// $userid = (int) $orderData['users_id'];
				// $message = 'Tambola Order ID ' . $orderId . ' has been cancelled. Refund will be processed.';
				// $title = 'Tambola Order Cancelled (' . $orderId . ')';
				// $this->common_model->saveNotifications($userid, $title, $message, $orderId);

				// Refund wallet statement - Credit transaction
				$user_oid     = isset($userData['_id']->{'$id'}) ? $userData['_id']->{'$id'} : (string) $userData['_id'];
				$refundAmount = (float) ($orderData['total_price'] ?? 0);
				
				if ($refundAmount > 0) {
					$refundparam = array(
						'load_balance_id'        => (int) $this->common_model->getNextSequence('loadBalance'),
						'order_oid'              => new MongoDB\BSON\ObjectID($orderData['_id']->{'$id'}),
						'user_oid'              => new MongoDB\BSON\ObjectID($user_oid),
						'product_oid'            => new MongoDB\BSON\ObjectID((string)$orderData['products_oid']),
						'user_id_cred'           => (int) $userData['users_id'],
						'user_id_deb'            => (int) 0,
						'order_id'               => $orderData['order_id'],
						'upoints'                => (float) $refundAmount,
						'availableArabianPoints' => (float) ($userData['availableArabianPoints'] ?? 0),
						'end_balance'            => (float) ($userData['availableArabianPoints'] ?? 0) + (float) $refundAmount,
						'record_type'            => 'Credit',
						'narration'              => 'Hourly Game Order Cancelled',
						'remarks'                => 'Order ID: ' .$orderData['order_id'],
						'creation_ip'            => $this->input->ip_address(),
						'created_at'             => date('Y-m-d H:i:s'),
						'created_by'             => (int) $this->session->userdata('UW_ADMIN_ID'),
						'status'                 => 'A',
					);
					$update3 = $this->common_model->addData('uw_loadBalance', $refundparam);
					// $update2 = $this->mongodb_client->insertDocument('loadBalance', $refundparam, $session);

					$refundparam = (float)($userData['availableArabianPoints']+$refundAmount);

					$wherecon22['where'] = array('order_oid'=> new MongoDB\BSON\ObjectID($orderId)  , 'narration'  =>  "Hourly Game Commission"); 
					$commission        = $this->common_model->getData('single','uw_loadBalance',$wherecon22 );

					if(!empty($commission)):
						$commissionAmount = $commission['upoints'];
						$commissionParam  = array(
							'load_balance_id'        => (int) $this->common_model->getNextSequence('loadBalance'),
							'order_oid'              => new MongoDB\BSON\ObjectID($orderData['_id']->{'$id'}),
							'user_oid'              => new MongoDB\BSON\ObjectID($user_oid),
						    'product_oid'            => new MongoDB\BSON\ObjectID((string)$orderData['products_oid']),
							'user_id_cred'           => (int)0,
							'user_id_deb'            => (int)$userData['users_id'],
							'order_id'               => $orderData['order_id'],
							'upoints'                => (float) $commissionAmount,
							'availableArabianPoints' => (float) $userData['availableArabianPoints']+$refundAmount ,
							'end_balance'            => (float) ($userData['availableArabianPoints']+$refundAmount) - (float)$commissionAmount,
							'record_type'            => 'Debit',
							'narration'              => 'Hourly Game Commission Cancelled',
							'remarks'                => 'Order ID: ' .$orderData['order_id'],
							'creation_ip'            => $this->input->ip_address(),
							'created_at'             => date('Y-m-d H:i:s'),
							'created_by'             => (int) $this->session->userdata('UW_ADMIN_ID'),
							'status'                 => 'A',
						);
						$update4     = $this->common_model->addData('uw_loadBalance', $commissionParam);
						// $update3     = $this->mongodb_client->insertDocument('loadBalance', $commissionParam, $session);
						$refundparam = (float)($userData['availableArabianPoints']+$refundAmount)- (float)$commissionAmount;
					endif;
					
					// Update user's availableArabianPoints
					$paramUser      = array('availableArabianPoints' => $refundparam);
					$update5        = $this->common_model->editData('uw_users', $paramUser, '_id', new MongoDB\BSON\ObjectID($user_oid));
					// $creditWhereCon = array('_id' =>  new MongoDB\BSON\ObjectID($user_oid) );
					// $update4        = $this->mongodb_client->updateDocument('users', $creditWhereCon, ['$set' => $paramUser], $session);
				}

				// Commit transaction
				// $session->commitTransaction();
				// $this->mongodb_client->commitTransaction($session);
				$this->session->set_flashdata('alert_success', 'Hourly Game order cancelled successfully. Refund processed.');
				redirect(correctLink('ALLHOURLYORDERDATA', getCurrentControllerPath('index')));
			} else {
				// For A (Active) or I (Inactive) - simple status update without refund
				$param = array(
					'status'      => $statusType,
					'update_date' => (int) $this->timezone->utc_time(),
					'updated_by'  => (int) $this->session->userdata('UW_ADMIN_ID'),
				);
				$this->common_model->editData('uw_hourly_orders', $param, 'order_id', $orderId);

				$this->session->set_flashdata('alert_success', 'Order status updated successfully.');
				redirect(correctLink('ALLHOURLYORDERDATA', getCurrentControllerPath('index')));
			}
		} catch (Exception $e) {
			$this->session->set_flashdata('alert_error', $e->getMessage());
			redirect(correctLink('ALLHOURLYORDERDATA', getCurrentControllerPath('index')));
		}
	}

	 
	 /***********************************************************************
	** Function name 	: exportexcel
	** Developed By 	: Dilip halder
	** Purpose  		: This function used for export order data
	** Date 			: 13 March 2026
	************************************************************************/
	function exportexcel()
	{	
		$this->admin_model->authCheck('view_data');
		$data['error'] 			 = '';
		$data['activeMenu'] 	 = 'hourlygame';
		$data['activeSubMenu'] 	 = 'allhourlyorders';
		
		//Generating Logs
	    $this->common_model->generateLogs();

		if ($this->input->post('fromDate') !== null && $this->input->post('fromDate') !== '') {
			$fromDate = date('Y-m-d H:i:00', strtotime($this->input->post('fromDate')));
		} else {
			$fromDate = date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' 00:00:00'));
		}
		if ($this->input->post('toDate') !== null && $this->input->post('toDate') !== '') {
			$toDate = date('Y-m-d H:i:59', strtotime($this->input->post('toDate')));
		} else {
			$toDate = date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' 23:59:59'));
		}
		$searchField = $this->input->post('searchField');
		$searchValue = $this->input->post('searchValue');
		$drawTimeSearch = $this->input->post('drawTimeSearch');
		if ($searchField === 'draw_time_string' && $searchValue !== '' && $searchValue !== null && ($drawTimeSearch === '' || $drawTimeSearch === null)) {
			$drawTimeSearch = $searchValue;
		}
		$whereCondition = array('where' => array(
			'created_at' => array(
				'$gte' => (int) strtotime($fromDate),
				'$lte' => (int) strtotime($toDate),
			),
		));
		if (!empty($searchField) && $searchValue !== '' && $searchValue !== null && $searchField !== 'draw_time_string') {
			if ($searchField === 'order_id' || $searchField === 'users.store_name') {
				$whereCondition['where'][$searchField] = array('$regex' => $searchValue, '$options' => 'i');
			} elseif ($searchField == 'winning_status') {
				$whereCondition['where']['winning_status'] = strtolower($searchValue);
			} elseif ($searchField == 'users_mobile') {
				$whereConUser['where']['users_mobile'] = (int) $searchValue;
				$userData = $this->common_model->getData('single', 'uw_users', $whereConUser);
				if (!empty($userData)) {
					$whereCondition['where']['users_oid'] = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
				}
			} elseif ($searchField == 'users_email') {
				$whereConUser['where']['users_email'] = $searchValue;
				$userData = $this->common_model->getData('single', 'uw_users', $whereConUser);
				if (!empty($userData)) {
					$whereCondition['where']['users_oid'] = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
				} else {
					$whereCondition['where']['users_oid'] = 'default';
				}
			} elseif ($searchField == 'pos_number') {
				$whereConUser['where']['pos_number'] = (int) $searchValue;
				$userData = $this->common_model->getData('single', 'uw_users', $whereConUser);
				if (!empty($userData)) {
					$whereCondition['where']['users_oid'] = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
				} else {
					$whereCondition['where']['users_oid'] = 'default';
				}
			} elseif ($searchField == 'settler_pos_number') {
				$whereConUser['where']['pos_number'] = is_numeric($searchValue) ? (int) $searchValue : $searchValue;
				$userData = $this->common_model->getData('single', 'uw_users', $whereConUser);
				if (!empty($userData)) {
					$whereCondition['where']['settler_users_oid'] = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
				} else {
					$whereCondition['where']['settler_users_oid'] = 'default';
				}
			} elseif ($searchField == 'settler_mobile') {
				$whereConUser['where']['users_mobile'] = (int) $searchValue;
				$userData = $this->common_model->getData('single', 'uw_users', $whereConUser);
				if (!empty($userData)) {
					$whereCondition['where']['settler_users_oid'] = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
				} else {
					$whereCondition['where']['settler_users_oid'] = 'default';
				}
			} else {
				$whereCondition['where'][$searchField] = is_numeric($searchValue) ? (int) $searchValue : $searchValue;
			}
		}

		if (!empty($drawTimeSearch)) {
			$this->applyDrawTimeWhereCondition($whereCondition, $drawTimeSearch);
		}

		$tblName   = 'uw_hourly_orders';
		$totalRows = (int) $this->common_model->getHourlyGameOrderData('count', $tblName, $whereCondition);
		$itemsPerPage = 5000;
		// At least one "page" so the export view always runs one AJAX batch (empty [] if no rows).
		$totalPages = $totalRows > 0 ? max(1, (int) ceil($totalRows / $itemsPerPage)) : 1;

		$data['current_page']   = 1;
		$data['total_page'] 	= $totalPages;
		$data['searchField'] 	= $searchField;
		$data['searchValue'] 	= $searchValue;
		$data['drawTimeSearch'] = $drawTimeSearch;
		$data['fromDate'] 		= $fromDate;
		$data['toDate'] 		= $toDate;
		$data['cancelled_order'] = '';
		$postedExportType = $this->input->post('exportType');
		if (is_array($postedExportType) && !empty($postedExportType[0])) {
			$data['exportType'] = strtolower(trim((string) $postedExportType[0]));
		} else {
			$data['exportType'] = 'draw';
		}
		$data['includeDrawTime'] = ($this->input->post('includeDrawTime') == '1') ? '1' : '';
		$this->layouts->set_title('Export CSV | Hourly Game Orders | UWINN');
		$this->layouts->admin_view('hourlygame/allhourlyorders/exportexcel',array(),$data);
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name 	: exportexcelApi
	** Developed By 	: Dilip halder
	** Purpose  		: This function used for export order data
	** Date 			: 23 July 2024
	************************************************************************/
	function exportexcelApi(){
		
		$this->admin_model->authCheck('view_data');
		$data['error'] 		   = '';
		$data['activeMenu']    = 'hourlygame';
		$data['activeSubMenu'] = 'allhourlyorders';

		if ($this->input->post('fromDate') !== null && $this->input->post('fromDate') !== '') {
			$fromDate = date('Y-m-d H:i:00', strtotime($this->input->post('fromDate')));
		} else {
			$fromDate = date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' 00:00:00'));
		}
		if ($this->input->post('toDate') !== null && $this->input->post('toDate') !== '') {
			$toDate = date('Y-m-d H:i:59', strtotime($this->input->post('toDate')));
		} else {
			$toDate = date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' 23:59:59'));
		}
		$searchField = $this->input->post('searchField');
		$searchValue = $this->input->post('searchValue');
		$drawTimeSearch = $this->input->post('drawTimeSearch');
		if ($searchField === 'draw_time_string' && $searchValue !== '' && $searchValue !== null && ($drawTimeSearch === '' || $drawTimeSearch === null)) {
			$drawTimeSearch = $searchValue;
		}

		$whereCondition = array('where' => array(
			'created_at' => array(
				'$gte' => (int) strtotime($fromDate),
				'$lte' => (int) strtotime($toDate),
			),
		));

		if (!empty($searchField) && $searchValue !== '' && $searchValue !== null && $searchField !== 'draw_time_string') {
			if ($searchField === 'order_id' || $searchField === 'users.store_name') {
				$whereCondition['where'][$searchField] = array('$regex' => $searchValue, '$options' => 'i');
			} elseif ($searchField == 'winning_status') {
				$whereCondition['where']['winning_status'] = strtolower($searchValue);
			} elseif ($searchField == 'users_mobile') {
				$whereConUser['where']['users_mobile'] = (int) $searchValue;
				$userData = $this->common_model->getData('single', 'uw_users', $whereConUser);
				if (!empty($userData)) {
					$whereCondition['where']['users_oid'] = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
				}
			} elseif ($searchField == 'users_email') {
				$whereConUser['where']['users_email'] = $searchValue;
				$userData = $this->common_model->getData('single', 'uw_users', $whereConUser);
				if (!empty($userData)) {
					$whereCondition['where']['users_oid'] = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
				} else {
					$whereCondition['where']['users_oid'] = 'default';
				}
			} elseif ($searchField == 'pos_number') {
				$whereConUser['where']['pos_number'] = (int) $searchValue;
				$userData = $this->common_model->getData('single', 'uw_users', $whereConUser);
				if (!empty($userData)) {
					$whereCondition['where']['users_oid'] = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
				} else {
					$whereCondition['where']['users_oid'] = 'default';
				}
			} elseif ($searchField == 'settler_pos_number') {
				$whereConUser['where']['pos_number'] = is_numeric($searchValue) ? (int) $searchValue : $searchValue;
				$userData = $this->common_model->getData('single', 'uw_users', $whereConUser);
				if (!empty($userData)) {
					$whereCondition['where']['settler_users_oid'] = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
				} else {
					$whereCondition['where']['settler_users_oid'] = 'default';
				}
			} elseif ($searchField == 'settler_mobile') {
				$whereConUser['where']['users_mobile'] = (int) $searchValue;
				$userData = $this->common_model->getData('single', 'uw_users', $whereConUser);
				if (!empty($userData)) {
					$whereCondition['where']['settler_users_oid'] = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
				} else {
					$whereCondition['where']['settler_users_oid'] = 'default';
				}
			} else {
				$whereCondition['where'][$searchField] = is_numeric($searchValue) ? (int) $searchValue : $searchValue;
			}
		}

		if (!empty($drawTimeSearch)) {
			$this->applyDrawTimeWhereCondition($whereCondition, $drawTimeSearch);
		}

		$page = (int) $this->input->post('pageno');
		if ($page < 1) {
			$page = 1;
		}
 		$itemsPerPage = 5000;
 		$startIndex  = ($page - 1) * $itemsPerPage;
 		$shortField  = array('created_at' => -1);
 		$resultType  = 'multiple';
		$tblName     = "uw_hourly_orders";

		$exportType = strtolower(trim((string) $this->input->post('exportType')));
		if (!in_array($exportType, array('accounts', 'winners', 'draw'), true)) {
			$exportType = 'draw';
		}
		$includeDrawTime = ($this->input->post('includeDrawTime') == '1');
		if ($exportType === 'draw') {
			$whereCondition['where']['status'] = array('$nin' => array('CL', 'Cancelled'));
		} elseif ($exportType === 'winners') {
			$whereCondition['where']['is_winner'] = 'Y';
		}
		$OrderData   = $this->common_model->getHourlyGameOrderData($resultType, $tblName, $whereCondition, $shortField, $itemsPerPage, $startIndex);
		

		$CSVData = array();
		if (is_array($OrderData)) {
			foreach ($OrderData as $index => $itemsArray) {
				$status = 'N/A';
				if (isset($itemsArray['status'])) {
					if ($itemsArray['status'] === 'CL') {
						$status = 'Cancelled';
					} elseif ($itemsArray['status'] === 'A') {
						$status = 'Success';
					} elseif ($itemsArray['status'] === 'Redeemed') {
						$status = 'Redeemed';
					}
				}

				$baseRow = array();
				$baseRow['POS No.']       = !empty($itemsArray['seller_pos_number']) ? $itemsArray['seller_pos_number'] : 'N/A';
				$baseRow['Order ID']      = !empty($itemsArray['order_id']) ? $itemsArray['order_id'] : 'N/A';
				$baseRow['Product Name']  = !empty($itemsArray['products_name']) ? $itemsArray['products_name'] : 'N/A';
				$baseRow['Store Name']    = !empty($itemsArray['seller_store_name']) ? $itemsArray['seller_store_name'] : 'N/A';
				$baseRow['Seller Name']   = !empty($itemsArray['seller_users_name']) ? $itemsArray['seller_users_name'] : 'N/A';
				$baseRow['Seller Mobile'] = !empty($itemsArray['seller_users_mobile']) ? $itemsArray['seller_users_mobile'] : 'N/A';
				$baseRow['Bind With']     = !empty($itemsArray['seller_users_bind_person_name']) ? $itemsArray['seller_users_bind_person_name'] : 'N/A';
				$baseRow['Total Amount']    = !empty($itemsArray['total_price']) ? (float) $itemsArray['total_price'] : 0.00;
				$baseRow['Payment Status']  = $status;
				$purchaseDate = !empty($itemsArray['created_at']) ? date('d-m-Y H:i', $itemsArray['created_at']) : 'N/A';

				$drawDateTime = 'N/A';
				if (!empty($itemsArray['draw_time'])) {
					$drawTimeValue = $itemsArray['draw_time'];
					$drawDateTime = is_numeric($drawTimeValue)
						? date('d-m-Y H:i', $drawTimeValue)
						: date('d-m-Y H:i', strtotime($drawTimeValue));
				}

				if ($exportType === 'draw' && isset($itemsArray['status']) && in_array($itemsArray['status'], array('CL', 'Cancelled'), true)) {
					continue;
				}

				$ticketRows = array();
				if (!empty($itemsArray['tickets']) && is_array($itemsArray['tickets'])) {
					foreach ($itemsArray['tickets'] as $ticketRow) {
						$ticketValue = 'N/A';
						$pointsValue = 'N/A';
						$couponTypeValue = 'N/A';
						$normalizedCouponType = '';

						$ticketValue     = preg_replace('/\s+/', '', (string) $ticketRow->ticket);
						$couponTypeValue = strtolower(trim((string) $ticketRow->type));

						$csvRow = $baseRow;
						$csvRow['Straight Amount'] = '0';
						$csvRow['Rumble Amount']   = '0';
						$csvRow['Chance Amount']   = '0';
						if ($couponTypeValue === 'straight') {
							$csvRow['Straight Amount'] = '1';
						} elseif ($couponTypeValue === 'rumble') {
							$csvRow['Rumble Amount'] = '1';
						} elseif ($couponTypeValue === 'chance') {
							$csvRow['Chance Amount'] = '1';
						}

						if ($exportType === 'draw') {
							unset($csvRow['Total Amount']);
							$paymentStatusValue = isset($csvRow['Payment Status']) ? $csvRow['Payment Status'] : 'N/A';
							unset($csvRow['Payment Status']);
							$csvRow['Payment Status'] = $paymentStatusValue;
							$csvRow['Purchase Date'] = $purchaseDate;
							if ($includeDrawTime) {
								$csvRow['Draw Date Time'] = $drawDateTime;
							}
							$csvRow['Coupons'] = $ticketValue;
							$ticketRows[] = $csvRow;
						}
					}
				}

				if ($exportType === 'accounts') {
					$accountRow = $baseRow;
					$paymentStatusValue = isset($accountRow['Payment Status']) ? $accountRow['Payment Status'] : 'N/A';
					$totalAmountValue = isset($accountRow['Total Amount']) ? (float) $accountRow['Total Amount'] : 0.00;
					unset($accountRow['Total Amount']);
					unset($accountRow['Payment Status']);
					$accountRow['Payment Status'] = $paymentStatusValue;
					$accountRow['Purchase Date'] = $purchaseDate;
					if ($includeDrawTime) {
						$accountRow['Draw Date Time'] = $drawDateTime;
					}
					$accountRow['Total Amount'] = (float) $totalAmountValue;
					$CSVData[] = $accountRow;
				} elseif ($exportType === 'winners') {
					$winnerRow = $baseRow;
					$redeemingDate = 'N/A';
					if (!empty($itemsArray['redeemed_at'])) {
						$redeemingDate = date('d-m-Y H:i', (int)$itemsArray['redeemed_at']);
					}
					$winnerRow['Winner Type']      = !empty($itemsArray['winner_type']) ? $itemsArray['winner_type'] : 'N/A';
					$winnerRow['Winning Amount']   = !empty($itemsArray['winning_amount']) ? (float) $itemsArray['winning_amount'] : 0.00;
					$winnerRow['Winning Status']   = !empty($itemsArray['winning_status']) ? $itemsArray['winning_status'] : 'unpaid';
					$winnerRow['Redeemed By']      = !empty($itemsArray['settler_full_name']) ? $itemsArray['settler_full_name'] : 'N/A';
					$winnerRow['Redeemed POS ID']  = !empty($itemsArray['settler_pos_number']) ? $itemsArray['settler_pos_number'] : 'N/A';
					$winnerRow['Redeemed Date']    = !empty($redeemingDate) ? $redeemingDate : 'N/A';
					$winnerRow['Purchase Date']    = $purchaseDate;
					if ($includeDrawTime) {
						$winnerRow['Draw Date Time'] = $drawDateTime;
					}
					$winnerRow['Area']    = !empty($itemsArray['area']) ? $itemsArray['area'] : 'N/A';;
					$CSVData[] = $winnerRow;
				} elseif (!empty($ticketRows)) {
					$CSVData = array_merge($CSVData, $ticketRows);
				}
			}
		}

		$this->output->set_content_type('application/json');
		echo json_encode($CSVData);
		die();
	}

	private function formatLottoTicketCoupons($orderRow = array())
	{
		if (empty($orderRow['tickets']) || !is_array($orderRow['tickets'])) {
			return 'N/A';
		}

		$coupons = array();
		foreach ($orderRow['tickets'] as $ticketRow) {
			$ticketValue = '';
			if (is_object($ticketRow) && isset($ticketRow->ticket)) {
				$ticketValue = (string) $ticketRow->ticket;
			} elseif (is_array($ticketRow) && isset($ticketRow['ticket'])) {
				$ticketValue = (string) $ticketRow['ticket'];
			}

			$ticketValue = trim($ticketValue);
			if ($ticketValue !== '') {
				$coupons[] = preg_replace('/\s+/', '', $ticketValue);
			}
		}

		if (empty($coupons)) {
			return 'N/A';
		}

		return implode(' | ', $coupons);
	}

	private function currentAdminShouldSeeCoupons()
	{
		if ($this->session->userdata('ADMIN_TYPE') === 'Super Admin') {
			// return false;
			return 'Y';
		}
		$adminId = (int) $this->session->userdata('ADMIN_ID');
		if ($adminId < 1) {
			return true;
		}
		$row = $this->common_model->getDataByParticularField('admin', 'admin_id', $adminId);
		if (empty($row)) {
			return true;
		}
		if (is_object($row)) {
			$row = json_decode(json_encode($row), true);
		}
		if (!is_array($row)) {
			return true;
		}
		if(isset($row['permission_show_coupons']) && $row['permission_show_coupons'] === 'Y'){
			return 'Y';
		} else {
			return 'N';
		}
	}

	/***********************************************************************
	** Function name 	: sendsms
	** Developed By 	: Dilip halder
	** Purpose  		: This function used for send sms to user.
	** Date 			: 06 May 2026
	************************************************************************/
	public function sendsms($orderId='')
	{	
		$this->admin_model->authCheck('view_data');
		$data['error'] 		   = '';
		$data['activeMenu']    = 'hourlygame';
		$data['activeSubMenu'] = 'allhourlyorders';

		if($orderId):
			$this->admin_model->authCheck('edit_data');
			$whereCondition['where'] = array('_id' => new MongoDB\BSON\ObjectId($orderId));
			$data['orderData'] = $this->common_model->getHourlyGameOrderData('single', 'uw_hourly_orders', $whereCondition);
			$data['orderData'] = $data['orderData'][0];
			// echo "<pre>";print_r($data['orderData']);die();
		else:
			if (empty($data['orderData'])) {
				$this->session->set_flashdata('alert_error', 'Order not found.');
				redirect(correctLink('ALLHOURLYORDERSDATA', getCurrentControllerPath('index')));
			}
		endif;

		if($this->input->post('SaveChanges')):
			$error = 'NO';
			$this->form_validation->set_rules('gateway', 'Gateway', 'trim|required');
			$gateway  = $this->input->post('gateway');
			if($this->form_validation->run() && $error == 'NO'): 
				
				$buyerMobile      = $data['orderData']['buyer_mobile']?? "";
				$buyerEmail       = $data['orderData']['buyer_email']?? "";
				$buyerCountryCode = $data['orderData']['buyer_country_code']?? "";
				$senderDetails['gateway'] = $gateway;
				if(!empty($buyerMobile) && !empty($buyerCountryCode) || !empty($buyerEmail)):
					$senderDetails['user_type']    = 'Buyer';
					$senderDetails['country_code'] = $buyerCountryCode;
					$senderDetails['users_mobile'] = $buyerMobile;
					$senderDetails['users_email']  = $buyerEmail;
				else:
					$senderDetails['user_type']    = $userDetails['seller_users_type'];
					$senderDetails['country_code'] = $userDetails['seller_users_country_code'];
					$senderDetails['users_mobile'] = $userDetails['seller_users_mobile'];
					$senderDetails['users_email']  = $userDetails['seller_users_email'];
				endif;
				 
				// echo "<pre>";print_r($data['orderData']);die();
				 
				$message = "";
				if(!empty($data['orderData']['tickets'])):
					
					$ticketLIST      = $data['orderData']['tickets'];
					$ORDERID         = $data['orderData']['order_id'];
					$CAMPAIGNAME     = $data['orderData']['product_name'];
					$LINK            = 'https://tktinvoice.com/uwin-download-invoice/'.$ORDERID;	

					$output        = [];
					$CouponDetails = '';
					$map = ['S', 'R', 'C'];
					foreach ($ticketLIST as $key => $tickectitem) {
						// Ticket numbers
						$line = $tickectitem->ticket;
						$line .= ' (';
						if($tickectitem->type == "straight"):
							$line .= 'S';
						elseif($tickectitem->type == "rumble"):
							$line .= 'R';
						elseif($tickectitem->type == "chance"):
							$line .= 'C';
						endif;
						$line .= ')';
							
						$output[] = $line;
					}

					$CouponDetails = implode('. ', $output);
					$drawDate = date('d.m.Y h:iA', $data['orderData']['draw_time']);
					$message  = 'Order ID '.$ORDERID.' of '.$CAMPAIGNAME.' with coupons '.$CouponDetails.' Ddate '.$drawDate.' You can download the invoice here '.$LINK;
					$senderDetails['message'] = $message;
				endif;

				if( !empty($message) && !empty($senderDetails['country_code']) && !empty($senderDetails['users_mobile']) && 
				    (
						$senderDetails['gateway'] == 'smscountry'   || 
						$senderDetails['gateway'] == 'digitizebird' ||
						$senderDetails['gateway'] == 'ndm'   
					)
				):
					$result = $this->sms_model->sendSMS($senderDetails);
				elseif( !empty($message) && !empty($senderDetails['country_code']) && !empty($senderDetails['users_mobile']) && $senderDetails['gateway'] == 'whatsapp' ):
					$senderDetails['ORDERID']       = $ORDERID;
					$senderDetails['CAMPAIGNAME']   = $CAMPAIGNAME;
					$senderDetails['CouponDetails'] = $CouponDetails;
					$senderDetails['DDATE']         = $drawDate;
					$senderDetails['LINK']          = $LINK;
					$result = $this->sms_model->sendWhatsAppMessage($senderDetails);
				elseif(!empty($message) && !empty($buyerEmail) && $senderDetails['gateway'] == 'email'):
					$subject= "Order Confirmation";
					$result = $this->email_model->sendEmail($buyerEmail,$subject,$message);
				else:
					$result = false;
				endif;
				$result = json_decode($result, true);
				if($result['status'] == "Success"):
					$this->session->set_flashdata('alert_success', 'Order SMS sent successfully.');
					redirect(correctLink('ALLORDERSDATA', getCurrentControllerPath('index')));
				else:
					$error = 'Failed to send order SMS. '.$result['error'];
					$this->session->set_flashdata('alert_error',$error);
					redirect(correctLink('ALLORDERSDATA', getCurrentControllerPath('index')));
				endif;
			else:
				$this->session->set_flashdata('alert_error', validation_errors());
				redirect(correctLink('ALLORDERSDATA', getCurrentControllerPath('index')));
			endif;
		endif;

		$drawId = isset($data['orderData']['draw_id']) ? $data['orderData']['draw_id'] : 0;
		$data['drawDetails'] = array('draw_date' => '', 'draw_time' => '');
		if ($drawId) {
			$serchfields = array('draw_date', 'draw_time', 'products_id');
			$wcon['where'] = array('draw_id' => (int)$drawId);
			$data['drawDetails'] = $this->common_model->getParticularFieldByMultipleCondition($serchfields, 'uw_products_draw_records', $wcon);
			if (!is_array($data['drawDetails'])) {
				$data['drawDetails'] = array('draw_date' => '', 'draw_time' => '');
			}
		}

		$data['gateways'] = array();
		$enableSMS = $this->common_model->getData('single', 'uw_enablesms');
		 
		$data['gateways']['smscountry']   = 'SMS Country';
		$data['gateways']['digitizebird'] = 'Digitizebird';
		$data['gateways']['ndm'] 		  = 'NDM';
		$data['gateways']['whatsapp']     = 'WhatsApp';
		$data['gateways']['email']        = 'Email';

		unset($data['gateways'][$enableSMS['default_sms']]);
		$this->layouts->set_title('Send Order SMS | UWINN');
		$this->layouts->admin_view('hourlygame/allhourlyorders/sendsms', array(), $data);
	}

	private function applyDrawTimeWhereCondition(&$whereCon, $drawTimeSearch)
	{
		if ($drawTimeSearch === '' || $drawTimeSearch === null) {
			return;
		}

		$drawTimeTs = strtotime($drawTimeSearch);
		if ($drawTimeTs === false || $drawTimeTs <= 0) {
			return;
		}

		$hourStart = (int) strtotime(date('Y-m-d H:00:00', $drawTimeTs));
		$hourEnd = (int) strtotime(date('Y-m-d H:59:59', $drawTimeTs));
		$dateHourPrefix = date('Y-m-d H', $drawTimeTs);

		$drawTimeFilter = array(
			'$or' => array(
				array('draw_time' => array('$gte' => $hourStart, '$lte' => $hourEnd)),
				array('draw_time_string' => array('$regex' => '^' . preg_quote($dateHourPrefix, '/'))),
			),
		);

		if (isset($whereCon['where']['$and'])) {
			$whereCon['where']['$and'][] = $drawTimeFilter;
		} elseif (!empty($whereCon['where'])) {
			$existingWhere = $whereCon['where'];
			$whereCon['where'] = array('$and' => array($existingWhere, $drawTimeFilter));
		} else {
			$whereCon['where'] = $drawTimeFilter;
		}
	}

	private function buildHourlyOrderExportWhere($fromDate, $toDate, $searchField, $searchValue, $cancelled_order = '', $drawTimeSearch = '')
	{
		$whereCondition = array('where' => array(
			'created_at' => array(
				'$gte' => (int) strtotime($fromDate),
				'$lte' => (int) strtotime($toDate),
			),
		));

		if($cancelled_order != 'on'):
			$whereCondition['where']['status'] = array('$nin' => array('CL', 'Cancelled'));
		endif;

		if(!empty($searchField) && $searchValue !== '' && $searchValue !== null && $searchField !== 'draw_time_string'):
			if($searchField === 'order_id' || $searchField === 'users.store_name'):
				$whereCondition['where'][$searchField] = array('$regex' => $searchValue, '$options' => 'i');
			elseif($searchField == 'winning_status'):
				$whereCondition['where']['winning_status'] = strtolower($searchValue);
			elseif($searchField == 'users_mobile'):
				$whereConUser['where']['users_mobile'] = (int) $searchValue;
				$userData = $this->common_model->getData('single', 'uw_users', $whereConUser);
				if(!empty($userData)):
					$whereCondition['where']['users_oid'] = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
				endif;
			elseif($searchField == 'users_email'):
				$whereConUser['where']['users_email'] = $searchValue;
				$userData = $this->common_model->getData('single', 'uw_users', $whereConUser);
				if(!empty($userData)):
					$whereCondition['where']['users_oid'] = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
				else:
					$whereCondition['where']['users_oid'] = 'default';
				endif;
			elseif($searchField == 'pos_number'):
				$whereConUser['where']['pos_number'] = (int) $searchValue;
				$userData = $this->common_model->getData('single', 'uw_users', $whereConUser);
				if(!empty($userData)):
					$whereCondition['where']['users_oid'] = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
				else:
					$whereCondition['where']['users_oid'] = 'default';
				endif;
			elseif($searchField == 'settler_pos_number'):
				$whereConUser['where']['pos_number'] = is_numeric($searchValue) ? (int) $searchValue : $searchValue;
				$userData = $this->common_model->getData('single', 'uw_users', $whereConUser);
				if(!empty($userData)):
					$whereCondition['where']['settler_users_oid'] = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
				else:
					$whereCondition['where']['settler_users_oid'] = 'default';
				endif;
			elseif($searchField == 'settler_mobile'):
				$whereConUser['where']['users_mobile'] = (int) $searchValue;
				$userData = $this->common_model->getData('single', 'uw_users', $whereConUser);
				if(!empty($userData)):
					$whereCondition['where']['settler_users_oid'] = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
				else:
					$whereCondition['where']['settler_users_oid'] = 'default';
				endif;
			else:
				$whereCondition['where'][$searchField] = is_numeric($searchValue) ? (int) $searchValue : $searchValue;
			endif;
		endif;

		if ($searchField === 'draw_time_string' && $searchValue !== '' && $searchValue !== null && ($drawTimeSearch === '' || $drawTimeSearch === null)):
			$drawTimeSearch = $searchValue;
		endif;

		if (!empty($drawTimeSearch)):
			$this->applyDrawTimeWhereCondition($whereCondition, $drawTimeSearch);
		endif;

		return $whereCondition;
	}

	private function getHourlyShopExportFilterData($useJsonGameIds = false)
	{
		if($this->input->post('fromDate') !== null && $this->input->post('fromDate') !== ''):
			$fromDate = date('Y-m-d H:i:00', strtotime($this->input->post('fromDate')));
		else:
			$fromDate = date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' 00:00:00'));
		endif;
		if($this->input->post('toDate') !== null && $this->input->post('toDate') !== ''):
			$toDate = date('Y-m-d H:i:59', strtotime($this->input->post('toDate')));
		else:
			$toDate = date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' 23:59:59'));
		endif;

		if($useJsonGameIds):
			$rawGameIds = $this->input->post('gameIds');
			if(is_array($rawGameIds)):
				$gameIds = $rawGameIds;
			elseif(is_string($rawGameIds) && $rawGameIds !== ''):
				$decodedGameIds = json_decode($rawGameIds, true);
				$gameIds = is_array($decodedGameIds) ? $decodedGameIds : array();
			else:
				$gameIds = array();
			endif;
		else:
			$gameIds = isset($_POST['gameIds']) ? (array)$_POST['gameIds'] : array();
		endif;
		$gameIds = is_array($gameIds) ? $gameIds : array();
		$gameIds = array_values(array_filter($gameIds, function($id) {
			return $id !== '' && $id !== null;
		}));
		$gameIds = array_map(function($id) {
			return ($id <= PHP_INT_MAX) ? (int)$id : $id;
		}, $gameIds);

		$searchField = $this->input->post('searchField');
		$searchValue = $this->input->post('searchValue');
		$cancelled_order = $this->input->post('cancelled_order');
		$drawTimeSearch = $this->input->post('drawTimeSearch');
		$whereCondition = $this->buildHourlyOrderExportWhere($fromDate, $toDate, $searchField, $searchValue, $cancelled_order, $drawTimeSearch);

		$hourlyGames = $this->common_model->getData('multiple', 'uw_hourly_games', array('where' => array('status' => 'A')), array('title' => 1));
		if(!empty($gameIds)):
			$hourlyGames = array_values(array_filter($hourlyGames, function($game) use ($gameIds) {
				return in_array((int)$game['products_id'], $gameIds, true);
			}));
		endif;

		$hourlyGameTitles = array();
		$hourlyGameTitleMap = array();
		foreach($hourlyGames as $hourlyGame):
			$title = trim((string)($hourlyGame['title'] ?? ''));
			if($title === ''):
				continue;
			endif;
			$hourlyGameTitles[] = $title;
			$hourlyGameTitleMap[strtoupper($title)] = $title;
		endforeach;
		usort($hourlyGameTitles, 'strnatcasecmp');

		if(!empty($gameIds) && !empty($hourlyGameTitles)):
			$whereCondition['where']['products_name']['$in'] = $hourlyGameTitles;
		endif;

		return compact(
			'fromDate', 'toDate', 'whereCondition', 'gameIds', 'searchField', 'searchValue',
			'cancelled_order', 'hourlyGameTitles', 'hourlyGameTitleMap'
		);
	}

	private function isHourlyShopExportAdminSeller($itemsArray)
	{
		$sellerName = strcasecmp(trim($this->hourlyShopExportScalar($itemsArray['seller_users_name'] ?? '')), 'Admin') === 0;
		$sellerType = strcasecmp(trim((string)($itemsArray['seller_users_type'] ?? '')), 'Admin') === 0;
		$pos = $this->hourlyShopExportScalar($itemsArray['seller_pos_number'] ?? '');
		if($pos === ''):
			$pos = $this->hourlyShopExportScalar($itemsArray['seller_pos_device_id'] ?? '');
		endif;
		$csPos = $pos !== '' && preg_match('/^CS\d+/i', $pos) === 1;
		return $sellerName || $sellerType || $csPos;
	}

	private function shouldSkipHourlyShopExportOrder($itemsArray)
	{
		if(($itemsArray['seller_users_type'] ?? '') !== 'Users'):
			return false;
		endif;
		return !$this->isHourlyShopExportAdminSeller($itemsArray);
	}

	private function hourlyShopExportScalar($value)
	{
		if($value === null || $value === ''):
			return '';
		endif;
		if(is_array($value)):
			if(isset($value['$oid'])):
				return trim((string)$value['$oid']);
			endif;
			if(isset($value['$numberLong'])):
				return trim((string)$value['$numberLong']);
			endif;
			if(isset($value['$numberInt'])):
				return trim((string)$value['$numberInt']);
			endif;
			return '';
		endif;
		if(is_object($value)):
			if($value instanceof \MongoDB\BSON\ObjectId):
				return trim((string)$value);
			endif;
			if(isset($value->{'$id'})):
				return trim((string)$value->{'$id'});
			endif;
			if(method_exists($value, '__toString')):
				return trim((string)$value);
			endif;
			return '';
		endif;
		return trim((string)$value);
	}

	private function getHourlyShopExportUserByOid($usersOid)
	{
		$oidString = $this->hourlyShopExportScalar($usersOid);
		if($oidString === ''):
			return array();
		endif;
		$cacheKey = 'oid|'.$oidString;
		if(!array_key_exists($cacheKey, $this->hourlyShopExportUserCache)):
			$retailer = false;
			try {
				$retailer = $this->common_model->getDataByParticularField('uw_users', '_id', new MongoDB\BSON\ObjectID($oidString));
			} catch (\Throwable $th) {
				$retailer = false;
			}
			$this->hourlyShopExportUserCache[$cacheKey] = $retailer ? $retailer : array();
		endif;
		return $this->hourlyShopExportUserCache[$cacheKey];
	}

	private function getHourlyShopExportUserByUsersId($usersId)
	{
		$usersId = $this->hourlyShopExportScalar($usersId);
		if($usersId === ''):
			return array();
		endif;
		$cacheKey = 'uid|'.$usersId;
		if(!array_key_exists($cacheKey, $this->hourlyShopExportUserCache)):
			$retailer = false;
			if(is_numeric($usersId) && (float)$usersId <= PHP_INT_MAX):
				$retailer = $this->common_model->getDataByParticularField('uw_users', 'users_id', (int)$usersId);
			endif;
			if(empty($retailer)):
				$retailer = $this->common_model->getDataByParticularField('uw_users', 'users_id', $usersId);
			endif;
			$this->hourlyShopExportUserCache[$cacheKey] = $retailer ? $retailer : array();
		endif;
		return $this->hourlyShopExportUserCache[$cacheKey];
	}

	private function getHourlyOrderSellerUser($itemsArray)
	{
		$retailer = $this->getHourlyShopExportUserByOid($itemsArray['users_oid'] ?? '');
		if(!empty($retailer)):
			return $retailer;
		endif;
		return $this->getHourlyShopExportUserByUsersId($itemsArray['users_id'] ?? '');
	}

	private function fetchShopExportHourlyOrders(array $whereCondition, $startIndex, $itemsPerPage)
	{
		$orders = $this->common_model->getHourlyGameOrderData(
			'multiple', 'uw_hourly_orders', $whereCondition,
			array('created_at' => -1), $itemsPerPage, $startIndex
		);
		return is_array($orders) ? $orders : array();
	}

	private function resolveHourlyShopRetailerInfo($itemsArray)
	{
		$pos = !empty($itemsArray['seller_pos_number'])
			? $this->hourlyShopExportScalar($itemsArray['seller_pos_number']) : '';
		if($pos === '' && !empty($itemsArray['seller_users_pos_number'])):
			$pos = $this->hourlyShopExportScalar($itemsArray['seller_users_pos_number']);
		endif;
		if($pos === '' && !empty($itemsArray['seller_pos_device_id'])):
			$pos = $this->hourlyShopExportScalar($itemsArray['seller_pos_device_id']);
		endif;

		$storeName = !empty($itemsArray['seller_store_name'])
			? $this->hourlyShopExportScalar($itemsArray['seller_store_name']) : '';
		$region = !empty($itemsArray['area'])
			? $this->hourlyShopExportScalar($itemsArray['area']) : '';
		$supervisor = !empty($itemsArray['seller_users_bind_person_name'])
			? $this->hourlyShopExportScalar($itemsArray['seller_users_bind_person_name'])
			: (!empty($itemsArray['bind_person_name']) ? $this->hourlyShopExportScalar($itemsArray['bind_person_name']) : '');

		$retailer = $this->getHourlyOrderSellerUser($itemsArray);
		if(!empty($retailer)):
			if($pos === '' && !empty($retailer['pos_number'])):
				$pos = $this->hourlyShopExportScalar($retailer['pos_number']);
			endif;
			if($pos === '' && !empty($retailer['pos_device_id'])):
				$pos = $this->hourlyShopExportScalar($retailer['pos_device_id']);
			endif;
			if($storeName === '' && !empty($retailer['store_name'])):
				$storeName = $this->hourlyShopExportScalar($retailer['store_name']);
			endif;
			if($region === '' && !empty($retailer['area'])):
				$region = $this->hourlyShopExportScalar($retailer['area']);
			endif;
			if($supervisor === '' && !empty($retailer['bind_person_name'])):
				$supervisor = $this->hourlyShopExportScalar($retailer['bind_person_name']);
			endif;
		endif;

		if(($storeName === '' || $region === '' || $supervisor === '') && $pos !== '' && $pos !== 'N/A' && is_numeric($pos)):
			$posKey = (string)$pos;
			if(!array_key_exists($posKey, $this->hourlyShopExportPosCache)):
				$posRetailer = $this->common_model->getDataByParticularField('uw_users', 'pos_number', (int)$pos);
				if(empty($posRetailer)):
					$posRetailer = $this->common_model->getDataByParticularField('uw_users', 'pos_number', $posKey);
				endif;
				$this->hourlyShopExportPosCache[$posKey] = $posRetailer ? $posRetailer : array();
			endif;
			$posRetailer = $this->hourlyShopExportPosCache[$posKey];
			if(!empty($posRetailer)):
				if($storeName === '' && !empty($posRetailer['store_name'])):
					$storeName = $this->hourlyShopExportScalar($posRetailer['store_name']);
				endif;
				if($region === '' && !empty($posRetailer['area'])):
					$region = $this->hourlyShopExportScalar($posRetailer['area']);
				endif;
				if($supervisor === '' && !empty($posRetailer['bind_person_name'])):
					$supervisor = $this->hourlyShopExportScalar($posRetailer['bind_person_name']);
				endif;
			endif;
		endif;

		if($storeName === '' && $this->isHourlyShopExportAdminSeller($itemsArray)):
			$sellerName = trim($this->hourlyShopExportScalar($itemsArray['seller_users_name'] ?? ''));
			$storeName = $sellerName !== '' ? $sellerName : 'Admin';
		endif;

		return array(
			'pos' => ($pos !== '' && $pos !== 'N/A') ? $pos : 'N/A',
			'shop_name' => $storeName !== '' ? $storeName : 'N/A',
			'region' => $region,
			'supervisor' => $supervisor,
		);
	}

	private function aggregateHourlyShopExportRows($hourlyOrderData, array $hourlyGameTitles, array $hourlyGameTitleMap)
	{
		$shopData = array();

		if(empty($hourlyOrderData) || empty($hourlyGameTitles)):
			return $shopData;
		endif;

		foreach($hourlyOrderData as $itemsArray):
			if($this->shouldSkipHourlyShopExportOrder($itemsArray)):
				continue;
			endif;

			$shopInfo = $this->resolveHourlyShopRetailerInfo($itemsArray);
			$pos = $shopInfo['pos'];
			$shopName = $shopInfo['shop_name'];
			$region = $shopInfo['region'];
			$supervisor = $shopInfo['supervisor'];
			$shopKey = $pos.'|'.$shopName.'|'.$region.'|'.$supervisor;

			if(!isset($shopData[$shopKey])):
				$shopData[$shopKey] = array(
					'shopKey' => $shopKey,
					'POS' => $pos,
					'SHOP NAME' => $shopName,
					'Region' => $region,
					'Supervisor' => $supervisor,
				);
				foreach($hourlyGameTitles as $title):
					$shopData[$shopKey][$title] = 0;
				endforeach;
			endif;

			$productName = trim((string)($itemsArray['products_name'] ?? $itemsArray['product_name'] ?? ''));
			$qty = (int)($itemsArray['qty'] ?? 0);
			if($productName === '' || $qty <= 0):
				continue;
			endif;

			$matchedTitle = '';
			$upperName = strtoupper($productName);
			if(isset($hourlyGameTitleMap[$upperName])):
				$matchedTitle = $hourlyGameTitleMap[$upperName];
			else:
				foreach($hourlyGameTitles as $title):
					if(strcasecmp($title, $productName) === 0):
						$matchedTitle = $title;
						break;
					endif;
				endforeach;
			endif;

			if($matchedTitle !== ''):
				$shopData[$shopKey][$matchedTitle] += $qty;
			endif;
		endforeach;

		return array_values($shopData);
	}

	public function exportshopexcel()
	{
		$this->admin_model->authCheck('view_data');
		$this->common_model->generateLogs();

		$filterData = $this->getHourlyShopExportFilterData(false);
		extract($filterData);

		$itemsPerPage = 5000;
		$totalRows = (int)$this->common_model->getHourlyGameOrderData('count', 'uw_hourly_orders', $whereCondition);
		$totalPages = $totalRows > 0 ? max(1, (int)ceil($totalRows / $itemsPerPage)) : 1;

		$data['error'] = '';
		$data['activeMenu'] = 'hourlygame';
		$data['activeSubMenu'] = 'allhourlyorders';
		$data['current_page'] = 1;
		$data['total_page'] = $totalPages;
		$data['searchField'] = $searchField;
		$data['searchValue'] = $searchValue;
		$data['fromDate'] = $fromDate;
		$data['toDate'] = $toDate;
		$data['cancelled_order'] = $cancelled_order;
		$data['gameIds'] = json_encode($gameIds);
		$data['hourlyGameTitles'] = $hourlyGameTitles;

		$this->layouts->set_title('Export Hourly Shop Excel | UWINN');
		$this->layouts->admin_view('hourlygame/allhourlyorders/exportshopexcel', array(), $data);
	}

	public function exportshopexcelApi()
	{
		$this->admin_model->authCheck('view_data');

		header('Content-Type: application/json');
		try {
			$this->hourlyShopExportUserCache = array();
			$this->hourlyShopExportPosCache = array();
			$filterData = $this->getHourlyShopExportFilterData(true);
			extract($filterData);

			$page = (int)$this->input->post('pageno');
			if($page < 1):
				$page = 1;
			endif;
			$itemsPerPage = 5000;
			$startIndex = ($page - 1) * $itemsPerPage;

			$hourlyOrderData = $this->fetchShopExportHourlyOrders($whereCondition, $startIndex, $itemsPerPage);
			$shopRows = $this->aggregateHourlyShopExportRows($hourlyOrderData, $hourlyGameTitles, $hourlyGameTitleMap);
			echo json_encode($shopRows);
		} catch (\Throwable $th) {
			header('HTTP/1.1 500 Internal Server Error');
			echo json_encode(array('error' => $th->getMessage()));
		}
		exit;
	}
}

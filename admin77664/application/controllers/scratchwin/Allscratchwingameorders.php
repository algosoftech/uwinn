<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Allscratchwingameorders extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		error_reporting(E_ALL ^ E_NOTICE);
		$this->load->model(array('admin_model', 'emailtemplate_model', 'sms_model', 'notification_model'));
		$this->lang->load('statictext', 'admin');
		$this->load->helper('common');
	}

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name  : index
	 + + Developed By   : Dilip Halder
	 + + Purpose        : List Scratch Win orders
	 + + Date           : 23 July 2026
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index()
	{
		$this->admin_model->authCheck('view_data');
		$data['error']         = '';
		$data['activeMenu']    = 'scratchwin';
		$data['activeSubMenu'] = 'allscratchwingameorders';

		$fromDate    = $this->resolveFromDate($this->input->get('fromDate'));
		$toDate      = $this->resolveToDate($this->input->get('toDate'));
		$searchField = $this->input->get('searchField');
		$searchValue = $this->input->get('searchValue');
		$whereCon    = $this->buildOrderWhereCon($fromDate, $toDate, $searchField, $searchValue);

		$data['searchField'] = $searchField;
		$data['searchValue'] = $searchValue;
		$data['fromDate']    = $fromDate;
		$data['toDate']      = $toDate;

		$shortField = array('created_at' => -1);
		$baseUrl    = getCurrentControllerPath('index');
		$tblName    = 'uw_scratch_win_orders';

		$this->session->set_userdata('ALLSCRATCHWINGAMEORDERSDATA', currentFullUrl());
		$qStringdata = explode('?', currentFullUrl());
		$suffix      = isset($qStringdata[1]) && $qStringdata[1] ? '?' . $qStringdata[1] : '';
		$totalRows   = $this->common_model->getData('count', $tblName, $whereCon, $shortField, '0', '0');

		if ($this->input->get('showLength') == 'All') {
			$perPage         = $totalRows;
			$data['perpage'] = $this->input->get('showLength');
		} elseif ($this->input->get('showLength')) {
			$perPage         = $this->input->get('showLength');
			$data['perpage'] = $this->input->get('showLength');
		} else {
			$perPage         = SHOW_NO_OF_DATA;
			$data['perpage'] = SHOW_NO_OF_DATA;
		}

		$uriSegment         = getUrlSegment();
		$data['PAGINATION'] = adminPagination($baseUrl, $suffix, $totalRows, $perPage, $uriSegment);

		if ($this->uri->segment(getUrlSegment())) {
			$page = $this->uri->segment(getUrlSegment());
		} else {
			$page = 0;
		}

		$data['forAction'] = $baseUrl;
		if ($totalRows) {
			$first         = (int) ($page) + 1;
			$data['first'] = $first;
			$pageData      = ($data['perpage'] == 'All') ? $totalRows : $data['perpage'];
			$last          = ((int) ($page) + $pageData) > $totalRows ? $totalRows : ((int) ($page) + $pageData);
			$data['noOfContent'] = 'Showing ' . $first . '-' . $last . ' of ' . $totalRows . ' items';
		} else {
			$data['first']       = 1;
			$data['noOfContent'] = '';
		}

		$data['ALLDATA'] = $this->common_model->getData('multiple', $tblName, $whereCon, $shortField, $perPage, $page);
		$this->layouts->set_title('Scratch Win Orders | UWINN');
		$this->layouts->admin_view('scratchwin/allscratchwingameorders/index', array(), $data);
	}

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name  : addeditdata
	 + + Developed By   : Dilip Halder
	 + + Purpose        : View Scratch Win order details
	 + + Date           : 23 July 2026
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function addeditdata($orderId = '')
	{
		$this->admin_model->authCheck('view_data');
		$data['error']         = '';
		$data['activeMenu']    = 'scratchwin';
		$data['activeSubMenu'] = 'allscratchwingameorders';

		if (empty($orderId)) {
			$this->session->set_flashdata('alert_error', 'Order ID required.');
			redirect(getCurrentControllerPath('index'));
			return;
		}

		$orderData = $this->common_model->getDataByParticularField('uw_scratch_win_orders', '_id', new MongoDB\BSON\ObjectID($orderId));
		if (empty($orderData)) {
			$this->session->set_flashdata('alert_error', 'Order not found.');
			redirect(correctLink('ALLSCRATCHWINGAMEORDERSDATA', getCurrentControllerPath('index')));
			return;
		}

		$data['order'] = $orderData;
		if (!empty($orderData['users_id'])) {
			$data['userData'] = $this->common_model->getDataByParticularField('uw_users', 'users_id', (int) $orderData['users_id']);
		} else {
			$data['userData'] = array();
		}

		$this->layouts->set_title('Scratch Win Order Details | UWINN');
		$this->layouts->admin_view('scratchwin/allscratchwingameorders/addeditdata', array(), $data);
	}

	/***********************************************************************
	** Function name 	: exportexcel
	** Developed By 	: Dilip Halder
	** Purpose  		: Export Scratch Win orders
	** Date 			: 23 July 2026
	************************************************************************/
	public function exportexcel()
	{
		$this->admin_model->authCheck('view_data');
		$this->common_model->generateLogs();

		$fromDate    = $this->resolveFromDate($this->input->post('fromDate'));
		$toDate      = $this->resolveToDate($this->input->post('toDate'));
		$searchField = $this->input->post('searchField');
		$searchValue = $this->input->post('searchValue');
		$whereCon    = $this->buildOrderWhereCon($fromDate, $toDate, $searchField, $searchValue);
		$orderData   = $this->common_model->getData('multiple', 'uw_scratch_win_orders', $whereCon, array('created_at' => -1), 0, 0);

		$filename = 'Scratch-Win-Orders-' . date('d-m-Y-H-i-s') . '.csv';
		while (ob_get_level() > 0) {
			ob_end_clean();
		}
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Pragma: no-cache');
		header('Expires: 0');

		$fp = fopen('php://output', 'w');
		fprintf($fp, chr(0xEF) . chr(0xBB) . chr(0xBF));
		fputcsv($fp, array(
			'S.No', 'Order ID', 'Txn ID', 'Game Name', 'Game Mode', 'Qty', 'Total Price',
			'User ID', 'Buyer Mobile', 'Buyer Email', 'SMS Type', 'Winning Status',
			'Winning Type', 'Winning Amount', 'Status', 'Created Date',
		));

		$slno = 1;
		if (is_array($orderData)) {
			foreach ($orderData as $row) {
				fputcsv($fp, array(
					$slno++,
					!empty($row['order_id']) ? $row['order_id'] : 'N/A',
					!empty($row['txn_id']) ? $row['txn_id'] : 'N/A',
					!empty($row['products_name']) ? $row['products_name'] : 'N/A',
					!empty($row['game_mode']) ? $row['game_mode'] : 'N/A',
					(int) ($row['qty'] ?? 0),
					(float) ($row['total_price'] ?? 0),
					!empty($row['users_id']) ? $row['users_id'] : 'N/A',
					trim(($row['buyer_country_code'] ?? '') . ' ' . ($row['buyer_mobile'] ?? '')),
					!empty($row['buyer_email']) ? $row['buyer_email'] : 'N/A',
					!empty($row['sms_type']) ? $row['sms_type'] : 'N/A',
					!empty($row['winning_status']) ? $row['winning_status'] : 'N',
					!empty($row['winning_type']) ? $row['winning_type'] : 'N/A',
					(float) ($row['winning_amount'] ?? 0),
					!empty($row['status']) ? $row['status'] : 'N/A',
					!empty($row['created_at']) ? date('d-m-Y H:i:s', $row['created_at']) : 'N/A',
				));
			}
		}
		fclose($fp);
		exit;
	}

	private function resolveFromDate($fromDateInput)
	{
		if ($fromDateInput !== null && $fromDateInput !== '') {
			return date('Y-m-d H:i:00', strtotime($fromDateInput));
		}
		return date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' 00:00:00'));
	}

	private function resolveToDate($toDateInput)
	{
		if ($toDateInput !== null && $toDateInput !== '') {
			return date('Y-m-d H:i:59', strtotime($toDateInput));
		}
		return date('Y-m-d H:i:s', strtotime(date('Y-m-d') . ' 23:59:59'));
	}

	private function buildOrderWhereCon($fromDate, $toDate, $searchField, $searchValue)
	{
		$whereCon = array('where' => array(
			'created_at' => array(
				'$gte' => (int) strtotime($fromDate),
				'$lte' => (int) strtotime($toDate),
			),
		));

		if (!empty($searchField) && $searchValue !== '' && $searchValue !== null) {
			if ($searchField === 'order_id' || $searchField === 'products_name' || $searchField === 'buyer_email' || $searchField === 'txn_id') {
				$whereCon['where'][$searchField] = array('$regex' => trim($searchValue), '$options' => 'i');
			} elseif ($searchField === 'buyer_mobile' || $searchField === 'users_id') {
				$whereCon['where'][$searchField] = is_numeric($searchValue) ? (int) $searchValue : $searchValue;
			} elseif ($searchField === 'winning_status') {
				$whereCon['where']['winning_status'] = strtoupper(trim($searchValue));
			} elseif ($searchField === 'game_mode') {
				$whereCon['where']['game_mode'] = trim($searchValue);
			} else {
				$whereCon['where'][$searchField] = is_numeric($searchValue) ? (int) $searchValue : trim($searchValue);
			}
		}

		return $whereCon;
	}
}

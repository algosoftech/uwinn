<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CommonReportSummary extends CI_Controller {
	
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
	 * * Function name : getUnifiedSummary
	 * * Developed By  : Dilip Halder
     * * Clubbed By    : Sumit Bedwal
	 * * Purpose  	   : This function used to getUnifiedSummary
	 * * Date 		   : 02 June 2026
	 * * **********************************************************************/
    public function getUnifiedSummary()
    {
        // print_r($this->input->post());die;
        $apiHeaderData = getApiHeaderData();
        $this->generatelogs->putLog('APP', logOutPut($_POST));
        $result = array();

        try {
            if (!requestAuthenticate(APIKEY, 'POST')):
                throw new Exception(lang('FORBIDDEN_MSG'), 1);
            endif;

            // ── Common inputs ──────────────────────────────────────────────
            $usersId      = $this->input->post('users_id') ?: $this->input->post('user_id');
            $startDate    = $this->input->post('start_date') ?: $this->input->post('from');
            $endDate      = $this->input->post('end_date')   ?: $this->input->post('to');
            $productTitle = (string)($this->input->post('product_title') ?? '');

            if (empty($usersId)):
                throw new Exception(lang('USER_ID_EMPTY'), 1);
            elseif (empty($startDate)):
                throw new Exception(lang('EMPTY_START_DATE'), 1);
            elseif (empty($endDate)):
                throw new Exception(lang('EMPTY_END_DATE'), 1);
            endif;

            // ── Validate user ──────────────────────────────────────────────
            $whereCon['where']['users_id'] = (int)$usersId;
            $userData = $this->common_model->getData('single', 'uw_users', $whereCon);

            if (empty($userData)):
                throw new Exception(lang('USER_NOT_FOUND'), 1);
            elseif ($userData['status'] != 'A'):
                throw new Exception(lang('ACCOUNT_BLOCKED'), 1);
            endif;

            $userOid   = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
            $user_oid  = $userData['_id']->{'$id'};   // plain string form kept for legacy helpers

            // ── 1. Tambola / ReportSummary ─────────────────────────────────
            $tambolaWhere = array();
            $tambolaWhere['where']['users_oid']   = $userOid;
            $tambolaWhere['where']['created_at']  = array(
                '$gte' => strtotime($startDate),
                '$lte' => strtotime($endDate)
            );
            $result['tambola_summary'] = $this->_getTambolaReportSummary($tambolaWhere);

            // ── 2. Lotto / newSummaryReportSearch ──────────────────────────
            $result['lotto_summary'] = $this->_getLottoSummary(
                $usersId, $user_oid, $startDate, $endDate, $productTitle
            );

            // ── 3. Hourly / getHourlyReportSummary ────────────────────────
            $fmtStart    = date('Y-m-d H:i:00', strtotime($startDate));
            $fmtEnd      = date('Y-m-d H:i:59', strtotime($endDate));
            $ledgerStart = date('Y-m-d H:i', strtotime($startDate));
            $ledgerEnd   = date('Y-m-d H:i', strtotime($endDate));
            $result['hourly_summary'] = $this->getHourlyReportSummary(
                $userData,
                strtotime($fmtStart),
                strtotime($fmtEnd),
                $ledgerStart,
                $ledgerEnd,
                $productTitle
            );

            // ── 4. Raffle / summeryReportRaffle ───────────────────────────
            $result['raffle_summary'] = $this->_getRaffleSummary(
                $userData, $userOid, $startDate, $endDate, $productTitle
            );

            // ── 5. Cash-voucher / newCashVoucherSummary ───────────────────
            $userData['from'] = $startDate;
            $userData['to']   = $endDate;
            $result['cash_voucher_summary'] = $this->common_model->getCashSummery2($userData);

            // ── Common date range echo ─────────────────────────────────────
            $result['date_range'] = array(
                'start_date' => $startDate,
                'end_date'   => $endDate
            );

            echo outPut(1, lang('SUCCESS_CODE'), lang('SUCCESS_ACTION'), $result);

        } catch (Exception $e) {
            echo outPut(0, lang('SUCCESS_CODE'), $e->getMessage(), $result);
        }
    }


    // ═══════════════════════════════════════════════════════════════════════════════
    // Private helpers (thin wrappers around your existing logic)
    // ═══════════════════════════════════════════════════════════════════════════════

    /**
     * Tambola summary — extracted from your first ReportSummary / getReportSummary pair.
     */
    private function _getTambolaReportSummary(array $mainWhereCon): array
    {
        // Delegates directly to the already-existing private method.
        // If getReportSummary() is defined in this controller, just call it.
        return $this->getReportSummary($mainWhereCon);
    }

    /**
     * Lotto summary — logic from newSummaryReportSearch(), now parameter-driven.
     */
    private function _getLottoSummary(
        int    $usersId,
        string $user_oid,
        string $startDate,
        string $endDate,
        string $productTitle
    ): array {
        $result = array();

        $start = date('Y-m-d H:i', strtotime($startDate));
        $end   = date('Y-m-d H:i', strtotime($endDate));

        $where = array();
        $where['created_at'] = array('$gte' => $start, '$lte' => $end);

        if (!empty($productTitle)):
            $where['product_title'] = $productTitle;
        endif;

        $where['user_id']     = (int)$usersId;
        $where['status']      = 'A';
        $where['raffle_mode'] = array('$ne' => 'Y');
        $wcon['where']        = $where;

        $shortField          = array('product_id' => -1);
        $totalProductDetails = $this->common_model->getsummeryReport(
            'multiple', 'uw_lotto_orders', $wcon, $shortField, $num_page, $cnt
        );

        $totalSales              = $this->common_model->totalSales($user_oid, $where);
        $commissionAmount        = $this->common_model->commissionList($user_oid, $where);
        $totalcancelOrderAmount  = $this->common_model->cancelOraderList($user_oid, $where);
        $totalCustomerPaid       = $this->common_model->totalCustomerPaid($user_oid, $where);
        $dueBalance              = (float)$totalSales - $commissionAmount - $totalCustomerPaid;

        $ProductListArray = array();
        if (!empty($totalProductDetails)):
            foreach ($totalProductDetails as $key => $items):
                $wcon['where']['product_id'] = $items['product_id'];
                $wcon['where']['status']     = array('$ne' => '');
                $OrderArrayList = $this->common_model->getFieldInArray(
                    'order_id', 'uw_lotto_orders', $wcon
                );

                $where['where_in']['order_id']                       = $OrderArrayList;
                $totalProductDetails[$key]['commissionAmount']        = $this->common_model->commissionList($user_oid, $where);
                $totalProductDetails[$key]['totalcancelOrderAmount']  = $this->common_model->cancelOraderList($user_oid, $where);

                $where['product_id']                                 = (int)$items['product_id'];
                $totalProductDetails[$key]['totalCustomerPaid']       = $this->common_model->totalCustomerPaid($user_oid, $where);

                $ProductListArray[] = $items['product_id'];
            endforeach;
        endif;

        $RedeemedPrizeList = $this->common_model->RedeemedPrizeList($user_oid, $where);

        // Cancelled orders not yet in product list
        $wcon22['where']['created_at'] = $wcon['where']['created_at'];
        $wcon22['where']['user_id']    = $wcon['where']['user_id'];
        $wcon22['where']['status']     = 'CL';
        $wcon22['where']['raffle_mode']= array('$ne' => 'Y');
        $cancelOraderList = $this->common_model->CancellationPIDs($user_oid, $wcon22);

        if (!empty($cancelOraderList)):
            foreach ($cancelOraderList as $ProductID1):
                if (!in_array($ProductID1, $ProductListArray)):
                    $RFields    = array('products_id', 'title', 'straight_add_on_amount', 'product_image');
                    $whereCon2['where'] = array(
                        'products_id'          => (int)$ProductID1,
                        'enable_raffle_ticket' => array('$ne' => 'Enable')
                    );
                    $result123 = $this->common_model->getParticularFieldByMultipleCondition(
                        $RFields, 'uw_products', $whereCon2
                    );

                    $wcon['where']['product_id'] = $ProductID1;
                    $wcon['where']['status']     = array('$ne' => '');
                    $OrderArrayList = $this->common_model->getFieldInArray(
                        'order_id', 'uw_lotto_orders', $wcon
                    );

                    $row = array(
                        '_id'                    => $result123['title'],
                        'price'                  => $result123['straight_add_on_amount'],
                        'sales_count'            => 0,
                        'sales'                  => 0,
                        'product_image'          => $result123['product_image'],
                        'product_id'             => $result123['products_id'],
                        'commissionAmount'       => 0,
                    );
                    $where333['where_in']['order_id'] = $OrderArrayList;
                    $where333['created_at']           = $where['created_at'];
                    $where333['user_id']              = $where['user_id'];
                    $where333['status']               = $where['status'];

                    $row['totalcancelOrderAmount'] = $this->common_model->cancelOraderList($user_oid, $where333);
                    $where['product_id']           = (int)$ProductID1;
                    $row['totalCustomerPaid']      = $this->common_model->totalCustomerPaid($user_oid, $where);

                    $totalProductDetails[] = $row;
                endif;
            endforeach;
        endif;

        // Redeemed prizes not yet in product list
        if (!empty($RedeemedPrizeList)):
            foreach ($RedeemedPrizeList as $ProductID):
                if (!in_array($ProductID, $ProductListArray) && !in_array($ProductID, (array)$cancelOraderList)):
                    $RFields2   = array('products_id', 'title', 'straight_add_on_amount', 'product_image');
                    $whereCon3['where'] = array('products_id' => (int)$ProductID);
                    $resultsss  = $this->common_model->getParticularFieldByMultipleCondition(
                        $RFields2, 'uw_products', $whereCon3
                    );

                    $wcon['where']['product_id'] = $ProductID;
                    $wcon['where']['status']     = array('$ne' => '');
                    $OrderArrayList = $this->common_model->getFieldInArray(
                        'order_id', 'uw_lotto_orders', $wcon
                    );

                    $row2 = array(
                        '_id'                    => $resultsss['title'],
                        'price'                  => $resultsss['straight_add_on_amount'],
                        'sales_count'            => 0,
                        'sales'                  => 0,
                        'product_image'          => $resultsss['product_image'],
                        'product_id'             => $resultsss['products_id'],
                        'commissionAmount'       => 0,
                    );
                    $where333['where_in']['order_id'] = $OrderArrayList;
                    $where333['created_at']           = $where['created_at'];
                    $where333['user_id']              = $where['user_id'];
                    $where333['status']               = $where['status'];

                    $row2['totalcancelOrderAmount'] = !empty($OrderArrayList)
                        ? $this->common_model->cancelOraderList($user_oid, $where333)
                        : 0;

                    $where['product_id']        = (int)$ProductID;
                    $row2['totalCustomerPaid']  = $this->common_model->totalCustomerPaid($user_oid, $where);

                    $totalProductDetails[] = $row2;
                endif;
            endforeach;
        endif;

		$totalProductDetails = array_values(array_unique($totalProductDetails, SORT_REGULAR));
        if (empty($productTitle)):
            $result['totalSales']              = (string)$totalSales;
            $result['commissionAmount']        = (string)$commissionAmount;
            $result['totalcancelOrderAmount']  = (string)$totalcancelOrderAmount;
            $result['totalCustomerPaid']       = (string)$totalCustomerPaid;
            $result['dueBalance']             = (string)$dueBalance;
            $result['totalProductDetails']    = $totalProductDetails;
        else:
            $matched = array();
            foreach ($totalProductDetails as $Item):
                if ($productTitle == $Item['_id']):
                    $matched = $Item;
                    break;
                endif;
            endforeach;
            $dueBalance = (float)($matched['sales'] ?? 0)
                        - (float)($matched['commissionAmount'] ?? 0)
                        - (float)($matched['totalCustomerPaid'] ?? 0);

            $result['totalSales']             = (string)($matched['sales'] ?? 0);
            $result['commissionAmount']       = (string)($matched['commissionAmount'] ?? 0);
            $result['totalcancelOrderAmount'] = (string)($matched['totalcancelOrderAmount'] ?? 0);
            $result['totalCustomerPaid']      = (string)($matched['totalCustomerPaid'] ?? 0);
            $result['dueBalance']            = (string)$dueBalance;
            $result['totalProductDetails']   = $matched;
        endif;

        return $result;
    }

    /**
     * Raffle summary — logic from summeryReportRaffle(), now parameter-driven.
     */
    private function _getRaffleSummary(
        array  $userData,
        object $userOid,
        string $startDate,
        string $endDate,
        string $productTitle
    ): array {
        $userData['from'] = $startDate;
        $userData['to']   = $endDate;

        $whereCondition['user_oid'] = $userOid;
        if (!empty($startDate)):
            $whereCondition['created_at']['$gte'] = date('Y-m-d H:i', strtotime($startDate));
        endif;
        if (!empty($endDate)):
            $whereCondition['created_at']['$lte'] = date('Y-m-d H:i', strtotime($endDate));
        endif;

        $RaffleCampaign = $this->common_model->getRaffleSummary($whereCondition, $productTitle);

        $RaffleSummery  = array();
        $RaffleSummery1 = array();

        if (!empty($RaffleCampaign)):
            foreach ($RaffleCampaign as $items):
                $RaffleSummery['total_raffle_orders']              += $items['sales'];
                $RaffleSummery['total_raffle_commisson']           += $items['commissionAmount'];
                $RaffleSummery['total_raffle_orders_canceled']     += $items['totalcancelOrderAmount'];
                $RaffleSummery['total_raffle_cancelled_commisson'] += $items['commissionCanceledAmount'];
                $RaffleSummery['total_raffle_prize_redeemed']      += $items['totalCustomerPaid'];
            endforeach;
            $RaffleSummery['total_due'] =
                $RaffleSummery['total_raffle_orders']
                - $RaffleSummery['total_raffle_commisson']
                - $RaffleSummery['total_raffle_prize_redeemed'];
            $RaffleSummery1[] = $RaffleSummery;
        endif;

        return array(
            'RaffleCampaign' => $RaffleCampaign  ?? [],
            'RaffleSummery'  => $RaffleSummery1
        );
    }

    // Tambola summary private function
    private function getReportSummary($mainWhereCon=array())
	{
		$result = array();
		$tblName = 'uw_tambola_orders';
		
		if(empty($mainWhereCon) || !isset($mainWhereCon['where'])):
			return $result;
		endif;
		
		$whereCondition = $mainWhereCon['where'];
		
		// Step 1: Group by status for counts and totals using getAggregateData
		$groupBy = array(
			'_id' => '$status',
			'status' => array('$first' => '$status'),
			'order_count' => array('$sum' => 1),
			'total_price_sum' => array('$sum' => '$total_price')
		);
		
		$sortBy = array('_id' => 1);
		$SelectFields = array();
		$lookup = array();
		$unwind = array();
		$resultType = '';
		$page = '';
		$per_page = '';
		
		$countData = $this->common_model->getAggregateData( $tblName, $SelectFields, $whereCondition, $groupBy, $sortBy, $lookup, $unwind, $resultType, $page, $per_page );

		// echo "<pre>";
		// print_r($countData);
		// die();
		
		
		// First unwind winning_numbers, then group by status and sum prizes
		$prizeUnwind = array('$winning_numbers');
		
		$prizeGroupBy = array(
			'_id' => array(
				'status' => '$status',
				'order_id' => '$order_id'
			),
			'status' => array('$first' => '$status'),
			'order_id' => array('$first' => '$order_id'),
			'order_prize_sum' => array(
				'$sum' => array(
					'$cond' => array(
						array('$ne' => array('$winning_numbers', null)),
						array('$ifNull' => array('$winning_numbers.prize', 0)),
						0
					)
				)
			)
		);
		
		// Second group to combine orders by status
		// Note: getAggregateData only supports one group, so we'll do a second aggregation
		// For now, process the first group result and then group by status in PHP
		$prizeSelectFields = array(
			'order_id' => 1,
			'status' => 1,
			'winning_numbers' => 1
		);
		
		$prizeSortBy = array();
		$prizeLookup = array();
		$prizeResultType = '';
		$prizePage = '';
		$prizePerPage = '';
		
		$prizeDataStep1 = $this->common_model->getAggregateData(
			$tblName,
			$prizeSelectFields,
			$whereCondition,
			$prizeGroupBy,
			$prizeSortBy,
			$prizeLookup,
			$prizeUnwind,
			$prizeResultType,
			$prizePage,
			$prizePerPage
		);
		
		// Group by status in PHP since getAggregateData only supports one $group stage
		$prizeData = array();
		if(!empty($prizeDataStep1)):
			foreach($prizeDataStep1 as $item):
				$status = isset($item['status']) ? $item['status'] : 'UNKNOWN';
				$orderId = isset($item['order_id']) ? $item['order_id'] : '';
				$orderPrize = isset($item['order_prize_sum']) ? (float)$item['order_prize_sum'] : 0;
				
				if($orderPrize > 0):
					if(!isset($prizeData[$status])):
						$prizeData[$status] = array(
							'status' => $status,
							'total_prize_amount' => 0,
							'orders' => array()
						);
					endif;
					
					$prizeData[$status]['total_prize_amount'] += $orderPrize;
					$prizeData[$status]['orders'][] = array(
						'order_id' => $orderId,
						'prize_amount' => $orderPrize
					);
				endif;
			endforeach;
			$prizeData = array_values($prizeData);
		endif;
		
		// Process grouped data
		$statusGroups = array();
		$totalOrdersCount = 0;
		$totalPrizeAmount = 0;
		
		// Initialize status groups from count data
		if(!empty($countData)):
			foreach($countData as $group):
				$status = isset($group['status']) ? $group['status'] : 'UNKNOWN';
				$orderCount = isset($group['order_count']) ? (int)$group['order_count'] : 0;
				$totalPriceSum = isset($group['total_price_sum']) ? (float)$group['total_price_sum'] : 0;
				
				$statusGroups[$status] = array(
					'status' => $status,
					'order_count' => $orderCount,
					'total_price_sum' => $totalPriceSum,
					'total_prize_amount' => 0,
					'prize_breakdown' => array()
				);
				
				$totalOrdersCount += $orderCount;
			endforeach;
		endif;
		
		// Add prize data to status groups
		if(!empty($prizeData)):
			foreach($prizeData as $prizeGroup):
				$status = isset($prizeGroup['status']) ? $prizeGroup['status'] : 'UNKNOWN';
				$prizeAmount = isset($prizeGroup['total_prize_amount']) ? (float)$prizeGroup['total_prize_amount'] : 0;
				$orders = isset($prizeGroup['orders']) ? $prizeGroup['orders'] : array();
				
				if(isset($statusGroups[$status])):
					$statusGroups[$status]['total_prize_amount'] = $prizeAmount;
					$statusGroups[$status]['prize_breakdown'] = $orders;
				else:
					$statusGroups[$status] = array(
						'status' => $status,
						'order_count' => 0,
						'total_price_sum' => 0,
						'total_prize_amount' => $prizeAmount,
						'prize_breakdown' => $orders
					);
				endif;
				
				$totalPrizeAmount += $prizeAmount;
			endforeach;
		endif;
		
		// Prepare result with grouped data
		$result['total_orders'] = $totalOrdersCount;
		$result['total_prize_amount'] = (float)$totalPrizeAmount;
		$result['status_groups'] = $statusGroups;
		
		// Add individual status counts for easy access
		$result['cancelled_orders'] = isset($statusGroups['CL']) ? $statusGroups['CL']['order_count'] : 0;
		$result['redeemed_orders'] = isset($statusGroups['REDEEMED']) ? $statusGroups['REDEEMED']['order_count'] : 0;
		$result['active_orders'] = isset($statusGroups['A']) ? $statusGroups['A']['order_count'] : 0;
		
		// Combine all prize breakdowns
		$allPrizeBreakdown = array();
		foreach($statusGroups as $statusGroup):
			if(!empty($statusGroup['prize_breakdown'])):
				$allPrizeBreakdown = array_merge($allPrizeBreakdown, $statusGroup['prize_breakdown']);
			endif;
		endforeach;
		$result['prize_breakdown'] = $allPrizeBreakdown;
		
		return $result;
	}

    // Hourly summary private function
    private function getHourlyReportSummary($userData=array(), $startTs=0, $endTs=0, $startDate='', $endDate='', $productTitle='')
	{
		$userOid = new MongoDB\BSON\ObjectID($userData['_id']->{'$id'});
		$orders   = array();
		$whereCon = array();
		$whereCon['where']['users_oid'] = $userOid;
		$whereCon['where']['created_at'] = array('$gte' => (int)$startTs, '$lte' => (int)$endTs);
		$orderslist = $this->common_model->getData('multiple', 'uw_hourly_orders', $whereCon);
		 
		$wheresellConSettler['where']['settler_users_oid'] = $userOid;
		$wheresellConSettler['where']['redeemed_at'] = array('$gte' => (int)$startTs, '$lte' => (int)$endTs);
		$ordersettler = $this->common_model->getData('multiple', 'uw_hourly_orders', $wheresellConSettler);
		if(!empty($orderslist) && !empty($ordersettler) ):
			$orders = array_merge($orderslist, $ordersettler);
			$orders = array_unique($orders, SORT_REGULAR);
		elseif(!empty($orderslist)):
			$orders = $orderslist;
		elseif(!empty($ordersettler)):
			$orders = $ordersettler;
		endif;
		
		$productMap = array();
		$cancelledProductOrders = array();
		$allOrderIds = array();
		foreach($orders as $order):
			if(!empty($productTitle)):
				$orderProductName = isset($order['products_name']) ? (string)$order['products_name'] : '';
				if(stripos($orderProductName, $productTitle) === false):
					continue;
				endif;
			endif;

			$productKey = '';
			if(!empty($order['products_oid']) && isset($order['products_oid']->{'$id'})):
				$productKey = $order['products_oid']->{'$id'};
			else:
				$productKey = isset($order['products_name']) ? $order['products_name'] : 'N/A';
			endif;

			if(!isset($productMap[$productKey])):
				$productMap[$productKey] = array(
					'_id' => isset($order['products_name']) ? $order['products_name'] : 'N/A',
					'price' => 0,
					'sales_count' => 0,
					'totalSalesCount' => 0,
					'sales' => 0,
					'product_image' => '',
					'product_id' => isset($order['products_id']) ? (int)$order['products_id'] : 0,
					'draw_date' => '',
					'draw_time' => '',
					'commissionAmount' => 0,
					'totalcancelOrderAmount' => 0,
					'totalCustomerPaid' => 0,
					'order_ids' => array(),
					'products_oid_str' => (!empty($order['products_oid']) && isset($order['products_oid']->{'$id'})) ? (string)$order['products_oid']->{'$id'} : '',
					'products_name' => isset($order['products_name']) ? (string)$order['products_name'] : ''
				);
			endif;

			$isCancelledOrder = (isset($order['status']) && $order['status'] == 'CL');
			if($isCancelledOrder):
				if(!isset($cancelledProductOrders[$productKey])):
					$cancelledProductOrders[$productKey] = array(
						'order_ids' => array()
					);
				endif;
				if(!empty($order['order_id'])):
					$cancelledProductOrders[$productKey]['order_ids'][] = $order['order_id'];
				endif;
			else:

				
				if(
					(string)$order['users_oid'] === (string)$userOid &&
					(int)$order['created_at'] >= (int)$startTs &&
					(int)$order['created_at'] <= (int)$endTs
				):
					$productMap[$productKey]['sales_count'] += (int)($order['qty'] ?? 1);
					$productMap[$productKey]['totalSalesCount'] += 1;
					$productMap[$productKey]['sales'] += (float)($order['total_price'] ?? 0);
				endif;
				if(!empty($order['order_id'])):
					$productMap[$productKey]['order_ids'][] = $order['order_id'];
					$allOrderIds[] = $order['order_id'];
				endif;
			endif;

			if(empty($productMap[$productKey]['draw_date']) && !empty($order['expiry_date']) && is_numeric($order['expiry_date'])):
				$productMap[$productKey]['draw_date'] = date('Y-m-d', (int)$order['expiry_date']);
				$productMap[$productKey]['draw_time'] = date('H:i', (int)$order['expiry_date']);
			endif;
		endforeach;

		if(!empty($cancelledProductOrders)):
			foreach($cancelledProductOrders as $cKey => $cItem):
				if(isset($productMap[$cKey])):
					$productMap[$cKey]['order_ids'] = array_merge($productMap[$cKey]['order_ids'], $cItem['order_ids']);
				else:
					if(!empty($cItem['order_ids'])):
						$firstCancelledOrderId = $cItem['order_ids'][0];
						$cancelOrderCon['where']['order_id'] = $firstCancelledOrderId;
						$cancelOrderInfo = $this->common_model->getData('single', 'uw_hourly_orders', $cancelOrderCon);
						if(!empty($cancelOrderInfo)):
							$productMap[$cKey] = array(
								'_id' => isset($cancelOrderInfo['products_name']) ? $cancelOrderInfo['products_name'] : 'N/A',
								'price' => 0,
								'sales_count' => 0,
								'totalSalesCount' => 0,
								'sales' => 0,
								'product_image' => '',
								'product_id' => isset($cancelOrderInfo['products_id']) ? (int)$cancelOrderInfo['products_id'] : 0,
								'draw_date' => '',
								'draw_time' => '',
								'commissionAmount' => 0,
								'totalcancelOrderAmount' => 0,
								'totalCustomerPaid' => 0,
								'order_ids' => $cItem['order_ids'],
								'products_oid_str' => (!empty($cancelOrderInfo['products_oid']) && isset($cancelOrderInfo['products_oid']->{'$id'})) ? (string)$cancelOrderInfo['products_oid']->{'$id'} : '',
								'products_name' => isset($cancelOrderInfo['products_name']) ? (string)$cancelOrderInfo['products_name'] : ''
							);
							if(!empty($cancelOrderInfo['expiry_date']) && is_numeric($cancelOrderInfo['expiry_date'])):
								$productMap[$cKey]['draw_date'] = date('Y-m-d', (int)$cancelOrderInfo['expiry_date']);
								$productMap[$cKey]['draw_time'] = date('H:i', (int)$cancelOrderInfo['expiry_date']);
							endif;
						endif;
					endif;
				endif;
			endforeach;
		endif;

		$this->_mergeMissingHourlyOrdersFromLedger($userOid, $startDate, $endDate, $allOrderIds, $productMap, $startTs, $endTs);

		foreach($productMap as $key => $item):
			$gameDetails = array();
			if(!empty($item['products_oid_str']) && strlen($item['products_oid_str']) == 24):
				$gameDetails = $this->common_model->getDataByParticularField('uw_hourly_games','_id',new MongoDB\BSON\ObjectID($item['products_oid_str']));
			elseif(!empty($item['product_id'])):
				$gameDetails = $this->common_model->getDataByParticularField('uw_hourly_games','products_id',(int)$item['product_id']);
			elseif(!empty($item['products_name'])):
				$gameWhereCon['where']['title'] = $item['products_name'];
				$gameDetails = $this->common_model->getData('single','uw_hourly_games',$gameWhereCon);
			endif;

			if(!empty($gameDetails)):
				$productMap[$key]['_id'] = isset($gameDetails['title']) ? $gameDetails['title'] : $productMap[$key]['_id'];
				$productMap[$key]['price'] = isset($gameDetails['price']) ? (float)$gameDetails['price'] : $productMap[$key]['price'];
				$productMap[$key]['product_id'] = isset($gameDetails['products_id']) ? (int)$gameDetails['products_id'] : $productMap[$key]['product_id'];
				$productMap[$key]['product_image'] = isset($gameDetails['game_image']) ? $gameDetails['game_image'] : (isset($gameDetails['product_image']) ? $gameDetails['product_image'] : $productMap[$key]['product_image']);
				if((empty($productMap[$key]['draw_date']) || empty($productMap[$key]['draw_time'])) && !empty($gameDetails['draw_time']) && is_numeric($gameDetails['draw_time'])):
					$productMap[$key]['draw_date'] = date('Y-m-d', (int)$gameDetails['draw_time']);
					$productMap[$key]['draw_time'] = date('H:i', (int)$gameDetails['draw_time']);
				endif;
			endif;
		endforeach;

		$totalProductDetails = array_values($productMap);
		if(!empty($productTitle)):
			$totalProductDetails = array_values(array_filter($totalProductDetails, function($item) use ($productTitle){
				$title = isset($item['_id']) ? (string)$item['_id'] : '';
				return stripos($title, $productTitle) !== false;
			}));
		endif;
		$loadBalanceWhere = array(
			'user_oid' => $userOid,
			'created_at' => array('$gte' => $startDate, '$lte' => $endDate)
		);

		$totalSales = 0;
		$totalSalesCount = 0;
		$totalCommission = 0;
		$totalCancel = 0;
		$totalPaid = 0;

		foreach($totalProductDetails as $idx => $product):
			$orderIds = array_values(array_unique($product['order_ids']));
			$productLoadWhere = $loadBalanceWhere;
			if(!empty($orderIds)):
				$productLoadWhere['order_id'] = array('$in' => $orderIds);
			endif;

			$productLoadCon['where'] = $productLoadWhere;
			$loadRows = $this->common_model->getData('multiple','uw_loadBalance',$productLoadCon);
			$commissionAmount = 0;
			$cancelAmount = 0;
			$customerPaid = 0;
			$salesAmount = !empty($orderIds)
				? $this->_getHourlyTotalSalesFromLoadBalance($userOid, '', '', $orderIds)
				: 0;

			foreach($loadRows as $row):
				$narration = isset($row['narration']) ? $row['narration'] : '';
				$upoints = (float)($row['upoints'] ?? 0);
				if($narration == 'Hourly Game Commission'):
					$commissionAmount += $upoints;
				elseif($narration == 'Hourly Game Commission Cancelled'):
					$commissionAmount -= $upoints;
				elseif($narration == 'Hourly Game Order Cancelled'):
					$cancelAmount += $upoints;
				elseif($narration == 'Hourly Game Prize Redeemed'):
					$customerPaid += $upoints;
				endif;
			endforeach;

			if(!empty($orderIds)):
				$totalProductDetails[$idx]['sales'] = $salesAmount;
			endif;
			$totalProductDetails[$idx]['commissionAmount'] = round($commissionAmount, 2);
			$totalProductDetails[$idx]['totalcancelOrderAmount'] = round($cancelAmount, 2);
			$totalProductDetails[$idx]['totalCustomerPaid'] = round($customerPaid, 2);

			// Skip products with no activity in selected range.
			if(
				(int)$totalProductDetails[$idx]['sales_count'] === 0 &&
				(float)$totalProductDetails[$idx]['sales'] == 0 &&
				(float)$totalProductDetails[$idx]['commissionAmount'] == 0 &&
				(float)$totalProductDetails[$idx]['totalcancelOrderAmount'] == 0 &&
				(float)$totalProductDetails[$idx]['totalCustomerPaid'] == 0
			):
				unset($totalProductDetails[$idx]);
				continue;
			endif;

			unset($totalProductDetails[$idx]['order_ids']);
			unset($totalProductDetails[$idx]['products_oid_str']);
			unset($totalProductDetails[$idx]['products_name']);

			$totalSalesCount += (int)$totalProductDetails[$idx]['sales_count'];
			$totalSales += (float)$totalProductDetails[$idx]['sales'];
			$totalCommission += (float)$totalProductDetails[$idx]['commissionAmount'];
			$totalCancel += (float)$totalProductDetails[$idx]['totalcancelOrderAmount'];
			$totalPaid += (float)$totalProductDetails[$idx]['totalCustomerPaid'];
		endforeach;
		$totalProductDetails = array_values($totalProductDetails);

		if(empty($productTitle)):
			$totalSales = $this->_getHourlyTotalSalesFromLoadBalance($userOid, $startDate, $endDate);
		endif;

		$dueBalance = $totalSales - $totalCommission - $totalPaid;
		return array(
			'totalSalesCount' => (string)$totalSalesCount,
			'totalSales' => (string)round($totalSales, 2),
			'commissionAmount' => (string)round($totalCommission, 2),
			'totalcancelOrderAmount' => (string)round($totalCancel, 2),
			'totalCustomerPaid' => (string)round($totalPaid, 2),
			'dueBalance' => (string)round($dueBalance, 2),
			'totalProductDetails' => $totalProductDetails
		);
	}

    /**
     * Net hourly sales from load balance ledger (same date logic as lotto totalSales).
     */
    private function _getHourlyTotalSalesFromLoadBalance($userOid, $ledgerStart = '', $ledgerEnd = '', $orderIds = array())
    {
        $whereCon = array(
            'user_oid'  => $userOid,
            'narration' => array('$in' => array('Hourly Game Order', 'Hourly Game Order Cancelled'))
        );
        if($ledgerStart !== '' && $ledgerEnd !== ''):
            $whereCon['created_at'] = array('$gte' => $ledgerStart, '$lte' => $ledgerEnd);
        endif;
        if(!empty($orderIds)):
            $whereCon['order_id'] = array('$in' => array_values(array_unique($orderIds)));
        endif;

        $loadCon['where'] = $whereCon;
        $rows = $this->common_model->getData('multiple', 'uw_loadBalance', $loadCon);
        $netSales = 0;
        if(!empty($rows)):
            foreach($rows as $row):
                $narration = isset($row['narration']) ? $row['narration'] : '';
                $upoints   = (float)($row['upoints'] ?? 0);
                if($narration == 'Hourly Game Order'):
                    $netSales += $upoints;
                elseif($narration == 'Hourly Game Order Cancelled'):
                    $netSales -= $upoints;
                endif;
            endforeach;
        endif;
        return round($netSales, 2);
    }

    /**
     * Add hourly orders that exist in load balance but were missed by order query.
     */
    private function _mergeMissingHourlyOrdersFromLedger($userOid, $ledgerStart, $ledgerEnd, &$allOrderIds, &$productMap, $startTs, $endTs)
    {
        $ledgerCon['where'] = array(
            'user_oid'   => $userOid,
            'created_at' => array('$gte' => $ledgerStart, '$lte' => $ledgerEnd),
            'narration'  => 'Hourly Game Order'
        );
        $ledgerRows = $this->common_model->getData('multiple', 'uw_loadBalance', $ledgerCon);
        if(empty($ledgerRows)):
            return;
        endif;

        $knownOrderIds = array_flip(array_values(array_unique((array)$allOrderIds)));
        foreach($ledgerRows as $ledgerRow):
            $orderId = isset($ledgerRow['order_id']) ? (string)$ledgerRow['order_id'] : '';
            if($orderId === '' || isset($knownOrderIds[$orderId])):
                continue;
            endif;

            $orderCon['where']['order_id'] = $orderId;
            $orderInfo = $this->common_model->getData('single', 'uw_hourly_orders', $orderCon);
            if(empty($orderInfo)):
                continue;
            endif;

            if((string)$orderInfo['users_oid'] !== (string)$userOid):
                continue;
            endif;

            $productKey = '';
            if(!empty($orderInfo['products_oid']) && isset($orderInfo['products_oid']->{'$id'})):
                $productKey = $orderInfo['products_oid']->{'$id'};
            else:
                $productKey = isset($orderInfo['products_name']) ? $orderInfo['products_name'] : 'N/A';
            endif;

            if(!isset($productMap[$productKey])):
                $productMap[$productKey] = array(
                    '_id' => isset($orderInfo['products_name']) ? $orderInfo['products_name'] : 'N/A',
                    'price' => 0,
                    'sales_count' => 0,
                    'totalSalesCount' => 0,
                    'sales' => 0,
                    'product_image' => '',
                    'product_id' => isset($orderInfo['products_id']) ? (int)$orderInfo['products_id'] : 0,
                    'draw_date' => '',
                    'draw_time' => '',
                    'commissionAmount' => 0,
                    'totalcancelOrderAmount' => 0,
                    'totalCustomerPaid' => 0,
                    'order_ids' => array(),
                    'products_oid_str' => (!empty($orderInfo['products_oid']) && isset($orderInfo['products_oid']->{'$id'})) ? (string)$orderInfo['products_oid']->{'$id'} : '',
                    'products_name' => isset($orderInfo['products_name']) ? (string)$orderInfo['products_name'] : ''
                );
            endif;

            if(isset($orderInfo['status']) && $orderInfo['status'] != 'CL'):
                if(
                    (int)$orderInfo['created_at'] >= (int)$startTs &&
                    (int)$orderInfo['created_at'] <= (int)$endTs
                ):
                    $productMap[$productKey]['sales_count'] += (int)($orderInfo['qty'] ?? 1);
                    $productMap[$productKey]['totalSalesCount'] += 1;
                    $productMap[$productKey]['sales'] += (float)($orderInfo['total_price'] ?? 0);
                endif;
            endif;

            $productMap[$productKey]['order_ids'][] = $orderId;
            $allOrderIds[] = $orderId;
            $knownOrderIds[$orderId] = true;

            if(empty($productMap[$productKey]['draw_date']) && !empty($orderInfo['expiry_date']) && is_numeric($orderInfo['expiry_date'])):
                $productMap[$productKey]['draw_date'] = date('Y-m-d', (int)$orderInfo['expiry_date']);
                $productMap[$productKey]['draw_time'] = date('H:i', (int)$orderInfo['expiry_date']);
            endif;
        endforeach;
    }
	
}

<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class UserDashboard extends CI_Controller {

	public function  __construct() 
	{ 
		parent:: __construct();
		error_reporting(E_ALL ^ E_NOTICE);  
		$this->load->model(array('admin_model','notification_model'));
		$this->lang->load('statictext', 'admin');
		$this->load->helper('common');
	} 

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name 	: index
	 + + Developed By 	: Ashif Iqbal
	 + + Purpose  		: This function used for get user activity data
	 + + Date 			: 10 June 2025
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index($params=0)
	{	
		try{
			$this->admin_model->authCheck();
			$data['error'] 						= 	'';
			$data['activeMenu'] 				= 	'users';
			$data['activeSubMenu'] 				= 	'allusers';

			//user data
			$whereCon['where']['users_id'] 		=  (int) $params;
			$shortField 						= 	array('users_id'=> -1);
			$tblName 							= 	'uw_users';
			$data['userdata'] 					= 	$this->common_model->getData('single',$tblName,$whereCon,$shortField);

			// product data
			$pwhereCon['where']['user_id'] = (int) $params;
			$pwhereCon['where']['status'] = "A";

			$lottodata = $this->common_model->getData('multiple','uw_lotto_orders',$pwhereCon);
			$totalProductQty=0;
			$order_id =[];
			$draw_id=[];
			$windata=[];
			if($lottodata){
				foreach ($lottodata as $doc) {
					$totalProductQty += (int) $doc['product_qty'];  // cast string to int
					$order_id[] = $doc['order_id'];
					$draw_id[]=$doc['draw_id'];
				}
				$wwhereCon['where']['status'] = 1; 
				$wwhereCon['where_in'] = ['order_id', $order_id];
	
				$windata = $this->common_model->getData('multiple', 'uw_uwin_winner', $wwhereCon);
			}
			
			$total_winn =0; 
			if($windata){

				foreach ($windata as $v) {
					$total_winn +=(int)$v['amount'];
				}
			}
			$data['total_winn'] = $total_winn;
			$data['total_ticket'] = $totalProductQty;
			
			// total spent
			
			$twhereCon['where']['user_oid'] = new MongoDB\BSON\ObjectId($data['userdata']['_id']->{'$id'});
			$twhereCon['where']['status'] = "A";

			$lodbalancedata = $this->common_model->getData('multiple','uw_loadBalance',$twhereCon);
			$totalspent=0;
			$voucher_topups=0;
			$online_purchase=0;
			$transfer_wallet=0;
			if($lodbalancedata){

				foreach ($lodbalancedata as $doc) {
					if($doc['record_type'] == 'Debit'){
						$totalspent += (int) $doc['upoints'];  // cast string to int
					}
					if($doc['narration'] == 'Online recharge' && $doc['record_type'] == 'Credit'){
						$online_purchase += (int) $doc['upoints'];  // cast string to int
					}
					if($doc['narration'] == 'Recharge Coupon' && $doc['record_type'] == 'Credit'){
						$voucher_topups += (int) $doc['upoints'];  // cast string to int
					}
					if($doc['narration'] == 'Moved winning Prize' && $doc['record_type'] == 'Credit'){
						$transfer_wallet  += (int) $doc['upoints'];  // cast string to int
					}
				}
			}
			$data['totalspent'] = $totalspent;
			$data['online_purchase'] = $online_purchase;
			$data['voucher_topups'] = $voucher_topups;
			$data['transfer_wallet'] = $transfer_wallet;

			$nwhereCon['where']['users_id'] = (int) $params;
			$nwhereCon['where']['status'] = "A";

			$notificationdata = $this->common_model->getData('multiple','uw_notifications_details',$nwhereCon);
			// Notification data
			$totalread=0;
			$totalunread = 0;
			if($notificationdata){

				foreach ($notificationdata as $doc) {
					if($doc['is_read'] == 'Y'){
						$totalread +=1;
					}else{
						$totalunread +=1;
					}
					   // cast string to int
				}
			}
			$data['totalread'] = $totalread;
			$data['totalunread'] = $totalunread;
			$activeticketcount = 0;
			$adwherecon['where']['status']  = 'A';
			$adwherecon['where_gte'] = [['draw_date', date('Y-m-d')]];
			$adwherecon['where']['user_id']  = (int) $params;
			$drdata = $this->common_model->getData('multiple', 'uw_lotto_orders', $adwherecon);
			if($drdata){

				foreach($drdata as $dd){
					$activeticketcount += (int) $dd['product_qty'];
				}
			}
			$data['activeticketcount'] = $activeticketcount;
			$this->layouts->set_title($data['userdata']['users_name'].' | User | UWINN');
			$this->layouts->admin_view('users/allusers/dashboard',array(),$data);
		}catch(Exception $error){
			echo 'Caught exception: ', $error->getMessage();
		}
    }
	

	public function getUserTicketList() {
    try {
        $request = $_POST;

        $draw   = (int)($request['draw'] ?? 1);
        $start  = (int)($request['start'] ?? 0);
        $length = (int)($request['length'] ?? 10);
        $searchValue = $request['search']['value'] ?? '';
        $filterDate  = $request['filterDate'] ?? '';
        $userId = (int)($request['user_id'] ?? 0);

        $tblName = 'uw_lotto_orders';
        $shortField = ['created_at' => -1];

        $whereCon = ['where' => ['user_id' => $userId, 'status' => 'A']];

        if (!empty($filterDate)) {
            $whereCon['where']['created_at'] = new MongoDB\BSON\Regex($filterDate, 'i');
        }

        if (!empty($searchValue)) {
            $whereCon['like'] = ['product_title', $searchValue];
        }

        // ✅ Fetch more DB rows (e.g. 50 at a time)
        $maxDbRowsToTry = 50;
        $offset = 0;
        $expandedData = [];
		if ($request['length'] == -1){
					$length=10000;
					$start= 0;
		}
        while (count($expandedData) < ($start + $length)) {
            $partialList = $this->common_model->getData(
                'multiple',
                $tblName,
                $whereCon,
                $shortField,
                $maxDbRowsToTry,
                $offset
            );

            if (empty($partialList)) break; // no more data
			
			$currenttime = time();
            foreach ($partialList as $row) {
                $tickets = json_decode($row['ticket'] ?? '[]', true);
                $selections = json_decode($row['selection_values'] ?? '[]', true);
				$dwherecon['where']['draw_id']  =$row['draw_id'];
				$drawrecords= $this->common_model->getData('single', 'uw_products_draw_records',$dwherecon );
				$drawdatetime = strtotime($drawrecords['draw_date'].' '.$drawrecords['draw_time']);

                foreach ($tickets as $index => $ticketArray) {
                    $ticketStr = implode(',', $ticketArray);
                    $selectionStr = isset($selections[$index]) ? implode('-', $selections[$index]) : '';
					$wwherecon['where']['order_id'] = $row['order_id'];
					$wwherecon['where']['code'] = $ticketStr;
					$winrecords= $this->common_model->getData('single', 'uw_uwin_winner',$wwherecon );

                    $expandedData[] = [
						'order_id'=>$row['order_id'],
                        'ticket'        => $row['order_id'],
                        'draw_name'     => $row['product_title'] ?? '',
                        'amount'        => $row['total_price'] ?? '',
                        'purchase_date' => date('d-m-Y H:i',strtotime($row['created_at'])) ?? '',
                        'status' => $winrecords ? '<span class="badge badge-warning">Winner</span>' : ($currenttime > $drawdatetime ? '<span class="badge badge-success">Completed</span>' : '<span class="badge badge-secondary">Active</span>'),
						
												
                    ];
                }
            }

            $offset += $maxDbRowsToTry;
        }

        $recordsTotal = count($expandedData);
        $pagedData = array_slice($expandedData, $start, $length);

        $response = [
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsTotal,
            'data' => $pagedData
        ];

        echo json_encode($response); die();

    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}
public function getUserWinnerList() {
    try {
        $request = $_POST;

        
        $start  = (int)($request['start'] ?? 0);
        $length = (int)($request['length'] ?? 10);
        $searchValue = $request['search']['value'] ?? '';
        $filterDate  = $request['filterDate'] ?? '';
        $userId = (int)($request['user_id'] ?? 0);
		$pwhereCon['where']['user_id'] = $userId;
		$pwhereCon['where']['status'] = "A";
		$lottodata = $this->common_model->getData('multiple','uw_lotto_orders',$pwhereCon);
		
		$totalrow =0;
		$expandedData = [];
		if($lottodata){
				$order_id =[];
				foreach ($lottodata as $doc) {
					$order_id[] = $doc['order_id'];
				}
				if (!empty($filterDate)) {
					$wwhereCon['where']['created_at'] = new MongoDB\BSON\Regex($filterDate, 'i');
				}

				if (!empty($searchValue)) {
					$wwhereCon['like'] = ['order_id', $searchValue];
				}
				$wwhereCon['where']['status'] = 1; 
				$wwhereCon['where_in'] = ['order_id', $order_id];
				$shortField = ['created_at' => -1];
				$totalrow = $this->common_model->getData('count', 'uw_uwin_winner', $wwhereCon);
				if ($request['length'] == -1){
					$length='';
					$start= 0;
				}
				$winnerdata = $this->common_model->getData('multiple', 'uw_uwin_winner', $wwhereCon,$shortField,$length,$start);
			
			foreach ($winnerdata as $index => $row) {
				

				$expandedData[] = [
					'seller_name'=>$row['seller_first_name'],
					'order_id'        =>$row['order_id'] ?? '',
					'ticket'        =>$row['code'] ?? '',
					'amount'        => $row['amount'] ?? '',
					'win_date' => date('d-m-Y',strtotime($row['created_at'])) ?? '',
					'status' => $row['redeem_status'] == 'paid' ? '<span class="badge badge-success">Paid</span>' : '<span class="badge badge-danger">Due</span>',
					'claim_date'=>date('d-m-Y : H:i',strtotime($row['modified_at']))
											
				];
			}
		}
        
        
            

       
        $response = [
            'recordsTotal' => $totalrow,
            'recordsFiltered' => $totalrow,
            'data' => $expandedData
        ];

        echo json_encode($response); die();

    } catch (Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
}
public function getUserActiveTicketList(){
        try {
            $request = $_POST;

            $draw   = (int)($request['draw'] ?? 1);
            $start  = (int)($request['start'] ?? 0);
            $length = (int)($request['length'] ?? 10);
            $searchValue = $request['search']['value'] ?? '';
            $filterDate  = $request['filterDate'] ?? '';
            $userId = (int)($request['user_id'] ?? 0);

            $tblName = 'uw_lotto_orders';
            $shortField = ['created_at' => -1];

            $whereCon = ['where' => ['user_id' => $userId, 'status' => 'A']];

            // if (!empty($filterDate)) {
            //     $whereCon['where']['created_at'] = new MongoDB\BSON\Regex($filterDate, 'i');
            // }
            $whereCon['where_gte'] = [['draw_date', date('Y-m-d')]];

            if (!empty($searchValue)) {
                $whereCon['like'] = ['product_title', $searchValue];
            }

            // ✅ Fetch more DB rows (e.g. 50 at a time)
            $maxDbRowsToTry = 50;
            $offset = 0;
            $expandedData = [];
            if ($request['length'] == -1){
                        $length=10000;
                        $start= 0;
            }
            while (count($expandedData) < ($start + $length)) {
                $partialList = $this->common_model->getData(
                    'multiple',
                    $tblName,
                    $whereCon,
                    $shortField,
                    $maxDbRowsToTry,
                    $offset
                );

                if (empty($partialList)) break; // no more data
                
                $currenttime = time();
                foreach ($partialList as $row) {
                    $tickets = json_decode($row['ticket'] ?? '[]', true);
                    $selections = json_decode($row['selection_values'] ?? '[]', true);
                    

                    foreach ($tickets as $index => $ticketArray) {
                    $expandedData[] = [
                            'campaign_name'     => $row['product_title'] ?? '',
                            'draw_date'         => $row['draw_date'],
                            'order_id'          =>$row['order_id'],
                            'spent_amount'      => $row['total_price'] ?? '',
                            'status'            =>'<span class="badge badge-secondary">Active</span>',
                            'purchase_date'     => date('d-m-Y H:i',strtotime($row['created_at'])) ?? '',
                        ];
                    }
                }

                $offset += $maxDbRowsToTry;
            }

            $recordsTotal = count($expandedData);
            $pagedData = array_slice($expandedData, $start, $length);

            $response = [
                'draw' => $draw,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsTotal,
                'data' => $pagedData
            ];

            echo json_encode($response); die();

        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

	public function getUserNotificationList(){
		try{
            $request = $_POST;
		 	$start  = (int)($request['start'] ?? 0);
            $length = (int)($request['length'] ?? 10);
            $searchValue = $request['search']['value'] ?? '';
            
            $userId = (int)($request['user_id'] ?? 0);

            $tblName = 'uw_notifications_details';
            $shortField = ['creation_date' => -1];
			if($request['filterDate'] != 'all'){
				$wcon['where']['is_read'] = $request['filterDate'];
			}
			if (!empty($searchValue)) {
				$wcon['like'] = ['notific_title', $searchValue];
			}
			$wcon['where']['users_id'] =$userId;
			$wcon['where']['status'] = 'A';
			
			$recordsTotal =	$this->common_model->getData('count','uw_notifications_details',$wcon);
			$results =	$this->common_model->getData('multiple','uw_notifications_details',$wcon,$shortField,$length,$start);
			$pagedData=[];
			if($results){

				foreach ($results as $index => $row) {
							$pagedData[] = [
									'title'     => $row['notific_title'] ?? '',
									'notific_message'         => $row['notific_message'],
									'created_at'          =>date('d-m-Y H:i',strtotime($row['creation_date'])),
									'status'            =>$row['is_read']=='Y' ? '<span class="badge badge-success">Read</span>' : '<span class="badge badge-danger">Unread</span>',
									
								];
				}
			}
			$response = [
					
					'recordsTotal' => $recordsTotal,
					'recordsFiltered' => $recordsTotal,
					'data' => $pagedData
			];

            echo json_encode($response); die();
		}catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
	}
    public function getTopupsVouchersList(){
        try{
            $request = $_POST;
		 	$start  = (int)($request['start'] ?? 0);
            $length = (int)($request['length'] ?? 10);
            $searchValue = $request['search']['value'] ?? '';
            $filterDate  = $request['filterDate'] ?? '';
            $userId = (int)($request['user_id'] ?? 0);
            $type = $request['type'] ?? '';
            $whereCon['where']['users_id'] 		=  (int) $userId;
			$shortField 						= 	array('users_id'=> -1);
			$tblName 							= 	'uw_users';
			$data['userdata'] 					= 	$this->common_model->getData('single',$tblName,$whereCon,$shortField);
            if (!empty($filterDate)) {
				$twhereCon['where']['created_at'] = new MongoDB\BSON\Regex($filterDate, 'i');
			}
            if (!empty($searchValue)) {
				$twhereCon['like'] = ['remarks', $searchValue];
			}
            $twhereCon['where']['user_oid'] = new MongoDB\BSON\ObjectId($data['userdata']['_id']->{'$id'});
			$twhereCon['where']['status'] = "A";
            $twhereCon['where']['narration'] =$type;
            $shortField 						= 	array('createt_at'=> -1);
			$recordsTotal = $this->common_model->getData('count','uw_loadBalance',$twhereCon);
            $lodbalancedata = $this->common_model->getData('multiple','uw_loadBalance',$twhereCon,$shortField,$length,$start);
            $pagedData=[]; 
            if($lodbalancedata){

                foreach($lodbalancedata as $row){
                    $pagedData[] = [
									'amount'     => $row['upoints'] ?? '',
									'description'         => $row['remarks'],
									'created_at'          =>date('d-m-Y',strtotime($row['created_at'])) ?? '',
									'time'          =>date('H:i:s',strtotime($row['created_at'])) ?? '',
                                    'narration'=>$row['narration']
								];
                }
            }
            $response = [
					
					'recordsTotal' => $recordsTotal,
					'recordsFiltered' => $recordsTotal,
					'data' => $pagedData
			];

            echo json_encode($response); die();
        }catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
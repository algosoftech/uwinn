<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class Geneal_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	/***********************************************************************
	** Function name: getDataByMultipleAndCondition
	** Developed By: Manoj Kumar
	** Purpose: This function used for get data by query
	** Date : 29 JULY 2021
	************************************************************************/
	public function getDataByMultipleAndCondition($tbl_name='',$query='',$arrayfield=array())
	{  
		$resultData = array();
		$result 		= 		$this->mongo_db->aggregate($tbl_name,$query,array('batchSize'=>4)); 
		foreach($result as $results):
			foreach($results as $key=>$valye):
				if(is_array($results[$key])):
					$results[$key] 	=	$results[$key][0];
				endif;
			endforeach;
			array_push($resultData,$results);
		endforeach;		
		$groupedData 	= json_decode(json_encode($resultData), true);
		return $groupedData;
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name: getOrderData
	** Developed By: Manoj Kumar
	** Purpose: This function used for get Property Data
	** Date : 29 JULY 2021
	************************************************************************/
	public function getOrderData($action='',$tbl_name='',$where_condition='',$short_field='',$page='',$per_page='')
	{  
		$filterArray 				=	array();

		if($where_condition['search']):
			array_push($filterArray,array($where_condition['search'][0]=>new MongoDB\BSON\Regex ($where_condition['search'][1],'i')));
		endif;
		if($where_condition['where']):
			foreach($where_condition['where'] as $where_key=>$where_value):
				array_push($filterArray,array($where_key=>$where_value));
			endforeach;
		endif;

		$selectFields 			=  	array(
							'$project' => array(
								'_id'=>0,
								'order_details_id'=>1,
								'order_id'=>1,
								'product_id'=>1,
								'users_id'=>1,
								'quantity'=>1,
								'subtotal'=>1,
								'quantity'=>1,
								'price'=>1,
								'is_donated'=>1,
								'created_at'=>1,

								'payment_mode'=>'$from_order.payment_mode',
								'order_status'=>'$from_order.order_status',

								'users_name'=>'$from_users.users_name',
								'users_email'=>'$from_users.users_email',
								'users_mobile'=>'$from_users.users_mobile',
								'product_name'=>'$from_product.title',
								'stock'=>'$from_product.stock'

								));

		$whereCondition					=	array();

		if($filterArray):
			foreach($filterArray as $filterInfo):
				array_push($whereCondition,$filterInfo);
			endforeach;
		endif;

		$currentQuery					=	array(array('$lookup'=>array('from'=>'uw_users','localField'=>'user_id','foreignField'=>'users_id','as'=>'from_users')),
												  array('$lookup'=>array('from'=>'uw_products','localField'=>'product_id','foreignField'=>'products_id','as'=>'from_product')),

												  array('$lookup'=>array('from'=>'uw_orders','localField'=>'order_id','foreignField'=>'order_id','as'=>'from_order')),

												  $selectFields,
												  array('$match'=>array('$and'=>$whereCondition)),
												  array('$sort'=>$short_field));//echo '<pre>';print_r($currentQuery);die;

		if($action == 'count'):
			$totalDataCount				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
			if($totalDataCount):
				return count($totalDataCount);
			endif;
		elseif($action == 'single'):
			$currentData				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
			return $currentData[0];
		elseif($action == 'multiple'):	
			if($per_page):
				array_push($currentQuery,array('$skip'=>(int)$page));
				array_push($currentQuery,array('$limit'=>(int)$per_page));
			endif;
			$currentData				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
			return $currentData;
		endif;
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name: getOrderData
	** Developed By: Dilip Halder
	** Purpose: This function used for get Property Data
	** Date : 25 January 2024
	************************************************************************/
	public function getlottoOrderData($action='',$tbl_name='',$where_condition='',$short_field='',$page='',$per_page='')
	{  
		$filterArray 				=	array();

		if($where_condition['search']):
			array_push($filterArray,array($where_condition['search'][0]=>new MongoDB\BSON\Regex ($where_condition['search'][1],'i')));
		endif;
		if($where_condition['where']):
			foreach($where_condition['where'] as $where_key=>$where_value):
				array_push($filterArray,array($where_key=>$where_value));
			endforeach;
		endif;

		$selectFields 			=  	array(
							'$project' => array(
								'_id'=>0,
								'order_details_id'=>1,
								'order_id'=>1,
								'product_id'=>1,
								'users_id'=>1,
								'quantity'=>1,
								'subtotal'=>1,
								'quantity'=>1,
								'price'=>1,
								'is_donated'=>1,
								'created_at'=>1,

								'payment_mode'=>'$from_order.payment_mode',
								'order_status'=>'$from_order.order_status',

								'users_name'=>'$from_users.users_name',
								'users_email'=>'$from_users.users_email',
								'users_mobile'=>'$from_users.users_mobile',
								'product_name'=>'$from_product.title',
								'stock'=>'$from_product.stock'

								));

		$whereCondition					=	array();

		if($filterArray):
			foreach($filterArray as $filterInfo):
				array_push($whereCondition,$filterInfo);
			endforeach;
		endif;

		$currentQuery					=	array(array('$lookup'=>array('from'=>'uw_users','localField'=>'user_id','foreignField'=>'users_id','as'=>'from_users')),
												  array('$lookup'=>array('from'=>'uw_products','localField'=>'product_id','foreignField'=>'products_id','as'=>'from_product')),

												  array('$lookup'=>array('from'=>'uw_lotto_orders','localField'=>'order_id','foreignField'=>'order_id','as'=>'from_order')),

												  $selectFields,
												  array('$match'=>array('$and'=>$whereCondition)),
												  array('$sort'=>$short_field));//echo '<pre>';print_r($currentQuery);die;

		if($action == 'count'):
			$totalDataCount				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
			if($totalDataCount):
				return count($totalDataCount);
			endif;
		elseif($action == 'single'):
			$currentData				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
			return $currentData[0];
		elseif($action == 'multiple'):	
			if($per_page):
				array_push($currentQuery,array('$skip'=>(int)$page));
				array_push($currentQuery,array('$limit'=>(int)$per_page));
			endif;
			$currentData				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
			return $currentData;
		endif;
	}	// END OF FUNCTION


	/***********************************************************************
	** Function name: getApointmentData
	** Developed By: Manoj Kumar
	** Purpose: This function used for get Property Data
	** Date : 29 JULY 2021
	************************************************************************/
	public function getApointmentData($action='',$tbl_name='',$where_condition='',$short_field='',$page='',$per_page='')
	{  
		$filterArray 				=	array();

		if($where_condition['search']):
			array_push($filterArray,array($where_condition['search'][0]=>new MongoDB\BSON\Regex ($where_condition['search'][1],'i')));
		endif;
		if($where_condition['where']):
			foreach($where_condition['where'] as $where_key=>$where_value):
				array_push($filterArray,array($where_key=>$where_value));
			endforeach;
		endif;

		$selectFields 					=  	array('$project' => array('_id'=>0,'appointment_id'=>1,'sequence_appointment_id'=>1,'doctor_id'=>1,'fees'=>1,'appointment_date'=>1,'appointment_time'=>1,'users_id'=>1,'appointment_status'=>1,'creation_date'=>1,'status'=>1,
			'doctor_name'=>'$from_doctor.doctor_name','specialization_name'=>'$from_doctor.specialization_name','city_name'=>'$from_doctor.city_name',
			'users_name'=>'$from_users.users_name','users_email'=>'$from_users.users_email','users_address'=>'$from_users.users_address','users_mobile'=>'$from_users.users_mobile'));

		$whereCondition					=	array();

		if($filterArray):
			foreach($filterArray as $filterInfo):
				array_push($whereCondition,$filterInfo);
			endforeach;
		endif;

		$currentQuery					=	array(array('$lookup'=>array('from'=>'uw_users','localField'=>'users_id','foreignField'=>'users_id','as'=>'from_users')),
												  array('$lookup'=>array('from'=>'uw_doctors','localField'=>'doctor_id','foreignField'=>'doctor_id','as'=>'from_doctor')),
												  $selectFields,
												  array('$match'=>array('$and'=>$whereCondition)),
												  array('$sort'=>$short_field));

		if($action == 'count'):
			$totalDataCount				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
			if($totalDataCount):
				return count($totalDataCount);
			endif;
		elseif($action == 'single'):
			$currentData				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
			return $currentData[0];
		elseif($action == 'multiple'):	
			if($per_page):
				array_push($currentQuery,array('$skip'=>(int)$page));
				array_push($currentQuery,array('$limit'=>(int)$per_page));
			endif; 
			$currentData				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
			return $currentData;
		endif;
	}	// END OF FUNCTION


	/***********************************************************************
	** Function name: getassignedUserdata
	** Developed By: Ravi Negi
	** Purpose: This function used for get assigned order data
	** Date : 30 SEP 2021
	************************************************************************/
	public function getassignedUserdata($action='',$tbl_name='',$where_condition='',$short_field='',$page='',$per_page='')
	{  
		$filterArray 				=	array();

		if($where_condition['search']):
			array_push($filterArray,array($where_condition['search'][0]=>new MongoDB\BSON\Regex ($where_condition['search'][1],'i')));
		endif;
		if($where_condition['where']):
			foreach($where_condition['where'] as $where_key=>$where_value):
				array_push($filterArray,array($where_key=>$where_value));
			endforeach;
		endif;

		$selectFields 					=  	array('$project' => array('_id'=>0,'assigned_order_id'=>1,'order_id'=>1,'provider_id'=>1,'assign_date'=>1,'creation_date'=>1,
																	  'address'=>1,'current_location'=>1,'status'=>1,
																	  'users_name'=>'$from_users.users_name','users_email'=>'$from_users.users_email','users_mobile'=>'$from_users.users_mobile'));

		$whereCondition					=	array();

		if($filterArray):
			foreach($filterArray as $filterInfo):
				array_push($whereCondition,$filterInfo);
			endforeach;
		endif;

		$currentQuery					=	array(array('$lookup'=>array('from'=>'uw_users','localField'=>'provider_id','foreignField'=>'users_id','as'=>'from_users')),
												  $selectFields,
												  array('$match'=>array('$and'=>$whereCondition)),
												  array('$sort'=>$short_field));

		if($action == 'count'):
			$totalDataCount				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
			if($totalDataCount):
				return count($totalDataCount);
			endif;
		elseif($action == 'single'):
			$currentData				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
			return $currentData[0];
		elseif($action == 'multiple'):	
			if($per_page):
				array_push($currentQuery,array('$skip'=>(int)$page));
				array_push($currentQuery,array('$limit'=>(int)$per_page));
			endif; 
			$currentData				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
			return $currentData;
		endif;
	}	// END OF FUNCTION

/***********************************************************************
** Function name: getCouponData
** Developed By: Manoj Kumar
** Purpose: This function used for get Property Data
** Date : 29 JULY 2021
************************************************************************/
public function getCouponData($action='',$tbl_name='',$where_condition='',$short_field='',$page='',$per_page='')
{  
	//echo $action.'__'. $tbl_name.'__'. $where_condition; die();
	$filterArray 				=	array();

	if($where_condition['search']):
		array_push($filterArray,array($where_condition['search'][0]=>new MongoDB\BSON\Regex ($where_condition['search'][1],'i')));
	endif;
	if($where_condition['where']):
		foreach($where_condition['where'] as $where_key=>$where_value):
			array_push($filterArray,array($where_key=>$where_value));
		endforeach;
	endif;

	$selectFields 					=  	array('$project' => array('_id'=>0,'coupon_id'=>1,'users_id'=>1,'users_email'=>1,'order_id'=>1,'product_id'=>1,'product_title'=>1,'coupon_code'=>1,'created_at'=>1,'coupon_status' =>1,
																  
																  'product_name'=>'$from_product.title','adepoints'=>'$from_product.adepoints',

																  'payment_mode'=>'$from_orders.payment_mode','dilivertAddress'=>'$from_orders.dilivertAddress',

																  'users_name'=>'$from_users.users_name','users_mobile'=>'$from_users.users_mobile'));

	$whereCondition					=	array();

	if($filterArray):
		foreach($filterArray as $filterInfo):
			array_push($whereCondition,$filterInfo);
		endforeach;
	endif;

	$currentQuery					=	array(array('$lookup'=>array('from'=>'uw_users','localField'=>'users_id','foreignField'=>'users_id','as'=>'from_users')),
											  array('$lookup'=>array('from'=>'uw_products','localField'=>'product_id','foreignField'=>'products_id','as'=>'from_product')),
											  array('$lookup'=>array('from'=>'uw_orders','localField'=>'order_id','foreignField'=>'order_id','as'=>'from_orders')),
											  $selectFields,
											  array('$match'=>array('$and'=>$whereCondition)),
											  array('$sort'=>$short_field));

	
	if($action == 'count'):

		$totalDataCount				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
		//echo "<pre>"; print_r($totalDataCount); die();
		if($totalDataCount):
			return count($totalDataCount);
		endif;
	elseif($action == 'single'):
		$currentData				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
		return $currentData[0];
	elseif($action == 'multiple'):	
		if($per_page):
			array_push($currentQuery,array('$skip'=>(int)$page));
			array_push($currentQuery,array('$limit'=>(int)$per_page));
		endif; 
		$currentData				=	$this->getDataByMultipleAndCondition($tbl_name,$currentQuery);
		return $currentData;
	endif;
}	// END OF FUNCTION

/***********************************************************************
** Function name 	: updateInventoryStock
** Developed By 	: Afar Ali
** Purpose 			: This function used for update stock
** Date 			: 06 MAY 2022
************************************************************************/
public function updateInventoryStock($cpid='',$pid='', $qty='')
{  

	$resultData = array();
	$whereCon = ['products_id'=>(int)$pid,'collection_point_id' => (int)$cpid ];
	$this->mongo_db->select('*');
	$this->mongo_db->where($whereCon);
	$resultData2 = $this->mongo_db->find_one('uw_inventory');

	$inventory_available_qty = (int)$resultData2['available_qty'] - (int)$qty;
	$order_request_qty      = (int)$resultData2['order_request_qty'] - (int)$qty;
	$invParam['available_qty']	=	$inventory_available_qty;
	$invParam['order_request_qty']	=	$order_request_qty;

	$this->mongo_db->where(array('products_id'=>(int)$pid,'collection_point_id' => (int)$cpid));
	$this->mongo_db->set($invParam);
	$this->mongo_db->update('uw_inventory');
	//echo '<pre>';print_r($whereCon);die();
	//$this->updateOnlyOne('uw_inventory',$invParam,$whereCon);
	return;
}	// END OF FUNCTION


	/***********************************************************************
	** Function name 	: getWinnerList
	** Developed By 	: Dilip	Halder
	** Purpose 			: This function used to get getWinnerList details..
	** Date 			: 02 Novermber 2023
	************************************************************************/
	public function getWinnerList($AvailableWinnerCouponList='')
	{	
		$lotto_type = $AvailableWinnerCouponList['lotto_type'];
		// SoldOut Coupon Matched status.
		$data['straightMatchedCoupons'] = $this->allMacthedCoupon($AvailableWinnerCouponList,$lotto_type,'straight_add_on_amount');
		$data['rumbleMixCoupons'] 		= $this->allMacthedCoupon($AvailableWinnerCouponList,$lotto_type,'rumble_add_on_amount');
		$data['reverseCoupons'] 		= $this->allMacthedCoupon($AvailableWinnerCouponList,$lotto_type,'reverse_add_on_amount');
		return $data;
	}


	public function allMacthedCoupon($AvailableWinnerCouponList='',$lotto_type='',$serchType='')
	{	
		// retriving Product Details as per produuct id
		$productID = $AvailableWinnerCouponList['product_id'];

		
		$whereCon = array('product_id'=>(int)$productID , $serchType => array('$gt' =>  0),'status' => array('$ne' => "CL"));
		$this->mongo_db->select('*');
		$this->mongo_db->where($whereCon);
		$OrderData = $this->mongo_db->get('uw_lotto_orders');

		$WinnerCoupon = $AvailableWinnerCouponList['coupon_code'];
		// echo "<pre>";print_r($WinnerCoupon);die();

		$resultData = array();
		$count = 1;
		
		if($serchType == "straight_add_on_amount"):
			foreach ($OrderData as $key => $item):
				$soldoutarray = json_decode($item['ticket']);
				$order_id     = $item['order_id'];
				$findarray 	  =  $WinnerCoupon;
				
				$result = $this->straightMatchedCoupons($soldoutarray, $findarray,$order_id);
				if(!empty($result)):
					array_push($resultData, $result);
				endif;
				
			endforeach;
			// return $resultData;
		endif;

		if($serchType == "reverse_add_on_amount"):
			foreach ($OrderData as $key => $item):
				$soldoutarray = json_decode($item['ticket']);
				$order_id     = $item['order_id'];
				$findarray 	  =  $WinnerCoupon;

				$result = $this->reverseCoupons($soldoutarray, $findarray,$order_id);
				if(!empty($result)):
					array_push($resultData, $result);
				endif;

			endforeach;
			// return $resultData;

		endif;

		if($serchType == "rumble_add_on_amount"):

			foreach ($OrderData as $key => $item):
				$soldoutarray = json_decode($item['ticket']);
				$order_id     = $item['order_id'];
				$findarray 	  =  $WinnerCoupon;

				$result = $this->rumbleMixCoupons($soldoutarray, $findarray,$order_id);
				if(!empty($result)):
					array_push($resultData, $result);
				endif;
			endforeach;
			// return $resultData;
		endif;

		
		return $resultData;

	}

	function straightMatchedCoupons($haystack, $needle,$orderID='') {

		$result = [];
		foreach ($haystack as $key => $item):
			
			 

			if ($item === $needle):
			    $data['coupon'] =  implode(', ', $item);
			 	$data['matched_count'] = count($item);
			 	$data['orderID'] = $orderID;
			else:
			    $count = 0;
			    $data =array();
				// Loop through the arrays and compare values
				for ($i = 0; $i < count($item) && $i < count($needle); $i++):
				    if ($item[$i] === $needle[$i]):
				        $count++;
				        $data['coupon'] =  implode(', ', $item);
					 	$data['matched_count'] = $count;
					 	$data['orderID'] = $orderID;


				    else:
				        // Break the loop if a mismatch is found
				        break;
				    endif;
				endfor;
		    endif;
		    if(!empty($data)):
					array_push($result ,$data);
				endif;
		endforeach;
		return $result;
	} 

	function reverseCoupons($haystack, $needle,$orderID='') {
	  	
		$ReveredArray = [];
		foreach ($haystack as $key => $item):
		 		$ReveredArray[] = array_reverse($item);
		endforeach;

	 	$result = [];
		foreach ($ReveredArray as $key => $item):
			  if(count($item) == count($needle)):
		        $ArrayLength = count($item);
				
				for ($i=0; $i <$ArrayLength ; $i++):

					 $CouponsCount = 1;
					 
					 if($item[$i] === $needle[$i]):
	                   
	                    $ArrayCount = $ArrayCount + $CouponsCount;
					 	$data['coupon'] =  implode(', ', $item);
					 	$counter = $i+1;
				 		if($counter === $ArrayCount):
					 		$data['matched_count'] = $ArrayCount;
				 		endif;	
					 	$data['orderID'] = $orderID;

					elseif($item[$i] !== $needle[$i]):
					 	if($i == 0):
					 		$counter = 0;
					 	elseif($counter != $key):
					 		$counter = 0;
					 	endif;
					 	$data['coupon'] =  implode(', ', $item);
					 	$data['matched_count'] = $counter;
					 	$data['orderID'] = $orderID;
					endif;
					 
				endfor;

			 	array_push($result ,$data);

		      endif;
		endforeach;


		return $result;

	}

	function rumbleMixCoupons($haystack, $needle,$orderID='') {

		$result = [];
		foreach ($haystack as $key => $firstitem):
		 	
		 	$count  = 0;
			$store  =  [];
			foreach($firstitem as $key => $item):
				if(in_array($item ,$needle) && !in_array($item, $store) ):
					$store[] = $item;
					++$count;
				endif;
				$data['coupon'] = implode(', ', $firstitem);
			    $data['matched_count'] = $count?$count:0;
			    $data['orderID'] = $orderID;
			endforeach;
			array_push($result,$data);
		endforeach;

		return $result;
	} 	

	/***********************************************************************
	** Function name : OrderStaticReport
	** Developed By  : Dilip Halder
	** Purpose       : This function used to get orice static report by condition..
	** Date 		 : 09 May 2024
	************************************************************************/
	public function GetGroupData($tblName='',$SelectFields=array(),$whereCondition='',$groupBy='',$sortBy='')
	{  
		$query  = array( 
						  array('$project' => $SelectFields),
						  array('$match'   => $whereCondition),
					      array('$group'   => $groupBy),
					      array('$sort' => $sortBy)
				);

		$result 	= $this->mongo_db->aggregate($tblName,$query,array('batchSize'=>4)); 
		foreach($result as $key => $item):
			$result[$key]['start_time'] = $whereCondition['created_at']['$gte'];
			$result[$key]['end_time'] = $whereCondition['created_at']['$lte'];
		endforeach;
		return $result;
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name : GetWinnerGroupByData
	** Developed By  : Dilip Halder
	** Purpose       : This function used to get GetWinnerGroupByData by condition..
	** Date 		 : 25 May 2024
	************************************************************************/
	public function GetWinnerGroupByData($whereCon='', $resultType='', $page='', $skip='')
	{  
		$SelectFields = array(
     	 "voucher_id" => 1,
     	 "order_id" => 1,
	     "batch_id" => 1,
	     'csv_name'=>1,
	     "amount" => 1,
	     "status"     => 1,
	     "soft_delete" => 1,
	     "created_at" => 1,
	     "created_by" => 1,
	     "total_count" => 1,
	     "first_order_id" => 1,
	     "last_order_id" => 1,
		 "activeStatus" => 1,
		 "inactiveStatus" => 1,
		 "paid" => 1,
		 'unpaid'=>1
		);

		if($whereCon):
		  $whereCondition  =  $whereCon['where'];
		endif;
		$groupBy = array(
            '_id' 		    => '$batch_id',
            'batch_id'      => array('$first'   => '$batch_id'),
            'csv_name'      => array('$first'   => '$csv_name'),
            'first_order_id'=> array('$first' => '$order_id'),
            'last_order_id' => array('$last'  => '$order_id'),
            'status'        => array('$first'   => '$status'),
            'soft_delete'   => array('$first'   => '$soft_delete'),
            'amount' => array('$sum' => array(
                '$cond' => array(
                    'if' => array(
                        '$regexMatch' => array(
                            'input' => '$amount',
                            'regex' => '^[0-9]+$' 
                        )
                    ),
                    'then' => array('$toDouble' => '$amount'),
                    'else' => '0'
                )
	        )),
            'total_count'   => array('$sum'     => (int)1),
            'created_at'    => array('$last'     => '$created_at'),
			'inactiveStatus' => array('$sum'     => array(
				'$cond' => array(
					'if' => array('$eq' => array('$status', 0)),
					'then' => 1,
					'else' => 0
				)
			)),
			'activeStatus' => array('$sum'     => array(
				'$cond' => array(
					'if' => array('$eq' => array('$status', 1)),
					'then' => 1,
					'else' => 0
				)
			)),
			'paid' => array(
			    '$sum' => array(
			        '$cond' => array(
			            'if' => array(
			                '$and' => array(
			                    array('$eq' => array('$redeem_status', 'paid')),
			                    array('$regexMatch' => array( 'input' => '$amount', 'regex' => '^[0-9]+$' ))
			                )
			            ),
			            'then' => array('$toDouble' => '$amount'),  // Convert to double if numeric
			            'else' => 0
			        )
			    )
			),

			'unpaid' => array(
			    '$sum' => array(
			        '$cond' => array(
			            'if' => array(
			                '$and' => array(
			                    array('$ne' => array('$redeem_status', 'paid')),
			                    array('$regexMatch' => array( 'input' => '$amount', 'regex' => '^[0-9]+$' ))
			                )
			            ),
			            'then' => array('$toDouble' => '$amount'),  // Convert to double if numeric
			            'else' => 0
			        )
			    )
			),
    	);

    	$sortBy      = array('_id' => -1);
		$tblName     = "uw_uwin_winner";
		$WinnerData  = $this->common_model->getAggregateData($tblName,$SelectFields,$whereCondition,$groupBy,$sortBy,$lookup,$unwind,$resultType,$page,$skip);
		
		return $WinnerData;

	}	// END OF FUNCTION

	/***********************************************************************
	** Function name : GetRaffleWinnerGroupByData
	** Developed By  : Dilip Halder
	** Purpose       : This function used to get GetRaffleWinnerGroupByData by condition..
	** Date 		 : 25 January 2025
	************************************************************************/
	public function GetRaffleWinnerGroupByData($whereCon='', $resultType='', $page='', $skip='')
	{  
		$SelectFields = array(
     	 "voucher_id" => 1,
     	 "order_id" => 1,
	     "batch_id" => 1,
	     'csv_name'=>1,
	     "amount" => 1,
	     "status"     => 1,
	     "soft_delete" => 1,
	     "created_at" => 1,
	     "created_by" => 1,
	     "total_count" => 1,
	     "first_order_id" => 1,
	     "last_order_id" => 1,
		 "activeStatus" => 1,
		 "inactiveStatus" => 1,
		 "paid" => 1,
		 'unpaid'=>1
		);

		if($whereCon):
		  $whereCondition  =  $whereCon['where'];
		endif;
		$groupBy = array(
            '_id' 		    => '$batch_id',
            'batch_id'      => array('$first'   => '$batch_id'),
            'csv_name'      => array('$first'   => '$csv_name'),
            'first_order_id'=> array('$first'   => '$order_id'),
            'last_order_id' => array('$last'    => '$order_id'),
            'status'        => array('$first'   => '$status'),
            'amount' 		=> array('$sum'     => '$amount_in_number' ),
            'total_count'   => array('$sum'     => (int)1),
            'created_at'    => array('$last'    => '$created_at'),
			
			'paid' => array('$sum' => array(
				'$cond' => array(
					'if' => array('$eq' => array('$status', "C")),
					'then' => 1,
					'else' => 0
				)
			)),

			'paid' => array(
			    '$sum' => array(
			        '$cond' => array(
			            'if' => array('$eq' => array('$status', "C")),
			            'then' => array('$toDouble' => '$amount_in_number'),  // Convert to double if numeric
			            'else' => 0
			        )
			    )
			),

			'unpaid' => array(
			    '$sum' => array(
			        '$cond' => array(
			            'if' => array('$eq' => array('$status', "A")),
			            'then' => array('$toDouble' => '$amount_in_number'),  // Convert to double if numeric
			            'else' => 0
			        )
			    )
			),
			'inactiveStatus' => array('$sum'     => array(
				'$cond' => array(
					'if' => array('$eq' => array('$status', "I")),
					'then' => 1,
					'else' => 0
				)
			)),
			'activeStatus' => array('$sum'     => array(
				'$cond' => array(
					'if' => array('$eq' => array('$status', "A")),
					'then' => 1,
					'else' => 0
				)
			)),
    	);

    	$sortBy      = array('_id' => -1);
		$tblName     = "uw_raffle_winner";
		$WinnerData  = $this->common_model->getAggregateData($tblName,$SelectFields,$whereCondition,$groupBy,$sortBy,$lookup,$unwind,$resultType,$page,$skip);
		
		return $WinnerData;

	}	// END OF FUNCTION
	
}
<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class Order_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	/***********************************************************************
	** Function name: getDataByMultipleAndCondition
	** Developed By: Afsar Ali
	** Purpose: This function used for get data by query
	** Date : 29 DEC 2022
	************************************************************************/
	public function getDataByMultipleAndCondition($tbl_name='',$query='',$arrayfield=array())
	{  
		$resultData = array();
		$result 		= 		$this->mongo_db->aggregate($tbl_name,$query,array('batchSize'=>4)); 
		foreach($result as $results):
			foreach($results as $key=>$valye):
				if(is_array($results[$key])):
					$results[$key] 	=	$results[$key];
				endif;
			endforeach;
			array_push($resultData,$results);
		endforeach;		
		$groupedData 	= json_decode(json_encode($resultData), true);
		return $groupedData;
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name: getordersList
	** Developed By: Afsar Ali
	** Purpose: This function used for get Property Data
	** Date : 29 DEC 2022
	** updated By : Dilip Halder
	** Updated date : 17 march 2023
	************************************************************************/
	public function getordersList($action='',$tbl_name='',$where_condition='',$short_field='',$page='',$per_page='')
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

		if($where_condition['where_gte']):

			foreach($where_condition['where_gte'] as $where_key => $where_value):
				// $data = $this->mongo_db->where_gte($where_value[0],$where_value[1]);
				array_push($filterArray,array($where_value[0]=> array('$gte' => $where_value[1])));
			endforeach;
		endif;

		if($where_condition['where_lte']):

			foreach($where_condition['where_lte'] as $where_key => $where_value):
				// $data = $this->mongo_db->where_gte($where_value[0],$where_value[1]);
				array_push($filterArray,array($where_value[0]=> array('$lte' => $where_value[1])));
			endforeach;
		endif;


		if(isset($where_condition['like']) && $where_condition['like']):	
			array_push($filterArray,array($where_condition['like'][0]=> $where_condition['like'][1]));
		endif;

		$selectFields 			=  	array(
							'$project' => array(
								'_id'=>0,
								'sequence_id'=>1,
								'order_id'=>1,
								'user_id'=>1,
								'user_type'=>1,
								'user_email'=>1,
								'user_phone'=>1,
								'product_is_donate'=>1,
								'shipping_method'=>1,
								'emirate_id'=>1,
								'emirate_name'=>1,
								'area_id'=>1,
								'area_name'=>1,
								'collection_point_id'=>1,
								'collection_point_name'=>1,
								'shipping_address'=>1,
								'product_count'=>1,
								'shipping_charge'=>1,
								'inclusice_of_vat'=>1,
								'subtotal'=>1,
								'vat_amount'=>1,
								'total_price'=>1,
								'payment_mode'=>1,
								'payment_from'=>1,
								'order_status'=>1,
								'created_at'=>1,
								'collection_code'=>1,
								'collection_status'=>1,
								'expairy_data'=>1,
								'update_date'=>1,
								'user_name' => '$from_users.users_name',
								'last_name' => '$from_users.last_name',
								'country_code' => '$from_users.country_code',

								'order_details'=>'$from_orders_details',
								'product_name'=>'$from_orders_details.product_name',
								'coupons' => '$from_coupons.coupon_code'

								//'collection_point_details' => '$from_collection_point',


								

								));

		$whereCondition					=	array();

		if($filterArray):
			foreach($filterArray as $filterInfo):
				array_push($whereCondition,$filterInfo);
			endforeach;
		endif;
		if(count($whereCondition) != 0):
			$currentQuery					=	array(
				//array('$lookup'=>array('from'=>'uw_orders','localField'=>'user_id','foreignField'=>'user_id','as'=>'from_orders')),
													array('$lookup'=>array('from'=>'uw_orders_details','localField'=>'order_id','foreignField'=>'order_id','as'=>'from_orders_details')),
													array('$lookup'=>array('from'=>'uw_users','localField'=>'user_id','foreignField'=>'users_id','as'=>'from_users')),
													array('$lookup'=>array('from'=>'uw_coupons','localField'=>'order_id','foreignField'=>'order_id','as'=>'from_coupons')),
													$selectFields,
													array('$match'=>array('$and'=>$whereCondition)),
													array('$sort'=>$short_field)
												);//echo '<pre>';print_r($currentQuery);die;
		else:
			$currentQuery					=	array(
				//array('$lookup'=>array('from'=>'uw_orders','localField'=>'user_id','foreignField'=>'user_id','as'=>'from_orders')),
													array('$lookup'=>array('from'=>'uw_orders_details','localField'=>'order_id','foreignField'=>'order_id','as'=>'from_orders_details')),
													array('$lookup'=>array('from'=>'uw_users','localField'=>'user_id','foreignField'=>'users_id','as'=>'from_users')),
													//array('$lookup'=>array('from'=>'uw_emirate_collection_point','localField'=>'collection_point_id','foreignField'=>'collection_point_id','as'=>'from_emirate_collection_point')),
													$selectFields,
													//array('$match'=>array('$and'=>$whereCondition)),
													array('$sort'=>$short_field)
												);//echo '<pre>';print_r($currentQuery);die;
		endif;
		
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
	** Function name: getQuickordersList
	** Developed By: Dilip Halder
	** Purpose: This function used for get Property Data
	** Date : 07 March 2023
	************************************************************************/
	public function getQuickordersList($action='',$tbl_name='',$where_condition='',$short_field='',$page='',$per_page='')
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

		if($where_condition['where_gte']):

			foreach($where_condition['where_gte'] as $where_key => $where_value):
				// $data = $this->mongo_db->where_gte($where_value[0],$where_value[1]);
				array_push($filterArray,array($where_value[0]=> array('$gte' => $where_value[1])));
			endforeach;
		endif;

		if($where_condition['where_lte']):

			foreach($where_condition['where_lte'] as $where_key => $where_value):
				// $data = $this->mongo_db->where_gte($where_value[0],$where_value[1]);
				array_push($filterArray,array($where_value[0]=> array('$lte' => $where_value[1])));
			endforeach;
		endif;


		if(isset($where_condition['like']) && $where_condition['like']):	
			array_push($filterArray,array($where_condition['like'][0]=> $where_condition['like'][1]));
		endif;

		$selectFields 			=  	array(
							'$project' => array(
								'_id'=>1,
								'sequence_id'=>1,
								'ticket_order_id'=>1,
								'user_id'=>1,
								'user_type'=>1,
								'user_email'=>1,
								'user_phone'=>1,
								'product_title'=>1,
								'product_qty'=>1,
								'prize_title'=>1,
								'vat_amount'=>1,
								'subtotal'=>1,
								'total_price'=>1,
								'payment_mode'=>1,
								'payment_from'=>1,
								'product_is_donate'=>1,
								'user_name' => '$from_users.users_name',
								'country_code' => '$from_users.country_code',
								'order_details'=>'$from_orders_details',
								
								'order_first_name' =>'$coupons.order_first_name',
								'order_last_name' =>'$coupons.order_last_name',
								'order_users_mobile' =>'$coupons.order_users_mobile',
								'order_users_email'=>'$coupons.order_users_email',
								'coupon_code' => '$coupons.coupon_code',
								'order_status' =>1,
								'created_at' =>1,

								));

			$whereCondition					=	array();



			if($filterArray):
				foreach($filterArray as $filterInfo):
					array_push($whereCondition,$filterInfo);
				endforeach;
			endif;


			if(count($whereCondition) != 0):
				$currentQuery					=	array(
					//array('$lookup'=>array('from'=>'uw_orders','localField'=>'user_id','foreignField'=>'user_id','as'=>'from_orders')),
														array('$lookup'=>array('from'=>'uw_ticket_coupons','localField'=>'ticket_order_id','foreignField'=>'ticket_order_id','as'=>'coupons')),
														array('$lookup'=>array('from'=>'uw_users','localField'=>'user_id','foreignField'=>'users_id','as'=>'from_users')),
														//array('$lookup'=>array('from'=>'uw_emirate_collection_point','localField'=>'collection_point_id','foreignField'=>'collection_point_id','as'=>'from_collection_point')),
														$selectFields,
														array('$match'=>array('$and'=>$whereCondition)),
														array('$sort'=>$short_field)
													);
													// echo '<pre>';print_r($currentQuery);die;
			else:
				$currentQuery					=	array(
					//array('$lookup'=>array('from'=>'uw_orders','localField'=>'user_id','foreignField'=>'user_id','as'=>'from_orders')),
														array('$lookup'=>array('from'=>'uw_ticket_coupons','localField'=>'product_id','foreignField'=>'product_id','as'=>'coupons')),
														array('$lookup'=>array('from'=>'uw_users','localField'=>'user_id','foreignField'=>'users_id','as'=>'from_users')),
														//array('$lookup'=>array('from'=>'uw_emirate_collection_point','localField'=>'collection_point_id','foreignField'=>'collection_point_id','as'=>'from_collection_point')),
														$selectFields,
														// array('$match'=>array('$and'=>$whereCondition)),
														array('$sort'=>$short_field)
													);//echo '<pre>';print_r($currentQuery);die;
			endif;
			
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

}
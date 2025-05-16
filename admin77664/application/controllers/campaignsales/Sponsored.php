<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sponsored extends CI_Controller {

	public function  __construct() 
	{ 
		parent:: __construct();
		error_reporting(E_ALL ^ E_NOTICE);  
		$this->load->model(array('admin_model','emailtemplate_model','sms_model','notification_model','geneal_model'));
		$this->lang->load('statictext', 'admin');
		$this->load->helper('common');
	} 

	/* * *********************************************************************
	 * * Function name : index
	 * * Developed By : Manoj Kumar
	 * * Purpose  : This function used for 
	 * * Date : 21 SEPTEMBER 2022
	 * * **********************************************************************/
	public function index()
	{	
		$this->admin_model->authCheck();
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'';
		$data['activeSubMenu'] 				= 	'';
		$data['ordersponsoredData'] 		= 	array();

		$sponseredProduct = [];
		// Online product purchases 
		$prodFields 		=  	array('$project' => array( '_id'=>0  ));
		$prodCon			=	array(array('is_donated' =>  array( '$eq' => "Y" ) ) );
		$prodQuery			=	array(

									  array('$lookup'=>array('from'=>'da_orders','localField'=>'order_id','foreignField'=>'order_id','as'=>'from_order')),
									  $prodFields,

									  array('$match'=>array('$and'=>$prodCon)),
									  array('$group' => array(	
									  							'_id' => '$product_id',
									  							'product_name' => array('$first' => '$product_name'),
									  							'soldout_qty'=>array('$sum' => '$quantity'),
									  							'total_sales'=>array('$sum' => '$price'),
								  								'start_date'=>array('$first' => '$from_order.created_at'),  
									  							'end_date'=>array('$last' => '$from_order.created_at') 
									  						)),
									  array('$sort'=>array('creation_date'=>-1))
									);	
		$OnlinPurchases		=	$this->common_model->getDataByMultipleAndCondition('da_orders_details',$prodQuery);

		// array_push($sponseredProduct, $OnlinPurchases);

		// Quick product purchases
		$prodFields1 		=  	array('$project' => array( '_id'=>0));
		$prodCon1			=	array(array('product_is_donate' =>  array( '$eq' => "Y" ) ) );
		$prodQuery1			=	array(
									  $prodFields1,
									  array('$match'=>array('$and'=>$prodCon1)),
									  array('$group' => array(
									  							'_id' => '$product_id',
									  							'product_name' => array('$first' => '$product_title'), 
									  							'soldout_qty'=>array('$sum' => 1 ) ,
									  							'total_sales'=>array('$sum' => '$total_price'),
									  							'start_date'=>array('$first' => '$created_at'),  
									  							'end_date'=>array('$last' => '$created_at')   
									  						)),
									  array('$sort'=>array('created_at'=>-1))
									);	
		$QuickPurchases		=	$this->common_model->getDataByMultipleAndCondition('da_ticket_orders',$prodQuery1);
		
		// array_push($sponseredProduct, $QuickPurchases);
		
		function sortByCreatedAt($a, $b) {
		    $timeA = strtotime($a['start_date']);
		    $timeB = strtotime($b['start_date']);
		    return $timeB - $timeA;
		}
		
		if($OnlinPurchases):
			usort($OnlinPurchases, 'sortByCreatedAt');
		endif;

		if($QuickPurchases):
			usort($QuickPurchases, 'sortByCreatedAt');
		endif;

		$data['onlinPurchases']  = $OnlinPurchases;
		$data['quickPurchases']  = $QuickPurchases;

		$this->layouts->set_title('Sponsored| Admin | DealzAribia');
		$this->layouts->admin_view('campaignsales/sponsored/index',array(),$data);

	}	// END OF FUNCTION
}
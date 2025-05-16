<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sales extends CI_Controller {

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
		$data['orderTargetData'] 			= 	array();

		$data['moduleData']					=	$this->admin_model->getMenuModule('main'); 

		$prodFields 		=  	array('$project' => array('_id'=>0,'title'=>1,'products_id'=>1,'stock'=>1,'totalStock'=>1,'target_stock'=>1,
														  'category_name'=>1,'sub_category_name'=>1,'product_image'=>1,'clossingSoon'=>1,'product_seq_id'=>1,'validuptodate'=>1,'validuptotime'=>1,
														  'actual_product_name'=>'$from_prize.title','actual_product_image'=>'$from_prize.prize_image'
														 ));
		$prodCon			=	array(array('clossingSoon'=>'N')
									  //,array('stock'=>array('$gt'=> 0)) 
									);
		$prodQuery			=	array(array('$lookup'=>array('from'=>'da_prize','localField'=>'products_id','foreignField'=>'product_id','as'=>'from_prize')),
									  $prodFields,
									  array('$match'=>array('$and'=>$prodCon)),
									  array('$sort'=>array('creation_date'=>-1)));	
		$prodData			=	$this->common_model->getDataByMultipleAndCondition('da_products',$prodQuery);
		if($prodData):	$count  		=	0;
			foreach($prodData as $prodInfo):
				if($count <=100):
					$dataFound   			=	'N';

					$ordTarFields 			=  	array('$project' => array('_id'=>0,'product_id'=>1,'quantity'=>1,'order_status'=>'$from_order.order_status'));
					$ordTarCon				=	array(array('product_id'=>$prodInfo['products_id'],'order_status'=>'Success'));
					$ordTarQuery			=	array(array('$lookup'=>array('from'=>'da_orders','localField'=>'order_id','foreignField'=>'order_id','as'=>'from_order')),
											  	$ordTarFields,
											  	array('$match'=>array('$and'=>$ordTarCon)),
											  	array('$group' => array('_id' => '$product_id','count'=>array('$sum' => '$quantity'))),
											  	array('$sort'=>array('creation_date'=>-1)));	
					$ordTarData				=	$this->common_model->getDataByMultipleAndCondition('da_orders_details',$ordTarQuery);
					if($ordTarData):
						$prodInfo['total_sale'] 	=	$ordTarData[0]['count'];
						array_push($data['orderTargetData'],$prodInfo);
					endif;
				endif;

				$count++;
			endforeach;
		endif;

		$this->layouts->set_title('Campaign sales | Admin | DealzAribia');
		$this->layouts->admin_view('campaignsales/sales/index',array(),$data);

	}	// END OF FUNCTION
}
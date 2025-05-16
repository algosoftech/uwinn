<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class uwinn extends CI_Controller {
	
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
	 * * Function name 	: getLottoProductListPageData
	 * * Developed By 	: Dilip Halder
	 * * Purpose  		: This function used for get Product List Page Data
	 * * Date 			: 06 March 2024
	 * * Updated By     : Dilip Halder
	 * * Updated Date   : 20 June 2024
	 * * **********************************************************************/
	public function getProductListPageData()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 			= 	array();	
		if(requestAuthenticate(APIKEY,'GET')):
			
			$ourCampaigns 	  =	array();
			$P_tblName 	  	  = 'uw_products';
			$P_fileds		  = array('product_image','title','description','straight_add_on_amount','rumble_add_on_amount','reverse_add_on_amount','lotto_type','lotto_range','draw_date','draw_time','products_id','campaign_auto_freezing_mode','campaign_freezing_end_time','campaign_freezing_start_time','reverse_settings_default_check','rumble_settings_default_check','straight_settings_default_check','product_name','enable_number_prefix','lotto_range_prefix','lotto_range_start','lotto_range_end','ticket_number_repeat','app_image','reverse_settings','rumble_settings','straight_settings','straight_game_name','rumble_game_name','reverse_game_name' ,'straight_settings_default_check','rumble_settings_default_check','reverse_settings_default_check','campaign_price','show_ticket_for_campaign','campaign_price','ticket_count_per_campaign','show_ticket_for_campaign','show_on','enable_raffle_ticket','reffle_prefix','reffle_length'
			);

			$P_where['where']['status']  = "A";
			
			if($this->input->get('show_on')):
				$P_where['where']['show_on'] = $this->input->get('show_on');
			else:
				$P_where['where']['show_on'] = array('$in' => array('POS'));
			endif;
				$P_where['where']['enable_raffle_ticket'] =  array('$ne' => 'Enable');
			$P_where['where']['stock'] 	 = array('$gt'=> 0);
			$P_where['where']['remarks'] = "lotto-products";
			$shortField   	  			 = array('seq_order' => -1 );
			$data2	  		  			 = $this->common_model->getDataByNewQuery($P_fileds,'multiple',$P_tblName,$P_where , $shortField);

			$USERID = $this->input->get('users_id');
			$whereCon['where'] 		=   array( 'status'=> 'A');
			$PermissionDetails		=	$this->common_model->getData('single','uw_allowed_campaigns_permission',$whereCon);
			// if($USERID):

			// Product Settings.... 
			$whereConSettings['where']   = array( 'status'=> 'A');
        	$settings                    = $this->common_model->getData('single','uw_settings',$whereConSettings);
			//Products settings start
            $straight_settings               = $settings['straight_settings'];
            $rumble_settings                 = $settings['rumble_settings'];
            $reverse_settings                = $settings['reverse_settings'];
            
            $straight_game_name              = $settings['straight_game_name'];
            $rumble_game_name                = $settings['rumble_game_name'];
            $reverse_game_name               = $settings['reverse_game_name'];
            
            $reverse_settings_default_check  = $settings['reverse_settings_default_check'];
            $rumble_settings_default_check   = $settings['rumble_settings_default_check'];
            $straight_settings_default_check = $settings['straight_settings_default_check'];
            $game_description                = $settings['game_description'];
            $game_rule_image                 = $settings['game_rule_image'];
            
            $campaign_auto_freezing_mode     = $settings['campaign_auto_freezing_mode'];
            $campaign_freezing_end_time      = $settings['campaign_freezing_end_time'];
            $campaign_freezing_start_time    = $settings['campaign_freezing_start_time'];
            $show_ticket_for_campaign    	 = $settings['show_ticket_for_campaign'];
            //Products settings End 

			$productData  			= array();
			foreach($data2 as $iTems):

				if($iTems['straight_game_name'] == '' && $iTems['rumble_game_name'] == '' && $iTems['reverse_game_name'] == "" ):
					$iTems['straight_game_name'] = $straight_game_name;
					$iTems['rumble_game_name']   = $rumble_game_name;
					$iTems['reverse_game_name']  = $reverse_game_name;
				endif;

				if($iTems['straight_settings'] == '' && $iTems['rumble_settings'] == '' && $iTems['reverse_settings'] == "" ):
					$iTems['straight_settings'] = $straight_settings;
					$iTems['rumble_settings']   = $rumble_settings;
					$iTems['reverse_settings']  = $reverse_settings;
				endif;

				if($iTems['reverse_settings_default_check'] == '' && $iTems['rumble_settings_default_check'] == '' && $iTems['straight_settings_default_check'] == "" ):
					$iTems['straight_settings_default_check'] = $straight_settings_default_check;
					$iTems['rumble_settings_default_check']   = $rumble_settings_default_check;
					$iTems['reverse_settings_default_check']  = $reverse_settings_default_check;
				endif;

				if($iTems['campaign_auto_freezing_mode']  == "" || $iTems['campaign_freezing_end_time']  == ""|| $iTems['campaign_freezing_start_time']  == ""):
					$iTems['campaign_auto_freezing_mode'] 	= $campaign_auto_freezing_mode;
					$iTems['campaign_freezing_end_time']   	= $campaign_freezing_end_time;
					$iTems['campaign_freezing_start_time']  = $campaign_freezing_start_time;
				endif;

				if($iTems['show_ticket_for_campaign']  == ""):
					$iTems['show_ticket_for_campaign'] 	= $show_ticket_for_campaign;
				endif;

				if(in_array($USERID, $PermissionDetails['seleted_users'])  &&  in_array($iTems['products_id'] , $PermissionDetails['seleted_campaign']) ):
					 $productData[] = $iTems;
				elseif(!in_array($USERID, $PermissionDetails['seleted_users'])  &&  !in_array($iTems['products_id'] , $PermissionDetails['seleted_campaign']) ):
					 $productData[] = $iTems;
				endif;
			endforeach;
				
			// endif;

			if($productData):
				foreach($productData as $info2):
					$info2['product_name'] = $info2['title'];
					
					$drawDate2 			= 	$info2['draw_date'].' '.$info2['draw_time'].':00';
					$today2 			= 	date('Y-m-d H:i:s');

					if(strtotime($drawDate2) > strtotime($today2)):
						if($this->input->post('users_id')):
							$prowhere['where']	=	array('users_id'=>(int)$this->input->post('users_id'),'product_id'=>(int)$info2['products_id']);
							$prodData			=	$this->common_model->getData('single','uw_wishlist',$prowhere);
							if($prodData):
								if($prodData['wishlist_product'] == 'Y'):
									$info2['wishlist_product']  = 'Y';
								else:
									$info2['wishlist_product']  = 'N';
								endif; 
							else:
								$info2['wishlist_product']  	= 'N';
							endif;
						else:
							$info2['wishlist_product']  		= 'N';
						endif;

						if($this->input->post('users_id')):
							$USRwhere 					=	[ 'users_id' => (int)$this->input->post('users_id') ];
							$USRtblName 				=	'uw_users';
							$userDetails 				=	$this->geneal_model->getOnlyOneData($USRtblName, $USRwhere);
							if($userDetails):
								$productShareUrl  		= 	generateProductShareUrl($info2['products_id'],$this->input->post('users_id'),$userDetails['referral_code']);
								$info2['share_url']  	= 	$productShareUrl;
							else:	
								$info2['share_url']  	= 	'';
							endif;
						else:
							$info2['share_url']  		= 	'';
						endif;

						$Prize_Fields					= 	array('title','stright_prize_heading','stright_prize_type','stright_prize1','stright_prize2','stright_prize3','stright_prize4','stright_prize5','stright_prize6','stright_prize7','rumble_mix_prize_heading' ,'rumble_mix_prize_type','rumble_mix_prize1','rumble_mix_prize2','rumble_mix_prize3','rumble_mix_prize4','rumble_mix_prize5','rumble_mix_prize6','rumble_mix_prize7','reverse_prize_heading','reverse_prize_type','reverse_prize1','reverse_prize2','reverse_prize3' ,'reverse_prize4','reverse_prize5' ,'reverse_prize6'  ,'lotto_type'  ,'enable_stright_prize_heading','enable_reverse_prize_heading','enable_rumble_mix_prize_heading','enable_title','btc_heading','btc_prize_text' );
						$product_prise_data 			= 	$this->geneal_model->getParticularDataByParticularField($Prize_Fields ,'uw_prize', 'product_id', $info2['products_id']);

						if($product_prise_data <> ''):
							$info2['product_prise_data']  = $product_prise_data;
						else:
							$info2['product_prise_data']  = '';
						endif;

						array_push($ourCampaigns,$info2);
					endif;
				endforeach;
			endif;

			$NewourCampaigns = array();
			foreach ($ourCampaigns as $key => $items):
				 if(!empty($items['product_prise_data'])):
			 		$NewourCampaigns[] = $items;
				 endif;
			endforeach;
			$ourCampaigns = $NewourCampaigns;
			$result['ourCampaigns'] 	=	$ourCampaigns;

			echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}
	// public function getProductListPageData()
	// {	
	// 	$apiHeaderData 		=	getApiHeaderData();
	// 	$this->generatelogs->putLog('APP',logOutPut($_POST));
	// 	$result 			= 	array();	
	// 	if(requestAuthenticate(APIKEY,'GET')):
			
	// 		$ourCampaigns 	  =	array();
	// 		$P_tblName 	  	  = 'uw_products';
	// 		$P_fileds		  = array('product_image','title','description','straight_add_on_amount','rumble_add_on_amount','reverse_add_on_amount','lotto_type','lotto_range','draw_date','draw_time','products_id','campaign_auto_freezing_mode','campaign_freezing_end_time','campaign_freezing_start_time','reverse_settings_default_check','rumble_settings_default_check','straight_settings_default_check','product_name','enable_number_prefix','lotto_range_prefix','lotto_range_start','lotto_range_end','ticket_number_repeat','app_image','reverse_settings','rumble_settings','straight_settings','straight_game_name','rumble_game_name','reverse_game_name' ,'straight_settings_default_check','rumble_settings_default_check','reverse_settings_default_check','campaign_price','show_ticket_for_campaign','campaign_price','ticket_count_per_campaign','show_ticket_for_campaign','show_on'
	// 		);

	// 		$P_where['where']['status']  = "A";
	// 		$P_where['where']['show_on'] = $this->input->get('show_on');
	// 		$P_where['where']['stock'] 	 = array('$gt'=> 0);
	// 		$P_where['where']['remarks'] = "lotto-products";
	// 		$shortField   	  			 = array('seq_order' => -1 );
	// 		$data2	  		  			 = $this->common_model->getDataByNewQuery($P_fileds,'multiple',$P_tblName,$P_where , $shortField);

	// 		$USERID = $this->input->get('users_id');
	// 		$whereCon['where'] 		=   array( 'status'=> 'A');
	// 		$PermissionDetails		=	$this->common_model->getData('single','uw_allowed_campaigns_permission',$whereCon);
	// 		// if($USERID):

	// 		// Product Settings.... 
	// 		$whereConSettings['where']   = array( 'status'=> 'A');
    //     	$settings                    = $this->common_model->getData('single','uw_settings',$whereConSettings);
	// 		//Products settings start
    //         $straight_settings               = $settings['straight_settings'];
    //         $rumble_settings                 = $settings['rumble_settings'];
    //         $reverse_settings                = $settings['reverse_settings'];
            
    //         $straight_game_name              = $settings['straight_game_name'];
    //         $rumble_game_name                = $settings['rumble_game_name'];
    //         $reverse_game_name               = $settings['reverse_game_name'];
            
    //         $reverse_settings_default_check  = $settings['reverse_settings_default_check'];
    //         $rumble_settings_default_check   = $settings['rumble_settings_default_check'];
    //         $straight_settings_default_check = $settings['straight_settings_default_check'];
    //         $game_description                = $settings['game_description'];
    //         $game_rule_image                 = $settings['game_rule_image'];
            
    //         $campaign_auto_freezing_mode     = $settings['campaign_auto_freezing_mode'];
    //         $campaign_freezing_end_time      = $settings['campaign_freezing_end_time'];
    //         $campaign_freezing_start_time    = $settings['campaign_freezing_start_time'];
    //         $show_ticket_for_campaign    	 = $settings['show_ticket_for_campaign'];
    //         //Products settings End 

	// 		$productData  			= array();
	// 		foreach($data2 as $iTems):

	// 			if($iTems['straight_game_name'] == '' && $iTems['rumble_game_name'] == '' && $iTems['reverse_game_name'] == "" ):
	// 				$iTems['straight_game_name'] = $straight_game_name;
	// 				$iTems['rumble_game_name']   = $rumble_game_name;
	// 				$iTems['reverse_game_name']  = $reverse_game_name;
	// 			endif;

	// 			if($iTems['straight_settings'] == '' && $iTems['rumble_settings'] == '' && $iTems['reverse_settings'] == "" ):
	// 				$iTems['straight_settings'] = $straight_settings;
	// 				$iTems['rumble_settings']   = $rumble_settings;
	// 				$iTems['reverse_settings']  = $reverse_settings;
	// 			endif;

	// 			if($iTems['reverse_settings_default_check'] == '' && $iTems['rumble_settings_default_check'] == '' && $iTems['straight_settings_default_check'] == "" ):
	// 				$iTems['straight_settings_default_check'] = $straight_settings_default_check;
	// 				$iTems['rumble_settings_default_check']   = $rumble_settings_default_check;
	// 				$iTems['reverse_settings_default_check']  = $reverse_settings_default_check;
	// 			endif;

	// 			if($iTems['campaign_auto_freezing_mode']  == "" || $iTems['campaign_freezing_end_time']  == ""|| $iTems['campaign_freezing_start_time']  == ""):
	// 				$iTems['campaign_auto_freezing_mode'] 	= $campaign_auto_freezing_mode;
	// 				$iTems['campaign_freezing_end_time']   	= $campaign_freezing_end_time;
	// 				$iTems['campaign_freezing_start_time']  = $campaign_freezing_start_time;
	// 			endif;

	// 			if($iTems['show_ticket_for_campaign']  == ""):
	// 				$iTems['show_ticket_for_campaign'] 	= $show_ticket_for_campaign;
	// 			endif;

	// 			if(in_array($USERID, $PermissionDetails['seleted_users'])  &&  in_array($iTems['products_id'] , $PermissionDetails['seleted_campaign']) ):
	// 				 $productData[] = $iTems;
	// 			elseif(!in_array($USERID, $PermissionDetails['seleted_users'])  &&  !in_array($iTems['products_id'] , $PermissionDetails['seleted_campaign']) ):
	// 				 $productData[] = $iTems;
	// 			endif;
	// 		endforeach;
				
	// 		// endif;

	// 		if($productData):
	// 			foreach($productData as $info2):
	// 				$info2['product_name'] = $info2['title'];
					
	// 				$drawDate2 			= 	$info2['draw_date'].' '.$info2['draw_time'].':00';
	// 				$today2 			= 	date('Y-m-d H:i:s');

	// 				if(strtotime($drawDate2) > strtotime($today2)):
	// 					if($this->input->post('users_id')):
	// 						$prowhere['where']	=	array('users_id'=>(int)$this->input->post('users_id'),'product_id'=>(int)$info2['products_id']);
	// 						$prodData			=	$this->common_model->getData('single','uw_wishlist',$prowhere);
	// 						if($prodData):
	// 							if($prodData['wishlist_product'] == 'Y'):
	// 								$info2['wishlist_product']  = 'Y';
	// 							else:
	// 								$info2['wishlist_product']  = 'N';
	// 							endif; 
	// 						else:
	// 							$info2['wishlist_product']  	= 'N';
	// 						endif;
	// 					else:
	// 						$info2['wishlist_product']  		= 'N';
	// 					endif;

	// 					if($this->input->post('users_id')):
	// 						$USRwhere 					=	[ 'users_id' => (int)$this->input->post('users_id') ];
	// 						$USRtblName 				=	'uw_users';
	// 						$userDetails 				=	$this->geneal_model->getOnlyOneData($USRtblName, $USRwhere);
	// 						if($userDetails):
	// 							$productShareUrl  		= 	generateProductShareUrl($info2['products_id'],$this->input->post('users_id'),$userDetails['referral_code']);
	// 							$info2['share_url']  	= 	$productShareUrl;
	// 						else:	
	// 							$info2['share_url']  	= 	'';
	// 						endif;
	// 					else:
	// 						$info2['share_url']  		= 	'';
	// 					endif;

	// 					$Prize_Fields					= 	array('title','stright_prize_heading','stright_prize_type','stright_prize1','stright_prize2','stright_prize3','stright_prize4','stright_prize5','stright_prize6','stright_prize7','rumble_mix_prize_heading' ,'rumble_mix_prize_type','rumble_mix_prize1','rumble_mix_prize2','rumble_mix_prize3','rumble_mix_prize4','rumble_mix_prize5','rumble_mix_prize6','rumble_mix_prize7','reverse_prize_heading','reverse_prize_type','reverse_prize1','reverse_prize2','reverse_prize3' ,'reverse_prize3','reverse_prize5' ,'reverse_prize6'  ,'lotto_type'  ,'enable_stright_prize_heading','enable_reverse_prize_heading','enable_rumble_mix_prize_heading','enable_title' );
	// 					$product_prise_data 			= 	$this->geneal_model->getParticularDataByParticularField($Prize_Fields ,'uw_prize', 'product_id', $info2['products_id']);

	// 					if($product_prise_data <> ''):
	// 						$info2['product_prise_data']  = $product_prise_data;
	// 					else:
	// 						$info2['product_prise_data']  = '';
	// 					endif;

	// 					array_push($ourCampaigns,$info2);
	// 				endif;
	// 			endforeach;
	// 		endif;

	// 		$NewourCampaigns = array();
	// 		foreach ($ourCampaigns as $key => $items):
	// 			 if(!empty($items['product_prise_data'])):
	// 		 		$NewourCampaigns[] = $items;
	// 			 endif;
	// 		endforeach;
	// 		$ourCampaigns = $NewourCampaigns;
	// 		$result['ourCampaigns'] 	=	$ourCampaigns;

	// 		echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
	// 	else:
	// 		echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
	// 	endif;
	// }

	/* * *********************************************************************
	 * * Function name  : paymentCapture
	 * * Developed By   : Dilip Halder
	 * * Purpose    	: This function capture payment.
	 * * Date 			: 27 October 2023
	 * * Updated By   	: Dilip Halder
	 * * Updated Date 	: 12 April 2024
	 * * **********************************************************************/
	// public function paymentCapture()
	// {	
	// 	$apiHeaderData 		=	getApiHeaderData();
	// 	$this->generatelogs->putLog('APP',logOutPut($_POST));
	// 	$result 							= 	array();	
	// 	if(requestAuthenticate(APIKEY,'POST')):

	// 		// Campaign Freezing code start here..
	// 		  $whereCon['where']  = array('status' => 'A');
	// 	 	  $campaignFreezing 	= $this->common_model->getData('single','uw_campaign_freezing',$whereCon);
	// 	 	  if($campaignFreezing['campaign_freezing'] == 'enable'):
	// 	 		echo outPut(0,lang('SUCCESS_CODE'),$campaignFreezing['freezing_title'],$result);die();
	// 	 	  endif;
	// 		// Campaign Freezing code end here..
	// 		if( $this->input->get('users_id') == ''): 
	// 			echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);

	// 		elseif( $this->input->post('prize_title') == ''): 
	// 			echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);

	// 		// elseif( $this->input->post('first_name') == ''): 
	// 		// 	echo outPut(0,lang('SUCCESS_CODE'),lang('EMPRT_FIRST_NAME'),$result);

	// 		// elseif( $this->input->post('last_name') == ''): 
	// 		// 	echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_LAST_NAME'),$result);

	// 		// elseif( $this->input->post('product_is_donate') == ''): 
	// 		// 	echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_IS_DONATE'),$result);
			
	// 		elseif( $this->input->post('product_id') == ''): 
	// 			echo outPut(0,lang('SUCCESS_CODE'),lang('PRODUCT_ID_EMPTY'),$result);

	// 		elseif( $this->input->post('product_title') == ''): 
	// 			echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_PRODUCT_TITLE'),$result);

	// 		elseif( $this->input->post('product_qty') == ''): 
	// 			echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_PRODUCT_QTY'),$result);

	// 		elseif( $this->input->post('draw_date') == ''): 
	// 			echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_DRAW_DATE'),$result);

	// 		elseif( $this->input->post('lotto_type') == ''): 
	// 			echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_LOTTO_TYPE'),$result);

	// 		// elseif( $this->input->post('users_email') == ''): 
	// 		// 	echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_USER_EMAIL'),$result);

	// 		elseif( $this->input->post('subtotal') == ''): 
	// 			echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_SUBTOTAL'),$result);

	// 		// elseif( $this->input->post('country_code') == ''): 
	// 		// 	echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_COUNTRYCODE'),$result);

	// 		// elseif( $this->input->post('users_mobile') == ''): 
	// 		// 	echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_USERMOBILE'),$result);

	// 		// elseif( $this->input->post('SMS') == ''): 
	// 		// 	echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_SMS'),$result);

	// 		elseif( $this->input->post('device_type') == ''): 
	// 			echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_DEVICE_TYPE'),$result);

	// 		elseif( $this->input->post('app_version') == ''): 
	// 			echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_APP_VERSION'),$result);

	// 		elseif( $this->input->post('ticket') == '' &&  $this->input->post('raffle_mode') != 'Y' ): 
	// 			echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_TICKET'),$result);

	// 		else:

	// 			$product_id  = $this->input->post('product_id');
	// 			$tableName 		   = 'uw_products';
	// 			$whereCon['where'] = array('products_id'=> (int)$product_id ,'status' => "A");
	// 			$shortField 	   = array('products_id' => -1);
	// 			$ProductData       = $this->common_model->getData('single',$tableName, $whereCon, $shortField);

	// 			$DrawDateNTime     = $ProductData['draw_date'].' '.$ProductData['draw_time'];
	// 			$currentDatentime  = date('Y-m-d H:i');
				
				 
	// 			if($ProductData['products_id'] != $product_id  || strtotime($DrawDateNTime) < strtotime($currentDatentime)  || strtotime($this->input->post('draw_date')) != strtotime($ProductData['draw_date'])):
	// 				echo outPut(0,lang('SUCCESS_CODE'),lang('RESTART_APP'),$result);die();
	// 			endif;  
				
	// 			// Check Product availability
	// 			$productID 			= $this->input->post('product_id');
	// 			$productQty 		= $this->input->post('product_qty');
	// 			$productIsDonated 	= $this->input->post('product_id');
	// 			$Plateform 			= 'app';
	// 			$CouponGenerate 	= '';
	// 			$USER['USERID']		= $this->input->get('users_id');
	// 			$USER['total_price']= $this->input->post('total_price');
				
	// 			// Test campaign restriction for not allowed users.
	// 			$whereCon['where'] 			=   array( 'status'=> 'A');
	// 			$TestCampaignData			=	$this->common_model->getData('single','uw_allowed_campaigns_permission',$whereCon);
	// 			if(in_array($productID, $TestCampaignData['seleted_campaign']) && !in_array($USER['USERID'], $TestCampaignData['seleted_users'])):
	// 				echo outPut(0,lang('SUCCESS_CODE'),lang('DEMO_CAMPAIGN'),$result);die();
	// 			endif;

	// 			//complete validation..
	// 			$result 			=  $this->common_model->CheckAvailableTickets($productID,$productQty ,$productIsDonated,$Plateform,$CouponGenerate,$USER);

				

	// 			//Update stock
	// 			// $this->geneal_model->updateStock($productID,$productQty);

	// 			$tbl_name  		= 'uw_users';
	// 			$whereCon  		= array('users_id'  =>(int)$USER['USERID'] ,'status'=> 'A');
	// 			$sellerDetails  = $this->geneal_model->getOnlyOneData($tbl_name, $whereCon);
				
	// 			if($sellerDetails['app_version'] != $this->input->post('app_version')):
	// 				$updateParams['app_version'] 	= $this->input->post('app_version');
	// 				$updateParams["updated_at"]     = date('Y-m-d H:i');
	// 				$this->common_model->editData('uw_users', $updateParams, 'users_id', (int)$sellerDetails['users_id']);
	// 			endif;

	// 			$user_oid 			   = $sellerDetails['_id']->{'$id'};
	// 			$commission_percentage = $sellerDetails['commission_percentage'];

	// 			/* ----- Raffle Mode Addon code  ---------*/
	// 			$raffle_mode 	= $this->input->post('raffle_mode');
	// 			if($raffle_mode == "Y"):
	// 				$quantity 	    = $this->input->post('product_qty');
	// 				$reffle_prefix  = $ProductData['reffle_prefix'];
	// 				$reffle_length  = $ProductData['reffle_length'];
	// 				$raffle_tickets = $this->common_model->generateRaffle($quantity,$reffle_prefix ,$reffle_length);
	// 			endif;
	// 			/* ----- Raffle Mode Addon code  ---------*/
				
	// 			$ORparam["sequence_id"]		    		=	(int)$this->geneal_model->getNextSequence('uw_lotto_orders');
	// 	        $ORparam["user_oid"] 					=	new MongoDB\BSON\ObjectId($user_oid);
	// 	        $ORparam["order_id"]		        	=	$this->geneal_model->getNextUWINOrderId();
	// 	        $ORparam["draw_id"]		    			=	(int)$ProductData['draw_id']; 
	// 	        $ORparam["order_code"]		    		=	base64_encode(rand(1000,9999)); 
	// 	        $ORparam["user_id"] 					=	(int)$this->input->get('users_id');
	// 	        $ORparam["user_type"] 					=	$sellerDetails['users_type']; 
	//         	$ORparam["user_email"] 					=   $sellerDetails['users_email'];	
	// 		 	$ORparam["user_phone"] 					=	$sellerDetails['users_mobile'];	
	// 		 	$ORparam["store_name"] 					=	$sellerDetails['store_name'];
	// 		 	$ORparam["pos_number"] 					=	(int)$sellerDetails['pos_number'];
	// 		 	$ORparam["pos_device_id"] 				=	$this->input->post('pos_device_id'); // $sellerDetails['pos_device_id'];
	// 	     	$ORparam["product_id"] 					=	(int)$this->input->post('product_id');
	// 	     	$ORparam["product_title"] 				=	$this->input->post('product_title');
	// 	     	$ORparam["product_qty"] 				=	$this->input->post('product_qty');
	// 	     	$ORparam["prize_title"] 				=	$this->input->post('prize_title');
	// 	        $ORparam["vat_amount"] 					=	(float)$this->input->post('vat_amount');
	// 	        $ORparam["straight_add_on_amount"] 		=	(float)$this->input->post('straight_add_on_amount');
	// 	        $ORparam["rumble_add_on_amount"] 		=	(float)$this->input->post('rumble_add_on_amount');
	// 	        $ORparam["reverse_add_on_amount"] 		=	(float)$this->input->post('reverse_add_on_amount');
	// 	        $ORparam["subtotal"] 					=	(float)$this->input->post('subtotal');
	// 	        $ORparam["total_price"] 				=	(float)$this->input->post('total_price');
	// 	        $ORparam["availableArabianPoints"] 		=	(float)$sellerDetails["availableArabianPoints"];
	// 			$ORparam["end_balance"] 				=	(float)$sellerDetails["availableArabianPoints"] - (float)$ORparam["total_price"] ;
	// 		    $ORparam["payment_mode"] 				=	'UPoints';
	// 	        $ORparam["product_is_donate"] 			=	$this->input->post('product_is_donate'); //$this->input->post('product_is_donate');
	// 		    $ORparam["order_status"] 				=	"Success";
	// 		    $ORparam["device_type"] 				=	$this->input->post('device_type');
   	// 			$ORparam["app_name"] 					=	$this->input->post('app_name');
   	// 			$ORparam["app_version"] 				=	$this->input->post('app_version');
   	// 			$ORparam["ticket"] 						=	$this->input->post('ticket');
   	// 			$ORparam["selection_values"] 			=	$this->input->post('selection_values');
	// 		    $ORparam["status"] 						=	"A";
	// 	     	$ORparam["order_first_name"] 			=	$this->input->post('first_name');
	// 	     	$ORparam["order_last_name"] 			=	$this->input->post('last_name');
	// 	     	$ORparam["order_users_country_code"] 	=	$this->input->post('country_code');
	// 	     	$ORparam["order_users_mobile"] 			=	$this->input->post('users_mobile');
	// 	     	$ORparam["order_users_email"] 			=	$this->input->post('users_email');
	// 	        $ORparam["commission_percentage"] 		=	$commission_percentage; 
	// 	     	$ORparam["SMS"] 						=	$this->input->post('SMS');
	// 	     	if(!empty($raffle_tickets)):
	// 				$ORparam['raffle_mode']			    = $raffle_mode;
	// 				$ORparam['raffle_tickets']		    = $raffle_tickets;
	// 			endif;
	// 		    $ORparam["creation_ip"] 				=	$this->input->post('ip_address');
	// 		    $ORparam["created_at"] 					=	date('Y-m-d H:i');
	// 		    //Saving order details for Ticket
	// 		    $orderInsertID 							=	$this->geneal_model->addData('uw_lotto_orders', $ORparam);
			  	
	// 		  	// Deduct the purchesed points and get available arabian points of user.
	// 			$currentBal 							= 	$this->geneal_model->debitPointsByAPI($USER['total_price'],$USER['USERID']); 

	// 	     	// Order capturing in order uw_loadbalance table..
	// 	     		$narration1 = $raffle_mode =='Y'? 'Raffle Order':'Order';
	// 			    $fromuserparam["load_balance_id"]		 =	(int)$this->geneal_model->getNextSequence('uw_loadBalance');
	// 				$fromuserparam["order_oid"] 			 =	new MongoDB\BSON\ObjectId($orderInsertID['_id']->{'$id'});
	// 				$fromuserparam["user_oid"] 				 =	new MongoDB\BSON\ObjectId($user_oid);
	// 				$fromuserparam["product_oid"] 			 =	new MongoDB\BSON\ObjectId($ProductData['_id']->{'$id'});
	// 				$fromuserparam["product_qty"] 			 =	(int)$orderInsertID["product_qty"];
	// 				$fromuserparam["user_id_deb"]			 =	(int)$this->input->get('users_id');
	// 				$fromuserparam["order_id"] 				 =	$orderInsertID['order_id'];
	// 				$fromuserparam["user_id_cred"] 			 =	(int)0;
	// 				$fromuserparam["upoints"] 				 =	(float)$orderInsertID['total_price'];
	// 				$fromuserparam["availableArabianPoints"] =	(float)$orderInsertID['availableArabianPoints'];
	// 				$fromuserparam["end_balance"] 			 =	(float)$orderInsertID['end_balance'];
	// 			    $fromuserparam["record_type"] 			 =	'Debit';
	// 			    $fromuserparam["narration"]				 =	$narration1;
	// 			    $fromuserparam["remarks"]				 =	'Ticket ID : '.$orderInsertID['order_id'];
	// 			    $fromuserparam["creation_ip"] 			 =	$this->input->ip_address();
	// 			    $fromuserparam["created_at"] 			 =	date('Y-m-d H:i');
	// 			    $fromuserparam["created_by"] 			 =	(int)$this->input->get('users_id');
	// 			    $fromuserparam["status"] 				 =	"A";
	// 		    	$this->geneal_model->addData('uw_loadBalance', $fromuserparam);
	// 	    	/* Order capturing code start here.  End */


	// 	    	//Commission code start here.
	// 		    	$totalPrice 			 = (float)$orderInsertID['total_price'];
	// 				$commission_percentage   = $sellerDetails['commission_percentage'];
	// 				// Calculate commission amount
	// 				$commission_amount 		 = ($totalPrice * $commission_percentage) / 100;
	// 				$narration 			     =  $raffle_mode =='Y'? 'Raffle Commission':'Commission';
	// 		     	// Commission capturing in order uw_loadbalance table..
	// 			    $commissionParam["load_balance_id"]		 	 =	(int)$this->geneal_model->getNextSequence('uw_loadBalance');
	// 				$commissionParam["order_oid"] 			 	 =	new MongoDB\BSON\ObjectId($orderInsertID['_id']->{'$id'});
	// 				$commissionParam["user_oid"] 				 =	new MongoDB\BSON\ObjectId($user_oid);
	// 				$commissionParam["product_oid"] 			 =	new MongoDB\BSON\ObjectId($ProductData['_id']->{'$id'});
	// 				$commissionParam["product_qty"] 			 =	(int)$orderInsertID["product_qty"];
	// 				$commissionParam["user_id_cred"] 			 =	(int)$this->input->get('users_id');
	// 				$commissionParam["user_id_deb"]			 	 =	(int)0;
	// 				$commissionParam["order_id"] 				 =	$orderInsertID['order_id'];
	// 				$commissionParam["upoints"] 				 =	(float)$commission_amount;
	// 				$commissionParam["availableArabianPoints"] 	 =	(float)$orderInsertID['end_balance'];
	// 				$commissionParam["end_balance"] 			 =	(float)$orderInsertID['end_balance']+$commission_amount;
	// 			    $commissionParam["record_type"] 			 =	'Credit';
	// 			    $commissionParam["narration"]			 	 =	$narration;
	// 			    $commissionParam["remarks"]				 	 =	'Ticket ID : '.$orderInsertID['order_id'];
	// 			    $commissionParam["creation_ip"] 			 =	$this->input->ip_address();
	// 			    $commissionParam["created_at"] 				 =	date('Y-m-d H:i');
	// 			    $commissionParam["created_by"] 			 	 =	(int)$this->input->get('users_id');
	// 			    $commissionParam["status"] 				 	 =	"A";
	// 		    	$this->geneal_model->addData('uw_loadBalance', $commissionParam);
	// 		    	// Credit the purchesed points and get available arabian points of user.
					
	// 				$this->geneal_model->creaditPoints($commission_amount,$orderInsertID["user_id"]); 
	// 	    		/*  Commission code start here.  End */

	// 		    $result = $ORparam;
	// 			echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);

	// 		endif;
	// 	else:
	// 		echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
	// 	endif;
	// }

	/* * *********************************************************************
	 * * Function name  : paymentCapture
	 * * Developed By   : Dilip Halder
	 * * Purpose    	: This function capture payment.
	 * * Date 			: 27 October 2023
	 * * Updated By   	: Dilip Halder
	 * * Updated Date 	: 12 April 2024
	 * * **********************************************************************/
	public function paymentCapture()
	{	
		// echo "working";die;
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):

			$this->session->sess_regenerate();
			// Campaign Freezing code start here..
			  $whereCon['where']  = array('status' => 'A');
		 	  $campaignFreezing 	= $this->common_model->getData('single','uw_campaign_freezing',$whereCon);
		 	  if($campaignFreezing['campaign_freezing'] == 'enable'):
		 		echo outPut(0,lang('SUCCESS_CODE'),$campaignFreezing['freezing_title'],$result);die();
		 	  endif;
			// Campaign Freezing code end here..
			
			if( $this->input->get('users_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);

			elseif( $this->input->post('prize_title') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);

			// elseif( $this->input->post('first_name') == ''): 
			// 	echo outPut(0,lang('SUCCESS_CODE'),lang('EMPRT_FIRST_NAME'),$result);

			// elseif( $this->input->post('last_name') == ''): 
			// 	echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_LAST_NAME'),$result);

			// elseif( $this->input->post('product_is_donate') == ''): 
			// 	echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_IS_DONATE'),$result);
			
			elseif( $this->input->post('product_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('PRODUCT_ID_EMPTY'),$result);

			elseif( $this->input->post('product_title') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_PRODUCT_TITLE'),$result);

			elseif( $this->input->post('product_qty') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_PRODUCT_QTY'),$result);

			elseif( $this->input->post('draw_date') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_DRAW_DATE'),$result);

			elseif( $this->input->post('lotto_type') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_LOTTO_TYPE'),$result);

			// elseif( $this->input->post('users_email') == ''): 
			// 	echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_USER_EMAIL'),$result);

			elseif( $this->input->post('subtotal') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_SUBTOTAL'),$result);

			// elseif( $this->input->post('country_code') == ''): 
			// 	echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_COUNTRYCODE'),$result);

			// elseif( $this->input->post('users_mobile') == ''): 
			// 	echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_USERMOBILE'),$result);

			// elseif( $this->input->post('SMS') == ''): 
			// 	echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_SMS'),$result);

			elseif( $this->input->post('device_type') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_DEVICE_TYPE'),$result);

			elseif( $this->input->post('app_version') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_APP_VERSION'),$result);

			elseif( $this->input->post('ticket') == '' &&  $this->input->post('raffle_mode') != 'Y' ): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_TICKET'),$result);

			else: 
				
				// try{
				// 		$session = $this->mongo_db->startSession();
				// 		$session->startTransaction();
				// 	}catch(Exception $e){
				// 	echo outPut(0,lang('BAD_REQUEST_CODE'),'error',$e);
				// }
				$session = $this->mongodb_client->client->startSession();
				$session->startTransaction();

				try{

				
					$product_id  = $this->input->post('product_id');
				$tableName 		   = 'uw_products';
				$whereCon['where'] = array('products_id'=> (int)$product_id ,'status' => "A");
				$shortField 	   = array('products_id' => -1);
				$ProductData       = $this->common_model->getData('single',$tableName, $whereCon, $shortField);

				$DrawDateNTime     = $ProductData['draw_date'].' '.$ProductData['draw_time'];
				$currentDatentime  = date('Y-m-d H:i');
				
				if($ProductData['products_id'] != $product_id  || strtotime($DrawDateNTime) < strtotime($currentDatentime)  || strtotime($this->input->post('draw_date')) != strtotime($ProductData['draw_date'])):
					
					echo outPut(0,lang('SUCCESS_CODE'),lang('RESTART_APP'),$product_id);die();
				endif;  
				
				// Check Product availability
				$productID 			= $this->input->post('product_id');
				$productQty 		= $this->input->post('product_qty');
				$productIsDonated 	= $this->input->post('product_id');
				$Plateform 			= 'app';
				$CouponGenerate 	= '';
				$USER['USERID']		= $this->input->get('users_id');
				$USER['total_price']= $this->input->post('total_price');
				
				// Test campaign restriction for not allowed users.
				$whereCon['where'] 			=   array( 'status'=> 'A');
				$TestCampaignData			=	$this->common_model->getData('single','uw_allowed_campaigns_permission',$whereCon);
				if(in_array($productID, $TestCampaignData['seleted_campaign']) && !in_array($USER['USERID'], $TestCampaignData['seleted_users'])):
					echo outPut(0,lang('SUCCESS_CODE'),lang('DEMO_CAMPAIGN'),$result);die();
				endif;

				//complete validation..
				$result 			=  $this->common_model->CheckAvailableTickets($productID,$productQty ,$productIsDonated,$Plateform,$CouponGenerate,$USER);

				

				//Update stock
				// $this->geneal_model->updateStock($productID,$productQty);

				$tbl_name  		= 'uw_users';
				$whereCon  		= array('users_id'  =>(int)$USER['USERID'] ,'status'=> 'A');
				$sellerDetails  = $this->geneal_model->getOnlyOneData($tbl_name, $whereCon);
				// $sellerDetails=[];
				// print_r($sellerDetails);die();
				if($sellerDetails['app_version'] != $this->input->post('app_version')):
					$updateParams['app_version'] 	= $this->input->post('app_version');
					$updateParams["updated_at"]     = date('Y-m-d H:i');
					$this->common_model->editData('uw_users', $updateParams, 'users_id', (int)$sellerDetails['users_id']);
				endif;

				$user_oid 			   = $sellerDetails['_id']->{'$id'};
				$commission_percentage = $sellerDetails['commission_percentage'];

				/* ----- Raffle Mode Addon code  ---------*/
				$raffle_mode 	= $this->input->post('raffle_mode');
				if($raffle_mode == "Y"):
					$quantity 	    = $this->input->post('product_qty');
					$reffle_prefix  = $ProductData['reffle_prefix'];
					$reffle_length  = $ProductData['reffle_length'];
					$raffle_tickets = $this->common_model->generateRaffle($quantity,$reffle_prefix ,$reffle_length);
				endif;
				/* ----- Raffle Mode Addon code  ---------*/
				
				$ORparam["sequence_id"]		    		=	(int)$this->geneal_model->getNextSequence('uw_lotto_orders');
		        $ORparam["user_oid"] 					=	new MongoDB\BSON\ObjectId($user_oid);
		        $ORparam["order_id"]		        	=	$this->geneal_model->getNextUWINOrderId();
		        $ORparam["draw_id"]		    			=	(int)$ProductData['draw_id']; 
		        $ORparam["order_code"]		    		=	base64_encode(rand(1000,9999)); 
		        $ORparam["user_id"] 					=	(int)$this->input->get('users_id');
		        $ORparam["user_type"] 					=	$sellerDetails['users_type']; 
	        	$ORparam["user_email"] 					=   $sellerDetails['users_email'];	
			 	$ORparam["user_phone"] 					=	$sellerDetails['users_mobile'];	
			 	$ORparam["store_name"] 					=	$sellerDetails['store_name'];
			 	$ORparam["pos_number"] 					=	(int)$sellerDetails['pos_number'];
			 	$ORparam["pos_device_id"] 				=	$this->input->post('pos_device_id'); // $sellerDetails['pos_device_id'];
		     	$ORparam["product_id"] 					=	(int)$this->input->post('product_id');
		     	$ORparam["product_title"] 				=	$this->input->post('product_title');
		     	$ORparam["product_qty"] 				=	$this->input->post('product_qty');
		     	$ORparam["prize_title"] 				=	$this->input->post('prize_title');
		        $ORparam["vat_amount"] 					=	(float)$this->input->post('vat_amount');
		        $ORparam["straight_add_on_amount"] 		=	(float)$this->input->post('straight_add_on_amount');
		        $ORparam["rumble_add_on_amount"] 		=	(float)$this->input->post('rumble_add_on_amount');
		        $ORparam["reverse_add_on_amount"] 		=	(float)$this->input->post('reverse_add_on_amount');
		        $ORparam["subtotal"] 					=	(float)$this->input->post('subtotal');
		        $ORparam["total_price"] 				=	(float)$this->input->post('total_price');
		        $ORparam["availableArabianPoints"] 		=	(float)$sellerDetails["availableArabianPoints"];
				$ORparam["end_balance"] 				=	(float)$sellerDetails["availableArabianPoints"] - (float)$ORparam["total_price"] ;
			    $ORparam["payment_mode"] 				=	'UPoints';
		        $ORparam["product_is_donate"] 			=	$this->input->post('product_is_donate'); //$this->input->post('product_is_donate');
			    $ORparam["order_status"] 				=	"Success";
			    $ORparam["device_type"] 				=	$this->input->post('device_type');
   				$ORparam["app_name"] 					=	$this->input->post('app_name');
   				$ORparam["app_version"] 				=	$this->input->post('app_version');
   				$ORparam["ticket"] 						=	$this->input->post('ticket');
   				$ORparam["selection_values"] 			=	$this->input->post('selection_values');
			    $ORparam["status"] 						=	"A";
		     	$ORparam["order_first_name"] 			=	$this->input->post('first_name');
		     	$ORparam["order_last_name"] 			=	$this->input->post('last_name');
		     	$ORparam["order_users_country_code"] 	=	$this->input->post('country_code');
		     	$ORparam["order_users_mobile"] 			=	$this->input->post('users_mobile');
		     	$ORparam["order_users_email"] 			=	$this->input->post('users_email');
		        $ORparam["commission_percentage"] 		=	$commission_percentage; 
		     	$ORparam["SMS"] 						=	$this->input->post('SMS');
		        $ORparam["is_printed"] 					=	'N'; 
			     	if(!empty($raffle_tickets)):
					$ORparam['raffle_mode']			    = $raffle_mode;
					$ORparam['raffle_tickets']		    = $raffle_tickets;
				endif;
			    $ORparam["creation_ip"] 				=	$this->input->post('ip_address');
			    $ORparam["created_at"] 					=	date('Y-m-d H:i');
			    //Saving order details for Ticket
			   
			    $orderInsertID = $this->mongodb_client->insertDocument('uw_lotto_orders', $ORparam, $session);	
			    $o_id  = (string) $orderInsertID['_id'];
			    // echo $ProductData['_id']->{'$id'};
			    //  print_r($ProductData);
			    // die();
			    // $this->geneal_model->addData('uw_lotto_orders', $ORparam);
			  	
			  	// Deduct the purchesed points and get available arabian points of user.
				$currentBal 							= 	$this->geneal_model->debitPointsByAPI($USER['total_price'],$USER['USERID']); 

		     	// Order capturing in order uw_loadbalance table..
		     		$narration1 = $raffle_mode =='Y'? 'Raffle Order':'Order';
				    $fromuserparam["load_balance_id"]		 =	(int)$this->geneal_model->getNextSequence('uw_loadBalance');
					// $fromuserparam["order_oid"] 			 =	new MongoDB\BSON\ObjectId($orderInsertID['_id']->{'$id'});
					$fromuserparam["order_oid"] 			 =	new MongoDB\BSON\ObjectId($o_id);
					// ècho $orderInsertID['_id']->{'$id'};
					$fromuserparam["user_oid"] 				 =	new MongoDB\BSON\ObjectId($user_oid);
					$fromuserparam["product_oid"] 			 =	new MongoDB\BSON\ObjectId($ProductData['_id']->{'$id'});
					$fromuserparam["product_qty"] 			 =	(int)$orderInsertID["product_qty"];
					$fromuserparam["user_id_deb"]			 =	(int)$this->input->get('users_id');
					$fromuserparam["order_id"] 				 =	$orderInsertID['order_id'];
					$fromuserparam["user_id_cred"] 			 =	(int)0;
					$fromuserparam["upoints"] 				 =	(float)$orderInsertID['total_price'];
					$fromuserparam["availableArabianPoints"] =	(float)$orderInsertID['availableArabianPoints'];
					$fromuserparam["end_balance"] 			 =	(float)$orderInsertID['end_balance'];
				    $fromuserparam["record_type"] 			 =	'Debit';
				    $fromuserparam["narration"]				 =	$narration1;
				    $fromuserparam["remarks"]				 =	'Ticket ID : '.$orderInsertID['order_id'];
				    $fromuserparam["creation_ip"] 			 =	$this->input->ip_address();
				    $fromuserparam["created_at"] 			 =	date('Y-m-d H:i');
				    $fromuserparam["created_by"] 			 =	(int)$this->input->get('users_id');
				    $fromuserparam["status"] 
				    				 =	"A";
			    	$fromuserinsertResult = $this->mongodb_client->insertDocument('uw_loadBalance', $fromuserparam, $session);
			    	// $this->geneal_model->addData('uw_loadBalance', $fromuserparam);
		    	/* Order capturing code start here.  End */


		    	//Commission code start here.
			    	$totalPrice 			 = (float)$orderInsertID['total_price'];
					$commission_percentage   = $sellerDetails['commission_percentage'];
					// Calculate commission amount
					$commission_amount 		 = ($totalPrice * $commission_percentage) / 100;
					$narration 			     =  $raffle_mode =='Y'? 'Raffle Commission':'Commission';
			     	// Commission capturing in order uw_loadbalance table..
				    $commissionParam["load_balance_id"]		 	 =	(int)$this->geneal_model->getNextSequence('uw_loadBalance');
					// $commissionParam["order_oid"] 			 	 =	new MongoDB\BSON\ObjectId($orderInsertID['_id']->{'$id'});
					$commissionParam["order_oid"] 			 	 =	new MongoDB\BSON\ObjectId($o_id);
					$commissionParam["user_oid"] 				 =	new MongoDB\BSON\ObjectId($user_oid);
					$commissionParam["product_oid"] 			 =	new MongoDB\BSON\ObjectId($ProductData['_id']->{'$id'});
					$commissionParam["product_qty"] 			 =	(int)$orderInsertID["product_qty"];
					$commissionParam["user_id_cred"] 			 =	(int)$this->input->get('users_id');
					$commissionParam["user_id_deb"]			 	 =	(int)0;
					$commissionParam["order_id"] 				 =	$orderInsertID['order_id'];
					$commissionParam["upoints"] 				 =	(float)$commission_amount;
					$commissionParam["availableArabianPoints"] 	 =	(float)$orderInsertID['end_balance'];
					$commissionParam["end_balance"] 			 =	(float)$orderInsertID['end_balance']+$commission_amount;
				    $commissionParam["record_type"] 			 =	'Credit';
				    $commissionParam["narration"]			 	 =	$narration;
				    $commissionParam["remarks"]				 	 =	'Ticket ID : '.$orderInsertID['order_id'];
				    $commissionParam["creation_ip"] 			 =	$this->input->ip_address();
				    $commissionParam["created_at"] 				 =	date('Y-m-d H:i');
				    $commissionParam["created_by"] 			 	 =	(int)$this->input->get('users_id');
				    $commissionParam["status"] 				 	 =	"A";
			    	// $this->geneal_model->addData('uw_loadBalance', $commissionParam);
			    	$commissioninsertResult =$this->mongodb_client->insertDocument('uw_loadBalance', $commissionParam, $session);
			    	// Credit the purchesed points and get available arabian points of user.
					
					$this->geneal_model->creaditPoints($commission_amount,$orderInsertID["user_id"]); 
		    		/*  Commission code start here.  End */

			    $result = $ORparam;
			    if($fromuserinsertResult && $commissioninsertResult ){
			    	 $session->commitTransaction();
			    	// $session->abortTransaction();
			    	 echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
			    }else{
			    	$session->abortTransaction();
			    	echo outPut(0,lang('BAD_REQUEST_CODE'),'Error: ',$e);
			    }
			    

				
					
				}catch(Exception $e){
					$session->abortTransaction();
					echo $e;
					echo outPut(0,lang('BAD_REQUEST_CODE'),'Error: ',$e);
				}
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}
	/* * *********************************************************************
	 * * Function name : orderHistory
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used to fetch order history.
	 * * Date : 27 October 2023
	 * * **********************************************************************/
	public function orderHistory()
	{

		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();

		if(requestAuthenticate(APIKEY,'GET')):
			
			if( $this->input->get('users_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			else:
				
				$tbl 					=	'uw_lotto_orders';
				$wcon['where']          =  array('user_id' => (int)$this->input->get('users_id'));
				$Sortdata 				=	array('sequence_id' => -1);
				
				$fields = array('order_id','product_qty', 'product_title','prize_title','status','ticket','created_at','total_price','order_code','selection_values','is_printed');
	 					
				// getDataByNewQuery($fields=array(),$action='',$tbl_name='',$wcon='',$shortField='',$num_page='',$cnt='')
				$userOrderList	=	$this->common_model->getDataByNewQuery($fields,'multiple', $tbl, $wcon,$Sortdata);

				if(!empty($userOrderList)):
					$results = $userOrderList;
					echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);	
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('DATA_NOT_FOUND'),$results);	
				endif;

			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : SummaryReportSearch
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to fetch order history.
	 * * Date 		   : 27 October 2023
	 * * **********************************************************************/
	public function SummaryReportSearch()
	{

		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();

		if(requestAuthenticate(APIKEY,'GET')):
			
			if( $this->input->get('users_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			else:
			  	$usersId = $this->input->get('users_id');

			  	$tbl 			= 'uw_lotto_orders';
			 	$product_title  = $this->input->get('product_title');
			  	$start_date     = date('Y-m-d 21:31' ,strtotime($this->input->get('start_date')) );
		      	$end_date 		= date('Y-m-d 21:30' ,strtotime($this->input->get('end_date')));

		      	// $RestrictDateTill = strtotime($this->input->get('start_date'));
		      	// $AllowedDate      = strtotime('2024-02-22 21:31');

		      	// if($RestrictDateTill< $AllowedDate):
				// 	echo outPut(0,lang('SUCCESS_CODE'),lang('DATA_NOT_FOUND'),$result);die();
		      	// endif;

			 	if(!empty($product_title)):
			 		$where['product_title'] = $product_title;
			 	endif;

			 	if($this->input->get('start_date') && $this->input->get('end_date') ):
			 		$where['created_at'] = array('$gte' => $start_date ,'$lte' => $end_date );
			 	elseif($this->input->get('start_date')):
			 		$where['created_at'] = array('$gte' => $start_date);
			 	elseif($this->input->get('end_date')):
			 		$where['created_at'] = array('$lte' => $end_date);
			 	endif;

				$shortField 			=	['coupon_id' => -1];
				$wcon1['where']         =  array('users_id' => (int)$usersId ,'status' => array('$ne'=> 'CL'));
				$userResult				=  $this->geneal_model->getData2('single', 'uw_users', $wcon1);

				$user_oid 					=  $userResult['_id']->{'$id'};
				// CommissionAmount 
				$commissionAmount 			=  $this->common_model->commissionListold($user_oid ,$where);
				$totalcancelOrder			=  $this->common_model->cancelOraderListold($user_oid,$where);
				
				

				$result['totalArabianPoints'] 	  = $userResult['totalArabianPoints'];
				$result['availableArabianPoints'] = $userResult['availableArabianPoints'];
				$result['redeemed_points']		  = $userResult['redeemed_points']?$userResult['redeemed_points']:0;
				// $result['total_profits']		  = $commissionAmount?$commissionAmount:0;
				$result['total_profits']		  = 0;
				$result['cancelled_orders']		  = $totalcancelOrder?$totalcancelOrder:0;

			 	$wcon1['where']  = array();
				$whereCon['user_id']         =  (int)$usersId;
				$whereCon['status']          =  array('$ne'=> 'CL');
				if($where['created_at']):
					$whereCon['created_at']  =  $where['created_at'];
				endif;
				if($where['created_at']):
					$whereCon['created_at']  =  $where['created_at'];
				endif;
				if(!empty($product_title)):
			 		$whereCon['product_title'] = $product_title;
			 	endif;

				$tbl 				= 'uw_lotto_orders';
				$wcon1['where']  	= $whereCon;
				$result['products']	=	$this->geneal_model->GetQuickOrderTotalCount('multiple', $tbl, $wcon1,$shortField);

				if($this->input->get('product_title') || $this->input->get('start_date')  || $this->input->get('end_date') ):
					$result['totalArabianPoints'] 	  =	$result['products'][0]['availableArabianPoints'];
					$result['availableArabianPoints'] =	$result['products'][0]['end_balance'];
				endif;

				if(!empty($result)):
					$results = $result;
					echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);	
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('DATA_NOT_FOUND'),$results);	
				endif;

			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : SummaryReportSearch
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to fetch order history.
	 * * Date 		   : 01 March 2024
	 * * Updated By    : Dilip halder
	 * * Date 		   : 25 May 2024
	 * * **********************************************************************/
	// public function newSummaryReportSearch()
	// {
	// 	$apiHeaderData 		=	getApiHeaderData();
	// 	$this->generatelogs->putLog('APP',logOutPut($_POST));
	// 	$result 			= 	array();

	// 	if(requestAuthenticate(APIKEY,'GET')):
			
	// 		if( $this->input->get('users_id') == ''): 
	// 			echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
	// 		else:
	// 		  	$usersId 		= $this->input->get('users_id');
	// 		  	$start_date     = date('Y-m-d H:i' ,strtotime($this->input->get('start_date')));
	// 	      	$end_date 		= date('Y-m-d H:i' ,strtotime($this->input->get('end_date')));
	// 	      	$product_title 	= $this->input->get('product_title');
	// 	      	$queue_seq_id 	= $this->input->get('queue_seq_id');

	// 	      	if($queue_seq_id):
	// 	      		$tbname  = 'uw_summery_queue';
	// 	      		$this->common_model->deleteData($tbname,'seq_id',(int)$queue_seq_id);
	// 	      	endif;

	// 		 	if($this->input->get('start_date') && $this->input->get('end_date') ):
	// 		 		$where['created_at'] = array('$gte' => $start_date ,'$lte' => $end_date );
	// 		 	elseif($this->input->get('start_date')):
	// 		 		$where['created_at'] = array('$gte' => $start_date);
	// 		 	elseif($this->input->get('end_date')):
	// 		 		$where['created_at'] = array('$lte' => $end_date);
	// 		 	endif;

	// 			$shortField 			=	['coupon_id' => -1];
	// 			$wcon1['where']         =  array('users_id' => (int)$usersId ,'status' => 'A');
	// 			$userResult				=  $this->geneal_model->getData2('single', 'uw_users', $wcon1);
	// 			$user_oid 				=  $userResult['_id']->{'$id'};

	// 			if($product_title):
	// 			$where['product_title'] = $product_title;
	// 			endif;
	// 			$where['user_id'] 		= (int)$usersId;
	// 			$where['status'] 		= 'A';
	// 			$wcon['where'] 			= $where;

	// 			$tblName2 				= 'uw_lotto_orders';
	// 			$shortField   			= array('product_id' => -1 );
	// 			$totalProductDetails	= $this->common_model->getsummeryReport('multiple',$tblName2 , $wcon , $shortField,$num_page,$cnt);
	// 			// Main - CommissionAmount 
	// 			$totalSales 			=  $this->common_model->totalSales($user_oid ,$where);
	// 			$commissionAmount 		=  $this->common_model->commissionList($user_oid ,$where);
	// 			$totalcancelOrderAmount	=  $this->common_model->cancelOraderList($user_oid,$where);
	// 			$totalCustomerPaid		=  $this->common_model->totalCustomerPaid($user_oid,$where);
	// 			$dueBalance				=  (float)$totalSales-$commissionAmount-$totalCustomerPaid;


	// 			//Campaign Wise Data Mapping..
	// 			if($totalProductDetails):
	// 				foreach($totalProductDetails as $key => $items):
	// 					// Order ID  ArrayList per Campaign.
	// 					$wcon['where']['product_id'] = $items['product_id'];
	// 					$tblNameOrder 				 = 'uw_lotto_orders';
	// 					$shortField   				 = array('product_id' => -1 );
	// 					$wcon['where']['status'] 	 =  array('$ne'  => '');
	// 					$OrderArrayList				 = $this->common_model->getFieldInArray('order_id',$tblNameOrder , $wcon);
	// 					// CommissionAmount 
	// 					$where['where_in']['order_id'] = $OrderArrayList;
	// 					// $totalProductDetails[$key]['totalSales'] 		 =  $this->common_model->totalSales($user_oid ,$where);
	// 					$totalProductDetails[$key]['commissionAmount'] 		 =  $this->common_model->commissionList($user_oid ,$where);
	// 					$totalProductDetails[$key]['totalcancelOrderAmount'] =  $this->common_model->cancelOraderList($user_oid,$where);

	// 					$where['product_id'] = (int)$items['product_id'];
	// 					$totalProductDetails[$key]['totalCustomerPaid']		 =  $this->common_model->totalCustomerPaid($user_oid,$where ,$idd);
	// 				endforeach;
	// 			endif;

	// 			$ProductTitle = $this->input->get('product_title');

	// 			if(empty($ProductTitle)):
	// 				$result['totalSales'] 	  		    = (string)$totalSales;
	// 				$result['commissionAmount'] 	    = (string)$commissionAmount;
	// 				$result['totalcancelOrderAmount'] 	= (string)$totalcancelOrderAmount;
	// 				$result['totalCustomerPaid'] 	    = (string)$totalCustomerPaid;
	// 				$result['dueBalance'] 	    		= (string)$dueBalance;
	// 				$result['totalProductDetails'] 	    = $totalProductDetails;
	// 			else:
	// 				$dueBalance							= (float)$totalProductDetails[0]['sales']-$totalProductDetails[0]['commissionAmount']-$totalProductDetails[0]['totalCustomerPaid'];
	// 				$result['totalSales'] 	  		    = (string)$totalProductDetails[0]['sales'];
	// 				$result['commissionAmount'] 	    = (string)$totalProductDetails[0]['commissionAmount'];
	// 				$result['totalcancelOrderAmount'] 	= (string)$totalProductDetails[0]['totalcancelOrderAmount'];
	// 				$result['totalCustomerPaid'] 	    = (string)$totalProductDetails[0]['totalCustomerPaid'];
	// 				$result['dueBalance'] 	    		= (string)$dueBalance;
	// 			endif;
				
	// 			if(!empty($result)):
	// 				$results = $result;
	// 				echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);	
	// 			else:
	// 				echo outPut(0,lang('SUCCESS_CODE'),lang('DATA_NOT_FOUND'),$results);	
	// 			endif;

	// 		endif;
	// 	else:
	// 		echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
	// 	endif;
	// }

	/* * *********************************************************************
	 * * Function name : SummaryReportSearch
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to fetch order history.
	 * * Date 		   : 01 March 2024
	 * * Updated By    : Dilip halder
	 * * Date 		   : 25 May 2024
	 * * **********************************************************************/
	public function newSummaryReportSearch()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 			= 	array();

		if(requestAuthenticate(APIKEY,'GET')):
			
			if( $this->input->get('users_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			else:
			  	$usersId 		= $this->input->get('users_id');
			  	$start_date     = date('Y-m-d H:i' ,strtotime($this->input->get('start_date')));
		      	$end_date 		= date('Y-m-d H:i' ,strtotime($this->input->get('end_date')));
		      	$product_title 	= $this->input->get('product_title');
		      	$queue_seq_id 	= $this->input->get('queue_seq_id');

		      	if($queue_seq_id):
		      		$tbname  = 'uw_summery_queue';
		      		$this->common_model->deleteData($tbname,'seq_id',(int)$queue_seq_id);
		      	endif;

			 	if($this->input->get('start_date') && $this->input->get('end_date') ):
			 		$where['created_at'] = array('$gte' => $start_date ,'$lte' => $end_date );
			 	elseif($this->input->get('start_date')):
			 		$where['created_at'] = array('$gte' => $start_date);
			 	elseif($this->input->get('end_date')):
			 		$where['created_at'] = array('$lte' => $end_date);
			 	endif;

				$shortField 			=	['coupon_id' => -1];
				$wcon1['where']         =  array('users_id' => (int)$usersId ,'status' => 'A');
				$userResult				=  $this->geneal_model->getData2('single', 'uw_users', $wcon1);
				$user_oid 				=  $userResult['_id']->{'$id'};

				if($product_title):
				$where['product_title'] = $product_title;
				endif;
				$where['user_id'] 		= (int)$usersId;
				$where['status'] 		= 'A';
				$where['raffle_mode'] 	= array('$ne'=> 'Y');
				$wcon['where'] 			= $where;

				$tblName2 				= 'uw_lotto_orders';
				$shortField   			= array('product_id' => -1 );
				$totalProductDetails	= $this->common_model->getsummeryReport('multiple',$tblName2 , $wcon , $shortField,$num_page,$cnt);

				// Main - CommissionAmount 
				$totalSales 			=  $this->common_model->totalSales($user_oid ,$where);
				$commissionAmount 		=  $this->common_model->commissionList($user_oid ,$where);
				$totalcancelOrderAmount	=  $this->common_model->cancelOraderList($user_oid,$where);
				$totalCustomerPaid		=  $this->common_model->totalCustomerPaid($user_oid,$where);
				$dueBalance				=  (float)$totalSales-$commissionAmount-$totalCustomerPaid;


				//Campaign Wise Data Mapping..
				$ProductListArray = array();
				if($totalProductDetails):
					foreach($totalProductDetails as $key => $items):
						// Order ID  ArrayList per Campaign.
						$wcon['where']['product_id'] = $items['product_id'];
						$tblNameOrder 				 = 'uw_lotto_orders';
						$shortField   				 = array('product_id' => -1 );
						$wcon['where']['status'] 	 =  array('$ne'  => '');
						$OrderArrayList				 = $this->common_model->getFieldInArray('order_id',$tblNameOrder , $wcon);
						// CommissionAmount 
						$where['where_in']['order_id'] = $OrderArrayList;
						// $totalProductDetails[$key]['totalSales'] 		 =  $this->common_model->totalSales($user_oid ,$where);
						$totalProductDetails[$key]['commissionAmount'] 		 =  $this->common_model->commissionList($user_oid ,$where);
						$totalProductDetails[$key]['totalcancelOrderAmount'] =  $this->common_model->cancelOraderList($user_oid,$where);

						$where['product_id'] = (int)$items['product_id'];
						$totalProductDetails[$key]['totalCustomerPaid']		 =  $this->common_model->totalCustomerPaid($user_oid,$where ,$idd);

						$ProductListArray[] = $items['product_id'];
					endforeach;
				endif;

				// Product IDs..
				$RedeemedPrizeList		 =  $this->common_model->RedeemedPrizeList($user_oid,$where ,$idd);

				// --------------------------------------------------------------------------------------------------------------------- //
				$wcon22['where']['created_at'] =  $wcon['where']['created_at'];
				$wcon22['where']['user_id']	   =  $wcon['where']['user_id'];
				$wcon22['where']['status']	   =  'CL';
				$wcon22['where']['raffle_mode']=  array('$ne'=> 'Y');
				$cancelOraderList			   = $this->common_model->CancellationPIDs($user_oid,$wcon22);

				
				if($cancelOraderList):
				 foreach ($cancelOraderList as $key => $ProductID1):
		 			if(!in_array($ProductID1, $ProductListArray)):

		 				$RtblName   		 = 'uw_products';
		 				$whereCon['where']   = array('products_id'  => (int)$ProductID1 ,'enable_raffle_ticket' => array('$ne'=> 'Enable'));
		 				$RFields    		 = array('products_id','title','straight_add_on_amount','product_image');
		 				$result123  		 = $this->common_model->getParticularFieldByMultipleCondition($RFields,$RtblName,$whereCon);

		 				$wcon['where']['product_id'] = $ProductID1;
						$tblNameOrder 				 = 'uw_lotto_orders';
						$shortField   				 = array('product_id' => -1 );
						$wcon['where']['status'] 	 =  array('$ne'  => '');
						$OrderArrayList				 = $this->common_model->getFieldInArray('order_id',$tblNameOrder , $wcon);

		 				$CancelOraderListData['_id']              = $result123['title'];
			            $CancelOraderListData['price'] 	          = $result123['straight_add_on_amount'];
			            $CancelOraderListData['sales_count']      = (int)'0';
			            $CancelOraderListData['sales']		      = (int)'0';
			            $CancelOraderListData['product_image'] 	  = $result123['product_image'];
			            $CancelOraderListData['product_id']       = $result123['products_id'];
			            $CancelOraderListData['commissionAmount'] = 0;


		 				$where333['where_in']['order_id'] = $OrderArrayList;
			            $where333['created_at'] = $where['created_at'];
			            $where333['user_id'] 	= $where['user_id'];
			            $where333['status'] 	= $where['status'];
			            $CancelOraderListData['totalcancelOrderAmount']  = $this->common_model->cancelOraderList($user_oid,$where333); 
			            
			            $where['product_id'] = (int)$ProductID1;
			            $CancelOraderListData['totalCustomerPaid']  = $this->common_model->totalCustomerPaid($user_oid,$where);
			            array_push($totalProductDetails,$CancelOraderListData);
		 			 	 
		 			endif;
				 endforeach;
				endif;

				// --------------------------------------------------------------------------------------------------------------------- //

				if($RedeemedPrizeList):
				 foreach ($RedeemedPrizeList as $key => $ProductID):
		 			if(!in_array($ProductID, $ProductListArray) && !in_array($ProductID, $cancelOraderList)):

		 				$RtblName   		 = 'uw_products';
		 				$whereCon['where']   = array('products_id'  => (int)$ProductID );
		 				$RFields    		 = array('products_id','title','straight_add_on_amount','product_image');
		 				$resultsss  		 = $this->common_model->getParticularFieldByMultipleCondition($RFields,$RtblName,$whereCon);

		 				$wcon['where']['product_id'] = $ProductID;
						$tblNameOrder 				 = 'uw_lotto_orders';
						$shortField   				 = array('product_id' => -1 );
						$wcon['where']['status'] 	 =  array('$ne'  => '');
						$OrderArrayList				 = $this->common_model->getFieldInArray('order_id',$tblNameOrder , $wcon);

		 				$RedeemListData['_id']           = $resultsss['title'];
			            $RedeemListData['price'] 	     = $resultsss['straight_add_on_amount'];
			            $RedeemListData['sales_count']   = (int)'0';
			            $RedeemListData['sales']		 = (int)'0';
			            $RedeemListData['product_image'] = $resultsss['product_image'];
			            $RedeemListData['product_id']    = $resultsss['products_id'];
			            $RedeemListData['commissionAmount'] = 0;


			            $where333['where_in']['order_id'] = $OrderArrayList;
			            $where333['created_at'] = $where['created_at'];
			            $where333['user_id'] 	= $where['user_id'];
			            $where333['status'] 	= $where['status'];

			            if($OrderArrayList):
			             $RedeemListData['totalcancelOrderAmount']  = $this->common_model->cancelOraderList($user_oid,$where333); 
			            else:
			             $RedeemListData['totalcancelOrderAmount']  = 0; 
			            endif;
			            
			            $where['product_id'] = (int)$ProductID;
			            $RedeemListData['totalCustomerPaid']  = $this->common_model->totalCustomerPaid($user_oid,$where ,$idd);
			            array_push($totalProductDetails,$RedeemListData);
		 			 	 
		 			endif;
				 endforeach;
				endif;

				$ProductTitle = $this->input->get('product_title');

				if(empty($ProductTitle)):
					$result['totalSales'] 	  		    = (string)$totalSales;
					$result['commissionAmount'] 	    = (string)$commissionAmount;
					$result['totalcancelOrderAmount'] 	= (string)$totalcancelOrderAmount;
					$result['totalCustomerPaid'] 	    = (string)$totalCustomerPaid;
					$result['dueBalance'] 	    		= (string)$dueBalance;
					$result['totalProductDetails'] 	    = $totalProductDetails;
				else:
					$dueBalance							= (float)$totalProductDetails[0]['sales']-$totalProductDetails[0]['commissionAmount']-$totalProductDetails[0]['totalCustomerPaid'];
					$result['totalSales'] 	  		    = (string)$totalProductDetails[0]['sales'];
					$result['commissionAmount'] 	    = (string)$totalProductDetails[0]['commissionAmount'];
					$result['totalcancelOrderAmount'] 	= (string)$totalProductDetails[0]['totalcancelOrderAmount'];
					$result['totalCustomerPaid'] 	    = (string)$totalProductDetails[0]['totalCustomerPaid'];
					$result['dueBalance'] 	    		= (string)$dueBalance;
					if($totalProductDetails):

						foreach($totalProductDetails as $Item):
							if($ProductTitle == $Item['_id'] ):
				  				$result['totalProductDetails'] 	    = $Item;
							endif;
						endforeach;

					endif;

				endif;
				
				if(!empty($result)):
					$results = $result;
					echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);	
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('DATA_NOT_FOUND'),$results);	
				endif;

			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : totalSales
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to fetch order history.
	 * * Date 		   : 22 February 2024
	 * * **********************************************************************/
	public function totalSalesReports()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 			= 	array();

		if(requestAuthenticate(APIKEY,'GET')):
			
			if( $this->input->get('users_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			else:
			  	$usersId 		= $this->input->get('users_id');
			  	$start_date     = date('Y-m-d 21:31' ,strtotime($this->input->get('start_date')));
		      	$end_date 		= date('Y-m-d 21:30' ,strtotime($this->input->get('end_date')));

			 	if($this->input->get('start_date') && $this->input->get('end_date') ):
			 		$where['created_at'] = array('$gte' => $start_date ,'$lte' => $end_date );
			 	elseif($this->input->get('start_date')):
			 		$where['created_at'] = array('$gte' => $start_date);
			 	elseif($this->input->get('end_date')):
			 		$where['created_at'] = array('$lte' => $end_date);
			 	endif;

				$shortField 			=	['coupon_id' => -1];
				$wcon1['where']         =  array('users_id' => (int)$usersId ,'status' => 'A');
				$userResult				=  $this->geneal_model->getData2('single', 'uw_users', $wcon1);
				$user_oid 				=  $userResult['_id']->{'$id'};

				// CommissionAmount 
				$totalSales 			=  $this->common_model->totalSales($user_oid ,$where);
				$commissionAmount 		=  $this->common_model->commissionList($user_oid ,$where);
				$totalcancelOrderAmount	=  $this->common_model->cancelOraderList($user_oid,$where);
				$totalCustomerPaid		=  $this->common_model->totalCustomerPaid($user_oid,$where);
				// $dueBalance				=  $this->common_model->dueBalance($user_oid,$where);

				$result['totalSales'] 	  		    = (string)$totalSales;
				$result['commissionAmount'] 	    = (string)$commissionAmount;
				$result['totalcancelOrderAmount'] 	= (string)$totalcancelOrderAmount;
				$result['totalCustomerPaid'] 	    = (string)$totalCustomerPaid;

				if(!empty($result)):
					$results = $result;
					echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);	
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('DATA_NOT_FOUND'),$results);	
				endif;

			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	public function getWinner()
	{

		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 			= 	array();

		if(requestAuthenticate(APIKEY,'GET')):
			
			// if($this->input->get('users_id') == ''): 
			// 	echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			// else
			// 	if( $this->input->post('product_id') == ''): 
			// 	echo outPut(0,lang('SUCCESS_CODE'),lang('PRODUCT_ID_EMPTY'),$result);
			// else:

			 	// $productID 					 = $this->input->post('product_id');
				
				// $date 			   			= strtotime(date('Y-m-d'));
				// $whereCon['where'] 			= array('update_date' => array('$gte' => $date ));
			 	$AvailableWinnerCouponList 	=	$this->common_model->getData('multiple','uw_lotto_winners',$whereCon);
			 	


			 	if($AvailableWinnerCouponList):
				 	
			 		foreach ($AvailableWinnerCouponList as $key => $list):
					 	$product_id = $list['product_id'];
					 	$tableName = 'uw_prize';
					 	$whereCon['where'] = array('product_id' => (int)$product_id);
					 	$PrizeData = $this->common_model->getData('single',$tableName , $whereCon);

					 	$whereConproduct['where'] = array('products_id' => (int)$product_id);
					 	$productData = $this->common_model->getFieldInArray('title','uw_products' , $whereConproduct);
					 	$AvailableWinnerCouponList[$key]['title'] = $productData[0];

					 	if($PrizeData):
					 		$AvailableWinnerCouponList[$key]['prizeData'] = $PrizeData;
						else:
						 	$AvailableWinnerCouponList[$key]['prizeData'] = '';
						endif;

			 		endforeach;
				 	
			 		$results = $AvailableWinnerCouponList;
			 	else:
			 		$results = [];
			 	endif;

			 	echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);	
			// endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}
	
	/* * *********************************************************************
	 * * Function name : productSettings
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to fetch order history.
	 * * Date 		   : 29 January 2024
	 * * **********************************************************************/
	public function productSettings()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_GET));
		$result 			= 	array();

		if(requestAuthenticate(APIKEY,'GET')):
			 	$whereCon['where']  = array('status' => 'A');
			 	$Product_Settings 	= $this->common_model->getData('multiple','uw_settings',$whereCon);
			 	if($Product_Settings):
			 		$results = $Product_Settings;
			 	else:
			 		$results = [];
			 	endif;

			 	echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);	
			// endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : winnerTestimonial
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to get winner's Testimonial.
	 * * Date 		   : 29 January 2024
	 * * **********************************************************************/
	public function winnerTestimonial()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_GET));
		$result 			= 	array();

		if(requestAuthenticate(APIKEY,'GET')):
			 	$whereCon['where']  = array('status' => 'A');
			 	$Sortdata 			= array('testimonial_id' => -1);
			 	$Product_Settings 	= $this->common_model->getData('multiple','uw_uwin_testimonials',$whereCon,$Sortdata);
			 	if($Product_Settings):
			 		$results = $Product_Settings;
			 	else:
			 		$results = [];
			 	endif;

			 	echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);	
			// endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : checkWinner
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to check winner.
	 * * Date 		   : 02 February 2024
	 * * Updated By    : Dilip Halder
	 * * Updated Date  : 02 February 2024
	 * * **********************************************************************/
	public function checkWinner()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 			= 	array();

		if(requestAuthenticate(APIKEY,'POST')):
				$users_id 	=  $this->input->get('users_id');
				$tickect_id =  $this->input->post('tickect_id');

				if(empty($users_id)):
					echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
				elseif(empty($tickect_id)):
					echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_TICKET'),$result);die();
				else:

					// Cancelleed Order..
					$tableName1 			= "uw_lotto_orders";
					$whereCon1['where']  = array('order_id' => $tickect_id ,'status' => 'CL');
				 	$Canceled 	    	= $this->common_model->getData('multiple',$tableName1,$whereCon1);
				 	if(!empty($Canceled)):
				 		$result  = [];
				 		echo outPut(1,lang('SUCCESS_CODE'),lang('CANCELLED_ORDER'),$result);die();
				 	endif;
					 
					// Checking coupon in collection.
					$tableName 			= "uw_uwin_winner";
					$whereCon['where']  = array('order_id' => $tickect_id ,'status' => (int)1);
				 	$CheckWinner 	    = $this->common_model->getData('multiple',$tableName,$whereCon);

				 	if($CheckWinner):
				 		$winnerStatus  = array();
					 	foreach ($CheckWinner as $key => $winnerItem):
					 		if($winnerItem['redeem_status'] == 'paid'):
					 			$winnerStatus[] = 'P';
					 		elseif($winnerItem['redeem_status'] != 'paid'):
					 			$winnerStatus[] = 'N';
					 		endif;

					 	endforeach;
				 	endif;

				 	if(!in_array('N', $winnerStatus)):
				 		$query = array('order_id' => $tickect_id ,'status' => (int)1 );
				 	else:
				 		$query = array('order_id' => $tickect_id ,'status' => (int)1  , "redeem_status" => array('$ne' => "paid") );
				 	endif;
					$tableName 			= "uw_uwin_winner";
					$whereCon['where']  = $query;
				 	$WinnerList 	    = $this->common_model->getData('multiple',$tableName,$whereCon);

				 	$tblName		   = "uw_lotto_orders";
				    $Fields 		   = array('_id','created_at','draw_id','pos_number');
			 	    // $orderDetails 	   = $this->common_model->getSingleDataByParticularField($Fields,$tblName,'order_id',$tickect_id);
			 	    $orderDetails 	   = $this->geneal_model->getOrderDetail($whereCon);

			 	    $Drawdate =  date('d/m/y',strtotime($orderDetails['draw_dateTime']));
			 	    $Drawtime =  date('h:i A',strtotime($orderDetails['draw_dateTime']));

			 	    if(empty($WinnerList)  &&  strtotime($orderDetails['draw_dateTime']) > strtotime(date('Y-m-d H:i'))):
			 	    	// echo outPut(1,lang('SUCCESS_CODE'),lang('DRAW_ALERT'),$result);die();
			 	    	echo outPut(1,lang('SUCCESS_CODE'),"The draw is scheduled for $Drawdate at $Drawtime. Please check the results after the draw.",$result);die();
			 	    endif;

			 	    $order_date  	   = $orderDetails['created_at'];

				 	if($WinnerList):
					 	$totalPrizeAmount    = 0;
					 	$totalCode 			 = array();
					 	$totalMatchingCode 	 = array();
					 	$totalMatchingAmount = array();
					 	$totalWinnerType 	 = array();
					 	foreach ($WinnerList as $key => $items) :
					 	 $totalPrizeAmount   	= $totalPrizeAmount+ $items['amount'];
					 	 $totalCode[]   		= $items['coupons']?$items['coupons']:$items['code'];
					 	 $totalMatchingCode[]   = $items['code'];
					 	 $totalMatchingAmount[] = $items['amount'];
					 	 $totalWinnerType[] 	= $items['winner_type']?$items['winner_type']:'N/A';
					 	endforeach;


					 	$totalCode = implode(' / ', $totalCode);


					 	$winerList['voucher_id'] 		= $items['voucher_id'];
					 	$winerList['pos_number']   		= $orderDetails['pos_number']?$orderDetails['pos_number']:'N/A';
					 	$winerList['order_id']   		= $items['order_id'];
					 	$winerList['seller_first_name'] = $items['seller_first_name'];
					 	$winerList['seller_last_name'] 	= $items['seller_last_name'];
					 	$winerList['code'] 				= $totalCode;
					 	$winerList['matching_code'] 	= $totalMatchingCode;
					 	$winerList['matching_amount'] 	= $totalMatchingAmount;
					 	$winerList['amount'] 			= (string)$totalPrizeAmount;
					 	$winerList['winner_type'] 		= $totalWinnerType;
					 	$winerList['status'] 			= $items['status'];
					 	$winerList['products_id'] 		= $items['products_id'];
					 	$winerList['created_at'] 		= $items['created_at'];
					 	// $winerList['created_by'] 		= $items['created_by'];
						$winerList["created_at"]        =   date('Y-m-d h:i A',strtotime($order_date)) ;
						$winerList["modified_at"]       =   date('Y-m-d h:i A',strtotime($items['modified_at'])) ;
					 	if($items['modified_by']):
					 		$winerList['modified_by'] 		= $items['modified_by'];
					 	endif;
					 	if($items['redeem_by_mode']):
					 		$winerList['redeem_by_mode'] 	= $items['redeem_by_mode'];
					 	endif;
					 	if($items['redeem_status']):
					 		$winerList['redeem_status'] 	= $items['redeem_status'];
					 	endif;
					 	if($items['seller_id']):
					 		$winerList['seller_id'] 		= $items['seller_id'];
					 	endif;
				 		$results = array($winerList);
				 	else:
				 		$result  = [];
				 		echo outPut(1,lang('SUCCESS_CODE'),lang('NOT_WINNER'),$result);die();
				 	endif;
				endif;
			 	echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);	
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : redeemByCash
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to Redeem Coupons.
	 * * Date 		   : 02 February 2024
	 * * Updated By    : Dilip Halder
	 * * Updated Date  : 24 February 2024
	 * * **********************************************************************/
	public function redeemByMode()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 			= 	array();

		if(requestAuthenticate(APIKEY,'POST')):
				$users_id 			=  $this->input->post('users_id');
				$voucher_id 		=  $this->input->post('voucher_id');
				$tickect_id 		=  $this->input->post('tickect_id');
				$redeem_status 		=  $this->input->post('redeem_status');
				$redeem_by_mode 	=  $this->input->post('redeem_by_mode');
				$pos_device_id 		=  $this->input->post('pos_device_id');
				$ip_address      	=  $this->input->post('ip_address');

				if(empty($users_id)):
					echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
				elseif(empty($tickect_id)):
					echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_TICKET'),$result);die();
				elseif(empty($voucher_id)):
					echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_VOUCHER'),$result);die();
				elseif(empty($redeem_status)):
					echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_REDEEMSTATUS'),$result);die();
				elseif(empty($redeem_by_mode)):
					echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_RedeemByMode'),$result);die();
				else:

					$API_log["order_id"]  = $tickect_id;
					$API_log["api"] 	  = 'redeemByMode';
					$API_log["data"] 	  = $_POST;
					$Api_Log			  = $this->geneal_model->addData('api_logs', $API_log);

					// Checking coupon in collection.
					$tableName 			= "uw_uwin_winner";
					$whereCon['where']  = array('order_id' => $tickect_id , 'status' => (int)1 ,'redeem_status' => array('$ne' => 'paid') );
				 	$WinnerList 	    = $this->common_model->getData('multiple',$tableName,$whereCon);

				 	$tableName	  = "uw_users";
				    $Fields 	  = array('_id','users_id' ,'availableArabianPoints','redeeming_amount_limit');
			 	    $userDetails  = $this->common_model->getSingleDataByParticularField($Fields,$tableName,'users_id',(int)$users_id);

			 	    if(!empty($userDetails['redeeming_amount_limit'])):
			 	   		$redeeming_amount_limit =  $userDetails['redeeming_amount_limit']; 
			 	    else:
			 	   		$redeeming_amount_limit =  999; 
			 	    endif;

			 	    $totalPrizeAmount   = 0;
				 	foreach ($WinnerList as $key => $items) :
				 	 $totalPrizeAmount   = $totalPrizeAmount+ $items['amount'];
					 	if(!empty($items['redeem_status']) && $items['redeem_status'] == 'paid'):
					 		echo outPut(0,lang('SUCCESS_CODE'),lang('ALREADY_REDEEM'),$result);die();
					 	endif;

					 	if((int)$totalPrizeAmount >= $redeeming_amount_limit &&  $users_id != 100000000000110 ):
					 		echo outPut(0,lang('SUCCESS_CODE'),lang('BIG_WINNER_TEXT'),$result);die();
					 	endif;

				 	endforeach;

					if($WinnerList):
						$updateParams['pos_device_id'] 	= $pos_device_id;
					 	$updateParams['redeem_status'] 	= $redeem_status;
						$updateParams['redeem_by_mode'] = $redeem_by_mode;
						$updateParams["modified_at"]    = date('Y-m-d H:i');
						$updateParams['seller_id'] 		= (int)$users_id;
						$updateParams['created_ip'] 	= $ip_address;
						$WInnner_whereCon = array('order_id' => $this->input->post('tickect_id'), 'status' => (int)'1','redeem_status' => array('$ne' => 'paid') );
						$this->common_model->editMultipleDataByMultipleCondition('uw_uwin_winner', $updateParams,$WInnner_whereCon);
			 	     	

				 	    $tblName		   = "uw_lotto_orders";
					    $Fields 		   = array('_id','order_id','product_id');
				 	    $orderDetails 	   = $this->common_model->getSingleDataByParticularField($Fields,$tblName,'order_id',$this->input->post('tickect_id'));
				 	    //Error Log generating for unmatched users...
				 	    if((int)$userDetails['users_id']  != (int)$users_id  || $orderDetails['order_id'] != $tickect_id ):
							$errorLog["order_id"]         = $tickect_id;
							$errorLog["amount"]           = (int)$totalPrizeAmount;
							$errorLog["users_id"]         = (int)$users_id;
							$errorLog["error_users_id"]   = (int)$userDetails['users_id'];
							$errorLog["error_order_id"]   = $orderDetails['order_id'];
							$errorLog['client_details']   = json_encode($_SERVER);
							$errorLog["status"]           = "A";
							$this->geneal_model->addData('uw_error_log', $errorLog );

							$availableArabianPoints 	 =   (float)'0';
							// $end_balance 		 	     =   (float)'0';
							$user_OId 	 = '';
						  	$order_oid   = '';
						  	$productID   = '';
						  	$orderID     = $tickect_id;

						else:
						    $availableArabianPoints  =   (float)$userDetails['availableArabianPoints'];
							// $end_balance 		 	     =   (float)$userDetails['availableArabianPoints'];
						  	$user_OId 	 = $userDetails['_id']['$id'];
						  	$order_oid   = $orderDetails['_id']['$id'];
						  	$orderID     = $orderDetails['order_id'];
						  	$productID   = $orderDetails['product_id'];
				 	    endif;

						/* Load Balance Table -- after Sign Up*/
						$Redeemparam["load_balance_id"]          =   (int)$this->geneal_model->getNextSequence('uw_loadBalance');
						$Redeemparam["user_oid"]        	     =   new MongoDB\BSON\ObjectId($user_OId);
						$Redeemparam["order_oid"]       		 =   new MongoDB\BSON\ObjectId($order_oid);
						$Redeemparam["order_id"]       		     =   $orderID;
						$Redeemparam["product_id"]       		 =   (int)$productID;
						$Redeemparam["user_id_deb"]              =   (int)$users_id;
						$Redeemparam["user_id_cred"]             =   (int)0;
						$Redeemparam["upoints"]       		     =   (float)$totalPrizeAmount;
						$Redeemparam["record_type"]              =   'Debit';
						$Redeemparam["narration"]  			     =   'Redeem Prize';
						$Redeemparam["remarks"]  			     =   "Redeemed Prize ".$totalPrizeAmount." AED in cash";
						$Redeemparam["availableArabianPoints"] 	 =   (float)$availableArabianPoints;
						$Redeemparam["end_balance"] 		 	 =   (float)$availableArabianPoints;
						$Redeemparam["creation_ip"]         	 =   currentIp();
						$Redeemparam["created_at"]          	 =   date('Y-m-d H:i');
						$Redeemparam["created_by"]         	  	 =   (int)$users_id;
						$Redeemparam["status"]               	 =   "A";
						$this->geneal_model->addData('uw_loadBalance', $Redeemparam);
					 
					    $this->geneal_model->addRedeem_Cash_Amount_TO_Seller($users_id,(float)$totalPrizeAmount);

					  	 // Removing variable stored values. 
					    unset($users_id);
						unset($voucher_id);
						unset($tickect_id);
						unset($redeem_status);
						unset($redeem_by_mode);
						unset($pos_device_id);
						unset($ip_address);
						unset($Redeemparam["load_balance_id"]);
						unset($Redeemparam["user_oid"]);
						unset($Redeemparam["order_oid"]);
						unset($Redeemparam["order_id"]);
						unset($Redeemparam["product_id"]);
						unset($Redeemparam["user_id_deb"]);
						unset($Redeemparam["user_id_cred"]);
						unset($Redeemparam["upoints"]);
						unset($Redeemparam["record_type"]);
						unset($Redeemparam["narration"]);
						unset($Redeemparam["remarks"]);
						unset($Redeemparam["availableArabianPoints"]);
						unset($Redeemparam["end_balance"]);
						unset($Redeemparam["creation_ip"]);
						unset($Redeemparam["created_at"]);
						unset($Redeemparam["created_by"]);
						unset($Redeemparam["status"]);
						unset($updateParams["pos_device_id"]);
						unset($updateParams["redeem_status"]);
						unset($updateParams["redeem_by_mode"]);
						unset($updateParams["modified_at"]);
						unset($updateParams["seller_id"]);
						unset($updateParams["created_ip"]);

						$result = array('payment_date' => date('Y-m-d H:i'));
				  	 echo outPut(1,lang('SUCCESS_CODE'),lang('COUPON_REDEEMED_SUCCESFULLY'),$result);die();
				 	endif;
				endif;
			 	// echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);	
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : uwinAllowedUser
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to check permission.
	 * * Date 		   : 02 February 2024
	 * * **********************************************************************/
	public function uwinAllowedUser()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_GET));
		$result 			= 	array();

		if(requestAuthenticate(APIKEY,'GET')):
			 	$whereCon['where']  = array('status' => 'A');
			 	$UwinPermission 	= $this->common_model->getData('multiple','uw_uwin_allowed_user',$whereCon);
			 	if($UwinPermission):
			 		$results = $UwinPermission;
			 	else:
			 		$results = [];
			 	endif;

			 	echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);	
			// endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : cancelOrderRequest
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to send reuest to admin to cancel request.
	 * * Date 		   : 09 February 2024
	 * * **********************************************************************/
	public function cancelOrderRequest()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 			= 	array();

		if(requestAuthenticate(APIKEY,'POST')):

				$users_id 			=  $this->input->post('users_id');
				$tickect_id 		=  $this->input->post('tickect_id');
				$remark 			=  $this->input->post('remark');

				if(empty($users_id)):
					echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
				elseif(empty($tickect_id)):
					echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_TICKET'),$result);die();
				elseif(empty($remark)):
					echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_REMARK'),$result);die();
				else:

					// Checking coupon in collection.
					$tableName 			= "uw_lotto_orders";
					$whereCon['where']  = array('order_id' => $tickect_id , 'user_id' => (int)$users_id );
				 	$orderDeatail 	    = $this->common_model->getData('single',$tableName,$whereCon);
					
					if( $orderDeatail['order_id'] === $tickect_id ):
						 
						$currentDate           = date('Y-m-d h:m:s');
						$orderDate 	 		   = $orderDeatail['created_at'];
						$CancallationDateLimit = date('Y-m-d h:m:s', strtotime($orderDate . ' +1 day'));

						echo "orderDate= ".$orderDate;
			 			echo "<br>";
						echo "CancallationDateLimit= ".$CancallationDateLimit;
			 			echo "<br>";
			 			echo "currentDate= ".$currentDate;
			 			die();

						$currentDate 		   = strtotime($currentDate);
						$CancallationDateLimit = strtotime($CancallationDateLimit);

						echo "orderDate= ".$orderDate;
			 			echo "<br>";
						echo "CancallationDateLimit= ".$CancallationDateLimit;
			 			echo "<br>";
			 			echo "currentDate= ".$currentDate;
			 			die();

				 		if($CancallationDateLimit >= $currentDate):
				 			echo "CancallationDateLimit= ".$CancallationDateLimit;
			 			echo "<br>";
			 			echo "currentDate= ".$currentDate;
			 			die();
				 		endif;

				 			// echo "CancallationDateLimit= ".$CancallationDateLimit;
				 			// echo "<br>";
				 			// echo "currentDate= ".$currentDate;

					 	die();
					 	

					 	 
					 	 


					endif;


					die();



					// if(!empty($WinnerList['redeem_status']) && $WinnerList['redeem_status'] == 'paid'):
				 	// 	echo outPut(0,lang('SUCCESS_CODE'),lang('ALREADY_REDEEM'),$result);die();

				 	// elseif((int)$WinnerList['amount']>= 1000):
				 	// 	echo outPut(0,lang('SUCCESS_CODE'),lang('BIG_WINNER_TEXT'),$result);die();
					// else:

				 	//   $updateParams['redeem_status'] 	= $redeem_status;
					//   $updateParams['redeem_by_mode'] = $redeem_by_mode;
					//   $updateParams['seller_id'] 		= (int)$users_id;
					//   $this->common_model->editData('uw_uwin_winner', $updateParams, 'voucher_id', (int)$voucher_id);

					//   $this->geneal_model->addRedeem_Cash_Amount_TO_Seller($users_id,(float)$WinnerList['amount']);

				  	//  echo outPut(0,lang('SUCCESS_CODE'),lang('COUPON_REDEEMED_SUCCESFULLY'),$result);die();
				 	// endif;
				endif;
			 	// echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);	
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : campaignFreezing
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to campaign Freezing.
	 * * Date 		   : 10 February 2024
	 * * **********************************************************************/
	public function campaignFreezing()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_GET));
		$result 			= 	array();

		if(requestAuthenticate(APIKEY,'GET')):
			 	$whereCon['where']  = array('status' => 'A');
			 	$campaignFreezing 	= $this->common_model->getData('multiple','uw_campaign_freezing',$whereCon);
			 	if($campaignFreezing):
			 		$results = $campaignFreezing;
			 	else:
			 		$results = [];
			 	endif;

			 	echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);	
			// endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : checkSingleCampaignFreezing
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to check Single Campaign Freezing.
	 * * Date 		   : 28 February 2024
	 * * **********************************************************************/
	public function checkSingleCampaignFreezing()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 			= 	array();

		if(requestAuthenticate(APIKEY,'POST')):
				$usersId    = $this->input->get('users_id');
				$productId  = $this->input->post('product_id');
				
				$tblName  		   = 'uw_users';
				$whereCon['where'] = array('users_id' => (int)$usersId );
				$UserData          = $this->common_model->getData('single',$tblName,$whereCon);
				
				if(empty($UserData)):
					echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_USER_ID'),$results);	
				elseif(!empty($UserData) && $UserData['status'] != "A" ):
					echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$results);
				elseif(!empty($UserData) && $UserData['status'] == "A"):
					
				 $tblName  		    = 'uw_products';
				 $whereCon['where'] = array('products_id' => (int)$productId );
				 $productData       = $this->common_model->getData('single',$tblName,$whereCon);
					if(empty($productData)):
						echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_PRODUCT_ID'),$results);	
					elseif(!empty($productData) && $productData['status'] != "A" ):
						echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_PRODUCT'),$results);
					elseif(!empty($productData) && $productData['status'] == "A"):
						// Api Data Responce 
						$result['title'] 					    = $productData['title'];
						$result['status'] 					    = $productData['status'];
						$result['draw_date'] 					= $productData['draw_date'];
						$result['draw_time'] 					= $productData['draw_time'];
						$result['campaign_auto_freezing_mode']  = $productData['campaign_auto_freezing_mode'];
						$result['campaign_freezing_start_time'] = $productData['campaign_freezing_start_time'];
						$result['campaign_freezing_end_time']   = $productData['campaign_freezing_end_time'];
						$results = $result;
						echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);	
					endif;
				endif;

			// endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : AddSummeryReportqueue
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to check Summery Report queue.
	 * * Date 		   : 29 February 2024
	 * * **********************************************************************/
	// public function AddSummeryReportqueue()
	// {
	// 	$apiHeaderData 		=	getApiHeaderData();
	// 	$this->generatelogs->putLog('APP',logOutPut($_POST));
	// 	$result 			= 	array();

	// 	if(requestAuthenticate(APIKEY,'POST')):
	// 			$usersId    = $this->input->get('users_id');
	// 			$productId  = $this->input->post('product_id');
				
	// 			$tblName  		   = 'uw_users';
	// 			$whereCon['where'] = array('users_id' => (int)$usersId );
	// 			$UserData          = $this->common_model->getData('single',$tblName,$whereCon);
				
	// 			if(empty($UserData)):
	// 				echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_USER_ID'),$results);	
	// 			elseif(!empty($UserData) && $UserData['status'] != "A" ):
	// 				echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$results);
	// 			elseif(!empty($UserData) && $UserData['status'] == "A"):
					
	// 			 $tblName  		    = 'uw_products';
	// 			 $whereCon['where'] = array('products_id' => (int)$productId );
	// 			 $productData       = $this->common_model->getData('single',$tblName,$whereCon);
	// 				if(empty($productData)):
	// 					echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_PRODUCT_ID'),$results);	
	// 				elseif(!empty($productData) && $productData['status'] != "A" ):
	// 					echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_PRODUCT'),$results);
	// 				elseif(!empty($productData) && $productData['status'] == "A"):

						
	// 					$tblName 			= 'uw_summery_queue';
	// 					$whereCon['where']  = array('status' => 'A');
	// 					$SummeryQueue       = $this->common_model->getData('single',$tblName,$whereCon,$shortField);
						
	// 					if($SummeryQueue):
	// 						$currentDateAndTime = date('Y-m-d H:i:s') ;
	// 						$triggerTime = date('Y-m-d H:i:s', strtotime($currentDateAndTime . '+2 seconds'));
	// 					else:
	// 						$currentDateAndTime  = date('Y-m-d H:i:s') ;
	// 						$triggerTime         = $currentDateAndTime;
	// 					endif;
						
	// 					$ParamData['seq_id'] 		 		 = (int)$this->geneal_model->getNextSequence('uw_summery_queue');
	// 					$ParamData['user_id']  			     = (int)$usersId;
	// 					$ParamData['products_id']  			 = (int)$productData['products_id'];
	// 					$ParamData['campaign_name']        	 = $productData['title'];
	// 					$ParamData['draw_date']    			 = $productData['draw_date'];
	// 					$ParamData['draw_time']    			 = $productData['draw_time'];
	// 					$ParamData['status']                 = 'A';
	// 					$ParamData['currentDate']  			 =	$currentDateAndTime;
	// 					$ParamData['allocated_trigger_time'] =	$triggerTime;
	// 					$this->common_model->addData('uw_summery_queue',$ParamData);
						 
	// 					$results = $ParamData;
	// 					echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);	
	// 				endif;
	// 			endif;

	// 		// endif;
	// 	else:
	// 		echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
	// 	endif;
	// }

	public function AddSummeryReportqueue()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 			= 	array();

		if(requestAuthenticate(APIKEY,'POST')):
			$usersId    = $this->input->get('users_id');
			$productId  = $this->input->post('product_id');
			
			$tblName  		   = 'uw_users';
			$whereCon['where'] = array('users_id' => (int)$usersId );
			$UserData          = $this->common_model->getData('single',$tblName,$whereCon);
			
			if(empty($UserData)):
				echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_USER_ID'),$results);	
			elseif(!empty($UserData) && $UserData['status'] != "A" ):
				echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$results);
			elseif(!empty($UserData) && $UserData['status'] == "A"):
			
			if($productId):	
			 $tblName  		    = 'uw_products';
			 $whereCon['where'] = array('products_id' => (int)$productId );
			 $productData       = $this->common_model->getData('single',$tblName,$whereCon);
			endif;

		 	// if(empty($productData)):
			// 	echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_PRODUCT_ID'),$results);	
			// elseif(!empty($productData) && $productData['status'] != "A" ):
			// 	echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_PRODUCT'),$results);
			// elseif(!empty($productData) && $productData['status'] == "A"):
			// endif;

				$tblName 			= 'uw_summery_queue';
				$whereCon['where']  = array('status' => 'A');
				$shortField         = array('seq_id' => -1);
				$SummeryQueue       = $this->common_model->getData('single',$tblName,$whereCon,$shortField);
				
				if($SummeryQueue && $SummeryQueue['allocated_trigger_time']):
					$currentDateAndTime = $SummeryQueue['allocated_trigger_time'];
					$triggerTime = date('Y-m-d H:i:s', strtotime($currentDateAndTime . '+2 seconds'));
				else:
					$currentDateAndTime  = date('Y-m-d H:i:s') ;
					$triggerTime         = $currentDateAndTime;
				endif;
				
				$ParamData['seq_id'] 		 		 = (int)$this->geneal_model->getNextSequence('uw_summery_queue');
				$ParamData['user_id']  			     = (int)$usersId;
				$ParamData['products_id']  			 = (int)$productData['products_id'];
				$ParamData['campaign_name']        	 = $productData['title'];
				$ParamData['draw_date']    			 = $productData['draw_date'];
				$ParamData['draw_time']    			 = $productData['draw_time'];
				$ParamData['status']                 = 'A';
				$ParamData['currentDate']  			 =	$currentDateAndTime;
				$ParamData['allocated_trigger_time'] =	$triggerTime;
				$this->common_model->addData('uw_summery_queue',$ParamData);
				 
				$results = $ParamData;
				echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);	
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : recharge
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to redeem recharge CouponCode.
	 * * Date 		   : 28 March 2024
	 * * **********************************************************************/
	public function recharge()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 			= 	array();

		if(requestAuthenticate(APIKEY,'POST')):
				$usersId    = $this->input->get('users_id');
				$couponCode = $this->input->post('coupon_code');
				
				$tblName  		   = 'uw_coupon_code_only';
				$whereCon['where'] = array('coupon_code' => (int)$couponCode );
				$CouponCodeData    = $this->common_model->getData('single',$tblName,$whereCon);
				$results = array();
				if(empty($CouponCodeData)):
					echo outPut(1,lang('SUCCESS_CODE'),lang('INVALID_COUPON'),$results);die();
				elseif($CouponCodeData['coupon_code_statys'] == "Expire" || $CouponCodeData['coupon_code_statys'] == "Inactive"):
					echo outPut(1,lang('SUCCESS_CODE'),lang('RECHARGE_CODE_EXPIRED'),$results);die();
				elseif($CouponCodeData['coupon_code_statys'] == "Redeemed"):
					echo outPut(1,lang('SUCCESS_CODE'),lang('REDDEM_COUPON'),$results);die();
				else:
					if(strtotime($CouponCodeData['expair_date']) >=  strtotime(date('Y-m-d'))):
					 	$tblName  		   = 'uw_users';
					 	$whereCon['where'] = array('users_id' => (int)$usersId);
					 	$UserData          = $this->common_model->getData('single',$tblName,$whereCon);
					 	if(empty($UserData)):
							echo outPut(1,lang('SUCCESS_CODE'),lang('INVALID_USER_ID'),$results);
						elseif($UserData['status']== 'I' || $UserData['status']== 'D' || $UserData['status']== 'B'):
							echo outPut(1,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$results);
						elseif($UserData['status']== 'A'):

							$user_oid 	     = $UserData['_id']->{'$id'};
							$recharge_oid    = $CouponCodeData['_id']->{'$id'};
							$recharge_amount = $CouponCodeData['coupon_code_amount'];

							$fromuserparam["load_balance_id"]		 =	(int)$this->geneal_model->getNextSequence('uw_loadBalance');
							$fromuserparam["recharge_oid"] 			 =	new MongoDB\BSON\ObjectId($recharge_oid);
							$fromuserparam["user_oid"] 				 =	new MongoDB\BSON\ObjectId($user_oid);
							$fromuserparam["user_id_deb"]			 =	(int)0;
							$fromuserparam["user_id_cred"] 			 =	(int)$this->input->get('users_id');
							$fromuserparam["upoints"] 				 =	(float)$recharge_amount;
							$fromuserparam["availableArabianPoints"] =	(float)$UserData['availableArabianPoints'];
							$fromuserparam["end_balance"] 			 =	(float)$UserData['availableArabianPoints'] + $recharge_amount;
						    $fromuserparam["record_type"] 			 =	'Credit';
						    $fromuserparam["narration"]				 =	'Recharge Coupon';
						    $fromuserparam["remarks"]				 =	'Recharge Coupon Code : '.$CouponCodeData['coupon_code'];
						    $fromuserparam["creation_ip"] 			 =	$this->input->ip_address();
						    $fromuserparam["created_at"] 			 =	date('Y-m-d H:i');
						    $fromuserparam["created_by"] 			 =	(int)$this->input->get('users_id');
						    $fromuserparam["status"] 				 =	"A";
					    	$this->geneal_model->addData('uw_loadBalance', $fromuserparam);
							/*  Commission code start here.   */
							$this->geneal_model->creaditPoints($recharge_amount,$usersId); 
		    				/*  Commission code end here. */
		    				$updateParams['coupon_code_statys'] = 'Redeemed';
		    				$updateParams['redeem_date'] 		= date('Y-m-d H:i:s');
		    				$updateParams['redeem_by'] 		    = (int)$this->input->get('users_id');
							$this->common_model->editData('uw_coupon_code_only', $updateParams, 'coupon_code', (int)$couponCode);
							echo outPut(1,lang('SUCCESS_CODE'),$recharge_amount .' AED '. lang('recharge_success'),$results);	
					 	endif;
					endif;
				endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : rechargeHistory
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to redeemed recharge History.
	 * * Date 		   : 28 March 2024
	 * * **********************************************************************/
	public function rechargeHistory()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 			= 	array();

		if(requestAuthenticate(APIKEY,'POST')):
			$usersId       = $this->input->get('users_id');

			$page          = $this->input->post('page');
			$DrawDate      = $this->input->post('draw_date');
			$itemsPerPage  = $this->input->post('itemsPerPage');

			$tblName  		   = 'uw_coupon_code_only';
			$whereCon['where'] = array('redeem_by' => (int)$usersId, 'coupon_code_statys' => 'Redeemed');
			$CouponCodeData    = $this->common_model->getData('multiple',$tblName,$whereCon);


			// Sample long array with data
			$longArray = $CouponCodeData;

			// Items per page
			$itemsPerPage = $this->input->post('itemsPerPage');
			$pageno       = $this->input->post('page');

			// Current page number (received from URL query parameter, e.g., ?page=2)
			$page = isset($pageno) ? (int)$pageno : 1;

			// Calculate total number of pages
			$totalPages = ceil(count($longArray) / $itemsPerPage);

			// Calculate the starting index of the current page
			$startIndex = ($page - 1) * $itemsPerPage;

			// Extract the data for the current page
			$data = array_slice($longArray, $startIndex, $itemsPerPage);

			$totalpage= array();
			// Pagination links
			for ($i = 1; $i <= $totalPages; $i++) {
			    if ($i == $page) {
			         $current_page = $i;
			         $totalpage[] = $i;
			    } else {
			         $totalpage[] = $i;
			    }
			}

			$totalpage 				    = count($totalpage);
			$result['recharge_history'] = $data?$data:array();
			$result['current_page']     = $current_page;
			$result['total_page'] 	    =   $totalpage;

			$results		   = array();
			if($result):
			   $results = $result;
			   echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);	
			else:
			   echo outPut(1,lang('SUCCESS_CODE'),lang('DATA_NOT_FOUND'),$results);	
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : checkRelogin
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to check Relogin.
	 * * Date 		   : 01 Apirl 2024
	 * * **********************************************************************/
	public function checkRelogin()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_GET));
		$result 			= 	array();

		if(requestAuthenticate(APIKEY,'GET')):
				$usersId = $this->input->get('users_id');
			 	
			 	$tblName  		   = 'uw_users';
			 	$whereCon['where'] = array('users_id' => (int)$usersId);
			 	$Fields 		   = array('users_id','users_name','last_name','status','login_token','device_type'); 
			 	$UserData          = $this->common_model->getSingleDataByParticularField($Fields,$tblName,'users_id' , (int)$usersId);

			 	$results = array();
			 	if(empty($UserData)):
					echo outPut(1,lang('SUCCESS_CODE'),lang('INVALID_USER_ID'),$results);
				elseif($UserData['status']== 'I' || $UserData['status']== 'D' || $UserData['status']== 'B'):
					$results = $UserData;
					echo outPut(1,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$results);
				elseif($UserData['status']== 'A'):
					$results = $UserData;
			 		echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);	
				endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : winnerRedeemedList
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used get prize redeemed list (winners )
	 * * Date 		   : 28 June 2024
	 * * **********************************************************************/
	public function winnerRedeemedList()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_GET));
		$result 			= 	array();

		if(requestAuthenticate(APIKEY,'GET')):
			
			$usersId 	   = $this->input->get('users_id');
			$page          = $this->input->get('page');
			$itemsPerPage  = $this->input->get('itemsPerPage');
			$winnerType    = $this->input->get('winner_type');
 
			if(empty($usersId)):
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
			else: 	

				if($winnerType == 'RaffleWinner'):
					$redeemedWinnerLIst = $this->geneal_model->getRaffleWinnerList();
				elseif(winnerType == 'uwinnWinner'):
					$redeemedWinnerLIst = $this->geneal_model->getWinnerList();
				else:
					$redeemedWinnerLIst = $this->geneal_model->getWinnerList();
				endif;

			endif;
			 	$results = array();
				$results = $redeemedWinnerLIst;
		 		echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);	
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	
	/* * *********************************************************************
	 * * Function name : getReferralcode
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to getReferralcode
	 * * Date 		   : 13 December 2024
	 * * **********************************************************************/
	public function getReferralcode()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_GET));
		$result 			= 	array();

		if(requestAuthenticate(APIKEY,'POST')):
			$usersId  		  = $this->input->post('users_id');
			$requestFrom 	  = "app";
			$validationResult = $this->common_model->userValidate($usersId,$requestFrom);

			$tableName	 = "uw_users";
		    $Fields 	 = array('_id','users_id' ,'country_code','users_mobile' ,'users_email','referral_code','pos_number');
	 	    $userDetails = $this->common_model->getSingleDataByParticularField($Fields,$tableName,'users_id',(int)$usersId);

 	     	if(!empty($userDetails['pos_number']) &&  !is_numeric($userDetails['referral_code']) ):
				$updateParams['referral_code'] 	= (int)$userDetails['pos_number'];
				$updateParams["updated_at"]     = date('Y-m-d H:i');
				$this->common_model->editData($tableName, $updateParams, 'users_id', (int)$usersId);
	 	    else:
	 	    	$ReferralCode = $userDetails['pos_number'];
	 	    endif;

		 	$results 				  = array();
			$results['referral_code'] = $ReferralCode;
	 		echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);	
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : isPrinted
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to getReferralcode
	 * * Date 		   : 07 Apirl 2025
	 * * **********************************************************************/
	public function isPrinted()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_GET));
		$result 			= 	array();

		if(requestAuthenticate(APIKEY,'POST')):
			$usersId  		  = $this->input->post('users_id');
			$orderId  		  = $this->input->post('order_id');
			$requestFrom 	  = "app";
			$validationResult = $this->common_model->userValidate($usersId,$requestFrom);

			$tableName	 = "uw_lotto_orders";
		    $Fields 	 = array('order_id','is_printed','user_id','status');
	 	    $orderDetails = $this->common_model->getSingleDataByParticularField($Fields,$tableName,'order_id',$orderId);
 	     	if(!empty($orderDetails['user_id']) && $orderDetails['status'] == 'A' && $orderDetails['is_printed'] == 'N'  ):
				$updateParams['is_printed'] = 'Y';
				$updateParams["updated_at"] = date('Y-m-d H:i');
				$this->common_model->editData($tableName, $updateParams, 'order_id',$orderId);
				$results = [];
	 			echo outPut(1,lang('SUCCESS_CODE'),lang('TICKET_PRINTED_SUCCESSFULLY'),$results);die();
	 		elseif($orderDetails['is_printed'] == 'Y'):
		 	    $results = [];
		 		echo outPut(1,lang('SUCCESS_CODE'),lang('TICKET_PRINTED_ALREADY'),$results);	
		 	else:
		 		$results = [];
		 		echo outPut(1,lang('SUCCESS_CODE'),lang('ORDER_ID_INCORRECT'),$results);
	 	    endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}	

}
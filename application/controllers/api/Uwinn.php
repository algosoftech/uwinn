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
			// $P_fileds		  = array('product_image','title','description','straight_add_on_amount','rumble_add_on_amount','reverse_add_on_amount','lotto_type','lotto_range','draw_date','draw_time','products_id','campaign_auto_freezing_mode','campaign_freezing_end_time','campaign_freezing_start_time','reverse_settings_default_check','rumble_settings_default_check','straight_settings_default_check','product_name','enable_number_prefix','lotto_range_prefix','lotto_range_start','lotto_range_end','ticket_number_repeat','app_image','reverse_settings','rumble_settings','straight_settings','straight_game_name','rumble_game_name','reverse_game_name' ,'straight_settings_default_check','rumble_settings_default_check','reverse_settings_default_check','campaign_price','show_ticket_for_campaign','campaign_price','ticket_count_per_campaign','show_ticket_for_campaign','show_on','enable_raffle_ticket','reffle_prefix','reffle_length','enable_super_ball','super_ball_type','superbal_range_start','superbal_range_end');
			$P_fileds		  = array();

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

						$Prize_Fields					= 	array('title','stright_prize_heading','stright_prize_type','stright_prize1','stright_prize2','stright_prize3','stright_prize4','stright_prize5','stright_prize6','stright_prize7','rumble_mix_prize_heading' ,'rumble_mix_prize_type','rumble_mix_prize1','rumble_mix_prize2','rumble_mix_prize3','rumble_mix_prize4','rumble_mix_prize5','rumble_mix_prize6','rumble_mix_prize7','reverse_prize_heading','reverse_prize_type','reverse_prize1','reverse_prize2','reverse_prize3' ,'reverse_prize4','reverse_prize5' ,'reverse_prize6'  ,'lotto_type'  ,'enable_stright_prize_heading','enable_reverse_prize_heading','enable_rumble_mix_prize_heading','enable_title','btc_heading','btc_prize_text','straight_super_prize','rumble_super_prize','chance_super_prize');
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
	 * * Updated Date 	: 12 April 2024 // last function
	 * * **********************************************************************/
	// public function paymentCapture()
	// {	
	// 	// echo "working";die;
	// 	$apiHeaderData 		=	getApiHeaderData();
	// 	$this->generatelogs->putLog('APP',logOutPut($_POST));
	// 	$result 							= 	array();	
	// 	if(requestAuthenticate(APIKEY,'POST')):

	// 		$this->session->sess_regenerate();
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
				
	// 			// try{
	// 			// 		$session = $this->mongo_db->startSession();
	// 			// 		$session->startTransaction();
	// 			// 	}catch(Exception $e){
	// 			// 	echo outPut(0,lang('BAD_REQUEST_CODE'),'error',$e);
	// 			// }
	// 			$session = $this->mongodb_client->client->startSession();
	// 			$session->startTransaction();

	// 			try{

				
	// 				$product_id  = $this->input->post('product_id');
	// 			$tableName 		   = 'uw_products';
	// 			$whereCon['where'] = array('products_id'=> (int)$product_id ,'status' => "A");
	// 			$shortField 	   = array('products_id' => -1);
	// 			$ProductData       = $this->common_model->getData('single',$tableName, $whereCon, $shortField);

	// 			$DrawDateNTime     = $ProductData['draw_date'].' '.$ProductData['draw_time'];
	// 			$currentDatentime  = date('Y-m-d H:i');
				
	// 			if($ProductData['products_id'] != $product_id  || strtotime($DrawDateNTime) < strtotime($currentDatentime)  || strtotime($this->input->post('draw_date')) != strtotime($ProductData['draw_date'])):
					
	// 				echo outPut(0,lang('SUCCESS_CODE'),lang('RESTART_APP'),$product_id);die();
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
	// 			// $sellerDetails=[];
	// 			// print_r($sellerDetails);die();
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
	// 	        $ORparam["draw_date"]		    		=	$ProductData['draw_date']; 
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
	// 	        $ORparam["is_printed"] 					=	'Y'; 
	// 		     	if(!empty($raffle_tickets)):
	// 				$ORparam['raffle_mode']			    = $raffle_mode;
	// 				$ORparam['raffle_tickets']		    = $raffle_tickets;
	// 			endif;
	// 		    $ORparam["creation_ip"] 				=	$this->input->post('ip_address');
	// 		    $ORparam["created_at"] 					=	date('Y-m-d H:i');
	// 		    //Saving order details for Ticket
			   
	// 		    $orderInsertID = $this->mongodb_client->insertDocument('uw_lotto_orders', $ORparam, $session);	
	// 		    $o_id  = (string) $orderInsertID['_id'];
	// 		    // echo $ProductData['_id']->{'$id'};
	// 		    //  print_r($ProductData);
	// 		    // die();
	// 		    // $this->geneal_model->addData('uw_lotto_orders', $ORparam);
			  	
	// 		  	// Deduct the purchesed points and get available arabian points of user.
	// 			$currentBal 							= 	$this->geneal_model->debitPointsByAPI($USER['total_price'],$USER['USERID']); 

	// 	     	// Order capturing in order uw_loadbalance table..
	// 	     		$narration1 = $raffle_mode =='Y'? 'Raffle Order':'Order';
	// 			    $fromuserparam["load_balance_id"]		 =	(int)$this->geneal_model->getNextSequence('uw_loadBalance');
	// 				// $fromuserparam["order_oid"] 			 =	new MongoDB\BSON\ObjectId($orderInsertID['_id']->{'$id'});
	// 				$fromuserparam["order_oid"] 			 =	new MongoDB\BSON\ObjectId($o_id);
	// 				// ècho $orderInsertID['_id']->{'$id'};
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
	// 			    $fromuserparam["status"] 
	// 			    				 =	"A";
	// 		    	$fromuserinsertResult = $this->mongodb_client->insertDocument('uw_loadBalance', $fromuserparam, $session);
	// 		    	// $this->geneal_model->addData('uw_loadBalance', $fromuserparam);
	// 	    	/* Order capturing code start here.  End */


	// 	    	//Commission code start here.
	// 		    	$totalPrice 			 = (float)$orderInsertID['total_price'];
	// 				$commission_percentage   = $sellerDetails['commission_percentage'];
	// 				// Calculate commission amount
	// 				$commission_amount 		 = ($totalPrice * $commission_percentage) / 100;
	// 				$narration 			     =  $raffle_mode =='Y'? 'Raffle Commission':'Commission';
	// 		     	// Commission capturing in order uw_loadbalance table..
	// 			    $commissionParam["load_balance_id"]		 	 =	(int)$this->geneal_model->getNextSequence('uw_loadBalance');
	// 				// $commissionParam["order_oid"] 			 	 =	new MongoDB\BSON\ObjectId($orderInsertID['_id']->{'$id'});
	// 				$commissionParam["order_oid"] 			 	 =	new MongoDB\BSON\ObjectId($o_id);
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
	// 		    	// $this->geneal_model->addData('uw_loadBalance', $commissionParam);
	// 		    	$commissioninsertResult =$this->mongodb_client->insertDocument('uw_loadBalance', $commissionParam, $session);
	// 		    	// Credit the purchesed points and get available arabian points of user.
					
	// 				$this->geneal_model->creaditPoints($commission_amount,$orderInsertID["user_id"]); 
	// 	    		/*  Commission code start here.  End */
	//     		unset($ORparam["draw_date"]);
	// 		    $result = $ORparam;
	// 		    if($fromuserinsertResult && $commissioninsertResult ){
	// 		    	 $session->commitTransaction();
	// 		    	// $session->abortTransaction();
	// 		    	 echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
	// 		    }else{
	// 		    	$session->abortTransaction();
	// 		    	echo outPut(0,lang('BAD_REQUEST_CODE'),'Error: ',$e);
	// 		    }
			    

				
					
	// 			}catch(Exception $e){
	// 				$session->abortTransaction();
	// 				echo $e;
	// 				echo outPut(0,lang('BAD_REQUEST_CODE'),'Error: ',$e);
	// 			}
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
		try {

			// echo "working";die;
			$apiHeaderData = getApiHeaderData();
			$this->generatelogs->putLog('APP',logOutPut($_POST));
			$result = array();	
			
			if(requestAuthenticate(APIKEY,'POST')):

				$userId 		     = $this->input->get('users_id');
				$prizeTitle 	     = $this->input->post('prize_title');
				$firstName 	     	 = $this->input->post('first_name');
				$lastName 	     	 = $this->input->post('last_name');
				$productIsDonate 	 = $this->input->post('product_is_donate');
				$productID 			 = $this->input->post('product_id');
				$productTitle 		 = $this->input->post('product_title');
				$productQuantity 	 = $this->input->post('product_qty');
				$straightAddOnAmount = $this->input->post('straight_add_on_amount');
				$rumbleAddOnAmount 	 = $this->input->post('rumble_add_on_amount');
				$reverseAddOnAmount  = $this->input->post('reverse_add_on_amount');
				$vatAmount 	 	 	 = $this->input->post('vat_amount');
				$subTotal 	 	 	 = $this->input->post('subtotal');
				$totalPrice 	     = $this->input->post('total_price');
				$drawDate 	     	 = $this->input->post('draw_date');
				// $drawTime 	     		 	 = $this->input->post('draw_time');
				$lottoType 	     	 = $this->input->post('lotto_type');
				$usersEmail 	     = $this->input->post('users_email');
				$countryCode 	     = $this->input->post('country_code');
				$usersMobile 	     = $this->input->post('users_mobile');
				$SMS 	     		 = $this->input->post('SMS');
				$deviceType 	     = $this->input->post('device_type');
				$appName 	     	 = $this->input->post('app_name');
				$appVersion 	     = $this->input->post('app_version');
				$ticket 	     	 = $this->input->post('ticket');
				$selectionValues 	 = $this->input->post('selection_values');
				$paymentMode 	     = $this->input->post('payment_mode');
				$raffleMode 		 = $this->input->post('raffle_mode');
				$posDeviceID 		 = $this->input->post('pos_device_id');
				$superBallMode 		 = $this->input->post('super_ball_mode');
				$sbTickect 			 = $this->input->post('sb_tickect');
				$otpSent 			 = $this->input->post('otp_sent');
				$buyerCountryCode 	 = $this->input->post('buyer_country_code');
				$buyerMobile 		 = $this->input->post('buyer_mobile');
				$buyerEmail 		 = $this->input->post('buyer_email');
				$txnID 			     = $this->input->post('txn_id');


				if(empty($userId)): 
					throw new Exception(lang('USER_ID_EMPTY'), 1);
				elseif(empty($prizeTitle)): 
					throw new Exception(lang('EMPTY_PRIZE_TITLE'), 1);
				elseif(empty($productID)): 
					throw new Exception(lang('PRODUCT_ID_EMPTY'), 1);
				elseif(empty($productTitle)): 
					throw new Exception(lang('EMPTY_PRODUCT_TITLE'), 1);
				elseif(empty($productQuantity)): 
					throw new Exception(lang('EMPTY_PRODUCT_QTY'), 1);
				elseif(empty($drawDate)): 
					throw new Exception(lang('EMPTY_DRAW_DATE'), 1);
				elseif(empty($lottoType)): 
					throw new Exception(lang('EMPTY_LOTTO_TYPE'), 1);
				// elseif(empty($subTotal)): 
				// 	throw new Exception(lang('EMPTY_SUBTOTAL'), 1);
				elseif(empty($deviceType)): 
					throw new Exception(lang('EMPTY_DEVICE_TYPE'), 1);
				elseif(empty($appVersion)): 
					throw new Exception(lang('EMPTY_APP_VERSION'), 1);
				// elseif(empty($ticket) && $raffle_mode != 'Y' ): 
				// 	throw new Exception(lang('EMPTY_TICKET'), 1);
			    else:
					
				  $this->session->sess_regenerate();
		    	  $session = $this->mongodb_client->client->startSession();
				  $session->startTransaction();

		    		// Test campaign restriction for not allowed users.
					$whereCon['where'] 			=   array( 'status'=> 'A');
					$testCampaignData			=	$this->common_model->getData('single','uw_allowed_campaigns_permission',$whereCon);
					// echo "<pre>";print_r($testCampaignData);die();
					
					// checking test compaign...
					if(in_array($productID, $testCampaignData['seleted_campaign']) && !in_array($userId, $testCampaignData['seleted_users'])):
						throw new Exception(lang('DEMO_CAMPAIGN'), 1);
					else:

						$FieldList 		   = array('draw_id','draw_date','draw_time','status','products_id','campaign_price','reffle_prefix','reffle_length');
						$tableName 		   = 'uw_products';
						$pwhereCon['where']['products_id'] = (int)$productID;
						$shortField 	   = array('products_id' => -1);
						$productData	   = $this->common_model->getDataByNewQuery($FieldList,'single',$tableName,$pwhereCon,$shortField);
						// echo "<pre>"; print_r($productData); die();

						$drawDateNTime     = $productData['draw_date'].' '.$productData['draw_time'];
						$currentDatentime  = date('Y-m-d H:i');
						// $currentDatentime  = date('Y-m-d H:i',strtotime('2025-06-21 09:18'));

						if(!empty($productData)  && $productData['products_id'] == $productID  && $productData['status'] == 'A' && strtotime($drawDateNTime) > strtotime($currentDatentime) && strtotime($drawDate) == strtotime($productData['draw_date']) ):

							$tbl_name  		= 'uw_users';
							$whereCon  		= array('users_id'  =>(int)$userId,'status'=> 'A');
							$sellerDetails  = $this->geneal_model->getOnlyOneData($tbl_name, $whereCon);
							if($sellerDetails['status'] == 'A' && $sellerDetails['availableArabianPoints']  >= $totalPrice):


								// $sellerDetails=[];
								if($sellerDetails['app_version'] != $appVersion):
									$updateParams['app_version']  = $appVersion;
									$updateParams["updated_at"]   = date('Y-m-d H:i');
									$this->common_model->editData('uw_users', $updateParams, 'users_id', (int)$userId);
								endif;

								$user_oid 			   = $sellerDetails['_id']->{'$id'};
								$commission_percentage = $sellerDetails['commission_percentage'];

								/* ----- Raffle Mode Addon code  ---------*/
								if($raffleMode == "Y"):
									$reffle_prefix  = $productData['reffle_prefix'];
									$reffle_length  = $productData['reffle_length'];
									$raffleTickets = $this->common_model->generateRaffle($productQuantity,$reffle_prefix ,$reffle_length);
								endif;

								//Buffering time order duplication check.. START
								if(!empty($txnID)):
									$shortFielddd   = array('_id' => -1);
									$ddd['where']['user_oid']   = new MongoDB\BSON\ObjectId($user_oid);
									$ddd['where']['product_id'] = (int)$productID;
									$ddd['where']['txn_id']     = $txnID;
									$duplicateOrderData = $this->common_model->getData('single','uw_lotto_orders',$ddd,$shortFielddd);
									if(!empty($duplicateOrderData)):
										unset($duplicateOrderData["draw_date"]);
										echo outPut(1, lang('SUCCESS_CODE'), lang('ALREADY_ORDER_PLACED'), $duplicateOrderData);
										die();
									endif;
								endif;
								//Buffering time order duplication check.. END

								/* ----- Raffle Mode Addon code  ---------*/
								$orderIdss = floor((microtime(true) * 1000)).rand(100,999);
								$ORparam["sequence_id"]		    		=	(int)$this->geneal_model->getNextSequence('uw_lotto_orders');
								$ORparam["txn_id"]			    		=	$txnID;
						        $ORparam["user_oid"] 					=	new MongoDB\BSON\ObjectId($user_oid);
						        $ORparam["order_id"]		        	=	"UWINN".$orderIdss;//$this->geneal_model->getNextUWINOrderId();
						        // $ORparam["order_id"]		        	=	$this->geneal_model->getNextUWINOrderId();
						        $ORparam["draw_id"]		    			=	(int)$productData['draw_id']; 
		        				$ORparam["draw_date"] 					=	$productData['draw_date']; 
						        $ORparam["order_code"]		    		=	base64_encode(rand(1000,9999)); 
						        $ORparam["user_id"] 					=	(int)$userId;
						        $ORparam["user_type"] 					=	$sellerDetails['users_type']; 
					        	$ORparam["user_email"] 					=   $sellerDetails['users_email'];	
							 	$ORparam["user_phone"] 					=	$sellerDetails['users_mobile'];	
							 	$ORparam["store_name"] 					=	$sellerDetails['store_name'];
							 	$ORparam["pos_number"] 					=	(int)$sellerDetails['pos_number'];
							 	$ORparam["pos_device_id"] 				=	$posDeviceID; // $sellerDetails['pos_device_id'];
						     	$ORparam["product_id"] 					=	(int)$productID;
						     	$ORparam["product_title"] 				=	$productTitle;
						     	$ORparam["product_qty"] 				=	$productQuantity;
						     	$ORparam["prize_title"] 				=	$prizeTitle;
						        $ORparam["vat_amount"] 					=	(float)$vatAmount;
						        $ORparam["straight_add_on_amount"] 		=	(float)$straightAddOnAmount;
						        $ORparam["rumble_add_on_amount"] 		=	(float)$rumbleAddOnAmount;
						        $ORparam["reverse_add_on_amount"] 		=	(float)$reverseAddOnAmount;
						        $ORparam["subtotal"] 					=	(float)$subTotal;
						        $ORparam["total_price"] 				=	(float)$totalPrice;
						        $ORparam["availableArabianPoints"] 		=	(float)$sellerDetails["availableArabianPoints"];
								$ORparam["end_balance"] 				=	(float)$sellerDetails["availableArabianPoints"] - (float)$ORparam["total_price"] ;
							    $ORparam["payment_mode"] 				=	!empty($paymentMode)? $paymentMode :'UPoints';
						        $ORparam["product_is_donate"] 			=	$productIsDonate; //$this->input->post('product_is_donate');
							    $ORparam["order_status"] 				=	"Success";
							    $ORparam["device_type"] 				=	$deviceType;
				   				$ORparam["app_name"] 					=	$appName;
				   				$ORparam["app_version"] 				=	$appVersion;
				   				$ORparam["ticket"] 						=	$ticket;
				   				$ORparam["selection_values"] 			=	$selectionValues;
							    $ORparam["status"] 						=	"A";
						     	$ORparam["order_first_name"] 			=	$firstName;
						     	$ORparam["order_last_name"] 			=	$lastName;
						     	$ORparam["order_users_country_code"] 	=	$countryCode;
						     	$ORparam["order_users_mobile"] 			=	$usersMobile;
						     	$ORparam["order_users_email"] 			=	$usersEmail;
								$ORparam["buyer_country_code"] 			= $buyerCountryCode;
							    $ORparam["buyer_mobile"] 			    = (int)$buyerMobile;
							    $ORparam["buyer_email"] 			    = $buyerEmail;
							    $ORparam["otp_sent"] 				    =   $otpSent;
						        $ORparam["commission_percentage"] 		=	$commission_percentage; 
						     	$ORparam["SMS"] 						=	$SMS;
						        $ORparam["is_printed"] 					=	'Y'; 
						     	
						     	if(!empty($raffleTickets)):
									$ORparam['raffle_mode']			    = $raffleMode;
									$ORparam['raffle_tickets']		    = $raffleTickets;
								endif;
								if(!empty($superBallMode)):
									$ORparam['super_ball_mode']			= $superBallMode;
									$ORparam['sb_tickect']		    	= $sbTickect;
								endif;

							    $ORparam["creation_ip"] 				=	$this->input->post('ip_address');
							    $ORparam["created_at"] 					=	date('Y-m-d H:i');
							    $ORparam["currentDatetime"] 			=	date('Y-m-d H:i:s');
							    // Saving order details for Ticket
							    $orderInsertID  = $this->mongodb_client->insertDocument('uw_lotto_orders', $ORparam, $session);	
							    $o_id           = (string)$orderInsertID['_id'];


								// SMS AND EMAIL CODE
								$message = "";
								if(!empty($ticket) && !empty($selectionValues)):
									$ticketLIST      = json_decode($ticket, true);
									$selectionValues = json_decode($selectionValues, true);
									$sbTickectList   = json_decode($sbTickect, true);

									$output        = [];
									$CouponDetails = '';
									$map = ['S', 'R', 'C'];
									foreach ($ticketLIST as $key => $tickets) {
	
										// Ticket numbers
										$line = implode(',', $tickets);
										// Append super ball for this ticket if present
										if (!empty($sbTickectList) && isset($sbTickectList[$key])) {
											$line .= ' + ' . $sbTickectList[$key];
										}
										$line .= ' (';
	
										// Selection letters
										$selected = [];
										foreach ($selectionValues[$key] as $sKey => $sVal) {
											if ($sVal > 0) {
												$selected[] = $map[$sKey];
											}
										}
	
										$line .= implode(',', $selected) . ')';
										$output[] = $line;
									}
	
									$CouponDetails = implode('. ', $output);
									$drawDate = date('d.m.Y h:iA', strtotime($drawDateNTime));
									$message = 'Order ID '.$ORparam["order_id"].' of '.$ORparam["product_title"].' with coupons '.$CouponDetails.' Ddate '.$drawDate.' You can download the invoice here https://tktinvoice.com/uwin-download-invoice/'.$ORparam["order_id"];
									
								endif;
								// if(!empty($buyerCountryCode) && !empty($buyerMobile) && !empty($message)):
							    // 	$this->sms_model->raffleWinnersSms($buyerCountryCode,$buyerMobile,$message);
							    // endif;
								// if(!empty($buyerCountryCode) && !empty($buyerMobile) && !empty($message)):
								// 	if($otpSent == "SMS" || empty($otpSent) ):
								// 		$this->sms_model->raffleWinnersSms($buyerCountryCode,$buyerMobile,$message);
								// 	elseif($otpSent == "WhatsApp"):
								// 		$senderDetails['country_code']  = $buyerCountryCode;
								// 		$senderDetails['users_mobile']  = $buyerMobile;
								// 		$senderDetails['message']       = $message;
								// 		$senderDetails['ORDERID']       = $ORparam["order_id"];
								// 		$senderDetails['CAMPAIGNAME']   = $ORparam["product_title"];
								// 		$senderDetails['CouponDetails'] = $CouponDetails;
								// 		$senderDetails['DDATE']         = $drawDate;
								// 		$senderDetails['LINK']          = 'https://tktinvoice.com/uwin-download-invoice/'.$ORparam["order_id"];
								// 		$this->sms_model->sendWhatsAppMessage($senderDetails);
								// 	endif;
							    // endif;

								if(!empty($buyerCountryCode) && !empty($buyerMobile) && !empty($message)):
									if($otpSent == "SMS" || empty($otpSent) ):

										$enableSmsFields = ['default_sms'];
										$enableTblName   = 'uw_enablesms';
										$enableSMSData   = $this->common_model->getSingleDataByParticularField($enableSmsFields,$enableTblName, 'status', 'A');
										$defaultSMSGateway = $enableSMSData['default_sms'];
										
										$senderDetails['gateway']       = $defaultSMSGateway;
										$senderDetails['users_mobile']  = $buyerMobile;
										$senderDetails['country_code']  = $buyerCountryCode;
										$senderDetails['message']       = $message;
										$result = $this->sms_model->sendSMS($senderDetails);
										// $this->sms_model->raffleWinnersSms($buyerCountryCode,$buyerMobile,$message);

									elseif($otpSent == "WhatsApp"):
										$senderDetails['country_code']  = $buyerCountryCode;
										$senderDetails['users_mobile']  = $buyerMobile;
										$senderDetails['message']       = $message;
										$senderDetails['ORDERID']       = $ORparam["order_id"];
										$senderDetails['CAMPAIGNAME']   = $ORparam["product_title"];
										$senderDetails['CouponDetails'] = $CouponDetails;
										$senderDetails['DDATE']         = $drawDate;
										$senderDetails['LINK']          = 'https://tktinvoice.com/uwin-download-invoice/'.$ORparam["order_id"];
										$this->sms_model->sendWhatsAppMessage($senderDetails);
									endif;
							    endif;

								if(!empty($buyerEmail) && !empty($message)):
									$subject = "Order Confirmation";
									$this->emailsendgrid_model->sendEmail($buyerEmail,$subject,$message);
								endif;


							    // echo $ProductData['_id']->{'$id'};

							    // Deduct the purchesed points and get available arabian points of user.
								$currentBal  = 	$this->geneal_model->debitPointsByAPI($totalPrice,$userId); 

								// Order capturing in order uw_loadbalance table..
					     		$narration1 = $raffleMode =='Y'? 'Raffle Order':'Order';
							    $fromuserparam["load_balance_id"]		 = (int)$this->geneal_model->getNextSequence('uw_loadBalance');
								// $fromuserparam["order_oid"] 			 = new MongoDB\BSON\ObjectId($orderInsertID['_id']->{'$id'});
								$fromuserparam["order_oid"] 			 = new MongoDB\BSON\ObjectId($o_id);
								// ècho $orderInsertID['_id']->{'$id'};   
								$fromuserparam["user_oid"] 				 = new MongoDB\BSON\ObjectId($user_oid);
								$fromuserparam["product_oid"] 			 = new MongoDB\BSON\ObjectId($productData['_id']['$id']);
								$fromuserparam["product_qty"] 			 = (int)$productQuantity;
								$fromuserparam["user_id_deb"]			 = (int)$userId;
								$fromuserparam["order_id"] 				 = $orderInsertID['order_id'];
								$fromuserparam["user_id_cred"] 			 = (int)0;
								$fromuserparam["upoints"] 				 = (float)$orderInsertID['total_price'];
								$fromuserparam["availableArabianPoints"] = (float)$orderInsertID['availableArabianPoints'];
								$fromuserparam["end_balance"] 			 = (float)$orderInsertID['end_balance'];
							    $fromuserparam["record_type"] 			 = 'Debit';
							    $fromuserparam["narration"]				 = $narration1;
							    $fromuserparam["remarks"]				 = 'Ticket ID : '.$orderInsertID['order_id'];
							    $fromuserparam["creation_ip"] 			 = $this->input->ip_address();
							    $fromuserparam["created_at"] 			 = date('Y-m-d H:i');
							    $fromuserparam["created_by"] 			 = (int)$userId;
							    $fromuserparam["status"]  				 =	"A";
						    	// $fromuserinsertResult = $this->geneal_model->addData('uw_loadBalance', $fromuserparam);
								$fromuserinsertResult = $this->mongodb_client->insertDocument('uw_loadBalance', $fromuserparam, $session);


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
								$commissionParam["product_oid"] 			 =	new MongoDB\BSON\ObjectId($productData['_id']->{'$id'});
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

								if($fromuserinsertResult || $FirstpurchaseinsertResult ){
									$session->commitTransaction();
		   							// $session->abortTransaction();
	    		                    unset($ORparam["draw_date"]);
		   							$result = $ORparam;
									echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
									return $orderInsertID;
							    }else{
								   $session->abortTransaction();
								   echo outPut(0,lang('BAD_REQUEST_CODE'),'Error: Unable to capture payment',[]);
								   die();
							    }
							elseif($sellerDetails['status'] == 'A' && $sellerDetails['availableArabianPoints'] < $totalPrice):
								throw new Exception(lang('LOW_BALANCE'), 1);
							elseif($SellerDetails['status'] == "I" || $SellerDetails['status'] == "D" ):
								throw new Exception(lang('ACCOUNT_BLOCKED'), 1);
							endif;

						elseif(!empty($productData) && $productData['status'] == 'I'):
							throw new Exception(lang('OUTOFSTOCK'). " - " .$productTitle , 1);
						elseif( strtotime($drawDateNTime) < strtotime($currentDatentime) ):
							throw new Exception(lang('RESTART_APP'), 1);
						else:
							throw new Exception(lang('PRODUCT_NOT_FOUND'), 1);
						endif;
					endif;
			    endif;
			else:
				throw new Exception(lang('FORBIDDEN_MSG'), 1);
			endif;
			
		} catch (Exception $e) {
			echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);	
		}
	 }
	
	
	/* * *********************************************************************
	 * * Function name : orderHistory
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used to fetch order history.
	 * * Date : 27 October 2023
	 * * Updated By    : Dilip Halder
	 * * Updated Date  : 17-06-2025 
	 * * **********************************************************************/
	public function orderHistory()
	{

		try {

			$apiHeaderData 		=	getApiHeaderData();
			$this->generatelogs->putLog('APP',logOutPut($_POST));
			$result 			= 	array();

			if(requestAuthenticate(APIKEY,'GET')):
				
				$usersId  = $this->input->get('users_id');
				$from     = $this->input->get('from');
				$to       = $this->input->get('to');
				
				if(empty($usersId)): 
	 		 		throw new Exception(lang('USER_ID_EMPTY'));
				else:

					if( !empty($from) && !empty($to) ):
				 		$wcon['where']['created_at'] = array('$gte' => $from ,'$lte' => $to );
				 	elseif(!empty($from)):
				 		$wcon['where']['created_at'] = array('$gte' => $from);
				 	elseif(!empty($to)):
				 		$wcon['where']['created_at'] = array('$lte' => $to);
				 	endif;
					$wcon['where']['user_id'] = (int)$usersId;
					
					$userOrderList = $this->common_model->orderHistory($wcon);
					// echo "<pre>";print_r($userOrderList);die();
					if(!empty($userOrderList)):
						$results = $userOrderList;
						echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);	
					else:
	 		 			throw new Exception(lang('DATA_NOT_FOUND'));
					endif;

				endif;
			else:
	 			throw new Exception(lang('FORBIDDEN_MSG'));
			endif;
		} catch (Exception $e) {
			echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);	
		}
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
				$totalProductDetails = array_values(array_unique($totalProductDetails, SORT_REGULAR));
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
		
			$userID 	= $this->input->get('users_id');
			$tickectID  = $this->input->post('tickect_id');

			if(empty($userID)):
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
			elseif(empty($tickectID)):
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_TICKET'),$result);die();
			else :

				/* Start function main section*/
				try {
						/* Checked User Validation */
						$requestFrom 	  = "app";
						$validationResult = $this->common_model->userValidate($userID,$requestFrom);

						// Checking Entered order. 
						$whereCon1['where'] = array('order_id' => $tickectID );
						$orderDetails 	    = $this->common_model->getOrderDetail($whereCon1);
						// echo "<pre>"; print_r($orderDetails); die();

						if(empty($orderDetails)):
							$this->hourlyGameWinners();
						endif;

						if(!empty($orderDetails) && $orderDetails['status'] == "A" ):
							
							// checking current draw date and time..
							$currentDate  = strtotime(date('Y-m-d H:i'));
							$drawDateTime = strtotime($orderDetails['draw_dateTime']);
							
							if($currentDate >= $drawDateTime):

								// Checking winning tickets..
								$tableName   = "uw_uwin_winner";
								$whereCon['where']['order_id']   = $tickectID;
								$whereCon['where']['status']     = (int)1;
								$whereCon['where']['created_at'] = array('$gte' => $orderDetails['created_at']);
								$checkWinner  = $this->common_model->getData('multiple',$tableName,$whereCon);
								
								if(!empty($checkWinner)):
									$result = $this->WinningPrize($checkWinner,$userID);
									if($result[0]['redeeming_amount_limit'] < $result[0]['amount']  && $userID != "100000000000110" ):
										$result[0]['winning_message'] = lang('BIG_WINNER_TEXT');
									endif;
									// echo "<pre>"; print_r($result); die();
									echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$result);	
								elseif($orderDetails['draw_id'] == $orderDetails['current_draw_id']):
									throw new Exception(lang('DRAW_ONGOIN'));
								else:
									throw new Exception(lang('NOT_WINNER'));
								endif;
							else:
								$Drawdate =  date('d/m/y',strtotime($orderDetails['draw_dateTime']));
								$Drawtime =  date('h:i A',strtotime($orderDetails['draw_dateTime']));
								$error 	  = "The draw is scheduled for $Drawdate at $Drawtime. Please check the results after the draw.";
								throw new Exception($error);
							endif;

						elseif($orderDetails['status'] == "CL"):
							throw new Exception(lang('CANCELLED_ORDER'));
						else:
							throw new Exception(lang('ORDET_ID_INVALID')); //Error added for invalid order id.
						endif;

				} catch (Exception $e) {
					$result = [];
					echo outPut(1,lang('SUCCESS_CODE'),$e->getMessage(),$result);	
				}
			endif;
	else:
		echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
	endif;
	}

	private function hourlyGameWinners($orderDetails='')
	{
		$result    = array();
		$userID    = $this->input->post('users_id') ?: $this->input->get('users_id');
		$tickectID = $this->input->post('tickect_id');

		try {
			if(empty($userID)) throw new Exception(lang('USER_ID_EMPTY'));
			if(empty($tickectID)) throw new Exception(lang('EMPTY_TICKET'));

			$this->common_model->userValidate($userID, "app");

			$whereCon['where']['order_id'] = $tickectID;
			$resultData = $this->common_model->getHourlyGameOrderData('single', "uw_hourly_orders", $whereCon,'', '', '');
			$orderData = $resultData[0];

			if($orderData['draw_time'] > strtotime(date('Y-m-d H:i:s'))):
				$Drawdate =  date('d/m/y',$orderData['draw_time']);
				$Drawtime =  date('h:i A',$orderData['draw_time']);
				$error 	  = "The draw is scheduled for $Drawdate at $Drawtime. Please check the results after the draw.";
				throw new Exception($error);
			endif;

			if(empty($orderData)) throw new Exception(lang('ORDET_ID_INVALID'));
			if(isset($orderData['status']) && $orderData['status'] == 'CL') throw new Exception(lang('CANCELLED_ORDER'));
			if((!isset($orderData['is_winner']) || $orderData['is_winner'] != 'Y'  || $orderData['winning_status'] == 'Inactive'|| $orderData['winning_status'] == 'inactive'||  $orderData['winning_status'] == 'Deleted')) throw new Exception(lang('NOT_WINNER'));

			$winningDetails = array();
			if(!empty($orderData['winning_details'])):
				$winningDetails = is_array($orderData['winning_details'])
					? $orderData['winning_details']
					: (json_decode($orderData['winning_details'], true) ?: array());
			endif;

			// if(empty($winningDetails)):
			// 	$winningDetails[] = array(
			// 		'coupon_code'      => $orderData['coupon_code'] ?? 'N/A',
			// 		'matching_coupons' => $orderData['matching_coupons'] ?? 'N/A',
			// 		'winner_type'      => $orderData['winner_type'] ?? 'N/A',
			// 		'winning_amount'   => $orderData['winning_amount'] ?? 0,
			// 		'status'           => $orderData['status'] ?? 'N/A',
			// 	);
			// endif;
			
			$matchingCode = array(); $matchingAmount = array(); $winnerType = array();$couponCode =array();
			foreach($winningDetails as $item):
				$item = is_object($item) ? (array)$item : $item;
				if($item['status'] == 'A'  && $item['winning_amount'] > '0' && ( $orderData['batch_id'] == $item['batch_id'] ) ):
					$matchingCode[] = (string)($item['matching_coupons'] ?? ($item['matching_coupon'] ?? 'N/A'));
					$couponCode[]   = (string)($item['coupon_code'] ?? ($item['coupon_code'] ?? 'N/A'));
					$matchingAmount[] = (string)($item['winning_amount'] ?? '0');
					$winnerType[] = (string)($item['winner_type'] ?? 'N/A');
				endif;
			endforeach;

			$userDetails = $this->common_model->getSingleDataByParticularField(
				array('_id','users_id','area','redeeming_amount_limit'),
				'uw_users',
				'users_id',
				(int)$userID
			);

			$redeemByMode = $orderData['redeem_by_mode'] ?? '';
			$redeemStatus = $orderData['winning_status'];
			$redeemMessage = $redeemStatus == 'paid'
				? ($redeemByMode == 'wallet' ? "Already moved to the user's wallet." : "Already paid.")
				: 'Not redeemed yet.';

			$amount = (string)($orderData['winning_amount'] ?? 0);
			$redeemLimit = (string)($userDetails['redeeming_amount_limit'] ?? '499');
			$winningMessage = ((float)$amount > (float)$redeemLimit) ? lang('BIG_WINNER_TEXT') : '';

			$createdAt = '';
			if(!empty($orderData['created_at'])):
				$createdAt = date('Y-m-d h:i A', is_numeric($orderData['created_at']) ? (int)$orderData['created_at'] : strtotime($orderData['created_at']));
			endif;

			$modifiedAt = '';
			if(!empty($orderData['update_date']) && is_numeric($orderData['update_date'])):
				$modifiedAt = date('Y-m-d h:i A', (int)$orderData['update_date']);
			elseif(!empty($orderData['winner_uploaded_at']) && is_numeric($orderData['winner_uploaded_at'])):
				$modifiedAt = date('Y-m-d h:i A', (int)$orderData['winner_uploaded_at']);
			elseif(!empty($orderData['modified_at'])):
				$modifiedAt = date('Y-m-d h:i A', strtotime($orderData['modified_at']));
			endif;


			$code = implode(' / ', $couponCode);

			$result[] = array(
				'voucher_id'             => $orderData['voucher_id'] ?? 0,
				'pos_number'             => !empty($orderData['seller_pos_number']) ? $orderData['seller_pos_number'] : 'N/A',
				'order_id'               => $orderData['order_id'] ?? $tickectID,
				'seller_first_name'      => $orderData['seller_users_name'] ?? ($orderData['seller_users_name'] ?? 'N/A'),
				'seller_last_name'       => $orderData['seller_users_last_name'] ?? ($orderData['seller_users_last_name'] ?? 'N/A'),
				'code'                   => $code,
				'matching_code'          => $matchingCode,
				'matching_amount'        => $matchingAmount,
				'amount'                 => $amount,
				'winner_type'            => $winnerType,
				'status'                 => (isset($orderData['status']) && is_numeric($orderData['status'])) ? (int)$orderData['status'] : 1,
				'area'                   => $orderData['seller_store_area'] ?? ($orderData['seller_store_area'] ?? 'N/A'),
				'redeeming_amount_limit' => $redeemLimit,
				'products_id'            => (int)($orderData['product_id'] ?? 0),
				'created_at'             => $createdAt,
				'modified_at'            => $modifiedAt,
				'redeem_status_message'  => $redeemMessage,
				'redeem_by_mode'         => $redeemByMode,
				'redeem_status'          => $redeemStatus,
				'seller_id'              => isset($orderData['seller_id']) ? (int)$orderData['seller_id'] : (int)$userID,
				'winning_message'        => $winningMessage
			);

			echo outPut(1, lang('SUCCESS_CODE'), lang('SUCCESS_ACTION'), $result);
		} catch(Exception $e) {
			echo outPut(1, lang('SUCCESS_CODE'), $e->getMessage(), $result);
		}
		die();
	}

	private function WinningPrize($WinnerList='',$userID="")
	{
		$totalPrizeAmount    = 0;
		$totalPaidAmount     = 0;
	 	$totalCode 			 = array();
	 	$totalMatchingCode 	 = array();
	 	$totalMatchingAmount = array();
	 	$totalWinnerType 	 = array();



	 	foreach ($WinnerList as $key => $items) :
	 	if($items['redeem_status'] != 'paid'):
	 	 $totalPrizeAmount   	= $totalPrizeAmount+ $items['amount'];
	 	else:
	 	 $totalPaidAmount   	= $totalPaidAmount+ $items['amount'];
		
		 if($items['redeem_by_mode'] == 'wallet'):
			$redeem_status_message = "Already moved to the user's wallet.";
		 else:
			$redeem_status_message = "Already paid.";
		 endif;

	 	endif;	

	 	 $totalCode[]   		= $items['coupons']?$items['coupons']:$items['code'];
	 	 $totalMatchingCode[]   = $items['code'];
	 	 $totalMatchingAmount[] = $items['amount'];
	 	 $totalWinnerType[] 	= $items['winner_type']?$items['winner_type']:'N/A';
	 	endforeach;


	 	$totalCode = implode(' / ', $totalCode);

	 	$tableName1	   = "uw_lotto_orders";
	    $Fields 	   = array('_id','user_id','created_at','pos_number');
 	    $orderDetails  = $this->common_model->getSingleDataByParticularField($Fields,$tableName1,'order_id',$items['order_id']);
 	   	$order_date    = $orderDetails['created_at'];

 	    if(!empty($userID)):
			$tableName1	 = "uw_users";
		    $Fields 	 = array('_id','user_id' ,'area','redeeming_amount_limit');
	 	    $userDetails = $this->common_model->getSingleDataByParticularField($Fields,$tableName1,'users_id',(int)$orderDetails['user_id']);
		// else:
 	    	$tableName1	 = "uw_users";
		    $Fields 	 = array('_id','user_id' ,'area','redeeming_amount_limit');
	 	    $sellerInfo = $this->common_model->getSingleDataByParticularField($Fields,$tableName1,'users_id',(int)$userID );
 	    endif;
		  
 	     

	 	$winerList['voucher_id'] 		= $items['voucher_id'];
	 	$winerList['pos_number']   		= $orderDetails['pos_number']?$orderDetails['pos_number']:'N/A';
	 	$winerList['order_id']   		= $items['order_id'];
	 	$winerList['seller_first_name'] = $items['seller_first_name'];
	 	$winerList['seller_last_name'] 	= $items['seller_last_name'];
	 	$winerList['code'] 				= $totalCode;
	 	$winerList['matching_code'] 	= $totalMatchingCode;
	 	$winerList['matching_amount'] 	= $totalMatchingAmount;
	 	$winerList['amount'] 			= $totalPrizeAmount?(string)$totalPrizeAmount:(string)$totalPaidAmount;
	 	$winerList['winner_type'] 		= $totalWinnerType;
	 	$winerList['status'] 			= $items['status'];
	 	$winerList['area'] 				= $userDetails['area'];
		$winerList['redeeming_amount_limit'] = $sellerInfo['redeeming_amount_limit']?$sellerInfo['redeeming_amount_limit']:'499';
	 	$winerList['products_id'] 		= $items['products_id'];
	 	// $winerList['created_at'] 		= $orderDetails['created_at'];
	 	// $winerList['created_by'] 		= $items['created_by'];
		$winerList["created_at"]        =   date('Y-m-d h:i A',strtotime($order_date)) ;
		$winerList["modified_at"]       =   date('Y-m-d h:i A',strtotime($items['modified_at']));
		$winerList['redeem_status_message'] = $redeem_status_message;
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

 		return $results;
	}

	/* * *********************************************************************
	 * * Function name : redeemByCash
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to Redeem Coupons.
	 * * Date 		   : 02 February 2024
	 * * Updated By    : Dilip Halder
	 * * Updated Date  : 24 February 2024
	 * * **********************************************************************/
	//  public function redeemByMode()
	//  {
	// 	$apiHeaderData 		=	getApiHeaderData();
	// 	$this->generatelogs->putLog('APP',logOutPut($_POST));
	// 	$result 			= 	array();

	// 	if(requestAuthenticate(APIKEY,'POST')):
	// 		$userID 	  =  $this->input->post('users_id');
	// 		$tickectID 	  =  $this->input->post('tickect_id');
	// 		// $voucherId 	  =  $this->input->post('voucher_id');
	// 		$redeemStatus =  $this->input->post('redeem_status');
	// 		$redeemByMode =  $this->input->post('redeem_by_mode');
	// 		$posDeviceId  =  $this->input->post('pos_device_id');
	// 		$ipAddress    =  $this->input->post('ip_address');

	// 		try {

	// 			if(empty($userID)):
	// 				throw new Exception(lang('USER_ID_EMPTY'));
	// 			elseif(empty($tickectID)):
	// 				throw new Exception(lang('EMPTY_TICKET'));
	// 			// elseif(empty($voucherId)):
	// 			// 	throw new Exception(lang('EMPTY_VOUCHER'));
	// 			elseif(empty($redeemStatus)):
	// 				throw new Exception(lang('EMPTY_REDEEMSTATUS'));
	// 			elseif(empty($redeemStatus)):
	// 				throw new Exception(lang('EMPTY_RedeemByMode'));
	// 			else:

	// 				/* Checked User Validation */
	// 				$requestFrom 	  = "app";
	// 				$validationResult = $this->common_model->userValidate($userID,$requestFrom);

	// 				$whereCon1['where'] = array('order_id' => $tickectID );
	// 				$orderDetails 	    = $this->common_model->getOrderDetail($whereCon1);
	// 				// echo "<pre>";print_r($orderDetails);die();
	// 				if(!empty($orderDetails) && $orderDetails['status'] == "A" ):
						
	// 					// Checking winning tickets..
	// 					$tableName  = "uw_uwin_winner";
	// 					$whereCon['where']['order_id']   = $tickectID;
	// 					$whereCon['where']['status']     = (int)1;
	// 					$whereCon['where']['created_at'] = array('$gte' => $orderDetails['created_at']);
	// 					$WinnerList  = $this->common_model->getData('multiple',$tableName,$whereCon);

	// 					if(!empty($WinnerList)):
							
	// 						$totalPrizeAmount     = 0;
	// 						$totalPaidPrizeAmount = 0;
	// 						foreach ($WinnerList as $key => $items) :
	// 							if($items['redeem_status'] != 'paid' && $items['status'] == 1 ):
	// 							$totalPrizeAmount     = $totalPrizeAmount+ $items['amount'];
	// 							elseif($totalPaidPrizeAmount == 'paid' && $items['status'] == 1):
	// 							$totalPaidPrizeAmount = $totalPaidPrizeAmount+ $items['amount'];
	// 							endif;
	// 						endforeach;

	// 						$tableName1	    = "uw_users";
	// 						$Fields 	    = array('_id','users_id' ,'availableArabianPoints','redeeming_amount_limit');
	// 						$sellerDetails  = $this->common_model->getSingleDataByParticularField($Fields,$tableName1,'users_id',(int)$userID);
	// 						$redeeming_amount_limit = $sellerDetails['redeeming_amount_limit']?$sellerDetails['redeeming_amount_limit'] : 999; 
							
	// 						if($totalPrizeAmount > $redeeming_amount_limit &&  $userID != 100000000000110):
	// 							throw new Exception(lang('BIG_WINNER_TEXT'));
	// 						elseif(!empty($totalPrizeAmount) && $totalPrizeAmount >=1 ):
	// 							//fetching placed order details..
	// 							if(!empty($orderDetails)):
	// 								// Added redeeming Param..
	// 								$updateParams['pos_device_id'] 	= $posDeviceId;
	// 								$updateParams["user_type"]      = $orderDetails['user_type'];
	// 								$updateParams['redeem_status'] 	= $redeemStatus;
	// 								$updateParams['redeem_by_mode'] = $redeemByMode;
	// 								$updateParams["modified_at"]    = date('Y-m-d H:i');
	// 								$updateParams['created_ip'] 	= $ip_address;
	// 								$updateParams['seller_id'] 		= (int)$userID;
	// 								$WInnner_whereCon = array('order_id' => $tickectID, 'status' => (int)'1','redeem_status' => array('$ne' => 'paid') );
	// 								$record = $this->common_model->editMultipleDataByMultipleCondition('uw_uwin_winner', $updateParams,$WInnner_whereCon);
									
	// 								/* Load Balance Table -- after Sign Up*/
	// 								$Redeemparam["load_balance_id"]          =   (int)$this->geneal_model->getNextSequence('uw_loadBalance');
	// 								$Redeemparam["user_oid"]        	     =   new MongoDB\BSON\ObjectId($sellerDetails['_id']['$id']);
	// 								$Redeemparam["order_oid"]       		 =   new MongoDB\BSON\ObjectId($orderDetails['_id']->{'$id'});
	// 								$Redeemparam["order_id"]       		     =   $tickectID;
	// 								$Redeemparam["user_type"]       		 =   $orderDetails['user_type'];
	// 								$Redeemparam["product_id"]       		 =   (int)$orderDetails['product_id'];
	// 								$Redeemparam["user_id_deb"]              =   (int)$userID;
	// 								$Redeemparam["user_id_cred"]             =   (int)0;
	// 								$Redeemparam["upoints"]       		     =   (float)$totalPrizeAmount;
	// 								$Redeemparam["record_type"]              =   'Debit';
	// 								$Redeemparam["narration"]  			     =   'Redeem Prize';
	// 								$Redeemparam["remarks"]  			     =   "Redeemed Prize ".$totalPrizeAmount." AED in cash";
	// 								$Redeemparam["availableArabianPoints"] 	 =   (float)$sellerDetails['availableArabianPoints'];
	// 								$Redeemparam["end_balance"] 		 	 =   (float)$sellerDetails['availableArabianPoints'];
	// 								$Redeemparam["creation_ip"]         	 =   currentIp();
	// 								$Redeemparam["created_at"]          	 =   date('Y-m-d H:i');
	// 								$Redeemparam["created_by"]         	  	 =   (int)$userID;
	// 								$Redeemparam["status"]               	 =   "A";
									
	// 								$reddemData = $this->common_model->addData('uw_loadBalance', $Redeemparam);
	// 								$this->geneal_model->addRedeem_Cash_Amount_TO_Seller($userID,(float)$totalPrizeAmount);
									
	// 								//Adding loadbalkance records for users..
	// 								if($orderDetails['user_type'] == 'Users'):

	// 									// User Details
	// 									$tableName1	  = "uw_users";
	// 									$Fields 	  = array('_id','users_id' ,'availableArabianPoints','redeeming_amount_limit');
	// 									$userDetails  = $this->common_model->getSingleDataByParticularField($Fields,$tableName1,'users_id',(int)$orderDetails['user_id']);

	// 									/* Load Balance Table -- after Sign Up*/
	// 									$Redeemparam11["load_balance_id"]          = (int)$this->geneal_model->getNextSequence('uw_loadBalance');
	// 									$Redeemparam11["user_oid"]        	       = new MongoDB\BSON\ObjectId((string)$orderDetails['user_oid']);
	// 									$Redeemparam11['request_id'] 			   = new MongoDB\BSON\ObjectId($reddemData['_id']->{'$id'});
	// 									$Redeemparam11["order_id"]       		   = $tickectID;
	// 									$Redeemparam11["user_id_deb"]              = (int)0;
	// 									$Redeemparam11["user_id_cred"]             = (int)$orderDetails['user_id'];
	// 									$Redeemparam11["upoints"]       		   = (float)$totalPrizeAmount;
	// 									$Redeemparam11["record_type"]              = 'Credit';
	// 									$Redeemparam11["narration"]  			   = 'Winning Ticket Redeemed';
	// 									$Redeemparam11["remarks"]  			       = "Completed ( " .$tickectID." )";
	// 									$Redeemparam11["availableArabianPoints"]   = (float)$userDetails['availableArabianPoints'];
	// 									$Redeemparam11["end_balance"] 		 	   = (float)$userDetails['availableArabianPoints'];
	// 									$Redeemparam11["creation_ip"]         	   = currentIp();
	// 									$Redeemparam11["created_at"]          	   = date('Y-m-d H:i');
	// 									$Redeemparam11["created_by"]         	   = (int)$orderDetails['user_id'];
	// 									$Redeemparam11["status"]               	   = "A";

	// 									$this->geneal_model->addData('uw_loadBalance', $Redeemparam11);

	// 									$commission_percentage = 5;
	// 									$commission_amount     = $totalPrizeAmount*$commission_percentage/100;
	// 									/* Load Balance Table -- after Cash voucher Redeeming */
	// 									$RedeemCashparam["load_balance_id"]          = (int)$this->geneal_model->getNextSequence('uw_loadBalance');
	// 									$RedeemCashparam["request_id"]               = $VoucherData['voucher_id'];
	// 									$RedeemCashparam['request_oid'] 			 = new MongoDB\BSON\ObjectId($reddemData['_id']->{'$id'});
	// 									$RedeemCashparam["user_oid"]                 = new MongoDB\BSON\ObjectId($sellerDetails['_id']['$id']);
	// 									$RedeemCashparam["order_oid"]                = new MongoDB\BSON\ObjectId($orderDetails['_id']->{'$id'});
	// 									$RedeemCashparam["order_id"]                 = $orderDetails['order_id'];
	// 									$RedeemCashparam["user_type"]                = $orderDetails['user_type'];
	// 									$RedeemCashparam["product_id"]               = $orderDetails['product_id'];
	// 									$RedeemCashparam["user_id_deb"]              = (int)0;
	// 									$RedeemCashparam["user_id_cred"]             = (int)$sellerDetails['users_id'];
	// 									$RedeemCashparam["upoints"]                  = (float)$commission_amount;
	// 									$RedeemCashparam["record_type"]              = "Debit";
	// 									$RedeemCashparam["narration"]                = "Redeem Prize Commission";
	// 									$RedeemCashparam["remarks"]                  = "Redeemed Prize ".$totalPrizeAmount." AED in cash for " .$tickectID;
	// 									$RedeemCashparam["availableArabianPoints"]   = (float)$sellerDetails['availableArabianPoints'];
	// 									$RedeemCashparam["end_balance"]              = (float)$sellerDetails['availableArabianPoints'] + $commission_amount;
	// 									$RedeemCashparam["creation_ip"]              = currentIp();
	// 									$RedeemCashparam["created_at"]               = date('Y-m-d H:i');
	// 									$RedeemCashparam["created_by"]               = (int)$users_id;
	// 									$RedeemCashparam["status"]                   = "A";
	// 									$this->geneal_model->addData('uw_loadBalance', $RedeemCashparam);

	// 									//Creaditing amount to selelr account..
	// 									$availableArabianPoints = (float)$sellerDetails['availableArabianPoints'];
	// 									$totalArabianPoints     = (float)$sellerDetails['totalArabianPoints'] + (float)$commission_amount;

	// 									$uparam['availableArabianPoints']    =   $availableArabianPoints + $commission_amount;
	// 									$uparam['totalArabianPoints']        =   $totalArabianPoints;
	// 									$uparam['update_date']               =   date('Y-m-d h:m');
	// 									$isUpdate = $this->common_model->editData('uw_users',$uparam, 'users_id',(int)$sellerDetails['users_id']);

	// 									$result = array('payment_date' => date('Y-m-d H:i'));
	// 								endif;

	// 								$result = array('payment_date' => date('Y-m-d H:i'));
	// 								echo outPut(1,lang('SUCCESS_CODE'),lang('COUPON_REDEEMED_SUCCESFULLY'),$result);die();
	// 							else:
	// 								throw new Exception(lang('ORDET_ID_INVALID'));
	// 							endif;
								
	// 						elseif(!empty($totalPaidPrizeAmount) && $totalPaidPrizeAmount >=1 ):
	// 							throw new Exception(lang('ALREADY_REDEEM'));
	// 						endif;
	// 					else:
	// 						throw new Exception(lang('NOT_WINNER'));
	// 					endif;
						
	// 				elseif($orderDetails['status'] == "CL"):
	// 					throw new Exception(lang('CANCELLED_ORDER'));
	// 				else:
	// 					throw new Exception(lang('ORDET_ID_INVALID')); //Error added for invalid order id.
	// 				endif;
				
	// 			endif;
	// 		} catch (Exception $e) {
	// 			echo outPut(0, lang('SUCCESS_CODE'), $e->getMessage());
	// 		}
	// 	else:
	// 		echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
	// 	endif;
	//  }
    public function redeemByMode()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 			= 	array();

		if(requestAuthenticate(APIKEY,'POST')):
			$userID 	  =  $this->input->post('users_id');
			$tickectID 	  =  $this->input->post('tickect_id');
			// $voucherId 	  =  $this->input->post('voucher_id');
			$redeemStatus =  $this->input->post('redeem_status');
			$redeemByMode =  $this->input->post('redeem_by_mode');
			$posDeviceId  =  $this->input->post('pos_device_id');
			$ipAddress    =  $this->input->post('ip_address');

			try {

				if(empty($userID)):
					throw new Exception(lang('USER_ID_EMPTY'));
				elseif(empty($tickectID)):
					throw new Exception(lang('EMPTY_TICKET'));
				// elseif(empty($voucherId)):
				// 	throw new Exception(lang('EMPTY_VOUCHER'));
				elseif(empty($redeemStatus)):
					throw new Exception(lang('EMPTY_REDEEMSTATUS'));
				elseif(empty($redeemStatus)):
					throw new Exception(lang('EMPTY_RedeemByMode'));
				else:

					/* Checked User Validation */
					$requestFrom 	  = "app";
					$validationResult = $this->common_model->userValidate($userID,$requestFrom);

					$whereCon1['where'] = array('order_id' => $tickectID );
					$orderDetails 	    = $this->common_model->getOrderDetail($whereCon1);
					// echo "<pre>";print_r($orderDetails);die();

					if(empty($orderDetails)):
						$this->hourlyGameWinnersRedeemByMode();
					endif;
					
					if(!empty($orderDetails) && $orderDetails['status'] == "A" ):
						
						// Checking winning tickets..
						$tableName  = "uw_uwin_winner";
						$whereCon['where']['order_id']   = $tickectID;
						$whereCon['where']['status']     = (int)1;
						$whereCon['where']['created_at'] = array('$gte' => $orderDetails['created_at']);
						$WinnerList  = $this->common_model->getData('multiple',$tableName,$whereCon);

						if(!empty($WinnerList)):
							
							$totalPrizeAmount     = 0;
							$totalPaidPrizeAmount = 0;
							foreach ($WinnerList as $key => $items) :
								if($items['redeem_status'] != 'paid' && $items['status'] == 1 ):
								$totalPrizeAmount     = $totalPrizeAmount+ $items['amount'];
								elseif($totalPaidPrizeAmount == 'paid' && $items['status'] == 1):
								$totalPaidPrizeAmount = $totalPaidPrizeAmount+ $items['amount'];
								endif;
							endforeach;

							$tableName1	    = "uw_users";
							$Fields 	    = array('_id','users_id' ,'availableArabianPoints','redeeming_amount_limit');
							$sellerDetails  = $this->common_model->getSingleDataByParticularField($Fields,$tableName1,'users_id',(int)$userID);
							$defaultRedeemingAmountLimit = 499;
							$redeeming_amount_limit = $sellerDetails['redeeming_amount_limit']?$sellerDetails['redeeming_amount_limit'] : $defaultRedeemingAmountLimit; 
							
							if($totalPrizeAmount > $redeeming_amount_limit &&  $userID != 100000000000110):
								throw new Exception(lang('BIG_WINNER_TEXT'));
							elseif(!empty($totalPrizeAmount) && $totalPrizeAmount >=1 ):
								//fetching placed order details..
								if(!empty($orderDetails)):
									// Added redeeming Param..
									$this->session->sess_regenerate();
									$session = $this->mongodb_client->client->startSession();
									$session->startTransaction();
									$updateParams['pos_device_id'] 	= $posDeviceId;
									$updateParams["user_type"]      = $orderDetails['user_type'];
									$updateParams['redeem_status'] 	= $redeemStatus;
									$updateParams['redeem_by_mode'] = $redeemByMode;
									$updateParams["modified_at"]    = date('Y-m-d H:i');
									$updateParams['created_ip'] 	= $ip_address;
									$updateParams['seller_id'] 		= (int)$userID;
									$WInnner_whereCon = array('order_id' => $tickectID, 'status' => (int)'1','redeem_status' => array('$ne' => 'paid') );
									// $record = $this->common_model->editMultipleDataByMultipleCondition('uw_uwin_winner', $updateParams,$WInnner_whereCon);
									$record = $this->mongodb_client->updateDocument(
										'uw_uwin_winner',         // Collection name
										$WInnner_whereCon,        // Filter / condition for which document to update
										['$set' => $updateParams],// Proper MongoDB update syntax
										$session                  // MongoDB session (optional)
									);
										
									/* Load Balance Table -- after Sign Up*/
									$Redeemparam["load_balance_id"]          =   (int)$this->geneal_model->getNextSequence('uw_loadBalance');
									$Redeemparam["user_oid"]        	     =   new MongoDB\BSON\ObjectId($sellerDetails['_id']['$id']);
									$Redeemparam["order_oid"]       		 =   new MongoDB\BSON\ObjectId($orderDetails['_id']->{'$id'});
									$Redeemparam["order_id"]       		     =   $tickectID;
									$Redeemparam["user_type"]       		 =   $orderDetails['user_type'];
									$Redeemparam["product_id"]       		 =   (int)$orderDetails['product_id'];
									$Redeemparam["user_id_deb"]              =   (int)$userID;
									$Redeemparam["user_id_cred"]             =   (int)0;
									$Redeemparam["upoints"]       		     =   (float)$totalPrizeAmount;
									$Redeemparam["record_type"]              =   'Debit';
									$Redeemparam["narration"]  			     =   'Redeem Prize';
									$Redeemparam["remarks"]  			     =   "Redeemed Prize ".$totalPrizeAmount." AED in cash";
									$Redeemparam["availableArabianPoints"] 	 =   (float)$sellerDetails['availableArabianPoints'];
									$Redeemparam["end_balance"] 		 	 =   (float)$sellerDetails['availableArabianPoints'];
									$Redeemparam["creation_ip"]         	 =   currentIp();
									$Redeemparam["created_at"]          	 =   date('Y-m-d H:i');
									$Redeemparam["created_by"]         	  	 =   (int)$userID;
									$Redeemparam["status"]               	 =   "A";
									
									// $reddemData = $this->geneal_model->addData('uw_loadBalance', $Redeemparam);
									$reddemData  = $this->mongodb_client->insertDocument('uw_loadBalance', $Redeemparam, $session);
									if (is_object($reddemData['_id']) && property_exists($reddemData['_id'], '$id')) {
										$reddem_id = new MongoDB\BSON\ObjectId($reddemData['_id']->{'$id'});
									} else {
										$reddem_id = new MongoDB\BSON\ObjectId($reddemData['_id']);
									}
									
									$this->geneal_model->addRedeem_Cash_Amount_TO_Seller($userID,(float)$totalPrizeAmount);
									
									//Adding loadbalkance records for users..
									if($orderDetails['user_type'] == 'Users'):

										// User Details
										$tableName1	  = "uw_users";
										$Fields 	  = array('_id','users_id' ,'availableArabianPoints','redeeming_amount_limit');
										$userDetails  = $this->common_model->getSingleDataByParticularField($Fields,$tableName1,'users_id',(int)$orderDetails['user_id']);

										/* Load Balance Table -- after Sign Up*/
										$Redeemparam11["load_balance_id"]          = (int)$this->geneal_model->getNextSequence('uw_loadBalance');
										$Redeemparam11["user_oid"]        	       = new MongoDB\BSON\ObjectId((string)$orderDetails['user_oid']);
										$Redeemparam11['request_id'] 			   = $reddem_id;
										$Redeemparam11["order_id"]       		   = $tickectID;
										$Redeemparam11["user_id_deb"]              = (int)0;
										$Redeemparam11["user_id_cred"]             = (int)$orderDetails['user_id'];
										$Redeemparam11["upoints"]       		   = (float)$totalPrizeAmount;
										$Redeemparam11["record_type"]              = 'Credit';
										$Redeemparam11["narration"]  			   = 'Winning Ticket Redeemed';
										$Redeemparam11["remarks"]  			       = "Completed ( " .$tickectID." )";
										$Redeemparam11["availableArabianPoints"]   = (float)$userDetails['availableArabianPoints'];
										$Redeemparam11["end_balance"] 		 	   = (float)$userDetails['availableArabianPoints'];
										$Redeemparam11["creation_ip"]         	   = currentIp();
										$Redeemparam11["created_at"]          	   = date('Y-m-d H:i');
										$Redeemparam11["created_by"]         	   = (int)$orderDetails['user_id'];
										$Redeemparam11["status"]               	   = "A";
										$red2 = $this->mongodb_client->insertDocument('uw_loadBalance', $Redeemparam11, $session);
										// $this->geneal_model->addData('uw_loadBalance', $Redeemparam11);

										$commission_percentage = 5;
										$commission_amount     = $totalPrizeAmount*$commission_percentage/100;
										/* Load Balance Table -- after Cash voucher Redeeming */
										$RedeemCashparam["load_balance_id"]          = (int)$this->geneal_model->getNextSequence('uw_loadBalance');
										$RedeemCashparam["request_id"]               = $VoucherData['voucher_id'];
										$RedeemCashparam['request_oid'] 			 = $reddem_id;
										$RedeemCashparam["user_oid"]                 = new MongoDB\BSON\ObjectId($sellerDetails['_id']['$id']);
										$RedeemCashparam["order_oid"]                = new MongoDB\BSON\ObjectId($orderDetails['_id']->{'$id'});
										$RedeemCashparam["order_id"]                 = $orderDetails['order_id'];
										$RedeemCashparam["user_type"]                = $orderDetails['user_type'];
										$RedeemCashparam["product_id"]               = $orderDetails['product_id'];
										$RedeemCashparam["user_id_deb"]              = (int)0;
										$RedeemCashparam["user_id_cred"]             = (int)$sellerDetails['users_id'];
										$RedeemCashparam["upoints"]                  = (float)$commission_amount;
										$RedeemCashparam["record_type"]              = "Debit";
										$RedeemCashparam["narration"]                = "Redeem Prize Commission";
										$RedeemCashparam["remarks"]                  = "Redeemed Prize ".$totalPrizeAmount." AED in cash for " .$tickectID;
										$RedeemCashparam["availableArabianPoints"]   = (float)$sellerDetails['availableArabianPoints'];
										$RedeemCashparam["end_balance"]              = (float)$sellerDetails['availableArabianPoints'] + $commission_amount;
										$RedeemCashparam["creation_ip"]              = currentIp();
										$RedeemCashparam["created_at"]               = date('Y-m-d H:i');
										$RedeemCashparam["created_by"]               = (int)$users_id;
										$RedeemCashparam["status"]                   = "A";
										// $this->geneal_model->addData('uw_loadBalance', $RedeemCashparam);
										$this->mongodb_client->insertDocument('uw_loadBalance', $RedeemCashparam, $session);
										//Creaditing amount to selelr account..
										$availableArabianPoints = (float)$sellerDetails['availableArabianPoints'];
										$totalArabianPoints     = (float)$sellerDetails['totalArabianPoints'] + (float)$commission_amount;

										$uparam['availableArabianPoints']    =   $availableArabianPoints + $commission_amount;
										$uparam['totalArabianPoints']        =   $totalArabianPoints;
										$uparam['update_date']               =   date('Y-m-d h:m');
										$isUpdate = $this->common_model->editData('uw_users',$uparam, 'users_id',(int)$sellerDetails['users_id']);
										
										$result = array('payment_date' => date('Y-m-d H:i'));
									endif;
									if($record && $reddemData ){
											if($totalPrizeAmount >$defaultRedeemingAmountLimit  && $userID != 100000000000110):
												$uLimitParam['redeeming_amount_limit']    = $defaultRedeemingAmountLimit;
												$uLimitParam['update_date']               =   date('Y-m-d h:m');
												$this->common_model->editData('uw_users',$uLimitParam, 'users_id',(int)$userID);
											endif;
										$session->commitTransaction();
										$result = array('payment_date' => date('Y-m-d H:i'));
										echo outPut(1,lang('SUCCESS_CODE'),lang('COUPON_REDEEMED_SUCCESFULLY'),$result);die(); 
									}else{
										$session->abortTransaction();
										echo outPut(0,lang('BAD_REQUEST_CODE'),'Error: Unable to reddem',[]);
										die();
							    	}
									
								else:
									throw new Exception(lang('ORDET_ID_INVALID'));
								endif;
								
							elseif(!empty($totalPaidPrizeAmount) && $totalPaidPrizeAmount >=1 ):
								throw new Exception(lang('ALREADY_REDEEM'));
							endif;
						else:
							throw new Exception(lang('NOT_WINNER'));
						endif;
						
					elseif($orderDetails['status'] == "CL"):
						throw new Exception(lang('CANCELLED_ORDER'));
					else:
						throw new Exception(lang('ORDET_ID_INVALID')); //Error added for invalid order id.
					endif;
				
				endif;
			} catch (Exception $e) {
				echo outPut(0, lang('SUCCESS_CODE'), $e->getMessage());
			}
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	private function hourlyGameWinnersRedeemByMode()
	{
		$result = array();
		$userID = $this->input->post('users_id');
		$tickectID = $this->input->post('tickect_id');
		$redeemStatus = $this->input->post('redeem_status');
		$redeemByMode = $this->input->post('redeem_by_mode');
		$posDeviceId = $this->input->post('pos_device_id');
		$ipAddress = $this->input->post('ip_address');

		try {
			if(empty($userID)):
				throw new Exception(lang('USER_ID_EMPTY'));
			elseif(empty($tickectID)):
				throw new Exception(lang('EMPTY_TICKET'));
			elseif(empty($redeemStatus)):
				throw new Exception(lang('EMPTY_REDEEMSTATUS'));
			elseif(empty($redeemByMode)):
				throw new Exception(lang('EMPTY_RedeemByMode'));
			endif;

			$requestFrom = "app";
			$this->common_model->userValidate($userID, $requestFrom);

			$orderWhere['where']['order_id'] = $tickectID;
			$orderData = $this->common_model->getData('single', 'uw_hourly_orders', $orderWhere);
			if(empty($orderData)):
				throw new Exception(lang('ORDET_ID_INVALID'));
			elseif(isset($orderData['status']) && $orderData['status'] == 'CL'):
				throw new Exception(lang('CANCELLED_ORDER'));
			elseif(!isset($orderData['is_winner']) || $orderData['is_winner'] != 'Y'):
				throw new Exception(lang('NOT_WINNER'));
			endif;

			$winningAmount = (float)($orderData['winning_amount'] ?? 0);
			if($winningAmount <= 0):
				throw new Exception(lang('NOT_WINNER'));
			endif;

			if((isset($orderData['winning_status']) && $orderData['winning_status'] == 'paid') || (isset($orderData['redeem_status']) && $orderData['redeem_status'] == 'paid')):
				throw new Exception(lang('ALREADY_REDEEM'));
			endif;

			$Fields = array('_id','users_id','availableArabianPoints','redeeming_amount_limit');
			$sellerDetails = $this->common_model->getSingleDataByParticularField($Fields,'uw_users','users_id',(int)$userID);
			if(empty($sellerDetails)):
				throw new Exception(lang('USER_NOT_FOUND'));
			endif;

			$redeemingAmountLimit = !empty($sellerDetails['redeeming_amount_limit']) ? (float)$sellerDetails['redeeming_amount_limit'] : 999;
			if($winningAmount > $redeemingAmountLimit && $userID != 100000000000110):
				throw new Exception(lang('BIG_WINNER_TEXT'));
			endif;

			$updateParams['winning_status']   = 'paid';
			$updateParams['status']           = 'Redeemed';
			$updateParams['redeem_by_mode']   = $redeemByMode;
			$updateParams['redeemed_at']      =  strtotime(date('Y-m-d H:i'));
			$updateParams['settler_users_id'] = (int)$userID;
			$updateParams['settler_users_oid']= new MongoDB\BSON\ObjectId($sellerDetails['_id']['$id']);
			$updateParams['update_ip']        = !empty($ipAddress) ? $ipAddress : currentIp();
			$this->common_model->editData('uw_hourly_orders', $updateParams, 'order_id', $tickectID);

			$orderOid = null;
			if(isset($orderData['_id']) && is_object($orderData['_id']) && isset($orderData['_id']->{'$id'})):
				$orderOid = new MongoDB\BSON\ObjectId($orderData['_id']->{'$id'});
			elseif(isset($orderData['_id']) && is_array($orderData['_id']) && isset($orderData['_id']['$id'])):
				$orderOid = new MongoDB\BSON\ObjectId($orderData['_id']['$id']);
			elseif(isset($orderData['_id']) && is_string($orderData['_id']) && strlen($orderData['_id']) == 24):
				$orderOid = new MongoDB\BSON\ObjectId($orderData['_id']);
			endif;

			$Redeemparam["load_balance_id"] = (int)$this->geneal_model->getNextSequence('uw_loadBalance');
			$Redeemparam["users_id"] = (int)$userID;
			$Redeemparam["user_oid"] = new MongoDB\BSON\ObjectId($sellerDetails['_id']['$id']);
			if($orderOid):
				$Redeemparam["order_oid"] = $orderOid;
			endif;
			if(!empty($orderData['products_oid']) && isset($orderData['products_oid']->{'$id'})):
				$Redeemparam["product_oid"] = new MongoDB\BSON\ObjectId($orderData['products_oid']->{'$id'});
			endif;
			$Redeemparam["order_id"] = $tickectID;
			$Redeemparam["product_id"] = isset($orderData['products_id']) ? (int)$orderData['products_id'] : 0;
			$Redeemparam["user_id_deb"] = (int)$userID;
			$Redeemparam["user_id_cred"] = (int)0;
			$Redeemparam["upoints"] = $winningAmount;
			$Redeemparam["record_type"] = 'Debit';
			$Redeemparam["narration"] = 'Hourly Game Prize Redeemed';
			$Redeemparam["remarks"] = "Redeemed Prize ".$winningAmount." AED in ".$redeemByMode." for ".$tickectID;
			$Redeemparam["availableArabianPoints"] = (float)$sellerDetails['availableArabianPoints'];
			$Redeemparam["end_balance"] = (float)$sellerDetails['availableArabianPoints'];
			$Redeemparam["creation_ip"] = currentIp();
			$Redeemparam["created_at"] = date('Y-m-d H:i');
			$Redeemparam["created_by"] = (int)$userID;
			$Redeemparam["status"] = "A";
			$this->geneal_model->addData('uw_loadBalance', $Redeemparam);

			$result = array('payment_date' => date('Y-m-d H:i'));
			echo outPut(1,lang('SUCCESS_CODE'),lang('COUPON_REDEEMED_SUCCESFULLY'),$result);die();
		} catch (Exception $e) {
			echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);die();
		}
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
			$report_type   = $this->input->get('report_type');
			$startDate     = $this->input->get('from');
			$endDate       = $this->input->get('to');
 
			if(empty($usersId)):
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
			else: 	

				if($winnerType == 'RaffleWinner'):
					$redeemedWinnerLIst = $this->geneal_model->getRaffleWinnerList();
				elseif($winnerType == 'uwinnWinner'):
					$redeemedWinnerLIst = $this->geneal_model->getWinnerList();
				elseif($winnerType == 'HourlyGameWinner'):
					$startDate = date('Y-m-d H:i:01', strtotime($startDate));
					$endDate = date('Y-m-d H:i:59', strtotime($endDate));
					
					if(!empty($startDate) && !empty($endDate)):
						$whereCon['where']['redeemed_at'] = array(
							'$gte' => strtotime($startDate),
							'$lte' => strtotime($endDate)
						);
					endif;
					
					$whereCon['where']['status']           = "Redeemed";
					$whereCon['where']['settler_users_id'] = (int)$usersId;
					$WinnerLIst = $this->common_model->getHourlyGameOrderData('multiple','uw_hourly_orders',$whereCon);
					$redeemedWinnerLIst['HourlyGameWinner'] = $WinnerLIst ;
				else:
					$redeemedWinnerLIst = $this->geneal_model->getWinnerList();
				endif;
			endif;
				$results = $redeemedWinnerLIst;
		 		echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),(object) $results);
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
	
	/* * *********************************************************************
	 * * Function name : checkRaffleEligible
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to checkRaffleEligible
	 * * Date 		   : 14 october 2025
	 * * **********************************************************************/
	public function checkRaffleEligible()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_GET));
		$result 			= 	array();
		try {
			if(requestAuthenticate(APIKEY,'POST')):
				$usersId = $this->input->post('users_id');
				$orderId = $this->input->post('order_id');
				if(empty($usersId)):
					throw new Exception(lang('USER_ID_EMPTY'), 1);
				elseif(empty($orderId)):
					throw new Exception(lang('USER_ID_EMPTY'), 1);
				else:
					$requestFrom 	  = "app";
					$validationResult = $this->common_model->userValidate($usersId,$requestFrom);

					$tableName	             = "uw_raffle_eligible_orders";
					$whereCon['where']       = array('status' => "A" , 'order_id' => $orderId);
			 	    $eligibleCampaignDetails = $this->common_model->getData('count',$tableName,$whereCon);
			 	    // echo "<pre>";print_r($eligibleCampaignDetails);die();

					if($eligibleCampaignDetails > 0 ):
						throw new Exception(lang('ALREADY_UPLOADED'), 1);
					else:
						$tableName	  = "uw_lotto_orders";
						$Fields 	  = array('order_id','is_printed','user_id','status','product_id','total_price','draw_date','draw_time','created_at');
						$orderDetails = $this->common_model->getSingleDataByParticularField($Fields,$tableName,'order_id',$orderId);
						// echo "<pre>";print_r($orderDetails);die();

						if(empty($orderDetails)):
							throw new Exception(lang('ORDET_ID_INVALID'), 1);
						elseif(empty($orderDetails['product_id'])):
							throw new Exception(lang('PRODUCT_EXPIRE'), 1);
						elseif($orderDetails['status'] == "CL"):
							throw new Exception(lang('CANCELLED_ORDER'), 1);
						else:

							$tableName	    = "uw_products";
							$Fields 	    = array('enable_raffle_eligibility','raffle_eligible_amount','raffle_draw_eligible_date','raffle_draw_announcement_date');
							$productDetails = $this->common_model->getSingleDataByParticularField($Fields,$tableName,'products_id',$orderDetails['product_id']);
							// echo "<pre>";print_r($orderDetails);die();

							$orderDateTime     = strtotime($orderDetails['created_at']);
							$raffleStartDate   = strtotime($productDetails['raffle_draw_eligible_date']);
							$raffleEndDrawDate = strtotime($productDetails['raffle_draw_announcement_date']);
							// echo "<pre>";print_r($raffleEndDrawDate);die();

							if(empty($productDetails)):
								throw new Exception(lang('INVALID_PRODUCT_ID'), 1);
						
							elseif( empty($productDetails['enable_raffle_eligibility']) || $productDetails['enable_raffle_eligibility'] == "N" || $orderDetails['total_price'] <  $productDetails['raffle_eligible_amount'] ):
								$raffleError   = str_replace('###ORDERID###', ucwords($orderId), lang('NOT_RAFFLE_ELIGIBLE'));
								throw new Exception($raffleError, 1);
							
							elseif($orderDateTime < $raffleStartDate):
								throw new Exception(lang('INVALID_PRODUCT_ID'), 1);
							elseif(   
								($orderDateTime >= $raffleStartDate && $orderDateTime <= $raffleEndDrawDate ) &&    
								($productDetails['enable_raffle_eligibility'] == "Y" && $orderDetails['total_price'] >=  $productDetails['raffle_eligible_amount']) 
								):

								$eligibleTickctsCount = floor($orderDetails['total_price']/$productDetails['raffle_eligible_amount']);
								$raffleSuccessMessage   = str_replace('###ORDERID###', ucwords($orderId), lang('RAFFLE_ELIGIBLE'));
								$raffleSuccessMessage   = str_replace('###RAFFLECOUNT###', $eligibleTickctsCount, $raffleSuccessMessage);
								if($eligibleTickctsCount == 1):
									$raffleSuccessMessage   = str_replace('entry', 'entry', $raffleSuccessMessage);
								else:
									$raffleSuccessMessage   = str_replace('entry', 'entries', $raffleSuccessMessage);
								endif;
								echo outPut(1,lang('SUCCESS_CODE'),$raffleSuccessMessage,$orderDetails);die();
							endif;

						endif;
					endif;
				endif;
			else:
				throw new Exception(lang('FORBIDDEN_MSG'),1);
			endif;
		} catch (Exception $e) {
			echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);	
		}
	}	

	/* * *********************************************************************
	 * * Function name : submitEligibleTicket
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to submitEligibleTicket
	 * * Date 		   : 14 october 2025
	 * * **********************************************************************/
	public function submitEligibleTicket()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_GET));
		$result 			= 	array();
		try {
			if(requestAuthenticate(APIKEY,'POST')):
				
				$usersId  = $this->input->post('users_id');
				$orderId  = $this->input->post('order_id');
				$countryCode  = $this->input->post('country_code');
				$mobileNumber = $this->input->post('mobile_number');
				$firstName    = $this->input->post('first_name');
				$lastName     = $this->input->post('last_name');
				$posNumber    = $this->input->post('pos_number');

				if(empty($usersId)):
					throw new Exception(lang('USER_ID_EMPTY'), 1);
				elseif(empty($orderId)):
					throw new Exception(lang('USER_ID_EMPTY'), 1);
				elseif(empty($countryCode)):	
					throw new Exception(lang('EMPTY_COUNTRYCODE'), 1);
				elseif(empty($mobileNumber)):	
					throw new Exception(lang('EMPTY_USERMOBILE'), 1);
				elseif(empty($posNumber)):	
					throw new Exception(lang('POS_NUMBER_REQUIRED'), 1);
				else:
					$requestFrom 	  = "app";
					$validationResult = $this->common_model->userValidate($usersId,$requestFrom);

					$tableName	             = "uw_raffle_eligible_orders";
					$whereCon['where']       = array('status' => "A" , 'order_id' => $orderId);
			 	    $eligibleCampaignDetails = $this->common_model->getData('count',$tableName,$whereCon);
			 	    // echo "<pre>";print_r($eligibleCampaignDetails);die();

					if($eligibleCampaignDetails > 0 ):
						throw new Exception(lang('ALREADY_UPLOADED'), 1);
					else:

						$tableName	  = "uw_lotto_orders";
					    $Fields 	  = array('order_id','is_printed','user_id','status','product_id','total_price','draw_date','draw_time','created_at');
				 	    $orderDetails = $this->common_model->getSingleDataByParticularField($Fields,$tableName,'order_id',$orderId);
				 	    // echo "<pre>";print_r($orderDetails);die();

				 	    if(empty($orderDetails)):
							throw new Exception(lang('ORDET_ID_INVALID'), 1);
						elseif(empty($orderDetails['product_id'])):
							throw new Exception(lang('PRODUCT_EXPIRE'), 1);
				 	    else:

				 	    	$tableName	    = "uw_products";
						    $Fields 	    = array('enable_raffle_eligibility','raffle_eligible_amount','raffle_draw_eligible_date','raffle_draw_announcement_date');
					 	    $productDetails = $this->common_model->getSingleDataByParticularField($Fields,$tableName,'products_id',$orderDetails['product_id']);
				 	   		// echo "<pre>";print_r($orderDetails);die();

				 	    	$orderDateTime     = strtotime($orderDetails['created_at']);
				 	    	$raffleStartDate   = strtotime($productDetails['raffle_draw_eligible_date']);
				 	    	$raffleEndDrawDate = strtotime($productDetails['raffle_draw_announcement_date']);
				 	   		// echo "<pre>";print_r($raffleEndDrawDate);die();

					 	    if(empty($productDetails)):
								throw new Exception(lang('INVALID_PRODUCT_ID'), 1);
					 	   
					 	    elseif( empty($productDetails['enable_raffle_eligibility']) || $productDetails['enable_raffle_eligibility'] == "N" || $orderDetails['total_price'] <  $productDetails['raffle_eligible_amount'] ):
					 	    	$raffleError   = str_replace('###ORDERID###', ucwords($orderId), lang('NOT_RAFFLE_ELIGIBLE'));
								throw new Exception($raffleError, 1);
							
							elseif($orderDateTime < $raffleStartDate):
								throw new Exception(lang('INVALID_PRODUCT_ID'), 1);
							elseif(   
								 ($orderDateTime >= $raffleStartDate && $orderDateTime <= $raffleEndDrawDate ) &&    
								 ($productDetails['enable_raffle_eligibility'] == "Y" && $orderDetails['total_price'] >=  $productDetails['raffle_eligible_amount']) 
							    ):

								$eligibleTickctsCount = floor($orderDetails['total_price']/$productDetails['raffle_eligible_amount']);
								for ($i=0; $i <$eligibleTickctsCount ; $i++) { 
									$param[$i]['order_id']      = $orderId;
									$param[$i]['country_code']  = $countryCode;
									$param[$i]['mobile_number'] = (int)$mobileNumber;
									$param[$i]['first_name']    = $firstName;
									$param[$i]['last_name']     = $lastName;
									$param[$i]['pos_number']    = $posNumber;
									$param[$i]['status']        = "A";
									$param[$i]['created_at']    = date('Y-m-d H:i');
									$param[$i]['created_by']    = (int)$usersId;
								}

								$tableName = 'uw_raffle_eligible_orders';
								$result    = $this->common_model->addManyData($tableName,$param);
								
								// add raffle user details..
								$param1['country_code']  = $countryCode;	
								$param1['mobile_number'] = (int)$mobileNumber;
								$param1['first_name']    = $firstName;
								$param1['last_name']     = $lastName;
								$param1['created_at']    = date('Y-m-d H:i');
								$param1['created_by']    = (int)$usersId;
								$this->common_model->addData('uw_raffle_users',$param1);
								// end add raffle user details..
								
								$drawDate  = date("d/m/Y",strtotime($productDetails['raffle_draw_announcement_date']));
								$message   = "Your Order ID: ".$orderId." has been successfully added to the ".$drawDate." campaign.";
								$this->sms_model->raffleWinnersSms($countryCode, $mobileNumber,$message);
		 						echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),'');die();
					 	    endif;
				 	    endif;
					endif;
				endif;
			else:
				throw new Exception(lang('FORBIDDEN_MSG'),1);
			endif;
		} catch (Exception $e) {
			echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);	
		}
	}

	/* * *********************************************************************
	 * * Function name : autoFetchUsersData
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to autoFetchUsersData
	 * * Date 		   : 17 october 2025
	 * * **********************************************************************/
	public function autoFetchUsersData()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 			= 	array();
		try {
			if(requestAuthenticate(APIKEY,'POST')):
				$usersId      = $this->input->post('users_id');
				$countryCode  = $this->input->post('country_code');
				$mobileNumber = $this->input->post('mobile_number');

				if(empty($countryCode)):
					throw new Exception(lang('EMPTY_COUNTRYCODE'), 1);
				elseif(empty($mobileNumber)):
					throw new Exception(lang('EMPTY_USERMOBILE'), 1);
				elseif(empty($usersId)):
					throw new Exception(lang('USER_ID_EMPTY'), 1);
				else:
					$requestFrom 	  = "app";
					$validationResult = $this->common_model->userValidate($usersId,$requestFrom);
					
					$whereCon['where']['country_code']  = $countryCode;
					$whereCon['where']['mobile_number'] = (int)$mobileNumber;
					$raffleUserDetails					= $this->common_model->getData('single','uw_raffle_users',$whereCon);
					if(empty($raffleUserDetails)):
						throw new Exception(lang('USER_NOT_FOUND'), 1);
					else:		
						$result = $raffleUserDetails;
						echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$result);
					endif;
				endif;
				 
			else:
				throw new Exception(lang('FORBIDDEN_MSG'),1);
			endif;
		} catch (Exception $e) {
			echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);	
		}
	}
	
	/* * *********************************************************************
	 * * Function name : drawResult
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to drawResult
	 * * Date 		   : 07 November 2025
	 * * **********************************************************************/
	public function drawResultRange()
	{
		$apiHeaderData = getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 	   = array();
		try {
			if(requestAuthenticate(APIKEY,'POST')):
				$usersId  = $this->input->post('users_id');
				$DrawDate = $this->input->post('draw_date');

				if(empty($usersId)):
					throw new Exception(lang('USER_ID_EMPTY'), 1);
				else:
					$tblName    = 'uw_users';
					$fieldList  = array('users_id','status','users_type');
					$whereCon['where']['users_id'] = (int)$usersId;
					$userData   = $this->common_model->getParticularFieldByMultipleCondition($fieldList ,$tblName,$whereCon);
					
					if(empty($userData)   || $userData['status'] != "A" || $userData['users_type'] != "Retailer" ):
						throw new Exception(lang('INVALID_USER'), 1);
					elseif(!empty($userData) && $userData['status'] == "A" && $userData['users_type'] == "Retailer" ):
						$tblName    = 'uw_draw_result_settings';
						$whereCon1['where']['status'] = "A";
						$fieldList  = array('range_value');
						$resultData = $this->common_model->getParticularFieldByMultipleCondition($fieldList ,$tblName,$whereCon1);
						if(empty($resultData)):
							throw new Exception(lang('USER_NOT_FOUND'), 1);
						else:
							$result = $resultData;
							echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$result);
						endif;
					endif;
					 
				endif;
			else:
				throw new Exception(lang('FORBIDDEN_MSG'),1);
			endif;
		} catch (Exception $e) {
			echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);	
		}
	}

	/* * *********************************************************************
	 * * Function name : drawResult
	 * * Developed By  : Dilip Halder
	 * * Purpose  	   : This function used to drawResult
	 * * Date 		   : 07 November 2025
	 * * **********************************************************************/
	public function drawResult()
	{
		$apiHeaderData = getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 	   = array();
		try {
			if(requestAuthenticate(APIKEY,'POST')):
				$usersId  = $this->input->post('users_id');
				$DrawDate = $this->input->post('draw_date');

				if(empty($usersId)):
					throw new Exception(lang('USER_ID_EMPTY'), 1);
				elseif(empty($DrawDate)):
					throw new Exception(lang('EMPTY_DRAW_DATE'), 1);
				else:
					$tblName    = 'uw_users';
					$fieldList  = array('users_id','status','users_type');
					$whereCon['where']['users_id'] = (int)$usersId;
					$userData   = $this->common_model->getParticularFieldByMultipleCondition($fieldList ,$tblName,$whereCon);
					
					if(empty($userData)   || $userData['status'] != "A" || $userData['users_type'] != "Retailer" ):
						throw new Exception(lang('INVALID_USER'), 1);
					elseif(!empty($userData) && $userData['status'] == "A" && $userData['users_type'] == "Retailer" ):
						$tblName    = 'uw_draw_result';
						$drawDateCon['where']['result_date'] = strtotime($DrawDate);
						$drawDateCon['where']['status'] = "A";
						$resultData = $this->common_model->getData('multiple',$tblName,$drawDateCon);						
						if(empty($resultData)):
							throw new Exception(lang('DATA_NOT_FOUND'), 1);
						else:
							$result = $resultData;
							echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$result);
						endif;
					endif;
					 
				endif;

				 
			else:
				throw new Exception(lang('FORBIDDEN_MSG'),1);
			endif;
		} catch (Exception $e) {
			echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);	
		}
	}

}
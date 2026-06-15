<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Common extends CI_Controller {
	
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
	} 

	/* * *********************************************************************
	 * * Function name  : getCountryCode
	 * * Developed By 	: Dilip Halder
	 * * Purpose  		: This function used for get Country Code
	 * * Date 			: 07 February 2024
	 * * **********************************************************************/
	public function getHomePageData()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'GET')):
			
			$result['banners']    		=   array();
			$bannerWhereCon['where'] 	=	array( 'draw_date' => array('$gte' => date('Y-m-d')) );
			$result['banners'] 			=	$this->geneal_model->getProductWithPrizeDetails($bannerWhereCon);

			$result['latestDrawResult'] =   array();
			$latestWhereCon['where'] 	=	array();
			$latestOrder 				=	['_id' => 'ASC'];
			$latestLimit				=	5;
			$result['latestDrawResult'] =	$this->geneal_model->getData2('multiple','uw_uwin_winner', $latestWhereCon,$latestOrder,0,5);


			$result['winner_gallery']	= $this->geneal_model->getWinnerGallery();
			
			echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}	

	/* * *********************************************************************
	 * * Function name  : getConversion
	 * * Developed By 	: Afsar Ali
	 * * Purpose  		: This function used for get currency conversion data
	 * * Date 			: 05 JULY 2024
	 * * **********************************************************************/
	public function getConversion()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'GET')):
			$result    				=   array();
			$type					= 	'multiple';
			if($this->input->get('type') <> ''):
				$type = $this->input->get('type');
			endif;

			$whereCon['where']['status']	= 'A';
			if($this->input->get('country') <> ''):
				$whereCon['where']['country']	= $this->input->get('country');
			endif;

			// if($this->input->get('phone_code') <> ''):
			// 	$whereCon['where']['phone_code']	= $this->input->get('phone_code');
			// endif;

			$result					=	$this->geneal_model->getData2($type,'uw_conversions', $whereCon);
			echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}


	/* * *********************************************************************
	 * * Function name  : pageContent
	 * * Developed By 	: Dilip Halder
	 * * Purpose  		: This function used for get page Content
	 * * Date 			: 13 August 2024
	 * * Updated By 	: Dilip Halder
	 * * Updated Date 	: 04 October 2024
	 * * **********************************************************************/
	 public function pageContent()
	 {
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 			= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):
			$users_id     = $this->input->post('users_id');
			$added_for    = $this->input->post('added_for');
			$itemsPerPage = $this->input->post('itemsPerPage');
			$pageno 	  = $this->input->post('page');

			$upload_type  = $this->input->post('upload_type');
			if(empty($users_id)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
            elseif(empty($added_for)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_PAGE_NAME'),$result);die();
            else:

            	if($added_for):
            		$Added_For_Array = explode(',', $added_for);
            		$SortedArray 	 = array_values(array_filter(array_map('trim', $Added_For_Array)));
            		$Added_for_Data  = array_values(array_diff($SortedArray, array("Website", "App","POS")));

            		$added_for_prefix = array_map(function($item) {
					    return 'added_for_'.str_replace(' ', '_', strtolower($item));
					}, $Added_for_Data);

        			$added_for_prefix_data = [];
            		if(isset($SortedArray) &&  in_array('App', $SortedArray)):
        			 	array_push($added_for_prefix_data, 'App');
            		endif;
            		if(isset($SortedArray) &&  in_array('Website', $SortedArray)):
    			 		array_push($added_for_prefix_data, 'Website');
            		endif;

            		if(isset($SortedArray) &&  in_array('POS', $SortedArray)):
        			 	array_push($added_for_prefix_data, 'POS');
            		endif;

            	endif;
        		//Using these variables to getting datas.  1) SortedArray  2) added_for_prefix_data 3) Added_for_Data
            	$tblName    					= 'uw_contents';
            	$whereCon['where']['status']    = 'A';

            	if(!empty($Added_for_Data)):
	        		$whereCon['where']['added_for'] = array('$all' => $Added_for_Data);
            	endif;
	        	if(!empty($added_for_prefix)):
		        	foreach ($added_for_prefix as $key => $items):
		        		$whereCon['where'][$items] = array('$all' => $added_for_prefix_data);
		        	endforeach;
	        	endif;
	        	if(!empty($upload_type)):
	        		$whereCon['where']['upload_type'] = $upload_type;
	        	endif;
	        	$created_date = $this->input->post('created_date');
	        	if(!empty($created_date)):
	        		$whereCon['where']['created_date'] = $created_date;
				endif;

				$date = date('Y-m-d H:i');
				$whereCon['where']['live_date_time'] = array('$lte' =>  strtotime($date) );
	        	$totalcount	 = $this->common_model->getData('count',$tblName,$whereCon);
				// echo "<pre>";print_r($totalcount);die();	
				
	        	// Sample long array with data
				$longArray = $totalcount;
				// Current page number (received from URL query parameter, e.g., ?page=2)
				$page = isset($pageno) ? (int)$pageno : 1;

				// Calculate total number of pages
				$totalPages = ceil($longArray / $itemsPerPage);
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
				$shortField = array("position" => 1);
				$startIndex    = ($page - 1) * $itemsPerPage;
	        	$PageData 	   = $this->common_model->getData('multiple',$tblName,$whereCon,$shortField,$itemsPerPage,$startIndex);
				// Banner Time update query start..
				// if(!empty($PageData)):
				// 	foreach($PageData as $key => $items):
				// 		$date = date('Y-m-d H:i');
				// 		if( !empty($items['live_date_time_later']) && strtotime($date) > $items['live_date_time_later']):
				// 			$param['position'] 		 = $items['new_position'];
				// 			$param['live_date_time'] = $items['live_date_time_later'];
				// 			$param['new_position']   	   = "";
				// 			$param['live_date_time_later'] = "";
				// 			$param['updated_ip'] 	 = $apiHeaderData['ip'];
				// 			$param['updated_by'] 	 = $apiHeaderData['user_id'];
				// 			$this->common_model->editData($tblName, $param ,'_id', new MongoDB\BSON\ObjectId($items['_id']->{'$id'}) );
				// 		endif;
				// 	endforeach;
				// endif;
				// Banner Time update query end..
				$PageData 	   = $this->common_model->getData('multiple',$tblName,$whereCon,$shortField,$itemsPerPage,$startIndex);
			    if(!empty($PageData)):
			    	$totalpage 				 = count($totalpage);
			    	$result['current_page']  = $current_page;
					$result['total_page'] 	 = $totalpage;
		    		$result['PageData']      = $PageData;
		        	echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);die();
			    else:
					echo outPut(0,lang('FORBIDDEN_CODE'),lang('DATA_NOT_FOUND'),$result);die();
			    endif;
			endif;
			echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	 }
	// public function pageContent()
	// {
	// 	$apiHeaderData 		=	getApiHeaderData();
	// 	$this->generatelogs->putLog('APP',logOutPut($_POST));
	// 	$result 			= 	array();	
	// 	if(requestAuthenticate(APIKEY,'POST')):
	// 		$users_id     = $this->input->post('users_id');
	// 		$added_for    = $this->input->post('added_for');
	// 		$itemsPerPage = $this->input->post('itemsPerPage');
	// 		$pageno 	  = $this->input->post('page');

	// 		$upload_type  = $this->input->post('upload_type');
	// 		if(empty($users_id)):
    //             echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
    //         elseif(empty($added_for)):
    //             echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_PAGE_NAME'),$result);die();
    //         else:


    //         	if($added_for):
    //         		$Added_For_Array = explode(',', $added_for);
    //         		$SortedArray 	 = array_values(array_filter(array_map('trim', $Added_For_Array)));
    //         		$Added_for_Data  = array_values(array_diff($SortedArray, array("Website", "App","POS")));

    //         		$added_for_prefix = array_map(function($item) {
	// 				    return 'added_for_'.str_replace(' ', '_', strtolower($item));
	// 				}, $Added_for_Data);

    //     			$added_for_prefix_data = [];
    //         		if(isset($SortedArray) &&  in_array('App', $SortedArray)):
    //     			 	array_push($added_for_prefix_data, 'App');
    //         		endif;
    //         		if(isset($SortedArray) &&  in_array('Website', $SortedArray)):
    // 			 		array_push($added_for_prefix_data, 'Website');
    //         		endif;

    //         		if(isset($SortedArray) &&  in_array('POS', $SortedArray)):
    //     			 	array_push($added_for_prefix_data, 'POS');
    //         		endif;

    //         	endif;
    //     		//Using these variables to getting datas.  1) SortedArray  2) added_for_prefix_data 3) Added_for_Data
    //         	$tblName    					= 'uw_contents';
    //         	$whereCon['where']['status']    = 'A';

    //         	if(!empty($Added_for_Data)):
	//         		$whereCon['where']['added_for'] = array('$all' => $Added_for_Data);
    //         	endif;

	//         	if(!empty($added_for_prefix)):
	// 	        	foreach ($added_for_prefix as $key => $items):
	// 	        		$whereCon['where'][$items] = array('$all' => $added_for_prefix_data);
	// 	        	endforeach;
	//         	endif;

	//         	if(!empty($upload_type)):
	//         		$whereCon['where']['upload_type'] = $upload_type;
	//         	endif;

	//         	$created_date = $this->input->post('created_date');
	//         	if(!empty($created_date)):
	//         		$whereCon['where']['created_date'] = $created_date;
	// 			endif;

	//         	// echo "<pretotalcount>";print_r($whereCon);die();
	//         	$totalcount	 = $this->common_model->getData('count',$tblName,$whereCon);
	//         	// Sample long array with data
	// 			$longArray = $totalcount;
	// 			// Current page number (received from URL query parameter, e.g., ?page=2)
	// 			$page = isset($pageno) ? (int)$pageno : 1;

	// 			// Calculate total number of pages
	// 			$totalPages = ceil($longArray / $itemsPerPage);
	// 			$totalpage= array();
	// 			// Pagination links
	// 			for ($i = 1; $i <= $totalPages; $i++) {
	// 			    if ($i == $page) {
	// 			         $current_page = $i;
	// 			         $totalpage[] = $i;
	// 			    } else {
	// 			         $totalpage[] = $i;
	// 			    }
	// 			}

	// 			$startIndex    = ($page - 1) * $itemsPerPage;
	//         	$PageData 	   = $this->common_model->getData('multiple',$tblName,$whereCon,$shortField,$itemsPerPage,$startIndex);
	// 		    if(!empty($PageData)):
	// 		    	$totalpage 				    = count($totalpage);
	// 		    	$result['current_page']     = $current_page;
	// 				$result['total_page'] 	    =   $totalpage;
	// 	    		$result['PageData'] = $PageData;
	// 	        	echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);die();
	// 		    else:
	// 				echo outPut(0,lang('FORBIDDEN_CODE'),lang('DATA_NOT_FOUND'),$result);die();
	// 		    endif;
	// 		endif;
	// 		echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
	// 	else:
	// 		echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
	// 	endif;
	// }

	/* * *********************************************************************
	 * * Function name  : contactUs
	 * * Developed By 	: Dilip Halder
	 * * Purpose  		: This function used for submit contactUs form 
	 * * Date 			: 13 August 2024
	 * * **********************************************************************/
	public function contactUs()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 			= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):
			
			$usersID   = $this->input->post('users_id');
			$orderID   = $this->input->post('order_id');
			$firstName = $this->input->post('first_name');
			$lastName  = $this->input->post('last_name');
			$email     = $this->input->post('email');
			$mobile    = $this->input->post('mobile');
			$subject   = $this->input->post('subject');
			$message   = $this->input->post('message');

			if(empty($usersID)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
            elseif(empty($orderID)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('ORDER_ID_EMPTY'),$result);die();
            elseif(empty($firstName)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMPRT_FIRST_NAME'),$result);die();
            elseif(empty($lastName)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_LAST_NAME'),$result);die();
            elseif(empty($email)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMAIL_EMPTY'),$result);die();
            elseif(empty($mobile)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('PHONE_EMPTY'),$result);die();
            elseif(empty($subject)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('SUBJECT_EMPTY'),$result);die();
            elseif(empty($message)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('MESSAGE_EMPTY'),$result);die();
            else:
				
			    $param['id']         = (int)$this->geneal_model->getNextSequence('da_contact');
			    $param['first_name'] = htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8');
			    $param['last_name']  = htmlspecialchars($lastName, ENT_QUOTES, 'UTF-8');
			    $param['email']      = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
			    $param['mobile']     = (int)$mobile;
			    $param['subject']    = htmlspecialchars($subject, ENT_QUOTES, 'UTF-8');
			    $param['message']    = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
			    $param['order_id']   = htmlspecialchars($orderID, ENT_QUOTES, 'UTF-8');
			    $param['created_at'] = date('Y-m-d H:i');
				$data = $this->geneal_model->addData('uw_contacts', $param);

			    if(!empty($data)):
	        		$result = $data;
	            	echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);die();
			    else:
					echo outPut(0,lang('FORBIDDEN_CODE'),lang('DATA_NOT_FOUND'),$result);die();
			    endif;
			endif;
			echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name  : faqs
	 * * Developed By 	: Dilip Halder
	 * * Purpose  		: This function used to show faqs
	 * * Date 			: 22 August 2024
	 * * **********************************************************************/
	public function faqs()
	{  
	    $data 					= array();
		$data['page']			= 'Frequently Asked Questions';

		// Fraud Awareness
		$tblName 			     = 'uw_cms';
		$whereCon['where']	     = array('page_name'=>'Faqs');		
		$shortField 		     = array('privacy_policy_id'=> -1);
		$data['faqs']  			 = $this->common_model->getData('single',$tblName,$whereCon,$shortField);
		$this->layouts->set_title('FAQs');
		$this->layouts->front_view('faqs',array(),$data,'apiview');
	} // END OF FUNCTION

	/***********************************************************************
	** Function name 	: privacyPolicy
	** Developed By 	: Dilip Halder
	** Purpose 			: This function used for Privacy Policy
	** Date 			: 22 August 2024
	************************************************************************/ 	
	public function privacyPolicy()
	{  
	    $data 					= array();
		$data['page']			= 'Privacy Policy';

		//banners.
		$tblName 			  = 'uw_homepage_slider';
		$whereCon['where']	  = array('show_on'=> 'Web' , 'status' => 'A');		
		$shortField 		  = array('slider_id'=> -1);
		$data['slider']  	  = $this->common_model->getData('multiple',$tblName,$whereCon,$shortField);

		// Privacy Policy
		$tblName 			     = 'uw_cms';
		$whereCon['where']	     = array('page_name'=>'Privacy Policy');		
		$shortField 		     = array('privacy_policy_id'=> -1);
		$data['privacy_policy']  = $this->common_model->getData('single',$tblName,$whereCon,$shortField);
		$this->layouts->set_title('Privacy Policy');
		$this->layouts->front_view('privacy_policy',array(),$data,'apiview');
  	} // END OF FUNCTION

  	/***********************************************************************
	** Function name 	: termsConditions
	** Developed By 	: Dilip Halder
	** Purpose 			: This function used for result
	** Date 			: 22 August 2024
	************************************************************************/ 	
	public function termsConditions()
	{  
	    $data 					= array();
		$data['page']			= 'terms&conditions';
   
		// Term&Conditions
		$tblName 			     = 'uw_cms';
		$whereCon['where']	     = array('page_name'=>'Terms and conditions');		
		$shortField 		     = array('slider_id'=> -1);
		$data['term_conditions'] = $this->common_model->getData('single',$tblName,$whereCon,$shortField);

		$this->layouts->set_title('Terms & Conditions');
		$this->layouts->front_view('term_conditions',array(),$data,'apiview');
	} // END OF FUNCTION

	/***********************************************************************
	** Function name 	: userAgreement
	** Developed By 	: Dilip Halder
	** Purpose 			: This function userAgreement
	** Date 			: 15 November 2024
	************************************************************************/ 	
	public function userAgreement()
	{  
	    $data 					= array();
		$data['page']			= 'Users Agreement';
   
		// Term&Conditions
		$tblName 			     = 'uw_cms';
		$whereCon['where']	     = array('page_name'=>'Users Agreement');		
		$shortField 		     = array('slider_id'=> -1);
		$data['term_conditions'] = $this->common_model->getData('single',$tblName,$whereCon,$shortField);

		$this->layouts->set_title('Terms & Conditions');
		$this->layouts->front_view('term_conditions',array(),$data,'apiview');
	} // END OF FUNCTION

	/***********************************************************************
	** Function name 	: howToPlay
	** Developed By 	: Dilip Halder
	** Purpose 			: This function used for result
	** Date 			: 06 March 2024
	************************************************************************/ 	
	public function howToPlay()
	{  
		$data 				= array();
		$data['page']		= 'How To Play';
		//Play Data ..
		$tblName 			= 'uw_cms';
		$whereCon['where']	= array('page_name'=>'How it works');	
		$shortField 		= array('title_name'=>'ASC');
		$result	    		= $this->common_model->getData('single',$tblName,$whereCon,$shortField);
		$data['content'] 	= $result;
		$this->layouts->set_title('How To Play');
		$this->layouts->front_view('content',array(),$data,'apiview');


	} // END OF FUNCTION

	/***********************************************************************
	** Function name 	: aboutUS
	** Developed By 	: Dilip Halder
	** Purpose 			: This function used for about us
	** Date 			: 22 August 2024
	************************************************************************/ 	
	public function aboutUS()
	{  
		$data 				= array();
		$data['page']		= 'About Us';
		//About us Data ..
		$tblName 			= 'uw_cms';
		$whereCon['where']	= array('page_name'=>'About Us');	
		$shortField 		= array('title_name'=>'ASC');
		$result				= $this->common_model->getData('single',$tblName,$whereCon,$shortField);
		$data['items'] 		= $result['sections'];
		// echo "<pre>"; print_r($data); die();
		$this->layouts->set_title('About US');
		$this->layouts->front_view('about_us',array(),$data,'apiview');
	} // END OF FUNCTION

	/***********************************************************************
	** Function name 	: refundPolicy
	** Developed By 	: Dilip Halder
	** Purpose 			: This function used for refundPolicy
	** Date 			: 22 August 2024
	************************************************************************/
	public function refundPolicy()
	{  
	    $data 					= array();
		$data['page']			= 'Refund Policy';
		// Term&Conditions
		$tblName 			     = 'uw_cms';
		$whereCon['where']	     = array('page_name'=>'Refund Policy');		
		$shortField 		     = array('slider_id'=> -1);
		$data['term_conditions'] = $this->common_model->getData('single',$tblName,$whereCon,$shortField);

		$this->layouts->set_title('Refund Policy');
		$this->layouts->front_view('term_conditions',array(),$data,'apiview');
	} // END OF FUNCTION

	/***********************************************************************
	** Function name 	: cancellationPolicy
	** Developed By 	: Dilip Halder
	** Purpose 			: This function used for cancellationPolicy
	** Date 			: 22 August 2024
	************************************************************************/
	public function cancellationPolicy()
	{  
	    $data 					= array();
		$data['page']			= 'Cancellation Policy';
		// Term&Conditions
		$tblName 			     = 'uw_cms';
		$whereCon['where']	     = array('page_name'=>'Cancellation Policy');		
		$shortField 		     = array('slider_id'=> -1);
		$data['term_conditions'] = $this->common_model->getData('single',$tblName,$whereCon,$shortField);

		$this->layouts->set_title('Cancellation Policy');
		$this->layouts->front_view('term_conditions',array(),$data,'apiview');
	} // END OF FUNCTION

	/***********************************************************************
	** Function name 	: gameRules
	** Developed By 	: Dilip Halder
	** Purpose 			: This function used for gameRules.
	** Date 			: 23 August 2024
	************************************************************************/
	public function gameRules()
	{  
		$data 				= array();
		$data['page']		= 'Game Rules';
		//Play Data ..
		$tblName 			= 'uw_cms';
		$whereCon['where']	= array('page_name'=>'Game Rules');	
		$shortField 		= array('title_name'=>'ASC');
		$result	    		= $this->common_model->getData('single',$tblName,$whereCon,$shortField);
		$data['items']    	= $result['sections'];
		$data['where_to_play'] = $result['where_to_play'];
		// echo "<pre>"; print_r($data); die();
		$this->layouts->set_title('Game Rules');
		$this->layouts->front_view('how_to_play',array(),$data,'apiview');
	} // END OF FUNCTION

	/***********************************************************************
	** Function name 	: contestRules
	** Developed By 	: Dilip Halder
	** Purpose 			: This function used for contestRules.
	** Date 			: 16 December 2024
	************************************************************************/
	public function contestRules()
	{  
	 	$data 					= array();
		$data['page']			= 'Contest Rules';
		// Term&Conditions
		$tblName 			     = 'uw_cms';
		$whereCon['where']	     = array('page_name'=>'Contest Rules');		
		$shortField 		     = array('slider_id'=> -1);
		$data['term_conditions'] = $this->common_model->getData('single',$tblName,$whereCon,$shortField);

		$this->layouts->set_title('Contest Rules');
		$this->layouts->front_view('term_conditions',array(),$data,'apiview');

	} // END OF FUNCTION

    /* * *********************************************************************
	* * Function name   : deleteRequest
	* * Developed By 	: Dilip Halder
	* * Purpose  		: This function used for submit deleteRequest 
	* * Date 			: 03 September 2024
	* * **********************************************************************/
	public function deleteRequest()
	{
		$apiHeaderData  = getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 		= array();

		if(requestAuthenticate(APIKEY,'POST')):
			
			$user_id    = $this->input->post('users_id');

			if(empty($user_id)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
            else:
				
				// Users validation function.
            	$requestFrom = 'app';
				$USERDATA    = $this->common_model->userValidate($user_id,$requestFrom);

				$param['id']         	= (int)$this->geneal_model->getNextSequence('request_id');
			    $param['users_id'] 	 	= htmlspecialchars((int)$this->input->post('users_id'), ENT_QUOTES, 'UTF-8');
                $param['request_for']   = htmlspecialchars($this->input->post('request_for'), ENT_QUOTES, 'UTF-8');;
                $param['created_at']    = date('Y-m-d H:i');
                $param['created_by']    = (int)$id;
		     	$param['creation_ip']   = $this->input->ip_address();;
                $param['status']        = 'A';
				$data = $this->geneal_model->addData('uw_users_requesrt', $param);
			     

			    if(!empty($data)):
	        		$result = $data;
	            	echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);die();
			    else:
					echo outPut(0,lang('FORBIDDEN_CODE'),lang('DATA_NOT_FOUND'),$result);die();
			    endif;
			endif;
			echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}


	/***********************************************************************
	** Function name 	: deleteProfileImage
	** Developed By 	: Dilip Halder
	** Purpose 			: This function used for deleteProfileImage
	** Date 			: 12 September 2024
	************************************************************************/ 
	public function deleteProfileImage()
	{

		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):
			
			$imageName = $this->input->post('imageName');
			$usersID   = $this->input->post('users_id');

			if(empty($usersID)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
            elseif(empty($imageName)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_PROFILE'),$result);die();
            else:
			    $FieldList   		  = array('users_id','status','profile'); 
				$tableName            = 'uw_users';
			    $whereCon['where']    = array('users_id' => (int)$usersID );
				$userDetails 		  = $this->common_model->getParticularFieldByMultipleCondition($FieldList,$tableName,$whereCon);

			 	if($userDetails['profile'] == $imageName ):
					$this->load->library("upload_crop_img");
				 	$this->upload_crop_img->_delete_image(trim($imageName)); 
					$param['profile']  =	''; 
					$result = $this->common_model->editData($tableName,$param,'users_id',(int)$usersID);
                	echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
                else:
                	echo outPut(0,lang('FORBIDDEN_CODE'),lang('DATA_NOT_FOUND'),$result);die();
				endif;
			endif;

		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name  : pickupPoints
	 * * Developed By 	: Dilip Halder
	 * * Purpose  		: This function used for get pickupPoints
	 * * Date 			: 18 Sep 2024
	 * * **********************************************************************/
	public function pickupPoints()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'GET')):
			$Fields    			= array('collection_point_id','collection_point_name');
			$action    			= 'multiple';
			$TblName   			= 'uw_emirate_collection_point';
			$whereCon['where']  = array('status' => 'A');
			$shortField 		= array('collection_point_id' =>-1 );
			$collection_point  = $this->common_model->getDataByNewQuery($Fields,$action,$TblName,$whereCon,$shortField='');
			
			if($collection_point):
				$result['collection_point'] = $collection_point;
				echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$result);
			else:
				echo outPut(0,lang('SUCCESS_CODE'),lang('DATA_NOT_FOUND'),$result);
			endif;

		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}	

	/* * *********************************************************************
	 * * Function name  : userRequest
	 * * Developed By 	: Dilip Halder
	 * * Purpose  		: This function used to delete account
	 * * Date 			: 09 December 2024
	 * * **********************************************************************/
	public function userRequest()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):
			 
			$usersID    = $this->input->post('users_id');
			$RequestFor = $this->input->post('request_for');
			$Reason     = $this->input->post('reason');

			if(empty($usersID)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
            elseif(empty($RequestFor)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_REQUESTFOR'),$result);die();
            elseif(empty($Reason)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_REASON'),$result);die();
            else:
			    
				$tableName          = 'uw_users';
			    $whereCon['where']  = array('users_id' => (int)$usersID );
				$userDetails 		= $this->common_model->getParticularFieldByMultipleCondition($FieldList,$tableName,$whereCon);
				// echo "<pre>";print_r($userDetails);die();
			 	if($userDetails['status'] == 'A'):
			     	$param['status']     	= "D";
			     	$param['delete_reason'] = $reason;
					$param['update_date'] 	= date('Y-m-d H:i:s');
					$result 				= $this->common_model->editData($tableName,$param,'users_id',(int)$usersID);
                	echo outPut(1,lang('SUCCESS_CODE'),lang('ACCOUNT_DELETED_SUCCESSFULLY'),$result);die();
                elseif($userDetails['status'] == 'D' && $userDetails['is_verify'] == "Y"):
                	echo outPut(1,lang('SUCCESS_CODE'),lang('ACCOUNT_ALREADY_DELETED'),true);die();
                else:
                	echo outPut(0,lang('FORBIDDEN_CODE'),lang('DATA_NOT_FOUND'),$result);die();
				endif;
			endif;
			
			echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}	



}
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
	public function getCountryCode()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'GET')):
			
			$result['countryCodeData']    	=   array();
			$countryCodeData 				=	countryCodeList();
			foreach($countryCodeData as $countryCodeKey=>$countryCodeValue):
				array_push($result['countryCodeData'],array('country_code'=>$countryCodeKey,'country_name'=>$countryCodeValue));
			endforeach;
			echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name 	: getCountryList
	 * * Developed By 	: Dilip Halder
	 * * Purpose  		: This function used for get Country List
	 * * Date 			: 07 February 2024
	 * * **********************************************************************/
	public function getCountryList()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'GET')):
			
			$tblName2 					=	'uw_country';
			$where2['where'] 			=	array( 'status' => 'A');
			$order2 					=	['country_name' => 'ASC'];
			$data2						=	$this->geneal_model->getData2('multiple',$tblName2,$where2,$order2);
			$result['countryList']    	=   $data2?$data2: [];
			echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : contact
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for contact
	 * * Date : 11 JULY 2022
	 * * **********************************************************************/
	public function contact()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):
			
			if($this->input->get('users_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
			elseif($this->input->post('subject') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('SUBJECT_EMPTY'),$result);
			elseif($this->input->post('message') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('MESSAGE_EMPTY'),$result);
			else:

				$where 			=	[ 'users_id' => (int)$this->input->get('users_id') ];
				$tblName 		=	'uw_users';
				$userDetails 	=	$this->geneal_model->getOnlyOneData($tblName, $where);

				if(!empty($userDetails)):
					if($userDetails['status'] == 'A'):

						$insert_data = array(
											'id'			=> (int)$this->geneal_model->getNextSequence('uw_contact'),
											"name"     		=> $userDetails['users_name'],	
											"mobile"        => $userDetails['users_mobile'],
											"email"         => $userDetails['users_email'],
											"subject"       => $this->input->post('subject'),
											"message"     	=> $this->input->post('message'),
											'created_at'	=> date('Y-m-d h:i'),	
											'created_ip'	=> $this->input->ip_address(),
											);
						$this->geneal_model->addData('uw_contacts', $insert_data);

						echo outPut(1,lang('SUCCESS_CODE'),lang('contact_success'),$result);
					else:
						echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$result);
					endif;
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_INCORRECT'),$result);
				endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}
	/* * *********************************************************************
	 * * Function name : contestrules
	 * * Developed By : Afsar Ali
	 * * Purpose  : This function used for contact
	 * * Date : 09 DEC 2022
	 * * **********************************************************************/
	public function contestrules()
	{	
		$data = [];
		$tblName 					=	'uw_cms';
		$where['where'] 			=	array( 'page_name' => 'Contest Rules','status'=>'A');
		$order 						=	['title' => 'ASC'];
		$data						=	$this->geneal_model->getData2('single',$tblName,$where,$order);
		$this->load->view('web_api/generaldata',$data);
	}//END OF FUNCTION
	/* * *********************************************************************
	 * * Function name : termsconditions
	 * * Developed By : Afsar Ali
	 * * Purpose  : This function used for Terms and conditions
	 * * Date : 09 DEC 2022
	 * * **********************************************************************/
	public function termsconditions()
	{	
		$data = [];
		$tblName 					=	'uw_cms';
		$where['where'] 			=	array( 'page_name' => 'Terms and conditions');
		$order 						=	['title' => 'ASC','status'=>'A'];
		$data						=	$this->geneal_model->getData2('single',$tblName,$where,$order);
		$this->load->view('web_api/generaldata',$data);
	}//END OF FUNCTION
	/* * *********************************************************************
	 * * Function name : privacypolicy
	 * * Developed By : Afsar Ali
	 * * Purpose  : This function used for Privacy Policy
	 * * Date : 09 DEC 2022
	 * * **********************************************************************/
	public function privacypolicy()
	{	
		$data = [];
		$tblName 					=	'uw_cms';
		$where['where'] 			=	array( 'page_name' => 'Privacy Policy');
		$order 						=	['title' => 'ASC','status'=>'A'];
		$data						=	$this->geneal_model->getData2('single',$tblName,$where,$order);
		$this->load->view('web_api/generaldata',$data);
	}//END OF FUNCTION
	/* * *********************************************************************
	 * * Function name : usersagreement
	 * * Developed By : Afsar Ali
	 * * Purpose  : This function used for Privacy Policy
	 * * Date : 09 DEC 2022
	 * * **********************************************************************/
	public function usersagreement()
	{	
		$data = [];
		$tblName 					=	'uw_cms';
		$where['where'] 			=	array( 'page_name' => 'Users Agreement','status'=>'A');
		$order 						=	['title' => 'ASC'];
		$data						=	$this->geneal_model->getData2('single',$tblName,$where,$order);
		
		$this->load->view('web_api/generaldata',$data);
	}//END OF FUNCTION
	/* * *********************************************************************
	 * * Function name : download_invoice
	 * * Developed By : Dilip
	 * * Purpose  : This function used for download invoice
	 * * Date : 28 JAN 2023
	 * * **********************************************************************/
	public function download_invoice($id=""){
		$oid = base64_decode($id);
		
		$this->load->library('Mpdf');
		
		$tblName 				=	'uw_orders';
		$shortField 			= 	array('_id'=> -1 );
		$whereCon['where']		= 	array('order_id'=>$oid);
	
		$orderData  =	$this->geneal_model->getordersList('single', $tblName, $whereCon,$shortField);
		
		$name = explode('@',$data['orderData']['user_email']);
		$data['orderData'] = $orderData;
	
		// $this->load->view('web_api/order_pdf_template', $data);
		$this->load->view('web_api/order_pdf_template_table', $data);
		$this->DownlodeOrderPDF($oid.'.pdf');
		return;
	}
	/* * *********************************************************************
	 * * Function name : DownlodeOrderPDF
	 * * Developed By : Dilip
	 * * Purpose  : This function used for download file
	 * * Date : 28 JAN 2023
	 * * **********************************************************************/
	public function DownlodeOrderPDF($file='')
	{  
		if (file_exists($this->config->item("root_path")."/assets/orderpdf/".$file)) {
			header('Content-Description: File Transfer');
			// We'll be outputting a PDF
			header('Content-type: application/pdf');
			// It will be called downloaded.pdf
			header('Content-Disposition: attachment; filename="'.$file.'"');
			// The PDF source is in original.pdf
			readfile($this->config->item("root_path")."/assets/orderpdf/".$file);
			
			@unlink($this->config->item("root_path")."/assets/orderpdf/".$file);
			exit;
		}
	}

	/* * *********************************************************************
	 * * Function name : getGeneralInfo
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for get General Info
	 * * Date : 06 June 2023
	 * * **********************************************************************/
	public function getGeneralInfo()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'GET')):
			
			$tbl 					=	'uw_general_data';
			$where 					=	['status' => 'A'];
			$general_details =	$this->geneal_model->getData($tbl, $where,[]);

			$result['general_details'] = $general_details;

			echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	
	/* * *********************************************************************
	 * * Function name	: getSubWinnerist
	 * * Developed By 	: Afsar Ali
	 * * Purpose  		: This function used for get General Info
	 * * Date 			: 08 JULY 2023
	 * * **********************************************************************/
	public function getSubWinnerist()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):
			$user_id 		= 	'';
			$order_id		=	'';
			$code			=	'';
			$offset			=	0;
			$limit 			=	10;
			// $insert_data = $this->input->post();

			if($this->input->post('users_id')):
				$user_id 	=	$this->input->post('users_id');
			endif;

			if($this->input->post('search')):
				$text = $this->input->post('search');
				if (substr($text, 0, 3) == "DLZ" || substr($text, 0, 3) == "DZI"):
					$order_id 	=	$this->input->post('search');
				else:
					$code = $this->input->post('search');
				endif;
			endif;

			if($this->input->post('offset')):
				$offset = $this->input->post('offset');
			endif;
			if($this->input->post('limit')):
				$limit = $this->input->post('limit');
			endif;
			
			$winner_list = $this->getWinnerList($code,$order_id,$offset,$limit,$user_id);

			if(count($winner_list) > 0){
				echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$winner_list);
			}else{
				echo outPut(1,lang('SUCCESS_CODE'),'No winners found',[]);
			}
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

/* * *********************************************************************
* * Function name	: getWinnerList
* * Developed By 	: Afsar Ali
* * Purpose  		: This function used for get Winner List
* * Date 			: 08 JULY 2023
* * **********************************************************************/
public function getWinnerList($code = '', $order_id = '', $offset=0, $limit=10, $usr_id=''){
	$ord_id = $order_id;
	$product_id = [];
	//$usr_id = '';
	$user_id = '';
	
	if(substr($ord_id, 0, 3) == "DLZ"):
		$whereGetOrderID['where']		=	[ 'order_id' => "$ord_id" ];
		$currentOrderData 				=	$this->geneal_model->getData2('multiple', 'uw_orders_details', $whereGetOrderID);
		foreach ($currentOrderData as $key => $value) {
			array_push($product_id, $value['product_id']);
		}
	elseif(substr($ord_id, 0, 3) == "DZI"):
		$whereGetOrderID['where']		=	[ 'order_id' => "$ord_id" ];
		$currentOrderData 				=	$this->geneal_model->getData2('single', 'uw_ticket_orders', $whereGetOrderID);
		array_push($product_id, $currentOrderData['product_id']);
	endif;
	
	$pid = implode(',', $product_id); 

	if($order_id == '' && $code == ''):
		$user_id = $usr_id;
	endif;
	
	$curl = curl_init();

	curl_setopt_array($curl, array(
	CURLOPT_URL => 'https://winner.dealzarabia.com/coupons_dealzarabia_api/v1/get_winners_list.php',
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING => '',
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 0,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => 'POST',
	CURLOPT_POSTFIELDS =>'{
		"code":"'.$code	.'", 
		"product_id":"'.$pid.'",	
		"offset":'.$offset.',
		"limit":'.$limit.',
		"user_id": "'.$user_id.'",
		"order_id" : "'.$ord_id.'"
	}',
	CURLOPT_HTTPHEADER => array(
		'Content-Type: application/json',
		'Authorization: Basic REVBTFpfQVBJOmRoZHl3czc3NjVHU0dTMw=='
	),
	));
	$response = curl_exec($curl);

	curl_close($curl);

	$param['data'] = '{
		"code":"'.$code	.'", 
		"product_id":"'.$pid.'",	
		"offset":'.$offset.',
		"limit":'.$limit.',
		"user_id": "'.$user_id.'",
		"order_id" : "'.$ord_id.'"
	}';

	$this->geneal_model->addData('uw_test2', $param);

	$result = json_decode($response, true);
	if($result['status'] == 1){
		return $result['info'];
	}else{
		return [];
	}
}

	/* * *********************************************************************
	 * * Function name	: getDailySubWinnerist
	 * * Developed By 	: Dilip Halder
	 * * Purpose  		: This function used for get General Info
	 * * Date 			: 11 August 2023
	 * * **********************************************************************/
	public function getDailySubWinnerist()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):
			$code			=	'';
			$offset			=	0;
			$limit 			=	10;

			if($this->input->post('code')):
				$code = $this->input->post('code');
			endif;
			if($this->input->post('offset')):
				$offset = $this->input->post('offset');
			endif;
			if($this->input->post('limit')):
				$limit = $this->input->post('limit');
			endif;
			
			$curl = curl_init();
			curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://winner.dealzarabia.com/coupons_dealzarabia_api/v1/get_daily_winners_list.php',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS =>'{
			    "code":"'.$code	.'", 
			    "offset":'.$offset.',
			    "limit":'.$limit.'

				}',
			  	CURLOPT_HTTPHEADER => array(
			    'Content-Type: application/json',
			    'Authorization: Basic YWRtaW5fdXNlcjpBZG1pbkA3NDEwNzQxMA==',
			    'Cookie: AppAdmin=admin1|ZP6jf|ZP6fg'
			  ),
			));

			$response = curl_exec($curl);

			curl_close($curl);

			$winner_list = json_decode($response, true);



			if(count($winner_list) > 0){
				echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$winner_list);
			}else{
				echo outPut(1,lang('SUCCESS_CODE'),'No winners found',[]);
			}
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	 /***********************************************************************
    ** Function name    : generateOrderId
    ** Developed By     : Dilip Halder
    ** Purpose          : This function used for generate order ID
    ** Date             : 24 APRIL 2023
    ** Updated By       :
    ** Updated Date     : 
    ************************************************************************/  
    public function generateOrderId()
    {   
        $apiHeaderData      =   getApiHeaderData();
        $this->generatelogs->putLog('APP',logOutPut($_POST));
        $result                             =   array();    
        if(requestAuthenticate(APIKEY,'POST')):

            if($this->input->post('device_type') == 'ios' && $this->input->post('app_version') == '1.51' || $this->input->post('device_type') == 'ios' && $this->input->post('app_version') == '1.53'):
                echo outPut(0,lang('SUCCESS_CODE'), 'Online purchases are stoped in latest ios application due to technical issue. visit our website ( https://dealzarabia.com/ ) for Online purchases.' ,$result);
            endif;
            
            if($this->input->get('users_id') == ''): 
                echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
            elseif($this->input->post('user_type') == ''): 
                echo outPut(0,lang('SUCCESS_CODE'),lang('USER_TYPE_EMPTY'),$result);
            // elseif($this->input->post('user_email') == ''): 
            //  echo outPut(0,lang('SUCCESS_CODE'),lang('USER_EMAIL_EMPTY'),$result);
            elseif($this->input->post('user_phone') == ''): 
                echo outPut(0,lang('SUCCESS_CODE'),lang('USER_PHONE_EMPTY'),$result);
            elseif($this->input->post('product_is_donate') == ''): 
                echo outPut(0,lang('SUCCESS_CODE'),lang('PRODUCT_IN_DONATE_EMPTY'),$result);
            elseif($this->input->post('product_is_donate') == 'N' && $this->input->post('shipping_method') == ''): 
                echo outPut(0,lang('SUCCESS_CODE'),lang('SHIPPINT_METHOD_EMPTY'),$result);


            //elseif($this->input->post('product_is_donate') == 'N' && $this->input->post('shipping_method') == 'ship' && $this->input->post('address') == ''): 
            //  echo outPut(0,lang('SUCCESS_CODE'),lang('SHIPPING_ADDRESS_EMPTY'),$result);

            elseif($this->input->post('product_is_donate') == 'N' && $this->input->post('shipping_method') == 'pickup' && $this->input->post('emirate_id') == ''): 
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMIRATE_ID_EMPTY'),$result);
            elseif($this->input->post('product_is_donate') == 'N' && $this->input->post('shipping_method') == 'pickup' && $this->input->post('emirate_name') == ''): 
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMIRATE_NAME_EMPTY'),$result);

            elseif($this->input->post('product_is_donate') == 'N' && $this->input->post('shipping_method') == 'pickup' && $this->input->post('area_id') == ''): 
                echo outPut(0,lang('SUCCESS_CODE'),lang('AREA_ID_EMPTY'),$result);
            elseif($this->input->post('product_is_donate') == 'N' && $this->input->post('shipping_method') == 'pickup' && $this->input->post('area_name') == ''):
                echo outPut(0,lang('SUCCESS_CODE'),lang('AREA_NAME_EMPTY'),$result);

            elseif($this->input->post('product_is_donate') == 'N' && $this->input->post('shipping_method') == 'pickup' && $this->input->post('collection_point_id') == ''): 
                echo outPut(0,lang('SUCCESS_CODE'),lang('COLLECTION_POINT_ID_EMPTY'),$result);
            elseif($this->input->post('product_is_donate') == 'N' && $this->input->post('shipping_method') == 'pickup' && $this->input->post('collection_point_name') == ''): 
                echo outPut(0,lang('SUCCESS_CODE'),lang('COLLECTION_POINT_NAME_EMPTY'),$result);


            elseif($this->input->post('shipping_charge') == ''): 
                echo outPut(0,lang('SUCCESS_CODE'),lang('SHIPPING_CHARGE_EMPTY'),$result);
            elseif($this->input->post('inclusice_of_vat') == ''): 
                echo outPut(0,lang('SUCCESS_CODE'),lang('INCLUSICE_OF_VAT_EMPTY'),$result);
            elseif($this->input->post('subtotal') == ''): 
                echo outPut(0,lang('SUCCESS_CODE'),lang('SUBTOTAL_EMPTY'),$result);
            elseif($this->input->post('vat_amount') == ''): 
                echo outPut(0,lang('SUCCESS_CODE'),lang('VAT_AMOUNT_EMPTY'),$result);
            else:
                
                $where          =   [ 'users_id' => (int)$this->input->get('users_id') ];
                $tblName        =   'uw_users';
                $userDetails    =   $this->geneal_model->getOnlyOneData($tblName, $where);

                if(!empty($userDetails)):
                    if($userDetails['status'] == 'A'):

                        $wcon['where']      =   [ 'user_id' => (int)$this->input->get('users_id') ];
                        $cartItems          =   $this->geneal_model->getData2('multiple', 'uw_cartItems', $wcon);
                        $productCount       =   $this->geneal_model->getData2('count', 'uw_cartItems', $wcon);

                        foreach($cartItems as $CA):
                            // Available prodcut and soldout product details..
                            $tblName = 'uw_tickets_sequence';
                            $whereCon2['where']                 =   array('product_id' => (int)$CA['id'] , 'status' => 'A');    
                            $shortField                         =   array('tickets_seq_id'=>'DESC');
                            $CurrentTicketSequence              =   $this->common_model->getData('single',$tblName,$whereCon2,$shortField,'0','0');

                            $tblName = 'uw_quickcoupons_totallist';
                            $whereCon3['where']                 =   array('product_id' => (int)$CA['id'] , 'tickets_seq_id' => (int)$CurrentTicketSequence['tickets_seq_id'] ); 
                            $shortField3                        =   array('tickets_seq_id'=>'DESC');
                            $SoldoutTicketList                  =   $this->common_model->getData('single',$tblName,$whereCon3,$shortField3,'0','0');

                            $available_ticket = $CurrentTicketSequence['tickets_sequence_end'] -$CurrentTicketSequence['tickets_sequence_start'];
                            
                            if($CurrentTicketSequence['tickets_seq_id'] == $SoldoutTicketList['tickets_seq_id'] ):
                                $coupon_sold_number =   $SoldoutTicketList['coupon_sold_number'];
                            endif;

                            if( $this->input->post('product_is_donate') == "Y"):
                                $check_availblity = $CA['qty'] * 2;
                            elseif( $this->input->post('product_is_donate') == "N"):
                                $check_availblity = $CA['qty'];
                            endif;
                            
                            $left_ticket = $available_ticket - ($coupon_sold_number + $check_availblity);  

                            // echo 'available_ticket == '.$available_ticket."<br>";
                            // echo 'SoldoutTicketList == '.$coupon_sold_number."<br>";
                            // echo  'left_ticket == '. $left_ticket."<br><br>";
                            // die();

                            if($left_ticket < 0):
                                echo outPut(0,lang('SUCCESS_CODE'),lang('TICKET_NOT_AVAILABLE'). " for " .$CA['name'],$result);die();
                                die();
                            endif;
                        
                        endforeach;

                        if($cartItems):
                           
                            $inclusice_of_vat               =   (int)(trim($this->input->post('inclusice_of_vat'))*100);
                            $ORparam["order_id"]            =   $this->geneal_model->getNextOrderId();
                            if($ORparam["order_id"]):  

                                /* Order Place Table */
                                $ORparam["sequence_id"]             =   (int)$this->geneal_model->getNextSequence('uw_orders');
                                //$ORparam["order_id"]              =   $this->geneal_model->getNextOrderId();
                                $ORparam["user_id"]                 =   (int)$this->input->get('users_id');
                                $ORparam["user_type"]               =   $this->input->post('user_type');
                                if($this->input->post('user_email')):
                                    $ORparam["user_email"]              =   $this->input->post('user_email');
                                else:
                                    $ORparam["user_email"]              =   "dealzarabiasales1@gmail.com";
                                endif;
                                $ORparam["user_phone"]              	=   (int)$this->input->post('user_phone');

                                $ORparam["product_is_donate"]       =   $this->input->post('product_is_donate');
                                if($ORparam["product_is_donate"] == 'N'):
                                    $ORparam["shipping_method"]     =   $this->input->post('shipping_method');
                                    $ORparam["emirate_id"]              =   $this->input->post('emirate_id');
                                    $ORparam["emirate_name"]            =   $this->input->post('emirate_name');
                                    $ORparam["area_id"]                 =   $this->input->post('area_id');
                                    $ORparam["area_name"]               =   $this->input->post('area_name');
                                    $ORparam["collection_point_id"]     =   $this->input->post('collection_point_id');
                                    $ORparam["collection_point_name"]   =   $this->input->post('collection_point_name');

                                    $ORparam["shipping_address"]    =   '';

                                else:
                                    $ORparam["shipping_method"]     =   '';

                                    $ORparam["emirate_id"]              =   '';
                                    $ORparam["emirate_name"]            =   '';
                                    $ORparam["area_id"]                 =   '';
                                    $ORparam["area_name"]               =   '';
                                    $ORparam["collection_point_id"]     =   '';
                                    $ORparam["collection_point_name"]   =   '';

                                    $ORparam["shipping_address"]    =   '';
                                endif;
                                $ORparam["product_count"]           =   (int)$productCount;
                                $ORparam["shipping_charge"]         =   (float)$this->input->post('shipping_charge');
                                $ORparam["inclusice_of_vat"]        =   (float)$this->input->post('inclusice_of_vat');
                                $ORparam["subtotal"]                =   (float)$this->input->post('subtotal');
                                $ORparam["vat_amount"]              =   (float)$this->input->post('vat_amount');
                                $ORparam["total_price"]             =   (float)$ORparam["inclusice_of_vat"];
                                // $ORparam["payment_mode"]            =   'Telr';
                                $ORparam["payment_mode"]            =   $this->input->post('payment_mode');
                                $ORparam["availableArabianPoints"] 	=	(float)$userDetails["availableArabianPoints"];
				        		$ORparam["end_balance"] 			=	(float)$userDetails["availableArabianPoints"];
                                $ORparam["payment_from"]            =   'App';
                                $ORparam["device_type"]             =   $this->input->post('device_type');
                                $ORparam["app_version"]             =   $this->input->post('app_version');
                                $ORparam["order_status"]            =   "Initialize";
                                $ORparam["creation_ip"]             =   $this->input->ip_address();
                                $ORparam["created_at"]              =   date('Y-m-d H:i');

                                $orderInsertID                      =   $this->geneal_model->addData('uw_orders', $ORparam);
 
                                if($orderInsertID && $cartItems):
                                    foreach($cartItems as $CA): 
                                        //Manage Inventory
                                        if($this->input->post('product_is_donate') == 'N'):
                                            $where['where']     =   array(
                                                                        'products_id'           =>  (int)$CA['id'],
                                                                        'collection_point_id'   =>  (int)$ORparam["collection_point_id"]
                                                                    );
                                            $INVcheck   =   $this->geneal_model->getData2('single','uw_inventory',$where);
                                            if($INVcheck <> ''):
                                                $orqty = $INVcheck['order_request_qty'] + (int)$CA['qty'];
                                                $INVUpdate['order_request_qty']     =   $orqty;
                                                $this->geneal_model->editDataByMultipleCondition('uw_inventory',$INVUpdate,$where['where']);
                                            else:
                                                $INVparam['products_id']                =   (int)$CA['id'];
                                                $INVparam['qty']                        =   (int)0;
                                                $INVparam['available_qty']              =   (int)0;
                                                $INVparam['order_request_qty']          =   (int)$CA['qty'];
                                                $INVparam['collection_point_id']        =   (int)$ORparam["collection_point_id"];

                                                $INVparam['inventory_id']               =   (int)$this->common_model->getNextSequence('uw_inventory');
                                                
                                                $INVparam['creation_ip']                =   currentIp();
                                                $INVparam['creation_date']              =   (int)$this->timezone->utc_time();//currentDateTime();
                                                $INVparam['status']                     =   'A';
                                                $this->geneal_model->addData('uw_inventory', $INVparam);
                                            endif;
                                        endif;
                                        //END
                                        $ORDparam["order_details_id"]   =   (int)$this->geneal_model->getNextSequence('uw_orders_details');
                                        $ORDparam["order_sequence_id"]  =   (int)$ORparam["sequence_id"];
                                        $ORDparam["order_id"]           =   $ORparam["order_id"];
                                        $ORDparam["user_id"]            =   (int)$CA['user_id'];
                                        $ORDparam["product_id"]         =   (int)$CA['id'];
                                        $ORDparam["product_name"]       =   $CA['name'];
                                        $ORDparam["quantity"]           =   (int)$CA['qty'];
                                        if($CA['color']):
                                            $ORDparam["color"]          =   $CA['color'];
                                        endif;
                                        if($CA['size']):
                                            $ORDparam["size"]           =   $CA['size'];
                                        endif;
                                        $ORDparam["price"]              =   (float)$CA['price'];
                                        $ORDparam["tax"]                =   (float)0;
                                        $ORDparam["subtotal"]           =   (float)$CA['subtotal'];
                                        if($this->input->post('product_is_donate') == 'Y'):
                                            $ORDparam["is_donated"]         =   'Y';
                                        else:
                                            $ORDparam["is_donated"]         =   $CA['is_donated'];
                                        endif;
                                        //$ORDparam["is_donated"]       =   $CA['is_donated'];
                                        $ORDparam["other"]              =   array(
                                                                                    'image'         =>  $CA['other']->image,
                                                                                    'description'   =>  $CA['other']->description,
                                                                                    'aed'           =>  $CA['other']->aed
                                                                                );
                                        $ORDparam["current_ip"]         =   $CA['current_ip'];
                                        $ORDparam["rowid"]              =   $CA['rowid'];
                                        $ORDparam["curprodrowid"]       =   $CA['curprodrowid'];

                                        $this->geneal_model->addData('uw_orders_details', $ORDparam);
                                    endforeach;
                                endif;

                                //Get current order of user.
                                $wcon1['where']                 =   [ 'order_id' => $ORparam["order_id"] ];
                                $result['orderData']            =   $this->geneal_model->getData2('single', 'uw_orders', $wcon1);
                                
                                //Get current order details of user.
                                $wcon2['where']                 =   [ 'order_id' => $ORparam["order_id"] ];
                                $result['orderDetails']         =   $this->geneal_model->getData2('multiple', 'uw_orders_details', $wcon2);
                                


                                echo outPut(1,lang('SUCCESS_CODE'),lang('ORDER_ID_GENERATED'),$result);
                            else:
                                echo outPut(1,lang('SUCCESS_CODE'),lang('ORDER_ID_GENERATION_FAILED'),$result);
                            endif;
                        else:
                            echo outPut(0,lang('SUCCESS_CODE'),lang('CART_EMPTY'),$result);
                        endif;
                    else:
                        echo outPut(0,lang('SUCCESS_CODE'),lang('ACCOUNT_BLOCKED'),$result);
                    endif;
                else:
                    echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_INCORRECT'),$result);
                endif;
            endif;
        else:
            echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
        endif;
    }
	
     /***********************************************************************
    ** Function name    : telrOrderSuccess
    ** Developed By     : Dilip Halder
    ** Purpose          : This function used for generate order ID
    ** Date             : 31 JULY 2023
    ** Updated By       :
    ** Updated Date     : 
    ************************************************************************/  
    public function orderSuccess()
    {
        $apiHeaderData      =   getApiHeaderData();
        $this->generatelogs->putLog('APP',logOutPut($_POST));
        $result                             =   array();    
        if(requestAuthenticate(APIKEY,'GET')):
            
            if($this->input->get('users_id') == ''): 
                echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
            elseif($this->input->get('order_id') == ''): 
                echo outPut(0,lang('SUCCESS_CODE'),lang('ORDER_ID_EMPTY'),$result);
            else:

                $where          =   [ 'users_id' => (int)$this->input->get('users_id') ];
                $tblName        =   'uw_users';
                $userDetails    =   $this->geneal_model->getOnlyOneData($tblName, $where);

                $where2         =   [ 'user_id' => (int)$this->input->get('users_id') , 'order_id' => $this->input->get('order_id') ];
                $tblName        =   'uw_orders';
                $ORparam   =   $this->geneal_model->getOnlyOneData($tblName, $where2);

                 $orderData = array();
                if(!empty($ORparam)  && !empty($userDetails)):
                    
                    $data['user_id']                =   $ORparam['user_id'];
                    $data['user_email']             =   $ORparam['user_email'];
                    $data['inclusice_of_vat']       =   $ORparam['inclusice_of_vat'];
                    $data['stripe_token']           =   $ORparam['stripe_token'];
                    $data['user_type']              =   $ORparam['user_type'];

                    $data['order_id']               =   $ORparam['order_id'];
                    $data['stripeToken']            =   $ORparam['stripeToken'];
                    $data['customerId']             =   $ORparam['customerId'];
                    $data['captureAmount']          =   $ORparam['captureAmount'];
                    $data['stripeChargeId']         =   $ORparam['stripeChargeId'];

                    //Get current order of user.
                    $wcon['where']                  =   [ 'order_id' => $ORparam["order_id"] ];
                    $data['orderData']              =   $this->geneal_model->getData2('single', 'uw_orders', $wcon);
                 
                    array_push($orderData,$data['orderData'] );
                    //Get current order details of user.
                    $wcon2['where']                 =   [ 'order_id' => $ORparam["order_id"] ];
                    $OrderorderDetails              =   $this->geneal_model->getData2('multiple', 'uw_orders_details', $wcon2);

                    //update order status
                    if($data['orderData']["product_is_donate"] == 'Y'){                                                                             
                        $updateParams                   =   array('transaction_id'=> $this->input->get('transaction_id') , 'order_status' => 'Success', 'collection_status' => 'Donated');  
                    }else{
                        $expairyData                    =   date('Y-m-d H:i', strtotime($data['orderData']['created_at']. ' 14 Days'));
                        $collectionCode                 =   base64_encode(rand(1000,9999)); 
                        $updateParams                   =   array('transaction_id'=> $this->input->get('transaction_id') , 'order_status' => 'Success', 'collection_code' => $collectionCode, 'collection_status' => 'Pending to collect', 'expairy_data' => $expairyData);  
                    }
                    // $this->geneal_model->addData('uw_test',$updateParams);
                    //$updateStatus                     =   [ 'order_status' => 'Success' ];
                    $updateorderstatus              =   $this->geneal_model->editData('uw_orders', $updateParams, 'order_id', $ORparam["order_id"]);

                    $currentOrderDetails            =   array();                                                                            //New code 21-09-2022
                    $couponDetails                  =   array();  
                    $CashbackDetails                =   array(); 
                                                                                              //New code 21-09-2022
                    //Generate coupon 
                    //foreach($data['orderDetails'] as $CA):  
                    $cashbackcount = 0;
					$ref1count =0;                                                                              //Old code 21-09-2022
                    foreach($OrderorderDetails as $CA):                                                                                     //New code 21-09-2022

                        $CPwcon['where']                    =   [ 'product_id' => $CA["product_id"] ];                                      //New code 21-09-2022
                        $CPData                             =   $this->geneal_model->getData2('single', 'uw_prize', $CPwcon);               //New code 21-09-2022
                        $CA['actual_product_name']          =   $CPData['title'];                                                           //New code 21-09-2022
                        array_push($currentOrderDetails,$CA);                                                                               //New code 21-09-2022
                        
                        $this->geneal_model->updateStock($CA['product_id'],$CA['quantity']);
                        // if($data['orderData']["product_is_donate"] == 'Y'):
                        //  $this->geneal_model->updateInventoryStock($data['orderData']['collection_point_id'],$CA['product_id'],$CA['quantity']);
                        // endif;

                        $productIdPrice[$CA['product_id']]      =   ($CA['quantity'] * $CA['price']);

                        // //Get current Ticket order sequence from admin panel.
                        $tblName = 'uw_tickets_sequence';
                        $whereCon2['where']                 =   array('product_id' => (int)$CA['product_id'] , 'status' => 'A');    
                        $shortField                         =   array('tickets_seq_id'=>'DESC');
                        $CurrentTicketSequence              =   $this->common_model->getData('single',$tblName,$whereCon2,$shortField,'0','0');

                        $tblName = 'uw_quickcoupons_totallist';
                        $whereCon3['where']                 =   array('product_id' => (int)$CA['product_id'] , 'tickets_seq_id' => (int)$CurrentTicketSequence['tickets_seq_id']  );    
                        $shortField3                        =   array('coupon_id'=>'DESC');
                        $SoldoutTicketList                  =   $this->common_model->getData('single',$tblName,$whereCon3,$shortField3,'0','0');

                        $available_ticket = $CurrentTicketSequence['tickets_sequence_end'] -$CurrentTicketSequence['tickets_sequence_start'];
                        
                        if($CurrentTicketSequence['tickets_seq_id'] == $SoldoutTicketList['tickets_seq_id'] ):
                            $coupon_sold_number =   $SoldoutTicketList['coupon_sold_number'];
                        endif;

                        if($this->input->post('product_is_donate') == 'Y'):
                            $check_availblity = $this->input->post('product_qty') * 2;
                        endif;

                        $left_ticket = $available_ticket - ($coupon_sold_number + $check_availblity); 

                        if($CA['is_donated'] == 'N' ):
                            $soldOutQty = $CA['quantity'] ;
                        elseif($CA['is_donated'] == "Y"):
                            $soldOutQty = $CA['quantity']*2 ;
                        endif;

                        // Created 1st ticket sequence record.
                        if(empty($SoldoutTicketList['coupon_sold_number'])):

                            //Storing new ticket sequence in uw_quickcoupons_totallist
                            $quickcoupons["id"]                 =   (int)$this->geneal_model->getNextSequence('uw_quickcoupons_totallist');
                            $quickcoupons["product_id"]         =   (int)$CA['product_id'];
                            $quickcoupons["tickets_seq_id"]     =   (int)$CurrentTicketSequence['tickets_seq_id'];
                            $quickcoupons["coupon_sold_number"] =   '';//$soldOutQty;
                            $quickcoupons["creation_ip"]        =   $this->input->ip_address();
                            $quickcoupons["created_at"]         =   date('Y-m-d H:i');

                            //Saving quick coupons number  
                            $orderInsertID                      =   $this->geneal_model->addData('uw_quickcoupons_totallist', $quickcoupons);

                        else:
                            // checking existing ticket sequence record.
                            if($SoldoutTicketList['tickets_seq_id'] != $CurrentTicketSequence['tickets_seq_id'] ):
                            //Storing new ticket sequence in uw_quickcoupons_totallist
                                $quickcoupons["id"]                 =   (int)$this->geneal_model->getNextSequence('uw_quickcoupons_totallist');
                                $quickcoupons["product_id"]         =   (int)$CA['product_id'];
                                $quickcoupons["tickets_seq_id"] =       (int)$CurrentTicketSequence['tickets_seq_id'];
                            $quickcoupons["coupon_sold_number"] =   ''; //$soldOutQty;
                            $quickcoupons["creation_ip"]        =   $this->input->ip_address();
                            $quickcoupons["created_at"]         =   date('Y-m-d H:i');

                            //Saving quick coupons number  
                            $orderInsertID                      =   $this->geneal_model->addData('uw_quickcoupons_totallist', $quickcoupons);

                        endif;

                    endif;

                    //Admin announced ticket available number.
                     $available_ticket =  $CurrentTicketSequence['tickets_sequence_end'] - $CurrentTicketSequence['tickets_sequence_start'];
                    
                    if($CurrentTicketSequence['tickets_seq_id'] == $SoldoutTicketList['tickets_seq_id'] ):
                        $coupon_sold_number =   $SoldoutTicketList['coupon_sold_number'];
                    endif;
                    
                    //defining varibale to store coupon list
                    $couponList = array();

                    if ($available_ticket > $coupon_sold_number ):

                        // Getting product details
                        $wcon2['where']                 =   array('products_id' => (int)$CA['product_id'] );
                        $ProductData                    =   $this->geneal_model->getData2('single', 'uw_products', $wcon2);

                        if($CA['is_donated'] == 'N'):

                                //Start Create Coupons for simple product
                            for($i=1; $i <= $CA['quantity']*$ProductData['sponsored_coupon']; $i++){

                                if($CurrentTicketSequence['tickets_sequence_start']):

                                        // generating ticket order..
                                    if(!empty($SoldoutTicketList['coupon_sold_number'])):
                                       $TicketSequence = $CurrentTicketSequence['tickets_sequence_start']+ $SoldoutTicketList['coupon_sold_number'] +$i;
                                       $totalsoldqty = $SoldoutTicketList['coupon_sold_number'] +$i;
                                   else:
                                        $TicketSequence = $CurrentTicketSequence['tickets_sequence_start']+ $i;
                                        $totalsoldqty = $i;
                                    endif;
                                    $coupon_code = $CurrentTicketSequence['tickets_prefix'].$TicketSequence;

                                    array_push($couponList,$coupon_code);
                                        //Storing new ticket sequence in uw_quickcoupons_totallist start
                                        // $quickcoupons["product_id"]      =   (int)$ORparam['product_id'];
                                    $quickcoupons1["coupon_sold_number"] =  (int)$totalsoldqty;
                                    $quickcoupons1["creation_ip"]       =   $this->input->ip_address();
                                    $quickcoupons1["updated_at"]        =   date('Y-m-d H:i');

                                            //Saving quick coupons number  
                                            // echo $totalsoldqty.'<br>';
                                    $this->geneal_model->editData('uw_quickcoupons_totallist',$quickcoupons1,'tickets_seq_id',(int)$CurrentTicketSequence['tickets_seq_id']);
                                            //End

                                endif;

                                $couponData['coupon_id']        =   (int)$this->geneal_model->getNextSequence('uw_coupons');
                                $couponData['users_id']         =   (int)$ORparam["user_id"];
                                $couponData['users_email']      =   $ORparam["user_email"];
                                $couponData['order_id']         =   $ORparam["order_id"];
                                $couponData['product_id']       =   $CA['product_id'];
                                $couponData['product_title']    =   $CA['product_name'];
                                $couponData['is_donated']       =   'N';
                                $couponData['coupon_status']    =   'Live';
                                $couponData['coupon_code']      =   $coupon_code;
                                $couponData["device_type"]      =   $this->input->get('device_type');
                                $couponData["app_version"]      =   $this->input->get('app_version');
                                $couponData['draw_date']        =   $ProductData['draw_date'];
                                $couponData['draw_time']        =   $ProductData['draw_time'];
                                $couponData['coupon_type']      =   'Simple';
                                $couponData['created_at']       =   date('Y-m-d H:i');

                                array_push($couponDetails,$couponData);
                                        //creating coupon for secific product.
                                $this->geneal_model->addData('uw_coupons',$couponData);
                            }//End Create Coupons

                        endif;

                         //Get current sponsored coupon count from product. Added on 25-06-2023
                         $wconsponsoredCount['where']   =   array('products_id' => (int)$CA['product_id'] );
                         $productDetails                =   $this->geneal_model->getData2('single', 'uw_products', $wconsponsoredCount);

                         if($productDetails['sponsored_coupon']):
                             $sponsored_coupon = $productDetails['sponsored_coupon']*2;
                         else:
                             $sponsored_coupon = 2;
                         endif;
                         //END
                            
                            if($CA['is_donated'] == 'Y'):

                                //Start Create Coupons for donate product
                                for($i=1; $i <= $CA['quantity']*$sponsored_coupon; $i++){

                                    // generating ticket order..
                                    if(!empty($SoldoutTicketList['coupon_sold_number'])):
                                       $TicketSequence = $CurrentTicketSequence['tickets_sequence_start']+ $SoldoutTicketList['coupon_sold_number'] +$i;
                                       $totalsoldqty = $SoldoutTicketList['coupon_sold_number'] +$i;
                                   else:
                                    $TicketSequence = $CurrentTicketSequence['tickets_sequence_start']+ $i;
                                    $totalsoldqty = $i;
                                endif;
                                $coupon_code = $CurrentTicketSequence['tickets_prefix'].$TicketSequence;

                                array_push($couponList,$coupon_code);

                                    //Storing new ticket sequence in uw_quickcoupons_totallist start
                                    // $quickcoupons["product_id"]      =   (int)$ORparam['product_id'];
                                $quickcoupons1["coupon_sold_number"] =  (int)$totalsoldqty;
                                $quickcoupons1["creation_ip"]       =   $this->input->ip_address();
                                $quickcoupons1["updated_at"]        =   date('Y-m-d H:i');

                                //Saving quick coupons number  
                                // echo $totalsoldqty.'<br>';
                                $this->geneal_model->editData('uw_quickcoupons_totallist',$quickcoupons1,'tickets_seq_id',(int)$CurrentTicketSequence['tickets_seq_id']);
                                //End

                                $couponData['coupon_id']        =   (int)$this->geneal_model->getNextSequence('uw_coupons');
                                $couponData['users_id']         =   (int)$ORparam["user_id"];
                                $couponData['users_email']      =   $ORparam["user_email"];
                                $couponData['order_id']         =   $ORparam["order_id"];
                                $couponData['product_id']       =   $CA['product_id'];
                                $couponData['product_title']    =   $CA['product_name'];
                                $couponData['is_donated']       =   'Y';
                                $couponData['coupon_status']    =   'Live';
                                $couponData['coupon_code']      =   $coupon_code;
                                $couponData["device_type"]      =   $this->input->get('device_type');
                                $couponData["app_version"]      =   $this->input->get('app_version');
                                $couponData['draw_date']        =   $ProductData['draw_date'];
                                $couponData['draw_time']        =   $ProductData['draw_time'];
                                $couponData['coupon_type']      =   'Donated';
                                $couponData['created_at']       =   date('Y-m-d H:i');

                                array_push($couponDetails,$couponData);

                                $this->geneal_model->addData('uw_coupons',$couponData);
                                }//End Create Coupons

                            endif;

                        endif;

                            

                        //End Create Coupons

                        $data['finalPrice']             =   $ORparam["inclusice_of_vat"];
                        $data['stripe_token']           =   $ORparam["stripe_token"];

                        $wcon['where']                  =   array('order_id' => $ORparam["order_id"]);
                        $data['couponDetails']          =   $this->geneal_model->getData2('multiple', 'uw_coupons', $wcon);

                        $where          =   [ 'users_id' => (int)$ORparam["user_id"] ];
                        $tblName        =   'uw_users';
                        $userDetails    =   $this->geneal_model->getOnlyOneData($tblName, $where);
                        
                        $membershipData = $this->geneal_model->getMembership((int)$userDetails['totalArabianPoints']);
                        if($cashbackcount == 0):
	                        if($membershipData):
	                            $cashback           =   $data['finalPrice'] * $membershipData['benifit'] /100;
	                            $data['cashback']   =   $cashback;
	                            if($cashback):
	                                $insertCashback = array(
	                                    'cashback_id'   =>  (int)$this->geneal_model->getNextSequence('uw_cashback'),
	                                    'user_id'       =>  (int)$ORparam["user_id"],
	                                    'order_id'      =>  $ORparam["order_id"],
	                                    'cashback'      =>  (float)$cashback,
	                                    'created_at'    =>  date('Y-m-d H:i'),
	                                );
	                                $this->geneal_model->addData('uw_cashback',$insertCashback);

	                                $where2                     =   ['users_id' => (int)$ORparam["user_id"] ];
	                                $UserData                   =   $this->geneal_model->getOnlyOneData('uw_users', $where2);

	                                /* Load Balance Table -- after buy product*/
	                                $Cashbparam["load_balance_id"]      =   (int)$this->geneal_model->getNextSequence('uw_loadBalance');
	                                $Cashbparam["user_id_cred"]         =   (int)$ORparam["user_id"];
	                                $Cashbparam["user_id_deb"]          =   (int)$ORparam["user_id"];
	                                $Cashbparam["order_id"]             =   $ORparam["order_id"];
	                                $Cashbparam["availableArabianPoints"]   =   (float)$userDetails["availableArabianPoints"];
	                                $Cashbparam["end_balance"]              =   (float)$userDetails["availableArabianPoints"] + (float)$cashback ;
	                                $Cashbparam["arabian_points"]       =   (float)$cashback;
	                                $Cashbparam["record_type"]          =   'Credit';
	                                $Cashbparam["arabian_points_from"]  =   'Membership Cashback';
	                                $Cashbparam["creation_ip"]          =   $this->input->ip_address();
	                                $Cashbparam["created_at"]           =   date('Y-m-d H:i');
	                                $Cashbparam["created_by"]           =   $ORparam["user_type"];
	                                $Cashbparam["status"]               =   "A";

	                                $this->geneal_model->addData('uw_loadBalance', $Cashbparam);

	                                // Credit the purchesed points and get available arabian points of user.
	                                $this->geneal_model->creaditPoints($cashback ,(int)$ORparam["user_id"]);
	                                /* End */
	                            endif;
	                        endif;
                        endif;

                        //Add Referral Point
                        $SPwhereCon['where']        =   array('BUY_USER_ID' => (int)$ORparam["user_id"], 'SHARED_PRODUCT_ID' => (int)$CA['product_id']);
                        $shared_details             =   $this->geneal_model->getData2('single','uw_deep_link',$SPwhereCon);
                        
                        if($shared_details['SHARED_USER_ID'] && $shared_details['SHARED_USER_REFERRAL_CODE'] && $shared_details['SHARED_PRODUCT_ID']):
                            if(isset($productIdPrice[$shared_details['SHARED_PRODUCT_ID']])):

                                $prowhere['where']  =   array('products_id'=>(int)$shared_details['SHARED_PRODUCT_ID']);
                                $prodData           =   $this->geneal_model->getData2('single','uw_products',$prowhere);
                                
                                $sharewhere['where']=   array('users_id'=>(int)$shared_details['SHARED_USER_ID'],'products_id'=>(int)$shared_details['SHARED_PRODUCT_ID']);
                                $shareCount         =   $this->geneal_model->getData2('count','uw_product_share',$sharewhere);

                                if($shareCount == NULL):
                                    $shareCount = 0;
                                endif;

                                if(isset($prodData['share_limit']) && $shareCount < $prodData['share_limit']):

                                    $param['share_id']                  =   (int)$this->common_model->getNextSequence('uw_product_share');
                                    $param['users_id']                  =   (int)$shared_details['SHARED_USER_ID'];
                                    $param['products_id']               =   (int)$shared_details['SHARED_PRODUCT_ID'];
                                    $param['creation_date']             =   date('Y-m-d H:i');
                                    $param['creation_ip']               =   $this->input->ip_address();
                                    $this->common_model->addData('uw_product_share',$param);

                                    $productCartAmount          =   $productIdPrice[$shared_details['SHARED_PRODUCT_ID']];
                                    //First label referal amount Credit
                                    // $ref1tbl                     =   'referral_percentage';
                                    // $ref1where                   =   ['referral_lebel' => (int)1 ];
                                    // $referal1Data                =   $this->geneal_model->getOnlyOneData($ref1tbl, $ref1where);
                                    $ref1tbl                    =   'uw_products';
                                    $ref1where                  =   ['products_id' => (int)$shared_details['SHARED_PRODUCT_ID'] ];
                                    $referal1Data               =   $this->geneal_model->getOnlyOneData($ref1tbl, $ref1where);
                                    if($referal1Data && $referal1Data['share_percentage_first'] > 0):
                                        $referal1Amount         =   (($productCartAmount*$referal1Data['share_percentage_first'])/100);

                                        /* Referal Product Table -- after buy product*/
                                        $ref1Amtparam["referral_id"]            =   (int)$this->geneal_model->getNextSequence('referral_product');
                                        $ref1Amtparam["referral_user_code"]     =   (int)$shared_details['SHARED_USER_REFERRAL_CODE'];
                                        $ref1Amtparam["referral_from_id"]       =   (int)$shared_details['SHARED_USER_ID'];
                                        $ref1Amtparam["referral_to_id"]         =   (int)$ORparam["user_id"];
                                        $ref1Amtparam["referral_percent"]       =   (float)$referal1Data['share_percentage_first'];
                                        $ref1Amtparam["referral_amount"]        =   (float)$referal1Amount;
                                        $ref1Amtparam["referral_cart_amount"]   =   (float)$productCartAmount;
                                        $ref1Amtparam["referral_product_id"]    =   (int)$shared_details['SHARED_PRODUCT_ID'];
                                        $ref1Amtparam["creation_ip"]            =   $this->input->ip_address();
                                        $ref1Amtparam["created_at"]             =   date('Y-m-d H:i');
                                        $ref1Amtparam["created_by"]             =   (int)$ORparam["user_id"];
                                        $ref1Amtparam["status"]                 =   "A";
                                        
                                        array_push($ref1AmtDetails,$ref1Amtparam);


                                        $this->geneal_model->addData('referral_product', $ref1Amtparam);
                                        /* End */
                                        if($ref1count == 0):
	                                        /* Load Balance Table -- after buy product*/
	                                        $ref1param["load_balance_id"]       =   (int)$this->geneal_model->getNextSequence('uw_loadBalance');
	                                        $ref1param["user_id_cred"]          =   (int)$shared_details['SHARED_USER_ID'];
	                                        $ref1param["user_id_deb"]           =   (int)0;
	                                        $ref1param["product_id"]            =   (int)$ref1Amtparam["referral_product_id"];
	                                        $ref1param["arabian_points"]        =   (float)$referal1Amount;
	                                        $ref1param["record_type"]           =   'Credit';
	                                        $ref1param["arabian_points_from"]   =   'Referral';
	                                        $ref1param["creation_ip"]           =   $this->input->ip_address();
	                                        $ref1param["created_at"]            =   date('Y-m-d H:i');
	                                        $ref1param["created_by"]            =   (int)$ORparam["user_id"];
	                                        $ref1param["status"]                =   "A";
	                                        
	                                        $this->geneal_model->addData('uw_loadBalance', $ref1param);

	                                        $ref1count++;

										endif;

                                        $where25['where']               =   [ 'users_id' => (int)$shared_details['SHARED_USER_ID'] ];
                                        $sharedUserdata                 =   $this->geneal_model->getData2('single','uw_users', $where25);
                                        $this->geneal_model->addData('uw_test', $sharedUserdata);

                                        $userWhecrCon['where']      =   array('users_id' => (int)$sharedUserdata['users_id']);
                                        $totalArabianPoints         =   $sharedUserdata['totalArabianPoints'] + $ref1param["arabian_points"];
                                        $availableArabianPoints     =   $sharedUserdata['availableArabianPoints'] + $ref1param["arabian_points"];
                                        
                                        $updateArabianPoints['totalArabianPoints']      =   (float)$totalArabianPoints;
                                        $updateArabianPoints['availableArabianPoints']  =   (float)$availableArabianPoints;

                                        $this->geneal_model->editDataByMultipleCondition('uw_users',$updateArabianPoints,$userWhecrCon['where']);
                                        /* End */
                                    endif;
                                    
                                endif;
                            endif;
                            $this->geneal_model->deleteData('uw_deep_link', 'seq_id', (int)$shared_details['seq_id']);
                        endif;
                        //END

                    endforeach;

                    //Delete cart items.
                    $this->geneal_model->deleteData('uw_cartItems', 'user_id',(int)$this->input->get('users_id'));

                    $result['orderData']              = $data['orderData'];
                    $result['finalPrice']             = $orderData[0]['inclusice_of_vat'];
                    $result['couponDetails']          = $couponDetails;
                    $result['cashback']               = $cashback;
                    $result['orderDetails']           = $currentOrderDetails;         

                    $results['successData'] = $result;
                   
                    $this->emailsendgrid_model->sendOrderMailToUser($this->input->get('order_id'));
                    $this->sms_model->sendTicketDetails($this->input->get('order_id'));

                    echo outPut(1,lang('SUCCESS_CODE'),lang('ORDER_SUCCESS'),$results);

                else:
                    echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_INCORRECT'),$result);
                endif;
            endif;
            else:
                echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
            endif;
    }

    /* * *********************************************************************
	 * * Function name : showDefaltCompany
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for get show Defalt Company
	 * * Date : 05 August 2023
	 * * **********************************************************************/
	public function showDefaltCompany()
	{	
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'GET')):
			
			$tblName2 					=	'uw_default_company';
			$where2['where'] 			=	array( 'status' => 'A');
			$data2						=	$this->geneal_model->getData2('single',$tblName2,$where2,$order2);
			$result['company_details']    	=   $data2;
			echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}
	
	/* * *********************************************************************
	 * * Function name : draw_winner
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used to send redeem_status to admin user.
	 * * Date : 20 December 2023
	 * * **********************************************************************/
	public function draw_winner()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):
			
			if($this->input->post('order_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('ORDER_ID_EMPTY'),$result);
			
			elseif($this->input->post('redeem_status') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_REDEEMSTATUS'),$result);
			
			elseif($this->input->post('redeemeByArabianPoint') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_RedeemByArabianPoint'),$result);
			else:

				
				$where['where'] =	array('order_id' => $this->input->post('order_id'));
				$tblName 		=	'wn_draw_winners';
				$DrawWinnerData	=	$this->common_model->getData('single',$tblName, $where);

				if(!empty($DrawWinnerData)):
					if($DrawWinnerData['redeemeByArabianPoint'] == 'N' || $DrawWinnerData['redeemeByArabianPoint'] == null ):
						
						$updateParams['redeem_status'] 		=  $this->input->post('redeem_status');
						$updateParams['redeemeByArabianPoint'] =  $this->input->post('redeemeByArabianPoint');
						$result = $this->geneal_model->editData('wn_draw_winners', $updateParams, 'order_id', $this->input->post('order_id'));
						echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
					else:
						echo outPut(0,lang('SUCCESS_CODE'),lang('ALREADY_SENT_TO_ADMIN'),$result);
					endif;
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('DATA_NOT_FOUND'),$result);
				endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}

	/* * *********************************************************************
	 * * Function name : cancle_draw_winner
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used to send redeem_status to admin user.
	 * * Date : 20 December 2023
	 * * **********************************************************************/
	public function cancel_draw_winner()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):
			
			if($this->input->post('order_id') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('ORDER_ID_EMPTY'),$result);
			elseif($this->input->post('redeem_status') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_REDEEMSTATUS'),$result);
			elseif($this->input->post('redeemeByArabianPoint') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_RedeemByArabianPoint'),$result);
			else:

				
				$where['where'] =	array('order_id' => $this->input->post('order_id'));
				$tblName 		=	'wn_draw_winners';
				$DrawWinnerData	=	$this->common_model->getData('single',$tblName, $where);

				if(!empty($DrawWinnerData)):
					if($DrawWinnerData['redeemeByArabianPoint'] == 'Y' || $DrawWinnerData['redeemeByArabianPoint'] == null ):
						$updateParams['redeem_status'] 		    =  "";
						$updateParams['redeemeByArabianPoint']  =  $this->input->post('redeemeByArabianPoint');
						$result = $this->geneal_model->editData('wn_draw_winners', $updateParams, 'order_id', $this->input->post('order_id'));
						echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
					else:
						echo outPut(0,lang('SUCCESS_CODE'),lang('ALREADY_SENT_TO_ADMIN'),$result);
					endif;
				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('DATA_NOT_FOUND'),$result);
				endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}
   	

   	/* * *********************************************************************
	 * * Function name : reddem_by_cash_draw
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used to update status of redeem by cash.
	 * * Date : 08 January 2024
	 * * **********************************************************************/
	public function redeem_by_cash_draw()
	{
		$apiHeaderData 		=	getApiHeaderData();
		$this->generatelogs->putLog('APP',logOutPut($_POST));
		$result 							= 	array();	
		if(requestAuthenticate(APIKEY,'POST')):
			
			if($this->input->post('code') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('COUPON_CODE_ID_EMPTY'),$result);
			elseif($this->input->post('redeem_status') == ''): 
				echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_REDEEMSTATUS'),$result);
			// elseif($this->input->post('redeem_status') == ''): 
			// 	echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_RedeemByArabianPoint'),$result);
			// elseif($this->input->post('redeembycash') == ''): 
			// 	echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_RedeemByArabianPoint'),$result);
			else:
				
				$where['where']  =	array('code' => $this->input->post('code'));
				$tblName 		 =	'wn_daily_winners';
				$DrawWinnerData	 =	$this->common_model->getData('single',$tblName, $where);

				if(!empty($DrawWinnerData)):
					if($DrawWinnerData['redeembycash'] == '' || $DrawWinnerData['redeembycash'] == null ):
						$updateParams['redeem_status'] 	  =  "paid";
						$updateParams['setteld_status']   =  (int)"1";
						$updateParams['redeembycash']  	  =  $this->input->post('redeembycash');
						$result = $this->geneal_model->editData('wn_daily_winners', $updateParams, 'code', $this->input->post('code'));
						echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
					else:
						echo outPut(0,lang('SUCCESS_CODE'),lang('ALREADY_SENT'),$result);
					endif;

				else:
					echo outPut(0,lang('SUCCESS_CODE'),lang('DATA_NOT_FOUND'),$result);
				endif;
			endif;
		else:
			echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
		endif;
	}
	
}
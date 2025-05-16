<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Sms_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct(); 
	}

	/* * *********************************************************************
	 * * Function name : sendMessageFunction 
	 * * Developed By : Manoj Kumar
	 * * Purpose  : This function use for send Message Function
	 * * Date : 08 APRIL 2021
	 * * **********************************************************************/
	public function sendMessageFunction($phone='',$message='',$type='') {
		if(!empty($phone) && !empty($message) && (SMS_SEND_STATUS == 'YES' || $type =='default')):
			$mobileno	=	$phone;
			$sentmessage=	urlencode($message);
			$authkey	=	SMS_AUTH_KEY;
			$url		=	"http://api.msg91.com/api/sendhttp.php";
			$hiturl		=	$url."?country=".SMS_COUNTRY_CODE."&sender=".SMS_SENDER."&route=4&mobiles=".$mobileno."&authkey=".$authkey."&message=".$sentmessage;	
			$ch = curl_init();
			// set URL and other appropriate options
			curl_setopt($ch, CURLOPT_URL, $hiturl);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			// grab URL and pass it to the browser
			curl_exec($ch);
			// close cURL resource, and free up system resources
			curl_close($ch);
			//$homepage = file_get_contents($hiturl);
			//return $homepage;
		endif;
	}
	
	/* * *********************************************************************
	 * * Function name : sendMessageFunction1 
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function use for send Message Function
	 * * Date : 23 march 2023
	 * * **********************************************************************/
	public function sendMessageFunction1($phone='',$message='',$senderid='') {
		try {
			if(!empty($phone) && !empty($message) && !empty($senderid)):
				//Please Enter Your Details
				$user='pltech'; //your username
				$password=SMSCOUNTRYPASSWORD; //your password
				$mobilenumbers=substr($phone,1); //enter Mobile numbers comma seperated
				$message = $message; //enter Your Message
				$senderid=$senderid; //Your senderid

				$messagetype="N"; //Type Of Your Message
				$DReports="Y"; //Delivery Reports
				$url="http://www.smscountry.com/SMSCwebservice_Bulk.aspx";
				$message = urlencode($message);
				$ch = curl_init();
				if (!$ch){die("Couldn't initialize a cURL handle");}
				$ret = curl_setopt($ch, CURLOPT_URL,$url);
				curl_setopt ($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
				curl_setopt ($ch, CURLOPT_POSTFIELDS,
				"User=$user&passwd=$password&mobilenumber=$mobilenumbers&message=$message&sid=$senderid&mtype=$messagetype&DR=$DReports");
				$ret = curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				//If you are behind proxy then please uncomment below line and provide your proxy ip with port.
				// $ret = curl_setopt($ch, CURLOPT_PROXY, "PROXY IP ADDRESS:PORT");
				$curlresponse = curl_exec($ch); // execute
				if(curl_errno($ch))
				return "ERROR";
				//echo 'curl error : '. curl_error($ch);
				if (empty($ret)) {
					return "some kind of an error happened";
				// some kind of an error happened
				//die(curl_error($ch));
				curl_close($ch); // close cURL handler
				} else {
				$info = curl_getinfo($ch);
				curl_close($ch); // close cURL handler
				return $curlresponse; //echo "Message Sent Succesfully" ;
				}
			endif;
		} catch (\Throwable $th) {
			return "FAIL";
		}
		
	}
	

	/***********************************************************************
	** Function name : sendOtpVarification
	** Developed By : Dilip Halder
	** Purpose  : This is use for send Login Otp Sms To User
	** Date : 24 APRIL 2023
	************************************************************************/
	public function sendOtpVarification($mobile='',$otp)
	{
	 	$message		=	"Your 4 digit OTP is. ".$otp;
		$senderid		=	"DLZARBA";

		if($mobile == "919539894273"):
			$mobileNumber   =   '+91'.$mobile;
		else:
			$mobileNumber   =   '+971'.$mobile;
		endif;
		$returnMessage	=	$this->sendMessageFunction1($mobileNumber,$message,$senderid);
		return $returnMessage;
	}

	/***********************************************************************
	** Function name : sendLoginOtpSmsToUser
	** Developed By : Manoj Kumar
	** Purpose  : This is use for send Login Otp Sms To User
	** Date : 08 APRIL 2021
	************************************************************************/
	function sendLoginOtpSmsToUser($mobileNumber='',$otp='') {  
		if($mobileNumber && $otp):  
			$message		=	"Your One Time Password for Login is ".$otp.".";
			$returnMessage	=	$this->sendMessageFunction($mobileNumber,$message);
		
			return $returnMessage;
		endif;
	}
	


	/***********************************************************************
	** Function name : sendForgotPasswordOtpSmsToUser
	** Developed By : Manoj Kumar
	** Purpose  : This is use for send Forgot Password Otp Sms To User
	** Date : 08 APRIL 2021
	************************************************************************/
	function sendForgotPasswordOtpSmsToUser($mobileNumber='',$otp='') {  
		if($mobileNumber && $otp):
			$message		=	"Your One Time Password for Forgot Password is ".$otp.".";
			$returnMessage	=	$this->sendMessageFunction($mobileNumber,$message);
			return $returnMessage;
		endif;
	}
	
		/***********************************************************************
	** Function name : sendForgotPinOtpSmsToUser
	** Developed By : Manoj Kumar
	** Purpose  : This is use for send Forgot Pin Otp Email To User
	** Date : 08 APRIL 2021
	************************************************************************/
	
		function sendForgotPinOtpSmsToUser($email='',$otp='') {  
		   
		if($email && $otp):
		$htmlContent = "Your 4 digit OTP is. $otp";
        $config = Array(
        'EMAIL_HOST' => 'ssl://smtp.gmail.com',
        'EMAIL_PORT' => 465,
        'EMAIL_HOST_USER' => 'mritu0600@gmail.com',
        'EMAIL_HOST_PASSWORD' => '12345',
        'EMAIL_USE_TLS' => TRUE
        );
        $this->load->library('email',$config);
        $this->email->set_newline("\r\n");
        $this->email->set_header('MIME-Version', '1.0; charset=utf-8');
        $this->email->set_header('Content-type', 'text/html');
        $this->email->from('mritu0600@gmail.com');
        $this->email->to($email);
        $this->email->subject('forgetpin');
        $this->email->message($htmlContent);
        $a = $this->email->send();
        var_dump($a);
        echo $this->email->print_debugger();
		endif;
	}
	


	/***********************************************************************
	** Function name : sendChangePasswordOtpSmsToUser
	** Developed By : Manoj Kumar
	** Purpose  : This is use for send Change Password Otp Sms To User
	** Date : 08 APRIL 2021
	************************************************************************/
	function sendChangePasswordOtpSmsToUser($mobileNumber='',$otp='') {  
		if($mobileNumber && $otp):
			$message		=	"Your One Time Password for Change Password is ".$otp.".";
			$returnMessage	=	$this->sendMessageFunction($mobileNumber,$message);
			return $returnMessage;
		endif;
	}


	/***********************************************************************
	** Function name 	: sendTicketDetails
	** Developed By 	: Dilip Halder
	** Purpose  		: This is use for send success reset password Sms To User
	** Date 			: 02 march 2023
	** Updatead By 		: Dilip Halder
	** Date 			: 15 march 2023
	************************************************************************/
	function sendTicketDetails($oid = ''){

		// Getting order details.
		$tblName 				=	'uw_orders';
		$shortField 			= 	array('_id'=> -1 );
		$whereCon['where']		= 	array('order_id'=>$oid);
		$orderData  =	$this->common_model->getData('single', $tblName, $whereCon,$shortField);

		$tblName2 				=	'uw_orders_details';
		$shortField 			= 	array('_id'=> -1 );
		$whereCon['where']		= 	array('order_id'=>$oid);
		$orderDetailData  =	$this->common_model->getData('multiple', $tblName2, $whereCon,$shortField);


		// Featching All details related to order.
		$product = [];// created aaray to store product details from loop.
		foreach($orderDetailData as $items):
			
			// product 
			$tblName2 				=	'uw_products';
			$shortField 			= 	array('_id'=> -1 );
			$whereCon['where']		= 	array('products_id'=>(int)$items['product_id']);
			$productDetails  		=	$this->common_model->getData('single', $tblName2, $whereCon,$shortField);

			$product['title'][] 	=  $productDetails['title'];
			$product['draw_date'][] =  $productDetails['draw_date'];
		endforeach;
		// End order details.
		
		// coupons.
		$tblName3 				=	'uw_coupons';
		$shortField 			= 	array('_id'=> -1 );
		$whereCon['where']		= 	array('order_id'=>$oid );
		$CouponDetails  		=	$this->common_model->getData('multiple', $tblName3, $whereCon,$shortField);

		foreach($CouponDetails as $coupons):
			$product['coupons'][] = $coupons['coupon_code'];
		endforeach;

		// user id 
		$UserId = $orderData['user_id'];
		$tbl 				=	'uw_users';
		$where['where'] 	=	['users_id' => (int)$UserId];
		$UserData			=	$this->common_model->getData('single',$tbl, $where,[]);

		$order_id  		  	= $orderData['order_id'];
		$collection_code  	= $orderData['collection_code'];

		$productTitle 		= implode(' , ', $product['title']);
		$draw_date 	= implode(' , ', $product['draw_date']);
		$couponList			= implode(' , ', $product['coupons']);
		$country_code     	= $UserData['country_code'];
		$users_mobile     	= $UserData['users_mobile'];
		$total_price     	= $orderData['total_price'];
		
		// Creating list of coupon code generated per order.

		if($order_id ):
			 // $message		=	"Your ticket number is $oid , campaign - ". ucfirst($productTitle)."  , coupon code - $couponList has been cancelled ";
			 $message		=	"You canceled your purchase of a $total_price AED ticket with the order ID $oid and the coupon code $couponList .";
			$senderid		=	"DLZARBA";
			$mobileNumber   =   $country_code.$users_mobile;
			$returnMessage	=	$this->sendMessageFunction1($mobileNumber,$message,$senderid);
			return $returnMessage;
		endif;
	}

	/***********************************************************************
	** Function name 	: sendLottoTicketDetails
	** Developed By 	: Dilip Halder
	** Purpose  		: This is use for send success reset password Sms To User
	** Date 			: 30 January 2024
	** Updatead By 		:  
	** Date 			: 
	************************************************************************/
	function sendLottoTicketDetails($oid = ''){

		// Getting order details.
		$tblName 				= 'uw_lotto_orders';
		$shortField 			= array('_id'=> -1 );
		$whereCon['where']		= array('order_id'=>$oid);
		$orderData  			= $this->common_model->getData('single', $tblName, $whereCon,$shortField);
		$order_id  		  		= $orderData['order_id'];
		$couponList             = $orderData['ticket'];
		$total_price     	    = $orderData['total_price'];
		$UserId 				= $orderData['user_id'];
		
		$ticket = json_decode($orderData['ticket']);
		foreach ($ticket as $key => $item):
		   $coupon = implode(' ', $item);
		   $Couponlist[] = $coupon;
		endforeach;
		$couponList = implode(', ', $Couponlist);
			
		if($order_id ):

			// user id 
			$tbl 				=	'uw_users';
			$where['where'] 	=	array('users_id' => (int)$UserId);
			$UserData			=	$this->common_model->getData('single',$tbl, $where,[]);
			
			// $country_code =	$UserData['country_code'];
			// $users_mobile =	$UserData['users_mobile'];
			$country_code =	"+91";
			$users_mobile =	"8700144841";

			$message		=	"You canceled your purchase of a $total_price AED ticket with the order ID $oid and the coupon code $couponList .";
			$senderid		=	"DLZARBA";
			$mobileNumber   =   $country_code.$users_mobile;
			$returnMessage	=	$this->sendMessageFunction1($mobileNumber,$message,$senderid);
			return $returnMessage;
		endif;
	}

	/***********************************************************************
	** Function name 	: sendTicketDetails
	** Developed By 	: Afsar Ali
	** Purpose  		: This is use for send success reset password Sms To User
	** Date 			: 06 July 2023
	** Updatead By 		: 
	** Date 			: 
	************************************************************************/
	function sendCancelQuickBuy($oid = ''){
		// Getting order details.
		$tblName 				=	'uw_ticket_coupons';
		$shortField 			= 	array('_id'=> -1 );
		$whereCon['where']		= 	array('ticket_order_id'=>$oid);
		$orderData  =	$this->common_model->getData('single', $tblName, $whereCon,$shortField);


		$couponList			= 	implode(' , ', $orderData['coupon_code']);
		$total_price		=	$orderData['total_price'];
		$country_code 		=	$orderData['order_users_country_code'];
		$users_mobile		=	$orderData['order_users_mobile'];
		$message		=	"You canceled your purchase of a $total_price AED ticket with the order ID $oid and the coupon code $couponList .";
		$senderid		=	"DLZARBA";
		$mobileNumber   =   $country_code.$users_mobile;
		$returnMessage	=	$this->sendMessageFunction1($mobileNumber,$message,$senderid);
		
		return $returnMessage;
	}

	/***********************************************************************
	** Function name 	: creditAp
	** Developed By 	: Dilip Halder
	** Purpose  		: This is use for send success reset password Sms To User
	** Date 			: 23 December 2023
	************************************************************************/
	function creditAp($UserDetails='',$DrawData='',$statusType=''){
		
		$full_name 		= $UserDetails['users_name'] .' '. $UserDetails['last_name'];
		$order_id  		= $DrawData['order_id'];
		$amount    		= $DrawData['amount'];
		$country_code   = $UserDetails['country_code']?$UserDetails['country_code']:'+971';
		$users_mobile   = $UserDetails['users_mobile'];

		if($statusType == "A"):
			$message		=	"Hi ".$full_name." ".$amount ." Arabian Points credited in your Account" ;
		else:
			$message		=	"Hi ".$full_name." ".$amount ." Arabian Points debited into your Account" ;
		endif;
		$senderid		=	"DLZARBA";
		$mobileNumber   =   $country_code.$users_mobile;
		$returnMessage	=	$this->sendMessageFunction1($mobileNumber,$message,$senderid);
		return $returnMessage;
	}
}	
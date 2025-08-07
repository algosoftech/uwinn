<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
require_once 'sendgridmail/library/sendgrid-php.php';

use SendGrid\Mail\Mail;

class Emailsendgrid_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct(); 
		//$this->load->database();
	}
	/***********************************************************************
	** Function name : get_email_template_by_mail_type
	** Developed By : Dilip Halder
	** Purpose  : This is get  email template by mail type.
	** Date : 24 APRIL 2023
	************************************************************************/
	function sendOtpVarification($to='',$otp )
	{	
		
		//  $html = "Your 4 digit OTP is. $otp";

		//  // echo $email;
		// if($html <> ""):  
		// 		#.............................. message section ............................#
		// 		try {

		// 			if($to):
		// 				$email = new Mail();
		// 				$email->setFrom(MAIL_FROM_MAIL, MAIL_SITE_FULL_NAME);
		// 				$email->setSubject('OTP Varification');
		// 				$email->addTo($to);
		// 				$email->addContent("text/html",$html);
						
		// 				$sendgrid = new \SendGrid(SENDGRID_KEY);
		// 				$response = $sendgrid->send($email);
		// 				return true;

		// 			else:
		// 				throw new Exception('Email id required');
		// 			endif;
					
		// 		} catch (Exception $e) {
		// 			return false;
		// 		}
				
		// 	endif;

		try {
			if(!empty($to) && !empty($otp)):
 				$html     = "Your 4 digit OTP is $otp";
 				$subject  = "OTP Verification";
 				$curl = curl_init();
				curl_setopt_array($curl, array(
				  CURLOPT_URL => 'https://api.mailjet.com/v3.1/send',
				  CURLOPT_RETURNTRANSFER => true,
				  CURLOPT_ENCODING => '',
				  CURLOPT_MAXREDIRS => 10,
				  CURLOPT_TIMEOUT => 0,
				  CURLOPT_FOLLOWLOCATION => true,
				  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				  CURLOPT_CUSTOMREQUEST => 'POST',
				  CURLOPT_POSTFIELDS =>'{
						"Messages":[
							{
								"From": {
										"Email": "info@u-winn.com",
										"Name": "Uwinn"
								},
								"To": [
										{
										  "Email": "'.$to.'"
										}
								],
								"Subject" : "'.$subject.'",
								"TextPart": "'.$html.'",
								"HTMLPart": "'.$html.'"
							}
						]
					}',
				  CURLOPT_HTTPHEADER => array(
				    'Content-Type: application/json',
				    'Authorization: Basic '.MAILJET
				  ),
				));
				$response = curl_exec($curl);
				curl_close($curl);
				$result   = json_decode($response ,true);
				if(!empty($result['Messages'][0]['Status']) && $result['Messages'][0]['Status'] != 'success'):
					throw new Exception("Email not send", 1);
				elseif($result['StatusCode'] == 400):
					throw new Exception("Email not send", 1);
				endif;
			else:
				throw new Exception("Email not send", 1);
			endif; 
			
		} catch (Exception $e) {
			// echo outPut(0,lang('SUCCESS_CODE'),$e->getMessage(),$result);die();
		}
	}
	


	##################################################################################################################################
	####################################	SENDGRID MAILER SECTION 		##############################################################
	##################################################################################################################################
	function sendForgotpasswordMailToUser($userData=array()) {  
		if(!empty($userData['users_email'])):
			$MTData				=	$this->get_email_template_by_mail_type(100000000000002); 
			if($MTData <> ""):  
				#.............................. message section ............................#
				$MHData 		= 	stripslashes($MTData['mail_header']);
	
				$MBData 		= 	str_replace('{USERNAME}', $userData['users_name'], stripslashes($MTData['mail_body']));
				
				$MBData         =   str_replace('{OTP}',$userData['users_otp'], $MBData);

				$MFData 		= 	stripslashes($MTData['mail_footer']);
	
				$MHtml	 		= 	stripslashes($MTData['html']);
				$MHtml	 		= 	str_replace('{MAIL-HEADER}', $MHData, $MHtml);	
				$MHtml	 		= 	str_replace('{MAIL-BODY}', $MBData, $MHtml);
				$MHtml	 		= 	str_replace('{MAIL-FOOTER}', $MFData, $MHtml);
				//echo $MHtml; die;

				$email = new Mail();
				$email->setFrom(MAIL_FROM_MAIL, MAIL_SITE_FULL_NAME);
				$email->setSubject($MTData['subject']);
				$email->addTo($userData['users_email'], $userData['users_name']);
				//$email->addBcc("test@example.com", "Example User");
				//$email->addContent("text/plain", "and easy to do anywhere, even with PHP");
				$email->addContent("text/html",$MHtml);

				//$sendgrid = new \SendGrid(getenv('SENDGRID_API_KEY'));
				$sendgrid = new \SendGrid(SENDGRID_KEY);
				$response = $sendgrid->send($email);
				return true;
				// try {
				//     $response = $sendgrid->send($email);
				//     print $response->statusCode() . "\n";
				//     //print_r($response->headers());
				//     print $response->body() . "\n";
				// } catch (Exception $e) {
				//     echo 'Caught exception: '.  $e->getMessage(). "\n";
				// }
			endif;
			return true;
		endif;
	}
	



	/***********************************************************************
	** Function name 	: sendOrderMailToUser
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used to send email.
	** Date 			: 14 JUNE 2023
	************************************************************************/

	function sendOrderMailToUser($oid = '') {  
		if(!empty($oid)):
			$tblName 				=	'uw_orders';
			$shortField 			= 	array('_id'=> -1 );
			$whereCon['where']		= 	array('order_id'=>$oid);

			// $orderData  =	$this->common_model->getordersList('single', $tblName, $whereCon,$shortField);
			$orderData  =	$this->common_model->getdata('single', $tblName, $whereCon,$shortField);
			
			$tblName2 				=	'uw_orders_details';
			$orderDetails  =	$this->common_model->getdata('multiple', $tblName2, $whereCon,$shortField);


			$tblName3 				=	'uw_coupons';
			$CouponsDetails  =	$this->common_model->getdata('multiple', $tblName3, $whereCon,$shortField);

			$name = explode('@',$orderData['user_email']);
			//$MHtml = $this->load->view('order_mail_template', $data);
			//print_r($MHtml);die();
			$html = '<!DOCTYPE html>
			<html lang="en">

			<head>
			<title>Dealzerbia | mailtemplate</title>
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<style>
			a:link, a:active {
				color: #000 !important;
				text-decoration: none;
			}
			a{
				color:#000 !important;
			}
			@media screen and (min-device-width: 360px) and (max-device-width: 600px) {
				body {
					background-color: #fff !important;
					margin: 0px;
				}
			}
			</style>
			</head>
			<body style="background-color: #1e1e1e;color: #010f19 !important;">
			<div style="width: 533px;margin: auto;background-color: #fff;padding: 19px 15px 19px;">
			<div class="row" style="margin: 0px 35px;">
			<div style="background: #fff;padding: 5px 15px 5px;border: 1px solid #d4d4d4;border-radius: 10px;margin: 0px 18px;text-align: center;">
			<img src="https://dealzarabia.com/assets/img/jpeg_logo.jpg" alt="logo">
			</div>
			</div>';


			$html .= '<div class="main_content" style="margin:39px 0px;text-align:center;">
			<h1 style="font-family: "sans-serif;font-size: 27px;color: #343333;text-align: center;margin: 0px;line-height: 0px;"> Refund Invoice </h1>
			<p style="font-family: "sans-serif;font-size: 18px;color: #343333;text-align: center;margin: 0px;line-height: 0px;"> Hello '.$name[0].', Your Order Details</p>
			</div>
			<div class="main_content" style="margin:39px 0px;    margin: 39px 54px;border: 1px solid #80808061;padding: 4px 16px 15px;border-radius: 5px;">
			<div class="row" style="border-bottom: 1px solid #80808030;display: inline-flex;justify-content: space-between;margin: 11px 0px 0px;">
			<div style="width: 315px;">	
			<p style=" margin-left: 10px !important;margin:0px;font-family: "Open Sans", sans-serif;font-size: 16px;color: #343333;margin-bottom:7px;margin-top: 0px;">
			Order Id</p>
			</div>
			<div>
			<p style=" margin-left: 10px !important;margin:0px;font-family: "Open Sans", sans-serif;font-size: 16px;color: #343333;margin-bottom:7px;font-weight:600;">
			'.$orderData['order_id'].'</p>
			</div>	
			</div>
			<div class="row" style="border-bottom: 1px solid #80808030;display: inline-flex;justify-content: space-between;margin: 0px 0px;">
			<div style="width: 315px;">
			<p style=" margin-left: 10px !important;margin:0px;font-family: "Open Sans", sans-serif;font-size: 16px;color: #343333;margin: 0px;
			margin-right: 247px;margin-top: 0px"> Order Date</p>
			</div>
			<div>
			<p style=" margin-left: 10px !important;margin:0px;font-family: "Open Sans", sans-serif;font-size: 16px;color: #343333;margin:0px;font-weight:600;margin-top: 0px"> '.date('d M, Y', strtotime($orderData['created_at'])).'</p>
			</div>
			</div>
			</div>';

			foreach($orderDetails as $orderinfo):

				$htmlorder .= ' <div class="row" style="display: flex;justify-content: space-between;margin: 11px 57px;border: 1px solid #80808061;padding: 13px;">
				<div class="product_box">
				<div class="img">
				<img src="'.MAIN_URL.$orderinfo['other']->image.'" style="width:100px;">
				</div>
				</div>
				<div class="product" style="margin: 0px 20px;">
				<h1 style="font-family: "Open Sans", sans-serif;font-size: 16px;color: #343333;margin-bottom: 1px;"> '.stripslashes($orderinfo['product_name']).'</h1>
				<p style="font-family: "Open Sans", sans-serif;font-size: 16px;color: #343333;margin:0px;ine-height: 20px;"> Qty : <span style="font-weight: 600;">'.$orderinfo['quantity'].'</span></p>
				<p style="font-family: "Open Sans", sans-serif;font-size: 16px;color: #343333;margin: 0px;line-height: 20px;"> '.stripslashes($orderinfo['other']->description).'</p>
				</div>
				<div class="product_details">
				<p style="font-family: "Open Sans", sans-serif;font-size: 16px;color: #343333;font-weight:600;text-align: end;margin: 12px 0px;"> AED'.number_format($orderinfo['price'],2).'</p>
				</div>
				</div>'; 

			endforeach;

			$html .=  $htmlorder;

			$html .= '<div class="main_content" style="margin:0px 0px;">
			<div class="row" >
			<div style="text-align: center;">
			<h1 style="font-family: "Open Sans", sans-serif;font-size: 18px;color: #343333;">Payment Information </h1>
			</div>
			<div class="main_content" style="margin: 29px 54px;border: 1px solid #80808061;padding: 4px 16px;border-radius: 5px;">
			<div class="row" style="display: inline-flex;justify-content: space-between;margin: 0px 0px;border-bottom: 1px solid #80808030;">
			<div style=" width: 324px;">
			<p style="margin-left: 10px !important;margin: 0px;font-family: "Open Sans", sans-serif;font-size: 16px;color: #343333;margin-bottom:7px;margin-top: 0px;"> Inclusice of VAT</p>
			</div>
			<div>
			<p style="margin-left: 10px !important;margin: 0px;font-family: "Open Sans", sans-serif;font-size: 16px;color: #343333;margin-bottom:7px;font-weight: 600;margin-top: 0px;"> AED '.number_format($orderData['inclusice_of_vat'],2).'</p>
			</div>
			</div>
			<div class="row" style="display: inline-flex;justify-content: space-between;margin: 0px 0px;border-bottom: 1px solid #80808030;">
			<div  style=" width: 324px;">
			<p style="margin-left: 10px !important;margin: 0px;font-family: "Open Sans", sans-serif;font-size: 16px;color: #343333;margin-bottom:7px;width: 324px;margin-top: 0px;"> Subtotal</p>
			</div>
			<div>
			<p style="margin-left: 10px !important;margin: 0px;font-family: "Open Sans", sans-serif;font-size: 16px;color: #343333;margin-bottom:7px;font-weight: 600;margin-top: 0px;"> AED '.number_format($orderData['subtotal'],2).'</p>
			</div>
			</div>
			<div class="row" style="display: inline-flex;justify-content: space-between;margin: 0px 0px;border-bottom: 1px solid #80808030;">
			<div  style=" width: 324px;">
			<p style="margin-left: 10px !important;margin: 0px;font-family: "Open Sans", sans-serif;font-size: 16px;color: #343333;margin-bottom:7px;width: 324px;margin-top: 0px;"> VAT</p>
			</div>
			<div>   
			<p style="margin-left: 10px !important;margin: 0px;font-family: "Open Sans", sans-serif;font-size: 16px;color: #343333;margin-bottom:7px;font-weight: 600;margin-top: 0px;"> AED'.number_format($orderData['vat_amount'],2).'</p>
			</div>

			</div>';

			if($orderData['payment_mode'] == 'Arabian Points'){
				$html .='<div class="row" style="display: inline-flex;justify-content: space-between;margin: 0px 0px;border-bottom: 1px solid #80808030;">
				<div style=" width: 324px;">
				<p style="margin-left: 10px !important;margin: 0px;font-family: "Open Sans", sans-serif;font-size: 16px;color: #343333;margin-bottom:7px;width: 324px;margin-top: 0px;"> Paid Using Arabian Point</p>
				</div>
				<div>
				<p style="margin-left: 10px !important;margin: 0px;font-family: "Open Sans", sans-serif;font-size: 16px;color: #343333;margin-bottom:7px;font-weight: 600;margin-top: 0px;"> AED '.number_format($orderData['total_price'],2).'</p>
				</div>
				</div>';
			}else{
				$html .='<div class="row" style="display: inline-flex;justify-content: space-between;margin: 0px 0px;border-bottom: 1px solid #80808030;">
				<div style=" width: 324px;">	
				<p style="margin-left: 10px !important;margin: 0px;font-family: "Open Sans", sans-serif;font-size: 16px;color: #343333;margin-bottom:7px;width: 324px;margin-top: 0px;"> Paid Using Card </p>
				</div>
				<div>	
				<p style="margin-left: 10px !important;margin: 0px;font-family: "Open Sans", sans-serif;font-size: 16px;color: #343333;margin-bottom:7px;font-weight: 600;margin-top: 0px;"> AED '.number_format($orderData['total_price'],2).'</p>
				</div>
				</div>';
			}

			$html .='<div class="row" style="display: inline-flex;justify-content: space-between;margin: 0px 0px;border-bottom: 1px solid #80808030;">
				<div style=" width: 324px;">	
				<p style="margin-left: 10px !important;margin: 0px;font-family: "Open Sans", sans-serif;font-size: 16px;color: #343333;margin-bottom:7px;width: 324px;margin-top: 0px;"> Refunded Amount </p>
				</div>
				<div>	
				<p style="margin-left: 10px !important;margin: 0px;font-family: "Open Sans", sans-serif;font-size: 16px;color: #343333;margin-bottom:7px;font-weight: 600;margin-top: 0px;"> AED '.number_format($orderData['total_price'],2).'</p>
				</div>
				</div>';

			if($orderDetails):
				$html .= '</div> </div> </div>';
				$html .= '<div class="main_content" style="margin:0px 0px;">
				<div class="row" >
				<div style="text-align: center;font-family: sans-serif;">
				<h1 style="font-family: sans-serif;;font-size: 18px;color: #343333;">Coupon Information </h1>
				</div>
				<div class="main_content" style="margin: 29px 54px;border: 1px solid #80808061;padding: 4px 16px;border-radius: 5px;">';
			
			foreach($CouponsDetails as $couponData):

				$couponhtml .=	
				'<div class="row" style="display: inline-flex;justify-content: space-between;margin: 0px;border-bottom: 1px solid #80808030;">
					<div style="width:285px">
						<p style="margin-left: 10px !important;margin: 0px;font-family: "Open Sans", sans-serif;font-size: 16px;color: #343333;margin-bottom:7px;"> Product Name</p>
					</div>
				<div>
				<p style="margin-left: 10px !important;margin: 0px;font-family: "Open Sans", sans-serif;font-size: 16px;color: #343333;margin-bottom:7px;font-weight: 600;"> 
				'	.substr(stripslashes($couponData['product_title']),0,15).
				'</p>
				</div>
				</div>

					<div class="row" style="display: inline-flex;justify-content: space-between;margin: 0px;border-bottom: 1px solid #80808030;">
						<div style="width:285px">	
							<p style="margin-left: 10px !important;margin: 0px;font-family: "Open Sans", sans-serif;font-size: 16px;color: #343333;margin-bottom:7px;"> Coupon Code</p>
						</div>
					<div>	
					<p style="margin-left: 10px !important;margin: 0px;font-family: "Open Sans", sans-serif;font-size: 16px;color: #343333;margin-bottom:7px;font-weight: 600;"> '.stripcslashes($couponData['coupon_code']).'</p>
				</div>
				</div>';
				

				

			endforeach;

			$html .= $couponhtml;
					 
			endif;

			$html .= '</div> </div> </div>'; 	

			$html .='<div class="main_content" style="margin: -13px 41px 0px;text-align:center;">
			<div class="row" style="border-bottom: 1px solid #80808030;">
			<div style="text-aling:center;font-family:sans-serif;font-size: 26px; ">
			<p style="color: #343333;font-family: "Open Sans", sans-serif;font-size: 26px; font-weight: 600;text-align: center;font-size: 19px;margin:0px;">Customer Support Service</p>
			</div>
			</div>
			<div class="row" style="display:inline-flex;justify-content: space-around;margin: 24px 0px;">
			<div class="" style="display: flex;flex-direction: row;justify-content: center; align-items: cente;color: #343333;margin-right: 38px;">
			<img src="'.MAIN_URL.'assets/img/phone-solid.jpg" alt="" style="width: 20px;">
			<a href="tel:045541927" style=" margin-left: 10px !important;margin:0px;font-family: sans-serif;font-size: 16px;color: #343333 !important;">045541927</a>
			</div>
			<div class="" style="display:inline-flex;flex-direction: row;justify-content: center; align-items: center;color: #343333;">
			<img src="'.MAIN_URL.'assets/img/envelope-solid.jpg" alt="" style="width: 20px;">
			<a href="mailto:info@dealzarabia.com" style=" margin-left: 10px !important;margin:0px;font-family: sans-serif;font-size: 16px;color: #343333 !important;">info@dealzarabia.com</a>
			</div>
			</div>
			<div class="row" style="display:blockjustify-content:center;margin: 0px 0px;color:#000;">
			<div class="" style="display:inline-flex;flex-direction: row;justify-content: center; align-items: center;">
			<img src="'.MAIN_URL.'assets/img/globe-solid.jpg" alt="" style="width: 20px;height: 10%;">
			<p style="margin:0px;color:#000;!important;line-height: 0px;">  <a href="https://dealzarabia.com/" style=" margin-left: 10px !important;margin:0px;font-family: sans-serif;font-size: 16px;color:#000;">https://dealzarabia.com/</a><p>
			</div>
			</div>
			</div>
			<div class="row" style="text-align: center;">
			<div style="background-color: #d82b2b;color: #fff;font-family: "Open Sans", sans-serif;font-size: 16px; font-weight: 400;text-align: center;width:50%;background-color: #d82b2b;
			color: #fff;
			">
			<p style="margin:0px;padding: 12px 76px;background-color: #d12929;
			color: #fff;">Copyright© '.date('Y').'</p>
			</div>
			</div>
			</body>

			</html>';
			// echo $html;die();
			if($html <> ""):  
				#.............................. message section ............................#
				try {

					if($orderData['user_email']):

						$email = new Mail();
						$email->setFrom(MAIL_FROM_MAIL, MAIL_SITE_FULL_NAME);
						$email->setSubject('Your ticket number ( '.$orderData['order_id'].' ) has been cancelled Successfully');
						$email->addTo($orderData['user_email'], $name[0]);
						$email->addContent("text/html",$html);
						
						$sendgrid = new \SendGrid(SENDGRID_KEY);
						$response = $sendgrid->send($email);
						return true;

					// else:
					// 	throw new Exception('Email id required');
					endif;
					
				} catch (Exception $e) {
					return false;
				}
				
			endif;
			return true;
		endif;
	} // End of function


	/***********************************************************************
	** Function name 	: sendlottoOrderMailToUser
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used to send email.
	** Date 			: 30 January 2024
	************************************************************************/
	function sendlottoOrderMailToUser($oid = '') {  
	  if(!empty($oid)):
		$tblName 			=	'uw_lotto_orders';
		$shortField 		= 	array('_id'=> -1 );
		$whereCon['where']	= 	array('order_id'=>$oid);
		$orderData  		=	$this->common_model->getdata('single', $tblName, $whereCon,$shortField);

		$name   = explode('@',$orderData['user_email']);

		$html  .= '<!DOCTYPE html>
		<html lang="en">
		<head>
			<title>Dealzerbia | mailtemplate</title>
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<style>
			 body { background-color:#1e1e1e;color: #010f19 !important;}
			 a:link, a:active {color: #000 !important;text-decoration: none; }
			 a{color:#000 !important;}
			 @media screen and (min-device-width: 360px) and (max-device-width: 600px) {
				body {background-color: #fff !important;margin: 0px;}
			 }
			 .page-container{ width: 533px;margin: auto;background-color: #fff;padding: 19px 15px 19px;}
			 .text-center{ text-align:center; }

			.my-order-section {width: 85%;
							    margin: auto;
							    border: 1px solid #80808038;
							    border-radius: 6px;
							    padding: 20px;
							   }
			.order-title ,.order-details {
			        width: 50%;
			        font-family: sans-serif;
	    	}
	    	.order-title{
	    		text-align : left;
	    	}
	    	.order-details{
	    		text-align : right;
	    	}

			</style>
		</head>
		<body>
		<div class="page-container">
			<div class="logo-section">
				<div class="text-center">
					<img src="https://dealzarabia.com/assets/img/jpeg_logo.jpg" alt="logo">
				</div>
			</div>
			<div style="margin:39px 0px;text-align:center;">
				<h1> Refund Invoice </h1>
				<p> Hello '.$name[0].', Your Order Details</p>
			</div>
			<table class="my-order-section">
	            <tr>
	                <td class="order-title">Order Id</td>
	                <td class="order-details">'.$orderData['order_id'].'</td>
	            </tr> 
	            <tr>
	                <td class="order-title">Order Date</td>
	                <td class="order-details">'.date("d M, Y",strtotime($orderData['created_at'])).'</td>
	            </tr> 
	        </table>
	        <div style="margin:39px 0px;text-align:center;">
				<h1> Payment Information</h1>
			</div>
			<table class="my-order-section">
	            <tr>
	                <td class="order-title">Inclusice of VAT</td>
	                <td class="order-details">' .'AED '.number_format($orderData['total_price'],2).'</td>
	            </tr> 
	            <tr>
	                <td class="order-title">Subtotal</td>
	                <td class="order-details">' .'AED '.number_format($orderData['total_price'],2).'</td>
	            </tr> 
	            <tr>
	                <td class="order-title">VAT </td>
	                <td class="order-details"> ' .'AED '.number_format($orderData['total_price'],2).'</td>
	            </tr> 
	            <tr>
	                <td class="order-title">Paid Using Arabian Point </td>
	                <td class="order-details"> ' .'AED '.number_format($orderData['total_price'],2).'</td>
	            </tr> 
	            <tr>
	                <td class="order-title">Refunded Amount</td>
	                <td class="order-details"> ' .'AED '.number_format($orderData['total_price'],2).'</td>
	            </tr> 
	        </table>
	        <div class="text-center">
			<div style="text-aling:center;font-family:sans-serif;font-size: 26px; ">
				<p style="color: #343333;font-family: "Open Sans", sans-serif;font-size: 26px; font-weight: 600;text-align: center;font-size: 19px;margin:0px;">Customer Support Service</p>
			</div>
			<div class="row" style="display:inline-flex;justify-content: space-around;margin: 24px 0px;">
			<div class="" style="display: flex;flex-direction: row;justify-content: center; align-items: cente;color: #343333;margin-right: 38px;">
			<img src="'.MAIN_URL.'assets/img/phone-solid.jpg" alt="" style="width: 20px;">
			<a href="tel:045541927" style=" margin-left: 10px !important;margin:0px;font-family: sans-serif;font-size: 16px;color: #343333 !important;">045541927</a>
			</div>
			<div class="" style="display:inline-flex;flex-direction: row;justify-content: center; align-items: center;color: #343333;">
			<img src="'.MAIN_URL.'assets/img/envelope-solid.jpg" alt="" style="width: 20px;">
			<a href="mailto:info@dealzarabia.com" style=" margin-left: 10px !important;margin:0px;font-family: sans-serif;font-size: 16px;color: #343333 !important;">info@dealzarabia.com</a>
			</div>
			</div>
			<div class="row" style="display:blockjustify-content:center;margin: 0px 0px;color:#000;">
			<div class="" style="display:inline-flex;flex-direction: row;justify-content: center; align-items: center;">
			<img src="'.MAIN_URL.'assets/img/globe-solid.jpg" alt="" style="width: 20px;height: 10%;">
			<p style="margin:0px;color:#000;!important;line-height: 0px;">  <a href="https://dealzarabia.com/" style=" margin-left: 10px !important;margin:0px;font-family: sans-serif;font-size: 16px;color:#000;">https://dealzarabia.com/</a><p>
			</div>
			</div>
			</div>
			<div class="row" style="text-align: center;">
			<div style="background-color: #d82b2b;color: #fff;font-family: "Open Sans", sans-serif;font-size: 16px; font-weight: 400;text-align: center;width:50%;background-color: #d82b2b;
			color: #fff;
			">
			<p style="margin:0px;padding: 12px 76px;background-color: #d12929;
			color: #fff;">Copyright© '.date('Y').'</p>
			</div>
			</div>
			</body>

			</html>';
			echo $html;die();
			if($html <> ""):  
				#.............................. message section ............................#
				try {

					if($orderData['user_email']):

						$email = new Mail();
						$email->setFrom(MAIL_FROM_MAIL, MAIL_SITE_FULL_NAME);
						$email->setSubject('Your ticket number ( '.$orderData['order_id'].' ) has been cancelled Successfully');
						$email->addTo($orderData['user_email'], $name[0]);
						$email->addContent("text/html",$html);
						
						$sendgrid = new \SendGrid(SENDGRID_KEY);
						$response = $sendgrid->send($email);
						return true;

					// else:
					// 	throw new Exception('Email id required');
					endif;
					
				} catch (Exception $e) {
					return false;
				}
				
			endif;
			return true;
		endif;
	} // End of function



	public function SendUserList($UserListDetails='')
	{


		 $file =  fileFCPATH.'admin/assets/excel_sheet/'.$UserListDetails;
		 $html = 'All user list are attached below:-';
		
		if($html <> ""):  
				#.............................. message section ............................#
		// try {

				$email = new  \SendGrid\Mail\Mail();
				$email->setFrom(MAIL_FROM_MAIL, MAIL_SITE_FULL_NAME);
				$email->setSubject('User list daily report');
				$email->addTo('operations@dealzarabia.com');
				$email->addContent("text/html",$html);


				$fileContent = file_get_contents($file);
				$fileEncoded = base64_encode($fileContent);

				$attachment = new \SendGrid\Mail\Attachment();
				$attachment->setContent($fileEncoded);
				$attachment->setType('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				$attachment->setFilename($UserListDetails);
				$attachment->setDisposition('attachment');
				$email->addAttachment($attachment);
				
				$sendgrid = new \SendGrid(SENDGRID_KEY);
				$response = $sendgrid->send($email);
				return true;

				// echo "<pre>";
				// print_r($response);

				// return true;	
			// } catch (Exception $e) {
			// 	return false;
			// }
			
		endif;
	}


}	
?>
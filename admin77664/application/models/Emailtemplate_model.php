<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class Emailtemplate_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct(); 
	}
	
	
	/***********************************************************************
	** Function name : get_email_template_by_mail_type
	** Developed By : Tejaswi
	** Purpose  : This is get  email template by mail type.
	** Date : 08 APRIL 2021
	************************************************************************/
	function get_email_template_by_mail_type($type='') { 
	
		$this->mongo_db->select('*');
		$this->mongo_db->where(array('email_template_id'=>$type));
		$result = $this->mongo_db->find_one('uw_email_templates');
		//print_r($result);die;
		if($result):
			return $result;
		else:	
			return false;
		endif;
	}
	
	/***********************************************************************
	** Function name : sendRegisterFormOneMailToUsers
	** Developed By : Dilip Halder
	** Purpose  : This function used for send Register Form Mail To Users
	** Date : 21 JUNE 2021
	************************************************************************/
	function sendForgotpinMailToUser($customerData=array())
	{ //echo '<pre>';print_r($customerData);die;
	    
		if(!empty($customerData['admin_email'])):
			$MTData				=	$this->get_email_template_by_mail_type(100000000000001); 
		//	echo"<pre>";print_r($MTData);die;
			if($MTData <> ""):  
				$fromMail  		= 	MAIL_FROM_MAIL;
				$siteFullName	=	MAIL_SITE_FULL_NAME;
				$toMail  		= 	$customerData['admin_email'];
				$toName  		= 	$customerData['admin_first_name'];
				$subject 		= 	$MTData['subject'];
				
									 
				#.............................. message section ............................#
				$MHData 		= 	stripslashes($MTData['mail_header']);
	
				$MBData 		= 	str_replace('{USERNAME}', $toName, stripslashes($MTData['mail_body']));
				
				$MBData         =   str_replace('{OTP}',$customerData['admin_password_otp'], $MBData);
	
				$MHtml	 		= 	stripslashes($MTData['html']);
				$MHtml	 		= 	str_replace('{MAIL-HEADER}', $MHData, $MHtml);
				$MHtml	 		= 	str_replace('{MAIL-BODY}', $MBData, $MHtml);
				$MHtml	 		= 	str_replace('{MAIL-FOOTER}', $MFData, $MHtml);
				//echo $MHtml; die;
				$this->smtp_mailer($siteFullName,$toMail,$toName,'',$subject,$MHtml,'');
			endif;
			return true;
		endif;
	}	// END OF FUNCTION


	##################################################################################################################################
	####################################	SMTP MAILER SECTION 		##############################################################
	##################################################################################################################################
	/*
		$config = array(
			'protocol'  => 'smtp',
			'smtp_host' => 'mail.jobsenn.com',
			'smtp_port' => 465,
			'smtp_user' => 'noreplytest@jobsenn.com',
			'smtp_pass' => 'Bb}nl.KLHM5Q',
			'mailtype'  => 'html',
			'charset'   => 'iso-8859-1'
		);
	*/

	public function smtp_mailer($sitefullname='',$tomail='',$toname='',$bcc_email='',$subject='',$body='',$attachment='')
	{
	    
		if($_SERVER['SERVER_NAME']=='localhost'){
			return true;
		} else {
			include_once APPPATH . 'third_party/mail/class.phpmailer.php';
			error_reporting(E_ALL ^ E_NOTICE);  

			$mail                = 	new PHPMailer();

			$mail->IsSMTP(); // telling the class to use SMTP
			$mail->Host          = 	"apkconnectlab.com";
			$mail->SMTPAuth      = 	true;                  // enable SMTP authentication
			$mail->SMTPSecure 	 = 	"tls";    
			$mail->SMTPKeepAlive = 	true;                  // SMTP connection will not close after each email sent
			$mail->Port          = 	587;                    // set the SMTP port for the GMAIL server
			$mail->Username      = 	"noreply@apkconnectlab.com"; // SMTP account username
			$mail->Password      = 	")3f7o9*#G5@e";        // SMTP account password
			
			$mail->SetFrom('noreplytest@jobsenn.com', $sitefullname);
			$mail->AddReplyTo('noreplytest@jobsenn.com', $sitefullname);

			$mail->Subject		= 	$subject;

			//$mail->AltBody    	= "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
			
			$mail->MsgHTML($body);
			$mail->AddAddress($tomail, $toname);

			if($bcc_email):
				//$this->email->bcc($bcc_email);
			endif;
			if(is_array($attachment)):
	    		foreach($attachment as $file):
	    		   $mail->AddAttachment(str_replace(base_url(),FCPATH,$file));      // attachment
	    		endforeach;
	    	elseif($attachment != '' && is_string($attachment)):
				$mail->AddAttachment(str_replace(base_url(),FCPATH,$attachment)); // attachment
			endif;

			if($mail->Send()) {	
				return true;
			} else {		
				return false;
			}
			// Clear all addresses and attachments for next loop
			$mail->ClearAddresses();
			$mail->ClearAttachments();
		}
	}
	
	
}	
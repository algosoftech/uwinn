<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Notification_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct(); 
		$this->load->model(array('geneal_model','common_model'));
	}

	/***********************************************************************
	** Function name : sendNotificationToAllUsers
	** Developed By : Ravi Kumar
	** Purpose  : This is get send send Notification To All Users
	** Date : 08 APRIL 2021
	************************************************************************/
	public function sendNotificationToAllUsers($notificationIDs='',$message='',$data=array(),$legencyKey='')
	{
		if(!empty($notificationIDs) && !empty($message) && !empty($data) && !empty($legencyKey)):
			
			$fields 	= 	array('to'=>$notificationIDs,'notification'=>$message,'data'=>$data);
			$headers 	= 	array('Authorization: key='.$legencyKey,'Content-Type:application/json');
				
			#Send Reponse To FireBase Server	
    		$ch = curl_init();
    		curl_setopt( $ch,CURLOPT_URL,'https://fcm.googleapis.com/fcm/send');
    		curl_setopt( $ch,CURLOPT_POST,true);
    		curl_setopt( $ch,CURLOPT_HTTPHEADER,$headers);
    		curl_setopt( $ch,CURLOPT_RETURNTRANSFER,true);
    		curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER,true);
    		curl_setopt( $ch,CURLOPT_POSTFIELDS,json_encode($fields));
    		$result = curl_exec($ch);
    		curl_close($ch);
    		//echo "<pre>";print_r($result);die;
			return $result;
		endif;
	}	// END OF FUNCTION

	/* * *********************************************************************
	 * * Function name : sendNotificationToMultipleUserFunction 
	 * * Developed By : Manoj Kumar
	 * * Purpose  : This function use for send Notification To Multiple User Function
	 * * Date : 26 NOVEMBER 2021
	 * * **********************************************************************/
	public function sendNotificationToMultipleUserFunction($registrationIds='',$message='',$data=array(),$deviceType='') {
		if(!empty($registrationIds) && !empty($message) && !empty($data) && !empty($deviceType)):
			$fields 		= 	array('registration_ids'=>$registrationIds,'notification'=>$message,'data'=>$data,'image'=>$message['image']);
			//if($deviceType == 'Andriod'):
			//	$headers 	= 	array('Authorization: key='.BRAINTRAIN_USER_ANDRIOD_KEY,'Content-Type:application/json');
		//	elseif($deviceType == 'IOS'):
			//	$headers 	= 	array('Authorization: key='.BRAINTRAIN_USER_IOS_KEY,'Content-Type:application/json');
			//endif;

			$legency_key = 'AAAAdIFBqHc:APA91bEvSlUhFNCWCn6M40D_SX8HFFw3WkzvOk_CHw_OFXuE_UuqmODNXvDsDf1arDfkHKoYPBDaZXhlIH6iibb2-qRpylKj-f_TXY-xAmXL5iKvepJ92w8KCSW2TBO-xeArjK_A1Rdd';
			$headers = array('Authorization: key='.$legency_key,'Content-Type: application/json');

			#Send Reponse To FireBase Server 	
			$ch = curl_init();
			curl_setopt( $ch,CURLOPT_URL,'https://fcm.googleapis.com/fcm/send');
			curl_setopt( $ch,CURLOPT_POST,true);
			curl_setopt( $ch,CURLOPT_HTTPHEADER,$headers);
			curl_setopt( $ch,CURLOPT_RETURNTRANSFER,true);
			curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER,false);
			curl_setopt( $ch,CURLOPT_POSTFIELDS,json_encode($fields));
			$result = curl_exec($ch);
			curl_close($ch);
			#Echo Result Of FireBase Server 
			//echo "<pre>"; print_r($result); die;
			return $result;
		endif;
	}	// END OF FUNCTION	

	/***********************************************************************
	** Function name : sendBRConfirmationNotificationToMultipleUser
	** Developed By : Manoj Kumar
	** Purpose  : This is use for send Booking Request Confirmation Notification To Multiple User
	** Date : 26 NOVEMBER 2021
	************************************************************************/
	function sendBRConfirmationNotificationToMultipleUser($registrationIds='',$prodId='',$deviceType='',$title='',$message='',$image = '') { 
		if($registrationIds && $deviceType):
			$message 		= 	array('body'=>$message,
						 	 		  'title'=>$title,
             	         	 		  'image'=>$image,
              	         	 		  'sound'=>'default',
          				 	 		  ); 
			$data			=	array('notification'=>$title,'message'=>$message);
            $fields 	    = 	array('to'=>$registrationIds,'notification'=>$message,'data'=>$data);
			$returnMessage	=	$this->sendNotificationToMultipleUserFunction($registrationIds,$message,$data,$deviceType);
			
			return $returnMessage;
		endif;
	} //END OF FUNCTION
	/***********************************************************************
	** Function name : rceivedArabianPointNotification
	** Developed By : Afsar Ali
	** Purpose  : This is use for received arabian point notification
	** Date : 01 JAN 2023
	************************************************************************/
	public function rceivedArabianPointNotification($data = array()){
		if($data):
			$point		= $data['arabianpoint'];
			$name 		= ucwords(strtolower($data['name']));
			$user_id	= $data['user_id'];
			$device_id	= $data['device_id'];
			$title = "Arabian Points Received.";
			$message = "You have received ".$point." Arabian Points from ".$name." on ".date('Y-m-d H:i').".";

			//Save in notificaton table
			$Nparams['notification_id']		=	(int)$this->common_model->getNextSequence('uw_notifications');
			$Nparams['notific_title']		=	$title;
			$Nparams['notific_message']		=	$message;
			$Nparams['creation_ip']			=	$this->input->ip_address();
			$Nparams['creation_date']		=	strtotime(date('Y-m-d H:i'));
			$Nparams['created_by']			=	(int)$user_id;
			$Nparams['status']				=	'A';
			$insert_id = $this->common_model->addData('uw_notifications', $Nparams);
			//END

			//Save in notificaton details table
			$NDparams['notification_details_id']	=	(int)$this->common_model->getNextSequence('uw_notifications_details');
			$NDparams['users_id']					=	(int)$user_id;
			$NDparams['notification_id']			=	(int)$insert_id;
			$NDparams['notific_title']				=	$title;
			$NDparams['notific_message']			=	$message;
			$NDparams['is_read']					=	"N";
			$NDparams['sound']						=	"Coin";
			$NDparams['creation_ip']				=	$this->input->ip_address();
			$NDparams['creation_date']				=	strtotime(date('Y-m-d H:i'));
			$NDparams['created_by']					=	(int)$this->session->userdata('UW_ADMIN_ID');
			$NDparams['status']						=	'A';
			$this->common_model->addData('uw_notifications_details', $NDparams);
			//END

			$device_id = array($device_id);

			$legency_key 			= 	'AAAAdIFBqHc:APA91bEvSlUhFNCWCn6M40D_SX8HFFw3WkzvOk_CHw_OFXuE_UuqmODNXvDsDf1arDfkHKoYPBDaZXhlIH6iibb2-qRpylKj-f_TXY-xAmXL5iKvepJ92w8KCSW2TBO-xeArjK_A1Rdd';
			$deviceType 			= 	'Andriod';

			$message 		= 	array('body'=>$message,
										'title'=>$title,
										'icon'=>'myIcon',
										'sound'=>'mySound',
										"android_channel_id"=>"Dealz_admin_channel",
										'badge'=>1
										); 
			$data			=	array('notification'=>$title,'message'=>$message, 'android_channel_id' => 'Dealz_admin_channel');

			$response   			=  $this->notification_model->sendNotificationToMultipleUserFunction($device_id,$message,$data,$deviceType); 
		endif;
	} // End of function
}	
<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');

class Notification_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct(); 
		error_reporting(0);
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
			
			$fields 	= 	array('to'=>$notificationIDs,'notification'=>$message,'data'=>$data,'image'=>$message['image']);
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
			$fields 		= 	array('registration_ids'=>$registrationIds,'notification'=>$message,'data'=>$data);
			$legency_key = 'AAAA8ZhMvTA:APA91bEpo8br1w4lkZ_ND-DyVVfz40wwYBUJKEXydUF4W6FS4nHKCxIJ2JR6gN3UeEEYp7oEYwYUJF6gTNNqeruW9ujIXeBHxmaqz5jgflAfWWMDH2TwXuV_QI2t2iNkfrfwmock5jQL';
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
			return $result;
		endif;
	}	// END OF FUNCTION	

	/***********************************************************************
	** Function name : sendBRConfirmationNotificationToMultipleUser
	** Developed By : Manoj Kumar
	** Purpose  : This is use for send Booking Request Confirmation Notification To Multiple User
	** Date : 26 NOVEMBER 2021
	************************************************************************/
	function sendBRConfirmationNotificationToMultipleUser($registrationIds='',$prodId='',$deviceType='',$title='',$message='',$image='') { 
		if($registrationIds && $deviceType):
			$message 		= 	array('body'=>$message,
						 	 		  'title'=>$title,
             	         	 		  'icon'=>$image,
              	         	 		  'sound'=>'mySound'
          				 	 		  ); 
	
			$data			=	array('notification'=>$title,'message'=>$message);
            $fields 	    = 	array('to'=>$registrationIds,'notification'=>$message,'data'=>$data);
			$returnMessage	=	$this->sendNotificationToMultipleUserFunction($registrationIds,$message,$data,$deviceType);
			return $returnMessage;
		endif;
	}//
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
			$Nparams['notification_id']		=	(int)$this->geneal_model->getNextSequence('uw_notifications');
			$Nparams['notific_title']		=	$title;
			$Nparams['notific_message']		=	$message;
			$Nparams['creation_ip']			=	$this->input->ip_address();
			$Nparams['creation_date']		=	strtotime(date('Y-m-d H:i'));
			$Nparams['created_by']			=	(int)$user_id;
			$Nparams['status']				=	'A';
			$insert_id = $this->geneal_model->addData('uw_notifications', $Nparams);
			//END

			//Save in notificaton details table
			$NDparams['notification_details_id']	=	(int)$this->geneal_model->getNextSequence('uw_notifications_details');
			$NDparams['users_id']					=	(int)$user_id;
			$NDparams['notification_id']			=	(int)$insert_id;
			$NDparams['notific_title']				=	$title;
			$NDparams['notific_message']			=	$message;
			$NDparams['is_read']					=	"N";
			$NDparams['sound']						=	"Coin";
			$NDparams['creation_ip']				=	$this->input->ip_address();
			$NDparams['creation_date']				=	strtotime(date('Y-m-d H:i'));
			$NDparams['created_by']					=	(int)$this->session->userdata('DZL_USERID');
			$NDparams['status']						=	'A';
			$this->geneal_model->addData('uw_notifications_details', $NDparams);
			//END
			//Unread Notification count
			$where['where']			=	array(
				'users_id' => (int)$user_id,
				'is_read'	=> 'N'
				);
			$badge = $this->geneal_model->getData2('count','uw_notifications_details',$where);
			//END
			$device_id = array($device_id);

			$legency_key 			= 	'AAAA8ZhMvTA:APA91bEpo8br1w4lkZ_ND-DyVVfz40wwYBUJKEXydUF4W6FS4nHKCxIJ2JR6gN3UeEEYp7oEYwYUJF6gTNNqeruW9ujIXeBHxmaqz5jgflAfWWMDH2TwXuV_QI2t2iNkfrfwmock5jQL';
			$deviceType 			= 	'Andriod';

			$message 		= 	array('body'=>$message,
										'title'=>$title,
										'icon'=>'myIcon',
										'sound'=>'coin_sound',
										'android_channel_id' => 'Dealz_admin_channel',
										'badge'=>$badge
										); 


			$data			=	array('notification'=>$title,'message'=>$message,'android_channel_id'=>'Dealz_admin_channel');

			$response   			=  $this->notification_model->sendNotificationToMultipleUserFunction($device_id,$message,$data,$deviceType); 
		endif;
	} // End of function

	/***********************************************************************
	** Function name : sentArabianPointNotification
	** Developed By : Afsar Ali
	** Purpose  : This is use for sent arabian point notification
	** Date : 01 JAN 2023
	************************************************************************/
	public function sentArabianPointNotification($data = array()){
		if($data):
			$point		= $data['arabianpoint'];
			$name 		= ucwords(strtolower($data['name']));
			$user_id	= $data['user_id'];
			$device_id	= $data['device_id'];
			$title = "DealzArabia Debit Notification.";
			$message = "You have transferred ".$point." Arabian Points to ".$name." on ".date('Y-m-d H:i').".";

			//Save in notificaton table
			$Nparams['notification_id']		=	(int)$this->geneal_model->getNextSequence('uw_notifications');
			$Nparams['notific_title']		=	$title;
			$Nparams['notific_message']		=	$message;
			$Nparams['creation_ip']			=	$this->input->ip_address();
			$Nparams['creation_date']		=	strtotime(date('Y-m-d H:i'));
			$Nparams['created_by']			=	(int)$user_id;
			$Nparams['status']				=	'A';
			$insert_id = $this->geneal_model->addData('uw_notifications', $Nparams);
			//END

			//Save in notificaton details table
			$NDparams['notification_details_id']	=	(int)$this->geneal_model->getNextSequence('uw_notifications_details');
			$NDparams['users_id']					=	(int)$this->session->userdata('DZL_USERID');
			$NDparams['notification_id']			=	(int)$insert_id;
			$NDparams['notific_title']				=	$title;
			$NDparams['notific_message']			=	$message;
			$NDparams['is_read']					=	"N";
			$NDparams['sound']						=	"Coin";
			$NDparams['creation_ip']				=	$this->input->ip_address();
			$NDparams['creation_date']				=	strtotime(date('Y-m-d H:i'));
			$NDparams['created_by']					=	(int)$this->session->userdata('DZL_USERID');
			$NDparams['status']						=	'A';
			$this->geneal_model->addData('uw_notifications_details', $NDparams);
			//END
			//Unread Notification count
			$where['where']			=	array(
				'users_id' => (int)$user_id,
				'is_read'	=> 'N'
				);
			$badge = $this->geneal_model->getData2('count','uw_notifications_details',$where);
			//END
			$device_id = array($device_id);

			$legency_key 			= 	'AAAA8ZhMvTA:APA91bEpo8br1w4lkZ_ND-DyVVfz40wwYBUJKEXydUF4W6FS4nHKCxIJ2JR6gN3UeEEYp7oEYwYUJF6gTNNqeruW9ujIXeBHxmaqz5jgflAfWWMDH2TwXuV_QI2t2iNkfrfwmock5jQL';
			$deviceType 			= 	'Andriod';

			$message 		= 	array('body'=>$message,
										'title'=>$title,
										'icon'=>'myIcon',
										'sound'=>'coin_sound',
										'android_channel_id' => 'Dealz_admin_channel',
										'badge'=>$badge
										); 

			$data			=	array('notification'=>$title,'message'=>$message,'android_channel_id'=>'Dealz_admin_channel');

			$response   			=  $this->notification_model->sendNotificationToMultipleUserFunction($device_id,$message,$data,$deviceType); 
		endif;
	} // End of function
	/***********************************************************************
	** Function name 	: sentSignupBonusNotification
	** Developed By 	: Afsar Ali
	** Purpose  		: This is use for sent singup bonus notification
	** Date 			: 17 JAN 2023
	************************************************************************/
	public function sentSignupBonusNotification($data = array()){
		if($data):
			$point		= 2;
			$name 		= ucwords(strtolower($data['name']));
			$user_id	= $data['user_id'];
			$device_id	= $data['device_id'];
			$title = "Buy & Win Amazing Prize";
			$message = "Your  have  earned ".$point." AP as a sign up bonus on ".date('Y-m-d H:i').".";
			 
			//Save in notificaton table
			$Nparams['notification_id']		=	(int)$this->geneal_model->getNextSequence('uw_notifications');
			$Nparams['notific_title']		=	$title;
			$Nparams['notific_message']		=	$message;
			$Nparams['creation_ip']			=	$this->input->ip_address();
			$Nparams['creation_date']		=	strtotime(date('Y-m-d H:i'));
			$Nparams['created_by']			=	(int)$user_id;
			$Nparams['status']				=	'A';
			$insert_id = $this->geneal_model->addData('uw_notifications', $Nparams);
			//END

			//Save in notificaton details table
			$NDparams['notification_details_id']	=	(int)$this->geneal_model->getNextSequence('uw_notifications_details');
			$NDparams['users_id']					=	(int)$user_id;
			$NDparams['notification_id']			=	(int)$insert_id;
			$NDparams['notific_title']				=	$title;
			$NDparams['notific_message']			=	$message;
			$NDparams['is_read']					=	"N";
			$NDparams['sound']						=	"Coin";
			$NDparams['creation_ip']				=	$this->input->ip_address();
			$NDparams['creation_date']				=	strtotime(date('Y-m-d H:i'));
			$NDparams['created_by']					=	(int)$user_id;
			$NDparams['status']						=	'A';
			$this->geneal_model->addData('uw_notifications_details', $NDparams);
			//END
			//Unread Notification count
			$where['where']			=	array(
				'users_id' => (int)$user_id,
				'is_read'	=> 'N'
				);
			$badge = $this->geneal_model->getData2('count','uw_notifications_details',$where);
			//END
			$device_id = array($device_id);

			$legency_key 			= 	'AAAA8ZhMvTA:APA91bEpo8br1w4lkZ_ND-DyVVfz40wwYBUJKEXydUF4W6FS4nHKCxIJ2JR6gN3UeEEYp7oEYwYUJF6gTNNqeruW9ujIXeBHxmaqz5jgflAfWWMDH2TwXuV_QI2t2iNkfrfwmock5jQL';
			$deviceType 			= 	'Andriod';

			$message 		= 	array(	'body'=>$message,
										'title'=>$title,
										'image'=>'https://dealzarabia.com//assets/AP-GREEN.png',
										'sound'=>'coin_sound',
										'android_channel_id' => 'Dealz_admin_channel',
										'badge'=>$badge
										); 

			$data			=	array('notification'=>$title,'message'=>$message,'android_channel_id'=>'Dealz_admin_channel');

			$response   			=  $this->notification_model->sendNotificationToMultipleUserFunction($device_id,$message,$data,$deviceType); 
		endif;
	} // End of function
}	
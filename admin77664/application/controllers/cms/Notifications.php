<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Firebase\JWT\JWT;
use GuzzleHttp\Client;

class Notifications extends CI_Controller {

	private $serviceAccountFile =  APPPATH . 'libraries/service-account.json';
    private $messagingScope 	= 'https://www.googleapis.com/auth/firebase.messaging';
    private $projectId      	= '<YOUR-PROJECT-ID>';
    private $host             	= 'https://fcm.googleapis.com/v1/projects/';


	public function  __construct() 
	{ 
		parent:: __construct();
		// error_reporting(E_ALL ^ E_NOTICE);
		error_reporting(E_ERROR | E_PARSE);
		
  		
		$this->load->model(array('admin_model','emailtemplate_model','sms_model','notification_model'));
		$this->lang->load('statictext', 'admin');
		$this->load->helper(['common','firebase']);

	} 

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name 	: index
	 + + Developed By 	: Manoj Kumar
	 + + Purpose  		: This function used for index
	 + + Date 			: 31 MARCH 2022
	 + + Updated Date 	: 
	 + + Updated By   	: 
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index()
	{	
		
		$this->admin_model->authCheck();
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'cms/notifications';
		$data['activeSubMenu'] 				= 	'notifications';
		
		if($this->input->get('searchField') && $this->input->get('searchValue')):
			$sField							=	$this->input->get('searchField');
			$sValue							=	$this->input->get('searchValue');
			$whereCon['like']			 	= 	array('0'=>trim($sField),'1'=>trim($sValue));
			$data['searchField'] 			= 	$sField;
			$data['searchValue'] 			= 	$sValue;
		else:
			$whereCon['like']		 		= 	"";
			$data['searchField'] 			= 	'';
			$data['searchValue'] 			= 	'';
		endif;
				
		$whereCon['where']		 			= 	"";		
		$shortField 						= 	array('_id'=>-1);
		
		$baseUrl 							= 	getCurrentControllerPath('index');
		$this->session->set_userdata('TGPNOTIFICATIONDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 	'uw_notifications';
		$con 								= 	'';
		$totalRows 							= 	$this->common_model->getData('count',$tblName,$whereCon,$shortField,'0','0');
		
		if($this->input->get('showLength') == 'All'):
			$perPage	 					= 	$totalRows;
			$data['perpage'] 				= 	$this->input->get('showLength');  
		elseif($this->input->get('showLength')):
			$perPage	 					= 	$this->input->get('showLength'); 
			$data['perpage'] 				= 	$this->input->get('showLength'); 
		else:
			$perPage	 					= 	SHOW_NO_OF_DATA;
			$data['perpage'] 				= 	SHOW_NO_OF_DATA; 
		endif;
		$uriSegment 						= 	getUrlSegment();
	    $data['PAGINATION']					=	adminPagination($baseUrl,$suffix,$totalRows,$perPage,$uriSegment);
	    
       if ($this->uri->segment(getUrlSegment())):
           $page = $this->uri->segment(getUrlSegment());
       else:
           $page = 0;
       endif;
		
		$data['forAction'] 					= 	$baseUrl; 
		if($totalRows):
			$first							=	(int)($page)+1;
			$data['first']					=	$first;
			$last							=	((int)($page)+$data['perpage'])>$totalRows?$totalRows:((int)($page)+$data['perpage']);
			$data['noOfContent']			=	'Showing '.$first.'-'.$last.' of '.$totalRows.' items';
		else:
			$data['first']					=	1;
			$data['noOfContent']			=	'';
		endif;
		
		$data['ALLDATA'] 					= 	$this->common_model->getData('multiple',$tblName,$whereCon,$shortField,$perPage,$page);
		$this->layouts->set_title('Notification | CMS | Dealz Arabia');
		$this->layouts->admin_view('cms/notifications/index',array(),$data);
	}	// END OF FUNCTION


	public function test($value='')
	{
		$serviceAccount = json_decode(file_get_contents($this->serviceAccountFile), true);
		if (!$serviceAccount) {
            throw new Exception('Service account file not found.');
        } 


        $now = time();
        $token = [
            'iss' => $serviceAccount['client_email'],
            'scope' => $this->messagingScope,
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
        ];

        $jwt = JWT::encode($token, $serviceAccount['private_key'], 'RS256');


        echo "<pre>";
        print_r($jwt);
        die();
	}

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name : addeditdata
	 + + Developed By  : Manoj Kumar
	 + + Purpose  	   : This function used for Add Edit data
	 + + Date 		   : 31 MARCH 2022
	 + + Updated Date  : 
	 + + Updated By    :
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function addeditdata($editId='')
	{	 
		
		$data['error'] 				= 	'';
		$data['activeMenu'] 		= 	'cms/notifications';
		$data['activeSubMenu'] 		= 	'notifications';
		
		if($editId):
			$this->admin_model->authCheck('edit_data');
			$data['EDITDATA']		=	$this->common_model->getDataByParticularField('uw_notifications','notification_temp_id',(int)$editId);
		else:
			$this->admin_model->authCheck('add_data');
		endif;

		$UwhereCon['where']		 	= 	array("status"=>'A');		
		$UshortField 				= 	array('users_name'=>'ASC');
		$data['usersdata'] 			= 	$this->common_model->getDataByNewQuery(array('users_id','users_name','users_email','users_mobile','email_notification','notification','sms_notification'),'multiple','uw_users',$UwhereCon,$UshortField,'0','0');
		
		if($this->input->post('SaveChanges')): 
			$error					=	'NO';
			$error							=	'NO';
			$data['formError'] 				= 	'Yes';
			$this->form_validation->set_rules('notific_type', 'notification type', 'trim|required');
			if($this->input->post('notific_type') != 'all-users'):
				$this->form_validation->set_rules('student_id[]', 'Student', 'trim|required');
			endif;
			$this->form_validation->set_rules('notific_title', 'notification title', 'trim|required');
			$this->form_validation->set_rules('notific_message', 'message', 'trim|required');
			// if($_FILES['image']['name'] == ''):
			// 	$this->form_validation->set_rules('image', 'Image', 'required');
			// endif;

			if($this->form_validation->run() && $error == 'NO'):  
			
				$param['notification_id']			=	(int)$this->common_model->getNextSequence('uw_notifications');
				$param['notific_title']				= 	stripslashes($this->input->post('notific_title'));
				$param['notific_message']			= 	stripslashes($this->input->post('notific_message'));

				if($_FILES['image']['name']):
					$ufileName						= 	$_FILES['image']['name'];
					$utmpName						= 	$_FILES['image']['tmp_name'];
					$ufileExt         				= 	pathinfo($ufileName);
					$unewFileName 					= 	$this->common_model->microseconds().'.'.$ufileExt['extension'];
					$this->load->library("upload_crop_img");
					$uimageLink						=	$this->upload_crop_img->_upload_image($ufileName,$utmpName,'notifications',$unewFileName,'');
					if($uimageLink != 'UPLODEERROR'):
						$param['image']		= $uimageLink;
						$img_path           = $uimageLink;
					else:
						$param['image']		= 	'';
					endif;
				endif;
				
				if($this->input->post('notific_type') == 'all-users'):
					$whereCon['where_ne'][0]     	=  'device_id';
					$whereCon['where_ne'][1]    	=  "";
				else:
					if($this->input->post('student_id')):
						$selectStudentIds  			=	array();
						foreach($this->input->post('student_id') as $student_id):
							array_push($selectStudentIds,(int)$student_id);
						endforeach;
					else:
						$selectStudentIds  			=	array();
					endif;
					$whereCon['where_in']     		=  array(array('users_id',$selectStudentIds));
					$whereCon['where_ne'][0]     	=  'device_id';
					$whereCon['where_ne'][1]    	=  '';
				endif;
				$deviceId 							= 	$this->common_model->getDataByNewQuery(array('users_id','device_id'),'multiple','uw_users',$whereCon,'','0','0');
				if($deviceId <>''):  
					
					$param['creation_ip']			=	currentIp();
					$param['creation_date']			=	(int)$this->timezone->utc_time();//currentDateTime();
					$param['created_by']			=	(int)$this->session->userdata('HCAP_ADMIN_ID');
					$param['status']				=	'A';
					$alastInsertId					=	$this->common_model->addData('uw_notifications',$param);
					
					$i = 1;  $j=0;	$notification_Id_array 	=	array();
					foreach($deviceId as $devid): 
						array_push($notification_Id_array,$devid['device_id']);

						$detailParam['notification_details_id']	=	(int)$this->common_model->getNextSequence('uw_notifications_details');
						$detailParam['users_id']				=	$devid['users_id'];
						$detailParam['notification_id']			=	$param['notification_id'];
						$detailParam['notific_title']			= 	stripslashes($this->input->post('notific_title'));
						$detailParam['notific_message']			= 	stripslashes($this->input->post('notific_message'));
						$detailParam['link']					= 	$this->input->post('link');
						$detailParam['image']					= 	$param['image'];
						$detailParam['is_read']					= 	'N';

						$detailParam['creation_ip']				=	currentIp();
						$detailParam['creation_date']			=	(int)$this->timezone->utc_time();//currentDateTime();
						$detailParam['created_by']				=	(int)$this->session->userdata('HCAP_ADMIN_ID');
						$detailParam['status']					=	'A';
						$detailParam['push_status']					=	0;
						$this->common_model->addData('uw_notifications_details',$detailParam);
					$i++;
					endforeach;

					// if($notification_Id_array):
						
					// 	// $res = sendNotification($this->input->post('notific_title'),$this->input->post('notific_message'), $notification_Id_array,$img_path);
					// 	// if($res == false){
					// 	// 	$this->session->set_flashdata('alert_error','Unable to send notification');
					// 	// }
					// 	// $notification_Ids_array  	=	array_chunk($notification_Id_array, 500);
					// 	// if($notification_Ids_array):
					// 	// 	foreach($notification_Ids_array as $notification_Ids):
					// 	// 		$legency_key 			= 	'AAAAMa0rZsg:APA91bH1OhWfs7PXQX6MaApB3CqBjqETD3mdBxYYwtQ_i3bQYS2X-iJVLWgc5lY-wbeuB3-cwntDzz-D5M3z_qlH_AAO4Z8RSJ2ILviFovrdLhnO26i852DUJR0yj5yM2HoZJ5m36qPl';
					// 	// 		//$legency_key 			= 	'AAAAMa0rZsg:APA91bH1OhWfs7PXQX6MaApB3CqBjqETD3mdBxYYwtQ_i3bQYS2X-iJVLWgc5lY-wbeuB3-cwntDzz-D5M3z_qlH_AAO4Z8RSJ2ILviFovrdLhnO26i852DUJR0yj5yM2HoZJ5m36qPl';
					// 	//         $deviceType 			= 	'Andriod';
					// 	// 		$img_path 				=	fileBaseUrl.$param['image'];
								
					// 	// 		$response   			=  $this->notification_model->sendBRConfirmationNotificationToMultipleUser($notification_Ids,$legency_key,$deviceType,$this->input->post('notific_title'),$this->input->post('notific_message'),$img_path); 
					// 	// 	endforeach;
					// 	// endif;

					// endif;
					$this->session->set_flashdata('alert_success',lang('addsuccess'));
				else:
					$this->session->set_flashdata('alert_error','No data Found');
				endif;
				
				$this->session->set_flashdata('alert_success','Notifications Sent');
				redirect(correctLink('TGPNOTIFICATIONDATA',$this->session->userdata('HCAP_ADMIN_CURRENT_PATH').$this->router->fetch_class().'/index'));
			endif;
		endif;
		
		//echo '<pre>';  print_r($data['notific_type']); die;
		$this->layouts->set_title('Add/Edit Notification | CMS | Dealz Arabia');
		$this->layouts->admin_view('cms/notifications/addeditdata',array(),$data);

	}	// END OF FUNCTION	
	
	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name 	: 	changestatus
	 + + Developed By 	: 	Manoj Kumar
	 + + Purpose  		: 	This function used for change status
	 + + Date 			: 	31 MARCH 2022
	 + + Updated Date 	:  
	 + + Updated By   	:	  
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	function changestatus($changeStatusId='',$statusType='')
	{  
		$this->admin_model->authCheck('edit_data');
		$param['status']		=	$statusType;
		$this->common_model->editData('uw_notifications',$param,'notification_temp_id',(int)$changeStatusId);
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		
		redirect(correctLink('TGPNOTIFICATIONDATA',$this->session->userdata('HCAP_ADMIN_CURRENT_PATH').$this->router->fetch_class().'/index'));
	}

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name 	: deletedata
	 + + Developed By 	: Manoj Kumar
	 + + Purpose  		: This function used for delete data
	 + + Date 			: 31 MARCH 2022
	 + + Updated Date 	:  
	 + + Updated By   	:	  
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	function deletedata($deleteId='') 
	{  
		$this->admin_model->authCheck('delete_data');
		$this->common_model->deleteData('uw_notifications','notification_temp_id',(int)$deleteId);
		$this->session->set_flashdata('alert_success',lang('deletesuccess'));
		
		redirect(correctLink('TGPNOTIFICATIONDATA',$this->session->userdata('HCAP_ADMIN_CURRENT_PATH').$this->router->fetch_class().'/index'));
	}

	/***********************************************************************
	** Function name 	: ImageDelete
	** Developed By 	: Manoj Kumar
	** Purpose  		: This function used to delete image
	** Date 			: 31 MRACH 2022
	** Updated 			: 
	************************************************************************/
	function ImageDelete()
	{  
		$imageName			=	$this->input->post('imageName');
		$id 					=	$this->input->post('id');
		//echo $id;die;
		$param['image']		=	''; 
		if($imageName):
			$this->load->library("upload_crop_img");
			$return	=	$this->upload_crop_img->_delete_image(trim($imageName)); 
			$this->common_model->editData('uw_notifications',$param,'notification_temp_id',(int)$id);
		endif;
		$returnArray  		= 	array('status'=>1,'message'=>'Image deleted.');
		header('Content-type: application/json');
		echo json_encode($returnArray); die;
	}	// END OF FUNCTION
	public function getnotificationuser(){
		$notification_id = $this->input->post('notification_id');
		$ntype = $this->input->post('type');
		$wcon['where'] =['notification_id' => (int)$notification_id,'is_read' => $ntype];
		// $wcon['where'] =[];
		$wcon['populate'] =[ 
			[
			'field' => 'users_id',  
			'from' => 'uw_users',      
			'localField' => 'users_id', 
			'foreignField' => 'users_id',
			// 'as' => 'user_info'    
			]
		];
		
		$results =	$this->common_model->getData('multiple','uw_notifications_details',$wcon);
		
		$html = '<ul class="list-group">';
		if (!empty($results)) {
			foreach ($results as $row) {
				$name = isset($row['users_id'][0]->users_name) ? $row['users_id'][0]->users_name : 'N/A';
				$html .= "<li class='list-group-item d-flex justify-content-between'>{$name} </li>";
			}
		} else {
			$html .= '<li class="list-group-item">No users found.</li>';
		}
		$html .= '</ul>';
	
		echo json_encode([
				'status' => 'success',
			'data' => $html
		]);die();
	}	
}
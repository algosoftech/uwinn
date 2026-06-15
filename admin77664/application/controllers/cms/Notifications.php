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
	// public function index()
	// {	
		
	// 	$this->admin_model->authCheck();
	// 	$data['error'] 						= 	'';
	// 	$data['activeMenu'] 				= 	'cms/notifications';
	// 	$data['activeSubMenu'] 				= 	'notifications';
		
	// 	if($this->input->get('searchField') && $this->input->get('searchValue')):
	// 		$sField							=	$this->input->get('searchField');
	// 		$sValue							=	$this->input->get('searchValue');
	// 		$whereCon['like']			 	= 	array('0'=>trim($sField),'1'=>trim($sValue));
	// 		$data['searchField'] 			= 	$sField;
	// 		$data['searchValue'] 			= 	$sValue;
	// 	else:
	// 		$whereCon['like']		 		= 	"";
	// 		$data['searchField'] 			= 	'';
	// 		$data['searchValue'] 			= 	'';
	// 	endif;
				
	// 	$whereCon['where']		 			= 	"";		
	// 	$shortField 						= 	array('_id'=>-1);
		
	// 	$baseUrl 							= 	getCurrentControllerPath('index');
	// 	$this->session->set_userdata('TGPNOTIFICATIONDATA',currentFullUrl());
	// 	$qStringdata						=	explode('?',currentFullUrl());
	// 	$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
	// 	$tblName 							= 	'uw_notifications';
	// 	$con 								= 	'';
	// 	$totalRows 							= 	$this->common_model->getData('count',$tblName,$whereCon,$shortField,'0','0');
		
	// 	if($this->input->get('showLength') == 'All'):
	// 		$perPage	 					= 	$totalRows;
	// 		$data['perpage'] 				= 	$this->input->get('showLength');  
	// 	elseif($this->input->get('showLength')):
	// 		$perPage	 					= 	$this->input->get('showLength'); 
	// 		$data['perpage'] 				= 	$this->input->get('showLength'); 
	// 	else:
	// 		$perPage	 					= 	SHOW_NO_OF_DATA;
	// 		$data['perpage'] 				= 	SHOW_NO_OF_DATA; 
	// 	endif;
	// 	$uriSegment 						= 	getUrlSegment();
	//     $data['PAGINATION']					=	adminPagination($baseUrl,$suffix,$totalRows,$perPage,$uriSegment);
	    
    //    if ($this->uri->segment(getUrlSegment())):
    //        $page = $this->uri->segment(getUrlSegment());
    //    else:
    //        $page = 0;
    //    endif;
		
	// 	$data['forAction'] 					= 	$baseUrl; 
	// 	if($totalRows):
	// 		$first							=	(int)($page)+1;
	// 		$data['first']					=	$first;
	// 		$last							=	((int)($page)+$data['perpage'])>$totalRows?$totalRows:((int)($page)+$data['perpage']);
	// 		$data['noOfContent']			=	'Showing '.$first.'-'.$last.' of '.$totalRows.' items';
	// 	else:
	// 		$data['first']					=	1;
	// 		$data['noOfContent']			=	'';
	// 	endif;
		
	// 	$data['ALLDATA'] 					= 	$this->common_model->getData('multiple',$tblName,$whereCon,$shortField,$perPage,$page);
	// 	$this->layouts->set_title('Notification | CMS | Dealz Arabia');
	// 	$this->layouts->admin_view('cms/notifications/index',array(),$data);
	// }	// END OF FUNCTION

public function index()
		
		{
			// $this->admin_model->authCheck();

			$data['error'] = '';
			$data['activeMenu'] = 'cms/notifications';
			$data['activeSubMenu'] = 'notifications';

			// ----------------- SEARCH FILTER -----------------
			if ($this->input->get('searchField') && $this->input->get('searchValue')) {
				$sField = $this->input->get('searchField');
				$sValue = $this->input->get('searchValue');
				$whereCon['like'] = array('0' => trim($sField), '1' => trim($sValue));
				$data['searchField'] = $sField;
				$data['searchValue'] = $sValue;
			} else {
				$whereCon['like'] = "";
				$data['searchField'] = '';
				$data['searchValue'] = '';
			}

			$whereCon['where'] = "";
			if ($this->input->get('notification_type')) {
				if ($this->input->get('notification_type') !='All') {
					$whereCon['where'] = ['broadcast_type' => $this->input->get('notification_type')];
				}
				$data['notification_type'] 	= $this->input->get('notification_type');
			}else{
				$data['notification_type'] 	= 'All';
			}
			$shortField = array('_id' => -1);
			$tblName = 'uw_notifications';

			$baseUrl = getCurrentControllerPath('index');
			$this->session->set_userdata('TGPNOTIFICATIONDATA', currentFullUrl());
			$qStringdata = explode('?', currentFullUrl());
			$suffix = $qStringdata[1] ? '?' . $qStringdata[1] : '';

			// ----------------- PAGINATION -----------------
			$totalRows = $this->common_model->getData('count', $tblName, $whereCon, $shortField, '0', '0');

			if ($this->input->get('showLength') == 'All') {
				$perPage = $totalRows;
				$data['perpage'] = $this->input->get('showLength');
			} elseif ($this->input->get('showLength')) {
				$perPage = $this->input->get('showLength');
				$data['perpage'] = $this->input->get('showLength');
			} else {
				$perPage = SHOW_NO_OF_DATA;
				$data['perpage'] = SHOW_NO_OF_DATA;
			}

			$uriSegment = getUrlSegment();
			$data['PAGINATION'] = adminPagination($baseUrl, $suffix, $totalRows, $perPage, $uriSegment);

			$page = $this->uri->segment(getUrlSegment()) ?: 0;

			$data['forAction'] = $baseUrl;

			if ($totalRows) {
				$first = (int)$page + 1;
				$data['first'] = $first;
				$last = (($first - 1) + $data['perpage']) > $totalRows ? $totalRows : (($first - 1) + $data['perpage']);
				$data['noOfContent'] = 'Showing ' . $first . '-' . $last . ' of ' . $totalRows . ' items';
			} else {
				$data['first'] = 1;
				$data['noOfContent'] = '';
			}

			// ----------------- MAIN DATA FETCH -----------------
			$ALLDATA = $this->common_model->getData('multiple', $tblName, $whereCon, $shortField, $perPage, $page);
			$data['ALLDATA'] = [];

			if (!empty($ALLDATA)) {
				$notificationIds = array_column($ALLDATA, 'notification_id');

				// ----------------- AGGREGATION FOR READ/UNREAD -----------------
				$pipeline = [
					[
						'$match' => [
							'notification_id' => ['$in' => $notificationIds]
						]
					],
					[
						'$group' => [
							'_id' => [
								'notification_id' => '$notification_id',
								'is_read' => '$is_read'
							],
							'count' => ['$sum' => 1]
						]
					]
				];

				$aggregationResults = $this->mongo_db->aggregate('uw_notifications_details', $pipeline,['batchSize' => 4]);

				// ----------------- MAPPING READ/UNREAD COUNTS -----------------
				$readStatusMap = [];
				foreach ($aggregationResults as $res) {
					$res = (array)$res;
					$res['_id'] = (array)$res['_id'];

					$nid = $res['_id']['notification_id'];
					$isRead = $res['_id']['is_read'];
					$readStatusMap[$nid][$isRead] = $res['count'];
				}

				// ----------------- FINAL DATA BUILD -----------------
				foreach ($ALLDATA as $item) {
					$nid = $item['notification_id'];
					$item['read_count'] = isset($readStatusMap[$nid]['Y']) ? $readStatusMap[$nid]['Y'] : 0;
					$item['unread_count'] = isset($readStatusMap[$nid]['N']) ? $readStatusMap[$nid]['N'] : 0;
					$data['ALLDATA'][] = $item;
				}
			}
			// echo"<pre>";print_r($data);die();
			$this->layouts->set_title('Notification | CMS | Dealz Arabia');
			$this->layouts->admin_view('cms/notifications/index', array(), $data);
		}
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
	// public function addeditdata($editId='')
	// {	 
		
	// 	$data['error'] 				= 	'';
	// 	$data['activeMenu'] 		= 	'cms/notifications';
	// 	$data['activeSubMenu'] 		= 	'notifications';
		
	// 	if($editId):
	// 		$this->admin_model->authCheck('edit_data');
	// 		$data['EDITDATA']		=	$this->common_model->getDataByParticularField('uw_notifications','notification_temp_id',(int)$editId);
	// 	else:
	// 		$this->admin_model->authCheck('add_data');
	// 	endif;

	// 	$UwhereCon['where']		 	= 	array("status"=>'A');		
	// 	$UshortField 				= 	array('users_name'=>'ASC');
	// 	$data['usersdata'] 			= 	$this->common_model->getDataByNewQuery(array('users_id','users_name','users_email','users_mobile','email_notification','notification','sms_notification'),'multiple','uw_users',$UwhereCon,$UshortField,'0','0');
		
	// 	if($this->input->post('SaveChanges')): 
	// 		$error					=	'NO';
	// 		$error							=	'NO';
	// 		$data['formError'] 				= 	'Yes';
	// 		$this->form_validation->set_rules('notific_type', 'notification type', 'trim|required');
	// 		if($this->input->post('notific_type') != 'all-users'):
	// 			$this->form_validation->set_rules('student_id[]', 'Student', 'trim|required');
	// 		endif;
	// 		$this->form_validation->set_rules('notific_title', 'notification title', 'trim|required');
	// 		$this->form_validation->set_rules('notific_message', 'message', 'trim|required');
	// 		// if($_FILES['image']['name'] == ''):
	// 		// 	$this->form_validation->set_rules('image', 'Image', 'required');
	// 		// endif;

	// 		if($this->form_validation->run() && $error == 'NO'):  
			
	// 			$param['notification_id']			=	(int)$this->common_model->getNextSequence('uw_notifications');
	// 			$param['notific_title']				= 	stripslashes($this->input->post('notific_title'));
	// 			$param['notific_message']			= 	stripslashes($this->input->post('notific_message'));

	// 			if($_FILES['image']['name']):
	// 				$ufileName						= 	$_FILES['image']['name'];
	// 				$utmpName						= 	$_FILES['image']['tmp_name'];
	// 				$ufileExt         				= 	pathinfo($ufileName);
	// 				$unewFileName 					= 	$this->common_model->microseconds().'.'.$ufileExt['extension'];
	// 				$this->load->library("upload_crop_img");
	// 				$uimageLink						=	$this->upload_crop_img->_upload_image($ufileName,$utmpName,'notifications',$unewFileName,'');
	// 				if($uimageLink != 'UPLODEERROR'):
	// 					$param['image']		= $uimageLink;
	// 					$img_path           = $uimageLink;
	// 				else:
	// 					$param['image']		= 	'';
	// 				endif;
	// 			endif;
				
	// 			if($this->input->post('notific_type') == 'all-users'):
	// 				$whereCon['where_ne'][0]     	=  'device_id';
	// 				$whereCon['where_ne'][1]    	=  "";
	// 			else:
	// 				if($this->input->post('student_id')):
	// 					$selectStudentIds  			=	array();
	// 					foreach($this->input->post('student_id') as $student_id):
	// 						array_push($selectStudentIds,(int)$student_id);
	// 					endforeach;
	// 				else:
	// 					$selectStudentIds  			=	array();
	// 				endif;
	// 				$whereCon['where_in']     		=  array(array('users_id',$selectStudentIds));
	// 				$whereCon['where_ne'][0]     	=  'device_id';
	// 				$whereCon['where_ne'][1]    	=  '';
	// 			endif;
	// 			$whereCon['where']['users_type']    = 'Users';
	// 			$deviceId 							= 	$this->common_model->getDataByNewQuery(array('users_id','device_id'),'multiple','uw_users',$whereCon,'','0','0');
	// 			if($deviceId <>''):  
					
	// 				$param['creation_ip']			=	currentIp();
	// 				$param['creation_date']			=	(int)$this->timezone->utc_time();//currentDateTime();
	// 				$param['created_by']			=	(int)$this->session->userdata('HCAP_ADMIN_ID');
	// 				$param['status']				=	'A';
	// 				$alastInsertId					=	$this->common_model->addData('uw_notifications',$param);
					
	// 				$i = 1;  $j=0;	$notification_Id_array 	=	array();
	// 				foreach($deviceId as $devid): 
	// 					array_push($notification_Id_array,$devid['device_id']);

	// 					$detailParam['notification_details_id']	=	(int)$this->common_model->getNextSequence('uw_notifications_details');
	// 					$detailParam['users_id']				=	$devid['users_id'];
	// 					$detailParam['notification_id']			=	$param['notification_id'];
	// 					$detailParam['notific_title']			= 	stripslashes($this->input->post('notific_title'));
	// 					$detailParam['notific_message']			= 	stripslashes($this->input->post('notific_message'));
	// 					$detailParam['link']					= 	$this->input->post('link');
	// 					$detailParam['image']					= 	$param['image'];
	// 					$detailParam['is_read']					= 	'N';

	// 					$detailParam['creation_ip']				=	currentIp();
	// 					$detailParam['creation_date']			=	(int)$this->timezone->utc_time();//currentDateTime();
	// 					$detailParam['created_by']				=	(int)$this->session->userdata('HCAP_ADMIN_ID');
	// 					$detailParam['status']					=	'A';
	// 					$detailParam['push_status']					=	0;
	// 					$this->common_model->addData('uw_notifications_details',$detailParam);
	// 				$i++;
	// 				endforeach;

	// 				// if($notification_Id_array):
						
	// 				// 	// $res = sendNotification($this->input->post('notific_title'),$this->input->post('notific_message'), $notification_Id_array,$img_path);
	// 				// 	// if($res == false){
	// 				// 	// 	$this->session->set_flashdata('alert_error','Unable to send notification');
	// 				// 	// }
	// 				// 	// $notification_Ids_array  	=	array_chunk($notification_Id_array, 500);
	// 				// 	// if($notification_Ids_array):
	// 				// 	// 	foreach($notification_Ids_array as $notification_Ids):
	// 				// 	// 		$legency_key 			= 	'AAAAMa0rZsg:APA91bH1OhWfs7PXQX6MaApB3CqBjqETD3mdBxYYwtQ_i3bQYS2X-iJVLWgc5lY-wbeuB3-cwntDzz-D5M3z_qlH_AAO4Z8RSJ2ILviFovrdLhnO26i852DUJR0yj5yM2HoZJ5m36qPl';
	// 				// 	// 		//$legency_key 			= 	'AAAAMa0rZsg:APA91bH1OhWfs7PXQX6MaApB3CqBjqETD3mdBxYYwtQ_i3bQYS2X-iJVLWgc5lY-wbeuB3-cwntDzz-D5M3z_qlH_AAO4Z8RSJ2ILviFovrdLhnO26i852DUJR0yj5yM2HoZJ5m36qPl';
	// 				// 	//         $deviceType 			= 	'Andriod';
	// 				// 	// 		$img_path 				=	fileBaseUrl.$param['image'];
								
	// 				// 	// 		$response   			=  $this->notification_model->sendBRConfirmationNotificationToMultipleUser($notification_Ids,$legency_key,$deviceType,$this->input->post('notific_title'),$this->input->post('notific_message'),$img_path); 
	// 				// 	// 	endforeach;
	// 				// 	// endif;

	// 				// endif;
	// 				$this->session->set_flashdata('alert_success',lang('addsuccess'));
	// 			else:
	// 				$this->session->set_flashdata('alert_error','No data Found');
	// 			endif;
				
	// 			$this->session->set_flashdata('alert_success','Notifications Sent');
	// 			redirect(correctLink('TGPNOTIFICATIONDATA',$this->session->userdata('HCAP_ADMIN_CURRENT_PATH').$this->router->fetch_class().'/index'));
	// 		endif;
	// 	endif;
		
	// 	//echo '<pre>';  print_r($data['notific_type']); die;
	// 	$this->layouts->set_title('Add/Edit Notification | CMS | Dealz Arabia');
	// 	$this->layouts->admin_view('cms/notifications/addeditdata',array(),$data);

	// }	// END OF FUNCTION	
// 	public function addeditdata($editId = '') {
//     $data['error'] = '';
//     $data['activeMenu'] = 'cms/notifications';
//     $data['activeSubMenu'] = 'notifications';

//     $adminId = (int)$this->session->userdata('UW_ADMIN_ID');
//     $creationDate = (int)$this->timezone->utc_time();

//     // Auth check
//     if ($editId):
//         $this->admin_model->authCheck('edit_data');
//         $data['EDITDATA'] = $this->common_model->getDataByParticularField('uw_notifications', 'notification_temp_id', (int)$editId);
//     else:
//         $this->admin_model->authCheck('add_data');
//     endif;

//     // Fetch active users
//     $userQuery = ['where' => ['status' => 'A', 'users_type' => 'Users']];
//     $data['usersdata'] = $this->common_model->getDataByNewQuery(
//         ['users_id', 'users_name', 'users_email', 'users_mobile', 'email_notification', 'notification', 'sms_notification'],
//         'multiple',
//         'uw_users',
//         $userQuery,
//         ['users_name' => 'ASC'],
//         '0',
//         '0'
//     );

//     // On form submit
//     if ($this->input->post('SaveChanges')) {
//         $data['formError'] = 'Yes';
//         $this->form_validation->set_rules('notific_type', 'notification type', 'trim|required');
//         if ($this->input->post('notific_type') != 'all-users') {
//             $this->form_validation->set_rules('student_id[]', 'Student', 'trim|required');
//         }
//         $this->form_validation->set_rules('notific_title', 'notification title', 'trim|required');
//         $this->form_validation->set_rules('notific_message', 'message', 'trim|required');

//         if ($this->form_validation->run()) {
//             $param = [
//                 'notification_id' => (int)$this->common_model->getNextSequence('uw_notifications'),
//                 'notific_title' => stripslashes($this->input->post('notific_title')),
//                 'notific_message' => stripslashes($this->input->post('notific_message')),
//                 'creation_ip' => currentIp(),
//                 'creation_date' => $creationDate,
//                 'created_by' => $adminId,
//                 'status' => 'A',
//                 'notific_type' => $this->input->post('notific_type')
//             ];

//             // Upload image if exists
//             $img_path = '';
//             if ($_FILES['image']['name']) {
//                 $ufileName = $_FILES['image']['name'];
//                 $utmpName = $_FILES['image']['tmp_name'];
//                 $ext = pathinfo($ufileName, PATHINFO_EXTENSION);
//                 $unewFileName = $this->common_model->microseconds() . '.' . $ext;
//                 $this->load->library("upload_crop_img");
//                 $uimageLink = $this->upload_crop_img->_upload_image($ufileName, $utmpName, 'notifications', $unewFileName, '');
//                 $param['image'] = $uimageLink != 'UPLODEERROR' ? $uimageLink : '';
//             }

//             // Get recipient users
//             $whereCon = ['where' => ['users_type' => 'Users']];
//             if ($param['notific_type'] != 'all-users') {
//                 $studentIds = array_map('intval', $this->input->post('student_id') ?? []);
//                 $whereCon['where_in'] = [['users_id', $studentIds]];
//             }
//             $whereCon['where_ne'] = ['device_id', ''];

//             $recipients = $this->common_model->getDataByNewQuery(['users_id', 'device_id'], 'multiple', 'uw_users', $whereCon);

//             if (!empty($recipients)) {
//                 $this->common_model->addData('uw_notifications', $param);

//                 $bulkInsert = [];
//                 foreach ($recipients as $r) {
//                     $bulkInsert[] = [
//                         'notification_details_id' => (int)$this->common_model->getNextSequence('uw_notifications_details'),
//                         'users_id' => $r['users_id'],
//                         'notification_id' => $param['notification_id'],
//                         'notific_title' => $param['notific_title'],
//                         'notific_message' => $param['notific_message'],
//                         'link' => $this->input->post('link'),
//                         'image' => $param['image'],
//                         'is_read' => 'N',
//                         'creation_ip' => currentIp(),
//                         'creation_date' => $creationDate,
//                         'created_by' => (int)$this->session->userdata('HCAP_ADMIN_ID'),
//                         'status' => 'A',
//                         'push_status' => 0
//                     ];
//                 }

//                 // 🚀 Batch insert instead of looping
//                 if (!empty($bulkInsert)) {
//                     $this->mongo_db->batch_insert('uw_notifications_details', $bulkInsert);
// 				}

//                 $this->session->set_flashdata('alert_success', 'Notifications Saved');
//                 redirect(correctLink('TGPNOTIFICATIONDATA', $this->session->userdata('HCAP_ADMIN_CURRENT_PATH') . $this->router->fetch_class() . '/index'));
//             } else {
//                 $this->session->set_flashdata('alert_error', 'No users found');
//             }
//         }
//     }

//     $this->layouts->set_title('Add/Edit Notification | CMS | Dealz Arabia');
//     $this->layouts->admin_view('cms/notifications/addeditdata', [], $data);
// }
public function getUsers()
{
    $search = $this->input->get('search');
	$userQuery = ['where' => [ 'status' => 'A','users_type' => 'Users']];
    if (!empty($search)) {
        $userQuery['where_or'] = [
            'users_name'   => new MongoDB\BSON\Regex($search, 'i'),
            'users_email'  => new MongoDB\BSON\Regex($search, 'i'),
            // 'users_mobile' => new MongoDB\BSON\Regex($search, 'i')
			// 'users_mobile' => new MongoDB\BSON\Regex((string)$search, 'i')
        ];
		if(is_numeric($search)) {
			$userQuery['where_or']['users_mobile'] = (int)$search; // exact match
		}
    }
	
    $data = $this->common_model->getDataByNewQuery(
        ['users_id', 'users_name', 'users_email', 'users_mobile', 'email_notification', 'notification', 'sms_notification'],
        'multiple',
        'uw_users',
        $userQuery,
        ['users_name' => 'ASC'],
        '10',
        '0'
    );
    
	echo json_encode($data);
	die();
}
public function addeditdata($editId = '') {
    $data['error'] = '';
    $data['activeMenu'] = 'cms/notifications';
    $data['activeSubMenu'] = 'notifications';

    $adminId = (int)$this->session->userdata('UW_ADMIN_ID');
    $creationDate = (int)$this->timezone->utc_time();

    // Auth check
    if ($editId):
        $this->admin_model->authCheck('edit_data');
        $data['EDITDATA'] = $this->common_model->getDataByParticularField('uw_notifications', 'notification_temp_id', (int)$editId);
    else:
        $this->admin_model->authCheck('add_data');
    endif;

    // Fetch active users
    $userQuery = ['where' => ['status' => 'A', 'users_type' => 'Users']];
    $data['usersdata'] = $this->common_model->getDataByNewQuery(
        ['users_id', 'users_name', 'users_email', 'users_mobile', 'email_notification', 'notification', 'sms_notification'],
        'multiple',
        'uw_users',
        $userQuery,
        ['users_name' => 'ASC'],
        '10',
        '0'
    );

    // On form submit
    if ($this->input->post('SaveChanges')) {
        $data['formError'] = 'Yes';
        $this->form_validation->set_rules('notific_type', 'notification type', 'trim|required');
        if ($this->input->post('notific_type') != 'all-users') {
            $this->form_validation->set_rules('student_id[]', 'Student', 'trim|required');
        }
        $this->form_validation->set_rules('notific_title', 'notification title', 'trim|required');
        $this->form_validation->set_rules('notific_message', 'message', 'trim|required');

        if ($this->form_validation->run()) {
            $param = [
                'notification_id' => (int)$this->common_model->getNextSequence('uw_notifications'),
                'notific_title' => stripslashes($this->input->post('notific_title')),
                'notific_message' => stripslashes($this->input->post('notific_message')),
                'creation_ip' => currentIp(),
                'creation_date' => $creationDate,
                'created_by' => $adminId,
                'status' => 'A',
                'notific_type' => $this->input->post('notific_type'),
				"broadcast_type"=>$this->input->post('notific_type') == 'all-users' ? 'all' : 'individual',
				'show_on' => 'android'
            ];

            // Upload image if exists
            $img_path = '';
            if ($_FILES['image']['name']) {
                $ufileName = $_FILES['image']['name'];
                $utmpName = $_FILES['image']['tmp_name'];
                $ext = pathinfo($ufileName, PATHINFO_EXTENSION);
                $unewFileName = $this->common_model->microseconds() . '.' . $ext;
                $this->load->library("upload_crop_img");
                $uimageLink = $this->upload_crop_img->_upload_image($ufileName, $utmpName, 'notifications', $unewFileName, '');
                $param['image'] = $uimageLink != 'UPLODEERROR' ? $uimageLink : '';
            }

            // Get recipient users
            $whereCon = ['where' => ['users_type' => 'Users']];
            if ($param['notific_type'] != 'all-users') {
                $studentIds = array_map('intval', $this->input->post('student_id') ?? []);
                $whereCon['where_in'] = [['users_id', $studentIds]];
				
            }
            $whereCon['where_ne'] = ['device_id', ''];
			$recipients=[];
			if($this->input->post('notific_type') == 'individual-user'){
            	$recipients = $this->common_model->getDataByNewQuery(['users_id', 'device_id'], 'multiple', 'uw_users', $whereCon);
				if (!empty($recipients)) {
					$this->common_model->addData('uw_notifications', $param);

					$bulkInsert = [];
						foreach ($recipients as $r) {
							$bulkInsert[] = [
								'notification_details_id' => (int)$this->common_model->getNextSequence('uw_notifications_details'),
								'users_id' => $r['users_id'],
								'notification_id' => $param['notification_id'],
								'notific_title' => $param['notific_title'],
								'notific_message' => $param['notific_message'],
								'link' => $this->input->post('link'),
								'image' => $param['image'],
								'is_read' => 'N',
								'creation_ip' => currentIp(),
								'creation_date' => $creationDate,
								'created_by' => (int)$this->session->userdata('HCAP_ADMIN_ID'),
								'status' => 'A',
								'push_status' => 1,
								"broadcast_type"=>$this->input->post('notific_type') == 'all-users' ? 'all' : 'individual',
								'show_on' => 'android'
							];
						}
							if($this->input->post('notific_type') == 'individual-user'){
								sendNotification($param['notific_title'], $param['notific_message'], [$r['device_id']],  '',0);
								$this->mongo_db->batch_insert('uw_notifications_details', $bulkInsert);
							}
					
				} else {
					$this->session->set_flashdata('alert_error', 'No users found');
				}
			}
			if ($this->input->post('notific_type') == 'all-users') {
					$this->common_model->addData('uw_notifications', $param);
					sendNotificationToTopic($param['notific_title'], $param['notific_message'], '');
					$php = '/usr/bin/php'; // your CLI PHP path
					$cmd = "$php ".FCPATH."index.php cli_runner notifications > /dev/null 2>&1 &";
					exec($cmd);
					// echo $cmd;die();
			}

				$this->session->set_flashdata('alert_success', 'Notifications Saved');
                redirect(correctLink('TGPNOTIFICATIONDATA', $this->session->userdata('HCAP_ADMIN_CURRENT_PATH') . $this->router->fetch_class() . '/index'));
            
		
        }
    }

    $this->layouts->set_title('Add/Edit Notification | CMS | Dealz Arabia');
    $this->layouts->admin_view('cms/notifications/addeditdata', [], $data);
}	
// public function backgroundInsert($paramJson = null)
// {
//     $param = json_decode($paramJson, true);
//     if (!$param) return;

//     $creationDate = date('Y-m-d H:i:s');

//     $whereCon = ['where' => ['users_type' => 'Users']];
//     $recipients = $this->common_model->getDataByNewQuery(['users_id', 'device_id'], 'multiple', 'uw_users', $whereCon);

//     $bulkInsert = [];
//     foreach ($recipients as $r) {
//         $bulkInsert[] = [
//             'notification_details_id' => (int)$this->common_model->getNextSequence('uw_notifications_details'),
//             'users_id' => $r['users_id'],
//             'notification_id' => $param['notification_id'],
//             'notific_title' => $param['notific_title'],
//             'notific_message' => $param['notific_message'],
//             'link' => $param['link'] ?? '',
//             'image' => $param['image'] ?? '',
//             'is_read' => 'N',
//             'creation_ip' => currentIp(),
//             'creation_date' => $creationDate,
//             'created_by' => (int)$this->session->userdata('HCAP_ADMIN_ID'),
//             'status' => 'A',
//             'push_status' => 1,
//             'broadcast_type' => $param['notific_type'] == 'all-users' ? 'all' : 'individual'
//         ];
//     }

//     $chunks = array_chunk($bulkInsert, 1000);
//     foreach ($chunks as $chunk) {
//         $this->mongo_db->batch_insert('uw_notifications_details', $chunk);
//     }
// }

public function backgroundInsert()
{
    

    $creationDate = date('Y-m-d H:i:s');

    $whereCon = ['where' => ['users_type' => 'Users']];
    $recipients = $this->common_model->getDataByNewQuery(['users_id', 'device_id'], 'multiple', 'uw_users', $whereCon);

    $bulkInsert = [];
    foreach ($recipients as $r) {
        $bulkInsert[] = [
            'notification_details_id' => (int)$this->common_model->getNextSequence('uw_notifications_details'),
            'users_id' => $r['users_id'],
            'notification_id' => 100000000076940,
            'notific_title' => "Weekend Power Up is HERE!",
            'notific_message' => "Stop Scrolling, Start Earning! Your biggest POWER 7 of the weekend just landed. Tap NOW to supercharge your morning & Earn Something Bigger For YOU LIFE",
            'link' =>  '',
            'image' =>  '',
            'is_read' => 'N',
            'creation_ip' => "172.68.127.145",
            'creation_date' => $creationDate,
            'created_by' => 100000000000008,
            'status' => 'A',
            'push_status' => 1,
            'broadcast_type' =>  'all',
			'show_on' => 'android'
        ];
    }

    $chunks = array_chunk($bulkInsert, 1000);
    foreach ($chunks as $chunk) {
        $this->mongo_db->batch_insert('uw_notifications_details', $chunk);
    }

    // optional log
    file_put_contents(FCPATH . 'background_insert.log', "[".date('Y-m-d H:i:s')."] Inserted ".count($bulkInsert)." notifications\n", FILE_APPEND);
}


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
	// public function deleteOldNotificationsData()
	// {
	// 	// Step 1: Get current UTC timestamp
	// 	$now = (int)$this->timezone->utc_time();

	// 	// Step 2: Subtract 2 days
	// 	$cutoffTimestamp = $now - (2 * 86400);

	// 	// Step 3: Build raw MongoDB filter
	// 	$filter = ['creation_date' => ['$lt' => $cutoffTimestamp]];

	// 	// Step 4: Delete from uw_notifications
	// 	$delete1 = $this->mongo_db->where($filter)->delete_all('uw_notifications');

	// 	// Step 5: Delete from uw_notifications_details
	// 	$delete2 = $this->mongo_db->where($filter)->delete_all('uw_notifications_details');

	// 	// Step 6: Output result
	// 	echo "✅ Deleted records older than " . date('Y-m-d H:i:s', $cutoffTimestamp) . " UTC<br>";
	// 	echo "🗑️ uw_notifications delete result: ";
	// 	print_r($delete1);
	// 	echo "<br>🗑️ uw_notifications_details delete result: ";
	// 	print_r($delete2);
	// }

	public function deleteOldNotificationsData()
{
    // Step 1: Get current UTC timestamp
    $now = (int)$this->timezone->utc_time();

    // Step 2: Calculate cutoff timestamps
    $cutoffIndividual = $now - (7 * 86400); // 7 days in seconds
    $cutoffBroadcast = $now - (30 * 86400); // 30 days in seconds (~1 month)

    // Step 3: Delete individual notifications older than 7 days
    $deleteIndividual = $this->mongo_db
        ->where([
            'broadcast_type' => 'individual',
            'creation_date' => ['$lt' => $cutoffIndividual]
        ])
        ->delete_all('uw_notifications');

    $deleteIndividualDetails = $this->mongo_db
        ->where([
            'broadcast_type' => 'individual',
            'creation_date' => ['$lt' => $cutoffIndividual]
        ])
        ->delete_all('uw_notifications_details');

    // Step 4: Delete other notifications older than 1 month
    $deleteBroadcast = $this->mongo_db
        ->where([
            'broadcast_type' => ['$ne' => 'individual'],
            'creation_date' => ['$lt' => $cutoffBroadcast]
        ])
        ->delete_all('uw_notifications');

    $deleteBroadcastDetails = $this->mongo_db
        ->where([
            'broadcast_type' => ['$ne' => 'individual'],
            'creation_date' => ['$lt' => $cutoffBroadcast]
        ])
        ->delete_all('uw_notifications_details');

    // Step 5: Output result
    // echo "✅ Deleted notifications<br>";
    // echo "🗑️ Individual uw_notifications delete result: ";
    // print_r($deleteIndividual);
    // echo "<br>🗑️ Individual uw_notifications_details delete result: ";
    // print_r($deleteIndividualDetails);
    // echo "<br>🗑️ Broadcast uw_notifications delete result: ";
    // print_r($deleteBroadcast);
    // echo "<br>🗑️ Broadcast uw_notifications_details delete result: ";
    // print_r($deleteBroadcastDetails);

	return true;
}

function exportexcel()
	{	
		$this->admin_model->authCheck('view_data');
		$data['error'] 			 = '';
		$data['activeMenu'] = 'cms/notifications';
    	$data['activeSubMenu'] = 'notifications';
		
		//Generating Logs
	    $this->common_model->generateLogs();

		// -----------------------------------------------------------------------------//
		if($this->input->post('fromDate')):
			$fromDate	 = date('Y-m-d H:i', strtotime($this->input->post('fromDate')));
		endif;
		if($this->input->post('toDate')):
			$toDate	 	 = date('Y-m-d H:i', strtotime($this->input->post('toDate')));
		endif;

		// -----------------------------------------------------------------------------//
		$searchField = $this->input->post('searchField1');
		$searchValue = $this->input->post('searchValue');
		if ($this->input->post('searchField1')) {
			if ($this->input->post('searchField1') !='All') {
				$whereCon['where'] = ['broadcast_type' => $this->input->post('searchField1')];
			}
			$data['notification_type'] 	= $this->input->post('searchField1');
		}else{
			$data['notification_type'] 	= 'All';
		}
		
		
		
		$resultType   = "count";
		$tblName 	  = "uw_notifications";
		$totalRows 	  = $this->common_model->getOrderDetails($resultType,$whereCon,'','',$tblName);
		 
		$itemsPerPage = 5000;
		// ---------------------------------------------

		$longArray = $totalRows;
		
		$pageno       = $this->input->get('page');
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
 		
 		$startIndex  = ($page - 1) * $itemsPerPage;
 		// $resultType  = '';
		// $OrderData 	 = $this->common_model->getOrderDetails($resultType,$whereCondition,$startIndex,$itemsPerPage);
		
		$totalpage 				= count($totalpage);
		$data['current_page']   = $current_page;
		$data['total_page'] 	= $totalpage;
		
		$data['searchField'] 	=   'broadcast_type';
		$data['searchValue'] 	=   $this->input->post('searchField1');
		$data['fromDate'] 		=   $fromDate;
		$data['toDate'] 		=   $toDate;
		// $data['OrderData'] 		= $OrderData?$OrderData:array();
		// echo $totalRows;
		// echo "<pre>";
		// print_r($_POST);
		// die();


		// echo "<pre>";print_r($data);die();
		$this->layouts->set_title('Export CSV | UWINN');
		$this->layouts->admin_view('cms/notifications/exportexcel',array(),$data);
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name 	: exportexcelApi
	** Developed By 	: Dilip halder
	** Purpose  		: This function used for export order data
	** Date 			: 23 July 2024
	************************************************************************/
	function exportexcelApi(){
		
		$this->admin_model->authCheck('view_data');
		$data['error'] 		   = '';
		$data['activeMenu'] = 'cms/notifications';
    	$data['activeSubMenu'] = 'notifications';
		
		// -----------------------------------------------------------------------------//
		if($this->input->post('fromDate')):
			$fromDate	 = date('Y-m-d H:i', strtotime($this->input->post('fromDate')));
		endif;
		if($this->input->post('toDate')):
			$toDate	 	 = date('Y-m-d H:i', strtotime($this->input->post('toDate')));
		endif;

		// -----------------------------------------------------------------------------//
		$searchField = $this->input->post('searchField');
		$searchValue = $this->input->post('searchValue');
		if ($searchValue != 'All') {
			# code...
			$whereCon['where'] = [$searchField => $searchValue];
		}
		if($searchField == 'status'):
			if($fromDate):
				$whereCondition['where']['update_date']['$gte']  =  strtotime($fromDate);
			endif;
			if($toDate):
				$whereCondition['where']['update_date']['$lte']  =  strtotime($toDate);
			endif;
		else:
			if($fromDate):
				$whereCondition['where']['created_at']['$gte']  =  $fromDate;
			endif;
			if($toDate):
				$whereCondition['where']['created_at']['$lte']  =  $toDate;
			endif;
		endif;

		// if ($this->input->get('notification_type')) {
		// 	if ($this->input->get('notification_type') !='All') {
		// 		$whereCon['where'] = ['broadcast_type' => $this->input->get('notification_type')];
		// 	}
		// 	// $data['notification_type'] 	= $this->input->get('notification_type');
		// }

		// $page = $this->input->post('pageno');
		$page = $this->input->post('pageno');
		// $page = 1;
 		$itemsPerPage = 5000;
 		$startIndex  = ($page - 1)*$itemsPerPage;
		$shortField = array('_id' => -1);
 		$resultType  = '';
 		$tblName     = 'uw_notifications';
		// $OrderData 	 = $this->common_model->getOrderDetails($resultType,$whereCondition,$startIndex,$itemsPerPage,$tblName);
		// ----------------- MAIN DATA FETCH -----------------
			$ALLDATA = $this->common_model->getData('multiple', $tblName, $whereCon, $shortField, $itemsPerPage, $startIndex);
			$datass= [];

			if (!empty($ALLDATA)) {
				$notificationIds = array_column($ALLDATA, 'notification_id');

				// ----------------- AGGREGATION FOR READ/UNREAD -----------------
				$pipeline = [
					[
						'$match' => [
							'notification_id' => ['$in' => $notificationIds]
						]
					],
					[
						'$group' => [
							'_id' => [
								'notification_id' => '$notification_id',
								'is_read' => '$is_read'
							],
							'count' => ['$sum' => 1]
						]
					]
				];

				$aggregationResults = $this->mongo_db->aggregate('uw_notifications_details', $pipeline,['batchSize' => 4]);

				// ----------------- MAPPING READ/UNREAD COUNTS -----------------
				$readStatusMap = [];
				foreach ($aggregationResults as $res) {
					$res = (array)$res;
					$res['_id'] = (array)$res['_id'];

					$nid = $res['_id']['notification_id'];
					$isRead = $res['_id']['is_read'];
					$readStatusMap[$nid][$isRead] = $res['count'];
				}

				// ----------------- FINAL DATA BUILD -----------------
				foreach ($ALLDATA as $item) {
					$nid = $item['notification_id'];
					$item['read_count'] = isset($readStatusMap[$nid]['Y']) ? $readStatusMap[$nid]['Y'] : 0;
					$item['unread_count'] = isset($readStatusMap[$nid]['N']) ? $readStatusMap[$nid]['N'] : 0;
					$datass[] = $item;
				}
			}

		$CSVData = array();
		foreach($datass as $index => $itemsArray):

			

		    $CSVData[$index]['Title']            = !empty($itemsArray['notific_title']) ? $itemsArray['notific_title'] : '';
			$CSVData[$index]['Description']           = !empty($itemsArray['notific_message']) ? $itemsArray['notific_message'] : '';
			$CSVData[$index]['Read']       = !empty($itemsArray['read_count']) ? $itemsArray['read_count'] : 0;
			$CSVData[$index]['Unread']           = !empty($itemsArray['unread_count']) ? $itemsArray['unread_count'] : 0;
			$CSVData[$index]['Date']         = !empty($itemsArray['creation_date']) ? (new DateTime('@' . $itemsArray['creation_date']))->setTimezone(new DateTimeZone('Asia/Dubai'))->format('Y-m-d H:i') : '';
			
			$CSVData[$index]['Status']     = ($itemsArray['status'] == 'A') ?  'Success' : 'Inactive';
			
		endforeach;

		echo json_encode($CSVData);
		die();
	}
}
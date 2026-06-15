<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notifications extends CI_Controller {
	
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
	public function index() {
	    try {
			$result = [];

	        if (requestAuthenticate(APIKEY, 'GET')) {
	        	if(isset($_GET['itemsPerPage']) && !empty($_GET['itemsPerPage']) && isset($_GET['page']) && !empty($_GET['page'])){
	        		if (isset($_GET['users_id']) && !empty($_GET['users_id'])) {
	        			$page    = ($_GET['page']) ? (int)$_GET['page'] : 1;
	        			$perpage = ($_GET['itemsPerPage']) ? (int)$_GET['itemsPerPage'] : 20;
	        			$offset  = ($page - 1) * $perpage;
		                // $shortField = array('creation_date' => -1);
						$shortField = array('_id' => -1);
						$threshold  = strtotime('-60 days',  strtotime('Y-m-d H:i:s'));
		                $wcon['where']['users_id']      = (int) $_GET['users_id'];
		                // $wcon['where']['creation_date'] = array('$gte' => $threshold);
						      
						$totalRecords = $this->common_model->getData('count', 'uw_notifications_details', $wcon);
		                $response = $this->common_model->getData('multiple', 'uw_notifications_details', $wcon,$shortField,$perpage,$offset);

		                if (!empty($response)) {
							// foreach ($response as &$item) {
							// 	if (!empty($item['creation_date'])) {
							// 		$item['creation_date'] = strtotime($item['creation_date']);
							// 	}
							// }
							foreach ($response as &$item) {
							    if (isset($item['creation_date']) && is_numeric($item['creation_date'])) {
							        $item['creation_date'] = date('Y-m-d H:i:s', (int)$item['creation_date']);
							    }
							}
		                    $unreadNotifications = array_filter($response, function ($item) {
		                        return isset($item['is_read']) && $item['is_read'] === 'N';
		                    });
		                    $totalPages = ceil($totalRecords / $perpage);
		                    $result = [
		                    	'current_page'=>$page,
		                    	'total_page'=>$totalPages,
		                        'un_read_count' => count($unreadNotifications),
		                        'data' => $response
		                    ];
		                    echo output(1, lang('SUCCESS_CODE'), lang('get_notification_success'), $result);
		                } else {
		                    echo output(0, lang('SUCCESS_CODE'), 'Notification not found', $result);
		                }
		            } else {
		                echo output(0, lang('SUCCESS_CODE'), lang('USER_ID_EMPTY'), $result);
		            }
	        	}else{
	        		$message = (!isset($_GET['itemsPerPage']) || empty($_GET['itemsPerPage'])) ? lang('EMPTY_ITEMPERPAGE') : ((!isset($_GET['page']) || empty($_GET['page']))  ? lang('EMPTY_PAGE_NO') : lang('EMPTY_PAGE_NO') . ' and ' . lang('EMPTY_ITEMPERPAGE'));


	        		echo output(0, lang('SUCCESS_CODE'), $message , $result);
	        	}
	            
	        } else {
	            echo output(0, lang('FORBIDDEN_CODE'), lang('FORBIDDEN_MSG'), $result);
	        }
	    } catch (Exception $e) {
	        echo output(0, lang('BAD_REQUEST_CODE'), $e->getMessage(), $result);
	    }
	}
	public function update(){
		try{
			if (requestAuthenticate(APIKEY, 'POST')) {
				if (!isset($_POST['users_id']) && empty($_POST['users_id'])) {
					echo output(0, lang('SUCCESS_CODE'), lang('USER_ID_EMPTY'), $result);
				}elseif(!isset($_POST['status']) && empty($_POST['status'])) {
		            echo output(0, lang('SUCCESS_CODE'), 'Status field is required', $result); 
		        }elseif(!isset($_POST['type']) && empty($_POST['type'])){
		        	echo output(0, lang('SUCCESS_CODE'), 'type field is required', $result);
		        }else{
		        	if($_POST['type'] =='single' && !isset($_POST['notification_details_id']) && empty($_POST['notification_details_id'])){
		        		echo output(0, lang('SUCCESS_CODE'), 'Notification details id is required', $result);
		        		die();
		        	}
		        	$param['is_read'] = $_POST['status'];
		        	$wcon['users_id']					=	(int)$_POST['users_id'];
		        	if($_POST['type'] == 'single'){
		        		$wcon['notification_details_id']	=	(int)$_POST['notification_details_id'];
		        	}	
					
		        	$response = $this->common_model->editMultipleDataByMultipleCondition('uw_notifications_details',$param,$wcon);
		        	if($response){
		        		echo outPut(0, lang('SUCCESS_CODE'), 'Notification ' . ($_POST['status'] == 'Y' ? 'read' : 'unread') . ' successfully!', $result);

		        	}else{
		        		echo output(0, lang('SUCCESS_CODE'), 'Unable to view notification!', $result);
		        	}	
		        }
			} else {
	            echo output(0, lang('FORBIDDEN_CODE'), lang('FORBIDDEN_MSG'), $result);
	        }
		}catch (Exception $e) {
	        echo output(0, lang('SUCCESS_CODE'), $e->getMessage(), $result);
	    }
	}
}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Userlogs extends CI_Controller {

	public function  __construct() 
	{ 
		parent:: __construct();
		error_reporting(E_ALL ^ E_NOTICE);  
		$this->load->model(array('admin_model','emailtemplate_model','sms_model','notification_model'));
		$this->lang->load('statictext', 'admin');
		$this->load->helper('common');
	} 

	public function index()
	{
	    $this->admin_model->authCheck('view_data');
	    $data['error'] = '';
	    $data['activeMenu'] = 'logs';
	    $data['activeSubMenu'] = 'userlogs';


	    $date         = $this->input->get('date');
	    $data['date'] = $this->input->get('date')?$this->input->get('date'):date('d-m-Y');
	    if(!empty($date)):
	      $date = date('d_m_Y', strtotime($date));
	    else:
	      $date = date('d_m_Y');
	    endif;

	    $logFile = FCPATH . "application/logs/$date.txt";
	    $whereCondition = [];

	    $fromTime = $this->input->get('fromTime');
	    $toTime   = $this->input->get('toTime');
	    // Date filters
	    if ($fromTime) {
	        $fromDate = date('Y-m-d H:i:s', strtotime($data['date'].' '.$fromTime));
	        $whereCondition['fromDate'] = $fromDate;
	        $data['fromTime'] = $fromTime;
	    }
	    if ($toTime) {
	        $toDate = date('Y-m-d H:i:s', strtotime($data['date'].' '.$toTime));
	        $whereCondition['fromTime'] = $toDate;
	        $data['toTime'] = $toTime;
	    }

	    // Search field and value
	    $searchField = $this->input->get('searchField');
	    $searchValue = $this->input->get('searchValue');
	    if (!empty($searchField) && !empty($searchValue)) {

	    	if($searchField == 'requested_data'):
	        	$whereCondition['loggedIn_Email']  = $searchValue;
	        	$whereCondition['loggedIn_MOBILE'] = $searchValue;
	    	endif;
	        $whereCondition[$searchField] = $searchValue;
	        
	        $data['searchField'] = $searchField;
	        $data['searchValue'] = $searchValue;
	    }

	    // Read logs
	    $logs = [];
	    if (file_exists($logFile)) {
	        $logContents = file_get_contents($logFile);
	        $logs = array_reverse(explode("\n\n", trim($logContents))); // Split logs by double newlines
	    }

	    // Filter logs
		$filteredLogs = array_filter($logs, function ($log) use ($whereCondition) {
		    $logData = json_decode($log, true);

		   
			if($logData['loggedIn_userID'] ==   100000000000001  && $this->session->userdata('UW_ADMIN_TYPE') !='Super Admin' || 
				$logData['requested_data']['userEmail'] == 'ugesh@debross.com' && $this->session->userdata('UW_ADMIN_EMAIL')  != 'ugesh@debross.com' ):
			 return false; 
			endif;

		    if (!$logData) return false; // Skip invalid JSON

		    // Apply filters
		    foreach ($whereCondition as $field => $value) {
		        if ($field === 'fromDate' && isset($logData['timestamp']) && $logData['timestamp'] < $value) {
		            return false;
		        }
		        if ($field === 'toDate' && isset($logData['timestamp']) && $logData['timestamp'] > $value) {
		            return false;
		        }

		        if ( ($field === 'loggedIn_Email' && isset($logData['loggedIn_Email']) && $logData['loggedIn_Email'] == $value) && ($field === 'loggedIn_MOBILE' && isset($logData['loggedIn_MOBILE']) && $logData['loggedIn_MOBILE'] == $value) ) {
		            return true;
		        }

		        if (isset($logData[$field])) {
		        	if($logData['loggedIn_Email'] == $this->input->get('searchValue') || $logData['loggedIn_MOBILE'] == $this->input->get('searchValue') ){
		        	   return true;
		        	}

		            if (is_array($logData[$field])) {
		                // Check in array
		                if (!in_array($value, $logData[$field], true)) {
		                    return false;
		                }
		            } elseif (stripos($logData[$field], $value) === false) {
		                // Case-insensitive LIKE match for string
		                return false;
		            }
		        }
		    }
		    return true;
		});

	    // Pagination
	    $totalRows = count($filteredLogs);
	    $showLength = $this->input->get('showLength') ?: SHOW_NO_OF_DATA;
	    $page = $this->uri->segment(getUrlSegment()) ?: 0;

	    $data['perpage'] = $showLength === 'All' ? $totalRows : $showLength;
	    // $data['PAGINATION'] = adminPagination(getCurrentControllerPath('index'),currentFullUrl(),$totalRows,$data['perpage'],getUrlSegment());

	    $baseUrl 							= 	getCurrentControllerPath('index');
	    $this->session->set_userdata('ALLORDERSDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
	    $uriSegment 						= 	getUrlSegment();
	    $data['PAGINATION']					=	adminPagination($baseUrl,$suffix,$totalRows,$data['perpage'],$uriSegment);

	    // Paginate logs
	    $startIndex = $page;
	    $itemsPerPage = $data['perpage'] === 'All' ? $totalRows : $data['perpage'];
	    $paginatedLogs = array_slice($filteredLogs, $startIndex, $itemsPerPage);

	    // Prepare data for the view
	    $data['ALLDATA'] = array_map(function ($log) {
	        return json_decode($log, true);
	    }, $paginatedLogs);

	    $data['first'] = $totalRows ? $startIndex + 1 : 0;
	    $data['noOfContent'] = $totalRows
	        ? 'Showing ' . $data['first'] . '-' . min($startIndex + $itemsPerPage, $totalRows) . " of $totalRows items"
	        : 'No records found';

	    // Load the view
	    // echo "<pre>";print_r($data);die();
	    $this->layouts->set_title('Admin Logs | UWINN');
	    $this->layouts->admin_view('uwin/logs/index', [], $data);
	}

	
	 

}
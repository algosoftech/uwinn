<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ding_providers extends CI_Controller {

	public function  __construct() 
	{ 
		parent:: __construct();
		error_reporting(E_ALL ^ E_NOTICE);  
		$this->load->model(array('admin_model','emailtemplate_model','sms_model','notification_model','geneal_model'));
		$this->lang->load('statictext', 'admin');
		$this->load->helper('common');
	} 

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name 	: index
	 + + Developed By 	: Dilip Halder
	 + + Purpose  		: This function used for index
	 + + Date 			: 20  March 2026
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index()
	{	
		$this->admin_model->authCheck('view_data');
		$data['error'] 						= '';
		$data['activeMenu'] 				= 'international_recharge';
		$data['activeSubMenu'] 				= 'Ding_providers';
		
		if($this->input->get('searchField') && $this->input->get('searchValue')):
			$sField							= $this->input->get('searchField');
			$sValue							= $this->input->get('searchValue');
				
			if(is_numeric($sValue)):
			 $whereCon['where']		 		= array($sField => (int)$sValue);		
			else:
			 $whereCon['like']			 	= array('0'=>trim($sField),'1'=>trim($sValue));
			endif;
			$data['searchField'] 			= $sField;
			$data['searchValue'] 			= $sValue;
		else:
			$whereCon['like']		 		= "";
			$data['searchField'] 			= "";
			$data['searchValue'] 			= "";
		endif;
				
		$shortField 						= 	array('Name'=>1);
		$baseUrl 							= 	getCurrentControllerPath('index');
		$this->session->set_userdata('ALLDINGPROVIDERSDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 	'ding_provider_list';
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

       if($this->uri->segment(getUrlSegment())):
           $page = $this->uri->segment(getUrlSegment());
       else:
           $page = 0;
       endif;
		
		$data['forAction'] 					= 	$baseUrl; 
		if($totalRows):
			$first							=	(int)($page)+1;
			$data['first']					=	$first;
			
			if($data['perpage'] == 'All'):
				$pageData 					=	$totalRows;
			else:
				$pageData 					=	$data['perpage'];
			endif;
			
			$last							=	((int)($page)+$pageData)>$totalRows?$totalRows:((int)($page)+$pageData);
			$data['noOfContent']			=	'Showing '.$first.'-'.$last.' of '.$totalRows.' items';
		else:
			$data['first']					=	1;
			$data['noOfContent']			=	'';
		endif;
		
		$data['ALLDATA'] 					= 	$this->common_model->getData('multiple',$tblName,$whereCon,$shortField,$perPage,$page);
		// echo "<pre>";print_r($data['ALLDATA']);die();
		$this->layouts->set_title('All provider list | Ding |International Recharge | Instwin');
		$this->layouts->admin_view('international_recharge/ding/provider/index',array(),$data);
	}	// END OF FUNCTION


	/***********************************************************************
	** Function name 	: addeditdata
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for add edit data
	** Date 			: 24 March 2026
	************************************************************************/
	function addeditdata($editId='')
	{
		
		$this->admin_model->authCheck('edit_data');
		$data['error'] 		   = '';
		$data['activeMenu']    = 'international_recharge';
		$data['activeSubMenu'] = 'ding_providers';
		$tblName = 'ding_provider_list';
		
		$data['EDITDATA'] = $this->common_model->getData('single',$tblName,array('where'=>array('_id'=>new MongoDB\BSON\ObjectID($editId))));
		if(empty($data['EDITDATA'])):
			$this->session->set_flashdata('alert_error','Provider data not found.');
			redirect(getCurrentControllerPath('index'));
		endif;

		if($this->input->post('SaveChanges')):
			$error = 'NO';
			$this->form_validation->set_rules('markup_commission' , 'Markup Commission' , 'trim');
			$this->form_validation->set_rules('agent_commission' , 'Agent Commission' , 'trim');
			$this->form_validation->set_rules('SaveChanges' , 'SaveChanges' , 'trim|required');
			if($this->form_validation->run() && $error == 'NO'):
				$param['markup_commission'] = $this->input->post('markup_commission');
				$param['agent_commission'] = $this->input->post('agent_commission');	
				$param['update_ip'] = currentIp();
				$param['update_date'] = (int)$this->timezone->utc_time();
				$param['updated_by'] = (int)$this->session->userdata('ADMIN_ID');
				$this->common_model->editData($tblName, $param, '_id', new MongoDB\BSON\ObjectID($editId));
				$this->session->set_flashdata('alert_success',lang('updatesuccess'));
				redirect(correctLink('ALLDINGPROVIDERSDATA',getCurrentControllerPath('index')));
			endif;
		endif;
		$this->layouts->set_title('Edit Ding Provider | International Recharge | Instwin');
		$this->layouts->admin_view('international_recharge/ding/provider/addeditdata',array(),$data);
	}

	/***********************************************************************
	** Function name 	: changestatus
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for change status
	** Date 			: 23 March 2026
	************************************************************************/
	function changestatus($changeStatusId='',$statusType='')
	{  
		
		$this->admin_model->authCheck('edit_data');
		$param['status'] =	$statusType;
		$this->common_model->editData('ding_provider_list',$param,'_id', new MongoDB\BSON\ObjectID($changeStatusId));
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		
		redirect(correctLink('ALLDINGPROVIDERSDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name 	: deletedata
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for delete data
	** Date 			: 23 March 2026
	************************************************************************/
	function deletedata($deleteId='')
	{  
		$this->admin_model->authCheck('delete_data');
		$this->common_model->deleteData('ding_provider_list','_id', new MongoDB\BSON\ObjectID($deleteId));
		$this->session->set_flashdata('alert_success',lang('deletesuccess'));
		redirect(correctLink('ALLDINGPROVIDERSDATA',getCurrentControllerPath('index')));
	}


	/***********************************************************************
	** Function name : exportexcel
	** Developed By  : Dilip Halder
	** Purpose       : This function used for export country code list data
	** Date          : 30 January 2026
	************************************************************************/
	function exportexcel()
	{	
		$this->admin_model->authCheck('view_data');
		$data['error'] 		   = '';
		$data['activeMenu']    = 'international_recharge';
		$data['activeSubMenu'] = 'ding_countries';
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
		$searchField = $this->input->post('searchField');
		$searchValue = $this->input->post('searchValue');
		// -----------------------------------------------------------------------------//
		if(!empty($searchField) && !empty($searchValue)):
			$whereCondition['where'][$searchField] =  is_numeric($searchValue)?(int)$searchValue:$searchValue;
		endif;
		// -----------------------------------------------------------------------------//
		$tblName   = "ding_provider_list";
		$totalRows = $this->common_model->getData('count', $tblName, $whereCondition );
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
		$totalpage 				= count($totalpage);
		$data['current_page']   = $current_page;
		$data['total_page'] 	= $totalpage;
		$data['searchField'] 	=   $searchField;
		$data['searchValue'] 	=   $searchValue;
		$data['fromDate'] 		=   $fromDate;
		$data['toDate'] 		=   $toDate;
		
		// echo "<pre>";print_r($data);die();
		$this->layouts->set_title('Export CSV | Ding Region List | International Recharge | Inswin');
		$this->layouts->admin_view('international_recharge/ding/region/exportexcel',array(),$data);
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
		$data['activeMenu']    = 'international_recharge';
		$data['activeSubMenu'] = 'ding_regions';

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

		// -----------------------------------------------------------------------------//
		if(!empty($searchField) && !empty($searchValue)):
			$whereCondition['where'][$searchField] =  is_numeric($searchValue)?(int)$searchValue:$searchValue;
		endif;

		// $page = $this->input->post('pageno');
		$page = $this->input->post('pageno');
		// $page = 1;
 		$itemsPerPage = 5000;
 		$startIndex  = ($page - 1)*$itemsPerPage;
 		$shortField  = array('RegionName' => 1);
 		$resultType  = '';
		$tblName     = "ding_provider_list";
		$OrderData   = $this->common_model->getData('multiple',$tblName, $whereCondition,$shortField,$itemsPerPage,$page);
		$CSVData     = array();
		foreach($OrderData as $index => $itemsArray):
		    $CSVData1['Country ISO']       = !empty($itemsArray['CountryIso']) ? $itemsArray['CountryIso'] : 'N/A';
			$CSVData1['Region Code']       = !empty($itemsArray['RegionCode']) ? $itemsArray['RegionCode'] : 'N/A';
			$CSVData1['Region Name']       = !empty($itemsArray['RegionName']) ? $itemsArray['RegionName'] : 'N/A';
			$CSVData1['Status']             = !empty($itemsArray['status']) ? $itemsArray['status'] : 'N/A';
			array_push($CSVData, $CSVData1);
		endforeach;

		echo json_encode($CSVData);
		die();
	}
	// END OF FUNCTION		

}
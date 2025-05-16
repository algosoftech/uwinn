<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\RichText\RichText;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\ColumnDimension;
use PhpOffice\PhpSpreadsheet\Worksheet;

class Alltickets extends CI_Controller {

	public function  __construct() 
	{ 
		parent:: __construct();
		error_reporting(E_ALL ^ E_NOTICE);  
		$this->load->model(array('admin_model','emailtemplate_model','sms_model','notification_model'));
		$this->lang->load('statictext', 'admin');
		$this->load->helper('common');
	} 

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name 	: index
	 + + Developed By 	: Dilip Halder
	 + + Purpose  		: This function used for index
	 + + Date 			: 27 February 2023
	 + + Updated Date 	: Dilip Halder
	 + + Updated By   	: 14 March 2023
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index($pid='')
	{	
		$this->admin_model->authCheck();
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'products';
		$data['activeSubMenu'] 				= 	'allproducts';


		if(!empty($pid)):
			$data['productID']					=	base64_decode($pid);
			$this->session->set_userdata('productID4tickets', base64_decode($pid));
		
		
			if($this->input->get('searchField') && $this->input->get('searchValue')):
				$sField							=	$this->input->get('searchField');
				$sValue							=	$this->input->get('searchValue');
				$whereCon['like']			 	= 	array('0'=>trim($sField),'1'=>trim($sValue));
				$data['searchField'] 			= 	$sField;
				$data['searchValue'] 			= 	$sValue;
			else:
				// $whereCon['like']		 		= 	"";
				$data['searchField'] 			= 	'';
				$data['searchValue'] 			= 	'';
			endif;
					
			$whereCon['where']		 			= 	"";		
			$shortField 						= 	"";
			$whereCon['where']		 			= 	array('product_id' => (int)$data['productID']);		
			$shortField 						= 	array('tickets_seq_id'=>'DESC');
			
			$baseUrl 							= 	getCurrentControllerPath('index');
			$this->session->set_userdata('ALLTICKETSDATA',currentFullUrl());
			$qStringdata						=	explode('?',currentFullUrl());
			$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
			$tblName 							= 	'da_tickets_sequence';
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
	       //     $page = $this->uri->segment(getUrlSegment());
	       // else:
	       endif;
	           $page = 0;
			
			$data['forAction'] 					= 	$this->session->userdata('ALLTICKETSDATA'); 
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
			
			
			// echo "<pre>";
			// print_r($data);
			// die();

			
			$tblName 							= 	'da_tickets_sequence';
			$shortField 						= 	array('created_at'=> -1);
			$data['ALLDATA']				= 	$this->common_model->getTicketCount('multiple',$tblName,$whereCon,$shortField,$perPage,$page);



		else:

			$tblName 							= 	'da_tickets_sequence';
			$shortField 						= 	array('created_at'=> -1);
			$whereCon['where']			 		= 	array('status' => 'A');		
			$data['ALLDATA']				= 	$this->common_model->getTicketCount('multiple',$tblName,$whereCon,$shortField);

		endif;

		// echo '<pre>'; print_r($data['ALLDATA']); die;

		$this->layouts->set_title('All Tickets | Tickets | Dealz Arabia');
		$this->layouts->admin_view('tickets/alltickets/index',array(),$data);
	}	// END OF FUNCTION

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name 	: index
	 + + Developed By 	: Dilip Halder
	 + + Purpose  		: This function used for index
	 + + Date 			: 27 February 2023
	 + + Updated Date  : 
	 + + Updated By    :
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function addeditdata($editId='')
	{	
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'products';
		$data['activeSubMenu'] 				= 	'allproducts';

		$subadmin = $this->session->userdata('HCAP_ADMIN_TYPE');
		
		if($editId):
			
			if($subadmin != 'Sub Admin'):
			  $this->admin_model->authCheck('edit_data');
			endif;

			  $data['EDITDATA']				=	$this->common_model->getDataByParticularField('da_tickets_sequence','tickets_seq_id',(int)$editId);//echo '<pre>';print_r($data['EDITDATA']);die;
		else:

			if($subadmin != 'Sub Admin'):
			  $this->admin_model->authCheck('add_data');
			endif;
			
		endif;
		
		$error					=	'NO';

		$this->form_validation->set_rules('tickets_prefix', 'Tickets prefix', 'trim');
		$this->form_validation->set_rules('tickets_sequence_start', 'Tickets sequance start', 'trim|required');
		$this->form_validation->set_rules('tickets_sequence_end', 'Tickets sequance end', 'trim|required');
		if($this->form_validation->run() && $error == 'NO'): 

			$param['product_id']					= 	(int)$this->session->userdata('productID4tickets');
			$param['tickets_prefix']				= 	addslashes($this->input->post('tickets_prefix'));
			$param['tickets_sequence_start']		= 	addslashes($this->input->post('tickets_sequence_start'));
			$param['tickets_sequence_end']			= 	addslashes($this->input->post('tickets_sequence_end'));
			$param['total_ticket_count']			= 	$this->input->post('tickets_sequence_end') - $this->input->post('tickets_sequence_start') +1  ;
			
			if($this->input->post('CurrentDataID') ==''):
				$param['tickets_seq_id']		=	(int)$this->common_model->getNextSequence('tickets_seq_id');
				$param['creation_ip']		=	currentIp();
				$param['creation_date']		=	date('Y-m-d H:i');
				$param['created_by']		=	(int)$this->session->userdata('HCAP_ADMIN_ID');
				$param['status']			=	'A';

				$alastInsertId				=	$this->common_model->addData('da_tickets_sequence',$param);
				$this->session->set_flashdata('alert_success',lang('addsuccess'));
			else:
				$categoryId					=	$this->input->post('CurrentDataID');
				$param['update_ip']			=	currentIp();
				$param['update_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
				$param['updated_by']		=	(int)$this->session->userdata('HCAP_ADMIN_ID');
				$this->common_model->editData('da_tickets_sequence',$param,'tickets_seq_id',(int)$categoryId);
				$this->session->set_flashdata('alert_success',lang('updatesuccess'));
			endif;

				redirect(correctLink('MASTERDATAPRODUCTTYPE',getCurrentControllerPath('index/'.base64_encode($param['product_id']))));
		endif;
		
		$this->layouts->set_title('Add/Edit Winners');
		$this->layouts->admin_view('tickets/alltickets/addeditdata',array(),$data);
	}	// END OF FUNCTION	

	/***********************************************************************
	** Function name 	: changestatus
	** Developed By 	: Afsar Ali
	** Purpose  		: This function used for change status
	** Date 			: 14 APRIL 2022
	************************************************************************/
	function changestatus($changeStatusId='',$statusType='')
	{  

		$this->admin_model->authCheck('edit_data');
		$param['status']		=	$statusType;
		$this->common_model->editData('da_tickets_sequence',$param,'tickets_seq_id',(int)$changeStatusId);
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		
		redirect(correctLink('ALLTICKETSDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name 	: deletedata
	** Developed By 	: Manoj Kumar
	** Purpose  		: This function used for delete data
	** Date 			: 21 JUNE 2021
	************************************************************************/
	function deletedata($deleteId='')
	{  
		$this->admin_model->authCheck('delete_data');
		$this->common_model->deleteData('da_tickets_sequence','tickets_seq_id',(int)$deleteId);
		$this->session->set_flashdata('alert_success',lang('deletesuccess'));
		
		redirect(correctLink('ALLTICKETSDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name 	: check_tickets_prefix
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for check tickets_prefix data
	** Date 			: 04 January 2024
	************************************************************************/
	function check_tickets_prefix(){
		$this->admin_model->authCheck();
		$tickets_prefix  = $this->input->post('tickets_prefix');
		$product_id      = $this->input->post('product_id');

		if($tickets_prefix):
			$whereCon['where']   = 	array('tickets_prefix' =>$tickets_prefix);
			$tblName 		     = 	'da_tickets_sequence';
			$shortField 		 =  array('tickets_seq_id' => -1);
			$TicketData 		 = 	$this->common_model->getData('single',$tblName,$whereCon,$shortField,'0','0');
			$data_exist = 'Y'; //error
			if($TicketData['product_id'] != $product_id && !empty($TicketData)):
				// If checking exiting ticket prifix.
				$result = array('status' =>$data_exist);
				echo json_encode($result);
			elseif(empty($product_id) &&  !empty($TicketData)):
				// If checking New ticket prifix.
				$result = array('status' =>$data_exist);
				echo json_encode($result);
			else:
				// ticktek available
				$data_exist = 'N'; //error
				$result = array('status' =>$data_exist);
				echo json_encode($result);
			endif;
		endif;
		die();
	}

}
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Allinventory extends CI_Controller {

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
	 + + Developed By 	: Afsar Ali
	 + + Purpose  		: This function used for index
	 + + Date 			: 12 Nov 2022
	 + + Updated Date 	: 
	 + + Updated By   	:
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index($id='')
	{	
		$this->admin_model->authCheck();
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'emirate';
		$data['activeSubMenu'] 				= 	'allcollectionpoint';
		$data['collection_point_id']		=	$id;
		if(!empty($id)){
			$this->session->set_userdata('collection_point_id',$id);
		}
		
		
		if($this->input->get('searchField') && $this->input->get('searchValue')):
			$sField							=	$this->input->get('searchField');
			$sValue							=	$this->input->get('searchValue');
			$whereCon['like']			 	= 	array('0'=>trim($sField),'1'=>trim($sValue));
			$data['searchField'] 			= 	$sField;
			$data['searchValue'] 			= 	$sValue;
			$data['collection_point_id']	=	$this->session->userdata('collection_point_id');
		else:
			$whereCon['like']		 		= 	"";
			$data['searchField'] 			= 	'';
			$data['searchValue'] 			= 	'';
		endif;
				
		$whereCon['where']		 			= 	array('collection_point_id' => (int)base64_decode($data['collection_point_id']));	
		$shortField 						= 	array('creation_date'=> -1);
		
		$baseUrl 							= 	getCurrentControllerPath('index/'.$this->session->userdata('collection_point_id'));
		$this->session->set_userdata('ALLCOLLECTIONPOINTDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 	'da_inventory';
		$con 								= 	'';
		$totalRows 							= 	$this->common_model->getInventoryList('count',$tblName,$whereCon,$shortField,'0','0');
		
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

		$uriSegment 						= 	5;
	    $data['PAGINATION']					=	adminPagination($baseUrl,$suffix,$totalRows,$perPage,$uriSegment);

		if($this->uri->segment(getUrlSegment())):
			$page = $this->uri->segment(5);
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
		
		$data['ALLDATA'] 					= 	$this->common_model->getInventoryList('multiple',$tblName,$whereCon,$shortField,$perPage,$page);
		//echo $page;die();
		//echo '<pre>'; print_r($data['ALLDATA']);die();
		$this->layouts->set_title('Collection Point | Collection Point | Dealz Arabia');
		$this->layouts->admin_view('emirate/inventory/index',array(),$data);
	}	// END OF FUNCTION

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name : addeditdata
	 + + Developed By  : AFSAR ALI
	 + + Purpose  	   : This function used for Add Edit data
	 + + Date 		   : 12 Nov 2022
	 + + Updated Date  : 
	 + + Updated By    :
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function addeditdata($collection_point_id = '', $editId='')
	{		
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'emirate';
		$data['activeSubMenu'] 				= 	'allcollectionpoint';
		$data['collection_point_id']		=	$collection_point_id;
		
		if($editId):
			$this->admin_model->authCheck('edit_data');
			$getInventoryDetailsWhere['where']	=	array('inventory_id' => (int)$editId);
			$short = array('creation_date' => -1);
			$data['EDITDATA']				=	$this->common_model->getInventoryList('single','da_inventory',$getInventoryDetailsWhere,$short);
		else:
			$this->admin_model->authCheck('add_data');
		endif;
		
		if($this->input->post('SaveChanges')):
			$error					=	'NO';
			$this->form_validation->set_rules('product', 'Product', 'trim|required');
			$this->form_validation->set_rules('qty', 'Quantity', 'trim|required');
			$productData = explode('|', $this->input->post('product'));

			$qty = (int)$this->input->post('qty');

			if($getAvailalableStock > $qty){
				$this->session->set_flashdata('alert_error','Quentity must be less than available stocks..');
				$data['email_id_error'] = "Quentity must be less than available stocks.";
			}

			if($this->form_validation->run() && $error == 'NO'):
				
				$chkWhere['where']	= array(
											'products_id'=>(int)$productData[0],
											'collection_point_id' => (int)(base64_decode($this->input->post('collection_point_id')))
										);
				
				$ckhInv 			= $this->common_model->getData('single','da_inventory',$chkWhere);
				$CurrentDataID = '';
				if($ckhInv):
					$CurrentDataID = $ckhInv['inventory_id'];
				endif;
				//echo '<pre>';print_r($CurrentDataID);die();
				if($this->input->post('CurrentDataID') == '' && $CurrentDataID == ''):
					$param['products_id']				= 	(int)$productData[0];
					$param['qty']						=	(int)$this->input->post('qty');
					$param['available_qty']				=	(int)$this->input->post('qty');
					$param['collection_point_id']		=	(int)(base64_decode($this->input->post('collection_point_id')));

					$param['inventory_id']			=	(int)$this->common_model->getNextSequence('da_inventory');
					
					$param['creation_ip']			=	currentIp();
					$param['creation_date']			=	(int)$this->timezone->utc_time();//currentDateTime();
					$param['created_by']			=	(int)$this->session->userdata('HCAP_ADMIN_ID');
					$param['status']				=	'A';

					$alastInsertId					=	$this->common_model->addData('da_inventory',$param);
					if(!empty($alastInsertId)){
						//$params['insert_id']		=	$param['products_id'];
						$params['inventory_id']		=	$param['inventory_id'];
						$params['qty']				=	(int)$param['qty'];
						$params['creation_date']	=	(int)$this->timezone->utc_time();
						$params['status']			=	'A';
						$alastInsertId				=	$this->common_model->addData('da_inventory_history',$params);
					}
					// Manage Product inventory Stock
					
					$this->common_model->updateInventoryStock($param['products_id'],$qty);

					$this->session->set_flashdata('alert_success',lang('addsuccess'));
				else:
					if($CurrentDataID == ''):
						$inventoryId					=	$this->input->post('CurrentDataID');
					else:
						$inventoryId					=	$CurrentDataID;
					endif;
					
					$whe['where']					=	array('inventory_id' => (int)$inventoryId);
					$shortField 					= 	array('creation_date'=> -1);
					$inventoryData 					=	$this->common_model->getData('single','da_inventory',$whe,$shortField);
					$totalQTY						=	(int)$inventoryData['qty'] + (int)$this->input->post('qty');	 
					$avlQTY 						= 	(int)$inventoryData['available_qty'] + (int)$this->input->post('qty');
					
					$params['qty']					=	(int)$totalQTY;
					$params['available_qty']		=	(int)$avlQTY;
					$params['update_ip']			=	currentIp();
					$params['update_date']			=	(int)$this->timezone->utc_time();//currentDateTime();
					$params['updated_by']			=	(int)$this->session->userdata('HCAP_ADMIN_ID');
					$this->common_model->editData('da_inventory',$params,'inventory_id',(int)$inventoryId);
					// Manage Product inventory Stock
					$this->common_model->updateInventoryStock((int)$productData[0],$qty);
					
					//$param['insert_id']				=	(int)$inventoryId;
					$param['inventory_id']			=	(int)$inventoryData['inventory_id'];
					$param['qty']					=	(int)$this->input->post('qty');
					$param['status']				=	'A';
					$param['creation_date']			=	(int)$this->timezone->utc_time();
					$alastInsertId				=	$this->common_model->addData('da_inventory_history',$param);
					$this->session->set_flashdata('alert_success',lang('updatesuccess'));
				endif;

				redirect(correctLink('ALLCOLLECTIONPOINTDATA',getCurrentControllerPath('index')));
			endif;
		endif;
		$whereCon1['where'] 	=	array('status'=>'A','stock'=> array('$gt'=> 0));
		$data['productList']	=	$this->common_model->getData('multiple','da_products',$whereCon1);
		$this->layouts->set_title('Add/Edit Inventory | Dealz Arabia');
		$this->layouts->admin_view('emirate/inventory/addeditdata',array(),$data);
	}	// END OF FUNCTION	

	/***********************************************************************
	** Function name : changestatus
	** Developed By : Manoj Kumar
	** Purpose  : This function used for change status
	** Date : 21 JUNE 2021
	************************************************************************/
	function changestatus($changeStatusId='',$statusType='')
	{  
		$this->admin_model->authCheck('edit_data');
		$param['status']		=	$statusType;
		$this->common_model->editData('da_emirate_collection_point',$param,'collection_point_id',(int)$changeStatusId);
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		
		redirect(correctLink('ALLCOLLECTIONPOINTDATA',getCurrentControllerPath('index')));
	}
	/***********************************************************************
	** Function name : getdatabyajax
	** Developed By : AFSAR ALI
	** Purpose  : This function used for change status
	** Date : 12 NOV 2022
	************************************************************************/
	function getdatabyajax($id='')
	{
		if($id == ''){
			$productID = $this->input->post('id');
		}else{
			$productID = $id;
		}
		$stock = $this->common_model->getPaticularFieldByFields('inventory_stock','da_products','products_id',(int)$productID);
		echo $stock;
	}
}
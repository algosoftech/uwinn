<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Allproductrequest extends CI_Controller {

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
	 + + Date 			: 23 Nov 2022
	 + + Updated Date 	: 
	 + + Updated By   	:
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index()
	{	
		$this->admin_model->authCheck();
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'emirate';
		$data['activeSubMenu'] 				= 	'Allproductrequest';
		
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
				
		$whereCon['where']		 			= 	array('status'=> array('$ne'=> ' '));	
		$shortField 						= 	array('creation_date'=> -1);
		
		$baseUrl 							= 	getCurrentControllerPath('index');
		$this->session->set_userdata('ALLCOLLECTIONPOINTDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 	'da_product_request';
		$con 								= 	'';
		$totalRows 							= 	$this->common_model->getProductRequestList('count',$tblName,$whereCon,$shortField,'0','0');
		//echo $totalRows;die();
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
		
		$data['ALLDATA'] 					= 	$this->common_model->getProductRequestList('multiple',$tblName,$whereCon,$shortField,$perPage,$page);
		//echo '<pre>'; print_r($data['ALLDATA']);die();
		$this->layouts->set_title('Product Request | Collection Point | Dealz Arabia');
		$this->layouts->admin_view('emirate/product_request/index',array(),$data);
	}	// END OF FUNCTION

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name : addeditdata
	 + + Developed By  : AFSAR ALI
	 + + Purpose  	   : This function used for Add Edit data
	 + + Date 		   : 24 Nov 2022
	 + + Updated Date  : 
	 + + Updated By    :
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function addeditdata($editId='')
	{		
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'emirate';
		$data['activeSubMenu'] 				= 	'Allproductrequest';
		if($editId):
			$this->admin_model->authCheck('edit_data');
			$where['where']	=	array('request_id' => (int)$editId);
			$short = array('creation_date' => -1);
			$data['EDITDATA']				=	$this->common_model->getData('single','da_product_request',$where,$short);

			$where2['where'] = array('products_id'=> $data['EDITDATA']['product_id']);
			$praductData 	= $this->common_model->getData('single','da_products',$where2,$short);

			$data['EDITDATA']['stock']		=	$praductData['stock'];
			$data['EDITDATA']['product_name']		=	$praductData['title'];
			
			//echo '<pre>';print_r($data);die();
		else:
			$this->session->set_flashdata('alert_error','Request Not Found.');
			redirect(correctLink('ALLCOLLECTIONPOINTDATA',getCurrentControllerPath('index')));
		endif;
		
		if($this->input->post('SaveChanges')):
			$error					=	'NO';
			$this->form_validation->set_rules('product', 'Product', 'trim|required');
			$this->form_validation->set_rules('qty', 'Quantity', 'trim|required');

			$productData = explode('|', $this->input->post('product'));

			
			$qty = (int)$this->input->post('qty');

			if($praductData['stock'] > $qty){
				$this->session->set_flashdata('alert_error','Quantity must be less than available stocks.');
				$data['email_id_error'] = "Quantity must be less than available stocks.";
			}

			if($this->form_validation->run() && $error == 'NO'): 
				if($this->input->post('CurrentDataID') ==''):
					$this->session->set_flashdata('alert_error','Request Not Found.');
					redirect(correctLink('ALLCOLLECTIONPOINTDATA',getCurrentControllerPath('index')));
				else:
					//Add Quantity in Inventory
					$request_id						=	$this->input->post('CurrentDataID');
					$inventoryId					=	$this->input->post('inventory_id');
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
					
					$param['insert_id']				=	$inventoryData;
					$param['inventory_id']			=	(int)$inventoryData['inventory_id'];
					$param['qty']					=	(int)$this->input->post('qty');
					$param['status']				=	'A';
					$param['creation_date']			=	(int)$this->timezone->utc_time();
					$alastInsertId					=	$this->common_model->addData('da_inventory_history',$param);

					//Change Request Product Status
					$Uparam['sent_qty']				=	(int)$this->input->post('qty');
					$Uparam['sent_date']			=	(int)$this->timezone->utc_time();
					$Uparam['status']				=	'C';
					$this->common_model->editData('da_product_request',$Uparam,'request_id',(int)$request_id);
					$this->session->set_flashdata('alert_success',lang('updatesuccess'));
				endif;

				redirect(correctLink('ALLCOLLECTIONPOINTDATA',getCurrentControllerPath('index')));
			endif;
		endif;
		$whereCon1['where'] 	=	array('status'=>'A','stock'=> array('$gt'=> 0));
		$data['productList']	=	$this->common_model->getData('multiple','da_products',$whereCon1);
		//echo '<pre>';print_r($data);die();
		$this->layouts->set_title('Add/Edit Product Request | Dealz Arabia');
		$this->layouts->admin_view('emirate/product_request/addeditdata',array(),$data);
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
		$this->common_model->editData('da_product_request',$param,'request_id',(int)$changeStatusId);
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
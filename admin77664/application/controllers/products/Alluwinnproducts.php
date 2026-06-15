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

class Alluwinnproducts extends CI_Controller {

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
	 + + Date 			: 23 October 2023
	 + + Updated Date 	: 
	 + + Updated By   	:
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index()
	{	
		$this->admin_model->authCheck('view_data');
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'producrs';
		$data['activeSubMenu'] 				= 	'alluwinnproducts';
		
		if($this->input->get('searchField') && $this->input->get('searchValue')):
			$sField							=	$this->input->get('searchField');
			$sValue							=	$this->input->get('searchValue');
				
			if(is_numeric($sValue)):
			 $whereCon['where']		 			= 	array($sField => (int)$sValue);		
			else:
			 $whereCon['like']			 	= 	array('0'=>trim($sField),'1'=>trim($sValue));
			endif;

			$data['searchField'] 			= 	$sField;
			$data['searchValue'] 			= 	$sValue;
		else:
			$whereCon['like']		 		= 	"";
			$data['searchField'] 			= 	'';
			$data['searchValue'] 			= 	'';
		endif;
				
		$shortField 						= 	array('creation_date'=>-1);
		
		$baseUrl 							= 	getCurrentControllerPath('index');
		$this->session->set_userdata('ALLLOTOPRODUCTSDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 	'uw_products';
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

		$this->layouts->set_title('All Products | Products | UWINN');
		$this->layouts->admin_view('products/alllottoproducts/index',array(),$data);
	}	// END OF FUNCTION

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name : addeditdata
	 + + Developed By  : Dilip Halder
	 + + Purpose  	   : This function used for Add Edit data
	 + + Date 		   : 23 October 2023
	 + + Updated Date  : Dilip Halder
	 + + Updated By    : 26 April 2024
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function addeditdata($editId='')
	{		
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'products';
		$data['activeSubMenu'] 				= 	'alluwinnproducts';
		
		if($editId):
			$this->admin_model->authCheck('edit_data');
			$data['EDITDATA']				=	$this->common_model->getDataByParticularField('uw_products','products_id',(int)$editId);
		else:
			$this->admin_model->authCheck('add_data');
		endif;

		if($this->input->post('SaveChanges')):
			$error					=	'NO';
			$TotalDataCount			=	$this->input->post('TotalDataCount');
			if($this->input->post('is_color') == 'Y'):
				if($TotalDataCount):  
					for($i=0; $i <= $TotalDataCount; $i++):
						if($this->input->post('color'.$i)):
							$this->form_validation->set_rules('color'.$i, 'color', 'trim');
							if($this->input->post('S'.$i)):
								$this->form_validation->set_rules('S'.$i, 'color', 'trim');
							elseif($this->input->post('M'.$i)):
								$this->form_validation->set_rules('M'.$i, 'color', 'trim');
							elseif($this->input->post('L'.$i)):
								$this->form_validation->set_rules('L'.$i, 'color', 'trim');
							elseif($this->input->post('XL'.$i)):
								$this->form_validation->set_rules('XL'.$i, 'color', 'trim');
							elseif($this->input->post('XXL'.$i)):
								$this->form_validation->set_rules('XXL'.$i, 'color', 'trim');
							elseif($this->input->post('FRS'.$i)):
								$this->form_validation->set_rules('FRS'.$i, 'color', 'trim');
							else:
								$this->form_validation->set_rules('S'.$i, 'color', 'trim');
							endif;
						endif;
						$i++;
					endfor;
				endif;
			endif;

			$this->form_validation->set_rules('category_id', 'Category', 'trim|required');
			$this->form_validation->set_rules('sub_category_id', 'Sub Category', 'trim|required');
			$this->form_validation->set_rules('title', 'Title', 'trim|required');
			$this->form_validation->set_rules('description', 'Description', 'trim');

			$this->form_validation->set_rules('image', 'Image', 'trim');
			$this->form_validation->set_rules('product_image_alt', 'Alt Text', 'trim');
			$this->form_validation->set_rules('straight_add_on_amount', 'Straight ADE Points', 'trim|required');
			$this->form_validation->set_rules('rumble_add_on_amount', 'Rumble Mix ADE Points', 'trim|required');
			$this->form_validation->set_rules('reverse_add_on_amount', 'Reverse ADE Points', 'trim|required');
			$this->form_validation->set_rules('commingSoon', 'Comming Soon', 'trim|required');
			$this->form_validation->set_rules('countdown_status', 'Countdown', 'trim');
			$this->form_validation->set_rules('validuptodate', 'Valid Upto Date', 'trim|required');
			$this->form_validation->set_rules('validuptotime', 'Valid Upto Time', 'trim|required');
			$this->form_validation->set_rules('lotto_type', 'Lotto Type', 'trim|required');
			$this->form_validation->set_rules('is_show_closing', 'Is Show Closing', 'trim');
			$this->form_validation->set_rules('draw_date', 'Campaigns Draw Date', 'trim');
			$this->form_validation->set_rules('draw_time', 'Campaigns Draw Tme', 'trim');
			// $this->form_validation->set_rules('lotto_range', 'Number Range', 'trim|required');
			$this->form_validation->set_rules('lotto_range_start', 'Number Range Start', 'trim|required');
			$this->form_validation->set_rules('lotto_range_end', 'Number Range End', 'trim|required');
			$this->form_validation->set_rules('lotto_range_prefix', 'Lotto Range Prefix', 'trim');
			$this->form_validation->set_rules('enable_number_prefix', 'Enable Number Prifix', 'trim|required');
			$this->form_validation->set_rules('ticket_number_repeat', 'Ticket Number Prifix', 'trim|required');
			$this->form_validation->set_rules('seq_order', 'Seq Order', 'trim');
			$this->form_validation->set_rules('show_as_banner', 'Seq Order', 'trim');
			$this->form_validation->set_rules('ticket_count_per_campaign', 'ticket count per campaign', 'trim');
			$this->form_validation->set_rules('enable_ticket_for_campaign', 'Enable Tickect for campaign', 'trim|required');
			$this->form_validation->set_rules('show_on[]', 'Show On', 'trim|required');
			$this->form_validation->set_rules('enable_super_ball', 'Enable Super Ball', 'trim');
			$this->form_validation->set_rules('super_ball_type'  , 'Super Ball Type', 'trim');
			$this->form_validation->set_rules('superbal_range_start' , 'Number Range Start', 'trim');
			$this->form_validation->set_rules('superbal_range_end' , 'Number Range End', 'trim');
			$this->form_validation->set_rules('SaveChanges', 'SaveChanges', 'trim|required');
			
			if($this->form_validation->run() && $error == 'NO'):

				/* Product Image Section start*/
				if($_FILES['product_image']['name']):
					// $ufileName						= 	$_FILES['product_image']['name'];
					$ufileName						= 	str_replace(" ","_",$_FILES['product_image']['name']);
					$utmpName						= 	$_FILES['product_image']['tmp_name'];
					$ufileExt         				= 	pathinfo($ufileName);
					// $unewFileName 					= 	$this->common_model->microseconds().'.'.$ufileExt['extension'];

					$unewFileName 					= 	$_FILES['slider_image']['name'];
					$filePath =  fileFCPATH .'assets/lottoproductsImage/'.$_FILES['slider_image']['name'];
					 
					if(file_exists($filePath)):
						$unewFileName =	$ufileExt['filename'] .'_'.$this->common_model->random_strings(8).'.'.$ufileExt['extension'];
					endif;

					$this->load->library("upload_crop_img");
					$imageName = $data['EDITDATA']['product_image'];
					if($imageName):
						$this->upload_crop_img->_delete_image(trim($imageName)); 
					endif;
					$uimageLink						=	$this->upload_crop_img->_upload_image($ufileName,$utmpName,'lottoproductsImage',$unewFileName,'');
					if($uimageLink != 'UPLODEERROR'):
						$param['product_image']		= 	$uimageLink;
					endif;
				endif;
				$param['product_image_alt']	= 	addslashes($this->input->post('product_image_alt'));
				/* Product Image Section End*/

				/* App Image Section start*/
				if($_FILES['app_image']['name']):
					// $ufileName						= 	$_FILES['product_image']['name'];
					$ufileName_app						= 	str_replace(" ","_",$_FILES['app_image']['name']);
					$utmpName_app						= 	$_FILES['app_image']['tmp_name'];
					$ufileExt_app         				= 	pathinfo($ufileName_app);
					// $unewFileName 					= 	$this->common_model->microseconds().'.'.$ufileExt_app['extension'];

					$unewFileName_app 					= 	$_FILES['slider_image']['name'];
					$filePath =  fileFCPATH .'assets/lottoproductsImage/'.$_FILES['slider_image']['name'];
					 
					if(file_exists($filePath)):
						$unewFileName_app =	$ufileExt_app['filename'] .'_'.$this->common_model->random_strings(8).'.'.$ufileExt['extension'];
					endif;

					$this->load->library("upload_crop_img");
					$imageName = $data['EDITDATA']['app_image'];
					if($imageName):
						$this->upload_crop_img->_delete_image(trim($imageName)); 
					endif;
					$uimageLink_app						=	$this->upload_crop_img->_upload_image($ufileName_app,$utmpName_app,'lottoproductsImage',$unewFileName_app,'');
					if($uimageLink_app != 'UPLODEERROR'):
						$param['app_image']		= 	$uimageLink_app;
					endif;
				endif;
				$param['app_image_alt']	= 	addslashes($this->input->post('app_image_alt'));
				/* App Image Section End */


				/* Campaign Image Section Start*/
				// if($_FILES['campaign_image']['name']):
				// 	// $ufileName						= 	$_FILES['product_image']['name'];
				// 	$ufileName_app						= 	str_replace(" ","_",$_FILES['campaign_image']['name']);
				// 	$utmpName_app						= 	$_FILES['campaign_image']['tmp_name'];
				// 	$ufileExt_app         				= 	pathinfo($ufileName_app);
				// 	// $unewFileName 					= 	$this->common_model->microseconds().'.'.$ufileExt_app['extension'];

				// 	$unewFileName_app 					= 	$_FILES['slider_image']['name'];
				// 	$filePath =  fileFCPATH .'assets/lottoproductsImage/'.$_FILES['slider_image']['name'];
					 
				// 	if(file_exists($filePath)):
				// 		$unewFileName_app =	$ufileExt_app['filename'] .'_'.$this->common_model->random_strings(8).'.'.$ufileExt['extension'];
				// 	endif;

				// 	$this->load->library("upload_crop_img");
				// 	$imageName = $data['EDITDATA']['campaign_image'];
				// 	if($imageName):
				// 		$this->upload_crop_img->_delete_image(trim($imageName)); 
				// 	endif;
				// 	$uimageLink_app						=	$this->upload_crop_img->_upload_image($ufileName_app,$utmpName_app,'lottoproductsImage',$unewFileName_app,'');
				// 	if($uimageLink_app != 'UPLODEERROR'):
				// 		$param['campaign_image']		= 	$uimageLink_app;
				// 	endif;
				// endif;
				// $param['campaign_image_alt']	= 	addslashes($this->input->post('campaign_image_alt'));
				/* Campaign Image Section End */


				$categoryData						=	explode('_____',$this->input->post('category_id'));
				$sub_categoryData					= 	explode('____',$this->input->post('sub_category_id'));
				
				$param['title']						= 	addslashes($this->input->post('title'));
				$param['title_slug']				= 	url_title(strtolower($this->input->post('title').' '.date('dMY',strtotime($this->input->post('draw_date')))));
				$param['description']				= 	addslashes($this->input->post('description'));
				$param['category_id']				= 	(int)$categoryData[0];
				$param['category_name']				= 	$categoryData[1];
				$param['sub_category_id']			= 	(int)$sub_categoryData[0];
				$param['sub_category_name']			= 	$sub_categoryData[1];

				// $param['stock']						= 	(int)addslashes($this->input->post('stock'));
				// $param['inventory_stock']			=	(int)addslashes($this->input->post('stock'));
				// $param['totalStock']					= 	(int)addslashes($this->input->post('stock'));
				// $param['target_stock']				= 	(int)addslashes($this->input->post('target_stock'));

				$param['stock']						= 	(int)1000;
				$param['inventory_stock']			=	(int)1000;
				$param['totalStock']				= 	(int)1000;
				$param['target_stock']				= 	(int)1000;
				$param['adepoints']					= 	(float)$this->input->post('straight_add_on_amount');
				$param['straight_add_on_amount']	= 	(float)$this->input->post('straight_add_on_amount');
				$param['rumble_add_on_amount']		=	(float)$this->input->post('rumble_add_on_amount');
				$param['reverse_add_on_amount']		=	(float)$this->input->post('reverse_add_on_amount');
				$param['commingSoon']				= 	addslashes($this->input->post('commingSoon'));
				$param['countdown_status']			=	$this->input->post('countdown_status');
				$param['validuptodate']				= 	addslashes($this->input->post('validuptodate'));
				$param['validuptotime']				= 	addslashes($this->input->post('validuptotime'));
				$param['lotto_type']				=	(int)$this->input->post('lotto_type');
				$param['is_show_closing']			= 	addslashes($this->input->post('is_show_closing'));
				$param['draw_date']					= 	addslashes($this->input->post('draw_date'));
				$param['draw_time']					= 	addslashes($this->input->post('draw_time'));
				$param['seq_order']					=	(int)$this->input->post('seq_order');
				$param['enable_number_prefix']		=	$this->input->post('enable_number_prefix');
				$param['lotto_range_prefix']		=	(int)$this->input->post('lotto_range_prefix');
				// $param['lotto_range']				=	(int)$this->input->post('lotto_range');
				$param['lotto_range_start']			=	(int)$this->input->post('lotto_range_start');
				$param['lotto_range_end']			=	(int)$this->input->post('lotto_range_end');
				$param['ticket_number_repeat']		=	$this->input->post('ticket_number_repeat');
				$param['show_as_banner']			=	$this->input->post('show_as_banner');
				$param['remarks']					= 	'lotto-products';
				$param['campaign_price']			=	(float)$this->input->post('straight_add_on_amount');
				$param['ticket_count_per_campaign']	=	$this->input->post('ticket_count_per_campaign');
				$param['enable_ticket_for_campaign']=	$this->input->post('enable_ticket_for_campaign');
				$param['show_on']					=	$this->input->post('show_on');
				$param['text_field_1']				=	$this->input->post('text_field_1');
				$param['text_field_2']				=	$this->input->post('text_field_2');
				$param['text_field_3']				=	$this->input->post('text_field_3');
				
				$param['enable_super_ball']	   		=   $this->input->post('enable_super_ball');
				$param['super_ball_type']	   		=   $this->input->post('super_ball_type');
				$param['superbal_range_start'] 		=   $this->input->post('superbal_range_start');
				$param['superbal_range_end']   		=   $this->input->post('superbal_range_end');
				$param['enable_raffle_eligibility'] = $this->input->post('enable_raffle_eligibility');
				$param['raffle_eligible_amount']    = $this->input->post('raffle_eligible_amount');
				$param['raffle_draw_eligible_date']     = $this->input->post('raffle_draw_eligible_date');
				$param['raffle_draw_announcement_date'] = $this->input->post('raffle_draw_announcement_date');
				
				if($this->input->post('CurrentDataID') ==''):
					$param['status']			=	'A';
					$param['products_id']		=	(int)$this->common_model->getNextSequence('uw_products');
					$param['product_seq_id']	=	$this->common_model->getNextIdSequence('product_seq_id','lotto_products');
					$param['creation_ip']		=	currentIp();
					$param['creation_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
					$param['created_by']		=	(int)$this->session->userdata('UW_ADMIN_ID');
					$alastInsertId				=	$this->common_model->addData('uw_products',$param);
					$this->session->set_flashdata('alert_success',lang('addsuccess'));
				else:

					$categoryId					=	$this->input->post('CurrentDataID');
					$param['target_stock']		= 	(int)addslashes($this->input->post('target_stock'));
					$param['update_ip']			=	currentIp();
					$param['update_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
					$param['updated_by']		=	(int)$this->session->userdata('UW_ADMIN_ID');
					$this->common_model->editData('uw_products',$param,'products_id',(int)$categoryId);
					$this->session->set_flashdata('alert_success',lang('updatesuccess'));
				endif;

				$products_id   = $alastInsertId['products_id']?$alastInsertId['products_id']:$editId;
				$products_oid  = $alastInsertId['_id']->{'$id'}?$alastInsertId['_id']->{'$id'}:$data['EDITDATA']['_id']->{'$id'};

				// Draw Date Validations...
				$DrawDate 	 = $data['EDITDATA']['draw_date'];
				$DrawTime    = $data['EDITDATA']['draw_time'];
				$NewDrawDate = $this->input->post('draw_date');
				$NewDrawTime = $this->input->post('draw_time');

				$DrawDateTime    = strtotime($DrawDate.' '.$DrawTime); 
				$NewDrawDateTime = strtotime($NewDrawDate.' '.$NewDrawTime); 

				/// Storing Draw Date in Data in  uw_products_draw_records..
				if($DrawDateTime != $NewDrawDateTime || empty($editId) ):
					$DrawParam['draw_id']  			   =  $param['draw_id']?(int)$param['draw_id']:(int)$this->common_model->getNextSequence('uw_products_draw_records');
					$DrawParam['products_id']		   =  (int)$products_id;
					$DrawParam['products_oid']		   =  new MongoDB\BSON\ObjectId($products_oid);
					$DrawParam['draw_date']		   	   =  $this->input->post('draw_date');
					$DrawParam['draw_time']		       =  $this->input->post('draw_time');
					$DrawParam['creation_ip']		   =  currentIp();
					$DrawParam['creation_date']		   =  (int)$this->timezone->utc_time();//currentDateTime();
					$DrawParam['created_by']		   =  (int)$this->session->userdata('UW_ADMIN_ID');
					$DrawInsertId				       =  $this->common_model->addData('uw_products_draw_records',$DrawParam);
					
					// Updating present draw ID 
					$draw_id   = $DrawInsertId['draw_id'];
					$draw_oid  = $DrawInsertId['_id']->{'$id'};
					$ProductParam['draw_id']		 =  (int)$draw_id;
					$ProductParam['draw_oid']		 = 	new MongoDB\BSON\ObjectId($draw_oid);
					$ProductParam['target_stock']	 = 	(int)addslashes($this->input->post('target_stock'));
					$ProductParam['update_ip']		 =	currentIp();
					$ProductParam['update_date']	 =	(int)$this->timezone->utc_time();//currentDateTime();
					$ProductParam['updated_by']		 =	(int)$this->session->userdata('UW_ADMIN_ID');
					$this->common_model->editData('uw_products',$ProductParam,'products_id',(int)$products_id);
				endif;
				redirect(correctLink('MASTERDATAPRODUCTTYPE',getCurrentControllerPath('index')));
			endif;
		endif;
		
		// echo "<pre>";print_r($data );die();
		$this->layouts->set_title('Add/Edit Product | Products | UWINN');
		$this->layouts->admin_view('products/alllottoproducts/addeditdata',array(),$data);
	}	// END OF FUNCTION			

	/***********************************************************************
	** Function name 	: changestatus
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for change status
	** Date 			: 21 JUNE 2021
	************************************************************************/
	function changestatus($changeStatusId='',$statusType='')
	{  
		$this->admin_model->authCheck('edit_data');
		$param['status']		=	$statusType;
		$this->common_model->editData('uw_products',$param,'products_id',(int)$changeStatusId);
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		
		redirect(correctLink('ALLLOTOPRODUCTSDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name 	: deletedata
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for delete data
	** Date 			: 21 JUNE 2021
	************************************************************************/
	function deletedata($deleteId='')
	{  
		$this->admin_model->authCheck('delete_data');
		$this->common_model->deleteData('uw_products','products_id',(int)$deleteId);
		$this->session->set_flashdata('alert_success',lang('deletesuccess'));
		
		redirect(correctLink('ALLLOTOPRODUCTSDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name 	: getsub_categoryData
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for delete data
	** Date 			: 05 APRIL 2022
	************************************************************************/
	public function getsubcategoryData(){
		$categoryData			=	explode('_____',$this->input->post('category_id'));
		$categoryId				= 	(int)$categoryData[0];
		$whereCon['where']		= 	array('category_id' => $categoryId);	
		$sub_categoryData 				=   $this->common_model->getData('multiple','uw_sub_category',$whereCon);
		$sub_categoryIdData 			=   explode('_____',$this->input->post('sub_category_id'));
		$sub_category					= 	(int)$sub_categoryIdData[0];
		$sub_categoryName				= 	(int)$sub_categoryIdData[1];
		if($sub_categoryData <> ""):
			$html = '<option value="">Select Sub Category</option>';
			if($sub_category == ''): $sub_category = $sub_categoryData[0]['sub_category_id'];endif;
			$i=1;foreach($sub_categoryData as $sub_categoryData):
			//echo '<pre>';print_r($sub_categoryData);die;
				if($sub_category == stripslashes($sub_categoryData['sub_category_id'])): $select = 'selected="selected"'; else: $select = ''; endif;
		        $html .='<option '.$select.' value="'.stripslashes($sub_categoryData["sub_category_id"]).'____'.stripslashes($sub_categoryData["sub_category"]).'">'.stripslashes($sub_categoryData["sub_category"]).'</option>';
	        $i++;endforeach;
	    else:
	    	$html = '<option value="">No Sub Category</option>';
	    endif;
        echo $html;
        die();
	}

	/***********************************************************************
	** Function name : exportexcel
	** Developed By : Ravi Negi
	** Purpose  : This function used for export deleted users data
	** Date : 31 AUG 2021
	** Updated Date : 10 NOV 2021
	** Updated By   : Ravi Negi
	************************************************************************/
	function exportexcel()
	{  
		$this->admin_model->authCheck('view_data');
		/* Export excel button code */
		$wcon['where']          =   '';
		$data        			=   $this->common_model->getData('multiple','uw_products',$wcon);//echo '<pre>';print_r($data);die;

        $spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'Sl.No');
		$sheet->setCellValue('B1', 'HOSPITAL ID');
		$sheet->setCellValue('C1', 'HOSPITAL NAME');
		$sheet->setCellValue('D1', 'PHONE');
		$sheet->setCellValue('E1', 'ADDRESS');
		$sheet->setCellValue('F1', 'sub_category');
		$sheet->setCellValue('G1', 'STATE');
		$sheet->setCellValue('H1', 'CREATION DATE');
		$sheet->setCellValue('I1', 'CREATION IP');
		$sheet->setCellValue('J1', 'VACCINATION STATUS');
		
		$slno = 1;
		$start = 2;
			foreach($data as $d){
				$sheet->setCellValue('A'.$start, $slno);
				$sheet->setCellValue('B'.$start, $d['product_seq_id']);
				$sheet->setCellValue('C'.$start, $d['title']);
				$sheet->setCellValue('D'.$start, $d['phone']);
				$sheet->setCellValue('E'.$start, $d['address']);
				$sheet->setCellValue('F'.$start, $d['sub_category_name']);
				$sheet->setCellValue('G'.$start, $d['state_name']);
				$sheet->setCellValue('H'.$start, date('d-m-Y H:i:s', $d['creation_date']));
				$sheet->setCellValue('I'.$start, $d['creation_ip']);
				$sheet->setCellValue('J'.$start, $d['vaccination_status']);
				
				
		$start = $start+1;
		$slno = $slno+1;
			}
		$styleThinBlackBorderOutline = [
					'borders' => [
						'allBorders' => [
							'borderStyle' => Border::BORDER_THIN,
							'color' => ['argb' => 'FF000000'],
						],
					],
				];
		//Font BOLD
		$sheet->getStyle('A1:I1')->getFont()->setBold(true);		
		$sheet->getStyle('A1:I1000')->applyFromArray($styleThinBlackBorderOutline);
		//Alignment
		//fONT SIZE
		$sheet->getStyle('A1:D10')->getFont()->setSize(12);
		$sheet->getStyle('A1:D2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
		$sheet->getStyle('A2:D100')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		//Custom width for Individual Columns
		$sheet->getColumnDimension('A')->setWidth(5);
		$sheet->getColumnDimension('B')->setWidth(15);
		$sheet->getColumnDimension('C')->setWidth(30);
		$sheet->getColumnDimension('D')->setWidth(30);
		$sheet->getColumnDimension('E')->setWidth(15);
		$sheet->getColumnDimension('F')->setWidth(15);
		$sheet->getColumnDimension('G')->setWidth(15);
		$sheet->getColumnDimension('H')->setWidth(30);
		$sheet->getColumnDimension('I')->setWidth(30);
		

		$curdate = date('d-m-Y H:i:s');
		$writer = new Xlsx($spreadsheet);
		$filename = 'Hospital-list'.$curdate;
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
		//endif;
		/* Export excel END */
	}

    /*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name 	: index
	 + + Developed By 	: Dilip Halder
	 + + Purpose  		: This function used for index
	 + + Date 			: 05 APRIL 2022
	 + + Updated Date 	: 
	 + + Updated By   	:
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function getAllusers($id='')
	{	
		$this->admin_model->authCheck('view_data');
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'products';
		$data['activeSubMenu'] 				= 	'alluwinnproducts';
		
		if($this->input->get('searchField') && $this->input->get('searchValue')):
			$sField							=	$this->input->get('searchField');
			$sValue							=	$this->input->get('searchValue');
			$whereCon['search']			 	= 	array('0'=>trim($sField),'1'=>trim($sValue));
			$data['searchField'] 			= 	$sField;
			$data['searchValue'] 			= 	$sValue;
		else:
			$whereCon['search']		 		= 	"";
			$data['searchField'] 			= 	'';
			$data['searchValue'] 			= 	'';
		endif;
				
		$whereCon['where']		 			= 	array('product_id'=>(int)$id);		
		$shortField 						= 	array('user_id'=>-1);
		
		$baseUrl 							= 	getCurrentControllerPath('getAllusers/'.$id);
		$this->session->set_userdata('ALLLOTOPRODUCTSDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 	'uw_orders';
		$con 								= 	'';
		$totalRows 							= 	$this->geneal_model->getOrderData('count',$tblName,$whereCon,$shortField,'','');
		//echo $totalRows; die();
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

		$uriSegment 						= 	5;//getUrlSegment();
	    $data['PAGINATION']					=	adminPagination($baseUrl,$suffix,$totalRows,$perPage,$uriSegment);

       if($this->uri->segment($uriSegment)):
           $page = $this->uri->segment($uriSegment);
       else:
           $page = 0;
       endif;
		
		$data['forAction'] 					= 	$baseUrl; 
		if($totalRows):
			$first							=	(int)1;
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
		
		$data['ALLDATA'] 					= 	$this->geneal_model->getlottoOrderData('multiple',$tblName,$whereCon,$shortField,$page,$perPage);

		$this->layouts->set_title('Campaign orders List | UWINN');
		$this->layouts->admin_view('products/alllottoproducts/getallusers',array(),$data);
	}	// END OF FUNCTION

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name 	: getCoupon
	 + + Developed By 	: Dilip Halder
	 + + Purpose  		: This function used for get all coupon
	 + + Date 			: 06 APRIL 2022
	 + + Updated Date 	: 
	 + + Updated By   	:
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function getCoupon($id='')
	{	
		$this->admin_model->authCheck('view_data');
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'coupons';
		$data['activeSubMenu'] 				= 	'allcoupons';
		$data['products_id']				=	$id;
		
		if($this->input->get('searchField') && $this->input->get('searchValue')):
			$sField							=	$this->input->get('searchField');
			$sValue							=	$this->input->get('searchValue');
			$whereCon['search']			 	= 	array('0'=>trim($sField),'1'=>trim($sValue));
			$data['searchField'] 			= 	$sField;
			$data['searchValue'] 			= 	$sValue;
		else:
			$whereCon['search']		 		= 	"";
			$data['searchField'] 			= 	'';
			$data['searchValue'] 			= 	'';
		endif;
				
		$whereCon['where']		 			= 	array('product_id'=>(int)$id);		
		$shortField 						= 	array('user_id'=>-1);
		
		$baseUrl 							= 	getCurrentControllerPath('getCoupon/'.$id);
		$this->session->set_userdata('ALLLOTOPRODUCTSDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 	'uw_coupons';
		$con 								= 	'';
		$totalRows 							= 	$this->geneal_model->getCouponData('count',$tblName,$whereCon,$shortField,'','');
		
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

		$uriSegment 						= 	5;//getUrlSegment();
	    $data['PAGINATION']					=	adminPagination($baseUrl,$suffix,$totalRows,$perPage,$uriSegment);

	   if($this->uri->segment($uriSegment)):
	       $page = $this->uri->segment($uriSegment);
	   else:
	       $page = 0;
	   endif;
		
		$data['forAction'] 					= 	$baseUrl; 
		if($totalRows):
			$first							=	(int)1;
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

		$data['ALLDATA'] 					= 	$this->geneal_model->getCouponData('multiple',$tblName,$whereCon,$shortField,$page,$perPage);

		$this->layouts->set_title('All Coupons | Coupons | UWINN');
		$this->layouts->admin_view('products/alllottoproducts/allcoupons',array(),$data);
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name 	: couponExportExcel
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for export deleted users data
	** Date 			: 23 October 2023
	** Updated Date 	: 
	** Updated By   	: 
	************************************************************************/
	function couponExportExcel($pid='')
	{  
		$this->admin_model->authCheck('view_data');
		/* Export excel button code */
		$wcon['where']          =   array( 'product_id' => (int)$pid );
		$shortField 			= 	array('user_id'=>-1);
		$data        			=   $this->geneal_model->getCouponData('multiple','uw_coupons',$wcon,$shortField,[],[]);
		
	    $spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'Sl.No');
		$sheet->setCellValue('B1', 'NAME');
		$sheet->setCellValue('C1', 'MOBILE NO.');
		$sheet->setCellValue('D1', 'EMAIL ID');
		$sheet->setCellValue('E1', 'PRODUCT ID');
		$sheet->setCellValue('F1', 'PRODUCT NAME');
		$sheet->setCellValue('G1', 'AED');
		$sheet->setCellValue('H1', 'COUPON CODE');
		$sheet->setCellValue('I1', 'ORDER ID');
		$sheet->setCellValue('J1', 'DATE & TIME');
		/*$sheet->setCellValue('H1', 'CREATION DATE');
		$sheet->setCellValue('I1', 'CREATION IP');
		$sheet->setCellValue('J1', 'VACCINATION STATUS');*/
		
		$slno = 1;
		$start = 2;
			foreach($data as $d){
				$sheet->setCellValue('A'.$start, $slno);
				$sheet->setCellValue('B'.$start, $d['users_name']);
				$sheet->setCellValue('C'.$start, $d['users_mobile']);
				$sheet->setCellValue('D'.$start, $d['users_email']);
				$sheet->setCellValue('E'.$start, (int)$d['product_id']);
				$sheet->setCellValue('F'.$start, $d['product_title']);
				$sheet->setCellValue('G'.$start, $d['adepoints']);
				$sheet->setCellValue('H'.$start, $d['coupon_code']);
				$sheet->setCellValue('I'.$start, $d['order_id']);
				$sheet->setCellValue('J'.$start, date('d-m-Y H:i A', strtotime($d['created_at'])));
				/*$sheet->setCellValue('I'.$start, $d['creation_ip']);
				$sheet->setCellValue('J'.$start, $d['vaccination_status']);*/
				
				
		$start = $start+1;
		$slno = $slno+1;
			}
		$styleThinBlackBorderOutline = [
					'borders' => [
						'allBorders' => [
							'borderStyle' => Border::BORDER_THIN,
							'color' => ['argb' => 'FF000000'],
						],
					],
				];
		//Font BOLD
		$sheet->getStyle('A1:I1')->getFont()->setBold(true);		
		$sheet->getStyle('A1:I1000')->applyFromArray($styleThinBlackBorderOutline);
		//Alignment
		//fONT SIZE
		$sheet->getStyle('A1:D10')->getFont()->setSize(12);
		$sheet->getStyle('A1:D2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
		$sheet->getStyle('A2:D100')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		//Custom width for Individual Columns
		$sheet->getColumnDimension('A')->setWidth(5);
		$sheet->getColumnDimension('B')->setWidth(15);
		$sheet->getColumnDimension('C')->setWidth(30);
		$sheet->getColumnDimension('D')->setWidth(30);
		$sheet->getColumnDimension('E')->setWidth(15);
		$sheet->getColumnDimension('F')->setWidth(15);
		$sheet->getColumnDimension('G')->setWidth(15);
		$sheet->getColumnDimension('H')->setWidth(30);
		$sheet->getColumnDimension('I')->setWidth(30);
		

		$curdate = date('d-m-Y H:i:s');
		$writer = new Xlsx($spreadsheet);
		$filename = 'Coupons-list'.$curdate;
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
		//endif;
		/* Export excel END */
	}

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name 	: index
	 + + Developed By 	: Dilip Halder
	 + + Purpose  		: This function used for index
	 + + Date 			: 23 October 2023
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function prizeList($pid='')
	{	
		$this->admin_model->authCheck('view_data');
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'products';
		$data['activeSubMenu'] 				= 	'alluwinnproducts';
		$data['productID'] 					= 	base64_decode($pid);
		
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
				
		$whereCon['where']		 			= 	array('product_id' => (int)$data['productID']);		
		$shortField 						= 	array('title'=>'ASC');
		
		$baseUrl 							= 	getCurrentControllerPath('prizeList/'.$pid);
		$this->session->set_userdata('ALLLOTTOPRIZEDATA',currentFullUrl('addprize'));
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 	'uw_prize';
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

		$uriSegment 						= 	5;//getUrlSegment();
	    $data['PAGINATION']					=	adminPagination($baseUrl,$suffix,$totalRows,$perPage,$uriSegment);

	    if($this->uri->segment($uriSegment)):
	       $page = $this->uri->segment($uriSegment);
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

		$this->layouts->set_title('All Prize | Prize | UWINN');
		$this->layouts->admin_view('products/alllottoproducts/allprize',array(),$data);
	}	// END OF FUNCTION

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name : addprize
	 + + Developed By  : Dilip Halder
	 + + Purpose  	   : This function used for Add Edit data
	 + + Date 		   : 23 October 2023
	 + + Updated By    : Dilip Halder
	 + + Updated Date  : 06 May 2024
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
 	 public function addprize($editId='')
	 {		
	    
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'products';
		$data['activeSubMenu'] 				= 	'alluwinnproducts';

		if($editId):
			$this->admin_model->authCheck('edit_data');
			$data['EDITDATA']				=	$this->common_model->getDataByParticularField('uw_prize','prize_id',(int)$editId);
			// echo '<pre>';print_r($data['EDITDATA']);die;
		else:
			$this->admin_model->authCheck('add_data');
		endif;

		$productDetails 	= $this->common_model->getDataByParticularField('uw_products','products_id',(int)$this->session->userdata('productID4Prize'));
		// echo '<pre>';print_r($productDetails);die;
		if( $productDetails['straight_settings'] == 'Disable'  && $productDetails['rumble_settings'] == 'Disable' && $productDetails['reverse_settings'] == 'Disable'  ):
			$productDetailsGolobal = $this->common_model->getSingleDataByParticularField('','uw_settings');

			$productDetails['straight_settings']   = "Enable";
			$productDetails['reverse_settings']    = $productDetailsGolobal['reverse_settings'];
			$productDetails['rumble_settings']     = $productDetailsGolobal['rumble_settings'];
			
			if($productDetails['rumble_settings_default_check'] == "Checked"):
				$productDetails['rumble_settings']     = "Enable";
			endif;

			if($productDetails['reverse_settings_default_check'] == "Checked"):
				$productDetails['reverse_settings']     = "Enable";
			endif;
			
		endif;

		$data['lotto_type'] 		= $productDetails['lotto_type'];
		$data['straight_settings']  = $productDetails['straight_settings'];
		$data['reverse_settings']   = $productDetails['reverse_settings'];
		$data['rumble_settings']    = $productDetails['rumble_settings'];
		$data['enable_super_ball']  = $productDetails['enable_super_ball'];
		$data['super_ball_type']    = $productDetails['super_ball_type'];

		if($this->input->post('SaveChanges')):

			$error					=	'NO';

			$this->form_validation->set_rules('title', 'Title', 'trim');
			$this->form_validation->set_rules('description', 'Description', 'trim');
			//$this->form_validation->set_rules('image', 'Image', 'trim');
			$this->form_validation->set_rules('productID', 'Product', 'trim|required');

			// lotto_type to validate all prize stage.
			$lotto_type = $this->input->post('lotto_type');

			// Stright Prize Validation.. 
			if($productDetails['straight_settings'] == 'Enable'):

				if($productDetails['enable_super_ball'] == 'Y'):
					$this->form_validation->set_rules('straight_super_prize', 'Stright Super Prize', 'trim|required');
				endif;

				$this->form_validation->set_rules('stright_prize_heading', 'Stright Prize Heading', 'trim|required');
				$this->form_validation->set_rules('stright_prize_type[]', 'Stright Prize Type', 'trim');
				for($i=1; $i <$lotto_type ; $i++):   
					$this->form_validation->set_rules('stright_prize'.$i, 'Stright Prize '.$i, 'trim|required');
	            endfor; 
	        endif;
            
            if($productDetails['rumble_settings'] == 'Enable'):
				// Rumble Mix Prize Validation.. 
				if($productDetails['enable_super_ball'] == 'Y'):
					$this->form_validation->set_rules('rumble_super_prize', 'Rumble Super Prize', 'trim|required');
				endif;

				$this->form_validation->set_rules('rumble_mix_prize_heading', 'Rumble Mix Prize Heading', 'trim|required');
				$this->form_validation->set_rules('rumble_mix_prize_type[]', 'Rumble Mix Prize Type', 'trim');
				for($i=1; $i <$lotto_type ; $i++):   
					$this->form_validation->set_rules('rumble_mix_prize'.$i, 'Rumble Mix Prize '.$i, 'trim|required');
	            endfor; 
			endif;

            if($productDetails['reverse_settings'] == 'Enable'):
				// Reverse Prize Validation.. 
				if($productDetails['enable_super_ball'] == 'Y'):
					$this->form_validation->set_rules('chance_super_prize', 'Chance Super Prize', 'trim|required');
				endif;
				$this->form_validation->set_rules('reverse_prize_heading', 'Rumble Mix Prize Heading', 'trim|required');
				$this->form_validation->set_rules('reverse_prize_type[]', 'Rumble Mix Prize Type', 'trim');
				for($i=1; $i <$lotto_type ; $i++):   
					$this->form_validation->set_rules('reverse_prize'.$i, 'Rumble Mix Prize '.$i, 'trim|required');
	            endfor;
			endif;


			if($this->form_validation->run() && $error == 'NO'): 
				
				$param['product_id']		= 	(int)$this->session->userdata('productID4Prize');
				$param['enable_title'] 		=  $this->input->post('enable_title');
				$param['title']				= 	addslashes($this->input->post('title'));
				$param['title_slug']		= 	url_title(strtolower($this->input->post('title')));
				$param['description']		= 	addslashes($this->input->post('description'));
				

			 	if($productDetails['straight_settings'] == 'Enable'):
					//Stright param fields..
					$param['enable_stright_prize_heading']	=  $this->input->post('enable_stright_prize_heading');
					$param['stright_prize_heading']			=  $this->input->post('stright_prize_heading');
					$param['stright_prize_type']	 		=  $this->input->post('stright_prize_type')?$this->input->post('stright_prize_type'):[];
					for($i=1; $i <=$lotto_type ; $i++):   
						$param['stright_prize'.$i]	  	    =  (int)addslashes($this->input->post('stright_prize'.$i));
		            endfor;
		            if($productDetails['enable_super_ball'] == 'Y'):
						$param['straight_super_prize'] =  $this->input->post('straight_super_prize');
					endif;
	            endif;


	            if($productDetails['rumble_settings'] == 'Enable'):
					//Rumble Mix param fields..
					$param['enable_rumble_mix_prize_heading'] =  $this->input->post('enable_rumble_mix_prize_heading');
					$param['rumble_mix_prize_heading']	 	  =  $this->input->post('rumble_mix_prize_heading');
					$param['rumble_mix_prize_type']	     	  =  $this->input->post('rumble_mix_prize_type')?$this->input->post('rumble_mix_prize_type'):[];
					for($i=1; $i <=$lotto_type ; $i++):   
						$param['rumble_mix_prize'.$i]	 	  =  (int)addslashes($this->input->post('rumble_mix_prize'.$i));
		            endfor;
		            if($productDetails['enable_super_ball'] == 'Y'):
						$param['rumble_super_prize'] =  $this->input->post('rumble_super_prize');
					endif;
	            endif;

	            if($productDetails['reverse_settings'] == 'Enable'):
					//Reverse param fields..
					$param['enable_reverse_prize_heading'] =  $this->input->post('enable_reverse_prize_heading');
					$param['reverse_prize_heading']	       =  $this->input->post('reverse_prize_heading');
					$param['reverse_prize_type']	       =  $this->input->post('reverse_prize_type')?$this->input->post('reverse_prize_type'):[];
					for($i=1; $i <=$lotto_type ; $i++):   
						$param['reverse_prize'.$i]	 =  (int)addslashes($this->input->post('reverse_prize'.$i));
		            endfor;
		            if($productDetails['enable_super_ball'] == 'Y'):
						$param['chance_super_prize'] =  $this->input->post('chance_super_prize');
					endif;
	            endif;
				
				$param['lotto_type']	  	 =  (int)$this->input->post('lotto_type');

				if($_FILES['prize_image']['name']):
					$ufileName						= 	$_FILES['prize_image']['name'];
					$utmpName						= 	$_FILES['prize_image']['tmp_name'];
					$ufileExt         				= 	pathinfo($ufileName);
					// $unewFileName 					= 	$this->common_model->microseconds().'.'.$ufileExt['extension'];

					$unewFileName 					= 	$_FILES['slider_image']['name'];
					$filePath =  fileFCPATH .'assets/prizeImage/'.$_FILES['slider_image']['name'];
					 
					if(file_exists($filePath)):
						$unewFileName =	$ufileExt['filename'] .'_'.$this->common_model->random_strings(8).'.'.$ufileExt['extension'];
					endif;

					$this->load->library("upload_crop_img");
					$uimageLink						=	$this->upload_crop_img->_upload_image($ufileName,$utmpName,'lottoprizeImage',$unewFileName,'');
					if($uimageLink != 'UPLODEERROR'):
						$param['prize_image']		= 	$uimageLink;
					endif;
				endif;
				$param['prize_image_alt']	= addslashes($this->input->post('prize_image_alt'));
				$param['btc_heading']	    = addslashes($this->input->post('btc_heading'));
				$param['btc_prize_text']	= addslashes($this->input->post('btc_prize_text'));

				if($this->input->post('CurrentDataID') ==''):
					$param['prize_id']			=	(int)$this->common_model->getNextSequence('uw_prize');
					$param['prize_seq_id']		=	$this->common_model->getNextIdSequence('prize_seq_id','prize');
					$param['creation_ip']		=	currentIp();
					$param['creation_date']		=	date('Y-m-d H:i');
					$param['created_by']		=	(int)$this->session->userdata('UW_ADMIN_ID');
					$param['status']			=	'A';
					$alastInsertId				=	$this->common_model->addData('uw_prize',$param);
					$this->session->set_flashdata('alert_success',lang('addsuccess'));
				else:
					$CurrentDataID					=	$this->input->post('CurrentDataID');
					$param['update_ip']			=	currentIp();
					$param['update_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
					$param['updated_by']		=	(int)$this->session->userdata('UW_ADMIN_ID');
					$this->common_model->editData('uw_prize',$param,'prize_id',(int)$CurrentDataID);
					$this->session->set_flashdata('alert_success',lang('updatesuccess'));
				endif;	
				
				redirect('products/alluwinnproducts/prizeList/'.base64_encode($param['product_id']));
			endif;
		endif;

		// echo '<pre>';
		// print_r($data);
		// die;


		$this->layouts->set_title('Add/Edit Prize');
		$this->layouts->admin_view('products/alllottoproducts/addprize',array(),$data);
	 } // END OF FUNCTION	
	 
	// public function addprize($editId='')
	// {		
	    
	// 	$data['error'] 						= 	'';
	// 	$data['activeMenu'] 				= 	'products';
	// 	$data['activeSubMenu'] 				= 	'alluwinnproducts';

	// 	if($editId):
	// 		$this->admin_model->authCheck('edit_data');
	// 		$data['EDITDATA']				=	$this->common_model->getDataByParticularField('uw_prize','prize_id',(int)$editId);
	// 		// echo '<pre>';print_r($data['EDITDATA']);die;
	// 	else:
	// 		$this->admin_model->authCheck('add_data');
	// 	endif;

	// 	if($this->input->post('SaveChanges')):
	// 		$error					=	'NO';

	// 		$productDetails 	=	$this->common_model->getDataByParticularField('uw_products','products_id',(int)(int)$this->session->userdata('productID4Prize'));
	// 		$data['lotto_type'] 		= $productDetails['lotto_type'];
	// 		$data['straight_settings']  = $productDetails['straight_settings'];
	// 		$data['reverse_settings']   = $productDetails['reverse_settings'];
	// 		$data['rumble_settings']    = $productDetails['rumble_settings'];

	// 		$this->form_validation->set_rules('title', 'Title', 'trim');
	// 		$this->form_validation->set_rules('description', 'Description', 'trim');
	// 		//$this->form_validation->set_rules('image', 'Image', 'trim');
	// 		$this->form_validation->set_rules('productID', 'Product', 'trim|required');

	// 		// lotto_type to validate all prize stage.
	// 		$lotto_type = $this->input->post('lotto_type');

	// 		// Stright Prize Validation.. 
	// 		$this->form_validation->set_rules('stright_prize_heading', 'Stright Prize Heading', 'trim|required');
	// 		$this->form_validation->set_rules('stright_prize_type[]', 'Stright Prize Type', 'trim');

	// 		for($i=1; $i <$lotto_type ; $i++):   
	// 			$this->form_validation->set_rules('stright_prize'.$i, 'Stright Prize '.$i, 'trim|required');
    //         endfor; 
            
    //         if($productDetails['rumble_settings'] == 'Enable'):
	// 			// Rumble Mix Prize Validation.. 
	// 			$this->form_validation->set_rules('rumble_mix_prize_heading', 'Rumble Mix Prize Heading', 'trim|required');
	// 			$this->form_validation->set_rules('rumble_mix_prize_type[]', 'Rumble Mix Prize Type', 'trim');
	// 			for($i=1; $i <$lotto_type ; $i++):   
	// 				$this->form_validation->set_rules('rumble_mix_prize'.$i, 'Rumble Mix Prize '.$i, 'trim|required');
	//             endfor; 
	// 		endif;

    //         if($productDetails['rumble_settings'] == 'Enable'):
	// 			// Reverse Prize Validation.. 
	// 			$this->form_validation->set_rules('reverse_prize_heading', 'Rumble Mix Prize Heading', 'trim|required');
	// 			$this->form_validation->set_rules('reverse_prize_type[]', 'Rumble Mix Prize Type', 'trim');
	// 			for($i=1; $i <$lotto_type ; $i++):   
	// 				$this->form_validation->set_rules('reverse_prize'.$i, 'Rumble Mix Prize '.$i, 'trim|required');
	//             endfor;
	// 		endif;

	// 		if($this->form_validation->run() && $error == 'NO'): 
				
	// 			$param['product_id']		= 	(int)$this->session->userdata('productID4Prize');
	// 			$param['enable_title'] 		=  $this->input->post('enable_title');
	// 			$param['title']				= 	addslashes($this->input->post('title'));
	// 			$param['title_slug']		= 	url_title(strtolower($this->input->post('title')));
	// 			$param['description']		= 	addslashes($this->input->post('description'));
				
	// 			//Stright param fields..
	// 			$param['enable_stright_prize_heading']	=  $this->input->post('enable_stright_prize_heading');
	// 			$param['stright_prize_heading']			=  $this->input->post('stright_prize_heading');
	// 			$param['stright_prize_type']	 		=  $this->input->post('stright_prize_type')?$this->input->post('stright_prize_type'):[];
	// 			for($i=1; $i <=$lotto_type ; $i++):   
	// 				$param['stright_prize'.$i]	  	    =  (int)addslashes($this->input->post('stright_prize'.$i));
	//             endfor;

	//             if($productDetails['rumble_settings'] == 'Enable'):
	// 				//Rumble Mix param fields..
	// 				$param['enable_rumble_mix_prize_heading'] =  $this->input->post('enable_rumble_mix_prize_heading');
	// 				$param['rumble_mix_prize_heading']	 	  =  $this->input->post('rumble_mix_prize_heading');
	// 				$param['rumble_mix_prize_type']	     	  =  $this->input->post('rumble_mix_prize_type')?$this->input->post('rumble_mix_prize_type'):[];
	// 				for($i=1; $i <=$lotto_type ; $i++):   
	// 					$param['rumble_mix_prize'.$i]	 	  =  (int)addslashes($this->input->post('rumble_mix_prize'.$i));
	// 	            endfor;
	//             endif;

	//             if($productDetails['reverse_settings'] == 'Enable'):
	// 				//Reverse param fields..
	// 				$param['enable_reverse_prize_heading'] =  $this->input->post('enable_reverse_prize_heading');
	// 				$param['reverse_prize_heading']	       =  $this->input->post('reverse_prize_heading');
	// 				$param['reverse_prize_type']	       =  $this->input->post('reverse_prize_type')?$this->input->post('reverse_prize_type'):[];
	// 				for($i=1; $i <=$lotto_type ; $i++):   
	// 					$param['reverse_prize'.$i]	 =  (int)addslashes($this->input->post('reverse_prize'.$i));
	// 	            endfor;
	//             endif;
				
	// 			$param['lotto_type']	  	 =  (int)$this->input->post('lotto_type');

	// 			if($_FILES['prize_image']['name']):
	// 				$ufileName						= 	$_FILES['prize_image']['name'];
	// 				$utmpName						= 	$_FILES['prize_image']['tmp_name'];
	// 				$ufileExt         				= 	pathinfo($ufileName);
	// 				// $unewFileName 					= 	$this->common_model->microseconds().'.'.$ufileExt['extension'];

	// 				$unewFileName 					= 	$_FILES['slider_image']['name'];
	// 				$filePath =  fileFCPATH .'assets/prizeImage/'.$_FILES['slider_image']['name'];
					 
	// 				if(file_exists($filePath)):
	// 					$unewFileName =	$ufileExt['filename'] .'_'.$this->common_model->random_strings(8).'.'.$ufileExt['extension'];
	// 				endif;

	// 				$this->load->library("upload_crop_img");
	// 				$uimageLink						=	$this->upload_crop_img->_upload_image($ufileName,$utmpName,'lottoprizeImage',$unewFileName,'');
	// 				if($uimageLink != 'UPLODEERROR'):
	// 					$param['prize_image']		= 	$uimageLink;
	// 				endif;
	// 			endif;
	// 			$param['prize_image_alt']	= addslashes($this->input->post('prize_image_alt'));
	// 			$param['btc_heading']	    = addslashes($this->input->post('btc_heading'));
	// 			$param['btc_prize_text']	= addslashes($this->input->post('btc_prize_text'));
	// 			if($this->input->post('CurrentDataID') ==''):
	// 				$param['prize_id']			=	(int)$this->common_model->getNextSequence('uw_prize');
	// 				$param['prize_seq_id']		=	$this->common_model->getNextIdSequence('prize_seq_id','prize');
	// 				$param['creation_ip']		=	currentIp();
	// 				$param['creation_date']		=	date('Y-m-d H:i');
	// 				$param['created_by']		=	(int)$this->session->userdata('UW_ADMIN_ID');
	// 				$param['status']			=	'A';
	// 				$alastInsertId				=	$this->common_model->addData('uw_prize',$param);
	// 				$this->session->set_flashdata('alert_success',lang('addsuccess'));
	// 			else:
	// 				$CurrentDataID					=	$this->input->post('CurrentDataID');
	// 				$param['update_ip']			=	currentIp();
	// 				$param['update_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
	// 				$param['updated_by']		=	(int)$this->session->userdata('UW_ADMIN_ID');
	// 				$this->common_model->editData('uw_prize',$param,'prize_id',(int)$CurrentDataID);
	// 				$this->session->set_flashdata('alert_success',lang('updatesuccess'));
	// 			endif;	
				
	// 			redirect('products/alluwinnproducts/prizeList/'.base64_encode($param['product_id']));
	// 		endif;
	// 	endif;

		
	// 	// echo '<pre>';print_r($data['productDetails']);die;
	// 	$productDetails 	=	$this->common_model->getDataByParticularField('uw_products','products_id',(int)(int)$this->session->userdata('productID4Prize'));
	// 	$data['lotto_type'] 		= $productDetails['lotto_type'];
	// 	$data['straight_settings']  = $productDetails['straight_settings'];
	// 	$data['reverse_settings']   = $productDetails['reverse_settings'];
	// 	$data['rumble_settings']    = $productDetails['rumble_settings'];
	// 	$this->layouts->set_title('Add/Edit Prize');
	// 	$this->layouts->admin_view('products/alllottoproducts/addprize',array(),$data);
	// }	// END OF FUNCTION	

	/***********************************************************************
	** Function name 	: deletePrize
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for delete data
	** Date 			: 16 MAY 2022
	************************************************************************/
	function deletePrize($deleteId='')
	{  

		$this->admin_model->authCheck('delete_data');
		$data	=	$this->common_model->getDataByParticularField('uw_prize','prize_id',(int)$deleteId);
		$imagepath =  fileFCPATH.$data['prize_image'];
		@unlink($imagepath);
		$this->common_model->deleteData('uw_prize','prize_id',(int)$deleteId);
		$this->session->set_flashdata('alert_success',lang('deletesuccess'));
		redirect(correctLink('ALLPRIZEDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name 	: updatestock
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for delete data
	** Date 			: 23 MAY 2022
	************************************************************************/
	function updatestock($pid=''){
		$this->admin_model->authCheck('edit_data');
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'products';
		$data['activeSubMenu'] 				= 	'alluwinnproducts';
		if($pid):
			$data['EDITDATA']				=	$this->common_model->getDataByParticularField('uw_products','products_id',(int)$pid);
		endif;
		if($this->input->post('SaveChanges')):
			$error					=	'NO';
			$this->form_validation->set_rules('stock', 'Stock', 'trim');
			if($this->form_validation->run() && $error == 'NO'): 
				$praduct			=	$this->common_model->getDataByParticularField('uw_products','products_id',(int)$pid);
				$totalstock 		= 	$praduct['totalStock'];
				$availablestock 	= 	$praduct['stock'];
				$inventory_stock	=	$praduct['inventory_stock'];

				$utotalstock 		= (int)$totalstock + (int)$this->input->post('stock');

				$uavailablestock 		= (int)$availablestock + (int)$this->input->post('stock');

				$uinventory_stock 		= (int)$inventory_stock + (int)$this->input->post('stock');

				$param['totalStock']		= 	(int)$utotalstock;
				$param['target_stock']		= 	(int)$utotalstock;
				$param['stock']				= 	(int)$uavailablestock;
				$param['inventory_stock']	= 	(int)$uinventory_stock;

				$param['update_ip']			=	currentIp();
				$param['update_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
				$param['updated_by']		=	(int)$this->session->userdata('UW_ADMIN_ID');
				$this->common_model->editData('uw_products',$param,'products_id',(int)$pid);
				$this->session->set_flashdata('alert_success',lang('updatesuccess'));
				redirect('products/alllottoproducts/index/');
			endif;
		endif;
		$this->layouts->set_title('Add Stock');
		$this->layouts->admin_view('products/alllottoproducts/stockupdate',array(),$data);
	} // END OF FUNCTION

	/***********************************************************************
	** Function name 	: orderstatus
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for change order status
	** Date 			: 23 MAY 2022
	************************************************************************/
	function orderstatus($changeStatusId='',$statusType='')
	{  
		//echo $changeStatusId; die();
		$this->admin_model->authCheck('edit_data');
		$param['order_status']		=	$statusType;
		$this->common_model->editData('uw_orders',$param,'order_id',$changeStatusId);
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		
		redirect(correctLink('ALLLOTOPRODUCTSDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name 	: changesoldoutstatus
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for change soldout status
	** Date 			: 17 MAY 2023
	************************************************************************/
	function changesoldoutstatus($changeStatusId='',$statusType='')
	{  
		// echo $changeStatusId; die();
		$this->admin_model->authCheck('edit_data');
		$param['isSoldout']		=	$statusType;
		// echo $param['isSoldout'];die();
		$this->common_model->editData('uw_products',$param,'products_id',(int)$changeStatusId);
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		
		redirect(correctLink('ALLLOTOPRODUCTSDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name 	: imageDelete
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used to delete image
	** Date 			: 05 December 2023
	************************************************************************/
	function imageDelete()
	{  
		$this->admin_model->authCheck('delete_data');
		$imageName			=	$this->input->post('imageName');
		$id 				=	$this->input->post('id');
		$typ 				=	$this->input->post('typ');

		//echo $typ;die;
		if($typ == 'web'):
			$param['product_image']		=	''; 
		elseif($typ == 'app'):
			$param['product_image']		=	''; 
		endif;

		if($imageName):
			$this->load->library("upload_crop_img");
			$return	=	$this->upload_crop_img->_delete_image(trim($imageName)); 
			$this->common_model->editData('uw_products',$param,'products_id',(int)$id);
		endif;
		if($typ == 'web'):
			$returnArray  		= 	array('status'=>1,'message'=>'Image deleted.');
		elseif($typ == 'app'):
			$returnArray  		= 	array('status'=>2,'message'=>'Image deleted.');
		endif;
		header('Content-type: application/json');
		echo json_encode($returnArray); die;
	}

	/***********************************************************************
	** Function name 	: imagePrizeDelete
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used to delete image
	** Date 			: 13 February 2024
	************************************************************************/
	function imagePrizeDelete()
	{  
		$this->admin_model->authCheck('delete_data');
		$imageName			=	$this->input->post('imageName');
		$id 				=	$this->input->post('id');
		$typ 				=	$this->input->post('typ');

		//echo $typ;die;
		if($typ == 'web'):
			$param['prize_image']		=	''; 
		elseif($typ == 'app'):
			$param['prize_image']		=	''; 
		endif;

		if($imageName):
			$this->load->library("upload_crop_img");
			$return	=	$this->upload_crop_img->_delete_image(trim($imageName)); 
			$this->common_model->editData('uw_prize',$param,'prize_id',(int)$id);
		endif;
		if($typ == 'web'):
			$returnArray  		= 	array('status'=>1,'message'=>'Image deleted.');
		elseif($typ == 'app'):
			$returnArray  		= 	array('status'=>2,'message'=>'Image deleted.');
		endif;
		header('Content-type: application/json');
		echo json_encode($returnArray); die;
	}


	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name : addeditdata
	 + + Developed By  : Dilip Halder
	 + + Purpose  	   : This function used for Add Edit settings
	 + + Date 		   : 27 January 2024
	 + + Updated By    : Dilip Halder 
	 + + Updated Date  : 07 May 2024
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	 public function settings($pid='')
	 {		
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'products';
		$data['activeSubMenu'] 				= 	'alluwinnproducts';
		
		$this->admin_model->authCheck('view_data');
		if($pid):
			$tblName  			= 'uw_products';
			$where['where']  	=  array('status' => 'A','products_id' =>(int)$pid);
			$data['EDITDATA']	=	$this->common_model->getData('single',$tblName,$where);
		else:
			$tblName  			= 'uw_settings';
			$where['where']  	=  array('status' => 'A');
			$data['EDITDATA']	=	$this->common_model->getData('single',$tblName,$where);
		endif;

		if($this->input->post('SaveChanges')):
			$error					=	'NO';

			// $this->form_validation->set_rules('lotto_settings_id', 'lotto_settings_id', 'trim');
			$this->form_validation->set_rules('straight_settings', 'straight amount', 'trim|required');
			$this->form_validation->set_rules('rumble_settings', 'Rumble amount', 'trim|required');
			$this->form_validation->set_rules('reverse_settings', 'Reverse Amount', 'trim|required');
			$this->form_validation->set_rules('straight_settings_default_check', 'Straight Checkbox', 'trim|required');
			$this->form_validation->set_rules('rumble_settings_default_check', 'Rumble Checkbox', 'trim|required');
			$this->form_validation->set_rules('reverse_settings_default_check', 'Reverse Checkbox', 'trim|required');
			$this->form_validation->set_rules('campaign_auto_freezing_mode', 'Campaign Auto Freezing Mode', 'trim|required');
			$this->form_validation->set_rules('game_description', 'Game Description', 'trim|required');

			$campaignFreezingParameters = $this->input->post('campaign_auto_freezing_mode')== 'Enable' ? 'trim|required' : 'trim';
			$this->form_validation->set_rules('campaign_freezing_start_time', 'Campaign Freezing Start Time', $campaignFreezingParameters);
			$this->form_validation->set_rules('campaign_freezing_end_time', 'Campaign Freezing End Time'    , $campaignFreezingParameters);
			$raffleParameters = $this->input->post('enable_raffle_ticket')== 'Enable' ? 'trim|required' : 'trim';
			$this->form_validation->set_rules('reffle_prefix', 'Reffle Prefix', $raffleParameters);
			$this->form_validation->set_rules('reffle_length', 'Reffle length', $raffleParameters);
			$this->form_validation->set_rules('primary_color', 'Primary Color', 'trim|required');
			$this->form_validation->set_rules('secondary_color', 'Secondary Color', 'trim|required');
			$this->form_validation->set_rules('SaveChanges', 'SaveChanges', 'trim|required');

			if($this->form_validation->run() && $error == 'NO'):
				$param['straight_settings']	     		  = $this->input->post('straight_settings');
				$param['rumble_settings']	 	 		  = $this->input->post('rumble_settings');
				$param['reverse_settings']	     		  = $this->input->post('reverse_settings');
				$param['straight_game_name']	     	  = $this->input->post('straight_game_name');
				$param['rumble_game_name']	     	  	  = $this->input->post('rumble_game_name');
				$param['reverse_game_name']	     	  	  = $this->input->post('reverse_game_name');
				$param['straight_settings_default_check'] = $this->input->post('straight_settings_default_check');
				$param['rumble_settings_default_check']	  = $this->input->post('rumble_settings_default_check');
				$param['reverse_settings_default_check']  = $this->input->post('reverse_settings_default_check');
				$param['campaign_auto_freezing_mode']     = $this->input->post('campaign_auto_freezing_mode');
				$param['campaign_freezing_start_time']    = $this->input->post('campaign_freezing_start_time');
				$param['campaign_freezing_end_time']      = $this->input->post('campaign_freezing_end_time');
				$param['game_description']      		  = $this->input->post('game_description');
				$param['enable_raffle_ticket']      	  = $this->input->post('enable_raffle_ticket');
				$param['reffle_prefix']      		  	  = $this->input->post('reffle_prefix');
				$param['reffle_length']      		  	  = $this->input->post('reffle_length');
				$param['primary_color']      		  	  = $this->input->post('primary_color');
				$param['secondary_color']      		  	  = $this->input->post('secondary_color');
				
				$images = $_FILES['game_rule_image'];
				if($images['name']):
					$ufileName    = 	$images['name'];
					$utmpName	  = 	$images['tmp_name'];
					$ufileExt     = 	pathinfo($ufileName);
					$unewFileName = $this->common_model->random_strings(8).'.'.$ufileExt['extension'];
					$this->load->library("upload_crop_img");
					$uimageLink	  =	$this->upload_crop_img->_upload_image($ufileName, $utmpName, 'lottoproductsImage', $unewFileName, '');
					if($uimageLink != 'UPLODEERROR'):
						$imageName = $data['EDITDATA']['game_rule_image'];
						if($imageName):
							$this->load->library("upload_crop_img");
							$this->upload_crop_img->_delete_image(trim($imageName)); 	
						endif;

						$param['game_rule_image']		= 	$uimageLink;
					else:
						$param['game_rule_image']		= 	'';
					endif;
				endif;
				
				if($this->input->post('CurrentDataID') ==''):
					$param['lotto_settings_id'] =	(int)$this->common_model->getNextSequence('lotto_settings_id');
					$param['status']			=	'A';
					$param['creation_ip']		=	currentIp();
					$param['creation_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
					$param['created_by']		=	(int)$this->session->userdata('UW_ADMIN_ID');
					$alastInsertId				=	$this->common_model->addData('uw_settings',$param);
					$this->session->set_flashdata('alert_success',lang('addsuccess'));
				else:
					$categoryId					=	$this->input->post('CurrentDataID');
					$param['update_ip']			=	currentIp();
					$param['update_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
					$param['updated_by']		=	(int)$this->session->userdata('UW_ADMIN_ID');
					if($pid):
					 $this->common_model->editData('uw_products',$param,'products_id',(int)$categoryId);
					else:
					 $this->common_model->editData('uw_settings',$param,'lotto_settings_id',(int)$categoryId);
					endif;
					$this->session->set_flashdata('alert_success',lang('updatesuccess'));
				endif;
				redirect(correctLink('ALLLOTOPRODUCTSDATA',getCurrentControllerPath('settings')));
			endif;
		endif;
		
		$this->layouts->set_title('Add/Edit Products');
		$this->layouts->admin_view('products/alllottoproducts/settings',array(),$data);
	 }
	 // END OF FUNCTION		

	 /*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name : updateDraw
	 + + Developed By  : Dilip Halder
	 + + Purpose  	   : This function used to update draw.
	 + + Date 		   : 27 May 2025
	 + + Updated By    :   
	 + + Updated Date  : 
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	 public function updateDraw($pid='')
	 {
	 	$tblName  		 = 'uw_products';
		$where['where']  =  array('products_id' =>(int)$pid );
		$ProductData	 =	$this->common_model->getData('single',$tblName,$where);

		if(!empty($ProductData)):
			// Draw Date Validations...
			
			$NewDrawDate     = date('Y-m-d', strtotime(date('Y-m-d').'+1 days' ));
			$NewDrawTime     = $ProductData['draw_time'];

			$DrawDateTime    = strtotime($ProductData['draw_date'].' '.$NewDrawTime); 
			$NewDrawDateTime = strtotime($NewDrawDate.' '.$NewDrawTime); 

			/// Storing Draw Date in Data in  uw_products_draw_records..
			if($DrawDateTime != $NewDrawDateTime ):
				//Added Draw date and time entry in draw collection.
				$DrawParam['draw_id']  	    =  $param['draw_id']?(int)$param['draw_id']:(int)$this->common_model->getNextSequence('uw_products_draw_records');
				$DrawParam['products_id']	=  (int)$ProductData['products_id'];
				$DrawParam['products_oid']	=  new MongoDB\BSON\ObjectId($ProductData['_id']->{'$id'});
				$DrawParam['draw_date']		=  $NewDrawDate;
				$DrawParam['draw_time']		=  $NewDrawTime;
				$DrawParam['creation_ip']	=  currentIp();
				$DrawParam['creation_date']	=  (int)$this->timezone->utc_time();//currentDateTime();
				$DrawParam['created_by']	=  (int)$this->session->userdata('UW_ADMIN_ID');
				$DrawInsertId				=  $this->common_model->addData('uw_products_draw_records',$DrawParam);

				if(!empty($DrawInsertId)):
					// Updating present draw ID 
					$draw_id   = $DrawInsertId['draw_id'];
					$draw_oid  = $DrawInsertId['_id']->{'$id'};
					$ProductParam['draw_id']		 = (int)$draw_id;
					$ProductParam['draw_date']		 = $NewDrawDate;
					$ProductParam['validuptodate']   = $NewDrawDate;
					$ProductParam['draw_oid']		 = new MongoDB\BSON\ObjectId($draw_oid);
					$ProductParam['target_stock']	 = (int)addslashes($this->input->post('target_stock'));
					$ProductParam['update_ip']		 = currentIp();
					$ProductParam['update_date']	 = (int)$this->timezone->utc_time();//currentDateTime();
					$ProductParam['updated_by']		 = (int)$this->session->userdata('UW_ADMIN_ID');
					// echo "<pre>";print_r($DrawInsertId);die();
					$this->common_model->editData('uw_products',$ProductParam,'products_id',(int)$pid);
					$this->session->set_flashdata('alert_success',lang('updatesuccess'));
				else:
					$this->session->set_flashdata('alert_error',lang('updateerror'));
				endif;
			else:
				$this->session->set_flashdata('alert_error','Already Updated.');
			endif;
				redirect('products/alluwinnproducts/index');
		endif;
	 	die();
	 }
}

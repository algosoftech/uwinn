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

class Allcampaigns extends CI_Controller {

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
	 + + Developed By 	: AFSAR ALI
	 + + Purpose  		: This function used for index
	 + + Date 			: 05 APRIL 2022
	 + + Updated Date 	: 
	 + + Updated By   	:
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index()
	{	
		$this->admin_model->authCheck();
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'campaigns';
		$data['activeSubMenu'] 				= 	'allcampaigns';
		
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
				
		$whereCon['where']		 			= 	'';		
		$shortField 						= 	array('title'=>'ASC');
		
		$baseUrl 							= 	getCurrentControllerPath('index');
		$this->session->set_userdata('ALLPRODUCTSDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 	'db_campaigns';
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

		$this->layouts->set_title('All Campaigns | Campaigns | Dealz Arabia');
		$this->layouts->admin_view('campaigns/allcampaigns/index',array(),$data);
	}	// END OF FUNCTION

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name : addeditdata
	 + + Developed By  : AFSAR ALI
	 + + Purpose  	   : This function used for Add Edit data
	 + + Date 		   : 05 APRIL 2022
	 + + Updated Date  : 
	 + + Updated By    :
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function addeditdata($editId='')
	{		
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'campaigns';
		$data['activeSubMenu'] 				= 	'allcampaigns';
		
		if($editId):
			$this->admin_model->authCheck('edit_data');
			$data['EDITDATA']				=	$this->common_model->getDataByParticularField('db_campaigns','campaigns_id',(int)$editId);//echo '<pre>';print_r($data['EDITDATA']);die;
		else:
			$this->admin_model->authCheck('add_data');
		endif;
		
		if($this->input->post('SaveChanges')):
			$error					=	'NO';

			$this->form_validation->set_rules('title', 'Title', 'trim');
			$this->form_validation->set_rules('category_id', 'Category', 'trim');
			$this->form_validation->set_rules('sub_category_id', 'Sub Category', 'trim');
			$this->form_validation->set_rules('description', 'Description', 'trim');
			$this->form_validation->set_rules('image', 'Image', 'trim');
			$this->form_validation->set_rules('stock', 'Stock', 'trim');
			$this->form_validation->set_rules('adepoints', 'ADE Points', 'trim');

			if($this->form_validation->run() && $error == 'NO'): 

				$param['title']				= 	addslashes($this->input->post('title'));
				$param['title_slug']				= 	url_title(strtolower($this->input->post('title')));
				$param['description']				= 	addslashes($this->input->post('description'));

				$categoryData						=	explode('_____',$this->input->post('category_id'));
				$param['category_id']				= 	(int)$categoryData[0];
				$param['category_name']				= 	$categoryData[1];

				$sub_categoryData							= 	explode('____',$this->input->post('sub_category_id'));
				$param['sub_category_id']					= 	(int)$sub_categoryData[0];
				$param['sub_category_name']					= 	$sub_categoryData[1];
				
				if($_FILES['campaigns_image']['name']):
					$ufileName						= 	$_FILES['campaigns_image']['name'];
					$utmpName						= 	$_FILES['campaigns_image']['tmp_name'];
					$ufileExt         				= 	pathinfo($ufileName);
					$unewFileName 					= 	$this->common_model->microseconds().'.'.$ufileExt['extension'];
					$this->load->library("upload_crop_img");
					$uimageLink						=	$this->upload_crop_img->_upload_image($ufileName,$utmpName,'campaignsImage',$unewFileName,'');
					if($uimageLink != 'UPLODEERROR'):
						$param['campaigns_image']		= 	$uimageLink;
					endif;
				endif;
				$param['campaigns_image_alt']	= 	addslashes($this->input->post('campaigns_image_alt'));
				$param['stock']				= 	addslashes($this->input->post('stock'));
				$param['adepoints']				= 	addslashes($this->input->post('adepoints'));

				if($this->input->post('CurrentDataID') ==''):
					$param['campaigns_id']		=	(int)$this->common_model->getNextSequence('db_campaigns');
					$param['campaigns_seq_id']	=	$this->common_model->getNextIdSequence('campaigns_seq_id','campaigns');
					$param['creation_ip']		=	currentIp();
					$param['creation_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
					$param['created_by']		=	(int)$this->session->userdata('HCAP_ADMIN_ID');
					$param['status']			=	'A';
					$alastInsertId				=	$this->common_model->addData('db_campaigns',$param);
					$this->session->set_flashdata('alert_success',lang('addsuccess'));
				else:

					$categoryId					=	$this->input->post('CurrentDataID');
					$param['update_ip']			=	currentIp();
					$param['update_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
					$param['updated_by']		=	(int)$this->session->userdata('HCAP_ADMIN_ID');
					$this->common_model->editData('db_campaigns',$param,'campaigns_id',(int)$categoryId);
					$this->session->set_flashdata('alert_success',lang('updatesuccess'));
				endif;

				redirect(correctLink('MASTERDATACAMPAINGSTYPE',getCurrentControllerPath('index')));
			endif;
		endif;
		
		$this->layouts->set_title('Add/Edit Campaigns');
		$this->layouts->admin_view('campaigns/allcampaigns/addeditdata',array(),$data);
	}	// END OF FUNCTION	

	/***********************************************************************
	** Function name 	: changestatus
	** Developed By 	: Manoj Kumar
	** Purpose  		: This function used for change status
	** Date 			: 21 JUNE 2021
	************************************************************************/
	function changestatus($changeStatusId='',$statusType='')
	{  
		$this->admin_model->authCheck('edit_data');
		$param['status']		=	$statusType;
		$this->common_model->editData('db_campaigns',$param,'campaigns_id',(int)$changeStatusId);
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		
		redirect(correctLink('ALLPRODUCTSDATA',getCurrentControllerPath('index')));
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
		$this->common_model->deleteData('db_campaigns','campaigns_id',(int)$deleteId);
		$this->session->set_flashdata('alert_success',lang('deletesuccess'));
		
		redirect(correctLink('ALLPRODUCTSDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name 	: getsub_categoryData
	** Developed By 	: AFSAR ALI
	** Purpose  		: This function used for delete data
	** Date 			: 05 APRIL 2022
	************************************************************************/
	public function getsubcategoryData(){
		$categoryData			=	explode('_____',$this->input->post('category_id'));
		$categoryId				= 	(int)$categoryData[0];
		$whereCon['where']		= 	array('category_id' => $categoryId);	
		$sub_categoryData 				=   $this->common_model->getData('multiple','da_sub_category',$whereCon);
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
		/* Export excel button code */
		$wcon['where']          =   '';
		$data        			=   $this->common_model->getData('multiple','da_campaigns',$wcon);//echo '<pre>';print_r($data);die;

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


public function getSubCategory(){
	echo "string"; die();
}


}
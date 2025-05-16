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

class Allsawinners extends CI_Controller {

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
	 + + Date 			: 19 January 2024
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index($pid='')
	{	
		$this->admin_model->authCheck('view_data');
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'products';
		$data['activeSubMenu'] 				=   'allsaproducts';
		$data['productID']					=	base64_decode($pid);

		$this->session->set_userdata('productID4winners', base64_decode($pid));
		
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
		
		$baseUrl 							= 	getCurrentControllerPath('index');
		$this->session->set_userdata('ALLWINNERSDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 	'da_sa_winners';
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

		$this->layouts->set_title('All Winners | Winners | Dealz Arabia');
		$this->layouts->admin_view('winners/allwinners/index',array(),$data);
	}	// END OF FUNCTION

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name : addeditdata
	 + + Developed By  : AFSAR ALI
	 + + Purpose  	   : This function used for Add Edit data
	 + + Date 		   : 14 APRIL 2022
	 + + Updated Date  : 20-07-2023
	 + + Updated By    : Dilip halder
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function addeditdata($editId='')
	{	
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'winners';
		$data['activeSubMenu'] 				= 	'allwinners';
		
		$subadmin = $this->session->userdata('HCAP_ADMIN_TYPE');

		if($editId):
			
			if($subadmin != 'Sub Admin'):
			  $this->admin_model->authCheck('edit_data');
			endif;

			$data['EDITDATA']				=	$this->common_model->getDataByParticularField('da_sa_winners','winners_id',(int)$editId);//echo '<pre>';print_r($data['EDITDATA']);die;
		else:

			if($subadmin != 'Sub Admin'):
			  $this->admin_model->authCheck('add_data');
			endif;
			
		endif;
		

		if($this->input->post('SaveChanges')):
			$error					=	'NO';
			$this->form_validation->set_rules('title', 'Title', 'trim');
			$this->form_validation->set_rules('name', 'Winners Name', 'trim');
			$this->form_validation->set_rules('coupon', 'Coupon No.', 'trim');
			$this->form_validation->set_rules('aDate', 'Announced Date', 'trim');
			$this->form_validation->set_rules('aTime', 'Announced Time', 'trim');
			$this->form_validation->set_rules('image', 'Image', 'trim');
			$this->form_validation->set_rules('country', 'Country', 'trim');
			//$this->form_validation->set_rules('stock', 'Stock', 'trim');
			//$this->form_validation->set_rules('adepoints', 'ADE Points', 'trim');

			if($this->form_validation->run() && $error == 'NO'): 

				$param['title']				= 	addslashes($this->input->post('title'));
				$param['product_id']		= 	(int)$this->session->userdata('productID4winners');
				$param['name']				= 	addslashes($this->input->post('name'));
				$param['coupon']			= 	addslashes($this->input->post('coupon'));
				$param['coupon_id']			= 	(int)($this->input->post('coupon_id'));
				$param['user_id']			= 	(int)($this->input->post('user_id'));
				$param['announcedDate']		= 	addslashes($this->input->post('announcedDate'));
				$param['announcedTime']		= 	addslashes($this->input->post('announcedTime'));
				$param['winner_position']	= 	addslashes($this->input->post('winner_position'));
				$param['country']			= 	addslashes($this->input->post('country'));
				//$param['adepoints']			= 	addslashes($this->input->post('adepoints'));
				
				if($_FILES['winners_image']['name']):
					$ufileName						= 	$_FILES['winners_image']['name'];
					$utmpName						= 	$_FILES['winners_image']['tmp_name'];
					$ufileExt         				= 	pathinfo($ufileName);
					// $unewFileName 					= 	$this->common_model->microseconds().'.'.$ufileExt['extension'];
					
					$unewFileName 					= 	$_FILES['slider_image']['name'];
					
					$filePath =  fileFCPATH .'assets/winnersImage/'.$_FILES['slider_image']['name'];
					 
					if(file_exists($filePath)):
						$unewFileName =	$ufileExt['filename'] .'_'.$this->common_model->random_strings(8).'.'.$ufileExt['extension'];
					endif;

					$this->load->library("upload_crop_img");
					$uimageLink						=	$this->upload_crop_img->_upload_image($ufileName,$utmpName,'winnersImage',$unewFileName,'');
					if($uimageLink != 'UPLODEERROR'):
						$param['winners_image']		= 	$uimageLink;
					endif;
				endif;
				$param['winners_image_alt']	= 	addslashes($this->input->post('winners_image_alt'));

				// Checking Winner Announcment For a Specific Product.
				$PrizeAnnouncementCon['where']		 	= 	array('product_id' => (int)$this->session->userdata('productID4winners'));	
				$PrizeAnnouncementChecking				=	$this->common_model->getData('single','da_prize',$PrizeAnnouncementCon);

				if($PrizeAnnouncementChecking):

					$prize_type =  $PrizeAnnouncementChecking['prize_type'];
					
					if($prize_type == 'Cash'):
						$prize1 =  $PrizeAnnouncementChecking['prize1'];
						$prize2 =  $PrizeAnnouncementChecking['prize2'];
						$prize3 =  $PrizeAnnouncementChecking['prize3'];

						// Counting prizes.
						if($prize1 > 0 && $prize2 > 0 && $prize3 > 0 ){
							$prize_count = 3;
						}elseif($prize1 > 0 && $prize2 > 0){
							$prize_count = 2;
						}elseif($prize1 > 0){
							$prize_count = 1;
						}else{
							$prize_count = 0;
						}

					endif;

				endif;

				$WinnerAnnouncementCon['where']		 	= 	array('product_id' => (int)$this->session->userdata('productID4winners'));	
				$WinnerAnnouncementChecking				=	$this->common_model->getData('multiple','da_coupons',$WinnerAnnouncementCon);

					if($WinnerAnnouncementChecking){
						$WinnerAnnouncementCount = count(array_column($WinnerAnnouncementChecking, 'winner_position'))+1;
					}else{
						$WinnerAnnouncementCount = 0;
					}


				$cupdateparam['winners_name'] 			=	$param['name'];
				$cupdateparam['winners_title'] 			=	$param['title'];
				$cupdateparam['winners_coupon'] 		=	$param['coupon'];
				$cupdateparam['winners_coupon_id'] 		=	$param['coupon_id'];
				$cupdateparam['winners_user_id'] 		=	$param['user_id'];
				$cupdateparam['winners_announcedDate'] 	=	$param['announcedDate'];
				$cupdateparam['winners_announcedTime'] 	=	$param['announcedTime'];
				$cupdateparam['winner_position'] 		=	$param['winner_position'];
				$cupdateparam['country'] 				=	$param['country'];
				//$cupdateparam['winners_adepoints'] 		=	$param['adepoints'];
				$cupdateparam['is_old'] 			=	'Yes';
				$cupdateparam['coupon_status'] 			=	'Expired';


				if($prize_count > $WinnerAnnouncementCount):

					$cupdatewhere = array('product_id' => (int)$this->session->userdata('productID4winners') , 'coupon_code' => $this->input->post('coupon'));
					$this->common_model->editDataByMultipleCondition('da_coupons',$cupdateparam,$cupdatewhere);
				else:
					$cupdatewhere['product_id'] 			=	(int)$this->session->userdata('productID4winners');
					$this->common_model->editMultipleDataByMultipleCondition('da_coupons',$cupdateparam,$cupdatewhere);
				endif;


				if($this->input->post('CurrentDataID') ==''):
					$param['winners_id']		=	(int)$this->common_model->getNextSequence('da_sa_winners');
					//$param['winners_seq_id']	=	$this->common_model->getNextIdSequence('winners_seq_id','products');
					$param['creation_ip']		=	currentIp();
					$param['creation_date']		=	date('Y-m-d H:i');
					$param['created_by']		=	(int)$this->session->userdata('HCAP_ADMIN_ID');
					$param['status']			=	'A';

					$alastInsertId				=	$this->common_model->addData('da_sa_winners',$param);
					$this->session->set_flashdata('alert_success',lang('addsuccess'));
				else:

					$categoryId					=	$this->input->post('CurrentDataID');
					$param['update_ip']			=	currentIp();
					$param['update_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
					$param['updated_by']		=	(int)$this->session->userdata('HCAP_ADMIN_ID');
					$this->common_model->editData('da_sa_winners',$param,'winners_id',(int)$categoryId);
					$this->session->set_flashdata('alert_success',lang('updatesuccess'));
				endif;

				redirect(correctLink('MASTERDATAPRODUCTTYPE',getCurrentControllerPath('index/'.base64_encode($param['product_id']))));
			endif;
		endif;
		
		$this->layouts->set_title('Add/Edit Winners');
		$this->layouts->admin_view('winners/allwinners/addeditdata',array(),$data);
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
		$this->common_model->editData('da_sa_winners',$param,'winners_id',(int)$changeStatusId);
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		
		redirect(correctLink('ALLWINNERSDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name 	: deletedata
	** Developed By 	: Manoj Kumar
	** Purpose  		: This function used for delete data
	** Date 			: 21 JUNE 2021
	************************************************************************/
	function deletedata($deleteId='')
	{  
		$this->admin_model->authCheck();
		$this->common_model->deleteData('da_sa_winners','winners_id',(int)$deleteId);
		$this->session->set_flashdata('alert_success',lang('deletesuccess'));
		
		redirect(correctLink('ALLWINNERSDATA',getCurrentControllerPath('index')));
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
		$data        			=   $this->common_model->getData('multiple','da_sa_winners',$wcon);//echo '<pre>';print_r($data);die;

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
			$sheet->setCellValue('B'.$start, $d['winners_seq_id']);
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

	/***********************************************************************
	** Function name : checkDeplicacy
	** Developed By : Ritu Mishra
	** Purpose  : This function used for export deleted users data
	** Date : 15 MAY 2022
	** Updated Date : Afsar Ali
	** Updated By   : 16 May 2022
	************************************************************************/
public function checkDeplicacy(){
	//echo "working"; die();
	$user_data = array();
	$user 	= $_POST['coupon'];

	$user_data = $this->common_model->getDataByParticularField('da_coupons', 'coupon_code', $user);
	$where = ['products_id' => $user_data['product_id']];


	// checking coupon in quick orders.
	if(empty($user_data)):
		$table  = "da_ticket_coupons";
		$where['like'] 		=	array( '0' => 'coupon_code' , '1' => $user );
		$shortField 			=	array('_id'=> -1);
		$fields 				=	array('users_id','users_email','users_mobile','collection_point_name','collection_point_id');
		
		$user_data = $this->common_model->getData('multiple',$table,$where,$shortField);
		
		$has_ticket_order = '';
		
		if($user_data):
			$has_ticket_order  = 1;

			if(count($user_data) > 1):
				echo "Invlid coupon code";	 
				exit();
			endif;
		endif;

	endif;


	if($has_ticket_order ==1):
		$product_data = $this->common_model->getDataByParticularField('da_products', 'products_id', $user_data[0]['product_id']);
	else:
		$product_data = $this->common_model->getDataByParticularField('da_products', 'products_id', $user_data['product_id']);
	endif;

	$prize_data = $this->common_model->getDataByParticularField('da_prize', 'product_id', $product_data['products_id']);

	
	// $winner_data  = $this->common_model->getDataByParticularField('da_sa_winners', 'product_id', $product_data['products_id']);

	$tblName 							=   'da_sa_winners';
	$whereCon['where']		 			= 	array('product_id' => (int)$product_data['products_id'] ,'is_old' => array('$ne' => 'Yes'));		
	$shortField 						= 	'';

	$winner_data 						= 	$this->common_model->getData('multiple',$tblName,$whereCon,$shortField,'0','0');

	//Counting winner positions
	if($winner_data):
		foreach ($winner_data as  $winner) {
			$winnerPosition[] = $winner['winner_position'];
		}
		$winner =  implode('__', $winnerPosition);
	endif;

	$prize_type = $prize_data['prize_type'];
	if($prize_type == 'Cash'):

		$prize1 = $prize_data['prize1'];
		$prize2 = $prize_data['prize2'];
		$prize3 = $prize_data['prize3'];
		$winner_data =  $winner;
	endif;
	
	if(!empty($user_data)){
		if($prize_type == 'Cash'):

			//Generateing responce for quick coupon and online coupons.
			if($has_ticket_order ==1):
				echo "Coupon code is verified"."__".$user_data[0]['coupon_id']."__".$product_data['adepoints']."__".$user_data[0]['users_id']."__".$prize_type."__".$prize1."__".$prize2."__".$prize3."__".$winner_data; 
			else:
				echo "Coupon code is verified"."__".$user_data['coupon_id']."__".$product_data['adepoints']."__".$user_data['users_id']."__".$prize_type."__".$prize1."__".$prize2."__".$prize3."__".$winner_data; 
			endif;

		else:
			//Generateing responce for quick coupon and online coupons.
			if($has_ticket_order ==1):
				echo "Coupon code is verified"."__".$user_data[0]['coupon_id']."__".$product_data['adepoints']."__".$user_data[0]['users_id']; 
				
			else:
				echo "Coupon code is verified"."__".$user_data['coupon_id']."__".$product_data['adepoints']."__".$user_data['users_id']; 
			endif;

		endif;
	}else{
		echo "Invlid coupon code";
	}

}
}
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



class Referralreport extends CI_Controller {

	public function  __construct() 
	{ 
		parent:: __construct();
		error_reporting(E_ALL ^ E_NOTICE);  
		$this->load->model(array('admin_model','emailtemplate_model','sms_model','notification_model','geneal_model','order_model'));
		$this->lang->load('statictext', 'admin');
		$this->load->helper('common');
	} 

	/* * *********************************************************************
	 * * Function name : index
	 * * Developed By : Manoj Kumar
	 * * Purpose  : This function used for 
	 * * Date : 21 SEPTEMBER 2022
	 * * Date : 10 JANUARY 2023
	 * * UPDATED BY : Dilip Halder
	 * * **********************************************************************/
	public function index()
	{	
		// $this->admin_model->authCheck();
		// $data['error'] 						= 	'';
		// $data['activeMenu'] 				= 	'';
		// $data['activeSubMenu'] 				= 	'';
		// $data['referralData'] 			= 	array();

		// $data['moduleData']					=	$this->admin_model->getMenuModule('main'); 

		// $referralWhere['where']				=	array('status' => 'A');
		// $referralField 						= 	array('created_at'=> -1);
		// $data['referralData']				=	$this->common_model->getReferralList('multiple','referral_product',$referralWhere,$referralField);

		// $this->layouts->set_title('Referral Report | Admin | DealzAribia');
		// $this->layouts->admin_view('campaignsales/referralreport/index',array(),$data);



		$this->admin_model->authCheck();
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'';
		$data['activeSubMenu'] 				= 	'';
		
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

			$whereCon['where']		 			= 	array('status' => 'A');	
			$shortField 						= 	array('created_at'=>-1);
			$baseUrl 							= 	getCurrentControllerPath('index');
			$this->session->set_userdata('REFERRAlRECRT',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 	'referral_product';
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

		$data['ALLDATA'] 					= 	$this->common_model->getReferralList('multiple',$tblName,$whereCon,$shortField,$page,$perPage);

		$this->layouts->set_title('Referral Report | Admin | DealzAribia');
		$this->layouts->admin_view('campaignsales/referralreport/index',array(),$data);
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name 	: exportexcel
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for export referral report data
	** Date 			: 10 JAN 2023
	** Updated Date 	: 
	** Updated By   	: 
	************************************************************************/
	function exportexcel($load_balance_id='')
	{  
		/* Export excel button code */
		echo '<pre>';
		// print_r($this->input->post());
		if($this->input->post('fromDate')){
			$data['fromDate'] 				= 	$this->input->post('fromDate');
			$whereCon['where_gte'] 			= 	array(array("created_at",$data['fromDate']));
		}else{
			$whereCon['where_gte'] 			=	array(array("created_at",date('Y-m-01')));
		}
		if($this->input->post('toDate')){
			$data['toDate'] 				= 	$this->input->post('toDate');
			$whereCon['where_lte'] 			= 	array(array("created_at",$data['toDate']));
		}
		$whereCon['where'] = array('status'=>'A');
	
		$shortField 			= 	array('created_at'=> -1);
		$tblName 				= 	'referral_product';

		$referralreport			=	$this->common_model->getDataByNewQuery('*','multiple',$tblName,$whereCon,$shortField,0,0);

		$ReferralReportsData 		=	array();

		foreach ($referralreport as $key => $item) {
			$won['where'] = array('users_id'=> (int)$item['referral_from_id']);
			$FromReceiver       =   $this->common_model->getDataByNewQuery('*','multiple','da_users',$won,$shortField,0,0);
			$item['FromReceiverDetails'] = $FromReceiver;


			$won1['where'] = array('users_id'=> (int)$item['referral_to_id']);
			$FromSender   =   $this->common_model->getDataByNewQuery('*','multiple','da_users',$won1,$shortField,0,0);
			$item['FromSenderDetails'] = $FromSender;


			$won2['where'] = array('products_id'=> (int)$item['referral_product_id']);
			$ProductDetails       =   $this->common_model->getDataByNewQuery('*','multiple','da_products',$won2,$shortField,0,0);
			$item['ProductDetails'] = $ProductDetails;
			
			array_push($ReferralReportsData,$item);

		}

		// echo "<pre>";
		// print_r($ReferralReportsData);
		// die();
		

        $spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'Receiver Name');
		$sheet->setCellValue('B1', 'Campaign Name');
		$sheet->setCellValue('C1', 'Amount');
		$sheet->setCellValue('D1', 'Sender');
		$sheet->setCellValue('E1', 'Created At');
	
		$slno = 1;
		$start = 2;
		foreach($ReferralReportsData as $d){

			$receiver_name = $d['FromReceiverDetails'][0]['users_name'];
			$sender_name = $d['FromSenderDetails'][0]['users_name'];
			$product_name = $d['ProductDetails'][0]['title'];

			$sheet->setCellValue('A'.$start, $receiver_name);
			$sheet->setCellValue('B'.$start,  $product_name);
			$sheet->setCellValue('C'.$start,  number_format($d['referral_amount'],2));
			$sheet->setCellValue('D'.$start, $sender_name);
			$sheet->setCellValue('E'.$start, $d['created_at']);
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
		$sheet->getStyle('A1:E1')->getFont()->setBold(true);		
		$sheet->getStyle('A1:E1')->applyFromArray($styleThinBlackBorderOutline);
		//Alignment
		//fONT SIZE
		//$sheet->getStyle('A1:D10')->getFont()->setSize(12);
		//$sheet->getStyle('A1:D2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
		//$sheet->getStyle('A2:D1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		//Custom width for Individual Columns
		$sheet->getColumnDimension('A')->setWidth(20);
		$sheet->getColumnDimension('B')->setWidth(20);
		$sheet->getColumnDimension('C')->setWidth(30);
		$sheet->getColumnDimension('D')->setWidth(30);
		$sheet->getColumnDimension('E')->setWidth(30);
		
		$curdate = date('d-m-Y H:i:s');
		$writer = new Xlsx($spreadsheet);
		$filename = 'Referral Reports'.$curdate;
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
		//endif;
		/* Export excel END */
		//redirect('orders/allorders/index');
	}
}
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

	class Membershipcashback extends CI_Controller{
		
			public function  __construct() 
			{ 
				parent:: __construct();
				error_reporting(E_ALL ^ E_NOTICE);  
				$this->load->model(array('admin_model','emailtemplate_model','sms_model','notification_model','geneal_model'));
				$this->lang->load('statictext', 'admin');
				$this->load->helper('common');
			} 

			/* * *********************************************************************
			 * * Function name : index
			 * * Developed By : Dilip Halder
			 * * Purpose  : This function used for Membership Cashback List 
			 * * Date : 11 JANUARY 2023
			 * * **********************************************************************/

			public function index()
			{

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

			$whereCon['where']		 			= 	array('status' => 'A','arabian_points_from' => 'Membership Cashback','record_type'=>'Credit');
			$shortField 						= 	array('created_at'=> -1);
			
			$baseUrl 							= 	getCurrentControllerPath('index');
			$this->session->set_userdata('MEMBERSHIPCASHBACK',currentFullUrl());
			$qStringdata						=	explode('?',currentFullUrl());
			$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
			$tblName 							= 	'da_loadBalance';
			$totalRows 							= 	$this->common_model->getData('count',$tblName,$whereCon,$shortField,'0','0');
			// echo "<pre>";print_r($totalRows);die();

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


			$data['ALLDATA'] 			= 	$this->common_model->getMembershipCashbackList('multiple',$tblName,$whereCon,$shortField,$page,$perPage);

			$this->layouts->set_title('Dashboard | Admin | DealzAribia');
			$this->layouts->admin_view('cashback/membership/index',array(),$data);

			}


		/***********************************************************************
		** Function name 	: exportexcel
		** Developed By 	: Dilip Halder
		** Purpose  		: This function used for export Membership Cashback data
		** Date 			: 11 JAN 2023
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

				$whereCon['where']		 			= 	array('status' => 'A','arabian_points_from' => 'Membership Cashback','record_type'=>'Credit');
				$shortField 						= 	array('created_at'=> -1);

				$tblName 				= 	'da_loadBalance';

				$membershipcashback		=	$this->common_model->getDataByNewQuery('*','multiple',$tblName,$whereCon,$shortField,0,0);

				

				$membershipCashbackData 		=	array();

				foreach ($membershipcashback as $key => $item) {
					$won['where'] = array('users_id'=> (int)$item['user_id_cred']);
					$FromReceiver       =   $this->common_model->getDataByNewQuery('*','multiple','da_users',$won,$shortField,0,0);
					$item['FromUser'] = $FromReceiver;
				
					
					array_push($membershipCashbackData,$item);

				}

		        $spreadsheet = new Spreadsheet();
				$sheet = $spreadsheet->getActiveSheet();
				$sheet->setCellValue('A1', 'CREATED DATE');
				$sheet->setCellValue('B1', 'USER TYPE');
				$sheet->setCellValue('C1', 'USER NAME');
				$sheet->setCellValue('D1', 'CASHBACK AMOUNT');
			
				$slno = 1;
				$start = 2;
				foreach($membershipCashbackData as $d){

					$created_at = $d['created_at'];
					$users_type = $d['FromUser'][0]['users_type'];
					$users_name = $d['FromUser'][0]['users_name'];
					$arabian_points = $d['arabian_points'];
					

					$sheet->setCellValue('A'.$start, $created_at);
					$sheet->setCellValue('B'.$start,  $users_type);
					$sheet->setCellValue('C'.$start,  $users_name);
					$sheet->setCellValue('D'.$start,  number_format($arabian_points,2));
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
				$sheet->getStyle('A1:D1')->getFont()->setBold(true);		
				$sheet->getStyle('A1:D1')->applyFromArray($styleThinBlackBorderOutline);
				//Alignment
				//fONT SIZE
				//$sheet->getStyle('A1:D10')->getFont()->setSize(12);
				//$sheet->getStyle('A1:D2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
				//$sheet->getStyle('A2:D1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
				//Custom width for Individual Columns
				$sheet->getColumnDimension('A')->setWidth(30);
				$sheet->getColumnDimension('B')->setWidth(20);
				$sheet->getColumnDimension('C')->setWidth(30);
				$sheet->getColumnDimension('D')->setWidth(30);
				
				
				$curdate = date('d-m-Y H:i:s');
				$writer = new Xlsx($spreadsheet);
				$filename = 'Membership Cashback Report'.$curdate;
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

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

class Allcontact extends CI_Controller
{
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
 + + Date 			: 19 APRIL 2022
 + + Updated Date 	: 
 + + Updated By   	:
 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
public function index()
{	
	$this->admin_model->authCheck();
	$data['error'] 						= 	'';
	$data['activeMenu'] 				= 	'contact';
	$data['activeSubMenu'] 				= 	'allcontact';
	
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
	$shortField 						= 	array('id'=> -1);
	
	$baseUrl 							= 	getCurrentControllerPath('index');
	$this->session->set_userdata('ALLCONTACTDATA',currentFullUrl());
	$qStringdata						=	explode('?',currentFullUrl());
	$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
	$tblName 							= 	'uw_contacts';
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

	$this->layouts->set_title('All Contact | Contact | UWINN');
	$this->layouts->admin_view('contact/allcontact/index',array(),$data);
}


/***********************************************************************
** Function name 	: deletedata
** Developed By 	: Dilip Halder
** Purpose  		: This function used for delete data
** Date 			: 20 APRIL 2022
************************************************************************/
function deletedata($deleteId='')
{  
	$this->admin_model->authCheck('delete_data');
	$this->common_model->deleteData('uw_contacts','id',(int)$deleteId);
	$this->session->set_flashdata('alert_success',lang('deletesuccess'));
	
	redirect(correctLink('ALLCONTACTDATA',getCurrentControllerPath('index')));
}

/***********************************************************************
	** Function name 	: exportexcel
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for export deleted users data
	** Date 			: 09 APRIL 2022
	** Updated Date 	: 
	** Updated By   	: 
	************************************************************************/
	function exportexcel($load_balance_id='')
	{  
		$this->admin_model->authCheck('admin_view');
		//Generating Logs
		$this->common_model->generateLogs();
		/* Export excel button code */
		//echo $load_balance_id; die();
		$whereCon['where']		= 	'';		
		$shortField 			= 	array('id'=> -1);
		$tblName 				= 	'uw_contacts';

		$data        			=   $this->common_model->getData('multiple',$tblName,$whereCon,$shortField,'0','0');

        $spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setCellValue('A1', 'Sl.No');
		$sheet->setCellValue('B1', 'Name');
		$sheet->setCellValue('C1', 'Email');
		$sheet->setCellValue('D1', 'Mobile No.');
		$sheet->setCellValue('E1', 'Subject');
		$sheet->setCellValue('F1', 'Message');
		
	$slno = 1;
	$start = 2;
		foreach($data as $d){
			$sheet->setCellValue('A'.$start, $slno);
			$sheet->setCellValue('B'.$start, $d['name']);
			$sheet->setCellValue('C'.$start, $d['email']);
			$sheet->setCellValue('D'.$start, $d['mobile']);
			$sheet->setCellValue('E'.$start, $d['subject']);
			$sheet->setCellValue('F'.$start, $d['message']);
			
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
		$sheet->getStyle('A1:F1')->getFont()->setBold(true);		
		$sheet->getStyle('A1:F1')->applyFromArray($styleThinBlackBorderOutline);
		//Alignment
		//fONT SIZE
		//$sheet->getStyle('A1:D10')->getFont()->setSize(12);
		//$sheet->getStyle('A1:D2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
		//$sheet->getStyle('A2:D1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
		//Custom width for Individual Columns
		$sheet->getColumnDimension('A')->setWidth(5);
		$sheet->getColumnDimension('B')->setWidth(30);
		$sheet->getColumnDimension('C')->setWidth(30);
		$sheet->getColumnDimension('D')->setWidth(30);
		$sheet->getColumnDimension('E')->setWidth(30);
		$sheet->getColumnDimension('F')->setWidth(30);


		$curdate = date('d-m-Y H:i:s');
		$writer = new Xlsx($spreadsheet);
		$filename = 'Recharge-Coupon-list'.$curdate;
		ob_end_clean();
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"'); 
		header('Cache-Control: max-age=0');
		$writer->save('php://output');
		//endif;
		/* Export excel END */
	}

}
?>
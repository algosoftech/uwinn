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

class Alllottowinners extends CI_Controller {

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
	 + + Date 			: 01 November 2023
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function index($pid='')
	{	
		$this->admin_model->authCheck();
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'products';
		$data['activeSubMenu'] 				= 	'alllottoproducts';
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
		$this->session->set_userdata('ALLLOTTOWINNERSDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	$qStringdata[1]?'?'.$qStringdata[1]:'';
		$tblName 							= 	'da_lotto_winners';
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
		$this->layouts->set_title('All Winners | Winners | Dealz Arabia');
		$this->layouts->admin_view('winners/alllottowinners/index',array(),$data);
	}	// END OF FUNCTION

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name 	: addeditdata
	 + + Developed By 	: Dilip Halder	
	 + + Purpose  		: This function used for Add Edit data
	 + + Date 			: 01 November 2023
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function addeditdata($editId='')
	{	
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'products';
		$data['activeSubMenu'] 				= 	'alllottoproducts';
		
		$subadmin = $this->session->userdata('HCAP_ADMIN_TYPE');

		if($editId):
			
			if($subadmin != 'Sub Admin'):
			  $this->admin_model->authCheck('edit_data');
			endif;

			$data['EDITDATA']				=	$this->common_model->getDataByParticularField('da_lotto_winners','lotto_winners_id',(int)$editId);
			// echo '<pre>';print_r($data['EDITDATA']);die;
		else:
			if($subadmin != 'Sub Admin'):
			  $this->admin_model->authCheck('add_data');
			endif;
			
		endif;
		$AvailableWinnerCouponList = $data['EDITDATA'];

		$fields = array('*');
		$tableName='da_lotto_prize';
		$Prizewcon['where']  = array('product_id' => (int)$data['EDITDATA']['product_id'] , 'status' => "A");
		$data['prize_data']  = $this->common_model->getData('single',$tableName,$Prizewcon);
		$data['result'] 	 = $this->geneal_model->getWinnerList($AvailableWinnerCouponList);
		// echo "<pre>";print_r($data);die();
		$this->layouts->set_title('Add/Edit Winners');
		$this->layouts->admin_view('winners/alllottowinners/addeditdata',array(),$data);
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
		$this->common_model->editData('da_lotto_winners',$param,'lotto_winners_id',(int)$changeStatusId);
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		
		redirect(correctLink('ALLLOTTOWINNERSDATA',getCurrentControllerPath('index')));
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
		$this->common_model->deleteData('da_lotto_winners','lotto_winners_id',(int)$deleteId);
		$this->session->set_flashdata('alert_success',lang('deletesuccess'));
		
		redirect(correctLink('ALLLOTTOWINNERSDATA',getCurrentControllerPath('index')));
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
		$data        			=   $this->common_model->getData('multiple','da_lotto_winners',$wcon);//echo '<pre>';print_r($data);die;

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
		$product_data = $this->common_model->getDataByParticularField('da_lotto_products', 'products_id', $user_data[0]['product_id']);
	else:
		$product_data = $this->common_model->getDataByParticularField('da_lotto_products', 'products_id', $user_data['product_id']);
	endif;

	$prize_data = $this->common_model->getDataByParticularField('da_prize', 'product_id', $product_data['products_id']);

	
	// $winner_data  = $this->common_model->getDataByParticularField('da_lotto_winners', 'product_id', $product_data['products_id']);

	$tblName 							=   'da_lotto_winners';
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

	/***********************************************************************
	** Function name : checkDeplicacy
	** Developed By : Dilip	Halder
	** Purpose  : This function used for export deleted users data
	** Date : 15 MAY 2022
	** Updated Date : Afsar Ali
	** Updated By   : 16 May 2022
	************************************************************************/
	public function generatewinner()
	{
		//Fetching Product data....
	    $ProcuctID  				=  $this->session->userdata('productID4winners');
		$tableName2					=  'da_lotto_products';
		$USERwhereCon['where']  	= array("products_id" => (int)$ProcuctID , 'remarks' => 'lotto-products');
		$productDetails 			= $this->common_model->getData('single',$tableName2,$USERwhereCon);
		// echo "<pre>";print_r($ProcuctID);die();

		//Fetching Product data....
		$tableName3					=  'da_lotto_orders';
		$ORDwhereCon['where']  		= array("product_id" => (int)$ProcuctID, "status" => "A");
		$orderDetails 				= $this->common_model->getData('count',$tableName3,$ORDwhereCon);

		if($orderDetails  > 0):

		    // Checking Coupon under rang of this 
		    if( $productDetails['lotto_type'] == 6):
	            $numbeUnique  =  'Y';
	        elseif($productDetails['lotto_type'] == 5):
	            $numbeUnique  =  'Y';
	        elseif($productDetails['lotto_type'] == 4):
	            $numbeUnique  =  'N';
	        elseif($productDetails['lotto_type'] == 3 ):
	            $numbeUnique  =  'N';
	        endif;
            
            $ticket_range =  $productDetails['lotto_range'];
	        $lotto_type   =  $productDetails['lotto_type'];
	 
       
	       
			$resultNumbers = $this->lottogenerate($lotto_type,$ticket_range ,$numbeUnique);

			// $UniqueCoupons = $this->checkresult($resultNumbers,$soldoutNumbers);

			// echo json_encode(array('UniqueCoupons'=>$UniqueCoupons));
			//Adding new Generated coupon.
			if($resultNumbers):
				$param['lotto_winners_id']	=	(int)$this->common_model->getNextSequence('lotto_lotto_winners_id');
				$param['lotto_type']		=	$lotto_type;
				$param['coupon_code']		=	$resultNumbers;
				$param['product_id']		=	(int)$this->session->userdata('productID4winners');
				$param['creation_ip']		=	currentIp();
				$param['creation_date']		=	date('Y-m-d H:i');
				$param['created_by']		=	(int)$this->session->userdata('HCAP_ADMIN_ID');
				$param['status']			=	'A';
				// echo "<pre>";print_r($param);die();
				$alastInsertId				=	$this->common_model->addData('da_lotto_winners',$param);
				$this->session->set_flashdata('alert_success',lang('addsuccess'));
			endif;
		else:
			$this->session->set_flashdata('alert_error',lang('updatewarning'));
		endif;

		redirect(correctLink('ALLLOTTOWINNERSDATA',getCurrentControllerPath('index/'.base64_encode($param['product_id']))));
	}




	public function lottogenerate($lotto_type,$ticket_range ,$numbeUnique)
	{
		$uniqueNumbers = [];
		// Generate 5 unique random numbers and add them to the array
		while (count($uniqueNumbers) < $lotto_type):
		    $randomNumber = rand(1, $ticket_range); // Change the range as per your requirement
		    
		    if($numbeUnique == "Y" && !in_array($randomNumber, $uniqueNumbers) ):
		        $uniqueNumbers[] = $randomNumber;
		    endif;

		 	if($numbeUnique == "N"):
		        $uniqueNumbers[] = $randomNumber;
		    endif;
		endwhile;

		return $uniqueNumbers;
	}

	public function checkresult($resultNumbers,$soldoutNumbers='')
	{
	 	foreach ($soldoutNumbers as $key => $soldoutNumber):
	 		// $resultNumbers = array(10,29,2);
	 		// echo "<pre>";
	 		// print_r($resultNumbers);
	 		// die();

	        if (count($soldoutNumber) == count($resultNumbers) && array_search($resultNumbers, $soldoutNumbers) !== false):
	            return true;
	        else:
				return $resultNumbers;
	        endif;
    	endforeach;
	}


	public function addwinners($lotto='',$matchedCount='')
	{

		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'products';
		$data['activeSubMenu'] 				= 	'alllottoproducts';

		$productID4Prize = $this->session->userdata('productID4winners');

		$baseUrl 							= 	getCurrentControllerPath('addwinners');
		// $tblName 			= 'da_lotto_products';
		// $whereCon['where']	= array('remarks' => 'lotto-products','products_id' =>(int)$productID4Prize );
		// $ProductDetails 	= $this->common_model->getData('single',$tblName,$whereCon);

		$tblName1 			= 'da_lotto_winners';
		$whereCon1['where']	= array('product_id' =>  (int)$productID4Prize,'status' => 'A');
		$lottoOrders 		= $this->common_model->getData('single',$tblName1,$whereCon1);

		$lotto_type 		=  $lottoOrders['lotto_type'];

		if($lotto == 'straight'):
		  $serchType = 'straight_add_on_amount';
		elseif($lotto == 'rumblemix'):
		  $serchType = 'rumble_add_on_amount';
		elseif($lotto == 'reverse'):
		  $serchType = 'reverse_add_on_amount';
		endif;

		$MatchedCoupons = $this->geneal_model->allMacthedCoupon($lottoOrders,$lotto_type,$serchType);

		$result = array();
		//filtering matched values and generating result.
		foreach ($MatchedCoupons as $key => $firstitems):
		 	foreach ($firstitems as $key => $items):
				if($items['matched_count'] == $matchedCount):
					$result[] = $items ;
				endif;
			endforeach;

		endforeach;
		
		$data['matchedCoupons'] 	= $result;
		$data['matchedLottoType'] 	=  $matchedCount;
		$data['lotto_type'] 		=  $lotto_type;
		$data['macthedtype'] 		=  $lotto;

		// echo "<pre>"; print_r($data);die();

		$this->layouts->set_title('Add/Edit Winners');
		$this->layouts->admin_view('winners/alllottowinners/addwinners',array(),$data);

	}

	public function winstatus($macthedtype='',$orderId='',$matchedCount='',$winstatus='')
	{
		$this->admin_model->authCheck('edit_data');


		//Winner Coupon Order Details..
		$productID4winners = $this->session->userdata('productID4winners');
		$WinnerDetails 	   = $this->common_model->getDataByParticularField('da_lotto_winners','product_id',(int)$productID4winners);

		$OrderDetails =	$this->common_model->getDataByParticularField('da_lotto_orders','order_id',$orderId);
		// echo '<pre>';print_r($OrderDetails);die();
		// echo '<pre>';print_r($WinnerDetails);die();

		if(!empty($OrderDetails)):
			
			if($WinnerDetails[$macthedtype.'_'.'winners'.$matchedCount]):
				$winners = $WinnerDetails[$macthedtype.'_'.'winners'.$matchedCount] +1;
				$param[$macthedtype.'_'.'winners'.$matchedCount]  = (int)$winners;
			else:
				$param[$macthedtype.'_'.'winners'.$matchedCount]  = (int)1;
			endif;



			$dataParam['status']		 =	$winstatus;
			$dataParam['order_id']		 =	$orderId;
			$dataParam['macthced_count'] =	(int)$matchedCount;
			$dataParam['announcedDate']	 =	(int)strtotime(date('Y-m-d h:i:s'));;
			
			if($WinnerDetails[$macthedtype.'_'.'winnerlist'.$matchedCount]):
				$oldResult =array();
				$oldResultData = $WinnerDetails[$macthedtype.'_'.'winnerlist'.$matchedCount];

				foreach ($oldResultData as $key => $item):
					// echo "<pre>";print_r($item);
					array_push($oldResult, $item);
				endforeach;

				array_push($oldResult, $dataParam);

				$param[$macthedtype.'_'.'winnerlist'.$matchedCount]		= $oldResult;
			else:
				$param[$macthedtype.'_'.'winnerlist'.$matchedCount]		= array($dataParam);
			endif;
			$param['update_ip']		=	currentIp();
			$param['update_date']	=	(int)strtotime(date('Y-m-d h:i'));
			$param['updated_by']	=	(int)$this->session->userdata('HCAP_ADMIN_ID');

			// echo "<pre>";print_r($param);die();

			$this->common_model->editData('da_lotto_winners',$param,'product_id',(int)$productID4winners);
			$this->session->set_flashdata('alert_success',lang('statussuccess'));

		else:
			$this->session->set_flashdata('alert_success',lang('accessstatusdenied'));
		endif;

		redirect(correctLink('ALLLOTTOWINNERSDATA',getCurrentControllerPath('index')));

	}

	public function winnerannouncement($id)
	{
		$whereCon 					 = array('status'=> 'A' , 'product_id'=> (int)$id );
	 	$AvailableWinnerCouponList	 =	$this->common_model->getData('single','da_lotto_winners',$whereCon);
   		$winnerList 	 			 = $this->geneal_model->getWinnerList($AvailableWinnerCouponList);

   		// echo "<pre>";
   		// print_r($winnerList);
   		// die();


   		$lotto_type = $AvailableWinnerCouponList['lotto_type'];
   		
		$resultlist= array(); 
   		for ($i=1; $i <= $lotto_type ; $i++):
			$count = 0;
	   		foreach ($winnerList['straightMatchedCoupons'] as $key => $FirstItem):
				foreach($FirstItem as $key => $items):
					if($items['matched_count'] == $i):
						++$count;
						$result['status']   		= "approve";
						$result['order_id'] 		= $items['orderID'];
						$result['matched_count']    = $items['matched_count'];
						$result['announcedDate']    = strtotime(date('d-m-Y h:m:s'));
					 	$resultlist[] = $result;
						// array_push($resultlist, $result);

					endif;
	   			endforeach;
	   		endforeach;

	   		$param['straight_winnerlist' ]= $resultlist;
	   		$param['straight_winners'.$i ]= $count;

   		endfor;
   		
   		//rumbleMixCoupons
   		$resultlist= array(); 
   		for ($i=1; $i <= $lotto_type ; $i++):
			$count = 0;
	   		foreach ($winnerList['rumbleMixCoupons'] as $key => $FirstItem):
				foreach($FirstItem as $key => $items):
					if($items['matched_count'] == $i):
						++$count;
						$result['status']   		= "approve";
						$result['order_id'] 		= $items['orderID'];
						$result['matched_count']    = $items['matched_count'];
						$result['announcedDate']    = strtotime(date('d-m-Y h:m:s'));
					 	$resultlist[] = $result;
						// array_push($resultlist, $result);

					endif;
	   			endforeach;
	   		endforeach;

	   		$param['rumblemix_winnerlist' ]= $resultlist;
	   		$param['rumblemix_winners'.$i ]= $count;

   		endfor;

   		$resultlist= array(); 
   		for ($i=1; $i <= $lotto_type ; $i++):
			$count = 0;
	   		foreach ($winnerList['reverseCoupons'] as $key => $FirstItem):
				foreach($FirstItem as $key => $items):
					if($items['matched_count'] == $i):
						++$count;
						$result['status']   		= "approve";
						$result['order_id'] 		= $items['orderID'];
						$result['matched_count']    = $items['matched_count'];
						$result['announcedDate']    = strtotime(date('d-m-Y h:m:s'));
					 	$resultlist[] = $result;
						// array_push($resultlist, $result);

					endif;
	   			endforeach;
	   		endforeach;

	   		$param['reverse_winnerlist' ]= $resultlist;
	   		$param['reverse_winners'.$i ]= $count;

   		endfor;
   		$param['update_ip']		=	currentIp();
		$param['update_date']	=	(int)strtotime(date('Y-m-d h:i'));
   		$param['updated_by']	=	(int)$this->session->userdata('HCAP_ADMIN_ID');
		$this->common_model->editData('da_lotto_winners',$param,'product_id',(int)$id);
		 
		$this->session->set_flashdata('alert_success',lang('winner_announced'));
		redirect(correctLink('ALLLOTTOWINNERSDATA',getCurrentControllerPath('index')));
	}


}
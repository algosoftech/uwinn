<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class allhourlygames extends CI_Controller {

	public function  __construct() 
	{ 
		parent:: __construct();
		error_reporting(0);
		$this->load->model(array('admin_model','emailtemplate_model','sms_model','notification_model','geneal_model'));
		$this->lang->load('statictext', 'admin');
		$this->load->helper('common');
	} 

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name 	: index
	 + + Developed By 	: Dilip Halder
	 + + Purpose  		: This function used for index
	 + + Date 			: 30 January 2026
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	 public function index()
	 {	
		$this->admin_model->authCheck('view_data');
		$data['error'] 		   = '';
		$data['activeMenu']    = 'hourlygame';
		$data['activeSubMenu'] = "allhourlygames";
		
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
		endif;
				
		$shortField 						= 	array('creation_date'=>-1);
		
		$baseUrl 							= 	getCurrentControllerPath('index');
		$this->session->set_userdata('ALLHOURLYGAMEDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	isset($qStringdata[1]) && $qStringdata[1] ? '?'.$qStringdata[1] : '';
		$tblName 							= 	'uw_hourly_games';
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
		// echo "<pre>";print_r($data);die();
		
		$this->layouts->set_title('All Hourly games | Products | UWINN');
		$this->layouts->admin_view('hourlygame/allhourlygames/index',array(),$data);
	 }	// END OF FUNCTION

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name : addeditdata
	 + + Developed By  : Dilip Halder
	 + + Purpose  	   : This function used for Add Edit data
	 + + Date 		   : 30 January 2026
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function addeditdata($editId='')
	{		
		$data['error'] 		   = '';
		$data['activeMenu']    = 'hourlygame';
		$data['activeSubMenu'] = 'allhourlygames';
		if($editId):
			$this->admin_model->authCheck('edit_data');
			$data['EDITDATA']  = $this->common_model->getDataByParticularField('uw_hourly_games','_id', new MongoDB\BSON\ObjectID($editId));
		else:
			$this->admin_model->authCheck('add_data');
		endif;
		// echo "<pre>";print_r($data['EDITDATA']);die();
		if($this->input->post('SaveChanges')):
			$error = 'NO';

			$this->form_validation->set_rules('category_oid'      , 'Category'         , 'trim|required');
			$this->form_validation->set_rules('sub_category_oid'  , 'Sub Category'     , 'trim|required');
			$this->form_validation->set_rules('title'            , 'Title'            , 'trim|required');
			$this->form_validation->set_rules('description'      , 'Description'      , 'trim');
			$this->form_validation->set_rules('price'            , 'Price'            , 'trim|required');
			$this->form_validation->set_rules('game_type'        , 'Game Type'        , 'trim|required');
			$this->form_validation->set_rules('coupon_selection_type','Coupon Selection Type', 'trim|required');
			$this->form_validation->set_rules('number_repeat'    , 'Number Repeat'    , 'trim|required');
			$this->form_validation->set_rules('number_range_start', 'Number Range Start', 'trim|required');
			$this->form_validation->set_rules('number_range_end' , 'Number Range End' , 'trim|required');	
			$this->form_validation->set_rules('seq_order'        , 'Sequence Order'   , 'trim|required');
			$this->form_validation->set_rules('show_on[]'        , 'Show On'          , 'trim|required');
			$this->form_validation->set_rules('start_date'       , 'Start Date'       , 'trim|required');
			$this->form_validation->set_rules('is_24_hours'      , 'Is 24 Hours'      , 'trim|required');
			$this->form_validation->set_rules('expiry_date'      , 'Expiry Date'      , 'trim|required');
			$this->form_validation->set_rules('SaveChanges'      , 'SaveChanges'      , 'trim|required');
			if($this->form_validation->run() && $error == 'NO'):

				/* Game Image Section start*/
				if($_FILES['game_image']['name']):
					$ufileName	  = str_replace(" ","_",$_FILES['game_image']['name']);
					$utmpName	  = $_FILES['game_image']['tmp_name'];
					$ufileExt     = pathinfo($ufileName);
					$unewFileName = $_FILES['game_image']['name'];
					$filePath     = fileFCPATH .'assets/gamesImage/'.$_FILES['game_image']['name'];
					if(file_exists($filePath)):
						$unewFileName =	$ufileExt['filename'] .'_'.$this->common_model->random_strings(8).'.'.$ufileExt['extension'];
					endif;
					$this->load->library("upload_crop_img");
					$imageName = $data['EDITDATA']['game_image'];
					if($imageName):
						$this->upload_crop_img->_delete_image(trim($imageName)); 
					endif;
					$uimageLink						=	$this->upload_crop_img->_upload_image($ufileName,$utmpName,'gamesImage',$unewFileName,'');
					if($uimageLink != 'UPLODEERROR'):
						$param['game_image']		= 	$uimageLink;
					endif;
				endif;

				/* Product Image Section End*/
				$param['title']				 = addslashes($this->input->post('title'));
				$param['title_slug']		 = url_title(strtolower($this->input->post('title').' '.date('dMY',strtotime($this->input->post('draw_date')))));
				$param['description']		 = addslashes($this->input->post('description'));
				$param['category_oid']	     = new MongoDB\BSON\ObjectId($this->input->post('category_oid'));
				$param['sub_category_oid']	 = new MongoDB\BSON\ObjectId($this->input->post('sub_category_oid'));
				$param['price']				 = (int)$this->input->post('price');
				$param['game_type']			 = (int)$this->input->post('game_type');
				$param['coupon_selection_type'] = $this->input->post('coupon_selection_type');
				$param['number_repeat']		 = $this->input->post('number_repeat');
				$param['number_range_start'] = (int)$this->input->post('number_range_start');
				$param['number_range_end']	 = (int)$this->input->post('number_range_end');
				$param['seq_order']			 = (int)$this->input->post('seq_order');
				$param['show_on']			 = $this->input->post('show_on');
				$param['is_24_hours']		 = $this->input->post('is_24_hours');
				$param['start_date']		 = strtotime($this->input->post('start_date'));
				$param['expiry_date']		 = strtotime($this->input->post('expiry_date'));
				if(empty($editId)):
					$param['draw_date']		 = strtotime($this->input->post('expiry_date'));
					$param['status']		 = 'A';
					$param['prize_setting']  = 'disabled';
					$param['products_id']	 = (int)$this->common_model->getNextSequence('uw_hourly_games');
					$param['creation_ip']	 = currentIp();
					$param['creation_date']	 = (int)$this->timezone->utc_time();//currentDateTime();
					$param['created_by']	 = (int)$this->session->userdata('UW_ADMIN_ID');
					$alastInsertId			 =	$this->common_model->addData('uw_hourly_games',$param);
					$this->session->set_flashdata('alert_success',lang('addsuccess'));
				else:
					if($param['price'] != $data['EDITDATA']['price']):
						$param['prize_setting']  = 'disabled';
					endif;
					$param['update_ip']		 = currentIp();
					$param['update_date']	 = (int)$this->timezone->utc_time();//currentDateTime();
					$param['updated_by']	 = (int)$this->session->userdata('UW_ADMIN_ID');
					$this->common_model->editData('uw_hourly_games',$param,'_id', new MongoDB\BSON\ObjectID($editId));
					$this->session->set_flashdata('alert_success',lang('updatesuccess'));
				endif;
				redirect(correctLink('ALLHOURLYGAMEDATA',getCurrentControllerPath('index')));
			endif;
		endif;
		
		$this->layouts->set_title('Add/Edit Hourly game | Products | UWINN');
		$this->layouts->admin_view('hourlygame/allhourlygames/addeditdata',array(),$data);
	}	// END OF FUNCTION			
        
	/***********************************************************************
	** Function name 	: changestatus
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for change status
	** Date 			: 30 January 2026
	************************************************************************/
	function changestatus($changeStatusId='',$statusType='')
	{  
		$this->admin_model->authCheck('edit_data');
		$param['status'] =	$statusType;
		$this->common_model->editData('uw_hourly_games',$param,'_id', new MongoDB\BSON\ObjectID($changeStatusId));
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		
		redirect(correctLink('ALLHOURLYGAMEDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name 	: deletedata
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for delete data
	** Date 			: 30 January 2026
	************************************************************************/
	function deletedata($deleteId='')
	{  
		$this->admin_model->authCheck('delete_data');
		$this->common_model->deleteData('uw_hourly_games','_id', new MongoDB\BSON\ObjectID($deleteId));
		$this->session->set_flashdata('alert_success',lang('deletesuccess'));
		redirect(correctLink('ALLHOURLYGAMEDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name 	: getsub_categoryData
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for delete data
	** Date 			: 30 January 2026
	************************************************************************/
	public function getsubcategoryData(){
		
		$categoryId        = $this->input->post('category_oid');
		$subCategoryId     = $this->input->post('_id');
		$whereCon['where'] = array('category_oid' => new MongoDB\BSON\ObjectId($categoryId));	
		$shortField        = array('sub_category_name' => 'ASC');
		$subCategoryData  = $this->common_model->getData('multiple','uw_sub_category',$whereCon,$shortField);
		
		if(!empty($subCategoryData)):
			$html = '<option value="">Select Sub Category</option>';
			
			foreach($subCategoryData as $subCategoryData):
				if($subCategoryId == $subCategoryData['_id']->{'$id'}): $select = 'selected="selected"'; else: $select = ''; endif;
				$html .='<option '.$select.' value="'.$subCategoryData["_id"]->{'$id'}.'">'.stripslashes($subCategoryData["sub_category"]).'</option>';
			endforeach;
		else:
			$html = '<option value="">No Sub Category</option>';
		endif;
		echo $html;
		die();
	}

	/***********************************************************************
	** Function name : exportexcel
	** Developed By  : Dilip Halder
	** Purpose       : This function used for export deleted users data
	** Date          : 30 January 2026
	************************************************************************/
	function exportexcel()
	{  
		$this->admin_model->authCheck('view_data');
		/* Export excel button code */
		$wcon['where']          =   '';
		$data        			=   $this->common_model->getData('multiple','uw_hourly_games',$wcon);//echo '<pre>';print_r($data);die;

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

	/***********************************************************************
	** Function name 	: orderstatus
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for change order status
	** Date 			: 30 January 2026
	************************************************************************/
	function orderstatus($changeStatusId='',$statusType='')
	{  
		//echo $changeStatusId; die();
		$this->admin_model->authCheck('edit_data');
		$param['status']		=	$statusType;
		$this->common_model->editData('uw_hourly_games',$param,'_id', new MongoDB\BSON\ObjectID($changeStatusId));
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		redirect(correctLink('ALLHOURLYGAMEDATA',getCurrentControllerPath('index')));
	}

	/***********************************************************************
	** Function name 	: settings
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for settings
	** Date 			: 30 January 2026
	************************************************************************/
	public function settings($editId='')
	{		
		$data['error'] 		   = '';
		$data['activeMenu']    = 'hourlygame';
		$data['activeSubMenu'] = 'allhourlygames';

		$tblName  			    = 'uw_hourly_games';
		$this->session->set_userdata('ALLHOURLYGAMEDATA',currentFullUrl());
		if($editId):
			$this->admin_model->authCheck('edit_data');
			$data['EDITDATA']	=	$this->common_model->getDataByParticularField($tblName,'_id', new MongoDB\BSON\ObjectID($editId));
		else:	
			$this->admin_model->authCheck('view_data');
		endif;
		
		if($this->input->post('SaveChanges')):
			$error					=	'NO';

			$this->form_validation->set_rules('prize_title', 'Prize Title', 'trim|required');
			$this->form_validation->set_rules('primary_color', 'Primary Color', 'trim|required');
			$this->form_validation->set_rules('secondary_color', 'Secondary Color', 'trim|required');
			$this->form_validation->set_rules('is_straight_enable', 'Straight Enable', 'trim|required');
			$this->form_validation->set_rules('is_straight_show_hide', 'Straight Show Hide', 'trim|required');
			$this->form_validation->set_rules('is_straight_checkbox', 'Straight Checkbox', 'trim|required');
			$this->form_validation->set_rules('straigt_heading_name', 'Straight Heading Name', 'trim|required');

			$this->form_validation->set_rules('is_rumble_enable', 'Rumble Enable/Disable', 'trim|required');
			$this->form_validation->set_rules('is_rumble_show_hide', 'Rumble Show/Hide', 'trim|required');
			$this->form_validation->set_rules('is_rumble_checkbox', 'Rumble Checkbox', 'trim|required');
			$this->form_validation->set_rules('rumble_heading_name', 'Rumble Heading Name', 'trim|required');

			$this->form_validation->set_rules('is_chance_enable', 'Chance Enable/Disable', 'trim|required');
			$this->form_validation->set_rules('is_chance_show_hide', 'Chance Show/Hide', 'trim|required');
			$this->form_validation->set_rules('is_chance_checkbox', 'Chance Checkbox', 'trim|required');
			$this->form_validation->set_rules('chance_heading_name', 'Chance Heading Name', 'trim|required');

			if($this->input->post('is_straight_enable') == 'Y'):
				
				$this->form_validation->set_rules('straight_prize_title', 'Straight Prize Title', 'trim|required');
				for($i=1; $i<=$data['EDITDATA']['game_type']; $i++):
					$this->form_validation->set_rules('straight_prize_'.$i, 'Straight Prize '.$i, 'trim|required');
				endfor;
			endif;

			if($this->input->post('is_rumble_enable') == 'Y'):
				$this->form_validation->set_rules('rumble_prize_title', 'Rumble Prize Title', 'trim|required');
				for($i=1; $i<=$data['EDITDATA']['game_type']; $i++):
					$this->form_validation->set_rules('rumble_prize_'.$i, 'Rumble Prize '.$i, 'trim|required');
				endfor;
			endif;

			if($this->input->post('is_chance_enable') == 'Y'):
				$this->form_validation->set_rules('chance_prize_title', 'Chance Prize Title', 'trim|required');
				for($i=1; $i<=$data['EDITDATA']['game_type']; $i++):
					$this->form_validation->set_rules('chance_prize_'.$i, 'Chance Prize '.$i, 'trim|required');
				endfor;
			endif;
			
			if($this->form_validation->run() && $error == 'NO'):

				$param['prize_title']           = $this->input->post('prize_title');
				$param['primary_color']         = $this->input->post('primary_color');
				$param['secondary_color']       = $this->input->post('secondary_color');
				$param['is_straight_enable']    = $this->input->post('is_straight_enable');
				$param['is_rumble_enable']      = $this->input->post('is_rumble_enable');
				$param['is_chance_enable']      = $this->input->post('is_chance_enable');

				$param['is_straight_show_hide'] = $this->input->post('is_straight_show_hide');
				$param['is_rumble_show_hide']    = $this->input->post('is_rumble_show_hide');
				$param['straight_prize_title']   = $this->input->post('straight_prize_title');

				$param['is_straight_checkbox']  = $this->input->post('is_straight_checkbox');
				$param['straigt_heading_name']  = $this->input->post('straigt_heading_name');

				$param['is_rumble_checkbox']     = $this->input->post('is_rumble_checkbox');
				$param['rumble_heading_name']    = $this->input->post('rumble_heading_name');
				
				$param['is_chance_show_hide']    = $this->input->post('is_chance_show_hide');
				$param['is_chance_checkbox']     = $this->input->post('is_chance_checkbox');
				$param['chance_heading_name']    = $this->input->post('chance_heading_name');
				
				$param['rumble_prize_title']     = $this->input->post('rumble_prize_title');
				$param['chance_prize_title']     = $this->input->post('chance_prize_title');
				for($i=1; $i<=$data['EDITDATA']['game_type']; $i++):
					$param['straight_prize_'.$i]   = $this->input->post('straight_prize_'.$i)?$this->input->post('straight_prize_'.$i):0;
					$param['rumble_prize_'.$i]     = $this->input->post('rumble_prize_'.$i)?$this->input->post('rumble_prize_'.$i):0;
					$param['chance_prize_'.$i]     = $this->input->post('chance_prize_'.$i)?$this->input->post('chance_prize_'.$i):0;
				endfor;

				/* Game Image Section start*/
				if($_FILES['prize_image']['name']):
					$ufileName	  = str_replace(" ","_",$_FILES['prize_image']['name']);
					$utmpName	  = $_FILES['prize_image']['tmp_name'];
					$ufileExt     = pathinfo($ufileName);
					$unewFileName = $_FILES['prize_image']['name'];
					$filePath     = fileFCPATH .'assets/prizeImage/'.$_FILES['prize_image']['name'];
					if(file_exists($filePath)):
						$unewFileName =	$ufileExt['filename'] .'_'.$this->common_model->random_strings(8).'.'.$ufileExt['extension'];
					endif;
					$this->load->library("upload_crop_img");
					$imageName = $data['EDITDATA']['prize_image'];
					if($imageName):
						$this->upload_crop_img->_delete_image(trim($imageName)); 
					endif;
					$uimageLink						=	$this->upload_crop_img->_upload_image($ufileName,$utmpName,'gamesImage',$unewFileName,'');
					if($uimageLink != 'UPLODEERROR'):
						$param['prize_image']		= 	$uimageLink;
					endif;
				endif;
				$param['prize_setting']         = "enabled";
				$param['update_ip']             = currentIp();
				$param['update_date']           = (int)$this->timezone->utc_time();
				$param['updated_by']            = (int)$this->session->userdata('UW_ADMIN_ID');
				$this->common_model->editData($tblName,$param,'_id', new MongoDB\BSON\ObjectID($editId));
				$this->session->set_flashdata('alert_success',lang('updatesuccess'));
				redirect(correctLink('ALLHOURLYGAMEDATA',getCurrentControllerPath('index')));
			endif;
		endif;
		// echo "<pre>";print_r($data);die();
		$this->layouts->set_title('Add/Edit Hourly game settings | Products | UWINN');
		$this->layouts->admin_view('hourlygame/allhourlygames/settings',array(),$data);
	}
	// END OF FUNCTION	

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	+ + Function name : updateDraw
	+ + Developed By  : Dilip Halder
	+ + Purpose  	   : This function used to update draw.
	+ + Date 		   : 31 March 2026
	++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function updateDraw($editId='')
	{
		$this->admin_model->authCheck('edit_data');

		$tblName      = "uw_hourly_games";
		$drawTblName  = "uw_hourly_games_draw_records";
		$where['where'] = array('_id' => new MongoDB\BSON\ObjectID($editId));
		$gameData = $this->common_model->getData('single', $tblName, $where);
		if(!empty($gameData)):
			$startDate   = $gameData['start_date'];
			$expiryDate  = $gameData['expiry_date'];
			$currentDate = strtotime(date('Y-m-d H:i'));

			if($expiryDate < $currentDate):
				$newStartDate  = strtotime('+1 day',$startDate);
				$newExpiryDate = strtotime('+1 day',$expiryDate);
				$param['start_date']  = (int)$newStartDate;
				$param['expiry_date'] = (int)$newExpiryDate;
				$this->common_model->editData($tblName,$param,'_id', new MongoDB\BSON\ObjectID($editId));
				$this->session->set_flashdata('alert_success',lang('updatesuccess'));
			else:
				$error = 'Please try after '.date('Y-m-d H:i',$expiryDate);
				$this->session->set_flashdata('alert_error', $error);
			endif;
			redirect(correctLink('ALLHOURLYGAMEDATA',getCurrentControllerPath('index')));
		endif;
	}

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	+ + Function name  : manageDrawTime
	+ + Developed By   : Dilip Halder
	+ + Purpose  	   : Manage hourly draw time slots for a product.
	+ + Date 		   : 29 June 2026
	++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function manageDrawTime($productOid='', $editId='')
	{
		$this->admin_model->authCheck('view_data');
		$data['error'] 		   = '';
		$data['activeMenu']    = 'hourlygame';
		$data['activeSubMenu'] = 'allhourlygames';
		$data['EDITDATA']      = array();
		$drawTblName           = 'uw_hourly_draw_time';
		$gameTblName           = 'uw_hourly_games';

		$campaignWhere['where'] = array('is_24_hours' => 'Y');
		$shortField             = array('creation_date' => -1);
		$data['CAMPAIGNS24H']   = $this->common_model->getData('multiple', $gameTblName, $campaignWhere, $shortField);

		if($editId):
			$this->admin_model->authCheck('edit_data');
			$data['EDITDATA'] = $this->common_model->getDataByParticularField($drawTblName, '_id', new MongoDB\BSON\ObjectID($editId));
		else:
			$this->admin_model->authCheck('add_data');
			$existingDrawTimes = $this->common_model->getData('multiple', $drawTblName, array(), array('creation_date' => -1), 1);
			if(!empty($existingDrawTimes)):
				$data['EDITDATA'] = $existingDrawTimes[0];
			endif;
		endif;

		if($this->input->post('SaveChanges')):
			$error = 'NO';
			$this->form_validation->set_rules('draw_time_start', 'Draw Time Start', 'trim|required');
			$this->form_validation->set_rules('draw_time_end', 'Draw Time End', 'trim|required');
			$this->form_validation->set_rules('SaveChanges', 'SaveChanges', 'trim|required');

			if($this->form_validation->run() && $error == 'NO'):
				$param['draw_time_start'] = stripslashes($this->input->post('draw_time_start'));
				$param['draw_time_end']   = stripslashes($this->input->post('draw_time_end'));

				if($this->input->post('CurrentDataID') == ''):
					$param['creation_ip']   = currentIp();
					$param['creation_date'] = (int)$this->timezone->utc_time();
					$param['created_by']    = (int)$this->session->userdata('UW_ADMIN_ID');
					$param['status']        = 'A';
					$this->common_model->addData($drawTblName, $param);
					$this->session->set_flashdata('alert_success', lang('addsuccess'));
				else:
					$param['update_ip']   = currentIp();
					$param['update_date'] = (int)$this->timezone->utc_time();
					$param['updated_by']  = (int)$this->session->userdata('UW_ADMIN_ID');
					$this->common_model->editData($drawTblName, $param, '_id', new MongoDB\BSON\ObjectID($this->input->post('CurrentDataID')));
					$this->session->set_flashdata('alert_success', lang('updatesuccess'));
				endif;

				redirect(getCurrentControllerPath('manageDrawTime'));
			endif;
		endif;

		$this->layouts->set_title('Manage Draw Time | Hourly Games | UWINN');
		$this->layouts->admin_view('hourlygame/allhourlygames/managedrawtimes', array(), $data);
	}

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	+ + Function name  : deleteDrawTime
	+ + Developed By   : Dilip Halder
	+ + Purpose  	   : Delete hourly draw time slot.
	+ + Date 		   : 29 June 2026
	++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function deleteDrawTime($productOid='', $deleteId='')
	{
		$this->admin_model->authCheck('edit_data');

		if($deleteId):
			$this->common_model->deleteData('uw_hourly_draw_time', '_id', new MongoDB\BSON\ObjectID($deleteId));
			$this->session->set_flashdata('alert_success', lang('deletesuccess'));
		endif;

		redirect(getCurrentControllerPath('manageDrawTime'));
	}



}
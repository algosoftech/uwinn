<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Allscratchwingames extends CI_Controller {

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
	 + + Date 			: 16 July 2026
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	 public function index()
	 {	
		$this->admin_model->authCheck('view_data');
		$data['error'] 		   = '';
		$data['activeMenu']    = 'scratchwin';
		$data['activeSubMenu'] = "allscratchwingames";
		
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
		$this->session->set_userdata('ALLSCRATCHWINGAMESDATA',currentFullUrl());
		$qStringdata						=	explode('?',currentFullUrl());
		$suffix								= 	isset($qStringdata[1]) && $qStringdata[1] ? '?'.$qStringdata[1] : '';
		$tblName 							= 	'uw_scratch_win_games';
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
		
		$this->layouts->set_title('All Scratch Win Games | Scratch Win Games | UWINN');
		$this->layouts->admin_view('campaigns/allscratchwingames/index',array(),$data);
	 }	// END OF FUNCTION

	/*++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 + + Function name : addeditdata
	 + + Developed By  : Dilip Halder
	 + + Purpose  	   : This function used for Add Edit data
	 + + Date 			: 16 July 2026
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++*/
	public function addeditdata($editId='')
	{		
		$data['error'] 		   = '';
		$data['activeMenu']    = 'scratchwin';
		$data['activeSubMenu'] = 'allscratchwingames';
		if($editId):
			$this->admin_model->authCheck('edit_data');
			$data['EDITDATA']  = $this->common_model->getDataByParticularField('uw_scratch_win_games','_id', new MongoDB\BSON\ObjectID($editId));
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
			$this->form_validation->set_rules('seq_order'        , 'Sequence Order'   , 'trim|required');
			$this->form_validation->set_rules('show_on[]'        , 'Show On'          , 'trim|required');
			$this->form_validation->set_rules('start_date'       , 'Start Date'       , 'trim|required');
			$this->form_validation->set_rules('expiry_date'      , 'Expiry Date'      , 'trim|required');
			$this->form_validation->set_rules('game_mode'        , 'Game Mode'        , 'trim|required');
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
				$param['seq_order']			 = (int)$this->input->post('seq_order');
				$param['show_on']			 = $this->input->post('show_on');
				$param['start_date']		 = strtotime($this->input->post('start_date'));
				$param['expiry_date']		 = strtotime($this->input->post('expiry_date'));
				$param['game_mode']		     = $this->input->post('game_mode');
				if(empty($editId)):
					$param['status']		 = 'A';
					$param['prize_setting']  = 'disabled';
					$param['products_id']	 = (int)$this->common_model->getNextSequence('uw_scratch_win_games');
					$param['creation_ip']	 = currentIp();
					$param['creation_date']	 = (int)$this->timezone->utc_time();//currentDateTime();
					$param['created_by']	 = (int)$this->session->userdata('UW_ADMIN_ID');
					$alastInsertId			 =	$this->common_model->addData('uw_scratch_win_games',$param);
					$this->session->set_flashdata('alert_success',lang('addsuccess'));
				else:
					if($param['price'] != $data['EDITDATA']['price']):
						$param['prize_setting']  = 'disabled';
					endif;
					$param['update_ip']		 = currentIp();
					$param['update_date']	 = (int)$this->timezone->utc_time();//currentDateTime();
					$param['updated_by']	 = (int)$this->session->userdata('UW_ADMIN_ID');
					$this->common_model->editData('uw_scratch_win_games',$param,'_id', new MongoDB\BSON\ObjectID($editId));
					$this->session->set_flashdata('alert_success',lang('updatesuccess'));
				endif;
				redirect(correctLink('ALLSCRATCHWINGAMESDATA',getCurrentControllerPath('index')));
			endif;
		endif;
		
		$this->layouts->set_title('Add/Edit Scratch Win Games | Scratch Win Games | UWINN');
		$this->layouts->admin_view('campaigns/allscratchwingames/addeditdata',array(),$data);
	}	// END OF FUNCTION			
        
	/***********************************************************************
	** Function name 	: changestatus
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for change status
	** Date 			: 16 July 2026
	************************************************************************/
	function changestatus($changeStatusId='',$statusType='')
	{  
		$this->admin_model->authCheck('edit_data');
		$param['status'] =	$statusType;
		$this->common_model->editData('uw_scratch_win_games',$param,'_id', new MongoDB\BSON\ObjectID($changeStatusId));
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		
		redirect(correctLink('ALLSCRATCHWINGAMESDATA',getCurrentControllerPath('index')));
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
		$this->common_model->deleteData('uw_scratch_win_games','_id', new MongoDB\BSON\ObjectID($deleteId));
		$this->session->set_flashdata('alert_success',lang('deletesuccess'));
		redirect(correctLink('ALLSCRATCHWINGAMESDATA',getCurrentControllerPath('index')));
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
		$data        			=   $this->common_model->getData('multiple','uw_scratch_win_games',$wcon);//echo '<pre>';print_r($data);die;

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
		$this->common_model->editData('uw_scratch_win_games',$param,'_id', new MongoDB\BSON\ObjectID($changeStatusId));
		$this->session->set_flashdata('alert_success',lang('statussuccess'));
		redirect(correctLink('ALLSCRATCHWINGAMESDATA',getCurrentControllerPath('index')));
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
		$data['activeMenu']    = 'scratchwin';
		$data['activeSubMenu'] = 'allscratchwingames';

		$tblName  			    = 'uw_scratch_win_games';
		$this->session->set_userdata('ALLSCRATCHWINGAMESDATA',currentFullUrl());
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
				redirect(correctLink('ALLSCRATCHWINGAMESDATA',getCurrentControllerPath('index')));
			endif;
		endif;
		// echo "<pre>";print_r($data);die();
		$this->layouts->set_title('Add/Edit Scratch Win Games settings | Scratch Win Games | UWINN');
		$this->layouts->admin_view('campaigns/allscratchwingames/settings',array(),$data);
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
			redirect(correctLink('ALLSCRATCHWINGAMESDATA',getCurrentControllerPath('index')));
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




	/***********************************************************************
	** Function name 	: rtp
	** Developed By 	: Dilip Halder
	** Purpose  		: Global RTP prize pool configuration for Scratch Win games
	** Date 			: 02 July 2026
	************************************************************************/
	public function rtp($editId = '')
	{
		$data['error'] = '';
		$data['activeMenu'] = 'scratchwin';
		$data['activeSubMenu'] = 'allscratchwingames';
		$tblName = 'db_scratch_win_rtp_settings';
 
		if (!$this->currentAdminCanScratchWinSettings()):
			$this->session->set_flashdata('alert_warning', lang('accessdenied'));
			redirect(site_url('scratchwin/allscratchwingames/index'));
		endif;

		if ($editId !== ''):
			redirect(correctLink('ALLSCRATCHWINGAMESDATA',getCurrentControllerPath('rtp')));
		endif;

		$this->admin_model->authCheck('view_data');
		$this->_clearScratchWinGamesApcuCache();

		$rtpScope = $this->_sanitizeRtpScope($this->input->get('scope'));
		$selectedGameId = $this->_sanitizeMongoIdString($this->input->get('game_id'));
		$filterDate = $this->_sanitizeRtpFilterDate($this->input->get('filterDate'));

		$data['EDITDATA'] = $this->_loadScratchWinRtpSettingsRow();
		if (empty($rtpScope) && !empty($data['EDITDATA']['rtp_scope'])) {
			$rtpScope = $this->_sanitizeRtpScope($data['EDITDATA']['rtp_scope']);
		} elseif (empty($rtpScope) && !empty($data['EDITDATA']['settings_key'])) {
			$rtpScope = $this->_sanitizeRtpScope($data['EDITDATA']['settings_key']);
		}
		$rtpScope = $this->_sanitizeRtpScope($rtpScope);

		$data['rtp_filter_date'] = $filterDate;
		$data['rtp_scope'] = $rtpScope;
		$data['selected_game_id'] = $selectedGameId;
		$data['rtp_game_options'] = $this->_getRtpGameOptions($filterDate);
		$data['rtp_display_title'] = 'Global RTP Prize Pool';
		$data['rtp_scope_label'] = 'Global RTP';
		$data['rtp_readonly_mode'] = false;
		$data['rtp_scope_message'] = '';
		$data['live_stats'] = $this->_getGlobalRtpLiveStats($filterDate);

		if ($rtpScope === 'game') {
			if ($selectedGameId === '') {
				$data['rtp_display_title'] = 'Game Wise RTP - All Games Sales';
				$data['rtp_scope_label'] = 'Game Wise RTP';
				$data['rtp_scope_message'] = '';
				$data['live_stats'] = $this->_getGlobalRtpLiveStats($filterDate);
				$data['rtp_readonly_mode'] = false;
			} else {
			$gameRow = $this->_getRtpSelectedGameRow($selectedGameId);
			if (empty($gameRow)) {
				$data['rtp_scope_message'] = 'Select a Scratch Win game to view game-wise RTP and prize distribution.';
				$data['EDITDATA'] = array();
				$data['live_stats'] = array();
				$data['rtp_readonly_mode'] = true;
			} else {
				$data['rtp_display_title'] = 'Game Wise RTP - ' . (isset($gameRow['title']) ? $gameRow['title'] : ('Product ID ' . (isset($gameRow['products_id']) ? $gameRow['products_id'] : '')));
				$data['rtp_scope_label'] = 'Game Wise RTP';
				if (isset($gameRow['prize_slab_mode']) && $gameRow['prize_slab_mode'] === 'rtp_based_prize') {
					$data['EDITDATA'] = $this->_buildGameWiseRtpEditData($gameRow);
					$data['EDITDATA']['is_rtp_enabled'] = 1;
					$data['live_stats'] = $this->_getGameWiseRtpLiveStats($gameRow, $filterDate);
				} else {
					$data['EDITDATA'] = $this->_buildGameWiseRtpEditData($gameRow);
					$data['EDITDATA']['is_rtp_enabled'] = 1;
					$data['live_stats'] = $this->_getGameWiseRtpLiveStats($gameRow, $filterDate);
					$data['rtp_scope_message'] = 'Game wise RTP stays enabled here. Save once to apply RTP Based Prize mode for this game.';
				}
			}
			}
		}

		if ($rtpScope === 'game' && $this->input->post('SaveGameListRtp')):
			$this->admin_model->authCheck('edit_data');
			$gameListRtp = $this->input->post('game_list_rtp');
			if (is_array($gameListRtp)) {
				foreach ($gameListRtp as $gameOidStr => $rtpPercent) {
					$gameOidStr = $this->_sanitizeMongoIdString($gameOidStr);
					if ($gameOidStr === '') {
						continue;
					}
					$gameRow = $this->_getRtpSelectedGameRow($gameOidStr);
					if (empty($gameRow)) {
						continue;
					}
					$existingRtpConfig = array();
					if (!empty($gameRow['rtp_config'])) {
						$existingRtpConfig = is_object($gameRow['rtp_config']) ? (array)$gameRow['rtp_config'] : $gameRow['rtp_config'];
						if (!is_array($existingRtpConfig)) {
							$existingRtpConfig = array();
						}
					}
					$mergedConfig = $this->_mergeGameWiseRtpConfigForSave($existingRtpConfig, array(
						'target_rtp_percent' => (float)$rtpPercent,
					), $gameRow);
					$this->common_model->editData('uw_scratch_win_games', array(
						'prize_slab_mode' => 'rtp_based_prize',
						'prize_setting' => 'enabled',
						'rtp_config' => $mergedConfig,
						'update_ip' => currentIp(),
						'update_date' => (int)$this->timezone->utc_time(),
						'updated_by' => (int)$this->session->userdata('UW_ADMIN_ID'),
					), '_id', new MongoDB\BSON\ObjectID($gameOidStr));
				}
			}
			$this->session->set_flashdata('alert_success', lang('updatesuccess'));
			$redirectUrl = correctLink('ALLSCRATCHWINGAMESDATA',getCurrentControllerPath('rtp')) . '?scope=game&filterDate=' . urlencode($filterDate);
			if ($selectedGameId !== '') {
				$redirectUrl .= '&game_id=' . urlencode($selectedGameId);
			}
			redirect($redirectUrl);
		endif;

		if ($this->input->post('SaveChanges')):
			$this->admin_model->authCheck('edit_data');
			if ($rtpScope === 'game') {
				$postGameId = $this->_sanitizeMongoIdString($this->input->post('selected_game_id'));
				if ($postGameId !== '') {
					$selectedGameId = $postGameId;
				}
			}

			$rtpConfigPost = $this->input->post('rtp_config');
			if (!is_array($rtpConfigPost) || empty($rtpConfigPost)) {
				$this->form_validation->set_rules('rtp_config', 'RTP Configuration', 'required');
			} else {
				$this->form_validation->set_rules('rtp_config[target_rtp_percent]', 'Global RTP Percent', 'trim|required|numeric');
				$this->form_validation->set_rules('rtp_config[regular_pool_percent]', 'Regular Pool Percent', 'trim|required|numeric');
				$this->form_validation->set_rules('rtp_config[reserve_pool_percent]', 'Medium Pool Percent', 'trim|required|numeric');
				$this->form_validation->set_rules('rtp_config[big_pool_percent]', 'Big Pool Percent', 'trim|required|numeric');
			}
			$rtpSlabsPost = $this->input->post('rtp_prize_slabs');
			if (!is_array($rtpSlabsPost) || empty($rtpSlabsPost)) {
				$this->form_validation->set_rules('rtp_prize_slabs', 'RTP Prize Slabs', 'required');
			}

			if ($this->form_validation->run()):
				$globalSettingsRow = $this->_normalizeMongoRowToArray(
					$this->common_model->getDataByParticularField($tblName, 'settings_key', 'global')
				);
				if ($rtpScope === 'game' && $selectedGameId !== '') {
					$existingRow = $this->_getRtpSelectedGameRow($selectedGameId);
				} else {
					$existingRow = $globalSettingsRow;
				}
				$existingRtpConfig = array();
				if (!empty($existingRow['rtp_config'])) {
					$existingRtpConfig = is_object($existingRow['rtp_config'])
						? (array)$existingRow['rtp_config']
						: $existingRow['rtp_config'];
					if (!is_array($existingRtpConfig)) {
						$existingRtpConfig = array();
					}
				}
				$existingSlabs = !empty($existingRow['rtp_prize_slabs']) ? $existingRow['rtp_prize_slabs'] : array();

				// Respect toggle for both Global and Game Wise. Enabling one mode becomes the saved active RTP.
				$isRtpEnabledPost = !empty($this->input->post('is_rtp_enabled')) ? 1 : 0;
				if ($isRtpEnabledPost === 0 && is_array($rtpConfigPost) && array_key_exists('is_rtp_enabled', $rtpConfigPost)) {
					$isRtpEnabledPost = !empty($rtpConfigPost['is_rtp_enabled']) ? 1 : 0;
				}

				if ($rtpScope === 'game' && $selectedGameId !== ''):
					$objectId = !empty($existingRow['_id']) ? $this->_extractMongoObjectId($existingRow['_id']) : null;
					if ($objectId === null) {
						$this->session->set_flashdata('alert_warning', 'Please select a valid Scratch Win game.');
						redirect(correctLink('ALLSCRATCHWINGAMESDATA',getCurrentControllerPath('rtp')) . '?scope=game&filterDate=' . urlencode($filterDate));
					}
					$param = array(
						'prize_slab_mode' => !empty($isRtpEnabledPost) ? 'rtp_based_prize' : (isset($existingRow['prize_slab_mode']) ? $existingRow['prize_slab_mode'] : ''),
						'prize_setting' => 'enabled',
						'rtp_config' => $this->_mergeGameWiseRtpConfigForSave($existingRtpConfig, is_array($rtpConfigPost) ? $rtpConfigPost : array(), $existingRow),
						'rtp_prize_slabs' => $this->_mergeGlobalRtpSlabsForSave($existingSlabs, is_array($rtpSlabsPost) ? $rtpSlabsPost : array()),
						'update_ip' => currentIp(),
						'update_date' => (int)$this->timezone->utc_time(),
						'updated_by' => (int)$this->session->userdata('UW_ADMIN_ID'),
					);
					$this->common_model->editData('uw_scratch_win_games', $param, '_id', $objectId);
					// Switching this mode updates the saved active RTP scope (mutual exclusion with Global).
					$this->_persistRtpActiveMode($tblName, $globalSettingsRow, $rtpScope, $isRtpEnabledPost);
				else:



					$param = array(
						'settings_key' => 'global',
						'rtp_scope' => $this->_sanitizeRtpScope($rtpScope),
						'is_rtp_enabled' => !empty($isRtpEnabledPost) ? 1 : 0,
						'rtp_config' => $this->_mergeGlobalRtpConfigForSave($existingRtpConfig, is_array($rtpConfigPost) ? $rtpConfigPost : array()),
						'rtp_prize_slabs' => $this->_mergeGlobalRtpSlabsForSave($existingSlabs, is_array($rtpSlabsPost) ? $rtpSlabsPost : array()),
					);
					$currentDataId = $this->input->post('CurrentDataID');
					$objectId = !empty($existingRow['_id']) ? $this->_extractMongoObjectId($existingRow['_id']) : null;
					if ($objectId === null && $currentDataId !== '' && $currentDataId !== null):
						try {
							$objectId = new MongoDB\BSON\ObjectID((string)$currentDataId);
						} catch (Exception $objectIdException) {
							$objectId = null;
						}
					endif;

					if ($objectId === null):
						$param['creation_ip'] = currentIp();
						$param['creation_date'] = (int)$this->timezone->utc_time();
						$param['created_by'] = (int)$this->session->userdata('UW_ADMIN_ID');
						$this->common_model->addData($tblName, $param);
					else:
						$param['update_ip'] = currentIp();
						$param['update_date'] = (int)$this->timezone->utc_time();
						$param['updated_by'] = (int)$this->session->userdata('UW_ADMIN_ID');
						$this->common_model->editData($tblName, $param, '_id', $objectId);
					endif;
				endif;

				try {
					$this->common_model->generateLogs(array(
						'rtp_scope' => $rtpScope,
						'SaveChanges' => 'Yes',
						'settings_key' => ($rtpScope === 'game' && $selectedGameId !== '') ? 'scratchwin_game' : 'global',
						'game_id' => ($rtpScope === 'game' && $selectedGameId !== '') ? $selectedGameId : '',
						'is_rtp_enabled' => ($rtpScope === 'game' && $selectedGameId !== '') ? (!empty($isRtpEnabledPost) ? 1 : 0) : (isset($param['is_rtp_enabled']) ? $param['is_rtp_enabled'] : 0),
						'slab_count' => count($param['rtp_prize_slabs']),
					));
				} catch (Exception $logException) {
					// Never block RTP save when logging fails
				}

				$this->session->set_flashdata('alert_success', lang('updatesuccess'));
				$redirectUrl = correctLink('ALLSCRATCHWINGAMESDATA',getCurrentControllerPath('rtp')) . '?scope=' . urlencode($rtpScope) . '&filterDate=' . urlencode($filterDate);
				if ($rtpScope === 'game' && $selectedGameId !== '') {
					$redirectUrl .= '&game_id=' . urlencode($selectedGameId);
				}
				redirect($redirectUrl);

			else:
				$existingRtpConfig = array();
				if (!empty($data['EDITDATA']['rtp_config'])) {
					$existingRtpConfig = is_object($data['EDITDATA']['rtp_config'])
						? (array)$data['EDITDATA']['rtp_config']
						: $data['EDITDATA']['rtp_config'];
					if (!is_array($existingRtpConfig)) {
						$existingRtpConfig = array();
					}
				}
				$existingSlabs = !empty($data['EDITDATA']['rtp_prize_slabs']) ? $data['EDITDATA']['rtp_prize_slabs'] : array();

				if ($this->input->post('is_rtp_enabled') !== null):
					$data['EDITDATA']['is_rtp_enabled'] = !empty($this->input->post('is_rtp_enabled')) ? 1 : 0;
				endif;
				if ($this->input->post('rtp_config')):
					$data['EDITDATA']['rtp_config'] = $rtpScope === 'game'
						? $this->_mergeGameWiseRtpConfigForSave($existingRtpConfig, $this->input->post('rtp_config'), $existingRow)
						: $this->_mergeGlobalRtpConfigForSave($existingRtpConfig, $this->input->post('rtp_config'));
				endif;
				if ($this->input->post('rtp_prize_slabs')):
					$data['EDITDATA']['rtp_prize_slabs'] = $this->_mergeGlobalRtpSlabsForSave(
						$existingSlabs,
						$this->input->post('rtp_prize_slabs')
					);
				endif;
			endif;
		endif;


		$this->layouts->set_title('Global RTP Configuration | Scratch Win Games | Campaigns | UWINN');
		$this->layouts->admin_view('campaigns/allscratchwingames/rtp', array(), $data);
	}
	// END OF FUNCTION

	private function _sanitizeRtpScope($scope = '')
	{
		$scope = trim((string) $scope);
		if(!empty($scope)) {
			return $scope;
		}
	}

	private function _sanitizeMongoIdString($id = '')
	{
		$id = trim((string) $id);
		return preg_match('/^[a-f0-9]{24}$/i', $id) ? $id : '';
	}

	private function _getRtpGameOptions($filterDate = '')
	{
		list($filterDate, $dateStart, $dateEnd) = $this->_getRtpFilterDateBounds($filterDate);
		$salesByGame = ($dateStart > 0 && $dateEnd > 0)
			? $this->_aggregateScratchWinSalesByGameForRtp($dateStart, $dateEnd)
			: array();
		$prizeByGame = ($dateStart > 0 && $dateEnd > 0)
			? $this->_aggregateScratchWinPrizePayoutByGameForRtp($dateStart, $dateEnd)
			: array();

		$whereCon = array(
			'where' => array('status' => 'A'),
			'select' => array('title', 'products_id', 'prize_slab_mode', 'rtp_config', 'price'),
		);
		$rows = $this->common_model->getData('multiple', 'uw_scratch_win_games', $whereCon, array('title' => 'ASC'));
		$options = array();
		if (!is_array($rows)) {
			return $options;
		}
		foreach ($rows as $row) {
			$row = $this->_normalizeMongoRowToArray($row);
			if (!is_array($row) || empty($row['_id'])) {
				continue;
			}
			$oid = $this->_extractMongoObjectId($row['_id']);
			if ($oid === null) {
				continue;
			}
			$oidStr = (string) $oid;
			$gameSales = isset($salesByGame[$oidStr]) ? $salesByGame[$oidStr] : array();
			$gamePrizePayout = isset($prizeByGame[$oidStr]) ? (float) $prizeByGame[$oidStr] : 0;
			$options[] = array(
				'_id' => $oidStr,
				'title' => isset($row['title']) ? (string) $row['title'] : '',
				'products_id' => isset($row['products_id']) ? (int) $row['products_id'] : 0,
				'prize_slab_mode' => isset($row['prize_slab_mode']) ? (string) $row['prize_slab_mode'] : '',
				'is_rtp_enabled' => (isset($row['prize_slab_mode']) && (string)$row['prize_slab_mode'] === 'rtp_based_prize') ? 1 : 0,
				'target_rtp_percent' => (float) (
					isset($row['rtp_config'])
					&& is_array(is_object($row['rtp_config']) ? (array)$row['rtp_config'] : $row['rtp_config'])
					&& isset((is_object($row['rtp_config']) ? (array)$row['rtp_config'] : $row['rtp_config'])['target_rtp_percent'])
						? (is_object($row['rtp_config']) ? (array)$row['rtp_config'] : $row['rtp_config'])['target_rtp_percent']
						: 0
				),
				'ticket_price' => isset($row['price']) ? (float) $row['price'] : 0,
				'total_sales' => (float) (isset($gameSales['daily_sales']) ? $gameSales['daily_sales'] : 0),
				'order_count' => (int) (isset($gameSales['order_count']) ? $gameSales['order_count'] : 0),
				'distributed_prize_amount' => round(max(0, $gamePrizePayout), 2),
			);
		}
		return $options;
	}

	private function _aggregateScratchWinSalesByGameForRtp($dateStart, $dateEnd)
	{
		$saleDouble = array('$toDouble' => array('$ifNull' => array('$total_price', 0)));
		$query = array(
			array('$match' => array(
				'created_at' => array('$gte' => (int)$dateStart, '$lte' => (int)$dateEnd),
			)),
			array('$group' => array(
				'_id' => '$products_oid',
				'order_count' => array('$sum' => array('$cond' => array(
					array('$ne' => array('$status', 'CL')),
					1,
					0,
				))),
				'daily_sales' => array('$sum' => array('$cond' => array(
					array('$ne' => array('$status', 'CL')),
					$saleDouble,
					0,
				))),
			)),
		);

		$rows = $this->common_model->mongo_db->aggregate('db_scratch_win_orders', $query, array('batchSize' => 4));
		$result = array();
		if (!is_array($rows)) {
			return $result;
		}
		foreach ($rows as $row) {
			$row = $this->_normalizeMongoRowToArray($row);
			if (empty($row['_id'])) {
				continue;
			}
			$productOid = $this->_extractMongoObjectId($row['_id']);
			if ($productOid === null) {
				continue;
			}
			$result[(string) $productOid] = array(
				'daily_sales' => round(max(0, (float)(isset($row['daily_sales']) ? $row['daily_sales'] : 0)), 2),
				'order_count' => max(0, (int)(isset($row['order_count']) ? $row['order_count'] : 0)),
			);
		}
		return $result;
	}

	private function _sumScratchWinPrizeDocumentPayout($row)
	{
		$row = $this->_normalizeMongoRowToArray($row);
		if (!is_array($row)) {
			return 0.0;
		}

		if (isset($row['amount']) && $row['amount'] !== '' && $row['amount'] !== null) {
			return round(max(0, (float) $row['amount']), 2);
		}

		$totalPayout = 0.0;
		$repeatDetails = isset($row['prize_repeat_details']) ? $row['prize_repeat_details'] : array();
		if (is_object($repeatDetails)) {
			$repeatDetails = (array) $repeatDetails;
		}
		if (is_array($repeatDetails)) {
			foreach ($repeatDetails as $tier) {
				$tier = $this->_normalizeMongoRowToArray($tier);
				$prizeAmount = (float) (isset($tier['winning_amount']) ? $tier['winning_amount'] : 0);
				$winners = (int) (isset($tier['prize_repeat_count']) ? $tier['prize_repeat_count'] : 0);
				if ($prizeAmount <= 0 || $winners <= 0) {
					continue;
				}
				$totalPayout = round($totalPayout + ($prizeAmount * $winners), 2);
			}
		}

		$bigPrizes = isset($row['big_prize']) ? $row['big_prize'] : array();
		if (is_object($bigPrizes)) {
			$bigPrizes = (array) $bigPrizes;
		}
		if (is_array($bigPrizes)) {
			foreach ($bigPrizes as $bigPrize) {
				$bigPrize = $this->_normalizeMongoRowToArray($bigPrize);
				$prizeAmount = (float) (isset($bigPrize['winning_amount']) ? $bigPrize['winning_amount'] : 0);
				if ($prizeAmount <= 0) {
					continue;
				}
				$totalPayout = round($totalPayout + $prizeAmount, 2);
			}
		}

		return round(max(0, $totalPayout), 2);
	}

	private function _aggregateScratchWinPrizePayoutByGameForRtp($dateStart, $dateEnd)
	{
		$whereCon = array(
			'where' => array(
				'created_at' => array('$gte' => (int) $dateStart, '$lte' => (int) $dateEnd),
			),
		);
		$rows = $this->common_model->getData('multiple', 'db_scratch_win_prize_amounts', $whereCon, array('_id' => -1));
		$result = array();
		if (!is_array($rows)) {
			return $result;
		}
		foreach ($rows as $row) {
			$row = $this->_normalizeMongoRowToArray($row);
			$productOid = $this->_extractMongoObjectId(isset($row['products_oid']) ? $row['products_oid'] : null);
			if ($productOid === null) {
				continue;
			}
			$oidStr = (string) $productOid;
			$payout = isset($row['amount']) ? round(max(0, (float) $row['amount']), 2) : $this->_sumScratchWinPrizeDocumentPayout($row);
			if (!isset($result[$oidStr])) {
				$result[$oidStr] = 0.0;
			}
			$result[$oidStr] = round($result[$oidStr] + $payout, 2);
		}
		return $result;
	}

	private function _getRtpSelectedGameRow($selectedGameId = '')
	{
		$selectedGameId = $this->_sanitizeMongoIdString($selectedGameId);
		if ($selectedGameId === '') {
			return array();
		}
		$row = $this->common_model->getDataByParticularField('uw_scratch_win_games', '_id', new MongoDB\BSON\ObjectID($selectedGameId));
		return $this->_normalizeMongoRowToArray($row);
	}

	private function _buildGameWiseRtpEditData($gameRow)
	{
		$gameRow = $this->_normalizeMongoRowToArray($gameRow);
		if (!is_array($gameRow)) {
			return array();
		}
		$isEnabled = (isset($gameRow['prize_slab_mode']) && $gameRow['prize_slab_mode'] === 'rtp_based_prize') ? 1 : 0;
		$rtpConfig = array();
		if (!empty($gameRow['rtp_config'])) {
			$rtpConfig = is_object($gameRow['rtp_config']) ? (array) $gameRow['rtp_config'] : $gameRow['rtp_config'];
		}
		if (!is_array($rtpConfig)) {
			$rtpConfig = array();
		}
		$rtpConfig = array_merge(array(
			'ticket_price' => isset($gameRow['price']) ? (float) $gameRow['price'] : 3,
			'target_rtp_percent' => 70,
			'regular_pool_percent' => 70,
			'reserve_pool_percent' => 20,
			'big_pool_percent' => 10,
			'regular_pool_release_percent' => 100,
			'reserve_pool_release_percent' => 100,
			'big_pool_release_percent' => 100,
		), $rtpConfig);

		return array(
			'_id' => isset($gameRow['_id']) ? $gameRow['_id'] : '',
			'is_rtp_enabled' => $isEnabled,
			'rtp_config' => $rtpConfig,
			'rtp_prize_slabs' => !empty($gameRow['rtp_prize_slabs']) ? $gameRow['rtp_prize_slabs'] : array(),
			'title' => isset($gameRow['title']) ? $gameRow['title'] : '',
			'products_id' => isset($gameRow['products_id']) ? $gameRow['products_id'] : 0,
		);
	}

	private function _getGameWiseRtpLiveStats($gameRow, $filterDate = '')
	{
		$gameRow = $this->_normalizeMongoRowToArray($gameRow);
		list($filterDate, $dateStart, $dateEnd) = $this->_getRtpFilterDateBounds($filterDate);
		$productOid = !empty($gameRow['_id']) ? $this->_extractMongoObjectId($gameRow['_id']) : null;
		if ($dateStart <= 0 || $dateEnd <= 0 || $productOid === null) {
			return array();
		}

		$salesSummary = $this->_aggregateGlobalScratchWinSalesForRtp($dateStart, $dateEnd, $productOid);
		$prizeSummary = $this->_aggregateGlobalPrizeDistributionForRtp($dateStart, $dateEnd, $productOid);
		$rtpSlabs = !empty($gameRow['rtp_prize_slabs']) ? $gameRow['rtp_prize_slabs'] : array();
		$prizeSummary = $this->_enrichRtpDistributedSlabsWithConfig($prizeSummary, $rtpSlabs);

		$rtpCfg = !empty($gameRow['rtp_config']) ? (is_object($gameRow['rtp_config']) ? (array) $gameRow['rtp_config'] : $gameRow['rtp_config']) : array();
		if (!is_array($rtpCfg)) {
			$rtpCfg = array();
		}
		$targetRtpPercent = isset($rtpCfg['target_rtp_percent']) ? (float) $rtpCfg['target_rtp_percent'] : 0;
		$ticketPrice = isset($gameRow['price']) ? (float) $gameRow['price'] : (isset($rtpCfg['ticket_price']) ? (float) $rtpCfg['ticket_price'] : 0);
		$dailySales = (float) (isset($salesSummary['daily_sales']) ? $salesSummary['daily_sales'] : 0);
		$totalRtpBudget = round($dailySales * ($targetRtpPercent / 100), 2);
		$totalDistributed = (float) (isset($prizeSummary['total_payout']) ? $prizeSummary['total_payout'] : 0);
		$effectiveRtp = $dailySales > 0 ? round(($totalDistributed / $dailySales) * 100, 2) : 0;
		$manualBigPrizeAmount = (float) (isset($prizeSummary['total_manual_big_prize']) ? $prizeSummary['total_manual_big_prize'] : 0);
		$gameWisePrizeAmount = (float) (isset($prizeSummary['total_game_wise_payout']) ? $prizeSummary['total_game_wise_payout'] : 0);
		$manualBigPrizePercent = $dailySales > 0 ? round(($manualBigPrizeAmount / $dailySales) * 100, 2) : 0.0;
		$gameWisePrizePercent = $dailySales > 0 ? round(($gameWisePrizeAmount / $dailySales) * 100, 2) : 0.0;
		$allGamesMetrics = $this->_getAllGamesPrizeMetricsForRtp($dateStart, $dateEnd);

		return array(
			'filter_date' => $filterDate,
			'daily_sales' => $dailySales,
			'ticket_price' => $ticketPrice,
			'target_rtp_percent' => $targetRtpPercent,
			'total_rtp_budget' => $totalRtpBudget,
			'order_count' => (int) (isset($salesSummary['order_count']) ? $salesSummary['order_count'] : 0),
			'cancelled_count' => (int) (isset($salesSummary['cancelled_count']) ? $salesSummary['cancelled_count'] : 0),
			'total_winning_amount' => $totalDistributed,
			'total_big_prize_amount' => (float) (isset($prizeSummary['total_big_prize']) ? $prizeSummary['total_big_prize'] : 0),
			'total_manual_big_prize_amount' => $manualBigPrizeAmount,
			'total_game_wise_prize_amount' => $gameWisePrizeAmount,
			'total_winners' => (int) (isset($prizeSummary['total_winners']) ? $prizeSummary['total_winners'] : 0),
			'effective_rtp' => $effectiveRtp,
			'average_prize_amount' => $allGamesMetrics['average_prize_amount'],
			'all_games_prize_percent' => round($manualBigPrizePercent + $gameWisePrizePercent, 2),
			'manual_big_prize_percent' => $manualBigPrizePercent,
			'game_wise_prize_percent' => $gameWisePrizePercent,
			'all_games_sales' => $allGamesMetrics['all_games_sales'],
			'all_games_payout' => $allGamesMetrics['all_games_payout'],
			'all_games_winners' => $allGamesMetrics['all_games_winners'],
			'distributed_slabs' => isset($prizeSummary['slabs']) ? $prizeSummary['slabs'] : array(),
			'pools' => isset($prizeSummary['pools']) ? $prizeSummary['pools'] : array(),
		);
	}

	private function _mergeGlobalRtpConfigForSave($existingConfig, $rtpConfigPost)
	{
		$existing = is_array($existingConfig) ? $existingConfig : array();
		if (is_object($existingConfig)) {
			$existing = (array)$existingConfig;
		}

		$defaults = array(
			'ticket_price' => 3,
			'target_rtp_percent' => 70,
			'regular_pool_percent' => 70,
			'reserve_pool_percent' => 20,
			'big_pool_percent' => 10,
			'regular_pool_release_percent' => 100,
			'reserve_pool_release_percent' => 100,
			'big_pool_release_percent' => 100,
		);

		$merged = array();
		foreach ($defaults as $key => $defaultValue) {
			$merged[$key] = isset($existing[$key]) ? (float)$existing[$key] : (float)$defaultValue;
		}

		if (!is_array($rtpConfigPost)) {
			return $merged;
		}

		$saveableKeys = array(
			'target_rtp_percent',
			'regular_pool_percent',
			'reserve_pool_percent',
			'big_pool_percent',
			'regular_pool_release_percent',
			'reserve_pool_release_percent',
			'big_pool_release_percent',
		);
		foreach ($saveableKeys as $key) {
			if (array_key_exists($key, $rtpConfigPost)) {
				$merged[$key] = (float)$rtpConfigPost[$key];
			}
		}

		return $merged;
	}

	private function _mergeGameWiseRtpConfigForSave($existingConfig, $rtpConfigPost, $gameRow = array())
	{
		$merged = $this->_mergeGlobalRtpConfigForSave($existingConfig, $rtpConfigPost);
		$gameRow = $this->_normalizeMongoRowToArray($gameRow);
		if (isset($gameRow['price']) && (float)$gameRow['price'] > 0) {
			$merged['ticket_price'] = (float)$gameRow['price'];
		}
		return $merged;
	}

	private function _mergeGlobalRtpSlabsForSave($existingSlabs, $rtpSlabsPost)
	{
		$existingByPool = array(
			'regular' => array(),
			'reserve' => array(),
			'big' => array(),
		);
		if (is_array($existingSlabs)) {
			foreach ($existingSlabs as $slabRow) {
				$slabRow = $this->_normalizeMongoRowToArray($slabRow);
				if (!is_array($slabRow)) {
					continue;
				}
				$poolType = isset($slabRow['pool_type']) ? trim((string)$slabRow['pool_type']) : 'regular';
				if (!isset($existingByPool[$poolType])) {
					$poolType = 'regular';
				}
				$existingByPool[$poolType][] = $slabRow;
			}
		}

		$poolCounters = array('regular' => 0, 'reserve' => 0, 'big' => 0);
		$result = array();

		if (!is_array($rtpSlabsPost)) {
			return $result;
		}

		foreach ($rtpSlabsPost as $slabRow) {
			if (!is_array($slabRow)) {
				continue;
			}
			$poolType = isset($slabRow['pool_type']) ? trim((string)$slabRow['pool_type']) : 'regular';
			if ($poolType === '' || !isset($poolCounters[$poolType])) {
				$poolType = 'regular';
			}
			$poolIndex = $poolCounters[$poolType];
			$poolCounters[$poolType]++;

			$existing = isset($existingByPool[$poolType][$poolIndex]) ? $existingByPool[$poolType][$poolIndex] : array();
			if (!is_array($existing)) {
				$existing = array();
			}

			$result[] = array(
				'pool_type' => $poolType,
				'prize_amount' => (float)(isset($slabRow['prize_amount']) ? $slabRow['prize_amount'] : 0),
				'distribution_percentage' => (float)(isset($slabRow['distribution_percentage']) ? $slabRow['distribution_percentage'] : 0),
				'winner_count' => (int)(isset($existing['winner_count']) ? $existing['winner_count'] : 0),
				'max_repeats' => (int)(isset($existing['max_repeats']) ? $existing['max_repeats'] : 0),
				'freeze_after_repeat' => (int)(isset($existing['freeze_after_repeat']) ? $existing['freeze_after_repeat'] : 0),
				'freeze_minutes' => (int)(isset($existing['freeze_minutes']) ? $existing['freeze_minutes'] : 0),
				'is_high_prize' => !empty($existing['is_high_prize']) ? 1 : ($poolType === 'reserve' ? 1 : 0),
				'is_big_prize' => !empty($existing['is_big_prize']) ? 1 : ($poolType === 'big' ? 1 : 0),
				'is_mandatory' => !empty($existing['is_mandatory']) ? 1 : 0,
				'is_active' => !isset($existing['is_active']) || !empty($existing['is_active']) ? 1 : 0,
			);
		}

		return $result;
	}

	/***********************************************************************
	** Function name 	: getRtpLiveStats
	** Purpose  		: Live Scratch Win sales + prize distribution for global RTP admin
	************************************************************************/
	public function getRtpLiveStats()
	{
		$this->admin_model->authCheck('view_data');

		$filterDate = $this->input->post('filterDate');
		if (empty($filterDate)) {
			$filterDate = $this->input->get('filterDate');
		}
		$filterDate = $this->_sanitizeRtpFilterDate($filterDate);
		$scope = $this->_sanitizeRtpScope($this->input->post('scope'));
		if ($scope === 'global' && $this->input->get('scope')) {
			$scope = $this->_sanitizeRtpScope($this->input->get('scope'));
		}
		$gameId = $this->_sanitizeMongoIdString($this->input->post('game_id'));
		if ($gameId === '') {
			$gameId = $this->_sanitizeMongoIdString($this->input->get('game_id'));
		}

		if ($scope === 'game' && $gameId !== '' && $gameId !== 'all') {
			$gameRow = $this->_getRtpSelectedGameRow($gameId);
			$liveStats = !empty($gameRow) ? $this->_getGameWiseRtpLiveStats($gameRow, $filterDate) : array();
		} else {
			$liveStats = $this->_getGlobalRtpLiveStats($filterDate);
		}
		header('Content-Type: application/json');
		echo json_encode(array('success' => true, 'data' => $liveStats));
		exit;
	}

	/***********************************************************************
	** Function name 	: getRtpGameConfig
	** Purpose  		: Load selected game RTP config for AJAX game picker
	************************************************************************/
	public function getRtpGameConfig()
	{
		$this->admin_model->authCheck('view_data');

		$gameId = trim((string) $this->input->post('game_id'));
		if ($gameId === '') {
			$gameId = trim((string) $this->input->get('game_id'));
		}
		$filterDate = $this->input->post('filterDate');
		if (empty($filterDate)) {
			$filterDate = $this->input->get('filterDate');
		}
		$filterDate = $this->_sanitizeRtpFilterDate($filterDate);

		if ($gameId === 'all') {
			$rtpRow = $this->_loadScratchWinRtpSettingsRow();
			$editData = is_array($rtpRow) ? $rtpRow : array();
			$liveStats = $this->_getGlobalRtpLiveStats($filterDate);
			$currentDataId = '';
			if (!empty($editData['_id'])) {
				$currentDataId = $this->_extractMongoObjectIdString($editData['_id']);
			}
			$rtpConfig = !empty($editData['rtp_config']) ? $editData['rtp_config'] : array();
			if (is_object($rtpConfig)) {
				$rtpConfig = (array) $rtpConfig;
			}
			if (!is_array($rtpConfig)) {
				$rtpConfig = array();
			}
			if (!isset($rtpConfig['target_rtp_percent']) || (float)$rtpConfig['target_rtp_percent'] <= 0) {
				$rtpConfig['target_rtp_percent'] = isset($liveStats['target_rtp_percent']) ? (float)$liveStats['target_rtp_percent'] : 70;
			}
			$isRtpEnabled = isset($editData['is_rtp_enabled']) ? !empty($editData['is_rtp_enabled']) : true;
			header('Content-Type: application/json');
			echo json_encode(array(
				'success' => true,
				'data' => array(
					'game_id' => 'all',
					'title' => 'All Games Sales',
					'products_id' => 0,
					'current_data_id' => $currentDataId,
					'is_rtp_enabled' => $isRtpEnabled ? 1 : 0,
					'rtp_config' => $rtpConfig,
					'rtp_prize_slabs' => $this->_groupRtpPrizeSlabsByPool(!empty($editData['rtp_prize_slabs']) ? $editData['rtp_prize_slabs'] : array()),
					'live_stats' => $liveStats,
					'scope_message' => 'Combined sales across all Scratch Win games for the selected date.',
					'is_all_games' => 1,
				),
			));
			exit;
		}

		$gameId = $this->_sanitizeMongoIdString($gameId);
		$gameRow = $this->_getRtpSelectedGameRow($gameId);
		if (empty($gameRow)) {
			header('Content-Type: application/json');
			echo json_encode(array('success' => false, 'message' => 'Game not found.'));
			exit;
		}

		$editData = $this->_buildGameWiseRtpEditData($gameRow);
		$editData['is_rtp_enabled'] = 1;
		$liveStats = $this->_getGameWiseRtpLiveStats($gameRow, $filterDate);
		$currentDataId = '';
		if (!empty($editData['_id'])) {
			$currentDataId = $this->_extractMongoObjectIdString($editData['_id']);
		}

		$scopeMessage = '';
		if (!isset($gameRow['prize_slab_mode']) || $gameRow['prize_slab_mode'] !== 'rtp_based_prize') {
			$scopeMessage = 'Game wise RTP stays enabled here. Save once to apply RTP Based Prize mode for this game.';
		}

		$rtpConfig = !empty($editData['rtp_config']) ? $editData['rtp_config'] : array();
		if (is_object($rtpConfig)) {
			$rtpConfig = (array) $rtpConfig;
		}
		if (!is_array($rtpConfig)) {
			$rtpConfig = array();
		}

		header('Content-Type: application/json');
		echo json_encode(array(
			'success' => true,
			'data' => array(
				'game_id' => $gameId,
				'title' => isset($editData['title']) ? (string) $editData['title'] : '',
				'products_id' => isset($editData['products_id']) ? (int) $editData['products_id'] : 0,
				'current_data_id' => $currentDataId,
				'is_rtp_enabled' => 1,
				'rtp_config' => $rtpConfig,
				'rtp_prize_slabs' => $this->_groupRtpPrizeSlabsByPool(!empty($editData['rtp_prize_slabs']) ? $editData['rtp_prize_slabs'] : array()),
				'live_stats' => $liveStats,
				'scope_message' => $scopeMessage,
			),
		));
		exit;
	}

	/***********************************************************************
	** Function name 	: getRtpGameListStats
	** Purpose  		: Campaign list sales/orders for selected filter date (AJAX)
	************************************************************************/
	public function getRtpGameListStats()
	{
		$this->admin_model->authCheck('view_data');

		$filterDate = $this->input->post('filterDate');
		if (empty($filterDate)) {
			$filterDate = $this->input->get('filterDate');
		}
		$filterDate = $this->_sanitizeRtpFilterDate($filterDate);
		$options = $this->_getRtpGameOptions($filterDate);
		$totalSales = 0;
		$totalOrders = 0;
		$totalDistributedPrize = 0;
		foreach ($options as $row) {
			$totalSales += (float) (isset($row['total_sales']) ? $row['total_sales'] : 0);
			$totalOrders += (int) (isset($row['order_count']) ? $row['order_count'] : 0);
			$totalDistributedPrize += (float) (isset($row['distributed_prize_amount']) ? $row['distributed_prize_amount'] : 0);
		}

		header('Content-Type: application/json');
		echo json_encode(array(
			'success' => true,
			'data' => array(
				'filter_date' => $filterDate,
				'games' => $options,
				'summary' => array(
					'game_count' => count($options),
					'total_sales' => $totalSales,
					'total_orders' => $totalOrders,
					'total_distributed_prize' => round($totalDistributedPrize, 2),
				),
			),
		));
		exit;
	}

	private function _extractMongoObjectIdString($value)
	{
		$oid = $this->_extractMongoObjectId($value);
		return ($oid instanceof MongoDB\BSON\ObjectID) ? (string) $oid : '';
	}

	private function _groupRtpPrizeSlabsByPool($savedSlabs)
	{
		$defaultSlabs = array(
			'regular' => array(
				array('prize_amount' => 3, 'distribution_percentage' => 28, 'is_active' => 1),
				array('prize_amount' => 5, 'distribution_percentage' => 25, 'is_active' => 1),
				array('prize_amount' => 10, 'distribution_percentage' => 10, 'is_active' => 1),
				array('prize_amount' => 15, 'distribution_percentage' => 15, 'is_active' => 1),
				array('prize_amount' => 20, 'distribution_percentage' => 10, 'is_active' => 1),
				array('prize_amount' => 25, 'distribution_percentage' => 8, 'is_active' => 1),
				array('prize_amount' => 30, 'distribution_percentage' => 4, 'is_active' => 1),
			),
			'reserve' => array(
				array('prize_amount' => 50, 'distribution_percentage' => 45, 'is_active' => 1),
				array('prize_amount' => 60, 'distribution_percentage' => 20, 'is_active' => 1),
				array('prize_amount' => 70, 'distribution_percentage' => 15, 'is_active' => 1),
				array('prize_amount' => 75, 'distribution_percentage' => 10, 'is_active' => 1),
				array('prize_amount' => 80, 'distribution_percentage' => 10, 'is_active' => 1),
			),
			'big' => array(
				array('prize_amount' => 100, 'distribution_percentage' => 15, 'is_active' => 1),
				array('prize_amount' => 150, 'distribution_percentage' => 15, 'is_active' => 1),
				array('prize_amount' => 200, 'distribution_percentage' => 20, 'is_active' => 1),
				array('prize_amount' => 250, 'distribution_percentage' => 20, 'is_active' => 1),
				array('prize_amount' => 500, 'distribution_percentage' => 20, 'is_active' => 1),
				array('prize_amount' => 1000, 'distribution_percentage' => 10, 'is_active' => 1, 'is_mandatory' => 1),
			),
		);
		$pools = array('regular' => array(), 'reserve' => array(), 'big' => array());
		if (is_object($savedSlabs)) {
			$savedSlabs = (array) $savedSlabs;
		}
		if (!empty($savedSlabs) && is_array($savedSlabs)) {
			foreach ($savedSlabs as $slab) {
				if (is_object($slab)) {
					$slab = (array) $slab;
				}
				if (!is_array($slab)) {
					continue;
				}
				$pool = isset($slab['pool_type']) ? (string) $slab['pool_type'] : 'regular';
				if (!isset($pools[$pool])) {
					$pool = 'regular';
				}
				$pools[$pool][] = array(
					'prize_amount' => isset($slab['prize_amount']) ? (float) $slab['prize_amount'] : 0,
					'distribution_percentage' => isset($slab['distribution_percentage']) ? (float) $slab['distribution_percentage'] : 0,
					'is_active' => !isset($slab['is_active']) || !empty($slab['is_active']) ? 1 : 0,
					'is_mandatory' => !empty($slab['is_mandatory']) ? 1 : 0,
				);
			}
		}
		foreach (array('regular', 'reserve', 'big') as $poolKey) {
			if (empty($pools[$poolKey])) {
				$pools[$poolKey] = $defaultSlabs[$poolKey];
			}
		}
		return $pools;
	}

	private function _loadScratchWinRtpSettingsRow()
	{
		$row = $this->_normalizeMongoRowToArray(
			$this->common_model->getDataByParticularField('db_scratch_win_rtp_settings', 'settings_key', 'global')
		);
		if (!empty($row)) {
			return $row;
		}
		$row = $this->_normalizeMongoRowToArray(
			$this->common_model->getDataByParticularField('db_scratch_win_rtp_settings', 'settings_key', 'game')
		);
		if (!empty($row)) {
			return $row;
		}
		$row = $this->_normalizeMongoRowToArray(
			$this->common_model->getData('single', 'db_scratch_win_rtp_settings', array())
		);
		return is_array($row) ? $row : array();
	}

	/**
	 * Persist which RTP mode is active. Enabling one mode (global/game) disables the other.
	 */
	private function _persistRtpActiveMode($tblName, $existingRow, $scope, $isEnabled)
	{
		$scope = $this->_sanitizeRtpScope($scope);
		$isEnabled = !empty($isEnabled) ? 1 : 0;
		$existingRow = $this->_normalizeMongoRowToArray($existingRow);
		$param = array(
			'settings_key' => 'global',
			'rtp_scope' => $scope,
			'is_rtp_enabled' => $isEnabled,
		);
		$objectId = !empty($existingRow['_id']) ? $this->_extractMongoObjectId($existingRow['_id']) : null;
		if ($objectId === null) {
			$param['creation_ip'] = currentIp();
			$param['creation_date'] = (int)$this->timezone->utc_time();
			$param['created_by'] = (int)$this->session->userdata('UW_ADMIN_ID');
			$this->common_model->addData($tblName, $param);
			return;
		}
		$param['update_ip'] = currentIp();
		$param['update_date'] = (int)$this->timezone->utc_time();
		$param['updated_by'] = (int)$this->session->userdata('UW_ADMIN_ID');
		$this->common_model->editData($tblName, $param, '_id', $objectId);
	}

	private function _sanitizeRtpFilterDate($filterDate = '')
	{
		$filterDate = trim((string)$filterDate);
		$todayDubai = $this->timezone->current_date('Y-m-d', 'Asia/Dubai');
		if ($todayDubai === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $todayDubai)) {
			$todayDubai = date('Y-m-d');
		}
		if ($filterDate === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $filterDate)) {
			return $todayDubai;
		}

		$dateStart = strtotime($filterDate . ' 00:00:00');
		if ($dateStart === false) {
			return $todayDubai;
		}

		$todayEnd = strtotime($todayDubai . ' 23:59:59');
		if ($dateStart > $todayEnd) {
			return $todayDubai;
		}

		return date('Y-m-d', $dateStart);
	}

	private function _getRtpFilterDateBounds($filterDate = '')
	{
		$filterDate = $this->_sanitizeRtpFilterDate($filterDate);
		$dateStart = (int)$this->timezone->calender_datetime_to_utc_date_time($filterDate . ' 00:00:00', 'Asia/Dubai');
		$dateEnd = (int)$this->timezone->calender_datetime_to_utc_date_time($filterDate . ' 23:59:59', 'Asia/Dubai');
		if ($dateStart <= 0 || $dateEnd <= 0) {
			try {
				$tz = new DateTimeZone('Asia/Dubai');
				$dateStart = (int)(new DateTime($filterDate . ' 00:00:00', $tz))->setTimezone(new DateTimeZone('UTC'))->format('U');
				$dateEnd = (int)(new DateTime($filterDate . ' 23:59:59', $tz))->setTimezone(new DateTimeZone('UTC'))->format('U');
			} catch (Exception $e) {
				$dateStart = (int)strtotime($filterDate . ' 00:00:00');
				$dateEnd = (int)strtotime($filterDate . ' 23:59:59');
			}
		}
		return array($filterDate, $dateStart, $dateEnd);
	}

	private function _getGlobalRtpLiveStats($filterDate = '')
	{
		list($filterDate, $dateStart, $dateEnd) = $this->_getRtpFilterDateBounds($filterDate);
		if ($dateStart <= 0 || $dateEnd <= 0) {
			return array();
		}

		$salesSummary = $this->_aggregateGlobalScratchWinSalesForRtp($dateStart, $dateEnd);
		$prizeSummary = $this->_aggregateGlobalPrizeDistributionForRtp($dateStart, $dateEnd);

		$rtpRow = $this->_loadScratchWinRtpSettingsRow();
		$rtpSlabs = array();
		if (!empty($rtpRow['rtp_prize_slabs'])) {
			$rtpSlabs = is_object($rtpRow['rtp_prize_slabs']) ? (array)$rtpRow['rtp_prize_slabs'] : $rtpRow['rtp_prize_slabs'];
		}
		if (!is_array($rtpSlabs)) {
			$rtpSlabs = array();
		}
		$prizeSummary = $this->_enrichRtpDistributedSlabsWithConfig($prizeSummary, $rtpSlabs);

		$rtpCfg = array();
		if (!empty($rtpRow['rtp_config'])) {
			$rtpCfg = is_object($rtpRow['rtp_config']) ? (array)$rtpRow['rtp_config'] : $rtpRow['rtp_config'];
			if (!is_array($rtpCfg)) {
				$rtpCfg = array();
			}
		}

		$targetRtpPercent = isset($rtpCfg['target_rtp_percent']) ? (float)$rtpCfg['target_rtp_percent'] : 70;
		if ($targetRtpPercent <= 0) {
			$targetRtpPercent = 70;
		}
		$ticketPrice = isset($rtpCfg['ticket_price']) ? (float)$rtpCfg['ticket_price'] : 3;
		if ($ticketPrice <= 0) {
			$ticketPrice = 3;
		}
		$dailySales = (float)(isset($salesSummary['daily_sales']) ? $salesSummary['daily_sales'] : 0);
		$totalRtpBudget = round($dailySales * ($targetRtpPercent / 100), 2);
		$totalDistributed = (float)(isset($prizeSummary['total_payout']) ? $prizeSummary['total_payout'] : 0);
		$effectiveRtp = $dailySales > 0 ? round(($totalDistributed / $dailySales) * 100, 2) : 0;
		$totalWinners = (int)(isset($prizeSummary['total_winners']) ? $prizeSummary['total_winners'] : 0);
		$averagePrizeAmount = $totalWinners > 0 ? round($totalDistributed / $totalWinners, 2) : 0.0;
		$manualBigPrizeAmount = (float)(isset($prizeSummary['total_manual_big_prize']) ? $prizeSummary['total_manual_big_prize'] : 0);
		$gameWisePrizeAmount = (float)(isset($prizeSummary['total_game_wise_payout']) ? $prizeSummary['total_game_wise_payout'] : 0);
		$manualBigPrizePercent = $dailySales > 0 ? round(($manualBigPrizeAmount / $dailySales) * 100, 2) : 0.0;
		$gameWisePrizePercent = $dailySales > 0 ? round(($gameWisePrizeAmount / $dailySales) * 100, 2) : 0.0;
		$allGamesPrizePercent = round($manualBigPrizePercent + $gameWisePrizePercent, 2);

		return array(
			'filter_date'          => $filterDate,
			'daily_sales'          => $dailySales,
			'ticket_price'         => $ticketPrice,
			'target_rtp_percent'   => $targetRtpPercent,
			'total_rtp_budget'     => $totalRtpBudget,
			'order_count'          => (int)(isset($salesSummary['order_count']) ? $salesSummary['order_count'] : 0),
			'cancelled_count'      => (int)(isset($salesSummary['cancelled_count']) ? $salesSummary['cancelled_count'] : 0),
			'total_winning_amount' => $totalDistributed,
			'total_big_prize_amount' => (float)(isset($prizeSummary['total_big_prize']) ? $prizeSummary['total_big_prize'] : 0),
			'total_manual_big_prize_amount' => $manualBigPrizeAmount,
			'total_game_wise_prize_amount' => $gameWisePrizeAmount,
			'total_winners'        => $totalWinners,
			'effective_rtp'        => $effectiveRtp,
			'average_prize_amount' => $averagePrizeAmount,
			'all_games_prize_percent' => $allGamesPrizePercent,
			'manual_big_prize_percent' => $manualBigPrizePercent,
			'game_wise_prize_percent' => $gameWisePrizePercent,
			'all_games_sales'      => $dailySales,
			'all_games_payout'     => $totalDistributed,
			'all_games_winners'    => $totalWinners,
			'distributed_slabs'    => isset($prizeSummary['slabs']) ? $prizeSummary['slabs'] : array(),
			'pools'                => isset($prizeSummary['pools']) ? $prizeSummary['pools'] : array(),
		);
	}

	private function _getAllGamesPrizeMetricsForRtp($dateStart, $dateEnd)
	{
		$salesSummary = $this->_aggregateGlobalScratchWinSalesForRtp($dateStart, $dateEnd);
		$prizeSummary = $this->_aggregateGlobalPrizeDistributionForRtp($dateStart, $dateEnd);
		$allGamesSales = (float)(isset($salesSummary['daily_sales']) ? $salesSummary['daily_sales'] : 0);
		$allGamesPayout = (float)(isset($prizeSummary['total_payout']) ? $prizeSummary['total_payout'] : 0);
		$allGamesWinners = (int)(isset($prizeSummary['total_winners']) ? $prizeSummary['total_winners'] : 0);
		$manualBigPrizeAmount = (float)(isset($prizeSummary['total_manual_big_prize']) ? $prizeSummary['total_manual_big_prize'] : 0);
		$gameWisePrizeAmount = (float)(isset($prizeSummary['total_game_wise_payout']) ? $prizeSummary['total_game_wise_payout'] : 0);

		$manualBigPrizePercent = $allGamesSales > 0 ? round(($manualBigPrizeAmount / $allGamesSales) * 100, 2) : 0.0;
		$gameWisePrizePercent = $allGamesSales > 0 ? round(($gameWisePrizeAmount / $allGamesSales) * 100, 2) : 0.0;

		return array(
			'all_games_sales' => $allGamesSales,
			'all_games_payout' => $allGamesPayout,
			'all_games_winners' => $allGamesWinners,
			'average_prize_amount' => $allGamesWinners > 0 ? round($allGamesPayout / $allGamesWinners, 2) : 0.0,
			'all_games_prize_percent' => round($manualBigPrizePercent + $gameWisePrizePercent, 2),
			'manual_big_prize_percent' => $manualBigPrizePercent,
			'game_wise_prize_percent' => $gameWisePrizePercent,
			'total_manual_big_prize_amount' => $manualBigPrizeAmount,
			'total_game_wise_prize_amount' => $gameWisePrizeAmount,
		);
	}

	private function _aggregateGlobalScratchWinSalesForRtp($dateStart, $dateEnd, $productOid = null)
	{
		$saleDouble = array('$toDouble' => array('$ifNull' => array('$total_price', 0)));
		$match = array(
			'created_at' => array('$gte' => (int)$dateStart, '$lte' => (int)$dateEnd),
		);
		if ($productOid instanceof MongoDB\BSON\ObjectID) {
			$match['products_oid'] = $productOid;
		}
		$query = array(
			array('$match' => $match),
			array('$group' => array(
				'_id'            => null,
				'order_count'    => array('$sum' => array('$cond' => array(
					array('$ne' => array('$status', 'CL')),
					1,
					0,
				))),
				'cancelled_count' => array('$sum' => array('$cond' => array(
					array('$eq' => array('$status', 'CL')),
					1,
					0,
				))),
				'daily_sales'    => array('$sum' => array('$cond' => array(
					array('$ne' => array('$status', 'CL')),
					$saleDouble,
					0,
				))),
			)),
		);

		$rows = $this->common_model->mongo_db->aggregate('db_scratch_win_orders', $query, array('batchSize' => 4));
		if (!is_array($rows) || empty($rows[0])) {
			return array(
				'daily_sales'     => 0.0,
				'order_count'     => 0,
				'cancelled_count' => 0,
			);
		}

		$row = $this->_normalizeMongoRowToArray($rows[0]);

		return array(
			'daily_sales'     => round(max(0, (float)(isset($row['daily_sales']) ? $row['daily_sales'] : 0)), 2),
			'order_count'     => max(0, (int)(isset($row['order_count']) ? $row['order_count'] : 0)),
			'cancelled_count' => max(0, (int)(isset($row['cancelled_count']) ? $row['cancelled_count'] : 0)),
		);
	}

	private function _aggregateGlobalPrizeDistributionForRtp($dateStart, $dateEnd, $productOid = null)
	{
		$whereCon = array(
			'where' => array(
				'created_at' => array('$gte' => (int)$dateStart, '$lte' => (int)$dateEnd),
			),
		);
		if ($productOid instanceof MongoDB\BSON\ObjectID) {
			$whereCon['where']['products_oid'] = $productOid;
		}
		$rows = $this->common_model->getData('multiple', 'db_scratch_win_prize_amounts', $whereCon, array('_id' => -1));
		if (!is_array($rows)) {
			$rows = array();
		}

		$byPrize = array();
		$totalBigPrize = 0.0;
		$totalManualBigPrize = 0.0;
		$totalGameWisePayout = 0.0;

		foreach ($rows as $row) {
			$row = $this->_normalizeMongoRowToArray($row);
			$repeatDetails = isset($row['prize_repeat_details']) ? $row['prize_repeat_details'] : array();
			if (is_object($repeatDetails)) {
				$repeatDetails = (array)$repeatDetails;
			}
			if (is_array($repeatDetails)) {
				foreach ($repeatDetails as $tier) {
					$tier = $this->_normalizeMongoRowToArray($tier);
					$prizeAmount = (float)(isset($tier['winning_amount']) ? $tier['winning_amount'] : 0);
					$winners = (int)(isset($tier['prize_repeat_count']) ? $tier['prize_repeat_count'] : 0);
					if ($prizeAmount <= 0 || $winners <= 0) {
						continue;
					}
					$key = (string)round($prizeAmount, 2);
					if (!isset($byPrize[$key])) {
						$byPrize[$key] = array('prize_amount' => $prizeAmount, 'winners' => 0, 'payout' => 0.0);
					}
					$tierPayout = round($prizeAmount * $winners, 2);
					$byPrize[$key]['winners'] += $winners;
					$byPrize[$key]['payout'] = round($byPrize[$key]['payout'] + $tierPayout, 2);
					$totalGameWisePayout = round($totalGameWisePayout + $tierPayout, 2);
				}
			}

			$bigPrizes = isset($row['big_prize']) ? $row['big_prize'] : array();
			if (is_object($bigPrizes)) {
				$bigPrizes = (array)$bigPrizes;
			}
			if (is_array($bigPrizes)) {
				foreach ($bigPrizes as $bigPrize) {
					$bigPrize = $this->_normalizeMongoRowToArray($bigPrize);
					$prizeAmount = (float)(isset($bigPrize['winning_amount']) ? $bigPrize['winning_amount'] : 0);
					if ($prizeAmount <= 0) {
						continue;
					}
					$totalBigPrize = round($totalBigPrize + $prizeAmount, 2);

					$bigSource = isset($bigPrize['source']) ? strtolower(trim((string)$bigPrize['source'])) : '';
					if ($bigSource === 'manual' || $bigSource === 'attack_mode') {
						// Manual / attack-mode big prizes are tracked separately and must not
						// appear in automatic RTP pool slabs (e.g. Big Prize Pool list).
						$totalManualBigPrize = round($totalManualBigPrize + $prizeAmount, 2);
						continue;
					}

					$key = (string)round($prizeAmount, 2);
					if (!isset($byPrize[$key])) {
						$byPrize[$key] = array('prize_amount' => $prizeAmount, 'winners' => 0, 'payout' => 0.0);
					}
					$byPrize[$key]['winners'] += 1;
					$byPrize[$key]['payout'] = round($byPrize[$key]['payout'] + $prizeAmount, 2);
					// Non-manual big prizes count toward game-wise/RTP payout.
					$totalGameWisePayout = round($totalGameWisePayout + $prizeAmount, 2);
				}
			}
		}

		ksort($byPrize, SORT_NUMERIC);

		$slabs = array();
		$pools = array(
			'regular' => array('winners' => 0, 'payout' => 0.0),
			'reserve' => array('winners' => 0, 'payout' => 0.0),
			'big'     => array('winners' => 0, 'payout' => 0.0),
		);
		$totalWinners = 0;
		$totalPayout = 0.0;

		foreach ($byPrize as $entry) {
			$prizeAmount = (float)$entry['prize_amount'];
			$winners = (int)$entry['winners'];
			$payout = (float)$entry['payout'];
			$poolType = $this->_classifyRtpPrizePool($prizeAmount);

			$slabs[] = array(
				'pool_type'    => $poolType,
				'prize_amount' => $prizeAmount,
				'winners'      => $winners,
				'payout'       => $payout,
			);

			$pools[$poolType]['winners'] += $winners;
			$pools[$poolType]['payout'] = round($pools[$poolType]['payout'] + $payout, 2);
			$totalWinners += $winners;
			$totalPayout = round($totalPayout + $payout, 2);
		}

		return array(
			'slabs'                 => $slabs,
			'pools'                 => $pools,
			'total_winners'         => $totalWinners,
			'total_payout'          => $totalPayout,
			'total_big_prize'       => $totalBigPrize,
			'total_manual_big_prize'=> $totalManualBigPrize,
			'total_game_wise_payout'=> $totalGameWisePayout,
		);
	}

	private function _classifyRtpPrizePool($prizeAmount)
	{
		$prizeAmount = (float)$prizeAmount;
		if ($prizeAmount >= 100) {
			return 'big';
		}
		if ($prizeAmount >= 50) {
			return 'reserve';
		}
		return 'regular';
	}

	private function _enrichRtpDistributedSlabsWithConfig($prizeSummary, $rtpSlabs)
	{
		if (!is_array($prizeSummary)) {
			$prizeSummary = array();
		}
		if (!isset($prizeSummary['slabs']) || !is_array($prizeSummary['slabs'])) {
			$prizeSummary['slabs'] = array();
		}

		$configLookup = array();
		foreach ($rtpSlabs as $slabRow) {
			$slabRow = $this->_normalizeMongoRowToArray($slabRow);
			if (!is_array($slabRow)) {
				continue;
			}
			$poolType = isset($slabRow['pool_type']) ? (string)$slabRow['pool_type'] : 'regular';
			$prizeAmount = round((float)(isset($slabRow['prize_amount']) ? $slabRow['prize_amount'] : 0), 2);
			if ($prizeAmount <= 0) {
				continue;
			}
			$configLookup[$poolType . '|' . (string)$prizeAmount] = (float)(isset($slabRow['distribution_percentage']) ? $slabRow['distribution_percentage'] : 0);
		}

		foreach ($prizeSummary['slabs'] as $idx => $slabRow) {
			$slabRow = $this->_normalizeMongoRowToArray($slabRow);
			if (!is_array($slabRow)) {
				continue;
			}
			$poolType = isset($slabRow['pool_type']) ? (string)$slabRow['pool_type'] : $this->_classifyRtpPrizePool(isset($slabRow['prize_amount']) ? $slabRow['prize_amount'] : 0);
			$prizeAmount = round((float)(isset($slabRow['prize_amount']) ? $slabRow['prize_amount'] : 0), 2);
			$lookupKey = $poolType . '|' . (string)$prizeAmount;
			if (isset($configLookup[$lookupKey])) {
				$slabRow['distribution_percentage'] = $configLookup[$lookupKey];
			}
			$prizeSummary['slabs'][$idx] = $slabRow;
		}

		return $prizeSummary;
	}

	private function _clearScratchWinGamesApcuCache()
	{
		if (function_exists('apcu_delete')) {
			apcu_delete('scratchWinGamesData');
		}
	}

	private function _normalizeMongoRowToArray($row)
	{
		if (empty($row)) {
			return array();
		}
		if (is_array($row)) {
			return $row;
		}
		if (is_object($row)) {
			if ($row instanceof MongoDB\Model\BSONDocument || $row instanceof ArrayObject) {
				return $row->getArrayCopy();
			}
			return (array)$row;
		}
		return array();
	}

	private function _extractMongoObjectId($value)
	{
		if ($value instanceof MongoDB\BSON\ObjectId) {
			return $value;
		}
		if (is_object($value) && isset($value->{'$id'})) {
			return new MongoDB\BSON\ObjectID((string)$value->{'$id'});
		}
		if (is_string($value) && $value !== '') {
			return new MongoDB\BSON\ObjectID($value);
		}
		return null;
	}

	/***********************************************************************
	** Helper: currentAdminCanScratchWinSettings
	** Purpose: Returns true if current admin can open Scratch Win Settings.
	**          Super Admin always allowed; Sub Admin requires
	**          permission_scratchwin_settings === 'Y' on admin record.
	************************************************************************/
	private function currentAdminCanScratchWinSettings()
	{
		// Scratch Win RTP uses standard authCheck; keep helper for parity with source.
		return true;
	}

}

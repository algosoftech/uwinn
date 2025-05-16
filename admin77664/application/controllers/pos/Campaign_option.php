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

class Campaign_option extends CI_Controller {

	public function  __construct() 
	{ 
		parent:: __construct();
		error_reporting(E_ALL ^ E_NOTICE);  
		$this->load->model(array('admin_model','emailtemplate_model','emailsendgrid_model','sms_model','notification_model'));
		$this->lang->load('statictext', 'admin');
		$this->load->helper('common');
	} 
 	

 	public function index()
 	{		
		//echo $editId; die();
		$data['error'] 						= 	'';
		$data['activeMenu'] 				= 	'pos';
		$data['activeSubMenu'] 				= 	'campaign_option';
		
		// $this->admin_model->authCheck('add_data');
		// $this->admin_model->authCheck('edit_data');
		$data['selectedCampaign'] =	$this->common_model->getData('single','da_selected_campaign');
		$whereCon['where']  	= array('status' =>'A');
		$whereCon['where_gte']  = array(array('0'=>'draw_date' , '1' => date('Y-m-d' )));
		$tblName 				= "da_products";
		$shortField   			= array('products_id' => -1);
		$data['productlist'] 	= $this->common_model->getData('multiple',$tblName, $whereCon,$shortField);
		
		if($this->input->post('SaveChanges')):
			$error					=	'NO';
			$this->form_validation->set_rules('selected_campaign_list[]', 'selected_campaign_list', 'trim');
			$this->form_validation->set_rules('selected_web_campaign_list[]', 'selected_web_campaign_list', 'trim');
			$this->form_validation->set_rules('selected_app_campaign_list[]', 'selected_app_campaign_list', 'trim');
			$this->form_validation->set_message('is_unique', 'The %s is already taken');

			if($this->form_validation->run() && $error == 'NO'): 
				$param['selected_campaign_list'] =	array_map('intval', $this->input->post('selected_campaign_list'));  
				$param['selected_web_campaign_list'] =	array_map('intval', $this->input->post('selected_web_campaign_list'));  
				$param['selected_app_campaign_list'] =	array_map('intval', $this->input->post('selected_app_campaign_list'));  
				if($this->input->post('CurrentDataID') ==''):
					$param['campaign_id']		=	(int)$this->common_model->getNextSequence('da_selected_campaign');
					$param['creation_ip']		=	currentIp();
					$param['creation_date']		=	(int)$this->timezone->utc_time();//currentDateTime();
					$param['created_by']		=	(int)$this->session->userdata('HCAP_ADMIN_ID');
					$param['status']			=	'A';
					$alastInsertId				=	$this->common_model->addData('da_selected_campaign',$param);
					$this->session->set_flashdata('alert_success',lang('addsuccess'));
				else:
					$categoryId						 =	$this->input->post('CurrentDataID');
					$param['update_ip']				 =	currentIp();
					$param['update_date']		     =	date('Y-m-d h:i');
					$param['updated_by']		     =	(int)$this->session->userdata('HCAP_ADMIN_ID');

					$this->common_model->editData('da_selected_campaign',$param,'campaign_id',(int)$categoryId);
					$this->session->set_flashdata('alert_success',lang('updatesuccess'));
					redirect('pos/campaign_option/index');
				endif;
			endif;
		endif;

		$this->layouts->set_title('Add/Edit Sales Person | UWINN');
		$this->layouts->admin_view('pos/campaign_option/assign_campaign',array(),$data);
	}	// END OF FUNCTION	

 
}

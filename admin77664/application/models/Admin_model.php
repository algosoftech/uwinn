<?php
if(!defined('BASEPATH')) exit('No direct script access allowed');
class Admin_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	/* * *********************************************************************
	 * * Function name : Authenticate
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for imRD Admin Login Page
	 * * Date : 06 February 2024
	 * * **********************************************************************/
	public function Authenticate($userEmail='')
	{
		$this->mongo_db->select('*');
		$this->mongo_db->where(array('admin_email'=>$userEmail));
		$result = $this->mongo_db->find_one('uw_admin');
		if($result):
			return $result;
		else:	
			return false;
		endif;

	}	// END OF FUNCTION
	
	/* * *********************************************************************
	 * * Function name : encriptPassword
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for encript_password
	 * * Date : 06 February 2024
	 * * **********************************************************************/
	public function encriptPassword($password)
	{
		return $this->encrypt->encode($password, $this->config->item('encryption_key'));
	}	// END OF FUNCTION
	
	/* * *********************************************************************
	 * * Function name : encriptPin
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for encript_password
	 * * Date : 06 February 2024
	 * * **********************************************************************/
	public function encriptPin($pin)
	{
		return $this->encrypt->encode($pin, $this->config->item('encryption_key'));
	}	// END OF FUNCTION
	
	/* * *********************************************************************
	 * * Function name : decryptsPassword
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for encript_password
	 * * Date : 06 February 2024
	 * * **********************************************************************/
	public function decryptsPassword($password)
	{	
		return $this->encrypt->decode($password, $this->config->item('encryption_key'));
	}	// END OF FUNCTION
	
	/* * *********************************************************************
	 * * Function name : authCheck
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for auth Check
	 * * Date : 06 February 2024
	 * * **********************************************************************/
	public function authCheck($showType='')
	{  
		if($this->session->userdata('UW_ADMIN_ID') == ""):
			setcookie('UW_ADMIN_REFERENCE_PAGES',uri_string(),time()+60*60*24*5,'/');
			redirect(getCurrentBasePath().'logout');
		else:
			$this->mongo_db->select('*');
			$this->mongo_db->where(array('admin_id'=>(int)$this->session->userdata('UW_ADMIN_ID'),
								         'login_status'=>'Login',
								         'admin_token'=>isset($_COOKIE['UW_ADMIN_LOGIN_TOKEN']) ? $_COOKIE['UW_ADMIN_LOGIN_TOKEN'] : ''));
			$result = $this->mongo_db->find_one('uw_admin_login_log');
			if($result):
				if($showType==''):
					return true;
				elseif($this->checkPermission($showType)):  
					return true;
				else:		
					$this->session->set_flashdata('alert_warning',lang('accessdenied'));
					redirect($this->session->userdata('UW_ADMIN_CURRENT_PATH').'maindashboard');
				endif;
			else:
				$this->session->set_flashdata('alert_warning',lang('loginanothersystem'));
				setcookie('UW_ADMIN_REFERENCE_PAGES',uri_string(),time()+60*60*24*5,'/');
				redirect(getCurrentBasePath().'logout');
			endif;
		endif;
	}	// END OF FUNCTION
	
	/* * *********************************************************************
	 * * Function name : checkPermission
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for check Permission
	 * * Date : 06 February 2024
	 * * **********************************************************************/
	public function checkPermission($showType='')
	{  
		$returnType 			=	0;
		if($this->session->userdata('UW_ADMIN_TYPE') == 'Super Admin'):
			$returnType 		=	1;
		elseif($this->session->userdata('UW_ADMIN_TYPE') == 'Sub Admin'): 
			$adminId			=	$this->session->userdata('UW_ADMIN_ID');  
			$currentClass		=	$this->router->fetch_class();  
			$this->mongo_db->select('*');
			$this->mongo_db->where(array('admin_id'=>(int)$adminId));
			$this->mongo_db->where_or(array('module_name'=>$currentClass,'first_data.module_name'=>$currentClass,'second_data.module_name'=>$currentClass));
			$result = $this->mongo_db->find_one('uw_admin_permissions');
			if($result):	
				if($result['first_data']):
					$firstData 		=	$result['first_data'];
					foreach($firstData as $firstInfo):
						if($firstInfo->module_name == $currentClass && $firstInfo->$showType == 'Y' && $returnType==0):
							$returnType =	1;
						endif;
						// if($firstInfo['second_data']):
						// 	$secondData 	=	$firstInfo['second_data'];
						// 	foreach($secondData as $secondInfo):
						// 		if($secondInfo->module_name == $currentClass && $secondInfo->$showType == 'Y' && $returnType==0):
						// 			$returnType =	1;
						// 		endif;
						// 	endforeach;
						// else:
						// 	if($firstInfo->module_name == $currentClass && $firstInfo->$showType == 'Y' && $returnType==0):
						// 		$returnType =	1;


						// 	endif;
						// endif;
					endforeach;
				elseif($result[$showType] == 'Y' && $returnType==0):
					$returnType =	1;
				endif;
			elseif($this->session->userdata('UW_ADMIN_ID') == 100000000000008 && $returnType==0):
				$returnType =	1;
			endif;
		endif;
		return $returnType==1?true:false;
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name: getPermissionType
	** Developed By: Dilip Halder
	** Purpose: This function used for get permission type
	** Date : 06 February 2024
	************************************************************************/
	public function getPermissionType(&$data)	
	{
		if (!is_array($data)) {
			$data = array();
		}
		if($this->session->userdata('UW_ADMIN_TYPE') == 'Super Admin'):
			$data['view_data']		=	'Y';
			$data['add_data']		=	'Y';
			$data['edit_data']		=	'Y';
			$data['delete_data']	=	'Y';
		elseif($this->session->userdata('UW_ADMIN_TYPE') == 'Sub Admin'):
			$data['view_data']		=	'N';
			$data['add_data']		=	'N';
			$data['edit_data']		=	'N';
			$data['delete_data']	=	'N';
			$adminId			=	$this->session->userdata('UW_ADMIN_ID');  
			$currentClass		=	$this->router->fetch_class();  
			$this->mongo_db->select('*');
			$this->mongo_db->where(array('admin_id'=>(int)$adminId));
			$this->mongo_db->where_or(array('module_name'=>$currentClass,'first_data.module_name'=>$currentClass,'second_data.module_name'=>$currentClass));
			$result = $this->mongo_db->find_one('uw_admin_permissions');
			if($result):
				$result = json_decode(json_encode($result), true);
				if(!empty($result['first_data']) && is_array($result['first_data'])):
					foreach($result['first_data'] as $firstInfo):
						$firstInfo = is_array($firstInfo) ? $firstInfo : json_decode(json_encode($firstInfo), true);
						$data['view_data']		=	$firstInfo['view_data'] ?? 'N';
						$data['add_data']		=	$firstInfo['add_data'] ?? 'N';
						$data['edit_data']		=	$firstInfo['edit_data'] ?? 'N';
						$data['delete_data']	=	$firstInfo['delete_data'] ?? 'N';
					endforeach;
				else:
					$data['view_data']		=	$result['view_data'] ?? 'N';
					$data['add_data']		=	$result['add_data'] ?? 'N';
					$data['edit_data']		=	$result['edit_data'] ?? 'N';
					$data['delete_data']	=	$result['delete_data'] ?? 'N';
				endif;
			endif;
		endif;
	}

	/***********************************************************************
	** Function name: getMenuModule
	** Developed By: Dilip Halder
	** Purpose: This function used for getMenuModule
	** Date : 06 February 2024
	************************************************************************/
	public function getMenuModule($type='main',$mainModuleName='')	
	{  
		$returnArray 		=	array();
		if($this->session->userdata('UW_ADMIN_TYPE') == 'Super Admin'):
			if($type == 'main'):
				$this->mongo_db->select('*');
				$this->mongo_db->order_by(array('module_orders'=>1));
				$result = $this->mongo_db->get('uw_admin_module');
				if($result):
					foreach($result as $info):
						$info['first_data']		=	$info['first_data'][0]->module_name;
						array_push($returnArray,$info);
					endforeach;
					return $returnArray;
				else:	
					return false;
				endif;
			else:
				$this->mongo_db->select('*');
				$this->mongo_db->where(array('module_name'=>$mainModuleName));
				$this->mongo_db->order_by(array('module_orders'=>1));
				$result = $this->mongo_db->find_one('uw_admin_module');
				if($result):
					return $result;
				else:	
					return false;
				endif;
			endif;
		elseif($this->session->userdata('UW_ADMIN_TYPE') == 'Sub Admin'): 
			$adminId			=	$this->session->userdata('UW_ADMIN_ID');  
			if($type == 'main'):
				$this->mongo_db->select('*');
				$this->mongo_db->where(array('admin_id'=>(int)$adminId));
				$this->mongo_db->order_by(array('module_orders'=>1));
				$result = $this->mongo_db->get('uw_admin_permissions');
				if($result):
					foreach($result as $info):
						$info['first_data']		=	$info['first_data'][0]->module_name;
						array_push($returnArray,$info);
					endforeach;
					return $returnArray;
				else:	
					return false;
				endif;
			else:
				$this->mongo_db->select('*');
				$this->mongo_db->where(array('module_name'=>$mainModuleName,'admin_id'=>(int)$adminId));
				$this->mongo_db->order_by(array('module_orders'=>1));
				$result = $this->mongo_db->find_one('uw_admin_permissions');
				if($result):
					return $result;
				else:	
					return false;
				endif;
			endif;
		endif;
	}

	/***********************************************************************
	** Function name: getMenuModuleNew
	** Developed By: Dilip Halder
	** Purpose: This function used for getMenuModuleNew
	** Date: 09 JULY 2021
	************************************************************************/
	public function getMenuModuleNew()	
	{  
		if($this->session->userdata('UW_ADMIN_TYPE') == 'Super Admin'):
			$this->mongo_db->select('*');
			$this->mongo_db->order_by(array('module_orders'=>1));
			$result = $this->mongo_db->get('uw_admin_module');
			if($result):
				return $result;
			else:	
				return false;
			endif;
		elseif($this->session->userdata('UW_ADMIN_TYPE') == 'Sub Admin'): 
			$adminId			=	$this->session->userdata('UW_ADMIN_ID');  
			$this->mongo_db->select('*');
			$this->mongo_db->where(array('admin_id'=>(int)$adminId));
			$this->mongo_db->order_by(array('module_orders'=>1));
			$result = $this->mongo_db->get('uw_admin_permissions');
			if($result):
				return $result;
			else:	
				return false;
			endif;
		endif;
	}

	/***********************************************************************
	** Function name: getModule
	** Developed By: Dilip Halder
	** Purpose: This function used for get Module
	** Date : 06 February 2024
	************************************************************************/
	public function getModule()	
	{  
		$this->mongo_db->select('*');
		$this->mongo_db->order_by(array('module_orders'=>1));
		$result = $this->mongo_db->get('uw_admin_module');
		if($result):
			return $result;
		else:	
			return false;
		endif;
	}

	/***********************************************************************
	** Function name: getModulePermission
	** Developed By: Dilip Halder
	** Purpose: This function used for get Module Permission
	** Date : 06 February 2024
	************************************************************************/
	public function getModulePermission($adminId='')	
	{  
		$selecarray				=	array();
		$this->mongo_db->select('*');
		$this->mongo_db->where(array('admin_id'=>(int)$adminId));
		$this->mongo_db->order_by(array('module_orders'=>1));
		$result = $this->mongo_db->get('uw_admin_permissions');
		if($result):
			foreach($result as $MDinfo):
				$selecarray['mainmodule_'.$MDinfo['module_id']]							=	'Y';
				if($MDinfo['first_data']):
					foreach($MDinfo['first_data'] as $CDinfo):
						$selecarray['firstchildmodule_'.$MDinfo['module_id'].'_'.$CDinfo->module_id]							=	'Y';
						if($CDinfo->second_data):
							foreach($CDinfo->second_data as $CCDinfo):
								$selecarray['secondchildmodule_'.$MDinfo['module_id'].'_'.$CDinfo->module_id.'_'.$CCDinfo->module_id]						=	'Y';
								if($CCDinfo->view_data == 'Y'):
									$selecarray['secondchildmodule_view_data_'.$MDinfo['module_id'].'_'.$CDinfo->module_id.'_'.$CCDinfo->module_id]			=	'Y';
								endif;
								if($CCDinfo->add_data == 'Y'):
									$selecarray['secondchildmodule_add_data_'.$MDinfo['module_id'].'_'.$CDinfo->module_id.'_'.$CCDinfo->module_id]			=	'Y';
								endif;
								if($CCDinfo->edit_data == 'Y'):
									$selecarray['secondchildmodule_edit_data_'.$MDinfo['module_id'].'_'.$CDinfo->module_id.'_'.$CCDinfo->module_id]			=	'Y';
								endif;
								if($CCDinfo->delete_data == 'Y'):
									$selecarray['secondchildmodule_delete_data_'.$MDinfo['module_id'].'_'.$CDinfo->module_id.'_'.$CCDinfo->module_id]		=	'Y';
								endif;
							endforeach;
						else:
							if($CDinfo->view_data == 'Y'):
								$selecarray['firstchildmodule_view_data_'.$MDinfo['module_id'].'_'.$CDinfo->module_id]			=	'Y';
							endif;
							if($CDinfo->add_data == 'Y'):
								$selecarray['firstchildmodule_add_data_'.$MDinfo['module_id'].'_'.$CDinfo->module_id]			=	'Y';
							endif;
							if($CDinfo->edit_data == 'Y'):
								$selecarray['firstchildmodule_edit_data_'.$MDinfo['module_id'].'_'.$CDinfo->module_id]			=	'Y';
							endif;
							if($CDinfo->delete_data == 'Y'):
								$selecarray['firstchildmodule_delete_data_'.$MDinfo['module_id'].'_'.$CDinfo->module_id]		=	'Y';
							endif;
						endif;
					endforeach;
				else:
					if($MDinfo['view_data'] == 'Y'):
						$selecarray['mainmodule_view_data_'.$MDinfo['module_id']]		=	'Y';
					endif;
					if($MDinfo['add_data'] == 'Y'):
						$selecarray['mainmodule_add_data_'.$MDinfo['module_id']]		=	'Y';
					endif;
					if($MDinfo['edit_data'] == 'Y'):
						$selecarray['mainmodule_edit_data_'.$MDinfo['module_id']]		=	'Y';
					endif;
					if($MDinfo['delete_data'] == 'Y'):
						$selecarray['mainmodule_delete_data_'.$MDinfo['module_id']]		=	'Y';
					endif; 
				endif;
			endforeach;
		endif;
		return $selecarray;
	}

	/* * *********************************************************************
	 * * Function name : checkOTP
	 * * Developed By : Dilip Halder
	 * * Purpose  : This function used for Admin otp
	 * * Date : 06 February 2024
	 * * **********************************************************************/
	public function checkOTP($userOtp='')
	{
		$this->mongo_db->select('*');
		$this->mongo_db->where(array('admin_password_otp'=>(int)$userOtp));
		$result = $this->mongo_db->find_one('uw_admin');
		if($result):
			return $result;
		else:	
			return false;
		endif;
	}
	
	public function checkPin($userOtp='')
	{
	    $this->mongo_db->select('*');
		$this->mongo_db->where(array('admin_pin'=>(int)$userOtp));
		$result = $this->mongo_db->find_one('uw_admin');
		if($result):
			return $result;
		else:	
			return false;
		endif;
	}	
	// END OF FUNCTION



	/***********************************************************************
	** Function name : getDepartment
	** Developed By : Dilip Halder
	** Purpose  : This function used for get Department
	** Date : 06 February 2024
	************************************************************************/
	function getDepartment($departmentId='')
	{
		$html			=	'<option value="">Select Department</option>';
		$this->mongo_db->select('*');
		$this->mongo_db->where(array('status'=>'A'));
		$this->mongo_db->order_by(array('department_name'=>'ASC'));	
		$result = $this->mongo_db->get('uw_admin_department');
		if($result):	
			foreach($result as $info):
				if($info['department_id'] == $departmentId):  $select ='selected="selected"'; else: $select =''; endif;
				$html		.=	'<option value="'.$info['department_id'].'_____'.stripslashes($info['department_name']).'" '.$select.'>'.stripslashes($info['department_name']).'</option>';
			endforeach;
		endif;
		return $html;
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name : getDesignation
	** Developed By : Dilip Halder
	** Purpose  : This function used for get Designation
	** Date : 06 February 2024
	************************************************************************/
	function getDesignation($designationId='')
	{
		$html			=	'<option value="">Select Designation</option>';
		$this->mongo_db->select('*');
		$this->mongo_db->where(array('status'=>'A'));
		$this->mongo_db->order_by(array('designation_name'=>'ASC'));	
		$result = $this->mongo_db->get('uw_admin_designation');
		if($result):	
			foreach($result as $info):
				if($info['designation_id'] == $designationId):  $select ='selected="selected"'; else: $select =''; endif;
				$html		.=	'<option value="'.$info['designation_id'].'_____'.stripslashes($info['designation_name']).'" '.$select.'>'.stripslashes($info['designation_name']).'</option>';
			endforeach;
		endif;
		return $html;
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name: getTableAllData
	** Developed By: Dilip Halder
	** Purpose: This function used for get Module
	** Date : 06 February 2024
	************************************************************************/
	public function getTableAllData($tableName='')	
	{  
		$this->mongo_db->select('*');
		$result = $this->mongo_db->get($tableName);
		if($result):
			return $result;
		else:	
			return false;
		endif;
	}

	/***********************************************************************
	** Function name : getCategory
	** Developed By : Dilip Halder
	** Purpose  : This function used for get Category
	** Date : 06 February 2024
	************************************************************************/
	function getCategory($designationId='')
	{
		$html			=	'<option value="">Select Category</option>';
		$this->mongo_db->select('*');
		$this->mongo_db->where(array('status'=>'A'));
		$this->mongo_db->order_by(array('category_name'=>'ASC'));	
		$result = $this->mongo_db->get('uw_category');
		if($result):	
			foreach($result as $info):
				if($info['category_id'] == $designationId):  $select ='selected="selected"'; else: $select =''; endif;
				$html		.=	'<option value="'.$info['category_id'].'_____'.stripslashes($info['category_name']).'" '.$select.'>'.stripslashes($info['category_name']).'</option>';
			endforeach;
		endif;
		return $html;
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name : getProductList
	** Developed By : Dilip Halder
	** Purpose  : This function used for get Product List
	** Date : 06 February 2024
	************************************************************************/
	function getProductList($product_id='')
	{
		$html			=	'<option value="">Select Product</option>';
		$this->mongo_db->select('*');
		$this->mongo_db->where(array('status'=>'A'));
		$this->mongo_db->order_by(array('title'=>'ASC'));	
		$result = $this->mongo_db->get('uw_products');
		if($result):	
			foreach($result as $info):
				if($info['products_id'] == $product_id):  $select ='selected="selected"'; else: $select =''; endif;
				$html		.=	'<option value="'.$info['products_id'].'_____'.stripslashes($info['title']).'" '.$select.'>'.stripslashes($info['title']).'</option>';
			endforeach;
		endif;
		return $html;
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name : getProductCategory
	** Developed By : Dilip Halder
	** Purpose  : This function used for get Category
	** Date : 06 February 2024
	************************************************************************/
	function getProductCategory($designationId='')
	{
		$html			=	'<option value="">Select Category</option>';
		$this->mongo_db->select('*');
		$this->mongo_db->where(array('status'=>'A'));
		$this->mongo_db->order_by(array('category_name'=>'ASC'));	
		$result = $this->mongo_db->get('uw_category');
		if($result):	
			foreach($result as $info):
				if($info['category_id'] == $designationId):  $select ='selected="selected"'; else: $select =''; endif;
				$html		.=	'<option value="'.$info['category_id'].'_____'.stripslashes($info['category_name']).'" '.$select.'>'.stripslashes($info['category_name']).'</option>';
			endforeach;
		endif;
		return $html;
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name : getEmirates
	** Developed By : Dilip Halder
	** Purpose  : This function used for get Emirates
	** Date : 06 February 2024
	************************************************************************/
	function getEmirates($emirateId='')
	{
		$html			=	'<option value="">Select Emirate</option>';
		$this->mongo_db->select('*');
		$this->mongo_db->where(array('status'=>'A'));
		$this->mongo_db->order_by(array('emirate_name'=>'ASC'));	
		$result = $this->mongo_db->get('uw_emirate');
		if($result):	
			foreach($result as $info):
				if($info['emirate_id'] == $emirateId):  $select ='selected="selected"'; else: $select =''; endif;
				$html		.=	'<option value="'.$info['emirate_id'].'_____'.stripslashes($info['emirate_name']).'" '.$select.'>'.stripslashes($info['emirate_name']).'</option>';
			endforeach;
		endif;
		return $html;
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name : getEmiratesArea
	** Developed By : Afsar Ali
	** Purpose  : This function used for get Emirates
	** Date : 06 February 2024
	************************************************************************/
	function getEmiratesArea($emirateId='',$area_id='')
	{
		$html			=	'<option value="">Select Area</option>';
		$this->mongo_db->select('*');
		$this->mongo_db->where(array('status'=>'A','emirate_id'=>(int)$emirateId));
		$this->mongo_db->order_by(array('area_name'=>'ASC'));	
		$result = $this->mongo_db->get('uw_area');
		
		if($result):	
			foreach($result as $info):
				if($info['area_id'] == $area_id):  $select ='selected="selected"'; else: $select =''; endif;
				$html		.=	'<option value="'.$info['area_id'].'_____'.stripslashes($info['area_name']).'" '.$select.'>'.stripslashes($info['area_name']).'</option>';
			endforeach;
		endif;
		return $html;
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name : getsubCategory
	** Developed By : Dilip Halder
	** Purpose  : This function used for get Category
	** Date : 06 February 2024
	************************************************************************/
	function getsubCategory($designationId='')
	{
		$html			=	'<option value="">Select sub category</option>';
		$this->mongo_db->select('*');
		$this->mongo_db->where(array('status'=>'A'));
		$this->mongo_db->order_by(array('uw_sub_category'=>'ASC'));	
		$result = $this->mongo_db->get('uw_sub_category');
		if($result):	
			foreach($result as $info):
				if($info['sub_category_id'] == $designationId):  $select ='selected="selected"'; else: $select =''; endif;
				$html		.=	'<option value="'.$info['sub_category_id'].'_____'.stripslashes($info['sub_category_name']).'" '.$select.'>'.stripslashes($info['sub_category_name']).'</option>';
			endforeach;
		endif;
		return $html;
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name: checkInstectorLogin
	** Developed By: Dilip Halder
	** Purpose: This function used for check Instector Login
	** Date : 03 AUGUST 2021
	************************************************************************/ 
	public function checkInstectorLogin($inspector_id='')
	{  
		$this->mongo_db->select('inspector_token');	
		$this->mongo_db->where(array('inspector_id'=>(int)$inspector_id));
		$this->mongo_db->where(array('login_status'=>'Login'));
		$this->mongo_db->order_by(array('login_log_id'=>-1));
		$this->mongo_db->limit(1);
		$result = $this->mongo_db->get('uw_inspector_login_log');
		if($result):
			return 1;
		else:
			return 0;
		endif;
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name 	: getCategoryList
	** Developed By 	: Dilip Halder
	** Purpose  		: This function used for get Category
	** Date 			: 05 APRIL 2022
	************************************************************************/
	function getCategoryList($categoryId='')
	{
		$html			=	'<option value="">Select Category</option>';
		$this->mongo_db->select('*');
		$this->mongo_db->where(array('status'=>'A'));
		$this->mongo_db->order_by(array('category_name'=>'ASC'));	
		$result = $this->mongo_db->get('uw_category');
		if($result):	
			foreach($result as $info):
				if($info['category_id'] == $categoryId):  $select ='selected="selected"'; else: $select =''; endif;
				$html		.=	'<option value="'.$info['category_id'].'_____'.stripslashes($info['category_name']).'" '.$select.'>'.stripslashes($info['category_name']).'</option>';
			endforeach;
		endif;
		return $html;
	}	// END OF FUNCTION
	
		
	/***********************************************************************
** Function name 	: getOnlyOneData
** Developed By 	: AFSAR ALI
** Purpose 			: This function used for get only one entry
** Date 			: 15 APRIL 2022
************************************************************************/ 
public function getOnlyOneData($tbl_name, $whereCon){
	$this->mongo_db->select('*');
	$this->mongo_db->where($whereCon);
	//$this->mongo_db->limit(1);
	$result = $this->mongo_db->find_one($tbl_name);
	return $result;
} // END OF FUNCTION

/***********************************************************************
	** Function name 	: getCampaignCategory
	** Developed By 	: Team
	** Purpose  		: Return category options by Mongo _id
	** Date 			: 02 MAR 2026
	************************************************************************/
	function getCampaignCategory($categoryOid = '')
	{
		$html = '<option value="">Select Category</option>';
		$this->mongo_db->select('*');
		$this->mongo_db->where(array('status' => 'A'));
		$this->mongo_db->order_by(array('category_name' => 'ASC'));
		$result = $this->mongo_db->get('uw_category');
		if ($result):
			foreach ($result as $info):
				$currentOid = isset($info['_id']->{'$id'}) ? $info['_id']->{'$id'} : '';
				$selected = ((string)$currentOid === (string)$categoryOid) ? 'selected="selected"' : '';
				$name = isset($info['category_name']) ? $info['category_name'] : '';
				$html .= '<option value="'.$currentOid.'" '.$selected.'>'.stripslashes($name).'</option>';
			endforeach;
		endif;
		return $html;
	}	// END OF FUNCTION

	/***********************************************************************
	** Function name 	: getCampaignSubCategory
	** Developed By 	: Team
	** Purpose  		: Return sub category options by Mongo _id
	** Date 			: 02 MAR 2026
	************************************************************************/
	function getCampaignSubCategory($categoryOid = '', $subCategoryOid = '')
	{
		$html = '<option value="">Select Sub Category</option>';
		$this->mongo_db->select('*');
		$whereCon = array('status' => 'A', 'category_oid' => new MongoDB\BSON\ObjectId($categoryOid));
		$this->mongo_db->where($whereCon);
		$this->mongo_db->order_by(array('sub_category_name' => 'ASC'));
		$result = $this->mongo_db->get('uw_sub_category');

		if ($result):
			foreach ($result as $info):
				$currentOid = isset($info['_id']->{'$id'}) ? $info['_id']->{'$id'} : '';
				$selected = ((string)$currentOid === (string)$subCategoryOid) ? 'selected="selected"' : '';
				$name = isset($info['sub_category']) ? $info['sub_category'] : (isset($info['sub_category_name']) ? $info['sub_category_name'] : '');
				$html .= '<option value="'.$currentOid.'" '.$selected.'>'.stripslashes($name).'</option>';
			endforeach;
		endif;

		return $html;
	}	// END OF FUNCTION




}
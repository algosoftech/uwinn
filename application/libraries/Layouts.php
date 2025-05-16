<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Layouts
{
	// hold CI intance 
	private $CI;
	//hold layout title
	private $layout_title = NULL;
	//hold layout discription
	private $layout_description = NULL;
	
	public function __construct()
	{
		$this->CI = & get_instance();
	}

	public function front_view($view_name, $layouts=array(), $params=array(),$viewtype='')
	{	
		$this->CI->load->library('parser');
		if(is_array($layouts) && count($layouts) >=1):
			foreach($layouts as $layout_key => $layout):
				$params[$layout_key] = $this->CI->parser->parse($layout, $params, true);
			endforeach;
		endif;
		
		$params['BASE_URL']				= 	base_url();
		// $params['FULL_SITE_URL']		= 	$this->CI->session->userdata('HCAP_ADMIN_CURRENT_PATH')?$this->CI->session->userdata('HCAP_ADMIN_CURRENT_PATH'):getCurrentBasePath();
		$params['ASSET_URL']			= 	base_url().'assets/';
		$params['ASSET_INCLUDE_URL']	= 	base_url().'assets/admin/';

		$params['CURRENT_CLASS']		= 	$this->CI->router->fetch_class();
		$params['CURRENT_METHOD']		= 	$this->CI->router->fetch_method();
		
		$pagedata['title'] 				= 	$this->layout_title?$this->layout_title:'Login';
		$pagedata['description']		= 	$this->layout_description;
		$pagedata['keyword'] 			= 	$this->keyword;
			
		if($viewtype == 'onlyview'):
			$this->CI->parser->parse($view_name, $params);
		elseif($viewtype == 'login'):
			$pagedata['head'] 				= 	$this->CI->parser->parse("layouts/admin/login_head",$params,true);
			$pagedata['content']			= 	$this->CI->parser->parse($view_name,$params,true);
			$pagedata['footer_js'] 			= 	$this->CI->parser->parse("layouts/admin/login_footer_js",$params,true);
			$this->CI->parser->parse("layouts/admin_login", $pagedata);
		
		elseif($viewtype == 'userview'):
			$pagedata['head'] 				= 	$this->CI->parser->parse("layouts/common/head",$params,true);
			$pagedata['header'] 			= 	$this->CI->parser->parse("layouts/common/header",$params,true);
			$pagedata['navigation'] 		= 	$this->CI->parser->parse("layouts/common/navigation",$params,true);
			$pagedata['banner'] 			= 	$this->CI->parser->parse("layouts/common/banner",$params,true);
			$pagedata['slider'] 			= 	$this->CI->parser->parse("layouts/common/slider",$params,true);
			$pagedata['sidebar'] 			= 	$this->CI->parser->parse("layouts/common/sidebar",$params,true);
			$pagedata['content']			= 	$this->CI->parser->parse($view_name,$params,true);
			$pagedata['footer'] 			= 	$this->CI->parser->parse("layouts/common/footer",$params,true);
			$pagedata['footer_js'] 			= 	$this->CI->parser->parse("layouts/common/footer_js",$params,true);
			$this->CI->parser->parse("layouts/userfront", $pagedata);
		elseif($viewtype == 'commonview'):
			$pagedata['head'] 				= 	$this->CI->parser->parse("layouts/common/head",$params,true);
			$pagedata['header'] 			= 	$this->CI->parser->parse("layouts/common/header",$params,true);
			$pagedata['navigation'] 		= 	$this->CI->parser->parse("layouts/common/navigation",$params,true);
			$pagedata['banner'] 			= 	$this->CI->parser->parse("layouts/common/banner",$params,true);
			$pagedata['slider'] 			= 	$this->CI->parser->parse("layouts/common/slider",$params,true);
			$pagedata['content']			= 	$this->CI->parser->parse($view_name,$params,true);
			$pagedata['footer'] 			= 	$this->CI->parser->parse("layouts/common/footer",$params,true);
			$pagedata['footer_js'] 			= 	$this->CI->parser->parse("layouts/common/footer_js",$params,true);
			$this->CI->parser->parse("layouts/commonfront", $pagedata);
		elseif($viewtype == 'apiview'):
			$pagedata['head'] 				= 	$this->CI->parser->parse("layouts/common/head",$params,true);
			$pagedata['header'] 			= 	$this->CI->parser->parse("layouts/common/header",$params,true);
			$pagedata['slider'] 			= 	$this->CI->parser->parse("layouts/common/slider",$params,true);
			$pagedata['content']			= 	$this->CI->parser->parse($view_name,$params,true);
			$pagedata['footer_js'] 			= 	$this->CI->parser->parse("layouts/common/footer_js",$params,true);
			$this->CI->parser->parse("layouts/apifront", $pagedata);
		elseif($viewtype == 'mobileview'):
			$pagedata['head'] 				= 	$this->CI->parser->parse("layouts/common-mobile/head",$params,true);
			$pagedata['header'] 			= 	$this->CI->parser->parse("layouts/common-mobile/header",$params,true);
			$pagedata['footer_nav'] 		= 	$this->CI->parser->parse("layouts/common-mobile/footer_nav",$params,true);
			$pagedata['slider'] 			= 	$this->CI->parser->parse("layouts/common-mobile/slider",$params,true);
			$pagedata['content']			= 	$this->CI->parser->parse('mobile/'.$view_name,$params,true);
			$pagedata['footer_js'] 			= 	$this->CI->parser->parse("layouts/common-mobile/footer_js",$params,true);
			$this->CI->parser->parse("layouts/mobileview", $pagedata);
		elseif($viewtype == 'common_mobileview'):
			$pagedata['head'] 				= 	$this->CI->parser->parse("layouts/common-mobile/head",$params,true);
			$pagedata['header'] 			= 	$this->CI->parser->parse("layouts/common-mobile/header",$params,true);
			$pagedata['slider'] 			= 	$this->CI->parser->parse("layouts/common-mobile/slider",$params,true);
			$pagedata['content']			= 	$this->CI->parser->parse('mobile/'.$view_name,$params,true);
			$pagedata['countrycode_list'] 	= 	$this->CI->parser->parse("layouts/common-mobile/countrycode-list",$params,true);
			$pagedata['footer_js'] 			= 	$this->CI->parser->parse("layouts/common-mobile/footer_js",$params,true);
			$this->CI->parser->parse("layouts/common_mobileview", $pagedata);
		else:
			$pagedata['head'] 				= 	$this->CI->parser->parse("layouts/common/head",$params,true);
			$pagedata['navigation'] 		= 	$this->CI->parser->parse("layouts/common/navigation",$params,true);
			$pagedata['slider'] 			= 	$this->CI->parser->parse("layouts/common/slider",$params,true);
			$pagedata['content']			= 	$this->CI->parser->parse($view_name,$params,true);
			$pagedata['footer'] 			= 	$this->CI->parser->parse("layouts/common/footer",$params,true);
			$pagedata['footer_js'] 			= 	$this->CI->parser->parse("layouts/common/footer_js",$params,true);
			$this->CI->parser->parse("layouts/front", $pagedata);
		endif;
	}

	/**
     * Set page title
     *
     * @param $title
     */
    public function set_title($title)
	{
		$this->layout_title = $title;
		return $this;
	}
	
	/**
     * Set page description
     *
     * @param $description
     */
    public function set_description($description)
	{
		$this->layout_description = $description;
		return $this;
	}
	
	/**
     * Set page keyword
     *
     * @param $keyword
     */
    public function set_keyword($keyword)
	{
		$this->layout_keyword = $keyword;
		return $this;
	}
}
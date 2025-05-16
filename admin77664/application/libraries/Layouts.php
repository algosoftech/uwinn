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

	public function admin_view($view_name, $layouts=array(), $params=array(),$viewtype='')
	{	
		$this->CI->load->library('parser');
		if(is_array($layouts) && count($layouts) >=1):
			foreach($layouts as $layout_key => $layout):
				$params[$layout_key] = $this->CI->parser->parse($layout, $params, true);
			endforeach;
		endif;
		
		$params['BASE_URL']				= 	base_url();
		$params['FULL_SITE_URL']		= 	$this->CI->session->userdata('HCAP_ADMIN_CURRENT_PATH')?$this->CI->session->userdata('HCAP_ADMIN_CURRENT_PATH'):getCurrentBasePath();
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
		elseif($viewtype == 'maindashboard'):
			$pagedata['head'] 				= 	$this->CI->parser->parse("layouts/admin/maindashboard_head",$params,true);
			$pagedata['navigation'] 		= 	$this->CI->parser->parse("layouts/admin/maindashboard_navigation",$params,true);
			$pagedata['content']			= 	$this->CI->parser->parse($view_name,$params,true);
			$pagedata['footer'] 			= 	$this->CI->parser->parse("layouts/admin/maindashboard_footer",$params,true);
			$pagedata['footer_js'] 			= 	$this->CI->parser->parse("layouts/admin/maindashboard_footer_js",$params,true);
			$this->CI->parser->parse("layouts/maindashboard", $pagedata);
		else:
			$pagedata['head'] 				= 	$this->CI->parser->parse("layouts/admin/head",$params,true);
			$pagedata['menu'] 				= 	$this->CI->parser->parse("layouts/admin/menu",$params,true);
			$pagedata['navigation'] 		= 	$this->CI->parser->parse("layouts/admin/navigation",$params,true);
			$pagedata['content']			= 	$this->CI->parser->parse($view_name,$params,true);
			$pagedata['footer'] 			= 	$this->CI->parser->parse("layouts/admin/footer",$params,true);
			$pagedata['footer_js'] 			= 	$this->CI->parser->parse("layouts/admin/footer_js",$params,true);
			$this->CI->parser->parse("layouts/admin", $pagedata);
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
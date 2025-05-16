<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Upload_crop_img
{
	// hold CI intance
	private $CI;

	public function __construct()
	{
		//parent::__construct();
		//$this->CI = & get_instance();
	}

	function _upload_image($file_name='',$tmp_name='',$type='',$newfilename='',$subfolder='')
	{ 
		$img_properties			=	$this->_get_image_path_by_referance_and_id($type,$subfolder);
		if(!is_array($img_properties)) die("Please Set Image Properties For Upload Path ,Allowed Types ,Max Size etc. In Array Form");

		if($newfilename):
			$file_name	 		= 	$newfilename;
		else:
			$file_name	 		= 	time().sanitizedFilename($file_name);
		endif;

		if(move_uploaded_file($tmp_name, $img_properties['original']['path'].$file_name)): 

			//For creating original nails perfect size Start,.......
			if(isset($img_properties['original']['perfect'])):
				$this->_create_resized_image($file_name,$img_properties['perfect']);
			endif;

			//For creating original nails medium size Start,...
			if(isset($img_properties['original']['medium'])):
				$this->_create_resized_image($file_name,$img_properties['medium']);
			endif;

			//For creating thumb nails Start,.......
			if(isset($img_properties['original']['thumb'])):
				$this->_create_resized_image($file_name,$img_properties['thumb']);
			endif;

			//$imagefolder   = $img_properties['thumb']['path']?$img_properties['thumb']['path']:($img_properties['medium']['path']?$img_properties['medium']['path']:$img_properties['original']['path']);
			$imagefolder   =  $img_properties['original']['path'];
			//return base_url().$imagefolder.$file_name;
			//return $imagefolder.$file_name;
			return str_replace(fileFCPATH,'',$imagefolder).$file_name;
		else:
			return 'UPLODEERROR';
		endif;
	}

	function _upload_canvas_image($image_data='',$image_name='',$type='',$subfolder='')
	{ 
		$img_properties			=	$this->_get_image_path_by_referance_and_id($type,$subfolder);
		if(!is_array($img_properties)) die("Please Set Image Properties For Upload Path ,Allowed Types ,Max Size etc. In Array Form");

		if($image_data && $image_name && $img_properties):	
			$photo_name		=	str_replace('./','',$img_properties['original']['path'].$image_name);
			/* Decoding image */
			$image_uri 		=  	substr($image_data,strpos($image_data,",")+1);
			$binary 		= 	base64_decode($image_uri);
			/* Opening image */
			//header('Content-Type: bitmap; charset=utf-8');
			$file 			= 	fopen ($photo_name,'wb');
			/* Writing to server */
			fwrite($file,$binary, strlen($binary));
			/* Closing image file */
			fclose($file);
			if(file_exists($photo_name)):
				//For creating original nails perfect size Start,.......
				if($img_properties['original']['perfect']):
					$this->_create_resized_image($image_name,$img_properties['perfect']);
				endif;
				//For creating original nails medium size Start,...
				if($img_properties['original']['medium']):
					$this->_create_resized_image($image_name,$img_properties['medium']);
				endif;

				//For creating thumb nails Start,.......
				if($img_properties['original']['thumb']):
					$this->_create_resized_image($image_name,$img_properties['thumb']);
				endif;

				//$imagefolder   = $img_properties['thumb']['path']?$img_properties['thumb']['path']:($img_properties['medium']['path']?$img_properties['medium']['path']:($img_properties['perfect']['path']?$img_properties['perfect']['path']:$img_properties['original']['path']));
				$imagefolder   = $img_properties['original']['path'];
				//return base_url().$imagefolder.$image_name;
				//return $imagefolder.$image_name;
				return str_replace(fileFCPATH,'',$imagefolder).$image_name;
			else:
				return false;
			endif;
		endif;
	}
	
	function _upload_image_from_app($file_name='',$tmp_name='',$newfilename='',$type='',$subfolder='')
	{ 
		$img_properties			=	$this->_get_image_path_by_referance_and_id($type,$subfolder);
		if(!is_array($img_properties)) die("Please Set Image Properties For Upload Path ,Allowed Types ,Max Size etc. In Array Form");

		if($newfilename):
			$file_name	 		= 	sanitizedFilename($newfilename);
		else:
			$file_name	 		= 	time().sanitizedFilename($file_name);
		endif;

		if(move_uploaded_file($tmp_name, $img_properties['original']['path'].$file_name)): 
			/*
			//For creating original nails perfect size Start,.......
			if($img_properties['original']['perfect']):
				$this->_create_resized_image($file_name,$img_properties['perfect']);
			endif;
			//For creating original nails medium size Start,...
			if($img_properties['original']['medium']):
				$this->_create_resized_image($file_name,$img_properties['medium']);
			endif;

			//For creating thumb nails Start,.......
			if($img_properties['original']['thumb']):
				$this->_create_resized_image($file_name,$img_properties['thumb']);
			endif;

			$imagefolder   = $img_properties['thumb']['path']?$img_properties['thumb']['path']:($img_properties['medium']['path']?$img_properties['medium']['path']:($img_properties['perfect']['path']?$img_properties['perfect']['path']:$img_properties['original']['path']));
			//return fileBaseUrl.str_replace(fileFCPATH,'',$imagefolder).$file_name;
			return str_replace(fileFCPATH,'',$imagefolder).$file_name;
			*/

			$imagefolder   		= 	$img_properties['original']['path'];
			//return fileBaseUrl.str_replace(fileFCPATH,'',$imagefolder).$file_name;
			return str_replace(fileFCPATH,'',$imagefolder).$file_name;
		else:
			return 'UPLODEERROR';
		endif;
	}

	function _create_resized_image($fileName,$img_properties) {
		$CI =& get_instance();
		$CI->load->library('image_lib');
		$config['image_library'] 				= 	'gd2';
		$config['source_image'] 				= 	$img_properties['source_path'].$fileName;       
		$config['create_thumb'] 				= 	TRUE;
		$config['maintain_ratio'] 				= 	TRUE;
		$config['width'] 						= 	$img_properties['max_width'];
		$config['height'] 						= 	$img_properties['max_height'];
		$config['new_image'] 					= 	$img_properties['path'].$fileName;

		$CI->image_lib->initialize($config);
		if(!$CI->image_lib->resize()):
			echo $CI->image_lib->display_errors();
		endif;
	}

	function watermarking($imagePath='')
    {
        $config['source_image'] 		= 	$imagePath;
        //The image path,which you would like to watermarking
        $config['wm_text'] 				= 	'AL KAYAN GROUP';
        $config['wm_type'] 				= 	'text';
        $config['wm_font_path'] 		= 	'./system/fonts/texb.ttf';
        $config['wm_font_size'] 		= 	16;
        $config['wm_font_color'] 		= 	'ffffff';
        $config['wm_vrt_alignment'] 	= 	'bottom';
        $config['wm_hor_alignment'] 	= 	'right';
        $config['wm_padding'] 			= 	'20';
        $this->image_lib->initialize($config);
        if (!$this->image_lib->watermark()):
            echo $this->image_lib->display_errors();
        endif;
    }

	function _delete_image($imagename='')
	{  //echo fileBaseUrl; die;
		if(!strpos($imagename,'logo.png') && !strpos($imagename,'com-soon.jpg')):

			// $thumbpath		=	str_replace(fileBaseUrl,fileFCPATH,trim($imagename));
			
			// Added By Dilip..
			$thumbpath			=	fileFCPATH.trim($imagename);
			$path = explode('/', $thumbpath);
			$index = count($path) - 2;
			$FilePath = $path[$index];
			$originalpath	=	str_replace($FilePath,$FilePath.'/thumb',$thumbpath);
			//echo $thumbpath.'---'.$originalpath; die;
			$perfectpath	=	str_replace('thumb/','perfect/',$thumbpath);
			$mediumpath		=	str_replace('thumb/','medium/',$thumbpath); 
			if(file_exists($originalpath)):
				@unlink($originalpath);
			endif;
			if(file_exists($perfectpath)):
				@unlink($perfectpath);
			endif;
			if(file_exists($mediumpath)):
				@unlink($mediumpath);
			endif;
			if(file_exists($thumbpath)):
				@unlink($thumbpath);
			endif;
		endif;
		// return true;
	}
	
	function _delete_original_image($imagename='')
	{  //echo $imagename; die;
		if(!strpos($imagename,'logo.png') && !strpos($imagename,'com-soon.jpg')):
			$imagepath		=	str_replace(fileBaseUrl,fileFCPATH,$imagename);
			$originalpath	=	str_replace('thumb/','',$imagepath);
			if(file_exists($originalpath)):
				@unlink($originalpath);
			endif;
		endif;
		return true;
	}

	function _get_image_path_by_referance_and_id($type='',$subfolder='')	
	{	
		$data	=	array();
		switch($type):
			case 'profileimage':
				$data['original']	= 	array("path"=>fileFCPATH."./assets/profileimage/","allowed_types"=>"gif|jpg|png","max_size"=>"20000","max_width"=>"","max_height"=>"", "thumb"=>TRUE);//Original
				$data['thumb']		= 	array("path"=>fileFCPATH."./assets/profileimage/thumb/","allowed_types"=>"gif|jpg|png","source_path"=>fileFCPATH."./assets/profileimage/","max_width"=>"50","max_height"=>"50");//Thumb
			break;
			case 'testimonialImage':
				$data['original']	= 	array("path"=>fileFCPATH."./assets/testimonialImage/","allowed_types"=>"gif|jpg|png","max_size"=>"20000","max_width"=>"","max_height"=>"", "thumb"=>TRUE);//Original
				$data['thumb']		= 	array("path"=>fileFCPATH."./assets/testimonialImage/thumb/","allowed_types"=>"gif|jpg|png","source_path"=>fileFCPATH."./assets/testimonialImage/","max_width"=>"50","max_height"=>"50");//Thumb
			break;
		endswitch;
		return $data;
	}
	
	function _check_directory($path='')
	{
		$patharray	=	explode('/',$path);
		$dirpath	=	"./assets";
		for($i=2; $i < count($patharray); $i++):
			$oldmask = umask(0);
			$dirpath	=	$dirpath.'/'.$patharray[$i];
			if (!file_exists(FCPATH.$dirpath)):
				@mkdir(FCPATH.$dirpath, 0775, true);
				umask($oldmask);
			endif;
		endfor;
		return true;
	}
}
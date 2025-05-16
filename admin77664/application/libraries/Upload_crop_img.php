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
		//print_r($img_properties); die();
		if(!is_array($img_properties)) die("Please Set Image Properties For Upload Path ,Allowed Types ,Max Size etc. In Array Form");

		if($newfilename):
			$file_name	 		= 	$newfilename;
		else:
			$file_name	 		= 	time().sanitizedFilename($file_name);
		endif;

		if(move_uploaded_file($tmp_name, $img_properties['original']['path'].$file_name)): 

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
			case 'profileImage':
				$data['original']	= 	array("path"=>fileFCPATH."./assets/profileImage/","allowed_types"=>"gif|jpg|png","max_size"=>"20000","max_width"=>"","max_height"=>"", "thumb"=>TRUE);//Original
				$data['thumb']		= 	array("path"=>fileFCPATH."./assets/profileImage/thumb/","allowed_types"=>"gif|jpg|png","source_path"=>fileFCPATH."./assets/profileImage/","max_width"=>"50","max_height"=>"50");//Thumb
				//$this->_check_directory($data['thumb']['path']);
			break;

			case 'lottoproductsImage':
				$data['original']	= 	array("path"=>fileFCPATH."./assets/lottoproductsImage/","allowed_types"=>"gif|jpg|png","max_size"=>"20000","max_width"=>"","max_height"=>"", "thumb"=>TRUE);//Original
				$data['thumb']		= 	array("path"=>fileFCPATH."./assets/lottoproductsImage/thumb/","allowed_types"=>"gif|jpg|png","source_path"=>fileFCPATH."./assets/lottoproductsImage/","max_width"=>"50","max_height"=>"50");//Thumb
				//$this->_check_directory($data['thumb']['path']);
			break;

			case 'lottoprizeImage':
				$data['original']	= 	array("path"=>fileFCPATH."./assets/lottoprizeImage/","allowed_types"=>"gif|jpg|png","max_size"=>"20000","max_width"=>"","max_height"=>"", "thumb"=>TRUE);//Original
				$data['thumb']		= 	array("path"=>fileFCPATH."./assets/lottoprizeImage/thumb/","allowed_types"=>"gif|jpg|png","source_path"=>fileFCPATH."./assets/lottoprizeImage/","max_width"=>"50","max_height"=>"50");//Thumb
				//$this->_check_directory($data['thumb']['path']);
			break;
			
			case 'homepageSliderImage':
				$data['original']	= 	array("path"=>fileFCPATH."./assets/homepageSliderImage/","allowed_types"=>"gif|jpg|png","max_size"=>"20000","max_width"=>"","max_height"=>"", "thumb"=>TRUE);//Original
				$data['thumb']		= 	array("path"=>fileFCPATH."./assets/homepageSliderImage/thumb/","allowed_types"=>"gif|jpg|png","source_path"=>fileFCPATH."./assets/homepageSliderImage/","max_width"=>"50","max_height"=>"50");//Thumb
				//$this->_check_directory($data['thumb']['path']);
			break;
			case 'productsImage':
				$data['original']	= 	array("path"=>fileFCPATH."./assets/productsImage/","allowed_types"=>"gif|jpg|png","max_size"=>"20000","max_width"=>"","max_height"=>"", "thumb"=>TRUE);//Original
				$data['thumb']		= 	array("path"=>fileFCPATH."./assets/productsImage/thumb/","allowed_types"=>"gif|jpg|png","source_path"=>fileFCPATH."./assets/productsImage/","max_width"=>"50","max_height"=>"50");//Thumb
				//$this->_check_directory($data['thumb']['path']);
			break;
			case 'categoryImage':
				$data['original']	= 	array("path"=>fileFCPATH."./assets/categoryImage/","allowed_types"=>"gif|jpg|png","max_size"=>"20000","max_width"=>"","max_height"=>"", "thumb"=>TRUE);//Original
				$data['thumb']		= 	array("path"=>fileFCPATH."./assets/categoryImage/thumb/","allowed_types"=>"gif|jpg|png","source_path"=>fileFCPATH."./assets/categoryImage/","max_width"=>"50","max_height"=>"50");//Thumb
				//$this->_check_directory($data['thumb']['path']);
			break;
			case 'subCategoryImage':
				$data['original']	= 	array("path"=>fileFCPATH."./assets/subCategoryImage/","allowed_types"=>"gif|jpg|png","max_size"=>"20000","max_width"=>"","max_height"=>"", "thumb"=>TRUE);//Original
				$data['thumb']		= 	array("path"=>fileFCPATH."./assets/subCategoryImage/thumb/","allowed_types"=>"gif|jpg|png","source_path"=>fileFCPATH."./assets/subCategoryImage/","max_width"=>"50","max_height"=>"50");//Thumb
				//$this->_check_directory($data['thumb']['path']);
			break;

			case 'prizeImage':
				$data['original']	= 	array("path"=>fileFCPATH."./assets/prizeImage/","allowed_types"=>"gif|jpg|png","max_size"=>"20000","max_width"=>"","max_height"=>"", "thumb"=>TRUE);//Original
				$data['thumb']		= 	array("path"=>fileFCPATH."./assets/prizeImage/thumb/","allowed_types"=>"gif|jpg|png","source_path"=>fileFCPATH."./assets/prizeImage/","max_width"=>"50","max_height"=>"50");//Thumb
				//$this->_check_directory($data['thumb']['path']);
			break;
			case 'couponsImage':
				$data['original']	= 	array("path"=>fileFCPATH."./assets/couponsImage/","allowed_types"=>"gif|jpg|png","max_size"=>"20000","max_width"=>"","max_height"=>"", "thumb"=>TRUE);//Original
				$data['thumb']		= 	array("path"=>fileFCPATH."./assets/couponsImage/thumb/","allowed_types"=>"gif|jpg|png","source_path"=>fileFCPATH."./assets/couponsImage/","max_width"=>"50","max_height"=>"50");//Thumb
				//$this->_check_directory($data['thumb']['path']);
			break;

			case 'winnersImage':
				$data['original']	= 	array("path"=>fileFCPATH."./assets/winnersImage/","allowed_types"=>"gif|jpg|png","max_size"=>"20000","max_width"=>"","max_height"=>"", "thumb"=>TRUE);//Original
				$data['thumb']		= 	array("path"=>fileFCPATH."./assets/winnersImage/thumb/","allowed_types"=>"gif|jpg|png","source_path"=>fileFCPATH."./assets/winnersImage/","max_width"=>"50","max_height"=>"50");//Thumb
				//$this->_check_directory($data['thumb']['path']);
			break;

			

			case 'propertySideImage':
				$data['original']	= 	array("path"=>fileFCPATH."./assets/propertySideImage/","allowed_types"=>"gif|jpg|png","max_size"=>"20000","max_width"=>"","max_height"=>"", "thumb"=>TRUE);//Original
				$data['thumb']		= 	array("path"=>fileFCPATH."./assets/propertySideImage/thumb/","allowed_types"=>"gif|jpg|png","source_path"=>fileFCPATH."./assets/propertySideImage/","max_width"=>"50","max_height"=>"50");//Thumb
				//$this->_check_directory($data['thumb']['path']);
			break;
			case 'aboutusimage':
				$data['original']	= 	array("path"=>fileFCPATH."./assets/aboutusimage/","allowed_types"=>"gif|jpg|png","max_size"=>"20000","max_width"=>"","max_height"=>"", "thumb"=>TRUE);//Original
				$data['thumb']		= 	array("path"=>fileFCPATH."./assets/aboutusimage/thumb/","allowed_types"=>"gif|jpg|png","source_path"=>fileFCPATH."./assets/aboutusimage/","max_width"=>"50","max_height"=>"50");//Thumb
				//$this->_check_directory($data['thumb']['path']);
			break;
			case 'generaldata':
				$data['original']	= 	array("path"=>fileFCPATH."./assets/generaldata/","allowed_types"=>"gif|jpg|png","max_size"=>"20000","max_width"=>"","max_height"=>"", "thumb"=>TRUE);//Original
				$data['thumb']		= 	array("path"=>fileFCPATH."./assets/generaldata/thumb/","allowed_types"=>"gif|jpg|png","source_path"=>fileFCPATH."./assets/generaldata/","max_width"=>"50","max_height"=>"50");//Thumb
				//$this->_check_directory($data['thumb']['path']);
			break;
			case 'notifications':
				$data['original']	= 	array("path"=>fileFCPATH."./assets/notifications/","allowed_types"=>"gif|jpg|png","max_size"=>"20000","max_width"=>"","max_height"=>"", "thumb"=>TRUE);//Original
				// $data['thumb']		= 	array("path"=>fileFCPATH."./assets/notifications/thumb/","allowed_types"=>"gif|jpg|png","source_path"=>fileFCPATH."./assets/notifications/","max_width"=>"50","max_height"=>"50");//Thumb
				//$this->_check_directory($data['thumb']['path']);
			break;
			case 'testimonialImage':
				$data['original']	= 	array("path"=>fileFCPATH."./assets/testimonialImage/","allowed_types"=>"gif|jpg|png","max_size"=>"20000","max_width"=>"","max_height"=>"", "thumb"=>TRUE);//Original
				$data['thumb']		= 	array("path"=>fileFCPATH."./assets/testimonialImage/thumb/","allowed_types"=>"gif|jpg|png","source_path"=>fileFCPATH."./assets/testimonialImage/","max_width"=>"50","max_height"=>"50");//Thumb
				//$this->_check_directory($data['thumb']['path']);
			break;

			case 'uwin-winnerImage':
				$data['original']	= 	array("path"=>fileFCPATH."./assets/uwin-winnerImage/","allowed_types"=>"gif|jpg|png","max_size"=>"20000","max_width"=>"","max_height"=>"", "thumb"=>TRUE);//Original
				$data['thumb']		= 	array("path"=>fileFCPATH."./assets/uwin-winnerImage/thumb/","allowed_types"=>"gif|jpg|png","source_path"=>fileFCPATH."./assets/uwin-winnerImage/","max_width"=>"50","max_height"=>"50");//Thumb
				//$this->_check_directory($data['thumb']['path']);
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
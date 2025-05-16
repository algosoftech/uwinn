<?php
/*
* Get url segment for pagination
*/
if (!function_exists('getUrlSegment')) {
	function getUrlSegment(){
		/////////////   Localhost 		/////////////////
		if($_SERVER['SERVER_NAME']=='localhost'):
			$urlSegment	=	4;
		else:
			$urlSegment	=	4;
		endif;
		return $urlSegment;
	}
}

/*
* Get current base path
*/
if (!function_exists('getCurrentBasePath')) {
	function getCurrentBasePath(){
		return base_url();
	}
}

/*
* Get current base path
*/
if (!function_exists('getCurrentControllerPath')) {
	function getCurrentControllerPath($postfixUrl=''){
		$CI =& get_instance();
		$functionArray 				=	array('index','addeditdata','deletedata','changestatus','imageUpload','imageDelete','deleteContent','memberDelete','viewdata','changedatastatus','getdatabyajax'); 
		$baseUrl 					=	getCurrentBasePath();
		if(in_array($CI->uri->segment(4),$functionArray)):  
		    $baseUrl 				=	$baseUrl.$CI->uri->segment(1).'/'.$CI->uri->segment(2).'/'.$CI->uri->segment(3).'/'.$postfixUrl;
		elseif(in_array($CI->uri->segment(3),$functionArray)):	
			$baseUrl 				=	$baseUrl.$CI->uri->segment(1).'/'.$CI->uri->segment(2).'/'.$postfixUrl;
		else:		
			$baseUrl 				=	$baseUrl.$postfixUrl;
		endif; 
		return $baseUrl;
	}
}

/*
* Get current dashboard base path
*/
if (!function_exists('getCurrentDashboardPath')) {
	function getCurrentDashboardPath($postfixUrl=''){
		$CI =& get_instance();
		$baseUrl 					=	getCurrentBasePath();
		$baseUrl 					=	$baseUrl.$CI->uri->segment(1).'/'.$postfixUrl;
		return $baseUrl;
	}
}

/*
 * show status
 */
if (!function_exists('showStatus')) {
	function showStatus($text='') {
		$statusArray	=	array('A'=>'<label class="badge badge-light-success">'.lang('active').'</label>',
								  'I'=>'<label class="badge badge-light-danger">'.lang('inactive').'</label>',
								  'B'=>'<label class="badge badge-light-danger">'.lang('block').'</label>',
								  'D'=>'<label class="badge badge-light-danger">'.lang('deleted').'</label>',
								  'Y'=>'<label class="badge badge-light-success">'.lang('active').'</label>',
								  'N'=>'<label class="badge badge-light-danger">'.lang('inactive').'</label>',
								  'R'=>'<label class="badge badge-light-danger">'.lang('inactive').'</label>');
								  
		return $statusArray[$text];
	}
}

/*
 * show payment status
 */
if (!function_exists('showOtherStatus')) {
	function showOtherStatus($text='') {
		$statusArray	=	array('COD'=>'<label class="badge badge-light-success">'.lang('cod').'</label>',
								  'Razorpay'=>'<label class="badge badge-light-success">'.lang('razerpay').'</label>',
								  'PTM'=>'<label class="badge badge-light-success">'.lang('paytm').'</label>',
								  'A'=>'<label class="badge badge-light-success">'.lang('accept').'</label>',
								  'R'=>'<label class="badge badge-light-danger">'.lang('reject').'</label>',
								  'D'=>'<label class="badge badge-light-success">'.lang('done').'</label>',
								  'N'=>'<label class="badge badge-light-danger">'.lang('notdone').'</label>',
								  'C'=>'<label class="badge badge-light-danger">'.lang('cancelled').'</label>',
								  'S'=>'<label class="badge badge-light-success">'.lang('success').'</label>',
								  'PSN'=>'<label class="badge badge-light-success">'.lang('PSN').'</label>',
								  'PSY'=>'<label class="badge badge-light-danger">'.lang('PSY').'</label>',
								  'OPN'=>'<label class="badge badge-light-success">'.lang('OPN').'</label>',
								  'OPY'=>'<label class="badge badge-light-danger">'.lang('OPY').'</label>',
								  'OAN'=>'<label class="badge badge-light-danger">'.lang('OAN').'</label>',
								  'SH'=>'<label class="badge badge-light-success">'.lang('SH').'</label>',
								  'DD'=>'<label class="badge badge-light-success">'.lang('DD').'</label>',
								  'OAY'=>'<label class="badge badge-light-success">'.lang('OAY').'</label>'
								  );
		return $statusArray[$text];
	}
}

/*
 * show payment status
 */
if (!function_exists('showPaymentStatus')) {
	function showPaymentStatus($text='') {
		$statusArray	=	array('N'=>'<label class="badge badge-light-danger">'.lang('pending').'</label>',
								  'Y'=>'<label class="badge badge-light-success">'.lang('released').'</label>'
								  );
		return $statusArray[$text];
	}
}

/*
 * show payment status
 */
if (!function_exists('showAcountVerifiedStatus')) {
	function showAcountVerifiedStatus($text='') {
		$statusArray	=	array('N'=>'<label class="badge badge-light-danger">'.lang('account_pending').'</label>',
								  'Y'=>'<label class="badge badge-light-success">'.lang('account_verified').'</label>'
								  );
		return $statusArray[$text];
	}
}

/*
 * show payment status
 */
if (!function_exists('showPropertyStatus')) {
	function showPropertyStatus($text='') {
		$statusArray	=	array('p'=>'<label class="badge badge-light-danger">'.lang('property_pending').'</label>',
								  'IV'=>'<label class="badge badge-light-success">'.lang('inspector_verify').'</label>',
								  'IR'=>'<label class="badge badge-light-success">'.lang('inspector_reject').'</label>',
								  'AV'=>'<label class="badge badge-light-success">'.lang('admin_verify').'</label>',
								  'AR'=>'<label class="badge badge-light-success">'.lang('admin_reject').'</label>'
								  );
		return $statusArray[$text];
	}
}

/*
 * show payment status
 */
if (!function_exists('showPaymentStatusSimple')) {
	function showPaymentStatusSimple($text='') {
		$statusArray	=	array('0'=>lang('initialize'),
								  '1'=>lang('success'),
								  '2'=>lang('cancel'),
								  '3'=>lang('error'),
								  '4'=>lang('refund'));
		return $statusArray[$text];
	}
}
/*
 * show Aprrove Status
 */
if (!function_exists('showApproveStatus')) {
	function showApproveStatus($text='') {
		$statusArray	=	array('A'=>'<label class="badge badge-light-success">'.lang('publish').'</label>',
								  'I'=>'<label class="badge badge-light-danger">'.lang('unpublish').'</label>',
								  'D'=>'<label class="badge badge-light-danger">'.lang('reject').'</label>');
		return $statusArray[$text];
	}
}

/*
 * show Aprrove Status
 */
if (!function_exists('showAccountActiveStatus')) {
	function showAccountActiveStatus($text='') {
		$statusArray	=	array('1'=>'<label class="badge badge-light-success">'.lang('login').'</label>',
								  '0'=>'<label class="badge badge-light-danger">'.lang('logout').'</label>');
		return $statusArray[$text];
	}
}

if(!function_exists('Pagination')):
	function Pagination($baseUrl='',$page='',$totalPages='' ,$perPage='',$totalRows=''){

		$links = array();
		$links[] = "<ul class='pagination-section'>";

			if($page >1):
			$previouspageno = $page -1;
			$links[] = "<li><a href=".$baseUrl.'/'.$previouspageno."> $previouspageno </a></li>";
			endif;
			$links[] = "<li><a class='currentpage' href=".$baseUrl.'/'.$page."> $page </a></li>";
		if($page >= 1):

			for ($i=$page; $i < $totalPages ; $i++):
				if($i <= $page+2):
					$pageno = $i+1;
					$links[] = "<li><a href=".$baseUrl.'/'.$pageno."> $pageno </a></li>";
				endif;
			endfor;
		endif;

				// $links[] = "<li><a href='javacript:void(0)'>Current</a></li>";
				$links[] = "<li><a href=".$baseUrl.'/1'."> First </a></li>";
				$links[] = "<li><a href=".$baseUrl.'/'.$totalPages."> Last </a></li>";
				$links[] = "</ul>";

		return $links;
	}
endif;


/*
* Generate admin Pagination
*/
if (!function_exists('adminPagination')) {
	function adminPagination($url='',$suffix='',$rowsCount='',$perPage='',$uriSegment='') {
		$ci = & get_instance();
		$ci->load->library('pagination');
		
		$config = array();
		$config["base_url"] 		= 	$url;
		$config['suffix'] 			= 	$suffix;
		$config["total_rows"] 		= 	$rowsCount;
		$config["per_page"] 		= 	$perPage;
		$config["uri_segment"] 		= 	$uriSegment;
		$config['full_tag_open'] 	= 	'<ul class="pagination">';
		$config['full_tag_close'] 	= 	'</ul>';
		$config['num_tag_open'] 	= 	'<li class="paginate_button page-item">';
		$config['num_tag_close'] 	= 	'</li>';
		$config['cur_tag_open'] 	= 	'<li class="paginate_button page-item active"><a href="javascript:void(0);" class="page-link">';
		$config['cur_tag_close'] 	= 	'</a></li>';
		
		$config['next_link'] 		= 	'Next';
		$config['next_tag_open'] 	= 	'<li class="paginate_button page-item next">';
		$config['next_tag_close'] 	= 	'</li>';
		$config['prev_tag_open'] 	= 	'<li class="paginate_button page-item previous">';
		$config['prev_link'] 		= 	'Previous';
		$config['prev_tag_close'] 	= 	'</li>';
		
		$config['first_link'] 		= 	'First';
		$config['first_tag_open'] 	= 	'<li class="paginate_button page-item previous">';
		$config['first_tag_close'] 	= 	'</li>';
		
		$config['last_link'] 		= 	'Last';
		$config['last_tag_open'] 	= 	'<li class="paginate_button page-item next">';
		$config['last_tag_close'] 	= 	'</li>';
		
		$ci->pagination->initialize($config);
		return $ci->pagination->create_links();
	}
}

if (!function_exists('sanitizedFieldName')) {
	function sanitizedFieldName($filename){
		$sanitized = preg_replace('/[^a-zA-Z0-9-_\.]/','', $filename);
		return $sanitized;
	}
}
	
/*
 * check Remote File
 */
if (!function_exists('checkRemoteFile')) {
	function checkRemoteFile($url='')
	{
		if($url):
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_NOBODY, 1);
			curl_setopt($ch, CURLOPT_FAILONERROR, 1);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			if(curl_exec($ch)!==FALSE):
				return true;
			else:
				return false;
			endif;
		else:
			return false;
		endif;
	}
}	

/*
 * Show correct image
 */
if (!function_exists('showCorrectImage')) {
	function showCorrectImage($imageUrl='', $type = '',$showType='') {
		if(checkRemoteFile($imageUrl)): 
			if($type=='original'):
				$imageUrl = str_replace('/thumb','',$imageUrl);
			elseif($type):
				$imageUrl = str_replace('thumb',$type,$imageUrl);
			endif;
		else:	
			if($showType == 'profile'):
				if($type=='original'):
					$imageUrl = base_url().'assets/admin/image/profile.png';
				elseif($type=='thumb'):
					$imageUrl = base_url().'assets/admin/image/profile.png';
				endif;
			endif;
		endif;
		return trim($imageUrl);
	}
}

/*
 * pages list
 */
if (!function_exists('pagesListData')) {
	function pagesListData() {
		$pagesArray			=	array('homepage'				=>	"Home",
								  	  'aboutus'					=>	"About Us",
								  	  'contactus'				=>	"Contact Us",
								  	  'faq'						=>	"FAQ",
								  	  'privacypolicy'			=>	"Privacy Policy",
								  	  'termsconditions'			=>	"Terms & Conditions");
		return $pagesArray;
	}
}             

/*
 * show pages data
 */
if (!function_exists('showPagesData')) {
	function showPagesData($type='') {
		$showArray			=	array('homepage'				=>	"Home",
								  	  'aboutus'					=>	"About Us",
								  	  'faq'						=>	"FAQ",
								  	  'privacypolicy'			=>	"Privacy Policy",
								  	  'contactus'				=>	"Contact Us",
								  	  'termsconditions'			=>	"Terms & Conditions");
		return $showArray[$type];
	}
}  


if (!function_exists('getDateDifference')) {
	function getDateDifference($drawdate='') {
			 
			$CurrentDate = date('d-m-Y h:i');

			$date1=date_create($CurrentDate);
			$date2=date_create($drawdate);

			$diff=date_diff($date1,$date2);
			
			$DrawDatetstatus =  $diff->format("%R%a days %H Hours %M Minutes");

			$word = "-";
			$position = strpos($DrawDatetstatus, $word);
			if ($position !== false) {
				return "Draw date is finished";
			} else {
				return $DrawDatetstatus . ' left from Draw Date' ;
			}
	}
}  
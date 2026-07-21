<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
//echo $_SERVER['SERVER_NAME'];
$route['default_controller'] 	= 	'welcome/index';
$route['404_override'] 			= 	'';
$route['translate_uri_dashes'] 	= 	FALSE;

//$curUrl						=	explode('/',$_SERVER['REQUEST_URI']);  print_r($curUrl); die;
$curUrl						=	strpos($_SERVER['REQUEST_URI'],'/?')?explode('/?',$_SERVER['REQUEST_URI']):explode('?',$_SERVER['REQUEST_URI']); 
$curUrl						=	explode('/',$curUrl[0]); //print_r($curUrl); die;
/////////////   Localhost 		/////////////////
if($_SERVER['SERVER_NAME']=='localhost'):
	$firstSlug				=	isset($curUrl[3])?$curUrl[3]:'';
	$secondSlug				=	isset($curUrl[4])?$curUrl[4]:'';
	$thirdSlug				=	isset($curUrl[5])?$curUrl[5]:'';
	$fourthlug				=	isset($curUrl[6])?$curUrl[6]:'';
	$extractData 			=	'/admin77664/';

/////////////   SERVER		/////////////////	
else: 
	$firstSlug				=	isset($curUrl[2])?$curUrl[2]:'';
	$secondSlug				=	isset($curUrl[3])?$curUrl[3]:'';
	$thirdSlug				=	isset($curUrl[4])?$curUrl[4]:'';
	$fourthlug				=	isset($curUrl[5])?$curUrl[5]:'';
	$extractData 			=	'/admin77664/';
endif;
$functionArray 				=	array('getsubcategoryData','exportexcel','videoDelete','index','addeditdata','addprize','deletedata','changestatus','imageUpload','imageDelete','deleteContent','memberDelete','viewdata','changedatastatus','getdatabyajax','getCityData','getStatisticsByUserID','registrationListByEmail','getmaratArea','checkRetailer','users_list','exportAllUsers','getTicketData','getCampaignSalesData','getSponsoredData','getRefferalData','getSignupBonusData','getMembershipData','getRechargeData','checkDeplicacy','generatecoupons','userdetails','addOption','upload_subwinners','settings','subwinner','imagePrizeDelete','generatePosNumber','checkpreview','uploadVoucher','exportexcelApi','changestatusByorderID','checkInactivepreview','rejectrequest','adminRecharges','getnotificationuser','multiplechangestatus','getUserTicketList','getUserActiveTicketList','getUserNotificationList','getTopupsVouchersList','deleteOldNotificationsData','getbindwith','getUsers','uploadVoucher','manageDrawTime','deleteDrawTime','redeeminglimits','redeeminglimit','reverseAmount','updatequickuser','changeRechargeCouponRedeemstatus','rtp');

if($firstSlug == 'login'):  
	$route['login'] 											= 	'login/index';
elseif($firstSlug == 'login-verify-otp'):  
	$route['login-verify-otp'] 									= 	'login/loginverifyotp';
elseif($firstSlug == 'resend-otp'):  
	$route['resend-otp'] 										= 	'login/resendotp';
	
	elseif($firstSlug == 'resend-pin'):  
		$route['resend-pin'] 										= 	'login/resendpin';
elseif($firstSlug == 'forgot-password'):  
	$route['forgot-password'] 									= 	'login/forgotpassword';
	
	elseif($firstSlug == 'forgot-pin'):  
		$route['forgot-pin'] 									= 	'login/forgotpin';
			elseif($firstSlug == 'pin-recover'):  
		$route['pin-recover'] 									= 	'login/pinrecover';
	
elseif($firstSlug == 'password-recover'):  
	$route['password-recover'] 									= 	'login/passwordrecover';
elseif($firstSlug == 'logout'):  
	$route['logout'] 											= 	'login/logout';
elseif($firstSlug == 'maindashboard'):  
	$route['maindashboard'] 									= 	'account/maindashboard';
elseif($firstSlug == 'dashboard'):  
	$route['dashboard'] 										= 	'account/dashboard';
elseif($firstSlug == 'profile'):  		
	$route['profile'] 											= 	'account/profile';
elseif($firstSlug == 'editprofile'):  
	$route['editprofile'] 										= 	'account/editprofile';
	$route['editprofile/(:any)'] 								= 	'account/editprofile/$1';
elseif($firstSlug == 'change-password'):  
	$route['change-password'] 									= 	'account/changepassword';
	$route['change-password/(:any)'] 							= 	'account/changepassword/$1';
elseif($firstSlug == 'change-password-verify-otp'):  
	$route['change-password-verify-otp'] 						= 	'account/changepasswordverifyotp';

elseif($firstSlug == 'winners'):  
	$route['add-winner'] 										= 	'winners/allwinners/addeditdata';
elseif($firstSlug == 'users' && $secondSlug == 'activity-dashboard'):  
	$route['users/activity-dashboard/(:any)'] = 	'users/UserDashboard/index/$1';


elseif($firstSlug == 'users' && $secondSlug == 'get-user-ticket-list'):  
	$route['users/get-user-ticket-list'] = 	'users/UserDashboard/getUserTicketList';

elseif($firstSlug == 'users' && $secondSlug == 'get-user-active-ticket-list'):  
	$route['users/get-user-active-ticket-list'] = 	'users/UserDashboard/getUserActiveTicketList';


elseif($firstSlug == 'users' && $secondSlug == 'get-user-winner-list'):  
	$route['users/get-user-winner-list'] = 	'users/UserDashboard/getUserWinnerList';

elseif($firstSlug == 'users' && $secondSlug == 'get-user-notification-list'):  
	$route['users/get-user-notification-list'] = 	'users/UserDashboard/getUserNotificationList';

elseif($firstSlug == 'users' && $secondSlug == 'get-user-voucher-topups-list'):  
	$route['users/get-user-voucher-topups-list'] = 	'users/UserDashboard/getTopupsVouchersList';

elseif($secondSlug == 'allsatickets'):  
	$route['index'] 											= 	'tickets/allsatickets/index';
elseif($firstSlug == 'tickets'):  
	$route['add-ticket'] 										= 	'tickets/alltickets/addeditdata';
elseif($secondSlug == 'subwinner'):  
	$route['subwinner'] 										= 	'draws/subwinner';
elseif($secondSlug == 'allinventory'):  
	$route['emirate/allinventory/addeditdata'] 					= 	'emirate/allinventory/addeditdata';
else: 
	// $mngConf		= 	new MongoDB\Driver\Manager("mongodb://192.168.1.7:27017");
	$mngConf		= 	new MongoDB\Driver\Manager("mongodb://localhost:27017");
	if(in_array($fourthlug,$functionArray)):
	    $filter 	= 	['module_name'=>$firstSlug,'first_data.module_name'=>$secondSlug,'first_data.second_data.module_name'=>$thirdSlug]; 
	elseif(in_array($thirdSlug,$functionArray)):	
		$filter 	= 	['module_name'=>$firstSlug,'first_data.module_name'=>$secondSlug];
	else:	
		 $filter 	= 	['module_name'=>'urlerror'];
	endif;
    $queryConf	= 	new MongoDB\Driver\Query($filter);     
    $resConf	= 	$mngConf->executeQuery("uwin_db.uw_admin_module", $queryConf);
    $resData	= 	current($resConf->toArray()); 
    if($resData):  
		$newCurUrl				=	explode($extractData,$_SERVER['REQUEST_URI']);
		$classFunction			=	isset($newCurUrl[1])?$newCurUrl[1]:'';
		$classFunction			=	strpos($classFunction,'/?')?explode('/?',$classFunction):explode('?',$classFunction);
		if($classFunction[0]):	 
			if(in_array($fourthlug,$functionArray)):	 
				$route[$classFunction[0]] 						= 	str_replace($secondSlug.'/','',$classFunction[0]); 
			else:	
				$route[$classFunction[0]] 						= 	$classFunction[0]; 
			endif;
		endif;
	else:
		$route['campaignsales/sales/index'] 					= 	'campaignsales/sales/index';
		$route['campaignsales/sponsored/index'] 				= 	'campaignsales/sponsored/index';

		$route['campaignsales/referralreport/index'] 			= 	'campaignsales/referralreport/index';
		$route['campaignsales/signupbonus/index'] 				= 	'campaignsales/signupbonus/index';
		$route['campaignsales/referralreport/exportexcel'] 		= 	'campaignsales/referralreport/exportexcel';
		
		// $route['campaignsales/sales/exportexcel'] 				= 	'campaignsales/sales/exportexcel';
		
		$route['campaignsales/signupbonus/exportexcel'] 		= 	'campaignsales/signupbonus/exportexcel';

		$route['cashback/membershipcashback/index'] 			= 	'cashback/membershipcashback/index';
		$route['cashback/membershipcashback/exportexcel'] 		= 	'cashback/membershipcashback/exportexcel';
		

		$route['recharge/allrechargevoucher/viewredeemcoupons'] = 	'recharge/allrechargevoucher/viewredeemcoupons';

		$route[$firstSlug.'/'.$secondSlug.'/(:any)']			= 	'account/maindashboard';
	endif;
endif;
// echo '<pre>';  print_r($route); die;

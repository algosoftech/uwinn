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

$route['default_controller'] 					= 	'home/index';
$route['404_override'] 							= 	'';
$route['translate_uri_dashes'] 					= 	FALSE;


$route['api/getCountryCode'] 									= 	'api/common/getCountryCode';
$route['api/getCountryList'] 									= 	'api/common/getCountryList';
$route['api/signup'] 											= 	'api/users/signup';
$route['api/login'] 											= 	'api/users/login';
$route['api/login_New'] 										= 	'api/users/login_New';
$route['api/verify_otp'] 										= 	'api/users/verifyOTP';

$route['app/btc/login'] 										=   'app/v1/btc/login';
$route['app/btc/verify_otp'] 									= 	'app/v1/btc/verifyOTP';

$route['api/forgotPassword'] 									= 	'api/users/forgotPassword';
$route['api/resetPassword'] 									= 	'api/users/resetPassword';
$route['api/getProfileData'] 									= 	'api/users/getProfileData';
$route['api/updateProfile'] 									= 	'api/users/updateProfile';
$route['api/changePassword'] 									= 	'api/users/changePassword';

$route['api/send-otp-mobile-email'] 							=   'api/users/sendotpOTPMobileEmail';
$route['api/update-mobile-email'] 								=   'api/users/updateMobileEmail';
$route['api/verifyaccount'] 									= 	'api/users/verifyaccount';
$route['api/deleteAccount'] 									= 	'api/users/deleteAccount';


$route['api/rsendotp'] 									        = 	'api/users/rsendotp';
$route['api/refreshPoint'] 										= 	'api/users/refreshPoint';
$route['api/checkEmail'] 										= 	'api/users/checkEmail';
$route['api/checkMobile'] 										= 	'api/users/checkMobile';
$route['api/getgeneralinfo'] 									= 	'api/common/getGeneralInfo';
$route['api/getMembershipDetails'] 								= 	'api/users/getMembershipDetails';
$route['api/update-network-usage'] 								= 	'api/users/updateNetworkUsage';

// API Lists
$route['api/getreferralcode']									=   'api/uwinn/getReferralcode';
$route['api/getLottoProductListPageData'] 						= 	'api/uwinn/getProductListPageData';
$route['api/lotto/paymentCapture'] 								= 	'api/uwinn/paymentCapture';
$route['api/lotto/auto-printed'] 								= 	'api/uwinn/isPrinted';
$route['api/lotto/orderHistory'] 								= 	'api/uwinn/orderHistory';
$route['api/lotto/SummaryReportSearch'] 						= 	'api/uwinn/SummaryReportSearch';
$route['api/lotto/newSummaryReportSearch'] 						= 	'api/uwinn/newSummaryReportSearch';
$route['api/lotto/getWinner'] 									= 	'api/uwinn/getWinner';
$route['api/lotto/product-settings'] 							= 	'api/uwinn/productSettings';
$route['api/lotto/winner-testimonial'] 							= 	'api/uwinn/winnerTestimonial';
$route['api/lotto/check-winner'] 								= 	'api/uwinn/checkWinner';
$route['api/lotto/new-check-winner'] 							= 	'api/uwinn/checkWinner_new';


$route['api/lotto/redeem-by-mode'] 								= 	'api/uwinn/redeemByMode';

$route['api/lotto/uwin-allowed-user']							= 	'api/uwinn/uwinAllowedUser';
// $route['api/uwinn/cancel-order-request'] 						= 	'api/uwinn/cancelOrderRequest';
$route['api/uwinn/campaign-freezing'] 							= 	'api/uwinn/campaignFreezing';
$route['api/uwinn/check-single-campaign-freezing'] 				= 	'api/uwinn/checkSingleCampaignFreezing';
$route['api/uwinn/add-summery-report-queue'] 					= 	'api/uwinn/AddSummeryReportqueue';


// $route['lotto/order/(:any)'] 								= 	'pdf/getlottoOrder/$1';
$route['uwin-download-invoice/(:any)'] 							= 	'pdf/download_uwin_invoice/$1';
$route['api/uwinn/total-sales'] 								= 	'api/uwinn/totalSalesReports';

$route['api/uwinn/check-relogin'] 								= 	'api/uwinn/checkRelogin';
// echo "<pre>";print_r($route);die();

//  Third Party APIs  start 
$route['api/v1/uwinn/getCountryCode'] 									= 	'v1/api/common/getCountryCode';
$route['api/v1/uwinn/getCountryList'] 									= 	'v1/api/common/getCountryList';
$route['api/v1/uwinn/signup'] 											= 	'v1/api/users/signup';
$route['api/v1/uwinn/login'] 											= 	'v1/api/users/login';
$route['api/v1/uwinn/forgotPassword'] 									= 	'v1/api/users/forgotPassword';
$route['api/v1/uwinn/resetPassword'] 									= 	'v1/api/users/resetPassword';
$route['api/v1/uwinn/getProfileData'] 									= 	'v1/api/users/getProfileData';
$route['api/v1/uwinn/updateProfile'] 									= 	'v1/api/users/updateProfile';
$route['api/v1/uwinn/changePassword'] 									= 	'v1/api/users/changePassword';
$route['api/v1/uwinn/verifyaccount'] 									= 	'v1/api/users/verifyaccount';
$route['api/v1/uwinn/deleteAccount'] 									= 	'api/uv1/sers/deleteAccount';
$route['api/v1/uwinn/rsendotp'] 									    = 	'v1/api/users/rsendotp';
$route['api/v1/uwinn/refreshPoint'] 									= 	'v1/api/users/refreshPoint';
$route['api/v1/uwinn/checkEmail'] 										= 	'v1/api/users/checkEmail';
$route['api/v1/uwinn/checkMobile'] 										= 	'v1/api/users/checkMobile';
$route['api/v1/uwinn/getgeneralinfo'] 									= 	'v1/api/common/getGeneralInfo';
$route['api/v1/uwinn/getMembershipDetails'] 							= 	'v1/api/users/getMembershipDetails';

// API Lists
$route['api/v1/uwinn/getLottoProductListPageData'] 				      	= 'v1/api/uwinn/getProductListPageData';
$route['api/v1/uwinn/paymentCapture'] 								    = 'v1/api/uwinn/paymentCapture';
$route['api/v1/uwinn/getLottoProductListPageDataTest'] 				    = 'v1/api/uwinn/getProductListPageData_test';
$route['api/v1/uwinn/paymentCaptureTest'] 								= 'v1/api/uwinn/paymentCapture_test';
$route['api/v1/uwinn/orderHistory'] 								    = 'v1/api/uwinn/orderHistory';
$route['api/v1/uwinn/summaryReportSearch'] 								= 'v1/api/uwinn/summaryReportSearch';
$route['api/v1/uwinn/getWinner'] 										= 'api/v1uwinn/getWinner';
$route['api/v1/uwinn/product-settings'] 								= 'v1/api/uwinn/productSettings';
$route['api/v1/uwinn/winner-testimonial'] 								= 'v1/api/uwinn/winnerTestimonial';
$route['api/v1/uwinn/check-winner'] 									= 'v1/api/uwinn/checkWinner';
$route['api/v1/uwinn/redeem-by-mode'] 									= 'v1/api/uwinn/redeemByMode';
$route['api/v1/uwinn/uwin-allowed-user']								= 'v1/api/uwinn/uwinAllowedUser';
$route['api/v1/uwinn/campaign-freezing'] 								= 'v1/api/uwinn/campaignFreezing';
$route['api/v1/uwinn/total-sales'] 										= 'v1/api/uwinn/totalSalesReports';
$route['api/v1/uwinn/reconcile-request'] 								= 'v1/api/uwinn/reconcileRequest';
$route['api/v1/uwinn/raffle-campaign']	 	 							= 'v1/api/uwinn/raffleCampaignList';


$route['api/v1/uwinn/recharge/create'] 			                        = 'v1/api/recharge/createRecharge';
$route['api/v1/uwinn/recharge/reverse'] 			                    = 'v1/api/recharge/reverseRecharge';
$route['api/v1/uwinn/recharge/coupon/create'] 			                = 'v1/api/recharge/generateRechargeCoupon'; // sumit 04-02-26
$route['api/v1/uwinn/recharge/coupon/list'] 			                = 'v1/api/recharge/rechargeCouponList'; // sumit 05-02-26
$route['api/v1/uwinn/recharge/coupon/cancel'] 			                = 'v1/api/recharge/cancelRechargeCoupon'; // Dilip 11-02-26

$route['api/uwinn/update-summery-pin'] 									= 'api/users/updateSummeryPin';
$route['api/uwinn/verify-summery-pin'] 									= 'api/users/verifySummeryPin';
$route['api/getdrawData']												= 'api/thiredparty/drawDataList';

$route['api/uwinn/send-summary-pin-otp']  = 'api/users/SendSummaryPinOTP';
$route['api/uwinn/verify-summary-otp']    = 'api/users/VerifySummaryPin';
/*********************************************** APP Routs Start ***************************************************/
$route['api/raffle-campaign']	 	 = 'api/raffle/campaignList';
$route['api/check-winner-type'] 	 = 'api/raffle/checkWinnerType';
$route['api/raffle-check-winner'] 	 = 'api/raffle/checkWinner';
$route['api/raffle-winner-redeem'] 	 = 'api/raffle/winnerRedeem';
$route['api/raffle-summery-report']	 = 'api/raffle/summeryReportRaffle';

/*********************************************** APP Routs End  ****************************************************/

//New routes.
/*********************************************** APP Routs Start****************************************************/
$route['api/v1/app/common/get-homepage-data'] 		= 	'app/v1/common/getHomePageData';
$route['api/v1/app/common/page-content'] 	 		= 	'app/v1/common/pageContent';
$route['api/v1/app/common/contact-us'] 		 		= 	'app/v1/common/contactUs';

// order routes..
$route['api/lotto/update-order'] 					= 'app/v1/orders/updateOrder';
$route['api/v1/app/order/initialize-orders'] 		= 'app/v1/orders/initialize_order';
$route['api/v1/app/order/paymentCapture'] 			= 'app/v1/orders/paymentCapture';
$route['api/v1/app/order/order-history'] 			= 'app/v1/orders/orderHistory';
$route['api/v1/app/order/order-cancellation'] 		= 	'app/v1/orders/orderCancellation';
$route['api/v1/app/order/transaction-history'] 		= 'app/v1/orders/transactionHistory';
$route['api/v1/app/order/winning-history'] 			= 'app/v1/orders/winningHistory';
$route['api/v1/app/order/winner-gallery'] 			= 'app/v1/orders/winnerGallery';
$route['api/v1/user-verify'] 			            = 'app/v1/btc/userVerify';

// wallet routes..
$route['api/v1/app/users/get-wallet-balance'] 		 = 'app/v1/wallets/getBalance';
$route['api/v1/app/users/transfer-winning-balance']  = 'app/v1/wallets/transferWinningBalance';
$route['api/v1/app/users/withdraw-winning-balance']  = 'app/v1/wallets/withdrawWinningBalance';
$route['api/v1/app/users/add-play-balance'] 		 = 'app/v1/wallets/addPlayBalance';
$route['api/v1/app/generate-payment-url']   		 = '/app/v1/wallets/generatePaymentUrl';

$route['api/v1/app/get-voucher-details'] 			 = '/app/v1/wallets/getVoucherDetails';
$route['api/v1/app/generate-cash-voucher'] 			 = '/app/v1/wallets/generateCashVoucher';
$route['api/v1/app/cash-voucher-history'] 			 = '/app/v1/wallets/cashVoucherHistory';
$route['api/v1/app/redeem-cash-voucher'] 			 = '/app/v1/wallets/redeemCashVoucher';
$route['api/lotto/winner-redeemed-list'] 			 = 'api/uwinn/winnerRedeemedList';

$route['api/v1/app/payment-responce'] 				 = 'app/v1/wallets/paymentResponce';
$route['api/v1/app/users/user-request'] 			 = '/app/v1/common/userRequest';

// Recharge coupon pos routes..
$route['api/pos/generate-cash-voucher'] 	= 'api/pos/generateRechargeCoupon';
$route['api/pos/cash-voucher-history'] 		= 'api/pos/rechargeCouponHistory';
$route['api/uwinn/redeem-recharge-coupon']	= 'api/pos/redeemRechargeCoupon';
$route['api/pos/cash-voucher-summery'] 	    = 'api/pos/SummeryReportCashVoucher';
$route['api/pos/new-cash-voucher-summary']  = 'api/pos/newCashVoucherSummary';

$route['api/pos/checkwinner-order-history'] = 'api/pos/checkWinnerOrderHistory';
/*********************************************** APP Routs End****************************************************/


$route['delete-request'] 					=  'users/userDeleteRequest';
$route['verify-otp'] 						=  'users/verifyUserRequestOTP';

$route['api/uwinn/faqs'] 					=  'app/v1/common/faqs';
$route['api/uwinn/privacy-policy'] 			=  'app/v1/common/privacyPolicy';
$route['api/uwinn/terms-and-conditions'] 	=  'app/v1/common/termsConditions';
$route['api/uwinn/user-agreement'] 			=  'app/v1/common/userAgreement';
$route['api/uwinn/about-us'] 				=  'app/v1/common/aboutUS';
$route['api/uwinn/how-to-play'] 			=  'app/v1/common/howToplay';
$route['api/uwinn/cancellation-policy'] 	=  'app/v1/common/cancellationPolicy';
$route['api/uwinn/refund-policy'] 			=  'app/v1/common/refundPolicy';
$route['api/uwinn/game-rules'] 				=  'app/v1/common/gameRules';
$route['api/uwinn/contest-rules'] 			=  'app/v1/common/contestRules';


$route['api/hourly-report']					= 'api/thiredparty/hourlyReport';
$route['api/rechargeToUser'] 				= 'api/pos/rechargeToUser';
$route['api/getRechargeHistory'] 			= 'api/pos/getRechargeHistory';
$route['api/check-user'] 					= 'api/pos/checkUser';

// Stripe payment gateway..
$route['api/stripe-intent'] 			    = 'app/v1/stripe/initilizeOrder';
$route['api/stripe-payment-status'] 		= 'app/v1/stripe/paymentsuccess';
$route['api/stripe-info'] 			        = 'app/v1/stripe/stripeDetails';


// notification
$route['api/v1/app/notification/get-notifications'] = 'app/v1/notifications/index';
$route['api/v1/app/notification/update-notifications'] = 'app/v1/notifications/update';
$route['cron-notification'] = 'home/triggerNotificationJob';
$route['cron-cancel-order'] = 'home/AutoCancelNotificationJob';


$route['api/btc/move-to-wallet'] 			= 'app/v1/btc/moveToWallet';
$route['api/btc/winning-orders'] 			= 'app/v1/btc/winningOrders';
$route['api/btc/bank-transer'] 				= 'app/v1/btc/bankTranser';
$route['api/btc/cryto-transer'] 			= 'app/v1/btc/crytoTranser';

$route['api/btc/verify/email'] 			    = 'app/v1/btc/verifyEmail';
$route['api/btc/verify/mobile'] 			= 'app/v1/btc/verifyMobile';
$route['api/btc/verify/mobile/code'] 	    = 'app/v1/btc/verifyMobileCode';
$route['api/btc/verify/email/code'] 		= 'app/v1/btc/verifyEmailCode';
$route['api/btc/verify/whatsapp']           = 'app/v1/btc/verifyWhatsapp';

$route['api/lotto/check-raffle-eligible']        = 'api/uwinn/checkRaffleEligible';
$route['api/lotto/submit-eligible-raffleticket'] = 'api/uwinn/submitEligibleTicket';
$route['api/lotto/autofetch-users-data']         = 'api/uwinn/autoFetchUsersData';
$route['api/lotto/draw-result-range']            = 'api/uwinn/drawResultRange';
$route['api/lotto/draw-result']                  = 'api/uwinn/drawResult';

// $route['test'] 									= 'test/test';
$route['api/btctest/move-to-wallet'] 			= 'app/v1/btctest/moveToWallet';
// $route['api/test/orderCancellation'] 			= 'app/v1/btctest/orderCancellation';
$route['api/pos/refresh/campaigns'] 		     = 'api/pos/refreshCampaigns';


$route['api/hourlygame/list'] 				     = 'api/hourlygames/getGameList';
$route['api/hourlygame/order/create'] 			 = 'api/hourlygames/orderCreate';
$route['api/hourlygame/order/update']            = 'api/hourlygames/updateOrder';
$route['api/hourlygame/order/cancel']            = 'api/hourlygames/cancellationOrder';
$route['api/hourlygame/draw/slots']              = 'api/hourlygames/drawSlots';

$route['api/hourlygame/order/history'] 		     = 'api/hourlygames/OrderHistory';
$route['api/hourlygame/order/check-winner'] 	 = 'api/hourlygames/checkWinner';
$route['api/hourlygame/order/redeem'] 		     = 'api/hourlygames/OrderRedeem';
$route['api/hourlygame/report/summary'] 		 = 'api/hourlygames/ReportSummary';
$route['api/hourlygame/order/hourly-order-list'] = 'api/hourlygames/hourlyOrderList';
$route['api/lotto/hourly-draw-result']           = 'api/hourlygames/hourlyDrawResult';
$route['api/hourlygame/move-to-wallet']          = 'api/hourlygames/moveToWallet';

// $route['api/ding/operator/getaccount']     = 'api/ding/getaccount';
// $route['api/ding/plan/list'] 		       = 'api/ding/getPlanList'; 

// common summary api 
$route['api/updateSimNo'] 		   = 'api/users/updateSimNo';
$route['api/combined-summary'] 	   = 'api/CommonReportSummary/getUnifiedSummary';


$route['api/ding/operator/getaccount']     = 'api/ding/getaccount';
$route['api/ding/operator/authcheck']     = 'api/ding/authcheck';
$route['api/ding/plan/list'] 		       = 'api/ding/getPlanList';
$route['api/ding/plan/recharge/create']    = 'api/ding/createRecharge';
$route['api/ding/plan/recharge/history']   = 'api/ding/rechargeHistory';
$route['api/ding/plan/recharge/summary']   = 'api/ding/rechargeSummary';
$route['api/ding/recharge/history']        = 'api/ding/rechargeHistory';
$route['api/ding/recharge/summary']        = 'api/ding/rechargeSummary';
$route['api/ding/user/enable'] 		       = 'api/ding/setEnableDing';
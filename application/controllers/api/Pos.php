<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pos extends CI_Controller {
    
    var $postdata;
    var $user_agent;
    var $request_url; 
    var $method_name;
    
    public function  __construct()  
    { 
        parent:: __construct();
        error_reporting(E_ALL ^ E_NOTICE);  
        $this->load->model(array('sms_model','emailsendgrid_model','notification_model','emailtemplate_model'));
        $this->lang->load('statictext', 'api');
        $this->load->helper('apidata');
        $this->load->model(array('geneal_model','common_model'));

        $this->user_agent       =   $_SERVER['HTTP_USER_AGENT'];
        $this->request_url      =   $_SERVER['REDIRECT_URL'];
        $this->method_name      =   $_SERVER['REDIRECT_QUERY_STRING'];

        $this->load->library('generatelogs',array('type'=>'common'));
    } 


     /* * *********************************************************************
     * * Function name  : rechargeCouponHistory
     * * Developed By   : Dilip Halder
     * * Purpose        : This function used for rechargeCouponHistory History
     * * Date           : 29 August 2024
     * * **********************************************************************/
    public function rechargeCouponHistory()
    {   
        $apiHeaderData      =   getApiHeaderData();
        $this->generatelogs->putLog('APP',logOutPut($_POST));
        $result                             =   array();    
        if(requestAuthenticate(APIKEY,'POST')):
            
            $user_id            = $this->input->post('user_id');
            $searchBy           = $this->input->post('search_by');
            $searchValue        = $this->input->post('search_value');
            $itemsPerPage       = $this->input->post('itemsPerPage');
            $pageno             = $this->input->post('page');

            // data filter
            $from       = $this->input->post('from');
            $to         = $this->input->post('to');


            if(empty($user_id)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
            elseif(empty($this->input->post('itemsPerPage'))):
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_ITEMPERPAGE'),$result);die();
            elseif(empty($this->input->post('page'))):
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_PAGE_NO'),$result);die();
            else:

                $tblName             = 'uw_coupon_code_only';
                // where condition..
                // $whereCon['where']['users_id']  = (int)$user_id;
                if(!empty($searchBy) && !empty($searchValue) ):
                    $whereCon['where'][$searchBy]  = is_numeric($searchValue)?(int)$searchValue :$searchValue;
                endif;

                $whereCon['where_or'] = array('created_by' => (int)$user_id ,'redeemed_by' => (int)$user_id);

                // data condition..
                if($from):
                    $whereCon['where']['modified_at']['$gte']  = $from;
                endif;
                if($to):
                    $whereCon['where']['modified_at']['$lte']  = $to;
                endif;
                $sortBy       = array('modified_at' => -1);
                $totalcount   = $this->common_model->getData('count',$tblName,$whereCon,$sortBy);

                $itemsPerPage = $this->input->post('itemsPerPage');
                $pageno       = $this->input->post('page');

                // Sample long array with data
                $longArray = $totalcount;
                // Current page number (received from URL query parameter, e.g., ?page=2)
                $page = isset($pageno) ? (int)$pageno : 1;

                // Calculate total number of pages
                $totalPages = ceil($longArray / $itemsPerPage);
                $totalpage= array();
                // Pagination links
                for ($i = 1; $i <= $totalPages; $i++) {
                    if ($i == $page) {
                         $current_page = $i;
                         $totalpage[] = $i;
                    } else {
                         $totalpage[] = $i;
                    }
                }

                $startIndex    = ($page - 1) * $itemsPerPage;
                $CashVoucherList  = $this->common_model->getData('multiple',$tblName,$whereCon,$sortBy,$itemsPerPage,$startIndex);

                if(!empty($CashVoucherList)):
                    $totalpage                  = count($totalpage);
                    $result['recharge_list']     = $CashVoucherList?$CashVoucherList:array();
                    $result['current_page']     = $current_page;
                    $result['total_page']       =   $totalpage;
                    echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$result);die();
                else:
                    echo outPut(0,lang('FORBIDDEN_CODE'),lang('DATA_NOT_FOUND'),$result);die();
                endif;
            endif;
        else:
            echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
        endif;
    }  


    /* * *********************************************************************
     * * Function name  : generateRechargeCoupon
     * * Developed By   : Dilip Halder
     * * Purpose        : This function used for generateRechargeCoupon 
     * * Date           : 05 November 2024
     * * **********************************************************************/
    public function generateRechargeCoupon()
    {
        $apiHeaderData      =   getApiHeaderData();
        $this->generatelogs->putLog('APP',logOutPut($_POST));
        $result                             =   array();    
        if(requestAuthenticate(APIKEY,'GET')):
            
            $USERID         = $this->input->get('users_id');
            $coupon_amount  = $this->input->get('coupon_amount');

            if(empty($USERID)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
            elseif(empty($coupon_amount)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_Amount'),$result);die();
            else:

                $tblName           = 'uw_users'; 
                $whereCon['where'] =  array('users_id'=> (int)$USERID);
                $UserData          = $this->common_model->getData('single',$tblName,$whereCon);

                if(!empty($UserData) && $UserData['status'] == 'A' && ($UserData['is_verify']  == 'Y' || $UserData['is_varified']  == 'Y') ):

                    if($UserData['availableArabianPoints'] >= $coupon_amount):
 
                            $coupon_code_length = 12; // Length of the coupon code
                            $code = generateRandomString($coupon_code_length, "n");
                            $isDuplicate = true; // Flag to track duplicates

                            while ($isDuplicate) {
                                $whereCon['where'] = array('coupon_code' => (int)$code);
                                $DuplicateCoupn = $this->common_model->getData('single', 'uw_coupon_code_only', $whereCon);

                                if (empty($DuplicateCoupn)) {
                                    $isDuplicate = false; // No duplicate found
                                } else {
                                    // Regenerate the code
                                    $code = generateRandomString($coupon_code_length, "n");
                                }
                            }

                            // Debug output
                            $param['rc_id']                 = 'UTP'.$this->common_model->generateSerialNo('uw_rechargecoupons');;
                            $param['coupon_code']           = (int)$code;
                            $param['aed']                   = (float)$coupon_amount;
                            $param['coupon_code_amount']    = (float)$coupon_amount;
                            $param['coupon_code_statys']    = "Active";
                            $param['status']                = "A";
                            $param['created_user']          = $UserData['users_type'];
                            $param['created_by']            = (int)$USERID;
                            $param['created_at']            = date('Y-m-d H:i');
                            $param['modified_at']           = date('Y-m-d H:i');
                            $param['created_date']          = (int)$this->timezone->utc_time();
                            $param['expair_date']           = date('Y-m-d',strtotime('+1 years'));
                            $param['user_oid']              = new MongoDB\BSON\ObjectId($UserData['_id']->{'$id'});
                            $param['creation_ip']           = $this->input->ip_address();;
                            $result                         = $this->geneal_model->addData('uw_coupon_code_only', $param);

                            //Voucher Createing section start here ..
                            if($result):
                                
                                $commission_percentage = $UserData['commission_percentage'];
                                $commission_amount     = $coupon_amount*$commission_percentage/100;
                                
                                if($commission_amount > 0):
                                    $availableArabianPoints             = ($UserData['availableArabianPoints'] - $coupon_amount) + $commission_amount;
                                    // Updating remaing Upoints after Generating coupons.
                                    $uparam['availableArabianPoints']   = (float)$availableArabianPoints;
                                    $uparam['update_date']              = date('Y-m-d h:m');
                                    $UserDetails                        = $this->common_model->editData('uw_users',$uparam, 'users_id',(int)$USERID);
                                    
                                    // generating loadBalance for recharge coupon creation.. 
                                    $loadBalance['load_balance_id'] =  (int)$this->geneal_model->getNextSequence('uw_loadBalance');
                                    $loadBalance['user_oid']        =  new MongoDB\BSON\ObjectId($UserData['_id']->{'$id'});
                                    $loadBalance['request_id']      =  $param['rc_id'];
                                    $loadBalance['request_oid']     =  new MongoDB\BSON\ObjectId($result['_id']->{'$id'});
                                    $loadBalance['user_id_deb']     =  (int)$USERID;
                                    $loadBalance['user_id_cred']    =  (int)0;
                                    $loadBalance["availableArabianPoints"] =   (float)$UserData['availableArabianPoints'];
                                    $loadBalance["end_balance"]            =   (float)$UserData['availableArabianPoints']-$coupon_amount;
                                    $loadBalance['record_type']     = 'Debit';
                                    $loadBalance['narration']       = 'Recharge Coupon';
                                    $loadBalance['remarks']         = "Serial no. ".$param['rc_id'].'.';
                                    $loadBalance['upoints']         = (float)$coupon_amount;
                                    $loadBalance['creation_ip']     = $this->input->ip_address();;
                                    $loadBalance['created_at']      = date('Y-m-d H:i');
                                    $loadBalance['created_by']      = (int)$USERID;
                                    $loadBalance['status']          = 'A';
                                    $this->geneal_model->addData('uw_loadBalance', $loadBalance);

                                    // Generating loadBalance for Commission amount adding..
                                    $commisionBalance['load_balance_id'] =  (int)$this->geneal_model->getNextSequence('uw_loadBalance');
                                    $commisionBalance['user_oid']        =  new MongoDB\BSON\ObjectId($UserData['_id']->{'$id'});
                                    $commisionBalance['request_id']      =  $param['rc_id'];
                                    $commisionBalance['request_oid']     =  new MongoDB\BSON\ObjectId($result['_id']->{'$id'});
                                    $commisionBalance['user_id_deb']     =  (int)0;
                                    $commisionBalance['user_id_cred']    =  (int)$USERID;
                                    $commisionBalance["availableArabianPoints"] =   (float)$UserData['availableArabianPoints']-$coupon_amount;
                                    $commisionBalance["end_balance"]            =   (float)$availableArabianPoints;
                                    $commisionBalance['record_type']     = 'Credit';
                                    $commisionBalance['narration']       = 'Recharge Commission';
                                    $commisionBalance['remarks']         = "Serial no. ".$param['rc_id'].'.';
                                    $commisionBalance['upoints']         = (float)$commission_amount;
                                    $commisionBalance['creation_ip']     = $this->input->ip_address();;
                                    $commisionBalance['created_at']      = date('Y-m-d H:i');
                                    $commisionBalance['created_by']      = (int)$USERID;
                                    $commisionBalance['status']          = 'A';
                                    $this->geneal_model->addData('uw_loadBalance', $commisionBalance);
                                    echo outPut(1,lang('SUCCESS_CODE'),lang('VOUCHER_CREATED_SUCCESSFULLY'),$result);
                                endif;
                               
                            else:
                                 echo outPut(0,lang('SUCCESS_CODE'),lang('FORBIDDEN_MSG'),$result);die();
                            endif;
                        //Voucher Createing section end here ..
                        
                    else:
                        $error_msg = str_replace('###AMOUNT###', $coupon_amount ,  lang('LOW_AVAILABLE_BALANCE'));
                        echo outPut(0,lang('FORBIDDEN_CODE'),$error_msg ,$result);die();
                    endif;
                else:
                    echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_USER_ID'),$result);die();
                endif;

            endif;
        else:
            echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
        endif;
    }

    /* * *********************************************************************
     * * Function name : redeemRechareCoupon
     * * Developed By  : Dilip Halder
     * * Purpose       : This function used for redeem Coupon
     * * Date          : 06 August 2024
     * * **********************************************************************/
    public function redeemRechargeCoupon()
    {   
        $apiHeaderData      =   getApiHeaderData();
        $this->generatelogs->putLog('APP',logOutPut($_POST));
        $result             =   array();    
        if(requestAuthenticate(APIKEY,'POST')):
            
            $userId  = $this->input->post('users_id');
            $coupon  = $this->input->post('coupon');

            if($userId == ''): 
                echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
            elseif($coupon == ''): 
                echo outPut(0,lang('SUCCESS_CODE'),lang('COUPON_CODE_ID_EMPTY'),$result);
            else:
                $plateform = 'app';
                $this->common_model->redeemCouponVoucher($userId,$coupon,$plateform);
            endif;
        else:
            echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
        endif;
    }

    /* * *********************************************************************
     * * Function name  : SummeryReportCashVoucher
     * * Developed By   : Dilip Halder
     * * Purpose        : This function used for SummeryReportCashVoucher History
     * * Date           : 11 November 2024
     * * **********************************************************************/
    public function SummeryReportCashVoucher()
    {
        $apiHeaderData      =   getApiHeaderData();
        $this->generatelogs->putLog('APP',logOutPut($_POST));
        $result                             =   array();    
        if(requestAuthenticate(APIKEY,'POST')):

            $USERID = $this->input->post('user_id');
            $from   = $this->input->post('from');
            $to     = $this->input->post('to');

            if(empty($USERID)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
            else:
 
                $tblName           = 'uw_users'; 
                $whereCon['where'] =  array('users_id'=> (int)$USERID);
                $UserData          = $this->common_model->getData('single',$tblName,$whereCon);
                $UserData['from']  = $this->input->post('from');
                $UserData['to']    = $this->input->post('to');
                if(!empty($UserData) && $UserData['status'] == 'A' && ($UserData['is_verify']  == 'Y' || $UserData['is_varified']  == 'Y') ):

                    $whereCon1['where']  = array('user_oid'=> new MongoDB\BSON\ObjectId($UserData['_id']->{'$id'}) ,'narration' => "Recharge Coupon");
                    $result              = $this->common_model->getCashSummery($UserData); 
                    echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$result);die();

                else:
                    echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_USER_ID'),$result);die();
                endif;

            endif;
        else:
            echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
        endif;
    } 

    /* * *********************************************************************
     * * Function name : rechargeToUser
     * * Developed By  : Dilip Halder
     * * Purpose       : This function used for recharge To User
     * * Date          : 23 December 2024
     * * **********************************************************************/
    public function rechargeToUser()
    {   
        $apiHeaderData      =   getApiHeaderData();
        $this->generatelogs->putLog('APP',logOutPut($_POST));
        $result                             =   array();    
        if(requestAuthenticate(APIKEY,'POST')):
            
            if($this->input->get('users_id') == ''): 
                echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
            elseif($this->input->post('recharge_by') == ''): 
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_RECHARGE_BY'),$result);
            elseif($this->input->post('recharge_amount') == ''): 
                echo outPut(0,lang('SUCCESS_CODE'),lang('RECHARGE_AMOUNT_EMPTY'),$result);
            elseif($this->input->post('recharge_amount') < 5): 
                echo outPut(0,lang('SUCCESS_CODE'),lang('RECHARGE_AMOUNT_ERROR'),$result);
            else:

                // seller id..
                $usersId        = $this->input->get('users_id');  
                // users details..
                $RechargeBy     = $this->input->post('recharge_by');
                $rechargeAmount = $this->input->post('recharge_amount');

                $tblName        = "uw_users";
                $where['where'] = array('users_id' => (int)$usersId);
                $FieldList      = array('users_type','status','availableArabianPoints','commission_percentage');
                $userDetails    = $this->common_model->getParticularFieldByMultipleCondition($FieldList,$tblName, $where);
                // echo "<pre>";print_r();die();
                
                if(!empty($userDetails) && $userDetails['status'] == "A"): 
                  
                    if($userDetails['availableArabianPoints'] >= $rechargeAmount ):
                        // check valid users
                        $whereCon['where']['status']      = 'A';
                        $whereCon['where']['users_type']  = 'Users';
                        $whereCon['where']['$or']         = array(
                            array( 'users_email'  =>  strtolower($this->input->post('recharge_by')) ),
                            array( 'users_mobile' =>  (int)$this->input->post('recharge_by'))
                        );
                        $FieldList  = array('users_id','users_type','users_name','last_name','country_code','users_mobile','users_email','referrel_amount','availableArabianPoints');
                        $checkUser  = $this->common_model->getParticularFieldByMultipleCondition($FieldList,$tblName,$whereCon);
                        // echo "<pre>";print_r($checkUser);die();
                        if($checkUser):
                            if((int)$checkUser['users_id'] != (int)$usersId):

                                //Commission added for rechrge..
                                $commission_percentage = $userDetails['commission_percentage'];
                                $commission_amount     = $rechargeAmount*$commission_percentage/100;
                                //deduting recharge amount from saeller account..
                                $sellerParam['availableArabianPoints'] = -(float)$rechargeAmount+$commission_amount;
                                $this->common_model->manageBalance($tblName,$sellerParam,'users_id',(int)$usersId);

                                //Updating recharge amount..
                                $param['totalArabianPoints']     = +(float)$rechargeAmount;
                                $param['availableArabianPoints'] = +(float)$rechargeAmount;
                                $this->common_model->manageBalance($tblName,$param,'users_id',(int)$checkUser['users_id']);

                                /* Load Balance Table -- BTB user*/
                                $fromuserparam["load_balance_id"]    = (int)$this->geneal_model->getNextSequence('uw_loadBalance');
                                $fromuserparam["user_oid"]           = new MongoDB\BSON\ObjectId($userDetails['_id']['$id']);
                                $fromuserparam["user_id_cred"]       = (int)$checkUser['users_id'];
                                $fromuserparam["user_id_deb"]        = (int)$usersId;
                                $fromuserparam["user_id_to"]         = (int)$checkUser["users_id"];
                                $fromuserparam["upoints"]            = (float)$rechargeAmount;
                                $fromuserparam["record_type"]        = 'Debit';
                                $fromuserparam["narration"]          = 'Recharge';
                                $fromuserparam["remarks"]            = $rechargeAmount.' AED recharged to '.$RechargeBy;
                                $fromuserparam["availableArabianPoints"]   = (float)$userDetails['availableArabianPoints'];
                                $fromuserparam["end_balance"]              = (float)$userDetails['availableArabianPoints']-$rechargeAmount;
                                $fromuserparam["creation_ip"]        = currentIp();
                                $fromuserparam["created_at"]         = date('Y-m-d H:i');
                                $fromuserparam["created_by"]         = (int)$usersId;
                                $fromuserparam["status"]             = "A";
                                $this->geneal_model->addData('uw_loadBalance', $fromuserparam);
                                /* End */
 
                                /* Load Balance Table -- BTC user*/
                                $touserparam["load_balance_id"]     = (int)$this->geneal_model->getNextSequence('uw_loadBalance');
                                $touserparam["user_oid"]            = new MongoDB\BSON\ObjectId($checkUser['_id']['$id']);
                                $touserparam["user_id_cred"]        = (int)$checkUser["users_id"];
                                $touserparam["user_id_deb"]         = (int)$usersId;
                                $touserparam["upoints"]             = (float)$rechargeAmount;
                                $touserparam["record_type"]         = 'Credit';
                                $touserparam["narration"]           = 'Recharge';
                                $touserparam["remarks"]             = $rechargeAmount.' AED recharged';
                                $touserparam["availableArabianPoints"]  = (float)$checkUser['availableArabianPoints'];
                                $touserparam["end_balance"]             = (float)$checkUser['availableArabianPoints']+$rechargeAmount;
                                $touserparam["creation_ip"]         = currentIp();
                                $touserparam["created_at"]          = date('Y-m-d H:i');
                                $touserparam["created_user_id"]     = (int)$usersId;
                                $touserparam["created_by"]          = (int)$usersId;
                                $touserparam["status"]              = "A";
                                $this->geneal_model->addData('uw_loadBalance', $touserparam);

                                // Commission capturing in order uw_loadbalance table..
                                $commissionParam["load_balance_id"]          = (int)$this->geneal_model->getNextSequence('uw_loadBalance');
                                $commissionParam["user_oid"]                 = new MongoDB\BSON\ObjectId($userDetails['_id']['$id']);
                                $commissionParam["user_id_cred"]             = (int)$usersId;
                                $commissionParam["user_id_deb"]              = (int)0;
                                $commissionParam["upoints"]                  = (float)$commission_amount;
                                $commissionParam["availableArabianPoints"]   = (float)$userDetails['availableArabianPoints']-$rechargeAmount;
                                $commissionParam["end_balance"]              = (float)$userDetails['availableArabianPoints']-$rechargeAmount+$commission_amount;
                                $commissionParam["record_type"]              = 'Credit';
                                $commissionParam["narration"]                = 'Recharge Commission';
                                $commissionParam["remarks"]                  = 'Commission added for recharge.';
                                $commissionParam["creation_ip"]              =  $this->input->ip_address();
                                $commissionParam["created_at"]               =  date('Y-m-d H:i');
                                $commissionParam["created_by"]               =  (int)$usersId;
                                $commissionParam["status"]                   =  "A";
                                $this->geneal_model->addData('uw_loadBalance', $commissionParam);
                                // Credit the purchesed points and get available arabian points of user.

                                // REFERRAL AMOUNT RELEASING FUNCTION..
                                if(!empty($checkUser['referrel_amount']) && $checkUser['referrel_amount'] > 0):
                                    $this->common_model->referralAmountRelease($checkUser['users_id'],$rechargeAmount);
                                endif;

                                $result  = array(
                                    'upoints'      => $rechargeAmount,
                                    'record_type'  => 'Debit',
                                    'narration'    => 'Recharge',
                                    'created_at'   => $touserparam["created_at"],
                                    'users_name'   => $checkUser['users_name']." ".$users_name['last_name'],
                                    'users_email'  => $checkUser['users_email'],
                                    'country_code' => $checkUser['country_code'],
                                    'users_mobile' => $checkUser['users_mobile'],
                                );
                                echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$result);
                            else:
                                echo outPut(0,lang('SUCCESS_CODE'),lang('ERROR_SELF_TRANSFER'),$result);
                            endif;
                            
                        else:
                            echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_USER'),$result);
                        endif;

                    else:
                        $error_msg = str_replace('###AMOUNT###', $rechargeAmount.' AED ',lang('LOW_RECHARGE_AVAILABLE_BALANCE'));
                        echo outPut(0,lang('SUCCESS_CODE'),$error_msg,$result);
                    endif;

                else:
                    echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_USER'),$result);
                endif;
            endif;
        else:
            echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
        endif;
    }

    /* * *********************************************************************
     * * Function name  : getRechargeHistory
     * * Developed By   : Dilip Halder
     * * Purpose        : This function used for get Recharge History
     * * Date           : 24 December 2024
     * * **********************************************************************/
    public function getRechargeHistory()
    {   
        $apiHeaderData      =   getApiHeaderData();
        $this->generatelogs->putLog('APP',logOutPut($_POST));
        $result             =   array();    
        if(requestAuthenticate(APIKEY,'POST')):
                
            $usersId         = $this->input->post('users_id');
            $searchBy        = $this->input->post('search_by');
            $searchValue     = $this->input->post('search_value');
            $itemsPerPage    = $this->input->post('itemsPerPage');
            $pageno          = $this->input->post('page');
            // data filter
            $from   = $this->input->post('from');
            $to     = $this->input->post('to');

            if(empty($usersId)): 
                echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
            elseif(empty($itemsPerPage)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_ITEMPERPAGE'),$result);die();
            elseif(empty($pageno)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_PAGE_NO'),$result);die();
            else:

                $tblName        = "uw_users";
                $where['where'] = array('users_id' => (int)$usersId);
                $FieldList      = array('users_type','status','availableArabianPoints');
                $userDetails    = $this->common_model->getParticularFieldByMultipleCondition($FieldList,$tblName, $where);
                
                if(!empty($userDetails)):
                    
                    $whereCon['user_oid']    = new MongoDB\BSON\ObjectId($userDetails['_id']['$id']);
                    $whereCon['narration']   = 'Recharge';
                    $whereCon['record_type'] = 'Debit';
                    
                    if(!empty($from)):
                        $from = date('Y-m-d H:i',strtotime($from));
                        $whereCon['created_at']['$gte'] = $from;
                    endif;
                    if(!empty($to)):
                        $to = date('Y-m-d H:i',strtotime($to));
                        $whereCon['created_at']['$lte'] = $to;
                    endif;

                    $tblName     = 'uw_loadBalance';
                    $shortField  = array('_id'=> -1);
                    $resultType  = 'count';

                    // $totalcount  = $this->common_model->getRechargeHistory($tblName,$whereCon,$shortField,$resultType,$itemsPerPage,$startIndex);
                    $totalcount  = $this->common_model->getRechargeHistory($tblName,$whereCon,$shortField,$resultType);

                    $longArray   = $totalcount?$totalcount:0;
                    // Current page number (received from URL query parameter, e.g., ?page=2)
                    $page = isset($pageno) ? (int)$pageno : 1;

                    // Calculate total number of pages
                    $totalPages = ceil($longArray / $itemsPerPage);
                    $totalpage= array();
                    // Pagination links
                    for ($i = 1; $i <= $totalPages; $i++) {
                        if ($i == $page) {
                             $current_page = $i;
                             $totalpage[] = $i;
                        } else {
                             $totalpage[] = $i;
                        }
                    }

                    $startIndex    = ($page - 1) * $itemsPerPage;
                    $resultType    = '';
                    $rechargeList  = $this->common_model->getRechargeHistory($tblName,$whereCon,$shortField,$resultType,$itemsPerPage,$startIndex);
                    
                    if(!empty($rechargeList)):
                        $totalpage                  = count($totalpage);
                        $result['recharge_list']    = $rechargeList?$rechargeList:array();
                        $result['current_page']     = $current_page;
                        $result['total_page']       = $totalpage;
                        echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$result);die();
                    else:
                        echo outPut(0,lang('FORBIDDEN_CODE'),lang('DATA_NOT_FOUND'),$result);die();
                    endif;
                else:
                    echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_USER'),$result);
                endif;
               
            endif;
        else:
            echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
        endif;
    }

    /* * *********************************************************************
     * * Function name  : checkUser
     * * Developed By   : Dilip Halder
     * * Purpose        : This function used to checkUser.
     * * Date           : 27 December 2024
     * * **********************************************************************/
    public function checkUser()
    {
        $apiHeaderData      =   getApiHeaderData();
        $this->generatelogs->putLog('APP',logOutPut($_POST));
        $result             =   array();    
        if(requestAuthenticate(APIKEY,'POST')):

            $users_id  = $this->input->get('users_id');
            $search_by = $this->input->post('search_by');

            if($users_id == ''): 
                echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);
            elseif($search_by == ''):
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_NUMBER_EMAIL'),$result);
            else:
                
                $usersId        = (int)$this->input->get('users_id');
                $tblName        = "uw_users";
                $where['where'] =  array(
                    '$or' => array(
                        array('users_mobile' => (int)$search_by),
                        array('users_email' => $search_by),
                    ) 
                );
                $FieldList   = array('users_name','last_name');
                $userDetails = $this->common_model->getParticularFieldByMultipleCondition($FieldList,$tblName, $where );
                if(!empty($userDetails)):
                    $full_name = $userDetails['users_name'].' '.$userDetails['last_name'];
                    $result['users_name'] = $full_name;
                    echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$result);
                else:
                    echo outPut(0,lang('SUCCESS_CODE'),lang('USER_NOT_AVAILABLE'),$result);
                endif;
               
            endif;
        else:
            echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
        endif;
    }

    /* * *********************************************************************
     * * Function name  : checkWinnerOrderHistory
     * * Developed By   : Dilip Halder
     * * Purpose        : This function used for checkWinnerOrderHistory 
     * * Date           : 07 December 2024
     * * **********************************************************************/
    public function checkWinnerOrderHistory()
    {
        $apiHeaderData      =   getApiHeaderData();
        $this->generatelogs->putLog('APP',logOutPut($_POST));
        $result                             =   array();    
        if(requestAuthenticate(APIKEY,'POST')):
            $USERID  = $this->input->post('user_id');
            $orderId = $this->input->post('order_id');

            if(empty($USERID)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
            elseif(empty($orderId)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('ORDER_ID_EMPTY'),$result);die();
            else:
                $tblName           = 'uw_users'; 
                $whereCon['where'] =  array('users_id'=> (int)$USERID);
                $UserData          = $this->common_model->getData('single',$tblName,$whereCon);
                if(!empty($UserData) && $UserData['status'] == 'A' && ($UserData['is_verify']  == 'Y' || $UserData['is_varified']  == 'Y') ):
                    $tblName           = 'uw_lotto_orders'; 
                    $whereCon['where'] =  array('order_id'=> $orderId);
                    $orderData         = $this->common_model->getData('single',$tblName,$whereCon);
                    if(!empty($orderData)):
                        echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$orderData);die();
                    else:
                        $result = [];
                        echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$result);die();
                    endif;
                else:
                    echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_USER_ID'),$result);die();
                endif;

            endif;
        else:
            echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
        endif;
    } 
     
}
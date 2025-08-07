<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Wallets extends CI_Controller {
    
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
     * * Function name  : getBalance
     * * Developed By   : Afsar Ali
     * * Purpose        : This function used for get product list
     * * Date           : 27 JUNE 2024
     * * Date           : Dilip Halder
     * * Date           : 18 October 2024
     * * **********************************************************************/
    public function getBalance()
    {   
        try {
            $apiHeaderData      =   getApiHeaderData();
            $this->generatelogs->putLog('APP',logOutPut($_POST));
            $result                             =   array();    
            if(!$this->input->get('user_id')):
                echo outPut(0,lang('FORBIDDEN_CODE'),lang('USER_ID_INCORRECT'),$result);
            else:
                if(requestAuthenticate(APIKEY,'GET')):
                    $USERID                         =   (int)$this->input->get('user_id');
                    $winningDetails                 = $this->common_model->winningBalance($USERID);

                    $result['play_balance']     =   0;
                    $result['winning_balance']  =   0;
        
                    $whereCon['where']          =   array('users_id' => $USERID, 'status' => 'A');
                    $order                      =   array('_id' => -1);
                    $userData                   =   $this->geneal_model->getData2('single','uw_users', $whereCon,$order);
        
                    $result['play_balance']     =   $userData['availableArabianPoints'];
                    $result['winning_balance']  =   $userData['winningBalance']?$userData['winningBalance']:0;
        
                    echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
                else:
                    echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
                endif;
            endif;
        } catch (\Throwable $th) {
            echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),array());
        }
    }

    /* * *********************************************************************
     * * Function name  : transferPlayBalance
     * * Developed By   : Afsar Ali
     * * Purpose        : This function used for transfer play balance
     * * Date           : 27 JUNE 2024
     * * **********************************************************************/
    public function transferPlayBalance()
    {   
        try {
            $apiHeaderData      =   getApiHeaderData();
            $this->generatelogs->putLog('APP',logOutPut($_POST));
            $result                             =   array();    
            if(!$this->input->get('user_id')):
                echo outPut(0,lang('FORBIDDEN_CODE'),lang('USER_ID_INCORRECT'),$result);
            else:
                if(requestAuthenticate(APIKEY,'POST')):
                    $id                         =   (int)$this->input->post('user_id');
                    $result['play_balance']     =   0;
                    $result['winning_balance']  =   0;
        
                    $whereCon['where']          =   array('users_id' => $id, 'status' => 'A');
                    $order                      =   array('_id' => -1);
                    $userData                   =   $this->geneal_model->getData2('single','uw_users', $whereCon,$order);
        
                    $result['play_balance']     =   $userData['availableArabianPoints'];
                    $result['winning_balance']  =   $userData['winningBalance']?$userData['winningBalance']:0;
        
                    echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
                else:
                    echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
                endif;
            endif;
        } catch (\Throwable $th) {
            echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),array());
        }
    }

    /* * *********************************************************************
     * * Function name  : transferPlayBalance
     * * Developed By   : Afsar Ali
     * * Purpose        : This function used for transfer play balance
     * * Date           : 28 JUNE 2024
     * * **********************************************************************/
    public function transferWinningBalance()
    {   
        try {
            $apiHeaderData      =   getApiHeaderData();
            $this->generatelogs->putLog('APP',logOutPut($_POST));
            $result                             =   array();    
            if(!$this->input->post('user_id')):
                echo outPut(0,lang('FORBIDDEN_CODE'),lang('USER_ID_INCORRECT'),$result);
            elseif(!$this->input->post('amount')):
                echo outPut(0,lang('FORBIDDEN_CODE'),lang('AMOUNT_EMPTY'),$result);
            else:
                if(requestAuthenticate(APIKEY,'POST')):

                    $USERID    = $this->input->post('user_id');
                    $amount    = $this->input->post('amount');
                    $plateform = 'app';
                    $result    = $this->common_model->redeemwinningAMount($USERID,$amount,$plateform);
                else:
                    echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
                endif;
            endif;
        } catch (\Throwable $th) {
            // echo $th->getMessage();die();
            echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),array('error' => $th->getMessage()));
        }
    }
    // public function transferWinningBalance()
    // {   
    //     try {
    //         $apiHeaderData      =   getApiHeaderData();
    //         $this->generatelogs->putLog('APP',logOutPut($_POST));
    //         $result                             =   array();    
    //         if(!$this->input->post('user_id')):
    //             echo outPut(0,lang('FORBIDDEN_CODE'),lang('USER_ID_INCORRECT'),$result);
    //         elseif(!$this->input->post('amount')):
    //             echo outPut(0,lang('FORBIDDEN_CODE'),lang('AMOUNT_EMPTY'),$result);
    //         else:
    //             if(requestAuthenticate(APIKEY,'POST')):
    //                 $id                         =   (int)$this->input->post('user_id');
    //                 $amount                     =   (float)$this->input->post('amount');
    //                 //Get User Data
    //                 $whereCon['where']          =   array('users_id' => $id, 'status' => 'A');
    //                 $order                      =   array('_id' => -1);
    //                 $userData                   =   $this->geneal_model->getData2('single','uw_users', $whereCon,$order);
    //                 //End

    //                 if($userData['winningBalance'] > $amount):
    //                     $availableArabianPoints =   (float)$userData['availableArabianPoints'] + $amount;
    //                     $totalArabianPoints     =   (float)$userData['totalArabianPoints'] + $amount;

    //                     $winningBalance         =   (float)$userData['winningBalance'] - $amount;

    //                     $param['availableArabianPoints']    =   $availableArabianPoints;
    //                     $param['totalArabianPoints']        =   $totalArabianPoints;
    //                     $param['winningBalance']            =   $winningBalance;
    //                     $param['update_date']               =   date('Y-m-d h:m');

    //                     $isInsert = $this->common_model->editData('uw_users',$param, 'users_id',(int)$id);
    //                     if($isInsert){
                            
    //                         $debitRecord['load_balance_id'] =   (int)$this->geneal_model->getNextSequence('uw_loadBalance');
    //                         $debitRecord['user_oid']        =   new MongoDB\BSON\ObjectId($userData['_id']->{'$id'});
    //                         $debitRecord['user_id_deb']     =   (int)$id;
    //                         $debitRecord['user_id_cred']    =   (int)$id;
    //                         $debitRecord['record_type']     =   'Debit';
    //                         $debitRecord['narration']       =   'Winning Amount';
    //                         $debitRecord['remarks']         =   'Transfered Money from Winning Balance to Play Balance';
    //                         $debitRecord['upoints']         =   (float)$amount;
    //                         $debitRecord['winningBalance']  =   (float)$winningBalance;
    //                         $debitRecord['creation_ip']     =   $this->input->ip_address();;
    //                         $debitRecord['created_at']      =   date('Y-m-d H:i');
    //                         $debitRecord['created_by']      =   (int)$id;
    //                         $debitRecord['status']          =   'A';

    //                         $this->geneal_model->addData('uw_loadBalance', $debitRecord);

    //                         $creaditRecord['load_balance_id'] =   (int)$this->geneal_model->getNextSequence('uw_loadBalance');
    //                         $creaditRecord['user_oid']        =   new MongoDB\BSON\ObjectId($userData['_id']->{'$id'});
    //                         $creaditRecord['user_id_deb']     =   (int)$id;
    //                         $creaditRecord['user_id_cred']    =   (int)$id;
    //                         $creaditRecord['record_type']     =   'Credit';
    //                         $creaditRecord['narration']       =   'Play Amount';
    //                         $creaditRecord['remarks']         =   'Transfered Money from Winning Balance to Play Balance';
    //                         $creaditRecord['upoints']         =   (float)$amount;
    //                         $creaditRecord['availableArabianPoints']  =   (float)$availableArabianPoints;
    //                         $creaditRecord['creation_ip']     =   $this->input->ip_address();;
    //                         $creaditRecord['created_at']      =   date('Y-m-d H:i');
    //                         $creaditRecord['created_by']      =   (int)$id;
    //                         $creaditRecord['status']          =   'A';

    //                         $this->geneal_model->addData('uw_loadBalance', $creaditRecord);
    //                     }
    //                     $userData                   =   $this->geneal_model->getData2('single','uw_users', $whereCon,$order);
    //                     $result = array (
    //                         'totalArabianPoints'     => $userData['totalArabianPoints'],
    //                         'availableArabianPoints' => $userData['availableArabianPoints'],
    //                         'winningBalance'         => $userData['winningBalance'],
    //                         'totalWinningBalance'    => $userData['totalWinningBalance'],
    //                     );
    //                     echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
    //                 else:
    //                     echo outPut(1,lang('SUCCESS_CODE'),lang('AMOUNT_ERROR'),$result);
    //                 endif;
    //                 // $result['play_balance']  =   $userData['availableArabianPoints'];
    //                 // $result['winning_balance']   =   $userData['winningBalance']?$userData['winningBalance']:0;
        
    //             else:
    //                 echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
    //             endif;
    //         endif;
    //     } catch (\Throwable $th) {
    //         // echo $th->getMessage();die();
    //         echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),array('error' => $th->getMessage()));
    //     }
    // }
    
     /* * *********************************************************************
     * * Function name  : withdrawWinningBalance
     * * Developed By   : Afsar Ali
     * * Purpose        : This function used for widthdraw inning balance
     * * Date           : 28 JUNE 2024
     * * Updated By     : Dilip Halder
     * * Updated Date   : 12-09-2024
     * * **********************************************************************/
     public function withdrawWinningBalance()
     {   
        try {
            $apiHeaderData      =   getApiHeaderData();
            $this->generatelogs->putLog('APP',logOutPut($_POST));
            $result                             =   array();    
            if(!$this->input->post('user_id')):
                echo outPut(0,lang('FORBIDDEN_CODE'),lang('USER_ID_INCORRECT'),$result);
            elseif(!$this->input->post('amount')):
                echo outPut(0,lang('FORBIDDEN_CODE'),lang('AMOUNT_EMPTY'),$result);
            elseif($this->input->post('amount') < 100):
                echo outPut(0,lang('FORBIDDEN_CODE'),lang('MIN_WITHDRAW_AMOUNT_ERROR'),$result);
            elseif(!$this->input->post('type')):
                echo outPut(0,lang('FORBIDDEN_CODE'),lang('WITHDRAW_TYPE'),$result);
            else:
                if(requestAuthenticate(APIKEY,'POST')):
                    echo outPut(0,lang('FORBIDDEN_CODE'),lang('WITHDRAWAL_DISABLED'),$result);die();  
                    $USERID         = (int)$this->input->post('user_id');

                    $tableName   = "uw_users";
                    $Fields      = array('_id','users_id' ,'totalArabianPoints','availableArabianPoints','winningBalance','status');
                    $userDetails = $this->common_model->getSingleDataByParticularField($Fields,$tableName,'users_id',(int)$USERID);

                    $wallet_type                = $this->input->post('type');
                    $winning_amount             = (float)$this->input->post('amount');;
                    $availableWinningBalance    = $userDetails['winningBalance'];
                    $availableArabianPoints     = $userDetails['availableArabianPoints'];
                    $totalArabianPoints         = $userDetails['totalArabianPoints'];
                    $user_OId                   = $userDetails['_id']['$id'];
                    $plateform                  = 'app';

                    if( !empty($userDetails) && $userDetails['status'] == 'A' && $availableArabianPoints >= $winning_amount && $availableWinningBalance >= $winning_amount && $winning_amount >= 100 ):
                        
                        if($wallet_type     == 'Cash'):
                            $this->common_model->generateWinnerVouvcher($USERID,$amount,$plateform);
                        elseif($wallet_type  == "Bank"  || $wallet_type  == "Cripto" ):
                            
                            $POSTDATA['amount']              = $this->input->post('amount');
                            $POSTDATA['type']                = $this->input->post('type');
                            $POSTDATA['cripto_id']           = $this->input->post('cripto_id');
                            $POSTDATA['account_holder_name'] = $this->input->post('account_holder_name');
                            $POSTDATA['bank_name']           = $this->input->post('bank_name');
                            $POSTDATA['account_no']          = $this->input->post('account_no');
                            $POSTDATA['ifsc_code']           = $this->input->post('ifsc_code');
                            $this->common_model->withdrawWinningBalance($USERID,$POSTDATA,$plateform);
                            die();
                        endif;  

                    elseif($userDetails['status'] === 'I' || $userDetails['status'] === 'B' || $userDetails['status'] === 'D' ):
                        echo outPut(0,lang('FORBIDDEN_CODE'),lang('ACCOUNT_INACIVE'),$result);die();  
                    elseif($availableWinningBalance < $winning_amount ):
                        $error_msg = str_replace('###AMOUNT###', $winning_amount ,  lang('LOW_AVAILABLE_WINNING_BALANCE'));
                        echo outPut(0,lang('FORBIDDEN_CODE'),$error_msg,$result);die();  
                    elseif($availableArabianPoints < $winning_amount ):
                        $error_msg = str_replace('###AMOUNT###', $winning_amount ,  lang('LOW_AVAILABLE_BALANCE_TO_REDEEM'));
                        echo outPut(0,lang('FORBIDDEN_CODE'),$error_msg,$result);die();  
                    else:
                        echo outPut(0,lang('FORBIDDEN_CODE'),lang('MIN_WITHDRAW_AMOUNT_ERROR'));die();  
                    endif;
 
                else:
                    echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
                endif;
            endif;
        } catch (\Throwable $th) {
            // echo $th->getMessage();die();
            echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),array('error' => $th->getMessage()));
        }
     }
    
    /* * *********************************************************************
     * * Function name  : addPlayBalance
     * * Developed By   : Afsar Ali
     * * Purpose        : This function used for add play balance
     * * Date           : 29 JUNE 2024
     * * **********************************************************************/
    public function addPlayBalance()
    {   
        try {
            $apiHeaderData      =   getApiHeaderData();
            $this->generatelogs->putLog('APP',logOutPut($_POST));
            $result                             =   array();    
            if(!$this->input->post('user_id')):
                echo outPut(0,lang('FORBIDDEN_CODE'),lang('USER_ID_INCORRECT'),$result);
            elseif(!$this->input->post('amount')):
                echo outPut(0,lang('FORBIDDEN_CODE'),lang('AMOUNT_EMPTY'),$result);
            // elseif($this->input->post('amount') < 200):
            //     echo outPut(0,lang('FORBIDDEN_CODE'),lang('MIN_WITHDRAW_AMOUNT_ERROR'),$result);
            elseif(!$this->input->post('payment_mode')):
                echo outPut(0,lang('FORBIDDEN_CODE'),lang('PAYMENT_MODE_EMPTY'),$result);
            else:
                if(requestAuthenticate(APIKEY,'POST')):
                    $payment_mode               =   $this->input->post('payment_mode');
                    $id                         =   (int)$this->input->post('user_id');
                    $amount                     =   (float)$this->input->post('amount');
                    //Get User Data
                    $whereCon['where']          =   array('users_id' => $id, 'status' => 'A');
                    $order                      =   array('_id' => -1);
                    $userData                   =   $this->geneal_model->getData2('single','uw_users', $whereCon,$order);
                    //End

                    if($amount > 0):
                        $availableArabianPoints = (float)$userData['availableArabianPoints'];
                        $end_balance            = (float)$userData['availableArabianPoints'] + (float)$amount;
                        $totalArabianPoints     = (float)$userData['totalArabianPoints'] + (float)$amount;

                        $uparam['availableArabianPoints']    =   $availableArabianPoints + $amount;
                        $uparam['totalArabianPoints']        =   $totalArabianPoints;
                        $uparam['update_date']               =   date('Y-m-d h:m');
                        $isUpdate = $this->common_model->editData('uw_users',$uparam, 'users_id',(int)$id);

                        if($isUpdate):
                            $creditRecord['load_balance_id']        =   (int)$this->geneal_model->getNextSequence('uw_loadBalance');
                            $creditRecord['user_oid']               =   new MongoDB\BSON\ObjectId($userData['_id']->{'$id'});
                            $creditRecord['user_id_deb']            =   0;
                            $creditRecord['user_id_cred']           =   (int)$id;
                            $creditRecord['record_type']            =   'Credit';
                            $creditRecord['narration']              =   'Play Amount';
                            $creditRecord['remarks']                =   'Add Play balance by '.$payment_mode;
                            $creditRecord['upoints']                =   (float)$amount;
                            $creditRecord['availableArabianPoints'] =   (float)$availableArabianPoints;
                            $creditRecord['end_balance']            =   (float)$end_balance;
                            $creditRecord['creation_ip']            =   $this->input->ip_address();;
                            $creditRecord['created_at']             =   date('Y-m-d H:i');
                            $creditRecord['created_by']             =   (int)$id;
                            $creditRecord['status']                 =   'A';
                            $this->geneal_model->addData('uw_loadBalance', $creditRecord);
                        endif;
                        $userData                   =   $this->geneal_model->getData2('single','uw_users', $whereCon,$order);
            
                        $result['play_balance']     =   $userData['availableArabianPoints'];
                        $result['winning_balance']  =   $userData['winningBalance']?$userData['winningBalance']:0;
                        echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
                    else:
                        echo outPut(0,lang('FORBIDDEN_CODE'),lang('AMOUNT_EMPTY'),$result);
                    endif;
                else:
                    echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
                endif;
            endif;
        } catch (\Throwable $th) {
            // echo $th->getMessage();die();
            echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),array('error' => $th->getMessage()));
        }
    }

    /* * *********************************************************************
     * * Function name  : generateCashVoucher
     * * Developed By   : Dilip Halder
     * * Purpose        : This function used for generateCashVoucher
     * * Date           : 29 August 2024
     * * **********************************************************************/
    public function generateCashVoucher()
    {
        $apiHeaderData      =   getApiHeaderData();
        $this->generatelogs->putLog('APP',logOutPut($_POST));
        $result                             =   array();    
        if(requestAuthenticate(APIKEY,'POST')):
            
            echo outPut(0,lang('SUCCESS_CODE'),"new version available ,please update from store",$result);die();

            $USERID          = $this->input->post('user_id');
            $winning_amount  = $this->input->post('winning_amount');

            if(empty($USERID)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
            elseif(empty($winning_amount)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_Amount'),$result);die();
            else:

                $plateform = 'app';
                $result    = $this->common_model->generateWinnerVouvcher($USERID,$winning_amount,$plateform);
                die();
            endif;
        else:
            echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
        endif;
    }

    /* * *********************************************************************
     * * Function name  : cashVoucherHistory
     * * Developed By   : Dilip Halder
     * * Purpose        : This function used for cashVoucherHistory History
     * * Date           : 29 August 2024
     * * **********************************************************************/
    public function cashVoucherHistory()
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
            $date['from']       = $this->input->post('from');
            $date['to']         = $this->input->post('to');
            
            if(empty($user_id)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
            elseif(empty($this->input->post('itemsPerPage'))):
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_ITEMPERPAGE'),$result);die();
            elseif(empty($this->input->post('page'))):
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_PAGE_NO'),$result);die();
            else:


                $requestFrom = 'app';
                $USERDATA    = $this->common_model->userValidate($user_id,$requestFrom);

                $resultType  = 'count';
                $totalcount  = $this->common_model->getCashVouchers($user_id,$searchBy,$searchValue,$itemsPerPage,$startIndex,$resultType,$date);

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
                $resultType    = '';
                $CashVoucherList  = $this->common_model->getCashVouchers($user_id,$searchBy,$searchValue,$itemsPerPage,$startIndex,$resultType,$date);

                if(!empty($CashVoucherList)):
                    $totalpage                  = count($totalpage);
                    $result['voucher_list']     = $CashVoucherList?$CashVoucherList:array();
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
     * * Function name  : getVoucherDetails
     * * Developed By   : Dilip Halder
     * * Purpose        : This function used for getVoucherDetails.
     * * Date           : 29 August 2024
     * * **********************************************************************/
    public function getVoucherDetails()
    {   
        $apiHeaderData      =   getApiHeaderData();
        $this->generatelogs->putLog('APP',logOutPut($_POST));
        $result                             =   array();    
        if(requestAuthenticate(APIKEY,'POST')):
            
            $SELLERID          = $this->input->post('seller_id');
            $coupon_code       = $this->input->post('coupon_code');

            if(empty($SELLERID)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_SELLER_ID'),$result);die();
            elseif(empty($coupon_code)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('COUPON_CODE_ID_EMPTY'),$result);die();
            else:

                $requestFrom = 'app';
                $USERDATA    = $this->common_model->userValidate($SELLERID,$requestFrom);

                $tableName   = "uw_cash_vouchers";
                $Fields      = array('voucher_id','coupon_code' ,'verification_code','amount','status','seller_id');
                $VoucherData = $this->common_model->getSingleDataByParticularField($Fields,$tableName,'coupon_code',(int)$coupon_code);

                if(!empty($VoucherData)):
                  $result       =   $VoucherData;
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
     * * Function name  : redeemCashVoucher
     * * Developed By   : Dilip Halder
     * * Purpose        : This function used for redeemCashVoucher History
     * * Date           : 29 August 2024
     * * **********************************************************************/
    public function redeemCashVoucher()
    {
        $apiHeaderData      =   getApiHeaderData();
        $this->generatelogs->putLog('APP',logOutPut($_POST));
        $result             =   array();    
        if(requestAuthenticate(APIKEY,'POST')):
            
            $USERID            = $this->input->post('user_id');
            $coupon_code       = $this->input->post('coupon_code');
            $SELLERID          = $this->input->post('seller_id');
            $verification_code = $this->input->post('verification_code');

            if(empty($USERID)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
            elseif(empty($coupon_code)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('COUPON_CODE_ID_EMPTY'),$result);die();
            elseif(empty($SELLERID)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_SELLER_ID'),$result);die();
            else:

                $requestFrom = 'app';
                $USERDATA    = $this->common_model->userValidate($USERID,$requestFrom);
               
                $tableName          = 'uw_cash_vouchers';
                $whereCon['where']  = array('coupon_code' => (int)$coupon_code ,'status'=> 'A');
                $VoucherData        = $this->common_model->getData('single',$tableName,$whereCon);

                if($VoucherData):

                    $tableName   = "uw_users";
                    $Fields      = array('_id','users_id' ,'totalArabianPoints','availableArabianPoints','winningBalance');
                    $userDetails = $this->common_model->getSingleDataByParticularField($Fields,$tableName,'users_id',(int)$USERID);

                    if($VoucherData['status'] == 'A' && $VoucherData['verification_code'] == $verification_code):
                        
                        $tableName   = "uw_users";
                        $Fields      = array('_id','users_id' ,'totalArabianPoints','availableArabianPoints','winningBalance');
                        $sellerDetails = $this->common_model->getSingleDataByParticularField($Fields,$tableName,'users_id',(int)$SELLERID);

                       /* adding loadbalance start */
                        $Redeemparam["load_balance_id"]        = (int)$this->geneal_model->getNextSequence('uw_loadBalance');
                        $Redeemparam["user_oid"]               = new MongoDB\BSON\ObjectId($sellerDetails['_id']['$id']);
                        $Redeemparam["user_id_deb"]            = (int)$USERID;
                        $Redeemparam["user_id_cred"]           = (int)$SELLERID;
                        $Redeemparam["upoints"]                = (float)$VoucherData['amount'];
                        $Redeemparam["record_type"]            = 'Credit';
                        $Redeemparam["narration"]              = 'Cash Prize Redeem';
                        $Redeemparam["remarks"]                = "Redeemed Prize ".$VoucherData['amount']." AED in cash. Voucher id :- $coupon_code";
                        $Redeemparam["availableArabianPoints"] = (float)$sellerDetails['availableArabianPoints'];
                        $Redeemparam["end_balance"]            = (float)$sellerDetails['availableArabianPoints'];
                        $Redeemparam["creation_ip"]            = currentIp();
                        $Redeemparam["created_at"]             = date('Y-m-d H:i');
                        $Redeemparam["created_by"]             = (int)$USERID;
                        $Redeemparam["status"]                 = "A";
                        $this->geneal_model->addData('uw_loadBalance', $Redeemparam);
                        /* adding loadbalance end */
                        
                        $param['status']      = 'C';
                        $param['update_date'] = date('Y-m-d H:i:s');
                        $param['seller_id']   =  (int)$SELLERID;
                        $this->geneal_model->editData('uw_cash_vouchers',$param,'voucher_id',(int)$VoucherData['voucher_id']);

                        $tableName          = 'uw_cash_vouchers';
                        $whereCon['where']  = array('coupon_code' => (int)$coupon_code ,'status'=> 'C' );
                        $result             = $this->common_model->getData('single',$tableName,$whereCon);
                        echo outPut(1,lang('SUCCESS_CODE'),lang('REDEEM_VOUCHER_SUCCESS'),$result);die();
                    
                    elseif($VoucherData['status'] == 'C'):
                        echo outPut(0,lang('SUCCESS_CODE'),lang('REDDEM_COUPON'),$result);die();

                    elseif($VoucherData['verification_code'] != $verification_code ):
                        echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_VERIFICATION_CODE'),$result);die();
                    else:
                        echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_COUPON'),$result);die();
                    endif;
                else:
                    echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_COUPON'),$result);die();
                endif;
        

                $winning_amount             = $this->input->post('winning_amount');
                $availableWinningBalance    = $userDetails['winningBalance'];
                $availableArabianPoints     = $userDetails['availableArabianPoints'];
                $totalArabianPoints         = $userDetails['totalArabianPoints'];
                $user_OId                   = $userDetails['_id']['$id'];

                if(!empty($userDetails) && ( $availableWinningBalance >= $winning_amount) && $winning_amount >= 200  ):

                    $availableWinningBalance = $availableWinningBalance - $winning_amount;
                    $uparam['winningBalance']           = (float)$availableWinningBalance;
                    $uparam['update_date']              = date('Y-m-d h:m');
                    $this->common_model->editData('uw_users',$uparam, 'users_id',(int)$USERID);

                    $coupon_code = 10;
                    $code        = generateRandomString($coupon_code,"n");
                    $param['voucher_id']        = (int)$this->common_model->getNextSequence('uw_cash_vouchers');
                    $param['coupon_code']       = (int)$code;
                    $param['verification_code'] = (int)generateRandomString(4,"n");;
                    $param['amount']            = (float)$winning_amount;
                    $param['users_id']          = (int)$USERID;
                    $param['user_oid']          = new MongoDB\BSON\ObjectId($userDetails['_id']['$id']);
                    $param['status']            = 'A';
                    $param['creation_ip']       = $this->input->ip_address();;
                    $param['created_at']        = date('Y-m-d H:i');
                    $param['created_by']        = (int)$USERID;
                    $result = $this->geneal_model->addData('uw_cash_vouchers', $param);

                    $loadBalance['load_balance_id'] =  (int)$this->geneal_model->getNextSequence('uw_loadBalance');
                    $loadBalance['user_oid']        =  new MongoDB\BSON\ObjectId($userDetails['_id']['$id']);
                    $loadBalance['request_id']      =  $param['voucher_id'];
                    $loadBalance['request_oid']     =  new MongoDB\BSON\ObjectId($result['_id']->{'$id'});
                    $loadBalance['user_id_deb']     =  (int)$USERID;
                    $loadBalance['user_id_cred']    =  (int)0;
                    $loadBalance["availableArabianPoints"] =   (float)$userDetails['availableArabianPoints'];
                    $loadBalance["end_balance"]            =   (float)$userDetails['availableArabianPoints'];
                    $loadBalance['record_type']     = 'Debit';
                    $loadBalance['narration']       = 'Cash Voucher';
                    $loadBalance['remarks']         = 'Winning amount '.$winning_amount.' AED moved on cash voucher';
                    $loadBalance['upoints']         = (float)$winning_amount;
                    $loadBalance['winningBalance']  = (float)$availableWinningBalance - $winning_amount;
                    $loadBalance['creation_ip']     = $this->input->ip_address();;
                    $loadBalance['created_at']      = date('Y-m-d H:i');
                    $loadBalance['created_by']      = (int)$USERID;
                    $loadBalance['status']          = 'A';
                    $this->geneal_model->addData('uw_loadBalance', $loadBalance);

                    if(!empty($result)):
                        echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$result);die();
                    else:
                        echo outPut(0,lang('FORBIDDEN_CODE'),lang('BAD_REQUEST_ACTION'),$result);die();
                    endif;
                else:
                    echo outPut(0,lang('SUCCESS_CODE'),lang('MIN_WITHDRAW_AMOUNT_ERROR'),$result);
                endif;
            endif;
        else:
            echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
        endif;
    }

    /* * *********************************************************************
     * * Function name  : paymentResponce
     * * Developed By   : Dilip Halder
     * * Purpose        : This function used for paymentResponce
     * * Date           : 29 August 2024
     * * **********************************************************************/
    public function paymentResponce()
    {
        $apiHeaderData      =   getApiHeaderData();
        $this->generatelogs->putLog('APP',logOutPut($_POST));
        $result                             =   array();    
        if(requestAuthenticate(APIKEY,'POST')):
            
            $USERID             = $this->input->post('user_id');
            $request_id         = $this->input->post('request_id');
            $transaction_id     = $this->input->post('transaction_id');
            $order_id           = $this->input->post('order_id');
            $device_type        = $this->input->post('device_type');
            $bank_ref_no        = $this->input->post('bank_ref_no');
            $order_status       = $this->input->post('order_status');
            $payment_mode       = $this->input->post('payment_mode');
            $card_name          = $this->input->post('card_name');
            $status_message     = $this->input->post('status_message');
            $currency           = $this->input->post('currency');
            $bank_qsi_no        = $this->input->post('bank_qsi_no');
            $bank_receipt_no    = $this->input->post('bank_receipt_no');
            $status              = $this->input->post('status');

            if(empty($USERID)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
            
            elseif(empty($request_id)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('REQUEST_NO'),$result);die();
            
            elseif(empty($order_id)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('ORDER_ID_EMPTY'),$result);die();
            
            elseif(empty($device_type)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_DEVICE_TYPE'),$result);die();
           
            elseif(empty($bank_ref_no)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_REFEREANCE_NO'),$result);die();

            elseif(empty($order_status) || empty($status)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('ORDER_ID_EMPTY'),$result);die();
            else:

                $requestFrom = 'app';
                $USERDATA    = $this->common_model->userValidate($USERID,$requestFrom);
               
                $tableName          = 'uw_topup';
                $whereCon['where']  = array('request_id' => (int)$request_id , 'users_id' => (int)$USERID);
                $RequestData        = $this->common_model->getData('single',$tableName,$whereCon);


                if(!empty($RequestData)):

                    if($RequestData['status'] == 'P' && (int)$RequestData['request_id'] == (int)$request_id && (int)$RequestData['users_id'] == (int)$USERID ):
                       
                        // $param['user_id']         = (int)$USERID;
                        // $param['request_id']      = $request_id;
                        $param['transaction_id']  = (int)$transaction_id;
                        $param['order_id']        = (int)$order_id;
                        $param['device_type']     = $device_type;
                        $param['bank_ref_no']     = $bank_ref_no;
                        $param['order_status']    = $order_status;
                        $param['payment_mode']    = $payment_mode;
                        $param['card_name']       = $card_name;
                        $param['status_message']  = $status_message;
                        $param['currency']        = $currency;
                        $param['bank_qsi_no']     = $bank_qsi_no;
                        $param['bank_receipt_no'] = $bank_receipt_no;
                        $param['status']          = $status;
                        $param['update_date']     = date("Y-m-d H:i:s");
                        $whereCon                 = array('request_id' => (int)$request_id , 'users_id' => (int)$USERID ,'status'=> 'P');
                        $this->common_model->editDataByMultipleCondition($tableName,$param,$whereCon);


                        if($status_message == "Success"):
                            //Creadited Topup Amount..
                            $TopupAmount = $RequestData['amount'];
                            $this->geneal_model->creaditPoints($TopupAmount,$USERID);

                            $tableName   = "uw_users";
                            $Fields      = array('_id','users_id' ,'totalArabianPoints','availableArabianPoints','winningBalance','referrel_amount');
                            $userDetails = $this->common_model->getSingleDataByParticularField($Fields,$tableName,'users_id',(int)$USERID);

                            // REFERRAL AMOUNT RELEASING FUNCTION..
                            if(!empty($userDetails['referrel_amount']) && $userDetails['referrel_amount'] > 0):
                                $this->common_model->referralAmountRelease($USERID,$TopupAmount);
                            endif;

                            // Load Balance .. 
                            $loadBalance['load_balance_id'] = (int)$this->geneal_model->getNextSequence('uw_loadBalance');
                            $loadBalance['user_oid']        = new MongoDB\BSON\ObjectId($userDetails['_id']['$id']);
                            $loadBalance['request_id']      = $param['voucher_id'];
                            $loadBalance['request_oid']     = new MongoDB\BSON\ObjectId($result['_id']->{'$id'});
                            $loadBalance['user_id_deb']     = (int)$USERID;
                            $loadBalance['user_id_cred']    = (int)0;
                            $loadBalance["availableArabianPoints"] = (float)$userDetails['availableArabianPoints'];
                            $loadBalance["end_balance"]            = (float)$userDetails['availableArabianPoints']+$TopupAmount;
                            $loadBalance['record_type']     = 'Credit';
                            $loadBalance['narration']       = 'Online recharge';
                            $loadBalance['remarks']         = 'Online rechagred '.$TopupAmount.' aed.';
                            $loadBalance['upoints']         = (float)$TopupAmount;
                            $loadBalance['creation_ip']     = $this->input->ip_address();;
                            $loadBalance['created_at']      = date('Y-m-d H:i');
                            $loadBalance['created_by']      = (int)$USERID;
                            $loadBalance['status']          = 'A';
                            $this->geneal_model->addData('uw_loadBalance', $loadBalance);
                          
                            $result = $loadBalance['remarks'];
                            echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$result);die();
                         
                        elseif($status_message == "Aborted"):
                            $result = $RequestData;
                            echo outPut(1,lang('SUCCESS_CODE'),lang('PAYMENT_CANCELLED'),$result);die();
                         
                        elseif($status_message == "Failure"):
                            $result = $RequestData;
                            echo outPut(1,lang('SUCCESS_CODE'),lang('PAYMENT_FAILED'),$result);die();
                        endif;

                    elseif($RequestData['status'] == 'C' && $RequestData['order_status'] == 'Success' ):
                        $result = $RequestData;
                        echo outPut(1,lang('SUCCESS_CODE'),lang('PAYMENT_CAPTURED_ALREADY'),$result);die();
                    elseif($RequestData['status'] == 'CL' && $RequestData['order_status'] == 'Aborted' ):
                        $result = $RequestData;
                        echo outPut(1,lang('SUCCESS_CODE'),lang('PAYMENT_CAPTURED_ALREADY'),$result);die();
                    else:
                        echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_TRANSACTION'),$result);die();
                    endif;
                else:
                    echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_TRANSACTION'),$result);die();
                endif;
            endif;
        else:
            echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
        endif;
    }

    /* * *********************************************************************
     * * Function name  : generatePaymentUrl
     * * Developed By   : Dilip Halder
     * * Purpose        : This function used to generate payment url.
     * * Date           : 27 JUNE 2024
     * * **********************************************************************/
    public function generatePaymentUrl()
    {   
        try {
            $apiHeaderData      =   getApiHeaderData();
            $this->generatelogs->putLog('APP',logOutPut($_POST));
            $result             =   array();    

            echo outPut(0,lang('FORBIDDEN_CODE'),lang('ONLINE_RECHARGE_DISABLED'),$result);die();
            
            if(!$this->input->post('user_id')):
                echo outPut(0,lang('FORBIDDEN_CODE'),lang('USER_ID_INCORRECT'),$result);
            elseif(!$this->input->post('amount')):
                echo outPut(0,lang('FORBIDDEN_CODE'),lang('AMOUNT_EMPTY'),$result);
            else:
                if(requestAuthenticate(APIKEY,'POST')):
                    $USERID    = $this->input->post('user_id');
                    $amount    = $this->input->post('amount');
                    $plateform = 'app';
                    $result    = $this->common_model->topUpAmount($USERID,$amount,$plateform);
        
                    echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$result);
                else:
                    echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
                endif;
            endif;
        } catch (\Throwable $th) {
            echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),array());
        }
    }
}
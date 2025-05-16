<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Raffle extends CI_Controller {
    
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
     * * Function name  : campaignList
     * * Developed By   : Dilip Halder
     * * Purpose        : This function used for campaignList 
     * * Date           : 22 January 2025
     * * **********************************************************************/
    public function campaignList()
    {   
        $apiHeaderData      =   getApiHeaderData();
        $this->generatelogs->putLog('APP',logOutPut($_POST));
        $result                             =   array();    
        if(requestAuthenticate(APIKEY,'GET')):
            $UserID            = $this->input->get('user_id');
            if(empty($UserID) || !is_numeric($UserID) ):
                echo outPut(0,lang('SUCCESS_CODE'),lang('LOGIN_REQUIRED'),$result);die();
            else:
                $tblName           = 'uw_users';
                $whereCon['where'] = array( 'users_id' => (int)$UserID , 'status' => 'A');
                $UserDetails       = $this->common_model->getData('single',$tblName,$whereCon);
                if(!empty($UserDetails)):

                    $tblName    = 'uw_products';
                    $show_on    = $this->input->get('show_on');
                    $whereCon['where'] = array(
                        'enable_raffle_ticket' => 'Enable' , 
                        'draw_date' => array('$gte' => date('Y-m-d')) , 
                        'status'    => 'A', 
                        'show_on'   =>  array('$in' => array($show_on) )
                    );
                    $shortField        = array('seq_order' => -1 );
                    $productList       = $this->common_model->getData('multiple',$tblName,$whereCon,$shortField);
                    if(!empty($productList)):
                        $campaignList = array();
                        $CurrentDateTime = strtotime(date('Y-m-d H:i'));
                        foreach($productList as $key => $item):
                          $DrawDateTime    = strtotime($item['draw_date'].' '.$item['draw_time']);
                          if($DrawDateTime > $CurrentDateTime):
                            $tblName                  = 'uw_prize';
                            $whereCon['where']        = array('product_id' => (int)$item['products_id'] );
                            $prizeList                = $this->common_model->getData('single',$tblName,$whereCon);
                            $result['campaign'] = $item;
                            $result['prize']    = $prizeList;
                            // $campaignList[] = $result;
                            if(!empty($prizeList)):
                                array_push($campaignList,$result);
                            endif;
                          endif;
                        endforeach;

                        if(!empty($campaignList)):
                            echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_MSG'),$campaignList);
                        else:
                            echo outPut(0,lang('SUCCESS_CODE'),lang('DATA_NOT_FOUND'),$result);
                        endif;
                    else:
                        echo outPut(0,lang('SUCCESS_CODE'),lang('DATA_NOT_FOUND'),$result);
                        // echo outPut(0,lang('SUCCESS_CODE'),lang('LOGIN_REQUIRED'),$results);   
                    endif;
                else:
                    echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_USER_ID'),$results);   
                endif;
            endif;
        else:
            echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
        endif;
    }  

    /* * *********************************************************************
    * * Function name : checkWinnerType
    * * Developed By  : Dilip Halder
    * * Purpose       : This function used to checkWinnerType.
    * * Date          : 27 January 2025
    * * **********************************************************************/
    public function checkWinnerType()
    {
        $apiHeaderData      =   getApiHeaderData();
        $this->generatelogs->putLog('APP',logOutPut($_POST));
        $result             =   array();

        if(requestAuthenticate(APIKEY,'POST')):
                $USERID   =  $this->input->post('users_id');
                $orderID  =  $this->input->post('tickect_id');

                if(empty($USERID)):
                    echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
                elseif(empty($orderID)):
                    echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_TICKET'),$result);die();
                else:
                    /*-------------USER QUERY START HERE-------------------*/ 
                    $tblName            = 'uw_users';
                    $whereCon['where']  = array('users_id' => (int)$USERID ,'status' => 'A' );
                    $USerData           = $this->common_model->getData('count',$tblName,$whereCon);
                    /*-------------USER DETAILS END HERE-------------------*/ 
                    if(!empty($USerData)):

                        /*-------------DRAW ALERT START HERE-------------------*/ 
                        $whereCon1['where']= array('order_id' => $orderID);
                        $orderDetails      = $this->geneal_model->getOrderDetail($whereCon1);
                        $Drawdate =  date('d/m/y',strtotime($orderDetails['draw_dateTime']));
                        $Drawtime =  date('h:i A',strtotime($orderDetails['draw_dateTime']));
                        if( strtotime($orderDetails['draw_dateTime']) > strtotime(date('Y-m-d H:i'))):
                            echo outPut(0,lang('SUCCESS_CODE'),"The draw is scheduled for $Drawdate at $Drawtime. Please check the results after the draw.",$result);die();
                        endif;
                        /*-------------DRAW ALERT END HERE-------------------*/ 

                        /*-------------WINNER QUERY START HERE-------------------*/ 
                        $tblName            = 'uw_raffle_winner';
                        $whereCon['where']  = array('order_id' => $orderID);
                        $WinnerData         = $this->common_model->getData('count',$tblName,$whereCon);
                        if(!empty($WinnerData)):
                            $result['winner_type'] = 'RaffleWinner';
                            echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$result);  die();  
                        else:
                            $tblName            = 'uw_uwin_winner';
                            $whereCon['where']  = array('order_id' => $orderID);
                            $UwinnData          = $this->common_model->getData('count',$tblName,$whereCon);
                            if(!empty($UwinnData)):
                                $result['winner_type'] = 'uwinnWinner';
                                echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$result);  die();  
                            else:
                                $result['winner_type'] = [];
                                echo outPut(0,lang('SUCCESS_CODE'),lang('NOT_WINNER'),$result);  die();  
                            endif;

                        endif;
                        /*-------------WINNER DETAILS END HERE-------------------*/ 
                    else:
                        echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_INCORRECT'),$result);die();
                    endif;
                endif;
                echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);    
        else:
            echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
        endif;
    }

    /* * *********************************************************************
     * * Function name : checkWinner
     * * Developed By  : Dilip Halder
     * * Purpose       : This function used to check winner.
     * * Date          : 27 January 2025
     * * **********************************************************************/
    public function checkWinner()
    {
        $apiHeaderData      =   getApiHeaderData();
        $this->generatelogs->putLog('APP',logOutPut($_POST));
        $result             =   array();

        if(requestAuthenticate(APIKEY,'POST')):
                $USERID   =  $this->input->post('users_id');
                $orderID  =  $this->input->post('tickect_id');

                if(empty($USERID)):
                    echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
                elseif(empty($orderID)):
                    echo outPut(0,lang('SUCCESS_CODE'),lang('EMPTY_TICKET'),$result);die();
                else:

                    /*-------------USER QUERY START HERE-------------------*/ 
                    $tblName            = 'uw_users';
                    $whereCon['where']  = array('users_id' => (int)$USERID ,'status' => 'A' );
                    $USerData           = $this->common_model->getData('count',$tblName,$whereCon);
                    /*-------------USER DETAILS END HERE-------------------*/ 
                    if(!empty($USerData)):

                        /*-------------WINNER QUERY START HERE-------------------*/ 
                        $tblName            = 'uw_raffle_winner';
                        $whereCon['where']  = array('order_id' => $orderID);
                        $WinnerData         = $this->common_model->getData('multiple',$tblName,$whereCon);
                        /*-------------WINNER DETAILS END HERE-------------------*/ 
                        if(!empty($WinnerData)):
                            $totalPrizeAmount = 0;
                            $totalCode        = array();
                            $totalPaidPrizeAmount = 0;
                            foreach ($WinnerData as $key => $items) :
                              if($items['status']== 'A'):
                                $totalPrizeAmount = $totalPrizeAmount + $items['amount_in_number'];
                              elseif($items['status']== 'C'):
                                $totalPaidPrizeAmount = $totalPaidPrizeAmount + $items['amount_in_number'];
                              endif;
                                $totalCode[]      = $items['coupons'];
                            endforeach;
                            $totalCode = implode(' / ', $totalCode);
                        endif;

                        if(!empty($WinnerData)):
                            $winerList = array();
                            $winerList['order_id']          = $items['order_id'];
                            $winerList['winner_type']       = $items['winner_type'];
                            if($USERID == '100000000000110'):
                                $winerList['amount']            = $items['amount'];
                            else:
                                if($items['status'] == 'C'):
                                    if($totalPaidPrizeAmount == 0):
                                        $winerList['amount']            = $items['amount'];
                                    else:
                                        $winerList['amount']            = $totalPaidPrizeAmount;
                                    endif;
                                else:
                                    $winerList['amount']                = $totalPrizeAmount;
                                endif;
                            endif;
                            $winerList['seller_first_name'] = $items['seller_first_name'];
                            $winerList['seller_last_name']  = $items['seller_last_name'];
                            $winerList['seller_mobile']     = $items['seller_mobile'];
                            $winerList['store_name']        = $items['store_name'];
                            $winerList['pos_number']        = $items['pos_number'];
                            $winerList['coupons']           = $totalCode;
                            $winerList['status']            = $items['status'];
                            $winerList['created_at']        = $items['created_at'];
                            $winerList['updated_at']        = $items['updated_at']?$items['updated_at']:'';
                            $winerList['updated_by']        = $items['updated_by']?$items['updated_by']:'';
                            
                           

                            if($USERID != '100000000000110'):
                                if($items['status'] == 'C'):
                                    $resultss = array($winerList);
                                endif;
                                if(!empty($WinnerData)  && $WinnerData[0]['amount_in_number']  == 0):
                                    $Prize = $WinnerData[0]['amount'];
                                    $MESSAGE = str_replace('###WINNINGDATA###', $Prize, lang('WINNNING_PRIZE'));
                                    echo outPut(1,lang('SUCCESS_CODE'),$MESSAGE,$resultss);die();
                                else:
                                    if(!empty($WinnerData)  && $totalPrizeAmount >= 1000):
                                        $MESSAGE = str_replace('###WINNINGDATA###', 'AED '.$totalPrizeAmount, lang('WINNNING_PRIZE'));
                                        echo outPut(1,lang('SUCCESS_CODE'),$MESSAGE,$resultss);die();
                                    endif;
                                endif;
                            endif;

                            $result = array($winerList);
                            echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$result);  die();  
                        else:
                            echo outPut(0,lang('SUCCESS_CODE'),lang('NOT_WINNER'),$result);die();
                        endif;
                    else:
                        echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_INCORRECT'),$result);die();
                    endif;
                endif;
                echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);    
        else:
            echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
        endif;
    }

    /* * *********************************************************************
    * * Function name : winnerRedeem
    * * Developed By  : Dilip Halder
    * * Purpose       : This function used to redeem winner.
    * * Date          : 27 January 2025
    * * **********************************************************************/
    public function winnerRedeem()
    {
        $apiHeaderData      =   getApiHeaderData();
        $this->generatelogs->putLog('APP',logOutPut($_POST));
        $result             =   array();

        if(requestAuthenticate(APIKEY,'POST')):
            $USERID  =  $this->input->post('users_id');
            $orderID =  $this->input->post('tickect_id');
            $amount  =  $this->input->post('amount');

            if(empty($USERID)):
                echo outPut(1,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
            elseif(empty($orderID)):
                echo outPut(1,lang('SUCCESS_CODE'),lang('EMPTY_TICKET'),$result);die();
            elseif(empty($amount) && $USERID != '100000000000110'):
                echo outPut(1,lang('SUCCESS_CODE'),lang('EMPTY_Amount'),$result);die();
            else:

                /*-------------USER QUERY START HERE-------------------*/ 
                $tblName            = 'uw_users';
                $whereCon['where']  = array('users_id' => (int)$USERID ,'status' => 'A' );
                $USerData           = $this->common_model->getData('single',$tblName,$whereCon);

                /*-------------USER DETAILS END HERE-------------------*/ 
                if(!empty($USerData)):

                    /*-------------WINNER QUERY START HERE-------------------*/ 
                    $tblName            = 'uw_raffle_winner';
                    $whereCon['where']  = array('order_id' => $orderID);
                    $WinnerData         = $this->common_model->getData('multiple',$tblName,$whereCon);
                    /*-------------WINNER DETAILS END HERE-------------------*/ 

                    if(!empty($WinnerData)):
                        $totalPrizeAmount = 0;
                        $totalCode        = array();
                        foreach ($WinnerData as $key => $items) :
                          if($items['status']== 'A'):
                            $totalPrizeAmount = $totalPrizeAmount + $items['amount_in_number'];
                            $totalCode[]      = $items['coupons'];
                          endif;
                        endforeach;
                    endif;

                    if(!empty($WinnerData)):
                        if($totalPrizeAmount == $amount):
                            $updateParams['status']     = 'C';
                            $updateParams["updated_at"] = date('Y-m-d H:i');
                            $updateParams["updated_by"] = (int)$USERID;
                            $tblName          = 'uw_raffle_winner';
                            $WInnner_whereCon = array('order_id' => $orderID, 'status' => 'A');
                            $result           = $this->common_model->editMultipleDataByMultipleCondition($tblName, $updateParams,$WInnner_whereCon);

                            $tblName            = 'uw_loadBalance';
                            $whereCon1['where'] = array('order_id' => $orderID,'narration' => 'Raffle Order');
                            $loadBalanceData    = $this->common_model->getFieldInArray('product_oid',$tblName,$whereCon1);

                            /* Load Balance Table -- after Sign Up*/
                            $Redeemparam["load_balance_id"]          = (int)$this->geneal_model->getNextSequence('uw_loadBalance');
                            $Redeemparam["user_oid"]                 = new MongoDB\BSON\ObjectId($USerData['_id']->{'$id'});
                            $Redeemparam["product_oid"]              = new MongoDB\BSON\ObjectId((string)$loadBalanceData[0]);
                            $Redeemparam["order_id"]                 = $orderID;
                            $Redeemparam["user_id_deb"]              = (int)$USERID;
                            $Redeemparam["user_id_cred"]             = (int)0;
                            $Redeemparam["upoints"]                  = (float)$totalPrizeAmount;
                            $Redeemparam["record_type"]              = 'Debit';
                            $Redeemparam["narration"]                = 'Raffle Cash Prize Redeem';
                            $Redeemparam["remarks"]                  = "Redeemed Prize ".$totalPrizeAmount." AED in cash";
                            $Redeemparam["availableArabianPoints"]   = (float)$USerData['availableArabianPoints'];
                            $Redeemparam["end_balance"]              = (float)$USerData['availableArabianPoints'];
                            $Redeemparam["creation_ip"]              = currentIp();
                            $Redeemparam["created_at"]               = date('Y-m-d H:i');
                            $Redeemparam["created_by"]               = (int)$USERID;
                            $Redeemparam["status"]                   = "A";
                            $this->geneal_model->addData('uw_loadBalance',$Redeemparam);

                            /* Rresponce Data */ 
                            $winerList['order_id']          = $items['order_id'];
                            $winerList['winner_type']       = $items['winner_type'];
                            if($USERID == '100000000000110'):
                                $winerList['amount']            = $items['amount'];
                            else:
                                $winerList['amount']            = $totalPrizeAmount;
                            endif;
                            $winerList['seller_first_name'] = $items['seller_first_name'];
                            $winerList['seller_last_name']  = $items['seller_last_name'];
                            $winerList['seller_mobile']     = $items['seller_mobile'];
                            $winerList['coupons']           = $totalCode;
                            $winerList['status']            = $items['status'];
                            $winerList['created_at']        = $items['created_at'];
                            $winerList['updated_at']        = $Redeemparam['created_at'];
                            $winerList['updated_by']        = $Redeemparam['created_by'];
                            $results = array($winerList);

                            echo outPut(1,lang('SUCCESS_CODE'),lang('COUPON_REDEEMED_SUCCESFULLY'),$results);die();
                        elseif($WinnerData[0]['status'] =='C'):
                            $results = [];
                            echo outPut(1,lang('SUCCESS_CODE'),lang('ALREADY_REDEEM'),$results);die();
                        else:
                            echo outPut(1,lang('SUCCESS_CODE'),lang('RECHECK_AGAIN'),$result);die();
                        endif;
                    else:
                        echo outPut(0,lang('SUCCESS_CODE'),lang('NOT_WINNER'),$result);die();
                    endif;
                else:
                    echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_INCORRECT'),$result);die();
                endif;
            endif;
            echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$results);    
        else:
            echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
        endif;
    }

    /* * *********************************************************************
     * * Function name  : summeryReportRaffle
     * * Developed By   : Dilip Halder
     * * Purpose        : This function used for summeryReportRaffle 
     * * Date           : 24 January 2025
     * * **********************************************************************/
    public function summeryReportRaffle()
    {
        $apiHeaderData      =   getApiHeaderData();
        $this->generatelogs->putLog('APP',logOutPut($_POST));
        $result                             =   array();    
        if(requestAuthenticate(APIKEY,'POST')):

            $USERID         = $this->input->post('user_id');
            $from           = $this->input->post('from');
            $to             = $this->input->post('to');
            if($this->input->post('product_title')):
              $product_title  = $this->input->post('product_title');
            endif;

            if(empty($USERID)):
                echo outPut(0,lang('SUCCESS_CODE'),lang('USER_ID_EMPTY'),$result);die();
            else:
 
                $tblName           = 'uw_users'; 
                $whereCon['where'] =  array('users_id'=> (int)$USERID);
                $UserData          = $this->common_model->getData('single',$tblName,$whereCon);
                $UserData['from']  = $this->input->post('from');
                $UserData['to']    = $this->input->post('to');
                if(!empty($UserData) && $UserData['status'] == 'A' && ($UserData['is_verify']  == 'Y' || $UserData['is_varified']  == 'Y') ):
                    
                    $whereCondition['user_oid']    = new MongoDB\BSON\ObjectId($UserData['_id']->{'$id'});
                    if(!empty($from)):
                        $whereCondition['created_at']['$gte'] = date('Y-m-d H:i' ,strtotime($from));
                    endif;
                    if(!empty($to)):
                        $whereCondition['created_at']['$lte'] = date('Y-m-d H:i' ,strtotime($to));
                    endif;
                    $RaffleCampaign              = $this->common_model->getRaffleSummary($whereCondition ,$product_title); 
                    if(!empty($RaffleCampaign)):
                        $RaffleSummery  = array();
                        $RaffleSummery1 = array();
                        foreach ($RaffleCampaign as $key => $items):
                            $RaffleSummery['total_raffle_orders']               += $items['sales'];
                            $RaffleSummery['total_raffle_commisson']            += $items['commissionAmount'];
                            $RaffleSummery['total_raffle_orders_canceled']      += $items['totalcancelOrderAmount'];
                            $RaffleSummery['total_raffle_cancelled_commisson']  += $items['commissionCanceledAmount'];
                            $RaffleSummery['total_raffle_prize_redeemed']       += $items['totalCustomerPaid'];
                        endforeach;
                        $RaffleSummery['total_due'] = $RaffleSummery['total_raffle_orders']-$RaffleSummery['total_raffle_commisson']-$RaffleSummery['total_raffle_prize_redeemed'];
                        $RaffleSummery1[] = $RaffleSummery;
                    else:
                        $RaffleSummery1 = [];
                    endif;
                    // echo "<pre>";print_r($RaffleSummery);die();
                    $result['RaffleCampaign']   = $RaffleCampaign;
                    $result['RaffleSummery']    = $RaffleSummery1;
                    echo outPut(1,lang('SUCCESS_CODE'),lang('SUCCESS_ACTION'),$result);die();
                else:
                    echo outPut(0,lang('SUCCESS_CODE'),lang('INVALID_USER_ID'),$result);die();
                endif;

            endif;
        else:
            echo outPut(0,lang('FORBIDDEN_CODE'),lang('FORBIDDEN_MSG'),$result);
        endif;
    } 

}
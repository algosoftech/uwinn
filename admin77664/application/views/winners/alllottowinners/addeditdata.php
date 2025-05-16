<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
<script>
$(function(){
   $("#date").datepicker({dateFormat:'yy-mm-dd',changeMonth: true,changeYear: true,yearRange:"1970:<?php echo date('Y')?>"});
});
</script>
<div class="pcoded-main-container">
    <div class="pcoded-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo getCurrentDashboardPath('dashboard/index'); ?>"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('ALLLOTTOWINNERSDATA',getCurrentControllerPath('index')); ?>"> Winners</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);"><?=$EDITDATA?'Edit':'Add'?> Winners</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->
        <!-- [ Main Content ] start -->
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                  <div class="card-header">
                    <h5><?=$EDITDATA?'Edit':'Add'?> Winners</h5>

                    <?php $winnerID = $this->session->userdata('productID4winners'); ?>

                    <a href="<?php echo correctLink('ALLLOTTOWINNERSDATA',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
                    <a href="<?php echo getCurrentControllerPath('winnerannouncement/'.$winnerID)?>" class="btn btn-sm btn-primary pull-right mr-2">Winner Announce</a>
                  </div>
                     <div class="card-body">
                        <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                            <div class="basic-login-inner">
                                <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="winners_id"/>
                                <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$EDITDATA['winners_id']?>"/>
                                <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$EDITDATA['winners_id']?>"/>
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                            </div>

                            <div class="row">
                                 <div class="col-sm-12">
                                  
                                    <div class="table-responsive">
                                        <h3 class="">Straight Coupon List</h3>
                                        <table id="simpletable" class="table table-striped table-bordered nowrap dataTable" role="grid" aria-describedby="simpletable_info">
                                            <thead style="text-align: center;">
                                                  <tr role="row">
                                                    <th width="5%" style="text-align: center;">S.No.</th>
                                                    <th width="20%">Matches</th>
                                                    <th width="10%">No. of Winners</th>
                                                    <th width="20%">Prize Money Per Winners</th>
                                                    <th width="20%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>    
                                                <?php for ($i=0; $i <$prize_data['lotto_type'] ; $i++): $k = 1+$i; ?>
                                                    <tr>
                                                        <td width="5%"><?=$k; ?></td>
                                                        <td width="5%"><?='Match of '. $k.'/'.$prize_data['lotto_type'] ; ?></td>
                                                        <td width="10%">
                                                             <?php if($result['straightMatchedCoupons']):
                                                                    $MixCoupons = 1 ;
                                                                    $ArrayCount = 0;
                                                                    foreach ($result['straightMatchedCoupons'] as $key => $item):
                                                                         for ($j=0; $j <$prize_data['lotto_type'] ; $j++): $l = 1+$j;  
                                                                                if($item[$j]['matched_count'] == $k && $item[$j]['matched_count'] != 0):
                                                                                    $ArrayCount += $MixCoupons;
                                                                                endif;
                                                                         endfor;  
                                                                    endforeach;
                                                                echo $ArrayCount;
                                                            endif; ?>
                                                        </td>
                                                        <td width="20%" class="text-left"> 
                                                               <?php
                                                                if($ArrayCount > 0):
                                                                    //if prize amount is fixed will show this if data from if conditions. 
                                                                    if(in_array('Prize'.$k, $prize_data['stright_prize_type'])):
                                                                        echo "Fixed prize<br>";
                                                                        echo $prize_data['stright_prize'.$k] .' X ' .$ArrayCount .' = '. $prize_data['stright_prize'.$k] *$ArrayCount;
                                                                        echo "<br> Each winner will get AED ".$prize_data['stright_prize'.$k];

                                                                    else:
                                                                    //prize amount is shared will show this if data from if conditions. 
                                                                        echo "Shared prize<br>";
                                                                        $prize_amount = $prize_data['stright_prize'.$k]/$ArrayCount;
                                                                        echo 'AED '.number_format($prize_amount,'2') . 'X' .$ArrayCount .' ( AED '.number_format($prize_data['stright_prize'.$k],'2').' )';
                                                                    endif;

                                                                else:
                                                                     echo "No winner yet<br>";
                                                                     echo 'AED '.$prize_data['stright_prize'.$k];
                                                                endif;
                                                                ?>
                                                        </td>
                                                        <td width="20%" class="text-center">
                                                            <?php if($ArrayCount >0 ): ?>
                                                            <div class="btn-group">
                                                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                                                                <ul class="dropdown-menu" role="menu">
                                                                    <li>
                                                                        <a href="<?php echo getCurrentControllerPath('addwinners/straight/'.$k)?>"><i class="fas fa-edit"></i> View Winner List</a>
                                                                    </li>
                                                                    <?php if($ALLDATAINFO['status'] == 'A'): ?>
                                                                      <li><a href="<?php echo getCurrentControllerPath('changestatus/'.$ALLDATAINFO['lotto_winners_id'].'/I')?>"><i class="fas fa-thumbs-down"></i> Inactive</a></li>
                                                                    <?php elseif($ALLDATAINFO['status'] == 'I'): ?>
                                                                      <li>
                                                                        <a href="<?php echo getCurrentControllerPath('changestatus/'.$ALLDATAINFO['lotto_winners_id'].'/A')?>">
                                                                            <i class="fas fa-thumbs-up"></i> Active
                                                                        </a>
                                                                      </li>
                                                                    <?php endif; ?>
                                                                   
                                                                </ul>
                                                            </div>
                                                        <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endfor; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                    
                                    <div class="table-responsive">
                                        <h3 class="">Rumble Mix Coupon List</h3>
                                        <table id="simpletable" class="table table-striped table-bordered nowrap dataTable" role="grid" aria-describedby="simpletable_info">
                                            <thead style="text-align: center;">
                                                  <tr role="row">
                                                    <th width="5%" style="text-align: center;">S.No.</th>
                                                    <th width="20%">Matches</th>
                                                    <th width="10%">No. of Winners</th>
                                                    <th width="20%">Prize Money Per Winners</th>
                                                    <th width="20%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>    
                                            
                                                <?php for ($i=0; $i <$prize_data['lotto_type'] ; $i++): $k = 1+$i; ?>
                                                    <tr>
                                                        <td width="5%"><?=$k; ?></td>
                                                        <td width="5%"><?='Match of '. $k.'/'.$prize_data['lotto_type'] ; ?></td>
                                                        <td width="10%">

                                                            <?php if($result['rumbleMixCoupons']):
                                                                    $MixCoupons = 1 ;
                                                                    $ArrayCount = 0;
                                                                    foreach ($result['rumbleMixCoupons'] as $key => $item):
                                                                         for ($j=0; $j <$prize_data['lotto_type'] ; $j++): $l = 1+$j;  
                                                                                if($item[$j]['matched_count'] == $k && $item[$j]['matched_count'] != 0):
                                                                                    $ArrayCount += $MixCoupons;
                                                                                endif;
                                                                         endfor;  
                                                                    endforeach;
                                                                echo $ArrayCount;
                                                            endif; ?>
                                                                
                                                        </td>


                                                        <td width="20%" class="text-left"> 
                                                                <?php
                                                                if($ArrayCount > 0):
                                                                    //if prize amount is fixed will show this if data from if conditions. 
                                                                    if(in_array('Prize'.$k, $prize_data['rumble_mix_prize_type'])):
                                                                        echo "Fixed prize<br>";
                                                                        echo $prize_data['rumble_mix_prize'.$k] .' X ' .$ArrayCount .' = '. $prize_data['rumble_mix_prize'.$k] *$ArrayCount;
                                                                        echo "<br> Each winner will get AED ".$prize_data['rumble_mix_prize'.$k];

                                                                    else:
                                                                    //prize amount is shared will show this if data from if conditions. 
                                                                        echo "Shared prize<br>";
                                                                        $prize_amount = $prize_data['rumble_mix_prize'.$k]/$ArrayCount;
                                                                        echo 'AED '.number_format($prize_amount,'2') . 'X' .$ArrayCount .' ( AED '.number_format($prize_data['rumble_mix_prize'.$k],'2').' )';
                                                                    endif;

                                                                else:
                                                                     echo "No winner yet<br>";
                                                                     echo 'AED '.$prize_data['rumble_mix_prize'.$k];
                                                                endif;
                                                                ?>
                                                        </td>
                                                        
                                                        <td width="20%" class="text-center">

                                                            <?php if($ArrayCount >0): ?>
                                                            <div class="btn-group">
                                                                  <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                                                                    <ul class="dropdown-menu" role="menu">
                                                                    <li>
                                                                        <a href="<?php echo getCurrentControllerPath('addwinners/rumblemix/'.$k)?>"><i class="fas fa-edit"></i> View Winner List</a>
                                                                    </li>
                                                                    <?php if($ALLDATAINFO['status'] == 'A'): ?>
                                                                      <li><a href="<?php echo getCurrentControllerPath('changestatus/'.$ALLDATAINFO['lotto_winners_id'].'/I')?>"><i class="fas fa-thumbs-down"></i> Inactive</a></li>
                                                                    <?php elseif($ALLDATAINFO['status'] == 'I'): ?>
                                                                      <li>
                                                                        <a href="<?php echo getCurrentControllerPath('changestatus/'.$ALLDATAINFO['lotto_winners_id'].'/A')?>">
                                                                            <i class="fas fa-thumbs-up"></i> Active
                                                                        </a>
                                                                      </li>
                                                                    <?php endif; ?>
                                                                   
                                                                </ul>
                                                            </div>
                                                        <?php  endif; ?>
                                                          </td>

                                                    </tr>
                                                <?php endfor; ?>

                                        </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-sm-12">
                                     
                                    <div class="table-responsive">
                                        <h3 class="">Reverse Coupon List</h3>
                                        <table id="simpletable" class="table table-striped table-bordered nowrap dataTable" role="grid" aria-describedby="simpletable_info">
                                            <thead style="text-align: center;">
                                                <tr role="row">
                                                    <th width="5%" style="text-align: center;">S.No.</th>
                                                    <th width="20%">Matches</th>
                                                    <th width="10%">No. of Winners</th>
                                                    <th width="20%">Prize Money Per Winners</th>
                                                    <th width="20%">Action</th>
                                                </tr>
                                            </thead>
                                              <tbody>    
                                                <?php for ($i=0; $i <$prize_data['lotto_type'] ; $i++): $k = 1+$i; ?>
                                                    <tr>
                                                        <td width="5%"><?=$k; ?></td>
                                                        <td width="5%"><?='Match of '. $k.'/'.$prize_data['lotto_type'] ; ?></td>
                                                        <td width="10%">

                                                            <?php if($result['reverseCoupons']):
                                                                    $MixCoupons = 1 ;
                                                                    $ArrayCount = 0;
                                                                    foreach ($result['reverseCoupons'] as $key => $item):
                                                                         for ($j=0; $j <$prize_data['lotto_type'] ; $j++): $l = 1+$j;  
                                                                                if($item[$j]['matched_count'] == $k && $item[$j]['matched_count'] != 0):
                                                                                    $ArrayCount += $MixCoupons;
                                                                                endif;
                                                                         endfor;  
                                                                    endforeach;
                                                                echo $ArrayCount;
                                                            endif; ?>
                                                                
                                                            </td>
                                                         <td width="20%" class="text-left"> 
                                                              <?php
                                                                if($ArrayCount > 0):
                                                                    //if prize amount is fixed will show this if data from if conditions. 
                                                                    if(in_array('Prize'.$k, $prize_data['reverse_prize_type'])):
                                                                        echo "Fixed prize<br>";
                                                                        echo $prize_data['reverse_prize'.$k] .' X ' .$ArrayCount .' = '. $prize_data['reverse_prize'.$k] *$ArrayCount;
                                                                        echo "<br> Each winner will get AED ".$prize_data['reverse_prize'.$k];

                                                                    else:
                                                                    //prize amount is shared will show this if data from if conditions. 
                                                                        echo "Shared prize<br>";
                                                                        $prize_amount = $prize_data['reverse_prize'.$k]/$ArrayCount;
                                                                        echo 'AED '.number_format($prize_amount,'2') . 'X' .$ArrayCount .' ( AED '.number_format($prize_data['reverse_prize'.$k],'2').' )';
                                                                    endif;

                                                                else:
                                                                     echo "No winner yet<br>";
                                                                     echo 'AED '.$prize_data['reverse_prize'.$k];
                                                                endif;
                                                                ?>
                                                        </td>


                                                        <td width="20%" class="text-center">
                                                                <?php if($ArrayCount >0 ): ?>
                                                                    <div class="btn-group">
                                                                          <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button>
                                                                            <ul class="dropdown-menu" role="menu">
                                                                            <li>
                                                                                <a href="<?php echo getCurrentControllerPath('addwinners/reverse/'.$k)?>"><i class="fas fa-edit"></i> View Winner List</a>
                                                                            </li>
                                                                            <?php if($ALLDATAINFO['status'] == 'A'): ?>
                                                                              <li><a href="<?php echo getCurrentControllerPath('changestatus/'.$ALLDATAINFO['lotto_winners_id'].'/I')?>"><i class="fas fa-thumbs-down"></i> Inactive</a></li>
                                                                            <?php elseif($ALLDATAINFO['status'] == 'I'): ?>
                                                                              <li>
                                                                                <a href="<?php echo getCurrentControllerPath('changestatus/'.$ALLDATAINFO['lotto_winners_id'].'/A')?>">
                                                                                    <i class="fas fa-thumbs-up"></i> Active
                                                                                </a>
                                                                              </li>
                                                                            <?php endif; ?>
                                                                           
                                                                        </ul>
                                                                    </div>
                                                                <?php endif; ?>
                                                          </td>
                                                    </tr>
                                                <?php endfor; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>


                           
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- [ Main Content ] end -->
</div>
</div>
 
 

 

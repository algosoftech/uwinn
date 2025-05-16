
 <?php if($OrderDetails): ?>
    <?php foreach ($OrderDetails as $key => $item): ?>
        <div class="container">
            <div class="row">
                <div class="col-12 px-0">
                    <div class="result_list">
                        <div class="inner_section">
                            <div class="promotion_campign">
                                <p>Promotional Campaign Value</p>
                                <div class="d-flex">
                                    <?php 
                                        $prize =  $item->prize_title;
                                        $prize = preg_replace('/^(.*?)(\d)/', '<h3>$1</h3>', $prize);
                                        $prize_with_color = preg_replace('/(\d{1,3}(,\d{3})*(\.\d+)?\s?AED)/', '<h2>$1</h2>', $prize);
                                        echo $prize_with_color;
                                    ?>
                                </div>
                            </div>
                            <div class="tickets_details">
                             <ul>
                                <li><p>Purchased On:</p> <p> <?=date('d F h:i A' , strtotime($item->created_at));?></p></li>
                                <li><p>Product:     </p> <p><?=$item->product_qty;?>x<?=ucwords($item->product_title);?></p></li>
                                <li><p>Ticket ID:   </p> <p><?=$item->order_id;?></p></li>
                                <li><p>Total (inc. VAT 5%)</p><p>Total (inc. VAT 5%)</p></li>
                             </ul>
                            </div>

                            <div class="refale_details_info">
                                <h2>Raffle Details</h2>

                                 <?php 
                                    $Tickect = str_replace('[[', '', $item->ticket);
                                    $Tickect = str_replace(']]', '/', $Tickect);
                                    $Tickect = str_replace('],[', '/', $Tickect);
                                    $Tickect = array_filter(explode('/', $Tickect));
                                    $selection_values = $item->selection_values;

                                    $selection_values = str_replace('[[', '', $selection_values);
                                    $selection_values = str_replace(']]', '/', $selection_values);
                                    $selection_values = str_replace('],[', '/', $selection_values);
                                    $selection_values = array_filter(explode('/', $selection_values));
                                ?>

                                <?php foreach ($Tickect as $Kay => $couponItems):  $SNo = $Kay+1;  
                                        $couponItemsArray = explode(',', $couponItems); ?>

                                        <div class="ticket_detils_reffle_Details">
                                            <div class="">
                                                <div class="serial_no_info">
                                                    <h2 class="mt-2"><?=$SNo;?>.</h2>
                                                    <?php
                                                        foreach ($item->draw as $key => $winnerItems):
                                                            $MatchingCoupons = $winnerItems->coupons;
                                                            $winningCode     = explode(',', $winnerItems->code);
                                                            $WinnerType      = $winnerItems->winner_type;
                                                            $winningAmount   = $winnerItems->amount;
                                                        endforeach;
                                                    ?>


                                                    <ul>
                                                        <?php if($couponItemsArray): ?>
                                                            <?php foreach ($couponItemsArray as $skey => $couponNo): ?>

                                                                <?php if($MatchingCoupons == $couponItems): ?>
                                                                    <?php $matched = in_array($couponNo, $winningCode)?>
                                                                <?php endif; ?>
                                                                    <li class="<?=$matched == 1 ?'matched':'';?>" ><?=$couponNo;?></li>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </ul>
                                                </div>
                                                
                                                <?php      
                                                    $PriceList      = $selection_values[$Kay];
                                                    $priceDataArray = explode(',', $PriceList);
                                                   
                                                    $sharingGrand   = '';
                                                    $multipleWin    = '';
                                                    $cashBack       = '';
                                                foreach($priceDataArray as $priceKey => $priceData ): 
                                                    if($priceKey == 0 && $priceData >= 1):
                                                       $sharingGrand = 'checked';
                                                    elseif($priceKey == 1 && $priceData >= 1 ):
                                                       $multipleWin  = 'checked';
                                                    elseif($priceKey == 2 && $priceData >= 1 ):
                                                       $cashBack     = 'checked';
                                                    endif;
                                                endforeach;?>

                                                    <div class="sel">
                                                        <div>
                                                            <input type="radio" <?=$sharingGrand;?>>
                                                            <p>Sharing Grand</p>
                                                         </div>
                                                         <div>
                                                            <input type="radio" <?=$multipleWin;?>>
                                                            <p>Multiple Win</p>
                                                         </div>
                                                         <div>
                                                            <input type="radio" <?=$cashBack; ?> >
                                                            <p>Cash Back</p>
                                                         </div>
                                                    </div>
                                            </div>

                                            <?php if(!empty($winningAmount) &&  $MatchingCoupons == $couponItems  ): ?>
                                                <button> You Won with <?=$winningAmount;?> AED prize amount</button>
                                            <?php else: ?>
                                                <button>No matches</button>
                                            <?php endif; ?>

                                        </div>
                                <?php endforeach; ?>

                            </div>


                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach;?>
<?php else: ?>
    <div class="row">
        <div class="col-md-6">
            <p>No Records found</p>
        </div>
    </div>
<?php endif; ?>
        
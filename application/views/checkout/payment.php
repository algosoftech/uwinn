<div class="payment_details_section">
        <div class="container">
          <form action="<?=base_url('/order-place')?>" method="POST" id="paymentDetailsForm" >
            <div class="row">
                <div class="col-md-8">
                    <div class="">
                        <ul class="breadcrumbs">
                            <li class="active"><a href="#">Checkout Details</a> </li>
                            <li class="active"><a href="#">Payment Details</a></li>
                        </ul>
                    </div>
                    <h2 class="sub_heading3">Select Payment Type</h2>
                    <div class="payment_Horizontalline2"> 
                        <hr>
                    </div>
                    
                    <div class="payment_card">
                        <ul>
                           <!--  <li class="product_summry">
                                <div class="card_info">
                                    <input type="radio" name='payment_mode' value="Online" required> <p> Credit / Debit / ATM Card </p>
                                </div> 
                                <div>
                                    <img src="<?=base_url('assets/frontend/img/mastercard.png');?>" alt="">
                                </div>
                            </li> -->
                            
                            <li class="product_summry">
                                <div class="card_info">
                                    <input type="radio" name='payment_mode' value="UPoints" checked required> <p>UPoints 
                                        <span> ( <?=number_format($this->session->userdata('availableArabianPoints'),2);?> )</span>
                                    </p>
                                </div> 
                                <div>
                                    <img src="<?=base_url('assets/img/coin.png');?>" alt="">
                                </div>
                            </li>
                        </ul>
                    </div>
                
                </div>
                <div class="col-md-4">
                    <div class="payment_details">
                        <h1>Payment Details</h1>
                        <div class="payment_Horizontalline3"> 
                        <hr>
                        </div>
                        <ul>
                          <li>
                             <h1>detail</h1>
                            <h1>Price</h1>
                          </li>
                          <li <?php if($productSetting['straight_settings'] != "Enable"  ): ?> style="display: none;"  <?php endif; ?> >
                              <p>Straight</p>
                              <p>AED <?=$straight_total;?></p>
                          </li>
                          <li <?php if($productSetting['rumble_settings'] != "Enable"  ): ?> style="display: none;"  <?php endif; ?> >
                              <p>Rumble</p>
                              <p>AED <?=$rumble_total;?></p>
                          </li>
                          <li <?php if($productSetting['reverse_settings'] != "Enable"  ): ?> style="display: none;"  <?php endif; ?> >
                              <p>Chance</p>
                              <p>AED <?=$reverse_total;?></p>
                          </li>
                        </ul>
                        <div class="total_price">
                            <h1>Total</h1>
                             <h1>AED <?=$total_price;?></h1>
                        </div>
                        <div class="text-center">
                            <input type="hidden" name="products_id" value="<?=$products_id;?>">
                            <input type="hidden" name="total_price" value="<?=$total_price;?>">
                            <input type="hidden" name="ticket_range" value="<?php print_r($ticket_range);?>">
                            <input type="hidden" name="ticket_mode" value="<?php print_r($ticket_mode);?>">
                            <input type="hidden" name="quantity" value="<?php print_r(count($ticket_range));?>">
                            <input type="hidden" name="rumble_total"   value="<?=$rumble_total;?>">
                            <input type="hidden" name="reverse_total"  value="<?=$reverse_total;?>">
                            <input type="hidden" name="straight_total" value="<?=$straight_total;?>">
                            <button  class="fill_btn" name="submitbutton" > Pay Now </button>
                        </div>
                    </div>
                </div>
            </div>
          </form>
        </div>
    </div>
   
<div class="wrapper pt-0">
    <?php if($slider): ?>
        <?php foreach($slider as $SliderItem): ?>
            <section class="mega_innerbanner">
                <div class="container-fluid p-0">
                    <img src="<?=ImageExist($SliderItem['image'])?>" alt="<?=$SliderItem['slider_image_alt']?>" class="w-100 product_detailbanner"/>
                </div>
            </section>        
        <?php endforeach;?>
    <?php endif;?>
   
    <section class="power_stripe container">
        <div class="row">
            <div class="col-12 labelstrip products_striptext">
                <div class="main-strip">
                    <div class="white-strip">
                        <div class="whitestripProd">
                            <img src="<?=ImageExist($ourCampaigns['product_image']);?>" alt="img">
                            <p><?=$ourCampaigns['title'];?></p> 
                        </div>
                        <div class="timer" id="countdown">
                            <p> 
                                <span class="timer-shw" id="days"></span> 
                                <span class="web-week">DAYS</span>
                            </p> 
                                <span class="colon">:</span> 
                            <p> 
                                <span class="timer-shw" id="hours"></span>
                                 <span class="web-week">HOURS</span>
                            </p> 
                                <span class="colon">:</span> 
                            <p> 
                                <span class="timer-shw" id="minutes"></span>  
                                <span class="web-week">MINUTES</span>
                            </p> 
                                <span class="colon">:</span> 
                            <p> 
                                <span class="timer-shw" id="seconds"></span>  
                                <span class="web-week">SECONDS</span>
                            </p>
                        </div>
                    </div>
                    <div class="amount-section">
                        <small class="amount-title">AED</small>
                            <?php if(!empty($ourCampaigns['lotto_type'])): ?>
                               <i class="amount"> <?=$ourCampaigns['prize_stright'.$ourCampaigns['lotto_type']]?></i>
                            <?php endif; ?>
                    </div>

                    <div class="megajakpot price">
                        <div class="megajakpot">
                            <img src="<?=ImageExist('assets/img/megajakport.png');?>" alt="megajakport" width="100">
                        </div>
                        <div class="pricestrip">
                            <p>Price: <?=$ourCampaigns['straight_add_on_amount']?> AED</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <form class="number-picking-form" action="<?=base_url('checkout')?>"  method="POST" >

        <input type="hidden" name="users_id" value="<?=$this->session->userdata('users_id')?>">
        <input type="hidden" name="product_id" value="<?=$ourCampaigns['products_id']?>">

        <section class="picknumber_Sec">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <div class="prduct_flex mb-3">
                            <div class="product_summry">
                                <h3>Product</h3>
                                <div class="product_img">
                                    <img src="<?=ImageExist($ourCampaigns['product_image']);?>" alt="<?=$item['product_image_alt'];?>" />
                                </div>
                            </div>
                            <div class="product_summry">
                                <h3>Pick Ticket & Numbers</h3>
                                <div class="product_qty">
                                    <div class="value-button" id="decrease">-</div>
                                    <input type="number" id="qty" value="1"  disabled />
                                    <div class="value-button" id="increase">+</div>
                                </div>
                            </div>
                        </div>
                        <div class="product_summry">
                            <h3>Numbers</h3>
                            <div class="product_summry_in">
                                <div class="tickect-section">
                                    <div class="product_summry_row ticket_1">
                                        <strong>1</strong>
                                        <div class="product_summrynum">
                                            <?php for ($i=0; $i <$ourCampaigns['lotto_type'] ; $i++): ?>
                                            <span></span>
                                            <?php endfor; ?>
                                            <input type="hidden" name="ticket_range[]" class="ticket_range">
                                        </div>

                                        <div class="entityrandom_delete">
                                            <a href="javascript:void(0)" class="section_random_number"><i class="bi bi-shuffle"></i></a>
                                            <p class="delete_icons"><i class="bi bi-trash"></i></p>
                                            <p class="cross_deletbutton"><i class="bi bi-x-square"></i></p>
                                        </div>
                                    </div>
                                    
                                    <div  class="ticket_1" <?php if($productSetting['straight_settings'] != 'Enable' && $productSetting['rumble_settings'] != 'Enable' && $productSetting['reverse_settings'] != 'Enable'): ?>
                                     style="display: none;" <?php endif;?>>
                                        <h2  class="sub_heading my-2">Select Ticket Type</h2>
                                        <div class="horizontal-Type">
                                            <hr>
                                        </div>
                                       
                                       <div class="ticket_type">
                                            <ul>
                                                <li <?php if($productSetting['straight_settings'] != "Enable"  ): ?> style="display: none;"  <?php endif; ?>>
                                                    <input type="checkbox" name="ticket_mode[0][0]" id="ticket_mode_1_0" class='ticket_mode' value="Straight" <?=$productSetting['straight_settings_default_check'];?>   
                                                     <?php if($productSetting['straight_settings'] != "Enable"  ): ?> style="display: none;"  <?php endif; ?>  >

                                                    <p>Straight + <?=$ourCampaigns['straight_add_on_amount'];?> AED</p>
                                                </li>

                                                <li  <?php if($productSetting['rumble_settings'] != "Enable"  ): ?> style="display: none;"  <?php endif; ?> >
                                                    <input type="checkbox" name="ticket_mode[0][1]" id="ticket_mode_1_1" class='ticket_mode' value="Rumble" <?=$productSetting['rumble_settings_default_check'];?> 
                                                    <?php if($productSetting['rumble_settings'] != "Enable"  ): ?> style="display: none;"  <?php endif; ?> >
                                                    <p>Rumble + <?=$ourCampaigns['rumble_add_on_amount'];?> AED</p>
                                                </li>

                                                <li <?php if($productSetting['reverse_settings'] != "Enable"  ): ?> style="display: none;"  <?php endif; ?>>
                                                    <input type="checkbox" name="ticket_mode[0][2]" id="ticket_mode_1_2" class='ticket_mode' value="Reverse" <?=$productSetting['reverse_settings_default_check'];?>
                                                    <?php if($productSetting['reverse_settings'] != "Enable"  ): ?> style="display: none;"  <?php endif; ?> >
                                                    <p>Chance + <?=$ourCampaigns['reverse_add_on_amount'];?> AED</p>
                                                </li>
                                            </ul>
                                       </div>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>

                    <div class="col-12 col-md-6">
                        <div class="pick_num_gray">
                            <div class="pick_numhead">
                                <h3>Pick Your Numbers</h3>
                                <a href="javascript:void(0)" class="clearAllTickets">Clear All</a>
                                <!-- <a href="javascript:void(0)" class="random_number">Random</a> -->
                            </div>
                            <div class="picknumselect">
                                <?php for ($i=$ourCampaigns['lotto_range_start']; $i <= $ourCampaigns['lotto_range_end']; $i++): ?>
                                    <label class="number_<?=$i;?>">
                                        <span class="pickup-number">
                                            <?=($ourCampaigns['enable_number_prefix'] == "Y")? $ourCampaigns['lotto_range_prefix'].$i : $i; ?>
                                        </span>
                                    </label>
                                <?php endfor; ?>
                            </div>
                          
                        </div>


                        <div class="payment_details">
                            <h1>Payment Details</h1>
                            <div class="payment_Horizontalline">
                                <hr>
                            </div>
                            <ul>
                                <li><h1>Detail</h1><h1>Price</h1></li>
                                <li  <?php if($productSetting['straight_settings'] != "Enable"  ): ?> style="display: none;"  <?php endif; ?> >
                                    <p>Straight</p> <p>AED <span class="Total_StraightPrice">0.00</span></p>
                                </li>
                                <li  <?php if($productSetting['rumble_settings'] != "Enable"  ): ?> style="display: none;"  <?php endif; ?>>
                                    <p>Rumble</p> <p>AED <span class="Total_RumblePrice">0.00</span></p>
                                </li>
                                <li  <?php if($productSetting['reverse_settings'] != "Enable"  ): ?> style="display: none;"  <?php endif; ?>>
                                    <p>Chance</p> <p>AED <span class="Total_ReversePrice">0.00</span></p>
                                </li>
                            </ul>
                            <div class="total_price">
                                <h1>Total</h1>
                                <h1>AED <span id="final_price">0.00</span></h1>
                            </div>
                            <div class="text-center">
                                 <input type="submit" class="fill_btn" value="Proceed To Next">
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </form>

    <?php if($productSetting['game_rule_image'] && $productSetting['game_description'] ): ?>

    <section class="how_toparticipate mb-4">
        <div class="balls_img">
            <img src="<?=base_url('assets/frontend/img/balls1.png');?>" />
        </div>
        <div class="balls_img entry_price">
            <img src="<?=base_url('assets/frontend/img/entry_price.png');?>" />
        </div>
          <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <h2 class="sec_head">How to Play in Uwinn?</h2>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-6">
                            <?= '<div class="game-description">' . $productSetting['game_description'] . '</div>'; ?>
                            <button type="button" class="submit-button linear-background coupen_code mt-0" data-bs-toggle="modal" data-bs-target="#confirmationModal" id="submitButton">Read More</button>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header py-0">
               
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="bi bi-x"></i></button>
            </div>
            <div class="modal-body py-0">
                <small class="read_moretext"><?=$productSetting['game_description'];?></small>
             
            </div>
            <!-- <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="confirmSubmit">Yes, Transfer</button>
            </div> -->
        </div>
    </div>
</div>


                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-6">
                            <img src="<?=ImageExist($productSetting['game_rule_image']);?>" class="how_play_img">
                        </div>
                    </div>
                     
                </div>
        
        <div class="balls_img bottom"><img src="<?=base_url('assets/frontend/img/balls1.png');?>" /></div>
    </section>

    <?php endif; ?>
</div>


<script>
    $(document).ready(function(){

        // Delete function with rearranging ticket numbers
        $(document).on('click', '.delete_icons', function() {
            if($('.tickect-section').length > 1){
                // Remove the parent ticket row
                $(this).closest('.tickect-section').remove();
                NewQTY = parseInt($('.tickect-section').length);
                // console.log(NewQTY);
                // Rearrange the ticket numbers
                $('.product_summry_row').each(function(index) {
                    $(this).find('strong').text(NewQTY - index );
                    $('#qty').val(index + 1);
                });

                // Update other ticket-related fields as necessary
                updateTicketRange();
                checkIfAllFilled();
                updatePrices();
            }
        });

        // Existing cross delete button functionality
        // $(document).on('click', '.cross_deletbutton', function() {
        //     $(this).closest('.product_summry_row').find('.product_summrynum span').text('');
        //     $(this).closest('.product_summry_row').find('.ticket_range').val('');
        //     checkIfAllFilled();
        // });


        /*---------------- Campaign Increment & Decrease code Start ---------------------------*/ 
        $(document).on('click','#increase', function() {
            
            let AddTicket = "N";
            $('.tickect-section').find('span').each(function() {
               if($(this).text().trim() !== ""){
                AddTicket = "Y";
               }else{
                AddTicket = "N";
                return false;
               }
            });

            if(AddTicket == "Y"){

                    $('.product_summry_row').removeClass('selected');
                    $('.pickup-number').removeClass('selected');

                    let QTY = $('#qty').val();
                    let newQty = parseInt(QTY) + 1;
                    $('#qty').val(newQty);

                    let lottoType = "<?=$ourCampaigns['lotto_type'];?>"; 
                    let spanElements = '';
                    for (let i = 0; i < parseInt(lottoType); i++) {
                        spanElements += '<span></span>';
                    }

                    // Check if ticket section already exists
                    if ($(`.ticket_${newQty}`).length === 0) {
                        $(".product_summry_in").prepend(`
                            <div class="tickect-section">
                                <div class="ticket_row product_summry_row ticket_${newQty} selected">
                                    <strong>${newQty}</strong>
                                    <div class="product_summrynum">${spanElements}</div>
                                    <input type="hidden" name="ticket_range[${newQty}]" class="ticket_range">
                                    <div class="entityrandom_delete">
                                        <a href="javascript:void(0)" class="section_random_number"><i class="bi bi-shuffle"></i></a>
                                        <p class="delete_icons"><i class="bi bi-trash"></i></p>
                                        <p class="cross_deletbutton"><i class="bi bi-x-square"></i></p>
                                    </div>
                                </div>

                                <div class="ticket_${newQty}" <?php if($productSetting['straight_settings'] != 'Enable' && $productSetting['rumble_settings'] != 'Enable' && $productSetting['reverse_settings'] != 'Enable'): ?>
                                     style="display: none;" <?php endif;?>>
                                    <h2 class="sub_heading my-2">Select Ticket Type</h2>
                                    <div class="horizontal-Type"><hr></div>
                                    <div class="ticket_type">
                                        <ul>
                                            <li <?php if($productSetting['straight_settings'] != "Enable"  ): ?> style="display: none;"  <?php endif; ?>>
                                                <input type="checkbox" name="ticket_mode[${newQty}][0]" id="ticket_mode_${newQty}_0" class='ticket_mode' value="Straight"  <?php if($productSetting['straight_settings_default_check'] == 'Checked'): ?> checked="checked" <?php endif; ?> <?php if($productSetting['straight_settings'] != "Enable"  ): ?> style="display: none;"  <?php endif; ?> >
                                                <p>Straight + <?=$ourCampaigns['straight_add_on_amount'];?> AED</p>
                                            </li>
                                            <li <?php if($productSetting['rumble_settings'] != "Enable"  ): ?> style="display: none;"  <?php endif; ?>>
                                                <input type="checkbox" name="ticket_mode[${newQty}][1]" id="ticket_mode_${newQty}_1" class='ticket_mode' value="Rumble" <?php if($productSetting['rumble_settings_default_check'] == 'Checked'): ?> checked="checked" <?php endif; ?> <?php if($productSetting['rumble_settings'] != "Enable"  ): ?> style="display: none;"  <?php endif; ?> >
                                                <p>Rumble + <?=$ourCampaigns['rumble_add_on_amount'];?> AED</p>
                                            </li>
                                            <li <?php if($productSetting['reverse_settings'] != "Enable"  ): ?> style="display: none;"  <?php endif; ?>>
                                                <input type="checkbox" name="ticket_mode[${newQty}][2]" id="ticket_mode_${newQty}_2" class='ticket_mode' value="Reverse" <?php if($productSetting['reverse_settings_default_check'] == 'Checked'): ?> checked="checked" <?php endif; ?> <?php if($productSetting['reverse_settings'] != "Enable"  ): ?> style="display: none;"  <?php endif; ?> >
                                                <p>Chance + <?=$ourCampaigns['reverse_add_on_amount'];?> AED</p>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        `);
                    }else{

                        let duplicate_newQTY = parseInt(newQty+1);

                        $(".product_summry_in").prepend(`
                            <div class="tickect-section">
                                <div class="ticket_row product_summry_row ticket_${duplicate_newQTY}">
                                    <strong>${newQty}</strong>
                                    <div class="product_summrynum">${spanElements}</div>
                                    <input type="hidden" name="ticket_range[${duplicate_newQTY}]" class="ticket_range">
                                    <div class="entityrandom_delete">
                                        <a href="javascript:void(0)" class="section_random_number"><i class="bi bi-shuffle"></i></a>
                                        <p class="delete_icons"><i class="bi bi-trash"></i></p>
                                        <p class="cross_deletbutton"><i class="bi bi-x-square"></i></p>
                                    </div>
                                </div>

                                <div class="ticket_${duplicate_newQTY}" <?php if($productSetting['straight_settings'] != 'Enable' && $productSetting['rumble_settings'] != 'Enable' && $productSetting['reverse_settings'] != 'Enable'): ?>
                                     style="display: none;" <?php endif;?>>
                                    <h2 class="sub_heading my-2">Select Ticket Type</h2>
                                    <div class="horizontal-Type"><hr></div>
                                    <div class="ticket_type">
                                        <ul>
                                            <li <?php if($productSetting['straight_settings'] != "Enable"  ): ?> style="display: none;"  <?php endif; ?>>
                                                <input type="checkbox" name="ticket_mode[${duplicate_newQTY}][0]" id="ticket_mode_${duplicate_newQTY}_0" class='ticket_mode' value="Straight"  <?php if($productSetting['straight_settings_default_check'] == 'Checked'): ?> checked="checked" <?php endif; ?> <?php if($productSetting['straight_settings'] != "Enable"  ): ?> style="display: none;"  <?php endif; ?> >
                                                <p>Straight + <?=$ourCampaigns['straight_add_on_amount'];?> AED</p>
                                            </li>
                                            <li <?php if($productSetting['rumble_settings'] != "Enable"  ): ?> style="display: none;"  <?php endif; ?>>
                                                <input type="checkbox" name="ticket_mode[${duplicate_newQTY}][1]" id="ticket_mode_${duplicate_newQTY}_1" class='ticket_mode' value="Rumble" <?php if($productSetting['rumble_settings_default_check'] == 'Checked'): ?> checked="checked" <?php endif; ?> <?php if($productSetting['rumble_settings'] != "Enable"  ): ?> style="display: none;"  <?php endif; ?> >
                                                <p>Rumble + <?=$ourCampaigns['rumble_add_on_amount'];?> AED</p>
                                            </li>
                                            <li <?php if($productSetting['reverse_settings'] != "Enable"  ): ?> style="display: none;"  <?php endif; ?>>
                                                <input type="checkbox" name="ticket_mode[${duplicate_newQTY}][2]" id="ticket_mode_${duplicate_newQTY}_2" class='ticket_mode' value="Reverse" <?php if($productSetting['reverse_settings_default_check'] == 'Checked'): ?> checked="checked" <?php endif; ?> <?php if($productSetting['reverse_settings'] != "Enable"  ): ?> style="display: none;"  <?php endif; ?> >
                                                <p>Chance + <?=$ourCampaigns['reverse_add_on_amount'];?> AED</p>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        `);
                    }
            }else{
            alertMessageModelPopup('Please enter all numbers.','danger');

            }
            // Call the check function to ensure all inputs are properly filled
            checkIfAllFilled();
        });

        $("#decrease").on('click', function() {
            let QTY = $('#qty').val();
            if (QTY > 1) {
                let newQty = parseInt(QTY) - 1;
                $('#qty').val(newQty);
                
                // Remove the last .tickect-section
                $('.tickect-section').last().remove();
                
                checkIfAllFilled();
            }
        });

        /*---------------- Campaign Increment & Decrease code End ---------------------------*/ 


        /*---------------- Clear All Tickect Start ---------------------------*/ 
        $('.clearAllTickets').on('click',function(){

            // Select the .product_summry_row that has the .selected class
            $('.product_summry_row.selected').each(function() {


                // Clear all <span> text inside .product_summrynum
                $(this).find('.product_summrynum span').text('');
                // Remove all input values inside the selected row
                $(this).find('input').val('');

               $(this).parent('.tickect-section').find('.ticket_mode').each(function(){
                    // var straight_settings_default_check =  "<?=$productSetting['straight_settings_default_check'];?>"; 
                    // var reverse_settings_default_check  =  "<?=$productSetting['reverse_settings_default_check'];?>";
                    // var rumble_settings_default_check   =  "<?=$productSetting['rumble_settings_default_check'];?>";

                    <?php 
                        $straight_status =  ($productSetting['straight_settings_default_check'] == 'Checked')   ? $straight_status = true : $straight_status = false;
                        $reverse_status  =  ($productSetting['reverse_settings_default_check'] == 'Checked')    ?  $reverse_status = true : $reverse_status  = false;
                        $rumble_status   =  ($productSetting['rumble_settings_default_check'] == 'Checked')     ?   $rumble_status = true : $rumble_status   = false;
                    ?>
                   
                    var ticket_mode = $(this).is(":checked");
                    if(ticket_mode){
                    // $('.ticket_mode').prop('checked', false);
                      if($(this).val() == 'Straight'){
                        $(this).prop('checked',"<?=$straight_status?>");
                      } 

                      if( $(this).val() == 'Rumble'){
                        // console.log(<?=$rumble_status?>)
                        $(this).prop('checked', "<?=$rumble_status?>");
                      }

                      if( $(this).val() == 'Reverse'){
                        // console.log("<?=$reverse_status?>")
                        $(this).prop('checked', "<?=$reverse_status?>");
                      }

                    }

               });


            });



             
            $('.pickup-number').removeClass('selected');
            checkIfAllFilled();
        });
        /*---------------- Clear All Tickect End ---------------------------*/ 

        /*---------------- Generate Random Number Start ---------------------------*/ 
        $(document).on('click','.random_number', function() {

            $('.ticket_range').val('');
            $('.product_summrynum span').empty();
            // $('.ticket_mode').prop('checked', false);
            $('.pickup-number').removeClass('selected');

            let enable_number_prefix  = "<?=$ourCampaigns['enable_number_prefix']?>";
            let lotto_range_prefix    = "<?=$ourCampaigns['lotto_range_prefix']?>";
            let ticket_number_repeat  = "<?=$ourCampaigns['ticket_number_repeat']?>";
            let lotto_range_start     = "<?=$ourCampaigns['lotto_range_start']?>";
            let lotto_range_end       = "<?=$ourCampaigns['lotto_range_end']?>";
            let lotto_type            = "<?=$ourCampaigns['lotto_type']?>";

            $('.product_summry_row').each(function() {
                let row = $(this);
                let numbers = [];
                row.find('.product_summrynum span').each(function() {
                    if ($(this).text() !== '') {
                        numbers.push($(this).text());
                    }
                });

                for (let i = numbers.length; i < lotto_type; i++) {
                    let randomNumber;
                    while (true) {
                        randomNumber = Math.floor(Math.random() * (lotto_range_end - lotto_range_start + 1)) + parseInt(lotto_range_start);
                        if (ticket_number_repeat === "N" && numbers.includes(randomNumber.toString())) {
                            continue;
                        }
                        if (enable_number_prefix === "Y") {
                            randomNumber = lotto_range_prefix + randomNumber;
                        }
                        if (row.find('.product_summrynum span').filter(function() { return $(this).text() === randomNumber.toString(); }).length === 0) {
                            break;
                        }
                    }

                    row.find('.product_summrynum span').each(function() {
                        if ($(this).text() === '') {
                            $('.number_'+randomNumber + ' span' ).addClass("selected");
                            $(this).text(randomNumber);
                            numbers.push(randomNumber.toString());
                            return false;
                        }
                    });
                }
                row.find('.ticket_range').val(numbers.join(','));
            });
            checkIfAllFilled();
        });

       
        $(document).on('click', '.section_random_number', function() {
            $(this).closest('.product_summry_row').find('.product_summrynum span').text('');
            $(this).closest('.product_summry_row').find('.ticket_range').val('');

            let enable_number_prefix  = "<?=$ourCampaigns['enable_number_prefix']?>";
            let lotto_range_prefix    = "<?=$ourCampaigns['lotto_range_prefix']?>";
            let ticket_number_repeat  = "<?=$ourCampaigns['ticket_number_repeat']?>";
            let lotto_range_start     = "<?=$ourCampaigns['lotto_range_start']?>";
            let lotto_range_end       = "<?=$ourCampaigns['lotto_range_end']?>";
            let lotto_type            = "<?=$ourCampaigns['lotto_type']?>";

            let row = $(this).closest('.product_summry_row');
            let SingleTicket = row.find('.product_summrynum span');
            let numbers = [];

            SingleTicket.each(function() {
                if ($(this).text() === '') {
                    let randomNumber;
                    while (true) {
                        randomNumber = Math.floor(Math.random() * (lotto_range_end - lotto_range_start + 1)) + parseInt(lotto_range_start);
                        if (ticket_number_repeat === "N" && numbers.includes(randomNumber.toString())) {
                            continue;
                        }
                        if (enable_number_prefix === "Y") {
                            randomNumber = lotto_range_prefix + randomNumber;
                        }
                        if (!numbers.includes(randomNumber.toString())) {
                            numbers.push(randomNumber.toString());
                            break;
                        }
                    }
                    $(this).text(randomNumber);
                } else {
                    numbers.push($(this).text());
                }
            });

            row.find('.ticket_range').val(numbers.join(','));
            checkIfAllFilled();
        });
        function updateTicketRange() {
            $('.product_summry_row').each(function() {
                let numbers = [];
                $(this).find('.product_summrynum span').each(function() {
                    if ($(this).text() !== '') {
                        numbers.push($(this).text());
                    }
                });
                $(this).find('.ticket_range').val(numbers.join(','));
            });
            checkIfAllFilled();
        }

        var currentIndex = 0;
        var ticketValues = [];
      
        // $(document).on('click','.pickup-number', function(){
        //     var ticket_number_repeat = 'Y';
        //     let numberValue = $(this).text().trim();
            
        //     $('.product_summry_row').removeClass('selected');
        //     $('.tickect-section').find('.product_summrynum').each(function(RowIndex){

        //         let SpanLength = $(this).find('span').length;
                
        //         for (var i = 0; i < SpanLength; i++) {
                    
        //             let sSpanInput = $(this).eq(RowIndex).find('span').eq(i);
        //             if(sSpanInput.text() == ""){
                       
        //                 console.log(RowIndex)

        //                 $('.number_'+numberValue + ' span' ).addClass("selected"); 
        //                 sSpanInput.text(numberValue);
        //                 break;
        //             }
        //         }
        //     });
        // });

        // $(document).on('click', '.pickup-number', function() {
        //     var numberValue = $(this).text().trim();
            
        //     $('.product_summry_row').removeClass('selected'); // Remove 'selected' class from all rows

        //     $('.tickect-section').each(function(RowIndex) {
                
        //         var productSummryNum = $(this).find('.product_summrynum');
        //         var emptySpan = productSummryNum.find('span').filter(function() {
        //             return $(this).text().trim() === "";
        //         }).first(); 


        //         if (emptySpan.length > 0) {
                    
        //             console.log(RowIndex)
                    
        //             // Add 'selected' class to the current row
        //             emptySpan.closest('.product_summry_row').addClass('selected');
        //             emptySpan.text(numberValue);
        //             $('.number_'+numberValue + ' span').addClass("selected");
                    
        //             return false; // Exit the .each() loop once the number is added
        //         }
                
        //     });
        // });

        $(document).on('click', '.pickup-number', function() {
            var numberValue = $(this).text().trim();
            $('.product_summry_row ,.pickup-number').removeClass('selected'); // Remove 'selected' class from all rows
            var numberExists = false; // Flag to check if number already exists
            $('.tickect-section').each(function() {

                var productSummryNum  = $(this).find('.product_summrynum');
                var emptySpan         = productSummryNum.find('span').filter(function() {
                    return $(this).text().trim() === "";
                }).first();

                if (emptySpan.length > 0) {

                    productSummryNum.find('span').each(function(){
                        let SpanValue = $(this).text();
                        $('.number_' + SpanValue + ' span').addClass("selected");
                    });

                    let ticket_number_repeat = "<?=$ourCampaigns['ticket_number_repeat']?>";
                    if(ticket_number_repeat == "N"){
                        // Check if the number already exists in the loop
                        var isNumberPresent = productSummryNum.find('span').filter(function() {
                            return $(this).text().trim() === numberValue;
                        }).length > 0;

                        if (isNumberPresent) {
                            numberExists = true; // Set flag if number already exists
                            return false; // Exit the loop if the number exists
                        }
                    }

                    // Add 'selected' class to the current row
                    emptySpan.closest('.product_summry_row').addClass('selected');
                    emptySpan.text(numberValue);
                    $('.number_' + numberValue + ' span').addClass("selected");
                    
                    return false; // Exit the .each() loop once the number is added
                }
                
            });

            if (numberExists) {
                alertMessageModelPopup('Select your numbers.','danger');
            }
            updateTicketRange();
            checkIfAllFilled();

        });


        /*---------------- Generate Random Number End ---------------------------*/ 


        /*---------------- Hilight Selected tickect - Start ---------------------------*/ 
        $(document).on('click', '.product_summry_row', function() {
            // Remove red background from all other rows
            $('.product_summry_row').removeClass('selected');

            // Add red background to the clicked row
            $(this).addClass('selected');


            const selectedRow = document.querySelector('.product_summry_row.selected');
            // Check if the selectedRow exists
            if (selectedRow) {
                // Get all the span elements inside the selectedRow
                const spans = selectedRow.querySelectorAll('.product_summrynum span');
                // Create an array to hold the span values
                const spanValues = [];
                $('.pickup-number' ).removeClass("selected");
                spans.forEach(span => {
                    var spanValue = span.textContent;
                    $('.number_'+spanValue + ' span' ).addClass("selected");
                });
            }

        });
        /*---------------- Hilight Selected tickect - End ---------------------------*/ 


        /*---------------- Generate Random Number Start ---------------------------*/ 
        // $(document).on('click', '.cross_deletbutton', function() {
            // $(this).closest('.product_summry_row').find('.product_summrynum span').text('');
            // $(this).closest('.product_summry_row').find('.ticket_range').val('');
            // checkIfAllFilled();
        // });

        $(document).on('click', '.cross_deletbutton', function() {
            let SpanList = $(this).closest('.product_summry_row').find('.product_summrynum span');
            let lastNonEmptySpan = null;

            // Iterate over the span elements in reverse to find the last non-empty one
            for (let i = SpanList.length - 1; i >= 0; i--) {
                if ($(SpanList[i]).text().trim() !== '') {
                    lastNonEmptySpan = SpanList[i];
                    break;
                }
            }

            // Empty the last non-empty span
            if (lastNonEmptySpan) {
                $(lastNonEmptySpan).text('');
            }

            $(this).closest('.product_summry_row').find('.ticket_range').val('');
            checkIfAllFilled();
        });
        /*---------------- Generate Random Number End ---------------------------*/ 

        /*---------------- Price Managing Start ---------------------------*/ 
         const StraightPrice = <?=json_encode($ourCampaigns['straight_add_on_amount']);?>;
         const RumblePrice   = <?=json_encode($ourCampaigns['rumble_add_on_amount']);?>;
         const ReversePrice  = <?=json_encode($ourCampaigns['reverse_add_on_amount']);?>;

         function updatePrices() {
            let ticketModes = { 'Straight': 0, 'Rumble': 0, 'Reverse': 0 };

            $('.ticket_mode:checked').each(function() {
                ticketModes[$(this).val()]++;
            });

            let StraightTotalPrice = ticketModes['Straight'] * StraightPrice;
            let RumbleTotalPrice   = ticketModes['Rumble'] * RumblePrice;
            let ReverseTotalPrice  = ticketModes['Reverse'] * ReversePrice;

            $('.Total_StraightPrice').text(StraightTotalPrice.toFixed(2));
            $('.Total_RumblePrice').text(RumbleTotalPrice.toFixed(2));
            $('.Total_ReversePrice').text(ReverseTotalPrice.toFixed(2));

            let Final_Price = StraightTotalPrice + RumbleTotalPrice + ReverseTotalPrice;
            $('#final_price').text(Final_Price.toFixed(2));
         }

         $(document).on('change','.ticket_mode', function() {
            updatePrices();
            checkIfAllFilled();
         });
         updatePrices();
        /*---------------- Price Managing End ---------------------------*/ 
        // function checkIfAllFilled(){
        //     let  TickectCount = $(".product_summry_in .product_summry_row .tickect-section").length;
        //     let error = 'NO';
        //     for (var i = 1; i <= TickectCount; i++) {
        //         let optionA = $(`#ticket_mode_${i}_0`).prop('checked');
        //         let optionB = $(`#ticket_mode_${i}_1`).prop('checked');
        //         let optionC = $(`#ticket_mode_${i}_2`).prop('checked');


        //         if(optionA !== true && optionB !== true && optionC !== true){
        //             error = 'YES';
        //         }
        //     }

        //     $('.product_summry_row').each(function() {
        //         $(this).find('.product_summrynum span').each(function() {
        //             if($(this).text() === '') {
        //                 error = "YES";
        //             }
        //         });
        //     });

        //     console.log(TickectCount);  
        //     console.log(error)
        //     updatePrices();

        //     if(error === 'YES'){
        //         return 'error';
        //         // $('.fill_btn').attr('disabled', true);
        //     }else{
        //         return 'success';

        //         // $('.fill_btn').attr('disabled', false);
        //     }
        // }



         function checkIfAllFilled() {
            
            let error = 'NO';

            $(".tickect-section").each(function(sectionIndex) {
                let optionsSelected = $(this).find('.ticket_mode:checked').length > 0;
                // If none of the options are selected for the current ticket section
                if (optionsSelected == 0) {
                    error = 'YES';
                    return false; // Break the loop as soon as we find an unselected ticket
                }

                $(this).find('.product_summrynum span').each(function() {
                    if($(this).text().trim() === '') {
                        error = "YES";
                    }
                });

            });

            // console.log(error);
            updatePrices();

            if (error === 'YES') {
                return 'error';
                // Optionally disable a button if there's an error
                // $('.fill_btn').attr('disabled', true);
            } else {
                return 'success';
                // Enable button if no error
                // $('.fill_btn').attr('disabled', false);
            }
        }

        $(document).on('submit' ,'.number-picking-form', function(e) {
            var responce = checkIfAllFilled();
            if(responce == 'error'){
                e.preventDefault();
                alertMessageModelPopup('Please select an option! ','danger');
            }
           
        });

        checkIfAllFilled();
    })
 
        // Set the date we're counting down to
        var draw_date  = "<?=$ourCampaigns['draw_date'].' '.$ourCampaigns['draw_time']; ?>";
        var countDownDate = new Date(draw_date).getTime();
        // Update the count down every 1 second
        var countdownFunction = setInterval(function() {
        
        // Get today's date and time
        var now = new Date().getTime();

        // Find the distance between now and the count down date
        var distance = countDownDate - now;

        // Time calculations for days, hours, minutes, and seconds
        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        // Output the result in elements with the respective IDs
        document.getElementById("days").innerHTML = days;
        document.getElementById("hours").innerHTML = hours;
        document.getElementById("minutes").innerHTML = minutes;
        document.getElementById("seconds").innerHTML = seconds;

        // If the count down is over, write some text 
        if (distance < 0) {
            clearInterval(countdownFunction);
            document.getElementById("countdown").innerHTML = "EXPIRED";
        }
    }, 1000);
</script>




<?=$this->session->set_userdata('products_id',$ourCampaigns['products_id'] ); ?>
<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
<div class="section_First1">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="banner_First">
                    <img src="<?=base_url('assets/frontend/img/Banner.png')?>" alt="Banner" class="banner_Img">
                    <div class="space_Manage_5">
                        <p class="to_Know">To Know More Information <br> And How to Play in</p>
                        <h3 class="uwinn">Uwinn?</h3>
                        <p class="check_Our">Check Our YouTube Videos</p>
                        <div class="check_out_btn">
                            <a class="view_Results">View Results</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<form action="<?=base_url('/payment-detail')?>" method="POST" id="checkoutForm"> 
    <input type="hidden" name="products_id"  value="<?=$ourCampaigns['products_id'];?>" >
    <div class="payment_details_section">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div>
                        <ul class="breadcrumbs">
                            <li class="active"><a href="#">Checkout Details</a></li>
                            <li><a href="#">Payment Details</a></li>
                        </ul>
                    </div>
                    <h2 class="sub_heading">You are logged in as:</h2>
                    <div class="card_Small">
                        <div class="row checkout_Logo">
                            <div class="col-md-3">
                                <div class="logo_Flex">
                                    <i class="fa-solid fa-user man_Logo"></i>
                                    <p><?=htmlspecialchars($this->session->userdata('first_name').' '.$this->session->userdata('last_name')); ?></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="logo_Flex">
                                    <i class="fa-solid fa-phone man_Logo"></i>
                                    <p><?=htmlspecialchars($this->session->userdata('country_code').'-'.$this->session->userdata('users_mobile')); ?></p>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="logo_Flex">
                                    <i class="fa-solid fa-envelope man_Logo"></i>
                                    <p><?=htmlspecialchars($this->session->userdata('users_email')); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h2 class="sub_heading">Ticket Details</h2>
                        <div class="horizontal-Details">
                            <hr>
                        </div>
                        <div class="product_summry_in">
                            <?php $TicketRange = $this->input->post('ticket_range'); ?>
                            <?php if ($TicketRange): $S_no; ?>
                                <?php $S_no = 1; foreach ($TicketRange as $ticketRange => $ticketArrayGroup): ?>
                                    <div class="product_summry_row">
                                        <strong>Ticket <?=$S_no;?></strong>
                                        <div class="product_summrynum">
                                            <?php $ticketArray = explode(',', $ticketArrayGroup); ?>
                                            <?php foreach ($ticketArray as $ticketItem): ?>
                                                <span><?=htmlspecialchars($ticketItem);?></span>
                                            <?php endforeach; ?>
                                            <input type="hidden" name="ticket_range[<?=$S_no;?>]" class="ticket_range" value="<?=htmlspecialchars($ticketArrayGroup);?>">
                                        </div>
                                        <div>
                                            
                                        </div>
                                        <!-- <button type="button" class="deleteButton"><img src="<?=base_url('assets/frontend/img/close.svg')?>" alt="Close"></button> -->
                                    </div>
                                    <?php if(  $ourCampaigns['straight_settings'] == 'Enable' || $ourCampaigns['rumble_settings'] == 'Enable' || $ourCampaigns['reverse_settings'] == 'Enable'):?>
                                    <div>
                                        <h2 class="sub_heading">Select Ticket Type</h2>
                                        <div class="horizontal-Type">
                                            <hr>
                                        </div>
                                        <?php $ticketMode = $this->input->post('ticket_mode'); ?> 

                                        <div class="ticket_type">
                                            <ul>
                                                <li>
                                                    <input type="checkbox" name="ticket_mode[<?=$S_no;?>][0]" id="ticket_mode_<?=$S_no;?>_0" class="ticket_mode" value="Straight" <?=isset($ticketMode[$ticketRange][0]) && $ticketMode[$ticketRange][0] == 'Straight' ? 'checked' : '';?>>
                                                    <p>Straight + <?=htmlspecialchars($ourCampaigns['straight_add_on_amount']);?> AED</p>
                                                </li>
                                                <li>
                                                    <input type="checkbox" name="ticket_mode[<?=$S_no;?>][1]" id="ticket_mode_<?=$S_no;?>_1" class="ticket_mode" value="Rumble" <?=isset($ticketMode[$ticketRange][1]) && $ticketMode[$ticketRange][1] == 'Rumble' ? 'checked' : '';?>>
                                                    <p>Rumble + <?=htmlspecialchars($ourCampaigns['rumble_add_on_amount']);?> AED</p>
                                                </li>
                                                <li>
                                                    <input type="checkbox" name="ticket_mode[<?=$S_no;?>][2]" id="ticket_mode_<?=$S_no;?>_2" class="ticket_mode" value="Reverse" <?=isset($ticketMode[$ticketRange][2]) && $ticketMode[$ticketRange][2] == 'Reverse' ? 'checked' : '';?>>
                                                    <p>Reverse + <?=htmlspecialchars($ourCampaigns['reverse_add_on_amount']);?> AED</p>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <?php else:?>
                                        <input type="hidden" name="ticket_mode[<?=$S_no;?>][0]" id="ticket_mode_<?=$S_no;?>_0" class="ticket_mode" value="Straight"  checked >
                                    <?php endif;  ?>

                                <?php $S_no++; endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="payment_details">
                        <h1>Payment Details</h1>
                        <div class="payment_Horizontalline">
                            <hr>
                        </div>
                        <ul>
                            <li><h1>Detail</h1><h1>Price</h1></li>
                            <li><p>Straight</p> <p>AED <span class="Total_StraightPrice">0.00</span></p></li>
                            <li><p>Rumble</p> <p>AED <span class="Total_RumblePrice">0.00</span></p></li>
                            <li><p>Reverse</p> <p>AED <span class="Total_ReversePrice">0.00</span></p></li>
                        </ul>
                        <div class="total_price">
                            <h1>Total</h1>
                            <h1>AED <span id="final_price">0.00</span></h1>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="fill_btn" >Proceed To Next</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>



<script>
    $(document).ready(function() {
        const StraightPrice = <?=json_encode($ourCampaigns['straight_add_on_amount']);?>;
        const RumblePrice   = <?=json_encode($ourCampaigns['rumble_add_on_amount']);?>;
        const ReversePrice  = <?=json_encode($ourCampaigns['reverse_add_on_amount']);?>;

        function updatePrices() {
            let ticketModes = { 'Straight': 0, 'Rumble': 0, 'Reverse': 0 };

            $('.ticket_mode:checked').each(function() {
                ticketModes[$(this).val()]++;
            });

            let StraightTotalPrice = ticketModes['Straight'] * StraightPrice;
            let RumbleTotalPrice = ticketModes['Rumble'] * RumblePrice;
            let ReverseTotalPrice = ticketModes['Reverse'] * ReversePrice;

            $('.Total_StraightPrice').text(StraightTotalPrice.toFixed(2));
            $('.Total_RumblePrice').text(RumbleTotalPrice.toFixed(2));
            $('.Total_ReversePrice').text(ReverseTotalPrice.toFixed(2));

            let Final_Price = StraightTotalPrice + RumbleTotalPrice + ReverseTotalPrice;
            $('#final_price').text(Final_Price.toFixed(2));
        }

        updatePrices();

        $('.ticket_mode').on('change', function() {
            updatePrices();
        });

        $('#checkoutForm').on('click', function(e) {
            CheckTicketTypeVal();
        });

        function CheckTicketTypeVal(){
            let TickectCount = "<?=count($TicketRange)?>";
            let error = 'NO';
            for (var i = 1; i <= TickectCount; i++) {

                let optionA = $(`#ticket_mode_${i}_0`).prop('checked');
                let optionB = $(`#ticket_mode_${i}_1`).prop('checked');
                let optionC = $(`#ticket_mode_${i}_2`).prop('checked');

                if(optionA !== true && optionB !== true && optionC !== true){
                    error = 'YES';
                }
            }

            if(error === 'YES'){
                $('.fill_btn').attr('disabled', true);
            }else{
                $('.fill_btn').attr('disabled', false);
            }
        }

        CheckTicketTypeVal();
    });
</script>
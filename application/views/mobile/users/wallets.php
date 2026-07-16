<script src="<?=base_url('assets/frontend/js/qrcode.min.js');?>"></script>
<?php 
    $percentage     = ($winning_balance/100)*100;
    $percentage     =  $percentage< 100 ? $percentage.'%' :  "100%";
?>  
    
<button class="credit-btn"><a href="javascript:void(0)">+ Add Credit</a> </button>

<div class="inner_section">
    <div>
        <div class="balance_point  d-flex justify-content" id="transaction" data-section="transaction-container">Play Balance<button>UPoint <?=$play_balance;?></button></div>
        <div class="balance_point d-flex justify-content" id="credit" data-section="payment-container">Withdrawable Balance <button>UPoint <?=$winning_balance;?></button></div>
        <sup class="text-danger">winning amount will be reflected with in 30 minutes</sup>
    </div>
    <p class="notes_point"> * 1 UPoint = 1AED </p>
    <h1 class="trasection_selection">Select Transaction Type</h1>
    <div class="fonm-section">
        

        <?php /*
            <div class="Transaction_btn">
                <div>
                    <input name="wallet_type" type="radio" class="section" id="transfer" data-section="transfer-container">
                    <label for="transfer">Transfer</label>
                </div>
                <form action="<?=base_url('wallet-submit')?>" method="POST" class="transfer-section transfer-container form-container d-none mt-3" id="transferForm">
                    <div class="transaction-container">
                        <p>Move Money from Winning Balance to Play Balance</p>
                        <div class="input-group">
                            <input type="number" min="1" name="winning_amount" id="transferAmount" value="<?=$winning_balance;?>" placeholder="Enter Transfer Amount" required>
                            <span class="currency linear-background">AED</span>
                        </div>
                        <input name="wallet_type" type="hidden" value="transfer-amount" class="placeholder-box">
                        <button class="contact_btn">Submit</button>
                    </div>
                </form>
            </div>
        */ ?>

        <div class="Transaction_btn">
            <div>
                <input name="wallet_type" type="radio" class="section" id="withdraw" data-section="withdraw-container" checked>
                <label for="withdraw">Withdraw</label>
            </div>
            <div class="payment-container withdraw-container form-container">
                <?php if($winning_balance < 100): ?>
                    <div class="withdraw_circles">
                        <p class="balance_heading">Withdrawable Balance </p>
                        <h2 class="withdrw_balanceamount">AED <?=$winning_balance;?></h2>
                        <div class="chart easyPieChart" data-percent="<?=$percentage;?>"
                            style="width: 80px; height: 80px; line-height: 70px;"><span
                                class="withdraw_amount">100%</span>
                        </div>
                    </div>
                <?php else: ?>

                <!--- bank, crypto,cash-->
                <div class="withdraw_circles">
                    <p class="balance_heading">* Minimal withdrawal amount 100 AED </p>

                    <div>
                        <ul class="withdraw_listing ">
                            <li>
                                <button class="withdraw_info">
                                    <input name="withdraw" type="radio" class="section" id="bank" data-section="bank-container">
                                    <label for="bank">Bank</label>
                                </button>

                                <form action="<?=base_url('wallet-submit')?>" method="POST" class="bank-container form-container d-none mt-2">
                                    <div class="bank_details">
                                        <h2>Enter Your Bank Details</h2>
                                        <div class="bank-details-form">
                                            <input name="account_holder_name" type="text" placeholder="Enter Account Holder Name" required>
                                        </div>
                                        <div class="bank-details-form">
                                            <input name="bank_name" type="text" placeholder="Enter Bank Name" required>
                                        </div>
                                        <div class="bank-details-form">
                                            <input name="account_no" type="number" placeholder="Enter Account Number" required>
                                        </div>
                                        <div class="bank-details-form">
                                            <input name="confirm_account_no" type="number" placeholder="Confirm Account Number" required>
                                        </div>
                                        <div class="bank-details-form">
                                            <input name="ifsc_code" type="text" placeholder="Enter IBAN Number/IFSC" required>
                                        </div>
                                        <div class="bank-details-form">
                                            <input name="amount" type="number" class="placeholder-box" placeholder="Enter Amount" min="100" value="<?=$winning_balance;?>" required>
                                        </div>  
                                        <div class="bank-details-form">
                                            <input name="wallet_type" type="hidden" value="Bank">
                                            <button type="submit" value="SaveChanges" class="contact_btn">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </li>

                            <li>
                                <button class="withdraw_info">
                                    <input name="withdraw" type="radio" class="section" id="cash" data-section="cash-container">
                                    <label for="cash" >Cash</label>
                                </button>

                                <form action="<?=base_url('wallet-submit')?>" method="POST"  class="cash-container form-container d-none">
                                    <div class="bank_details">
                                        <h2>Radeem at the UWINN store</h2>
                                        <div class="bank-details-form">
                                            <input name="amount" type="number" class="placeholder-box" placeholder="Enter Amount" min="100" value="<?=$winning_balance;?>" required>
                                        </div>
                                        <div class="bank-details-form">
                                            <input name="wallet_type" type="hidden" value="Cash">
                                            <button type="submit" value="Submit" class="contact_btn" id="genterate_model">Generate Voucher</button>
                                        </div>
                                    </div>
                                </form>
                            </li>
                            <li>
                                <button class="withdraw_info">
                                    <input name="withdraw" type="radio" class="section" id="crypto" data-section="crypto-container">
                                    <label for="crypto">Crypto</label>
                                </button>

                                <form action="<?=base_url('wallet-submit')?>" method="POST" class="crypto-container form-container d-none">
                                    <div class="bank_details">

                                        <h2>Enter Crypto Details</h2>
                                        <div class="bank-details-form">
                                            <input name="cripto_id" type="text" class="placeholder-box" placeholder="Enter Crypto Id">
                                        </div>
                                        <div class="bank-details-form">
                                            <input name="amount" type="number" class="placeholder-box" value="<?=$winning_balance;?>" placeholder="Enter Amount">
                                        </div>
                                        <div class="bank-details-form">
                                            <input name="wallet_type" type="hidden" value="Cripto">
                                            <button type="submit" value="Submit" class="contact_btn">Submit</button>
                                        </div>
                                    </div>
                                </form>
                            </li>
                        </ul>
                        

                    </div>

                </div>

            <?php endif;?>
            </div>
        </div>
        <div class="history-btn">
            <button type="submit" value="Submit" class="contact_btn transaction-history">Transaction History</button>
            <button type="submit" value="Submit" class="contact_btn voucher-history-btn"> Voucher History</button>
        </div>
    </div>
</div>
 
<div class="add-credit-container d-none">
    
     <div class="container">
            <div class="row">
                <div class="col-12 px-0">
                    <div class="result_list">
                        <div class="inner_header d-flex justify-content add-credit-section">
                            <a href="javascript:void(0)" id="credit" data-section="payment-container"><i class="bi bi-chevron-left"></i>Add Credit </a>
                        </div>
                        <div class="inner_section">
                         
                            <h1 class="trasection_selection">Select Transaction Type</h1>
                            <div class="add_credit">

                                <div class="Transaction_btn">
                                    <input name="payment" type="radio" class="section" id="credit_payment" data-section="credit-payment-container">
                                    <label for="credit_payment" class="mb-0">Credit Card/Debit Card</label>
                                </div>
                                <div class="Transaction_btn">
                                    <input name="payment" type="radio" class="section" id="redeem_coupon" data-section="redeem-coupon-container">
                                    <label for="redeem_coupon" class="mb-0">Redeem Coupon</label>
                                </div>

                                <!--Credit Payment form Start  -->
                                <div class=" credit-payment-container form-container mt-3 d-none" id="crypto_amount">
                                    <form action="<?=base_url('wallet-submit')?>" method="POST" class='transfer-section'>
                                        <div class="transaction-container mt-4 ">
                                           <h2 class="trasection_selection mb-0">Credit Amount</h2>

                                            <div class="input-group">
                                                <input type="number" name="amount" min="10" id="paymentAmount" placeholder="Enter Transfer Amount" required>
                                                <span class="currency linear-background">AED</span>
                                            </div>
                                            <div class="d-flex justify-content upont-amount">
                                            <p>25 UPoints</p>
                                            <p>50 UPoints</p>
                                            <p>100 UPoints</p>
                                            </div>
                                            <p class="notes_point mb-0 mt-2"> * 1 UPoint = 1AED </p>
                                            <input name="wallet_type" type="hidden" value="online-topup" class="placeholder-box">
                                            <button name="Submit" value="SaveChanges" class="contact_btn"> Submit</button>    
                                        </div>
                                    </form>
                                </div>
                                <!--Credit Payment form End  -->

                                <!--Credit Payment form Start  -->
                                <form action="<?=base_url('wallet-submit')?>" method="POST" class="transfer-section redeem-coupon-container form-container d-none mt-3">
                                    <div class="payment-container mt-4">
                                       <div class="withdraw_circles coupencode">
                                        <p>Enter Coupen Code</p>
                                        <input name="coupon_code" type="text" class="placeholder-box" placeholder="Enter Coupon Code">
                                        <input name="wallet_type" type="hidden" value="voucher" class="placeholder-box">
                                        <button class="contact_btn">Submit</button>
                                       </div>
                                    </div>
                                </form>
                                <!--Credit Payment form End  -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>

<div class="transaction-history-container d-none">
    <div class="mobile_warpper">
        <div class="container">
            <div class="row">
                <div class="col-12 px-0">
                    <div class="result_list">
                        <div class="inner_header d-flex justify-content">
                            <a href="javascript:void(0)" id="transection-history"><i class="bi bi-chevron-left"></i> Transection Histroy </a>
                            <i class="bi bi-calendar4 calendar-input-icon"></i>
                        </div>

                        <div class="bank-details-form">
                            <input name="account_holder_name"  id="calendarInput" type="hidden">
                        </div>

                        <?php if($transactionHistory): ?>
                            <?php foreach ($transactionHistory as $key => $items): $j++; ?>
                                <div class="trasection_details">
                                    <div class="d-flex justify-content">
                                        <h4 class="mb-0"><?=date('d M Y H:i', strtotime($items['created_at'])); ?></h4>
                                        <P class="<?=$items['record_type'] == 'Credit'?'credit_amount':'debit_amount';?>  mb-0"> 
                                            <?=$items['record_type'] == 'Credit'?'+':'-';?>
                                            <?=$items['upoints'];?>
                                        </P>
                                    </div>
                                   <hr>

                                   <!-- If Narration avaialble than show Narration Start-->
                                       <h1 class="mb-0">Narration : <?=$items['narration'];?></h1>
                                   <!-- If Narration avaialble than show Narration End-->
                                   <p class="mb-0">
                                    <?php if($items['narration'] == "Redeem Prize" || strpos((string)$items['narration'], 'Order Cancelled') === 0): ?>
                                        <?php if($items['order_id']): ?>
                                         Ticket ID :-  <?=$items['order_id'] ?> <br>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?=$items['remarks'];?>
                                   </p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="trasection_details">
                                <p class="mb-0">No Records found</p>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<div class="voucher-history-container d-none">
    <div class="mobile_warpper">
            <section class="login_sec">
                <div class="inner_header d-flex justify-content">
                    <a href="javascript:void(0)" class="voucher-history"><i class="bi bi-chevron-left"></i> Voucher Histroy </a>
                </div>
                    <div class="container">
                        <div class="">
                            <div class="voucher_page">
                                <ul class="nav nav-tabs mt-4">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#mobnumber">Active </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link " data-bs-toggle="tab" href="#withemailaccount">Used</a>
                                    </li>
                                </ul>
                                <!-- Tab panes -->
                                <div class="tab-content">
                                    <div class="tab-pane active" id="mobnumber">
                                        
                                        <?php if($cash_vouchers): ?>
                                            <?php foreach ($cash_vouchers as $key => $VoucherItems): $j++; ?>
                                                <?php if($VoucherItems['status'] =="A"):?>
                                                <div class="trasection_details mx-0">
                                                    <div class="d-flex justify-content">
                                                        <div>
                                                            <h1 class="create_on">Created Ons</h1>
                                                            <h4 class="mb-0">  <?=$VoucherItems['created_at']?> </h4>
                                                        </div>
                                                        <div>
                                                            <P class="credit_amount mb-0">AED <?=$VoucherItems['amount'];?></P>
                                                        </div>
                                                    </div>

                                                    <hr>
                                                    <div class="d-flex justify-content">
                                                        <h1 class="mb-0">Voucher : <?=$VoucherItems['coupon_code'] ?></h1>
                                                        <div class="qr-code_div" id="genterate_model" data-voucherID="<?=$VoucherItems['coupon_code'];?>" data-qrCode="<?=$VoucherItems['coupon_code'] ?>,qr_code_<?=$key;?>">
                                                            <img src="<?=base_url('assets/frontend_mobile/mob-img/qr-code.png');?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="Voucher_model popup <?=$VoucherItems['coupon_code'];?>" tabindex="-1" role="dialog">
                                                    <div class="modal-dialog mx-0 my-0" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-body pb-0">
                                                                <h1> Cash Voucher</h1>
                                                                
                                                                <div class="qr-code" >
                                                                    <div  class="qr-section" id="qr_code_<?=$key;?>"></div>
                                                                </div>

                                                                <div class="d-flex dollercenter mt-3">
                                                                    <img src="<?=base_url('assets/frontend_mobile/mob-img/doller_img.png');?>" class="doller_img mx-3">
                                                                    <P class="credit_amount mb-0">AED <?=$VoucherItems['amount'];?></P>
                                                                </div>
                                                                <p>Voucher Code :      <?=$VoucherItems['coupon_code'];?>       </p>
                                                                <p>Verification Code : <?=$VoucherItems['verification_code'];?> </p>
                                                                <div class="text-center">
                                                                    <button type="submit" value="Submit" class="vouhcer-close-btn contact_btn">Close</button>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endif;?>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="trasection_detail">
                                                <h1 class="text-center">Voucher id not found!</h1>
                                            </div>
                                        <?php endif; ?>

                                    </div>
                                    <div class="tab-pane fade" id="withemailaccount">
                                       <?php if($cash_vouchers): ?>
                                            <?php foreach ($cash_vouchers as $key => $VoucherItems): $j++; ?>
                                                <?php if($VoucherItems['status'] =="C"):?>
                                                <div class="trasection_details mx-0">
                                                    <div class="d-flex justify-content">
                                                        <div>
                                                            <h1 class="create_on">Created Ons</h1>
                                                            <h4 class="mb-0">  <?=$VoucherItems['created_at']?> </h4>
                                                        </div>
                                                        <div>
                                                            <P class="credit_amount mb-0">AED <?=$VoucherItems['amount'];?></P>
                                                        </div>
                                                    </div>

                                                    <hr>
                                                    <div class="d-flex justify-content">
                                                        <h1 class="mb-0">Voucher : <?=$VoucherItems['coupon_code'] ?></h1>
                                                        <div class="qr-code_div" id="genterate_model" data-voucherID="<?=$VoucherItems['coupon_code'];?>" data-qrCode="<?=$VoucherItems['coupon_code'] ?>,qr_code_used_<?=$key;?>">
                                                            <img src="<?=base_url('assets/frontend_mobile/mob-img/qr-code.png');?>">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="Voucher_model popup <?=$VoucherItems['coupon_code'];?>" tabindex="-1" role="dialog">
                                                    <div class="modal-dialog mx-0 my-0" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-body pb-0">
                                                                <h1> Cash Voucher</h1>
                                                                
                                                                <div class="qr-code" >
                                                                    <div  class="qr-section" id="qr_code_used_<?=$key;?>"></div>
                                                                </div>

                                                                <div class="d-flex dollercenter mt-3">
                                                                    <img src="<?=base_url('assets/frontend_mobile/mob-img/doller_img.png');?>" class="doller_img mx-3">
                                                                    <P class="credit_amount mb-0">AED <?=$VoucherItems['amount'];?></P>
                                                                </div>
                                                                <p>Voucher Code :      <?=$VoucherItems['coupon_code'];?>       </p>
                                                                <p>Verification Code : <?=$VoucherItems['verification_code'];?> </p>
                                                                <div class="text-center">
                                                                    <button type="submit" value="Submit" class="vouhcer-close-btn contact_btn">Close</button>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endif;?>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="trasection_detail">
                                                <h1 class="text-center">Voucher id not found!</h1>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </section>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        
        $(document).on('click', ".section" , function() {
           let section = $(this).attr('data-section');
           // console.log(section)
          
           if(section  == "transfer-container"){
                $(".transfer-container, .withdraw-container , .bank-container , .cash-container , .crypto-container ").addClass('d-none');
                $('.'+ section).removeClass('d-none');
           }else if( section  == "withdraw-container"){  
                $(".transfer-container, .withdraw-container ").addClass('d-none');
                $('.'+ section).removeClass('d-none');
           }else if(section  == "bank-container" || section  == "cash-container" || section  == "crypto-container"){
                $(".bank-container, .cash-container, .crypto-container").addClass('d-none');
                $('.'+ section).removeClass('d-none');
           }else if(section  == "credit-payment-container" || section  == "redeem-coupon-container" ){
                $(".credit-payment-container, .redeem-coupon-container").addClass('d-none');
                $('.'+ section).removeClass('d-none');
           }else{
                $('.form-container').addClass('d-none');
                $('.'+ section).removeClass('d-none');
           }
        });

    });

    $('#submitButton').on('click', function() {
        var transferAmount = $('#transferAmount').val();
        if (transferAmount <= 0) {
            alertMessageModelPopup('Transfer amount must be greater than 0.','danger'); 
            return false; // Prevent the modal from showing
        } else {
            $('#confirmationModal').modal('show'); // Show the modal
        }
    });

    $('#confirmSubmit').on('click', function() {
        $('#transferForm').submit();
    });
 
     function generateQrCode(qrContent, id) {
         new QRCode(document.getElementById(id), {
            text: qrContent,
            width: 150,
            height: 150,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H,
        });
    }

    $(document).on('click','.qr-code_div', function(){
        var qrCodeData  = $(this).data('qrcode');

        console.log(qrCodeData);

        var qrCodeParts = qrCodeData.split(',');
        var qrCodeData  = qrCodeParts[0];
        var elementId   = qrCodeParts[1];
        $('.qr-section').empty('fast');
        generateQrCode(qrCodeData, elementId);
    })

</script>
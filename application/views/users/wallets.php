<script src="<?=base_url('assets/frontend/js/qrcode.min.js');?>"></script>
<?php 
    $percentage     = ($winning_balance/100)*100;
    $percentage     =  $percentage< 100 ? $percentage.'%' :  "100%";
    $minPercentage  = $percentage<5 ? '6%': $percentage ; 
?>

<div class="col-md-9">
    <div class="card_Big2">
        <div class="container">
            <div class="row pt-3">
                <div class="col-md-6">
                    <p class="wallets_1">Wallets</p>
                </div>
                <div class="col-md-6">
                    <div class="transection-btns">
                         
                        <div class="button_3 section" id="transaction" data-section="transaction-container">
                            <p> <span>+</span> Add Transaction</p>
                        </div>   


                        <div class="button_3 section" id="credit" data-section="payment-container">
                            <p> <span>+</span> Add  Credit</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row balance-container">
                <div class="col-sm-12 col-md-6 col-lg-6">
                    <div class="balance-section">
                        <label for="play_balance" class="balance-lable">Play Balance</label>
                        <span id="play_balance" class="balance-amount"> UPoint <?=$play_balance;?> </span>
                    </div>
                </div>
                <div class="col-sm-12 col-md-6 col-lg-6 text-right">
                    <div class="balance-section">
                        <label for="winning_balance" class="balance-lable">Winning Balance</label>
                        <span id="winning_balance" class="balance-amount">UPoint <?=$winning_balance;?> </span>
                    </div>
                        <sup class="text-danger">winning amount will be reflected with in 30 minutes</sup>
                </div>
                 <div class="col-sm-12 col-md-12 col-lg-12">
                     <div class="text-left mt-2">
                        <label class="mb-0 note_point"> *AED 1 = 1 UPoint </label>
                    </div>
                 </div>
            </div>

            <div class="wallet-form">
                
                <div class="row transaction-container form-container ">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <p class="select_Transaction pt-0">Select Transaction Type</p>
                    </div>
                    <?php /*
                    <div class="col-md-6">
                        <div class="text-section">
                            <input name="wallet_type" type="radio" class="section" id="transfer" data-section="transfer-container">
                            <label for="transfer" class="mb-0">Transfer</label>
                        </div>
                    </div>
                    */ ?>

                    <div class="col-md-6">
                        <div class="text-section">
                            <input name="wallet_type" type="radio" class="section" id="withdraw" data-section="withdraw-container" >
                            <label for="withdraw" class="mb-0">Withdraw</label>
                        </div>
                    </div>
                </div>

                <!--Payment form Start  -->
                <div class="row payment-container form-container d-none">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <p class="select_Transaction pt-0">Select Payment Type</p>
                    </div>

                    <div class="col-md-6">
                        <div class="text-section">
                            <input name="payment" type="radio" class="section" id="credit_payment" data-section="credit-payment-container">
                            <label for="credit_payment" class="mb-0">Credit Card/Debit Card</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-section">
                            <input name="payment" type="radio" class="section" id="redeem_coupon" data-section="redeem-coupon-container">
                            <label for="redeem_coupon" class="mb-0">Redeem Coupon</label>
                        </div>
                    </div>
                </div>
                <!--Payment form Start  -->

                <!--Credit Payment form Start  -->
                <div class=" credit-payment-container form-container mt-3 d-none" id="crypto_amount">
                    <form action="<?=base_url('wallet-submit')?>" method="POST" class='transfer-section'>
                        <div class="row">
                            <div class="col-sm-12 col-md-6 col-lg-6">
                                <div class=" ">
                                    <label for="paymentAmount">Enter Amount</label>
                                    <div class="input-group">
                                        <input type="number" name="amount" min="10" id="paymentAmount" placeholder="Enter Transfer Amount" required>
                                        <span class="currency linear-background">AED</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-md-6 col-lg-6">
                                <input name="wallet_type" type="hidden" value="online-topup" class="placeholder-box">
                                <button name="Submit" value="SaveChanges" class="submit-button linear-background coupen_code submit_wallet"> Submit</button>
                            </div>
                        </div>
                    </form>
                     
                </div>
                    
                <!--Credit Payment form End  -->


                <!--Credit Payment form Start  -->
                <form action="<?=base_url('wallet-submit')?>" method="POST" class="transfer-section redeem-coupon-container form-container d-none mt-3">
                    <div class="row">
                        <div class="col-sm-12 col-md-5 col-lg-5 inner-form-content">
                            <input name="coupon_code" type="text" class="placeholder-box" placeholder="Enter Coupon Code">
                        </div>
                        <div class="col-sm-12 col-md-4 col-lg-4 inner-form-content mt-0" >
                            <input name="wallet_type" type="hidden" value="voucher" class="placeholder-box">
                            <button name="Submit" value="SaveChanges" class="submit-button linear-background coupen_code"> Submit</button>
                        </div>
                    </div>
                </form>
                <!--Credit Payment form End  -->


                <!--Transfer form Start  -->
                <form action="<?=base_url('wallet-submit')?>" method="POST" class="transfer-section transfer-container form-container d-none mt-3" id="transferForm">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12">
                            <label class="play_balance">Move money from winning balance to play balance.</label><br>
                            <label for="transferAmount" class="play_balance">Enter Transfer Amount</label>
                            <div class="input-group">
                                <input type="number" min="1" name="winning_amount" id="transferAmount" value="<?=$winning_balance;?>" placeholder="Enter Transfer Amount" required>
                                <span class="currency linear-background">AED</span>
                            </div>
                            <div class="col-sm-12 col-md-12 col-lg-12 inner-form-content">
                                <input name="wallet_type" type="hidden" value="transfer-amount" class="placeholder-box">
                                <!-- Trigger Modal -->
                                <button type="button" class="submit-button linear-background coupen_code mt-0" data-bs-toggle="modal" id="submitButton">Submit</button>
                            </div>
                        </div>
                    </div>
                </form>

                <!-- Confirmation Modal -->
                <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="confirmationModalLabel">Confirm Transfer</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i class="bi bi-x"></i></button>
                            </div>
                            <div class="modal-body">
                                <h5>You cannot reverse this !</h5>
                                <small> Are you sure you want to transfer the winning amount to your play balance? </small>
                            </div>
                            <div class="modal-footer">
                                <!-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button> -->
                                <button type="button" class="btn btn-primary" id="confirmSubmit">Yes, Transfer</button>
                            </div>
                        </div>
                    </div>
                </div>
                <!--Transfer form End  -->

                <!--Withdraw form Start  -->
                <div class="row withdraw-container form-container mt-2 d-none">
                    <?php if($winning_balance < 100): ?>
                        <div class="col-sm-12 col-md-12 col-lg-12 inner-form-content">
                            <p class="balance_heading">Withdrawable Balance </p>
                            <div class="progress-bg">
                                <div class="progress-bar" style="width:<?=$minPercentage;?> !important;">
                                  <p class="raised"><?=$percentage;?></p>
                                </div>
                                <p class="goal <?=$percentage == 100 ? 'd-none':''; ?>">100 AED <?$percentage;?> </p>
                            </div>
                        </div> 

                    <?php else: ?>
                        <div class="col-sm-12 col-md-12 col-lg-12 withdraw-section">
                            <div class="row">
                                <div class="col-sm-12 col-md-12 col-lg-12">
                                    <div class="transfer-container mt-2">
                                        <div class="col-sm-12 col-md-12 col-lg-12">
                                            <p class="note_point">* Minimal withdrawal amount 100 AED</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 withdraw">
                                    <div class="text-section">
                                        <input name="withdraw" type="radio" class="section" id="bank" data-section="bank-container">
                                        <label for="bank" class="mb-0">Bank</label>
                                    </div>
                                </div> 
                                <div class="col-md-4 withdraw">
                                    <div class="text-section">
                                        <input name="withdraw" type="radio" class="section" id="cash" data-section="cash-container">
                                        <label for="cash"  class="mb-0">Cash</label>
                                    </div>
                                </div>
                                <div class="col-md-4 withdraw">
                                    <div class="text-section">
                                        <input name="withdraw" type="radio" class="section" id="crypto" data-section="crypto-container">
                                        <label for="crypto"  class="mb-0">Crypto</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif;?>
                </div>
                <!--Withdraw form End  -->

                <!--Withdraw - bank form Start  -->
                <form action="<?=base_url('wallet-submit')?>" method="POST" class="bank-container form-container d-none mt-2">
                    <div class="row ">
                        <div class="col-sm-12 col-md-12 col-lg-12 inner-form-content">
                            <label>Enter Your Bank Details</label>
                        </div>
                        <div class="col-sm-12 col-md-4 col-lg-6 inner-form-content">
                            <input name="account_holder_name" class="placeholder-box" type="text" placeholder="Enter Account Holder Name" required>
                        </div>
                        <div class="col-sm-12 col-md-4 col-lg-6 inner-form-content">
                            <input name="bank_name" type="text" class="placeholder-box" placeholder="Enter Bank Name" required>
                        </div>
                        <div class="col-sm-12 col-md-4 col-lg-4 inner-form-content">
                            <input name="account_no" type="number" class="placeholder-box" placeholder="Enter Account Number" required>
                        </div>
                        <div class="col-sm-12 col-md-4 col-lg-4 inner-form-content">
                            <input name="ifsc_code" type="text" class="placeholder-box" placeholder="Enter IFSC" required>
                        </div>
                         <div class="col-sm-12 col-md-4 col-lg-4 inner-form-content">
                            <input name="amount" type="number" class="placeholder-box" placeholder="Enter Amount" min="100" value="<?=$winning_balance;?>" required>
                        </div>
                        <div class="col-sm-12 col-md-9 col-lg-9">
                        <p class="note_point">* Minimal withdrawal amount 100 AED</p>
                        </div>
                        <div class="col-sm-12 col-md-3 col-lg-3 inner-form-content">
                            <input name="wallet_type" type="hidden" value="Bank">
                            <button name="Submit" value="SaveChanges" class="submit-button linear-background wallet-btn mt-0 coupen_code">Submit</button>
                        </div>
                       
                    </div>
                </form>
                <!--Withdraw - bank form End  -->

                <!--Withdraw - cash form Start  -->
                <div class="cash-container form-container mt-2 d-none">
                    <form action="<?=base_url('wallet-submit')?>" method="POST" >
                      <div class="row">
                         <div class="col-sm-12 col-md-3 col-lg-3 inner-form-content">
                            <input name="amount" type="number" class="placeholder-box" placeholder="Enter Amount" min="100" value="<?=$winning_balance;?>" required>
                        </div>
                        
                         <div class="col-sm-12 col-md-4 col-lg-4 inner-form-content">
                            <input name="wallet_type" type="hidden" value="Cash">
                            <button name="Submit" value="SaveChanges" id="generator_btn" class="submit-button <?= $percentage >= 100? 'linear-background' :'' ;?>" <?= $percentage <100 ? "disabled" :'';?>  id="cash_vaucher" > Generate Cash Vaucher</button>
                        </div>

                        <div class="col-sm-12 col-md-5 col-lg-5">
                            <p class="note_point pt-2" >* Minimal withdrawal amount 100 AED</p>
                        </div>
                      </div>
                    </form>
                    <div class="card_Bigticket">
                        <div class="container">
                            <p class="my_Ticketspage mb-2">Cash Voucher</p>
                            <div class="trnsection_history_list ">
                                <?php if($cash_vouchers): ?>
                                    <?php foreach ($cash_vouchers as $key => $VoucherItems): $j++; ?>
                                        <div class="row">
                                            <div class="col-md-6">
                                              
                                              <div class="trnsection_history_list_innercontent">
                                               <h1>Coupon Code : </h1>
                                               <p> <?=$VoucherItems['coupon_code'] ?> </p>
                                              </div>

                                              <div class="trnsection_history_list_innercontent">
                                               <h1>Verification Code : </h1>
                                               <p> <?=$VoucherItems['verification_code'] ?> </p>
                                              </div>

                                              <div class="trnsection_history_list_innercontent">
                                               <h1>Amount : </h1>
                                               <p> <?=$VoucherItems['amount'] ?> </p>
                                              </div>
                                            </div>
                                            <div class="col-md-6">

                                              <?php 
                                                if($VoucherItems['status'] == "A" ):
                                                 $status = 'Active';
                                                 $statusclss = 'voucher-status';
                                                elseif($VoucherItems['status'] == "I" ):
                                                 $status = 'Rejected';
                                                 $statusclss = 'voucher-status-rejected';
                                                elseif($VoucherItems['status'] == "C" ):
                                                 $status = 'Completed';
                                                 $statusclss = 'voucher-status-completed';
                                                endif; 
                                              ?>
                                              <div class="trnsection_history_list_innercontent">
                                               <h1>Status : </h1>
                                               <p> <div class="<?=$statusclss;?>"><?=$status; ?></div> </p>
                                              </div>

                                              <div class="trnsection_history_list_innercontent">
                                               <h1>QR : </h1>
                                               <p>  
                                                <div class="qr-code"  data-qrCode="<?=$VoucherItems['coupon_code'] ?>,qr_code_<?=$key;?>" >
                                                    <i class="fas fa-eye-slash"></i>
                                                    <div  class="qr-section" id="qr_code_<?=$key;?>"></div>
                                                </div>
                                               </p>
                                              </div>
                                            </div>
                                            
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>

                            </div>
                        </div>
                    </div>

                    <div class="pagination-container mt-2">
                        <?php 
                            foreach($voucherPagination as $item):
                                echo $item;
                            endforeach;
                        ?>
                    </div>
                </div>
                <!--Withdraw - bank form End  -->

                 <!--Withdraw - crypto form Start  -->
                <form action="<?=base_url('wallet-submit')?>" method="POST" class="crypto-container form-container mt-2 d-none">
                    <div class="row">
                        <div class="col-sm-12 col-md-12 col-lg-12 inner-form-content">
                            <label>Enter Crypto Details</label>
                        </div>
                        <div class="col-sm-12 col-md-4 col-lg-5 inner-form-content">
                            <input name="cripto_id" type="text" class="placeholder-box" placeholder="Enter Crypto Id">
                        </div>
                         <div class="col-sm-12 col-md-4 col-lg-4 inner-form-content">
                            <input name="amount" type="number" class="placeholder-box" value="<?=$winning_balance;?>" placeholder="Enter Amount">
                        </div>
                        <div class="col-sm-12 col-md-4 col-lg-3 inner-form-content">
                            <input name="wallet_type" type="hidden" value="Cripto">
                            <button name="Submit" value="SaveChanges" class="submit-button linear-background mt-0 wallet-btn coupen_code"> Submit</button>
                        </div>
                        <div class="col-sm-12 col-md-6 col-lg-6">
                            <p class="note_point">* Minimal withdrawal amount 100 AED</p>
                        </div>
                    </div>
                </form>
                <!--Withdraw - bank form End  -->
            </div>

            <div class="row">
                <div class="card_Bigticket mt-2">
                   
                        <p class="my_Ticketspage mb-2">Transaction History</p>
                        
                        <div class="trnsection_history_list ">
                            <?php if($transactionHistory): ?>
                                <?php foreach ($transactionHistory as $key => $items): $i++; ?>
                                    <div class="row">
                                        <div class="col-md-7">

                                          <div class="trnsection_history_list_innercontent">
                                           <h1>Record Type :</h1>
                                           <p> <?=$items['narration'] ?> </p>
                                          </div>
                                          <div class="trnsection_history_list_innercontent">
                                             <h1>Status :</h1>
                                             <p>
                                                <span class="<?php if($items['record_type'] == 'Credit' ):?> green <?php else: ?>  red <?php endif;?>">  <?=$items['record_type'];?> </span>
                                             </p>
                                          </div>
                                          <div class="trnsection_history_list_innercontent">
                                             <h1>Narration :</h1>
                                             <p>
                                                <?php if($items['narration'] == "Redeem Prize"): ?>
                                                    <?php if($items['order_id']): ?>
                                                     Ticket ID :-  <?=$items['order_id'] ?> <br>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                                <?=$items['remarks'] ?>
                                             </p>
                                         </div>
                                        </div>
                                        <div class="col-md-5">
                                            <div class="trnsection_history_list_innercontent ">
                                                <h1>Created Date :  </h1>
                                                <p>
                                                    <?=date('d M Y h:i:s A', strtotime($items['created_at'])); ?>
                                                </p>
                                            </div>
                                            
                                            <?php if($items['record_type'] == 'Credit' ):?>  
                                            <div class="trnsection_history_list_innercontent">
                                                <h1>Credit : </h1>
                                                <p>
                                                      <?=$items['upoints'];?>
                                                </p>
                                            </div>
                                            <?php endif;?>
                                            <?php if($items['record_type'] == 'Debit' ):?>  
                                                <div class="trnsection_history_list_innercontent">
                                                    <h1>Debit : </h1>
                                                    <p>
                                                      <?=$items['upoints'];?>
                                                     </p>
                                                </div>
                                            <?php endif;?>
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
                        </div>
                            
                        <!-- pagination start  -->
                        <div class="pagination-container mt-2">
                            <?php 
                                foreach($pagination as $item):
                                    echo $item;
                                endforeach;
                            ?>
                        </div>
                        <!-- pagination End -->
                </div>
            </div>

        </div>
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

    $(document).on('click','.qr-code', function(){
        var qrCodeData  = $(this).data('qrcode');
        var qrCodeParts = qrCodeData.split(',');
        var qrCodeData  = qrCodeParts[0];
        var elementId   = qrCodeParts[1];
        $('.qr-section').empty('fast');
        if ($(this).find('i').hasClass('fa-eye-slash')) {
            generateQrCode(qrCodeData, elementId);
            $(this).find('i').removeClass('fa-eye-slash').addClass('fa-eye');
        } else {
            $(this).find('i').removeClass('fa-eye').addClass('fa-eye-slash');
        }
    })
</script>

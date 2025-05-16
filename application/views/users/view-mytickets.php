<div class="col-md-9">
    <div class="card_Bigticket">
        <div class="container">
            <p class="my_Ticketspage mb-2"></p>
            <div class="tabs_6">
                <div class="w3-container city">
                    <div class="trnsection_history_list ">
                        <?php if($OrderDetails): ?>
                            <?php foreach ($OrderDetails as $key => $item): ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="trnsection_history_list_innercontent">
                                            <h1>Purchased On :</h1>
                                            <p>
                                                <?=date('d M H:i A' , strtotime($item->created_at));?>
                                            </p>
                                        </div>
                                        <div class="trnsection_history_list_innercontent">
                                            <h1>Campaign :</h1>
                                            <p>
                                                <?=$item->product_qty;?>x<?=ucwords($item->product_title);?>
                                            </p>
                                        </div>

                                        <div class="trnsection_history_list_innercontent">
                                             <h1>Ticket Id :</h1>
                                             <p>
                                                <?=$item->order_id;?>
                                             </p>
                                        </div>

                                        <div class="trnsection_history_list_innercontent">
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
                                    </div>

                                   
                                        <?php if($item->draw):?>
                                        <div class="col-md-6">
                                            <div class="ticket_details_headings">
                                                <h4>Ticket Details</h4>
                                            </div>

                                            <?php foreach ($item->draw as $key => $winningItems): ?>
                                                <?php  $CouponArray = explode(',',$winningItems->coupons); ?>
                                                <div class="circle_Flex">
                                                    <?php foreach($CouponArray as $couponItem): ?>
                                                        <div class="circle_01"> 
                                                            <p class="number_011"><?=$couponItem;?></p>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        
                                        </div>
                                        <?php endif;?>

                                    
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
                </div>
            </div>
        </div>
    </div>
</div>

 <script>
    // Set the date we're counting down to
        var draw_date  = "<?=$item->draw_date_time;?>";
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
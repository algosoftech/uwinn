<div class="col-md-9">
    <div class="card_Bigticket">
        <div class="container">
            <p class="my_Ticketspage mb-2">My Tickets</p>
            <form action="<?=base_url('my-ticket')?>" method="POST" class="my-order">
                <div class="row">
                    <div class="col-md-4 col-12">
                        <p class="select_Draw">Select Purchase Date</p>
                        <div class="inner-form-content">
                            <input type="date" name="draw_date" id="draw_date" value="<?=$draw_date;?>"  class="placeholder-box8" placeholder="Select Date">
                        </div>
                    </div>
                    <div class="col-md-4 col-12">
                        <p class="select_campaign">Select Campaign Name</p>
                        <div class="inner-form-content">
                            <select name="CampaignName" class="CampaignName placeholder-box8">
                                <option>All</option>
                                <?php if($ourCampaigns): ?>
                                    <?php foreach($ourCampaigns as $key => $item): ?>
                                      <option value="<?=$item['title'];?>"  <?=$product == $item['title'] ? 'selected':'';?> > <?=$item['title'];?> </option>
                                    <?php endforeach;  ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4 col-12">
                        <button class="btn btn-reset"> search</button>
                    </div>
                </div>
            </form>

            <div class="tabs_6">
                <div class="w3-container city">
                    <div class="trnsection_history_list ">
                        <?php if($OrderDetails): ?>
                            <?php foreach ($OrderDetails as $key => $item): ?>
                                <div class="row">
                                    <div class="col-md-6">
                                      <div class="trnsection_history_list_innercontent">
                                         <h1>Purchase Date :</h1>
                                         <p>
                                            <?=date('d M H:i A' , strtotime($item->created_at));?>
                                         </p>
                                     </div>
                                     <div class="trnsection_history_list_innercontent">
                                         <h1>Campaign Name :</h1>
                                         <p>
                                            <?=ucwords($item->product_title);?>
                                         </p>
                                     </div>

                                      <div class="trnsection_history_list_innercontent">
                                         <h1>Order Id :</h1>
                                         <p>
                                            <?=$item->order_id;?>
                                         </p>
                                     </div>

                                     <div class="trnsection_history_list_innercontent">
                                             <h1>Draw Date :</h1>
                                             <p>
                                                <?=date('d M H:i A' , strtotime($item->draw_date_time));?>
                                             </p>
                                        </div>
                                        <a  class='btn-reset' href="<?=base_url('my-ticket/view/'.$item->order_id)?>">View Details</a>


                                    </div>
                                    <div class="col-md-6">
                                        <div class="trnsection_history_list_innercontent">
                                            <h1>Ball Numbers : </h1>
                                            <p>
                                                <div class="boloclass">
                                                    
                                               
                                                <?php $tickets = json_decode($item->ticket);  ?>
                                                    <?php foreach($tickets as $couponArray): ?>
                                                     <div class="circle_Flex">
                                                        <?php foreach($couponArray as $couponItem): ?>
                                                             <div class="circle_01"> 
                                                                <p class="number_011"><?=$couponItem;?></p>
                                                            </div>
                                                        <?php endforeach; ?>
                                                     </div>
                                                    <?php endforeach; ?>
                                                 </div>
                                             </p>
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
                    </div>

                   

                   
       </div>
          <!-- pagination start  -->
           
         <!-- pagination End -->
   </div>

</div>

</div>
</div>
<div class="pagination-container mt-3">
                <?php 
                    foreach($pagination as $item):
                        echo $item;
                    endforeach;
                ?>
            </div>
<script>
    // $(document).ready(function(){
    //     $(document).on('change', '.showRow', function() {
    //         // let row = $(this).data('section');  
    //         let row = $(this).val();  
    //         $('.tickets_Space').addClass('d-none');
    //         if(row != 'All'){
    //             $('.'+row).removeClass('d-none');
    //         }else if(row == 'All'){
    //          $('.tickets_Space').removeClass('d-none');
    //         }
    //     });
    // });


    // $(document).on('change','#draw_date, .showRow' , function(){
    //     $('.my-order').submit();
    // });

</script>
 
    <div class="mobile_warpper pb-2">
        <div class="container">
            <div class="row">
                <div class="col-12 px-0">
                    <div class="result_list">
                        <div class="inner_section">
                            <?php if($OrderDetails): ?>
                              <?php foreach ($OrderDetails as $key => $item): ?>
                                <?php 
                                    $Pid = str_replace(' ','_' , $item->product_title);
                                    $Pid = str_replace('/','_' , $Pid); 
                                ?>

                                <div class="tcicket_info  <?=$Pid;?>">
                                    <button class="gray_btn"><?=date('d F Y, H:i A' , strtotime($item->created_at));?></button>
                                    <div class="my_tickect">
                                        <ul>
                                            <li>
                                                <h1><?=$item->order_id;?></h1>
                                                <p><?=date('d F Y, H:i' , strtotime($item->created_at));?></p>
                                            </li>
                                        </ul>
                                        <div class="product_infoses">
                                            <div class="product_info">
                                                <img src="<?=base_url($item->product_image);?>">
                                            </div>
                                            <div class="product_info_time">
                                                <strong>Quantity :</strong>
                                                <p><?=$item->product_qty;?> x <?=$item->product_title?></p>
                                                <h2>Draw On : <?=date('d M H:i A' , strtotime($item->draw_date_time));?></h2>
                                            </div>
                                        </div>
                                        <div class="reffle_btn_info">
                                            <button class="amunt_info">Amount : <?=$item->total_price;?> </button>
                                            <button class="views_info"> <a href="<?=base_url('my-ticket/view/'.$item->order_id)?>">View Details</a></button>

                                        </div>

                                    </div>
                                </div>
                              <?php endforeach; ?>
                            <?php endif; ?>
                            <div class="reffle_campign">
                                <ul class="">
                                    <?php if($ourCampaigns): ?>
                                          <li class="activeCampaigns active_tab"><span data-campaign="All" class="showlist">All</span></li>
                                        <?php foreach($ourCampaigns as $key => $item): ?>
                                            <?php 
                                                $Pid = str_replace(' ','_' , $item['title']); 
                                                $Pid = str_replace('/','_' , $Pid); 
                                            ?>
                                         <li class="activeCampaigns"><span data-campaign="<?=$Pid;?>" class="showlist"> <?=$item['title'];?>  </span></li>
                                        <?php endforeach;  ?>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
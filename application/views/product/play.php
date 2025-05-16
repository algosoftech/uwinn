<div class="main_wrapper  mb-0">
    <div class="wrapper pt-0">
        <div class="container">
            <?php $ListedCampaign = array();?>
            <?php if($ourCampaigns):  ?>
                <section class="megaboxes pb-0" id="productContainer">
                    <div class="container">
                       <div class="row">
                        <!--1 Campign showing in banner start -->
                        <?php if(!empty($ourCampaigns)): ?>
                            <?php foreach ($ourCampaigns as $itemKey => $item): ?>
                                <?php if($item['show_as_banner'] == 'on'): ?>

                                    <?php array_push($ListedCampaign, $item['title']); ?>
                                    <div class="col-12 col-md-6 ">
                                        <div class="product-container">
                                            <div class="product-sections">
                                                <div class="prod_secImg">
                                                    <a href="javascript:void(0);">
                                                        <img src="<?=ImageExist($item['product_image']);?>" alt="<?=$item['product_image_alt'];?>">
                                                    </a>
                                                </div>

                                                <div class="megaboxbutton">
                                                    <p>Product: <?=ucwords($item['title']);?></p>
                                                    <p>Price: <?=$item['straight_add_on_amount']?> AED</p>
                                                    <a class="mega_buy" href="<?=base_url('product/'.$item['title_slug'].'/'.Encript($item['products_id']));?>">BUY <i class="fa-solid fa-chevron-right"></i></a>
                                                </div>

                                            </div>
                                        </div>
                                    </div>


                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                       </div>
                      <!--1 Campign Section Showing in banner End -->
                     
                        <div class="row">
                            <?php foreach ($ourCampaigns as $itemKey => $item): ?>
                                <?php if($item['show_as_banner'] != 'on'): ?>
                                    <?php array_push($ListedCampaign, $item['title']); ?>
                                     <!--2 Large Section Showing in banner Start -->
                                    <div class="col-md-4 productsmainbox_show">
                                        <div class="u-winProduct">
                                            <div class="products_show nmeby">
                                                <div class="show_productIMG">
                                                    <img src="<?=ImageExist($item['product_image']);?>" alt="<?=$item['product_image_alt'];?>">
                                                </div>

                                            </div>

                                            <div class="prductsnmebtn">
                                                <div class="innerproductmebtn">
                                                    <h3> Product: <?=ucwords($item['title']);?></h3>
                                                    <div class="productsBtnname">
                                                        <p>Price: <?=$item['straight_add_on_amount']?> AED</p>
                                                        <a class="pricebuybtn" href="<?=base_url('product/'.$item['title_slug'].'/'.Encript($item['products_id']));?>">BUY <i class="fa-solid fa-chevron-right"></i></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!--2 Large cmapaign Section End -->
                                <?php endif; ?>
                            <?php endforeach ?>
                      

                      
                    </div>
                </section>
            <?php endif; ?>

            <!--4 Large cmapaign Section Start -->
            <?php if($ourCampaigns): ?>
                <div class="row">
                    <?php foreach ($ourCampaigns as $itemKey => $item): ?>
                        <?php if(!in_array( $item['title'],$ListedCampaign )): ?>
                            <div class="col col-md-4 mb-3">
                                <div class="megab_box mega_gray">
                                    <div class="megabpower_img">
                                        <img src="<?=ImageExist($item['product_image']);?>"  alt="<?=$item['product_image_alt'];?>"  />
                                    </div>
                                    <div class="d_text">
                                        <small>AED</small>
                                        <?php if(!empty($item['lotto_type'])): ?>
                                          <i><?=$item['prize_stright'.$item['lotto_type']]?></i>
                                        <?php endif; ?>
                                    </div>
                                    <div class="megab_timer">
                                        <div class="wintimer">
                                            <div class="mega_timer">
                                                <div class="timer">
                                                    <div id="countdown">
                                                        <div id='tiles' class="tiles" data-target="<?=$item['draw_date'].' '.$item['draw_time'];?>"></div>
                                                        <div class="labels">
                                                        <li>Days</li>
                                                        <li>Hours</li>
                                                        <li>Mins</li>
                                                        <li>Secs</li>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="play_buttn">
                                        <a href="<?=base_url('product/'.$item['title_slug'].'/'.Encript($item['products_id']));?>"><?=htmlspecialchars("Play ".ucfirst($item['title']) )?> <i class="bi bi-chevron-double-right"></i></a>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    <?php endforeach ?>
                </div>
             <?php endif; ?>
            <!--4 Large cmapaign Section End -->
        </div>
    </div>
</div>
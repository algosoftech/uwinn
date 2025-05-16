<div class="container pb-5">

<?php if($mobile_banner): ?>
    <div class="banner-container">
        <div class="winner_slider">
            <div class="swiper winnerSwiper">
                <div class="swiper-wrapper">
                    <?php foreach ($mobile_banner as $key => $bannerItem): ?>
                        <div class="banner-section swiper-slide">
                            <img src="<?=$bannerItem['image'];?>" class="slider-image">
                        </div>
                    <?php endforeach;?>
                </div>
            </div>
        </div>
    </div>
<?php endif;?>


<?php if($ourCampaigns):?>
  <div class="row">
    <?php foreach ($ourCampaigns as $key => $item): ?>
        <div class="col-sm-12 col-md-6 col-lg-6">
            <div class="product-section">
               <div class="accordation-headings">
                    <div class="accordion-title">
                        <div class="row">
                            <div class="col-4">
                                <div class="product_img">
                                    <a href="javascript:void(0);" class="list_dots" data-pid="product-detail-<?=$key;?>">
                                        <img src="<?=ImageExist($item['product_image']);?>" alt="<?=$item['product_image_alt'];?>">
                                    </a>
                                </div>
                            </div>
                            <div class="col-8 home_right_side">
                                <div class="campaign-detail-section">
                                    <h3 class="campaign-title"> <?=$item['title'];?>  </h3>
                                    <p class="campaign-description"> <?= wordLimiter($item['description'],10);?></p>
                                    <i class="bi bi-info-circle-fill info-icon list_dots" data-pid="product-detail-<?=$key;?>" ></i>
                                </div>
                                <div class="price-link-container">
                                    <div class="price-section">
                                        <div class="aed_price">
                                            <img src="<?=base_url('assets/frontend_mobile')?>/mob-img/imgpsh_fullsize_anim.png" alt="bg_img">
                                            <p> 
                                                <?=$item['straight_add_on_amount']?> AED
                                            </p>
                                        </div>
                                    </div>
                                    <div>
                                        <a href="<?=base_url('product/'.$item['title_slug'].'/'.Encript($item['products_id']));?>" class="buy-link">
                                            Buy Now
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
               </div>
               <div class="row campaign-detail-container  product-detail-<?=$key;?> d-none">
                    <div class="col-12">
                        <div class="campaign-detail-section">
                            <div class="row">
                                <div class="col-12">
                                 <div class="product_details">
                                    <i class="bi bi-arrow-left"></i>
                                    <ul class="nav nav-tabs">
                                        <li class="nav-item">
                                            <a class="nav-link text-center active" data-bs-toggle="tab" href="#product<?=$key;?>">Product Details</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link text-center" data-bs-toggle="tab" href="#prize<?=$key;?>">Prize Details</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content">
                                        
                                        <div class="tab-pane active" id="product<?=$key;?>">
                                            <div class="product_history">
                                                <h2><?=$item['title'];?></h2>
                                                <img src="<?=ImageExist($item['product_image']);?>" alt="<?=$item['product_image_alt'];?>">
                                                <?php /* product description */ ?>
                                                <?=$item['description'];?>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="prize<?=$key;?>">
                                            <div class="cardbox mb-4">
                                                <h3 class="prize-heading">If you are lucky, You are going to get</h3>
                                                <ul>
                                                    <?php if($item['prize_enable_title'] == "Y"):?>
                                                        <li>
                                                            <span> Prize Amount </span>
                                                            <strong> <?=$item['prize_title'];?></strong>
                                                        </li>
                                                    <?php endif; ?>

                                                    <?php if($item['prize_enable_stright_prize_heading'] == "Y"):?>
                                                        <li>
                                                            <span>Sharing grand</span>
                                                            <strong> <?=$item['prize_stright_prize_heading'];?></strong>
                                                        </li>
                                                    <?php endif; ?>

                                                    <?php if($item['prize_enable_rumble_mix_prize_heading'] == "Y"):?>
                                                        <li>
                                                            <span>Multiple win</span>
                                                            <strong> <?=$item['prize_rumble_mix_prize_heading'];?></strong>
                                                        </li>
                                                    <?php endif; ?>

                                                    <?php if($item['prize_enable_reverse_prize_heading'] == "Y"):?>
                                                        <li>
                                                            <span>cash back</span>
                                                            <strong> <?=$item['prize_reverse_prize_heading'];?></strong>
                                                        </li>
                                                    <?php endif; ?>


                                                     
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                 </div>
                                </div>
                            </div>
                        </div>
                    </div>
               </div>
            </div>
        </div>
    <?php endforeach; ?>
  </div>
<?php endif;?>


    <?php /* if($ourCampaigns):?>
        <div class="row">
            <div class="col-12">
                <?php foreach ($ourCampaigns as $key => $item): ?>
                   <div class="accordation-headings">
                        <div class="accordion-title">
                            <div class="row">
                                <div class="col-4">
                                    <div class="product_img">
                                        <a href="javascript:void(0);">
                                            <img src="<?=ImageExist($item['campaign_image']);?>" alt="<?=$item['campaign_image_alt'];?>">
                                        </a>
                                    </div>
                                </div>
                                <div class="col-8 home_right_side">
                                    <div class="home_list">
                                        <div class="aed_price">
                                            <img src="<?=base_url('assets/frontend_mobile')?>/mob-img/imgpsh_fullsize_anim.png" alt="bg_img">
                                            <p>AED 
                                                <?=$item['straight_add_on_amount']?>
                                            </p>
                                        </div>
                                        <div>
                                            <p class="grand_prize">Get your chance of</p>
                                            <div class="d_text">
                                                <p>
                                                    <?php if(!empty($item['lotto_type'])): ?> 
                                                        <?=number_format($item['prize_stright'.$item['lotto_type']])?>
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="playgame">
                                        <?php 
                                            $Pid = str_replace(' ','_' , $item['title']);
                                            $Pid = str_replace('/','_' , $Pid); 
                                        ?>
                                        <button  class="buy_now" id="buy_now" data-title="<?=$Pid;?>"  type="button">
                                            <img src="<?=ImageExist($item['app_image']);?>" alt="<?=$item['product_image_alt'];?>">
                                            <p>Buy Now</p>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                   </div>
                <?php endforeach;?>
            </div>
        </div>
    <?php endif; */ ?>
    <?php if(!empty($winner_gallery)):  ?>
        <!-- Recent Slider Start-->
        <div class="row ">
            <div class="col-12">
                <h1 class="main_heading">Our Recent Winner</h1>
            </div>
        

            <div class="col-md-12">
                <div class="winner_slider">
                    <div class="swiper winnerSwiper">
                        <div class="swiper-wrapper">
                            <?php foreach($winner_gallery as $winnersItem):?>
                                <div class="swiper-slide">
                                    <img src="<?=ImageExist($winnersItem['image']);?>" alt="productimg">
                                </div>
                            <?php endforeach;?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Recent Slider end-->
    <?php endif;?>

     <div class="buy_nowpopup">
        <div class="buy_nowpopup_campign">
            <h2 class="heading_popup">Buy Now</h2>
            <i class="bi bi-x cross_icons_headingpops"></i>
            <ul class="mx-0 px-0">
                <?php if($ourCampaigns):?>
                    <?php foreach ($ourCampaigns as $key => $item): ?>
                        <?php 
                            $Pid = str_replace(' ','_' , $item['title']);
                            $Pid = str_replace('/','_' , $Pid); 
                        ?>

                        <li class="productList <?=$Pid;?>"> 
                            <a href="<?=base_url('product/'.$item['title_slug'].'/'.Encript($item['products_id']));?>" class="d-flex ">
                                <h2><?=$item['title'];?></h2>
                                <p><?=$item['campaign_image_alt'];?></p>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>

</div>

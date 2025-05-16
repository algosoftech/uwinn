
        <?php   //banners.
         $tblName              = 'uw_homepage_slider';
         $whereCon['where']    = array('show_on'=> 'Web' , 'status' => 'A');     
         $shortField           = array('slider_id'=> -1);
         $slider               = $this->common_model->getData('multiple',$tblName,$whereCon,$shortField);
         if($slider):  ?>
        <section class="mega_banner_sec backpage" data-sizes="50vw">
            <div class="lazy slider container">
                <?php foreach ($slider as $key => $sliderItems): $index = $key+1; ?>
                        <div class="">
                            <div class="mega_banner">
                                <div class="jackpot_log">
                                    <img src="<?=base_url($sliderItems['image'])?>" alt="<?=$sliderItems['image'];?>"  />
                                </div>
                                <div class="mega_head">
                                   <?=ucwords($sliderItems['slider_description']);?>
                                </div>
                                <div class="mega_timer">
                                    <div class="timer">
                                        <div id="countdown">
                                            <div id='tiles_<?=$index;?>'  class="tiles"></div>
                                            <div class="labels">
                                            <li>Days</li>
                                            <li>Hours</li>
                                            <li>Mins</li>
                                            <li>Secs</li>
                                            </div>
                                        </div>
                                    </div>
                                    <span><a href="<?=base_url($sliderItems['slider_image_link']);?>">Play Mega Jackpot</a></span>
                                </div>
                            </div>
                        </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
<?php  $url =  $this->uri->segment(1);?>     
 <div class="mobile_footer">
    <div class="footer">
        <ul>
            <li>
                <a href="<?=base_url('/')?>"  class="<?= ($url == '') ? 'active' : ''; ?>"> Home 
                    <span>
                        <img src="<?=base_url('assets/frontend_mobile')?>/mob-img/home.svg" alt="mob_home">
                    </span>
                </a>
            </li>
            <li>
                <a href="<?=base_url('draw-results')?>" class="<?=($url == 'draw-results')?'active':'';?>" > Result 
                    <span>
                        <img src="<?=base_url('assets/frontend_mobile')?>/mob-img/result.svg" alt="mob_home">
                    </span>
                </a>
            </li>
            <li>
                <a href="<?=base_url('play')?>" class="<?=$url == 'play'?'active':'';?>"> Buy Now
                    <span>
                        <img src="<?=base_url('assets/frontend_mobile')?>/mob-img/play.svg" alt="mob_home">
                    </span>
                </a>
            </li>
            <li>
                <a href="<?=base_url('wallets')?>" class="<?=$url == 'wallets'?'active':'';?>"> Wallet
                    <span>
                        <img src="<?=base_url('assets/frontend_mobile')?>/mob-img/wallet.svg" alt="mob_home">
                    </span>
                </a>
            </li>
            <li>
                <a href="javascript:void(0);" id="moremenu"> Menu 
                    <span>
                        <img src="<?=base_url('assets/frontend_mobile')?>/mob-img/grid.svg" alt="mob_home">
                    </span>
                </a>
            </li>
        </ul>
    </div>
</div>
<!--More Menu List start-->
<div class="menu_list">
    <button class="back_btn">
        <a href="javascrript:void(0)">
            <i class="bi bi-chevron-double-left"></i> Back
        </a>
    </button>

    <ul>
         
        <?php if($this->session->userdata('users_id') == ""): ?>
            <li>
                <a href="<?=base_url('login');?>" class="<?=$url == 'login'?'active':'';?>">
                    <img src="<?=base_url('assets/frontend_mobile')?>/mob-img/icons/logout.png"> Login
                </a>
            </li>
        <?php else: ?>
            <li>
                <a href="<?=base_url('my-ticket')?>" class="<?=$url == 'my-ticket'?'active':'';?>">
                    <img src="<?=base_url('assets/frontend_mobile')?>/mob-img/icons/ticket.png"> My Raffle ID
                </a>
            </li>
            <li>
                <a href="<?=base_url('winner-gallery')?>" class="<?=$url == 'winner-gallery'?'active':'';?>">
                    <img src="<?=base_url('assets/frontend_mobile')?>/mob-img/icons/winner.png"> Winners
                </a>
            </li>
        <?php endif; ?>
            <li>
                <a href="<?=base_url('live')?>" class="<?=$url == 'live'?'active':'';?>">
                    <img src="<?=base_url('assets/frontend_mobile')?>/mob-img/icons/facebook.png">Live
                </a>
            </li>
            <li>
                <a href="<?=base_url('contact-us')?>" class="<?=$url == 'contact-us'?'active':'';?>">
                    <img src="<?=base_url('assets/frontend_mobile')?>/mob-img/icons/phone.png"> Contact Us
                </a>
            </li>
            <li>
                <a href="<?=base_url('about-us')?>" class="<?=$url == 'about-us'?'active':'';?>">
                    <img src="<?=base_url('assets/frontend_mobile')?>/mob-img/icons/info.png"> About Us
                </a>
            </li>
            <li>
                <a href="<?=base_url('raffle-rules')?>" class="<?=$url == 'raffle-rules'?'active':'';?>">
                    <img src="<?=base_url('assets/frontend_mobile')?>/mob-img/icons/game (1).png"> Raffle Rules
                </a>
            </li>
            <li>
                <a href="<?=base_url('how-to-play')?>" class="<?=$url == 'how-to-play'?'active':'';?>">
                    <img src="<?=base_url('assets/frontend_mobile')?>/mob-img/icons/game.png"> How To Participate
                </a>
            </li>

            <li>
                <a href="<?=base_url('terms&conditions')?>" class="<?=$url == 'terms&conditions'?'active':'';?>">
                    <img src="<?=base_url('assets/frontend_mobile')?>/mob-img/icons/terms.png">Terms and Condition
                </a>
            </li>
            <li>
                <a href="<?=base_url('refund-policy')?>" class="<?=$url == 'refund-policy'?'active':'';?>">
                    <img src="<?=base_url('assets/frontend_mobile')?>/mob-img/icons/refund.png">Refund Policy
                </a>
            </li>
            <li>
                <a href="<?=base_url('privacy-policy')?>" class="<?=$url == 'privacy-policy'?'active':'';?>">
                    <img src="<?=base_url('assets/frontend_mobile')?>/mob-img/icons/file.png"> Privacy Policy
                </a>
            </li>
            <li>
                <a href="<?=base_url('cancellation-policy')?>" class="<?=$url == 'cancellation-policy'?'active':'';?>">
                    <img src="<?=base_url('assets/frontend_mobile')?>/mob-img/icons/cancelation.png">Cancellation Policy
                </a>
            </li>
            <li>
                <a href="<?=base_url('faqs')?>" class="<?=$url == 'faqs'?'active':'';?>">
                    <img src="<?=base_url('assets/frontend_mobile')?>/mob-img/icons/faq.png"> Faqs
                </a>
            </li>
        <?php if($this->session->userdata('users_id') != ""): ?>
            <li>
                <a href="<?=base_url('logout');?>">
                    <img src="<?=base_url('assets/frontend_mobile')?>/mob-img/icons/logout.png"> Logout
                </a>
            </li>
        <?php endif;?>
    </ul>
</div>
<!--More menu List End-->
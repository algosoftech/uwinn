<header id="header" class="header fixed-top d-flex align-items-center">
    <div class="container d-flex align-items-center justify-content-between">
        <a href="<?=base_url('/')?>" class="logo d-flex align-items-center me-auto me-xl-0">
            <img src="<?=base_url('assets/frontend/img/logo.png');?>" alt="Logo">
        </a>
        <!-- Nav Menu -->
        <nav id="navmenu" class="navmenu">
            <ul>
                <li><a href="<?=base_url('/')?>"   class="<?php if($page=='index'):echo "active";endif;?>">Home </a></li>
                <!-- <li><a href="<?=base_url('/about-us')?>" class="<?php if($page=='about-us'):echo "active"; endif;?>">About Us</a></li> -->
               <?php if($this->session->userdata('users_id')): ?>
                <li class="dropdown has-dropdown">
                    <button><span>Games</span> <i class="bi bi-chevron-down"></i></button>
                    <ul class="dd-box-shadow">
                        <?php $ourCampaigns  = $this->geneal_model->getProductWithPrizeDetails(); ?> 
                        <?php if($ourCampaigns):?>
                            <?php foreach ($ourCampaigns as $key => $item) : ?>
                                <li>
                                    <a href="<?=base_url('product/'.$item['title_slug'].'/'.Encript($item['products_id']));?>">
                                        <?=ucwords($item['title']);?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        <?php endif;?>
                    </ul>
                </li>
                <li class="dropdown has-dropdown left-border">
                <button><span>Results</span> <i class="bi bi-chevron-down"></i>   </button>
                    <ul class="dd-box-shadow">
                        <li><a href="<?=base_url('draw-results');?>" class="<?php if($page=='draw-results'):echo "active";endif;?>">Draw Results</a></li>
                        <li><a href="<?=base_url('live');?>" class="<?php if($page=='live'):echo "active";endif;?>">Live</a></li>
                        <!-- <li><a href="<?=base_url('previous-results');?>" class="<?php if($page=='previous-results'):echo "active";endif;?>">Previous Results</a></li> -->
                        <li><a href="<?=base_url('winner-gallery');?>" class="<?php if($page=='winner-gallery'):echo "active";endif;?>">Winner Galleries</a></li>
                        <li><a href="<?=base_url('how-to-play');?>" class="<?php if($page=='how-to-play'):echo "active";endif;?>">How to play</a></li>

                    </ul>
                </li>
            <?php endif; ?>
                <!-- <li><a href="<?=base_url('/news')?>" class="<?php if($page=='news'):echo "active";endif;?>">News</a></li>
                <li><a href="<?=base_url('/contact-us')?>" class="<?php if($page=='contact-us'):echo "active";endif;?>">Contact Us</a> </li> -->

                <?php if($this->session->userdata('users_id')): ?>
                    <div class="dropdown">
                      <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" aria-haspopup="true" aria-expanded="false">
                        <div class="users">
                            <div>
                                <i class="fa-regular fa-user changecolor_Logo"></i>
                                <span class="name_Male"><?=$this->session->userdata('first_name');?></span>
                            </div> 
                        <div>
                        <i class="bi bi-chevron-down" id="drop_down_username"></i>
                        </div>   
                        </div>
                        
                     
                      </button>
                      <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                        <div class="small_Carddropdown2">
                            <a href="<?=base_url('my-profile')?>">
                                <p class="ticket_Section">
                                    <span class="profile_icons"> <i class="fa-regular fa-user sidebar_profileicons"></i> </span> 
                                    <span id="profile_1"> Profile</span>
                                </p>
                            </a>
                            <a href="<?=base_url('play')?>">
                                <p class="ticket_Section">
                                    <span class="profile_icons"><i class="bi bi-play"></i></span>
                                    <span id="profile_1">Play</span>
                                </p>
                            </a>
                            <a href="<?=base_url('draw-results')?>">
                                <p class="ticket_Section">
                                    <span class="profile_icons"><i class="bi bi-card-list"></i></span>
                                    <span id="profile_1"> Draws </span>
                                </p>
                            </a>
                            <a href="<?=base_url('wallets')?>">
                                <p class="ticket_Section">
                                    <span class="profile_icons"><i class="bi bi-wallet2"></i></span> 
                                    <span id="profile_1">Wallets </span>
                                </p>
                            </a>
                            <a href="<?=base_url('my-ticket')?>">
                                <p class="ticket_Section">
                                    <span class="profile_icons"><i class="bi bi-ticket-perforated"></i></span>
                                    <span id="profile_1">My Tickets</span>
                                </p>
                            </a>
                            <a href="<?=base_url('help')?>">
                              <p class="ticket_Section">
                                <span class="profile_icons"><i class="bi bi-question-circle"></i></span>
                                <span id="profile_1">Help </span>
                              </p>
                            </a>
                            <a href="<?=base_url('logout')?>">  
                              <p class="ticket_Section">
                                <span  class="profile_icons"><i class="bi bi-box-arrow-left"></i></span>
                                <span id="profile_1">  Logout </span>
                              </p>
                            </a>
                        </div>
                      </div>
                    </div>
                <?php else: ?>
                 
                    <li class="header_btn"><a href="<?=base_url('register');?>">REGISTER</a></li>
                    <li class="header_btn dark_btn"><a href="<?=base_url('login');?>">LOG IN</a></li>
                  
              
                <?php endif; ?>

            </ul>
            <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            
        </nav><!-- End Nav Menu -->
    </div>
</header><!-- End Header -->
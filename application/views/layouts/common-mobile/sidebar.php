<?php
    $activeUrl = $this->uri->segment(1); 
    // $this->session->set_userdata('active-sidebar',$activeUrl );
    // $activeSidebar = $this->session->userdata('active-sidebar');   

    if($activeUrl == 'my-profile'){ $myProfile = 'active'; } 
    if($activeUrl == 'play'){ $play = 'active'; } 
    if($activeUrl == 'draws'){ $draws = 'active'; } 
    if($activeUrl == 'wallets'){ $wallets = 'active'; } 
    if($activeUrl == 'my-ticket'){ $myticket = 'active'; } 
    if($activeUrl == 'help'){ $help = 'active'; } 
?>


 <div class="col-md-3 profile_Cardcenter">
     <div class="small_Card">
        <div class="menu-section <?=$myProfile;?>">
            <a href="<?=base_url('my-profile')?>">
                <span class="icon  "><i class="fa-regular fa-user"></i> </span> 
                <span> Profile</span> 
            </a>
        </div>
        <div class="menu-section <?=$play;?>">
            <a href="<?=base_url('play')?>">
                <span class="icon"><i class="bi bi-play"></i></span>
                <span >Play</span>
            </a>
        </div>
        <div class="menu-section <?=$draws;?>">
            <a href="<?=base_url('draw-results')?>">
                <span class="icon"><i class="bi bi-card-list"></i></span>
                <span >Draws</span>
            </a>
        </div>
        <div class="menu-section <?=$wallets;?>">
            <a href="<?=base_url('wallets')?>">
                <span class="icon"><i class="bi bi-wallet2"></i></span> 
                <span >Wallets </span>
            </a>
        </div>
        <div class="menu-section <?=$myticket;?>">
            <a href="<?=base_url('my-ticket')?>">
                <span class="icon"><i class="bi bi-ticket-perforated"></i></span>
                <span>My Tickets</span>
            </a>
        </div>
        <div class="menu-section <?=$help;?>">
            <a href="<?=base_url('help')?>">
                <span class="icon"><i class="bi bi-question-circle"></i></span>
                <span>Help </span>
            </a>
        </div>
        <div class="menu-section ">
            <a href="<?=base_url('logout')?>">  
                <span class="icon"><i class="bi bi-box-arrow-left"></i></span>
                <span>  Logout </span>
            </a>
        </div>
    </div>
</div>


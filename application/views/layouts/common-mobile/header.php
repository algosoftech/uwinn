<?php  $url =  $this->uri->segment(1);?>
<?php if($url != ""): ?>
    <div class="container">
        <div class="row">
            <div class="col-12 px-0">
                <div class="result_list">
                    <div class="inner_header">
                        <div class="d-flex myticktheader">
                            <a href="<?=base_url('/')?>"><i class="bi bi-chevron-left"></i>  <?=ucfirst($page);?></a>
                            <p class="gray_btn"> </p>
                            <!-- <i class="bi bi-calendar4"></i> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else : ?>
    <div class="mobile_header">
        <div class="container">
            <div class="row">
                <div class="col-6">
                    <a href="<?=base_url('/')?>" class="logo d-flex align-items-center me-auto me-xl-0">
                        <img src="<?=base_url('assets/frontend/img/logo.png');?>" alt="Logo" class="logo">
                    </a>
                </div>
                <?php if($this->session->userdata('first_name') !=''): ?>
                    <div class="col-6">
                        <div class="profile_img">
                            <?php if($this->session->userdata('users_id')): ?>
                                <a href="<?=base_url('my-profile')?>">
                                    <span class="name_Male"><?=substr($this->session->userdata('first_name'), 0, 1);?></span>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endif;?>
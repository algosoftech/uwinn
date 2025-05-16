<div class="main_wrapper">
    <div class="mob_wrapper">
        <section class="login_sec">
            <a href="<?=base_url('/')?>">
                <img src="<?=base_url('assets/frontend/img/logo.png')?>" alt="logo" class="login_img">
            </a>

            <div class="login_pages">
                <h1> Welcome back</h1>
                <div class="login_tab">
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link active mobile-section" data-bs-toggle="tab" href="#mobnumber">With Mobile Number</a>
                          </li>
                        <li class="nav-item">
                          <a class="nav-link email-section" data-bs-toggle="tab" href="#withemailaccount">With Email Account</a>
                        </li>
                      </ul>
                      <!-- Tab panes -->
                    <form method="post" action="#" autocomplete="off">
                        <?php if( $this->session->flashdata('alert_error') ): ?>
                            <div class="alert alert-danger w-100 m-auto text-danger text-center mb-3"><?=$this->session->flashdata('alert_error');?> </div>
                        <?php endif;?>
                        <div class="tab-content">
                            <div class="tab-pane active" id="mobnumber">
                                <div class="form-group row">
                                    <div class="col-12 p-0">
                                        <?php if(set_value('country_code')): $country_Code = set_value('country_code'); else: $country_Code = '+971'; endif; ?>
                                        <select name="country_code" id="country_code"  placeholder="Country Code*" class="form-control"/>
                                                <option value=''>Select</option>
                                            <?php if($country_code):?>
                                                <?php foreach ($country_code as $countryCode => $CountryName): ?>
                                                <option value="<?=$countryCode;?>" <?php if($country_Code == $countryCode): echo 'selected="selected"'; endif; ?>><?=$CountryName?></option>  
                                                <?php endforeach ?>
                                            <?php endif;?>
                                        </select>
                                        <?php if(form_error('country_code')): ?>
                                        <span for="country_code" generated="true" class="help-inline"><?php echo form_error('country_code');?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col p-0">
                                        <input type="tel" name="users_mobile" id="users_mobile" value="<?php echo set_value('users_mobile'); ?>" placeholder="Mobile no." class="form-control" autocomplete="off" />
                                        <label id="userid-error" class="error" for="userid">Enter Mobile Number</label>
                                        <?php if(form_error('users_mobile')): ?>
                                          <span for="users_mobile" generated="true" class="help-inline"><?php echo form_error('users_mobile'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="withemailaccount">
                                <div class="form-group row">
                                    <div class="col-12 p-0">
                                        <input type="email" name="users_email" id="users_email"  placeholder="Email" class="form-control" autocomplete="off" />
                                        <label id="userid-error" class="error" for="users_email">Enter Email Id</label>
                                        <?php if(form_error('users_email')): ?>
                                            <span for="users_email" generated="true" class="help-inline"><?php echo form_error('users_email');?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col p-0">
                                <input type="password" name="password" id="password" value="<?php echo set_value('password'); ?>" placeholder="Password" class="form-control"  />
                                <label id="userid-error" class="error" for="password">Enter Password</label>
                                <i class="bi bi-eye-fill password-eye-icon"></i>
                                <?php if(form_error('password')): ?>
                                    <span for="password" generated="true" class="help-inline"><?php echo form_error('password'); ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12 p-0 text-end">
                                <a href="<?=base_url('forgot-password');?>" class="forget_password">Forgot Password ? Click here</a>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12 p-0">
                                <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                                <input type="submit" class="login_button" id="login_button2" value="Sign In">
                            </div>
                        </div>
                    </form>
                </div>

                <div class="signin_botomstripe">
                    Don't have an account? <a href="<?=base_url('register');?>"> Sign Up</a>
                </div>
            </div>
           
        </section>
    </div>
</div>
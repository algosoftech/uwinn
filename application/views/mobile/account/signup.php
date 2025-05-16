<div class="main_wrapper">
            <div class="mob_wrapper">
                <section class="login_sec">
                    <a href="<?=base_url('/');?>">
                        <img src="<?=base_url('assets/frontend/img/logo.png');?>" alt="logo" class="login_img">
                    </a>
                    <div class="signup_pages">
                        <h1> Create Account</h1>
                        <div class="login_tab">
                            <form method="post" action="#" autocomplete="off">
                                <div class="form-group row">
                                    <div class="col p-0">
                                        <input type="text" name="first_name" id="first_name" value="<?php echo set_value('first_name'); ?>" placeholder="First Name" class="form-control" autocomplete="off" required />
                                        <?php if(form_error('first_name')): ?>
                                            <div for="first_name" generated="true" class="help-inline"><?php echo form_error('first_name'); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col p-0">
                                        <input type="text" name="last_name" id="last_name" value="<?php echo set_value('last_name'); ?>" class="form-control" placeholder="Last Name" autocomplete="off" required />
                                        <?php if(form_error('last')): ?>
                                            <div for="last_name" generated="true" class="help-inline"><?php echo form_error('last_name'); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                              
                                <div class="form-group row">
                                    <div class="col-12 ps-0">
                                        <select name="country_code" id="country_code"  placeholder="Country Code*" class="form-control" required />
                                            <option value=''>Select Country Code</option>
                                            <?php if($country_code):?>
                                                <?php foreach ($country_code as $countryCode => $CountryName): ?>
                                                  <option value="<?=$countryCode;?>" <?= ($countryCode == set_value('country_code'))? 'selected':''; ?> ><?=$CountryName?></option>  
                                                <?php endforeach ?>
                                            <?php endif;?>
                                        </select>
                                        <?php if(form_error('country_code')): ?>
                                            <div for="country_code" generated="true" class="help-inline">
                                                <?php echo form_error('country_code');?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col p-0">
                                        <input type="number" name="mobile" id="mobile" value="<?php echo set_value('mobile'); ?>" class="form-control" placeholder="Mobile Number" autocomplete="off" required />
                                        <?php if(form_error('mobile')): ?>
                                            <div for="mobile" generated="true" class="help-inline"><?php echo form_error('mobile'); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <div class="col p-0">
                                        <input type="email" name="email" id="email" class="form-control" placeholder="Enter Email id" value="<?php echo set_value('email'); ?>" autocomplete="off">
                                        <label id="userid-error" class="error" for="email">Enter Email id</label>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col p-0">
                                        <input type="password" name="password" id="password" value="<?php echo set_value('password'); ?>" placeholder="password*" class="form-control" autocomplete="off" required />
                                        <i class="bi bi-eye-fill password-eye-icon"></i>
                                        <?php if(form_error('password')): ?>
                                            <div for="password" generated="true" class="help-inline"><?php echo form_error('password'); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col p-0">
                                        <input type="password" name="confirm_password" id="confirm_password" value="<?php echo set_value('confirm_password'); ?>" placeholder="Confirm password*" class="form-control"  required />
                                        <i class="bi bi-eye-fill password-eye-icon"></i>
                                        <?php if(form_error('confirm_password')): ?>
                                            <div for="confirm_password" generated="true" class="help-inline"><?php echo form_error('confirm_password'); ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-12 p-0 text-start">
                                     <div class="check-condition">
                                         <div class="round">
                                            <input type="checkbox" checked id="terms" name="terms" required />
                                            <label for="terms"></label>
                                          </div>
                                        <label for="terms" class="terms"> I agree to 
                                            <a href="<?=base_url('terms&conditions')?>" class="term">Usage Terms</a> and 
                                            <a href="<?=base_url('privacy-policy')?>" class="term">Privacy Policy</a> 
                                        </label>
                                     </div>
                                      
                                    </div>
                                </div>


                                <div class="form-group row">
                                    <div class="col-12 p-0">
                                        <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                                        <input type="submit" class="login_button" id="login_button2" value="Register">
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="signin_botomstripe">
                            Already have an account ?<a href="<?=base_url('login')?>"> Sign In</a>
                        </div>
                    </div>
                   
                </section>
                
            </div>
        </div>
<div class="main_wrapper">
            <div class="mob_wrapper">
                <section class="login_sec">
                    <img src="<?=base_url('assets/frontend/img/logo.png');?>" alt="logo" class="login_img">
                    <div class="signup_pages">
                        <h1> Verify OTP</h1>
                        <div class="login_tab">
                            <form method="post" action="#" autocomplete="off">
                                <div class="form-group row">
                                    <div class="col p-0">
                                        <input type="text" name="verify_otp" id="verify_otp" value="<?php echo set_value('verify_otp'); ?>" placeholder="Verify OTP*" class="form-control" required/>
                                        <?php if(form_error('verify_otp')): ?>
                                            <span for="verify_otp" generated="true" class="help-inline"><?php echo form_error('verify_otp'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col p-0">
                                        <input type="password" name="password" id="password" value="<?php echo set_value('password'); ?>" placeholder="New Password" class="form-control" required/>
                                        <?php if(form_error('password')): ?>
                                            <span for="password" generated="true" class="help-inline"><?php echo form_error('password'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col p-0">
                                        <input type="password" name="confirm_password" id="confirm_password" value="<?php echo set_value('confirm_password'); ?>" placeholder="Confirm New  Password" class="form-control" required/>
                                        <?php if(form_error('confirm_password')): ?>
                                            <span for="confirm_password" generated="true" class="help-inline"><?php echo form_error('confirm_password'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <div class="col-12 p-0">
                                        <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                                        <input type="submit" class="login_button" id="login_button2" value="Submit">
                                    </div>
                                </div>
                            </form>
                        </div>

                        
                    </div>
                   
                </section>
                
            </div>
        </div>
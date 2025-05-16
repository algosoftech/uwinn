<div class="main_wrapper">
            <div class="mob_wrapper">
                <section class="login_sec">
                    <a href="<?=base_url('/')?>">
                        <img src="<?=base_url('assets/frontend/img/logo.png');?>" alt="logo" class="login_img">
                    </a>
                    <div class="signup_pages">
                        <h1> Reset Password</h1>
                        <div class="login_tab">
                            <form action="<?=base_url('profile/reset-password');?>" method="POST" enctype="multipart/form-data" class="contact-form">
                                <div class="form-group row">
                                    <div class="col p-0">
                                        <input type="text" name="otp" id="otp" value="<?php echo set_value('otp'); ?>" placeholder="Verify OTP" class="form-control" required/>
                                        <?php if(form_error('otp')): ?>
                                            <span for="otp" generated="true" class="help-inline"><?php echo form_error('otp'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col p-0">
                                        <input type="password" class="form-control" name="new_password" id="new_password" placeholder="New Password" required>
                                        <?php if(form_error('new_password')): ?>
                                         <span for="new_password" generated="true" class="help-inline"><?php echo form_error('new_password'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col p-0">
                                        <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm Password">
                                        <?php if(form_error('confirm_password')): ?>
                                         <span for="confirm_password" generated="true" class="help-inline"><?php echo form_error('confirm_password'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="form-group row">
                                    <div class="col-12 p-0">
                                        <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                                        <input type="submit" class="login_button" id="login_button2" value="Update Password">
                                    </div>
                                </div>
                            </form>
                        </div>

                        
                    </div>
                   
                </section>
                
            </div>
        </div>
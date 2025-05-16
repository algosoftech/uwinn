<div class="wrapper">
    <section class="login_banner mb-5">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <div class="contact_form form">
                        <h4>Change Password</h4>
                        <form method="post" action="#" autocomplete="off">
                            <div class="row">
                                <div class="col-sm-12 col-md-12 col-lg-12">
                                    <div class="form-group">
                                        <input type="text" name="verify_otp" id="verify_otp" value="<?php echo set_value('verify_otp'); ?>" placeholder="Verify OTP*" class="form-control" required/>
                                    </div>
                                    <?php if(form_error('verify_otp')): ?>
                                    <span for="verify_otp" generated="true" class="help-inline"><?php echo form_error('verify_otp'); ?></span>
                                    <?php endif; ?>
                                </div>

                                <div class="col-sm-12 col-md-12 col-lg-12">
                                    <div class="form-group">
                                        <input type="password" name="password" id="password" value="<?php echo set_value('password'); ?>" placeholder="New Password*" class="form-control" required/>
                                    </div>
                                    <?php if(form_error('password')): ?>
                                    <span for="password" generated="true" class="help-inline"><?php echo form_error('password'); ?></span>
                                    <?php endif; ?>
                                </div>

                                 <div class="col-sm-12 col-md-12 col-lg-12">
                                    <div class="form-group">
                                        <input type="password" name="confirm_password" id="confirm_password" value="<?php echo set_value('confirm_password'); ?>" placeholder="Confirm New  Password*" class="form-control" required/>
                                    </div>
                                    <?php if(form_error('confirm_password')): ?>
                                    <span for="confirm_password" generated="true" class="help-inline"><?php echo form_error('confirm_password'); ?></span>
                                    <?php endif; ?>
                                </div>

                                <div class="col-sm-12 col-md-12 col-lg-12">
                                    <div class="form-group text-center">
                                        <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                                        <input type="submit" value="submit" class="btn btn_pink" />
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
           
          
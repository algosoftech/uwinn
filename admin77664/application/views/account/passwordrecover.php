<div class="auth-wrapper">
    <div class="auth-content">
        <div class="card">
            <div class="row align-items-center text-center">
                <div class="col-md-12">
                    <div class="card-body">
                        <form name="passwordRecoverForm" id="passwordRecoverForm" class="form-vertical" action="" method="post">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                            <img src="{ASSET_INCLUDE_URL}image/logo.png" alt="" class="img-fluid mb-4" style="width: 100px;">
                            <h4 class="mb-3 f-w-400">Set Password</h4>
                            <?php if($recovererror): ?>
                                <div class="form_heading alert alert-danger">
                                    <?=$recovererror?>
                                </div>
                            <?php elseif($recoversuccess): ?>
                                <div class="form_heading alert alert-success">
                                    <?=$recoversuccess?>
                                </div>
                            <?php elseif($this->session->flashdata('alert_success')): ?>
                                <div class="form_heading alert alert-success">
                                    <?=$this->session->flashdata('alert_success')?>
                                </div>
                            <?php endif; ?>
                            <div class="form-group mb-3">
                                <label class="floating-label" for="userOtp">OTP</label>
                                <input type="text" name="userOtp" id="userOtp" class="form-control required" value="<?php if(set_value('userOtp') && !$recoversuccess): echo set_value('userOtp'); endif; ?>" placeholder="OTP" autocomplete="off"/>
                                <?php if(form_error('userOtp')): ?>
                                    <label for="userOtp" generated="true" class="error"><?php echo form_error('userOtp'); ?></label>
                                <?php endif; ?>
                            </div>
                            <div class="form-group mb-3">
                                <label class="floating-label" for="userPassword">New Password</label>
                                <input type="password" name="userPassword" id="userPassword" class="form-control required" value="<?php if(set_value('userPassword') && !$recoversuccess): echo set_value('userPassword'); endif; ?>" placeholder="Password" autocomplete="off"/>
                                <?php if(form_error('userPassword')): ?>
                                    <label for="userPassword" generated="true" class="error"><?php echo form_error('userPassword'); ?></label>
                                <?php endif; ?>
                            </div>
                            <div class="form-group mb-3">
                                <label class="floating-label" for="userConfPassword">Confirm Password</label>
                                <input type="password" name="userConfPassword" id="userConfPassword" class="form-control required" value="<?php if(set_value('userConfPassword') && !$recoversuccess): echo set_value('userConfPassword'); endif; ?>" placeholder="Confirm Password" autocomplete="off"/>
                                <?php if(form_error('userConfPassword')): ?>
                                    <label for="userConfPassword" generated="true" class="error"><?php echo form_error('userConfPassword'); ?></label>
                                <?php endif; ?>
                            </div>
                            <input type="hidden" name="passwordRecoverFormSubmit" id="passwordRecoverFormSubmit" value="Yes">
                            <button class="btn btn-block btn-primary mb-4">Recover</button>
                            <p class="mb-2 text-muted"><a href="{FULL_SITE_URL}login" class="f-w-400 pull-left">Back to login</a>
                            <a href="{FULL_SITE_URL}resend-otp" class="f-w-400 pull-right">Resend OTP</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
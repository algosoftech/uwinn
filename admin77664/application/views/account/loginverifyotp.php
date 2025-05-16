<style>
body{
	background:#ecf5ff !important;
}
</style>
<div class="auth-wrapper admin-login-block">
    <div class="auth-content">
        <div class="card">
            <div class="row">
                <div class="col-md-12">
                    <div class="card-body">
                        <form name="otpVerificationForm" id="otpVerificationForm" class="form-vertical" action="" method="post">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                            <img src="{ASSET_INCLUDE_URL}image/logo.png" alt="" class="img-fluid mb-4 d-block mx-auto">
                            <h4 class="mb-3 f-w-400 text-center">OTP Verification</h4>
                            <?php if($recovererror): ?>
                                <div class="form_heading alert alert-danger">
                                    <?=$recovererror?>
                                </div>
                            <?php elseif($this->session->flashdata('alert_success')): ?>
                                <div class="form_heading alert alert-success">
                                    <?=$this->session->flashdata('alert_success')?>
                                </div>
                            <?php endif; ?>
                            <div class="form-group mb-3">
                                <label class="floating-label" for="userOtp">PIN</label>
                                <input type="password" name="userOtp" id="userOtp" class="form-control required" value="<?php if(set_value('userOtp')): echo set_value('userOtp'); endif; ?>" placeholder="PIN" autocomplete="off"/>
                                <?php if(form_error('userOtp')): ?>
                                    <label for="userOtp" generated="true" class="error"><?php echo form_error('userOtp'); ?></label>
                                <?php endif; ?>
                            </div>
                            <input type="hidden" name="otpVerificationFormSubmit" id="otpVerificationFormSubmit" value="Yes">
                            <button class="btn btn-block btn-primary mb-4">Submit</button>
                            <p class="mb-2 text-muted"><a href="{FULL_SITE_URL}login" class="f-w-400 pull-left">Back to login</a>
                            <a href="{FULL_SITE_URL}forgot-pin" class="f-w-400 pull-right">Resend OTP</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
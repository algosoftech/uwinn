<style>
body{
	background:#ecf5ff !important;
}
</style>
<div class="auth-wrapper admin-login-block">
    <div class="auth-content">
        <div class="card">
            <div class="row ">
                <div class="col-md-12">
                    <div class="card-body">
                        <form name="recoverform" id="recoverform" class="form-vertical" action="" method="post">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                            <img src="{ASSET_INCLUDE_URL}image/logo.png" alt="" class="img-fluid mb-4 d-block mx-auto" style="width: 100px;">
                            <h6 class="mb-3 f-w-400" style="color:#fff;">Enter Your Mobile Number Below And We Will Send You OTP To Recover a Password.</h6>
                            <?php if($forgoterror): ?>
                                <div class="form_heading alert alert-danger">
                                    <?=$forgoterror?>
                                </div>
                            <?php elseif($forgotsuccess): ?>
                                <div class="form_heading alert alert-success">
                                    <?=$forgotsuccess?>
                                </div>
                            <?php endif; ?>
                            <div class="form-group mb-3">
                                <label class="floating-label" for="forgotMobile">Mobile</label>
                                <input type="text" name="forgotMobile" id="forgotMobile" class="form-control required" value="<?php if(set_value('forgotMobile') && $forgotsuccess ==''): echo set_value('forgotMobile'); endif; ?>" placeholder="Mobile" autocomplete="off"/>
                                <?php if(form_error('forgotMobile')): ?>
                                    <label for="forgotMobile" generated="true" class="error"><?php echo form_error('forgotMobile'); ?></label>
                                <?php endif; ?>
                            </div>
                            <input type="hidden" name="recoverformSubmit" id="recoverformSubmit" value="Yes">
                            <button class="btn btn-block btn-primary mb-4">Send</button>
                            <p class="mb-2 text-muted"><a href="{FULL_SITE_URL}login" class="f-w-400 pull-left">Back to login</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
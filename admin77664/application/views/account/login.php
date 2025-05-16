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
                        <form name="loginform" id="loginform" class="form-vertical" action="" method="post">
                            <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                            <img src="{ASSET_INCLUDE_URL}image/logo.png" alt="" class="img-fluid mb-4 d-block mx-auto">
                            <h4 class="mb-3 f-w-400">Sign in</h4>
                            <?php if($error): ?>
                                <div class="form_heading alert alert-danger">
                                    <?=$error?>
                                </div>
                            <?php endif; ?>
                            <div class="form-group mb-3">
                                <label class="floating-label text-left" for="userEmail">Email address</label>
                                <input type="text" name="userEmail" id="userEmail" class="form-control required email" value="<?php if(set_value('userEmail')): echo set_value('userEmail'); endif; ?>" placeholder="Email" autocomplete="off"/>
                                <?php if(form_error('userEmail')): ?>
                                    <label for="userEmail" generated="true" class="error"><?php echo form_error('userEmail'); ?></label>
                                <?php endif; ?>
                            </div>
                            <div class="form-group mb-4">
                                <label class="floating-label text-left" for="userPassword">Password</label>
                                <input type="password" name="userPassword" id="userPassword" class="form-control required" value="<?php if(set_value('userPassword')): echo set_value('userPassword'); endif; ?>" placeholder="Password" autocomplete="off"/>
                                <?php if(form_error('userPassword')): ?>
                                    <label for="userPassword" generated="true" class="error"><?php echo form_error('userPassword'); ?></label>
                                <?php endif; ?>
                            </div>
                            <input type="hidden" name="loginFormSubmit" id="loginFormSubmit" value="Yes">
                            <button class="btn btn-block btn-primary mb-4">Signin</button>
                            <p class="mb-2 text-muted"><a href="{FULL_SITE_URL}forgot-password" class="f-w-400 pull-right">Forgot password?</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
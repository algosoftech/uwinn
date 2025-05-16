<style type="text/css">
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type="number"] {
        -moz-appearance: textfield;
    }
</style>
<div class="wrapper">
    <section class="login_banner mb-5">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <div class="contact_form form">
                        <h4>Registration</h4>
                        <form method="post" action="#" autocomplete="off">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <input type="text" name="first_name" id="first_name" value="<?php echo set_value('first_name'); ?>" placeholder="First Name*" class="form-control" required/>
                                    </div>
                                    <?php if(form_error('first_name')): ?>
                                    <span for="first_name" generated="true" class="help-inline"><?php echo form_error('first_name'); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <input type="text" name="last_name" id="last_name" value="<?php echo set_value('last_name'); ?>" placeholder="Last Name*" class="form-control" required/>
                                    </div>
                                    <?php if(form_error('last')): ?>
                                    <span for="last_name" generated="true" class="help-inline"><?php echo form_error('last_name'); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <input type="email" name="email" id="email" value="<?php echo set_value('email'); ?>" placeholder="Email*" class="form-control"  required  />
                                    </div>
                                    <?php if(form_error('email')): ?>
                                    <span for="email" generated="true" class="help-inline"><?php echo form_error('email'); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <?php if(set_value('country_code')): $country_Code = set_value('country_code'); else: $country_Code = '+971'; endif; ?>
                                        <select name="country_code" id="country_code"  placeholder="Country Code*" class="form-control" required />
                                            <option value=''>Select</option>
                                            <?php if($country_code):?>
                                                <?php foreach ($country_code as $countryCode => $CountryName): ?>
                                                  <option value="<?=$countryCode;?>" <?php if($country_Code == $countryCode): echo 'selected="selected"'; endif; ?>><?=$CountryName?></option>  
                                                <?php endforeach ?>
                                            <?php endif;?>
                                        </select>
                                    </div>
                                    <?php if(form_error('country_code')): ?>
                                    <span for="country_code" generated="true" class="help-inline"><?php echo form_error('country_code');?></span>
                                    <?php endif; ?>
                                </div>

                                <div class="col-6">
                                    <div class="form-group">
                                        <input type="number" name="mobile" id="mobile" value="<?php echo set_value('mobile'); ?>" placeholder="Phone Number*" class="form-control" required />
                                    </div>
                                    <?php if(form_error('mobile')): ?>
                                    <span for="mobile" generated="true" class="help-inline"><?php echo form_error('mobile'); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <input type="password" name="password" id="password" value="<?php echo set_value('password'); ?>" placeholder="password*" class="form-control"  required />
                                    </div>
                                    <?php if(form_error('password')): ?>
                                    <span for="password" generated="true" class="help-inline"><?php echo form_error('password'); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <input type="password" name="confirm_password" id="confirm_password" value="<?php echo set_value('confirm_password'); ?>" placeholder="Confirm password*" class="form-control"  required />
                                    </div>
                                    <?php if(form_error('confirm_password')): ?>
                                    <span for="confirm_password" generated="true" class="help-inline"><?php echo form_error('confirm_password'); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group text-center">
                                            <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                                            <input type="submit" value="submit" class="btn btn_pink" />
                                        </div>
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
           
          
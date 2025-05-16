<div class="forgetpassword">
    <section class="login_sec">
        <img src="<?=base_url('assets/frontend/img/logo.png');?>" alt="logo" class="login_img">
        <div class="inner_sectiobforget">
            <h1> BUY &  <span>WIN </span></h1>
            <h2>We make it affordble</h2>
            <p>Recover Your Password Thorugh</p>
            <div class="foregt_tab">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#mobnumber"> Mobile Number</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link " data-bs-toggle="tab" href="#withemailaccount">Email
                            Id</a>
                    </li>
                </ul>
                <!-- Tab panes -->
                <form method="post" action="#" autocomplete="off">
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
                                    <input type="tel" name="users_mobile" id="users_mobile" value="<?php echo set_value('users_mobile'); ?>" class="form-control"   placeholder="Enter Mobile Number" autocomplete="off" />
                                    <label id="userid-error" class="error" for="userid">Enter Mobile Number</label>
                                    <?php if(form_error('users_mobile')): ?>
                                        <span for="users_mobile" generated="true" class="help-inline"><?php echo form_error('users_mobile');?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="withemailaccount">
                            <div class="form-group row">
                                <div class="col-12 p-0">
                                    <input type="email" name="users_email" id="users_email"  placeholder="Email Id" class="form-control" autocomplete="off" />
                                    <label id="users_email" class="error" for="users_email">Enter Email Id</label>
                                    <?php if(form_error('users_email')): ?>
                                        <span for="users_email" generated="true" class="help-inline"><?php echo form_error('users_email');?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12 p-0">
                                <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                                <input type="submit" class="forgt_btn" id="login_button2" value="Submit">
                            </div>
                        </div>
                    </div>
                </form>
               
            </div>                 
        </div>
    </section>
</div>
<div class="forget_botomstripe">
   <a href="<?=base_url('/login')?>">Login</a>
</div>
     
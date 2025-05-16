<div class="wrapper">
    <section class="login_banner mb-5">
        <div class="container">


            <div class="row">
                <div class="col-sm-12 col-md-12 col-lg-12">
                    <div class="contact_form form">
                        <?php if( $this->session->flashdata('alert_error') ): ?>
                            <div class="alert alert-danger w-100 m-auto text-danger text-center mb-3"><?=$this->session->flashdata('alert_error');?> </div>
                        <?php endif;?>
                        <h4>Forgot Password</h4>

                        <ul id="tabs">
                            <li class="btn tab"> <a  href="#mobile-section" class="mobile-section active">With Mobile Number</a></li>
                            <li class="btn tab"> <a href="#email-section"  class="email-section">With User Email</a></li>
                        </ul>
                        <form method="post" action="#" autocomplete="off">
                            <div class="tabs_content" id="mobile-section">
                                <div class="row">
                                    
                                    <div class="col-sm-6 col-md-6 col-lg-6">
                                        <div class="form-group">
                                            <?php if(set_value('country_code')): $country_Code = set_value('country_code'); else: $country_Code = '+971'; endif; ?>
                                            <select name="country_code" id="country_code"  placeholder="Country Code*" class="form-control"/>
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
                                    <div class="col-sm-6 col-md-6 col-lg-6">
                                        <div class="form-group">
                                          <input type="tel" name="users_mobile" id="users_mobile" value="<?php echo set_value('users_mobile'); ?>" placeholder="Mobile no." class="form-control"  />
                                        </div>  
                                        <?php if(form_error('users_mobile')): ?>
                                          <span for="users_mobile" generated="true" class="help-inline"><?php echo form_error('users_mobile'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="tabs_content" id="email-section" style="display:none">
                                <div class="row">
                                 <div class="col-sm-12 col-md-12 col-lg-12">
                                    <div class="form-group">
                                        <input type="text" name="users_email" id="users_email"  placeholder="Email" class="form-control" />
                                    </div>
                                 </div>
                                </div>
                            </div>

                            <div class="row">
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
           

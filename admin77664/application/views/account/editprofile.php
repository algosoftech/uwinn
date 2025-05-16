<div class="pcoded-main-container">
    <div class="pcoded-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <?php /* ?><h5 class="m-b-10">Welcome <?=sessionData('HCAP_ADMIN_FIRST_NAME')?></h5><?php */ ?>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{FULL_SITE_URL}profile">Profile Details</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Edit Profile Details</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- [ breadcrumb ] end -->
        <!-- [ Main Content ] start -->
        <div class="row">
          <div class="col-sm-12">
            <div class="card">
              <div class="card-header">
                <h5>Edit Profile Details</h5>
                <a href="{FULL_SITE_URL}profile" class="btn btn-sm btn-primary pull-right">Back</a>
              </div>
              <div class="card-body">
                <div class="basic-login-inner">
                  <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="">
                    <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="admin_id"/>
                    <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$profileuserdata['admin_id']?>"/>
                    <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$profileuserdata['admin_id']?>"/>
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                    <div class="row">
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('admin_title')): ?>error<?php endif; ?>">
                        <label>Title<span class="required">*</span></label>
                        <input type="text" name="admin_title" id="admin_title" value="<?php if(set_value('admin_title')): echo set_value('admin_title'); else: echo stripslashes($profileuserdata['admin_title']);endif; ?>" class="form-control required" placeholder="Title">
                        <?php if(form_error('admin_title')): ?>
                          <span for="admin_title" generated="true" class="help-inline"><?php echo form_error('admin_title'); ?></span>
                        <?php endif; ?>
                      </div>
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('admin_first_name')): ?>error<?php endif; ?>">
                        <label>First Name<span class="required">*</span></label>
                        <input type="text" name="admin_first_name" id="admin_first_name" value="<?php if(set_value('admin_first_name')): echo set_value('admin_first_name'); else: echo stripslashes($profileuserdata['admin_first_name']);endif; ?>" class="form-control required" placeholder="First Name">
                        <?php if(form_error('admin_first_name')): ?>
                          <span for="admin_first_name" generated="true" class="help-inline"><?php echo form_error('admin_first_name'); ?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="row">
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('admin_middle_name')): ?>error<?php endif; ?>">
                        <label>Middle Name</label>
                        <input type="text" name="admin_middle_name" id="admin_middle_name" value="<?php if(set_value('admin_middle_name')): echo set_value('admin_middle_name'); else: echo stripslashes($profileuserdata['admin_middle_name']);endif; ?>" class="form-control" placeholder="Middle Name">
                        <?php if(form_error('admin_middle_name')): ?>
                          <span for="admin_middle_name" generated="true" class="help-inline"><?php echo form_error('admin_middle_name'); ?></span>
                        <?php endif; ?>
                      </div>
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('admin_last_name')): ?>error<?php endif; ?>">
                        <label>Last Name<span class="required">*</span></label>
                        <input type="text" name="admin_last_name" id="admin_last_name" value="<?php if(set_value('admin_last_name')): echo set_value('admin_last_name'); else: echo stripslashes($profileuserdata['admin_last_name']);endif; ?>" class="form-control required" placeholder="Last Name">
                        <?php if(form_error('admin_last_name')): ?>
                          <span for="admin_last_name" generated="true" class="help-inline"><?php echo form_error('admin_last_name'); ?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="row">
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('admin_email')): ?>error<?php endif; ?>">
                        <label>Email<span class="required">*</span></label>
                        <input type="text" name="admin_email" id="admin_email" value="<?php if(set_value('admin_email')): echo set_value('admin_email'); else: echo stripslashes($profileuserdata['admin_email']);endif; ?>" class="form-control required email" placeholder="Email">
                        <?php if(form_error('admin_email')): ?>
                          <span for="admin_email" generated="true" class="help-inline"><?php echo form_error('admin_email'); ?></span>
                        <?php endif; ?>
                      </div>
                       <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('admin_country_code')): ?>error<?php endif; ?>">
                        <label>Country Code<span class="required">*</span></label>
                        <input type="text" name="admin_country_code" id="admin_country_code" value="<?php if(set_value('admin_country_code')): echo set_value('admin_country_code'); else: echo stripslashes($profileuserdata['admin_country_code']);endif; ?>" class="form-control required" placeholder="Country Code">
                        <?php if(form_error('admin_country_code')): ?>
                          <span for="admin_country_code" generated="true" class="help-inline"><?php echo form_error('admin_country_code'); ?></span>
                        <?php endif; ?>
                      </div>
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('admin_phone')): ?>error<?php endif; ?>">
                        <label>Phone<span class="required">*</span></label>
                        <input type="text" name="admin_phone" id="admin_phone" value="<?php if(set_value('admin_phone')): echo set_value('admin_phone'); else: echo stripslashes($profileuserdata['admin_phone']);endif; ?>" class="form-control required" placeholder="Phone">
                        <?php if(form_error('admin_phone')): ?>
                          <span for="admin_phone" generated="true" class="help-inline"><?php echo form_error('admin_phone'); ?></span>
                        <?php endif; if($mobileerror):  ?>
                          <span for="admin_phone" generated="true" class="help-inline"><?php echo $mobileerror; ?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="row">
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('admin_address')): ?>error<?php endif; ?>">
                        <label>Address</label>
                        <input type="text" name="admin_address" id="admin_address" value="<?php if(set_value('admin_address')): echo set_value('admin_address'); else: echo stripslashes($profileuserdata['admin_address']);endif; ?>" class="form-control" placeholder="Address">
                        <?php if(form_error('admin_address')): ?>
                          <span for="admin_address" generated="true" class="help-inline"><?php echo form_error('admin_address'); ?></span>
                        <?php endif; ?>
                      </div>
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('admin_city')): ?>error<?php endif; ?>">
                        <label>City</label>
                        <input type="text" name="admin_city" id="admin_city" value="<?php if(set_value('admin_city')): echo set_value('admin_city'); else: echo stripslashes($profileuserdata['admin_city']);endif; ?>" class="form-control" placeholder="City">
                        <?php if(form_error('admin_city')): ?>
                          <span for="admin_city" generated="true" class="help-inline"><?php echo form_error('admin_city'); ?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="row">
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('admin_state')): ?>error<?php endif; ?>">
                        <label>State</label>
                        <input type="text" name="admin_state" id="admin_state" value="<?php if(set_value('admin_state')): echo set_value('admin_state'); else: echo stripslashes($profileuserdata['admin_state']);endif; ?>" class="form-control" placeholder="State">
                        <?php if(form_error('admin_state')): ?>
                          <span for="admin_state" generated="true" class="help-inline"><?php echo form_error('admin_state'); ?></span>
                        <?php endif; ?>
                      </div>
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('admin_pincode')): ?>error<?php endif; ?>">
                        <label>Pincode</label>
                        <input type="text" name="admin_pincode" id="admin_pincode" value="<?php if(set_value('admin_pincode')): echo set_value('admin_pincode'); else: echo stripslashes($profileuserdata['admin_pincode']);endif; ?>" class="form-control number" placeholder="Pincode">
                        <?php if(form_error('admin_pincode')): ?>
                          <span for="admin_pincode" generated="true" class="help-inline"><?php echo form_error('admin_pincode'); ?></span>
                        <?php endif; ?>
                      </div>
                        <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('admin_pincode')): ?>error<?php endif; ?>">
                        <label>Login Pin</label>
                        <input type="text" name="admin_pin" id="admin_pin" value="<?php if(set_value('admin_pin')): echo set_value('admin_pin'); else: echo stripslashes($profileuserdata['admin_pin']);endif; ?>" class="form-control number" placeholder="Login Pin">
                        <?php if(form_error('admin_pin')): ?>
                          <span for="admin_pin" generated="true" class="help-inline"><?php echo form_error('admin_pin'); ?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="row">
                      <div class="login-btn-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="inline-remember-me mt-4">
                          <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                          <button class="btn btn-primary mb-4">Submit</button>
                          <a href="{FULL_SITE_URL}profile" class="btn btn-danger has-ripple mb-4">Cancel</a>
                          <span class="tools pull-right">Note:- <strong><span style="color:#FF0000;">*</span> Indicates Required Fields</strong> </span> 
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- [ Main Content ] end -->
    </div>
</div>
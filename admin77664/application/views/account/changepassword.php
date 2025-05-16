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
                            <li class="breadcrumb-item"><a href="javascript:void(0);">Change Password</a></li>
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
                <h5>Change Password</h5>
                <a href="{FULL_SITE_URL}profile" class="btn btn-sm btn-primary pull-right">Back</a>
              </div>
              <div class="card-body">
                <div class="basic-login-inner">
                  <form id="changePasswordForm" name="changePasswordForm" class="form-auth-small" method="post" action="">
                    <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="admin_id"/>
                    <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$EDITDATA['admin_id']?>"/>
                    <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$EDITDATA['admin_id']?>"/>
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                    <div class="row">
                      <input type="hidden" name="old_password" id="old_password" value="<?php echo $OLDPASSWORD; ?>">
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('admin_title')): ?>error<?php endif; ?>">
                        <label>Current Password<span class="required">*</span></label>
                        <input type="password" name="current_password" id="current_password" value="<?php if(set_value('current_password')): echo set_value('current_password'); endif; ?>" class="form-control required" placeholder="Current Password">
                        <?php if(form_error('current_password')): ?>
                          <span for="current_password" generated="true" class="help-inline"><?php echo form_error('current_password'); ?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="row">
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('admin_first_name')): ?>error<?php endif; ?>">
                        <label>New Password<span class="required">*</span></label>
                        <input type="password" name="new_password" id="new_password" value="<?php if(set_value('new_password')): echo set_value('new_password'); endif; ?>" class="form-control required" placeholder="New Password">
                        <?php if(form_error('new_password')): ?>
                          <span for="new_password" generated="true" class="help-inline"><?php echo form_error('new_password'); ?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="row">
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('admin_middle_name')): ?>error<?php endif; ?>">
                        <label>Confirm Password<span class="required">*</span></label>
                        <input type="password" name="conf_password" id="conf_password" value="<?php if(set_value('conf_password')): echo set_value('conf_password'); endif; ?>" class="form-control required" placeholder="Confirm Password">
                        <?php if(form_error('conf_password')): ?>
                          <span for="conf_password" generated="true" class="help-inline"><?php echo form_error('conf_password'); ?></span>
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
<script type="text/javascript">
  $(document).ready(function(){
    // Form Validation
    $("#changePasswordForm").validate({
      rules:{
        old_password: { minlength: 6, maxlength: 25 },
        current_password: { minlength: 6, equalTo: "#old_password" },
        new_password: { minlength: 6, maxlength: 25 },
        conf_password: { minlength: 6, equalTo: "#new_password" }
      },
      errorClass: "error",
      errorElement: "span",
      highlight:function(element, errorClass, validClass) {
        $(element).parents('.form-group-inner').removeClass('success');
        $(element).parents('.form-group-inner').addClass('error');
      },
      unhighlight: function(element, errorClass, validClass) {
        $(element).parents('.form-group-inner').removeClass('error');
        $(element).parents('.form-group-inner').addClass('success');
      }
    });
  });
</script>
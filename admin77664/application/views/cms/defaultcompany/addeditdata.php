<!-- <link rel="stylesheet" href="{ASSET_INCLUDE_URL}canvasCrop/user.image.canvasCrop.css">
 --><link rel="stylesheet" href="{ASSET_INCLUDE_URL}canvasCrop/about.image.canvasCrop.css">
<script type="text/javascript" src="{ASSET_INCLUDE_URL}canvasCrop/jquery.canvasCrop.js"></script>
<style type="text/css">
  input#show_vat {
    margin-right: 30%;
    margin-left: 6px;
}
</style>
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
                            <li class="breadcrumb-item"><a href="<?php echo getCurrentDashboardPath('dashboard/index'); ?>"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('CMSENABLEPAYMENT',getCurrentControllerPath('index')); ?>"> CMS</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);"><?=$EDITDATA?'Edit':'Add'?> Default Company</a></li>
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
                <h5><?=$EDITDATA?'Edit':'Add'?> Default Company</h5>
                <a href="<?php echo correctLink('CMSENABLEPAYMENT',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
              </div>
              <div class="card-body">
                <div class="basic-login-inner">
                  <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                    <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="default_company_id"/>
                    <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$EDITDATA['default_company_id']?>"/>
                    <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$EDITDATA['default_company_id']?>"/>
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                  
                      
                    <div class="row">
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('first_company_name')): ?>error<?php endif; ?>">
                        <label>First Company Name<span class="required">*</span></label>
                        <input type="text" name="first_company_name" id="first_company_name" value="<?php if(set_value('first_company_name')): echo set_value('first_company_name'); else: echo stripslashes($EDITDATA['first_company_name']);endif; ?>" class="form-control required" placeholder="First Company Name">
                        <?php if(form_error('first_company_name')): ?>
                          <span for="first_company_name" generated="true" class="help-inline"><?php echo form_error('first_company_name'); ?></span>
                        <?php endif; ?>
                      </div>
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('first_company_address')): ?>error<?php endif; ?>">
                        <label>First Company Address<span class="required">*</span></label>
                        <input type="text" name="first_company_address" id="first_company_address" value="<?php if(set_value('first_company_address')): echo set_value('first_company_address'); else: echo stripslashes($EDITDATA['first_company_address']);endif; ?>" class="form-control required" placeholder="First Company Address">
                        <?php if(form_error('first_company_address')): ?>
                          <span for="first_company_address" generated="true" class="help-inline"><?php echo form_error('first_company_address'); ?></span>
                        <?php endif; ?>
                      </div>

                       <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('second_company_name')): ?>error<?php endif; ?>">
                        <label>Second Company Name<span class="required">*</span></label>
                        <input type="text" name="second_company_name" id="second_company_name" value="<?php if(set_value('second_company_name')): echo set_value('second_company_name'); else: echo stripslashes($EDITDATA['second_company_name']);endif; ?>" class="form-control required" placeholder="Second Company Name">
                        <?php if(form_error('second_company_name')): ?>
                          <span for="second_company_name" generated="true" class="help-inline"><?php echo form_error('second_company_name'); ?></span>
                        <?php endif; ?>
                      </div>
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('second_company_address')): ?>error<?php endif; ?>">
                        <label>Second Company Address<span class="required">*</span></label>
                        <input type="text" name="second_company_address" id="second_company_address" value="<?php if(set_value('second_company_address')): echo set_value('second_company_address'); else: echo stripslashes($EDITDATA['second_company_address']);endif; ?>" class="form-control required" placeholder="Second Company Address">
                        <?php if(form_error('second_company_address')): ?>
                          <span for="second_company_address" generated="true" class="help-inline"><?php echo form_error('second_company_address'); ?></span>
                        <?php endif; ?>
                      </div>

                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-12 col-xs-12 <?php if(form_error('enable_default_company')): ?>error<?php endif; ?>">
                        <label>Enable Default Company Name/Address </label>
                        <select name="enable_default_company" id="enable_default_company" class="form-control" >
                          <option value='' <?php if($EDITDATA['enable_default_company'] == ''): echo 'selected'; endif; ?> >select</option>
                          <option value="first" <?php if($EDITDATA['enable_default_company'] == 'first'): echo 'selected'; endif; ?> >First</option>
                          <option value="second" <?php if($EDITDATA['enable_default_company'] == 'second'): echo 'selected'; endif; ?> >Second</option>
                        </select>
                        <?php if(form_error('enable_default_company')): ?>
                          <span for="enable_default_company" generated="true" class="help-inline"><?php echo form_error('enable_default_company'); ?></span>
                        <?php endif; ?>
                      </div>


                    </div>
                   
                    <div class="row">
                      <div class="login-btn-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="inline-remember-me mt-4">
                          <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                          <button class="btn btn-primary mb-4">Submit</button>
                          <a href="<?php echo correctLink('CMSPRIVACYPOLICY',getCurrentControllerPath('index')); ?>" class="btn btn-danger has-ripple mb-4">Cancel</a>
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
  $(function(){create_editor_for_textarea('description')});
</script>
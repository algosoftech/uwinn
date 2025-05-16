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
                            <li class="breadcrumb-item"><a href="javascript:void(0);"><?=$EDITDATA?'Edit':'Add'?> Enable SMS</a></li>
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
                <h5><?=$EDITDATA?'Edit':'Add'?>  Enable SMS</h5>
                <a href="<?php echo correctLink('CMSENABLEPAYMENT',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
              </div>
              <div class="card-body">
                <div class="basic-login-inner">
                  <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                    <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="enablesms_id"/>
                    <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$EDITDATA['enablesms_id']?>"/>
                    <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$EDITDATA['enablesms_id']?>"/>
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                  
                      
                    <div class="row">
                      
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-12 col-xs-12 <?php if(form_error('smscountry')): ?>error<?php endif; ?>">
                        <label>Enable Sms Country</label>
                        <select name="smscountry" id="smscountry" class="form-control" >
                          <option value="disable">Disabled</option>
                          <option value="enable" <?php if($EDITDATA['smscountry'] == 'enable'): echo 'selected'; endif; ?> >Sms Country</option>
                        </select>
                        <?php if(form_error('smscountry')): ?>
                          <span for="smscountry" generated="true" class="help-inline"><?php echo form_error('smscountry'); ?></span>
                        <?php endif; ?>
                      </div>

                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-12 col-xs-12 <?php if(form_error('digitizebird')): ?>error<?php endif; ?>">
                        <label>Enable Digitizebird Sms</label>
                        <select name="digitizebird" id="digitizebird" class="form-control" >
                          <option value="disable">Disabled</option>
                          <option value="enable" <?php if($EDITDATA['digitizebird'] == 'enable'): echo 'selected'; endif; ?> >Digitizebird Sms</option>
                        </select>
                        <?php if(form_error('digitizebird')): ?>
                          <span for="digitizebird" generated="true" class="help-inline"><?php echo form_error('digitizebird'); ?></span>
                        <?php endif; ?>
                      </div>

                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('sms_country_available_country')): ?>error<?php endif; ?>">
                        <label>Sms Country Available country list<span class="required">*</span></label>
                        <input type="text" name="sms_country_available_country" id="sms_country_available_country" value="<?php if(set_value('sms_country_available_country')): echo set_value('sms_country_available_country'); else: echo stripslashes($EDITDATA['sms_country_available_country']);endif; ?>" class="form-control required" placeholder="Available Country Code">
                        <p style="font-family:italic; color:red;">[ Add country code by comma (,) ]</p>
                        <?php if(form_error('sms_country_available_country')): ?>
                          <span for="sms_country_available_country" generated="true" class="help-inline"><?php echo form_error('sms_country_available_country'); ?></span>
                        <?php endif; ?>
                      </div>

                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('digitizebird_available_country')): ?>error<?php endif; ?>">
                        <label>Digitizebird Available country list<span class="required">*</span></label>
                        <input type="text" name="digitizebird_available_country" id="digitizebird_available_country" value="<?php if(set_value('digitizebird_available_country')): echo set_value('digitizebird_available_country'); else: echo stripslashes($EDITDATA['digitizebird_available_country']);endif; ?>" class="form-control required" placeholder="Available Country codes">
                        <p style="font-family:italic; color:red;">[ Add country code by comma (,) ]</p>
                        <?php if(form_error('digitizebird_available_country')): ?>
                          <span for="digitizebird_available_country" generated="true" class="help-inline"><?php echo form_error('digitizebird_available_country'); ?></span>
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
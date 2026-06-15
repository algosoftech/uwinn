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
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                    <fieldset>
                      <legend>SMS COUNTRY</legend>
                      <!-- SMS COUNTRY Start here -->
                      <div class="row">
                        <div class="form-group-inner col-lg-6 col-md-6 col-sm-12 col-xs-12 <?php if(form_error('smscountry')): ?>error<?php endif; ?>">
                          <label>Enable Sms Country</label>
                          <select name="smscountry" id="smscountry" class="form-control" >
                            <option value="disable">Disabled</option>
                            <option value="enable" <?php if(isset($EDITDATA['smscountry']) && $EDITDATA['smscountry'] == 'enable'): echo 'selected'; endif; ?> >Enable</option>
                          </select>
                          <?php if(form_error('smscountry')): ?>
                            <span for="smscountry" generated="true" class="help-inline"><?php echo form_error('smscountry'); ?></span>
                          <?php endif; ?>
                        </div>
                        <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('sms_country_available_country')): ?>error<?php endif; ?>">
                          <label>Sms Country Available country list<span class="required">*</span></label>
                          <input type="text" name="sms_country_available_country" id="sms_country_available_country" value="<?php if(set_value('sms_country_available_country')): echo set_value('sms_country_available_country'); else: echo stripslashes($EDITDATA['sms_country_available_country'] ?? '');endif; ?>" class="form-control required" placeholder="Available Country Code">
                          <p style="font-family:italic; color:red;">[ Add country code by comma (,) ]</p>
                          <?php if(form_error('sms_country_available_country')): ?>
                            <span for="sms_country_available_country" generated="true" class="help-inline"><?php echo form_error('sms_country_available_country'); ?></span>
                          <?php endif; ?>
                        </div>
                      </div>
                      <!-- SMS COUNTRY End here -->
                      <!-- Digitizebird Start here -->
                      <div>
                        <legend>DIGITIZEBIRD</legend>
                      </div>
                      <div class="row">
                        <div class="form-group-inner col-lg-6 col-md-6 col-sm-12 col-xs-12 <?php if(form_error('digitizebird')): ?>error<?php endif; ?>">
                          <label>Enable Digitizebird</label>
                          <select name="digitizebird" id="digitizebird" class="form-control" >
                            <option value="disable">Disabled</option>
                            <option value="enable" <?php if(isset($EDITDATA['digitizebird']) && $EDITDATA['digitizebird'] == 'enable'): echo 'selected'; endif; ?> >Enable</option>
                          </select>
                        </div>
                        <div class="form-group-inner col-lg-6 col-md-6 col-sm-12 col-xs-12 <?php if(form_error('digitizebird_available_country')): ?>error<?php endif; ?>">
                          <label>Digitizebird Available country list<span class="required">*</span></label>
                          <input type="text" name="digitizebird_available_country" id="digitizebird_available_country" value="<?php if(set_value('digitizebird_available_country')): echo set_value('digitizebird_available_country'); else: echo stripslashes($EDITDATA['digitizebird_available_country'] ?? '');endif; ?>" class="form-control required" placeholder="Available Country Code">
                          <p style="font-family:italic; color:red;">[ Add country code by comma (,) ]</p>
                          <?php if(form_error('digitizebird_available_country')): ?>
                            <span for="digitizebird_available_country" generated="true" class="help-inline"><?php echo form_error('digitizebird_available_country'); ?></span>
                          <?php endif; ?>
                        </div>
                      </div>
                      <!-- Digitizebird End here -->
                      <!-- NDM Start here -->
                      <div>
                        <legend>NDM</legend>
                      </div>
                      <div class="row">
                        <div class="form-group-inner col-lg-6 col-md-6 col-sm-12 col-xs-12 <?php if(form_error('ndm')): ?>error<?php endif; ?>">
                          <label>Enable NDM</label>
                          <select name="ndm" id="ndm" class="form-control" >
                            <option value="disable">Disabled</option>
                            <option value="enable" <?php if(isset($EDITDATA['ndm']) && $EDITDATA['ndm'] == 'enable'): echo 'selected'; endif; ?> >Enable</option>
                          </select>
                        </div>
                        <div class="form-group-inner col-lg-6 col-md-6 col-sm-12 col-xs-12 <?php if(form_error('ndm_available_country')): ?>error<?php endif; ?>">
                          <label>NDM Available country list<span class="required">*</span></label>
                          <input type="text" name="ndm_available_country" id="ndm_available_country" value="<?php if(set_value('ndm_available_country')): echo set_value('ndm_available_country'); else: echo stripslashes($EDITDATA['ndm_available_country'] ?? '');endif; ?>" class="form-control required" placeholder="Available Country Code">
                          <p style="font-family:italic; color:red;">[ Add country code by comma (,) ]</p>
                          <?php if(form_error('ndm_available_country')): ?>
                            <span for="ndm_available_country" generated="true" class="help-inline"><?php echo form_error('ndm_available_country'); ?></span>
                          <?php endif; ?>
                        </div>
                      </div>
                      <!-- NDM End here -->

                      <!-- WhatsApp & Email Start here -->
                      <div class="row">
                        <div class="form-group-inner col-lg-6 col-md-6 col-sm-12 col-xs-12 <?php if(form_error('whatsapp')): ?>error<?php endif; ?>">
                          <!-- WhatsApp & Email Start here -->
                          <div>
                            <legend>WHATSAPP</legend>
                          </div>
                          <label>Enable WhatsApp</label>
                          <select name="whatsapp" id="whatsapp" class="form-control" >
                            <option value="disable">Disabled</option>
                            <option value="enable" <?php if(isset($EDITDATA['whatsapp']) && $EDITDATA['whatsapp'] == 'enable'): echo 'selected'; endif; ?> >Enable</option>
                          </select>
                        </div>

                        <div class="form-group-inner col-lg-6 col-md-6 col-sm-12 col-xs-12 <?php if(form_error('email')): ?>error<?php endif; ?>">
                          <div>
                            <legend>EMAIL</legend>
                          </div>
                          <label>Enable Email</label>
                          <select name="email" id="email" class="form-control" >
                            <option value="disable">Disabled</option>
                            <option value="enable" <?php if(isset($EDITDATA['email']) && $EDITDATA['email'] == 'enable'): echo 'selected'; endif; ?> >Enable</option>
                          </select>
                        </div>
                      </div>
                      <!-- WhatsApp & Email End here -->

                      <!-- Default SMS Start here -->
                      <div>
                        <legend>DEFAULT SMS Gateway</legend>
                      </div>
                      <div class="row">
                        <div class="form-group-inner col-lg-6 col-md-6 col-sm-12 col-xs-12 <?php if(form_error('default_sms')): ?>error<?php endif; ?>">
                          <label>Enable Default SMS Gateway</label>
                          <select name="default_sms" id="default_sms" class="form-control" >
                            <option value="smscountry" <?php if(isset($EDITDATA['default_sms']) && $EDITDATA['default_sms'] == 'smscountry'): echo 'selected'; endif; ?> >SMS Country</option>
                            <option value="digitizebird" <?php if(isset($EDITDATA['default_sms']) && $EDITDATA['default_sms'] == 'digitizebird'): echo 'selected'; endif; ?> >Digitizebird</option>
                            <option value="ndm" <?php if(isset($EDITDATA['default_sms']) && $EDITDATA['default_sms'] == 'ndm'): echo 'selected'; endif; ?> >NDM</option>
                            <option value="whatsapp" <?php if(isset($EDITDATA['default_sms']) && $EDITDATA['default_sms'] == 'whatsapp'): echo 'selected'; endif; ?> >WhatsApp</option>
                            <option value="email" <?php if(isset($EDITDATA['default_sms']) && $EDITDATA['default_sms'] == 'email'): echo 'selected'; endif; ?> >Email</option>
                          </select>
                        </div>
                      </div>
                      <!-- Default SMS End here -->
                    </fieldset>
                   
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
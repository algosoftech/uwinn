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
                            <li class="breadcrumb-item"><a href="javascript:void(0);"><?=$EDITDATA?'Edit':'Add'?> Enable Payment</a></li>
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
                <h5><?=$EDITDATA?'Edit':'Add'?>  Enable Payment</h5>
                <a href="<?php echo correctLink('CMSENABLEPAYMENT',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
              </div>
              <div class="card-body">
                <div class="basic-login-inner">
                  <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                    <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="paymentmode_id"/>
                    <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$EDITDATA['paymentmode_id']?>"/>
                    <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$EDITDATA['paymentmode_id']?>"/>
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                  
                      
                    <div class="row">

                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-12 col-xs-12 <?php if(form_error('title_stripe')): ?>error<?php endif; ?>">
                        <label>Title (Stripe)</label>
                        <input type="text" name="title_stripe" id="title_stripe" class="form-control" value="<?=$EDITDATA['title_stripe']?>"/>
                        <?php if(form_error('title_stripe')): ?>
                          <span for="title_stripe" generated="true" class="help-inline"><?php echo form_error('title_stripe'); ?></span>
                        <?php endif; ?>
                      </div>
                      
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-12 col-xs-12 <?php if(form_error('stripe')): ?>error<?php endif; ?>">
                        <label>Enable Stripe</label>
                        <select name="stripe" id="stripe" class="form-control" >
                          <option value="disable">Disabled</option>
                          <option value="enable" <?php if($EDITDATA['stripe'] == 'enable'): echo 'selected'; endif; ?> >Stripe</option>
                        </select>
                        <?php if(form_error('stripe')): ?>
                          <span for="stripe" generated="true" class="help-inline"><?php echo form_error('stripe'); ?></span>
                        <?php endif; ?>
                      </div>

                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-12 col-xs-12 <?php if(form_error('title_telr')): ?>error<?php endif; ?>">
                        <label>Title (Telr)</label>
                        <input type="text" name="title_telr" id="title_telr" class="form-control" value="<?=$EDITDATA['title_telr']?>"/>
                        <?php if(form_error('title_telr')): ?>
                          <span for="title_telr" generated="true" class="help-inline"><?php echo form_error('title_telr'); ?></span>
                        <?php endif; ?>
                      </div>
                     
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-12 col-xs-12 <?php if(form_error('telr')): ?>error<?php endif; ?>">
                        <label>Enable Telr</label>
                        <select name="telr" id="telr" class="form-control" >
                          <option value="disable">Disabled</option>
                          <option value="enable" <?php if($EDITDATA['telr'] == 'enable'): echo 'selected'; endif; ?>  >Telr</option>
                        </select>
                        <?php if(form_error('telr')): ?>
                          <span for="telr" generated="true" class="help-inline"><?php echo form_error('telr'); ?></span>
                        <?php endif; ?>
                      </div>

                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-12 col-xs-12 <?php if(form_error('title_noon')): ?>error<?php endif; ?>">
                        <label>Title (Noon)</label>
                        <input type="text" name="title_noon" id="title_noon" class="form-control" value="<?=$EDITDATA['title_noon']?>"/>
                        <?php if(form_error('title_noon')): ?>
                          <span for="title_noon" generated="true" class="help-inline"><?php echo form_error('title_noon'); ?></span>
                        <?php endif; ?>
                      </div>
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-12 col-xs-12 <?php if(form_error('noon')): ?>error<?php endif; ?>">
                        <label>Enable Noon</label>
                        <select name="noon" id="noon" class="form-control" >
                          <option value="disable">Disabled</option>
                          <option value="enable" <?php if($EDITDATA['noon'] == 'enable'): echo 'selected'; endif; ?>  >Noon</option>
                        </select>
                        <?php if(form_error('noon')): ?>
                          <span for="noon" generated="true" class="help-inline"><?php echo form_error('noon'); ?></span>
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
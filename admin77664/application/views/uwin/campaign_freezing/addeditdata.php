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
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo getCurrentDashboardPath('dashboard/index'); ?>"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('CMSENABLEPAYMENT',getCurrentControllerPath('index')); ?>"> UWIN</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);"><?=$EDITDATA?'Edit':'Add'?> Campaign Freezing</a></li>
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
                <h5><?=$EDITDATA?'Edit':'Add'?>  Campaign Freezing</h5>
                <a href="<?php echo correctLink('CMSENABLEPAYMENT',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
              </div>
              <div class="card-body">
                <div class="basic-login-inner">
                  <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                    <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="campaign_id"/>
                    <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$EDITDATA['campaign_id']?>"/>
                    <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$EDITDATA['campaign_id']?>"/>
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                      
                    <div class="row">
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-12 col-xs-12 <?php if(form_error('campaign_freezing')): ?>error<?php endif; ?>">
                        <label>Enable/Disable</label>
                        <select name="campaign_freezing" id="campaign_freezing" class="form-control" >
                          <option value="Disable">Disable</option>
                          <option value="enable" <?php if($EDITDATA['campaign_freezing'] == 'enable'): echo 'selected'; endif; ?> >Enable</option>
                        </select>
                        <?php if(form_error('campaign_freezing')): ?>
                          <span for="campaign_freezing" generated="true" class="help-inline"><?php echo form_error('campaign_freezing'); ?></span>
                        <?php endif; ?>
                      </div>

                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('freezing_title')): ?>error<?php endif; ?>">
                        <label>Freezing Title<span class="required">*</span></label>
                        <input type="text" name="freezing_title" id="freezing_title" value="<?php if(set_value('freezing_title')): echo set_value('freezing_title'); else: echo stripslashes($EDITDATA['freezing_title']);endif; ?>" class="form-control required" placeholder="Freezing Title">
                        <?php if(form_error('freezing_title')): ?>
                          <span for="freezing_title" generated="true" class="help-inline"><?php echo form_error('freezing_title'); ?></span>
                        <?php endif; ?>
                      </div>

                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-12 col-xs-12 <?php if(form_error('auto_campaign_freezing')): ?>error<?php endif; ?>">
                        <label>Auto Freezing</label>
                        <select name="auto_campaign_freezing" id="auto_campaign_freezing" class="form-control" >
                          <option value="Disable">Disable</option>
                          <option value="enable" <?php if($EDITDATA['auto_campaign_freezing'] == 'enable'): echo 'selected'; endif; ?> >Enable</option>
                        </select>
                        <?php if(form_error('auto_campaign_freezing')): ?>
                          <span for="auto_campaign_freezing" generated="true" class="help-inline"><?php echo form_error('auto_campaign_freezing'); ?></span>
                        <?php endif; ?>
                      </div>

                      <div class="form-group-inner col-lg-3 col-md-3 col-sm-6 col-xs-12 <?php if(form_error('Freezing_time_start')): ?>error<?php endif; ?>">
                        <label>Freezing time Start<span class="required">*</span></label>
                        <input type="time" name="Freezing_time_start" id="Freezing_time_start" value="<?php if(set_value('Freezing_time_start')): echo set_value('Freezing_time_start'); else: echo stripslashes($EDITDATA['Freezing_time_start']);endif; ?>" class="form-control required" placeholder="Freezing time Start">
                        <?php if(form_error('Freezing_time_start')): ?>
                          <span for="Freezing_time_start" generated="true" class="help-inline"><?php echo form_error('Freezing_time_start'); ?></span>
                        <?php endif; ?>
                      </div>

                       <div class="form-group-inner col-lg-3 col-md-3 col-sm-6 col-xs-12 <?php if(form_error('update_date')): ?>error<?php endif; ?>">
                        <label>Last updated<span class="required">*</span></label>
                        <input type="text" name="update_date" id="update_date" value="<?php if(set_value('update_date')): echo set_value('update_date'); else: echo  date('Y-m-d H:i:s',$EDITDATA['update_date']);endif; ?>" class="form-control required" placeholder="Freezing time Start">
                        <?php if(form_error('update_date')): ?>
                          <span for="update_date" generated="true" class="help-inline"><?php echo form_error('update_date'); ?></span>
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
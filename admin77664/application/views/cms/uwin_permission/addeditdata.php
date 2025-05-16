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
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('CMSENABLEPAYMENT',getCurrentControllerPath('index')); ?>"> CMS</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);"><?=$EDITDATA?'Edit':'Add'?> App Uwin Permission</a></li>
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
                    <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="allowd_user_id"/>
                    <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$EDITDATA['allowd_user_id']?>"/>
                    <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$EDITDATA['allowd_user_id']?>"/>
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                  
                      
                    <div class="row">
                      
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-12 col-xs-12 <?php if(form_error('uwin_permission')): ?>error<?php endif; ?>">
                        <label>Enable/Disable UWINN in Dealz App</label>
                        <select name="uwin_permission" id="uwin_permission" class="form-control" >
                          <option value="Disable">Disable</option>
                          <option value="enable" <?php if($EDITDATA['uwin_permission'] == 'Enable'): echo 'selected'; endif; ?> >Enable</option>
                        </select>
                        <?php if(form_error('uwin_permission')): ?>
                          <span for="uwin_permission" generated="true" class="help-inline"><?php echo form_error('uwin_permission'); ?></span>
                        <?php endif; ?>
                      </div>

                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-12 col-xs-12 <?php if(form_error('uwin_allowd_user_type')): ?>error<?php endif; ?>">
                        <label>Uwin Allowed User</label>
                        <select name="uwin_allowd_user_type[]" id="uwin_allowd_user_type" class="form-control"  multiple>
                            <option>Select user type</option>
                            <option value="Freelancer">Freelancer</option>
                            <option value="Sales Person">Sales Person</option>
                            <option value="Retailer">Retailer</option>
                            <option value="Promoter">Promoter</option>
                            <option value="Users">Users</option>
                            <option value="Admin">Admin</option>
                            <option value="Super Retailer">Super Retailer</option>
                            <option value="Super Salesperson">Super Salesperson</option>
                            <option value="Freelancer Promoter">Freelancer Promoter</option>
                        </select>
                        <?php if(form_error('uwin_allowd_user_type')): ?>
                          <span for="uwin_allowd_user_type" generated="true" class="help-inline"><?php echo form_error('uwin_allowd_user_type'); ?></span>
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
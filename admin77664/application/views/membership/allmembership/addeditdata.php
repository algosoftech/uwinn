<link rel="stylesheet" href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
<script>
$(function(){
   $("#date").datepicker({dateFormat:'yy-mm-dd',changeMonth: true,changeYear: true,yearRange:"1970:<?php echo date('Y')?>"});
});
</script>
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
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('ALLMEMBERSHIPDATA',getCurrentControllerPath('index')); ?>"> Membership</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);"><?=$EDITDATA?'Edit':'Add'?> Membership</a></li>
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
                <h5><?=$EDITDATA?'Edit':'Add'?> Membership</h5>
                <a href="<?php echo correctLink('ALLMEMBERSHIPDATA',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
              </div>
              <div class="card-body">
                <div class="basic-login-inner">
                  <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                    <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="membership_id"/>
                    <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$EDITDATA['membership_id']?>"/>
                    <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$EDITDATA['membership_id']?>"/>
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                    <div class="row">
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('membership_type')): ?>error<?php endif; ?>">
                        <label>Membership Type<span class="required">*</span></label>
                        <input type="text" name="membership_type" id="membership_type" class="form-control required" value="<?php if(set_value('membership_type')): echo set_value('membership_type'); else: echo stripslashes($EDITDATA['membership_type']);endif; ?>" placeholder="Membership Type">
                        <?php if(form_error('membership_type')): ?>
                          <span for="membership_type" generated="true" class="help-inline"><?php echo form_error('membership_type'); ?></span>
                        <?php endif; ?>
                      </div>

                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('ade')): ?>error<?php endif; ?>">
                        <label>ADE<span class="required">*</span></label>
                        <input type="number" min="0"  name="ade" id="ade" class="form-control required" value="<?php if(set_value('ade')): echo set_value('ade'); else: echo stripslashes($EDITDATA['ade']);endif; ?>" placeholder="ADE">
                        <?php if(form_error('ade')): ?>
                          <span for="ade" generated="true" class="help-inline"><?php echo form_error('ade'); ?></span>
                        <?php endif; ?>
                      </div>

                      <div class="form-group-inner col-lg-9 col-md-9 col-sm-9 col-xs-12 <?php if(form_error('benifitDetails')): ?>error<?php endif; ?>">
                        <label>Benifit Details<span class="required">*</span></label>
                        <input type="text" name="benifitDetails" id="benifitDetails" class="form-control required" value="<?php if(set_value('benifitDetails')): echo set_value('benifitDetails'); else: echo stripslashes($EDITDATA['benifitDetails']);endif; ?>" placeholder="ADE">
                        <?php if(form_error('benifitDetails')): ?>
                          <span for="benifitDetails" generated="true" class="help-inline"><?php echo form_error('benifitDetails'); ?></span>
                        <?php endif; ?>
                      </div>

                      <div class="form-group-inner col-lg-3 col-md-3 col-sm-3 col-xs-12 <?php if(form_error('benifit')): ?>error<?php endif; ?>">
                        <label>Benifit %<span class="required">*</span></label>
                        <input type="number" min="0"  name="benifit" id="benifit" class="form-control required" value="<?php if(set_value('benifit')): echo set_value('benifit'); else: echo stripslashes($EDITDATA['benifit']);endif; ?>" placeholder="ADE">
                        <?php if(form_error('benifit')): ?>
                          <span for="benifit" generated="true" class="help-inline"><?php echo form_error('benifit'); ?></span>
                        <?php endif; ?>
                      </div>

                    </div>

                    
                    <div class="row">
                      <div class="login-btn-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="inline-remember-me mt-4">
                          <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                          <button class="btn btn-primary mb-4">Submit</button>
                          <a href="<?php echo correctLink('ALLMEMBERSHIPDATA',getCurrentControllerPath('index')); ?>" class="btn btn-danger has-ripple mb-4">Cancel</a>
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
<script>

  function ImageDelete(imageName,id,typ)
  {//alert(id);
    if(confirm("Sure to delete?"))
    {//alert(CURRENTCLASS);
      $.ajax({
            type: 'post',
             url: FULLSITEURL+'masterdata/'+CURRENTCLASS+'/imageDelete',
            data: {imageName:imageName,id:id,typ:typ},
         success: function(rdata) { 
              if(parseInt(rdata.status) == 1) {
                $('#image').val('');
                $('#ImageDiv').html('');
              }else{
                $('#image').val('');
                $('#ImageDiv2').html('');

              }
              return false;
            }
      });
    }
  }
</script>
<link rel="stylesheet" href="{ASSET_INCLUDE_URL}canvasCrop/user.image.canvasCrop.css">
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
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('CMSABOUTUD',getCurrentControllerPath('index')); ?>"> Help</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);"><?=$EDITDATA?'Edit':'Add'?> Help</a></li>
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
                <h5><?=$EDITDATA?'Edit':'Add'?> Help</h5>
                <a href="<?php echo correctLink('CMSABOUTUD',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
              </div>
              <div class="card-body">
                <div class="basic-login-inner">
                  <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                    <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="help_id"/>
                    <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$EDITDATA['help_id']?>"/>
                    <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$EDITDATA['help_id']?>"/>
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                    

                    <div class="row">
                      <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php if(form_error('question')): ?>error<?php endif; ?>">
                        <label>Question<span class="required">*</span></label>
                        <input type="text" name="question" id="question" class="form-control required" value="<?php if(set_value('question')): echo set_value('question'); else: echo stripslashes($EDITDATA['question']);endif; ?>" placeholder="Title">
                        <?php if(form_error('question')): ?>
                          <span for="question" generated="true" class="help-inline"><?php echo form_error('question'); ?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                   
                   
                    <div class="row">
                      <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php if(form_error('answer')): ?>error<?php endif; ?>">
                        <label>Answer<span class="required">*</span></label>
                        <textarea id="answer" name="answer" class="form-control required"><?php if(set_value('answer')): echo set_value('answer'); else: echo stripslashes($EDITDATA['answer']);endif; ?></textarea>
                        <?php if(form_error('answer')): ?>
                          <span for="answer" generated="true" class="help-inline"><?php echo form_error('answer'); ?></span>
                        <?php endif; ?>
                      </div>
                    </div>
          
                    <div class="row">
                      <div class="login-btn-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="inline-remember-me mt-4">
                          <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                          <button class="btn btn-primary mb-4">Submit</button>
                          <a href="<?php echo correctLink('CMSABOUTUD',getCurrentControllerPath('index')); ?>" class="btn btn-danger has-ripple mb-4">Cancel</a>
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
  $(function(){create_editor_for_textarea('answer')});
  /*$(function(){create_editor_for_textarea('description2')});
  $(function(){create_editor_for_textarea('description3')});*/
</script>
<!-- <script>

  function ImageDelete(imageName,id,typ)
  {//alert(id);
    if(confirm("Sure to delete?"))
    {//alert(FULLSITEURL);
      $.ajax({
            type: 'post',
             url: FULLSITEURL+'website/'+CURRENTCLASS+'/imageDelete',
            data: {imageName:imageName,id:id,typ:typ},
         success: function(rdata) { 
              if(parseInt(rdata.status) == 1) {
                $('#image').val('');
                $('#ImageDiv').html('');
              }
              return false;
            }
      });
    }
  }
</script>
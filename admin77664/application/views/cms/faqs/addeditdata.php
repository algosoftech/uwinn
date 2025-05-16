<!-- <link rel="stylesheet" href="{ASSET_INCLUDE_URL}canvasCrop/user.image.canvasCrop.css">
 --><link rel="stylesheet" href="{ASSET_INCLUDE_URL}canvasCrop/about.image.canvasCrop.css">
<script type="text/javascript" src="{ASSET_INCLUDE_URL}canvasCrop/jquery.canvasCrop.js"></script>
<style type="text/css">
  input#show_vat {
    margin-right: 30%;
    margin-left: 6px;
}

.addMoreData , .removeMoreData {
    position: relative;
    top: 50%;
    left: 50%;
}

</style>
<div class="pcoded-main-container">
    <div class="pcoded-content">
        <!-- [ breadcrumb ] start -->
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <div class="page-header-heading">
                            <?php /* ?><h5 class="m-b-11">Welcome <?=sessionData('HCAP_ADMIN_FIRST_NAME')?></h5><?php */ ?>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo getCurrentDashboardPath('dashboard/index'); ?>"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('CMSFAQS',getCurrentControllerPath('index')); ?>"> Faqs</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);"><?=$ALLDATA?'Edit':'Add'?> Faqs</a></li>
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
                <h5><?=$ALLDATA?'Edit':'Add'?> Faqs</h5>
                <a href="<?php echo correctLink('CMSFAQS',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
              </div>
              <div class="card-body">
                <div class="basic-login-inner">
                  <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="<?=base_url('/cms/faqs/addeditdata/');?>" enctype="multipart/form-data">
                    <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="faqs_id"/>
                    <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$ALLDATA['faqs_id']?>"/>
                    <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$ALLDATA['faqs_id']?>"/>
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">

                <fieldset>
                    <legend>FAQ's </legend>
                      <div class="faq-container">

                      <?php if($ALLDATA['faq_list']) : ?>

                          <?php foreach ($ALLDATA['faq_list'] as $key => $items): ?>

                                  <!-- <div class="extra-fields"></div> -->
                                  <div class="row faq-section">
                                    <div class="form-group-inner col-lg-11 col-md-11 col-sm-11 col-xs-11 <?php if(form_error('heading')): ?>error<?php endif; ?>">
                                      <label>heading<span class="required">*</span></label>
                                      <input type="text" name="heading[]" id="heading" class="form-control required" value="<?=$items->heading;?>">
                                      <?php if(form_error('heading')): ?>
                                        <span for="heading" generated="true" class="help-inline"><?php echo form_error('heading'); ?></span>
                                      <?php endif; ?>
                                    </div>
                                    <div class="form-group-inner col-lg-1 col-md-1 col-sm-1 col-xs-1"> 
                                        <label>&nbsp;</label>
                                    <?php if($key == 0): ?>
                                        <a href="javascript:void(0);" class="addMoreData" id="addmore" ><img src="<?php echo base_url(); ?>assets/admin/image/addmore.png" alt="Add more" /></a>
                                      <?php else: ?>
                                        <a href="javascript:void(0);" class="removeMoreData" id="RemoveData"><img src="<?php echo base_url(); ?>/assets/admin/image/cross.png" alt="Remove" /></a>
                                      <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php if(form_error('description')): ?>error<?php endif; ?>">
                                      <label>Description<span class="required">*</span></label>
                                      <textarea id="description" rows="8" name="description[]" class="form-control required"><?=$items->description;?></textarea>
                                      <?php if(form_error('description')): ?>
                                        <span for="description" generated="true" class="help-inline"><?php echo form_error('description'); ?></span>
                                      <?php endif; ?>
                                    </div>
                                  </div>
                          <?php endforeach; ?>
                      <?php else: ?>
                            <div class="row faq-section">
                              <div class="form-group-inner col-lg-11 col-md-11 col-sm-11 col-xs-11 <?php if(form_error('heading')): ?>error<?php endif; ?>">
                                <label>heading<span class="required">*</span></label>
                                <input type="text" name="heading[]" id="heading" class="form-control required" value="<?=$items->heading;?>">
                                <?php if(form_error('heading')): ?>
                                  <span for="heading" generated="true" class="help-inline"><?php echo form_error('heading'); ?></span>
                                <?php endif; ?>
                              </div>
                              <div class="form-group-inner col-lg-1 col-md-1 col-sm-1 col-xs-1"><?php if($i > 1){ echo '<hr />'; } ?>
                                  <label>&nbsp;</label>
                                  <a href="javascript:void(0);" class="addMoreData" id="addmore" ><img src="<?php echo base_url(); ?>assets/admin/image/addmore.png" alt="Add more" /></a>
                              </div>
                              <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php if(form_error('description')): ?>error<?php endif; ?>">
                                <label>Description<span class="required">*</span></label>
                                <textarea id="description" rows="8" name="description[]" class="form-control required"><?=$items->description;?></textarea>
                                <?php if(form_error('description')): ?>
                                  <span for="description" generated="true" class="help-inline"><?php echo form_error('description'); ?></span>
                                <?php endif; ?>
                              </div>
                            </div>
                      <?php endif; ?>
                      </div>
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
<!-- <script type="text/javascript">
  $(function(){create_editor_for_textarea('description')});
</script> -->
<script>
 $(document).on('click', '#currentPageForm .addMoreData', function() { 
      $('.faq-container').append('<div class="row faq-section"> <div class="form-group-inner col-lg-11 col-md-11 col-sm-11 col-xs-11"> <label>heading<span class="required">*</span></label> <input type="text" name="heading[]" id="heading" class="form-control required" value=""> </div> <div class="form-group-inner col-lg-1 col-md-1 col-sm-1 col-xs-1"> <label>&nbsp;</label> <a href="javascript:void(0);" class="removeMoreData" id="RemoveData"><img src="<?php echo base_url(); ?>/assets/admin/image/cross.png" alt="Remove" /></a> </div> <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12"> <label>Description<span class="required">*</span></label> <textarea id="description" rows="8" name="description[]" class="form-control required"></textarea> </div> </div>');
  });

 $(document).on('click', '.removeMoreData', function() { 
    $(this).parents('.faq-section').remove();
  });


</script>
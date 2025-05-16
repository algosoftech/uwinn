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
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('CMSTERMSCONDITIONS',getCurrentControllerPath('index')); ?>"> Testimonials</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);"><?=$EDITDATA?'Edit':'Add'?> Testimonials</a></li>
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
                <h5><?=$EDITDATA?'Edit':'Add'?> Testimonials</h5>
                <a href="<?php echo correctLink('CMSTERMSCONDITIONS',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
              </div>
              <div class="card-body">
                <div class="basic-login-inner">
                  <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                    <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="testimonial_id"/>
                    <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$EDITDATA['testimonial_id']?>"/>
                    <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$EDITDATA['testimonial_id']?>"/>
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                    
                    <!-- <div class="row">
                      <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php if(form_error('title')): ?>error<?php endif; ?>">
                        <label>Title<span class="required">*</span></label>
                        <input type="text" name="title" id="title" class="form-control required" value="<?php if(set_value('title')): echo set_value('title'); else: echo stripslashes($EDITDATA['title']);endif; ?>">
                        <?php if(form_error('title')): ?>
                          <span for="title" generated="true" class="help-inline"><?php echo form_error('title'); ?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                    
                    <div class="row">
                      <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php if(form_error('description')): ?>error<?php endif; ?>">
                        <label>Description<span class="required">*</span></label>
                        <textarea id="description" name="description" class="form-control required"><?php if(set_value('description')): echo set_value('description'); else: echo stripslashes($EDITDATA['description']);endif; ?></textarea>
                        <?php if(form_error('description')): ?>
                          <span for="description" generated="true" class="help-inline"><?php echo form_error('description'); ?></span>
                        <?php endif; ?>
                      </div>
                    </div> -->

                    <div class="row">
                      
                      <div class="form-group-inner col-lg-12 col-md-12 col-sm-6 col-xs-12 <?php if(form_error('image')): ?>error<?php endif; ?>">
                        <label>Image</label><br>
                        <input type="file" name="image" id="image" class="" value="<?php if(set_value('image')): echo set_value('image'); endif; ?>" accept="image/png, image/jpeg, image/webp" <?php if(empty($EDITDATA['image'])){ ?> required <?php } ?> >
                        <p style="font-family:italic; color:red;">[Image Size : 241 x 136 px in jpg/jpeg/png]</p>
                        <?php if($EDITDATA['image']): ?>
                         <div id="ImageDiv2"><img src="<?php echo fileBaseUrl.$EDITDATA['image']; ?>" width="250" border="0" alt="">&nbsp;
                           <a href="javascript:void(0);" onclick="ImageDelete('<?php echo $EDITDATA['image']; ?>','<?php echo $EDITDATA['testimonial_id']; ?>','app');"> 
                            <img src="{ASSET_INCLUDE_URL}image/cross.png" border="0" alt="">
                          </a></div>
                        <?php endif; ?>
                        <?php if(form_error('image')): ?>
                          <span for="image" generated="true" class="help-inline"><?php echo form_error('image'); ?></span>
                        <?php endif; ?>
                      </div>

                    <!--   <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('image_alt')): ?>error<?php endif; ?>">
                        <label>Alt Text</label>
                        <input type="text" name="image_alt" id="image_alt" class="form-control" value="<?php if(set_value('image_alt')): echo set_value('image_alt'); else: echo stripslashes($EDITDATA['image_alt']);endif; ?>" placeholder="Alt Text">
                        <?php if(form_error('image_alt')): ?>
                          <span for="image_alt" generated="true" class="help-inline"><?php echo form_error('image_alt'); ?></span>
                        <?php endif; ?>
                      </div> -->

                    </div>
                  </div>

                   <!--  <div class="row">
                      <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php if(form_error('ratings')): ?>error<?php endif; ?>">
                        <label>Ratings<span class="required">*</span></label>
                        <input type="number" name="ratings" id="ratings" class="form-control" value="<?php if(set_value('ratings')): echo set_value('ratings'); else: echo stripslashes($EDITDATA['ratings']);endif; ?>" placeholder="Ratings">
                        <?php if(form_error('ratings')): ?>
                          <span for="ratings" generated="true" class="help-inline"><?php echo form_error('ratings'); ?></span>
                        <?php endif; ?>
                      </div>
                    </div> -->
                    
                    <div class="row">
                      <div class="login-btn-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="inline-remember-me mt-4">
                          <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                          <button class="btn btn-primary mb-4">Submit</button>
                          <a href="<?php echo correctLink('CMSTERMSCONDITIONS',getCurrentControllerPath('index')); ?>" class="btn btn-danger has-ripple mb-4">Cancel</a>
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
<script>

  function ImageDelete(imageName,id,typ)
  {//alert(id);
    if(confirm("Sure to delete?"))
    {//alert(CURRENTCLASS);
      $.ajax({
            type: 'post',
             url: FULLSITEURL+'cms/'+CURRENTCLASS+'/imageDelete',
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
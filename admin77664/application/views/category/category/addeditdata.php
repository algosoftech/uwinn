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
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('ALLCATEGORYDATA',getCurrentControllerPath('index')); ?>"> Shopping Category</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);"><?=$EDITDATA?'Edit':'Add'?> Shopping Category</a></li>
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
                <h5><?=$EDITDATA?'Edit':'Add'?> Shopping Category</h5>
                <a href="<?php echo correctLink('ALLCATEGORYDATA',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
              </div>
              <div class="card-body">
                <div class="basic-login-inner">
                  <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                    <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="category_id"/>
                    <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$EDITDATA['category_id']?>"/>
                    <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$EDITDATA['category_id']?>"/>
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                    <div class="row">
                      <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php if(form_error('category_name')): ?>error<?php endif; ?>">
                        <label>Name<span class="required">*</span></label>
                        <input type="text" name="category_name" id="category_name" class="form-control required" value="<?php if(set_value('category_name')): echo set_value('category_name'); else: echo stripslashes($EDITDATA['category_name']);endif; ?>" placeholder="Category Name">
                        <?php if(form_error('category_name')): ?>
                          <span for="category_name" generated="true" class="help-inline"><?php echo form_error('category_name'); ?></span>
                        <?php endif; ?>
                      </div>
                    </div>

                    <div class="row">
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('category_image')): ?>error<?php endif; ?>">
                        <label> Image</label><br>
                        <input type="file" name="category_image" id="category_image" class="" value="<?php if(set_value('category_image')): echo set_value('category_image'); endif; ?>" accept="image/png, image/jpeg">
                         <p style="font-family:italic; color:red;">[Image Size : 241 x 136 px in jpg/jpeg/png]</p>
                        <?php if($EDITDATA['category_image']): ?>
                           <div id="ImageDiv2"><img src="<?php echo fileBaseUrl.$EDITDATA['category_image']; ?>" width="50" border="0" alt="">&nbsp;
                           <a href="javascript:void(0);" onclick="ImageDelete('<?php echo $EDITDATA['category_image']; ?>','<?php echo $EDITDATA['category_id']; ?>','app');"> 
                              <img src="{ASSET_INCLUDE_URL}image/cross.png" border="0" alt="">
                            </a></div>
                          <?php endif; ?>
                        <?php if(form_error('category_image')): ?>
                          <span for="category_image" generated="true" class="help-inline"><?php echo form_error('category_image'); ?></span>
                        <?php endif; ?>
                      </div>
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('category_image_alt')): ?>error<?php endif; ?>">
                        <label>Alt</label>
                        <input type="text" name="category_image_alt" id="category_image_alt" class="form-control" value="<?php if(set_value('category_image_alt')): echo set_value('category_image_alt'); else: echo stripslashes($EDITDATA['category_image_alt']);endif; ?>" placeholder="Alt Text">
                        <?php if(form_error('category_image_alt')): ?>
                          <span for="category_image_alt" generated="true" class="help-inline"><?php echo form_error('category_image_alt'); ?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="row">
                      <div class="login-btn-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="inline-remember-me mt-4">
                          <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                          <button class="btn btn-primary mb-4">Submit</button>
                          <a href="<?php echo correctLink('ALLCATEGORYDATA',getCurrentControllerPath('index')); ?>" class="btn btn-danger has-ripple mb-4">Cancel</a>
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
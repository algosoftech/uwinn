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
                          <li class="breadcrumb-item"><a href="{FULL_SITE_URL}dashboard"><i class="feather icon-home"></i></a></li>
                          
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('TGPNOTIFICATIONDATA',getCurrentControllerPath('index')); ?>">Push Notification</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);"><?=$EDITDATA?'Edit':'Add'?> Notification</a></li>
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
                <h5><?=$EDITDATA?'Edit':'Add'?> Notification</h5>
                <a href="<?php echo correctLink('TGPNOTIFICATIONDATA',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
              </div>
              <div class="card-body">
                <div class="basic-login-inner">
                  <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                    <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="notification_temp_id"/>
                    <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$EDITDATA['notification_temp_id']?>"/>
                    <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$EDITDATA['notification_temp_id']?>"/>
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                    <div class="row">
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('image')): ?>error<?php endif; ?>">
                             <label> Image <span class="required"> </span></label><br>
                        <input type="file" name="image" class="image"><br>
                        <?php if($EDITDATA['image']): ?>
                           <div id="ImageDiv"><img src="<?php echo fileBaseUrl.$EDITDATA['image']; ?>" width="50" border="0" alt="">&nbsp;
                           <a href="javascript:void(0);" onclick="ImageDelete('<?php echo $EDITDATA['image']; ?>','<?php echo $EDITDATA['notification_temp_id']; ?>');"> 
                              <img src="{ASSET_INCLUDE_URL}images/cross.png" border="0" alt="">
                            </a></div>
                          <?php endif; ?>
                        <?php if(form_error('image')): ?>
                          <span for="image" generated="true" class="help-inline"><?php echo form_error('image'); ?></span>
                        <?php endif; ?>
                        </div>
                        <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('title')): ?>error<?php endif; ?>">
                        <label>Title<span class="required">*</span></label>
                        <input type="text" name="title" id="title" value="<?php if(set_value('title')): echo set_value('title'); else: echo stripslashes($EDITDATA['title']);endif; ?>" class="form-control required" placeholder="Title">
                        <?php if(form_error('title')): ?>
                          <span for="title" generated="true" class="help-inline"><?php echo form_error('title'); ?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="row">
                      <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php if(form_error('description_en')): ?>error<?php endif; ?>">
                        <label>Description<span class="required">*</span></label>
                       <textarea class="form-control required" name="description_en" id="description" rows="5" cols="110"><?php if(set_value('description_en')): echo set_value('description_en'); else: echo stripslashes($EDITDATA['description_en']);endif; ?></textarea>
                        <?php if(form_error('description_en')): ?>
                          <span for="description_en" generated="true" class="help-inline"><?php echo form_error('description_en'); ?></span>
                        <?php endif; ?>
                      </div>
                    </div>

                    
                    <div class="row">
                      <div class="login-btn-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="inline-remember-me mt-4">
                          <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                          <button class="btn btn-primary mb-4">Send</button>
                          <a href="<?php echo correctLink('TGPNOTIFICATIONDATA',getCurrentControllerPath('index')); ?>" class="btn btn-danger has-ripple mb-4">Cancel</a>
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

  function ImageDelete(imageName,id)
  {//alert(id);
    //var a = FULLSITEURL+'website/'+CURRENTCLASS+'/imageDelete';alert(a);
    if(confirm("Sure to delete?"))
    {//alert(CURRENTCLASS);
      $.ajax({
            type: 'post',
             url: FULLSITEURL+'website/'+CURRENTCLASS+'/imageDelete',
            data: {imageName:imageName,id,id},
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

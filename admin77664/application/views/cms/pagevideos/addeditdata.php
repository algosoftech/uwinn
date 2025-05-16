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
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('CMSPAGEBANNER',getCurrentControllerPath('index')); ?>"> Page Video</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);"><?=$EDITDATA?'Edit':'Add'?> Page Video</a></li>
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
                <h5><?=$EDITDATA?'Edit':'Add'?> Page Video</h5>
                <a href="<?php echo correctLink('CMSPAGEBANNER',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
              </div>
              <div class="card-body">
                <div class="basic-login-inner">
                  <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                    <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="slider_id"/>
                    <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$EDITDATA['slider_id']?>"/>
                    <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$EDITDATA['slider_id']?>"/>
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                    <div class="row">
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('slider_video')): ?>error<?php endif; ?>">
                        <label>Video<span class="required">*</span></label>
                        <input type="file" name="slider_video" id="slider_video" value="<?php if(set_value('slider_video')): echo set_value('slider_video'); endif; ?>" accept="mp4">
                         <p style="font-family:italic; color:red;">[Video in MP4 Format only]</p>
                        <?php if($EDITDATA['video_url']): ?>
                           <div id="ImageDiv2"><img src="<?php echo fileBaseUrl.$EDITDATA['image']; ?>" width="50" border="0" alt="">&nbsp;
                           <video width="220" height="100">
                              <source src="<?=base_url().$EDITDATA['video_url']?>" type="video/mp4">
                              Your browser does not support the video tag.
                            </video>
                           <a href="javascript:void(0);" onclick="VideoDelete('<?php echo $EDITDATA['video_url']; ?>','<?php echo $EDITDATA['slider_id']; ?>','web');"> 
                              <img src="{ASSET_INCLUDE_URL}image/cross.png" border="0" alt="">
                            </a>
                          </div>
                          <?php endif; ?>
                        <?php if(form_error('slider_video')): ?>
                          <span for="slider_video" generated="true" class="help-inline"><?php echo form_error('slider_video'); ?></span>
                        <?php endif; ?>
                      </div>
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('slider_video_alt')): ?>error<?php endif; ?>">
                        <label>Alt</label>
                        <input type="text" name="slider_video_alt" id="slider_video_alt" class="form-control" value="<?php if(set_value('slider_video_alt')): echo set_value('slider_video_alt'); else: echo stripslashes($EDITDATA['alt']);endif; ?>">
                        <?php if(form_error('slider_video_alt')): ?>
                          <span for="slider_video_alt" generated="true" class="help-inline"><?php echo form_error('slider_video_alt'); ?></span>
                        <?php endif; ?>
                      </div>
                    </div>

                    <div class="row">
                      <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php if(form_error('slider_description')): ?>error<?php endif; ?>">
                        <label>Description</label>
                        <input type="text" name="slider_description" id="slider_description" class="form-control" value="<?php if(set_value('slider_description')): echo set_value('slider_description'); else: echo stripslashes($EDITDATA['slider_description']);endif; ?>">
                        <?php if(form_error('slider_description')): ?>
                          <span for="slider_description" generated="true" class="help-inline"><?php echo form_error('slider_description'); ?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="row">
                      <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('pagename')): ?>error<?php endif; ?>">
                         <label>Type<span class="required">*</span></label>
                        <?php if(set_value('pagename')): $pagename = set_value('pagename'); elseif($EDITDATA['page']): $pagename = stripslashes($EDITDATA['page']); else: $pagename = ''; endif; ?>
                        <select name="pagename" id="pagename" class="form-control required">
                          <option value="">Select Page</option>
                          <?php if($PAGESDATA): foreach($PAGESDATA as $pageDataKey=>$pageDataValue): ?>
                            <option value="<?php echo $pageDataKey; ?>" <?php if($pageDataKey == $pagename): echo 'selected="selected"'; endif; ?>><?php echo $pageDataValue; ?></option>
                          <?php endforeach; endif; ?>
                        </select>  
                        <?php if(form_error('pagename')): ?>
                          <span for="pagename" generated="true" class="help-inline"><?php echo form_error('pagename'); ?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="row">
                      <div class="login-btn-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="inline-remember-me mt-4">
                          <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                          <button class="btn btn-primary mb-4">Submit</button>
                          <a href="<?php echo correctLink('CMSPAGEBANNER',getCurrentControllerPath('index')); ?>" class="btn btn-danger has-ripple mb-4">Cancel</a>
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
  // function ImageDelete(imageName,id,typ)
  // {//alert(id);
  //   if(confirm("Sure to delete?"))
  //   {//alert(CURRENTCLASS);
  //     $.ajax({
  //           type: 'post',
  //            url: FULLSITEURL+'website/'+CURRENTCLASS+'/imageDelete',
  //           data: {imageName:imageName,id:id,typ:typ},
  //        success: function(rdata) { 
  //             if(parseInt(rdata.status) == 1) {
  //               $('#image').val('');
  //               $('#ImageDiv').html('');
  //             }else{
  //               $('#image').val('');
  //               $('#ImageDiv2').html('');

  //             }
  //             return false;
  //           }
  //     });
  //   }
  // }

  function VideoDelete(videoName,id,typ)
  {//alert(id);
    if(confirm("Sure to delete?"))
    {
      //alert(typ);
      $.ajax({
            type: 'post',
             url: FULLSITEURL+'cms/'+CURRENTCLASS+'/videoDelete',
            data: {videoName:videoName,id:id,typ:typ},
         success: function(rdata) { 
          // alert(rdata);
          //     if(parseInt(rdata.status) == 1) {
          //       $('#slider_video').val('');
          //       $('#ImageDiv').html('');
          //     }else{
                $('#slider_video').val('');
                $('#ImageDiv2').html('');
              //}
              return false;
            }
      });
    }
  }
</script>
<link rel="stylesheet" href="{ASSET_INCLUDE_URL}canvasCrop/zodiac.canvasCrop.css" />
<script type="text/javascript" src="{ASSET_INCLUDE_URL}canvasCrop/jquery.canvasCrop.js" ></script>
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
                          <li class="breadcrumb-item"><a href="{FULL_SITE_URL}dashboard"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('TGPEMAILTEMPLATEDATA',getCurrentControllerPath('index')); ?>">Email Templates</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);"><?=$EDITDATA?'Edit':'Add'?> Email Template</a></li>
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
                <h5><?=$EDITDATA?'Edit':'Add'?> Email Template</h5>
                <a href="<?php echo correctLink('TGPEMAILTEMPLATEDATA',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
              </div>
              <div class="card-body">
                <div class="basic-login-inner">
                  <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="">
                    <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="email_template_id"/>
                    <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$EDITDATA['email_template_id']?>"/>
                    <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$EDITDATA['email_template_id']?>"/>
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                    <div class="row">
                      <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('mail_type')): ?>error<?php endif; ?>">
                          <label>Mail Type <span class="required">*</span></label>
                          <input type="text" name="mail_type" id="mail_type" value="<?php if(set_value('mail_type')): echo set_value('mail_type'); else: echo stripslashes($EDITDATA['mail_type']);endif; ?>" class="form-control" placeholder="Mail Type">
                          <?php if(form_error('mail_type')): ?>
                            <span for="mail_type" generated="true" class="help-inline"><?php echo form_error('mail_type'); ?></span>
                          <?php endif; ?>
                        </div>
                     <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('from_email')): ?>error<?php endif; ?>">
                          <label>From Email <span class="required">*</span></label>
                          <input type="email" name="from_email" id="from_email" value="<?php if(set_value('from_email')): echo set_value('from_email'); else: echo stripslashes($EDITDATA['from_email']);endif; ?>" class="form-control" placeholder="From Email">
                          <?php if(form_error('from_email')): ?>
                            <span for="from_email" generated="true" class="help-inline"><?php echo form_error('from_email'); ?></span>
                          <?php endif; ?>
                        </div>
                        <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('to_email')): ?>error<?php endif; ?>">
                          <label>To Email</label>
                          <input type="email" name="to_email" id="to_email" value="<?php if(set_value('to_email')): echo set_value('to_email'); else: echo stripslashes($EDITDATA['to_email']);endif; ?>" class="form-control" placeholder="To Email">
                          <?php if(form_error('to_email')): ?>
                            <span for="to_email" generated="true" class="help-inline"><?php echo form_error('to_email'); ?></span>
                          <?php endif; ?>
                        </div>
                    </div>
                     <div class="row">
                        
                        <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('bcc_email')): ?>error<?php endif; ?>">
                          <label>BCC Email</label>
                          <input type="email" name="bcc_email" id="bcc_email" value="<?php if(set_value('bcc_email')): echo set_value('bcc_email'); else: echo stripslashes($EDITDATA['bcc_email']);endif; ?>" class="form-control" placeholder="BCC Email">
                          <?php if(form_error('bcc_email')): ?>
                            <span for="bcc_email" generated="true" class="help-inline"><?php echo form_error('bcc_email'); ?></span>
                          <?php endif; ?>
                        </div>
                        <div class="form-group-inner col-lg-4 col-md-4 col-sm-4 col-xs-12 <?php if(form_error('subject')): ?>error<?php endif; ?>">
                          <label>Subject <span class="required">*</span></label>
                          <input type="text" name="subject" id="subject" value="<?php if(set_value('subject')): echo set_value('subject'); else: echo stripslashes($EDITDATA['subject']);endif; ?>" class="form-control" placeholder="Subject">
                          <?php if(form_error('subject')): ?>
                            <span for="subject" generated="true" class="help-inline"><?php echo form_error('subject'); ?></span>
                          <?php endif; ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php if(form_error('mail_header')): ?>error<?php endif; ?>">
                        <label>Mail Header<span class="required">*</span></label>
                        <textarea id="mail_header" name="mail_header" class="mail_header form-control"><?php if(set_value('mail_header')): echo set_value('mail_header'); else: echo stripslashes($EDITDATA['mail_header']);endif; ?></textarea>
                        <?php if(form_error('mail_header')): ?>
                          <span for="mail_header" generated="true" class="help-inline"><?php echo form_error('mail_header'); ?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="row">
                        <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php if(form_error('mail_body')): ?>error<?php endif; ?>">
                        <label>Mail Body<span class="required">*</span></label>
                        <textarea id="mail_body" name="mail_body" class="mail_body form-control"><?php if(set_value('mail_body')): echo set_value('mail_body'); else: echo stripslashes($EDITDATA['mail_body']);endif; ?></textarea>
                        <?php if(form_error('mail_body')): ?>
                          <span for="mail_body" generated="true" class="help-inline"><?php echo form_error('mail_body'); ?></span>
                        <?php endif; ?>
                         <span style="color:red;">Please don't make any changes in {VALUES}. The VALUES will be changed dynamically.</span>

                      </div>
                    </div>
                    <div class="row">
                        <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php if(form_error('mail_footer')): ?>error<?php endif; ?>">
                        <label>Mail Footer<span class="required">*</span></label>
                        <textarea id="mail_footer" name="mail_footer" class="mail_footer form-control"><?php if(set_value('mail_footer')): echo set_value('mail_footer'); else: echo stripslashes($EDITDATA['mail_footer']);endif; ?></textarea>
                        <?php if(form_error('mail_footer')): ?>
                          <span for="mail_footer" generated="true" class="help-inline"><?php echo form_error('mail_footer'); ?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="row">
                        <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php if(form_error('html')): ?>error<?php endif; ?>">
                        <label>Html<span class="required">*</span></label>
                        <textarea id="html" name="html" class="html form-control"><?php if(set_value('html')): echo set_value('html'); else: echo stripslashes($EDITDATA['html']);endif; ?></textarea>
                        <?php if(form_error('html')): ?>
                          <span for="html" generated="true" class="help-inline"><?php echo form_error('html'); ?></span>
                        <?php endif; ?>
                      </div>
                    </div>
                    <div class="row">
                      <div class="login-btn-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="inline-remember-me mt-4">
                          <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                          <button class="btn btn-primary mb-4">Submit</button>
                          <a href="<?php echo correctLink('TGPEMAILTEMPLATEDATA',getCurrentControllerPath('index')); ?>" class="btn btn-danger has-ripple mb-4">Cancel</a>
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

<div class="modal fade" id="zodiacImageUploadModel">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title text-center">Position and size your photo</h4>
      </div>
      <div class="modal-body">
        <div class="profileimg-plug col-md-12 col-sm-12 col-xs-12">
          <div class="text-center" style="color:#FF0000; font-size:12px;" id="productBlinker">Image must be min width: 400px and min height: 400px.</div>
          <div class="container-crop">
            <div class="imageBox" >
              <div class="mask"></div>
              <div class="thumbBox"></div>
              <div class="spinner" style="display: none">Loading...</div>
            </div>
            <div class="tools clearfix">
              <div class="upload-wapper">
                  Select An Image
                  <input type="file" id="upload-file" value="Upload" />
              </div>
              <span id="rotateLeft" >rotateLeft</span>
              <span id="rotateRight" >rotateRight</span>
              <span id="zoomIn" >zoomIn</span>
              <span id="zoomOut" >zoomOut</span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default zodiacImageClosedButton" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-default zodiacImageSaveButton">Save</button>
        <input type="hidden" name="zodiacImageLoading" id="zodiacImageLoading" value="NO" />
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  $(function(){create_editor_for_textarea('mail_header')});
  $(function(){create_editor_for_textarea('mail_body')});
  $(function(){create_editor_for_textarea('mail_footer')});
</script>
<script type="text/javascript">
  $(document).on('click','#zodiacImage',function(){ 
    $('#zodiacImageUploadModel').modal({backdrop:'static', show: true, keyword: false});
    var canvas = document.getElementById("visbleCanvas");
    //var context = canvas.getContext("2d");
    //context.clearRect(0,0,canvas.width,canvas.height);
    $('#zodiacImageUploadModel .modal-footer.plucgin-clodebtn .zodiacImageClosedButton').html('Close');
    $('#zodiacImageUploadModel .modal-footer.plucgin-clodebtn .zodiacImageSaveButton').html('Save');
    $('#zodiacImageUploadModel #zodiacImageLoading').val('NO');
    $(".modal-footer .zodiacImageClosedButton").removeAttr('disabled');
    $(".modal-footer .zodiacImageSaveButton").removeAttr('disabled');
  });
  $(function(){
    var rot = 0,ratio = 1;
    $('#upload-file').on('change', function(){
        var reader = new FileReader();
        reader.onload = function(e) {
            CanvasCrop = $.CanvasCrop({
                cropBox : ".imageBox",
                imgSrc : e.target.result,
                limitOver : 2
            });
            rot =0 ;
            ratio = 1;
        }
        reader.readAsDataURL(this.files[0]);
        this.files = [];
    });
    $("#rotateLeft").on("click",function(){
        rot -= 90;
        rot = rot<0?270:rot;
        CanvasCrop.rotate(rot);
    });
    $("#rotateRight").on("click",function(){
        rot += 90;
        rot = rot>360?90:rot;
        CanvasCrop.rotate(rot);
    });
    $("#zoomIn").on("click",function(){
        ratio =ratio*0.9;
        CanvasCrop.scale(ratio);
    });
    $("#zoomOut").on("click",function(){
        ratio =ratio*1.1;
        CanvasCrop.scale(ratio);
    });
    $(".zodiacImageSaveButton").on("click",function(){  
      var imageData = CanvasCrop.getDataURL("png"); 
      var base_path       = '<?php echo base_url(); ?>'; 
      if($('#zodiacImageUploadModel #zodiacImageLoading').val() == 'NO')
      { 
        $('#zodiacImageUploadModel .zodiacImageSaveButton').html('Saving..');
        $('#zodiacImageUploadModel #zodiacImageLoading').val('YES');
        
        $(".modal-footer .zodiacImageClosedButton").attr("disabled", "disabled");
        $(".modal-footer .zodiacImageSaveButton").attr("disabled", "disabled");

        $.ajax({  
            type: 'post',
             url: FULLSITEURL+CURRENTCLASS+'settings/settingsmdata/zodiacImageUpload', 
            data: {imageData:imageData},
          success: function(rdata){ 
            if(parseInt(rdata.status) == 1) {
              $('#image').val(rdata.routedata.image);
              $('#zodiacImageDiv').html('<img src="'+base_path+rdata.routedata.image+'" width="50" border="0" alt="" /> <a href="javascript:void(0);" onClick="imageDelete(\''+rdata.routedata.image+'\');"> <img src="{ASSET_INCLUDE_URL}img/cross.png" border="0" alt="" /> </a>');
              $('#zodiacImageUploadModel').modal('hide');
              $('#zodiacImageUploadModel #zodiacImageLoading').val('NO');
            }
            return false;
          }
        });
      }
    });
    console.log("ontouchend" in document);
  })
  //////////////////////////////////   Image delete Through Ajax
  function imageDelete(imageName)
  {
    if(confirm("Sure to delete?"))
    {
      $.ajax({
            type: 'post',
             url: FULLSITEURL+CURRENTCLASS+'/imageDelete',
            data: {imageName:imageName},
         success: function(rdata) { 
              if(parseInt(rdata.status) == 1) {
                $('#image').val('');
                $('#zodiacImageDiv').html('');
              }
              return false;
            }
      });
    }
  }
</script>
<script>
  var product_blink_speed = 500; var product = setInterval(function () { var firsele = document.getElementById('productBlinker'); firsele.style.visibility = (firsele.style.visibility == 'hidden' ? '' : 'hidden'); }, product_blink_speed);
</script>
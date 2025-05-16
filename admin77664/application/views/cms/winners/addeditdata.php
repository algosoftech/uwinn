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
                            <li class="breadcrumb-item"><a href="<?php echo base_url('/'); ?>"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('ALLWINNERDATA',getCurrentControllerPath('index')); ?>"> winners</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);"><?=$EDITDATA?'Edit':'Add'?> winners</a></li>
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
                <h5><?=$EDITDATA?'Edit':'Add'?> winners</h5>
                <a href="<?php echo correctLink('ALLWINNERDATA',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
              </div>
              <div class="card-body">
                <div class="basic-login-inner">
                  <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                    <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="section_id"/>
                    <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$EDITDATA['section_id']?>"/>
                    <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$EDITDATA['section_id']?>"/>
                    <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                    <div class="row">
                      
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('winner_image')): ?>error<?php endif; ?>">
                        <label>Image <?php if(empty($EDITDATA['winner_image'])): ?> <span class="required">*</span> <?php endif; ?>  </label>
                        <input type="file" name="winner_image" id="winner_image" <?php if(empty($EDITDATA['winner_image'])): ?>   class="required" <?php endif; ?> value="<?php if(set_value('winner_image')): echo set_value('winner_image'); endif; ?>" accept="image/png, image/jpeg ,image/webp">
                         <p style="font-family:italic; color:red;">[Mobile Size : 450 x 450 px in jpg/jpeg/png]</p>
                         <?php if($EDITDATA['winner_image']): ?>
                           <div id="ImageDiv2"><img src="<?php echo fileBaseUrl.$EDITDATA['winner_image']; ?>" width="150" border="0" alt="">&nbsp;
                           <a href="javascript:void(0);" onclick="ImageDelete('<?php echo $EDITDATA['winner_image']; ?>','<?php echo $EDITDATA['section_id']; ?>','web');"> 
                              <img src="{ASSET_INCLUDE_URL}image/cross.png" border="0" alt="">
                            </a></div>
                          <?php endif; ?>
                         <?php if(form_error('winner_image')): ?>
                         <span for="winner_image" generated="true" class="help-inline"><?php echo form_error('winner_image'); ?></span>
                        <?php endif; ?>
                      </div>

                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('order_id')): ?>error<?php endif; ?>">
                        <label>Ticket ID<span class="required">*</span></label>
                        <input type="text" name="order_id" id="order_id" value="<?php if(set_value('order_id')): echo set_value('order_id'); else: echo stripslashes($EDITDATA['order_id']);endif; ?>" class="form-control required" placeholder="order_id">
                        <?php if(form_error('order_id')): ?>
                          <span for="order_id" generated="true" class="help-inline"><?php echo form_error('order_id'); ?></span>
                        <?php endif; ?>
                      </div>

                    </div>

                    <div class="row">
                      <div class="login-btn-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="inline-remember-me mt-4">
                          <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                          <button class="btn btn-primary mb-4">Submit</button>
                          <a href="<?php echo correctLink('ALLWINNERDATA',getCurrentControllerPath('index')); ?>" class="btn btn-danger has-ripple mb-4">Cancel</a>
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
  {
    // alert(imageName);
    // alert(id);

    if(confirm("Sure to delete?"))
    {//alert(CURRENTCLASS);
      $.ajax({
            type: 'post',
            url: FULLSITEURL+'cms/'+CURRENTCLASS+'/imageDelete',
            data: {imageName:imageName,id:id,typ:typ},
            success: function(rdata) { 
              if(parseInt(rdata.status) == 1) {
                $('#image').val('');
                $('#ImageDiv2').html('');
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


   
   
   

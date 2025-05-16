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
                     <?php /* ?>
                     <h5 class="m-b-10">Welcome <?=sessionData('SCHW_ADMIN_FIRST_NAME')?></h5>
                     <?php */ ?>
                  </div>
                  <ul class="breadcrumb">
                     <li class="breadcrumb-item"><a href="<?php echo getCurrentDashboardPath('dashboard/index'); ?>"><i class="feather icon-home"></i></a></li>
                     <li class="breadcrumb-item"><a href="<?php echo correctLink('SUBCATEGORYDATA',getCurrentControllerPath('index')); ?>"> Sub-category</a></li>
                     <li class="breadcrumb-item"><a href="javascript:void(0);"><?=$EDITDATA?'Edit':'Add'?> Sub-category</a></li>
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
                  <h5><?=$EDITDATA?'Edit':'Add'?> Sub-category</h5>
                  <a href="<?php echo correctLink('SUBCATEGORYDATA',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
               </div>
               <div class="card-body">
                  <div class="basic-login-inner">
                     <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                        <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="sub_category_id"/>
                        <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$EDITDATA['sub_category_id']?>"/>
                        <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$EDITDATA['sub_category_id']?>"/>
                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                        <div class="row">
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('category_id')): ?>error<?php endif; ?>">
                        <label>Category<span class="required">*</span></label>
                        <?php if(set_value('category_id')): $categoryiddata = explode('_____',set_value('category_id')); $category_id = $categoryiddata[0]; elseif($EDITDATA['category_id']): $category_id = stripslashes($EDITDATA['category_id']); else: $category_id = ''; endif; ?>
                        <select name="category_id" id="category_id" class="form-control required">
                          <?php echo $this->admin_model->getProductCategory($category_id); ?>
                        </select>
                        <?php if(form_error('category_id')): ?>
                          <span for="category_id" generated="true" class="help-inline"><?php echo form_error('category_id'); ?></span>
                        <?php endif; ?>
                      </div>
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('sub_category')): ?>error<?php endif; ?>">
                              <label>Sub Category<span class="required">*</span></label>
                              <input type="text" name="sub_category" id="sub_category" class="form-control required" value="<?php if(set_value('sub_category')): echo set_value('sub_category'); else: echo stripslashes($EDITDATA['sub_category']);endif; ?>" placeholder="Title">
                              <?php if(form_error('sub_category')): ?>
                              <span for="sub_category" generated="true" class="help-inline"><?php echo form_error('sub_category'); ?></span>
                              <?php endif; ?>
                           </div>
                    </div>
                       
                        <div class="row">
                           <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('sub_cat_image')): ?>error<?php endif; ?>">
                              <label>Image</label><br>
                              <input type="file" name="sub_cat_image" id="sub_cat_image" class="" value="<?php if(set_value('sub_cat_image')): echo set_value('sub_cat_image'); endif; ?>" accept="image/png, image/jpeg">
                               <p style="font-family:italic; color:red;">[Image Size : 241 x 136 px in jpg/jpeg/png]</p>
                              <?php if($EDITDATA['sub_cat_image']): ?>
                              <div id="ImageDiv"><img src="<?php echo fileBaseUrl.$EDITDATA['sub_cat_image']; ?>" width="50" border="0" alt="">&nbsp;
                                 <a href="javascript:void(0);" onclick="ImageDelete('<?php echo $EDITDATA['sub_cat_image']; ?>','<?php echo $EDITDATA['sub_category_id']; ?>','web');"> 
                                 <img src="{ASSET_INCLUDE_URL}image/cross.png" border="0" alt="">
                                 </a>
                              </div>
                              <?php endif; ?>
                              <?php if(form_error('sub_cat_image')): ?>
                              <span for="sub_cat_image" generated="true" class="help-inline"><?php echo form_error('sub_cat_image'); ?></span>
                              <?php endif; ?>
                           </div>
                           <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('sub_cat_image_alt')): ?>error<?php endif; ?>">
                              <label>Alt</label>
                              <input type="text" name="sub_cat_image_alt" id="sub_cat_image_alt" class="form-control" value="<?php if(set_value('sub_cat_image_alt')): echo set_value('sub_cat_image_alt'); else: echo stripslashes($EDITDATA['sub_cat_image_alt']);endif; ?>">
                              <?php if(form_error('sub_cat_image_alt')): ?>
                              <span for="sub_cat_image_alt" generated="true" class="help-inline"><?php echo form_error('sub_cat_image_alt'); ?></span>
                              <?php endif; ?>
                           </div>
                        </div>
                       
                        <div class="row">
                           <div class="login-btn-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                              <div class="inline-remember-me mt-4">
                                 <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                                 <button class="btn btn-primary mb-4">Submit</button>
                                 <a href="<?php echo correctLink('SUBCATEGORYDATA',getCurrentControllerPath('index')); ?>" class="btn btn-danger has-ripple mb-4">Cancel</a>
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
<script type="text/javascript">
   $(function(){create_editor_for_textarea('description')});
   $(function(){create_editor_for_textarea('description2')});
</script>
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
                            <h5 class="m-b-10">Welcome <?=sessionData('HCAP_ADMIN_FIRST_NAME')?></h5>
                            <?php */ ?>
                        </div>
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="<?php echo getCurrentDashboardPath('dashboard/index'); ?>"><i class="feather icon-home"></i></a></li>
                            <li class="breadcrumb-item"><a href="<?php echo correctLink('ALLCAMPAINGSDATA',getCurrentControllerPath('index')); ?>"> Product</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);"><?=$EDITDATA?'Edit':'Add'?>Campaigns</a></li>
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
                        <h5><?=$EDITDATA?'Edit':'Add'?> Campaigns</h5>
                        <a href="<?php echo correctLink('ALLCAMPAINGSDATA',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
                    </div>
                    <div class="card-body">
                        <div class="basic-login-inner">
                            <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                                <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="campaigns_id"/>
                                <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$EDITDATA['campaigns_id']?>"/>
                                <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$EDITDATA['campaigns_id']?>"/>
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">

                                <div class="row">
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('category_id')): ?>error<?php endif; ?>">
                        <label>Category<span class="required">*</span></label>
                        <?php if(set_value('category_id')): $categoryiddata = explode('_____',set_value('category_id')); $category_id = $categoryiddata[0]; elseif($EDITDATA['category_id']): $category_id = stripslashes($EDITDATA['category_id']); else: $category_id = ''; endif; ?>
                        <select name="category_id" id="category_id" class="form-control required">
                          <?php echo $this->admin_model->getCategoryList($category_id); ?>
                        </select>
                        <?php if(form_error('category_id')): ?>
                          <span for="category_id" generated="true" class="help-inline"><?php echo form_error('category_id'); ?></span>
                        <?php endif; ?>
                      </div>
                         <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('sub_category_id')): ?>error<?php endif; ?>">
                              <label>Sub Category<span class="required">*</span></label>
                              <select id="sub_category_data" name="sub_category_id" class="form-control required">
                                 <option value="">Select sub_category</option>
                              </select>

                              <?php if(form_error('sub_category_id')): ?>
                              <span for="sub_category_id" generated="true" class="help-inline"><?php echo form_error('sub_category_id'); ?></span>
                              <?php endif; ?>
                           </div>
                        </div>

                                <div class="row">
                                    <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php if(form_error('title')): ?>error<?php endif; ?>">
                                        <label>Title<span class="required">*</span></label>
                                        <input type="text" name="title" id="title" class="form-control required" value="<?php if(set_value('title')): echo set_value('title'); else: echo stripslashes($EDITDATA['title']);endif; ?>" placeholder="Title">
                                        <?php if(form_error('title')): ?>
                                        <span for="title" generated="true" class="help-inline"><?php echo form_error('title'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    
                                </div>
                                <div class="row">
                                    <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php if(form_error('Description')): ?>error<?php endif; ?>">
                                        <label>Description</label>
                                        <textarea id="description" placeholder="Description" class="form-control" name="description"><?php if(set_value('description')): echo set_value('description'); else: echo stripslashes($EDITDATA['description']);endif; ?></textarea>
                                    </div>
                                </div>
                                
                                <div class="row">
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('campaigns_image')): ?>error<?php endif; ?>">
                        <label>Image</label><br>
                        <input type="file" name="campaigns_image" id="campaigns_image" class="" value="<?php if(set_value('campaigns_image')): echo set_value('campaigns_image'); endif; ?>" accept="image/png, image/jpeg">
                         <p style="font-family:italic; color:red;">[Image Size : 241 x 136 px in jpg/jpeg/png]</p>
                        <?php if($EDITDATA['campaigns_image']): ?>
                           <div id="ImageDiv2"><img src="<?php echo fileBaseUrl.$EDITDATA['campaigns_image']; ?>" width="50" border="0" alt="">&nbsp;
                           <a href="javascript:void(0);" onclick="ImageDelete('<?php echo $EDITDATA['campaigns_image']; ?>','<?php echo $EDITDATA['campaigns_image']; ?>','app');"> 
                              <img src="{ASSET_INCLUDE_URL}image/cross.png" border="0" alt="">
                            </a></div>
                          <?php endif; ?>
                        <?php if(form_error('campaigns_image')): ?>
                          <span for="campaigns_image" generated="true" class="help-inline"><?php echo form_error('campaigns_image'); ?></span>
                        <?php endif; ?>
                      </div>
                      <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('campaigns_image_alt')): ?>error<?php endif; ?>">
                        <label>Alt Text</label>
                        <input type="text" name="campaigns_image_alt" id="campaigns_image_alt" class="form-control" value="<?php if(set_value('campaigns_image_alt')): echo set_value('campaigns_image_alt'); else: echo stripslashes($EDITDATA['campaigns_image_alt']);endif; ?>" placeholder="Alt Text">
                        <?php if(form_error('campaigns_image_alt')): ?>
                          <span for="campaigns_image_alt" generated="true" class="help-inline"><?php echo form_error('campaigns_image_alt'); ?></span>
                        <?php endif; ?>
                      </div>
                    </div>

                                <div class="row">
                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('stock')): ?>error<?php endif; ?>">
                                        <label>Quantity<span class="required">*</span></label>
                                        <input type="number" min="0" name="stock" id="stock" class="form-control required" value="<?php if(set_value('stock')): echo set_value('stock'); else: echo stripslashes($EDITDATA['stock']);endif; ?>" placeholder="Quantity">
                                        <?php if(form_error('stock')): ?>
                                        <span for="stock" generated="true" class="help-inline"><?php echo form_error('stock'); ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="form-group-inner col-lg-6 col-md-6 col-sm-6 col-xs-12 <?php if(form_error('stock')): ?>error<?php endif; ?>">
                                        <label>ADE / iPoints<span class="required">*</span></label>
                                        <input type="number" min="0" name="adepoints" id="adepoints" class="form-control required" value="<?php if(set_value('adepoints')): echo set_value('adepoints'); else: echo stripslashes($EDITDATA['adepoints']);endif; ?>" placeholder="ADE / iPoints">
                                        <?php if(form_error('adepoints')): ?>
                                        <span for="adepoints" generated="true" class="help-inline"><?php echo form_error('adepoints'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                </div>

                    
                                <div class="row">
                                    <div class="login-btn-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <div class="inline-remember-me mt-4">
                                            <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                                            <button class="btn btn-primary mb-4">Submit</button>
                                            <a href="<?php echo correctLink('ALLCAMPAINGSDATA',getCurrentControllerPath('index')); ?>" class="btn btn-danger has-ripple mb-4">Cancel</a>
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
$('#category_id').on('change',function(){
var category_id =  $(this).val();   
$.ajax({
url:FULLSITEURL+'products/allproducts/getsubcategoryData',
type:'post',
data:{category_id:category_id},
success:function(data){
$('#sub_category_data').html(data);
}
});
});

$(document).ready(function(){
var category_id =  $('#category_id').val();
var sub_category_id  =  '<?php echo $EDITDATA['sub_category_id']?>';
$.ajax({
url:FULLSITEURL+'products/allproducts/getsubcategoryData',
type:'post',
data:{category_id:category_id,sub_category_id:sub_category_id},
success:function(data){
$('#sub_category_data').html(data);
}
});
});
</script>
<script type="text/javascript">
  $(function(){create_editor_for_textarea('description')});
  $(function(){create_editor_for_textarea('image')});
</script>
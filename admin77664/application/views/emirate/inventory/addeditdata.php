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
                     <li class="breadcrumb-item"><a href="<?php echo correctLink('ALLCOLLECTIONPOINTDATA',getCurrentControllerPath('index')); ?>"> Inventory List</a></li>
                     <li class="breadcrumb-item"><a href="javascript:void(0);"><?=$EDITDATA?'Edit':'Add'?> Inventory Point</a></li>
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
                  <h5><?=$EDITDATA?'Edit':'Add'?> Inventory</h5>
                  <a href="<?php echo correctLink('ALLCOLLECTIONPOINTDATA',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
               </div>
               <div class="card-body">
                  <div class="basic-login-inner">
                     <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                        <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="inventory_id"/>
                        <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$EDITDATA['inventory_id']?>"/>
                        <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$EDITDATA['inventory_id']?>"/>
                        <input type="hidden" name="collection_point_id" id="collection_point_id" value="<?=$collection_point_id?>"/>
                        <input type="hidden" name="availableStock" id="availableStock" value="<?php if(isset($EDITDATA['stock'])): echo $EDITDATA['stock']; else: echo  0; endif; ?>"/>
                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                        <div class="row">

                        <div class="form-group-inner col-lg-4 col-md-4 col-sm-6 col-xs-12 <?php if(form_error('product')): ?>error<?php endif; ?>">
                           <label>Select Products<span class="required">*</span></label>
                           <input type="text" list="products_list" name="product" id="product" class="form-control required" value="<?php if(isset($EDITDATA['products_id'])): echo $EDITDATA['products_id'].'|'.$EDITDATA['product_name']; endif; ?>" <?php if(isset($EDITDATA['products_id'])): echo 'readonly'; endif; ?>  placeholder="Enter product Name/Category/Sub Category/Description." />
                           <datalist id="products_list">
                              <?php foreach ($productList as $key => $item) { ?>
                                 <option value="<?php echo stripcslashes($item['products_id']).'|'.stripcslashes($item['title']) ?>"><?php echo stripcslashes($item['category_name']). ' | '.stripcslashes($item['sub_category_name']); ?></option>   
                              <?php } ?>
                           </datalist>
                           <?php if(form_error('products_list')): ?>
                           <span for="products_list" generated="true" class="help-inline"><?php echo form_error('products_list'); ?></span>
                           <?php else: ?>
                           <span for="products_list" id="products_list_error" generated="true" class="help-inline" style="color: red;"><?php if(isset($email_id_error)): echo $email_id_error; endif; ?></span>
                           <?php endif; ?>
                           <span for="products_list" id="no_of_stock" generated="true" class="help-inline" style="color: green;"></span>
                        </div>

                        <div class="form-group-inner col-lg-3 col-md-3 col-sm-3 col-xs-12 <?php if(form_error('qty')): ?>error<?php endif; ?>">
                           <label>Quantity<span class="required">*</span></label>
                           <input type="number" name="qty" id="qty" class="form-control required" value="<?php if(set_value('qty')): echo set_value('qty'); endif; ?>" placeholder="Quantity">
                           <?php if(form_error('qty')): ?>
                           <span for="qty" generated="true" class="help-inline"><?php echo form_error('qty'); ?></span>
                           <?php endif; ?>
                        </div>
                    </div>
                       
                        <div class="row">
                           <div class="login-btn-inner col-lg-12 col-md-12 col-sm-12 col-xs-12">
                              <div class="inline-remember-me mt-4">
                                 <input type="hidden" name="SaveChanges" id="SaveChanges" value="Yes">
                                 <button class="btn btn-primary mb-4">Submit</button>
                                 <a href="<?php echo correctLink('ALLCOLLECTIONPOINTDATA',getCurrentControllerPath('index')); ?>" class="btn btn-danger has-ripple mb-4">Cancel</a>
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
   $(document).ready(function(){
      var product = $('#product').val();
      var stock = $('#availableStock').val();
      var productData = product.split("|");
      //alert(productData[0]);
      $.ajax({
            type: 'post',
            url: FULLSITEURL+'emirate/'+CURRENTCLASS+'/getdatabyajax',
            data: {id:productData[0]},
            success: function(rdata) { 
               $('#availableStock').empty().val(rdata);
               $('#no_of_stock').empty().append("Total available stocks "+rdata + ' nos.');
               $('#no_of_stock').css('display','none');
            }
         });

      $('#product').change(function(){
         var product = $('#product').val();
         var productData = product.split("|");
         $.ajax({
            type: 'post',
            url: FULLSITEURL+'emirate/'+CURRENTCLASS+'/getdatabyajax',
            data: {id:productData[0]},
            success: function(rdata) { 
               $('#availableStock').empty().val(rdata);
               $('#no_of_stock').empty().append("Total available stocks "+rdata + ' nos.');
               $('#no_of_stock').css('display','block');
            }
         });
      });

      $('#qty').change(function(){
         var product = $('#qty').val();
         var stock = $('#availableStock').val();
         var A = parseInt(product)
         var B = parseInt(stock)
         if(B > A || B === A){
            $('#no_of_stock').empty().append("Total available stocks "+stock + ' nos.');
            $('#no_of_stock').css('display','block');
            $('#products_list_error').empty();
         } else {
            $('#products_list_error').empty().append("Quantity must be less than or equal to available stock");
            $('#no_of_stock').css('display','none');
         }
      });
   });
</script>
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
                     <li class="breadcrumb-item"><a href="<?php echo correctLink('ALLCOLLECTIONPOINTDATA',getCurrentControllerPath('index')); ?>"> Collection Point</a></li>
                     <li class="breadcrumb-item"><a href="javascript:void(0);"><?=$EDITDATA?'Edit':'Add'?> Collection Point</a></li>
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
                  <h5><?=$EDITDATA?'Edit':'Add'?> Collection Point</h5>
                  <a href="<?php echo correctLink('ALLCOLLECTIONPOINTDATA',getCurrentControllerPath('index')); ?>" class="btn btn-sm btn-primary pull-right">Back</a>
               </div>
               <div class="card-body">
                  <div class="basic-login-inner">
                     <form id="currentPageForm" name="currentPageForm" class="form-auth-small" method="post" action="" enctype="multipart/form-data">
                        <input type="hidden" name="CurrentFieldForUnique" id="CurrentFieldForUnique" value="collection_point_id"/>
                        <input type="hidden" name="CurrentIdForUnique" id="CurrentIdForUnique" value="<?=$EDITDATA['collection_point_id']?>"/>
                        <input type="hidden" name="CurrentDataID" id="CurrentDataID" value="<?=$EDITDATA['collection_point_id']?>"/>
                        <input type="hidden" name="<?php echo $this->security->get_csrf_token_name();?>" value="<?php echo $this->security->get_csrf_hash();?>">
                        <div class="row">

                        <div class="form-group-inner col-lg-4 col-md-4 col-sm-6 col-xs-12 <?php if(form_error('email_id')): ?>error<?php endif; ?>">
                           <label>Retailer's Email ID / Phone No.<span class="required">*</span></label>
                           <input type="text" name="email_id" id="email_id" class="form-control required" value="<?php if(set_value('email_id')): echo set_value('email_id'); elseif(isset($EDITDATA['users_email'])): echo stripslashes($EDITDATA['users_email']); else: echo stripslashes($EDITDATA['users_mobile']);endif; ?>" placeholder="Retailer's Email ID / Phone No.">
                           <?php if(form_error('email_id')): ?>
                           <span for="email_id" generated="true" class="help-inline"><?php echo form_error('email_id'); ?></span>
                           <?php else: ?>
                           <span for="email_id" id="email_id_error" generated="true" class="help-inline" style="color: red;"><?php if(isset($email_id_error)): echo $email_id_error; endif; ?></span>
                           <?php endif; ?>
                        
                        </div>

                        <div class="form-group-inner col-lg-4 col-md-4 col-sm-6 col-xs-12 <?php if(form_error('emirate_id')): ?>error<?php endif; ?>">
                           <label>Emirate<span class="required">*</span></label>
                           <?php if(set_value('emirate_id')): $categoryiddata = explode('_____',set_value('emirate_id')); $emirate_id = $categoryiddata[0]; elseif($EDITDATA['emirate_id']): $emirate_id = stripslashes($EDITDATA['emirate_id']); else: $emirate_id = ''; endif; ?>
                           <select name="emirate_id" id="emirate_id" class="form-control required">
                           <?php echo $this->admin_model->getEmirates($emirate_id); ?>
                           </select>
                           <?php if(form_error('emirate_id')): ?>
                           <span for="emirate_id" generated="true" class="help-inline"><?php echo form_error('emirate_id'); ?></span>
                           <?php endif; ?>
                        </div>
                        <div class="form-group-inner col-lg-4 col-md-4 col-sm-6 col-xs-12 <?php if(form_error('area_id')): ?>error<?php endif; ?>">
                           <label>Area<span class="required">*</span></label>
                           <?php if(set_value('area_id')): $categoryiddata = explode('_____',set_value('area_id')); $area_id = $categoryiddata[0]; elseif($EDITDATA['area_id']): $area_id = stripslashes($EDITDATA['area_id']); else: $area_id = ''; endif; ?>
                           <select name="area_id" id="area_id" class="form-control required">
                           <?php echo $this->admin_model->getEmiratesArea($EDITDATA['emirate_id'],$area_id); ?>
                           </select>
                           <?php if(form_error('area_id')): ?>
                           <span for="area_id" generated="true" class="help-inline"><?php echo form_error('area_id'); ?></span>
                           <?php endif; ?>
                        </div>

                        <div class="form-group-inner col-lg-12 col-md-12 col-sm-12 col-xs-12 <?php if(form_error('collection_point_name')): ?>error<?php endif; ?>">
                           <label>Collection Point<span class="required">*</span></label>
                           <input type="text" name="collection_point_name" id="collection_point_name" class="form-control required" value="<?php if(set_value('collection_point_name')): echo set_value('collection_point_name'); else: echo stripslashes($EDITDATA['collection_point_name']);endif; ?>" placeholder="Collection Point">
                           <?php if(form_error('collection_point_name')): ?>
                           <span for="collection_point_name" generated="true" class="help-inline"><?php echo form_error('collection_point_name'); ?></span>
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
      $('#emirate_id').change(function(){
         var emirate_id = $('#emirate_id').val();
         //alert(CURRENTCLASS);
         $.ajax({
            type: 'post',
            url: FULLSITEURL+'emirate/'+CURRENTCLASS+'/getmaratArea',
            data: {emirate_id:emirate_id},
            success: function(rdata) { 
               $('#area_id').empty().append(rdata);     
            }
         });
      });

      $('#email_id').change(function(){
         var retailerID = $('#email_id').val();
         $.ajax({
            type: 'post',
            url: FULLSITEURL+'emirate/'+CURRENTCLASS+'/checkRetailer',
            data: {retailerID:retailerID},
            success: function(rdata) { 
               if(rdata === 'error'){
                  $('#email_id_error').empty().append("This user account not assign as pickup point holder.");
               } 
               if(rdata === 'success'){
                  $('#email_id_error').empty();
               }
            }
         });
      });
   });
</script>